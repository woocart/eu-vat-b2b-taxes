<?php
/**
 * Tests the user class.
 *
 * @package eu-vat-b2b-taxes
 */

namespace Niteo\WooCart\EUVatTaxes;

use Niteo\WooCart\EUVatTaxes\Config;
use PHPUnit\Framework\TestCase;

/**
 * Tests Config class functions in isolation.
 *
 * @package Niteo\WooCart\EUVatTaxes
 * @coversDefaultClass \Niteo\WooCart\EUVatTaxes\Config
 */
class ConfigTest extends TestCase {

	function setUp() : void {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();
	}

	function tearDown() : void {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructor() {
		$config = new Config();

		\WP_Mock::expectActionAdded( 'init', array( $config, 'init' ) );

		$config->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::init
	 */
	public function testInit() {
		$config = new Config();

		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'return' => true,
			)
		);

		$config->init();
	}

	/**
	 * @covers ::__construct
	 * @covers ::check_environment
	 * @covers ::is_environment_compatible
	 * @covers ::deactivate_plugin
	 * @covers ::add_admin_notice
	 * @covers ::get_environment_message
	 * @covers ::get_php_version
	 * @covers ::get_plugin_name
	 * @covers ::get_plugin_base
	 */
	public function testCheckEnvironment() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'is_environment_compatible' )->andReturn( false );

		$_GET['activate'] = 'yes';

		\WP_Mock::userFunction(
			'is_plugin_active',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'deactivate_plugins',
			array(
				'return' => true,
			)
		);

		$mock->check_environment();
	}

	/**
	 * @covers ::__construct
	 * @covers ::add_plugin_notices
	 * @covers ::is_wp_compatible
	 * @covers ::is_wc_compatible
	 * @covers ::add_admin_notice
	 * @covers ::get_plugin_name
	 * @covers ::get_wc_version
	 * @covers ::get_wp_version
	 */
	public function testAddPluginNotices() {
		$config = new Config();

		\WP_Mock::userFunction(
			'get_bloginfo',
			array(
				'return' => '4.0',
			)
		);
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'esc_url',
			array(
				'return' => '#',
			)
		);

		$config->add_plugin_notices();
	}

	/**
	 * @covers ::__construct
	 * @covers ::admin_notices
	 */
	public function testAdminNotices() {
		$config          = new Config();
		$config->notices = array(
			'notice1' => array(
				'class'   => 'class1',
				'message' => 'message1',
			),
		);

		\WP_Mock::userFunction(
			'wp_kses',
			array(
				'return' => 'message1',
			)
		);

		$this->expectOutputString( '<div class="class1"><p>message1</p></div>' );
		$config->admin_notices();
	}

	/**
	 * @covers ::__construct
	 * @covers ::is_wc_compatible
	 * @covers ::get_wc_version
	 */
	public function testIsWcCompatible() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'get_wc_version' )->andReturn( false );
		$this->assertTrue( $mock->is_wc_compatible() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::is_wp_compatible
	 * @covers ::get_wp_version
	 */
	public function testIsWpCompatible() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'get_wp_version' )->andReturn( false );
		$this->assertTrue( $mock->is_wp_compatible() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::is_plugin_compatible
	 * @covers ::is_wp_compatible
	 * @covers ::is_wc_compatible
	 * @covers ::get_wp_version
	 * @covers ::get_wc_version
	 */
	public function testIsPluginCompatible() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'is_wp_compatible', 'is_wc_compatible' )->andReturn( true );
		$this->assertTrue( $mock->is_plugin_compatible() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::is_environment_compatible
	 * @covers ::get_php_version
	 */
	public function testEnvironmentCompatibleTrue() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'get_php_version' )->andReturn( '1.0' );
		$this->assertTrue( $mock->is_environment_compatible() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::is_environment_compatible
	 * @covers ::get_php_version
	 */
	public function testEnvironmentCompatibleFalse() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Config' )->makePartial();
		$mock->shouldReceive( 'get_php_version' )->andReturn( '100.0' );
		$this->assertFalse( $mock->is_environment_compatible() );
	}

}
