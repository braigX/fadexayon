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

class NewsletterProMailChimpResponse
{
    private $info;

    private $content;

    private $http_code;

    public function __construct(array $info)
    {
        $this->info = $info;

        $this->http_code = (int) $this->info['http_code'];
        $this->content = NewsletterProTools::jsonDecode($info['content'], true);

        if (!isset($this->content) && 204 !== $this->http_code) {
            throw new Exception('Invalid Response.');
        }
    }

    public function getContent($key = null)
    {
        if (isset($key)) {
            return $this->content[$key];
        }

        return $this->content;
    }

    public function success()
    {
        return 200 === $this->http_code || 204 === $this->http_code;
    }

    public function getErrors($simple = false)
    {
        $detail = '';
        $errors = [];

        if (!$this->success()) {
            $detail = '['.$this->content['status'].'] '.$this->content['title'].': '.$this->content['detail'];
            if (array_key_exists('errors', $this->content)) {
                $errors = $this->content['errors'];
            }
        }

        switch ($this->http_code) {
            case 400:
                break;
            case 401:
                break;
            case 403:
                break;
            case 404:
                break;
            case 405:
                break;
            case 414:
                break;
            case 422:
                break;
            case 429:
                break;
            case 500:
                break;
            case 503:
                break;
        }

        if ($simple) {
            return array_map(function ($error) {
                return $error['detail'];
            }, $errors);
        }

        return [
            'http_code' => $this->http_code,
            'detail' => $detail,
            'errors' => $errors,
        ];
    }
}
