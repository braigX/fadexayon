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
 * Clear the cache completly
 *
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_0_8($module)
{
    $ret = true;

    Configuration::updateValue('pagecache_ignore_before_pattern',
        JprestaUtils::encodeConfiguration('</header>'));

    $module->clearCacheAndStats('Upgrade 7.0.8');

    return (bool) $ret;
}
