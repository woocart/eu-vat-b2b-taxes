<?php
/**
 * Setup tests.
 */

define( 'Plugin_Url', 'xyz' );
define( 'Version', '1.0' );

$root_dir = dirname( dirname( __FILE__ ) );
require_once "$root_dir/vendor/autoload.php";

class WC_Admin_Report {
	public function check_current_range_nonce() {}
	public function calculate_current_range() {}
}

WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();
