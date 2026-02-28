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
    if ($('#switch').is(':checked')) {
        $('#wkproductsamplesettingwrap').show();
    } else {
        $('#wkproductsamplesettingwrap').hide();
    }
    $('#switch').on('change', function() {
        if ($(this).is(':checked')) {
            $('#wkproductsamplesettingwrap').fadeIn();
        } else {
            $('#wkproductsamplesettingwrap').fadeOut();
        }
    });
    if ($('#form_hooks_wk_follow_global').is(':checked')) {
        $('#wkproductsamplesetting').hide();
    } else {
        $('#wkproductsamplesetting').show();
    }
    $('input[name="wk_follow_setting"]').on('change', function() {
        if ($('#form_hooks_wk_follow_global').is(':checked')) {
            $('#wkproductsamplesetting').fadeOut();
        } else {
            $('#wkproductsamplesetting').fadeIn();
        }
    });
    displayPriceTypeVal($("#wk_sample_price_type").val());
    $(document).on("change", "#wk_sample_price_type", function(){
        var id_type = $(this).val();
        displayPriceTypeVal(id_type);
    });
    if (!$('#wk_sp_virtual_switch').is(":checked")) {
        $("#wk_sp_file_input, #wk_sp_file_delete").fadeOut();
    }
    $('#wk_sp_virtual_switch').on('change', function() {
        if ($('#wk_sp_virtual_switch').is(":checked")) {
            $("#wk_sp_file_input, #wk_sp_file_delete").fadeIn();
        } else {
            $("#wk_sp_file_input, #wk_sp_file_delete").fadeOut();
        }
    });
    const manageSampleVirtualFileField = (show = true) => {
        if (show && $('#form_step3_virtual_product_file_details.show').length > 0) {
            // virtual file exists
            $('#wk_sample_file_input_block').removeClass('d-none');
            $('#form_hooks_uploaded_sample_file').parent().parent().fadeIn();
        } else {
            // no virtual file.
            $('#wk_sample_file_input_block').addClass('d-none');
            $('#form_hooks_uploaded_sample_file').parent().parent().fadeOut();
        }
    }
    $('#form_step1_type_product').on('change', function() {
        manageSampleVirtualFileField($(this).val() == 2);
    });
    $('.js-btn-save, #submit').on('click', function() {
        if ($('#tab_hooks a').attr('aria-selected') && ($('#module_wksampleproduct').css('display') == 'block') && ($('#form_step1_type_product').val() == 2)) {
            if ($('#wk_sp_virtual_switch').is(":checked")) {
                uploadLSampleImage();
            } else {
                productId = $('#form_hooks_uploaded_sample_file').attr('data-id');
                deleteSampleImage(productId);
            }
        }
    });
    $('#form_hooks_delete_sample_file').on('click', function() {
        productId = $(this).attr('data-id');
        deleteSampleImage(productId);
    });
    function uploadLSampleImage()
    {
        image = $("#form_hooks_uploaded_sample_file")[0].files[0];
        var myFormData = new FormData();
        var idProduct = $('#form_hooks_uploaded_sample_file').attr('data-id');
        myFormData.append('pictureFile', image);
        myFormData.append('action', "saveSampleFile");
        myFormData.append('id_product', idProduct);

        if (typeof image != "undefined") {
            if (image.size/1000000 > maxFileSizeInPs) {
                animateStatusBar('error', maxSizeErrorMsg);
                alert(maxSizeErrorMsg);
            } else {
                $.ajax({
                    cache: false,
                    async: false,
                    type: "POST",
                    url: saveSample,
                    dataType: "json",
                    processData: false, // important
                    contentType: false,
                    data: myFormData,
                    success: function(result) {
                        if (result.success == 1) {
                            $('#form_hooks_delete_sample_file').fadeIn();
                            $('#form_hooks_uploaded_sample_file').attr('data-set', '1');
                            $('#form_hooks_uploaded_sample_file').val('');
                            animateStatusBar("success", uploadSuccessMsg+': '+result.name);
                        } else {
                            $('#form_hooks_uploaded_sample_file').attr('data-set', '0');
                            animateStatusBar("error", result.text);
                        }
                    }
                });
            }
        } else if ($('#form_hooks_delete_sample_file').css('display') === 'none') {
            if ($('#switch').is(":checked")) {
                animateStatusBar('error', noSampleErrorMsg);
                alert(noSampleErrorMsg);
            }
        }
    }

    function animateStatusBar(type, msg)
    {
        if (type == 'success') {
            $('#wk_sp_sample_file_status').removeClass('text-muted text-danger');
            $('#wk_sp_sample_file_status').addClass('text-success');
            $('#wk_sp_sample_file_status').animate({backgroundColor:'green'}, 300);
        } else {
            $('#wk_sp_sample_file_status').removeClass('text-muted text-success');
            $('#wk_sp_sample_file_status').addClass('text-danger');
            $('#wk_sp_sample_file_status').animate({backgroundColor:'red'}, 300);
        }
        $('#wk_sp_sample_file_status em').text(msg);
        $('#wk_sp_sample_file_status').animate({backgroundColor:'transparent'}, 300);
    }

    function deleteSampleImage(idProduct)
    {
        $.ajax({
            type: "POST",
            url: saveSample,
            dataType: "json",
            data: {
                action: 'deleteSample',
                'id_product': idProduct,
            },
            success: function() {
                $('#wk_sp_sample_file_status').attr('style','display:none');
                $('#form_hooks_delete_sample_file').fadeOut();
                $('#form_hooks_uploaded_sample_file').val('');
                if ($('#form_hooks_uploaded_sample_file').attr('data-set') == 1) {
                    $('#wk_sp_sample_file_status').attr('style','display:block');
                    animateStatusBar("success", deleteSuccessMsg);
                    $('#form_hooks_uploaded_sample_file').attr('data-set', 0);
                }
            }
        });
    }

    $('.wk_sample_bulk_carriers').on('change', function (e) {
        if ($(this).attr('id') == 'wk_sample_carriers_0') {
            if ($(this).is(':checked')) {
                $('.wk_sample_bulk_carriers:not(#wk_sample_carriers_0)').prop('checked', true);
            } else {
                $('.wk_sample_bulk_carriers:not(#wk_sample_carriers_0)').prop('checked', false);
            }
        } else if (!$(this).is(':checked')) {
            // $('#wk_sample_carriers_0').prop('checked', false);
        }
    })

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.url && settings.url.match('catalog/products/virtual')) {
            manageSampleVirtualFileField();
        }
        console.log(settings.url);
    })
});

function displayPriceTypeVal(id_type)
{
    if (id_type == 2) {
        $(".wk_sample_amount").show();
        $("#wk_sample_sign").show();
        $("#wk_sample_sign_label").show();
        $("#wk_sample_percent").hide();
        $("#wk_sample_percent_label").hide();
        $(".wk_sample_custom_price").hide();
        $(".wk_sample_price_tax").show();
    } else if (id_type == 3) {
        $(".wk_sample_amount").show();
        $("#wk_sample_sign").hide();
        $("#wk_sample_sign_label").hide();
        $("#wk_sample_percent").show();
        $("#wk_sample_percent_label").show();
        $(".wk_sample_custom_price").hide();
        $(".wk_sample_price_tax").hide();
    } else if (id_type == 4) {
        $(".wk_sample_custom_price").show();
        $(".wk_sample_amount").hide();
        $(".wk_sample_price_tax").show();
    } else if (id_type == 1) {
        $(".wk_sample_amount").hide();
        $(".wk_sample_custom_price").hide();
        $(".wk_sample_price_tax").hide();
    } else {
        $(".wk_sample_amount").hide();
        $(".wk_sample_custom_price").hide();
        $(".wk_sample_price_tax").hide();
    }
}

