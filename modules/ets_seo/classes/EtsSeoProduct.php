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

require_once __DIR__ . '/AbstractEtsSeoAnalyzableModel.php';

/**
 * Class EtsSeoProduct
 *
 * @mixin \ObjectModelCore
 */
class EtsSeoProduct extends AbstractEtsSeoAnalyzableModel
{
    /**
     * @var int
     */
    public $id_ets_seo_product;
    /**
     * @var int
     */
    public $id_product;

    /**
     * @var int
     */
    public $id_shop;

    /**
     * @var int
     */
    public $id_lang;

    /**
     * @var string
     */
    public $key_phrase;

    /**
     * @var string
     */
    public $minor_key_phrase;

    /**
     * @var int
     */
    public $allow_search;

    /**
     * @var int
     */
    public $allow_flw_link;

    /**
     * @var string
     */
    public $meta_robots_adv;

    /**
     * @var string
     */
    public $meta_keywords;

    /**
     * @var string
     */
    public $canonical_url;

    /**
     * @var int
     */
    public $seo_score;

    /**
     * @var int
     */
    public $readability_score;

    /**
     * @var string
     */
    public $score_analysis;

    /**
     * @var string
     */
    public $content_analysis;

    /**
     * @var string
     */
    public $social_title;

    /**
     * @var string
     */
    public $social_desc;

    /**
     * @var string
     */
    public $social_img;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'ets_seo_product',
        'primary' => 'id_ets_seo_product',
        'multilang_shop' => false,
        'fields' => [
            'id_ets_seo_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_lang' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'key_phrase' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'minor_key_phrase' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'allow_search' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'allow_flw_link' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'meta_robots_adv' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'meta_keywords' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'canonical_url' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'seo_score' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ],
            'readability_score' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ],
            'score_analysis' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'allow_null' => true,
            ],
            'content_analysis' => [
                'type' => self::TYPE_HTML,
                'allow_null' => true,
            ],
            'social_title' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'social_desc' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'social_img' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        if (!$this->id) {
            $this->allow_search = 2;
            $this->allow_flw_link = 1;
        }
    }

    /**
     * @param int $idObject
     * @param string $socialImgName
     *
     * @return \EtsSeoProduct
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function findOneBySocialImg($idObject, $socialImgName)
    {
        $sql = sprintf(
            'SELECT %s FROM `%s%s` WHERE `%s` = %d AND `%s` = "%s"',
            bqSQL(self::$definition['primary']),
            _DB_PREFIX_,
            bqSQL(self::$definition['table']),
            'id_product',
            (int) $idObject,
            'social_img',
            pSQL($socialImgName)
        );
        $id = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if (!$id) {
            throw new \PrestaShopException('Model not found.');
        }

        return new self($id);
    }

    public static function getSeoProduct($id_product, $context = null, $id_lang = null,$id_shop = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        if(!$id_shop)
            $id_shop = $context->shop->id;
        if ($id_lang) {
            return Db::getInstance()->getRow('SELECT * 
                                        FROM `' . _DB_PREFIX_ . 'ets_seo_product` 
                                        WHERE id_product = ' . (int) $id_product . ' AND id_shop = ' . (int) ($id_shop ?: $context->shop->id) . ' AND id_lang = ' . (int) $id_lang);
        }

        return Db::getInstance()->executeS('SELECT * 
                                        FROM `' . _DB_PREFIX_ . 'ets_seo_product` 
                                        WHERE id_product = ' . (int) $id_product . ' AND id_shop = ' . (int) ($id_shop ?: $context->shop->id));
    }

    public static function getProductAttributeName($id_product_attribute, $context = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        $sql = 'SELECT a.id_attribute,al.name,agl.name as group_name FROM `' . _DB_PREFIX_ . 'attribute` a
            INNER JOIN `' . _DB_PREFIX_ . 'attribute_shop` attribute_shop ON (a.id_attribute= attribute_shop.id_attribute AND attribute_shop.id_shop="' . (int) $context->shop->id . '")
            INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (a.id_attribute=pac.id_attribute)
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang="' . (int) $context->language->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (a.id_attribute_group= agl.id_attribute_group AND agl.id_lang="' . (int) $context->language->id . '")
            WHERE pac.id_product_attribute ="' . (int) $id_product_attribute . '"
        ';
        $attributes = Db::getInstance()->executeS($sql);
        $name_attribute = '';
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $name_attribute .= $attribute['group_name'] . ' ' . $attribute['name'] . ' - ';
            }
        }

        return trim($name_attribute, '- ');
    }

    public static function getCommentProductData($id_product)
    {
        if (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) {
            $data = Db::getInstance()->getRow('SELECT COUNT(*) as total_rating, SUM(grade) as total_review FROM `' . _DB_PREFIX_ . 'product_comment` pc WHERE id_product = ' . (int) $id_product);
            if ($data) {
                $rating_count = (int) $data['total_rating'];
                $total_review = (int) $data['total_review'];

                return [
                    'avg_rating' => $rating_count ? $total_review / $rating_count : 0,
                    'rating_count' => $rating_count,
                ];
            }
        }

        return [
            'avg_rating' => 0,
            'rating_count' => 0,
        ];
    }

    public static function getTotalProduct($active = false, $id_lang = null)
    {
        $shopId = (int) Ets_Seo::getContextStatic()->shop->id;

        // Get global configuration for allow_search = 2
        $allow_search_global = (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT');

        $sql = '
            SELECT COUNT(DISTINCT p.id_product) as total_product
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON p.id_product = ps.id_product AND ps.id_shop = ' . $shopId . '
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_seo_product` ep ON p.id_product = ep.id_product AND ep.id_shop = ' . $shopId;

        // If language is specified, join language table and filter by language
        if ($id_lang) {
            $sql .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.id_product = pl.id_product AND pl.id_lang = ' . (int) $id_lang . ' AND pl.id_shop = ' . $shopId;
        }

        $sql .= '
            WHERE ps.visibility IN ("both", "catalog")';

        // Only count active products if requested
        if ($active) {
            $sql .= ' AND ps.active = 1';
        }

        // If language is specified, only count for matching language in ep
        if ($id_lang) {
            $sql .= ' AND (ep.id_product IS NULL OR ep.id_lang = ' . (int) $id_lang . ')';
        }

        // Respect allow_search flag from ets_seo_product
        $sql .= '
            AND (
                ep.id_product IS NULL
                OR (
                    ep.allow_search = 1
                    OR (ep.allow_search = 2 AND ' . $allow_search_global . ' = 1)
                )
            )';

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getProductsByLinkRewrite($link_rewrite, $id_shop, $id_lang)
    {
        return Db::getInstance()->executeS('SELECT id_product, name FROM `' . _DB_PREFIX_ . "product_lang` WHERE link_rewrite='" . pSQL($link_rewrite) . "' AND id_shop=" . (int) $id_shop . "  AND id_lang = '" . (int) $id_lang . "'");
    }

    // Static variable to prevent duplicate saves
    private static $isUpdating = false;
    
    public static function updateSeoProduct($params)
    {
        // Prevent duplicate saves from multiple hooks
        if (self::$isUpdating) {
            return;
        }
        self::$isUpdating = true;
        
        if ($params['id_product']) {
            $id_product = $params['id_product'];
            $id_shop = Ets_Seo::getContextStatic()->shop->id;
            $key_phrase = ($key_phrase = Tools::getValue('ets_seo_key_phrase', [])) && Ets_Seo::validateArray($key_phrase) ? $key_phrase : [];
            $minor_key_phrase = ($minor_key_phrase = Tools::getValue('ets_seo_minor_keyphrase', [])) && Ets_Seo::validateArray($minor_key_phrase) ? $minor_key_phrase : [];
            $social_title = ($social_title = Tools::getValue('ets_seo_social_title', [])) && Ets_Seo::validateArray($social_title) ? $social_title : [];
            $social_desc = ($social_desc = Tools::getValue('ets_seo_social_desc', [])) && Ets_Seo::validateArray($social_desc) ? $social_desc : [];
            $social_img = ($social_img = Tools::getValue('ets_seo_social_img', [])) && Ets_Seo::validateArray($social_img) ? $social_img : [];
            $advanced = ($advanced = Tools::getValue('ets_seo_advanced', [])) && Ets_Seo::validateArray($advanced) ? $advanced : [];
            $score_data = ($score_data = Tools::getValue('ets_seo_score_data')) && Validate::isJson($score_data) ? $score_data : '';
            $contentAnalysis = ($contentAnalysis = Tools::getValue('ets_seo_content_analysis')) && Validate::isJson($contentAnalysis) ? $contentAnalysis : '';
            $contentAnalysis = $contentAnalysis ? json_decode($contentAnalysis, true) : [];
            $scoreArray = $score_data ? json_decode($score_data, true) : [];
            if ($key_phrase) {
                foreach ($key_phrase as $id_lang => $value) {
                    if(Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && Shop::getContext() == Shop::CONTEXT_ALL)
                    {
                        if($shops = Shop::getShops(false,null,true))
                        {
                            foreach($shops as $idShop)
                            {
                                if (!Validate::isLoadedObject(new Language($id_lang))) {
                                    continue;
                                }
                                if (!isset($contentAnalysis[$id_lang])) {
                                    $contentAnalysis[$id_lang] = [];
                                }
                                $id_ets_seo_product = ($productSeo = self::getSeoProduct($id_product, null, $id_lang,$idShop)) ? $productSeo['id_ets_seo_product'] : 0;
                                if ($id_ets_seo_product) {
                                {
                                    if($idShop!=$id_shop)
                                        continue;
                                    $seoProduct = new self($id_ets_seo_product);
                                }
                                } else {
                                    $seoProduct = new self();
                                }
                                $seoProduct->id_product = $id_product;
                                $seoProduct->id_shop = $idShop;
                                $seoProduct->id_lang = $id_lang;
                                $seoProduct->key_phrase = $value;
                                $seoProduct->minor_key_phrase = EtsSeoSetting::getInstance()->getMinorKeyphrase($minor_key_phrase[$id_lang]);
                                $seoProduct->social_title = isset($social_title[$id_lang]) ? $social_title[$id_lang] : null;
                                $seoProduct->social_desc = isset($social_desc[$id_lang]) ? $social_desc[$id_lang] : null;
                                $seoProduct->social_img = isset($social_img[$id_lang]) && $social_img[$id_lang] ? EtsSeoSetting::getInstance()->getSocialImage($social_img[$id_lang]) : null;
                                if ($advanced) {
                                    foreach ($advanced as $key => $val) {
                                        if (property_exists($seoProduct, $key) && isset($val[$id_lang])) {
                                            $seoProduct->{$key} = $val[$id_lang];
                                        }
                                    }
                                }
                                $seoProduct->setSeoScore($scoreArray['seo_score'], $scoreArray['readability_score'], $contentAnalysis);
                                $seoProduct->save();
                            }
                        }
                    }
                    else
                    {
                        if (!Validate::isLoadedObject(new Language($id_lang))) {
                            continue;
                        }
                        if (!isset($contentAnalysis[$id_lang])) {
                            $contentAnalysis[$id_lang] = [];
                        }
                        $id_ets_seo_product = ($productSeo = self::getSeoProduct($id_product, null, $id_lang,$id_shop)) ? $productSeo['id_ets_seo_product'] : 0;
                        if ($id_ets_seo_product) {
                            {
                                $seoProduct = new self($id_ets_seo_product);
                            }
                        } else {
                            $seoProduct = new self();
                        }
                        $seoProduct->id_product = $id_product;
                        $seoProduct->id_shop = $id_shop;
                        $seoProduct->id_lang = $id_lang;
                        $seoProduct->key_phrase = $value;
                        $seoProduct->minor_key_phrase = EtsSeoSetting::getInstance()->getMinorKeyphrase($minor_key_phrase[$id_lang]);
                        $seoProduct->social_title = isset($social_title[$id_lang]) ? $social_title[$id_lang] : null;
                        $seoProduct->social_desc = isset($social_desc[$id_lang]) ? $social_desc[$id_lang] : null;
                        $seoProduct->social_img = isset($social_img[$id_lang]) && $social_img[$id_lang] ? EtsSeoSetting::getInstance()->getSocialImage($social_img[$id_lang]) : null;
                        if ($advanced) {
                            foreach ($advanced as $key => $val) {
                                if (property_exists($seoProduct, $key) && isset($val[$id_lang])) {
                                    $seoProduct->{$key} = $val[$id_lang];
                                }
                            }
                        }
                        $seoProduct->setSeoScore($scoreArray['seo_score'], $scoreArray['readability_score'], $contentAnalysis);
                        $seoProduct->save();
                    }
                }
            }
            EtsSeoRating::getInstance()->updateSeoRating('product', $id_product);
        }
        
        // Reset flag after save completes
        self::$isUpdating = false;
    }

    /**
     * {@inheritDoc}
     */
    public function save($null_values = false, $auto_date = true)
    {
        if ($rs = parent::save($null_values, $auto_date)) {
            $cacheId = self::getModule()->_getCacheId(['seo_product_html' => $this->id], true);
            $cacheId2 = self::getModule()->_getCacheId(['displayCustomAdminProductsSeoStepBottom' => $this->id], true);
            self::getModule()->_clearCache('*', $cacheId);
            self::getModule()->_clearCache('*', $cacheId2);
        }

        return $rs;
    }

    /**
     * {@inheritDoc}
     */
    public static function getRelationIdColumnName()
    {
        return 'id_product';
    }
}
