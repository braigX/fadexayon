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

use PQNP\CategoryTree;

class NewsletterProAppStorage
{
    private static $extend_data = [];

    public static function get($entry)
    {
        $module = NewsletterProTools::module();
        $context = Context::getContext();

        switch ($entry) {
            case 'app':
                $iso = file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$context->language->iso_code.'.js') ? $context->language->iso_code : 'en';
                if (defined('_PS_CORE_DIR_')) {
                    $iso = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$context->language->iso_code.'.js') ? $context->language->iso_code : 'en';
                }

                return array_merge([
                    'categories' => (new CategoryTree())->getTree(),
                    'tinymce' => [
                        'iso' => $iso,
                        'path_css' => _THEME_CSS_DIR_,
                        'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
                    ],
                ], self::$extend_data);

            case 'app_front':
                return array_merge([
                    'ajax_url' => $context->link->getModuleLink('newsletterpro', 'ajax', []),
                    'config' => [
                        'CROSS_TYPE_CLASS' => pqnp_config('CROSS_TYPE_CLASS'),
                    ],
                ], self::$extend_data);
        }

        throw new Exception(sprintf('The entry "%s" is not defined.', $entry));
    }

    public static function extend($data = [])
    {
        self::$extend_data = array_merge(self::$extend_data, $data);
    }
}
