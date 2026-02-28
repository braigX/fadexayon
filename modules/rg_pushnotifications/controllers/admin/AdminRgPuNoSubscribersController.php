<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class AdminRgPuNoSubscribersController extends ModuleAdminController
{
    private $module_name;

    public function __construct()
    {
        $this->module_name = 'rg_pushnotifications';
        $this->table = 'rg_pushnotifications_subscriber';
        $this->identifier = 'id_subscriber';
        $this->className = 'RgPuNoSubscriber';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->list_no_link = true;

        $this->_select = 'IF(a.`id_customer`>0, CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`), CONCAT("Guest ", a.`id_guest`)) AS `customer`';
        $this->_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $platforms = array_combine(RgPuNoSubscriber::getPlatforms(), RgPuNoSubscriber::getPlatforms());
        $devices = [];

        foreach (RgPuNoSubscriber::getDevices() as $device) {
            $devices[$device['device']] = $device['device'];
        }

        $this->fields_list = [
            'id_subscriber' => [
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'havingFilter' => true,
                'prefix' => '<b>',
                'suffix' => '</b>',
            ],
            'device' => [
                'title' => $this->l('Device'),
                'type' => 'select',
                'list' => $devices,
                'filter_key' => 'device',
                'filter_type' => 'string',
            ],
            'platform' => [
                'title' => $this->l('Platform'),
                'type' => 'select',
                'list' => $platforms,
                'filter_key' => 'platform',
                'filter_type' => 'string',
            ],
            'session_count' => [
                'title' => $this->l('Session Count'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'last_active' => [
                'title' => $this->l('Last Active'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
            'unsubscribed' => [
                'title' => $this->l('Unsubscribed'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printStatusIcon',
                'orderby' => false,
            ],
            'date_add' => [
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
        ];

        if (Module::isInstalled('rg_psmobileapp')) {
            $this->fields_list['from_app'] = [
                'title' => $this->l('From app'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printFromAppIcon',
                'orderby' => false,
            ];
        }
    }

    public function printStatusIcon($id, $tr)
    {
        $this->context->smarty->assign([
            'icon_icon' => ($tr['unsubscribed'] ? 'check' : 'remove'),
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/icon.tpl');
    }

    public function printFromAppIcon($id, $tr)
    {
        $this->context->smarty->assign([
            'icon_icon' => ($tr['from_app'] ? 'check' : 'remove'),
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

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'edit' && $this->display != 'add') {
            $this->page_header_toolbar_btn['config'] = [
                'desc' => $this->l('Module Config'),
                'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module_name,
                'icon' => 'process-icon-configure',
            ];

            $this->page_header_toolbar_btn['refresh_subscriber'] = [
                'js' => 'refreshSubscribers();',
                'desc' => $this->l('Refresh Subscriber Data'),
                'icon' => 'process-icon-refresh',
            ];
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS($this->module->getPathUri() . 'views/css/back.css');
        $this->addJS($this->module->getPathUri() . 'views/js/back.js');

        Media::addJsDef([
            'refresh_loading_text' => $this->l('Updating suscribers data. Please wait...'),
            'refresh_processed_text' => $this->l('Processed') . ':',
            'suscribers_token' => Tools::getAdminTokenLite('AdminRgPuNoSubscribers'),
        ]);
    }

    public function renderKpis()
    {
        $kpis = [];
        $data = RgPuNoSubscriber::getTotalsData();

        $helper = new HelperKpi();
        $helper->id = 'box-total-clients';
        $helper->icon = 'icon-users';
        $helper->color = 'color1';
        $helper->title = $this->l('Customers', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['total_customer'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-total-clients';
        $helper->icon = 'icon-trophy';
        $helper->color = 'color2';
        $helper->title = $this->l('Subscribed Customers', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false) . ' (%)';
        $helper->value = (int) $data['subscribed_customer'] . ' (' . round((float) $data['subscribed_customer'] / (float) $data['total_customer'] * 100) . '%)';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-total-clients';
        $helper->icon = 'icon-check';
        $helper->color = 'color3';
        $helper->title = $this->l('Subscribers', null, null, false);
        $helper->subtitle = $this->l('GUESTS INCLUDED', null, null, false);
        $helper->value = (int) $data['subscribed_total'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function ajaxProcessRefreshSubscribers()
    {
        $result = RgPuNoTools::refreshPlayerData((int) Tools::getValue('page'));

        die(json_encode($result));
    }
}
