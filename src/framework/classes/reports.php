<?php

namespace Niteo\WooCart\BetterTaxHandling {

	use Niteo\WooCart\BetterTaxHandling\Reports\Taxes_Report_By_Country;
	use Niteo\WooCart\BetterTaxHandling\Reports\Business_Transactions_Report;

	/**
	 * Reports class for our custom tax reports.
	 *
	 * @since 1.0.0
	 */
	class Reports {

		/**
		 * Class constructor. 
		 */
		public function __construct() {
			add_filter( 'woocommerce_admin_reports', array( &$this, 'tabs' ), 10, 1 );
		}

		/**
		 * Add our tabs to the tax reports section.
		 *
		 * @param array $reports List of all tabs added to the reports page.
		 * @return array
		 */
		public function tabs( $reports ) {
			$taxes = array(
				'taxes_by_country' => array(
					'title'         => esc_html__( 'Tax Collected By Country', 'better-tax-handling' ),
					'description'   => '',
					'hide_title'    => true,
					'callback'      => array( __CLASS__, 'taxes_by_country' )
				)
			);

			$orders = array(
				'business_sales' => array(
					'title'         => esc_html__( 'B2B Transactions', 'better-tax-handling' ),
					'description'   => '',
					'hide_title'    => true,
					'callback'      => array( __CLASS__, 'business_orders' )
				)
			);

			// Merging arrays to add our tabs to the tax reports section.
			$reports['taxes']['reports'] = array_merge( $reports['taxes']['reports'], $taxes );
			$reports['orders']['reports'] = array_merge( $reports['orders']['reports'], $orders );

			return $reports;
		}

		/**
		 * EU Tax collected by country.
		 */
		public static function taxes_by_country() {
			$report = new Taxes_Report_By_Country();
			$report->output_report();
		}

		/**
		 * B2B transaction orders.
		 */
		public static function business_orders() {
			$report = new Business_Transactions_Report();
			$report->output_report();
		}

	}

}
