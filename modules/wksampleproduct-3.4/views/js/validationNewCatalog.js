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

$(document).ready(function () {
    validateOnV2Page();
});
function showError(elementSelector, errorMessage, extraClasses = '') {
    var errorHTML = '<div class="text-danger ' + extraClasses + '">' + errorMessage + '</div>';
    $(elementSelector).next('.text-danger').remove(); // Remove any existing error messages
    $(elementSelector).after(errorHTML);
    $('html, body').animate({
        scrollTop: $(elementSelector).offset().top - 20
    }, 500);
}

function validateOnV2Page() {
    let priceRegex = /^[-]?[0-9]{1,10}(\.[0-9]{1,9})?$/;
    let isGenericName = /^[^<>={}]*$/u;
    $(document).on('click', '#product_footer_save', function (e) {
        $('.text-danger').remove();
        if ($('[name=sample_active]').is(':checked') == true) {
            if ($('#form_hooks_wk_follow_global').is(':checked') && isGlobalSampleEnable == 0) {
                e.preventDefault();
                showError("#globalValidation", globalSettigValidation, 'mt-2');
            } else if ($('#form_hooks_max_cart_qty').val() < 0) {
                e.preventDefault();
                showError("#form_hooks_max_cart_qty", positiveQty);
            }

            let selectFieldVal = $('#wk_sample_price_type').val();
            if (selectFieldVal == 2 || selectFieldVal == 3) {
                if (selectFieldVal == 2 && $('#form_hooks_sample_amount').val() > adminProdPrice) {
                    e.preventDefault();
                    showError("#form_hooks_sample_amount", fixAmountGreater);
                }
                if ($('#form_hooks_sample_amount').val() == '' || $('#form_hooks_sample_amount').val() == 0) {
                    e.preventDefault();
                    showError("#form_hooks_sample_amount", valRequired);
                } else if ($('#form_hooks_sample_amount').val() < 0) {
                    e.preventDefault();
                    showError("#form_hooks_sample_amount", valShouldPositive);
                } else if (!priceRegex.test($('#form_hooks_sample_amount').val())) {
                    e.preventDefault();
                    showError("#form_hooks_sample_amount", invalidAmout);
                }
            } else if (selectFieldVal == 4) {
                if ($('#form_hooks_wk_sample_price').val() == '') {
                    e.preventDefault();
                    showError("#form_hooks_wk_sample_price", enterCusPrice);
                } else if (!priceRegex.test($('#form_hooks_wk_sample_price').val()) || $('#form_hooks_wk_sample_price').val() < 0) {
                    e.preventDefault();
                    showError("#form_hooks_wk_sample_price", invalidCusPrice);
                }
            }
            if ($('#form_hooks_wk_sample_weight').val() < 0 || !priceRegex.test($('#form_hooks_wk_sample_weight').val())) {
                e.preventDefault();
                showError("#hooks_wk_sample_weight", invalidWeight);
            }

            if (!isGenericName.test($('#form_hooks_sample_btn_label_' + langId).val())) {
                e.preventDefault();
                showError('#form_hooks_sample_btn_label_' + langId, invalidSampleTitle);
            }
        }
    });
}
