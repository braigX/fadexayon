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
 var height_index_heading = 100;


$(document).on('hooksLoaded',function(){
    runowl();
});
$(document).ready(function(){
    if($('.bybc-blog-slider .lazyload').length)
    {
        $(document).scrollTop(100);
    }

    $(document).on('change','#form_blog input[type="file"],.blog-managament-information input[type="file"]',function(){
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert(ybc_blog_invalid_file);
        }
        else
        {
            var file_name = $(this).val().replace('C:\\fakepath\\','');
            if($(this).closest('.upload_form_custom').find('.file_name').length)
            {
                $(this).closest('.upload_form_custom').find('.file_name').html(file_name);
            }
            else   
                $(this).closest('.upload_form_custom').append('<span class="file_name">'+file_name+'</span>'); 
            readURL(this);            
        }
        if($(this).next('.file_name').length>0)
        {
           $(this).next('.file_name').html($(this).val().replace('C:\\fakepath\\','')) 
        }
    });
    if ( $('.ybc-blog-rtl').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }

    function loadLazi(){
        if($('img.lazyload').length>0)
        {
            $('img.lazyload').lazyload({
                load : function () {
                    $(this).parent().removeClass('ybc_item_img_ladyload');
                    $(this).parent().parent().removeClass('ybc_item_img_ladyload');
                    $(this).addClass('isload');
                },
                threshold: 100,
            });
        }
    }
    $( window ).load(function() {
      loadLazi();
    });
    loadLazi();

    $(document).on('click','.owl-next',function(){
        if($('img.lazyload').length>0)
        {
            $('img.lazyload').lazyload();
        }
    });
    $(window).scroll(function(){
         autoLoadBlog();
    });
    runowl();
    autoLoadBlog();
});
function autoLoadBlog()
{
    var container = '.ybc-blog-wrapper-blog-list.loadmore';
    if ($(container).length > 0 && $(container+" .blog-paggination a.next").length > 0 && !$(container+" .blog-paggination a.next").hasClass('active') && $(window).scrollTop() + $(window).height() >= $(container).offset().top + $(container).height() ) {
        $(container+" .blog-paggination a.next").addClass('active');
          $('.ets_blog_loading.autoload').addClass('active');
          $.ajax({
                url:  $(container+" .blog-paggination a.next").attr('href'),
                data: 'loadajax=1'+($('select[name="ybc_sort_by_posts"]').length ? '&ybc_sort_by_posts='+$('select[name="ybc_sort_by_posts"]').val():''),
                type: 'post',
                dataType: 'json',                
                success: function(json){
                    if(json.list_blog)
                        $('.ybc-blog-list').append(json.list_blog);
                    if(json.list_galleries)
                    {
                        $('.ybc-galley-list').append(json.list_galleries);
                        ybcBlogPrettyPhoto();
                    }
                    $('.blog-paggination').html(json.blog_paggination);
                    if($('img.lazyload').length>0)
                    {
                        $('img.lazyload').lazyload({
                            load : function () {
                                $(this).parent().removeClass('ybc_item_img_ladyload');
                                $(this).parent().parent().removeClass('ybc_item_img_ladyload');
                            },
                            threshold: 100,
                        });
                    }
                    $('.ets_blog_loading.autoload').removeClass('active');
                },
                error: function(error)
                {
                   
                }
        });         
    } 
}
function runowl()
{
    if ( $('.ybc_blog_rtl_mode').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }

    if ($('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').length > 0)
    {
        var number_post_related_per_row = $('.ybc-blog-related-posts').attr('data-items');
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').slick({
            slidesToShow: number_post_related_per_row,
            arrows: true,
            rtl: rtl_blog,
            dots: false,
            autoplay: false,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_post_related_per_row,
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
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
    if ($('.ybc_related_products_type_carousel.ybc_blog_carousel').length > 0)
    {
        $('.ybc_related_products_type_carousel.ybc_blog_carousel').slick({
            slidesToShow: 4,
            arrows: true,
            rtl: rtl_blog,
            dots: false,
            autoplay: false,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
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
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
    if ($('.ybc_blog_related_posts_type_carousel .blog_type_slider').length > 0)
    {
        $('.ybc_blog_related_posts_type_carousel .blog_type_slider').slick({
            dots: false,
            infinite: false,
            speed: 300,
            arrows: true,
            slidesToShow: number_post_related_per_row,
            slidesToScroll: 4,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_post_related_per_row,
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
        $('.ybc_blog_related_posts_type_carousel .blog_type_slider').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if($(input).closest('.col-md-9').find('.thumb_post').length <= 0)
            {
                $(input).closest('.col-md-9').append('<div class="thumb_post"><img src="'+e.target.result+'"/> </div>');
            }
            else
            {
                $(input).closest('.col-md-9').find('.thumb_post img').eq(0).attr('src',e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}


function addLazyLoadAfterSlider(){
    if( $('.slick-cloned .ybc_item_img_ladyload .isload').length ){
        $('.slick-cloned .ybc_item_img_ladyload .isload').parent().removeClass('ybc_item_img_ladyload');
    }
    $('img.lazyload').each(function(){
        $(this).lazyload({
        threshold: 100,
        afterLoad      : function (element) {
			$(this).parent().removeClass('ybc_item_img_ladyload');
            $(this).parent().parent().removeClass('ybc_item_img_ladyload');
            $(this).addClass('isload');
		}
    });
    });
}


