<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }


function upgrade_module_4_5_2()
{
    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_reminder` ADD `discount_prefix` VARCHAR(64) DEFAULT NULL AFTER `allow_multi_discount`, ADD `highlight_discount` TINYINT(1) NOT NULL DEFAULT 0 AFTER `discount_prefix`; 
    ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_reminder` CHANGE `apply_discount_in` `apply_discount_in` DECIMAL(8,2) UNSIGNED NULL; ');

    return true;
}