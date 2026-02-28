/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
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
    var objDefPNGAlpha = jQuery.Deferred(function () {});

    if (TNTOfficiel_isPNGAlphaSupport.isPNGAlpha !== void(0)) {
        objDefPNGAlpha.resolve(TNTOfficiel_isPNGAlphaSupport.isPNGAlpha);
    } else {
        try {
            var a = new Image, b = window.document.createElement("canvas").getContext("2d");
            a.onload = function () {
                b.drawImage(a, 0, 0);
                TNTOfficiel_isPNGAlphaSupport.isPNGAlpha = 0 === b.getImageData(0, 0, 1, 1).data[3];
                objDefPNGAlpha.resolve(TNTOfficiel_isPNGAlphaSupport.isPNGAlpha);
            };
            a.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACGFjVEwAAAABAAAAAcMq2TYAAAANSURBVAiZY2BgYPgPAAEEAQB9ssjfAAAAGmZjVEwAAAAAAAAAAQAAAAEAAAAAAAAAAAD6A+gBAbNU+2sAAAARZmRBVAAAAAEImWNgYGBgAAAABQAB6MzFdgAAAABJRU5ErkJggg==";
        } catch (c) {
            objDefPNGAlpha.reject(c);
        }
    }

    return objDefPNGAlpha;
}

/**
 * Create the HTML spinner.
 *
 * @returns {Deferred}
 */
function TNTOfficiel_CreatePageSpinner() {
    var objDefPNGAlpha = TNTOfficiel_isPNGAlphaSupport();

    var objDefHTMLSpinner = jQuery.Deferred(function () {});

    objDefPNGAlpha.done(function (boolArgSupported) {
        // If no spinner created on page.
        if (jQuery('#TNTOfficielLoading').length === 0) {
            // Create spinner to be shown during AJAX request.
            jQuery('body').append('\
<div id="TNTOfficielLoading" style="display: none">\
    <img id="loading-image" src="' + window.TNTOfficiel.link.image
                + 'loader/loader-42' + (boolArgSupported ? '.png' : '.gif') + '" alt="Loading..."/>\
</div>');
        }

        objDefHTMLSpinner.resolve(jQuery('#TNTOfficielLoading'));
    });

    return objDefHTMLSpinner;
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

    var objDefHTMLSpinner = TNTOfficiel_CreatePageSpinner();
    objDefHTMLSpinner.done(function ($elmtLoading) {
        $elmtLoading.show();
        _this.intTimeout = intArgTimeout;
        _this.hdleTimeout = window.setTimeout(function () {
            _this.hide();
        }, _this.intTimeout);

        _this.constructor.hdleList[_this.hdleTimeout] = _this;
        ++_this.constructor.hdleLength;
    });

    return _this;
};

TNTOfficiel_PageSpinner.prototype.hide = function () {
    var _this = this;

    var objDefHTMLSpinner = TNTOfficiel_CreatePageSpinner();
    objDefHTMLSpinner.done(function ($elmtLoading) {
        if (!(_this.hdleTimeout in _this.constructor.hdleList)) {
            return _this;
        }

        window.clearTimeout(_this.hdleTimeout);
        delete _this.constructor.hdleList[_this.hdleTimeout];
        --_this.constructor.hdleLength;

        if (_this.constructor.hdleLength === 0) {
            $elmtLoading.hide();
        }
    });

    return _this;
};

/**
 * AJAX request.
 *
 * @param objArgAJAXParameters
 *
 * @returns {objJqXHR}
 */
function TNTOfficiel_AJAX(objArgAJAXParameters) {
    // Do not trigger global jQuery AJAX events.
    objArgAJAXParameters['global'] = false;

    var objPageSpinner = null;
    if (objArgAJAXParameters['dataType'] !== 'script'
        && objArgAJAXParameters['async'] === true
    ) {
        objPageSpinner = TNTOfficiel_PageSpinner(8 * 1000);
    }

    var objJqXHR = jQuery.ajax(objArgAJAXParameters);

    objJqXHR
        .done(function (mxdData, strTextStatus, objJqXHR) {
            // strTextStatus : 'success'

            // Type is JSON but no data.
            if (this.dataType === 'json' && mxdData === null) {
                // Request can be a 302 Found redirected to 200 OK (end of session).
                // Like a strTextStatus === 'parsererror'
                window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                // Login ?
                TNTOfficiel_Reload();
            }
        })
        .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
            if (strTextStatus === 'timeout') {
                window.alert(TNTOfficiel_getCodeTranslate('errorConnection'));
            } else if (strTextStatus === 'abort') {
                window.alert(TNTOfficiel_getCodeTranslate('errorConnection'));
            } else if (strTextStatus === 'parsererror') {
                // dataType mismatch, error in output ?
                window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                //window.console.log(objJqXHR.responseText);
            } else {
                // strTextStatus : 'error' ou null
                if (objJqXHR.status == 0 && window.navigator.onLine === false) {
                    window.alert(TNTOfficiel_getCodeTranslate('errorNetwork'));
                } else if (objJqXHR.status >= 500) {
                    var strStatus = objJqXHR.status + ' ' + objJqXHR.statusText;
                    var $objDoc = jQuery(jQuery.parseHTML(objJqXHR.responseText));

                    var $objTitle = $objDoc.filter('title');
                    var strTitle = TNTOfficiel_trim($objTitle.text());
                    window.alert(['[' + strStatus + ']', strTitle].join('\n'));

                    $objDoc = jQuery('<div>').append($objDoc);
                    $objDoc.find('meta, link, script, style, svg, img').remove();
                    $objDoc.find('br, hr').after(document.createTextNode('\n'));
                    var strDoc = TNTOfficiel_trim($objDoc.text());
                    strDoc = strDoc.replace(/(\ *\n){2,}/gi, '\n\n').replace(/\n[\t\ ]+/gi, '\n');
                    window.console.error(strDoc);
                } else {
                    window.alert(TNTOfficiel_getCodeTranslate('errorTechnical'));
                }
            }
        })
        .always(function () {
            if (objPageSpinner != null) {
                objPageSpinner.hide();
            }
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

/**
 * Translation
 *
 * @param strArgCode
 *
 * @returns {string}
 */
function TNTOfficiel_getCodeTranslate(strArgCode) {
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
 * Get date TimeZone offset in hours (UTC diff).
 *
 * GMT-1000 HST
 * GMT-0930 Pacific/Marquesas
 * GMT+0930 Australia/Broken_Hill
 * GMT+1400 Pacific/Kiritimati
 *
 * @param objArgDate
 *
 * @returns {number}
 */
function TNTOfficiel_getDateTZ(objArgDate) {
    if (!(objArgDate instanceof Date)) {
        objArgDate = new Date(objArgDate);
    }

    var objDate = Date.UTC(
        objArgDate.getFullYear(),
        objArgDate.getMonth(),
        objArgDate.getDate(),
        objArgDate.getHours(),
        objArgDate.getMinutes()
    );
    var objDateUTC = Date.UTC(
        objArgDate.getUTCFullYear(),
        objArgDate.getUTCMonth(),
        objArgDate.getUTCDate(),
        objArgDate.getUTCHours(),
        objArgDate.getUTCMinutes()
    );

    var intTZ = (objDate - objDateUTC) / (60 * 60 * 1000);

    /*
    intTZ = objArgDate.toString().replace(/^[\s\S]+\sGMT([+-][0-9]{2})([0-9]{2})[\s\S]+$/u, '$1') * 1;
    intTZ += objArgDate.toString().replace(/^[\s\S]+\sGMT([+-][0-9]{2})([0-9]{2})[\s\S]+$/u, '$2') / 60;
    */

    return intTZ;
}

/**
 * Keep UTC representation of date in current local TZ by removing offset.
 * Use case ; unix timestamp representation of a date (no TZ but representation is changed).
 *
 * @param objArgDate
 *
 * @returns {Date}
 */
function TNTOfficiel_getDateUTCLocalTZ(objArgDate) {
    if (!(objArgDate instanceof Date)) {
        objArgDate = new Date(objArgDate);
    }

    return new Date(+objArgDate - 1000 * 60 * 60 * TNTOfficiel_getDateTZ(objArgDate));
}

/**
 * Keep current local TZ representation of date in UTC by adding offset.
 * Use case ; unix timestamp representation of a date.
 *
 * @param objArgDate
 *
 * @returns {Date}
 */
function TNTOfficiel_getDateLocalTZUTC(objArgDate) {
    if (!(objArgDate instanceof Date)) {
        objArgDate = new Date(objArgDate);
    }

    return new Date(+objArgDate + 1000 * 60 * 60 * TNTOfficiel_getDateTZ(objArgDate));
}

/**
 * DateTime Formatter.
 *
 * https://www.php.net/manual/en/datetime.format.php
 * https://api.jqueryui.com/datepicker/#utility-formatDate
 *
 * Y A full numeric representation of a year, at least 4 digits.
 * y A two digit representation of a year.
 *
 * m Numeric representation of a month, with leading zeros.
 * n Numeric representation of a month, without leading zeros
 * F A full textual representation of a month, such as January or March.
 * M A short textual representation of a month, three letters.
 *
 * z The day of the year (starting from 0).
 * d Day of the month, 2 digits with leading zeros.
 * j Day of the month without leading zeros.
 * S Ordinal suffix (st, nd, rd or th) for the day of the month.
 * l A full textual representation of the day of the week.
 *
 * H UTC 24-hour format of an hour with leading zeros.
 * i UTC Minutes with leading zeros.
 * s UTC Seconds with leading zeros.
 *
 * U Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
 *
 * P Difference to Greenwich time (GMT) with colon between hours and minutes.
 * O Difference to Greenwich time (GMT) without colon between hours and minutes.
 *
 * // UTC Timestamp.
 * TNTOfficiel_getDateFormat(1690711200*1000, 'Y-m-d H:i:s P U')
 * // Local Date.
 * TNTOfficiel_getDateFormat('2023-07-30', 'l jS F Y Y-m-d H:i:s P O U z j n/M y')
 * // UTC Date.
 * TNTOfficiel_getDateFormat('2023-07-30T00:00:00.000+00:00', 'l jS F Y Y-m-d H:i:s P O U z j n/M y')
 * TNTOfficiel_getDateFormat('2023-07-30T00:00:00.000Z', 'l jS F Y Y-m-d H:i:s P O U z j n/M y')
 *
 * @param objArgDate
 * @param strArgFormat
 * @param boolArgUTC
 *
 * @returns {string}
 */
function TNTOfficiel_getDateFormat(objArgDate, strArgFormat, boolArgUTC) {
    if (!(objArgDate instanceof Date)) {
        objArgDate = new Date(objArgDate);
    }

    if (!(objArgDate instanceof Date)) {
        throw Error('not a date !');
    }

    var objDate = objArgDate;
    var intDateTZ = TNTOfficiel_getDateTZ(objDate);

    var strFormat = 'Y-m-d';
    if (strArgFormat != null) {
        strFormat = strArgFormat;
    }

    boolUTC = false;
    if (boolArgUTC != null) {
        boolUTC = !!boolArgUTC;
    }

    var $elmtInputText = jQuery('<input type="text" />');

    if ($elmtInputText.datepicker == null) {
        throw Error('datepicker not found !');
    }

    // UTC date display workaround.
    var objDateUTCLocalTZ = TNTOfficiel_getDateUTCLocalTZ(objDate);

    var intDayYear = $elmtInputText.clone().datepicker({"dateFormat": 'o'})
        .datepicker('setDate', boolUTC ? objDateUTCLocalTZ : objDate).val() - 1;
    var intDayMonth = $elmtInputText.clone().datepicker({"dateFormat": 'd'})
        .datepicker('setDate', boolUTC ? objDateUTCLocalTZ : objDate).val() - 0;

    strFormat = strFormat
        .replace(/\bz\b/g, intDayYear)
        //.replace(/\bzz\b/g, 'oo')
        .replace(/\bY\b/g, 'yy')
        .replace(/\by\b/g, 'y')
        .replace(/\bm\b/g, 'mm')
        .replace(/\bn\b/g, 'm')
        .replace(/\bd\b/g, 'dd')
        .replace(/\bj\b/g, 'd')
        .replace(/\bjS\b/g, 'dS')
        .replace(/\bl\b/g, 'DD')
        .replace(/\bD\b/g, 'D')
        .replace(/\bF\b/g, 'MM')
        .replace(/\bM\b/g, 'M')
        .replace(/\bU\b/g, +objDate / 1000);

    $elmtInputText.datepicker({"dateFormat": strFormat});

    $elmtInputText.datepicker('setDate', boolUTC ? objDateUTCLocalTZ : objDate);

    var strHours = boolUTC ? objDate.getUTCHours() : objDate.getHours();
    strHours = (strHours >= 0 && strHours < 10) ? ('0' + strHours) : strHours;
    var strMinutes = boolUTC ? objDate.getUTCMinutes() : objDate.getMinutes();
    strMinutes = (strMinutes >= 0 && strMinutes < 10) ? ('0' + strMinutes) : strMinutes;
    var strSeconds = boolUTC ? objDate.getUTCSeconds() : objDate.getSeconds();
    strSeconds = (strSeconds >= 0 && strSeconds < 10) ? ('0' + strSeconds) : strSeconds;

    intDateTZHours = intDateTZ >> 0;
    intDateTZMinutes = ((intDateTZ - intDateTZHours) * 60) >> 0;
    var strDiffP = ((intDateTZHours >= 0) ?
            '+' + ((intDateTZHours < 10) ? ('0' + intDateTZHours) : intDateTZHours) :
            '-' + ((intDateTZHours > -10) ? ('0' + -intDateTZHours) : -intDateTZHours)
    ) + ':' + ((intDateTZMinutes < 10) ? ('0' + intDateTZMinutes) : intDateTZMinutes);
    var strDiffO = ((intDateTZHours >= 0) ?
            '+' + ((intDateTZHours < 10) ? ('0' + intDateTZHours) : intDateTZHours) :
            '-' + ((intDateTZHours > -10) ? ('0' + -intDateTZHours) : -intDateTZHours)
    ) + ((intDateTZMinutes < 10) ? ('0' + intDateTZMinutes) : intDateTZMinutes);

    if (boolUTC) {
        strDiffP = '+00:00';
        strDiffO = '+0000';
    }

    var strDateText = $elmtInputText.val();
    strDateText = strDateText
        .replace(/(^|[^a-zA-Z_])S([^a-zA-Z_]|$)/g, '$1' + (intDayMonth === 1 ? 'er' : '') + '$2')
        .replace(/\bH\b/g, strHours)
        .replace(/\bi\b/g, strMinutes)
        .replace(/\bs\b/g, strSeconds)
        .replace(/\bP\b/g, strDiffP)
        .replace(/\bO\b/g, strDiffO);

    return strDateText;
}


/**
 * Mainly for input, via unique id or name/value alternative often used in forms.
 */
function TNTOfficiel_identifier(strArgFormat) {

    $elmt = jQuery(strArgFormat);

    // Save focus.
    var strID = $elmt.attr('id');
    // Restore focus.
    if (strID != null) {
        return '#' + strID;
    }

    var strName = $elmt.attr('name');
    var strValue = $elmt.attr('value');
    // Restore focus.
    if (strName != null) {
        return '[name=' + strName + '][value=' + strValue + ']';
    }

    return null;
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
                if ((TNTOfficiel_isType(elementOption.getAttribute('selected'), 'string') === true)
                    !== jQuery(elementOption).is(':selected')
                ) {
                    boolChange = true;
                }
            });
        } else if (jQuery(element).is(':radio')) {
            if ((TNTOfficiel_isType(element.getAttribute('checked'), 'string') === true)
                !== jQuery(element).is(':checked')
            ) {
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
