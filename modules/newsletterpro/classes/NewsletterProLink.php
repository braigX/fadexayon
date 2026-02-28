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

class NewsletterProLink
{
    // TODO: change all the links to this one because on some websites the generated links are not working
    public static function createUrl($controller, $params = [], $urldecode = false, $id_lang = null, $id_shop = null)
    {
        if (is_null($id_lang) || 0 == (int) $id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $shop = new Shop((int) $id_shop);

        $rewrite = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        $tial = http_build_query($params);

        $base_uri = __PS_BASE_URI__;
        $default_shop_url = Tools::getHttpHost(true).$base_uri;
        $shop_url = '';

        if ((bool) Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && Validate::isLoadedObject($shop)) {
            $shop_url = $shop->getBaseURL();
        } else {
            $shop_url = $default_shop_url;
        }

        $len = Tools::strlen($shop_url);

        if (isset($shop_url[$len - 1])) {
            $char = $shop_url[$len - 1];
            if ('/' !== $char) {
                $shop_url .= '/';
            }
        }

        $language_iso = Language::isMultiLanguageActivated($id_shop) ? Language::getIsoById($id_lang).'/' : '';

        if ($rewrite) {
            $link = $shop_url.$language_iso.'module/'.NewsletterProTools::module()->name.'/'.(string) $controller.(Tools::strlen($tial) > 0 ? '?' : '').$tial;
        } else {
            $link = $shop_url.'index.php?fc=module&module='.NewsletterProTools::module()->name.'&controller='.(string) $controller.'&id_lang='.(int) $id_lang.(Tools::strlen($tial) > 0 ? '&' : '').$tial;
        }

        if ($urldecode) {
            return urldecode($link);
        }

        return $link;
    }
}
