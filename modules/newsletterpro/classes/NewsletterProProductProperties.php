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

class NewsletterProProductProperties extends Product
{
    public static function getProductsProperties($id_lang, $query_result)
    {
        // important to clear the cache when sending the newsletter to multiple customers and have specific prices on the groups
        if (property_exists('Product', 'productPropertiesCache') && isset(self::$productPropertiesCache)) {
            self::$productPropertiesCache = [];
        }

        if (property_exists('Product', 'producPropertiesCache') && isset(self::$producPropertiesCache)) {
            self::$producPropertiesCache = [];
        }

        return parent::getProductsProperties($id_lang, $query_result);
    }
}
