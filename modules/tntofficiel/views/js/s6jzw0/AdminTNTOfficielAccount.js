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

    jQuery('form#configuration_form')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
        // Get state.
        var boolPreventUnsavedChange = TNTOfficiel_hasInputChange(jQuery(this).find('[name^="TNTOFFICIEL_"]'));

        // Unbinding live to prevent browser forced behavior.
        jQuery(window)
        .off('beforeunload.' + window.TNTOfficiel.module.name + ' unload.' + window.TNTOfficiel.module.name);

        // Something to save, warn to prevent page change.
        if (boolPreventUnsavedChange) {
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

        var boolContextShop = jQuery('input:hidden[name="AdminConfigContextShop"]').length === 1;
        var strConfirmMessage = TNTOfficiel_getCodeTranslate('confirmApplyContext');

        // Warn about saving in a non shop context.
        if (!boolContextShop) {
            var boolConfirm = window.confirm(strConfirmMessage);
            if (!boolConfirm) {
                // Prevent default action.
                objEvent.preventDefault();

                return false;
            }
        }

        return true;
    });

    /*
     * Zipcode & City
     */

    jQuery('#TNTOFFICIEL_CODE_POSTAL')
    .off('keyup.' + window.TNTOfficiel.module.name + ' change.' + window.TNTOfficiel.module.name)
    .on('keyup.' + window.TNTOfficiel.module.name + ' change.' + window.TNTOfficiel.module.name, function (objEvent) {
        var $elmtZipCode = jQuery('#TNTOFFICIEL_CODE_POSTAL'),
            $elmtCities = jQuery('#TNTOFFICIEL_VILLE'),
            strInputZipCode = $elmtZipCode.val(),
            strInputCity = $elmtCities.val();

        // Perform a check if the zipCode is entered.
        if (strInputZipCode.length === 5) {
            // If zipCode change.
            if ($elmtCities.data('zipCode') !== strInputZipCode) {
                $elmtCities.data('zipCode', strInputZipCode);
                // Get the cities list matching the postcode.
                var objJqXHR = TNTOfficiel_AJAX({
                    "url": window.TNTOfficiel.link.back.module.selectPostcodeCities,
                    "method": 'POST',
                    "dataType": 'json',
                    "data": {
                        "zipcode": strInputZipCode,
                        "city": strInputCity
                    },
                    "async": false
                });

                objJqXHR
                .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                    // handle the response from the ajax request.
                    $elmtZipCode.val(objResponseJSON.strZipCode);
                    $elmtCities.data('zipCode', objResponseJSON.strZipCode);
                    $elmtCities.empty().prop('disabled', false);
                    jQuery.each(objResponseJSON.arrCitiesNameList, function (index, strCity) {
                        if (typeof strCity === 'string') {
                            $elmtCities.append(
                                jQuery('<option value="' + strCity + '" ' + (objResponseJSON.strCity === strCity ? 'selected="selected"' : '') + '>' + strCity + '</option>')
                            );
                        }
                    });
                });
            }
        }
        else {
            $elmtCities.data('zipCode', null);
            $elmtCities.empty().prop('disabled', true);
        }
    });

    /*
     * Type ramassage.
     */

    function displayByPassageType($elmtArgSelectType) {
        if ($elmtArgSelectType.val() == 'REGULAR') {
            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER').closest('.form-group').removeClass('hidden');
            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING').closest('.form-group').addClass('hidden');
            jQuery('#TNTOFFICIEL_AFFICHAGE_RAMASSAGE_on').prop('disabled', true);
            jQuery('#TNTOFFICIEL_AFFICHAGE_RAMASSAGE_off').prop('disabled', true);
            jQuery('#TNTOFFICIEL_AFFICHAGE_RAMASSAGE_off').prop('checked', true);
        } else {
            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER').closest('.form-group').addClass('hidden');
            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING').closest('.form-group').removeClass('hidden');
            jQuery('#TNTOFFICIEL_AFFICHAGE_RAMASSAGE_on').prop('disabled', false);
            jQuery('#TNTOFFICIEL_AFFICHAGE_RAMASSAGE_off').prop('disabled', false);
        }
    }

    // onLoad
    var $elmtSelectType = jQuery('#TNTOFFICIEL_TYPE_RAMASSAGE');
    displayByPassageType($elmtSelectType);

    $elmtSelectType
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
        displayByPassageType($elmtSelectType);
        if (jQuery(this).val() != 'REGULAR') {

            var html = '<option value="0">00</option>' +
                '<option value="1">01</option>' + '<option value="2">02</option>' +
                '<option value="3">03</option>' + '<option value="4">04</option>' +
                '<option value="5">05</option>' + '<option value="6">06</option>' +
                '<option value="7">07</option>';

            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE option:first').before(html);
            jQuery('#TNTOFFICIEL_HEURE_RAMASSAGE').append('<option value="23">23</option>');
        } else {
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='0']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='1']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='2']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='3']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='4']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='5']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='6']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='7']").remove();
            jQuery("#TNTOFFICIEL_HEURE_RAMASSAGE option[value='23']").remove();
        }
    });

    /*
     * Zones
     */

    // Re init using custom placeholder.
    jQuery('select[name="TNTOFFICIEL_ZONE_1[]"], select[name="TNTOFFICIEL_ZONE_2[]"]')
    .chosen('destroy')
    .chosen({
        "placeholder_text_multiple": ' ...',
        "width": '100%'
    });

    var $elmtSelectZone1 = jQuery('select[name="TNTOFFICIEL_ZONE_1[]"]');
    var $elmtSelectZone2 = jQuery('select[name="TNTOFFICIEL_ZONE_2[]"]');
    var arrZone1Values = $elmtSelectZone1.val();
    var arrZone2Values = $elmtSelectZone2.val();

    // Init excluding options in Zone2 from values in Zone1.
    if (arrZone1Values !== null) {
        jQuery.each(arrZone1Values, function (index, value) {
            if (TNTOfficiel_isType(value, 'string') === true) {
                $elmtSelectZone2.find('option[value="' + value + '"]').prop('disabled', true);
            }
        });
    }
    // Init excluding options in Zone1 from values in Zone2.
    if (arrZone2Values !== null) {
        jQuery.each(arrZone2Values, function (index, value) {
            if (TNTOfficiel_isType(value, 'string') === true) {
                $elmtSelectZone1.find('option[value="' + value + '"]').prop('disabled', true);
            }
        });
    }

    jQuery('select[name="TNTOFFICIEL_ZONE_1[]"], select[name="TNTOFFICIEL_ZONE_2[]"]')
    // Updating select for exclusion.
    .trigger('chosen:updated')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent, objSet) {
        var $elmtSelectZone1 = jQuery('select[name="TNTOFFICIEL_ZONE_1[]"]');
        var $elmtSelectZone2 = jQuery('select[name="TNTOFFICIEL_ZONE_2[]"]');

        // Set option exclusion.
        if (objSet.selected) {
            if (this === $elmtSelectZone1[0]) {
                $elmtSelectZone2.find('option[value="' + objSet.selected + '"]').prop('disabled', true);
            }
            if (this === $elmtSelectZone2[0]) {
                $elmtSelectZone1.find('option[value="' + objSet.selected + '"]').prop('disabled', true);
            }
        }

        // Set option inclusion.
        if (objSet.deselected) {
            if (this === $elmtSelectZone1[0]) {
                $elmtSelectZone2.find('option[value="' + objSet.deselected + '"]').prop('disabled', false);
            }
            if (this === $elmtSelectZone2[0]) {
                $elmtSelectZone1.find('option[value="' + objSet.deselected + '"]').prop('disabled', false);
            }
        }

        // Update select.
        $elmtSelectZone1
        .trigger('chosen:updated');

        $elmtSelectZone2
        .trigger('chosen:updated');
    });

    /*
     * Statut
     */

    jQuery('input:radio[name="TNTOFFICIEL_OS_PARCEL_CHECK_ENABLE"]')
    .off('change.' + window.TNTOfficiel.module.name)
    .on('change.' + window.TNTOfficiel.module.name, function (objEvent) {
        var boolDisabled = (jQuery(this).val() === '0');

        if (boolDisabled) {
            jQuery('#TNTOFFICIEL_OS_PARCEL_CHECK_RATE').prop('disabled', true);
            jQuery('#TNTOFFICIEL_OS_PARCEL_CHECK_RATE option:selected').text('');
        } else {
            jQuery('#TNTOFFICIEL_OS_PARCEL_CHECK_RATE').prop('disabled', false);
            jQuery('#TNTOFFICIEL_OS_PARCEL_CHECK_RATE option:selected')
            .text((jQuery('#TNTOFFICIEL_OS_PARCEL_CHECK_RATE option:selected').val() / (60 * 60)) + 'h');
        }
    });
    jQuery('input:radio[name="TNTOFFICIEL_OS_PARCEL_CHECK_ENABLE"]:checked')
    .trigger('change.' + window.TNTOfficiel.module.name);

});