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

function upgrade_module_1_0_2()
{
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_rv_product_comment_video` ( 
    `id_ets_rv_product_comment_video` INT(11) NOT NULL AUTO_INCREMENT , 
    `id_ets_rv_product_comment` INT(11),
    `video` VARCHAR(500) NOT NULL , 
    `type` VARCHAR(32) NOT NULL , 
    `position` INT(11) NOT NULL ,
     PRIMARY KEY (`id_ets_rv_product_comment_video`), INDEX (`position`),INDEX(`id_ets_rv_product_comment`)) ENGINE= '._MYSQL_ENGINE_.' COLLATE=utf8_general_ci DEFAULT CHARSET=utf8');
    return true;
}