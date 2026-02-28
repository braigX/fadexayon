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


class EtsAbancartIndexCustomer
{
    public static function addCustomerIndexScheduleTime()
    {
        $reminder = json_decode(Configuration::getGlobalValue('ETS_ABANCART_REMINDER_RUNNING') ?: '[]', true);
        if (error_get_last() !== JSON_ERROR_NONE || empty($reminder)) {
            $reminder = Db::getInstance()->getRow("
                SELECT ar.`schedule_time`, ar.`id_ets_abancart_campaign`, ar.`id_ets_abancart_reminder`
                FROM `" . _DB_PREFIX_ . "ets_abancart_reminder` ar 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_abancart_campaign` ac ON ac.id_ets_abancart_campaign = ar.id_ets_abancart_campaign 
                WHERE ar.enabled=" . EtsAbancartReminder::REMINDER_STATUS_RUNNING . " 
                    AND ar.deleted=0 
                    AND ac.enabled=1 
                    AND ac.deleted=0 
                    AND ac.email_timing_option=" . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SCHEDULE_TIME . " 
                    AND ar.schedule_time <= '" . pSQL(date('Y-m-d H:i:s')) . "'
                ORDER BY ar.schedule_time DESC
            ");
        }
        if (!empty($reminder)) {
            Configuration::updateGlobalValue('ETS_ABANCART_REMINDER_RUNNING', json_encode($reminder), true);
            $customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT `id_customer`, `email`, `firstname`, `lastname`
                FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `active` = 1 
                    AND `deleted` = 0 
                    AND `date_add` <= \'' . pSQL($reminder['schedule_time']) . '\' 
                    AND `id_customer` > ' . (int)Configuration::getGlobalValue('ETS_ABANCART_LAST_CUSTOMER_ID') . '
                ORDER BY `id_customer`
                LIMIT 500
            ');
            $nb = count($customers);
            if ($nb > 0) {
                $max = $customers[$nb - 1];
                Configuration::updateGlobalValue('ETS_ABANCART_LAST_CUSTOMER_ID', (int)$max['id_customer']);
            }
            foreach ($customers as $customer) {
                if ((int)$customer['id_customer'] < 1)
                    continue;
                $c = new Customer((int)$customer['id_customer']);
                EtsAbancartIndexCustomer::addCustomerIndex(
                    $c
                    , (int)$reminder['id_ets_abancart_campaign']
                    , false
                    , false
                    , false
                    , false
                    , true
                    , (int)$reminder['id_ets_abancart_reminder']
                    , true
                );
            }
            $max_customer_id = (int)Db::getInstance()->getValue('SELECT MAX(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` WHERE `active`=1 AND `deleted`=0 AND `date_add`<=\'' . pSQL($reminder['schedule_time']) . '\'');
            if ($max_customer_id == (int)Configuration::getGlobalValue('ETS_ABANCART_LAST_CUSTOMER_ID') && Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder` SET `enabled`=' . (int)EtsAbancartReminder::REMINDER_STATUS_FINISHED . ' WHERE `id_ets_abancart_reminder`=' . (int)$reminder['id_ets_abancart_reminder'])) {
                Configuration::updateGlobalValue('ETS_ABANCART_LAST_CUSTOMER_ID', 0);
                Configuration::updateGlobalValue('ETS_ABANCART_REMINDER_RUNNING', json_encode([]), true);
            }
        }
    }

    public static function deleteIndex($id_ets_abancart_campaign = 0, $id_ets_abancart_reminder = 0, $id_customer = 0)//, $priority = 0
    {
        if ($id_ets_abancart_campaign > 0 && !Validate::isUnsignedInt($id_ets_abancart_campaign)
            || $id_ets_abancart_reminder > 0 && !Validate::isUnsignedInt($id_ets_abancart_reminder)
            || $id_ets_abancart_campaign <= 0 && $id_ets_abancart_reminder <= 0
        ) {
            return false;
        }
        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index_customer`
            WHERE 1'
            . ($id_ets_abancart_campaign > 0 ? ' AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign : '')
            . ($id_ets_abancart_reminder > 0 ? ' AND `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder : '')
            . ($id_customer > 0 ? ' AND `id_customer`=' . (int)$id_customer : '')
        );
    }

    public static function addCustomerIndex(
          $customer
        , $id_ets_abancart_campaign = 0
        , $isAfterLogin = false
        , $isAfterCustomerCreated = false
        , $isAfterOrder = false
        , $isAfterSubscribeLetter = false
        , $isAfterScheduleTime = false
        , $id_ets_abancart_reminder = 0
        , $reIndex = false // When modify campaign or reminder need reindex upgrade.
    )
    {
        $ip_address = Tools::getRemoteAddr();
        if ($ip_address == '::1')
            $ip_address = '127.0.0.1';
        if (
            !$customer ||
            !$customer instanceof Customer ||
            $id_ets_abancart_campaign
            && (
                !Validate::isUnsignedInt($id_ets_abancart_campaign) ||
                !($campaign = new EtsAbancartCampaign($id_ets_abancart_campaign)) ||
                $campaign->id < 1 ||
                $campaign->enabled < 1
            ) ||
            $reIndex && !EtsAbancartIndexCustomer::deleteIndex($id_ets_abancart_campaign, $id_ets_abancart_reminder, $customer->id)
        ) {
            return false;
        }
        $context = Context::getContext();
        $current_date = date('Y-m-d');
        $dq = new DbQuery();
        $dq
            ->select('ac.*, ar.*')
            ->from('ets_abancart_campaign', 'ac')
            ->leftJoin('ets_abancart_reminder', 'ar', 'ar.id_ets_abancart_campaign = ac.id_ets_abancart_campaign')
            ->leftJoin('ets_abancart_campaign_with_lang', 'cl', 'cl.id_ets_abancart_campaign = ac.id_ets_abancart_campaign AND cl.id_lang=' . (int)$customer->id_lang)
            ->where('ac.campaign_type=\'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_CUSTOMER) . '\'')
            ->where('ac.enabled = 1 AND ac.deleted = 0')
            ->where('ar.deleted = 0')
            ->where('IF(ac.email_timing_option=' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_RUN_NOW . ', ar.enabled = ' . EtsAbancartReminder::REMINDER_STATUS_FINISHED . ', ar.enabled = ' . EtsAbancartReminder::REMINDER_STATUS_RUNNING . ')')
            ->where('ac.id_shop=' . (int)$customer->id_shop)
            ->where('IF(ac.is_all_lang != 1, cl.id_ets_abancart_campaign is NOT NULL AND cl.id_lang=' . (int)$customer->id_lang . ', 1)')
            ->where('IF(ac.available_from is NOT NULL, ac.available_from <= "' . pSQL($current_date) . '", 1) AND IF(ac.available_to is NOT NULL, ac.available_to >= "' . pSQL($current_date) . '", 1)')
            ->groupBy('ac.id_ets_abancart_campaign, ar.id_ets_abancart_reminder');

        if ($isAfterScheduleTime) {
            $dq
                ->where('ac.email_timing_option=' . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SCHEDULE_TIME)
                ->where('ar.schedule_time <= \'' . date('Y-m-d H:i:s') . '\'')
                ->where('ar.schedule_time >= \'' . pSQL($customer->date_add) . '\'');
        }
        if (!$isAfterCustomerCreated) {
            $dq
                ->leftJoin('ets_abancart_campaign_country', 'acc', 'acc.id_ets_abancart_campaign = ac.id_ets_abancart_campaign');
            if ($customer->id) {
                $id_address = Address::getFirstCustomerAddressId($customer->id);
                $dq
                    ->leftJoin('address', 'a', 'a.id_country = acc.id_country AND a.id_customer=' . (int)$customer->id)
                    ->where('IF(ac.is_all_country != 1, a.id_country > 0 AND a.id_country=acc.id_country OR acc.id_country = -1 OR ' . (int)$id_address . '<1 AND acc.id_country=' . (int)$context->country->id . ', 1)');
            } else
                $dq->where('ac.is_all_country = 1 OR acc.id_country = -1 OR acc.id_country=' . (int)$context->country->id);
        }

        if ($id_ets_abancart_campaign > 0)
            $dq->where('ac.id_ets_abancart_campaign=' . (int)$id_ets_abancart_campaign);
        if ($id_ets_abancart_reminder > 0)
            $dq->where('ar.id_ets_abancart_reminder=' . (int)$id_ets_abancart_reminder);

        if ($isAfterLogin) {
            $dq->where('ac.email_timing_option=' . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN);
        } elseif ($isAfterCustomerCreated) {
            $dq->where('ac.email_timing_option=' . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_REGISTRATION);
        } elseif ($isAfterOrder) {
            $dq->where('ac.email_timing_option=' . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_ORDER_COMPLETION);
        } elseif ($isAfterSubscribeLetter) {
            $dq->where('ac.email_timing_option=' . (int)EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SUBSCRIBE_LETTER);
        }

        $sendRepeatOptions = array(EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_ORDER_COMPLETION, EtsAbancartReminder::CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN);

        if ($res = Db::getInstance()->executeS($dq)) {
            $exec = true;
            foreach ($res as $item) {
                if ((int)$item['id_ets_abancart_reminder'] < 1 || !in_array((int)$item['email_timing_option'], $sendRepeatOptions) && (int)Db::getInstance()->getValue('SELECT `id_ets_abancart_reminder` FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` WHERE `id_customer`' . ($customer->id > 0 ? '=' . (int)$customer->id : ' is NULL') . ' AND `email`=\'' . pSQL($customer->email) . '\' AND `id_ets_abancart_reminder`=' . (int)$item['id_ets_abancart_reminder']) > 0) {
                    continue;
                }
                // Subscriber
                if (!$customer->id) {
                    if ((int)$item['email_timing_option'] !== EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SUBSCRIBE_LETTER)
                        continue;
                } elseif (in_array((int)$item['email_timing_option'], $sendRepeatOptions)) {
                    if ((int)$item['send_repeat_email'] <= 0) {
                        $customerIsRun = self::getCustomerIsRun($customer->id, (int)$item['id_ets_abancart_reminder']);
                        if ($customerIsRun > 0)
                            continue;
                    } elseif ((int)$item['email_timing_option'] == EtsAbancartReminder::CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN) {
                        if (self::reIndexLastLogin($customer->id, (int)$item['id_ets_abancart_reminder']))
                            continue;
                    }
                }

                if (!$isAfterCustomerCreated && isset($item['has_placed_orders']) && trim($item['has_placed_orders']) !== 'all') {
                    if (trim($item['has_placed_orders']) == 'yes') {
                        // Order validate
                        if (trim($item['last_order_from']) !== '' ||
                            trim($item['last_order_to']) !== '' ||
                            trim($item['max_total_order']) !== '' ||
                            trim($item['min_total_order']) !== ''
                        ) {
                            $dq = new DbQuery();
                            $dq
                                ->select('o.id_order, (o.total_paid_tax_incl/o.conversion_rate) `total_paid_tax_incl`, o.date_add')
                                ->from('orders', 'o')
                                ->where('o.id_customer=' . (int)$customer->id)
                                ->orderBy('o.date_add DESC');
                            $last_order = Db::getInstance()->getRow($dq);
                            if (trim($item['last_order_from']) !== '' && (!isset($last_order['date_add']) || !$last_order['date_add'] || strtotime($last_order['date_add']) < strtotime($item['last_order_from'])) ||
                                trim($item['last_order_to']) !== '' && (!isset($last_order['date_add']) || !$last_order['date_add'] || strtotime($last_order['date_add']) > strtotime($item['last_order_to'])) ||
                                trim($item['max_total_order']) !== '' && (!isset($last_order['total_paid_tax_incl']) || !$last_order['total_paid_tax_incl'] || $last_order['total_paid_tax_incl'] > $item['max_total_order']) ||
                                trim($item['min_total_order']) !== '' && (!isset($last_order['total_paid_tax_incl']) || !$last_order['total_paid_tax_incl'] || $last_order['total_paid_tax_incl'] < $item['min_total_order'])
                            ) {
                                continue;
                            }
                        }
                        // Purchased product validate
                        if (trim($item['purchased_product']) !== '' || trim($item['not_purchased_product']) !== '') {
                            $purchased_product = trim($item['purchased_product']) !== '' ? explode(',', $item['purchased_product']) : '';
                            $not_purchased_product = trim($item['not_purchased_product']) !== '' ? explode(',', $item['not_purchased_product']) : '';

                            $dq = new DbQuery();
                            $dq
                                ->select('COUNT(od.product_id)')
                                ->from('orders', 'o')
                                ->leftJoin('order_detail', 'od', 'o.id_order=od.id_order')
                                ->where('o.id_customer=' . (int)$customer->id)
                                ->groupBy('od.product_id');
                            if (is_array($purchased_product) && Validate::isArrayWithIds($purchased_product)) {
                                $dq->where('od.product_id IN (' . implode(',', $purchased_product) . ')');
                            }
                            if (is_array($not_purchased_product) && Validate::isArrayWithIds($not_purchased_product)) {
                                $dq->where('od.product_id NOT IN (' . implode(',', $not_purchased_product) . ')');
                            }
                            if ((int)Db::getInstance()->getValue($dq) < 1) {
                                continue;
                            }
                        }
                    } else {
                        $dq = new DbQuery();
                        $dq
                            ->select('COUNT(o.id_customer)')
                            ->from('orders', 'o')
                            ->where('o.id_customer=' . (int)$customer->id);
                        if ((int)Db::getInstance()->getValue($dq) > 0) {
                            continue;
                        }
                    }
                }

                $query = 'INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_index_customer`(
                    `id_ets_abancart_index_customer`
                    , `id_customer`
                    , `id_ets_abancart_reminder`
                    , `id_ets_abancart_campaign`
                    , `id_shop`
                    , `firstname`
                    , `lastname`
                    , `email`
                    , `id_lang`
                    , `customer_date_add`
                    , `last_login_time`
                    , `newsletter_date_add`
                    , `last_date_order`
                    , `date_upd`
                    , `ip_address`
                ) VALUES (
                    NULL
                    , ' . (int)$customer->id . '
                    , ' . (int)$item['id_ets_abancart_reminder'] . '
                    , ' . (int)$item['id_ets_abancart_campaign'] . '
                    , ' . (int)$customer->id_shop . '
                    , \'' . pSQL($customer->firstname) . '\'
                    , \'' . pSQL($customer->lastname) . '\'
                    , \'' . pSQL($customer->email) . '\'
                    , ' . (int)$customer->id_lang . '
                    , ' . ($customer->date_add ? '\'' . pSQL($customer->date_add) . '\'' : 'NULL') . '
                    , ' . ($isAfterLogin ? '\'' . date('Y-m-d H:i:s') . '\'' : 'NULL') . '
                    , ' . ($isAfterSubscribeLetter ? '\'' . date('Y-m-d H:i:s') . '\'' : 'NULL') . '
                    , ' . ($isAfterOrder ? '\'' . date('Y-m-d H:i:s') . '\'' : 'NULL') . '
                    , ' . ($customer->date_upd ? '\'' . pSQL($customer->date_upd) . '\'' : 'NULL') . '
                    , \'' . pSQL($ip_address) . '\'
                )';
                $exec &= Db::getInstance()->execute($query);
            }
            return $exec;
        }
        return false;
    }

    public static function getCustomerIsRun($id_customer, $id_ets_abancart_reminder, $id_shop = 0)
    {
        if ($id_customer <= 0 || !Validate::isUnsignedInt($id_customer) || $id_ets_abancart_reminder <= 0 || !Validate::isUnsignedInt($id_ets_abancart_reminder))
            return false;

        return (int)Db::getInstance()->getValue('
            SELECT 
                (SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder . ($id_shop > 0 ? ' AND `id_shop` = ' . (int)$id_shop : '') . ') +
                (SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder . ($id_shop > 0 ? ' AND `id_shop` = ' . (int)$id_shop : '') . ') +
                (SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'ets_abancart_index_customer` WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder . ($id_shop > 0 ? ' AND `id_shop` = ' . (int)$id_shop : '') . ')
        ');
    }

    public static function reIndexLastLogin($id_customer, $id_ets_abancart_reminder, $id_shop = 0)
    {
        if ($id_customer <= 0 || !Validate::isUnsignedInt($id_customer) || $id_ets_abancart_reminder <= 0 || !Validate::isUnsignedInt($id_ets_abancart_reminder))
            return false;

        $res = (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_customer`) 
            FROM `' . _DB_PREFIX_ . 'ets_abancart_index_customer` 
            WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder . ($id_shop > 0 ? ' AND `id_shop` = ' . (int)$id_shop : '') . '
        ');
        if ($res)
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_index_customer` SET `last_login_time` = \'' . date('Y-m-d H:i:s') . '\' WHERE `id_customer` = ' . (int)$id_customer . ' AND `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder . ($id_shop > 0 ? ' AND `id_shop` = ' . (int)$id_shop : ''));
        return false;
    }

    public static function getTotalCustomerIndex($id_ets_abancart_reminder = 0)
    {
        if ($id_ets_abancart_reminder > 0 && !Validate::isUnsignedInt($id_ets_abancart_reminder)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('COUNT(*)')
            ->from('ets_abancart_index_customer');

        if ($id_ets_abancart_reminder > 0) {
            $query->where('id_ets_abancart_reminder = ' . (int)$id_ets_abancart_reminder);
        }

        return (int)Db::getInstance()->getValue($query);
    }
}