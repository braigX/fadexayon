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
 * Class EtsSeoSupplier
 */
class EtsSeoSupplier extends AbstractEtsSeoAnalyzableModel
{
    /**
     * @var int
     */
    public $id_ets_seo_supplier;
    /**
     * @var int
     */
    public $id_supplier;

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
        'table' => 'ets_seo_supplier',
        'primary' => 'id_ets_seo_supplier',
        'multilang_shop' => false,
        'fields' => [
            'id_ets_seo_supplier' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_supplier' => [
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
            bqSQL(self::$definition['primary']),
            _DB_PREFIX_,
            bqSQL(self::$definition['table']),
            'id_supplier',
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

    public static function getSeoSupplier($id_supplier, $context = null, $id_lang = null)
    {
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }

        if ($id_lang) {
            return Db::getInstance()->getRow('SELECT * 
            FROM `' . _DB_PREFIX_ . 'ets_seo_supplier` 
            WHERE id_supplier = ' . (int) $id_supplier . ' AND id_shop = ' . (int) $context->shop->id . ' AND id_lang = ' . (int) $id_lang);
        }

        return Db::getInstance()->executeS('SELECT * 
        FROM `' . _DB_PREFIX_ . 'ets_seo_supplier` 
        WHERE id_supplier = ' . (int) $id_supplier . ' AND id_shop = ' . (int) $context->shop->id);
    }

    public static function updateLinkRewriteSupplier($id = null, $name = null)
    {
        if ($id && $name) {
            if (Db::getInstance()->getValue('SELECT `id_supplier` FROM `' . _DB_PREFIX_ . 'ets_seo_supplier_url` WHERE `id_supplier`=' . (int) $id)) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . "ets_seo_supplier_url` SET `link_rewrite`='" . pSQL(Tools::str2url($name)) . "' WHERE `id_supplier`=" . (int) $id);
            } else {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_seo_supplier_url` (`id_supplier`, `link_rewrite`) VALUES(' . (int) $id . ",'" . pSQL(Tools::str2url($name)) . "')");
            }

            return true;
        }
        $suppliers = Db::getInstance()->executeS('SELECT `id_supplier`, `name` FROM `' . _DB_PREFIX_ . 'supplier`');
        if ($suppliers) {
            foreach ($suppliers as $supplier) {
                Db::getInstance()->execute('INSERT IGNORE  INTO `' . _DB_PREFIX_ . 'ets_seo_supplier_url` (`id_supplier`, `link_rewrite`) VALUES(' . (int) $supplier['id_supplier'] . ",'" . pSQL(Tools::str2url($supplier['name'])) . "')");
            }
        }

        return true;
    }

    public static function updateSeoSupplier($params)
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if (
            (!EtsSeoSetting::getInstance()->isNewTheme && 'AdminSuppliers' == $controller)
            || (EtsSeoSetting::getInstance()->isNewTheme && isset($params['object']) && $params['object'] instanceof Supplier)
        ) {
            $supplier = $params['object'];
            if ($supplier instanceof Supplier) {
                $id_supplier = $supplier->id;
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
                        $id_ets_seo_supplier = ($supplierSeo = self::getSeoSupplier($id_supplier, null, $id_lang)) ? (int) $supplierSeo['id_ets_seo_supplier'] : 0;
                        if ((int) $id_ets_seo_supplier) {
                            $seoSupplier = new self($id_ets_seo_supplier);
                        } else {
                            $seoSupplier = new self();
                        }
                        $seoSupplier->id_supplier = $id_supplier;
                        $seoSupplier->id_shop = Ets_Seo::getContextStatic()->shop->id;
                        $seoSupplier->id_lang = $id_lang;
                        $seoSupplier->key_phrase = $value;
                        $seoSupplier->minor_key_phrase = EtsSeoSetting::getInstance()->getMinorKeyphrase($minor_key_phrase[$id_lang]);
                        $seoSupplier->social_title = isset($social_title[$id_lang]) ? $social_title[$id_lang] : '';
                        $seoSupplier->social_desc = isset($social_desc[$id_lang]) ? $social_desc[$id_lang] : '';
                        $seoSupplier->social_img = isset($social_img[$id_lang]) ? EtsSeoSetting::getInstance()->getSocialImage($social_img[$id_lang]) : '';

                        if ($advanced) {
                            foreach ($advanced as $key => $val) {
                                if (property_exists($seoSupplier, $key) && isset($val[$id_lang])) {
                                    if (is_array($val[$id_lang])) {
                                        $val[$id_lang] = implode(',', $val[$id_lang]);
                                    }
                                    $seoSupplier->{$key} = $val[$id_lang];
                                }
                            }
                        }
                        if ($score_data && isset($scoreArray['seo_score'], $scoreArray['readability_score'])) {
                            $seoSupplier->setSeoScore($scoreArray['seo_score'], $scoreArray['readability_score'], $contentAnalysis);
                        }
                        $seoSupplier->save();
                    }
                }
                EtsSeoRating::getInstance()->updateSeoRating('supplier', $id_supplier);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getRelationIdColumnName()
    {
        return 'id_supplier';
    }
}
