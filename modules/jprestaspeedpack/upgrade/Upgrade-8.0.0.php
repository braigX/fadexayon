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
function upgrade_module_8_0_0($module)
{
    $ret = JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`');
    $ret = $ret && JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '`');
    $ret = $ret && JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '`');
    $ret = $ret && JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '`');

    PageCacheDAO::createTableContexts();

    $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        CHANGE COLUMN `url` `url` VARCHAR(255) NOT NULL,
        DROP INDEX `url`
        ');

    $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        DROP COLUMN `id_currency`,
        DROP COLUMN `id_lang`,
        DROP COLUMN `id_fake_customer`,
        DROP COLUMN `id_device`,
        DROP COLUMN `id_country`,
        DROP COLUMN `id_tax_csz`,
        DROP COLUMN `id_specifics`,
        DROP COLUMN `v_css`,
        DROP COLUMN `v_js`,
        ADD COLUMN `id_context` INT(11) UNSIGNED DEFAULT NULL AFTER `url`,
        ADD INDEX `id_controller` (`id_controller`),
        ADD UNIQUE `url_id_context` (`url`, `id_context`),
        ADD INDEX `id_controller_last_gen` (`id_controller`, `last_gen`)
        ');

    // Ignore if this one fails
    JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
        DROP INDEX `cache_key`
        ');

    $module->installStaticCode();

    $typecache = JprestaUtils::getConfigurationAllShop('pagecache_typecache');
    if ($typecache === 'std' || $typecache === 'stdzip' || $typecache === 'zip') {
        JprestaUtils::saveConfigurationAllShop('pagecache_typecache', 'static');
        $module->uninstallCache($typecache);
        $module->installCache();
        $module->clearCache();
    }

    JprestaUtils::saveConfigurationAllShop('pagecache_static_expires', 15);

    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
        $module->upgradeOverride('Media');
    }

    @unlink(_PS_MODULE_DIR_ . $module->name . '/views/js/pagecache.js');

    return (bool) $ret;
}
