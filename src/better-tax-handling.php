<?php
/**
 * Plugin Name: Better Tax Handling
 * Description: Better Tax Handling is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     @##VERSION##@
 * Runtime:     7.2+
 * Author:      WooCart
 * Text Domain: better-tax-handling
 * Domain Path: /i18n/
 * Author URI:  www.woocart.com
 */

namespace Niteo\WooCart\BetterTaxHandling {

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
	 * BetterTaxHandling class where all the action happens.
	 *
	 * @package WordPress
	 * @subpackage better-tax-handling
	 * @since 1.0.0
	 */
	class BetterTaxHandling {

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
	new BetterTaxHandling();

}
