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
 * @var Ets_reviews $object
 */
function upgrade_module_2_3_0()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` CHANGE `avatar` `avatar` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL');
    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE `id_customer`=0');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment` CHANGE `verified_purchase` `verified_purchase` VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'auto\'');

    return true;
}