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

function upgrade_module_5_4_0($object)
{
    Configuration::deleteByName('ADP_RICH_SNIPPETS_TS_COD');

    return true;
}
