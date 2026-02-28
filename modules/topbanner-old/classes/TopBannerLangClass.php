<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class TopBannerLangClass extends ObjectModel
{
    public $id_banner;
    public $id_lang;
    public $id_shop;
    public $name;
    public $value;
    protected $table = 'topbanner_lang';

    public static function saveValueWithHTML($id_banner, $id_lang, $name, $value)
    {
        return static::saveValue($id_banner, $id_lang, $name, $value, true);
    }

    public static function saveValue($id_banner, $id_lang, $name, $value, $withHtml = false)
    {
        $operation = '';
        if (is_null($id_banner)) {
            $operation = 'insert';
        } else {
            $exist = 'SELECT id_banner '
                    . 'FROM ' . _DB_PREFIX_ . 'topbanner_lang '
                    . 'WHERE id_banner = ' . (int) $id_banner . ' '
                    . 'AND id_lang = ' . (int) $id_lang . ' '
                    . 'AND name = "' . pSQL($name) . '"';
            $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($exist);
            $operation = (count($count) > 0) ? 'update' : 'insert';
        }

        switch ($operation) {
            case 'insert':
                $query = 'INSERT INTO ' . _DB_PREFIX_ . 'topbanner_lang(id_banner, id_lang, name, value) '
                        . 'VALUES(' . (int) $id_banner . ', ' . (int) $id_lang . ', "' . pSQL($name) . '", "' . pSQL($value, $withHtml) . '")';
                break;
            case 'update':
                $query = 'UPDATE ' . _DB_PREFIX_ . 'topbanner_lang '
                        . 'SET value = "' . pSQL($value, $withHtml) . '" '
                        . 'WHERE id_banner = ' . (int) $id_banner . ' '
                        . 'AND id_lang = ' . (int) $id_lang . ' '
                        . 'AND name = "' . pSQL($name) . '"';
                break;
            default:
                return 'An error occured - saveValue';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
    }

    public static function getLangs($id_banner)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'topbanner_lang WHERE id_banner = ' . (int) $id_banner;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getLangsPreview($banner)
    {
        $langs = [];

        foreach ($banner as $key => $field) {
            if (strpos($key, '-')) {
                $explode = explode('-', $key);
                $langs[] = [
                    'id_lang' => $explode[1],
                    'name' => $explode[0],
                    'value' => $field,
                ];
            }
        }

        return $langs;
    }

    public static function getValue($id_lang, $name, $langs)
    {
        foreach ($langs as $lang) {
            if ($lang['id_lang'] == $id_lang && $name == $lang['name']) {
                return $lang['value'];
            }
        }
    }
}
