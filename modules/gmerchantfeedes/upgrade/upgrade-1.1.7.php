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

function upgrade_module_1_1_7($module)
{
    if (Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` LIKE \'suffix_feature_title_set\'') == false) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'gmerchantfeedes` ADD `suffix_feature_title_set` TINYINT NOT NULL DEFAULT \'0\' AFTER `title_suffix`');
    }

    return $module;
}
