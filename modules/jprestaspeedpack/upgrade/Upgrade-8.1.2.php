<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_8_1_2($module)
{
    $ret = true;

    $typecache = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
    if ($typecache === 'static') {
        $module->uninstallCache($typecache);
        $module->installCache();
        $module->clearCache();
    }

    @unlink(_PS_MODULE_DIR_ . $module->name . '/views/js/pagecache-v8-1.js');

    return (bool) $ret;
}
