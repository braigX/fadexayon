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
function upgrade_module_7_9_0($module)
{
    $ret = true;

    $module->registerHook('actionAdminSaveBefore');

    Jprestaspeedpack::updateCacheKeyForCountries();
    Jprestaspeedpack::updateCacheKeyForUserGroups();
    JprestaCustomer::deleteAllFakeUsers();

    return (bool) $ret;
}
