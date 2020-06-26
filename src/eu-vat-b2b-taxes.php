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
			new Config();
			new Admin();
			new Rates();
			new UserView();
			new Reports();
		}

	}

	// Initialize
	new EUVatTaxes();

}
