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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/MetaPageHelper.php';
class InstallHelper extends BasicHelper
{
    public $metaPageHelper;

    public function __construct($module)
    {
        parent::__construct($module);
        $this->metaPageHelper = new MetaPageHelper($module);
    }

    public function installTabs()
    {
        $res = true;
        $res &= $this->installOneTab('FAQ Lists', 'AdminFaqop', 1);
        $res &= $this->installOneTab('FAQ Lists', 'AdminFaqopListsList', 0);
        // list general
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopBasicBlock', 0);
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopBlockGeneral', 0);
        $res &= $this->installOneTab('FAQ List Block', 'AdminFaqopList', 0);
        $res &= $this->installOneTab('FAQ List for Page', 'AdminFaqopPage', 0);
        // hook
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopHookBasic', 0);
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopHookList', 0);
        // other main tabs
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopCustomHook', 0);
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopHelp', 0);
        // styles
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopBasicStyles', 0);
        $res &= $this->installOneTab('FAQ List For Page', 'AdminFaqopStylesPage', 0);
        $res &= $this->installOneTab('FAQ List Block', 'AdminFaqopStylesList', 0);
        // items
        $res &= $this->installOneTab('FAQ Items', 'AdminFaqopItemsList', 0);
        $res &= $this->installOneTab('FAQ Item', 'AdminFaqopItem', 0);
        $res &= $this->installOneTab('FAQ Module', 'AdminFaqopBasicItem', 0);
        $res &= $this->installOneTab('FAQ Item Bindings', 'AdminFaqopBindItem', 0);
        $res &= $this->installOneTab('FAQ Item Styles', 'AdminFaqopStylesItem', 0);
        // adding items
        $res &= $this->installOneTab('Add FAQ Items to List', 'AdminFaqopAddItems', 0);
        $res &= $this->installOneTab('Add FAQ Items to List', 'AdminFaqopAddItemsList', 0);
        $res &= $this->installOneTab('Add FAQ Items to List', 'AdminFaqopAddItemsPage', 0);
        $res &= $this->installOneTab('Add FAQ Items to List', 'AdminFaqopAddReadyItem', 0);

        return $res;
    }

    public function uninstallTabs()
    {
        $res = true;
        $res &= $this->uninstallOneTab('AdminFaqop');
        $res &= $this->uninstallOneTab('AdminFaqopListsList');
        // list general
        $res &= $this->uninstallOneTab('AdminFaqopBasicBlock');
        $res &= $this->uninstallOneTab('AdminFaqopBlockGeneral');
        $res &= $this->uninstallOneTab('AdminFaqopList');
        $res &= $this->uninstallOneTab('AdminFaqopPage');
        // hook
        $res &= $this->uninstallOneTab('AdminFaqopHookBasoc');
        $res &= $this->uninstallOneTab('AdminFaqopHookList');
        // other main tabs
        $res &= $this->uninstallOneTab('AdminFaqopCustomHook');
        $res &= $this->uninstallOneTab('AdminFaqopHelp');
        // styles
        $res &= $this->uninstallOneTab('AdminFaqopBasicStyles');
        $res &= $this->uninstallOneTab('AdminFaqopStylesPage');
        $res &= $this->uninstallOneTab('AdminFaqopStylesList');
        // items
        $res &= $this->uninstallOneTab('AdminFaqopItemsList');
        $res &= $this->uninstallOneTab('AdminFaqopItem');
        $res &= $this->uninstallOneTab('AdminFaqopBasicItem');
        $res &= $this->uninstallOneTab('AdminFaqopBindItem');
        $res &= $this->uninstallOneTab('AdminFaqopStylesItem');
        // adding items
        $res &= $this->uninstallOneTab('AdminFaqopAddItems');
        $res &= $this->uninstallOneTab('AdminFaqopAddItemsList');
        $res &= $this->uninstallOneTab('AdminFaqopAddItemsPage');
        $res &= $this->uninstallOneTab('AdminFaqopAddReadyItem');

        return $res;
    }

    public function installOneTab($name, $className, $isActive = 0)
    {
        $tab = new Tab();
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->class_name = $className;
        $tab->module = $this->module->name;
        $tab->active = $isActive;
        if ($isActive && version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $tab->id_parent = (int) Tab::getIdFromClassName('Improve');
        }

        return $tab->add();
    }

    public function uninstallOneTab($className)
    {
        $res = true;

        while (true) {
            $idtab = Tab::getIdFromClassName($className);

            if (!$idtab) {
                break;
            }

            try {
                $tab = new Tab((int) $idtab);
                $res &= $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $res;
    }

    /* Create meta page */
    public function createMetaPage()
    {
        return $this->metaPageHelper->createMetaPage();
    }

    public function deleteMetaPage()
    {
        return $this->metaPageHelper->deleteMetaPage();
    }

    public function getMetaPageId()
    {
        return $this->metaPageHelper->getMetaPageId();
    }

    public function installSamples()
    {
        require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/InstallSamplesHelper.php';
        $ih = new InstallSamplesHelper($this->module);
        $res = true;

        $res &= $ih->installPage();
        $res &= $ih->installItems();

        return $res;
    }
}
