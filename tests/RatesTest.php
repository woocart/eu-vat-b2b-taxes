<?php
/**
 * Tests the rates class.
 *
 * @package better-tax-handling
 */

use Niteo\WooCart\BetterTaxHandling\Rates;
use PHPUnit\Framework\TestCase;

class RatesTest extends TestCase {

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
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 */	 
	public function testConstructor() {
		$rates = new Rates();

		\WP_Mock::expectActionAdded( 'admin_init', [ $rates, 'init' ] );

        $rates->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::init
	 */
	public function testInit() {
		global $pagenow;

		$rates = new Rates();

		$pagenow 				= 'admin.php';
		$_REQUEST['tab'] 		= 'tax';
		$_REQUEST['page'] 		= 'wc-settings';
		$_REQUEST['section'] 	= 'not_empty';

		\WP_Mock::expectActionAdded( 'admin_footer', [ $rates, 'footer' ] );

        $rates->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_code
	 */
	public function testGetTaxCode() {
		$rates = new Rates();

		$this->assertEquals( 'IN', $rates->get_tax_code( 'IN' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionGR() {
		$rates = new Rates();

		$this->assertEquals( 'EL', $rates->get_tax_code( 'GR' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionGB() {
		$rates = new Rates();

		$this->assertEquals( 'UK', $rates->get_tax_code( 'GB' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionMC() {
		$rates = new Rates();

		$this->assertEquals( 'FR', $rates->get_tax_code( 'MC' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_iso_code
	 */
	public function testGetIsoCode() {
		$rates = new Rates();

		$this->assertEquals( 'IN', $rates->get_iso_code( 'IN' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_iso_code
	 */
	public function testGetIsoCodeExceptionUK() {
		$rates = new Rates();

		$this->assertEquals( 'GB', $rates->get_iso_code( 'UK' ) );
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Rates::get_iso_code
	 */
	public function testGetIsoCodeExceptionEL() {
		$rates = new Rates();

		$this->assertEquals( 'GR', $rates->get_iso_code( 'EL' ) );
	}

}
