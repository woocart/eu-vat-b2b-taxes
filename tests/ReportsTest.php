<?php
/**
 * Tests the reports class.
 *
 * @package eu-vat-b2b-taxes
 */

namespace Niteo\WooCart\EUVatTaxes;

use Niteo\WooCart\EUVatTaxes\Reports;
use PHPUnit\Framework\TestCase;

/**
 * Tests Reports class functions in isolation.
 *
 * @package Niteo\WooCart\EUVatTaxes
 * @coversDefaultClass \Niteo\WooCart\EUVatTaxes\Reports
 */
class ReportsTest extends TestCase {

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
		$reports = new Reports();

		\WP_Mock::expectFilterAdded( 'woocommerce_admin_reports', array( $reports, 'tabs' ), 10, 1 );

		$reports->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::tabs
	 */
	public function testTabs() {
		$reports = new Reports();

		$tabs['taxes']['reports'] = array(
			'taxes_by_country' => array(
				'title'       => esc_html__( 'Tax Collected By Country', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $reports, 'taxes_by_country' ),
			),
		);

		$tabs['orders']['reports'] = array(
			'business_sales' => array(
				'title'       => esc_html__( 'B2B Transactions', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $reports, 'business_orders' ),
			),
		);

		$this->assertEquals(
			$tabs,
			$reports->tabs(
				array(
					'taxes'  => array( 'reports' => array() ),
					'orders' => array( 'reports' => array() ),
				)
			)
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::taxes_by_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Reports\TaxesReportByCountry::output_report
	 */
	public function testTaxesByCountry() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Reports' )->makePartial();

		$reports = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\TaxesReportByCountry' );
		$reports->shouldReceive( 'output_report' )->andReturn( true );

		$mock->shouldReceive( 'taxes_report' )->andReturn( $reports );
		$mock->taxes_by_country();
	}

	/**
	 * @covers ::__construct
	 * @covers ::business_orders
	 * @covers \Niteo\WooCart\EUVatTaxes\Reports\BusinessTransactionsReport::output_report
	 */
	public function testBusinessOrders() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Reports' )->makePartial();

		$reports = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\BusinessTransactionsReport' );
		$reports->shouldReceive( 'output_report' )->andReturn( true );

		$mock->shouldReceive( 'business_transactions' )->andReturn( $reports );
		$mock->business_orders();
	}

	/**
	 * @covers ::__construct
	 * @covers ::taxes_report
	 */
	public function testTaxesReport() {
		$reports = new Reports();

		$this->assertInstanceOf(
			'\Niteo\WooCart\EUVatTaxes\Reports\TaxesReportByCountry',
			$reports->taxes_report()
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::business_transactions
	 */
	public function testBusinessTransactions() {
		$reports = new Reports();

		$this->assertInstanceOf(
			'\Niteo\WooCart\EUVatTaxes\Reports\BusinessTransactionsReport',
			$reports->business_transactions()
		);
	}

}
