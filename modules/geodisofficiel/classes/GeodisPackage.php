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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';

class GeodisPackage extends ObjectModel
{
    public $reference;
    public $id_shipment;
    public $width;
    public $height;
    public $depth;
    public $volume;
    public $weight;
    public $package_type;
    public $status_code;
    public $status_label;
    public $incident;
    public $date_add;
    public $date_upd;
    protected $shipment;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_package',
        'primary' => 'id_package',
        'fields' => array(
            'id_shipment' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'height' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'depth' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'volume' => array('type' => self::TYPE_FLOAT,'validate' => 'isFloat'),
            'reference' => array('type' => self::TYPE_STRING, 'required' => true),
            'package_type' => array('type' => self::TYPE_NOTHING, 'required' => true),
            'status_code' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'status_label' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'incident' =>  array('type' => self::TYPE_BOOL, 'required' => true),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getShipment()
    {
        if (is_null($this->shipment)) {
            $this->shipment = new GeodisShipment($this->id_shipment);
        }

        return $this->shipment;
    }

    public function getPackageOrderDetailCollection()
    {
        $collection = new PrestaShopCollection('GeodisPackageOrderDetail');
        $collection->where('id_package', '=', $this->id);

        return $collection;
    }
}
