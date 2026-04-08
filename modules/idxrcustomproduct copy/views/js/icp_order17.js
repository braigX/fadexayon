/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL
* @license   INNOVADELUXE
*/

$(document).ready(function() {

    loadInfo();
    prestashop.on('updateCart', function () {
        setTimeout(
            function() 
            {
                loadInfo();
            }, 700);
    });
    $(document).on('opc-load-review:completed', function () {
        loadInfo();
    });

});

function loadInfo()
{
    extraInfo = JSON.parse(icp_extrainfo);
    $('.product-line-grid').each(function(){
        var grid = $(this);
        var product_id = $(this).find('.js-cart-line-product-quantity').data('product-id');
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                if (product.extra_info) {
                    grid.find('.input-group-btn-vertical').hide();
                    grid.find('.js-cart-line-product-quantity').prop('disabled', true);
                    grid.find('.js-cart-line-product-quantity').prop('title', icp_qtyblock_text);
                    
                }
                grid.find('.product-line-info').first().after(product.customization.replace('\\', ''));
                if (product.original_url) {
                    grid.find(".product-line-info a").attr("href", product.original_url)
                } else {
                    grid.find(".product-line-info a").replaceWith(function(){
                        return $("<span>" + $(this).html() + "</span>");
                    });
                }
                //grid.find('.product-line-info.product-price').remove();

                //Theme ZoneTheme
                grid.find('.product-line-info-wrapper').first().append(product.customization.replace('\\', ''));
                $(".product-name a").replaceWith(function(){
                    return $("<span>" + $(this).html() + "</span>");
                });
                //grid.find('.product-prices').remove();
                grid.find('.cart-line-product-actions').prepend(product.edit_button);
            }
        });
    });
    
    //Compatibilidad onepagechekoutps
    $('#order-detail-content .cart_item').each(function(){
        var grid = $(this);
        var product_id = $(this).find('.cart-line-product-quantity').data('product-id');
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                grid.find('.s_title_block').first().after(product.customization.replace('\\', ''));
                $(".s_title_block a").replaceWith(function(){
                    return $("<span>" + $(this).html() + "</span>");
                });
            }
        });
    });
    
    //Compatibilidad theme akira
    $('#canvas-mini-cart .block-shopping-cart .cart-item-product').each(function(){
        var product_id = $(this).find('.remove-from-cart').data('id-product');
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                //grid.find('.product-name').after(product.customization.replace('\\', ''));
                $(".product-name a").attr("href", product.original_url);
            }
        });
    });
    
    $('#cart-summary-product-list .media').each(function(){
        var grid = $(this);
        var image_url = $(this).find('.img-loader').attr('data-src');
        extraInfo.forEach(function(product){
            if(image_url.indexOf(product.product_image_id) > -1){
                grid.find('.product-name').after(product.customization.replace('\\', ''));
                $(".product-name").attr("href", product.original_url);
            }
        });
    });
    
    $('.cart-overview .cart-item').each(function(){
        var grid = $(this);
        var product_id = $(this).find('.js-cart-line-product-quantity').data('id-product');
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                grid.find('.product-name').after(product.customization.replace('\\', ''));
                $(".product-name a").replaceWith(function(){
                    return $("<span>" + $(this).html() + "</span>");
                });
            }
        });
    });
}
