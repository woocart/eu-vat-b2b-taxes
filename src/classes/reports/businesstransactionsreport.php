<?php

namespace Niteo\WooCart\AdvancedTaxes\Reports {

	use \WC_Admin_Report;

	/**
	 * Reports class for our custom tax reports.
	 *
	 * @since 1.0.0
	 */
	class Business_Transactions_Report extends WC_Admin_Report {

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
				<?php esc_html_e( 'Export CSV', 'advanced-taxes-woocommerce' ); ?>
			</a>
			<?php
		}

		/**
		 * Output the report.
		 */
		public function output_report() {
			$ranges = array(
				'year'       => esc_html__( 'Year', 'advanced-taxes-woocommerce' ),
				'last_month' => esc_html__( 'Last month', 'advanced-taxes-woocommerce' ),
				'month'      => esc_html__( 'This month', 'advanced-taxes-woocommerce' ),
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
					'function' => '',
					'name'     => 'post_id',
				),
				'_billing_country' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'country',
				),
				'_order_total'     => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'order_total',
				),
				'business_tax_id'  => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'tax_id',
				),
				'post_date_gmt'    => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'order_date',
				),
			);

			$tax_row_orders = $this->get_order_report_data(
				array(
					'data'                => $query_data,
					'query_type'          => 'get_results',
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
						<th><strong><?php esc_html_e( 'Country', 'advanced-taxes-woocommerce' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Date', 'advanced-taxes-woocommerce' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Order Number', 'advanced-taxes-woocommerce' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Tax ID', 'advanced-taxes-woocommerce' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Amount Sales', 'advanced-taxes-woocommerce' ); ?></strong></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tax_row_orders as $tax_row ) { ?>
					<tr>
						<td><?php echo WC()->countries->countries[ $tax_row->country ]; ?> (<?php echo $tax_row->country; ?>)</td>
						<td><?php echo $tax_row->order_date; ?></td>
						<td><?php echo $tax_row->post_id; ?></td>
						<td><?php echo $tax_row->tax_id; ?></td>
						<td><?php echo wc_price( $tax_row->order_total ); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
		}

	}

}
