<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }


require_once(dirname(__FILE__) . '/AdminEtsACOptionsController.php');

class AdminEtsACReminderLeaveController extends AdminEtsACOptionsController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->fields_options = array(
            'title' => $this->l('Leave website config', 'AdminEtsACReminderLeaveController'),
            'fields' => $this->def->getLeaveConfigs(true),
            'icon' => '',
            'submit' => array(
                'title' => $this->l('Save', 'AdminEtsACReminderLeaveController'),
            ),
        );
    }

    public function renderOptions()
    {
        return parent::renderOptions() . $this->getLeadForm();
    }

    public function getLeadForm()
    {
        $PS_ATTACHMENT_MAXIMUM_SIZE = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
        if ($this->isCached(($template = 'views/templates/hook/lead_form_list.tpl'), EtsAbancartCache::getCachedId('lead_form_list', null, $PS_ATTACHMENT_MAXIMUM_SIZE))) {
            $this->context->smarty->assign(array(
                'lead_forms' => EtsAbancartForm::getAllForms(false, true),
                'maxSizeUpload' => $PS_ATTACHMENT_MAXIMUM_SIZE,
                'reminderType' => 'leave',
                'field_types' => EtsAbancartField::getInstance()->getFieldType(),
                'isAdmin' => 1,
                'module_dir' => _PS_MODULE_DIR_ . $this->module->name,
                'is17Ac' => $this->module->is17,
                'baseUri' => __PS_BASE_URI__
            ));
        }
        return $this->module->display($this->module->getLocalPath(), $template, EtsAbancartCache::getCachedId('lead_form_list', null, $PS_ATTACHMENT_MAXIMUM_SIZE));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(array(
            _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js',
        ));
    }
}