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

class SitemapConfigMeta extends SitemapConfig
{
    protected static function getConfigSql()
    {
        $sql = parent::getConfigSql();

        return $sql;
    }

    public static function getItems($id_lang = null, $include_link = false)
    {
        $pages = Meta::getPages();
        $tmp_pages = array();
        $config = self::getConfig();
        $default_settings = SitemapConfig::getDefaultSettings(self::getType());
        foreach (array_keys($pages) as $page) {
            if (!is_null($id_lang)) {
                $languages = array(
                    array(
                        'id_lang' => $id_lang
                    )
                );
            } else {
                $languages = ToolsModuleSMP::getLanguages(true);
            }

            foreach ($languages as $lang) {
                $meta = Meta::getMetaByPage($page, $lang['id_lang']);
                if (!$meta) {
                    continue;
                }

                if (array_key_exists($meta['id_meta'], $config) && !$config[$meta['id_meta']]['is_export']) {
                    continue;
                }

                if (array_key_exists($meta['id_meta'], $config)) {
                    $meta['priority'] = $config[$meta['id_meta']]['priority'];
                    $meta['changefreq'] = $config[$meta['id_meta']]['changefreq'];
                } else {
                    $meta['priority'] = $default_settings['priority'];
                    $meta['changefreq'] = $default_settings['changefreq'];
                }

                $meta['links'] = array();
                if ($include_link) {
                    foreach (ToolsModuleSMP::getLanguages(true) as $l) {
                        $meta['links'][] = array(
                            'id_lang' => $l['id_lang'],
                            'page' => $meta['page']
                        );
                    }
                }

                $tmp_pages[] = $meta;
            }
        }
        return $tmp_pages;
    }

    public static function getType()
    {
        return 'meta';
    }
}
