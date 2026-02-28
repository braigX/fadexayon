<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_1($object)
{
    Configuration::deleteByName('ADP_ACTIVE_MICRODATA_COMBINATIONS_PRODUCT');

    Configuration::updateValue('ADP_PAGES_WITHOUT_MICRODATA', 'cart,checkout,my-account,myaccount,register,history,guest_tracking,order,order-detail,order-confirmation,order_detail,order_follow,order_return,order-slip,orderslip,identity,address,addresses,password,authentication,order-opc,orderopc,pdf_invoice,pdf_order_return,pdf_order_slip,order_login,pagenotfound');

    Configuration::updateValue('ACTIVE_MICRODATA_SHIPPING_DETAILS', 0);
    Configuration::updateValue('ADP_SHIPPING_DETAILS_SHIPPING_RATE', 0);
    Configuration::updateValue('ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY', 'ES');
    Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN', 0);
    Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX', 0);
    Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN', 0);
    Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX', 0);

    Configuration::updateValue('ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION', '{id_product}-{id_product_combination}');

    $object->registerHook('displayBackOfficeHeader');

    return true;
}
