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
function upgrade_module_7_3_7($module)
{
    $ret = true;

    // SPEEDPACK
    $isForceExtension = (int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION');
    if ($isForceExtension) {
        $module->removeOverride('Link.php');
        JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/override/classes/Link.php');
    } else {
        JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/override/classes/Link.php.off');
    }
    $module->jpresta_submodules['JprestaWebpModule']->updateHtaccessFile();

    $module->registerHook('actionObjectShopUrlAddAfter');
    $module->registerHook('actionObjectShopUrlUpdateAfter');
    $module->registerHook('actionObjectShopUrlDeleteAfter');
    // SPEEDPACK£

    return (bool) $ret;
}
