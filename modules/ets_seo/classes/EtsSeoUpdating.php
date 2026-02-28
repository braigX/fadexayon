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

class EtsSeoUpdating
{
    public function updateDuplicateProduct()
    {
        $products = Db::getInstance()->executeS(
            'SELECT link_rewrite,count(id_product) as count_id,min(id_product) as minid,id_shop,id_lang
                        FROM `' . _DB_PREFIX_ . 'product_lang`
                        GROUP BY id_shop,id_lang,link_rewrite
                        HAVING count_id>1'
        );
        if ($products) {
            foreach ($products as $product) {
                if ($product['link_rewrite']) {
                    try {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . "product_lang` 
                                        SET link_rewrite=CONCAT(link_rewrite, '-', id_product) 
                                        WHERE `link_rewrite`='" . pSQL((string) $product['link_rewrite']) . "' AND id_product != " . (int) $product['minid'] . ' AND `id_shop`=' . (int) $product['id_shop'] . ' AND `id_lang`=' . (int) $product['id_lang']
                        );
                    } catch (Exception $ex) {
                    }
                }
            }
        }
    }

    public function updateDuplicateCategory()
    {
        $results = Db::getInstance()->executeS(
            'SELECT link_rewrite,count(id_category) as count_id,min(id_category) as minid,id_shop,id_lang
                        FROM `' . _DB_PREFIX_ . 'category_lang`
                        GROUP BY id_shop,id_lang,link_rewrite
                        HAVING count_id>1'
        );
        if ($results) {
            foreach ($results as $item) {
                if ($item['link_rewrite']) {
                    try {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . "category_lang` 
                                        SET link_rewrite=CONCAT(link_rewrite, '-', id_category) 
                                        WHERE `link_rewrite`='" . pSQL((string) $item['link_rewrite']) . "' AND id_category != " . (int) $item['minid'] . ' AND `id_shop`=' . (int) $item['id_shop'] . ' AND `id_lang`=' . (int) $item['id_lang']
                        );
                    } catch (Exception $ex) {
                    }
                }
            }
        }
    }

    public function updateDuplicateCMS()
    {
        $results = Db::getInstance()->executeS(
            'SELECT link_rewrite,count(id_cms) as count_id,min(id_cms) as minid,id_shop,id_lang
                        FROM `' . _DB_PREFIX_ . 'cms_lang`
                        GROUP BY id_shop,id_lang,link_rewrite
                        HAVING count_id>1'
        );
        if ($results) {
            foreach ($results as $item) {
                if ($item['link_rewrite']) {
                    try {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . "cms_lang` 
                                        SET link_rewrite=CONCAT(link_rewrite, '-', id_cms) 
                                        WHERE `link_rewrite`='" . pSQL((string) $item['link_rewrite']) . "' AND id_cms != " . (int) $item['minid'] . ' AND `id_shop`=' . (int) $item['id_shop'] . ' AND `id_lang`=' . (int) $item['id_lang']
                        );
                    } catch (Exception $ex) {
                    }
                }
            }
        }
    }

    public function updateDuplicateCMSCategory()
    {
        $results = Db::getInstance()->executeS(
            'SELECT link_rewrite,count(id_cms_category) as count_id,min(id_cms_category) as minid,id_shop,id_lang
                        FROM `' . _DB_PREFIX_ . 'cms_category_lang`
                        GROUP BY id_shop,id_lang,link_rewrite
                        HAVING count_id>1'
        );
        if ($results) {
            foreach ($results as $item) {
                if ($item['link_rewrite']) {
                    try {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . "cms_category_lang` 
                                        SET link_rewrite=CONCAT(link_rewrite, '-', id_cms_category) 
                                        WHERE `link_rewrite`='" . pSQL((string) $item['link_rewrite']) . "' AND id_cms_category != " . (int) $item['minid'] . ' AND `id_shop`=' . (int) $item['id_shop'] . ' AND `id_lang`=' . (int) $item['id_lang']
                        );
                    } catch (Exception $ex) {
                    }
                }
            }
        }
    }

    public function updateDuplicateMeta()
    {
        $results = Db::getInstance()->executeS(
            'SELECT url_rewrite,count(id_meta) as count_id,min(id_meta) as minid,id_shop,id_lang
                        FROM `' . _DB_PREFIX_ . 'meta_lang`
                        GROUP BY id_shop,id_lang,url_rewrite
                        HAVING count_id>1'
        );
        if ($results) {
            foreach ($results as $item) {
                if ($item['url_rewrite']) {
                    try {
                        Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . "meta_lang` 
                                        SET url_rewrite=CONCAT(url_rewrite, '-', id_meta) 
                                        WHERE `url_rewrite`='" . pSQL((string) $item['url_rewrite']) . "' AND id_meta != " . (int) $item['minid'] . ' AND `id_shop`=' . (int) $item['id_shop'] . ' AND `id_lang`=' . (int) $item['id_lang']
                        );
                    } catch (Exception $ex) {
                    }
                }
            }
        }
    }

    public static function updateBaseUrlTheme($dir = null)
    {
        if (!$dir) {
            $dir = _PS_THEME_DIR_;
        }
        $files = glob(rtrim($dir, '/') . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::updateBaseUrlTheme($file);
            } elseif (is_file($file) && file_exists($file)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ('tpl' == $ext) {
                    $content = Tools::file_get_contents($file);
                    if (preg_match('/<a(.*)\s+href="\{(\$urls\.base_url)\}"([^>]*)>/', $content)) {
                        file_put_contents(dirname($file) . '/' . basename($file, '.tpl') . '.ets_seo_bak.tpl', $content);
                        $replacement = '{if isset($ets_seo_base_url) && $ets_seo_base_url}{$ets_seo_base_url}{else}{$urls.base_url}{/if}';
                        $content = preg_replace('/(<a(.*)\s+href=")(\{\$urls\.base_url\})("([^>]*)>)/', '$1' . $replacement . '$4', $content);
                        file_put_contents($file, $content);
                    }
                }
            }
        }
    }

    public static function deleteDataSocialImg($id, $type, $img, $is_cms_category = false)
    {
        $table = null;
        $id_col = '';
        switch ($type) {
            case 'AdminProducts':
                $table = 'ets_seo_product';
                $id_col = 'id_product';
                break;
            case 'AdminCmsContent':
                if ($is_cms_category) {
                    $table = 'ets_seo_cms_category';
                    $id_col = 'id_cms_category';
                } else {
                    $table = 'ets_seo_cms';
                    $id_col = 'id_cms';
                }
                break;
            case 'AdminMeta':
                $table = 'ets_seo_meta';
                $id_col = 'id_meta';
                break;
            case 'AdminCategories':
                $table = 'ets_seo_category';
                $id_col = 'id_category';
                break;
            case 'AdminSuppliers':
                $table = 'ets_seo_supplier';
                $id_col = 'id_supplier';
                break;
            case 'AdminManufacturers':
                $table = 'ets_seo_manufacturer';
                $id_col = 'id_manufacturer';
                break;
        }
        if ($table && $id) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . (string) $table . '` SET `social_img` = NULL WHERE ' . (string) $id_col . '=' . (int) $id . " AND `social_img`='" . pSQL((string) $img) . "'");
            if ('ets_seo_cms' == $table) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_seo_cms_category` SET `social_img` = NULL WHERE id_cms_category=' . (int) $id . " AND `social_img`='" . pSQL((string) $img) . "'");
            }

            return true;
        }

        return false;
    }

    public static function getSeoDatas($id_column, $table, $ids, $idLangs)
    {
        $allowCols = ['id_cms', 'id_meta', 'id_cms_category', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier'];
        $allowTables = ['ets_seo_cms', 'ets_seo_meta', 'ets_seo_cms_category', 'ets_seo_product', 'ets_seo_category', 'ets_seo_manufacturer', 'ets_seo_supplier'];
        if (in_array($id_column, $allowCols, true) && in_array($table, $allowTables, true)) {
            return Db::getInstance()->executeS(
                'SELECT esm.`' . (string) $id_column . '`, esm.`id_lang`, esm.`seo_score`, esm.`readability_score`, esm.`score_analysis`, lang.`name` as lang_name, lang.`iso_code`, esm.`key_phrase`, esm.`allow_search`, esm.`minor_key_phrase`, esm.`score_analysis`
                    FROM `' . _DB_PREFIX_ . (string) $table . '` esm 
                    JOIN `' . _DB_PREFIX_ . 'lang` lang ON esm.`id_lang` = lang.`id_lang`
                    WHERE esm.`' . (string) $id_column . '` IN (' . implode(',', array_map('intval', $ids)) . ') 
                    AND esm.`id_lang` IN (' . implode(',', array_map('intval', $idLangs)) . ')
                    AND esm.`id_shop` =' . (int) Ets_Seo::getContextStatic()->shop->id
            );
        }

        return [];
    }

    public static function updateAllSeoScores()
    {
        $tables = [
            'product' => 'ets_seo_product',
            'category' => 'ets_seo_category',
            'cms' => 'ets_seo_cms',
            'cms_category' => 'ets_seo_cms_category',
            'manufacturer' => 'ets_seo_manufacturer',
            'supplier' => 'ets_seo_supplier',
            'meta' => 'ets_seo_meta'
        ];

        foreach ($tables as $type => $table) {
            $id_column = 'id_' . $type;
            $records = Db::getInstance()->executeS('SELECT `' . $id_column . '`, `id_lang`, `score_analysis` FROM `' . _DB_PREFIX_ . $table . '` WHERE `score_analysis` IS NOT NULL AND `score_analysis` != ""');
            
            if ($records) {
                foreach ($records as $record) {
                    $scoreAnalysis = json_decode($record['score_analysis'], true);
                    if ($scoreAnalysis) {
                        $updated = false;
                        
                        // Recalculate SEO applicable rules
                         $applicable_seo_rules = 0;
                         if (isset($scoreAnalysis['seo_score']) && is_array($scoreAnalysis['seo_score'])) {
                            foreach ($scoreAnalysis['seo_score'] as $rule => $score) {
                                if ((int)$score != -999) {
                                    $applicable_seo_rules++;
                                }
                            }
                            if (!isset($scoreAnalysis['total_applicable_seo_rules']) || $scoreAnalysis['total_applicable_seo_rules'] != $applicable_seo_rules) {
                                $scoreAnalysis['total_applicable_seo_rules'] = $applicable_seo_rules;
                                $updated = true;
                            }
                         }

                        // Recalculate Readability applicable rules
                         $applicable_readability_rules = 0;
                         if (isset($scoreAnalysis['readability_score']) && is_array($scoreAnalysis['readability_score'])) {
                            foreach ($scoreAnalysis['readability_score'] as $rule => $score) {
                                if ((int)$score != -999) {
                                    $applicable_readability_rules++;
                                }
                            }
                            if (!isset($scoreAnalysis['total_applicable_readability_rules']) || $scoreAnalysis['total_applicable_readability_rules'] != $applicable_readability_rules) {
                                $scoreAnalysis['total_applicable_readability_rules'] = $applicable_readability_rules;
                                $updated = true;
                            }
                         }
                         
                         if ($updated) {
                             Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . $table . '` SET `score_analysis` = "' . pSQL(json_encode($scoreAnalysis)) . '" WHERE `' . $id_column . '` = ' . (int)$record[$id_column] . ' AND `id_lang` = ' . (int)$record['id_lang']);
                         }
                    }
                }
            }
        }
        return true;
    }
}
