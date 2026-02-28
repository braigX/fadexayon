<?php
/**
 * 2007-2023 ETS-Soft
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft < etssoft.jsc@gmail.com >
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EtsRVUnsubscribe extends ObjectModel
{
    public $email;
    public $active;
    public $date_add;

    public static $definition = array(
        'table' => 'ets_rv_unsubscribe',
        'primary' => 'id_ets_rv_unsubscribe',
        'fields' => array(
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function getInstanceByEmail($email)
    {
        $id_ets_rv_unsubscribe = (int)Db::getInstance()->getValue('SELECT `id_ets_rv_unsubscribe` FROM `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` WHERE `email`=\'' . pSQL(trim($email)) . '\'');
        return new EtsRVUnsubscribe($id_ets_rv_unsubscribe);
    }

    public static function isUnsubscribe($email)
    {
        return (bool)Db::getInstance()->getValue('SELECT `email` FROM `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` WHERE `active`=1 AND `email`=\'' . pSQL(trim($email)) . '\'');
    }

    public static function setCustomerUnsubscribe($email, $active = 1)
    {
        $unsubscribe = self::getInstanceByEmail($email);
        if ($unsubscribe->id <= 0) {
            $unsubscribe->email = $email;
        }
        $unsubscribe->active = $active;
        $unsubscribe->date_add = date('Y-m-d H:i:s');

        return $unsubscribe->save();
    }

    public static function isSubscribeByEmail($email)
    {
        if (trim($email) == '' || !Validate::isEmail($email))
            return false;
        return Db::getInstance()->getValue('SELECT `email` FROM `' . _DB_PREFIX_ . (version_compare(_PS_VERSION_, '1.7', '>=') ? 'emailsubscription' : 'newsletter') . '` WHERE `email`=\'' . pSQL(trim($email)) . '\'');
    }
}