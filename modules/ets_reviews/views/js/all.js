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
$(document).ready(function () {
    $(document).on('click', '.ets-rv-images-videos .ets_rv_image_item > .ets_rv_fancy', function (e) {
        if ($(this).parents('.slick-slide').length <= 0) {
            e.preventDefault();
            var content_slider = $(this).parents('.ets_rv_images').clone(),
                value_active = $(this).attr('data-value') - 1
            ;
            $(this).closest('.ets_rv_all_reviews').find('.ets_popup_content').append(content_slider);
            $('.ets_popup_content .ets_rv_fancy').each(function () {
                $(this).css('background-image', 'url(' + $(this).attr('href') + ')');
            });
            $(this).closest('.ets_rv_all_reviews').find('.ets_image_list_popup').addClass('active').addClass('blur_content');
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
});