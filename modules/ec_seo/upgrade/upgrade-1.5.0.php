<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 2.2,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_1_5_0($module)
{
    /**
     * Do everything you want right there,
     * You could add a column in one of your module's tables
     */
    //Footer SEO
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer` (
             `id` INT AUTO_INCREMENT,
             `type` VARCHAR(20),
             `spe` TINYINT(1),
             `active` TINYINT(1),
             `id_shop` INT,
             PRIMARY KEY (`id`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_lang` (
             `id_footer` INT AUTO_INCREMENT,
             `id_lang` INT,
             `title` VARCHAR(50),
             `description` TEXT,
             PRIMARY KEY (`id_footer`, `id_lang`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_block` (
             `id` INT AUTO_INCREMENT,
             `id_footer` INT,
             `active` TINYINT(1),
             `position` INT,
             PRIMARY KEY (`id`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_block_lang` (
             `id_block` INT,
             `id_lang` INT,
             `title` VARCHAR(50),
             PRIMARY KEY (`id_block`, `id_lang`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_link` (
             `id` INT AUTO_INCREMENT,
             `id_block` INT,
             `position` INT,
             PRIMARY KEY (`id`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_link_lang` (
             `id_link` INT,
             `id_lang` INT,
             `title` VARCHAR(50),
             `link` VARCHAR(200),
             PRIMARY KEY (`id_link`, `id_lang`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_category` (
             `id_footer` INT,
             `id_category` INT,
             PRIMARY KEY (`id_footer`, `id_category`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_product` (
             `id_footer` INT,
             `id_product` INT,
             PRIMARY KEY (`id_footer`, `id_product`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_cms` (
             `id_footer` INT,
             `id_cms` INT,
             PRIMARY KEY (`id_footer`, `id_cms`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_supplier` (
             `id_footer` INT,
             `id_supplier` INT,
             PRIMARY KEY (`id_footer`, `id_supplier`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_manufacturer` (
             `id_footer` INT,
             `id_manufacturer` INT,
             PRIMARY KEY (`id_footer`, `id_manufacturer`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    //FIN Footer SEO

    //Block HTML
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_block_html` (
             `id_block_html` INT AUTO_INCREMENT,
             `id_hook` INT,
             `active` TINYINT(1),
             `id_shop` INT,
             PRIMARY KEY (`id_block_html`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_block_html_lang` (
             `id_block_html` INT,
             `content` TEXT,
             `id_lang` INT,
             PRIMARY KEY (`id_block_html`, `id_lang`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    //FIN Block HTML

    //SmartKeyword API
    Db::getInstance()->execute(
        '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_smartkeyword` (
             `id` INT,
             `id_lang` INT,
             `id_shop` INT,
             `keyword` VARCHAR(100),
             `page` VARCHAR(50),
             `info` TEXT,
             `date_upd` DATETIME,
             PRIMARY KEY (`id`,`id_lang`,`id_shop`,`page`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
    //FIN SmartKeyword API
    
    $list = array('product' => true, 'category' => true, 'cms' => false, 'supplier' => false, 'manufacturer' => false);
    $languages = Language::getLanguages(false);
    $shops = Shop::getShops(true);
    foreach ($shops as $shop) {
        $id_shop = $shop['id_shop'];
        foreach ($list as $class => $spe) {
            Db::getinstance()->insert(
                'ec_seo_footer',
                array(
                    'type' => pSQL($class),
                    'spe' => 0,
                    'active' => 0,
                    'id_shop' => (int)$id_shop,
                )
            );
            $id_footer = Db::getInstance()->Insert_ID();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                Db::getinstance()->insert(
                    'ec_seo_footer_lang',
                    array(
                        'id_footer' => (int)$id_footer,
                        'id_lang' => (int)$id_lang,
                        'title' => '',
                        'description' => '',
                    )
                );
            }
        }
    }
    $module->registerHook('actionAdminCategoriesListingFieldsModifier');
    $module->registerHook('actionAdminCategoriesListingResultsModifier');
    $module->registerHook('actionCategoryGridQueryBuilderModifier');
    $module->registerHook('actionCategoryGridDefinitionModifier');
    $module->registerHook('actionCategoryGridDataModifier');
    $module->registerHook('displayEcSeoCustomBlock');
    $module->registerHook('displayFooter');
    return true;
}
