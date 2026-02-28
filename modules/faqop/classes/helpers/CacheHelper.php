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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';

class CacheHelper extends BasicHelper
{
    public function __construct($module)
    {
        parent::__construct($module);
    }

    public function addOneBlockToCacheTable($id, $block_type)
    {
        $res = true;
        $res &= $this->deleteOneBlockFromCacheTable($id, $block_type);

        $data = $this->doSelectQueryOneBlock($id, $block_type);
        $res &= $this->insertIntoCacheTable($data);

        return $res;
    }

    // delete one block
    public function deleteOneBlockFromCacheTable($id, $block_type)
    {
        $id = (int) $id;
        $block_type = pSQL($block_type);
        $res = Db::getInstance()->delete('op_faq_lists_cache', "id_block = $id 
            AND block_type = '{$block_type}'");

        return $res;
    }

    public function deleteManyBlocksFromCacheTable($blockIds, $block_type)
    {
        $blockIds = HelperFaq::sanitizeIntsFromArray($blockIds);
        $block_type = pSQL($block_type);

        $where_clause = '';
        if (!empty($blockIds)) {
            $where_clause .= "id_block IN($blockIds) AND";
        }
        $where_clause .= " block_type = '{$block_type}'";

        $res = Db::getInstance()->delete('op_faq_lists_cache', $where_clause);

        return $res;
    }

    // button recache pressed (works for all shops)
    public function recacheAllLists()
    {
        $res = true;
        $array = ConfigsFaq::BLOCK_TYPES;
        foreach ($array as $block_type) {
            $blocksIds = $this->module->rep->getBlockIdsAll($block_type);
            $res &= $this->recacheManyLists($blocksIds, $block_type);
        }

        return $res;
    }

    // this is for all shops, when item fully deleted
    public function recacheListsForItem($id_item)
    {
        $res = true;

        $array = ConfigsFaq::BLOCK_TYPES;
        foreach ($array as $block_type) {
            $res &= $this->recacheListsForItemByType($id_item, $block_type);
        }

        return $res;
    }

    public function recacheListsForItemByType($id_item, $block_type)
    {
        $res = true;
        // recache pages
        if ($block_type == 'page' && $this->module->rep->itemBelongsToPageWithoutShop($id_item)) {
            $pageIds = $this->module->rep->getPageIdsAll();
            $res &= $this->recacheManyLists($pageIds, 'page');
        }

        if ($block_type == 'list') {
            // recache lists
            $blockIds = $this->module->rep->getAllBlocksForItem($id_item);
            $res &= $this->recacheManyLists($blockIds, 'list');
        }

        return $res;
    }

    public function recacheListsForItemByTypeAndShop($id_item, $block_type)
    {
        $res = true;
        // recache pages
        if ($block_type == 'page' && $this->module->rep->itemBelongsToPage($id_item)) {
            $pageIds = $this->module->rep->getPageIdByShop();
            $res &= $this->recacheManyLists($pageIds, 'page');
        }

        if ($block_type == 'list') {
            // recache lists
            $blockIds = $this->module->rep->getAllBlocksForItemByShop($id_item);
            $res &= $this->recacheManyLists($blockIds, 'list');
        }

        return $res;
    }

    public function recacheManyLists($blockIds, $block_type)
    {
        $res = true;

        if (!empty($blockIds)) {
            $res &= $this->deleteManyBlocksFromCacheTable($blockIds, $block_type);

            $data = $this->doSelectQueryManyBlocks($blockIds, $block_type);

            $res &= $this->insertIntoCacheTable($data);
        }

        return $res;
    }

    public function selectQueryBasic($block_type)
    {
        return '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's` obb
			INNER JOIN `' . _DB_PREFIX_ . 'op_faq_' . bqSQL($block_type) . 's_lang` obl
			USING (id)
			WHERE obb.block_type = "' . pSQL($block_type) . '"
			AND obb.active = 1';
    }

    public function doSelectQueryManyBlocks($ids, $block_type)
    {
        $sql = $this->selectQueryBasic($block_type);
        $ids = HelperFaq::sanitizeIntsFromArray($ids);
        $sql .= ' AND obb.id IN (' . $ids . ')';

        try {
            $blocks = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        } catch (PrestaShopDatabaseException $e) {
            $this->logger->logInfo($e->getMessage());
        }

        return $blocks;
    }

    public function doSelectQueryOneBlock($id, $block_type)
    {
        $ids = [$id];

        return $this->doSelectQueryManyBlocks($ids, $block_type);
    }

    public function insertIntoCacheTable($data)
    {
        $res = true;

        $lists = $this->processDataForCache($data);

        if (!empty($lists)) {
            try {
                $res &= Db::getInstance()->insert('op_faq_lists_cache', $lists);
            } catch (PrestaShopDatabaseException $e) {
                $this->logger->logInfo($e->getMessage());
            }
        }

        return $res;
    }

    public function processDataForCache($data)
    {
        $final_array = [];

        foreach ($data as $key => $item) {
            $final_array[$key] =
                [
                    'title' => pSQL($item['title']),
                    'hook_name' => pSQL($item['hook_name']),
                    'id_block' => (int) $item['id'],
                    'block_type' => pSQL($item['block_type']),
                    'id_lang' => (int) $item['id_lang'],
                    'id_shop' => (int) $item['id_shop'],
                    'block_tag' => pSQL($item['block_tag']),
                    'block_class' => pSQL($item['block_class']),
                    'title_tag' => pSQL($item['title_tag']),
                    'title_class' => pSQL($item['title_class']),
                    'content_tag' => pSQL($item['content_tag']),
                    'content_class' => pSQL($item['content_class']),
                    'item_tag' => pSQL($item['item_tag']),
                    'item_class' => pSQL($item['item_class']),
                    'question_tag' => pSQL($item['question_tag']),
                    'question_class' => pSQL($item['question_class']),
                    'answer_tag' => pSQL($item['answer_tag']),
                    'answer_class' => pSQL($item['answer_class']),
                    'show_title' => (int) $item['show_title'],
                    'show_markup' => (int) $item['show_markup'],
                    'accordion' => (int) $item['accordion'],
                ];

            if (isset($item['position'])) {
                $final_array[$key]['position'] = (int) $item['position'];
            } else {
                $final_array[$key]['position'] = 0;
            }

            if (isset($item['not_all_languages'])) {
                $final_array[$key]['not_all_languages'] = (int) $item['not_all_languages'];
            } else {
                $final_array[$key]['not_all_languages'] = 0;
            }

            if (isset($item['languages'])) {
                $final_array[$key]['languages'] = pSQL($item['languages']);
            } else {
                $final_array[$key]['languages'] = '';
            }

            if (isset($item['not_all_currencies'])) {
                $final_array[$key]['not_all_currencies'] = (int) $item['not_all_currencies'];
            } else {
                $final_array[$key]['not_all_currencies'] = 0;
            }

            if (isset($item['currencies'])) {
                $final_array[$key]['currencies'] = pSQL($item['currencies']);
            } else {
                $final_array[$key]['currencies'] = '';
            }

            if (isset($item['not_all_customer_groups'])) {
                $final_array[$key]['not_all_customer_groups'] = (int) $item['not_all_customer_groups'];
            } else {
                $final_array[$key]['not_all_customer_groups'] = 0;
            }

            if (isset($item['customer_groups'])) {
                $final_array[$key]['customer_groups'] = pSQL($item['customer_groups']);
            } else {
                $final_array[$key]['customer_groups'] = '';
            }

            if (isset($item['not_all_pages'])) {
                $final_array[$key]['not_all_pages'] = (int) $item['not_all_pages'];
            } else {
                $final_array[$key]['not_all_pages'] = 0;
            }
            if (isset($item['all_products'])) {
                $final_array[$key]['all_products'] = (int) $item['all_products'];
            } else {
                $final_array[$key]['all_products'] = 0;
            }
            if (isset($item['select_products_by_id'])) {
                $final_array[$key]['select_products_by_id'] = (int) $item['select_products_by_id'];
            } else {
                $final_array[$key]['select_products_by_id'] = 0;
            }
            if (isset($item['product_ids'])) {
                $final_array[$key]['product_ids'] = pSQL($item['product_ids']);
            } else {
                $final_array[$key]['product_ids'] = '';
            }
            if (isset($item['select_products_by_category'])) {
                $final_array[$key]['select_products_by_category'] = (int) $item['select_products_by_category'];
            } else {
                $final_array[$key]['select_products_by_category'] = 0;
            }
            if (isset($item['category_ids_p'])) {
                $final_array[$key]['category_ids_p'] = pSQL($item['category_ids_p']);
            } else {
                $final_array[$key]['category_ids_p'] = '';
            }
            if (isset($item['is_only_default_category'])) {
                $final_array[$key]['is_only_default_category'] = (int) $item['is_only_default_category'];
            } else {
                $final_array[$key]['is_only_default_category'] = 0;
            }
            if (isset($item['select_products_by_brand'])) {
                $final_array[$key]['select_products_by_brand'] = (int) $item['select_products_by_brand'];
            } else {
                $final_array[$key]['select_products_by_brand'] = 0;
            }
            if (isset($item['brand_ids_p'])) {
                $final_array[$key]['brand_ids_p'] = pSQL($item['brand_ids_p']);
            } else {
                $final_array[$key]['brand_ids_p'] = '';
            }
            if (isset($item['select_products_by_tag'])) {
                $final_array[$key]['select_products_by_tag'] = (int) $item['select_products_by_tag'];
            } else {
                $final_array[$key]['select_products_by_tag'] = 0;
            }
            if (isset($item['tag_ids_p'])) {
                $final_array[$key]['tag_ids_p'] = pSQL($item['tag_ids_p']);
            } else {
                $final_array[$key]['tag_ids_p'] = '';
            }
            if (isset($item['select_products_by_feature'])) {
                $final_array[$key]['select_products_by_feature'] = (int) $item['select_products_by_feature'];
            } else {
                $final_array[$key]['select_products_by_feature'] = 0;
            }
            if (isset($item['feature_ids_p'])) {
                $final_array[$key]['feature_ids_p'] = pSQL($item['feature_ids_p']);
            } else {
                $final_array[$key]['feature_ids_p'] = '';
            }
            if (isset($item['all_categories'])) {
                $final_array[$key]['all_categories'] = (int) $item['all_categories'];
            } else {
                $final_array[$key]['all_categories'] = 0;
            }
            if (isset($item['category_ids'])) {
                $final_array[$key]['category_ids'] = pSQL($item['category_ids']);
            } else {
                $final_array[$key]['category_ids'] = '';
            }
            if (isset($item['all_brands'])) {
                $final_array[$key]['all_brands'] = (int) $item['all_brands'];
            } else {
                $final_array[$key]['all_brands'] = 0;
            }
            if (isset($item['brand_ids'])) {
                $final_array[$key]['brand_ids'] = pSQL($item['brand_ids']);
            } else {
                $final_array[$key]['brand_ids'] = '';
            }
            if (isset($item['all_cms_pages'])) {
                $final_array[$key]['all_cms_pages'] = (int) $item['all_cms_pages'];
            } else {
                $final_array[$key]['all_cms_pages'] = 0;
            }
            if (isset($item['cms_page_ids'])) {
                $final_array[$key]['cms_page_ids'] = pSQL($item['cms_page_ids']);
            } else {
                $final_array[$key]['cms_page_ids'] = '';
            }
            if (isset($item['all_without'])) {
                $final_array[$key]['all_without'] = (int) $item['all_without'];
            } else {
                $final_array[$key]['all_without'] = 0;
            }
            if (isset($item['special_ids'])) {
                $final_array[$key]['special_ids'] = pSQL($item['special_ids']);
            } else {
                $final_array[$key]['special_ids'] = '';
            }

            if (isset($item['description'])) {
                $final_array[$key]['description'] = pSQL($item['description'], true);
            } else {
                $final_array[$key]['description'] = '';
            }
            if (isset($item['description_tag'])) {
                $final_array[$key]['description_tag'] = pSQL($item['description_tag']);
            } else {
                $final_array[$key]['description_tag'] = 'div';
            }
            if (isset($item['description_class'])) {
                $final_array[$key]['description_class'] = pSQL($item['description_class']);
            } else {
                $final_array[$key]['description_class'] = '';
            }
            if (isset($item['show_description'])) {
                $final_array[$key]['show_description'] = (int) $item['show_description'];
            } else {
                $final_array[$key]['show_description'] = 0;
            }

            $final_array[$key]['content'] = pSQL(
                $this->packOneFaqList(
                    $item['id'],
                    $item['id_lang'],
                    $item['block_type'],
                    $item['id_shop']
                ),
                true
            );
        }

        return $final_array;
    }

    public function packOneFaqList($id_block, $id_lang, $block_type, $id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }
        $sql = '
            SELECT oil.`question`, oil.`answer`,
                   oi.`i_class`, oi.`q_class`, oi.`a_class`
            FROM ' . _DB_PREFIX_ . 'op_faq_block_item obi
            JOIN ' . _DB_PREFIX_ . 'op_faq_items oi 
            ON (obi.id_item = oi.id) 
            JOIN ' . _DB_PREFIX_ . 'op_faq_items_lang oil 
            ON (oil.id = oi.id) 
            WHERE obi.id_shop = ' . (int) $id_shop . '
            AND obi.id_block = ' . (int) $id_block . '
            AND obi.block_type = "' . pSQL($block_type) . '" 
            AND oil.id_lang = ' . (int) $id_lang . '
            ORDER BY obi.`position`';

        try {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (Exception $e) {
            $this->logger->logInfo($e->getMessage());
        }

        return json_encode($rows);
    }
}
