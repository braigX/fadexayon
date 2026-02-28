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

var ETS_ABANCART_LINK_AJAX = ETS_ABANCART_LINK_AJAX || '',
    ets_abancart_timeout = false,
    ets_abancart_delay = 0,
    ets_abancart_disable_keydown = false,
    ETS_ABANCART_LEAVE_DISPLAY = 1
;

document.documentElement.addEventListener('mouseleave', ets_abancart_mouseleave);
document.documentElement.addEventListener('mouseenter', ets_abancart_mouseenter);
document.documentElement.addEventListener('keydown', ets_abancart_keydown);

var ets_ab_fn_leave_website = {
    exitPopupLeave: function () {
        $('.ets_abancart_leave_website_overload.active').removeClass('active');
        $('.ets_abancart_leave_website_overload .ets_abancart_wrapper.active').remove();
    },
};

function ets_abancart_leavewebsite() {
    var _overload = $('.ets_abancart_leave_website_overload:not(.disabled)');
    if (ETS_ABANCART_LEAVE_DISPLAY && ETS_ABANCART_LINK_AJAX && _overload.length > 0 && _overload.find('.ets_abancart_wrapper').length && !_overload.hasClass('active') && !_overload.hasClass('loading')) {

        _overload.addClass('loading');
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: ETS_ABANCART_LINK_AJAX,
            data: 'leave&ajax=1',
            success: function (json) {
                _overload.removeClass('loading');
                if (json) {
                    if (typeof json.redisplay !== "undefined" && parseInt(json.redisplay) < 0) {
                        ETS_ABANCART_LEAVE_DISPLAY = 0
                        return;
                    }
                    if (json.errors) {
                        _overload
                            .removeClass('active')
                            .addClass('disabled');
                        ets_abancart_mouseenter();
                    } else {
                        var _wrapper = _overload.find('.ets_abancart_wrapper').clone(true),
                            _html = _wrapper.length ? _wrapper.html() : '';
                        $.each(json, function (p, item) {
                            var pattern = p.replace(/([\[\]])/g, '\\$1'),
                                regExp = new RegExp(pattern, 'g');
                            _html = _html.replace(regExp, item);
                        });
                        _overload.find('.ets_abancart_wrapper').addClass('form-original').after(
                            _wrapper
                                .html(_html)
                                .addClass('active')
                        );
                        _overload.addClass('active');
                        _overload.find('.ets_abancart_wrapper.form-original').remove();
                        ets_ab_fn_shortcode.countdown();
                        ets_ab_fn_shortcode.countdown2();
                        etsAbancartDatepickerLoad();

                        if (json.background_color) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css('background-color', json.background_color);
                        }
                        if (json.popup_width) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css('max-width', json.popup_width + 'px');
                        }
                        if (json.popup_height) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css('height', json.popup_height + 'px');
                        }
                        if (json.border_radius) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css({
                                'border-radius': json.border_radius + 'px',
                                'overflow': 'hidden'
                            });
                        }
                        if (json.border_width) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css({
                                'border-width': json.border_width + 'px',
                                'border-style': 'solid'
                            });
                        }
                        if (json.border_color) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css('border-color', json.border_color);
                        }
                        if (json.padding) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container').css('border-color', json.padding + 'px');
                        }
                        if (json.close_btn_color) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container .ets_abancart_close').find('style').remove();
                            $('.ets_abancart_leave_website_overload  .ets_abancart_container .ets_abancart_close').append('<style rel="stylesheet">.ets_abancart_leave_website_overload  .ets_abancart_container .ets_abancart_close:after,.ets_abancart_leave_website_overload  .ets_abancart_container .ets_abancart_close:before{background-color: ' + json.close_btn_color + ';}</style>');
                        }
                        if (json.font_size) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_wrapper div,.ets_abancart_leave_website_overload  .ets_abancart_wrapper p,.ets_abancart_leave_website_overload  .ets_abancart_wrapper a').css('font-size', json.font_size + 'px');
                        }
                        if (json.vertical_align) {
                            $('.ets_abancart_leave_website_overload  .ets_abancart_wrapper p,.ets_abancart_leave_website_overload  .ets_abancart_wrapper a,.ets_abancart_leave_website_overload  .ets_abancart_wrapper div:not(.ets_abancart_product_list_table)').css('text-align', 'inherit');
                            $('.ets_abancart_leave_website_overload  .ets_abancart_wrapper').css('text-align', json.vertical_align);
                        }
                        if (json.overlay_bg) {
                            var color = json.overlay_bg;
                            if (json.overlay_bg_opacity) {
                                var colorRgb = etsAcHexToRgb(json.overlay_bg);
                                if (colorRgb) {
                                    color = 'rgba(' + colorRgb.r + ',' + colorRgb.g + ',' + colorRgb.b + ',' + json.overlay_bg_opacity + ')';
                                }
                            }
                            $('.ets_abancart_leave_website_overload').css('background-color', color);
                        }
                        etsAcCheckHasCaptcha(_overload.find('.ets_abancart_wrapper'));
                        etsAcOnLoadRecaptcha();

                    }
                }
            },
            error: function () {
                _overload.removeClass('loading');
            }
        });
    }
}

function etsAcCheckHasCaptcha(el) {
    if (el.find('input[name="captcha_type"]').length) {
        if (el.find('input[name="captcha_type"]').first().val() == 'v2') {
            el.prepend('<script src="https://www.google.com/recaptcha/api.js" async defer></script>');
        } else if (el.find('input[name="captcha_type"]').first().val() == 'v3') {
            var captchaKey = el.find('input[name="captcha_site_key"]').first().val();
            el.prepend('<script src="https://www.google.com/recaptcha/api.js?render=' + captchaKey + '" async defer></script>');
        }
    }
}

function isIE() {
    ua = navigator.userAgent;
    /* MSIE used to detect old browsers and Trident used to newer ones*/
    var is_ie = ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1;

    return is_ie;
}

function ets_abancart_mouseleave(event) {
    var y, _ie = isIE();
    y = event.clientY || event.screenY || event.pageY;
    if ((y >= 0 && !_ie) || (_ie && y > 5)) {
        return;
    }
    ets_abancart_timeout = setTimeout(ets_abancart_leavewebsite, 0);
}

function ets_abancart_mouseenter() {
    if (ets_abancart_timeout) {
        clearTimeout(ets_abancart_timeout);
        ets_abancart_timeout = null;
    }
}

function ets_abancart_keydown(e) {
    if (ets_abancart_disable_keydown || !e.metaKey || e.keyCode !== 76) {
        return;
    }
    ets_abancart_disable_keydown = true;
    ets_abancart_timeout = setTimeout(ets_abancart_leavewebsite, ets_abancart_delay);
}

$(document).mouseleave(function () {
    //setTimeout(ets_abancart_leavewebsite, 0)
});

$(document).ready(function () {
    $(document).on('click', '.ets_abancart_leave_website_overload .ets_abancart_close', function (ev) {
        ev.preventDefault();
        ets_ab_fn_leave_website.exitPopupLeave();
        var btn = $(this);
        if (!btn.hasClass('active') && ETS_ABANCART_LINK_AJAX) {
            $.ajax({
                type: 'post',
                url: ETS_ABANCART_LINK_AJAX,
                dataType: 'json',
                data: 'leave_closed',
                success: function (json) {
                    btn.removeClass('active');
                },
            });
        }
    });
    $(document).on('click', '.ets_abancart_leave_website_overload .ets_abancart_no_thanks', function (ev) {
        ev.preventDefault();
        var btn = $(this),
            overload = btn.parents('.ets_abancart_overload');
        overload.remove();
        if (!btn.hasClass('active') && ETS_ABANCART_LINK_AJAX) {
            btn.addClass('active');
            $.ajax({
                type: 'post',
                url: ETS_ABANCART_LINK_AJAX,
                dataType: 'json',
                data: 'offLeave',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
});