/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var ETS_ABANCART_HAS_BROWSER = ETS_ABANCART_HAS_BROWSER || 0;
if (ETS_ABANCART_HAS_BROWSER) {
    document.addEventListener('DOMContentLoaded', function () {
        if (!("Notification" in window)) {
        } else if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    });
}
var ETS_ABANCART_CAMPAIGNS = ETS_ABANCART_CAMPAIGNS || [],
    ETS_ABANCART_COOKIE_CAMPAIGNS = ETS_ABANCART_COOKIE_CAMPAIGNS || [],
    ETS_ABANCART_LINK_AJAX = ETS_ABANCART_LINK_AJAX || '',
    ETS_ABANCART_COPIED_MESSAGE = ETS_ABANCART_COPIED_MESSAGE || 'Copied',
    ETS_ABANCART_CLOSE_TITLE = ETS_ABANCART_CLOSE_TITLE || 'Close',
    ETS_ABANCART_QUEUE = {},
    ETS_ABANCART_REQUEST = {}
;
var notification;
var ets_ab_fn = {
    init: function () {
        ets_ab_fn.initCampaign();
    },
    initCampaign: function () {
        if (ETS_ABANCART_CAMPAIGNS) {
            ETS_ABANCART_CAMPAIGNS.forEach(function (item) {
                ets_ab_fn.setCampaign(item);
            });
        }
        if (ETS_ABANCART_COOKIE_CAMPAIGNS) {
            ETS_ABANCART_COOKIE_CAMPAIGNS.forEach(function (item) {
                ets_ab_fn.setCampaignCookie(item);
            });
        }
    },
    clearTimeout: function (id, isRemove) {
        if (typeof ETS_ABANCART_QUEUE[id] !== "undefined") {
            clearTimeout(ETS_ABANCART_QUEUE[id]);
            if (isRemove)
                delete ETS_ABANCART_QUEUE[id];
        }
    },
    setCampaign: function (item) {
        ets_ab_fn.clearTimeout(item.id_ets_abancart_reminder);
        ETS_ABANCART_QUEUE[item.id_ets_abancart_reminder] = setTimeout(
            function () {
                ets_ab_fn.request(parseInt(item.id_ets_abancart_reminder), item.campaign_type);
            }
            , parseInt((parseFloat(item.lifetime) > 0 ? parseFloat(item.lifetime) * 1000 : 0))
        );
    },
    setCampaignCookie: function (item) {
        ets_ab_fn.clearTimeout(item.id_ets_abancart_reminder);
        var timeOut = 0;
        if (typeof item.lifetime !== "undefined" || parseFloat(item.redisplay) > 0) {
            if (typeof item.lifetime !== "undefined") {
                timeOut = item.lifetime * 1000;
            } else {
                timeOut = parseFloat(item.redisplay) > 0 ? parseFloat(item.redisplay) * 1000 : 0;
            }
            ETS_ABANCART_QUEUE[item.id_ets_abancart_reminder] = setTimeout(
                function () {
                    ets_ab_fn.request(parseInt(item.id_ets_abancart_reminder), item.type);
                }
                , timeOut
            );
        }
    },
    mergeCampaign: function (reminder, campaigns, action, isCookie) {
        var flag = 0;
        if (campaigns.length > 0) {
            campaigns.forEach(function (item) {
                if (isCookie) {
                    if (item.length > 0) {
                        item.forEach(function (rem) {
                            if (parseInt(rem.id_ets_abancart_reminder) === parseInt(reminder.id_ets_abancart_reminder)) {
                                flag = 1;
                                return true;
                            }
                        });
                    }
                } else {
                    if (parseInt(item.id_ets_abancart_reminder) === parseInt(reminder.id_ets_abancart_reminder)) {
                        flag = 1;
                    }
                }
                if (flag > 0)
                    return true;
            });
        }
        if (flag < 1) {
            switch (action) {
                case 'add':
                    if (isCookie)
                        ets_ab_fn.setCampaignCookie(reminder);
                    else
                        ets_ab_fn.setCampaign(reminder);
                    break;
                case 'delete':
                    ets_ab_fn.removeCampaign(reminder);
                    break;
            }
        }
    },
    restCampaigns: function (campaigns) {
        if (ETS_ABANCART_CAMPAIGNS.length > 0) {
            ETS_ABANCART_CAMPAIGNS.forEach(function (item) {
                ets_ab_fn.mergeCampaign(item, campaigns, 'delete');
            })
        }
        if (campaigns.length > 0) {
            campaigns.forEach(function (item) {
                ets_ab_fn.mergeCampaign(item, ETS_ABANCART_CAMPAIGNS, 'add');
            });
        }
    },
    restCookieCampaigns: function (campaigns) {
        if (ETS_ABANCART_COOKIE_CAMPAIGNS.length > 0) {
            ETS_ABANCART_COOKIE_CAMPAIGNS.forEach(function (item) {
                ets_ab_fn.mergeCampaign(item, campaigns, 'delete', true);
            });
        }
        if (campaigns.length > 0) {
            campaigns.forEach(function (item) {
                if (item.length > 0) {
                    item.forEach(function (rem) {
                        ets_ab_fn.mergeCampaign(rem, ETS_ABANCART_COOKIE_CAMPAIGNS, 'add');
                    });
                }
            });
        }
    },
    removeCampaign: function (id) {
        ets_ab_fn.clearTimeout(id, true);
        delete ETS_ABANCART_REQUEST[id];
    },
    ajaxState: function () {
        var flag = 0,
            first = 0,
            requestQueue = Object.keys(ETS_ABANCART_REQUEST);
        if (requestQueue.length > 0) {
            requestQueue.forEach(function (key) {
                if (parseInt(first) <= 0)
                    first = ETS_ABANCART_REQUEST[key].id;
                if (ETS_ABANCART_REQUEST[key].state > 0) {
                    flag = 1;
                    return true;
                }
            });
        }
        return flag <= 0 ? first : 0;
    },
    request: function (id, campaign_type) {
        if (ETS_ABANCART_LINK_AJAX && parseInt(id) > 0) {
            ETS_ABANCART_REQUEST[id] = {
                type: 'post',
                url: ETS_ABANCART_LINK_AJAX,
                dataType: 'json',
                data: 'renderDisplay&id_ets_abancart_reminder=' + id + '&campaign_type=' + campaign_type,
                state: 0,
                id: id
            };
            var nextId = ets_ab_fn.ajaxState();
            if (parseInt(nextId) > 0)
                ets_ab_fn.doRequestAjax(id);
        }
    },
    doRequestAjax: function (id) {
        var request = ETS_ABANCART_REQUEST[id];
        request.state = 1;
        request.success = function (json) {
            delete ETS_ABANCART_REQUEST[id];
            if (json) {
                if (json.campaigns)
                    ets_ab_fn.restCampaigns(json.campaigns);
                if (json.cookies)
                    ets_ab_fn.restCookieCampaigns(json.cookies);
                if (json.redisplay < 0 && json.id_ets_abancart_reminder > 0) {
                    ets_ab_fn.removeCampaign(json.id_ets_abancart_reminder);
                } else {
                    switch (json.type) {
                        case 'popup':
                            ets_ab_fn.popup(json, id);
                            break;
                        case 'bar':
                            ets_ab_fn.bar(json, id);
                            break;
                        case 'browser':
                            ets_ab_fn.browser(json, id);
                            break;
                    }
                }
            }
        }
        $.ajax(request);
    },
    views: function (id, json, group_class) {
        if (id && json) {
            // FIRST:
            var overloadEl = '.ets_abancart_' + json.type + '_overload';
            if ($('.ets_abancart_' + json.type + '_overload').length <= 0) {
                $('body').prepend('<div class="ets_abancart_' + json.type + '_overload ' + group_class + ' ets_abancart_overload" data-id="' + id + '" data-type="' + json.type + '" ' + (json.type !== 'popup' ? 'style="background-color: ' + json.background_color + '; color: ' + json.text_color + '"' : '') + '><div class="ets_abancart_width"><div class="ets_table"><div class="ets_tablecell"><div class="ets_abancart_container"><div class="ets_abancart_close" title="' + ETS_ABANCART_CLOSE_TITLE + '"></div><div class="ets_abancart_wrapper"></div></div></div></div></div></div>');
            }
            // NEXT:
            var _container = $('body .ets_abancart_' + json.type + '_overload');
            _container
                .attr({'data-id': id, 'data-type': json.type})
                .addClass('active')
                .find('.ets_abancart_wrapper')
                .html('<div class="ets-ac-popup-body" style="' + (json.type === 'popup' && json.popup_body_bg ? 'background-color: ' + json.popup_body_bg + ';' : '') + '">' + json.html + '</div>')
                .prepend((json.type === 'popup' ? '<h4 class="ets_abancart_title" style="' + (json.header_bg ? 'background-color: ' + json.header_bg + ';' : '') + (json.header_text_color ? 'color: ' + json.header_text_color + ';' : '') + (json.header_height ? 'height: ' + json.header_height + 'px;' : '') + (json.header_font_size ? 'font-size: ' + json.header_font_size + 'px;' : '') + '">' + json.title + '</h4>' : ''))
            ;
            /*---HIGHLIGHT BAR---*/
            if (json.type !== 'popup') {
                _container.attr('style', 'background-color: ' + json.background_color + '; color: ' + json.text_color);
            }
            var selectorContainer = _container.find('.ets_abancart_container');
            if (json.type === 'bar') {
                selectorContainer = _container.find('.ets_abancart_width');
            }
            selectorContainer.css('margin', '0 auto');
            if (json.popup_width)
                selectorContainer.css('max-width', json.popup_width + 'px');
            if (json.popup_height) {
                selectorContainer.css('height', json.popup_height + 'px');
                selectorContainer.css('min-height', json.popup_height + 'px');
            }
            if (json.border_radius)
                selectorContainer.css('border-radius', json.border_radius + 'px');
            if (json.border_width) {
                selectorContainer.css('border-width', json.border_width + 'px');
                selectorContainer.css('border-style', 'solid');
            }
            if (json.border_color)
                selectorContainer.css('border-color', json.border_color);
            if (json.close_btn_color) {
                _container.find('.ets_abancart_close').find('style').remove();
                _container.find('.ets_abancart_close').append('<style>' + overloadEl + ' .ets_abancart_close:before,' + overloadEl + ' .ets_abancart_close:after{background-color: ' + json.close_btn_color + ';}</style>');
            }
            if (json.vertical_align) {
                $(overloadEl + ' .ets-ac-popup-body p, ' + overloadEl + ' .ets-ac-popup-body a,' + overloadEl + ' .ets-ac-popup-body div:not(.ets_abancart_product_list_table)').css('text-align', 'inherit');
                $(overloadEl + ' .ets-ac-popup-body').css('text-align', json.vertical_align);
            }
            if (json.font_size) {
                $('' + overloadEl + ' .ets-ac-popup-body,' + overloadEl + ' .ets-ac-popup-body p, ' + overloadEl + ' .ets-ac-popup-body a,' + overloadEl + ' .ets-ac-popup-body div').css('font-size', json.font_size + 'px');
            }
            if (json.padding) {
                if (json.popup_width) {
                    if (json.type === 'bar') {
                        selectorContainer.css('padding', json.padding + 'px');
                    } else
                        $('' + overloadEl + ' .ets-ac-popup-body').css('padding', json.padding + 'px');
                }

            }
            if (json.overlay_bg) {
                var color = json.overlay_bg;
                if (json.overlay_bg_opacity) {
                    var rgbColor = etsAcHexToRgb(json.overlay_bg);
                    color = 'rgba(' + rgbColor.r + ',' + rgbColor.g + ',' + rgbColor.b + ',' + json.overlay_bg_opacity + ')';
                }
                $('.ets_abancart_popup_overload').css('background-color', color);
            }

            ets_ab_fn_shortcode.countdown();
            ets_ab_fn_shortcode.countdown2();
            etsAbancartDatepickerLoad();
            etsAcOnLoadRecaptcha();
        }
    },
    popup: function (json, id) {
        ets_ab_fn.views(id, json, 'ets_abancart_popup');
    },
    bar: function (json, id) {
        ets_ab_fn.views(id, json, '');
    },
    browser: function (json, id) {
        if (json && id) {
            if (!("Notification" in window)) {
                return false;
            } else if (Notification.permission === "granted") {
                ets_ab_fn.setNotification(json, id);
            } else if (Notification.permission !== "denied" && ETS_ABANCART_HAS_BROWSER) {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        ets_ab_fn.setNotification(json, id);
                    }
                });
            }
        }
    },
    setNotification: function (json, id) {
        notification = new Notification(json.title, {icon: json.icon, body: json.html, requireInteraction: true});
        notification.onclick = function () {
            if (typeof json.code !== "undefined" && json.code) {
                $.ajax({
                    type: 'post',
                    url: ETS_ABANCART_LINK_AJAX,
                    dataType: 'json',
                    data: 'add_cart_rule&discount_code=' + json.code,
                    success: function (json) {
                        if (json) {
                            if (json.errors) {
                                showErrorMessage(json.errors)
                            } else {
                                window.location.href = json.link_checkout;
                            }
                        }
                    },
                });
            }
        };
        notification.onclose = function () {
            if (id) {
                $.ajax({
                    type: 'post',
                    url: ETS_ABANCART_LINK_AJAX,
                    dataType: 'json',
                    data: 'type=browser&redisplay=1&id=' + id,
                    success: function (json) {
                        if (json) {
                            ets_ab_fn.close('browser', json);
                        }
                    }
                });
            }
        };
    },
    close: function (type, json) {
        $('body .ets_abancart_' + type + '_overload.active').remove();
        if (parseFloat(json.redisplay) > 0) {
            ETS_ABANCART_QUEUE[json.id_ets_abancart_reminder] = setTimeout(function () {
                ets_ab_fn.request(json.id_ets_abancart_reminder, type);
            }, parseFloat(json.redisplay) * 1000);
        }
    },
};

$(document).ready(function () {
    $(document).mouseup(function (e)
    {
        var mce_container =$('.ets_abancart_overload.active .ets_abancart_container');
        if (!mce_container.is(e.target) && mce_container.has(e.target).length === 0 )
        {
            $('.ets_abancart_overload.active').removeClass('active');
        }
    });
    etsAbancartDatepickerLoad();
    if (typeof ETS_ABANCART_SUPERSPEED_ENABLED === typeof undefined || parseInt(ETS_ABANCART_SUPERSPEED_ENABLED) < 1 || $('.ets_speed_dynamic_hook').length < 1) {
        ets_ab_fn.init();
    }
    $(document).on("hooksLoaded", function () {
        Object.keys(ETS_ABANCART_QUEUE).forEach(function (i) {
            clearTimeout(ETS_ABANCART_QUEUE[i]);
            delete ETS_ABANCART_QUEUE[i];
        });
        ets_ab_fn.init();
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        var nextId = ets_ab_fn.ajaxState();
        if (parseInt(nextId) > 0)
            ets_ab_fn.doRequestAjax(nextId);
    });
    $(document).on('click', '.ets_abancart_close:not(.leave), .ets_abancart_no_thanks', function (ev) {
        ev.preventDefault();
        var btn = $(this),
            overload = btn.parents('.ets_abancart_overload'),
            id = overload.attr('data-id'),
            type = overload.attr('data-type');
        $('body .ets_abancart_' + type + '_overload.active').remove();
        if (!btn.hasClass('active') && ETS_ABANCART_LINK_AJAX && id) {
            $.ajax({
                type: 'post',
                url: ETS_ABANCART_LINK_AJAX,
                dataType: 'json',
                data: 'type=' + type + '&redisplay=1&id=' + id + (btn.hasClass('ets_abancart_no_thanks') ? '&closed=1' : ''),
                success: function (json) {
                    if (json) {
                        ets_ab_fn.close(type, json);
                    }
                }
            });
        }
    });
});