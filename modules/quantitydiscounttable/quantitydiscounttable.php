<?php
/**
 * Quantitydiscounttable
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *  @author    FME Modules
 *  @copyright 2023 FMM Modules All right reserved
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 *  @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class QuantityDiscountTable extends Module
{
    protected $id_shop;

    protected $id_shop_group;

    public function __construct()
    {
        $this->name = 'quantitydiscounttable';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->author = 'FMM Modules';
        $this->bootstrap = true;
        $this->module_key = '9bf30beeb128f6ca8c7b91c8569ee2e7';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        parent::__construct();

        $this->displayName = 'Quantity Discount Table';
        $this->description = 'Display quantity discount table on product detail in a more visible place. ';
        $this->ps_versions_compliancy = ['min' => '1.6.0.0', 'max' => _PS_VERSION_];

        if ($this->id_shop === null || !Shop::isFeatureActive()) {
            $this->id_shop = Shop::getContextShopID(true);
        } else {
            $this->id_shop = $this->context->shop->id;
        }

        if ($this->id_shop_group === null || !Shop::isFeatureActive()) {
            $this->id_shop_group = Shop::getContextShopGroupID();
        } else {
            $this->id_shop_group = $this->context->shop->id_shop_group;
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        Configuration::updateValue('QDT_SHOW_PD', 0);
        Configuration::updateValue('QDT_SHOW_HOME', 0);
        Configuration::updateValue('QDT_HOOK_POSITION', 1);
        // new values
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_FONT', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BODY_COLOR', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_FONT', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR', '');
        Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BG_COLOR', '');

        if (parent::install()
            && $this->installTab()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayReassurance')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('displayProductTab')
            && $this->registerHook('displayLeftColumnProduct')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayProductListReviews')) {
            return true;
        }

        return false;
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminQuantityDiscount';
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Quantity Discount');
        $tab->add();

        $tab2 = new Tab();
        $tab2->class_name = 'AdminQuantityDiscount';
        $tab2->id_parent = Tab::getIdFromClassName('AdminQuantityDiscount');
        $tab2->module = $this->name;
        $tab2->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Specific Pricing');
        $tab2->add();

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('QDT_SHOW_PD');
        Configuration::deleteByName('QDT_SHOW_HOME');
        Configuration::deleteByName('QDT_HOOK_POSITION');
        // new values
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_HEADER_FONT');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BODY_COLOR');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BORDER');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_TEXT_FONT');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR');
        Configuration::deleteByName('QUANTITY_DISCOUNT_TABLE_BG_COLOR');

        if (parent::uninstall()
            && $this->uninstallTab()
            && $this->unregisterHook('header')
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('displayProductAdditionalInfo')
            && $this->unregisterHook('displayReassurance')
            && $this->unregisterHook('displayAfterProductThumbs')
            && $this->unregisterHook('displayFooterProduct')
            && $this->unregisterHook('displayProductTab')
            && $this->unregisterHook('displayLeftColumnProduct')
            && $this->unregisterHook('displayProductListReviews')) {
            return true;
        }

        return false;
    }

    public function uninstallTab()
    {
        $id_tab_delivery = (int) Tab::getIdFromClassName('AdminQuantityDiscount');
        $id_tab_delivery2 = (int) Tab::getIdFromClassName('AdminQuantityDiscount');
        if ($id_tab_delivery || $id_tab_delivery2) {
            $tab2 = new Tab($id_tab_delivery);
            $tab3 = new Tab($id_tab_delivery2);
            if ($tab3->delete() && $tab2->delete()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getContent()
    {
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        $this->context->smarty->assign(
            [
                'QDT_SHOW_PD' => Configuration::get('QDT_SHOW_PD'),
                'QDT_SHOW_HOME' => Configuration::get('QDT_SHOW_HOME'),
                'fme_path' => $this->_path,
                'QDT_SELECTED_POS' => Configuration::get('QDT_HOOK_POSITION'),
            ]
        );

        if (Tools::getValue('action') == 'getSearchProducts') {
            $this->getSearchProducts();
            exit;
        }

        return $this->html . $this->postProcess() . $this->displayForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitquantitydiscounttable')) {
            $qdt_show_pd = (int) Tools::getValue('QDT_SHOW_PD');
            $qdt_show_home = (int) Tools::getValue('QDT_SHOW_HOME');
            $qdt_hook_pos = (int) Tools::getValue('QDT_HOOK_POSITION');
            Configuration::updateValue('QDT_SHOW_PD', $qdt_show_pd, false, $this->id_shop_group, $this->id_shop);
            Configuration::updateValue('QDT_SHOW_HOME', $qdt_show_home, false, $this->id_shop_group, $this->id_shop);
            Configuration::updateValue('QDT_HOOK_POSITION', $qdt_hook_pos, false, $this->id_shop_group, $this->id_shop);
            // new values
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_FONT', Tools::getValue('QUANTITY_DISCOUNT_TABLE_HEADER_FONT'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR', Tools::getValue('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BODY_COLOR', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BODY_COLOR'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN', Tools::getValue('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BORDER', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BORDER'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_FONT', Tools::getValue('QUANTITY_DISCOUNT_TABLE_TEXT_FONT'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR', Tools::getValue('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR'));
            Configuration::updateValue('QUANTITY_DISCOUNT_TABLE_BG_COLOR', Tools::getValue('QUANTITY_DISCOUNT_TABLE_BG_COLOR'));

            $this->context->controller->confirmations[] = $this->l('Updated Successfully');
            $this->context->smarty->assign(
                [
                    'QDT_SHOW_PD' => Configuration::get('QDT_SHOW_PD', null, $this->id_shop_group, $this->id_shop),
                    'QDT_SHOW_HOME' => Configuration::get('QDT_SHOW_HOME', null, $this->id_shop_group, $this->id_shop),
                    'QDT_SELECTED_POS' => Configuration::get('QDT_HOOK_POSITION', null, $this->id_shop_group, $this->id_shop),
                ]
            );
        }
    }

    public function displayForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $ver = (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) ? 'seven' : 'six';

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'tabs' => [
                    'configuration' => $this->l('Configuration'),
                    'table_style' => $this->l('Table Style'),
                ],
                'input' => [
                    [
                        'type' => 'quantity_discount',
                        'name' => 'QUANTITY_DISCOUNT_NOTICE',
                        'tab' => 'configuration',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display on product detail page'),
                        'name' => 'QDT_SHOW_PD',
                        'required' => false,
                        'class' => 't',
                        'tab' => 'configuration',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => $ver,
                        'label' => $this->l('Select Position :'),
                        'name' => 'QDT_HOOK_POSITION',
                        'tab' => 'configuration',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display on Home page'),
                        'name' => 'QDT_SHOW_HOME',
                        'required' => false,
                        'class' => 't',
                        'tab' => 'configuration',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    // new Values
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display Table Border'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BORDER',
                        'tab' => 'table_style',
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
                                'label' => $this->l('Disbaled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Border Size'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BORDER_SIZE',
                        'col' => '4',
                        'desc' => $this->l('Enter the border size in pixel without unit (e.g 1).'),
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Border color'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BORDER_COLOR',
                        'desc' => $this->l('Select the color for border of table.'),
                        'size' => 20,
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Striped'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Striped'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Simple'),
                            ],
                        ],
                        'desc' => $this->l('Display even no. of Rows in Default Color'),
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Header Row Text Color'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_HEADER_COLOR',
                        'desc' => $this->l('Select color for header row text.'),
                        'size' => 20,
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Header Row Font Size'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_HEADER_FONT',
                        'col' => '4',
                        'desc' => $this->l('Enter the header row font size in pixel without unit (e.g 14).'),
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Header background color'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BG_COLOR',
                        'desc' => $this->l('Choose Background Color for Header row.'),
                        'size' => 20,
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Align Text'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN',
                        'desc' => $this->l('Set the position of Text in Table.'),
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => 'center',
                                    'name' => 'Center',
                                ],
                                [
                                    'id_option' => 'right',
                                    'name' => 'Right',
                                ],
                                [
                                    'id_option' => 'left',
                                    'name' => 'Left',
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Body Rows Background Color'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_BODY_COLOR',
                        'desc' => $this->l('Select background color for table body rows.'),
                        'size' => 20,
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Body Row Font Size'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_TEXT_FONT',
                        'col' => '4',
                        'desc' => $this->l('Enter the table body rows font size in pixel without unit (e.g 14).'),
                        'tab' => 'table_style',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Body row Font color:'),
                        'name' => 'QUANTITY_DISCOUNT_TABLE_TEXT_COLOR',
                        'desc' => $this->l('Choose font color for table body rows.'),
                        'size' => 20,
                        'tab' => 'table_style',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->submit_action = 'submit' . $this->name;
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function getConfigFieldsValues()
    {
        $fields = [];
        $fields['QDT_SHOW_PD'] = Configuration::get('QDT_SHOW_PD', null, $this->id_shop_group, $this->id_shop);
        $fields['QDT_SHOW_HOME'] = Configuration::get('QDT_SHOW_HOME', null, $this->id_shop_group, $this->id_shop);
        $fields['QDT_HOOK_POSITION'] = Configuration::get('QDT_HOOK_POSITION', null, $this->id_shop_group, $this->id_shop);
        // new values
        $fields['QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED');
        $fields['QUANTITY_DISCOUNT_TABLE_HEADER_FONT'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_FONT');
        $fields['QUANTITY_DISCOUNT_TABLE_HEADER_COLOR'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR');
        $fields['QUANTITY_DISCOUNT_TABLE_BODY_COLOR'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BODY_COLOR');
        $fields['QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN');
        $fields['QUANTITY_DISCOUNT_TABLE_BORDER_SIZE'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE');
        $fields['QUANTITY_DISCOUNT_TABLE_BORDER_COLOR'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR');
        $fields['QUANTITY_DISCOUNT_TABLE_BORDER'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER');
        $fields['QUANTITY_DISCOUNT_TABLE_TEXT_FONT'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_FONT');
        $fields['QUANTITY_DISCOUNT_TABLE_TEXT_COLOR'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR');
        $fields['QUANTITY_DISCOUNT_TABLE_BG_COLOR'] = Configuration::get('QUANTITY_DISCOUNT_TABLE_BG_COLOR');

        return $fields;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->name === Tools::getValue('configure')) {
            $this->context->controller->addCSS($this->_path . 'views/css/quantitydiscounttable.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/quantitydiscounttable-back.js', 'all');
        }
    }

    public function hookDisplayHeader()
    {
        Media::addJsDef([
            'qdt_show' => Configuration::get('QDT_SHOW_PD', null, $this->context->shop->id_shop_group, $this->context->shop->id),
            'qdt_pos' => Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id),
            'qdt_home' => Configuration::get('QDT_SHOW_HOME', null, $this->context->shop->id_shop_group, $this->context->shop->id),
            'qdt_ps_version' => true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? true : false,
        ]);
        $this->context->controller->addCSS($this->_path . 'views/css/quantitydiscounttable.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/quantitydiscounttable.js', 'all');
    }

    protected function getQuantityDiscount()
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->smarty->assign(
                [
                    'QDT_SHOW_PD' => Configuration::get('QDT_SHOW_PD', null, $this->context->shop->id_shop_group, $this->context->shop->id),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED'),
                    'QUANTITY_DISCOUNT_TABLE_HEADER_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_FONT'),
                    'QUANTITY_DISCOUNT_TABLE_HEADER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BODY_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BODY_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_SIZE' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_FONT'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BG_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BG_COLOR'),
                ]
            );

            return $this->display(__FILE__, 'quantitydisctable-17.tpl');
        } else {
            $this->smarty->assign(
                [
                    'QDT_SHOW_PD' => Configuration::get('QDT_SHOW_PD', null, $this->context->shop->id_shop_group, $this->context->shop->id),
                    'display_discount_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE', null, $this->context->shop->id_shop_group, $this->context->shop->id),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED'),
                    'QUANTITY_DISCOUNT_TABLE_HEADER_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_FONT'),
                    'QUANTITY_DISCOUNT_TABLE_HEADER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BODY_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BODY_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_SIZE' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BORDER' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_FONT'),
                    'QUANTITY_DISCOUNT_TABLE_TEXT_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR'),
                    'QUANTITY_DISCOUNT_TABLE_BG_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BG_COLOR'),
                ]
            );

            return $this->display(__FILE__, 'quantitydisctable-16.tpl');
        }
    }

    public function hookDisplayProductAdditionalInfo()
    {
        if (1 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
            return $this->getQuantityDiscount();
        }
    }

    public function hookDisplayReassurance()
    {
        if (2 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
            return $this->getQuantityDiscount();
        }
    }

    public function hookDisplayAfterProductThumbs()
    {
        if (3 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
            return $this->getQuantityDiscount();
        }
    }

    public function hookDisplayFooterProduct()
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            if (4 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
                return $this->getQuantityDiscount();
            }
        }
    }

    public function hookDisplayProductTab()
    {
        if (3 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
            return $this->getQuantityDiscount();
        }
    }

    public function hookDisplayLeftColumnProduct()
    {
        if (1 == Configuration::get('QDT_HOOK_POSITION', null, $this->context->shop->id_shop_group, $this->context->shop->id)) {
            return $this->getQuantityDiscount();
        }
    }

    public function hookDisplayProductListReviews($params)
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $id_product = $params['product']['id'];
        } else {
            $id_product = $params['product']['id_product'];
        }

        $cookie = $this->context->cookie;
        $product = new Product($id_product, true, (int) $cookie->id_lang);

        if (!isset($product->specificPrice['id_product_attribute'])) {
            $id_product_attribute = null;
        } else {
            $id_product_attribute = $product->specificPrice['id_product_attribute'];
        }
        $id_currency = (int) $cookie->id_currency;
        $id_group = (int) Group::getCurrent()->id;
        $id_customer = (isset($this->context->customer) ? (int) $this->context->customer->id : 0);
        $id_country = $id_customer ? (int) Customer::getCurrentCountry($id_customer) : (int) Tools::getCountry();
        $id_shop = Context::getContext()->shop->id;
        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, true, (int) $this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }

        $this->smarty->assign(
            [
                'quantity_discounts' => $quantity_discounts,
                'qdt_product_id' => $id_product,
                'QDT_SHOW_HOME' => Configuration::get('QDT_SHOW_HOME', null, $this->context->shop->id_shop_group, $this->context->shop->id),
                'ps_version' => _PS_VERSION_,
                'display_disc_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE', null, $this->context->shop->id_shop_group, $this->context->shop->id),
                'QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_STRIPED'),
                'QUANTITY_DISCOUNT_TABLE_HEADER_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_FONT'),
                'QUANTITY_DISCOUNT_TABLE_HEADER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_HEADER_COLOR'),
                'QUANTITY_DISCOUNT_TABLE_BODY_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BODY_COLOR'),
                'QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_ALIGN'),
                'QUANTITY_DISCOUNT_TABLE_BORDER_SIZE' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_SIZE'),
                'QUANTITY_DISCOUNT_TABLE_BORDER_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER_COLOR'),
                'QUANTITY_DISCOUNT_TABLE_BORDER' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BORDER'),
                'QUANTITY_DISCOUNT_TABLE_TEXT_FONT' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_FONT'),
                'QUANTITY_DISCOUNT_TABLE_TEXT_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_TEXT_COLOR'),
                'QUANTITY_DISCOUNT_TABLE_BG_COLOR' => Configuration::get('QUANTITY_DISCOUNT_TABLE_BG_COLOR'),
            ]
        );

        return $this->display(__FILE__, 'quantitydisctable-home.tpl');
    }

    protected function getSearchProducts()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            exit(json_encode($this->l('Found Nothing.')));
        }

        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = '
        SELECT p.`id_product`,
        pl.`link_rewrite`,
        p.`reference`,
        pl.`name`,
        image_shop.`id_image` id_image,
        il.`legend`,
        p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' .
        (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' .
        (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' .
        (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ .
            'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination || $excludeIds)) {
            $results = [];
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']) . (
                        !empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''
                    ) . '|' . (int) $item['id_product'];
                } else {
                    $cover = Product::getCover($item['id_product']);
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'] . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''),
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                ($item['id_image']) ? $item['id_image'] : $cover['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    ];
                }
            }

            if (!$forceJson) {
                echo implode("\n", $results);
            } else {
                echo json_encode($results);
            }
        } elseif ($items) {
            // packs
            $results = [];
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = '
                    SELECT pa.`id_product_attribute`,
                    pa.`reference`,
                    ag.`id_attribute_group`,
                    pai.`id_image`,
                    agl.`name` AS group_name,
                    al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_combination` pac ON pac.`id_product_attribute` =
                            pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group_lang` agl ON (ag.`id_attribute_group` =
                                agl.`id_attribute_group` AND agl.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
                            $cover = Product::getCover($item['id_product']);
                            $results[$k['id_product_attribute']]['id'] = $item['id_product'];

                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] =
                                $combination['id_product_attribute'];
                            !empty(
                                $results[$combination['id_product_attribute']]['name']
                            ) ? $results[$combination['id_product_attribute']]['name'] .=
                            ' ' . $combination['group_name'] . '-' .
                            $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] =
                                $item['name'] . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] =
                                !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $context->link->getImageLink(
                                        $item['link_rewrite'],
                                        ($combination['id_image']) ? $combination['id_image'] : $cover['id_image'],
                                        $this->getFormatedName('home')
                                    )
                                );
                            }
                        }
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    $this->getFormatedName('home')
                                )
                            ),
                        ];
                    }
                } else {
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    ];
                }
            }
            echo json_encode(array_values($results));
        } else {
            echo json_encode([]);
        }
    }

    public function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(['_' . $theme_name, $theme_name . '_'], '', $name);
        // check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name, 'products')) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' . $theme_name, 'products')) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' . $name_without_theme_name, 'products')) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }
}
