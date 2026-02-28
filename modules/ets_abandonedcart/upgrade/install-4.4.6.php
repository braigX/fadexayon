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


function upgrade_module_4_4_6()
{
    if (Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_campaign` ADD `date_upd` DATETIME NULL DEFAULT NULL AFTER `date_add`')) {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_campaign` SET `date_upd`=`date_add`');
    }
    if (Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_reminder` ADD `date_upd` DATETIME NULL DEFAULT NULL AFTER `date_add`')) {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_abancart_reminder` SET `date_upd`=`date_add`');
    }
    return true;
}