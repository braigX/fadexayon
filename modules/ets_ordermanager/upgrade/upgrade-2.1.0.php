<?php
/**
 * 2007-2023 ETS-Soft
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_2_1_0($object)
{
    $object->registerHook('actionAdminOrdersListingResultsModifier');
    $object->registerHook('actionOrderGridQueryBuilderModifier');
    $object->registerHook('actionOrderGridDefinitionModifier');
    $object->registerHook('actionOrderGridDataModifier');
    $object->registerHook('displayBlockInputChangeInLine');
    $object->registerHook('actionDispatcherBefore');
    $sqls = array();
    if(!Ode_dbbase::checkCreatedColumn('ets_export_order_rule_lang','title_mail'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule_lang` ADD `title_mail` text';
    }
    if(!Ode_dbbase::checkCreatedColumn('ets_export_order_rule_lang','description_mail'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule_lang` ADD `description_mail` text';
    }
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    if(Ode_dbbase::checkCreatedColumn('ets_export_order_rule','title_mail'))
    {
        $order_rules  = Db::getInstance()->executeS('SELECT title_mail,id_ets_export_order_rule FROM '._DB_PREFIX_.'ets_export_order_rule');
        if($order_rules)
        {
            foreach($order_rules as $order_rule)
            {
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_export_order_rule_lang` SET title_mail="'.pSQL($order_rule['title_mail']).'" WHERE id_ets_export_order_rule = "'.(int)$order_rule['id_ets_export_order_rule'].'"');
            }
        }
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` DROP `title_mail`');
    }
    if(Ode_dbbase::checkCreatedColumn('ets_export_order_rule','description_mail'))
    {
        $order_rules  = Db::getInstance()->executeS('SELECT description_mail,id_ets_export_order_rule FROM '._DB_PREFIX_.'ets_export_order_rule');
        if($order_rules)
        {
            foreach($order_rules as $order_rule)
            {
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_export_order_rule_lang` SET description_mail="'.pSQL($order_rule['description_mail']).'" WHERE id_ets_export_order_rule = "'.(int)$order_rule['id_ets_export_order_rule'].'"');
            }
        }
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_export_order_rule` DROP `description_mail`');
    }
    $object->_installOverried();
    return true;
}