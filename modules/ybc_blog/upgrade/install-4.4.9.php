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
function upgrade_module_4_4_9($module)
{
    $module->_installTabs();
    $module->registerHook('actionUpdateBlog');
    $module->registerHook('actionUpdateBlogImage');
    $module->registerHook('actionMetaPageSave');
    $sqls = array();
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_category` ADD INDEX(`id_parent`),ADD INDEX(`added_by`),ADD INDEX(`enabled`),ADD INDEX(`sort_order`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_comment` ADD INDEX(`id_user`),ADD INDEX(`id_post`),ADD INDEX(`approved`),ADD INDEX(`viewed`),ADD INDEX(`reported`),ADD INDEX(`replied_by`),ADD INDEX(`customer_reply`),ADD INDEX(`rating`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_gallery` ADD INDEX(`enabled`),ADD INDEX(`sort_order`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post` ADD INDEX(`id_category_default`),ADD INDEX(`is_featured`),ADD INDEX(`added_by`),ADD INDEX(`is_customer`),ADD INDEX(`sort_order`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_gallery` ADD INDEX(`enabled`),ADD INDEX(`sort_order`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_tag` ADD INDEX(`id_post`),ADD INDEX(`id_lang`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_employee` ADD INDEX(`id_employee`),ADD INDEX(`is_customer`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_log_view` ADD INDEX(`id_post`),ADD INDEX(`id_customer`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_log_like` ADD INDEX(`id_post`),ADD INDEX(`id_customer`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_reply` ADD INDEX(`id_comment`),ADD INDEX(`id_user`),ADD INDEX(`id_employee`),ADD INDEX(`approved`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_polls` ADD INDEX(`id_user`),ADD INDEX(`id_post`),ADD INDEX(`polls`)';
    $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ybc_blog_chatgpt_message` ADD INDEX(`is_chatgpt`)';
    if($sqls)
    {
        foreach($sqls as $sql) {
            try {
                Db::getInstance()->execute($sql);
            } catch (Exception $e)
            {
                if($e)
                {
                    continue;
                }
            }
        }
    }
    return true;
}