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
 * Update database, remove obsolete files, reset the cache
 */
function upgrade_module_8_5_0($module)
{
    $ret = true;

    $module->installOverridesForModules(true);

    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'id_lang')) {
        $ret = JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD COLUMN `id_shop` INT(10) UNSIGNED DEFAULT NULL AFTER `context_key`,
            ADD COLUMN `id_lang` INT(10) UNSIGNED DEFAULT NULL AFTER `id_shop`,
            ADD INDEX (`id_shop`)
        ');
        $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            DROP INDEX `idx_find_context_full`,
            DROP INDEX `idx_find_context`,
            ADD INDEX `idx_find_context_full` (`id_shop`, `id_lang`, `id_currency`, `id_fake_customer`, `id_device`, `id_country`, `id_tax_csz`, `id_specifics`, `v_css`, `v_js`) USING BTREE,
            ADD INDEX `idx_find_context` (`id_shop`, `id_lang`, `id_currency`, `id_fake_customer`, `id_device`, `id_country`, `id_tax_csz`, `id_specifics`) USING BTREE
        ');
    }
    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_PERFS, 'id_shop')) {
        $ret = $ret && JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '`
            ADD COLUMN `id_shop` INT(10) UNSIGNED DEFAULT NULL AFTER `id`,
            ADD INDEX (`id_shop`)
        ');
    }
    $module->clearCacheAndStats('Upgrade 8.5.0');

    // Page Cache: Remove the 'speed analysis' feature because it is now useless with the TTFB statistics diagram
    $module->uninstallTab('AdminPageCacheSpeedAnalysis');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/controllers/admin/AdminPageCacheSpeedAnalysisController.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/views/templates/admin/pagecache-speed-analyse.tpl');

    // SPEEDPACK
    $module->registerHook('actionAjaxDieSearchControllerdoProductSearchBefore');
    $module->registerHook('actionAjaxDieCategoryControllerdoProductSearchBefore');
    // SPEEDPACK£

    return $ret;
}
