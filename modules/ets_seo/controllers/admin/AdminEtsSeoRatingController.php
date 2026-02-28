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

/**
 * Class AdminEtsSeoRatingController
 *
 * @property Ets_seo $module;
 */
class AdminEtsSeoRatingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'rating' => [
                'title' => $this->module->l('Ratings', 'AdminEtsSeoRatingController'),
                'fields' => $seoDef->fields_config()['rating'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoRatingController'),
                ],
                'description' => $this->module->l('Forced ratings allow you to manually specify the number of rating stars displayed on Google search result pages. Once enabled, you can enter the ratings you want when editing products, category, CMS pages, etc. You are always recommended to use real ratings from your customers to avoid spam penalties from search engines', 'AdminEtsSeoRatingController'),
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoRatingController');
        }
    }

    public function renderOptions()
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $this->context->smarty->assign([
            'ets_seo_rating_pages' => $seoDef->rating_pages(),
            'ETS_SEO_RATING_PAGES' => explode(',', Configuration::get('ETS_SEO_RATING_PAGES')),
        ]);

        return parent::renderOptions();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $rating_enabled = (int) Tools::getValue('ETS_SEO_RATING_ENABLED');
            if (1 == $rating_enabled) {
                $avgRating = Tools::getValue('ETS_SEO_RATING_AVG');
                $countRating = Tools::getValue('ETS_SEO_RATING_COUNT');
                $bestRating = Tools::getValue('ETS_SEO_RATING_BEST');
                $worstRating = Tools::getValue('ETS_SEO_RATING_WORST');
                if (!$avgRating) {
                    $this->errors[] = $this->module->l('The Average rating is required.', 'AdminEtsSeoRatingController');
                } elseif (!Validate::isUnsignedFloat($avgRating)) {
                    $this->errors[] = $this->module->l('The Average rating must be a decimal.', 'AdminEtsSeoRatingController');
                } elseif ((float) $avgRating <= 0 && (float) $avgRating > 5) {
                    $this->errors[] = $this->module->l('The Average rating is invalid.', 'AdminEtsSeoRatingController');
                } else {
                    if ($bestRating || '0' === $bestRating) {
                        if (!Validate::isUnsignedInt($bestRating)) {
                            $this->errors[] = $this->module->l('The Best rating must be a integer.', 'AdminEtsSeoRatingController');
                        } elseif ((int) $worstRating > 5) {
                            $this->errors[] = $this->module->l('The Best rating is invalid.', 'AdminEtsSeoRatingController');
                        } elseif ((int) $bestRating < (float) $avgRating) {
                            $this->errors[] = $this->module->l('The Best rating must be greater than or equal to the average rating.', 'AdminEtsSeoRatingController');
                        }
                    }
                    if ($worstRating || '0' === $worstRating) {
                        if (!Validate::isUnsignedInt($worstRating)) {
                            $this->errors[] = $this->module->l('The Worst rating must be a integer.', 'AdminEtsSeoRatingController');
                        } elseif ((int) $worstRating <= 0) {
                            $this->errors[] = $this->module->l('The Worst rating is invalid.', 'AdminEtsSeoRatingController');
                        } elseif ((int) $worstRating > (float) $avgRating) {
                            $this->errors[] = $this->module->l('The Worst rating must be less than or equal to the average rating.', 'AdminEtsSeoRatingController');
                        }
                    }
                }

                if (!$countRating) {
                    $this->errors[] = $this->module->l('The rating count is required.', 'AdminEtsSeoRatingController');
                } elseif (!Validate::isUnsignedInt($countRating)) {
                    $this->errors[] = $this->module->l('The rating count must be an integer.', 'AdminEtsSeoRatingController');
                } elseif ($countRating <= 0) {
                    $this->errors[] = $this->module->l('The rating count is invalid.', 'AdminEtsSeoRatingController');
                }
            }
            $_POST['ETS_SEO_RATING_PAGES'] = ($ratingPages = Tools::getValue('ETS_SEO_RATING_PAGES')) && is_array($ratingPages) && Ets_Seo::validateArray($ratingPages) ? implode(',', $ratingPages) : '';
        }
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }
}
