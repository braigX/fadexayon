<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormSettings extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'settings';
        $this->submit_action = 'submit' . Tools::ucfirst($this->menu_active) . 'Form';
    }

    public function getFormFields()
    {
        $form = [];

        $form[] = [
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('OneSignal App ID'),
                        'name' => $this->p . 'OS_APP_ID',
                        'required' => true,
                        'hint' => $this->l('You must create a new App for Web Push platform at OneSignal admin panel. After doing that you will find your App ID at Keys & IDs tab.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('OneSignal REST API Key'),
                        'name' => $this->p . 'OS_API_KEY',
                        'required' => true,
                        'hint' => $this->l('You must create a new App for Web Push platform at OneSignal admin panel. After doing that you will find your REST API Key at Keys & IDs tab.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('OneSignal Apple Safari Web ID'),
                        'name' => $this->p . 'OS_SAFARI_ID',
                        'required' => true,
                        'hint' => $this->l('Apple Safari needs an additional platform configuration, which you may do at App Settings. After doing that you will get an auto generated Web ID that you must insert in this field.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Debug mode'),
                        'name' => $this->p . 'DEBUG_MODE',
                        'required' => false,
                        'is_bool' => true,
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
                        'hint' => $this->l('This option must be used only by advanced or developer users to trace OneSignal specific errors. DO NOT USE in any other situation.'),
                    ],
                    [
                        'type' => 'rg-group',
                        'label' => $this->l('Delay in permission requests'),
                        'name' => 'generic-group',
                        'input' => [
                            [
                                'type' => 'text',
                                'suffix' => $this->l('pages viewed'),
                                'name' => $this->p . 'REQUEST_DELAY_PAGES_VIEWED',
                                'class' => 'fixed-width-sm',
                            ],
                            [
                                'type' => 'text',
                                'suffix' => $this->l('seconds'),
                                'name' => $this->p . 'REQUEST_DELAY_TIME',
                                'class' => 'fixed-width-sm',
                            ],
                        ],
                        'hint' => $this->l('The customer must wait the configured time before showing the request message to receive notifications. You can use a delay in pages viewed and/or seconds (leave "0" to omit).'),
                        'desc' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/info_permission_requests.tpl'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Permission request message'),
                        'name' => $this->p . 'REQUEST_MSG',
                        'lang' => true,
                        'required' => true,
                        'maxchar' => 90,
                        'maxlength' => 90,
                        'hint' => $this->l('Message showed to ask users to receive notifications.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Accept button text'),
                        'name' => $this->p . 'REQUEST_BTN_ACCEPT',
                        'lang' => true,
                        'required' => true,
                        'maxchar' => 15,
                        'maxlength' => 15,
                        'hint' => $this->l('Text of the button to grant notifications permission.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Cancel button text'),
                        'name' => $this->p . 'REQUEST_BTN_CANCEL',
                        'lang' => true,
                        'required' => true,
                        'maxchar' => 15,
                        'maxlength' => 15,
                        'hint' => $this->l('Text of the button to decline notifications permission.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Persistent notification'),
                        'name' => $this->p . 'PERSISTENT_NOTIF',
                        'required' => false,
                        'is_bool' => true,
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
                        'hint' => $this->l('By activating this option, all notifications that are sent will remain fixed until the user makes some action on it. NOTE: You can see more details about its compatibility in the documentation.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show a popup if notifications are allowed'),
                        'name' => $this->p . 'POPUP_ALLOWED_SHOW',
                        'required' => false,
                        'is_bool' => true,
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
                        'hint' => $this->l('A popup will be shown when notification permission change to "granted".'),
                    ],
                    [
                        'type' => 'textarea',
                        'label' => '',
                        'name' => $this->p . 'POPUP_ALLOWED_MSG',
                        'form_group_class' => 'child_of_' . $this->p . 'POPUP_ALLOWED_SHOW',
                        'required' => true,
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('HTML to display in the allowed notifications popup.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show a popup if notifications are declined'),
                        'name' => $this->p . 'POPUP_DECLINED_SHOW',
                        'required' => false,
                        'is_bool' => true,
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
                        'hint' => $this->l('A popup will be shown when notification permission change to "declined".'),
                    ],
                    [
                        'type' => 'textarea',
                        'label' => '',
                        'name' => $this->p . 'POPUP_DECLINED_MSG',
                        'form_group_class' => 'child_of_' . $this->p . 'POPUP_DECLINED_SHOW',
                        'required' => true,
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('HTML to display in the declined notifications popup.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        return $form;
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = [
            ($name = $this->p . 'OS_APP_ID') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'OS_API_KEY') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'OS_SAFARI_ID') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'REQUEST_DELAY_PAGES_VIEWED') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'REQUEST_DELAY_TIME') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'DEBUG_MODE') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'POPUP_ALLOWED_SHOW') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'POPUP_DECLINED_SHOW') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'PERSISTENT_NOTIF') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
        ];

        $languages = $this->context->language->getLanguages(false);
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_lang = [
            $this->p . 'REQUEST_MSG',
            $this->p . 'POPUP_ALLOWED_MSG',
            $this->p . 'POPUP_DECLINED_MSG',
            $this->p . 'REQUEST_BTN_ACCEPT',
            $this->p . 'REQUEST_BTN_CANCEL',
        ];

        foreach ($fields_lang as $key) {
            foreach ($languages as $lang) {
                if (Tools::isSubmit($key . '_' . (int) $lang['id_lang'])) {
                    if (Tools::getValue($key . '_' . (int) $lang['id_lang'])) {
                        $fields_value[$key][(int) $lang['id_lang']] = trim(Tools::getValue($key . '_' . (int) $lang['id_lang']));
                    } else {
                        $fields_value[$key][(int) $lang['id_lang']] = trim(Tools::getValue($key . '_' . (int) $default_lang));
                    }
                } else {
                    $fields_value[$key][(int) $lang['id_lang']] = trim(Configuration::get($key, (int) $lang['id_lang']));
                }
            }
        }

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues(true);
        $languages = $this->context->language->getLanguages(false);
        $panel = $this->l('Settings') . ' > ';

        if (!$val[$this->p . 'OS_APP_ID'] || !Validate::isString($val[$this->p . 'OS_APP_ID'])) {
            return $panel . $this->l('OneSignal App ID') . ' ' . $this->l('is invalid.');
        }

        if (!$val[$this->p . 'OS_API_KEY'] || !Validate::isString($val[$this->p . 'OS_API_KEY'])) {
            return $panel . $this->l('OneSignal REST API Key') . ' ' . $this->l('is invalid.');
        }

        if (!$val[$this->p . 'OS_SAFARI_ID'] || !Validate::isString($val[$this->p . 'OS_SAFARI_ID'])) {
            return $panel . $this->l('OneSignal Apple Safari Web ID') . ' ' . $this->l('is invalid.');
        }

        if (!RgPuNoTools::testOneSignalCredentials($val[$this->p . 'OS_APP_ID'], $val[$this->p . 'OS_API_KEY'])) {
            return $panel . $this->l('OneSignal credentials are incorrect. Please, check it and configure it again.');
        }

        foreach ($languages as $lang) {
            if (!$val[$this->p . 'REQUEST_MSG'][$lang['id_lang']] ||
                !Validate::isMessage($val[$this->p . 'REQUEST_MSG'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Permission request message') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if (!$val[$this->p . 'REQUEST_BTN_ACCEPT'][$lang['id_lang']] ||
                !Validate::isMessage($val[$this->p . 'REQUEST_BTN_ACCEPT'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Accept button text') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if (!$val[$this->p . 'REQUEST_BTN_CANCEL'][$lang['id_lang']] ||
                !Validate::isMessage($val[$this->p . 'REQUEST_BTN_CANCEL'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Cancel button text') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if (!$val[$this->p . 'POPUP_ALLOWED_MSG'][$lang['id_lang']] ||
                !Validate::isCleanHtml($val[$this->p . 'POPUP_ALLOWED_MSG'][$lang['id_lang']], true)
            ) {
                if ($val[$this->p . 'POPUP_ALLOWED_SHOW']) {
                    return $panel . $this->l('Allowed notification mesage') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid HTML code.');
                } else {
                    $_POST[$this->p . 'POPUP_ALLOWED_MSG_' . $lang['id_lang']] = '';
                }
            }

            if (!$val[$this->p . 'POPUP_DECLINED_MSG'][$lang['id_lang']] ||
                !Validate::isCleanHtml($val[$this->p . 'POPUP_DECLINED_MSG'][$lang['id_lang']], true)
            ) {
                if ($val[$this->p . 'POPUP_DECLINED_SHOW']) {
                    return $panel . $this->l('Declined notification mesage') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid HTML code.');
                } else {
                    $_POST[$this->p . 'POPUP_DECLINED_MSG_' . $lang['id_lang']] = '';
                }
            }
        }

        return false;
    }

    public function processForm()
    {
        $val = $this->getFormValues(true);

        if ($val[$this->p . 'OS_APP_ID'] != RgPuNoConfig::get('OS_APP_ID') ||
            $val[$this->p . 'OS_API_KEY'] != RgPuNoConfig::get('OS_API_KEY') ||
            $val[$this->p . 'OS_SAFARI_ID'] != RgPuNoConfig::get('OS_SAFARI_ID')) {
            RgPuNoConfig::delete('CONNECTED');
        }

        $html_fields = [$this->p . 'POPUP_ALLOWED_MSG', $this->p . 'POPUP_DECLINED_MSG'];

        foreach ($val as $k => $v) {
            Configuration::updateValue($k, $v, in_array($k, $html_fields));
        }

        return $this->module->l('Configuration updated successfully.');
    }
}
