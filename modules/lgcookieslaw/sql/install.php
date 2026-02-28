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

$queries = [];

$queries[] = '
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_purpose` (
        `id_lgcookieslaw_purpose` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(11) unsigned NOT NULL,
        `technical` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
        `locked_modules` text NOT NULL,
        `consent_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
        `consent_type` varchar(32) NULL,
        `active` tinyint(1) UNSIGNED NOT NULL DEFAULT \'1\',
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_lgcookieslaw_purpose`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
';
$queries[] = '
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_purpose_lang` (
        `id_lgcookieslaw_purpose` int(11) unsigned NOT NULL,
        `id_lang` int(11) NOT NULL,
        `name` varchar(64) NOT NULL,
        `description` text NULL,
        PRIMARY KEY (`id_lgcookieslaw_purpose`,`id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
';
$queries[] = '
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_cookie` (
        `id_lgcookieslaw_cookie` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(11) unsigned NOT NULL,
        `id_lgcookieslaw_purpose` int(11) unsigned NOT NULL,
        `name` varchar(64) NOT NULL,
        `provider` TEXT NULL,
        `provider_url` TEXT NULL,
        `install_script` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
        `script_hook` varchar(255) NULL,
        `add_script_tag` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
        `add_script_literal` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
        `script_notes` text NULL,
        `active` tinyint(1) UNSIGNED NOT NULL DEFAULT \'1\',
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_lgcookieslaw_cookie`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
';
$queries[] = '
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_cookie_lang` (
        `id_lgcookieslaw_cookie` int(11) unsigned NOT NULL,
        `id_lang` int(11) NOT NULL,
        `cookie_purpose` text NULL,
        `expiry_time` text NULL,
        `script_code` text NULL,
        PRIMARY KEY (`id_lgcookieslaw_cookie`,`id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
';
$queries[] = '
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_user_consent` (
        `id_lgcookieslaw_user_consent` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `id_shop` int(11) unsigned NOT NULL,
        `ip_address` varchar(15) NOT NULL,
        `consent_date` datetime NOT NULL,
        `download_hash` varchar(255) NOT NULL,
        `purposes` varchar(255) NOT NULL,
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id_lgcookieslaw_user_consent`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
';

Db::getInstance()->execute('START TRANSACTION');
foreach ($queries as $query) {
    if (!Db::getInstance()->execute($query)) {
        Db::getInstance()->execute('ROLLBACK');
        return false;
    }
}
Db::getInstance()->execute('COMMIT');

return true;
