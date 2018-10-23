<?php

namespace Niteo\WooCart\BetterTaxHandling;

/**
 * Plugin Name: Better Tax Handling
 * Description: Better Tax Handling is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     1.0.0
 * Runtime:     7.2+
 * Author:      WooCart
 * Text Domain: better-tax-handling
 * Domain Path: /langs/
 * Author URI:  www.woocart.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include composer autoload.
 */
require_once 'vendor/autoload.php';

/**
 * BetterTaxHandling class where all the action happens.
 *
 * @package WordPress
 * @subpackage better-tax-handling
 * @since 1.0.0
 */
class BetterTaxHandling {

	/**
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * @var string
	 */
	public static $plugin_url;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Get the directory URL.
		self::$plugin_url = plugin_dir_url( __FILE__ );

		// For WP admin.
		new Admin();

		// Tax rates.
		new Rates();

		// Frontend.
		new UserView();
	}

}

// Initialize Plugin.
new BetterTaxHandling();
