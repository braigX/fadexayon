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
    // Add Multiple Select
    $('.rf-multiple-add-button').click(function(){
        $.ajax({
            url: presta_admin_rf_multiple_value,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                ajax: true,
                action: 'prestaAddMultipleValueFields',
            },
            success: function(data) {
                if (data.status == 'ok') {
                    $(data.tpl).insertAfter($('.presta-select-url-content').last());
                }
            }
        });
    });

    $('.ps-select-form-wrapper').on('click', '.multiple_delete', function(e){
        e.preventDefault();
        $(this).parents('.select_urls').remove();
    });

    // Field Type
    if ($('select[name="field_type"] option:selected').val() == 'text') {
        $('.presta_rf_text_field').show();
        $('.presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'date') {
        $('.presta_ps_date').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'multiSelect') {
        $('.presta_rf_multiple_selected').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'dropdown') {
        $('.presta_rf_multiple_selected').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'checkbox') {
        $('.presta_rf_multiple_selected').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'radio') {
        $('.presta_rf_multiple_selected').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'file') {
        $('.presta_image_types').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_message_types').hide('slow');
    } else if($('select[name="field_type"] option:selected').val() == 'message') {
        $('.presta_message_types').show('slow');
        $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_image_types, .presta_rf_is_required').hide('slow');
    }

    $(document).on('change', 'select[name="field_type"]', function(){
        // $('.presta_rf_multiple_content').empty();
        if ($(this).val() == 'text') {
            $('.presta_rf_text_field').show('slow');
            $('.presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'textarea') {
            $('.presta_rf_text_field').show('slow');
            $('.presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'date') {
            $('.presta_ps_date').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'yes/no') {
            $('.presta_ps_date').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_rf_multiple_selected, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'multiSelect') {
            $('.presta_rf_multiple_selected').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'dropdown') {
            $('.presta_rf_multiple_selected').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'checkbox') {
            $('.presta_rf_multiple_selected').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'radio') {
            $('.presta_rf_multiple_selected').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_image_types, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'file') {
            $('.presta_image_types').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_message_types').hide('slow');
        } else if ($(this).val() == 'message') {
            $('.presta_message_types').show('slow');
            $('.presta_rf_text_field, .presta_ps_text_area, .presta_ps_date, .presta_rf_multiple_selected, .presta_image_types, .presta_rf_is_required').hide('slow');
        }
    });

    // Dependant Fields
    if ($('input[name="PRESTA_DEPENDANT_FIELDS"]:checked').val() == 1) {
        $('.presta_rf_dependant_fields').show('slow');
        $('.presta_select_value_dependant').show('slow');
    } else {
        $('.presta_rf_dependant_fields').hide('slow');
        $('.presta_select_value_dependant').hide('slow');
    }


    $(document).on('change', 'input[name="PRESTA_DEPENDANT_FIELDS"]', function(){
        if ($(this).val() == 1) {
            $('.presta_rf_dependant_fields').show('slow');
            $('.presta_select_value_dependant').show('slow');
        } else {
            $('.presta_rf_dependant_fields').hide('slow');
            $('.presta_select_value_dependant').hide('slow');
        }
    });

    $(document).on('change', 'select[name="presta_dependant_field"]', function(){
        getDependantValue($(this).val());
    });
    getDependantValue($('select[name="presta_dependant_field"] option:selected').val());
});

// Message multiline
function changeLanguage(currentThis)
{
    let selectObj = $(currentThis);
    let idLang = selectObj.val();
    $('.presta_registration_main_div').hide();
    $('.presta_current_registration_fields_div_'+idLang).show();
}

function getDependantValue(id_dependant)
{
    $.ajax({
        url: presta_admin_dependant_link,
        method: 'POST',
        dataType: 'json',
        cache: false,
        data: {
            ajax: true,
            action: 'getDepandantValue',
            id: id_dependant,
            currentId: currentId
        },
        success: function(data) {
            $('#presta_dependant_field').empty();
            if (data.status == 'ok') {
                $('#presta_dependant_field').append(data.tpl)
            }
        }
    });
}


