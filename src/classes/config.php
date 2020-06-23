<?php
/**
 * Configuration file for the plugin.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\AdvancedTaxes {

	/**
	 * For fetching tax rates and adding them to the settings.
	 */
	class Config {

    /**
     * @var string
     */
    public const VERSION = '@##VERSION##@';

    /**
     * @var string
     */
    public static $plugin_url;

    /**
     * @var string
     */
    public static $plugin_path;

    /**
		 * Class constructor.
		 */
		public function __construct() {
      self::$plugin_url   = plugin_dir_url( dirname( __FILE__ ) );
      self::$plugin_path  = plugin_dir_path( dirname( __FILE__ ) );
		}

  }

}
