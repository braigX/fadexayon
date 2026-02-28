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

function validateIfNoProductSelected()
{
    if (($('[name="searchedProduct[]"]').length == 0) || (typeof $('[name="searchedProduct[]"]:checked').val() == 'undefined')) {
        event.preventDefault();
        event.stopPropagation();
        return $.growl.error({
            title: "",
            size: "large",
            message: errorMsg.productRequired
        });
    }
}

function validateFilters(event) {
    var idCategories = [];
    $.each($('[name="wk_id_categories[]"]:checked'), function () {
        idCategories.push($(this).val());
    });
    if ($('#search_product_name').val().trim() != "") {
        if (!$("#search_product_name").val().match(/^[^<>;=#{}]*$/)) {
            event.preventDefault();
            event.stopPropagation();
            return $.growl.error({
                title: "",
                size: "large",
                message: errorMsg['patternInvalid']
            });
        }
        if ($("#search_product_name").val().trim().length < 3) {
            event.preventDefault();
            event.stopPropagation();
            return $.growl.error({
                title: "",
                size: "large",
                message: errorMsg['patternLength']
            });
        }
    }
    if ($('#search_product_name').val().trim() == "" &&
        idCategories.length == 0 &&
        $('[name="id_manufacturers[]"]').val() == null &&
        $('[name="id_suppliers[]"]').val() == null) {
        event.preventDefault();
        event.stopPropagation();
        return $.growl.error({
            title: "",
            size: "large",
            message: errorMsg['oneFilterRequired']
        });
    }

    return true;
}

$(document).ready(function(){
    $('[name^="id_manufacturers"]').removeClass('fixed-width-xl');
    $('[name^="id_manufacturers"]').addClass('fixed-width-xxxl');
    $('[name^="id_manufacturers"]').chosen({
        placeholder_text: chosenPlaceholder,
        no_results_text: noMatchFound
    });
    $('[name^="id_suppliers"]').removeClass('fixed-width-xl');
    $('[name^="id_suppliers"]').addClass('fixed-width-xxxl');
    $('[name^="id_suppliers"]').chosen({
        placeholder_text: chosenPlaceholder,
        no_results_text: noMatchFound
    });

    tinySetup({
        editor_selector: "wk_autoload_rte"
    });
    $('form#wk-filtered-products').on('submit', function(e){
        if ($(document.activeElement).attr('id') != 'submitSampleSettingsBulk') {
            e.preventDefault();
            $('#wk_src_btn').trigger('click');
        } else {
            validateIfNoProductSelected();
        }
    });
    $('.wk-search-product-loader').hide();
    // checking checkbox when products row is clicked
    $(document).on('mouseover', '.wk-checked-products-row', function(){
        $(this).css('cursor', 'pointer');
    });
    $(document).on('click', '.wk-checked-products-row', function(){
        $(this).closest('tr').find('.wk-checked-products').trigger('click');
    });

    $('.wk-product-search').find('label:first').
        css({
            'text-align':'left',
            'margin-bottom':'3px',
            'font-weight':'bold'
        }).addClass('col-lg-12');
    $('.wk_search_button').closest('div .col-lg-offset-3').removeClass('col-lg-offset-3');
    $('.wk-product-search').find('.chosen-container').css('width', '100%');

    $('#wk_src_btn').on('click', function (event) {
        if (validateFilters(event) === true) {
            srcPattern = $('#search_product_name').val();
            var idCategories = [];
            $.each($('[name="wk_id_categories[]"]:checked'), function () {
                idCategories.push($(this).val());
            });
            idSuppliers = $('[name="id_suppliers[]"]').val();
            idManufacturers = $('[name="id_manufacturers[]"]').val();
            $.ajax({
                url: wkSampleFilterProduct,
                type: 'POST',
                cache: false,
                data: {
                    ajax: true,
                    action: 'displayFilteredProducts',
                    srcPattern: srcPattern,
                    idCategories: idCategories,
                    idSuppliers: idSuppliers,
                    idManufacturers: idManufacturers
                },
                beforeSend: function () {
                    $('.wk-search-product-loader').show();
                    $('#wk-product-search-info').hide();
                },
                success: function (response) {
                    $('.wk-search-product-loader').hide();
                    $('#wk-filtered-list').html(response);
                    if (response) {
                        $('#wk-product-search-info').hide();
                        $('#addNewProductButton').show();
                        $('#wk-no-product-found').hide();
                    } else {
                        $('#addNewProductButton').hide();
                        $('#wk-no-product-found').show().text(errorMsg.productNotFound);
                        $('#wk-product-search-info').hide();
                    }
                },
            });
        }
    });
    configureGlobalSamplePrice($('#wk_sample_price_type').val());
    $('#wk_sample_price_type').on('change', function() {
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

    $('.wk_sample_bulk_carriers').on('change', function (e) {
        e.stopPropagation();
        if ($(this).attr('id') == 'wk_sample_carriers_0') {
            if ($(this).is(':checked')) {
                $('.wk_sample_bulk_carriers:not(#wk_sample_carriers_0)').prop('checked', true);
            } else {
                $('.wk_sample_bulk_carriers:not(#wk_sample_carriers_0)').prop('checked', false);
            }
        } else if (!$(this).is(':checked')) {
            $('#wk_sample_carriers_0').prop('checked', false);
        }
    })
});
