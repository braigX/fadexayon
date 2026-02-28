<?php
/**
 * Loulou66
 * LpsTextBanner module for Prestashop
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php*
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LpsTextBannerClass extends ObjectModel
{
    public $id_lpstextbanner;

    public $id_shop;

    public $message;

    public $display_link;

    public $link;

    public $target;

    public $position;

    public $active;

    public static $definition = [
        'table' => 'lpstextbanner',
        'primary' => 'id_lpstextbanner',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'display_link' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'target' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'message' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isMessage'],
            'link' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isAbsoluteUrl'],
        ],
    ];

    public function copyFromPost()
    {
        foreach ($_POST as $key => $value) {
            if (property_exists($this, $key) && $key != 'id_' . $this->table) {
                $this->{$key} = $value;
            }
        }
        if (count($this->fieldsValidateLang)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach ($this->fieldsValidateLang as $field => $validation) {
                    $validation = $field;
                    if (Tools::getIsset($field . '_' . (int) $language['id_lang'])) {
                        $this->{$field}[(int) $language['id_lang']] = Tools::getValue(
                            $field . '_' . (int) $language['id_lang']
                        );
                    }
                }
            }
        }
    }
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = LpsTextBannerClass::getHigherPosition() + 1;
        }
        $this->id_shop = (int) Context::getContext()->shop->id;
        $autoDate = false;
        $return = parent::add($autoDate, $nullValues);
        return $return;
    }
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(position) FROM `' . _DB_PREFIX_ . 'lpstextbanner`';
        $position = Db::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
    public static function updatePositions()
    {
        $positions = Tools::getValue('module-lpstextbanner');
        $new_positions = [];
        foreach ($positions as $v) {
            if (count(explode('_', $v)) == 4) {
                $new_positions[] = $v;
            }
        }
        foreach ($new_positions as $position => $value) {
            $pos = explode('_', $value);
            $lpstextbannerClass = new LpsTextBannerClass((int) $pos[2]);
            $lpstextbannerClass->position = $position;
            $lpstextbannerClass->save();
        }
    }
    public static function statusToggle($id_lpstextbanner)
    {
        $lpsTextBannerClass = new LpsTextBannerClass((int) $id_lpstextbanner);
        if (Validate::isLoadedObject($lpsTextBannerClass)) {
            $lpsTextBannerClass->active = !$lpsTextBannerClass->active;
            return $lpsTextBannerClass->update();
        } else {
            return false;
        }
    }
    public static function getTextBannerIds($id_shop)
    {
        $sql = 'SELECT lpstb.`id_lpstextbanner`
            FROM  `' . _DB_PREFIX_ . 'lpstextbanner` lpstb
            WHERE lpstb.`id_shop` = ' . (int) $id_shop . '
            ORDER BY lpstb.`position`';
        return Db::getInstance()->executeS($sql);
    }
    public static function getAllMessages($id_lang, $id_shop)
    {
        $sql = 'SELECT lpstb.*, lpstbl.*
            FROM `' . _DB_PREFIX_ . 'lpstextbanner` lpstb
            LEFT JOIN `' . _DB_PREFIX_ . 'lpstextbanner_lang` lpstbl
            ON (lpstb.`id_lpstextbanner` = lpstbl.`id_lpstextbanner`)
            WHERE lpstbl.`id_lang` = ' . (int) $id_lang . '
            AND lpstb.`id_shop` = ' . (int) $id_shop . '
            AND lpstb.`active` = 1
            ORDER BY lpstb.`position`';
        return Db::getInstance()->executeS($sql);
    }
}
