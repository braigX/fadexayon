/**
 * 2007-2018 PrestaShop
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
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2022 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var MessageViewer = {
    showError: function (message)
    {
        MessageViewer.init();
        MessageViewer.show(message, 'error');
    },
    showSuccess: function (message)
    {
        MessageViewer.init();
        MessageViewer.show(message, 'success');
    },
    showInfo: function (message)
    {
        MessageViewer.init();
        MessageViewer.show(message, 'info');
    },
    setCenterPosAbs : function (elem)
    {
        var offsetElemTop = 20;
        var scrollTop = $(document).scrollTop();
        var elemWidth = $(elem).width();
        var windowWidth = $(window).width();
        $(elem).css({
            top: ($(elem).height() > $(window).height() ? scrollTop + offsetElemTop : scrollTop + (($(window).height()-$(elem).height())/2)),
            left: ((windowWidth-elemWidth)/2)
        });
    },
    init: function ()
    {
        if (!$('.stage_mv').length)
        {
            $('body').prepend('<div class="message_mv" style="display: none;">' +
                '<div class="tn-box tn-box_success message_mv_content">' +
                '</div>' +
            '</div>');
            $('.stage_mv, .message_mv').live('click', function () {
                MessageViewer.reset();
            });
            $(window).resize(function (){
                MessageViewer.setCenterPosAbs('.message_mv');
            });
        }
    },
    hide: function ()
    {
        $('.stage_mv, .message_mv').hide();
        MessageViewer.reset();
        $('.message_mv').removeClass('mv_error mv_success mv_info');
        $('.message_mv_content').html('');
    },
    reset: function () {
        $('.stage_mv, .message_mv').fadeOut(300);
        $('.tn-box_success').removeClass('tn-box-active');
    },
    show: function (message, type)
    {
        $('.message_mv').removeClass('mv_error mv_success mv_info').addClass('mv_' + type);
        $('.message_mv_content').html(message);
        $('.stage_mv, .message_mv').stop(true, true).fadeIn(300);
        MessageViewer.setCenterPosAbs('.message_mv');
        $('.tn-box_success').addClass('tn-box-active');
        setTimeout(function() {
            $('.stage_mv').fadeOut(300);
            $('.tn-box_success').removeClass('tn-box-active');
        }, 5000);
    }
};