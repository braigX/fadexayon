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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicItemController.php';

class AdminFaqopBindItemController extends AdminFaqopBasicItemController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav($this->module->helper->getItemsParent());
        $this->content .= $this->module->mes->getCurrentShopInfoMsgItemBlock();

        parent::initContent();
    }

    public function getFieldsForm()
    {
        $fields_form = [];
        $fields_form['form'] = $this->getFieldsFormFirst();
        $fields_form[0] = $this->getFormForBindingBlocks();
        $fields_form[1] = [
            'form' => $this->module->getSaveCancelButtons($this->back),
        ];

        return $fields_form;
    }

    public function renderItemNav($active_url = 'bindings')
    {
        return parent::renderItemNav($active_url);
    }

    protected function postProcessSubmitItem()
    {
        $errors = [];
        /* Make block object, Sets ID if needed */
        $factory = new OpFaqModelFactory();
        $item = $factory->createItem($this->module);

        if (Tools::isSubmit('id_item') && Tools::isSubmit('edit')) {
            if (Tools::getValue('belongs_to_page') == 1) {
                if (!$item->addToPage()) {
                    $errors[] = $this->module->displayError($this->l(
                        'The item could not be added to FAQ page.'
                    ));
                }
            } else {
                if (!$item->removeFromPage()) {
                    $errors[] = $this->module->displayError($this->l(
                        'The item could not be removed from FAQ page.'
                    ));
                }
            }
            if (!empty(Tools::getValue('bind_blocks'))) {
                if (!$item->updateItemForBlocks(Tools::getValue('bind_blocks'))) {
                    $errors[] = $this->module->displayError($this->l(
                        'The items relation to blocks could not be updated'
                    ));
                }
            } else {
                if (!$item->removeAllBlocksFromItemShop()) {
                    $errors[] = $this->module->displayError($this->l(
                        'The items relation to blocks could not be deleted'
                    ));
                }
            }

            try {
                $itemController = $this->context->link->getAdminLink($this->controller_name) .
                    '&edit=1' .
                    '&id_item=' . (int) $item->id .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
            $postHelper = new PostHelper(
                0,
                $errors,
                $itemController,
                $this->back,
                $this->module
            );

            $this->output .= $postHelper->post();
        }
    }

    public function setItemFields($item)
    {
    }

    public function getAddItemFieldsValues($item, $fields = [])
    {
        $pageIsBound = $this->module->rep->itemBelongsToPage(Tools::getValue('id_item'));
        $fields['belongs_to_page'] = Tools::getValue('belongs_to_page', $pageIsBound);

        return $fields;
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        return $this->getFormForBindingPage();
    }

    public function getFormForBindingBlocks($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $blocks_to_bind = $this->module->rep->getBlocksForItemBind(Tools::getValue('id_item'));

        $fields['form']['legend'] = [
            'title' => $this->l('Item Belongs To Lists:'),
            'icon' => 'icon-folder-open',
        ];
        $fields['form']['input'][] = [
            'type' => 'op_bind_blocks_for_item_checkbox',
            'form_group_class' => 'bind_blocks_for_item checkbox-block-custom',
            'name' => 'bind_blocks[]',
            'values' => $blocks_to_bind,
        ];

        return $fields;
    }

    public function getFormForBindingPage($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('Item Belongs To Page:'),
            'icon' => 'icon-pagelines',
        ];
        $fields['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Item Belongs To Page:'),
            'name' => 'belongs_to_page',
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'belongs_to_page_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'belongs_to_page_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ],
        ];

        return $fields;
    }
}
