/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

jQuery(function() {
    jQuery('[id^=table-shipment_]').parent().hide(0);
    jQuery('[id^=table-shipment_]').parent().parent().find('row').hide(0);
    jQuery('[id^=table-shipment_]').parent().parent().css({
        padding: '0 4px 0 4px'
    });
    jQuery('[id^=table-shipment_]').parent().parent().find('h3').css({
        padding: 0,
        margin: 0,
        'padding-left': '10px',
        'border-bottom': 'none'
    });
    jQuery('[id^=table-shipment_]').parent().parent().find('h3').text(geodisOrderGridShow);
    jQuery('[id^=table-shipment_]').parent().parent().find('h3').css({
        cursor: 'pointer'
    });
    jQuery('[id^=table-shipment_]').parent().parent().find('h3').click(function() {
        if (jQuery(this).parent().find('[id^=table-shipment_]').parent().is(':visible')) {
            jQuery(this).parent().find('[id^=table-shipment_]').parent().hide(300);
            jQuery(this).parent().find('[id^=table-shipment_]').parent().parent().find('h3').text(geodisOrderGridShow);
        } else {
            jQuery(this).parent().find('[id^=table-shipment_]').parent().show(300);
            jQuery(this).parent().find('[id^=table-shipment_]').parent().parent().find('h3').text(geodisOrderGridHide);
        }

        return false;
    });

    jQuery('td.pointer.shipment_status').each(function(index, element) {
        var isComplete = jQuery(element).parent().find('.action-enabled');
        if(isComplete.length) {
            jQuery(element).parent().find('.dropdown-toggle').remove();
            jQuery(element).parent().find('.dropdown-menu').remove();
        }
    });
});
