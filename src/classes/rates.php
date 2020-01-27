<?php

namespace Niteo\WooCart\AdvancedTaxes {

	/**
	 * For fetching tax rates and adding them to the settings.
	 *
	 * @since 1.0.0
	 */
	class Rates {

		/**
		 * @var array
		 */
		private $rates = array();

		/**
		 * @var array
		 */
		private $known_rates;

		/**
		 * @var string
		 */
		private $which_rate = 'standard_rate';

		/**
		 * @var array
		 */
		private $sources = array(
			'https://euvatrates.com/rates.json',
		);

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( &$this, 'init' ) );
		}

		/**
		 * Initialize on `admin_init` hook.
		 */
		public function init() {
			global $pagenow;

			$this->known_rates = array(
				'standard_rate' => esc_html__( 'Standard Rate', 'better-tax-handling' ),
				'reduced_rate'  => esc_html__( 'Reduced Rate', 'better-tax-handling' ),
			);

			// Check for tax settings and tab.
			if ( 'admin.php' == $pagenow &&
				! empty( $_REQUEST['page'] ) &&
				( 'woocommerce_settings' == $_REQUEST['page'] || 'wc-settings' == $_REQUEST['page'] ) &&
				! empty( $_REQUEST['tab'] ) &&
				'tax' == $_REQUEST['tab'] &&
				! empty( $_REQUEST['section'] )
			) {
				$this->which_rate = 'standard_rate';

				// Hook to the admin footer.
				add_action( 'admin_footer', array( &$this, 'footer' ) );

				if ( 'reduced-rate' == $_REQUEST['section'] ) {
					$this->which_rate = 'reduced_rate';
				}
			}
		}

		/**
		 * All the action happens here.
		 *
		 * @todo Seperate JS from the code.
		 * @codeCoverageIgnore
		 */
		public function footer() {
			$get_rates        = $this->get_tax_rates();
			$rates            = ( is_array( $get_rates ) ) ? $get_rates : array();
			$rate_description = esc_html__( 'Add / Update EU Tax Rates', 'better-tax-handling' );
			?>
			<script type="text/javascript">
			( function( $ ) {
				$( document ).ready( function() {
					var rates = <?php echo json_encode( $rates ); ?>;

					function better_tax_add_row( iso_code, tax_rate, tax_label ) {
						// From WC_Settings_Tax::output_tax_rates (class-wc-settings-tax.php)
						var $taxrates_form = $( '.wc_tax_rates' );
						var $tbody = $taxrates_form.find( 'tbody' );

						// How many rows are there currently?
						var size = $tbody.find( 'tr' ).size();

						// Does a line for this country already exist? If so, we want to update that
						var possible_existing_lines = $tbody.find( 'tr' );
						var was_updated = false;

						$.each( possible_existing_lines, function( ind, line ) {
							var p_iso = $( line ).find( 'td.country input:first' ).val();
							if( '' == p_iso || p_iso != iso_code ) { return; }

							var p_state = $( line ).find( 'td.state input:first' ).val();
							var p_postcode = $( line ).find( 'td.postcode input:first' ).val();
							var p_city = $( line ).find( 'td.city input:first' ).val();

							if( p_iso == iso_code && ( typeof p_state == 'undefined' || p_state == '' ) && ( typeof p_postcode == 'undefined' || p_postcode == '' ) && ( typeof p_city == 'undefined' || p_city == '' ) ) {
								$( line ).find( 'td.rate input:first' ).val( tax_rate ).change();

								// Since the tax amount is in the label, update that too
								$( line ).find( 'td.name input:first' ).val( tax_label ).change();
								was_updated = true;
								return;
							}
						} );

						// If a row existed, and we updated it, then we're done.
						if( true == was_updated ) {
							return;
						}

						$taxrates_form.find( '.button.insert' ).click();
						var $new_row_parent = $tbody.find( 'tr[data-id^="new"] .country input[value=""]' ).first();
						var $new_row = $new_row_parent.parents( 'tr' ).first();

						$new_row.attr( 'country', iso_code );

						$new_row.find( '.rate input' ).val( tax_rate ).change();
						$new_row.find( '.name input' ).val( tax_label ).change();
						$new_row.find( '.country input' ).val( iso_code ).change();

						return false;
					}

					<?php
						$tax_info = esc_html__( 'Grab all the EU Tax rates at the click of a button.', 'better-tax-handling' );
					?>

					var known_rates = [ "<?php echo implode( '", "', array_keys( $this->known_rates ) ); ?>" ];
					var known_rate_descriptions = [ "<?php echo implode( '", "', array_values( $this->known_rates ) ); ?>" ];
					var $foot = $( 'table.wc_tax_rates tfoot a.remove_tax_rates' ).first();

					$foot.after( '<a href="#" id="better-tax-updaterates" class="button better-tax-updaterates"><?php echo esc_js( $rate_description ); ?></a>' );
					var rate_selector = '<select id="better-tax-whichrate">';

					for( i = 0; i < known_rates.length; i++ ) {
						rate_selector += '<option value="' + known_rates[i] + '">' + known_rate_descriptions[i] + '</option>';
					}

					rate_selector = rate_selector + '</select>';

					var tax_description = ' <?php esc_attr_e( 'Name:', 'better-tax-handling' ); ?> <input id="better-tax-whatdescription" title="<?php esc_attr_e( 'The description that will be used when using the button for mass adding/updating of EU rates', 'better-tax-handling' ); ?>" type="text" size="6" value="<?php esc_attr_e( 'Tax', 'better-tax-handling' ); ?>">';

					$foot.after( '<?php echo esc_js( __( 'Use rates:', 'better-tax-handling' ) ); ?> ' + rate_selector + tax_description );

					$( 'table.wc_tax_rates' ).first().before( '<p><em><?php echo $tax_info; ?></em></p>' );

					$( 'table.wc_tax_rates' ).on( 'click', '.better-tax-updaterates', function() {
						var which_rate = $( '#better-tax-whichrate' ).val();

						if( typeof which_rate == 'undefined' || '' == which_rate ) {
							which_rate = '<?php echo $this->which_rate; ?>';
						}

						$.each( rates, function( iso, country ) {
							var rate = country.standard_rate;
							if( which_rate == 'reduced_rate' ) {
								var reduced_rate = country.reduced_rate;
								if( typeof reduced_rate != 'boolean' ) { rate = reduced_rate; }
							}

							// VAT-compliant invoices must show the rate
							var name = $( '#better-tax-whatdescription' ).val() + ' (' + rate.toString() + '%)';
							better_tax_add_row( iso, rate.toString(), name )
						} );

						return false;
					} );
				} );
			} )( jQuery );
			</script>
			<?php
		}

		/**
		 * Convert from ISO 3166-1 country code to country VAT code.
		 * https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes
		 * http://ec.europa.eu/taxation_customs/resources/documents/taxation/vat/how_vat_works/rates/vat_rates_en.pdf
		 */
		public function get_tax_code( $country ) {
			$country_code = $country;

			// Deal with exceptions
			switch ( $country ) {
				case 'GR':
					$country_code = 'EL';
					break;
				case 'IM':
				case 'GB':
					$country_code = 'UK';
					break;
				case 'MC':
					$country_code = 'FR';
					break;
			}

			return $country_code;
		}

		/**
		 * Fetch ISO code.
		 */
		public function get_iso_code( $country ) {
			$iso_code = $country;

			// Deal with exceptions
			switch ( $country ) {
				case 'EL':
					$iso_code = 'GR';
					break;
				case 'UK':
					$iso_code = 'GB';
					break;
			}

			return $iso_code;
		}

		/**
		 * Takes an EU country code @see get_tax_code()
		 * Available rates: standard_rate, reduced_rate
		 */
		public function get_tax_rate_for_country( $country_code, $rate = 'standard_rate' ) {
			$rates = $this->get_tax_rates();

			if ( empty( $rates ) || ! is_array( $rates ) || ! isset( $rates[ $country_code ] ) ) {
				return false;
			}

			if ( ! isset( $rates[ $country_code ][ $rate ] ) ) {
				return false;
			}

			return $rates[ $country_code ][ $rate ];
		}

		/**
		 * Fetch tax rates from remote URL.
		 */
		public function fetch_remote_tax_rates() {
			$new_rates = false;

			foreach ( $this->sources as $url ) {
				$get = wp_remote_get(
					$url,
					array(
						'timeout' => 5,
					)
				);

				if ( is_wp_error( $get ) || ! is_array( $get ) ) {
					continue;
				}

				if ( ! isset( $get['response'] ) || ! isset( $get['response']['code'] ) ) {
					continue;
				}

				if ( $get['response']['code'] >= 300 || $get['response']['code'] < 200 || empty( $get['body'] ) ) {
					continue;
				}

				$rates = json_decode( $get['body'], true );

				if ( empty( $rates ) || ! isset( $rates['rates'] ) ) {
					continue;
				}

				$new_rates = $rates['rates'];
				break;
			}

			return $new_rates;
		}

		/**
		 * Get tax rates.
		 */
		public function get_tax_rates( $use_transient = true ) {
			if ( ! empty( $this->rates ) ) {
				return $this->rates;
			}

			$rates = ( $use_transient ) ? get_site_transient( 'tax_rates_byiso' ) : false;

			if ( is_array( $rates ) && ! empty( $rates ) ) {
				$new_rates = $rates;
			} else {
				$this->rates = false;
				$new_rates   = $this->fetch_remote_tax_rates();
			}

			// The array we return should use ISO country codes.
			if ( ! empty( $new_rates ) ) {
				$corrected_rates = array();

				foreach ( $new_rates as $country => $rate ) {
					$iso                     = $this->get_iso_code( $country );
					$corrected_rates[ $iso ] = $rate;
				}

				// Add - Monaco.
				if ( isset( $corrected_rates['FR'] ) ) {
					$corrected_rates['MC'] = $corrected_rates['FR'];
				}

				// Add - Isle of Man.
				if ( isset( $corrected_rates['GB'] ) ) {
					$corrected_rates['IM']            = $corrected_rates['GB'];
					$corrected_rates['IM']['country'] = esc_html__( 'Isle of Man', 'better-tax-handling' );
				}

				set_site_transient( 'tax_rates_byiso', $corrected_rates, 43200 );
				$this->rates = $corrected_rates;
			}

			return $this->rates;
		}

	}

}
