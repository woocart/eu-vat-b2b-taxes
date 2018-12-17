<?php
/**
 * Tests the admin class.
 *
 * @package better-tax-handling
 */

use Niteo\WooCart\BetterTaxHandling\Admin;
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
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 */
	public function testConstructor() {
		$admin = new Admin();

		\WP_Mock::expectActionAdded( 'admin_init', [ $admin, 'init' ] );

		$admin->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::init
	 */
	public function testInit() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'is_plugin_active',
			[
				'return' => true,
			]
		);

		\WP_Mock::expectActionAdded( 'woocommerce_tax_settings', [ $admin, 'settings' ], PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $admin, 'scripts' ] );

		$admin->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::settings
	 */
	public function testSettings() {
		$admin = new Admin();

		$this->assertNotEquals( [], $admin->settings( [] ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::scripts
	 */
	public function testScripts() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_create_nonce',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_localize_script',
			[
				'return' => true,
			]
		);

		$admin->scripts();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::ajax_digital_tax_rates
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::fetch_remote_tax_rates
	 */
	public function testAjaxDigitalTaxRates() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'esc_html',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'get_option',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'get_site_transient',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_remote_get',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'return' => false,
			]
		);
		\WP_Mock::userFunction(
			'update_option',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_send_json_error',
			[
				'return' => true,
			]
		);

		$mock = \Mockery::mock( '\Niteo\WooCart\BetterTaxHandling\Rates' );
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					[
						'DE' => '20.0',
						'SI' => '30.0',
					]
				);

		$admin->ajax_digital_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::ajax_distance_tax_rates
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::fetch_remote_tax_rates
	 */
	public function testAjaxDistanceTaxRates() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'esc_html',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'get_option',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'get_site_transient',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_remote_get',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'return' => false,
			]
		);
		\WP_Mock::userFunction(
			'update_option',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'wp_send_json_error',
			[
				'return' => true,
			]
		);

		$_POST['countries'] = [
			'IN',
			'SI',
			'DE',
			'GB',
		];

		$mock = \Mockery::mock( '\Niteo\WooCart\BetterTaxHandling\Rates' );
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					[
						'DE' => '20.0',
						'SI' => '30.0',
					]
				);

		$admin->ajax_distance_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Admin::ajax_tax_id_check
	 * @covers \Niteo\WooCart\BetterTaxHandling\Vies::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Vies::isValid
	 * @covers \Niteo\WooCart\BetterTaxHandling\Vies::isValidCountryCode
	 */
	public function testAjaxTaxIdCheck() {
		$admin = new Admin();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			[
				'return' => true,
			]
		);
		\WP_Mock::userFunction(
			'sanitize_text_field',
			[
				'return' => true,
			]
		);

		$_POST['business_id'] = 'EU123456789';

		$mock = \Mockery::mock( '\Niteo\WooCart\BetterTaxHandling\Vies' );
		$mock->shouldReceive( 'isValid' )
				 ->with( $_POST['business_id'] )
				 ->andReturn( true );

		\WP_Mock::userFunction(
			'wp_send_json_error',
			[
				'return' => true,
			]
		);

		$admin->ajax_tax_id_check();
	}

}
