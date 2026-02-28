/**
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 2.7.7
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 2.7.7                      *
 * ***************************************************
*/

jQuery(document).ready(function($) {
    showSelectedCarrier(10);
    //var ed_17 = false;
    init_do(8);
    init_submit_refresh();
    // When the initial carriers load show the pre-selected carrier
    
    function init_submit_refresh()
    {
        $('#login_form, #new_account_form').submit(function() {
            init_do(15);
            setTimeout(function() { init_submit_refresh(); }, 2000);
        });
    }
    function init_do(tries)
    {
        if ($('.delivery_options').length > 0) {
            // It's 1.5 - 1.6
            $('#estimateddelivery-cart').insertAfter($('.delivery_options'));
            init_do_next();
        } else {
            if ($('.delivery-options').length > 0) {
            $('#estimateddelivery-cart').insertAfter($('.delivery-options'));
            init_do_next();
            //ed_17 = true;
            } else {
                if (tries > 0) {
                    setTimeout(function() { init_do(tries-1); }, 250);
                } else {
                    console.log('Could not find the delivery options');
                }
            }
        }
    }
    function init_do_next()
    {
        $('.delivery_options input[type=radio], .delivery-options input[type=radio]').click(function() {
            //$('#estimateddelivery-cart > div').fadeOut(500);
            var new_id = $(this).val().replace(',', '');
            setTimeout(function() {
                showSelectedCarrier(10);
                init_do(5); 
                $('#estimateddelivery-cart > div').hide();
                $('#estimateddelivery-cart #ed-cart-'+new_id).fadeIn(500);
            }, 700);
        });
    }
    
    function showSelectedCarrier(tries)
    {
        e = $('.delivery_options input[type=radio], .delivery-options input[type=radio]');
        if (e.length > 0) {
            e = e.filter(':checked').val().replace(',', '');
            if (typeof e != 'undefined' && e > 0) {
                $('#estimateddelivery-cart #ed-cart-'+e).show();
            } else {
                if (tries > 0) {
                    setTimeout(function() { showSelectedCarrier(tries-1); }, 250);
                }
            }
        } else {
            if (tries > 0) {
                setTimeout(function() { showSelectedCarrier(tries-1); }, 250);
            }
        }
    }
});