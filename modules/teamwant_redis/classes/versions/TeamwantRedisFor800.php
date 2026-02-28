<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */

namespace Teamwant\Prestashop17\Redis\Classes\Versions;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Cache;
use Context;
use Teamwant\Prestashop17\Redis\Classes\TeamwantRedis;
use Teamwant\Prestashop17\Redis\Classes\TeamwantRedisCacheConfig;
use Teamwant_redis\OverrideSrc\OverrideHookFile;
use Tools;
use Image;
use Language;
use Shop;
use Db;
use Pack;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Product;
use ProductAssembler;
use ProductPresenterFactory;
use TaxConfiguration;

trait TeamwantRedisVersion
{
    use TeamwantRedis;
    use TeamwantRedisCacheConfig;

    private $tw_product_list = [];

    public function install()
    {
        $this->createCustomOverride($this->prestashopVersion);

        // custom override presta files
        load_Teamwant_redis_OverrideSrc();

        // custom override presta files
        $this->registerAdminControllers();

        // tworzenie startowego pliku
        $this->createDefaultConfigFile();

        // from 1.6.2
        OverrideHookFile::install();

        if (version_compare(_PS_VERSION_, '1.7.7.5', '<=')) {
            \Teamwant_redis\OverrideSrc\OverrideSrcCachingType::install();
        }

        // dir dla 1.7.8
        if (!is_dir(_PS_OVERRIDE_DIR_ . '/controllers/front/listing')) {
            @mkdir(_PS_OVERRIDE_DIR_ . '/controllers/front/listing', 0777, true);
        }

        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionClearCompileCache')
            && $this->registerHook('actionObjectProductUpdateAfter')
            && $this->registerHook('actionObjectCombinationUpdateAfter')
            && $this->registerHook('actionObjectUpdateAfter')
            && $this->registerHook('actionOrderHistoryAddAfter')
            && $this->registerHook('actionPerformancePagecachingForm')
            && $this->registerHook('actionTeamwantRedisUpdateProductImage')
            && $this->registerHook('actionDispatcherAfter')
            && $this->registerHook('actionObjectImageAddAfter')
            && $this->registerHook('actionObjectImageDeleteBefore')
            && $this->registerHook('actionOnImageResizeAfter')
            // && $this->registerHook('actionObjectStockAvailableUpdateAfter')
        ;
    }

    public function enable($force_all = false)
    {
        $this->createCustomOverride($this->prestashopVersion);

        // custom override presta files
        load_Teamwant_redis_OverrideSrc();
        $this->registerHook('displayBackOfficeHeader');
        $this->registerHook('actionClearCompileCache');
        $this->registerHook('actionObjectProductUpdateAfter');
        $this->registerHook('actionObjectCombinationUpdateAfter');
        $this->registerHook('actionObjectUpdateAfter');
        $this->registerHook('actionOrderHistoryAddAfter');
        $this->registerHook('actionPerformancePagecachingForm');
        $this->registerHook('actionTeamwantRedisUpdateProductImage');
        $this->registerHook('actionDispatcherAfter');
        $this->registerHook('actionObjectImageAddAfter');
        $this->registerHook('actionObjectImageDeleteBefore');
        $this->registerHook('actionOnImageResizeAfter');
        $this->registerAdminControllers();

        // from 1.6.2
        OverrideHookFile::install();

        if (version_compare(_PS_VERSION_, '1.7.7.5', '<=')) {
            \Teamwant_redis\OverrideSrc\OverrideSrcCachingType::install();
        }

        return parent::enable($force_all);
    }

    public function hookActionPerformancePagecachingForm(array $params)
    {
        $this->disableBrowserCacheForAdmin();
        $params['form_builder'] = \Teamwant\Teamwantpredis\TeamwantRedisCachingTypeForm::getPerformanceForm($params['form_builder']);
    }

    public function hookActionObjectProductUpdateAfter($data = [])
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0) && (!empty($data['object']) && !empty($data['object']->id))) {
            $this->tw_product_list[] = $data['object']->id;
        }

        if (!empty($data['object']) && !empty($data['object']->id)) {
            $queries = [
                'SELECT *
                FROM `[[prefix]]product` a
                LEFT JOIN `[[prefix]]product_lang` `b` ON a.`id_product` = b.`id_product` AND b.`id_lang` = [[id_lang]]
                LEFT JOIN `[[prefix]]product_shop` `c` ON a.`id_product` = c.`id_product` AND c.`id_shop` = [[id_shop]]
                WHERE (a.`id_product` = [[id_product]]) AND (b.`id_shop` = [[id_shop]]) LIMIT 1',
                'SELECT product_shop.`price`, product_shop.`ecotax`,
                IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on, product_attribute_shop.`ecotax` AS attribute_ecotax
                FROM `[[prefix]]product` p
                INNER JOIN `[[prefix]]product_shop` `product_shop` ON (product_shop.id_product=p.id_product AND product_shop.id_shop = [[id_shop]])
                LEFT JOIN `[[prefix]]product_attribute_shop` `product_attribute_shop` ON (product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = [[id_shop]])
                WHERE (p.`id_product` = [[id_product]]) ',
            ];

            // id_store
            $shops = \Db::getInstance()->executeS('
                SELECT `id_store`
                FROM ' . _DB_PREFIX_ . 'store a
                WHERE 1
            ');

            // id_store
            $langs = \Db::getInstance()->executeS('
                SELECT `id_lang`
                FROM ' . _DB_PREFIX_ . 'lang
                WHERE 1
            ');

            if (empty($shops) || empty($langs)) {
                return;
            }

            foreach ($shops as $shop) {
                foreach ($langs as $lang) {
                    foreach ($queries as $item) {
                        $item = str_replace(['[[prefix]]', '[[id_shop]]', '[[id_lang]]', '[[id_product]]'], [_DB_PREFIX_, $shop['id_store'], $lang['id_lang'], $data['object']->id], $item);
                        \Cache::getInstance()->delete(
                            \Cache::getInstance()->getQueryHash($item)
                        );
                    }
                }
                \Cache::getInstance()->delete(
                    'StockAvailable::getQuantityAvailableByProduct_' . (int) $data['object']->id . '-' . (int) 0 . '-' . (int) $shop['id_store']
                );
            }

            \Cache::getInstance()->delete(
                'StockAvailable::getQuantityAvailableByProduct_' . (int) $data['object']->id . '-' . (int) 0 . '-' . (int) 0
            );
        }
    }

    public function hookActionObjectCombinationUpdateAfter($data = [])
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0) && (!empty($data['object']) && !empty($data['object']->id))) {
            $this->tw_product_list[] = $data['object']->id_product;
        }
        
        if (!empty($data['object']) && !empty($data['object']->id)) {
            $queries = [
                'SELECT *
                FROM `[[prefix]]product` a
                LEFT JOIN `[[prefix]]product_lang` `b` ON a.`id_product` = b.`id_product` AND b.`id_lang` = [[id_lang]]
                LEFT JOIN `[[prefix]]product_shop` `c` ON a.`id_product` = c.`id_product` AND c.`id_shop` = [[id_shop]]
                WHERE (a.`id_product` = [[id_product]]) AND (b.`id_shop` = [[id_shop]]) LIMIT 1',
                'SELECT product_shop.`price`, product_shop.`ecotax`,
                IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on, product_attribute_shop.`ecotax` AS attribute_ecotax
                FROM `[[prefix]]product` p
                INNER JOIN `[[prefix]]product_shop` `product_shop` ON (product_shop.id_product=p.id_product AND product_shop.id_shop = [[id_shop]])
                LEFT JOIN `[[prefix]]product_attribute_shop` `product_attribute_shop` ON (product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = [[id_shop]])
                WHERE (p.`id_product` = [[id_product]]) ',
                'SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                FROM `[[prefix]]image` i
                ' . \Shop::addSqlAssociation('image', 'i') . '
                LEFT JOIN `[[prefix]]image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = [[id_lang]])
                WHERE i.`id_product` = [[id_product]]
                ORDER BY `position`',
                'SELECT `id_product_attribute`
                FROM `[[prefix]]product_attribute`
                WHERE `id_product` = [[id_product]]',
            ];

            // id_store
            $shops = \Db::getInstance()->executeS('
                SELECT `id_store`
                FROM ' . _DB_PREFIX_ . 'store a
                WHERE 1
            ');

            // id_store
            $langs = \Db::getInstance()->executeS('
                SELECT `id_lang`
                FROM ' . _DB_PREFIX_ . 'lang
                WHERE 1
            ');

            foreach ($shops as $shop) {
                foreach ($langs as $lang) {
                    foreach ($queries as $item) {
                        $item = str_replace(['[[prefix]]', '[[id_shop]]', '[[id_lang]]', '[[id_product]]'], [_DB_PREFIX_, $shop['id_store'], $lang['id_lang'], $data['object']->id_product], $item);
                        \Cache::getInstance()->delete(
                            \Cache::getInstance()->getQueryHash($item)
                        );
                    }
                }
            }

            if (\Combination::isFeatureActive()) {
                $product_attributes = \Db::getInstance()->executeS(
                    'SELECT `id_product_attribute`
                    FROM `' . _DB_PREFIX_ . 'product_attribute`
                    WHERE `id_product` = ' . (int) $data['object']->id_product
                );

                if ($product_attributes) {
                    $ids = [];

                    foreach ($product_attributes as $product_attribute) {
                        $ids[] = (int) $product_attribute['id_product_attribute'];

                        foreach ($shops as $shop) {
                            \Cache::getInstance()->delete(
                                'StockAvailable::getQuantityAvailableByProduct_' . (int) $data['object']->id_product . '-' . (int) $product_attribute['id_product_attribute'] . '-' . (int) $shop['id_store']
                            );
                        }
                        \Cache::getInstance()->delete(
                            'StockAvailable::getQuantityAvailableByProduct_' . (int) $data['object']->id_product . '-' . (int) $product_attribute['id_product_attribute'] . '-' . (int) 0
                        );
                    }

                    foreach ($langs as $lang) {
                        \Cache::getInstance()->delete(
                            \Cache::getInstance()->getQueryHash('
                            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
                            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
                            WHERE pai.`id_product_attribute` IN (' . implode(', ', $ids) . ') AND il.`id_lang` = ' . (int) $lang['id_lang'] . ' ORDER by i.`position`')
                        );
                    }
                }
            }
        }
    }

    public function hookActionObjectUpdateAfter($data = [])
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0)) {
            return $this->hookActionObjectUpdateAfterLegacy($data);
        }

        $def = \ObjectModel::getDefinition($data['object']);
        if (
            strpos($def['table'], 'session') !== false
            || strpos($def['table'], 'cache') !== false
            || strpos($def['table'], 'configuration') !== false
        ) {
            return;
        }

        if (in_array($def['table'], ['image']) && $data['object']->id_product) {
            $this->tw_product_list[] = $data['object']->id_product;
            return;
        }

        $cache = Cache::getInstance();
        if ($cache instanceof \Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
            $cache->delete('objectmodel_' . $def['classname'] . '_' . (int) $data['object']->id . '_*');
            $cache->deleteOnGetFlag = true;
            $this->hookActionObjectUpdateAfterLegacy($data);
            $cache->deleteOnGetFlag = false;
        }
    }

    public function hookActionObjectUpdateAfterLegacy($data = [])
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (isset($_REQUEST['_DISABLE_REDIS_']) && $_REQUEST['_DISABLE_REDIS_'] === true) {
            return;
        }
        if (!empty($data['object']) && !empty($data['object']->id)) {
            $cache = Cache::getInstance();

            if (!$cache instanceof \Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                return;
            }


            $def = \ObjectModel::getDefinition($data['object']);
            if (
                strpos($def['table'], 'session') !== false
                || strpos($def['table'], 'cache') !== false
                || strpos($def['table'], 'configuration') !== false
            ) {
                return;
            }

            if (isset($def['multilang']) && $def['multilang'] === true) {
                //override for languages
                foreach (\Language::getIDs() as $idl) {
                    $this->overrideObjectCache($data['object'], $idl);
                }
            }

            $this->overrideObjectCache($data['object']);
        }
    }

    public function overrideObjectCache($object, $id_lang = null)
    {
        if (isset($_REQUEST['_DISABLE_REDIS_']) && $_REQUEST['_DISABLE_REDIS_'] === true) {
            return;
        }
        try {
            if ($object->id) {
                $cache = Cache::getInstance();

                if ($cache instanceof \Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                    $cache->setForceSave(true);
                    $id = $object->id;
                    $id_lang = $id_lang ?: $object->id_lang;
                    // $entity = $object;
                    $className = get_class($object);
                    $entity = $object;
                    $entity_defs = \ObjectModel::getDefinition($className);
                    $id_shop = $object->getShopId();
                    // $should_cache_objects = \ObjectModel::$cache_objects;
                    $cache_id = 'objectmodel_' . $entity_defs['classname'] . '_' . (int) $id . '_' . (int) $id_shop . '_' . (int) $id_lang;
                    $sql = new \DbQuery();
                    $sql->from($entity_defs['table'], 'a');
                    $sql->where('a.`' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id);

                    if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                        $sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.`' . bqSQL($entity_defs['primary']) . '` = b.`' . bqSQL($entity_defs['primary']) . '` AND b.`id_lang` = ' . (int) $id_lang);

                        if ($id_shop && !empty($entity_defs['multilang_shop'])) {
                            $sql->where('b.`id_shop` = ' . (int) $id_shop);
                        }
                    }

                    if (\Shop::isTableAssociated($entity_defs['table'])) {
                        $sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.`' . bqSQL($entity_defs['primary']) . '` = c.`' . bqSQL($entity_defs['primary']) . '` AND c.`id_shop` = ' . (int) $id_shop);
                    }

                    if ($object_datas = \Db::getInstance()->getRow($sql, false)) {

                        if ($sql instanceof \DbQuery) {
                            $sql = $sql->build();
                        }

                        $sql = rtrim($sql, " \t\n\r\0\x0B;") . ' LIMIT 1';
                        Cache::getInstance()->setQuery($sql, $object_datas);

                        $objectVars = get_object_vars($entity);

                        if (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                            $sql = 'SELECT *
                                FROM `' . bqSQL(_DB_PREFIX_ . $entity_defs['table']) . '_lang`
                                WHERE `' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id
                                . (($id_shop && $entity->isLangMultishop()) ? ' AND `id_shop` = ' . (int) $id_shop : '');

                            if ($object_datas_lang = \Db::getInstance()->executeS($sql, true, false)) {

                                if ($sql instanceof \DbQuery) {
                                    $sql = $sql->build();
                                }

                                Cache::getInstance()->setQuery($sql, $object_datas_lang);

                                foreach ($object_datas_lang as $row) {
                                    foreach ($row as $key => $value) {
                                        if ($key != $entity_defs['primary'] && array_key_exists($key, $objectVars)) {
                                            if (!isset($object_datas[$key]) || !is_array($object_datas[$key])) {
                                                $object_datas[$key] = [];
                                            }
                                            $object_datas[$key][$row['id_lang']] = $value;
                                        }
                                    }
                                }
                            }
                        }
                        $entity->id = (int) $id;

                        foreach ($object_datas as $key => $value) {
                            if (array_key_exists($key, $entity_defs['fields']) || $key == $entity_defs['primary']) {
                                $entity->{$key} = $value;
                            } else {
                                unset($object_datas[$key]);
                            }
                        }

                        // if ($should_cache_objects) {
                        //     Cache::store($cache_id, $object_datas);
                        // }
                    }
                    $cache->setForceSave(false);
                }
            }
        } catch (\Throwable $e) {
            // dump($e);exit;
        }
    }

    public function hookActionOrderHistoryAddAfter($params)
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }

        if (!!\Cache::getInstance()->getConfigValueForCache('twredis_update_product_after_order') === false) {
            return;
        }

        //\Module::getInstanceByName('teamwant_redis')->hookActionOrderHistoryAddAfter(['order_history' => (object)['id_order' => 16]]);
        // \Hook::exec('actionOrderHistoryAddAfter', ['order_history' => (object)['id_order' => 16]]);
        if (isset($params['order_history']) && !empty($params['order_history']->id_order)) {
            $orderId = (int)$params['order_history']->id_order;
            $sql = 'SELECT od.product_id
                    FROM ' . _DB_PREFIX_ . 'order_detail od
                    WHERE od.id_order = ' . $orderId . '
                    GROUP BY od.product_id';

            $productIds = \Db::getInstance()->executeS($sql);

            \Cache::getInstance()->setForceSave(true);
            if (!empty($productIds)) {
                foreach ($productIds as $productData) {
                    $mockProduct = new \stdClass();
                    $mockProduct->id = (int)$productData['product_id'];

                    $this->hookActionObjectProductUpdateAfter(['object' => $mockProduct]);
                    $this->hookActionObjectCombinationUpdateAfter(['object' => $mockProduct]);
                }
            }
            \Cache::getInstance()->setForceSave(false);
        }
    }

    public function hookActionTeamwantRedisUpdateProductImage($params)
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        $idProduct = isset($params['id_product']) ? $params['id_product'] : null;
        $idImage = isset($params['id_image']) ? $params['id_image'] : null;
        $this->clearImageCache($idProduct, $idImage);
    }

    public function hookActionObjectImageAddAfter($params)
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0) && (!empty($data['object']) && !empty($data['object']->id))) {
            $this->tw_product_list[] = $params['object']->id_product;
            return;
        }

        $idProduct = isset($params['object']->id_product) ? $params['object']->id_product : null;
        $idImage = isset($params['object']->id_image) ? $params['object']->id_image : null;
        $this->clearImageCache($idProduct, $idImage);
    }

    public function hookActionObjectImageDeleteBefore($params)
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0) && (!empty($data['object']) && !empty($data['object']->id))) {
            $this->tw_product_list[] = $params['object']->id_product;
            return;
        }

        $idProduct = isset($params['object']->id_product) ? $params['object']->id_product : null;
        $idImage = isset($params['object']->id_image) ? $params['object']->id_image : null;
        $this->clearImageCache($idProduct, $idImage);
    }

    public function hookActionOnImageResizeAfter($params)
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }
        if (!!\Configuration::get('twredis_new_cache_refresh', null, null, null, 0) && (!empty($data['object']) && !empty($data['object']->id))) {
            $this->tw_product_list[] = $params['object']->id_product;
            return;
        }

        $idProduct = isset($params['object']->id_product) ? $params['object']->id_product : null;
        $idImage = isset($params['object']->id_image) ? $params['object']->id_image : null;
        $this->clearImageCache($idProduct, $idImage);
    }

    private function clearImageCache($idProduct = null, $id_image = null) {
        $cache = Cache::getInstance();
                
        if ($cache instanceof \Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
            $cache->setForceSave(true);
            $cache->deleteOnGetFlag = true;
            $idProduct = $idProduct ? $idProduct : Tools::getValue('id_product');

            if (!$idProduct) {
                $id_image = $id_image ? $id_image : (int) Tools::getValue('id_image');
                if ($id_image) {
                    $image = new Image($id_image);
                    $idProduct = (int) $image->id_product;
                }
            }

            if (!$idProduct) {
                return false;
            }

            $this->tw_product_list[] = $idProduct;


            foreach (Language::getIDs() as $id_lang) {
                $sql = '
                    SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                    FROM `' . _DB_PREFIX_ . 'image` i
                    ' . Shop::addSqlAssociation('image', 'i') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                    WHERE i.`id_product` = ' . (int) $idProduct . '
                    ORDER BY `position`';

                $cache->clearQuery($sql);
                \Cache::getInstance()->delete(
                    \Cache::getInstance()->getQueryHash($sql)
                );

                $sql = 'SELECT image_shop.`id_image`
                FROM `' . _DB_PREFIX_ . 'image` i
                ' . Shop::addSqlAssociation('image', 'i') . '
                WHERE i.`id_product` = ' . (int) $idProduct . '
                AND image_shop.`cover` = 1';
                $cache->clearQuery($sql);
            }

            $cache->deleteOnGetFlag = false;
            return true;
        }

        return false;
    }

    public function hookActionDispatcherAfter()
    {
        if (empty(\Cache::getInstance()) || get_class(\Cache::getInstance()) !== 'Teamwant\Prestashop17\Redis\Classes\Cache\Redis') {
            return;
        }

        try {
            if (empty($this->tw_product_list)) {
                return;
            }

            $uniqueProductIds = array_filter(array_unique($this->tw_product_list));
            if (empty($uniqueProductIds)) {
                return;
            }

            // $context = Context::getContext();
            // $ppf = new ProductPresenterFactory($context, new TaxConfiguration());
            // $presenter = $ppf->getPresenter();
            // $settings = $ppf->getPresentationSettings();

            $cache = Cache::getInstance();
            if ($cache instanceof \Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                $cache->setForceSave(true); // Wymuś zapis do Redis
                $cache->deleteOnGetFlag = true;
                foreach ($uniqueProductIds as $productId) {
                    foreach (Language::getIDs() as $id_lang) {
                        $p = new Product($productId, true, $id_lang);
                        $p->getImages($id_lang);
                        $p->getCombinationImages($id_lang);
                        $p->getAccessories($id_lang);
                        $p->getAttributesGroups($id_lang);

                        Product::getCover($productId);
                        $cache->clearQuery(
                            '
                            SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                            FROM `' . _DB_PREFIX_ . 'image` i
                            ' . Shop::addSqlAssociation('image', 'i') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                            WHERE i.`id_product` = ' . (int) $productId . '
                            ORDER BY `position`'
                        );
                    }
                }
                $cache->deleteOnGetFlag = false;
                $cache->setForceSave(false); // Wyłącz wymuszanie zapisu
            }

            // Wyczyść listę po przetworzeniu, aby uniknąć ponownego przetwarzania w kolejnych dyspachach
            $this->tw_product_list = [];
        } finally {
            // Always clear the list, even if processing fails
            $this->tw_product_list = [];
        }
    }
}
