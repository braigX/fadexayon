<?php
/**
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'gmerchantfeedes/classes/GMerchantFeedConfig.php';
require_once _PS_MODULE_DIR_ . 'gmerchantfeedes/traits/RenderForm.php';

class GMerchantFeedES extends Module
{
    use RenderForm;

    protected $useTax = false;
    protected $config_form = false;
    protected static $currentIndex;
    protected static $tableKey = 'gmerchantfeedes';
    protected $errors = array();
    protected $confirmations = array();
    protected $alternativeJs = false;
    protected $shipping_weight_format = 3;
    protected $taxonomy_language = 0;
    protected $categoryNames = [];
    private $changePriceRules = [];
    private static $priceChangeTypes = [
        'percent' => 'percent',
        'value' => 'value'
    ];

    public $gmfOptions = [];

    public $id_product;

    public function __construct()
    {
        $this->name = 'gmerchantfeedes';
        $this->tab = 'administration';
        $this->version = '1.5.2';
        $this->author = 'ExtraSolutions';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'f8d3be8fa41d6a01229f3527154ac9b5';
        $this->alternativeJs = (bool)Configuration::get('GMERCHANTFEEDS_ALT_JS');

        parent::__construct();

        $this->id_product = 45244;
        $this->displayName = $this->l('Google Merchant Center (Google Shopping Feed) PRO');
        $this->description = $this->l('Export your products to Google Merchant Center, easily!');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->gmfOptions['mpn'] = property_exists(Product::class, 'mpn');
    }

    /**
     * @return bool
     */
    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        if (!is_dir(_PS_CACHEFS_DIRECTORY_)) {
            @mkdir(_PS_CACHEFS_DIRECTORY_, 0777, true);
        }

        Configuration::updateValue('GMERCHANTFEEDS_ALT_JS', 0);
        Configuration::updateValue('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT', 0);
//        actionProductUpdate
        return parent::install() &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('deleteproduct') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    /**
     * @param $params
     * @throws PrestaShopDatabaseException
     */
    public function hookActionProductUpdate($params)
    {
        if (isset($params['id_product']) && $params['id_product'] > 0) {
            $this->updateExtraProductFields($params['id_product']);
        }
    }

    /**
     * @param int $id_product
     * @throws PrestaShopDatabaseException
     */
    private function updateExtraProductFields($id_product = 0)
    {
        $data_insert = array();

        foreach (Language::getLanguages() as $lang) {
            $title = Tools::getValue('gmerchantfeedes_title_' . $lang['id_lang']);
            $title = trim($title);

            $short_description = Tools::getValue('gmerchantfeedes_short_description_' . $lang['id_lang']);
            $short_description = trim($short_description);

            $description = Tools::getValue('gmerchantfeedes_description_' . $lang['id_lang']);
            $description = trim($description);

            $addition_code = Tools::getValue('gmerchantfeedes_addition_code');
            $addition_code = trim($addition_code);

            if (empty($title) && empty($short_description) && empty($description) && empty($addition_code)) {
                continue;
            }

            $data_insert[] = array(
                'id_product' => (int)$id_product,
                'title' => pSQL($title),
                'short_description' => pSQL($short_description),
                'description' => pSQL($description),
                'addition_code' => pSQL($addition_code, true),
                'id_lang' => (int)$lang['id_lang']
            );
        }

        DB::getInstance()->delete('gmerchantfeedes_product_rewrites', 'id_product = ' . (int)$id_product);
        if (count($data_insert)) {
            DB::getInstance()->insert('gmerchantfeedes_product_rewrites', $data_insert);
        }
    }

    /**
     * @param $params
     */
    public function hookDeleteProduct($params)
    {
        if (isset($params['id_product']) && $params['id_product'] > 0) {
            DB::getInstance()->delete('gmerchantfeedes_product_rewrites', 'id_product = ' . (int)$params['id_product']);
        }
    }

    /**
     * @param $params
     * @return false|string
     * @throws SmartyException
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (isset($params['id_product']) && !empty($params['id_product'])) ? (int)$params['id_product']
            : (int)Tools::getValue('id_product', 0);

        if (empty($id_product) && isset($params['request']) && method_exists($params['request'], 'get')) {
            $id_product = (int)$params['request']->get('id');
        }

        $fields = array(
            array(
                'group_name' => $this->l('Rewrite some product data fields for the Google Shopping Feed.'),
                'fields' => array(
                    array(
                        'type' => 'text',
                        'name' => 'gmerchantfeedes_title',
                        'field_name' => $this->l('Title')
                    ),
                    array(
                        'type' => 'textarea',
                        'rows' => 3,
                        'name' => 'gmerchantfeedes_short_description',
                        'field_name' => $this->l('Short description')
                    ),
                    array(
                        'type' => 'textarea',
                        'rows' => 15,
                        'name' => 'gmerchantfeedes_description',
                        'field_name' => $this->l('Description')
                    ),
                    array(
                        'type' => 'text',
                        'rows' => 15,
                        'lang' => false,
                        'name' => 'gmerchantfeedes_addition_code',
                        'field_name' => $this->l('Additional code')
                    )
                )
            )
        );

        $fields_value = ($id_product > 0) ? self::getExtraValueForProductId($id_product) : array();
        $field_default_value = ($id_product > 0) ? self::getProductDefaultData($id_product) : array();

        $this->context->smarty->assign(
            array(
                'defaultFormLanguage' => $this->context->language->id,
                'languages' => $this->context->language->getLanguages(),
                'fields' => $fields,
                'fields_value' => $fields_value,
                'field_default_value' => $field_default_value,
                'with_submit' => (version_compare(_PS_VERSION_, '1.7.0.0', '<')),
                'back_url' => $this->context->link->getAdminLink('AdminProducts', true)
            )
        );

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/product_field.tpl');
    }

    private static function getProductDefaultData($id_product = 0, $id_lang = 0)
    {
        $dataPrepared = array();

        $values = DB::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'product_lang` 
            WHERE `id_product` = ' . (int)$id_product . (($id_lang > 0) ? ' AND `id_lang`=' . (int)$id_lang : ''));

        foreach ($values as $value) {
            $dataPrepared[$value['id_lang']] = [
                'default_title' => (new self)->clearHtmlTags($value['name']),
                'default_short_description' => (new self)->clearHtmlTags($value['description_short']),
                'default_description' => (new self)->clearHtmlTags($value['description'])
            ];
        }

        return $dataPrepared;
    }

    /**
     * @param int $id_product
     * @param false $onlyFromDB
     * @param int $id_lang
     * @return array|string
     * @throws PrestaShopDatabaseException
     */
    protected static function getExtraValueForProductId($id_product = 0, $onlyFromDB = false, $id_lang = 0)
    {
        $dataPrepared = array();

        $values = DB::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_product_rewrites` 
            WHERE `id_product` = ' . (int)$id_product . (($id_lang > 0) ? ' AND `id_lang`=' . (int)$id_lang : ''));
        if ($onlyFromDB) {
            return (count($values) === 1) ? $values[0] : $values;
        }

        foreach ($values as $value) {
            $dataPrepared[$value['id_lang']] = [
                'gmerchantfeedes_title' => $value['title'],
                'gmerchantfeedes_short_description' => $value['short_description'],
                'gmerchantfeedes_description' => $value['description']
            ];

            $dataPrepared['gmerchantfeedes_addition_code'] = $value['addition_code'];
        }

        return $dataPrepared;
    }


    /**
     * @return bool
     */
    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        Configuration::deleteByName('GMERCHANTFEEDS_ALT_JS');
        Configuration::deleteByName('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT');

        return parent::uninstall();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->alternativeJs) {
            $this->getJsAndCss();
        }

        self::$currentIndex = AdminController::$currentIndex
            . '&configure=' . urlencode($this->name)
            . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $this->postProcess();

        return $this->renderMainForm()
            . $this->renderOptionalForm()
            . $this->getVerifyBtn()
            . $this->renderDiscoverModules();
    }

    public function renderExampleForm()
    {
        $this->context->smarty->assign(
            array(
                'module_dir' => _MODULE_DIR_
            )
        );

        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/overrideInstallHelper.tpl');
    }

    public function renderOptionalForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'optionalConfigure';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOptionalEdit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'GMERCHANTFEEDS_ALT_JS' => Configuration::get('GMERCHANTFEEDS_ALT_JS'),
                'GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT' => Configuration::get('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT'),
            ),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(
            array(
                array(
                    'form' => array(
                        'legend' => array(
                            'title' => $this->l('Optional configures'),
                            'icon' => 'icon-edit'
                        ),
                        'input' => array(
                            array(
                                'type' => 'switch',
                                'tab' => 'other',
                                'label' => $this->l('Use ?id_country={countryID} on the product detail page', 'gmerchantfeedes'),
                                'desc' => $this->l('If you add ?id_country={countryID} at the end of the product page URL, 
                                                    the all data on the product page will be as for country with 
                                                    Id = {countryID}. Recognition by country IP will be ignored in 
                                                    this case.', 'gmerchantfeedes'),
                                'name' => 'GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT',
                                'is_bool' => true,
                                'values' => array(
                                    array(
                                        'id' => 'active_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled', 'gmerchantfeedes')
                                    ),
                                    array(
                                        'id' => 'active_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled', 'gmerchantfeedes')
                                    )
                                )
                            ),
                            array(
                                'type' => 'html',
                                'tab' => 'general',
                                'name' => '',
                                'label' => '',
                                'col' => 8,
                                'html_content' => $this->renderExampleForm()
                            ),
                            array(
                                'type' => 'switch',
                                'label' => $this->l('Alternative JavaScript path'),
                                'name' => 'GMERCHANTFEEDS_ALT_JS',
                                'is_bool' => true,
                                'values' => array(
                                    array(
                                        'id' => 'js_alternative_switch_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled', 'gmerchantfeedes')
                                    ),
                                    array(
                                        'id' => 'js_alternative_switch_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled', 'gmerchantfeedes')
                                    )
                                )
                            ),
                        ),
                        'submit' => array(
                            'title' => $this->l('Save')
                        )
                    ),
                )
            )
        );
    }

    public function getVerifyBtn()
    {
        $linkToUpdateTables = AdminController::$currentIndex . '&configure=' . urlencode($this->name)
            . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&verifyTables';

        $linkToResetHooks = AdminController::$currentIndex . '&configure=' . urlencode($this->name)
            . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&resetHooks';

        $this->smarty->assign(array(
            'linkToUpdate' => $linkToUpdateTables,
            'linkToResetHooks' => $linkToResetHooks
        ));

        return $this->display($this->name, 'tableVerifyTablesBtn.tpl');
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
        $helper->submit_action = 'submitGMerchantFeedESModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->alternativeJs) {
            return;
        }

        $this->getJsAndCss();
    }

    private function getJsAndCss()
    {
        if (Tools::getValue('module_name') == $this->name
            || Tools::getValue('configure') == $this->name) {
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    public function generationCSVList()
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        header('Content-type: text/tab-separated-values');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->table . '_' . date('Y-m-d_His') . '.tsv"');

        $id_feed = (int)Tools::getValue('key');
        $feed = new GMerchantFeedConfig($id_feed);
        if (!Validate::isLoadedObject($feed)) {
            return false;
        }

        $configuration = $feed->getFields(true);
        $id_lang = (int)$configuration['select_lang'];
        $this->preparePriceChangeRules([
            'price_change' => $configuration['price_change'],
            'price_change_type' => $configuration['price_change_type']
        ]);

        if (isset($configuration['instance_of_tax'])) {
            switch ($configuration['instance_of_tax']) {
                case '0':
                    $this->useTax = true;
                    if (Group::getPriceDisplayMethod((int)Configuration::get('PS_GUEST_GROUP'))) {
                        $this->useTax = false;
                    }
                    break;
                case '1':
                    $this->useTax = true;
                    break;
                default:
                    $this->useTax = false;
                    break;
            }
        }

        $excludeIdsFromFile = $this->getCsvExcludeFile($id_feed);
        if (isset($excludeIdsFromFile['ids']) && is_array($excludeIdsFromFile['ids'])) {
            $configuration['excludeIdsFromFile'] = array_map(function ($id_product) {
                if (is_numeric($id_product) && $id_product > 0) {
                    return (int)$id_product;
                }
            }, $excludeIdsFromFile['ids']);
            unset($excludeIdsFromFile);
        }

        $products_list = $this->getProductsByConf($configuration, $id_lang, $configuration['taxonomy_language']);
        if (empty($products_list)) {
            return;
        }

//        store code |  itemid |  price | availability | quantity
        $fields_list = array(
            [
                'field' => 'store_code',
                'label' => 'store code'
            ],
            [
                'field' => 'id',
                'label' => 'id'
            ],
            [
                'field' => 'price',
                'label' => 'price'
            ],
            [
                'field' => 'availability',
                'label' => 'availability'
            ],
            [
                'field' => 'quantity',
                'label' => 'quantity'
            ]
        );

        if ($configuration['show_sale_price']) {
            array_push($fields_list, [
                'field' => 'sale_price',
                'label' => 'sale price'
            ]);
        }

        $fd = fopen('php://output', 'wb');
        $headers = array();
        foreach ($fields_list as $key => $datas) {
            $headers[] = Tools::htmlentitiesDecodeUTF8($datas['label']);
        }

        $text_delimiter = chr(127);
        fputcsv($fd, $headers, "\t", $text_delimiter);

        foreach ($products_list as $productItem) {
            $content = $this->getItemCSV($productItem, $configuration, $id_lang);

            if ($content) {
                if (isset($content['with_attributes']) && isset($content['data']) && $content['data']) {
                    foreach ($content['data'] as $datum) {
                        $qtyColumns = count($datum);
                        if (is_array($datum) && ($qtyColumns >= 4 || $qtyColumns <= 5)) {
                            fputcsv($fd, $datum, "\t", $text_delimiter);
                        }
                    }
                } else if (is_array($content) && (count($content) >= 4 || count($content) <= 5)) {
                    fputcsv($fd, $content, "\t", $text_delimiter);
                }
            }
        }

        @fclose($fd);
        die;
    }

    public function getItemCSV($product, $param, $id_lang, $attribute_flag = false, $attribute_param = array(), $attribute_group = array())
    {
        $context = Context::getContext();
        $context->language = new Language($id_lang);
        $context->currency = new Currency((int)$param['id_currency'], (int)$id_lang);
        $_product = new Product((int)$product['id_product'], true, (int)$id_lang, null, $context);
        $price = 0;

        if (empty($this->categoryNames)) {
            foreach (Category::getAllCategoriesName(null, (int)$id_lang) as $categoryName) {
                $this->categoryNames[$categoryName['id_category']] = $categoryName['name'];
            }
        }

        $rewriteExtraFields = self::getExtraValueForProductId((int)$product['id_product'], true, $id_lang);
        if ($rewriteExtraFields && is_array($rewriteExtraFields) && count($rewriteExtraFields)) {
            $product['name'] = (isset($rewriteExtraFields['title']) && !empty($rewriteExtraFields['title']))
                ? $rewriteExtraFields['title']
                : $product['name'];
            switch ($param['type_description']) {
                case 1:
                    $product['desc'] = (isset($rewriteExtraFields['short_description']) && !empty($rewriteExtraFields['short_description']))
                        ? $rewriteExtraFields['short_description']
                        : $product['desc'];
                    break;
                case 2:
                    break;
                case 3:
                case 4:
                    $shortDeskTemp = (isset($rewriteExtraFields['short_description']) && !empty($rewriteExtraFields['short_description']))
                        ? $rewriteExtraFields['short_description']
                        : $_product->description_short;
                    $deskTemp = (isset($rewriteExtraFields['description']) && !empty($rewriteExtraFields['description']))
                        ? $rewriteExtraFields['description']
                        : $_product->description;
                    $product['desc'] = $shortDeskTemp . ' ' . $deskTemp . (($param['type_description'] == 4) ? ' ' . $_product->meta_description : '');
                    break;
                default:
                    $product['desc'] = (isset($rewriteExtraFields['description']) && !empty($rewriteExtraFields['description']))
                        ? $rewriteExtraFields['description']
                        : $product['desc'];
                    break;
            }
        }

        $combination_qty = $_product->quantity;
        if ($attribute_flag && $attribute_group['id_group'] > 0) {
            $combination = new Combination($attribute_group['id_group']);
            $combination_qty = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute_group['id_group']);
            if (isset($param['filter_qty_from']) && $param['filter_qty_from'] != 0) {
                if ($combination_qty < (int)$param['filter_qty_from']) {
                    return '';
                }
            }

            if ($combination_qty <= 0 && isset($param['rule_out_of_stock']) && $param['rule_out_of_stock'] > 0) {
                $defaultStockRule = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
                if ($param['rule_out_of_stock'] == 1) {
                    //denied
                    if ($defaultStockRule == 2) {
                        if (!($_product->out_of_stock == 0 || $_product->out_of_stock == 2)) {
                            return '';
                        }
                    } elseif ($_product->out_of_stock != 0) {
                        return '';
                    }
                } elseif ($param['rule_out_of_stock'] == 2) {
                    // allowed
                    if ($defaultStockRule == 1) {
                        if (!($_product->out_of_stock == 1 || $_product->out_of_stock == 2)) {
                            return '';
                        }
                    } elseif ($_product->out_of_stock != 1) {
                        return '';
                    }
                }
            }

            $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
            if ((bool)$param['export_non_available'] === true && $PS_STOCK_MANAGEMENT) {
                if ($combination_qty <= 0) {
                    return '';
                }
            }


            unset($combination);
        }

        if ($attribute_flag) {
            if (((int)$combination_qty > 0) || !$param['export_product_quantity']) {
                $availability = "in stock";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = ($param['param_order_out_of_stock_sys']) ? (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') : false;
                $current_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $availability = "out of stock";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $availability = "preorder " . $datetime1->format("c");
                        } else {
                            $availability = "in stock";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $availability = "preorder " . $datetime1->format("c");
                            } else {
                                $availability = "in stock";
                            }
                        } else {
                            $availability = "out of stock";
                        }
                    }
                } else {
                    $availability = "out of stock";
                }
            }
        } else {
            if (((int)$_product->quantity > 0) || !$param['export_product_quantity']) {
                $availability = "in stock";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = ($param['param_order_out_of_stock_sys']) ? (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') : false;
                $current_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $availability = "out of stock";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $availability = "preorder " . $datetime1->format("c");
                        } else {
                            $availability = "in stock";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $availability = "preorder " . $datetime1->format("c");
                            } else {
                                $availability = "in stock";
                            }
                        } else {
                            $availability = "out of stock";
                        }
                    }
                } else {
                    $availability = "out of stock";
                }
            }
        }

        $csv_entry = array();
        if ($param['export_attributes'] > 0 && !$attribute_flag) {
            $attribute_report = false;
            $get_attribute_lists = array();

            if (isset($param['get_attribute_size[]']) && is_array($param['get_attribute_size[]']) && count($param['get_attribute_size[]']) > 0) {
                foreach ($param['get_attribute_size[]'] as $get_attribute_size) {
                    if (!in_array($get_attribute_size, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_size;
                    }
                }
            }
            if (isset($param['get_attribute_color[]']) && is_array($param['get_attribute_color[]']) && count($param['get_attribute_color[]']) > 0) {
                foreach ($param['get_attribute_color[]'] as $get_attribute_color) {
                    if (!in_array($get_attribute_color, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_color;
                    }
                }
            }
            if (isset($param['get_attribute_pattern[]']) && is_array($param['get_attribute_pattern[]']) && count($param['get_attribute_pattern[]']) > 0) {
                foreach ($param['get_attribute_pattern[]'] as $get_attribute_pattern) {
                    if (!in_array($get_attribute_pattern, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_pattern;
                    }
                }
            }
            if (isset($param['get_attribute_material[]']) && is_array($param['get_attribute_material[]']) && count($param['get_attribute_material[]']) > 0) {
                foreach ($param['get_attribute_material[]'] as $get_attribute_material) {
                    if (!in_array($get_attribute_material, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_material;
                    }
                }
            }

            if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                foreach ($param['custom_attribute'] as $customAttrParam) {
                    $get_attribute_lists[] = $customAttrParam['id_attribute'];
                }
            }

            $attr = array();
            $onlyMainAttribute = ($param['export_attributes_only_first']) ? true : false;
            $attributesLists = GMerchantFeedConfig::getProductAttributesIdsOverride($_product->id, $onlyMainAttribute);

            if ($attributesLists && is_array($attributesLists) && count($attributesLists) > 0) {
                foreach ($attributesLists as $attributesList) {
                    $attribute_values = Product::getAttributesParams($_product->id, $attributesList['id_product_attribute']);
                    if ($attribute_values && is_array($attribute_values) && count($attribute_values) > 0) {
                        foreach ($attribute_values as $attribute_value) {
                            if (in_array($attribute_value['id_attribute_group'], $get_attribute_lists)) {
                                $attr[$attributesList['id_product_attribute']][$attribute_value['id_attribute_group']] = $attribute_value['name'];
                                $attribute_report = true;
                            }
                        }
                    }

                    $combination = new Combination((int)$attributesList['id_product_attribute']);

                    if (isset($this->gmfOptions['mpn']) && $this->gmfOptions['mpn']) {
                        $attr[$attributesList['id_product_attribute']]['mpn'] = $combination->mpn;
                    }

                    $attr[$attributesList['id_product_attribute']]['ean13'] = $combination->ean13;
                    $attr[$attributesList['id_product_attribute']]['isbn'] = (isset($combination->isbn)) ? $combination->isbn : '';
                    $attr[$attributesList['id_product_attribute']]['upc'] = $combination->upc;
                    $attr[$attributesList['id_product_attribute']]['reference'] = $combination->reference;
                    $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, (int)$attributesList['id_product_attribute'], $_product->id_supplier);
                    $attr[$attributesList['id_product_attribute']]['supplier_reference'] = $supplier_ref;

                    unset($combination);
                }
            }

            $attr_variables = array(
                'get_attribute_size[]',
                'get_attribute_color[]',
                'get_attribute_pattern[]',
                'get_attribute_material[]'
            );
            $rebuild_arr = array();

            foreach ($attr as $key => &$item) {
                $exists = false;
                foreach ($item as $key_in => $item_in) {
                    foreach ($attr_variables as $attr_variables_item) {
                        if (isset($param[$attr_variables_item]) && is_array($param[$attr_variables_item])
                            && in_array($key_in, $param[$attr_variables_item])) {
                            $m = 0;
                            $repeat_param = array();
                            foreach ($param[$attr_variables_item] as $get_attribute_size_val) {
                                if (isset($item[$get_attribute_size_val])) {
                                    $repeat_param[] = $get_attribute_size_val;
                                    ++$m;
                                }
                            }
                            if ($m == 0) {
                                unset($item);
                            } elseif ($m >= 2) {
                                $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                            }
                            $exists = true;
                        }
                    }

                    if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                        foreach ($param['custom_attribute'] as $customAttrParam) {
                            if ($key_in == $customAttrParam['id_attribute']) {
                                $m = 0;
                                $repeat_param = array();
                                if (isset($item[$customAttrParam['id_attribute']])) {
                                    $repeat_param[] = $customAttrParam['id_attribute'];
                                    ++$m;
                                }
                                if ($m == 0) {
                                    unset($item);
                                } elseif ($m >= 2) {
                                    $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                                }
                                $exists = true;
                            }
                        }
                    }
                }
                if (!$exists) {
                    unset($attr[$key]);
                }
            }

            if (count($rebuild_arr) > 0) {
                $unset_list = array();
                foreach ($rebuild_arr as $rebuild_arr_key => $rebuild_arr_item) {
                    foreach ($rebuild_arr_item['attr'] as $item_key => $item_attr) {
                        foreach ($rebuild_arr_item['data'] as $item_data_key => $item_data_val) {
                            if (!in_array($item_data_key, $rebuild_arr_item['attr']) || $item_data_key != $item_attr) {
                                $attr[$rebuild_arr_key . '-' . $item_attr][$item_data_key] = $item_data_val;
                                $unset_list[] = $rebuild_arr_key;
                            }
                        }
                    }
                }
                if (isset($unset_list) && count($unset_list) > 0) {
                    foreach ($unset_list as $item_unset) {
                        if (isset($attr[$item_unset])) {
                            unset($attr[$item_unset]);
                        }
                    }
                }
            }

            if ($attribute_report) {
                foreach ($attr as $gr_id => $itm) {
                    $csv_entry_new = $this->getItemCSV($product, $param, $id_lang, true, $itm, array('id_group' => $gr_id));
                    $csv_entry[] = $csv_entry_new;
                }

                return [
                    'with_attributes' => 1,
                    'data' => $csv_entry
                ];
            }
        }

        if (isset($param['id_suffix']) && !empty($param['id_suffix'])) {
            $identityKeyXml = str_replace('{ID}', (int)$product['id_product'], $param['id_suffix']);
            $identityKeyXml = str_replace('{reference}', $_product->reference, $identityKeyXml);
        } else {
            $identityKeyXml = (int)$product['id_product'];
        }

        if (isset($param['parts_payment_enabled']) && $param['parts_payment_enabled'] && isset($param['max_parts_payment'])
            && $param['max_parts_payment'] >= 1) {
//            $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
            $price_without_reduct = $this->getPriceStatic(
                $param,
                $product['id_product'],
                $this->useTax,
                null,
                6,
                null,
                false,
                false
            );

            $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
            $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');
            $productPrice = number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '');
            if ($param['interest_rates'] > 0 && $param['interest_rates'] <= 1) {
                $discountProductPrice = number_format((float)$price_without_reduct * (1 - $param['interest_rates']), 2, '.', '');
            }
        } else {
            if ($attribute_flag && $attribute_group['id_group'] > 0 && $param['export_attribute_prices'] == 1) {
                $getGroup = explode('-', $attribute_group['id_group']);
                $price = round($this->getPriceStatic($param, $product['id_product'], $this->useTax, (int)$getGroup[0]), 2);
                $price = $this->pricePrepare($price, $param['rounding_price']);
//                $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax, (int)$getGroup[0]), 2);
                $price_without_reduct = $this->getPriceStatic(
                    $param,
                    $product['id_product'],
                    $this->useTax,
                    (int)$getGroup[0],
                    6,
                    null,
                    false,
                    false
                );
                $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
                $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');
                $shippingPrice = $price;
                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    if ($param['export_sale'] && $param['export_sale'] == 2) {
                        return '';
                    }

                    if ($param['only_once_show_the_price']) {
                        $productPrice = number_format((float)$price, 2, '.', '');
                    } else {
                        $productPrice = number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '');
                        $discountProductPrice = number_format((float)$price, 2, '.', '');
                    }
                } else {
                    if ($param['export_sale'] && $param['export_sale'] == 1) {
                        return '';
                    }
                    $productPrice = number_format((float)$price, 2, '.', '');
                }
            } else {
                $price = round($this->getPriceStatic($param, $product['id_product'], $this->useTax), 2);
                $price = $this->pricePrepare($price, $param['rounding_price']);
                $shippingPrice = $price;
//              $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
                $price_without_reduct = $this->getPriceStatic(
                    $param,
                    $product['id_product'],
                    $this->useTax,
                    null,
                    6,
                    null,
                    false,
                    false
                );

                $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
                $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');
                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    if ($param['export_sale'] && $param['export_sale'] == 2) {
                        return '';
                    }

                    if ($param['only_once_show_the_price']) {
                        $productPrice = number_format((float)$price, 2, '.', '');
                    } else {
                        $productPrice = number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '');
                        $discountProductPrice = number_format((float)$price, 2, '.', '');
                    }
                } else {
                    if ($param['export_sale'] && $param['export_sale'] == 1) {
                        return '';
                    }
                    $productPrice = number_format((float)$price, 2, '.', '');
                }
            }
        }

        if ($param['exclude_discount_price_more'] > 0 && $price != $price_without_reduct && $price > 0 && $price_without_reduct > 0) {
            $discountDifference = (($price_without_reduct - $price) / $price_without_reduct) * 100;
            $discountDifference = number_format($discountDifference, 5);
            $excludeDiscountPriceMore = number_format($param['exclude_discount_price_more'], 5);
            if ($discountDifference > $excludeDiscountPriceMore) {
                return null;
            }
        }

        $response = [
            $param['store_code_inventory_feed'] ? $param['store_code_inventory_feed'] : '-',
            ($attribute_flag && $attribute_group['id_group'] > 0) ? $identityKeyXml . '-' . $attribute_group['id_group'] : $identityKeyXml,
            $productPrice,
            $availability,
            $combination_qty
        ];

        if ($param['show_sale_price']) {
            $response[] = $price;
        }

        return $response;
    }

    public function generationList($method = '')
    {
        $id_feed = (int)Tools::getValue('key');
        $feed = new GMerchantFeedConfig($id_feed);
        if (!Validate::isLoadedObject($feed)) {
            return false;
        }

        $configuration = $feed->getFields(true);

        $generate_path_file = $this->createFeedPath((int)$configuration['id_gmerchantfeedes']);

        $this->preparePriceChangeRules([
            'price_change' => $configuration['price_change'],
            'price_change_type' => $configuration['price_change_type']
        ]);

        if (isset($configuration['instance_of_tax'])) {
            switch ($configuration['instance_of_tax']) {
                case '0':
                    $this->useTax = true;
                    if (Group::getPriceDisplayMethod((int)Configuration::get('PS_GUEST_GROUP'))) {
                        $this->useTax = false;
                    }
                    break;
                case '1':
                    $this->useTax = true;
                    break;
                default:
                    $this->useTax = false;
                    break;
            }
        }

        if (isset($configuration['shipping_weight_format'])) {
            switch ($configuration['shipping_weight_format']) {
                case 0:
                    $this->shipping_weight_format = 3;
                    break;
                case 1:
                    $this->shipping_weight_format = 2;
                    break;
                case 2:
                    $this->shipping_weight_format = 0;
                    break;
            }
        }

        $shop_url = Tools::getCurrentUrlProtocolPrefix()
            . $this->context->shop->domain_ssl
            . $this->context->shop->physical_uri;
        $id_lang = (int)$configuration['select_lang'];
        $image_convert = '';
        $imageType = ImageType::getImagesTypes();
        foreach ($imageType as $item) {
            if ($item['id_image_type'] == $configuration['type_image']) {
                $image_convert = $item['name'];
                break;
            }
        }

        $excludeIdsFromFile = $this->getCsvExcludeFile($id_feed);
        if (isset($excludeIdsFromFile['ids']) && is_array($excludeIdsFromFile['ids'])) {
            $configuration['excludeIdsFromFile'] = array_map(function ($id_product) {
                if (is_numeric($id_product) && $id_product > 0) {
                    return (int)$id_product;
                }
            }, $excludeIdsFromFile['ids']);
            unset($excludeIdsFromFile);
        }

        $products_list = $this->getProductsByConf($configuration, $id_lang, $configuration['taxonomy_language']);
        // Google Shopping XML
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $xml .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">' . "\n\n";
        $xml .= '<title><![CDATA[' . $configuration['name'] . ']]></title>' . "\n";
        $xml .= '<link rel="self" href="' . $shop_url . '"/>' . "\n";
        $xml .= '<updated>' . date('Y-m-d') . 'T' . date('H:i:s') . 'Z</updated>' . "\n";

        $googleObject = fopen($generate_path_file, 'w');
        fwrite($googleObject, pack("CCC", 0xef, 0xbb, 0xbf));
        fwrite($googleObject, $xml);

        foreach ($products_list as $product_item) {
            $xml_entry = $this->getItemXML($product_item, $configuration, $id_lang, $image_convert);
            if ($xml_entry) {
                fwrite($googleObject, $xml_entry);
            }
        }

        $xml = '' . "\n" . '</feed>';
        fwrite($googleObject, $xml);
        fclose($googleObject);
//        @chmod($googleObject, 0777);

        if ($method != 'only_rebuild') {
            $download_file_name = Date('m-d-y') . '_google';
            header('Content-disposition: attachment; filename="' . $download_file_name . '.xml"');
            header('Content-Type: text/xml');
            readfile($generate_path_file);
        } else {
            echo $this->l('All done.');
        }

        exit();
    }

    /**
     * @param $param
     */
    private function preparePriceChangeRules($param)
    {
        $this->changePriceRules['change_up'] = ($param['price_change'] > 0);

        if (!$this->changePriceRules['change_up']) {
            $param['price_change'] = (float)($param['price_change'] * -1);
        }

        if ($param['price_change_type'] === self::$priceChangeTypes['value']) {
            $this->changePriceRules['amount'] = ($param['price_change'] > 0)
                ? (float)$param['price_change']
                : 0;
        } else {
            $this->changePriceRules['percent'] = ($param['price_change'] >= 0 && (!$this->changePriceRules['change_up'] && $param['price_change'] <= 100
                    || $this->changePriceRules['change_up']))
                ? (float)$param['price_change']
                : 0;
        }
    }

    /**
     * @param $price
     * @param bool $roundingPrice
     * @return float|int|mixed
     */
    private function pricePrepare($price, $roundingPrice = false)
    {
        $priceChanged = 0;
        if (isset($this->changePriceRules['amount']) && ($this->changePriceRules['amount'] <= $price
                && !$this->changePriceRules['change_up'] || $this->changePriceRules['change_up'])) {
            $priceChanged = ($this->changePriceRules['change_up'])
                ? $price + $this->changePriceRules['amount']
                : $price - $this->changePriceRules['amount'];
        } elseif (isset($this->changePriceRules['percent']) && $this->changePriceRules['percent'] > 0
            && ($this->changePriceRules['percent'] <= 100 && !$this->changePriceRules['change_up']
                || $this->changePriceRules['change_up'])) {
            $changeRange = $price / 100 * $this->changePriceRules['percent'];
            $priceChanged = ($this->changePriceRules['change_up'])
                ? $price + $changeRange
                : $price - $changeRange;
        }

        if ($roundingPrice) {
            $pricePrepare = ($priceChanged > 0) ? $priceChanged : $price;

            return ($pricePrepare < 1) ? 1 : round($pricePrepare, 0);
        }

        return ($priceChanged > 0) ? $priceChanged : $price;
    }

    protected function getItemXML($product, $param, $id_lang, $image_type, $attribute_flag = false, $attribute_param = array(), $attribute_group = array())
    {
        $context = Context::getContext();
        $context->language = new Language($id_lang);
        $context->currency = new Currency((int)$param['id_currency'], (int)$id_lang);
        $_product = new Product((int)$product['id_product'], true, (int)$id_lang, null, $context);
        if (empty($this->categoryNames)) {
            foreach (Category::getAllCategoriesName(null, (int)$id_lang) as $categoryName) {
                $this->categoryNames[$categoryName['id_category']] = $categoryName['name'];
            }
        }

        $rewriteExtraFields = self::getExtraValueForProductId((int)$product['id_product'], true, $id_lang);
        $param['additionalCode'] = (isset($rewriteExtraFields['addition_code']) && !empty($rewriteExtraFields['addition_code'])) ? $rewriteExtraFields['addition_code'] : '';

        if ($rewriteExtraFields && is_array($rewriteExtraFields) && count($rewriteExtraFields)) {
            $product['name'] = (isset($rewriteExtraFields['title']) && !empty($rewriteExtraFields['title']))
                ? $rewriteExtraFields['title']
                : $product['name'];
            switch ($param['type_description']) {
                case 1:
                    $product['desc'] = (isset($rewriteExtraFields['short_description']) && !empty($rewriteExtraFields['short_description']))
                        ? $rewriteExtraFields['short_description']
                        : $product['desc'];
                    break;
                case 2:
                    break;
                case 3:
                case 4:
                    $shortDeskTemp = (isset($rewriteExtraFields['short_description']) && !empty($rewriteExtraFields['short_description']))
                        ? $rewriteExtraFields['short_description']
                        : $_product->description_short;
                    $deskTemp = (isset($rewriteExtraFields['description']) && !empty($rewriteExtraFields['description']))
                        ? $rewriteExtraFields['description']
                        : $_product->description;
                    $product['desc'] = $shortDeskTemp . ' ' . $deskTemp . (($param['type_description'] == 4) ? ' ' . $_product->meta_description : '');
                    break;
                default:
                    $product['desc'] = (isset($rewriteExtraFields['description']) && !empty($rewriteExtraFields['description']))
                        ? $rewriteExtraFields['description']
                        : $product['desc'];
                    break;
            }
        }

        $price = 0;
        $shippingPrice = 0;
        $combination_weight = 0;
        $combination_qty = $_product->quantity;
        if ($attribute_flag && $attribute_group['id_group'] > 0) {
            $combination = new Combination($attribute_group['id_group']);
            $combination_weight = $combination->weight;
            $combination_qty = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute_group['id_group']);
            if (isset($param['filter_qty_from']) && $param['filter_qty_from'] != 0) {
                if ($combination_qty < (int)$param['filter_qty_from']) {
                    return '';
                }
            }

            if ($combination_qty <= 0 && isset($param['rule_out_of_stock']) && $param['rule_out_of_stock'] > 0) {
                $defaultStockRule = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
                if ($param['rule_out_of_stock'] == 1) {
                    //denied
                    if ($defaultStockRule == 2) {
                        if (!($_product->out_of_stock == 0 || $_product->out_of_stock == 2)) {
                            return '';
                        }
                    } elseif ($_product->out_of_stock != 0) {
                        return '';
                    }
                } elseif ($param['rule_out_of_stock'] == 2) {
                    // allowed
                    if ($defaultStockRule == 1) {
                        if (!($_product->out_of_stock == 1 || $_product->out_of_stock == 2)) {
                            return '';
                        }
                    } elseif ($_product->out_of_stock != 1) {
                        return '';
                    }
                }
            }

            $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
            if ((bool)$param['export_non_available'] === true && $PS_STOCK_MANAGEMENT) {
                if ($combination_qty <= 0) {
                    return '';
                }
            }

            unset($combination);
        }

        $globalGeneralPrice = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ));

        $continue_item = false;
        $xml_entry = '';

        if ($param['export_attributes'] > 0 && !$attribute_flag) {
            $attribute_report = false;
            $get_attribute_lists = array();

            if (isset($param['get_attribute_size[]']) && is_array($param['get_attribute_size[]']) && count($param['get_attribute_size[]']) > 0) {
                foreach ($param['get_attribute_size[]'] as $get_attribute_size) {
                    if (!in_array($get_attribute_size, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_size;
                    }
                }
            }
            if (isset($param['get_attribute_color[]']) && is_array($param['get_attribute_color[]']) && count($param['get_attribute_color[]']) > 0) {
                foreach ($param['get_attribute_color[]'] as $get_attribute_color) {
                    if (!in_array($get_attribute_color, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_color;
                    }
                }
            }
            if (isset($param['get_attribute_pattern[]']) && is_array($param['get_attribute_pattern[]']) && count($param['get_attribute_pattern[]']) > 0) {
                foreach ($param['get_attribute_pattern[]'] as $get_attribute_pattern) {
                    if (!in_array($get_attribute_pattern, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_pattern;
                    }
                }
            }
            if (isset($param['get_attribute_material[]']) && is_array($param['get_attribute_material[]']) && count($param['get_attribute_material[]']) > 0) {
                foreach ($param['get_attribute_material[]'] as $get_attribute_material) {
                    if (!in_array($get_attribute_material, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_material;
                    }
                }
            }

            if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                foreach ($param['custom_attribute'] as $customAttrParam) {
                    $get_attribute_lists[] = $customAttrParam['id_attribute'];
                }
            }

            $attr = array();
            $onlyMainAttribute = ($param['export_attributes_only_first']) ? true : false;
            $attributesLists = GMerchantFeedConfig::getProductAttributesIdsOverride($_product->id, $onlyMainAttribute);

            if ($attributesLists && is_array($attributesLists) && count($attributesLists) > 0) {
                foreach ($attributesLists as $attributesList) {
                    $attribute_values = Product::getAttributesParams($_product->id, $attributesList['id_product_attribute']);
                    if ($attribute_values && is_array($attribute_values) && count($attribute_values) > 0) {
                        foreach ($attribute_values as $attribute_value) {
                            if (in_array($attribute_value['id_attribute_group'], $get_attribute_lists)) {
                                $attr[$attributesList['id_product_attribute']][$attribute_value['id_attribute_group']] = $attribute_value['name'];
                                $attribute_report = true;
                            }
                        }
                    }

                    $combination = new Combination((int)$attributesList['id_product_attribute']);

                    if (isset($this->gmfOptions['mpn']) && $this->gmfOptions['mpn']) {
                        $attr[$attributesList['id_product_attribute']]['mpn'] = $combination->mpn;
                    }

                    $attr[$attributesList['id_product_attribute']]['ean13'] = $combination->ean13;
                    $attr[$attributesList['id_product_attribute']]['isbn'] = (isset($combination->isbn)) ? $combination->isbn : '';
                    $attr[$attributesList['id_product_attribute']]['upc'] = $combination->upc;
                    $attr[$attributesList['id_product_attribute']]['reference'] = $combination->reference;
                    $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, (int)$attributesList['id_product_attribute'], $_product->id_supplier);
                    $attr[$attributesList['id_product_attribute']]['supplier_reference'] = $supplier_ref;

                    unset($combination);
                }
            }

            $attr_variables = array(
                'get_attribute_size[]',
                'get_attribute_color[]',
                'get_attribute_pattern[]',
                'get_attribute_material[]'
            );
            $rebuild_arr = array();

            foreach ($attr as $key => &$item) {
                $exists = false;
                foreach ($item as $key_in => $item_in) {
                    foreach ($attr_variables as $attr_variables_item) {
                        if (isset($param[$attr_variables_item]) && is_array($param[$attr_variables_item])
                            && in_array($key_in, $param[$attr_variables_item])) {
                            $m = 0;
                            $repeat_param = array();
                            foreach ($param[$attr_variables_item] as $get_attribute_size_val) {
                                if (isset($item[$get_attribute_size_val])) {
                                    $repeat_param[] = $get_attribute_size_val;
                                    ++$m;
                                }
                            }
                            if ($m == 0) {
                                unset($item);
                            } elseif ($m >= 2) {
                                $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                            }
                            $exists = true;
                        }
                    }

                    if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                        foreach ($param['custom_attribute'] as $customAttrParam) {
                            if ($key_in == $customAttrParam['id_attribute']) {
                                $m = 0;
                                $repeat_param = array();
                                if (isset($item[$customAttrParam['id_attribute']])) {
                                    $repeat_param[] = $customAttrParam['id_attribute'];
                                    ++$m;
                                }
                                if ($m == 0) {
                                    unset($item);
                                } elseif ($m >= 2) {
                                    $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                                }
                                $exists = true;
                            }
                        }
                    }
                }
                if (!$exists) {
                    unset($attr[$key]);
                }
            }

            if (count($rebuild_arr) > 0) {
                $unset_list = array();
                foreach ($rebuild_arr as $rebuild_arr_key => $rebuild_arr_item) {
                    foreach ($rebuild_arr_item['attr'] as $item_key => $item_attr) {
                        foreach ($rebuild_arr_item['data'] as $item_data_key => $item_data_val) {
                            if (!in_array($item_data_key, $rebuild_arr_item['attr']) || $item_data_key != $item_attr) {
                                $attr[$rebuild_arr_key . '-' . $item_attr][$item_data_key] = $item_data_val;
                                $unset_list[] = $rebuild_arr_key;
                            }
                        }
                    }
                }
                if (isset($unset_list) && count($unset_list) > 0) {
                    foreach ($unset_list as $item_unset) {
                        if (isset($attr[$item_unset])) {
                            unset($attr[$item_unset]);
                        }
                    }
                }
            }

            if ($attribute_report) {
                foreach ($attr as $gr_id => $itm) {
                    $xml_entry .= $this->getItemXML($product, $param, $id_lang, $image_type, true, $itm, array('id_group' => $gr_id));
                }
                return $xml_entry;
            }
        }

        $xml_entry .= '<entry>' . "\n";
        if (isset($param['id_suffix']) && !empty($param['id_suffix'])) {
            $identityKeyXml = str_replace('{ID}', (int)$product['id_product'], $param['id_suffix']);
            $identityKeyXml = str_replace('{reference}', $_product->reference, $identityKeyXml);
        } else {
            $identityKeyXml = (int)$product['id_product'];
        }

        if ($attribute_flag && $attribute_group['id_group'] > 0) {
            if (!isset($param['export_attributes_as_product'])
                || isset($param['export_attributes_as_product']) && !$param['export_attributes_as_product']) {
                $xml_entry .= '<g:item_group_id><![CDATA[' . $identityKeyXml . ']]></g:item_group_id>' . "\n";
            }

            $xml_entry .= '<g:id><![CDATA[' . $identityKeyXml . '-' . $attribute_group['id_group'] . ']]></g:id>' . "\n";
        } else {
            $xml_entry .= '<g:id><![CDATA[' . $identityKeyXml . ']]></g:id>' . "\n";
        }

        $replaceAttributeTitle = array();
        if ($attribute_flag && count($attribute_param) > 0) {
            if (isset($param['get_attribute_size[]']) && is_array($param['get_attribute_size[]']) && count($param['get_attribute_size[]']) > 0) {
                foreach ($param['get_attribute_size[]'] as $get_attribute_size) {
                    if (isset($attribute_param[$get_attribute_size]) && !empty($attribute_param[$get_attribute_size])) {
                        $replaceAttributeTitle['size'][] = $attribute_param[$get_attribute_size];
                    }
                }
            }
            if (isset($param['get_attribute_color[]']) && is_array($param['get_attribute_color[]']) && count($param['get_attribute_color[]']) > 0) {
                foreach ($param['get_attribute_color[]'] as $get_attribute_color) {
                    if (isset($attribute_param[$get_attribute_color]) && !empty($attribute_param[$get_attribute_color])) {
                        $replaceAttributeTitle['color'][] = $attribute_param[$get_attribute_color];
                    }
                }
            }
            if (isset($param['get_attribute_pattern[]']) && is_array($param['get_attribute_pattern[]']) && count($param['get_attribute_pattern[]']) > 0) {
                foreach ($param['get_attribute_pattern[]'] as $get_attribute_pattern) {
                    if (isset($attribute_param[$get_attribute_pattern]) && !empty($attribute_param[$get_attribute_pattern])) {
                        $replaceAttributeTitle['pattern'][] = $attribute_param[$get_attribute_pattern];
                    }
                }
            }
            if (isset($param['get_attribute_material[]']) && is_array($param['get_attribute_material[]']) && count($param['get_attribute_material[]']) > 0) {
                foreach ($param['get_attribute_material[]'] as $get_attribute_material) {
                    if (isset($attribute_param[$get_attribute_material]) && !empty($attribute_param[$get_attribute_material])) {
                        $replaceAttributeTitle['material'][] = $attribute_param[$get_attribute_material];
                    }
                }
            }
        }

        if (isset($param['title_suffix']) && !empty($param['title_suffix'])) {
            $new_name_field = str_replace('{title}', $product['name'], $param['title_suffix']);
            $new_name_field = str_replace('{manufacturer_name}', $product['manufacturer_name'], $new_name_field);

            $replace_size = (isset($replaceAttributeTitle['size']) && count($replaceAttributeTitle['size'])) ? join(', ', $replaceAttributeTitle['size']) : '';
            if ($param['suffix_attribute_title_set'] && preg_match_all("{size}", $new_name_field) && $replace_size) {
                $replace_size = $this->l('Size:', 'gmerchantfeedes') . $replace_size;
            }
            $new_name_field = str_replace('{size}', $replace_size, $new_name_field);

            $replace_color = (isset($replaceAttributeTitle['color']) && count($replaceAttributeTitle['color'])) ? join(', ', $replaceAttributeTitle['color']) : '';
            if ($param['suffix_attribute_title_set'] && preg_match_all("{color}", $new_name_field) && $replace_color) {
                $replace_color = $this->l('Color:', 'gmerchantfeedes') . $replace_color;
            }
            $new_name_field = str_replace('{color}', $replace_color, $new_name_field);

            $replace_pattern = (isset($replaceAttributeTitle['pattern']) && count($replaceAttributeTitle['pattern'])) ? join(', ', $replaceAttributeTitle['pattern']) : '';
            if ($param['suffix_attribute_title_set'] && preg_match_all("{pattern}", $new_name_field) && $replace_pattern) {
                $replace_pattern = $this->l('Pattern:', 'gmerchantfeedes') . $replace_pattern;
            }
            $new_name_field = str_replace('{pattern}', $replace_pattern, $new_name_field);

            $replace_material = (isset($replaceAttributeTitle['material']) && count($replaceAttributeTitle['material'])) ? join(', ', $replaceAttributeTitle['material']) : '';
            if ($param['suffix_attribute_title_set'] && preg_match_all("{material}", $new_name_field) && $replace_material) {
                $replace_material = $this->l('Material:', 'gmerchantfeedes') . $replace_material;
            }
            $new_name_field = str_replace('{material}', $replace_material, $new_name_field);

            $replace_category = (isset($this->categoryNames[$_product->id_category_default])) ? $this->categoryNames[$_product->id_category_default] : '';
            $new_name_field = str_replace('{main_product_category}', $replace_category, $new_name_field);

            $feature_rules = array();
            preg_match_all('/{feature:[^.*}]+}/i', $new_name_field, $feature_rules);
            if (is_array($feature_rules[0]) && count($feature_rules[0])) {
                $features = $_product->getFrontFeatures($id_lang);
                $featureGrouped = array();
                foreach ($features as $featureVal) {
                    $featureGrouped[$featureVal['id_feature']]['name'] = $featureVal['name'];
                    $featureGrouped[$featureVal['id_feature']]['values'][] = $featureVal['value'];
                }

                foreach ($feature_rules[0] as $random_meta_rule) {
                    $random_meta_rule = trim($random_meta_rule);
                    $items_for_random = str_replace('{random:', '', $random_meta_rule);
                    $items_for_random = mb_substr($items_for_random, 0, -1);
                    $items_for_random = explode(':', $items_for_random);

                    if (isset($featureGrouped[$items_for_random[1]])) {
                        $new_name_field = str_replace($random_meta_rule, (($param['suffix_feature_title_set']) ? $featureGrouped[$items_for_random[1]]['name'] . ':' : '') . join('|', $featureGrouped[$items_for_random[1]]['values']), $new_name_field);
                    }
                }

                foreach ($feature_rules[0] as $random_meta_rule) {
                    if (strpos($new_name_field, $random_meta_rule . ',') !== false) {
                        $new_name_field = str_replace($random_meta_rule . ',', '', $new_name_field);
                    } else {
                        $new_name_field = str_replace($random_meta_rule, '', $new_name_field);
                    }
                }
            }

            if (!empty($new_name_field)) {
                if ($param['modify_uppercase_title'] == true) {
                    $new_name_field = Tools::strtolower($new_name_field);
                    $new_name_field = Tools::ucfirst($new_name_field);
                }

                $xml_entry .= '<g:title><![CDATA[' . $new_name_field . ']]></g:title>' . "\n";
            } else {
                if ($param['modify_uppercase_title'] == true) {
                    $product['name'] = Tools::strtolower($product['name']);
                    $product['name'] = Tools::ucfirst($product['name']);
                }

                $xml_entry .= '<g:title><![CDATA[' . $product['name'] . ']]></g:title>' . "\n";
            }
        } else {
            if ($param['modify_uppercase_title'] == true) {
                $product['name'] = Tools::strtolower($product['name']);
                $product['name'] = Tools::ucfirst($product['name']);
            }

            $xml_entry .= '<g:title><![CDATA[' . $product['name'] . ']]></g:title>' . "\n";
        }

        if (isset($param['description_suffix']) && !empty($param['description_suffix'])) {
            $new_desc_field = str_replace('{description}', $product['desc'], $param['description_suffix']);
            $desc = $this->clearHtmlTags($new_desc_field);
            if ($param['description_crop'] == 1) {
                $desc = mb_substr($desc, 0, 5000);
            }
            if ($param['modify_uppercase_description']) {
                $desc = $this->mbStrToLowerAfterPoint($desc);
            }
            $xml_entry .= '<g:description><![CDATA[' . $this->clearHtmlTags($desc) . ']]></g:description>' . "\n";
        } else {
            $desc = $this->clearHtmlTags($product['desc']);
            if ($param['description_crop'] == 1) {
                $desc = mb_substr($desc, 0, 5000);
            }
            if ($param['modify_uppercase_description']) {
                $desc = $this->mbStrToLowerAfterPoint($desc);
            }
            $xml_entry .= '<g:description><![CDATA[' . $desc . ']]></g:description>' . "\n";
        }

        if ($attribute_flag && $attribute_group['id_group'] && $param['export_attribute_url']) {
            $productTLink = $this->context->link->getProductLink((int)($product['id_product']), $product['link_rewrite'], null, null, $id_lang, null, (int)$attribute_group['id_group'], false, false, true);
            $productTLinkExp = explode('#', $productTLink);
            $concatLink = array_shift($productTLinkExp) . $param['url_suffix'] . '#' . join('#', $productTLinkExp);
            $xml_entry .= '<g:link><![CDATA[' . $concatLink . ']]></g:link>' . "\n";
        } else {
            $xml_entry .= '<g:link><![CDATA[' . $this->context->link->getProductLink((int)($product['id_product']), $product['link_rewrite'], null, null, $id_lang) . $param['url_suffix'] . ']]></g:link>' . "\n";
        }

        $defaultImageInsert = true;
        if ($attribute_flag && $attribute_group['id_group'] > 0 && $param['export_attribute_images'] == 1) {
            $thisGroup = explode('-', $attribute_group['id_group']);
            $groupImages = Product::_getAttributeImageAssociations($thisGroup[0]);
            if (is_array($groupImages) && count($groupImages)) {
                $imagesTMP = Image::getImages((int)$id_lang, (int)$product['id_product']);
                $newGroupImages = array();
                foreach ($imagesTMP as $imageTMP) {
                    if (in_array($imageTMP['id_image'], $groupImages)) {
                        $newGroupImages[] = (int)$imageTMP['id_image'];
                    }
                }
                $insert_default = false;
                $limit = 10;
                foreach ($newGroupImages as $id_image) {
                    if (!is_numeric($id_image)) {
                        continue;
                    }
                    if (!$insert_default) {
                        $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . (int)$id_image, $image_type);
                        $xml_entry .= '<g:image_link><![CDATA[' . $image . ']]></g:image_link>' . "\n";
                        $insert_default = true;
                    } else {
                        if (isset($param['additional_image']) && ((bool)$param['additional_image']) == true) {
                            if (($limit--) <= 0) {
                                break;
                            }
                            $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . (int)$id_image, $image_type);
                            $xml_entry .= '<g:additional_image_link><![CDATA[' . $image . ']]></g:additional_image_link>' . "\n";
                        }
                    }
                    $defaultImageInsert = false;
                }
            }
        }

        if ($defaultImageInsert) {
            $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $product['id_image'], $image_type);
            $xml_entry .= '<g:image_link><![CDATA[' . $image . ']]></g:image_link>' . "\n";
            if (isset($param['additional_image']) && ((bool)$param['additional_image']) == true) {
                $images = Image::getImages((int)$id_lang, (int)$product['id_product']);
                $limit = 10;
                foreach ($images as $image) {
                    if ($image['cover'] == 1) {
                        continue;
                    }
                    if (($limit--) <= 0) {
                        break;
                    }
                    $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $image['id_image'], $image_type);
                    $xml_entry .= '<g:additional_image_link><![CDATA[' . $image . ']]></g:additional_image_link>' . "\n";
                }
            }
        }

        //interest_rates
        if (isset($param['parts_payment_enabled']) && $param['parts_payment_enabled'] && isset($param['max_parts_payment'])
            && $param['max_parts_payment'] >= 1) {
//            $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
            $price_without_reduct = $this->getPriceStatic(
                $param,
                $product['id_product'],
                $this->useTax,
                null,
                6,
                null,
                false,
                false
            );

            $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
            $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
            if ($param['interest_rates'] > 0 && $param['interest_rates'] <= 1) {
                $xml_entry .= '<g:sale_price>' . number_format((float)$price_without_reduct * (1 - $param['interest_rates']), 2, '.', '') . ' ' . $context->currency->iso_code . ' </g:sale_price>' . "\n";
            }
        } else {
            if ($attribute_flag && $attribute_group['id_group'] > 0 && $param['export_attribute_prices'] == 1) {
                $getGroup = explode('-', $attribute_group['id_group']);
                $price = round($this->getPriceStatic($param, $product['id_product'], $this->useTax, (int)$getGroup[0]), 2);
                $price = $this->pricePrepare($price, $param['rounding_price']);
//                $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax, (int)$getGroup[0]), 2);
                $price_without_reduct = $this->getPriceStatic(
                    $param,
                    $product['id_product'],
                    $this->useTax,
                    (int)$getGroup[0],
                    6,
                    null,
                    false,
                    false
                );

                $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
                $shippingPrice = $price;
                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    if ($param['export_sale'] && $param['export_sale'] == 2) {
                        return '';
                    }

                    if ($param['only_once_show_the_price']) {
                        $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                    } else {
                        $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                        $xml_entry .= '<g:sale_price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:sale_price>' . "\n";
                    }
                } else {
                    if ($param['export_sale'] && $param['export_sale'] == 1) {
                        return '';
                    }
                    $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
            } else {
                $price = round($this->getPriceStatic($param, $product['id_product'], $this->useTax), 2);
                $price = $this->pricePrepare($price, $param['rounding_price']);
                $shippingPrice = $price;
//              $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
                $price_without_reduct = $this->getPriceStatic(
                    $param,
                    $product['id_product'],
                    $this->useTax,
                    null,
                    6,
                    null,
                    false,
                    false
                );
                $price_without_reduct = $this->pricePrepare($price_without_reduct, $param['rounding_price']);
//                $price_without_reduct = number_format($price_without_reduct, 2, '.', '');

                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    if ($param['export_sale'] && $param['export_sale'] == 2) {
                        return '';
                    }

                    if ($param['only_once_show_the_price']) {
                        $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                    } else {
                        $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                        $xml_entry .= '<g:sale_price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:sale_price>' . "\n";
                    }
                } else {
                    if ($param['export_sale'] && $param['export_sale'] == 1) {
                        return '';
                    }
                    $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
            }
        }

        if ($param['exclude_discount_price_more'] > 0 && $price != $price_without_reduct && $price > 0 && $price_without_reduct > 0) {
            $discountDifference = (($price_without_reduct - $price) / $price_without_reduct) * 100;
            $discountDifference = number_format($discountDifference, 5);
            $excludeDiscountPriceMore = number_format($param['exclude_discount_price_more'], 5);
            if ($discountDifference > $excludeDiscountPriceMore) {
                return null;
            }
        }

        if (isset($param['parts_payment_enabled']) && $param['parts_payment_enabled'] && isset($param['max_parts_payment'])
            && $param['max_parts_payment'] >= 1) {
            $convPrice = (float)str_replace(' ', '', $price_without_reduct) / (int)$param['max_parts_payment'];
            $xml_entry .= '<g:installment>' . "\n";
            $xml_entry .= '<g:months>' . (int)$param['max_parts_payment'] . '</g:months>' . "\n";
            $xml_entry .= '<g:amount>' . number_format((float)str_replace(' ', '', $convPrice), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:amount>' . "\n";
            $xml_entry .= '</g:installment>' . "\n";
        }

        if ($attribute_flag) {
            if (((int)$combination_qty > 0) || !$param['export_product_quantity']) {
                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = ($param['param_order_out_of_stock_sys']) ? (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') : false;
                $current_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                            $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                        } else {
                            $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                                $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                            } else {
                                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                            }
                        } else {
                            $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                        }
                    }
                } else {
                    $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                }
            }
        } else {
            if (((int)$_product->quantity > 0) || !$param['export_product_quantity']) {
                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = ($param['param_order_out_of_stock_sys']) ? (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') : false;
                $current_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                            $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                        } else {
                            $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $current_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                                $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                            } else {
                                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                            }
                        } else {
                            $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                        }
                    }
                } else {
                    $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                }
            }
        }

        if (!$param['taxonomy_ref']) {
            if (!$param['taxonomy_ref']
                && isset($product['id_taxonomy'])
                && Validate::isInt($product['id_taxonomy'])
                && (int)$product['id_taxonomy'] > 0
                && isset($product['name_taxonomy'])
                && !empty($product['name_taxonomy'])
            ) {
                if (!$param['google_product_category_rewrite']) {
                    $xml_entry .= '<g:google_product_category><![CDATA[' . $product['name_taxonomy'] . ']]></g:google_product_category>' . "\n";
                } else {
                    $xml_entry .= '<g:google_product_category>' . $product['id_taxonomy'] . '</g:google_product_category>' . "\n";
                }
            }
//            else {
//                //  If you want to export only products with exists ref to "google_product_category"
//                $continue_item = true;
//            }
        }

        $fullpath = self::getProductPath((int)$_product->id_category_default, $_product->name, $param['product_title_in_product_type'], $context);
        $xml_entry .= '<g:product_type><![CDATA[' . $fullpath . ']]></g:product_type>' . "\n";

        /*  Brand :
         *  identifier_exists : если нет Brand И GTIN или MPN
         * 1.   Condition: new | - (опции в админке НЕ вы водить - работает по умолчанию:  если NEW и BRAND,GTIN,MPN === 0
         *  то identifier_exists == NO и в XML идет только 2 параметра CONDITION=NEW и identifier_exists=NO)
         * 2.   Brand!='' и (GTIN=='' и MPN =='') и Condition == NEW  ->  identifier_exists == NO | CONDITION=NEW
         *                                   и Condition != NEW  ->  identifier_exists == YES | CONDITION=(VARIABLE FROM CONDITION)
         * 3.   Brand!='' и (GTIN != '' ИЛИ MPN != '') ->  identifier_exists == YES   //
         * */
        $identifier_brand_exists = false;
        if (!empty($_product->manufacturer_name) && $param['brand_type'] == 'manufacturer') {
            $identifier_brand_exists = true;
            $xml_entry .= '<g:brand><![CDATA[' . $this->clearHtmlTags($_product->manufacturer_name) . ']]></g:brand>' . "\n";
        } elseif ($param['brand_type'] == 'supplier' && isset($_product->supplier_name) && !empty($_product->supplier_name)) {
            $identifier_brand_exists = true;
            $xml_entry .= '<g:brand><![CDATA[' . $this->clearHtmlTags($_product->supplier_name) . ']]></g:brand>' . "\n";
        }

        $identifier_gtin_exists = false;
        if (isset($param['gtin_type[]']) && is_array($param['gtin_type[]'])) {
            foreach ($param['gtin_type[]'] as $gtinTypeItem) {
                switch ($gtinTypeItem) {
                    case 'ean_13_jan':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['ean13'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['ean13'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->ean13) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->ean13 . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'upc':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['upc'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['upc'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->upc) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->upc . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'isbn':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['isbn'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['isbn'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->isbn) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->isbn . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'reference':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['reference'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['reference'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->reference) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->reference . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'supplier_reference':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['supplier_reference'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['supplier_reference'] . ']]></g:gtin>' . "\n";
                        } elseif (!$attribute_flag) {
                            $idProductAttribute = null;
                            $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, $idProductAttribute, $_product->id_supplier);
                            if ($supplier_ref && !empty($supplier_ref)) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:gtin><![CDATA[' . $supplier_ref . ']]></g:gtin>' . "\n";
                            }
                        }
                        break;
                    case 'mpn':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['mpn'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['mpn'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->mpn) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->mpn . ']]></g:gtin>' . "\n";
                        }
                        break;
                }
                if ($identifier_gtin_exists) {
                    break;
                }
            }
        }
        $identifier_only_gtin_exists = $identifier_gtin_exists;

        if (!$identifier_gtin_exists || $param['mpn_force_on']) {
            if (isset($param['mpn_type[]']) && is_array($param['mpn_type[]'])) {
                foreach ($param['mpn_type[]'] as $mpnTypeItem) {
                    switch ($mpnTypeItem) {
                        case 'ean_13_jan':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['ean13'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['ean13'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->ean13) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->ean13 . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'upc':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['upc'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['upc'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->upc) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->upc . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'isbn':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['isbn'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['isbn'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->isbn) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->isbn . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'reference':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['reference'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['reference'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->reference) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->reference . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'supplier_reference':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['supplier_reference'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['supplier_reference'] . ']]></g:mpn>' . "\n";
                            } elseif (!$attribute_flag) {
                                $idProductAttribute = null;
                                $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, $idProductAttribute, $_product->id_supplier);
                                if ($supplier_ref && !empty($supplier_ref)) {
                                    $identifier_gtin_exists = true;
                                    $xml_entry .= '<g:mpn><![CDATA[' . $supplier_ref . ']]></g:mpn>' . "\n";
                                }
                            }
                            break;
                        case 'mpn':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['mpn'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['mpn'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->mpn) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->mpn . ']]></g:mpn>' . "\n";
                            }
                            break;
                    }
                    if ($identifier_gtin_exists) {
                        break;
                    }
                }
            }
        }

        if (!$param['disable_tag_identifier_exists']) {
            if (!$param['identifier_exists_mpn'] && $identifier_gtin_exists || $identifier_only_gtin_exists) {
                $xml_entry .= '<g:identifier_exists>yes</g:identifier_exists>' . "\n";
            } else {
                $xml_entry .= '<g:identifier_exists>no</g:identifier_exists>' . "\n";
            }
        }

        if (isset($_product->condition) && in_array($_product->condition, array('new', 'used', 'refurbished'))) {
            $xml_entry .= '<g:condition><![CDATA[' . $_product->condition . ']]></g:condition>' . "\n";
        }
        if ($param['export_feature'] > 0) {
            $features_exp = array();
            $flists = $_product->getFeatures();
            if ($flists && is_array($flists) && count($flists) > 0) {
                foreach ($flists as $flist) {
                    $val_lists = FeatureValue::getFeatureValueLang($flist['id_feature_value']);
                    if ($val_lists && is_array($val_lists) && count($val_lists) > 0) {
                        foreach ($val_lists as $val_list) {
                            $features_exp[$flist['id_feature']][$val_list['id_lang']] = $val_list['value'];
                        }
                    }
                }
            }
            if (isset($param['get_features_gender']) && !empty($param['get_features_gender']) && Validate::isInt($param['get_features_gender'])
                && isset($features_exp[$param['get_features_gender']][$id_lang]) && !empty($features_exp[$param['get_features_gender']][$id_lang])
            ) {
                $xml_entry .= '<g:gender>' . $features_exp[$param['get_features_gender']][$id_lang] . '</g:gender>' . "\n";
            }
            if (isset($param['get_features_age_group']) && !empty($param['get_features_age_group']) && Validate::isInt($param['get_features_age_group'])
                && isset($features_exp[$param['get_features_age_group']][$id_lang]) && !empty($features_exp[$param['get_features_age_group']][$id_lang])
            ) {
                $xml_entry .= '<g:age_group>' . $features_exp[$param['get_features_age_group']][$id_lang] . '</g:age_group>' . "\n";
            }
        }

        if ($attribute_flag && count($attribute_param) > 0) {
            if (isset($param['get_attribute_size[]']) && is_array($param['get_attribute_size[]']) && count($param['get_attribute_size[]']) > 0) {
                foreach ($param['get_attribute_size[]'] as $get_attribute_size) {
                    if (isset($attribute_param[$get_attribute_size]) && !empty($attribute_param[$get_attribute_size])) {
                        $xml_entry .= '<g:size><![CDATA[' . $attribute_param[$get_attribute_size] . ']]></g:size>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_color[]']) && is_array($param['get_attribute_color[]']) && count($param['get_attribute_color[]']) > 0) {
                foreach ($param['get_attribute_color[]'] as $get_attribute_color) {
                    if (isset($attribute_param[$get_attribute_color]) && !empty($attribute_param[$get_attribute_color])) {
                        $xml_entry .= '<g:color><![CDATA[' . $attribute_param[$get_attribute_color] . ']]></g:color>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_pattern[]']) && is_array($param['get_attribute_pattern[]']) && count($param['get_attribute_pattern[]']) > 0) {
                foreach ($param['get_attribute_pattern[]'] as $get_attribute_pattern) {
                    if (isset($attribute_param[$get_attribute_pattern]) && !empty($attribute_param[$get_attribute_pattern])) {
                        $xml_entry .= '<g:pattern><![CDATA[' . $attribute_param[$get_attribute_pattern] . ']]></g:pattern>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_material[]']) && is_array($param['get_attribute_material[]']) && count($param['get_attribute_material[]']) > 0) {
                foreach ($param['get_attribute_material[]'] as $get_attribute_material) {
                    if (isset($attribute_param[$get_attribute_material]) && !empty($attribute_param[$get_attribute_material])) {
                        $xml_entry .= '<g:material><![CDATA[' . $attribute_param[$get_attribute_material] . ']]></g:material>' . "\n";
                    }
                }
            }

            if (isset($param['custom_attribute']) && is_array($param['custom_attribute'])
                && count($param['custom_attribute'])) {
                foreach ($param['custom_attribute'] as $customAttr) {
                    if (isset($attribute_param[$customAttr['id_attribute']]) && !empty($attribute_param[$customAttr['id_attribute']])) {
                        $xml_entry .= '<' . $customAttr['unit'] . '><![CDATA[' . $attribute_param[$customAttr['id_attribute']] . ']]></' . $customAttr['unit'] . '>' . "\n";
                    }
                }
            }
        }

        $language = new Language($id_lang);
        $id_country = (int)$param['id_country'];
        if (!isset($param['id_country']) || !Validate::isInt($param['id_country'])) {
            $id_country = Country::getByIso($language->iso_code);
        }

        $country = new Country($id_country);
        $shipping_lists = array();

        if (!empty($param['id_reference[]'])) {
            foreach ($param['id_reference[]'] as $referenceIds) {
                $carrierBtRef = Carrier::getCarrierByReference((int)$referenceIds);
                if (Validate::isLoadedObject($carrierBtRef)) {
                    $carrierList[] = $carrierBtRef->id;
                }
            }
        } else {
            $carrierList = $param['id_carrier[]'];
        }

        if (is_array($carrierList) && count($carrierList) > 0) {
            foreach ($carrierList as $id_carrier) {
                if (Carrier::checkCarrierZone($id_carrier, (int)$country->id_zone)) {
                    $carrier = new Carrier($id_carrier);
                    if ((int)$carrier->shipping_method == 1) {
                        $weight = round($_product->weight, 3);
                        if ($attribute_flag && $attribute_group['id_group'] > 0 && is_numeric($combination_weight) &&
                            $combination_weight > 0) {
                            $weight += round($combination_weight, 3);
                        }
                        $shipping = round($carrier->getDeliveryPriceByWeight($weight, (int)$country->id_zone) * (1 + ((float)Tax::getCarrierTaxRate((int)$carrier->id) / 100)), 3);
                        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($id_default_currency != (int)$context->currency->id) {
                            $shipping = Tools::convertPrice($shipping, (int)$context->currency->id);
                        }
                        $shipping_lists[] = array(
                            'country' => $country->iso_code,
                            'service' => (!empty($carrier->name)) ? $carrier->name : 'Standard',
                            'price' => ((!empty($shipping) && is_numeric($shipping) && $shipping > 0) ? $shipping : 0),
                            'shipping_external' => $carrier->shipping_external,
                            'shipping_handling' => $carrier->shipping_handling,
                            'id_reference' => $carrier->id_reference,
                            'is_free' => $carrier->is_free,
                            'id_carrier' => $id_carrier
                        );
                    } elseif ((int)$carrier->shipping_method == 2) {
//                        $shippingPrice = round($_product->getPriceStatic((int)$product['id_product'], $this->useTax), 2);

                        $shipping = round($carrier->getDeliveryPriceByPrice($shippingPrice, (int)$country->id_zone, (int)$context->currency->id) * (1 + ((float)Tax::getCarrierTaxRate((int)$carrier->id) / 100)), 2);
                        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($id_default_currency != (int)$context->currency->id) {
                            $shipping = Tools::convertPrice($shipping, (int)$context->currency->id);
                        }
                        $shipping_lists[] = array(
                            'country' => $country->iso_code,
                            'service' => (!empty($carrier->name)) ? $carrier->name : 'Standard',
                            'price' => ((!empty($shipping) && is_numeric($shipping) && $shipping > 0) ? $shipping : 0),
                            'shipping_external' => $carrier->shipping_external,
                            'shipping_handling' => $carrier->shipping_handling,
                            'id_reference' => $carrier->id_reference,
                            'is_free' => $carrier->is_free,
                            'id_carrier' => $id_carrier
                        );
                    }
                }
            }
        }

        $incTax = false;
        if (isset($id_country) && $id_country > 0) {
            $addr = new Address();
            $addr->id_country = (int)$id_country;
            $tax_manager = TaxManagerFactory::getManager($addr, Product::getIdTaxRulesGroupByIdProduct((int)$_product->id, $context));
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $incTax = true;
        }

        $additional_shipping_cost = 0;
        if ($param['use_additional_shipping_cost']) {
            $additional_shipping_cost = (isset($_product->additional_shipping_cost)
                && !empty($_product->additional_shipping_cost)
                && is_numeric($_product->additional_shipping_cost)) ? (float)$_product->additional_shipping_cost : 0;

            $additional_shipping_cost = Tools::convertPrice((float)$additional_shipping_cost, Currency::getCurrencyInstance((int)$context->currency->id));
            if (isset($incTax) && $incTax) {
                $additional_shipping_cost = $product_tax_calculator->addTaxes($additional_shipping_cost);
            }
        }

        if (count($shipping_lists) > 0) {
            $carrierHandleSelected = DB::getInstance()->executeS('SELECT ic.id_carrier
            FROM `' . _DB_PREFIX_ . 'carrier` AS ic INNER JOIN `' . _DB_PREFIX_ . 'product_carrier` AS pc
                ON(ic.id_reference = pc.id_carrier_reference)
                    WHERE pc.id_product=' . (int)$_product->id . ' AND ic.deleted != 1');
            if (is_array($carrierHandleSelected) && count($carrierHandleSelected) > 0) {
                $carrierHandleSelected = array_map(function ($data) {
                    return (int)$data['id_carrier'];
                }, $carrierHandleSelected);
            }

            foreach ($shipping_lists as $shipping_list) {
                if (isset($carrierHandleSelected) && is_array($carrierHandleSelected)
                    && count($carrierHandleSelected) && !in_array($shipping_list['id_carrier'], $carrierHandleSelected)) {
                    continue;
                }
                if (!$shipping_list['is_free']) {
                    $shipping_list['price'] += $additional_shipping_cost;
                    if (isset($globalGeneralPrice['PS_SHIPPING_HANDLING']) && $shipping_list['shipping_handling']) {
                        $handingPrice = Tools::convertPrice((float)$globalGeneralPrice['PS_SHIPPING_HANDLING'], Currency::getCurrencyInstance((int)$context->currency->id));
                        if (isset($incTax) && $incTax) {
                            $handingPrice = $product_tax_calculator->addTaxes($handingPrice);
                        }

                        $shipping_list['price'] += $handingPrice;
                    }

                    if (isset($globalGeneralPrice['PS_SHIPPING_FREE_PRICE'])
                        && (float)$globalGeneralPrice['PS_SHIPPING_FREE_PRICE'] > 0) {
                        $additional_shipping_cost_free = Tools::convertPrice((float)$globalGeneralPrice['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int)$context->currency->id));
                        if ($price >= $additional_shipping_cost_free) {
                            $shipping_list['price'] = 0;
                        }
                    }

                    $weight = round($_product->weight, 3);
                    if ($attribute_flag && $attribute_group['id_group'] > 0 && is_numeric($combination_weight) &&
                        $combination_weight > 0) {
                        $weight += round($combination_weight, 3);
                    }

                    if (isset($globalGeneralPrice['PS_SHIPPING_FREE_WEIGHT'])
                        && $weight >= (float)$globalGeneralPrice['PS_SHIPPING_FREE_WEIGHT']
                        && (float)$globalGeneralPrice['PS_SHIPPING_FREE_WEIGHT'] > 0
                        && !empty($weight) && $weight > 0) {
                        $shipping_list['price'] = 0;
                    }
                }

                $xml_entry .= '<g:shipping>' . "\n";
                $xml_entry .= "\t" . '<g:country>' . $shipping_list['country'] . '</g:country>' . "\n";
                $xml_entry .= "\t" . '<g:service><![CDATA[' . $shipping_list['service'] . ']]></g:service>' . "\n";
                if (!empty($shipping_list['price'])) {
                    $xml_entry .= "\t" . '<g:price>' . number_format($shipping_list['price'], 2, '.', ' ') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                } else {
                    $xml_entry .= "\t" . '<g:price> 0.00 ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
                $xml_entry .= '</g:shipping>' . "\n";
            }
        }

        if (!empty($_product->weight) && is_numeric($_product->weight) && $_product->weight > 0
            || $attribute_flag && $combination_weight > 0) {
            $weight = round($_product->weight, 3);
            if ($attribute_flag && $attribute_group['id_group'] > 0 && is_numeric($combination_weight) &&
                $combination_weight > 0) {
                $weight += round($combination_weight, 3);
            }

            $weight = Tools::ps_round($weight, $this->shipping_weight_format, PS_ROUND_UP);
            $xml_entry .= '<g:shipping_weight>' . number_format($weight, $this->shipping_weight_format, '.', '') . ' ' . Configuration::get('PS_WEIGHT_UNIT') . '</g:shipping_weight>' . "\n";
        }

        if ($param['export_width'] && $_product->width > 0) {
            $xml_entry .= '<g:shipping_width>' . number_format($_product->width, 2, '.', '') . (!empty($param['export_width_inp']) ? ' ' : '') . $param['export_width_inp'] . '</g:shipping_width>' . "\n";
        }

        if ($param['export_height'] && $_product->height > 0) {
            $xml_entry .= '<g:shipping_height>' . number_format($_product->height, 2, '.', '') . (!empty($param['export_height_inp']) ? ' ' : '') . $param['export_height_inp'] . '</g:shipping_height>' . "\n";
        }

        if ($param['export_depth'] && $_product->depth > 0) {
            $xml_entry .= '<g:shipping_length>' . number_format($_product->depth, 2, '.', '') . (!empty($param['export_depth_inp']) ? ' ' : '') . $param['export_depth_inp'] . '</g:shipping_length>' . "\n";
        }

        $featureLists = $_product::getFrontFeaturesStatic((int)$context->language->id, (int)$product['id_product']);
        if (isset($param['features_custom_mod']) && is_array($param['features_custom_mod'])
            && count($param['features_custom_mod']) && $featureLists && count($featureLists) > 0) {
            foreach ($param['features_custom_mod'] as $custom_param) {
                if (empty($custom_param['id_feature'])) {
                    continue;
                }
                foreach ($featureLists as $featureItem) {
                    if ($featureItem['id_feature'] == $custom_param['id_feature']) {
                        $xml_entry .= '<' . str_replace(' ', '_', $custom_param['unit']) . '><![CDATA[' . $this->clearHtmlTags($featureItem['value']) . ']]></' . str_replace(' ', '_', $custom_param['unit']) . '>' . "\n";
                    }
                }
            }
        }

        if (isset($param['custom_product_row']) && is_array($param['custom_product_row']) && count($param['custom_product_row'])) {
            foreach ($param['custom_product_row'] as $custom_param) {
                $xmlEntityPrepare = $this->getCustomProductValue($custom_param['id_param'], $_product, [
                    'title' => (!empty($new_name_field)) ? $new_name_field : $product['name'],
                    'price' => ($price) ? $price : 0,
                    'attribute_flag' => $attribute_flag,
                    'attribute_group' => $attribute_group,
                    'combination_qty' => $combination_qty,
                    'rounding_price' => $param['rounding_price'],
                    'url_suffix' => $param['url_suffix'],
                    'id_lang' => $id_lang
                ], $context);

                if ($xmlEntityPrepare) {
                    $xml_entry .= '<' . str_replace(' ', '_', $custom_param['unit']) . '><![CDATA[' . $this->clearHtmlTags($xmlEntityPrepare) . ']]></' . str_replace(' ', '_', $custom_param['unit']) . '>' . "\n";
                }
            }
        }

        if (!empty($param['additional_each_product'])) {
            $additionalLines = explode(PHP_EOL, $param['additional_each_product']);
            if (is_array($additionalLines) && count($additionalLines)) {
                foreach ($additionalLines as $additionalLine) {
                    $additionalLine = trim($additionalLine);
                    if (!empty($additionalLine)) {
                        $xml_entry .= $additionalLine . "\n";
                    }
                }
            }
        }

        if (isset($param['additionalCode']) && !empty($param['additionalCode'])) {
            $xml_entry .= $param['additionalCode'] . "\n";
        }


        $xml_entry .= '</entry>' . "\n";

        unset($context);
        unset($_product);
        return (!$continue_item) ? $xml_entry : '';
    }

    /**
     * @param string $fieldKey
     * @param $_product
     * @param $options
     * @param $context
     * @return mixed|string
     */
    private function getCustomProductValue($fieldKey, $_product, $options, $context = null)
    {
        switch ($fieldKey) {
            case 'product_title':
                return $options['title'];
            case 'product_id':
                return ($options['attribute_flag'] && $options['attribute_group']['id_group'] > 0)
                    ? $_product->id . '-' . $options['attribute_group']['id_group'] : $_product->id;
            case 'product_category_title':
                return ($this->categoryNames[$_product->id_category_default]) ? $this->categoryNames[$_product->id_category_default] : '-';
            case 'product_price':
                return $options['price'];
            case 'product_cost_price':
                $costPrice = (isset($_product->wholesale_price) && $_product->wholesale_price > 0) ? number_format((float)$_product->wholesale_price, 2, '.', '') : 0;
                return $costPrice . ' ' . $context->currency->iso_code;
            case 'product_qty':
                return $options['combination_qty'];
            case 'product_condition':
                return $_product->condition;
            case 'product_brand':
                return $_product->manufacturer_name;
            case 'product_supplier':
                return ($_product->supplier_name) ? $_product->supplier_name : '';
            case 'price_per_unit':
                return ($_product->unit_price) ? number_format($this->pricePrepare($_product->unit_price, $options['rounding_price']), 2) . ' ' . $context->currency->iso_code . ' ' . $_product->unity : '';
            case 'product_url':
                if ($options['attribute_flag'] && $options['attribute_group']['id_group']) {
                    $productTLink = $this->context->link->getProductLink((int)($_product->id), $_product->link_rewrite, null, null, $options['id_lang'], null, (int)$options['attribute_group']['id_group'], false, false, true);
                    $productTLinkExp = explode('#', $productTLink);
                    return array_shift($productTLinkExp) . $options['url_suffix'] . '#' . join('#', $productTLinkExp);
                }

                return $this->context->link->getProductLink((int)($_product->id), $_product->link_rewrite, null, null, $options['id_lang']) . $options['url_suffix'];
            default:
                return '';
        }
    }

    /**
     * Returns product price.
     * @param array additional conf to current product
     * @param int $id_product Product id
     * @param bool $usetax With taxes or not (optional)
     * @param int|null $id_product_attribute product attribute id (optional).
     *                                       If set to false, do not apply the combination price impact.
     *                                       NULL does apply the default combination price impact
     * @param int $decimals Number of decimals (optional)
     * @param int|null $divisor Useful when paying many time without fees (optional)
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $usereduc Set if the returned amount will include reduction
     * @param int $quantity Required for quantity discount application (default value: 1)
     * @param bool $force_associated_tax DEPRECATED - NOT USED Force to apply the associated tax.
     *                                   Only works when the parameter $usetax is true
     * @param int|null $id_customer Customer ID (for customer group reduction)
     * @param int|null $id_cart Cart ID. Required when the cookie is not accessible
     *                          (e.g., inside a payment module, a cron task...)
     * @param int|null $id_address Customer address ID. Required for price (tax included)
     *                             calculation regarding the guest localization
     * @param null $specific_price_output If a specific price applies regarding the previous parameters,
     *                                    this variable is filled with the corresponding SpecificPrice object
     * @param bool $with_ecotax insert ecotax in price output
     * @param bool $use_group_reduction
     * @param Context $context
     * @param bool $use_customer_price
     *
     * @return float Product price
     */
    public static function getPriceStatic(
        $param,
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true,
        $id_customization = null
    )
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $cur_cart = $context->cart;

        if ($divisor !== null) {
            Tools::displayParameterAsDeprecated('divisor');
        }

        if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }

        // Initializations
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }

        // If there is cart in context or if the specified id_cart is different from the context cart id
        if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart)) {
            /*
            * When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
            * When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
            * When called from the back office, cart ID can be inexistant
            */
            if (!$id_cart && !isset($context->employee)) {
                die(Tools::displayError());
            }
            $cur_cart = new Cart($id_cart);
            // Store cart in context to avoid multiple instantiations in BO
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $cur_cart;
            }
        }

        $cart_quantity = 0;
        if ((int)$id_cart) {
            $cache_id = 'gmerchantfeedes::getPriceStatic_' . (int)$id_product . '-' . (int)$id_cart;
            if (!Cache::isStored($cache_id) || ($cart_quantity = Cache::retrieve($cache_id) != (int)$quantity)) {
                $sql = 'SELECT SUM(`quantity`)
                FROM `' . _DB_PREFIX_ . 'cart_product`
                WHERE `id_product` = ' . (int)$id_product . '
                AND `id_cart` = ' . (int)$id_cart;
                $cart_quantity = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                Cache::store($cache_id, $cart_quantity);
            } else {
                $cart_quantity = Cache::retrieve($cache_id);
            }
        }

        $id_currency = Validate::isLoadedObject($context->currency) ? (int)$context->currency->id : (int)Configuration::get('PS_CURRENCY_DEFAULT');

        if (!$id_address && Validate::isLoadedObject($cur_cart)) {
            $id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        // retrieve address informations
        $address = Address::initialize($id_address, true);

        if (!empty((int)$param['id_country'])) {
            $id_country = (int)$param['id_country'];
        } else {
            $id_country = (int)$address->id_country;
        }

        $id_state = (int)$address->id_state;
        $zipcode = $address->postcode;

        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }

        if ($usetax != false
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $usetax = false;
        }

        if (is_null($id_customer) && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }

        $return = Product::priceCalculation(
            $context->shop->id,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $cart_quantity,
            $id_customization
        );

        return $return;
    }

    public function renderDiscoverModules()
    {
        $this->smarty->assign(array(
            'this_module' => $this,
            'documentation_link' => 'https://extra-solutions.com/readme_en-GMCPRO.pdf',
            'documentation_text' => $this->l('Module documentation'),
            'labels' => array(
                'like' => $this->l('Do you like the [1]%s[/1] module?'),
                'yes' => $this->l('Yes'),
                'no' => $this->l('No'),
                'title' => $this->l('Promote your products'),
                'discover' => $this->l('Discover')
            )
        ));

        return $this->display($this->name, 'modules.tpl');
    }
}
