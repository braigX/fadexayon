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
function upgrade_module_7_9_28($module)
{
    $ret = true;

    $moduleIds = [];
    foreach ($module->getModulesToCheck() as $moduleToCheck) {
        $moduleIds[] = (int) Module::getModuleIdByName($moduleToCheck);
    }
    if (count($moduleIds) > 0) {
        JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '` WHERE `id_module` NOT IN (' . implode(',',
            $moduleIds) . ');');
    }

    return (bool) $ret;
}
