<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleConnectAdminForms
{
    private $module;
    private $name;
    private $context;

    public function __construct($module)
    {
        $this->module = $module;
        $this->name = $module->name;
        $this->context = Context::getContext();
    }

    public function l($string)
    {
        return $this->module->l($string);
    }

    public function renderSettingsForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Google Connect Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Google Connect'),
                        'name' => 'enableGoogleConnect',
                        'is_bool' => true,
                        'doc' => 'enableGoogleConnect',
                        'values' => [
                            [
                                'id' => 'enableGoogleConnect_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'enableGoogleConnect_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Client ID'),
                        'name' => 'googleClientId',
                        'desc' => $this->l('Video instructions on how to create Google Client ID: ') . 'https://www.youtube.com/watch?v=aBTt-nc-8Hw',
                        'doc' => 'googleClientId',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Clean Database when module uninstalled'),
                        'name' => 'cleanDb',
                        'is_bool' => true,
                        'desc' => $this->l('Not recommended, use this only when you\'re not going to use the module'),
                        'doc' => 'cleanDb',
                        'values' => [
                            [
                                'id' => 'cleanDb_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'cleanDb_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitSettingsForm',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitBlockSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) . '&configure=' . $this->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->name . '&' . $this->name . '=generalSettings';
        $helper->module = $this->module;
        $helper->tpl_vars = [
            'fields_value' => [
                'enableGoogleConnect' => $this->module->enableGoogleConnect,
                'googleClientId' => $this->module->googleClientId,
                'cleanDb' => $this->module->cleanDb,
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function renderUsersList($filters = [], $pageItems = 50, $pageNumber = 1)
    {
        if (!(int) $pageItems) {
            $pageItems = 50;
        }
        if (!(int) $pageNumber) {
            $pageNumber = 1;
        }

        $fields_list = [
            'id_google_account' => [
                'title' => $this->l('Account ID'),
                'type' => 'text',
                'search' => false,
            ],
            'first_name' => [
                'title' => $this->l('First Name'),
                'type' => 'text',
                'search' => true,
            ],
            'last_name' => [
                'title' => $this->l('Last Name'),
                'type' => 'text',
                'search' => true,
            ],
            'email' => [
                'title' => $this->l('Email'),
                'type' => 'text',
                'search' => true,
            ],
            'date_add' => [
                'title' => $this->l('Date Add'),
                'type' => 'text',
                'search' => false,
            ],
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = true;
        $helper->identifier = 'id_user';
        $helper->show_toolbar = false;
        $helper->title = $this->l('Users');
        $helper->table = 'higoogleuser';
        $helper->actions = ['delete'];
        // just to display the list badge and refresh button
        $helper->toolbar_btn['dummy'] = [
            'desc' => '',
        ];
        $helper->module = $this->module;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&' . $this->name . '=users';
        $users = HiGoogleConnectUser::filterUsers($filters, $pageItems, $pageNumber);
        $helper->listTotal = $users['total'];

        return $helper->generateList($users['result'], $fields_list);
    }

    public function renderPositionsList()
    {
        $fields_list = [
            'hook' => [
                'title' => $this->l('Hook'),
                'width' => 140,
                'type' => 'text',
                'search' => false,
            ],
            'status' => [
                'title' => $this->l('Status'),
                'width' => 140,
                'type' => 'text',
                'search' => false,
            ],
            'preview' => [
                'title' => $this->l('Preview'),
                'type' => 'text',
                'search' => false,
            ],
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = true;
        $helper->actions = ['edit'];
        $helper->identifier = 'id_position';
        $helper->show_toolbar = false;
        $helper->title = $this->l('Positions');
        $helper->table = 'higoogleconnect';
        $helper->module = $this->module;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&' . $this->name . '=positions';
        $positions = $this->module->getPositionsList();
        $helper->listTotal = count($positions);

        return $helper->generateList($positions, $fields_list);
    }

    public function reanderPositionForm($id_position)
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Update Position Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_position',
                    ],
                    [
                        'type' => 'buttonPreview',
                        'label' => $this->l('Preview'),
                        'name' => 'buttonPreview',
                        'doc' => 'buttonPreview',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'active',
                        'is_bool' => true,
                        'doc' => 'buttonEnable',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Button Type'),
                        'name' => 'buttonType',
                        'doc' => 'buttonType',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'standart',
                                    'name' => $this->l('Standart'),
                                ],
                                [
                                    'id' => 'icon',
                                    'name' => $this->l('Icon'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Theme'),
                        'name' => 'buttonTheme',
                        'doc' => 'buttonTheme',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'outline',
                                    'name' => $this->l('White'),
                                ],
                                [
                                    'id' => 'filled_blue',
                                    'name' => $this->l('Blue'),
                                ],
                                [
                                    'id' => 'filled_black',
                                    'name' => $this->l('Black'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Button Shape'),
                        'name' => 'buttonShape',
                        'doc' => 'buttonShape',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'rectangular',
                                    'name' => $this->l('Rectangular'),
                                ],
                                [
                                    'id' => 'pill',
                                    'name' => $this->l('Pill'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Button Text'),
                        'name' => 'buttonText',
                        'doc' => 'buttonText',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'signin_with',
                                    'name' => $this->l('Sign in with Google'),
                                ],
                                [
                                    'id' => 'signup_with',
                                    'name' => $this->l('Sign up with Google'),
                                ],
                                [
                                    'id' => 'continue_with',
                                    'name' => $this->l('Continue with Google'),
                                ],
                                [
                                    'id' => 'signin',
                                    'name' => $this->l('Sign in'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Button Size'),
                        'name' => 'buttonSize',
                        'doc' => 'buttonSize',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'large',
                                    'name' => $this->l('Large'),
                                ],
                                [
                                    'id' => 'medium',
                                    'name' => $this->l('Medium'),
                                ],
                                [
                                    'id' => 'small',
                                    'name' => $this->l('Small'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable One Tap prompt'),
                        'name' => 'enableOneTapPrompt',
                        'is_bool' => true,
                        'doc' => 'enableOneTapPrompt',
                        'values' => [
                            [
                                'id' => 'enableOneTapPrompt_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'enableOneTapPrompt_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'savePositionSettings',
                ],
                'buttons' => [
                    [
                        'title' => $this->l('Cancel'),
                        'name' => 'cancelPositionSettings',
                        'type' => 'submit',
                        'icon' => 'process-icon-cancel',
                        'class' => 'btn btn-default pull-left',
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->show_toolbar = false;
        $this->fields_form = [];
        $helper->submit_action = 'submitPositionSettings';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name . '&' . $this->module->name . '=positions';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this->module;
        $helper->table = 'higoogleposition';
        $helper->identifier = 'id_position';
        $helper->id = $id_position;
        $helper->tpl_vars = [
            'fields_value' => $this->module->getPositionSettings($id_position),
        ];

        return $helper->generateForm([$fields_form]);
    }
}
