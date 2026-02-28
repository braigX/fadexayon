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
 * Register to actionJPrestaClearCache
 */
function upgrade_module_8_3_0($module)
{
    $module->registerHook('actionJPrestaClearCache');

    JprestaUtils::copyFiles(Jprestaspeedpack::getParentCacheDirectory() . '/widget_blocks', Jprestaspeedpack::getWidgetBlockDir());

    $module->installOverridesForModules();
    Configuration::deleteByName('pagecache_stblog__article');
    Configuration::deleteByName('pagecache_stblog__category');
    Configuration::deleteByName('pagecache_stblog__default');
    Configuration::deleteByName('pagecache_stblogarchives__default');

    // Ignore all backlinks after tag /footer>
    JprestaUtils::saveConfigurationAllShop('pagecache_ignore_after_pattern', JprestaUtils::encodeConfiguration('/footer>'));

    // It does not matter if it fails
    return true;
}
