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
document.addEventListener('readystatechange',function() {
    if (document.readyState !== 'complete') {
        return;
    }
    showSelectedCarrier(20);
    //var ed_17 = false;
    init_do(12, 150);
    init_submit_refresh();
    init_do_static(10);
    $(document).ajaxComplete(function(ev, jqXHR, settings) {
        if (typeof settings.data !== 'undefined' && settings.data != '') {
            var requestUrl = '';
            var is_cart_action = false;
            if( parseInt(ps_version) == 17 ) {
                requestUrl = settings.url;
                if( requestUrl.indexOf('update') > 0 || requestUrl.indexOf('delete') > 0 )
                    is_cart_action = true;
            }
            else { 
                requestUrl = settings.data;
                if( requestUrl.indexOf('add=true') > 0 || requestUrl.indexOf('delete=true') > 0 )
                    is_cart_action = true;
            }
            //console.log(is_cart_action);
            if( typeof settings.url !== 'undefined' && settings.url != '' && is_cart_action ) {
                var params = {}, hash;
                var vars = requestUrl.split('&');
                for(var i = 0; i < vars.length; i++) {
                    if( vars[i].indexOf('?update') > 0 ) {
                        params['ajax_req'] = 'update';
                    } else if( vars[i].indexOf('?delete') > 0 ) {
                        params['ajax_req'] = 'delete';
                    } else {
                        hash = vars[i].split('=');
                        if( hash[0] != 'token' )
                            params[hash[0]] = hash[1];
                    }
                }
                params['ajax'] = 1;
                params['action_on_cart'] = 1;
                params['action'] = 'Cart';
                if( typeof params['controller'] !== 'undefined' && params['controller'] != '' ) { // for v1.6
                    delete(params['controller']);
                    delete(params['add']);
                    delete(params['delete']);
                    if( params['id_product_attribute'] == undefined )
                        params['id_product_attribute'] = params['ipa'];
                }
                var returnData = '';

                if( (typeof front_ajax_url !== 'undefined' && front_ajax_url != '') && (typeof ed_display_option !== 'undefined' && ed_display_option != 0) ) {
                    params['ed_display_option'] = ed_display_option;
                    var ajaxLoop = 1;
                    if(ed_display_option == 3) {
                        ajaxLoop = 2;
                        params['ed_display_option'] = 1;
                    }
                    for(var k = 0; k < ajaxLoop; k++) {
                        returnData = $.ajax({
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
                        if( returnData.responseJSON != '') {
                            if( params['ed_display_option'] == 1 ) {
                                $('#ed_shopping_footer').html('');
                                $('#ed_shopping_footer').replaceWith(returnData.responseJSON);
                                $('#ed_shopping_footer').fadeIn('slow');
                            } else {
                                var refreshED = $(returnData.responseJSON);
                                var edDateDom;
                                for(var i=0; i<refreshED.length; i++) {
                                    if( $(refreshED[i])[0] instanceof Element && refreshED[i].tagName.toUpperCase() != 'SCRIPT') {
                                        edDateDom = refreshED[i];
                                    }
                                }
                                var id_product = params['id_product'];
                                var id_product_attribute = params['id_product_attribute'];
                                $(".ed_product_summary[data-id_product='" + id_product + "'][data-id_product_attribute='" + id_product_attribute + "']").replaceWith($(edDateDom).find('.ed_product_summary')[0]);
                            }
                        }
                        if(k == 0 && ed_display_option == 3)
                            params['ed_display_option'] = 2;
                    }
                }
            }
        } 
        else{
            if(settings.url.indexOf('ajax=1') > 0 && settings.url.indexOf('&action=refresh')) {
                if($('.cart-voucher').find('#ed_shopping_footer').length > 0) {
                    // var ed_data = $('#ed_shopping_footer');
                    var jsonArray =  $.parseHTML(jqXHR.responseJSON.cart_voucher);
                    var refreshDom;
                    for(var i=0; i<jsonArray.length; i++) {
                        if( $(jsonArray[i])[0] instanceof Element ) {
                            refreshDom = $(jsonArray[i])[0];
                        }
                    }
                    $('#ed_shopping_footer').insertBefore($(refreshDom).find('.cart-voucher-area'));
                    var tmp = document.createElement("div");
                    tmp.appendChild($(refreshDom)[0]);
                    jqXHR.responseJSON.cart_voucher = tmp.innerHTML;
                }
            }
        }
    });
    function init_do(tries, ms)
    {
        let sel = false;
        let dyn_tout = 1000;
        if (dynamic_refresh === false) {
            if ($('.delivery_options').length > 0) {
                // It's 1.5 - 1.6
                if ($('#estimateddelivery-cart').length === 0) {
                    $('#estimateddelivery-cart').insertAfter($('.' + $(this).attr("class")));
                }
                if ($('#onepagecheckoutps').length > 0) {
                    sel = '#onepagecheckoutps input';
                } else if ($('.opc-main-block').length > 0) {
                    sel = '.opc-main-block input';
                }
            } else if ($('.delivery-options').length > 0) {
                $('#estimateddelivery-cart').insertAfter($('.delivery-options'));
                if ($('#onepagecheckoutps').length > 0) {
                    sel = '#onepagecheckoutps input';
                } else if ($('.opc-main-block').length > 0) {
                    sel = '.opc-main-block input';
                }
            } else if ($('.opc_shipping_method input[type=radio]').length > 0) {
                sel = '.opc_shipping_method input'
            }
            if (sel !== false) {
                $(document).on('change', sel, function () {
                    dynamic_refresh = setTimeout(function () {
                        init_do_dynamic(15, dynamic_refresh);
                    }, dyn_tout);
                });
            } else {
                if (tries > 0) {
                    ms *= 1.1;
                    setTimeout(function () {
                        init_do(tries - 1, ms);
                    }, ms);
                } else {
                    console.log('Could not find the delivery options');
                }
            }
        }
    }
    function init_do_static(tries)
    {
        setTimeout(function() {
            $(document).on('click', '.delivery_options input[type=radio], .delivery_options_address input[type=radio], .delivery-options input[type=radio], .supercheckout_shipping_option', function() {
                showSelectedId($(this));
            });
        }, 500);
    }
    function init_do_dynamic(tries, timeoutId)
    {
        var selectors = '.delivery_options input[type=radio]:checked, .delivery_options_address input[type=radio]:checked, .delivery-options input[type=radio]:checked, .delivery-option:checked, .supercheckout_shipping_option:checked';
        if (timeoutId !== dynamic_refresh) {
            clearTimeout(timeoutId);
            return;
        }
        if ($('#estimateddelivery-cart div:visible').length > 0) {
            if (tries > 0) {
                setTimeout(function() { init_do_dynamic(tries-1, timeoutId); }, 500);
            }
        } else {
            if ($(selectors).length > 0) {
                setTimeout(function() { showSelectedCarrier(10); }, 500);
            }
        }
    }
    function getSelectedId(e) {
        let a;
        if (typeof e.val() !== 'undefined' && e.val() != '') {
            a = e.val().split(',');
        } else {
            a = e.find('input').first().val().split(',');
        }
        return a.filter(n => n);
    }
    function showSelectedId(e) {
        var new_ids = getSelectedId(e);;
        if (new_ids.length > 0) {
            $('#estimateddelivery-cart > div').hide();
            for (let i = 0; i < new_ids.length; i++) {
                setTimeout(function() {showSelectedED('#estimateddelivery-cart #ed-cart-'+new_ids[i], 20, 200); }, 500);
            }
        } else {
            console.log('Couldn\'t find the New ID');
        }
    }
    function showSelectedED(e, tries, ms) {
        if ($(e).length > 0 && $('.loading_small').is(':visible') === false) {
            // It has been updated an loaded the new content
            init_do(5);
            $(e).fadeIn(500);
        } else {
            if (tries > 0) {
                ms *= 1.05;
                setTimeout(function() {
                    showSelectedED(e, tries-1, ms);
                }, ms);
            }
        }
    }
    // When the initial carriers load show the pre-selected carrier
    function init_submit_refresh()
    {
        $('button[type=submit], a.cart_quantity_delete').click(function() {
            setTimeout(function() { 
                init_do(15);
                init_submit_refresh(); 
            }, 2500);
        });
        $('#onepagecheckoutps input').each(function() {
            var elem = $(this);
            // Save current value of element
            elem.data('oldVal', elem.val());

            // Look for changes in the value
            elem.bind("propertychange change click keyup input paste", function(event){
            // If value has changed...
                if (elem.data('oldVal') != elem.val()) {
                // Updated stored value
                elem.data('oldVal', elem.val());
                // Do action
                setTimeout(function() { 
                    init_do(15);
                    init_submit_refresh(); 
                }, 2500);
                }
            });
        });
        $('#onepagecheckoutps select').change(function() {
            setTimeout(function() { 
                showSelectedCarrier(10);
                init_do(15);
                init_submit_refresh(); 
            }, 2500);
        });
    }

    function showSelectedCarrier(tries) {
        var e = $('.delivery_options input[type=radio], .delivery_options_address input[type=radio], .delivery-options input[type=radio], .supercheckout_shipping_option').filter(':checked');
        if (e.length > 0) {
            e = e.val().split(',');
            let shown = false;
            for (let i = 0; i < e.length; i++) {
                if (typeof e[i] != 'undefined' && e[i] != '' && e[i] > 0) {
                    $('#estimateddelivery-cart #ed-cart-' + e[i]).show();
                    shown = true;
                }
            }
            if (!shown) {
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