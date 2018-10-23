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
} )( jQuery );