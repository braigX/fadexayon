<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormBell extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'bell';
        $this->submit_action = 'submit' . Tools::ucfirst($this->menu_active) . 'Form';
        $this->p .= 'BELL_';
    }

    public function getFormFields()
    {
        $id_lang = Context::getContext()->language->id;
        $RGPUNO_BELL_THEME = Configuration::get($this->p . 'THEME');
        $this->context->smarty->assign($this->module->name . '_bell', [
            $this->p . 'THEME' => ($RGPUNO_BELL_THEME == 'custom' ? 'default' : Configuration::get($this->p . 'THEME')),
            $this->p . 'SIZE' => Configuration::get($this->p . 'SIZE'),
            $this->p . 'BACK' => ($RGPUNO_BELL_THEME == 'custom' ? Configuration::get($this->p . 'BACK') : ''),
            $this->p . 'FORE' => ($RGPUNO_BELL_THEME == 'custom' ? Configuration::get($this->p . 'FORE') : ''),
            $this->p . 'SHOW_CREDIT' => (int) Configuration::get($this->p . 'SHOW_CREDIT'),
            $this->p . 'DIAG_FORE' => Configuration::get($this->p . 'DIAG_FORE'),
            $this->p . 'DIAG_BACK' => Configuration::get($this->p . 'DIAG_BACK'),
            $this->p . 'DIAG_BACK_HOVER' => Configuration::get($this->p . 'DIAG_BACK_HOVER'),
            $this->p . 'TIP_STATE_SUB' => Configuration::get($this->p . 'TIP_STATE_SUB', $id_lang),
            $this->p . 'MAIN_SUB' => Configuration::get($this->p . 'MAIN_SUB', $id_lang),
            $this->p . 'MAIN_TITLE' => Configuration::get($this->p . 'MAIN_TITLE', $id_lang),
        ]);
        $html_bell = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/configure-os-bell.tpl');
        $html_dialog = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/configure-os-dialog.tpl');

        $form = [];

        $form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Notification Bell'),
                    'icon' => 'icon-bell',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show notification bell'),
                        'name' => $this->p . 'SHOW',
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
                        'hint' => $this->l('A bell is shown in your shop\'s front allowing users to subscribe or unsubscribe from notifications at any time.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Hide to subscribed users'),
                        'name' => $this->p . 'HIDE_SUBS',
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
                        'hint' => $this->l('Hide the bell to subscribed users. Therefore the bell will only be showed to new or unsubscribed users.'),
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Size'),
                        'name' => $this->p . 'SIZE',
                        'values' => [
                            [
                                'id' => 'bell_size_small',
                                'value' => 'small',
                                'label' => $this->l('Small'),
                            ],
                            [
                                'id' => 'bell_size_medium',
                                'value' => 'medium',
                                'label' => $this->l('Medium'),
                            ],
                            [
                                'id' => 'bell_size_large',
                                'value' => 'large',
                                'label' => $this->l('Large'),
                            ],
                        ],
                        'hint' => $this->l('Size of the bell.') . ' ' . $this->l('NOTE: This size is visible only to unsubscribed users or when the mouse is over it.'),
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Theme'),
                        'name' => $this->p . 'THEME',
                        'values' => [
                            [
                                'id' => 'bell_theme_default',
                                'value' => 'default',
                                'label' => $this->l('Default (red-white)'),
                            ],
                            [
                                'id' => 'bell_theme_inverse',
                                'value' => 'inverse',
                                'label' => $this->l('Inverse (white-red)'),
                            ],
                            [
                                'id' => 'bell_theme_custom',
                                'value' => 'custom',
                                'label' => $this->l('Custom'),
                            ],
                        ],
                        'hint' => $this->l('Theme of the bell. Choose Custom to personalize bell colors.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => $this->p . 'BACK',
                        'form_group_class' => 'child_of_' . $this->p . 'THEME',
                        'hint' => $this->l('Customized bell background color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Foreground color'),
                        'hint' => $this->l('Customized bell foreground color.'),
                        'form_group_class' => 'child_of_' . $this->p . 'THEME',
                        'name' => $this->p . 'FORE',
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Bell preview'),
                        'name' => 'bell_example',
                        'html_content' => $html_bell,
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Position'),
                        'name' => $this->p . 'POSITION',
                        'values' => [
                            [
                                'id' => 'bell_position_bottom-right',
                                'value' => 'bottom-right',
                                'label' => $this->l('Bottom right'),
                            ],
                            [
                                'id' => 'bell_position_bottom-left',
                                'value' => 'bottom-left',
                                'label' => $this->l('Bottom left'),
                            ],
                        ],
                        'hint' => $this->l('Bell position in the shop\'s front.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Offset bottom'),
                        'name' => $this->p . 'OFFSET_BOTOM',
                        'class' => 'fixed-width-sm',
                        'required' => true,
                        'suffix' => 'px',
                        'hint' => $this->l('Bell margin from page bottom.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Offset right'),
                        'name' => $this->p . 'OFFSET_RIGHT',
                        'class' => 'fixed-width-sm',
                        'required' => true,
                        'suffix' => 'px',
                        'hint' => $this->l('Bell margin from page right side. Only for Bottom right position.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Offset left'),
                        'name' => $this->p . 'OFFSET_LEFT',
                        'class' => 'fixed-width-sm',
                        'required' => true,
                        'suffix' => 'px',
                        'hint' => $this->l('Bell margin from page left side. Only for Bottom left position.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show OneSignal credits'),
                        'name' => $this->p . 'SHOW_CREDIT',
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
                        'hint' => $this->l('Shows OneSignal copyright text at the bottom of the dialog. Check Dialog preview for demonstration.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Dialog button background color'),
                        'name' => $this->p . 'DIAG_BACK',
                        'hint' => $this->l('Customized dialog button background color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Dialog button foreground color'),
                        'name' => $this->p . 'DIAG_FORE',
                        'hint' => $this->l('Customized dialog button foreground color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Dialog button hover background color'),
                        'name' => $this->p . 'DIAG_BACK_HOVER',
                        'hint' => $this->l('Customized dialog button hover background color.'),
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Dialog preview'),
                        'name' => 'dialog_example',
                        'html_content' => $html_dialog,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Main dialog title'),
                        'name' => $this->p . 'MAIN_TITLE',
                        'lang' => true,
                        'hint' => $this->l('Text you can read as title at the main dialog.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Main dialog subscribe button text'),
                        'name' => $this->p . 'MAIN_SUB',
                        'lang' => true,
                        'hint' => $this->l('Text of the main dialog subscribe button.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Main dialog unsubscribe button text'),
                        'name' => $this->p . 'MAIN_UNS',
                        'lang' => true,
                        'hint' => $this->l('Text of the main dialog unsubscribe button.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Blocked dialog title'),
                        'name' => $this->p . 'BLOCKED_TITLE',
                        'lang' => true,
                        'hint' => $this->l('Text you can read as title at the main dialog when notifications are blocked.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Blocked dialog message'),
                        'name' => $this->p . 'BLOCKED_MSG',
                        'lang' => true,
                        'hint' => $this->l('Text you can read as message at the main dialog when notifications are blocked.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Message after unsubscribing'),
                        'name' => $this->p . 'ACTION_UNS',
                        'lang' => true,
                        'hint' => $this->l('Message shown after unsubscribe action.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Message after subscribing'),
                        'name' => $this->p . 'ACTION_SUBS',
                        'lang' => true,
                        'hint' => $this->l('Message shown after subscribe action.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Message after resubscribing'),
                        'name' => $this->p . 'ACTION_RESUB',
                        'lang' => true,
                        'hint' => $this->l('Message shown after resubscribe action.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Tip for unsubscribed state'),
                        'name' => $this->p . 'TIP_STATE_UNS',
                        'lang' => true,
                        'hint' => $this->l('Tip text showed when the mouse is over the bell and user is "unsubcribed".'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Tip for subscribed state'),
                        'name' => $this->p . 'TIP_STATE_SUB',
                        'lang' => true,
                        'hint' => $this->l('Tip text showed when the mouse is over the bell and user is "subcribed".'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Tip for blocked state'),
                        'name' => $this->p . 'TIP_STATE_BLO',
                        'lang' => true,
                        'hint' => $this->l('Tip text showed when the mouse is over the bell and user block notifications from this website.'),
                    ],
                    /*array(
                        'type' => 'switch',
                        'label' => $this->l('Prenotify'),
                        'name' => $this->p.'PRENOTIFY',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('Show an icon with 1 unread message for first-time site visitors.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Message prenotify'),
                        'name' => $this->p.'MSG_PRENOTIFY'
                        'lang' => true,
                        'hint' => $this->l('Message of default unread prenotification.'),
                    ),*/
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
            ($name = $this->p . 'SHOW') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'HIDE_SUBS') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'SIZE') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'THEME') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'BACK') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'FORE') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'POSITION') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'OFFSET_BOTOM') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'OFFSET_RIGHT') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'OFFSET_LEFT') => abs((int) Tools::getValue($name, Configuration::get($name))),
            // ($name = $this->p.'PRENOTIFY') => (int)(bool)Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'SHOW_CREDIT') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'DIAG_FORE') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'DIAG_BACK') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'DIAG_BACK_HOVER') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'SHOW_CREDIT') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
        ];

        $languages = $this->context->language->getLanguages(false);
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_lang = [
            $this->p . 'TIP_STATE_UNS',
            $this->p . 'TIP_STATE_SUB',
            $this->p . 'TIP_STATE_BLO',
            // $this->p.'MSG_PRENOTIFY',
            $this->p . 'ACTION_SUBS',
            $this->p . 'ACTION_RESUB',
            $this->p . 'ACTION_UNS',
            $this->p . 'MAIN_TITLE',
            $this->p . 'MAIN_UNS',
            $this->p . 'MAIN_SUB',
            $this->p . 'BLOCKED_TITLE',
            $this->p . 'BLOCKED_MSG',
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

        if (!$for_save) {
            if (!$fields_value[$this->p . 'SIZE']) {
                $fields_value[$this->p . 'SIZE'] = 'medium';
            }

            if (!$fields_value[$this->p . 'THEME']) {
                $fields_value[$this->p . 'THEME'] = 'default';
            }

            if (!$fields_value[$this->p . 'POSITION']) {
                $fields_value[$this->p . 'POSITION'] = 'bottom-right';
            }

            if (!$fields_value[$this->p . 'OFFSET_BOTOM']) {
                $fields_value[$this->p . 'OFFSET_BOTOM'] = '15';
            }

            if (!$fields_value[$this->p . 'OFFSET_RIGHT']) {
                $fields_value[$this->p . 'OFFSET_RIGHT'] = '15';
            }

            if (!$fields_value[$this->p . 'OFFSET_LEFT']) {
                $fields_value[$this->p . 'OFFSET_LEFT'] = '15';
            }
        }

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues(true);
        $languages = $this->context->language->getLanguages(false);
        $panel = $this->l('Notification Bell') . ' > ';

        if (!in_array($val[$this->p . 'SIZE'], ['small', 'medium', 'large'])) {
            return $panel . $this->l('Size') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (!in_array($val[$this->p . 'THEME'], ['default', 'inverse', 'custom'])) {
            return $panel . $this->l('Theme') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if ($val[$this->p . 'BACK'] && !Validate::isColor($val[$this->p . 'BACK'])) {
            return $panel . $this->l('Background color') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid color.');
        }

        if ($val[$this->p . 'FORE'] && !Validate::isColor($val[$this->p . 'FORE'])) {
            return $panel . $this->l('Foreground color') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid color.');
        }

        if ($val[$this->p . 'DIAG_FORE'] && !Validate::isColor($val[$this->p . 'DIAG_FORE'])) {
            return $panel . $this->l('Dialog button foreground color') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid color.');
        }

        if ($val[$this->p . 'DIAG_BACK'] && !Validate::isColor($val[$this->p . 'DIAG_BACK'])) {
            return $panel . $this->l('Dialog button background color') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid color.');
        }

        if ($val[$this->p . 'DIAG_BACK_HOVER'] && !Validate::isColor($val[$this->p . 'DIAG_BACK_HOVER'])) {
            return $panel . $this->l('Dialog button hover background color') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid color.');
        }

        if (!in_array($val[$this->p . 'POSITION'], ['bottom-right', 'bottom-left'])) {
            return $panel . $this->l('Position') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        foreach ($languages as $lang) {
            if ($val[$this->p . 'TIP_STATE_UNS'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'TIP_STATE_UNS'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Tip state unsubscribed') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'TIP_STATE_SUB'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'TIP_STATE_SUB'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Tip state subscribed') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'TIP_STATE_BLO'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'TIP_STATE_BLO'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Tip state blocked') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            /*if ($values[$this->p.'MSG_PRENOTIFY'][$lang['id_lang']] && !Validate::isMessage($values[$this->p.'MSG_PRENOTIFY'][$lang['id_lang']])) {
                return $panel.$this->l('Message prenotify').' '.$this->l('is invalid.').' '.$this->l('Must be a valid string.');
            }*/

            if ($val[$this->p . 'ACTION_UNS'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'ACTION_UNS'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Message action unsubscribed') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'ACTION_SUBS'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'ACTION_SUBS'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Message action subscribed') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'ACTION_RESUB'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'ACTION_RESUB'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Message action resubscribed') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'MAIN_TITLE'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'MAIN_TITLE'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Main dialog title') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'MAIN_SUB'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'MAIN_SUB'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Main dialog button subscribe') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'MAIN_UNS'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'MAIN_UNS'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Main dialog button unsubscribe') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'BLOCKED_TITLE'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'BLOCKED_TITLE'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Blocked dialog title') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }

            if ($val[$this->p . 'BLOCKED_MSG'][$lang['id_lang']] &&
                !Validate::isMessage($val[$this->p . 'BLOCKED_MSG'][$lang['id_lang']])
            ) {
                return $panel . $this->l('Blocked dialog message') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }
        }

        return false;
    }
}
