( function( $ ) {
	'use strict';

	function trigger_change() {
		var option = $( '#b2b_sales' ).val();
		var parent = $( '#b2b_sales' ).closest( 'tbody' );

		if( 'none' == option ) {
			// Hide all but show only first :)
			parent.find( 'tr' ).hide();
			parent.find( 'tr:first-child' ).show();
		} else {
			// Show everything now!
			parent.find( 'tr' ).show();

			// Hide one option for Non-EU stores
			if( 'noneu' == option ) {
				parent.find( '#tax_eu_with_vatid' ).closest( 'tr' ).hide();
			}
		}
	}

	// Trigger on page load.
	$( document ).ready( function() {
		trigger_change();
	} );

	// Also to be triggered on value change!
	$( document ).on( 'change', '#b2b_sales', function() {
		trigger_change();
	} );

	/**
	 * AJAX function.
	 */
	function btp_ajax( req_type, trigger ) {
		var req_data = {
			action: 'add_' + req_type,
			nonce: btp_localize.nonce
		};

		if( req_type == 'distance_taxes' ) {
			req_data.countries = $( 'select[name="vat_distance_selling_countries[]"]' ).val();
		}

		if( req_type == 'tax_id_check' ) {
			req_data.business_id = trigger.attr( 'data-value' );
		}

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: req_data,
			beforeSend: function() {
				trigger.prop( 'disabled', true );
			}
		} ).done( function( data ) {
			// Unblock the button
			trigger.prop( 'disabled', false );

			if( data.success ) {
				if( req_type == 'tax_id_check' ) {
					// Success.
					$( '#btn-vat-response' ).css( {'color':'#2d882d' } ).html( data.data );
				} else {
					// Refreshes the options to the latest values.
					window.location.href = window.location.href;
				}
			} else {
				if( req_type == 'tax_id_check' ) {
					// Error.
					$( '#btn-vat-response' ).css( {'color':'#ff0000' } ).html( data.data );
				}
			}
		} );
	}

	/**
	 * Import taxes for digital goods & distance selling.
	 */
	$( '.import-digital-tax-rates' ).on( 'click', function( event ) {
		event.preventDefault();

		btp_ajax( 'digital_taxes', $( this ) );
	} );

	// Distance selling.
	$( '.import-distance-tax-rates' ).on( 'click', function( event ) {
		event.preventDefault();

		btp_ajax( 'distance_taxes', $( this ) );
	} );

	// Tax ID checker AJAX request.
	$( '#bth-vat-check' ).on( 'click', function( event ) {
		event.preventDefault();

		btp_ajax( 'tax_id_check', $( this ) );
	} );
} )( jQuery );
