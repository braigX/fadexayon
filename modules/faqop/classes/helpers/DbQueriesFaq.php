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

require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqModelFactory.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';

class DbQueriesFaq extends BasicHelper
{
    public function createTables()
    {
        $sql = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_lists` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_shop` int(11) unsigned NOT NULL,
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `block_type` varchar(10) NOT NULL,
              `hook_name` varchar(191),
              `position` int(11) unsigned NOT NULL DEFAULT \'0\',
              `admin_name` varchar(255),
              `not_all_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `all_products` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_id` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `product_ids` text,
              `select_products_by_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids_p` text,
              `is_only_default_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_brand` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `brand_ids_p` text,
              `select_products_by_tag` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `tag_ids_p` text,
              `select_products_by_feature` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `feature_ids_p` text,
              `all_categories` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids` text,
              `all_brands` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `brand_ids` text,
              `all_cms_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `cms_page_ids` text,
              `all_without` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `special_ids` text,
              `not_all_languages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `languages` text,
              `not_all_currencies` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `currencies` text,
              `not_all_customer_groups` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `customer_groups` text,
              `show_title` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `block_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `block_class` varchar(255),
              `title_tag` varchar(255) NOT NULL DEFAULT \'h2\',
              `title_class` varchar(255),
              `content_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `content_class` varchar(255),
              `item_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `item_class` varchar(255),
              `question_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `question_class` varchar(255),
              `answer_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `answer_class` varchar(255),
              `show_markup` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `accordion` tinyint(1) unsigned NOT NULL DEFAULT \'0\', 
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_lists_lang` (
              `id` int(11) unsigned NOT NULL,
              `id_lang` int(11) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              PRIMARY KEY (`id`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_pages` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_shop` int(11) unsigned NOT NULL,
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `block_type` varchar(10) NOT NULL,
              `hook_name` varchar(255) NOT NULL,
              `block_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `block_class` varchar(255),
              `title_tag` varchar(255) NOT NULL DEFAULT \'h1\',
              `title_class` varchar(255),
              `content_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `content_class` varchar(255),
              `item_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `item_class` varchar(255),
              `question_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `question_class` varchar(255),
              `answer_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `answer_class` varchar(255),
              `description_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `description_class` varchar(255),
              `show_markup` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `show_title` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
              `show_description` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
              `accordion` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_pages_lang` (
              `id` int(11) unsigned NOT NULL,
              `id_lang` int(11) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              `description` text,
              PRIMARY KEY (`id`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_lists_cache` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `hook_name` varchar(191),
              `id_block` int(11) unsigned NOT NULL DEFAULT \'0\',
              `block_type` varchar(10) NOT NULL,
              `position` int(11) unsigned NOT NULL DEFAULT \'0\',
              `id_shop` int(11) unsigned NOT NULL DEFAULT \'0\',
              `id_lang` int(11) unsigned NOT NULL DEFAULT \'0\',
              `content` longtext,
              `show_title` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `show_markup` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `description` text,
              `show_description` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `not_all_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `all_products` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_id` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `product_ids` text,
              `select_products_by_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids_p` text,
              `is_only_default_category` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `select_products_by_brand` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `brand_ids_p` text,
              `select_products_by_tag` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `tag_ids_p` text,
              `select_products_by_feature` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `feature_ids_p` text,
              `all_categories` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `category_ids` text,
              `all_brands` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `brand_ids` text,
              `all_cms_pages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `cms_page_ids` text,
              `all_without` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `special_ids` text,
              `not_all_languages` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `languages` text,
              `not_all_currencies` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `currencies` text,
              `not_all_customer_groups` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              `customer_groups` text,
              `block_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `block_class` varchar(255),
              `title_tag` varchar(255) NOT NULL DEFAULT \'h2\',
              `title_class` varchar(255),
              `content_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `content_class` varchar(255),
              `item_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `item_class` varchar(255),
              `question_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `question_class` varchar(255),
              `answer_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `answer_class` varchar(255),
              `description_tag` varchar(255) NOT NULL DEFAULT \'div\',
              `description_class` varchar(255),
              `accordion` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $sql &= (bool) Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_hooks` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `id_hook` int(11) unsigned NOT NULL,
                `hook_name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`, `id_hook`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        /* Items configuration */
        $sql &= (bool) Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_items` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `i_class` varchar(255),
                `q_class` varchar(255),
                `a_class` varchar(255),
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        /* Items lang configuration */
        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_items_lang` (
              `id` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `question` text NOT NULL,
              `answer` text NOT NULL,
              `title` text NOT NULL,
              PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        /* block == list */
        $sql &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'op_faq_block_item` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_item` int(10) unsigned NOT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `id_block` int(11) unsigned NOT NULL,
              `block_type` varchar(10) NOT NULL,
              `id_shop` int(11) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        return $sql;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            '
            DROP TABLE IF EXISTS `' .
            _DB_PREFIX_ . 'op_faq_lists`, `' .
            _DB_PREFIX_ . 'op_faq_lists_lang`, `' .
            _DB_PREFIX_ . 'op_faq_pages`, `' .
            _DB_PREFIX_ . 'op_faq_pages_lang`, `' .
            _DB_PREFIX_ . 'op_faq_lists_cache`,`' .
            _DB_PREFIX_ . 'op_faq_hooks`, `' .
            _DB_PREFIX_ . 'op_faq_items`, `' .
            _DB_PREFIX_ . 'op_faq_items_lang`, `' .
            _DB_PREFIX_ . 'op_faq_block_item`'
        );
    }

    public function getIsoCurrencyById($id)
    {
        $id = (int) $id;
        $query = 'SELECT `iso_code`
                    FROM  `' . _DB_PREFIX_ . 'currency`
                    WHERE `id_currency` = ' . $id;
        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) {
            return $result;
        }

        return false;
    }

    public function listExists($id_block, $block_type, $id_shop = null)
    {
        if ($id_shop == null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $req = 'SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's`
                WHERE id = ' . (int) $id_block .
            ' AND id_shop = ' . (int) $id_shop;
        $val = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($req);

        return $val;
    }

    public function itemExists($id_item)
    {
        $req = 'SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'op_faq_items`
                WHERE id = ' . (int) $id_item;
        $val = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($req);

        return $val;
    }

    public function hookExists($id_hook)
    {
        $req = 'SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'op_faq_hooks` obb
                WHERE obb.`id_hook` = ' . (int) $id_hook;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($req);

        return $row;
    }

    public function getBlocksInHook($hook_name)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $current_language = (int) Context::getContext()->language->id;
        $hook_name = pSQL($hook_name);

        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . "op_faq_lists_cache`
			WHERE hook_name = '{$hook_name}' 
			AND id_shop = " . $id_shop . '
			AND id_lang = ' . $current_language . '
			ORDER BY position';

        try {
            $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $items;
    }

    public function getOtherBlocksInHook($hook_name)
    {
        $itemTypes = ConfigsFaq::BLOCK_TYPES;
        $hook_name = pSQL($hook_name);

        foreach ($itemTypes as $item) {
            $sql = '
			SELECT COUNT(*)
			FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($item) . "s`
			WHERE hook_name = '{$hook_name}'";
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($res) {
                return true;
            }
        }

        return false;
    }

    public function isSetHookName($hookName, $block_type)
    {
        $sql = '
			SELECT hook_name
			FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's`';

        try {
            $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        $res = [];
        foreach ($query as $row) {
            $res[] = $row['hook_name'];
        }
        $res = array_flip($res);
        if ($res[$hookName]) {
            return true;
        }

        return false;
    }

    public function getOldHook($id, $block_type)
    {
        $id = (int) $id;

        $sql = '
			SELECT `hook_name`
			FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's`
			WHERE id = ' . $id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function isOldActive($id, $block_type)
    {
        $id = (int) $id;

        $sql = '
			SELECT `active`
			FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's`
			WHERE id = ' . $id;

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getProducts($product_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $res = [];

        $product_ids = HelperFaq::sanitizeIntsFromString($product_ids);
        if (!empty($product_ids)) {
            $query = 'SELECT distinct `id_product`, `name`
                    FROM  `' . _DB_PREFIX_ . 'product_lang`
                    WHERE `id_product` IN(' . $product_ids . ')
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop . '
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getProduct($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $id = (int) $id;

        $query = 'SELECT `id_product`, `name`
                    FROM  `' . _DB_PREFIX_ . 'product_lang`
                    WHERE `id_product` = ' . $id . '
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getCategories($category_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $category_ids = HelperFaq::sanitizeIntsFromString($category_ids);

        $res = [];

        if (!empty($category_ids)) {
            $query = 'SELECT distinct `id_category`, `name`
                    FROM  `' . _DB_PREFIX_ . 'category_lang`
                    WHERE `id_category` IN(' . $category_ids . ')
                    AND `id_lang` = ' . (int) $id_lang . '
                    AND `id_shop` = ' . (int) $id_shop . '
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getBrands($brand_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $brand_ids = HelperFaq::sanitizeIntsFromString($brand_ids);

        $res = [];

        if (!empty($brand_ids)) {
            $query = 'SELECT distinct m.`id_manufacturer`, m.`name`
                    FROM  `' . _DB_PREFIX_ . 'manufacturer` m
                    JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms
                    USING (id_manufacturer)
                    WHERE m.`id_manufacturer` IN(' . $brand_ids . ')
                    AND ms.`id_shop` = ' . $id_shop . '
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getTags($tag_ids)
    {
        $tag_ids = HelperFaq::sanitizeIntsFromString($tag_ids);

        $res = [];

        if (!empty($tag_ids)) {
            $query = 'SELECT distinct `id_tag`, `name`
                    FROM  `' . _DB_PREFIX_ . 'tag`
                    WHERE `id_tag` IN(' . $tag_ids . ')
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getFeatures($feature_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $feature_ids = HelperFaq::sanitizeIntsFromString($feature_ids);

        $res = [];

        if (!empty($feature_ids)) {
            $query = 'SELECT distinct fl.`id_feature`, fl.`name`
                    FROM  `' . _DB_PREFIX_ . 'feature_lang` fl
                    JOIN `' . _DB_PREFIX_ . 'feature_shop` fs
                    USING (id_feature)
                    WHERE fl.`id_feature` IN(' . $feature_ids . ')
                    AND fl.`id_lang` = ' . $id_lang . '
                    AND fs.`id_shop` = ' . $id_shop . '
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getCategory($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        $id = (int) $id;

        $query = 'SELECT `id_category`, `name`
                    FROM  `' . _DB_PREFIX_ . 'category_lang`
                    WHERE `id_category` = ' . $id . '
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getBrand($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;

        $id = (int) $id;

        $query = 'SELECT m.`id_manufacturer`, m.`name`
                    FROM  `' . _DB_PREFIX_ . 'manufacturer` m
                    JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms
                    USING (id_manufacturer)
                    WHERE m.`id_manufacturer` = ' . $id . '
                    AND ms.`id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getTag($id)
    {
        $id = (int) $id;

        $query = 'SELECT `id_tag`, `name`
                    FROM  `' . _DB_PREFIX_ . 'tag`
                    WHERE `id_tag` = ' . $id;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getFeature($id)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        $id = (int) $id;

        $query = 'SELECT fl.`id_feature`, fl.`name`
                    FROM  `' . _DB_PREFIX_ . 'feature_lang` fl
                    JOIN `' . _DB_PREFIX_ . 'feature_shop` fs
                    USING (id_feature)
                    WHERE fl.`id_feature` = ' . $id . '
                    AND fl.`id_lang` = ' . $id_lang . '
                    AND fs.`id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getCmsCategory($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $id = (int) $id;

        $query = 'SELECT `id_cms_category`, `name`
                    FROM  `' . _DB_PREFIX_ . 'cms_category_lang`
                    WHERE `id_cms_category` = ' . $id . '
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getCmsCategories($category_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $category_ids = HelperFaq::sanitizeIntsFromString($category_ids);

        if (!empty($category_ids)) {
            $query = 'SELECT distinct `id_cms_category`, `name`
                    FROM  `' . _DB_PREFIX_ . 'cms_category_lang`
                    WHERE `id_cms_category` IN(' . $category_ids . ')
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop . '
                    ORDER BY `name` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getCmsPages($cms_ids)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $cms_ids = HelperFaq::sanitizeIntsFromString($cms_ids);

        $res = [];

        if (!empty($cms_ids)) {
            $query = 'SELECT distinct `id_cms`, `meta_title`
                    FROM  `' . _DB_PREFIX_ . 'cms_lang`
                    WHERE `id_cms` IN(' . $cms_ids . ')
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop . '
                    ORDER BY `meta_title` DESC';

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getCmsPage($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $id = (int) $id;

        $query = 'SELECT `id_cms`, `meta_title`
                    FROM  `' . _DB_PREFIX_ . 'cms_lang`
                    WHERE `id_cms` = ' . $id . '
                    AND `id_lang` = ' . $id_lang . '
                    AND `id_shop` = ' . $id_shop;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getCategoryIdsByProductId($product_id, $is_only_default_category)
    {
        $product_id = (int) $product_id;

        if ($is_only_default_category) {
            $query = 'SELECT `id_category_default` as `id_category`
                    FROM  `' . _DB_PREFIX_ . 'product_shop`
                    WHERE `id_product` = ' . $product_id . ' AND id_shop = '
                . Context::getContext()->shop->id;
        } else {
            $query = 'SELECT `id_category`
                    FROM  `' . _DB_PREFIX_ . 'category_product`
                    WHERE `id_product` = ' . $product_id;
        }

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getBrandIdByProductId($product_id)
    {
        $product_id = (int) $product_id;
        $query = 'SELECT `id_manufacturer`
                    FROM  `' . _DB_PREFIX_ . 'product`
                    WHERE `id_product` = ' . $product_id;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getTagIdsByProductId($product_id)
    {
        $product_id = (int) $product_id;
        $query = 'SELECT `id_tag`
                    FROM  `' . _DB_PREFIX_ . 'product_tag`
                    WHERE `id_product` = ' . $product_id;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getFeatureIdsByProductId($product_id)
    {
        $product_id = (int) $product_id;
        $query = 'SELECT `id_feature`
                    FROM  `' . _DB_PREFIX_ . 'feature_product`
                    WHERE `id_product` = ' . $product_id;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    /* Delete custom hooks */

    public function getThisModuleUnpositionedHooks()
    {
        $hook_ids = [];
        $hook_ids_pre = $this->getThisModuleHooks();
        if ($hook_ids_pre) {
            foreach ($hook_ids_pre as $hook_id) {
                $hook_ids[] = $hook_id['id_hook'];
            }

            $hook_ids_string = implode(',', $hook_ids);

            $untouchables = $this->getHooksModules($hook_ids_string);
            if ($untouchables) {
                $new_untouchables = [];
                foreach ($untouchables as $id_hook) {
                    $new_untouchables[] = $id_hook['id_hook'];
                }
                $untouchables = HelperFaq::sanitizeIntsFromArray($new_untouchables);

                $query2 = 'SELECT `id_hook`, `hook_name`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_hooks` ' .
                    "WHERE `id_hook` NOT IN ($untouchables)";

                try {
                    $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query2);
                } catch (PrestaShopDatabaseException $e) {
                    echo $e->getMessage();
                }
            } else {
                $query2 = 'SELECT `id_hook`, `hook_name`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_hooks`';

                try {
                    $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query2);
                } catch (PrestaShopDatabaseException $e) {
                    echo $e->getMessage();
                }
            }
        } else {
            $sql = false;
        }

        return $sql;
    }

    public function getThisModuleHooks()
    {
        $query = 'SELECT `id_hook`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_hooks`';

        try {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (Throwable $e) {
            echo $e->getMessage();
            $result = false;
        }

        return $result;
    }

    public function deleteCustomHooks()
    {
        $res = true;
        $hook_ids_pre = $this->getThisModuleHooks();
        if ($hook_ids_pre) {
            $hook_ids = [];
            foreach ($hook_ids_pre as $hook_id) {
                $hook_ids[] = $hook_id['id_hook'];
            }
            if (!empty($hook_ids)) {
                $hook_ids_string = HelperFaq::sanitizeIntsFromArray($hook_ids);

                $res &= Db::getInstance()->delete('hook_module', "id_hook IN ($hook_ids_string)");
                $res &= Db::getInstance()->delete('hook', "id_hook IN ($hook_ids_string)");
            }
        }

        return $res;
    }

    public function deleteCustomHook($id)
    {
        $id = (int) $id;
        $res = false;
        if (!$this->getHooksModules($id)) {
            $res = Db::getInstance()->delete('op_faq_hooks', "id_hook = $id");
            $res &= Db::getInstance()->delete('hook', "id_hook = $id");
        }

        return $res;
    }

    public function getHooksModules($hook_ids)
    {
        $res = false;
        $hook_ids = HelperFaq::sanitizeIntsFromString($hook_ids);
        $id_shop = Context::getContext()->shop->id;

        if (!empty($hook_ids)) {
            $query = 'SELECT `id_hook`, `id_module`
                    FROM  `' . _DB_PREFIX_ . 'hook_module`
                    WHERE `id_hook` IN(' . $hook_ids . ')
                    AND `id_shop` = ' . (int) $id_shop;

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getHooksModulesNames()
    {
        $hook_ids = [];
        $hook_ids_pre = $this->getThisModuleHooks();
        if ($hook_ids_pre) {
            foreach ($hook_ids_pre as $hook_id) {
                $hook_ids[] = $hook_id['id_hook'];
            }
            $hook_ids_string = implode(',', $hook_ids);
            $basic_array = $this->getHooksModules($hook_ids_string);
            $names_array = [];
            if ($basic_array) {
                foreach ($basic_array as $key => $pair) {
                    try {
                        if ($module_name_check = $this->getModuleNameOp($pair['id_module'])) {
                            $names_array[Hook::getNameById($pair['id_hook'])][$key]['module_name'] = $module_name_check;
                        } else {
                            $names_array[Hook::getNameById($pair['id_hook'])][$key]['module_name'] = '';
                        }
                    } catch (PrestaShopObjectNotFoundException $e) {
                        echo $e->getMessage();
                    }
                }

                return $names_array;
            }
        }

        return false;
    }

    public function getModuleNameOp($id)
    {
        $id = (int) $id;
        $query = 'SELECT `name`
                    FROM  `' . _DB_PREFIX_ . 'module`
                    WHERE `id_module` = ' . $id;
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) {
            return $row;
        }

        return false;
    }

    public function addCustomHookToTable($hook_name)
    {
        try {
            $hook_id = $this->module->helper->getHookIdByName($hook_name);

            return Db::getInstance()->insert(
                'op_faq_hooks',
                [['id_hook' => (int) $hook_id,
                    'hook_name' => pSQL($hook_name)],
                ]
            );
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function getNextPositionNumber($db, $block_type, $id_block)
    {
        $sql = '
			SELECT MAX(`position`)
			FROM `' . _DB_PREFIX_ . bqSQL($db) . '` 
			WHERE id_block = ' . (int) $id_block . '
			AND block_type = "' . pSQL($block_type) . '"';

        try {
            $result = Db::getInstance()->ExecuteS($sql)[0]['MAX(`position`)'];
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return ++$result;
    }

    public function updateBlocksPositionsAjax($block_type, $item_id, $position)
    {
        $item_id = (int) $item_id;
        $position = (int) $position;
        $sql = Db::getInstance()->update(
            'op_faq_' . bqSQL($block_type) . 's',
            ['position' => $position],
            "id = {$item_id}"
        );
        $sql &= Db::getInstance()->update(
            'op_faq_lists_cache',
            ['position' => $position],
            "id_block = {$item_id} AND block_type = '" . pSQL($block_type) . "'"
        );

        return $sql;
    }

    /* profile language titles regeneration */
    public function getListsIds($op_type)
    {
        $query = 'SELECT `id`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($op_type) . 's`';

        try {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        $arrayProcessed = [];
        foreach ($result as $item) {
            $arrayProcessed[] = $item['id'];
        }

        return $arrayProcessed;
    }

    public function hasProfileLang($id, $op_type)
    {
        $id_lang = Context::getContext()->language->id;
        $query = 'SELECT `id_lang`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($op_type) . 's_lang`
                    WHERE id = ' . (int) $id;

        try {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        foreach ($result as $lang) {
            if ($lang['id_lang'] == $id_lang) {
                return true;
            }
        }

        return false;
    }

    public function getBlocksIdsShop($block_type, $id_shop = null, $all_shops = false)
    {
        $shop_ids = [];
        if ($id_shop === null && !$all_shops) {
            $shop_ids = Shop::getContextListShopID();
        } elseif ($all_shops) {
            $shop_ids = Shop::getShops(false, null, true);
        } else {
            $shop_ids[] = $id_shop;
        }
        $arrayProcessed = [];
        foreach ($shop_ids as $id_shop) {
            $query = 'SELECT DISTINCT `id`
                    FROM  `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's` 
                    WHERE `id_shop` = ' . (int) $id_shop;

            try {
                $result = Db::getInstance()->executeS($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }

            foreach ($result as $item) {
                $arrayProcessed[] = $item['id'];
            }
        }

        return $arrayProcessed;
    }

    public function getMainShopId()
    {
        if (Configuration::get('PS_SSL_ENABLED')) {
            $url = Configuration::get('PS_SHOP_DOMAIN_SSL');
            $column = 'domain_ssl';
        } else {
            $url = Configuration::get('PS_SHOP_DOMAIN');
            $column = 'domain';
        }
        $query = new DbQuery();
        $query->select('`id_shop`');
        $query->from('shop_url');
        $query->where(bqSQL($column) . " = '" . pSQL($url) . "'");

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        if (!$res) {
            $query = new DbQuery();
            $query->select('`id_shop`');
            $query->from('shop_url');
            $query->where('main = 1');
            $query->where('active = 1');

            try {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function getBlockNameById($id, $block_type, $id_lang)
    {
        $sql = 'SELECT `title` 
            FROM ' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's_lang
            WHERE id = ' . (int) $id . ' 
            AND id_lang = ' . (int) $id_lang;

        try {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();

            return false;
        }
    }

    public function getItemNameById($id, $id_lang)
    {
        $sql = 'SELECT `title` 
            FROM ' . _DB_PREFIX_ . 'op_faq_items_lang
            WHERE id = ' . (int) $id . ' 
            AND id_lang = ' . (int) $id_lang;

        try {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();

            return false;
        }
    }

    public function deleteSingleBlock($module, $id, $type)
    {
        $factory = new OpFaqModelFactory();
        $block = $factory->makeBlock($module, $type, $id);
        $res = $block->delete();

        return $res;
    }

    public function deleteSingleItem($module, $id)
    {
        $factory = new OpFaqModelFactory();
        $item = $factory->makeItem($module, $id);
        $res = $item->delete();

        return $res;
    }

    public function publishSingleBlock($module, $id, $type)
    {
        $factory = new OpFaqModelFactory();
        $block = $factory->makeBlock($module, $type, $id);
        $res = $block->publishOne();

        return $res;
    }

    public function unpublishSingleBlock($module, $id, $type)
    {
        $factory = new OpFaqModelFactory();
        $block = $factory->makeBlock($module, $type, $id);
        $res = $block->unpublishOne();

        return $res;
    }

    public function updateBlocksForLanguages()
    {
        $res = true;
        $array = ConfigsFaq::BLOCK_TYPES;
        foreach ($array as $block_type) {
            $blocksIds = $this->getListsIds($block_type);
            $res &= $this->addLangIds($blocksIds, $block_type);
        }

        return (bool) $res;
    }

    protected function addLangIds($itemsIds, $op_type)
    {
        $res = true;
        $id_lang = Context::getContext()->language->id;
        foreach ($itemsIds as $itemsId) {
            if (!$this->hasProfileLang($itemsId, $op_type)) {
                try {
                    $res &= Db::getInstance()->insert(
                        'op_faq_' . bqSQL($op_type) . 's_lang',
                        [['id_lang' => (int) $id_lang,
                            'id' => (int) $itemsId],
                        ]
                    );
                } catch (PrestaShopDatabaseException $e) {
                    echo $e->getMessage();
                }
            }
        }

        return (bool) $res;
    }

    public function getContentBlock($block_id, $block_type)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $block_id = (int) $block_id;
        $block_type = pSQL($block_type);

        $sql = '
            SELECT *' .
            ' FROM `' . _DB_PREFIX_ . 'op_faq_lists_cache` 
            WHERE id_lang = ' . $id_lang . ' 
            AND id_block = ' . $block_id . ' 
            AND block_type = "' . $block_type . '"
            AND id_shop = ' . $id_shop;

        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return $query;
    }

    public function getItemsForListEdit($id_block, $block_type)
    {
        $id_lang = Context::getContext()->language->id;

        $items = [];

        try {
            $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT ofip.`id_item` as id, ofip.`position`, ofil.`title`
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item ofip
            LEFT JOIN ' . _DB_PREFIX_ . 'op_faq_items_lang ofil ON (ofip.id_item = ofil.id)
            WHERE ofip.id_block = ' . (int) $id_block . '
            AND ofip.block_type = "' . pSQL($block_type) . '"
            AND ofil.id_lang = ' . (int) $id_lang . '
            ORDER BY ofip.position');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $items;
    }

    public function removeOneItemFromBlockDb($id_item, $id_block, $block_type)
    {
        $id_block = (int) $id_block;
        $id_item = (int) $id_item;
        $block_type = pSQL($block_type);
        $res = true;
        $res &= Db::getInstance()->delete('op_faq_block_item', "id_block = $id_block AND id_item = $id_item 
        AND block_type = '$block_type'");

        return $res;
    }

    public function addItemToBlock($id_item, $id_block, $block_type)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $position = (int) $this->getNextPositionNumber('op_faq_block_item', $block_type, $id_block);
        $res = true;

        try {
            $res &= Db::getInstance()->insert(
                'op_faq_block_item',
                [['id_item' => (int) $id_item,
                    'position' => $position,
                    'id_block' => (int) $id_block,
                    'block_type' => pSQL($block_type),
                    'id_shop' => $id_shop],
                ]
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res &= $this->module->cache_helper->addOneBlockToCacheTable($id_block, $block_type);

        return $res;
    }

    /*
     * Update items positions in Db in block
     */
    public function updateItemsPositionsAjaxBlock($item_id, $position, $id_block, $block_type)
    {
        $item_id = (int) $item_id;
        $position = (int) $position;
        $block_type = pSQL($block_type);
        $sql = Db::getInstance()->update(
            'op_faq_block_item',
            ['position' => $position],
            "id_item = {$item_id} AND id_block = {$id_block}
             AND block_type = '$block_type'"
        );
        $sql &= $this->module->cache_helper->addOneBlockToCacheTable($id_block, $block_type);

        return $sql;
    }

    public function removeAllBlocksFromItemDb($id_item, $op_type)
    {
        $id_item = (int) $id_item;
        $id_shop = (int) Context::getContext()->shop->id;
        $res = true;
        $res &= Db::getInstance()->delete('op_faq_block_item', "id_item = $id_item 
        AND block_type = '" . pSQL($op_type) . "'
        AND id_shop = $id_shop");

        return $res;
    }

    public function removeAllBlocksFromItemDbWhenDelete($id_item)
    {
        $id_item = (int) $id_item;
        $res = true;
        $res &= Db::getInstance()->delete('op_faq_block_item', "id_item = $id_item");

        return $res;
    }

    public function getBlocksForItemBind($id_item)
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;
        $blocks = [];

        $sql = '
            SELECT obi.`id` as id, obi.`hook_name`, obi.`block_type`, obil.`title` as title 
            FROM ' . _DB_PREFIX_ . 'op_faq_lists obi
            LEFT JOIN ' . _DB_PREFIX_ . 'op_faq_lists_lang obil 
            ON (obi.id = obil.id) 
            WHERE obi.id_shop = ' . (int) $id_shop . '
            AND obil.id_lang = ' . (int) $id_lang;

        try {
            $blocks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        foreach ($blocks as $key => $row) {
            if ($row['id'] == Tools::getValue('id_list') && $row['block_type'] == Tools::getValue('op_type')) {
                $blocks[$key]['checked'] = 1;
            } else {
                $belongs = $this->itemBelongsToBlock($id_item, $row['id'], 'list');
                if ($belongs) {
                    $blocks[$key]['checked'] = 1;
                } else {
                    $blocks[$key]['checked'] = 0;
                }
            }
        }

        return $blocks;
    }

    public function getAllBlocksForItem($id_item)
    {
        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_block`
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE block_type = "list"
            AND id_item = ' . (int) $id_item);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res = [];
        foreach ($rows as $row) {
            $res[] = $row['id_block'];
        }

        return $res;
    }

    public function getAllBlocksForItemByShop($id_item)
    {
        $id_shop = Context::getContext()->shop->id;

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_block`
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE block_type = "list"
            AND id_item = ' . (int) $id_item . '
            AND id_shop = ' . (int) $id_shop);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res = [];
        foreach ($rows as $row) {
            $res[] = $row['id_block'];
        }

        return $res;
    }

    public function itemBelongsToBlock($id_item, $id_block, $block_type)
    {
        $id_shop = Context::getContext()->shop->id;

        $res = false;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(*)
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE block_type = "' . pSQL($block_type) . '"
            AND id_block = ' . (int) $id_block . '
            AND id_item = ' . (int) $id_item . '
            AND id_shop = ' . (int) $id_shop);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function itemBelongsToPage($id_item)
    {
        $id_shop = Context::getContext()->shop->id;

        $res = false;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(*)
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE block_type = "page"
            AND id_item = ' . (int) $id_item . '
            AND id_shop = ' . (int) $id_shop);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function itemBelongsToPageWithoutShop($id_item)
    {
        $res = false;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(*)
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE block_type = "page"
            AND id_item = ' . (int) $id_item);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function removeAllItemsFromBlock($id_block, $block_type)
    {
        $id_block = (int) $id_block;
        $res = true;
        $res &= Db::getInstance()->delete('op_faq_block_item', "id_block = $id_block 
        AND block_type='" . pSQL($block_type) . "'");

        return $res;
    }

    public function getMetaPageId()
    {
        $req = 'SELECT `id_meta`
                FROM `' . _DB_PREFIX_ . "meta`
                WHERE `page` = '" . pSQL(ConfigsFaq::PAGE) . "'";
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

        return $row ? $row['id_meta'] : 0;
    }

    public function addItemToPage($id_item, $id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $id_block = $this->getPageIdByShop($id_shop);

        $res = true;

        try {
            $res &= Db::getInstance()->insert(
                'op_faq_block_item',
                [['id_item' => (int) $id_item,
                    'position' => 0,
                    'id_block' => (int) $id_block,
                    'block_type' => 'page',
                    'id_shop' => $id_shop],
                ]
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res &= $this->module->cache_helper->addOneBlockToCacheTable($id_block, 'page');

        return $res;
    }

    public function getPageIdByShop($id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }
        $res = false;

        try {
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT id
            FROM ' . _DB_PREFIX_ . 'op_faq_pages
            WHERE id_shop = ' . (int) $id_shop);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function getPageIdsAll()
    {
        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT id
            FROM ' . _DB_PREFIX_ . 'op_faq_pages');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res = [];
        foreach ($rows as $row) {
            $res[] = $row['id'];
        }

        return $res;
    }

    public function getBlockIdsAll($block_type)
    {
        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT id
            FROM ' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $res = [];
        foreach ($rows as $row) {
            $res[] = $row['id'];
        }

        return $res;
    }

    public function customerGroupExists($id, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('group', 'g');
        $sql->join('JOIN ' . _DB_PREFIX_ . 'group_shop gs ON `gs`.`id_group` = `g`.`id_group`');
        $sql->where('`g`.`id_group` = ' . (int) $id);
        $sql->where('`gs`.`id_shop` = ' . (int) $id_shop);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}
