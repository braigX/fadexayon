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
try {
    var ETS_RV_DATETIME_PICKER = typeof ets_rv_datetime_picker !== "undefined" ? JSON.parse(ets_rv_datetime_picker) : [];
    ETS_RV_DATETIME_PICKER.currentText = ets_rv_datetime_picker_currentText ?? 'Now';
    ETS_RV_DATETIME_PICKER.closeText = ets_rv_datetime_picker_closeText ?? 'Done';
    ETS_RV_DATETIME_PICKER.timeOnlyTitle = ets_rv_datetime_picker_timeOnlyTitle ?? 'Choose time';
    ETS_RV_DATETIME_PICKER.timeText = ets_rv_datetime_picker_timeText ?? 'Time';
    ETS_RV_DATETIME_PICKER.hourText = ets_rv_datetime_picker_hourText ?? 'Hour';
    ETS_RV_DATETIME_PICKER.minuteText = ets_rv_datetime_picker_minuteText ?? 'Minute';
} catch (error) {
    console.error(error);
}
var ETS_RV_DISPLAY_RATE_AND_QUESTION = typeof ETS_RV_DISPLAY_RATE_AND_QUESTION !== typeof undefined ? ETS_RV_DISPLAY_RATE_AND_QUESTION : 'button';
var ETS_RV_SCROLL_ITEM = null;
// reCAPTCHA google.
var reCaptchaETS = {
    recaptchaWidgets: [],
    onLoad: function (forms) {
        if (ETS_RV_CUSTOMER_IS_LOGGED && ETS_RV_RECAPTCHA_USER_REGISTERED)
            return;
        if (ETS_RV_RECAPTCHA_TYPE !== 'recaptcha_v2')
            reCaptchaETS.v3(forms);
        else
            reCaptchaETS.v2(forms);
    },
    reset: function (form) {
        if (ETS_RV_CUSTOMER_IS_LOGGED && ETS_RV_RECAPTCHA_USER_REGISTERED)
            return;
        if (ETS_RV_RECAPTCHA_TYPE !== 'recaptcha_v2') {
            form.removeClass('g-loaded');
            reCaptchaETS.v3(form);
        } else {
            reCaptchaETS.v2Reset(form.attr('id'));
        }
    },
    v2: function (forms) {
        if (!ETS_RV_RECAPTCHA_ENABLED || !ETS_RV_RECAPTCHA_SITE_KEY || typeof forms === "undefined" || !forms)
            return false;
        const pattern = /(^|\s)g-recaptcha(\s|$)/;
        for (let i = 0; i < forms.length; i++) {
            if (forms[i].classList.contains('g-loaded'))
                continue;
            var items = forms[i].getElementsByTagName('div');
            for (let k = 0; k < items.length; k++) {
                if (items[k].className && items[k].className.match(pattern)) {
                    this.recaptchaWidgets[forms[i].id] = grecaptcha.render(items[k], {
                        'sitekey': ETS_RV_RECAPTCHA_SITE_KEY,
                    });
                    forms[i].classList.add('g-loaded');
                    break;
                }
            }
        }
    },
    v3: function (forms) {
        if (!ETS_RV_RECAPTCHA_ENABLED || !ETS_RV_RECAPTCHA_SITE_KEY || typeof forms === "undefined" || !forms)
            return false;
        const pattern = /(^|\s)g-recaptcha-response(\s|$)/;
        let body = document.getElementsByTagName('body'),
            action = body[0].id ? body[0].id.replace(/(?=[^A-Za-z\_])([^A-Za-z\_])/g, '_') : 'submit'
        ;
        for (let i = 0; i < forms.length; i++) {
            if (forms[i].classList.contains('g-loaded'))
                continue;
            let items = forms[i].getElementsByTagName('input');
            for (let k = 0; k < items.length; k++) {
                if (items[k].className && items[k].className.match(pattern)) {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(ETS_RV_RECAPTCHA_SITE_KEY, {action: action}).then(function (token) {
                            if (token) {
                                items[k].value = token;
                                forms[i].classList.add('g-loaded');
                            }
                        });
                    });
                    break;
                }
            }
        }
    },
    v2Reset: function (id) {
        if (!ETS_RV_RECAPTCHA_ENABLED || !ETS_RV_RECAPTCHA_SITE_KEY || !id)
            return false;
        if (this.recaptchaWidgets && typeof grecaptcha !== typeof undefined) {
            ETS_RV_RECAPTCHA_VALID = 0;
            if (id) {
                grecaptcha.reset(this.recaptchaWidgets[id]);
                reCaptchaETS.removeClassError(id);
            } else {
                Object.keys(this.recaptchaWidgets).forEach(function (key) {
                    grecaptcha.reset(this.recaptchaWidgets[key]);
                    reCaptchaETS.removeClassError(key);
                });
            }
        }
    },
    removeClassError: function (id) {
        var form = document.getElementById(id);
        if (form.classList.contains('g-loaded')) {
            var ele = form.getElementsByClassName('g-recaptcha');
            if (ele[0].classList.contains('error')) {
                ele[0].classList.remove('error');
            }
        }
    },
};
// End reCAPTCHA google.
var funcPCETS = {
    n2br: function (str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />');
    },
    br2n: function (str) {
        return (str + '').replace(/<br\s*[\/]?>/gi, '\r\n');
    },
    colorHex: function (str) {
        var hash = 0;
        if (str && str.length === 0) return hash;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
            hash = hash & hash;
        }
        var color = '#';
        for (var ik = 0; ik < 3; ik++) {
            var value = (hash >> (ik * 8)) & 255;
            color += ('00' + value.toString(16)).substr(-2);
        }
        return color;
    },
    readURL: function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if ($(input).hasClass('video')) {
                    $(input).parents('.ets_rv_upload_video_item').addClass('cms_has_video').find('.ets_rv_video').html('<video controls><source src="' + e.target.result + '" type="video/mp4"></video>');
                } else {
                    $(input).parents('.ets_rv_upload_photo_item').addClass('cms_has_video').find('.ets_rv_upload_photo_wrap')
                        .css('background-image', 'url(' + e.target.result + ')')
                        .attr('title', input.files[0].name)
                        .addClass('selected')
                    ;
                }

            };
            reader.readAsDataURL(input.files[0]);
        }
    },
    decodeHTMLEntities: function (text) {
        var textArea = document.createElement('textarea');
        textArea.innerHTML = text;
        return textArea.value;
    },
    //display a success/error/notice message
    showSuccessMessage: function (msg) {
        if ($.growl)
            $.growl.notice({title: "", message: msg});
    },
    showErrorMessage: function (msg) {
        if ($.growl)
            $.growl.error({title: "", message: msg});
    },
    isSafari: function () {
        var e = navigator, c = e.userAgent, g = e.vendor;
        return (!(/(Chrome|CriOS)\s*\/\s*(\d[\d\.]*)/i).test(c) && ((/Apple/i).test(g) || !g) && (/Safari\s*\/\s*(\d[\d\.]*)/i).test(c));
    },
    hide: function (dom) {
        if (dom.hasClass('bo')) {
            dom.hide();
        } else {
            dom.remove();
        }
    },
    show: function (dom) {
        if (dom.hasClass('bo')) {
            dom.show();
        } else {
            dom.remove();
        }
    },
    isGuestLogin: function ($this, _prop) {
        var prop = _prop || 'comment',
            bindingEl = $this || $('body')
        ;
        if (!bindingEl.hasClass('loading')) {
            bindingEl.addClass('loading');
            $.ajax({
                url: $('#ets-rv-product-' + prop + 's-list').data('comments-url'),
                type: 'POST',
                data: {
                    __ac: 'is_guest_login'
                },
                dataType: 'json',
                success: function (json) {
                    bindingEl.removeClass('loading');
                    if (json) {
                        const decodedErrorMessage = $("<div/>").html(json.error).html();
                        productCommentsETS.showUpdateErrorModal(decodedErrorMessage);
                    }
                },
                error: function () {
                    bindingEl.removeClass('loading');
                }
            });
        }
    },
    stats: function (jsonData, prop) {
        if (jsonData.length < 1 || prop === '')
            return false;
        const $stats = $('#ets-rv-product-comments-list-header');
        var results = jsonData;

        $('.ets_rv_average_rating .ets_rv_average_grade', $stats).html(results.average_grade.toFixed(1));
        $('.ets_rv_average_rating .ets_rv_grade_stars', $stats).attr('data-grade', results.average_grade.toFixed(1));
        $('.ets_rv_average_rating .ets_rv_nb_comments, .ets_rv_li_dropdown.ets_rv_all_review, .ets_rv_tab_reviews .ets_rv_selection', $stats).html(function () {
            if ($(this).hasClass('ets_rv_selection')) {
                var total = $(this).attr('data-default').replace(/\d+/, parseInt(results.nb_reviews));
                $(this).attr('data-default', total);
                return total;
            } else if ($(this).hasClass('ets_rv_nb_comments')) {
                return (parseInt(results.nb_reviews) > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%s/g, parseInt(results.nb_reviews));
            } else
                return $(this).html().replace(/\d+/, parseInt(results.nb_reviews));
        });
        $('.ets_rv_tab_questions .ets_rv_question_selection', $stats).html(function () {
            return $(this).html().replace(/\d+/, parseInt(results.nb_questions))
        });
        if (prop !== 'question') {
            var ets_rv_image_review = $('.ets_rv_li_dropdown.ets_rv_image_review');
            if (ets_rv_image_review.length > 0) {
                if (results.nb_reviewHasImageVideo) {
                    ets_rv_image_review.html(ets_rv_image_review.html().replace(/\d+/, parseInt(results.nb_reviewHasImageVideo)));
                    ets_rv_image_review.removeClass('empty');
                } else
                    ets_rv_image_review.addClass('empty');
            }
            if (results.grade_stats) {
                $.each(results.grade_stats, function (i, stat) {
                    $('.ets_rv_grade_stars_' + stat.id + ' .ets_rv_grade_stars_percent', $stats).css('width', stat.grade_percent.toFixed(1) + '%');
                    $('.ets_rv_grade_stars_' + stat.id + ' .ets_rv_grade_stars_total', $stats).html(stat.grade_total);
                    $('.ets_rv_' + stat.id + '', $stats).html(function () {
                        if (stat.grade_total > 0) {
                            $(this).removeClass('empty');
                        } else {
                            $(this).addClass('empty');
                        }
                        return $(this).html().replace(/\d+/, stat.grade_total);
                    });
                });
            }
            $('.ets_rv_header_wrap:hidden').show();
        }
    }
};

var productCommentsETS = {
    init: function () {
    },
    initSlider: function (photos) {
        if ($('.ets_rv_review_photos_wrap').length <= 0) {
            $('.ets_rv_statistics').after(funcPCETS.decodeHTMLEntities(photos));
        } else {
            $('.ets_rv_review_photos_wrap').html(funcPCETS.decodeHTMLEntities(photos));
        }
        $('.ets_rv_review_photos.slick-initialized').slick('unslick');
        $('.ets_rv_review_photos_wrap .ets_rv_review_photos:not(.slick-initialized)').slick({
            arrows: true,
            autoplay: true,
            slidesToShow: 6,
            slidesToScroll: 1,
            speed: 300,
            adaptiveHeight: false,
            responsive: [
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 6,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                }
            ]
        });
        $('.ets_rv_review_photos_wrap').addClass('active');
    },
    showErrorModal: function (errorMessage) {
        $('#ets-rv-product-comment-post-error-message').html(errorMessage);
        $('#ets-rv-product-comment-post-error').ETSModal('show');
        $('.post-question-buttons button').removeClass('active');
        if (typeof id_language !== "undefined") {
            showErrorMessage(errorMessage);
        }
    },
    rawActions: function ($productComment, data, prop) {
        // like.
        $('.useful-' + data.type + ':not(.disable)', $productComment).click(function () {
            data.options.usefulness = 1;
            data.options.__ac = 'useful_' + data.type.replace('-', '_');
            productCommentsETS.updateUsefulness($productComment, data, prop);
        });

        if (data.usefulness > 0 && data.current) {
            $('.useful-' + data.type, $productComment).addClass('current');
        }

        // dislike.
        $('.ets-rv-not-useful-' + data.type + ':not(.disable)', $productComment).click(function () {
            data.options.usefulness = 0;
            data.options.__ac = 'useful_' + data.type.replace('-', '_');
            productCommentsETS.updateUsefulness($productComment, data, prop);
        });

        if (data.usefulness < 1 && data.current) {
            $('.ets-rv-not-useful-' + data.type, $productComment).addClass('current');
        }

        if (prop !== 'comment') {
            $('.useful-answer.bo', $productComment).click(function () {
                data.options.usefulness = $(this).hasClass('checked') ? 0 : 1;
                data.options.__ac = 'useful_answer';
                productCommentsETS.usefulnessAnswer($productComment, data, prop);
            });
        }
    },
    add: function (comment, isPosted) {
        const qa = comment.question && parseInt(comment.question) > 0 ? 1 : 0;
        const prop = qa ? 'question' : 'comment';
        const commentsList = $('#ets-rv-product-' + prop + 's-list');
        var commentTemplate = productCommentsETS.htmlTemplate($('#ets-rv-product-' + prop + '-item-prototype').html(), comment),
            initialComments = commentsList.data('comments-initial') ? parseInt(commentsList.data('comments-initial')) : 1,
            commentsNbForward = typeof comment.comments_nb_forward !== "undefined" ? parseInt(comment.comments_nb_forward) : 0,
            commentsNb = parseInt(comment.comments_nb),
            nbComments = commentsNb + commentsNbForward,
            answersNbForward = typeof comment.answers_nb_forward !== "undefined" ? parseInt(comment.answers_nb_forward) : 0,
            answersNb = typeof comment.answers_nb !== "undefined" ? parseInt(comment.answers_nb) : 0,
            nbAnswers = answersNb + answersNbForward
        ;
        commentTemplate = commentTemplate.replace(/@COMMENT_ID@/g, comment.id_ets_rv_comment);
        if (typeof comment.title === "object") {
            var languages = Object.keys(comment.title);
            languages.forEach(function (id_lang) {
                var regEx = new RegExp('@COMMENT_TITLE_' + id_lang + '@', 'g');
                commentTemplate = commentTemplate.replace(regEx, comment.title[id_lang] ? comment.title[id_lang] : '');
            });
        } else
            commentTemplate = commentTemplate.replace(/@COMMENT_TITLE@/g, comment.title ? comment.title : '');
        commentTemplate = commentTemplate.replace(/@COMMENT_GRADE@/g, parseFloat(comment.grade).toFixed(1));
        commentTemplate = commentTemplate.replace(/@COMMENT_IMAGES@/g, comment.images);
        commentTemplate = commentTemplate.replace(/@COMMENT_VIDEOS@/g, comment.videos);
        commentTemplate = commentTemplate.replace(/@COMMENTS_PER_PAGE@/g, commentsList.data('comments-per-page') ? parseInt(commentsList.data('comments-per-page')) : 5);
        commentTemplate = commentTemplate.replace(/@COMMENTS_NB@/g, nbComments);
        // Comment's.
        if (comment.comments) {
            if (commentsNbForward > 0) {
                var initialCommentsReal = initialComments + commentsNb;
                commentTemplate = commentTemplate.replace(/@COMMENTS_BEGIN@/g, initialCommentsReal);
                commentTemplate = commentTemplate.replace(/@COMMENTS_LOADMORE@/g, nbComments > initialCommentsReal ? nbComments - initialCommentsReal : 0);
                commentTemplate = commentTemplate.replace(/@COMMENTS_BEGIN_FORWARD@/g, commentsNb > initialComments ? commentsNb - initialComments : 0);
                commentTemplate = commentTemplate.replace(/@COMMENTS_LOADMORE_FORWARD@/g, commentsNb);
                commentTemplate = commentTemplate.replace(/@COMMENTS_PER_PAGE_FORWARD@/g, commentsNb > commentsList.data('comments-per-page') ? parseInt(commentsList.data('comments-per-page')) : commentsNb);
            } else {
                commentTemplate = commentTemplate.replace(/@COMMENTS_BEGIN@/g, initialComments);
                commentTemplate = commentTemplate.replace(/@COMMENTS_LOADMORE@/g, commentsNb > initialComments ? commentsNb - initialComments : 0);
            }
        }

        // Answer's
        if (prop !== 'comment')
            commentTemplate = commentTemplate.replace(/@ANSWERS_NB@/g, nbAnswers);
        if (comment.answers) {
            if (answersNbForward > 0) {
                var initialAnswersReal = initialComments + answersNb;
                commentTemplate = commentTemplate.replace(/@ANSWERS_BEGIN@/g, initialAnswersReal);
                commentTemplate = commentTemplate.replace(/@ANSWERS_LOADMORE@/g, nbAnswers > initialAnswersReal ? nbAnswers - initialAnswersReal : 0);
                commentTemplate = commentTemplate.replace(/@ANSWERS_BEGIN_FORWARD@/g, answersNb > initialComments ? answersNb - initialComments : 0);
                commentTemplate = commentTemplate.replace(/@ANSWERS_LOADMORE_FORWARD@/g, answersNb);
                commentTemplate = commentTemplate.replace(/@ANSWERS_PER_PAGE_FORWARD@/g, answersNb > commentsList.data('comments-per-page') ? parseInt(commentsList.data('comments-per-page')) : answersNb);
            } else {
                commentTemplate = commentTemplate.replace(/@ANSWERS_BEGIN@/g, initialComments);
                commentTemplate = commentTemplate.replace(/@ANSWERS_LOADMORE@/g, answersNb > initialComments ? answersNb - initialComments : 0);
            }
        }

        const $productComment = $(commentTemplate);
        const $action = $('.comment_dropdown_action', $productComment);
        const $comment_title = $('.ets-rv-product-comment-title-html', $productComment);

        // Load more comments.
        if (comment.comments && (commentsNbForward > 0 && nbComments > initialCommentsReal || commentsNbForward <= 0 && commentsNb > initialComments)) {
            var btn = $('.ets_rv_comment_load_more:not(.answer):not(.forward-comments)', $productComment),
                rest = parseInt(btn.attr('data-rest')),
                per_page = parseInt(btn.attr('data-comments-per-page'))
            ;
            btn.show();
            if (rest > per_page) {
                btn.html(function () {
                    return (rest > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, per_page).replace(/%2s/g, rest);
                });
            } else {
                btn.html(function () {
                    return (rest > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, rest);
                });
            }
        }
        // Load more comments forward.
        if (comment.comments && commentsNbForward > 0) {
            var btn_forward = $('.ets_rv_comment_load_more.forward-comments.bo:not(.answer)', $productComment),
                rest_forward = parseInt(btn_forward.attr('data-rest')),
                per_page_forward = parseInt(btn_forward.attr('data-comments-per-page'))
            ;
            if (rest_forward <= 0 || commentsNb <= 0) {
                btn_forward.hide();
            } else {
                btn_forward.show();
                if (rest_forward > per_page_forward) {
                    btn_forward.html(function () {
                        return (rest_forward > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, per_page_forward).replace(/%2s/g, rest_forward);
                    });
                } else {
                    btn_forward.html(function () {
                        return (rest_forward > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, rest_forward);
                    });
                }
            }
        }

        if (!comment.title)
            $comment_title.hide();
        else
            $comment_title.show();

        if (!comment.edit_profile)
            $('.product-comment-author-profile-link', $productComment).remove();

        if (!qa) {
            if (parseInt(comment.grade) > 0) {
                $('.ets-rv-grade-stars', $productComment).etsRating({
                    grade: comment.grade
                });
            } else {
                $('.ets-rv-grade-stars, .ets_rv_grade_stars', $productComment).hide();
            }
            if (comment.criterion.length > 1) {
                var popover = new String(), rateInvalidCount = 0;
                comment.criterion.forEach(function (item) {
                    if (parseInt(item.grade) > 0) {
                        popover += '<li class="criterion-item"><span class="criterion-name">' + item.name + ':</span><span class="ets_rv_grade_stars' + (ETS_RV_DESIGN_COLOR1 ? ' color1' : '') + '" data-grade="' + item.grade + '" data-rate-empty="☆☆☆☆☆" data-rate-full="★★★★★"></span> <span class="criterion-grade">(' + item.grade + ')</span></li>';
                        rateInvalidCount++;
                    }
                });
                if (popover && rateInvalidCount > 1)
                    $('.ets_rv_grade_stars', $productComment).append('<ul class="popover_wrap">' + popover + '</ul>');
            }
        } else
            $('.ets_rv_grade_stars', $productComment).remove();

        if (comment.answers) {
            $('.ets_rv_answers_nb .ets_rv_answers_text, .comment_actions_right .ets_rv_answers_text', $productComment).html(function () {
                return (parseInt(comment.answers_nb) > 1 ? $(this).data('multi-text') : $(this).data('text'));
            });
        } else {
            $('.comment_actions_right .nb-comment-text', $productComment).html(function () {
                return (parseInt(comment.comments_nb) > 1 ? $(this).data('multi-text') : $(this).data('text'));
            });
        }
        if (!comment.action_allowed) {
            $('.comment_dropdown_action:not(.bo), .comment_clock:not(.bo), .review-date-upd:not(.bo)', $productComment).remove();
        } else {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_private.product-comment', $productComment).remove();
        }
        if (!comment.edit) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_edit.product-comment', $productComment).remove();
        }
        if (!comment.delete) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_delete.product-comment', $productComment).remove();
        }
        if ($('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.product-comment', $productComment).length <= 0) {
            $('.comment_dropdown_action:not(.bo)', $productComment).remove();
        }
        const date_upd = $('.review-date-upd', $productComment);
        if (date_upd.length > 0 && date_upd.attr('data-upd-date')) {
            date_upd.show().addClass('show_il');
        }
        if (!comment.comment_allowed) {
            if (prop !== 'comment') {
                $('.ets_rv_btn_add_answer,.ets_rv_btn_add_comment', $productComment).remove();
            } else {
                $('.write-a-comment', $productComment).remove();
                $('.ets_rv_form_comment', $productComment).empty();
            }
        }
        productCommentsETS.rawActions($productComment, {
            type: 'product-comment',
            options: {
                id_ets_rv_product_comment: comment.id_ets_rv_product_comment,
                __ac: 'useful_product_comment'
            },
            bo: ':not(.bo)',
            current: comment.current,
            usefulness: comment.usefulness,
        }, prop);

        $('.write-a-comment, .ets_rv_btn_add_comment' + (prop !== 'comment' ? ', .ets_rv_btn_add_answer' : ''), $productComment).click(function () {
            const _wrap = $((prop !== 'comment' && !$(this).hasClass('ets_rv_btn_add_comment') ? '.ets_rv_answer_list' : '.ets_rv_comment_list') + '[data-product-comment-id=' + comment.id_ets_rv_product_comment + '] .ets_rv_form_comment');
            var $this = $(this);
            if ($this.hasClass('guest')) {
                funcPCETS.isGuestLogin($this, prop);

                return false;
            }
            _wrap
                .addClass('active')
                .find('textarea.ets_rv_comment')
                .focus();

            var btnCancel = _wrap.find('.ets_rv_cancel_comment');
            if (!btnCancel.hasClass('show_answer_box') && !btnCancel.hasClass('show_comment_box'))
                btnCancel.show();
        });

        if (isPosted) {
            commentsList.prepend($productComment);
        } else {
            commentsList.find('#product-comments-list-footer').before($productComment);
        }

        // Comment's.
        if (comment.comments && comment.comments.length > 0)
            comment.comments.forEach(function (ele) {
                commentsETS.add(ele, false);
            });

        // Load more answer.
        if (comment.answers && (answersNbForward > 0 && nbAnswers > initialAnswersReal || answersNbForward <= 0 && answersNb > initialComments)) {
            var answer_btn = $('.ets_rv_comment_load_more.answer:not(.forward-answers)', $productComment),
                answer_rest = parseInt(answer_btn.attr('data-rest')),
                answer_per_page = parseInt(answer_btn.attr('data-comments-per-page'))
            ;
            answer_btn.show();
            if (answer_rest > answer_per_page) {
                answer_btn.html(function () {
                    return (answer_rest > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, answer_per_page).replace(/%2s/g, answer_rest);
                });
            } else {
                answer_btn.html(function () {
                    return (answer_rest > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, answer_rest);
                });
            }
        }
        // Load more answer forward.
        if (comment.answers && answersNbForward > 0) {
            var answer_btn_forward = $('.ets_rv_comment_load_more.answer.forward-answers.bo', $productComment),
                answer_rest_forward = parseInt(answer_btn_forward.attr('data-rest')),
                answer_per_page_forward = parseInt(answer_btn_forward.attr('data-comments-per-page'))
            ;
            if (answer_rest_forward <= 0 || answersNb <= 0) {
                answer_btn_forward.hide();
            } else {
                answer_btn_forward.show();
                if (answer_rest_forward > answer_per_page_forward) {
                    answer_btn_forward.html(function () {
                        return (answer_rest_forward > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, answer_per_page_forward).replace(/%2s/g, answer_rest_forward);
                    });
                } else {
                    answer_btn_forward.html(function () {
                        return (answer_rest_forward > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, answer_rest_forward);
                    });
                }
            }
        }

        // Answer's.
        if (comment.answers && comment.answers.length > 0)
            comment.answers.forEach(function (ele) {
                commentsETS.add(ele, false);
            });

        if (!qa) {
        }

        if (($('body#product').length <= 0 || ETS_RV_ADDED_PRODUCT_COMMENT) && !ETS_RV_SCROLL_ITEM && (comment.answers && answersNbForward <= 0 || !comment.answers && commentsNbForward <= 0)) {
            var footerList = $productComment.find('.ets_rv_answer_list .ets_rv_comment_footer').length > 0 ? $productComment.find('.ets_rv_answer_list .ets_rv_comment_footer') : $productComment.find('.ets_rv_comment_list .ets_rv_comment_footer'),
                footerForm = footerList.find('.ets_rv_form_comment')
            ;
            footerForm.addClass('active');
            setTimeout(function () {
                $('.ets_rv_overload.active').animate({scrollTop: footerForm.position().top}, 750);
                if (typeof id_language !== "undefined" && id_language > 0) {
                    footerForm.find('form textarea[name=comment_content_' + id_language + ']').focus();
                } else {
                    footerForm.find('form textarea[name^=comment_content]').focus();
                }
            }, 750);
        }

        reCaptchaETS.onLoad($('form.form-control-comment:not(.g-loaded)', $productComment));
    },
    updateUsefulness: function ($comment, data, prop) {
        var usefulness = $('.useful-' + data.type + '-value', $comment),
            not_usefulness = $('.ets-rv-not-useful-' + data.type + '-value', $comment),
            total_usefulness = $('.total-usefulness-' + data.type, $comment)
        ;
        var hasError = false, msgError = null;

        var countUseful = parseInt(usefulness.attr('data-count')),
            parentUseful = usefulness.parents('.useful-' + data.type),
            currentUseful = parentUseful.hasClass('current'),
            countNotUseful = parseInt(not_usefulness.attr('data-count')),
            parentNotUseful = not_usefulness.parents('.ets-rv-not-useful-' + data.type),
            currentNotUseful = parentNotUseful.hasClass('current')
        ;

        if (data.options.usefulness > 0) {
            if (currentUseful) {
                countUseful--
                parentUseful.removeClass('current');
            } else {
                countUseful++
                if (currentNotUseful) {
                    parentNotUseful.removeClass('current');
                }
                parentUseful.addClass('current');
            }
        }
        if (data.options.usefulness < 1) {
            if (currentNotUseful) {
                countNotUseful--;
                parentNotUseful.removeClass('current');
            } else {
                countNotUseful++
                if (currentUseful) {
                    parentUseful.removeClass('current');
                }
                parentNotUseful.addClass('current');
            }
        }

        $.post($('#ets-rv-product-' + prop + 's-list').data('comments-url'), data.options, function (jsonResponse) {
            var jsonData = false;
            try {
                jsonData = JSON.parse(jsonResponse);
            } catch (e) {
                hasError = true;
            }
            if (jsonData) {
                if (jsonData.success) {
                    usefulness.html(jsonData.usefulness).attr('data-count', jsonData.usefulness);

                    const notUseful = jsonData.total_usefulness - jsonData.usefulness;
                    not_usefulness.html(notUseful).attr('data-count', notUseful);

                    const totalUseful = jsonData.usefulness - (jsonData.total_usefulness - jsonData.usefulness);
                    total_usefulness.html(totalUseful).attr('data-count', totalUseful);
                    // admin:
                    if (typeof ets_rv !== "undefined") ets_rv.refreshList(jsonData);
                } else {
                    msgError = $("<div/>").html(jsonData.error).html();
                    hasError = true;
                }
            } else
                hasError = true;

            if (hasError) {
                productCommentsETS.showUpdateErrorModal(msgError !== null ? msgError : productCommentUpdatePostErrorMessage);
                if (data.options.usefulness > 0) {
                    if (currentUseful) {
                        countUseful++;
                        parentUseful.addClass('current');
                    } else {
                        countUseful--
                        if (currentNotUseful) {
                            parentNotUseful.addClass('current');
                        }
                        parentUseful.removeClass('current');
                    }
                }
                if (data.options.usefulness < 1) {
                    if (currentNotUseful) {
                        countNotUseful++;
                        parentNotUseful.addClass('current');
                    } else {
                        countNotUseful--
                        if (currentUseful) {
                            parentUseful.addClass('current');
                        }
                        parentNotUseful.removeClass('current');
                    }
                }
            }

        }).fail(function () {
            hasError = true;
        });

        if (hasError) {
            productCommentsETS.showUpdateErrorModal(msgError !== null ? msgError : productCommentUpdatePostErrorMessage);
        }
    },
    usefulnessAnswer: function ($comment, data, prop) {
        var useful_answer = $('.useful-answer', $comment),
            answerList = useful_answer.parents('.ets_rv_answer_list')
        ;
        answerList
            .find('.useful-answer.checked')
            .addClass('old-checked')
            .removeClass('checked')
        ;
        if (data.options.usefulness) {
            useful_answer.addClass('checked');
        } else {
            useful_answer.removeClass('checked');
        }
        $.post($('#ets-rv-product-' + prop + 's-list').data('comments-url'), data.options, function (jsonResponse) {
            var jsonData = false;
            try {
                jsonData = JSON.parse(jsonResponse);
            } catch (e) {
            }
            if (jsonData) {
                if (jsonData.success) {
                    answerList
                        .find('.useful-answer.old-checked')
                        .removeClass('.old-checked')
                    // backoffice:
                    if (typeof ets_rv !== "undefined") {
                        ets_rv.refreshList(jsonData);
                    }
                } else {
                    if (data.options.usefulness) {
                        useful_answer.removeClass('checked');
                    } else {
                        useful_answer.addClass('checked');
                    }
                    answerList
                        .find('.useful-answer.old-checked')
                        .removeClass('.old-checked')
                        .addClass('checked');
                    const decodedErrorMessage = $("<div/>").html(jsonData.error).html();
                    productCommentsETS.showUpdateErrorModal(decodedErrorMessage);
                }
            } else {
                if (data.options.usefulness) {
                    useful_answer.removeClass('checked');
                } else {
                    useful_answer.addClass('checked');
                }
                answerList
                    .find('.useful-answer.old-checked')
                    .removeClass('.old-checked')
                    .addClass('checked');
                productCommentsETS.showUpdateErrorModal(productCommentUpdatePostErrorMessage);
            }
        }).fail(function () {
            if (data.options.usefulness) {
                useful_answer.removeClass('checked');
            } else {
                useful_answer.addClass('checked');
            }
            answerList
                .find('.useful-answer.old-checked')
                .removeClass('.old-checked')
                .addClass('checked');
            productCommentsETS.showUpdateErrorModal(productCommentUpdatePostErrorMessage);
        });
    },
    populate: function (comments, prop) {
        var prop = prop || 'comment';
        $('#ets-rv-product-' + prop + 's-list').find('.ets-rv-product-comment-list-item').remove();
        comments.forEach(function (ele) {
            productCommentsETS.add(ele, false);
        });
    },
    paginate: function (config, $prop, $bindingEL) {
        var prop = $prop || 'comment',
            commentsList = $('#ets-rv-product-' + prop + 's-list'),
            urlPost = commentsList.data('comments-url'),
            bindingEL = $bindingEL || false
        ;
        if (!config) {
            config = {};
        }
        var default_config = {
            __ac: 'list_product_comment',
            qa: prop !== 'question' ? 0 : 1,
        };
        $.each(default_config, function (index, el) {
            if (config[index] === undefined)
                config[index] = el;
        });

        if ($('.form-group-review-wrap').length > 0) {
            $('.form-group-review-wrap:not(.loading)').addClass('loading');
        }
        if (urlPost) {
            $.ajax({
                type: 'GET',
                url: urlPost,
                data: config,
                dataType: 'json',
                success: function (jsonResponse) {
                    if (jsonResponse) {
                        if ($('.form-group-review-wrap').length > 0)
                            $('.form-group-review-wrap.loading').removeClass('loading');
                        if (jsonResponse.comments && jsonResponse.comments.length > 0) {
                            productCommentsETS.populate(jsonResponse.comments, prop);
                            if (config.back_office) {
                                ets_rv_op.multiOptions();
                                $('.ets_rv_overload:not(.active)').addClass('active');
                            }
                        } else
                            commentsList.find('.ets-rv-product-comment-list-item').remove();
                        var btn_load_more = $('.ets_rv_product_comment_load_more', commentsList);
                        if (jsonResponse.id)
                            btn_load_more.remove();
                        else if (jsonResponse.reviews_nb > jsonResponse.reviews_initial) {
                            btn_load_more.show();
                        }
                        if (bindingEL && typeof ets_rv !== "undefined") {
                            ets_rv.processFinish(bindingEL);
                            bindingEL.parents('.btn-group-action').find('.btn-group > a.btn').removeClass('active')
                        }
                        if (jsonResponse.photos) {
                            productCommentsETS.initSlider(jsonResponse.photos);
                        }
                    }
                },
                error: function () {
                    if (bindingEL && typeof ets_rv !== "undefined") {
                        ets_rv.processFinish(bindingEL);
                        bindingEL.parents('.btn-group-action').find('.btn-group > a.btn').removeClass('active')
                    }
                }
            });
        }
    },
    htmlTemplate: function (prototype, comment) {
        var commentTemplate = prototype,
            customerName = comment.customer_name,
            groupTabs = $('#ets_rv_group_tabs')
        ;
        if (!customerName)
            customerName = comment.firstname + ' ' + comment.lastname;
        commentTemplate = commentTemplate.replace(/@PRODUCT_COMMENT_ID@/g, comment.id_ets_rv_product_comment);
        commentTemplate = commentTemplate.replace(/@PRODUCT_ID@/g, comment.id_product);
        commentTemplate = commentTemplate.replace(/@CUSTOMER_NAME@/g, customerName);
        commentTemplate = commentTemplate.replace(/@COMMENT_DATE@/g, '<span title="' + comment.date_add + '">' + comment.time_elapsed + '</span>');
        commentTemplate = commentTemplate.replace(/@COMMENT_UPD_DATE@/g, comment.upd_date);
        if (typeof comment.content === "object") {
            var languages = Object.keys(comment.content);
            languages.forEach(function (id_lang) {
                var regEx = new RegExp('@COMMENT_COMMENT_' + id_lang + '@', 'g');
                commentTemplate = commentTemplate.replace(regEx, funcPCETS.n2br(comment.content[id_lang]));
            });
        } else
            commentTemplate = commentTemplate.replace(/@COMMENT_COMMENT@/g, funcPCETS.n2br(comment.content));
        commentTemplate = commentTemplate.replace(/@COMMENT_USEFUL_ADVICES@/g, comment.usefulness);
        commentTemplate = commentTemplate.replace(/@COMMENT_NOT_USEFUL_ADVICES@/g, (comment.total_usefulness - comment.usefulness));
        commentTemplate = commentTemplate.replace(/@COMMENT_TOTAL_ADVICES@/g, comment.total_usefulness);
        commentTemplate = commentTemplate.replace(/@VERIFY_PURCHASE@/g, comment.verify_purchase);
        commentTemplate = commentTemplate.replace(/@COMMENT_ISO_COUNTRY@/g, comment.iso_code ? '<img src="' + comment.iso_code + '" title="' + comment.country_name + '" alt="" />' : '');
        commentTemplate = commentTemplate.replace(/@COMMENT_AVATAR@/g, comment.avatar ? '<span class="ets_rv_avatar_photo" style="background-image:url(' + comment.avatar + ')"></span>' : (customerName ? '<span class="ets_rv_avatar_caption" style="background-color: ' + funcPCETS.colorHex(customerName) + '">' + customerName.substr(0, 1).toUpperCase() + '</span>' : ''));
        commentTemplate = commentTemplate.replace(/@CURRENT_AVATAR@/g, groupTabs.data('profile-photo') ? '<span class="ets_rv_avatar_photo" style="background-image:url(' + groupTabs.data('profile-photo') + ')"></span>' : (groupTabs.data('profile-name') ? '<span class="ets_rv_avatar_caption" style="background-color: ' + funcPCETS.colorHex(groupTabs.data('profile-name')) + '">' + (groupTabs.data('profile-name')).substr(0, 1).toUpperCase() + '</span>' : ''));
        commentTemplate = commentTemplate.replace(/@CURRENT_NAME@/g, groupTabs.data('profile-name') ? groupTabs.data('profile-name') : '');

        commentTemplate = commentTemplate.replace(/@FORM_ID@/g, Math.floor((Math.random() * Math.pow(10, 8)) + 1));

        commentTemplate = commentTemplate.replace(/@AUTHOR_PROFILE@/g, comment.author_profile ? comment.author_profile : '');
        commentTemplate = commentTemplate.replace(/data-href\s*=\s*\"@MY_ACCOUNT@\"/g, 'href="' + (comment.my_account ? comment.my_account : '') + '"');
        commentTemplate = commentTemplate.replace(/data-href\s*=\s*\"@CURRENT_MY_ACCOUNT@\"/g, 'href="' + (groupTabs.data('my-account-link') ? groupTabs.data('my-account-link') : 'javascript::void(0);') + '"');
        commentTemplate = commentTemplate.replace(/data-href\s*=\s*\"@ACTIVITY_LINK@\"/g, 'href="' + (comment.activity_link ? comment.activity_link : 'javascript::void(0);') + '"');
        commentTemplate = commentTemplate.replace(/@NO_LINK@/g, comment.activity_link ? '' : ' no-link');
        commentTemplate = commentTemplate.replace(/@DATE_ADD@/g, comment.date_add);
        commentTemplate = commentTemplate.replace(/@VALIDATE@/g, comment.validate);
        if (parseInt(comment.validate) === 3) {
            commentTemplate = commentTemplate.replace(/@COMMENT_NO_APPROVE@/g, '<span class="rejected">' + comment.comment_refuse + '</span>');
        } else
            commentTemplate = commentTemplate.replace(/@COMMENT_NO_APPROVE@/g, comment.comment_no_approve);
        commentTemplate = commentTemplate.replace(/@CURRENT_STATE@/g, parseInt(comment.validate) === 1 ? 'approved' : (parseInt(comment.validate) === 0 ? 'pending' : 'private'));

        return commentTemplate;
    },
    showUpdateErrorModal: function (errorMessage) {
        $('#ets-rv-update-comment-usefulness-post-error-message').html(errorMessage);
        $('#ets-rv-update-comment-usefulness-post-error').ETSModal('show');
    },
    updateCriterion: function (action, numberOfRating) {
        const criterionList = $('#criterions_list');
        var submit = action || '';

        if (parseInt(criterionList.attr('data-maximum-rating')) !== 0) {
            var maximum_rating = parseInt(criterionList.attr('data-maximum-rating'));
            switch (submit) {
                case "add":
                case 'delete':
                    criterionList.attr('data-number-of-rating', numberOfRating);
                    break;
                case "edit":
                    criterionList.parents('.ets-pc-criterion:not(.active)').addClass('active');
                    break;
            }
            if (maximum_rating <= parseInt(criterionList.attr('data-number-of-rating'))) {
                criterionList.parents('.ets-pc-criterion').removeClass('active').removeAttr('style');
            } else {
                criterionList.parents('.ets-pc-criterion').addClass('active').removeAttr('style');
            }
        }
    },
};

var repliesETS = {
    post: function (form, button) {
        var id_lang_default = form.data('lang-default'),
            ele = form.find('textarea[name=comment_content]').eq(0).length > 0 ? form.find('textarea[name=comment_content]').eq(0) : form.find('textarea[name=comment_content_' + id_lang_default + ']').eq(0)
        ;
        if (ele.val() && !button.hasClass('active')) {
            button.addClass('active');
            var replyCommentList = form.parents('.ets_rv_reply_comment_list').eq(0),
                replyComment = replyCommentList.find('.reply-comment-list-item.active').eq(0),
                onUpdate = button.hasClass('update-reply-comment')
            ;
            var formData = new FormData(form.get(0));

            formData.append('id_product', replyCommentList.data('product-id'));
            formData.append('id_comment', replyCommentList.data('comment-id'));
            if (onUpdate)
                formData.append('id_reply_comment', replyComment.data('reply-comment-id'));

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    button.removeClass('active');
                    reCaptchaETS.reset(form);
                    if (json) {
                        if (!json.success) {
                            productCommentsETS.showErrorModal(json.error);
                        } else if (json.comment) {
                            if (onUpdate) {
                                if (typeof json.comment.content === "object") {
                                    var languages = Object.keys(json.comment.content);
                                    languages.forEach(function (id_lang) {
                                        replyComment.find('p.reply-comment-content-html .translatable-field.lang-' + id_lang).html(funcPCETS.n2br(json.comment.content[id_lang]));
                                    });
                                } else
                                    replyComment.find('p.reply-comment-content-html').html(funcPCETS.n2br(json.comment.content));
                                if (json.comment.upd_date)
                                    replyComment.find('.reply-comment-date-upd').show().find('.reply-comment-upd-date').html(json.comment.upd_date);
                                if (json.comment.time_elapsed)
                                    replyComment.find('.reply-comment-date .reply-comment-date-add span').attr('title', json.comment.date_add).html(json.comment.time_elapsed);
                            } else {
                                repliesETS.add(json.comment, true);
                                replyCommentList.prev().find('.nb-reply-comment-value').text(function () {
                                    var data_count = parseInt($(this).text()) + 1;
                                    $(this).parent('.nb-reply-comment').attr('data-count', data_count);
                                    return parseInt($(this).text()) + 1;

                                });
                            }
                            form.find('textarea[name^=comment_content]').val('');
                            if (replyCommentList.find('button.ets_rv_post_reply_comment.update-reply-comment').length > 0) {
                                replyCommentList.find('button.ets_rv_cancel_reply_comment:visible').trigger('click');
                            }
                            // admin:
                            if (typeof ets_rv !== "undefined")
                                ets_rv.refreshList(json);
                        }
                    }
                },
                error: function () {
                    button.removeClass('active');
                    reCaptchaETS.reset(form);
                }
            });
        } else
            ele.focus();
    },
    add: function (replyComment, isPosted) {
        var qa = parseInt(replyComment.question) > 0 ? 1 : 0,
            option = qa > 0 ? 'answer' : 'reply',
            prop = qa ? 'question' : 'comment'
        ;
        var commentTemplate = productCommentsETS.htmlTemplate($('#' + option + '-comment-item-prototype').html(), replyComment);

        commentTemplate = commentTemplate.replace(/@COMMENT_ID@/g, replyComment.id_ets_rv_comment);
        commentTemplate = commentTemplate.replace(/@REPLY_COMMENT_ID@/g, replyComment.id_ets_rv_reply_comment);
        commentTemplate = commentTemplate.replace(/@REPLIES_NB@/g, parseInt(replyComment.replies_nb));

        const $replyComment = $(commentTemplate);
        const $action = $('.comment_dropdown_action', $replyComment);

        if (!replyComment.edit_profile)
            $('.reply-comment-author-profile-link', $replyComment).remove();

        if (!replyComment.action_allowed) {
            $('.comment_dropdown_action:not(.bo), .reply-comment-date-upd:not(.bo)', $replyComment).remove();
        } else {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_private.reply-comment', $replyComment).remove();
        }
        if (!replyComment.edit) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_edit.reply-comment', $replyComment).remove();
        }
        if (!replyComment.delete) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.ets_rv_btn_delete.reply-comment', $replyComment).remove();
        }
        if ($('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.reply-comment', $replyComment).length <= 0) {
            $('.comment_dropdown_action:not(.bo)', $replyComment).remove();
        }
        const date_upd = $('.reply-comment-date-upd', $replyComment);
        if (date_upd.length > 0 && date_upd.attr('data-upd-date')) {
            date_upd.show();
        }
        if (!replyComment.comment_allowed) {
            if (prop !== 'question') {
                $('.ets_rv_button_reply', $replyComment).remove();
                $('.ets_rv_form_reply_comment', $replyComment).empty();
            }
        }
        productCommentsETS.rawActions($replyComment, {
            type: 'reply-comment',
            options: {
                id_ets_rv_reply_comment: replyComment.id_ets_rv_reply_comment,
                __ac: 'useful_reply_comment'
            },
            bo: '',
            current: replyComment.current,
            usefulness: replyComment.usefulness,
        }, prop);

        $('.ets_rv_button_reply', $replyComment).click(function () {
            const _wrap = $('.ets_rv_reply_comment_list[data-comment-id=' + replyComment.id_ets_rv_comment + '] .ets_rv_form_reply_comment');
            _wrap
                .addClass('active')
                .find('textarea.ets_rv_reply_comment')
                .focus();

            var btnCancel = _wrap.find('.ets_rv_cancel_reply_comment');
            if (!btnCancel.hasClass('show_reply_box'))
                btnCancel.show();
        });

        var position = $('.ets_rv_reply_comment_list[data-comment-id=' + replyComment.id_ets_rv_comment + '] .ets_rv_reply_comment_' + (isPosted ? 'footer' : 'header'));
        if (isPosted) {
            position.before($replyComment);
        } else {
            position.after($replyComment);
        }
        if (replyComment.scroll) {
            if (ETS_RV_SCROLL_ITEM) {
                ETS_RV_SCROLL_ITEM.removeClass('ets_rv_highlight');
            }
            ETS_RV_SCROLL_ITEM = $('.reply-comment-list-item[data-reply-comment-id=' + replyComment.id_ets_rv_reply_comment + ']').addClass('ets_rv_highlight');
        }
    },
    pagination: function (button, prop, begin, perPage) {
        var ele = button || $('.ets_rv_reply_comment_load_more' + (prop ? '.question' : '')).eq(0);
        if (ele.length <= 0)
            return;
        var grade = $('.ets_rv_tab_reviews').length > 0 ? $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active') : 0,
            commentList = $('#ets-rv-product-' + prop + 's-list'),
            rest = ele.hasClass('forward-replies') ? parseInt(ele.attr('data-rest')) : 0
        ;
        if (!ele.hasClass('active') && commentList.data('comments-url')) {
            ele.addClass('active');
            $.ajax({
                url: commentList.data('comments-url'),
                data: '__ac=list_reply_comment&id_comment=' + ele.parents('.ets_rv_reply_comment_list').attr('data-comment-id') + '&begin=' + (begin ? begin : ele.attr('data-begin')) + '&replies_per_page=' + (perPage ? perPage : ele.attr('data-replies-per-page')) + '&sort_by=date_add.desc' + (grade && prop !== 'question' ? '&grade=' + grade.data('grade') : '') + (prop !== 'comment' ? '&qa=1' : '') + (rest ? '&rest=' + rest : ''),
                type: 'POST',
                dataType: 'json',
                success: function (json) {
                    if (json) {
                        ele.removeClass('active');
                        if (json.replies) {
                            json.replies.forEach(function (ele) {
                                repliesETS.add(ele, rest > 0);
                            });
                        }
                        var rest2 = parseInt(json.nb) - parseInt(json.begin);
                        ele.attr({
                            'data-begin': json.begin,
                            'data-replies-per-page': json.replies_per_page,
                            'data-rest': rest2
                        });
                        if (json.begin >= json.nb || json.replies_per_page <= 0) {
                            ele.hide();
                        } else if (json.success) {
                            if (rest > parseInt(json.replies_per_page)) {
                                ele.html(function () {
                                    return (rest2 > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, json.replies_per_page).replace(/%2s/g, rest2);
                                });
                            } else {
                                ele.html(function () {
                                    return (parseInt(json.replies_per_page) > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, json.replies_per_page);
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
        return false;
    },
};

var commentsETS = {
    add: function (comment, isPosted) {
        var qa = parseInt(comment.question) > 0 ? 1 : 0;
        var prop = qa > 0 ? 'question' : 'comment';
        const commentsList = $('#ets-rv-product-' + prop + 's-list'),
            initialReplies = commentsList.data('replies-initial') ? parseInt(commentsList.data('replies-initial')) : 1,
            repliesNbForward = typeof comment.replies_nb_forward !== "undefined" ? parseInt(comment.replies_nb_forward) : 0,
            repliesNb = parseInt(comment.replies_nb),
            nbReplies = repliesNb + repliesNbForward
        ;

        var commentTemplate = productCommentsETS.htmlTemplate($('#' + (qa ? prop + '-' : '') + 'comment-item-prototype').html(), comment);
        commentTemplate = commentTemplate.replace(/@COMMENT_ID@/g, comment.id_ets_rv_comment);
        commentTemplate = commentTemplate.replace(/@PRODUCT_COMMENT_ID@/g, parseInt(comment.id_ets_rv_product_comment));
        commentTemplate = commentTemplate.replace(/@REPLIES_NB@/g, nbReplies);//repliesNb
        commentTemplate = commentTemplate.replace(/@REPLIES_PER_PAGE@/g, commentsList.data('replies-per-page'));
        if (comment.replies) {
            if (repliesNbForward > 0) {
                var initialRepliesReal = initialReplies + repliesNb;
                commentTemplate = commentTemplate.replace(/@REPLIES_BEGIN@/g, initialRepliesReal);
                commentTemplate = commentTemplate.replace(/@REPLIES_LOADMORE@/g, nbReplies > initialRepliesReal ? nbReplies - initialRepliesReal : 0);
                commentTemplate = commentTemplate.replace(/@REPLIES_BEGIN_FORWARD@/g, repliesNb > initialReplies ? repliesNb - initialReplies : 0);
                commentTemplate = commentTemplate.replace(/@REPLIES_LOADMORE_FORWARD@/g, repliesNb);
                commentTemplate = commentTemplate.replace(/@REPLIES_PER_PAGE_FORWARD@/g, repliesNb > commentsList.data('replies-per-page') ? parseInt(commentsList.data('replies-per-page')) : repliesNb);
            } else {
                commentTemplate = commentTemplate.replace(/@REPLIES_BEGIN@/g, initialReplies);
                commentTemplate = commentTemplate.replace(/@REPLIES_LOADMORE@/g, repliesNb > initialReplies ? repliesNb - initialReplies : 0);
            }
        }
        if (parseInt(comment.answer)) {
            commentTemplate = commentTemplate.replace(/@USEFUL_ANSWER@/g, parseInt(comment.useful_answer) > 0 ? 'checked' : '');
            commentTemplate = commentTemplate.replace(/@TOTAL_USEFULNESS@/g, parseInt(comment.usefulness) - (parseInt(comment.total_usefulness) - parseInt(comment.usefulness)));
        } else {
            commentTemplate = commentTemplate.replace(/@USEFUL_ANSWER@/g, '');
            commentTemplate = commentTemplate.replace(/@TOTAL_USEFULNESS@/g, '');
        }

        const $comment = $(commentTemplate);
        const $action = $('.comment_dropdown_action', $comment);

        // Load more replies:
        if ((qa && parseInt(comment.answer) || !qa) && comment.replies && (repliesNbForward > 0 && nbReplies > initialRepliesReal || repliesNbForward <= 0 && repliesNb > initialReplies)) {
            var btn = $('.ets_rv_reply_comment_load_more:not(.forward-replies)', $comment),
                rest = parseInt(btn.attr('data-rest')),
                per_page = parseInt(btn.attr('data-replies-per-page'))
            ;
            btn.show();
            if (rest > per_page) {
                btn.html(function () {
                    return (rest > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, per_page).replace(/%2s/g, rest);
                });
            } else {
                btn.html(function () {
                    return (rest > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, rest);
                });
            }
        }
        // Load more comments forward.
        if ((qa && parseInt(comment.answer) || !qa) && comment.replies && repliesNbForward > 0) {
            var btn_forward = $('.ets_rv_reply_comment_load_more.forward-replies.bo', $comment),
                rest_forward = parseInt(btn_forward.attr('data-rest')),
                per_page_forward = parseInt(btn_forward.attr('data-replies-per-page'))
            ;
            if (rest_forward <= 0 || repliesNb <= 0) {
                btn_forward.hide();
            } else {
                btn_forward.show();
                if (rest_forward > per_page_forward) {
                    btn_forward.html(function () {
                        return (rest_forward > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, per_page_forward).replace(/%2s/g, rest_forward);
                    });
                } else {
                    btn_forward.html(function () {
                        return (rest_forward > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, rest_forward);
                    });
                }
            }
        }

        if (!comment.edit_profile)
            $('.comment-author-profile-link', $comment).remove();

        if (!comment.action_allowed) {
            $('.comment_dropdown_action:not(.bo), .comment-date-upd:not(.bo)', $comment).remove();
        } else {
            $('.comment_dropdown_action:not(.bo) .ets_rv_btn_private.comment', $comment).remove();
        }
        if (!comment.edit) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_btn_edit.comment', $comment).remove();
        }
        if (!comment.delete) {
            $('.comment_dropdown_action:not(.bo) .ets_rv_btn_delete.comment', $comment).remove();
        }
        if ($('.comment_dropdown_action:not(.bo) .ets_rv_li_dropdown.comment', $comment).length <= 0) {
            $('.comment_dropdown_action:not(.bo)', $comment).remove();
        }
        const date_upd = $('.comment-date-upd', $comment);
        if (date_upd.length > 0 && date_upd.attr('data-upd-date')) {
            date_upd.show().addClass('show_il');
        }
        if (!comment.comment_allowed) {
            if (prop !== 'comment') {
                $('.ets_rv_btn_add_reply', $comment).remove();
            } else {
                $('.ets_rv_button_reply', $comment).remove();
                $('.ets_rv_form_reply_comment', $comment).empty();
            }
        }
        productCommentsETS.rawActions($comment, {
            type: 'comment',
            options: {
                id_ets_rv_comment: comment.id_ets_rv_comment,
                __ac: 'useful_comment'
            },
            bo: '',
            current: comment.current,
            usefulness: comment.usefulness,
        }, prop);
        $(prop !== 'question' ? '.ets_rv_button_reply' : '.ets_rv_btn_add_reply', $comment).click(function () {
            const _wrap = $('.ets_rv_reply_comment_list[data-comment-id=' + comment.id_ets_rv_comment + '] .ets_rv_form_reply_comment');
            _wrap
                .addClass('active')
                .find('textarea.ets_rv_reply_comment')
                .focus();

            var btnCancel = _wrap.find('.ets_rv_cancel_reply_comment');
            if (!btnCancel.hasClass('show_reply_box'))
                btnCancel.show();
        });

        const option = parseInt(comment.answer) > 0 ? 'answer' : 'comment';
        const raw = $('.ets_rv_' + option + '_list[data-product-comment-id=' + comment.id_ets_rv_product_comment + ']');
        if (isPosted) {
            raw.find('.ets_rv_comment_footer').before($comment);
        } else {
            if (prop !== 'comment' && option !== 'comment' && raw.find('.comment-list-item[data-useful-answer*=checked]').length > 0) {
                raw.find('.comment-list-item[data-useful-answer*=checked]').after($comment);
            } else {
                raw.find('.ets_rv_comment_header').after($comment);
            }
        }
        raw.find('.ets_rv_answers_nb:hidden').show();
        if (qa && !parseInt(comment.answer)) {
            $('.ets_rv_reply_comment_list, .nb-reply-comment, .ets_rv_button_reply, .ets_rv_btn_add_reply, .comment_arrow_total_ans.answer', $comment).remove();
        } else if (parseInt(comment.answer)) {
            $('.ets-rv-comment-buttons .useful-comment, .ets-rv-comment-buttons .ets-rv-not-useful-comment', $comment).remove();
        }
        //Scroll item:
        if (comment.scroll) {
            if (qa && parseInt(comment.answer) <= 0) {
                var commentBox = $comment.parents('.ets_rv_comment_list').eq(0).find('.ets_rv_comment_footer .ets_rv_form_comment').eq(0);
                commentBox.addClass('active');
                setTimeout(function () {
                    $('.ets_rv_overload.active').animate({scrollTop: commentBox.position().top}, 750);
                    if (typeof id_language !== "undefined" && id_language > 0) {
                        commentBox.find('form textarea[name=comment_content_' + id_language + ']').focus();
                    } else {
                        commentBox.find('form textarea[name^=comment_content]').focus();
                    }
                }, 750);
            } else {
                var replyBox = $('.ets_rv_form_reply_comment', $comment);
                replyBox.addClass('active');
                setTimeout(function () {
                    $('.ets_rv_overload.active').animate({scrollTop: replyBox.offset().top}, 750);
                    if (typeof id_language !== "undefined" && id_language > 0) {
                        replyBox.find('form textarea[name=comment_content_' + id_language + ']').focus();
                    } else {
                        replyBox.find('form textarea[name^=comment_content]').focus();
                    }

                }, 750);
            }
            if (ETS_RV_SCROLL_ITEM !== null) {
                ETS_RV_SCROLL_ITEM.removeClass('ets_rv_highlight');
            }
            ETS_RV_SCROLL_ITEM = $('.comment-list-item[data-comment-id=' + comment.id_ets_rv_comment + ']').addClass('ets_rv_highlight');
        }
        if (comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(function (ele) {
                repliesETS.add(ele, false);
            });
        }
        reCaptchaETS.onLoad($('form.form-control-reply-comment:not(.g-loaded)', $comment));
    },
    pagination: function (button, prop, begin, perPage) {
        var ele = button || $('.ets_rv_comment_load_more' + (prop ? '.question' : '')).eq(0);
        if (ele.length <= 0)
            return;
        var grade = $('.ets_rv_tab_reviews').length > 0 ? $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active') : 0,
            commentList = $('#ets-rv-product-' + prop + 's-list'),
            option = ele.hasClass('answer') ? 'answer' : 'comment',
            rest = ele.hasClass('forward-comments') || ele.hasClass('forward-answers') ? parseInt(ele.attr('data-rest')) : 0
        ;
        if (!ele.hasClass('active') && commentList.data('comments-url')) {
            ele.addClass('active');
            $.ajax({
                url: commentList.data('comments-url'),
                data: '__ac=list_comment&id_product_comment=' + ele.parents('.ets_rv_' + option + '_list').attr('data-product-comment-id') + '&begin=' + (begin ? begin : ele.attr('data-begin')) + '&comments_per_page=' + (perPage ? perPage : ele.attr('data-comments-per-page')) + '&sort_by=date_add.desc' + (grade && prop !== 'question' ? '&grade=' + grade.data('grade') : '') + (prop !== 'comment' ? '&qa=1' : '') + (option !== 'comment' ? '&answer=1' : '') + (rest ? '&rest=' + rest : ''),
                type: 'POST',
                dataType: 'json',
                success: function (json) {
                    if (json) {
                        ele.removeClass('active');
                        if (json.comments) {
                            json.comments.forEach(function (ele) {
                                commentsETS.add(ele, rest > 0);
                            });
                        }
                        if (json.begin >= json.nb || json.comments_per_page <= 0) {
                            ele.remove();
                        } else if (json.success) {
                            var rest2 = parseInt(json.nb) - parseInt(json.begin);
                            ele.attr({
                                'data-begin': json.begin,
                                'data-comments-per-page': json.comments_per_page,
                                'data-rest': rest2
                            });
                            if (rest2 > parseInt(json.comments_per_page)) {
                                ele.html(function () {
                                    return (rest2 > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, json.comments_per_page).replace(/%2s/g, rest2);
                                });
                            } else {
                                ele.html(function () {
                                    return (parseInt(json.comments_per_page) > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, json.comments_per_page);
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
        return false;
    },
    post: function (form, button) {
        var id_lang_default = form.data('lang-default'),
            ele = form.find('textarea[name=comment_content]').eq(0).length > 0 ? form.find('textarea[name=comment_content]').eq(0) : form.find('textarea[name=comment_content_' + id_lang_default + ']').eq(0)
        ;
        if (ele.val() && !button.hasClass('active')) {
            button.addClass('active');
            var option = form.hasClass('answer') ? 'answer' : 'comment',
                commentList = form.parents('.ets_rv_' + option + '_list').eq(0),
                commentBlock = form.parents('.ets-rv-product-comment-list-item');
            var comment = commentList.find('.comment-list-item.active').eq(0),
                onUpdate = button.hasClass('update-comment')
            ;
            var formData = new FormData(form.get(0));
            formData.append('id_product', commentList.data('product-id'));
            formData.append('id_product_comment', commentList.data('product-comment-id'));
            if (option !== 'comment')
                formData.append('answer', 1);
            if (onUpdate)
                formData.append('id_comment', comment.data('comment-id'));
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (json) {
                    button.removeClass('active');
                    reCaptchaETS.reset(form);
                    if (json) {
                        if (!json.success) {
                            productCommentsETS.showErrorModal(json.error);
                        } else if (json.comment) {
                            if (onUpdate) {
                                if (typeof json.comment.content === "object") {
                                    var languages = Object.keys(json.comment.content);
                                    languages.forEach(function (id_lang) {
                                        comment.find('.ets-rv-comment-content-html > span .translatable-field.lang-' + id_lang).html(funcPCETS.n2br(json.comment.content[id_lang]));
                                    });
                                } else
                                    comment.find('.ets-rv-comment-content-html > span').html(funcPCETS.n2br(json.comment.content));
                                if (json.comment.upd_date)
                                    comment.find('.comment-date-upd').show().find('.comment-upd-date').html(json.comment.upd_date);
                                if (json.comment.time_elapsed)
                                    comment.find('.comment-date-add.ets_rv_date_add > span').attr('title', json.comment.date_add).html(json.comment.time_elapsed);
                            } else {
                                commentsETS.add(json.comment, true);
                                if (option !== 'answer') {
                                    var nb_comments = 0;
                                    commentBlock.find('.nb-comment-value').text(function () {
                                        nb_comments = parseInt($(this).text()) + 1;
                                        return nb_comments;
                                    });
                                    commentBlock.find('.nb-comment-text').text(function () {
                                        return nb_comments > 1 ? $(this).data('multi-text') : $(this).data('text');
                                    });
                                    commentBlock.find('.nb-comment-text').parents('.nb-comment:not(.active)').trigger('click');
                                } else if (commentList.hasClass('answer')) {
                                    var nb_answers = 0;
                                    commentBlock.find('.ets_rv_nb_answers').text(function () {
                                        nb_answers = parseInt($(this).text()) + 1;
                                        return nb_answers;
                                    });
                                    commentBlock.find('.ets_rv_answers_text').html(function () {
                                        return nb_answers > 1 ? $(this).data('multi-text') : $(this).data('text');
                                    });
                                    commentBlock.find('.ets_rv_answers_text').parents('.ets_rv_btn_show_answer:not(.active)').trigger('click');
                                }
                            }
                            form.find('textarea[name^=comment_content]').val('');
                            if (commentList.find('button.ets_rv_post_comment.update-comment').length > 0) {
                                commentList.find('button.ets_rv_cancel_comment:visible').trigger('click');
                            }
                            // admin:
                            if (typeof ets_rv !== "undefined")
                                ets_rv.refreshList(json);
                            commentList.addClass('show_content');
                        }
                    }
                },
                error: function () {
                    button.removeClass('active');
                    reCaptchaETS.reset(form);
                }
            });
        } else {
            ele.focus();
            if (typeof hideOtherLanguage !== typeof undefined)
                hideOtherLanguage(id_lang_default);
            if (typeof ETS_RV_DEFAULT_LANGUAGE_MSG !== typeof undefined)
                showErrorMessage(ETS_RV_DEFAULT_LANGUAGE_MSG);
        }
    },
};

var ETS_RV_ADDED_PRODUCT_COMMENT = false;

$(document).ready(function () {
    // Init function.
    productCommentsETS.init();
    $(document).on('hooksLoaded', function () {
        productCommentsETS.init();
    });
    $(document).on('keyup', '.error', function () {
        if (typeof $(this)[0] === typeof undefined || typeof $(this)[0].name === typeof undefined || !$(this)[0].name) return;
        if ($(this)[0].name !== 'email' && $(this).val() !== '' || $(this)[0].name === 'email' && /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test($(this).val()))
            $(this).removeClass('error');
    });
    $(document).on('click', '.ets_rv_product_id.ets-rv-post-product-comment', function (e) {
        e.preventDefault();
        var ele = $(this);
        if (!ele.hasClass('active')) {
            ele.addClass('active');
            var orderId = ele.data('id-order') ?? 0;
            $.ajax({
                type: 'GET',
                data: 'qa=0&ajax=1&action=formPostComment&product_id=' + ele.data('id-product') + (parseInt(orderId) > 0 ? '&id_order=' + ele.data('id-order') : ''),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            funcPCETS.showErrorMessage(json.errors);
                        } else {
                            if (json.form) {
                                $('.ets_rv_form').html(json.form);
                                commonPCETS.initForm('comment');
                                commonPCETS.showPostModal('comment', 'add');
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
    if (typeof $.fn.slick !== typeof undefined && $('.ets_rv_review_photos').length > 0) {
        $('.ets_rv_review_photos').slick({
            arrows: true,
            slidesToShow: 6,
            slidesToScroll: 1,
            speed: 300,
            autoplay: true,
            adaptiveHeight: false,
            responsive: [
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 6,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                }
            ]
        });
    }
    $(document).on('click', '.ets_rv_review_photos_wrap.display_type_slider .ets_rv_image_item .ets_rv_fancy', function (e) {
        e.preventDefault();
        $(this).closest('.ets_rv_review_photos_wrap').find('.ets_image_list_popup').addClass('active').addClass('blur_content');
        let current_slide_active = $(this).attr('data-value');

        if (!$('.ets_image_list_popup.active .ets_rv_review_photos_ul').hasClass('slick-initialized')) {
            $('.ets_image_list_popup.active .ets_rv_review_photos_ul').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
            });
        }
        $('.ets_image_list_popup.active .ets_rv_review_photos_ul').slick('slickGoTo', current_slide_active, true);
        $('.ets_image_list_popup.active.blur_content').removeClass('blur_content');

    });
    $(document).on('click', '.product-comment-content_images_videos .ets_rv_image_item > .ets_rv_fancy', function (e) {
        if ($(this).parents('.slick-slide').length <= 0) {
            e.preventDefault();
            var content_slider = $(this).parents('.ets_rv_images').clone(),
                value_active = $(this).attr('data-value') - 1
            ;
            $(this).parents('.product-comment-image-html').find('.ets_popup_content').append(content_slider);
            $('.ets_popup_content .ets_rv_fancy').each(function () {
                $(this).css('background-image', 'url(' + $(this).attr('href') + ')');
            });
            $(this).parents('.product-comment-image-html').find('.ets_image_list_popup').addClass('active').addClass('blur_content');
            setTimeout(function () {
                $('.ets_image_list_popup.active .ets_rv_images').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                });
                $('.ets_image_list_popup.active .ets_rv_images').slick('slickGoTo', value_active, true);
                $('.ets_image_list_popup.active.blur_content').removeClass('blur_content');
            }, 500);
        }
    });

    $(document).keyup(function (e) {
        if (e.keyCode === 27) {
            $('.ets_image_list_popup.active').removeClass('blur_content').removeClass('active');
            $('.ets_image_list_popup').find('.ets_rv_images').remove();
        }
    });

    $(document).on('click', '.close_img_list', function () {
        $('.ets_image_list_popup.active').removeClass('blur_content').removeClass('active');
        $('.ets_image_list_popup').find('.ets_rv_images').remove();
    });
    $(document).on('click', '.ets_rv_form_comment.guest textarea.ets_rv_comment', function (e) {
        e.preventDefault();
        funcPCETS.isGuestLogin($(this));

        return false;
    });
    $(document).on('keypress', '.form-control-comment textarea.ets_rv_comment', function (event) {
        if (parseInt(event.keyCode) === 13 && ETS_RV_PRESS_ENTER_ENABLED) {
            var ele = $(this),
                form = ele.parents('form').eq(0)
            ;
            commentsETS.post(form, form.find('button.ets_rv_post_comment'));
            event.preventDefault();
        }
    });

    $(document).on('click', '.ets-rv-post-product-question', function () {
        $('.ets-rv-btn-question-big').removeClass('active');
        $('.ets-rv-product-comment-modal .ets_rv_error, .ets-rv-product-comment-modal .ets_rv_error').remove();
    });

    $(document).on('click', '.form-control-comment button.ets_rv_post_comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            form = $(this).parents('form')
        ;
        if (form.parent('.ets_rv_form_comment.guest').length > 0) {
            funcPCETS.isGuestLogin($(this));

            return false;
        }
        commentsETS.post(form, ele);
    });

    $(document).on('keypress', '.form-control-reply-comment .ets_rv_reply_comment', function (event) {
        if (parseInt(event.keyCode) === 13 && typeof ETS_RV_PRESS_ENTER_ENABLED !== "undefined" && ETS_RV_PRESS_ENTER_ENABLED) {
            var ele = $(this),
                form = ele.parents('form').eq(0)
            ;
            repliesETS.post(form, form.find('button.ets_rv_post_reply_comment'));
            return false;
        }
    });

    $(document).on('click', '.form-control-reply-comment .ets_rv_post_reply_comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            form = $(this).parents('form')
        ;
        repliesETS.post(form, ele);
    });

    // load more.
    $(document).on('click', '.ets_rv_reply_comment_load_more', function (ev) {
        ev.preventDefault();
        repliesETS.pagination($(this), ($(this).hasClass('question') ? 'question' : 'comment'));
    });

    $(document).on('click', '.ets_rv_comment_load_more', function (ev) {
        ev.preventDefault();
        commentsETS.pagination($(this), ($(this).hasClass('question') ? 'question' : 'comment'));

    });

    $(document).on('click', '.ets_rv_product_comment_load_more', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            sort_by = $('.ets_rv_sort_by').length > 0 ? $('.ets_rv_sort_by .ets_rv_li_dropdown.active') : false,
            grade = $('.ets_rv_tab_reviews').length > 0 ? $('.ets_rv_tab_reviews .ets_rv_li_dropdown.active') : false,
            commentList = ele.parents('.ets_rv_tab_content').eq(0)
        ;
        if (!ele.hasClass('active') && commentList.data('comments-url')) {
            ele.addClass('active');
            $.ajax({
                url: commentList.data('comments-url'),
                data: '__ac=list_product_comment&begin=' + ele.attr('data-begin') + '&reviews_per_page=' + ele.attr('data-reviews-per-page') + (sort_by ? '&sort_by=' + sort_by.data('sort') : '') + (grade ? '&grade=' + grade.data('grade') : ''),
                type: 'POST',
                dataType: 'json',
                success: function (json) {
                    if (json) {
                        ele.removeClass('active');
                        if (json.comments) {
                            json.comments.forEach(function (ele) {
                                productCommentsETS.add(ele, false);
                            });
                        }
                        var rest = parseInt(json.reviews_nb) - parseInt(json.begin);
                        if (parseInt(json.begin) >= parseInt(json.reviews_nb) || parseInt(json.reviews_per_page) <= 0)
                            ele.hide();
                        else if (json.success) {
                            ele.attr({
                                'data-begin': json.begin,
                                'data-reviews-per-page': json.reviews_per_page,
                                'data-rest': rest,
                            });
                            if (parseInt(ele.attr('data-rest')) > parseInt(ele.attr('data-reviews-per-page'))) {
                                ele.html(function () {
                                    return (rest > 1 ? $(this).data('multi-text-rest') : $(this).data('text-rest')).replace(/%1s/g, json.reviews_per_page).replace(/%2s/g, rest);
                                });
                            } else {
                                ele.html(function () {
                                    return (parseInt(json.reviews_per_page) > 1 ? $(this).data('multi-text') : $(this).data('text')).replace(/%1s/g, json.reviews_per_page);
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
        return false;
    });

    // Delete.
    $(document).on('click', '.ets_rv_btn_delete.product-comment', function (ev) {
        ev.preventDefault();
        if (!$(this).hasClass('employee')) {
            var ele = $(this),
                productComment = ele.parents('.ets-rv-product-comment-list-item'),
                prop = productComment.hasClass('question') ? 'question' : 'comment',
                commentList = $('#ets-rv-product-' + prop + 's-list')
            ;
            if (!ele.hasClass('active') && confirm('Do you want delete item selected?')) {
                $('.ets-rv-product-comment-list-item').removeClass('active');
                ele.addClass('active');
                $.ajax({
                    type: 'POST',
                    url: commentList.data('comments-url'),
                    data: '__ac=delete_product_comment&id_product_comment=' + productComment.data('product-comment-id'),
                    dataType: 'json',
                    success: function (json) {
                        ele.removeClass('active');
                        if (json) {
                            if (!json.success)
                                productCommentsETS.showErrorModal(json.error);
                            else {
                                if (json.msg)
                                    funcPCETS.showSuccessMessage(json.msg);
                                if ((parseInt(json.review_enabled) > 0 || parseInt(json.question_enabled) > 0) && (parseInt(json.nb_reviews) < 1 && parseInt(json.nb_questions) < 1 || parseInt(json.nb_reviews) < 1 && parseInt(json.nb_questions) > 0 && parseInt(json.question_enabled) < 1 || parseInt(json.nb_reviews) > 0 && parseInt(json.review_enabled) < 1 && parseInt(json.nb_questions) < 1)) {
                                    //$('.ets-rv-product-comments-additional-info').show();
                                    if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                        $('.ets_rv_wrap').hide();
                                    if (parseInt(json.nb_reviews) < 1) {
                                        if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user').addClass('ets-rv-hidden');
                                        if (typeof json.stats !== typeof undefined) {
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars').attr('data-grade', json.stats.average_grade.toFixed(1));
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars .ets-rv-comments-nb').text('(' + json.stats.nb_reviews + ')');
                                        }
                                    }
                                    if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                        $('.ets-rv-product-comments-additional-info .link-comment, .ets-rv-product-comments-additional-info .ets-rv-btn-comment').removeClass('ets-rv-hidden');
                                } else {
                                    //$('.ets-rv-product-comments-additional-info').hide();
                                    if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                        $('.ets_rv_wrap').show();
                                    if (parseInt(json.nb_reviews) > 0) {
                                        if (typeof json.stats !== typeof undefined) {
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars').attr('data-grade', json.stats.average_grade.toFixed(1));
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user .ets_rv_grade_stars .ets-rv-comments-nb').text('(' + json.stats.nb_reviews + ')');
                                        }
                                        if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                            $('.ets-rv-product-comments-additional-info .ets-rv-btn-read-user').removeClass('ets-rv-hidden');
                                    }
                                    if (ETS_RV_DISPLAY_RATE_AND_QUESTION === 'button')
                                        $('.ets-rv-product-comments-additional-info .link-comment, .ets-rv-product-comments-additional-info .ets-rv-btn-comment').addClass('ets-rv-hidden');
                                }
                                productComment.remove();
                                productCommentsETS.updateCriterion('delete', json.nb_rate);
                                if ($('#ets-rv-product-' + prop + 's-list .ets-rv-product-comment-list-item').length < 1) $('#ets-rv-product-' + prop + 's-list #product-comments-list-footer .ets-rv-' + prop + '.empty').show();
                                if (prop !== 'question') {
                                    if (json.review_allowed) {
                                        $('#ets-rv-post-product-comment-form').removeClass('hide');
                                        $('.ets_rv_review_error').addClass('hide');
                                    } else {
                                        $('#ets-rv-post-product-comment-form').addClass('hide');
                                        $('.ets_rv_review_error').removeClass('hide');
                                    }
                                }
                                if ($('body[id*=comment]').length > 0) {
                                    window.location.reload();
                                }
                                // front:
                                if (json.stats) {
                                    funcPCETS.stats(json.stats, prop);
                                } else if (typeof json.nb_questions !== "undefined") {
                                    $('#ets-rv-product-comments-list-header .ets_rv_tab_questions .ets_rv_question_selection').html(function () {
                                        return $(this).html().replace(/\d+/, parseInt(json.nb_questions))
                                    });
                                }
                                if (json.photos) {
                                    if ($('.ets_rv_review_photos_wrap').length <= 0) {
                                        $('.ets_rv_statistics').after(funcPCETS.decodeHTMLEntities(json.photos));
                                    } else {
                                        $('.ets_rv_review_photos_wrap').replaceWith(funcPCETS.decodeHTMLEntities(json.photos));
                                    }
                                    $('.ets_rv_review_photos.slick-initialized').slick('unslick');
                                    $('.ets_rv_review_photos_wrap .ets_rv_review_photos:not(.slick-initialized)').slick({
                                        arrows: true,
                                        autoplay: true,
                                        slidesToShow: 6,
                                        slidesToScroll: 1,
                                        speed: 300,
                                        adaptiveHeight: false,
                                        responsive: [
                                            {
                                                breakpoint: 1199,
                                                settings: {
                                                    slidesToShow: 6,
                                                    slidesToScroll: 6,
                                                    infinite: true,
                                                    dots: true
                                                }
                                            },
                                            {
                                                breakpoint: 992,
                                                settings: {
                                                    slidesToShow: 5,
                                                    slidesToScroll: 5,
                                                    infinite: true,
                                                    dots: true
                                                }
                                            },
                                            {
                                                breakpoint: 768,
                                                settings: {
                                                    slidesToShow: 4,
                                                    slidesToScroll: 4
                                                }
                                            },
                                            {
                                                breakpoint: 480,
                                                settings: {
                                                    slidesToShow: 3,
                                                    slidesToScroll: 3
                                                }
                                            }
                                        ]
                                    });
                                    $('.ets_rv_review_photos_wrap').addClass('active');
                                }
                                // backoffice:
                                if (typeof ets_rv !== "undefined")
                                    ets_rv.refreshList(json);
                            }
                        }
                    },
                    error: function () {
                        ele.removeClass('active');
                    }
                });
            }
        } else
            $('#product-comment-render-form a.delete').trigger('click');
    });
    $(document).on('click', '.ets_rv_btn_delete.comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            prop = ele.parents('.comment-list-item.question') ? 'question' : 'comment',
            commentList = $('#ets-rv-product-' + prop + 's-list'),
            comment = ele.parents('.comment-list-item'),
            listComment = comment.parents('.ets_rv_comment_list').eq(0).length > 0 ? comment.parents('.ets_rv_comment_list').eq(0) : comment.parents('.ets_rv_answer_list').eq(0),
            productCommentActions = ele.parents('.ets-rv-product-comment-list-item').eq(0).find('.comment_actions_right').eq(0)
        ;
        if (!ele.hasClass('active') && confirm('Do you want delete item selected?')) {
            $('.comment-list-item').removeClass('active');
            ele.addClass('active');
            $.ajax({
                type: 'POST',
                url: commentList.data('comments-url'),
                data: '__ac=delete_comment&id_comment=' + comment.data('comment-id') + (prop === 'question' ? '&qa=1' : ''),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (!json.success)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);
                            comment.remove();
                            listComment.find('.ets_rv_comment_load_more').attr('data-begin', function () {
                                return parseInt($(this).attr('data-begin')) > 0 ? parseInt($(this).attr('data-begin')) - 1 : 0;
                            });
                            listComment.prev().find('.nb-comment-value').text(function () {
                                return parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0;
                            });
                            if (listComment.hasClass('answer')) {
                                var nb_reviews = 0;
                                listComment.find('.ets_rv_nb_answers').text(function () {
                                    nb_reviews = (parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0);
                                    return nb_reviews;
                                });
                                listComment.find('.ets_rv_answers_text').html(function () {
                                    return (nb_reviews > 1 ? $(this).data('multi-text') : $(this).data('text'));
                                });
                            }

                            //new code:
                            if (productCommentActions.length > 0) {
                                if (listComment.hasClass('answer')) {
                                    var nb_questions = 0;
                                    productCommentActions.find('.ets_rv_nb_answers').text(function () {
                                        nb_questions = (parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0);
                                        return nb_questions;
                                    });
                                    productCommentActions.find('.ets_rv_answers_text').html(function () {
                                        return (nb_questions > 1 ? $(this).data('multi-text') : $(this).data('text'));
                                    });
                                } else {
                                    productCommentActions.find('.nb-comment-value').html(function () {
                                        nb_questions = (parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0);
                                        return nb_questions;
                                    });
                                    productCommentActions.find('.nb-comment-text').html(function () {
                                        return (nb_questions > 1 ? $(this).data('multi-text') : $(this).data('text'));
                                    });
                                }
                            }
                        }
                        // admin:
                        if (typeof ets_rv !== "undefined")
                            ets_rv.refreshList(json);
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_rv_btn_delete.reply-comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            commentList = $('#ets-rv-product-comments-list'),
            replyComment = ele.parents('.reply-comment-list-item').eq(0),
            listReplyComment = replyComment.parents('.ets_rv_reply_comment_list').eq(0)
        ;
        if (!ele.hasClass('active') && confirm('Do you want delete item selected?')) {
            if (listReplyComment.find('button.ets_rv_post_reply_comment.update-reply-comment').length > 0) {
                listReplyComment.find('button.ets_rv_cancel_reply_comment:visible').trigger('click');
            }
            $('.reply-comment-list-item').removeClass('active');
            ele.addClass('active');
            $.ajax({
                type: 'POST',
                url: commentList.data('comments-url'),
                data: '__ac=delete_reply_comment&id_reply_comment=' + replyComment.data('reply-comment-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (!json.success)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);
                            replyComment.remove();
                            listReplyComment.find('.ets_rv_reply_comment_load_more').attr('data-begin', function () {
                                return parseInt($(this).attr('data-begin')) > 0 ? parseInt($(this).attr('data-begin')) - 1 : 0;
                            });
                            listReplyComment.prev().find('.nb-reply-comment-value').text(function () {
                                var data_count = parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0;
                                $(this).parent('.nb-reply-comment').attr('data-count', data_count);
                                return parseInt($(this).text()) > 0 ? parseInt($(this).text()) - 1 : 0;

                            });
                            // admin:
                            if (typeof ets_rv !== "undefined")
                                ets_rv.refreshList(json);
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
    });

    // Approve
    $(document).on('click', '.ets_rv_btn_approve.product-comment, .ets_rv_btn_private.product-comment', function (ev) {
        ev.preventDefault();
        if ($(this).hasClass('bo')) {
            var ele = $(this),
                productComment = ele.parents('.ets-rv-product-comment-list-item'),
                prop = productComment.hasClass('question') ? 'question' : 'comment',
                commentList = $('#ets-rv-product-' + prop + 's-list'),
                _ac = ele.hasClass('ets_rv_btn_private') ? 'private' : 'approve'
            ;
            if (!ele.hasClass('active')) {
                $('.ets-rv-product-comment-list-item').removeClass('active');
                ele.addClass('active');
                $.ajax({
                    type: 'POST',
                    url: commentList.data('comments-url'),
                    data: '__ac=' + _ac + '_product_comment&id_product_comment=' + productComment.data('product-comment-id'),
                    dataType: 'json',
                    success: function (json) {
                        ele.removeClass('active');
                        if (json) {
                            if (!json.success)
                                productCommentsETS.showErrorModal(json.error);
                            else {
                                if (json.msg)
                                    funcPCETS.showSuccessMessage(json.msg);
                                if (_ac !== 'private') {
                                    ele.parents('.ets-rv-product-comment-list-item').attr('data-status', 'approved');
                                } else {
                                    ele.parents('.ets-rv-product-comment-list-item').attr('data-status', 'private');
                                }
                                // admin:
                                if (typeof ets_rv !== "undefined")
                                    ets_rv.refreshList(json);

                                if (ele.hasClass('ets_rv_btn_approve'))
                                    $('#product-comment-render-form .panel-footer a.ets_rv_approve').hide();
                                else
                                    $('#product-comment-render-form .panel-footer a.ets_rv_approve').show();
                            }
                        }
                    },
                    error: function () {
                        ele.removeClass('active');
                    }
                });
            }
        }
    });
    $(document).on('click', '.ets_rv_btn_approve.comment, .ets_rv_btn_private.comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            prop = ele.parents('.comment-list-item').hasClass('question') ? 'question' : 'comment',
            commentList = $('#ets-rv-product-' + prop + 's-list'),
            comment = ele.parents('.comment-list-item'),
            _ac = ele.hasClass('ets_rv_btn_private') ? 'private' : 'approve'
        ;
        if (!ele.hasClass('active')) {
            $('.comment-list-item').removeClass('active');
            ele.addClass('active');
            $.ajax({
                type: 'POST',
                url: commentList.data('comments-url'),
                data: '__ac=' + _ac + '_comment&id_comment=' + comment.data('comment-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (!json.success)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);

                            if (_ac !== 'private') {
                                ele.parents('.comment-list-item').attr('data-status', 'approved');
                            } else {
                                ele.parents('.comment-list-item').attr('data-status', 'private');
                            }

                            // admin:
                            if (typeof ets_rv !== "undefined")
                                ets_rv.refreshList(json);
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_rv_btn_approve.reply-comment, .ets_rv_btn_private.reply-comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            prop = ele.parents('.reply-comment-list-item').hasClass('question') ? 'question' : 'comment',
            commentList = $('#ets-rv-product-' + prop + 's-list'),
            replyComment = ele.parents('.reply-comment-list-item').eq(0),
            _ac = ele.hasClass('ets_rv_btn_private') ? 'private' : 'approve'
        ;
        if (!ele.hasClass('active')) {
            $('.reply-comment-list-item').removeClass('active');
            ele.addClass('active');
            $.ajax({
                type: 'POST',
                url: commentList.data('comments-url'),
                data: '__ac=' + _ac + '_reply_comment&id_reply_comment=' + replyComment.data('reply-comment-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (!json.success)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);

                            if (_ac !== 'private') {
                                ele.parents('.reply-comment-list-item').attr('data-status', 'approved');
                            } else {
                                ele.parents('.reply-comment-list-item').attr('data-status', 'private');
                            }
                            // Backoffice:
                            if (typeof ets_rv !== "undefined")
                                ets_rv.refreshList(json);
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
    });

    // Edit
    $(document).on('click', '.ets_rv_btn_edit.reply-comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            replyComment = ele.parents('.reply-comment-list-item').eq(0),
            listReplyComment = replyComment.parents('.ets_rv_reply_comment_list').eq(0),
            dropdown = ele.parents('.comment_dropdown_action').eq(0),
            prop = replyComment.hasClass('question') ? 'question' : 'comment'
        ;

        if (!ele.hasClass('active') && !replyComment.hasClass('active')) {

            reCaptchaETS.reset(listReplyComment.find('.form-control-reply-comment'));

            if (listReplyComment.find('button.ets_rv_post_reply_comment.update-reply-comment').length > 0) {
                listReplyComment.find('button.ets_rv_cancel_reply_comment:visible').trigger('click');
            }
            $('.reply-comment-list-item').removeClass('active');
            replyComment.addClass('active');
            ele.addClass('active');
            if (!ele.hasClass('json-loading')) {
                ele.addClass('json-loading');
                var commentList = $('#ets-rv-product-' + prop + 's-list');
                $.ajax({
                    url: commentList.data('comments-url'),
                    data: '__ac=reply_comment&id_reply_comment=' + replyComment.data('reply-comment-id'),
                    type: 'get',
                    dataType: 'json',
                    success: function (json) {
                        ele.removeClass('json-loading');
                        if (json) {
                            listReplyComment.find('.form-control-reply-comment textarea[name^=comment_content]').val(function () {
                                var matches = $(this).attr('name').match(/comment_content_(\d+)/i);
                                if (matches && typeof matches[1] !== "undefined") {
                                    var prop = matches[1];
                                    if (json.content.hasOwnProperty(prop)) {
                                        return funcPCETS.br2n(json.content[prop] ? json.content[prop] : json.origin_content);
                                    }
                                } else
                                    return funcPCETS.br2n(json.origin_content);
                            });
                            if (dropdown.hasClass('bo')) {
                                if (listReplyComment.find('.form-control-reply-comment input.datetimepicker').length < 1)
                                    listReplyComment.find('.form-control-reply-comment .form-group.date_add').find('.input-group').append('<input id="date_add_' + Math.random().toString(36).slice(-8) + '" type="text" data-hex="true" class="datetimepicker" name="date_add" />');
                                listReplyComment.find('.form-control-reply-comment .form-group.date_add').removeClass('hide');
                                if (json.date_add !== '0000-00-00 00:00:00')
                                    listReplyComment.find('.form-control-reply-comment input.datetimepicker').val(json.date_add);
                                listReplyComment.find('.form-control-reply-comment input.datetimepicker:not(.hasDatepicker)').datetimepicker(ETS_RV_DATETIME_PICKER);
                            }

                            listReplyComment.find('button.ets_rv_cancel_reply_comment:hidden').show();
                            listReplyComment.find('button.ets_rv_post_reply_comment')
                                .addClass('update-reply-comment')
                                .html(function () {
                                    return $(this).data('upd');
                                });
                            listReplyComment.find('.ets_rv_form_reply_comment:not(.active)').addClass('active');
                            listReplyComment.find('textarea.ets_rv_reply_comment').focus();
                        }
                    },
                    error: function () {
                        ele.removeClass('json-loading');
                        ele.removeClass('active');
                    }
                });
            }
        }
    });
    $(document).on('click', '.ets_rv_btn_edit.comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            comment = ele.parents('.comment-list-item').eq(0),
            listComment = comment.parents('.ets_rv_answer_list').eq(0).length > 0 ? comment.parents('.ets_rv_answer_list').eq(0) : comment.parents('.ets_rv_comment_list').eq(0),
            dropdown = ele.parents('.comment_dropdown_action').eq(0),
            prop = comment.hasClass('question') ? 'question' : 'comment'
        ;
        if (!ele.hasClass('active') && !comment.hasClass('active')) {
            if (listComment.find('button.ets_rv_post_comment.update-comment').length > 0) {
                listComment.find('button.ets_rv_cancel_comment:visible').trigger('click');
            }
            $('.comment-list-item').removeClass('active');
            comment.addClass('active');
            ele.addClass('active');
            if (!ele.hasClass('json-loading')) {
                ele.addClass('json-loading');
                var commentList = $('#ets-rv-product-' + prop + 's-list');
                $.ajax({
                    url: commentList.data('comments-url'),
                    data: '__ac=comment&id_comment=' + comment.data('comment-id'),
                    type: 'get',
                    dataType: 'json',
                    success: function (json) {
                        ele.removeClass('json-loading');
                        if (json) {
                            listComment.find('.form-control-comment textarea[name^=comment_content]').val(function () {
                                var matches = $(this).attr('name').match(/comment_content_(\d+)/i);
                                if (matches && typeof matches[1] !== "undefined") {
                                    var idLang = matches[1];
                                    if (json.content.hasOwnProperty(idLang)) {
                                        return funcPCETS.br2n(json.content[idLang] ? json.content[idLang] : json.origin_content);
                                    }
                                } else {
                                    var idDefaultLang = parseInt($(this).parents('form').data('lang-default'));
                                    return funcPCETS.br2n(json.origin_content ? json.origin_content : (json.content && json.content[idDefaultLang] ? json.content[idDefaultLang] : ''));
                                }
                            });
                            if (dropdown.hasClass('employee')) {
                                if (listComment.find('.form-control-comment input.datetimepicker').length < 1)
                                    listComment.find('.form-control-comment .form-group.date_add').find('.input-group').append('<input id="date_add_' + Math.random().toString(36).slice(-8) + '" type="text" data-hex="true" class="datetimepicker" name="date_add" />');
                                listComment.find('.form-control-comment .form-group.date_add').removeClass('hide');
                                if (json.date_add !== '0000-00-00 00:00:00')
                                    listComment.find('.form-control-comment input.datetimepicker').val(json.date_add);
                                listComment.find('.form-control-comment input.datetimepicker:not(.hasDatepicker)').datetimepicker(ETS_RV_DATETIME_PICKER);
                            }

                            listComment.find('button.ets_rv_cancel_comment:hidden').show();
                            listComment.find('button.ets_rv_post_comment')
                                .addClass('update-comment')
                                .html(function () {
                                    return $(this).data('upd');
                                });
                            reCaptchaETS.reset(listComment.find('.form-control-comment'));
                            listComment.find('.ets_rv_form_comment:not(.active)').addClass('active');
                            listComment.find('textarea.ets_rv_comment').focus();

                        }
                    },
                    error: function () {
                        ele.removeClass('json-loading');
                        ele.removeClass('active');
                    }
                });
            }
        }
    });

    // Cancel.
    $(document).on('click', '.ets_rv_cancel_comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            listComment = ele.parents('.ets_rv_comment_list').eq(0).length > 0 ? ele.parents('.ets_rv_comment_list').eq(0) : ele.parents('.ets_rv_answer_list').eq(0),
            comment = listComment.find('.comment-list-item.active').eq(0),
            option = comment.hasClass('question') ? 'question' : 'comment'
        ;
        comment.removeClass('active');
        comment.find('.ets_rv_btn_edit.comment').removeClass('active');
        listComment.find('button.ets_rv_cancel_comment:visible').hide();
        listComment.find('button.ets_rv_post_comment')
            .removeClass('update-comment')
            .html(function () {
                return $(this).data('name');
            });
        listComment.find('textarea.ets_rv_comment').val('').focus();
        listComment.find('.form-group.date_add').addClass('hide');
        if (!ele.hasClass('show_answer_box') && !ele.hasClass('show_comment_box')) {
            ele.parents('.ets_rv_form_comment').removeClass('active');
        }
    });
    $(document).on('click', '.ets_rv_cancel_reply_comment', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            listReplyComment = ele.parents('.ets_rv_reply_comment_list').eq(0),
            replyComment = listReplyComment.find('.reply-comment-list-item.active').eq(0),
            option = replyComment.hasClass('question') ? 'question' : 'comment'
        ;
        replyComment.removeClass('active');
        replyComment.find('.ets_rv_btn_edit.reply-comment').removeClass('active');

        listReplyComment.find('button.ets_rv_cancel_reply_comment:visible').hide();
        listReplyComment.find('button.ets_rv_post_reply_comment')
            .removeClass('update-reply-comment')
            .html(function () {
                return $(this).data('name');
            });
        listReplyComment.find('textarea.ets_rv_reply_comment').val('').focus();
        listReplyComment.find('.form-group.date_add').addClass('hide');
        if (!ele.hasClass('show_reply_box')) {
            ele.parents('.ets_rv_form_reply_comment').removeClass('active');
        }
    });

    $(document).on('click', '#ets-rv-post-product-comment-form .ets_rv_btn_upload, form[id^=ets_rv_product_comment_form] .ets_rv_btn_upload, .ets_rv_form_upload_image .ets_rv_btn_upload', function () {
        $(this).parents('.ets_rv_upload_photo_item').find('input[type=file]').trigger('click');
    });

    $(document).on('change', '#ets-rv-post-product-comment-form input[type=file][name*=image],#ets-rv-post-product-comment-form input[type=file][name*=video], form[id^=ets_rv_product_comment_form] input[type=file][name*=image], form[id^=ets_rv_product_comment_form] input[type=file][name*=video],.ets_rv_form_upload_image input[type=file][name*=image],.ets_rv_form_upload_image input[type=file][name*=video]', function (event) {
        event.preventDefault();
        var ele = $(this),
            max_file_size = PS_ATTACHMENT_MAXIMUM_SIZE,
            ext_file = ele.val().split('.').pop().toLowerCase()
        ;
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
        if (ele.hasClass('video')) {
            fileExtension = ['mp4', 'webm', 'mov'];
        }
        if (fileExtension.indexOf(ext_file) === -1) {
            ele.val('');
            alert(file_not_valid_text);
        } else if (this.files && this.files[0] && this.files[0].size > max_file_size) {
            $(this).val('');
            alert(file_is_to_large_text.replace('%s', PS_ATTACHMENT_MAXIMUM_SIZE_TEXT));
        } else {
            var photos_wrap = ele.parents('.ets_rv_upload_photos'),
                maximum_of_photo = parseInt(photos_wrap.data('photos')),
                current_photo_wrap = ele.parents('.ets_rv_upload_photo_item').eq(0),
                count_photo = photos_wrap.find('.ets_rv_upload_photo_item').length,
                randomSize = Math.random().toString(36).slice(-8)
            ;
            if (maximum_of_photo > count_photo) {
                var addPhoto = current_photo_wrap.clone(true, true);
                addPhoto
                    .find('input[type=file]')
                    .val('')
                    .attr({
                        id: 'image_' + randomSize,
                        name: 'image[' + randomSize + ']'
                    });
                photos_wrap.append(addPhoto);
            }
            funcPCETS.readURL(this);

            // Upload image.
            if ($(this).parents('form').eq(0).hasClass('ets_rv_form_upload_image')) {
                var btn = $(this),
                    form = btn.parents('form'),
                    formFiles = form.find('input[type="file"]');
                if (!btn.hasClass('active')) {
                    btn.addClass('active');
                    var formData = new FormData(form.get(0));
                    // Safari fixed.
                    if (formFiles.length > 0) {
                        formFiles.each(function () {
                            if ($($(this).attr('id')).files.length == 0) {
                                formData.delete($(this).attr('id'));
                            }
                        });
                    }
                    $.ajax({
                        type: 'POST',
                        url: form.attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (json) {
                            btn.removeClass('active');
                            if (json) {
                                if (json.error)
                                    productCommentsETS.showErrorModal(json.error);
                                else if (json.errors) {
                                    productCommentsETS.showErrorModal(json.errors);
                                } else {
                                    btn.val('');
                                    if (json.msg)
                                        funcPCETS.showSuccessMessage(json.msg);
                                    if (json.images) {
                                        btn.parents('.ets_rv_upload_photo_item')
                                            .find('.ets_rv_btn_delete_photo')
                                            .attr({
                                                'data-product-comment-image-id': json.images.id,
                                                'data-product-comment-id': json.images.id_ets_rv_product_comment,
                                            });
                                    }
                                }
                            }
                        },
                        error: function () {
                            btn.removeClass('active');
                        }
                    });
                }
            }
        }

    });

    $(document).on('click', '#ets-rv-post-product-comment-form .ets_rv_btn_delete_photo, form[id^=ets_rv_product_comment_form] .ets_rv_btn_delete_photo, .ets_rv_form_upload_image .ets_rv_btn_delete_photo', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            formPost = $('#ets-rv-post-product-comment-form').length > 0 ? $('#ets-rv-post-product-comment-form') : $('form[id^=ets_rv_product_comment_form]'),
            url = formPost.attr('action');
        if (!ele.hasClass('active') && ele.data('product-comment-image-id') && url) {
            if (url.match(/(__ac=)/ig)) {
                url = url.replace(/(__ac=)([^\&\?]+)(&)?/ig, '$1delete_product_comment_image$3')
            } else {
                url += '&__ac=delete_product_comment_image'
            }
            ele.addClass('active');
            $.ajax({
                url: url,
                type: 'post',
                data: 'id_product_comment_image=' + ele.data('product-comment-image-id') + '&id_product_comment=' + ele.data('product-comment-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (json.error)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
        var photos_wrap = ele.parents('.ets_rv_upload_photos').eq(0),
            maximum_of_photo = parseInt(photos_wrap.data('photos')),
            photo_items = photos_wrap.find('.ets_rv_upload_photo_item'),
            selected_photo = ele.parents('.ets_rv_upload_photo_item').eq(0)
        ;
        if (photos_wrap.find('.ets_rv_upload_photo_item .ets_rv_upload_photo_wrap:not(.selected)').length <= 0) {
            var addPhoto = selected_photo.clone(true, true),
                randomSize = Math.random().toString(36).slice(-8)
            ;
            addPhoto
                .find('input[type=file]').val('')
                .attr({
                    id: 'image_' + randomSize,
                    name: 'image[' + randomSize + ']'
                })
            ;
            addPhoto
                .find('.ets_rv_upload_photo_wrap')
                .removeClass('selected')
                .css('background-image', '')
                .attr('title', '')
            ;
            photos_wrap.append(addPhoto);
        }
        selected_photo.remove();
    });
    $(document).on('click', '#ets-rv-post-product-comment-form .ets_rv_btn_delete_video, form[id^=ets_rv_product_comment_form] .ets_rv_btn_delete_video, .ets_rv_form_upload_video .ets_rv_btn_delete_video', function (ev) {
        ev.preventDefault();
        var ele = $(this),
            formPost = $('#ets-rv-post-product-comment-form').length > 0 ? $('#ets-rv-post-product-comment-form') : $('form[id^=ets_rv_product_comment_form]'),
            url = formPost.attr('action')
        ;
        selected_video = ele.parents('.ets_rv_upload_video_item').eq(0);

        if (!ele.hasClass('active') && ele.data('product-comment-video-id') && url) {
            if (url.match(/(__ac=)/ig)) {
                url = url.replace(/(__ac=)([^\&\?]+)(&)?/ig, '$1delete_product_comment_video$3')
            } else {
                url += '&__ac=delete_product_comment_video'
            }
            ele.addClass('active');
            $.ajax({
                url: url,
                type: 'post',
                data: 'id_product_comment_video=' + ele.data('product-comment-video-id') + '&id_product_comment=' + ele.data('product-comment-id'),
                dataType: 'json',
                success: function (json) {
                    ele.removeClass('active');
                    if (json) {
                        if (json.error)
                            productCommentsETS.showErrorModal(json.error);
                        else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);
                        }
                    }
                },
                error: function () {
                    ele.removeClass('active');
                }
            });
        }
        ele.parents('.ets_rv_upload_video_item').find('input[type=file]').val('');
        ele.parents('.ets_rv_upload_video_wrap').attr('title', '').removeClass('selected').parents('.ets_rv_upload_video_item').removeClass('cms_has_video');
        ele.parents('.ets_rv_upload_video_wrap').find('.ets_rv_video').html('');
        var copy_selected = selected_video.clone();
        selected_video.remove();
        $('.ets_rv_upload_videos').append(copy_selected);

    });
    $(document).on('click', '.ets_rv_edit_date', function (ev) {
        var btn = $(this),
            form = btn.prev('.comment-form');
        if (form.find('.datepicker').length > 0) {
            form.find('.datepicker').datepicker({
                prevText: '',
                nextText: '',
                dateFormat: 'yy-mm-dd',
            });
        }
        form.addClass('active');
        $('#ui-datepicker-div').css('z-index', '10000');
    });

    $(document).on('click', '.ets_rv_btn_edit_date', function (ev) {
        ev.preventDefault();
        var btn = $(this), urlPost = btn.attr('href');
        if (!btn.hasClass('active') && urlPost !== '') {
            btn.addClass('active');
            var requestData = {};
            if (btn.hasClass('product-comment')) {
                requestData.id_product_comment = btn.parents('.ets-rv-product-comment-list-item').data('product-comment-id');
            } else if (btn.hasClass('comment')) {
                requestData.id_comment = btn.parents('.comment-list-item').data('comment-id');
            } else {
                requestData.id_reply_comment = btn.parents('.reply-comment-list-item').data('reply-comment-id');
            }
            requestData.date_add = btn.parents('.comment-form').find('input[name=date_add]').val();
            $.ajax({
                url: urlPost,
                type: 'POST',
                data: requestData,
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            showErrorMessage(json.errors);
                        } else {
                            if (json.msg)
                                funcPCETS.showSuccessMessage(json.msg);
                            if (json.date_add) {
                                btn.parents('.comment-form').removeClass('active');
                                btn.parents('.ets_rv_form_date_add').find('.ets_rv_date_add').eq(0).text(json.date_add);
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

    $(document).keyup(function (e) {
        if (e.keyCode === 27) {
            $('.comment-form.active').removeClass('active');
        }
    });

    $(document).mouseup(function (e) {
        var commentForm = $('.comment-form.active'),
            datePicker = $('#ui-datepicker-div');

        if (!commentForm.is(e.target) && commentForm.has(e.target).length === 0 && (!datePicker.length || !datePicker.is(e.target) && datePicker.has(e.target).length === 0 && datePicker.css('display') === 'none')) {
            commentForm.removeClass('active');
        }
    });
    $(document).on('click', '.ets_rv_btn_show_answer', function () {
        $(this).toggleClass('active').parents('.ets-rv-product-comment-list-item.question').find('.ets_rv_answer_list.answer').stop().toggleClass('show_content');
    });
    $(document).on('click', '.nb-comment.question', function () {
        $(this).stop().toggleClass('active').parents('.ets-rv-product-comment-list-item.question').find('.ets_rv_comment_list').stop().toggleClass('show_content');
    });
});