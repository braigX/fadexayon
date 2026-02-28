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

class NewsletterProResponse
{
    private $errors = [];

    private $form_errors = [];

    private $data = [];

    public static function newInstance()
    {
        return new self();
    }

    public function error($message)
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                $this->errors[] = $msg;
            }

            return $this;
        }

        $this->errors[] = $message;

        return $this;
    }

    public function formError(array $errors)
    {
        foreach ($errors as $key => $message) {
            $this->form_errors[$key] = $message;
        }

        return $this;
    }

    public function success()
    {
        return empty($this->errors) && empty($this->form_errors);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function setMultiple($data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function json($data = false)
    {
        if (isset($data) && $data) {
            return NewsletterProTools::jsonEncode($this->data);
        }

        return NewsletterProTools::jsonEncode($this->display($data));
    }

    public function display($data = false)
    {
        if (isset($data) && $data) {
            return $this->data;
        }

        return [
            'status' => $this->success(),
            'success' => $this->success(),
            'errors' => $this->errors,
            'formErrors' => (!empty($this->form_errors) ? $this->form_errors : null),
            'data' => $this->data,
        ];
    }

    public function output($data = false)
    {
        header('Content-Type: application/json');
        echo NewsletterProTools::jsonEncode($this->display($data));
        exit;
    }
}
