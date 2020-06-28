(function($) {
	'use strict';

	// AJAX request
	function euVatAjax(req_type, trigger) {
		var req_data = {
			action: 'add_' + req_type,
      business_id: trigger.data( 'taxid' ),
      order_id: trigger.data( 'orderid' ),
			nonce: wc_euvat_l10n.nonce
		};

		$.ajax(
			{
				type: 'POST',
				url: ajaxurl,
				data: req_data,
				beforeSend: function() {
					trigger.prop( 'disabled', true );
				}
			}
		).done(
			function(data) {
				// Unblock button
				trigger.prop( 'disabled', false );

				if (data.status == 'success') {
          $( '#wc-euvat-response' ).css( { 'color':'#2d882d' } ).html( data.data );
				} else {
          $( '#wc-euvat-response' ).css( { 'color':'#ff0000' } ).html( data.data );
				}
			}
		);
	}

	// Initialize on DOM ready
	$( document ).ready(
    function() {
      // Tax ID checker
      $( '#wc-euvat-check' ).on(
        'click',
        function(e) {
          e.preventDefault();
          euVatAjax( 'tax_id_check', $( this ) );
        }
      );
    }
  );
})( jQuery );
