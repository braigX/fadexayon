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
 * Class AdminEtsSeoSearchAppearanceRSSController.
 *
 * @property \Ets_Seo $module
 * @property \Context|\ContextCore $context
 */
class AdminEtsSeoSearchAppearanceRSSController extends ModuleAdminController
{
    public $priority_options;
    /**
     * __construct.
     *
     * @return void
     */
    public $rss_options;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'rss_setting' => [
                'title' => $this->module->l('RSS settings', 'AdminEtsSeoSearchAppearanceRSSController'),
                'fields' => $seoDef->fields_config()['rss_setting'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoSearchAppearanceRSSController'),
                ],
            ],
        ];
        $this->rss_options = [
            'product_category' => [
                'label' => $this->module->l('Product categories', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
            'cms_category' => [
                'label' => $this->module->l('CMS categories', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
            'all_products' => [
                'label' => $this->module->l('All products', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
            'new_products' => [
                'label' => $this->module->l('New products', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
            'special_products' => [
                'label' => $this->module->l('Special products', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
            'popular_products' => [
                'label' => $this->module->l('Popular products', 'AdminEtsSeoSearchAppearanceRSSController'),
                'value' => '',
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoSearchAppearanceRSSController');
        }
    }

    public function renderOptions()
    {
        $option_value = explode(',', (string) Configuration::get('ETS_SEO_RSS_OPTION'));
        $this->context->smarty->assign([
            'ets_seo_base_uri' => __PS_BASE_URI__,
            'ets_seo_multilang_activated' => Language::isMultiLanguageActivated($this->context->shop->id),
            'ets_seo_languages' => Language::getLanguages(true),
            'ets_seo_baseurl' => $this->context->shop->getBaseURL(true, true),
            'ETS_SEO_RSS_OPTION' => $option_value,
            'ets_seo_rss_options' => $this->rss_options,
            'link' => $this->context->link,
        ]);

        return parent::renderOptions();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $rss_options = Tools::getValue('ETS_SEO_RSS_OPTION', []);
            $rss_enable = (int) Tools::getValue('ETS_SEO_RSS_ENABLE');
            if ($rss_enable && !$rss_options ) {
                $this->errors[] = $this->module->l('The page included in rss is required', 'AdminEtsSeoSearchAppearanceRSSController');
            } elseif (!Ets_Seo::validateArray($rss_options)) {
                $this->errors[] = $this->module->l('The page included in rss is not valid', 'AdminEtsSeoSearchAppearanceRSSController');
            }
            $_POST['ETS_SEO_RSS_OPTION'] = implode(',', $rss_options);
            $postLimit = Tools::getValue('ETS_SEO_RSS_POST_LIMIT');
            if ($postLimit && !Validate::isUnsignedInt($postLimit)) {
                $this->errors[] = $this->module->l('The post limit must be an unsigned integer value', 'AdminEtsSeoSearchAppearanceRSSController');
            } elseif ('0' == $postLimit) {
                $this->errors[] = $this->module->l('The post limit must be an unsigned integer value', 'AdminEtsSeoSearchAppearanceRSSController');
            }
        }

        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $rssOption = ($rssOption = Tools::getValue('ETS_SEO_RSS_OPTION')) && Validate::isCleanHtml($rssOption) ? $rssOption : '';
            Configuration::updateValue('ETS_SEO_RSS_OPTION', $rssOption);
            $this->module->_clearCache('*');
        }
    }
}
