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


class EtsAbancartCache extends ObjectModel
{
    public function clearCacheAllSmarty($template, $cache_id = null)
    {
        return Module::getInstanceByName(_ETS_ABANCART_NAME_)->clearCache($template, _ETS_ABANCART_NAME_ . ($cache_id !== null ? '|' . trim($cache_id, '|') : ''));
    }

    public function clearCacheBoSmarty($template, $name = null, $before = null, $after = null)
    {
        return Module::getInstanceByName(_ETS_ABANCART_NAME_)->clearCache($template, self::getCachedId($name, $before, $after));
    }

    public function clearCacheFoSmarty($template, $name = null, $before = null, $after = null)
    {
        $module = Module::getInstanceByName(_ETS_ABANCART_NAME_);
        return $module->clearCache($template, $module->getCacheId($name, $before, $after));
    }

    public static function getCachedId($name = null, $before = null, $after = null)
    {
        $context = Context::getContext();
        $cache_id = _ETS_ABANCART_NAME_ . (trim(Tools::strtolower($name)) ? '|' . trim(Tools::strtolower($name)) : '') . (is_array($before) ? '|' . implode('|', $before) : ($before ? '|' . trim($before, '|') : ''));
        if (Configuration::get('PS_SSL_ENABLED')) {
            $cache_id .= '|' . (int)Tools::usingSecureMode();
        }
        if (Shop::isFeatureActive()) {
            $cache_id .= '|' . (int)$context->shop->id;
        }
        if (isset($context->employee)) {
            $cache_id .= '|' . (int)$context->employee->id;
        }
        if (Language::isMultiLanguageActivated()) {
            $cache_id .= '|' . (int)$context->language->id;
        }
        return $cache_id . (is_array($after) ? '|' . implode('|', $after) : ($after ? '|' . trim($after, '|') : ''));
    }
}