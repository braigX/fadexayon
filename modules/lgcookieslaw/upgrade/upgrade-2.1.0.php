<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_0($module)
{
    $queries = [];

    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ . 'lgcookieslaw_purpose`
            ADD `consent_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\'
            AFTER `locked_modules`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ . 'lgcookieslaw_purpose`
            ADD `consent_type` varchar(32) NULL
            AFTER `consent_mode`;
    ';

    foreach ($queries as $query) {
        Db::getInstance()->execute($query);
    }

    $configurations_list = [
        'PS_LGCOOKIES_ANONYMIZE_UC_IP' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_CONSENT_MODE' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
    ];

    $shops = Shop::getShops(false, null, true);

    foreach ($shops as $id_shop) {
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        foreach ($configurations_list as $configuration_name => $configuration) {
            Configuration::updateValue(
                $configuration_name,
                $configuration['default_value'],
                $configuration['html'],
                (int) $id_shop_group,
                (int) $id_shop
            );
        }
    }

    Configuration::deleteByName('PS_LGCOOKIES_USER_CONSENT_PDF');
    Configuration::deleteByName('PS_LGCOOKIES_CONSENT_MODE_CLASS');
    Configuration::deleteByName('PS_LGCOOKIES_ID_ANALYTICS_PURPSE');

    $module->unregisterHook(Hook::getIdByName('header'), $shops);
    $module->registerHook('displayHeader', $shops);
    $module->updatePosition(Hook::getIdByName('displayHeader'), 0, 1);

    return true;
}
