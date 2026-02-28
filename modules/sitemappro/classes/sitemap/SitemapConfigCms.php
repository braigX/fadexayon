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

class SitemapConfigCms extends SitemapConfig
{
    protected static function getConfigSql()
    {
        $sql = parent::getConfigSql();
        $sql->select('cl.`meta_title`');
        $sql->leftJoin(
            'cms_lang',
            'cl',
            'cl.`id_cms` = a.`id_object` AND cl.`id_lang` = '
            .(int)Context::getContext()->language->id
        );

        return $sql;
    }

    protected static function getItemsSql($id_lang = null)
    {
        $sql = parent::getItemsSql($id_lang);
        $sql->select('c.`id_cms`');
        $sql->select('a.`priority`');
        $sql->select('a.`changefreq`');
        $sql->select('a.`is_export`');
        $sql->select('l.`id_lang`');
        $sql->from(self::$definition['table'], 'a');
        $sql->leftJoin('cms', 'c', 'c.`id_cms` = a.`id_object`');
        $sql->innerJoin(
            'cms_lang',
            'l',
            'c.id_cms = l.id_cms
                '.(version_compare(_PS_VERSION_, '1.6.0.9', '>')
                ? ' AND l.id_shop = '.(int)Shop::getContextShopID() : '')
            .(!is_null($id_lang) ? ' AND l.id_lang = '.(int)$id_lang : '')
        );
        $sql->innerJoin(
            'cms_shop',
            'cs',
            'c.id_cms = cs.id_cms AND cs.id_shop = '.(int)Shop::getContextShopID()
        );
        $sql->where('c.active = 1');
        $sql->where('a.`type_object` = "'.pSQL(self::getType()).'"');
        $sql->where('a.`is_export` = 1');
        $sql->where((version_compare(_PS_VERSION_, '1.6.0.9', '>')
            ? 'l.id_shop = a.`id_shop`' : ''));
        $sql->where(
            'l.`id_lang` IN('.implode(
                ',',
                array_map('intval', ToolsModuleSMP::getLanguageIds())
            ).')'
        );
        $sql->orderBy('position');
        return $sql;
    }

    public static function getItems($id_lang = null, $include_link = false)
    {
        $items = parent::getItems($id_lang);
        $nb_languages = count(ToolsModuleSMP::getLanguages(true));

        foreach ($items as &$item) {
            if ($include_link && $nb_languages > 1) {
                $item['links'] = self::getLinks($item['id_cms']);
            } else {
                $item['links'] = array();
            }
        }
        return $items;
    }

    public static function getLinks($id_cms)
    {
        $sql = new DbQuery();
        $sql->select('c.`id_cms`');
        $sql->select('l.`id_lang`');
        $sql->from('cms', 'c');
        $sql->innerJoin(
            'cms_lang',
            'l',
            'c.id_cms = l.id_cms
            '.(version_compare(_PS_VERSION_, '1.6', '>=')
            ? ' AND l.id_shop = '.(int)Shop::getContextShopID() : '')
        );
        $sql->leftJoin(
            'lang',
            'lang',
            'lang.`id_lang` = l.`id_lang`'
        );
        $sql->innerJoin(
            'cms_shop',
            'cs',
            'c.id_cms = cs.id_cms AND cs.id_shop = '.(int)Shop::getContextShopID()
        );
        $sql->where('c.`id_cms` = '.(int)$id_cms);
        $sql->where('lang.`active` = 1');
        return Db::getInstance()->executeS($sql);
    }

    public static function getType()
    {
        return 'cms';
    }
}
