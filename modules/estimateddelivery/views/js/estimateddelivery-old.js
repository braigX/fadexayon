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

jQuery(document).ready(function($) {
    if ((typeof ed_disable_cc == 'undefined' || ed_disable_cc == 0) && ed_sm == 1) {
        /* Combinations Check */
        setTimeout(function() { chooseEstimated(1,400) }, 800);

        $('#attributes a, #attributes input').click(function() {
            setTimeout(function() { chooseEstimated(10,150) }, 800);
        });
        $('#attributes select').change(function() {
            setTimeout(function() { chooseEstimated(10,150) }, 800);
        });
    }
    var ccombi = {};
    function chooseEstimated(left_checks, ms) {
        //console.log('Start Choose Estimated ('+left_checks+', '+ms+')');
        var changed = false;
        var edProductBlock = '';
        if ($("#add_to_cart").parents('#estimateddelivery').length > 0) {
            edProductBlock = $("#add_to_cart").parents('#estimateddelivery').first();
        } else {
            if ($('.primary-block #estimateddelivery').length > 0) {
                edProductBlock = $('.primary-block #estimateddelivery').first()
            } else {
                edProductBlock = $('#estimateddelivery').first()
            }
        }
        id_prod = edProductBlock.data('idprod');
        //console.log('id Prod: ' + id_prod);
        var ed_stock = edProductBlock.find(".ed_stock");
        var ed_oos_days = edProductBlock.find(".ed_oos_days_add");
        if ($("#add_to_cart, a.ajax_add_to_cart_button").is(':visible')) { //Customization: added the selector a.ajax_add_to_cart_button
            if (typeof combi !== 'undefined' && typeof combi[id_prod] !== 'undefined') {
                ccombi = combi[id_prod];
                //console.log(ccombi[$("#idCombination").val()]);
                //console.log(ed_stock);
                if (ccombi[$("#idCombination").val()] > 0) {
                    if (!ed_stock.is(':visible')) {
                        if (ed_oos_days.length > 0) {
                            ed_oos_days.fadeOut(350,function() {ed_stock.fadeIn(350) });
                            changed = true;
                        } else {
                            ed_stock.fadeIn(350);
                            changed = true;
                        }
                    }
                } else {
                    if (!ed_oos_days.is(':visible')) {
                        ed_stock.fadeOut(350,function() {
                            if (ed_oos_days.length > 0) {
                                ed_oos_days.fadeIn(350);
                            }
                        });
                        changed = true;
                    } else {
                        ed_oos_days.fadeIn(350);
                        changed = true;
                    }
                    
                }
            }
        } else {
            if (typeof combi != 'undefined') {
                ed_stock.fadeOut(350);
                ed_oos_days.fadeOut(350);
            } else if (ccombi[$("#idCombination").val()] <= 0 && typeof ed_disable_cc == 'undefined' || ed_disable_cc == 0) {
                if (ed_stock.is(':visible')) {
                    ed_stock.fadeOut(350);
                    changed = true;
                }
                if (ed_oos_days.length > 0 && ed_oos_days.is(':visible')) {
                    ed_oos_days.fadeOut(350);
                    changed = true;
                }
            } else {
                if (left_checks > 0) {
                    //console.log("ED Recursive check: "+left_checks);
                    //ms *= 1.15;
                    setTimeout(function() { chooseEstimated(left_checks-1, ms) }, ms);
                }
            }
        }
        /*if (left_checks > 0 && changed == false) {
            ms *= 1.15;
            console.log('recu2');
            //console.log("ED Recursive check: "+left_checks);
            setTimeout(function() { chooseEstimated(left_checks-1, ms) }, ms);
        }*/
    }
    function getCurrentCombination() {
        if ($("#idCombination").length > 0) {
            return $("#idCombination").val();
        }
    }
});