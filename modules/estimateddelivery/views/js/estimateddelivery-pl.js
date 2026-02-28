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

jQuery(document).ready(function($) {
if ($(".ed_countdown").length != 0) {
    var time_limit = $(".ed_countdown").data('rest');
    var curr_hour = new Date()
    curr_hour = curr_hour.getHours()+':'+curr_hour.getMinutes();
    time_limit = time_limit.split(':');
    // Added to prevent extended time with refresh message
    if (time_limit[0] == 0 && time_limit[1] < 59) {
        time_limit[1]++;
    }
    countdown();
    var ed_count_color = $(".ed_countdown").css('color');
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
            time = (time_limit[0] != 0 ? time_limit[0]+' '+ed_hours+' '+ed_and+' ' : '')+(time_limit[1]+' '+ed_minutes)
            $(".ed_countdown").html(time);
            setTimeout(function () { countdown(); },60000);
        } else {
            $(".ed_orderbefore").html(ed_refresh);
        }
    }
    improveLangDisplay();
    $(document).ajaxComplete(function() {
        setTimeout( () => { improveLangDisplay() },200);
    })
    function replaceText(node, search, replace) {
        // Check if the node is a text node
        if (node.nodeType === 3) {
            // Replace text content
            node.nodeValue = node.nodeValue.replace(/\s+/g, ' ');
            node.nodeValue = node.nodeValue.replace(new RegExp(search, 'g'), replace);
        } else {
            // If it's not a text node, recursively search its children
            $(node).contents().each(function() {
                replaceText(this, search, replace);
            });
        }
    }

    function improveLangDisplay() {
        if (typeof prestashop !== 'object') {
            return;
        }

        const langIso = prestashop.language.iso_code;
        const mappings = {
            'pl': {prefix: 'w', replacement: 'we', days: ['wtorek', 'środa']},
            'cz': {prefix: 'v', replacement: 've', days: ['středu', 'čtvrtek']},
            'sk': {prefix: 'v', replacement: 'vo', days: ['štvrtok']}
        };

        if (mappings[langIso]) {
            const {prefix, replacement, days} = mappings[langIso];
            $('.estimateddelivery').each(function() {
                days.forEach(day => {
                    // Simple search and replace for the specific day names
                    const search = `${prefix} ${day}`;
                    const replace = `${replacement} ${day}`;
                    replaceText(this, search, replace);
                });
            });
        }
    }

});