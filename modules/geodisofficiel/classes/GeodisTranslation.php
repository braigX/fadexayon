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

class GeodisTranslation extends ObjectModel
{
    public $key;
    public $id_lang;
    public $value;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_translation',
        'primary' => 'id_translation',
        'fields' => array(
            'key' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => '100'),
            'id_lang' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'value' => array('type' => self::TYPE_HTML, 'required' => true, 'size' => 2048),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function get($key, $idLang, $ignorePrefix = false)
    {
        if (!$ignorePrefix) {
            $key = GEODIS_NAME_SQL.'.'.$key;
        }

        $collection = new PrestaShopCollection('GeodisTranslation');
        $collection->where('key', '=', $key);
        $collection->where('id_lang', '=', $idLang);

        $item = $collection->getFirst();
        if (!$item) {
            $item = new GeodisTranslation();
            $item->key = $key;
            $item->id_lang = $idLang;
        }

        return $item;
    }

    public static function set($key, $idLang, $value, $ignorePrefix = false, $override = true)
    {
        $item = self::get($key, $idLang, $ignorePrefix);
        if (!$override && $item->id) {
            return;
        }

        $item->value = $value;
        $item->save();
    }
}
