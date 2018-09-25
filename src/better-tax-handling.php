<?php

namespace Niteo\Woocart\BetterTaxHandling;

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

/**
 * BetterTaxHandling class where all the action happens.
 *
 * @package WordPress
 * @subpackage better-tax-handling
 * @since 1.0.0
 */
class BetterTaxHandling {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		if ( class_exists( 'WooCommerce' ) )  {
			print_r( woocommerce_admin_fields() );
		}
	}

	/**
	 * Attached to the activation hook.
	 */
	public function activate_plugin() {
		
	}

	/**
	 * Attached to the de-activation hook.
	 */
	public function deactivate_plugin() {
		
	}

}

// Initialize Plugin.
if ( defined( 'ABSPATH' ) ) {
	$niteo_tax_handling = new BetterTaxHandling();

	// Activation Hook.
	register_activation_hook( __FILE__, array( &$niteo_tax_handling, 'activate_plugin' ) );

	// Deactivation Hook.
	register_deactivation_hook( __FILE__, array( &$niteo_tax_handling, 'deactivate_plugin' ) );
}
