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
 * Add details_md5 column
 *
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_0_10($module)
{
    $ret = true;

    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache_details`
        CHANGE COLUMN `details` `details` TEXT NOT NULL,
        ADD COLUMN `details_md5` VARCHAR(32) DEFAULT NULL,
        ADD INDEX `details_md5` (`details_md5`)
        ');

    $ret &= JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . 'jm_pagecache_details`
        SET `details_md5` = MD5(`details`)
        WHERE `details_md5` IS NULL
        ');

    try {
        JprestaUtils::dbExecuteSQL('DROP INDEX `details` ON `' . _DB_PREFIX_ . 'jm_pagecache_details`');
    } catch (Throwable $e) {
        // Ignore because it is not a big deal
    }

    return (bool) $ret;
}
