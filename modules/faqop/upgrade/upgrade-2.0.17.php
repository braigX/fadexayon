<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_17(FaqOp $module)
{
    $ih = $module->helper->getInstallHelper($module->install_helper);
    $res = $ih->createMetaPage();

    /* Add configuration value */
    $shops = Shop::getContextListShopID();
    $shop_groups_list = [];

    /* Setup each shop */
    foreach ($shops as $shop_id) {
        $shop_group_id = (int) Shop::getGroupFromShop($shop_id, true);

        if (!in_array($shop_group_id, $shop_groups_list)) {
            $shop_groups_list[] = $shop_group_id;
        }

        /* Sets up configuration */
        $res &= Configuration::updateValue(
            'OP_FAQ_MARKUP_ENABLED',
            1,
            false,
            $shop_group_id,
            $shop_id
        );
    }

    /* Sets up Shop Group configuration */
    if (count($shop_groups_list)) {
        foreach ($shop_groups_list as $shop_group_id) {
            $res &= Configuration::updateValue('OP_FAQ_MARKUP_ENABLED', 1, false, $shop_group_id);
        }
    }

    /* Sets up Global configuration */
    $res &= Configuration::updateValue('OP_FAQ_MARKUP_ENABLED', 1);

    /* Install new hooks */
    $res &= $module->registerHook('displayBackOfficeHeader');
    $res &= $module->registerHook('filterCmsContent');
    $res &= $module->registerHook('filterProductContent');
    $res &= $module->registerHook('filterCategoryContent');

    /* retrieve old data */
    $sql = '
            SELECT oi.`id_op_faq_item` as id_item, ofi.`position`, ofil.`question`,
            ofil.`answer`, ofil.`id_lang`, oi.`id_shop`
            FROM ' . _DB_PREFIX_ . 'op_faq oi
            RIGHT JOIN ' . _DB_PREFIX_ . 'op_faq_items ofi ON (oi.id_op_faq_item = ofi.id_op_faq_item)
            LEFT JOIN ' . _DB_PREFIX_ . 'op_faq_items_lang ofil ON (ofi.id_op_faq_item = ofil.id_op_faq_item)
            WHERE ofi.`active` = 1';

    try {
        $lang_items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    } catch (PrestaShopDatabaseException $e) {
        echo $e->getMessage();
    }

    $sql = '
            SELECT oi.`id_op_faq_item` as id_item, ofi.`position`, oi.`id_shop`
            FROM ' . _DB_PREFIX_ . 'op_faq oi
            RIGHT JOIN ' . _DB_PREFIX_ . 'op_faq_items ofi ON (oi.id_op_faq_item = ofi.id_op_faq_item) 
            WHERE ofi.`active` = 1';

    try {
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    } catch (PrestaShopDatabaseException $e) {
        echo $e->getMessage();
    }

    $res &= Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'op_faq`, `' . _DB_PREFIX_ . 'op_faq_items`, `' . _DB_PREFIX_ .
        'op_faq_items_lang`');

    $res &= createTablesUpgrade2();

    /* write old data to new tables */
    if (!empty($lang_items)) {
        foreach ($lang_items as $row) {
            /* Items lang */
            $title = Tools::substr($row['question'], 0, 100) . '...';

            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_items_lang',
                    [[
                        'id_item' => (int) $row['id_item'],
                        'id_lang' => (int) $row['id_lang'],
                        'question' => pSQL($row['question']),
                        'answer' => pSQL($row['answer']),
                        'title' => pSQL($title),
                    ]],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    if (!empty($items)) {
        foreach ($items as $row) {
            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_items',
                    [[
                        'id_item' => (int) $row['id_item'],
                        'id_shop' => (int) $row['id_shop'],
                    ]],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            /* Tie items to page */

            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_page_item',
                    [[
                        'id_item' => (int) $row['id_item'],
                        'position' => (int) $row['position'],
                        'id_shop' => (int) $row['id_shop'],
                    ]],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/list.tpl')) {
        unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/list.tpl');
    }

    return $res;
}

function createTablesUpgrade2()
{
    /* Items configuration */
    $sql = (bool) Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_items` (
                `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_item`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    /* Items lang configuration */
    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_items_lang` (
              `id_item` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `question` text NOT NULL,
              `answer` text NOT NULL,
              `title` text NOT NULL,
              PRIMARY KEY (`id_item`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_title` (
                `id_op_faq_title` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_op_faq_title`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_title_lang` (
              `id_op_faq_title` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              PRIMARY KEY (`id_op_faq_title`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_description` (
                `id_op_faq_description` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_op_faq_description`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_description_lang` (
              `id_op_faq_description` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `description` text NOT NULL,
              PRIMARY KEY (`id_op_faq_description`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_page_item` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_item` int(10) unsigned NOT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `id_shop` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`,`id_item`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_page_item_index` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_item` int(10) unsigned NOT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `id_lang` int(10) unsigned NOT NULL,
              `question` text NOT NULL,
              `answer` text NOT NULL,
              `id_shop` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_blocks` (
              `id_block` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `hook_name` varchar(255) NOT NULL,
              `position` int(11) unsigned NOT NULL DEFAULT \'0\',
              `not_all_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `all_products` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_id` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `product_ids` text,
              `category_ids_p` text,
              `all_categories` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids` text,
              `all_cms_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `cms_page_ids` text,
              `all_without` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `special_ids` text,
              `not_all_languages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `languages` text,
              `not_all_currencies` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `currencies` text,
              `show_title` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `show_markup` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `id_shop` int(11) unsigned NOT NULL,
              PRIMARY KEY (`id_block`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_blocks_lang` (
              `id_block` int(11) unsigned NOT NULL,
              `id_lang` int(11) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              PRIMARY KEY (`id_block`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_blocks_index` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `content` longtext,
              `hook_name` varchar(255) NOT NULL,
              `id_block` int(11) unsigned NOT NULL DEFAULT \'0\',
              `position` int(11) unsigned NOT NULL DEFAULT \'0\',
              `not_all_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `all_products` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_id` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `product_ids` text,
              `category_ids_p` text,
              `all_categories` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids` text,
              `all_cms_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `cms_page_ids` text,
              `all_without` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `special_ids` text,
              `not_all_languages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `languages` text,
              `not_all_currencies` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `currencies` text,
              `id_lang` int(11) unsigned NOT NULL DEFAULT \'0\',
              `id_shop` int(11) unsigned NOT NULL DEFAULT \'0\',
              `show_markup` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `show_title` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= (bool) Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_hooks` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `id_hook` int(11) unsigned NOT NULL,
                `hook_name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`, `id_hook`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_block_item` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_item` int(10) unsigned NOT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `id_shop` int(10) unsigned NOT NULL,
              `id_block` int(11) unsigned NOT NULL,
              PRIMARY KEY (`id`,`id_item`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

    return $sql;
}
