<?php
/**
 * 2007-2026 PrestaShop
 *
 * Performance remediation workspace module.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaLoad extends Module
{
    public const CONFIG_ENABLED = 'PRESTALOAD_ENABLED';
    public const CONFIG_AUDIT_REPORT = 'PRESTALOAD_AUDIT_REPORT';
    public const CONFIG_PLAN_PATH = 'PRESTALOAD_PLAN_PATH';

    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->trans('PrestaLoad', [], 'Modules.Prestaload.Admin');
        $this->description = $this->trans(
            'Provides a controlled module workspace for homepage performance remediation based on Lighthouse findings.',
            [],
            'Modules.Prestaload.Admin'
        );
        $this->confirmUninstall = $this->trans(
            'This removes PrestaLoad configuration only. It does not delete the audit JSON or plan files.',
            [],
            'Modules.Prestaload.Admin'
        );
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue(self::CONFIG_ENABLED, 1)
            && Configuration::updateValue(self::CONFIG_AUDIT_REPORT, 'modules/prestaload/plexi.local.test-20260314T180057.json')
            && Configuration::updateValue(self::CONFIG_PLAN_PATH, 'modules/prestaload/plan.md')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        return Configuration::deleteByName(self::CONFIG_ENABLED)
            && Configuration::deleteByName(self::CONFIG_AUDIT_REPORT)
            && Configuration::deleteByName(self::CONFIG_PLAN_PATH)
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitPrestaLoadSettings')) {
            Configuration::updateValue(self::CONFIG_ENABLED, (int) Tools::getValue(self::CONFIG_ENABLED, 1));
            $output .= $this->displayConfirmation($this->trans('Settings updated.', [], 'Admin.Notifications.Success'));
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPrestaLoadSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
        ];

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->displayName,
                    'icon' => 'icon-dashboard',
                ],
                'description' => $this->trans(
                    'This module is the execution point for the performance remediation plan built from the bundled Lighthouse audit.',
                    [],
                    'Modules.Prestaload.Admin'
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable module hooks', [], 'Modules.Prestaload.Admin'),
                        'name' => self::CONFIG_ENABLED,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => self::CONFIG_ENABLED . '_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => self::CONFIG_ENABLED . '_off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Audit report', [], 'Modules.Prestaload.Admin'),
                        'name' => self::CONFIG_AUDIT_REPORT,
                        'readonly' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Execution plan', [], 'Modules.Prestaload.Admin'),
                        'name' => self::CONFIG_PLAN_PATH,
                        'readonly' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        return $output . $helper->generateForm([$form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            self::CONFIG_ENABLED => (int) Configuration::get(self::CONFIG_ENABLED, 1),
            self::CONFIG_AUDIT_REPORT => (string) Configuration::get(self::CONFIG_AUDIT_REPORT, 'modules/prestaload/plexi.local.test-20260314T180057.json'),
            self::CONFIG_PLAN_PATH => (string) Configuration::get(self::CONFIG_PLAN_PATH, 'modules/prestaload/plan.md'),
        ];
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (!$this->isEnabled()) {
            return;
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!$this->isEnabled()) {
            return;
        }
    }

    private function isEnabled()
    {
        return (bool) Configuration::get(self::CONFIG_ENABLED, 1);
    }
}
