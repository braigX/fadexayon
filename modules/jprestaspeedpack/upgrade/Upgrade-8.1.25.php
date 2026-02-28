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
 * Update cache directory
 */
function upgrade_module_8_1_25($module)
{
    if (JprestaUtils::startsWith(_PS_CACHE_DIR_, _PS_ROOT_DIR_ . '/var/cache/')) {
        // Prestashop clears the cache everytime a module is enabled/disabled and we don't want this behavior.
        // Also having different cache for DEV and PROD is not needed for this module.
        $currentCachedir = realpath(_PS_ROOT_DIR_ . '/var/cache/' . Jprestaspeedpack::PAGECACHE_DIR);
    } else {
        $currentCachedir = realpath(_PS_CACHE_DIR_ . Jprestaspeedpack::PAGECACHE_DIR);
    }

    $type = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
    try {
        $newCachedir = realpath($currentCachedir . '/' . $type);
        if (is_dir($currentCachedir) && !file_exists($newCachedir)) {
            rename($currentCachedir, $newCachedir);
            $blocksDir = $newCachedir . '/widget_blocks';
            if (is_dir($blocksDir)) {
                rename($blocksDir, $currentCachedir . '/widget_blocks');
            }
        }
    } catch (Exception $e) {
        JprestaUtils::addLog('Upgrade 8.1.25 | Cannot move the old cache directory, but you can do it manually by deleting all files in ' . $currentCachedir . ' except "widget_blocks" directory');
    }

    if ($type === 'static') {
        // Re-create static PHP file
        $module->uninstallCache($type);
        $module->installCache();
    }

    return true;
}
