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

function upgrade_module_2_0_3()
{
    $query = '
        ALTER TABLE `' . _DB_PREFIX_ . 'lgcookieslaw_user_consent` ADD `purposes` varchar(255) NOT NULL
            AFTER `download_hash`;
    ';

    Db::getInstance()->execute($query);

    $configurations_list = [
        'PS_LGCOOKIES_USER_CONSENT_PDF' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_LOAD_FANCYBOX' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
    ];

    foreach (Shop::getShops(false, null, true) as $id_shop) {
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

    return true;
}
