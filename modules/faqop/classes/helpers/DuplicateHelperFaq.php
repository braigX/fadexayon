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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';

class DuplicateHelperFaq extends HelperFaq
{
    public function shopHookDataDuplication($params)
    {
        $array = ConfigsFaq::BLOCK_TYPES;
        foreach ($array as $block_type) {
            $blocksIds = $this->module->rep->getBlocksIdsShop($block_type, (int) $params['old_id_shop']);
            foreach ($blocksIds as $blockId) {
                $this->duplicateOneBlock($blockId, $block_type, $params['old_id_shop'], $params['new_id_shop']);
            }
        }
        $this->duplicateMetaPage($params);
    }

    public function duplicateOneBlockPre($blockId, $block_type, $old_id_shop, $new_id_shop)
    {
        if ($block_type == 'page') {
            return $this->duplicateOnePage($blockId, $old_id_shop, $new_id_shop);
        }

        return $this->duplicateOneBlock($blockId, $block_type, $old_id_shop, $new_id_shop);
    }

    public function duplicateOnePage($blockId, $old_id_shop, $new_id_shop)
    {
        $res = true;

        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'op_faq_pages 
                WHERE id = ' . (int) $blockId . ' 
                AND id_shop = ' . (int) $old_id_shop;

        try {
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        $res &= $this->createOrUpdatePage(
            $new_id_shop,
            $row['show_title'],
            $row['show_description'],
            $row['show_markup'],
            $row['accordion'],
            null,
            null,
            $row['block_tag'],
            $row['block_class'],
            $row['title_tag'],
            $row['title_class'],
            $row['content_tag'],
            $row['content_class'],
            $row['item_tag'],
            $row['item_class'],
            $row['question_tag'],
            $row['question_class'],
            $row['answer_tag'],
            $row['answer_class'],
            $row['description_tag'],
            $row['description_class']
        );

        $newId = $this->module->rep->getPageIdByShop($new_id_shop);

        /* Add to languages */
        $res &= $this->addBlockToLanguages($blockId, $newId, 'page', $old_id_shop, $new_id_shop);

        /*
         * Duplicate items in block
         */
        $res &= $this->duplicateItemsBoundToBlock($blockId, $newId, 'page', $old_id_shop, $new_id_shop);

        // generate index
        $res &= $this->module->cache_helper->addOneBlockToCacheTable($newId, 'page');

        return $res;
    }

    public function duplicateOneBlock($blockId, $block_type, $old_id_shop, $new_id_shop)
    {
        $res = true;

        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's 
                WHERE id = ' . (int) $blockId . ' 
                AND id_shop = ' . (int) $old_id_shop;

        try {
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        $insertArray = [
            'active' => 0,
            'hook_name' => pSQL($row['hook_name']),
            'block_type' => pSQL($row['block_type']),
            'id_shop' => (int) $new_id_shop,
            'block_tag' => pSQL($row['block_tag']),
            'block_class' => pSQL($row['block_class']),
            'title_tag' => pSQL($row['title_tag']),
            'title_class' => pSQL($row['title_class']),
            'content_tag' => pSQL($row['content_tag']),
            'content_class' => pSQL($row['content_class']),
            'item_tag' => pSQL($row['item_tag']),
            'item_class' => pSQL($row['item_class']),
            'question_tag' => pSQL($row['question_tag']),
            'question_class' => pSQL($row['question_class']),
            'answer_tag' => pSQL($row['answer_tag']),
            'answer_class' => pSQL($row['answer_class']),
            'accordion' => (int) $row['accordion'],
        ];

        if (isset($row['position'])) {
            $insertArray['position'] = (int) $row['position'];
        }

        if (isset($row['admin_name'])) {
            $insertArray['admin_name'] = pSQL($row['admin_name']);
        }

        if (isset($row['not_all_languages'])) {
            $insertArray['not_all_languages'] = (int) $row['not_all_languages'];
        }

        if (isset($row['languages'])) {
            $insertArray['languages'] = pSQL($row['languages']);
        }

        if (isset($row['not_all_currencies'])) {
            $insertArray['not_all_currencies'] = (int) $row['not_all_currencies'];
        }

        if (isset($row['currencies'])) {
            $insertArray['currencies'] = pSQL($row['currencies']);
        }

        if (isset($row['not_all_customer_groups'])) {
            $insertArray['not_all_customer_groups'] = (int) $row['not_all_customer_groups'];
        }

        if (isset($row['customer_groups'])) {
            $insertArray['customer_groups'] = pSQL($row['customer_groups']);
        }

        if (isset($row['not_all_pages'])) {
            $insertArray['not_all_pages'] = (int) $row['not_all_pages'];
        }

        if (isset($row['all_products'])) {
            $insertArray['all_products'] = (int) $row['all_products'];
        }
        if (isset($row['select_products_by_id'])) {
            $insertArray['select_products_by_id'] = (int) $row['select_products_by_id'];
        }
        if (isset($row['product_ids'])) {
            $insertArray['product_ids'] = pSQL($row['product_ids']);
        }
        if (isset($row['select_products_by_category'])) {
            $insertArray['select_products_by_category'] = (int) $row['select_products_by_category'];
        }
        if (isset($row['category_ids_p'])) {
            $insertArray['category_ids_p'] = pSQL($row['category_ids_p']);
        }
        if (isset($row['is_only_default_category'])) {
            $insertArray['is_only_default_category'] = (int) $row['is_only_default_category'];
        }
        if (isset($row['select_products_by_brand'])) {
            $insertArray['select_products_by_brand'] = (int) $row['select_products_by_brand'];
        }
        if (isset($row['brand_ids_p'])) {
            $insertArray['brand_ids_p'] = pSQL($row['brand_ids_p']);
        }
        if (isset($row['select_products_by_tag'])) {
            $insertArray['select_products_by_tag'] = (int) $row['select_products_by_tag'];
        }
        if (isset($row['tag_ids_p'])) {
            $insertArray['tag_ids_p'] = pSQL($row['tag_ids_p']);
        }
        if (isset($row['select_products_by_feature'])) {
            $insertArray['select_products_by_feature'] = (int) $row['select_products_by_feature'];
        }
        if (isset($row['feature_ids_p'])) {
            $insertArray['feature_ids_p'] = pSQL($row['feature_ids_p']);
        }
        if (isset($row['all_categories'])) {
            $insertArray['all_categories'] = (int) $row['all_categories'];
        }
        if (isset($row['category_ids'])) {
            $insertArray['category_ids'] = pSQL($row['category_ids']);
        }
        if (isset($row['all_brands'])) {
            $insertArray['all_brands'] = (int) $row['all_brands'];
        }
        if (isset($row['brand_ids'])) {
            $insertArray['brand_ids'] = pSQL($row['brand_ids']);
        }
        if (isset($row['all_cms_pages'])) {
            $insertArray['all_cms_pages'] = (int) $row['all_cms_pages'];
        }
        if (isset($row['cms_page_ids'])) {
            $insertArray['cms_page_ids'] = pSQL($row['cms_page_ids']);
        }
        if (isset($row['all_without'])) {
            $insertArray['all_without'] = (int) $row['all_without'];
        }
        if (isset($row['special_ids'])) {
            $insertArray['special_ids'] = pSQL($row['special_ids']);
        }

        if (isset($row['description'])) {
            $insertArray['description'] = pSQL($row['description'], true);
        }
        if (isset($row['show_title'])) {
            $insertArray['show_title'] = (int) $row['show_title'];
        }
        if (isset($row['show_description'])) {
            $insertArray['show_description'] = (int) $row['show_description'];
        }

        if (isset($row['description_tag'])) {
            $insertArray['description_tag'] = pSQL($row['description_tag']);
        }

        if (isset($row['description_class'])) {
            $insertArray['description_class'] = pSQL($row['description_class']);
        }

        try {
            $res &= Db::getInstance()->insert(
                'op_faq_' . bqSQL($block_type) . 's',
                [$insertArray],
                false,
                false,
                Db::INSERT_IGNORE
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $newId = (int) Db::getInstance()->Insert_ID();

        /* Add to languages */
        $res &= $this->addBlockToLanguages($blockId, $newId, $block_type, $old_id_shop, $new_id_shop);

        /*
         * Duplicate items in block
         */
        $res &= $this->duplicateItemsBoundToBlock($blockId, $newId, $block_type, $old_id_shop, $new_id_shop);

        // we do not generate cache here because lists are duplicated inactive

        return $res;
    }

    public function makeCloneTitle($title, $old_shop = null, $new_shop = null)
    {
        if ($old_shop == $new_shop) {
            $title .= ' (c)';
        }

        return $title;
    }

    public function duplicateOneItem($itemId)
    {
        $res = true;

        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'op_faq_items 
                WHERE id = ' . (int) $itemId;

        try {
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        $insertArray = [
            'i_class' => pSQL($row['i_class']),
            'q_class' => pSQL($row['q_class']),
            'a_class' => pSQL($row['a_class']),
        ];

        try {
            $res &= Db::getInstance()->insert(
                'op_faq_items',
                [$insertArray],
                false,
                false,
                Db::INSERT_IGNORE
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $newId = (int) Db::getInstance()->Insert_ID();

        /* Add to languages */
        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'op_faq_items_lang 
                WHERE id = ' . (int) $itemId;

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        /* Items lang */
        foreach ($rows as $row) {
            $insertArray = [
                'id' => (int) $newId,
                'id_lang' => (int) $row['id_lang'],
                'title' => pSQL($this->makeCloneTitle($row['title'])),
                'question' => pSQL($row['question']),
                'answer' => pSQL($row['answer']),
            ];

            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_items_lang',
                    [$insertArray],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function duplicateMetaPage($params)
    {
        $metaId = $this->module->rep->getMetaPageId();

        $sql = 'SELECT ml.id_lang, ml.title, ml.description, 
       ml.keywords, ml.url_rewrite
            FROM ' . _DB_PREFIX_ . 'meta_lang ml
            WHERE ml.id_meta = ' . (int) $metaId . ' 
            AND ml.id_shop = ' . (int) $params['old_id_shop'];

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        foreach ($rows as $row) {
            try {
                Db::getInstance()->insert(
                    'meta_lang',
                    [[
                        'id_meta' => (int) $metaId,
                        'id_shop' => (int) $params['new_id_shop'],
                        'id_lang' => (int) $row['id_lang'],
                        'title' => pSQL($row['title']),
                        'description' => pSQL($row['description']),
                        'keywords' => pSQL($row['keywords']),
                        'url_rewrite' => pSQL($row['url_rewrite']),
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

    public function addBlockToLanguages($oldBlockId, $newBlockId, $block_type, $old_id_shop, $new_id_shop)
    {
        $res = true;

        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's_lang 
                WHERE id = ' . (int) $oldBlockId;

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        if ($block_type == 'page') {
            $res &= Db::getInstance()->delete(
                'op_faq_' . bqSQL($block_type) . 's_lang',
                'id = ' . (int) $newBlockId
            );
        }

        /* Blocks lang */
        foreach ($rows as $row) {
            if ($block_type == 'list') {
                $title = pSQL($this->makeCloneTitle($row['title'], $old_id_shop, $new_id_shop));
            } else {
                $title = pSQL($row['title']);
            }
            $insertArray = [
                'id' => (int) $newBlockId,
                'id_lang' => (int) $row['id_lang'],
                'title' => $title,
            ];
            if ($block_type == 'page') {
                $insertArray['description'] = pSQL($row['description']);
            }

            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_' . bqSQL($block_type) . 's_lang',
                    [$insertArray],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    public function duplicateItemsBoundToBlock($oldBlockId, $newBlockId, $block_type, $old_id_shop, $new_id_shop)
    {
        $res = true;

        if ($block_type == 'page') {
            $res &= Db::getInstance()->delete(
                'op_faq_block_item',
                'id_block = ' . (int) $newBlockId . " AND block_type='" . pSQL($block_type) . "'"
            );
        }

        $sql = '
            SELECT *
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item
            WHERE id_shop = ' . (int) $old_id_shop . '
            AND id_block = ' . (int) $oldBlockId . '
            AND block_type = "' . pSQL($block_type) . '"';

        try {
            $rows = Db::getInstance()->ExecuteS($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        foreach ($rows as $row) {
            $insertArray = [
                'id_item' => (int) $row['id_item'],
                'position' => (int) $row['position'],
                'id_block' => (int) $newBlockId,
                'block_type' => pSQL($row['block_type']),
                'id_shop' => (int) $new_id_shop,
            ];

            try {
                $res &= Db::getInstance()->insert(
                    'op_faq_block_item',
                    [$insertArray],
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }
}
