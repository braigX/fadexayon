<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Cache management for zonehomeblocks module
 */
function upgrade_module_6_3_4($module)
{
    $ret = true;
    if (strpos(Configuration::get('pagecache_product_a_mods'), 'zonehomeblocks') === false) {
        Configuration::updateValue('pagecache_product_a_mods',
            Configuration::get('pagecache_product_a_mods') . ' zonehomeblocks', false, null, null);
    }
    if (!Configuration::get('pagecache_product_refreshEveryX')) {
        Configuration::updateValue('pagecache_product_refreshEveryX', 1);
    }

    return (bool) $ret;
}
