<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoSubscriber extends ObjectModel
{
    private static $customers_ranking = [];
    public $id_subscriber;
    public $id_customer;
    public $id_guest;
    public $device;
    public $platform;
    public $session_count = 0;
    public $last_active;
    public $id_player;
    public $unsubscribed = 0;
    public $from_app = 0;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'rg_pushnotifications_subscriber',
        'primary' => 'id_subscriber',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_guest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'device' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'platform' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'session_count' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'last_active' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'id_player' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'unsubscribed' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'from_app' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public static function cleanUnsubscribed($clean_unsubscribed = null)
    {
        if (is_null($clean_unsubscribed)) {
            $clean_unsubscribed = (int) RgPuNoConfig::get('CLEAN_UNSUBSCRIBED');
        }

        if ($clean_unsubscribed) {
            Db::getInstance()->delete('rg_pushnotifications_subscriber', 'unsubscribed = 1');
        }
    }

    public static function getIdSubscribersByGuest($id_guest, $from_app = false)
    {
        $subscribers = Db::getInstance()->executeS('SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `unsubscribed` != 1 AND `from_app` = ' . (int) $from_app . ' AND `id_guest` = ' . (int) $id_guest);

        if (!count($subscribers)) {
            return false;
        }

        $id_subscribers = array_column($subscribers, 'id_subscriber');
        $id_subscribers = array_map('intval', $id_subscribers);

        return $id_subscribers;
    }

    public static function getIdSubscribersByCustomer($id_customer, $from_app = false)
    {
        $subscribers = Db::getInstance()->executeS('SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `unsubscribed` != 1 AND `from_app` = ' . (int) $from_app . ' AND `id_customer` = ' . (int) $id_customer);

        if (!count($subscribers)) {
            return false;
        }

        $id_subscribers = array_column($subscribers, 'id_subscriber');
        $id_subscribers = array_map('intval', $id_subscribers);

        return $id_subscribers;
    }

    public static function getIdPlayerBySubscriber($id_subscriber)
    {
        return Db::getInstance()->getValue('SELECT `id_player`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_subscriber` = ' . (int) $id_subscriber);
    }

    public static function getIdSubscriberByPlayer($id_player)
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_player` = "' . pSQL($id_player) . '"
        ');
    }

    public static function getIdPlayersByIdCustomers($id_customers, $include_guest = false, $extra_params = false)
    {
        $sql = 'SELECT `id_subscriber`, `id_player`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_player` != ""';
        $sql .= ($include_guest || $id_customers ? ' AND (0' : '');

        if ($include_guest) {
            $sql .= ' OR `id_customer` IS NULL OR `id_customer` = 0';
        }

        if ($id_customers) {
            $sql .= ' OR `id_customer` IN(' . implode(',', array_map('intval', $id_customers)) . ')';
        }

        $sql .= ($include_guest || $id_customers ? ')' : '');

        if ($include_guest) {
            if (isset($extra_params['devices']) && $extra_params['devices']) {
                $sql .= ' AND `device` IN ("' . implode('","', array_map('pSQL', $extra_params['devices'])) . '")';
            }

            if (isset($extra_params['platforms']) && $extra_params['platforms']) {
                $sql .= ' AND `platform` IN ("' . implode('","', array_map('pSQL', $extra_params['platforms'])) . '")';
            }

            if (isset($extra_params['last_active']) && $extra_params['last_active']) {
                $sql .= ' AND `last_active` >= "' . pSQL($extra_params['last_active']) . '")';
            }

            if ((isset($extra_params['min_session']) && $extra_params['min_session']) ||
                (isset($extra_params['max_session']) && $extra_params['max_session'])
            ) {
                $sql .= ' AND `session_count` BETWEEN ' . ($extra_params['min_session'] ? (int) $extra_params['min_session'] : 0) . ' AND ' . ($extra_params['max_session'] ? (int) $extra_params['max_session'] : 99999999999);
            }
        }

        $players_data = Db::getInstance()->executeS($sql);
        $id_players = [];

        foreach ($players_data as $data) {
            $id_players[(int) $data['id_subscriber']] = $data['id_player'];
        }

        return $id_players;
    }

    public static function setUnsubcribedPlayer($id_player)
    {
        return Db::getInstance()->update(
            'rg_pushnotifications_subscriber',
            ['id_player' => '', 'unsubscribed' => 1],
            '`id_player` = "' . pSQL($id_player) . '"'
        );
    }

    public static function getTotalsData()
    {
        $data = [
            'total_customer' => (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'customer` WHERE `active` = 1 AND `deleted` = 0
            '),
            'subscribed_customer' => (int) Db::getInstance()->getValue('
                SELECT COUNT(DISTINCT `id_customer`) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` WHERE `id_player` != ""
            '),
            'subscribed_total' => (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` WHERE `unsubscribed` != 1
            '),
        ];

        return $data;
    }

    public static function getDevices()
    {
        return Db::getInstance()->executeS('
            SELECT DISTINCT `device`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `device` != ""
        ');
    }

    public static function getPlatforms()
    {
        return [
            0 => 'iOS',
            1 => 'ANDROID',
            2 => 'AMAZON',
            3 => 'WINDOWSPHONE (MPNS)',
            4 => 'CHROME APPS / EXTENSIONS',
            5 => 'CHROME WEB PUSH',
            6 => 'WINDOWSPHONE (WNS)',
            7 => 'SAFARI',
            8 => 'FIREFOX',
            9 => 'MACOS',
            10 => 'ALEXA',
            11 => 'EMAIL',
        ];
    }

    public static function searchSubscribedCustomer($query, $limit = null)
    {
        $sql_base = 'SELECT * FROM `' . _DB_PREFIX_ . 'customer`';
        $where_base = ' `id_customer` IN(SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` WHERE `id_player` != "") AND ';
        $sql = '(' . $sql_base . ' WHERE' . $where_base . '`email` LIKE \'%' . pSQL($query) . '%\' ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . ')';
        $sql .= ' UNION (' . $sql_base . ' WHERE' . $where_base . '`id_customer` = ' . (int) $query . ' ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . ')';
        $sql .= ' UNION (' . $sql_base . ' WHERE' . $where_base . '`lastname` LIKE \'%' . pSQL($query) . '%\' ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . ')';
        $sql .= ' UNION (' . $sql_base . ' WHERE' . $where_base . '`firstname` LIKE \'%' . pSQL($query) . '%\' ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . ')';

        if ($limit) {
            $sql .= ' LIMIT 0, ' . (int) $limit;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function searchSubscribedCustomerByIdCustomer($id_customers)
    {
        if (count($id_customers)) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` IN(SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` WHERE `id_player` != "")
                AND `id_customer` IN(' . implode(',', array_map('intval', $id_customers)) . ')';

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }

        return [];
    }

    public static function getCustomerIdLangByIdsPlayer($ids_player)
    {
        $ids_lang = [];

        foreach ($ids_player as $id_player) {
            $ids_lang[$id_player] = (int) Db::getInstance()->getValue('
                SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` = (
                    SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
                    WHERE `id_player` = "' . pSQL($id_player) . '"
                    LIMIT 1
                )
            ');
        }

        return $ids_lang;
    }

    public static function getCustomersToNotify($params)
    {
        $sql = 'SELECT c.`id_customer`, c.`email` FROM `' . _DB_PREFIX_ . 'customer` c' .
            ($params['min_sell'] || $params['max_sell'] || $params['min_valid_orders'] || $params['max_valid_orders'] ? ' LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON(c.`id_customer` = o.`id_customer` AND o.`valid` = 1)' : '') .
            ' WHERE c.`id_customer` IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` WHERE `unsubscribed` != 1)' .
            ' AND c.`active` = 1 ' .
            ' AND c.`newsletter` IN(' . pSQL($params['newsletter']) . ') ' .
            ' AND c.`optin` IN(' . pSQL($params['optin']) . ') ' .
            ($params['payment_method_list'] ? ' AND c.`id_customer` IN(SELECT DISTINCT `id_customer` FROM `' . _DB_PREFIX_ . 'orders` WHERE `module` IN("' . implode('","', array_map('pSQL', $params['payment_method_list'])) . '"))' : '') .
            ($params['carrier_list'] ? ' AND c.`id_customer` IN(SELECT DISTINCT `id_customer` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_carrier` IN(' . implode(',', array_map('intval', $params['carrier_list'])) . '))' : '') .
            ($params['currency_list'] ? ' AND c.`id_customer` IN(SELECT DISTINCT `id_customer` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_currency` IN(' . implode(',', array_map('intval', $params['currency_list'])) . '))' : '') .
            ($params['gender'] ? ' AND c.`id_gender` IN(' . implode(',', array_map('intval', $params['gender'])) . ')' : '') .
            ($params['languages_list'] ? ' AND c.`id_lang` IN(' . implode(',', array_map('intval', $params['languages_list'])) . ')' : '') .
            ($params['shops_list'] ? ' AND c.`id_shop` IN(' . implode(',', array_map('intval', $params['shops_list'])) . ')' : '') .
            ($params['min_registration'] || $params['max_registration'] ? ' AND c.`date_add` BETWEEN "' . ($params['min_registration'] ? pSQL($params['min_registration']) : '2000-01-01 00:00:00') . '" AND "' . ($params['max_registration'] ? pSQL($params['max_registration']) : '2100-01-01 00:00:00') . '"' : '') .
            ($params['countries'] ? ' AND c.`id_customer` IN(SELECT DISTINCT `id_customer` FROM `' . _DB_PREFIX_ . 'address` WHERE `id_country` IN(' . implode(',', array_map('intval', $params['countries'])) . '))' : '') .
            ($params['groups'] ? ' AND c.`id_customer` IN(SELECT DISTINCT `id_customer` FROM `' . _DB_PREFIX_ . 'customer_group` WHERE `id_group` IN(' . implode(',', array_map('intval', $params['groups'])) . '))' : '') .
            ($params['min_sell'] || $params['max_sell'] || $params['min_valid_orders'] || $params['max_valid_orders'] ? 'GROUP BY c.`id_customer` HAVING 1' : '') .
            ($params['min_sell'] || $params['max_sell'] ? ' AND SUM(o.`total_paid_real` / o.`conversion_rate`) BETWEEN ' . ($params['min_sell'] ? (float) $params['min_sell'] : '-1') . ' AND ' . ($params['max_sell'] ? (float) $params['max_sell'] : '999999999999999') : '') .
            ($params['min_valid_orders'] || $params['max_valid_orders'] ? ' AND COUNT(o.`id_order`) BETWEEN ' . ($params['min_valid_orders'] ? (int) $params['min_valid_orders'] : '-1') . ' AND ' . ($params['max_valid_orders'] ? (int) $params['max_valid_orders'] : '999999999999999') : '');

        $customers = Db::getInstance()->executeS($sql);

        foreach ($customers as $key => $custom) {
            if ($params['min_sell_units'] || $params['max_sell_units']) {
                $sell_units = self::getSellUnitsByCustomer((int) $custom['id_customer']);

                if (($params['min_sell_units'] && $sell_units < $params['min_sell_units']) ||
                    ($params['max_sell_units'] && $sell_units > $params['max_sell_units'])
                ) {
                    unset($customers[$key]);

                    continue;
                }
            }

            if ($params['min_sell_units_order'] || $params['max_sell_units_order']) {
                $sell_units_order = self::getSellUnitsOrderByCustomer((int) $custom['id_customer']);

                if (($params['min_sell_units_order'] && $sell_units_order < $params['min_sell_units_order']) ||
                    ($params['max_sell_units_order'] && $sell_units_order > $params['max_sell_units_order'])
                ) {
                    unset($customers[$key]);

                    continue;
                }
            }

            if ($params['min_ranking'] || $params['max_ranking']) {
                $ranking = self::getCustomersRanking((int) $custom['id_customer']);

                if ($ranking && ($params['min_ranking'] && $ranking < $params['min_ranking']) ||
                    ($params['max_ranking'] && $ranking > $params['max_ranking'])
                ) {
                    unset($customers[$key]);

                    continue;
                }
            }

            if ($params['bought_product'] &&
                !self::hasCustomerBoughtProducts((int) $custom['id_customer'], $params['bought_product'], $params['bought_product_days'], $params['bought_product_qty'])
            ) {
                unset($customers[$key]);

                continue;
            }

            if ($params['bought_category'] &&
                !self::hasCustomerBoughtCategories((int) $custom['id_customer'], $params['bought_category'], $params['bought_category_days'], $params['bought_category_qty'])
            ) {
                unset($customers[$key]);

                continue;
            }

            if ($params['bought_manufacturer'] &&
                !self::hasCustomerBoughtManufacturers((int) $custom['id_customer'], $params['bought_manufacturer'], $params['bought_manufacturer_days'], $params['bought_manufacturer_qty'])
            ) {
                unset($customers[$key]);

                continue;
            }

            if (($params['abandoned_cart_time'] ||
                $params['abandoned_cart_amount']) &&
                !self::hasCustomerAbandonedCart((int) $custom['id_customer'], (int) $params['abandoned_cart_time'], (float) $params['abandoned_cart_amount'])
            ) {
                unset($customers[$key]);
            }

            if ($params['devices'] && !self::hasCustomerDevice((int) $custom['id_customer'], $params['devices'])) {
                unset($customers[$key]);
            }

            if ($params['platforms'] && !self::hasCustomerPlatform((int) $custom['id_customer'], $params['platforms'])) {
                unset($customers[$key]);
            }

            if ($params['last_active'] && !self::hasCustomerLastActive((int) $custom['id_customer'], $params['last_active'])) {
                unset($customers[$key]);
            }

            if (($params['min_session'] ||
                $params['max_session']) &&
                !self::hasCustomerSessionCount((int) $custom['id_customer'], $params['min_session'], $params['max_session'])
            ) {
                unset($customers[$key]);
            }
        }

        return $customers;
    }

    private static function getSellUnitsByCustomer($id_customer)
    {
        return (int) Db::getInstance()->getValue('SELECT SUM(`product_quantity`)
            FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.`id_order` = od.`id_order` AND` valid` = 1)
            WHERE `id_customer` = ' . (int) $id_customer);
    }

    private static function getSellUnitsOrderByCustomer($id_customer)
    {
        $max = 0;
        $data = Db::getInstance()->executeS('
            SELECT SUM(`product_quantity`) AS `qty`
            FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.`id_order` = od.`id_order` AND `valid` = 1)
            WHERE `id_customer` = ' . (int) $id_customer . ' GROUP BY od.`id_order`
        ');

        foreach ($data as $d) {
            if ((int) $d['qty'] > $max) {
                $max = (int) $d['qty'];
            }
        }

        return $max;
    }

    private static function getCustomersRanking($id_customer)
    {
        if (!self::$customers_ranking) {
            self::$customers_ranking = Db::getInstance()->executeS('
                SELECT `id_customer`, SUM(`total_paid_real`) AS `amount`
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE `valid` = 1
                GROUP BY `id_customer`
                ORDER BY `amount` DESC
            ');
        }

        foreach (self::$customers_ranking as $pos => $custom) {
            if ((int) $custom['id_customer'] == (int) $id_customer) {
                return $pos + 1;
            }
        }

        return false;
    }

    private static function hasCustomerBoughtProducts($id_customer, $id_products, $days, $qty)
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.`id_order` = od.`id_order` AND `valid` = 1)
            WHERE o.`id_customer` = ' . (int) $id_customer . '
                AND `product_id` IN(' . implode(',', array_map('intval', $id_products)) . ')' .
            ($days ? ' AND date_add >= "' . date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days')) . '"' : '') .
            ($qty ? ' GROUP BY `product_id` HAVING SUM(`product_quantity`) >= ' . (int) $qty : '');

        return (int) Db::getInstance()->getValue($sql);
    }

    private static function hasCustomerBoughtCategories($id_customer, $id_categories, $days, $qty)
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.`id_order` = od.`id_order` AND `valid` = 1)
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON(p.`id_product` = od.`product_id`)
            WHERE o.`id_customer` = ' . (int) $id_customer . '
                AND `id_category_default` IN(' . implode(',', array_map('intval', $id_categories)) . ')' .
            ($days ? ' AND o.`date_add` >= "' . date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days')) . '"' : '') .
            ($qty ? ' GROUP BY `id_category_default` HAVING SUM(`product_quantity`) >= ' . (int) $qty : '');

        return (int) Db::getInstance()->getValue($sql);
    }

    private static function hasCustomerBoughtManufacturers($id_customer, $id_manufacturers, $days, $qty)
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.`id_order` = od.`id_order` AND `valid` = 1)
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON(p.`id_product` = od.`product_id`)
            WHERE o.`id_customer` = ' . (int) $id_customer . '
                AND `id_manufacturer` IN(' . implode(',', array_map('intval', $id_manufacturers)) . ')' .
            ($days ? ' AND o.`date_add` >= "' . date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days')) . '"' : '') .
            ($qty ? ' GROUP BY `id_manufacturer` HAVING SUM(`product_quantity`) >= ' . (int) $qty : '');

        return (int) Db::getInstance()->getValue($sql);
    }

    private static function hasCustomerAbandonedCart($id_customer, $hours, $amount)
    {
        static $customer_carts = false;
        static $last_carts = false;

        if (!$customer_carts) {
            $customer_carts_data = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_customer`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` > 0
                    AND `id_cart` NOT IN(SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders`)
                    AND `date_upd` < "' . date('Y-m-d H:i:s', strtotime('-' . (int) $hours . ' hours')) . '"
                GROUP BY `id_customer`
            ');
            $customer_carts = array_column($customer_carts_data, 'id_cart', 'id_customer');
        }

        if (!$last_carts) {
            $last_carts_data = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_customer`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` > 0
                GROUP BY `id_customer`
            ');
            $last_carts = array_column($last_carts_data, 'id_cart', 'id_customer');
        }

        if (!isset($customer_carts[$id_customer]) ||
            !isset($last_carts[$id_customer]) ||
            $customer_carts[$id_customer] != $last_carts[$id_customer]
        ) {
            return false;
        }

        if ($amount) {
            $cart = new Cart((int) $customer_carts[$id_customer]);

            if ($cart->getOrderTotal() < (float) $amount) {
                return false;
            }
        }

        return (int) $customer_carts[$id_customer];
    }

    private static function hasCustomerDevice($id_customer, $devices)
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_customer` = ' . (int) $id_customer . '
                AND `device` IN ("' . implode('","', array_map('pSQL', $devices)) . '")
        ');
    }

    private static function hasCustomerPlatform($id_customer, $platforms)
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_customer` = ' . (int) $id_customer . '
                AND `platform` IN ("' . implode('","', array_map('pSQL', $platforms)) . '")
        ');
    }

    private static function hasCustomerLastActive($id_customer, $last_active)
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_customer` = ' . (int) $id_customer . ' AND `last_active` >= "' . pSQL($last_active) . '"
        ');
    }

    private static function hasCustomerSessionCount($id_customer, $min_session, $max_session)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_subscriber`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
            WHERE `id_customer` = ' . (int) $id_customer . '
                AND `session_count` BETWEEN ' . ($min_session ? (int) $min_session : 0) . ' AND ' . ($max_session ? (int) $max_session : 99999999999));
    }
}
