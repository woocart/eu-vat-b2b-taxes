<?php
/**
 * Tests the user class.
 *
 * @package better-tax-handling
 */

use Niteo\WooCart\BetterTaxHandling\UserView;
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
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 */	 
	public function testConstructor() {
		$user = new UserView();

		\WP_Mock::expectActionAdded( 'init', [ $user, 'init' ] );

        $user->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::init
	 */
	public function testInit() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'admin_url', [
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'wp_create_nonce', [
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'wp_localize_script', [
				'return' => true
			]
		);

		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', [ $user, 'scripts' ] );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_review', [ $user, 'calculate_tax' ] );
		\WP_Mock::expectActionAdded( 'woocommerce_after_checkout_validation', [ $user, 'checkout_validation' ], PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_meta', [ $user, 'update_order_meta' ], 10, 1 );

		\WP_Mock::expectFilterAdded( 'woocommerce_billing_fields', [ $user, 'checkout_fields' ] );

        $user->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::scripts
	 */	 
	public function testScripts() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'wp_enqueue_script', [
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'wp_enqueue_style', [
				'return' => true
			]
		);

        $user->scripts();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::checkout_fields
	 */	 
	public function testCheckoutFields() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option', [
				'return' => 'none'
			]
		);

        $this->assertEquals( [], $user->checkout_fields( [] ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::checkout_fields
	 */	 
	public function testCheckoutFieldsNotEmpty() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'get_option', [
				'return' => 'notnone'
			]
		);

        $this->assertNotEquals( [], $user->checkout_fields( [] ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::return_tax
	 */	 
	public function testReturnTax() {
		$user = new UserView();

        $this->assertEmpty( $user->return_tax( '', '', '', false, false ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::checkout_validation
	 */	 
	public function testCheckoutValidation() {
		$user = new UserView();

		$_POST['business_check'] = 'yes';

		\WP_Mock::userFunction(
			'get_option', [
				'return' => 'yes'
			]
		);

		$mock = \Mockery::mock( '\WP_Error' );
		$mock->shouldReceive( 'add' )
			 ->with( 'billing', 'Business Tax ID is a required field.' )
			 ->andReturns( true );

        $user->checkout_validation( '', $mock );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\UserView::update_order_meta
	 */	 
	public function testUpdateOrderMeta() {
		$user = new UserView();

		$_POST['business_check'] 	= 'NOT_EMPTY';
		$_POST['business_tax_id'] 	= 'NOT_EMPTY';

		\WP_Mock::userFunction(
			'update_post_meta', [
				'return' => true
			]
		);
		\WP_Mock::userFunction(
			'sanitize_text_field', [
				'return' => true
			]
		);

		$user->update_order_meta( '1000' );
	}

}
