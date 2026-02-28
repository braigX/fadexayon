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
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/GridHelperFaq.php';
class AdminFaqopListsListController extends ModuleAdminController
{
    protected $grid_helper;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('List of blocks with FAQ items');
        $this->grid_helper = new GridHelperFaq($this->module);
    }

    public function initContent()
    {
        if (!$this->module->helper->isCurrentShopChosen()) {
            $this->content .= $this->module->mes->getWarningMultishopHtml();
        } else {
            $this->content .= $this->showStatusDeleteErrors();
            $this->content .= $this->displayBlockList();
        }
        parent::initContent();
    }

    protected function showStatusDeleteErrors()
    {
        if (Tools::isSubmit('statusWrong')) {
            return $this->module->displayError($this->l('The status could not be updated.'));
        }
        if (Tools::isSubmit('deleteWrong')) {
            return $this->module->displayError($this->l('Could not delete.'));
        }
        if (Tools::isSubmit('cloneWrong')) {
            return $this->module->displayError($this->l('Could not clone'));
        }
        if (Tools::isSubmit('blockWrong')) {
            return $this->module->displayError($this->l('List does not exist'));
        }

        return '';
    }

    public function displayBlockList()
    {
        $output = '';
        // Save to cookie current address to use it in "back" href of edit forms, if it wasn't update or delete
        $this->module->helper->saveToCookieCurrentAddress(ConfigsFaq::BLOCKS_COOKIE);
        $this->addJqueryUI('ui.sortable');
        $output .= $this->module->displayNav('list');
        $output .= $this->renderBlocks();
        $output .= $this->module->mes->getCurrentShopInfoMsg();

        return $output;
    }

    public function renderBlocks()
    {
        $normalUrl = $this->module->helper->getCleanBlocksListUrl();

        try {
            $newBlockUrl = $this->context->link->getAdminLink('AdminFaqopList') .
                '&create=1' .
                '&op_type=list' .
                $this->module->helper->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
        $blocks = $this->grid_helper->getBlocksForListFilter();

        foreach ($blocks as $key => $block) {
            $type_op = 'list';

            $type_op_c = Tools::ucfirst($type_op);

            try {
                $blockController = $this->context->link->getAdminLink('AdminFaqop' . $type_op_c) .
                    '&id_list=' . (int) $block['id_item'] .
                    '&op_type=' . $type_op .
                    $this->module->helper->createAnticacheString();
                $blockControllerEdit = $this->context->link->getAdminLink('AdminFaqopAddItems' . $type_op_c) .
                    '&id_list=' . (int) $block['id_item'] .
                    '&op_type=' . $type_op .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }

            $blocks[$key]['item_type_c'] = $type_op_c;
            $blocks[$key]['status_title'] = ((int) $block['active'] == 0 ? $this->l('Disabled') :
                $this->l('Enabled'));
            $blocks[$key]['status_icon'] = ((int) $block['active'] == 0 ? 'icon-remove' : 'icon-check');
            $blocks[$key]['status_class'] = ((int) $block['active'] == 0 ? 'btn-danger' : 'btn-success');

            $blocks[$key]['status_href'] = $blockController . '&changeStatus=1';
            $blocks[$key]['delete_href'] = $blockController . '&delete=1';
            $blocks[$key]['clone_href'] = $blockController . '&clone=1';
            $blocks[$key]['edit_href'] = $blockControllerEdit . '&edit=1';

            $blocks[$key]['shortcode'] = $this->module->helper->composeShortcode($block['id_item']);

            if ($blocks[$key]['not_all_languages']) {
                $iso_array = [];
                if ($blocks[$key]['languages']) {
                    $lang_array = explode(',', $blocks[$key]['languages']);
                    foreach ($lang_array as $lang_id) {
                        if ($iso = Language::getIsoById($lang_id)) {
                            $iso_array[] = $iso;
                        }
                    }
                }
                $blocks[$key]['iso_array'] = $iso_array;
            }

            if ($blocks[$key]['not_all_currencies']) {
                $iso_cur_array = [];
                if ($blocks[$key]['currencies']) {
                    $cur_array = explode(',', $blocks[$key]['currencies']);
                    foreach ($cur_array as $cur_id) {
                        if ($iso_cur = $this->module->rep->getIsoCurrencyById($cur_id)) {
                            $iso_cur_array[] = $iso_cur;
                        }
                    }
                }
                $blocks[$key]['iso_cur_array'] = $iso_cur_array;
            }
        }
        $hooks_array = $this->grid_helper->getAllHooksForList();
        ksort($hooks_array);
        $hooks_array['empty'] = $this->l('Empty');
        $hooks_array['all'] = $this->l('All');

        $change_positions = false;
        if (Tools::isSubmit('filterHook')) {
            if (!empty(Tools::getValue('filterHook'))) {
                $change_positions = true;
            }
        }

        $search = Tools::getValue('search');
        $search = pSQL($this->grid_helper->processSearchClause($search));

        $this->context->smarty->assign(
            [
                'table_title' => $this->l('Lists of FAQ items'),
                'blocks' => $blocks,
                'classnames' => $this->grid_helper->getClassNamesForTh(),
                'selectedActive' => $this->grid_helper->getSelectedActive(),
                'activeList' => $this->getActiveWordsList(),
                'selectedType' => $this->grid_helper->getSelectedType(),

                'selectedHook' => $this->grid_helper->getSelectedHook(),
                'hookList' => $hooks_array,
                'change_positions' => $change_positions,
                'clean_url' => $normalUrl,
                'searchtext' => $search,
                'new_block_link' => $newBlockUrl,
                'multistore_active' => $this->module->helper->getIsMultistoreActive(),
                'shops_list' => $this->module->helper->getShopsWithoutCurrent(),
                'old_shop' => Context::getContext()->shop->id,
                'includeTpl' => _PS_MODULE_DIR_ . 'faqop/views/templates/hook/copy_shop.tpl',
            ]
        );

        return $this->module->displayBlocks();
    }

    public function getActiveWordsList()
    {
        return [
            $this->l('All'),
            $this->l('Active'),
            $this->l('Inactive'),
        ];
    }

    public function ajaxProcessUpdateBlocksPosition()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('You cannot update positions in demo mode'));
        }

        $items = json_decode(Tools::getValue('order'), true);
        $sql = true;

        foreach ($items as $position => $id_item) {
            $parts = explode('-', $id_item);
            $item_type = $parts[1];
            $item_id = $parts[2];
            $sql &= $this->module->rep->updateBlocksPositionsAjax($item_type, $item_id, $position);
        }

        if ($sql) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Something went wrong when updating positions'));
        }
    }

    public function ajaxProcessDeleteBulkBlocks()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('Error: could not delete blocks in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'), true);
        $result = true;
        foreach ($ids as $item) {
            $result &= $this->module->rep->deleteSingleBlock($this->module, $item['id'], $item['type']);
        }

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not delete blocks'));
        }
    }

    public function ajaxProcessPublishBulkBlocks()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('Error: could not publish blocks in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'), true);
        $result = true;
        foreach ($ids as $item) {
            $result &= $this->module->rep->publishSingleBlock($this->module, $item['id'], $item['type']);
        }

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not publish blocks'));
        }
    }

    public function ajaxProcessUnpublishBulkBlocks()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('Error: could not unpublish blocks in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'), true);
        $result = true;
        foreach ($ids as $item) {
            $result &= $this->module->rep->unpublishSingleBlock($this->module, $item['id'], $item['type']);
        }

        if ($result) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not unpublish blocks'));
        }
    }

    public function ajaxProcessCopyBulkBlocksShop()
    {
        $this->module->copyBulkBlocksShop();
    }
}
