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
function upgrade_module_2_0_1($object)
{
    $sqls=array();
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_lang` ADD COLUMN IF NOT EXISTS `message_ip_black_list` INT(1) DEFAULT NULL AFTER `message_captcha_not_match`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_message` ADD COLUMN IF NOT EXISTS `id_customer` INT(11) DEFAULT NULL AFTER `id_contact`';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_log`(
            `ip` varchar(50) DEFAULT NULL,
            `id_contact` INT(11) NOT NULL,
            `browser` varchar(70) DEFAULT NULL,
            `id_customer` INT (11) DEFAULT NULL,
            `datetime_added` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
    foreach($sqls as $sql)
    {
        Db::getInstance()->execute($sql);
    }
    return _installTabs($object);
}
function _installTabs($object)
{
    $languages = Language::getLanguages(false);
    $tabId = Tab::getIdFromClassName('AdminContactForm');
    if($tabId)
    {
        $subTabs = array(
            array(
                'class_name' => 'AdminContactFormContactForm',
                'tab_name' => $object->l('Contact forms'),
                'icon'=>'icon icon-envelope-o'
            ),
            array(
                'class_name' => 'AdminContactFormMessage',
                'tab_name' => $object->l('Messages'),
                'icon'=>'icon icon-comments',
            ),
            array(
                'class_name' => 'AdminContactFormEmail',
                'tab_name' => $object->l('Email templates'),
                'icon'=>'icon icon-file-text-o',
            ),
            array(
                'class_name' => 'AdminContactFormImportExport',
                'tab_name' => $object->l('Import/Export'),
                'icon'=>'icon icon-exchange',
            ),
            array(
                'class_name' => 'AdminContactFormIntegration',
                'tab_name' => $object->l('Integration'),
                'icon'=>'icon icon-cogs',
            ),
            array(
                'class_name' => 'AdminContactFormStatistics',
                'tab_name' => $object->l('Statistics'),
                'icon'=>'icon icon-line-chart',
            ),
            array(
                'class_name' => 'AdminContactFormHelp',
                'tab_name' => $object->l('Help'),
                'icon'=>'icon icon-question-circle',
            ),
        );
        foreach($subTabs as $tabArg)
        {
            if(!Tab::getIdFromClassName($tabArg['class_name']))
            {
                $tab = new Tab();
                $tab->class_name = $tabArg['class_name'];
                $tab->module = $object->name;
                $tab->id_parent = $tabId; 
                $tab->icon=$tabArg['icon'];           
                foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $tabArg['tab_name'];
                }
                $tab->save();
            }
        }                 
    }            
    return true;
}