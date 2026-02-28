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

function upgrade_module_2_0_0($module)
{
    $queries = [];

    $queries[] = '
        DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_lang`;
    ';
    $queries[] = '
        TRUNCATE `' . _DB_PREFIX_ . 'lgcookieslaw_cookie`;
    ';
    $queries[] = '
        TRUNCATE `' . _DB_PREFIX_ . 'lgcookieslaw_cookie_lang`;
    ';
    $queries[] = '
        TRUNCATE `' . _DB_PREFIX_ . 'lgcookieslaw_purpose`;
    ';
    $queries[] = '
        TRUNCATE `' . _DB_PREFIX_ . 'lgcookieslaw_purpose_lang`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie` ADD `install_script` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\'
            AFTER `provider_url`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie` ADD `script_hook` varchar(255) NULL
            AFTER `install_script`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie` ADD `add_script_tag` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\'
            AFTER `script_hook`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie` ADD `add_literal_tag` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\'
            AFTER `add_script_tag`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie` ADD `script_notes`TEXT NOT NULL
            AFTER `add_literal_tag`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_cookie_lang` ADD `script_code` text NULL
            AFTER `expiry_time`;
    ';
    $queries[] = '
        ALTER TABLE `' . _DB_PREFIX_ .
            'lgcookieslaw_purpose` DROP COLUMN `js_code`;
    ';
    $queries[] = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_user_consent` (
            `id_lgcookieslaw_user_consent` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `ip_address` varchar(15) NOT NULL,
            `consent_date` datetime NOT NULL,
            `download_hash` varchar(255) NOT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_lgcookieslaw_user_consent`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    ';

    foreach ($queries as $query) {
        Db::getInstance()->execute($query);
    }

    Configuration::deleteByName('PS_LGCOOKIES_HIDDEN');
    Configuration::deleteByName('PS_LGCOOKIES_HOOK');
    Configuration::deleteByName('PS_LGCOOKIES_BLOCK');
    Configuration::deleteByName('PS_LGCOOKIES_TIMELIFE');
    Configuration::deleteByName('PS_LGCOOKIES_NAME');
    Configuration::deleteByName('PS_LGCOOKIES_POSITION');
    Configuration::deleteByName('PS_LGCOOKIES_DIVCOLOR');
    Configuration::deleteByName('PS_LGCOOKIES_OPACITY');
    Configuration::deleteByName('PS_LGCOOKIES_SHADOWCOLOR');
    Configuration::deleteByName('PS_LGCOOKIES_FONTCOLOR');
    Configuration::deleteByName('PS_LGCOOKIES_BTN1_BG_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_BTN1_FONT_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_CMS');
    Configuration::deleteByName('PS_LGCOOKIES_CMS_TARGET');
    Configuration::deleteByName('PS_LGCOOKIES_SHOW_REJECT_ALL_BTN');
    Configuration::deleteByName('PS_LGCOOKIES_CMS_SHOW_BANNER');

    $configurations_list = [
        'PS_LGCOOKIES_BANNER_HOOK' => [
            'default_value' => 'footer',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BLOCK_NAVIGATION' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_SHOW_CLOSE_BTN' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_SHOW_FIXED_BTN' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_FIXED_BTN_POSITION' => [
            'default_value' => 'left',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_SHOW_BANNER_IN_CMS' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_SAVE_USER_CONSENT' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_DELETE_USER_CONSENT' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_ID_ANALYTICS_PURPSE' => [
            'default_value' => 0,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_CONSENT_MODE_CLASS' => [
            'default_value' => 'lggoogleanalytics-accept',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_COOKIE_TIMELIFE' => [
            'default_value' => '31536000',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_COOKIE_NAME' => [
            'default_value' => 'lgcookieslaw',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_POSITION' => [
            'default_value' => '3',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_BG_COLOR' => [
            'default_value' => '#3B3B3B',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_BG_OPACITY' => [
            'default_value' => '0.9',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_SHADOWCOLOR' => [
            'default_value' => '#707070',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_FONTCOLOR' => [
            'default_value' => '#FFFFFF',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_BANNER_MESSAGE' => [
            'default_value' => [],
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => true,
        ],
        'PS_LGCOOKIES_ACPT_BTN_TITLE' => [
            'default_value' => [],
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_ACPT_BTN_BG_COLOR' => [
            'default_value' => '#8BC954',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_ACPT_BTN_FONT_COLOR' => [
            'default_value' => '#FFFFFF',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_INFO_LINK_TITLE' => [
            'default_value' => [],
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_INFO_LINK_ID_CMS' => [
            'default_value' => Configuration::get('PS_CONDITIONS_CMS_ID'),
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_INFO_LINK_TARGET' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_SHOW_RJCT_BTN' => [
            'default_value' => true,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_RJCT_BTN_TITLE' => [
            'default_value' => [],
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_RJCT_BTN_BG_COLOR' => [
            'default_value' => '#8BC954',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
        'PS_LGCOOKIES_RJCT_BTN_FONT_COLOR' => [
            'default_value' => '#FFFFFF',
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
    ];

    $installation_defaults = $module->getInstallationDefaults();

    foreach (Language::getLanguages() as $lang) {
        $iso_code = $lang['iso_code'];
        $id_lang = $lang['id_lang'];

        $accept_button_title_index = 'PS_LGCOOKIES_ACPT_BTN_TITLE';

        $accept_button_title_default_value = isset($installation_defaults[$accept_button_title_index][$iso_code]) ?
            $installation_defaults[$accept_button_title_index][$iso_code] :
            $installation_defaults[$accept_button_title_index]['en'];

        $configurations_list[$accept_button_title_index]['default_value'][(int) $id_lang] =
            $accept_button_title_default_value;

        $info_link_title_index = 'PS_LGCOOKIES_INFO_LINK_TITLE';

        $info_link_title_default_value = isset($installation_defaults[$info_link_title_index][$iso_code]) ?
            $installation_defaults[$info_link_title_index][$iso_code] :
            $installation_defaults[$info_link_title_index]['en'];

        $configurations_list[$info_link_title_index]['default_value'][(int) $id_lang] =
            $info_link_title_default_value;

        $reject_button_title_index = 'PS_LGCOOKIES_RJCT_BTN_TITLE';

        $reject_button_title_default_value = isset($installation_defaults[$reject_button_title_index][$iso_code]) ?
            $installation_defaults[$reject_button_title_index][$iso_code] :
            $installation_defaults[$reject_button_title_index]['en'];

        $configurations_list[$reject_button_title_index]['default_value'][(int) $id_lang] =
            $reject_button_title_default_value;

        $banner_message_index = 'PS_LGCOOKIES_BANNER_MESSAGE';

        $banner_message_default_value = isset($installation_defaults[$banner_message_index][$iso_code]) ?
            $installation_defaults[$banner_message_index][$iso_code] :
            $installation_defaults[$banner_message_index]['en'];

        $configurations_list[$banner_message_index]['default_value'][(int) $id_lang] =
            $banner_message_default_value;
    }

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

    $module->installationDefaults();

    if ($module->uninstallOverrides()) {
        $module->installOverrides();
    }

    return true;
}
