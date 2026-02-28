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
class LGCookiesLawUserConsent extends ObjectModel
{
    public $id_shop;
    public $ip_address;
    public $consent_date;
    public $download_hash;
    public $purposes;
    public $date_add;

    public static $definition = [
        'table' => 'lgcookieslaw_user_consent',
        'primary' => 'id_lgcookieslaw_user_consent',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'ip_address' => ['type' => self::TYPE_STRING, 'required' => true],
            'consent_date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'download_hash' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'purposes' => ['type' => self::TYPE_STRING, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $this->id_shop = $this->id_shop ?: (int) $id_shop;

        return parent::add($autodate, $null_values);
    }

    public static function existDownloadHash($download_hash, $id_shop = null)
    {
        $context = Context::getContext();

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('COUNT(a.`id_lgcookieslaw_user_consent`)');
        $query->from('lgcookieslaw_user_consent', 'a');
        $query->where('a.`download_hash` = \'' . pSQL($download_hash) . '\'');
        $query->where('a.`id_shop` = ' . (int) $id_shop);

        return (bool) Db::getInstance()->getValue($query);
    }

    public static function getExpiredUserConsents()
    {
        $cookie_lifetime_fo = Configuration::get('PS_LGCOOKIES_COOKIE_TIMELIFE')
            ? (int) Configuration::get('PS_LGCOOKIES_COOKIE_TIMELIFE')
            : 31536000;

        $query = new DbQuery();

        $query->select(self::$definition['primary']);
        $query->from('lgcookieslaw_user_consent', 'a');
        $query->where('a.`consent_date` < NOW() - INTERVAL ' . (int) $cookie_lifetime_fo . ' SECOND');

        return Db::getInstance()->executeS($query);
    }

    public static function getIdByDownloadHash($download_hash, $id_shop = null)
    {
        $context = Context::getContext();

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.`id_lgcookieslaw_user_consent`');
        $query->from('lgcookieslaw_user_consent', 'a');
        $query->where('a.`download_hash` = \'' . pSQL($download_hash) . '\'');
        $query->where('a.`id_shop` = ' . (int) $id_shop);

        return Db::getInstance()->getValue($query);
    }
}
