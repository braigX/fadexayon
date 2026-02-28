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


class EtsAbancartIndex
{
    public static function deleteIndex($id_ets_abancart_campaign = 0, $id_ets_abancart_reminder = 0, $id_cart = 0, $priority = 0)
    {
        if ($id_ets_abancart_campaign > 0 && !Validate::isUnsignedInt($id_ets_abancart_campaign)
            || $id_ets_abancart_reminder > 0 && !Validate::isUnsignedInt($id_ets_abancart_reminder)
            || $id_cart > 0 && !Validate::isUnsignedInt($id_cart)
            || $id_ets_abancart_campaign <= 0 && $id_ets_abancart_reminder <= 0 && $id_cart <= 0
        ) {
            return false;
        }
        if ($priority > 0) {
            $rm = EtsAbancartReminder::nextRun($id_ets_abancart_campaign, $id_ets_abancart_reminder, $priority);
            if ($rm) {
                return (bool)self::updateIndex($id_ets_abancart_campaign, $rm['id_ets_abancart_reminder'], $id_ets_abancart_reminder, $rm['priority'], $id_cart);
            }
        }
        return (bool)Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index` 
            WHERE 1'
            . ($id_ets_abancart_campaign > 0 ? ' AND id_ets_abancart_campaign=' . (int)$id_ets_abancart_campaign : '')
            . ($id_ets_abancart_reminder > 0 ? ' AND id_ets_abancart_reminder=' . (int)$id_ets_abancart_reminder : '')
            . ($id_cart > 0 ? ' AND id_cart=' . (int)$id_cart : '')
        );
    }

    /**
     * @param $cart Cart
     * @param $customer Customer
     * @param int $id_ets_abancart_campaign
     * @return bool
     */
    public static function addCartIndex($cart, $customer, $id_ets_abancart_campaign = 0, $id_ets_abancart_reminder = 0, $reIndex = false)
    {
        if (!$cart instanceof Cart ||
            $cart->id <= 0 ||
            Order::getIdByCartId($cart->id) > 0 ||
            !$customer instanceof Customer ||
            $customer->id <= 0 ||
            $id_ets_abancart_campaign > 0
            && (
                !Validate::isUnsignedInt($id_ets_abancart_campaign) ||
                !($campaign = new EtsAbancartCampaign($id_ets_abancart_campaign)) ||
                $campaign->id < 1 ||
                $campaign->enabled < 1
            ) ||
            $reIndex && !self::deleteIndex($id_ets_abancart_campaign, $id_ets_abancart_reminder, $cart->id)
        ) {
            return false;
        }
        $context = Context::getContext();
        $backups = [
            'cart' => $context->cart,
            'currency' => $context->currency,
            'customer' => $context->customer,
        ];
        $context->cart = $cart;
        $context->currency = Currency::getCurrencyInstance($cart->id_currency ?: Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->customer = $customer;

        $group = new Group($customer->id_default_group ?: Group::getCurrent()->id);
        $total_cart = Tools::convertPrice($cart->getOrderTotal(!$group->price_display_method, Cart::BOTH, $cart->getProducts(true), $cart->id_carrier), $cart->id_currency, false);
        $has_applied_voucher = ($vouchers = $cart->getCartRules()) && is_array($vouchers) && count($vouchers) > 0 ? 1 : 0;
        $current_date = date('Y-m-d');
        $id_address = Address::getFirstCustomerAddressId($customer->id);

        $dq = new DbQuery();
        $dq
            ->select('ac.*')
            ->from('ets_abancart_campaign', 'ac')
            ->leftJoin('ets_abancart_campaign_group', 'acg', 'acg.id_ets_abancart_campaign = ac.id_ets_abancart_campaign')
            ->leftJoin('group_shop', 'gs', 'gs.id_group = acg.id_group AND gs.id_shop=' . (int)$cart->id_shop)
            ->leftJoin('customer_group', 'cg', 'cg.id_group = gs.id_group AND cg.id_customer=' . (int)$customer->id)
            ->leftJoin('ets_abancart_campaign_with_lang', 'cl', 'cl.id_ets_abancart_campaign = ac.id_ets_abancart_campaign AND cl.id_lang = ' . (int)$cart->id_lang)
            ->leftJoin('ets_abancart_campaign_country', 'acc', 'acc.id_ets_abancart_campaign = ac.id_ets_abancart_campaign')
            ->leftJoin('address', 'a', 'a.id_country = acc.id_country AND a.id_customer = ' . (int)$customer->id)
            ->where('ac.campaign_type = \'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_EMAIL) . '\'')
            ->where('ac.id_shop = ' . (int)$cart->id_shop)
            ->where('ac.enabled = 1 AND ac.deleted = 0')
            ->where('cg.id_group is NOT NULL')
            ->where('IF(ac.is_all_lang != 1, cl.id_ets_abancart_campaign is NOT NULL AND cl.id_lang=' . (int)$cart->id_lang . ', 1)')
            ->where('IF(ac.min_total_cart is NOT NULL OR ac.min_total_cart != \'\', ac.min_total_cart <= ' . (float)$total_cart . ', 1)')
            ->where('IF(ac.max_total_cart is NOT NULL OR ac.max_total_cart != \'\', ac.max_total_cart >= ' . (float)$total_cart . ', 1)')
            ->where('IF(ac.has_applied_voucher = \'' . pSQL(EtsAbancartCampaign::APPLIED_VOUCHER_BOTH) . '\' OR (ac.has_applied_voucher = \'' . pSQL(EtsAbancartCampaign::APPLIED_VOUCHER_YES) . '\' AND ' . (int)$has_applied_voucher . ' > 0) OR (ac.has_applied_voucher = \'' . pSQL(EtsAbancartCampaign::APPLIED_VOUCHER_NO) . '\' AND ' . (int)$has_applied_voucher . ' = 0), 1, 0)')
            ->where('IF(ac.available_from is NOT NULL, ac.available_from <= "' . pSQL($current_date) . '", 1) AND IF(ac.available_to is NOT NULL, ac.available_to >= "' . pSQL($current_date) . '", 1)')
            ->where('IF(ac.is_all_country != 1, a.id_country > 0 AND a.id_country=acc.id_country OR acc.id_country = -1 OR ' . (int)$id_address . '<1 AND acc.id_country=' . (int)$context->country->id . ', 1)')
            ->groupBy('ac.id_ets_abancart_campaign');

        if ($id_ets_abancart_campaign > 0) {
            $dq
                ->where('ac.id_ets_abancart_campaign=' . (int)$id_ets_abancart_campaign);
        }
        $ip_address = Tools::getRemoteAddr();
        if ($ip_address == '::1')
            $ip_address = '127.0.0.1';
        if ($res = Db::getInstance()->executeS($dq)) {
            $query = [];
            foreach ($res as $item) {
                $last_cp_run = Db::getInstance()->getRow('SELECT `id_ets_abancart_reminder`, `id_ets_abancart_campaign`, `last_campaign_run`, `priority` FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` WHERE `id_cart`=' . (int)$cart->id . ' AND `id_ets_abancart_campaign`=' . (int)$item['id_ets_abancart_campaign'] . ' ORDER BY `priority` DESC');
                $last_cp_run_time = null;
                if ($last_cp_run) {
                    $next_rm_run = EtsAbancartReminder::nextRun((int)$item['id_ets_abancart_campaign'], (int)$last_cp_run['id_ets_abancart_reminder'], (int)$last_cp_run['priority']);
                    $last_cp_run_time = !empty($last_cp_run['last_campaign_run']) ? $last_cp_run['last_campaign_run'] : null;
                } else {
                    $next_rm_run = EtsAbancartReminder::nextRun((int)$item['id_ets_abancart_campaign']);
                }
                if (empty($next_rm_run)) {
                    continue;
                }
                if (isset($item['last_order_from']) && trim($item['last_order_from']) !== '' || isset($item['last_order_to']) && trim($item['last_order_to']) !== '') {
                    $dq = new DbQuery();
                    $dq
                        ->select('o.date_add')
                        ->from('orders', 'o')
                        ->where('o.id_customer=' . (int)$customer->id)
                        ->orderBy('o.date_add DESC');
                    $last_order = Db::getInstance()->getValue($dq);
                    if (!$last_order ||
                        (isset($item['last_order_from']) && trim($item['last_order_from']) !== '' && strtotime($last_order) < strtotime($item['last_order_from'])) ||
                        (isset($item['last_order_to']) && trim($item['last_order_to']) !== '' && strtotime($last_order) < strtotime($item['last_order_to']))
                    ) {
                        continue;
                    }
                }
                $query[] = '(
                    ' . (int)$cart->id . '
                    , ' . (int)$next_rm_run['id_ets_abancart_reminder'] . '
                    , ' . (int)$item['id_ets_abancart_campaign'] . '
                    , ' . (int)$cart->id_shop . '
                    , ' . (int)$cart->id_customer . '
                    , \'' . pSQL($customer->firstname) . '\'
                    , \'' . pSQL($customer->lastname) . '\'
                    , \'' . pSQL($customer->email) . '\'
                    , ' . (int)$cart->id_lang . '
                    , ' . (float)$total_cart . '
                    , \'' . pSQL($cart->date_add) . '\'
                    , \'' . pSQL(($last_cp_run_time !== null ? $last_cp_run_time : $cart->date_add)) . '\'
                    , ' . (int)$next_rm_run['priority'] . '
                    , \'' . pSQL($cart->date_upd) . '\'
                    , \'' . pSQL($ip_address) . '\'
                )';
            }
            if ($query) {
                Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_abancart_index`(
                    `id_cart`
                    , `id_ets_abancart_reminder`
                    , `id_ets_abancart_campaign`
                    , `id_shop`
                    , `id_customer`
                    , `firstname`
                    , `lastname`
                    , `email`
                    , `id_cart_lang`
                    , `total_cart`
                    , `cart_date_add`
                    , `last_campaign_run`
                    , `priority`
                    , `date_upd`
                    , `ip_address`
                ) VALUES' . implode(',', $query));
            }
        }

        foreach ($backups as $key => $backup) {
            $context->$key = $backup;
        }

        return true;
    }

    public static function clearDiscountIsExpired()
    {
        $query = '
            SELECT cr.id_cart_rule 
            FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr ON (ccr.id_cart_rule = cr.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON (c.id_cart = ccr.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = c.id_cart)
            WHERE cr.date_to < \'' . pSQL(date('Y-m-d H:i:s')) . '\' AND o.id_cart is NULL AND ccr.id_cart is NULL
        ';
        $cart_rules = Db::getInstance()->executeS($query);
        $totalDeleted = 0;
        foreach ($cart_rules as $item) {
            $cart_rule = new CartRule($item['id_cart_rule']);
            if ($cart_rule->id && $cart_rule->delete()) {
                ++$totalDeleted;
            }
        }

        return $totalDeleted;
    }

    public static function nextIndexRun($index, $next_rm, $last_campaign_run = null)
    {
        if ($last_campaign_run == null)
            $last_campaign_run = date('Y-m-d H:i:s');
        return (bool)Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_index` 
                SET `id_ets_abancart_reminder`=' . (int)$next_rm['id_ets_abancart_reminder'] . '
                    , `priority`=' . (int)$next_rm['priority'] . '
                    , `last_campaign_run` = \'' . pSQL($last_campaign_run) . '\'
            WHERE `id_ets_abancart_reminder`=' . (int)$index['id_ets_abancart_reminder'] . ' AND `id_ets_abancart_campaign`=' . (int)$index['id_ets_abancart_campaign'] . ' AND `id_cart`=' . (int)$index['id_cart'] . '
        ');
    }

    public static function updateIndex($id_ets_abancart_campaign, $id_ets_abancart_reminder_new, $id_ets_abancart_reminder_old, $priority, $id_cart = 0)
    {
        return (bool)Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_index` 
                SET `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder_new . ', `priority`=' . (int)$priority . '
            WHERE `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder_old . ' AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign
            . ($id_cart > 0 ? ' AND id_cart=' . (int)$id_cart : '') . '
        ');
    }
}