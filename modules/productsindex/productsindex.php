<?php
/**
 * 2007 - 2017 ZLab Solutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future. If you wish to customize module for your
 * needs please contact developer at http://zlabsolutions.com for more information.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLab Solutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions https://www.facebook.com/ZlabSolutions/
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

ini_set('max_execution_time', 3000);

include_once _PS_MODULE_DIR_ . 'productsindex/classes/Zlabcustom.class.php';
include_once _PS_MODULE_DIR_ . 'productsindex/classes/ajaxController.php';
include_once _PS_MODULE_DIR_ . 'productsindex/classes/ProductsIndex.class.php';
// include_once(_PS_MODULE_DIR_.'productsindex/productsindex.php');

/*
function productsindexShutDownFunction()
{
    $error = error_get_last();
    if (($error['type'] === E_ERROR) || ($error['type'] === E_WARNING) || ($error['type'] === E_NOTICE)) {
        if ((stripos($error['message'], 'productsindex') != false) ||
            (stripos($error['file'], 'productsindex') != false)
        ) {
            file_put_contents(_PS_MODULE_DIR_.'/productsindex/cache/errors.txt', print_r($error, true), FILE_APPEND);
        }
    }
}

register_shutdown_function('productsindexShutDownFunction');
*/

class Productsindex extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'productsindex';
        $this->tab = 'quick_bulk_update';
        $this->version = '2.4.1';
        $this->author = 'EALab Solutions';
        $this->need_instance = 0;
        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();
        $this->module_key = '9e72d182e457c2912b66007ea056c787';
        $this->author_address = '0xc920f15eA4F84113D40E6C06FE2AD52AB7F974dD';
        $this->displayName = $this->l('Move and Sort Products Positions in Categories and Brands');
        $this->description = $this->l(
            'Edit products positions in Categories and Brands, Drag&Drop products display order.'
        );

        $this->confirmUninstall = $this->l('Uninstall Module?');

        $this->ps_versions_compliancy = ['min' => '1.6.0.0', 'max' => _PS_VERSION_];
    }

    public function reset()
    {
        $have_old_table = $this->backupModuleTables();
        include _PS_MODULE_DIR_ . 'productsindex/sql/install.php';
        if ($have_old_table) {
            $this->restoreModuleTables();
        }
        // $this->checkModuleTab17();
        return true;
    }

    public function restoreModuleTables()
    {
        return Zlabcustomclasszl::restoreModuleTables();
    }

    public function backupModuleTables()
    {
        return Zlabcustomclasszl::backupModuleTables();
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        // $db = Db::getInstance();
        // backup possible old order table
        $have_old_table = $this->backupModuleTables();

        include _PS_MODULE_DIR_ . 'productsindex/sql/install.php';

        if ($have_old_table) {
            $this->restoreModuleTables();
        }
        // copy data from old order table

        $min_image_type = Zlabcustomclasszl::getShopMinimumImageType();
        if ($min_image_type) {
            Zlabcustomclasszl::updateSetting(2, $min_image_type);
        }

        return parent::install()
            && $this->registerHook('actionProductDelete')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displaybackOfficeHeader')
            && $this->registerHook('displayFooter')
            && $this->registerHook('actionProductSave')
            && $this->registerHook('filterManufacturerContent')
            && $this->registerHook('filterProductSearch')
            && $this->registerHook('actionManufacturerSave')
            && $this->installTab();
    }

    public function uninstall()
    {
        include _PS_MODULE_DIR_ . 'productsindex/sql/uninstall.php';

        if (Tab::getIdFromClassName('Adminproductsindex')) {
            $this->uninstallTab();
        }
        // $this->unregisterHook('displayProductPriceBlock');
        return parent::uninstall()
            && $this->unregisterHook('actionProductDelete')
            && $this->unregisterHook('displayHeader')
            && $this->unregisterHook('displaybackOfficeHeader')
            && $this->unregisterHook('displayFooter')
            && $this->unregisterHook('actionProductSave')
            && $this->unregisterHook('filterProductSearch')
            && $this->unregisterHook('filterManufacturerContent');
    }

    public function hookfilterManufacturerContent($params)
    {
        if (_PS_VERSION_ > '1.7.0.0') {
            // echo 'hookfilterProductSearch echo manufacturer 2';
            // print_r($params);
            // return $this->_updateContentVars($params['filtered_content']);
            /*
            $query = new ProductSearchQuery();
            $query
                ->setIdManufacturer($this->manufacturer->id)
                ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));
        ;
        */
        }
    }

    public function hookactionProductSave($params)
    {
        $id_product = (int) $params['id_product'];
        ProductsIndexClass::addNewProductToManufacturerList($id_product);

        return true;
    }

    public function hookactionObjectManufacturerAddAfter($params)
    {
        /*
        $id_product = (int) $params['id_product'];
        file_put_contents(_PS_MODULE_DIR_.'/productsindex/mantemp.txt', print_r($params, true));
        ProductsIndexClass::addNewProductToManufacturerList($id_product);
        */
        return true;
    }

    public function hookactionProductDelete($params)
    {
        $id_product = (int) $params['id_product'];
        ProductsIndexClass::deleteProductFromManufacturerList($id_product);

        return true;
    }

    public function hookfilterProductSearch(&$searchVariables)
    {
        // Brand view
        // return true;
        // file_put_contents(_PS_MODULE_DIR_.'/productsindex/temp1.txt', print_r($searchVariables, true));
        if (Tools::getIsset('order')) {
            return true;
        }
        if ((Context::getContext()->controller->php_self == 'manufacturer') && (_PS_VERSION_ > '1.7.0.0')) {
            // echo 'ok';
            $products = $searchVariables['searchVariables']['products'];
            $id_manufacturer = $searchVariables['searchVariables']['products'][0]['id_manufacturer'];

            if ($id_manufacturer && count($products)) {
                $page = (int) Tools::getValue('page');
                if (($page == 0) || ($page == 1) || !$page) {
                    $page = 1;
                }
                if (($page > 1)
                    && (Tools::getIsset('orderby') || Tools::getIsset('price') || Tools::getIsset('categories'))
                ) {
                    return true;
                }

                $nbrPerPage = (int) Tools::getValue('n');
                if (!$nbrPerPage) {
                    $nbrPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
                    if (!$nbrPerPage) {
                        $nbrPerPage = 12;
                    }
                }
                $index = new ProductsIndexClass();
                $sorted_products = $index->sortManufacturerPositions(
                    $id_manufacturer,
                    $products,
                    $page,
                    $nbrPerPage
                );
                // file_put_contents(_PS_MODULE_DIR_.'/productsindex/temp.txt', print_r($sorted_products, true));
                if (count($sorted_products)) {
                    $searchVariables['searchVariables']['products'] = $sorted_products;
                }
            }
        }
    }

    /* manufacturer index */
    public function hookDisplayFooter()
    {
        if ($this->context->controller->php_self == 'manufacturer') {
            if (_PS_VERSION_ < '1.7.0.0') {
                $page = (int) Tools::getValue('p');
                if (($page == 0) || ($page == 1) || !$page) {
                    $page = 1;
                }
                if (($page > 1)
                    && (Tools::getIsset('orderby') || Tools::getIsset('price') || Tools::getIsset('categories'))
                ) {
                    return true;
                }
                $vars = $this->context->smarty->getTemplateVars();

                $products = $vars['products'];
                $id_manufacturer = (int) $products[0]['id_manufacturer'];
                if (!$id_manufacturer) {
                    foreach ($products as $product_el) {
                        $id_manufacturer = $product_el['id_manufacturer'];
                        break;
                    }
                }
                if (Tools::getValue('debug_mode') == 1) {
                    // print_r($products);
                    echo "$id_manufacturer && " . count($products);
                }
                if ($id_manufacturer && count($products)) {
                    $index = new ProductsIndexClass();
                    if (Tools::getValue('debug_mode') == 1) {
                        echo _PS_VERSION_;
                        foreach ($products as $prod) {
                            echo $prod['id_product'] . '<br>';
                        }
                        echo '-----------------------------<br>';
                    }

                    $nbrPerPage = (int) Tools::getValue('n');
                    if (!$nbrPerPage) {
                        $nbrPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
                        if (!$nbrPerPage) {
                            $nbrPerPage = 12;
                        }
                    }
                    $sorted_products = $index->sortManufacturerPositions(
                        $id_manufacturer,
                        $products,
                        $page,
                        $nbrPerPage
                    );
                    /*
                    if (Tools::getValue('debug') == 1) {
                        echo "$page -- $nbrPerPage";
                        foreach ($sorted_products as $prod) {
                            echo $prod['id_product'].'<br>';
                        }
                    }
                    */
                    $this->context->smarty->assign('products', $sorted_products);
                }
            }
        }
    }

    public function hookdisplayShoppingCartFooter($params)
    {
        return '';
    }

    public function hookactionAfterDeleteProductInCart()
    {
        return true;
    }

    public function hookactionCartSave($params)
    {
        return true;
    }

    public function getContent()
    {
        $order_value = Configuration::get('PS_PRODUCTS_ORDER_BY');
        if ($order_value != 4) {
            // $this->adminDisplayWarning(
            $this->adminDisplayWarning(
                'Please change "default order by" product setting to "Position inside category".
                This is the order in which products are displayed in the product list.
                You can do that in module Settings Tab or Parameters->Product settings.'
            );
        }
        $this->context->smarty->assign(['default_order' => $order_value]);
        $debug_tab = 0;
        if (Tools::getValue('debug') == '1') {
            $debug_tab = 1;
        }

        $link = new Link();
        $zlab_ajax_link = $link->getAdminLink('Adminproductsindex');
        $admin_path = explode('/', _PS_ADMIN_DIR_);
        if (count($admin_path) < 2) {
            $admin_path = explode('\\', _PS_ADMIN_DIR_);
        }
        // echo _PS_BASE_URL_.__PS_BASE_URI__.$admin_path[count($admin_path) - 1].'/';
        $this->context->smarty->assign(['zlab_ajax_link' => $zlab_ajax_link]);

        $this->context->smarty->assign(['debug_tab' => $debug_tab]);
        $this->checkModuleTab17();
        $settings = Zlabcustomclasszl::getConfig();
        /* FIELDS FOR AJAX */
        $ajaxfields = [
            'please_select_category' => $this->l('Please select category or manufacturer first'),
            'text_index_updated' => $this->l('Index Updated'),
            'error' => $this->l('Error'),
            'text_updated' => $this->l('Updated'),
        ];
        $ajaxfields_json = json_encode($ajaxfields);
        $ajaxfields_json = rawurlencode($ajaxfields_json);
        $this->context->smarty->assign('ajaxfields', $ajaxfields_json);
        /* END FIELDS FOR AJAX */

        // $id_lang = Context::getContext()->language->id;
        if (((bool) Tools::isSubmit('submitAmazonshopModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $current_order = 1;
        $this->context->smarty->assign(['current_order' => $current_order]);
        $id_employee = $this->context->employee->id;
        // echo $id_employee;
        /*
                $token = Tools::getValue('token');
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
                $host = $protocol.$_SERVER['SERVER_NAME'];
        */
        $base_uri = __PS_BASE_URI__ == '/' ? '' : Tools::substr(__PS_BASE_URI__, 0, Tools::strlen(__PS_BASE_URI__) - 1);
        $this->context->smarty->assign('baseuri', $base_uri);
        $log = Zlabcustomclasszl::getLog();
        $this->context->smarty->assign(['log' => $log]);
        $this->context->smarty->assign(['settings' => $settings]);
        $this->context->smarty->assign(['ps_version' => _PS_VERSION_]);
        $this->context->smarty->assign(['check_e' => $id_employee]);
        $max_execution_time = 0;
        $max_execution_time = ini_get('max_execution_time');
        $max_execution_time_minutes = round($max_execution_time / 60);

        $this->context->smarty->assign(['max_execution_time' => $max_execution_time_minutes]);

        // products_index
        $manufacturers = self::getManufacturersList();
        // print_r($manufacturers);
        $this->context->smarty->assign('manufacturers', $manufacturers);

        // categories tree
        if (_PS_VERSION_ >= '1.7.0.0') {
            $tree_header_template = 'tree_associated_header.tpl';
            $tree_template = 'tree_associated_categories.tpl';
        } else {
            $tree_header_template = 'tree_header.tpl';
            $tree_template = 'tree_categories.tpl';
        }
        $categories = self::getCategoriesList();
        $selected_categories = [];
        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
        $tree->setTemplate($tree_template)
            ->setHeaderTemplate($tree_header_template)
            ->setRootCategory(Configuration::get('PS_HOME_CATEGORY'))
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setSelectedCategories($selected_categories);
        // ->setHeaderTemplate('tree_associated_header.tpl')

        $this->context->smarty->assign([
            'category_tree' => $tree->render(),
        ]);
        // end category tree
        $this->context->smarty->assign('categories', $categories);

        $image_types = ImageType::getImagesTypes(null, true);
        $this->context->smarty->assign('image_types', $image_types);

        $blank_products_list = [];
        if (_PS_VERSION_ < '8.0.0') {
            $class_name = 'Tools';
        } else {
            $class_name = 'Utils';
        }
        // call_user_func(array($class_name .'::doSomething'), $arg);
        $this->context->smarty->assign([
            'productsfound' => json_encode([
                'columns' => [
                    ['content' => $this->l('New Position'), 'center' => true],
                    ['content' => $this->l('ID'), 'key' => 'id_product', 'center' => true],
                    ['content' => $this->l('Image'), 'key' => 'image', 'center' => true],
                    ['content' => $this->l('Reference'), 'key' => 'image', 'center' => true],
                    ['content' => $this->l('Product name'), 'key' => 'name', 'center' => true],
                    ['content' => $this->l('Category default'), 'key' => 'image', 'center' => true],
                    ['content' => $this->l('Price'), 'key' => 'price', 'center' => true],
                    ['content' => $this->l('Quantity'), 'key' => 'qty', 'center' => true],
                    ['content' => $this->l('Brand'), 'key' => 'brand', 'center' => true],
                    ['content' => $this->l('Supplier'), 'key' => 'supplier', 'center' => true],
                    [
                        'content' => $this->l('Enabled'),
                        'key' => 'active',
                        'bool' => true,
                        'center' => true,
                        'fa' => true,
                    ],
                    ['content' => $this->l('Current Position'), 'center' => true],
                ],
                'rows' => $blank_products_list,
                'rows_actions' => [
                    ['title' => $this->l('Edit'), 'action' => 'massa_product_edit', 'icon' => 'pencil'],
                    ['title' => $this->l('View'), 'action' => 'massa_product_view', 'icon' => 'eye'],
                ],
                'url_params' => ['configure' => $this->name],
                'identifier' => 'id_product',
            ]),
        ]);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/prestui/ps-tags.tpl');
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/categories-tree.tpl');
        // /$output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/prestui/ps-form.tpl');
        return $output;
    }

    public static function getManufacturersList()
    {
        $manufacturers = [];
        $mans = Manufacturer::getManufacturers(false, Context::getContext()->language->id, true, false);
        // print_r($mans);
        foreach ($mans as $cat) {
            $manufacturers[] = ['id_option' => $cat['id_manufacturer'], 'name' => $cat['name']];
        }
        // print_r($manufacturers);
        return $manufacturers;
    }

    public static function getShopCategoriesAjax()
    {
        $result = [0, 'Error'];
        $id_shop = Tools::getValue('target_id_shop');
        if ($id_shop) {
            $result = [1, self::getCategoriesList($id_shop)];
        }
        echo json_encode($result);
    }

    public static function getCategoriesList($id_shop = 0)
    {
        $categories = [];
        if ($id_shop == 0) {
            $cats = Category::getCategories(Context::getContext()->language->id, false, false);
        } else {
            $cats = Category::getCategories(
                Context::getContext()->language->id,
                false,
                false,
                ' AND c.id_category IN(SELECT DISTINCT id_category FROM ' . _DB_PREFIX_ . 'category_shop 
                    WHERE id_shop=' . (int) $id_shop . ')'
            );
        }

        $k = 0;
        foreach ($cats as $cat) {
            if ($k > 0) {
                $categories[] = ['id_option' => $cat['id_category'], 'name' => $cat['name']];
            }
            ++$k;
        }

        return $categories;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookdisplayBackOfficeHeader()
    {
        if ((Tools::getValue('module_name') == $this->name) || (Tools::getValue('configure') == $this->name)) {
            if (_PS_VERSION_ > '1.7.0.0') {
                $this->context->controller->addJquery();
                // $this->context->controller->addJS($this->_path.'views/js/jquery-ui.js');
            }
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            // $this->context->controller->addJS($this->_path.'views/js/riot+compiler.min.js');
            $this->context->controller->addJS('https://cdn.jsdelivr.net/riot/2.4.1/riot+compiler.min.js');
            // $this->context->controller->addCSS('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addCSS($this->_path . 'views/css/material.css');
            $this->context->controller->addCSS($this->_path . 'views/css/animate.css');
            $this->context->controller->addJS($this->_path . 'views/js/jquery.popupoverlay.js');
            $this->context->controller->addJS($this->_path . 'views/js/chosen/chosen.jquery.js');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        if (Context::getContext()->controller->php_self == 'manufacturer') {
            // echo 'manufacturer';
        }
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }

    public function hookDisplayProductPriceBlock($params)
    {
        $output = '';

        return $output;
    }

    /* TABS */
    public function installTab()
    {
        if (_PS_VERSION_ > '1.7.0.0') {
            $this->insertTab17(
                'Adminproductsindex',
                'Products Position',
                'AdminCatalog'
            );
        } else {
            $this->installModuleTab16(
                'Adminproductsindex',
                [Configuration::get('PS_LANG_DEFAULT') => 'Products Position'],
                Tab::getIdFromClassName('AdminCatalog')
            );
        }

        return true;
    }

    public function uninstallTab()
    {
        if (_PS_VERSION_ > '1.7.0.0') {
            $this->deleteTab17('Adminproductsindex');
        } elseif (_PS_VERSION_ > '1.5.0.0') {
            $this->uninstallModuleTab16('Adminproductsindex');
        }

        return true;
    }

    /* 1.7 */
    public function checkModuleTab17()
    {
        if (_PS_VERSION_ > '1.7.0.0') {
            return $this->checkFixModuleTab17(
                $this->name,
                'Adminproductsindex',
                'AdminCatalog',
                'Product Position'
            );
        } else {
            return true;
        }
    }

    public function checkFixModuleTab17($module_name, $class_name, $parent_name, $tab_name)
    {
        $is_exists_sql = 'SELECT id_tab 
            FROM ' . _DB_PREFIX_ . 'tab 
            WHERE module=\'' . pSQL($module_name) . '\' 
                AND class_name=\'' . pSQL($class_name) . '\'';
        if ($tab = Db::getInstance()->getRow($is_exists_sql)) {
            $this->updateTab17($tab['id_tab'], $class_name, $tab_name, $parent_name);
        } else {
            $this->insertTab17($class_name, $tab_name, $parent_name);
        }

        return true;
    }

    public function updateTab17($id_tab, $class_name, $name, $parent_name)
    {
        $tab = new Tab($id_tab);
        $id_parent = Tab::getIdFromClassName($parent_name);
        if ($tab->id_parent == $id_parent) {
            return true;
        }
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = Zlabcustomclasszl::createMultiLangField($name);
        $tab->position = 0;
        if (!$tab->update()) {
            return false;
        }

        return true;
    }

    public function insertTab17($class_name, $name, $parent_name)
    {
        $tab = new Tab();
        $id_parent = Tab::getIdFromClassName($parent_name);
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        $tab->class_name = $class_name;
        $tab->name = Zlabcustomclasszl::createMultiLangField($name);
        $tab->position = 0;
        if (!$tab->save()) {
            return false;
        }

        return true;
    }

    public function deleteTab17($class_name)
    {
        $id_parent = Tab::getIdFromClassName($class_name);
        $tab = new Tab($id_parent);
        $tab->delete();

        return true;
    }

    /* 1.7 */
    public function installModuleTab16($tab_class, $tab_name, $id_tab_parent)
    {
        $this->displayConfirmation($this->l('ZLab Custom install'));
        // Tools::copy(_PS_MODULE_DIR_.$this->name.'/logo.png', _PS_IMG_DIR_.'t/'.$tab_class.'.png');
        $tab = new Tab();
        $tab->name = $tab_name;
        $tab->class_name = $tab_class;
        $tab->module = $this->name;
        // for 1.5
        if (stripos(_PS_VERSION_, '.5.') == 1) {
            $id_tab_parent = 16;
        }

        $tab->id_parent = $id_tab_parent;
        if (!$tab->save()) {
            return false;
        }

        return true;
    }

    public function uninstallModuleTab16($tab_class)
    {
        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            @unlink(_PS_IMG_DIR . 't/' . $tab_class . '.png');

            return true;
        }

        return false;
    }
    /* END TABS */
}
