<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class EtsSeoGptTemplate
 *
 * @since 2.6.2
 *
 * @mixin \ObjectModelCore
 */
class EtsSeoGptTemplate extends ObjectModel
{
    const LANG_TABLE = 'ets_seo_gpt_template_lang';
    public $position;
    public $label;
    public $content;
    public $display_page;
    public static $definition = [
        'table' => 'ets_seo_gpt_template',
        'primary' => 'id_ets_seo_gpt_template',
        'multilang' => true,
        'fields' => [
            'position' => ['type' => self::TYPE_INT],
            'display_page' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'label' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
            'content' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];
    public static function countAllTemplates()
    {
        $query = (new \DbQuery())->select(sprintf('COUNT(`%s`)', bqSQL(self::$definition['primary'])))->from(bqSQL(self::$definition['table']));

        return (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    public static function countTemplateByDisplayPage($displayPage)
    {
        $query = (new \DbQuery())->select(sprintf('COUNT(`%s`)', bqSQL(self::$definition['primary'])))->from(bqSQL(self::$definition['table']));
        $query->where(sprintf('display_page = "%s"', pSQL($displayPage)));

        return (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @param string|null $page
     *
     * @return self[]
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function getAllTemplates($page = null)
    {
        $query = (new \DbQuery())->select(sprintf('t.`%s` as id', bqSQL(self::$definition['primary'])))->from(bqSQL(self::$definition['table']), 't');
        if ($page) {
            $query->where(sprintf('display_page = "%s"', pSQL($page)));
        }
        $rs = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $results = [];
        foreach ($rs as $r) {
            $results[] = new self($r['id']);
        }

        return $results;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateDisplayPage($value)
    {
        $allowPages = ['AdminProducts', 'AdminCategories', 'AdminCmsContent'];

        return in_array($value, $allowPages, true);
    }
}
