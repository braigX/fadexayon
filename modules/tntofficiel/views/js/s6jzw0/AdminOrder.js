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
    if (!window.TNTOfficiel.order.isTNT) {
        return;
    }

    var $elmtOrderPanel = jQuery('#tabOrder').closest('.panel');
    var $elmtCustomerPanel = jQuery('#tabAddresses').closest('.panel');
    var $elmtTNTOfficielPanel = jQuery('#TNTOfficelAdminOrdersViewOrder');

    var $elmtTNTOfficielOrderWellButton = $elmtTNTOfficielPanel.find('#TNTOfficielOrderWellButton');

    var $elmtTNTOfficielCustomerAdressShippingTabPane = jQuery('#TNTOfficielOrderReceiverInfo');
    if ($elmtTNTOfficielCustomerAdressShippingTabPane.length !== 1) {
        $elmtTNTOfficielCustomerAdressShippingTabPane = $elmtCustomerPanel.find('#addressShipping');
    }

    /**
     * Button (BT,Tracking)
     */

    var $elmtOrderPanelFirstWell = $elmtOrderPanel.children('.well');
    // Move them to upper.
    if ($elmtOrderPanelFirstWell.length === 1
        && $elmtTNTOfficielOrderWellButton.length === 1
    ) {
        $elmtTNTOfficielOrderWellButton.removeClass().css('margin', '8px 0 0');
        $elmtOrderPanelFirstWell.append($elmtTNTOfficielOrderWellButton);
    }


    // Disable delivery address Modification for DROPOFFPOINT or DEPOT.
    if (window.TNTOfficiel.order.isCarrierDeliveryPoint) {
        jQuery('#addressShipping form :input').attr('disabled', true);
        jQuery('#addressShipping a').css('cursor', 'not-allowed');
        jQuery('#addressShipping a')
        .off('click.' + window.TNTOfficiel.module.name)
        .on('click.' + window.TNTOfficiel.module.name, function (objEvent) {
            // Prevent default action.
            objEvent.preventDefault();

            return false;
        });

        jQuery('#addressShipping [name="submitAddressShipping"]').closest('form').hide();
        jQuery('#addressShipping .well').first().hide();
        jQuery('#map-delivery-point-canvas').replaceWith(jQuery('#map-delivery-canvas'));
    }


    /**
     * Delivery Point.
     */

    var $elmtTNTOfficielS2 = $elmtTNTOfficielPanel.find('#TNTOfficielSection2');
    if ($elmtTNTOfficielCustomerAdressShippingTabPane.length === 1
        && $elmtTNTOfficielS2.length === 1
    ) {
        $elmtTNTOfficielCustomerAdressShippingTabPane.append($elmtTNTOfficielS2.html());
        $elmtTNTOfficielS2.remove();
    }


    // Click on DROPOFFPOINT or DEPOT address displayed in delivery option.
    jQuery(window.document)
    .off('click.' + window.TNTOfficiel.module.name, '.tntofficiel-shipping-method-info-select')
    .on('click.' + window.TNTOfficiel.module.name, '.tntofficiel-shipping-method-info-select', function (objEvent) {
        // Prevent bubbling plus further handlers to execute.
        objEvent.stopImmediatePropagation();
        // Prevent default action.
        objEvent.preventDefault();

        TNTOfficiel_XHRBoxDeliveryPoints(window.TNTOfficiel.order.intCarrierID);

        return false;
    });


    /**
     * Carrier Additional Information.
     */

    var $elmtTNTOfficielTAI = $elmtTNTOfficielPanel.find('#TNTOfficielSection3');
    if ($elmtTNTOfficielCustomerAdressShippingTabPane.length === 1
        && $elmtTNTOfficielTAI.length === 1
    ) {
        $elmtTNTOfficielCustomerAdressShippingTabPane.append($elmtTNTOfficielTAI.html());
        $elmtTNTOfficielTAI.remove();
    }


    /**
     * Parcel
     */

    jQuery(window.document)
    .off('change.' + window.TNTOfficiel.module.name, '#formAdminParcelsPanel input')
    .on('change.' + window.TNTOfficiel.module.name, '#formAdminParcelsPanel input', function (objEvent) {
        var strName = this.id;

        if (! /^parcel(Weight|InsuranceAmount)-/gi.test(strName)) {
            return true;
        }

        // Default : input[id*="parcelInsuranceAmount-"]
        var nbrMin = 0.0;
        var intFixed = 2;
        if (/^parcelWeight-/gi.test(strName)) {
            nbrMin = 0.1;
            intFixed = 1;
        }

        var strValRaw = jQuery(this).val();
        // Separator helper.
        var strVal = TNTOfficiel_trim(strValRaw.replace(',','.'));
        // NaN if NaN, Empty string, etc.
        var nbrVal = parseFloat(strVal);
        // Apply min value.
        if (nbrVal < nbrMin) {
            nbrVal = nbrMin;
        }

        // Round number to fixed-point string.
        var strValFixed = nbrVal.toFixed(intFixed);
        // String to number.
        var nbrValFixed = parseFloat(strValFixed);

        // Error (NaN).
        if (strValFixed.replace(/[0-9\.]/gi, '').length > 0
            || nbrValFixed < nbrMin
        ) {
            jQuery(this).parent().addClass('has-error');
            // Stop here, user must fix.
            return;
        } else {
            jQuery(this).parent().removeClass('has-error');
        }

        // Pretty.
        var strNbr = strValFixed.replace(/0+$/gi, '').replace(/\.$/gi, '');
        // Apply.
        jQuery(this).val(strNbr);

        // Auto save.
        jQuery(this).closest('tr').find(':submit[name="updateParcel"]')
        .trigger('click.'+ window.TNTOfficiel.module.name);
    });

    jQuery(window.document)
    .off('click.' + window.TNTOfficiel.module.name, '#formAdminParcelsPanel :submit')
    .on('click.' + window.TNTOfficiel.module.name, '#formAdminParcelsPanel :submit', function (objEvent) {
        var strName = objEvent.currentTarget.name;
        if (! /^((remove|update|add)Parcel|updateOrderStateDeliveredParcels)$/gi.test(strName)) {
            return true;
        }

        // Prevent bubbling plus further handlers to execute.
        objEvent.stopImmediatePropagation();
        // Prevent default action.
        objEvent.preventDefault();

        var objData = {};
        objData['orderId'] = window.TNTOfficiel.order.intOrderID;

        if (strName !== 'updateOrderStateDeliveredParcels') {
            if (strName === 'addParcel') {
                objData['parcelId'] = 0;
            } else {
                var strArgParcelID = jQuery(objEvent.currentTarget).val();
                objData['parcelId'] = strArgParcelID;

                if (strName === 'updateParcel') {
                    objData['weight'] = jQuery('#parcelWeight-' + strArgParcelID).val();
                    var isAccountInsuranceEnabled = jQuery('#total-insurance_amount').length === 1;
                    if (isAccountInsuranceEnabled) {
                        objData['parcelInsuranceAmount'] = jQuery('#parcelInsuranceAmount-' + strArgParcelID).val();
                    }
                } else if (strName === 'removeParcel') {
                    objData['weight'] = 0;
                }
            }
        }

        jQuery(objEvent.currentTarget).addClass('disabled');

        var objJqXHR = TNTOfficiel_AJAX({
            "url": window.TNTOfficiel.link.back.module.updateOrderParcels,
            "method": 'POST',
            "data": objData,
            "dataType": 'json',
            "async": true
        });

        objJqXHR
        .done(function (objResponseJSON, strTextStatus, objJqXHR) {
            // Update content.
            if (objResponseJSON.template) {
                // Save focus.
                var strFocusIdentifier = TNTOfficiel_identifier('#formAdminParcelsPanel :input:focus');
                // Replace template.
                jQuery('#formAdminParcelsPanel').replaceWith(objResponseJSON.template);
                // Restore focus.
                if (strFocusIdentifier != null) {
                    jQuery('#formAdminParcelsPanel :input'+strFocusIdentifier).focus();
                    jQuery('#formAdminParcelsPanel :input'+strFocusIdentifier).select();
                }
            }

            if (objResponseJSON.error) {
                //showErrorMessage(TNTOfficiel_getCodeTranslate('errorUpdateFailRetry'));
                showErrorMessage(TNTOfficiel_getCodeTranslate(objResponseJSON.error));

                if (strName === 'updateParcel' && objResponseJSON.id != null) {
                    jQuery('#'+objResponseJSON.id).parent().addClass('has-warning');
                    jQuery('#'+objResponseJSON.id).focus();
                    jQuery('#'+objResponseJSON.id).select();
                }

            } else {
                if (/^((update)Parcel|updateOrderStateDeliveredParcels)$/gi.test(strName)) {
                    showSuccessMessage(TNTOfficiel_getCodeTranslate('successUpdateSuccessful'));
                }
                // strName == addParcel
                //showSuccessMessage(TNTOfficiel_getCodeTranslate('successAddSuccessful'));
                // strName == removeParcel
                //showSuccessMessage(TNTOfficiel_getCodeTranslate('successDeleteSuccessful'));
            }

            if (objResponseJSON.reload) {
                // Reload to display new order status.
                TNTOfficiel_Reload();
            }
        })
        .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
            //TNTOfficiel_Reload();
        })
        .always(function () {
            jQuery(objEvent.currentTarget).removeClass('disabled');
        });

        return false;
    });


    /*
     * Picking date
     */

    window.TNTOfficiel.order.objDatePickupMin = window.TNTOfficiel.order.objDatePickupStart;
    // When expedition is already created
    if (window.TNTOfficiel.order.isExpeditionCreated) {
        // Use minimal date (not start date) to allow display old shipping date.
        window.TNTOfficiel.order.objDatePickupMin = new Date(0);
    }

    var strDateFormat = 'l jS F Y';

    // https://api.jqueryui.com/datepicker/
    jQuery('#shipping_date').datepicker({
        "duration": 'fast',
        "minDate": window.TNTOfficiel.order.objDatePickupMin,
        "prevText": '',
        "nextText": '',
        "dateFormat": 'DD dd MM yy',
        "beforeShowDay": function (objArgDate) {
            // The date is invalid before the current date.
            if (objArgDate < window.TNTOfficiel.order.objDatePickupStart) {
                return [false, ''];
            }

            // The date is invalid on weekends.
            var arrWeek = jQuery.datepicker.noWeekends(objArgDate);
            if (!arrWeek[0]) {
                return arrWeek;
            }

            // The date is valid.
            return [true, ''];
        },
        "onSelect": function () {
            jQuery('#delivery-date-error, #delivery-date-warning').hide();

            // Get selected date.
            var objDateRequested = jQuery('#shipping_date').datepicker('getDate');

            // Display selected date.
            jQuery('#shipping_date_alt').text(
                TNTOfficiel_getDateFormat(objDateRequested, strDateFormat)
            );

            var objJqXHR = TNTOfficiel_AJAX({
                "url": window.TNTOfficiel.link.back.module.checkShippingDateValidUrl,
                "method": 'POST',
                "data": {
                    "orderId":  window.TNTOfficiel.order.intOrderID,
                    "shippingDate": TNTOfficiel_getDateFormat(objDateRequested, 'Y-m-d')
                },
                "dataType": 'json',
                "async": true
            });

            objJqXHR
            .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                if (objResponseJSON.strResponseMsgError && objResponseJSON.strResponseMsgError.length) {
                    jQuery('#delivery-date-error p').html(objResponseJSON.strResponseMsgError);
                    jQuery('#delivery-date-error').show();

                    return;
                } else if (objResponseJSON.strResponseMsgWarning && objResponseJSON.strResponseMsgWarning.length) {
                    jQuery('#delivery-date-warning p').html(objResponseJSON.strResponseMsgWarning);
                    jQuery('#delivery-date-warning').show();
                } else {
                    showSuccessMessage(TNTOfficiel_getCodeTranslate('successUpdateSuccessful'));
                }

                if (objResponseJSON.shippingDate > 0) {
                    var objDateSaved = new Date(objResponseJSON.shippingDate*1000);

                    // Set selected date.
                    jQuery('#shipping_date').datepicker(
                        'setDate',
                        // shippingDate is a unix timestamp representation of a date.
                        objDateSaved
                    );
                    // Display selected date.
                    jQuery('#shipping_date_alt').text(
                        TNTOfficiel_getDateFormat(objDateSaved, strDateFormat)
                    );
                }
                if (objResponseJSON.dueDate > 0) {
                    var objDateEstimated = new Date(objResponseJSON.dueDate*1000);

                    // Display due date.
                    jQuery('#due-date').text(
                        TNTOfficiel_getDateFormat(objDateEstimated, strDateFormat)
                    );
                }
            })
            .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
                jQuery('#delivery-date-error p').html(
                    'Une erreur s\'est produite, merci de réessayer dans quelques minutes.'
                );
                jQuery('#delivery-date-error').show();
            });
        }
    });

    // Show datepicker when clicking on the input group.
    $('#shipping_date_alt').parent('.input-group').on('mouseup', function () {
        // datepicker.
        $('#shipping_date').datepicker('show');
    });

    // If shipping date defined.
    if (window.TNTOfficiel.order.objDatePickupShipping != null) {
        // Set selected date.
        jQuery('#shipping_date').datepicker(
            'setDate',
            // shippingDate is a unix timestamp representation of a date.
            window.TNTOfficiel.order.objDatePickupShipping
        );
        // Display selected date.
        jQuery('#shipping_date_alt').text(
            TNTOfficiel_getDateFormat(window.TNTOfficiel.order.objDatePickupShipping, strDateFormat)
        );
    }

    if (window.TNTOfficiel.order.isExpeditionCreated) {
        // Disable date.
        jQuery('#shipping_date').datepicker('option', 'disabled', true);
        // Disable display.
        jQuery('#shipping_date_alt').attr('disabled', 'disabled');

    }
});