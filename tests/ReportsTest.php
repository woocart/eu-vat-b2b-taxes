<?php
/**
 * Tests the reports class.
 *
 * @package eu-vat-b2b-taxes
 */

use Niteo\WooCart\EUVatTaxes\Reports;
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
	 * @covers \Niteo\WooCart\EUVatTaxes\Reports::__construct
	 */
	public function testConstructor() {
		$reports = new Reports();

		\WP_Mock::expectFilterAdded( 'woocommerce_admin_reports', array( $reports, 'tabs' ), 10, 1 );

		$reports->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Reports::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Reports::tabs
	 */
	public function testTabs() {
		$reports = new Reports();

		$tabs['taxes']['reports'] = array(
			'taxes_by_country' => array(
				'title'       => esc_html__( 'Tax Collected By Country', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'Niteo\WooCart\EUVatTaxes\Reports', 'taxes_by_country' ),
			),
		);

		$tabs['orders']['reports'] = array(
			'business_sales' => array(
				'title'       => esc_html__( 'B2B Transactions', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'Niteo\WooCart\EUVatTaxes\Reports', 'business_orders' ),
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

}
