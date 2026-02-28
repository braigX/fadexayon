<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_9_37($module)
{
    $ret = true;
    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
        $module->registerHook('actionAdminProductsControllerSaveAfter');
    }

    return (bool) $ret;
}
