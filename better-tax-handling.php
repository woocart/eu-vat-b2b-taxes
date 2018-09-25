<?php

namespace Niteo\Woocart\BetterTaxHandling;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name: Better Tax Handling
 * Description: Better Tax Handling is a plugin for WooCommerce stores that simplifies the complex part of taxation for B2B and B2C selling.
 * Version:     1.0.0
 * Runtime:     5.4+
 * Author:      WooCart
 * Text Domain: better-tax-handling
 * Domain Path: /langs/
 * Author URI:  www.woocart.com
 */

/**
 * BetterTaxHandling class where all the action happens.
 *
 * @package WordPress
 * @subpackage better-tax-handling
 * @since 1.0.0
 */
class BetterTaxHandling {

	/**
	 * @var string.
	 */
	protected $plugin_url;

	/**
	 * @var string.
	 */
	protected $version = '1.0.0';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Set plugin URL.
		$this->plugin_url = plugins_url( '', __FILE__ );

		// Settings fields.
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	/**
	 * User facing stuff.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'front_scripts' ) );
		add_filter( 'woocommerce_billing_fields', array( &$this, 'checkout_fields' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'calculate_taxes' ) );
	}

	/**
	 * Initialize on `admin_init` hook.
	 */
	public function admin_init() {
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'woocommerce_tax_settings', array( &$this, 'add_settings' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_reports', array( &$this, 'vat_report' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );
		}
	}

	/**
	 * Add custom settings to the `woocommerce` tax options page.
	 */
	public function add_settings( $settings ) {
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
			),
		);

		return array_merge( $settings, $this->form_fields );
    }

    /**
     * Calculate taxes.
     */
    public function calculate_taxes( $cart ) {
    	global $wpdb;

    	// Get cart items.
    	$items 		= WC()->cart->get_cart();

    	// Customer billing details.
    	$customer 	= WC()->customer->get_billing();

    	// Initial tax.
    	$tax 		= 0;
    	$tax_rate 	= 'TAX (0%)';

    	// Loop through each item and calculate tax.
    	foreach( $items as $item ) {
    		$price 		= $item['data']->get_price();
    		$tax_status = $item['data']->get_tax_status();
    		$tax_class 	= $item['data']->get_tax_class();

    		// Calculate tax.
    		if ( empty( $tax_class ) ) {
    			$query 	= $wpdb->prepare( "SELECT tax_rate, tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s", $customer['country'] );
    		} else {
	    		$query 	= $wpdb->prepare( "SELECT tax_rate, tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s AND tax_rate_class = %s", $customer['country'], $tax_class );
    		}

    		$result = $wpdb->get_results( $query, 'ARRAY_A' );

    		// Calculate tax.
    		if ( ! empty( $result ) ) {
    			$tax = $price * $result[0]['tax_rate'] / 100;

	    		// Add tax to the page.
	    		$tax_rate = $result[0]['tax_rate_name'];
    		} else {
    			$query 	= $wpdb->prepare( "SELECT tax_rate, tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s", $customer['country'] );

    			$result = $wpdb->get_results( $query, 'ARRAY_A' );

    			$tax = $price * $result[0]['tax_rate'] / 100;

	    		// Add tax to the page.
	    		$tax_rate = $result[0]['tax_rate_name'];
    		}

	    	$cart->add_fee( $tax_rate, $tax, false );
    	}
    }

    /**
     * Add required admin script for the tax page.
     */
    public function admin_scripts() {
    	wp_enqueue_script( 'better-tax-admin', $this->plugin_url . '/framework/js/admin.js', array( 'jquery' ), $this->version, true );
    }

    /**
     * Add styles and scripts for the frontend.
     */
    public function front_scripts() {
    	wp_enqueue_script( 'better-tax-public', $this->plugin_url . '/framework/js/public.js', array( 'jquery' ), $this->version, true );
    	wp_enqueue_style( 'better-tax-public', $this->plugin_url . '/framework/css/public.css', array(), $this->version );
    }

    /**
     * Add custom fields to the checkout page.
     */
    public function checkout_fields( $fields ) {
    	$b2b_sales = esc_html( get_option( 'b2b_sales' ) );

    	// Show conditionally :)
    	if ( 'eu' === $b2b_sales || 'noneu' === $b2b_sales ) {
	    	// Check for business status.
			$fields['business_check'] = array(
				'label' 	=> esc_html__( 'Are you making this purchase as a Business entity?', 'better-tax-handling' ),
				'type' 		=> 'checkbox',
				'required' 	=> false,
				'class' 	=> array( 'better-tax-business-check' ),
				'clear' 	=> true,
				'priority' 	=> 5
			);

	    	// Ask for VAT ID.
	    	$fields['business_tax_id'] = array(
				'label' 	=> esc_html__( 'Business Tax ID', 'better-tax-handling' ),
				'type' 		=> 'text',
				'required' 	=> true,
				'class' 	=> array( 'better-tax-vat-id better-tax-hidden' ),
				'clear' 	=> true,
				'priority' 	=> 10
			);
		}

    	return $fields;
    }

    /**
     * Reports for the plugin.
     */
    public function vat_report( $reports ) {
		if ( isset( $reports['taxes'] ) ) {
			$reports['taxes']['reports']['vat_report'] = array(
				'title'       => esc_html__( 'B2B Transactions', 'better-tax-handling' ),
				'description' => '',
				'hide_title'  => false,
				'callback'    => array( $this, 'tax_b2b_transactions' )
			);
		}

		return $reports;
	}

    /**
	 * Function used for debugging :)
	 */
	public function debug() {
		if ( class_exists( 'woocommerce' ) )  {
			$options 	= array();
			$settings 	= apply_filters( 'woocommerce_tax_settings', $options );

			print_r( $settings );
		}
	}

	/**
	 * Attached to the activation hook.
	 */
	public function activate_plugin() {}

	/**
	 * Attached to the de-activation hook.
	 */
	public function deactivate_plugin() {}

}

// Initialize Plugin.
$woocart_tax_handling = new BetterTaxHandling();

// Activation Hook.
register_activation_hook( __FILE__, array( &$woocart_tax_handling, 'activate_plugin' ) );

// Deactivation Hook.
register_deactivation_hook( __FILE__, array( &$woocart_tax_handling, 'deactivate_plugin' ) );
