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

$(document).ajaxComplete(function(ev, jqXHR, settings) {
    let refreshing = false;
    if( typeof settings.type !== 'undefined' && settings.type == 'POST' ) {
        if( settings.data.indexOf('&add=') > 0 && typeof jqXHR.responseJSON.products !== 'undefined' && jqXHR.responseJSON.products.length > 0 ) {
            if(ed_in_modal == 0 || refreshing)
                return false;
            refreshing = true;
            var params = {}, hash;
            var vars = settings.data.split('&');
            for(var i = 0; i < vars.length; i++){
                hash = vars[i].split('=');
                if( hash[0] != 'controller' && hash[0] != 'add' && hash[0] != 'token' ) {
                    params[hash[0]] = hash[1];
                }
            }
            params['ajaxRefresh'] = 1;
            params['action'] = 'cart';
            var returnData = $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: front_ajax_url,
                async: false,
                cache: false,
                dataType : "json",
                data: params,
                complete: function(res){
                    if(res.statusText == 'OK') {
                        return res;
                    }
                }
            });
            if( returnData != '') {
                var ed_data = returnData.responseJSON;
                let dd = jQuery('#layer_cart').find('#estimateddelivery');
                if (dd.length > 0) {
                    rr.remove();
                }
                jQuery('#layer_cart').find('.layer_cart_cart').append(ed_data);
            }
            setTimeout(function() { refreshing = false}, 500);
        }
    }
});