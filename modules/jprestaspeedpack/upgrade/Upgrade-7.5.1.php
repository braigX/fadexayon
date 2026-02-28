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
function upgrade_module_7_5_1($module)
{
    $ret = true;

    JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` CHANGE COLUMN `id_object` `id_object` INT UNSIGNED');

    return (bool) $ret;
}
