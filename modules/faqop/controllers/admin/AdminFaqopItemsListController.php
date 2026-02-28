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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/GridItemsHelperFaq.php';
class AdminFaqopItemsListController extends ModuleAdminController
{
    protected $grid_helper;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('All FAQ Items (Elements for FAQ lists)');
        $this->grid_helper = new GridItemsHelperFaq($this->module);
    }

    public function initContent()
    {
        $this->content .= $this->showDeleteErrors();
        $this->content .= $this->displayItemList();
        $this->content .= $this->module->mes->getInfoMultishopAboutItem();
        parent::initContent();
    }

    protected function showDeleteErrors()
    {
        if (Tools::isSubmit('deleteWrong')) {
            return $this->module->displayError($this->l('Could not delete.'));
        }
        if (Tools::isSubmit('cloneWrong')) {
            return $this->module->displayError($this->l('Could not clone'));
        }
        if (Tools::isSubmit('itemWrong')) {
            return $this->module->displayError($this->l('Item does not exist'));
        }
        if (Tools::isSubmit('removeWrong')) {
            return $this->module->displayError($this->l('Could not remove.'));
        }

        return '';
    }

    public function displayItemList()
    {
        $output = '';
        // Save to cookie current address to use it in "back" href of edit forms, if it wasn't update or delete
        $this->module->helper->saveToCookieCurrentAddress(ConfigsFaq::ITEMS_COOKIE);
        $this->module->helper->saveToCookieItemsParent('items');

        $output .= $this->module->displayNav('items');
        $output .= $this->renderItems();

        return $output;
    }

    public function renderItems()
    {
        $normalUrl = $this->module->helper->getCleanItemsListUrl();

        try {
            $newItemUrl = $this->context->link->getAdminLink('AdminFaqopItem') .
                '&create=1' .
                $this->module->helper->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
        $items = $this->grid_helper->getItemsForListFilter();

        foreach ($items as $key => $item) {
            try {
                $itemController = $this->context->link->getAdminLink('AdminFaqopItem') .
                    '&id_item=' . (int) $item['id_item'] .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }

            $items[$key]['delete_href'] = $itemController . '&delete=1';
            $items[$key]['clone_href'] = $itemController . '&clone=1';
            $items[$key]['edit_href'] = $itemController . '&edit=1';
        }

        $search = Tools::getValue('search');
        $search = pSQL($this->grid_helper->processSearchClause($search));

        $this->context->smarty->assign(
            [
                'table_title' => $this->l('All FAQ Items'),
                'items' => $items,
                'classnames' => $this->grid_helper->getClassNamesForTh(),
                'clean_url' => $normalUrl,
                'searchtext' => $search,
                'new_item_link' => $newItemUrl,
                'multistore_active' => $this->module->helper->getIsMultistoreActive(),
                'shops_list' => $this->module->helper->getShopsWithoutCurrent(),
                'old_shop' => Context::getContext()->shop->id,
            ]
        );

        return $this->module->displayItems();
    }

    public function ajaxProcessDeleteBulkItems()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('This action is prohibited in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'));
        $result = true;
        foreach ($ids as $id) {
            $result &= $this->module->rep->deleteSingleItem($this->module, $id);
        }

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not delete items'));
        }
    }
}
