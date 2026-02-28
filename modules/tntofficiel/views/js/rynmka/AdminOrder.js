/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2023 Inetum, 2016-2023 TNT
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
            var nbrMin = 0.1;
            var intFixed = 1;
        }

        var strValRaw = jQuery(this).val();
        // Helper.
        var strVal = TNTOfficiel_trim(strValRaw.replace(',','.'));
        var nbrVal = parseFloat(strVal);
        if (nbrVal < nbrMin) {
            nbrVal = nbrMin;
        }
        // Round.
        var strValFixed = nbrVal.toFixed(intFixed);
        var nbrValFixed = parseFloat(strValFixed);
        // Error (NaN).
        if (strValFixed.replace(/[0-9\.]/gi, '').length > 0) {
            jQuery(this).parent().addClass('has-error');
            return;
        } else if (nbrValFixed < nbrMin) {
            jQuery(this).parent().addClass('has-error');
        } else {
            jQuery(this).parent().removeClass('has-error');
        }
        // Pretty.
        var strNbr = strValFixed.replace(/0+$/gi, '').replace(/\.$/gi, '');
        // Apply.
        jQuery(this).val(strNbr);
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
            "dataType": 'json',
            "data": objData,
            "async": true
        });

        objJqXHR
        .done(function (objResponseJSON, strTextStatus, objJqXHR) {
            // Update content.
            if (objResponseJSON.template) {
                jQuery('#formAdminParcelsPanel').replaceWith(objResponseJSON.template);
            }

            if (objResponseJSON.error) {
                //showErrorMessage(TNTOfficiel_getCodeTranslate('errorUpdateFailRetry'));
                showErrorMessage(TNTOfficiel_getCodeTranslate(objResponseJSON.error));
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

    // Fallback client side.
    if (window.TNTOfficiel.order.objDatePickupStart == null) {
        // Today.
        window.TNTOfficiel.order.objDatePickupStart = new Date();
        // Midnight.
        window.TNTOfficiel.order.objDatePickupStart.setHours(0, 0, 0, 0);
    }

    window.TNTOfficiel.order.objDatePickupMin = window.TNTOfficiel.order.objDatePickupStart;
    // When expedition is already created
    if (window.TNTOfficiel.order.isExpeditionCreated) {
        // Use minimal date (not start date) to allow display old shipping date.
        window.TNTOfficiel.order.objDatePickupMin = new Date(0);
    }

    jQuery('#shipping_date').datepicker({
        "minDate": window.TNTOfficiel.order.objDatePickupMin,
        "prevText": '',
        "nextText": '',
        "dateFormat": 'dd/mm/yy',
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
            jQuery('#delivery-date-error, #delivery-date-success').hide();
            var objData = {};
            objData['orderId'] = window.TNTOfficiel.order.intOrderID;
            objData['shippingDate'] = jQuery('#shipping_date').val();

            var objJqXHR = TNTOfficiel_AJAX({
                "url": window.TNTOfficiel.link.back.module.checkShippingDateValidUrl,
                "method": 'POST',
                "dataType": 'json',
                "data": objData,
                "async": true
            });

            objJqXHR
            .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                if (objResponseJSON.strResponseMsgError && objResponseJSON.strResponseMsgError.length) {
                    jQuery('#delivery-date-error p').html(objResponseJSON.strResponseMsgError);
                    jQuery('#delivery-date-error').show();

                    return;
                } else if (objResponseJSON.strResponseMsgWarning && objResponseJSON.strResponseMsgWarning.length) {
                    jQuery('#delivery-date-success p').html(objResponseJSON.strResponseMsgWarning);
                    jQuery('#delivery-date-success').show();
                } else {
                    jQuery('#delivery-date-success p').html('La date est valide.');
                    jQuery('#delivery-date-success').show();
                }

                if (objResponseJSON.dueDate) {
                    jQuery('#due-date').html(objResponseJSON.dueDate);
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

    // If shipping date defined.
    if (window.TNTOfficiel.order.objDatePickupShipping != null) {
        jQuery('#shipping_date').datepicker('setDate', window.TNTOfficiel.order.objDatePickupShipping);
    }
    if (window.TNTOfficiel.order.isExpeditionCreated) {
        jQuery('#shipping_date').datepicker('option', 'disabled', true);
    }
});