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
 * Add a uniq index to contexts
 */
function upgrade_module_8_8_49($module)
{
    // Add an index on id_context
    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE, 'id_context')) {
        JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`
            ADD INDEX `id_context` (`id_context`) USING BTREE;');
    }

    // Add column uniq_key
    $ret = true;
    if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'uniq_key')) {
        $ret = JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD COLUMN `uniq_key` INT UNSIGNED DEFAULT NULL');
    }

    if ($ret) {
        $rows = JprestaUtils::dbSelectRows('SELECT * FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`');
        foreach ($rows as $row) {
            $id_ctx_checked = (int) $row['id'];

            // uniq_key is used to have a uniq index. We cannot do it on multiple columns because they are nullable
            $uniq_key = crc32(JprestaUtils::dbToInt($row['id_shop'])
                . '|' . JprestaUtils::dbToInt($row['id_lang'])
                . '|' . JprestaUtils::dbToInt($row['id_currency'])
                . '|' . JprestaUtils::dbToInt($row['id_fake_customer'])
                . '|' . JprestaUtils::dbToInt($row['id_device'])
                . '|' . JprestaUtils::dbToInt($row['id_country'])
                . '|' . JprestaUtils::dbToInt($row['id_tax_csz'])
                . '|' . JprestaUtils::dbToInt($row['id_specifics'])
                . '|' . JprestaUtils::dbToInt($row['v_css'])
                . '|' . JprestaUtils::dbToInt($row['v_js']));
            JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` SET uniq_key=' . sprintf('%u', $uniq_key) . ' WHERE id=' . $id_ctx_checked);

            $whereClauses = [];
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_shop', $row['id_shop']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_lang', $row['id_lang']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_currency', $row['id_currency']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_fake_customer', $row['id_fake_customer']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_device', $row['id_device']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_country', $row['id_country']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_tax_csz', $row['id_tax_csz']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_specifics', $row['id_specifics']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('v_css', $row['v_css']);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('v_js', $row['v_js']);
            $orderBy = ' ORDER BY (count_bot+count_hit_server+count_hit_static+count_hit_browser+count_hit_bfcache+count_missed) DESC';
            $id_ctx_used = (int) JprestaUtils::dbGetValue('SELECT id FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` WHERE ' . implode(' AND ', $whereClauses) . $orderBy . ' LIMIT 1');
            if ($id_ctx_used && $id_ctx_used !== $id_ctx_checked) {
                // Fix values if possible (there might have some duplicates URL+id_context)
                JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` SET id_context=' . $id_ctx_used . ' WHERE id_context=' . $id_ctx_checked, false);
                // Delete remaining pages with old id_context
                JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '` WHERE id_context=' . $id_ctx_checked);
                // Finally delete the context
                JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` WHERE id=' . $id_ctx_checked);
            }
        }
        // Now forbids NULL values for uniq_key
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            CHANGE COLUMN `uniq_key` `uniq_key` INT UNSIGNED NOT NULL');
    }
    // Add a uniq key to avoid creating similar contexts
    $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
        DROP INDEX `idx_find_context_full`,
        ADD UNIQUE `idx_find_context_full` (`id_shop`, `id_lang`, `id_currency`, `id_fake_customer`, `id_device`, `id_country`, `id_tax_csz`, `id_specifics`, `v_css`, `v_js`) USING BTREE
        ');
    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'uniq_key')) {
        $ret &= JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD UNIQUE `idx_uniq_key` (uniq_key) USING BTREE
        ');
    }

    return $ret;
}
