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
 * Fix pagecache_product_refresh_every_x length name and .htaccess for WEBP
 */
function upgrade_module_6_3_13($module)
{
    $ret = true;
    Configuration::updateValue('pagecache_product_refreshEveryX', Configuration::get('pagecache_product_refresh_every_x', null, null, null, 1));

    // SPEEDPACK
    $module->jpresta_submodules['JprestaWebpModule']->updateHtaccessFile();
    // SPEEDPACK£

    return (bool) $ret;
}
