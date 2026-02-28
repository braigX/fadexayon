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
 * Hook to actionCustomerAccountAdd
 */
function upgrade_module_8_8_61($module)
{
    $module->registerHook('actionCustomerAccountAdd');

    return true;
}
