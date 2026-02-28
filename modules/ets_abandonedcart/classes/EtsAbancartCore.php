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


class EtsAbancartCore
{
    public $module;
    public $smarty;
    public $context;
    public $cipherTool;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('ets_abandonedcart');
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty;
        }
        if (class_exists('PhpEncryption'))
            $this->cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);
        else
            $this->cipherTool = new Rijndael(_NEW_COOKIE_KEY_, _COOKIE_IV_);
    }

    public function l($string, $source = null)
    {
        return Translate::getModuleTranslation('ets_abandonedcart', $string, $source == null ? pathinfo(__FILE__, PATHINFO_FILENAME) : $source);
    }

    public function display($template)
    {
        if (!$this->module)
            return '';
        return $this->module->display($this->module->getLocalPath(), $template);
    }

    public function encrypt($content)
    {
        return $this->cipherTool->encrypt($content);
    }

    public function decrypt($content)
    {
        return $this->cipherTool->decrypt($content);
    }

    public function getCanonicalUrl($url, $params = [])
    {
        if (!Configuration::get('ETS_ABANCART_EMAIL_GENERATE_URL')) {
            return $url;
        }
        $rewrite = [
            'url' => $url,
            'params' => $params
        ];
        return $this->context->link->getModuleLink($this->module->name, 'url', ['_ab' => $this->encrypt(json_encode($rewrite))], Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
    }
}