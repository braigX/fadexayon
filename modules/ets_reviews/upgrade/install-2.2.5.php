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

/**
 * @return bool
 */
function upgrade_module_2_2_5()
{
    Db::getInstance()->execute(' ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` DROP PRIMARY KEY');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` ADD `id_ets_rv_unsubscribe` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT AFTER `id_customer`, ADD PRIMARY KEY (`id_ets_rv_unsubscribe`)');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` ADD `email` VARCHAR(255) NOT NULL AFTER `id_ets_rv_unsubscribe`');
    Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` u 
        INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON c.`id_customer` = u.`id_customer` 
        SET u.`email` = c.`email`
    ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_unsubscribe` DROP `id_customer`');
    return true;
}