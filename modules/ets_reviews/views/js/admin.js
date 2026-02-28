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

document.addEventListener('DOMContentLoaded', function () {

    const links = document.querySelectorAll(".btn-group-action a, .ets_dropdownmenucontent a");
    links.forEach(function (link) {
        link.addEventListener("contextmenu", function (event) {
            event.preventDefault();
        });
    });

    const titleBoxes = document.querySelectorAll('span.title_box');
    titleBoxes.forEach(titleBox => {
        const sortSpan = document.createElement('span');
        sortSpan.classList.add('ets-rv-sort');
        const anchorElements = titleBox.querySelectorAll('a');
        if (anchorElements.length > 0) {
            anchorElements.forEach(anchor => {
                sortSpan.appendChild(anchor);
            });
            titleBox.parentNode.insertBefore(sortSpan, titleBox.nextSibling);
        }
    });
});
var ets_rv_edited_id = 0;
var ETS_RV_DELETE_TITLE = ETS_RV_DELETE_TITLE || 'Delete';
var ETS_RV_CLEAN_LOG_CONFIRM = ETS_RV_CLEAN_LOG_CONFIRM || 'Do you want to clear all mail logs?';
var ets_rv = {
    init: function () {
        ['Review', 'Question'].forEach(function (prop) {
            ets_rv.notifyApprove(0, 0, prop);
        });
        this.filter();
        ets_rv.checkBoxAll();
        ets_rv.cacheEnabled($('input[name=ETS_RV_CACHE_ENABLED]:checked'));
        ets_rv.hookDisplayCustom();
    },
    cacheEnabled: function (_this) {
        if (_this.val() === '1') {
            $('.ets_rv_cache_lifetime').show();
        } else {
            $('.ets_rv_cache_lifetime').hide();
        }
    },
    processLoading: function (el) {
        if (el !== undefined)
            el.addClass('active');
    },
    processFinish: function (el) {
        if (el !== undefined)
            el.removeClass('active');
    },
    isLoading: function (el) {
        return !!(el !== undefined && el.hasClass('active'));
    },
    offForm: function () {
        $('.ets_rv_overload').removeClass('active');
        if (ETS_RV_SCROLL_ITEM) {
            ETS_RV_SCROLL_ITEM.removeClass('ets_rv_highlight');
        }
    },
    initForm: function (form) {
        $('.ets_rv_form').html(form);
        $('.ets_rv_overload').addClass('active');
        var initRate = $('#criterions_list .criterion-item:not(.template) .ets-rv-grade-stars');
        if (initRate.length > 0) {
            initRate.etsRating();
        }
    },
    notifyApprove: function (refresh, counter, prop) {
        var propTab = prop,
            prop = prop.toLowerCase(),
            menu = $('.form-menu-item.' + prop + 's'),
            sub_menu = $('.form-sub-menu-item.' + prop + 's')
        ;
        if (refresh) {
            var counter = parseInt(counter);

            if (menu.length > 0) {
                menu.attr('data-count', counter);
                if ($('a.form-menu-item-link > span.badge', menu).length > 0) {
                    $('a.form-menu-item-link > span.badge', menu).html(counter);
                } else {
                    $('a.form-menu-item-link', menu).append('&nbsp;<span class="badge badge-danger">' + counter + '</span>');
                }
            } else if (sub_menu.length > 0) {
                sub_menu.attr('data-count', counter);
                if ($('a.form-sub-menu-item-link > span.badge', sub_menu).length > 0) {
                    $('a.form-sub-menu-item-link > span.badge', sub_menu).html(counter);
                } else {
                    $('a.form-sub-menu-item-link', sub_menu).append('&nbsp;<span class="badge badge-danger">' + counter + '</span>');
                }
            }
        }
        if (!$('#subtab-AdminEtsRVReviews').length && !refresh)
            return;
        var notApprove = 0;
        if (menu.length > 0) {
            notApprove = parseInt(menu.attr('data-count'));
            if (!notApprove) {
                $('.form-menu-item.' + prop + 's span.badge, #subtab-AdminEtsRV' + propTab + 's > a.link .ets_rv_' + prop + 's_not_approve').remove();
                return;
            }
        } else if (sub_menu.length > 0) {
            notApprove = parseInt(sub_menu.attr('data-count'));
            if (!notApprove) {
                $('.form-sub-menu-item.' + prop + 's span.badge, #subtab-AdminEtsRV' + propTab + 's > a.link .ets_rv_' + prop + 's_not_approve').remove();
                return;
            }
        }

        if (!$('li[id*=-AdminEtsRV' + propTab + 's] > a span.ets_rv_' + prop + 's_not_approve').length) {
            if (menu.length > 0) {
                $('li[id*=-AdminEtsRV' + propTab + 's] > a > span').append('<span class="badge badge-danger ets_rv_' + prop + 's_not_approve">' + notApprove + '</span>');
            } else if (sub_menu.length > 0) {
                $('li[id*=-AdminEtsRV' + propTab + 's] > a').append('<span class="badge badge-danger ets_rv_' + prop + 's_not_approve">' + notApprove + '</span>');
            }

        } else {
            $('li[id*=-AdminEtsRV' + propTab + 's] > a span.ets_rv_' + prop + 's_not_approve').html(notApprove);
        }
    },
    copyToClipboard: function (el) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(el.text()).select();
        document.execCommand("copy");
        $temp.remove();
        showSuccessMessage(copied_translate);
        setTimeout(function () {
            el.removeClass('copy');
        }, 300);
    },
    refreshList: function (jsonData) {
        if (jsonData) {
            var _wrap = $('.ets-pc-form-group-wrapper');
            if (jsonData.list && _wrap.length > 0) {
                _wrap.html(jsonData.list);
            }
            if (typeof jsonData.counter !== "undefined") {
                ets_rv.notifyApprove(true, jsonData.counter, parseInt(jsonData.qa) > 0 ? 'Question' : 'Review');
            }
        }
    },
    filter: function () {
        if ($('button[name="submitFilter"]').length && $('tr.filter .filter').length) {
            var filter = false;
            $('tr.filter .filter').each(function () {
                if ($(this).val() != '') {
                    $('button[name="submitFilter"]').removeAttr('disabled');
                    filter = true;
                    return true;
                }
            });
            if (!filter)
                $('button[name="submitFilter"]').attr('disabled', 'disabled');
        }
    },
    removeIds: function (parent, element) {
        var ax = -1;
        if ((ax = parent.indexOf(element)) !== -1)
            parent.splice(ax, 1);
        return parent;
    },
    doShortCode: function (html) {
        if (!html)
            return '';
        var shortCodeTexts = [
            {
                short_code: '{shop_name}',
                text: 'My Store'
            },
            {
                short_code: '{logo_img}',
                text: (typeof PS_SHOP_LOGO !== typeof undefined && PS_SHOP_LOGO !== '' ? '<img src="' + PS_SHOP_LOGO + '"/>' : ''),
            },
            {
                short_code: '{shop_url}',
                text: PS_SHOP_URL,
            },
            {
                short_code: '{unsubscribe}',
                text: ETS_RV_UNSUBSCRIBE_LABEL,
            }
        ];
        $.each(shortCodeTexts, function (index, item) {
            html = html.replaceAll(item.short_code, item.text);
        });
        return html;
    },
    checkBoxAll: function () {
        $('body').find('.input-group input[type="text"]').attr('autocomplete', 'off');
        if ($('table[id^=table-ets_rv] > thead > tr.filter').length > 0 && $('table[id^=table-ets_rv] > tbody > tr > td.row-selector').length > 0) {
            var idTable = $('table[id^=table-]').attr('id').replace('table-', '');
            $('table[id^=table-ets_rv] > thead > tr.filter > th:first').html('<input id="checkBoxAll" type="checkbox" name="' + idTable + 'Box[]" value="" class="noborder">');
        }
    },
    insertAtCaret: function (areaId, text) {
        var txtarea = document.getElementById(areaId);
        if (!txtarea) {
            return;
        }
        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart === '0') ? "ff" : (document.selection ? "ie" : false));
        if (br === "ie") {
            txtarea.focus();
            if (document.selection && document.selection.createRange) {
                var range = document.selection.createRange();
            } else {
                var range = document.selection.createRange;
            }

            range.moveStart('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if (br === "ff") {
            strPos = txtarea.selectionStart;
        }
        var front = (txtarea.value).substring(0, strPos);
        var back = (txtarea.value).substring(strPos, txtarea.value.length);
        txtarea.value = front + text + back;
        strPos = strPos + text.length;
        if (br === "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart('character', -txtarea.value.length);
            ieRange.moveStart('character', strPos);
            ieRange.moveEnd('character', 0);
            ieRange.select();
        } else if (br === "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }
        txtarea.scrollTop = scrollPos;
    },
    hookDisplayCustom: function () {
        if ($('input[name=ETS_RV_AVERAGE_RATE_POSITION]:checked').val() === 'custom') {
            $('.ets_rv_hook_custom').show();
        } else {
            $('.ets_rv_hook_custom').hide();
        }
    }
};
var ets_rv_search = {
    searchProduct: function (el) {
        var _el = el || $('input[name=search_product]'),
            form = _el.parents('form'),
            xhr = null
        ;
        if (_el.length > 0 && form.length > 0 && form.attr('action')) {
            _el.autocomplete(form.attr('action') + '&searchProduct=1&ajax=1&action=searchProduct&time' + new Date().getTime(), {
                resultsClass: "ets_rv_product_results",
                appendTo: '.ets_rv_product_search',
                delay: 100,
                minChars: 1,
                autoFill: true,
                max: 20,
                matchContains: true,
                mustMatch: true,
                scroll: false,
                cacheLength: 0,
                multipleSeparator: '||',
                formatItem: function (item) {
                    return '<span data-id="' + item[0] + '">' + (item[3] ? '<img src="' + item[3] + '" title="' + item[1] + (item[2] ? ' (' + item[2] + ')' : '') + '" width="64"/>' : '') + item[1] + (item[2] ? ' (' + item[2] + ')' : '') + '</span>';
                }
            }).result(function (event, item) {
                if (item == null)
                    return false;

                $('input[name=id_product]').val(item[0]);
                $('.ets_rv_product_search').addClass('active').html('<div class="ets_rv_product_item" data-id="' + item[0] + '">' + (item[3] ? '<img src="' + item[3] + '" title="' + item[1] + ' (' + item[2] + ')" width="64"/>' : '') + item[1] + ' (' + item[2] + ')<span class="remove_ctm"></span></div>');

                ets_rv_search.closeSearch();
            });
        }
    },
    closeSearch: function (el, destroy) {
        var _el = el || $('input[name=search_product]'),
            _destroy = destroy || false
        ;
        if (_el.length > 0)
            _el.val('');
        if (_destroy)
            _el.unautocomplete();
    },
    searchCustomer: function (el, firstCustomerAddress, customerInfo) {
        var _el = el || $('input[name=search_customer]'),
            form = _el.parents('form'),
            xhr = null
        ;
        if (_el.length > 0 && form.length > 0 && form.attr('action')) {
            _el.autocomplete(form.attr('action') + '&searchCustomer=1&ajax=1&action=searchCustomer&time' + new Date().getTime(), {
                resultsClass: "ets_rv_customer_results",
                appendTo: '.ets_rv_customer_search',
                delay: 100,
                minChars: 1,
                autoFill: true,
                max: 20,
                matchContains: true,
                mustMatch: true,
                scroll: false,
                cacheLength: 0,
                multipleSeparator: '||',
                formatItem: function (item) {
                    return '<span data-id="' + item[0] + '">' + item[0] + '-' + item[1] + ' ' + item[2] + ' (' + item[3] + ') </span>';
                }
            }).result(function (event, item) {
                if (item == null)
                    return false;

                $('input[name=id_customer]').val(item[0]);
                $('.ets_rv_customer_search').addClass('active').html('<div class="ets_rv_customer_item" data-id="' + item[0] + '">' + item[0] + '-' + item[1] + ' ' + item[2] + ' (' + item[3] + ') <span class="remove_ctm"></span></div>');

                if (xhr !== null)
                    xhr.abort();
                if (parseInt(item[0]) > 0) {
                    if (firstCustomerAddress) {
                        xhr = $.ajax({
                            url: form.attr('action'),
                            type: 'GET',
                            data: {
                                ajax: 1,
                                action: 'firstCustomerAddress',
                                time: new Date().getTime(),
                                id_customer: item[0],
                            },
                            dataType: 'json',
                            success: function (json) {
                                if (json) {
                                    if (json.address) {
                                        var idCountry = json.address.id_country !== null ? parseInt(json.address.id_country) : 0;
                                        $('#id_country').val(idCountry);
                                        $('.ets_rv_customer_item[data-id=' + item[0] + ']').attr('data-id-country', idCountry);
                                    }
                                }
                            }
                        });
                    }
                    if (customerInfo) {
                        xhr = $.ajax({
                            url: form.attr('action'),
                            type: 'GET',
                            data: {
                                ajax: 1,
                                action: 'customerInfo',
                                time: new Date().getTime(),
                                id_customer: item[0],
                            },
                            dataType: 'json',
                            success: function (json) {
                                if (json) {
                                    if (json.customer) {
                                        $('#display_name').val(json.customer.display_name);
                                    }
                                    var id = 'avatar', images = $('#avatar').parents('.form-group');
                                    if (json.avatar) {
                                        ets_rv_file.clearInputFile($('#' + id));
                                        if (images.find('#' + id + '-images-thumbnails').length <= 0) {
                                            images.find('.form-group').before('<div class="form-group"><div class="col-lg-12" id="' + id + '-images-thumbnails"><div>' + json.avatar + '<p><p>File size <span class="ets-rv-file-size">' + json.size + '</span></p><a class="btn btn-default ets-rv-delete-avatar" href="' + json.delete_url + '"><i class="icon-trash"></i> ' + ETS_RV_DELETE_TITLE + '</a></p></div></div></div>');
                                        } else {
                                            images.find('#' + id + '-images-thumbnails').find('img').replaceWith(json.avatar);
                                            images.find('#' + id + '-images-thumbnails').find('.ets-rv-delete-avatar').attr('href', json.delete_url);
                                            images.find('#' + id + '-images-thumbnails').find('.ets-rv-file-size').attr('href', json.size);
                                        }
                                    } else if (!$('#' + id).val()) {
                                        $('#' + id + '-images-thumbnails').parent('.form-group').remove();
                                    }
                                }
                            }
                        });
                    }
                }

                ets_rv_search.closeSearch(_el);
            });
        }
    },
};
var ets_rv_op = {
    init: function () {
        ets_rv_op.multiOptions();
        ets_rv_op.reCAPTCHA();
        ets_rv_op.allowGuests();
        ets_rv_op.autoApprove();
        ets_rv_op.enabledUploadPhotos();
        ets_rv_op.enabledUploadVideos();
        ets_rv_op.discountOption();
        ets_rv_op.navTabs();
        ets_rv_op.invitationEmail();
        ets_rv_op.sendRatingInvitation();
        ets_rv_op.moderateReview();
        ets_rv_op.discount();
        ets_rv_op.templateType();
        var flag = false;
        $('[id^=content_html]:not([id^=content_html_full])').each(function () {
            var id = $(this).attr('id').replace('content_html_', '');
            ets_rv_op.previewIframe('html', id);
            ets_rv_op.previewIframe('txt', id);
            if (!flag)
                flag = true;
        });
        if (flag) {
            $('.ets_rv_preview_template .template_type').hide();
            $('.ets_rv_preview_template .template_' + $('#template_type').val()).show();
            hideOtherLanguage(id_language);
        }
        ets_rv_op.whoPostReview();
        ets_rv_op.whoPostRating();
        ets_rv_op.allowEditComment();
        ets_rv_op.allowDeleteComment();
        ets_rv_op.allowEditCommentQuestion();
        ets_rv_op.allowDeleteCommentQuestion();
    },
    templateType: function (op) {
        var option = op || $('#template_type').val();
        $('.form-group.template_type').hide();
        if (option) {
            $('.form-group.template_type.' + option).show();
        }
    },
    resizeIframe: function (obj) {
        var pHeight = $(obj).parent().height();
        if (obj.contentWindow.document.documentElement.scrollHeight > pHeight) {
            obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
        } else {
            obj.style.height = ($(obj).parent().height() ?? 500) + 'px';
        }
    },
    previewIframe: function (op, idLang) {
        var type = op || $('#template_type').val(),
            createIframe = null,
            templateHtml = $('.ets_rv_preview_template .template_html'),
            templateTxt = $('.ets_rv_preview_template .template_txt'),
            lang_id = idLang || id_language
        ;
        switch (type) {
            case 'html':
                templateTxt.hide();
                if (templateHtml.find('#preview_template_html_' + lang_id).length < 1) {
                    createIframe = $('<iframe id="preview_template_html_' + lang_id + '" class="translatable-field lang-' + lang_id + '" onload="ets_rv_op.resizeIframe(this)" style="min-height: 473px;"></iframe>');
                    templateHtml.append(createIframe);
                } else {
                    templateHtml.show();
                    createIframe = templateHtml.find('#preview_template_html_' + lang_id);
                }
                var contentIFrame = createIframe[0].contentDocument || createIframe[0].contentWindow.document;
                contentIFrame.write(ets_rv.doShortCode($('#content_html_full_' + lang_id).val().replace(/@content@/, $('#content_html_' + lang_id).val())));
                contentIFrame.close();
                break;
            case 'txt':
                templateHtml.hide();
                if (templateTxt.find('#preview_template_txt_' + lang_id).length < 1) {
                    templateTxt.append($('<div id="preview_template_txt_' + lang_id + '" class="translatable-field lang-' + lang_id + '" onload="ets_rv_op.resizeIframe(this)"></div>'));
                } else
                    templateTxt.show();
                templateTxt.find('#preview_template_txt_' + lang_id).html($('#content_txt_' + lang_id).val());
                break;
        }
    },
    discount: function () {
        var new_review = $('#ETS_RV_DISCOUNT_ENABLED_on').is(':checked');

        if (new_review === false) {
            $('.form-group.discount:not(.discount_enabled):visible').addClass('hide');
        } else {
            $('.form-group.discount.hide').removeClass('hide');
        }
    },
    reCAPTCHA: function (el) {
        var _el = el || $('input[name=ETS_RV_RECAPTCHA_ENABLED]:checked')
        ;
        if (parseInt(_el.val()) === 1) {
            $('.form-group.recaptcha_type').show();
            ets_rv_op.reCAPTCHAType();
        } else {
            $('.form-group.recaptcha_type').hide();
        }
    },
    reCAPTCHAType: function (el) {
        var _el = el || $('input[name=ETS_RV_RECAPTCHA_TYPE]:checked')
        ;
        $('.form-group.recaptcha_type.' + _el.val()).show();
        $('.form-group.recaptcha_type:not(.' + _el.val() + '):not(.is_parent_group)').hide();
    },
    multiOptions: function (el) {
        var _el = el || $('[id^=ids_language]>option:selected');
        if (_el.val() == 'all' || $('[id^=ids_language]>option:not([value="all"]):selected').length == $('[id^=ids_language]>option:not([value="all"])').length) {
            $('[id^=ids_language]>option:not(:selected)').prop('selected', true);
        }
    },
    allowGuests: function (el) {
        var _el = el || $('input[name=ETS_RV_ALLOW_GUESTS]:checked');
        if (parseInt(_el.val()) === 1) {
            $('.form-group.allow_guests_no').hide();
            $('.form-group.allow_guests_yes').show();
        } else {
            $('.form-group.allow_guests_yes').hide();
            $('.form-group.allow_guests_no').show();
            ets_rv_op.purchasedProduct();
        }
    },
    purchasedProduct: function (el) {
        var ele = el || $('input[name=ETS_RV_PURCHASED_PRODUCT]:checked');
        if (parseInt(ele.val()) === 1) {
            $('.form-group.purchased_product_no').hide();
            $('.form-group.purchased_product').show();
        } else {
            $('.form-group.purchased_product').hide();
            $('.form-group.purchased_product_no').show();
        }
    },
    moderateReview: function (el) {
        var ele = el || $('input[name=ETS_RV_MODERATE]:checked');
        if (parseInt(ele.val()) === 1) {
            $('.form-group.moderate_yes').show();
        } else {
            $('.form-group.moderate_yes').hide();
        }
    },
    autoApprove: function (el) {
        var ele = el || $('input[name=ETS_RV_AUTO_APPROVE]:checked');
        if (parseInt(ele.val()) === 1) {
            $('.form-group.auto_approve_no').hide();
        } else {
            $('.form-group.auto_approve_no').show();
        }
    },
    isBlock: function (ac) {
        if (ac === 'block') {
            $('a.block,.is_block_no').hide();
            $('a.unblock,.is_block_yes').show();
        } else {
            $('a.block,.is_block_no').show();
            $('a.unblock,.is_block_yes').hide();
        }
    },
    enabledUploadPhotos: function (el) {
        var ele = el || $('input[name=ETS_RV_UPLOAD_PHOTO_ENABLED]:checked');
        if (parseInt(ele.val()) === 1) {
            $('.form-group.ets_rv_max_upload_photo').show();
        } else {
            $('.form-group.ets_rv_max_upload_photo').hide();
        }
    },
    enabledUploadVideos: function (el) {
        var ele = el || $('input[name=ETS_RV_UPLOAD_VIDEO_ENABLED]:checked');
        if (parseInt(ele.val()) === 1) {
            $('.form-group.ets_rv_max_upload_video').show();
        } else {
            $('.form-group.ets_rv_max_upload_video').hide();
        }
    },
    discountOption: function (el) {
        var _el = el || $('input[name=ETS_RV_DISCOUNT_OPTION]:checked').val();
        if (_el) {
            $('.form-group.discount_option:not(.is_parent1)').hide();
            $('.form-group.discount_option.' + _el).show();
            if (_el == 'auto') {
                ets_rv_op.applyDiscount();
            }
        }
        //reset default if is invalid.
        if (!/^(\d)+$/.test($('#ETS_RV_APPLY_DISCOUNT_IN').val()))
            $('#ETS_RV_APPLY_DISCOUNT_IN').val(1);
        if (!/^([a-zA-Z0-9-_])*$/.test($('#discount_code').val())) {
            $('#ETS_RV_DISCOUNT_CODE').val('');
        }
    },
    applyDiscount: function (el) {
        var _el = el || $('input[name=ETS_RV_APPLY_DISCOUNT]:checked').val();
        if (_el) {
            $('.form-group.apply_discount:not(.is_parent2)').hide();
            $('.form-group.apply_discount.' + _el).show();
        }
        //reset default if is invalid
        if (!/^(\d+(\.?)\d*)$/.test($('#ETS_RV_REDUCTION_AMOUNT').val()))
            $('#ETS_RV_REDUCTION_AMOUNT').val(0);
        if (!/^([0-9]|[1-9][0-9]|100)$/.test($('#ETS_RV_REDUCTION_PERCENT').val()))
            $('#ETS_RV_REDUCTION_PERCENT').val(0);
    },
    navTabs: function (op) {
        var current_tab = $('#current_tab_active'),
            currentUrl = window.location.href,
            matches = currentUrl.match(/#(.+?)$/),
            option = op || $('li.ets-pc-nav-item.' + (matches !== null ? matches[1] : current_tab.val())),
            formSettings = $('form.ets_rv_form_config')
        ;
        if (formSettings.length < 1)
            return false;
        // Nav tab
        $('.ets-pc-nav-tabs li.ets-pc-nav-item:not(.' + option.data('tab') + '), .ets_rv_form_config .form-wrapper-group-item:not(.' + option.data('tab') + ')').removeClass('active');
        option.addClass('active');

        // Form wrapper:
        $('.ets_rv_form_config .form-wrapper-group-item.' + option.data('tab')).addClass('active');
        current_tab.val(option.data('tab'));

        // Form action:
        formSettings.attr('action', formSettings.attr('action').replace(/#(.+?)$/i, '#' + current_tab.val()));

        // Custom from tab:
        if (current_tab.val() === 'design')
            $('.ets_rv_reset_to_default.hide').removeClass('hide');
        else
            $('.ets_rv_reset_to_default:not(.hide)').addClass('hide');

        ets_rv_op.discount();
    },
    invitationEmail: function (op) {
        var option = op || $('#ETS_RV_EMAIL_TO_CUSTOMER_RATING_on:checked');
        if (parseInt(option.val()) > 0) {
            $('.form-group.customer_rating').show();
        } else {
            $('.form-group.customer_rating').hide();
        }
    },
    whoPostReview: function () {
        if (!$('#ETS_RV_WHO_POST_REVIEW_guest').is(':checked'))
            $('#ETS_RV_WHO_POST_RATING_guest').prop('checked', false);

        if (!$('#ETS_RV_WHO_POST_REVIEW_purchased').is(':checked'))
            $('input[id^=ETS_RV_WHO_POST_RATING][value=purchased]').prop('checked', false);

        if ($('#ETS_RV_WHO_POST_RATING_no_purchased_incl').length < 1 && !$('#ETS_RV_WHO_POST_REVIEW_no_purchased').is(':checked'))
            $('input[id^=ETS_RV_WHO_POST_RATING][value*=no_purchased]').prop('checked', false);

        if ($('#ETS_RV_WHO_POST_REVIEW_no_purchased_incl').length > 0 && !$('#ETS_RV_WHO_POST_REVIEW_no_purchased_incl').is(':checked'))
            $('#ETS_RV_WHO_POST_RATING_no_purchased_incl').prop('checked', false);

        if ($('#ETS_RV_WHO_POST_REVIEW_no_purchased_excl').length > 0 && !$('#ETS_RV_WHO_POST_REVIEW_no_purchased_excl').is(':checked'))
            $('#ETS_RV_WHO_POST_RATING_no_purchased_excl').prop('checked', false);

        $('input[id^=ETS_RV_WHO_POST_RATING][id$=guest], input[id^=ETS_RV_WHO_POST_REVIEW][id$=guest]').prop('disabled', false);

        ets_rv_op.reviewAvailable();
    },
    reviewAvailable: function () {
        if ($('#ETS_RV_WHO_POST_REVIEW_guest').is(':checked') || $('#ETS_RV_WHO_POST_REVIEW_no_purchased').is(':checked') || $('#ETS_RV_WHO_POST_REVIEW_no_purchased_excl').is(':checked') || !$('#ETS_RV_WHO_POST_REVIEW_purchased').is(':checked'))
            $('.form-group.ets_rv_review_available_time').hide();
        else if ($('#ETS_RV_WHO_POST_REVIEW_purchased').is(':checked'))
            $('.form-group.ets_rv_review_available_time').show();
    },
    whoPostRating: function () {
        if ($('#ETS_RV_WHO_POST_RATING_guest').is(':checked'))
            $('#ETS_RV_WHO_POST_REVIEW_guest').prop('checked', true);

        if ($('#ETS_RV_WHO_POST_RATING_purchased').is(':checked'))
            $('#ETS_RV_WHO_POST_REVIEW_purchased').prop('checked', true);

        if ($('#ETS_RV_WHO_POST_RATING_no_purchased_incl').is(':checked'))
            $('#ETS_RV_WHO_POST_REVIEW_no_purchased_incl').prop('checked', true);

        if ($('#ETS_RV_WHO_POST_RATING_no_purchased_excl').is(':checked'))
            $('#ETS_RV_WHO_POST_REVIEW_no_purchased_excl').prop('checked', true);

        if ($('#ETS_RV_WHO_POST_RATING_no_purchased_incl').length < 1 && $('input[id=ETS_RV_WHO_POST_RATING_no_purchased]').is(':checked')) {
            $('input[id=ETS_RV_WHO_POST_REVIEW_no_purchased]').prop('checked', true);
        }

        $('input[id^=ETS_RV_WHO_POST_RATING][id$=guest], input[id^=ETS_RV_WHO_POST_REVIEW][id$=guest]').prop('disabled', false);
    },
    allowEditComment: function () {
        if (parseInt($('input[name="ETS_RV_ALLOW_EDIT_COMMENT"]:checked').val()) === 1) {
            $('.form-group.review.ets_rv_customer_edit_approved').show();
        } else
            $('.form-group.review.ets_rv_customer_edit_approved').hide();
    },
    allowDeleteComment: function () {
        if (parseInt($('input[name="ETS_RV_ALLOW_DELETE_COMMENT"]:checked').val()) === 1) {
            $('.form-group.review.ets_rv_customer_delete_approved').show();
        } else
            $('.form-group.review.ets_rv_customer_delete_approved').hide();
    },
    allowEditCommentQuestion: function () {
        if (parseInt($('input[name="ETS_RV_QA_ALLOW_EDIT_COMMENT"]:checked').val()) === 1) {
            $('.form-group.question.ets_rv_qa_customer_edit_approved').show();
        } else
            $('.form-group.question.ets_rv_qa_customer_edit_approved').hide();
    },
    allowDeleteCommentQuestion: function () {
        if (parseInt($('input[name="ETS_RV_QA_ALLOW_DELETE_COMMENT"]:checked').val()) === 1) {
            $('.form-group.question.ets_rv_qa_customer_delete_approved').show();
        } else
            $('.form-group.question.ets_rv_qa_customer_delete_approved').hide();
    },
    sendRatingInvitation: function () {
        if ($('#ETS_RV_SEND_RATING_INVITATION_on').is(':checked')) {
            $('.ets_rv_email_to_customer_order_status, .ets_rv_cronjob_schedule_time').show();
        } else {
            $('.ets_rv_email_to_customer_order_status, .ets_rv_cronjob_schedule_time').hide();
        }
    }
};
var ets_rv_valid = {
    isEmail: function (email) {
        return email && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
    }
};
var ets_rv_file = {
    clearInputFile: function (el) {
        var _el = el || $('input[type=file]'),
            _dummy = _el.next()
        ;
        _el.val('');
        if (_dummy.hasClass('dummyfile')) {
            _dummy.find('input[type=text]').val('');
        }
    },
    checkFile: function (file) {
        if (!file) {
            return false;
        }
        const extension = file.name.split(".").pop().toLowerCase();
        const allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        if (!allowedExtensions.includes(extension)) {
            alert('File uploads are only permitted in JPG, JPEG, PNG, and GIF formats.');
            return false;
        }
        const maxSize = PS_ATTACHMENT_MAXIMUM_SIZE ?? 1048576;
        console.log(file.size, maxSize)
        if (file.size > maxSize) {
            alert("File size is too large. Maximum size is " + (PS_ATTACHMENT_MAXIMUM_SIZE_TEXT ?? maxSize + 'MB') + ".");
            return false;
        }

        return true;
    },
    readURL: function (input, validateBefore) {
        var images = $(input).parents('.form-group-file').eq(0),
            id = $(input).attr('name')
        ;
        if (input.files && input.files[0] && (!validateBefore || this.checkFile(input.files[0]))) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if (images.find('#' + id + '-images-thumbnails').length <= 0) {
                    images.find('.form-group').before('<div class="form-group"><div class="col-lg-12" id="' + id + '-images-thumbnails"><div><img src="#" alt="" class="imgm img-thumbnail">&nbsp;&nbsp;<a class="btn btn-default base64encode" href="#" title="' + ETS_RV_DELETE_TITLE + '"><i class="icon-trash"></i></a></div></div></div>');
                }
                var _thumbnail = $('#' + id + '-images-thumbnails .img-thumbnail');
                _thumbnail.attr({
                    src: e.target.result,
                    alt: input.files[0].name,
                    width: '180'
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
};

function setMore_menu() {
    var menu_width_box = $('.ets-pc-panel-heading').width();
    var menu_width = $('.ets-pc-panel-heading .form-group-menus').width();
    var itemwidthlist = 0
    $(".form-group-menus .form-menu-item").each(function () {
        var itemwidth = $(this).width();
        itemwidthlist = itemwidthlist + itemwidth;
        if (itemwidthlist > menu_width_box - 70 && itemwidthlist > 500) {
            $(this).addClass('hide_more');
        } else {
            $(this).removeClass('hide_more');
        }
    });
}

$(document).ready(function () {
    $('.ets_rv_panel table .datepicker:not(.hasEtsDatepicker)').datepicker({
        prevText: '',
        nextText: '',
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    }).addClass('hasEtsDatepicker');
    setMore_menu();
    $(window).resize(function () {
        setMore_menu();
        $(".form-menu-item.hide_more").removeClass('show_hover');
    });
    $('.form-menu-item.more_menu').on('click', function (e) {
        $(".form-menu-item.hide_more").toggleClass('show_hover');
    });
    $(document).mouseup(function (e) {
        var confirm_popup = $('.form-menu-item.hide_more');
        if (!confirm_popup.is(e.target) && confirm_popup.has(e.target).length === 0) {
            $(".form-menu-item.hide_more").removeClass('show_hover');
        }
    });
    $('table.ets_rv_activity tr td .ets_rv_make.unread').parents('tr').addClass('ets-rv-unread');
    if ($('.bootstrap .alert.alert-success:not(.cronjob), .bootstrap .alert.alert-danger:not(.cronjob)').length > 0) {
        setTimeout(function () {
            $('.bootstrap .alert.alert-success:not(.cronjob), .bootstrap .alert.alert-danger:not(.cronjob)').hide();
        }, 5000);
    }
    if ($('#search_customer').length > 0) {
        ets_rv_search.searchCustomer($('#search_customer'), false, true);
    }
    $('.ets-rv-fancybox-image').fancybox();
    setTimeout(function () {
        if (typeof $.fn.mColorPicker === "undefined" || typeof $.fn.mColorPicker.setTextColor === "undefined")
            return;
        var mColorPicker = $('.mColorPicker');
        if (mColorPicker.length > 0) {
            mColorPicker.each(function () {
                $.fn.mColorPicker.setTextColor($(this).val());
                $(this).css('background-color', $(this).val());
            });
        }
    }, 750);
    if ($('.ets-pc-panel-heading_height').length > 0) {
        var m_height = $('.page-head').outerHeight() + $('#header_infos').outerHeight() - 1;
        $('.ets-pc-panel-heading').css('top', m_height);
    }

    ets_rv.init();
    ets_rv_op.init();
    $(document).on('change', 'input[name=ETS_RV_AVERAGE_RATE_POSITION]', function () {
        ets_rv.hookDisplayCustom();
    });
    $(document).on('click', '.ets-rv-short-code', function (e) {
        e.preventDefault();
        var rel = $(this).attr('rel'),
            shortCode = $(this).html()
        ;
        if (rel === 'content_txt' || rel === 'subject')
            ets_rv.insertAtCaret(rel + '_' + id_language, shortCode);
        else if (typeof tinyMCE !== "undefined")
            tinyMCE.get(rel + '_' + id_language).execCommand('mceInsertContent', false, shortCode);
    });

    $(document).on('click', '.ets_rv_re_sendmail', function (e) {
        e.preventDefault();
        var btn = $(this), postUrl = btn.attr('href');
        if (!btn.hasClass('active') && postUrl !== '#') {
            btn.addClass('active');
            $.ajax({
                type: 'POST',
                data: 'ajax=1&action=sendmail',
                url: postUrl,
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                            if (json.html)
                                $('.ets-pc-form-group-wrapper').html(json.html);
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });

    // Upload files.
    $(document).on('change', '#avatar[type=file]', function () {
        ets_rv_file.readURL(this, true);
    });
    // Delete image.
    $(document).on('click', '[id$=-images-thumbnails] a.btn', function (ev) {
        ev.preventDefault();
        var _self = $(this),
            _fg = $(this).parents('.form-group').eq(0),
            _file = _fg.next().find('input[type=file]').eq(0)
        ;
        if (!_self.hasClass('active') && _self.attr('href') !== '#') {
            _self.addClass('active');
            $.ajax({
                type: 'post',
                url: _self.attr('href'),
                data: 'ajax=1&action=deleteImage',
                dataType: 'json',
                success: function (json) {
                    _self.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            if (json.msg)
                                showErrorMessage(json.msg);
                        } else {
                            showSuccessMessage(json.msg);
                        }
                    }
                },
                error: function () {
                    _self.removeClass('active');
                }
            });
        }
        ets_rv_file.clearInputFile(_file);
        _fg.remove();
    });
    $(document).on('change', 'input[name=ETS_RV_SEND_RATING_INVITATION]', function () {
        ets_rv_op.sendRatingInvitation();
    });
    $(document).on('change', 'table[id^=table-ets_rv] > thead > tr.filter #checkBoxAll', function (e) {
        e.preventDefault();
        var idTable = $('table[id^=table-]').attr('id').replace('table-', '');
        if ($(this).is(':checked'))
            checkDelBoxes($(this).closest('form').get(0), idTable + 'Box[]', true);
        else
            checkDelBoxes($(this).closest('form').get(0), idTable + 'Box[]', false);
    });

    $(document).on('click', '.ets-rv-criterion-clear', function () {
        $(this).parent('.criterion-rating').find('.ets-rv-grade-stars').etsRating({grade: 0});
    });

    $(document).on('click', '#template_type', function () {
        var type = $(this).val();
        ets_rv_op.templateType(type);


        ets_rv_op.previewIframe(type);
    });

    $(document).on('keyup', 'textarea[id^=content_html], textarea[id^=content_txt]', function () {
        ets_rv_op.previewIframe(false, false);
    });

    $(document).on('click', '.ets_rv_secure_token span.input-group-addon', function () {
        var chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz",
            random = '',
            secure_token = $('#ets_rv_secure_token').val();
        for (var i = 1; i <= 10; ++i)
            random += chars.charAt(Math.floor(Math.random() * chars.length));

        $('#ets_rv_secure_token').val(random);
        $('#ets_abd_cronjob_path').html($('#ets_abd_cronjob_path').html().replace('secure=' + secure_token, 'secure=' + $('#ets_rv_secure_token').val()));
        $('#ets_rv_cronjob_link').attr('href', $('#ets_rv_cronjob_link').attr('href').replace('secure=' + secure_token, 'secure=' + $('#ets_rv_secure_token').val()));
    });

    $(document).on('click', 'input[name^=ETS_RV_WHO_POST_REVIEW]', function () {
        ets_rv_op.whoPostReview();
    });

    $(document).on('click', 'input[name^=ETS_RV_WHO_POST_RATING]', function () {
        ets_rv_op.whoPostRating();
        ets_rv_op.reviewAvailable();
    });
    $(document).on('change', 'input[name="ETS_RV_ALLOW_EDIT_COMMENT"]', function () {
        ets_rv_op.allowEditComment();
    });

    $(document).on('change', 'input[name="ETS_RV_ALLOW_DELETE_COMMENT"]', function () {
        ets_rv_op.allowDeleteComment();
    });

    $(document).on('change', 'input[name=ETS_RV_DISCOUNT_ENABLED]', function () {
        ets_rv_op.discount();
    });

    $(document).on('change', 'input[name="ETS_RV_QA_ALLOW_EDIT_COMMENT"]', function () {
        ets_rv_op.allowEditCommentQuestion();
    });

    $(document).on('change', 'input[name="ETS_RV_QA_ALLOW_DELETE_COMMENT"]', function () {
        ets_rv_op.allowDeleteCommentQuestion();
    });

    $(document).on('click', 'input[name="customer_type"]', function () {
        if ($(this).val() === 'customer') {
            $('.customer_type.guest').hide();
            $('.customer_type.customer').show();
            $('#id_country').val($('.ets_rv_customer_item').attr('data-id-country'));
        } else {
            $('.customer_type.customer').hide();
            $('.customer_type.guest').show();
            $('#id_country').val(0);
        }
    });

    $(document).on('keyup change input', 'tr.filter .filter', function () {
        ets_rv.filter();
    });

    $(document).on('change', 'input[name=ETS_RV_MODERATE]', function () {
        ets_rv_op.moderateReview($(this));
    });

    $(document).on('click', '.ets_rv_short_code', function () {
        ets_rv.copyToClipboard($(this));
    });

    $(document).on('click', '.ets_rv_reset_to_default', function (ev) {
        ev.preventDefault();
        var ETS_RV_DESIGN_COLOR1 = '#ee9a00',
            ETS_RV_DESIGN_COLOR2 = '#555555',
            ETS_RV_DESIGN_COLOR3 = '#ee9a00',
            ETS_RV_DESIGN_COLOR4 = '#48AF1A',
            ETS_RV_DESIGN_COLOR5 = '#2fb5d2'
        ;
        $('input[name=ETS_RV_DESIGN_COLOR1]').val(ETS_RV_DESIGN_COLOR1).css({
            'background-color': ETS_RV_DESIGN_COLOR1,
            'color': $.fn.mColorPicker.textColor(ETS_RV_DESIGN_COLOR1)
        }).trigger('change');

        $('input[name=ETS_RV_DESIGN_COLOR2]').val(ETS_RV_DESIGN_COLOR2).css({
            'background-color': ETS_RV_DESIGN_COLOR2,
            'color': $.fn.mColorPicker.textColor(ETS_RV_DESIGN_COLOR2)
        }).trigger('change');

        $('input[name=ETS_RV_DESIGN_COLOR3]').val(ETS_RV_DESIGN_COLOR3).css({
            'background-color': ETS_RV_DESIGN_COLOR3,
            'color': $.fn.mColorPicker.textColor(ETS_RV_DESIGN_COLOR3)
        }).trigger('change');

        $('input[name=ETS_RV_DESIGN_COLOR4]').val(ETS_RV_DESIGN_COLOR4).css({
            'background-color': ETS_RV_DESIGN_COLOR4,
            'color': $.fn.mColorPicker.textColor(ETS_RV_DESIGN_COLOR4)
        }).trigger('change');

        $('input[name=ETS_RV_DESIGN_COLOR5]').val(ETS_RV_DESIGN_COLOR5).css({
            'background-color': ETS_RV_DESIGN_COLOR5,
            'color': $.fn.mColorPicker.textColor(ETS_RV_DESIGN_COLOR5)
        }).trigger('change');

    });

    $(document).on('change', '.ets_rv_form_import_export #data', function (ev) {
        ev.preventDefault();
        var btn = $(this);
        if (!btn.hasClass('active')) {
            btn.addClass('active');

            var formData = new FormData();
            formData.append(btn.attr('name'), btn[0].files[0]);
            formData.append('key', btn.attr('name'));

            $.ajax({
                url: btn.data('url'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });

    $('#ETS_RV_IE_DATA_IMPORT_rv').click(function () {
        if (!$(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_cm, #ETS_RV_IE_DATA_IMPORT_rc').prop('checked', false);
        }
    });
    $('#ETS_RV_IE_DATA_IMPORT_cm').click(function () {
        if ($(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_rv').prop('checked', true);
        } else {
            $('#ETS_RV_IE_DATA_IMPORT_rc').prop('checked', false);
        }
    });
    $('#ETS_RV_IE_DATA_IMPORT_rc').click(function () {
        if ($(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_cm, #ETS_RV_IE_DATA_IMPORT_rv').prop('checked', true);
        }
    });
    $('#ETS_RV_IE_DATA_IMPORT_qa').click(function () {
        if (!$(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_qs, #ETS_RV_IE_DATA_IMPORT_qc').prop('checked', false);
        }
    });
    $('#ETS_RV_IE_DATA_IMPORT_qs').click(function () {
        if ($(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_qa').prop('checked', true);
        }
    });
    $('#ETS_RV_IE_DATA_IMPORT_qc').click(function () {
        if ($(this).is(':checked')) {
            $('#ETS_RV_IE_DATA_IMPORT_qa').prop('checked', true);
        }
    });

    $(document).on('click', '.ets_rv_import_data', function (ev) {
        ev.preventDefault();
        if ($(this).hasClass('prestashop')) {
            var btn = $(this);
            if (!btn.hasClass('active')) {
                btn.addClass('active');
                $.ajax({
                    url: btn.attr('href'),
                    type: 'POST',
                    data: $('#import_productcomment_prestashop :input').serialize(),
                    dataType: 'json',
                    success: function (json) {
                        btn.removeClass('active');
                        if (json) {
                            if (json.errors)
                                showErrorMessage(json.errors);
                            else {
                                if (json.msg)
                                    showSuccessMessage(json.msg);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1500)
                            }
                        }
                    },
                    error: function () {
                        btn.removeClass('active');
                    }
                });
            }
        } else {
            var btn = $(this),
                packageFile = $('#data'),
                choiceList = $('input[name^=ETS_RV_IE_DATA_IMPORT]:checked')
            ;
            if (!packageFile.val()) {
                showErrorMessage(packageFile.data('error'));
            } else if (!choiceList.length) {
                showErrorMessage(btn.data('empty'));
            } else if (!btn.hasClass('active')) {
                btn.addClass('active');
                var formData = {
                    'ETS_RV_IE_OVERRIDE': $('#ETS_RV_IE_OVERRIDE_on').is(':checked') ? 1 : 0,
                    'ETS_RV_IE_DATA_IMPORT[]': [],
                    'ETS_RV_IE_DELETE_ALL': $('#ETS_RV_IE_DELETE_ALL_on').is(':checked') ? 1 : 0,
                };
                $('input[name^=ETS_RV_IE_DATA_IMPORT]:checked').each(function () {
                    formData['ETS_RV_IE_DATA_IMPORT[]'].push($(this).val());
                });
                $.ajax({
                    url: btn.attr('href'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (json) {
                        btn.removeClass('active');
                        if (json) {
                            if (json.errors)
                                showErrorMessage(json.errors);
                            else {
                                if (json.msg)
                                    showSuccessMessage(json.msg);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1500)
                            }
                        }
                    },
                    error: function () {
                        btn.removeClass('active');
                    }
                });
            }
        }

    });

    $(document).on('click', '.ets_rv_import_data_csv', function (ev) {
        ev.preventDefault();
        var btn = $(this), files = $('#data_csv_or_xlsx');
        if (!btn.hasClass('active')) {
            btn.addClass('active');

            var formData = new FormData();
            formData.append(files.attr('name'), files[0].files[0]);
            formData.append('delete_all', $('input[name=data_csv_or_xlsx_delete_all]:checked').val());
            $.ajax({
                url: btn.attr('href'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                        }
                        setTimeout(function () {
                            //window.location.reload();
                        }, 1500);
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.ets_rv_import_data_qa', function (ev) {
        ev.preventDefault();
        var btn = $(this), files = $('#data_qa_csv_or_xlsx');
        if (!btn.hasClass('active')) {
            btn.addClass('active');

            var formData = new FormData();
            formData.append(files.attr('name'), files[0].files[0]);
            formData.append('delete_all', $('input[name=data_qa_csv_or_xlsx_delete_all]:checked').val());
            $.ajax({
                url: btn.attr('href'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                        }
                        setTimeout(function () {
                            //window.location.reload();
                        }, 1500);
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.ets_rv_form_import_export .ets_rv_export_data', function (ev) {
        ev.preventDefault();
        var href = $(this).attr('href') + '&ETS_RV_IE_EXPORT_PHOTOS=' + ($('#ETS_RV_IE_EXPORT_PHOTOS').is(':checked') ? 1 : 0);
        window.location.href = href;
    });

    $(document).on('change', 'input[name=ETS_RV_EMAIL_TO_CUSTOMER_RATING]', function () {
        ets_rv_op.invitationEmail($(this));
    });

    $(document).on('click', '.ets_rv_form_config li.ets-pc-nav-item', function () {
        ets_rv_op.navTabs($(this));
    });

    $(document).on('change', 'input[name=ETS_RV_DISCOUNT_OPTION]', function () {
        ets_rv_op.discountOption($(this).val());
    });

    $(document).on('change', 'input[name=ETS_RV_APPLY_DISCOUNT]', function () {
        ets_rv_op.applyDiscount($(this).val());
    });

    $(document).on('click', '.remove_ctm', function () {
        $(this).parent().parent().removeClass('active');
        $(this).parents('.form-group').find('input').val('');
        $(this).parent().remove();
    });
    $(document).on('click', '[id^=ids_language]>option', function (ev) {
        ev.preventDefault();
        ets_rv_op.multiOptions($(this));
    });
    $(document).on('change', '[id^=ids_language]', function (ev) {
        ev.preventDefault();
        ets_rv_op.multiOptions($(this));
    });
    $(document).on('click', '#product-comment-render-form .ets_rv_btn_edit.product-comment.bo', function (ev) {
        ev.preventDefault();
        $('#product-comment-render-form .panel-footer a.edit').trigger('click');
    });
    $(document).on('click', '#form-ets_rv_reply_comment .btn-group a, #form-ets_rv_comment .btn-group a, #form-ets_rv_product_comment .btn-group-action a:not(.js-ets-trans-pc-list-item), #form-ets_rv_product_comment_customer:not([action*="Staffs"]) .btn-group a, form[id^=ets_rv_] .btn-group-action a, form[id^=ets_rv_user_comment_form] .ets_rv_delete, form[id^=ets_rv_] .panel-footer a:not(.ets_rv_cancel):not(.ets-rv-back-to-list), #product-comment-render-form .btn-group-action a, #product-comment-render-form .panel-footer a:not(.ets_rv_cancel), #form-ets_rv_activity .btn-group a', function (ev) {
        ev.preventDefault();
        if ($(this).hasClass('js-ets-trans-pc-list-item'))
            return false;
        var _el = $(this),
            _url = _el.attr('href'),
            _wrapper = $('.ets-pc-form-group-wrapper').eq(0),
            _ac = 'view',
            _confirm = _el.data('confirm')
        ;
        if (!ets_rv.isLoading(_el) && _url && (!_confirm || confirm(_confirm))) {

            ets_rv.processLoading(_el);
            // Action.
            if (_el.hasClass('ets_rv_approve'))
                _ac = 'approve';
            if (_el.hasClass('ets_rv_refuse'))
                _ac = 'refuse';
            else if (_el.hasClass('ets_rv_private'))
                _ac = 'private';
            else if (_el.hasClass('edit'))
                _ac = 'edit';
            else if (_el.hasClass('delete') || _el.hasClass('ets_rv_delete'))
                _ac = 'delete';
            else if (_el.hasClass('block'))
                _ac = 'block';
            else if (_el.hasClass('unblock'))
                _ac = 'unblock';
            else if (_el.hasClass('delete_all'))
                _ac = 'delete_all';
            if (_ac === 'approve' || _ac === 'refuse' || _ac === 'edit' || _ac === 'delete' || _ac === 'view') {
                $(this).parents('.btn-group-action').find('.btn-group > a.btn').addClass('active');
            }
            var isViewReview = _ac === 'view' && (_el.parents('table[id^=table-ets_rv]').eq(0).length > 0 || _el.parents('table[class*=ets_rv_]').eq(0).length > 0) ? 1 : 0;
            $.ajax({
                type: 'post',
                url: _url,
                data: 'ajax=1&action=' + _ac,
                dataType: 'json',
                success: function (json) {
                    if (isViewReview < 1)
                        ets_rv.processFinish(_el);
                    if (json) {
                        if (isViewReview < 1)
                            _el.parents('.btn-group-action').find('.btn-group > a.btn').removeClass('active');
                        if (json.errors) {
                            showErrorMessage(json.errors);
                            ets_rv.processFinish(_el);
                        } else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                            if (json.list) {
                                _wrapper.html(json.list);
                            }
                            if (json.form) {
                                if (_ac === 'edit') {
                                    if (parseInt(_el.data('id')) > 0) {
                                        ets_rv_edited_id = parseInt(_el.data('id'));
                                    }
                                    ets_rv.initForm(json.form);
                                    ets_rv_op.multiOptions();
                                    if (json.images) {
                                        const form = $('.ets_rv_overload.active form');
                                        $('.ets_rv_upload_photos .ets_rv_upload_photo_item:not(:first)', form).remove();
                                        var photos_wrap = $('.ets_rv_upload_photos', form),
                                            maximum_of_photo = parseInt(photos_wrap.data('photos')),
                                            count = photos_wrap.find('.ets_rv_upload_photo_item').length
                                        ;
                                        json.images.forEach(function (image, i) {
                                            let currentPhoto = photos_wrap.find('.ets_rv_upload_photo_item').eq(i);
                                            if (i < maximum_of_photo) {
                                                if (i >= count - 1 && count < maximum_of_photo) {
                                                    var addPhoto = currentPhoto.clone(true, true),
                                                        randomSize = Math.random().toString(36).slice(-8)
                                                    ;
                                                    addPhoto
                                                        .find('input[type=file]')
                                                        .attr({
                                                            id: 'image_' + randomSize,
                                                            name: 'image[' + randomSize + ']'
                                                        });
                                                    photos_wrap.append(addPhoto);
                                                    count++;
                                                }
                                                currentPhoto
                                                    .find('.ets_rv_upload_photo_wrap')
                                                    .addClass('selected')
                                                    .css('background-image', 'url("' + json.images[i].url + '")')
                                                    .children('.ets_rv_btn_delete_photo')
                                                    .attr({
                                                        'data-product-comment-image-id': json.images[i].id_ets_rv_product_comment_image,
                                                        'data-product-comment-id': json.images[i].id_ets_rv_product_comment,
                                                    });
                                            }
                                        });
                                    }
                                    if (json.videos) {
                                        json.videos.forEach(function (video, i) {
                                            let video_wrap = $('.ets_rv_upload_video_item .ets_rv_upload_video_wrap').eq(i);
                                            video_wrap
                                                .addClass('selected')
                                                .children('.ets_rv_video').html('<video controls height="240" width="320"><source src="' + video.url + '" type="' + video.type + '"></source></video>')
                                            ;
                                            video_wrap
                                                .parents('.ets_rv_upload_video_item')
                                                .addClass('cms_has_video');
                                            video_wrap
                                                .children('.ets_rv_btn_delete_video')
                                                .attr({
                                                    'data-product-comment-video-id': video.id_ets_rv_product_comment_video,
                                                    'data-product-comment-id': video.id_ets_rv_product_comment,
                                                });
                                        });
                                    }
                                    $('#date_add.datetimepicker')
                                        .removeClass('hasDatepicker')
                                        .datetimepicker({
                                            changeMonth: true,
                                            changeYear: true,
                                            dateFormat: 'yy-mm-dd',
                                            timeFormat: 'hh:mm:ss tt',
                                            maxDateTime: new Date(),
                                        });
                                } else if (_ac === 'view') {
                                    if (_el.parents('form[id$=ets_rv_product_comment_customer]').length > 0) {
                                        ets_rv.processFinish(_el);
                                        ets_rv.initForm(json.form);
                                    } else if (isViewReview) {
                                        if (json.product_comment) {
                                            var prop = parseInt(json.product_comment.qa) > 0 ? 'question' : 'comment';
                                            $('.ets_rv_overload .ets_rv_form').html(json.form);
                                            productCommentsETS.paginate(json.product_comment, prop, _el);
                                            // BackOffice:
                                            $('.ets_rv_comment_list, .ets_rv_answer_list.answer').addClass('show_content');
                                            $('.ets_rv_btn_show_answer, .nb-comment.question').addClass('active');
                                        } else
                                            ets_rv.processFinish(_el);
                                        if (json.read) {
                                            _el.parents('tr')
                                                .find('td > span.ets_rv_make')
                                                .removeClass('unread')
                                                .addClass('read');
                                        }
                                    } else {
                                        ets_rv.initForm(json.form);
                                    }
                                } else if (_ac === 'refuse') {
                                    ets_rv.initForm(json.form);
                                }
                            } else {
                                if (_ac === 'approve') {
                                    _el.hide();
                                    if (json.id) {
                                        $('.ets-rv-product-comment-list-item[data-product-comment-id=' + json.id + ']').attr('data-status', 'approved');
                                    }
                                } else if (_ac === 'private') {
                                    if (json.id)
                                        $('.ets-rv-product-comment-list-item[data-product-comment-id=' + json.id + ']').attr('data-status', 'private');
                                } else if (_ac === 'delete' || _ac === 'delete_all') {
                                    ets_rv.offForm();
                                } else if (_ac === 'block' || _ac === 'unblock') {
                                    ets_rv_op.isBlock(_ac);
                                }
                            }
                            if (_ac === 'delete_all' && typeof json.customer !== "undefined")
                                $('#ets_rv_custome_id_' + json.customer).parents('tr').remove();
                        }
                    }
                },
                error: function () {
                    ets_rv.processFinish(_el);
                }
            });
        }
    });

    $(document).on('click', '.ets_rv_form .ets_rv_cancel, .ets_rv_form_off', function (ev) {
        ev.preventDefault();
        var _el = $(this);
        if (_el.hasClass('ets_rv_back_to_view')) {
            var _form = _el.parents('form'),
                _url = _form.attr('action'),
                _wrapper = $('.ets-pc-form-group-wrapper').eq(0),
                _ac = 'view',
                table = _form.attr('id').replace(/_form_(\d+)/i, '')
            ;
            if (!ets_rv.isLoading(_el) && _url) {
                ets_rv.processLoading(_el);
                $.ajax({
                    type: 'post',
                    url: _url + '&' + _ac + table + '&id_' + table + '=' + $('#id_' + table).val(),
                    data: 'ajax=1&action=' + _ac,
                    dataType: 'json',
                    success: function (json) {
                        ets_rv.processFinish(_el);
                        if (json) {
                            if (json.errors)
                                showErrorMessage(json.errors);
                            else {
                                if (typeof json.msg !== "undefined" && json.msg)
                                    showSuccessMessage(json.msg);
                                if (typeof json.list !== "undefined" && json.list) {
                                    _wrapper.html(json.list);
                                }
                                if (typeof json.form !== "undefined" && json.form) {
                                    if (_ac === 'edit') {
                                        ets_rv.initForm(json.form);
                                        ets_rv_op.multiOptions();
                                    } else {
                                        //$('.ets_rv_overload.active').removeClass('active');
                                        $('.ets_rv_form').html(json.form);
                                        productCommentsETS.paginate(json.product_comment, parseInt(json.product_comment.qa) > 0 ? 'question' : 'comment');
                                    }
                                }
                            }
                        }
                    },
                    error: function () {
                        ets_rv.processFinish(_el);
                    }
                });
            }
        } else
            ets_rv.offForm();
    });
    $(document).on('click', 'button[name=submitAddets_rv_product_comment], button[name=submitAddets_rv_comment], button[name=submitAddets_rv_reply_comment]', function (ev) {
        ev.preventDefault();
        var _el = $(this),
            _form = _el.parents('form').eq(0),
            _url = _form.attr('action')
        ;
        if (!ets_rv.isLoading(_el) && _url) {
            ets_rv.processLoading(_el);
            var formData = new FormData(_form.get(0));
            formData.append('action', 'save');
            formData.append('ajax', 1);
            $('input[name="id_customer"],input[name="customer_name"],input[name="id_product"]', '#title_' + id_language, '#content_' + id_language).removeClass('error');
            $.ajax({
                type: 'post',
                url: _url,
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                dataType: 'json',
                success: function (json) {
                    ets_rv.processFinish(_el);
                    if (json) {
                        if (json.errors) {
                            showErrorMessage(json.errors);
                            if ($('input[name="customer_name"]').val() == '')
                                $('input[name="customer_name"]').addClass('error');
                            else
                                $('input[name="customer_name"]').removeClass('error');
                            if ($('input[name="email"]').val() !== '' && !/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test($('input[name="email"]').val()))
                                $('input[name="email"]').addClass('error');
                            else
                                $('input[name="email"]').removeClass('error');
                            if ($('input[name="id_customer"]').val() == '') {
                                $('input[name="search_customer"]').addClass('error');
                            } else {
                                $('input[name="search_customer"]').removeClass('error');
                            }
                            if ($('input[name="id_product"]').val() == '') {
                                $('input[name="search_product"]').addClass('error');
                            } else {
                                $('input[name="search_product"]').removeClass('error');
                            }
                            if ($('input[name="customer_name"]').val() == '' && $('input[name="customer_name"]').parents('.form-group.customer_type.guest:visible').length > 0) {
                                $('input[name="customer_name"]').addClass('error');
                            } else {
                                $('input[name="customer_name"]').removeClass('error');
                            }
                            if ($('#title_' + id_language + '[required="required"]').val() == '') {
                                $('#title_' + id_language + '[required="required"]').addClass('error');
                            } else {
                                $('#title_' + id_language).removeClass('error');
                            }
                            if ($('#content_' + id_language).val() == '') {
                                $('#content_' + id_language).addClass('error');
                            } else {
                                $('#content_' + id_language).removeClass('error');
                            }

                            if ($('input[name="id_customer"]').val() == '' && $('.form-group.customer_type.customer:not(:hidden)').length > 0)
                                $('input[name="search_customer"]').focus();
                            else if ($('input[name="customer_name"]').val() == '' && $('.form-group.customer_type.guest:not(:hidden)').length > 0)
                                $('input[name="customer_name"]').focus();
                            else if ($('input[name="id_product"]').val() == '')
                                $('input[name="search_product"]').focus();
                            else if ($('#title_' + id_language + '[required="required"]').val() == '')
                                $('#title_' + id_language + '[required="required"]').focus();
                            else if ($('#content_' + id_language).val() == '')
                                $('#content_' + id_language).focus();
                        } else {
                            if (typeof json.msg !== "undefined" && json.msg) {
                                showSuccessMessage(json.msg);
                            }
                            if (typeof json.list !== "undefined" && json.list) {
                                $('.ets-pc-form-group-wrapper').html(json.list);
                                ets_rv.checkBoxAll();
                            }
                            $('a.ets_rv_edit[data-id=' + ets_rv_edited_id + ']').parents('tr').addClass('ets_rv_highlight_tr');
                            if (json.id_product_comment) {
                                if (!_form.find('input[name=id_ets_rv_product_comment]').length) {
                                    _form.prepend('<input id="id_ets_rv_product_comment" type="hidden" name="id_ets_rv_product_comment" value="' + json.id_product_comment + '" />');
                                }
                            }
                            if (json.form_title) {
                                _form.find('.panel-heading').html('<i class="icon-cogs"></i>' + json.form_title);
                            }
                        }
                    }
                    $('.datepicker').attr('autocomplete', 'off');
                },
                error: function () {
                    ets_rv.processFinish(_el);
                }
            });
        }
    });
    $(document).on('click', 'a[id$=ets_rv_product_comment-new]', function (ev) {
        ev.preventDefault();
        var _el = $(this),
            _url = _el.attr('href')
        ;
        if (!ets_rv.isLoading(_el) && _url) {
            ets_rv.processLoading(_el);
            $.ajax({
                type: 'post',
                url: _url,
                data: 'ajax=1',
                dataType: 'json',
                success: function (json) {
                    ets_rv.processFinish(_el);
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errros);
                        else {
                            if (json.form !== undefined && json.form) {
                                ets_rv.initForm(json.form);
                                ets_rv_op.multiOptions();
                                ets_rv_search.searchProduct();
                                ets_rv_search.searchCustomer($('input[name=search_customer]'), true, false);
                                if ($('input[name="customer_type"]').length) {
                                    $('.customer_type.guest').hide();
                                }
                                //chatGTPinit();
                            }
                        }
                    }
                },
                error: function () {
                    ets_rv.processFinish(_el);
                }
            });
        }
    });

    $('input[name=ETS_RV_RECAPTCHA_ENABLED]').change(function () {
        ets_rv_op.reCAPTCHA($(this));
    });
    $('input[name=ETS_RV_RECAPTCHA_TYPE]').change(function () {
        ets_rv_op.reCAPTCHAType($(this));
    });
    $('input[name=ETS_RV_ALLOW_GUESTS]').change(function () {
        ets_rv_op.allowGuests($(this));
    });
    $('input[name=ETS_RV_PURCHASED_PRODUCT]').change(function () {
        ets_rv_op.purchasedProduct($(this));
    });
    $('input[name=ETS_RV_AUTO_APPROVE]').change(function () {
        ets_rv_op.autoApprove($(this));
    });
    $('input[name=ETS_RV_UPLOAD_PHOTO_ENABLED]').change(function () {
        ets_rv_op.enabledUploadPhotos($(this));
    });
    $('input[name=ETS_RV_UPLOAD_VIDEO_ENABLED]').change(function () {
        ets_rv_op.enabledUploadVideos($(this));
    });
    if ($.fn.autocomplete) {
        $('#ets_rv_managitor_email')
            .autocomplete(
                ETS_RV_REVIEW_LINK + '&searchCustomer=1&ajax=1&action=searchCustomer&time' + new Date().getTime(), {
                    resultsClass: "ets_rv_customer_results",
                    appendTo: '.ets_rv_customers',
                    delay: 100,
                    minChars: 1,
                    autoFill: true,
                    max: 20,
                    matchContains: true,
                    mustMatch: true,
                    scroll: false,
                    cacheLength: 0,
                    multipleSeparator: '||',
                    formatItem: function (item) {
                        return '<span data-id="' + item[0] + '">' + item[0] + ' - ' + item[1] + ' ' + item[2] + ' (' + item[3] + ') </span>';
                    }
                })
            .result(function (event, item) {
                if (item == null) {
                    return false;
                }
                var ets_rv_managitor = $('#ETS_RV_MANAGITOR').val().trim(),
                    listUL = $('.ets_rv_customers'),
                    customers = ets_rv_managitor !== '' ? ets_rv_managitor.split(',') : [];
                ;
                if (ets_rv_managitor === '' || ets_rv_managitor.indexOf(item[0]) < 0) {
                    customers.push(item[0]);
                    listUL.append('<li class="ets_rv_customer" data-id="' + item[0] + '">' + item[0] + ' - ' + item[1] + ' ' + item[2] + ' (' + item[3] + ') <span class="remove_ctm"></span></li>');
                    $('#ETS_RV_MANAGITOR').val(customers.join(','));
                }
                $('#ets_rv_managitor_email').val('');
            });

        $('.ets_rv_customer .remove_ctm').click(function () {
            var ele = $(this),
                li = ele.parent('li'),
                customer_ids = $('#ETS_RV_MANAGITOR').val(),
                customers = customer_ids.val().split(','),
                id_customer = li.data('id') + ''
            ;
            customer_ids.val(ets_rv.removeIds(customers, id_customer));
            li.remove();
        });
    }

    $(document).on('change', 'input[name*=ETS_RV_DESIGN_COLOR]', function () {
        $('#ets_rv_change_color').val(1);
    });
    $('.datepicker').attr('autocomplete', 'off');

    $(document).ajaxComplete(function (event, xhr, settings) {
        $('.datepicker').attr('autocomplete', 'off');
    });

    $(document).on('click', '#ets_rv_cronjob_link', function (ev) {
        ev.preventDefault();
        var _self = $(this);
        if (!_self.hasClass('active')) {
            _self.addClass('active');
            $.ajax({
                type: 'post',
                data: 'ajax=1&action=cronjobExecute&secure=' + $('#ETS_RV_SECURE_TOKEN').val(),
                dataType: 'json',
                success: function (json) {
                    _self.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            showErrorMessage(json.errors);
                        } else {
                            if (json.result)
                                showSuccessMessage(json.result);
                            if (json.log)
                                $('#ETS_RV_CRONJOB_LOG').val($('#ETS_RV_CRONJOB_LOG').val() + json.log + "\r\n");
                        }
                    }
                },
                error: function () {
                    _self.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.ets-rv-clean-log', function (e) {
        if (!confirm(ETS_RV_CLEAN_LOG_CONFIRM))
            e.preventDefault();
    });

    $(document).on('click', '.ets_rv_clear_log', function (e) {
        e.preventDefault();

        var _self = $(this);

        if (!_self.hasClass('active') && confirm(ETS_RV_CLEAN_LOG_CONFIRM)) {
            _self.addClass('active');
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: 'ajax=1&action=clearLog',
                success: function (json) {
                    _self.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            if (json.msg)
                                showErrorMessage(json.msg);
                        } else {
                            showSuccessMessage(json.msg);
                            $('#ETS_RV_CRONJOB_LOG').val('');
                        }
                    }
                },
                error: function () {
                    _self.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.table.ets_rv_mail_log a.btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        if (!btn.hasClass('active')) {
            btn.addClass('active');
            $.ajax({
                url: btn.attr('href'),
                data: 'ajax=1&action=renderView',
                type: 'GET',
                dataType: 'json',
                success: function (json) {
                    if (json) {
                        btn.removeClass('active');
                        if (json.errors) {
                            showErrorMessage(json.errors);
                        } else {
                            if (json.html) {
                                if ($('.ets-rv-overload.ets_rv_mail_log_form').length < 1) {
                                    $('.ets-pc-form-group-wrapper').append(json.html);
                                } else {
                                    $('.ets-rv-overload.ets_rv_mail_log_form').replaceWith(json.html);
                                }
                                $('.ets-rv-overload.ets_rv_mail_log_form').addClass('active');
                            }
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.ets-rv-close-view', function () {
        $('.ets-pc-form-group-wrapper .ets-rv-overload.active').removeClass('active');
    });

    $(document).on('change', 'input[name=ETS_RV_CACHE_ENABLED]', function () {
        ets_rv.cacheEnabled($(this));
    });
    $(document).on('click', 'a.ets_rv_clear_cache', function (e) {
        e.preventDefault();
        var _this = $(this);
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            $.ajax({
                url: _this.attr('href'),
                type: 'POST',
                dataType: 'json',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            if (json.msg)
                                showSuccessMessage(json.msg);
                        }
                    }
                }, error: function () {
                    _this.removeClass('active');
                }

            })
        }
    });
    $(document).on('click', '#ets_rv_refuse_form_submit', function (e) {
        e.preventDefault();
        var _this = $(this);
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            var form = _this.closest('form');
            var formData = new FormData(form.get(0));
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    _this.removeClass('active');
                    if (json) {
                        if (json.errors)
                            showErrorMessage(json.errors);
                        else {
                            ets_rv.offForm();
                            if (json.msg)
                                showSuccessMessage(json.msg);
                            if (json.list) {
                                $('.ets-pc-form-group-wrapper').html(json.list);
                            }
                        }
                    }
                }, error: function () {
                    _this.removeClass('active');
                }

            })
        }
    });

    if ($('table.ets_rv_tracking').length > 0) {
        var filterthLength = $('table.ets_rv_tracking tr.filter.row_hover th').length;
        var filtertdLength = $('table.ets_rv_tracking tbody tr:first-child td').length;
        if (filterthLength > filtertdLength) {
            $('table.ets_rv_tracking tbody tr').append('<td></td>');
        }
    }
});
$(document).ready(function () {
    $(document).on('click', '.btn-group-action .btn-group:not(.ets_dropdown)', function (e) {
        if ($('.ets_dropdownmenucontent').length > 0) {
            $('.ets_dropdownmenucontent').remove();
        }
        if (e.target === this) {
            var _pos_top = $(this).offset().top + $(this).outerHeight();
            var _pos_left = $(this).offset().left;
            var _pos_right = $(window).width() - $(this).offset().left - $(this).outerWidth();
            var _thiswidth = $(this).outerWidth();
            var _content_dropdown = $(this).find('.dropdown-menu').html();
            $('body').append('<div class="ets_dropdownmenucontent" style="min-width:' + _thiswidth + 'px;position:absolute;top:' + _pos_top + 'px;right:' + _pos_right + 'px;"><ul class="dropdown-menu">' + _content_dropdown + '</ul></div>');
            $(this).addClass('ets_dropdown');
        }
    });
    $(document).on('click', '.btn-group-action .btn-group.ets_dropdown', function (e) {
        $('.ets_dropdownmenucontent').remove();
        $(this).removeClass('ets_dropdown');
    });
    $(document).mouseup(function (e) {
        var _ets_dropdownmenucontent = $('.ets_dropdownmenucontent');
        var _btn_group = $('.btn-group.ets_dropdown');
        if (!_ets_dropdownmenucontent.is(e.target) && _ets_dropdownmenucontent.has(e.target).length === 0 && !_btn_group.is(e.target) && _btn_group.has(e.target).length === 0) {
            $(".ets_dropdownmenucontent").remove();
            $('.btn-group-action .btn-group.ets_dropdown').removeClass('ets_dropdown');
        }
    });
    $(document).on('click', '.ets_dropdownmenucontent li', function (e) {
        var _index = $(this).index() + 1;
        $('.btn-group-action .btn-group.ets_dropdown .dropdown-menu li:nth-child(' + _index + ') a').trigger('click');
    });


    $(document).on('click', '.panel-footer #configuration_form_submit_btn,.panel-footer #configuration_form_submit_btn,button[name="submitAddets_rv_staff"],button[name="submitAddets_rv_staffAndStay"]', function (e) {
        $(this).addClass('active');
    });
    //fixed table filter
    if ($('.ets_rv_email_queue thead tr.filter.row_hover').length > 0) {
        var _table = $('.ets_rv_email_queue');
        var _Formtable = $('.ets_rv_email_queue').parents('form');
        var _FormtableFixing = $('.ets_rv_email_queue').parents('form.fixed_load');
        column_filters_fixed_height = _table.find('thead tr.filter.row_hover').outerHeight();
        var width_child = _table.width();
        var width_parent = _table.parent().width();
        _table.before('<div class="scroll_tabheader" style="width:' + width_parent + 'px;"><div class="scroll_tabheader_bar" style="width:' + width_child + 'px;"></div></div>');
        if (width_child > width_parent)
            $('.scroll_tabheader').addClass('show');
        _Formtable.addClass('fixed_load');
        var sticky_navigation_offset_top = _table.find('thead').offset().top;
        var headerFloatingHeight = $('.page-head').outerHeight() + $('#header_infos').outerHeight() + $('.ets-pc-panel-heading').outerHeight() - 1;
        insertwidthvaluetable();
        var sticky_navigation = function () {
            var scroll_top = $(window).scrollTop();
            var parent_width = $('#order_filter_form .table-responsive-row').width();
            if (scroll_top > sticky_navigation_offset_top - headerFloatingHeight) {
                if ($('.ets_thead').length == 0) {
                    _table.find('thead').addClass('ets_thead');
                }
                if (!_table.find('.ets_thead').hasClass('no_scroll_heading')) {
                    _table.find('.ets_thead').addClass('scroll_heading').css({'margin-top': headerFloatingHeight + 'px'});
                    $('.scroll_tabheader').addClass('scroll_heading').css({'margin-top': (headerFloatingHeight + _table.find('.ets_thead').height()) + 'px'});
                }
                var theade_heaight = _table.find('.ets_thead.scroll_heading').height();
                $('.table-responsive-row').css({'margin-top': theade_heaight + 'px'});
                var left = $('.table-responsive-row').scrollLeft();
                _table.find('thead.scroll_heading').css('margin-left', -left);
                _FormtableFixing.addClass('fixing');
                if (_FormtableFixing.length > 0) {
                    var loadmore_top = theade_heaight + headerFloatingHeight + 100;
                    var nav_left_space = 165 - $('.nav-bar').width();
                    $('#ets_warningGradientOuterBarG').css({'margin-top': loadmore_top + 'px'}).css({'margin-left': nav_left_space + 'px'});
                } else {
                    $('#ets_warningGradientOuterBarG').css({'margin-top': '-' + theade_heaight + 'px'});
                }
            } else {
                _Formtable.find('.ets_thead').removeClass('scroll_heading').css({'margin-top': ''}).css({'width': ''});
                $('.scroll_tabheader').removeClass('scroll_heading').css({'margin-top': ''});
                $(this).parents('table.table.product').find('thead').find('th').css({'width': 'auto'});
                $('.table-responsive-row').css({'margin-top': ''});
                $('#ets_warningGradientOuterBarG').css({'margin-top': ''}).css({'margin-left': ''});
                if ($('.ets_thead').length > 0) {
                    $('.ets_thead').removeClass('ets_thead');
                }
                _FormtableFixing.removeClass('fixing');
            }
        };
        $(window).scroll(function () {
            sticky_navigation();
            if (_FormtableFixing.length > 0) {
                if ($('#ets_warningGradientOuterBarG').length <= 0) {
                    $('#order_filter_form.fixed_load').find('.table-responsive-row').append('<div id="ets_warningGradientOuterBarG"><div id="ets_warningGradientFrontBarG" class="ets_warningGradientAnimationG"><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div></div></div>');
                }
            }
        });
    }
    $('.table-responsive-row').on('scroll', function (e) {
        var left = $(this).scrollLeft();
        $('thead.scroll_heading').css('margin-left', -left);
        $('.scroll_tabheader').scrollLeft(left);
    });
    $('.scroll_tabheader').on('scroll', function (e) {
        var left_h = $(this).scrollLeft();
        $('thead.scroll_heading').css('margin-left', -left_h);
        $('.table-responsive-row').scrollLeft(left_h);
    });
    $(window).resize(function () {
        setTimeout(function () {
            $(".ets_rv_email_queue th.has_loaded").removeAttr("style");
            insertwidthvaluetable();
            var _table = $('.ets_rv_email_queue');
            var index = 0;
            $('.scroll_tabheader,.scroll_tabheader_bar').removeAttr("style");
            column_filters_fixed_height = _table.find('thead tr.filter.row_hover').outerHeight();
            var width_child = _table.width();
            var width_parent = _table.parent().width();
            $('.scroll_tabheader').css('width', width_parent + 'px');
            $('.scroll_tabheader_bar').css('width', width_child + 'px');
        }, 10);
    });


});

function insertwidthvaluetable() {
    var _Formtable = $('.ets_rv_email_queue').parents('form');
    var index = 0;
    var thindex = 0;
    if (_Formtable.find('tbody tr:not(.empty_row)').length > 0) {
        if (_Formtable.find('tr th.has_loaded').length > 0 && $('th.has_loaded[style^=""]').length > 0) {
            _Formtable.find$('thead tr:first-child th').each(function () {
                var thwidth = $(this).outerWidth();
                _Formtable.find('tr td').eq(thindex).attr('style', 'min-width:' + thwidth + 'px!important;max-width:' + thwidth + 'px!important;width:' + thwidth + 'px!important;').addClass('has_loaded');
                _Formtable.find('tr th').eq(thindex).attr('style', 'min-width:' + thwidth + 'px!important;max-width:' + thwidth + 'px!important;width:' + thwidth + 'px!important;').addClass('has_loaded');
                thindex++;
            });
        } else {
            if (_Formtable.find('tbody tr:first-child td:not(.no-product)').length) {
                _Formtable.find('tbody tr:first-child td').each(function () {
                    var $width = $(this).outerWidth();
                    _Formtable.find('tr:first-child th').eq(index).attr('style', 'min-width:' + $width + 'px!important;max-width:' + $width + 'px!important;width:' + $width + 'px!important;').addClass('has_loaded');
                    _Formtable.find('tr.filter.row_hover th').eq(index).attr('style', 'min-width:' + $width + 'px!important;max-width:' + $width + 'px!important;width:' + $width + 'px!important;').addClass('has_loaded');
                    _Formtable.find('tbody td').eq(index).attr('style', 'min-width:' + $width + 'px!important;max-width:' + $width + 'px!important;width:' + $width + 'px!important;').addClass('has_loaded');
                    index++;
                });
            }
        }
    }
}