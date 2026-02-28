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

class OutlookService
{
    public function sendMail($recipient, $subject, $body)
    {
        $accessToken = Configuration::get('ETS_ABANCART_MAIL_ACCESS_TOKEN_HOTMAIL');
        if (!$this->isAccessTokenValid()) {
            $accessToken = $this->refreshAccessToken();
        }

        $url = "https://graph.microsoft.com/v1.0/me/sendMail";
        $emailData = [
            "message" => [
                "subject"     => $subject,
                "body"        => ["contentType" => "HTML", "content" => $body],
                "toRecipients" => [["emailAddress" => ["address" => $recipient]]],
            ]
        ];

        $response = Tools::file_get_contents($url, false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Authorization: Bearer $accessToken\r\n" .
                    "Content-type: application/json\r\n",
                'content' => json_encode($emailData)
            ]
        ]));

        return json_decode($response, true);
    }

    private function isAccessTokenValid()
    {
        return time() < Configuration::get('ETS_ABANCART_MAIL_TOKEN_EXPIRES_HOTMAIL');
    }

    private function refreshAccessToken()
    {
        $clientId = Configuration::get('ETS_ABANCART_MAIL_CLIENT_ID_HOTMAIL');
        $clientSecret = Configuration::get('ETS_ABANCART_MAIL_CLIENT_SECRET_HOTMAIL');
        $refreshToken = Configuration::get('ETS_ABANCART_MAIL_REFRESH_TOKEN_HOTMAIL');

        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        $data = [
            'client_id'     => $clientId,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_secret' => $clientSecret,
        ];

        $response = Tools::file_get_contents($url, false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ]));

        $tokenData = json_decode($response, true);
        Configuration::updateValue('ETS_ABANCART_MAIL_ACCESS_TOKEN_HOTMAIL', $tokenData['access_token']);
        Configuration::updateValue('ETS_ABANCART_MAIL_TOKEN_EXPIRES_HOTMAIL', time() + $tokenData['expires_in']);

        return $tokenData['access_token'];
    }
}
