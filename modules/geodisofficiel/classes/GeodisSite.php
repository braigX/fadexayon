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

class GeodisSite extends ObjectModel
{
    public $type;
    public $code;
    public $name;
    public $email;
    public $telephone;
    public $address1;
    public $address2;
    public $zip_code;
    public $city;
    public $id_country;
    public $default;
    public $removal;
    public $date_add;
    public $date_upd;
    public $country_name;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_site',
        'primary' => 'id_site',
        'multilang_shop' => true,
        'multishop' => true,
        'multilang' => true,
        'fields' => array(
            'type' => array('type' => self::TYPE_STRING, 'required' => true),
            'code' => array('type' => self::TYPE_STRING, 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'required' => true),
            'email' => array('type' => self::TYPE_STRING),
            'telephone' => array('type' => self::TYPE_STRING),
            'address1' => array('type' => self::TYPE_STRING, 'required' => true),
            'address2' => array('type' => self::TYPE_STRING),
            'zip_code' => array('type' => self::TYPE_STRING, 'required' => true),
            'city' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'default' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'lang' => true),
            'removal' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getFromExternal($type, $code)
    {
        $collection = self::getCollection();
        $collection->where('type', '=', $type);
        $collection->where('code', '=', $code);

        if (($first = $collection->getFirst())) {
            return $first;
        }

        $item = new self();
        $item->type = $type;
        $item->code = $code;

        return $item;
    }

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public static function getDefault()
    {
        $collection = new PrestaShopCollection(self::class, Context::getContext()->language->id);
        $collection->sqlWhere('a1.default = \'1\'');
        $collection->debug = true;

        return $collection->getFirst();
    }

    public function getCountry()
    {
        return new Country($this->id_country);
    }

    public static function getCountryName($idCountry)
    {
        $collection = new PrestashopCollection('country');
        $collection->where('id_country', '=', $idCountry);
        $country = $collection->getFirst();
        return $country->name[Context::getContext()->language->id];
    }
}
