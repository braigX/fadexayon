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
var dynamic_refresh = false;
function reverseString(str) {
    return str.split("").reverse().join("");
}
jQuery(document).ready(function($) {
    let curr_ele = '';
    let t = 0;
    init_do(8, 200);
    setTimeout( function() {init_submit_refresh(); }, 800);
    // Some One page Checkouts Modules reload the code after a few milliseconds, check if the ED needs to be repopulated
    setTimeout( function() { init_do(8, 200); }, 3000);
    function restart_ed() {
        clearTimeout(t);
        t = setTimeout(function() {
            init_do(200, 50);
        }, 500);
    }
    // When the initial carriers load show the pre-selected carrier
    function init_submit_refresh()
    {
        // One Page checkout modules support
        var elements = ['#onepagecheckoutps', '#opc_checkout', '#supercheckout', '#velsof_supercheckout_form', '#module-steasycheckout-default', '#module-thecheckout-order', '#tc-container', '#checkout', '#opc_checkot'];
        if ($(elements.join(', ')).length > 0) {
            for (i = 0; i < elements.length; i++) {
                if ($(elements[i]).length > 0) {
                    //console.log('OPC Selector Found');
                    $(document).on('change', elements[i] + ' input, ' + elements[i] + ' select', restart_ed);
                    $(document).on('focusout', elements[i] + ' input, ' + elements[i] + ' select', restart_ed);
                    $(document).on('click', elements[i] + ' a, ' + elements[i] + ' button, ' + elements[i] + ' img', restart_ed);
                    $(document).on('mousedown', elements[i] + ' button', restart_ed);
                    break;
                }
            }
        } else {
            // Normal Checkout support
            $(document).on('mousedown', '.step-title, button.continue, button[type=submit], a.cart_quantity_delete, .cart_quantity a', restart_ed);
            $(document).on('focusout', '.cart_quantity_input', restart_ed);
            $(document).on('change', 'select', restart_ed);
        }
    }

    function init_do(tries, ms) {
        // console.log('init_do');
        let new_ele = [];
        if (curr_ele.length > 0) {
            new_ele = curr_ele.filter(function () {
                return $(this).parent().length && $(this).is(':visible');
            });
        }
        // Array of Selector, version, delay
        let sels = [
            ['.delivery_options input[type=radio], .delivery_options_address input[type=radio]', '1.6', 0],
            ['.delivery-options input[type=radio]', '1.7', 0],
            ['.opc_shipping_method input[type=radio]', 'new-supercheckout', 1200],
            ['#shipping-method input[type=radio]', 'supercheckout', 1200],
            ['#opc_delivery_methods .carrier_infos', '1.6', 1200],
            //['#opc_delivery_methods', '1.6', 0],
        ];
        let moved = false;
        if ($('#estimateddelivery-cart').length > 0 && (curr_ele.length == 0 || curr_ele.length != new_ele.length)) {
            for (let sel of sels) {
                if ($(sel[0]).length > 0) {

                    // Remove any ID duplicate, just in case there content is generated more than once
                    let txt = [];
                    let s = $(sel[0]).filter(function() {
                        if (typeof txt[$(this).prop('id')] === 'undefined') {
                            txt[$(this).prop('id')] = true;
                            return true;
                        }
                    });
                    curr_ele = s;
                    s.each(function(i) {
                        move_options($(this), sel[1]);
                        if (s.length == i + 1) {
                            // Moved the last one, clear the content
                            $('#estimateddelivery-cart').remove();
                            moved = true;
                        }
                    });
                    // WAS:
                    // moved = true;
                    // s.each(function(i) {
                    //     setTimeout(() => {
                    //         move_options($(this), sel[1]);
                    //         if (s.length == i + 1) {
                    //             // Moved the last one, clear the content
                    //             $('#estimateddelivery-cart').remove();
                    //         }
                    //     }, sel[2]);
                    // })
                    break;
                }
            }
        }
        // If the ED has been moved...
        //if (!moved) {
            if (tries > 0) {
                if (ms <= 333) {
                    ms *= 1.1;
                }
                setTimeout(function() { init_do(tries-1, ms); }, ms);
                return false;
            }
        //}

        /*$('.delivery_options input[type=radio], .delivery_options_address input[type=radio], .delivery-options input[type=radio], .delivery_option').on('click', function() {
            init_do_recursive(50, 50);
        });*/
    }
    function move_options(e, version) {
        var repo = 2;
        var new_id = 0;
        if (e.val().indexOf(',') != -1) {
            new_id = e.val().split(',');
        } else {
            new_id = e.val();
        }
        // Have to be reviewed
        if (new_id.constructor === Array) {
            new_id.splice(new_id.length-1, 1);
        } else if (new_id === '') {
            return false;
        } else {
            var rep = parseInt(new_id[0])+1;
            rep = '0'.repeat(rep);
            var carr_id = new_id.substr(1);
            carr_id = reverseString(carr_id);
            carr_id = carr_id.split(rep);
            carr_id = carr_id.reverse();
            carr_id = reverseString(carr_id[0]);
            new_id = [carr_id];
        }
        for (var i = 0; i < new_id.length; i++) {
            repo = 2;
            if (e.closest('td').length > 0) {
                if (e.closest('td')[0].hasAttribute('rowspan') && e.closest('td').attr('rowspan') > 1) {
                    if (version == '1.6') {
                        var tmp = e.closest('table').find('tr').eq(i).find('td');
                        if (!(tmp[i].hasAttribute('rowspan') && tmp.eq(i).attr('rowspan') > 1)) {
                            repo = 1;
                        }
                        move_ed_to_line(tmp.eq(tmp.length - repo), '<div id="ed-item-', '"></div>', new_id[i]);
                    } else {
                        if (version == '1.7') {
                            move_ed_to_line(e.closest('.row'), '<div class="row ed_row"><div class="col-sm-1 col-1"></div><div class="col-sm-11 col-11"><div class="col-xs-12" id="ed-item-', '"></div></div></div>', new_id[i]);
                        }
                    }
                } else {
                    // Normal Cart with one carrier for each option
                    if (version == '1.6') {
                        if (e.closest('td').siblings().length > 1) {
                            move_ed_to_line(e.closest('td').next().next(), '<div id="ed-item-', '"></div>', new_id[i]);
                        } else if (e.closest('td').siblings().length == 1) {
                            // One Page Checkout Zelarg >> module onepagecheckout
                            move_ed_to_line(e.closest('tr').find('.delivery_option_delay'), '<div id="ed-item-', '"></div>', new_id[i]);
                        }
                    } else if (version == '1.7') {
                        move_ed_to_line(e.closest('.row'), '<div class="row ed_row"><div class="col-sm-1 col-1"></div><div class="col-sm-11 col-11"><div class="col-xs-12" id="ed-item-', '"></div></div></div>', new_id[i]);
                    } else if (version == 'supercheckout') {
                        e = e.closest('tr').find('td.shipping_info');
                        e.append('<div class="ed_delay"></div>');
                        move_ed_to_line( e.find('.ed_delay'), '<div id="ed-item-', '"></div>', new_id[i]);
                    }
                }
            } else if (e.next().find('.delivery_option_delay').length > 0) {
                // It's 1.5
                e = e.next().find('.delivery_option_delay');
                move_ed_to_line((e.next().length > 0 ? e.next() : e), '<div class="col-xs-12" id="ed-item-', '"></div>', new_id[i]);
            } else {
                /*
                if (e.find('.delivery_option_delay')) {
                    move_ed_to_line(e.find('.delivery_option_delay'), '<div id="ed-item-', '"></div>', new_id[i]);
                }
                */
                if (e.closest('.delivery-option').find('.carrier-delay').first().length > 0) {
                    move_ed_to_line(e.closest('.delivery-option').find('.carrier-delay').first().parent(), '<div id="ed-item-', '"></div>', new_id[i]);
                    //Replace the value from the summary delay for the selected carrier's ED
                    if ($('.summary-selected-carrier').find('.carrier-delay').length > 0) {
                        if (e.closest('.delivery-option').find('.ed-cart-selected').length > 0) {
                            e.closest('.delivery-option').find('.ed-cart-selected').clone().appendTo('.summary-selected-carrier .row div:eq(3)');
                            e.closest('.delivery-option').find('.carrier-delay').hide();
                            $('.summary-selected-carrier').find('.carrier-delay').remove();
                        }
                    }
                } else if (e.closest('.delivery-option').length > 0) {
                    move_ed_to_line(e.closest('.delivery-option').find('.delivery_option_delay'), '<div id="ed-item-', '"></div>', new_id[i]);
                } else if (e.closest('li').find('.shippingInfo').length > 0) {
                    e = e.closest('li');
                    // New-supercheckout
                    // Remove old content
                    //e.parent().find('.shippingInfo').empty();
                    if (e.find('#ed-item-' + new_id[i]).length == 0) {
                        e.find('.shippingInfo').html('');
                    }
                    move_ed_to_line(e.find('.shippingInfo'), '<div id="ed-item-', '"></div>', new_id[i]);
                } else if (e.closest('.row').length > 0 && e.closest('.row').find('.carrier_delay').length > 0) {
                    if (e.closest('.delivery_options_address').length > 0) {
                        move_ed_to_line(e.closest('.row').find('.delivery_option_delay'), '<div class="col-xs-12" id="ed-item-', '"></div>', new_id[i]);
                    } else if (e.closest('.row').find('.carrier_delay').length > 0) {
                        move_ed_to_line(e.closest('.row').find('.carrier_delay'), '<div id="ed-item-', '"></div>', new_id[i]);
                    } else if (e.closest('.row').next().hasClass('carrier-extra-content')) {
                        move_ed_to_line(e.closest('.row').next(), '<div class="col-xs-1 col-1"></div><div class="col-xs-11 col-11" id="ed-item-', '"></div>', new_id[i]);
                        e.closest('.row').next().hasClass('carrier-extra-content').show();
                    } else {
                        move_ed_to_line(e.closest('.row'), '<div class="col-xs-3"></div><div class="col-xs-9" id="ed-item-', '"></div>', new_id[i]);
                    }
                } else {
                    // I'm waiting
                    console.log("Cart list not found, contact module developer to get help");
                }
            }
        }
    }
    function move_ed_to_line(e, append_pre, append_post, carrier_id)
    {
        if (typeof ed_hide_delay !== 'undefined') {
            var ed_hide_delay = false;
        }
        var delay_msg = ['.carrier-delay', '.delivery_option_delay', '.carrier_delay'];
        if (ed_hide_delay) {
            for (let i=0; i < delay_msg.length; i++) {
                if (e.find(delay_msg[i]).length > 0) {
                    e = e.find(delay_msg[i]);
                    break;
                }
            }
        }
        if ($('#estimateddelivery-cart #ed-cart-'+carrier_id).length > 0) {
            if ($('#ed-item-'+carrier_id).length == 0) {
                if (ed_hide_delay) {
                    e.empty();
                }
                e.append(append_pre+carrier_id+append_post);
                $('#estimateddelivery-cart #ed-cart-'+carrier_id).appendTo($('#ed-item-'+carrier_id)).addClass('ed_order_list').show();
            } else {
                if ($('#estimateddelivery-cart #ed-cart-'+carrier_id).length > 0) {
                    $('#estimateddelivery-cart #ed-cart-'+carrier_id).remove();
                }
            }
        } else {
            // If the ED does not exist in the destination
            if (e.find('#ed-item-'+carrier_id).length == 0) {
                if (ed_hide_delay) {
                    e.empty();
                }
                var new_id = carrier_id+'-1';
                while ($('#estimateddelivery-cart #ed-cart-'+new_id).length > 0) {
                    var id = new_id.slice(-1);
                    id++;
                    new_id = new_id.slice(0, new_id.length -1)+id;
                }
                // Once the While ends we have the new id for creating the carrier
                e.append(append_pre+new_id+append_post);
                $('#ed-item-'+carrier_id).clone().appendTo($('#ed-item-'+new_id)).addClass('ed_order_list').show();
            }
        }
    }
});
