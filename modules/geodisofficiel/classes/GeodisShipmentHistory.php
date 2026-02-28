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

class GeodisShipmentHistory extends ObjectModel
{
    public $id_shipment;
    public $status_code;
    public $status_label;
    public $event_date;
    public $event_place;
    public $event_trace;
    public $event_infos;
    public $date_add;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_shipment_history',
        'primary' => 'id_shipment_history',
        'fields' => array(
            'id_shipment' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'status_code' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'status_label' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'event_date' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'event_place' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'event_trace' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'event_infos' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );
}
