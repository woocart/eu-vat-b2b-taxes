<?php
/**
 * Plugin Name: EU VAT & B2B Taxes
 * Description: EU VAT & B2B Taxes for WooCommerce is a plugin which helps with configuring complex tax variations when selling in or into the EU.
 * Version:     @##VERSION##@
 * Runtime:     5.6+
 * Author:      WooCart
 * Text Domain: eu-vat-b2b-taxes
 * Domain Path: i18n
 * Author URI:  https://woocart.com
 */

namespace Niteo\WooCart\EUVatTaxes {

	// Composer autoloader
	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * EUVatTaxes class which initializes plugin modules.
	 *
	 * @package WordPress
	 * @subpackage eu-vat-b2b-taxes
	 * @since 1.0.0
	 */
	class EUVatTaxes {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			// Initialize plugin configuration
			$config = new Config();

			// Run on plugin activation
			register_activation_hook( __FILE__, array( $config, 'activation_check' ) );

			// Check for compatible environment
			if ( $config->is_environment_compatible() && $config->is_plugin_compatible() ) {
				// Load rest of the modules
				new Admin();
				new Rates();
				new UserView();
				new Reports();
			}
		}

	}

	// Initialize
	new EUVatTaxes();

}
