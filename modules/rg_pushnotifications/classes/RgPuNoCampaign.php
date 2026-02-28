<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoCampaign extends ObjectModel
{
    public $id_campaign;
    public $title;
    public $total_notifications = 0;
    public $total_delivered = 0;
    public $total_unreachable = 0;
    public $total_clicked = 0;
    public $finished = 0;
    public $delivery;
    public $date_start;
    public $date_end;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'rg_pushnotifications_campaign',
        'primary' => 'id_campaign',
        'fields' => [
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'total_notifications' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'total_delivered' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'total_unreachable' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'total_clicked' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'finished' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'delivery' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'values' => ['immediately', 'intelligent', 'optimized'], 'default' => 'immediately'],
            'date_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public static function getTotalsData()
    {
        return Db::getInstance()->getRow('
            SELECT
                SUM(`total_notifications`) AS `total_notifications`,
                SUM(`total_delivered`) AS `total_delivered`,
                SUM(`total_unreachable`) AS `total_unreachable`,
                SUM(`total_clicked`) AS `total_clicked`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_campaign`
        ');
    }

    public static function getIdCampaignFinished()
    {
        $campaigns = Db::getInstance()->executeS('
            SELECT `id_campaign`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_campaign`
            WHERE `finished` = 1
        ');

        $id_campaigns = array_column($campaigns, 'id_campaign');
        $id_campaigns = array_map('intval', $id_campaigns);

        return $id_campaigns;
    }

    public function delete()
    {
        if ($result = parent::delete()) {
            Db::getInstance()->delete('`rg_pushnotifications_notification`', '`id_campaign` = ' . (int) $this->id_campaign);
        }

        return $result;
    }

    public function cancel()
    {
        $this->finished = 1;

        if ($result = $this->update()) {
            Db::getInstance()->update(
                'rg_pushnotifications_notification',
                ['status' => 'canceled'],
                '`status` IN("queued", "scheduled") AND `id_campaign` = ' . (int) $this->id_campaign
            );
        }

        return $result;
    }
}
