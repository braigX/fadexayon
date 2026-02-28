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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';

class GeodisCarrierOption extends ObjectModel
{
    public $id_carrier;
    public $id_option;
    public $price_impact;
    public $active;
    public $date_add;
    public $date_upd;

    protected $option;
    protected $carrier;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_carrier_option',
        'primary' => 'id_carrier_option',
        'fields' => array(
            'id_carrier' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_option' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'price_impact' => array('type' => self::TYPE_FLOAT, 'required' => true, 'validate' => 'isFloat'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public function getCarrier()
    {
        if (!$this->carrier) {
            $this->carrier = new GeodisCarrier($this->id_carrier);
        }

        return $this->option;
    }

    public function getOption()
    {
        if (!$this->option) {
            $this->option = new GeodisOption($this->id_option);
        }

        return $this->option;
    }
}
