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
 * Register to hook displayAdminAfterHeader
 *
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_0_15($module)
{
    $ret = true;

    try {
        $module->registerHook('displayAdminAfterHeader');
    } catch (Throwable $e) {
        // Ignore because it is not a big deal
    }

    return (bool) $ret;
}
