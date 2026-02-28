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

document.addEventListener('DOMContentLoaded', function () {
    let refresh_carrier = true;
    $(document).on("click", '.js-address-item', function() {
        refresh_carrier = true;
    });
    function detectAjaxCompletedCalls() {
        // Store the original XMLHttpRequest open method
        let originalOpen = window.XMLHttpRequest.prototype.open;
        let t = null; //Timeout
        let delay = 250;

        // Override the open method to intercept AJAX requests
        window.XMLHttpRequest.prototype.open = function(method, url) {
            var xhr = this;
            xhr.addEventListener("load", function() {
                // console.log(refresh_carrier);
                if (!refresh_carrier) {
                    return;
                }
                // Clear previous timeout
                clearTimeout(t);
                // Set a new timeout to execute the action after a delay
                t = setTimeout(function() {
                    // Action to perform after the last AJAX call completes
                    $('.delivery_option_radio[checked]').prop('checked', false).trigger('click');
                    // console.log('Selecting the carrier...');
                    refresh_carrier = false;
                }, delay);
            });
            // Call the original open method
            originalOpen.apply(xhr, arguments);
        };
    }

    // Call the function to start logging AJAX requests and handling the last one
    detectAjaxCompletedCalls();
    /*
    // let refresh_carrier = true;
    // $(document).on("click", '.js-address-item', function() {
    //     refresh_carrier = true;
    // });
    $(document).ajaxComplete(function(event, xhr, settings) {
        // Check if an AJAX request is in progress
        // if (refresh_carrier) {
        console.log(settings.url);
        console.log(settings.url.indexOf('shipping/update'));

        if (settings.url.indexOf('shipping/update') !== -1) {
            // setTimeout(() => {
                // When user changes addresses
                // Simulate the carrier selection with the preselected carrier by deselecting and selecting the selected one

                // Reset the flag to indicate that no AJAX request is in progress
                refresh_carrier = false;
            // }, 10);
        }
        // }
    });*/
    // let urls = ['checkout'];
    // $(document).on('mousedown', '.delivery_option_radio, .delivery-option img', () => {
    //     setTimeout(function() { updateProductDeliveries(100, 100); }, 800);
    // });
    // function updateProductDeliveries(tries, ms) {
    //     if ($('.loading-opc').length > 0) {
    //         if (tries > 0) {
    //             setTimeout(function() { updateProductDeliveries(tries-1, ms * 1.1) }, 150);
    //         }
    //     } else {
    //         $.ajax({
    //             type: 'POST',
    //             headers: { "cache-control": "no-cache" },
    //             url: front_ajax_url + '&rand=' + new Date().getTime(),
    //             async: false,
    //             cache: false,
    //             dataType : "json",
    //             data: {
    //                 ajaxRefresh: true,
    //                 action: 'productSummaryUpdate',
    //             },
    //             success: function(data) {
    //                 if (typeof data === 'object') {
    //                     for (let product of data) {
    //                         let e = '.ed_product_summary';
    //                         let sel = '';
    //                         if (product.id_product_attribute > 0) {
    //                             sel = '[data-id_product_attribute="' + product.id_product_attribute + '"]'
    //                         } else {
    //                             sel = '[data-id_product="' + product.id_product + '"]'
    //                         }
    //                         if ($(e + sel).length > 0) {
    //                             $(e + sel).parent().replaceWith(product.html);
    //                         }
    //                     }
    //                 }
    //             }
    //         })
    //     }
    // }
});

