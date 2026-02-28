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

var ETS_ABANCART_LINK_SHOPPING_CART = ETS_ABANCART_LINK_SHOPPING_CART || '';
if (typeof ETS_ABANCART_LIFE_TIME == "undefined")
    var ETS_ABANCART_LIFE_TIME = -1;
var ets_ab_fn_shopping_cart = {
    init: function () {
        if (typeof ETS_ABANCART_CLIENT_OFF_CART === typeof undefined || !ETS_ABANCART_CLIENT_OFF_CART) {
            ets_ab_fn_shopping_cart.saveCart();
        }
    },
    saveCart: function () {
        if ((ETS_ABANCART_LIFE_TIME >= 0 || $('#ets_abancart_cart_save.active').length > 0) && ETS_ABANCART_LINK_SHOPPING_CART) {
            setTimeout(function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ETS_ABANCART_LINK_SHOPPING_CART,
                    data: 'init',
                    success: function (json) {
                        $('#ets_abancart_cart_save.active').removeClass('active');
                        if (json) {
                            if ($('body .ets_abancart_shopping_cart_overload').length <= 0) {
                                $('body').prepend('<div class="ets_abancart_shopping_cart_overload ets_abancart_overload"><div class="ets_abancart_wrapper"></div></div>');
                            }
                            if (json.html) {
                                $('body .ets_abancart_shopping_cart_overload').addClass('active').find('.ets_abancart_wrapper').html(json.html);
                            } else if (json.home_url) {
                                window.location.href = json.home_url;
                            }
                        }
                    },
                    error: function () {
                        $('#ets_abancart_cart_save.active').removeClass('active');
                    }
                });
            }, $('#ets_abancart_cart_save.active').length > 0 ? 0 : ETS_ABANCART_LIFE_TIME * 1000);
        }
    },
    exitPopupSaveCart: function (notReDisplay) {
        var _notReDisplay = notReDisplay || true;
        $('.ets_abancart_shopping_cart_overload.active').removeClass('active');
        if (_notReDisplay && ETS_ABANCART_LINK_SHOPPING_CART) {
            $('#save_cart_form .bootstrap').remove();
            $.ajax({
                type: 'post',
                url: ETS_ABANCART_LINK_SHOPPING_CART,
                dataType: 'json',
                data: 'ajax=1&offCart',
                success: function () {
                },
                error: function () {
                }
            });
        }
    },
    exitPopupCart: function () {
        $('.ets_abancart_display_shopping_cart_overload.active').removeClass('active');
    },
}
$(document).ready(function () {
    if (typeof ETS_ABANCART_SUPERSPEED_ENABLED === typeof undefined || parseInt(ETS_ABANCART_SUPERSPEED_ENABLED) < 1 || $('.ets_speed_dynamic_hook').length < 1) {
        ets_ab_fn_shopping_cart.init();
    }
    $(document).on("hooksLoaded", function () {
        ets_ab_fn_shopping_cart.init();
    });
    $(document).on('click', '.ets_abancart_shopping_cart_overload .ets_abancart_create_account', function (ev) {
        ev.preventDefault();
        if ($('#id_customer').length > 0 && parseInt($('#id_customer').val()) <= 0) {
            $('.ets_abancart_form_login').fadeOut();
            $('.ets_abancart_form_create').fadeIn();
        }
    });
    $(document).on('click', '.ets_abancart_view_shopping_cart', function (ev) {
        ev.preventDefault();
        var btn = $(this);
        if (!btn.hasClass('active') && btn.attr('href') !== '') {
            btn.addClass('active');
            $.ajax({
                type: 'POST',
                url: btn.attr('href'),
                dataType: 'json',
                data: 'ajax=1',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            if (typeof json.my_account_link !== typeof undefined)
                                window.location.href = json.my_account_link;
                        } else {
                            if ($('body .ets_abancart_display_shopping_cart_overload').length <= 0) {
                                $('body').prepend('<div class="ets_abancart_display_shopping_cart_overload ets_abancart_popup ets_abancart_overload"><div class="ets_table"><div class="ets_tablecell"><div class="ets_abancart_container"><div class="ets_abancart_close"></div><div class="ets_abancart_wrapper"></div></div></div></div></div>');
                            }
                            $('body .ets_abancart_display_shopping_cart_overload').addClass('active').find('.ets_abancart_wrapper').html(json.html);
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_abancart_display_shopping_cart_overload .ets_abancart_close, .ets_abancart_display_shopping_cart_overload .ets_abancart_cancel', function (ev) {
        ev.preventDefault();
        ets_ab_fn_shopping_cart.exitPopupCart();
    });
    $(document).on('click', '.ets_abancart_load_this_cart', function (ev) {
        ev.preventDefault();
        var btn = $(this);
        if (!btn.hasClass('active') && btn.attr('href') !== '') {
            btn.addClass('active');
            $.ajax({
                type: 'POST',
                url: btn.attr('href'),
                dataType: 'json',
                data: 'ajax=1',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            $('body .ets_abancart_display_shopping_cart_overload').prepend(json.errors);
                        else
                            window.location.href = json.link_checkout;
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
    $(document).on('change', 'input#cart_name', function (e) {
        if ($(this).val() !== '') {
            $(this).removeClass('error');
        } else {
            $(this).addClass('error');
        }
    });
    $(document).on('click', '.ets_abancart_shopping_cart_overload button[name=submitLogin]', function (ev) {
        ev.preventDefault();
        var btn = $(this), form = $('#login_form');
        if (!btn.hasClass('active') && form.attr('action')) {
            btn.addClass('active');
            var formData = new FormData(form.get(0));
            formData.append('cart_name', $('#cart_name').val());
            formData.append('ajax', 1);
            $('#login_form .bootstrap').remove();
            $.ajax({
                type: 'post',
                url: form.attr('action'),
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            form.prepend(json.errors);
                        else
                            window.location.reload();
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_abancart_shopping_cart_overload button[name=submitCreate]', function (ev) {
        ev.preventDefault();
        var btn = $(this), form = $('#create_form');
        if (!btn.hasClass('active') && form.attr('action')) {
            btn.addClass('active');
            var formData = new FormData(form.get(0));
            formData.append('cart_name', $('#cart_name').val());
            formData.append('ajax', 1);
            $('#login_form .bootstrap').remove();
            $.ajax({
                type: 'post',
                url: form.attr('action'),
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            form.prepend(json.errors);
                        else
                            window.location.reload();
                    }
                },
                error: function () {
                    btn.removeClass('active');
                    window.location.reload();
                }
            });
        }
    });
    $(document).on('click', '.ets_abancart_shopping_cart_overload .ets_abancart_close', function (ev) {
        ev.preventDefault();
        ets_ab_fn_shopping_cart.exitPopupSaveCart();
    });
    $(document).on('click', '#ets_abancart_cart_save', function (ev) {
        ev.preventDefault();
        if (!$(this).hasClass('active')) {
            $(this).addClass('active');
            ets_ab_fn_shopping_cart.saveCart();
        }
    });
    $(document).on('click', '.ets_abancart_shopping_cart_overload button[id=submit_cart]', function (ev) {
        ev.preventDefault();

        var btn = $(this), form = $('#save_cart_form');
        btn.parents('form#save_cart_form').find('input.cart_name').removeClass('error');
        if (!btn.hasClass('active') && form.attr('action')) {
            btn.addClass('active');
            var formData = new FormData(form.get(0));
            formData.append('ajax', 1);
            $('#save_cart_form .bootstrap').remove();
            $.ajax({
                type: 'post',
                url: form.attr('action'),
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.not_logged && parseInt($('#id_customer').val()) <= 0) {
                            $('.ets_abancart_form_login').fadeIn();
                            $('.ets_abancart_form_save_cart').fadeOut();
                        } else if (json.errors) {
                            form.prepend(json.errors);
                            btn.parents('form#save_cart_form').find('input#cart_name').addClass('error').focus();
                        } else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                            $('#ets_abancart_cart_save').remove();
                            ets_ab_fn_shopping_cart.exitPopupSaveCart(false);
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_abancart_display_shopping_cart_overload .ets_abancart_close, .ets_abancart_display_shopping_cart_overload .ets_abancart_cancel', function (ev) {
        ev.preventDefault();
        ets_ab_fn_shopping_cart.exitPopupCart();
    });
    $(document).on('click', '.ets_abancart_delete_cart, .ets_abancart_delete', function (ev) {
        var btn = $(this);
        if (!confirm(btn.data('confirm'))) {
            ev.preventDefault();
        }
    });
    $(document).keyup(function (e) {
        if (e.keyCode === 27) {
            ets_ab_fn_shopping_cart.exitPopupCart();
            ets_ab_fn_shopping_cart.exitPopupSaveCart();
        }
    });
    $(document).mouseup(function (e) {

        var displayShoppingCart = $('.ets_abancart_display_shopping_cart_overload.active .ets_abancart_container'),
            displayCartSave = $('.ets_abancart_shopping_cart_overload.active .ets_abancart_shopping_cart');

        if (displayShoppingCart.length > 0 && !displayShoppingCart.is(e.target) && displayShoppingCart.has(e.target).length === 0) {
            ets_ab_fn_shopping_cart.exitPopupCart();
        }
        if (displayCartSave.length > 0 && !displayCartSave.is(e.target) && displayCartSave.has(e.target).length === 0) {
            ets_ab_fn_shopping_cart.exitPopupSaveCart();
        }
    });
});