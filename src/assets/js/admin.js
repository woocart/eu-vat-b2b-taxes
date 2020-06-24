(function($) {
	'use strict';

	$(document).ready(function() {
		var rates,
				known_rates,
				known_rate_values,
				$foot,
				rate_selector,
				tax_description;
		
		rates = wc_euvat_l10n.tax_rates;

		function add_row(iso_code, tax_rate, tax_label) {
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

			$taxrates_form = $('.wc_tax_rates');
			$tbody = $taxrates_form.find('tbody');

			// Find no. of rows
			$size = $tbody.find('tr').size();

			// If a line for the country exists, update it
			possible_existing_lines = $tbody.find('tr');
			was_updated = false;

			$.each(possible_existing_lines, function(ind, line) {
				p_iso = $(line).find('td.country input:first').val();

				if (!p_iso || p_iso != iso_code) {
					return;
				}

				p_state = $(line).find('td.state input:first').val();
				p_postcode = $(line).find('td.postcode input:first').val();
				p_city = $(line).find('td.city input:first').val();

				if (p_iso == iso_code && (typeof p_state == 'undefined' || p_state == '') && (typeof p_postcode == 'undefined' || p_postcode == '') && (typeof p_city == 'undefined' || p_city == '')) {
					$(line).find('td.rate input:first').val(tax_rate).change();

					// Update tax amount in the label
					$(line).find('td.name input:first').val(tax_label).change();
					was_updated = true;
					return;
				}
			});

			// We are done if the row was updated
			if (was_updated) {
				return;
			}

			$taxrates_form.find('.button.insert').click();

			$new_row_parent = $tbody.find('tr[data-id^="new"] .country input[value=""]').first();
			$new_row = $new_row_parent.parents('tr').first();
			$new_row.attr('country', iso_code);
			$new_row.find('.rate input').val(tax_rate).change();
			$new_row.find('.name input').val(tax_label).change();
			$new_row.find('.country input').val(iso_code).change();

			return false;
		}

		known_rates = wc_euvat_l10n.known_rates_key.split(',');
		known_rate_values = wc_euvat_l10n.known_rates_values.split(',');
		$foot = $('table.wc_tax_rates tfoot a.remove_tax_rates').first();

		$foot.after('<a href="#" id="better-tax-updaterates" class="button better-tax-updaterates">' + wc_euvat_l10n.add_update_text + '</a>');
		rate_selector = '<select id="better-tax-whichrate">';

		for (var i = 0; i < known_rates.length; i++) {
			rate_selector += '<option value="' + known_rates[i].replace('"', '').trim() + '">' + known_rate_values[i].replace('"', '').trim() + '</option>';
		}

		rate_selector = rate_selector + '</select>';
		tax_description = '&nbsp;&nbsp;' + wc_euvat_l10n.name_text + '<input id="better-tax-whatdescription" title="' + wc_euvat_l10n.name_desc_text + '" type="text" size="6" value="' + wc_euvat_l10n.name_value_text + '">';

		$foot.after('&nbsp;&nbsp;' + wc_euvat_l10n.use_rate_text + ' ' + rate_selector + tax_description );

		$('table.wc_tax_rates').first().before('<p><em>' + wc_euvat_l10n.grab_tax_text + '</em></p>');
		$('table.wc_tax_rates').on('click', '.better-tax-updaterates', function() {
			var which_rate,
					rate,
					reduced_rate,
					name;

			which_rate = $('#better-tax-whichrate').val();

			if (typeof which_rate == 'undefined' || which_rate == '') {
				which_rate = wc_euvat_l10n.which_rate;
			}

			$.each(rates, function(iso, country) {
				rate = country.standard_rate;

				if (which_rate == 'reduced_rate') {
					reduced_rate = country.reduced_rate;

					if (typeof reduced_rate != 'boolean') {
						rate = reduced_rate;
					}
				}

				// VAT-compliant invoices must show the rate
				name = $('#better-tax-whatdescription').val() + ' (' + rate.toString() + '%)';
				add_row(iso, rate.toString(), name)
			});

			return false;
		});
	});
})(jQuery);
