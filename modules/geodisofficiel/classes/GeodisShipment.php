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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackage.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackageOrderDetail.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipmentHistory.php';

class GeodisShipment extends ObjectModel
{
    public $reference;
    public $id_order;
    public $id_group_carrier;
    public $id_carrier;
    public $id_reference_carrier;
    public $tracking_number;
    public $tracking_url;
    public $is_complete;
    public $is_label_printed;
    public $weight;
    public $status_code;
    public $status_label;
    public $incident;
    public $recept_number;
    public $type_position;
    public $departure_date;
    public $date_add;
    public $date_upd;
    public $is_endlife;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_shipment',
        'primary' => 'id_shipment',
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'id_order' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_reference_carrier' => array(
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ),
            'id_group_carrier' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_carrier' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'is_complete' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_label_printed' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'tracking_number' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'tracking_url' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'status_code' =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'status_label' =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'incident' =>  array('type' => self::TYPE_BOOL, 'required' => true),
            'recept_number' =>  array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'departure_date'  =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'recept_number' =>  array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'type_position' =>  array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'is_endlife' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        ),
    );

    public function getPackages()
    {
        $collection = new PrestaShopCollection('GeodisPackage');
        $collection->where('id_shipment', '=', $this->id);

        return $collection;
    }

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public static function getListRecepMajStatus()
    {
        $collection = new PrestaShopCollection('GeodisShipment');
        $collection->where('is_endlife', '=', 0);

        return $collection;
    }

    public function getHistory()
    {
        $collection = new PrestaShopCollection('GeodisShipmentHistory');
        $collection->where('id_shipment', '=', $this->id);
        $collection->orderBy('date_add', 'DESC');

        return $collection;
    }

    public static function getFromExternal($receptNumber)
    {
        $collection = self::getCollection();
        $collection->where('recept_number', '=', $receptNumber);

        return $collection->getFirst();
    }
}
