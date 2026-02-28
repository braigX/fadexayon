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

class NewsletterProToken
{
    /**
     * @return string
     */
    public static function getToken()
    {
        return Tools::encrypt(NewsletterPro::NEWSLETTER_PRO_KEY);
    }

    public static function isValidToken($token)
    {
        $token = trim((string) $token);

        return Tools::strlen($token) > 0 && $token === self::getToken();
    }

    public static function getPublicToken()
    {
        return Tools::encrypt(md5('newsletterpro_public_key'));
    }

    public static function validateToken($requestToken, $token)
    {
        $requestToken = trim((string) $requestToken);

        return Tools::strlen($requestToken) > 0 && $requestToken === $token;
    }
}
