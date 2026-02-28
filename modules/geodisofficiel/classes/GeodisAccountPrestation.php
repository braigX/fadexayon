<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbSchema.php';

class GeodisAccountPrestation extends ObjectModel
{
    public $id_account;
    public $id_prestation;
    public $manage_wine_and_liquor;
    public $delay;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_account_prestation',
        'primary' => 'id_account_prestation',
        'fields' => array(
            'id_account' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_prestation' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'manage_wine_and_liquor' => array('type' => self::TYPE_BOOL, 'required' => false),
            'delay' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public static function get($idAccount, $idPrestation)
    {
        $collection = self::getCollection();
        $collection->where('id_account', '=', $idAccount);
        $collection->where('id_prestation', '=', $idPrestation);

        if (($first = $collection->getFirst())) {
            return $first;
        }

        $item = new self();
        $item->id_account = $idAccount;
        $item->id_prestation = $idPrestation;

        return $item;
    }

    public static function getCollectionFromAccount($account)
    {
        $collection = self::getCollection();

        $collection->where('id_account', '=', $account->id);

        return $collection;
    }

    public static function getCollectionFromPrestation($prestation)
    {
        $collection = self::getCollection();

        $collection->where('id_prestation', '=', $prestation->id);

        return $collection;
    }

    public static function getFromExternal($idAccount, $idPrestation)
    {
        $collection = self::getCollection();
        $collection->where('id_account', '=', $idAccount);
        $collection->where('id_prestation', '=', $idPrestation);

        if (($first = $collection->getFirst())) {
            return $first;
        }

        $item = new self();

        return $item;
    }
}
