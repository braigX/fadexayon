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

class Ets_reviewsCommentModuleFrontController extends ModuleFrontController
{
    public $question;
    protected $redirectionExtraExcludedKeys = ['id_product_comment', 'id_product', 'module', 'id_customer'];

    public function __construct()
    {
        parent::__construct();

        if (!$this->module->is17) {
            if (isset($this->display_column_right)) $this->display_column_right = false;
            if (isset($this->display_column_left)) $this->display_column_left = false;
        }
        $this->question = trim(Tools::getValue('current_tab')) == 'my_question' ? 1 : 0;
    }

    public function init()
    {
        parent::init();
        if (!defined('_PS_ADMIN_DIR_') && Tools::getIsset('id_product_comment')) {
            $this->context->controller->php_self = 'detail';
        }
    }

    public function getCanonicalURL()
    {
        return $this->context->link->getModuleLink($this->module->name, 'comment', [], $this->ssl);
    }

    public function postProcess()
    {
        $this->module->postProcess();
    }

    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged() || !$this->context->customer->id) {
            Tools::redirect($this->context->link->getPageLink('authentication') . '?back=' . $this->context->link->getModuleLink($this->module->name, 'comment', (trim(Tools::getValue('current_tab')) !== '' ? ['current_tab' => trim(Tools::getValue('current_tab'))] : []), $this->ssl));
        } elseif (!(int)Configuration::get('ETS_RV_REVIEW_ENABLED') && !(int)Configuration::get('ETS_RV_QUESTION_ENABLED')) {
            Tools::redirect($this->context->link->getPageLink('index'));
        }
        if ($id = (int)Tools::getValue('id_product_comment')) {
            $product_comment = new EtsRVProductComment($id, (int)$this->context->language->id);
            if (!Validate::isLoadedObject($product_comment))
                Tools::redirect($this->context->link->getModuleLink($this->module->name, 'comment'));
            if (!EtsRVProductCommentCustomer::isGrandStaff($this->context->customer->id) && (int)$product_comment->id_customer !== $this->context->customer->id) {
                Tools::redirect($this->context->link->getModuleLink($this->module->name, 'comment', ['current_tab' => 'my_review']));
            }
        }
        $tabs = $this->module->reviewTabs();
        $title = isset($product_comment) && $product_comment->title ? $product_comment->title : null;
        $current_tab = trim(Tools::getValue('current_tab', Tools::getValue('back', 'waiting_for_review')));
        if ($title == null && isset($tabs[$current_tab]['title'])) {
            $title = $tabs[$current_tab]['title'];
        }
        $this->context->smarty->assign(['current_tab' => $current_tab]);
        if ($this->module->is17) {
            $this->context->smarty->assign([
                'page' => array(
                    'title' => $title,
                    'canonical' => $this->getCanonicalURL(),
                    'meta' => array(
                        'title' => $title,
                        'description' => '',
                        'keywords' => '',
                        'robots' => 'index',
                    ),
                    'page_name' => 'comment',
                    'body_classes' => array('my-review'),
                    'admin_notifications' => array(),
                ),
            ]);
        } else {
            $breadcrumb = $this->getBreadcrumb();
            $this->context->smarty->assign([
                'meta_title' => $title,
                'meta_description' => '',
                'meta_keywords' => '',
                'robots' => 'index',
                'breadcrumb' => $breadcrumb,
                'path' => $breadcrumb,
            ]);
        }

        $this->setTemplate(($this->module->is17 ? 'module:' . $this->module->name . '/views/templates/front/' : '') . 'my-review' . ($this->module->is17 ? '' : '-16') . '.tpl');
    }

    public function getBreadcrumb()
    {
        $breadcrumb = $this->getBreadcrumbLinks();
        $breadcrumb['count'] = count($breadcrumb['links']);
        if ($this->module->is17)
            return $breadcrumb;
        else
            return $this->displayBreadcrumb($breadcrumb);
    }

    public function displayBreadcrumb($breadcrumb)
    {
        $this->context->smarty->assign('breadcrumb', $breadcrumb);
        return $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/front/breadcrumb.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = array();
        if ($this->module->is17) {
            $breadcrumb['links'][] = array(
                'title' => $this->module->l('Home', 'comment'),
                'url' => $this->context->link->getPageLink('index', true),
            );
        }
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('My account', 'comment'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );
        if (Tools::getValue('controller') == 'comment') {
            $tabs = $this->module->reviewTabs();
            $current_tab = trim(Tools::getValue('current_tab', Tools::getValue('back', 'waiting_for_review')));
            $title = $this->module->l('My reviews', 'comment');
            if (isset($tabs[$current_tab]) && isset($tabs[$current_tab]['title'])) {
                $title = $tabs[$current_tab]['title'];
            }
            $breadcrumb['links'][] = array(
                'title' => $title,
                'url' => $this->context->link->getModuleLink($this->module->name, 'comment', ['current_tab' => $current_tab]),
            );
        }
        return $breadcrumb;
    }
}
