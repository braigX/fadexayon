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
function upgrade_module_2_3_8()
{
    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_rv_product_comment_order_license`(
           `id_ets_rv_product_comment` INT(11) UNSIGNED NOT NULL,
           `id_license` INT(11) UNSIGNED NOT NULL,
           `id_product` INT(11) UNSIGNED NOT NULL,
           PRIMARY KEY (`id_ets_rv_product_comment`, `id_license`, `id_product`)
        ) ENGINE='._MYSQL_ENGINE_.' CHARSET=utf8mb4;
    ');

    return true;
}