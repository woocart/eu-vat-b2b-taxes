<?php
/**
 * Tests the user class.
 *
 * @package eu-vat-b2b-taxes
 */

use Niteo\WooCart\EUVatTaxes\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {

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
	 * @covers \Niteo\WooCart\EUVatTaxes\Config::__construct
	 */
	public function testConstructor() {
		$config = new Config();

		\WP_Mock::expectActionAdded( 'init', array( $config, 'init' ) );

		$config->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Config::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Config::init
	 */
	public function testInit() {
		$config = new Config();

		\WP_Mock::userFunction(
			'\plugin_dir_url',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'\plugin_dir_path',
			array(
				'return' => true,
			)
		);

		$config->init();
	}

}
