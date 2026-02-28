<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/WkSampleProductMap.php';
require_once 'classes/WkSampleCart.php';
require_once 'classes/WkSampleDb.php';
class WkSampleProduct extends Module
{
    public $html = '';
    public $secure_key = '';
    public $count = 0;
    public const WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS = 32;

    public function __construct()
    {
        $this->name = 'wksampleproduct';
        $this->tab = 'front_office_features';
        $this->version = '5.3.3';
        $this->module_key = '28cce2b6b29dea6d801b05f233e36f59';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->secure_key = Tools::hash($this->name);
        parent::__construct();
        $this->displayName = $this->l('Sample product');
        $this->description = $this->l('Allow customer to buy sample product');
        $this->confirmUninstall = $this->l('Are you sure?');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit' . $this->name)) {
            $this->postValidation();
            if (!count($this->context->controller->errors)) {
                $this->postProcess();
            }
        }
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/wksampleproductconfig.js');
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/sampletabs.css');
        $this->html .= $this->getConfigTabTemplate();
        $this->html .= $this->renderForm();
        Media::addJsDef([
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => 42160,
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/docs/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());

        return $this->html;
    }

    public function getConfigTabTemplate($class = 'AdminModules')
    {
        $configTabs = [
            [
                'name' => $this->l('General'),
                'current' => ($class == 'AdminModules'),
                'icon_class' => 'icon-cog',
                'link' => $this->context->link->getAdminLink(
                    'AdminModules',
                    true,
                    ['configure' => 'wksampleproduct'],
                    ['configure' => 'wksampleproduct']
                ),
            ],
            [
                'name' => $this->l('Bulk assign'),
                'current' => ($class == 'AdminWkBulkSample'),
                'icon_class' => 'icon-tasks',
                'link' => $this->context->link->getAdminLink('AdminWkBulkSample'),
            ],
        ];
        $this->context->smarty->assign(['wkConfigTabs' => $configTabs]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/config_nav.tpl');
    }

    /**
     * Validate post data
     */
    protected function postValidation()
    {
        if (Tools::isSubmit('submitGlobalSample')) {
            $maxSample = Tools::getValue('WK_GLOBAL_SAMPLE');
            if (Tools::getValue('WK_GLOBAL_SAMPLE')) {
                $priceType = trim(Tools::getValue('WK_GLOBAL_SAMPLE_PRICE_TYPE'));
                $sampleAmount = trim(Tools::getValue('WK_GLOBAL_SAMPLE_AMOUNT'));
                $samplePrice = trim(Tools::getValue('WK_GLOBAL_SAMPLE_PRICE'));
                $samplePercent = trim(Tools::getValue('WK_GLOBAL_SAMPLE_PERCENT'));
                $maxInCart = trim(Tools::getValue('WK_GLOBAL_SAMPLE_IN_CART'));
                $sampleWeight = trim(Tools::getValue('WK_GLOBAL_SAMPLE_WEIGHT'));
                if ($maxInCart && !Validate::isUnsignedInt($maxInCart)) {
                    $this->context->controller->errors[] =
                    $this->l('Maximum global sample quantity in one cart should be a number.');
                }
                if ($priceType == 2) {
                    if (!$sampleAmount || !Tools::strlen($sampleAmount) || ((float) $sampleAmount == 0)) {
                        $this->context->controller->errors[] =
                        $this->l('Please enter global sample deduction amount.');
                    } elseif (!Validate::isUnsignedFloat($sampleAmount)) {
                        $this->context->controller->errors[] =
                        $this->l('Global sample deduction amount should be a number.');
                    }
                } elseif ($priceType == 3) {
                    if (!$samplePercent || !Tools::strlen($samplePercent) || ((float) $samplePercent == 0)) {
                        $this->context->controller->errors[] =
                        $this->l('Please enter global sample deduction percent.');
                    } elseif (!Validate::isUnsignedFloat($samplePercent)) {
                        $this->context->controller->errors[] =
                        $this->l('Global sample deduction percent should be a number.');
                    } elseif (!Validate::isPercentage($samplePercent)) {
                        $this->context->controller->errors[] =
                        $this->l('Global sample deduction percent should be in between 0-100.');
                    }
                } elseif ($priceType == 4) {
                    if ($samplePrice == '') {
                        $this->context->controller->errors[] =
                        $this->l('Please enter global sample custom price.');
                    } elseif (!Validate::isUnsignedFloat($samplePrice)) {
                        $this->context->controller->errors[] =
                        $this->l('Global sample custom price should be a number.');
                    }
                }
                if (($sampleWeight && !Validate::isFloat($sampleWeight)) || ((float) $sampleWeight < 0)) {
                    $this->context->controller->errors[] =
                    $this->l('Sample weight should be a valid number.');
                }
                $allLanguages = Language::getLanguages();
                foreach ($allLanguages as $lang) {
                    $sampleTitle = Tools::getValue('WK_GLOBAL_SAMPLE_BUTTON_LABEL_' . $lang['id_lang']);
                    if (Tools::strlen(trim($sampleTitle)) > WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS) {
                        $this->context->controller->errors[] =
                        $this->l('Please enter global sample button title upto ') . WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS . $this->l(' characters.') .
                        ' : (' . $lang['iso_code'] . ')';
                    }
                    if (Tools::strlen(trim($sampleTitle))
                        && !Validate::isGenericName($sampleTitle)
                    ) {
                        $this->context->controller->errors[] =
                        $this->l('Please enter valid global sample button title.') .
                        ' : (' . $lang['iso_code'] . ')';
                    }
                    $sampleDesc = Tools::getValue('WK_GLOBAL_SAMPLE_DESC_' . $lang['id_lang']);

                    $objProductMap = new WkSampleProductMap();
                }
            }
        } else {
            $maxSample = Tools::getValue('WK_MAX_SAMPLE_IN_CART');
            if ($maxSample && !Validate::isUnsignedInt($maxSample)) {
                $this->context->controller->errors[] =
                $this->l('Maximum sample product in one cart should be a number.');
            }
            $sampleBtnBgColor = Tools::getValue('WK_SAMPLE_BUTTON_BG_COLOR');
            if (!$sampleBtnBgColor || !Tools::strlen(trim($sampleBtnBgColor))) {
                $this->context->controller->errors[] = $this->l('Please select sample button background color.');
            } elseif (!$this->isValidHexaCode($sampleBtnBgColor)) {
                $this->context->controller->errors[] = $this->l('Please enter correct sample button background color.');
            } elseif (!Validate::isColor($sampleBtnBgColor)) {
                $this->context->controller->errors[] = $this->l('Please enter correct sample button background color.');
            }
            $sampleBtnTextColor = Tools::getValue('WK_SAMPLE_BUTTON_TEXT_COLOR');
            if (!$sampleBtnTextColor || !Tools::strlen(trim($sampleBtnTextColor))) {
                $this->context->controller->errors[] = $this->l('Please select sample button title color.');
            } elseif (!$this->isValidHexaCode($sampleBtnTextColor)) {
                $this->context->controller->errors[] = $this->l('Please enter correct sample button title color.');
            } elseif (!Validate::isColor($sampleBtnTextColor)) {
                $this->context->controller->errors[] = $this->l('Please enter correct sample button title color.');
            }
        }
    }

    /**
     * Validate Hex color code
     *
     * @param string $color
     *
     * @return bool
     */
    public function isValidHexaCode($color)
    {
        return preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color);
    }

    /**
     * Save form data
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submitGlobalSample')) {
            Configuration::updateValue('WK_GLOBAL_SAMPLE', trim(Tools::getValue('WK_GLOBAL_SAMPLE')));
            if (Tools::getValue('WK_GLOBAL_SAMPLE')) {
                Configuration::updateValue(
                    'WK_GLOBAL_SAMPLE_IN_CART',
                    (int) trim(Tools::getValue('WK_GLOBAL_SAMPLE_IN_CART'))
                );
                Configuration::updateValue(
                    'WK_GLOBAL_SAMPLE_PRICE_TYPE',
                    trim(Tools::getValue('WK_GLOBAL_SAMPLE_PRICE_TYPE'))
                );
                Configuration::updateValue(
                    'WK_GLOBAL_SAMPLE_WEIGHT',
                    trim(Tools::getValue('WK_GLOBAL_SAMPLE_WEIGHT'))
                );

                Configuration::updateValue('WK_GLOBAL_SAMPLE_AMOUNT', trim(Tools::getValue('WK_GLOBAL_SAMPLE_AMOUNT')));
                Configuration::updateValue('WK_GLOBAL_SAMPLE_PRICE', trim(Tools::getValue('WK_GLOBAL_SAMPLE_PRICE')));
                Configuration::updateValue('WK_GLOBAL_SAMPLE_TAX', trim(Tools::getValue('WK_GLOBAL_SAMPLE_TAX')));
                Configuration::updateValue('WK_GLOBAL_SAMPLE_PERCENT', trim(Tools::getValue('WK_GLOBAL_SAMPLE_PERCENT')));
                $allLanguages = Language::getLanguages();
                $sampleTitles = [];
                $sampleDescs = [];
                foreach ($allLanguages as $lang) {
                    $sampleTitle = trim(Tools::getValue('WK_GLOBAL_SAMPLE_BUTTON_LABEL_' . $lang['id_lang']));
                    $sampleDesc = trim(Tools::getValue('WK_GLOBAL_SAMPLE_DESC_' . $lang['id_lang']));
                    $sampleTitles[$lang['id_lang']] = Tools::strlen($sampleTitle) ? $sampleTitle : '';
                    $sampleDescs[$lang['id_lang']] = Tools::strlen($sampleDesc) ? $sampleDesc : '';
                }
                Configuration::updateValue('WK_GLOBAL_SAMPLE_BUTTON_LABEL', $sampleTitles);
                Configuration::updateValue('WK_GLOBAL_SAMPLE_DESC', $sampleDescs, true);
                $allCarriers = Carrier::getCarriers(
                    $this->context->language->id,
                    true,
                    false,
                    false,
                    null,
                    Carrier::ALL_CARRIERS
                );
                $idReferences = array_column($allCarriers, 'id_reference');
                $selectedCarriers = [];
                foreach ($idReferences as $idReference) {
                    $state = Tools::getValue('WK_GLOBAL_SAMPLE_CARRIERS_' . $idReference);
                    if ($state && $state == 'on') {
                        $selectedCarriers[] = (int) $idReference;
                    }
                }
                Configuration::updateValue('WK_GLOBAL_SAMPLE_CARRIERS', json_encode($selectedCarriers));
            }
        } else {
            Configuration::updateValue('WK_MAX_SAMPLE_IN_CART', trim(Tools::getValue('WK_MAX_SAMPLE_IN_CART')));
            Configuration::updateValue('WK_SAMPLE_STOCK_UPDATE', trim(Tools::getValue('WK_SAMPLE_STOCK_UPDATE')));
            Configuration::updateValue('WK_SAMPLE_LOGGED_ONLY', trim(Tools::getValue('WK_SAMPLE_LOGGED_ONLY')));
            Configuration::updateValue('WK_SAMPLE_QUANTITY_SPIN', trim(Tools::getValue('WK_SAMPLE_QUANTITY_SPIN')));
            Configuration::updateValue('WK_SAMPLE_BUTTON_TEXT_COLOR', trim(Tools::getValue('WK_SAMPLE_BUTTON_TEXT_COLOR')));
            Configuration::updateValue('WK_SAMPLE_BUTTON_BG_COLOR', trim(Tools::getValue('WK_SAMPLE_BUTTON_BG_COLOR')));
        }
        $this->context->controller->confirmations[] = $this->l('Successfully saved.');
    }

    public function renderForm()
    {
        $fieldsForm = [];
        $fieldsForm['form']['form'] = [
            'id_form' => 'wksample_configform',
            'legend' => [
                'title' => $this->l('General'),
                'icon' => 'icon-cog',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Maximum sample product in one cart'),
                    'name' => 'WK_MAX_SAMPLE_IN_CART',
                    'col' => '2',
                    'desc' => $this->l('Leave empty or fill zero if no limitation.'),
                    'hint' => $this->l('This is maximum allowed number of different products in one cart.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Stock update on sample product order'),
                    'name' => 'WK_SAMPLE_STOCK_UPDATE',
                    'hint' => $this->l('If disabled, the stock will be unchanged on ordering sample products.'),
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
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Only logged in customer can order'),
                    'name' => 'WK_SAMPLE_LOGGED_ONLY',
                    'desc' => $this->l('If no, guest can also order.'),
                    'hint' => $this->l('If enabled, then only logged in customers can see sample purchase options.'),
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
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Show sample quantity selector'),
                    'name' => 'WK_SAMPLE_QUANTITY_SPIN',
                    'hint' => $this->l('Select if you want users to select sample quantity or add 1 on each button click.'),
                    'desc' => $this->l('If disabled, 1 sample will be added to cart on each sample button click.'),
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
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Sample button background color'),
                    'required' => true,
                    'name' => 'WK_SAMPLE_BUTTON_BG_COLOR',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Sample button title color'),
                    'required' => true,
                    'name' => 'WK_SAMPLE_BUTTON_TEXT_COLOR',
                ],
                // @Todo in future
                /*array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'values' => $groups,
                    'name' => 'groupBox',
                    'col' => '6',
                    'hint' => $this->l('Select all the groups that you would like to apply for order sample product')
                ),*/
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];
        $sampleCarrierOptions = [['id_reference' => 0, 'name' => $this->l('Select/unselect all')]];
        $sampleCarrierOptions = array_merge($sampleCarrierOptions, Carrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        ));
        $fieldsForm['form_1']['form'] = [
            'legend' => [
                'title' => $this->l('Global sample'),
                'icon' => 'icon-archive',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Global sample'),
                    'desc' => $this->l('All products will be offered with a sample with below settings.'),
                    'name' => 'WK_GLOBAL_SAMPLE',
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
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Maximum quantity of each sample product in one cart'),
                    'form_group_class' => 'wk_global_sample_block',
                    'name' => 'WK_GLOBAL_SAMPLE_IN_CART',
                    'col' => '2',
                    'desc' => $this->l('Leave empty or fill zero if no limitation.'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Price type'),
                    'name' => 'WK_GLOBAL_SAMPLE_PRICE_TYPE',
                    'form_group_class' => 'wk_global_sample_block wk_price_type_wrap',
                    'is_bool' => true,
                    'col' => '8',
                    'options' => [
                        'id' => 'id_option',
                        'name' => 'name',
                        'query' => [
                            [
                                'id_option' => 1,
                                'name' => $this->l('Product standard price'),
                            ],
                            [
                                'id_option' => 2,
                                'name' => $this->l('Deduct fix amount from product price'),
                            ],
                            [
                                'id_option' => 3,
                                'name' => $this->l('Deduct percentage of price from product price'),
                            ],
                            [
                                'id_option' => 4,
                                'name' => $this->l('Custom price'),
                            ],
                            [
                                'id_option' => 5,
                                'name' => $this->l('Free sample'),
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Amount'),
                    'prefix' => $this->context->currency->sign,
                    'required' => true,
                    'name' => 'WK_GLOBAL_SAMPLE_AMOUNT',
                    'form_group_class' => 'wk_global_sample_block wk_price_type_amount',
                    'col' => '2',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Sample price'),
                    'prefix' => $this->context->currency->sign,
                    'required' => true,
                    'name' => 'WK_GLOBAL_SAMPLE_PRICE',
                    'form_group_class' => 'wk_global_sample_block wk_price_type_customprice',
                    'col' => '2',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Tax'),
                    'name' => 'WK_GLOBAL_SAMPLE_TAX',
                    'form_group_class' => 'wk_global_sample_block wk_price_type_tax',
                    'is_bool' => true,
                    'options' => [
                        'id' => 'id_option',
                        'name' => 'name',
                        'query' => [
                            [
                                'id_option' => 0,
                                'name' => $this->l('Tax excluded'),
                            ],
                            [
                                'id_option' => 1,
                                'name' => $this->l('Tax included'),
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Percentage'),
                    'prefix' => '%',
                    'name' => 'WK_GLOBAL_SAMPLE_PERCENT',
                    'required' => true,
                    'form_group_class' => 'wk_global_sample_block wk_price_type_percent',
                    'col' => '2',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Sample weight'),
                    'suffix' => Configuration::get('PS_WEIGHT_UNIT'),
                    'name' => 'WK_GLOBAL_SAMPLE_WEIGHT',
                    'form_group_class' => 'wk_global_sample_block',
                    'col' => '2',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Sample button title'),
                    'name' => 'WK_GLOBAL_SAMPLE_BUTTON_LABEL',
                    'form_group_class' => 'wk_global_sample_block wk_cus_margin',
                    'lang' => true,
                    'col' => '3',
                    'desc' => sprintf($this->l('Maximum %d characters are allowed.'), WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Sample description'),
                    'form_group_class' => 'wk_global_sample_block wk_sample_desc_mce',
                    'name' => 'WK_GLOBAL_SAMPLE_DESC',
                    'lang' => true,
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Sample carriers'),
                    'form_group_class' => 'wk_global_sample_block wk_global_sample_carriers',
                    'name' => 'WK_GLOBAL_SAMPLE_CARRIERS',
                    'desc' => $this->l('Only selected carriers will be available for a sample product.') . ' ' .
                    $this->l(' If no carrier selected, then standard product settings will be applied.'),
                    'values' => [
                        'query' => $sampleCarrierOptions,
                        'id' => 'id_reference',
                        'name' => 'name',
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitGlobalSample',
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $sampleBtnTitle = [];
        $sampleBtnDesc = [];
        $allLanguages = $this->context->controller->getLanguages();
        foreach ($allLanguages as $lang) {
            $title = trim(Configuration::get(
                'WK_GLOBAL_SAMPLE_BUTTON_LABEL',
                $lang['id_lang']
            ));
            $sampleBtnTitle[$lang['id_lang']] = ($title && Tools::strlen($title)) ? $title : 'Buy sample';
            $sampleBtnDesc[$lang['id_lang']] = trim(Configuration::get(
                'WK_GLOBAL_SAMPLE_DESC',
                $lang['id_lang']
            ));
        }

        $formValues = $this->getConfigFormValues();
        $formValues['WK_GLOBAL_SAMPLE_BUTTON_LABEL'] = $sampleBtnTitle;
        $formValues['WK_GLOBAL_SAMPLE_DESC'] = $sampleBtnDesc;
        $carriers = json_decode(Tools::stripslashes(Configuration::get('WK_GLOBAL_SAMPLE_CARRIERS')));
        if (is_array($carriers)) {
            foreach ($carriers as $idCarr) {
                $formValues['WK_GLOBAL_SAMPLE_CARRIERS_' . $idCarr] = true;
            }
        }
        $helper->tpl_vars = [
            'fields_value' => $formValues,
            'languages' => $allLanguages,
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function getConfigFormValues()
    {
        $keys = [
            'WK_MAX_SAMPLE_IN_CART',
            'WK_SAMPLE_STOCK_UPDATE',
            'WK_SAMPLE_LOGGED_ONLY',
            'WK_SAMPLE_QUANTITY_SPIN',
            'WK_SAMPLE_BUTTON_BG_COLOR',
            'WK_SAMPLE_BUTTON_TEXT_COLOR',
            'WK_GLOBAL_SAMPLE',
            'WK_GLOBAL_SAMPLE_IN_CART',
            'WK_GLOBAL_SAMPLE_PRICE_TYPE',
            'WK_GLOBAL_SAMPLE_AMOUNT',
            'WK_GLOBAL_SAMPLE_PRICE',
            'WK_GLOBAL_SAMPLE_TAX',
            'WK_GLOBAL_SAMPLE_PERCENT',
            'WK_GLOBAL_SAMPLE_WEIGHT',
        ];

        return Configuration::getMultiple($keys);
    }

    /*public function hookDisplayProductAdditionalInfo($params)
    {
        $product = $params['product'];
        if ((Tools::getValue('action') != 'quickview') && !Tools::getValue('quickview')) {
            if (Configuration::get('WK_SAMPLE_LOGGED_ONLY')) {
                if (isset($this->context->customer->id)) {
                    return $this->displaySampleButton($product);
                }
            } else {
                return $this->displaySampleButton($product);
            }
        } else {
            if (Configuration::get('WK_SAMPLE_LOGGED_ONLY')) {
                if (isset($this->context->customer->id)) {
                    return $this->displaySampleButton($product, true);
                }
            } else {
                return $this->displaySampleButton($product, true);
            }
        }
    }*/

public function hookDisplayProductAdditionalInfo($params)
{
    $product = $params['product'];

    $langCode = Context::getContext()->language->language_code; 

    // Check if NOT quickview
    if ((Tools::getValue('action') != 'quickview') && !Tools::getValue('quickview')) {

        // Check if sample button exists by calling hookDisplayFooterProduct
        $sampleButton = $this->hookDisplayFooterProduct($params);

        if (!empty($sampleButton)) {
            // Return the sample button HTML wrapper
            return '
            <div class="product__group product__group--sample">
                <button class="product-cta product-cta--small" data-block-anchor="" data-block-class=".block-cta">
                    <img src="/themes/modez/assets/img/sample.webp" alt="" loading="lazy" width="15" height="15">
                    <span class="product-cta__content">
                        <span class="product-cta__title">Commandez un échantillon</span>
                    </span>
                </button>
            </div>
<script>
            document.addEventListener("DOMContentLoaded", function () {
                var lang = "' . strtolower($langCode) . '"; // e.g. "it-it", "es-es"
                var translations = {
                    "fr-fr": "Commandez un échantillon",
                    "en-gb": "Order a sample",
                    "en-us": "Order a sample",
                    "es-es": "Pedir una muestra",
                    "de-de": "Ein Muster bestellen",
                    "it-it": "Ordina un campione",
                    "nl-nl": "Bestel een staal",
                    "pt-pt": "Pedir uma amostra",
                    "pl-pl": "Zamów próbkę",
                    "ro-ro": "Comandă o mostră",
                    "hu-hu": "Minta rendelése",
                    "cs-cz": "Objednat vzorek",
                    "sk-sk": "Objednať vzorku",
                    "sl-si": "Naroči vzorec",
                    "bg-bg": "Поръчай мостра",
                    "hr-hr": "Naruči uzorak",
                    "el-gr": "Παραγγείλετε ένα δείγμα",
                    "sv-se": "Beställ ett prov",
                    "fi-fi": "Tilaa näyte",
                    "da-dk": "Bestil en prøve",
                    "lv-lv": "Pasūtīt paraugu",
                    "lt-lt": "Užsisakyti pavyzdį",
                    "et-ee": "Telli proov",
                    "mt-mt": "Ordna kampjun"
                };
                var baseLang = lang.split(\'-\')[0];
                var el = document.querySelector(".product-cta__title");
                if (el) {
                    if (translations[lang]) {
                        el.textContent = translations[lang];
                    } else if (translations[baseLang]) {
                        el.textContent = translations[baseLang];
                    }
                }
            });
            </script>';
        }
    }

    // For quickview or if no sample button, return empty
    return '';
}

public function hookDisplayFooterProduct($params)
{
    $product = $params['product'];
    $objSampleProductMap = new WkSampleProductMap();

    $sample = $objSampleProductMap->getSampleProduct($product['id']);

    if ($sample && $sample['active']) {
        return $this->displaySampleButton($product);
    }

    return '';
}




    private function getTaxIncludedSampleAmount($sample, $idTaxRulesGroup, $psProductPrice)
    {
        $sampleAmount = ($sample['price_type'] == 2) ?
        (float) $sample['amount'] :
        (($psProductPrice * (float) $sample['amount']) / 100);
        $taxConfig = new TaxConfiguration();
        $id_address = $this->getAddressIdFromContext();
        // $taxmethod = 1 for tax excl & 0 for tax incl.
        if (!$sample['price_tax'] && $id_address && $idTaxRulesGroup && $taxConfig->includeTaxes()) {
            $taxCalculator = $this->getTaxCalculator($idTaxRulesGroup, $id_address);
            if ($sample['price_type'] == 2) {
                // amount
                $sampleAmount = $taxCalculator->addTaxes((float) $sample['amount']);
            } elseif ($sample['price_type'] == 3) {
                // percent
                $amountFromPercent = (($psProductPrice * (float) $sample['amount']) / 100);
                $sampleAmount = $taxCalculator->addTaxes($amountFromPercent);
            }
        }

        return $sampleAmount;
    }

    private function getTaxCalculator($idTaxRulesGroup, $id_address)
    {
        $taxRule = new TaxRulesTaxManager(Address::initialize($id_address, true), $idTaxRulesGroup);

        return $taxRule->getTaxCalculator();
    }

    private function getAddressIdFromContext()
    {
        if ($id_address = $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            return $id_address;
        } else {
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
            if (!empty($addresses)) {
                return Address::getFirstCustomerAddressId($this->context->customer->id);
            } else {
                return 0;
            }
        }
    }

    private function removeTaxes($amount, $id_tax_rules_group)
    {
        $id_address = $this->getAddressIdFromContext();
        $taxCalculator = $this->getTaxCalculator($id_tax_rules_group, $id_address);
        $taxRate = $taxCalculator->getTotalRate();

        return ($amount * 100) / (100 + (float) $taxRate);
    }

    public function getSamplePrice($sample)
    {
        $product = new Product($sample['id_product']);
        $taxConfig = new TaxConfiguration();
        $taxMethod = $taxConfig->includeTaxes();
        // Add specific price discount
        $productPrice = Product::getPriceStatic($sample['id_product'], $taxMethod);

        $amountToDeduct = $this->getTaxIncludedSampleAmount($sample, $product->id_tax_rules_group, $productPrice);
        $samplePrice = '';

        if ($sample['price_type'] == 2) {
            if (!$taxMethod) {
                // excluded
                if ($sample['price_tax']) {
                    $amountToDeduct = $this->removeTaxes($sample['amount'], $product->id_tax_rules_group);
                    $samplePrice = $productPrice - $this->removeTaxes($sample['amount'], $product->id_tax_rules_group);
                }
            }

            $defaultPsId = (int) Configuration::get('PS_CURRENCY_DEFAULT');

            $fromCurrency = new Currency($defaultPsId);

            $toCurrency = $this->context->currency;

            $amountToDeduct = Tools::convertPriceFull($amountToDeduct, $fromCurrency, $toCurrency);

            $samplePrice = ($amountToDeduct > $productPrice) ? 0 : $productPrice - $amountToDeduct;
        } elseif ($sample['price_type'] == 3) {
            $samplePrice = ($amountToDeduct > $productPrice) ? 0 : $productPrice - $amountToDeduct;
        } elseif ($sample['price_type'] == 4) {
            if ($sample['price_tax'] && !$taxMethod) {
                $samplePrice = $this->removeTaxes($sample['price'], $product->id_tax_rules_group);
            } else {
                $samplePrice = $sample['price'];
            }
        } elseif ($sample['price_type'] == 5) {
            $samplePrice = 0;
        }

        return $samplePrice;
    }

    public function getProductQuantityInCart($idProduct, $idAttr = false)
    {
        $allProducts = $this->context->cart->getProducts();
        $totalQuantity = 0;
        foreach ($allProducts as $cartProduct) {
            if ($cartProduct['id_product'] == $idProduct) {
                if (!$idAttr || ($idAttr == $cartProduct['id_product_attribute'])) {
                    $totalQuantity += $cartProduct['cart_quantity'];
                }
            }
        }

        return $totalQuantity;
    }

    private function isAvailableWhenOutOfStock($idProduct, $idAttr)
    {
        $objProduct = new Product($idProduct, false, $this->context->language->id);
        $shouldShowButton = $objProduct->available_for_order;
        if ($shouldShowButton) {
            $cartQuantity = (float) $this->getProductQuantityInCart($idProduct, $idAttr);
            $quantity = Product::getQuantity($objProduct->id, $idAttr);
            $quantity -= $cartQuantity;
            switch ($objProduct->out_of_stock) {
                case 0:
                    $shouldShowButton = ($quantity > 0);
                    break;
                case 1:
                    $shouldShowButton = true;
                    break;
                default:
                    $shouldShowButton = ($quantity > 0) || (Configuration::get('PS_ORDER_OUT_OF_STOCK') == 1);
                    break;
            }
        }

        return $shouldShowButton;
    }

    public function displaySampleButton($product, $isQuickView = false)
    {
        $objSampleProductMap = new WkSampleProductMap();
        $objSampleCart = new WkSampleCart();
        $sample = $objSampleProductMap->getSampleProduct($product['id']);
        if ($sample && $sample['active']) {
            $samplePrice = $this->getSamplePrice($sample);

            if ($samplePrice || ((string) $samplePrice == '0')) {
                $this->context->smarty->assign('sampleOrgPrice', $samplePrice);
                $this->context->smarty->assign('samplePrice', Tools::displayPrice($samplePrice));
            }
            $sampleCart = $objSampleCart->getSampleCartProduct(
                $this->context->cart->id,
                $product['id'],
                $product['id_product_attribute']
            );
            $cartExactQuantity = $this->getProductQuantityInCart($product['id'], $product['id_product_attribute']);
            $taxConfig = new TaxConfiguration();
            $taxMethod = $taxConfig->includeTaxes();
            $standardAdded = false;
            $productStock = StockAvailable::getQuantityAvailableByProduct(
                $product['id'],
                $product['id_product_attribute']
            );
            $sample['max_cart_qty'] = (($sample['max_cart_qty'] > 0) && ($sample['max_cart_qty'] < $productStock)) ?
            $sample['max_cart_qty'] :
            $productStock;
            $sampleFullInCart = false;
            if ($sampleCart) {
                // sample is added to cart
                if ($sample['price_type'] == 1) {
                    $productPrice = Product::getPriceStatic($product['id'], $taxMethod);
                    $this->context->smarty->assign('sampleOrgPrice', $productPrice);
                    $this->context->smarty->assign('samplePrice', Tools::displayPrice($productPrice));
                }
                $sampleFullInCart = ($sample['max_cart_qty'] > 0) && ($cartExactQuantity >= $sample['max_cart_qty']);
            } else {
                // sample is not added to cart
                $standardAdded = $cartExactQuantity > 0;
            }
            $configs = Configuration::getMultiple([
                'WK_SAMPLE_QUANTITY_SPIN',
                'WK_SAMPLE_BUTTON_BG_COLOR',
                'WK_SAMPLE_BUTTON_TEXT_COLOR',
            ]);
            $lowStockQty = (int) Configuration::get('PS_LAST_QTIES');
            $sampleQtyWarning = ((($sample['max_cart_qty'] - $cartExactQuantity) < $lowStockQty) && !$sampleFullInCart);
            $this->context->smarty->assign([
                'sample' => $sample,
                'wkShowQtySpin' => $configs['WK_SAMPLE_QUANTITY_SPIN'],
                'wkSampleBg' => $configs['WK_SAMPLE_BUTTON_BG_COLOR'],
                'wkSampleColor' => $configs['WK_SAMPLE_BUTTON_TEXT_COLOR'],
                'wkIdProduct' => $product['id'],
                'wkIdProductAttr' => $product['id_product_attribute'],
                'standardAdded' => $standardAdded,
                'sampleFullInCart' => $sampleFullInCart,
                'sampleQtyWarning' => $sampleQtyWarning,
                'isTaxExclDisplay' => !$taxMethod,
                'addToCartEnabled' => $this->isAvailableWhenOutOfStock(
                    $product['id'],
                    $product['id_product_attribute']
                ),
                'wkIdCustomer' => $this->context->customer->id,
                'cartPageURL' => $this->context->link->getPageLink(
                    'cart',
                    null,
                    null,
                    [
                        'add' => 1,
                        'id_product' => $product['id'],
                        'ipa' => 0,
                        'sample_cart' => 1,
                    ]
                ),
            ]);

            return $this->fetch('module:' . $this->name . '/views/templates/hook/productadditionalinfo.tpl');
        }
    }

    /**
     * Display sample coulmn in Order render list
     *
     * @param array $params row data
     */
    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        if (isset($params['select'])) {
            $params['select'] .= ', coalesce(wsc.sample, 0) as sample ';
        }
        if (isset($params['join'])) {
            $params['join'] .= ' LEFT JOIN ' . _DB_PREFIX_ . 'wk_sample_cart wsc ON (a.id_order = wsc.id_order)';
        }
        if (isset($params['join'])) {
            $params['group_by'] .= ' GROUP BY a.`id_order`';
        }
        $params['fields']['sample'] = [
            'title' => $this->l('Has sample (Yes/No)'),
            'type' => 'bool',
            'align' => 'text-center',
            'orderby' => false,
            'search' => true,
            'havingFilter' => true,
        ];
    }

    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        $definition = $params['definition'];
        $definition
            ->getColumns()
            ->addAfter(
                'osname',
                (new DataColumn('wk_has_sample'))
                    ->setName($this->l('Has sample (Yes/No)'))
                    ->setOptions([
                        'field' => 'wk_has_sample',
                        // 'callback' => 'callPosOrder',
                    ])
            )
        ;
        // For search filter
        $definition->getFilters()->add(
            (new Filter('wk_has_sample', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('wk_has_sample')
        );
    }

    /**
     * Hook allows to modify Customers query builder and add custom sql statements.
     *
     * @param array $params
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var CustomerFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect(
            'coalesce(wsc.sample, 0) as sample, (CASE WHEN coalesce(wsc.sample, 0)=1 THEN "' . $this->l('Yes') . '"
            WHEN coalesce(wsc.sample, 0)=0 THEN "' . $this->l('No') . '" END) AS wk_has_sample'
        );
        $searchQueryBuilder->leftJoin(
            'o',
            '`' . pSQL(_DB_PREFIX_) . 'wk_sample_cart`',
            'wsc',
            'o.id_order = wsc.id_order'
        );
        $searchQueryBuilder->groupBy('o.`id_order`');
        // if ('order_type' === $searchCriteria->getOrderBy()) {
        //     $searchQueryBuilder->orderBy('wko.`order_type`', $searchCriteria->getOrderWay());
        // }
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (Tools::strtolower($filterValue) == 'yes') {
                $filterValue = 1;
            }
            if (Tools::strtolower($filterValue) == 'no') {
                $filterValue = 0;
            }
            if ('wk_has_sample' === $filterName) {
                $searchQueryBuilder->andWhere('wsc.`sample` = :isSample');
                $searchQueryBuilder->setParameter('isSample', $filterValue);

                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('wsc.`sample` IS NULL');
                }
            }
        }
    }

    public function hookActionObjectOrderDetailAddAfter($params)
    {
        if ($params && isset($params['object']->id_order)) {
            $paramOrder = new Order($params['object']->id_order);
            $cart = new Cart($paramOrder->id_cart);
            $allOrders = Order::getByReference($paramOrder->reference)->getResults();
            $objSampleCart = new WkSampleCart();
            $objSampleProductMap = new WkSampleProductMap();
            foreach ($allOrders as $eachOrder) {
                $products = $eachOrder->getProducts();
                foreach ($products as $product) {
                    $sample = $objSampleProductMap->getSampleProduct($product['product_id']);
                    $sampleCart = $objSampleCart->getSampleCartProduct(
                        $cart->id,
                        $product['product_id'],
                        $product['product_attribute_id']
                    );
                    if ($sample && $sampleCart) {
                        if (!Configuration::get('WK_SAMPLE_STOCK_UPDATE')) {
                            StockAvailable::updateQuantity(
                                $product['product_id'],
                                $product['product_attribute_id'],
                                $product['product_quantity']
                            );
                        }
                        // update cart order
                        $objSampleCart->updateCartOrder(
                            $cart->id,
                            $product['product_id'],
                            $eachOrder->id,
                            $product['product_attribute_id']
                        );
                        // delete specific price
                        $objSampleCart->deleteSampleSpecificPrice(
                            $cart->id,
                            $product['product_id'],
                            $product['product_attribute_id']
                        );
                    }
                }
            }
        }
    }

    /**
     * Display Extra Information on Product Edit
     *
     * @param array $params this product details
     *
     * @return tpl
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $isSample = [];
        $idProduct = (int) $params['id_product'];
        if ($idProduct) {
            $objSampleProductMap = new WkSampleProductMap();
            $isglobalSample = $objSampleProductMap->getSampleProduct($idProduct, true);
            $isNotglobalSample = $objSampleProductMap->getSampleProduct($idProduct, false);
            if (!empty($isglobalSample)) {
                $isSample = $isglobalSample;
            } elseif (!empty($isNotglobalSample)) {
                $isSample = $isNotglobalSample;
            }
            $product = new Product($idProduct);

            $this->context->smarty->assign([
                'isSample' => $isSample,
            ]);

            if ($isNotglobalSample) {
                $this->context->smarty->assign([
                    'sign' => $this->context->currency->sign,
                    'sample' => $isNotglobalSample,
                    'isSample' => $isSample,
                    'productPrice' => Tools::displayPrice($product->getPrice(false)),
                ]);
            }
            $sampleExists = false;
            if ($sampleFileName = $objSampleProductMap->getSampleFileName($idProduct)) {
                $fileDir = _PS_MODULE_DIR_ . $this->name . '/views/samples/';
                if (Tools::strlen(trim($sampleFileName['sample_file']))
                    && file_exists($fileDir . $sampleFileName['sample_file'])
                ) {
                    $sampleExists = true;
                    $this->context->smarty->assign('sampleFileName', $sampleFileName['sample_file']);
                }
            }
            $product_download = new ProductDownload();
            $id_product_download = $product_download->getIdFromIdProduct(
                $this->context->controller->getFieldValue($product, 'id')
            );
            if ($id_product_download) {
                $product_download = new ProductDownload($id_product_download);
            }
            $allLanguages = Language::getLanguages();
            $allCarriers = Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                Carrier::ALL_CARRIERS
            );
            $sampleCarriers = $objSampleProductMap->getSampleCarriers($idProduct, $this->context->shop->id);
            $selectedCarriers = array_column($sampleCarriers, 'id_reference');
            $this->context->smarty->assign([
                'sign' => $this->context->currency->sign,
                'isVirtual' => $product->is_virtual,
                'idProduct' => $idProduct,
                'allLanguages' => $allLanguages,
                'wk_language' => $this->context->language->id,
                'shouldUpload' => $product_download->id && $product_download->filename,
                'sampleExists' => $sampleExists,
                'attachmentMaxSize' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'wkWeightUnit' => Configuration::get('PS_WEIGHT_UNIT'),
                'productPrice' => Tools::displayPrice($product->getPrice(false)),
                'wk_carrier_list' => $allCarriers,
                'wk_carrier_selected' => $selectedCarriers,
                'WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS' => WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS,
            ]);

            return $this->display(__FILE__, 'adminproduct.tpl');
        }

        return '';
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof Product) && isset($params['object']->id)) {
            // Delete sample and carrier table entries
            $objSampleProductMap = new WkSampleProductMap();
            $sampleProduct = $objSampleProductMap->getSampleProduct($params['object']->id, false);
            if ($sampleProduct && $sampleProduct['id_sample_product']) {
                $sample = new WkSampleProductMap((int) $sampleProduct['id_sample_product']);
                $sample->delete();
            }
        }
    }

    public function removeScriptFromHtml($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML(htmlspecialchars_decode($html));
        $script = $dom->getElementsByTagName('script');
        $remove = [];
        foreach ($script as $item) {
            $remove[] = $item;
        }
        foreach ($remove as $item) {
            $item->parentNode->removeChild($item);
        }

        return $dom->saveHTML();
    }

    public function hookActionObjectProductAddBefore($params)
    {
        $this->validationBeforeProductSave();
    }

    public function hookActionObjectProductUpdateBefore()
    {
        $this->validationBeforeProductSave();
    }

    public function validationBeforeProductSave()
    {
        $useGlobal = Tools::getValue('wk_follow_setting');
        $status = Tools::getValue('sample_active');
        if ($useGlobal == 1) {
            if ($status == 'on' && Configuration::get('WK_GLOBAL_SAMPLE') == 0) {
                $this->context->controller->errors['hooks_wk_follow_global'] = [
                    $this->l('Global sample configuration is not enable.'),
                ];
            }
        } elseif ($useGlobal == 2) {
            $maxCartQty = trim(Tools::getValue('max_cart_qty'));
            $weight = trim(Tools::getValue('wk_sample_weight'));
            $priceType = trim(Tools::getValue('wk_sample_price_type'));
            $priceTax = trim(Tools::getValue('wk_sample_price_tax'));
            $price = trim(Tools::getValue('wk_sample_price'));
            $sampleAmount = Tools::getValue('sample_amount');
            if ($maxCartQty && !Validate::isUnsignedInt($maxCartQty)) {
                $this->context->controller->errors['hooks_max_cart_qty'] = [
                    $this->l('Maximum quantity in cart should be a positive number.'),
                ];
            }
            if (($weight && !Validate::isFloat($weight)) || ($weight < 0)) {
                $this->context->controller->errors['hooks_wk_sample_weight'] = [
                    $this->l('Weight is not valid.'),
                ];
            }
        }
        $allLanguages = Language::getLanguages();
        $sampleTitles = [];
        $sampleDescs = [];
        foreach ($allLanguages as $lang) {
            $sampleTitle = Tools::getValue('sample_btn_label_' . $lang['id_lang']);
            if (Tools::strlen(trim($sampleTitle)) > WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS) {
                $this->context->controller->errors['hooks_sample_btn_label_' . $lang['id_lang']] = [
                    $this->l('Please enter button label upto ') . WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS . $this->l(' characters.') .
                ' : (' . $lang['iso_code'] . ')',
                ];
            } elseif (Tools::strlen(trim($sampleTitle)) && !Validate::isGenericName($sampleTitle)) {
                $this->context->controller->errors['hooks_sample_btn_label_' . $lang['id_lang']] = [
                    $this->l('Button label is not valid.') . ' : (' . $lang['iso_code'] . ')',
                ];
            } else {
                $sampleTitles[$lang['id_lang']] = pSQL(trim($sampleTitle));
            }
            $sampleDesc = trim(Tools::getValue('wk_sample_desc_' . $lang['id_lang']));
            if ($sampleDesc && Tools::strlen(trim($sampleDesc)) && !Validate::isCleanHtml($sampleDesc)) {
                $this->context->controller->errors['hooks_wk_sample_desc_' . $lang['id_lang']] = [
                    $this->l('Description is not valid.') . ' : (' . $lang['iso_code'] . ')',
                ];
            }
        }

        if (!empty($this->context->controller->errors)) {
            http_response_code(400);
            exit(json_encode($this->context->controller->errors));
        }
    }

    /**
     * Save product extra information
     *
     * @param array $params this product details
     */
    public function hookActionProductSave($params)
    {
        if ($params['id_product']) {
            $isNewProductPageEnable = WkSampleProductMap::checkNewPSProductPage();
            ++$this->count;
            $status = Tools::getValue('sample_active');
            $useGlobal = Tools::getValue('wk_follow_setting');
            if ($useGlobal === false) {
                // sample form not submitted (in case of new product)
                return;
            }
            if ($useGlobal == 1) {
                if ($status == 'on' && Configuration::get('WK_GLOBAL_SAMPLE') == 0) {
                    $this->context->controller->errors['hooks_wk_follow_global'] = [
                        $this->l('Global sample configuration is not enable.'),
                    ];
                }
            }

            if (!empty($this->context->controller->errors)) {
                http_response_code(400);
                exit(json_encode($this->context->controller->errors));
            }
            $maxCartQty = trim(Tools::getValue('max_cart_qty'));
            $priceType = trim(Tools::getValue('wk_sample_price_type'));
            $priceTax = trim(Tools::getValue('wk_sample_price_tax'));
            $price = trim(Tools::getValue('wk_sample_price'));
            $weight = trim(Tools::getValue('wk_sample_weight'));
            $sampleAmount = Tools::getValue('sample_amount');
            if ($status && ($status == 'on') && $useGlobal == 1) {
                $objSampleProductMap = new WkSampleProductMap();
                $sampleProduct = $objSampleProductMap->getSampleProduct($params['id_product'], false);
                if ($sampleProduct && $sampleProduct['id_sample_product']) {
                    $sample = new WkSampleProductMap((int) $sampleProduct['id_sample_product']);
                    $sample->delete();
                }

                return;
            }
            $psProduct = $params['product'];

            if ($maxCartQty && !Validate::isUnsignedInt($maxCartQty)) {
                $this->context->controller->errors['hooks_max_cart_qty'] = [
                    $this->l('Maximum quantity in cart should be a positive number.'),
                ];
            }
            if (($weight && !Validate::isFloat($weight)) || ($weight < 0)) {
                $this->context->controller->errors['hooks_wk_sample_weight'] = [
                    $this->l('Weight is not valid.'),
                ];
            }
            $taxRules = TaxRule::getTaxRulesByGroupId(
                $this->context->language->id,
                $psProduct->id_tax_rules_group
            );
            if ($priceType == 3) {
                // If type is percent, then tax calculation will always be Tax-included
                $priceTax = 1;
            }
            if ($priceType == 2 || $priceType == 3) {
                // Calculate product final price as per tax
                $taxConfig = new TaxConfiguration();
                $taxMethod = $taxConfig->includeTaxes();
                $productPrice = $psProduct->getPrice($taxMethod);
                if ($sampleAmount && !Validate::isUnsignedFloat($sampleAmount)) {
                    $this->context->controller->errors['form_hooks_sample_amount'] = [
                        $this->l('Value should be positive.'),
                    ];
                } else {
                    $sampleAmountWithTax = $sampleAmount;
                    if ($priceTax == 1) {
                        if ($priceType == 2) {
                            if (!(float) $sampleAmount) {
                                $this->context->controller->errors['hooks_sample_amount'] = [
                                    $this->l('Please enter the amount'),
                                ];
                            } elseif ($sampleAmount > $productPrice) {
                                $this->context->controller->errors['hooks_sample_amount'] = [
                                    $this->l('Amount should be less than product price.'),
                                ];
                            }
                        } else {
                            $sampleAmountPercent = ($productPrice * $sampleAmount) / 100;
                            if (!$sampleAmountPercent) {
                                $this->context->controller->errors['hooks_sample_amount'] = [
                                    $this->l('Please enter the amount.'),
                                ];
                            } elseif ($sampleAmountPercent > $productPrice) {
                                $this->context->controller->errors['hooks_sample_amount'] = [
                                    $this->l('Amount should be less than product price.'),
                                ];
                            }
                        }
                    } else {
                        if (!(float) $sampleAmount) {
                            $this->context->controller->errors['hooks_sample_amount'] = [
                                $this->l('Please enter the amount'),
                            ];
                        } elseif ($sampleAmount > $productPrice) {
                            $this->context->controller->errors['hooks_sample_amount'] = [
                                $this->l('Amount should be less than product price.'),
                            ];
                        }
                        foreach ($taxRules as $taxArr) {
                            // If any taxincluded amount is greater than product price
                            $taxRate = $taxArr['rate'];
                            if ($priceType == 2) {
                                $sampleAmountWithTax = $sampleAmount + (($sampleAmount * $taxRate) / 100);
                            }
                            if ($sampleAmountWithTax > $productPrice) {
                                $this->context->controller->errors['hooks_sample_amount'] = [
                                    $this->l('Amount should be less than product price.'),
                                ];
                                break;
                            }
                        }
                    }
                }
            }
            if ($priceType == 4) {
                if (!(float) $price) {
                    $this->context->controller->errors['hooks_wk_sample_price'] = [
                        $this->l('Please enter the price.'),
                    ];
                } elseif ($price && !Validate::isPrice($price)) {
                    $this->context->controller->errors['hooks_wk_sample_price'] = [
                        $this->l('Price is not valid.'),
                    ];
                }
            }
            $allLanguages = Language::getLanguages();
            $sampleTitles = [];
            $sampleDescs = [];
            foreach ($allLanguages as $lang) {
                $sampleTitle = Tools::getValue('sample_btn_label_' . $lang['id_lang']);
                if (Tools::strlen(trim($sampleTitle)) > WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS) {
                    $this->context->controller->errors['hooks_sample_btn_label_' . $lang['id_lang']] =
                    $this->l('Please enter button label upto ') . WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS . $this->l(' characters.') .
                    ' : (' . $lang['iso_code'] . ')';
                } elseif (Tools::strlen(trim($sampleTitle)) && !Validate::isGenericName($sampleTitle)) {
                    $this->context->controller->errors['hooks_sample_btn_label_' . $lang['id_lang']] =
                    $this->l('Button label is not valid.') .
                    ' : (' . $lang['iso_code'] . ')';
                } else {
                    $sampleTitles[$lang['id_lang']] = pSQL(trim($sampleTitle));
                }

                $objProductMap = new WkSampleProductMap();

                $sampleDesc = trim(Tools::getValue('wk_sample_desc_' . $lang['id_lang']));
                $sampleDescs[$lang['id_lang']] = trim(preg_replace('/\s+/', ' ', $sampleDesc));
            }

            if (!empty($this->context->controller->errors)) {
                http_response_code(400);
                exit(json_encode($this->context->controller->errors));
            }

            $objSampleProductMap = new WkSampleProductMap();
            $sampleProduct = $objSampleProductMap->getSampleProduct($params['id_product'], false);
            if ($sampleProduct && $sampleProduct['id_sample_product']) {
                $sample = new WkSampleProductMap((int) $sampleProduct['id_sample_product'], $this->context->language->id, $this->context->shop->id);
            } else {
                $sample = new WkSampleProductMap();
            }
            unset($sampleProduct);
            unset($objSampleProductMap);
            $sample->id_product = (int) $params['id_product'];
            $sample->id_product_attribute = (int) $psProduct->cache_default_attribute;
            $sample->max_cart_qty = (int) $maxCartQty;
            $sample->price_type = (int) $priceType;
            $sample->price_tax = (int) $priceTax;
            $sample->amount = (float) $sampleAmount;
            if ($priceType == 4) {
                $sample->price = (float) $price;
            }
            $sample->weight = (float) $weight;
            $sample->button_label = $sampleTitles;
            $sample->description = $sampleDescs;
            $sample->active = (int) ($status === 'on' ? 1 : 0);
            if (isset($this->context->cookie->wksample_file_name)) {
                $parts = explode('__::__', $this->context->cookie->wksample_file_name);
                if (count($parts) == 2 && ((int) $parts[0] == $params['id_product'])) {
                    $sample->sample_file = pSQL($parts[1]);
                }
                unset($this->context->cookie->wksample_file_name);
                $this->context->cookie->write();
            }
            if ($isNewProductPageEnable) {
                if ($this->count == 1) {
                    $sample->save();
                }
            } else {
                $sample->save();
            }
            // Carriers
            $carrierList = Tools::getValue('wk_sample_carriers');
            if (!is_array($carrierList)) {
                $carrierList = [];
            }
            $sample->setSampleCarriers($carrierList, $sample->id_product, $this->context->shop->id);
        }
    }

    /**
     * Validate & Customize Cart for Sample
     *
     * @param int $idCart
     */
    public function customizeSampleCart()
    {
        $sampleCart = Tools::getValue('sample_cart');
        $idProduct = Tools::getValue('id_product');
        if ($sampleCart && $idProduct) {
            Tools::redirect($this->context->link->getPageLink('cart', null, null, ['action' => 'show']));
        }
    }

    public function hookDisplayHeader()
    {
        $this->customizeSampleCart();
        Media::addJsDef(
            [
                'loginreq' => $this->l('To buy sample product you need to login first.'),
                'sampleSpecificPriceURL' => $this->context->link->getModuleLink(
                    'wksampleproduct',
                    'samplespecificprice'
                ),
                'sampleCartPage' => $this->context->link->getModuleLink(
                    'wksampleproduct',
                    'samplespecificprice'
                ),
            ]
        );
    }

    public function hookActionSampleProductAddInCart($params)
    {
        $idProduct = $params['idProduct'];
        $idAttr = $params['idAttr'];
        $objSampleCart = new WkSampleCart();
        $sampleCart = $objSampleCart->getSampleCartProduct($this->context->cart->id, $idProduct, 0);
        if (version_compare(_PS_VERSION_, '1.7.5', '<')) {
            return $sampleCart ? true : false;
        } else {
            return ($sampleCart || (($this->context->cookie->sampleProductId == $idProduct)
            && ($this->context->cookie->sampleProductIdAttr == $idAttr))) ? true : false;
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if ($this->context->controller->php_self == 'product') {
            $this->context->controller->registerStylesheet(
                'module-wksampleproduct-css',
                'modules/' . $this->name . '/views/css/wkspsample.css'
            );
            Media::addJsDef(
                [
                    'sampleCartActionUrl' => $this->context->link->getPageLink('cart'),
                ]
            );
            $this->context->controller->registerJavascript(
                'module-wksampleproduct',
                'modules/' . $this->name . '/views/js/wksampleproduct.js'
            );
        } elseif ($this->context->controller->php_self == 'cart') {
            $products = $this->context->cart->getProducts();
            $sampleProducts = [];
            $objSampleCart = new WkSampleCart();
            foreach ($products as $product) {
                $sampleCart = $objSampleCart->getSampleCartProduct(
                    $this->context->cart->id,
                    $product['id_product'],
                    $product['id_product_attribute']
                );
                if ($sampleCart) {
                    $sampleProducts[$product['id_product'] . '_' . $product['id_product_attribute']] = 0;
                }
            }
            Media::addJsDef(['samplesInCart' => $sampleProducts]);
            $this->context->controller->registerJavascript(
                'module-wksamplecart',
                'modules/' . $this->name . '/views/js/wksamplecart.js'
            );
        } else {
            $this->context->controller->registerStylesheet(
                'module-wksampleproduct-css',
                'modules/' . $this->name . '/views/css/wkspsample.css'
            );
            Media::addJsDef(
                [
                    'sampleCartActionUrl' => $this->context->link->getPageLink('cart'),
                ]
            );
            $this->context->controller->registerJavascript(
                'module-wksampleproduct',
                'modules/' . $this->name . '/views/js/wksamplequickview.js'
            );
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $isNewProductPageEnable = WkSampleProductMap::checkNewPSProductPage();

        if ($this->context->controller->php_self == 'AdminProducts') {
            $idProduct = 0;
            $productPrice = 0;
            $container = SymfonyContainer::getInstance();
            $requestStack = $container->get('request_stack');
            $request = $requestStack->getCurrentRequest();
            $idProduct = $request->get('id') ?? $request->get('productId');
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $productPrice = (float) $objProduct->price;
            }
            Media::addJsDef(
                [
                    'saveSample' => $this->context->link->getAdminLink('AdminWkBulkSample'),
                    'langId' => $this->context->language->id,
                    'noSampleErrorMsg' => $this->l('Please upload sample product.'),
                    'maxSizeErrorMsg' => $this->l('File is too large.'),
                    'maxFileSizeInPs' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                    'uploadSuccessMsg' => $this->l('Sample uploaded successfully.'),
                    'deleteSuccessMsg' => $this->l('Sample deleted successfully.'),
                    'valShouldPositive' => $this->l('Value should be positive.'),
                    'valRequired' => $this->l('Please enter the value.'),
                    'invalidAmout' => $this->l('Invalid amount.'),
                    'enterCusPrice' => $this->l('Please enter the custom price.'),
                    'invalidCusPrice' => $this->l('Invalid custom price.'),
                    'positiveQty' => $this->l('Please enter a positive quantity.'),
                    'invalidWeight' => $this->l('Invalid weight.'),
                    'invalidQty' => $this->l('Invalid quantity.'),
                    'invalidSampleTitle' => $this->l('Invalid sample button title.'),
                    'adminProdPrice' => $productPrice,
                    'fixAmountGreater' => $this->l('Amount should be less than product price.'),
                    'globalSettigValidation' => $this->l('Global sample configuration is not enable.'),
                    'isGlobalSampleEnable' => Configuration::get('WK_GLOBAL_SAMPLE'),
                ]
            );

            $this->context->controller->addJS(
                $this->_path . '/views/js/wksampleproducttab.js'
            );
            if (!$isNewProductPageEnable) {
                $this->context->controller->addCSS(
                    $this->_path . '/views/css/wkspsample.css'
                );
            }
            if ($isNewProductPageEnable) {
                $this->context->controller->addJS(
                    $this->_path . '/views/js/validationNewCatalog.js'
                );
            }
        }
    }

    /**
     * Delete Sample from our map if delete
     *
     * @param array $params
     */
    public function hookActionObjectProductInCartDeleteAfter($params)
    {
        $objSampleCart = new WkSampleCart();
        $objSampleCart->deleteSampleSpecificPrice(
            $params['id_cart'],
            $params['id_product'],
            $params['id_product_attribute']
        );
        $objSampleCart->deleteSampleCart($params['id_cart'], $params['id_product'], $params['id_product_attribute']);
    }

    public function getSampleProductCarriers($idCart, $idProduct, $idShop)
    {
        $cacheId = 'WkSampleProduct_getSampleProductCarriers_' . (int) $idCart . '_' . (int) $idProduct . '_' . (int) $idShop;
        if (!Cache::isStored($cacheId)) {
            $objSampleCart = new WkSampleCart();
            $sampleCart = $objSampleCart->getSampleCartProduct(
                $idCart,
                $idProduct
            );
            $query = new DbQuery();
            $query->select('id_carrier');
            $joinTables = true;
            if ($sampleCart) {
                $objSampleProductMap = new WkSampleProductMap();
                $sampleProduct = $objSampleProductMap->getSampleProduct($idProduct);
                if ($sampleProduct) {
                    if ((int) $sampleProduct['id_sample_product'] === 0) {
                        // Global sample
                        $carriers = json_decode(Tools::stripslashes(Configuration::get('WK_GLOBAL_SAMPLE_CARRIERS')));
                        if (is_array($carriers) && !empty($carriers)) {
                            $query->from('carrier', 'c');
                            $query->where(
                                'c.id_reference IN (' . implode(',', $carriers) . ') AND c.deleted = 0 AND c.active = 1'
                            );
                            $joinTables = false;
                        } else {
                            // No global carrier, follow standard settings
                            $query->from('product_carrier', 'pc');
                        }
                    } else {
                        // Particular product sample carriers
                        $query->from('wk_sample_carrier', 'pc');
                    }
                } else {
                    // Not a sample product, follow standard settings
                    $query->from('product_carrier', 'pc');
                }
            } else {
                // No a sample product in cart, follow standard settings
                $query->from('product_carrier', 'pc');
            }
            if ($joinTables) {
                $query->innerJoin(
                    'carrier',
                    'c',
                    'c.id_reference = pc.id_carrier_reference AND c.deleted = 0 AND c.active = 1'
                );
                $query->where('pc.id_product = ' . (int) $idProduct);
                $query->where('pc.id_shop = ' . (int) $idShop);
            }

            $productCarriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cacheId, $productCarriers);
        }

        return Cache::retrieve($cacheId);
    }

    public function getSampleCartInformations($idCart, $idProducts = [])
    {
        $sampleInformation = [
            'samples' => [],
            'weights' => [
                'global' => (float) Configuration::get('WK_GLOBAL_SAMPLE_WEIGHT'),
            ],
        ];
        $cartSamples = WkSampleDb::getCartProduct($idProducts, $idCart);
        if (!empty($cartSamples)) {
            $idSamples = array_unique(array_column($cartSamples, 'prod_comb'));
            $idProductSamples = array_unique(array_column($cartSamples, 'id_product'));
            $sampleWeights = WkSampleDb::getSampleWeight($idProductSamples);
            $sampleInformation['samples'] = $idSamples;
            foreach ($sampleWeights as $sampleWeight) {
                $sampleInformation['weights']['prod_' . $sampleWeight['id_product']] = $sampleWeight['weight'];
            }
        }

        return $sampleInformation;
    }

    /**
     * Prevent to add main product if sample already in cart
     * Prevent to update quantity of sample if ristricted
     *
     * @param array $params
     */
// ============================================================
// COMPLETE REPLACEMENT for hookActionCartUpdateQuantityBefore
// in wksampleproduct.php (starting around line 1033)
// ============================================================

public function hookActionCartUpdateQuantityBefore($params)
{
    $cart = $params['cart'];
    $product = $params['product'];
    $cartProduct = $params['cart']->getProducts();

    $objSampleCart = new WkSampleCart();
    if (Tools::isSubmit('submitReorder') && ($idOrder = Tools::getValue('id_order'))) {
        $objSampleMap = new WkSampleProductMap();
        if ($objSampleMap->getSampleProduct($product->id)) {
            // check if product is sample
            $sampleOrder = $objSampleCart->getSampleOrderProduct(
                $idOrder,
                $product->id,
                $params['id_product_attribute']
            );
            if ($sampleOrder) {
                $this->context->cookie->sampleProductId = $product->id;
                $this->context->cookie->sampleProductIdAttr = (int) $params['id_product_attribute'];
            }
        }
    }

    if (isset($params['operator'])) {
        if ($params['operator'] == 'up') {
            $isSampleProduct = $objSampleCart->getSampleCartProduct(
                $cart->id,
                $product->id,
                $params['id_product_attribute']
            );
            if (isset($this->context->cookie->sampleProductId, $this->context->cookie->sampleProductIdAttr)
            && ($this->context->cookie->sampleProductId == $product->id)
            && ($this->context->cookie->sampleProductIdAttr == (int) $params['id_product_attribute'])
            && !$isSampleProduct
            ) {
                $oldCart = $this->context->cart;
                $this->context->cart = $cart;
                unset($this->context->cookie->sampleProductId);
                unset($this->context->cookie->sampleProductIdAttr);
                $sampleCart = new WkSampleCart();
                $sampleCart->validateSampleCart($params['id_product_attribute'], $product->id);
                $this->context->cart = $oldCart;
            } else {
                $sampleObj = new WkSampleProductMap();
                $sampleProductUpdate = $sampleObj->getSampleProduct($product->id);
                if ($sampleProductUpdate) {
                    // *** FIXED: Get tax configuration ***
                    $taxConfig = new TaxConfiguration();
                    $shopUsesTaxIncluded = $taxConfig->includeTaxes();
                    
                    // *** FIXED: Get proper product prices ***
                    $productPriceWithTax = Product::getPriceStatic(
                        $product->id, 
                        true,  // with tax
                        $params['id_product_attribute'], 
                        6, 
                        null, 
                        false, 
                        false
                    );
                    $productPriceWithoutTax = Product::getPriceStatic(
                        $product->id, 
                        false,  // without tax
                        $params['id_product_attribute'], 
                        6, 
                        null, 
                        false, 
                        false
                    );
                    
                    $sampleCarts = SpecificPrice::getIdsByProductId(
                        $product->id, 
                        $params['id_product_attribute'], 
                        $this->context->cart->id
                    );
                    
                    if (!empty($sampleCarts)) {
                        foreach ($sampleCarts as $sampleCart) {
                            $specificPriceId = $sampleCart['id_specific_price'];
                            $specificPrice = new SpecificPrice($specificPriceId);
                            
                            // *** FIXED: Calculate final price properly ***
                            $finalPrice = 0;
                            
                            if ($sampleProductUpdate['price_type'] == 1) {
                                // Standard price
                                $finalPrice = $shopUsesTaxIncluded ? $productPriceWithTax : $productPriceWithoutTax;
                                
                            } elseif ($sampleProductUpdate['price_type'] == 2) {
                                // Fixed amount deduction
                                $deductAmount = (float) $sampleProductUpdate['amount'];
                                
                                if ($sampleProductUpdate['price_tax'] == 1) {
                                    // Deduction is WITH tax
                                    if ($shopUsesTaxIncluded) {
                                        $finalPrice = $productPriceWithTax - $deductAmount;
                                    } else {
                                        $deductAmountNoTax = $objSampleCart->removeTaxes(
                                            $deductAmount, 
                                            $product->id_tax_rules_group
                                        );
                                        $finalPrice = $productPriceWithoutTax - $deductAmountNoTax;
                                    }
                                } else {
                                    // Deduction is WITHOUT tax
                                    if ($shopUsesTaxIncluded) {
                                        $deductAmountWithTax = $objSampleCart->addTaxToAmount(
                                            $deductAmount, 
                                            $product->id_tax_rules_group
                                        );
                                        $finalPrice = $productPriceWithTax - $deductAmountWithTax;
                                    } else {
                                        $finalPrice = $productPriceWithoutTax - $deductAmount;
                                    }
                                }
                                
                            } elseif ($sampleProductUpdate['price_type'] == 3) {
                                // Percentage deduction
                                $percentAmount = (float) $sampleProductUpdate['amount'];
                                
                                if ($shopUsesTaxIncluded) {
                                    $deductAmount = ($productPriceWithTax * $percentAmount) / 100;
                                    $finalPrice = $productPriceWithTax - $deductAmount;
                                } else {
                                    $deductAmount = ($productPriceWithoutTax * $percentAmount) / 100;
                                    $finalPrice = $productPriceWithoutTax - $deductAmount;
                                }
                                
                            } elseif ($sampleProductUpdate['price_type'] == 4) {
                                // *** CUSTOM PRICE - YOUR CASE ***
                                $customPrice = (float) $sampleProductUpdate['price'];
                                
                                if ($sampleProductUpdate['price_tax'] == 1) {
                                    // Custom price is WITH tax (your configuration)
                                    if ($shopUsesTaxIncluded) {
                                        // Shop displays WITH tax - use as-is
                                        $finalPrice = $customPrice;
                                    } else {
                                        // Shop displays WITHOUT tax - remove tax
                                        $finalPrice = $objSampleCart->removeTaxes(
                                            $customPrice, 
                                            $product->id_tax_rules_group
                                        );
                                    }
                                } else {
                                    // Custom price is WITHOUT tax
                                    if ($shopUsesTaxIncluded) {
                                        // Shop displays WITH tax - add tax
                                        $finalPrice = $objSampleCart->addTaxToAmount(
                                            $customPrice, 
                                            $product->id_tax_rules_group
                                        );
                                    } else {
                                        // Shop displays WITHOUT tax - use as-is
                                        $finalPrice = $customPrice;
                                    }
                                }
                                
                            } elseif ($sampleProductUpdate['price_type'] == 5) {
                                // Free sample
                                $finalPrice = 0;
                            }
                            
                            // Ensure not negative
                            if ($finalPrice < 0) {
                                $finalPrice = 0;
                            }
                            
                            // Round to 6 decimals (PrestaShop standard)
                            $finalPrice = round($finalPrice, 6);
                            
                            // *** FIXED: Update specific price object ***
                            $specificPrice->id_product = $sampleProductUpdate['id_product'];
                            $specificPrice->id_shop = $this->context->shop->id;
                            $specificPrice->id_currency = $this->context->currency->id;
                            $specificPrice->id_country = 0;
                            $specificPrice->reduction_type = 'amount';
                            $specificPrice->from_quantity = 1;
                            $specificPrice->id_group = 0;
                            $specificPrice->price = $finalPrice;  // *** THIS IS THE KEY FIX ***
                            $specificPrice->id_cart = $this->context->cart->id;
                            $specificPrice->id_customer = (int) $this->context->customer->id;
                            $specificPrice->reduction = 0;
                            $specificPrice->from = '0000-00-00 00:00:00';
                            $specificPrice->to = '0000-00-00 00:00:00';
                            $specificPrice->id_product_attribute = $params['id_product_attribute'];

                            $specificPrice->save();
                        }
                    }
                }
            }
            if ($objSampleCart->checkProductQtyInCart(
                $cart,
                $product->id,
                $params['quantity'],
                $params['id_product_attribute']
            )) {
                $objSampleProductMap = new WkSampleProductMap();
                $sample = $objSampleProductMap->getSampleProduct($product->id);
                exit(json_encode([
                    'hasError' => true,
                    'hasSample' => true,
                    'errors' => [
                        sprintf(
                            $this->l('Only %d quantity allowed to buy in single cart.'),
                            $sample['max_cart_qty']
                        ),
                    ],
                ]));
            }
        } elseif ($params['operator'] == 'down') {
            $quantity = $this->getProductQuantityInCart($product->id, $params['id_product_attribute']);
            $reduceQuantity = $params['quantity'];
            if ($quantity == $reduceQuantity) {
                $objSampleCart->deleteSampleSpecificPrice($cart->id, $product->id, $params['id_product_attribute']);
                $objSampleCart->deleteSampleCart($cart->id, $product->id, $params['id_product_attribute']);
            }
        }
    } else {
        // just for security if in case operator is not found
        if ($objSampleCart->checkProductQtyInCart($cart, $product->id, $params['id_product_attribute'])) {
            exit(json_encode([
                'hasError' => true,
                'errors' => ['Max quantity exceeded for this sample product in cart'],
            ]));
        }
    }
}

    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        $products = $order->getProducts();
        $sample = [];
        $objSampleCart = new WkSampleCart();
        foreach ($products as $product) {
            $sampleOrder = $objSampleCart->getSampleOrderProduct(
                $order->id,
                $product['product_id'],
                $product['product_attribute_id']
            );
            if ($sampleOrder) {
                $sample[] = $product;
            }
        }

        if (!empty($sample)) {
            foreach ($sample as &$product) {
                $product['sample_price'] = Tools::displayPrice($product['total_price_tax_incl']);
            }
            $this->context->smarty->assign([
                'sample' => $sample,
                'sampleCount' => count($sample),
            ]);

            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/order-detail-notifier.tpl'
            );
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);
        $products = $order->getProducts();
        $sample = [];
        $objSampleCart = new WkSampleCart();
        foreach ($products as $product) {
            $sampleOrder = $objSampleCart->getSampleOrderProduct(
                $params['id_order'],
                $product['product_id'],
                $product['product_attribute_id']
            );
            if ($sampleOrder) {
                $sample[] = $product;
            }
        }

        if (!empty($sample)) {
            foreach ($sample as &$product) {
                $product['sample_price'] = Tools::displayPrice($product['total_price_tax_incl']);
            }
            $this->context->smarty->assign([
                'sample' => $sample,
                'sampleCount' => count($sample),
            ]);

            return $this->display(__FILE__, 'displayadminorder' .
            (version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? 'sf' : '')
            . '.tpl');
        }
    }

    // Custom hook
    public function hookActionSampleProductDownloadBefore(&$params)
    {
        $info = $params[0];
        $objSampleCart = new WkSampleCart();
        $sampleOrder = $objSampleCart->getSampleOrderProduct(
            $info['id_order'],
            $info['product_id'],
            $info['product_attribute_id']
        );
        if ($sampleOrder) {
            $flag = false;
            $objSampleProductMap = new WkSampleProductMap();
            if ($sampleFileName = $objSampleProductMap->getSampleFileName($info['id_product'])) {
                $fileDir = _PS_MODULE_DIR_ . $this->name . '/views/samples/';
                if (Tools::strlen(trim($sampleFileName['sample_file']))
                    && file_exists($fileDir . $sampleFileName['sample_file'])
                ) {
                    $flag = true;
                    $params[1] = $fileDir . $sampleFileName['sample_file'];
                    $params[2] = $sampleFileName['sample_file'];
                }
            }
            if (!$flag) {
                $params[2] = false;
                $params[1] = $this->l('This sample does not have a file.');
            }
        }
    }

    public function hookDisplayCartExtraProductActions($params)
    {
        if ($this->context->controller->php_self == 'cart') {
            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/cartsamplenotifier.tpl',
                ['showNotice' => false]
            );
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if (($this->context->controller->php_self == 'order')
            || ($this->context->controller->php_self == 'order-confirmation')
        ) {
            $sampleCart = false;
            if ($this->context->controller->php_self == 'order') {
                $objSampleCart = new WkSampleCart();
                $sampleCart = $objSampleCart->getSampleCartProduct(
                    $this->context->cart->id,
                    $params['product']['id_product'],
                    $params['product']['id_product_attribute']
                );
            } else {
                if (array_key_exists('id_order', (array) $params['product'])) {
                    $idOrder = $params['product']['id_order'];
                    $objSampleCart = new WkSampleCart();
                    $order = new Order($idOrder);
                    $sampleCart = $objSampleCart->getSampleCartProduct(
                        $order->id_cart,
                        $params['product']['product_id'],
                        $params['product']['product_attribute_id']
                    );
                    $this->context->smarty->assign('addBreaks', 1);
                }
            }
            if ($sampleCart) {
                return $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/cartsamplenotifier.tpl',
                    ['showNotice' => true]
                );
            }
        }
    }

    public function hookDisplayPDFOrderSlip($params)
    {
        $order_slip = $params['object'];
        $id_order = $order_slip->id_order;
        if (!empty($this->isOrderContainsSampleProduct($id_order))) {
            $html = $this->invoiceAndOrderSlip($id_order);
            if ($html) {
                return $html;
            }
        }
    }

    public function hookDisplayPDFDeliverySlip($params)
    {
        $order_invoice = $params['object'];
        $id_order = $order_invoice->id_order;
        if (!empty($this->isOrderContainsSampleProduct($id_order))) {
            $html = $this->invoiceAndOrderSlip($id_order);
            if ($html) {
                return $html;
            }
        }
    }

    public function hookDisplayPDFInvoice($params)
    {
        $order_invoice = $params['object'];
        $id_order = $order_invoice->id_order;
        if (!empty($this->isOrderContainsSampleProduct($id_order))) {
            $html = $this->invoiceAndOrderSlip($id_order);
            if ($html) {
                return $html;
            }
        }
    }

    private function invoiceAndOrderSlip($id_order)
    {
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order)) {
            $objWkSampleCart = new WkSampleCart();
            $objCart = new Cart($order->id_cart);
            $cartProducts = $objCart->getProducts();
            $sampleProductPurchaseDetails = [];
            if (is_array($cartProducts) && count($cartProducts) > 0) {
                foreach ($cartProducts as $cartProd) {
                    $sampleProductDetails = $objWkSampleCart->getSampleOrderProduct($id_order, $cartProd['id_product'], $cartProd['id_product_attribute']);
                    if (is_array($sampleProductDetails) && count($sampleProductDetails) > 0) {
                        $sampleProductPurchaseDetails[] = [
                            'reference' => $cartProd['reference'],
                            'product' => $cartProd['name'],
                        ];
                    }
                }
            }
            $this->context->smarty->assign([
                'title' => $this->l('Sample Purchase Details '),
                'table_title_ref' => $this->l('Reference'),
                'table_title_product' => $this->l('Product'),
                'sampleProductPurchaseDetails' => $sampleProductPurchaseDetails,
            ]);

            return $this->display(__FILE__, 'invoiceandorderslip.tpl');
        }

        return false;
    }

    /**
     * Check order contains sample product.
     *
     * @param int $id_order
     *
     * @return array
     */
    private function isOrderContainsSampleProduct($id_order)
    {
        $order = new Order($id_order);
        $products = $order->getProducts();
        $sample = [];
        $objSampleCart = new WkSampleCart();
        foreach ($products as $product) {
            $sampleOrder = $objSampleCart->getSampleOrderProduct(
                $id_order,
                $product['product_id'],
                $product['product_attribute_id']
            );
            if ($sampleOrder) {
                $sample[] = $product;
            }
        }

        return $sample;
    }

    public function registerModuleHook()
    {
        return $this->registerHook([
            'displayAdminProductsExtra',
            'displayProductAdditionalInfo',
            'actionProductSave',
            'actionCartUpdateQuantityBefore',
            'actionAdminControllerSetMedia',
            'actionFrontControllerSetMedia',
            'displayHeader',
            'actionObjectProductInCartDeleteAfter',
            'actionAdminOrdersListingFieldsModifier',
            'displayAdminOrder',
            'actionSampleProductDownloadBefore',
            'displayCartExtraProductActions',
            'actionObjectOrderDetailAddAfter',
            'displayProductPriceBlock',
            'actionSampleProductAddInCart',
            'actionOrderGridDefinitionModifier',
            'actionOrderGridQueryBuilderModifier',
            'displayOrderDetail',
            'actionObjectProductDeleteAfter',
            'actionObjectProductAddBefore',
            'actionObjectProductUpdateBefore',
            'displayPDFInvoice',
            'displayPDFDeliverySlip',
            'displayPDFOrderSlip',
            'displayFooterProduct',
        ]);
    }

    public function install()
    {
        $dbObj = new WkSampleDb();
        if (!parent::install()
            || !$this->registerModuleHook()
            || !$dbObj->createTable()
            || !$this->callInstallTab()
            || !$this->defaultConfig()
        ) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminWkBulkSample', 'Bulk sample products');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = $className;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;

        return $tab->add();
    }

    public function enable($force_all = false)
    {
        if (Shop::isFeatureActive()) {
            $this->uninstallOverrides();
        }

        return parent::enable($force_all);
    }

    public function disable($force_all = false)
    {
        if (parent::disable($force_all)) {
            if (Shop::isFeatureActive()) {
                $ifExists = WkSampleDb::isDisable($this->id);
                if ($ifExists) {
                    $this->installOverrides();
                }
            }

            return true;
        }

        return false;
    }

    public function defaultConfig()
    {
        $configs = [
            'WK_SAMPLE_BUTTON_BG_COLOR' => '#2fb5d2',
            'WK_SAMPLE_QUANTITY_SPIN' => 1,
            'WK_SAMPLE_BUTTON_TEXT_COLOR' => '#ffffff',
            'WK_GLOBAL_SAMPLE' => 0,
        ];
        foreach ($configs as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function deleteConfigKey()
    {
        $keys = [
            'WK_MAX_SAMPLE_IN_CART',
            'WK_SAMPLE_STOCK_UPDATE',
            'WK_SAMPLE_LOGGED_ONLY',
            'WK_SAMPLE_QUANTITY_SPIN',
            'WK_SAMPLE_BUTTON_BG_COLOR',
            'WK_SAMPLE_BUTTON_TEXT_COLOR',
            'WK_GLOBAL_SAMPLE',
            'WK_GLOBAL_SAMPLE_IN_CART',
            'WK_GLOBAL_SAMPLE_PRICE_TYPE',
            'WK_GLOBAL_SAMPLE_AMOUNT',
            'WK_GLOBAL_SAMPLE_PRICE',
            'WK_GLOBAL_SAMPLE_TAX',
            'WK_GLOBAL_SAMPLE_PERCENT',
            'WK_GLOBAL_SAMPLE_BUTTON_LABEL',
            'WK_GLOBAL_SAMPLE_DESC',
            'WK_GLOBAL_SAMPLE_CARRIERS',
            'WK_GLOBAL_SAMPLE_WEIGHT',
        ];

        foreach ($keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        $sampleVirtualFiles = glob(_PS_MODULE_DIR_ . $this->name . '/views/samples/*');
        foreach ($sampleVirtualFiles as $sample) {
            if (!unlink($sample)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        $dbObj = new WkSampleDb();

        if (!parent::uninstall()
            || !$dbObj->deleteTables()
            || !$this->uninstallTab()
            || !$this->deleteConfigKey()
        ) {
            return false;
        }

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }
}
