<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class AdminRgPuNoNotificationsController extends ModuleAdminController
{
    private $module_name;

    public function __construct()
    {
        $this->module_name = 'rg_pushnotifications';
        $this->table = 'rg_pushnotifications_notification';
        $this->identifier = 'id_notification';
        $this->className = 'RgPuNoNotification';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->list_no_link = true;

        $this->_select = 'IF(s.`id_customer` > 0, CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`), CONCAT("Guest ", s.`id_guest`)) AS `customer`, IF(a.`id_campaign` > 0, "--", a.`status`) AS `status`';
        $this->_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber` s ON (s.`id_subscriber` = a.`id_subscriber`)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = s.`id_customer`)';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $notification_types = array_combine(['event', 'reminder', 'message'], ['event', 'reminder', 'message']);
        $notification_status = array_combine(
            ['delivered', 'queued', 'scheduled', 'canceled', 'norecipients'],
            ['delivered', 'queued', 'scheduled', 'canceled', 'norecipients']
        );

        $this->fields_list = [
            'id_campaign' => [
                'title' => $this->l('Campaign ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'havingFilter' => true,
                'prefix' => '<b>',
                'suffix' => '</b>',
            ],
            'id_cart' => [
                'title' => $this->l('Cart ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'title' => [
                'title' => $this->l('Title'),
            ],
            'notification_type' => [
                'title' => $this->l('Type'),
                'type' => 'select',
                'list' => $notification_types,
                'filter_key' => 'notification_type',
                'filter_type' => 'string',
            ],
            'status' => [
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => $notification_status,
                'filter_key' => 'status',
                'filter_type' => 'string',
            ],
            'clicked' => [
                'title' => $this->l('Clicked'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printStatusIcon',
                'orderby' => false,
            ],
            'date_start' => [
                'title' => $this->l('Start Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
            'date_end' => [
                'title' => $this->l('End Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
        ];
    }

    public function printStatusIcon($id, $tr)
    {
        if ($tr['id_campaign']) {
            return '--';
        }

        $this->context->smarty->assign([
            'icon_icon' => ($tr['clicked'] ? 'check' : 'remove'),
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/icon.tpl');
    }

    public function initContent()
    {
        if (!RgPuNoTools::validateBasicSettings()) {
            $this->errors[] = $this->displayWarning($this->l('Apparently there are no credentials configured in this shop. It is necessary to set them in the module configurator in order to continue.'));

            return;
        }

        return parent::initContent();
    }

    public function init()
    {
        if (Tools::getValue('refresh_campaign')) {
            $this->action = 'refreshCampaign';
        }
        parent::init();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'edit' && $this->display != 'add') {
            $this->page_header_toolbar_btn['campains'] = [
                'desc' => $this->l('Campaigns'),
                'href' => $this->context->link->getAdminLink('AdminRgPuNoCampaigns'),
                'icon' => 'process-icon-edit',
            ];

            $this->page_header_toolbar_btn['config'] = [
                'desc' => $this->l('Module Config'),
                'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module_name,
                'icon' => 'process-icon-configure',
            ];

            $this->page_header_toolbar_btn['refresh_campaign'] = [
                'href' => self::$currentIndex . '&refresh_campaign=1&token=' . $this->token,
                'desc' => $this->l('Refresh Notifications Data'),
                'icon' => 'process-icon-refresh',
            ];
        }
    }

    public function processRefreshCampaign()
    {
        RgPuNoTools::refreshCampaignData();
        $this->redirect_after = $this->context->link->getAdminLink('AdminRgPuNoNotifications') . '&conf=4';
    }

    public function renderKpis()
    {
        $kpis = [];
        $data = RgPuNoNotification::getTotalsData();

        $helper = new HelperKpi();
        $helper->id = 'box-total-clients';
        $helper->icon = 'icon-envelope';
        $helper->color = 'color1';
        $helper->title = $this->l('Notifications', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['total'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-subscribed-clients';
        $helper->icon = 'icon-bell';
        $helper->color = 'color2';
        $helper->title = $this->l('Campaign Notifications', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['campaign'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-total-notifications';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color3';
        $helper->title = $this->l('Cart Reminder', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['cart'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-viewed-notifications';
        $helper->icon = 'icon-check';
        $helper->color = 'color4';
        $helper->title = $this->l('Click Rate', null, null, false);
        $helper->subtitle = $this->l('PERCENT', null, null, false);
        $helper->value = ((int) $data['total'] - (int) $data['campaign'] ? round((float) $data['clicked'] / ((float) $data['total'] - (float) $data['campaign']) * 100) : '0') . '%';
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }
}
