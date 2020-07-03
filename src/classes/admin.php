<?php
/**
 * Handle WP admin features of the plugin.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\EUVatTaxes {

	use Niteo\WooCart\EUVatTaxes\Vies;

	/**
	 * Class for all the WP admin panel magic.
	 *
	 * @since 1.0.0
	 */
	class Admin {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Initialize on `init` hook.
		 */
		public function init() {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_filter( 'woocommerce_get_settings_tax', array( $this, 'settings' ), PHP_INT_MAX, 2 );
				add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
				add_action( 'woocommerce_admin_field_button', array( $this, 'button_field' ) );
				add_action( 'wp_ajax_add_digital_taxes', array( $this, 'ajax_digital_tax_rates' ) );
				add_action( 'wp_ajax_add_tax_id_check', array( $this, 'ajax_tax_id_check' ) );
				add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'order_meta' ) );
			}
		}

		/**
		 * Add script for checking VAT to the order page.
		 *
		 * @return void
		 */
		public function scripts( $hook ) {
			global $post;

			if ( 'post.php' === $hook ) {
				if ( 'shop_order' === $post->post_type ) {
					wp_enqueue_script( 'euvat-order', Config::$plugin_url . 'assets/js/order.js', array( 'jquery' ), Config::VERSION, true );
					wp_enqueue_style( 'euvat-admin', Config::$plugin_url . 'assets/css/admin.css', array(), Config::VERSION );

					// Add data to be passed to an array
					$localize = array(
						'nonce' => wp_create_nonce( '__wc_euvat_nonce' ),
					);

					// Pass data to JS
					wp_localize_script(
						'euvat-order',
						'wc_euvat_l10n',
						$localize
					);
				}
			}
		}

		/**
		 * Add custom settings to the `woocommerce` tax options page.
		 *
		 * @param array  $settings Tax settings to be extended
		 * @param string $current_section Name of the current section
		 * @return array
		 */
		public function settings( $settings, $current_section ) {
			$tax_options = array(
				array(
					'id'    => 'wc_euvat_options',
					'title' => esc_html__( 'Tax Handling for B2B', 'eu-vat-b2b-taxes' ),
					'type'  => 'title',
					'desc'  => esc_html__( 'Customize settings if you sell to companies. Defaults are ticked checkboxes.', 'eu-vat-b2b-taxes' ),
				),
				array(
					'id'      => 'wc_b2b_sales',
					'title'   => esc_html__( 'B2B sales (adds fields Company Name and Tax ID)', 'eu-vat-b2b-taxes' ),
					'type'    => 'select',
					'options' => array(
						'none'  => esc_html__( 'disabled', 'eu-vat-b2b-taxes' ),
						'eu'    => esc_html__( 'EU store', 'eu-vat-b2b-taxes' ),
						'noneu' => esc_html__( 'Non-EU store', 'eu-vat-b2b-taxes' ),
					),
					'default' => 'none',
				),
				array(
					'id'      => 'wc_tax_id_required',
					'title'   => esc_html__( 'Tax ID field required for B2B', 'eu-vat-b2b-taxes' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Tax ID required', 'eu-vat-b2b-taxes' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_home_country',
					'title'   => esc_html__( 'B2B sales in the home country', 'eu-vat-b2b-taxes' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Charge Tax', 'eu-vat-b2b-taxes' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_eu_with_vatid',
					'title'   => esc_html__( 'B2B sales in the EU when VIES/VAT ID is provided', 'eu-vat-b2b-taxes' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Do not charge Tax', 'eu-vat-b2b-taxes' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_charge_vat',
					'title'   => esc_html__( 'B2B sales outside the country', 'eu-vat-b2b-taxes' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Do not charge Tax', 'eu-vat-b2b-taxes' ),
					'default' => 'yes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wc_euvat_options',
				),
				array(
					'id'    => 'wc_euvat_digital_goods',
					'title' => esc_html__( 'EU Tax Handling - Digital Goods (B2C)', 'eu-vat-b2b-taxes' ),
					'type'  => 'title',
					'desc'  => esc_html__( 'If you sell digital goods in/to EU, you need to charge the customer\'s country Tax. Automatically validates the customer IP against their billing address, and prompts the customer to self-declare their address if they do not match. Applies only to digital goods and services sold to consumers (B2C).', 'eu-vat-b2b-taxes' ),
				),
				array(
					'id'      => 'wc_vat_digital_goods_enable',
					'title'   => esc_html__( 'EU Tax Handling for Digital Goods', 'eu-vat-b2b-taxes' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Enable', 'eu-vat-b2b-taxes' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wc_vat_digital_goods_rates',
					'title'   => esc_html__( 'Import tax rates for all EU countries and create tax class Digital Goods' ),
					'type'    => 'button',
					'default' => esc_html__( 'Import Taxes', 'eu-vat-b2b-taxes' ),
					'class'   => 'button-secondary import-digital-tax-rates',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wc_euvat_digital_goods',
				),
			);

			return array_merge( $settings, $tax_options );
		}

		/**
		 * Adds a custom button field for woocommerce settings.
		 *
		 * @param $value array
		 * @codeCoverageIgnore
		 */
		public function button_field( $value ) {
			// Handling custom attribute
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<button
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="<?php echo esc_attr( $value['type'] ); ?>"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
						><?php echo esc_html( $value['default'] ); ?></button><?php echo esc_html( $value['suffix'] ); ?>
				</td>
			</tr>
			<?php
		}

		/**
		 * AJAX request for importing digital goods tax rates.
		 *
		 * @return string
		 */
		public function ajax_digital_tax_rates() {
			// CSRF protection
			check_ajax_referer( '__wc_euvat_nonce', 'nonce' );

			$tax_name = esc_html__( 'Digital Goods', 'eu-vat-b2b-taxes' );
			$tax_slug = 'digital-goods';

			// Add taxes to DB
			$response = $this->add_taxes_to_db( $tax_name, $tax_slug );

			// Return response to JS
			wp_send_json( $response );
		}

		/**
		 * Add tax rates to DB.
		 *
		 * @param string $name Name used to define tax rates
		 * @param string $slug Tax name slug
		 * @return array
		 */
		public function add_taxes_to_db( $name, $slug ) {
			global $wpdb;

			$query  = $wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wc_tax_rate_classes WHERE slug = %s",
				$slug
			);
			$result = $wpdb->get_row( $query, ARRAY_A, 0 );

			// Tax class not found
			// First, add class to DB
			if ( ! $result ) {
				$wpdb->insert(
					$wpdb->prefix . 'wc_tax_rate_classes',
					array(
						'name' => $name,
						'slug' => $slug,
					),
					array(
						'%s',
						'%s',
					)
				);
			}

			// Fetch tax rates
			$rates = $this->rates();
			$data  = $rates->get_tax_rates();

			// Response which we will be sending back to the page
			$response = array(
				'status'  => 'error',
				'message' => esc_html__( 'Nothing has been added or updated in the database.', 'eu-vat-b2b-taxes' ),
			);

			// Adding tax rates to the table
			if ( ! empty( $data ) && is_array( $data ) ) {
				// Counter to calculate query changes
				$i = 0;

				foreach ( $data as $key => $value ) {
					$query  = $wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s AND tax_rate_class = %s",
						$key,
						$slug
					);
					$result = $wpdb->get_row( $query, ARRAY_A, 0 );

					// Determine whether tax rate for specific country and tax class exists
					// If yes, then just update the option
					if ( $result ) {
						$wpdb->update(
							$wpdb->prefix . 'woocommerce_tax_rates',
							array(
								'tax_rate'      => $value['standard_rate'] . '.0000',
								'tax_rate_name' => $name . ' (' . $value['standard_rate'] . '%)',
							),
							array(
								'tax_rate_id' => $result['tax_rate_id'],
							),
							array(
								'%s',
								'%s',
							),
							array( '%d' )
						);
					} else {
						$wpdb->insert(
							$wpdb->prefix . 'woocommerce_tax_rates',
							array(
								'tax_rate_country'  => $key,
								'tax_rate'          => $value['standard_rate'] . '.0000',
								'tax_rate_name'     => $name . ' (' . $value['standard_rate'] . '%)',
								'tax_rate_priority' => 1,
								'tax_rate_order'    => 1,
								'tax_rate_class'    => $slug,
							),
							array(
								'%s',
								'%s',
								'%s',
								'%d',
								'%d',
								'%s',
							)
						);
					}

					// Increase counter
					++$i;
				}

				$response = array(
					'status'  => 'success',
					'message' => $i . ' tax entries have been updated',
				);
			}

			return $response;
		}

		/**
		 * Display field value on the order edit page.
		 *
		 * @param object $order Order object for getting post meta information
		 * @return void
		 */
		public function order_meta( $order ) {
			$order_id        = absint( $order->get_id() );
			$b2b_sale        = (bool) ( get_post_meta( $order_id, 'b2b_sale', true ) );
			$business_tax_id = sanitize_text_field( get_post_meta( $order_id, 'business_tax_id', true ) );
			$business_valid  = sanitize_text_field( get_post_meta( $order_id, 'business_tax_validation', true ) );

			// Add HTML for Business ID
			$this->add_html( $b2b_sale, $business_tax_id, $business_valid, $order_id );
		}

		/**
		 * Adds HTML to order meta.
		 *
		 * @param bool   $b2b_sale Whether the sale is B2B or not
		 * @param string $business_tax_id Tax ID to be used for verification
		 * @param string $business_valid Determines whether verification has already been done
		 * @param int    $order_id Order ID which has the B2B sale data
		 * @codeCoverageIgnore
		 */
		public function add_html( $b2b_sale, $business_tax_id, $business_valid, $order_id ) {
			$b2b_sale_text = ( $b2b_sale ) ? esc_html__( 'Yes', 'eu-vat-b2b-taxes' ) : esc_html__( 'No', 'eu-vat-b2b-taxes' );
			echo '<p><strong>' . esc_html__( 'B2B Sale', 'eu-vat-b2b-taxes' ) . ':</strong><br/>' . $b2b_sale_text . '</p>';

			// Not a B2B sale
			if ( ! $b2b_sale ) {
				return;
			}

			// We don't have a tax ID for verification
			if ( ! $business_tax_id ) {
				echo '<p><strong>' . esc_html__( 'Business Tax ID', 'eu-vat-b2b-taxes' ) . ':</strong><br/>' . esc_html__( 'None', 'eu-vat-b2b-taxes' ) . '</p>';
				return;
			}

			echo '<p><strong>' . esc_html__( 'Business Tax ID', 'eu-vat-b2b-taxes' ) . ':</strong><br/>' . $business_tax_id . '</p>';

			// Verification has been done
			if ( $business_valid ) {
				if ( 'yes' === $business_valid ) {
					echo '<div id="wc-euvat-response" class="wc-message-success">' . esc_html__( 'The Tax ID has been verified and is marked as valid.', 'eu-vat-b2b-taxes' ) . '</div>';
				} else {
					echo '<div id="wc-euvat-response" class="wc-message-error">' . esc_html__( 'The TAX ID has been verified and is marked as invalid.', 'eu-vat-b2b-taxes' ) . '</div>';
				}

				return;
			}

			echo '<p><button id="wc-euvat-check" class="button button-primary" data-taxid="' . $business_tax_id . '" data-orderid="' . $order_id . '">' . esc_html__( 'Check VAT ID', 'eu-vat-b2b-taxes' ) . '</button>';
			echo '<div id="wc-euvat-response" class="wc-message-notice">' . esc_html__( 'Business Tax ID verification has not been done.', 'eu-vat-b2b-taxes' ) . '</div>';
		}

		/**
		 * Process AJAX request for checking the Tax ID.
		 *
		 * @return string
		 */
		public function ajax_tax_id_check() {
			// Check for nonce
			check_ajax_referer( '__wc_euvat_nonce', 'nonce' );

			// Business Tax ID & Order ID
			$business_id = sanitize_text_field( $_POST['business_id'] );
			$order_id    = sanitize_text_field( $_POST['order_id'] );

			if ( ! empty( $business_id ) && ! empty( $order_id ) ) {
				// Doing Tax ID check over here
				// We are using Vies class for validating our request
				$validator = $this->vies();
				$check     = $validator->isValid( $business_id, true );

				if ( $check ) {
					// Update post meta and send response
					update_post_meta( $order_id, 'business_tax_validation', 'yes' );
					wp_send_json_success( esc_html__( 'The TAX ID has been verified and is marked as valid.', 'eu-vat-b2b-taxes' ) );
				}

				// Update post meta and send response
				update_post_meta( $order_id, 'business_tax_validation', 'no' );
				wp_send_json_error( esc_html__( 'The Tax ID has been verified and is marked as invalid.', 'eu-vat-b2b-taxes' ) );
			}

			wp_send_json_error( esc_html__( 'Unable to verify Tax ID because of the missing data.', 'eu-vat-b2b-taxes' ) );
		}

		/**
		 * Initiate the Vies class for Tax ID check.
		 */
		public function vies() : Vies {
			return new Vies();
		}

		/**
		 * Initiate the Rates class to fetch tax rates.
		 */
		public function rates() : Rates {
			return new Rates();
		}

	}

}
