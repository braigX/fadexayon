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
 * Update static cache file
 */
function upgrade_module_8_7_0($module)
{
    $type = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
    if ($type === 'static') {
        // Re-create static PHP file
        $module->uninstallCache($type);
        $module->installCache();
    }

    return true;
}
