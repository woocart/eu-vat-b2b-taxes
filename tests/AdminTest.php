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

		\WP_Mock::expectActionAdded( 'init', array( $admin, 'init' ) );

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

		\WP_Mock::expectFilterAdded( 'woocommerce_get_settings_tax', array( $admin, 'settings' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', array( $admin, 'scripts' ) );
		\WP_Mock::expectActionAdded( 'woocommerce_admin_field_button', array( $admin, 'button_field' ) );
		\WP_Mock::expectActionAdded( 'wp_ajax_add_digital_taxes', array( $admin, 'ajax_digital_tax_rates' ) );
		\WP_Mock::expectActionAdded( 'wp_ajax_add_distance_taxes', array( $admin, 'ajax_distance_tax_rates' ) );
		\WP_Mock::expectActionAdded( 'wp_ajax_add_tax_id_check', array( $admin, 'ajax_tax_id_check' ) );
		\WP_Mock::expectActionAdded( 'woocommerce_admin_order_data_after_billing_address', array( $admin, 'order_meta' ) );

		$admin->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::settings
	 */
	public function testSettings() {
		$admin = new Admin();

		$this->assertNotEquals( array(), $admin->settings( array(), '' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::scripts
	 */
	public function testScriptsOrderPage() {
		global $post;
		$post = (object) array(
			'post_type' => 'shop_order'
		);

		$admin = new Admin();

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_enqueue_style',
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

		$admin->scripts( 'post.php' );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_digital_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_taxes_to_db
	 */
	public function testAjaxDigitalTaxRates() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )
						->makePartial();
		$mock->shouldReceive( 'add_taxes_to_db' )
				 ->andReturn( true );

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_send_json',
			array(
				'return' => true,
			)
		);

		$mock->ajax_digital_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_distance_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_taxes_to_db
	 */
	public function testAjaxDistanceTaxRates() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )
						->makePartial();
		$mock->shouldReceive( 'add_taxes_to_db' )
				 ->andReturn( true );

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_send_json',
			array(
				'return' => true,
			)
		);

		$mock->ajax_distance_tax_rates();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::order_meta
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_html
	 */
	public function testOrderMeta() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )->makePartial();
		$mock->shouldReceive( 'add_html' )->andReturn( true );

		$order = new class {
			function get_id() {
				return true;
			}
		};

		\WP_Mock::userFunction(
			'absint',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_post_meta',
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

		$mock->order_meta( $order );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_tax_id_check
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::vies
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testAjaxTaxIdCheckInvalid() {
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
				'times' => 2,
				'return' => true,
			)
		);

		$_POST['business_id'] = 'XX000000';
		$_POST['order_id'] = '100';

		\WP_Mock::userFunction(
			'update_post_meta',
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

		$admin->ajax_tax_id_check();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::ajax_tax_id_check
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::vies
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testAjaxTaxIdCheckValid() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )->makePartial();

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'times' => 2,
				'return' => true,
			)
		);

		$_POST['business_id'] = 'SI9999999';
		$_POST['order_id'] = '100';

		$vies = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Vies' );
		$vies->shouldReceive( 'isValid' )->andReturn( true );

		$mock->shouldReceive( 'vies' )->andReturn( $vies );

		\WP_Mock::userFunction(
			'update_post_meta',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'return' => true,
			)
		);

		$mock->ajax_tax_id_check();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_taxes_to_db
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testAddTaxesToDbDigital() {
		global $wpdb;
		$wpdb = new class {
			public $prefix = 'wp_';

			function prepare() {
				return true;
			}
			function get_row() {
				return true;
			}
			function update() {
				return true;
			}
		};

		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )->makePartial();
		$rates = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' );
		$rates->shouldReceive( 'get_tax_rates' )
					->andReturn(
						array(
							'DE' => [
								'standard_rate' => '20.0'
							],
							'SI' => [
								'standard_rate' => '30.0'
							],
						)
					);
		$mock->shouldReceive( 'rates' )->andReturn( $rates );
		$mock->add_taxes_to_db( 'Digital Goods', 'digital-goods' );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_taxes_to_db
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::get_countries
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testAddTaxesToDbDistance() {
		global $wpdb;

		$wpdb = new class {
			public $prefix = 'wp_';

			function prepare() {
				return true;
			}
			function get_row() {
				return false;
			}
			function update() {
				return true;
			}
			function insert() {
				return true;
			}
		};

		
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )->makePartial();

		\WP_Mock::userFunction(
			'update_option',
			array(
				'return' => true,
			)
		);

		$rates = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' );
		$rates->shouldReceive( 'get_tax_rates' )
					->andReturn(
						array(
							'DE' => [
								'standard_rate' => '20.0'
							],
							'SI' => [
								'standard_rate' => '30.0'
							],
						)
					);
		$mock->shouldReceive( 'rates' )->andReturn( $rates );
		$mock->shouldReceive( 'get_countries' )->andReturn( [ 'DE', 'SI' ] );

		$this->assertEquals(
			array(
				'status' => 'success',
				'message' => '2 tax entries have been updated'
			), $mock->add_taxes_to_db( 'Distance Selling', 'distance-selling' )
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::add_taxes_to_db
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::get_countries
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testAddTaxesToDbDistanceNoCountries() {
		global $wpdb;

		$wpdb = new class {
			public $prefix = 'wp_';

			function prepare() {
				return true;
			}
			function get_row() {
				return false;
			}
			function update() {
				return true;
			}
			function insert() {
				return true;
			}
		};

		
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Admin' )->makePartial();

		\WP_Mock::userFunction(
			'update_option',
			array(
				'return' => true,
			)
		);

		$rates = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' );
		$rates->shouldReceive( 'get_tax_rates' )
					->andReturn(
						array(
							'DE' => [
								'standard_rate' => '20.0'
							],
							'SI' => [
								'standard_rate' => '30.0'
							],
						)
					);
		$mock->shouldReceive( 'rates' )->andReturn( $rates );
		$mock->shouldReceive( 'get_countries' )->andReturn( [ 'FR', 'BE' ] );

		$this->assertEquals(
			array(
				'status' => 'success',
				'message' => '0 tax entries have been updated'
			), $mock->add_taxes_to_db( 'Distance Selling', 'distance-selling' )
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::get_countries
	 */
	public function testGetCountries() {
		$admin = new Admin();

		$_POST['countries'] = [
			'DE',
			'SI'
		];

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'times' => 2,
				'return' => 'SANITIZED_VALUE',
			)
		);

		$this->assertEquals(
			[ 'SANITIZED_VALUE', 'SANITIZED_VALUE' ],
			$admin->get_countries()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Admin::rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 */
	public function testRates() {
		$admin = new Admin();

		$this->assertInstanceOf(
			'\Niteo\WooCart\EUVatTaxes\Rates',
			$admin->rates()
		);
	}

}
