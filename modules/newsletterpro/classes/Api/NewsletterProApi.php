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

abstract class NewsletterProApi
{
    /**
     * @var NewsletterProOutput
     */
    protected $output;

    /**
     * @var NewsletterProRequest
     */
    protected $request;

    /**
     * @var NewsletterProTranslate
     */
    private $translate;

    abstract public function call();

    /**
     * new NewsletterProApiCss(false, 'text/css');
     * new NewsletterProApiMailchimp(true);
     * new NewsletterProApiNewsletter(NewsletterProToken::getPublicToken(), 'text/html');.
     *
     * @param bool|string $token
     * @param string      $contentType
     *
     * @return void
     */
    public function __construct($token = true, $contentType = 'text/plain')
    {
        $this->output = new NewsletterProOutput();
        $this->request = new NewsletterProRequest();
        $this->translate = new NewsletterProTranslate($this);

        if (is_bool($token)) {
            if ($token && !NewsletterProToken::isValidToken($this->request->get('token', ''))) {
                exit('Invalid token');
            }
        } elseif (is_string($token)) {
            if (!NewsletterProToken::validateToken($this->request->get('token', ''), $token)) {
                exit('Invalid token');
            }
        }

        if (isset($contentType)) {
            header('Content-Type: '.$contentType);
        }
    }

    public function l($string)
    {
        return $this->translate->l($string);
    }

    public static function getLink($action, $params = [], $token = false, $urldecode = false, $ssl = null, $idLang = null, $idShop = null, $relativeProtocol = false)
    {
        $ownParams = [];

        if (is_bool($token)) {
            if ($token) {
                $ownParams['token'] = NewsletterProToken::getToken();
            }
        } elseif (is_string($token)) {
            $ownParams['token'] = $token;
        }

        $ownParams['action'] = $action;

        $link = Context::getContext()->link->getModuleLink(NewsletterProTools::module()->name, 'api', array_merge($ownParams, $params), $ssl, $idLang, $idShop, $relativeProtocol);

        if ($urldecode) {
            return urldecode($link);
        }

        return $link;
    }
}
