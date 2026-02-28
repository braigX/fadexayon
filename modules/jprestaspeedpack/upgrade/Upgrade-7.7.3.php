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

/**
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_7_3($module)
{
    $ret = true;
    if (strpos(Configuration::get('pagecache_product_a_mods'), 'posnewproduct') === false) {
        Configuration::updateValue('pagecache_product_a_mods', Configuration::get('pagecache_product_a_mods') . ' posnewproduct', false, null, null);
    }

    return (bool) $ret;
}
