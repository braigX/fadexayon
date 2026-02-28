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
if (!class_exists('EtsSeoUpdating')) {
    require_once __DIR__ . '/EtsSeoUpdating.php';
}
if (!class_exists('EtsSeoStrHelper')) {
    require_once __DIR__ . '/utils/EtsSeoStrHelper.php';
}
require_once __DIR__ . '/traits/EtsSeoTranslationTrait.php';
require_once __DIR__ . '/traits/EtsSeoGetModuleTrait.php';
require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';

class EtsSeoAnalysis
{
    use EtsSeoTranslationTrait;
    use EtsSeoGetModuleTrait;
    use EtsSeoGetInstanceTrait;
    /**
     * @var \Context|\ContextCore
     */
    protected $context;

    public function __construct()
    {
        $this->context = Ets_Seo::getContextStatic();
    }

    public function analysisPages($pages = [])
    {
        $limit = 10;
        $pageType = $pages[0];

        $defaultData = [
            'id' => 0,
            'id_lang' => 0,
            'name' => null,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'description' => null,
            'description_short' => null,
            'key_phrase'=>null,
            'minor_key_phrase'=>null,
            'link_rewrite'=>null,
        ];
        switch ($pageType) {
            case 'product':
                $datas = Db::getInstance()->executeS('SELECT b.key_phrase, b.minor_key_phrase, a.id_product as `id`, a.id_lang as id_lang, a.name as name, a.meta_title, a.meta_description, a.description, a.description_short, a.link_rewrite
                        FROM `' . _DB_PREFIX_ . 'product_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_product` b ON (a.id_product = b.id_product AND a.id_lang = b.id_lang )
                        WHERE b.id_product IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'category':
                $datas = Db::getInstance()->executeS("SELECT b.key_phrase, b.minor_key_phrase, a.id_category as `id`, a.id_lang as id_lang, a.name as name, a.meta_title, a.meta_description, a.description, '' as description_short, a.link_rewrite
                        FROM `" . _DB_PREFIX_ . 'category_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_category` b ON (a.id_category = b.id_category AND a.id_lang = b.id_lang)
                        WHERE b.id_category IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'cms':
                $datas = Db::getInstance()->executeS("SELECT b.key_phrase, b.minor_key_phrase, a.id_cms as `id`, a.id_lang as id_lang, a.meta_title as `name`, a.head_seo_title as meta_title, a.meta_description, a.content as description, '' as description_short, a.link_rewrite
                        FROM `" . _DB_PREFIX_ . 'cms_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms` b ON (a.id_cms = b.id_cms AND a.id_lang = b.id_lang)
                        WHERE b.id_cms IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'cms_category':
                $datas = Db::getInstance()->executeS("SELECT b.key_phrase, b.minor_key_phrase, a.id_cms_category as `id`, a.id_lang as id_lang, a.name, a.meta_title, a.meta_description, a.description, '' as description_short, a.link_rewrite
                        FROM `" . _DB_PREFIX_ . 'cms_category_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms_category` b ON (a.id_cms_category = b.id_cms_category AND a.id_lang = b.id_lang)
                        WHERE b.id_cms_category IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'manufacturer':
                $datas = Db::getInstance()->executeS('SELECT b.key_phrase, b.minor_key_phrase, a.id_manufacturer as `id`, a.id_lang as id_lang, m.name, a.meta_title, a.meta_description, a.description, a.short_description as description_short, COALESCE(mu.link_rewrite, \'\') as link_rewrite
                        FROM `' . _DB_PREFIX_ . 'manufacturer_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.id_manufacturer = a.id_manufacturer
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_manufacturer` b ON (a.id_manufacturer = b.id_manufacturer AND a.id_lang = b.id_lang)
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_manufacturer_url` mu ON mu.id_manufacturer = a.id_manufacturer
                        WHERE b.id_manufacturer IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'supplier':
                $datas = Db::getInstance()->executeS("SELECT b.key_phrase, b.minor_key_phrase, a.id_supplier as `id`, a.id_lang as id_lang, s.name, a.meta_title, a.meta_description, a.description, '' as description_short, COALESCE(su.link_rewrite, '') as link_rewrite
                        FROM `" . _DB_PREFIX_ . 'supplier_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON a.id_supplier = s.id_supplier
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_supplier` b ON (a.id_supplier = b.id_supplier AND a.id_lang = b.id_lang)
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_supplier_url` su ON su.id_supplier = a.id_supplier
                        WHERE b.id_supplier IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            case 'meta':
                $datas = Db::getInstance()->executeS("SELECT b.key_phrase, b.minor_key_phrase, a.id_meta as `id`, a.id_lang as id_lang, a.title as `name`, '' as meta_title, '' as meta_description, a.description, '' as description_short, a.url_rewrite as link_rewrite
                        FROM `" . _DB_PREFIX_ . 'meta_lang` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_meta` b ON (a.id_meta = b.id_meta AND a.id_lang = b.id_lang)
                        WHERE b.id_meta IS NULL OR b.seo_score = 0 LIMIT ' . (int) $limit);
                break;
            default:
                $datas = [];
        }

        if (!$datas && count($pages) > 1) {
            if (($keyPage = array_search($pageType, $pages)) !== false) {
                array_splice($pages, $keyPage, 1);

                return $this->analysisPages($pages);
            }
        }
        if ($datas) {
            $results = [];
            foreach ($datas as $item) {
                $results[] = array_merge($defaultData, $item);
            }
            $datas = $results;
        }

        return [
            'data' => $datas,
            'page_type' => $pageType,
            'stop' => !$datas || !$pages ? 1 : 0,
        ];
    }

    public function updateDataAnalysis($pageType, $scoreData)
    {
        $tblName = '';
        $idCol = '';
        switch ($pageType) {
            case 'product':
                $tblName = 'ets_seo_product';
                $idCol = 'id_product';
                break;
            case 'category':
                $tblName = 'ets_seo_category';
                $idCol = 'id_category';
                break;
            case 'cms':
                $tblName = 'ets_seo_cms';
                $idCol = 'id_cms';
                break;
            case 'cms_category':
                $tblName = 'ets_seo_cms_category';
                $idCol = 'id_cms_category';
                break;
            case 'manufacturer':
                $tblName = 'ets_seo_manufacturer';
                $idCol = 'id_manufacturer';
                break;
            case 'supplier':
                $tblName = 'ets_seo_supplier';
                $idCol = 'id_supplier';
                break;
            case 'meta':
                $tblName = 'ets_seo_meta';
                $idCol = 'id_meta';
                break;
        }

        if ($tblName) {
            foreach ($scoreData as $scoreItem) {
                // Count applicable rules from score detail if available
                $applicable_seo_rules = 0;
                $applicable_readability_rules = 0;
                $scoreAnalysisData = [
                    'seo_score' => [],
                    'readability_score' => []
                ];

                if (isset($scoreItem['score_detail'])) {
                    // Count SEO rules
                    if (isset($scoreItem['score_detail']['seo']) && is_array($scoreItem['score_detail']['seo'])) {
                        foreach ($scoreItem['score_detail']['seo'] as $rule => $score) {
                            $scoreAnalysisData['seo_score'][$rule] = $score;
                            if ((int)$score != -999) {
                                $applicable_seo_rules++;
                            }
                        }
                    }

                    // Count Readability rules
                    if (isset($scoreItem['score_detail']['readability']) && is_array($scoreItem['score_detail']['readability'])) {
                        foreach ($scoreItem['score_detail']['readability'] as $rule => $score) {
                            $scoreAnalysisData['readability_score'][$rule] = $score;
                            if ((int)$score != -999) {
                                $applicable_readability_rules++;
                            }
                        }
                    }
                }

                // Add total applicable rules count
                $scoreAnalysisData['total_applicable_seo_rules'] = $applicable_seo_rules;
                $scoreAnalysisData['total_applicable_readability_rules'] = $applicable_readability_rules;

                // Encode score_analysis as JSON
                $scoreAnalysisJson = json_encode($scoreAnalysisData);

                // Encode content_analysis as JSON if available
                $contentAnalysisJson = isset($scoreItem['content_analysis'])
                    ? json_encode($scoreItem['content_analysis'])
                    : '{}';

                // Check if record already exists
                $idShop = (int) $this->context->shop->id;
                $idLang = (int) $scoreItem['id_lang'];
                $id = (int) $scoreItem['id'];

                // Get primary key name for the table (format: id_ets_seo_{table_name})
                $primaryKey = 'id_' . $tblName;

                $existingId = (int) Db::getInstance()->getValue('SELECT `' . bqSQL($primaryKey) . '` 
                    FROM `' . _DB_PREFIX_ . (string) $tblName . '` 
                    WHERE `' . (string) $idCol . '` = ' . $id . ' 
                    AND `id_lang` = ' . $idLang . ' 
                    AND `id_shop` = ' . $idShop);

                if ($existingId) {
                    // Update existing record
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . (string) $tblName . '` 
                        SET `seo_score` = ' . ((int) $scoreItem['score']['seo'] > 0 ? (int) $scoreItem['score']['seo'] : 1) . ',
                            `readability_score` = ' . ((int) $scoreItem['score']['readability'] > 0 ? (int) $scoreItem['score']['readability'] : 1) . ',
                            `score_analysis` = "' . pSQL($scoreAnalysisJson) . '",
                            `content_analysis` = "' . pSQL($contentAnalysisJson) . '",
                            `allow_flw_link` = 1,
                            `allow_search` = 2
                        WHERE `' . bqSQL($primaryKey) . '` = ' . $existingId);
                } else {
                    // Insert new record
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . (string) $tblName . '` 
                        (`' . (string) $idCol . '`, `id_lang`, `id_shop`, `seo_score`, `readability_score`, `score_analysis`, `content_analysis`, `allow_flw_link`, `allow_search`) 
                        VALUES(
                            ' . $id . ',
                            ' . $idLang . ', 
                            ' . $idShop . ', 
                            ' . ((int) $scoreItem['score']['seo'] > 0 ? (int) $scoreItem['score']['seo'] : 1) . ',
                            ' . ((int) $scoreItem['score']['readability'] > 0 ? (int) $scoreItem['score']['readability'] : 1) . ',
                            "' . pSQL($scoreAnalysisJson) . '",
                            "' . pSQL($contentAnalysisJson) . '",
                            1, 
                            2
                        )');
                }
            }

            return true;
        }

        return false;
    }

    public static function getTotalIndexFollow()
    {
        $sqlOverAllSeoScore = ' (b.`seo_score` / (' . ETS_TOTAL_SEO_RULE_SCORE . ' * 9) * 10)';
        $sqlOverAllReadabilityScore = ' (b.`readability_score` / (' . ETS_TOTAL_READABILITY_RULE_SCORE . ' * 9) * 10)';
        $languages = Language::getLanguages(true);
        $firstLangId = isset($languages[0]['id_lang']) ? $languages[0]['id_lang'] : Configuration::get('PS_LANG_DEFAULT');
        $sql = [];
        $sql['product'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0 THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'product` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_product` b ON a.id_product = b.id_product AND b.id_lang=' . (int) $firstLangId;
        $sql['category'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL  THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'category` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_category` b ON a.id_category = b.id_category AND b.id_lang=' . (int) $firstLangId;
        $sql['cms'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'cms` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms` b ON a.id_cms = b.id_cms AND b.id_lang=' . (int) $firstLangId;
        $sql['cms_category'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'cms_category` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_cms_category` b ON a.id_cms_category = b.id_cms_category AND b.id_lang=' . (int) $firstLangId;
        $sql['meta'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_META_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_META_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_META_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'meta` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_meta` b ON a.id_meta = b.id_meta AND b.id_lang=' . (int) $firstLangId;
        $sql['manufacturer'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'manufacturer` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_manufacturer` b ON a.id_manufacturer = b.id_manufacturer AND b.id_lang=' . (int) $firstLangId;
        $sql['supplier'] = 'SELECT 
                    SUM(CASE WHEN b.allow_search = 1 OR (b.allow_search = 2 AND 1 = ' . (int) Configuration::get('ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT') . ') OR (b.allow_search IS NULL AND 1 = ' . (int) Configuration::get('ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as hasindex, 
                    SUM(CASE WHEN b.allow_search = 0 OR (b.allow_search = 2 AND 0 = ' . (int) Configuration::get('ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT') . ') THEN 1 ELSE 0 END) as noindex, 
                    SUM(CASE WHEN b.allow_flw_link = 1 OR b.allow_flw_link IS NULL THEN 1 ELSE 0 END) as follow, 
                    SUM(CASE WHEN b.allow_flw_link = 0  THEN 1 ELSE 0 END) as nofollow,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' <= 4 THEN 1 END) as seo_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 4 AND ' . (string) $sqlOverAllSeoScore . ' <= 7 THEN 1 END) as seo_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllSeoScore . ' > 7 THEN 1 END) as seo_score_good,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' <= 4 THEN 1 END) as readability_score_bad,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 4 AND ' . (string) $sqlOverAllReadabilityScore . ' <= 7 THEN 1 END) as readability_score_na,
                    SUM(CASE WHEN ' . (string) $sqlOverAllReadabilityScore . ' > 7 THEN 1 END) as readability_score_good
                    FROM `' . _DB_PREFIX_ . 'supplier` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_supplier` b ON a.id_supplier = b.id_supplier AND b.id_lang=' . (int) $firstLangId;

        $result = [
            'index' => 0,
            'noindex' => 0,
            'follow' => 0,
            'nofollow' => 0,
            'seo_score_bad' => 0,
            'seo_score_na' => 0,
            'seo_score_good' => 0,
            'readability_score_bad' => 0,
            'readability_score_na' => 0,
            'readability_score_good' => 0,
            'pages' => [],
        ];
        foreach ($sql as $k => $s) {
            $item = Db::getInstance()->getRow($s);
            $result['index'] += (int) $item['hasindex'];
            $result['noindex'] += (int) $item['noindex'];
            $result['follow'] += (int) $item['follow'];
            $result['nofollow'] += (int) $item['nofollow'];
            $result['seo_score_bad'] += (int) $item['seo_score_bad'];
            $result['seo_score_na'] += (int) $item['seo_score_na'];
            $result['seo_score_good'] += (int) $item['seo_score_good'];
            $result['readability_score_bad'] += (int) $item['readability_score_bad'];
            $result['readability_score_na'] += (int) $item['readability_score_na'];
            $result['readability_score_good'] += (int) $item['readability_score_good'];
            $result['pages'][$k] = [
                'readability_score' => [
                    'bad' => (int) $item['readability_score_bad'],
                    'na' => (int) $item['readability_score_na'],
                    'good' => (int) $item['readability_score_good'],
                ],
                'seo_score' => [
                    'bad' => (int) $item['seo_score_bad'],
                    'na' => (int) $item['seo_score_na'],
                    'good' => (int) $item['seo_score_good'],
                ],
            ];
        }

        return $result;
    }

    /**
     * @param string $pageType
     *
     * @return string
     */
    private static function _sqlNoAnalysisPages($pageType = 'product')
    {
        switch ($pageType) {
            case 'category':
                return sprintf('SELECT 
                        SUM(IF(b.id_category IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%scategory_lang` a
                        LEFT JOIN `%sets_seo_category` b ON (a.id_category = b.id_category AND b.id_lang=a.id_lang AND b.seo_score > 0)', _DB_PREFIX_, _DB_PREFIX_);
            case 'cms':
                return sprintf('SELECT 
                        SUM(IF(b.id_cms IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%scms_lang` a
                        LEFT JOIN `%sets_seo_cms` b ON (a.id_cms = b.id_cms AND b.id_lang=a.id_lang AND b.seo_score > 0)', _DB_PREFIX_, _DB_PREFIX_);
            case 'cms_category':
                return sprintf('SELECT 
                        SUM(IF(b.id_cms_category IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%scms_category_lang` a
                        LEFT JOIN `%sets_seo_cms_category` b ON (a.id_cms_category = b.id_cms_category AND b.id_lang=a.id_lang AND b.seo_score > 0)', _DB_PREFIX_, _DB_PREFIX_);
            case 'meta':
                return sprintf('SELECT 
                        SUM(IF(b.id_meta IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.title IS NULL OR a.title = "" OR a.description IS NULL OR a.description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%smeta_lang` a
                        LEFT JOIN `%sets_seo_meta` b ON (a.id_meta = b.id_meta AND b.id_lang=a.id_lang)', _DB_PREFIX_, _DB_PREFIX_);
            case 'manufacturer':
                return sprintf('SELECT 
                        SUM(IF(b.id_manufacturer IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%smanufacturer_lang` a
                        LEFT JOIN `%sets_seo_manufacturer` b ON (a.id_manufacturer = b.id_manufacturer AND b.id_lang=a.id_lang AND b.seo_score > 0)', _DB_PREFIX_, _DB_PREFIX_);
            case 'supplier':
                return sprintf('SELECT 
                        SUM(IF(b.id_supplier IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END) as nometa,
                        COUNT(*) as total
                        FROM `%ssupplier_lang` a
                        LEFT JOIN `%sets_seo_supplier` b ON (a.id_supplier = b.id_supplier AND b.id_lang=a.id_lang AND b.seo_score > 0)', _DB_PREFIX_, _DB_PREFIX_);
            case 'product':
            default:
                return sprintf('SELECT 
                        SUM(IF(b.id_product IS NULL, 1, 0)) as noanalysis,
                        SUM(CASE WHEN a.meta_title IS NULL OR a.meta_title = "" OR a.meta_description IS NULL OR a.meta_description = "" THEN 1 ELSE 0 END ) as nometa,
                        COUNT(*) as total
                        FROM `%sproduct_lang` a
                        LEFT JOIN `%sets_seo_product` b ON (a.id_product = b.id_product AND b.id_lang=a.id_lang AND b.seo_score > 0)
                        INNER JOIN `%sproduct` ax ON (a.id_product = ax.id_product AND ax.state = 1);', _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_);
        }
    }

    /**
     * @param string $pageType
     *
     * @return int[]
     */
    public static function countNoAnalysisPageByType($pageType)
    {
        $types = ['product', 'category', 'cms', 'cms_category', 'meta', 'manufacturer', 'supplier'];
        if (in_array($pageType, $types, true)) {
            return Db::getInstance()->getRow(self::_sqlNoAnalysisPages($pageType));
        }

        return [
            'noanalysis' => 0,
            'nometa' => 0,
            'total' => 0,
        ];
    }

    public static function getTotalMetaIndex()
    {
        $sql = [];
        $types = ['product', 'category', 'cms', 'cms_category', 'meta', 'manufacturer', 'supplier'];
        foreach ($types as $type) {
            $sql[$type] = self::_sqlNoAnalysisPages($type);
        }

        $result = [
            'noanalysis' => 0,
            'noanalysis_pages' => [],
            'nometa' => 0,
            'hasmeta' => 0,
            'pages' => [],
        ];
        foreach ($sql as $k => $s) {
            $data = Db::getInstance()->getRow($s);
            $result['noanalysis'] += (int) $data['noanalysis'];
            $result['nometa'] += (int) $data['nometa'];
            $result['hasmeta'] += ((int) $data['total'] - (int) $data['nometa']);
            $result['pages'][$k] = [
                'readability_score' => [
                    'noanalysis' => (int) $data['noanalysis'],
                ],
                'seo_score' => [
                    'noanalysis' => (int) $data['noanalysis'],
                ],
            ];
        }

        return $result;
    }

    private function calcOverAllScore($type, $total_score = 0, $score_analysis = null)
    {
        $numberResult = ETS_TOTAL_READABILITY_RULE_SCORE;
        $applicableRulesKey = 'total_applicable_readability_rules';

        if ('seo_score' == $type) {
            $numberResult = ETS_TOTAL_SEO_RULE_SCORE;
            $applicableRulesKey = 'total_applicable_seo_rules';
        }

        // If score_analysis is provided and contains actual applicable rules count, use it
        if ($score_analysis && isset($score_analysis[$applicableRulesKey]) && (int)$score_analysis[$applicableRulesKey] != -999) {
            $numberResult = (int)$score_analysis[$applicableRulesKey];
        }

        // Avoid division by zero
        if ($numberResult === 0) {
            return 0;
        }

        return round((int) $total_score / ($numberResult * 9) * 10);
    }

    /**
     * modifyResultList.
     *
     * @param string $type
     * @param array $lists
     *
     * @return array
     */
    public function modifyResultList($type = 'cms', $lists = [])
    {
        $id_column = 'id_cms';
        $table = 'ets_seo_cms';
        $index_config_name = 'ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT';
        $enable_seo = 0;
        switch ($type) {
            case 'meta':
                $id_column = 'id_meta';
                $table = 'ets_seo_meta';
                $index_config_name = 'ETS_SEO_META_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
            case 'cms_category':
                $id_column = 'id_cms_category';
                $table = 'ets_seo_cms_category';
                $index_config_name = 'ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
            case 'product':
                $id_column = 'id_product';
                $table = 'ets_seo_product';
                $index_config_name = 'ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
            case 'category':
                $id_column = 'id_category';
                $table = 'ets_seo_category';
                $index_config_name = 'ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
            case 'manufacturer':
                $id_column = 'id_manufacturer';
                $table = 'ets_seo_manufacturer';
                $index_config_name = 'ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
            case 'supplier':
                $id_column = 'id_supplier';
                $table = 'ets_seo_supplier';
                $index_config_name = 'ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT';
                $enable_seo = 1;
                break;
        }

        $results = [];
        $ids = [];
        foreach ($lists as $item) {
            $ids[] = $item[$id_column];
        }

        if ($ids) {
            $multiLangActive = Language::isMultiLanguageActivated($this->context->shop->id);
            $idLangs = $multiLangActive ? Language::getIDs(true, $this->context->shop->id) : [$this->context->language->id];

            $seo_data = EtsSeoUpdating::getSeoDatas($id_column, $table, $ids, $idLangs);
            $results = [];
            foreach ($lists as $item) {
                $item['seo_score'] = '';
                $item['readability_score'] = '';
                if ($seo_data) {
                    foreach ($seo_data as $seo) {
                        if ($seo[$id_column] == $item[$id_column]) {
                            // Parse score_analysis JSON to get the actual number of applicable rules
                            $scoreAnalysis = !empty($seo['score_analysis']) ? json_decode($seo['score_analysis'], true) : null;

                            $overAllSeoScore = $this->calcOverAllScore('seo_score', (int) $seo['seo_score'], $scoreAnalysis);
                            $classPrefix = 'ets_seo_analysis_score ets_seo_score ';
                            $seo_status = $this->l('No analysis available', __FILE__);
                            $seo_class = 'grey';
                            if (!$enable_seo) {
                                $seo_status = $this->l('No index', __FILE__);
                                $seo_class = 'yellow';
                            } elseif (!(int) $seo['allow_search'] || (2 == (int) $seo['allow_search'] && 0 == (int) Configuration::get($index_config_name))) {
                                $seo_status = $this->l('No index', __FILE__);
                                $seo_class = 'grey-noindex';
                            } elseif (!trim((string)$seo['key_phrase']) && !trim((string)$seo['minor_key_phrase'])) {
                                $seo_status = $this->l('No Focus or Related key phrases', __FILE__);
                                $seo_class = 'grey-nokeyphrase';
                            } elseif ($overAllSeoScore <= 4) {
                                $seo_status = $this->l('Not good', __FILE__);
                                $seo_class = 'red';
                            } elseif ($overAllSeoScore > 4 && $overAllSeoScore <= 7) {
                                $seo_status = $this->l('Acceptable', __FILE__);
                                $seo_class = 'orange';
                            } elseif ($overAllSeoScore > 7) {
                                $seo_status = $this->l('Excellent', __FILE__);
                                $seo_class = 'green';
                            }
                            $seo_class .= $multiLangActive ? ' has_multilang' : ' no_multilang';
                            $item['seo_score'] .= EtsSeoStrHelper::displayText($seo['iso_code'], 'span', ['class' => $classPrefix . $seo_class, 'title' => $seo_status]);
                            $overAllReadabilityScore = $this->calcOverAllScore('readability_score', (int) $seo['readability_score'], $scoreAnalysis);
                            $readability_status = $this->l('No analysis available', __FILE__);
                            $readability_class = 'yellow';
                            if ('meta' == $type) {
                                $readability_status = $this->l('No analysis available', __FILE__);
                                $readability_class = 'grey-darken';
                            } elseif ($overAllReadabilityScore <= 4) {
                                $readability_status = $this->l('Not good', __FILE__);
                                $readability_class = 'red';
                            } elseif ($overAllReadabilityScore > 4 && $overAllReadabilityScore <= 7) {
                                $readability_status = $this->l('Acceptable', __FILE__);
                                $readability_class = 'orange';
                            } elseif ($overAllReadabilityScore > 7) {
                                $readability_status = $this->l('Excellent', __FILE__);
                                $readability_class = 'green';
                            }
                            $readability_class .= $multiLangActive ? ' has_multilang' : ' no_multilang';
                            $item['readability_score'] .= EtsSeoStrHelper::displayText($seo['iso_code'], 'span', ['class' => $classPrefix . $readability_class, 'title' => $readability_status]);
                        }
                    }
                }
                if ('' == $item['seo_score']) {
                    $item['seo_score'] = '--';
                }
                if ('' == $item['readability_score']) {
                    $item['readability_score'] = '--';
                }

                if (('cms' == $type) && isset($item['head_seo_title'])) {
                    $cms = new CMS($item['id_cms'], $this->context->language->id);
                    $cmsCate = new CMSCategory($cms->id_cms_category, $this->context->language->id);
                    $cmsCateName = $cmsCate->name ? : '';
                    $item['head_seo_title'] = self::getModule()->formatSeoMeta($item['head_seo_title'], ['post_title' => $item['meta_title'], 'is_title' => true, 'category' => $cmsCateName], 'cms');
                }
                $results[] = $item;
            }
        }

        return $results;
    }
}
