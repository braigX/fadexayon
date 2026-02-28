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
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_7_0($module)
{
    $ret = true;

    $module->registerHook('actionObjectGroupAddAfter');
    $module->registerHook('actionObjectGroupUpdateAfter');
    $module->registerHook('actionObjectGroupDeleteAfter');
    $module->unregisterHook('actionObjectCartRuleAddAfter');
    $module->unregisterHook('actionObjectCartRuleUpdateAfter');
    $module->unregisterHook('actionObjectCartRuleDeleteAfter');

    Jprestaspeedpack::updateCacheKeyForCountries();
    Jprestaspeedpack::updateCacheKeyForUserGroups();
    JprestaCustomer::deleteAllFakeUsers();

    return (bool) $ret;
}
