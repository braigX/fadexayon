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

class SitemapConfigSupplier extends SitemapConfig
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
            'supplier',
            's',
            's.`id_supplier` = a.`id_object`'
        );
        $sql->select('CONCAT(s.`id_supplier`, "| ", s.`name`) as `name`');

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
        $suppliers = Supplier::getSuppliers(false, $id_lang);
        $tmp_suppliers = array();
        $config = self::getConfig();
        $default_settings = SitemapConfig::getDefaultSettings(self::getType());
        foreach ($suppliers as $supplier) {
            if (array_key_exists($supplier['id_supplier'], $config)
                && !$config[$supplier['id_supplier']]['is_export']
                && !$show_all
            ) {
                continue;
            }
            if (!array_key_exists($supplier['id_supplier'], $config)
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
                if (array_key_exists($supplier['id_supplier'], $config)) {
                    $supplier['priority'] = $config[$supplier['id_supplier']]['priority'];
                    $supplier['changefreq'] = $config[$supplier['id_supplier']]['changefreq'];
                    $supplier['is_export'] = (int)$config[$supplier['id_supplier']]['is_export'];
                } else {
                    $supplier['priority'] = $default_settings['priority'];
                    $supplier['changefreq'] = $default_settings['changefreq'];
                    $supplier['is_export'] = $default_settings['is_export'];
                }

                if (!$default_settings['is_changefreq']) {
                    $supplier['changefreq'] = null;
                }

                $supplier['id_lang'] = $lang['id_lang'];

                $supplier['links'] = array();
                if ($include_link) {
                    foreach (ToolsModuleSMP::getLanguages(true) as $l) {
                        $supplier['links'][] = array(
                            'id_lang' => $l['id_lang'],
                            'id_supplier' => $supplier['id_supplier']
                        );
                    }
                }

                $tmp_suppliers[] = $supplier;
            }
        }
        return $tmp_suppliers;
    }

    public static function getType()
    {
        return 'supplier';
    }
}
