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
 * Override for pm_advancedsearch4
 */
function upgrade_module_8_7_1($module)
{
    $module->installOverridesForModules();
    Jprestaspeedpack::removeManagedControllerName('pm_advancedsearch4__seo');
    Configuration::deleteByName('pagecache_pm_advancedsearch4__seo');

    // It does not matter if it fails
    return true;
}
