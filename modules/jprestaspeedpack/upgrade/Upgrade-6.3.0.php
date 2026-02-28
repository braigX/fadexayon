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

/*
 * Set default configuration for cache key concerning taxes
 */
function upgrade_module_6_3_0($module)
{
    $ret = true;
    if (Configuration::get('PS_GEOLOCATION_ENABLED') && JprestaUtils::version_compare(_PS_VERSION_, '1.6.0.12', '<')) {
        $module->upgradeOverride('FrontController');
    }

    return (bool) $ret;
}
