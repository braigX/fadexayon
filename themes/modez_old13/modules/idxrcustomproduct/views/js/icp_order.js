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
    extraInfo = jQuery.parseJSON(icp_extrainfo);    
    extraInfo.forEach(function(product){
        $('tr[id^="product_' + product.id_product +'"]').each(function(){
            $(this).children().each(function(){
                if($(this).hasClass('cart_product')){
                    $(this).find('a').attr('href','#');
                }
                if($(this).hasClass('cart_description')){
                    $(this).find('a').attr('href','#');
                    $(this).append(product.customization.replace('\\', ''));
                }
            });
        });
    });
});
