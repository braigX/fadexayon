/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
$(document).ready(function() {
    $(document).on('click', '.presta-active', function(){
        $('.presta-active').removeClass('active');
        $(this).addClass('active');
    });

    if ($('.presta-tiny-mce').length) {
        tinySetup({
            editor_selector : "presta-tiny-mce"
        });
    }

    if ($('input[name="presta_cms_page_rule"]:checked').val() == "1") {
        $('.presta-choose-cms').show();
    } else {
        $('.presta-choose-cms').hide();
    }

    $(document).on('change', 'input[name="presta_cms_page_rule"]', function(){
        if ($(this).val() == "1") {
            $('.presta-choose-cms').show('slow');
        } else if ($(this).val() == "0"){
            $('.presta-choose-cms').hide('slow');
        }
    });

    $(document).on('change', 'input[name="presta_enable_group_selection"]', function(){
        if ($(this).val() == "1") {
            $('.presta-select-group').show('slow');
            $('.presta-assign-groups').hide('slow');
        } else if ($(this).val() == "0"){
            $('.presta-select-group').hide('slow');
            $('.presta-assign-groups').show('slow');
        }
    });
    if ($('input[name="presta_enable_group_selection"]:checked').val() == "1") {
        $('.presta-select-group').show();
        $('.presta-assign-groups').hide();
    } else {
        $('.presta-select-group').hide();
        $('.presta-assign-groups').show();
    }

    $(document).on('change', 'input[name="presta_enable_group_selection_one"]', function(){
        if ($(this).val() == "1") {
            if ($('input[name="presta_enable_group_selection"]:checked').val() == "1") {
                $('.presta-select-group').show('slow');
                $('.presta-assign-groups').hide('slow');
            } else {
                $('.presta-select-group').hide('slow');
                $('.presta-assign-groups').show('slow');
            }
            $('.presta-group-selection').show('slow');
        } else {
            $('.presta-group-selection').hide('slow');
            $('.presta-assign-groups').hide('slow');
            $('.presta-select-group').hide('slow');
        }
    });
    if ($('input[name="presta_enable_group_selection_one"]:checked').val() == "1") {
        if ($('input[name="presta_enable_group_selection"]:checked').val() == "1") {
            $('.presta-select-group').show('slow');
            $('.presta-assign-groups').hide('slow');
        } else {
            $('.presta-select-group').hide('slow');
            $('.presta-assign-groups').show('slow');
        }
    } else {
        $('.presta-group-selection').hide();
        $('.presta-assign-groups').hide();
        $('.presta-select-group').hide();
    }

     // SELECT ALL STORE
    $("#presta_check_all_groups").click(function () {
        $('.checkbox').not(this).prop('checked', this.checked);
    });

    if ($('input[name="presta_send_email_notification_admin"]:checked').val() == "1") {
        $('.presta-admin-email-id').show();
    } else {
        $('.presta-admin-email-id').hide();
    }

    $(document).on('change', 'input[name="presta_send_email_notification_admin"]', function(){
        if ($(this).val() == "1") {
            $('.presta-admin-email-id').show('slow');
        } else if ($(this).val() == "0"){
            $('.presta-admin-email-id').hide('slow');
        }
    });

    //for enable auto approval

    if ($('input[name="presta_b2b_customer_auto_approval"]:checked').val() == "1") {
        $('.presta-pending-account-text').hide();
    } else {
        $('.presta-pending-account-text').show();
    }

    $(document).on('change', 'input[name="presta_b2b_customer_auto_approval"]', function(){
        if ($(this).val() == "1") {
            $('.presta-pending-account-text').hide('slow');
        } else if ($(this).val() == "0"){
            $('.presta-pending-account-text').show('slow');
        }
    });

    if ($('input[name="presta_enable_google_recaptcha"]:checked').val() == "1") {
        $('.presta-recaptcha-content').show();
    } else {
        $('.presta-recaptcha-content').hide();
    }

    $(document).on('change', 'input[name="presta_enable_google_recaptcha"]', function(){
        if ($(this).val() == "1") {
            $('.presta-recaptcha-content').show('slow');
        } else if ($(this).val() == "0"){
            $('.presta-recaptcha-content').hide('slow');
        }
    });

    if ($('select[name="presta_recaptcha_type"]').find(":selected").val() == 1) {
        $('.presta_v2_recaptcha').show();
        $('.presta_number_recaptcha').hide();
        $('.presta-site-key').show();
        $('.presta-secret-key').show();

    } else {
        $('.presta_v2_recaptcha').hide();
        $('.presta_number_recaptcha').show();
        $('.presta-site-key').hide();
        $('.presta-secret-key').hide();

    }

    $(document).on('change', 'select[name="presta_recaptcha_type"]', function(){
        if ($(this).val() == 1) {
            $('.presta_v2_recaptcha').show('slow');
            $('.presta_number_recaptcha').hide('slow');
            $('.presta-site-key').show('slow');
            $('.presta-secret-key').show('slow');

        } else {
            $('.presta_v2_recaptcha').hide('slow');
            $('.presta_number_recaptcha').show('slow');
            $('.presta-site-key').hide('slow');
            $('.presta-secret-key').hide('slow');
        }
    });

    // for address fields
    $(document).on('change', 'input[name="presta_address"]', function(){
        if ($(this).val() == "1") {
            $('.presta-vat-number').show('slow');
            $('.presta-address-comp').show('slow');
            $('.presta-phone').show('slow');
        } else if ($(this).val() == "0"){
            $('.presta-vat-number').hide('slow');
            $('.presta-address-comp').hide('slow');
            $('.presta-phone').hide('slow');

        }
    });
    if ($('input[name="presta_address"]:checked').val() == "1") {
        $('.presta-vat-number').show();
        $('.presta-address-comp').show();
        $('.presta-phone').show();
    } else {
        $('.presta-vat-number').hide();
        $('.presta-address-comp').hide();
        $('.presta-phone').hide();
    }

    $(document).on('change', 'input[name="presta_vat_number"]', function(){
        if ($(this).val() == "1") {
            $('.presta-required-vat').show('slow');
            $('.presta-vat-validation').show('slow');
        } else if ($(this).val() == "0"){
            $('.presta-required-vat').hide('slow');
            $('.presta-vat-validation').hide('slow');
        }
    });
    if ($('input[name="presta_vat_number"]:checked').val() == "1") {
        $('.presta-required-vat').show();
        $('.presta-vat-validation').show();
    } else {
        $('.presta-required-vat').hide();
        $('.presta-vat-validation').hide();
    }
})

// Message multiline
function changeLanguage(currentThis)
{
    let selectObj = $(currentThis);
    let idLang = selectObj.val();
    $('.presta_div').hide();
    $('.presta_current_div_'+idLang).show();
}
