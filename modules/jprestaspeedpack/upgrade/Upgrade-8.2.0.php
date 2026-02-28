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
 * Managed controllers are now stored in Db
 */
function upgrade_module_8_2_0($module)
{
    Configuration::deleteByName('pagecache_managed_ctrl');
    Jprestaspeedpack::getManagedControllers();

    $module->registerHook('actionObjectAddAfter');
    $module->registerHook('actionObjectUpdateAfter');
    $module->registerHook('actionObjectDeleteAfter');
    $module->installOverridesForModules();

    return true;
}
