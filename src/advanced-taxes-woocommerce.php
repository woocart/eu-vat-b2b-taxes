<?php
/**
 * Plugin Name: Advanced Taxes for WooCommerce
 * Description: Advanced Taxes for WooCommerce is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     @##VERSION##@
 * Runtime:     5.6+
 * Author:      WooCart
 * Text Domain: advanced-taxes-woocommerce
 * Domain Path: i18n
 * Author URI:  www.woocart.com
 */

namespace Niteo\WooCart\AdvancedTaxes {

	// Composer autoloader
	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * AdvancedTaxes class which initializes plugin modules.
	 *
	 * @package WordPress
	 * @subpackage advanced-taxes-woocommerce
	 * @since 1.0.0
	 */
	class AdvancedTaxes {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			new Admin();
			new Rates();
			new UserView();
			new Reports();
		}

	}

	// Initialize
	new AdvancedTaxes();

}
