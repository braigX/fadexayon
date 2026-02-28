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
function upgrade_module_4_4_1($module)
{
    $sqls = array();
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','exclude_products'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post` ADD `exclude_products` VARCHAR(500) NOT NULL';
    }
    $sqls[] ="CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ybc_blog_post_related_product_categories` (
        `id_post` INT(11) NOT NULL , 
        `id_category` INT(11) NOT NULL,
         PRIMARY KEY (`id_post`,`id_category`)
         ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
    foreach($sqls as $sql)
    {
        Db::getInstance()->execute($sql);
    }
    return true;
}