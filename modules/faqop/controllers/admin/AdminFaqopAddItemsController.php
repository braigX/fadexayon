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

class AdminFaqopAddItemsController extends ModuleAdminController
{
    protected $type_op;

    protected $id_list;

    protected $back;

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
        if (Tools::isSubmit('op_type')) {
            $this->type_op = Tools::getValue('op_type');
        }
        if (Tools::isSubmit('id_list')) {
            $this->id_list = Tools::getValue('id_list');
        }

        $this->back = $this->module->helper->getBlocksListUrl();

        if (Tools::isSubmit('edit') && Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
            $block_name = $this->module->rep->getBlockNameById(
                $this->id_list,
                $this->type_op,
                $this->context->language->id
            );
            $this->page_header_toolbar_title = $this->l('Edit ') . $block_name;
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['cancel'] = [
            'href' => $this->back,
            'desc' => $this->l('Back'),
            'icon' => 'process-icon-back',
        ];

        parent::initPageHeaderToolbar();
    }

    public function initContent()
    {
        if (!$this->module->helper->isCurrentShopChosen()) {
            $this->content .= $this->module->mes->getWarningMultishopHtml();
        } else {
            $this->addJqueryUI('ui.sortable');
            $this->content .= $this->module->helper->getCurrrentShopMessage($this->type_op, $this->back);
            if (Tools::isSubmit('removeWrong')) {
                $this->content .= $this->module->displayError($this->l('Could not remove.'));
            }
            $this->content .= $this->renderBlockNav();
            if (Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
                if ($this->module->rep->listExists(Tools::getValue('id_list'), $this->type_op)) {
                    $this->content .= $this->renderBlockItems();
                } else {
                    $this->content .= $this->module->mes->getErrorMsg($this->l('List with this id does not exist'));
                }
            } else {
                $this->content .= $this->module->mes->getErrorMsg($this->l('List id is not stated'));
            }
        }

        parent::initContent();
    }

    public function renderBlockItems()
    {
        // Save to cookie current address to use it in "back" href of edit forms, if it wasn't update or delete
        $this->module->helper->saveToCookieCurrentAddress(ConfigsFaq::ITEMS_COOKIE);

        $items = $this->module->rep->getItemsForListEdit($this->id_list, $this->type_op);

        try {
            $itemController = $this->context->link->getAdminLink('AdminFaqopItem') .
                '&id_list=' . (int) $this->id_list .
                '&op_type=' . $this->type_op .
                $this->module->helper->createAnticacheString();
            $addReadyHref = $this->context->link->getAdminLink('AdminFaqopAddReadyItem') .
                '&id_list=' . (int) $this->id_list .
                '&op_type=' . $this->type_op .
                $this->module->helper->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        $addNewItemHref = $itemController . '&create=1';

        foreach ($items as $key => $item) {
            $items[$key]['remove_href'] = $itemController . '&remove=1&id_item=' . (int) $item['id'];
            $items[$key]['edit_href'] = $itemController . '&edit=1&id_item=' . (int) $item['id'];
        }

        $this->context->smarty->assign(
            [
                'items' => $items,
                'table_title' => $this->l('FAQ Items in this list'),
                'back' => $this->back,
                'id_list' => $this->id_list,
                'type' => $this->type_op,
                'new_href' => $addNewItemHref,
                'add_ready_href' => $addReadyHref,
            ]
        );

        return $this->module->displayItemListInsideBlock();
    }

    public function ajaxProcessRemoveBulkItems()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('This action is prohibited in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'));
        $listId = Tools::getValue('listId');
        $typeList = Tools::getValue('listType');
        $result = true;

        foreach ($ids as $id) {
            $result &= $this->module->rep->removeOneItemFromBlockDb($id, $listId, $typeList);
        }
        $result &= $this->module->cache_helper->addOneBlockToCacheTable($listId, $typeList);

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not remove items'));
        }
    }

    public function ajaxProcessUpdateItemsPosition()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('This action is prohibited in demo mode'));
        }

        $items = json_decode(Tools::getValue('order'));
        $listId = Tools::getValue('listId');
        $typeList = Tools::getValue('listType');
        $sql = true;

        foreach ($items as $position => $id_item) {
            $parts = explode('-', $id_item);
            $item_id = $parts[1];
            $sql &= $this->module->rep->updateItemsPositionsAjaxBlock($item_id, $position, $listId, $typeList);
        }

        if ($sql) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Something went wrong when updating positions'));
        }
    }
}
