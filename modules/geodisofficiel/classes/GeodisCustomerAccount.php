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

class GeodisCustomerAccount extends ObjectModel
{
    public $type;
    public $name;
    public $telephone;
    public $address1;
    public $address2;
    public $zip_code;
    public $city;
    public $id_country;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_customer_account',
        'primary' => 'id_customer_account',
        'fields' => array(
            'type' => array('type' => self::TYPE_STRING, 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'required' => true),
            'telephone' => array('type' => self::TYPE_STRING, 'required' => true),
            'address1' => array('type' => self::TYPE_STRING, 'required' => true),
            'address2' => array('type' => self::TYPE_STRING, 'required' => true),
            'zip_code' => array('type' => self::TYPE_STRING, 'required' => true),
            'city' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );
}
