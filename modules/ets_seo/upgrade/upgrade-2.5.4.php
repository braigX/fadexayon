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
function update_db_2_5_4()
{
    $tbls = [
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_not_found_url` (
         `id_ets_seo_not_found_url` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
         `id_shop` INT NOT NULL , 
         `url` VARCHAR(255) NOT NULL , 
         `referer` VARCHAR(255) NULL DEFAULT NULL , 
         `visit_count` INT NOT NULL , 
         `last_visited_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
          PRIMARY KEY (`id_ets_seo_not_found_url`),
          INDEX (`id_shop`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci',
        'ALTER TABLE `' . _DB_PREFIX_ . 'ets_seo_not_found_url` ADD INDEX(`url`)',
    ];
    foreach ($tbls as $tbl) {
        Db::getInstance()->execute($tbl);
    }
}

/**
 * @param \Ets_Seo $instance
 *
 * @return bool
 */
function upgrade_module_2_5_4($instance)
{
    update_db_2_5_4();
    $instance->_installTabs();
    $instance->uninstallOverrides();
    if (!$instance->isRegisteredInHook('actionFrontControllerRedirectBefore')) {
        $instance->registerHook('actionFrontControllerRedirectBefore');
    }
    $instance->installOverrides();

    return true;
}
