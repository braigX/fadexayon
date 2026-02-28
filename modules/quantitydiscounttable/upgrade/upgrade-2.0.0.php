<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_0($module)
{
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_HEADER_FONT')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_FONT', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BODY_COLOR')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BODY_COLOR', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BORDER')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_TEXT_FONT')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_FONT', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR', '');
    }
    if (!Configuration::hasKey('QUANTITY_DISCOUNT_TABLE_BG_COLOR')) {
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BG_COLOR', '');
    }

    // Check if the parent tab already exists
    $parentTabClassName = 'AdminParentQuantityDiscount';
    $parentTabName = 'Quantity Discount';
    $parentTabId = Tab::getIdFromClassName($parentTabClassName);

    if (!$parentTabId) {
        // Create the parent tab 'Quantity Discount'
        $parentTab = new Tab();
        $parentTab->module = $module->name;
        $parentTab->active = true;
        $parentTab->class_name = $parentTabClassName;
        $parentTab->id_parent = 0; // 0 indicates that it is a root-level tab

        foreach (Language::getLanguages(false) as $language) {
            $parentTab->name[$language['id_lang']] = $parentTabName;
        }

        if (!$parentTab->add()) {
            return false;
        }

        // Retrieve the ID of the newly created parent tab
        $parentTabId = $parentTab->id;
    }

    // Check if the child tab already exists
    $tabClassName = 'AdminQuantityDiscount';
    $tabName = 'Specific Pricing';
    $childTabId = Tab::getIdFromClassName($tabClassName);

    if (!$childTabId) {
        // Create the child tab 'Specific Pricing' under 'Quantity Discount'
        $childTab = new Tab();
        $childTab->module = $module->name;
        $childTab->active = true;
        $childTab->class_name = $tabClassName;
        $childTab->id_parent = $parentTabId; // Set the parent tab ID

        foreach (Language::getLanguages(false) as $language) {
            $childTab->name[$language['id_lang']] = $tabName;
        }

        if (!$childTab->add()) {
            return false;
        }
    }

    return true;
}
