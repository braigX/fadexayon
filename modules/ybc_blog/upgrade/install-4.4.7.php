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

if (!defined('_PS_VERSION_'))
    exit;
/**
 * @param Ybc_blog $module
 * @return bool
 */
function upgrade_module_4_4_7($module)
{
    $sqls = array();
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_chatgpt_template` (
    `id_ybc_chatgpt_template` INT(11) NOT NULL AUTO_INCREMENT , 
    `position` INT(11) NOT NULL ,
    PRIMARY KEY (`id_ybc_chatgpt_template`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_chatgpt_template_lang` (
    `id_ybc_chatgpt_template` INT(11) NOT NULL , 
    `id_lang` INT(11) NOT NULL , 
    `label` text ,
    `content` text ,
    PRIMARY KEY (`id_ybc_chatgpt_template`,`id_lang`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_blog_chatgpt_message` (
             `id_ybc_blog_chatgpt_message` INT(11) NOT NULL AUTO_INCREMENT , 
             `is_chatgpt` INT(1) NOT NULL ,
             `message` text,
             `field` VARCHAR(32),
             `date_add` datetime,
          PRIMARY KEY (`id_ybc_blog_chatgpt_message`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    foreach($sqls as $sql)
    {
        Db::getInstance()->execute($sql);
    }
    return true;
}