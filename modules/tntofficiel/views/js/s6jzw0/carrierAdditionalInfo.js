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

    // If not an order with TNT carrier.
    if (window.TNTOfficiel.link.back && !window.TNTOfficiel.order.isTNT) {
        return;
    }

    var strSelectorReceiverInputItems = [
        '#extra_address_data #receiver_email',
        '#extra_address_data #receiver_phone',
        '#extra_address_data #receiver_building',
        '#extra_address_data #receiver_accesscode',
        '#extra_address_data #receiver_floor',
        '#extra_address_data #receiver_instructions'
    ].join(',');

    // Clear on each display.
    window.clearInterval(window.TNTOfficiel_hdlInterval);
    window.TNTOfficiel_hdlInterval = window.setInterval(function () {
        // Get state.
        var boolTextChanged = TNTOfficiel_hasInputChange(strSelectorReceiverInputItems);

        // If form validated or modified.
        if ((!window.TNTOfficiel.link.back && !TNTOfficiel_isExtraDataValidated()) || boolTextChanged) {
            // Display Validate button.
            jQuery('#submitAddressExtraData').fadeIn(125);
        } else {
            // Hide Validate button.
            jQuery('#submitAddressExtraData').fadeOut(65);
        }
    }, 125);

    // Perform an AJAX request when the extra address data form is submitted.
    // Submit the address extra data form in AJAX.
    jQuery(window.document)
    .off('click.' + window.TNTOfficiel.module.name, '#submitAddressExtraData')
    .on('click.' + window.TNTOfficiel.module.name, '#submitAddressExtraData', function (objEvent) {

        var objLink = window.TNTOfficiel.link.front;
        var objData = {
            "receiver_email": jQuery('#receiver_email').val(),
            "receiver_phone": jQuery('#receiver_phone').val(),
            "receiver_building": jQuery('#receiver_building').val(),
            "receiver_accesscode": jQuery('#receiver_accesscode').val(),
            "receiver_floor": jQuery('#receiver_floor').val(),
            "receiver_instructions": jQuery('#receiver_instructions').val()
        };

        if (window.TNTOfficiel.link.back) {
            objLink = window.TNTOfficiel.link.back;
            objData['id_order'] = window.TNTOfficiel.order.intOrderID;
        }

        var objJqXHR = TNTOfficiel_AJAX({
            "url": objLink.module.storeReceiverInfo,
            "method": 'POST',
            "data": objData,
            "dataType": 'json',
            "async": true
        });

        objJqXHR
        .done(function (objResponseJSON, strTextStatus, objJqXHR) {

            // Update HTML data for AJAX for modified state.
            jQuery(strSelectorReceiverInputItems)
            .each(function (intIndex, element) {
                element.setAttribute('value', jQuery(element).val());
            });

            jQuery('#extra_address_data .alert-danger').remove();
            jQuery.each(objResponseJSON.fields, function (strFieldName, strFieldValue) {

                // If modification during request send/receive.
                if (objData[strFieldName] !== jQuery('#extra_address_data #' + strFieldName).val()) {
                    return;
                }
                // If returned value diff from original value.
                if (objData[strFieldName] !== strFieldValue) {
                    // Update the field.
                    jQuery('#extra_address_data #' + strFieldName).val(strFieldValue);
                }
                var strErrorMessage = objResponseJSON.errors[strFieldName];
                if (strErrorMessage) {
                    jQuery('#extra_address_data .alert-danger.error-' + strFieldName).remove();
                    jQuery('#extra_address_data .info-' + strFieldName).hide();
                    jQuery('#extra_address_data #' + strFieldName).after(
                        jQuery('<small class="form-text alert-danger error-' + strFieldName + '"></div>')
                        .html(strErrorMessage)
                    );
                    jQuery('#extra_address_data #' + strFieldName)
                    .parent('.form-group')
                    .removeClass('form-ok')
                    .addClass('form-error');
                } else {
                    jQuery('#extra_address_data #' + strFieldName)
                    .parent('.form-group')
                    .removeClass('form-error')
                    .addClass('form-ok');
                }
            });

            // Flag validated. Default.
            var boolExtraDataValidated = false;

            // If there is no error.
            if (objResponseJSON.length === 0) {
                // If data not stored.
                if (!objResponseJSON.stored) {
                    window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                    return false;
                }

                // Flag validated. Allow payment usage.
                boolExtraDataValidated = true;

                if (window.TNTOfficiel.link.back) {
                    TNTOfficiel_Reload();
                }
            }

            if (!window.TNTOfficiel.link.back) {
                TNTOfficiel_setExtraDataValidated(boolExtraDataValidated);
            }
        })
        .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
            TNTOfficiel_Reload();
        });

    });

    if (!window.TNTOfficiel.link.back) {
        // unset form field class.
        jQuery(window.document)
        .off('change.' + window.TNTOfficiel.module.name, strSelectorReceiverInputItems)
        .on('change.' + window.TNTOfficiel.module.name, strSelectorReceiverInputItems, function (objEvent) {
            jQuery(this).parent('.form-group').removeClass('form-error').removeClass('form-ok');
        });
    }

});