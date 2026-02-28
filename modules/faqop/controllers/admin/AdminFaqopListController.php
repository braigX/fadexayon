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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBlockGeneralController.php';

class AdminFaqopListController extends AdminFaqopBlockGeneralController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav('list');
        parent::initContent();
    }

    public function postProcessBlock()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            if ($this->postValidationBlock()) {
                if (Tools::isSubmit('changeStatus')) {
                    if (!$this->module->helper->isCurrentShopChosen()) {
                        Tools::redirectAdmin($this->back . '&statusWrong=1');
                    } else {
                        $factory = new OpFaqModelFactory();
                        $block = $factory->createBlock($this->module);
                        $block->updateStatus();
                    }
                }

                if (Tools::isSubmit('delete')) {
                    if (!$this->module->helper->isCurrentShopChosen()) {
                        Tools::redirectAdmin($this->back . '&deleteWrong=1');
                    } else {
                        $factory = new OpFaqModelFactory();
                        $block = $factory->createBlock($this->module);
                        $block->deleteWithRedirect();
                    }
                }

                if (Tools::isSubmit('clone')) {
                    if (!$this->module->helper->isCurrentShopChosen()) {
                        Tools::redirectAdmin($this->back . '&cloneWrong=1');
                    } else {
                        $factory = new OpFaqModelFactory();
                        $block = $factory->createBlock($this->module);
                        $block->cloneWithRedirect();
                    }
                }

                if ($this->module->helper->checkIfSubmitClicked()) {
                    $this->postProcessSubmitBlock();
                }
            }
        }

        return $this->output;
    }

    protected function postValidationBlock($errors = [])
    {
        $errorsInList = false;

        if (Tools::isSubmit('changeStatus') || Tools::isSubmit('delete') || Tools::isSubmit('clone')) {
            if (!$this->module->rep->listExists($this->id_list, $this->type_op)) {
                $errorsInList = true;
            }
        }

        /* Redirect with error if change status or delete without success */
        if ($errorsInList) {
            Tools::redirectAdmin($this->back . '&blockWrong=1');

            return false;
        }

        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks state (active) */
            if (!Validate::isInt(Tools::getValue('active')) || (Tools::getValue('active') != 0
                    && Tools::getValue('active') != 1)) {
                $errors[] = $this->l('Invalid block state.');
            }
        }

        return parent::postValidationBlock($errors);
    }

    protected function postProcessSubmitBlock()
    {
        /* Here we update block */
        $data = parent::postProcessSubmitBlock();

        /* Create new block. Can create only in general block settings. Can update from other forms too */

        if (!Tools::isSubmit('id_list') && Tools::isSubmit('create')) {
            $block = $data['block'];
            $errors = $data['errors'];

            if (!$block->add()) {
                $this->errors[] = $this->module->displayError($this->l(
                    'Could not create list.'
                ));
            }

            try {
                $type_op_c = Tools::ucfirst($this->type_op);
                $blockController = $this->context->link->getAdminLink('AdminFaqopAddItems' . $type_op_c) .
                    '&edit=1' .
                    '&id_list=' . (int) $block->id .
                    '&op_type=' . $this->type_op .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }

            // 1 is create
            $postHelper = new PostHelper(
                1,
                $errors,
                $blockController,
                $this->back,
                $this->module
            );

            $this->output .= $postHelper->post();
        }
    }

    public function setBlockFields($block)
    {
        parent::setBlockFields($block);
        $block->active = (int) Tools::getValue('active');
        $block->admin_name = Tools::getValue('admin_name');
    }

    public function getFormForShortcode($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $shortcode = $this->module->helper->composeShortcode($this->id_list);
        $fields['form']['legend'] = [
            'title' => $this->l('Shortcode'),
            'icon' => 'icon-edit',
        ];
        $fields['form']['input'][] = [
            'type' => 'html',
            'name' => 'shortcode',
            'html_content' => $this->module->displayShortcodeInForm($shortcode),
        ];

        return $fields;
    }

    public function getFieldsForm()
    {
        $fields_form = parent::getFieldsForm();

        if (Tools::isSubmit('edit')) {
            $fields_form[0] = $this->getFormForShortcode();
        }

        return $fields_form;
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields = parent::getFieldsFormFirst($fields);

        array_unshift($fields['form']['input'], [
            'type' => 'text',
            'label' => $this->l('Internal Name'),
            'name' => 'admin_name',
            'class' => 'title-text-field',
            'desc' => $this->l('Shown only in the admin panel for your convenience.'),
        ]);

        array_unshift($fields['form']['input'], [
            'type' => 'switch',
            'label' => $this->l('Enabled'),
            'name' => 'active',
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ],
        ]);

        return $fields;
    }

    public function renderBlockNav($active_url = 'general')
    {
        return $this->module->helper->renderBlockNavTabs($active_url, $this->id_list);
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields = parent::getAddBlockFieldsValues($block, $fields);
        $fields['active'] = Tools::getValue('active', $block->active);
        $fields['admin_name'] = Tools::getValue('admin_name', $block->admin_name);

        return $fields;
    }
}
