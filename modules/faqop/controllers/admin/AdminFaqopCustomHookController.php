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

class AdminFaqopCustomHookController extends ModuleAdminController
{
    public $output = '';

    public $delete_id;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('Manage Custom Hooks');
    }

    public function initContent()
    {
        $this->content .= $this->module->displayNav('hook');
        $this->content .= $this->getContentHooks();
        parent::initContent();
    }

    protected function getContentHooks()
    {
        /* Validate & process */
        if (Tools::isSubmit('delete_id')) {
            $this->delete_id = (int) Tools::getValue('delete_id');
            if ($this->postValidationHook()) {
                $this->postProcessHook();
            }
        }
        $this->output .= $this->renderHooks();

        return $this->output;
    }

    protected function postValidationHook()
    {
        $errors = [];

        if (!Validate::isInt($this->delete_id)
            || !$this->module->rep->hookExists($this->delete_id)) {
            $errors[] = $this->l('Invalid hook ID');
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->output .= $this->module->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    protected function postProcessHook()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            $res = $this->module->rep->deleteCustomHook($this->delete_id);

            if (!$res) {
                $this->output .= $this->module->displayError('Could not delete. One of reasons can be that this
                 hook is transplanted to another module.');
            } else {
                $this->output .= $this->module->displayConfirmation('Custom hook was successfully deleted.');
            }
        }
    }

    public function renderHooks()
    {
        $toDeleteArray = $this->module->rep->getThisModuleUnpositionedHooks();
        $infoArray = $this->module->rep->getHooksModulesNames();

        try {
            $pageUrl = $this->context->link->getAdminLink($this->controller_name) .
                $this->module->helper->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
        $this->context->smarty->assign(
            [
                'table_title' => $this->l('Custom hooks'),
                'linkSelf' => $pageUrl,
                'toDeleteArray' => $toDeleteArray,
                'infoArray' => $infoArray,
            ]
        );

        return $this->module->displayHooksTable();
    }
}
