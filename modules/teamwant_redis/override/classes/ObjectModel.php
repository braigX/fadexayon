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

abstract class ObjectModel extends ObjectModelCore
{
    
    
    public $has_redis_update_override = true;



    public function update($null_values = false)
    {
        $r = parent::update($null_values);

        // if (in_array(get_class($this), ['Product', 'Category'])) {
        //     //override for languages
        //     foreach (Language::getIDs() as $idl) {
        //         $this->overrideObjectCache($idl);
        //     }
        // }

        $def = $this->def;
        if (isset($def['multilang']) && $def['multilang'] === true) {
            //override for languages
            foreach (\Language::getIDs() as $idl) {
                $this->overrideObjectCache($idl);
            }
        }

        $this->overrideObjectCache();
        $this->clearCache();

        if (
            Tools::getValue('submitAddress')
            || (Tools::getValue('id_address') && Tools::getValue('delete'))
            || (Tools::getValue('id_address') && Tools::getValue('deleteAddress'))
        ) {
            $this->clearCustomerDataAfterUpdate();
        }

        return $r;
    }

    public function add($auto_date = true, $null_values = false)
    {
        $r = parent::add($auto_date, $null_values);
        $this->overrideObjectCache();
        $this->clearCache();

        if (
            Tools::getValue('submitAddress')
            || (Tools::getValue('id_address') && Tools::getValue('delete'))
            || (Tools::getValue('id_address') && Tools::getValue('deleteAddress'))
        ) {
            $this->clearCustomerDataAfterUpdate();
        }

        return $r;
    }

    public function delete()
    {
        $r = parent::delete();
        $this->clearObjectCache();
        $this->clearCache();

        if (
            Tools::getValue('submitAddress')
            || (Tools::getValue('id_address') && Tools::getValue('delete'))
            || (Tools::getValue('id_address') && Tools::getValue('deleteAddress'))
        ) {
            $this->clearCustomerDataAfterUpdate();
        }

        return $r;
    }

    public function clearObjectCache()
    {
        try {
            if ($this->id) {
                $cache = Cache::getInstance();
                
                if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                    $cache->setForceSave(true);
                    $id = $this->id;
                    $id_lang = $this->id_lang;
                    $entity = $this;
                    $entity_defs = $this->def;
                    $id_shop = $this->id_shop;
                    $should_cache_objects = self::$cache_objects;
                    $sql = new DbQuery();
                    $sql->from($entity_defs['table'], 'a');
                    $sql->where('a.`' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id);

                    if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                        $sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.`' . bqSQL($entity_defs['primary']) . '` = b.`' . bqSQL($entity_defs['primary']) . '` AND b.`id_lang` = ' . (int) $id_lang);

                        if ($id_shop && !empty($entity_defs['multilang_shop'])) {
                            $sql->where('b.`id_shop` = ' . (int) $id_shop);
                        }
                    }

                    if (Shop::isTableAssociated($entity_defs['table'])) {
                        $sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.`' . bqSQL($entity_defs['primary']) . '` = c.`' . bqSQL($entity_defs['primary']) . '` AND c.`id_shop` = ' . (int) $id_shop);
                    }

                    if ($sql instanceof DbQuery) {
                        $sql = $sql->build();
                    }

                    $sql = rtrim($sql, " \t\n\r\0\x0B;") . ' LIMIT 1';
                    $cache->clearQuery($sql);

                    if (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                        $sql = 'SELECT *
                                        FROM `' . bqSQL(_DB_PREFIX_ . $entity_defs['table']) . '_lang`
                                        WHERE `' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id
                            . (($id_shop && $entity->isLangMultishop()) ? ' AND `id_shop` = ' . (int) $id_shop : '');

                        if ($sql instanceof DbQuery) {
                            $sql = $sql->build();
                        }

                        $cache->clearQuery($sql);
                    }
                    $cache->setForceSave(false);
                }
            }
        } catch (Throwable $e) {
        }
    }

    public function overrideObjectCache($id_lang = null)
    {
        try {
            if ($this->id) {
                $cache = Cache::getInstance();
                
                if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                    $cache->setForceSave(true);
                    $id = $this->id;
                    $id_lang = $id_lang ?: $this->id_lang;
                    // $entity = $this;
                    $className = get_class($this);
                    $entity = $this;
                    $entity_defs = self::getDefinition($className);
                    $id_shop = $this->id_shop;
                    $should_cache_objects = self::$cache_objects;
                    $cache_id = 'objectmodel_' . $entity_defs['classname'] . '_' . (int) $id . '_' . (int) $id_shop . '_' . (int) $id_lang;
                    $sql = new DbQuery();
                    $sql->from($entity_defs['table'], 'a');
                    $sql->where('a.`' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id);

                    if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                        $sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.`' . bqSQL($entity_defs['primary']) . '` = b.`' . bqSQL($entity_defs['primary']) . '` AND b.`id_lang` = ' . (int) $id_lang);

                        if ($id_shop && !empty($entity_defs['multilang_shop'])) {
                            $sql->where('b.`id_shop` = ' . (int) $id_shop);
                        }
                    }

                    if (Shop::isTableAssociated($entity_defs['table'])) {
                        $sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.`' . bqSQL($entity_defs['primary']) . '` = c.`' . bqSQL($entity_defs['primary']) . '` AND c.`id_shop` = ' . (int) $id_shop);
                    }

                    if ($object_datas = Db::getInstance()->getRow($sql, false)) {

                        if ($sql instanceof DbQuery) {
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

                            if ($object_datas_lang = Db::getInstance()->executeS($sql, true, false)) {

                                if ($sql instanceof DbQuery) {
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

                        if ($should_cache_objects) {
                            Cache::store($cache_id, $object_datas);
                        }
                    }
                    $cache->setForceSave(false);
                }
            }
        } catch (Throwable $e) {
        }
    }

    public function clearCustomerDataAfterUpdate()
    {
        try {
            $cache = Cache::getInstance();

            if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                $n = new CustomerSession(Context::getContext()->customer->id);
                $n->clearObjectCache();
                $n = new Customer(Context::getContext()->customer->id);
                $n->clearObjectCache();
            }
        } catch (Throwable $e) {
        }

        try {
            $cache = Cache::getInstance();

            if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                if ($this->id) {
                    $id_lang = Context::getContext()->language->id;
                    $customer = Context::getContext()->customer;

                    if ($customer->id) {
                        $group = Context::getContext()->shop->getGroup();
                        $shareOrder = isset($group->share_order) ? (bool) $group->share_order : false;
                        $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso
                            FROM `' . _DB_PREFIX_ . 'address` a
                            LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON (a.`id_country` = c.`id_country`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                            ' . ($shareOrder ? '' : Shop::addSqlAssociation('country', 'c')) . '
                            WHERE `id_lang` = ' . (int) $id_lang . ' AND `id_customer` = ' . (int) $customer->id . ' AND a.`deleted` = 0';
                        $cache->delete($cache->getQueryHash($sql));
                    }
                }
            }
        } catch (Throwable $e) {
        }

        try {
            $cache = Cache::getInstance();

            if ($cache instanceof Teamwant\Prestashop17\Redis\Classes\Cache\Redis) {
                if ($this->id) {
                    $id_lang = Context::getContext()->language->id;
                    $customer = Context::getContext()->customer;

                    if ($customer->id) {
                        $sql = $customer->getSimpleAddressSql(null, $id_lang);
                        $cache->delete($cache->getQueryHash($sql));
                        $cache->delete($sql);
                    }
                }
            }
        } catch (Throwable $e) {
        }
    }
}
