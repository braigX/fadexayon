/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function () {
    $('body').on('click', '#front_seo .seo-grp', function(){
        $('#front_seo').toggleClass('active');
        if ($(window).width() < 992) {
            $('body').toggleClass('seo_locked');
        }
    });

    $('body').on('click', '.seo-onglet:not(.active)', function(){
        $('.seo-content').removeClass('active');
        $('#'+$(this).data('toggle')).addClass('active');
        $('.seo-onglet').removeClass('active');
        $(this).addClass('active');
    });

    $('body').on('click', '.seo-mask-mobile', function(){
        $('#front_seo .seo-grp').trigger('click');
    });


    notation_it = 1;
    $('.seo-notation .inner-notation').each(function(){
        note = parseInt($(this).text());
        note_color = 'green';
        if (note <= 33) {
            note_color = 'red';
        } else if(note <= 66) {
            note_color = 'orange';
        }
        $(this).attr('id','inner-notation'+notation_it);
        var bar = new ProgressBar.Circle('#inner-notation'+notation_it, {
            strokeWidth: 12,
            easing: 'easeInOut',
            duration: 1400,
            color: note_color,
            trailColor: '#eee',
            trailWidth: 12,
            svgStyle: null
        });
        notation_it++;
        bar.animate(note/100);
    });


    

});