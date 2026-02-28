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

function upgrade_module_2_0_3()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment`  ADD `email` VARCHAR(255) DEFAULT NULL AFTER `customer_name`');

    if (Shop::isFeatureActive()) {
        Db::getInstance()->execute('
            INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_email_template_shop`(`id_ets_rv_email_template`, `id_shop`)
            SELECT `id_ets_rv_email_template`, `id_shop`
            FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_lang`
            WHERE `id_lang` = ' . (int)Configuration::get('PS_LANG_DEFAULT') . '
        ');
    }
    return true;
}