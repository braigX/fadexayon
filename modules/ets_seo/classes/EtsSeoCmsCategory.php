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
 * Class EtsSeoCmsCategory
 */
class EtsSeoCmsCategory extends AbstractEtsSeoAnalyzableModel
{
    /**
     * @var int
     */
    public $id_ets_seo_cms_category;
    /**
     * @var int
     */
    public $id_cms_category;

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
        'table' => 'ets_seo_cms_category',
        'primary' => 'id_ets_seo_cms_category',
        'multilang_shop' => false,
        'fields' => [
            'id_ets_seo_cms_category' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_cms_category' => [
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
     * @param int $idObject
     * @param string $socialImgName
     *
     * @return self
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function findOneBySocialImg($idObject, $socialImgName)
    {
        $sql = sprintf(
            'SELECT %s FROM `%s%s` WHERE `%s` = %d AND `%s` = "%s"',
            self::$definition['primary'],
            _DB_PREFIX_,
            self::$definition['table'],
            'id_cms_category',
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

    public static function getSeoCmsCategory($id_cms_category, $context = null, $id_lang = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }

        if ($id_lang) {
            return Db::getInstance()->getRow('SELECT * 
            FROM `' . _DB_PREFIX_ . 'ets_seo_cms_category` 
            WHERE id_cms_category = ' . (int) $id_cms_category . ' AND id_shop = ' . (int) $context->shop->id . ' AND id_lang = ' . (int) $id_lang);
        }

        return Db::getInstance()->executeS('SELECT * 
        FROM `' . _DB_PREFIX_ . 'ets_seo_cms_category` 
        WHERE id_cms_category = ' . (int) $id_cms_category . ' AND id_shop = ' . (int) $context->shop->id);
    }

    public static function getCmsCategoriesByLinkRewrite($link_rewrite, $id_shop, $id_lang)
    {
        return Db::getInstance()->executeS('SELECT id_cms_category, name FROM `' . _DB_PREFIX_ . "cms_category_lang` WHERE link_rewrite='" . pSQL($link_rewrite) . "' AND id_shop=" . (int) $id_shop . "   AND id_lang = '" . (int) $id_lang . "'");
    }

    public static function updateSeoCmsCategory($params)
    {
        $controller = ($controller = Tools::getValue('controller', '')) && Validate::isControllerName($controller) ? $controller : '';
        if (
            (!EtsSeoSetting::getInstance()->isNewTheme && 'AdminCmsContent' == $controller)
            || (EtsSeoSetting::getInstance()->isNewTheme && isset($params['object']) && $params['object'] instanceof CMSCategory)
        ) {
            $cms = $params['object'];
            if ($cms instanceof CMSCategory) {
                $id_cms = $cms->id;
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
                        if (!Validate::isLoadedObject(new Language($id_lang))) {
                            continue;
                        }
                        if (!isset($contentAnalysis[$id_lang])) {
                            $contentAnalysis[$id_lang] = [];
                        }
                        $id_ets_seo_cms = ($cmsCateogrySeo = self::getSeoCmsCategory($id_cms, null, $id_lang)) ? (int) $cmsCateogrySeo['id_ets_seo_cms_category'] : 0;

                        if ((int) $id_ets_seo_cms) {
                            $seoCms = new self($id_ets_seo_cms);
                        } else {
                            $seoCms = new self();
                        }

                        $seoCms->id_cms_category = $id_cms;
                        $seoCms->id_shop = Ets_Seo::getContextStatic()->shop->id;
                        $seoCms->id_lang = $id_lang;
                        $seoCms->key_phrase = $value;
                        $seoCms->minor_key_phrase = EtsSeoSetting::getInstance()->getMinorKeyphrase($minor_key_phrase[$id_lang]);
                        $seoCms->social_title = isset($social_title[$id_lang]) ? $social_title[$id_lang] : null;
                        $seoCms->social_desc = isset($social_desc[$id_lang]) ? $social_desc[$id_lang] : null;
                        $seoCms->social_img = isset($social_img[$id_lang]) ? EtsSeoSetting::getInstance()->getSocialImage($social_img[$id_lang]) : null;

                        if ($advanced) {
                            foreach ($advanced as $key => $val) {
                                if (property_exists($seoCms, $key) && isset($val[$id_lang])) {
                                    if (is_array($val[$id_lang])) {
                                        $val[$id_lang] = implode(',', $val[$id_lang]);
                                    }
                                    $seoCms->{$key} = $val[$id_lang];
                                }
                            }
                        }
                        if ($score_data && isset($scoreArray['seo_score'], $scoreArray['readability_score'])) {
                            $seoCms->setSeoScore($scoreArray['seo_score'], $scoreArray['readability_score'], $contentAnalysis);
                        }
                        $seoCms->save();
                    }
                }
                EtsSeoRating::getInstance()->updateSeoRating('cms_category', $id_cms);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getRelationIdColumnName()
    {
        return 'id_cms_category';
    }
}
