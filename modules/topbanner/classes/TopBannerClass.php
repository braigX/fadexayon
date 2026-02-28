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
class TopBannerClass extends ObjectModel
{
    public $id_banner;
    public $name;
    public $height;
    public $background;
    public $type;
    public $subtype;
    public $cartrule;
    public $timer;
    public $timer_background;
    public $timer_text_color;
    public $text_size;
    public $cta;
    public $cta_text_color;
    public $cta_background;
    public $status;
    public $mobile_text_size;
    public $with_mobile_text;
    protected $table = 'topbanner';
    protected $identifier = 'id_banner';

    public function getFields()
    {
        parent::validateFields();

        $fields = [];

        $fields['id_banner'] = (int) $this->id_banner;
        $fields['name'] = pSQL($this->name);
        $fields['height'] = pSQL($this->height);
        $fields['background'] = pSQL($this->background);
        $fields['type'] = (int) $this->type;
        $fields['subtype'] = (int) $this->subtype;
        $fields['cartrule'] = (int) $this->cartrule;

        $fields['timer'] = (int) $this->timer;
        $fields['timer_background'] = pSQL($this->timer_background);
        $fields['timer_text_color'] = pSQL($this->timer_text_color);

        $fields['text_size'] = (int) $this->text_size;

        $fields['cta'] = (int) $this->cta;
        $fields['cta_text_color'] = pSQL($this->cta_text_color);
        $fields['cta_background'] = pSQL($this->cta_background);

        $fields['mobile_text_size'] = (int) $this->mobile_text_size;
        $fields['with_mobile_text'] = (int) $this->with_mobile_text;

        $fields['status'] = (int) $this->status;

        return $fields;
    }

    public static function getAllBanners($id_lang)
    {
        $query = 'SELECT t.*, cr.code, crl.name as cr_name '
            . 'FROM ' . _DB_PREFIX_ . 'topbanner t '
            . 'JOIN ' . _DB_PREFIX_ . 'cart_rule cr ON t.cartrule = cr.id_cart_rule '
            . 'JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON cr.id_cart_rule = crl.id_cart_rule '
            . 'WHERE crl.id_lang = ' . (int) $id_lang;
        $cartRuleBanners = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $query = 'SELECT t.* '
            . 'FROM ' . _DB_PREFIX_ . 'topbanner t '
            . 'WHERE cartrule = 0';
        $noCartRuleBanners = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return array_merge($cartRuleBanners, $noCartRuleBanners);
    }

    public static function getBanner()
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'topbanner WHERE status = 1';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    public static function getBannerById($id_banner)
    {
        $query = 'SELECT * '
            . 'FROM ' . _DB_PREFIX_ . 'topbanner t '
            . 'WHERE t.id_banner = ' . (int) $id_banner;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    public static function disableAll()
    {
        $query = 'UPDATE ' . _DB_PREFIX_ . 'topbanner SET status = 0';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
    }

    public static function disableBanner($id_banner)
    {
        $query = 'UPDATE ' . _DB_PREFIX_ . 'topbanner SET status = 0 WHERE id_banner = ' . (int) $id_banner;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
    }

    public static function deleteBanner($id_banner)
    {
        $query = 'DELETE FROM ' . _DB_PREFIX_ . 'topbanner WHERE id_banner = ' . (int) $id_banner;
        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query)) {
            $query = 'DELETE FROM ' . _DB_PREFIX_ . 'topbanner_lang WHERE id_banner = ' . (int) $id_banner;

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
        }
    }
}
