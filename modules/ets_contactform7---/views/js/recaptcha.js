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
if (typeof ets_ct7_recaptcha_enabled!= 'undefined') {
    if (!ets_ct7_recaptcha_v3) {

        var recaptchaWidgets = [];
        var recaptchaCallback = function () {

            var forms = document.getElementsByTagName('form');
            var pattern = /(^|\s)ets-ct7-recaptcha(\s|$)/;
            for (var i = 0; i < forms.length; i++) {
                var divs = forms[i].getElementsByTagName('div');

                for (var j = 0; j < divs.length; j++) {
                    console.log('div capcha', divs[j])
                    var sitekey = divs[j].getAttribute('data-key');

                    if (divs[j].className && divs[j].className.match(pattern) && sitekey) {
                        var params = {
                            'sitekey': sitekey,
                            'type': divs[j].getAttribute('data-type'),
                            'size': divs[j].getAttribute('data-size'),
                            'theme': divs[j].getAttribute('data-theme'),
                            'badge': divs[j].getAttribute('data-badge'),
                            'tabindex': divs[j].getAttribute('data-tabindex')
                        };

                        var callback = divs[j].getAttribute('data-callback');

                        if (callback && 'function' == typeof window[callback]) {
                            params['callback'] = window[callback];
                        }

                        var expired_callback = divs[j].getAttribute('data-expired-callback');

                        if (expired_callback && 'function' == typeof window[expired_callback]) {
                            params['expired-callback'] = window[expired_callback];
                        }
                        var widget_id = grecaptcha.render(divs[j], params);
                        console.log('widget_id: ', widget_id)
                        recaptchaWidgets.push(widget_id);
                        break;
                    }
                }
            }
        };
        $(document).on('wpcf7submit',function(){
            for (var i = 0; i < recaptchaWidgets.length; i++) {
                grecaptcha.reset(recaptchaWidgets[i]);
            }
        });
    }
    else {
//v3.
        (function ($) {
            var ets_ct7_v3_render_clientID = {};
            var ct7_re_captcha_v3 = function (form) {
                if ($('.wpcf7 form:not(.g-loaded)').length <= 0 && typeof ets_ct7_recaptcha_key === "undefined" && !ets_ct7_recaptcha_key )
                    return;
                var g_captcha = form.find('.ets-ct7-recaptcha.wpcf7-recaptcha').eq(0);
                if (g_captcha.length > 0 && !form.hasClass('run-recaptcha')) {
                    form.addClass('run-recaptcha');
                    var theme = g_captcha[0].getAttribute('data-theme') ? g_captcha[0].getAttribute('data-theme') : 'light';
                    var badge = g_captcha[0].getAttribute('data-badge') ? g_captcha[0].getAttribute('data-badge') : 'bottomright';
                    grecaptcha.ready(function () {
                        var renderClientId;
                        if (typeof ets_ct7_v3_render_clientID[g_captcha[0].id] === "undefined") {
                            renderClientId = grecaptcha.render(g_captcha[0], {
                                'sitekey': ets_ct7_recaptcha_key,
                                'theme': theme,
                                'badge': badge,
                                'size': 'invisible',
                            });
                            ets_ct7_v3_render_clientID[g_captcha[0].id] = renderClientId;
                        } else {
                            renderClientId = ets_ct7_v3_render_clientID[g_captcha[0].id];
                            grecaptcha.reset(ets_ct7_v3_render_clientID[g_captcha[0].id]);
                        }
                        grecaptcha.execute(renderClientId, {action: 'contact'}).then(function (token) {
                            if (token) {
                                if (g_captcha.find('input[name=g-recaptcha-response]').length > 0) {
                                    g_captcha.find('input[name=g-recaptcha-response]').val(token);
                                } else {
                                    g_captcha.append('<input type="hidden" class="g-recaptcha-response" name="g-recaptcha-response" value="' + token + '"/>');
                                }
                                form.addClass('g-loaded');
                                ct7_re_captcha_v3(form);
                            }
                        });
                    });
                }
            };
            $(document).on('wpcf7submit',function(){
                var form = $('.wpcf7 form');
                if (form) {
                    form.removeClass('g-loaded');
                    form.removeClass('run-recaptcha');
                    if (ets_ct7_recaptcha_v3) {
                        ct7_re_captcha_v3(form);
                    } else if (typeof grecaptcha !== "undefined") {
                        grecaptcha.reset();
                    }
                }
            });
            $(document).ready(function () {
                if (ets_ct7_recaptcha_v3) {
                    ct7_re_captcha_v3($('.wpcf7 form:not(.g-loaded)'));
                }
            });
        })(jQuery);
    }
}