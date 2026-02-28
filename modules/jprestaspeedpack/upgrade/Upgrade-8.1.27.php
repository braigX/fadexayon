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
 * Hook to actionCustomerLogoutAfter and actionAuthentication
 */
function upgrade_module_8_1_27($module)
{
    $module->registerHook('actionCustomerLogoutAfter');
    $module->registerHook('actionAuthentication');

    return true;
}
