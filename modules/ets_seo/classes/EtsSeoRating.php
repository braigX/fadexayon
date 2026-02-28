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

require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';

class EtsSeoRating extends ObjectModel
{
    use EtsSeoGetInstanceTrait;
    public $id_ets_seo_rating;
    public $page_type;
    public $id_page;
    public $enable;
    public $average_rating;
    public $best_rating;
    public $worst_rating;
    public $rating_count;
    public $id_shop;
    public static $definition = [
        'table' => 'ets_seo_rating',
        'primary' => 'id_ets_seo_rating',
        'multilang_shop' => false,
        'fields' => [
            'page_type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'id_page' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'enable' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'average_rating' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
            ],
            'best_rating' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
                'allow_null' => true,
            ],
            'worst_rating' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
                'allow_null' => true,
            ],
            'rating_count' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    public static function getRating($type, $id, $context = null)
    {
        if ($type && (int) $id) {
            if (!$context) {
                $context = Ets_Seo::getContextStatic();
            }

            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . "ets_seo_rating` WHERE page_type = '" . pSQL($type) . "' AND id_page = " . (int) $id . ' AND id_shop = ' . (int) $context->shop->id);
        }

        return false;
    }

    public static function getRatingConfig($type, $id)
    {
        $ratingConfig = Configuration::get('ETS_SEO_RATING_PAGES') ? explode(',', Configuration::get('ETS_SEO_RATING_PAGES')) : [];
        if (!in_array($type, $ratingConfig)) {
            return null;
        }
        $dataRating = null;
        if ((int) $id) {
            switch ($type) {
                case 'product':
                    $dataRating = self::getRating('product', $id);
                    break;
                case 'cms':
                    $dataRating = self::getRating('cms', $id);
                    break;
                case 'meta':
                    $dataRating = self::getRating('meta', $id);
                    break;
                case 'category':
                    $dataRating = self::getRating('category', $id);
                    break;
                case 'cms_category':
                    $dataRating = self::getRating('cms_category', $id);
                    break;
                case 'manufacturer':
                    $dataRating = self::getRating('manufacturer', $id);
                    break;
                case 'supplier':
                    $dataRating = self::getRating('supplier', $id);
                    break;
                default:
                    $dataRating = self::getRating('meta', $id);
                    break;
            }
        }

        $ratingSeo = [
            'avg_rating' => '',
            'best_rating' => '',
            'worst_rating' => '',
            'rating_count' => '',
        ];

        $enableRating = false;
        if ($dataRating && 1 == (int) $dataRating['enable']) {
            $ratingSeo = [
                'avg_rating' => (float) $dataRating['average_rating'],
                'best_rating' => (int) $dataRating['best_rating'],
                'worst_rating' => (int) $dataRating['worst_rating'],
                'rating_count' => (int) $dataRating['rating_count'],
            ];
            $enableRating = true;
        } elseif ($dataRating && 0 == (int) $dataRating['enable']) {
            $ratingSeo = [
                'avg_rating' => (float) $dataRating['average_rating'],
                'best_rating' => (int) $dataRating['best_rating'],
                'worst_rating' => (int) $dataRating['worst_rating'],
                'rating_count' => (int) $dataRating['rating_count'],
            ];
            $enableRating = false;
        }

        return $enableRating ? $ratingSeo : null;
    }

    public function updateSeoRating($type, $id)
    {
        $seoRating = self::getRating($type, $id);
        $ets_seo_rating_enable = (int) Tools::getValue('ets_seo_rating_enable');
        $ets_seo_rating_average = (float) Tools::getValue('ets_seo_rating_average');
        $bestRating = (float) Tools::getValue('ets_seo_rating_best');
        $worstRating = (float) Tools::getValue('ets_seo_rating_worst');
        $ets_seo_rating_count = (int) Tools::getValue('ets_seo_rating_count');
        if (1 !== $ets_seo_rating_enable) {
            if ($seoRating && isset($seoRating['id_ets_seo_rating']) && ($id_ets_seo_rating = $seoRating['id_ets_seo_rating'])) {
                $rating = new self($id_ets_seo_rating);
                $rating->enable = 0;
                $rating->save();
            }

            $this->clearRatingCache($type, (int) $id);

            return;
        }
        $errors = $this->validateSeoRating();
        if ($errors) {
            return;
        }

        if ($seoRating && isset($seoRating['id_ets_seo_rating']) && ($id_ets_seo_rating = $seoRating['id_ets_seo_rating'])) {
            $rating = new self($id_ets_seo_rating);
        } else {
            $rating = new self();
        }
        $rating->page_type = $type;
        $rating->id_page = (int) $id;
        $rating->enable = (int) $ets_seo_rating_enable;
        $rating->average_rating = (float) $ets_seo_rating_average;
        if ($bestRating) {
            $rating->best_rating = (int) $bestRating;
        } else {
            $rating->best_rating = null;
        }
        if ($worstRating) {
            $rating->worst_rating = (int) $worstRating;
        }
        $rating->rating_count = (int) $ets_seo_rating_count;
        $rating->id_shop = (int) Ets_Seo::getContextStatic()->shop->id;

        $rating->save();

        $this->clearRatingCache($type, (int) $id);
    }

    public function validateSeoRating()
    {
        $errors = [];
        $seoDef = Ets_Seo_Define::getInstance();
        $trans = $seoDef->translateMessages();
        $ets_seo_rating_enable = (int) Tools::getValue('ets_seo_rating_enable');
        if (1 == $ets_seo_rating_enable) {
            $avgRating = Tools::getValue('ets_seo_rating_average');
            $bestRating = Tools::getValue('ets_seo_rating_best');
            $worstRating = Tools::getValue('ets_seo_rating_worst');
            $countRating = Tools::getValue('ets_seo_rating_count');
            if (!$avgRating) {
                $errors[] = $trans['avg_rating_required'];
            } elseif (!Validate::isUnsignedFloat($avgRating)) {
                $errors[] = $trans['avg_rating_decimal'];
            } elseif ((float) $avgRating <= 0 && (float) $avgRating > 5) {
                $errors[] = $trans['avg_rating_invalid'];
            } else {
                if ($bestRating) {
                    if (!Validate::isUnsignedInt($bestRating)) {
                        $errors[] = $trans['best_rating_integer'];
                    } elseif ((int) $bestRating > 5) {
                        $errors[] = $trans['best_rating_invalid'];
                    } elseif ((int) $bestRating < (float) $avgRating) {
                        $errors[] = $trans['best_rating_greater_than_avg'];
                    }
                }

                if ($worstRating) {
                    if (!Validate::isUnsignedInt($worstRating)) {
                        $errors[] = $trans['worst_rating_integer'];
                    } elseif ((int) $worstRating <= 0) {
                        $errors[] = $trans['worst_rating_invalid'];
                    } elseif ((int) $worstRating > (float) $avgRating) {
                        $errors[] = $trans['worst_rating_less_than_avg'];
                    }
                }
            }
            if (!$countRating) {
                $errors[] = $trans['rating_count_required'];
            } elseif (!Validate::isUnsignedInt($countRating)) {
                $errors[] = $trans['rating_count_integer'];
            }
        }

        return $errors;
    }

    protected function clearRatingCache($type, $id)
    {
        if ('product' !== $type || !$id) {
            return;
        }
        $module = Module::getInstanceByName('ets_seo');
        if (!$module instanceof Ets_Seo) {
            return;
        }

        $productCacheId = $module->_getCacheId(['seo_product_html' => (int) $id]);
        $module->_clearCache('parts/_rating.tpl', $productCacheId);
        $module->_clearCache('parts/_seo_advanced.tpl', $productCacheId);
        $module->_clearCache('parts/_tab_seo.tpl', $productCacheId);

        $tabCacheId = $module->_getCacheId(['displayCustomAdminProductsSeoStepBottom' => (int) $id]);
        $module->_clearCache('page/seo_setting.tpl', $tabCacheId);
    }
}
