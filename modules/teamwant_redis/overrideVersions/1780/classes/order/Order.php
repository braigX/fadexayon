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

class Order extends OrderCore
{
    /**
     * Get the order id by its cart id.
     *
     * @param int $id_cart Cart id
     *
     * @return int $id_order
     */
    public static function getIdByCartId($id_cart)
    {
        if (_PS_CACHE_ENABLED_) {
            $caching_system = _PS_CACHING_SYSTEM_;

            if (
                $caching_system === 'Redis'
                && class_exists(Teamwant_Redis::class)
            ) {
                $sql = 'SELECT `id_order`
                    FROM `' . _DB_PREFIX_ . 'orders`
                    WHERE `id_cart` = ' . (int) $id_cart .
                    Shop::addSqlRestriction();

                $result = Db::getInstance()->getValue($sql, false);

                return !empty($result) ? (int) $result : false;
            }
        }

        return parent::getIdByCartId($id_cart);
    }
}
