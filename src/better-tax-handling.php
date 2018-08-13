<?php

namespace Woocart\BetterTaxHandling;

/**
 * Plugin Name: Better Tax Handling
 * Description: Better Tax Handling is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     1.0.0
 * Runtime:     5.3+
 * Author:      WooCart
 * Text Domain: better-tax-handling
 * Domain Path: /langs/
 * Author URI:  www.woocart.com
 */

/**
 * Checks for PHP version and stop the plugin if the version is < 5.4.0.
 */
if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
	?>
	<div id="error-page">
		<p>
		<?php
		esc_html_e(
			'This plugin requires PHP 5.4.0 or higher. Please contact your hosting provider about upgrading your
			server software. Your PHP version is', 'better-tax-handling'
		);
		?>
		<b><?php echo esc_html( PHP_VERSION ); ?></b></p>
	</div>
	<?php
	die();
}

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
