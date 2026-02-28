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
$(document).ready(function(){
    if (presta_required_vat) {
        $('input[name="vat_number"]').parent('div').next('div').empty();
        $('input[name="vat_number"]').attr('required', true);
    }
    if (presta_enable_btob == '1') {
        if (presta_enable_address == '1' && presta_current_page != 'identity') {
            getStatesByIdCountry($('select[name="id_country"] option:selected').val());
            $(document).on('change', 'select[name="id_country"]', function(){
                getStatesByIdCountry($(this).val());
            });
        }
        if (presta_current_page == 'order') {
            $(document).on('click', '[data-link-action=show-login-form]', function(){
                $('.presta-btob-btns').hide('slow');
            });
            $(document).on('click', '[aria-controls=checkout-guest-form]', function(){
                $('.presta-btob-btns').show('slow');
            });
        }
        if ($('input[name="presta_btob_registration"]:checked').val() == 1) {
            $('input[name="siret"]').parent('div').next('div').empty();
            $('input[name="company"]').parent('div').next('div').empty();
            if (presta_required_vat) {
                $('input[name="vat_number"]').parent('div').next('div').empty();
            }
            $('input[name="address1"]').parent('div').next('div').empty();
            $('input[name="address2"]').parent('div').next('div').empty();
            $('input[name="city"]').parent('div').next('div').empty();
            $('input[name="postcode"]').parent('div').next('div').empty();
            $('input[name="phone"]').parent('div').next('div').empty();
            $('select[name="id_state"]').parent('div').next('div').empty();
            $('select[name="id_country"]').parent('div').next('div').empty();
            $('select[name="customer_group"]').parent('div').next('div').empty();
            $('input[name="presta_imgcaptcha"]').parent('div').next('div').empty();
            $('input[name="siret"]').attr('required', true);
            $('input[name="company"]').attr('required', true);
            if (presta_required_vat) {
                $('input[name="vat_number"]').attr('required', true);
            } else {
                $('input[name="vat_number"]').attr('required', false);
            }
            $('input[name="address1"]').attr('required', true);
            $('input[name="address2"]').attr('required', true);
            $('input[name="city"]').attr('required', true);
            $('input[name="postcode"]').attr('required', true);
            $('input[name="phone"]').attr('required', true);
            $('select[name="id_state"]').attr('required', true);
            $('select[name="id_country"]').attr('required', true);
            $('select[name="customer_group"]').attr('required', true);
            $('input[name="presta_imgcaptcha"]').attr('required', true);
        }

        $(document).on('change', 'input[name="presta_btob_registration"]', function(){
            if ($(this).val() == 1) {
                if (presta_hide_siret == '1') {
                    $('input[name="siret"]').parent('div').parent('div').show(1000);
                }
                $('input[name="company"]').parent('div').parent('div').show(1000);
                $('input[name="vat_number"]').parent('div').parent('div').show(1000);
                $('input[name="address1"]').parent('div').parent('div').show(1000);
                $('input[name="address2"]').parent('div').parent('div').show(1000);
                $('input[name="city"]').parent('div').parent('div').show(1000);
                $('input[name="postcode"]').parent('div').parent('div').show(1000);
                $('input[name="phone"]').parent('div').parent('div').show(1000);
                $('select[name="id_state"]').parent('div').parent('div').show(1000);
                $('select[name="id_country"]').parent('div').parent('div').show(1000);
                $('select[name="customer_group"]').parent('div').parent('div').show(1000);
                $('input[name="presta_imgcaptcha"]').parent('div').parent('div').show(1000);
                if (presta_hide_siret == '1') {
                    $('input[name="siret"]').parent('div').next('div').empty();
                }
                $('input[name="company"]').parent('div').next('div').empty();
                if (presta_required_vat) {
                    $('input[name="vat_number"]').parent('div').next('div').empty();
                }
                $('input[name="address1"]').parent('div').next('div').empty();
                $('input[name="address2"]').parent('div').next('div').empty();
                $('input[name="city"]').parent('div').next('div').empty();
                $('input[name="postcode"]').parent('div').next('div').empty();
                $('input[name="phone"]').parent('div').next('div').empty();
                $('select[name="id_state"]').parent('div').next('div').empty();
                $('select[name="id_country"]').parent('div').next('div').empty();
                $('select[name="customer_group"]').parent('div').next('div').empty();
                $('input[name="presta_imgcaptcha"]').parent('div').next('div').empty();
                if (presta_hide_siret == '1') {
                    $('input[name="siret"]').attr('required', true);
                }
                $('input[name="company"]').attr('required', true);
                if (presta_required_vat) {
                    $('input[name="vat_number"]').attr('required', true);
                } else {
                    $('input[name="vat_number"]').attr('required', false);
                }
                $('input[name="address1"]').attr('required', true);
                $('input[name="address2"]').attr('required', true);
                $('input[name="city"]').attr('required', true);
                $('input[name="postcode"]').attr('required', true);
                $('input[name="phone"]').attr('required', true);
                $('select[name="id_state"]').attr('required', true);
                $('select[name="id_country"]').attr('required', true);
                $('select[name="customer_group"]').attr('required', true);
                $('input[name="presta_imgcaptcha"]').attr('required', true);
                $('input[name="prestaBtoBRegistration"]').val('1');
                $.ajax({
                    url: presta_btob_process_url,
                    cache: false,
                    type : 'POST',
                    dataType: "json",
                    data: {
                        'ajax': true,
                        'action': 'displayCustomFeilds',
                    },
                    success: function(result) {
                        if (result.status == 'ok') {
                            $('#presta_custom_field').append(result.tpl);
                        } else if (result.status == 'ko') {
                        }
                    }
                });
            } else {
                $('input[name="prestaBtoBRegistration"]').val('');
                if (presta_hide_siret == '1') {
                    $('input[name="siret"]').parent('div').parent('div').hide(1000);
                }
                $('input[name="company"]').parent('div').parent('div').hide(1000);
                $('input[name="vat_number"]').parent('div').parent('div').hide(1000);
                $('input[name="address1"]').parent('div').parent('div').hide(1000);
                $('input[name="address2"]').parent('div').parent('div').hide(1000);
                $('input[name="city"]').parent('div').parent('div').hide(1000);
                $('input[name="postcode"]').parent('div').parent('div').hide(1000);
                $('input[name="phone"]').parent('div').parent('div').hide(1000);
                $('select[name="id_state"]').parent('div').parent('div').hide(1000);
                $('select[name="id_country"]').parent('div').parent('div').hide(1000);
                $('select[name="customer_group"]').parent('div').parent('div').hide(1000);
                $('input[name="presta_imgcaptcha"]').parent('div').parent('div').hide(1000);
                if (presta_hide_siret == '1') {
                    $('input[name="siret"]').attr('required', false);
                }
                $('input[name="company"]').attr('required', false);
                $('input[name="vat_number"]').attr('required', false);
                $('input[name="address1"]').attr('required', false);
                $('input[name="address2"]').attr('required', false);
                $('input[name="city"]').attr('required', false);
                $('input[name="postcode"]').attr('required', false);
                $('input[name="phone"]').attr('required', false);
                $('select[name="id_state"]').attr('required', false);
                $('select[name="id_country"]').attr('required', false);
                $('select[name="customer_group"]').attr('required', false);
                $('input[name="presta_imgcaptcha"]').attr('required', false);
                $('#presta_custom_field').empty('slow');

            }
        });

        $(document).on('click', '#presta_private_customer', function(){
            $('input[name="tobewebto_pec"]').parent('div').parent('div').hide(1000);
            $('input[name="tobewebto_sdicode"]').parent('div').parent('div').hide(1000);
            if (presta_hide_siret == '1') {
                $('input[name="siret"]').parent('div').parent('div').hide(1000);
            }
            $('input[name="company"]').parent('div').parent('div').hide(1000);
            $('input[name="vat_number"]').parent('div').parent('div').hide(1000);
            $('input[name="address1"]').parent('div').parent('div').hide(1000);
            $('input[name="address2"]').parent('div').parent('div').hide(1000);
            $('input[name="city"]').parent('div').parent('div').hide(1000);
            $('input[name="postcode"]').parent('div').parent('div').hide(1000);
            $('input[name="phone"]').parent('div').parent('div').hide(1000);
            $('select[name="id_state"]').parent('div').parent('div').hide(1000);
            $('select[name="id_country"]').parent('div').parent('div').hide(1000);
            $('select[name="customer_group"]').parent('div').parent('div').hide(1000);
            $('input[name="presta_imgcaptcha"]').parent('div').parent('div').hide(1000);
            $('input[name="presta_account_type"]').empty().val(1);
            if (presta_hide_siret == '1') {
                $('input[name="siret"]').attr('required', false);
            }
            $('input[name="company"]').attr('required', false);
            if (presta_required_vat) {
                $('input[name="vat_number"]').attr('required', true);
            } else {
                $('input[name="vat_number"]').attr('required', false);
            }
            $('input[name="address1"]').attr('required', false);
            $('input[name="address2"]').attr('required', false);
            $('input[name="city"]').attr('required', false);
            $('input[name="postcode"]').attr('required', false);
            $('input[name="phone"]').attr('required', false);
            $('select[name="id_state"]').attr('required', false);
            $('select[name="id_country"]').attr('required', false);
            $('select[name="customer_group"]').attr('required', false);
            $('input[name="presta_imgcaptcha"]').attr('required', false);
            $('input[name="tobewebto_pec"]').attr('required', false);
            $('input[name="tobewebto_sdicode"]').attr('required', false);
        });

        // custom field heading hide show
        if ($('input[name="presta_btob_registration"]:checked').val() == "1") {
            $('.custom-heading-feild').show();
            $('.personal-data-heading').show();
        } else {
            $('.custom-heading-feild').hide();
            $('.personal-data-heading').hide();
        }

        $(document).on('change', 'input[name="presta_btob_registration"]', function(){
            if ($(this).val() == "1") {
                $('.custom-heading-feild').show('slow');
                $('.personal-data-heading').show('slow');
            } else if ($(this).val() == "0"){
                $('.custom-heading-feild').hide('slow');
                $('.personal-data-heading').hide('slow');
            }
        });

        // hide dob
        if (presta_hide_dob == '0') {
            $('input[name="birthday"]').parent().parent().empty();
        }
        if (presta_hide_siret == '0') {
            $('input[name="siret"]').parent().parent().hide();
            $('input[name="siret"]').attr('required', false);

        }
        //registration heading tpl
        // Yes or No fetch dependant fields
        $(document).on('change', '.presta_yes_no input', function(){
            let currentInputVal = $(this).val();
            let fieldId = $(this).parents('.presta_yes_no').attr('data-id');
            let currentParentDiv = $(this).parents('.presta_field-dependant');
            getDependantField(currentParentDiv, currentInputVal, fieldId);
        });

        let currentInputVal = $('.presta_yes_no input:checked').val();
        let fieldId = $('.presta_yes_no input:checked').parents('.presta_yes_no').attr('data-id');
        let currentParentDiv = $('.presta_yes_no input:checked').parents('.presta_field-dependant');
        getDependantField(currentParentDiv, currentInputVal, fieldId);
        // End of code

        // Dropdown fetch dependant fields
        $(document).on('change', '.presta_dropDown_select select', function(){
            let currentInputVal = $(this).val();
            let fieldId = $(this).parents('.presta_dropDown_select').attr('data-id');
            let currentParentDiv = $(this).parents('.presta_field-dependant');
            getDependantField(currentParentDiv, currentInputVal, fieldId);
        });

        let currentSelectInputVal = $('.presta_dropDown_select select option:selected').val();
        let selectedFieldId = $('.presta_dropDown_select select option:selected').parents('.presta_dropDown_select').attr('data-id');
        let currentSelectParentDiv = $('.presta_dropDown_select select option:selected').parents('.presta_field-dependant');
        if (currentSelectInputVal !== 'undefined' && selectedFieldId !== 'undefined' && currentSelectParentDiv.length) {
            getDependantField(currentSelectParentDiv, currentSelectInputVal, selectedFieldId);
        }
        // End of code

        // Radio button fetch dependant fields
        $(document).on('change', '.presta_radio_select input', function(){
            let currentInputVal = $(this).val();
            let fieldId = $(this).parents('.presta_radio_select').attr('data-id');
            let currentParentDiv = $(this).parents('.presta_field-dependant');
            getDependantField(currentParentDiv, currentInputVal, fieldId);
        });

        let currentRadioInputVal = $('.presta_radio_select input:checked').val();
        let radioFieldId = $('.presta_radio_select input:checked').parents('.presta_radio_select').attr('data-id');
        let currentRadioParentDiv = $('.presta_radio_select input:checked').parents('.presta_field-dependant');
        if (currentRadioInputVal !== 'undefined' && radioFieldId !== 'undefined' && currentRadioParentDiv.length) {
            getDependantField(currentRadioInputVal, radioFieldId, currentRadioParentDiv);
        }
    }
});

function getStatesByIdCountry(idCountry)
{
    $.ajax({
        url: presta_btob_process_url,
        cache: false,
        type: 'POST',
        dataType: "json",
        data: {
            'ajax': true,
            'action': 'checkStates',
            'idCountry': idCountry
        },
        success: function(data) {
            if (data.state_req == 1) {
                option = '<option value="" disabled="" selected="">-- '+presta_please_choose+' --</option>';
                $.each(data.states, function(i, value) {
                    option += '<option value="'+value.id_state+'">'+value.name+'</option>';
                })
                $('select[name="id_state"]').empty().append(option);
                $('select[name="id_state"]').closest('.form-group').show();
                $('select[name="id_state"]').attr('required', 'required');
            } else {
                $('select[name="id_state"]').empty();
                $('select[name="id_state"]').removeAttr('required');
                $('select[name="id_state"]').closest('.form-group').hide();
            }
            if (data.dni_req == 1) {
                $('input[name="dni"]').closest('.form-group').show();
                $('input[name="dni"]').attr('required', 'required');
            } else {
                $('input[name="dni"]').closest('.form-group').hide();
                $('input[name="dni"]').removeAttr('required');
            }
        }
    });
}

function refreshCaptcha() {
    var img = document.images['presta_captcha_image'];
    img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}

function getDependantField(currentParentDiv, currentInputVal, fieldId)
{
    $.ajax({
        url: presta_btob_process_url,
        method: 'POST',
        dataType: 'json',
        cache: false,
        data : {
            ajax: true,
            action: 'getDependantField',
            currentInputVal: currentInputVal,
            fieldId: fieldId,
            // front_controller:tf_front_controller
        },
        success: function(result) {
            if ($('.customIdField-'+fieldId).length) {
                $('.customIdField-'+fieldId).remove();
            }
            if (result.status == 'ok') {
                currentParentDiv.after(result.tpl);
                $('span.presta_custom_errors').each(function(key, obj) {
                    let errorMsg = $(this).attr('data-error-value');
                    let errorId = $(this).attr('data-error');
                    $('p[class="'+errorId+'"').remove();
                    $('<p class="presta_custom_error_msg '+errorId+'" style="color:red;">'+errorMsg+'</p>').insertAfter('div[data-custom="'+errorId+'"]');
                });
            }
        }
    });
}

