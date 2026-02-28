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
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * @param \Ets_Seo $instance
 *
 * @return bool
 */
function upgrade_module_2_6_9($instance)
{
    if (version_compare(_PS_VERSION_, '8.1.0', '>=')) {
        $instance->registerHook('actionProductFormBuilderModifier');
    }
    $keys = ['ETS_SEO_ENABLE_RECORD_404_REQUESTS'];
    if ($shops = Shop::getShops()) {
        foreach ($shops as $shop) {
            foreach ($keys as $key) {
                if (!Configuration::hasKey($key)) {
                    Configuration::updateValue($key, 1, false, null, $shop['id_shop']);
                }
            }
        }
    }

    return true;
}
