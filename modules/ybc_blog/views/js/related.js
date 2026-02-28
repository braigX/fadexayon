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
    if(!$('#category .ybc_block_related_category_page').length){
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: '',
            async: true,
            cache: false,
            dataType : "json",
            data:'displayPostRelatedCategories=1',
            success: function(jsonData)
            {
                if($('#category #main').length)
                {
                    $('#category #main').append(jsonData.html_block);
                }
                else if($('#category #center_column').length)
                {
                    $('#category #center_column').append(jsonData.html_block);
                }
                slickBlogRelated();
            }
        });
    } else {
        slickBlogRelated();
    }
 });
 function slickBlogRelated(){
     if ($('.ybc_block_related_category_page.ybc_block_slider .ybc_blog_content_block').length > 0){
         var number_category_posts_per_row = $('.ybc_block_related_category_page').attr('data-items');
         console.log(number_category_posts_per_row);
         $('.ybc_block_related_category_page.ybc_block_slider .ybc_blog_content_block').slick({
             slidesToShow: number_category_posts_per_row,
             arrows: true,
             dots: false,
             autoplay: false,
             responsive: [
                 {
                     breakpoint: 1200,
                     settings: {
                         slidesToShow: number_category_posts_per_row,
                     }
                 },
                 {
                     breakpoint: 992,
                     settings: {
                         slidesToShow: 3,
                     }
                 },
                 {
                     breakpoint: 768,
                     settings: {
                         slidesToShow: 2,
                     }
                 },
                 {
                     breakpoint: 400,
                     settings: {
                         slidesToShow: 1,
                     }
                 }
             ]
         });
     }
 }