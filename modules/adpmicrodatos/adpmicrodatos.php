<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

class Adpmicrodatos extends Module
{
    const ADD = 'a';
    const REMOVE = 'd';

    private static $SECONDS_OPERATION_TIMEOUT = 5;

    private static $HOOK_HEADER = 'header';
    private static $HOOK_DISPLAYHEADER = 'displayHeader';
    private static $HOOK_DISPLAY_BACKOFFICE_HEADER = 'displayBackOfficeHeader';

    private static $PAGE_CATEGORY = 'category';
    private static $PAGE_PRODUCT = 'product';
    private static $PAGE_MANUFACTURER = 'manufacturer';
    private static $PAGE_PRICESDROP = 'pricesdrop';
    private static $PAGE_BESTSALES = 'bestsales';
    private static $PAGE_NEWPRODUCTS = 'newproducts';

    private static $TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS = 'Organization/LocalBusiness';
    private static $TYPE_MICRODATA_BREADCRUMBLIST = 'BreadcrumbList';
    private static $TYPE_MICRODATA_ITEMLIST = 'ItemList';
    private static $TYPE_MICRODATA_STORE = 'Store';
    private static $TYPE_MICRODATA_WEBPAGE = 'WebPage';
    private static $TYPE_MICRODATA_PRODUCT = 'Product';

    private $id_shop;
    private $controller_name;
    private $path;
    private $css_path;
    private $js_path;
    private $base_url;
    private $tmp_folder;
    private $adpmicrodatos_microdatos;
    private $adpmicrodatos_tools;

    public $adp_rich_snippets_ts_cod = [];
    public $adp_default_manufacturer;
    public $adp_num_products_related = 0;
    public $active_microdata_organization;
    public $active_microdata_webpage;
    public $active_microdata_website;
    public $active_microdata_store;
    public $set_microdata_store_character_separation_hours;
    public $active_microdata_breadcrumbs;
    public $active_microdata_page_category;
    public $active_microdata_page_product;
    public $active_microdata_list_product;
    public $active_microdata_features_product;
    public $adp_ids_disable_microdata_features_product;
    public $num_days_date_valid_until_product = 0;
    public $active_microdata_rich_snippets;
    public $set_microdata_type_combinations_product;
    public $set_microdata_id_product;
    public $set_microdata_id_product_combination;
    public $set_microdata_description_product_page;
    public $set_microdata_store;
    public $set_microdata_organization;
    public $set_image_product;
    public $set_configuration_product_taxes;
    public $set_configuration_product_gtin;
    public $active_microdata_rootcategory;
    public $active_microdata_homecategory;
    public $adp_product_image_type;
    public $adp_category_image_type;
    public $adp_manufacturer_image_type;
    public $adp_pages_without_microdata;
    public $adp_ids_product_without_microdata;
    public $adp_ids_manufacturers_without_microdata;
    public $adp_ids_categories_without_microdata;
    public $adp_customize_types_microdata_home;
    public $adp_customize_types_microdata_list_product;
    public $adp_customize_types_microdata_page_product;
    public $adp_customize_types_microdata_other_pages;
    public $active_microdata_page_product_description;
    public $active_microdata_page_product_brand;
    public $active_microdata_page_product_category;
    public $active_microdata_mpn_reference_same_value;
    public $active_microdata_product_weight;
    public $desactive_microdata_product_stock;
    public $desactive_microdata_product_price;
    public $adp_id_feature_3d_model;
    public $adp_return_policy_information;
    public $active_microdata_shipping_details;
    public $adp_shipping_details_shipping_rate;
    public $adp_shipping_details_address_country;
    public $adp_shipping_details_delivery_handling_time_min;
    public $adp_shipping_details_delivery_handling_time_max;
    public $adp_shipping_details_transit_handling_time_min;
    public $adp_shipping_details_transit_handling_time_max;

    public $active_tab;

    public function __construct()
    {
        $this->name = 'adpmicrodatos';
        $this->tab = 'seo';
        $this->version = '5.5.1';
        $this->author = 'Adalop';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->id_shop = Context::getContext()->shop->id;
        $this->module_key = 'a8e97c0f56df5e23bc5a0714d78d7be3';
        $this->controller_name = 'AdminAdpMicrodatos';
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];

        $this->base_url = '';
        if (defined('_PS_BASE_URL_')) {
            $this->base_url = Tools::getShopDomain(true);
        }
        if (defined('_PS_BASE_URL_SSL_')) {
            $this->base_url = Tools::getShopDomainSsl(true);
        }

        include_once dirname(__FILE__) . '/classes/adpmicrodatos.microdatos.class.php';
        include_once dirname(__FILE__) . '/classes/adpmicrodatos.richsnippets.class.php';
        include_once dirname(__FILE__) . '/classes/adpmicrodatos.tools.class.php';
        include_once dirname(__FILE__) . '/classes/ThemeFiles.php';
        include_once dirname(__FILE__) . '/classes/tools/TextTools.php';
        include_once dirname(__FILE__) . '/classes/customexceptions/OperationTimeOutException.php';
        require_once dirname(__FILE__) . '/classes/tools/AdpMicrodatosDiff.php';

        $this->adpmicrodatos_microdatos = new AdpmicrodatosMicrodatos();
        $this->adpmicrodatos_tools = new AdpmicrodatosTools();

        $config = Configuration::getMultiple(['ADP_RICH_SNIPPETS_TS_CODE', 'ADP_DEFAULT_MANUFACTURER', 'ADP_NUM_PRODUCTS_RELATED', 'ADP_ACTIVE_MICRODATA_ORGANIZATION', 'ADP_ACTIVE_MICRODATA_WEBPAGE', 'ADP_ACTIVE_MICRODATA_WEBSITE', 'ADP_ACTIVE_MICRODATA_STORE', 'ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS', 'ADP_ACTIVE_MICRODATA_BREADCRUMBS', 'ADP_ACTIVE_MICRODATA_PAGE_PRODUCT', 'ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT', 'ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT', 'ADP_ACTIVE_MICRODATA_LIST_PRODUCT', 'ADP_ACTIVE_MICRODATA_RICH_SNIPPETS', 'ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT', 'ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT', 'ADP_SET_MICRODATA_ID_PRODUCT', 'ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION', 'ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE', 'ADP_SET_MICRODATA_STORE', 'ADP_SET_MICRODATA_ORGANIZATION', 'ADP_ACTIVE_MICRODATA_ROOTCATEGORY', 'ADP_ACTIVE_MICRODATA_HOMECATEGORY', 'ADP_PRODUCT_IMAGE_TYPE', 'ADP_CATEGORY_IMAGE_TYPE', 'ADP_MANUFACTURER_IMAGE_TYPE', 'ADP_PAGES_WITHOUT_MICRODATA', 'ADP_IDS_PRODUCT_WITHOUT_MICRODATA', 'ADP_IDS_MANUFACTURERS_WITHOUT_MICRODATA', 'ADP_IDS_CATEGORIES_WITHOUT_MICRODATA', 'ADP_CUSTOMIZE_TYPES_MICRODATA_HOME', 'ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT', 'ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT', 'ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES', 'ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION', 'ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND', 'ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY', 'ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE', 'ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT', 'ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK', 'ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE', 'ADP_ID_FEATURE_3D_MODEL', 'ADP_SET_CONFIGURATION_PRODUCT_GTIN', 'ADP_SET_IMAGE_PRODUCT', 'ADP_SET_CONFIGURATION_PRODUCT_TAXES', 'ADP_RETURN_POLICY_INFORMATION', 'ACTIVE_MICRODATA_SHIPPING_DETAILS', 'ADP_SHIPPING_DETAILS_SHIPPING_RATE', 'ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY', 'ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN', 'ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX', 'ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN', 'ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX']);

        if (!empty($config['ADP_RICH_SNIPPETS_TS_CODE'])) {
            $this->adp_rich_snippets_ts_cod = json_decode($config['ADP_RICH_SNIPPETS_TS_CODE'], true);
        }
        if (!empty($config['ADP_DEFAULT_MANUFACTURER'])) {
            $this->adp_default_manufacturer = $config['ADP_DEFAULT_MANUFACTURER'];
        }
        if (!empty($config['ADP_NUM_PRODUCTS_RELATED'])) {
            $this->adp_num_products_related = $config['ADP_NUM_PRODUCTS_RELATED'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_ORGANIZATION'])) {
            $this->active_microdata_organization = $config['ADP_ACTIVE_MICRODATA_ORGANIZATION'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_WEBSITE'])) {
            $this->active_microdata_website = $config['ADP_ACTIVE_MICRODATA_WEBSITE'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_STORE'])) {
            $this->active_microdata_store = $config['ADP_ACTIVE_MICRODATA_STORE'];
        }
        if (!empty($config['ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS'])) {
            $this->set_microdata_store_character_separation_hours = $config['ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_WEBPAGE'])) {
            $this->active_microdata_webpage = $config['ADP_ACTIVE_MICRODATA_WEBPAGE'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_BREADCRUMBS'])) {
            $this->active_microdata_breadcrumbs = $config['ADP_ACTIVE_MICRODATA_BREADCRUMBS'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT'])) {
            $this->active_microdata_page_product = $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT'])) {
            $this->active_microdata_features_product = $config['ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT'];
        }
        if (!empty($config['ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT'])) {
            $this->adp_ids_disable_microdata_features_product = $config['ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_LIST_PRODUCT'])) {
            $this->active_microdata_list_product = $config['ADP_ACTIVE_MICRODATA_LIST_PRODUCT'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_RICH_SNIPPETS'])) {
            $this->active_microdata_rich_snippets = $config['ADP_ACTIVE_MICRODATA_RICH_SNIPPETS'];
        }
        if (!empty($config['ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT'])) {
            $this->num_days_date_valid_until_product = $config['ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT'];
        }
        if (!empty($config['ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT'])) {
            $this->set_microdata_type_combinations_product = $config['ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT'];
        }
        if (!empty($config['ADP_SET_MICRODATA_ID_PRODUCT'])) {
            $this->set_microdata_id_product = $config['ADP_SET_MICRODATA_ID_PRODUCT'];
        }
        if (!empty($config['ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION'])) {
            $this->set_microdata_id_product_combination = $config['ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION'];
        }
        if (!empty($config['ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE'])) {
            $this->set_microdata_description_product_page = $config['ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE'];
        }
        if (!empty($config['ADP_SET_MICRODATA_STORE'])) {
            $this->set_microdata_store = $config['ADP_SET_MICRODATA_STORE'];
        }
        if (!empty($config['ADP_SET_MICRODATA_ORGANIZATION'])) {
            $this->set_microdata_organization = $config['ADP_SET_MICRODATA_ORGANIZATION'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_ROOTCATEGORY'])) {
            $this->active_microdata_rootcategory = $config['ADP_ACTIVE_MICRODATA_ROOTCATEGORY'];
        }
        if (!empty($config['ADP_ACTIVE_MICRODATA_HOMECATEGORY'])) {
            $this->active_microdata_homecategory = $config['ADP_ACTIVE_MICRODATA_HOMECATEGORY'];
        }
        if (!empty($config['ADP_PRODUCT_IMAGE_TYPE'])) {
            $this->adp_product_image_type = $config['ADP_PRODUCT_IMAGE_TYPE'];
        }
        if (!empty($config['ADP_CATEGORY_IMAGE_TYPE'])) {
            $this->adp_category_image_type = $config['ADP_CATEGORY_IMAGE_TYPE'];
        }
        if (!empty($config['ADP_MANUFACTURER_IMAGE_TYPE'])) {
            $this->adp_manufacturer_image_type = $config['ADP_MANUFACTURER_IMAGE_TYPE'];
        }
        if (!empty($config['ADP_PAGES_WITHOUT_MICRODATA'])) {
            $this->adp_pages_without_microdata = $config['ADP_PAGES_WITHOUT_MICRODATA'];
        }
        if (!empty($config['ADP_IDS_PRODUCT_WITHOUT_MICRODATA'])) {
            $this->adp_ids_product_without_microdata = $config['ADP_IDS_PRODUCT_WITHOUT_MICRODATA'];
        }
        if (!empty($config['ADP_IDS_MANUFACTURERS_WITHOUT_MICRODATA'])) {
            $this->adp_ids_manufacturers_without_microdata = $config['ADP_IDS_MANUFACTURERS_WITHOUT_MICRODATA'];
        }
        if (!empty($config['ADP_IDS_CATEGORIES_WITHOUT_MICRODATA'])) {
            $this->adp_ids_categories_without_microdata = $config['ADP_IDS_CATEGORIES_WITHOUT_MICRODATA'];
        }
        if (!empty($config['ADP_SET_IMAGE_PRODUCT'])) {
            $this->set_image_product = $config['ADP_SET_IMAGE_PRODUCT'];
        }
        if (!empty($config['ADP_SET_CONFIGURATION_PRODUCT_TAXES'])) {
            $this->set_configuration_product_taxes = $config['ADP_SET_CONFIGURATION_PRODUCT_TAXES'];
        }
        /* Customization microdata */
        if (!empty($config['ADP_CUSTOMIZE_TYPES_MICRODATA_HOME'])) {
            $this->adp_customize_types_microdata_home = json_decode($config['ADP_CUSTOMIZE_TYPES_MICRODATA_HOME'], true);
        } else {
            $this->adp_customize_types_microdata_home = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataHomeList();
        }
        if (!empty($config['ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT'])) {
            $this->adp_customize_types_microdata_list_product = json_decode($config['ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT'], true);
        } else {
            $this->adp_customize_types_microdata_list_product = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataListProduct();
        }
        if (!empty($config['ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT'])) {
            $this->adp_customize_types_microdata_page_product = json_decode($config['ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT'], true);
        } else {
            $this->adp_customize_types_microdata_page_product = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataProduct();
        }
        if (!empty($config['ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES'])) {
            $this->adp_customize_types_microdata_other_pages = json_decode($config['ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES'], true);
        } else {
            $this->adp_customize_types_microdata_other_pages = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataOtherPages();
        }
        /* Customization microdata page product */
        if (isset($config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION']) && '' != $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION']) {
            $this->active_microdata_page_product_description = $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION'];
        } else {
            $this->active_microdata_page_product_description = 1;
        }
        if (isset($config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND']) && '' != $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND']) {
            $this->active_microdata_page_product_brand = $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND'];
        } else {
            $this->active_microdata_page_product_brand = 1;
        }
        if (isset($config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY']) && '' != $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY']) {
            $this->active_microdata_page_product_category = $config['ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY'];
        } else {
            $this->active_microdata_page_product_category = 1;
        }
        if (isset($config['ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE']) && '' != $config['ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE']) {
            $this->active_microdata_mpn_reference_same_value = $config['ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE'];
        } else {
            $this->active_microdata_page_product_category = 0;
        }
        if (isset($config['ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT']) && '' != $config['ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT']) {
            $this->active_microdata_product_weight = $config['ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT'];
        } else {
            $this->active_microdata_product_weight = 0;
        }
        if (isset($config['ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK']) && '' != $config['ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK']) {
            $this->desactive_microdata_product_stock = $config['ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK'];
        } else {
            $this->desactive_microdata_product_stock = 0;
        }
        if (isset($config['ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE']) && '' != $config['ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE']) {
            $this->desactive_microdata_product_price = $config['ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE'];
        } else {
            $this->desactive_microdata_product_price = 0;
        }
        if (!empty($config['ADP_ID_FEATURE_3D_MODEL'])) {
            $this->adp_id_feature_3d_model = $config['ADP_ID_FEATURE_3D_MODEL'];
        }
        if (isset($config['ADP_SET_CONFIGURATION_PRODUCT_GTIN']) && '' != $config['ADP_SET_CONFIGURATION_PRODUCT_GTIN']) {
            $this->set_configuration_product_gtin = $config['ADP_SET_CONFIGURATION_PRODUCT_GTIN'];
        } else {
            $this->set_configuration_product_gtin = 0;
        }
        if (!empty($config['ADP_RETURN_POLICY_INFORMATION'])) {
            $this->adp_return_policy_information = json_decode($config['ADP_RETURN_POLICY_INFORMATION'], true);
        }
        if (!empty($config['ACTIVE_MICRODATA_SHIPPING_DETAILS'])) {
            $this->active_microdata_shipping_details = $config['ACTIVE_MICRODATA_SHIPPING_DETAILS'];
        } else {
            $this->active_microdata_shipping_details = 0;
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_SHIPPING_RATE'])) {
            $this->adp_shipping_details_shipping_rate = $config['ADP_SHIPPING_DETAILS_SHIPPING_RATE'];
        } else {
            $this->adp_shipping_details_shipping_rate = 0;
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY'])) {
            $this->adp_shipping_details_address_country = $config['ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY'];
        } else {
            $this->adp_shipping_details_address_country = '';
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN'])) {
            $this->adp_shipping_details_delivery_handling_time_min = $config['ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN'];
        } else {
            $this->adp_shipping_details_delivery_handling_time_min = 0;
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX'])) {
            $this->adp_shipping_details_delivery_handling_time_max = $config['ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX'];
        } else {
            $this->adp_shipping_details_delivery_handling_time_max = 0;
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN'])) {
            $this->adp_shipping_details_transit_handling_time_min = $config['ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN'];
        } else {
            $this->adp_shipping_details_transit_handling_time_min = 0;
        }
        if (!empty($config['ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX'])) {
            $this->adp_shipping_details_transit_handling_time_max = $config['ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX'];
        } else {
            $this->adp_shipping_details_transit_handling_time_max = 0;
        }

        parent::__construct();
        $this->displayName = $this->l('Automatic Rich Snippets JSON-LD Integration - SEO');
        $this->description = $this->l('Clean your microdata templates automatically and add new microdata correctly configured for your online store');
        $this->confirmUninstall = $this->l('Are you sure about removing these options?');
        $this->path = _PS_MODULE_DIR_ . $this->name . '/';
        $this->css_path = $this->path . 'views/css/';
        $this->js_path = $this->path . 'views/js/';
        $this->tmp_folder = $this->path . 'tmp/';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !Configuration::updateValue('ADP_RICH_SNIPPETS_TS_CODE', json_encode([]))
            || !Configuration::updateValue('ADP_DEFAULT_MANUFACTURER', '')
            || !Configuration::updateValue('ADP_NUM_PRODUCTS_RELATED', '0')
            || !Configuration::updateValue('STAGE_NUMBER_INSTALLATION', '0')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_ORGANIZATION', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_WEBSITE', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_WEBPAGE', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_STORE', '0')
            || !Configuration::updateValue('ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS', '-')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_BREADCRUMBS', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT', '1')
            || !Configuration::updateValue('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT', '0')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT', '0')
            || !Configuration::updateValue('ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT', '')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_LIST_PRODUCT', '0')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_RICH_SNIPPETS', null != AdpmicrodatosRichsnippets::getRichSnippetsEnabledModuleName())
            || !Configuration::updateValue('ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT', '1')
            || !Configuration::updateValue('ADP_SET_MICRODATA_ID_PRODUCT', '{id_product}')
            || !Configuration::updateValue('ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION', '{id_product}-{id_product_combination}')
            || !Configuration::updateValue('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE', '1')
            || !Configuration::updateValue('ADP_SET_MICRODATA_STORE', '1')
            || !Configuration::updateValue('ADP_SET_MICRODATA_ORGANIZATION', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_ROOTCATEGORY', '0')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_HOMECATEGORY', '1')
            || !Configuration::updateValue('ADP_PRODUCT_IMAGE_TYPE', AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_TIPO_IMAGEN, 'large'))
            || !Configuration::updateValue('ADP_CATEGORY_IMAGE_TYPE', AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_TIPO_IMAGEN, 'category'))
            || !Configuration::updateValue('ADP_MANUFACTURER_IMAGE_TYPE', AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_TIPO_IMAGEN, 'large'))
            || !Configuration::updateValue('ADP_PAGES_WITHOUT_MICRODATA', 'cart,checkout,my-account,myaccount,register,history,guest_tracking,order,order-detail,order-confirmation,order_detail,order_follow,order_return,order-slip,orderslip,identity,address,addresses,password,authentication,order-opc,orderopc,pdf_invoice,pdf_order_return,pdf_order_slip,order_login,pagenotfound')
            || !Configuration::updateValue('ADP_PAGES_IDS_PRODUCT_MICRODATA', '')
            || !Configuration::updateValue('ADP_PAGES_IDS_MANUFACTURERS_MICRODATA', '')
            || !Configuration::updateValue('ADP_PAGES_IDS_CATEGORIES_MICRODATA', '')
            || !Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_HOME', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataHomeList()))
            || !Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataListProduct()))
            || !Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataProduct()))
            || !Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataOtherPages()))
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY', '1')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE', '0')
            || !Configuration::updateValue('ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT', '0')
            || !Configuration::updateValue('ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK', '0')
            || !Configuration::updateValue('ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE', '0')
            || !Configuration::updateValue('ADP_ID_FEATURE_3D_MODEL', '')
            || !Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_GTIN', '1')
            || !Configuration::updateValue('ADP_SET_IMAGE_PRODUCT', '1')
            || !Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_TAXES', '0')
            || !Configuration::updateValue('ADP_RETURN_POLICY_INFORMATION', json_encode([]))
            || !Configuration::updateValue('ACTIVE_MICRODATA_SHIPPING_DETAILS', 0)
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_SHIPPING_RATE', 0)
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY', 'ES')
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN', 0)
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX', 0)
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN', 0)
            || !Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX', 0)
            || !$this->installTab()
            || !$this->_hookSetup(self::ADD)) {
            return false;
        }

        if (is_writable($this->tmp_folder)) {
            $result = ThemeFiles::processing($this->getFoldersToProcess());
            Configuration::updateValue('STAGE_NUMBER_INSTALLATION', $result['filesProcessing'] > 0 ? '1' : '2');

            Tools::clearAllCache();
        }

        return true;
    }

    private function getFoldersToProcess()
    {
        $foldersToProcess = [];

        $themeManagerBuilder = new PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder($this->context, Db::getInstance());
        $themesRepository = $themeManagerBuilder->buildRepository();
        $themeName = Context::getContext()->shop->theme_name;

        while (null != $themeName) {
            $theme = $themesRepository->getInstanceByName($themeName);
            $foldersToProcess[] = $theme->get('directory');
            $themeName = $theme->get('parent');
        }

        // Add here the module names that you want to process automatically when you install the adpmicrodatos module
        $modulesToProcess = ['productcomments', 'prrs', 'myprestacomments'];
        foreach ($modulesToProcess as $moduleToProcess) {
            if (Module::isInstalled($moduleToProcess) && Module::isEnabled($moduleToProcess)) {
                $foldersToProcess[] = _PS_MODULE_DIR_ . $moduleToProcess;
            }
        }

        return $foldersToProcess;
    }

    /**
     * Install an admin tab.
     *
     * @return bool
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $this->controller_name;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'adpmicrodatos';
        }
        unset($lang);
        $tab->id_parent = -1;
        $tab->module = $this->name;
        $result = $tab->add();

        return $result;
    }

    public function uninstall()
    {
        ThemeFiles::recovery($this->getFoldersToProcess());
        if (!$this->uninstallTab()
            || !$this->_hookSetup(self::REMOVE)
            || !parent::uninstall()) {
            return false;
        }

        Configuration::deleteByName('ADP_RICH_SNIPPETS_TS_CODE');
        Configuration::deleteByName('ADP_DEFAULT_MANUFACTURER');
        Configuration::deleteByName('ADP_NUM_PRODUCTS_RELATED');
        Configuration::deleteByName('STAGE_NUMBER_INSTALLATION');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_ORGANIZATION');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_WEBSITE');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_WEBPAGE');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_STORE');
        Configuration::deleteByName('ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_BREADCRUMBS');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT');
        Configuration::deleteByName('ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_LIST_PRODUCT');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_RICH_SNIPPETS');
        Configuration::deleteByName('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT');
        Configuration::deleteByName('ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT');
        Configuration::deleteByName('ADP_SET_MICRODATA_ID_PRODUCT');
        Configuration::deleteByName('ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION');
        Configuration::deleteByName('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE');
        Configuration::deleteByName('ADP_SET_MICRODATA_STORE');
        Configuration::deleteByName('ADP_SET_MICRODATA_ORGANIZATION');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_ROOTCATEGORY');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_HOMECATEGORY');
        Configuration::deleteByName('ADP_SCANNED_MODULES');
        Configuration::deleteByName('ADP_SCANNED_MODULES_TIMEOUT');
        Configuration::deleteByName('ADP_PRODUCT_IMAGE_TYPE');
        Configuration::deleteByName('ADP_CATEGORY_IMAGE_TYPE');
        Configuration::deleteByName('ADP_MANUFACTURER_IMAGE_TYPE');
        Configuration::deleteByName('ADP_PAGES_WITHOUT_MICRODATA');
        Configuration::deleteByName('ADP_IDS_PRODUCTS_WITHOUT_MICRODATA');
        Configuration::deleteByName('ADP_IDS_MANUFACTURERS_WITHOUT_MICRODATA');
        Configuration::deleteByName('ADP_IDS_CATEGORIES_WITHOUT_MICRODATA');
        Configuration::deleteByName('ADP_CUSTOMIZE_TYPES_MICRODATA_HOME');
        Configuration::deleteByName('ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT');
        Configuration::deleteByName('ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT');
        Configuration::deleteByName('ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE');
        Configuration::deleteByName('ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK');
        Configuration::deleteByName('ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE');
        Configuration::deleteByName('ADP_ID_FEATURE_3D_MODEL');
        Configuration::deleteByName('ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT');
        Configuration::deleteByName('ADP_SET_CONFIGURATION_PRODUCT_GTIN');
        Configuration::deleteByName('ADP_SET_IMAGE_PRODUCT');
        Configuration::deleteByName('ADP_SET_CONFIGURATION_PRODUCT_TAXES');
        Configuration::deleteByName('ADP_RETURN_POLICY_INFORMATION');
        Configuration::deleteByName('ACTIVE_MICRODATA_SHIPPING_DETAILS');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_SHIPPING_RATE');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN');
        Configuration::deleteByName('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX');

        return true;
    }

    /**
     * Uninstall Tab.
     *
     * @return bool
     */
    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('Adminadpmicrodatos');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    private function _hookSetup($action)
    {
        $expectedHooksAdd = [
            self::$HOOK_DISPLAYHEADER,
            self::$HOOK_DISPLAY_BACKOFFICE_HEADER,
        ];

        $expectedHooksReMove = [
            self::$HOOK_DISPLAYHEADER,
            self::$HOOK_DISPLAY_BACKOFFICE_HEADER,
            self::$HOOK_HEADER,
        ];

        $pass = true;

        if (Adpmicrodatos::ADD == $action) {
            foreach ($expectedHooksAdd as $expectedHook) {
                if (!$this->isRegisteredInHook($expectedHook)) {
                    if (!$this->registerHook($expectedHook)) {
                        $this->_errors[] = $this->l('Unable to Register Hook') . ':' . $expectedHook;
                        $pass = false;
                    }
                }
            }
            if ($pass) {
                $this->adpmicrodatos_tools->movePositionHookAfterInstall($expectedHooksAdd, $this->name);
            }
        }
        if (Adpmicrodatos::REMOVE == $action) {
            foreach ($expectedHooksReMove as $expectedHook) {
                if ($this->isRegisteredInHook($expectedHook)) {
                    if (!$this->unregisterHook($expectedHook)) {
                        $this->_errors[] = $this->l('Unable to Unregister Hook') . ':' . $expectedHook;
                        $pass = false;
                    }
                }
            }
        }

        return $pass;
    }

    private function listModules($includeDisabled = false)
    {
        $result = [];
        $ignoreModules = [
            $this->name,
            'adpsearchlocatemicrodatos',
            'adpopengraph',
            'adpmicrodatosvideos',
            'adpfaqrichsnippets,',
        ];

        foreach (Module::getModulesInstalled() as $module) {
            if (!in_array($module['name'], $ignoreModules) && ($includeDisabled || Module::isEnabled($module['name']))) {
                $result[$module['name']] = _PS_MODULE_DIR_ . $module['name'];
            }
        }

        return $result;
    }

    /**
     * Escaneo de módulos.
     *
     * @return array Un valor boleano indicando si se agotó el tiempo de espera y el listado de módulos escaneados que contienen microdatos
     */
    private function scanModules()
    {
        $modulesScanningTimeOut = Configuration::hasKey('ADP_SCANNED_MODULES_TIMEOUT') ? Configuration::get('ADP_SCANNED_MODULES_TIMEOUT') : false;
        $modulesContainsMicrodata = Configuration::hasKey('ADP_SCANNED_MODULES') ? json_decode(Configuration::get('ADP_SCANNED_MODULES'), true) : [];

        if (Tools::isSubmit('adpmicrodatos_form_scan_modules_btn')) {
            $fullScan = key_exists('adpmicrodatos_fullscanmodules', $_POST);
            if ($fullScan) {
                $modulesScanningTimeOut = false;
                $modulesContainsMicrodata = [];
            }

            $firstModule = null;
            $lastModule = null;
            $modulesScanningTimeOut = false;
            $endTime = time() + self::$SECONDS_OPERATION_TIMEOUT;
            try {
                foreach ($this->listModules() as $moduleName => $moduleFolder) {
                    if (!array_key_exists($moduleName, $modulesContainsMicrodata)) {
                        if (null == $firstModule) {
                            $firstModule = $moduleName;
                        }
                        $lastModule = $moduleName;
                        $modulesContainsMicrodata[$moduleName] = ThemeFiles::folderContainsMicrodata($moduleFolder, $endTime) ? 1 : 0;
                    }
                }
            } catch (OperationTimeOutException $timeOutException) {
                if ($firstModule == $lastModule) {
                    $modulesContainsMicrodata[$lastModule] = -1;
                }
                $modulesScanningTimeOut = true;
            } catch (Exception $exception) {
                $modulesContainsMicrodata[$lastModule] = -2;
            } catch (Error $error) {
                $modulesContainsMicrodata[$lastModule] = -2;
            }

            Configuration::updateValue('ADP_SCANNED_MODULES', json_encode($modulesContainsMicrodata));
            Configuration::updateValue('ADP_SCANNED_MODULES_TIMEOUT', $modulesScanningTimeOut);
        }

        // Generamos los datos para mostrar los módulos que contienen microdatos
        $link = new Link();
        $baseUrl = ((Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://') . Tools::getShopDomainSsl() . __PS_BASE_URI__;
        $modulesContainsMicrodataResult = [];
        foreach ($modulesContainsMicrodata as $name => $statusCode) {
            if (1 == $statusCode) {
                $modulesContainsMicrodataResult[] = [
                    'name' => $name,
                    'fullName' => Module::getModuleName($name),
                    'url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $name,
                    'logoUrl' => $baseUrl . 'modules/' . $name . '/logo.png',
                ];
            }
        }

        return [$modulesScanningTimeOut, $modulesContainsMicrodataResult];
    }

    public function initialise_return_policy()
    {
        $iso_country_default = Context::getContext()->country->iso_code;

        $paises = Country::getCountries(Context::getContext()->language->id, true);

        $refund_policy = [];

        foreach ($paises as $key => $value) {
            $active = 0;
            $return_policy_categories = '';
            $merchant_return_days = '';
            $return_method = '';
            $return_fees = '';

            $refund_policy[strtolower($value['iso_code'])] = [
                'active' => $active,
                'applicable_country' => strtolower($value['iso_code']),
                'name_country' => $value['country'],
                'return_policy_categories' => $return_policy_categories,
                'merchant_return_days' => $merchant_return_days,
                'return_method' => $return_method,
                'return_fees' => $return_fees,
            ];
        }

        Configuration::updateValue('ADP_RETURN_POLICY_INFORMATION', json_encode($refund_policy));
    }

    public function getContent()
    {
        // Procesamiento de solicitudes asíncronas
        if (isset($_REQUEST['asyncAction'])) {
            $action = $_REQUEST['asyncAction'];
            $this->$action();
            exit;
        }

        if (Tools::isSubmit('adpmicrodatos_form_log_submit_btn')) {
            $logContent = ThemeFiles::getLogFull();
            if (!empty($logContent)) {
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename=log.txt');
                header('Pragma: no-cache');
                echo $logContent;
                exit;
            }
        }

        // Opción de reescaneo
        if (Tools::isSubmit('adpmicrodatos_form_reescan_submit_btn')) {
            $count = ThemeFiles::reescanFolders($this->getFoldersToProcess());
            if ($count > 0) {
                Configuration::updateValue('STAGE_NUMBER_INSTALLATION', '1');
            }
        }

        // Obtenemos los módulos escaneados, y se realiza un escaneo si procede
        list($modulesScanningTimeOut, $modulesContainsMicrodataResult) = $this->scanModules();

        $this->active_tab = '#tab_installation';
        $this->processConfiguration();
        $admin_token = '&token=' . Tools::getAdminTokenLite($this->controller_name);
        $controller_url = 'index.php?tab=' . $this->controller_name . $admin_token;
        $doc_lang = $this->getLangForDoc();

        $idiomas = Language::getLanguages(true, Context::getContext()->shop->id);
        $isos_idiomas = [];
        foreach ($idiomas as $idioma) {
            $isos_idiomas[$idioma['name']] = $idioma['iso_code'];
        }

        // Inicializamos si está vacío
        if (empty($this->adp_rich_snippets_ts_cod)) {
            foreach (Language::getLanguages(false) as $language) {
                $this->adp_rich_snippets_ts_cod[(int) $language['id_lang']] = '';
            }
        }

        // Inicializamos si está vacío
        if (empty($this->adp_return_policy_information)) {
            $this->initialise_return_policy();
        }

        // Passing variables to the back-office template
        $this->context->smarty->assign(
            [
                'module_version' => $this->version,
                'id_lang' => Context::getContext()->language->id,
                'module_display' => $this->displayName,
                'guide_link' => 'docs/doc_' . $this->name . '_' . $doc_lang . '.pdf',
                'results_scan_files' => !empty(ThemeFiles::getLog()) ? ThemeFiles::getLog() : [],
                'backup_files' => ThemeFiles::listBackupFiles(),
                'result_scan_modules' => $modulesContainsMicrodataResult,
                'result_scan_modules_timeout' => $modulesScanningTimeOut,
                'tracking_url' => '?utm_source=back-office&utm_medium=module&utm_campaign=
                                                 back-office-ES&utm_content=' . $this->name,
                'adp_installation_path' => $this->path . 'views/templates/admin/installation.tpl',
                'adp_configuration_path' => $this->path . 'views/templates/admin/configuration.tpl',
                'adp_customize_path' => $this->path . 'views/templates/admin/customize.tpl',
                'adp_backups_path' => $this->path . 'views/templates/admin/backups.tpl',
                'adp_help_path' => $this->path . 'views/templates/admin/help.tpl',
                'adp_modules_related_path' => $this->path . 'views/templates/admin/modules_related.tpl',
                'adp_thirdparty_richsnippets_modules_path' => $this->path . 'views/templates/admin/thirparty_richsnippet_modules.tpl',
                'adp_rich_snippets_ts_cod' => $this->adp_rich_snippets_ts_cod,
                'adp_default_manufacturer' => $this->adp_default_manufacturer,
                'adp_num_products_related' => $this->adp_num_products_related,
                'adp_pages_without_microdata' => $this->adp_pages_without_microdata,
                'adp_ids_product_without_microdata' => $this->adp_ids_product_without_microdata,
                'adp_ids_manufacturers_without_microdata' => $this->adp_ids_manufacturers_without_microdata,
                'adp_ids_categories_without_microdata' => $this->adp_ids_categories_without_microdata,
                'msg_confirmation_delete' => $this->l('Are you sure you want to delete this item?'),
                'controller_name' => $this->controller_name,
                'stage_number_installation' => Configuration::get('STAGE_NUMBER_INSTALLATION'),
                'temp_folder_unwriteble' => !is_writable($this->tmp_folder),
                'admin_controller_url' => $controller_url,
                'active_microdata_organization' => $this->active_microdata_organization,
                'active_microdata_webpage' => $this->active_microdata_webpage,
                'active_microdata_website' => $this->active_microdata_website,
                'active_microdata_store' => $this->active_microdata_store,
                'set_microdata_store_character_separation_hours' => $this->set_microdata_store_character_separation_hours,
                'active_microdata_breadcrumbs' => $this->active_microdata_breadcrumbs,
                'active_microdata_page_product' => $this->active_microdata_page_product,
                'active_microdata_features_product' => $this->active_microdata_features_product,
                'ids_disable_microdata_features_product' => $this->adp_ids_disable_microdata_features_product,
                'num_days_date_valid_until_product' => $this->num_days_date_valid_until_product,
                'active_microdata_list_product' => $this->active_microdata_list_product,
                'active_microdata_rich_snippets' => $this->active_microdata_rich_snippets,
                'microdata_richsnippets_implemented_modules' => join('<br />', array_keys(AdpmicrodatosRichsnippets::$richSnippetsImplementedModules)),
                'set_microdata_type_combinations_product' => $this->set_microdata_type_combinations_product,
                'set_microdata_id_product' => $this->set_microdata_id_product,
                'set_microdata_id_product_combination' => $this->set_microdata_id_product_combination,
                'set_microdata_description_product_page' => $this->set_microdata_description_product_page,
                'set_microdata_store' => $this->set_microdata_store,
                'set_microdata_organization' => $this->set_microdata_organization,
                'active_microdata_rootcategory' => $this->active_microdata_rootcategory,
                'active_microdata_homecategory' => $this->active_microdata_homecategory,
                'image_types' => $this->adpmicrodatos_tools->getDataComboImageTypes(),
                'refund_policies_types' => $this->adpmicrodatos_tools->getDataComboRefundPolicies(),
                'active_tab' => $this->active_tab,
                'richSnippetsImplementedModules' => AdpmicrodatosRichsnippets::$richSnippetsImplementedModules,
                'types_microdata_homepage' => $this->adpmicrodatos_microdatos->getMicrodataHomeList(),
                'types_microdata_homepage_selected' => $this->adp_customize_types_microdata_home,
                'types_microdata_product_list' => $this->adpmicrodatos_microdatos->getMicrodataListProduct(),
                'types_microdata_product_list_selected' => $this->adp_customize_types_microdata_list_product,
                'types_microdata_product_page' => $this->adpmicrodatos_microdatos->getMicrodataProduct(),
                'types_microdata_product_page_selected' => $this->adp_customize_types_microdata_page_product,
                'types_microdata_other_pages' => $this->adpmicrodatos_microdatos->getMicrodataOtherPages(),
                'types_microdata_other_pages_selected' => $this->adp_customize_types_microdata_other_pages,
                'active_microdata_page_product_description' => $this->active_microdata_page_product_description,
                'active_microdata_page_product_brand' => $this->active_microdata_page_product_brand,
                'active_microdata_page_product_category' => $this->active_microdata_page_product_category,
                'active_microdata_mpn_reference_same_value' => $this->active_microdata_mpn_reference_same_value,
                'active_microdata_product_weight' => $this->active_microdata_product_weight,
                'desactive_microdata_product_stock' => $this->desactive_microdata_product_stock,
                'desactive_microdata_product_price' => $this->desactive_microdata_product_price,
                'id_feature_3d_model' => $this->adp_id_feature_3d_model,
                'set_configuration_product_gtin' => $this->set_configuration_product_gtin,
                'iso_code_language' => strtolower(Language::getIsoById(Context::getContext()->language->id)),
                'isos_idiomas' => $isos_idiomas,
                'pais_defecto' => strtolower(Context::getContext()->country->iso_code),
                'languages' => Language::getLanguages(false),
                'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
                'set_image_product' => $this->set_image_product,
                'set_configuration_product_taxes' => $this->set_configuration_product_taxes,
                'adp_return_policy_information' => $this->adp_return_policy_information,
                'active_microdata_shipping_details' => $this->active_microdata_shipping_details,
                'adp_shipping_details_shipping_rate' => $this->adp_shipping_details_shipping_rate,
                'adp_shipping_details_address_country' => $this->adp_shipping_details_address_country,
                'adp_shipping_details_delivery_handling_time_min' => $this->adp_shipping_details_delivery_handling_time_min,
                'adp_shipping_details_delivery_handling_time_max' => $this->adp_shipping_details_delivery_handling_time_max,
                'adp_shipping_details_transit_handling_time_min' => $this->adp_shipping_details_transit_handling_time_min,
                'adp_shipping_details_transit_handling_time_max' => $this->adp_shipping_details_transit_handling_time_max,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/index.tpl');
    }

    public function processConfiguration()
    {
        $post_data = self::getAllValues();

        if (!empty($post_data['ClearCache'])) {
            Tools::clearAllCache();
            $this->context->smarty->assign(['confirmation' => 'ok']);
        } elseif (Tools::isSubmit('option_form_submit_btn')) {
            if ($this->formIsValid($post_data)) {
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_RICH_SNIPPETS', null == AdpmicrodatosRichsnippets::getRichSnippetsEnabledModuleName() ? 0 : $post_data['active_microdata_rich_snippets']);

                foreach (Language::getLanguages(false) as $language) {
                    $this->adp_rich_snippets_ts_cod[(int) $language['id_lang']] = $post_data['view_microdata_code_ts_' . (int) $language['id_lang']];
                }
                Configuration::updateValue('ADP_RICH_SNIPPETS_TS_CODE', json_encode($this->adp_rich_snippets_ts_cod));

                Configuration::updateValue('ADP_NUM_PRODUCTS_RELATED', $post_data['view_microdata_products_related']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_ORGANIZATION', $post_data['active_microdata_organization']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_WEBPAGE', $post_data['active_microdata_webpage']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_WEBSITE', $post_data['active_microdata_website']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_STORE', $post_data['active_microdata_store']);
                Configuration::updateValue('ADP_SET_MICRODATA_STORE_CHARACTER_SEPARATION_HOURS', $post_data['set_microdata_store_character_separation_hours']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_BREADCRUMBS', $post_data['active_microdata_breadcrumbs']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT', $post_data['active_microdata_page_product']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_FEATURES_PRODUCT', $post_data['active_microdata_features_product']);
                Configuration::updateValue('ADP_IDS_DISABLE_MICRODATA_FEATURES_PRODUCT', $post_data['ids_disable_microdata_features_product']);
                Configuration::updateValue('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT', $post_data['num_days_date_valid_until_product']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_LIST_PRODUCT', $post_data['active_microdata_list_product']);
                Configuration::updateValue('ADP_SET_MICRODATA_TYPE_COMBINATION_PRODUCT', $post_data['set_microdata_type_combinations_product']);
                Configuration::updateValue('ADP_SET_MICRODATA_ID_PRODUCT', $post_data['set_microdata_id_product']);
                Configuration::updateValue('ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION', $post_data['set_microdata_id_product_combination']);
                Configuration::updateValue('ADP_SET_MICRODATA_STORE', $post_data['set_microdata_store']);
                Configuration::updateValue('ADP_SET_MICRODATA_ORGANIZATION', $post_data['set_microdata_organization']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_ROOTCATEGORY', $post_data['active_microdata_rootcategory']);
                Configuration::updateValue('ADP_ACTIVE_MICRODATA_HOMECATEGORY', $post_data['active_microdata_homecategory']);
                Configuration::updateValue('ADP_PRODUCT_IMAGE_TYPE', $post_data['product_image_type']);
                Configuration::updateValue('ADP_CATEGORY_IMAGE_TYPE', $post_data['category_image_type']);
                Configuration::updateValue('ADP_MANUFACTURER_IMAGE_TYPE', $post_data['manufacturer_image_type']);
                Configuration::updateValue('ADP_PAGES_WITHOUT_MICRODATA', $post_data['adp_pages_without_microdata']);
                Configuration::updateValue('ADP_IDS_PRODUCT_WITHOUT_MICRODATA', $post_data['adp_ids_product_without_microdata']);
                Configuration::updateValue('ADP_IDS_MANUFACTURERS_MICRODATA', $post_data['adp_ids_manufacturers_without_microdata']);
                Configuration::updateValue('ADP_IDS_CATEGORIES_WITHOUT_MICRODATA', $post_data['adp_ids_categories_without_microdata']);

                $this->adp_num_products_related = $post_data['view_microdata_products_related'];
                $this->active_microdata_organization = $post_data['active_microdata_organization'];
                $this->active_microdata_webpage = $post_data['active_microdata_webpage'];
                $this->active_microdata_website = $post_data['active_microdata_website'];
                $this->active_microdata_store = $post_data['active_microdata_store'];
                $this->set_microdata_store_character_separation_hours = $post_data['set_microdata_store_character_separation_hours'];
                $this->active_microdata_breadcrumbs = $post_data['active_microdata_breadcrumbs'];
                $this->active_microdata_page_product = $post_data['active_microdata_page_product'];
                $this->active_microdata_features_product = $post_data['active_microdata_features_product'];
                $this->adp_ids_disable_microdata_features_product = $post_data['ids_disable_microdata_features_product'];
                $this->num_days_date_valid_until_product = $post_data['num_days_date_valid_until_product'];
                $this->active_microdata_list_product = $post_data['active_microdata_list_product'];
                $this->set_microdata_type_combinations_product = $post_data['set_microdata_type_combinations_product'];
                $this->set_microdata_id_product = $post_data['set_microdata_id_product'];
                $this->set_microdata_id_product_combination = $post_data['set_microdata_id_product_combination'];
                $this->set_microdata_store = $post_data['set_microdata_store'];
                $this->set_microdata_organization = $post_data['set_microdata_organization'];
                $this->active_microdata_rootcategory = $post_data['active_microdata_rootcategory'];
                $this->active_microdata_homecategory = $post_data['active_microdata_homecategory'];
                $this->adp_product_image_type = $post_data['product_image_type'];
                $this->adp_category_image_type = $post_data['category_image_type'];
                $this->adp_manufacturer_image_type = $post_data['manufacturer_image_type'];
                $this->adp_pages_without_microdata = $post_data['adp_pages_without_microdata'];
                $this->adp_ids_product_without_microdata = $post_data['adp_ids_product_without_microdata'];
                $this->adp_ids_manufacturers_without_microdata = $post_data['adp_ids_manufacturers_without_microdata'];
                $this->adp_ids_categories_without_microdata = $post_data['adp_ids_categories_without_microdata'];
                $this->active_microdata_rich_snippets = null == AdpmicrodatosRichsnippets::getRichSnippetsEnabledModuleName() ? 0 : $post_data['active_microdata_rich_snippets'];

                $this->context->smarty->assign(['confirmation' => 'ok']);
            } else {
                $this->context->smarty->assign(['error' => 'ok']);
            }
            $this->active_tab = '#tab_configuration';
        } elseif (Tools::isSubmit('customize_form_submit_btn')) {
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_HOME', json_encode($post_data['customize_types_microdata_homepage']));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT', json_encode($post_data['customize_types_microdata_product_list']));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT', json_encode($post_data['customize_types_microdata_product_page']));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES', json_encode($post_data['customize_types_microdata_other_pages']));

            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION', $post_data['active_microdata_page_product_description']);
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND', $post_data['active_microdata_page_product_brand']);
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY', $post_data['active_microdata_page_product_category']);
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE', $post_data['active_microdata_mpn_reference_same_value']);
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PRODUCT_WEIGHT', $post_data['active_microdata_product_weight']);
            Configuration::updateValue('ADP_DESACTIVE_MICRODATA_PRODUCT_STOCK', $post_data['desactive_microdata_product_stock']);
            Configuration::updateValue('ADP_DESACTIVE_MICRODATA_PRODUCT_PRICE', $post_data['desactive_microdata_product_price']);
            Configuration::updateValue('ADP_ID_FEATURE_3D_MODEL', $post_data['id_feature_3d_model']);
            Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_GTIN', $post_data['set_configuration_product_gtin']);

            Configuration::updateValue('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE', $post_data['set_microdata_description_product_page']);
            Configuration::updateValue('ADP_DEFAULT_MANUFACTURER', $post_data['view_microdata_default_manufacturer']);
            Configuration::updateValue('ADP_SET_IMAGE_PRODUCT', $post_data['set_image_product']);
            Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_TAXES', $post_data['set_configuration_product_taxes']);

            $paises = Country::getCountries(Context::getContext()->language->id, true);
            $refund_policy = [];
            foreach ($paises as $key => $value) {
                $aux_iso_code = strtolower($value['iso_code']);

                $refund_policy[$aux_iso_code] = [
                    'active' => $post_data['active_microdata_refund_policy_' . $aux_iso_code],
                    'applicable_country' => $aux_iso_code,
                    'name_country' => $value['country'],
                    'return_policy_categories' => $post_data['adp_return_policy_categories_' . $aux_iso_code],
                    'merchant_return_days' => $post_data['adp_merchant_return_days_' . $aux_iso_code],
                    'return_method' => $post_data['adp_return_method_' . $aux_iso_code],
                    'return_fees' => $post_data['adp_return_fees_' . $aux_iso_code],
                ];
            }
            Configuration::updateValue('ADP_RETURN_POLICY_INFORMATION', json_encode($refund_policy));

            Configuration::updateValue('ACTIVE_MICRODATA_SHIPPING_DETAILS', $post_data['active_microdata_shipping_details']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_SHIPPING_RATE', $post_data['adp_shipping_details_shipping_rate']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_ADDRESS_COUNTRY', $post_data['adp_shipping_details_address_country']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MIN', $post_data['adp_shipping_details_delivery_handling_time_min']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_DELIVERY_HANDLING_TIME_MAX', $post_data['adp_shipping_details_delivery_handling_time_max']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MIN', $post_data['adp_shipping_details_transit_handling_time_min']);
            Configuration::updateValue('ADP_SHIPPING_DETAILS_TRANSIT_HANDLING_TIME_MAX', $post_data['adp_shipping_details_transit_handling_time_max']);

            $this->adp_customize_types_microdata_home = $post_data['customize_types_microdata_homepage'];
            $this->adp_customize_types_microdata_list_product = $post_data['customize_types_microdata_product_list'];
            $this->adp_customize_types_microdata_page_product = $post_data['customize_types_microdata_product_page'];
            $this->adp_customize_types_microdata_other_pages = $post_data['customize_types_microdata_other_pages'];

            $this->active_microdata_page_product_description = $post_data['active_microdata_page_product_description'];
            $this->active_microdata_page_product_brand = $post_data['active_microdata_page_product_brand'];
            $this->active_microdata_page_product_category = $post_data['active_microdata_page_product_category'];
            $this->active_microdata_mpn_reference_same_value = $post_data['active_microdata_mpn_reference_same_value'];
            $this->active_microdata_product_weight = $post_data['active_microdata_product_weight'];
            $this->desactive_microdata_product_stock = $post_data['desactive_microdata_product_stock'];
            $this->desactive_microdata_product_price = $post_data['desactive_microdata_product_price'];
            $this->adp_id_feature_3d_model = $post_data['id_feature_3d_model'];
            $this->set_configuration_product_gtin = $post_data['set_configuration_product_gtin'];

            $this->set_microdata_description_product_page = $post_data['set_microdata_description_product_page'];
            $this->adp_default_manufacturer = $post_data['view_microdata_default_manufacturer'];
            $this->set_image_product = $post_data['set_image_product'];
            $this->set_configuration_product_taxes = $post_data['set_configuration_product_taxes'];

            $this->adp_return_policy_information = json_decode(Configuration::get('ADP_RETURN_POLICY_INFORMATION'), true);

            $this->active_microdata_shipping_details = $post_data['active_microdata_shipping_details'];
            $this->adp_shipping_details_shipping_rate = $post_data['adp_shipping_details_shipping_rate'];
            $this->adp_shipping_details_address_country = $post_data['adp_shipping_details_address_country'];
            $this->adp_shipping_details_delivery_handling_time_min = $post_data['adp_shipping_details_delivery_handling_time_min'];
            $this->adp_shipping_details_delivery_handling_time_max = $post_data['adp_shipping_details_delivery_handling_time_max'];
            $this->adp_shipping_details_transit_handling_time_min = $post_data['adp_shipping_details_transit_handling_time_min'];
            $this->adp_shipping_details_transit_handling_time_max = $post_data['adp_shipping_details_transit_handling_time_max'];

            $this->context->smarty->assign(['confirmation' => 'ok']);
            $this->active_tab = '#tab_customize';
        } elseif (Tools::isSubmit('customize_reset_form_submit_btn')) {
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_HOME', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataHomeList()));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_LIST_PRODUCT', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataListProduct()));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_PAGE_PRODUCT', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataProduct()));
            Configuration::updateValue('ADP_CUSTOMIZE_TYPES_MICRODATA_OTHER_PAGES', json_encode($this->adpmicrodatos_microdatos->getDefaultOptionMicrodataOtherPages()));

            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_DESCRIPTION', '1');
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_BRAND', '1');
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_PAGE_PRODUCT_CATEGORY', '1');
            Configuration::updateValue('ADP_ACTIVE_MICRODATA_MPN_REFERENCE_SAME_VALUE', '0');

            Configuration::updateValue('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE', '1');
            Configuration::updateValue('ADP_DEFAULT_MANUFACTURER', '');
            Configuration::updateValue('ADP_SET_IMAGE_PRODUCT', '1');
            Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_TAXES', '0');
            Configuration::updateValue('ADP_SET_CONFIGURATION_PRODUCT_GTIN', '1');

            $this->adp_customize_types_microdata_home = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataHomeList();
            $this->adp_customize_types_microdata_list_product = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataListProduct();
            $this->adp_customize_types_microdata_page_product = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataProduct();
            $this->adp_customize_types_microdata_other_pages = $this->adpmicrodatos_microdatos->getDefaultOptionMicrodataOtherPages();

            $this->active_microdata_page_product_description = 1;
            $this->active_microdata_page_product_brand = 1;
            $this->active_microdata_page_product_category = 1;
            $this->active_microdata_mpn_reference_same_value = 0;
            $this->active_microdata_product_weight = 0;
            $this->desactive_microdata_product_stock = 0;
            $this->desactive_microdata_product_price = 0;
            $this->set_configuration_product_gtin = 1;

            $this->set_microdata_description_product_page = 1;
            $this->adp_default_manufacturer = '';
            $this->set_image_product = 1;
            $this->set_configuration_product_taxes = 0;

            $this->context->smarty->assign(['confirmation' => 'ok']);
            $this->active_tab = '#tab_customize';
        } else {
            $this->context->smarty->assign(
                [
                    'class_tab' => 'not-active',
                ]
            );
        }
    }

    public function formIsValid($post_data)
    {
        if (empty($post_data['view_microdata_products_related'])) {
            $post_data['view_microdata_products_related'] = 0;
        }

        if (empty($post_data['num_days_date_valid_until_product'])) {
            $post_data['num_days_date_valid_until_product'] = 0;
        }

        if (!Validate::isInt($post_data['view_microdata_products_related'])) {
            return false;
        }

        if (!Validate::isInt($post_data['num_days_date_valid_until_product'])) {
            return false;
        }

        if (!empty($post_data['ids_disable_microdata_features_product'])) {
            $ids_disable_features = explode(',', $post_data['ids_disable_microdata_features_product']);

            foreach ($ids_disable_features as $key => $value) {
                $aux_feature = new Feature($value);
                if (!Validate::isLoadedObject($aux_feature)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    public function getLangForDoc()
    {
        $doc_langs_available = ['FR', 'ES', 'EN', 'DE', 'IT', 'RU'];
        foreach ($doc_langs_available as $lang) {
            if (Tools::strtoupper($this->context->language->iso_code) == $lang) {
                return $lang;
            }
        }

        return 'EN';
    }

    public function isActiveMicrodataOrganizationLocalbusiness($types_microdata_list)
    {
        if (!in_array(self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS, $types_microdata_list)) {
            return;
        }

        $isos_idiomas = [];
        $nombres_idiomas = [];
        $idiomas = Language::getLanguages(true, Context::getContext()->shop->id);
        foreach ($idiomas as $idioma) {
            $isos_idiomas[$idioma['id_lang']] = $idioma['iso_code'];
            $nombres_idiomas[] = $idioma['name'];
        }

        $datos_shop = $this->adpmicrodatos_microdatos->getDatosShop($this->id_shop);
        $this->smarty->assign('telefono_comercio', $datos_shop['telefono_comercio']);
        $this->smarty->assign('email_comercio', $datos_shop['email_comercio']);
        $this->smarty->assign('addressLocality', $datos_shop['addressLocality']);
        $this->smarty->assign('addressRegion', $datos_shop['addressRegion']);
        $this->smarty->assign('postalCode', $datos_shop['postalCode']);
        $this->smarty->assign('streetAddress', $datos_shop['streetAddress']);
        $this->smarty->assign('country', $datos_shop['country']);
        $this->smarty->assign('region', $datos_shop['region']);
        $this->smarty->assign('latitude', $datos_shop['latitude']);
        $this->smarty->assign('longitude', $datos_shop['longitude']);
        $this->smarty->assign('min_price', $datos_shop['min_price']);
        $this->smarty->assign('max_price', $datos_shop['max_price']);
        $this->smarty->assign('iso_code', $isos_idiomas[$this->context->language->id]);
        $this->smarty->assign('nombre_idiomas_disponibles', $nombres_idiomas);
    }

    public function isActiveMicrodataStore($types_microdata_list)
    {
        if (!in_array(self::$TYPE_MICRODATA_STORE, $types_microdata_list)) {
            $this->smarty->assign('active_microdata_store', '0');

            return;
        }

        $tiendas = $this->adpmicrodatos_microdatos->getStores($this->set_microdata_store_character_separation_hours);
        $tipo_tienda = 'Store';
        if (!$this->set_microdata_store) {
            $tipo_tienda = 'LocalBusiness';
        }
        $this->smarty->assign('tiendas', $tiendas);
        $this->smarty->assign('tipo_tienda', $tipo_tienda);
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->css_path . 'adpmicrodatos.css');
            $this->context->controller->addCSS($this->css_path . 'datatables.min.css');
            $this->context->controller->addJS($this->js_path . 'adpmicrodatos.js');
            $this->context->controller->addJS($this->js_path . 'datatables.min.js');
        }
    }

    public function hookHeader($params)
    {
        return $this->displayJsonLD($params);
    }

    public function hookDisplayHeader($params)
    {
        return $this->displayJsonLD($params);
    }

    public function displayJsonLD($params)
    {
        if ('0' == Configuration::get('STAGE_NUMBER_INSTALLATION')) {
            return;
        }

        // Obtenemos el controlador de la pagina
        $page_name = Tools::getValue('controller');

        // Filtros
        $aux_pages_without_microdata = explode(',', $this->adp_pages_without_microdata);
        $aux_ids_product_without_microdata = explode(',', $this->adp_ids_product_without_microdata);
        $aux_ids_manufacturers_without_microdata = explode(',', $this->adp_ids_manufacturers_without_microdata);
        $aux_ids_categories_without_microdata = explode(',', $this->adp_ids_categories_without_microdata);

        if ($aux_pages_without_microdata && in_array($page_name, $aux_pages_without_microdata)) {
            return;
        }

        switch ($page_name) {
            case self::$PAGE_CATEGORY:
                $id_categoria = (int) Tools::getValue('id_category');

                if ($aux_ids_categories_without_microdata && in_array($id_categoria, $aux_ids_categories_without_microdata)) {
                    return;
                }
                break;
            case self::$PAGE_PRODUCT:
                $id_product = (int) Tools::getValue('id_product');

                if ($aux_ids_product_without_microdata && in_array($id_product, $aux_ids_product_without_microdata)) {
                    return;
                }

                $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

                if (Validate::isLoadedObject($product)) {
                    if (!empty($product->id_category_default) && in_array($product->id_category_default, $aux_ids_categories_without_microdata)) {
                        return;
                    }

                    if (!empty($product->id_manufacturer) && in_array($product->id_manufacturer, $aux_ids_manufacturers_without_microdata)) {
                        return;
                    }
                }

                break;
            case self::$PAGE_MANUFACTURER:
                $id_manufacturer = (int) Tools::getValue('id_manufacturer');

                if ($aux_ids_manufacturers_without_microdata && in_array($id_manufacturer, $aux_ids_manufacturers_without_microdata)) {
                    return;
                }
                break;
        }

        // Inicializando parámetros globales
        if (!$this->set_microdata_organization) {
            $this->smarty->assign('is_microdata_organization', '0');
            $this->smarty->assign('is_microdata_localbusiness', '1');
        } else {
            $this->smarty->assign('is_microdata_organization', '1');
            $this->smarty->assign('is_microdata_localbusiness', '0');
        }
        $this->smarty->assign('active_microdata_organization', $this->active_microdata_organization);
        $this->smarty->assign('active_microdata_webpage', $this->active_microdata_webpage);
        $this->smarty->assign('active_microdata_website', $this->active_microdata_website);
        $this->smarty->assign('active_microdata_store', $this->active_microdata_store);
        $this->smarty->assign('set_microdata_store_character_separation_hours', $this->set_microdata_store_character_separation_hours);

        $this->smarty->assign('active_microdata_breadcrumbs', $this->active_microdata_breadcrumbs);
        $this->smarty->assign('active_microdata_page_product', $this->active_microdata_page_product);
        $this->smarty->assign('active_microdata_features_product', $this->active_microdata_features_product);
        $this->smarty->assign('num_days_date_valid_until_product', $this->num_days_date_valid_until_product);
        $this->smarty->assign('active_microdata_list_product', $this->active_microdata_list_product);
        $this->smarty->assign('active_microdata_rich_snippets', $this->active_microdata_rich_snippets);
        $this->smarty->assign('active_microdata_product_weight', $this->active_microdata_product_weight);
        $this->smarty->assign('desactive_microdata_product_stock', $this->desactive_microdata_product_stock);
        $this->smarty->assign('desactive_microdata_product_price', $this->desactive_microdata_product_price);
        $this->smarty->assign('id_feature_3d_model', $this->adp_id_feature_3d_model);
        $this->smarty->assign('adp_default_manufacturer', $this->adp_default_manufacturer);
        $this->smarty->assign('set_configuration_product_gtin', $this->set_configuration_product_gtin);
        $this->smarty->assign('set_microdata_type_combinations_product', $this->set_microdata_type_combinations_product);

        // Politicas de devolucion
        $this->smarty->assign('iso_code_country_selected', strtolower($this->context->country->iso_code));
        $this->smarty->assign('refund_policy_information', $this->adp_return_policy_information[strtolower($this->context->country->iso_code)]);

        // Detalles de envío
        $this->smarty->assign('active_microdata_shipping_details', $this->active_microdata_shipping_details);
        $this->smarty->assign('adp_shipping_details_shipping_rate', $this->adp_shipping_details_shipping_rate);
        $this->smarty->assign('adp_shipping_details_address_country', $this->adp_shipping_details_address_country);
        $this->smarty->assign('adp_shipping_details_delivery_handling_time_min', $this->adp_shipping_details_delivery_handling_time_min);
        $this->smarty->assign('adp_shipping_details_delivery_handling_time_max', $this->adp_shipping_details_delivery_handling_time_max);
        $this->smarty->assign('adp_shipping_details_transit_handling_time_min', $this->adp_shipping_details_transit_handling_time_min);
        $this->smarty->assign('adp_shipping_details_transit_handling_time_max', $this->adp_shipping_details_transit_handling_time_max);

        $this->smarty->assign('is_p177', version_compare(_PS_VERSION_, '1.7.7', '>='));
        $this->smarty->assign('module_version', $this->version);

        switch ($page_name) {
            case self::$PAGE_PRICESDROP:
            case self::$PAGE_BESTSALES:
            case self::$PAGE_NEWPRODUCTS:
                if ($this->active_microdata_breadcrumbs && in_array(self::$TYPE_MICRODATA_BREADCRUMBLIST, $this->adp_customize_types_microdata_list_product)) {
                    $relate_pages = [self::$PAGE_PRICESDROP => 'prices-drop', self::$PAGE_BESTSALES => 'best-sales', self::$PAGE_NEWPRODUCTS => 'new-products'];

                    $result = $this->adpmicrodatos_microdatos->getBreadCrumbsFromPage($relate_pages[$page_name]);

                    if (!empty($result)) {
                        $this->smarty->assign('categorias', $result);
                        $this->smarty->assign('img_category', '');
                    }
                }

                if ($this->active_microdata_list_product && in_array(self::$TYPE_MICRODATA_ITEMLIST, $this->adp_customize_types_microdata_list_product)) {
                    $listados_productos = $this->adpmicrodatos_microdatos->getListadosProductosFromCategory(null, $page_name, $this->adp_product_image_type);

                    if (!empty($listados_productos)) {
                        $this->smarty->assign('listados_productos', $listados_productos);
                    }
                }

                if ($this->active_microdata_organization) {
                    $this->isActiveMicrodataOrganizationLocalbusiness($this->adp_customize_types_microdata_list_product);
                    // Valoraciones Rich Snippets
                    if ($this->active_microdata_rich_snippets) {
                        $valoraciones = AdpmicrodatosRichsnippets::getShopRichSnippets();
                        if (!empty($valoraciones)) {
                            $this->smarty->assign([
                                'shopreviews' => $valoraciones,
                            ]);
                        }
                    }
                }

                if ($this->active_microdata_store) {
                    $this->isActiveMicrodataStore($this->adp_customize_types_microdata_list_product);
                }

                if (!in_array(self::$TYPE_MICRODATA_WEBPAGE, $this->adp_customize_types_microdata_list_product)) {
                    $this->smarty->assign('active_microdata_webpage', 'no');
                }
                $this->smarty->assign('active_microdata_page_product', 'no');
                break;
            case self::$PAGE_CATEGORY:
                $id_categoria = (int) Tools::getValue('id_category');

                $categoria = new Category($id_categoria, $this->context->language->id, $this->context->shop->id);

                if (Validate::isLoadedObject($categoria)) {
                    if ($this->active_microdata_breadcrumbs && in_array(self::$TYPE_MICRODATA_BREADCRUMBLIST, $this->adp_customize_types_microdata_list_product)) {
                        list($categorias, $img_category) = $this->adpmicrodatos_microdatos->getCategorias($id_categoria, $this->adp_category_image_type, $this->active_microdata_rootcategory, $this->active_microdata_homecategory);
                        $this->smarty->assign('categorias', $categorias);
                        $this->smarty->assign('img_category', $img_category);
                    }

                    if ($this->active_microdata_list_product && in_array(self::$TYPE_MICRODATA_ITEMLIST, $this->adp_customize_types_microdata_list_product)) {
                        $listados_productos = $this->adpmicrodatos_microdatos->getListadosProductosFromCategory($id_categoria, $page_name, $this->adp_product_image_type);
                        if (!empty($listados_productos)) {
                            $this->smarty->assign('listados_productos', $listados_productos);
                        }
                    }
                }
                if ($this->active_microdata_organization) {
                    $this->isActiveMicrodataOrganizationLocalbusiness($this->adp_customize_types_microdata_list_product);
                    // Valoraciones Rich Snippets
                    if ($this->active_microdata_rich_snippets) {
                        $valoraciones = AdpmicrodatosRichsnippets::getShopRichSnippets();
                        if (!empty($valoraciones)) {
                            $this->smarty->assign([
                                'shopreviews' => $valoraciones,
                            ]);
                        }
                    }
                }
                if ($this->active_microdata_store) {
                    $this->isActiveMicrodataStore($this->adp_customize_types_microdata_list_product);
                }
                if (!in_array(self::$TYPE_MICRODATA_WEBPAGE, $this->adp_customize_types_microdata_list_product)) {
                    $this->smarty->assign('active_microdata_webpage', 'no');
                }

                $this->smarty->assign('active_microdata_page_product', 'no');
                break;
            case self::$PAGE_MANUFACTURER:
                $id_manufacturer = (int) Tools::getValue('id_manufacturer');

                if ($this->active_microdata_breadcrumbs && in_array(self::$TYPE_MICRODATA_BREADCRUMBLIST, $this->adp_customize_types_microdata_list_product)) {
                    $categorias = (0 == $id_manufacturer)
                        ? $this->adpmicrodatos_microdatos->getBreadCrumbsFromPage('manufacturer')
                        : $this->adpmicrodatos_microdatos->getCategoriasFabricante($id_manufacturer, $this->active_microdata_rootcategory, $this->active_microdata_homecategory);
                    $this->smarty->assign('categorias', $categorias);
                }

                if (!empty($id_manufacturer) && in_array(self::$TYPE_MICRODATA_ITEMLIST, $this->adp_customize_types_microdata_list_product)) {
                    $listados_productos = $this->adpmicrodatos_microdatos->getListadosProductosFromManufacturer($id_manufacturer, $this->adp_manufacturer_image_type);

                    if (!empty($listados_productos)) {
                        $this->smarty->assign('listados_productos', $listados_productos);
                    }

                    $this->smarty->assign('id_fabricante', $id_manufacturer);
                }
                if ($this->active_microdata_organization) {
                    $this->isActiveMicrodataOrganizationLocalbusiness($this->adp_customize_types_microdata_list_product);
                    // Valoraciones Rich Snippets
                    if ($this->active_microdata_rich_snippets) {
                        $valoraciones = AdpmicrodatosRichsnippets::getShopRichSnippets();
                        if (!empty($valoraciones)) {
                            $this->smarty->assign([
                                'shopreviews' => $valoraciones,
                            ]);
                        }
                    }
                }
                if ($this->active_microdata_store) {
                    $this->isActiveMicrodataStore($this->adp_customize_types_microdata_list_product);
                }
                if (!in_array(self::$TYPE_MICRODATA_WEBPAGE, $this->adp_customize_types_microdata_list_product)) {
                    $this->smarty->assign('active_microdata_webpage', 'no');
                }
                $this->smarty->assign('active_microdata_page_product', 'no');
                break;
            case self::$PAGE_PRODUCT:
                $id_product = (int) Tools::getValue('id_product');

                $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

                if (Validate::isLoadedObject($product)) {
                    if (!empty($product->id_category_default) && in_array(self::$TYPE_MICRODATA_BREADCRUMBLIST, $this->adp_customize_types_microdata_page_product)) {
                        list($categorias, $img_category) = $this->adpmicrodatos_microdatos->getCategorias($product->id_category_default, $this->adp_category_image_type, $this->active_microdata_rootcategory, $this->active_microdata_homecategory);
                        $this->smarty->assign('categorias', $categorias);
                    }

                    if ($this->active_microdata_page_product && in_array(self::$TYPE_MICRODATA_PRODUCT, $this->adp_customize_types_microdata_page_product)) {
                        $datos_producto = $this->adpmicrodatos_microdatos->getDatosProducto($product, $this->adp_product_image_type);

                        $datos_combinaciones_producto = [];

                        if ($this->set_microdata_type_combinations_product) {
                            $getDataProduct = $this->context->controller->getTemplateVarProduct();
                            $id_product_combination = $getDataProduct->id_product_attribute;
                            if (!empty($id_product_combination)) {
                                $datos_producto = $this->adpmicrodatos_microdatos->getDatosCombinacionProducto($product, $id_product_combination, $this->adp_product_image_type);
                            }
                        } else {
                            if (!empty($product->hasAttributes())) {
                                $datos_combinaciones_producto = $this->adpmicrodatos_microdatos->getDatosCombinaciones($product, $this->adp_product_image_type);

                                $datos_producto['inProductGroupWithID'] = $product->id;
                            } else {
                                $this->smarty->assign('set_microdata_type_combinations_product', '1');
                            }
                        }

                        if (!empty($this->adp_num_products_related)) {
                            $productos_relacionados = $this->adpmicrodatos_microdatos->getProductosRelacionados($product, $this->adp_num_products_related, $this->adp_product_image_type, $this->active_microdata_mpn_reference_same_value);
                            if (!empty($productos_relacionados)) {
                                foreach ($productos_relacionados as &$producto_relacionado) {
                                    $producto_relacionado['valoraciones'] = AdpmicrodatosRichsnippets::getRichSnippetsFromProduct($producto_relacionado['id_product']);
                                }

                                $this->smarty->assign('productos_relacionados', $productos_relacionados);
                            }
                        }

                        // Valoraciones Rich Snippets
                        if ($this->active_microdata_rich_snippets) {
                            $valoraciones = AdpmicrodatosRichsnippets::getRichSnippetsFromProduct($id_product);

                            if (!empty($valoraciones)) {
                                $this->smarty->assign([
                                    'reviews' => $valoraciones,
                                ]);
                            }
                        }

                        $caracteristicas = $this->adpmicrodatos_microdatos->getCaracteristicas($id_product, $this->adp_ids_disable_microdata_features_product);

                        $caracteristica_3d_model = $this->adpmicrodatos_microdatos->getCaracteristicaById($id_product, $this->adp_id_feature_3d_model);

                        $this->smarty->assign('inProductGroupWithID', $datos_producto['inProductGroupWithID']);
                        $this->smarty->assign('nombre', $datos_producto['nombre']);
                        $this->smarty->assign('imagenes', $datos_producto['imagenes']);
                        $this->smarty->assign('caracteristicas_producto', $caracteristicas);
                        $this->smarty->assign('caracteristica_3d_model', $caracteristica_3d_model);
                        $this->smarty->assign('url', $datos_producto['url']);
                        $this->smarty->assign('id_product', $datos_producto['id_product']);
                        $this->smarty->assign('ean13', $datos_producto['ean13']);
                        $this->smarty->assign('upc', $datos_producto['upc']);
                        $this->smarty->assign('isbn', $datos_producto['isbn']);
                        $this->smarty->assign('category', ($this->active_microdata_page_product_category) ? $datos_producto['category'] : '');
                        $this->smarty->assign('mpn', ($this->active_microdata_mpn_reference_same_value) ? $datos_producto['sku'] : $datos_producto['mpn']);
                        $this->smarty->assign('sku', $datos_producto['sku']);
                        $this->smarty->assign('fabricante', (!empty($datos_producto['fabricante']) && $this->active_microdata_page_product_brand) ? $datos_producto['fabricante'] : '');
                        $this->smarty->assign('condition', $datos_producto['condition']);
                        $this->smarty->assign('description', ($this->active_microdata_page_product_description) ? $datos_producto['description'] : '');
                        $this->smarty->assign('quantity', $datos_producto['quantity']);
                        $this->smarty->assign('productPrice', $datos_producto['productPrice']);
                        $this->smarty->assign('moneda', $datos_producto['moneda']);
                        $this->smarty->assign('weight', $datos_producto['weight']);
                        $this->smarty->assign('weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
                        $this->smarty->assign('fechaValidaHasta', $datos_producto['fechaValidaHasta']);
                        $this->smarty->assign('permitir_pedidos_fuera_stock', $datos_producto['permitir_pedido_fuera_stock']);
                        $this->smarty->assign('combinations', $datos_combinaciones_producto);
                    } else {
                        $this->smarty->assign('active_microdata_page_product', 'no');
                    }
                } else {
                    $this->smarty->assign('active_microdata_page_product', 'no');
                }

                if ($this->active_microdata_organization) {
                    $this->isActiveMicrodataOrganizationLocalbusiness($this->adp_customize_types_microdata_page_product);
                    // Valoraciones Rich Snippets
                    if ($this->active_microdata_rich_snippets) {
                        $valoraciones = AdpmicrodatosRichsnippets::getShopRichSnippets();
                        if (!empty($valoraciones)) {
                            $this->smarty->assign([
                                'shopreviews' => $valoraciones,
                            ]);
                        }
                    }
                }
                if ($this->active_microdata_store) {
                    $this->isActiveMicrodataStore($this->adp_customize_types_microdata_page_product);
                }
                if (!in_array(self::$TYPE_MICRODATA_WEBPAGE, $this->adp_customize_types_microdata_page_product)) {
                    $this->smarty->assign('active_microdata_webpage', 'no');
                }
                break;
            default:
                if ($this->active_microdata_organization) {
                    $this->isActiveMicrodataOrganizationLocalbusiness($this->adp_customize_types_microdata_other_pages);
                    // Valoraciones Rich Snippets
                    if ($this->active_microdata_rich_snippets) {
                        $valoraciones = AdpmicrodatosRichsnippets::getShopRichSnippets();
                        if (!empty($valoraciones)) {
                            $this->smarty->assign([
                                'shopreviews' => $valoraciones,
                            ]);
                        }
                    }
                }
                if ($this->active_microdata_store) {
                    $this->isActiveMicrodataStore($this->adp_customize_types_microdata_other_pages);
                }
                if (!in_array(self::$TYPE_MICRODATA_WEBPAGE, $this->adp_customize_types_microdata_other_pages)) {
                    $this->smarty->assign('active_microdata_webpage', 'no');
                }
                $this->smarty->assign('active_microdata_page_product', 'no');
                $this->smarty->assign('active_microdata_breadcrumbs', 'no');
                break;
        }
        if (!Tools::getIsset('debug_adp_microdatos')) {
            return $this->display(__FILE__, 'index_microdata.tpl');
        }

        echo Tools::safeOutput(print_r($this->display(__FILE__, 'index_microdata.tpl')));
    }

    public function getDiff()
    {
        header('Content-Type: application/json');

        $filePath = $_REQUEST['filePath'];
        $diff = AdpMicrodatosDiff::compareFiles($filePath . ThemeFiles::$BACKUP_FILE_EXTENSION, $filePath);

        echo json_encode($diff);
    }
}
