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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';

class UpgradeHelper
{
    public static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . '/' . $object)) {
                        static::rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    } else {
                        @unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            @rmdir($dir);
        }
    }

    public static function deleteTables()
    {
        $sql = 'DROP TABLE IF EXISTS `' .
            _DB_PREFIX_ . 'op_faq_items`, `' .
            _DB_PREFIX_ . 'op_faq_items_lang`, `' .
            _DB_PREFIX_ . 'op_faq_title`, `' .
            _DB_PREFIX_ . 'op_faq_title_lang`, `' .
            _DB_PREFIX_ . 'op_faq_description`, `' .
            _DB_PREFIX_ . 'op_faq_description_lang`, `' .
            _DB_PREFIX_ . 'op_faq_page_item`, `' .
            _DB_PREFIX_ . 'op_faq_page_item_index`, `' .
            _DB_PREFIX_ . 'op_faq_blocks`, `' .
            _DB_PREFIX_ . 'op_faq_blocks_lang`, `' .
            _DB_PREFIX_ . 'op_faq_blocks_index`, `' .
            _DB_PREFIX_ . 'op_faq_block_item`';

        return Db::getInstance()->execute($sql);
    }

    public static function insertForUpgrade($db_name, $data)
    {
        if (!empty($data)) {
            try {
                return Db::getInstance()->insert(
                    bqSQL($db_name),
                    $data,
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return true;
    }

    public static function makeInsertItems()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_items`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        $res = [];

        foreach ($rows as $row) {
            $res[] = ['id' => (int) $row['id_item']];
        }

        return $res;
    }

    public static function makeInsertItemsLang()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_items_lang`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        $res = [];

        foreach ($rows as $row) {
            $res[] = [
                'id' => (int) $row['id_item'],
                'id_lang' => (int) $row['id_lang'],
                'question' => pSQL($row['question'], true),
                'answer' => pSQL($row['answer'], true),
                'title' => pSQL($row['title']),
            ];
        }

        return $res;
    }

    public static function deleteFiles()
    {
        if (file_exists(_PS_MODULE_DIR_ . 'faqop/traits/')) {
            static::rrmdir(_PS_MODULE_DIR_ . 'faqop/traits/');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/admin/_configure/')) {
            static::rrmdir(_PS_MODULE_DIR_ . 'faqop/views/templates/admin/_configure/');
        }

        // Files

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqBlock.php')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqBlock.php');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqDescription.php')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqDescription.php');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqItem.php')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqItem.php');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqPageBindCall.php')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqPageBindCall.php');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqTitle.php')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/classes/OpFaqTitle.php');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/faq_block_front.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/faq_block_front.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/faq_block_front_shortcode.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/faq_block_front_shortcode.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/instructions.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/instructions.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_add.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_add.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_inside_page.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_inside_page.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_standard.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/item_list_standard.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/jqueryui_table_move_blocks.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/jqueryui_table_move_blocks.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/jqueryui_table_move_items.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/jqueryui_table_move_items.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/page.tpl')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/templates/hook/page.tpl');
        }

        if (file_exists(_PS_MODULE_DIR_ . 'faqop/views/css/op_faq.css')) {
            @unlink(_PS_MODULE_DIR_ . 'faqop/views/css/op_faq.css');
        }
    }

    public static function updateConfiguration()
    {
        Configuration::deleteByName('OP_FAQ_TITLE_ENABLED');
        Configuration::deleteByName('OP_FAQ_DESCRIPTION_ENABLED');
        Configuration::deleteByName('OP_FAQ_MARKUP_ENABLED');

        return Configuration::updateGlobalValue('OP_FAQ_PAGE_ACTIVE', 1);
    }

    public static function getDataForPages()
    {
        $rows = static::getIdShopsForPages('title');

        $res = [];

        foreach ($rows as $row) {
            $id_shop = (int) $row['id_shop'];

            $res[] = [
                'active' => 1,
                'block_type' => 'page',
                'hook_name' => pSQL(ConfigsFaq::PAGE_HOOK),
                'id_shop' => $id_shop,
                'title_tag' => 'h1',
                'show_markup' => Configuration::get(
                    'OP_FAQ_MARKUP_ENABLED',
                    null,
                    null,
                    $id_shop
                ),
                'show_title' => Configuration::get(
                    'OP_FAQ_TITLE_ENABLED',
                    null,
                    null,
                    $id_shop
                ),
                'show_description' => Configuration::get(
                    'OP_FAQ_DESCRIPTION_ENABLED',
                    null,
                    null,
                    $id_shop
                ),
            ];
        }

        return $res;
    }

    public static function getIdShopsForPages($type)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($type) . '` ORDER BY `id_shop`';

        try {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }
    }

    public static function getDataForPagesLang()
    {
        $shop_ids = static::getIdShopsForPages('title');

        $res = [];
        $tmp = [];

        $nextId = 0;

        foreach ($shop_ids as $row) {
            ++$nextId;

            $id = (int) $row['id_op_faq_title'];

            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_title_lang` WHERE `id_op_faq_title` = ' . $id;

            try {
                $titleRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }

            // we will have rows with title and id_lang here
            foreach ($titleRows as $titleRow) {
                $id_lang = (int) $titleRow['id_lang'];

                $tmp[$nextId . '_' . $id_lang] = [
                    'id' => $nextId,
                    'id_lang' => $id_lang,
                    'title' => pSQL($titleRow['title']),
                ];
            }

            // page description
            $id_shop = (int) $row['id_shop'];
            $sql = 'SELECT id_op_faq_description FROM `' . _DB_PREFIX_ . 'op_faq_description` 
            WHERE `id_shop` = ' . $id_shop;

            try {
                $id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }

            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_description_lang` WHERE `id_op_faq_description` = ' . (int) $id;

            try {
                $descrRows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }

            foreach ($descrRows as $descrRow) {
                $id_lang = (int) $descrRow['id_lang'];

                $tmp[$nextId . '_' . $id_lang] = array_merge(
                    $tmp[$nextId . '_' . $id_lang],
                    ['description' => pSQL($descrRow['description'], true)]
                );
            }
        }

        foreach ($tmp as $row) {
            $res[] = $row;
        }

        return $res;
    }

    public static function getDataForPageItemsBind($faq_pages_insert)
    {
        $res = [];

        $pages = [];

        foreach ($faq_pages_insert as $key => $row) {
            $pages[$row['id_shop']] = ++$key;
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_page_item`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        foreach ($rows as $row) {
            $id_shop = (int) $row['id_shop'];
            $res[] = [
                'id_item' => (int) $row['id_item'],
                'position' => (int) $row['position'],
                'id_block' => $pages[$id_shop],
                'block_type' => 'page',
                'id_shop' => $id_shop,
            ];
        }

        return $res;
    }

    public static function makeInsertLists()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_blocks`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        $res = [];

        foreach ($rows as $row) {
            $res[] = [
                'id' => (int) $row['id_block'],
                'active' => (int) $row['active'],
                'hook_name' => pSQL($row['hook_name']),
                'block_type' => 'list',
                'position' => (int) $row['position'],
                'not_all_languages' => (int) $row['not_all_languages'],
                'languages' => pSQL($row['languages']),
                'not_all_currencies' => (int) $row['not_all_currencies'],
                'currencies' => pSQL($row['currencies']),
                'not_all_pages' => (int) $row['not_all_pages'],
                'all_products' => (int) $row['all_products'],
                'select_products_by_category' => (int) $row['select_products_by_category'],
                'select_products_by_id' => (int) $row['select_products_by_id'],
                'product_ids' => pSQL($row['product_ids']),
                'category_ids_p' => pSQL($row['category_ids_p']),
                'all_categories' => (int) $row['all_categories'],
                'category_ids' => pSQL($row['category_ids']),
                'all_cms_pages' => (int) $row['all_cms_pages'],
                'cms_page_ids' => pSQL($row['cms_page_ids']),
                'all_without' => (int) $row['all_without'],
                'special_ids' => pSQL($row['special_ids']),
                'show_title' => (int) $row['show_title'],
                'show_markup' => (int) $row['show_markup'],
                'id_shop' => (int) $row['id_shop'],
            ];
        }

        return $res;
    }

    public static function makeInsertListsLang()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_blocks_lang`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        $res = [];

        foreach ($rows as $row) {
            $res[] = [
                'id' => (int) $row['id_block'],
                'id_lang' => (int) $row['id_lang'],
                'title' => pSQL($row['title']),
            ];
        }

        return $res;
    }

    public static function getDataForListItemsBind()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'op_faq_block_item`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        $res = [];

        foreach ($rows as $row) {
            $res[] = [
                'id_item' => (int) $row['id_item'],
                'position' => (int) $row['position'],
                'id_block' => (int) $row['id_block'],
                'id_shop' => (int) $row['id_shop'],
                'block_type' => 'list',
            ];
        }

        return $res;
    }
}
