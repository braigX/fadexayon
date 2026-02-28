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
function upgrade_module_1_1_10($module)
{
    // Obtenemos los duplicados generados por error
    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'lgcanonicalurls` GROUP BY `id_object`, `type_object`';
    $result = Db::getInstance()->executeS($sql);

    // Si hay duplicados eliminamos antes de cambiar la tabla (si no no podemos alterar la tabla)
    if (!empty($result)) {
        foreach ($result as $row) {
            // Eliminamos todos
            $sql_del = 'DELETE FROM `' . _DB_PREFIX_ . 'lgcanonicalurls` '
                . 'WHERE `id_object` = ' . $row['id_object']
                . '  AND `type_object` = "' . $row['type_object'] . '"';
            Db::getInstance()->execute($sql_del);

            // insertamos una única fila
            $sql_ins = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcanonicalurls`(`id_object`, `type_object`, `type`, `parameters`)'
                . ' VALUES ('
                . $row['id_object'] . ', "'
                . $row['type_object'] . '", "'
                . $row['type'] . '", "'
                . $row['parameters']
                . '")';
            Db::getInstance()->execute($sql_ins);
        }
    }

    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'lgcanonicalurls` ADD PRIMARY KEY (`id_object`,`type_object`)',
        'ALTER TABLE `' . _DB_PREFIX_ . 'lgcanonicalurls_lang` ADD PRIMARY KEY(`id_object`,`type_object`, `id_lang`)',
    ];
    return $module->proccessQueries($queries);
}
