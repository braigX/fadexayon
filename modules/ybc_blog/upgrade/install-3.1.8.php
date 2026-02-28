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
function upgrade_module_3_1_8()
{
    $sqls = array();
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','id_category_default'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post` ADD COLUMN `id_category_default` INT(11) DEFAULT NULL AFTER `added_by`';
    if($sqls)
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    return true;
}