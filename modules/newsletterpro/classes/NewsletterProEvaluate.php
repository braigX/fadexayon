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

class NewsletterProEvaluate
{
    private $success;

    public function __construct()
    {
        $this->success = [];
    }

    public static function newInstance()
    {
        return new self();
    }

    public function add($value)
    {
        $this->success[] = (bool) $value;
    }

    public function success()
    {
        return !in_array(false, $this->success);
    }
}
