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
var availableQuantity = 0;
var standardInCart = 0;
var maxSampleQty = 0;
var addedStandard = 0;
var showStockWarning = 0;
$(document).ready(function(){
    prestashop.on("updatedProduct", function(resp) {
        $('#add-to-cart-or-refresh').parent().find('.wk-sample-block').remove();
        $('#add-to-cart-or-refresh').after($('.wk-sample-block'));
        var idProd = $('#wksamplebuybtn').attr('data-id-product');
        if (!isNaN(parseInt(idProd)) && (parseInt(idProd) > 0)) {
            getSampleMaxQuantity(idProd, resp.id_product_attribute);
        }
    });
    prestashop.on('clickQuickView', function(resp) {
        if (resp.dataset && resp.dataset.idProduct) {
            getSampleMaxQuantity(resp.dataset.idProduct, resp.dataset.idProductAttribute, false);
        }
    });

    $(document).on('show.bs.modal', '.modal.quickview', function() {
        $('#add-to-cart-or-refresh').after($('.wk-sample-block'));
        updateSampleTemplate()
    });
});
const setSampleQuantityInput = (val) => {
    $('#wkquantity_wanted').val(val);
    if ((maxSampleQty > 0) && ((val) > availableQuantity)) {
        $('#wksampleproductqty_spinerror').fadeIn();
        $("#wksamplebuybtn").prop('disabled', true);
    } else {
        $('#wksampleproductqty_spinerror').fadeOut();
        $("#wksamplebuybtn").removeAttr('disabled');
    }
}
$(document).off('click', '.wkbootstrap-touchspin-down').on('click', '.wkbootstrap-touchspin-down', function() {
    var val = parseInt($('#wkquantity_wanted').val());
    if (isNaN(val) || (val < 0)) {
        val = 2;
    }
    if (val > 1) {
        val -= 1;
        setSampleQuantityInput(val);
    }
});

$(document).off('click', '.wkbootstrap-touchspin-up').on('click', '.wkbootstrap-touchspin-up', function() {
    var val = parseInt($('#wkquantity_wanted').val());
    if (isNaN(val) || (val < 0)) {
        val = 0;
    }
    val += 1;
    setSampleQuantityInput(val);
});
$(document).on('blur', '#wkquantity_wanted', function() {
    var val = parseInt($('#wkquantity_wanted').val());
    if (isNaN(val) || (val < 0)) {
        val = 1;
    }
    setSampleQuantityInput(val);
});
$(document).off('click', '#wksamplebuybtn').on('click', '#wksamplebuybtn', function(e) {
    e.preventDefault();
    $('#wk_sp_ajax_error_wrap').fadeOut();
    var prod_qty = (isNaN(parseInt($('#wkquantity_wanted').val())) || (parseInt($('#wkquantity_wanted').val()) < 0)) ? 0: parseInt($('#wkquantity_wanted').val());
    $('.sample_ajax_errors').remove();
    $("#wksamplebuybtn").prop('disabled', true);
    if ((maxSampleQty > 0) && (prod_qty > availableQuantity)) {
        $('#wksampleproductqty_spinerror').fadeIn();
    } else {
        $('#wksampleproductqty_spinerror').fadeOut();
        var id_prod = $(this).attr('data-id-product');
        var id_attr = $(this).attr('data-id-product-attr');
        $('#'+id_prod+'-'+id_attr+'-loader').removeClass();
        if (id_prod) {
            var formData = $('#add-to-cart-or-refresh').serializeArray();
            $.each(formData, function(idx, dataItem) {
                if (dataItem.name == 'qty') {
                    formData[idx].value = prod_qty;
                }
            });
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: sampleSpecificPriceURL+'?'+$.param(formData)+'?rand=' + new Date().getTime(),
                async: false,
                cache: false,
                dataType: "json",
                data: {},
                success: function(response) {
                    $('#'+id_prod+'-'+id_attr+'-loader').addClass('customloader');
                    if (response.status == 'ok') {
                        formData.push({
                            name: "action",
                            value: "update"
                        });
                        formData.push({
                            name: "add",
                            value: 1
                        });
                        $.post(sampleCartActionUrl, $.param(formData), null, 'json').then(function (resp) {
                            $(".product-add-to-cart, .product-prices").hide();
                            getSampleMaxQuantity(resp.id_product, resp.id_product_attribute);
                            $("#wksamplebuybtn").prop('disabled', true);
                            prestashop.emit('updateCart', {
                                reason: {
                                    idProduct: resp.id_product,
                                    idCustomization: resp.id_customization,
                                    idProductAttribute: resp.id_product_attribute,
                                    cart: resp.cart,
                                    linkAction: 'add-to-cart',
                                    isSample: true
                                },
                                resp: resp
                            });
                        });
                    } else {
                        $('#wk_sp_ajax_error').text(response.msg);
                        $('#wk_sp_ajax_error_wrap').fadeIn();
                    }
                },
            });
        }
    }
});
function updateSampleTemplate()
{
    var prod_qty = $('#wkquantity_wanted').val();
    $('div.alert').hide();
    if (addedStandard) {
        $('.wk_spqv_standard_product_error').fadeIn();
        $('.wk-sample-block .product-quantity').hide();
    } else {
        if ((maxSampleQty > 0) && (prod_qty > availableQuantity)) {
            $('#wksampleproductqty_stockerror').fadeIn();
            $('#wksampleproductqty_spinerror').fadeOut();
            $("#wksamplebuybtn").prop('disabled', true);
        } else if (showStockWarning) {
            $('#wksampleproductqty_spinerror').fadeIn();
            $('#wksampleproductqty_stockerror').fadeOut();
            $("#wksamplebuybtn").prop('disabled', false);
        }
        $('.wk-sample-block .product-quantity').show();
        $('#wk_sp_standard_product_error').hide();
    }
    if (typeof standardInCart != "undefined" && (standardInCart == 1)) {
        $(".product-add-to-cart, .product-prices").hide();
    } else {
        $(".product-add-to-cart, .product-prices").show();
    }
}
function getSampleMaxQuantity(id_product, id_attr, updateView = true)
{
    $.ajax({
        type: 'POST',
        headers: {
            "cache-control": "no-cache"
        },
        url: sampleSpecificPriceURL,
        async: false,
        cache: false,
        dataType: "json",
        data: {
            ajax: true,
            action: 'getSampleInfo',
            attribute_id: id_attr,
            id_product: id_product
        },
        success: function(result) {
            standardInCart = result.sampleInCart;
            addedStandard = result.addedStandard;
            availableQuantity = result.availableQuantity;
            maxSampleQty = result.maxSampleQty;
            showStockWarning = result.showStockWarning;

            if (updateView) {
                updateSampleTemplate()
            }
            return true;
        }
    });
    return 0;
}
