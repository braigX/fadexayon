<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    try {
        // new hooks
        $module->registerHook('displayAdminProductsExtra');
        $module->registerHook('actionProductSave');
        $module->registerHook('displayFooterProduct');

        // create relations table
        $db_result = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_relation` (
             `id_prestawp_block` INT(11) UNSIGNED NOT NULL,
             `id_object` INT(11) UNSIGNED NOT NULL,
             `type` VARCHAR(65),
             UNIQUE (`id_prestawp_block`, `id_object`, `type`),
             INDEX (`id_prestawp_block`, `type`),
             INDEX (`id_object`, `type`)
             ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );
        if (!$db_result) {
            return false;
        }
        // product data table
        $db_result = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_product` (
                `id_product` INT(11) UNSIGNED NOT NULL,
                `id_shop` INT(11) UNSIGNED NOT NULL,
                `wp_categories` TEXT,
                `wp_posts` TEXT,
                UNIQUE (`id_product`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );
        if (!$db_result) {
            return false;
        }
        // block shop table
        $db_result = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_shop` (
                `id_prestawp_block` INT(11) UNSIGNED NOT NULL,
                `id_shop` INT(10) UNSIGNED NOT NULL,
                UNIQUE (`id_prestawp_block`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );
        if (!$db_result) {
            return false;
        }

        // add fields
        Db::getInstance()->execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'prestawp_block`
             ADD `show_preview` TINYINT(1) DEFAULT 0;'
        );

        // move category relations to the new table
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'prestawp_block`
             WHERE `ps_categories` != "" AND `ps_categories` IS NOT NULL'
        );
        foreach ($rows as $row) {
            $categories = $row['ps_categories'];
            $categories = explode(',', $categories);
            foreach ($categories as $id_category) {
                if ($id_category && is_numeric($id_category)) {
                    Db::getInstance()->execute(
                        'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_block_relation`
                         (`id_prestawp_block`, `id_object`, `type`)
                         VALUES
                         (' . (int) $row['id_prestawp_block'] . ', ' . (int) $id_category . ', "category")'
                    );
                }
            }
        }

        // move shop associations to the block shop table
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'prestawp_block`'
        );
        foreach ($rows as $row) {
            if ($row['id_shop']) {
                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_block_shop`
                     (`id_prestawp_block`, `id_shop`)
                     VALUES
                     (' . (int) $row['id_prestawp_block'] . ', ' . (int) $row['id_shop'] . ')'
                );
            }
        }

        // load default settings for the product posts
        foreach ($module->getProductPostsSettings() as $item) {
            if ($item['type'] == 'html') {
                continue;
            }

            if (Configuration::getGlobalValue($module->settings_prefix . $item['name']) === false) {
                if (isset($item['default_' . $module->getPSVersion(true)])) {
                    Configuration::updateGlobalValue(
                        $module->settings_prefix . $item['name'],
                        $item['default_' . $module->getPSVersion(true)]
                    );
                } elseif (isset($item['default'])) {
                    Configuration::updateGlobalValue($module->settings_prefix . $item['name'], $item['default']);
                }
            }
        }
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
