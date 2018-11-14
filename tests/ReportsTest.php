<?php
/**
 * Tests the reports class.
 *
 * @package better-tax-handling
 */

use Niteo\WooCart\BetterTaxHandling\Reports;
use PHPUnit\Framework\TestCase;

class ReportsTest extends TestCase {

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
	 * @covers \Niteo\WooCart\BetterTaxHandling\Reports::__construct
	 */	 
	public function testConstructor() {
		$reports = new Reports();

		\WP_Mock::expectFilterAdded( 'woocommerce_admin_reports', [ $reports, 'tabs' ], 10, 1 );

        $reports->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\Reports::__construct
	 * @covers \Niteo\WooCart\BetterTaxHandling\Reports::tabs
	 */
	public function testTabs() {
		$reports = new Reports();

		$tabs['taxes']['reports'] = array(
			'taxes_by_country' => array(
				'title'         => esc_html__( 'Tax Collected By Country', 'better-tax-handling' ),
				'description'   => '',
				'hide_title'    => true,
				'callback'      => array( 'Niteo\WooCart\BetterTaxHandling\Reports', 'taxes_by_country' )
			)
		);

		$tabs['orders']['reports'] = array(
			'business_sales' => array(
				'title'         => esc_html__( 'B2B Transactions', 'better-tax-handling' ),
				'description'   => '',
				'hide_title'    => true,
				'callback'      => array( 'Niteo\WooCart\BetterTaxHandling\Reports', 'business_orders' )
			)
		);

		$this->assertEquals( $tabs, $reports->tabs( [ 'taxes' => [ 'reports' => [] ], 'orders' => [ 'reports' => [] ] ] ) );
	}

}