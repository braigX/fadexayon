<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */

namespace Teamwant\Prestashop17\Redis\Classes;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;

trait TeamwantRedisCacheConfig
{
    /**
     * Dane konfiguracyjne na potrzeby frontu
     *
     * @return array
     */
    public static function getCacheConfigurationData()
    {
        // uzywamy configuration get poniewaz configuration multiple uzywa tych samych danych
        return [
            // Use cache for stock manager
            'use_cache_for_stock_manager' => \Configuration::get('twredis_use_cache_for_stock_manager', null, null, null, 1),

            // Use cache for hook list
            'use_cache_for_hook_list' => \Configuration::get('twredis_use_cache_for_hook_list', null, null, null, 1),
        ];
    }

    /**
     * @throws \PrestaShopException
     *
     * @return string
     */
    public function getContent()
    {
        $this->postProcess();

        return $this->getBackofficeConfigurationContent();
    }

    /**
     * Save form data.
     *
     * @throws \PrestaShopException
     */
    protected function postProcess()
    {
        if (\Tools::getIsset('submit_twredis_cache_config')) {
            $form_values = $this->getBackofficeConfigurationContentData();

            foreach (array_keys($form_values) as $key) {
                if (\Tools::getIsset($key)) {
                    \Configuration::updateValue($key, \Tools::getValue($key, 0));
                }
            }

            \Tools::clearAllCache();
            \Hook::exec('actionClearCompileCache');
            \Tools::clearAllCache();
        }

        if (\Tools::getIsset('submit_twredis_cache_disable')) {
            $form_values = $this->getBackofficeConfigurationContentData();

            foreach (array_keys($form_values) as $key) {
                if (\Tools::getIsset($key)) {
                    \Configuration::updateValue($key, \Tools::getValue($key, 0));
                }
            }

            \Tools::clearAllCache();
            \Hook::exec('actionClearCompileCache');
            \Tools::clearAllCache();
        }

        if (\Tools::getIsset('submit_twredis_cache_blacklist')) {
            $form_values = $this->getBackofficeConfigurationContentData();

            foreach (array_keys($form_values) as $key) {
                if (\Tools::getIsset($key)) {
                    \Configuration::updateValue($key, \Tools::getValue($key, 0));
                }
            }

            \Tools::clearAllCache();
            \Hook::exec('actionClearCompileCache');
            \Tools::clearAllCache();
        }
    }
}
