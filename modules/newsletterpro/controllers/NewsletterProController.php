<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProController
{
    public $content = '';

    public $context;

    public $controller;

    public $module;

    public $token;

    public $js_data = [];

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->token = Tools::getAdminTokenLite($this->context->controller->controller_name);
        $this->module = NewsletterPro::getInstance();
        $this->controller = &$this->context->controller;
        $this->response = NewsletterProAjaxResponse::newInstance();
    }

    public function initContent()
    {
        return '';
    }

    public function getContent()
    {
        return $this->content;
    }

    public function postProcess()
    {
    }

    public function setMedia()
    {
    }

    public function response($defalut_variables = [])
    {
        return new NewsletterProAjaxResponse($defalut_variables);
    }

    public function display($str, $json = false)
    {
        if ($json) {
            @header('Content-Type: application/json');
        }

        echo $str;
        exit;
    }

    public function l($string)
    {
        return Translate::getModuleTranslation($this->module, $string, Tools::getValue('controller'));
    }
}
