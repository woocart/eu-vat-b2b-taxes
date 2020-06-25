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
		private $source = 'assets/json/rates.json';

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
			wp_enqueue_script( 'euvat-vendors', Config::$plugin_url . 'assets/js/vendors.js', array(), Config::VERSION, true );
			wp_enqueue_script( 'euvat-admin', Config::$plugin_url . 'assets/js/admin.js', array( 'jquery' ), Config::VERSION, true );
			wp_enqueue_style( 'euvat-admin', Config::$plugin_url . 'assets/css/admin.css', array(), Config::VERSION );

			// Fetch tax rates
			$tax_rates = $this->get_tax_rates();

			// Add data to be passed to an array
			$localize = array(
				'nonce' 							=> wp_create_nonce( '__wc_euvat_nonce' ),
				'tax_rates' 					=> $tax_rates,
				'add_update_text' 		=> esc_html__( 'Add / Update EU Tax Rates', 'advanced-taxes-woocommerce' ),
				'name_text' 					=> esc_html__( 'Name', 'advanced-taxes-woocommerce' ),
				'use_rate_text' 			=> esc_html__( 'Use rates', 'advanced-taxes-woocommerce' ),
				'name_desc_text'  		=> esc_html__( 'The description that will be used when using the button for mass adding/updating of EU rates', 'advanced-taxes-woocommerce' ),
				'name_value_text' 		=> esc_html__( 'Tax', 'advanced-taxes-woocommerce' ),
				'grab_tax_text' 			=> esc_html__( 'Grab all the EU Tax rates at the click of a button.', 'advanced-taxes-woocommerce' ),
				'known_rates_key' 		=> implode( '", "', array_keys( $this->known_rates ) ),
				'known_rates_values' 	=> implode( '", "', array_values( $this->known_rates ) ),
				'which_rate' 					=> $this->which_rate,
			);

			// Pass data to JS
			wp_localize_script(
				'euvat-admin',
				'wc_euvat_l10n',
				$localize
			);
		}

		/**
		 * Get tax rates.
		 *
		 * @param bool $use_transient Whether to look for data in the transient.
		 */
		public function get_tax_rates( $use_transient = true ) {
			if ( ! empty( $this->rates ) ) {
				return $this->rates;
			}

			$rates = $use_transient ? get_site_transient( 'wc_euvat_tax_rates' ) : array();

			// If data is not present in transients, then fetch it from JSON file
			if ( empty( $rates ) ) {
				$rates = $this->fetch_tax_rates();
			}

			// The array we return should use ISO country codes
			if ( ! empty( $rates ) ) {
				$corrected_rates = array();

				foreach ( $rates as $country => $rate ) {
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

				set_site_transient( 'wc_euvat_tax_rates', $corrected_rates, 43200 );
				$this->rates = $corrected_rates;
			}

			return $this->rates;
		}

		/**
		 * Fetch tax rates from remote URL.
		 */
		public function fetch_tax_rates() {
			$get = file_get_contents( Config::$plugin_path . $this->source, true );

			// Decode the JSON file so that we have an array of tax rates
			if ( $get ) {
				$rates = json_decode( $get, true );

				return $rates['rates'];
			}

			return false;
		}

		/**
		 * Convert from ISO 3166-1 country code to country VAT code.
		 * https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes
		 * http://ec.europa.eu/taxation_customs/resources/documents/taxation/vat/how_vat_works/rates/vat_rates_en.pdf
		 */
		public function get_tax_code( $country ) {
			// Deal with exceptions
			switch ( $country ) {
				case 'GR':
					$country = 'EL';
					break;
				case 'IM':
				case 'GB':
					$country = 'UK';
					break;
				case 'MC':
					$country = 'FR';
					break;
			}

			return $country;
		}

		/**
		 * Fetch ISO code.
		 */
		public function get_iso_code( $country ) {
			// Deal with exceptions
			switch ( $country ) {
				case 'EL':
					$country = 'GR';
					break;
				case 'UK':
					$country = 'GB';
					break;
			}

			return $country;
		}

		/**
		 * Takes an EU country code.
		 * Available rates: standard_rate, reduced_rate
		 * @see get_tax_code()
		 *
		 * @param string $country_code ISO code for the country
		 * @param string $rate Tax rate to be fetched
		 *
		 * @return bool|string
		 */
		public function get_tax_rate_for_country( $country_code, $rate = 'standard_rate' ) {
			$rates = $this->get_tax_rates();

			if ( empty( $rates ) ) {
				return false;
			}

			if ( ! is_array( $rates ) ) {
				return false;
			}

			if ( ! isset( $rates[ $country_code ] ) ) {
				return false;
			}

			if ( ! isset( $rates[ $country_code ][ $rate ] ) ) {
				return false;
			}

			return $rates[ $country_code ][ $rate ];
		}

	}

}
