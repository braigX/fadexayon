/**
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*/

$(document).ready(function() {
    $('#ed_shopping_footer').hide();
    setTimeout(
        function() {
            if ($('.cart_navigation').length > 0) {
                $('#ed_shopping_footer').insertBefore('.cart_navigation');
                $('#ed_shopping_footer').fadeIn(500);
            } else {
                $('#ed_shopping_footer').fadeIn(500);
            }
        },800
    );
});