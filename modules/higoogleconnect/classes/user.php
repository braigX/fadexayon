<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class HiGoogleConnectUser extends ObjectModel
{
    public $id_user;
    public $id_shop;
    public $disabled;
    public $default_status;
    public $position;
    public $name;
    public $description;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'higoogleuser',
        'primary' => 'id_user',
        'multilang' => false,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_google_account' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255],
            'first_name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 100],
            'last_name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 100],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 100],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    public static function getUsers()
    {
        $id_shop = Context::getContext()->shop->id;

        $query = new DbQuery();
        $query
            ->select('u.*')
            ->from('higoogleuser', 'u')
            ->where('u.`id_shop` = ' . (int) $id_shop);

        return Db::getInstance()->executeS($query);
    }

    public static function filterUsers($filters = [], $pageItems = 50, $pageNumber = 1)
    {
        $id_shop = Context::getContext()->shop->id;

        $searchFirstName = false;
        $searchLastName = false;
        $searchEmail = false;
        if (isset($filters['higoogleuserFilter_first_name'])) {
            $searchFirstName = $filters['higoogleuserFilter_first_name'];
        }
        if (isset($filters['higoogleuserFilter_last_name'])) {
            $searchLastName = $filters['higoogleuserFilter_last_name'];
        }
        if (isset($filters['higoogleuserFilter_email'])) {
            $searchEmail = $filters['higoogleuserFilter_email'];
        }

        $query = new DbQuery();
        $query
            ->select('u.*')
            ->from('higoogleuser', 'u')
            ->where('u.`id_shop` = ' . (int) $id_shop);

        if ($searchFirstName) {
            $query->where('u.`first_name` like "%' . pSQL($searchFirstName) . '%"');
        }
        if ($searchLastName) {
            $query->where('u.`last_name` like "%' . pSQL($searchLastName) . '%"');
        }
        if ($searchEmail) {
            $query->where('u.`email` like "%' . pSQL($searchEmail) . '%"');
        }

        $res = Db::getInstance()->executeS($query);
        $total = 0;
        if ($res) {
            $total = count($res);
        }

        $query->limit((int) $pageItems, (int) (($pageNumber - 1) * $pageItems));
        $users = Db::getInstance()->executeS($query);

        return [
            'total' => $total,
            'result' => $users,
        ];
    }

    public static function getUserByGoogleId($id_google_account)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'higoogleuser` WHERE `id_google_account` = "' . pSQL($id_google_account) . '"');
    }
}
