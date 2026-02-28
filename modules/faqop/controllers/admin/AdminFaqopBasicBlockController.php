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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/PostHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqModelFactory.php';

abstract class AdminFaqopBasicBlockController extends ModuleAdminController
{
    /**
     * 1) post processes
     * 2) form
     */
    protected $type_op;

    protected $output;

    protected $back;

    protected $id_list;

    /**
     * @param $block
     *
     * @return mixed
     *               Data to be saved in post process
     */
    abstract public function setBlockFields($block);

    /**
     * @param $block
     *
     * @return mixed
     *               Fill fields in form
     */
    abstract public function getAddBlockFieldsValues($block);

    /**
     * @return mixed
     *               Add fields to form
     */
    abstract public function getFieldsFormFirst();

    public function getFieldsForm()
    {
        $fields_form = [];
        $fields_form['form'] = $this->getFieldsFormFirst();
        $fields_form[1] = [
            'form' => $this->module->getSaveCancelButtons($this->back),
        ];

        return $fields_form;
    }

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
        $this->output = '';
        if (Tools::isSubmit('create')) {
            $this->page_header_toolbar_title = $this->l('Create');
        } elseif (Tools::isSubmit('edit') && Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
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
            // check if we need to process request or just display form
            if ($this->module->helper->checkIfSubmitClicked()
                || Tools::isSubmit('changeStatus')
                || Tools::isSubmit('delete')
                || Tools::isSubmit('clone')) {
                $this->content .= $this->postProcessBlock();
            }
            $this->content .= $this->module->helper->getCurrrentShopMessage($this->type_op, $this->back);
            if (!Tools::isSubmit('create')) {
                if ($this->module->rep->listExists(Tools::getValue('id_list'), $this->type_op)) {
                    $this->content .= $this->renderBlockNav();
                } else {
                    $this->content .= $this->module->mes->getErrorMsg($this->l('List with this id does not exist'));
                }
            } else {
                $this->content .= $this->module->mes->getCreateListInfoMessage();
            }

            $this->display = 'edit';
        }

        parent::initContent();
    }

    public function postProcessBlock()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            if ($this->postValidationBlock()) {
                if ($this->module->helper->checkIfSubmitClicked()) {
                    $this->postProcessSubmitBlock();
                }
            }
        }

        return $this->output;
    }

    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            /* If edit : checks id_list */
            if (Tools::isSubmit('edit') && Tools::isSubmit('id_list')) {
                if (!Validate::isInt($this->id_list)
                    && !$this->module->rep->listExists((int) $this->id_list, $this->type_op)) {
                    $errors[] = $this->l('Invalid block ID');
                }
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->output .= $this->module->displayError($this->l('Could not save'));
            $this->output .= $this->module->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    protected function postProcessSubmitBlock()
    {
        $errors = [];
        /* Make block object, Sets ID if needed */
        $factory = new OpFaqModelFactory();
        $block = $factory->createBlock($this->module);

        $this->setBlockFields($block);

        if (Tools::isSubmit('id_list') && Tools::isSubmit('edit')) {
            if (!$block->update()) {
                $this->errors[] = $this->module->displayError($this->l(
                    'Could not update list.'
                ));
            }

            try {
                $blockController = $this->context->link->getAdminLink($this->controller_name) .
                    '&edit=1' .
                    '&id_list=' . (int) $block->id .
                    '&op_type=' . $this->type_op .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }

            // 0 is for update
            $postHelper = new PostHelper(
                0,
                $errors,
                $blockController,
                $this->back,
                $this->module
            );

            $this->output .= $postHelper->post();
        }

        return ['block' => $block, 'errors' => $errors];
    }

    public function renderBlockNav($active_url = 'items')
    {
        return $this->module->helper->renderBlockNavTabs($active_url, $this->id_list);
    }

    public function renderForm()
    {
        if (!Tools::isSubmit('create')
            && !$this->module->rep->listExists(Tools::getValue('id_list'), $this->type_op)) {
            return false;
        }
        $fields_form = $this->getFieldsForm();

        $helper = new HelperForm();
        $helper->toolbar_scroll = false;

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        if (Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')) {
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        } else {
            $helper->allow_employee_form_lang = 0;
        }
        $this->fields_form = [];
        $helper->module = $this->module;
        $helper->identifier = $this->module->name;
        $helper->submit_action = 'submitBlock';
        $helper->token = $this->token;

        $factory = new OpFaqModelFactory();
        $block = $factory->createBlock($this->module);

        $helper->tpl_vars = [
            'language' => [
                'id_lang' => $lang->id,
                'iso_code' => $lang->iso_code,
            ],
            'fields_value' => $this->getAddBlockFieldsValues($block),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $helper->override_folder = '/';

        return $helper->generateForm($fields_form);
    }

    public function ajaxProcessGetProductNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getProduct($id);
        if ($result) {
            exit($result[0]['name']);
        } else {
            $this->module->helper->throwError($this->l('Product with this id was not found'));
        }
    }

    public function ajaxProcessGetCategoryNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getCategory($id);
        if ($result) {
            exit($result[0]['name']);
        } else {
            $this->module->helper->throwError($this->l('Category with this id was not found'));
        }
    }

    public function ajaxProcessGetBrandNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getBrand($id);
        if ($result) {
            exit($result[0]['name']);
        } else {
            $this->module->helper->throwError($this->l('Brand with this id was not found'));
        }
    }

    public function ajaxProcessGetTagNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getTag($id);
        if ($result) {
            exit($result[0]['name']);
        } else {
            $this->module->helper->throwError($this->l('Tag with this id was not found'));
        }
    }

    public function ajaxProcessGetFeatureNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getFeature($id);
        if ($result) {
            exit($result[0]['name']);
        } else {
            $this->module->helper->throwError($this->l('Feature with this id was not found'));
        }
    }

    public function ajaxProcessGetCmsPageNameOp()
    {
        $id = Tools::getValue('id');
        $result = $this->module->rep->getCmsPage($id);
        if ($result) {
            exit($result[0]['meta_title']);
        } else {
            $this->module->helper->throwError($this->l('CMS page with this id was not found'));
        }
    }

    public function ajaxProcessCheckCustomHook()
    {
        $hook_name = Tools::getValue('hook');
        if ($this->module->helper->hasBadWordsInHook($hook_name)) {
            $this->module->helper->throwError($this->l('Your hook name contains forbidden words'));
        }
        if (!Validate::isHookName($hook_name)) {
            $this->module->helper->throwError($this->l('Your hook name contains forbidden symbols'));
        }

        try {
            if ($this->module->helper->getHookIdByName($hook_name)) {
                $this->module->helper->throwError($this->l('A hook with this name already exists'));
            }
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        exit;
    }
}
