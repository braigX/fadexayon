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
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminWkBulkSampleController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->list_no_link = true;

        parent::__construct();
        $this->display = 'add';
    }

    public function renderForm()
    {
        // Displaying previously selected filters when error is occurring
        if (Tools::getValue('wk_id_categories')) {
            $selectedCategories = Tools::getValue('wk_id_categories');
        } else {
            $selectedCategories = [];
        }
        if (Tools::getValue('id_manufacturers')) {
            $this->fields_value['id_manufacturers[]'] = Tools::getValue('id_manufacturers');
        }
        if (Tools::getValue('id_suppliers')) {
            $this->fields_value['id_suppliers[]'] = Tools::getValue('id_suppliers');
        }

        $categoryTree = new HelperTreeCategories('plan-categories');
        $categoryTree->setAttribute('is_category_filter', (bool) '1')
            ->setInputName('wk_id_categories')
            ->setRootCategory(Category::getRootCategory()->id)
            ->setSelectedCategories($selectedCategories)
            ->setUseCheckBox(true);

        $objManufacture = new Manufacturer();
        $manufactureData = $objManufacture->getManufacturers();

        $objSupplier = new Supplier();
        $supplierCoreData = $objSupplier->getSuppliers();

        // Sample settings form prefilled values if any error
        $maxCartQty = Tools::getValue('max_cart_qty');
        $priceType = Tools::getValue('wk_sample_price_type');
        $priceTax = Tools::getValue('wk_sample_tax');
        $samplePrice = Tools::getValue('wk_sample_customprice');
        $samplePercent = Tools::getValue('wk_sample_percent');
        $sampleAmount = Tools::getValue('wk_sample_amount');
        $weight = Tools::getValue('wk_sample_weight');
        $sampleSettings = [
            'max_cart_qty' => $maxCartQty,
            'wk_sample_price_type' => $priceType,
            'wk_sample_tax' => $priceTax,
            'wk_sample_customprice' => $samplePrice,
            'wk_sample_percent' => $samplePercent,
            'wk_sample_amount' => $sampleAmount,
            'wk_sample_weight' => $weight,
        ];
        $wkLanguages = Language::getLanguages();
        foreach ($wkLanguages as $wkLang) {
            $sampleSettings['wk_sample_btn_label_' . $wkLang['id_lang']] = Tools::getValue(
                'wk_sample_btn_label_' . $wkLang['id_lang'],
                'Buy sample'
            );
            $sampleSettings['wk_sample_desc_' . $wkLang['id_lang']] = Tools::getValue(
                'wk_sample_desc_' . $wkLang['id_lang']
            );
        }

        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $currentDirs = $this->context->smarty->getTemplateDir();
            $currentDirs[] = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/';
            $this->context->smarty->setTemplateDir($currentDirs);
        }

        $this->fields_form = [
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Product name pattern', get_class()),
                    'name' => 'search_product_name',
                    'col' => '12',
                    'desc' => $this->module->l('Enter at least three character for search product name pattern.', get_class()),
                    'form_group_class' => 'wk-product-search',
                ],
                [
                    'label' => $this->module->l('Categories', get_class()),
                    'type' => 'categories_select',
                    'name' => 'id_categories[]',
                    'class' => 'plan-categories',
                    'col' => '12',
                    'category_tree' => $categoryTree->render(),
                    'form_group_class' => 'wk-product-search',
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Brand', get_class()),
                    'name' => 'id_manufacturers[]',
                    // 'class' => 'chosen chosen-select',
                    'class' => 'fixed-width-xxxl',
                    'multiple' => true,
                    'col' => '12',
                    'placeholder' => $this->module->l('Choose brand', get_class()),
                    'form_group_class' => 'wk-product-search',
                    'options' => [
                        'query' => $manufactureData,
                        'id' => 'id_manufacturer',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Suppliers', get_class()),
                    'name' => 'id_suppliers[]',
                    'search' => true,
                    'multiple' => true,
                    'col' => '12',
                    'placeholder' => $this->module->l('Choose suppliers', get_class()),
                    'form_group_class' => 'wk-product-search',
                    'options' => [
                        'query' => $supplierCoreData,
                        'id' => 'id_supplier',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'html',
                    'name' => 'wk_add_product_search_button',
                    'html_content' => $this->productSearchButton(),
                ],
            ],
        ];

        $this->context->smarty->assign(
            [
                'productSearchBlock' => parent::renderForm(),
                'wk_languages' => $wkLanguages,
                'wk_carrier_list' => Carrier::getCarriers(
                    $this->context->language->id,
                    true,
                    false,
                    false,
                    null,
                    Carrier::ALL_CARRIERS
                ),
                'wk_sample' => $sampleSettings,
                'tableName' => $this->table,
                'wk_language' => $this->context->language->id,
                'ajaxLoader' => _MODULE_DIR_ . $this->module->name . '/views/img/loader.gif',
                'WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS' => WkSampleProduct::WK_SAMPLE_BUTTON_TITLE_ALLOWED_CHARACTERS,
            ]
        );

        $objModule = Module::getInstanceByName('wksampleproduct');
        /** @var WkSampleProduct $objModule */
        $configTab = $objModule->getConfigTabTemplate('AdminWkBulkSample');

        return $configTab . $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/filtered-products.tpl'
        );
    }

    public function productSearchButton()
    {
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/product-search-button.tpl'
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSampleSettingsBulk')) {
            $products = Tools::getValue('searchedProduct');
            $maxCartQty = (int) Tools::getValue('max_cart_qty');
            $priceType = (int) Tools::getValue('wk_sample_price_type');
            $priceTax = (int) Tools::getValue('wk_sample_tax');
            $samplePrice = Tools::getValue('wk_sample_customprice');
            $samplePercent = Tools::getValue('wk_sample_percent');
            $sampleAmount = Tools::getValue('wk_sample_amount');
            $weight = (float) Tools::getValue('wk_sample_weight');

            if (empty($products)) {
                $this->errors[] = $this->module->l('Please select some products to assign sample settings.', get_class(), get_class());
            }
            if ($maxCartQty && !Validate::isUnsignedInt($maxCartQty)) {
                $this->errors[] = $this->module->l('Maximum quantity in cart should be a positive number.', get_class());
            }
            if ($priceType == 2) {
                if (is_array($products)) {
                    foreach ($products as $prod) {
                        $productObj = new Product($prod, false, $this->context->language->id);
                        $price = $productObj->price;
                        if ((int) $price < (int) $sampleAmount) {
                            $this->errors[] = $this->module->l('The deducted amount of ', get_class()) . $productObj->name . $this->module->l(' should be less than or equal to its price.', get_class());
                        }
                    }
                }
                // Amount
                if (Tools::getValue('wk_sample_amount') == '') {
                    $this->errors[] = $this->module->l('Please enter sample price deduction amount.', get_class());
                } elseif (!Validate::isUnsignedFloat($sampleAmount)) {
                    $this->errors[] = $this->module->l('Sample price deduction amount should be a positive number.', get_class());
                } elseif ($sampleAmount && !Validate::isPrice($sampleAmount)) {
                    $this->errors[] = $this->module->l('Invalid deduct fix amount.', get_class());
                }
            }
            if ($priceType == 3) {
                // Percent
                if (Tools::getValue('wk_sample_percent') == '') {
                    $this->errors[] = $this->module->l('Please enter sample price deduction percent.', get_class());
                } elseif ($samplePercent && !Validate::isPrice($samplePercent)) {
                    $this->errors[] = $this->module->l('Invalid Sample price deduction percent.', get_class());
                } elseif (!Validate::isUnsignedFloat($samplePercent)) {
                    $this->errors[] = $this->module->l('Sample price deduction percent should be a positive number.', get_class());
                } elseif ($samplePercent > 100) {
                    $this->errors[] = $this->module->l('Sample price deduction percent should be a number between 0-100.', get_class());
                }
            }
            if ($priceType == 4) {
                // Custom price
                if (Tools::getValue('wk_sample_customprice') == '') {
                    $this->errors[] = $this->module->l('Please enter sample custom price.', get_class());
                } elseif ($samplePrice && !Validate::isPrice($samplePrice)) {
                    $this->errors[] = $this->module->l('Invalid sample custom price.', get_class());
                } elseif ($samplePrice < 0) {
                    $this->errors[] = $this->module->l('Sample custom price should be a positive number.', get_class());
                }
            }
            foreach ($products as $prod) {
                $productObj = new Product($prod, false, $this->context->language->id);
                if (!Validate::isLoadedObject($productObj)) {
                    $this->errors[] = $this->module->l('Product does not exist.', get_class());
                }
            }

            if ($weight && !Validate::isUnsignedFloat($weight)) {
                $this->errors[] = $this->module->l('Weight should be a positive number.', get_class());
            }
            $allLanguages = Language::getLanguages();
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            $sampleTitles = [];
            $sampleDescs = [];
            foreach ($allLanguages as $lang) {
                $sampleTitle = trim(Tools::getValue('wk_sample_btn_label_' . $lang['id_lang']));
                if (trim(Tools::getValue('wk_sample_btn_label_' . $defaultLang)) != '') {
                    if (Tools::strlen($sampleTitle) > 32) {
                        $this->errors[] = $this->module->l('Please enter button label upto 32 characters', get_class()) . ' : (' . $lang['iso_code'] . ')' .
                        ' : (' . $lang['iso_code'] . ')';
                    } elseif (Tools::strlen($sampleTitle) && !Validate::isGenericName($sampleTitle)) {
                        $this->errors[] = $this->module->l('Button label is not valid', get_class()) . ' : (' . $lang['iso_code'] . ')';
                    } else {
                        if ($sampleTitle != '') {
                            $sampleTitles[$lang['id_lang']] = pSQL($sampleTitle);
                        } else {
                            $sampleTitles[$lang['id_lang']] = pSQL(trim(Tools::getValue('wk_sample_btn_label_' . $defaultLang)));
                        }
                    }
                    $sampleDesc = trim(Tools::getValue('wk_sample_desc_' . $lang['id_lang']));
                    if ($sampleDesc == '') {
                        $sampleDescs[$lang['id_lang']] = trim(preg_replace('/\s+/', ' ', trim(Tools::getValue('wk_sample_btn_label_' . $defaultLang))));
                    } elseif ($sampleDesc && Tools::strlen($sampleDesc) && !Validate::isCleanHtml($sampleDesc)) {
                        $this->errors[] = $this->module->l('Description is not valid.', get_class());
                    } else {
                        $sampleDescs[$lang['id_lang']] = trim(preg_replace('/\s+/', ' ', $sampleDesc));
                    }
                }
            }
            if (empty($this->errors)) {
                if ($priceType == 3) {
                    // If type is percent, then tax calculation will always be Tax-included
                    $priceTax = 1;
                }
                foreach ($products as $idProduct) {
                    $objSampleProductMap = new WkSampleProductMap();
                    $sampleProduct = $objSampleProductMap->getSampleProduct($idProduct, false);
                    if ($sampleProduct && $sampleProduct['id_sample_product']) {
                        $sample = new WkSampleProductMap((int) $sampleProduct['id_sample_product']);
                    } else {
                        $sample = new WkSampleProductMap();
                    }
                    unset($sampleProduct);
                    unset($objSampleProductMap);
                    $sample->id_product = (int) $idProduct;
                    $sample->id_product_attribute = (int) Product::getDefaultAttribute($idProduct);
                    $sample->max_cart_qty = (int) $maxCartQty;
                    $sample->price_type = (int) $priceType;
                    $sample->price_tax = (int) $priceTax;
                    $sample->amount = ($priceType == 3) ? (float) $samplePercent : (float) $sampleAmount;
                    $sample->price = (float) $samplePrice;
                    $sample->weight = (float) $weight;
                    $sample->button_label = $sampleTitles;
                    $sample->description = $sampleDescs;
                    $sample->active = (int) 1;
                    $sample->save();
                    // Carriers
                    $carrierList = Tools::getValue('wk_sample_carriers');
                    if (!is_array($carrierList)) {
                        $carrierList = [];
                    }
                    $sample->setSampleCarriers($carrierList, $sample->id_product, $this->context->shop->id);
                }

                $url = Context::getContext()->link->getAdminLink('AdminWkBulkSample', true, [], ['conf' => 4]);
                Tools::redirectAdmin($url);
            }
        }
        $this->display = 'add';

        return parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(
            [
                'wkSampleFilterProduct' => self::$currentIndex . '&token=' . $this->token,
                'popUpView' => true,
                'errorMsg' => [
                    'productNotFound' => $this->module->l('No product found for this filter.', get_class()),
                    'patternInvalid' => $this->module->l('Invalid search product.', get_class()),
                    'patternLength' => $this->module->l('Search product must be at least three character.', get_class()),
                    'oneFilterRequired' => $this->module->l('At least one filter is required.', get_class()),
                    'productRequired' => $this->module->l('Please select at least one product to apply sample settings.', get_class()),
                ],
                'chosenPlaceholder' => $this->module->l('Select some options', get_class()),
                'noMatchFound' => $this->module->l('No results match', get_class()),
            ]
        );
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/sampletabs.css');
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');

        $this->addJS(
            _PS_MODULE_DIR_ . $this->module->name . '/views/js/bulksample.js'
        );
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/bulksample.css');
    }

    public function ajaxProcessDisplayFilteredProducts()
    {
        $searchPattern = Tools::getValue('srcPattern');
        $idCategories = Tools::getValue('idCategories');
        $idManufacturers = Tools::getValue('idManufacturers');
        $idSuppliers = Tools::getValue('idSuppliers');
        $sampleProductObj = new WkSampleProductMap();
        $filteredProductsList = $sampleProductObj->getFilteredProducts(
            $searchPattern,
            $idCategories,
            $idManufacturers,
            $idSuppliers,
            $this->context->language->id
        );
        $finalFilteredProducts = [];
        if ($filteredProductsList) {
            foreach ($filteredProductsList as $product) {
                $objProduct = new Product($product['id_product'], false, $this->context->language->id);
                $combinationData['id_product'] = $product['id_product'];
                $combinationData['image'] = $this->getProductImage($product['id_product'], $objProduct->link_rewrite);
                $combinationData['name'] = $product['name'];
                $combinationData['combination'] = '';
                $combinationData['id_product_attribute'] = $objProduct->getDefaultIdProductAttribute();
                array_push($finalFilteredProducts, $combinationData);
            }
        }

        $this->context->smarty->assign(
            [
                'filteredList' => $finalFilteredProducts,
            ]
        );
        exit(
            $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/filter-product-list.tpl'
            )
        );
    }

    public function getProductImage($idProduct, $rewrite, $type = 'cart')
    {
        $idImage = 0;
        $idAttr = Product::getDefaultAttribute($idProduct);
        if ($idAttr > 0) {
            $image = Product::getCombinationImageById($idAttr, $this->context->language->id);
            if (!empty($image)) {
                $idImage = $image['id_image'];
            }
        }
        if ($idImage == 0) {
            $cover = Product::getCover($idProduct, $this->context);
            if (!empty($cover)) {
                $idImage = $cover['id_image'];
            }
        }
        if ($idImage > 0) {
            return $this->context->link->getImageLink(
                $rewrite,
                $idProduct . '-' . $idImage,
                ImageType::getFormattedName($type)
            );
        }
        $imgLink = $this->context->link->getBaseLink() . 'img/p/';
        $imgLink .= Language::getIsoById($this->context->language->id) . '.jpg';

        return $imgLink;
    }

    public function ajaxProcessSaveSampleFile()
    {
        $this->uploadFile();
    }

    public function ajaxProcessDeleteSample()
    {
        $this->deleteFile(true);
    }

    public function deleteFile($isDeleteAction = false)
    {
        $objSampleProduct = new WkSampleProductMap();
        if ($sampleFileName = $objSampleProduct->getSampleFileName(Tools::getValue('id_product'))) {
            $fileDir = _PS_MODULE_DIR_ . $this->module->name . '/views/samples/';
            if (Tools::strlen(trim($sampleFileName['sample_file']))
                && file_exists($fileDir . $sampleFileName['sample_file'])
            ) {
                unlink($fileDir . $sampleFileName['sample_file']);
            }
            if ($isDeleteAction) {
                $objSample = new WkSampleProductMap($sampleFileName['id_sample_product']);
                $objSample->sample_file = pSQL('');
                $objSample->save();
            }
        }
        if ($isDeleteAction) {
            $result = [
                'success' => 1,
                'text' => $this->module->l('Sample product deleted successfully.'),
            ];
            exit(json_encode($result));
        }
    }

    public function uploadFile()
    {
        $result = [
            'success' => 0,
            'text' => $this->module->l('Something went wrong.', get_class()),
        ];
        if (array_key_exists('pictureFile', $_FILES)) {
            // Check standard virtual available
            $idProduct = Tools::getValue('id_product');
            $product_download = new ProductDownload();
            if ($id_product_download = $product_download->getIdFromIdProduct($idProduct)) {
                $product_download = new ProductDownload($id_product_download);
                if ($product_download->id) {
                    $sampleFile = $_FILES['pictureFile'];
                    if ($sampleFile['size'] / 1000000 >= Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')) {
                        $result = [
                            'success' => 0,
                            'text' => $this->module->l('File is too large.', get_class()),
                        ];
                    }
                    $fileDir = _PS_MODULE_DIR_ . $this->module->name . '/views/samples';
                    if (!file_exists($fileDir)) {
                        @mkdir($fileDir . '/', 0777, true);
                    }
                    if (!file_exists($fileDir . '/index.php')) {
                        @copy(
                            _PS_MODULE_DIR_ . $this->module->name . '/index.php',
                            $fileDir . '/index.php'
                        );
                    }

                    $name = $sampleFile['name'];
                    $this->deleteFile();
                    $helper = new HelperUploader('virtual_product_file_uploader');
                    $objSampleProduct = new WkSampleProductMap();
                    $file = $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                        ->setSavePath($fileDir . '/')->upload($sampleFile, $name);
                    if (isset($file['error'])
                        && ((int) $file['error'] != 0)
                        && (Tools::strlen($file['error']) > 0)
                    ) {
                        $result = [
                            'success' => 0,
                            'text' => $file['error'],
                        ];
                    } else {
                        if ($sampleFileProduct = $objSampleProduct->getSampleFileName(Tools::getValue('id_product'))) {
                            $objSample = new WkSampleProductMap($sampleFileProduct['id_sample_product']);
                            $objSample->sample_file = pSQL($name);
                            $objSample->save();
                            $result = [
                                'success' => 1,
                                'text' => $this->module->l('Sample product uploaded successfully.', get_class()),
                                'name' => $name,
                            ];
                        } else {
                            // $this->context->cookie->wksample_file_name = Tools::getValue('id_product') . '__::__' . $name;
                            $this->context->cookie->__set('wksample_file_name', Tools::getValue('id_product') . '__::__' . $name);
                            $this->context->cookie->write();
                            $result = [
                                'success' => 1,
                                'text' => $this->module->l('Sample product uploaded successfully.', get_class()),
                                'name' => $name,
                            ];
                        }
                    }
                } else {
                    $result = [
                        'success' => 0,
                        'text' => $this->module->l('Please add standard virtual file first.', get_class()),
                    ];
                }
            }
        }
        exit(json_encode($result));
    }
}
