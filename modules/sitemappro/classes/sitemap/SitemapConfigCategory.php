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

class SitemapConfigCategory extends SitemapConfig
{
    protected static function getConfigSql()
    {
        $sql = parent::getConfigSql();
        $sql->select('CONCAT(cl.`id_category`, "| ", cl.`name`) as `name`');
        $sql->leftJoin('category', 'c', 'c.`id_category` = a.`id_object`');
        $sql->leftJoin('category_lang', 'cl', 'c.`id_category` = cl.`id_category` 
        AND cl.`id_lang` = '.(int)Context::getContext()->language->id);
        $sql->leftJoin('category_shop', 'cs', 'c.`id_category` = cs.`id_category`');
        $sql->where('cl.`id_shop` = '.(int)Shop::getContextShopID());
        $sql->where('cs.`id_shop` = '.(int)Shop::getContextShopID());
        return $sql;
    }

    protected static function getItemsSql($id_lang = null)
    {
        $sql = new DbQuery();
        $sql->select('c.`id_category`, cl.`name`, c.`date_upd`, cl.`link_rewrite`, cl.`id_lang`');
        $sql->from('sitemap_category', 'sc');
        $sql->leftJoin('category', 'c', 'sc.`id_category` = c.`id_category`');
        $sql->leftJoin('category_lang', 'cl', 'c.`id_category` = cl.`id_category` '
            .(!is_null($id_lang) ? 'AND cl.`id_lang` = '.(int)$id_lang : ''));
        $sql->leftJoin('category_shop', 'cs', 'c.`id_category` = cs.`id_category`');
        $sql->where('cl.`id_shop` = '.(int)Shop::getContextShopID()
            .' AND c.`active` = 1'
            .' AND cl.`id_lang` IN('.implode(',', array_map('intval', ToolsModuleSMP::getLanguageIds())).')');
        $sql->where('cs.`id_shop` = '.(int)Shop::getContextShopID());
        $sql->where('sc.`id_shop` = '.(int)Shop::getContextShopID());
        return $sql;
    }

    public static function getItems($id_lang = null, $include_link = false, $with_image = false)
    {
        $items = parent::getItems($id_lang);
        $config = self::getConfig();
        $nb_languages = count(ToolsModuleSMP::getLanguages(true));
        $default_settings = SitemapConfig::getDefaultSettings(self::getType());

        foreach ($items as &$item) {
            if (array_key_exists($item['id_category'], $config)) {
                $item['priority'] = $config[$item['id_category']]['priority'];
                $item['changefreq'] = $config[$item['id_category']]['changefreq'];
                $item['is_export'] = (int)$config[$item['id_category']]['is_export'];
            } else {
                $item['priority'] = $default_settings['priority'];
                $item['changefreq'] = $default_settings['changefreq'];
                $item['is_export'] = $default_settings['is_export'];
            }

            if (!$default_settings['is_changefreq']) {
                $item['changefreq'] = null;
            }

            if ($include_link && $nb_languages > 1) {
                $item['links'] = self::getLinks($item['id_category']);
            } else {
                $item['links'] = array();
            }

            if ($with_image) {
                $item['images'] = array();

                $image_link = 'c/'.$item['id_category'].'.jpg';
                if (file_exists(_PS_IMG_DIR_.$image_link)) {
                    $item['images'][] = ToolsSMP::getShopDomain()._PS_IMG_.$image_link;
                }
                $thumbs = glob(_PS_IMG_DIR_.'c/'.$item['id_category'].'-medium'.'_default.jpg');
                if (is_array($thumbs) && count($thumbs)) {
                    foreach ($thumbs as $thumb) {
                        $item['images'][] = ToolsSMP::getShopDomain()._PS_IMG_.'c/'.basename($thumb);
                    }
                }
            } else {
                $item['images'] = array();
            }
        }
        return $items;
    }

    public static function getLinks($id_category)
    {
        $links = Db::getInstance()->executeS(
            'SELECT cl.`id_category`, cl.`id_lang`, cl.`link_rewrite`
            FROM `'._DB_PREFIX_.'category_lang` cl
             LEFT JOIN '._DB_PREFIX_.'lang l ON l.`id_lang` = cl.`id_lang`
             WHERE cl.`id_category` = '.(int)$id_category
            .' AND l.`active` = 1'
        );
        return is_array($links) && count($links) ? $links : array();
    }

    public static function getType()
    {
        return 'category';
    }
}
