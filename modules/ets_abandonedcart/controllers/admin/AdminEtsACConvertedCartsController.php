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


require_once(dirname(__FILE__) . '/AdminEtsACController.php');

class AdminEtsACConvertedCartsController extends AdminEtsACController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_form_cancel_button = false;
        $this->_redirect = false;
        $this->show_toolbar = false;
        $this->list_no_link = true;

        $this->table = 'cart';
        $this->className = 'Cart';
        $this->list_id = $this->table;

        $this->addRowAction('view');
        $this->addRowAction('reminderlog');
        if (Module::isEnabled('ets_trackingcustomer')) {
            $this->addRowAction('session');
        }
        $this->allow_export = false;
        $this->_orderBy = 'o.id_order';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->tpl_folder = 'common/';

        $this->_select = 'o.id_order,
            o.reference,
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`,
            o.`total_discounts_tax_incl`/o.`conversion_rate` `total_discounts_tax_incl`,
            o.`total_paid_tax_incl`/o.`conversion_rate` `total_paid_tax_incl`,
            a.date_add,
            GROUP_CONCAT(
                IF(t.id_ets_abancart_reminder = 0, \'' . pSQL($this->l('Leaving reminder', 'AdminEtsACConvertedCartsController')) . '\', CONCAT(rl.title, \' - \', cl.name, IF(r.day > 0, CONCAT(\' (\', r.day,\' \', IF(r.day > 1, \'' . pSQL($this->l('days', 'AdminEtsACConvertedCartsController')) . '\', \'' . pSQL($this->l('day', 'AdminEtsACConvertedCartsController')) . '\'), IF(r.hour <= 0, \')\', \'\')), \'\'), IF(r.hour > 0, CONCAT(IF(r.day > 0, \' + \', \' (\'), r.hour, \'' . pSQL($this->l('hr', 'AdminEtsACConvertedCartsController')) . '\', \')\'), \'\'))) ORDER BY t.date_add ASC SEPARATOR \'<br>\'
            ) as `reminderIds`,
            o.date_add `date_purchased`,
            IF(o.id_order, 1, 0) badge_success,
            IF(a.id_cart > 0, a.id_lang, c.id_lang) `lang_id`,
            t.id_cart,
            (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_abancart_tracking` t WHERE t.id_cart=a.id_cart) `reminders`,
            t.display_times
        ';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'currency` cu ON (cu.id_currency = a.id_currency)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = a.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_tracking` t ON (a.id_cart = t.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` r ON (r.id_ets_abancart_reminder = t.id_ets_abancart_reminder)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder_lang` rl ON (rl.id_ets_abancart_reminder = r.id_ets_abancart_reminder AND rl.id_lang=t.id_lang)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign` ac ON (ac.id_ets_abancart_campaign = r.id_ets_abancart_campaign)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_campaign_lang` cl ON (cl.id_ets_abancart_campaign = ac.id_ets_abancart_campaign AND cl.id_lang=t.id_lang)
        ';
        $this->_where = '
            AND o.id_order is NOT NULL 
            AND t.id_ets_abancart_tracking is NOT NULL 
            AND (t.id_ets_abancart_reminder > 0 AND ac.campaign_type=\'' . pSQL(EtsAbancartCampaign::CAMPAIGN_TYPE_EMAIL) . '\' OR t.id_ets_abancart_reminder = -1)
            AND os.paid = 1
            AND t.delivered=1 
            AND a.id_shop = ' . (int)$this->context->shop->id;

        $this->_group = 'GROUP BY o.id_order';

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('Order ID', 'AdminEtsACConvertedCartsController'),
                'type' => 'int',
                'class' => 'fixed-width-xs center',
                'filter_key' => 'o!id_order',
            ),
            'reference' => array(
                'title' => $this->l('Order reference', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-center',
                'filter_key' => 'o!reference',
                'callback' => 'displayReference'
            ),
            'customer' => array(
                'title' => $this->l('Customer', 'AdminEtsACConvertedCartsController'),
                'havingFilter' => true,
                'callback' => 'displayCustomer'
            ),
            'total_discounts_tax_incl' => array(
                'title' => $this->l('Discount value', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-right',
                'type' => 'text',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'class' => 'fixed-width-lg',
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-right',
                'type' => 'text',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
                'class' => 'fixed-width-lg',
            ),
            'date_add' => array(
                'title' => $this->l('Date added', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ),
            'reminders' => [
                'title' => $this->l('Reminders', 'AdminEtsACCartController'),
                'class' => 'fixed-width-xs center',
                'filterHaving' => 'reminders',
                'havingFilter' => true,
                'callback' => 'displayReminders'
            ],
            'reminderIds' => array(
                'title' => $this->l('Reminder', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-left reminder_col',
                'callback' => 'printReminderIds',
                'orderby' => false,
                'search' => false,
            ),
            'display_times' => array(
                'title' => $this->l('Last reminder time', 'AdminEtsACConvertedCartsController'),
                'align' => 'text-center',
                'type' => 'datetime',
                'filter_key' => 't!display_times',
            ),
            'date_purchased' => array(
                'title' => $this->l('Date purchased', 'AdminEtsACConvertedCartsController'),
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'o!date_upd',
            ),
        );
    }

    public function displayReminders($reminders, $tr)
    {
        if (isset($tr['id_cart']) && $tr['id_cart'] > 0) {
            $id_cart = $tr['id_cart'];
            if (!$reminders)
                return EtsAbancartTools::displayText($reminders, 'span');
            $attrs = [
                'href' => $this->context->link->getAdminLink(Ets_abandonedcart::$slugTab . 'Cart') . '&id_cart=' . (int)$id_cart . '&reminderlog',
                'class' => 'ets_abancart_reminder_log'
            ];
            return EtsAbancartTools::displayText(EtsAbancartTools::displayText($reminders, 'span', ['class' => 'badge badge-info']), 'a', $attrs);
        }

        return $reminders;
    }

    public function displayReference($reference, $tr)
    {
        if ($reference) {
            $attrs = [
                'href' => $this->context->link->getAdminLink('AdminOrders', true, version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? ['route' => 'admin_orders_view', 'orderId' => (int)$tr['id_order']] : [], ['vieworder' => '', 'id_order' => (int)$tr['id_order']]),
                'target' => '_bank',
                'title' => $reference,
                'class' => 'ets_ab_order_link',
            ];
            return EtsAbancartTools::displayText($reference, 'a', $attrs);
        }
    }

    public function displayCustomer($customer_name, $tr)
    {
        if (!isset($tr['id_customer']) || !(int)$tr['id_customer'])
            return $customer_name;
        $customerLink = '';
        try {
            $customerLink = $this->context->link->getAdminLink('AdminCustomers', true, $this->module->ver_min_1760 ? ['route' => 'admin_customers_view', 'customerId' => (int)$tr['id_customer']] : [], ['viewcustomer' => '', 'id_customer' => (int)$tr['id_customer']]);
        } catch (Exception $ex) {
            if ($ex)
                $customerLink = $this->context->link->getAdminLink('AdminCustomers', true) . '&viewcustomer&id_customer=' . (int)$tr['id_customer'];
        }
        $attrs = [
            'href' => $customerLink,
            'target' => '_bank',
            'title' => $customer_name,
            'class' => 'ets_ab_customer_link',
        ];
        return EtsAbancartTools::displayText($customer_name, 'a', $attrs);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initToolbarTitle()
    {
        if (!$this->display || $this->display == 'view') {
            $this->toolbar_title = array($this->l('Recovered carts', 'AdminEtsACConvertedCartsController', null, null, false));
            if (is_array($this->meta_title)) {
                $this->meta_title = array($this->l('Recovered carts', 'AdminEtsACConvertedCartsController', null, null, false));
            }
            if ($filter = $this->addFiltersToBreadcrumbs()) {
                $this->toolbar_title[] = $filter;
            }
        } else {
            parent::initToolbarTitle();
        }
    }

    public static function setOrderCurrency($echo, $tr)
    {
        return Tools::displayPrice($echo, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
    }

    public function printReminderIds($reminderIds, $row)
    {
        if ($row) {
            $idLang = isset($row['lang_id']) && (int)$row['lang_id'] > 0 ? $row['lang_id'] : Configuration::get('PS_LANGUAGE_DEFAULT');
            $language = new Language($idLang);
            $attrs = [
                'src' => $this->module->getPathUri() . 'views/img/flag/' . $language->iso_code . '.gif',
            ];
            return EtsAbancartTools::displayText('', 'img', $attrs) . ' ' . $language->name . ' ' . EtsAbancartTools::displayText('', 'br') . $reminderIds;
        }

        return $reminderIds;
    }

    public function displayReminderLogLink($token, $id)
    {
        if (!isset(self::$cache_lang['reminder_log'])) {
            self::$cache_lang['reminder_log'] = $this->l('view reminder log', 'AdminEtsACConvertedCartsController');
        }
        $attrs = [
            'href' => $this->context->link->getAdminLink(Ets_abandonedcart::$slugTab . 'Cart') . '&' . $this->identifier . '=' . $id . '&reminderlog&recover_cart=1',
            'action' => self::$cache_lang['reminder_log'],
            'class' => 'ets_abancart_reminder_log',
            'token' => $token,
        ];
        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-file']) . ' ' . self::$cache_lang['reminder_log'], 'a', $attrs);
    }

    public function displayViewLink($token, $id)
    {
        if (!isset(self::$cache_lang['view'])) {
            self::$cache_lang['view'] = $this->l('View', 'AdminEtsACConvertedCartsController');
        }
        $attrs = array(
            'href' => $this->context->link->getAdminLink('AdminCarts') . '&id_cart=' . $id . '&viewcart',
            'title' => self::$cache_lang['view'],
            'token' => $token,
            'class' => 'btn btn-default'
        );
        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-eye']) . ' ' . self::$cache_lang['view'], 'a', $attrs);
    }

    public function displaySessionLink($token, $id)
    {
        if (!isset(self::$cache_lang['view_session'])) {
            self::$cache_lang['view_session'] = $this->l('View', 'AdminEtsACConvertedCartsController');
        }
        $attrs = array(
            'href' => $this->context->link->getAdminLink('AdminTrackingCustomerSession') . '&' . $this->identifier . '=' . $id . '&current_tab=customer_session',
            'title' => $this->l('View session'),
            'class' => 'ets_view_session',
            'token' => $token,
        );
        return EtsAbancartTools::displayText(EtsAbancartTools::displayText('', 'i', ['class' => 'icon-search-plus']) . ' ' . self::$cache_lang['view_session'], 'a', $attrs);
    }
}
