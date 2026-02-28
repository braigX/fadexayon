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
abstract class LGCookiesLawPubli
{
    protected static $module;

    public static $modules = [];

    public static function setModules($modules = null)
    {
        self::$modules = $modules;
    }

    public static function setModule($module = null)
    {
        self::$module = $module;
    }

    private static function assignVars()
    {
        $params = [
            'lg_module_dir' => self::$module->getPathUri(),
            'lg_module_name' => self::$module->name,
            'lg_base_url' => _MODULE_DIR_ . self::$module->name . '/',
            'lg_iso_code' => Context::getContext()->language->iso_code,
            'lg_modules' => self::getPubliModules(),
        ];

        Context::getContext()->smarty->assign($params);
    }

    public static function renderHeader()
    {
        self::assignVars();

        return Context::getContext()->smarty->fetch(
            self::$module->getLocalPath() . 'views/templates/admin/view_header_' . self::$module->platform . '.tpl'
        );
    }

    public static function renderFooter()
    {
        self::assignVars();

        return Context::getContext()->smarty->fetch(
            self::$module->getLocalPath() . 'views/templates/admin/view_footer_' . self::$module->platform . '.tpl'
        );
    }

    public static function getPubliModules($count = 3)
    {
        $modules = [];
        $rand = array_rand(self::$modules, $count);

        if (!empty($rand)) {
            $iso_code = Context::getContext()->language->iso_code;

            foreach ($rand as $module) {
                $modules[$module] = self::$modules[$module];
                $modules[$module]['name'] = (empty(self::$modules[$module]['name'][$iso_code]) ?
                    self::$modules[$module]['name']['en'] :
                    self::$modules[$module]['name'][$iso_code]
                );
                $modules[$module]['description'] = (empty(self::$modules[$module]['description'][$iso_code]) ?
                    self::$modules[$module]['description']['en'] :
                    self::$modules[$module]['description'][$iso_code]
                );
                $modules[$module]['url'] = (empty(self::$modules[$module]['url'][$iso_code]) ?
                    self::$modules[$module]['url']['en'] :
                    self::$modules[$module]['url'][$iso_code]
                );
            }
        }

        return $modules;
    }
}
