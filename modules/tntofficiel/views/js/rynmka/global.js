/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2023 Inetum, 2016-2023 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */


/**
 * Safe way to run callback on or after document ready.
 *
 * @param fnArgCallback
 */

window.TNTOfficiel_Start = window.TNTOfficiel_Start || +new Date();

window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push = function TNTOfficiel_Ready(fnArgCallback) {
    window.TNTOfficiel_Ready.arrRegistry = window.TNTOfficiel_Ready.arrRegistry || [];
    window.TNTOfficiel_Ready.arrRegistry.push(fnArgCallback);

    // When document ready ...
    var fnCallbackReady = function () {
        // ... waiting 1.5 sec max for jQuery exist ...
        var hdlInterval = window.setInterval(function () {
            if (window.jQuery != null || ((+new Date()) - window.TNTOfficiel_Start) > 1500) {
                window.clearInterval(hdlInterval);
                // ... and run passing jQuery reference.
                fnArgCallback(window.jQuery);
            }
        }, 165);
    };

    // If document already ready ...
    if (/complete|loaded/gi.test(window.document.readyState)) {
        fnCallbackReady();
    } else if (window.document.addEventListener) {
        // ... else document is not ready and the addEventListener method is available.
        window.document.addEventListener('DOMContentLoaded', fnCallbackReady, false);
    } else {
        // ... else polling for document ready.
        var hdlInterval = window.setInterval(function () {
            if (/complete|loaded/gi.test(window.document.readyState)) {
                window.clearInterval(hdlInterval);
                fnCallbackReady();
            }
        }, 165);
    }
};

// Run registered callback before function was declared.
(function () {
    var fnArgCallback;
    while (fnArgCallback = window.TNTOfficiel_Ready.shift()) {
        window.TNTOfficiel_Ready.push(fnArgCallback);
    }
})();

/**
 * Cross-browser type test.
 *
 * @param mxdArgData
 * @param mxdArgTypeCmp 'undefined', 'null', 'window',
 * 'boolean', 'number', 'string', 'function', 'array', 'date', 'regexp', 'object', 'error', 'symbol'
 *
 * @returns {boolean|null} Use strict comparison (null on error).
 */
function TNTOfficiel_isType(mxdArgData, mxdArgTypeCmp) {

    var arrClassList = [
        'Boolean',
        'Number',
        'String',
        'Function',
        'Array',
        'Date',
        'RegExp',
        'Object',
        'Error',
        'Symbol'
    ];
    var arrTypeList = ['undefined', 'null', 'window'];
    var arrClassMap = {};

    jQuery.each(
        arrClassList,
        function (i, strArgType) {
            arrTypeList.push(strArgType.toLowerCase());
            arrClassMap['[object ' + strArgType + ']'] = strArgType.toLowerCase();
        }
    );


    var strTypeOfData = (typeof mxdArgData).toLowerCase();
    var strTypeData;

    if (mxdArgData === void(0)) {
        strTypeData = 'undefined';
    } else if (mxdArgData === null) {
        strTypeData = 'null';
    } else if (mxdArgData === mxdArgData.window) {
        strTypeData = 'window';
    } else if ((strTypeOfData === 'object') || (strTypeOfData === 'function')) {
        // Get class.
        strTypeData = (arrClassMap[({}).toString.call(mxdArgData)] || 'object');
    } else {
        // else use typeof.
        strTypeData = strTypeOfData;
    }

    // Unknown type comparison.
    if (!(jQuery.inArray(strTypeData, arrTypeList) >= 0)) {
        // unable.
        return null;
    }


    var strTypeOfCmp = (typeof mxdArgTypeCmp).toLowerCase();
    var strTypeCmp;

    if (mxdArgTypeCmp === void(0)) {
        strTypeCmp = 'undefined';
    } else if (mxdArgTypeCmp === null) {
        strTypeCmp = 'null';
    } else if (strTypeOfCmp === 'string' && mxdArgTypeCmp !== '') {
        // If non empty string, type is specified.
        strTypeCmp = mxdArgTypeCmp.toLowerCase();
    } else if (mxdArgTypeCmp === mxdArgTypeCmp.window) {
        strTypeCmp = 'window';
    } else if ((strTypeOfCmp === 'object') || (strTypeOfCmp === 'function')) {
        // Get class.
        strTypeCmp = (arrClassMap[({}).toString.call(mxdArgTypeCmp)] || 'object');
    } else {
        // else use typeof.
        strTypeCmp = strTypeOfCmp;
    }

    // Unknown type comparison.
    if (!(jQuery.inArray(strTypeCmp, arrTypeList) >= 0)) {
        // unable.
        return null;
    }


    return strTypeData === strTypeCmp;
}

/**
 * Trim.
 *
 * @param strArg
 *
 * @returns {null|string}
 */
function TNTOfficiel_trim(strArg) {
    if (TNTOfficiel_isType(strArg, 'string') !== true) {
        // unable.
        return null;
    }

    strArg = strArg.replace(/^\s+|\s+$/gi, '');

    return strArg;
}

/**
 * Cross-browser function binding.
 *
 * @param fnArgCallback
 * @param mxdArgContext
 *
 * @returns {function|null}
 */
function TNTOfficiel_bind(fnArgCallback, mxdArgContext) {
    if (TNTOfficiel_isType(fnArgCallback, 'function') !== true) {
        // unable.
        return null;
    }

    var arrArgumentsBind = Array.prototype.slice.call(arguments, 2);

    return function () {
        return fnArgCallback.apply(
            mxdArgContext || this,
            arrArgumentsBind
        );
    };
}

/**
 * Check if PNG Alpha is supported.
 *
 * @returns {Deferred}
 */
function TNTOfficiel_isPNGAlphaSupport() {
    var objDef = jQuery.Deferred(function () {});

    if (TNTOfficiel_isPNGAlphaSupport.isPNGAlpha !== void(0)) {
        objDef.resolve(TNTOfficiel_isPNGAlphaSupport.isPNGAlpha);
    } else {
        try {
            var a = new Image, b = window.document.createElement("canvas").getContext("2d");
            a.onload = function () {
                b.drawImage(a, 0, 0);
                TNTOfficiel_isPNGAlphaSupport.isPNGAlpha = 0 === b.getImageData(0, 0, 1, 1).data[3];
                objDef.resolve(TNTOfficiel_isPNGAlphaSupport.isPNGAlpha);
            };
            a.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACGFjVEwAAAABAAAAAcMq2TYAAAANSURBVAiZY2BgYPgPAAEEAQB9ssjfAAAAGmZjVEwAAAAAAAAAAQAAAAEAAAAAAAAAAAD6A+gBAbNU+2sAAAARZmRBVAAAAAEImWNgYGBgAAAABQAB6MzFdgAAAABJRU5ErkJggg==";
        } catch (c) {
            objDef.reject(c);
        }
    }

    return objDef;
}


function TNTOfficiel_CreatePageSpinner() {
    var objDef = TNTOfficiel_isPNGAlphaSupport();

    objDef.done(function (boolArgSupported) {
        // If no spinner created on page.
        if (jQuery('#TNTOfficielLoading').length === 0) {
            // Create spinner to be shown during AJAX request.
            jQuery('body').append('\
<div id="TNTOfficielLoading" style="display: none">\
    <img id="loading-image" src="' + window.TNTOfficiel.link.image + 'loader/loader-42' + (boolArgSupported ? '.png' : '.gif') + '" alt="Loading..."/>\
</div>');
        }
    });

    return jQuery('#TNTOfficielLoading');
}

/**
 * Spinner.
 *
 * @param intArgTimeout
 *
 * @returns {TNTOfficiel_PageSpinner}
 *
 * @constructor
 */
function TNTOfficiel_PageSpinner(intArgTimeout) {
    if (!(this instanceof TNTOfficiel_PageSpinner)) {
        return new TNTOfficiel_PageSpinner(intArgTimeout);
    }

    if (!(intArgTimeout != null && intArgTimeout > 1)) {
        intArgTimeout = 16 * 1000;
    }

    this.constructor.hdleList = this.constructor.hdleList || {};
    this.constructor.hdleLength = this.constructor.hdleLength || 0;

    this.show(intArgTimeout);
}

TNTOfficiel_PageSpinner.prototype.show = function (intArgTimeout) {
    var _this = this;

    TNTOfficiel_CreatePageSpinner();
    jQuery('#TNTOfficielLoading').show();

    this.intTimeout = intArgTimeout;
    this.hdleTimeout = window.setTimeout(function () {
        _this.hide();
    }, this.intTimeout);

    this.constructor.hdleList[this.hdleTimeout] = this;
    ++this.constructor.hdleLength;

    return this;
};

TNTOfficiel_PageSpinner.prototype.hide = function () {
    if (!(this.hdleTimeout in this.constructor.hdleList)) {
        return this;
    }

    window.clearTimeout(this.hdleTimeout);
    delete this.constructor.hdleList[this.hdleTimeout];
    --this.constructor.hdleLength;

    if (this.constructor.hdleLength === 0) {
        jQuery('#TNTOfficielLoading').hide();
    }

    return this;
};

/**
 * Translation
 *
 * @param strArgCode
 *
 * @returns {string}
 */
function TNTOfficiel_getCodeTranslate(strArgCode)
{
    if (TNTOfficiel_isType(strArgCode, 'string') === true) {
        // trim.
        strArgCode = TNTOfficiel_trim(strArgCode);
        // If is a translation ID.
        if (window.TNTOfficiel.translate[strArgCode]) {
            strArgCode = jQuery('<span>' + window.TNTOfficiel.translate[strArgCode] + '</span>').text();
        }
        // If is a translation ID (BO).
        else if (window.TNTOfficiel.translate.back
            && window.TNTOfficiel.translate.back[strArgCode]
        ) {
            strArgCode = jQuery('<span>' + window.TNTOfficiel.translate.back[strArgCode] + '</span>').text();
        }

        return strArgCode;
    }

    return '';
}

/**
 * AJAX request.
 *
 * @param $objArgAJAXParameters
 *
 * @returns {objJqXHR}
 */
function TNTOfficiel_AJAX($objArgAJAXParameters) {
    // Do not trigger global jQuery AJAX events.
    $objArgAJAXParameters['global'] = false;

    var objPageSpinner = TNTOfficiel_PageSpinner(8 * 1000);

    var objJqXHR = jQuery.ajax($objArgAJAXParameters);

    objJqXHR
        .done(function (mxdData, strTextStatus, objJqXHR) {
            // strTextStatus : 'success'

            // Type is JSON but no JSON object.
            if (this.dataType === 'json' && mxdData === null) {
                // Request can be a 302 Found redirected to 200 OK (end of session).
                // Like a strTextStatus === 'parsererror'
                alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                // Login ?
                TNTOfficiel_Reload();
            }
        })
        .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
            if (strTextStatus === 'timeout') {
                alert(TNTOfficiel_getCodeTranslate('errorConnection'));
            } else if (strTextStatus === 'abort') {
                alert(TNTOfficiel_getCodeTranslate('errorConnection'));
            } else if (strTextStatus === 'parsererror') {
                // dataType mismatch, error in output ?
                alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                //window.console.log(objJqXHR.responseText);
            } else {
                // strTextStatus : 'error' ou null
                if (objJqXHR.status == 0 && window.navigator.onLine === false) {
                    alert(TNTOfficiel_getCodeTranslate('errorNetwork'));
                } else if (objJqXHR.status >= 500) {
                    var strStatus = objJqXHR.status + ' ' + objJqXHR.statusText;
                    var $objDoc = jQuery(jQuery.parseHTML(objJqXHR.responseText));
                    $objDoc = $objDoc.not('meta, link, script, style, svg, img');
                    var $objTitle = $objDoc.filter('title');
                    var strTitle = TNTOfficiel_trim($objTitle.text());
                    //var strDoc = TNTOfficiel_trim($objDoc.text().replace(/(\ *\n){2,}/gi, '\n\n'));
                    //window.console.error(strDoc);
                    alert([strStatus, strTitle].join('\n'));
                } else {
                    alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                }
            }
        })
        .always(function () {
            objPageSpinner.hide();
        });

    return objJqXHR;
}

/**
 * Reload a page.
 */
function TNTOfficiel_Reload() {
    // Tells user to wait.
    TNTOfficiel_PageSpinner();
    // Reload page.
    window.location.reload();
}


function TNTOfficiel_AdminAlert(objArgAlert, strTitle) {

    if (!window.TNTOfficiel.link.back) {
        return;
    }

    var objAlertType = {
        "error": 'danger',
        "warning": 'warning',
        "info": 'info',
        "success": 'success'
    };

    // Default title.
    if (strTitle == null) {
        strTitle = window.TNTOfficiel.module.title;
    }

    // For each message type.
    for (var strAlertType in objAlertType) {
        var strAlertClass = objAlertType[strAlertType];
        // If at least one message for current type.
        if (objArgAlert
            && objArgAlert[strAlertType]
            && objArgAlert[strAlertType].length > 0
        ) {
            // Get new message array.
            var arrArgAlertType = jQuery.map(objArgAlert[strAlertType], function (value, key) {
                if (TNTOfficiel_isType(value, 'string') === true) {
                    // Add.
                    return TNTOfficiel_getCodeTranslate(value);
                }
                // Do not add.
                return null;
            });

            // Flush this messages type.
            objArgAlert[strAlertType] = [];

            // May occur if value is not a string.
            if (arrArgAlertType.length > 0) {
                var $elmtAlert = jQuery('\
                <div class="bootstrap">\
                    <div class="alert alert-' + strAlertClass + '" >\
                        <button type="button" class="close" data-dismiss="alert">×</button>\
                        <h4>' + strTitle + '</h4>\
                        <ul></ul>\
                    </div>\
                </div>');
                // Add list item.
                jQuery.each(arrArgAlertType, function (index, value) {
                    $elmtAlert.find('ul').append(jQuery('<li></li>').append(window.document.createTextNode(value)));
                });
                // Insert on top of page content PS 1.7.6.9-, PS 1.7.7+
                $elmtPrepend = jQuery('#main #content, #main-div .content-div');
                // If known theme.
                if ($elmtPrepend.length === 1) {
                    $elmtPrepend.prepend($elmtAlert);
                    // Force to show alert on top of page
                    // On load after redirection.
                    jQuery(window)
                        .on('load', function () {
                            window.setTimeout(function () {
                                jQuery(window).scrollTop(0);
                            }, 1);
                        });
                    // Or after a page was loaded.
                    jQuery(window).scrollTop(0);
                } else {
                    // Fallback.
                    window.alert('[' + strAlertType.toUpperCase() + '] '
                        + strTitle + ' :\n- ' + arrArgAlertType.join('\n- '));
                }
            }
        }
    }
}


function TNTOfficiel_hasInputChange(collection) {

    var $collection = jQuery();
    var boolChange = false;

    jQuery(collection).each(function (intIndex, element) {
        var $element = jQuery(element);
        if ($element.is('form')) {
            $collection = $collection.add(element.elements);
        } else {
            $collection = $collection.add(element);
        }
    });

    $collection = $collection
        .not(':disabled')
        .not('input[type="hidden"]')
        .not('[type="submit"]')
        .not('[type="reset"]')
        .not('button');

    $collection.each(function (intIndex, element) {
        // TODO : checkbox, textarea, file.
        if (jQuery(element).is('select')) {
            jQuery(element).find('option').each(function (intIndex, elementOption) {
                if ((TNTOfficiel_isType(elementOption.getAttribute('selected'), 'string') === true) !== jQuery(elementOption).is(':selected')) {
                    boolChange = true;
                }
            });
        } else if (jQuery(element).is(':radio')) {
            if ((TNTOfficiel_isType(element.getAttribute('checked'), 'string') === true) !== jQuery(element).is(':checked')) {
                boolChange = true;
            }
        } else {
            var strValue = element.getAttribute('value');
            if (TNTOfficiel_isType(strValue, 'string') !== true) {
                strValue = '';
            }
            if (strValue !== jQuery(element).val()) {
                boolChange = true;
            }
        }
    });

    return boolChange;
}


// On Ready.
window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push(function (jQuery) {

    /*
     Display error.
     */
    if (window.TNTOfficiel && window.TNTOfficiel.alert) {
        // Always check for message to display.
        var hdlInterval = window.setInterval(function () {
            TNTOfficiel_AdminAlert(window.TNTOfficiel.alert);
        }, 330);
    }
});
