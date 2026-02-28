<?php
/**
 * Copyright 2023 LÍNEA GRÁFICA E.C.E S.L.
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
class LGCanonicalurlsObj
{
    /** @var int */
    const LGOT_HOME = 0;

    /** @var int */
    const LGOT_PRODUCT = 1;

    /** @var int */
    const LGOT_CATEGORY = 2;

    /** @var int */
    const LGOT_CMS = 3;

    /** @var int */
    const LGOT_SUPPLIER = 4;

    /** @var int */
    const LGOT_MANUFACTURER = 5;

    /** @var int */
    const LGCU_AUTO = 1;

    /** @var int */
    const LGCU_CUSTOM = 2;

    /** @var int */
    const LGCU_DISABLED = 3;

    /** @var string */
    const LG_UNKNOWN = 'unknown';

    public static function update($object_id, $type_object, $tipo, $urls = [], $params = null)
    {
        $queries = [];

        $canonical_url = [];

        $langs = Language::getLanguages();

        $queries[] = 'REPLACE `' . _DB_PREFIX_ . 'lgcanonicalurls` ' .
            'SET ' .
            '`type` = ' . (int) $tipo . ', ' .
            '`parameters` = "' . pSQL($params) . '", ' .
            '`id_object`= ' . pSQL($object_id) . ', ' .
            '`type_object` = ' . pSQL($type_object) . ';';
        // Si se especifica una url canonica
        if ((int) $tipo == self::LGCU_CUSTOM) {
            foreach ($langs as $lang) {
                if (isset($urls[$lang['id_lang']])) {
                    $canonical_url[$lang['id_lang']] = pSQL($urls[$lang['id_lang']]);
                }
            }

            if (!empty($canonical_url)) {
                foreach ($canonical_url as $lang => $url) {
                    $queries[] = 'REPLACE `' . _DB_PREFIX_ . 'lgcanonicalurls_lang` ' .
                        'SET ' .
                        ' `canonical_url` = "' . pSQL($url) . '", ' .
                        ' `id_object`= ' . pSQL($object_id) . ', ' .
                        ' `type_object` = ' . pSQL($type_object) . ', ' .
                        ' `id_lang` = ' . pSQL($lang) . ';';
                }
            }
        }

        return self::proccessQueries($queries);
    }

    protected static function getWhich($type_object)
    {
        $which = self::LG_UNKNOWN;

        switch ($type_object) {
            case self::LGOT_CATEGORY:
                if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
                    $which = 'category';
                }

                break;
            case self::LGOT_CMS:
                if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
                    $which = 'cms_page';
                }

                break;
            case self::LGOT_MANUFACTURER:
                if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
                    $which = 'manufacturer';
                }

                break;
            case self::LGOT_SUPPLIER:
                if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
                    $which = 'supplier';
                }

                break;
            case self::LGOT_HOME:
            case self::LGOT_PRODUCT:
            default:
                $which = self::LG_UNKNOWN;

                break;
        }

        return $which;
    }

    public static function getTypeFromRequest($type_object)
    {
        $type = false;
        $category = (int) Tools::getValue('id_category');
        $which = self::getWhich($type_object);

        if ($category == 2) {
            $which = 'root_category';
        } else {
            $which = self::getWhich($type_object);
        }

        if (version_compare(_PS_VERSION_, '1.7.6', '>=') &&
            $which != self::LG_UNKNOWN
        ) {
            $request = Tools::getValue($which);

            $type = !empty($request['lgcanonicalurls_type']) ? $request['lgcanonicalurls_type'] : false;
        } else {
            $type = Tools::getValue('lgcanonicalurls_type', false);
        }

        return $type;
    }

    public static function getUrlsFromRequest($type_object)
    {
        $category = (int) Tools::getValue('id_category');
        $which = self::getWhich($type_object);

        if ($category == 2) {
            $which = 'root_category';
        } else {
            $which = self::getWhich($type_object);
        }

        if (version_compare(_PS_VERSION_, '1.7.6', '>=')
            && $which != self::LG_UNKNOWN
        ) {
            $request = Tools::getValue($which);
            $urls = !empty($request['lgcanonicalurls_canonical']) ? 
                $request['lgcanonicalurls_canonical'] :
                [];
        } else {
            $languages = Language::getLanguages();
            $urls = [];
            foreach ($languages as $lang) {
                $urls[$lang['id_lang']] = pSQL(
                    Tools::getValue(
                        'lgcanonicalurls_canonical_url_' . $lang['id_lang']
                    )
                );
            }
        }

        return $urls;
    }

    /**
     * Actualiza el objeto en función del tipo y versión de Prestashop
     *
     * @param $params
     * @param $type_object
     * @return bool|void
     */
    public static function updateObject($params, $type_object)
    {
        if (LGCanonicalurlsObj::isValidObjecType($type_object) &&
            Validate::isLoadedObject($params['object']) &&
            $type = self::getTypeFromRequest((int) $type_object)
        ) {
            $urls = self::getUrlsFromRequest((int) $type_object);

            return self::update((int) $params['object']->id, (int) $type_object, $type, $urls);
        }
    }

    /**
     * Elimina la url canonica a nivel local cuando el producto se elimina
     */
    public static function delete($id_object, $type_object)
    {
        $queries = [
            'DELETE FROM `' . _DB_PREFIX_ . 'lgcanonicalurls` ' .
            'WHERE `id_object` = ' . pSQL($id_object) .
            ' AND `type_object` = ' . pSQL($type_object),

            'DELETE FROM `' . _DB_PREFIX_ . 'lgcanonicalurls_lang` ' .
            'WHERE `id_object` = ' . pSQL($id_object) .
            ' AND `type_object` = ' . pSQL($type_object),
        ];

        return self::proccessQueries($queries);
    }

    public static function deleteObject($params, $type_object)
    {
        if (Validate::isLoadedObject($params['object'])) {
            return self::delete((int) $params['object']->id, $type_object);
        }
    }

    /**
     * Guarda la url canonica a nivel local si se ha establecido
     */
    public static function add($object_id, $type_object, $tipo, $urls = [], $params = null)
    {
        $queries = [];
        $canonical_url = [];
        $langs = Language::getLanguages();

        $queries[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcanonicalurls`(`id_object`,`type_object`,`type`,`parameters`)' .
                ' VALUES('
                . pSQL($object_id) . ', '
                . pSQL($type_object) . ', '
                . pSQL($tipo) . ', "'
                . pSQL($params)
                . '");';

        // Si se especifica una url canonica
        if ((int) $tipo == self::LGCU_CUSTOM) {
            foreach ($langs as $lang) {
                foreach ($langs as $lang) {
                    if (isset($urls[$lang['id_lang']])) {
                        $canonical_url[$lang['id_lang']] = pSQL($urls[$lang['id_lang']]);
                    }
                }
            }

            foreach ($canonical_url as $lang => $url) {
                $queries[] = 'INSERT INTO `' ._DB_PREFIX_. 'lgcanonicalurls_lang`(`id_object`,`type_object`,`id_lang`, `canonical_url`)'.
                    ' VALUES('
                    . pSQL($object_id) . ', '
                    . pSQL($type_object) . ', '
                    . pSQL($lang) . ',"'
                    . pSQL($url, true)
                    . '")';
            }
        }

        return self::proccessQueries($queries);
    }

    public static function addObject($params, $type_object)
    {
        if (LGCanonicalurlsObj::isValidObjecType($type_object) &&
            Validate::isLoadedObject($params['object']) &&
            $type = self::getTypeFromRequest((int) $type_object)
        ) {
            $urls = self::getUrlsFromRequest((int) $type_object);

            return self::add($params['object']->id, (int) $type_object, $type, $urls);
        }
    }

    public static function isValidObjecType($object_type)
    {
        return $object_type >= 0;
    }

    protected static function proccessQueries($queries)
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->Execute($query)) {
                LGCanonicalurlsLogger::add(date('m-d-Y h:i:s a', time()) . ': ERROR: CONSULTA - ' . $query . "\n");
                return false;
            } else {
                LGCanonicalurlsLogger::add(date('m-d-Y h:i:s a', time()) . ': EXITO: CONSULTA - ' . $query . "\n");
            }
        }

        return true;
    }

    public static function getObject($id_object, $type_object)
    {
        $sql = 'SELECT * '
            . 'FROM `' . _DB_PREFIX_ . 'lgcanonicalurls` '
            . 'WHERE `id_object` = ' . (int) $id_object
            . '  AND `type_object` = ' . (int) $type_object;

        return Db::getInstance()->getRow($sql);
    }

    public static function loadObject($id_object, $type_object)
    {
        $obj = new stdClass();
        switch ($type_object) {
            case self::LGOT_CATEGORY:
                $obj = new Category((int) $id_object);

                break;
            case self::LGOT_CMS:
                $obj = new CMS((int) $id_object);

                break;
            case self::LGOT_MANUFACTURER:
                $obj = new Manufacturer((int) $id_object);

                break;
            case self::LGOT_PRODUCT:
                $obj = new Manufacturer((int) $id_object);

                break;
            case self::LGOT_SUPPLIER:
                $obj = new Supplier((int) $id_object);

                break;
        }

        return $obj;
    }
}
