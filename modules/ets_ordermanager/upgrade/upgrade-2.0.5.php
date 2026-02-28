<?php
/**
 * 2007-2023 ETS-Soft
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_2_0_5($object)
{
    $sqls = array();
    $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` 
    CHANGE `send_file_time` `send_file_time_hours` VARCHAR(20) NULL DEFAULT NULL,
    CHANGE `server1_time` `server1_time_hours` VARCHAR(20) NULL DEFAULT NULL,
    CHANGE `server2_time` `server2_time_hours` VARCHAR(20) NULL DEFAULT NULL';
    $sqls[]= 'ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` 
    ADD `send_file_time_weeks` VARCHAR(222) NOT NULL AFTER `send_file_time_hours`,
    ADD `send_file_time_months` VARCHAR(222) NOT NULL AFTER `send_file_time_weeks`';
    $sqls[]= 'ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` 
    ADD `server1_time_weeks` VARCHAR(222) NOT NULL AFTER `server1_time_hours`,
    ADD `server1_time_months` VARCHAR(222) NOT NULL AFTER `server1_time_weeks`';
    $sqls[]= 'ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` 
    ADD `server2_time_weeks` VARCHAR(222) NOT NULL AFTER `server2_time_hours`,
    ADD `server2_time_months` VARCHAR(222) NOT NULL AFTER `server2_time_weeks`';
    foreach($sqls as $sql)
        Db::getInstance()->execute($sql);
    unset($object);
    return true;
}