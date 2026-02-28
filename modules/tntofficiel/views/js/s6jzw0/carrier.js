/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

/**
 * Get jQuery collection of TNT radio input element in checkout.
 *
 * @param boolArgChecked true to get the checked one.
 */
function TNTOfficiel_getCheckoutTNTRadio(boolArgChecked) {
    // If carrier list unavailable.
    if (!(window.TNTOfficiel
        && window.TNTOfficiel.carrier
        && window.TNTOfficiel.carrier.list
    )) {
        // error
        return jQuery();
    }

    var strDeliveryOptionRadioTNTSelector = jQuery
        .map(window.TNTOfficiel.carrier.list, function (value, id_carrier) {
            return '.delivery-option input:radio[value^="' + id_carrier + ',"]';
        }).join(', ');

    // Filter to exclude real hidden input (allow masked one with graphics).
    var $objDeliveryOptionRadioTNTList = jQuery(strDeliveryOptionRadioTNTSelector)
        .filter(function (intIndex, element) {
            var $element = jQuery(element);
            // true to keep element, false to exclude.
            return $element.parent(':visible').length === 1;
        });

    if (boolArgChecked === true) {
        return $objDeliveryOptionRadioTNTList.filter(':checked');
    }

    return $objDeliveryOptionRadioTNTList;
}

/**
 * Helper to get TNT Carrier ID form current selection or given radio element.
 *
 * @param $objArgDeliveryOptionRadioTNT void for current option else specified radio option element.
 *
 * @returns {*}
 */
function TNTOfficiel_getCheckoutTNTCarrierID($objArgDeliveryOptionRadioTNT) {
    var $objDeliveryOptionRadioTNTChecked = TNTOfficiel_getCheckoutTNTRadio(true);

    if ($objArgDeliveryOptionRadioTNT) {
        var $objDeliveryOptionRadioTNTList = TNTOfficiel_getCheckoutTNTRadio();
        $objDeliveryOptionRadioTNTChecked = jQuery($objArgDeliveryOptionRadioTNT);
        $objDeliveryOptionRadioTNTChecked = $objDeliveryOptionRadioTNTList.filter($objDeliveryOptionRadioTNTChecked);
    }

    if ($objDeliveryOptionRadioTNTChecked.length === 1) {
        return $objDeliveryOptionRadioTNTChecked.val().split(',')[0] | 0;
    }

    return null;
}

/**
 * Is some TNT carriers is displayed in checkout.
 *
 * @param boolChecked true to filter checked.
 *
 * @returns {boolean}
 */
function TNTOfficiel_isCheckoutTNTCarrierDisplay(boolChecked) {
    var $objDeliveryOptionRadioTNTList = TNTOfficiel_getCheckoutTNTRadio(boolChecked);

    return ($objDeliveryOptionRadioTNTList.length > 0);
}

/**
 * Is extra data form valiated ?
 *
 * @returns {boolean}
 */
function TNTOfficiel_isExtraDataValidated() {
    return (jQuery('#extra_address_data').length == 0 || (!!jQuery('#extra_address_data').data('validated')));
}

function TNTOfficiel_setExtraDataValidated(boolArgAllow) {
    // Flag validated.
    jQuery('#extra_address_data').data(
        'validated',
        boolArgAllow == null ? TNTOfficiel_isExtraDataValidated() : !!boolArgAllow
    );

    // Implies update.
    TNTOfficiel_updatePaymentDisplay();
}


function TNTOfficiel_isDeliveryPointValidated() {
    var $objDeliveryOptionRadioTNTChecked = TNTOfficiel_getCheckoutTNTRadio(true);
    var strTNTCheckedCarrierType = null,
        boolHasRepoAddressSelected = false;

    if ($objDeliveryOptionRadioTNTChecked.length === 1) {
        var intTNTCarrierIDChecked = TNTOfficiel_getCheckoutTNTCarrierID();

        if (window.TNTOfficiel
            && window.TNTOfficiel.carrier
            && window.TNTOfficiel.carrier.list
            && window.TNTOfficiel.carrier.list[intTNTCarrierIDChecked]) {
            strTNTCheckedCarrierType = window.TNTOfficiel.carrier.list[intTNTCarrierIDChecked].carrier_type;
        }

        boolHasRepoAddressSelected = jQuery('#TNTOfficielCarrierExtra_' + intTNTCarrierIDChecked)
            .find('.tntofficiel-shipping-method-info').length > 0;
    }

    var boolIsRepoTypeSelected = (
        strTNTCheckedCarrierType === 'DROPOFFPOINT'
        || strTNTCheckedCarrierType === 'DEPOT'
    );

    // If the selected TNT is a delivery point with a selected address.
    // or not a delivery point and no address is selected.
    return (
        (boolIsRepoTypeSelected && boolHasRepoAddressSelected)
        || (!boolIsRepoTypeSelected && !boolHasRepoAddressSelected)
    );
}

/**
 * Get current payment ready state.
 * @returns {boolean}
 * @constructor
 */
function TNTOfficiel_isPaymentReady() {
    var arrError = [];

    // Result from async AJAX request.
    var objResult = null;

    var objJqXHR = TNTOfficiel_AJAX({
        "url": window.TNTOfficiel.link.front.module.checkPaymentReady,
        "method": 'POST',
        "dataType": 'json',
        "async": false
    });

    objJqXHR
        .done(function (objResponseJSON, strTextStatus, objJqXHR) {
            objResult = objResponseJSON;
        })
        .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
            //window.console.error(objJqXHR.status + ' ' + objJqXHR.statusText);
        });

    // If no result or has error.
    if (!objResult || objResult.error != null) {
        // Display alert message.
        if (objResult && objResult.error != null) {
            arrError.push(objResult.error);
        } else {
            arrError.push('errorTechnical');
        }

        return arrError;
    }

    // If the selected carrier (core) is not TNT, we don't handle it.
    if (objResult['carrier'] !== window.TNTOfficiel.module.name) {
        return arrError;
    }
    /*
    if (!TNTOfficiel_isDeliveryPointValidated()) {
        arrError.push('errorNoDeliveryPointSelected');
    }
    */
    // If extra data form was not filled and validated.
    if (!TNTOfficiel_isExtraDataValidated()) {
        arrError.push('validateAdditionalCarrierInfo');
    }

    return arrError;
}

/**
 * Allow payment by showing or hiding payments options.
 */
function TNTOfficiel_updatePaymentDisplay() {
    var $elmtInsertBefore = jQuery('#checkout-payment-step .content');

    jQuery('#payment-confirmation :input').removeClass('disabled');
    jQuery('#TNTOfficielHidePayment').remove();

    // if extra data form to fill exist and is validated.
    var arrPaymentReadyError = TNTOfficiel_isPaymentReady();
    if (arrPaymentReadyError.length > 0) {
        var strError = (window.TNTOfficiel.translate[arrPaymentReadyError[0]] || arrPaymentReadyError[0]);

        jQuery('#payment-confirmation :input').addClass('disabled');
        $elmtInsertBefore.before('\
<div id="TNTOfficielHidePayment">\
<p class="alert alert-danger">' + window.TNTOfficiel.module.title + ': ' + strError + '</p>\
<style type="text/css">\
\
    #checkout-payment-step .content, #checkout-payment-step .content * {\
        display: none !important;\
    }\
\
</style>\
</div>');
    }
}


// On Ready.
window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push(function (jQuery) {

    var strSelectorShippingDeliveryPointsItems = [
        '.tntofficiel-shipping-method-info',
        '.tntofficiel-shipping-method-info-select'
    ].join(',');

    // Click on address displayed in delivery option from DROPOFFPOINT or DEPOT.
    jQuery(window.document)
        .off('click.' + window.TNTOfficiel.module.name, strSelectorShippingDeliveryPointsItems)
        .on('click.' + window.TNTOfficiel.module.name, strSelectorShippingDeliveryPointsItems, function (objEvent) {
            // Prevent bubbling plus further handlers to execute.
            objEvent.stopImmediatePropagation();
            // Prevent default action.
            objEvent.preventDefault();

            // Get carrier ID from the event.
            var strCarrierExtraID = jQuery(objEvent.currentTarget)
                .closest('div[id^=TNTOfficielCarrierExtra_]')
                .attr('id');

            if (!strCarrierExtraID) {
                //var intTNTCarrierID = TNTOfficiel_getCheckoutTNTCarrierID();
                window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));

                return false;
            }

            var intTNTCarrierID = strCarrierExtraID.split('_').pop() | 0;

            TNTOfficiel_XHRBoxDeliveryPoints(intTNTCarrierID);

            return false;
        });


    /*
     * Payment Choice.
     */

    // On payment submit.
    jQuery(window.document)
        .off('mousedown.' + window.TNTOfficiel.module.name + ' click.' + window.TNTOfficiel.module.name,
            '#payment-confirmation :input'
        )
        .on('mousedown.' + window.TNTOfficiel.module.name + ' click.' + window.TNTOfficiel.module.name,
            '#payment-confirmation :input',
            TNTOfficiel_XHRcheckPaymentReady
        );
});


/*
 * AJAX after a click on payment button.
 * Do check to prevent payment action.
 */
function TNTOfficiel_XHRcheckPaymentReady(objEvent) {
    // If payment is ready (JS check).
    var arrPaymentReadyError = TNTOfficiel_isPaymentReady();
    if (arrPaymentReadyError.length > 0) {
        // Prevent default and further action.
        objEvent.stopImmediatePropagation();
        // Prevent default action.
        objEvent.preventDefault();

        window.alert(TNTOfficiel_getCodeTranslate(arrPaymentReadyError[0]));
        // Force to stay on current page.
        TNTOfficiel_Reload();

        return false;
    }

    return true;
}
