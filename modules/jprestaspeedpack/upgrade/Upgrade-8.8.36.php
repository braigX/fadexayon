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
 * Fix profiling configuration
 */
function upgrade_module_8_8_36($module)
{
    $ret = true;

    Configuration::deleteByName('pagecache_profiling');
    Configuration::deleteByName('pagecache_profiling_min_ms');
    Configuration::deleteByName('pagecache_profiling_max_reached');
    JprestaUtils::saveConfigurationAllShop('pagecache_profiling', false);
    JprestaUtils::saveConfigurationAllShop('pagecache_profiling_min_ms', 100);
    JprestaUtils::saveConfigurationAllShop('pagecache_profiling_max_reached', false);

    return $ret;
}
