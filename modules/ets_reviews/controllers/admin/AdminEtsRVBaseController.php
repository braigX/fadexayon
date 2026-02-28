<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class AdminEtsRVBaseController extends ModuleAdminController
{
    /**
     * @var Ets_reviews
     */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->_default_pagination = 100;

        parent::__construct();
    }

    public function access($action, $disable = false)
    {
        if (!isset($this->context->employee) || (int)$this->context->employee->id_profile !== (int)_PS_ADMIN_PROFILE_ && !EtsRVStaff::isGrand($this->context->employee->id))
            return false;
        return parent::access($action, $disable);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $physical_uri = rtrim($this->context->shop->physical_uri, '/');
        $this->addJS(array(
            $physical_uri . '/modules/' . $this->module->name . '/views/js/modal.js',
            $physical_uri . '/modules/' . $this->module->name . '/views/js/jquery.rating.plugin.js',
            $physical_uri . '/js/jquery/plugins/fancybox/jquery.fancybox.js',
            $physical_uri . '/modules/' . $this->module->name . '/views/js/function.js',
            $physical_uri . '/modules/' . $this->module->name . '/views/js/moderate.js',
            $physical_uri . '/modules/' . $this->module->name . '/views/js/admin.js'
        ));
        if (!(int)Configuration::get('ETS_RV_SLICK_LIBRARY_DISABLED')) {
            $this->addJS($physical_uri . '/modules/' . $this->module->name . '/views/js/slick.js');
            $this->addCSS($physical_uri . '/modules/' . $this->module->name . '/views/css/slick.css');
        }
        $this->addCSS(array(
            $physical_uri . '/modules/' . $this->module->name . '/views/css/productcomments.all.css',
            $physical_uri . '/modules/' . $this->module->name . '/views/css/productcomments.admin.css',
        ));
        $colorPath = '/themes/' . _THEME_NAME_ . ($this->module->is17 ? '/assets' : '') . '/cache/productcomments.color.css';
        if (file_exists(_PS_ROOT_DIR_ . $colorPath))
            $this->addCSS(array(
                $physical_uri . $colorPath,
            ));
    }

    public function initContent()
    {
        parent::initContent();

        $this->content = $this->generalMenus($this->content);
        $this->context->smarty->assign(array(
            'content' => $this->content . $this->module->hookDisplayCronjobInfo(),
        ));
    }

    public function generalMenus($content)
    {
        $currentTab = Tools::getValue('controller');
        $this->context->smarty->assign(array(
            'html' => $content,
            'currentTab' => $currentTab !== '' && Validate::isCleanHtml($currentTab) ? $currentTab : Ets_reviews::TAB_PREFIX . 'Reviews',
            'menus' => $this->renderMenu(),
            'moduleIsEnabled' => Module::isEnabled($this->module->name),
        ));
        $this->module->addJss();
        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/admin.tpl');
    }

    public function renderMenu()
    {
        $currentTab = Tools::getValue('controller');
        $tabActive = trim(Tools::getValue('tab', 'employee'));
        $cache_id = EtsRVSmartyCache::getCachedId('menus', null, [$currentTab, $tabActive, (int)$this->module->active]);
        $template = 'views/templates/admin/menus.tpl';
        if ($cache_id == null || !$this->module->isCached($template, $cache_id)) {
            $this->context->smarty->assign(array(
                'menus' => EtsRVDefines::getInstance()->getQuickTabs(),
                'tabPrefix' => Ets_reviews::TAB_PREFIX,
                'link' => $this->context->link,
                'currentTab' => $currentTab !== '' && Validate::isCleanHtml($currentTab) ? $currentTab : Ets_reviews::TAB_PREFIX . 'Reviews',
                'tabActive' => $tabActive,
                'isSupperAdmin' => $this->context->employee->isLoggedBack() && $this->context->employee->isSuperAdmin(),
                'moduleIsEnabled' => Module::isEnabled($this->module->name),
            ));
        }
        return $this->module->display($this->module->getLocalPath(), $template, $cache_id);
    }

    public function displayGrade($grade)
    {
        if ($grade > 0) {
            $attrs = [
                'class' => 'ets_rv_grade' . ($grade < 3 ? ' danger' : ($grade < 4 ? ' warning' : ' success')),
                'data-grade' => $grade,
            ];
            return EtsRVTools::displayText($grade . '/5', 'span', $attrs);
        }

        return null;
    }

    public function extraJSON($data = array())
    {
        $refreshController = trim(Tools::getValue('refreshController'));
        if ($refreshController !== '') {
            $currentIndex = $this->context->link->getAdminLink($refreshController);
            $refreshController .= 'Controller';
            if (!class_exists($refreshController)) {
                require dirname(__FILE__) . '/' . $refreshController . '.php';
            }
            $controller = new $refreshController();
            if ($controller instanceof AdminController) {
                $controller::$currentIndex = $currentIndex;
                if ($this->context->cookie->__get('submitFilter' . $controller->list_id)) {
                    $controller->processFilter();
                }
                $data['list'] = $controller->renderList();
            }
        }

        return $data;
    }

    public function jsonRender($value = null)
    {
        die(json_encode($value));
    }

    public $_filterHaving;

    public function getHavingClause()
    {
        if (trim($this->_filterHaving) !== '') {
            if ($this->fields_list) {
                foreach ($this->fields_list as $key => $field) {
                    if (isset($field['havingFilter']) && $field['havingFilter'] && isset($field['ref']) && trim($field['ref']) !== '') {
                        $this->_filterHaving = preg_replace('/\s+AND\s+`(' . trim($key) . ')`\s+LIKE\s+\'%(.+?)%\'/', ' AND (`$1` LIKE \'%$2%\' OR `' . trim($field['ref']) . '`=\'$2\') ', $this->_filterHaving);
                    }
                }
            }
        }

        return parent::getHavingClause();
    }

    static $st_products = [];

    public function buildFieldProductLink($product_name, $tr)
    {
        if (!isset($tr['id_product']) || !(int)$tr['id_product'])
            return null;
        if (isset(self::$st_products[(int)$tr['id_product']]) && self::$st_products[(int)$tr['id_product']])
            return self::$st_products[(int)$tr['id_product']];
        $p = new Product((int)$tr['id_product'], false, $this->context->language->id);
        $tpl_vars = [
            'product_name' => $product_name,
            'product_link' => $this->context->link->getProductLink($p, $p->link_rewrite, $p->category, $p->ean13, $this->context->language->id, $this->context->shop->id, $p->getDefaultIdProductAttribute()),
        ];
        $id_image = isset($tr['id_image']) && $tr['id_image'] > 0 ? (int)$tr['id_image'] : 0;
        if ($id_image <= 0) {
            $cover = Product::getCover($p->id, $this->context);
            if (isset($cover['id_image']) && $cover['id_image'])
                $id_image = (int)$cover['id_image'];
        }
        if ($id_image > 0) {
            $tpl_vars['image'] = [
                'small' => $this->context->link->getImageLink($p->link_rewrite, $id_image, EtsRVTools::getFormattedName('cart')),
                'large' => $this->context->link->getImageLink($p->link_rewrite, $id_image, EtsRVTools::getFormattedName('large')),
            ];
        }
        $this->context->smarty->assign($tpl_vars);
        self::$st_products[(int)$tr['id_product']] = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/product-image-link.tpl');
        return self::$st_products[(int)$tr['id_product']];
    }

    static $st_customers = [];

    public function buildFieldCustomerLink($customer_name, $tr)
    {
        if (!isset($tr['id_customer']) || !(int)$tr['id_customer'] || !Customer::customerIdExistsStatic((int)$tr['id_customer']))
            return $customer_name ?: null;
        if (isset(self::$st_customers[(int)$tr['id_customer']]) && self::$st_customers[(int)$tr['id_customer']])
            return self::$st_customers[(int)$tr['id_customer']];
        $attrs = [
            'href' => EtsRVLink::getAdminLink('AdminCustomers', true, $this->module->ps1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']] : [], ['viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']], $this->context),
            'target' => '_bank',
            'title' => $customer_name,
            'class' => 'ets_rv_customer_link',
        ];
        self::$st_customers[(int)$tr['id_customer']] = EtsRVTools::displayText($customer_name, 'a', $attrs);
        return self::$st_customers[(int)$tr['id_customer']];
    }
}