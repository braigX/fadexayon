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

$(document).ready(function() {
    let init = false;
    $(document).on('click', '.quick-view, .quickview, .js-quick-view, .js-quick-view-iqit', function() {
        setTimeout(() => { ed_product_list_placement(80, 100); }, 500);
    });

    function ed_product_list_placement(tries, ms) {
        if (init) {
            return;
        }
        init = true;
        if ($('.quickview, .modal-quickview, .quick-view, .quickview-modal').not('a').length > 0) {
            ed_product_placement_init(80, 100);
            init = false;
        } else {
            if (tries > 0) {
                if (ms < 800) {
                    ms = ms * 1.05;
                }
                setTimeout(function () { ed_product_list_placement(tries-1, ms); }, ms);
            } else {
                console.log('ED: Couldn\'t place the box in the modal view`');
            }
        }
    }
});
