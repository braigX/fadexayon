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
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * @param \Ets_Seo $instance
 *
 * @return bool
 */
function upgrade_module_2_6_1($instance)
{
    $tbl = 'CREATE TABLE IF NOT EXISTS`' . _DB_PREFIX_ . 'ets_seo_gpt_message` (
 `id_ets_seo_gpt_message` bigint(20) NOT NULL AUTO_INCREMENT,
 `message` text NOT NULL,
 `is_chatgpt` tinyint(4) NOT NULL DEFAULT "0",
 `id_parent` bigint(20) NULL DEFAULT NULL,
 `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id_ets_seo_gpt_message`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($tbl);

    return true;
}
