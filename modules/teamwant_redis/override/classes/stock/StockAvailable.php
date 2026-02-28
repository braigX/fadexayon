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
if (!defined('_PS_VERSION_')) {
    exit;
}

class StockAvailable extends StockAvailableCore
{
    public $enable_redis = false;
    public $use_cache_for_stock_manager = true;

    /**
     * @param null $id
     * @param null $id_lang
     * @param null $id_shop
     * @param null $translator
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);

        if (_PS_CACHE_ENABLED_) {
            $caching_system = _PS_CACHING_SYSTEM_;

            if (
                $caching_system === 'Redis'
                && class_exists(Teamwant_Redis::class)
            ) {
                $this->enable_redis = true;
                $this->use_cache_for_stock_manager = Teamwant_Redis::getCacheConfiguration()['use_cache_for_stock_manager'];
            }
        }
    }

    /**
     * NULL gdy mamy wykonac standardową akcje, bool gdy mamy nadpisać wartość
     *
     * @return boolean|null
     */
    private static function getUseCacheForStockManager()
    {
        if (_PS_CACHE_ENABLED_) {
            $caching_system = _PS_CACHING_SYSTEM_;

            if (
                $caching_system === 'Redis'
                && class_exists(Teamwant_Redis::class)
            ) {
                return (bool) Teamwant_Redis::getCacheConfiguration()['use_cache_for_stock_manager'];
            }
        }

        return null;
    }

    public static function getStockAvailableIdByProductId($id_product, $id_product_attribute = null, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('id_stock_available');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);

        if ($id_product_attribute !== null) {
            $query->where('id_product_attribute = ' . (int) $id_product_attribute);
        }

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        if (($isEnabled = self::getUseCacheForStockManager()) !== null) {
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, $isEnabled);
        }

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product, tells if it depends on the physical (usable) stock.
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     *
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function dependsOnStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('depends_on_stock');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        if (($isEnabled = self::getUseCacheForStockManager()) !== null) {
            return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, $isEnabled);
        }

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product, get its "out of stock" flag.
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     *
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function outOfStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('out_of_stock');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        if (($isEnabled = self::getUseCacheForStockManager()) !== null) {
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, $isEnabled);
        }

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @param int $id_product
     * @param int id_product_attribute Optional
     * @param int $id_shop Optional
     *
     * @return bool|string
     */
    public static function getLocation($id_product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product = (int) $id_product;

        if (null === $id_product_attribute) {
            $id_product_attribute = 0;
        } else {
            $id_product_attribute = (int) $id_product_attribute;
        }

        $query = new DbQuery();
        $query->select('location');
        $query->from('stock_available');
        $query->where('id_product = ' . $id_product);
        $query->where('id_product_attribute = ' . $id_product_attribute);

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        if (($isEnabled = self::getUseCacheForStockManager()) !== null) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, $isEnabled);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given id_product and id_product_attribute, gets its stock available.
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_shop Optional : gets context by default
     *
     * @return int Quantity
     */
    public static function getQuantityAvailableByProduct($id_product = null, $id_product_attribute = null, $id_shop = null)
    {
        $isEnableCache = true;

        // if null, it's a product without attributes
        if ($id_product_attribute === null) {
            $id_product_attribute = 0;
        }

        $key = 'StockAvailable::getQuantityAvailableByProduct_' . (int) $id_product . '-' . (int) $id_product_attribute . '-' . (int) $id_shop;

        if (($isEnabled = self::getUseCacheForStockManager()) !== null) {
            $isEnableCache = $isEnabled;
        }

        if (!Cache::isStored($key) || !$isEnableCache) {
            $query = new DbQuery();
            $query->select('SUM(quantity)');
            $query->from('stock_available');

            // if null, it's a product without attributes
            if ($id_product !== null) {
                $query->where('id_product = ' . (int) $id_product);
            }

            $query->where('id_product_attribute = ' . (int) $id_product_attribute);
            $query = StockAvailable::addSqlShopRestriction($query, $id_shop);
            $result = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, $isEnableCache);

            if ($isEnableCache) {
                Cache::store($key, $result);
            }

            return $result;
        }

        return Cache::retrieve($key);
    }
}
