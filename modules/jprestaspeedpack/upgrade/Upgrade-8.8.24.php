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
function upgrade_module_8_8_24($module)
{
    $ret = true;
    if (strpos(Configuration::get('pagecache_product_a_mods'), 'newsellerincategory') === false) {
        Configuration::updateValue('pagecache_product_a_mods', Configuration::get('pagecache_product_a_mods') . ' newsellerincategory', false, null, null);
    }

    return (bool) $ret;
}
