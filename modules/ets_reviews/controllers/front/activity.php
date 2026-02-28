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

class Ets_reviewsActivityModuleFrontController extends ModuleFrontController
{
    protected $id_customer = 0;

    /**
     * @var Ets_reviews
     */
    public $module;

    public function __construct()
    {
        parent::__construct();

        if (!$this->module->is17) {
            if (isset($this->display_column_right)) $this->display_column_right = false;
            if (isset($this->display_column_left)) $this->display_column_left = false;
        }
    }

    public function init()
    {
        parent::init();
        if ((!isset($this->context->customer) || !$this->context->customer->isLogged())) {
            if (isset($this->context->customer) && $this->context->customer->isLogged()) {
                Tools::redirect($this->context->link->getModuleLink($this->module->name, 'activity'));
            } else {
                Tools::redirect($this->context->link->getPageLink('authentication') . '?back=' . $this->context->link->getModuleLink($this->module->name, 'activity'));
            }
        }
    }

    public function getCanonicalURL()
    {
        return $this->context->link->getModuleLink($this->module->name, 'activity', [], $this->ssl);
    }

    public function initContent()
    {
        parent::initContent();

        $list_per_pages = [
            20 => '',
            50 => '',
            100 => '',
            300 => '',
            1000 => ''
        ];
        $page = (int)Tools::getValue('page') ?: 1;
        $min_per_page = 20;
        $per_page = (int)Tools::getValue('per_page') ?: $min_per_page;

        $activityList = EtsRVProductComment::getActivityList($this->context->customer->id, 0, $page, $per_page, $this->context);
        if ($activityList) {
            foreach ($activityList as &$item) {
                $item['content'] = EtsRVActivityEntity::getInstance()->activityProperties(trim($item['content']), $item);
                $item['profile'] = [
                    'photo' => ($photo = EtsRVProductCommentCustomer::getAvatarByIdCustomer((int)$item['id_customer'])) ? $this->context->link->getMediaLink(_PS_IMG_ . $this->module->name . '/a/' . $photo) : false,
                ];
                if (!$photo && isset($item['profile'])) {
                    $item['profile']['caption'] = Tools::ucfirst(Tools::substr($item['customer_name'], 0, 1));
                    $item['profile']['color'] = trim($item['customer_name']);
                }
                if (isset($item['id_customer']) && (int)$item['id_customer'] && isset($this->context->customer->id) && $this->context->customer->isLogged() && $this->context->customer->id === (int)$item['id_customer']) {
                    $item['profile'] += [
                        'link' => $this->context->link->getPageLink('identity')
                    ];
                }
                $item['display_date_add'] = EtsRVActivityEntity::getInstance()->timeElapsedString($item['date_add']);
            }
        }
        $params = [];
        $total_records = EtsRVProductComment::getActivityList($this->context->customer->id, true, 1, 1, $this->context);
        $paginates = EtsRVLink::getPagination($this->module->name, 'activity', $total_records, $page, $per_page, $params, 7, $this->context);
        if ($list_per_pages) {
            foreach ($list_per_pages as $n => &$item) {
                $item = $this->context->link->getModuleLink($this->module->name, 'activity', $params + ['per_page' => $n, 'page' => 1]);
            }
        }
        $page_title = $this->module->l('Activities', 'activity');
        if ($this->module->is17) {
            $this->context->smarty->assign([
                'page' => [
                    'title' => $page_title,
                    'canonical' => $this->getCanonicalURL(),
                    'meta' => array(
                        'title' => $page_title,
                        'description' => '',
                        'keywords' => '',
                        'robots' => 'index',
                    ),
                    'page_name' => 'activity',
                    'body_classes' => array('activity-list'),
                    'admin_notifications' => array(),
                ]
            ]);
        } else {
            $breadcrumb = $this->getBreadcrumb();
            $this->context->smarty->assign([
                'meta_title' => $page_title,
                'meta_description' => '',
                'meta_keywords' => '',
                'robots' => 'index',
                'breadcrumb' => $breadcrumb,
                'path' => $breadcrumb
            ]);
        }
        $this->context->smarty->assign([
            'tabs' => $this->module->reviewTabs($this->context->customer->id),
            'activityList' => $activityList,
            'total_records' => $total_records,
            'paginates' => $paginates,
            'list_per_pages' => $list_per_pages,
            'current_per_page' => $per_page,
            'show_footer_btn' => $min_per_page > 0 && ceil($total_records / $min_per_page) > 1,
            'author' => $this->context->customer,
            'current_tab' => 'activity',
            'ETS_RV_DESIGN_COLOR2' => Configuration::get('ETS_RV_DESIGN_COLOR2'),
        ]);

        $this->setTemplate(($this->module->is17 ? 'module:' . $this->module->name . '/views/templates/front/' : '') . 'activity' . ($this->module->is17 ? '' : '-16') . '.tpl');
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
                'title' => $this->module->l('Home', 'activity'),
                'url' => $this->context->link->getPageLink('index', true),
            );
        }
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('My account', 'comment'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );
        if (trim(Tools::getValue('controller')) == 'activity') {
            $breadcrumb['links'][] = array(
                'title' => $this->module->l('Activities', 'activity'),
                'url' => $this->context->link->getModuleLink($this->module->name, 'activity'),
            );
        }
        return $breadcrumb;
    }
}
