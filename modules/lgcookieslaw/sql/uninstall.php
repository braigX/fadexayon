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

$queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_purpose`;';
$queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_purpose_lang`;';
$queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_cookie`;';
$queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_cookie_lang`;';
$queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_user_consent`;';

Db::getInstance()->execute('START TRANSACTION');
foreach ($queries as $query) {
    if (!Db::getInstance()->execute($query)) {
        Db::getInstance()->execute('ROLLBACK');
        return false;
    }
}
Db::getInstance()->execute('COMMIT');

return true;
