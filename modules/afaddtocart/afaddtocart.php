<?php
/**
 *  2023 ALGO-FACTORY.COM
 *
 *  NOTICE OF LICENSE
 *
 *  @author        Algo Factory <contact@algo-factory.com>
 *  @copyright     Copyright (c) 2020 Algo Factory
 *  @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *
 *  @version       1.0.0
 *
 *  @website       www.algo-factory.com
 *
 *  You can not resell or redistribute this software.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Afaddtocart extends Module
{
    /**
     * @var array
     */
    protected static $module_hooks;

    protected static $afaddtocartSettings;

    public function __construct()
    {
        $this->name = 'afaddtocart';
        $this->tab = 'front_office_features';
        $this->version = '2.0.5';
        $this->author = 'Algo Factory';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('SmartCart for Prestashop');
        $this->description = $this->l('Display the Prestashop cart in a lateral panel or dropdown and sticky bar to improve the user experience.');
        $this->module_key = '356aa3dbfb54e4b5bd647aa2dd3d3e81';

        self::$module_hooks = [
            'displayHeader',
            'displayAfterBodyOpeningTag',
        ];

        self::$afaddtocartSettings = [
            'PS_BLOCK_CART_AJAX' => true,
            'AFADDTOCART_USE_TOASTERCART' => true,
            'AFADDTOCART_USE_STICKY_ADDTOCART' => true,
            'AFADDTOCART_USE_STICKY_GOCHECKOUT' => true,
            'AFADDTOCART_DISPLAY_ON_CART_UPDATE' => true,
            'AFADDTOCART_DISPLAY_REMAINING_SHIPPING_COST' => true,
            'AFADDTOCART_USE_TINY_TOASTER' => true,
            'AFADDTOCART_OPENTOASTER_SELECTOR' => '.cart-preview a',
            'AFADDTOCART_DISPLAY_DISCOUNT_FORM' => true,
            'AFADDTOCART_DISPLAY_CROSSSELLING' => true,
            'AFADDTOCART_CROSSSELLING_LIMIT' => 8,
            'AFADDTOCART_DISPLAY_CUSTOMIZATION_DETAILS' => true,
            'AFADDTOCART_BACKGROUND_COLOR' => '#ffffff',
            'AFADDTOCART_BORDER_COLOR' => '#cccccc',
            'AFADDTOCART_TEXT_COLOR' => '#000000',
            'AFADDTOCART_LINK_COLOR' => '#24b9d7',
            'AFADDTOCART_BTN_BACKGROUND_COLOR' => '#24b9d7',
            'AFADDTOCART_BTN_TEXT_COLOR' => '#fff',
            'AFADDTOCART_ORDER_URL' => true,
            'AFADDTOCART_DISPLAY_PRODUCTS_COUNT_BTN' => true,
            'AFADDTOCART_OVERLAY_BACKGROUND_COLOR' => '#0000003A',
            'AFADDTOCART_LOADER_BACKGROUND_COLOR' => '#0000003A',
            'AFADDTOCART_SPINNER_COLOR' => '#0000003A',
            'AFADDTOCART_CROSS_SELL_COLOR' => '#000000',
            'AFADDTOCART_CROSS_LINK_COLOR' => '#24b9d7',
            'AFADDTOCART_CROSS_SELL_BACKGROUND_COLOR' => '#ffffff',
            'AFADDTOCART_CROSS_SELL_BORDER_COLOR' => '#cccccc',
            'AFADDTOCART_TOASTERUI_BACKGROUND_COLOR' => '#ffffff',
            'AFADDTOCART_TOASTERUI_BORDER_COLOR' => '#cccccc',
            'AFADDTOCART_TOASTERUI_TEXT_COLOR' => '#000000',
            'AFADDTOCART_TOASTERUI_DURATION' => 5000,
            'AFADDTOCART_GOCHEKOUT_BACKGROUND_COLOR' => '#ffffff',
            'AFADDTOCART_GOCHEKOUT_TEXT_COLOR' => '#000000',
            'AFADDTOCART_GOCHEKOUT_DELAY' => 2000,
            'AFADDTOCART_ADDTOCARTBAR_BACKGROUND_COLOR' => '#ffffff',
            'AFADDTOCART_ADDTOCARTBAR_BTN_BACKGROUND_COLOR' => '#cccccc',
            'AFADDTOCART_ADDTOCARTBAR_BTN_ICON_COLOR' => '#222',
            'AFADDTOCART_ADDTOCARTBAR_TEXT_COLOR' => '#000000',
            'AFADDTOCART_EXTRA_STYLES' => '',
        ];
        $langs = Language::getLanguages();
        foreach ($langs as $lang) {
            self::$afaddtocartSettings['AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang']] = '';
        }
    }

    public function install()
    {
        if (parent::install()) {
            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }

            $langs = Language::getLanguages();
            foreach ($langs as $lang) {
                Configuration::updateValue('AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang'], '', true);
            }

            foreach (self::$afaddtocartSettings as $key => $val) {
                if (!preg_match('/AFADDTOCART_TOASTER_EXTRA_TEXT_/', $key)) {
                    Configuration::updateValue($key, $val);
                }
            }

            foreach (self::$module_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
            $langs = Language::getLanguages();
            foreach ($langs as $lang) {
                Configuration::deleteByName('AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang']);
            }

            foreach (self::$afaddtocartSettings as $key => $val) {
                if (!preg_match('/AFADDTOCART_TOASTER_EXTRA_TEXT_/', $key)) {
                    Configuration::deleteByName($key);
                }
            }
            foreach (self::$module_hooks as $hook) {
                if (!$this->unregisterHook($hook)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getContent()
    {
        $output = '';
        if (((bool) Tools::isSubmit('submitAf_addtocartModule')) == true) {
            $errors = $this->postProcess();

            if (empty($errors)) {
                $output .= $this->displayConfirmation($this->l('The settings have been updated.'));
            } else {
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            }
        }

        $docUrlEn = Context::getContext()->shop->getBaseURL(true) . 'modules/afaddtocart/docs/readme_en.pdf';
        $docUrlFr = Context::getContext()->shop->getBaseURL(true) . 'modules/afaddtocart/docs/readme_fr.pdf';

        $this->context->smarty->assign([
            'docUrlFr' => $docUrlFr,
            'docUrlEn' => $docUrlEn,
        ]);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAf_addtocartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([
            $this->getConfigForm(),
            $this->getToasterConfigForm(),
            $this->getToasterUiConfigForm(),
            $this->getGoCheckoutBarConfigForm(),
            $this->getAddToCartBarConfigForm(),
        ]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Prestashop default add to cart popup'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Prestashop add to cart popup'),
                        'desc' => $this->l('Disabled the default Prestashop add to cart popup (ps_shipping)'),
                        'name' => 'PS_BLOCK_CART_AJAX',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Extra CSS'),
                        'name' => 'AFADDTOCART_EXTRA_STYLES',
                        'cols' => 50,
                        'rows' => 20,
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getToasterConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('SmartCart Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('SmartCart'),
                        'name' => 'AFADDTOCART_USE_TOASTERCART',
                        'desc' => $this->l('Display the current cart with SmartCart'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Open on Cart Update'),
                        'desc' => $this->l('Open SmartCart on Cart Update'),
                        'name' => 'AFADDTOCART_DISPLAY_ON_CART_UPDATE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Go to checkout on submit toasterCart'),
                        'desc' => $this->l('Default behavior of Prestashop go to the cart. Enabled this option if you want redirect directly to checkout.'),
                        'name' => 'AFADDTOCART_ORDER_URL',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display remaining shipping cost'),
                        'desc' => $this->l('Display remaining shipping cost on header toaster Cart. Getting the setting in Shipping / Preferences'),
                        'name' => 'AFADDTOCART_DISPLAY_REMAINING_SHIPPING_COST',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display discount form in SmartCart'),
                        'desc' => $this->l('Display discount form in cart and let customer submit voucher'),
                        'name' => 'AFADDTOCART_DISPLAY_DISCOUNT_FORM',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display Cross Selling in SmartCart'),
                        'name' => 'AFADDTOCART_DISPLAY_CROSSSELLING',
                        'desc' => $this->l('Display cross selling products in lateral panel cart. It only appear if one product was already bought with one product in the current cart by someone else.'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Cross selling product limit'),
                        'desc' => $this->l('Limit of products to display in cross selling'),
                        'is_interger' => true,
                        'name' => 'AFADDTOCART_CROSSSELLING_LIMIT',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display customization details in SmartCart'),
                        'name' => 'AFADDTOCART_DISPLAY_CUSTOMIZATION_DETAILS',
                        'desc' => $this->l('Display product customization details in cart'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Custom CSS Selector to open cart on click'),
                        'desc' => $this->l('In a case your theme is based on a different html structure than the Classic Prestashop theme, you need maybe to adjust it.'),
                        'name' => 'AFADDTOCART_OPENTOASTER_SELECTOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'AFADDTOCART_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Borders color'),
                        'name' => 'AFADDTOCART_BORDER_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'AFADDTOCART_TEXT_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Link color'),
                        'name' => 'AFADDTOCART_LINK_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Button background color'),
                        'name' => 'AFADDTOCART_BTN_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Button text color'),
                        'name' => 'AFADDTOCART_BTN_TEXT_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Products count in float button'),
                        'desc' => $this->l('Display products count in the float button'),
                        'name' => 'AFADDTOCART_DISPLAY_PRODUCTS_COUNT_BTN',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Overlay backgound color'),
                        'desc' => $this->l('Example overlay background color: #0000003A, #000000 is the background color 3A is the opacity.'),
                        'name' => 'AFADDTOCART_OVERLAY_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Loader backgound color'),
                        'desc' => $this->l('Example overlay background color: #0000003A, #000000 is the background color 3A is the opacity.'),
                        'name' => 'AFADDTOCART_LOADER_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Loader spinner color text'),
                        'name' => 'AFADDTOCART_SPINNER_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Cross sells text color'),
                        'name' => 'AFADDTOCART_CROSS_SELL_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Cross sells links color'),
                        'name' => 'AFADDTOCART_CROSS_LINK_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Cross sells Background color'),
                        'name' => 'AFADDTOCART_CROSS_SELL_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Cross sells borders color'),
                        'name' => 'AFADDTOCART_CROSS_SELL_BORDER_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Extra text'),
                        'desc' => $this->l('Text in bottom of toaster cart. HTMl is allowed'),
                        'lang' => true,
                        'name' => 'AFADDTOCART_TOASTER_EXTRA_TEXT',
                        'autoload_rte' => true,
                        'cols' => 50,
                        'rows' => 50,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getToasterUiConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Tiny Toaster UI Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Tiny Toaster'),
                        'name' => 'AFADDTOCART_USE_TINY_TOASTER',
                        'desc' => $this->l('Display tiny toaster while adding to the cart'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'AFADDTOCART_TOASTERUI_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Borders color'),
                        'name' => 'AFADDTOCART_TOASTERUI_BORDER_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'AFADDTOCART_TOASTERUI_TEXT_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Toaster duration'),
                        'desc' => $this->l('Duration in ms before hide it'),
                        'name' => 'AFADDTOCART_TOASTERUI_DURATION',
                        'size' => 10,
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getGoCheckoutBarConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Go checkout bar Settings (Sticky)'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Sticky go checkout bar'),
                        'name' => 'AFADDTOCART_USE_STICKY_GOCHECKOUT',
                        'desc' => $this->l('Display a sticky go to checkout bar over on each page other like : product, cart and checkout pages'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'AFADDTOCART_GOCHEKOUT_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'AFADDTOCART_GOCHEKOUT_TEXT_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Delay'),
                        'desc' => $this->l('Delay in ms before show it'),
                        'name' => 'AFADDTOCART_GOCHEKOUT_DELAY',
                        'size' => 10,
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getAddToCartBarConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Product Add To Cart bar Settings (Sticky)'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Sticky Add To Cart'),
                        'name' => 'AFADDTOCART_USE_STICKY_ADDTOCART',
                        'desc' => $this->l('Display a sticky add to cart bar on scroll in the product page'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'AFADDTOCART_ADDTOCARTBAR_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Button background color'),
                        'name' => 'AFADDTOCART_ADDTOCARTBAR_BTN_BACKGROUND_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Button icon color'),
                        'name' => 'AFADDTOCART_ADDTOCARTBAR_BTN_ICON_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'AFADDTOCART_ADDTOCARTBAR_TEXT_COLOR',
                        'size' => 10,
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        $configs = [];

        $langs = Language::getLanguages();
        foreach ($langs as $lang) {
            $configs['AFADDTOCART_TOASTER_EXTRA_TEXT'][$lang['id_lang']] = Configuration::get('AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang'], $lang['id_lang'], true);
        }

        foreach (self::$afaddtocartSettings as $key => $val) {
            if (!preg_match('/AFADDTOCART_TOASTER_EXTRA_TEXT_/', $key)) {
                $configs[$key] = Configuration::get($key);
            }
        }

        return $configs;
    }

    protected function postProcess()
    {
        $errors = [];

        $form_values = $this->getConfigFormValues();

        $langs = Language::getLanguages();
        foreach ($langs as $lang) {
            Configuration::updateValue('AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang'], Tools::getValue('AFADDTOCART_TOASTER_EXTRA_TEXT_' . $lang['id_lang']), true);
        }

        foreach (array_keys($form_values) as $key) {
            if (!preg_match('/AFADDTOCART_TOASTER_EXTRA_TEXT_/', $key)
                && $key !== 'AFADDTOCART_TOASTERUI_DURATION'
                && $key !== 'AFADDTOCART_GOCHEKOUT_DELAY'
                && $key !== 'AFADDTOCART_CROSSSELLING_LIMIT'
            ) {
                Configuration::updateValue($key, Tools::getValue($key));
            }

            if ($key === 'AFADDTOCART_TOASTERUI_DURATION') {
                if (is_numeric(Tools::getValue($key))) {
                    Configuration::updateValue($key, Tools::getValue($key));
                } else {
                    $errors[] = $this->l('The duration need to be an integer');
                }
            }

            if ($key === 'AFADDTOCART_GOCHEKOUT_DELAY') {
                if (is_numeric(Tools::getValue($key))) {
                    Configuration::updateValue($key, Tools::getValue($key));
                } else {
                    $errors[] = $this->l('The delay need to be an integer');
                }
            }

            if ($key === 'AFADDTOCART_CROSSSELLING_LIMIT') {
                if (is_numeric(Tools::getValue($key))) {
                    Configuration::updateValue($key, Tools::getValue($key));
                } else {
                    $errors[] = $this->l('The cossselling limit need to be an integer');
                }
            }
        }

        return $errors;
    }

    public function hookDisplayHeader()
    {
        if ($this->context->controller->page_name === 'module-ets_onepagecheckout-order') {
            return false;
        }

        $this->context->controller->addJS($this->_path . 'views/js/fr/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');

        $configs = [];
        foreach (self::$afaddtocartSettings as $key => $val) {
            if ($key !== 'PS_BLOCK_CART_AJAX' || !preg_match('/AFADDTOCART_TOASTER_EXTRA_TEXT_/', $key)) {
                $configs[$key] = Configuration::get($key);
            }
        }

        $configs['AFADDTOCART_TOASTER_EXTRA_TEXT'] = Configuration::get('AFADDTOCART_TOASTER_EXTRA_TEXT_' . Context::getContext()->language->id);

        Media::addJsDef([
            'afaddtocart_success' => $this->l('Product added to the cart'),
            'afaddtocart_ps_base_url' => Tools::getCurrentUrlProtocolPrefix() . Tools::getShopDomain() . '/',
            'afaddtocart_btnAddToCartClicked' => $this->l('In progress'),
            'afaddtocart_btnAddToCart' => $this->l('Add to cart'),
            'afaddtocart_btnAddToCartSuccess' => $this->l('Add with success'),
            'afaddtocart_voir_mon_panier' => $this->l('Go to cart'),
            'afaddtocart_panier' => $this->l('Cart'),
            'afaddtocart_input_discount' => $this->l('Discount code'),
            'afaddtocart_submit_discount' => $this->l('OK'),
            'afaddtocart_finalise_order' => $this->l('Go to checkout'),
            'afaddtocart_crossselling_title' => $this->l('You might also like'),
            'afaddtocart_gocheckout_inmycart' => $this->l('in my cart'),
            'afaddtocart_shipping_remaining' => $this->l('left for free shipping'),
            'afaddtocart_crossselling_url' => Context::getContext()->link->getModuleLink('afaddtocart', 'crossselling'),
            'afaddtocart_shipping_url' => Context::getContext()->link->getModuleLink('afaddtocart', 'shipping'),
            'afaddtocart_configuration' => $configs,
        ]);
        if (Configuration::get('AFADDTOCART_EXTRA_STYLES')) {
            $this->context->smarty->assign([
                'styles' => Configuration::get('AFADDTOCART_EXTRA_STYLES'),
            ]);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/styles.tpl');
        }
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        return $this->context->smarty->fetch('module:afaddtocart/views/templates/front/smartcart.tpl');
    }
}
