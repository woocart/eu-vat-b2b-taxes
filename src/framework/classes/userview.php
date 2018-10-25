<?php

namespace Niteo\WooCart\BetterTaxHandling {

	/**
	 * User class where we calculate taxes and get stuff done.
	 *
	 * @since 1.0.0
	 */
	class UserView {

		/**
		 * Class constructor. 
		 */
		public function __construct() {
			add_action( 'init', array( &$this, 'init' ) );
		}

		/**
		 * Initialize the user facing part of the plugin.
		 */
		public function init() {
			add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
			add_filter( 'woocommerce_billing_fields', array( &$this, 'checkout_fields' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'calculate_tax' ) );
			add_action( 'woocommerce_after_checkout_validation', array( &$this, 'checkout_validation' ), PHP_INT_MAX, 2 );
		}

		/**
	     * Add styles and scripts for the frontend.
	     */
	    public function scripts() {
	    	wp_enqueue_script( 'better-tax-public', Plugin_Url . 'framework/js/public.js', array( 'jquery' ), Version, true );
	    	wp_enqueue_style( 'better-tax-public', Plugin_Url . 'framework/css/public.css', '', Version );
	    }

	    /**
		 * Add custom fields to the checkout page.
	     */
	    public function checkout_fields( $fields ) {
	    	$b2b_sales = esc_html( get_option( 'b2b_sales' ) );

	    	// Show conditionally :)
	    	if ( 'none' !== $b2b_sales ) {
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
					'required' 	=> false,
					'class' 	=> array( 'form-row-wide', 'better-tax-vat-id', 'better-tax-hidden', 'address-field' ),
					'priority' 	=> 6
				);
			}

	    	return $fields;
	    }

	    /**
		 * We are returning empty taxes to simplify our tax computation.
		 *
		 * @return array
		 */
		public function return_tax( $taxes, $price, $rates, $price_includes_tax = false, $deprecated = false ) {
			return array();
		}

	    /**
	     * Simplifying taxation for the user.
	     *
	     * @todo Live validation for the Tax ID
	     * @codeCoverageIgnore
	     */
	    public function calculate_tax( $cart ) {
	    	global $woocommerce, $wpdb;

	    	// Grab tax settings.
	    	$b2b_sales 			= esc_html( get_option( 'b2b_sales' ) );

	    	// We will continue only if B2B sales are not disabled.
	    	if ( 'none' !== $b2b_sales ) {
		    	$b2b_home_tax 		= esc_html( get_option( 'tax_home_country' ) );
		    	$b2b_eu_tax_vat_id 	= esc_html( get_option( 'tax_eu_with_vatid' ) );
		    	$b2b_tax_outside 	= esc_html( get_option( 'tax_charge_vat' ) );
		    	$base_location 		= wc_get_base_location()['country'];

		    	// Get cart items.
		    	$items 				= WC()->cart->get_cart();

		    	// Customer billing details.
		    	$customer 			= WC()->customer->get_billing();

		    	// Check if the business ID field is checked. If it is, then only we start the tax calculation.
				// Get rid of other taxes.
		    	add_filter( 'woocommerce_calc_tax', array( &$this, 'return_tax' ), PHP_INT_MAX, 5 );

		    	/**
		    	 * Playing around with various combinations and then calculating the tax (if required).
		    	 */

		    	// If the base country and the customer country is same, and also if the tax charge option is ticked off, then do nothing.
		    	if ( ( $base_location === $customer['country'] ) && 'no' === $b2b_home_tax ) {
		    		return;
		    	}

		    	// If the sale is not made in the base country, and the option to not charge tax is ticked.
		    	if ( ( $base_location !== $customer['country'] ) && 'yes' === $b2b_tax_outside ) {
		    		return;
		    	}

		    	// Initial tax.
		    	$tax 		= 0;
		    	$tax_rate 	= esc_html__( 'TAX', 'better-tax-handling' );

		    	// Loop through each item and calculate tax.
		    	foreach( $items as $item ) {
		    		$price 		= $item['data']->get_price();
		    		$tax_status = $item['data']->get_tax_status();
		    		$tax_class 	= $item['data']->get_tax_class();

		    		// For digital goods.
		    		if ( 'digital-goods-rate' === $tax_class ) {
		    			if ( 'no' === get_option( 'vat_digital_goods_enable' ) ) {
		    				continue;
		    			}
		    		}

		    		// For distance selling.
		    		if ( 'distance-selling-rate' === $tax_class ) {
		    			if ( 'no' === get_option( 'vat_distance_selling_enable' ) ) {
		    				continue;
		    			} else {
		    				// Fetch digital selling countries where the taxes will be levied by the shop.
		    				$ds_countries = esc_html( get_option( 'vat_distance_selling_countries' ) );

		    				// If the customer country is not in the list, then continue as we are not going to charge in that case.
		    				if ( ! in_array( $customer['country'], $ds_countries ) ) {
		    					continue;
		    				}
		    			}
		    		}

		    		// Calculate tax.
		    		if ( empty( $tax_class ) ) {
		    			$query 	= $wpdb->prepare( "SELECT tax_rate, tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s", $customer['country'] );
		    		} else {
			    		$query 	= $wpdb->prepare( "SELECT tax_rate, tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = %s AND tax_rate_class = %s", $customer['country'], $tax_class );
		    		}

		    		$result = $wpdb->get_results( $query, 'ARRAY_A' );

		    		// Calculate tax.
		    		if ( ! empty( $result ) ) {
		    			$tax 		= $price * $result[0]['tax_rate'] / 100;

			    		// Add tax to the page.
			    		$tax_rate 	= $result[0]['tax_rate_name'];

			    		// Add fees to cart.
				    	$cart->add_fee( $tax_rate, $tax, false );
		    		}
		    	}
		    }
	    }

	    /**
	     * Add custom validation for the business tax ID field which is set to optional but we enforce it as required if the purchase is being made as a business entity.
	     *
	     * @param  array $data An array of posted data.
		 * @param  WP_Error $errors
	     */
	    public function checkout_validation( $data, $errors ) {
	    	if ( isset( $_POST['business_check'] ) ) {
				if ( ! isset( $_POST['business_tax_id'] ) || empty( $_POST['business_tax_id'] ) ) {
					if ( 'yes' === get_option( 'tax_id_required' ) ) {
						$errors->add( 'billing', esc_html__( 'Business Tax ID is a required field.', 'better-tax-handling' ) );
	    			}
	    		}
	    	}
	    }

	}

}
