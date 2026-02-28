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

require_once dirname(__FILE__) . '/../../classes/EtsRVValidate.php';
require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';

class AdminEtsRVOptionController extends AdminEtsRVBaseController
{
    public $submit;

    public function __construct()
    {
        parent::__construct();
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->submit)) {
            $this->_postConfigs();
        } else
            parent::postProcess();
    }

    public function getConfigs()
    {
        return [];
    }

    public function getConfigTabs()
    {
        return [];
    }

    public function getTemplateVars($helper)
    {
        $tab = trim(Tools::getValue('current_tab_active'));
        if (trim($tab) == '' || Validate::isCleanHtml($tab)) {
            $tab = 'general';
        }
        return array(
            'fields_value' => $this->getConfigFieldsValues($helper->submit_action),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'pathURI' => $this->module->getPathUri(),
            'config_tabs' => $this->getConfigTabs(),
            'name_controller' => 'ets_rv_form_config',
            'current_tab_active' => $tab,
            'currentIndex' => self::$currentIndex . '&token=' . $this->token,
        );
    }

    public function renderOptions()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings', 'AdminEtsRVOptionController'),
                    'icon' => '',
                ),
                'input' => $this->getConfigs(),
                'buttons' => array(
                    'reset' => array(
                        'href' => self::$currentIndex . '&ajax=1&action=geneColors&token=' . $this->token,
                        'title' => $this->l('Reset to default', 'AdminEtsRVOptionController'),
                        'icon' => 'process-icon-reset',
                        'class' => 'ets_rv_reset_to_default hide',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsRVOptionController'),
                    'class' => 'btn btn-default pull-right',
                    'name' => $this->submit,
                    'stay' => 1,
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this->module;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = $this->submit;
        $helper->currentIndex = self::$currentIndex . '&token=' . $this->token . '#' . Tools::getValue('current_tab_active', 'general');
        $helper->tpl_vars = $this->getTemplateVars($helper);

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues($submit_action)
    {
        $fields = [];
        $configs = $this->getConfigs();
        $customers_search = array();
        if ($configs) {
            $languages = Language::getLanguages(false);
            if (Tools::isSubmit($submit_action)) {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
                        }
                    } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                        $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = Tools::getValue($key, array());
                    } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes') {
                        $fields[$key] = Tools::getValue($key, array());
                    } else {
                        $fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
                    }
                    if (isset($config['customer_search'])) {
                        $customers_search[$key] = $fields[$key];
                    }
                }
            } else {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    $global = !empty($config['global']) ? 1 : 0;
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = $this->getFields($key, $global, $l['id_lang']);
                        }
                    } elseif (isset($config['multiple']) && $config['multiple']) {
                        $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = ($result = $this->getFields($key, $global)) != '' ? explode(',', $result) : array();
                    } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes') {
                        $fields[$key] = ($result = $this->getFields($key, $global)) != '' ? explode(',', $result) : array();
                    } else {
                        $fields[$key] = $this->getFields($key, $global);
                    }
                    if (isset($config['customer_search'])) {
                        $customers_search[$key] = $fields[$key];
                    }
                }
            }
            if ($customers_search) {
                foreach ($customers_search as $key => $field) {
                    $fields[$key . '_customers'] = $field ? EtsRVTools::getCustomers(explode(',', $field)) : array();
                }
            }
        }

        return $fields;
    }

    public function _postConfigs()
    {
        $configs = $this->getConfigs();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($configs) {
            foreach ($configs as $config) {
                $key = $config['name'];
                if (isset($config['lang']) && $config['lang']) {
                    if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && $this->requiredFields($key, $config, $id_lang_default)) {
                        $this->errors[] = $config['label'] . ' ' . $this->l('is required', 'AdminEtsRVOptionController');
                    }
                } else {
                    if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && $this->requiredFields($key, $config)) {
                        $this->errors[] = $config['label'] . ' ' . $this->l('is required', 'AdminEtsRVOptionController');
                    } elseif (isset($config['validate']) && !is_array(Tools::getValue($key)) && trim(Tools::getValue($key)) !== '' && $this->validateFields($key, $config)) {
                        $validate = $config['validate'];
                        if ((method_exists('EtsRVValidate', $validate) && !EtsRVValidate::$validate(trim(Tools::getValue($key))) || method_exists('Validate', $validate) && !Validate::$validate(trim(Tools::getValue($key)))) ||
                            $key === 'ETS_RV_MAX_UPLOAD_PHOTO' && !trim(Tools::getValue($key)) ||
                            $key === 'ETS_RV_DISCOUNT_CODE' && (int)Tools::getValue('ETS_RV_DISCOUNT_ENABLED') > 0 && trim(Tools::getValue('ETS_RV_DISCOUNT_OPTION')) == 'fixed' && !CartRule::cartRuleExists(Tools::getValue($key)) ||
                            $validate == 'isNullOrUnsignedId' && (int)trim(Tools::getValue($key)) == 0
                        ) {
                            $this->errors[] = $config['label'] . ' ' . $this->l('is invalid', 'AdminEtsRVOptionController');
                        }
                        unset($validate);
                    } elseif (!is_array(Tools::getValue($key)) && !Validate::isCleanHtml(trim(Tools::getValue($key)))) {
                        $this->errors[] = $config['label'] . ' ' . $this->l('is invalid', 'AdminEtsRVOptionController');
                    }
                }
                // Custom valid:
                if (trim($key) == 'ETS_RV_MAXIMUM_RATING_PER_USER' && ($max_review = (int)Tools::getValue('ETS_RV_MAXIMUM_REVIEW_PER_USER')) > 0 && ($max_rating = (int)Tools::getValue($key)) > 0 && $max_review < $max_rating) {
                    $this->errors[] = $config['label'] . ' ' . $this->l('is invalid', 'AdminEtsRVOptionController');
                }
            }
        }
        if (!$this->errors) {
            if ($configs) {
                foreach ($configs as $config) {
                    $global = !empty($config['global']) ? 1 : 0;
                    $key = $config['name'];
                    if (isset($config['update']) && !$config['update'])
                        continue;
                    if (isset($config['lang']) && $config['lang']) {
                        $values = array();
                        foreach ($languages as $lang) {
                            if ($config['type'] == 'switch')
                                $values[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                            else
                                $values[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                        }
                        $this->setFields($key, $global, $values, true);
                    } else {
                        if ($config['type'] == 'switch') {
                            $this->setFields($key, $global, (int)trim(Tools::getValue($key)) ? 1 : 0, true);
                        } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes' || $config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                            $this->setFields($key, $global, implode(',', Tools::getValue($key, array())), true);
                        } else {
                            $this->setFields($key, $global, trim(Tools::getValue($key)), true);
                        }
                    }
                }
            }
        }

        if (count($this->errors) < 1) {
            $this->confirmations[] = $this->_conf[6];
            if (Module::isEnabled('ets_superspeed'))
                Hook::exec('actionAdminPerformanceControllerSaveAfter');
        }
    }

    public function getFields($key, $global = false, $idLang = null)
    {
        return $global ? Configuration::getGlobalValue($key, $idLang) : Configuration::get($key, $idLang);
    }

    public function setFields($key, $global, $values, $html = false)
    {
        return $global ? Configuration::updateGlobalValue($key, $values, $html) : Configuration::updateValue($key, $values, $html);
    }

    public function requiredFields($key, $config, $id_lang_default = null)
    {
        return $config['type'] !== 'checkboxes' && trim(Tools::getValue($key . ($id_lang_default !== null ? '_' . $id_lang_default : ''), '')) == '' || $config['type'] == 'checkboxes' && empty(Tools::getValue($key));
    }

    public function validateFields($key, $config)
    {
        return trim($key) !== '' && isset($config['validate']) && trim($config['validate']) !== '' && method_exists('Validate', $config['validate']);
    }
}