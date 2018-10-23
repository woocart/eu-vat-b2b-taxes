<?php

namespace Niteo\WooCart\BetterTaxHandling {

	/**
	 * Class for all the WP admin panel magic.
	 *
	 * @since 1.0.0
	 */
	class Admin {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			// Initialize the admin part.
			add_action( 'admin_init', array( &$this, 'init' ) );
		}

		/**
		 * Initialize on `admin_init` hook.
		 */
		public function init() {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_action( 'woocommerce_tax_settings', array( &$this, 'settings' ), PHP_INT_MAX, 2 );
				add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );
			}
		}

		/**
		 * Add custom settings to the `woocommerce` tax options page.
		 */
		public function settings( $settings ) {
			$this->form_fields = array(
				array(
					'id' 		=> 'vatoptions',
					'name' 		=> esc_html__( 'Tax Handling for B2B', 'better-tax-handling' ),
					'type' 		=> 'title',
					'desc' 		=> esc_html__( 'Customize settings if you sell to companies. Defaults are ticked checkboxes.', 'better-tax-handling' )
				),
				array(
					'id' 		=> 'b2b_sales',
					'name' 		=> esc_html__( 'B2B sales (adds fields Company Name and Tax ID)', 'better-tax-handling' ),
					'type' 		=> 'select',
					'options' 	=> array(
						'none' 		=> esc_html__( 'disabled' ),
						'eu' 		=> esc_html__( 'EU store' ),
						'noneu' 	=> esc_html__( 'Non-EU store' )
					),
					'default' 	=> 'none'
				),
				array(
					'id' 		=> 'tax_id_required',
					'name' 		=> esc_html__( 'Tax ID field required for B2B', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Tax ID required', 'better-tax-handling' ),
					'default' 	=> 'yes'
				),
				array(
					'id' 		=> 'tax_home_country',
					'name' 		=> esc_html__( 'B2B sales in the home country', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Charge Tax', 'better-tax-handling' ),
					'default' 	=> 'yes'
				),
				array(
					'id' 		=> 'tax_eu_with_vatid',
					'name' 		=> esc_html__( 'B2B sales in the EU when valid VIES/VAT ID', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Do not charge Tax', 'better-tax-handling' ),
					'default' 	=> 'yes'
				),
				array(
					'id' 		=> 'tax_charge_vat',
					'name' 		=> esc_html__( 'B2B sales outside the country', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Do not charge Tax', 'better-tax-handling' ),
					'default' 	=> 'yes'
				),
				array(
					'type' 		=> 'sectionend',
					'id' 		=> 'vatoptions'
				),
				array(
					'id' 		=> 'vat_digital_goods',
					'name' 		=> esc_html__( 'EU Tax Handling - Digital Goods (B2C)', 'better-tax-handling' ),
					'type' 		=> 'title',
					'desc' 		=> esc_html__( 'If you sell digital goods in/to EU, you need to charge the customer\'s country Tax. Automatically validates the customer IP against their billing address, and prompts the customer to self-declare their address if they do not match. Applies only to digital goods and services sold to consumers (B2C).', 'better-tax-handling' )
				),
				array(
					'id' 		=> 'vat_digital_goods_enable',
					'name' 		=> esc_html__( 'EU Tax Handling for Digital Goods', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Enable', 'better-tax-handling' ),
					'default' 	=> 'no'
				),
				array(
					'id' 		=> 'vat_digital_goods_classes',
					'name' 		=> esc_html__( 'Tax Classes for Digital Goods', 'better-tax-handling' ),
					'type' 		=> 'text' 
				),
				array(
					'type' 		=> 'sectionend',
					'id' 		=> 'vat_digital_goods'
				),
				array(
					'id' 		=> 'vat_distance_selling',
					'name' 		=> esc_html__( 'EU Tax Handling - Distance Selling (B2C)', 'better-tax-handling' ),
					'type' 		=> 'title',
					'desc' 		=> sprintf( esc_html__( 'You need to register for EU Tax ID in countries where you reach %sDistance Selling EU Tax thresholds%s. Add countries where you are registered and the customers will be charged the local VAT. Applies only to products sold to consumers (B2C).', 'better-tax-handling' ), '<a href="https://www.vatlive.com/eu-vat-rules/distance-selling/distance-selling-eu-vat-thresholds/" target="_blank">', '</a>' )
				),
				array(
					'id' 		=> 'vat_distance_selling_enable',
					'name' 		=> esc_html__( 'EU VAT Handling for Distance Selling', 'better-tax-handling' ),
					'type' 		=> 'checkbox',
					'desc' 		=> esc_html__( 'Enable', 'better-tax-handling' ),
					'default' 	=> 'no'
				),
				array(
					'id' 		=> 'vat_distance_selling_countries',
					'name' 		=> esc_html__( 'Countries to charge local VAT', 'better-tax-handling' ),
					'type' 		=> 'text' 
				),
				array(
					'type' 		=> 'sectionend',
					'id' 		=> 'vat_distance_selling'
				)
			);

			return array_merge( $settings, $this->form_fields );
		}

	    /**
	     * Add required admin script for the tax page.
	     */
	    public function scripts() {
	    	wp_enqueue_script( 'better-tax-admin', BetterTaxHandling::$plugin_url . 'framework/js/admin.js', array( 'jquery' ), BetterTaxHandling::$version, true );
	    }

	}

}
