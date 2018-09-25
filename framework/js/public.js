( function( $ ) {
	'use strict';

	$( document ).on( 'change', '#business_check', function() {
		var business_check 	= $( '#business_check:checked' ).length > 0;
		var business_tax_id = $( '#business_tax_id' ).closest( 'p' );

		if( business_check ) {
			business_tax_id.removeClass( 'validate-required' ).addClass( 'validate-required' ).fadeIn();
		} else {
			business_tax_id.removeClass( 'validate-required' ).fadeOut();
		}
	} );
} )( jQuery );