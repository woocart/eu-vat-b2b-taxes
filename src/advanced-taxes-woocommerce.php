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

	/**
	 * Include composer autoload.
	 */
	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Constants for the plugin.
	 */
	$plugin_url = plugin_dir_url( __FILE__ );
	$version    = '@##VERSION##@';

	/**
	 * AdvancedTaxes class where all the action happens.
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
			// For WP admin.
			new Admin();

			// Tax rates.
			new Rates();

			// Frontend.
			new UserView();

			// Reports.
			new Reports();
		}

	}

	// Initialize Plugin.
	new AdvancedTaxes();

}
