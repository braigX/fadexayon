/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2023 Innova Deluxe SL
* @license   INNOVADELUXE
*/

$(document).ready(function() {
    extraInfo = JSON.parse(icp_extrainfo);
    $('#orderProductsTable .cellProduct').each(function(){
        var grid = $(this);
        var product_id = $(this).find('.js-order-product-edit-btn').data('product-id');
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                grid.find('.cellProductName').append(product.customization.replace('\\', ''));
            }
        });
    });
    
    $('#orderProducts .product-line-row').each(function(){
        var grid = $(this);
        var product_id = $(this).find('a:first').attr('href').split('products/')[1].split('?')[0];
        extraInfo.forEach(function(product){
            if(product.id_product == product_id){
                grid.find('.productName').parent().append(product.customization.replace('\\', ''));
            }
        });
    });
    
});