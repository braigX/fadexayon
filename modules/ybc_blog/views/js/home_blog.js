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
$(document).ready(function(){
    runowl();
});
$(document).on('hooksLoaded',function(){
    runowl();
});
function runowl()
{
    if ( $('.ybc_blog_rtl_mode').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }
    if ($('.page_home.ybc_block_slider .blog_type_slider').length > 0) {
        $('.page_home.ybc_block_slider .blog_type_slider').slick({
            dots: false,
            infinite: false,
            speed: 300,
            arrows: true,
            slidesToShow: number_home_posts_per_row,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_home_posts_per_row,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
    }
 if ($('.page_home_gallery.ybc_block_slider .blog_type_slider').length > 0)
     $('.page_home_gallery.ybc_block_slider .blog_type_slider').slick({
         slidesToShow: 6,
         arrows: true,
         rtl: rtl_blog,
         dots: false,
         autoplay: false,
         responsive: [
             {
                 breakpoint: 1200,
                 settings: {
                     slidesToShow: 6,
                 }
             },
             {
                 breakpoint: 992,
                 settings: {
                     slidesToShow: 5,
                 }
             },
             {
                 breakpoint: 768,
                 settings: {
                     slidesToShow: 4,
                 }
             },
             {
                 breakpoint: 400,
                 settings: {
                     slidesToShow: 3,
                 }
             }
         ]
     });
 }