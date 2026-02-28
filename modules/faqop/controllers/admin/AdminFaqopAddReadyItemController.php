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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/GridItemsHelperFaq.php';
class AdminFaqopAddReadyItemController extends ModuleAdminController
{
    protected $type_op;

    protected $id_list;

    protected $back;

    protected $grid_helper;

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

        $this->back = $this->module->helper->getItemsListUrl();

        if (Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
            $block_name = $this->module->rep->getBlockNameById(
                $this->id_list,
                $this->type_op,
                $this->context->language->id
            );
            $this->page_header_toolbar_title = $this->l('Add items to ') . $block_name;
        }
        $this->grid_helper = new GridItemsHelperFaq($this->module);
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
            if (Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
                if ($this->module->rep->listExists($this->id_list, $this->type_op)) {
                    $this->content .= $this->renderAddReadyGrid();
                } else {
                    $this->content .= $this->module->mes->getErrorMsg($this->l('List with this id does not exist'));
                }
            } else {
                $this->content .= $this->module->mes->getErrorMsg($this->l('List id is not stated'));
            }
        }

        parent::initContent();
    }

    protected function renderAddReadyGrid()
    {
        $search = '';
        if (Tools::isSubmit('search')) {
            $search = Tools::getValue('search');
            $search = pSQL($this->grid_helper->processSearchClause($search));
        }

        $items = $this->grid_helper->getItemsForListFilter();
        $items = $this->grid_helper->filterBelongingToBlock($items, $this->id_list, $this->type_op);

        $clearSearchLink = $this->module->helper->getCleanItemsAddReadyUrl($this->id_list, $this->type_op);

        $this->context->smarty->assign(
            [
                'items' => $items,
                'table_title' => $this->l('Add FAQ Items'),
                'classnames' => $this->grid_helper->getClassNamesForTh(),
                'searchtext' => $search,
                'clearSearchLink' => $clearSearchLink,
                'back' => $this->back,
                'type' => $this->type_op,
                'id_list' => $this->id_list,
            ]
        );

        return $this->module->displayAddReady();
    }

    public function ajaxProcessAddBulkItems()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('This action is prohibited in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'));
        $listId = Tools::getValue('listId');
        $typeList = Tools::getValue('listType');
        $result = true;

        foreach ($ids as $id) {
            $result &= $this->module->rep->addItemToBlock($id, $listId, $typeList);
        }
        $result &= $this->module->cache_helper->addOneBlockToCacheTable($listId, $typeList);

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not add items'));
        }
    }
}
