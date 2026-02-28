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
    $(document).on('click','#sidebar ul li a',function(e) {
        e.preventDefault();
        $('#sidebar ul li a').removeClass('active')
        $(this).addClass('active')
        var tabactive = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(tabactive).offset().top - 10
        }, 2000);
    });
    $(window).scroll(function() {
        var scroll_top = $(window).scrollTop();
        if (scroll_top >= 100) {
            $('.section').each(function(i) {
                if ($(this).position().top <= scroll_top + 30) {
                    $('#sidebar ul li a.active').removeClass('active');
                    $('#sidebar ul li a').eq(i).addClass('active');
                }
            });
        } else {
            $('#sidebar ul li a.active').removeClass('active');
            $('#sidebar ul li:first-child a').addClass('active');
        }
    });
});