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
 * Remove Context override and enable HTML minification
 */
function upgrade_module_10_0_0($module)
{
    $moduleOverride = _PS_MODULE_DIR_ . $module->name . '/override/classes/Context.php';
    if (file_exists($moduleOverride)) {
        $module->removeOverride('Context');
        @unlink($moduleOverride);
    }

    // Set HTML minification to disabled by default to encourage testing before activation
    Configuration::updateValue('pagecache_minifyhtml', false, 0, 0);

    // The user must enable it manually to avoid any problem
    JprestaUtils::saveConfigurationAllShop('SPEED_PACK_AVIF_ENABLE', false);
    JprestaUtils::saveConfigurationAllShop('SPEED_PACK_AVIF_QUALITY', 55);

    // It does not matter if it fails
    return true;
}
