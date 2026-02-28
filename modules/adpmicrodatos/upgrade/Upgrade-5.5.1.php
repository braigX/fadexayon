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

function upgrade_module_5_5_1($object)
{
    Configuration::updateValue('ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE', '0');
    Configuration::updateValue('ADP_IDS_PRODUCTS_WITHOUT_MICRODATA', '');
    Configuration::updateValue('ADP_IDS_MANUFACTURERS_WITHOUT_MICRODATA', '');
    Configuration::updateValue('ADP_IDS_CATEGORIES_WITHOUT_MICRODATA', '');

    return true;
}
