<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2024 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DmuEbpExport extends Module
{
    protected $menu_name_fr;
    protected $menu_name;
    protected $menu_parent;
    protected $menu_controller;
    protected $description_complete;
    protected $comment_acceder;
    protected $config_tabs;

    public function __construct()
    {
        require_once _PS_MODULE_DIR_ . 'dmuebpexport/classes/DmuEbpExportModel.php';

        $this->name = 'dmuebpexport';
        $this->tab = 'administration';
        $this->version = '3.1.2';
        $this->author = 'Dream me up';
        $this->module_key = '2ea1a98809d3bfbab94668c2e2bf78d5';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Accountant export and EBP');
        $this->description = $this->l('Export your bills and your assets to your accounting software EBP');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall ?');

        $this->menu_parent = [
            'default' => 'AdminOrders',
            '1.7' => 'AdminParentOrders',
        ];
        $this->menu_controller = 'AdminDmuEbpExport';
        $this->menu_name = 'Accountant export';
        $this->menu_name_fr = 'Export comptable';

        // Description détaillée du module pour l'onglet configuration
        $this->description_complete = $this->l('Export your bills and your assets to your accounting software EBP');

        // Comment accéder au module ?
        $comment_acceder = $this->l('To use this addon, you must use the menu to access Orders > Accountant export');
        $this->comment_acceder = $comment_acceder;

        // Onglets à afficher dans la configuration (mettre un tableau vide si pas de config)
        $this->config_tabs = [
            'Configuration8' => [
                'name' => $this->l('Export setup'),
                'is_helper' => true,
            ],
            'Configuration1' => [
                'name' => $this->l('Journal setup'),
                'is_helper' => true,
            ],
            'Configuration2' => [
                'name' => $this->l('Clients accounts setup'),
                'is_helper' => true,
            ],
            'Configuration4' => [
                'name' => $this->l('Payment accounts configuration'),
                'is_helper' => true,
            ],
            'Configuration3' => [
                'name' => $this->l('Products accounts setup'),
                'is_helper' => true,
            ],
            'Configuration7' => [
                'name' => $this->l('Carriers accounts configuration'),
                'is_helper' => true,
            ],
            'Configuration5' => [
                'name' => $this->l('VAT accounts configuration'),
                'is_helper' => true,
            ],
            'Configuration6' => [
                'name' => $this->l('Categories accounts configuration'),
                'is_helper' => true,
            ],
            'Help' => [
                'name' => $this->l('User guide'),
                'is_helper' => false,
            ],
        ];

        $this->checkUpdate();

        if (Tools::getIsset('ajax') && Tools::getIsset('action')) {
            $ajax = 'displayAjax' . Tools::getValue('action');
            if (method_exists($this, $ajax)) {
                $this->$ajax();
            }
        }
    }

    public function checkUpdate()
    {
        if (!Module::isInstalled($this->name)) {
            return;
        }

        $dmuebp_version = Configuration::get('DMUEBP_VERSION');

        if (!$dmuebp_version || version_compare($dmuebp_version, '2.2.0', '<')) {
            $this->registerHook('displayAdminCustomers');
        }

        if (version_compare($dmuebp_version, '2.7.0', '<')) {
            Configuration::updateValue('DMUEBP_SEPARATOR', ';');
            Configuration::updateValue('DMUEBP_EXORTZERO', '0');
        }

        if (version_compare($dmuebp_version, $this->version, '<')) {
            Configuration::updateGlobalValue('DMUEBP_VERSION', $this->version);
        }
    }

    public function getIdTab($tab_class)
    {
        return Db::getInstance()->getValue('
            SELECT id_tab 
            FROM ' . _DB_PREFIX_ . "tab 
            WHERE class_name = '" . pSQL($tab_class) . "'");
    }

    public function createHelperForm($form_id = '')
    {
        $helper_form = new HelperForm();
        $helper_form->show_toolbar = false;
        $helper_form->table = $this->table;

        $helper_form->identifier = $this->identifier;
        $helper_form->submit_action = 'Configuration' . $form_id;
        $helper_form->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
        '&configure=' . $this->name .
        '&tab_module=' . $this->tab .
        '&module_name=' . $this->name;
        $helper_form->token = Tools::getAdminTokenLite('AdminModules');
        $helper_form->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues('Configuration' . $form_id),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => (int) $this->context->language->id,
        ];

        return $helper_form;
    }

    /* Page guide utilisateur */
    public function getUserGuideContent()
    {
        $this->context->smarty->assign(
            [
                'path_module' => '../modules/' . $this->name . '',
                'txt_title' => $this->l('Accountant export and EBP'),
                'txt_description_line0' => $this->l('txt_description_line0'),
                'txt_description_line1' => $this->l('txt_description_line1'),
                'txt_description_line2' => $this->l('txt_description_line2'),
                'txt_import_process' => $this->l('txt_import_process'),
                'txt_step' => $this->l('txt_step'),
                'txt_desc_step2' => $this->l('txt_desc_step2'),
                'txt_desc_step3' => $this->l('txt_desc_step3'),
                'txt_desc_step5' => $this->l('txt_desc_step5'),
                'txt_desc_step6' => $this->l('txt_desc_step6'),
                'txt_desc_step6b' => $this->l('txt_desc_step6b'),
                'txt_desc_step7' => $this->l('txt_desc_step7'),
            ]
        );

        return $this->context->smarty->fetch(dirname(__FILE__) . '/views/templates/admin/user_guide.tpl');
    }

    /* Configuration du journal */
    public function getConfigurationForm1()
    {
        $form_id = 1;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Journal setup'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Journal'),
                        'name' => 'DMUEBP_JOURNAL',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Only for EBP export.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    /* Configuration comptes client */
    public function getConfigurationForm2()
    {
        $form_id = 2;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Clients accounts setup'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Customer account by default'),
                        'name' => 'DMUEBP_COMPTE_CLIENT',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...client'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Selling products with tax by default'),
                        'name' => 'DMUEBP_COMPTE_PRODUIT_TTC',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...product with vat.'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Selling products without tax by default'),
                        'name' => 'DMUEBP_COMPTE_PRODUIT_HT',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...product without vat'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Carrier with tax by default'),
                        'name' => 'DMUEBP_COMPTE_TRANSPORT_TTC',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...carrier with vat'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Carrier without tax by default'),
                        'name' => 'DMUEBP_COMPTE_TRANSPORT_HT',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...carrier with vat'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account tax by default'),
                        'name' => 'DMUEBP_COMPTE_TAX',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Considers...tax'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account reduction with tax'),
                        'name' => 'DMUEBP_COMPTE_RRR_TTC',
                        'required' => true,
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account reduction without tax'),
                        'name' => 'DMUEBP_COMPTE_RRR_HT',
                        'required' => true,
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Gift packaging with tax'),
                        'name' => 'DMUEBP_COMPTE_EMBALLAGE_TTC',
                        'required' => true,
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Account Gift packaging without tax'),
                        'name' => 'DMUEBP_COMPTE_EMBALLAGE_HT',
                        'required' => true,
                        'lang' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    /* Configuration comptes produits */
    public function getConfigurationForm3()
    {
        if (0 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
            $form_id = 3;

            $taxs = TaxRulesGroup::getTaxRulesGroups();

            $fields_form = [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Products accounts setup'),
                        'icon' => 'icon-cogs',
                    ],
                    'input' => [
                        ['type' => 'hidden', 'name' => 'form_id'],
                    ],
                    'submit' => [
                        'title' => $this->l('Update'),
                    ],
                ],
            ];

            foreach ($taxs as $t) {
                $fields_form['form']['input'][] = [
                    'type' => 'text',
                    'class' => 'input fixed-width-sm',
                    'label' => '<strong>' . $this->l('Tax rule') . ' ID : ' . $t['id_tax_rules_group'] . ' - ' . $t['name'] . '</strong>',
                    'name' => 'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'],
                    'lang' => false,
                    'desc' => $this->l('Default value if empty:') . ' ' . Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC'),
                ];

                $default = Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);
                if (empty($default)) {
                    $default = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');
                }

                // 1 - On va récupérer l'id tax du pays par défaut pour cette règle
                $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
                foreach ($taxes as $tax2) {
                    $fields_form['form']['input'][] = [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm ml-5',
                        'label' => $this->l('Tax Account') . ' : ' . $t['name'] .
                            '<br/>' . $tax2['country_name'] . ' ' . $tax2['rate'] . '%',
                        'name' => 'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'],
                        'lang' => false,
                        'desc' => $this->l('Default value if empty:') . ' ' . $default,
                    ];
                }
            }

            $helper_form = $this->createHelperForm($form_id);

            return $this->postProcess() . $helper_form->generateForm([$fields_form]);
        } else {
            $this->context->smarty->assign([
                'txt_warning' => $this->l('There is 1 warning.'),
                'txt_filter' => $this->l('You must choose simple filter...'),
            ]);

            $warning = $this->display(__FILE__, 'views/templates/admin/warning_filter.tpl');

            return $this->postProcess() . $warning;
        }
    }

    /* Configuration comptes paiement */
    public function getConfigurationForm4()
    {
        $form_id = 4;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Payment accounts configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        $sql = 'SELECT DISTINCT `payment_method` AS payment FROM ' . _DB_PREFIX_ . 'order_payment';
        $res_payments = Db::getInstance()->ExecuteS($sql);
        foreach ($res_payments as &$rp) {
            $rp['payment'] = DmuEbpExportModel::getPaymentModeSimpleName($rp['payment']);
        }
        $res_payments = array_map(function ($json) { return json_decode($json, true); }, array_unique(array_map('json_encode', $res_payments)));

        foreach ($res_payments as $rp) {
            $payment = str_replace(
                [' ', '[', ']'],
                ['_', '_', '_'],
                Tools::strtoupper(trim(Tools::replaceAccentedChars($rp['payment'])))
            );
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'class' => 'input fixed-width-sm',
                'label' => $this->l('Account') . ' ' . $rp['payment'],
                'name' => 'DMUEBP_COMPTE_PAYMENT[' . $payment . ']',
                'lang' => false,
                'desc' => $this->l('Default value if empty:') . ' ' . Configuration::get('DMUEBP_COMPTE_CLIENT'),
            ];
        }

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    /* Configuration compte de tva */
    public function getConfigurationForm5()
    {
        $form_id = 5;

        $taxs = TaxRulesGroup::getTaxRulesGroups();

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('VAT accounts configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        foreach ($taxs as $t) {
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'class' => 'input fixed-width-sm',
                'label' => '<strong>' . $this->l('Tax rule') . ' ID : ' . $t['id_tax_rules_group'] . ' - ' . $t['name'] . '</strong>',
                'name' => 'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'],
                'lang' => false,
                'desc' => $this->l('Default value if empty:') . ' ' . Configuration::get('DMUEBP_COMPTE_TAX'),
            ];

            $default = Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);
            if (empty($default)) {
                $default = Configuration::get('DMUEBP_COMPTE_TAX');
            }

            // 1 - On va récupérer l'id tax du pays par défaut pour cette règle
            $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
            foreach ($taxes as $tax2) {
                $fields_form['form']['input'][] = [
                    'type' => 'text',
                    'class' => 'input fixed-width-sm ml-5',
                    'label' => $this->l('Tax Account') . ' : ' . $t['name'] . '<br/>' . $tax2['country_name'] . ' ' . $tax2['rate'] . '%',
                    'name' => 'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'],
                    'lang' => false,
                    'desc' => $this->l('Default value if empty:') . ' ' . $default,
                ];
            }
        }

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    /* Configuration compte categories */
    public function getConfigurationForm6()
    {
        if (1 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
            $form_id = 'Configuration6';

            $categories = method_exists('Category', 'getNestedCategories') ?
                Category::getNestedCategories(Category::getRootCategory()->id, $this->context->language->id) :
                self::getNestedCategories(Category::getRootCategory()->id, $this->context->language->id);
            $dmuebp_category_ht = json_decode(Configuration::get('DMUEBP_CATEGORY_HT'), true);
            $dmuebp_category_ttc = json_decode(Configuration::get('DMUEBP_CATEGORY_TTC'), true);

            $url = 'index.php?controller=AdminModules&amp;configure=dmuebpexport
&amp;tab_module=administration&amp;module_name=dmuebpexport&amp;token=';
            $url .= Tools::getAdminTokenLite('AdminModules');

            $array_categories = [];
            $array_categories = $this->getRecursiveFormCategories(
                $array_categories,
                $categories,
                '',
                $dmuebp_category_ht,
                $dmuebp_category_ttc
            );

            $this->context->smarty->assign([
                'url' => $url,
                'form_id' => $form_id,
                'title' => $this->l('Categories accounts configuration'),
                'txt_update' => $this->l('Update'),
                'txt_vat' => Tools::ucfirst($this->l('VAT')),
                'txt_novat' => Tools::ucfirst($this->l('no VAT')),
                'array_categories' => $array_categories,
            ]);
        } else {
            $this->context->smarty->assign([
                'txt_warning' => $this->l('There is 1 warning.'),
                'txt_choose' => $this->l('You must choose advanced filter...'),
            ]);
        }

        $this->context->smarty->assign([
            'dmuebp_compte_priorite' => Configuration::get('DMUEBP_COMPTE_PRIORITE'),
        ]);

        return $this->postProcess() . $this->display(__FILE__, 'views/templates/admin/tab-categories.tpl');
    }

    /* Configuration compte transporteurs */
    public function getConfigurationForm7()
    {
        $form_id = 7;

        // 5 = ALL_CARRIERS (ps_carrier + module carriers)
        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, 5);

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Carriers accounts configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        foreach ($carriers as $c) {
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'class' => 'input fixed-width-sm',
                'label' => $c['name'] . ' (' . $this->l('VAT') . ')',
                'name' => 'DMUEBP_CARRIER_TTC_' . $c['id_carrier'],
                'lang' => false,
                'desc' => $this->l('Default value if empty:') . ' ' . Configuration::get('DMUEBP_COMPTE_TRANSPORT_TTC'),
            ];
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'class' => 'input fixed-width-sm',
                'label' => $c['name'] . ' (' . $this->l('no VAT') . ')',
                'name' => 'DMUEBP_CARRIER_HT_' . $c['id_carrier'],
                'lang' => false,
                'desc' => $this->l('Default value if empty:') . ' ' . Configuration::get('DMUEBP_COMPTE_TRANSPORT_HT'),
            ];
        }

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    /* Configuration d'export */
    public function getConfigurationForm8()
    {
        $form_id = 8;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Export setup'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'form_id'],
                    [
                        'type' => 'select',
                        'class' => 'input fixed-width-xl',
                        'label' => $this->l('Export format'),
                        'name' => 'DMUEBP_EXPORT',
                        'required' => true,
                        'options' => [
                            'query' => [
                                0 => ['name' => $this->l('Accounting export'), 'id_choice' => '0'],
                                1 => ['name' => $this->l('EBP export'), 'id_choice' => '1'], ],
                            'id' => 'id_choice',
                            'name' => 'name',
                        ],
                        'desc' => $this->l('Set export format...'),
                    ],
                    [
                        'type' => 'select',
                        'class' => 'input fixed-width-xl',
                        'label' => $this->l('Export Encoding'),
                        'name' => 'DMUEBP_EXPORT_ENCODING',
                        'required' => true,
                        'options' => [
                            'query' => [
                                0 => ['name' => $this->l('UTF-8'), 'id_encoding' => '0'],
                                1 => ['name' => $this->l('ISO-8859-1'), 'id_encoding' => '1'], ],
                            'id' => 'id_encoding',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'class' => 'input fixed-width-xxl',
                        'label' => $this->l('Priority account number'),
                        'name' => 'DMUEBP_COMPTE_PRIORITE',
                        'required' => true,
                        'options' => [
                            'query' => [
                                0 => ['name' => $this->l('VAT account'), 'id_choice' => '0'],
                                1 => ['name' => $this->l('Main product/category account'), 'id_choice' => '1'], ],
                            'id' => 'id_choice',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'free',
                        'class' => 'input fixed-width-xxl',
                        'label' => '<strong>' . $this->l('VAT account') . '</strong>',
                        'name' => 'null',
                        'desc' => $this->l('Simple Filter 1...') . '<br/>' . $this->l('Simple Filter 2...'),
                    ],
                    [
                        'type' => 'free',
                        'class' => 'input fixed-width-xxl',
                        'label' => '<strong>' . $this->l('Main product/category account') . '</strong>',
                        'name' => 'null',
                        'desc' => $this->l('Advanced Filter 1...') . '<br/>' . $this->l('Advanced Filter 2...'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display each line of payment method for invoices'),
                        'name' => 'DMUEBP_EACHPAYMENT',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'DMUEBP_EACHPAYMENT', 'value' => 1, 'label' => ''],
                            ['id' => 'DMUEBP_EACHPAYMENT', 'value' => 0, 'label' => ''],
                        ],
                        'desc' => $this->l('If you have several payments for an invoice'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Remove accents'),
                        'name' => 'DMUEBP_REMOVEACC',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'DMUEBP_REMOVEACC', 'value' => 1, 'label' => ''],
                            ['id' => 'DMUEBP_REMOVEACC', 'value' => 0, 'label' => ''],
                        ],
                        'desc' => $this->l('Remove all the accented chars'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Remove special chars'),
                        'name' => 'DMUEBP_REMOVESPE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'DMUEBP_REMOVESPE', 'value' => 1, 'label' => ''],
                            ['id' => 'DMUEBP_REMOVESPE', 'value' => 0, 'label' => ''],
                        ],
                        'desc' => $this->l('Remove all specials characters : ()[];#...'),
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('CSV Separator'),
                        'name' => 'DMUEBP_SEPARATOR',
                        'required' => true,
                        'lang' => false,
                        'desc' => $this->l('Use antislash t for a tabulation'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Export invoices and credit notes at 0 euros'),
                        'name' => 'DMUEBP_EXORTZERO',
                        'required' => true,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'DMUEBP_EXORTZERO', 'value' => 1, 'label' => ''],
                            ['id' => 'DMUEBP_EXORTZERO', 'value' => 0, 'label' => ''],
                        ],
                        'desc' => $this->l('By default the export does not contain entries at 0 euros'),
                    ],
                    [
                        'type' => 'select',
                        'class' => 'input fixed-width-xxl',
                        'label' => $this->l('Automatic Balancing'),
                        'name' => 'DMUEBP_AUTOBALANCE',
                        'required' => true,
                        'options' => [
                            'query' => [
                                0 => ['name' => $this->l('No'), 'id_choice' => '0'],
                                1 => ['name' => $this->l('Yes'), 'id_choice' => '1'], ],
                            'id' => 'id_choice',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'class' => 'input fixed-width-sm',
                        'label' => $this->l('Automatic Balancing Limit'),
                        'name' => 'DMUEBP_AUTOBALANCE_LIMIT',
                        'required' => true,
                        'suffix' => '€',
                        'lang' => false,
                        'desc' => $this->l('The limit amount you autorise for automatic balancing,
                            otherwise the accounting entry won\'t be exported.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Update'),
                ],
            ],
        ];

        $helper_form = $this->createHelperForm($form_id);

        return $this->postProcess() . $helper_form->generateForm([$fields_form]);
    }

    public function getRecursiveFormCategories(
        $array_categories,
        $categories,
        $html = '',
        $dmuebp_category_ht = '',
        $dmuebp_category_ttc = ''
    ) {
        foreach ($categories as $cat) {
            $dmuebp_category_ttc_value = ((isset($dmuebp_category_ttc[$cat['id_category']]))
                ? $dmuebp_category_ttc[$cat['id_category']]
                : '');

            $dmuebp_category_ht_value = ((isset($dmuebp_category_ht[$cat['id_category']]))
                ? $dmuebp_category_ht[$cat['id_category']]
                : '');

            $array_categories[] = [
                'cat' => $cat,
                'dmuebp_category_ttc_value' => $dmuebp_category_ttc_value,
                'dmuebp_category_ht_value' => $dmuebp_category_ht_value,
            ];

            if (isset($cat['children']) && count($cat['children']) > 0) {
                $array_categories = $this->getRecursiveFormCategories(
                    $array_categories,
                    $cat['children'],
                    $html,
                    $dmuebp_category_ht,
                    $dmuebp_category_ttc
                );
            }
        }

        return $array_categories;
    }

    public function postProcess()
    {
        // CONFIGURATION FORMAT EXPORT
        if (Tools::isSubmit('Configuration8')) {
            if ('' != trim(Tools::getValue('DMUEBP_EXPORT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_EXPORT', Tools::getValue('DMUEBP_EXPORT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_EXPORT_ENCODING'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_EXPORT_ENCODING', Tools::getValue('DMUEBP_EXPORT_ENCODING'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_PRIORITE'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_PRIORITE', Tools::getValue('DMUEBP_COMPTE_PRIORITE'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_EACHPAYMENT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_EACHPAYMENT', Tools::getValue('DMUEBP_EACHPAYMENT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_REMOVEACC'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_REMOVEACC', Tools::getValue('DMUEBP_REMOVEACC'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_REMOVESPE'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_REMOVESPE', Tools::getValue('DMUEBP_REMOVESPE'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_SEPARATOR'), ' \r\n')) {
                Configuration::updateValue('DMUEBP_SEPARATOR', Tools::getValue('DMUEBP_SEPARATOR'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_EXORTZERO'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_EXORTZERO', Tools::getValue('DMUEBP_EXORTZERO'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_AUTOBALANCE'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_AUTOBALANCE', Tools::getValue('DMUEBP_AUTOBALANCE'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_AUTOBALANCE_LIMIT'), ' \t\r\n')) {
                Configuration::updateValue(
                    'DMUEBP_AUTOBALANCE_LIMIT',
                    str_replace(',', '.', str_replace(' ', '', Tools::getValue('DMUEBP_AUTOBALANCE_LIMIT')))
                );
            }
        }

        // JOURNAL
        if (Tools::isSubmit('Configuration1')) {
            if ('' != trim(Tools::getValue('DMUEBP_JOURNAL'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_JOURNAL', Tools::getValue('DMUEBP_JOURNAL'));
            }
        }

        // COMPTES PAR DEFAUT
        if (Tools::isSubmit('Configuration2')) {
            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_CLIENT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_CLIENT', Tools::getValue('DMUEBP_COMPTE_CLIENT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_PRODUIT_HT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_PRODUIT_HT', Tools::getValue('DMUEBP_COMPTE_PRODUIT_HT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_PRODUIT_TTC'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_PRODUIT_TTC', Tools::getValue('DMUEBP_COMPTE_PRODUIT_TTC'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_TRANSPORT_HT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_TRANSPORT_HT', Tools::getValue('DMUEBP_COMPTE_TRANSPORT_HT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_TRANSPORT_TTC'), ' \t\r\n')) {
                Configuration::updateValue(
                    'DMUEBP_COMPTE_TRANSPORT_TTC',
                    Tools::getValue('DMUEBP_COMPTE_TRANSPORT_TTC')
                );
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_EMBALLAGE_HT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_EMBALLAGE_HT', Tools::getValue('DMUEBP_COMPTE_EMBALLAGE_HT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_EMBALLAGE_TTC'), ' \t\r\n')) {
                Configuration::updateValue(
                    'DMUEBP_COMPTE_EMBALLAGE_TTC',
                    Tools::getValue('DMUEBP_COMPTE_EMBALLAGE_TTC')
                );
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_TAX'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_TAX', Tools::getValue('DMUEBP_COMPTE_TAX'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_RRR_HT'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_RRR_HT', Tools::getValue('DMUEBP_COMPTE_RRR_HT'));
            }

            if ('' != trim(Tools::getValue('DMUEBP_COMPTE_RRR_TTC'), ' \t\r\n')) {
                Configuration::updateValue('DMUEBP_COMPTE_RRR_TTC', Tools::getValue('DMUEBP_COMPTE_RRR_TTC'));
            }
        }

        // COMPTES PRODUITS PAR TAX
        if (Tools::isSubmit('Configuration3')) {
            $taxs = TaxRulesGroup::getTaxRulesGroups();
            foreach ($taxs as $t) {
                Configuration::updateValue(
                    'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'],
                    Tools::getValue('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'])
                );

                $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
                foreach ($taxes as $tax2) {
                    Configuration::updateValue(
                        'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'],
                        Tools::getValue('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'])
                    );
                }
            }
        }

        // COMPTE CLIENT PAR MODE DE PAIEMENT
        if (Tools::isSubmit('Configuration4')) {
            $mode_payment = Tools::getValue('DMUEBP_COMPTE_PAYMENT');
            Configuration::updateValue('DMUEBP_COMPTE_PAYMENT', json_encode($mode_payment));
        }

        // COMPTE TAX
        if (Tools::isSubmit('Configuration5')) {
            $taxs = TaxRulesGroup::getTaxRulesGroups();
            foreach ($taxs as $t) {
                Configuration::updateValue(
                    'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'],
                    Tools::getValue('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'])
                );

                $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
                foreach ($taxes as $tax2) {
                    Configuration::updateValue(
                        'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'],
                        Tools::getValue('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'])
                    );
                }
            }
        }

        // COMPTE PRODUIT PAR CATEGORIE
        if (Tools::isSubmit('Configuration6')) {
            Configuration::updateValue('DMUEBP_CATEGORY_HT', json_encode(Tools::getValue('DMUEBP_CATEGORY_HT')));
            Configuration::updateValue('DMUEBP_CATEGORY_TTC', json_encode(Tools::getValue('DMUEBP_CATEGORY_TTC')));
        }

        // COMPTE TRANSPORTEURS
        if (Tools::isSubmit('Configuration7')) {
            $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, 5);
            foreach ($carriers as $c) {
                Configuration::updateValue(
                    'DMUEBP_CARRIER_HT_' . $c['id_carrier'],
                    Tools::getValue('DMUEBP_CARRIER_HT_' . $c['id_carrier'])
                );
                Configuration::updateValue(
                    'DMUEBP_CARRIER_TTC_' . $c['id_carrier'],
                    Tools::getValue('DMUEBP_CARRIER_TTC_' . $c['id_carrier'])
                );
            }
        }

        if (Tools::isSubmit('Configuration1')
            || Tools::isSubmit('Configuration2')
            || Tools::isSubmit('Configuration3')
            || Tools::isSubmit('Configuration4')
            || Tools::isSubmit('Configuration5')
            || Tools::isSubmit('Configuration6')
            || Tools::isSubmit('Configuration7')
            || Tools::isSubmit('Configuration8')) {
            return $this->displayConfirmation($this->l('Configuration updated'));
        } else {
            return '';
        }
    }

    public function install()
    {
        // Installation du module
        if (!parent::install()) {
            return false;
        }

        Configuration::updateValue('DMUEBP_VERSION', $this->version, false, 0, 0);
        Configuration::updateValue('DMUEBP_EXPORT', '0');
        Configuration::updateValue('DMUEBP_EXPORT_ENCODING', '0');
        Configuration::updateValue('DMUEBP_JOURNAL', 'VE');
        Configuration::updateValue('DMUEBP_COMPTE_CLIENT', '411');
        Configuration::updateValue('DMUEBP_COMPTE_PRIORITE', '1');
        Configuration::updateValue('DMUEBP_COMPTE_PRODUIT_HT', '701');
        Configuration::updateValue('DMUEBP_COMPTE_PRODUIT_TTC', '701');
        Configuration::updateValue('DMUEBP_COMPTE_TRANSPORT_HT', '706');
        Configuration::updateValue('DMUEBP_COMPTE_TRANSPORT_TTC', '706');
        Configuration::updateValue('DMUEBP_COMPTE_EMBALLAGE_HT', '706');
        Configuration::updateValue('DMUEBP_COMPTE_EMBALLAGE_TTC', '706');
        Configuration::updateValue('DMUEBP_COMPTE_TAX', '44571');
        Configuration::updateValue('DMUEBP_COMPTE_RRR_HT', '609');
        Configuration::updateValue('DMUEBP_COMPTE_RRR_TTC', '609');
        Configuration::updateValue('DMUEBP_COMPTE_PAYMENT', '');
        Configuration::updateValue('DMUEBP_CATEGORY_HT', '');
        Configuration::updateValue('DMUEBP_CATEGORY_TTC', '');
        Configuration::updateValue('DMUEBP_AUTOBALANCE', '0');
        Configuration::updateValue('DMUEBP_EACHPAYMENT', '0');
        Configuration::updateValue('DMUEBP_REMOVEACC', '0');
        Configuration::updateValue('DMUEBP_REMOVESPE', '0');
        Configuration::updateValue('DMUEBP_SEPARATOR', ';');
        Configuration::updateValue('DMUEBP_EXORTZERO', '0');
        Configuration::updateValue('DMUEBP_AUTOBALANCE_LIMIT', '0.00');

        $this->installDataBase();

        $id_lang = Language::getIdByIso('en');
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $id_lang_fr = Language::getIdByIso('fr');

        // ** **
        reset($this->menu_parent);
        $menu_parent = current($this->menu_parent);
        foreach ($this->menu_parent as $version => $parent) {
            if ('default' != $version && version_compare(_PS_VERSION_, $version, '>=')) {
                $menu_parent = $parent;
            }
        }
        // ** **
        $id_tab = self::getIdTab($menu_parent);

        // Installation d'un onglet admin
        if (!$this->installModuleTab(
            $this->menu_controller,
            [$id_lang => $this->menu_name, $id_lang_fr => $this->menu_name_fr],
            $id_tab
        )) {
            return false;
        }

        // Installation d'un nouvelle onglet fiche produit (admin)
        if (!$this->registerHook('actionProductUpdate') || !$this->registerHook('displayAdminProductsExtra')) {
            return false;
        }

        // Installation d'un nouvel onglet ficher client (admin)
        if (!$this->registerHook('displayAdminCustomers')) {
            return false;
        }

        // Modification de la table 'product' (ajout de 2 champs)
        if (!DmuEbpExportModel::alterTables('add')) {
            return false;
        }

        // Installation de hooks
        if (!$this->registerHook('displayBackOfficeHeader')) {
            return false;
        }

        return true;
    }

    private function installDataBase()
    {
        $sql = [];
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dmuebp_report` (
                    `id_report` int(8) NOT NULL AUTO_INCREMENT,
					  `type` varchar(16) DEFAULT NULL,
					  `ecriture_originale` mediumtext,
					  `difference` double DEFAULT NULL,
					  `is_avoir` int(1) DEFAULT NULL,
					  `ecriture_corrigee` mediumtext,
					  `num_piece` varchar(32) DEFAULT NULL,
					  `id_order` int(8) DEFAULT NULL,
					  PRIMARY KEY (`id_report`)
                    ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dmuebp_taxrule` (
            `new_tax_rules_group` int(8) NOT NULL,
            `old_tax_rules_group` int(8) NOT NULL,
            PRIMARY KEY (`new_tax_rules_group`,`old_tax_rules_group`)
          ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        // Suppression des variables de configuration
        Configuration::deleteByName('DMUEBP_VERSION');
        Configuration::deleteByName('DMUEBP_EXPORT');
        Configuration::deleteByName('DMUEBP_EXPORT_ENCODING');
        Configuration::deleteByName('DMUEBP_JOURNAL');
        Configuration::deleteByName('DMUEBP_COMPTE_CLIENT');
        Configuration::deleteByName('DMUEBP_COMPTE_PRIORITE');
        Configuration::deleteByName('DMUEBP_COMPTE_PRODUIT_HT');
        Configuration::deleteByName('DMUEBP_COMPTE_PRODUIT_TTC');
        Configuration::deleteByName('DMUEBP_COMPTE_TRANSPORT_HT');
        Configuration::deleteByName('DMUEBP_COMPTE_TRANSPORT_TTC');
        Configuration::deleteByName('DMUEBP_COMPTE_EMBALLAGE_HT');
        Configuration::deleteByName('DMUEBP_COMPTE_EMBALLAGE_TTC');
        Configuration::deleteByName('DMUEBP_COMPTE_TAX');
        Configuration::deleteByName('DMUEBP_COMPTE_RRR_HT');
        Configuration::deleteByName('DMUEBP_COMPTE_RRR_TTC');
        Configuration::deleteByName('DMUEBP_COMPTE_PAYMENT');
        Configuration::deleteByName('DMUEBP_CATEGORY_HT');
        Configuration::deleteByName('DMUEBP_CATEGORY_TTC');
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE "DMUEBP_%"');

        // Désinstallation d'un onglet en admin
        // ** **
        reset($this->menu_parent);
        $menu_parent = current($this->menu_parent);
        foreach ($this->menu_parent as $version => $parent) {
            if ('default' != $version && version_compare(_PS_VERSION_, $version, '>=')) {
                $menu_parent = $parent;
            }
        }
        // ** **
        $id_parent = $this->getIdTab($menu_parent);
        $this->uninstallModuleTab($this->menu_controller, $id_parent);

        // Modification de la table 'product' (suppression de 2 champs)
        DmuEbpExportModel::alterTables('remove');

        // Désinstallation du module
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        // Permet d'externaliser les traitements dans une autre fonction
        $this->postProcess();

        // Récupération du fichier de documentation
        $file_documentation = 'readme_' . $this->context->language->iso_code . '.pdf';
        if (!file_exists(dirname(__FILE__) . '/' . $file_documentation)) {
            $file_documentation = 'readme_en.pdf';
        }

        $this->context->smarty->assign(
            [
                'config_tabs' => $this->config_tabs,
                'version_prestashop' => _PS_VERSION_,
                'version_module' => $this->version,
                'nom_module' => $this->l('Accountant export and EBP'),
                'description_complete' => $this->description_complete,
                'comment_acceder' => $this->comment_acceder,
                'path_module' => '../modules/' . $this->name . '',
                'form_id' => (('' != Tools::getValue('form_id')) ? Tools::getValue('form_id') : ''),
                'path_documentation' => $file_documentation,

                // Textes traductibles à ne pas modifier
                'txt_module_version' => $this->l('Module version'),
                'txt_howto' => $this->l('How to use this module ?'),
                'txt_click_here' => $this->l('Click here to access the menu'),
                'txt_qui' => $this->l('Who are we ?'),
                'txt_dmu' => $this->l('Dream me up...'),
                'txt_notre_site' => $this->l('Our website'),
                'txt_notre' => $this->l('Our'),
                'txt_page' => $this->l('Prestashop Partner dedicated page'),
                'txt_decouvrez' => $this->l('Discover all our modules on our'),
                'txt_addons_page' => $this->l('Prestashop Addons dedicated page'),
                'txt_support' => $this->l('Support and Documentation'),
                'txt_open_doc' => $this->l('Click Here to open the module documentation'),
                'txt_support_only' => $this->l('The support of our modules is done exclusively'),
                'txt_rdv' => $this->l('Visit the module\'s page concerned and use the link "Contact the Developer"'),
                'txt_interm' => $this->l('through Prestashop Addons'),
                'txt_mention' => $this->l('You must mention'),
                'txt_desc_problem' => $this->l('A detailed description of the problem'),
                'txt_version_presta' => $this->l('Your Prestashop Version'),
                'txt_version_module' => $this->l('Your Module Version'),
                'lnk_page_prestashop' => $this->l('http://addons.prestashop.com/en/9_dream-me-up'),
                'txt_follow' => $this->l('Follow us'),
                'txt_follow_our' => $this->l('Follow our'),
                'txt_on' => $this->l('on'),
                'txt_and' => $this->l('and'),
                'txt_know_actu' => $this->l('to know all the news around our Addons'),
                'txt_to_have_details' => $this->l('to have all details on our new Addons versions and for every new launch of Addon'),
                'content_html' => [
                    // Liste des différents formulaires pour chaque onglet
                    'Help' => $this->getUserGuideContent(),
                    'Configuration1' => $this->getConfigurationForm1(),
                    'Configuration2' => $this->getConfigurationForm2(),
                    'Configuration3' => $this->getConfigurationForm3(),
                    'Configuration4' => $this->getConfigurationForm4(),
                    'Configuration5' => $this->getConfigurationForm5(),
                    'Configuration6' => $this->getConfigurationForm6(),
                    'Configuration7' => $this->getConfigurationForm7(),
                    'Configuration8' => $this->getConfigurationForm8(),
                ],
            ]
        );

        return $this->context->smarty->fetch(dirname(__FILE__) . '/views/templates/admin/configure.tpl');
    }

    public function getConfigFieldsValues($form_id = 0)
    {
        $data = [];

        $data['form_id'] = $form_id;

        $data['null'] = '';

        $data['DMUEBP_EXPORT'] = Configuration::get('DMUEBP_EXPORT');
        $data['DMUEBP_EXPORT_ENCODING'] = Configuration::get('DMUEBP_EXPORT_ENCODING');

        $data['DMUEBP_JOURNAL'] = Configuration::get('DMUEBP_JOURNAL');

        $data['DMUEBP_COMPTE_CLIENT'] = Configuration::get('DMUEBP_COMPTE_CLIENT');
        $data['DMUEBP_COMPTE_PRIORITE'] = Configuration::get('DMUEBP_COMPTE_PRIORITE');
        $data['DMUEBP_COMPTE_PRODUIT_HT'] = Configuration::get('DMUEBP_COMPTE_PRODUIT_HT');
        $data['DMUEBP_COMPTE_PRODUIT_TTC'] = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');
        $data['DMUEBP_COMPTE_TRANSPORT_HT'] = Configuration::get('DMUEBP_COMPTE_TRANSPORT_HT');
        $data['DMUEBP_COMPTE_TRANSPORT_TTC'] = Configuration::get('DMUEBP_COMPTE_TRANSPORT_TTC');
        $data['DMUEBP_COMPTE_TAX'] = Configuration::get('DMUEBP_COMPTE_TAX');
        $data['DMUEBP_COMPTE_RRR_HT'] = Configuration::get('DMUEBP_COMPTE_RRR_HT');
        $data['DMUEBP_COMPTE_RRR_TTC'] = Configuration::get('DMUEBP_COMPTE_RRR_TTC');
        $data['DMUEBP_COMPTE_EMBALLAGE_HT'] = Configuration::get('DMUEBP_COMPTE_EMBALLAGE_HT');
        $data['DMUEBP_COMPTE_EMBALLAGE_TTC'] = Configuration::get('DMUEBP_COMPTE_EMBALLAGE_TTC');
        $data['DMUEBP_EACHPAYMENT'] = Configuration::get('DMUEBP_EACHPAYMENT');
        $data['DMUEBP_REMOVEACC'] = Configuration::get('DMUEBP_REMOVEACC');
        $data['DMUEBP_REMOVESPE'] = Configuration::get('DMUEBP_REMOVESPE');
        $data['DMUEBP_SEPARATOR'] = Configuration::get('DMUEBP_SEPARATOR');
        $data['DMUEBP_EXORTZERO'] = Configuration::get('DMUEBP_EXORTZERO');
        $data['DMUEBP_AUTOBALANCE'] = Configuration::get('DMUEBP_AUTOBALANCE');
        $data['DMUEBP_AUTOBALANCE_LIMIT'] = Configuration::get('DMUEBP_AUTOBALANCE_LIMIT');

        $taxs = TaxRulesGroup::getTaxRulesGroups();
        foreach ($taxs as $t) {
            $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
            $data['DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']] =
                Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);

            foreach ($taxes as $tax2) {
                $data['DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country']] =
                    Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country']);
            }

            $data['DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']] =
                Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);

            foreach ($taxes as $tax2) {
                $data['DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country']] =
                    Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country']);
            }
        }

        $dmuebp_compte_payment = json_decode(Configuration::get('DMUEBP_COMPTE_PAYMENT'), true);

        $sql = 'SELECT DISTINCT `payment_method` AS payment FROM ' . _DB_PREFIX_ . 'order_payment';
        $res_payments = Db::getInstance()->ExecuteS($sql);
        foreach ($res_payments as &$rp) {
            $rp['payment'] = DmuEbpExportModel::getPaymentModeSimpleName($rp['payment']);
            $rp['payment'] = str_replace(
                [' ', '[', ']'],
                ['_', '_', '_'],
                Tools::strtoupper(trim(Tools::replaceAccentedChars($rp['payment'])))
            );
        }
        $res_payments = array_map(function ($json) { return json_decode($json, true); }, array_unique(array_map('json_encode', $res_payments)));

        foreach ($res_payments as $rp) {
            if (isset($dmuebp_compte_payment[$rp['payment']])) {
                $data['DMUEBP_COMPTE_PAYMENT[' . $rp['payment'] . ']'] = $dmuebp_compte_payment[$rp['payment']];
            } else {
                $data['DMUEBP_COMPTE_PAYMENT[' . $rp['payment'] . ']'] = '';
            }
        }

        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, 5);
        foreach ($carriers as $c) {
            $data['DMUEBP_CARRIER_HT_' . $c['id_carrier']] = Configuration::get('DMUEBP_CARRIER_HT_' . $c['id_carrier']);
            $data['DMUEBP_CARRIER_TTC_' . $c['id_carrier']] = Configuration::get('DMUEBP_CARRIER_TTC_' . $c['id_carrier']);
        }

        return $data;
    }

    private function installModuleTab($tab_class, $tab_name, $id_tab_parent)
    {
        $tab = new Tab();

        // Supprime l'ancienne tab (fix bug maj module 1.2.x > 2.0.0)
        $this->uninstallModuleTab('admindmuebp', $id_tab_parent);

        $id_lang = Language::getIdByIso('en');
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $langues = Language::getLanguages(false);
        foreach ($langues as $langue) {
            if (!isset($tab_name[$langue['id_lang']])) {
                $tab_name[$langue['id_lang']] = $tab_name[$id_lang];
            }
        }

        $tab->name = $tab_name;
        $tab->class_name = $tab_class;
        $tab->module = $this->name;
        $tab->id_parent = $id_tab_parent;
        $id_tab = $tab->save();
        if (!$id_tab) {
            return false;
        }

        $this->installcleanPositions($tab->id, $id_tab_parent);

        return true;
    }

    private function uninstallModuleTab($tab_class, $id_tab_parent)
    {
        $id_tab = Tab::getIdFromClassName($tab_class);
        if (0 != $id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
            $this->uninstallcleanPositions($id_tab_parent);

            return true;
        }

        return false;
    }

    public function installcleanPositions($id, $id_parent)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT `id_tab`,`position`
        FROM `' . _DB_PREFIX_ . 'tab`
        WHERE `id_parent` = ' . (int) $id_parent . '
        AND `id_tab` != ' . (int) $id . '
        ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'tab`
            SET `position` = ' . ((int) $result[$i]['position'] + 1) . '
            WHERE `id_tab` = ' . (int) $result[$i]['id_tab']);
        }

        return true;
    }

    public function uninstallcleanPositions($id_parent)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT `id_tab`
        FROM `' . _DB_PREFIX_ . 'tab`
        WHERE `id_parent` = ' . (int) $id_parent . '
        ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'tab`
            SET `position` = ' . ($i + 1) . '
            WHERE `id_tab` = ' . (int) $result[$i]['id_tab']);
        }

        return true;
    }

    public function hookActionProductUpdate($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if ((Tools::isSubmit('submitAddproduct')
                || Tools::isSubmit('submitAddproductAndStay'))
                && Validate::isLoadedObject($params['product'])) {
                DmuEbpExportModel::saveProductAccountingInfos(
                    $params['product']->id,
                    Tools::getValue('accounting_no_vat'),
                    Tools::getValue('accounting_vat')
                );
            }
        } else {
            if (Tools::isSubmit('submitAccounting')
                && Validate::isLoadedObject($params['product'])) {
                DmuEbpExportModel::saveProductAccountingInfos(
                    $params['product']->id,
                    Tools::getValue('accounting_no_vat'),
                    Tools::getValue('accounting_vat')
                );
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (1 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $id_product = (int) Tools::getValue('id_product');
            } else {
                $id_product = (int) $params['id_product'];
            }

            $product = new Product($id_product);

            if (Validate::isLoadedObject($product)) {
                // Numéro de compte pour produit HT
                $dmuebp_category_ht = json_decode(Configuration::get('DMUEBP_CATEGORY_HT'), true);
                $default_accounting_no_vat = Configuration::get('DMUEBP_COMPTE_PRODUIT_HT');

                // Priorité au numéro de compte de la catégorie principale
                if (isset($dmuebp_category_ht[$product->id_category_default]) && '' != $dmuebp_category_ht[$product->id_category_default]) {
                    $default_accounting_no_vat = $dmuebp_category_ht[$product->id_category_default];
                }

                // Numéro de compte pour produit TTC
                $dmuebp_category_ttc = json_decode(Configuration::get('DMUEBP_CATEGORY_TTC'), true);
                $default_accounting_vat = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');

                // Priorité au numéro de compte de la catégorie principale
                if ('' != Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $product->id_tax_rules_group)) {
                    $default_accounting_vat =
                        Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $product->id_tax_rules_group);
                }
                if (isset($dmuebp_category_ttc[$product->id_category_default]) && '' != $dmuebp_category_ttc[$product->id_category_default]) {
                    $default_accounting_vat = $dmuebp_category_ttc[$product->id_category_default];
                }

                $data = DmuEbpExportModel::getProductAccountingInfos($id_product);
                $this->context->smarty->assign([
                    'title_form' => $this->l('Accounting export with EBP format'),
                    'desc' => $this->l('Please define accounting infos for this product.'),
                    'accounting_number_no_VAT' => $this->l('Accounting number'),
                    'accounting_number' => $this->l('Accouting number (VAT)'),
                    'default_value_category' => $this->l('Default value if empty:'),
                    'id_product' => $id_product,
                    'accounting_no_vat' => $data[0]['accounting_no_vat'],
                    'accounting_vat' => $data[0]['accounting_vat'],
                    'default_accounting_no_vat' => $default_accounting_no_vat,
                    'default_accounting_vat' => $default_accounting_vat,
                    'ps16_old' => version_compare(_PS_VERSION_, '1.6.0.10', '<'),
                    'cancel' => $this->l('Cancel'),
                    'save' => $this->l('Save'),
                    'save_and_stay' => $this->l('Save and stay'),
                ]);
            } else {
                $this->context->smarty->assign([
                    'one_warning' => $this->l('There is 1 warning.'),
                    'warning_message' => $this->l('You must save...'),
                ]);
            }
        } else {
            $this->context->smarty->assign([
                'one_warning' => $this->l('There is 1 warning.'),
                'warning_message' => $this->l('You must choose...'),
            ]);
        }

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return $this->display(__FILE__, 'views/templates/admin/tab-product.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/tab-product-17.tpl');
        }
    }

    public function hookDisplayAdminCustomers($params)
    {
        if (Tools::getIsset('id_customer') || isset($params['id_customer'])) {
            $id_customer = (isset($params['id_customer']) ? $params['id_customer'] : Tools::getValue('id_customer'));
            $accounting_customers = json_decode(Configuration::get('DMUEBP_COMPTE_CUSTOMERS', null, 0, 0), true);
            $accounting_customer = isset($accounting_customers[$id_customer]) ?
                $accounting_customers[$id_customer] : null;
            $controller_epb = $this->context->link->getAdminLink('AdminDmuEbpExport') . '&id_customer=' . $id_customer;
            $this->context->smarty->assign([
                'default_accounting_customer' => Configuration::get('DMUEBP_COMPTE_CLIENT', null, 0, 0),
                'accounting_customer' => $accounting_customer,
                'controller_ebp' => $controller_epb,
            ]);
            if (version_compare(_PS_VERSION_, '1.7.6', '<')) {
                return $this->display(__FILE__, 'views/templates/admin/tab-customer.tpl');
            } else {
                return $this->display(__FILE__, 'views/templates/admin/tab-customer-17.tpl');
            }
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name || Tools::getValue('controller') == $this->menu_controller) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->_path . 'views/css/dmuebpexport.css';
            $this->context->controller->addCss($this->_path . 'views/css/dmuebpexport.css');
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCss($this->_path . 'views/css/ps15.css');
            }
        }
    }

    public function displayAjaxDmueeSetAccountingCustomer()
    {
        if (Tools::getIsset('id_customer') && Tools::getIsset('accounting_customer')) {
            $accounting_customer = Tools::getValue('accounting_customer');
            $id_customer = Tools::getValue('id_customer');
            $accounting_customers = json_decode(Configuration::get('DMUEBP_COMPTE_CUSTOMERS', null, 0, 0), true);
            if ($accounting_customer) {
                $accounting_customers[$id_customer] = $accounting_customer;
            } else {
                if (isset($accounting_customers[$id_customer])) {
                    unset($accounting_customers[$id_customer]);
                }
            }
            Configuration::updateValue('DMUEBP_COMPTE_CUSTOMERS', json_encode($accounting_customers));
            exit(json_encode([
                'success' => true,
                'accounting_customer' => $accounting_customer,
            ]));
        }
    }

    public static function getNestedCategories(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            exit(Tools::displayError());
        }
        if (!Validate::isBool($active)) {
            exit(Tools::displayError());
        }
        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }
        $cache_id = 'Category::getNestedCategories_' . md5(
            (int) $root_category . (int) $id_lang . (int) $active . (int) $use_shop_restriction .
            (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : '')
        );

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT c.*, cl.*
                FROM `' . _DB_PREFIX_ . 'category` c
                ' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' .
                Shop::addSqlRestrictionOnLang('cl') . '
                ' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ .
                'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
                ' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' .
                (int) $root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
                WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '') . '
                ' . ($active ? ' AND c.`active` = 1' : '') . '
                ' . (isset($groups) && Group::isFeatureActive() ?
                ' AND cg.`id_group` IN (' . implode(',', $groups) . ')' : '') . '
                ' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
                ' . ('' != $sql_sort ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
                ' . ('' == $sql_sort && $use_shop_restriction ? ', category_shop.`position` ASC' : '') . '
                ' . ('' != $sql_limit ? $sql_limit : '')
            );

            $categories = [];
            $buff = [];

            if (!isset($root_category)) {
                $root_category = Category::getRootCategory()->id;
            }
            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $root_category) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        }

        return Cache::retrieve($cache_id);
    }
}
