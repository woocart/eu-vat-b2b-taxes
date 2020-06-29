<?php
/**
 * Custom WC reports.
 *
 * @category   Plugins
 * @package    WordPress
 * @subpackage eu-vat-b2b-taxes
 * @since      1.0.0
 */

namespace Niteo\WooCart\EUVatTaxes {

	use Niteo\WooCart\EUVatTaxes\Reports\Taxes_Report_By_Country;
	use Niteo\WooCart\EUVatTaxes\Reports\Business_Transactions_Report;

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
			$reports['taxes']['reports']['taxes_by_country'] = array(
				'title'       => esc_html__( 'Tax Collected By Country', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'taxes_by_country' ),
			);

			$reports['orders']['reports']['business_sales'] = array(
				'title'       => esc_html__( 'B2B Transactions', 'eu-vat-b2b-taxes' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'business_orders' ),
			);

			return $reports;
		}

		/**
		 * EU Tax collected by country.
		 */
		public function taxes_by_country() {
			$report = $this->taxes_report();
			$report->output_report();
		}

		/**
		 * B2B transaction orders.
		 */
		public function business_orders() {
			$report = $this->business_transactions();
			$report->output_report();
		}

		/**
		 * Returns Taxes_Report_By_Country class
		 */
		public function taxes_report() {
			return new Taxes_Report_By_Country();
		}

		/**
		 * Returns Taxes_Report_By_Country class
		 */
		public function business_transactions() {
			return new Business_Transactions_Report();
		}

	}

}
