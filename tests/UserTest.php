<?php
/**
 * Tests the user class.
 *
 * @package eu-vat-b2b-taxes
 */

use Niteo\WooCart\EUVatTaxes\UserView;
use Niteo\WooCart\EUVatTaxes\Vies;
use PHPUnit\Framework\TestCase;

class UserViewTest extends TestCase {

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
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 */
	public function testConstructor() {
		$user = new UserView();

		\WP_Mock::expectActionAdded( 'init', array( $user, 'init' ) );

		$user->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::init
	 */
	public function testInit() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'admin_url',
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

		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', array( $user, 'scripts' ) );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_review', array( $user, 'calculate_tax' ) );
		\WP_Mock::expectActionAdded( 'woocommerce_after_checkout_validation', array( $user, 'checkout_validation' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_meta', array( $user, 'update_order_meta' ), 10, 1 );

		\WP_Mock::expectFilterAdded( 'woocommerce_billing_fields', array( $user, 'checkout_fields' ) );

		$user->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::scripts
	 */
	public function testScripts() {
		$user = new UserView();

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

		$user->scripts();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::checkout_fields
	 */
	public function testCheckoutFields() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => 'none',
			)
		);

		$this->assertEquals( array(), $user->checkout_fields( array() ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::checkout_fields
	 */
	public function testCheckoutFieldsNotEmpty() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => 'notnone',
			)
		);

		$this->assertNotEquals( array(), $user->checkout_fields( array() ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::return_tax
	 */
	public function testReturnTax() {
		$user = new UserView();

		$this->assertEmpty( $user->return_tax( '', '', '', false, false ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::checkout_validation
	 */
	public function testCheckoutValidation() {
		$user = new UserView();

		$_POST['business_check'] = 'yes';

		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => 'yes',
			)
		);

		$mock = \Mockery::mock( '\WP_Error' );
		$mock->shouldReceive( 'add' )
			 ->with( 'billing', 'Business Tax ID is a required field.' )
			 ->andReturns( true );

		$user->checkout_validation( '', $mock );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::update_order_meta
	 */
	public function testUpdateOrderMeta() {
		$user = new UserView();

		$_POST['business_check']  = 'NOT_EMPTY';
		$_POST['business_tax_id'] = 'NOT_EMPTY';

		\WP_Mock::userFunction(
			'update_post_meta',
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

		$user->update_order_meta( '1000' );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::calculate_tax
	 */
	public function testCalculateTaxb2bNone() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'b2b_sales',
				),
				'return' => 'none',
			)
		);

		$user->calculate_tax( 'billing_country=India&business_check=1' );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::calculate_tax
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testCalculateTaxb2bNotNone() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'b2b_sales',
				),
				'return' => 'not_none',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_home_country',
				),
				'return' => 'no',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_charge_vat',
				),
				'return' => 'yes',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_eu_with_vatid',
				),
				'return' => 'yes',
			)
		);
		\WP_Mock::userFunction(
			'wc_get_base_location',
			array(
				'return' => array(
					'country' => 'India',
				),
			)
		);

		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Vies' );
		$mock->shouldReceive( 'isValid' )
				->andReturn( true );

		$user->calculate_tax( 'billing_country=India&business_check=1&business_tax_id=VAT_ID' );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\UserView::calculate_tax
	 */
	public function testCalculateTaxb2bNotNoneDiffCountry() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'b2b_sales',
				),
				'return' => 'not_none',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_home_country',
				),
				'return' => 'no',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_charge_vat',
				),
				'return' => 'yes',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array(
					'tax_eu_with_vatid',
				),
				'return' => 'yes',
			)
		);
		\WP_Mock::userFunction(
			'wc_get_base_location',
			array(
				'return' => array(
					'country' => 'India',
				),
			)
		);

		$user->calculate_tax( 'billing_country=Spain&business_check=1' );
	}

}
