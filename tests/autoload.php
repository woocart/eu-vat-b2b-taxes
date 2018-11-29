<?php
/**
 * Setup tests.
 */

define( 'Plugin_Url', 'xyz' );
define( 'Version', '1.0' );

$root_dir = dirname( dirname( __FILE__ ) );
require_once "$root_dir/vendor/autoload.php";

class WC_Admin_Report {}

WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();
