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

class Hook extends HookCore
{
    /**
     * Returns a list of modules that are registered for a given hook, each following this schema:
     *
     * ```
     *     [
     *         'id_hook' => $hookId,
     *         'module' => $moduleName,
     *         'id_module' => $moduleId
     *     ]
     * ```
     *
     * If no hook name is given, it returns all the hook registrations, indexed by lower cased hook name.
     *
     * @param string|null $hookName Hook name (null to return all hooks)
     *
     * @return array[]|false returns an array of hook registrations, or false if the provided hook name is not registered
     *
     * @throws PrestaShopDatabaseException
     *
     * @since 1.5.0
     */
    public static function getHookModuleExecList($hook_name = null)
    {
        $modules_to_invoke = parent::getHookModuleExecList($hook_name);

        if (!empty($modules_to_invoke)
            && Module::isInstalled('lgcookieslaw')
            && Module::isEnabled('lgcookieslaw')
        ) {
            $lgcookieslaw = Module::getInstanceByName('lgcookieslaw');

            $modules_to_invoke = $lgcookieslaw->getHookModuleExecList($modules_to_invoke);
        }

        return $modules_to_invoke;
    }
}
