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
 * Migrate from InnoDb to MyIsam
 *
 * @var $module Jprestaspeedpack
 *
 * @return bool
 */
function upgrade_module_7_6_0($module)
{
    $ret = true;

    $constraintNameBl = JprestaUtils::dbGetValue('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
      WHERE TABLE_SCHEMA=\'' . JprestaUtils::getDatabaseName() . '\'
      AND TABLE_NAME =  \'' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '\'
      AND REFERENCED_TABLE_NAME = \'' . _DB_PREFIX_ . PageCacheDAO::TABLE . '\'');
    if (!$constraintNameBl) {
        // Try the default name, do not fail if the drop fail
        $constraintNameBl = 'ps_jm_pagecache_bl_ibfk_1';
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` DROP FOREIGN KEY ' . $constraintNameBl);
    } else {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` DROP FOREIGN KEY ' . $constraintNameBl);
    }

    $constraintNameMods = JprestaUtils::dbGetValue('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
      WHERE TABLE_SCHEMA=\'' . JprestaUtils::getDatabaseName() . '\'
      AND TABLE_NAME =  \'' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '\'
      AND REFERENCED_TABLE_NAME = \'' . _DB_PREFIX_ . PageCacheDAO::TABLE . '\'');
    if (!$constraintNameMods) {
        // Try the default name, do not fail if the drop fail
        $constraintNameMods = 'ps_jm_pagecache_mods_ibfk_1';
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '` DROP FOREIGN KEY ' . $constraintNameMods);
    } else {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '` DROP FOREIGN KEY ' . $constraintNameMods);
    }

    $constraintNameDetailsTax = JprestaUtils::dbGetValue('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
      WHERE TABLE_SCHEMA=\'' . JprestaUtils::getDatabaseName() . '\'
      AND TABLE_NAME =  \'' . _DB_PREFIX_ . PageCacheDAO::TABLE . '\'
      AND COLUMN_NAME =  \'id_tax_csz\'
      AND REFERENCED_TABLE_NAME = \'' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '\'');
    if (!$constraintNameDetailsTax) {
        // Try the default name, do not fail if the drop fail
        $constraintNameDetailsTax = 'ps_jm_pagecache_ibfk_1';
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` DROP FOREIGN KEY ' . $constraintNameDetailsTax);
    } else {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` DROP FOREIGN KEY ' . $constraintNameDetailsTax);
    }

    $constraintNameDetailsSpecifics = JprestaUtils::dbGetValue('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
      WHERE TABLE_SCHEMA=\'' . JprestaUtils::getDatabaseName() . '\'
      AND TABLE_NAME =  \'' . _DB_PREFIX_ . PageCacheDAO::TABLE . '\'
      AND COLUMN_NAME =  \'id_specifics\'
      AND REFERENCED_TABLE_NAME = \'' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '\'');
    if (!$constraintNameDetailsSpecifics) {
        // Try the default name, do not fail if the drop fail
        $constraintNameDetailsSpecifics = 'ps_jm_pagecache_ibfk_2';
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` DROP FOREIGN KEY ' . $constraintNameDetailsSpecifics);
    } else {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` DROP FOREIGN KEY ' . $constraintNameDetailsSpecifics);
    }

    $ret &= JprestaUtils::dbExecuteSQL('
		ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` ENGINE MyIsam;
        ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '` ENGINE MyIsam;
        ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` ENGINE MyIsam;
        ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '` ENGINE MyIsam;
        ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_SPECIFIC_PRICES . '` ENGINE MyIsam;
        ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PROFILING . '` ENGINE MyIsam;');

    return (bool) $ret;
}
