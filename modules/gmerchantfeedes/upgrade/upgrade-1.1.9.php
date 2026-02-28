<?php
/**
 * 2020 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2020 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_9($module)
{
    $module_key = 'gmerchantfeedes';
    $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. pSQL($module_key) . '_custom_rows` (
        `id_' . pSQL($module_key) . '` int(11) NOT NULL,
        `id_param` varchar(100) NOT NULL,
        `unit` varchar(255) NOT NULL,
        INDEX  (`id_' . pSQL($module_key) . '`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

    if (Db::getInstance()->execute($query) == false) {
        return false;
    }

    return $module;
}
