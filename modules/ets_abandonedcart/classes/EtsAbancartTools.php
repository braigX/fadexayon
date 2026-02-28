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


class EtsAbancartTools extends EtsAbancartCore
{
    public static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EtsAbancartTools();
        }
        return self::$instance;
    }

    public static function getContentQueue($id)
    {
        if (!$id || !Validate::isUnsignedInt($id)) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('a.content')
            ->from('ets_abancart_email_queue', 'a')
            ->where('id_ets_abancart_email_queue=' . (int)$id);
        return Db::getInstance()->getValue($dq);
    }

    public static function getQueue($id)
    {
        if (!$id || !Validate::isUnsignedInt($id)) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('*')
            ->from('ets_abancart_email_queue')
            ->where('id_ets_abancart_email_queue=' . (int)$id);
        return Db::getInstance()->getRow($dq);
    }

    public static function getLocale(Context $context)
    {
        if ($context && version_compare(_PS_VERSION_, '1.7.6.0', '>')) {
            $container = call_user_func_array('PrestaShop\PrestaShop\Adapter\ContainerBuilder::getContainer', array('front', _PS_MODE_DEV_));
            $localeRepo = $container->get('prestashop.core.localization.locale.repository');
            $context->currentLocale = $localeRepo->getLocale(
                $context->language->getLocale()
            );
        }
    }

    public static function getColor($num)
    {
        $hash = md5('color' . $num);
        $rgb = array(
            hexdec(Tools::substr($hash, 0, 2)),
            hexdec(Tools::substr($hash, 2, 2)),
            hexdec(Tools::substr($hash, 4, 2)));
        return 'rgba(' . implode(',', $rgb) . ', %s)';
    }

    public static function isArrayWithIds($ids)
    {
        if (count($ids)) {
            foreach ($ids as $id) {
                if ($id == 0 || !Validate::isInt($id)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function getImageType($name)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return ImageType::getFormattedName($name);
        }
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(array('_' . $theme_name, $theme_name . '_'), '', $name);

        //check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name)) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' . $theme_name)) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' . $name_without_theme_name)) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }

    public static function removeCartIndex($index, $dispatchJob = true)
    {
        if ($dispatchJob) {
            $reminder = EtsAbancartReminder::nextRun((int)$index['id_ets_abancart_campaign'], (int)$index['id_ets_abancart_reminder'], (int)$index['priority']);
            if (!empty($reminder)) {
                return EtsAbancartIndex::nextIndexRun($index, $reminder);
            }
        }
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index` WHERE id_cart=' . (int)$index['id_cart'] . ' AND id_ets_abancart_reminder=' . (int)$index['id_ets_abancart_reminder'] . ' AND id_ets_abancart_campaign=' . (int)$index['id_ets_abancart_campaign']);
    }

    public static function removeCustomerIndex($index)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index_customer` WHERE id_customer=' . (int)$index['id_customer'] . ' AND id_ets_abancart_reminder=' . (int)$index['id_ets_abancart_reminder'] . ' AND id_ets_abancart_campaign=' . (int)$index['id_ets_abancart_campaign']);
    }

    public function runCronjob($secure, $id_shop = null, $manual = null)
    {
        Configuration::updateGlobalValue('ETS_ABANCART_LAST_CRONJOB', date('Y-m-d H:i:s'), true);
        /**
         * @param $module Ets_abandonedcart
         */
        $module = Module::getInstanceByName('ets_abandonedcart');

        if (!($token = trim($secure)) || !Validate::isCleanHtml($token) || $token != Configuration::getGlobalValue('ETS_ABANCART_SECURE_TOKEN')) {
            if (Tools::isSubmit('ajax')) {
                die(json_encode(array(
                    'errors' => $this->l('Access denied'),
                    'result' => ''
                )));
            }
            die($this->l('Access denied'));
        }

        $totalDiscountDeleted = 0;
        if ((int)Configuration::get('ETS_ABANCART_AUTO_CLEAR_DISCOUNT')) {
            $totalDiscountDeleted = EtsAbancartIndex::clearDiscountIsExpired();
        }

        $context = Context::getContext();
        $keeps = [
            'currency' => $context->currency,
            'shop' => $context->shop,
            'cart' => $context->cart,
            'customer' => $context->customer,
        ];

        $context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        EtsAbancartTools::getLocale($context);
        $ip_address = Tools::getRemoteAddr();
        if ($ip_address == '::1') $ip_address = '127.0.0.1';

        /*-------------------------------------EMAIL-INDEX-------------------------------------*/
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_index` WHERE `id_cart`=0 OR `id_ets_abancart_reminder`=0 OR `email` is NULL');
        $sql = '
            SELECT (86400 * IFNULL(ar.`day`, 0) + 3600*IFNULL(ar.`hour`, 0)) `lifetime`
                , TIMESTAMPDIFF(SECOND , IFNULL(ai.`last_campaign_run`, ai.`cart_date_add`), "' . pSQL(date('Y-m-d H:i:s')) . '") `overtime`
                , rl.*
                , ai.*
                , ar.*
            FROM `' . _DB_PREFIX_ . 'ets_abancart_index` ai
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON (ar.`id_ets_abancart_reminder` = ai.`id_ets_abancart_reminder`)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder_lang` rl ON (rl.`id_ets_abancart_reminder` = ai.`id_ets_abancart_reminder` AND rl.`id_lang` = ai.`id_cart_lang`)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` au ON (ai.`id_customer` = au.`id_customer`)
            WHERE au.`id_customer` is NULL AND ar.`id_ets_abancart_reminder` is NOT NULL' . ($id_shop ? ' AND ai.`id_shop`=' . (int)$id_shop : '') . '
            HAVING `overtime` >= `lifetime`
        ';

        if ($jobs = Db::getInstance()->executeS($sql)) {
            foreach ($jobs as $job) {
                if (!isset($job['id_cart']) ||
                    (int)$job['id_cart'] < 0 ||
                    !isset($job['id_customer']) ||
                    (int)$job['id_customer'] < 0 ||
                    !$job['id_shop'] ||
                    (int)$job['id_shop'] < 0
                ) {
                    self::removeCartIndex($job, false);
                    continue;
                }
                try {
                    Db::getInstance()->execute('START TRANSACTION');

                    $exists = Db::getInstance()->getValue('
                        SELECT COUNT(*)
                        FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking`
                        WHERE `id_cart` = ' . (int)$job['id_cart'] . ' AND `id_ets_abancart_reminder` = ' . (int)$job['id_ets_abancart_reminder']
                    );

                    if ($exists) {
                        Db::getInstance()->execute('COMMIT');
                        continue;
                    }

                    $context->cart = new Cart((int)$job['id_cart']);
                    $has_applied_voucher = ($cart_rules = $context->cart->getCartRules()) && is_array($cart_rules) && count($cart_rules) > 0 ? 1 : 0;
                    if (isset($job['has_applied_voucher']) && (trim($job['has_applied_voucher']) === 'yes' && $has_applied_voucher === 0 || trim($job['has_applied_voucher']) === 'no' && $has_applied_voucher > 0)) {
                        self::removeCartIndex($job, false);
                        continue;
                    }
                    $context->shop = new Shop((int)$job['id_shop']);
                    $context->customer = new Customer((int)$job['id_customer']);
                    $context->language = new Language(isset($job['id_cart_lang']) ? (int)$job['id_cart_lang'] : (int)Configuration::get('PS_LANG_DEFAULT', null, null, $context->shop->id));
                    if ($context->cart->id_currency !== $context->currency->id && ($currency = new Currency($context->cart->id_currency)) && $currency->id > 0) {
                        $context->currency = $currency;
                        EtsAbancartTools::getLocale($context);
                    }
                    if (isset($job['ip_address']) && $job['ip_address'] !== '')
                        $ip_address = trim($job['ip_address']);
                    $added = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_tracking`(
                        `id_ets_abancart_tracking`
                        , `id_cart`
                        , `id_customer`
                        , `email`
                        , `ip_address`
                        , `id_ets_abancart_reminder`
                        , `id_ets_abancart_campaign`
                        , `id_lang`
                        , `id_shop`
                        , `display_times`
                        , `total_execute_times`
                        , `delivered`
                        , `read`
                        , `deleted`
                        , `date_add`
                        , `date_upd`
                        , `last_campaign_run`
                        , `priority`
                    ) VALUES(
                        NULL
                        , ' . (int)$job['id_cart'] . '
                        , NULL
                        , \'' . pSQL($job['email']) . '\'
                        , \'' . pSQL($ip_address) . '\'
                        , ' . (int)$job['id_ets_abancart_reminder'] . '
                        , ' . (int)$job['id_ets_abancart_campaign'] . '
                        , ' . (int)$context->language->id . '
                        , ' . (int)$job['id_shop'] . '
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , 0
                        , 0
                        , 0
                        , 0
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , ' . (int)$job['priority'] . '
                    )');
                    $id_ets_abancart_tracking = (int)Db::getInstance()->Insert_ID();
                    if (!$added) {
                        throw new Exception('Error when insert tracking');
                    }
                    // Cart rule
                    if ($job['discount_option'] == 'auto') {
                        $reminder = new EtsAbancartReminder((int)$job['id_ets_abancart_reminder']);
                        $cart_rule = $module->addCartRule($reminder, $context->customer->id);
                    } elseif ($job['discount_option'] != 'no') {
                        $id_cart_rule = isset($job['discount_code']) && $job['discount_code'] ? (int)CartRule::getIdByCode($job['discount_code']) : 0;
                        $cart_rule = new CartRule($id_cart_rule ?: null);
                    } else {
                        $cart_rule = new CartRule();
                    }
                    $content = $module->doShortCode($job['content'], 'email', $cart_rule, $context, (int)$job['id_ets_abancart_reminder'], [
                        'id_ets_abancart_campaign' => (int)$job['id_ets_abancart_campaign'],
                        'id_cart' => (int)$job['id_cart'],
                        'email' => $job['email']
                    ]);
                    $content = EtsAbancartEmailTemplate::createContentEmailToSend($content, (int)$job['id_ets_abancart_reminder'], $context->language->id);
                    $addedQueue = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_email_queue` VALUES(
                        NULL
                        , ' . (int)$context->shop->id . '
                        , ' . (int)$context->language->id . '
                        , ' . (int)$job['id_cart'] . '
                        , ' . (int)$job['id_customer'] . '
                        , ' . (int)$id_ets_abancart_tracking . '
                        , ' . (int)$job['id_ets_abancart_reminder'] . '
                        , ' . (int)$job['id_ets_abancart_campaign'] . '
                        , ' . (int)$job['lifetime'] . '
                        , \'' . pSQL($job['firstname'] . ' ' . $job['lastname']) . '\'
                        , \'' . pSQL($job['email']) . '\'
                        , \'' . pSQL($job['title']) . '\'
                        , \'' . pSQL($content, true) . '\'
                        , 0
                        , NULL
                        , 0
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                    )');

                    if (!$addedQueue) {
                        throw new Exception('Error when insert email queue');
                    }
                    if ($cart_rule->id > 0 && $id_ets_abancart_tracking > 0) {
                        $addedVoucher = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_discount`(`id_ets_abancart_tracking`, `id_cart_rule`, `code`, `use_same_cart`, `fixed_voucher`) VALUES (
                            ' . (int)$id_ets_abancart_tracking . '
                            , ' . (int)$cart_rule->id . '
                            , \'' . pSQL($cart_rule->code) . '\'
                            , ' . (int)$job['allow_multi_discount'] . '
                            , ' . ($job['discount_option'] == 'fixed' ? 1 : 0) . '
                        )');

                        if (!$addedVoucher) {
                            throw new Exception('Error when insert discount');
                        }
                    }
                    self::removeCartIndex($job);
                    Db::getInstance()->execute('COMMIT');
                } catch (Exception $e) {
                    Db::getInstance()->execute('ROLLBACK');
                    self::removeCartIndex($job, false);
                    $this->logError('Failed to process job with id_cart: ' . $job['id_cart'] . '. Error: ' . $e->getMessage());
                }
            }
        }

        /*-------------------------------------END EMAIL-INDEX--------------------------------*/

        /*-------------------------------------CUSTOMER-INDEX--------------------------------*/
        EtsAbancartIndexCustomer::addCustomerIndexScheduleTime();

        $sql = '
            SELECT ac.id_ets_abancart_campaign,ac.email_timing_option,
                   (86400 * IFNULL(ar.day, 0) + 3600*IFNULL(ar.hour, 0)) `lifetime`
                   , TIMESTAMPDIFF(SECOND, IF(ac.email_timing_option=' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN . ', ic.last_login_time, IF(ac.email_timing_option=' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SUBSCRIBE_LETTER . ', ic.newsletter_date_add, IF(ac.email_timing_option=' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_ORDER_COMPLETION . ', ic.last_date_order, ic.customer_date_add))), \'' . pSQL(date('Y-m-d H:i:s')) . '\') `overtime`
                   , ar.*
                   , arl.title
                   , arl.content
                   , arl.discount_name
                   , ic.*
            FROM `' . _DB_PREFIX_ . 'ets_abancart_index_customer` ic
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON (ic.id_ets_abancart_campaign  = ac.id_ets_abancart_campaign )
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` au ON (ic.id_customer = au.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON (ar.id_ets_abancart_campaign = ac.id_ets_abancart_campaign AND ar.id_ets_abancart_reminder = ic.id_ets_abancart_reminder)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder_lang` arl ON (arl.id_ets_abancart_reminder = ar.id_ets_abancart_reminder AND arl.id_lang = ic.id_lang)
            WHERE au.id_customer is NULL
                AND ar.id_ets_abancart_reminder is NOT NULL
                AND (ar.enabled=' . EtsAbancartReminder::REMINDER_STATUS_RUNNING . ' OR ar.enabled=' . EtsAbancartReminder::REMINDER_STATUS_FINISHED . ') 
                AND ar.deleted=0 
                AND ac.enabled=1 
                AND ac.deleted=0 
                ' . ($id_shop ? ' AND ic.id_shop=' . (int)$id_shop : '') . '
            GROUP BY ac.id_ets_abancart_campaign, ar.id_ets_abancart_reminder, ic.email
            HAVING IF(ac.email_timing_option IN(' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_REGISTRATION . ',' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_ORDER_COMPLETION . ',' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN . ',' . EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SUBSCRIBE_LETTER . '), overtime >= lifetime, 1)
        ';

        if ($cJobs = Db::getInstance()->executeS($sql)) {
            foreach ($cJobs as $job) {
                if (!isset($job['id_customer']) ||
                    (int)$job['id_customer'] < 0 ||
                    !$job['id_shop'] ||
                    (int)$job['id_shop'] < 0
                ) {
                    self::removeCustomerIndex($job);
                    continue;
                }
                try {

                    Db::getInstance()->execute('START TRANSACTION');

                    $exists = Db::getInstance()->getValue('
                        SELECT COUNT(*)
                        FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking`
                        WHERE `id_customer` = ' . (int)$job['id_customer'] . ' 
                            AND `id_ets_abancart_reminder` = ' . (int)$job['id_ets_abancart_reminder']
                    );

                    if ($exists) {
                        Db::getInstance()->execute('COMMIT');
                        continue;
                    }

                    $context->shop = new Shop((int)$job['id_shop']);
                    $context->customer = new Customer((int)$job['id_customer']);
                    if ($context->customer->id <= 0) {
                        $context->customer->id = 0;
                        $context->customer->id_lang = (int)$job['id_lang'];
                        $context->customer->id_shop = (int)$job['id_shop'];
                        $context->customer->email = $job['email'];
                    }
                    $context->language = new Language(isset($job['id_lang']) ? (int)$job['id_lang'] : (int)Configuration::get('PS_LANG_DEFAULT', null, null, $context->shop->id));
                    if (isset($context->cart->id_currency) && $context->cart->id_currency !== $context->currency->id && ($currency = new Currency($context->cart->id_currency)) && $currency->id > 0) {
                        $context->currency = $currency;
                        EtsAbancartTools::getLocale($context);
                    }
                    if (isset($job['ip_address']) && $job['ip_address'] !== '')
                        $ip_address = trim($job['ip_address']);
                    $addCustomer = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_tracking`(id_ets_abancart_tracking, id_cart, id_customer, email, ip_address, id_ets_abancart_reminder, id_ets_abancart_campaign, id_lang, id_shop, display_times, total_execute_times, delivered, `read`, `deleted`, date_add, date_upd) VALUES (
                        NULL
                        , NULL
                        , ' . ((int)$job['id_customer'] > 0 ? (int)$job['id_customer'] : 'NULL') . '
                        , \'' . pSQL($job['email']) . '\'
                        , \'' . pSQL($ip_address) . '\'
                        , ' . (int)$job['id_ets_abancart_reminder'] . '
                        , ' . (int)$job['id_ets_abancart_campaign'] . '
                        , ' . (int)$context->language->id . '
                        , ' . (int)$job['id_shop'] . '
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , 0
                        , 0
                        , 0
                        , 0
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                        , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                    )');
                    if (!$addCustomer) {
                        throw new Exception('Error when insert tracking');
                    }

                    $id_ets_abancart_tracking_customer = (int)Db::getInstance()->Insert_ID();
                    // Check queue with cart abandoned old time
                    $id_ets_abancart_email_queue_customer_old = 0;
                    $id_ets_abancart_tracking_customer_old = 0;
                    $qu2 = Db::getInstance()->getRow('
                        SELECT * FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` 
                        WHERE `id_ets_abancart_campaign`=' . (int)$job['id_ets_abancart_campaign'] . ' 
                            AND `id_cart` is NULL
                            AND `email` = \'' . pSQL($job['email']) . '\'
                            AND `id_customer`=' . (int)$job['id_customer']
                    );
                    if ($qu2 && (int)$qu2['time_run'] < (int)$job['lifetime']) {
                        $id_ets_abancart_email_queue_customer_old = (int)$qu2['id_ets_abancart_email_queue'];
                        $id_ets_abancart_tracking_customer_old = (int)$qu2['id_ets_abancart_tracking'];
                    }
                    // Add cart rule
                    $tpl_vars = [
                        'customer' => $context->customer,
                        'campaign_type' => 'customer',
                        'content' => $job['content']
                    ];
                    if ($job['discount_option'] == 'auto') {
                        $reminder = new EtsAbancartReminder((int)$job['id_ets_abancart_reminder']);
                        $tpl_vars['cart_rule'] = $module->addCartRule($reminder, (int)$job['id_customer']);
                    } elseif ($job['discount_option'] != 'no') {
                        $tpl_vars['cart_rule'] = new CartRule(!empty($job['discount_code']) ? (int)CartRule::getIdByCode($job['discount_code']) : null);
                    } else
                        $tpl_vars['cart_rule'] = new CartRule();

                    if (!isset($context->cart)) {
                        $context->cart = new Cart();
                    }
                    $content = $module->doShortCode($tpl_vars['content'], 'customer', $tpl_vars['cart_rule'], $context, (int)$job['id_ets_abancart_reminder'], [
                        'id_ets_abancart_campaign' => (int)$job['id_ets_abancart_campaign'],
                        'id_customer' => (int)$job['id_customer'],
                        'email' => $job['email']
                    ]);
                    $content = EtsAbancartEmailTemplate::createContentEmailToSend($content, (int)$job['id_ets_abancart_reminder'], $context->language->id);

                    $addQueue = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_email_queue` VALUES(
                        ' . ($id_ets_abancart_email_queue_customer_old > 0 ? (int)$id_ets_abancart_email_queue_customer_old : 'NULL') . '
                        , ' . (int)$context->shop->id . '
                        , ' . (int)$context->language->id . '
                        , NULL
                        , ' . ((int)$job['id_customer'] > 0 ? (int)$job['id_customer'] : 'NULL') . '
                        , ' . (int)$id_ets_abancart_tracking_customer . '
                        , ' . (int)$job['id_ets_abancart_reminder'] . '
                        , ' . (int)$job['id_ets_abancart_campaign'] . '
                        , ' . (int)$job['lifetime'] . '
                        , \'' . pSQL($job['firstname'] . ' ' . $job['lastname']) . '\'
                        , \'' . pSQL($job['email']) . '\'
                        , \'' . pSQL($job['title']) . '\'
                        , \'' . pSQL($content, true) . '\'
                        , 0
                        , NULL
                        , 0
                        , "' . pSQL(date('Y-m-d H:i:s')) . '"
                    )' .
                        ($id_ets_abancart_email_queue_customer_old > 0 ? ' ON DUPLICATE KEY UPDATE 
                        `id_ets_abancart_tracking`=' . (int)$id_ets_abancart_tracking_customer . ', 
                        `id_ets_abancart_reminder`=' . (int)$job['id_ets_abancart_reminder'] . ', 
                        `subject`=\'' . pSQL($job['title']) . '\', 
                        `content`=\'' . pSQL($content, true) . '\',
                        `sent`=0,
                        `send_count`=0,
                        `date_add`=\'' . pSQL(date('Y-m-d H:i:s')) . '\',
                        `time_run`=' . (int)$job['lifetime']
                            : '')
                    );

                    if (!$addQueue) {
                        throw new Exception('Error when insert email queue');
                    }

                    if ($id_ets_abancart_tracking_customer_old > 0 && (bool)Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` SET `deleted`=1 WHERE `id_ets_abancart_tracking`=' . (int)$id_ets_abancart_tracking_customer_old)) {
                        $id_cart_rule = (int)Db::getInstance()->getValue('SELECT `id_cart_rule` FROM  `' . _DB_PREFIX_ . 'ets_abancart_discount` WHERE `id_ets_abancart_tracking`=' . (int)$id_ets_abancart_tracking_customer_old . ' AND `fixed_voucher`=0');
                        if ($id_cart_rule > 0) {
                            $cartRule = new CartRule((int)$id_cart_rule);
                            if ($cartRule->id)
                                $cartRule->delete();
                        }
                    }

                    if ((int)$tpl_vars['cart_rule']->id > 0 && $id_ets_abancart_tracking_customer > 0) {
                        $addVoucher = Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_discount`(`id_ets_abancart_tracking`, `id_cart_rule`, `code`, `use_same_cart`, `fixed_voucher`) VALUES (
                            ' . (int)$id_ets_abancart_tracking_customer . '
                            , ' . (int)$tpl_vars['cart_rule']->id . '
                            , \'' . pSQL($tpl_vars['cart_rule']->code) . '\'
                            , ' . (int)$job['allow_multi_discount'] . '
                            , ' . ($job['discount_option'] == 'fixed' ? 1 : 0) . '
                        )');

                        if (!$addVoucher) {
                            throw new Exception('Error when insert discount');
                        }
                    }
                    self::removeCustomerIndex($job);
                    Db::getInstance()->execute('COMMIT');
                } catch (Exception $e) {
                    Db::getInstance()->execute('ROLLBACK');
                    self::removeCustomerIndex($job);
                    $this->logError('Failed to process job with id_customer: ' . $job['id_customer'] . '. Error: ' . $e->getMessage());
                }
            }
        }


        /*-------------------------------------END CUSTOMER-INDEX--------------------------------*/


        /*------------------------------------PUSH TO QUEUE LAST VISITED--------------------------*/

        /*------------------------------------END PUSH TO QUEUE LAST VISITED--------------------------*/

        /*-------------------------------------SEND MAIL-----------------------------------*/
        $count = 0;
        $max_try = ($max = (int)Configuration::getGlobalValue('ETS_ABANCART_CRONJOB_MAX_TRY')) && $max > 0 && Validate::isUnsignedInt($max) ? $max : 5;
        $max_emails = ($limit = (int)Configuration::getGlobalValue('ETS_ABANCART_CRONJOB_EMAILS')) && $limit > 0 && Validate::isUnsignedInt($limit) ? $limit : 5;

        if ($queues = Db::getInstance()->executeS('
            SELECT * FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` 
            WHERE (sent = 0 AND send_count < ' . (int)$max_try . ') OR sending_time is NULL OR TIMESTAMPDIFF(SECOND, sending_time, \'' . pSQL(date('Y-m-d H:i:s')) . '\') > 60
            LIMIT ' . (int)$max_emails
        )) {
            foreach ($queues as $queue) {

                $isDelivered = Db::getInstance()->getValue('
                    SELECT COUNT(*)
                    FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking`
                    WHERE 
                        (`id_cart`' . ((int)$queue['id_cart'] > 0 ? '=' . (int)$queue['id_cart'] : ' IS NULL') . ')
                        AND (`id_customer`' . ((int)$queue['id_customer'] > 0 ? '=' . (int)$queue['id_customer'] : ' IS NULL') . ')
                        AND `email` = "' . pSQL(trim($queue['email'])) . '"
                        AND `id_ets_abancart_reminder` = ' . (int)$queue['id_ets_abancart_reminder'] . '
                        AND `id_ets_abancart_campaign` = ' . (int)$queue['id_ets_abancart_campaign'] . '
                        AND `delivered` = 1
                ');

                if ($isDelivered) {
                    continue;
                }
                if (isset($queue['id_cart']) && $queue['id_cart'] > 0 && EtsAbancartTools::cleanQueueOrdered((int)$queue['id_cart'])) {
                    continue;
                }
                if (Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` SET `sent` = 1, `sending_time` = \'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `id_ets_abancart_email_queue` = ' . (int)$queue['id_ets_abancart_email_queue'])) {
                    $url_params = array(
                        'id_ets_abancart_reminder' => $queue['id_ets_abancart_reminder'],
                        'id_ets_abancart_campaign' => $queue['id_ets_abancart_campaign'],
                        'email' => trim($queue['email']),
                        'mtime' => microtime(),
                    );
                    if ((int)$queue['id_cart'] > 0)
                        $url_params['id_cart'] = (int)$queue['id_cart'];
                    elseif ((int)$queue['id_customer'] > 0)
                        $url_params['id_customer'] = (int)$queue['id_customer'];

                    if (!@glob($module->getLocalPath() . 'mails/' . Language::getIsoById((int)$queue['id_lang']) . '/abandoned_cart*[.txt|.html]'))
                        $module->_installMail(new Language((int)$queue['id_lang']));

                    $trackingURL = Context::getContext()->link->getModuleLink($module->name, 'image', ['rewrite' => self::getInstance()->encrypt(json_encode($url_params))], (int)Configuration::getGlobalValue('PS_SSL_ENABLED_EVERYWHERE'), (int)$queue['id_lang'], (int)$queue['id_shop']);

                    EtsAbancartTools::saveMailLog($queue, EtsAbancartMail::SEND_MAIL_TIMEOUT);
                    if (EtsAbancartMail::send(
                        (int)$queue['id_lang'],
                        'abandoned_cart',
                        $queue['subject'],
                        array(
                            '{tracking}' => $trackingURL,
                            '{content}' => $queue['content'],
                            '{url_params}' => json_encode($url_params),
                        ),
                        Tools::strtolower($queue['email']),
                        $queue['customer_name'], null, null, null, null,
                        $module->getLocalPath() . 'mails/', false,
                        (int)$queue['id_shop']
                    )) {
                        EtsAbancartTools::saveMailLog($queue, EtsAbancartMail::SEND_MAIL_DELIVERED);
                        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_ets_abancart_email_queue` = ' . (int)$queue['id_ets_abancart_email_queue']);
                        Db::getInstance()->execute('
                            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking`
                            SET 
                                `delivered` = 1,
                                `total_execute_times` = `total_execute_times` + 1,
                                `display_times` = \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                            WHERE `id_cart`' . ((int)$queue['id_cart'] > 0 ? '=' . (int)$queue['id_cart'] : ' is NULL') . ' AND `id_customer`' . ((int)$queue['id_cart'] < 1 && (int)$queue['id_customer'] > 0 ? '=' . (int)$queue['id_customer'] : ' is NULL') . ' AND `email`=\'' . pSQL(trim($queue['email'])) . '\' AND `id_ets_abancart_reminder`=' . (int)$queue['id_ets_abancart_reminder']
                        );
                        $count++;
                    } else {
                        EtsAbancartTools::saveMailLog($queue, EtsAbancartMail::SEND_MAIL_FAILED);
                        Db::getInstance()->execute('
                            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_email_queue` SET `sent` = 0, `send_count` = `send_count` + 1, `sending_time` = \'' . pSQL(date('Y-m-d H:i:s')) . '\' 
                            WHERE `id_ets_abancart_email_queue` = ' . (int)$queue['id_ets_abancart_email_queue']
                        );
                        Db::getInstance()->execute('
                            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` 
                            SET 
                                `total_execute_times` = `total_execute_times` + 1,
                                `display_times` = \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                            WHERE id_cart' . ((int)$queue['id_cart'] > 0 ? '=' . (int)$queue['id_cart'] : ' is NULL') . ' AND id_customer' . ((int)$queue['id_cart'] < 1 && (int)$queue['id_customer'] > 0 ? '=' . (int)$queue['id_customer'] : ' is NULL') . ' AND email=\'' . pSQL($queue['email']) . '\' AND id_ets_abancart_reminder = ' . (int)$queue['id_ets_abancart_reminder']
                        );
                    }
                }
            }
        }

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE send_count >= ' . (int)$max_try);

        /*------------------------------------END SEND MAIL-------------------------------*/

        if ((int)Configuration::getGlobalValue('ETS_ABANCART_SAVE_CRONJOB_LOG')) {
            $return = date($context->language->date_format_full);
            $msg = $this->l('There were %d emails sent successfully');
            if ($count > 0 && $count <= 1)
                $return .= '  ' . sprintf($msg, $count, '');
            elseif ($count > 1)
                $return .= '  ' . sprintf($msg, $count, 's');
            else
                $return .= '  ' . $this->l('No email has been sent');
            if ($totalDiscountDeleted) {
                $return .= " | " . sprintf($this->l('%s discount deleted'), $totalDiscountDeleted);
            } else {
                $return .= " | " . $this->l('No discount deleted');
            }
            $dest = _PS_ROOT_DIR_ . '/var/logs/';
            if (!@is_dir($dest))
                @mkdir($dest, 0755, true);

            EtsAbancartHelper::file_put_contents($dest . $module->name . '.cronjob.log', $return . "\r\n", null, [], FILE_APPEND);
        }

        foreach ($keeps as $key => $keep) {
            $context->$key = $keep;
        }
        $jsonArr = array(
            'result' => $this->l('Cronjob ran successfully') . ' ' . ($count <= 0 ? $totalDiscountDeleted ? '. ' . sprintf($this->l('%s discount deleted'), $totalDiscountDeleted) : '. ' . $this->l('Nothing to do!') : sprintf($this->l('%s email(s) was sent!'), $count)),
        );
        if ($id_shop && isset($return)) {
            $jsonArr['log'] = $return;
        }
        if ($manual) {
            $jsonArr['status'] = $module->hookDisplayCronjobInfo();
        }
        die(json_encode($jsonArr));
    }

    private function logError($errorMessage)
    {
        $dest = _PS_ROOT_DIR_ . '/var/logs/';
        if (!@is_dir($dest)) {
            @mkdir($dest, 0755, true);
        }
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$errorMessage}\r\n";

        $logFilePath = $dest . 'ets_abandonedcart.error.log';
        if (!EtsAbancartHelper::file_put_contents($logFilePath, $logEntry, null, [], FILE_APPEND)) {
            error_log("Failed to write to log file: {$logFilePath}");
        }
    }


    public static function saveMailLog($queue, $status)
    {
        if ((int)Configuration::getGlobalValue('ETS_ABANCART_CRONJOB_MAIL_LOG') < 1)
            return true;
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_mail_log`(
                `id_ets_abancart_email_queue`
                , `id_shop`
                , `id_lang`
                , `id_cart`
                , `id_customer`
                , `id_ets_abancart_reminder`
                , `customer_name`
                , `email`
                , `subject`
                , `content`
                , `sent_time`
                , `status`
            ) VALUES (
                ' . (int)$queue['id_ets_abancart_email_queue'] . '
                , ' . (int)$queue['id_shop'] . '
                , ' . (int)$queue['id_lang'] . '
                , ' . (int)$queue['id_cart'] . '
                , ' . (int)$queue['id_customer'] . '
                , ' . (int)$queue['id_ets_abancart_reminder'] . '
                , \'' . pSQL($queue['customer_name']) . '\'
                , \'' . pSQL($queue['email']) . '\'
                , \'' . pSQL($queue['subject']) . '\'
                , \'' . pSQL($queue['content'], true) . '\'
                , \'' . pSQL(date('Y-m-d H:i:s')) . '\'
                , ' . (int)$status . '
            ) ON DUPLICATE KEY UPDATE `sent_time`=\'' . pSQL(date('Y-m-d H:i:s')) . '\', `status` = ' . (int)$status . '
        ';
        return Db::getInstance()->execute($query);
    }

    public static function cleanQueueOrdered($id_cart)
    {
        $res = false;
        if ($id_cart > 0 && (int)Db::getInstance()->getValue('SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` WHERE id_cart=' . (int)$id_cart) > 0) {
            $res = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_cart` = ' . (int)$id_cart);
            $res &= Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` SET `deleted`=1 WHERE `total_execute_times`=0 AND `id_cart` = ' . (int)$id_cart);
            $cart_rules = Db::getInstance()->executeS('
                    SELECT cr. `id_cart_rule`
                    FROM  `' . _DB_PREFIX_ . 'ets_abancart_tracking` t 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_discount` d ON (d.`id_ets_abancart_tracking` = t.`id_ets_abancart_tracking`)
                    INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (cr.`id_cart_rule` = d.`id_cart_rule`)
                    WHERE `deleted`=1 AND `total_execute_times`=0 AND d.`fixed_voucher`=0 AND t.`id_cart`=' . (int)$id_cart
            );
            if ($cart_rules) {
                foreach ($cart_rules as $cart_rule) {
                    $cartRule = new CartRule((int)$cart_rule['id_cart_rule']);
                    if ($cartRule->id) {
                        $cartRule->delete();
                    }
                }
            }
            $res &= Db::getInstance()->execute('
                    DELETE d
                    FROM  `' . _DB_PREFIX_ . 'ets_abancart_tracking` t 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_discount` d ON (d.`id_ets_abancart_tracking` = t.`id_ets_abancart_tracking`)
                    WHERE `deleted`=1 AND `total_execute_times`=0 AND d.`fixed_voucher`=0 AND t.`id_cart`=' . (int)$id_cart
            );
        }

        return $res;
    }

    public static function getLastOrderCustomer($id_customer)
    {
        return Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "orders` WHERE id_customer=" . (int)$id_customer . " ORDER BY id_order DESC");
    }

    public static function getTotalOrder($id_customer)
    {
        $query = new DbQuery();
        $query->select('SUM(total_paid_tax_incl / conversion_rate) as total_order')
            ->from('orders')
            ->where('id_customer = ' . (int)$id_customer);

        return (float)Db::getInstance()->getValue($query);
    }

    public static function getLastLoginTime($id_customer)
    {
        return Db::getInstance()->getValue("
            SELECT cn.date_add FROM `" . _DB_PREFIX_ . "connections` cn 
            LEFT JOIN `" . _DB_PREFIX_ . "guest` g ON g.id_guest=cn.id_guest
            WHERE g.id_customer=" . (int)$id_customer . " ORDER BY cn.date_add DESC");
    }

    public static function getTotalCustomerInTime($datetime, $groups)
    {
        return (int)Db::getInstance()->getValue("
                    SELECT COUNT(*) as total_customer FROM `" . _DB_PREFIX_ . "customer` c 
                    LEFT JOIN`" . _DB_PREFIX_ . "group`c ON c.id_customer=g.id_customer
                    WHERE data_add<='" . date('Y-m-d H:i:s', strtotime($datetime)) . "' AND g.id_group IN (" . implode(',', $groups) . ") 
                    GROUP BY c.id_customer");
    }

    public static function createMailUploadFolder()
    {
        $mailDir = _ETS_AC_MAIL_UPLOAD_DIR_;
        if (!is_dir(_ETS_AC_IMG_DIR_)) {
            @mkdir(_ETS_AC_IMG_DIR_, 0755);
            @copy(dirname(__FILE__) . '/index.php', _ETS_AC_IMG_DIR_ . '/index.php');
        }
        if (!is_dir($mailDir)) {
            @mkdir($mailDir, 0755);
            @copy(dirname(__FILE__) . '/index.php', $mailDir . '/index.php');
        }
        return is_dir($mailDir) ? $mailDir : null;
    }

    public static function copyFolder($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyFolder($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function deleteAllDataInFolder($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file))
                self::deleteAllDataInFolder($file);
            else
                EtsAbancartHelper::unlink($file);
        }
        if (is_dir($dir))
            rmdir($dir);
    }

    public static function request($type, $uri, $params = array(), $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        if ($params && Tools::strtoupper($type) === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($params)));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36');
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function tableExist($table)
    {
        return Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . bqSQL($table) . '\'');
    }

    public static function getCartRuleByPromotion($discount_code)
    {
        if (EtsAbancartTools::tableExist('ets_pr_rule')) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_pr_rule` r
            INNER JOIN `' . _DB_PREFIX_ . 'ets_pr_action_rule` ar ON (r.id_ets_pr_rule = ar.id_ets_pr_rule)
            WHERE r.active=1 AND ar.code="' . pSQL($discount_code) . '"';
            return Db::getInstance()->getRow($sql);
        }

        return false;
    }

    public static function getOtherVoucherInCart($id_cart, $id_cart_rule)
    {
        if (!$id_cart || !Validate::isUnsignedInt($id_cart) || !$id_cart_rule || !Validate::isUnsignedInt($id_cart_rule)) {
            return 0;
        }
        $cache_id = 'EtsAbancartTools::getOtherVoucherInCart' . md5($id_cart . '|' . $id_cart_rule);
        if (!Cache::isStored($cache_id)) {
            $result = (int)Db::getInstance()->getValue("SELECT ccr.`id_cart_rule` FROM `" . _DB_PREFIX_ . "cart_cart_rule` ccr WHERE `id_cart`=" . (int)$id_cart . " AND `id_cart_rule` !=" . (int)$id_cart_rule);
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }
        return $result;
    }

    public static function canUseCartRule($id_cart, $id_cart_rule, &$voucherCode, $id_customer, &$voucherValidity = true)
    {
        $vouchers = [];
        if (!EtsAbancartTracking::isVoucher($id_cart_rule)) {
            $vouchers = EtsAbancartTracking::getCartRulesNotIsSameCart($id_cart);
        } elseif (EtsAbancartTools::getOtherVoucherInCart($id_cart, $id_cart_rule) > 0) {
            $voucher_is_same_cart = EtsAbancartTracking::getVoucherIsSameCart($id_cart_rule);
            if (isset($voucher_is_same_cart['use_same_cart']) && (int)$voucher_is_same_cart['use_same_cart'] > 0) {
                $vouchers = EtsAbancartTracking::getCartRulesNotIsSameCart($id_cart);
            } elseif ($voucher_is_same_cart) {
                $vouchers[] = $voucher_is_same_cart;
            }
        }
        if (empty($vouchers)) {
            if (!EtsAbancartDisplayTracking::isVoucher($id_cart_rule)) {
                $vouchers = EtsAbancartDisplayTracking::getCartRulesNotIsSameCart($id_cart);
            } elseif (EtsAbancartTools::getOtherVoucherInCart($id_cart, $id_cart_rule) > 0) {
                $voucher_is_same_cart = EtsAbancartDisplayTracking::getVoucherIsSameCart($id_cart_rule);
                if (isset($voucher_is_same_cart['use_same_cart']) && (int)$voucher_is_same_cart['use_same_cart'] > 0) {
                    $vouchers = EtsAbancartDisplayTracking::getCartRulesNotIsSameCart($id_cart);
                } elseif ($voucher_is_same_cart) {
                    $vouchers[] = $voucher_is_same_cart;
                }
            }
        }
        if (!empty($vouchers)) {
            $codes = [];
            foreach ($vouchers as $voucher) {
                $codes[] = $voucher['code'];
            }
            $voucherCode = implode(',', $codes);
            return false;
        }
        return true;
    }

    public static function getRandomIdProduct($limit = 1, $context = null)
    {
        if ($context === null) {
            $context = Context::getContext();
        }

        $query = new DbQuery();
        $query->select('id_product')
            ->from('product_shop')
            ->where('id_shop = ' . (int)$context->shop->id)
            ->orderBy('RAND()')
            ->limit((int)$limit);

        $results = Db::getInstance()->executeS($query);

        $ids = [];
        foreach ($results as $item) {
            $ids[] = (int)$item['id_product'];
        }

        return $ids;
    }

    public static function createImgDir()
    {
        if (!is_dir(_PS_IMG_DIR_ . 'ets_abandonedcart')) {
            @mkdir(_PS_IMG_DIR_ . 'ets_abandonedcart', 0777);
        }
        if (!is_dir(_PS_IMG_DIR_ . 'ets_abandonedcart/img')) {
            @mkdir(_PS_IMG_DIR_ . 'ets_abandonedcart/img', 0777);
        }
        return _PS_IMG_DIR_ . 'ets_abandonedcart/img';
    }

    public static function getNbSentMailQueue($id_queue)
    {
        return (int)Db::getInstance()->getValue('SELECT send_count FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_ets_abancart_email_queue` = ' . (int)$id_queue);
    }

    public static function getTotalOrderByIdShop($id_shop, $filter = null)
    {
        $ETS_ABANCART_CONVERSION_PERCENTAGE = trim(Configuration::get('ETS_ABANCART_CONVERSION_PERCENTAGE'));
        return (float)Db::getInstance()->getValue('
            SELECT SUM(o.total_paid_tax_incl) 
            FROM (
                SELECT a.`total_paid_tax_incl`/a.`conversion_rate` `total_paid_tax_incl`
                FROM `' . _DB_PREFIX_ . 'orders` a 
                INNER JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.id_order_state = a.current_state)
                INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_tracking` t ON (t.id_cart = a.id_cart)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON (ar.id_ets_abancart_reminder = t.id_ets_abancart_reminder)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON (ac.id_ets_abancart_campaign = ar.id_ets_abancart_campaign)
                WHERE a.id_shop=' . (int)$id_shop . ' 
                    AND t.id_cart > 0
                    AND os.paid = 1
                    AND t.delivered=1' . ($ETS_ABANCART_CONVERSION_PERCENTAGE == 'opened' ? ' AND t.`read`=1' : ($ETS_ABANCART_CONVERSION_PERCENTAGE == 'click_through' ? ' AND t.`read`=1 AND t.`nb_click`>0' : '')) . '
                    AND (t.id_ets_abancart_reminder > 0 AND ac.campaign_type=\'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_EMAIL) . '\' OR t.id_ets_abancart_reminder = -1)
                    ' . ($filter !== null ? ' AND ' . $filter : '') . '
                GROUP BY a.id_order
            ) as o
        ');
    }

    public static function getNbOrderByIdShop($id_shop, $filter = null)
    {
        $ETS_ABANCART_CONVERSION_PERCENTAGE = trim(Configuration::get('ETS_ABANCART_CONVERSION_PERCENTAGE'));
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(DISTINCT a.id_order) 
            FROM `' . _DB_PREFIX_ . 'orders` a 
            INNER JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.id_order_state = a.current_state)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_tracking` t ON (t.id_cart = a.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON (ar.id_ets_abancart_reminder = t.id_ets_abancart_reminder)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON (ac.id_ets_abancart_campaign = ar.id_ets_abancart_campaign)
            WHERE a.id_shop=' . (int)$id_shop . ' 
                AND t.id_cart > 0
                AND os.paid = 1
                AND t.delivered=1' . ($ETS_ABANCART_CONVERSION_PERCENTAGE == 'opened' ? ' AND t.`read`=1' : ($ETS_ABANCART_CONVERSION_PERCENTAGE == 'click_through' ? ' AND t.`read`=1 AND t.`nb_click`>0' : '')) . '
                AND (t.id_ets_abancart_reminder > 0 AND ac.campaign_type=\'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_EMAIL) . '\' OR t.id_ets_abancart_reminder = -1)
                ' . ($filter !== null ? ' AND ' . $filter : '') . '
        ');
    }

    public static function getMinYear()
    {
        $query = new DbQuery();
        $query->select('YEAR(o.date_add)')
            ->from('orders', 'o')
            ->orderBy('o.date_add ASC');

        return Db::getInstance()->getValue($query);
    }

    public static function getIdOrderByIdCart($id_cart)
    {
        $query = new DbQuery();
        $query->select('id_order')
            ->from('orders')
            ->where('id_cart = ' . (int)$id_cart . Shop::addSqlRestriction());

        return (int)Db::getInstance()->getValue($query);
    }

    public static function addCartRules2($ids, $id_cart_rule)
    {
        if (Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES(" . (int)$id_cart_rule . ", 1)")) {
            $idProductRuleGroup = Db::getInstance()->getValue("SELECT id_product_rule_group FROM `" . _DB_PREFIX_ . "cart_rule_product_rule_group` WHERE `id_cart_rule`=" . (int)$id_cart_rule);
            if ($idProductRuleGroup && Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES(" . (int)$idProductRuleGroup . ", 'products')")) {
                $idProductRule = Db::getInstance()->getValue("SELECT id_product_rule FROM `" . _DB_PREFIX_ . "cart_rule_product_rule` WHERE `id_product_rule_group`=" . (int)$idProductRuleGroup . " AND `type`='products'");
                if ($idProductRule) {
                    foreach ($ids as $idProduct) {
                        if ((int)$idProduct)
                            Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "cart_rule_product_rule_value` (id_product_rule, id_item) VALUES(" . (int)$idProductRule . "," . (int)$idProduct . ")");
                    }
                }
            }
        }
    }

    public static function addCartRules($ids, $id_cart_rule)
    {
        if (!$ids || !is_array($ids) || !$id_cart_rule || !Validate::isUnsignedInt($id_cart_rule)) {
            return; // Return early if input parameters are invalid
        }

        // Insert into cart_rule_product_rule_group
        $data = array(
            'id_cart_rule' => (int)$id_cart_rule,
            'quantity' => 1
        );
        Db::getInstance()->insert('cart_rule_product_rule_group', $data);

        // Get the ID of the inserted rule group
        $idProductRuleGroup = Db::getInstance()->Insert_ID();

        if ($idProductRuleGroup > 0) {
            // Insert into cart_rule_product_rule
            $data = array(
                'id_product_rule_group' => (int)$idProductRuleGroup,
                'type' => 'products'
            );
            Db::getInstance()->insert('cart_rule_product_rule', $data);

            // Get the ID of the inserted product rule
            $idProductRule = Db::getInstance()->Insert_ID();

            if ($idProductRule > 0) {
                // Insert into cart_rule_product_rule_value for each idProduct
                foreach ($ids as $idProduct) {
                    if ((int)$idProduct > 0) {
                        $data = array(
                            'id_product_rule' => (int)$idProductRule,
                            'id_item' => (int)$idProduct
                        );
                        Db::getInstance()->insert('cart_rule_product_rule_value', $data);
                    }
                }
            }
        }
    }

    public static function getCombinationImages($id_product_attribute, $id_lang)
    {
        return Db::getInstance()->executeS('
				SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
				FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
				LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
				WHERE pai.`id_product_attribute` = ' . (int)$id_product_attribute . ' AND il.`id_lang` = ' . (int)$id_lang . ' ORDER by i.`position` LIMIT 1'
        );
    }

    public static function getMetaByRewrite($url_rewrite)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE url_rewrite ="' . pSQL($url_rewrite) . '"');
    }

    public static function getMetaByControllerModule($moduleName, $controller)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE page ="module-' . pSQL($moduleName) . '-' . pSQL($controller) . '"');
    }

    public static function getMetaIdByControllerModule($moduleName, $controller)
    {
        return (int)Db::getInstance()->getValue('SELECT id_meta FROM `' . _DB_PREFIX_ . 'meta` WHERE page ="module-' . pSQL($moduleName) . '-' . pSQL($controller) . '"');
    }

    public static function isPageCachedEnabled()
    {
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema =\'' . _DB_NAME_ . '\' AND table_name =\'`' . _DB_PREFIX_ . 'ets_pagecache_dynamic`\'');
    }

    public static function addModuleToPagecache($id)
    {
        return (int)Db::getInstance()->execute('
				INSERT INTO `' . _DB_PREFIX_ . 'ets_pagecache_dynamic`(`id_module`, `hook_name`, `empty_content`) 
				VALUES (\'' . (int)$id . '\', \'displayFooter\', 1) ON DUPLICATE KEY UPDATE `id_module` = ' . (int)$id . '
			');
    }

    public static function deleteModuleFromPagecache($id)
    {
        return (int)Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_pagecache_dynamic` WHERE `id_module` = ' . (int)$id);
    }

    public static function quickSort($list, $field = 'position')
    {
        $left = $right = array();
        if (count($list) <= 1 || trim($field) == '') {
            return $list;
        }
        $pivot_key = key($list);
        $pivot = array_shift($list);

        foreach ($list as $key => $val) {
            if ($val[$field] <= $pivot[$field]) {
                $left[$key] = $val;
            } elseif ($val[$field] > $pivot[$field]) {
                $right[$key] = $val;
            }
        }
        return array_merge(self::quickSort($left, $field), array($pivot_key => $pivot), self::quickSort($right, $field));
    }

    public static function formatDateStr($date_str, $full = false)
    {
        $time = strtotime($date_str);
        $context = Context::getContext();
        $date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);
        $date = date($date_format, $time);

        return $date;
    }

    public static function getCustomizationPrice($idCustomization)
    {
        if (!(int)$idCustomization) {
            return 0;
        }

        return (float)Db::getInstance()->getValue('SELECT SUM(`price`) FROM `' . _DB_PREFIX_ . 'customized_data` WHERE `id_customization` = ' . (int)$idCustomization);
    }

    public static function getCustomizationId($id_cart, $id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_customization` FROM `' . _DB_PREFIX_ . 'customization` WHERE `id_cart` = ' . (int)$id_cart . ' AND `id_product`=' . (int)$id_product);
    }

    public static function perms($id_tab, $id_profile, $perm, $enabled)
    {
        $perms = [];
        $perms['view'] = $perms['add'] = $perms['edit'] = $perms['delete'] = ($perm == 'all' ? $enabled : 0);
        if ($perm !== 'all') {
            $parent = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'access` WHERE `id_tab`=' . (int)$id_tab . ' AND `id_profile`=' . (int)$id_profile);
            if ($parent) {
                foreach ($parent as $key => $val) {
                    if (isset($perms[$key]))
                        $perms[$key] = $val;
                }
            }
            $perms[$perm] = $enabled;
        }
        $duplicateOn = [];
        if ($perms) {
            foreach ($perms as $field => $val)
                $duplicateOn[] = '`' . pSQL($field) . '`=' . pSQL($val);
        }
        $queries = [];
        $tabs = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'tab` WHERE `id_parent`=' . (int)$id_tab);
        if ($tabs)
            self::recursePerms($id_profile, $perms, $queries, implode(',', $duplicateOn), $tabs);
        if ($queries) {
            foreach ($queries as $query) {
                Db::getInstance()->execute($query);
            }
        }
    }

    public static function recursePerms($id_profile, $perms, &$queries, $duplicateOn, $tabs)
    {
        if ($tabs) {
            foreach ($tabs as $tab) {
                if ($tabs2 = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'tab` WHERE `id_parent`=' . (int)$tab['id_tab'])) {
                    self::recursePerms($id_profile, $perms, $queries, $duplicateOn, $tabs2);
                } else {
                    $queries[] = 'INSERT INTO `' . _DB_PREFIX_ . 'access`(`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES (' . (int)$id_profile . ', ' . (int)$tab['id_tab'] . ',' . (int)$perms['view'] . ',' . (int)$perms['add'] . ',' . (int)$perms['edit'] . ',' . (int)$perms['delete'] . ') ON DUPLICATE KEY UPDATE ' . $duplicateOn . ';' . PHP_EOL;
                }
            }
        }

        return $queries;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getServerVars($var)
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : '';
    }

    public static function getPostMaxSizeBytes()
    {
        $postMaxSizeList = array(@ini_get('post_max_size'), @ini_get('upload_max_filesize'), (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') . 'M');
        $ik = 0;
        foreach ($postMaxSizeList as &$max_size) {
            $bytes = (int)trim($max_size);
            $last = Tools::strtolower($max_size[Tools::strlen($max_size) - 1]);
            switch ($last) {
                case 'g':
                    $bytes *= 1024;
                case 'm':
                    $bytes *= 1024;
                case 'k':
                    $bytes *= 1024;
            }
            if ($bytes == '') {
                unset($postMaxSizeList[$ik]);
            } else
                $max_size = $bytes;
            $ik++;
        }

        return min($postMaxSizeList);
    }

    public static function displayText($content, $tag, $attr_datas = array())
    {
        $text = '<' . $tag . ' ';
        if ($attr_datas) {
            foreach ($attr_datas as $key => $value) {
                if ($value == null)
                    $text .= $key;
                else
                    $text .= $key . '="' . $value . '" ';
            }
        }
        if ($tag == 'img' || $tag == 'br' || $tag == 'path' || $tag == 'input') {
            $text .= ' /' . '>';
        } else {
            $text .= '>';
        }

        if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
            $text .= $content;
        if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
            $text .= '<' . '/' . $tag . '>';
        return $text;
    }

    public static function checkEnableOtherShop($id_module)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module`=' . (int)$id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
        return Db::getInstance()->executeS($sql);
    }

    public static function activeTab($module_name)
    {
        if (property_exists('Tab', 'enabled'))
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab` SET `enabled`=1 WHERE `module`=\'' . pSQL($module_name) . '\'');
    }
}