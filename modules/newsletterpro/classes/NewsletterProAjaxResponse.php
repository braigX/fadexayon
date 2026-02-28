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

class NewsletterProAjaxResponse
{
    public $errors = [];

    public $data = [
            'success' => false,
            'errors' => [],
        ];

    public function __construct($default_variables = [])
    {
        if (!empty($default_variables)) {
            $this->data = array_merge($this->data, $default_variables);
        }
    }

    public static function newInstance($default_variables = [])
    {
        return new self($default_variables);
    }

    public function set($key, $val)
    {
        $this->data[$key] = $val;

        return $this;
    }

    public function setArray($array)
    {
        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($val)
    {
        $this->errors[] = $val;

        return $this;
    }

    public function mergeErrors($errors = [])
    {
        $this->errors = array_merge($this->errors, $errors);

        return $this;
    }

    public function success()
    {
        return empty($this->errors);
    }

    public function display()
    {
        if ($this->success()) {
            $this->data['status'] = true;
            $this->data['success'] = true;
            $this->data['errors'] = &$this->errors;
        } else {
            $this->data['success'] = false;
            $this->data['status'] = false;
            $this->data['errors'] = &$this->errors;
        }

        return self::jsonEncode($this->data);
    }

    public static function jsonEncode($array)
    {
        @header('Content-Type: application/json');

        return NewsletterProTools::jsonEncode($array);
    }

    public function displayArray()
    {
        return $this->data;
    }
}
