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


function upgrade_module_4_5_1()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_abancart_discount` ADD `fixed_voucher` TINYINT(1) NOT NULL DEFAULT 0 AFTER `use_same_cart`; ');
    Db::getInstance('
        UPDATE `' . _DB_PREFIX_ . 'ets_abancart_discount` ad
            INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_tracking` at ON (at.id_ets_abancart_tracking = ad.id_ets_abancart_tracking) 
            INNER JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON (ar.id_ets_abancart_reminder = ad.id_ets_abancart_reminder) 
            INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (ad.id_cart_rule = cr.id_cart_rule) 
        SET ad.`fixed_voucher`=1 
        WHERE ar.`discount_option`=\'fixed\' AND cr.discount_code is NOT NULL
    ');
    return true;
}