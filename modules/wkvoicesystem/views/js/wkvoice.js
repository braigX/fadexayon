/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
$(document).ready(function(){
    addOptionInSearchList();
    $('.material-icons.clear').css('right', '9px !important')
    if (prestashop.page.page_name == 'identity' || prestashop.page.page_name == 'authentication' || prestashop.page.page_name == 'registration' || prestashop.page.page_name == 'checkout') {
        addOptionInCustomerForm();
        addonWidth = $('#customer-form .wk_voice_block').width();
        inputWidth = $('#customer-form .wk_voice_block').parent().find('input').width();
        $('#customer-form .wk_voice_block').parent().find('input').css({width: inputWidth - addonWidth + 19, display: 'inline-block'});
        $('.form-control-comment').each(function(i,e) { $(e).attr('style', 'display: inline-block'); });
        if('Firefox' == getBrowserName()) {
            $('.wk_customer_address_from_element').each((i, e) => {
                $(e).css('padding', '0.6rem');
            });
        }
    }
    if (prestashop.page.page_name == 'address' || prestashop.page.page_name == 'checkout') {
        addOptionInCustomerAddressForm();
        addonWidth = $('.js-address-form form .wk_voice_block').width();
        inputWidth = $('.js-address-form form .wk_voice_block').parent().find('input').width();
        $('.js-address-form form .wk_voice_block').parent().find('input').css({width: inputWidth - addonWidth + 20, display: 'inline-block'});
        if('Firefox' == getBrowserName()) {
            $('.wk_customer_address_from_element').each((i, e) => {
                $(e).css('padding', '0.6rem');
            });
        }
    }
    $(window).resize(function(){
        if (prestashop.page.page_name == 'address') {
            addonWidth = $('.js-address-form form .wk_voice_block').width();
            inputWidth = $('.js-address-form form .wk_voice_block').parent().width();
            $('.js-address-form form .wk_voice_block').parent().find('input').css({width: inputWidth - addonWidth - 14, display: 'inline-block'});
        } else if (prestashop.page.page_name == 'identity' || prestashop.page.page_name == 'authentication' || prestashop.page.page_name == 'checkout') {
            addonWidth = $('#customer-form .wk_voice_block').width();
            inputWidth = $('#customer-form .wk_voice_block').parent().width();
            $('#customer-form .wk_voice_block').parent().find('input').css({width: inputWidth - addonWidth - 14, display: 'inline-block'});
        }
    });
    $(document).on('click', '.wk_voice_block', function() {
        startDictation($(this));
    });

});
function addOptionInSearchList() {
    if ($('.search-widget').length > 0) {
        $('.search-widget').children('form').append('<span class="wk_voice_block wk_voice_serach"><i class="material-icons">&#xe31d;</i></span>');
    }
}
function addOptionInCustomerForm() {
    if ($('#customer-form').length > 0) {
        var elements = ['firstname', 'lastname'];
        for (var item in elements) {
            // var selected_element = $('#customer-form').children('section').find('input[name="'+elements[item]+'"]');
            // selected_element.parent().append('<span class="wk_voice_block wk_customer_address_from_element"><i class="material-icons">&#xe31d;</i></span>');
            $('<span class="wk_voice_block wk_customer_address_from_element"><i class="material-icons">&#xe31d;</i></span>').insertAfter('input[name="'+elements[item]+'"]');
        }
    }
}
function addOptionInCustomerAddressForm() {
    if (document.getElementsByClassName('js-address-form').length > 0) {
        var list = document.querySelectorAll('.js-address-form form input[type=text]');
        if (list.length > 0) {
            for (var item of list) {
                // item.parentElement.insertAdjacentHTML('beforeend', '<span class="wk_voice_block wk_customer_address_from_element"><i class="material-icons">&#xe31d;</i></span>');
                $('<span class="wk_voice_block wk_customer_address_from_element"><i class="material-icons">&#xe31d;</i></span>').insertAfter('input[name="'+item.name+'"]');
            }
        }
    }
}
function startDictation(element) {
    element.parent().children('input[type="text"]').focus();
    var propetyExist = false;
    if (window.hasOwnProperty('webkitSpeechRecognition')) {
        propetyExist = true;
        var recognition = new webkitSpeechRecognition();
    } else if(window.hasOwnProperty('SpeechRecognition')) {
        propetyExist = true;
        var recognition = new SpeechRecognition();
    } else {
        $.growl.error({
            title: '',
            size: 'large',
            message: unsupported_browser,
        });
        return false;
    }

    if(propetyExist) {
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = locale;
        recognition.start();
        recognition.onresult = function(e) {
            element.parent().children('input[type="text"]').val(e.results[0][0].transcript);
            element.parent().children('input[type="text"]').keydown();
            recognition.stop();
        };

        recognition.onerror = function(e) {
            $.growl.error({
                title: capitalizeFirstLetter(e.error),
                size: 'medium',
                message: capitalizeFirstLetter(e.message),
            })
            recognition.stop();
        }
    }
}

function getBrowserName() {
    var browserInfo = navigator.userAgent;
    var browser;
    if (browserInfo.includes('Opera') || browserInfo.includes('Opr')) {
      browser = 'Opera';
    } else if (browserInfo.includes('Edg')) {
      browser = 'Edge';
    } else if (browserInfo.includes('Chrome')) {
      browser = 'Chrome';
    } else if (browserInfo.includes('Safari')) {
      browser = 'Safari';
    } else if (browserInfo.includes('Firefox')) {
      browser = 'Firefox'
    } else {
      browser = 'unknown'
    }
      return browser;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document ).on( "ajaxComplete", function(event, xhr, settings) {
    var ajax_url = settings.url;
    if(ajax_url.includes('action=addressForm')) {
        if (prestashop.page.page_name == 'address' || prestashop.page.page_name == 'checkout') {
            setTimeout(() => {
                addOptionInCustomerAddressForm();
                addonWidth = $('.js-address-form form .wk_voice_block').width();
                inputWidth = $('.js-address-form form .wk_voice_block').parent().find('input').width();
                $('.js-address-form form .wk_voice_block').parent().find('input').css({width: inputWidth - addonWidth + 20, display: 'inline-block'});
                if('Firefox' == getBrowserName()) {
                    $('.wk_customer_address_from_element').each((i, e) => {
                        $(e).css('padding', '0.6rem');
                    });
                }
            }, 10)
        }
    }
});