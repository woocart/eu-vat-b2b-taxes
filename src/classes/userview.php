<?php
/**
 * User facing plugin view.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\EUVatTaxes {

	/**
	 * User class where we calculate taxes and get stuff done.
	 *
	 * @since 1.0.0
	 */
	class UserView {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'init', array( &$this, 'init' ) );
		}

		/**
		 * Initialize the user facing part of the plugin.
		 */
		public function init() {
			add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
			add_filter( 'woocommerce_billing_fields', array( &$this, 'checkout_fields' ) );
			add_action( 'woocommerce_checkout_update_order_review', array( &$this, 'calculate_tax' ) );
			add_action( 'woocommerce_after_checkout_validation', array( &$this, 'checkout_validation' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'update_order_meta' ), 10, 1 );
		}

		/**
		 * Add styles and scripts for the frontend.
		 */
		public function scripts() {
			wp_enqueue_script( 'euvat-public', Config::$plugin_url . 'assets/js/public.js', array( 'jquery' ), Config::VERSION, true );
			wp_enqueue_style( 'euvat-public', Config::$plugin_url . 'assets/css/public.css', '', Config::VERSION );

			// Pass data to JS
			$localize = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( '__wc_euvat_nonce' ),
			);

			wp_localize_script( 'euvat-public', 'atw_localize', $localize );
		}

		/**
		 * Add custom fields to the checkout page.
		 */
		public function checkout_fields( $fields ) {
			$b2b_sales = esc_html( get_option( 'wc_b2b_sales' ) );

			// Show conditionally :)
			if ( 'none' !== $b2b_sales ) {
				// Check for business status.
				$fields['business_check'] = array(
					'label'    => esc_html__( 'Are you making this purchase as a Business entity?', 'eu-vat-b2b-taxes' ),
					'type'     => 'checkbox',
					'required' => false,
					'class'    => array( 'better-tax-business-check', 'update_totals_on_change' ),
					'clear'    => true,
					'priority' => 5,
				);

					// Ask for VAT ID.
					$fields['business_tax_id'] = array(
						'label'    => esc_html__( 'Business Tax ID', 'eu-vat-b2b-taxes' ),
						'type'     => 'text',
						'required' => false,
						'class'    => array( 'form-row-wide', 'better-tax-vat-id', 'better-tax-hidden', 'address-field' ),
						'priority' => 6,
					);
			}

			return $fields;
		}

		/**
		 * We are returning empty taxes to simplify our tax computation.
		 *
		 * @return array
		 */
		public function return_tax( $taxes, $price, $rates, $price_includes_tax = false, $deprecated = false ) {
			return array();
		}

		/**
		 * Simplifying taxation for the user.
		 *
		 * @todo Live validation for the Tax ID
		 * @codeCoverageIgnore
		 */
		public function calculate_tax( $data ) {
			$new_data = array();

			// Parse the string.
			parse_str( $data, $new_data );

			// Customer billing details.
			$country = $new_data['billing_country'];

			// Set vat_exempt to false
			$this->set_vat_exempt( false );

			// For B2B
			if ( isset( $new_data['business_check'] ) && ! empty( $new_data['business_check'] ) ) {
				// Grab tax settings.
				$b2b_sales = esc_html( get_option( 'wc_b2b_sales' ) );

				// We will continue only if B2B sales are not disabled.
				if ( 'none' !== $b2b_sales ) {
					$b2b_home_tax    = esc_html( get_option( 'wc_tax_home_country' ) );
					$b2b_eu_tax_id   = esc_html( get_option( 'wc_tax_eu_with_vatid' ) );
					$b2b_tax_outside = esc_html( get_option( 'wc_tax_charge_vat' ) );
					$base_location   = wc_get_base_location()['country'];

					/**
					 * Playing around with various combinations and then calculating the tax (if required).
					 */

					// If the base country and the customer country is same, and also if the tax charge option is ticked off, then do nothing.
					if ( ( $base_location === $country ) && 'no' === $b2b_home_tax ) {
						add_filter( 'woocommerce_calc_tax', array( &$this, 'return_tax' ), PHP_INT_MAX, 5 );
					}

					// If the sale is not made in the base country, and the option to not charge tax is ticked.
					if ( ( $base_location !== $country ) && 'yes' === $b2b_tax_outside ) {
						add_filter( 'woocommerce_calc_tax', array( &$this, 'return_tax' ), PHP_INT_MAX, 5 );
					}

					// Check if `business_tax_id` is provided and the option to not charge tax is turned on. We will return empty taxes if the first statement is true.
					if ( isset( $new_data['business_tax_id'] ) && ! empty( $new_data['business_tax_id'] ) ) {
						if ( 'yes' === $b2b_eu_tax_id ) {
							// Doing Tax ID check over here
							// We are using Vies class for validating our request
							$validator = new Vies();
							$bool      = $validator->isValid( $new_data['business_tax_id'], true );

							if ( $bool ) {
								$this->set_vat_exempt( true );
								return;
							}
						}
					}
				}
			} else {
				// For B2C (handling digital goods and distance selling)
				// Get cart items.
				$items = WC()->cart->get_cart();

				// Loop through each item and calculate tax.
				foreach ( $items as $item ) {
					$tax_class = $item['data']->get_tax_class();

					// For digital goods.
					if ( 'digital-goods' === $tax_class ) {
						if ( 'no' === get_option( 'wc_vat_digital_goods_enable' ) ) {
							$item['data']->set_tax_class( null );
						}
					}

					// For distance selling.
					if ( 'distance-selling' === $tax_class ) {
						if ( 'no' === get_option( 'wc_euvat_distance_selling' ) ) {
							// Remove `distance-selling-rate` from the tax list.
							$item['data']->set_tax_class( null );
						} else {
							// Fetch digital selling countries where the taxes will be levied by the shop.
							$ds_countries = get_option( 'wc_vat_distance_selling_countries' );

							// If the customer country is not in the list, then continue as we are not going to charge in that case.
							if ( ! in_array( $country, $ds_countries ) ) {
								// Remove taxes here as well as the country is not in the list.
								$item['data']->set_tax_class( null );
							}
						}
					}
				}
			}
		}

		/**
		 * Add custom validation for the business tax ID field which is set to optional but we enforce it as required if the purchase is being made as a business entity.
		 *
		 * @param  array    $data An array of posted data.
		 * @param  WP_Error $errors
		 */
		public function checkout_validation( $data, $errors ) {
			if ( isset( $_POST['business_check'] ) ) {
				if ( ! isset( $_POST['business_tax_id'] ) || empty( $_POST['business_tax_id'] ) ) {
					if ( 'yes' === get_option( 'wc_tax_id_required' ) ) {
						$errors->add( 'billing', sprintf( esc_html__( '%1$sBusiness Tax ID%2$s is a required field.', 'eu-vat-b2b-taxes' ), '<strong>', '</strong>' ) );
					}
				}
			}
		}

		/**
		 * Update order meta for the specified order.
		 *
		 * @param int $order_id Order ID
		 * @return void
		 */
		public function update_order_meta( $order_id ) {
			$business_check = sanitize_text_field( $_POST['business_check'] );

			if ( ! empty( $business_check ) ) {
				update_post_meta( $order_id, 'b2b_sale', $business_check );
			}

			$business_tax_id = sanitize_text_field( $_POST['business_tax_id'] );

			if ( ! empty( $business_tax_id ) ) {
				update_post_meta( $order_id, 'business_tax_id', $business_tax_id );
			}
		}

		/**
		 * Sets VAT exempt for the customer (only for B2B transactions).
		 *
		 * @param bool $status Whether to enable or disable VAT exempt
		 */
		public function set_vat_exempt( $status ) {
			return WC()->customer->set_is_vat_exempt( $status );
		}

	}

}
