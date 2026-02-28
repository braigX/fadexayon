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


class Ets_abandonedcartCallbackModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->template = 'module:ets_abandonedcart/views/templates/front/callback.tpl';
    }
    public function initContent()
    {
        parent::initContent();

        $code = Tools::getValue('code');
        $state = Tools::getValue('state');

        if (!$code || $state !== Tools::getToken(true)) {
            die($this->module->l('Authentication failed.', 'callback'));
        }

        $clientId = Configuration::get('ETS_ABANCART_MAIL_CLIENT_ID_HOTMAIL');
        $clientSecret = Configuration::get('ETS_ABANCART_MAIL_CLIENT_SECRET_HOTMAIL');
        $redirectUri = $this->context->link->getModuleLink('ets_abandonedcart', 'callback');

        $response = $this->fetchAccessToken($clientId, $clientSecret, $redirectUri, $code);
        $this->saveAccessToken($response);

        $this->context->smarty->assign([
            'locale' => $this->context->language->locale ?: $this->context->language->iso_code,
            'css' => $this->context->link->getMediaLink($this->module->getPathUri(). 'views/css/page-callback.css'),
            'js' => $this->context->link->getMediaLink($this->module->getPathUri() . 'views/js/page-callback.js'),
        ]);

        $this->setTemplate($this->template);
    }

    private function fetchAccessToken($clientId, $clientSecret, $redirectUri, $code)
    {
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        $data = [
            'client_id' => $clientId,
            'scope' => 'https://graph.microsoft.com/Mail.Send offline_access',
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'client_secret' => $clientSecret,
            'code' => $code,
        ];

        $response = Tools::file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ]));

        $responseDecoded = json_decode($response, true);
        if (!isset($responseDecoded['access_token'])) {
            $this->context->smarty->assign('error', (isset($responseDecoded['error_description']) ? $responseDecoded['error_description'] : $this->module->l('Failed to fetch access token.', 'callback')));
        }

        return $responseDecoded;
    }

    private function saveAccessToken($response)
    {
        if (!empty($response)) {
            Configuration::updateValue('ETS_ABANCART_MAIL_ACCESS_TOKEN_HOTMAIL', $response['access_token']);
            Configuration::updateValue('ETS_ABANCART_MAIL_REFRESH_TOKEN_HOTMAIL', $response['refresh_token']);
            Configuration::updateValue('ETS_ABANCART_MAIL_TOKEN_EXPIRES_HOTMAIL', time() + $response['expires_in']);
        }
    }
}