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

var ed_repo = false;

function repopulateED(tries, check = true, e = false) {
    // console.log({
    //     tries: tries,
    //     check: check,
    //     e: e,
    //     ed_repo: ed_repo,
    //     ed_in_modal: ed_in_modal,
    //     ed_placement: ed_placement
    // });
    if (ed_placement === 0) {
        // Skip, default placement
        // console.log('ED: skip, is default placement');
        return;
    }
    if (check && ed_repo) {
        // console.log('ED: Check and no repo. Aborting');
        return false;
    }

    ed_repo = true;

    if (tries === 0) {
        return;
    }

    if (check || !e) {
        // console.log('ED: Check elements');
        const movedElement = $('#add-to-cart-or-refresh #estimateddelivery.moved');
        const productElement = $('.product-information #estimateddelivery');

        e = movedElement.length > 0 ? movedElement : (productElement.length > 0 ? productElement : false);

        if (e) {
            e.filter(':not(.hide-default)').remove();
        } else {
            setTimeout(() => {
                repopulateED(tries - 1, false);
            }, 200);
            return;
        }
    }

    setTimeout(() => {
        console.log('ED: Init Repopulate placement');
        console.log($('#estimateddelivery'));
        console.log($('.estimateddelivery'));
        ed_product_placement_init(10, 100);
        ed_repo = false;
    }, ed_refresh_delay);
}

let ac_init = false;
// Cart Modal Display and Repopulate

if (document.readyState !== 'loading') {
    init_ac();
} else {
    document.addEventListener('DOMContentLoaded', function () {
        init_ac();
    });
}
function init_ac() {
    if (ac_init) {
        return;
    }
    ac_init = true;
    setTimeout(function () {
        $(document).ajaxComplete(function (ev, jqXHR, settings) {
            if (typeof jqXHR.responseJSON !== 'undefined' && jqXHR.responseJSON != null) {
                // console.log(jqXHR.responseJSON);
                // console.log(settings.data);
                let need_repopulate = checkRefreshOrUpdateFromSettings(settings.data);
                if (typeof jqXHR.responseJSON.product_additional_info !== 'undefined' && (parseInt(jqXHR.responseJSON.id_product_attribute) > 0 || need_repopulate)) {
                    // console.log('ED: repopulate');
                    ed_pp = false;
                    repopulateED(150);
                }
                if (typeof jqXHR.responseJSON.modal !== 'undefined' && jqXHR.responseJSON.modal) {
                    if (ed_in_modal == 0)
                        return false;
                    //console.log('IN');
                    var params = {}, hash;
                    var vars = settings.data.split('&');
                    for (var i = 0; i < vars.length; i++) {
                        hash = vars[i].split('=');
                        params[hash[0]] = hash[1];
                    }
                    params['modalAction'] = 1;
                    params['ajax'] = 1;
                    params['action'] = 'cart';
                    var returnData = $.ajax({
                        type: 'POST',
                        headers: {"cache-control": "no-cache"},
                        url: front_ajax_url,
                        async: false,
                        cache: false,
                        dataType: "json",
                        data: params,
                        complete: function (res) {
                            //console.log(res);
                            if (res.statusText == 'OK') {
                                return res;
                            }
                        }
                    });
                    if (returnData != '') {
                        // var ed_data = '<div class="col-md-12">' + returnData.responseJSON + '</div>';
                        var ed_data = returnData.responseJSON;
                        var jsonArray = $.parseHTML(jqXHR.responseJSON.modal);
                        var modalDom;
                        for (var i = 0; i < jsonArray.length; i++) {
                            if ($(jsonArray[i])[0] instanceof Element) {
                                modalDom = $(jsonArray[i])[0];
                            }
                        }
                        setTimeout(() => {
                            if ($('#ed_modal').length == 0) {
                                // $(modalDom).find('.modal-body .row .col-md-5 .row').append(ed_data);
                                $(modalDom).find('.modal-body .cart-content').first().append(ed_data);
                                var tmp = document.createElement("div");
                                tmp.appendChild($(modalDom)[0]);
                                jqXHR.responseJSON.modal = tmp.innerHTML;
                            }
                        }, 1500)
                    }
                }
            }
        });
    }, ed_ajax_delay);

    function checkRefreshOrUpdateFromSettings(url) {
        if (url == '' || typeof url == 'undefined') {
            return false;
        }
        let vars = url.split('&');
        for (let i = 0; i < vars.length; i++) {
            let hash = vars[i].split('=');
            // console.log(hash[0] + ' >> ' + hash[1]);
            // Check if hash[0] equals to refresh or update
            if (hash[1] == 'refresh' || hash[1] == 'update') {
                return true;
            }
        }
        return false;
    }
}
