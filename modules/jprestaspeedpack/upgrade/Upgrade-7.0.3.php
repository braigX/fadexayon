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
function upgrade_module_7_0_3($module)
{
    $ret = true;

    Configuration::updateValue('pagecache_max_exec_time', min(90, max(10, (int) ini_get('max_execution_time') - 5)));

    return (bool) $ret;
}
