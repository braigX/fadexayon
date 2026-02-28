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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackage.php';

class GeodisPackageOrderDetail extends ObjectModel
{
    public $id_package;
    public $id_order_detail;
    public $quantity;
    public $date_add;
    public $date_upd;
    public $is_wine_and_liquor = 0;
    public $id_fiscal_code;
    public $nb_col;
    public $volume_cl;
    public $volume_l;
    public $n_mvt;
    public $shipping_duration;
    public $fiscal_code_ref;
    public $n_ea;
    protected $package;

    protected static $wsRules = array(
        'CRD' => array(
            'nb_col' => array('required' => true, 'is_int' => 'true'),
            'volume_cl' => array('required' => true),
            'volume_l' => array('required' => true, 'is_alterable' => true),
        ),
        'DSA' => array(
            'nb_col' => array('required' => true, 'is_int' => 'true'),
            'volume_cl' => array('required' => true),
            'volume_l' => array('required' => true, 'is_alterable' => true),
            'n_mvt' => array('required' => true),
        ),
        'DAE' => array(
            'nb_col' => array('required' => true, 'is_int' => 'true'),
            'volume_cl' => array('required' => true),
            'fiscal_code_ref' => array('required' => true),
            'volume_l' => array('required' => true, 'is_alterable' => false),
        ),
        'DAA' => array(
            'nb_col' => array('required' => true, 'is_int' => 'true'),
            'volume_cl' => array('required' => true),
            'volume_l' => array('required' => true, 'is_alterable' => true),
            'n_ea' => array('required' => true),
            'shipping_duration' => array('required' => true, 'is_int' => 'true'),
            'n_mvt' => array('required' => true),
        ),
    );


    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_package_order_detail',
        'primary' => 'id_package_order_detail',
        'fields' => array(
            'id_package' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_order_detail' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'quantity' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'is_wine_and_liquor' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_fiscal_code' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'nb_col' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'volume_cl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'volume_l' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'n_mvt' => array('type' => self::TYPE_STRING, 'size' => 20),
            'shipping_duration' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'fiscal_code_ref' => array('type' => self::TYPE_STRING, 'size' => 20),
            'n_ea' => array('type' => self::TYPE_STRING, 'size' => 20),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public function getOrderDetail()
    {
        return new OrderDetail($this->id_order_detail);
    }

    public function getPackage()
    {
        if (is_null($this->package)) {
            $this->package = new GeodisPackage($this->id_package);
        }

        return $this->package;
    }

    public function save($nullValues = false, $autoDate = true)
    {
        if ($this->is_wine_and_liquor) {
            $fiscalCode = new GeodisFiscalCode($this->id_fiscal_code);
            if (isset(self::$wsRules[$fiscalCode->label])) {
                foreach (self::$wsRules[$fiscalCode->label] as $attribute => $option) {
                    if (empty($this->$attribute) && $option['required']) {
                        throw new Exception('Missing attribute "'.$attribute.'".');
                    }
                }
            }
        }

        return parent::save($nullValues, $autoDate);
    }

    public static function getWSRules()
    {
        return self::$wsRules;
    }
}
