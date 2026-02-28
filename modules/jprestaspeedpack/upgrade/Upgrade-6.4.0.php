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

/*
 * Optimize column sizes, add 2 needed columns
 */
function upgrade_module_6_4_0($module)
{
    $ret = true;
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` ADD COLUMN `last_gen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` ADD COLUMN `deleted` TINYINT NOT NULL DEFAULT 0');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` ADD INDEX (`last_gen`)');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` ADD INDEX (`deleted`)');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` MODIFY COLUMN `id_object` MEDIUMINT UNSIGNED');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` MODIFY COLUMN `id_shop` TINYINT UNSIGNED NOT NULL DEFAULT 1');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` MODIFY COLUMN `count_missed` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0');
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache` MODIFY COLUMN `count_hit` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0');

    if ($ret) {
        $module->installTab('AdminPageCacheDatas');
    }

    return (bool) $ret;
}
