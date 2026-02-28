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


class EtsAbancartReminder extends ObjectModel
{
    public $id_ets_abancart_reminder;
    public $id_ets_abancart_campaign;
    public $date_add;
    public $date_upd;
    public $enabled;
    public $deleted;
    public $day;
    public $hour;
    public $minute;
    public $second;
    public $redisplay;
    public $delay_popup_based_on;
    public $free_shipping;
    public $discount_option;
    public $discount_code;
    public $apply_discount;
    public $discount_name;
    public $discount_prefix;
    public $reduction_percent;
    public $reduction_amount;
    public $id_currency;
    public $reduction_tax;
    public $apply_discount_in;
    public $apply_discount_to;
    public $enable_count_down_clock;
    public $highlight_discount;
    public $allow_multi_discount;

    public $quantity;
    public $quantity_per_user;
    public $reduction_product;
    public $selected_product;
    public $reduction_exclude_special;
    public $gift_product;
    public $gift_product_attribute;
    public $send_repeat_email;
    public $schedule_time;
    public $id_ets_abancart_email_template;
    public $title;
    public $content;
    public $text_color;
    public $background_color;
    public $icon_notify;
    public $header_bg;
    public $header_text_color;
    public $header_height;
    public $header_font_size;
    public $popup_width;
    public $border_radius;
    public $popup_body_bg;
    public $border_width;
    public $border_color;
    public $font_size;
    public $close_btn_color;
    public $padding;
    public $popup_height;
    public $vertical_align;
    public $overlay_bg;
    public $overlay_bg_opacity;

    public $priority = 0;

    const CUSTOMER_EMAIL_SEND_AFTER_REGISTRATION = 1;
    const CUSTOMER_EMAIL_SEND_AFTER_ORDER_COMPLETION = 2;
    const CUSTOMER_EMAIL_SEND_AFTER_SCHEDULE_TIME = 3;
    const CUSTOMER_EMAIL_SEND_RUN_NOW = 4;
    const CUSTOMER_EMAIL_SEND_AFTER_SUBSCRIBE_LETTER = 5;
    const CUSTOMER_EMAIL_SEND_LAST_TIME_LOGIN = 6;

    const REMINDER_STATUS_DRAFT = 0;
    const REMINDER_STATUS_RUNNING = 1;
    const REMINDER_STATUS_STOP = 2;
    const REMINDER_STATUS_FINISHED = 3;
    const DELAY_PAGE_LOAD = 0;
    const DELAY_CART_CREATION_TIME = 1;

    public static $definition = array(
        'table' => 'ets_abancart_reminder',
        'primary' => 'id_ets_abancart_reminder',
        'multilang' => true,
        'fields' => array(

            'id_ets_abancart_campaign' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'enabled' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'priority' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            'day' => array('type' => self::TYPE_STRING),
            'hour' => array('type' => self::TYPE_STRING),
            'minute' => array('type' => self::TYPE_STRING),
            'second' => array('type' => self::TYPE_STRING),
            'redisplay' => array('type' => self::TYPE_STRING),
            'delay_popup_based_on' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

            'free_shipping' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'discount_option' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'discount_code' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'apply_discount' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'reduction_percent' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'reduction_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reduction_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'apply_discount_in' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'enable_count_down_clock' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'quantity_per_user' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'reduction_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'selected_product' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'reduction_exclude_special' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'gift_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'gift_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'send_repeat_email' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'schedule_time' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'allow_null' => true),
            'highlight_discount' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'allow_multi_discount' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            'id_ets_abancart_email_template' => array('type' => self::TYPE_INT),

            'text_color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'background_color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),

            'icon_notify' => array('type' => self::TYPE_STRING),
            'header_bg' => array('type' => self::TYPE_STRING),
            'popup_width' => array('type' => self::TYPE_INT),
            'popup_height' => array('type' => self::TYPE_INT),
            'border_radius' => array('type' => self::TYPE_INT),
            'border_width' => array('type' => self::TYPE_INT),
            'border_color' => array('type' => self::TYPE_STRING),
            'popup_body_bg' => array('type' => self::TYPE_STRING),
            'header_text_color' => array('type' => self::TYPE_STRING),
            'header_height' => array('type' => self::TYPE_INT),
            'header_font_size' => array('type' => self::TYPE_INT),
            'font_size' => array('type' => self::TYPE_INT),
            'close_btn_color' => array('type' => self::TYPE_STRING),
            'padding' => array('type' => self::TYPE_INT),
            'vertical_align' => array('type' => self::TYPE_STRING),
            'overlay_bg' => array('type' => self::TYPE_STRING),
            'overlay_bg_opacity' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_prefix' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),

            'title' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'discount_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 254),
        )
    );

    public static function setNoVoucher($id_ets_abancart_campaign)
    {
        if (!$id_ets_abancart_campaign || !Validate::isUnsignedInt($id_ets_abancart_campaign))
            return false;

        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder` SET `discount_option`=\'no\' 
            WHERE `discount_option`=\'auto\' AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign . ' 
        ');
    }

    public static function getReminders($id_ets_abancart_campaign, $context = null)
    {
        if ($id_ets_abancart_campaign < 1 || !Validate::isUnsignedInt($id_ets_abancart_campaign))
            return false;
        if ($context == null)
            $context = Context::getContext();
        $dq = new DbQuery();
        $dq
            ->select('ar.*, arl.title')
            ->from('ets_abancart_reminder', 'ar')
            ->leftJoin('ets_abancart_reminder_lang', 'arl', 'ar.id_ets_abancart_reminder=arl.id_ets_abancart_reminder')
            ->leftJoin('ets_abancart_campaign', 'ac', 'ac.id_ets_abancart_campaign=ar.id_ets_abancart_campaign')
            ->where('arl.id_lang=' . (int)$context->language->id)
            ->where('ac.id_shop=' . (int)$context->shop->id)
            ->where('ac.id_ets_abancart_campaign=' . (int)$id_ets_abancart_campaign);

        return Db::getInstance()->executeS($dq);
    }

    public function delete()
    {
        $this->deleted = 1;
        return $this->update();
    }

    public function clearTracking()
    {
        if ($this->id > 0) {
            return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` WHERE `id_ets_abancart_reminder`=' . (int)$this->id);
        }
        return true;
    }

    public function clearQueue()
    {
        if ($this->id > 0) {
            return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_abancart_email_queue` WHERE `id_ets_abancart_reminder`=' . (int)$this->id);
        }
        return true;
    }

    public function update($null_values = false)
    {
        if (!$null_values)
            $null_values = true;

        if ($this->schedule_time == '0000-00-00') {
            $this->schedule_time = null;
        }

        $keep_old_status = $this->deleted ? -1 : self::getStatusById($this->id);

        if ($res = parent::update($null_values)) {
            if ($this->deleted || $this->enabled == self::REMINDER_STATUS_STOP) {
                $res &= EtsAbancartIndex::deleteIndex($this->id_ets_abancart_campaign, $this->id, 0, $this->priority);
                $res &= EtsAbancartIndexCustomer::deleteIndex($this->id_ets_abancart_campaign, $this->id);
                if ($this->deleted)
                    $res &= self::updatePosition($this->id_ets_abancart_campaign);
                $res &= $this->clearQueue();
                $res &= $this->clearTracking();
            } else
                $this->addCustomerIndexNow($keep_old_status);
        }

        return $res;
    }

    public static function nextRun($id_ets_abancart_campaign, $id_ets_abancart_reminder = 0, $priority = 0)
    {
        if (!$id_ets_abancart_campaign || !Validate::isUnsignedInt($id_ets_abancart_campaign)) {
            return [];
        }
        if ($id_ets_abancart_reminder > 0 && (!Validate::isUnsignedInt($priority) || $priority <= 0)) {
            $priority = (int)Db::getInstance()->getValue('SELECT `priority` FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` WHERE `id_ets_abancart_reminder` = ' . (int)$id_ets_abancart_reminder);
        }
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` WHERE `deleted`=0 AND `enabled`=1 AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign . ' AND `id_ets_abancart_reminder`!=' . (int)$id_ets_abancart_reminder . ' AND `priority` >= ' . (int)$priority . ' ORDER BY `priority`');
    }

    public static function getStatusById($id_ets_abancart_reminder)
    {
        if (!$id_ets_abancart_reminder || !Validate::isUnsignedInt($id_ets_abancart_reminder))
            return false;
        return (int)Db::getInstance()->getValue('SELECT `enabled` FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` WHERE `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder);
    }

    public static function getLastPriority($id_ets_abancart_campaign)
    {
        return (int)Db::getInstance()->getValue('SELECT MAX(`priority`) FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` WHERE  `deleted`=0 AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign);
    }

    public function add($auto_date = true, $null_values = false)
    {
        if (!$null_values)
            $null_values = true;
        $this->priority = self::getLastPriority($this->id_ets_abancart_campaign) + 1;
        if ($exec = parent::add($auto_date, $null_values)) {
            $exec &= $this->addCustomerIndexNow(self::REMINDER_STATUS_DRAFT);
        }

        return $exec;
    }

    public function addCustomerIndexNow($old_status = null)
    {
        $campaign = new EtsAbancartCampaign($this->id_ets_abancart_campaign);
        if ($campaign->email_timing_option == EtsAbancartReminder::CUSTOMER_EMAIL_SEND_RUN_NOW && (int)$this->enabled == self::REMINDER_STATUS_FINISHED && $old_status == self::REMINDER_STATUS_DRAFT) {
            $customers = Customer::getCustomers(true);
            if ($customers) {
                foreach ($customers as $customer) {
                    if (!EtsAbancartUnsubscribers::isUnsubscribe($customer['email'])) {
                        EtsAbancartIndexCustomer::addCustomerIndex(new Customer((int)$customer['id_customer'])
                            , $this->id_ets_abancart_campaign
                            , false
                            , false
                            , false
                            , false
                            , false
                            , $this->id
                            , true
                        );
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param $campaign_ids 1,2,3..
     * @param Context $context
     * @return string
     */
    public static function getSQLReminders($campaign_ids, $context = null, $exclude_cookie = true)
    {
        if (!is_array($campaign_ids) || !count($campaign_ids) || !Validate::isArrayWithIds($campaign_ids))
            return false;

        if ($context == null)
            $context = Context::getContext();

        $excludeIdsAll = [];
        if ($exclude_cookie) {
            $abandonedCookies = isset($context->cookie->ets_abancart_reminders) ? json_decode($context->cookie->ets_abancart_reminders, true) : [];
            $campaigns = [EtsAbancartCampaign::CAMPAIGN_TYPE_POPUP, EtsAbancartCampaign::CAMPAIGN_TYPE_BAR, EtsAbancartCampaign::CAMPAIGN_TYPE_BROWSER];
            if ($campaigns) {
                foreach ($campaigns as $campaign) {
                    if (isset($abandonedCookies[$campaign]) && $abandonedCookies[$campaign])
                        foreach ($abandonedCookies[$campaign] as $reminder)
                            $excludeIdsAll[] = (int)$reminder['id_ets_abancart_reminder'];
                }
            }
        }

        $productInCart = count($context->cart->getProducts(true)) > 0;

        $query = '
            SELECT ar.id_ets_abancart_reminder 
            , ((86400*IFNULL(ar.day, 0) + 3600*IFNULL(ar.hour, 0) + 60*IFNULL(ar.minute, 0) + IFNULL(ar.second, 0)) - IF(ac.has_product_in_cart = ' . EtsAbancartCampaign::HAS_SHOPPING_CART_YES . ' AND ar.delay_popup_based_on = 1 AND ' . (int)$productInCart . ', ' . ($context->cart->date_add != null? (int)(time() - strtotime($context->cart->date_add)) : 0) . ', 0)) as `lifetime`
            , ac.campaign_type
            FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON ar.id_ets_abancart_campaign = ac.id_ets_abancart_campaign
            WHERE ar.enabled=1 AND ar.deleted=0
                AND ac.enabled=1 AND ac.deleted=0
                AND ar.id_ets_abancart_campaign IN (' . implode(',', $campaign_ids) . ')
                AND IF(ac.has_product_in_cart = ' . EtsAbancartCampaign::HAS_SHOPPING_CART_YES . ', ' . (int)$productInCart . ', 1)
                ' . ($excludeIdsAll ? ' AND ar.id_ets_abancart_reminder NOT IN (' . implode(',', $excludeIdsAll) . ')' : '') . '
        ';
        return Db::getInstance()->executeS($query);
    }

    public static function getLifeTime($id_ets_abancart_reminder, $id_ets_abancart_campaign = 0, $context = null)
    {
        if (!$id_ets_abancart_reminder || !Validate::isUnsignedInt($id_ets_abancart_reminder))
            return 0;

        if ($context == null)
            $context = Context::getContext();

        if ($id_ets_abancart_campaign <= 0) {
            $reminder = new EtsAbancartReminder($id_ets_abancart_reminder);
            $id_ets_abancart_campaign = $reminder->id_ets_abancart_campaign;
        }

        $productInCart = count($context->cart->getProducts(true)) > 0;
        $query = '
            SELECT ((86400*IFNULL(ar.day, 0) + 3600*IFNULL(ar.hour, 0) + 60*IFNULL(ar.minute, 0) + IFNULL(ar.second, 0)) - IF(ac.has_product_in_cart = ' . EtsAbancartCampaign::HAS_SHOPPING_CART_YES . ' AND ar.delay_popup_based_on = 1 AND ' . (int)$productInCart . ', ' . ($context->cart->date_add !== null? (int)(time() - strtotime($context->cart->date_add)) : 0) . ', 0))
            FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON ar.id_ets_abancart_campaign = ac.id_ets_abancart_campaign
            WHERE ar.enabled=1 AND ar.deleted=0 AND ac.enabled=1 AND ac.deleted=0
                AND ar.id_ets_abancart_reminder = ' . (int)$id_ets_abancart_reminder . '
                AND ac.id_ets_abancart_campaign = ' . (int)$id_ets_abancart_campaign . '
                AND IF(ac.has_product_in_cart = ' . EtsAbancartCampaign::HAS_SHOPPING_CART_YES . ', ' . (int)$productInCart . ', 1)
        ';
        return Db::getInstance()->getValue($query);
    }

    public static function campaignValid($type, $email_timing_option = 0)
    {
        if (trim($type) == '' || !Validate::isCleanHtml($type))
            return false;

        return (int)Db::getInstance()->getValue('
			SELECT COUNT(ar.id_ets_abancart_reminder)
            FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON (ac.id_ets_abancart_campaign = ar.id_ets_abancart_campaign)
			WHERE ac.id_ets_abancart_campaign is NOT NULL' . ($email_timing_option > 0 ? ' AND ac.email_timing_option=' . (int)$email_timing_option : '') . '
			    AND ac.campaign_type = \'' . pSQL($type) . '\'
			    AND ac.enabled = 1
			    AND ac.deleted = 0
			    AND ar.enabled = 1
			    AND ar.deleted = 0
		') > 0 ? 1 : 0;
    }

    public static function getTotalReminder($id_campaign = 0)
    {
        $query = new DbQuery();
        $query->select('COUNT(*) as total_reminder')
            ->from('ets_abancart_reminder')
            ->where('1');

        if ($id_campaign && Validate::isUnsignedInt($id_campaign)) {
            $query->where('id_ets_abancart_campaign = ' . (int)$id_campaign);
        }

        return (int)Db::getInstance()->getValue($query);
    }


    public static function getNextMailTime($id_cart, $fieldValue = true, $idLang = 0)
    {
        if ($idLang == 0) {
            $idLang = Context::getContext()->language->id;
        }
        $dq = new DbQuery();
        $dq
            ->select('FROM_UNIXTIME(UNIX_TIMESTAMP(ai.cart_date_add) + 86400 * IFNULL(ar.day, 0) + 3600*IFNULL(ar.hour, 0), \'%Y-%m-%d %H:%i:%s\') `next_mail_time`')
            ->from('ets_abancart_index', 'ai')
            ->leftJoin('ets_abancart_reminder', 'ar', 'ai.id_ets_abancart_reminder = ar.id_ets_abancart_reminder')
            ->leftJoin('ets_abancart_campaign', 'ac', 'ac.id_ets_abancart_campaign = ai.id_ets_abancart_campaign')
            ->where('ac.campaign_type=\'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_EMAIL) . '\'')
            ->where('ar.id_ets_abancart_reminder > 0')
            ->where('ai.id_cart=' . (int)$id_cart)
            ->orderBy('`next_mail_time` ASC');
        if (!$fieldValue) {
            $dq
                ->select('ar.id_ets_abancart_reminder')
                ->select('arl.title `reminder_name`')
                ->select('ac.campaign_type')
                ->leftJoin('ets_abancart_reminder_lang', 'arl', 'arl.id_ets_abancart_reminder = ar.id_ets_abancart_reminder AND arl.id_lang=' . (int)$idLang)
                ->where('ai.id_cart');

            return Db::getInstance()->executeS($dq);
        }
        return Db::getInstance()->getValue($dq);
    }

    public static function isInvalid($id_ets_abancart_reminder)
    {
        if (!$id_ets_abancart_reminder || !Validate::isUnsignedInt($id_ets_abancart_reminder))
            return true;
        $dq = new DbQuery();
        $dq
            ->select('id_ets_abancart_reminder')
            ->from('ets_abancart_reminder', 'ar')
            ->innerJoin('ets_abancart_campaign', 'ac', 'ac.id_ets_abancart_campaign = ar.id_ets_abancart_campaign')
            ->where('ar.id_ets_abancart_reminder=' . (int)$id_ets_abancart_reminder)
            ->where('ac.enabled = 0 OR ac.deleted = 1 OR ar.enabled != 1 OR ar.deleted = 1');
        return (bool)Db::getInstance()->getValue($dq);
    }

    public static function hasVoucherInReminder($id_ets_abancart_campaign, $id_ets_abancart_reminder = 0)
    {
        if (!$id_ets_abancart_campaign || !Validate::isUnsignedInt($id_ets_abancart_campaign))
            return false;

        $query = '
            SELECT COUNT(ar.`id_ets_abancart_reminder`)
            FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder_lang` arl ON (ar.`id_ets_abancart_reminder` = arl.`id_ets_abancart_reminder`)
            WHERE ar.`id_ets_abancart_campaign` = ' . (int)$id_ets_abancart_campaign . '
                AND ar.`discount_option` != \'fixed\'
                AND arl.`content` REGEXP \'\\\\[discount_(code|from|to)|reduction|money_saved|total_payment_after_discount|button_add_discount|show_discount_box|discount_count_down_clock\\\\]\'
                ' . ($id_ets_abancart_reminder > 0 ? ' AND `ar.id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder : '') . '
        ';
        return (int)Db::getInstance()->getValue($query);
    }

    public static function isSame($day, $hour, $min, $sec, $schedule_time = null, $id_ets_abancart_reminder = 0, $id_ets_abancart_campaign = 0)
    {
        $query = '
            SELECT `id_ets_abancart_reminder` FROM `' . _DB_PREFIX_ . 'ets_abancart_reminder`
            WHERE `enabled` = 1
                AND `deleted` = 0
                AND (`day`' . ($day > 0 ? '=' . $day . ')' : ' is NULL OR `day`=0.00) ') . '
                AND (`hour`' . ($hour > 0 ? '=' . $hour . ')' : ' is NULL OR `hour`=0.00) ') . '
                AND (`minute`' . ($min > 0 ? '=' . $min . ')' : ' is NULL OR `minute`=0.00) ') . '
                AND (`second`' . ($sec > 0 ? '=' . $sec . ')' : ' is NULL OR `second`=0.00) ') . '
                ' . ($schedule_time !== null && trim($schedule_time) !== '' ? 'AND `schedule_time`=\'' . pSQL($schedule_time) . '\'' : '') . '
                ' . ($id_ets_abancart_campaign > 0 ? 'AND `id_ets_abancart_campaign` =' . (int)$id_ets_abancart_campaign : '') . '
                ' . ($id_ets_abancart_reminder ? 'AND `id_ets_abancart_reminder` !=' . (int)$id_ets_abancart_reminder : '') . '
        ';
        return Db::getInstance()->executeS($query);
    }

    public static function getOrderTotalByOrderIds($order_ids)
    {
        if (!$order_ids)
            return 0;
        if (is_array($order_ids))
            $order_ids = implode(',', $order_ids);
        return Db::getInstance()->getValue('
            SELECT SUM(`total_paid_tax_incl`/`conversion_rate`) 
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_order` IN (' . pSQL($order_ids) . ')
        ');
    }

    public static function getTotalCartApplied($id_ets_abancart_campaign)
    {
        if (!$id_ets_abancart_campaign ||
            !Validate::isUnsignedInt($id_ets_abancart_campaign)
        ) {
            return 0;
        }
        $dq = new DbQuery();
        $dq
            ->select('COUNT(at.id_cart)')
            ->from('ets_abancart_campaign', 'ac')
            ->leftJoin('ets_abancart_reminder', 'ar', 'ar.id_ets_abancart_campaign=ac.id_ets_abancart_campaign')
            ->leftJoin('ets_abancart_tracking', 'at', 'at.id_ets_abancart_reminder =ar.id_ets_abancart_reminder')
            ->where('at.id_ets_abancart_reminder is NOT NULL')
            ->where('at.deleted=0')
            ->where('ac.id_ets_abancart_campaign=' . (int)$id_ets_abancart_campaign);

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function updatePosition($id_ets_abancart_campaign)
    {
        if (!$id_ets_abancart_campaign ||
            !Validate::isUnsignedInt($id_ets_abancart_campaign)
        ) {
            return false;
        }
        $exec = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder` SET `priority`=0 WHERE `deleted`=1 AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign, false);
        $exec &= Db::getInstance()->execute('SET @pos := 0', false);
        $exec &= Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder`
            SET `priority`=(SELECT @pos := @pos + 1)
            WHERE `deleted`=0 AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign . '
            ORDER BY `priority`
        ');
        return $exec;
    }

    public static function doTrackingMailRead($id_ets_abancart_reminder, $id_cart = 0, $id_customer = 0, $email = null)
    {
        if (!$id_ets_abancart_reminder || !Validate::isUnsignedInt($id_ets_abancart_reminder)) {
            return false;
        }
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` 
            SET `read`=1, `date_upd`="' . date('Y-m-d H:i:s') . '" 
            WHERE `id_cart`' . ($id_cart > 0 ? '=' . (int)$id_cart : ' is NULL') . ' 
                AND `id_customer`' . ($id_customer > 0 ? '=' . (int)$id_customer : ' is NULL') . ' 
                AND `email`' . (trim($email) !== '' ? '=\'' . pSQL($email) . '\'' : ' is NULL') . ' 
                AND `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder
        );
    }

    public static function doTrackingMailClick($id_ets_abancart_reminder, $id_ets_abancart_campaign = null, $id_cart = 0, $id_customer = 0, $email = null)
    {
        if (!$id_ets_abancart_reminder || !Validate::isUnsignedInt($id_ets_abancart_reminder)) {
            return false;
        }
        $query = 'UPDATE `' . _DB_PREFIX_ . 'ets_abancart_tracking` 
            SET `nb_click`=`nb_click`+1, 
                `date_upd`=\'' . date('Y-m-d H:i:s') . '\' 
            WHERE `id_cart`' . ($id_cart > 0 ? '=' . (int)$id_cart : ' is NULL') . ' 
                AND `id_customer`' . ($id_customer > 0 ? '=' . (int)$id_customer : ' is NULL') . ' 
                AND `email`' . ($email !== null && trim($email) !== '' ? '=\'' . pSQL($email) . '\'' : ' is NULL') . ' 
                AND `id_ets_abancart_reminder`=' . (int)$id_ets_abancart_reminder
            . ($id_ets_abancart_campaign != null && Validate::isUnsignedInt($id_ets_abancart_campaign) ? ' AND `id_ets_abancart_campaign`=' . (int)$id_ets_abancart_campaign : '');
        return Db::getInstance()->execute($query);
    }
}