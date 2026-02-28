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

class GeodisFiscalCode extends ObjectModel
{
    public $label;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_fiscal_code',
        'primary' => 'id_fiscal_code',
        'fields' => array(
            'label' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        $collection = new PrestaShopCollection('GeodisFiscalCode');

        return $collection;
    }

    public static function getByLabel($label)
    {
        $collection = self::getCollection();
        $collection->where('label', '=', $label);

        $first = $collection->getFirst();
        if (!$first) {
            $first = new GeodisFiscalCode();
            $first->label = $label;
        }

        return $first;
    }

    public static function init()
    {
        $fiscalCodes = array('CRD', 'DSA', 'DAE', 'DAA');

        foreach ($fiscalCodes as $code) {
            $object = self::getByLabel($code);
            if (!$object->save()) {
                return false;
            }
        }
        unset($fiscalCodes, $code, $object);

        return true;
    }
}
