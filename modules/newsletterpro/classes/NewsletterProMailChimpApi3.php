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

class NewsletterProMailChimpApi3
{
    public $key;
    public $url = 'https://<dc>.api.mailchimp.com/3.0';
    public $verify_ssl = false;

    // private $module;

    public function __construct($key, $url = null)
    {
        // $this->module = pqnp_module();

        if (isset($url)) {
            $this->url = $url;
        }

        $this->key = $key;
        $exp = explode('-', $this->key);

        if (count($exp) > 1) {
            list(, $dc) = explode('-', $this->key);
        } else {
            $dc = '';
        }

        $this->url = str_replace('<dc>', $dc, $this->url);
    }

    public function call($action, $params = [], $method = 'POST')
    {
        return $this->request($action, $params, $method);
    }

    public function callError($error, $method = 'POST')
    {
        return $this->request('', [
            'CURLOPT_HTTPHEADER' => ['X-Trigger-Error: '.$error],
        ], $method);
    }

    public function request($action, $params = [], $method = 'POST')
    {
        if (!function_exists('curl_init')) {
            throw new Exception(sprintf(pqnp_module()->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));
        }

        $url = $this->url.'/'.$action;

        $httpHeader = [
            'Content-Type: application/json',
            'Authorization: apikey '.$this->key,
        ];

        if (array_key_exists('CURLOPT_HTTPHEADER', $params)) {
            $httpHeader = array_merge($httpHeader, $params['CURLOPT_HTTPHEADER']);
        }

        $options = [
            CURLOPT_HTTPHEADER => $httpHeader,
            CURLOPT_USERAGENT => 'PHP-MCAPI/3.0',
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => NewsletterProTools::jsonEncode($params),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => $this->verify_ssl,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        $info = curl_getinfo($ch);

        $info = array_merge($info, [
            'errno' => $errno,
            'errmsg' => $errmsg,
            'content' => $content,
        ]);

        return new NewsletterProMailChimpResponse($info);
    }
}
