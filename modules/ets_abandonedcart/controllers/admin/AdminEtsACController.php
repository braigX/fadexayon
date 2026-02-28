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


class AdminEtsACController extends ModuleAdminController
{
    public $is17 = false;
    public $mPath;

    public function __construct()
    {
        parent::__construct();

        $this->is17 = (int)@version_compare(_PS_VERSION_, '1.7.0.0', '>=');
        $this->mPath = $this->module->getPathUri();
    }

    public function init()
    {
        $this->tpl_form_vars = array(
            'slugTab' => Ets_abandonedcart::$slugTab,
            'controller_name' => $this->controller_name,
            'link' => $this->context->link,
        );
        $this->context->smarty->assign($this->tpl_form_vars);

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $this->renderAdmin();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->context->controller->addCSS(array($this->mPath . 'views/css/abancart-admin.css'), 'all');
    }

    protected function renderJs()
    {
        Media::addJsDef([
            'ETS_ABANCART_COPIED_MESSAGE' => $this->l('Copied'),
            'ETS_ABANCART_AJAX_LINK' => self::$currentIndex . '&token=' . $this->token,
        ]);
        if (!$this->isCached(($template = 'views/templates/admin/javascript.tpl'), EtsAbancartCache::getCachedId('javascript', null, Configuration::get('PS_USE_HTMLPURIFIER')))) {
            $this->context->smarty->assign(array(
                'img_dir' => $this->mPath . 'views/img/origin/',
                'is17' => $this->is17,
                'js_files' => array($this->mPath . 'views/js/shortcode.js', $this->mPath . 'views/js/jquery.countdown.min.js', $this->mPath . 'views/js/abancart.admin.js'),
                'html_purifier' => Configuration::get('PS_USE_HTMLPURIFIER') ? 1 : 0,
                'campaign_url' => $this->context->link->getAdminLink(Ets_abandonedcart::$slugTab . 'Campaign', true)
            ));
        }
        return $this->module->display($this->module->getLocalPath(), $template, EtsAbancartCache::getCachedId('javascript', null, Configuration::get('PS_USE_HTMLPURIFIER')));
    }

    private function renderMenu()
    {
        $is_module_disabled = !Module::isEnabled('ets_abandonedcart');
        if (!$this->isCached(($template = 'views/templates/admin/menus.tpl'), EtsAbancartCache::getCachedId('menus', null, $this->controller_name . '|' . (int)$is_module_disabled))) {
            $this->context->smarty->assign(array(
                'path' => $this->mPath,
                'isModuleDisabled' => $is_module_disabled,
                'menus' => EtsAbancartDefines::getInstance()->getFields('menus'),
            ));
        }
        return $this->module->display($this->module->getLocalPath(), $template, EtsAbancartCache::getCachedId('menus', null, $this->controller_name . '|' . (int)$is_module_disabled));
    }

    protected function renderAdmin()
    {
        $assigns = array(
            'header' => $this->renderJs(),
            'content' => $this->content,
            'menus' => $this->renderMenu(),
            'path' => $this->mPath,
            'display' => $this->display
        );
        if (!$this->is17) {
            $assigns += [
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'title' => $this->page_header_toolbar_title,
                'toolbar_btn' => $this->page_header_toolbar_btn,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ];
        }
        $this->context->smarty->assign($assigns);
        $this->content = $this->createTemplate('admin.tpl')->fetch();
        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }

    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        $context = Context::getContext();

        $context->cart = new Cart($id_cart);
        if (!(int)$context->cart->id_currency)
            return null;
        $context->currency = new Currency((int)$context->cart->id_currency);
        $context->customer = new Customer((int)$context->cart->id_customer);

        return Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public function toJson($data)
    {
        if ($this->ajax) {
            die(json_encode($data));
        }
    }

    public function createTemplate($tpl_name)
    {
        if (@version_compare(_PS_VERSION_, '1.7.6.1', '<')) {
            foreach ($this->getTemplateLookupPaths() as $path) {
                if (@file_exists($path . $tpl_name)) {
                    return $this->context->smarty->createTemplate($path . $tpl_name, $this->context->smarty);
                }
            }
        }
        return parent::createTemplate($tpl_name);
    }

    protected function getTemplateLookupPaths()
    {
        $templatePath = $this->getTemplatePath();

        return [
            _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/admin/',
            $templatePath . $this->override_folder,
            $templatePath,
        ];
    }

    public function access($action, $disable = false)
    {
        return $this->is17 ? parent::access($action, $disable) : $this->viewAccess($disable);
    }

    public function displayRecoveredCarts($total_paid_tax_incl, $tr)
    {
        if (isset($tr['nb_order']) && $tr['nb_order'] > 0) {
            $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            $total_paid = EtsAbancartReminder::getOrderTotalByOrderIds($total_paid_tax_incl);
            $total_cart_applied = EtsAbancartReminder::getTotalCartApplied((int)$tr['id_ets_abancart_campaign']);
            return $tr['nb_order'] . ($total_cart_applied > 0 ? '/' . $total_cart_applied : '') . '(' . Tools::displayPrice($total_paid, $idCurrency) . ')';
        }
    }

    public function displayIsRecoveredCart($recovered_cart)
    {
        return EtsAbancartTools::displayText(($recovered_cart ? $this->l('Yes', 'AdminEtsACController') : $this->l('No', 'AdminEtsACController')), 'span', ['class' => 'badge badge-' . ($recovered_cart > 0 ? 'green' : 'gray')]);
    }

    public function clearCache($template, $cache_id = null)
    {
        if ($cache_id == null) {
            $cache_id = EtsAbancartCache::getCachedId();
        }
        return $this->module->clearCache($template, $cache_id);
    }
}
