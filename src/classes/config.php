<?php
/**
 * Configuration file for the plugin.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\EUVatTaxes {

	/**
	 * For fetching tax rates and adding them to the settings.
	 */
	class Config {

		/**
		 * @var string
		 */
		public const PLUGIN_NAME = 'EU VAT & B2B Taxes';

		/**
		 * @var string
		 */
		public const PLUGIN_BASE = 'eu-vat-b2b-taxes/eu-vat-b2b-taxes.php';

		/**
		 * @var string
		 */
		public const VERSION = '@##VERSION##@';

		/**
		 * @var string
		 */
		public const MINIMUM_PHP_VERSION = '5.6';

		/**
		 * @var string
		 */
		public const MINIMUM_WP_VERSION = '4.8.12';

		/**
		 * @var string
		 */
		public const MINIMUM_WC_VERSION = '4.2.0';

		/**
		 * @var string
		 */
		public static $plugin_url;

		/**
		 * @var string
		 */
		public static $plugin_path;

		/**
		 * @var array
		 */
		private $notices = array();

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'check_environment' ) );
			add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Set URL & path to plugin directory.
		 */
		public function init() {
			self::$plugin_url  = plugin_dir_url( dirname( __FILE__ ) );
			self::$plugin_path = plugin_dir_path( dirname( __FILE__ ) );
		}

		/**
		 * Checks the environment on loading WordPress, just in case the environment changes after activation.
		 */
		public function check_environment() {
			if ( ! $this->is_environment_compatible() && is_plugin_active( self::PLUGIN_BASE ) ) {
				$this->deactivate_plugin();
				$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
			}
		}

		/**
		 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
		 */
		public function add_plugin_notices() {
			// Check for WP version
			if ( ! $this->is_wp_compatible() ) {
				$this->add_admin_notice(
					'update_wordpress',
					'error',
					sprintf(
						'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
						'<strong>' . self::PLUGIN_NAME . '</strong>',
						self::MINIMUM_WP_VERSION,
						'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">',
						'</a>'
					)
				);
			}

			// Check for WooCommerce version
			if ( ! $this->is_wc_compatible() ) {
				$this->add_admin_notice(
					'update_woocommerce',
					'error',
					sprintf(
						'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
						'<strong>' . self::PLUGIN_NAME . '</strong>',
						self::MINIMUM_WC_VERSION,
						'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">',
						'</a>',
						'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip' ) . '">',
						'</a>'
					)
				);
			}
		}

		/**
		 * Displays any admin notices added with add_admin_notice()
		 */
		public function admin_notices() {
			foreach ( (array) $this->notices as $notice_key => $notice ) {
				?>

				<div class="<?php echo esc_attr( $notice['class'] ); ?>">
					<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
				</div>

				<?php
			}
		}

		/**
		 * Adds an admin notice to be displayed.
		 *
		 * @param string $slug the slug for the notice
		 * @param string $class the css class for the notice
		 * @param string $message the notice message
		 */
		private function add_admin_notice( $slug, $class, $message ) {
			$this->notices[ $slug ] = array(
				'class'   => $class,
				'message' => $message,
			);
		}

		/**
		 * Determines if the server environment is compatible with this plugin.
		 *
		 * @return bool
		 */
		public function is_environment_compatible() {
			return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
		}

		/**
		 * Gets the message for display when the environment is incompatible with this plugin.
		 *
		 * @return string
		 */
		public function get_environment_message() {
			return sprintf(
				esc_html__( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'eu-vat-b2b-taxes' ),
				self::MINIMUM_PHP_VERSION,
				PHP_VERSION
			);
		}

		/**
		 * Determines if the plugin is compatible to run.
		 *
		 * @return bool
		 */
		public function is_plugin_compatible() {
			return $this->is_wp_compatible() && $this->is_wc_compatible();
		}

		/**
		 * Determines if the WordPress compatible.
		 *
		 * @return bool
		 */
		private function is_wp_compatible() {
			if ( ! self::MINIMUM_WP_VERSION ) {
				return true;
			}

			return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
		}

		/**
		 * Determines if the WooCommerce compatible.
		 *
		 * @return bool
		 */
		private function is_wc_compatible() {
			if ( ! self::MINIMUM_WC_VERSION ) {
				return true;
			}

			return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
		}

		/**
		 * Deactivates the plugin.
		 */
		protected function deactivate_plugin() {
			deactivate_plugins( self::PLUGIN_BASE );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

	}

}
