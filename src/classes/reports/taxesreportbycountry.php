<?php

namespace Niteo\WooCart\EUVatTaxes\Reports {

	use \WC_Admin_Report;

	/**
	 * Reports class for our custom tax reports.
	 *
	 * @since 1.0.0
	 */
	class Taxes_Report_By_Country extends WC_Admin_Report {

		/**
		 * Get the legend for the main chart sidebar.
		 *
		 * @return array
		 */
		public function get_chart_legend() {
			return array();
		}

		/**
		 * Output an export link.
		 */
		public function get_export_button() {
			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'last_month';
			?>
			<a
				href="#"
				download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
				class="export_csv"
				data-export="table"
			>
				<?php esc_html_e( 'Export CSV', 'eu-vat-b2b-taxes' ); ?>
			</a>
			<?php
		}

		/**
		 * Output the report.
		 */
		public function output_report() {
			$ranges = array(
				'year'       => esc_html__( 'Year', 'eu-vat-b2b-taxes' ),
				'last_month' => esc_html__( 'Last month', 'eu-vat-b2b-taxes' ),
				'month'      => esc_html__( 'This month', 'eu-vat-b2b-taxes' ),
			);

			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'month';

			if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', '7day' ) ) ) {
				$current_range = 'month';
			}

			$this->check_current_range_nonce( $current_range );
			$this->calculate_current_range( $current_range );

			$hide_sidebar = true;

			include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
		}

		/**
		 * Get the main chart.
		 */
		public function get_main_chart() {
			global $wpdb;

			$query_data = array(
				'ID'               => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders',
					'distinct' => true,
				),
				'_billing_country' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'country',
				),
				'_order_total'     => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'order_total',
				),
				'_order_tax'       => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'tax_total',
				),
			);

			$tax_row_orders = $this->get_order_report_data(
				array(
					'data'                => $query_data,
					'query_type'          => 'get_results',
					'group_by'            => 'country',
					'filter_range'        => true,
					'order_types'         => array_merge( wc_get_order_types( 'sales-reports' ), array( 'shop_order_refund' ) ),
					'order_status'        => array( 'completed', 'processing', 'on-hold' ),
					'parent_order_status' => array( 'completed', 'processing', 'on-hold' ), // Partial refunds inside refunded orders should be ignored
				)
			);
			?>
			<table class="widefat">
				<thead>
					<tr>
						<th><strong><?php esc_html_e( 'Country', 'eu-vat-b2b-taxes' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Number Of Transactions', 'eu-vat-b2b-taxes' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Total Taxable Sales', 'eu-vat-b2b-taxes' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Total Tax Collected', 'eu-vat-b2b-taxes' ); ?></strong></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tax_row_orders as $tax_row ) { ?>
					<tr>
						<td><?php echo WC()->countries->countries[ $tax_row->country ]; ?> (<?php echo $tax_row->country; ?>)</td>
						<td><?php echo $tax_row->total_orders; ?></td>
						<td><?php echo wc_price( $tax_row->order_total ); ?></td>
						<td><?php echo wc_price( $tax_row->tax_total ); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
		}

	}

}
