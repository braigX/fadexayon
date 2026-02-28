<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/UpgradeHelper.php';

function upgrade_module_3_0_17(FaqOp $module)
{
    $res = $module->registerHook(ConfigsFaq::PAGE_HOOK);

    $ih = $module->helper->getInstallHelper($module->install_helper);
    $res &= $ih->installTabs();
    $res &= $ih->createMetaPage();

    // Db goes here
    $faq_items_insert = UpgradeHelper::makeInsertItems();
    $faq_items_lang_insert = UpgradeHelper::makeInsertItemsLang();
    $faq_pages_insert = UpgradeHelper::getDataForPages();
    $faq_pages_lang_insert = UpgradeHelper::getDataForPagesLang();
    $faq_pages_items_bind_insert = UpgradeHelper::getDataForPageItemsBind($faq_pages_insert);
    $faq_lists_insert = UpgradeHelper::makeInsertLists();
    $faq_lists_lang_insert = UpgradeHelper::makeInsertListsLang();
    $faq_lists_items_bind_insert = UpgradeHelper::getDataForListItemsBind();

    $res &= UpgradeHelper::updateConfiguration();

    $res &= UpgradeHelper::deleteTables();

    $res &= $module->rep->createTables();

    $res &= UpgradeHelper::insertForUpgrade('op_faq_items', $faq_items_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_items_lang', $faq_items_lang_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_pages', $faq_pages_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_pages_lang', $faq_pages_lang_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_block_item', $faq_pages_items_bind_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_lists', $faq_lists_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_lists_lang', $faq_lists_lang_insert);
    $res &= UpgradeHelper::insertForUpgrade('op_faq_block_item', $faq_lists_items_bind_insert);

    // and recache everything!
    $res &= $module->cache_helper->recacheAllLists();

    if ($res) {
        UpgradeHelper::deleteFiles();
    }

    return $res;
}
