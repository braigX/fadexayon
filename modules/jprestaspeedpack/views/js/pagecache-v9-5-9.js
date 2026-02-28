/*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*/

function jprestaPcGetParameterValue(e) {
    let t = "[\\?&]" + e + "=([^&#]*)";
    let n = new RegExp(t);
    let r = n.exec(window.location.href);
    if (r == null) return "";
    else return r[1]
}

function jprestaPcSplitUri(uri) {
    let splitRegExp = new RegExp('^' + '(?:' + '([^:/?#.]+)' + ':)?' + '(?://' + '(?:([^/?#]*)@)?' + '([\\w\\d\\-\\u0100-\\uffff.%]*)' + '(?:(:[0-9]+))?' + ')?' + '([^?#]+)?' + '(?:(\\?[^#]*))?' + '(?:(#.*))?' + '$');
    let split = uri.match(splitRegExp);
    for (let i = 1; i < 8; i++) {
        if (typeof split[i] === 'undefined') {
            split[i] = '';
        }
    }
    return {
        'scheme': split[1],
        'user_info': split[2],
        'domain': split[3],
        'port': split[4],
        'path': split[5],
        'query_data': split[6],
        'fragment': split[7]
    }
}

function jprestaPcSetCookie(cname, cvalue, ttl_minutes, path) {
    let d = new Date();
    d.setTime(d.getTime() + (ttl_minutes*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=" + path;
}

function jprestaPcGetCookie(cname, defaultValue) {
    if (defaultValue === undefined) {
        defaultValue = null;
    }
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return defaultValue;
}

function jprestaGetTTFB() {
    let timing = null;
    if (typeof window.performance.timing !== 'undefined') {
        timing = window.performance.timing;
    }
    else {
        timing = window.performance.getEntriesByType('navigation')[0];
    }
    return timing.responseStart - timing.requestStart;
}

function jprestaIsBot() {
    return /bot|googlebot|crawler|spider|robot|crawling|gtmetrix|chrome-lighthouse/i.test(navigator.userAgent);
}

function jprestaGetCacheType() {
    let cache_type = -2;
    let timing = null;
    if (typeof window.performance.getEntriesByType == 'function') {
        timing = window.performance.getEntriesByType('navigation')[0];
    }
    if (!timing && typeof window.performance.timing !== 'undefined') {
        timing = window.performance.timing;
    }
    if (timing && timing.transferSize === 0) {
        cache_type = 2;
    }
    else if (timing && typeof timing.serverTiming != 'undefined') {
        for (let i = 0; i < timing.serverTiming.length; i++) {
            if (timing.serverTiming[i].name === 'jpresta_cache') {
                cache_type = parseInt(timing.serverTiming[i].description);
                break;
            }
        }
    }
    return cache_type;
}

/**
 *  Forward dbgpagecache parameter
 */
function jprestaPcForwardDbgpagecacheParameter() {
    try {
        if (typeof prestashop !== 'undefined' && prestashop.urls && prestashop.urls.base_url) {
            baseDir = prestashop.urls.base_url;
        } else if (typeof baseDir === 'undefined') {
            baseDir = window.location.origin + '/';
        }

        if (window.location.href.indexOf("dbgpagecache=") > 0) {
            $("a:not(.pagecache)").each(function () {
                let e = $(this).attr("href");
                let t = this.search;
                let n = "dbgpagecache=" + jprestaPcGetParameterValue("dbgpagecache");
                let r = baseDir.replace("https", "http");
                if (typeof e !== "undefined" && e.substr(0, 1) !== "#" && (e.replace("https", "http").substr(0, r.length) === r || e.indexOf('://') === -1) && e.indexOf('javascript:') === -1 && e.indexOf('mailto:') === -1 && e.indexOf('tel:') === -1 && e.indexOf('callto:') === -1) {
                    if (t.length === 0) this.search = n;
                    else this.search += "&" + n;
                }
            });
            console.log("Page Cache Ultimate - Parameter dbgpagecache has been added to all links");
        }
    } catch (e) {
        console.warn("Page Cache Ultimate - Cannot forward dbgpagecache parameter on all links: " + e.message, e);
    }
}

jprestaPcStartsWith = function(str, search) {
    return typeof str === 'string' && str.substr(0, search.length) === search;
};

jprestaPcProcessDynamicModules = function(dyndatas) {
    for (let key in dyndatas) {
        if (key === 'js') {
            // Keep spaces arround 'key', some Prestashop removes [key] otherwise (?!)
            $('body').append(dyndatas[ key ]);
        }
        else if (jprestaPcStartsWith(key, 'dyn')) {
            // Keep spaces arround 'key', some Prestashop removes [key] otherwise (?!)
            try {
                $('#'+key).replaceWith(dyndatas[ key ]);
            }
            catch (error) {
                console.error('Page Cache Ultimate - A javasript error occured during the "eval" of the refreshed content ' + key + ': ' + error);
            }
        }
    }
    if (typeof pcRunDynamicModulesJs == 'function') {
        pcRunDynamicModulesJs();
    }
    $('header a[href]').each(function() {
        $(this).attr('href', $(this).attr('href')
            .replace(/(%26|%3F|\?|&)ajax(%3D|=)1(%26|&)page_cache_dynamics_mods(%3D|=)1(%26|&)action(%3D|=)refresh_dynamic_mods/, '')
        );
    });
    console.timeEnd('Page Cache Ultimate - Dynamic modules have been refreshed in ')
    if (typeof prestashop != 'undefined' && typeof prestashop.emit == 'function') {
        prestashop.emit('jprestaDynamicContentLoaded');
    }
};

/**
 * Refresh dynamic modules
 */
function jprestaPcRefreshDynamicModules(cacheSource) {
    try {
        console.time('Page Cache Ultimate - Dynamic modules have been refreshed in ')
        let dynDatas = {};
        dynDatas['cache_source'] = cacheSource;
        dynDatas['ttfb'] = jprestaGetTTFB();
        $('.dynhook').each(function(index, domhook){
            dynDatas['hk_' + index] = $(this).attr('id') + '|' + $(this).data('hooktype') + '|' + $(this).data('module') + '|' + $(this).data('hook') + '|' + $(this).data('hookargs');
        });
        let urlparts = jprestaPcSplitUri(document.URL);
        let url = urlparts['scheme'] + '://' + urlparts['domain'] + urlparts['port'] + urlparts['path'] + urlparts['query_data'];
        let indexEnd = url.indexOf('?');
        if (indexEnd >= 0 && indexEnd < url.length) {
            url += '&ajax=1&page_cache_dynamics_mods=1&action=refresh_dynamic_mods';
        }
        else {
            url += '?ajax=1&page_cache_dynamics_mods=1&action=refresh_dynamic_mods';
        }
        let headers = {};
        if (document.referrer) {
            headers['x-jpresta-referer'] = document.referrer;
        }
        $.ajax({url: url, type: 'POST', data: dynDatas, dataType: 'json', cache: false, headers: headers,
            success: jprestaPcProcessDynamicModules,
            error: function(jqXHR, textStatus, errorThrown) {
                let dyndatas;
                try {
                    let indexStart = jqXHR.responseText.indexOf('{');
                    let responseFixed = jqXHR.responseText.substring(indexStart, jqXHR.responseText.length);
                    dyndatas = $.parseJSON(responseFixed);
                    if (dyndatas != null) {
                        jprestaPcProcessDynamicModules(dyndatas);
                        return;
                    }
                } catch (err) {
                    console.error("Page Cache Ultimate - Cannot parse data of error=" + err, err);
                }
                console.error("Page Cache Ultimate - Cannot display dynamic modules: error=" + textStatus + " exception=" + errorThrown);
                console.log("Page Cache Ultimate - Dynamic module URL: " + url);
            }});
    } catch (e) {
        console.error("Page Cache Ultimate - Cannot display dynamic modules: " + e.message, e);
    }
}

/**
 * Stats TTFB (when using back/forward cache)
 */
function jprestaPcSendStats(cacheSource, ttfb) {
    try {
        console.time('Page Cache Ultimate - Sending stats in ');
        let dynDatas = {};
        dynDatas['cache_source'] = cacheSource;
        dynDatas['ttfb'] = ttfb;
        let urlparts = jprestaPcSplitUri(document.URL);
        let url = urlparts['scheme'] + '://' + urlparts['domain'] + urlparts['port'] + urlparts['path'] + urlparts['query_data'];
        let indexEnd = url.indexOf('?');
        if (indexEnd >= 0 && indexEnd < url.length) {
            url += '&ajax=1&page_cache_dynamics_mods=1&action=refresh_dynamic_mods&stats';
        }
        else {
            url += '?ajax=1&page_cache_dynamics_mods=1&action=refresh_dynamic_mods&stats';
        }
        $.ajax({url: url, type: 'POST', data: dynDatas, dataType: 'json', cache: false,
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Page Cache Ultimate - Cannot display dynamic modules: error=" + textStatus + " exception=" + errorThrown);
                console.log("Page Cache Ultimate - Stats URL: " + url);
            },
            complete: function() {
                console.timeEnd('Page Cache Ultimate - Sending stats in ');
            }
        });
    } catch (e) {
        console.error("Page Cache Ultimate - Cannot stats: " + e.message, e);
    }
}

let jprestaUpdateCartTryCount = 0;

function jprestaUpdateCart() {
    // Refresh the cart
    if (typeof prestashop !== 'undefined' && typeof prestashop.emit == 'function') {
        if (typeof prestashop._events['updateCart'] == 'undefined') {
            if (jprestaUpdateCartTryCount < 10) {
                // The cart is not yet attch to the event so we do it a little bit later
                jprestaUpdateCartTryCount++;
                console.log('Page Cache Ultimate - Cart is not ready, retrying...');
                setTimeout(jprestaUpdateCart, 100);
                return;
            }
            else {
                console.log('Page Cache Ultimate - Cart is not ready, NOT retrying because we tried too many times.');
            }
        }
        if (typeof jprestaUseCreativeElements == 'undefined' || !jprestaUseCreativeElements) {
            // >= PS 1.7
            console.log('Page Cache Ultimate - Refreshing the cart (PS >= 1.7)...');
            // Need to put it in a setTimeout to let other modules subscribes to the event
            setTimeout("prestashop.emit('updateCart', {reason: {linkAction: 'refresh'}, resp: {errors: []}})", 10);
        }
        else {
            // For CreativeElements
            console.log('Page Cache Ultimate - Refreshing the cart (CreativeElements)...');
            $.ajax({
                url: prestashop.urls.pages.cart,
                method: 'POST',
                dataType: 'json',
                data: {
                    ajax: 1,
                    action: 'update'
                }
            }).then(function(resp) {
                if (resp.success && resp.cart) {
                    prestashop.emit('updateCart', {
                        reason: {
                            linkAction: 'refresh'
                        },
                        resp: resp
                    });
                }
            });
        }
    }
    else if(typeof ajaxCart !== 'undefined') {
        // < PS 1.7
        console.log('Page Cache Ultimate - Refreshing the cart (PS 1.5/1.6)...');
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'controller=cart&ajax=1&token=' + static_token,
            success: function (jsonData) {
                ajaxCart.updateCart(jsonData);
            }
        });
    }
}

window.addEventListener('load',  function(event) {
    if (jprestaIsBot()) {
        // For bots just send stats
        jprestaPcSendStats(jprestaGetCacheType(), jprestaGetTTFB());
        return;
    }
    if (typeof jprestaUpdateCartDirectly != 'undefined' && jprestaUpdateCartDirectly) {
        // Refresh the cart right now instead of waiting after dynamic modules are refreshed
        if (typeof $ == 'function') {
            // This hack makes sure the event "updateCart" will be emitted after all modules are attached "on" it
            $(document).ready(jprestaUpdateCart);
        }
        else {
            jprestaUpdateCart();
        }
    }
    // jpresta_cache_source => -1=cannot be cached; 0=no cache; 1=server cache; 2=browser cache, 3=static cache, 4=back/forward cache
    let cacheSource = jprestaGetCacheType();
    switch (cacheSource) {
        case -2:
            jprestaPcRefreshDynamicModules(cacheSource);
            console.log('Page Cache Ultimate - Cannot determine the cache type :-(');
            break;
        case -1:
            console.log('Page Cache Ultimate - Cannot be cached');
            break;
        case 0:
            jprestaPcRefreshDynamicModules(cacheSource);
            console.log('Page Cache Ultimate - No cache was used');
            break;
        case 1:
            jprestaPcRefreshDynamicModules(cacheSource);
            console.log('Page Cache Ultimate - Server cache was used');
            break;
        case 2:
            jprestaPcRefreshDynamicModules(cacheSource);
            console.log('Page Cache Ultimate - Browser cache was used');
            break;
        case 3:
            jprestaPcRefreshDynamicModules(cacheSource);
            console.log('Page Cache Ultimate - Static cache was used');
            break;
        case 4:
            // Handled by 'pageshow' event
            break;
    }
    $('.pctype' + cacheSource).show();
    jprestaPcForwardDbgpagecacheParameter();
    let ctxUuid = jprestaPcGetCookie('jpresta_cache_context', false);
    if (ctxUuid) {
        console.log('Page Cache Ultimate - Displaying the page with context \x1B[1;4m' + ctxUuid + '\x1B[m');
    }
});

// Refresh the cart when back/forward cache is used
// pageshow is not always executed, only when the back/forward cache is used
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        jprestaPcSendStats(4, 0);
        // Refresh the cart
        console.log('Page Cache Ultimate - Back/forward cache is used');
        jprestaUpdateCart();
    }
});

window.addEventListener('resize', function(event) {
    document.cookie = "jpresta_cache_context=;path=/;expires=Thu, 01 Jan 1970 00:00:00 GMT";
});
