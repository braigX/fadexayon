<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'quantitydiscounttable/classes/QuantityDiscountModel.php';
class AdminQuantityDiscountController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'specific_price';
        $this->className = 'QuantityDiscountModel';
        $this->bootstrap = true;

        $this->identifier = 'id_specific_price';
        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'id_specific_price';

        $this->bulk_actions = ['delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Are You Sure You Want To Delete selected items?')]];
        $this->context = Context::getContext();
        $this->fields_list = $this->createList();
    }

    protected function createList()
    {
        return
        $formList = [
            'id_currency' => [
                'title' => $this->l('Currency'),
                'search' => false,
                'type' => 'text',
                'callback' => 'printCurrency',
            ],
            'id_country' => [
                'title' => $this->l('Country'),
                'search' => false,
                'type' => 'text',
                'callback' => 'printCountry',
            ],
            'id_product' => [
                'title' => $this->l('Product'),
                'search' => false,
                'type' => 'text',
                'callback' => 'printproductName',
            ],
            'from_quantity' => [
                'title' => $this->l('Reduction on Quantity'),
                'search' => false,
                'type' => 'text',
            ],
            'reduction_type' => [
                'title' => $this->l('Reduction Type'),
                'search' => false,
                'type' => 'text',
            ],
            'reduction' => [
                'title' => $this->l('Reduction'),
                'search' => false,
                'type' => 'text',
            ],
        ];
    }

    public function printCurrency($id_currency)
    {
        $currency = new Currency($id_currency);

        return $currency->iso_code;
    }

    public function printCountry($id_country)
    {
        // Load the Country object using the provided id_country
        $country = new Country($id_country, $this->context->language->id);

        // Return the country name
        return $country->name;
    }

    public function printProductName($id_product)
    {
        // Load the Product object using the provided id_product
        $product = new Product($id_product, false, $this->context->language->id);

        // Return the product name
        return $product->name;
    }

    protected function displayForm()
    {
        $currencies = Currency::getCurrencies();
        $currency_options = [
            [
                'id' => 0,
                'name' => $this->l('All Currencies'),
            ],
        ];
        foreach ($currencies as $currency) {
            $currency_options[] = [
                'id' => $currency['id_currency'],
                'name' => $currency['name'],
            ];
        }
        $countries = Country::getCountries($this->context->language->id);
        $country_options = [
            [
                'id' => 0,
                'name' => $this->l('All Countires'),
            ],
        ];
        foreach ($countries as $country) {
            $country_options[] = [
                'id' => $country['id_country'],
                'name' => $country['name'],
            ];
        }
        $groups = Group::getGroups((int) $this->context->language->id);
        $group_options = [
            [
                'id' => 0,
                'name' => $this->l('All Groups'),
            ],
        ];
        foreach ($groups as $group) {
            $group_options[] = [
                'id' => $group['id_group'],
                'name' => $group['name'],
            ];
        }
        // Init Fields form array
        $form = [
            'legend' => [
                'title' => $this->l('Specific Prices'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Disable Combinations'),
                    'name' => 'product_combination',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Disabled Combination'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Enabled Combination'),
                        ],
                    ],
                    'desc' => $this->l('Display Product COmbinations'),
                ],
                [
                    'type' => 'productids',
                    'name' => 'product_id',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Currency'),
                    'name' => 'id_currency',
                    'options' => [
                        'query' => $currency_options,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'options' => [
                        'query' => $country_options,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Customer Group'),
                    'name' => 'id_group',
                    'options' => [
                        'query' => $group_options,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Price'),
                    'name' => 'price',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Minimum number of units purchased '),
                    'name' => 'from_quantity',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Reduction Type'),
                    'name' => 'reduction_type',
                    'options' => [
                        'query' => [
                            ['id' => 'amount', 'name' => $this->l('Amount')],
                            ['id' => 'percentage', 'name' => $this->l('Percentage')],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Reduction (%)'),
                    'name' => 'reduction',
                    'required' => true,
                ],
            ],

            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        return $form;
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
            if (Tools::getValue('product_combination') == 1) {
                $product_ids = Tools::getValue('product_id');
                if (empty($product_ids)) {
                    $this->errors[] = $this->l('Please select atleast one Product.');
                }
            } else {
                if (Tools::getValue('product_attribute_id')) {
                    $product_ids = Tools::getValue('product_id');
                    $product_attribute_id = Tools::getValue('product_attribute_id');
                    if (empty($product_attribute_id) || empty($product_ids)) {
                        $this->errors[] = $this->l('Please select atleast one Product with Combination.');
                    }
                }
            }
            if (empty(Tools::getValue('from_quantity')) || !Validate::isInt(Tools::getValue('from_quantity'))) {
                $this->errors[] = $this->l('Please Enter a Valid  Minimum number of units purchased ');
            }
            if (empty(Tools::getValue('reduction')) || !Validate::isInt(Tools::getValue('reduction'))) {
                $this->errors[] = $this->l('Please Enter a Valid  Reduction');
            }

            if (count($this->errors)) {
                // Store errors in the session
                $this->context->cookie->__set('quantity_discount_errors', json_encode($this->errors));

                $token = Tools::getAdminTokenLite('AdminQuantityDiscount');

                // Construct the base URL dynamically
                $base_url = $this->context->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/index.php';

                // Set the parameters
                $params = [
                    'controller' => 'AdminQuantityDiscount',
                    'addspecific_price' => '',
                    'token' => $token,
                ];

                // Build the complete URL
                $url = $base_url . '?' . http_build_query($params);

                // Redirect
                Tools::redirectAdmin($url);
            }
        }
    }

    public function initContent()
    {
        parent::initContent();

        // Retrieve errors from the session
        if (isset($this->context->cookie->quantity_discount_errors)) {
            $errors = json_decode($this->context->cookie->quantity_discount_errors, true);
            foreach ($errors as $error) {
                $this->errors[] = $error;
            }
            // Remove errors from the session
            unset($this->context->cookie->quantity_discount_errors);
        }
    }

    public function renderForm()
    {
        $this->fields_form = $this->displayForm();

        $products = [];
        $product_ids = [];
        if (Tools::getValue('id_specific_price')) {
            $productDetails = QuantityDiscountModel::getProductIds((int) Tools::getValue('id_specific_price'));
            foreach ($productDetails as $productDetail) {
                $product_ids[] = $productDetail['product_id'];
            }
        }
        $id_lang = (int) $this->context->language->id;
        if (!empty($product_ids)) {
            if (!is_array($product_ids)) {
                $products = explode(',', $product_ids);
            } else {
                $products = $product_ids;
            }
            if (!empty($products) && is_array($products)) {
                foreach ($products as &$product) {
                    $product = new Product((int) $product, true, (int) $id_lang);
                    $product->id_product_attribute = (int) Product::getDefaultAttribute(
                        $product->id
                    ) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                    $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById(
                        (int) $product->id_product_attribute,
                        $id_lang
                    ) : Product::getCover($product->id);
                    if (!is_array($_cover)) {
                        $_cover = Product::getCover($product->id);
                    }
                    $product->id_image = $_cover['id_image'];
                }
            }
        }
        $versionGreater = 0;
        if (Tools::version_compare(_PS_VERSION_, '1.7.7.6', '>')) {
            $versionGreater = 1;
        }
        $this->context->smarty->assign([
            'products' => $products,
            'ps_version' => $versionGreater,
        ]);

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $product_attribute_id = [];
            if (Tools::getValue('product_combination') == 0) {
                $product_attribute_id = Tools::getValue('product_attribute_id');
                $specific_price = new QuantityDiscountModel((int) Tools::getValue('id_specific_price'));
                $product_ids = Tools::getValue('product_id');
                if (!empty($product_ids) && is_array($product_ids)) {
                    $specific_price->id_product = $product_ids;
                    $specific_price->id_currency = (int) Tools::getValue('id_currency');
                    $specific_price->id_country = (int) Tools::getValue('id_country');
                    $specific_price->id_group = (int) Tools::getValue('id_group');
                    $specific_price->product_attribute_id = $product_attribute_id;
                    $specific_price->price = (float) Tools::getValue('price');
                    $specific_price->from_quantity = (int) Tools::getValue('from_quantity');
                    $specific_price->reduction = (float) (Tools::getValue('reduction') / 100);
                    $specific_price->reduction_type = Tools::getValue('reduction_type');
                    $specific_price->save();
                }
            } else {
                $specific_price = new QuantityDiscountModel((int) Tools::getValue('id_specific_price'));
                $product_ids = Tools::getValue('product_id');
                if (!empty($product_ids) && is_array($product_ids)) {
                    $specific_price->id_product = $product_ids;
                    $specific_price->id_currency = (int) Tools::getValue('id_currency');
                    $specific_price->id_country = (int) Tools::getValue('id_country');
                    $specific_price->id_group = (int) Tools::getValue('id_group');
                    $specific_price->price = (float) Tools::getValue('price');
                    $specific_price->from_quantity = (int) Tools::getValue('from_quantity');
                    $specific_price->reduction = (float) (Tools::getValue('reduction') / 100);
                    $specific_price->reduction_type = Tools::getValue('reduction_type');
                    $specific_price->save();
                }
            }
        } elseif (Tools::isSubmit('update' . $this->table)) {
            $specific_price_id = (int) Tools::getValue('id_specific_price'); // Get the product ID
            $specific_price = new SpecificPrice($specific_price_id);
            if ($specific_price->id_product > 0) {
                // Construct the base URL dynamically
                $base_url = $this->context->link->getAdminLink('AdminProducts', true);
                // Replace 'AdminProducts' with 'AdminProducts-v2/$product_id/edit' in the URL
                $base_url = str_replace('products', 'products-v2/' . $specific_price->id_product . '/edit', $base_url);
                $url = $base_url . '#tab-product_pricing-tab';
                Tools::redirectAdmin($url);
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        $url = $this->context->link->getAdminLink('AdminModules', true);
        $versionGreater = 0;
        if (Tools::version_compare(_PS_VERSION_, '1.7.7.6', '<')) {
            $versionGreater = 1;
        }
        $ps16 = 0;
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>')) {
            $ps16 = 1;
        }
        Media::addJsDef([
            'delivery_action_url' => $url . '&configure=quantitydiscounttable&action=getSearchProducts&forceJson=1' .
            '&exclude_packs=0&excludeVirtuals=0&limit=20',
            'ps_version' => $versionGreater,
            'ps16' => $ps16,
            'FMM_DC_TOKEN' => Configuration::get('FMM_DC_TOKEN'),
        ]);
        parent::setMedia($isNewTheme);
        $this->addCSS($this->module->getPathUri() . '/views/css/quantityDiscountAdmin.css');
        $this->addJS($this->module->getPathUri() . '/views/js/quantityDiscountAdmin.js');
    }
}
