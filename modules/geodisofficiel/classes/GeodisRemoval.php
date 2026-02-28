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

class GeodisRemoval extends ObjectModel
{
    public $reference;
    public $recept_number;
    public $removal_date;
    public $time_slot;
    public $id_site;
    public $id_account;
    public $id_prestation;
    public $number_of_box;
    public $number_of_pallet;
    public $weight;
    public $volume;
    public $sent;
    public $observations;
    public $is_hazardous;
    public $fiscal_code;
    public $legal_volume;
    public $total_volume;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_removal',
        'primary' => 'id_removal',
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'recept_number' =>  array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'removal_date' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'time_slot' => array('type' => self::TYPE_NOTHING, 'required' => true),
            'id_site' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_account' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_prestation' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'number_of_box' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'number_of_pallet' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'weight' => array('type' => self::TYPE_FLOAT, 'required' => true, 'validate' => 'isFloat'),
            'volume' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'is_hazardous' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'fiscal_code' => array('type' => self::TYPE_INT, 'required' => false, 'validate' => 'isUnsignedInt'),
            'legal_volume' => array('type' => self::TYPE_FLOAT, 'required' => false, 'validate' => 'isFloat'),
            'total_volume' => array('type' => self::TYPE_FLOAT, 'required' => false, 'validate' => 'isFloat'),
            'sent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'observations' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 70),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }
}
