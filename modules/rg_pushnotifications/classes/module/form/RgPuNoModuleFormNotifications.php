<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormNotifications extends RgPuNoModuleForm
{
    private $available_notifications;

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'notifications';
        $this->submit_action = 'submit' . Tools::ucfirst($this->menu_active) . 'Form';

        $this->available_notifications = [
            1003 => $this->l('Tracking number registered'),
            1007 => $this->l('Answer to customer message'),
            1008 => $this->l('Voucher generated'),
        ];

        if (Module::isInstalled('mailalerts') && Module::isEnabled('mailalerts')) {
            $this->available_notifications[1009] = $this->l('Product availability');
        }
    }

    public function getFormFields()
    {
        $form = [];
        $available_notifications = [];

        foreach ($this->available_notifications as $id => $name) {
            $available_notifications[] = ['id_group' => $id, 'name' => $name];
        }

        $form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Welcome Notification'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Send welcome notification'),
                        'name' => $this->p . 'WELCOME_SHOW',
                        'required' => false,
                        'is_bool' => true,
                        'hint' => $this->l('Notification automatically send after the first notification permission is granted.'),
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
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'required' => true,
                        'lang' => true,
                        'maxchar' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                        'maxlength' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                        'form_group_class' => 'child_of_' . $this->p . 'WELCOME_SHOW',
                        'name' => $this->p . 'WELCOME_TITLE',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Message'),
                        'required' => true,
                        'lang' => true,
                        'maxchar' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                        'maxlength' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                        'form_group_class' => 'child_of_' . $this->p . 'WELCOME_SHOW',
                        'name' => $this->p . 'WELCOME_MSG',
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'form_group_class' => 'child_of_' . $this->p . 'WELCOME_SHOW',
                        'name' => $this->p . 'WELCOME_URL',
                        'label' => $this->l('Link'),
                        'hint' => $this->l('Leave blank to avoid opening a new window.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('General'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'rg-group-box',
                        'label' => $this->l('Events notifications'),
                        'name' => $this->p . 'NOTIFICATIONS',
                        'head' => [
                            'id' => $this->l('ID'),
                            'name' => $this->l('Event'),
                        ],
                        'values' => [
                            'query' => $available_notifications,
                            'id' => 'id_group',
                            'name' => 'name',
                        ],
                        'max_height' => 296,
                        'hint' => $this->l('Select which notifications you want to show customers when an event related to them occurs in your shop.'),
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Additional icon'),
                        'hint' => $this->l('Upload your custom icon. After uploaded it will be available for all order state change events.'),
                        'html_content' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/icon_uploader.tpl'),
                        'name' => 'additional_icon',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $icon_files = [];
        $icon_files_data = array_merge(glob($this->module->getLocalPath() . 'views/img/*.png'), glob($this->module->getLocalPath() . 'uploads/*.png'));

        foreach ($icon_files_data as $file) {
            if ($file_key = (int) basename($file)) {
                $icon_files[$file_key] = str_replace($this->module->getLocalPath(), '', $file);
            }
        }

        asort($icon_files, SORT_NATURAL);

        $form_values = $this->getFormValues();

        $order_states = OrderState::getOrderStates((int) $this->context->language->id);
        usort($order_states, function ($a, $b) {
            return $a['id_order_state'] - $b['id_order_state'];
        });

        foreach ($order_states as $state) {
            $this->context->smarty->assign($this->module->name . '_icon_selector', [
                '_path' => $this->module->getPathUri(),
                'icons' => $icon_files,
                'id_order_state' => $state['id_order_state'],
                'name' => $this->p . 'EVENT_ICON_' . $state['id_order_state'],
                'form_group_class' => 'child_of_' . $this->p . 'EVENT_ACTIVE_' . $state['id_order_state'],
                'icon' => $form_values[$this->p . 'EVENT_ICON_' . $state['id_order_state']],
            ]);
            $icon_selector_html = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/icon_selector.tpl');
            $description_html = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/info_tags.tpl');

            $form[] = [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Order state') . ' ' . $state['id_order_state'] . ': "' . $state['name'] . '"',
                        'icon' => 'icon-envelope',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => $this->l('Send notification'),
                            'name' => $this->p . 'EVENT_ACTIVE_' . $state['id_order_state'],
                            'is_bool' => true,
                            'hint' => $this->l('Notify the customer automatically when an order changes to this status.'),
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
                            'type' => 'html',
                            'html_content' => $description_html,
                            'name' => $this->p . 'EVENT_HELP',
                        ],
                        [
                            'type' => 'text',
                            'label' => $this->l('Title'),
                            'required' => true,
                            'lang' => true,
                            'maxchar' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                            'maxlength' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                            'form_group_class' => 'child_of_' . $this->p . 'EVENT_ACTIVE_' . $state['id_order_state'],
                            'name' => $this->p . 'EVENT_TITLE_' . $state['id_order_state'],
                        ],
                        [
                            'type' => 'textarea',
                            'label' => $this->l('Message'),
                            'required' => true,
                            'lang' => true,
                            'maxchar' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                            'maxlength' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                            'form_group_class' => 'child_of_' . $this->p . 'EVENT_ACTIVE_' . $state['id_order_state'],
                            'name' => $this->p . 'EVENT_MSG_' . $state['id_order_state'],
                        ],
                        [
                            'type' => 'html',
                            'label' => $this->l('Icon'),
                            'html_content' => $icon_selector_html,
                            'form_group_class' => 'child_of_' . $this->p . 'EVENT_ACTIVE_' . $state['id_order_state'],
                            'name' => $this->p . 'EVENT_ICON_' . $state['id_order_state'],
                        ],
                    ],
                    'submit' => [
                        'title' => $this->l('Save'),
                    ],
                ],
            ];
        }

        return $form;
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = [
            ($name = $this->p . 'WELCOME_SHOW') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
        ];

        $languages = $this->context->language->getLanguages(false);
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_lang = [
            $this->p . 'WELCOME_TITLE',
            $this->p . 'WELCOME_MSG',
            $this->p . 'WELCOME_URL',
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

        RgPuNoTools::getRgGroupBoxValue(
            $this->isSubmitForm(),
            $name = $this->p . 'NOTIFICATIONS',
            array_keys($this->available_notifications),
            Configuration::get($name),
            $fields_value,
            $for_save
        );

        $order_status = OrderState::getOrderStates((int) $this->context->language->id);

        foreach ($order_status as $status) {
            $fields_value[$this->p . 'EVENT_ACTIVE_' . $status['id_order_state']] = (int) (bool) Tools::getValue($this->p . 'EVENT_ACTIVE_' . $status['id_order_state'], Configuration::get($this->p . 'EVENT_ACTIVE_' . $status['id_order_state']));
            $fields_value[$this->p . 'EVENT_ICON_' . $status['id_order_state']] = (int) Tools::getValue($this->p . 'EVENT_ICON_' . $status['id_order_state'], Configuration::get($this->p . 'EVENT_ICON_' . $status['id_order_state']));
            $fields_lang = [
                $this->p . 'EVENT_TITLE_' . $status['id_order_state'],
                $this->p . 'EVENT_MSG_' . $status['id_order_state'],
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
        }

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues(true);
        $languages = $this->context->language->getLanguages(false);
        $order_states = OrderState::getOrderStates((int) $this->context->language->id);

        foreach ($languages as $lang) {
            $panel = $this->l('Welcome Notification') . ' > ';

            if (!$val[$this->p . 'WELCOME_TITLE'][$lang['id_lang']] ||
                !Validate::isMessage($val[$this->p . 'WELCOME_TITLE'][$lang['id_lang']])
            ) {
                if ($val[$this->p . 'WELCOME_SHOW']) {
                    return $panel . $this->l('Title') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                } else {
                    $_POST[$this->p . 'WELCOME_TITLE_' . $lang['id_lang']] = '';
                }
            }

            if (!$val[$this->p . 'WELCOME_MSG'][$lang['id_lang']] ||
                !Validate::isMessage($val[$this->p . 'WELCOME_MSG'][$lang['id_lang']])
            ) {
                if ($val[$this->p . 'WELCOME_SHOW']) {
                    return $panel . $this->l('Message') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                } else {
                    $_POST[$this->p . 'WELCOME_MSG_' . $lang['id_lang']] = '';
                }
            }

            if ($val[$this->p . 'WELCOME_URL'][$lang['id_lang']] &&
                !Validate::isAbsoluteUrl($val[$this->p . 'WELCOME_URL'][$lang['id_lang']])
            ) {
                if ($val[$this->p . 'WELCOME_SHOW']) {
                    return $panel . $this->l('Link') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid URL.');
                } else {
                    $_POST[$this->p . 'WELCOME_URL_' . $lang['id_lang']] = '';
                }
            }

            foreach ($order_states as $state) {
                $panel = $this->l('Order state') . ' ' . $state['id_order_state'] . ': "' . $state['name'] . '" > ';

                if (!$val[$this->p . 'EVENT_TITLE_' . $state['id_order_state']][$lang['id_lang']] ||
                    !Validate::isCleanHtml($val[$this->p . 'EVENT_TITLE_' . $state['id_order_state']][$lang['id_lang']])
                ) {
                    if ($val[$this->p . 'EVENT_ACTIVE_' . $state['id_order_state']]) {
                        return $panel . $this->l('Title') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                    } else {
                        $_POST[$this->p . 'EVENT_TITLE_' . $state['id_order_state'] . '_' . $lang['id_lang']] = '';
                    }
                }

                if (!$val[$this->p . 'EVENT_MSG_' . $state['id_order_state']][$lang['id_lang']] ||
                    !Validate::isCleanHtml($val[$this->p . 'EVENT_MSG_' . $state['id_order_state']][$lang['id_lang']])
                ) {
                    if ($val[$this->p . 'EVENT_ACTIVE_' . $state['id_order_state']]) {
                        return $panel . $this->l('Message') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                    } else {
                        $_POST[$this->p . 'EVENT_MSG_' . $state['id_order_state'] . '_' . $lang['id_lang']] = '';
                    }
                }
            }
        }

        if ($val[$this->p . 'NOTIFICATIONS'] && !Validate::isArrayWithIds(explode(',', $val[$this->p . 'NOTIFICATIONS']))) {
            return $this->l('General') . ' > ' . $this->l('Notifications') . ' ' . $this->l('is invalid.');
        }

        return false;
    }
}
