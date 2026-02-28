/**
 * Loulou66
 * LpsTextBanner module for Prestashop
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php*
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
$(document).ready(function() {
    if ($('.lpsfixed').length > 0) {
        $('.header-banner').addClass('lpsfixed');
    }
    if ($('.lpstypewriter').length > 0) {
        var typed = new Typed('.lpstypewriterspan', {
            stringsElement: '.lpstypewritermessages',
            typeSpeed: 100,
            backSpeed: 100,
            startDelay: 1000,
            loop: true,
            loopCount: Infinity,
            smartBackspace: false,
        });
    }
    if ($('.lpshorizontal_slider').length > 0) {
        $('.lpshorizontal_slider').css('display', 'block');
        var displayTime = $('.lpshorizontal_slider').attr('data-displayTime');
        var directionH = $('.lpshorizontal_slider').attr('data-directionH');
        $('.lpshorizontal_slider').slick({
            arrows: false,
            dots: false,
            autoplay: true,
            infinite: true,
            autoplaySpeed: displayTime,
            speed: 1000,
            pauseOnHover: false,
            pauseOnFocus: false,
            rtl: (directionH == 'lefttoright') ? true : false
        });
    }
    if ($('.lpsvertical_slider').length > 0) {
        $('.lpsvertical_slider').css('display', 'block');
        var displayTime = $('.lpsvertical_slider').attr('data-displayTime');
        var directionV = $('.lpsvertical_slider').attr('data-directionV');
        $('.lpsvertical_slider').slick({
            arrows: false,
            dots: false,
            vertical: true,
            infinite: true,
            autoplay: true,
            autoplaySpeed: displayTime,
            speed: 1000,
            rtl: false,
            pauseOnHover: false,
            pauseOnFocus: false,
            verticalReverse: (directionV == 'toptobottom') ? true : false
        });
    }
});
