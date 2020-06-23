<?php
/**
 * Handles fetching & updating tax rates.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\AdvancedTaxes {

	/**
	 * For fetching tax rates and adding them to the settings.
	 *
	 * @since 1.0.0
	 */
	class Rates {

		/**
		 * @var array
		 */
		private $rates = array();

		/**
		 * @var array
		 */
		private $known_rates;

		/**
		 * @var string
		 */
		private $which_rate = 'standard_rate';

		/**
		 * @var array
		 */
		private $source = 'json/rates.json';

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		 * Initialize 
		 */
		public function init() {
			global $pagenow;

			$this->known_rates = array(
				'standard_rate' => esc_html__( 'Standard Rate', 'advanced-taxes-woocommerce' ),
				'reduced_rate'  => esc_html__( 'Reduced Rate', 'advanced-taxes-woocommerce' ),
			);

			// Check for tax settings and tab
			if ( 'admin.php' == $pagenow ) {
				if ( ! isset( $_REQUEST['page'] ) ) {
					return;
				}

				if ( ! isset( $_REQUEST['tab'] ) ) {
					return;
				}

				if ( ! isset( $_REQUEST['section'] ) ) {
					return;
				}

				$current_page = sanitize_text_field( $_REQUEST['page'] );

				// Should be on WooCommerce settings page
				if ( 'woocommerce_settings' == $current_page || 'wc-settings' == $current_page ) {
					$tab = sanitize_text_field( $_REQUEST['tab'] );

					if ( 'tax' == $tab ) {
						$section = sanitize_text_field( $_REQUEST['section'] );

						// Set standard rate
						$this->which_rate = 'standard_rate';

						// Reduced rate if on a different tab
						if ( 'reduced-rate' == $section ) {
							$this->which_rate = 'reduced_rate';
						}

						// Add tax rates data to footer
						add_action( 'admin_footer', array( &$this, 'footer' ) );
					}
				}
			}
		}

		/**
		 * Fetches tax rates and passes it to JS for processing.
		 */
		public function footer() {
			$get_rates        = $this->get_tax_rates();
			$rates            = ( is_array( $get_rates ) ) ? $get_rates : array();
			$rate_description = esc_html__( 'Add / Update EU Tax Rates', 'advanced-taxes-woocommerce' );
		}

		/**
		 * Get tax rates.
		 */
		public function get_tax_rates( $use_transient = true ) {
			if ( ! empty( $this->rates ) ) {
				return $this->rates;
			}

			$rates = ( $use_transient ) ? get_site_transient( 'tax_rates_byiso' ) : array();

			if ( empty( $rates ) ) {
				$rates = $this->fetch_tax_rates();
			}

			// The array we return should use ISO country codes
			if ( ! empty( $new_rates ) ) {
				$corrected_rates = array();

				foreach ( $new_rates as $country => $rate ) {
					$iso                     = $this->get_iso_code( $country );
					$corrected_rates[ $iso ] = $rate;
				}

				// Monaco
				if ( isset( $corrected_rates['FR'] ) ) {
					$corrected_rates['MC'] = $corrected_rates['FR'];
				}

				// Isle of Man
				if ( isset( $corrected_rates['GB'] ) ) {
					$corrected_rates['IM']            = $corrected_rates['GB'];
					$corrected_rates['IM']['country'] = esc_html__( 'Isle of Man', 'advanced-taxes-woocommerce' );
				}

				set_site_transient( 'tax_rates_byiso', $corrected_rates, 43200 );
				$this->rates = $corrected_rates;
			}

			return $this->rates;
		}

	}

}
