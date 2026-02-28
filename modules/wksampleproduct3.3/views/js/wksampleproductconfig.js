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

$(document).ready(function() {
    $('#WK_GLOBAL_SAMPLE_PRICE_TYPE').removeClass('fixed-width-xl');
    $('#WK_GLOBAL_SAMPLE_PRICE_TYPE').addClass('wk_select_width');
    if ($('#WK_GLOBAL_SAMPLE_on').is(':checked')) {
        $('.wk_global_sample_block').fadeIn();
    } else {
        $('.wk_global_sample_block').fadeOut();
    }
    $('input[name="WK_GLOBAL_SAMPLE"]').on('change', function() {
        if ($('#WK_GLOBAL_SAMPLE_on').is(':checked')) {
            $('.wk_global_sample_block').fadeIn();
        } else {
            $('.wk_global_sample_block').fadeOut();
        }
        configureGlobalSamplePrice($('#WK_GLOBAL_SAMPLE_PRICE_TYPE').val());
    });
    configureGlobalSamplePrice($('#WK_GLOBAL_SAMPLE_PRICE_TYPE').val());
    $('#WK_GLOBAL_SAMPLE_PRICE_TYPE').on('change', function() {
        configureGlobalSamplePrice($(this).val());
    });

    function configureGlobalSamplePrice(priceType)
    {
        if ((priceType == 1) || (priceType == 5)) {
            $('.wk_price_type_amount').hide();
            $('.wk_price_type_customprice').hide();
            $('.wk_price_type_tax').hide();
            $('.wk_price_type_percent').hide();
        } else if (priceType == 2) {
            $('.wk_price_type_amount').show();
            $('.wk_price_type_customprice').hide();
            $('.wk_price_type_tax').show();
            $('.wk_price_type_percent').hide();
        } else if (priceType == 3) {
            $('.wk_price_type_amount').hide();
            $('.wk_price_type_customprice').hide();
            $('.wk_price_type_tax').hide();
            $('.wk_price_type_percent').show();
        } else if (priceType == 4) {
            $('.wk_price_type_amount').hide();
            $('.wk_price_type_customprice').show();
            $('.wk_price_type_tax').show();
            $('.wk_price_type_percent').hide();
        }
    }

    $('input[type="checkbox"][name^="WK_GLOBAL_SAMPLE_CARRIERS"]').on('change', function() {
        if (($(this).attr('id') == 'WK_GLOBAL_SAMPLE_CARRIERS_0')) {
            if ($(this).is(':checked')) {
                $('.wk_global_sample_carriers input[type="checkbox"][name^="WK_GLOBAL_SAMPLE_CARRIERS"]').prop('checked', true);
            } else {
                $('.wk_global_sample_carriers input[type="checkbox"][name^="WK_GLOBAL_SAMPLE_CARRIERS"]').prop('checked', false);
            }
        } else if (!$(this).is(':checked')) {
            $('#WK_GLOBAL_SAMPLE_CARRIERS_0').prop('checked', false);
        }
    });
});