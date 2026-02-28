<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Refresh static cache configuration
 */
function upgrade_module_8_8_45($module)
{
    $typecache = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
    if ($typecache === 'static') {
        $module->uninstallCache($typecache);
        $module->installCache();
    }

    return true;
}
