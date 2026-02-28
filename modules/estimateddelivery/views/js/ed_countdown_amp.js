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
    if ($(".ed_countdown").length != 0) {
        if ($(".ed_countdown").data('rest').length > 0) {
            var time_limit = $(".ed_countdown").data('rest');
            var curr_hour = new Date();
            curr_hour = curr_hour.getHours()+':'+curr_hour.getMinutes();
            time_limit = time_limit.split(':');
            // Added to prevent extended time with refresh message
            if (time_limit[0] == 0 && time_limit[1] < 59) {
                time_limit[1]++;
            }
            countdown();
            var ed_count_color = $(".ed_countdown").css('color');
        }
    }

    function countdown() {
        var time = '';
        time_limit[1] -= 1;
        if (time_limit[1] < 0) {
            time_limit[1] += 60;
            time_limit[0]--;
            if (time_limit[0] < 10 && time_limit[0] > 0) {
                time_limit[0] = '0'+time_limit[0];
            }
            if (time_limit[0] <= 0) {
                time = ed_refresh;
            }
        }

        if (time_limit[1] < 10 && time_limit[1] > 0) {
            time_limit[1] = '0'+time_limit[1];
        }
        if (time == '') {
            time = (time_limit[0] != 0 ? parseInt(time_limit[0])+' '+ed_hours+' '+ed_and+' ' : '')+(parseInt(time_limit[1])+' '+ed_minutes)
            $(".ed_countdown").html(time);
            setTimeout(function () { countdown(); },60000);
        } else {
            $(".ed_orderbefore").html(ed_refresh);
        }
    }
});
