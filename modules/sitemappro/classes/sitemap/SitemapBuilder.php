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

class SitemapBuilder
{
    public $link;
    private static $instance = null;

    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.6.1.15', '<')) {
            $this->link = new LinkSMP();
        } else {
            $this->link = new LinkSMPPS7();
        }
    }

    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function generate($id_lang = null, $with_image = false, $with_link = false)
    {
        if ($with_link && is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $sitemap = new Sitemap(ToolsSMP::getShopDomain());
        if (version_compare(_PS_VERSION_, '1.5.6', '<')) {
            $sitemap = new Sitemap('');
        }
        $sitemap->setPath(_PS_ROOT_DIR_ . '/');
        if ($with_link) {
            $sitemap->setIncludeLinks(true);
        }
        $sitemap->setFilename(SitemapConfig::getSitemapFilename($id_lang, true, $with_image, $with_link));
        $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
        if (empty($page_qty)) {
            $page_qty = 10000;
        }
        $categories = array();
        $list = scandir($sitemap->getPath());
        $file_name = $sitemap->getFilename();
        foreach ($list as $namefile) {
            $pattern_name = '/^'. $file_name .'[-][0-9]+/';
            preg_match($pattern_name, $namefile, $result);
            if (!empty($result)) {
                unlink($sitemap->getPath() . $namefile);
            }
        }
        foreach (SitemapConfigCategory::getItems($id_lang, $with_link, $with_image) as $category) {
            if (in_array($category['id_category'], array(1, 2))) {
                $categories[$category['id_category']] = array(
                    'priority' => $category['priority'],
                    'changefreq' => $category['changefreq']
                );
                continue;
            }

            $link = $this->link->getCategoryLink(
                (int)$category['id_category'],
                $category['link_rewrite'],
                (int)$category['id_lang']
            );
            $links = array();
            foreach ($category['links'] as $l) {
                $iso_code = Language::getIsoById($l['id_lang']);
                if (!$iso_code) {
                    continue;
                }
                $links[$iso_code] = $this->link->getCategoryLink(
                    $l['id_category'],
                    $l['link_rewrite'],
                    (int)$l['id_lang']
                );
            }

            $images = array();
            if (ConfSMP::getConf('EXPORT_CATEGORY_IMAGE')) {
                foreach ($category['images'] as $image) {
                    $image = array(
                        'loc' => $image,
                        'title' => $category['name']
                    );
                    $images[] = $image;
                }
            }

            $date = 'Today';
            if ($category['date_upd'] != '0000-00-00 00:00:00') {
                $date = date('d-m-Y', strtotime($category['date_upd']));
            }

            $categories[$category['id_category']] = array(
                'priority' => $category['priority'],
                'changefreq' => $category['changefreq']
            );
            if (!$category['is_export']) {
                continue;
            }

            $sitemap->addItem(
                $link,
                Tools::ps_round($category['priority'], 1),
                $category['changefreq'],
                $date,
                $images,
                $links,
                false,
                $page_qty
            );
            if ($page_qty == 0) {
                $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
            } else {
                $page_qty = $page_qty - 1;
            }
        }

        $product_default_settings = SitemapConfig::getDefaultSettings('product');
        $excl_products = json_decode(Configuration::get('SEOSA_SITEMAP_EXCLUDED_PRODUCTS'));
        $with_link = (int)ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE');

        if ((strpos($sitemap->filename, 'sitemap-with-alternate-links') !== false)
            or (strpos($sitemap->filename, 'sitemap-with-images-with-alternate-links') !== false)) {
            $lang_mass = Language::getLanguages(true, false, false);
        } else {
            if (is_null($id_lang)) {
                $id_lang = Configuration::get('PS_LANG_DEFAULT');
            }
            $lang_mass[] = Language::getLanguage($id_lang);
        }
        foreach ($lang_mass as $item) {
            $id_lang = $item['id_lang'];

            foreach (SitemapConfigProduct::getItems($id_lang, $with_link, $with_image) as $product) {
                if (is_array($excl_products) && in_array($product['id_product'], $excl_products)) {
                    continue;
                }
                if ($product_default_settings['is_on_default_setting_export_product'] == 0) {
                    continue;
                }

                $combinations = array(null);
                if (ConfSMP::getConf('EXPORT_COMBINATION')) {
                    $combinations = Product::getProductAttributesIds($product['id_product']);
                    if (!count($combinations)) {
                        $combinations = array(null);
                    }
                }

                if (ConfSMP::getConf('EXPORT_COMBINATION_DEF') == 1) {
                    $combinations[]['id_product_attribute'] = Product::getDefaultAttribute($product['id_product']);
                    if (!count($combinations)) {
                        $combinations = array(null);
                    }
                }

//            foreach ($combinations as $combination) {
//                if ($combination != null) {
//                    $anchor = $this->getAnchor(
//                        $product['id_product'],
//                        $combination['id_product_attribute'],
//                        (int)ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE')
//                    );
//                    $link = $this->link->getProductLink(
//                        $product['id_product'],
//                        $product['link_rewrite'],
//                        null,
//                        null,
//                        $product['id_lang'],
//                        null,
//                        $combination['id_product_attribute']
//                    );
//                } else {
//                    $anchor = '';
//                    $combination = Product::getDefaultAttribute($product['id_product']);
//                    $link = $this->link->getProductLink(
//                        $product['id_product'],
//                        $product['link_rewrite'],
//                        null,
//                        null,
//                        $product['id_lang'],
//                        null,
//                        $combination
//                    );
//                }
//
////
////                $link = $this->link->getProductLink(
////                    $product['id_product'],
////                    $product['link_rewrite'],
////                    null,
////                    null,
////                    $product['id_lang'],
////                    null,
////                    $combination['id_product_attribute']
////                );
//
//                $links = array();
//                foreach ($product['links'] as $l) {
//                    $iso_code = Language::getIsoById($l['id_lang']);
//                    if (!$iso_code) {
//                        continue;
//                    }
//                    $links[$iso_code] = $this->link->getProductLink(
//                            $l['id_product'],
//                            $l['link_rewrite'],
//                            null,
//                            null,
//                            $l['id_lang'],
//                            null
//                        ) . $anchor;
//                }

                foreach ($combinations as $combination) {
                    if ($combination == null && ConfSMP::getConf('EXPORT_COMBINATION_DEF') == 1) {
                        continue;
                    }
                    if ($combination != null) {
                        $anchor = $this->getAnchor(
                            $product['id_product'],
                            $combination['id_product_attribute'],
                            (int)ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE')
                        );

                        if (ConfSMP::getConf('EXPORT_COMBINATION_DEF') == 1) {
                            $link = $this->link->getProductLink(
                                $product['id_product'],
                                $product['link_rewrite'],
                                null,
                                null,
                                $product['id_lang'],
                                null,
                                $combination['id_product_attribute']
                            );
                        }
                    } else {
                        $anchor = '';
                        if (ConfSMP::getConf('EXPORT_COMBINATION_DEF') == 0) {
                            $def_atr = Product::getDefaultAttribute($product['id_product']);
                            $link = $this->link->getProductLink(
                                $product['id_product'],
                                $product['link_rewrite'],
                                null,
                                null,
                                $product['id_lang'],
                                null,
                                $def_atr
                            );
                        } else {
                            continue;
                        }
                    }

                    if (ConfSMP::getConf('EXPORT_COMBINATION_DEF') == 0) {
                        if (!empty($combination['id_product_attribute'])) {
                            $link = $this->link->getProductLink(
                                $product['id_product'],
                                $product['link_rewrite'],
                                null,
                                null,
                                $product['id_lang'],
                                null,
                                $combination['id_product_attribute']
                            );
                        }
                    }


                    $links = array();
                    foreach ($product['links'] as $l) {
                        $iso_code = Language::getIsoById($l['id_lang']);
                        if (!$iso_code) {
                            continue;
                        }
                        if ($with_image == 0) {
                            $temp = null;
                        } else {
                            $temp = $combination['id_product_attribute'];
                        }
                        $links[$iso_code] = $this->link->getProductLink(
                            $l['id_product'],
                            $l['link_rewrite'],
                            null,
                            null,
                            $l['id_lang'],
                            null,
                            $temp
                        ) . $anchor;
                    }
                    if ((int)ConfSMP::getConf('INCLUDE_ID_IN_ATTRIBUTE')) {
                        $iso_code = Language::getIsoById($product['id_lang']);
                        if ($with_image == 0) {
                            $r = explode("#/", $link);
                            $r2 = explode("#/", $links[$iso_code]);
                            if (!empty($r2[1])) {
                                $link = $r[0] . '#/' . $r2[1];
                            }
                        } else {
                            $r = explode("#/", $link);
                            $r2 = explode("#/", $links[$iso_code]);
                            if (!empty($r2[2])) {
                                $link = $r[0] . '#/' . $r2[2];
                            }
                        }
                    }
                    $simbol_legend = ConfSMP::getConf('SYMBOL_LEGEND');
                    $count_simbol = Tools::strlen($simbol_legend);

                    $images = array();
                    foreach ($product['images'] as $image) {
                        if ($simbol_legend && Tools::substr($image['legend'], $count_simbol * -1) == $simbol_legend) {
                            continue;
                        }
                        $thickbox_default = _PS_VERSION_ < 1.7 ? 'thickbox' : 'large';
                        $thickbox_default .= '_default';

                        $image = array(
                            'loc' => $this->link->getImageLink(
                                $product['link_rewrite'],
                                $image['id_image'],
                                $thickbox_default
                            ),
                            'title' => $image['legend']
                        );
                        if (ConfSMP::getConf('ALLOW_IMAGE_CAPTION_ATTR')) {
                            $image['caption'] = strip_tags(html_entity_decode($product['description_short']));
                        }

                        $images[] = $image;
                    }

                    if ($product_default_settings['is_on_default_setting_category']) {
                        $default = (
                        isset($categories[$product['id_category_default']])
                            ? $categories[$product['id_category_default']]
                            : (
                        isset($categories[$product['id_category']])
                            ? $categories[$product['id_category']]
                            : array(
                            'priority' => 0.5,
                            'changefreq' => 'always'
                            )
                        )
                        );
                    } else {
                        $default = array(
                            'priority' => $product_default_settings['priority'],
                            'changefreq' => $product_default_settings['changefreq']
                        );
                    }

                    $date = 'Today';
                    if ($product['date_upd'] != '0000-00-00 00:00:00') {
                        $date = date('d-m-Y', strtotime($product['date_upd']));
                    }

                    $priority = (empty($product['priority']) ? $default['priority'] : $product['priority']);
                    $changefreq = (empty($product['changefreq']) ? $default['changefreq'] : $product['changefreq']);

                    if (!$product_default_settings['is_changefreq']) {
                        $changefreq = null;
                    }

                    $sitemap->addItem(
                        $link,
                        $priority,
                        $changefreq,
                        $date,
                        $images,
                        $links,
                        false,
                        $page_qty
                    );
                    if ($page_qty == 0) {
                        $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                    } else {
                        $page_qty = $page_qty - 1;
                    }
                }
            }

            $cms_default_settings = SitemapConfig::getDefaultSettings('cms');
            foreach (SitemapConfigCms::getItems($id_lang, $with_link) as $cms_page) {
                $link = $this->link->getCMSLink($cms_page['id_cms'], null, null, $cms_page['id_lang']);
                $links = array();
                foreach ($cms_page['links'] as $l) {
                    $iso_code = Language::getIsoById($l['id_lang']);
                    if (!$iso_code) {
                        continue;
                    }
                    $links[$iso_code] = $this->link->getCMSLink($l['id_cms'], null, null, $l['id_lang']);
                }

                $sitemap->addItem(
                    $link,
                    Tools::ps_round($cms_page['priority'], 1),
                    ($cms_default_settings['is_changefreq'] ? $cms_page['changefreq'] : null),
                    'Today',
                    array(),
                    $links,
                    false,
                    $page_qty
                );
                if ($page_qty == 0) {
                    $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                } else {
                    $page_qty = $page_qty - 1;
                }
            }

            foreach (SitemapConfigManufacturer::getItems($id_lang, $with_link) as $manufacturer) {
                $link = $this->link->getManufacturerLink(
                    $manufacturer['id_manufacturer'],
                    null,
                    $manufacturer['id_lang']
                );
                $links = array();
                foreach ($manufacturer['links'] as $l) {
                    $iso_code = Language::getIsoById($l['id_lang']);
                    if (!$iso_code) {
                        continue;
                    }
                    $links[$iso_code] = $this->link->getManufacturerLink(
                        $l['id_manufacturer'],
                        null,
                        $l['id_lang']
                    );
                }

                $date = 'Today';
                if ($manufacturer['date_upd'] != '0000-00-00 00:00:00') {
                    $date = date('d-m-Y', strtotime($manufacturer['date_upd']));
                }

                $sitemap->addItem(
                    $link,
                    Tools::ps_round($manufacturer['priority'], 1),
                    $manufacturer['changefreq'],
                    $date,
                    array(),
                    $links,
                    false,
                    $page_qty
                );
                if ($page_qty == 0) {
                    $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                } else {
                    $page_qty = $page_qty - 1;
                }
            }

            foreach (SitemapConfigSupplier::getItems($id_lang, $with_link) as $supplier) {
                $link = $this->link->getSupplierLink(
                    $supplier['id_supplier'],
                    null,
                    $supplier['id_lang']
                );
                $links = array();
                foreach ($supplier['links'] as $l) {
                    $iso_code = Language::getIsoById($l['id_lang']);
                    if (!$iso_code) {
                        continue;
                    }
                    $links[$iso_code] = $this->link->getSupplierLink(
                        $l['id_supplier'],
                        null,
                        $l['id_lang']
                    );
                }

                $date = 'Today';
                if ($supplier['date_upd'] != '0000-00-00 00:00:00') {
                    $date = date('d-m-Y', strtotime($supplier['date_upd']));
                }

                $sitemap->addItem(
                    $link,
                    Tools::ps_round($supplier['priority'], 1),
                    $supplier['changefreq'],
                    $date,
                    array(),
                    $links,
                    false,
                    $page_qty
                );
                if ($page_qty == 0) {
                    $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                } else {
                    $page_qty = $page_qty - 1;
                }
            }

            $languages = Language::getLanguages(true);
            $meta_default_settings = SitemapConfig::getDefaultSettings('meta');
            foreach (SitemapConfigMeta::getItems($id_lang, $with_link) as $page) {
                if ($page['page'] != 'index') {
                    $link = $this->link->getPageLink($page['page'], null, $page['id_lang']);
                } else {
                    $link = '';
                    if (count($languages)) {
                        $iso_code = Language::getIsoById($page['id_lang']);
                        $link = '/' . $iso_code . '/';
                    }
                }
                $links = array();
                if ($page['page'] != 'index') {
                    foreach ($page['links'] as $l) {
                        $iso_code = Language::getIsoById($l['id_lang']);
                        if (!$iso_code) {
                            continue;
                        }
                        $links[$iso_code] = $this->link->getPageLink(
                            $l['page'],
                            null,
                            $l['id_lang']
                        );
                    }
                }

                $changefreq = $page['changefreq'];
                if (!$meta_default_settings['is_changefreq']) {
                    $changefreq = null;
                }

                $sitemap->addItem(
                    $link,
                    Tools::ps_round($page['priority'], 1),
                    $changefreq,
                    'Today',
                    array(),
                    $links,
                    false,
                    $page_qty
                );
                if ($page_qty == 0) {
                    $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                } else {
                    $page_qty = $page_qty - 1;
                }
            }

            $user_link_default_settings = SitemapConfig::getDefaultSettings('user_link');
            foreach (UserLink::getAll($id_lang, $with_link) as $user_link) {
                $changefreq = $user_link['changefreq'];
                if (!$user_link_default_settings['is_changefreq']) {
                    $changefreq = null;
                }

                $sitemap->addItem(
                    $user_link['link'],
                    Tools::ps_round($user_link['priority'], 1),
                    $changefreq,
                    'Today',
                    array(),
                    $user_link['links'],
                    false,
                    $page_qty
                );
                if ($page_qty == 0) {
                    $page_qty = (int)ConfSMP::getConf('ITEM_PER_SITEMAP');
                } else {
                    $page_qty = $page_qty - 1;
                }
            }
        }
        $sitemap->createSitemapIndex(ToolsSMP::getShopDomainWithBase());
    }

    public static function getAttributesParams($id_product, $id_product_attribute)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $cache_id = 'Product::getAttributesParams_' . (int)$id_product . '-'
            . (int)$id_product_attribute . '-' . (int)$id_lang . '-' . (int)$id_shop;

        // if blocklayered module is installed we check if user has set custom attribute name
        if (Module::isInstalled('blocklayered') && Module::isEnabled('blocklayered')) {
            $nb_custom_values = Db::getInstance()->executeS('
			SELECT DISTINCT la.`id_attribute`, la.`url_name` as `name`
			FROM `' . _DB_PREFIX_ . 'attribute` a
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				ON (a.`id_attribute` = pac.`id_attribute`)
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
				ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'layered_indexable_attribute_lang_value` la
				ON (la.`id_attribute` = a.`id_attribute` AND la.`id_lang` = ' . (int)$id_lang . ')
			WHERE la.`url_name` IS NOT NULL AND la.`url_name` != \'\'
			AND pa.`id_product` = ' . (int)$id_product . '
			AND pac.`id_product_attribute` = ' . (int)$id_product_attribute);

            if (!empty($nb_custom_values)) {
                $tab_id_attribute = array();
                foreach ($nb_custom_values as $attribute) {
                    $result = array();
                    $tab_id_attribute[] = $attribute['id_attribute'];

                    $group = Db::getInstance()->executeS(
                        'SELECT a.`id_attribute`, g.`id_attribute_group`, g.`url_name` as `group`
                        FROM `' . _DB_PREFIX_ . 'layered_indexable_attribute_group_lang_value` g
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
                            ON (a.`id_attribute_group` = g.`id_attribute_group`)
                        WHERE a.`id_attribute` = ' . (int)$attribute['id_attribute'] . '
                        AND g.`id_lang` = ' . (int)$id_lang . '
                        AND g.`url_name` IS NOT NULL AND g.`url_name` != \'\''
                    );
                    if (empty($group)) {
                        $group = Db::getInstance()->executeS(
                            'SELECT g.`id_attribute_group`, g.`name` as `group`
                            FROM `' . _DB_PREFIX_ . 'attribute_group_lang` g
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
                                ON (a.`id_attribute_group` = g.`id_attribute_group`)
                            WHERE a.`id_attribute` = ' . (int)$attribute['id_attribute'] . '
                            AND g.`id_lang` = ' . (int)$id_lang . '
                            AND g.`name` IS NOT NULL'
                        );
                    }
                    $result[] = array_merge($attribute, $group[0]);
                }
                $values_not_custom = Db::getInstance()->executeS(
                    'SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`
                    FROM `' . _DB_PREFIX_ . 'attribute` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                        ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                        ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        ON (a.`id_attribute` = pac.`id_attribute`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                        ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    WHERE pa.`id_product` = ' . (int)$id_product . '
                    AND pac.id_product_attribute = ' . (int)$id_product_attribute . '
                    AND a.`id_attribute` NOT IN(' . implode(', ', array_map('pSQL', $tab_id_attribute)) . ')'
                );
                return array_merge($values_not_custom, $result);
            }
        }

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`
                FROM `' . _DB_PREFIX_ . 'attribute` a
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                    ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = ' . (int)$id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                    ON (pac.`id_attribute` = a.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                    ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                    ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)$id_lang . ')
                WHERE pa.`id_product` = ' . (int)$id_product . '
                    AND pac.`id_product_attribute` = ' . (int)$id_product_attribute . '
				AND agl.`id_lang` = ' . (int)$id_lang
            );
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }
        return $result;
    }

    /**
     * Get the combination url anchor of the product
     *
     * @param int $id_product_attribute
     * @return string
     */
    public function getAnchor($id_product, $id_product_attribute, $with_id = false)
    {
        $attributes = self::getAttributesParams($id_product, $id_product_attribute);
        $anchor = '#';
        $sep = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        foreach ($attributes as &$a) {
            foreach ($a as &$b) {
                $b = str_replace($sep, '_', Tools::link_rewrite($b));
            }
            $anchor .= '/' . ($with_id && isset($a['id_attribute'])
                && $a['id_attribute'] ? (int)$a['id_attribute'] . $sep : '')
                . $a['group'] . $sep . $a['name'];
        }
        return $anchor;
    }
}
