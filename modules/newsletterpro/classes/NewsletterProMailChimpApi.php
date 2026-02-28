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

class NewsletterProMailChimpApi
{
    public $key;
    public $url;
    public $url_defined = 'https://<dc>.api.mailchimp.com/2.0/';
    public $verify_ssl = false;

    public $errors = [];
    public $module;

    /**
     * @var NewsletterProMailChimpApi3
     */
    protected $api3 = null;

    public function __construct($key, $url_defined = null)
    {
        $this->module = NewsletterPro::getInstance();

        if (isset($url_defined)) {
            $this->url_defined = $url_defined;
        }

        $this->api3 = new NewsletterProMailChimpApi3($key);
        $this->key = $key;
        $this->url = $this->createUrl($key);
    }

    protected function updateApiKey($key)
    {
        $this->api3 = new NewsletterProMailChimpApi3($key);
        $this->key = $key;
        $this->url = $this->createUrl($key);
    }

    private function createUrl($key)
    {
        $exp = explode('-', $key);
        if (count($exp) > 1) {
            list(, $dc) = explode('-', $key);
        } else {
            $dc = '';
        }

        return str_replace('<dc>', $dc, $this->url_defined);
    }

    public function clearErrors()
    {
        $this->errors = [];
    }

    public function call($method, $params = [])
    {
        $this->clearErrors();
        $content = $this->request($method, $params);

        if (false === $content) {
            return false;
        }

        if ($this->hasErrors()) {
            return false;
        }

        $content = NewsletterProTools::jsonDecode($content['content'], true);

        if (empty($content)) {
            $this->addError('MailChimp response is empty.');

            return false;
        } elseif (isset($content['status']) && 'error' == $content['status']) {
            $this->addError($content['error']);

            return false;
        }

        if (isset($content['errors']) && !empty($content['errors'])) {
            $this->addResponseErrors($content['errors']);
        }

        return $content;
    }

    public function request($method, $params = [])
    {
        if (function_exists('curl_init')) {
            $params['apikey'] = $this->key;
            $url = $this->url.$method.'.json';

            $options = [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_USERAGENT => 'PHP-MCAPI/2.0',
                CURLOPT_CUSTOMREQUEST => 'POST',
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
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            $info = curl_getinfo($ch);

            $info['errno'] = $err;
            $info['errmsg'] = $errmsg;
            $info['content'] = $content;

            if (200 != (int) $info['http_code']) {
                $response = NewsletterProTools::jsonDecode($content, true);

                if (isset($response['status']) && 'error' == $response['status']) {
                    $this->addError($response['error']);
                } else {
                    $this->addError('The HTTP response code is not 200.');
                }
            }

            return $info;
        } else {
            $this->addError(sprintf($this->module->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));
        }

        return false;
    }

    public function addError($error, $code = null)
    {
        $add_error = [
            'code' => $code,
            'error' => $error,
            // this causes problems if an error occurred when synchronizing the lists
            // 'error' => Tools::displayError($error),
        ];

        $this->errors[] = $add_error;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors($only_errors = false, $collapse_same_code = false)
    {
        $errors = $this->errors;

        if ($collapse_same_code) {
            $errors_collapse = [];
            $errors_coldes = [];

            foreach ($errors as $error) {
                if (!in_array($error['code'], $errors_coldes)) {
                    $errors_collapse[] = $error;
                    $errors_coldes[] = $error['code'];
                }
            }

            $errors = $errors_collapse;
        }

        $return_errors = [];
        if ($only_errors) {
            foreach ($errors as $error) {
                if (is_array($error) && isset($error['error'])) {
                    $return_errors[] = $error['error'];
                } else {
                    $return_errors[] = $error;
                }
            }
        } else {
            $return_errors = $errors;
        }

        return $return_errors;
    }

    public function addResponseErrors($errors)
    {
        foreach ($errors as $error) {
            $this->addError($error['error'], $error['code']);
        }
    }

    public function addParams(&$params, $data)
    {
        foreach ($data as $key => $item) {
            $this->addParam($params, $key, $item);
        }
    }

    public function addParam(&$params, $key, $name)
    {
        $params[$key] = $name;
    }

    public function mergeErrors(&$errors)
    {
        $errors = array_merge($errors, $this->getErrors(true));
    }

    public static function grep($array, $name)
    {
        $return_array = [];
        foreach ($array as $value) {
            if (isset($value[$name])) {
                $return_array[] = $value[$name];
            }
        }

        return $return_array;
    }

    public static function makeDate($date, $format = 'm/d/Y')
    {
        return date($format, strtotime($date));
    }

    public static function formatPhone($phone)
    {
        $phone = explode(' ', trim(preg_replace('/[()\s.-]+/', ' ', $phone)));
        $result = '';
        $len = count($phone);
        $i = 0;
        foreach ($phone as $value) {
            $result .= $value;

            if (($i >= $len - 3) && ($i < $len - 1)) {
                $result .= '-';
            } else {
                $result .= ' ';
            }
            ++$i;
        }

        return $result;
    }

    public static function searchFind($respose)
    {
        if (false === $respose) {
            return false;
        }

        return true;
    }

    public static function arrayMerge(&$array1, $array2)
    {
        foreach ($array2 as $key => $value) {
            $array1[$key] = $value;
        }
    }
}
