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
    if (typeof isformconfig !== 'undefined') {
        displayhide('display_banner');
    }
    if (typeof isformmessage !== 'undefined') {
        displayhide('display_link');
    }
    $('.speedScroll_range').each(function() {
        $(this).on('change', () => {
            $(this).parent().parent().find('.rangevalue').html($(this).val());
        });
    });
    $('.displayTime_range').each(function() {
        $(this).on('input change', () => {
            $(this).parent().parent().find('.rangevalue').html($(this).val());
        });
    });
    var transition_effect = $('#transition_effect').val();
    if (transition_effect == 'scrolling') {
        $('.displayWarnigSlider').hide();
        $('.directionH').hide();
        $('.directionV').hide();
        $('.speedScroll').show();
        $('.displayTime').hide();
    }
    if (transition_effect == 'typewriter') {
        $('.displayWarnigSlider').hide();
        $('.directionH').hide();
        $('.directionV').hide();
        $('.speedScroll').hide();
        $('.displayTime').hide();
    }
    if (transition_effect == 'horizontal_slider') {
        $('.displayWarnigSlider').show();
        $('.directionH').show();
        $('.directionV').hide();
        $('.speedScroll').hide();
        $('.displayTime').show();
    }
    if (transition_effect == 'vertical_slider') {
        $('.displayWarnigSlider').show();
        $('.directionH').hide();
        $('.directionV').show();
        $('.speedScroll').hide();
        $('.displayTime').show();
    }
    $('#transition_effect').change(function() {
        var transition_effect = $(this).val();
        if (transition_effect == 'scrolling') {
            $('.displayWarnigSlider').slideUp();
            $('.directionH').slideUp();
            $('.directionV').slideUp();
            $('.speedScroll').slideDown();
            $('.displayTime').slideUp();
        }
        if (transition_effect == 'typewriter') {
            $('.displayWarnigSlider').slideUp();
            $('.directionH').slideUp();
            $('.directionV').slideUp();
            $('.speedScroll').slideUp();
            $('.displayTime').slideUp();
        }
        if (transition_effect == 'horizontal_slider') {
            $('.displayWarnigSlider').slideDown();
            $('.directionH').slideDown();
            $('.directionV').slideUp();
            $('.speedScroll').slideUp();
            $('.displayTime').slideDown();
        }
        if (transition_effect == 'vertical_slider') {
            $('.displayWarnigSlider').slideDown();
            $('.directionH').slideUp();
            $('.directionV').slideDown();
            $('.speedScroll').slideUp();
            $('.displayTime').slideDown();
        }
    });
});
function displayhide(switcher) {
    var switcher_on = document.getElementById(switcher+'_on');
    var switcher_off = document.getElementById(switcher+'_off');
    if (switcher_off.checked) {
        $('.'+ switcher).hide('fast');
    }
    $('input[name="'+switcher+'"]').click(function() {
        if (switcher_on.checked) {
           $('.'+ switcher).fadeIn('fast');
        }
        if (switcher_off.checked) {
            $('.'+ switcher).fadeOut('fast');
        }
    });
}
