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

$(document).ready(function() {
    const correctCartView = () => {
        if (typeof samplesInCart != "undefined") {
            $.each(samplesInCart, function(index, item) {
                var ids = index.split('_');
                var id_prod = ids[0];
                var id_prod_attr = ids[1];

                $('.cart-line-product-actions a.remove-from-cart[data-id-product='+id_prod+'][data-id-product-attribute='+id_prod_attr+']').next().css('display', 'inline-block');
                $('.cart-line-product-actions a.remove-from-cart[data-id-product='+id_prod+'][data-id-product-attribute='+id_prod_attr+']').parent().parent().parent().parent().prev().find('.product-discount').remove();
                $('.cart-line-product-actions a.remove-from-cart[data-id-product='+id_prod+'][data-id-product-attribute='+id_prod_attr+']').parent().parent().parent().parent().prev().find('.product-line-info').removeClass('has-discount');
            });
        }
    }
    correctCartView(true);
    prestashop.on('updatedCart', function(res) {
        correctCartView();
    });
});