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

class SitemapConfigManufacturer extends SitemapConfig
{
    protected static function getConfigSql()
    {
        $sql = parent::getConfigSql();
        return $sql;
    }

    public static function getConfig()
    {
        $sql = self::getConfigSql();
        $sql->leftJoin(
            'manufacturer',
            'm',
            'm.`id_manufacturer` = a.`id_object`'
        );
        $sql->select('CONCAT(m.`id_manufacturer`, "| ", m.`name`) as `name`');

        $result = Db::getInstance()->executeS($sql->build());
        $conf = array();
        foreach ($result as $item) {
            $item['priority'] = Tools::ps_round($item['priority'], 1);
            $conf[$item['id_'.self::getType()]] = $item;
        }
        return $conf;
    }

    public static function getItems($id_lang = null, $include_link = false, $show_all = false)
    {
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang);
        $tmp_manufacturers = array();
        $config = self::getConfig();
        $default_settings = SitemapConfig::getDefaultSettings(self::getType());
        foreach ($manufacturers as $manufacturer) {
            if (array_key_exists($manufacturer['id_manufacturer'], $config)
                && !$config[$manufacturer['id_manufacturer']]['is_export']
                && !$show_all
            ) {
                continue;
            }
            if (!array_key_exists($manufacturer['id_manufacturer'], $config)
            && !$default_settings['is_export']) {
                continue;
            }

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
                if (array_key_exists($manufacturer['id_manufacturer'], $config)) {
                    $manufacturer['priority'] = $config[$manufacturer['id_manufacturer']]['priority'];
                    $manufacturer['changefreq'] = $config[$manufacturer['id_manufacturer']]['changefreq'];
                    $manufacturer['is_export'] = (int)$config[$manufacturer['id_manufacturer']]['is_export'];
                } else {
                    $manufacturer['priority'] = $default_settings['priority'];
                    $manufacturer['changefreq'] = $default_settings['changefreq'];
                    $manufacturer['is_export'] = $default_settings['is_export'];
                }

                if (!$default_settings['is_changefreq']) {
                    $manufacturer['changefreq'] = null;
                }

                $manufacturer['id_lang'] = $lang['id_lang'];

                $manufacturer['links'] = array();
                if ($include_link) {
                    foreach (ToolsModuleSMP::getLanguages(true) as $l) {
                        $manufacturer['links'][] = array(
                            'id_lang' => $l['id_lang'],
                            'id_manufacturer' => $manufacturer['id_manufacturer']
                        );
                    }
                }

                $tmp_manufacturers[] = $manufacturer;
            }
        }
        return $tmp_manufacturers;
    }

    public static function getType()
    {
        return 'manufacturer';
    }
}
