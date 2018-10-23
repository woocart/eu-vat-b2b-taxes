<?php

namespace Niteo\WooCart\BetterTaxHandling;

/**
 * Plugin Name: Better Tax Handling
 * Description: Better Tax Handling is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     1.0.0
 * Runtime:     5.4+
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
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

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
		$admin 	= new Admin();

		// Tax rates.
		$rates 	= new Rates();

		// Frontend.
		$user 	= new User();
	}

}

// Initialize Plugin.
$plugin = new BetterTaxHandling();
