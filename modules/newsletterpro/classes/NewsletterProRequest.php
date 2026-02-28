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

class NewsletterProRequest
{
    const TYPE_POST = 1;

    const TYPE_GET = 2;

    private $data_get = [];

    private $data_post = [];

    public function __construct()
    {
        $this->data_get = [];
        $this->data_post = [];

        foreach ($_GET as $key => $value) {
            $this->data_get[$key] = $value;
        }

        foreach ($_POST as $key => $value) {
            $this->data_post[$key] = $value;
        }
    }

    public static function newInstance()
    {
        return new self();
    }

    public function set($key, $value, $set_type = null)
    {
        $was_set = false;

        if (isset($set_type)) {
            if (self::TYPE_GET == $set_type) {
                $this->data_get[$key] = $value;
            } else {
                $this->data_post[$key] = $value;
            }

            return $this;
        } else {
            if (array_key_exists($key, $this->data_get)) {
                $this->data_get[$key] = $value;
                $was_set = true;
            }

            if (array_key_exists($key, $this->data_post)) {
                $this->data_post[$key] = $value;
                $was_set = true;
            }

            if (!$was_set) {
                $this->data_post[$key] = $value;
            }
        }

        return $this;
    }

    public function setPost($key, $value)
    {
        $this->data_post[$key] = $value;

        return $this;
    }

    public function setGet($key, $value)
    {
        $this->data_get[$key] = $value;

        return $this;
    }

    public function get($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->data_get)) {
            return $this->data_get[$key];
        }

        if (array_key_exists($key, $this->data_post)) {
            return $this->data_post[$key];
        }

        return $defaultValue;
    }

    public function getPrefix($key, $defaultValue = null)
    {
        return $this->get(_NEWSLETTER_PREFIX_.$key, $defaultValue);
    }

    public function dataGet()
    {
        return $this->data_get;
    }

    public function dataPost()
    {
        return $this->data_post;
    }

    public function has($key)
    {
        if (array_key_exists($key, $this->data_get)) {
            return true;
        } elseif (array_key_exists($key, $this->data_post)) {
            return true;
        }

        return false;
    }

    public function hasPrefix($key)
    {
        return $this->has(_NEWSLETTER_PREFIX_.$key);
    }

    public function validate(&$errors = [], &$form_errors = [], $fields = [])
    {
        $data = $this->grep(array_keys($fields));
        $validate = NewsletterProValidate::newInstance()->set($data);

        $success = $validate->success($errors, $form_errors, $fields);

        $validate_data = $validate->get();

        if (is_array($validate_data) && !empty($validate_data)) {
            foreach ($validate_data as $filed_name => $filed_value) {
                if (array_key_exists($filed_name, $this->data_post)) {
                    $this->data_post[$filed_name] = $filed_value;
                }

                if (array_key_exists($filed_name, $this->data_get)) {
                    $this->data_get[$filed_name] = $filed_value;
                }
            }
        }

        return $success;
    }

    public function data($type = null, $grep_fields = [])
    {
        $data = [];
        $data_grep = [];

        if (!isset($type)) {
            $data = array_merge($this->data_get, $this->data_post);
        } elseif (self::TYPE_POST == $type) {
            $data = $this->data_post;
        } elseif (self::TYPE_GET == $type) {
            $data = $this->data_get;
        }

        if (empty($grep_fields)) {
            return $data;
        }

        foreach ($grep_fields as $filed_name) {
            if (array_key_exists($filed_name, $data)) {
                $data_grep[$filed_name] = $data[$filed_name];
            }
        }

        return $data_grep;
    }

    public function grep($fields = [])
    {
        $data = [];

        foreach ($fields as $filed_name) {
            if (array_key_exists($filed_name, $this->data_get)) {
                $data[$filed_name] = $this->data_get[$filed_name];
            }

            if (array_key_exists($filed_name, $this->data_post)) {
                $data[$filed_name] = $this->data_post[$filed_name];
            }
        }

        return $data;
    }
}
