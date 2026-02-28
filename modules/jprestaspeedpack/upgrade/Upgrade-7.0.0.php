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
function upgrade_module_7_0_0($module)
{
    $ret = true;

    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` ENGINE=' . PageCacheDAO::MYSQL_ENGINE);
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '` ENGINE=' . PageCacheDAO::MYSQL_ENGINE);
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '` ENGINE=' . PageCacheDAO::MYSQL_ENGINE);
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_SPECIFIC_PRICES . '` ENGINE=' . PageCacheDAO::MYSQL_ENGINE);
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PROFILING . '` ENGINE=' . PageCacheDAO::MYSQL_ENGINE);

    $ret &= JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'jm_pagecache_details`(
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `details` VARCHAR(2000) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX (`details`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8');

    // Clear all tables because we are going to modify their structures
    $module->clearCacheAndStats('Upgrade 7.0.0');

    // New main table structure
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache`
        CHANGE COLUMN `url_crc32` `cache_key` INT UNSIGNED NOT NULL,
        CHANGE COLUMN `url` `url` VARCHAR(1000) NOT NULL,
        ADD COLUMN `id_currency` INT(10) UNSIGNED AFTER `id_object`,
        ADD COLUMN `id_lang` INT(10) UNSIGNED AFTER `id_currency`,
        ADD COLUMN `id_fake_customer` INT(10) UNSIGNED DEFAULT NULL AFTER `id_lang`,
        ADD COLUMN `id_device` TINYINT(1) UNSIGNED AFTER `id_fake_customer`,
        ADD COLUMN `id_country` INT(10) UNSIGNED DEFAULT NULL AFTER `id_device`,
        ADD COLUMN `mask_country` BINARY(4) DEFAULT NULL AFTER `id_country`,
        ADD COLUMN `id_tax_csz` INT(11) UNSIGNED DEFAULT NULL AFTER `mask_country`,
        ADD COLUMN `id_specifics` INT(11) UNSIGNED DEFAULT NULL AFTER `id_tax_csz`,
        ADD COLUMN `v_css` SMALLINT UNSIGNED DEFAULT NULL AFTER `id_specifics`,
        ADD COLUMN `v_js` SMALLINT UNSIGNED DEFAULT NULL AFTER `v_css`,
        ADD INDEX (`id_country`),
        ADD INDEX (`v_js`),
        ADD INDEX (`v_css`),
        ADD INDEX (`url`),
        ADD FOREIGN KEY (id_tax_csz) REFERENCES ' . _DB_PREFIX_ . 'jm_pagecache_details(id) ON DELETE RESTRICT,
        ADD FOREIGN KEY (id_specifics) REFERENCES ' . _DB_PREFIX_ . 'jm_pagecache_details(id) ON DELETE RESTRICT,
        DROP COLUMN `file`
        ');

    // New backlinks table structure
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache_bl`
        CHANGE COLUMN `backlink_crc32` `backlink_key` INT UNSIGNED NOT NULL,
        ADD FOREIGN KEY (id) REFERENCES ' . _DB_PREFIX_ . 'jm_pagecache(id) ON DELETE CASCADE
        ');

    // New mods constraints
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'jm_pagecache_mods`
        ADD FOREIGN KEY (id) REFERENCES ' . _DB_PREFIX_ . 'jm_pagecache(id) ON DELETE CASCADE
        ');

    // Increase default timeout value from 3 to 7 days
    foreach (Jprestaspeedpack::getManagedControllersNames() as $controller) {
        $timeout = Configuration::get('pagecache_' . $controller . '_timeout');
        if ($timeout == (60 * 24 * 3)) {
            Configuration::updateValue('pagecache_' . $controller . '_timeout', 60 * 24 * 7);
        }
    }

    return (bool) $ret;
}
