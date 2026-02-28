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
function upgrade_module_2_6_2($instance)
{
    $tbl_gpt_tpl = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template` (
          `id_ets_seo_gpt_template` INT NOT NULL AUTO_INCREMENT, 
          `position` INT NOT NULL, 
          `display_page` VARCHAR(50) NOT NULL,
          PRIMARY KEY (`id_ets_seo_gpt_template`),
          INDEX(`display_page`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    $tbl_gpt_tpl_lang = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template_lang` (
          `id_ets_seo_gpt_template` INT UNSIGNED NOT NULL, 
          `id_lang` INT UNSIGNED NOT NULL, 
          `label` TEXT NULL DEFAULT NULL, 
          `content` TEXT NULL DEFAULT NULL, 
          PRIMARY KEY (
            `id_ets_seo_gpt_template`, `id_lang`
          )
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    $instance->_installTabs();

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($tbl_gpt_tpl)
        && Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($tbl_gpt_tpl_lang);
}
