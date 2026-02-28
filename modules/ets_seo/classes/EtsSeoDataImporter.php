<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';

/**
 * Class EtsSeoDataImporter
 *
 * @since 2.5.9
 */
class EtsSeoDataImporter
{
    use EtsSeoGetInstanceTrait;

    /**
     * Directory to store files during export
     */
    const DATA_FILE_DIRECTORY = _PS_CACHE_DIR_ . 'ets_seo';

    const XML_FILE_NAME = 'ets_seo_data.xml';
    /**
     * @var array
     */
    private $shops;
    /**
     * @var array
     */
    private $options;

    /**
     * @var \Db|\DbCore
     */
    private static $_db;
    /**
     * @var \Shop|\ShopCore
     */
    private $oldContextShop;
    /**
     * @var array
     */
    public $currentProcessingItem;

    /**
     * @return \Db|\DbCore
     */
    private static function db()
    {
        if (!self::$_db instanceof \DbCore) {
            self::$_db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        }

        return self::$_db;
    }

    /**
     * @param array $shops
     * @param array $options
     */
    public function init($shops, $options)
    {
        $this->shops = $shops;
        $this->options = $options;
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function process()
    {
        $xmlFile = self::DATA_FILE_DIRECTORY . DIRECTORY_SEPARATOR . self::XML_FILE_NAME;
        if (!@file_exists($xmlFile)) {
            throw new \PrestaShopException($this->l('Neither ets_seo_data.xml exist'));
        }
        $content = Tools::file_get_contents($xmlFile);
        $simpleXml = simplexml_load_string($content);
        $arr = $this->xml2array($simpleXml);
        if (!isset($arr['ets_seo_data_exporter_version']) || version_compare(@$arr['ets_seo_data_exporter_version'], '1.1', '<')) {
            require_once __DIR__ . '/EtsSeoImportExport.php';
            $oldImporter = new EtsSeoImportExport($this->shops, $this->options);
            $oldImporter->importData($xmlFile);

            return true;
        }
        foreach ($arr as $index => $item) {
            $index = 'configuration' == $index ? 'config' : $index;
            if (!in_array($index, $this->options)) {
                continue;
            }
            switch ($index) {
                case 'cms':
                    $this->importCms($item);
                    break;
                case 'manufacturer':
                    $this->importManufacturer($item);
                    break;
                case 'supplier':
                    $this->importSupplier($item);
                    break;
                case 'category':
                    $this->importCategory($item);
                    break;
                case 'cms_category':
                    $this->importCmsCategory($item);
                    break;
                case 'product':
                    $this->importProduct($item);
                    break;
                case 'config':
                    $this->importConfig($item);
                    break;
                case 'meta':
                    $this->importMeta($item);
                    break;
                case 'redirect':
                    $this->importRedirect($item);
                    break;
            }
        }

        return true;
    }

    /**
     * @param array $data
     */
    private function importConfig($data)
    {
        foreach ($data as $config) {
            Configuration::updateValue($config['name'], $config['value'], true, null, $config['id_shop']);
        }
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     */
    private function importRedirect($data)
    {
        foreach ($data as $redirect) {
            $redirect['url'] = urldecode($redirect['url']);
            self::db()->delete('ets_seo_redirect', sprintf('url = "%s" AND id_shop = %d', pSQL($redirect['url']), (int) $redirect['id_shop']));
            self::db()->insert('ets_seo_redirect', $this->_correctItemData($redirect, true), true, false, Db::INSERT_IGNORE);
        }
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importMeta($data)
    {
        foreach ($data as $meta) {
            if (!isset($meta['id_meta'], $meta['id_shop'], $meta['page'])) {
                throw new \PrestaShopException($this->l('Invalid Meta data'));
            }
            if (!in_array($meta['id_shop'], $this->shops)) {
                continue;
            }
            $this->currentProcessingItem = $meta;
            $idQuery = (new \DbQuery())
                ->select('m.id_meta')
                ->from('meta', 'm')
                ->where(sprintf('m.page = "%s"', pSQL($meta['page'])));
            if (!($metaId = self::db()->getValue($idQuery))) {
                continue;
            }
            $meta['id_meta'] = (int) $metaId;
            $obj = new \Meta($meta['id_meta'], null, $meta['id_shop']);
            foreach ($meta as $key => $value) {
                if (in_array($key, ['id_meta', 'id_shop', 'page'])) {
                    continue;
                }
                if ('ets_seo_meta' !== $key) {
                    $toFill = array_column($value, 'value', 'id_lang');
                    if ('url_rewrite' === $key) {
                        $toFill = array_map(static function ($v) {
                            return (string) $v;
                        }, $toFill);
                    }
                    $obj->{$key} = $toFill;
                } else {
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        self::db()->delete($key, sprintf('id_meta = %d AND id_shop = %d AND id_lang = %d', (int) $item['id_meta'], (int) $item['id_shop'], (int) $item['id_lang']));
                        self::db()->insert($key, $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            try {
                $obj->save();
            } catch (PrestaShopException $e) {
                if (false !== strpos($e->getMessage(), 'url_rewrite')) {
                    continue;
                }
                throw $e;
            }
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importProduct($data)
    {
        foreach ($data as $product) {
            if (!isset($product['id_product'], $product['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid Product data'));
            }
            if (!in_array($product['id_shop'], $this->shops)) {
                continue;
            }
            $this->currentProcessingItem = $product;
            $queryExist = (new \DbQuery())->select('p.id_product')->from('product', 'p')
                ->innerJoin('product_shop', 'ps', 'p.id_product = ps.id_product AND ps.id_shop = ' . (int) $product['id_shop'])
                ->where('p.id_product = ' . (int) $product['id_product']);
            if (!self::db()->getValue($queryExist)) {
                continue;
            }
            $obj = new \Product($product['id_product'], true, null, $product['id_shop']);
            if (\Product::STATE_SAVED !== $obj->state) {
                continue;
            }
            foreach ($product as $key => $value) {
                if (in_array($key, ['id_product', 'id_shop'])) {
                    continue;
                }
                if (!in_array($key, ['redirect_type', 'ets_seo_product'])) {
                    $obj->{$key} = array_column($value, 'value', 'id_lang');
                } else {
                    if ('redirect_type' === $key) {
                        $obj->{$key} = (string) $value;
                        continue;
                    }
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        self::db()->delete($key, sprintf('id_product = %d AND id_shop = %d AND id_lang = %d', (int) $item['id_product'], (int) $item['id_shop'], (int) $item['id_lang']));
                        self::db()->insert($key, $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importCategory($data)
    {
        foreach ($data as $category) {
            if (!isset($category['id_category'], $category['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid Category data'));
            }
            if (!in_array($category['id_shop'], $this->shops)) {
                continue;
            }
            $this->currentProcessingItem = $category;
            $obj = new \Category($category['id_category'], null, $category['id_shop']);
            foreach ($category as $key => $value) {
                if (in_array($key, ['id_category', 'id_shop'])) {
                    continue;
                }
                if ('ets_seo_category' !== $key) {
                    if ($this->isSingleLevelArray($value)) {
                        $obj->{$key}[$value['id_lang']] = $value['value'];
                    } else {
                        $obj->{$key} = array_column($value, 'value', 'id_lang');
                    }
                } else {
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        self::db()->delete($key, sprintf('id_category = %d AND id_shop = %d AND id_lang = %d', (int) $item['id_category'], (int) $item['id_shop'], (int) $item['id_lang']));
                        self::db()->insert($key, $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importSupplier($data)
    {
        foreach ($data as $supplier) {
            if (!isset($supplier['id_supplier'], $supplier['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid Supplier data'));
            }
            if (!in_array($supplier['id_shop'], $this->shops)) {
                continue;
            }
            $this->oldContextShop = Ets_Seo::getContextStatic()->shop;
            if (Ets_Seo::getContextStatic()->shop->id != $supplier['id_shop']) {
                Ets_Seo::getContextStatic()->shop = new \Shop($supplier['id_shop']);
            }
            $this->currentProcessingItem = $supplier;
            $obj = new \Supplier($supplier['id_supplier'], null);
            foreach ($supplier as $key => $value) {
                if (in_array($key, ['id_supplier', 'id_shop'])) {
                    continue;
                }
                if (!in_array($key, ['link_rewrite', 'ets_seo_supplier', 'ets_seo_supplier_url'])) {
                    $obj->{$key} = array_column($value, 'value', 'id_lang');
                } else {
                    if ('link_rewrite' === $key) {
                        $obj->{$key} = $value;
                        continue;
                    }

                    $delSql = sprintf('id_supplier = %d', (int) $supplier['id_supplier']);
                    if ('ets_seo_supplier' === $key) {
                        foreach ($value as $item) {
                            if (!is_array($item)) {
                                continue;
                            }
                            self::db()->delete($key, $delSql . sprintf(' AND id_shop = %d AND id_lang = %d', (int) $item['id_shop'], (int) $item['id_lang']));
                            self::db()->insert($key, $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                        }
                    } else {
                        self::db()->delete($key, $delSql);
                        self::db()->insert($key, $this->_correctItemData($value, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importCms($data)
    {
        foreach ($data as $cms) {
            if (!isset($cms['id_cms'], $cms['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid CMS data'));
            }
            if (!in_array($cms['id_shop'], $this->shops)) {
                continue;
            }
            $this->currentProcessingItem = $cms;
            $obj = new \CMS($cms['id_cms'], null, $cms['id_shop']);
            foreach ($cms as $key => $value) {
                if (in_array($key, ['id_cms', 'id_shop'])) {
                    continue;
                }
                if ('ets_seo_cms' !== $key) {
                    $obj->{$key} = array_column($value, 'value', 'id_lang');
                } else {
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        self::db()->delete('ets_seo_cms', sprintf('id_cms = %d AND id_shop = %d AND id_lang = %d', (int) $item['id_cms'], (int) $item['id_shop'], (int) $item['id_lang']));
                        self::db()->insert('ets_seo_cms', $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importCmsCategory($data)
    {
        foreach ($data as $category) {
            if (!isset($category['id_cms_category'], $category['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid CMS Category data'));
            }
            if (!in_array($category['id_shop'], $this->shops)) {
                continue;
            }
            $this->currentProcessingItem = $category;
            $obj = new \CMSCategory($category['id_cms_category'], null, $category['id_shop']);
            foreach ($category as $key => $value) {
                if (in_array($key, ['id_cms_category', 'id_shop'])) {
                    continue;
                }
                if ('ets_seo_cms_category' !== $key) {
                    $obj->{$key} = array_column($value, 'value', 'id_lang');
                } else {
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        self::db()->delete('ets_seo_cms_category', sprintf('id_cms_category = %d AND id_shop = %d AND id_lang = %d', (int) $item['id_cms_category'], (int) $item['id_shop'], (int) $item['id_lang']));
                        self::db()->insert('ets_seo_cms_category', $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $data
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function importManufacturer($data)
    {
        foreach ($data as $manufacturer) {
            if (!isset($manufacturer['id_manufacturer'], $manufacturer['id_shop'])) {
                throw new \PrestaShopException($this->l('Invalid Brand data'));
            }
            if (!in_array($manufacturer['id_shop'], $this->shops)) {
                continue;
            }
            $this->oldContextShop = Ets_Seo::getContextStatic()->shop;
            if (Ets_Seo::getContextStatic()->shop->id != $manufacturer['id_shop']) {
                Ets_Seo::getContextStatic()->shop = new \Shop($manufacturer['id_shop']);
            }
            $this->currentProcessingItem = $manufacturer;
            $obj = new \Manufacturer($manufacturer['id_manufacturer'], null);
            foreach ($manufacturer as $key => $value) {
                if (in_array($key, ['id_manufacturer', 'id_shop'])) {
                    continue;
                }
                if (!in_array($key, ['link_rewrite', 'ets_seo_manufacturer', 'ets_seo_manufacturer_url'])) {
                    $obj->{$key} = array_column($value, 'value', 'id_lang');
                } else {
                    if ('link_rewrite' === $key) {
                        $obj->{$key} = $value;
                        continue;
                    }

                    $delSql = sprintf('id_manufacturer = %d', (int) $manufacturer['id_manufacturer']);
                    if ('ets_seo_manufacturer' === $key) {
                        foreach ($value as $item) {
                            if (!is_array($item)) {
                                continue;
                            }
                            self::db()->delete($key, $delSql . sprintf(' AND id_shop = %d AND id_lang = %d', (int) $item['id_shop'], (int) $item['id_lang']));
                            self::db()->insert($key, $this->_correctItemData($item, true), true, false, Db::INSERT_IGNORE);
                        }
                    } else {
                        self::db()->delete($key, $delSql);
                        self::db()->insert($key, $this->_correctItemData($value, true), true, false, Db::INSERT_IGNORE);
                    }
                }
            }
            $obj->save();
        }
        unset($this->currentProcessingItem);
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isSingleLevelArray($array)
    {
        $filteredArray = array_filter($array, static function ($item) {
            return !is_array($item);
        });

        return count($array) === count($filteredArray);
    }

    /**
     * @param \SimpleXMLElement|array $xmlObject
     * @param array $out
     *
     * @return array|string
     */
    public function xml2array($xmlObject, $out = [])
    {
        $forceArrKeys = [
            'product', 'category', 'cms', 'cms_category', 'supplier', 'manufacturer', 'meta', 'redirect',
        ];
        if (is_array($xmlObject) || (is_object($xmlObject) && $xmlObject->count() > 0)) {
            foreach ((array) $xmlObject as $index => $node) {
                $tmpArr = $this->xml2array($node);
                $idKey = 'redirect' === $index ? 'id_ets_seo_redirect' : 'id_' . $index;
                if (is_array($tmpArr) && array_key_exists($idKey, $tmpArr) && in_array($index, $forceArrKeys, true)) {
                    $tmpArr = [$tmpArr];
                }
                $out[$index] = $tmpArr;
            }
        } else {
            $out = json_decode($xmlObject);

            if (null === $out and !empty((string) $xmlObject)) {
                $out = (string) $xmlObject;
            }
        }

        return $out;
    }

    /**
     * @param array $etsSeoItem
     * @param bool $unsetPrimary
     *
     * @return array
     */
    private function _correctItemData($etsSeoItem, $unsetPrimary = false)
    {
        $primaryKey = null;
        foreach ($etsSeoItem as $k => $value) {
            if (0 === strpos($k, 'id_ets_seo_')) {
                $primaryKey = $k;
            }
            if ($value instanceof \stdClass) {
                $etsSeoItem[$k] = json_encode($value);
            }
            $etsSeoItem[$k] = pSQL($etsSeoItem[$k], true);
        }
        if ($unsetPrimary && $primaryKey) {
            unset($etsSeoItem[$primaryKey]);
        }

        return $etsSeoItem;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function l($str)
    {
        $instance = Module::getInstanceByName('ets_seo');
        if (!$instance) {
            return '';
        }

        return $instance->l($str, 'import-export');
    }
}
