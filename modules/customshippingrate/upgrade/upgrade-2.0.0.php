<?php
/**
* 2018 Prestamatic
*
* NOTICE OF LICENSE
*
*  @author    Prestamatic
*  @copyright 2018 Prestamatic
*  @license   Licensed under the terms of the MIT license
*  @link      https://prestamatic.co.uk
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_0($module)
{
    $_tabs = array(
        array(
            'class_name' => 'AdminCustomShippingRate',
            'parent' => 'AdminParentCustomer',
            'name' => $module->l('Shipping Quote Requests')
        ),
    );

    $languages = Language::getLanguages();
    foreach ($_tabs as $tab) {
        $_tab = new Tab();
        $_tab->class_name = $tab['class_name'];
        $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
        $_tab->module = $module->name;
        foreach ($languages as $language) {
            $_tab->name[$language['id_lang']] = $module->l($tab['name']);
        }
        $_tab->add();
    }
    $return = $module->installMails();
    $return = $return && Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.'customshippingrate`
        ADD `id_address_delivery` INT(10) UNSIGNED NOT NULL AFTER `id_customer`'
    );
    $return = $return && Configuration::updateValue('CUSTOMSHIP_ID_CONTACT', '0');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_EMAIL_TO', Configuration::get('PS_SHOP_EMAIL'));
    $return = $return && Configuration::updateValue('CUSTOMSHIP_QUOTE_EXPIRES', '15');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_CARRIERS', '');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_MIN_WEIGHT', '0');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_MAX_WEIGHT', '0');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_MIN_PRICE', '0');
    $return = $return && Configuration::updateValue('CUSTOMSHIP_MAX_PRICE', '0');
    $zone_list = array();
    $zones = Zone::getZones(true);
    if ($zones) {
        foreach ($zones as $zone) {
            $zone_list[] = $zone['id_zone'];
        }
    }
    $return = $return && Configuration::updateValue('CUSTOMSHIP_CARRIER_ZONE_LIST', json_encode($zone_list));
    $return = $return && Configuration::updateValue('CUSTOMSHIP_AUTO_CLEAN', '0');
    return $return;
}