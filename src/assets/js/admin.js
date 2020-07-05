(function($) {
	'use strict';

	function triggerChange() {
		var option = $( '#wc_b2b_sales' ).val();
		var parent = $( '#wc_b2b_sales' ).closest( 'tbody' );

		if ('none' == option) {
			// Hide all but show only first :)
			parent.find( 'tr' ).hide();
			parent.find( 'tr:first-child' ).show();
		} else {
			// Show everything now!
			parent.find( 'tr' ).show();

			// Hide one option for Non-EU stores
			if ('noneu' == option) {
				parent.find( '#wc_tax_eu_with_vatid' ).closest( 'tr' ).hide();
			}
		}
	}

	// AJAX request
	function euVatAjax(req_type, trigger) {
		var req_data = {
			action: 'add_' + req_type,
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

				// Refresh on a success response
				if (data.status == 'success') {
					// Refreshes options to latest values
					window.location.href = window.location.href;
				}
			}
		);
	}

	function addRow(iso_code, tax_rate, tax_label) {
		var $taxrates_form,
				$tbody,
				$size,
				possible_existing_lines,
				was_updated,
				p_iso,
				p_state,
				p_postcode,
				p_city,
				$new_row_parent,
				$new_row;

		$taxrates_form = $( '.wc_tax_rates' );
		$tbody         = $taxrates_form.find( 'tbody' );

		// Find no. of rows
		$size = $tbody.find( 'tr' ).size();

		// If a line for the country exists, update it
		possible_existing_lines = $tbody.find( 'tr' );
		was_updated             = false;

		$.each(
			possible_existing_lines,
			function(ind, line) {
				p_iso = $( line ).find( 'td.country input:first' ).val();

				if ( ! p_iso || p_iso != iso_code) {
					return;
				}

				p_state    = $( line ).find( 'td.state input:first' ).val();
				p_postcode = $( line ).find( 'td.postcode input:first' ).val();
				p_city     = $( line ).find( 'td.city input:first' ).val();

				if (p_iso == iso_code && (typeof p_state == 'undefined' || p_state == '') && (typeof p_postcode == 'undefined' || p_postcode == '') && (typeof p_city == 'undefined' || p_city == '')) {
					$( line ).find( 'td.rate input:first' ).val( tax_rate ).change();

					// Update tax amount in the label
					$( line ).find( 'td.name input:first' ).val( tax_label ).change();
					was_updated = true;
					return;
				}
			}
		);

		// We are done if the row was updated
		if (was_updated) {
			return;
		}

		$taxrates_form.find( '.button.insert' ).click();

		$new_row_parent = $tbody.find( 'tr[data-id^="new"] .country input[value=""]' ).first();
		$new_row        = $new_row_parent.parents( 'tr' ).first();
		$new_row.attr( 'country', iso_code );
		$new_row.find( '.rate input' ).val( tax_rate ).change();
		$new_row.find( '.name input' ).val( tax_label ).change();
		$new_row.find( '.country input' ).val( iso_code ).change();

		return false;
	}

	// Initialize on DOM ready
	$( document ).ready(
		function() {
			triggerChange();

			// For tax-rates management section
			var rates,
				known_rates,
				known_rate_values,
				$foot,
				rate_selector,
				tax_description;

			rates             = wc_euvat_l10n.tax_rates;
			known_rates       = wc_euvat_l10n.known_rates_key.split( ',' );
			known_rate_values = wc_euvat_l10n.known_rates_values.split( ',' );
			$foot             = $( 'table.wc_tax_rates tfoot a.remove_tax_rates' ).first();

			$foot.after( '<a href="#" id="wc-euvat-updaterates" class="button wc-euvat-updaterates">' + wc_euvat_l10n.add_update_text + '</a>' );
			rate_selector = '<select id="wc-euvat-rate">';

			for (var i = 0; i < known_rates.length; i++) {
				rate_selector += '<option value="' + known_rates[i].replace( '"', '' ).trim() + '">' + known_rate_values[i].replace( '"', '' ).trim() + '</option>';
			}

			rate_selector   = rate_selector + '</select>';
			tax_description = '&nbsp;&nbsp;' + wc_euvat_l10n.name_text + '<input id="wc-euvat-description" title="' + wc_euvat_l10n.name_desc_text + '" type="text" size="6" value="' + wc_euvat_l10n.name_value_text + '">';

			$foot.after( '&nbsp;&nbsp;' + wc_euvat_l10n.use_rate_text + ' ' + rate_selector + tax_description );

			$( 'table.wc_tax_rates' ).first().before( '<p><em>' + wc_euvat_l10n.grab_tax_text + '</em></p>' );
			$( 'table.wc_tax_rates' ).on(
				'click',
				'.wc-euvat-updaterates',
				function(e) {
					e.preventDefault();

					var which_rate,
						rate,
						reduced_rate,
						name;

					which_rate = $( '#wc-euvat-rate' ).val();

					if (typeof which_rate == 'undefined' || which_rate == '') {
						which_rate = wc_euvat_l10n.which_rate;
					}

					// Disable button
					$( this  ).addClass( 'disabled' );

					$.each(
						rates,
						function(iso, country) {
							rate = country.standard_rate;

							if (which_rate == 'reduced_rate') {
								reduced_rate = country.reduced_rate;

								if (typeof reduced_rate != 'boolean') {
									rate = reduced_rate;
								}
							}

							// VAT-compliant invoices must show the rate
							name = $( '#wc-euvat-description' ).val() + ' (' + rate.toString() + '%)';
							addRow( iso, rate.toString(), name )
						}
					);

					// Unblock button
					$( this  ).removeClass( 'disabled' );

					return false;
				}
			);
		}
	);

	// On-change DOM
	$( document ).on(
		'change',
		'#wc_b2b_sales',
		function() {
			triggerChange();
		}
	);

	// Import taxes for Digital Goods
	$( '.import-digital-tax-rates' ).on(
		'click',
		function(e) {
			e.preventDefault();
			euVatAjax( 'digital_taxes', $( this ) );
		}
	);
})( jQuery );
