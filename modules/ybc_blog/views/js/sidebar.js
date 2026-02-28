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
function displayListBlogCategory()
{
    if($('.ybc_block_categories li.active').length>0)
    {
        $('.ybc_block_categories li.active').closest('.children').addClass('show');
        $('.ybc_block_categories li.active').closest('.children').parent().find('.category-blog-parent').addClass('active');
    }
}
function runowlSidebar()
{
    if ( $('.ybc_blog_rtl_mode').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }
    if ($('.ybc_blog_sidebar .page_blog.ybc_block_slider .blog_type_slider').length > 0)
        $('.ybc_blog_sidebar .page_blog.ybc_block_slider .blog_type_slider').slick({
            dots: false,
            infinite: false,
            speed: 300,
            arrows: true,
            slidesToShow: 1,
            rtl: rtl_blog,
        });

    if ($('.ybc_blog_sidebar .page_blog_gallery.ybc_block_slider .blog_type_slider').length > 0)
        $('.ybc_blog_sidebar .page_blog_gallery.ybc_block_slider .blog_type_slider').slick({
            dots: false,
            infinite: false,
            speed: 300,
            arrows: true,
            slidesToShow: 3,
            rtl: rtl_blog,
        });

}
$(document).on('hooksLoaded',function(){
    runowlSidebar();
});
$(document).ready(function() {
    runowlSidebar();
    $(document).on('click','.ybc-navigation-blog',function(){
        $(this).toggleClass('active');
        $('.ybc-navigation-blog-content').toggleClass('show');
    });
    $(document).on('click', '.axpand_button', function () {
        if ($(this).hasClass('closed')) {
            $(this).next('.list-months').removeClass('hidden');
            $(this).removeClass('closed').addClass('opened');
        } else {
            $(this).next('.list-months').addClass('hidden');
            $(this).removeClass('opened').addClass('closed');
        }
    });
    $(document).on('click','.category-blog-parent',function(){
        $(this).next().toggleClass('show');
        $(this).toggleClass('active');
    });
    displayListBlogCategory();
});