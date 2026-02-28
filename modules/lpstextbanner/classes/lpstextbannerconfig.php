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

class LpsTextBannerConfig extends ObjectModel
{
    public $id_lpstextbanner_config;

    public $display_banner;

    public $fixed_banner;

    public $banner_background_color;

    public $banner_text_color;

    public $transition_effect;

    public $directionH;

    public $directionV;

    public $speedScroll;

    public $displayTime;

    public $id_shop;

    public static $definition = [
        'table' => 'lpstextbanner_config',
        'primary' => 'id_lpstextbanner_config',
        'multilang' => false,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'display_banner' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'fixed_banner' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'banner_background_color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor'],
            'banner_text_color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor'],
            'transition_effect' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100],
            'directionH' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100],
            'directionV' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100],
            'speedScroll' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'displayTime' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
        ],
    ];

    public function copyFromPost()
    {
        foreach ($_POST as $key => $value) {
            if (property_exists($this, $key) && $key != 'id_' . $this->table) {
                $this->{$key} = $value;
            }
        }
    }
    public static function getByIdShop($id_shop, $id_lang = null)
    {
        $sql = 'SELECT lpstbc.`id_lpstextbanner_config`
            FROM `' . _DB_PREFIX_ . 'lpstextbanner_config` lpstbc
            WHERE lpstbc.`id_shop` =' . (int) $id_shop;
        $id_lpstextbanner_config = Db::getInstance()->getValue($sql);
        if (!$id_lpstextbanner_config) {
            return new LpsTextBannerConfig();
        } else {
            if (!$id_lang) {
                return new LpsTextBannerConfig((int) $id_lpstextbanner_config, null, (int) $id_shop);
            } else {
                return new LpsTextBannerConfig((int) $id_lpstextbanner_config, (int) $id_lang, (int) $id_shop);
            }
        }
    }
    public static function getConfig($field, $lang = false)
    {
        $lpsTextBannerConfig = self::getByIdShop((int) Context::getContext()->shop->id);
        if ($lang) {
            $value = $lpsTextBannerConfig->{$field}[(int) Context::getContext()->language->id];
        } else {
            $value = $lpsTextBannerConfig->{$field};
        }
        return $value;
    }
}
