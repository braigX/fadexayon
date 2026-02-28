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

class NewsletterProTranslate
{
    public $source;

    public function __construct($source_obj)
    {
        $this->source = Tools::strtolower(is_object($source_obj) ? get_class($source_obj) : $source_obj);
    }

    public static function newInstance($source_obj)
    {
        return new self($source_obj);
    }

    public function l($string)
    {
        return Translate::getModuleTranslation(NewsletterProTools::module(), $string, $this->source);
    }
}
