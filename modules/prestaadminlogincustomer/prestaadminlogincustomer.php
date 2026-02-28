<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    die('VERSION NOT EXIST');
}

include_once 'classes/PrestaCustomerLoggedIn.php';

class PrestaAdminLoginCustomer extends Module
{
    public function __construct()
    {
        $this->name = 'prestaadminlogincustomer';
        $this->tab = 'administration';
        $this->version = '8.0.2';
        $this->author = 'presta_world';
        $this->bootstrap = true;
        $this->module_key = '74478fdd3c091462d00e108b900503cb';
        parent::__construct();
        $this->displayName = $this->l('Customer Login');
        $this->description = $this->l('Admin can login to customer account without password');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('PRESTA_CUSTOMER_LOGIN_FRONT_NAVI')) {
            $idCustomer = $this->context->customer->id;
            $cookieValue = 'presta_'.$idCustomer;
            if ($idCustomer && isset($this->context->cookie->{$cookieValue})) {
                $this->context->smarty->assign(
                    array(
                        'presta_customer' => $this->context->customer,
                    )
                );
                $this->context->controller->addCSS(
                    _MODULE_DIR_.$this->name.'/views/css/prestalogincustomer.css'
                );
                return $this->display(__FILE__, 'presta_front_login_header.tpl');
            }
        }
    }

    public function hookActionAuthentication($params)
    {
        if (isset($params['customer']->id)) {
            $cookieValue = 'presta_'.$params['customer']->id;
            if (isset($this->context->cookie->{$cookieValue})) {
                unset($this->context->cookie->{$cookieValue});
                $this->context->cookie->write();
            }
        }
    }

    public function hookDisplayAdminCustomers($params)
    {
        if (Configuration::get('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL') && _PS_VERSION_ >= '1.7.6.0') {
            if ($this->checkPermission()) {
                $idCustomer = $params['id_customer'];
                $customer = new Customer($idCustomer);
                $this->context->smarty->assign(
                    array(
                        'id_customer' => $params['id_customer'],
                        'customer' => $customer
                    )
                );
                return $this->display(__FILE__, 'presta_customer_detail.tpl');
            }
        }
    }

    public function hookDisplayBackOfficeTop()
    {
        if (Configuration::get('PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER')) {
            if ($this->checkPermission()) {
                Media::addJsDef(
                    array(
                        'presta_customer_search' => $this->context->link->getAdminLink('AdminPrestaCustomer'),
                    )
                );
                $this->context->smarty->assign(
                    array(
                        'modules_dir' => _MODULE_DIR_,
                    )
                );
                if (Configuration::get('PRESTA_CUSTOMER_LOGIN_HISTORY')) {
                    $this->context->smarty->assign(
                        array(
                            'presta_log' => PrestaCustomerLoggedIn::getRecords(),
                            'presta_cust' => $this->context->link->getAdminLink('AdminPrestaCustomer')
                        )
                    );
                }
                return $this->display(__FILE__, 'presta_customer_login_top.tpl');
            }
        }
    }

    public function hookDisplayAdminOrderLeft($params)
    {
        if (Configuration::get('PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL')) {
            if ($this->checkPermission()) {
                $idOrder = $params['id_order'];
                if ($idOrder) {
                    $order = new Order($idOrder);
                    $customer = new Customer($order->id_customer);
                    $this->context->smarty->assign(
                        array(
                            'customer' => $customer,
                            'href' => $this->context->link->getAdminLink('AdminCustomers') .
                                '&logincustomer=1&id_customer=' .
                                (int) $order->id_customer,
                        )
                    );
                    return $this->display(__FILE__, 'presta_order_customer_login.tpl');
                }
            }
        }
    }

    private function checkPermission()
    {
        $allow = false;
        $permission = Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION');
        if ($permission && Configuration::get('PRESTA_CUSTOMER_AS_LOGIN')) {
            $permission = json_decode($permission);
            if ($permission) {
                foreach ($permission as $profile) {
                    if ($profile == $this->context->employee->id_profile) {
                        $allow = true;
                        break;
                    }
                }
            }
        }
        return $allow;
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->smarty->assign(
            array(
                'modules_dir' => _MODULE_DIR_,
            )
        );
        $this->context->controller->addJS(
            _MODULE_DIR_ . $this->name . '/views/js/presta_customer_login.js'
        );
        $this->context->controller->addCss(
            _MODULE_DIR_ . $this->name . '/views/css/prestalogincustomer.css'
        );
    }

    public function getContent()
    {
        $this->_html = '';
        if (Tools::isSubmit('btnSubmit')) {
            $this->validatePermission();
            if (empty($this->_postErrors)) {
                $this->postProcess();
            } else {
                if ($this->_postErrors) {
                    $this->_html .= $this->displayError($this->_postErrors);
                }
            }
        } else {
            $this->_html .= '<br />';
        }
        if (Tools::getIsset('deletepresta_logged_in_details')) {
            $id = Tools::getValue('id_presta_logged_in_details');
            $objCustomerLoggedIn = new PrestaCustomerLoggedIn($id);
            if (Validate::isLoadedObject($objCustomerLoggedIn)) {
                if ($objCustomerLoggedIn->delete()) {
                    $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
                        Tools::redirectAdmin(
                            $this->context->link->getAdminLink('AdminModules') .
                            '&configure=' .
                            $this->name .
                            '&tab_module=' .
                            $this->tab .
                            '&module_name=' .
                            $this->name .
                            '&conf=2'
                        );
                }
            }
        }
        $this->_html .= $this->renderForm();
        if (Configuration::get('PRESTA_CUSTOMER_LOGIN_HISTORY')) {
            $this->_html .= $this->renderList();
        }
        $this->context->controller->addJS(
            _MODULE_DIR_ . 'prestaadminlogincustomer/views/js/presta_customer_login.js'
        );
        return $this->_html;
    }

    private function validatePermission()
    {
        if (Tools::getValue('PRESTA_CUSTOMER_AS_LOGIN') && empty(Tools::getValue('groupBox'))) {
            $this->_postErrors[] = $this->l('Please choose at least one profile');
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue(
                'PRESTA_CUSTOMER_AS_LOGIN',
                Tools::getValue('PRESTA_CUSTOMER_AS_LOGIN')
            );
            Configuration::updateValue(
                'PRESTA_CUSTOMER_LOGIN_FRONT_NAVI',
                Tools::getValue('PRESTA_CUSTOMER_LOGIN_FRONT_NAVI')
            );
            Configuration::updateValue(
                'PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL',
                Tools::getValue('PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL')
            );
            Configuration::updateValue(
                'PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL',
                Tools::getValue('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL')
            );
            Configuration::updateValue(
                'PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER',
                Tools::getValue('PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER')
            );
            Configuration::updateValue(
                'PRESTA_CUSTOMER_LOGIN_HISTORY',
                Tools::getValue('PRESTA_CUSTOMER_LOGIN_HISTORY')
            );
            Configuration::updateValue(
                'PRESTA_LOGIN_CUSTOMER_PERMISSION',
                json_encode(Tools::getValue('groupBox'))
            );
        }

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules') .
            '&configure=' .
            $this->name .
            '&tab_module=' .
            $this->tab .
            '&module_name=' .
            $this->name .
            '&conf=4'
        );
    }

    public function renderForm()
    {
        $profiles = array();
        foreach (Profile::getProfiles($this->context->language->id) as $profil) {
            $profiles[] = array('id_group' => $profil['id_profile'], 'name' => $profil['name']);
        }

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Login as Customer Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Login As Customer'),
                    'name' => 'PRESTA_CUSTOMER_AS_LOGIN',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l('Enable login as customer feature in backoffice'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Allow permission to'),
                    'name' => 'groupBox',
                    'values' => $profiles,
                    'required' => true,
                    'col' => '6',
                    'form_group_class' => 'presta_check',
                    'hint' => $this->l('Select all the profiles that you would like to give
                        permission to login as customer.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show In Front End Navigation'),
                    'name' => 'PRESTA_CUSTOMER_LOGIN_FRONT_NAVI',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l('Show in front end navigation bar when admin logged in
                        as customer account'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show In Admin Order Detail Page'),
                    'name' => 'PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l('Show login as customer on order detail page'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show In Admin Customer Detail Page'),
                    'name' => 'PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l(
                        'Show login as customer on customer detail page andcustomer list page'
                    ),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show In Backend Admin Header'),
                    'name' => 'PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l('Show customer login on backend header'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Manage History of Logged In'),
                    'name' => 'PRESTA_CUSTOMER_LOGIN_HISTORY',
                    'is_bool' => true,
                    'required' => true,
                    'hint' => $this->l('Keep the history of logged in customers'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex .
            '&configure=' .
            $this->name .
            '&tab_module=' .
            $this->tab .
            '&module_name=' .
            $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfiguationValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    public function renderList()
    {
        $fields_list = array(
            'id_presta_logged_in_details' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'id_customer' => array(
                'title' => 'Id Customer',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'cust_name' => array(
                'title' => 'Customer Name'
            ),
            'cust_email' => array(
                'title' => 'Customer Email'
            ),
            'emp_name' => array(
                'title' => 'Employee Name'
            ),
            'login_date' => array(
                'title' => 'Logged-in Date'
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array("delete");
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = PrestaCustomerLoggedIn::getLoggedInDetails(true);
        $helper->identifier = 'id_presta_logged_in_details';
        $helper->title = $this->l('History of Logged-In Customers');
        $helper->table = 'presta_logged_in_details';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList(PrestaCustomerLoggedIn::getLoggedInDetails(), $fields_list);
    }

    public function getConfiguationValues()
    {
        $configuration = array(
            'PRESTA_CUSTOMER_AS_LOGIN' => Tools::getValue(
                'PRESTA_CUSTOMER_AS_LOGIN',
                Configuration::get('PRESTA_CUSTOMER_AS_LOGIN')
            ),
            'PRESTA_CUSTOMER_LOGIN_FRONT_NAVI' => Tools::getValue(
                'PRESTA_CUSTOMER_LOGIN_FRONT_NAVI',
                Configuration::get('PRESTA_CUSTOMER_LOGIN_FRONT_NAVI')
            ),
            'PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL' => Tools::getValue(
                'PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL',
                Configuration::get('PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL')
            ),
            'PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL' => Tools::getValue(
                'PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL',
                Configuration::get('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL')
            ),
            'PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER' => Tools::getValue(
                'PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER',
                Configuration::get('PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER')
            ),
            'PRESTA_CUSTOMER_LOGIN_HISTORY' => Tools::getValue(
                'PRESTA_CUSTOMER_LOGIN_HISTORY',
                Configuration::get('PRESTA_CUSTOMER_LOGIN_HISTORY')
            ),
        );

        if (Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION')) {
            $selectProfile = json_decode(Configuration::get('PRESTA_LOGIN_CUSTOMER_PERMISSION'));
        }
        if ($selectProfile) {
            foreach (Profile::getProfiles($this->context->language->id) as $profil) {
                if (in_array($profil['id_profile'], $selectProfile)) {
                    $configuration['groupBox_' . $profil['id_profile']] = $profil['id_profile'];
                } else {
                    $configuration['groupBox_' . $profil['id_profile']] = '';
                }
            }
        } else {
            foreach (Profile::getProfiles($this->context->language->id) as $profil) {
                $configuration['groupBox_' . $profil['id_profile']] = '';
            }
        }
        return $configuration;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminPrestaCustomer', 'PrestaAdminLoginCustomer');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = -1;
        }

        $tab->module = $this->name;
        return $tab->add();
    }

    private function updateConfiguration()
    {
        $permission = array();
        foreach (Profile::getProfiles($this->context->language->id) as $profile) {
            $permission[] = $profile['id_profile'];
        }
        Configuration::updateValue('PRESTA_CUSTOMER_AS_LOGIN', 1);
        Configuration::updateValue('PRESTA_CUSTOMER_LOGIN_FRONT_NAVI', 1);
        Configuration::updateValue('PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL', 1);
        Configuration::updateValue('PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL', 1);
        Configuration::updateValue('PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER', 1);
        Configuration::updateValue('PRESTA_CUSTOMER_LOGIN_HISTORY', 1);
        Configuration::updateValue('PRESTA_LOGIN_CUSTOMER_PERMISSION', json_encode($permission));

        return true;
    }

    private function updateHook()
    {
        return $this->registerHook(
            array(
                'displayAdminCustomers',
                'actionAdminControllerSetMedia',
                'displayBackOfficeTop',
                'displayAdminOrderLeft',
                'displayHeader',
                'actionAuthentication'
            )
        );
    }

    public function install()
    {
        $objCustomerLogin = new PrestaCustomerLoggedIn();
        if (!parent::install()
            || !$objCustomerLogin->installTable()
            || !$this->updateHook()
            || !$this->callInstallTab()
            || !$this->updateConfiguration()
            ) {
            return false;
        }
        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'presta_logged_in_details`'
        );
    }

    public function uninstall()
    {
        $configuration = array(
            'PRESTA_CUSTOMER_AS_LOGIN', 'PRESTA_LOGIN_CUSTOMER_PERMISSION',
            'PRESTA_CUSTOMER_LOGIN_FRONT_NAVI', 'PRESTA_CUSTOMER_LOGIN_ORDER_DETAIL',
            'PRESTA_CUSTOMER_LOGIN_CUSTOMER_DETAIL', 'PRESTA_CUSTOMER_LOGIN_ADMIN_HEADER',
            'PRESTA_CUSTOMER_LOGIN_HISTORY', 'PRESTA_LOGIN_CUSTOMER_PERMISSION'
        );
        foreach ($configuration as $key) {
            Configuration::deleteByName($key);
        }
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->dropTable()
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
