<?php
/**
 * Handle WP admin features of the plugin.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\AdvancedTaxes {

	use Niteo\WooCart\AdvancedTaxes\Vies;

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
			add_action( 'init', array( &$this, 'init' ) );
		}
		
		/**
		 * Initialize on `admin_init` hook.
		 */
		public function init() {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_filter( 'woocommerce_get_settings_tax', array( &$this, 'settings' ), PHP_INT_MAX, 2 );
				add_action( 'woocommerce_admin_field_button', array( &$this, 'button_field' ) );
				add_action( 'wp_ajax_add_digital_taxes', array( &$this, 'ajax_digital_tax_rates' ) );
				add_action( 'wp_ajax_add_distance_taxes', array( &$this, 'ajax_distance_tax_rates' ) );
				add_action( 'wp_ajax_add_tax_id_check', array( &$this, 'ajax_tax_id_check' ) );
				add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this, 'order_meta' ) );
			}
		}

		/**
		 * Add custom settings to the `woocommerce` tax options page.
		 */
		public function settings( $settings, $current_section ) {
			$tax_options = array(
				array(
					'id'   		=> 'wc_euvat_options',
					'title' 	=> esc_html__( 'Tax Handling for B2B', 'advanced-taxes-woocommerce' ),
					'type' 		=> 'title',
					'desc' 		=> esc_html__( 'Customize settings if you sell to companies. Defaults are ticked checkboxes.', 'advanced-taxes-woocommerce' ),
				),
				array(
					'id'      => 'wc_b2b_sales',
					'title'   => esc_html__( 'B2B sales (adds fields Company Name and Tax ID)', 'advanced-taxes-woocommerce' ),
					'type'    => 'select',
					'options' => array(
						'none'  => esc_html__( 'disabled', 'advanced-taxes-woocommerce' ),
						'eu'    => esc_html__( 'EU store', 'advanced-taxes-woocommerce' ),
						'noneu' => esc_html__( 'Non-EU store', 'advanced-taxes-woocommerce' ),
					),
					'default' => 'none',
				),
				array(
					'id'      => 'wc_tax_id_required',
					'title'   => esc_html__( 'Tax ID field required for B2B', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Tax ID required', 'advanced-taxes-woocommerce' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_home_country',
					'title' 	=> esc_html__( 'B2B sales in the home country', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Charge Tax', 'advanced-taxes-woocommerce' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_eu_with_vatid',
					'title'   => esc_html__( 'B2B sales in the EU when VIES/VAT ID is provided', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Do not charge Tax', 'advanced-taxes-woocommerce' ),
					'default' => 'yes',
				),
				array(
					'id'      => 'wc_tax_charge_vat',
					'title'   => esc_html__( 'B2B sales outside the country', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Do not charge Tax', 'advanced-taxes-woocommerce' ),
					'default' => 'yes',
				),
				array(
					'type' 		=> 'sectionend',
					'id'   		=> 'wc_euvat_options',
				),
				array(
					'id'   		=> 'wc_euvat_digital_goods',
					'title' 	=> esc_html__( 'EU Tax Handling - Digital Goods (B2C)', 'advanced-taxes-woocommerce' ),
					'type' 		=> 'title',
					'desc' 		=> esc_html__( 'If you sell digital goods in/to EU, you need to charge the customer\'s country Tax. Automatically validates the customer IP against their billing address, and prompts the customer to self-declare their address if they do not match. Applies only to digital goods and services sold to consumers (B2C).', 'advanced-taxes-woocommerce' ),
				),
				array(
					'id'      => 'wc_vat_digital_goods_enable',
					'title' 	=> esc_html__( 'EU Tax Handling for Digital Goods', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Enable', 'advanced-taxes-woocommerce' ),
					'default' => 'no',
				),
				array(
					'id'      => 'wc_vat_digital_goods_rates',
					'title' 	=> esc_html__( 'Import tax rates for all EU countries and create tax class Digital Goods' ),
					'type'    => 'button',
					'default' => esc_html__( 'Import Taxes', 'advanced-taxes-woocommerce' ),
					'class'   => 'button-secondary import-digital-tax-rates',
				),
				array(
					'type' 		=> 'sectionend',
					'id'   		=> 'wc_euvat_digital_goods',
				),
				array(
					'id'   		=> 'wc_euvat_distance_selling',
					'title' 	=> esc_html__( 'EU Tax Handling - Distance Selling (B2C)', 'advanced-taxes-woocommerce' ),
					'type' 		=> 'title',
					'desc' 		=> sprintf( esc_html__( 'You need to register for EU Tax ID in countries where you reach %1$sDistance Selling EU Tax thresholds%2$s. Add countries where you are registered and the customers will be charged the local VAT. Applies only to products sold to consumers (B2C).', 'advanced-taxes-woocommerce' ), '<a href="https://www.vatlive.com/eu-vat-rules/distance-selling/distance-selling-eu-vat-thresholds/" target="_blank">', '</a>' ),
				),
				array(
					'id'      => 'wc_vat_distance_selling_enable',
					'title' 	=> esc_html__( 'EU VAT Handling for Distance Selling', 'advanced-taxes-woocommerce' ),
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Enable', 'advanced-taxes-woocommerce' ),
					'default' => 'no',
				),
				array(
					'id'   		=> 'wc_vat_distance_selling_countries',
					'title' 	=> esc_html__( 'Select countries for which you would like to import tax rates.', 'advanced-taxes-woocommerce' ),
					'type' 		=> 'multi_select_countries',
				),
				array(
					'id'      => 'wc_vat_distance_selling_rates',
					'title' 	=> esc_html__( 'Import tax rates for specific EU countries', 'advanced-taxes-woocommerce' ),
					'type'    => 'button',
					'default' => esc_html__( 'Import Taxes', 'advanced-taxes-woocommerce' ),
					'class'   => 'button-secondary import-distance-tax-rates',
				),
				array(
					'type' 		=> 'sectionend',
					'id'   		=> 'wc_euvat_distance_selling',
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
		 */
		public function ajax_digital_tax_rates() {
			// CSRF protection
			check_ajax_referer( '__wc_euvat_nonce', 'nonce' );

			$tax_name = esc_html__( 'Digital Goods', 'advanced-taxes-woocommerce' );
			$tax_slug = 'digital-goods';

			// Add taxes to DB
			$response = $this->add_taxes_to_db( $tax_name, $tax_slug );
			
			// Return response to JS
			wp_send_json( $response );
		}

		/**
		 * AJAX request for importing distance selling tax rates.
		 */
		public function ajax_distance_tax_rates() {
			// CSRF protection
			check_ajax_referer( '__wc_euvat_nonce', 'nonce' );

			$tax_name = esc_html__( 'Distance Selling', 'advanced-taxes-woocommerce' );
			$tax_slug = 'distance-selling';

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

			// Specific settings for `distance-selling`
			if ( 'distance-selling' === $slug ) {
				$countries = array_map( 'sanitize_text_field', $_POST['countries'] );
				$countries = json_decode( json_encode( $countries ), ARRAY_A );

				// Update countries cause we refresh the page after AJAX call
				// And, we don't want to lose the option set for importing taxes for the specific countries
				update_option( 'wc_vat_distance_selling_countries', $countries );
			}

			// Fetch tax rates
			$rates = new Rates();
			$data  = $rates->get_tax_rates();

      // Response which we will be sending back to the page
			$response = array(
				'status' 	=> 'error',
				'message' => esc_html__( 'Nothing has been added or updated in the database.', 'advanced-taxes-woocommerce' )
			);

			// Adding tax rates to the table
			if ( ! empty( $data ) && is_array( $data ) ) {
				// Counter to calculate query changes
				$i = 0;

				foreach ( $data as $key => $value ) {
					if ( 'distance-selling' === $slug && $countries ) {
						if ( ! in_array( $key, $countries ) ) {
							continue;
						}
					}

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
					'status' 	=> 'success',
					'message' => $i . ' tax entries have been updated'
				);
			}
			
			return $response;
		}

		

	}

}
