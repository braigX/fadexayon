/**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Misc JavaScript functions.
 */

function sogecommerceAddMultiOption(first) {
    if (first) {
        $('#sogecommerce_multi_options_btn').hide();
        $('#sogecommerce_multi_options_table').show();
    }

    var timestamp = new Date().getTime();

    var rowTpl = $('#sogecommerce_multi_row_option').html();
    rowTpl = rowTpl.replace(/SOGECOMMERCE_MULTI_KEY/g, '' + timestamp);

    $(rowTpl).insertBefore('#sogecommerce_multi_option_add');
}

function sogecommerceDeleteMultiOption(key) {
    $('#sogecommerce_multi_option_' + key).remove();

    if ($('#sogecommerce_multi_options_table tbody tr').length === 1) {
        $('#sogecommerce_multi_options_btn').show();
        $('#sogecommerce_multi_options_table').hide();
        $('#sogecommerce_multi_options_table').append("<input type=\"hidden\" id=\"SOGECOMMERCE_MULTI_OPTIONS\" name=\"SOGECOMMERCE_MULTI_OPTIONS\" value=\"\">");
    }
}

function sogecommerceAddOneyOption(first, suffix) {
    if (first) {
        $('#sogecommerce_oney' + suffix + '_options_btn').hide();
        $('#sogecommerce_oney' + suffix + '_options_table').show();
    }

    var timestamp = new Date().getTime();
    var key = suffix != '' ? /SOGECOMMERCE_ONEY34_KEY/g : /SOGECOMMERCE_ONEY_KEY/g;
    var rowTpl = $('#sogecommerce_oney' + suffix + '_row_option').html();
    rowTpl = rowTpl.replace(key, '' + timestamp);

    $(rowTpl).insertBefore('#sogecommerce_oney' + suffix + '_option_add');
}

function sogecommerceOneyOptionChanged(key, suffix) {
    var type = $('#SOGECOMMERCE_ONEY' + suffix + '_OPTIONS_' + key + '_card_type').val();

    if (type === 'ONEY_PAYLATER') {
        $('#SOGECOMMERCE_ONEY' + suffix + '_OPTIONS_' + key + '_count').css('opacity', '0.5');;
        $('#SOGECOMMERCE_ONEY' + suffix + '_OPTIONS_' + key + '_count').attr('disabled', 'disabled');
    } else {
        $('#SOGECOMMERCE_ONEY' + suffix + '_OPTIONS_' + key + '_count').css('opacity', '1');
        $('#SOGECOMMERCE_ONEY' + suffix + '_OPTIONS_' + key + '_count').removeAttr('disabled');
    }
}

function sogecommerceDeleteOneyOption(key, suffix) {
    $('#sogecommerce_oney' + suffix + '_option_' + key).remove();

    if ($('#sogecommerce_oney' + suffix + '_options_table tbody tr').length === 1) {
        $('#sogecommerce_oney' + suffix + '_options_btn').show();
        $('#sogecommerce_oney' + suffix + '_options_table').hide();
        $('#sogecommerce_oney' + suffix + '_options_table').append("<input type=\"hidden\" id=\"SOGECOMMERCE_ONEY" + suffix + "_OPTIONS\" name=\"SOGECOMMERCE_ONEY" + suffix + "_OPTIONS\" value=\"\">");
    }
}

function sogecommerceAddFranfinanceOption(first) {
    if (first) {
        $('#sogecommerce_ffin_options_btn').hide();
        $('#sogecommerce_ffin_options_table').show();
    }

    var timestamp = new Date().getTime();
    var rowTpl = $('#sogecommerce_ffin_row_option').html();
    rowTpl = rowTpl.replace(/SOGECOMMERCE_FFIN_KEY/g, '' + timestamp);

    $(rowTpl).insertBefore('#sogecommerce_ffin_option_add');
}

function sogecommerceDeleteFranfinanceOption(key) {
    $('#sogecommerce_ffin_option_' + key).remove();

    if ($('#sogecommerce_ffin_options_table tbody tr').length === 1) {
        $('#sogecommerce_ffin_options_btn').show();
        $('#sogecommerce_ffin_options_table').hide();
        $('#sogecommerce_ffin_options_table').append("<input type=\"hidden\" id=\"SOGECOMMERCE_FFIN_OPTIONS\" name=\"SOGECOMMERCE_FFIN_OPTIONS\" value=\"\">");
    }
}

function sogecommerceAdditionalOptionsToggle(legend) {
    var fieldset = $(legend).parent();

    $(legend).children('span').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s');
    fieldset.find('section').slideToggle();
}

function sogecommerceCategoryTableVisibility() {
    var category = $('select#SOGECOMMERCE_COMMON_CATEGORY option:selected').val();

    if (category === 'CUSTOM_MAPPING') {
        $('.sogecommerce_category_mapping').show();
        $('.sogecommerce_category_mapping select').removeAttr('disabled');
    } else {
        $('.sogecommerce_category_mapping').hide();
        $('.sogecommerce_category_mapping select').attr('disabled', 'disabled');
    }
}

function sogecommerceDeliveryTypeChanged(key) {
    var type = $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_type').val();

    if (type === 'RECLAIM_IN_SHOP') {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_address').show();
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_zip').show();
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_city').show();
    } else {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_address').val('');
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_zip').val('');
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_city').val('');

        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_address').hide();
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_zip').hide();
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_city').hide();
    }

    var speed = $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_speed').val();
    if (speed === 'PRIORITY') {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_delay').show();
    } else {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_delay').hide();
    }
}

function sogecommerceDeliverySpeedChanged(key) {
    var speed = $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_speed').val();

    if (speed === 'PRIORITY') {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_delay').show();
    } else {
        $('#SOGECOMMERCE_ONEY_SHIP_OPTIONS_' + key + '_delay').hide();
    }
}

function sogecommerceRedirectChanged() {
    var redirect = $('select#SOGECOMMERCE_REDIRECT_ENABLED option:selected').val();

    if (redirect === 'True') {
        $('#sogecommerce_redirect_settings').show();
        $('#sogecommerce_redirect_settings select, #sogecommerce_redirect_settings input').removeAttr('disabled');
    } else {
        $('#sogecommerce_redirect_settings').hide();
        $('#sogecommerce_redirect_settings select, #sogecommerce_redirect_settings input').attr('disabled', 'disabled');
    }
}

function sogecommerceFullcbEnableOptionsChanged() {
    var enable = $('select#SOGECOMMERCE_FULLCB_ENABLE_OPTS option:selected').val();

    if (enable === 'True') {
        $('#sogecommerce_fullcb_options_settings').show();
        $('#sogecommerce_fullcb_options_settings select, #sogecommerce_fullcb_options_settings input').removeAttr('disabled');
    } else {
        $('#sogecommerce_fullcb_options_settings').hide();
        $('#sogecommerce_fullcb_options_settings select, #sogecommerce_fullcb_options_settings input').attr('disabled', 'disabled');
    }
}

function sogecommerceHideOtherLanguage(id, name) {
    $('.translatable-field').hide();
    $('.lang-' + id).css('display', 'inline');

    $('.translation-btn button span').text(name);

    var id_old_language = id_language;
    id_language = id;

    if (id_old_language !== id) {
        changeEmployeeLanguage();
    }
}

function sogecommerceAddOtherPaymentMeansOption(first) {
    if (first) {
        $('#sogecommerce_other_payment_means_options_btn').hide();
        $('#sogecommerce_other_payment_means_options_table').show();
        $('#SOGECOMMERCE_OTHER_PAYMENT_MEANS').remove();
    }

    var timestamp = new Date().getTime();

    var rowTpl = $('#sogecommerce_other_payment_means_row_option').html();
    rowTpl = rowTpl.replace(/SOGECOMMERCE_OTHER_PAYMENT_SCRIPT_MEANS_KEY/g, '' + timestamp);

    $(rowTpl).insertBefore('#sogecommerce_other_payment_means_option_add');
}

function sogecommerceDeleteOtherPaymentMeansOption(key) {
    $('#sogecommerce_other_payment_means_option_' + key).remove();

    if ($('#sogecommerce_other_payment_means_options_table tbody tr').length === 1) {
        $('#sogecommerce_other_payment_means_options_btn').show();
        $('#sogecommerce_other_payment_means_options_table').hide();
        $('#sogecommerce_other_payment_means_options_table').append("<input type=\"hidden\" id=\"SOGECOMMERCE_OTHER_PAYMENT_MEANS\" name=\"SOGECOMMERCE_OTHER_PAYMENT_MEANS\" value=\"\">");
    }
}

function sogecommerceAddExtraPaymentMeansOption(first) {
    if (first) {
        $('#sogecommerce_extra_payment_means_options_btn').hide();
        $('#sogecommerce_extra_payment_means_options_table').show();
        $('#SOGECOMMERCE_EXTRA_PAYMENT_MEANS').remove();
    }

    var timestamp = new Date().getTime();

    var rowTpl = $('#sogecommerce_add_payment_means_row_option').html();
    rowTpl = rowTpl.replace(/SOGECOMMERCE_EXTRA_PAYMENT_MEANS_SCRIPT_KEY/g, '' + timestamp);

    $(rowTpl).insertBefore('#sogecommerce_extra_payment_means_option_add');
}

function sogecommerceDeleteExtraPaymentMeansOption(key) {
    $('#sogecommerce_extra_payment_means_option_' + key).remove();

    if ($('#sogecommerce_extra_payment_means_options_table tbody tr').length === 1) {
        $('#sogecommerce_extra_payment_means_options_btn').show();
        $('#sogecommerce_extra_payment_means_options_table').hide();
        $('#sogecommerce_extra_payment_means_options_table').append("<input type=\"hidden\" id=\"SOGECOMMERCE_EXTRA_PAYMENT_MEANS\" name=\"SOGECOMMERCE_EXTRA_PAYMENT_MEANS\" value=\"\">");
    }
}

function sogecommerceCountriesRestrictMenuDisplay(retrictCountriesPaymentId) {
    var countryRestrict = $('#' + retrictCountriesPaymentId).val();
    if (countryRestrict === '2') {
        $('#' + retrictCountriesPaymentId + '_MENU').show();
    } else {
        $('#' + retrictCountriesPaymentId + '_MENU').hide();
    }
}

function sogecommerceOneClickMenuDisplay() {
    var restModes = ['7', '8', '9'];
    var cardEntryMode = $('select#SOGECOMMERCE_STD_CARD_DATA_MODE option:selected').val();

    if (restModes.indexOf(cardEntryMode) == -1) {
        return;
    }

    var oneClickPayment =$('select#SOGECOMMERCE_STD_1_CLICK_PAYMENT option:selected').val();

    if (oneClickPayment == 'True') {
        $('#SOGECOMMERCE_STD_USE_WALLET_MENU').show();
    } else {
        $('#SOGECOMMERCE_STD_USE_WALLET_MENU').hide();
    }
}

function sogecommerceDisplayMultiSelect(selectId) {
    $('#' + selectId).show();
    $('#' + selectId).focus();
    $('#LABEL_' + selectId).hide();
}

function sogecommerceDisplayLabel(selectId, clickMessage) {
    $('#' + selectId).hide();
    $('#LABEL_' + selectId).show();
    $('#LABEL_' + selectId).text(sogecommerceGetLabelText(selectId, clickMessage));
}

function sogecommerceGetLabelText(selectId, clickMessage) {
    var select = document.getElementById(selectId);
    var labelText = '', option;

    for (var i = 0, len = select.options.length; i < len; i++) {
        option = select.options[i];

        if (option.selected) {
            labelText += option.text + ', ';
        }
    }

    labelText = labelText.substring(0, labelText.length - 2);
    if (!labelText) {
        labelText = clickMessage;
    }

    return labelText;
}

function sogecommerceSepa1clickPaymentMenuDisplay(sepaMandateModeId) {
    var sepaMandateMode = $('#' + sepaMandateModeId).val();
    if (sepaMandateMode === 'REGISTER_PAY') {
        $('#SOGECOMMERCE_SEPA_1_CLICK_PAYMNT_MENU').show();
    } else {
        $('#SOGECOMMERCE_SEPA_1_CLICK_PAYMNT_MENU').hide();
    }
}