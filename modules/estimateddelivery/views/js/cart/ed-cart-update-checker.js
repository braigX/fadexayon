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
    if (settings.url.indexOf('update=1') !== -1 && typeof front_ajax_url  !== 'undefined') { // && jqXHR.responseJSON )
        const r = jqXHR.responseJSON;
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: front_ajax_url + '&rand=' + new Date().getTime(),
            async: false,
            cache: false,
            dataType : "json",
            data: {
                ajaxRefresh: true,
                action: 'cartUpdate',
            },
            success: function(data) {
                if (data.success) {
                    $('#ed_shopping_footer').replaceWith(data.deliveries);
                }
            }
        })
        //console.log(jqXHR.responseJSON);
    }
});
