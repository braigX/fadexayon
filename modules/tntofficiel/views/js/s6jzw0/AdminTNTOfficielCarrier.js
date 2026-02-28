/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

// On Ready.
window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push(function (jQuery) {

    /*
     * Form
     */

    // If not editing a carrier (list view).
    if (jQuery('#TNTOFFICIEL_ZONES_ENABLED_on').length === 0) {

        // Nothing to do.
        return;
    }

    var boolForcePreventUnsavedChange = false;
    jQuery('form#configuration_form')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent, objData) {
        // Get state.
        var boolPreventUnsavedChange = TNTOfficiel_hasInputChange(jQuery(this).find('[name^="TNTOFFICIEL_"]'));

        // Keep in memory deleted input unsaved change.
        if (objData && objData.delete && boolPreventUnsavedChange) {
            boolForcePreventUnsavedChange = true;
        }

        // Unbinding live to prevent browser forced behavior.
        jQuery(window)
        .off('beforeunload.' + window.TNTOfficiel.module.name + ' unload.' + window.TNTOfficiel.module.name);

        // Something to save, warn to prevent page change.
        if (boolForcePreventUnsavedChange || boolPreventUnsavedChange) {
            // Binding live.
            jQuery(window)
            .on('beforeunload.' + window.TNTOfficiel.module.name + ' unload.' + window.TNTOfficiel.module.name, function (objEvent) {
                // Chrome force the behavior (boolConfirm = false) and then ALWAYS display a confirm box on return :
                // Leave site ? Changes that you made may not be saved.
                var boolConfirm = window.confirm('Changes that you made may not be saved.');

                // Prevent bubbling.
                objEvent.stopPropagation();
                if (!boolConfirm) {
                    // Prevent default action.
                    objEvent.preventDefault();

                    return false;
                }

                return true;
            });
        }
    })
    .off('submit.' + window.TNTOfficiel.module.name)
    .on('submit.' + window.TNTOfficiel.module.name, function (objEvent) {
        // Prevent bubbling plus further handlers to execute.
        objEvent.stopImmediatePropagation();

        // Legitimate save, do not prevent (unbind).
        jQuery(window)
        .off('beforeunload.' + window.TNTOfficiel.module.name + ' unload.' + window.TNTOfficiel.module.name);

        return true;
    });

    // Switch to display or hide zone part.
    jQuery('input:radio[name="TNTOFFICIEL_ZONES_ENABLED"]')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
        var boolDisabled = (jQuery(this).val() === '0');
        if (boolDisabled) {
            jQuery('#tab-zone').addClass('hidden');
        } else {
            jQuery('#tab-zone').removeClass('hidden');
        }
    });
    jQuery('input:radio[name="TNTOFFICIEL_ZONES_ENABLED"]:checked')
    .trigger('change.' + window.TNTOfficiel.module.name);

    // Select fees by cart weight or price.
    jQuery('#tab-zone .tab-pane')
    .off('change.' + window.TNTOfficiel.module.name, '.TNTOFFICIEL_ZONES_TYPE')
    .on('change.' + window.TNTOfficiel.module.name, '.TNTOFFICIEL_ZONES_TYPE', function (objEvent) {
        var strSelectedValue = jQuery(this).val();
        var currentZone = jQuery(this).closest('.tab-pane');

        if (strSelectedValue == 'price') {
            currentZone.find('#field_price_sup').addClass('hidden');
            currentZone.find('#field_limit').addClass('hidden');
            currentZone.find('table tr:first th:first').html('Sera appliqué lorsque le prix TTC est < (€)');
            // display|hide the table(arrRangePriceList|arrRangeWeightList) by type
            currentZone.find('table #tab_price').removeClass('hidden');
            currentZone.find('table #tab_weight').addClass('hidden');
        } else {
            currentZone.find('#field_price_sup').removeClass('hidden');
            currentZone.find('#field_limit').removeClass('hidden');
            currentZone.find('table tr:first th:first').html('Sera appliqué lorsque le poids est =< (kg)');
            // display|hide the table(arrRangePriceList|arrRangeWeightList) by type
            currentZone.find('table #tab_weight').removeClass('hidden');
            currentZone.find('table #tab_price').addClass('hidden');
        }
    });

    // Add a row in list Price on Zone bloc
    jQuery('#tab-zone .tab-pane')
    .off('click.' + window.TNTOfficiel.module.name, '.add_row')
    .on('click.' + window.TNTOfficiel.module.name, '.add_row', function (objEvent) {
        var intMaxTR = 128;
        var intMaxEmptyTR = 10;

        var elmtCurrentTab = jQuery(this).closest('.tab-pane');
        var intTabKey = elmtCurrentTab.attr('id').replace(/^\S*?([0-9]*)$/gi, '$1')|0;
        var strTypeSelect = ((elmtCurrentTab.find('.TNTOFFICIEL_ZONES_TYPE').val() === 'weight') ? 'weight' : 'price');
        var strTypeFee = ((strTypeSelect === 'weight') ? 'arrRangeWeightListCol' : 'arrRangePriceListCol');
        var strInputColName1 = 'TNTOFFICIEL_ZONES_CONF[' + intTabKey + '][' + strTypeFee + '1][]';
        var strInputColName2 = 'TNTOFFICIEL_ZONES_CONF[' + intTabKey + '][' + strTypeFee + '2][]';

        var elmtInputColName1 = jQuery('input[name="' + strInputColName1 + '"]');
        if (elmtInputColName1.length >= intMaxTR) {
            return;
        }
        if (elmtInputColName1.filter(function () {return jQuery(this).val() === '';}).length >= intMaxEmptyTR) {
            return;
        }

        var elmtTBodyVisible = elmtCurrentTab.find('tbody[id="tab_' + strTypeSelect + '"]');
        var intLastTR = elmtTBodyVisible.find('tr:last').attr('id').replace(/^\S*?([0-9]*)$/gi, '$1')|0;

        elmtTBodyVisible.find('#addr' + intLastTR).html('\
<td>\
    <div class="col-sm-6 col-sm-offset-3">\
        <input type="text" name="' + strInputColName1 + '" class="form-control" value="" />\
    </div>\
</td><td>\
    <div class="col-sm-6 col-sm-offset-3">\
        <input type="text" name="' + strInputColName2 + '" class="form-control" value="" />\
    </div>\
</td><td>\
    <a class="delete_row pull-right btn btn-default"><i class="icon-minus"></i></a>\
</td>'
        );

        elmtTBodyVisible.append('<tr id="addr' + (intLastTR + 1) + '"></tr>');
    });

    // Delete a row.
    jQuery('#tab-zone .tab-pane')
    .off('click.' + window.TNTOfficiel.module.name, '.delete_row')
    .on('click.' + window.TNTOfficiel.module.name, '.delete_row', function (objEvent) {

        $inputToDel = jQuery(this).closest('tr').find('input');
        // Simulate data delete without removing elements to allow trigger.
        $inputToDel.val('');
        // Check something to save for deleted input.
        $inputToDel.trigger('change.' + window.TNTOfficiel.module.name, [{"delete":true}]);

        jQuery(this).closest('tr').remove();
    });

    jQuery('#tab-zone .tab-pane')
    .off('change.' + window.TNTOfficiel.module.name, 'input[name^="TNTOFFICIEL_ZONES_CONF"]')
    .on('change.' + window.TNTOfficiel.module.name, 'input[name^="TNTOFFICIEL_ZONES_CONF"]', function (objEvent) {
        var field = jQuery(this).attr('name').replace(/^TNTOFFICIEL_ZONES_CONF\[([0-9]+)\]\[([^\]]+)\]\S*$/gi, '$2');
        var obj = {
            'arrRangeWeightListCol1': 1,
            'arrRangeWeightListCol2': 6,
            'fltRangeWeightPricePerKg': 6,
            'fltRangeWeightLimitMax': 1,
            'arrRangePriceListCol1': 6,
            'arrRangePriceListCol2': 6,
            'fltHRAAdditionalCost': 6,
            'fltMarginPercent': 2
        };
        if (!(field in obj)) {
            return;
        }

        var nbrMin = 0;
        var strValRaw = jQuery(this).val();
        if (strValRaw === '') {
            jQuery(this).parent().removeClass('has-error');
            return;
        }
        // Helper.
        var strVal = TNTOfficiel_trim(strValRaw.replace(',','.'));
        var nbrVal = parseFloat(strVal);
        // Round.
        var strValFixed = nbrVal.toFixed(obj[field]);
        var nbrValFixed = parseFloat(strValFixed);
        // Error (NaN).
        if (strValFixed.replace(/[0-9\.]/gi, '').length > 0) {
            jQuery(this).parent().addClass('has-error');
            return;
        } else if (nbrValFixed < nbrMin) {
            jQuery(this).parent().addClass('has-error');
            return;
        } else {
            jQuery(this).parent().removeClass('has-error');
        }
        // Pretty.
        var strNbr = strValFixed.replace(/0+$/gi, '').replace(/\.$/gi, '');

        jQuery(this).val(strNbr);
    });

    // Display/Hide Cloning list RG-32.
    jQuery('input:radio[name="TNTOFFICIEL_ZONES_CLONING_ENABLED"]')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
        var boolDisabled = (jQuery(this).val() === '0');
        if (boolDisabled) {
            jQuery('#tab-cloning').addClass('hidden');
        } else {
            jQuery('#tab-cloning').removeClass('hidden');
        }
    });
    jQuery('input:radio[name="TNTOFFICIEL_ZONES_CLONING_ENABLED"]:checked')
    .trigger('change.' + window.TNTOfficiel.module.name);

    // Remove lines too much RG-26.
    jQuery('#configuration_form_submit_btn')
    .off('click.' + window.TNTOfficiel.module.name)
    .on('click.' + window.TNTOfficiel.module.name, function (objEvent) {
        jQuery(".tab-content tbody tr").each(function () {
            if (jQuery(this).find('td:first input').val() == '' && jQuery(this).find('td:eq(1) input').val() == '') {
                jQuery(this).remove();
            }
        });
    })
});