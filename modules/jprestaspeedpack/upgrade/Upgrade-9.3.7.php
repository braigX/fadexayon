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
 * Set SPEED_PACK_WEBP_EXCLUDE_PATHS and fix static cache configuration
 */
function upgrade_module_9_3_7($module)
{
    if (!JPresta\SpeedPack\JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_CONVERT_UPLOAD_DIR', 0)) {
        JPresta\SpeedPack\JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', '/upload/');
    } else {
        JPresta\SpeedPack\JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', '');
    }
    Configuration::deleteByName('SPEED_PACK_WEBP_CONVERT_UPLOAD_DIR');

    // Re-generate the static cache configuration
    $module->installCache();

    return true;
}
