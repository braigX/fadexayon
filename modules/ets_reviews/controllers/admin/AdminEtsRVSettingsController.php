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

if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__) . '/AdminEtsRVOptionController.php';

class AdminEtsRVSettingsController extends AdminEtsRVOptionController
{
    /**
     * @var Ets_reviews
     */
    public $module;

    public function __construct()
    {
        parent::__construct();

        $this->submit = 'submitPcConfiguration';
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS(array(
            _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js',
        ));
    }

    public function getConfigs()
    {
        return EtsRVDefines::getInstance()->getConfigs();
    }

    public function getConfigTabs()
    {
        return EtsRVDefines::getInstance()->getConfigTabs();
    }

    public function _postConfigs()
    {
        parent::_postConfigs();

        if (!(int)Configuration::get('ETS_RV_CACHE_ENABLED')) {
            EtsRVSmartyCache::clearCacheAllSmarty('*');
        } else {
            EtsRVSmartyCache::clearCacheAllSmarty('*', 'recaptcha');
            EtsRVSmartyCache::clearCacheAllSmarty('*', 'color');
            EtsRVSmartyCache::clearCacheAllSmarty('*', 'home');
        }
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[6];
        }
    }

    public function getTemplateVars($helper)
    {
        $tpl_vars = parent::getTemplateVars($helper);
        $tpl_vars['clear_cache_link'] = self::$currentIndex . '&ajax=1&action=clearAllCache&token=' . $this->token;
        return $tpl_vars;
    }

    public function ajaxProcessClearAllCache()
    {
        EtsRVSmartyCache::clearCacheAllSmarty('*');
        die(json_encode([
            'msg' => $this->l('Smarty cache is cleaned successfully', 'AdminEtsRVSettingsController')
        ]));
    }

    public function requiredFields($key, $config, $id_lang_default = null)
    {
        if (!$key) return false;

        $res = true;
        $discount_option = ($option = trim(Tools::getValue('ETS_RV_DISCOUNT_OPTION'))) && Validate::isCleanHtml($option) ? $option : '';
        $apply_discount = ($apply = trim(Tools::getValue('ETS_RV_APPLY_DISCOUNT'))) && Validate::isCleanHtml($apply) ? $apply : '';
        $recaptcha = (int)Tools::getValue('ETS_RV_RECAPTCHA_ENABLED') ? 1 : 0;
        $recaptcha_type = ($type = trim(Tools::getValue('ETS_RV_RECAPTCHA_TYPE'))) && Validate::isCleanHtml($type) ? $type : '';
        $discount_enabled = (int)Tools::getValue('ETS_RV_DISCOUNT_ENABLED') > 0;

        switch (trim($key)) {
            case 'ETS_RV_RECAPTCHA_SITE_KEY_V2':
            case 'ETS_RV_RECAPTCHA_SECRET_KEY_V2':
                $res &= $recaptcha && $recaptcha_type == 'recaptcha_v2';
                break;
            case 'ETS_RV_RECAPTCHA_SITE_KEY_V3':
            case 'ETS_RV_RECAPTCHA_SECRET_KEY_V3':
                $res &= $recaptcha && $recaptcha_type == 'recaptcha_v3';
                break;
            case 'ETS_RV_MAX_UPLOAD_PHOTO':
                $res &= (int)Tools::getValue('ETS_RV_UPLOAD_PHOTO_ENABLED');
                break;
            case 'ETS_RV_DISCOUNT_CODE':
                $res &= $discount_enabled && $discount_option == 'fixed';
                break;
            case 'ETS_RV_REDUCTION_PERCENT':
                $res &= $discount_enabled && ($discount_option == 'auto' && $apply_discount == 'percent');
                break;
            case 'ETS_RV_DISCOUNT_NAME':
            case 'ETS_RV_APPLY_DISCOUNT_IN':
                $res &= $discount_enabled && ($discount_option == 'auto');
                break;
            case 'ETS_RV_REDUCTION_AMOUNT':
                $res &= $discount_enabled && ($discount_option == 'auto' && $apply_discount == 'amount');
                break;
        }

        return $res && parent::requiredFields($key, $config, $id_lang_default);
    }

    public function validateFields($key, $config)
    {
        $discount_option = trim(Tools::getValue('ETS_RV_DISCOUNT_OPTION'));
        $apply_discount = trim(Tools::getValue('ETS_RV_APPLY_DISCOUNT'));
        $ETS_RV_WHO_POST_REVIEW = Tools::getValue('ETS_RV_WHO_POST_REVIEW');

        $res = true;

        switch (trim($key)) {
            case 'ETS_RV_MAX_UPLOAD_PHOTO':
                $res &= (int)Tools::getValue('ETS_RV_UPLOAD_PHOTO_ENABLED');
                break;
            case 'ETS_RV_REDUCTION_AMOUNT':
                $res &= ($apply_discount == 'amount' && $discount_option == 'auto');
                break;
            case 'ETS_RV_REDUCTION_PERCENT':
                $res &= ($apply_discount == 'percent' && $discount_option == 'auto');
                break;
            case 'ETS_RV_APPLY_DISCOUNT_IN':
                $res &= ($discount_option == 'auto');
                break;
            case 'ETS_RV_REVIEW_AVAILABLE_TIME':
                $res &= is_array($ETS_RV_WHO_POST_REVIEW) && count($ETS_RV_WHO_POST_REVIEW) == 1 && $ETS_RV_WHO_POST_REVIEW[0] == 'purchased';
                break;
        }

        return $res && parent::validateField($key, $config);
    }
}