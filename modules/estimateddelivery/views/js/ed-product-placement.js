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
 * *                                                 *
 * ***************************************************
*/

var ed_pp = false;

$(document).ready(function() {
    ed_product_placement_detection(10, 100);

    $(document).on('click', '.quick-view, .quickview', function() {
        setTimeout(() => { ed_product_placement_init(80, 100); }, 500);
    });

    function ed_product_placement_detection(tries, ms) {
        if (tries < 0) {
            console.log('ED: Not found');
            return;
        }
        if ($('#estimateddelivery').length > 0) {
            setTimeout(() => { ed_product_placement_init(50, 100); }, ed_ajax_delay);
        } else {
            ms = Math.min(500, ms * 1.1);
            setTimeout(() => { ed_product_placement_detection(tries - 1, ms); }, ms);
        }
    }
});
function ed_product_placement_init(tries, ms) {
    // console.log('ED: Init product placement');
    improveLangDisplay();
    // console.log('ED: Init Placement ' + ed_pp);
    if (ed_pp) {
        return;
    }
    ed_pp = true;
    if (typeof ed_placement !== 'undefined') {
        // Update those two values to adapt it to any module.
        var selector = '.estimateddelivery-product';
        if (ed_placement > 0) {
            ed_product_placement(selector, ed_placement, tries, ms); //, false);
        }
    } else {
        if (tries > 0) {
            setTimeout(() => { ed_product_placement_init(tries-1); }, ms * 1.1);
        } else {
            ed_pp = false;
        }
    }
    // Define the replaceText function within ed_product_placement_init
    function replaceText(node, search, replace) {
        // Check if the node is a text node
        if (node.nodeType === 3) {
            // Replace text content
            node.nodeValue = node.nodeValue.replace(new RegExp(search, 'g'), replace);
        } else {
            // If it's not a text node, recursively search its children
            $(node).contents().each(function() {
                replaceText(this, search, replace);
            });
        }
    }

    function improveLangDisplay() {
        // Handle the Spanish "el hoy" correction
        var pickingDate = document.querySelector(".ed_picking_date");

        if (pickingDate && pickingDate.textContent.trim() === "hoy") {
            var orderBeforeMsg = pickingDate.closest(".ed_orderbefore_msg");
            if (orderBeforeMsg) {
                // Use a regex to replace " el " with a single space, making it more robust
                orderBeforeMsg.innerHTML = orderBeforeMsg.innerHTML.replace(/\s*el\s+/g, " ");
            }
        }

        // Check if prestashop object exists and language-specific replacements
        if (typeof prestashop === 'object' && prestashop.language.iso_code === 'pl') {
            // Perform language-specific replacements for Polish
            document.querySelectorAll('.estimateddelivery').forEach(function(element) {
                var search = "w w";
                var replace = "we w";
                // Perform a text replacement in each element
                replaceText(element, search, replace);
            });
        }
    }

    // Helper function to replace text content in an element
    function replaceText(element, search, replace) {
        // Use regex for global replacement and better performance
        var regex = new RegExp(search, 'g');
        element.innerHTML = element.innerHTML.replace(regex, replace);
    }

}


function ed_product_placement(selector, position, tries, ms) // WAS , repopulate)
{
    // console.log('ED: Placement ' + ed_pp);
    let moved = false;
    if (position == 0) {
        ed_pp = false;
        return
    }
    if (position == 50) {
        ed_custom_placement(selector, tries);
    } else {
        var main_position = Math.ceil(position / 2);
        // Move videos if the Side Position is not 0
        var destination = ['', ['h1[itemprop=name]', 'h2[itemprop=name]', 'h1', 'h2'], ['[itemprop=description]', '.description'], '.product-prices', '.product-add-to-cart'];
        if (tries > 0) {
            let dest = '';
            let sel = $(selector).not('.moved');
            if (sel.length > 0) {
                if ($(selector + '.moved').length > 0) {
                    $(selector + '.moved').remove();
                }
                for (let i = 0; i < destination[main_position].length; i++) {
                    if (typeof destination[main_position] === 'object') {
                        dest = destination[main_position][i];
                    } else {
                        dest = destination[main_position];
                    }
                    if (sel.closest('.quickview, .modal-quickview, .quick-view, .quickview-modal').length > 0) {
                        dest = $('.quickview, .modal-quickview, .quick-view, .quickview-modal').find(dest).first();
                    } else {
                        dest = $(dest);
                    }
                    if (dest.length > 0) {
                        if (position % 2 === 0) {
                            if (dest.next().is(selector)) {
                                dest.next().remove();
                            }
                            sel.first().insertAfter(dest);
                            moved = true;
                        } else {
                            if (dest.prev().is(selector)) {
                                dest.prev().remove();
                            }
                            sel.first().insertBefore(dest);
                            moved = true;
                        }
                    }
                }
            }
            if (!moved) {
                ms == undefined ? 100 : ms;
                if (ms < 800) {
                    ms = ms * 1.05;
                }
                setTimeout(function () {
                    ed_product_placement(selector, position, tries - 1, ms);
                }, ms);
            } else {
                sel.first().addClass('moved');
                sel.first().removeClass('hide-default');
                $('#ed_popup').appendTo('#estimateddelivery').removeClass('hide-default');
                //break;
                setTimeout(() => { ed_pp = false; }, 200);
                return false;
            }
        } else {
            console.log('ED: Could not locate the placement');
            ed_pp = false;
        }
    }
}
function ed_custom_placement(selector, tries) {
    var method = ['insertBefore', 'prependTo', 'appendTo', 'insertAfter'];
    if (ed_custom_sel != '' && typeof ed_custom_ins !== 'undefined') {
        if ($(selector).length > 0) {
            if ($(ed_custom_sel).length > 0) {
                if ($(selector + '.moved').length > 0) {
                    $(selector + '.moved').remove();
                }
                //$(selector).first().appendTo($(ed_custom_sel));
                $(selector).first()[method[ed_custom_ins-1]]($(ed_custom_sel));
                $(selector).first().addClass('moved');
                $(selector).first().removeClass('hide-default');
                $('#ed_popup').appendTo('#estimateddelivery').removeClass('hide-default');
                return true;
            } else {
                console.warn('Custom selector for Estimated Delivery could not be found. \n\r\n\rPlease Review the module selector on the Module\'s configuration page - Section 1');
            }
        }
        ed_pp = false;
    }
    if (tries > 0) {
        setTimeout(function() { ed_custom_placement(selector, tries-1); }, 50);
    } else {
        ed_pp = false;
    }
}