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

abstract class AdminFaqopBasicItemController extends ModuleAdminController
{
    protected $output;

    protected $back;

    protected $id_item;

    abstract public function setItemFields($item);

    abstract public function getAddItemFieldsValues($item);

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

        if (Tools::isSubmit('id_item')) {
            $this->id_item = Tools::getValue('id_item');
        }
        $this->back = $this->module->helper->getItemsListUrl();
        $this->output = '';
        if (Tools::isSubmit('create')) {
            $this->page_header_toolbar_title = $this->l('Create');
        } elseif (Tools::isSubmit('edit') && Tools::isSubmit('id_item')) {
            $item_name = $this->module->rep->getItemNameById(
                $this->id_item,
                $this->context->language->id
            );
            $this->page_header_toolbar_title = $this->l('Edit ') . $item_name;
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['cancel'] = [
            'href' => $this->back,
            'desc' => $this->l('Back to list'),
            'icon' => 'process-icon-back',
        ];

        parent::initPageHeaderToolbar();
    }

    public function initContent()
    {
        $this->content .= $this->module->mes->getItemInfoMessage();
        if (!$this->module->helper->isCurrentShopChosen()) {
            $this->content .= $this->module->mes->getWarningMultishopAboutItem();
        } else {
            // check if we need to process request or just display form
            if ($this->module->helper->checkIfSubmitClicked()
                || Tools::isSubmit('delete')
                || Tools::isSubmit('clone')
                || Tools::isSubmit('remove')
            ) {
                $this->content .= $this->postProcessItem();
            }
            if (!Tools::isSubmit('create')) {
                $this->content .= $this->renderItemNav();
            }

            $this->display = 'edit';
        }

        parent::initContent();
    }

    public function postProcessItem()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            if ($this->postValidationItem()) {
                if ($this->module->helper->checkIfSubmitClicked()) {
                    $this->postProcessSubmitItem();
                }
            }
        }

        return $this->output;
    }

    protected function postValidationItem($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            /* If edit : checks id_item */
            if (Tools::isSubmit('edit') && Tools::isSubmit('id_item')) {
                if (!Validate::isInt($this->id_item)
                    && !$this->module->rep->itemExists((int) $this->id_item)) {
                    $errors[] = $this->l('Invalid item ID');
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

    protected function postProcessSubmitItem()
    {
        $errors = [];
        /* Make block object, Sets ID if needed */
        $factory = new OpFaqModelFactory();
        $item = $factory->createItem($this->module);

        $this->setItemFields($item);

        if (Tools::isSubmit('id_item') && Tools::isSubmit('edit')) {
            if (!$item->update()) {
                $errors[] = $this->module->displayError($this->l(
                    'Could not update item.'
                ));
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

        return ['item' => $item, 'errors' => $errors];
    }

    public function renderItemNav($active_url = 'general')
    {
        $content = '';

        $urls = $this->module->helper->makeItemNavTabsUrls($this->id_item);

        if (Tools::isSubmit('edit')) {
            $content .= $this->module->displayItemNavTabs(
                $active_url,
                $urls['general_url'],
                $urls['styles_url'],
                $urls['bindings_url']
            );
        }

        return $content;
    }

    public function renderForm()
    {
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
        $item = $factory->createItem($this->module);

        $helper->tpl_vars = [
            'ajax_url' => $this->context->link->getModuleLink(
                $this->module->name,
                'ajax',
                [],
                null,
                null,
                $this->module->rep->getMainShopId()
            ),
            'language' => [
                'id_lang' => $lang->id,
                'iso_code' => $lang->iso_code,
            ],
            'fields_value' => $this->getAddItemFieldsValues($item),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $helper->override_folder = '/';

        return $helper->generateForm($fields_form);
    }
}
