<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class EtsRVCore
{
    public $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function l($string, $source = null)
    {
        return Translate::getModuleTranslation('ets_reviews', $string, $source !== null ? $source : pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function display($template, $assigns = [])
    {
        if ($assigns) {
            $this->context->smarty->assign($assigns);
        }

        return $this->context->smarty->fetch(dirname(__FILE__) . '/../views/templates/hook/' . $template);
    }

    public static function trans($text, $iso_code, $specific = null)
    {
        if (is_array($text) || trim($text) == '' || $iso_code == '' || !Validate::isLangIsoCode($iso_code)) {
            return $text;
        }
        $files_by_priority = _PS_MODULE_DIR_ . 'ets_reviews/translations/' . $iso_code . '.php';
        if (!@file_exists($files_by_priority)) {
            return $text;
        }
        $string = preg_replace("/\\\*'/", "\'", $text);
        $key = md5($string);
        $default_key = Tools::strtolower('<{ets_reviews}prestashop>' . ($specific ? Tools::strtolower($specific) : 'ets_reviews')) . '_' . $key;

        preg_match('/(\$_MODULE\[\'' . preg_quote($default_key) . '\'\]\s*=\s*\')(.*)(\';)/', Tools::file_get_contents($files_by_priority), $matches);

        if ($matches && isset($matches[2])) {
            return $matches[2];
        }
        return $text;
    }
}