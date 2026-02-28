<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WkSampleProductMap extends ObjectModel
{
    public $id_sample_product;
    public $id_product;
    public $id_product_attribute;
    public $max_cart_qty;
    /**
     * 1 = Product Actual Price
     * 2 = Deduct fix amount from product price
     * 3 = Deduct percentage of price from product price
     * 4 = Custom Price
     *
     * @var int
     */
    public $price_type;
    public $price_tax;
    public $amount;
    public $price;
    public $weight;
    public $sample_file;
    public $button_label;
    public $description;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_sample_product',
        'primary' => 'id_sample_product',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'max_cart_qty' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'price_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'price_tax' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'shop' => true],
            'amount' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'shop' => true],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'weight' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'sample_file' => ['type' => self::TYPE_STRING, 'shop' => true],
            'button_label' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_sample_product', ['type' => 'shop', 'primary' => 'id_sample_product']);
        Shop::addTableAssociation(
            'wk_sample_product_lang',
            ['type' => 'fk_shop', 'primary' => 'id_sample_product']
        );
    }

    public function delete()
    {
        if (Shop::isTableAssociated('wk_sample_product')) {
            $id_shop_list = Shop::getContextListShopID();
            if (count($this->id_shop_list)) {
                $id_shop_list = $this->id_shop_list;
            }

            $id_shop_list = array_map('intval', $id_shop_list);
            Db::getInstance()->delete(
                'wk_sample_carrier',
                ' `id_product`=' . (int) $this->id_product . ' AND id_shop IN (' . implode(', ', $id_shop_list) . ')'
            );
        } else {
            Db::getInstance()->delete('wk_sample_carrier', ' `id_product`=' . (int) $this->id_product);
        }

        return parent::delete();
    }

    /**
     * Get Product Sample Data
     *
     * @param int $idProduct
     *
     * @return array
     */
    public function getSampleProduct($idProduct, $useGlobal = true)
    {
        $idLang = Context::getContext()->language->id;
        $cacheId = 'WkSampleProductMap_getSampleProduct_' . (int) $idProduct . '_' . (int) $useGlobal . '_' . (int) $idLang;
        if (!Cache::isStored($cacheId)) {
            $sample = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
                'SELECT wsp.*, wspl.`button_label`, wspl.`description` FROM `' . _DB_PREFIX_ . 'wk_sample_product` wsp'
                . WkSampleCart::addSqlAssociationCustom('wk_sample_product', 'wsp', 'id_sample_product') .
                ' LEFT JOIN `' . _DB_PREFIX_ . 'wk_sample_product_lang` wspl
                ON (wspl.`id_sample_product`=wsp.`id_sample_product` AND wspl.`id_lang`=' . (int) $idLang . ')
                WHERE wsp.`id_product` = ' . (int) $idProduct . Shop::addSqlRestrictionOnLang('wspl')
            );
            $sampleResult = false;
            if ($sample) {
                if (!$useGlobal) {
                    $langValues = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                        'SELECT wspl.`id_lang`, wspl.`button_label`, wspl.`description`
                        FROM `' . _DB_PREFIX_ . 'wk_sample_product_lang` wspl
                        WHERE wspl.`id_sample_product`=' . (int) $sample['id_sample_product'] .
                            Shop::addSqlRestrictionOnLang('wspl')
                    );
                    $sampleTitles = [];
                    $sampleDescs = [];
                    foreach ($langValues as $sampleVal) {
                        $sampleTitles[$sampleVal['id_lang']] = $sampleVal['button_label'];
                        $sampleDescs[$sampleVal['id_lang']] = $sampleVal['description'];
                    }
                    $sample['button_label'] = $sampleTitles;
                    $sample['description'] = $sampleDescs;
                }
                if ($sample['active'] || !$useGlobal) {
                    $sampleResult = $sample;
                }
            } elseif (Configuration::get('WK_GLOBAL_SAMPLE') && $useGlobal) {
                $sampleResult = $this->getGlobalSample($idProduct, $idLang);
            }
            Cache::store($cacheId, $sampleResult);
        }

        return Cache::retrieve($cacheId);
    }

    private function getGlobalSample($idProduct, $idLang)
    {
        $globalSampleConfig = Configuration::getMultiple([
            'WK_GLOBAL_SAMPLE_IN_CART',
            'WK_GLOBAL_SAMPLE_PRICE_TYPE',
            'WK_GLOBAL_SAMPLE_AMOUNT',
            'WK_GLOBAL_SAMPLE_PRICE',
            'WK_GLOBAL_SAMPLE_TAX',
            'WK_GLOBAL_SAMPLE_PERCENT',
            'WK_GLOBAL_SAMPLE_WEIGHT',
            'WK_GLOBAL_SAMPLE_BUTTON_LABEL',
            'WK_GLOBAL_SAMPLE_DESC',
        ], $idLang);

        return [
            'id_sample_product' => '0',
            'id_product' => $idProduct,
            'id_product_attribute' => Product::getDefaultAttribute($idProduct),
            'max_cart_qty' => $globalSampleConfig['WK_GLOBAL_SAMPLE_IN_CART'],
            'price_type' => $globalSampleConfig['WK_GLOBAL_SAMPLE_PRICE_TYPE'],
            'price_tax' => $globalSampleConfig['WK_GLOBAL_SAMPLE_TAX'],
            'amount' => ($globalSampleConfig['WK_GLOBAL_SAMPLE_PRICE_TYPE'] == '3') ?
                $globalSampleConfig['WK_GLOBAL_SAMPLE_PERCENT'] : $globalSampleConfig['WK_GLOBAL_SAMPLE_AMOUNT'],
            'price' => $globalSampleConfig['WK_GLOBAL_SAMPLE_PRICE'],
            'weight' => $globalSampleConfig['WK_GLOBAL_SAMPLE_WEIGHT'],
            'button_label' => $globalSampleConfig['WK_GLOBAL_SAMPLE_BUTTON_LABEL'],
            'description' => $globalSampleConfig['WK_GLOBAL_SAMPLE_DESC'],
            'active' => '1',
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ];
    }

    public function getSampleFileName($idProduct)
    {
        $sample = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT wsp.`sample_file`, wsp.`id_sample_product` FROM `' . _DB_PREFIX_ . 'wk_sample_product` wsp'
                . WkSampleCart::addSqlAssociationCustom('wk_sample_product', 'wsp', 'id_sample_product') .
                ' WHERE wsp.`id_product` = ' . (int) $idProduct
        );

        return $sample;
    }

    public function getSampleCarriers($idProduct, $idShop)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.*
            FROM `' . _DB_PREFIX_ . 'wk_sample_carrier` pc
            INNER JOIN `' . _DB_PREFIX_ . 'carrier` c
                ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
            WHERE pc.`id_product` = ' . (int) $idProduct . '
                AND pc.`id_shop` = ' . (int) $idShop);
    }

    public function setSampleCarriers($carrier_list, $idProduct, $idShop)
    {
        $data = [];
        foreach ($carrier_list as $carrier) {
            $data[] = [
                'id_product' => (int) $idProduct,
                'id_carrier_reference' => (int) $carrier,
                'id_shop' => (int) $idShop,
            ];
        }
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'wk_sample_carrier`
            WHERE id_product = ' . (int) $idProduct . '
            AND id_shop = ' . (int) $idShop
        );

        $unique_array = [];
        foreach ($data as $sub_array) {
            if (!in_array($sub_array, $unique_array)) {
                $unique_array[] = $sub_array;
            }
        }

        if (count($unique_array)) {
            Db::getInstance()->insert('wk_sample_carrier', $unique_array, false, true, Db::INSERT_IGNORE);
        }
    }

    public function getFilteredProducts(
        $searchPattern,
        $idCategories,
        $idManufacturers,
        $idSuppliers,
        $idLang
    ) {
        $idCurrentContextShop = Context::getContext()->shop->id;
        $sql = 'SELECT p.`id_product`, pl.`name` FROM `' . _DB_PREFIX_ . 'product_shop` ps
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (ps.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $idLang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = ps.`id_product`
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON cp.`id_product` = ps.`id_product`
                LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` psu ON psu.`id_product` = ps.`id_product`
                WHERE ps.`id_shop` = ' . (int) $idCurrentContextShop .
                ' AND p.`active` = 1';
        if ($searchPattern) {
            $sql .= ' AND `name` LIKE "%' . pSQL($searchPattern) . '%"';
        }
        if ($idCategories) {
            $sql .= ' AND (';
            if (is_array($idCategories)) {
                $countIdCategories = count($idCategories);
                foreach ($idCategories as $categoryKey => $idCategory) {
                    if ($categoryKey + 1 === $countIdCategories) {
                        $sql .= 'cp.`id_category` = ' . (int) $idCategory . ')';
                    } else {
                        $sql .= 'cp.`id_category` = ' . (int) $idCategory . ' OR ';
                    }
                }
            } else {
                $sql .= 'cp.`id_category` = ' . (int) $idCategories . ')';
            }
        }
        if ($idManufacturers) {
            $sql .= ' AND (';
            if (is_array($idManufacturers)) {
                $countIdManufacturers = count($idManufacturers);
                foreach ($idManufacturers as $ManfctrKey => $idManufacturer) {
                    if ($ManfctrKey + 1 === $countIdManufacturers) {
                        $sql .= 'p.`id_manufacturer` = ' . (int) $idManufacturer . ')';
                    } else {
                        $sql .= 'p.`id_manufacturer` = ' . (int) $idManufacturer . ' OR ';
                    }
                }
            } else {
                $sql .= 'p.`id_manufacturer` = ' . (int) $idManufacturers . ')';
            }
        }
        if ($idSuppliers) {
            $sql .= ' AND (';
            if (is_array($idSuppliers)) {
                $countIdSuppliers = count($idSuppliers);
                foreach ($idSuppliers as $Supplierkey => $idSupplier) {
                    if ($Supplierkey + 1 === $countIdSuppliers) {
                        $sql .= 'psu.`id_supplier` = ' . (int) $idSupplier . ')';
                    } else {
                        $sql .= 'psu.`id_supplier` = ' . (int) $idSupplier . ' OR ';
                    }
                }
            } else {
                $sql .= 'psu.`id_supplier` = ' . (int) $idSuppliers . ')';
            }
        }
        $sql .= ' GROUP BY ps.`id_product`';

        return Db::getInstance()->executeS($sql);
    }

    public function checkScriptInHtml($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML(htmlspecialchars_decode($html));
        $script = $dom->getElementsByTagName('script');

        return $script->length;
    }

    public function checkParaInHtml($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML(htmlspecialchars_decode($html));
        $script = $dom->getElementsByTagName('p');

        return $script->length;
    }

    public static function checkNewPSProductPage()
    {
        $table = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'feature_flag"');
        if ($table) {
            return Db::getInstance()->getValue(
                'SELECT `state` FROM `' . _DB_PREFIX_ . 'feature_flag`
                            WHERE `name`="product_page_v2"'
            );
        }

        return false;
    }
}
