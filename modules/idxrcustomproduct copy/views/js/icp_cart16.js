/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2018 Innova Deluxe SL
* @license   INNOVADELUXE
*/

$(document).ready(function() {
    $('.ajax_add_to_cart_button').each(function(i, obj) {
        var id_product = $(this).attr('data-id-product');
        var button = $(this);
        $.each(custom_products, function( index, value ) {
            var products = value['products'].split(",");
            if(jQuery.inArray(id_product, products) !== -1){
                button.before('<a class="configure_cart_button" href="'+value['link']+'"><span>'+configure_text+'</span></a>');
                button.remove();
            }
        });
    });
});
