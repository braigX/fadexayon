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

function etsAbancartDatepickerLoad() {
    let datepicker = $('.ets_ac_datepicker'), datetimepicker = $('.ets_ac_datetimepicker')
    if (datepicker.length) {
        datepicker.removeClass('hasDatepicker');
        datepicker.datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
    }
    if (datetimepicker.length) {
        datetimepicker.removeClass('hasDatepicker');
        datetimepicker.datetimepicker({
            prevText: '',
            nextText: '',
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd h:i',
            currentText: 'Now',
            closeText: 'Done',
            ampm: false,
            amNames: ['AM', 'A'],
            pmNames: ['PM', 'P'],
            timeFormat: 'hh:mm:ss tt',
            timeSuffix: '',
            timeOnlyTitle: 'Choose Time',
            timeText: 'Time',
            hourText: 'Hour',
            minuteText: 'Minute',
        });
    }
}

var ets_ab_fn_shortcode = {
    countdown: function () {
        var clock = $('.ets_abancart_count_down_clock');
        var style = clock.attr('data-style') || '';
        if (clock.length > 0) {
            clock.countdown(parseInt(clock.data('date')) * 1000).on('update.countdown', function (event) {
                $(this).html(event.strftime(''
                    + (event.offset.weeks > 0 ? '<span class="ets_abancart_countdown weeks" style="' + style + '"><span>%-w</span> week%!w </span>' : '')
                    + (event.offset.days > 0 ? '<span class="ets_abancart_countdown days" style="' + style + '"><span>%-d</span> day%!d </span>' : '')
                    + '<span class="ets_abancart_countdown hours" style="' + style + '"><span>%H</span> hr </span>'
                    + '<span class="ets_abancart_countdown minutes" style="' + style + '"><span>%M</span> min </span>'
                    + '<span class="ets_abancart_countdown seconds" style="' + style + '"><span>%S</span> sec </span>'));
            });
        }
    },
    countdown2: function () {
        var clock = $('.ets_ac_evt_countdown2');
        var style = clock.attr('data-style') || '';
        if (clock.length > 0) {
            clock.countdown(parseInt(clock.data('date')) * 1000).on('update.countdown', function (event) {
                $(this).html(event.strftime(''
                    + (event.offset.weeks > 0 ? '<span class="ets_ac_countdown2 weeks" style="' + style + '"><span>%-w</span> week%!w </span>' : '')
                    + (event.offset.days > 0 ? '<span class="ets_ac_countdown2 days" style="' + style + '"><span>%-d</span> day%!d </span>' : '')
                    + '<span class="ets_ac_countdown2 hours" style="' + style + '"><span>%H</span> hr </span>'
                    + '<span class="ets_ac_countdown2 minutes" style="' + style + '"><span>%M</span> min </span>'
                    + '<span class="ets_ac_countdown2 seconds" style="' + style + '"><span>%S</span> sec </span>'));
            });
        }
    },
    copyToClipboard: function (el) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(el.text().trim()).select();
        document.execCommand("copy");
        $temp.remove();
        console.log(el.text());
        showSuccessMessage(ETS_ABANCART_COPIED_MESSAGE);
        setTimeout(function () {
            el.removeClass('copy');
        }, 300);
    },
};

$(document).ready(function () {

    etsAbancartDatepickerLoad();

    $(document).on('click', '.ets_abancart_short_code_url', function (ev) {
        ev.preventDefault();
        ets_ab_fn_shortcode.copyToClipboard($(this));
    });

    $(document).on('click', '.ets_abancart_box .ets_abancart_box_discount', function (ev) {
        ev.preventDefault();
        ets_ab_fn_shortcode.copyToClipboard($(this));
    });

    $(document).on('click', '.ets_abancart_overload .ets_abancart_add_discount', function (ev) {
        ev.preventDefault();
        var btn = $(this),
            overload = btn.parents('.ets_abancart_overload'),
            discount_code = btn.data('code');
        if (!btn.hasClass('active') && ETS_ABANCART_LINK_AJAX) {
            $.ajax({
                type: 'post',
                url: ETS_ABANCART_LINK_AJAX,
                dataType: 'json',
                data: 'add_cart_rule&discount_code=' + discount_code,
                success: function (json) {
                    if (json) {
                        if (json.errors) {
                            //overload.prepend(json.errors);
                            showErrorMessage(json.errors);
                        } else
                            window.location.href = json.link_checkout;
                    }
                }
            });
        }
    });

    $(document).on('click', '.js-ets-ac-btn-submit-lead-form', function (e) {
        var $this = $(this);
        if ($this.hasClass('loading')) {
            return false;
        }
        if ($this.closest('form').find('.ets_ac_captchav2').length && typeof grecaptcha !== 'undefined') {
            if (!grecaptcha.getResponse()) {
                $this.closest('.ets-ac-lead-form-field-shortcode').find('.form-errors').html('<div class="alert alert-danger"><ul>' + ETS_AC_RECAPTCHA_V2_INVALID + '</ul></div>');
                return false;
            }
        }
        var formData = new FormData();
        var inputDatas = $this.closest('form').serializeArray();

        $.each(inputDatas, function (i, el) {
            if ($this.closest('form').find('[name="' + el.name + '"]').attr('type') === 'file') {
                var fileItem = $this.closest('form').find('[name=' + el.name + ']')[0].files;
                if (fileItem.length) {
                    formData.append(el.name, fileItem[0]);
                }
            } else {
                formData.append(el.name, el.value);
            }
        });
        $this.closest('form').find('input[type=file]').each(function () {
            var fileItem = $(this)[0].files;
            if (fileItem.length) {
                formData.append($(this).attr('name'), fileItem[0]);
            }
        });

        formData.append('submitEtsAcLeadForm', 1);
        $.ajax({
            url: ETS_AC_LINK_SUBMIT_LEAD_FORM + (ETS_AC_LINK_SUBMIT_LEAD_FORM.indexOf('?') !== -1 ? '&ajax=1' : '?ajax=1'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $this.addClass('loading');
                $this.prop('disabled', true);
            },
            success: function (res) {
                if (res.success) {
                    if (res.display_thankyou_page) {
                        $this.closest('.ets_abancart_wrapper').html(res.thankyou);
                        if ($('.ets_abancart_popup_overload .ets_abancart_close').length) {
                            $('.ets_abancart_popup_overload .ets_abancart_close').addClass('thankyou-page');
                        }
                    } else {
                        $this.closest('.ets-ac-lead-form-field-shortcode').html('<div class="alert alert-success">' + res.message + '</div>');
                    }
                } else {
                    var msg = '';
                    $.each(res.message, function (i, el) {
                        msg += '<li>' + el + '</li>';
                    });
                    $this.closest('.ets-ac-lead-form-field-shortcode').find('.form-errors').html('<div class="alert alert-danger"><ul>' + msg + '</ul></div>');
                }
            },
            complete: function () {
                $this.removeClass('loading');
                $this.prop('disabled', false);
            }
        });
        return false;
    });

    $(document).on('click', '.ets-ac-btn-submit-lead-form ', function () {
        var $this = $(this);
        if ($this.closest('form').find('.ets_ac_captchav2').length && typeof grecaptcha !== 'undefined') {
            if (!grecaptcha.getResponse()) {
                $this.closest('form').find('.ets_ac_captchav2').parent().find('.form-error-item').remove();
                $this.closest('form').find('.ets_ac_captchav2').after('<p class="form-error-item">' + ETS_AC_RECAPTCHA_V2_INVALID + '</p>');
                return false;
            }
        }
    });
});