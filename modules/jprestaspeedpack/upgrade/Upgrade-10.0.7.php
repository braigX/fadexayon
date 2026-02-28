<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Add cache for Smartblog module
 */
function upgrade_module_10_0_7($module)
{
    $module->installOverridesForModules(true, 'smartblog');
    Jprestaspeedpack::removeManagedControllerName('smartblog__details');
    Jprestaspeedpack::removeManagedControllerName('smartblog__category');
    Configuration::deleteByName('pagecache_smartblog__details');
    Configuration::deleteByName('pagecache_smartblog__category');
    return true;
}
