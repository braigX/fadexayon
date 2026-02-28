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

var ets_rv_fo = {
    readURL: function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#ets_rv_avatar_thumbnail > img')
                    .attr({'src': e.target.result, 'title': input.files[0].name})
                    .css('max-width', '110px')
                    .addClass('image-loading')
                ;
            };
            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    },
    copyToClipboard: function (el) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(el.text()).select();
        document.execCommand("copy");
        $temp.remove();
        el.parent().addClass('copied');
        //showSuccessMessage(ets_rv_copied_translate);
        setTimeout(function () {
            el.removeClass('copy');
            el.parent().removeClass('copied');
        }, 1500);
    },
    isSafari: function () {
        var e = navigator, c = e.userAgent, g = e.vendor;
        return (!(/(Chrome|CriOS)\s*\/\s*(\d[\d\.]*)/i).test(c) && ((/Apple/i).test(g) || !g) && (/Safari\s*\/\s*(\d[\d\.]*)/i).test(c));
    }
};
var ets_rv_op = {
    initialize: function () {
        if ($('body[id=identity]').length > 0 && $('#customer-form input[name=new_password], #identity input[name=new_password]').length > 0 && $('.ets_rv_upload_avatar').length > 0) {
            var form_field = $('.ets_rv_upload_avatar .form-group').clone(true);
            $('#customer-form input[name=new_password], #identity input[name=new_password]').parents('.form-group').after(form_field);
            $('.ets_rv_upload_avatar').remove();
        }
    },
};
$(document).ready(function () {

    if ( $('.product-quantity .ets-rv-product-comments-additional-info').length > 0 ){
        var review_under_addcart = $('.product-quantity .ets-rv-product-comments-additional-info').clone();
        $('.product-quantity .ets-rv-product-comments-additional-info').remove();
        $('.product-quantity').after(review_under_addcart);
    }
    ets_rv_op.initialize();
    $(document).on('hooksLoaded', function () {
        ets_rv_op.initialize();
    });

    if (typeof funcPCETS !== "undefined") {
        var avatar = $('.ets_rv_author_avatar.gene-color');
        if (avatar.length > 0)
            avatar.css('background-color', funcPCETS.colorHex(avatar.data('profile')));
    }

    var star_content = $('.product-list-reviews .ets-rv-star-content');
    if (star_content.length > 0) {
        star_content.each(function () {
            $(this).parents('._thumb-container').addClass('hasreview');
        });
    }

    $(document).on('click', '.extra_videos_show', function () {
        $(this).parents('.multi_videos,.ets_rv_videos').addClass('show_videos');

    });
    $(document).on('click', 'a.link-comment.ets-rv-btn-read-user', function (e) {
        e.preventDefault();
        var scrolldivto = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(scrolldivto).offset().top - 150
        }, 500);
        $('.ets_rv_tab_reviews').trigger('click');
    });

    if ($('body#comment').length > 0 || $('body[id$=comment]').length > 0) {
        productCommentsETS.paginate({page: 1}, $('.ets_rv_comments_wrap.question').length > 0 ? 'question' : 'comment');
        if ($('.ets_rv_comments_wrap').length > 0) {
            $('.ets_rv_btn_show_answer, .nb-comment.question').addClass('active');
            $('.ets_rv_answer_list.answer, .ets_rv_comment_list').addClass('show_content');
        }
    }
    $(document).on('click', '.ets_rv_voucher_box.code', function () {
        ets_rv_fo.copyToClipboard($(this));
    });
    $(document).on('keyup', 'body', function (e) {
        if ($('.ets-rv-product-comment-modal textarea.error').length > 0) {
            var textarea_content = $('textarea.error').val();
            if (textarea_content.length > 0) {
                $('textarea.error').removeClass('error');
            }
        }
        if ($('.ets-rv-product-comment-modal input.error').length > 0) {
            var input_content = $('input.error').val();
            if (input_content.length > 0) {
                $('input.error').removeClass('error');
            }
        }
    });
    $(document).on('click', '#avatar-selectbutton', function () {
        $('#customer-form input[name=avatar], #identity input[name=avatar]').trigger('click');
    });
    $(document).on('change', '#customer-form input[name=avatar], #identity input[name=avatar]', function () {
        var _thumb = $('#ets_rv_avatar_thumbnail'),
            form = _thumb.parents('form'),
            image = _thumb.find('img')
        ;
        if (!_thumb.hasClass('loading') && $(this).val()) {
            _thumb.addClass('loading');
            $('#avatar-name').val(this.files[0].name);
            if (!_thumb.find('img').length > 0) {
                _thumb.append('<img/><span class="ets_rv_delete_avatar" title="' + _thumb.data('btn-delete-title') + '"><i class="fa fa-trash"> </i></span>');
            }
            ets_rv_fo.readURL(this);
            var formData = new FormData(form[0]);
            if (form.find('input[type="file"]').length > 0 && ets_rv_fo.isSafari()) {
                form.find('input[type="file"]').each(function () {
                    if (document.getElementById($(this).attr('id')).files.length === 0) {
                        formData.delete($(this).attr('id'));
                    }
                });
            }

            formData.append('ajax', 1);
            formData.append('action', 'uploadProfileImage');

            $.ajax({
                url: _thumb.data('upload-url'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (json) {
                    _thumb.removeClass('loading');
                    if (json) {
                        image.removeClass('image-loading');
                        if (json.errors) {
                            if (!$('.ets_rv_profile_image .ets_rv_error').length > 0) {
                                $('.ets_rv_profile_image').append('<div class="bootstrap"><div class="ets_rv_error alert alert-danger"></div></div>');
                            }
                            $('.ets_rv_profile_image .ets_rv_error').html(json.errors);
                            image.hide();
                            $('.post-comment-buttons button, .post-question-buttons button').removeClass('active');
                        } else {
                            if (json.msg)
                                $.growl.notice({title: "", message: json.msg});
                            if ($('.ets_rv_profile_image .bootstrap').length > 0) {
                                $('.ets_rv_profile_image .bootstrap').hide();
                                image.show();
                            }
                            form.find('#avatar-name, #avatar').val('');
                        }
                    }
                },
                error: function () {
                    _thumb.removeClass('loading');
                    $('.post-comment-buttons button, .post-question-buttons button').removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.ets_rv_delete_avatar', function () {
        var btn = $(this), _thumb = $('#ets_rv_avatar_thumbnail');
        if (!btn.hasClass('active') && _thumb.data('upload-url') !== '') {
            btn.addClass('active');
            $.ajax({
                url: _thumb.data('upload-url'),
                type: 'POST',
                data: 'ajax=1&action=deleteProfileImage',
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.errors) {
                            if (!$('.ets_rv_profile_image .ets_rv_error').length > 0) {
                                $('.ets_rv_profile_image').append('<div class="bootstrap"><div class="ets_rv_error alert alert-danger"></div></div>');
                            }
                            $('.ets_rv_profile_image .ets_rv_error').html(json.errors);
                        } else {
                            if (json.msg)
                                $.growl.notice({title: "", message: json.msg});
                            _thumb.html('');
                            $('#avatar-name').val('');
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
});