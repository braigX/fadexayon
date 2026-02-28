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

function upgrade_module_4_3_1($object)
{
    Configuration::updateValue('ADP_ACTIVE_MICRODATA_REFUND_POLICY', '0');
    Configuration::updateValue('ADP_RETURN_POLICY_CATEGORIES', '');
    Configuration::updateValue('ADP_MERCHANT_RETURN_DAYS', '30');
    Configuration::updateValue('ADP_RETURN_METHOD', '');
    Configuration::updateValue('ADP_RETURN_FEES', '');
    Configuration::updateValue('ADP_APLICABLE_COUNTRY', strtoupper(Language::getIsoById(Context::getContext()->language->id)));
    Configuration::updateValue('ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT', '0');

    return true;
}
