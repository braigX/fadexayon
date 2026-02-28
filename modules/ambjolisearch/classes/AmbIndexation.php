<?php
/**
 *   AmbJoliSearch Module : Search for prestashop
 *
 *   @author    Ambris Informatique
 *   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
 *   @license   Licensed under the EUPL-1.2-or-later
 *
 *   @module     Advanced search (AmbJoliSearch)
 *
 *   @file       AmbIndexation.php
 *
 *   @subject    indexation
 *   Support by mail: support@ambris.com
 */
require_once 'AmbSearch.php';

class AmbIndexation
{
    protected $db;
    protected $weight_array = [];
    protected $step_size = 100;
    protected $cron = false;

    protected $index_suppliers = false;

    public $token = null;

    public function __construct($cron = false, $step_size = 100)
    {
        $this->cron = $cron;
        $this->step_size = $step_size;
        $this->db = Db::getInstance();

        $this->index_suppliers = Configuration::hasKey(AJS_INDEX_SUPPLIER) ? Configuration::get(AJS_INDEX_SUPPLIER) : false;
    }

    /**
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public function getTags($id_product, $id_lang)
    {
        $tags = '';
        $tagsArray = $this->db->executeS('
        SELECT t.name FROM ' . _DB_PREFIX_ . 'product_tag pt
        LEFT JOIN ' . _DB_PREFIX_ . 'tag t ON (pt.id_tag = t.id_tag AND t.id_lang = ' . (int) $id_lang . ')
        WHERE pt.id_product = ' . (int) $id_product, true, false);
        foreach ($tagsArray as $tag) {
            $tags .= $tag['name'] . ' ';
        }

        return $tags;
    }

    /**
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public function getAttributes($id_product, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return '';
        }

        $attributes = '';
        $attributesArray = $this->db->executeS('
        SELECT al.name FROM ' . _DB_PREFIX_ . 'product_attribute pa
        INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
        INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang = ' . (int) $id_lang . ')
        ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
        WHERE pa.id_product = ' . (int) $id_product, true, false);
        foreach ($attributesArray as $attribute) {
            $attributes .= $attribute['name'] . ' ';
        }

        return $attributes;
    }

    /**
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public function getFeatures($id_product, $id_lang)
    {
        if (!Feature::isFeatureActive()) {
            return '';
        }

        $features = '';
        $featuresArray = $this->db->executeS('
        SELECT fvl.value FROM ' . _DB_PREFIX_ . 'feature_product fp
        LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
        WHERE fp.id_product = ' . (int) $id_product, true, false);
        foreach ($featuresArray as $feature) {
            $features .= $feature['value'] . ' ';
        }

        return $features;
    }

    /**
     * @return string
     */
    protected function getSQLProductAttributeFields()
    {
        $sql = '';
        if (is_array($this->weight_array)) {
            foreach ($this->weight_array as $key => $weight) {
                if ((int) $weight) {
                    switch ($key) {
                        case 'pa_reference':
                            $sql .= ', pa.reference AS pa_reference';
                            break;
                        case 'pa_supplier_reference':
                            $sql .= ', pa.supplier_reference AS pa_supplier_reference';
                            break;
                        case 'pa_ean13':
                            $sql .= ', pa.ean13 AS pa_ean13';
                            break;
                        case 'pa_upc':
                            $sql .= ', pa.upc AS pa_upc';
                            break;
                    }
                }
            }
        }

        return $sql;
    }

    protected function getProductsToIndex($id_product = false, $step = false)
    {
        $limit = ($step === false ? '' : 'LIMIT 0,' . (int) $this->step_size);
        $query = 'SELECT DISTINCT p.id_product FROM ' . _DB_PREFIX_ . 'product p
                ' . Shop::addSqlAssociation('product', 'p', true, null, true) . '
                WHERE
                    product_shop.`visibility` IN ("both", "search")
                AND product_shop.`active` = 1
                AND product_shop.indexed = 0
                ' . $limit;

        $limited_products = Db::getInstance()->executeS($query);
        $limited_products_array = [0];
        foreach ($limited_products as $limited_product) {
            $limited_products_array[] = $limited_product['id_product'];
        }

        $sql = 'SELECT p.id_product, pl.id_lang, pl.id_shop, l.iso_code';

        if (is_array($this->weight_array)) {
            foreach ($this->weight_array as $key => $weight) {
                if ((int) $weight) {
                    switch ($key) {
                        case 'pname':
                            $sql .= ', pl.name pname';
                            break;
                        case 'reference':
                            $sql .= ', p.reference';
                            break;
                        case 'supplier_reference':
                            $sql .= ', p.supplier_reference';
                            break;
                        case 'ean13':
                            $sql .= ', p.ean13';
                            break;
                        case 'upc':
                            $sql .= ', p.upc';
                            break;
                        case 'description_short':
                            $sql .= ', pl.description_short';
                            break;
                        case 'description':
                            $sql .= ', pl.description';
                            break;
                        case 'cname':
                            $sql .= ', cl.name cname';
                            break;
                        case 'mname':
                            $sql .= ', m.name mname';
                            if ($this->index_suppliers) {
                                $sql .= ', su.name suname';
                            }
                            break;
                    }
                }
            }
        }

        $join_supplier = '';
        if ($this->index_suppliers) {
            $join_supplier = '             LEFT JOIN ' . _DB_PREFIX_ . 'supplier su
                ON su.id_supplier = p.id_supplier ';
        }

        $sql .= ' FROM ' . _DB_PREFIX_ . 'product p
            ' . Shop::addSqlAssociation('product', 'p', true, null, true) . '
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
                ON p.id_product = pl.id_product AND pl.`id_shop` = product_shop.`id_shop`
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
                ON (cl.id_category = product_shop.id_category_default AND pl.id_lang = cl.id_lang AND cl.id_shop = product_shop.id_shop)
            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m
                ON m.id_manufacturer = p.id_manufacturer
            ' . $join_supplier . '
            LEFT JOIN ' . _DB_PREFIX_ . 'lang l
                ON l.id_lang = pl.id_lang
            WHERE product_shop.indexed = 0
            AND product_shop.visibility IN ("both", "search")
            ' . ($id_product ? 'AND p.id_product = ' . (int) $id_product : '') . '
            AND product_shop.`active` = 1
            AND p.id_product IN(
                ' . implode(',', $limited_products_array) . '
            )';

        return Db::getInstance()->executeS($sql, false);
    }

    protected function getTotalProductCount($only_indexed = false)
    {
        $sql = 'SELECT COUNT(DISTINCT p.id_product) FROM ' . _DB_PREFIX_ . 'product p ' . Shop::addSqlAssociation('product', 'p') . ' WHERE product_shop.`visibility` IN ("both", "search") AND product_shop.`active` = 1 ' . ($only_indexed ? ' AND product_shop.`indexed` = 1' : '');

        return $this->db->getValue($sql);
    }

    /**
     * @param int $id_product
     * @param string $sql_attribute
     *
     * @return array|null
     */
    protected function getAttributesFields($id_product, $sql_attribute)
    {
        return $this->db->executeS('SELECT id_product ' . $sql_attribute . ' FROM ' .
            _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product = ' . (int) $id_product, true, false);
    }

    protected function getProductsWeightArray()
    {
        return [
            'pname' => Configuration::get('PS_SEARCH_WEIGHT_PNAME'),
            'reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'description_short' => Configuration::get('PS_SEARCH_WEIGHT_SHORTDESC'),
            'description' => Configuration::get('PS_SEARCH_WEIGHT_DESC'),
            'cname' => Configuration::get('PS_SEARCH_WEIGHT_CNAME'),
            'mname' => Configuration::get('PS_SEARCH_WEIGHT_MNAME'),
            'suname' => Configuration::get('PS_SEARCH_WEIGHT_MNAME'),
            'tags' => Configuration::get('PS_SEARCH_WEIGHT_TAG'),
            'attributes' => Configuration::get('PS_SEARCH_WEIGHT_ATTRIBUTE'),
            'features' => Configuration::get('PS_SEARCH_WEIGHT_FEATURE'),
        ];
    }

    protected function getCategoriesWeightArray()
    {
        return [
            'name' => 30,
            'description' => 1,
        ];
    }

    protected function emptyCurrentIndex($id_product)
    {
        if ((int) $id_product > 0) {
            $this->db->execute('DELETE asi FROM `' . _DB_PREFIX_ . 'amb_search_index` asi
                INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = asi.id_product)
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE product_shop.`visibility` IN ("both", "search")
                AND product_shop.`active` = 1
                AND ' . ($id_product ? 'p.`id_product` = ' . (int) $id_product : 'product_shop.`indexed` = 0'));

            $this->db->execute('DELETE si FROM `' . _DB_PREFIX_ . 'search_index` si
                INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = si.id_product)
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE product_shop.`visibility` IN ("both", "search")
                AND product_shop.`active` = 1
                AND ' . ($id_product ? 'p.`id_product` = ' . (int) $id_product : 'product_shop.`indexed` = 0'));

            $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                SET p.`indexed` = 0, product_shop.`indexed` = 0
                WHERE product_shop.`visibility` IN ("both", "search")
                AND product_shop.`active` = 1
                AND ' . ($id_product ? 'p.`id_product` = ' . (int) $id_product : 'product_shop.`indexed` = 0'));
        } else {
            $this->db->execute('TRUNCATE ' . _DB_PREFIX_ . 'search_index');
            $this->db->execute('TRUNCATE ' . _DB_PREFIX_ . 'amb_search_index');
            $this->db->execute('TRUNCATE ' . _DB_PREFIX_ . 'search_word');
            ObjectModel::updateMultishopTable('Product', ['indexed' => 0]);
        }

        $this->db->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'ambjolisearch_synonyms');
    }

    protected function copyToPrestashopIndex($id_product = false)
    {
        $query = 'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'search_word(id_shop, id_word, id_lang, word)
        SELECT DISTINCT id_shop, NULL, id_lang, word FROM ' . _DB_PREFIX_ . 'amb_search_index;';

        $this->db->execute($query, false);

        $query = 'INSERT IGNORE  ' . _DB_PREFIX_ . 'search_index(id_product, id_word, weight)
        SELECT t1.id_product, t2.id_word, t1.weight
        FROM ' . _DB_PREFIX_ . 'amb_search_index t1
        INNER JOIN ' . _DB_PREFIX_ . 'search_word t2 ON t2.word=t1.word AND t1.id_shop=t2.id_shop AND t1.id_lang=t2.id_lang';

        $this->db->execute($query, false);

        ObjectModel::updateMultishopTable('Product', ['indexed' => 1], 'a.id_product IN (SELECT DISTINCT id_product FROM ' . _DB_PREFIX_ . 'amb_search_index)');

        $this->db->execute('TRUNCATE ' . _DB_PREFIX_ . 'amb_search_index');
    }

    public function initializeProducts()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'amb_search_index` (
          `id_word` int(10) NOT NULL AUTO_INCREMENT,
          `id_shop` int(11) NOT NULL DEFAULT "1",
          `id_lang` int(10) NOT NULL,
          `word` varchar(15) NOT NULL COLLATE utf8mb4_general_ci,
          `weight` varchar(45) DEFAULT NULL COLLATE utf8mb4_general_ci,
          `id_product` int(10) DEFAULT NULL,
          PRIMARY KEY (`id_word`),
          INDEX `word` (`word` ASC, `id_lang` ASC, `id_shop` ASC)
        )';

        Db::getInstance()->execute($query);
    }

    public function processProducts($id_product = false, $step = false, $full = true, $simple_return = false)
    {
        if ($this->cron) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $this->initializeProducts();

        $this->weight_array = $this->getProductsWeightArray();

        $cron = Tools::getValue('cron', false);

        if ((int) $step == 0 && ($full || $id_product)) {
            $this->emptyCurrentIndex($id_product);
        }

        if ($id_product === false) {
            $total_products = $this->getTotalProductCount();
            $indexed = $this->getTotalProductCount(true);
        } else {
            $total_products = 1;
            $indexed = 0;
        }

        $sql_attribute = self::getSQLProductAttributeFields();

        $products = $this->getProductsToIndex($id_product, $step);

        $has_indexed = false;

        $counter = [];

        while ($product = Db::getInstance()->nextRow($products)) {
            if ((int) $this->weight_array['tags']) {
                $product['tags'] = $this->getTags((int) $product['id_product'], (int) $product['id_lang']);
            }
            if ((int) $this->weight_array['attributes']) {
                $product['attributes'] = $this->getAttributes((int) $product['id_product'], (int) $product['id_lang']);
            }
            if ((int) $this->weight_array['features']) {
                $product['features'] = $this->getFeatures((int) $product['id_product'], (int) $product['id_lang']);
            }
            if ($sql_attribute) {
                $attribute_fields = $this->getAttributesFields((int) $product['id_product'], $sql_attribute);
                if ($attribute_fields) {
                    $product['attributes_fields'] = $attribute_fields;
                }
            }

            $scoring = [];

            foreach ($product as $key => $value) {
                if ($key == 'attributes_fields') {
                    foreach ($value as $pa_array) {
                        foreach ($pa_array as $pa_key => $pa_value) {
                            AmbIndexation::fillProductArray($scoring, $this->weight_array, $pa_key, $pa_value, $product['id_lang'], $product['iso_code']);
                        }
                    }
                } else {
                    AmbIndexation::fillProductArray($scoring, $this->weight_array, $key, $value, $product['id_lang'], $product['iso_code']);
                }
            }

            $query_array = [];

            foreach ($scoring as $word => $score) {
                if ($score) {
                    $query_array[$word] = '(' . (int) $product['id_lang'] . ', ' . (int) $product['id_shop'] . ', \'' . pSQL($word) . '\', ' . (int) $score . ', ' . $product['id_product'] . ')';
                }
            }

            $query = '
                INSERT INTO ' . _DB_PREFIX_ . 'amb_search_index (id_lang, id_shop, word, weight, id_product)
                VALUES ' . implode(',', $query_array);

            $this->db->execute($query, false);

            $has_indexed = true;
            $counter[$product['id_product']] = 1;
        }

        $products_done = $indexed + count($counter);

        $this->copyToPrestashopIndex($id_product);

        if ($simple_return !== false) { // When you only want to index some products from a php context, and don't need any redirection system
            if ((int) $id_product > 0) {
                return $id_product;
            } else {
                return true;
            }
        }

        if ($has_indexed) {
            $link = new Link();

            if (!$this->cron) {
                exit(
                    json_encode(
                        [
                            'url' => $link->getAdminLink('AdminModules') . '&configure=ambjolisearch&indexation=products&step=' . ($step + 1),
                            'status' => Tools::ps_round(($products_done / $total_products) * 100, 2) . '%',
                            'indexed' => $products_done,
                            'total' => $total_products,
                        ]
                    )
                );
            } else {
                $url = $link->getModuleLink('ambjolisearch', 'cron', ['configure' => 'ambjolisearch', 'indexation' => 'product', 'step' => ($step + 1), 'token' => $this->token, 'step_size' => $this->step_size]);

                Tools::redirect($url);
            }
        } else {
            if (!$this->cron) {
                exit(
                    json_encode(
                        ['url' => false,
                            'indexed' => $total_products,
                            'total' => $total_products,
                            'status' => '100%',
                        ]
                    )
                );
            } else {
                exit('Completely done');
            }
        }
    }

    protected static function fillProductArray(&$product_array, $weight_array, $key, $value, $id_lang, $iso_code)
    {
        if (strncmp($key, 'id_', 3) && isset($weight_array[$key])) {
            $words = AmbSearch::extractKeyWords($value, (int) $id_lang, true, $iso_code);
            foreach ($words as $word) {
                if (!empty($word)) {
                    $word = Tools::substr($word, 0, AmbSearch::getWordMaxLength());

                    if (!isset($product_array[$word])) {
                        $product_array[$word] = 0;
                    }
                    $product_array[$word] += $weight_array[$key];
                }
            }
        }
    }

    public static function removeProductsSearchIndex($products)
    {
        if (is_array($products) && !empty($products)) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'search_index WHERE id_product IN (' . implode(',', array_unique(array_map('intval', $products))) . ')');
            ObjectModel::updateMultishopTable('Product', ['indexed' => 0], 'a.id_product IN (' . implode(',', array_map('intval', $products)) . ')');
        }
    }
}
