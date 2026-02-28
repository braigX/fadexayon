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
var commonPCETS = {
    clearPostForm: function (form) {
        form.find('input[type="text"]').val('').removeClass('valid error');
        form.find('textarea').val('').removeClass('valid error');
        form.find('.criterion-item').removeClass('valid error');
        if (form.find('.criterion-rating .ets-rv-grade-stars').length > 0) {
            form.find('.criterion-rating input').val(parseInt(form.find('.criterion-rating .ets-rv-grade-stars').data('grade'))).change();
        }
        form.find('input[type=file]').val('');
        form.find('.ets_rv_upload_photo_wrap').css('background-image', '').attr('title', '').removeClass('selected').parents('.cm_img_uploaded').removeClass('cm_img_uploaded');
        form.find('.ets_rv_upload_video_wrap').removeClass('selected').parents('.cms_has_video').removeClass('cms_has_video');
        form.find('.ets_rv_upload_video_wrap').find('.ets_rv_video').html('');
        form.find('.ets_rv_upload_video_wrap').find('.ets_rv_btn_delete_video').removeAttr('data-product-comment-video-id');
        form.find('.ets_rv_upload_video_wrap').find('.ets_rv_btn_delete_video').removeAttr('data-product-comment-id');
        form.find('.ets_rv_error').hide();
        $('.post-comment-buttons button, .post-question-buttons button').removeClass('active');
        form.find('input[name=id_product_comment]').val(0);
        if (form.hasClass('.g-loaded'))
            reCaptchaETS.reset(form);
    },
    showPostedModal: function (prop, message) {
        $('#ets-rv-post-product-' + prop + '-modal').ETSModal('hide').removeClass('edit');
        $('#ets-rv-product-' + prop + '-post-error').ETSModal('hide');
        commonPCETS.clearPostForm($('#ets-rv-post-product-' + prop + '-form'));
        if (message)
            $('#ets-rv-product-' + prop + '-posted-modal-message').html(message);
        $('#ets-rv-product-' + prop + '-posted-modal').ETSModal('show');
    },
    showVoucherModal: function (message) {
        if (message) {
            $('#general-voucher-modal-message').html(message);
        }
        $('#ets-rv-post-product-comment-modal').ETSModal('hide');
        $('#ets-rv-product-comment-post-error').ETSModal('hide');
        $('#general-voucher-modal').ETSModal('show');
    },
    validateFormData: function (formData, form) {
        var isValid = true,
            focus = false,
            prop = form.data('type')
        ;
        formData.forEach(function (formField) {
            var fieldSelector = form.find('[name="' + formField.name + '"]'),
                isRating = formField.name.match(/criterion\[\d+\]/);
            if (isRating)
                fieldSelector = $(fieldSelector).parents('.criterion-item');
            if ((!formField.value || isRating && parseInt(formField.value) <= 0 && $(fieldSelector).parents('.ets-pc-criterion.active').length > 0) && (ETS_RV_REQUIRE_TITLE || prop === 'question' || formField.name !== 'comment_title')) {
                $(fieldSelector).addClass('error');
                isValid = false;
                if (!focus) {
                    focus = true;
                    $(fieldSelector).focus();
                }
            } else {
                $(fieldSelector).removeClass('error');
            }
        });
        return isValid;
    },
    submit: function (prop) {
        $('#ets-rv-post-product-' + prop + '-form').submit(function (event) {
            event.preventDefault();
            var form = $(this),
                prop = form.attr('id').indexOf('question') !== -1 ? 'question' : 'comment',
                reCAPTCHA = form.find('.g-recaptcha'),
                id_product_comment = parseInt(form.find('input[name=id_product_comment]').val()),
                btn = form.find('.ets-rv-btn-' + prop)
            ;
            form.find('.ets_rv_error').hide();
            if (!ETS_RV_RECAPTCHA_VALID && reCAPTCHA.length > 0) {
                reCAPTCHA.addClass('error');
            } else if (ETS_RV_RECAPTCHA_VALID && reCAPTCHA.hasClass('error')) {
                reCAPTCHA.removeClass('error');
            }
            if (!commonPCETS.validateFormData(form.serializeArray(), form)) {
                return;
            }
            var formData = new FormData(form.get(0));
            if (form.find('input[type="file"]').length > 0 && funcPCETS.isSafari()) {
                form.find('input[type="file"]').each(function () {
                    if (document.getElementById($(this).attr('id')).files.length === 0) {
                        formData.delete($(this).attr('id'));
                    }
                });
            }
            var post_url = form.attr('action');
            if (!btn.hasClass('active') && post_url) {
                btn.addClass('active');
                $.ajax({
                    url: post_url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (jsonData) {
                        form.find('.ets-rv-btn-comment').removeClass('active');
                        if (jsonData) {
                            if (jsonData.success) {
                                commonPCETS.showPostedModal(prop, jsonData.msg ? jsonData.msg : '');
                                if (typeof ETS_RV_ADDED_PRODUCT_COMMENT !== "undefined") {
                                    ETS_RV_ADDED_PRODUCT_COMMENT = true;
                                }
                                if (jsonData.product_comment) {
                                    var result = jsonData.product_comment;
                                    if (id_product_comment > 0) {
                                        const $productComment = $('.ets-rv-product-comment-list-item[data-product-comment-id=' + result.id_ets_rv_product_comment + ']');
                                        const $comment_title = $('.ets-rv-product-comment-title-html', $productComment);
                                        $comment_title.html(result.title);
                                        if (!result.title)
                                            $comment_title.hide();
                                        else
                                            $comment_title.show();
                                        $('.product-comment-content-html', $productComment).html(funcPCETS.n2br(result.content));

                                        if (prop !== 'question') {
                                            $('.product-comment-image-html', $productComment).html(result.images);
                                            $('.product-comment-video-html', $productComment).html(result.videos);
                                            $('.ets_rv_grade_stars', $productComment).attr('data-grade', result.grade.toFixed(1));
                                            if (parseFloat(result.grade) > 0) {
                                                $('.ets_rv_grade_stars', $productComment).show();
                                            } else
                                                $('.ets_rv_grade_stars', $productComment).hide();
                                            $('.ets_rv_average_grade_item', $productComment).html(result.grade.toFixed(1));
                                            if (result.criterion.length > 1) {
                                                var popover = '';
                                                result.criterion.forEach(function (item) {
                                                    popover += '<li class="criterion-item"><span class="criterion-name">' + item.name + ':</span><span class="ets_rv_grade_stars' + (ETS_RV_DESIGN_COLOR1 ? ' color1' : '') + '" data-grade="' + item.grade + '" data-rate-empty="☆☆☆☆☆" data-rate-full="★★★★★"></span> <span class="criterion-grade">(' + item.grade + ')</span></li>';
                                                });
                                                if (popover) {
                                                    $('.popover_wrap', $productComment).length > 0 ? $('.popover_wrap', $productComment).html(popover) : $('.ets_rv_grade_stars', $productComment).append('<ul class="popover_wrap">' + popover + '</ul>');
                                                }
                                            }
                                        }
                                        if (result.upd_date) {
                                            $('.review-date-upd', $productComment).show().find('.review-upd-date').html(result.upd_date);
                                        }
                                        $('.review-date-add', $productComment).html(result.time_elapsed);
                                        if (result.customer_name)
                                            $('.ets-rv-comment-author-name', $productComment).html(result.customer_name);
                                    } else {
                                        productCommentsETS.add(result, true);
                                        if (parseFloat(result.grade) > 0) {
                                            productCommentsETS.updateCriterion('add', result.nb_rate);
                                        }
                                        //sua:
                                        if (prop !== 'question') {
                                            if (result.review_allowed) {
                                                $('#ets-rv-post-product-comment-form').removeClass('hide');
                                                $('.ets_rv_review_error').addClass('hide');
                                            } else {
                                                $('#ets-rv-post-product-comment-form').addClass('hide');
                                                $('.ets_rv_review_error').removeClass('hide');
                                            }
                                        }
                                        if (result.id_product && result.id_order) {
                                            let btn = $('a.ets_rv_product_id[data-id-product=' + result.id_product + '][data-id-order=' + result.id_order + ']');
                                            if (btn.length > 0) {
                                                btn.parents('tr').remove();
                                            }
                                        }
                                        $('.ets_rv_wrap').show();
                                        $('.ets-rv-product-comments-additional-info .ets-rv-btn-comment').addClass('ets-rv-hidden');
                                    }
                                    $('.ets_rv_tab.active:not(.ets_rv_waiting_for_review), .ets_rv_tab_content.active').removeClass('active');
                                    $('.ets_rv_tab[data-tab-id=ets-rv-product-' + prop + 's-list]').show().addClass('active');
                                    $('#ets-rv-product-' + prop + 's-list').addClass('active');

                                    if ($('#ets-rv-product-' + prop + 's-list .ets-rv-product-comment-list-item').length > 0) $('#ets-rv-product-' + prop + 's-list #product-comments-list-footer .ets-rv-' + prop + '.empty').hide();
                                }
                                if (jsonData.stats) {
                                    funcPCETS.stats(jsonData.stats, prop);
                                    // Change stats and rating:
                                    var stats = jsonData.stats;
                                    if (prop === 'comment' && !$('.ets_rv_stats_review, .ets_rv_tab_reviews').is(':visible') && parseFloat(stats.average_grade) > 0 && parseInt(stats.nb_reviews) > 0) {
                                        $('.ets_rv_tab_reviews:hidden').show().removeClass('ets_rv_hide');
                                        $('.ets_rv_stats_review:hidden').show().removeClass('ets_rv_hide').next('.ets_rv_modal_review.center').removeClass('center');
                                    } else if (prop === 'question' && !$('.ets_rv_tab_questions').is(':visible') && parseInt(stats.nb_questions) > 0) {
                                        $('.ets_rv_tab_questions:hidden').show();
                                    }
                                    $('.ets_rv_wrap:hidden').show();
                                    // End:
                                    $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars').attr('data-grade', jsonData.stats.average_grade.toFixed(1));
                                    $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars .ets-rv-comments-nb').text('(' + jsonData.stats.nb_reviews + ')');
                                    if (parseFloat(jsonData.stats.average_grade) > 0)
                                        $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user').removeClass('ets-rv-hidden');
                                    else
                                        $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user').addClass('ets-rv-hidden');
                                }
                                if (jsonData.voucher) {
                                    commonPCETS.showVoucherModal(jsonData.voucher);
                                }
                                if (jsonData.photos) {
                                    productCommentsETS.initSlider(jsonData.photos);
                                }
                                if (prop === 'question')
                                    $('.ets_rv_review_photos_wrap').removeClass('active');
                                else
                                    $('.ets_rv_review_photos_wrap').addClass('active');
                            } else {
                                if (jsonData.errors) {
                                    var errorList = '<ul>';
                                    for (var i = 0; i < jsonData.errors.length; ++i) {
                                        errorList += '<li>' + jsonData.errors[i] + '</li>';
                                    }
                                    errorList += '</ul>';
                                    commonPCETS.showPostErrorModal(errorList, prop);
                                } else {
                                    const decodedErrorMessage = $("<div/>").html(jsonData.error).html();
                                    commonPCETS.showPostErrorModal(decodedErrorMessage, prop);
                                }
                            }
                        } else {
                            commonPCETS.showPostErrorModal(productCommentPostErrorMessage, prop);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        form.find('.ets-rv-btn-comment').removeClass('active');
                        commonPCETS.showPostErrorModal(productCommentPostErrorMessage, prop);
                        if (jqXHR.responseText)
                            console.log(jqXHR.responseText, textStatus, errorThrown);
                    }
                });
            }
            return false;
        });
    },
    showPostErrorModal: function (errorMessage, prop) {
        var prop = prop || 'comment', _error = $('#ets-rv-post-product-' + prop + '-form .ets_rv_error');
        if (_error.length < 1)
            $('.ets-rv-post_content_footer_modal').before('<div class="bootstrap"><div class="ets_rv_error alert alert-danger"></div></div>');
        $('#ets-rv-post-product-' + prop + '-form .ets_rv_error').html(errorMessage).show();
        reCaptchaETS.reset($('#ets-rv-post-product-' + prop + '-form.g-loaded'));
        $('.post-comment-buttons button, .post-question-buttons button').removeClass('active');
    },
    showPostModal: function (prop, action) {
        var prop = prop || 'comment',
            form = $('#ets-rv-post-product-' + prop + '-form'),
            postModal = $('#ets-rv-post-product-' + prop + '-modal')
        ;
        $('#ets-rv-post-product-' + prop + '-form .ets_rv_error').remove();
        $('#ets-rv-post-product-' + prop + '-form .ets-rv-modal-header h2, #ets-rv-post-product-' + prop + '-form button.btn-' + prop + ' span.title').html(function () {
            return $(this).data(action + '-title');
        });
        $('#ets-rv-product-' + prop + '-posted-modal').ETSModal('hide');
        $('#ets-rv-product-' + prop + '-post-error').ETSModal('hide');
        postModal.ETSModal('show');
        if (action === 'edit') {
            postModal.addClass('edit');
        } else
            postModal.removeClass('edit');
        $('.ets_rv_upload_photos .ets_rv_upload_photo_item:not(:first)', form).remove();
        if (!form.hasClass('g-loaded')) {
            reCaptchaETS.onLoad(form);
        } else {
            reCaptchaETS.reset(form);
        }
        this.initRate(prop);
    },
    filter: function (ele, sortBy, grade, prop) {
        const commentsList = $('#ets-rv-product-' + prop + 's-list');
        if (!ele.hasClass('loading') && commentsList.data('comments-url') !== '') {
            ele.addClass('loading');
            $.ajax({
                url: commentsList.data('comments-url'),
                type: 'POST',
                data: '__ac=list_product_comment&sort_by=' + sortBy.data('sort') + (prop !== 'question' ? '&grade=' + grade.data('grade') : ''),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('loading');
                    if (json) {
                        productCommentsETS.populate(json.comments, prop);
                        var btn = $('.ets_rv_product_comment_load_more', commentsList);
                        const rest = parseInt(json.reviews_nb) - parseInt(json.begin);
                        if (parseInt(json.begin) >= parseInt(json.reviews_nb) || parseInt(json.reviews_per_page) <= 0)
                            btn.hide();
                        else if (json.success) {
                            btn.show().attr({
                                'data-begin': json.begin,
                                'data-reviews-per-page': json.reviews_per_page,
                                'data-rest': rest,
                            });
                            if (parseInt(rest) > parseInt(json.reviews_per_page)) {
                                btn.html(function () {
                                    return (rest > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, json.reviews_per_page).replace(/%2s/g, rest);
                                });
                            } else {
                                btn.html(function () {
                                    return (rest > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, rest);
                                });
                            }
                        }
                    }
                },
                error: function () {
                    ele.removeClass('loading');
                }
            });
        }
    },
    tabFilter: function (el) {
        var currentUrl = window.location.href,
            matches = currentUrl.match(/\#(.+)$/i),
            selectedTab = $('.ets_rv_filter .ets_rv_tab').first(),
            tabCollection = ['review', 'question']
        ;
        if (matches !== null && typeof matches[1] !== "undefined" && tabCollection.indexOf(matches[1]) !== -1) {
            selectedTab = $('.ets_rv_filter .ets_rv_tab_' + matches[1] + 's');
        }
        var choiceTab = el || selectedTab;
        if (!choiceTab.is(':visible')) {
            choiceTab = $('.ets_rv_filter .ets_rv_tab:visible').eq(0);
        }
        $('.ets_rv_filter .ets_rv_tab.active, .ets_rv_tabs .ets_rv_tab_content').removeClass('active');
        choiceTab.addClass('active');
        $('#' + choiceTab.data('tab-id')).addClass('active');
        $('.ets_rv_tab:not(.active) .ets_rv_selection').html(function () {
            return $(this).attr('data-default');
        });
        if (choiceTab.hasClass('ets_rv_tab_questions')) {
            $('.ets_rv_sort_by.question').addClass('active');
            $('.ets_rv_sort_by.review').removeClass('active');
            $('.ets_rv_review_photos_wrap').removeClass('active');
        } else {
            $('.ets_rv_sort_by.review').addClass('active');
            $('.ets_rv_sort_by.question').removeClass('active');
            $('.ets_rv_review_photos_wrap').addClass('active');
        }
    },
    initRate: function (prop) {
        if (prop !== 'question') {
            productCommentsETS.updateCriterion();
            $('#ets-rv-post-product-' + prop + '-modal .criterion-item.custom').remove();
            $('#ets-rv-post-product-' + prop + '-modal .criterion-item:not(.template) .ets-rv-grade-stars').etsRating();
        }
    },
    initModal: function (prop) {
        this.initRate(prop);
        commonPCETS.submit(prop);
    },
    initForm: function (prop) {
        $('#ets-rv-post-product-' + prop + '-modal').on('hidden.bs.ets-rv-modal', function () {
            $('.ets_rv_form.ets_rv_modal #ets-rv-post-product-' + prop + '-modal').remove();
            commonPCETS.clearPostForm($('#ets-rv-post-product-' + prop + '-form'));
        }).ETSModal('hide');

        commonPCETS.initModal(prop);
    },
    init: function (prop) {
        if (!ETS_RV_REVIEW_ENABLED && prop === 'comment' || !ETS_RV_QUESTION_ENABLED && prop === 'question')
            return;
        commonPCETS.initForm(prop);
        productCommentsETS.paginate({page: 1}, prop);
    },
    initialize: function () {
        if ($('body#product').length > 0) {
            if ($('.ets_speed_dynamic_hook .ets_rv_wrap').length < 1) {
                $.ajax({
                    url: $('.ets_rv_wrap').data('comment-url'),
                    type: 'POST',
                    data: 'ajax=1&action=renderTemplateModal&currentUrl=' + encodeURIComponent(window.location.href),
                    dataType: 'json',
                    success: function (json) {
                        if (json) {
                            if (json.infos) {
                                $('#ets_rv_group_tabs').attr({
                                    'data-profile-photo' : json.infos.profile_photo || '',
                                    'data-profile-name' : json.infos.customer_name || '',
                                    'data-my-account-link' : json.infos.my_account_link || '',
                                });
                            }
                            if (json.html)
                                $('.ets_rv_wrap').after(funcPCETS.decodeHTMLEntities(json.html));
                            ['comment', 'question'].forEach(commonPCETS.init);
                            commonPCETS.loginBack();
                        }
                    },
                    error: function () {
                    }
                });
            }
            commonPCETS.tabFilter();
        } else if ($('body#comment').length > 0 || $('body[id$=comment]').length > 0) {
            commonPCETS.initForm('comment');
        }
    },
    loginBack: function () {
        if (!/\?back\s*=\s*http(s|):\/\//i.test(window.location.href)) {
            var refreshURL = null;
            if (/(\?|&)ets_rv_add_review\s*=\s*1/i.test(window.location.href) && $('button.ets-rv-post-product-comment').length > 0) {
                refreshURL = 'review';
            } else if (/(\?|&)ets_rv_add_question\s*=\s*1/i.test(window.location.href) && $('button.ets-rv-post-product-question').length > 0) {
                refreshURL = 'question';
            }
            if (refreshURL !== null) {
                var prop = refreshURL;
                refreshURL = window.location.protocol + "//" + window.location.host + window.location.pathname + '#' + refreshURL;
                window.history.pushState({path: refreshURL}, '', refreshURL);
                if ($('a[data-tab="' + prop + '"]').length > 0)
                    $('a[data-tab="' + prop + '"]').trigger('click');
                commonPCETS.showPostModal((prop === 'review' ? 'comment' : 'question'), 'add');
            }
        }
    }
};

$(document).ready(function () {

    commonPCETS.initialize();

    $(document).on('click', 'a[data-tab="review"]', function () {
        $('.ets_rv_filter .ets_rv_tab, #ets-rv-product-comments-list, #ets-rv-product-questions-list').removeClass('active');
        $('.ets_rv_tab.ets_rv_tab_reviews, #ets-rv-product-comments-list').addClass('active');
    });

    $(document).on('click', '.ets-rv-write-review', function () {
        $('a[data-tab="review"]').trigger('click');
        $('.ets-rv-post-product-comment').trigger('click');
    });

    $(document).on('click', '.ets-rv-grade-stars.ets-rv-small-stars', function () {
        $('a[data-tab="review"]').trigger('click');
        $('html,body').animate({
            scrollTop: $('.tabs.ets-ept-list-tabs-more-info').offset().top - 50
        }, 'slow');
    });

    $(document).on('click', '.ets-rv-post-product-comment, .ets-rv-write-rewrite', function (event) {
        event.preventDefault();
        commonPCETS.showPostModal('comment', 'add');
    });

    $(document).on('click', '.ets-rv-post-product-question, .ets-rv-ask-question', function (event) {
        event.preventDefault();
        commonPCETS.showPostModal('question', 'add');
    });

    $(document).on('click', '.ets_rv_ul_dropdown .ets_rv_li_dropdown', function () {
        var ele = $(this);
        ele.parent('.ets_rv_ul_dropdown').find('.ets_rv_li_dropdown.active').removeClass('active');
        ele.addClass('active');
        ele.parents('.ets_rv_bulk_actions').removeClass('open').find('.ets_rv_selection').html($(this).html());

        //Filter
        if ($('#ets-rv-product-comments-list-header li.ets_rv_sort_by.active').length > 0) {
            commonPCETS.filter(ele, $('.ets_rv_sort_by.active .ets_rv_li_dropdown.active'), $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active').length > 0 ? $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active') : 0, $('.ets_rv_tab.ets_rv_tab_questions.active').length > 0 ? 'question' : 'comment');
        }
    });

    $(document).on('click', '.ets_rv_filter .ets_rv_tab', function () {
        var ele = $(this);
        var prop = ele.hasClass('ets_rv_tab_reviews') ? 'review' : 'question';
        var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '#' + prop;
        window.history.pushState({path: refresh}, '', refresh);

        commonPCETS.tabFilter(ele);
        if (!ele.hasClass('active') && $('.ets_rv_all_review:not(.active)').length > 0) {
            $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active').removeClass('active');
            $('.ets_rv_all_review').addClass('active');
            var refreshFilter = $('.ets_rv_all_review.active');
            commonPCETS.filter(refreshFilter, $('.ets_rv_sort_by.active .ets_rv_li_dropdown.active'), refreshFilter, $('.ets_rv_tab.ets_rv_tab_questions.active').length > 0 ? 'question' : 'comment');
        }

        return false;
    });

    $(document).on('click', '.ets_rv_btn_edit.product-comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            productComment = ele.parents('.ets-rv-product-comment-list-item').eq(0),
            commentList = productComment.parents('.ets_rv_tab_content').eq(0),
            prop = commentList.attr('id') && commentList.attr('id') === 'ets-rv-product-questions-list' ? 'question' : 'comment',
            form = $('#ets-rv-post-product-' + prop + '-form')
        ;
        if (!ele.hasClass('active') && commentList.data('comments-url')) {
            ele.addClass('active');
            $.ajax({
                url: commentList.data('comments-url'),
                type: 'post',
                data: '__ac=list_product_comment&first=1&object=1&id_product_comment=' + productComment.data('product-comment-id') + '&id_product=' + productComment.data('product-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        commonPCETS.showPostModal(prop, 'edit');
                        if (json.comments) {
                            // Guest
                            if (parseInt(json.comments.id_guest) > 0 && json.comments.customer_name) {
                                form.find('input[name=customer_name]').val(funcPCETS.decodeHTMLEntities(json.comments.customer_name));
                                form.find('input[name=email]').val(funcPCETS.decodeHTMLEntities(json.comments.email));
                            }
                            form.find('input[name=id_product_comment]').val(json.comments.id_ets_rv_product_comment);
                            form.find('input[name=comment_title]').val(json.comments.title);
                            form.find('textarea[name=comment_content]').val(json.comments.content);
                            if (prop !== 'question') {
                                if (form.find('#criterions_list').length > 0 && json.comments.criterion.length > 0) {
                                    form.find('.ets-pc-criterion').show();
                                    var criterion = json.comments.criterion;
                                    // Reset rateValue:
                                    $('.criterion-item:not(.template) .ets-rv-grade-stars[data-input^=criterion]', form).etsRating({grade: 0});
                                    // Load rateValue:
                                    criterion.forEach(function (item, index) {
                                        var rating = $('.ets-rv-grade-stars[data-input="criterion[' + item.id_ets_rv_product_comment_criterion + ']"]', form);
                                        if (rating.length > 0) {
                                            rating.etsRating({grade: parseInt(item.grade)});
                                        } else {
                                            var initRate = $('.criterion-item.template', form).clone();
                                            initRate.removeClass('template').addClass('custom');
                                            initRate.find('label').html(item.name);
                                            if (initRate.find('p.desc_error').length > 0)
                                                initRate.find('p.desc_error').html(initRate.find('p.desc_error').html().replace(/%s/g, item.name));
                                            initRate.find('.ets-rv-grade-stars').attr({
                                                'data-grade': item.grade,
                                                'data-input': 'criterion[' + item.id_ets_rv_product_comment_criterion + ']'
                                            });
                                            $('.criterion-item:eq(' + index + ')', form).before(initRate);
                                            $('.ets-rv-grade-stars[data-input="criterion[' + item.id_ets_rv_product_comment_criterion + ']"]', form).etsRating({grade: parseInt(item.grade)});
                                        }
                                    });
                                } else
                                    form.find('.ets-pc-criterion').hide();
                            }
                            if (json.comments.images) {
                                $('.ets_rv_upload_photos .ets_rv_upload_photo_item:not(:first)', form).remove();
                                var photos_wrap = $('.ets_rv_upload_photos', form),
                                    maximum_of_photo = parseInt(photos_wrap.data('photos')),
                                    count = photos_wrap.find('.ets_rv_upload_photo_item').length
                                ;
                                for (var i = 0; i < json.comments.images.length; i++) {
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
                                            .css('background-image', 'url("' + json.comments.images[i].url + '")')
                                            .children('.ets_rv_btn_delete_photo')
                                            .attr({
                                                'data-product-comment-image-id': json.comments.images[i].id_ets_rv_product_comment_image,
                                                'data-product-comment-id': json.comments.images[i].id_ets_rv_product_comment,
                                            });
                                    }
                                }
                            }
                            if (json.comments.videos) {
                                json.comments.videos.forEach(function (video, i) {
                                    var __this = $('.ets_rv_upload_video_item .ets_rv_upload_video_wrap').eq(i);
                                    __this.parents('.ets_rv_upload_video_item').addClass('cms_has_video');
                                    __this
                                        .addClass('selected')
                                        .children('.ets_rv_video')
                                        .html('<video controls><source src="' + video.url + '" type="' + video.type + '"/></video>')
                                        .children('.ets_rv_btn_delete_video')
                                        .attr({
                                            'data-product-comment-video-id': video.id_ets_rv_product_comment_video,
                                            'data-product-comment-id': video.id_ets_rv_product_comment,
                                        }).parents('.ets_rv_upload_video_item').addClass('cms_has_video');
                                    __this
                                        .children('.ets_rv_btn_delete_video')
                                        .attr({
                                            'data-product-comment-video-id': video.id_ets_rv_product_comment_video,
                                            'data-product-comment-id': video.id_ets_rv_product_comment,
                                        });
                                });
                            }
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
    });

    // fix1.6
    if (typeof baseDir !== "undefined" && baseDir) {
        $(document).on('click', '.ets-rv-modal[role=dialog], button[data-dismiss=modal]', function () {
            $(this).parents('.ets-rv-modal[role=dialog]').ETSModal('hide').removeClass('edit');
        });
    }
});
