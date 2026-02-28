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

class GeodisOption extends ObjectModel
{
    public $code;
    public $attribute;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_option',
        'primary' => 'id_option',
        'fields' => array(
            'code' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'attribute' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'position' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public static function getByAttribute($attribute)
    {
        $collection = self::getCollection();
        $collection->where('attribute', '=', $attribute);

        $object = $collection->getFirst();

        if (!$object) {
            $object = new self();
            $object->attribute = $attribute;
        }

        return $object;
    }

    public static function init()
    {
        $options = array(
            array(
                'attribute' => 'livEtage',
                'code' => 'livEtage',
            ),
            array(
                'attribute' => 'miseLieuUtil',
                'code' => 'miseLieuUtil',
            ),
            array(
                'attribute' => 'depotage',
                'code' => 'depotage',
            ),
        );

        $position = 0;
        foreach ($options as $option) {
            $object = self::getByAttribute($option['attribute']);
            $object->code = $option['code'];
            $object->position = $position++;

            if (!$object->save()) {
                return false;
            }
        }
        unset($options, $position, $option, $object);

        return true;
    }
}
