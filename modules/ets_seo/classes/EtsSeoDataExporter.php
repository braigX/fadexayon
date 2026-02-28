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
 * Class EtsSeoDataExporter
 *
 * @since 2.5.7
 */
class EtsSeoDataExporter
{
    use EtsSeoGetInstanceTrait;

    const STATE_IDLE = 0;

    const STATE_INITIALIZED = 1;

    const STATE_EXPORTING = 2;

    const STATE_ARCHIVING = 3;

    const STATE_COMPLETED = 4;

    const DEFAULT_PRODUCT_PER_PROCESS = 250;

    const MAX_PRODUCT_PER_PROCESS = 10000;

    const DATA_STATE_FILE_NAME = 'export-state.dat';
    const DATA_XML_FILE_NAME = 'ets_seo_data.xml';
    /**
     * Directory to store files during export
     */
    const DATA_FILE_DIRECTORY = _PS_CACHE_DIR_ . 'ets_seo';

    /**
     * @var int
     */
    private $productPerProcess = self::DEFAULT_PRODUCT_PER_PROCESS;

    /**
     * @param int $productPerProcess
     *
     * @return self
     */
    public function setProductPerProcess($productPerProcess)
    {
        if ($productPerProcess > 0) {
            $this->productPerProcess = min($productPerProcess, self::MAX_PRODUCT_PER_PROCESS);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getProductPerProcess()
    {
        return $this->productPerProcess;
    }

    /**
     * Init export
     *
     * @param array $shops
     * @param array $options
     *
     * @return bool
     *
     * @throws \PrestaShopException
     * @throws \RuntimeException
     */
    public function init($shops, $options)
    {
        if ((!is_array($shops) || empty($shops)) || (!is_array($options) || empty($options))) {
            throw new \PrestaShopException('Invalid shops or options provided');
        }
        $data = [
            'currentState' => self::STATE_INITIALIZED,
            'shops' => $shops,
            'options' => $options,
        ];

        return $this->_writeData($data, self::DATA_STATE_FILE_NAME);
    }

    /**
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function process()
    {
        $data = $this->getStoredData();
        if (!isset($data['shops'], $data['options']) && @!$data['shops'] && @!$data['options']) {
            $this->_unlinkData();
            throw new \PrestaShopException('Invalid shops or options provided. Please try again.');
        }
        if (self::STATE_COMPLETED == $data['currentState']) {
            $this->_unlinkData();
            throw new \PrestaShopException('There is a operation finished. Please refresh page & try again.');
        }
        if (self::STATE_ARCHIVING == $data['currentState']) {
            $data = $this->generateArchive($data);

            return $this->addMessageToReturnData($data);
        }
        if (self::STATE_INITIALIZED == $data['currentState']) {
            $data['currentState'] = self::STATE_EXPORTING;
        }
        $shops = $data['shops'];
        $options = $data['options'];
        $currentShop = isset($data['currentShop']) ? $data['currentShop'] : $shops[0];
        $currentOption = isset($data['currentOption']) ? $data['currentOption'] : $options[0];
        $isOptionComplete = false;
        $isAllOptionsComplete = false;
        foreach ($shops as $sk => $shop) {
            if ($shop != $currentShop) {
                continue;
            }
            foreach ($options as $k => $option) {
                if ($option != $currentOption) {
                    continue;
                }
                switch ($option) {
                    case 'product':
                        $this->exportProduct($shop, $data);
                        if (@$data['productPointers'][$currentShop]['isCompleted']) {
                            $isOptionComplete = true;
                        }
                        break;
                    case 'category':
                        $this->exportCategory($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'manufacturer':
                        $this->exportManufacturer($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'supplier':
                        $this->exportSupplier($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'cms':
                        $this->exportCms($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'cms_category':
                        $this->exportCmsCategory($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'meta':
                        $this->exportMeta($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'redirect':
                        $this->exportRedirect($shop, $data);
                        $isOptionComplete = true;
                        break;
                    case 'config':
                    case 'configuration':
                    default:
                        $this->exportConfiguration($shop, $data);
                        $isOptionComplete = true;
                        break;
                }
                break;
            }
            if ($isOptionComplete) {
                if ($currentOption == end($options)) {
                    $currentOption = $options[0];
                    $isAllOptionsComplete = true;
                } else {
                    $currentOption = $options[array_search($currentOption, $options, true) + 1];
                }
            }
            break;
        }
        if ($isAllOptionsComplete) {
            if ($currentShop == end($shops)) {
                $data['currentState'] = self::STATE_ARCHIVING;
            } else {
                $currentShop = $shops[array_search($currentShop, $shops, true) + 1];
            }
        }
        $data['currentShop'] = $currentShop;
        $data['currentOption'] = $currentOption;
        $this->_writeData($data, self::DATA_STATE_FILE_NAME);

        return $this->addMessageToReturnData($data, $isOptionComplete);
    }

    /**
     * @param array $data
     * @param bool $isOptionComplete
     *
     * @return array
     */
    private function addMessageToReturnData($data, $isOptionComplete = true)
    {
        $currentShop = $data['currentShop'];
        $currentOption = $data['currentOption'];
        $data['message'] = '';
        switch ($data['currentState']) {
            case self::STATE_EXPORTING: $data['message'] = $this->l('Exporting');
                break;
            case self::STATE_ARCHIVING: $data['message'] = $this->l('Archiving');
                break;
            case self::STATE_COMPLETED: $data['message'] = $this->l('Completed');
                break;
        }
        if (self::STATE_EXPORTING == $data['currentState']) {
            $data['message'] .= sprintf($this->l(' shop %s. %s: '), $data['currentShop'], ucfirst($data['currentOption']));
        }
        if (self::STATE_COMPLETED !== $data['currentState']) {
            $data['processing'] = $this->l('Done');
            if ('product' == $currentOption) {
                $data['processing'] = $isOptionComplete ? $this->l('Done') : sprintf($this->l('Processing %d of %d'), $data['productPointers'][$currentShop]['startOffset'], $data['productPointers'][$currentShop]['total']);
            }
            $data['message'] .= $data['processing'];
        }
        $data['shop'] = $data['currentShop'];
        $data['option'] = $data['currentOption'];

        return $data;
    }

    /**
     * @param array $data
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function createXml($data)
    {
        if (!isset($data['shops'], $data['options']) && (empty($data['shops']) || empty($data['options']))) {
            throw new \PrestaShopException('Invalid exported data');
        }
        $str = ['<?xml version="1.0" encoding="UTF-8"?>' . '
<entity_profile><ets_seo_data_exporter_version>1.1</ets_seo_data_exporter_version>'];
        $valid = false;
        foreach ($data['shops'] as $shopId) {
            if (isset($data[$shopId])) {
                foreach ($data['options'] as $option) {
                    $option = ('config' == $option) ? 'configuration' : $option;
                    if (isset($data[$shopId][$option])) {
                        $valid = true;
                        array_push($str, ...$this->_generateXmlArray($option, $data[$shopId][$option]));
                    }
                }
            }
        }
        if (!$valid) {
            throw new \PrestaShopException('Invalid exported data');
        }
        $str[] = '</entity_profile>';
        $str = implode('', $str);
        $str = str_replace('&', 'and', $str);

        return $str;
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    public function generateArchive(&$data)
    {
        $zip = new ZipArchive();
        $cacheDir = self::DATA_FILE_DIRECTORY;
        $zip_file_name = 'ets_seo_export_' . date('Y-m-d_H-i-s') . '.zip';
        if (true === $zip->open($cacheDir . DIRECTORY_SEPARATOR . $zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE)) {
            if (!$zip->addFromString(self::DATA_XML_FILE_NAME, $this->createXml($data))) {
                throw new \PrestaShopException($this->l('Cannot create ets_seo_data.xml'));
            }
            $zip->close();
            if (!is_file($cacheDir . DIRECTORY_SEPARATOR . $zip_file_name)) {
                throw new \PrestaShopException(sprintf($this->l('Could not create %1s'), $cacheDir . DIRECTORY_SEPARATOR . $zip_file_name));
            }
            $data['currentState'] = self::STATE_COMPLETED;
            $data['fileName'] = $zip_file_name;
            $this->_unlinkData();

            return $data;
        }

        throw new \PrestaShopException($this->l('Cannot create ets_seo_data.xml'));
    }

    /**
     * @param string $type
     * @param array $optionData
     *
     * @return array
     */
    private function _generateXmlArray($type, $optionData)
    {
        $xml = [];
        foreach ($optionData as $datum) {
            $xml[] = sprintf('<%s>', $type);
            foreach ($datum as $key => $value) {
                if (is_array($value)) {
                    if ($this->isIdLangArray($value)) {
                        array_push($xml, ...$this->_generateIdLangXmlArray($key, $value));
                    } else {
                        array_push($xml, ...$this->_generateXmlArray($key, $value));
                    }
                } else {
                    $xml[] = sprintf('<%s>', $key);
                    $xml[] = $this->_safeXmlValue($value);
                    $xml[] = sprintf('</%s>', $key);
                }
            }
            $xml[] = sprintf('</%s>', $type);
        }

        return $xml;
    }

    /**
     * @param bool|string|null $value
     *
     * @return string
     */
    private function _safeXmlValue($value)
    {
        return sprintf('<![CDATA[%s]]>', $value);
    }

    /**
     * @param string $key
     * @param array $value
     *
     * @return array
     */
    private function _generateIdLangXmlArray($key, $value)
    {
        $xml = [];
        foreach ($value as $idLang => $item) {
            $xml[] = sprintf('<%s>', $key);
            $xml[] = sprintf('<%s>', 'id_lang');
            $xml[] = $this->_safeXmlValue($idLang);
            $xml[] = sprintf('</%s>', 'id_lang');
            $xml[] = sprintf('<%s>', 'value');
            $xml[] = $this->_safeXmlValue($item);
            $xml[] = sprintf('</%s>', 'value');
            $xml[] = sprintf('</%s>', $key);
        }

        return $xml;
    }

    /**
     * @param array $arr
     *
     * @return bool
     */
    private function isIdLangArray($arr)
    {
        if (isset($arr[0])) {
            return false;
        }
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Detect current state of export operation
     *
     * @return int
     */
    public function detectCurrentState()
    {
        $file = self::DATA_FILE_DIRECTORY . DIRECTORY_SEPARATOR . self::DATA_STATE_FILE_NAME;
        if (!file_exists($file)) {
            return self::STATE_IDLE;
        }
        $data = json_decode(Tools::file_get_contents($file), true);

        return isset($data['currentState']) ? $data['currentState'] : self::STATE_IDLE;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     */
    private function buildProductPointer($shopId, &$data)
    {
        $pointers = isset($data['productPointers']) ? $data['productPointers'] : [];
        $pointers[$shopId] = isset($pointers[$shopId]) ? $pointers[$shopId] : [];
        if (!isset($pointers[$shopId]['total'])) {
            $sql = 'SELECT COUNT(DISTINCT p.id_product)
                FROM `' . _DB_PREFIX_ . 'product` p
                INNER JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop
                ON (product_shop.id_product = p.id_product) WHERE product_shop.id_shop = ' . (int) $shopId;
            $pointers[$shopId]['total'] = (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        if (!isset($pointers[$shopId]['startOffset'])) {
            $pointers[$shopId]['startOffset'] = 0;
        }
        $data['productPointers'] = $pointers;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    private function exportProduct($shopId, &$data)
    {
        $data = $this->buildProductPointer($shopId, $data);
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $pointers = $data['productPointers'][$shopId];
        $currentData = isset($data[$shopId]['product']) ? $data[$shopId]['product'] : [];
        $offset = $pointers['startOffset'];
        $limit = $this->getProductPerProcess();
        $sql = 'SELECT DISTINCT p.id_product
                FROM `' . _DB_PREFIX_ . 'product` p
                INNER JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop
                ON (product_shop.id_product = p.id_product) 
                WHERE product_shop.id_shop = ' . (int) $shopId . ' 
                LIMIT ' . $limit . ' OFFSET ' . (int) $offset;
        $productIds = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($productIds as $arr) {
            $object = new \Product($arr['id_product'], true, null, $shopId);
            if (\Product::STATE_SAVED !== $object->state) {
                continue;
            }
            $item = [
                'id_product' => $arr['id_product'],
                'id_shop' => $shopId,
                'redirect_type' => $object->redirect_type,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_product';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_product = ' . (int) $arr['id_product']);
            $item['ets_seo_product'] = $list;
            $currentData[] = $item;
        }
        $offset += $limit;
        $pointers['startOffset'] = $offset;
        if ($offset >= ($pointers['total'] - 1)) {
            $pointers['isCompleted'] = true;
        }
        $data['productPointers'][$shopId] = $pointers;
        $data[$shopId]['product'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    private function exportCategory($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['category']) ? $data[$shopId]['category'] : [];
        $sql = '
			SELECT c.id_category
			FROM `' . _DB_PREFIX_ . 'category` c
            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = ' . (int) $shopId . ')
			WHERE 1 GROUP BY c.id_category ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
        $allCat = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($allCat as $category) {
            $object = new \Category($category['id_category'], null, $shopId);
            $item = [
                'id_category' => $category['id_category'],
                'id_shop' => $shopId,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_category';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_category = ' . (int) $category['id_category']);
            $item['ets_seo_category'] = $list;
            $currentData[] = $item;
        }
        $data[$shopId]['category'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return mixed
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function exportMeta($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['meta']) ? $data[$shopId]['meta'] : [];
        $query = (new \DbQuery())->select('m.id_meta')->from('meta', 'm');
        $query->innerJoin('meta_lang', 'ml', sprintf('m.id_meta = ml.id_meta AND ml.id_shop = %d', (int) $shopId));
        $query->groupBy('m.id_meta');
        $allMeta = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        foreach ($allMeta as $meta) {
            $object = new \Meta($meta['id_meta'], null, $shopId);
            $item = [
                'id_meta' => $meta['id_meta'],
                'id_shop' => $shopId,
                'title' => $object->title,
                'description' => $object->description,
                'url_rewrite' => $object->url_rewrite,
                'page' => $object->page,
            ];
            $tableName = 'ets_seo_meta';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_meta = ' . (int) $meta['id_meta']);
            $item['ets_seo_meta'] = $list;
            $currentData[] = $item;
        }
        $data[$shopId]['meta'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    private function exportRedirect($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $query = (new \DbQuery())->select('*')->from('ets_seo_redirect');
        $query->where(sprintf('id_shop = %d', (int) $shopId));
        $allRedirect = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $data[$shopId]['redirect'] = $allRedirect;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    private function exportManufacturer($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['manufacturer']) ? $data[$shopId]['manufacturer'] : [];
        $sql = 'SELECT m.id_manufacturer FROM `' . _DB_PREFIX_ . 'manufacturer` m 
        INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` manufacturer_shop 
        ON (manufacturer_shop.id_manufacturer = m.id_manufacturer AND manufacturer_shop.id_shop = ' . (int) $shopId . ')';
        $allManufacturers = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($allManufacturers as $manufacturer) {
            $object = new \Manufacturer($manufacturer['id_manufacturer']);
            $item = [
                'id_manufacturer' => $manufacturer['id_manufacturer'],
                'id_shop' => $shopId,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_manufacturer';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_manufacturer = ' . (int) $manufacturer['id_manufacturer']);
            $tableName2 = 'ets_seo_manufacturer_url';
            $list2 = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName2 . '` WHERE id_manufacturer = ' . (int) $manufacturer['id_manufacturer']);
            $item['ets_seo_manufacturer'] = $list;
            $item['ets_seo_manufacturer_url'] = $list2;
            $currentData[] = $item;
        }
        $data[$shopId]['manufacturer'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function exportSupplier($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['supplier']) ? $data[$shopId]['supplier'] : [];
        $query = new \DbQuery();
        $query->select('s.id_supplier');
        $query->from('supplier', 's');
        $jShop = 'INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` supplier_shop 
        ON (supplier_shop.id_supplier = s.id_supplier AND supplier_shop.id_shop = ' . (int) $shopId . ')';
        $query->join($jShop);
        $query->groupBy('s.id_supplier');
        $allSup = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        foreach ($allSup as $supplier) {
            $object = new \Supplier($supplier['id_supplier']);
            $item = [
                'id_supplier' => $supplier['id_supplier'],
                'id_shop' => $shopId,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_supplier';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_supplier = ' . (int) $supplier['id_supplier']);
            $tableName2 = 'ets_seo_supplier_url';
            $list2 = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName2 . '` WHERE id_supplier = ' . (int) $supplier['id_supplier']);
            $item['ets_seo_supplier'] = $list;
            $item['ets_seo_supplier_url'] = $list2;
            $currentData[] = $item;
        }
        $data[$shopId]['supplier'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function exportCms($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['cms']) ? $data[$shopId]['cms'] : [];
        $query = new \DbQuery();
        $query->select('c.id_cms');
        $query->from('cms', 'c');
        $query->innerJoin('cms_shop', 'cms_shop', 'cms_shop.id_cms = c.id_cms AND cms_shop.id_shop = ' . (int) $shopId);
        $query->groupBy('c.id_cms');
        $allCms = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        foreach ($allCms as $cms) {
            $object = new \CMS($cms['id_cms'], null, $shopId);
            $item = [
                'id_cms' => $cms['id_cms'],
                'id_shop' => $shopId,
                'head_seo_title' => $object->head_seo_title,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_cms';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_cms = ' . (int) $cms['id_cms']);
            $item['ets_seo_cms'] = $list;
            $currentData[] = $item;
        }
        $data[$shopId]['cms'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function exportCmsCategory($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['cms_category']) ? $data[$shopId]['cms_category'] : [];
        $query = new \DbQuery();
        $query->select('c.id_cms_category');
        $query->from('cms_category', 'c');
        $query->innerJoin('cms_category_shop', 'cms_category_shop', 'cms_category_shop.id_cms_category = c.id_cms_category AND cms_category_shop.id_shop = ' . (int) $shopId);
        $query->groupBy('c.id_cms_category');
        $allCat = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        foreach ($allCat as $category) {
            $object = new \CMSCategory($category['id_cms_category'], null, $shopId);
            $item = [
                'id_cms_category' => $category['id_cms_category'],
                'id_shop' => $shopId,
                'meta_title' => $object->meta_title,
                'meta_description' => $object->meta_description,
                'link_rewrite' => $object->link_rewrite,
            ];
            $tableName = 'ets_seo_cms_category';
            $list = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . $tableName . '` WHERE id_shop=' . (int) $shopId . ' AND id_cms_category = ' . (int) $category['id_cms_category']);
            $item['ets_seo_cms_category'] = $list;
            $currentData[] = $item;
        }
        $data[$shopId]['cms_category'] = $currentData;

        return $data;
    }

    /**
     * @param int|string $shopId
     * @param array $data
     *
     * @return array
     */
    private function exportConfiguration($shopId, &$data)
    {
        if (!isset($data[$shopId])) {
            $data[$shopId] = [];
        }
        $currentData = isset($data[$shopId]['config']) ? $data[$shopId]['configuration'] : [];
        $seoDef = Ets_Seo_Define::getInstance();
        $configs = $seoDef->fields_config();
        $languages = Language::getLanguages(false);
        foreach ($configs as $group) {
            foreach ($group as $k => $config) {
                $item = [
                    'name' => $k,
                    'id_shop' => $shopId,
                ];
                if (isset($config['textlang']) || isset($config['textareaLang']) || isset($config['selectLang'])) {
                    $item['value'] = [];
                    foreach ($languages as $lang) {
                        $item['value'][$lang['id_lang']] = Configuration::get($k, $lang['id_lang'], null, (int) $shopId);
                    }
                } else {
                    $item['value'] = Configuration::get($k, null, null, (int) $shopId);
                }
                $currentData[] = $item;
            }
        }

        $urlRules = $seoDef->url_rules();
        foreach ($urlRules as $k => $rule) {
            $item = [
                'name' => 'PS_ROUTE_' . $k,
                'id_shop' => $shopId,
                'value' => Configuration::get('PS_ROUTE_' . $k, null, null, (int) $shopId),
            ];
            $currentData[] = $item;
        }

        $data[$shopId]['configuration'] = $currentData;

        return $data;
    }

    /**
     * Write data to file using serialize method
     *
     * @param array $data
     * @param string $filename
     *
     * @return bool
     *
     * @throws \PrestaShopException
     * @throws \RuntimeException
     */
    private function _writeData($data, $filename)
    {
        if (!is_array($data)) {
            throw new \PrestaShopException('data to write must be an array.');
        }
        $concurrentDirectory = self::DATA_FILE_DIRECTORY;
        if (!is_dir($concurrentDirectory)) {
            if (!mkdir($concurrentDirectory, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException('Directory "%s" not exist & attempt to create was not success');
            }
        }
        $str = json_encode($data);

        return false !== file_put_contents($concurrentDirectory . DIRECTORY_SEPARATOR . $filename, $str, LOCK_EX);
    }

    /**
     * @return bool
     */
    private function _unlinkData()
    {
        return @unlink(self::DATA_FILE_DIRECTORY . DIRECTORY_SEPARATOR . self::DATA_STATE_FILE_NAME);
    }

    /**
     * @return array
     */
    private function getStoredData()
    {
        $file = self::DATA_FILE_DIRECTORY . DIRECTORY_SEPARATOR . self::DATA_STATE_FILE_NAME;

        return json_decode(Tools::file_get_contents($file), true);
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
