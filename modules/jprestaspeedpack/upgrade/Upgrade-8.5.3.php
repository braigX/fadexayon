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
 * Add a primary key to backlinks table
 */
function upgrade_module_8_5_3($module)
{
    $ret = true;

    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK, 'id_bl')) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` ADD `id_bl` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id_bl`)');
    }

    return $ret;
}
