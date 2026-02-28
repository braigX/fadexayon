<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoNotification extends ObjectModel
{
    public $id_notification;
    public $id_onesignal;
    public $id_campaign;
    public $id_subscriber;
    public $id_cart;
    public $title;
    public $notification_type;
    public $status;
    public $clicked = 0;
    public $date_start;
    public $date_end;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'rg_pushnotifications_notification',
        'primary' => 'id_notification',
        'fields' => [
            'id_campaign' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'],
            'id_onesignal' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'id_subscriber' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'],
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'notification_type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'values' => ['event', 'reminder', 'message'], 'default' => 'event'],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'values' => ['delivered', 'queued', 'scheduled', 'canceled', 'norecipients'], 'default' => 'queued'],
            'clicked' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public static function cleanNotifications($clean_options = false, $range = false, $delete_empty_campaign = true)
    {
        if ($clean_options === false) {
            if ($clean_options = RgPuNoConfig::get('CLEAN_CLEAN')) {
                $clean = explode(',', $clean_options);
            }
        } else {
            $clean = explode(',', $clean_options);
        }

        if ($range === false) {
            $range = RgPuNoConfig::get('CLEAN_RANGE');
        }

        if (isset($clean)) {
            $where = '`status` IN ("' . implode('","', array_map('pSQL', $clean)) . '")
                AND ((`date_end` IS NOT NULL AND `date_end` < "' . date('Y-m-d H:i:s') . '") OR ' . ((int) $range
                    ? '(`date_end` IS NULL AND `date_start` < "' . date('Y-m-d H:i:s', strtotime('-' . (int) $range . ' days')) . '"))'
                    : '1)');

            if ((int) RgPuNoConfig::get('CART_REMINDER')) {
                $where .= ' AND `notification_type` != "reminder"';
            }

            Db::getInstance()->delete('rg_pushnotifications_notification', $where);
        }

        if ($delete_empty_campaign) {
            Db::getInstance()->delete(
                'rg_pushnotifications_campaign',
                '`id_campaign` NOT IN (SELECT `id_campaign` FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`)'
            );
        }
    }

    public static function getLastNotificationByCart($id_cart)
    {
        return Db::getInstance()->getRow('SELECT *
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
            WHERE `id_cart` = ' . (int) $id_cart . '
            ORDER BY `date_add` DESC');
    }

    public static function getTotalsData()
    {
        $data = [
            'total' => (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`'),
            'campaign' => (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification` WHERE `id_campaign` > 0
            '),
            'cart' => (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification` WHERE `id_cart` > 0
            '),
            'clicked' => (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification` WHERE `clicked` > 0 AND `id_campaign` = 0
            '),
        ];

        return $data;
    }

    public static function getIdNotificationByIdOneSignalAndSubscriber($id_onesignal, $id_subscriber)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_notification`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
            WHERE `id_onesignal` = "' . pSQL($id_onesignal) . '" AND `id_subscriber` = ' . (int) $id_subscriber);
    }

    public static function getIdOneSignalByCampaign($id_campaign)
    {
        $id_onesignal = Db::getInstance()->executeS('SELECT DISTINCT `id_onesignal`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
            WHERE `id_campaign` = ' . (int) $id_campaign);

        return array_column($id_onesignal, 'id_onesignal');
    }
}
