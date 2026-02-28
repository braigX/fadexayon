<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class SitemapConfig extends ObjectModel
{
    public $id_object;
    public $id_shop;
    public $type_object;
    public $priority;
    public $changefreq;
    public $is_export = 1;
    public $default_category;

    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    const PROTOCOL_HTTP = 'HTTP';
    const PROTOCOL_HTTPS = 'HTTPS';

    public static $definition = array(
        'table'             => 'site_map_item_conf',
        'primary'           => 'id_site_map_item_conf',
        'fields'            => array(
            'id_object' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'priority' => array('type' => 'DECIMAL(2,1)'),
            'type_object' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'changefreq' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'is_export' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'default_category' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        ),
    );

    protected static function getConfigSql()
    {
        $sql = new DbQuery();
        $sql->select('a.`id_object` as id_'.pSQL(static::getType()));
        $sql->select('a.`priority`');
        $sql->select('a.`changefreq`');
        $sql->select('a.`is_export`');
        $sql->from(self::$definition['table'], 'a');
        $sql->where(
            'a.`id_shop` = '
            .(int)Context::getContext()->shop->id.' AND a.`type_object` = "'.pSQL(static::getType()).'"'
        );
        return $sql;
    }

    public static function getConfig()
    {
        $result = Db::getInstance()->executeS(static::getConfigSql()->build());
        $conf = array();
        foreach ($result as $item) {
            $item['priority'] = Tools::ps_round($item['priority'], 1);
            $conf[$item['id_'.static::getType()]] = $item;
        }
        return $conf;
    }

    protected static function getItemsSql($id_lang = null)
    {
        unset($id_lang);
        $sql = new DbQuery();
        return $sql;
    }

    public static function getItems($id_lang = null)
    {
        $result = Db::getInstance()->executeS(static::getItemsSql($id_lang)->build());
        return (is_array($result) && count($result) ? $result : array());
    }

    public static function getChangeFreqs()
    {
        $l = TransModSMP::getInstance();
        return array(
            array(
                'id' => self::ALWAYS,
                'name' => $l->l('always', __FILE__)
            ),
            array(
                'id' => self::HOURLY,
                'name' => $l->l('hourly', __FILE__)
            ),
            array(
                'id' => self::DAILY,
                'name' => $l->l('daily', __FILE__)
            ),
            array(
                'id' => self::WEEKLY,
                'name' => $l->l('weekly', __FILE__)
            ),
            array(
                'id' => self::MONTHLY,
                'name' => $l->l('monthly', __FILE__)
            ),
            array(
                'id' => self::YEARLY,
                'name' => $l->l('yearly', __FILE__)
            ),
            array(
                'id' => self::NEVER,
                'name' => $l->l('never', __FILE__)
            )
        );
    }

    public static function getPriorities()
    {
        $priorities = array();
        $priority = 0;
        while ($priority <= 1) {
            $priorities[] = array(
                'id' => Tools::ps_round($priority, 1)
            );
            $priority += 0.1;
        }
        return $priorities;
    }

    public static function getSitemapFilename(
        $id_lang = null,
        $without_index = false,
        $with_image = false,
        $with_link = false
    ) {
        $shop_prefix = '';
        if ((int)Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $shop_prefix = '-'.(int)Context::getContext()->shop->id;
        }
        $lang_prefix = '';
        if (!is_null($id_lang)) {
            $lang_prefix = '-'.Language::getIsoById($id_lang);
        }
        if ($with_link) {
            $lang_prefix = '';
        }

        if (ConfSMP::getConf('SMP_PROTECT_FILE')) {
            $end_link = '-secret_file-' . ConfSMP::getConf('SMP_SECRET_FILE');
        }

        $file_name = 'sitemap'.$lang_prefix.$shop_prefix.($with_image ? '-with-images' : '')
            .($with_link ? '-with-alternate-links' : '')
            .(!empty($end_link) ? $end_link:'' ).(!$without_index ? '-index.xml' : '');

        return $file_name;
    }

    public static function getFullLinkSitemap(
        $id_lang = null,
        $without_index = false,
        $with_image = false,
        $with_link = false
    ) {
        return _PS_ROOT_DIR_.'/'.self::getSitemapFilename($id_lang, $without_index, $with_image, $with_link);
    }

    public static function getLinkSitemap(
        $id_lang = null,
        $without_index = false,
        $with_image = false,
        $with_link = false
    ) {
        return ToolsSMP::getShopDomainWithBase()
            .self::getSitemapFilename($id_lang, $without_index, $with_image, $with_link);
    }

    public static function getCronUrl($id_lang = null, $with_image = false, $with_link = false, $id_shop = null)
    {
        $params = array();
        if (!is_null($id_lang)) {
            $params['id_lang'] = $id_lang;
        }
        if (!is_null($id_shop)) {
            $params['id_shop'] = $id_shop;
        }
        if ($with_image) {
            $params['with_image'] = 1;
        }
        if ($with_link) {
            $params['with_link'] = 1;
        }
        if (ConfSMP::getConf('SMP_PROTECT')) {
            $params['secret'] = ConfSMP::getConf('SMP_SECRET');
        }

        //ToolsSMP::getShopDomain(true)
        return ToolsSMP::getShopDomainWithBase().'modules/'.ToolsModuleSMP::getModNameForPath(__FILE__)
        .'/cron.php'.(count($params) ? '?'.http_build_query($params) : '');
    }

    public static function getFullCronUrl($id_lang = null, $with_image = false, $with_link = false, $id_shop = null)
    {
        $params = array();
        if (!is_null($id_lang)) {
            $params['id_lang'] = $id_lang;
        }
        if (!is_null($id_shop)) {
            $params['id_shop'] = $id_shop;
        }
        if ($with_image) {
            $params['with_image'] = 1;
        }
        if ($with_link) {
            $params['with_link'] = 1;
        }
        if (ConfSMP::getConf('SMP_PROTECT')) {
            $params['secret'] = ConfSMP::getConf('SMP_SECRET');
        }

        return 'php '._PS_MODULE_DIR_.ToolsModuleSMP::getModNameForPath(__FILE__).'/cron.php'
            .(count($params) ? ' \'&'.http_build_query($params).'\'' : '');
    }

    protected static function getType()
    {
        return 'config';
    }

    protected static $types = array(
        'product',
        'cms',
        'meta',
        'user_link',
        'category',
        'manufacturer',
        'supplier'
    );

    protected static $default_params = array(
        'is_export' => 1,
        'priority' => 0.5,
        'changefreq' => 'always',
        'is_changefreq' => 1,
        'is_on_default_setting_category' => 1,
        'is_on_default_setting_export_product' => 1,
        'default_category' => 0,
    );

    public static function getDefaultSettings($type = null)
    {
        $id_shop = Context::getContext()->shop->id;
        $settings = array();
        if (is_null($type)) {
            foreach (self::$types as $type) {
                $settings[$type] = self::getDefaultSettings($type);
            }
        } else {
            $is_export = (ConfSMP::hasConf($type.'_is_export', null, $id_shop)
                ? (int)ConfSMP::getConf($type.'_is_export', null, null, $id_shop)
                : self::$default_params['is_export']
            );

            $is_changefreq = (ConfSMP::hasConf($type.'_is_changefreq', null, $id_shop)
                ? (int)ConfSMP::getConf($type.'_is_changefreq', null, null, $id_shop)
                : self::$default_params['is_changefreq']
            );

            $is_on_default_setting_category = (ConfSMP::hasConf(
                $type.'_is_on_default_setting_category',
                null,
                $id_shop
            )
                ? (int)ConfSMP::getConf(
                    $type.'_is_on_default_setting_category',
                    null,
                    null,
                    $id_shop
                )
                : self::$default_params['is_on_default_setting_category']
            );

            $is_on_default_setting_export_product = (ConfSMP::hasConf(
                $type.'_is_on_default_setting_export_product',
                null,
                $id_shop
            )
                ? (int)ConfSMP::getConf(
                    $type.'_is_on_default_setting_export_product',
                    null,
                    null,
                    $id_shop
                )
                : self::$default_params['is_on_default_setting_export_product']
            );

            $priority = (
                ConfSMP::hasConf($type.'_priority', null, $id_shop)
                ? ConfSMP::getConf($type.'_priority', null, null, $id_shop)
                : self::$default_params['priority']
            );
            $changefreq = (
                ConfSMP::hasConf($type.'_changefreq', null, $id_shop)
                ? ConfSMP::getConf($type.'_changefreq', null, $id_shop)
                : self::$default_params['changefreq']
            );
            $default_category = (
            ConfSMP::hasConf($type.'_default_category', null, $id_shop)
                ? ConfSMP::getConf($type.'_default_category', null, $id_shop)
                : self::$default_params['default_category']
            );

            $settings['is_export'] = $is_export;
            $settings['default_category'] = $default_category;
            $settings['priority'] = $priority;
            $settings['changefreq'] = $changefreq;
            $settings['is_changefreq'] = $is_changefreq;
            $settings['is_on_default_setting_category'] = $is_on_default_setting_category;
            $settings['is_on_default_setting_export_product'] = $is_on_default_setting_export_product;
        }
        if (isset($settings['priority'])) {
            if ($settings['priority'] == "1") {
                $settings['priority'] = "1.0";
            }
        }
        return $settings;
    }

    public static function setDefaultSettings($settings, $type = null)
    {
        $id_shop = Context::getContext()->shop->id;
        if (is_null($type)) {
            foreach (self::$types as $type) {
                self::setDefaultSettings(
                    (is_array($settings) && isset($settings[$type]) ? $settings[$type] : self::$default_params),
                    $type
                );
            }
        } else {
            ConfSMP::setConf(
                $type.'_is_export',
                (
                    isset($settings['is_export'])
                    ? $settings['is_export']
                    : 0
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_is_changefreq',
                (
                    isset($settings['is_changefreq'])
                    ? $settings['is_changefreq']
                    : 0
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_is_on_default_setting_category',
                (
                    isset($settings['is_on_default_setting_category'])
                    ? $settings['is_on_default_setting_category']
                    : 0
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_is_on_default_setting_export_product',
                (
                isset($settings['is_on_default_setting_export_product'])
                    ? $settings['is_on_default_setting_export_product']
                    : 0
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_priority',
                (
                isset($settings['priority'])
                    ? $settings['priority']
                    : self::$default_params['priority']
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_changefreq',
                (
                isset($settings['changefreq'])
                    ? $settings['changefreq']
                    : self::$default_params['changefreq']
                ),
                false,
                null,
                $id_shop
            );
            ConfSMP::setConf(
                $type.'_default_category',
                (
                isset($settings['default_category'])
                    ? $settings['default_category']
                    : self::$default_params['default_category']
                ),
                false,
                null,
                $id_shop
            );
        }
    }

    public static function getSitemapCategories()
    {
        $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'sitemap_category
        WHERE id_shop = '.(int)Context::getContext()->shop->id);
        $ids = array();
        if (is_array($result) && count($result)) {
            foreach ($result as $item) {
                $ids[] = (int)$item['id_category'];
            }
        }
        return $ids;
    }
}
