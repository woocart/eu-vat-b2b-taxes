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
			'is_plugin_active', [
				'return' => true
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
			'wp_enqueue_script', [
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

        $admin->scripts();
	}

}
