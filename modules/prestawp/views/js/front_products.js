/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */

$(function () {
    // for compatibility with third-party modules
    window.show_popup = 0;

    // Open links in parent window
    const $pswp_page = $('#module-prestawp-products');
    // open all links in the parent window
    $pswp_page.find('a').attr('target', '_parent');
    // open js links in a new tab
    $pswp_page.find('[data-ob]').addClass('blank');

    if ($('#body_module-prestawp-products').length) {
        $('#body_module-prestawp-products').find('a').attr('target', '_parent');
    }
    // Disable ajax adding to cart
    setTimeout(function () {
        // for compatibility with third-party modules
        window.show_popup = 0;
        $('.ajax_add_to_cart_button').unbind('click');
        $(document).off('click', '.ajax_add_to_cart_button');
        $('body').off('click', '[data-button-action="add-to-cart"]');
        var $forms = $('.add-to-cart-or-refresh, .product-add-cart form, .an_productattributesForm');
        $forms = $forms.add($('.add-to-cart').closest('form'));
        $forms.attr('target', '_parent');
        $forms.each(function () {
            if (!$(this).find('[name="add"]').length) {
                $(this).append('<input type="hidden" name="add" value="1" />');
            }
            if (!$(this).find('[name="action"]').length) {
                $(this).append('<input type="hidden" name="action" value="update" />');
            }
        });
    }, 500);

    // make sure to submit the add-to-cart form even by default it's turned off
    $('[data-button-action="add-to-cart"]').on('click', function() {
        $(this).closest('form').submit();
        return false;
    })
});
