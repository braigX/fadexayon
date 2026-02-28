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
 * Upgrade override of pm_advancedsearch4
 */
function upgrade_module_10_0_6($module)
{
    $module->installOverridesForModules(true, 'pm_advancedsearch4');
    Jprestaspeedpack::removeManagedControllerName('pm_advancedsearch4__seo');
    Configuration::deleteByName('pagecache_pm_advancedsearch4__seo');
    return true;
}
