<?php
/**
 * 2022 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2022 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_8($module)
{
    $module_key = 'gmerchantfeedes';

    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . pSQL($module_key) . '` LIKE \'exclude_empty_description\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . pSQL($module_key) . '` ADD `exclude_empty_description` TINYINT NOT NULL DEFAULT \'0\'');
    }

    return $module;
}
