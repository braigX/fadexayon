<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormCart extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'cart';
        $this->submit_action = 'submit' . Tools::ucfirst($this->menu_active) . 'Form';
        $this->p .= 'CART_';
    }

    public function getFormFields()
    {
        $form = [];

        $carriers = [];
        $carriers_data = Carrier::getCarriers(Context::getContext()->language->id, true, 0, false, null, Carrier::ALL_CARRIERS);

        foreach ($carriers_data as $data) {
            $carriers[] = ['id' => $data['id_carrier'], 'name' => $data['name'] . ($data['delay'] ? ' - ' . $data['delay'] : '')];
        }

        $icon_path = $this->module->getLocalPath() . 'uploads/cart.png';
        $icon_url = ImageManager::thumbnail($icon_path, $this->module->name . '_cart.png', 129, 'png', false, true);
        $icon_size = file_exists($icon_path) ? filesize($icon_path) / 1000 : false;

        $form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Cart Reminder'),
                    'icon' => 'icon-bullhorn',
                ],
                'description' => $this->l('Remember to configure the cron job of "Abandoned Cart Reminder", this is mandatory.'),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activate'),
                        'name' => $this->p . 'REMINDER',
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
                        'hint' => $this->l('Activate cart reminder notification triggered with corresponding cron job.'),
                    ],
                    [
                        'type' => 'rg-group',
                        'label' => $this->l('Abandoned time'),
                        'name' => 'generic-group',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'input' => [
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MIN_TIME',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('from'),
                                'suffix' => $this->l('hours'),
                            ],
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MAX_TIME',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('to'),
                                'suffix' => $this->l('hours'),
                            ],
                        ],
                        'hint' => $this->l('Elapsed time you considered a cart is abandoned.'),
                    ],
                    [
                        'type' => 'rg-group',
                        'label' => $this->l('Required amount'),
                        'name' => 'generic-group',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'input' => [
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MIN_AMOUNT',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('from'),
                                'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                            ],
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MAX_AMOUNT',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('to'),
                                'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                            ],
                        ],
                        'hint' => $this->l('Amount required to consider an abandoned cart as valid.'),
                    ],
                    [
                        'type' => 'rg-group',
                        'label' => $this->l('Required products quantity'),
                        'name' => 'generic-group',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'input' => [
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MIN_QTY',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('from'),
                            ],
                            [
                                'type' => 'text',
                                'name' => $this->p . 'MAX_QTY',
                                'class' => 'fixed-width-sm',
                                'prefix' => $this->l('to'),
                            ],
                        ],
                        'hint' => $this->l('Total products quantity required to consider an abandoned cart as valid.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Time of the previous cart reminder'),
                        'name' => $this->p . 'PREVIOUS',
                        'class' => 'fixed-width-sm',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'suffix' => $this->l('hours'),
                        'hint' => $this->l('Minimun time lapse between the current date and the last cart reminder notification.'),
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Delivery mode'),
                        'name' => $this->p . 'DELIVERY',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'values' => [
                            [
                                'id' => 'delivery_mode_immediately',
                                'value' => 'immediately',
                                'label' => $this->l('Immediately'),
                            ],
                            [
                                'id' => 'delivery_mode_intelligent',
                                'value' => 'intelligent',
                                'label' => $this->l('Intelligent'),
                            ],
                        ],
                        'desc' => $this->l('Immediately') . ': ' . $this->l('Notification is sent right away.') . '<br>'
                            . $this->l('Intelligent') . ': ' . $this->l('Delivers over a 24 hour period at the time each user is most likely to open notifications. Maximizes open rates, but does not deliver right away.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Expiration time if not notified'),
                        'name' => $this->p . 'EXPIRATION',
                        'class' => 'fixed-width-sm',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'suffix' => $this->l('hours'),
                        'hint' => $this->l('Time lapse when notification is considered alive. Leave blank to automatically expire 72 hours later.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => $this->p . 'TITLE',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'lang' => true,
                        'maxchar' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                        'maxlength' => RgPuNoTools::MAX_NOTIFICATION_TITLE_LENGTH,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Message'),
                        'name' => $this->p . 'MSG',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'lang' => true,
                        'maxchar' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                        'maxlength' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Link'),
                        'name' => $this->p . 'URL',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'lang' => true,
                        'hint' => $this->l('Leave blank to lead to checkout page by default.'),
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Icon'),
                        'name' => $this->p . 'ICON',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'display_image' => true,
                        'image' => $icon_url ? $icon_url : false,
                        'size' => $icon_size,
                        'format' => ImageType::getFormattedName('small'),
                        'hint' => $this->l('File will be converted to a 129x129 PNG image. Max allowed file size: 1024 KB.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Create a discount coupon'),
                        'name' => $this->p . 'COUPON',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER',
                        'required' => false,
                        'is_bool' => true,
                        'hint' => $this->l('Create a discount coupon for the customer next buy.'),
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
                        'type' => 'rg-group',
                        'desc' => $this->l('Discount amount') . ': ' . $this->l('Amount to be discounted in cart.'),
                        'name' => 'generic-group',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER child_of_' . $this->p . 'COUPON',
                        'input' => [
                            [
                                'type' => 'text',
                                'name' => $this->p . 'COUPON_DISCOUNT_AMOUNT',
                                'class' => 'fixed-width-sm',
                                'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                            ],
                            [
                                'type' => 'select',
                                'name' => $this->p . 'COUPON_DISCOUNT_TYPE',
                                'options' => [
                                    'query' => [
                                        ['id' => 'amount', 'name' => $this->l('Amount')],
                                        ['id' => 'percent', 'name' => $this->l('Percent')],
                                    ],
                                    'id' => 'id',
                                    'name' => 'name',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'name' => $this->p . 'COUPON_FREE_SHIP',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER child_of_' . $this->p . 'COUPON',
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
                        'desc' => $this->l('Free shipping') . ': ' . $this->l('Generate a free shipping coupon.'),
                    ],
                    [
                        'type' => 'rg-group',
                        'desc' => $this->l('Minimum amount') . ': ' . $this->l('Cart minimum amount to apply coupon.'),
                        'name' => 'generic-group',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER child_of_' . $this->p . 'COUPON',
                        'input' => [
                            [
                                'type' => 'text',
                                'name' => $this->p . 'COUPON_AMOUNT',
                                'class' => 'fixed-width-sm',
                                'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                            ],
                            [
                                'type' => 'select',
                                'name' => $this->p . 'COUPON_W_TAX',
                                'options' => [
                                    'query' => [
                                        ['id' => 1, 'name' => $this->l('Tax included')],
                                        ['id' => 0, 'name' => $this->l('Tax excluded')],
                                    ],
                                    'id' => 'id',
                                    'name' => 'name',
                                ],
                            ],
                            [
                                'type' => 'select',
                                'name' => $this->p . 'COUPON_W_SHIP',
                                'options' => [
                                    'query' => [
                                        ['id' => 1, 'name' => $this->l('Shipping included')],
                                        ['id' => 0, 'name' => $this->l('Shipping excluded')],
                                    ],
                                    'id' => 'id',
                                    'name' => 'name',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => '',
                        'name' => $this->p . 'COUPON_TIME',
                        'class' => 'fixed-width-sm',
                        'form_group_class' => 'child_of_' . $this->p . 'REMINDER child_of_' . $this->p . 'COUPON',
                        'required' => true,
                        'suffix' => $this->l('hours'),
                        'desc' => $this->l('Valid time') . ': ' . $this->l('Coupon validity time.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => [
                    'reset_icon' => [
                        'name' => 'reset_icon',
                        'type' => 'submit',
                        'title' => $this->l('Reset Cart Reminder Icon'),
                        'class' => 'btn btn-default',
                        'icon' => 'process-icon-refresh',
                    ],
                ],
            ],
        ];

        return $form;
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = [
            'generic-group' => false,
            ($name = $this->p . 'REMINDER') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'MIN_TIME') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'MAX_TIME') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'EXPIRATION') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'PREVIOUS') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'MIN_AMOUNT') => abs((float) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'MAX_AMOUNT') => abs((float) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'MIN_QTY') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'MAX_QTY') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'ICON') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'COUPON') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'COUPON_TIME') => abs((int) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'COUPON_AMOUNT') => abs((float) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'COUPON_W_TAX') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'COUPON_W_SHIP') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'COUPON_FREE_SHIP') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'COUPON_DISCOUNT_TYPE') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'COUPON_DISCOUNT_AMOUNT') => abs((float) Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p . 'DELIVERY') => trim(Tools::getValue($name, Configuration::get($name))),
        ];

        $languages = $this->context->language->getLanguages(false);
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_lang = [
            $this->p . 'TITLE',
            $this->p . 'MSG',
            $this->p . 'URL',
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
        $panel = $this->l('Cart Reminder') . ' > ';

        if ($val[$this->p . 'REMINDER'] && $val[$this->p . 'MIN_TIME'] > $val[$this->p . 'MAX_TIME']) {
            return $panel . $this->l('Min abandoned time') . ' ' . $this->l('must be lower than') . ' ' . $this->l('Max abandoned time');
        }

        if ($val[$this->p . 'REMINDER'] && $val[$this->p . 'MIN_AMOUNT'] && $val[$this->p . 'MAX_AMOUNT'] && $val[$this->p . 'MIN_AMOUNT'] > $val[$this->p . 'MAX_AMOUNT']) {
            return $panel . $this->l('Min required amount') . ' ' . $this->l('must be lower than') . ' ' . $this->l('Max required amount');
        }

        if ($val[$this->p . 'REMINDER'] && $val[$this->p . 'MIN_QTY'] && $val[$this->p . 'MAX_QTY'] && $val[$this->p . 'MIN_QTY'] > $val[$this->p . 'MAX_QTY']) {
            return $panel . $this->l('Min required products quantity') . ' ' . $this->l('must be lower than') . ' ' . $this->l('Max required products quantity');
        }

        if (!in_array($val[$this->p . 'DELIVERY'], ['immediately', 'intelligent'])) {
            if ($val[$this->p . 'REMINDER']) {
                return $panel . $this->l('Delivery mode') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
            } else {
                $_POST[$this->p . 'DELIVERY'] = 'intelligent';
            }
        }

        foreach ($languages as $lang) {
            if (!$val[$this->p . 'TITLE'][$lang['id_lang']] || !Validate::isMessage($val[$this->p . 'TITLE'][$lang['id_lang']])) {
                if ($val[$this->p . 'REMINDER']) {
                    return $panel . $this->l('Title') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                } else {
                    $_POST[$this->p . 'TITLE_' . $lang['id_lang']] = '';
                }
            }

            if (!$val[$this->p . 'MSG'][$lang['id_lang']] || !Validate::isMessage($val[$this->p . 'MSG'][$lang['id_lang']])) {
                if ($val[$this->p . 'REMINDER']) {
                    return $panel . $this->l('Message') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
                } else {
                    $_POST[$this->p . 'MSG_' . $lang['id_lang']] = '';
                }
            }

            if ($val[$this->p . 'URL'][$lang['id_lang']] && !Validate::isAbsoluteUrl($val[$this->p . 'URL'][$lang['id_lang']])) {
                if ($val[$this->p . 'REMINDER']) {
                    return $panel . $this->l('Link') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid URL.');
                } else {
                    $_POST[$this->p . 'URL_' . $lang['id_lang']] = '';
                }
            }
        }

        if (!$val[$this->p . 'COUPON_TIME']) {
            if ($val[$this->p . 'REMINDER'] && $val[$this->p . 'COUPON']) {
                return $panel . $this->l('Valid time') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be greater than "0".');
            } else {
                $_POST[$this->p . 'COUPON_TIME'] = 0;
            }
        }

        if (!in_array($val[$this->p . 'COUPON_DISCOUNT_TYPE'], ['amount', 'percent'])) {
            if ($val[$this->p . 'REMINDER'] && $val[$this->p . 'COUPON']) {
                return $panel . $this->l('Discount type') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
            } else {
                $_POST[$this->p . 'COUPON_DISCOUNT_TYPE'] = 'percent';
            }
        }

        return false;
    }

    public function isSubmitForm()
    {
        if (Tools::isSubmit('reset_icon')) {
            return true;
        }

        return parent::isSubmitForm();
    }

    public function processForm()
    {
        if (Tools::isSubmit('reset_icon')) {
            if (Tools::copy($this->module->getLocalPath() . 'views/img/cart.png', $this->module->getLocalPath() . 'uploads/cart.png')) {
                return $this->l('Icon reseted successfully.');
            }
        }

        $error = false;

        if (isset($_FILES[$this->p . 'ICON']['tmp_name']) && !Tools::isEmpty($_FILES[$this->p . 'ICON']['tmp_name'])) {
            if (!($error = ImageManager::validateUpload($_FILES[$this->p . 'ICON'], Tools::getMaxUploadSize(1024 * 1024)))) {
                $temp_path = tempnam(_PS_TMP_IMG_DIR_, $this->module->name);

                if ($temp_path && move_uploaded_file($_FILES[$this->p . 'ICON']['tmp_name'], $temp_path)) {
                    if (ImageManager::checkImageMemoryLimit($temp_path)) {
                        $icon_path = $this->module->getLocalPath() . 'uploads/cart.png';

                        if (ImageManager::resize($temp_path, $icon_path, 129, 129, 'png')) {
                            unlink($temp_path);
                        } else {
                            $error = $this->l('Image cannot be converted. Please try again.');
                        }
                    } else {
                        $error = $this->l('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.');
                    }
                } else {
                    $error = $this->l('An error occurred while uploading the image.');
                }
            }
        }

        if ($error) {
            return $this->l('Error') . ': ' . $error;
        }

        return parent::processForm();
    }
}
