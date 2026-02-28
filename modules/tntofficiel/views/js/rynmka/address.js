/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2023 Inetum, 2016-2023 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

/**
 * Form validate function
 */
window.strTNTOfficieljQSelectorInputPostCode = [
    'input[name="postcode"]',
    'input[name="customer_address[postcode]"]',
    // module One page Checkout.
    'input[name="shipping_address[postcode]"]'
].join(',');

window.strTNTOfficieljQSelectorInputCity = [
    'input[name="city"]',
    'input[name="customer_address[city]"]',
    // module One page Checkout.
    'input[name="shipping_address[city]"]'
].join(',');

window.strTNTOfficieljQSelectorSelectCountrySelected = [
    '[name="id_country"] option:selected',
    '[name="customer_address[id_country]"] option:selected',
    // module One page Checkout.
    '[name="shipping_address[id_country]"] option:selected'
].join(',');


function validate_isPostCode(s) {
    var intCountryID = jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).val();
    // if country is FR and form field postcode, city and id_country exist on page.
    if (
        intCountryID
        && window.TNTOfficiel.country
        && window.TNTOfficiel.country[intCountryID] === 'FR'
        && jQuery(window.strTNTOfficieljQSelectorInputPostCode).length === 1
        && jQuery(window.strTNTOfficieljQSelectorInputPostCode).val() === s
        && jQuery(window.strTNTOfficieljQSelectorInputCity).length === 1
        && jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).length === 1
    ) {
        return validate_isTNTOfficielPostCode(false);
    }

    return null;
}

function validate_isCityName(s) {
    var intCountryID = jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).val();
    // if country is FR form field postcode, city and id_country exist on page.
    if (
        intCountryID
        && window.TNTOfficiel.country
        && window.TNTOfficiel.country[intCountryID] === 'FR'
        && jQuery(window.strTNTOfficieljQSelectorInputPostCode).length === 1
        && jQuery(window.strTNTOfficieljQSelectorInputCity).length === 1
        && jQuery(window.strTNTOfficieljQSelectorInputCity).val() === s
        && jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).length === 1
    ) {
        // Format city name field.
        s = s.replace(/\s*-\s*/gi, ' ');
        s = TNTOfficiel_trim(s);
        s = s.toUpperCase();
        jQuery(window.strTNTOfficieljQSelectorInputCity).val(s);

        return validate_isTNTOfficielCityName(false);
    }

    return null;
}

/**
 * AJAX request +cache to get cities using postcode and country.
 */
function TNTOfficiel_checkAddressPostcodeCity(intCountryID, strInputPostCode, strInputCity) {
    var objCheckPostcodeCityResponse = null;

    var objLink = window.TNTOfficiel.link.front;
    if (window.TNTOfficiel.link.back) {
        objLink = window.TNTOfficiel.link.back;
    }

    // if country is France.
    if (intCountryID
        && window.TNTOfficiel.country
        && window.TNTOfficiel.country[intCountryID] === 'FR'
    ) {
        // Do not perform a check if the postcode or the city is not entered.
        if (strInputPostCode.length === 5) {
            var strCacheKey = intCountryID + '\n' + strInputPostCode;

            TNTOfficiel_checkAddressPostcodeCity.cache = TNTOfficiel_checkAddressPostcodeCity.cache || {};

            // Retreive from cache if available (deep copy).
            if (TNTOfficiel_checkAddressPostcodeCity.cache[strCacheKey] != null) {
                objCheckPostcodeCityResponse =
                    jQuery.extend(true, {}, TNTOfficiel_checkAddressPostcodeCity.cache[strCacheKey]);
            }

            if (objCheckPostcodeCityResponse == null) {
                // Get the cities list matching the postcode.
                var objJqXHR = TNTOfficiel_AJAX({
                    "url": objLink.module.checkAddressPostcodeCity,
                    "method": 'POST',
                    "dataType": 'json',
                    "data": {
                        "countryId": intCountryID,
                        "postcode": strInputPostCode,
                        "city": ''
                    },
                    "async": false
                });

                objJqXHR
                .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                    // handle the response from the ajax request.
                    objCheckPostcodeCityResponse = objResponseJSON;
                })
                .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
                    TNTOfficiel_Reload();
                });

                // Store to cache if response available (deep copy).
                if (objCheckPostcodeCityResponse != null) {
                    TNTOfficiel_checkAddressPostcodeCity.cache[strCacheKey] =
                        jQuery.extend(true, {}, objCheckPostcodeCityResponse);
                }
            }

            // If selected city match one from list.
            // Note: inArray() return index -1 if not found.
            if (TNTOfficiel_isType(objCheckPostcodeCityResponse.cities, 'array') === true
                && jQuery.inArray(strInputCity, objCheckPostcodeCityResponse.cities) >= 0
            ) {
                // Flag matched.
                objCheckPostcodeCityResponse.cities = true;
            }
        }
    }

    return objCheckPostcodeCityResponse;
}

/**
 * Postcode form field validator.
 */
function validate_isTNTOfficielPostCode(boolArgNoSelect) {
    var intCountryID = jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).val(),
        strInputPostCode = jQuery(window.strTNTOfficieljQSelectorInputPostCode).val(),
        strInputCity = jQuery(window.strTNTOfficieljQSelectorInputCity).val();

    var objCheckPostcodeCityResponse = TNTOfficiel_checkAddressPostcodeCity(
        intCountryID,
        strInputPostCode,
        strInputCity
    );

    // If PostCode valid, but choice for cities.
    if (boolArgNoSelect !== true
        && objCheckPostcodeCityResponse != null
        && objCheckPostcodeCityResponse.postcode === true
        && TNTOfficiel_isType(objCheckPostcodeCityResponse.cities, 'array') === true
        && objCheckPostcodeCityResponse.cities.length > 0
    ) {
        // Display the FancyBox to select a city.
        TNTOfficiel_displayFancyBoxSelectCity(
            strInputPostCode,
            objCheckPostcodeCityResponse.cities,
            function (strArgCitySelected) {
                // Actions to perform when a city is selected in the fancybox.
                // put the value in the city field
                jQuery(window.strTNTOfficieljQSelectorInputCity).val(strArgCitySelected).focus();
                // Close the fancybox.
                jQuery.fancybox.close();

                // enable the save button.
                // jQuery("#submitAddress").removeClass("disabled");
            }
        );
    }

    // Postcode is valid if no response Error
    return objCheckPostcodeCityResponse != null
        // and if PostCode/City check not required.
        && (objCheckPostcodeCityResponse.required !== true
            // or if PostCode is valid.
            || objCheckPostcodeCityResponse.postcode === true
        );
}

/**
 * City form field validator.
 */
function validate_isTNTOfficielCityName(boolArgNoSelect) {
    var intCountryID = jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).val(),
        strInputPostCode = jQuery(window.strTNTOfficieljQSelectorInputPostCode).val(),
        strInputCity = jQuery(window.strTNTOfficieljQSelectorInputCity).val()
        ;

    var objCheckPostcodeCityResponse = TNTOfficiel_checkAddressPostcodeCity(
        intCountryID,
        strInputPostCode,
        strInputCity
    );

    // If PostCode valid, but choice for cities.
    if (boolArgNoSelect !== true
        && objCheckPostcodeCityResponse != null
        && objCheckPostcodeCityResponse.postcode === true
        && TNTOfficiel_isType(objCheckPostcodeCityResponse.cities, 'array') === true
        && objCheckPostcodeCityResponse.cities.length > 0
    ) {
        // Display the FancyBox to select a city.
        TNTOfficiel_displayFancyBoxSelectCity(
            strInputPostCode,
            objCheckPostcodeCityResponse.cities,
            function (strArgCitySelected) {
                // Actions to perform when a city is selected in the fancybox.
                // put the value in the city field
                jQuery(window.strTNTOfficieljQSelectorInputCity).val(strArgCitySelected).focus();
                // Close the fancybox.
                jQuery.fancybox.close();

                // enable the save button.
                // jQuery("#submitAddress").removeClass("disabled");
            }
        );
    }

    // City is valid if no response Error
    return objCheckPostcodeCityResponse != null
        // and if PostCode/City check not required.
        && (objCheckPostcodeCityResponse.required !== true
            // or if City is valid.
            || objCheckPostcodeCityResponse.cities === true
        );
}

/**
 * Display the fancybox to choose a city from the postcode.
 */
function TNTOfficiel_displayFancyBoxSelectCity(strArgPostCode, arrArgCities, onSubmitCity) {
    // Right postcode, there is cities to select.
    if (TNTOfficiel_isType(arrArgCities, 'array') === true && arrArgCities.length > 0) {
        // Generate the options to be put in the city select field.
        var strHTMLOptions = '';
        jQuery.each(arrArgCities, function (index, city) {
            strHTMLOptions += "<option value='" + city + "'>" + city + "</option>";
        });

        if (!!jQuery.prototype.fancybox) {

            //jQuery(window.strTNTOfficieljQSelectorInputPostCode).prop('disabled', true);
            //jQuery(window.strTNTOfficieljQSelectorInputCity).prop('disabled', true);

            jQuery.fancybox.open([
                {
                    "type": 'inline',
                    //"autoScale": true,
                    "autoDimensions": true,
                    //"centerOnScroll": true,
                    //"maxWidth": 256,
                    //"maxHeight": 768,
                    //"fitToView": false,
                    //"width": '100%',
                    //"height": '100%',
                    //"autoSize": false,
                    //"closeClick": false,
                    //"openEffect": 'none',
                    //"closeEffect": 'none',

                    "transitionIn": 'elastic',
                    "transitionOut": 'elastic',
                    "speedIn": 600,
                    "speedOut": 200,
                    "overlayShow": false,

                    "content": '\
<div id="city_helper" class="bootstrap">\
    <h1 class="page-subheading">' + TNTOfficiel_getCodeTranslate('titleValidateDeliveryAddress') + '</h1>\
    <p>'/*+TNTOfficiel_getCodeTranslate('errorUnrecognizedCity')+'<br />'*/ + TNTOfficiel_getCodeTranslate('selectCityDeliveryAddress') + '</p>\
    <div class="form-group">\
        <label for="postcode">' + TNTOfficiel_getCodeTranslate('labelPostalCode') + '</label>\
        <input class="form-control" type="text" id="helper_postcode" name="helper_postcode" value="' + strArgPostCode + '" disabled="disabled" />\
    </div>\
    <div class="form-group">\
        <label for="helper_city">' + TNTOfficiel_getCodeTranslate('labelCity') + '</label>\
        <select id="helper_city" name="helper_city" class="form-control">' + strHTMLOptions + '</select>\
    </div>\
    <br />\
    <p class="text-right">\
        <button id="validateCity" class="btn button button-tntofficiel-small">\
            <span>' + TNTOfficiel_getCodeTranslate('buttonValidate') + ' <i class="icon-chevron-right right"></i></span>\
        </button>\
    </p>\
</div>\
',
                    "afterShow": function () {
                        // Focus on city select.
                        jQuery('#city_helper #helper_city').focus();

                        //jQuery(window.strTNTOfficieljQSelectorInputPostCode).removeProp('disabled');
                        //jQuery(window.strTNTOfficieljQSelectorInputCity).removeProp('disabled');

                        // When a city is selected from the FancyBox pop-in.
                        jQuery('#city_helper #validateCity')
                        .off('click.' + window.TNTOfficiel.module.name)
                        .on('click.' + window.TNTOfficiel.module.name, function (objEvent) {
                            // Prevent bubbling plus further handlers to execute.
                            objEvent.stopImmediatePropagation();
                            // Prevent default action.
                            objEvent.preventDefault();

                            var strCitySelected = jQuery('#city_helper #helper_city').val();

                            if (TNTOfficiel_isType(onSubmitCity, 'function') === true) {
                                onSubmitCity(strCitySelected);
                            }

                            return false;
                        });
                    },
                    "helpers": {
                        "overlay": {
                            "locked": true,
                            "closeClick": false // prevents closing when clicking OUTSIDE fancybox.
                        }
                    }
                }
            ], {
                "padding": 20
            });
        } else {
            window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
            window.console.error(
                window.TNTOfficiel.module.title + ': ' + TNTOfficiel_getCodeTranslate('errorFancybox')
            );
        }
    }

    return;
}


// On Ready.
window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push(function (jQuery) {

    /* Direct */

    // If page is for this controller.
    switch (window.TNTOfficiel.link.controller) {
        case 'ordercontroller':
            // OrderController - Step 1 : 03. Address.
            // If form field postcode do not exist on page (no creation/modification address form).
            if (jQuery(window.strTNTOfficieljQSelectorInputPostCode).length === 0) {
                // On click submit button for next step.
                jQuery('#checkout')
                .off('click.' + window.TNTOfficiel.module.name)
                .on('click.' + window.TNTOfficiel.module.name,
                    ':submit[name="confirm-addresses"]',
                    TNTOfficiel_AddressDeliveryCityDirectValidate
                );
            }
            // carrier.js loaded for this controller.
            // If carrier list is displayed.
            if (TNTOfficiel_isCheckoutTNTCarrierDisplay()) {
                // Check current delivery address and reload page at validation.
                TNTOfficiel_AddressDeliveryCityDirectValidate();
            }
            break;
        case 'adminorderscontroller':
            if (TNTOfficiel.order.isDirectAddressCheck) {
                TNTOfficiel_AddressDeliveryCityDirectValidate();
            }
            break;
        default:
            break;
    }

    /* Form */

    // If form field postcode, city and id_country exist on page.
    if (jQuery(window.strTNTOfficieljQSelectorInputPostCode).length === 1
        && jQuery(window.strTNTOfficieljQSelectorInputCity).length === 1
        && jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).length === 1
    ) {
        // If page is for this controller.
        switch (window.TNTOfficiel.link.controller) {
            case 'addresscontroller':
            case 'ordercontroller':
            case 'authcontroller':
                // On address form click on submit button.
                jQuery(window.strTNTOfficieljQSelectorInputCity).closest('form').find(':submit')
                .filter(':submit[name="confirm-addresses"]')
                .off('click.' + window.TNTOfficiel.module.name)
                .on('click.' + window.TNTOfficiel.module.name, TNTOfficiel_onAddressSubmitValidate);
                // On address form submit.
                jQuery(window.strTNTOfficieljQSelectorInputCity).closest('form')
                .off('submit.' + window.TNTOfficiel.module.name)
                .on('submit.' + window.TNTOfficiel.module.name, TNTOfficiel_onAddressSubmitValidate);
                break;
            default:
                break;
        }
    }

    jQuery(window.document)
    .off('change.' + window.TNTOfficiel.module.name, window.strTNTOfficieljQSelectorInputPostCode)
    .on('change.' + window.TNTOfficiel.module.name, window.strTNTOfficieljQSelectorInputPostCode, function (objEvent) {
        validate_isPostCode(this.value);
    })
    .off('change.' + window.TNTOfficiel.module.name, window.strTNTOfficieljQSelectorInputCity)
    .on('change.' + window.TNTOfficiel.module.name, window.strTNTOfficieljQSelectorInputCity, function (objEvent) {
        validate_isCityName(this.value);
    });

});

/**
 * If required, display fancybox to set the city and prevent address form submit.
 */
function TNTOfficiel_onAddressSubmitValidate(objEvent) {
    // if country is France.
    var intCountryID = jQuery(window.strTNTOfficieljQSelectorSelectCountrySelected).val();

    if (intCountryID
        && window.TNTOfficiel.country
        && window.TNTOfficiel.country[intCountryID] === 'FR'
    ) {
        var boolIsPostCodeValid = validate_isTNTOfficielPostCode(true);
        var boolIsCityFromPostCodeValid = validate_isTNTOfficielCityName(false);

        if (!boolIsPostCodeValid) {
            jQuery(window.strTNTOfficieljQSelectorInputPostCode).focus();
            jQuery(window.strTNTOfficieljQSelectorInputPostCode).parent().addClass('form-error').removeClass('form-ok');
        }
        // if city is not correct from postcode.
        if (!boolIsCityFromPostCodeValid) {
            // Prevent bubbling plus further handlers to execute.
            objEvent.stopImmediatePropagation();
            // Prevent default action.
            objEvent.preventDefault();

            return false;
        }
    }

    return true;
}

/**
 * If required, display directly fancybox to set the city (not using address form).
 */
function TNTOfficiel_AddressDeliveryCityDirectValidate(objEvent) {
    var objLink = window.TNTOfficiel.link.front;
    var objData = {};
    if (window.TNTOfficiel.link.back) {
        objLink = window.TNTOfficiel.link.back;
        objData['id_order'] = window.TNTOfficiel.order.intOrderID;
    }

    var objJqXHR = TNTOfficiel_AJAX({
        "url": objLink.module.getAddressCities,
        "method": 'POST',
        "dataType": 'json',
        "data": objData,
        "async": false //!objEvent
    });

    objJqXHR
    .done(function (objResponseJSON, strTextStatus, objJqXHR) {
        // If city is valid, nothing to do (including non FR countries).
        if (objResponseJSON.valid === true) {
            return;
        }

        // If event exist.
        if (objEvent) {
            /// Prevent bubbling plus further handlers to execute.
            objEvent.stopImmediatePropagation();
            // Prevent default action.
            objEvent.preventDefault();
        }

        // Right postcode, there is cities.
        if (TNTOfficiel_isType(objResponseJSON.cities, 'array') === true && objResponseJSON.cities.length > 0) {
            TNTOfficiel_displayFancyBoxSelectCity(
                objResponseJSON.postcode,
                objResponseJSON.cities,
                function (strArgCitySelected) {
                    objData['city'] = strArgCitySelected;
                    // Save.
                    var objJqXHR = TNTOfficiel_AJAX({
                        "url": objLink.module.updateAddressDelivery,
                        "method": 'POST',
                        "dataType": 'json',
                        "data": objData,
                        "async": false
                    });

                    objJqXHR
                    .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                        if (objResponseJSON.result == true) {
                            // Success.
                        }
                    })
                    .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
                        TNTOfficiel_Reload();
                    })
                    .always(function () {
                        // Close the FancyBox.
                        jQuery.fancybox.close();

                        // If not an event to prevent, then reload page.
                        if (!objEvent) {
                            TNTOfficiel_Reload();
                        }
                    });
                }
            );
        }
        // Wrong postcode, there is no city.
        else {
            if (!!jQuery.prototype.fancybox) {
                jQuery.fancybox.open([
                    {
                        "type": 'inline',
                        //"autoScale": true,
                        "autoDimensions": true,
                        //"centerOnScroll": true,
                        //"maxWidth": 1280,
                        //"maxHeight": 768,
                        //"fitToView": false,
                        //"width": '100%',
                        //"height": '100%',
                        //"autoSize": false,
                        //"closeClick": false,
                        //"openEffect": 'none',
                        //"closeEffect": 'none',

                        "transitionIn": 'elastic',
                        "transitionOut": 'elastic',
                        "speedIn": 600,
                        "speedOut": 200,
                        "overlayShow": false,

                        "content": '\
<div id="city_postcode_error">\
    <h1 class="page-subheading">' + TNTOfficiel_getCodeTranslate('titleValidateDeliveryAddress') + '</h1>\
    <p class="alert-danger">' + TNTOfficiel_getCodeTranslate('errorUnknownPostalCode') + '</p>\
    <p>' + TNTOfficiel_getCodeTranslate('validatePostalCodeDeliveryAddress') + '</p>\
</div>\
',
                        "afterShow": function () {},
                        "helpers": {
                            "overlay": {
                                "locked": true,
                                // prevents closing when clicking OUTSIDE fancybox.
                                "closeClick": false
                            }
                        }
                    }
                ], {
                    "padding": 20
                });
            } else {
                window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                window.console.error(
                    window.TNTOfficiel.module.title + ': ' + TNTOfficiel_getCodeTranslate('errorFancybox')
                );
            }
        }

    })
    .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
        TNTOfficiel_Reload();
    });
}
