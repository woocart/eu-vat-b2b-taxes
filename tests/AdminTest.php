<?php
/**
 * Tests the admin class.
 *
 * @package eu-vat-b2b-taxes
 */

use Niteo\WooCart\EUVatTaxes\Admin;
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase {

	function setUp() {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();
	}

	function tearDown() {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 */
	public function testConstructor() {
		$admin = new Admin();

		\WP_Mock::expectActionAdded( 'admin_init', array( $admin, 'init' ) );

		$admin->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::init
	 */
	public function testInit() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'is_plugin_active',
			array(
				'return' => true,
			)
		);

		\WP_Mock::expectActionAdded( 'woocommerce_tax_settings', array( $admin, 'settings' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', array( $admin, 'scripts' ) );

		$admin->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::settings
	 */
	public function testSettings() {
		$admin = new Admin();

		$this->assertNotEquals( array(), $admin->settings( array() ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::scripts
	 */
	public function testScripts() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_create_nonce',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_localize_script',
			array(
				'return' => true,
			)
		);

		$admin->scripts();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_digital_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_remote_tax_rates
	 */
	public function testAjaxDigitalTaxRates() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'esc_html',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_site_transient',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_remote_get',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'is_wp_error',
			array(
				'return' => false,
			)
		);
		\WP_Mock::userFunction(
			'update_option',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_send_json_error',
			array(
				'return' => true,
			)
		);

		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' );
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					array(
						'DE' => '20.0',
						'SI' => '30.0',
					)
				);

		$admin->ajax_digital_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_distance_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_remote_tax_rates
	 */
	public function testAjaxDistanceTaxRates() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'esc_html',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_site_transient',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_remote_get',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'is_wp_error',
			array(
				'return' => false,
			)
		);
		\WP_Mock::userFunction(
			'update_option',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_send_json_error',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => true,
			)
		);

		$_POST['countries'] = array(
			'IN',
			'SI',
			'DE',
			'GB',
		);

		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' );
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					array(
						'DE' => '20.0',
						'SI' => '30.0',
					)
				);

		$admin->ajax_distance_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_tax_id_check
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testAjaxTaxIdCheck() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => true,
			)
		);

		$_POST['business_id'] = 'EU123456789';

		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Vies' );
		$mock->shouldReceive( 'isValid' )
				 ->with( $_POST['business_id'] )
				 ->andReturn( true );

		\WP_Mock::userFunction(
			'wp_send_json_error',
			array(
				'return' => true,
			)
		);

		$admin->ajax_tax_id_check();
	}

}
