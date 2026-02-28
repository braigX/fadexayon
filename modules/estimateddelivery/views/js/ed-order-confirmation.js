/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*/

$(document).ready(function()
{
    if ($('#center_column .cart_navigation').length > 0) {
        $('.estimateddelivery-order').insertBefore($('#center_column .cart_navigation'));
    } else if ($('#center_column').length > 0) {
        $('.estimateddelivery-order').append($('#center_column'));
    }
});