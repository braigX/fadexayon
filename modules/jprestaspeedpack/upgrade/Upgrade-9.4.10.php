<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Refresh pm_advancedsearch4__seo as a managed controllers
 */
function upgrade_module_9_4_10($module)
{
    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
        $module->installOverridesForModules(true, 'pm_advancedsearch4');
        Configuration::deleteByName('pm_advancedsearch4__seo');
    }

    // It does not matter if it fails
    return true;
}
