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
 * Add ph_simpleblog as a managed controller
 */
function upgrade_module_9_0_3($module)
{
    $module->installOverridesForModules();
    Configuration::deleteByName('pagecache_ph_simpleblog__category');
    Configuration::deleteByName('pagecache_ph_simpleblog__single');
    Configuration::deleteByName('pagecache_ph_simpleblog__list');

    // It does not matter if it fails
    return true;
}
