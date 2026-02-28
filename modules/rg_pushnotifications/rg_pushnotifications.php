<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require dirname(__FILE__) . '/vendor/autoload.php';

class Rg_PushNotifications extends Module
{
    private $menu;
    private $available_notifications;

    public function __construct()
    {
        $this->name = 'rg_pushnotifications';
        $this->tab = 'advertising_marketing';
        $this->version = '1.11.0';
        $this->author = 'Rolige';
        $this->author_link = 'https://www.rolige.com/';
        $this->addons_author_link = 'https://addons.prestashop.com/en/2_community-developer?contributor=99052';
        $this->module_id = 11;
        $this->addons_module_id = 23852;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = 'b7e3eda88bbf381dfe2c089f28bf9f33';
        $this->author_address = '0xbF9c7047a7F061754830f2F4D00BEaC7240E325C';

        parent::__construct();

        $this->displayName = $this->l('Web Browser Push Notifications using OneSignal');
        $this->description = $this->l('Send push notifications to web browsers in any device, even if your shop\'s website is closed, using advanced PrestaShop based segmentation filters.');

        $this->menu = [
            [
                'dashboard' => [
                    'icon' => 'icon-home',
                    'title' => $this->l('Dashboard'),
                ],
            ],
            [
                'settings' => [
                    'icon' => 'icon-cogs',
                    'title' => $this->l('Settings'),
                ],
                'notifications' => [
                    'icon' => 'icon-envelope',
                    'title' => $this->l('Notifications'),
                ],
                'bell' => [
                    'icon' => 'icon-bell',
                    'title' => $this->l('Notification Bell'),
                ],
                'cart' => [
                    'icon' => 'icon-bullhorn',
                    'title' => $this->l('Cart Reminder'),
                ],
            ],
            [
                'maintenance' => [
                    'icon' => 'icon-eraser',
                    'title' => $this->l('Maintenance'),
                ],
                'cron' => [
                    'icon' => 'icon-clock-o',
                    'title' => $this->l('Cron Jobs'),
                ],
                'log' => [
                    'icon' => 'icon-history',
                    'title' => $this->l('Logs'),
                ],
                'help' => [
                    'icon' => 'icon-question-circle',
                    'title' => $this->l('Help'),
                ],
            ],
        ];

        $this->available_notifications = [
            1003 => $this->l('Tracking number registered'),
            1007 => $this->l('Answer to customer message'),
            1008 => $this->l('Voucher generated'),
        ];

        if (Module::isInstalled('mailalerts') && Module::isEnabled('mailalerts')) {
            $this->available_notifications[1009] = $this->l('Product availability');
        }

        /* Events translations
        $this->l('Order payment accepted');
        $this->l('The payment of your order');
        $this->l('has been Accepted');
        $this->l('For more details go to Orders History in your Account or click here.');
        $this->l('Order preparation in progress');
        $this->l('Your order');
        $this->l('is in Preparation Process');
        $this->l('Tracking number registered');
        $this->l('Tracking Number registered for your order');
        $this->l('Order shipped');
        $this->l('has been Shipped');
        $this->l('Order delivered');
        $this->l('has been Delivered');
        $this->l('Order canceled');
        $this->l('has been Canceled');
        $this->l('New message');
        $this->l('An answer to your message is available');
        $this->l('To reply, click here.');
        $this->l('Voucher generated');
        $this->l('A new voucher has been created for you');
        $this->l('Voucher modified');
        $this->l('Your voucher has been modified');
        $this->l('Try applying this code in your next purchase');
        $this->l('For more details, click here.');
        $this->l('Product availability');
        $this->l('The product');
        $this->l('is already in stock');
        $this->l('You can get it by clicking here.');
        */
    }

    public function install()
    {
        include $this->local_path . 'sql/install.php';

        $languages = Language::getLanguages(false);

        $main_tab = new Tab();
        $main_tab->class_name = 'AdminRgPuNo';

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $main_tab->id_parent = (int) Db::getInstance()->getValue('
                SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "DEFAULT"
            ');
            $main_tab->icon = 'notifications';
        } else {
            $main_tab->id_parent = 0;
        }

        $main_tab->module = $this->name;

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $main_tab->name[$lang['id_lang']] = 'Notificaciones Push';

                    break;
                case 'fr':
                    $main_tab->name[$lang['id_lang']] = 'Notifications Push';

                    break;
                default:
                    $main_tab->name[$lang['id_lang']] = 'Push Notifications';

                    break;
            }
        }

        $main_tab->add();

        $tab = new Tab();
        $tab->class_name = 'AdminRgPuNoSubscribers';
        $tab->id_parent = (int) $main_tab->id;
        $tab->module = $this->name;

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $tab->name[$lang['id_lang']] = 'Subscriptores';

                    break;
                case 'fr':
                    $tab->name[$lang['id_lang']] = 'Abonnés';

                    break;
                default:
                    $tab->name[$lang['id_lang']] = 'Subscribers';

                    break;
            }
        }

        $tab->add();

        $tab = new Tab();
        $tab->class_name = 'AdminRgPuNoNotifications';
        $tab->id_parent = (int) $main_tab->id;
        $tab->module = $this->name;

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $tab->name[$lang['id_lang']] = 'Notificaciones';

                    break;
                case 'fr':
                    $tab->name[$lang['id_lang']] = 'Notifications';

                    break;
                default:
                    $tab->name[$lang['id_lang']] = 'Notifications';

                    break;
            }
        }

        $tab->add();

        $tab = new Tab();
        $tab->class_name = 'AdminRgPuNoCampaigns';
        $tab->id_parent = (int) $main_tab->id;
        $tab->module = $this->name;

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $tab->name[$lang['id_lang']] = 'Campañas';

                    break;
                case 'fr':
                    $tab->name[$lang['id_lang']] = 'Campagnes';

                    break;
                default:
                    $tab->name[$lang['id_lang']] = 'Campaigns';

                    break;
            }
        }

        $tab->add();

        Tools::copy($this->local_path . 'views/img/cart.png', $this->local_path . 'uploads/cart.png');
        $bo_header = version_compare(_PS_VERSION_, '1.7.0.0', '<') ? 'displayBackOfficeHeader' : 'actionAdminControllerSetMedia';

        return RgPuNoConfig::install() &&
            parent::install() &&
            $this->registerHook($bo_header) &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('actionOrderStatusPostUpdate') &&
            $this->registerHook('actionAdminOrdersTrackingNumberUpdate') &&
            $this->registerHook('actionObjectCustomerThreadUpdateAfter') &&
            $this->registerHook('actionObjectCartRuleUpdateAfter') &&
            $this->registerHook('actionObjectCartRuleAddAfter') &&
            $this->registerHook('actionUpdateQuantity') &&
            $this->registerHook('actionProductAttributeUpdate') &&
            $this->registerHook('actionObjectCustomerAddAfter') &&
            $this->registerHook('actionObjectCustomerUpdateAfter');
    }

    public function uninstall()
    {
        include $this->local_path . 'sql/uninstall.php';

        $id_tab = (int) Tab::getIdFromClassName('AdminRgPuNo');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        $id_tab = (int) Tab::getIdFromClassName('AdminRgPuNoCampaigns');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        $id_tab = (int) Tab::getIdFromClassName('AdminRgPuNoNotifications');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        $id_tab = (int) Tab::getIdFromClassName('AdminRgPuNoSubscribers');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return RgPuNoConfig::uninstall() && parent::uninstall();
    }

    /**
     * Configuration form
     */
    public function getContent()
    {
        $output = RgPuNoConflicts::get($this);
        $confirmation = '';

        $menu_selected = Tools::strtolower(trim(Tools::getValue('menu_active')));
        $moduleForm = RgPuNoModuleForm::getForm($menu_selected, $this->menu);

        if ($moduleForm->isSubmitForm()) {
            if (!$error = $moduleForm->validateForm()) {
                $confirmation = $moduleForm->processForm();
            }
        }

        if (isset($error)) {
            if (!$error) {
                $output .= $this->displayConfirmation($confirmation);
            } else {
                $output .= $this->displayError($error);
            }
        }

        if (!RgPuNoTools::validateBasicSettings()) {
            $this->menu = [
                [
                    'dashboard' => [
                        'icon' => 'icon-home',
                        'title' => $this->l('Dashboard'),
                    ],
                ],
                [
                    'settings' => [
                        'icon' => 'icon-cogs',
                        'title' => $this->l('Settings'),
                    ],
                ],
                [
                    'help' => [
                        'icon' => 'icon-question-circle',
                        'title' => $this->l('Help'),
                    ],
                ],
            ];
        }

        $this->boSmartyAssign([
            'menu' => [
                'items' => $this->menu,
                'link' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
                'active' => $moduleForm->menu_active,
            ],
            'form' => $moduleForm->renderForm(),
        ]);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output;
    }

    public function boSmartyAssign($vars = null)
    {
        static $smarty_vars = null;

        if ($smarty_vars === null) {
            $smarty_vars = [
                '_path' => $this->_path,
                'version' => $this->version,
                'new_version' => RgPuNoTools::getNewModuleVersion($this->name, $this->version),
            ];
        }

        $currency = Currency::getDefaultCurrency();

        Media::addJsDef([$this->name => [
            '_path' => $this->_path,
            'token' => $this->secure_key,
            'config_prefix' => RgPuNoConfig::prefix('config'),
            'currency_sign' => $currency->sign . ' (' . $currency->iso_code . ')',
        ]]);

        if (is_array($vars)) {
            $smarty_vars = array_merge($smarty_vars, $vars);

            return $this->context->smarty->assign($this->name, $smarty_vars);
        }

        return $this->context->smarty->assign($this->name, $smarty_vars);
    }

    /**
     * CSS & JavaScript files loaded in the BO
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (method_exists($this->context->controller, 'addJquery')) {
            $this->context->controller->addJquery();
        }

        return $this->hookActionAdminControllerSetMedia();
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ($this->context->controller->controller_name == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            $this->boSmartyAssign();

            $this->context->controller->addjQueryPlugin('ajaxfileupload');
            $this->context->controller->addCSS('https://onesignal.com/sdks/OneSignalSDKStyles.css');
            $this->context->controller->addCSS($this->_path . 'views/libs/slick/slick.css');
            $this->context->controller->addCSS($this->_path . 'views/libs/slick/slick-theme.css');
            $this->context->controller->addJS($this->_path . 'views/libs/slick/slick.min.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addJS($this->_path . 'views/js/module_config.js');

            $list_menu_options = [
                'menu-subscribers-list' => [
                    'desc' => $this->l('Subscribers List'),
                    'href' => $this->context->link->getAdminLink('AdminRgPuNoSubscribers'),
                    'icon' => 'icon-users',
                ],
                'menu-campaign-list' => [
                    'desc' => $this->l('Campaigns List'),
                    'href' => $this->context->link->getAdminLink('AdminRgPuNoCampaigns'),
                    'icon' => 'icon-envelope',
                ],
                'menu-notifications-list' => [
                    'desc' => $this->l('Notifications List'),
                    'href' => $this->context->link->getAdminLink('AdminRgPuNoNotifications'),
                    'icon' => 'icon-bell',
                ],
            ];

            $this->context->smarty->assign('list_menu_options', $list_menu_options);
            $menu_html = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/list-menu-options.tpl');
            $this->context->controller->addJS($this->_path . 'views/js/list_menu_options.js');
            $addJsDef = ['list_menu_options_html' => trim($menu_html)];

            Media::addJsDef($addJsDef);
        }
    }

    public function hookDisplayHeader()
    {
        if (RgPuNoTools::validateBasicSettings()) {
            $RGPUNO_WELCOME_SHOW = (bool) RgPuNoConfig::get('RGPUNO_WELCOME_SHOW');
            $RGPUNO_BELL_SHOW = (bool) RgPuNoConfig::get('BELL_SHOW');
            $RGPUNO_BELL_THEME = RgPuNoConfig::get('BELL_THEME');
            $id_lang = (int) Context::getContext()->language->id;
            $configuracion_params = [$this->name => [
                '_path' => $this->_path,
                'token' => $this->secure_key,
                'APP_ID' => RgPuNoConfig::get('OS_APP_ID'),
                'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
                'RGPUNO_REQUEST_DELAY_PAGES_VIEWED' => (int) RgPuNoConfig::get('REQUEST_DELAY_PAGES_VIEWED'),
                'RGPUNO_REQUEST_DELAY_TIME' => (int) RgPuNoConfig::get('REQUEST_DELAY_TIME'),
                'RGPUNO_REQUEST_MSG' => RgPuNoConfig::get('REQUEST_MSG', $id_lang),
                'RGPUNO_REQUEST_BTN_ACCEPT' => RgPuNoConfig::get('REQUEST_BTN_ACCEPT', $id_lang),
                'RGPUNO_REQUEST_BTN_CANCEL' => RgPuNoConfig::get('REQUEST_BTN_CANCEL', $id_lang),
                'RGPUNO_DEBUG_MODE' => (bool) RgPuNoConfig::get('DEBUG_MODE'),
                'RGPUNO_PERSISTENT_NOTIF' => (bool) RgPuNoConfig::get('PERSISTENT_NOTIF'),
                'RGPUNO_POPUP_ALLOWED_SHOW' => (bool) RgPuNoConfig::get('POPUP_ALLOWED_SHOW'),
                'RGPUNO_POPUP_ALLOWED_MSG' => RgPuNoConfig::get('POPUP_ALLOWED_MSG', $id_lang),
                'RGPUNO_POPUP_DECLINED_SHOW' => (bool) RgPuNoConfig::get('POPUP_DECLINED_SHOW'),
                'RGPUNO_POPUP_DECLINED_MSG' => RgPuNoConfig::get('POPUP_DECLINED_MSG', $id_lang),
                'RGPUNO_WELCOME_SHOW' => $RGPUNO_WELCOME_SHOW,
                'RGPUNO_WELCOME_TITLE' => ($RGPUNO_WELCOME_SHOW ? RgPuNoConfig::get('WELCOME_TITLE', $id_lang) : ''),
                'RGPUNO_WELCOME_MSG' => ($RGPUNO_WELCOME_SHOW ? RgPuNoConfig::get('WELCOME_MSG', $id_lang) : ''),
                'RGPUNO_WELCOME_URL' => ($RGPUNO_WELCOME_SHOW ? RgPuNoConfig::get('WELCOME_URL', $id_lang) : ''),
                'RGPUNO_BELL_SHOW' => $RGPUNO_BELL_SHOW,
                'RGPUNO_BELL_HIDE_SUBS' => (bool) RgPuNoConfig::get('BELL_HIDE_SUBS'),
                'RGPUNO_BELL_SIZE' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_SIZE') : ''),
                'RGPUNO_BELL_THEME' => ($RGPUNO_BELL_SHOW
                    ? ($RGPUNO_BELL_THEME == 'custom' ? 'default' : RgPuNoConfig::get('BELL_THEME'))
                    : ''
                ),
                'RGPUNO_BELL_BACK' => ($RGPUNO_BELL_SHOW && $RGPUNO_BELL_THEME == 'custom' ? RgPuNoConfig::get('BELL_BACK') : ''),
                'RGPUNO_BELL_FORE' => ($RGPUNO_BELL_SHOW && $RGPUNO_BELL_THEME == 'custom' ? RgPuNoConfig::get('BELL_FORE') : ''),
                'RGPUNO_BELL_DIAG_FORE' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_DIAG_FORE') : ''),
                'RGPUNO_BELL_DIAG_BACK' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_DIAG_BACK') : ''),
                'RGPUNO_BELL_DIAG_BACK_HOVER' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_DIAG_BACK_HOVER') : ''),
                'RGPUNO_BELL_POSITION' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_POSITION') : ''),
                'RGPUNO_BELL_OFFSET_BOTOM' => ($RGPUNO_BELL_SHOW ? (int) RgPuNoConfig::get('BELL_OFFSET_BOTOM') : '15'),
                'RGPUNO_BELL_OFFSET_RIGHT' => ($RGPUNO_BELL_SHOW ? (int) RgPuNoConfig::get('BELL_OFFSET_RIGHT') : '15'),
                'RGPUNO_BELL_OFFSET_LEFT' => ($RGPUNO_BELL_SHOW ? (int) RgPuNoConfig::get('BELL_OFFSET_LEFT') : '15'),
                'RGPUNO_BELL_PRENOTIFY' => false/* (bool)RgPuNoConfig::get('BELL_PRENOTIFY') */,
                'RGPUNO_BELL_SHOW_CREDIT' => (bool) RgPuNoConfig::get('BELL_SHOW_CREDIT'),
                'RGPUNO_BELL_TIP_STATE_UNS' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_TIP_STATE_UNS', $id_lang) : ''),
                'RGPUNO_BELL_TIP_STATE_SUB' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_TIP_STATE_SUB', $id_lang) : ''),
                'RGPUNO_BELL_TIP_STATE_BLO' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_TIP_STATE_BLO', $id_lang) : ''),
                'RGPUNO_BELL_MSG_PRENOTIFY' => ''/* ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_MSG_PRENOTIFY', $id_lang) : '') */,
                'RGPUNO_BELL_ACTION_SUBS' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_ACTION_SUBS', $id_lang) : ''),
                'RGPUNO_BELL_ACTION_RESUB' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_ACTION_RESUB', $id_lang) : ''),
                'RGPUNO_BELL_ACTION_UNS' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_ACTION_UNS', $id_lang) : ''),
                'RGPUNO_BELL_MAIN_TITLE' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_MAIN_TITLE', $id_lang) : ''),
                'RGPUNO_BELL_MAIN_UNS' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_MAIN_UNS', $id_lang) : ''),
                'RGPUNO_BELL_MAIN_SUB' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_MAIN_SUB', $id_lang) : ''),
                'RGPUNO_BELL_BLOCKED_TITLE' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_BLOCKED_TITLE', $id_lang) : ''),
                'RGPUNO_BELL_BLOCKED_MSG' => ($RGPUNO_BELL_SHOW ? RgPuNoConfig::get('BELL_BLOCKED_MSG', $id_lang) : ''),
                'SAFARI_WEB_ID' => RgPuNoConfig::get('OS_SAFARI_ID'),
            ]];

            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addjqueryPlugin('fancybox');
            $this->context->controller->addJS($this->_path . 'views/js/front.js');

            $this->context->smarty->assign($this->name, [
                '_path' => $this->_path,
                'is_https' => (Tools::getShopProtocol() == 'https://'),
            ]);

            Media::addJsDef($configuracion_params);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/os-script.tpl');
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $id_order = (int) $params['id_order'];
        $order = new Order($id_order);
        $id_order_state = (int) $params['newOrderStatus']->id;

        if (RgPuNoConfig::get('EVENT_ACTIVE_' . $id_order_state)) {
            if ($id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $order->id_customer)) {
                if ($id_onesignal = RgPuNoTools::sendOrderStatusUpdateHookNotification($params['newOrderStatus'], $id_subscribers, $order, $this)
                ) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $params['newOrderStatus']->name . ' (' . (int) $order->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }

            // app subscribers
            if (Module::isEnabled('rg_psmobileapp') &&
                Configuration::get('RGMOAPP_SET_APP_NAME') &&
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $order->id_customer, true)
            ) {
                if ($id_onesignal = RgPuNoTools::sendOrderStatusUpdateHookNotification($params['newOrderStatus'], $id_subscribers, $order, $this, true)
                ) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $params['newOrderStatus']->name . ' (' . (int) $order->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }
        }
    }

    public function hookActionAdminOrdersTrackingNumberUpdate($params)
    {
        $selected_notifications = explode(',', RgPuNoConfig::get('NOTIFICATIONS'));

        if (($order = $params['order']) && $order->id_customer && in_array(1003, $selected_notifications)) {
            $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $order->id_customer);

            if ($id_subscribers && $order->shipping_number) {
                if ($id_onesignal = RgPuNoTools::sendHookNotification(1003, $id_subscribers, $order, $this)) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $this->available_notifications[1003] . ' (' . (int) $order->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }

            // app subscribers
            $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $order->id_customer, true);

            if (Module::isEnabled('rg_psmobileapp') &&
                Configuration::get('RGMOAPP_SET_APP_NAME') &&
                $id_subscribers &&
                $order->shipping_number
            ) {
                if ($id_onesignal = RgPuNoTools::sendHookNotification(1003, $id_subscribers, $order, $this, false, true)) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $this->available_notifications[1003] . ' (' . (int) $order->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }
        }
    }

    public function hookActionObjectCustomerThreadUpdateAfter($params)
    {
        $selected_notifications = explode(',', RgPuNoConfig::get('NOTIFICATIONS'));

        if (($ct = $params['object']) && $ct->id_customer && $ct->status == 'closed' && in_array(1007, $selected_notifications)) {
            $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $ct->id_customer);

            if ($id_subscribers) {
                if ($id_onesignal = RgPuNoTools::sendHookNotification(1007, $id_subscribers, $ct, $this)) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $this->available_notifications[1007] . ' (' . (int) $ct->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }

            // app subscribers
            $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $ct->id_customer, true);

            if (Module::isEnabled('rg_psmobileapp') &&
                Configuration::get('RGMOAPP_SET_APP_NAME') &&
                $id_subscribers
            ) {
                if ($id_onesignal = RgPuNoTools::sendHookNotification(1007, $id_subscribers, $ct, $this, false, true)) {
                    foreach ($id_subscribers as $id_subscriber) {
                        $notification = new RgPuNoNotification();
                        $notification->id_subscriber = (int) $id_subscriber;
                        $notification->id_onesignal = $id_onesignal;
                        $notification->title = $this->available_notifications[1007] . ' (' . (int) $ct->id . ')';
                        $notification->notification_type = 'event';
                        $notification->date_start = date('Y-m-d H:i:s');
                        $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                        $notification->add();
                    }
                }
            }
        }
    }

    public function hookActionObjectCartRuleUpdateAfter($params, $added = false)
    {
        if (!isset(Context::getContext()->dont_send_notifications)) {
            $selected_notifications = explode(',', RgPuNoConfig::get('NOTIFICATIONS'));

            if (($cart_rule = $params['object']) && $cart_rule->id_customer && in_array(1008, $selected_notifications)) {
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $cart_rule->id_customer);

                if ($id_subscribers) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1008, $id_subscribers, $cart_rule, $this, $added)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1008] . ' (' . (int) $cart_rule->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }

                // app subscribers
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $cart_rule->id_customer, true);

                if (Module::isEnabled('rg_psmobileapp') &&
                    Configuration::get('RGMOAPP_SET_APP_NAME') &&
                    $id_subscribers
                ) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1008, $id_subscribers, $cart_rule, $this, $added, true)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1008] . ' (' . (int) $cart_rule->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }
            }
        }
    }

    public function hookActionObjectCartRuleAddAfter($params)
    {
        $this->hookActionObjectCartRuleUpdateAfter($params, true);
    }

    public function hookActionUpdateQuantity($params)
    {
        $id_product = (int) $params['id_product'];
        $quantity = (int) $params['quantity'];
        $id_product_attribute = (int) $params['id_product_attribute'];
        $selected_notifications = explode(',', RgPuNoConfig::get('NOTIFICATIONS'));

        if (Module::isInstalled('mailalerts') &&
            Module::isEnabled('mailalerts') &&
            (int) Configuration::get('MA_CUSTOMER_QTY') &&
            $quantity > 0 &&
            $id_product &&
            in_array(1009, $selected_notifications)
        ) {
            include_once _PS_MODULE_DIR_ . '/mailalerts/MailAlert.php';
            $customers = MailAlert::getCustomers($id_product, $id_product_attribute);

            if (count($customers)) {
                $product = new Product((int) $id_product, false, Context::getContext()->language->id);
            }

            foreach ($customers as $customer) {
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $customer['id_customer']);

                if ($id_subscribers) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1009, $id_subscribers, $product, $this)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1009] . ' (' . (int) $product->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }

                // app subscribers
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $customer['id_customer'], true);

                if (Module::isEnabled('rg_psmobileapp') &&
                    Configuration::get('RGMOAPP_SET_APP_NAME') &&
                    $id_subscribers
                ) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1009, $id_subscribers, $product, $this, false, true)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1009] . ' (' . (int) $product->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }
            }
        }
    }

    public function hookActionProductAttributeUpdate($params)
    {
        $id_product_attribute = (int) $params['id_product_attribute'];
        $sql = 'SELECT `id_product`, `quantity`
            FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product_attribute` = ' . (int) $params['id_product_attribute'];
        $result = Db::getInstance()->getRow($sql);
        $id_product = (int) $result['id_product'];
        $selected_notifications = explode(',', RgPuNoConfig::get('NOTIFICATIONS'));

        if (Module::isInstalled('mailalerts') &&
            Module::isEnabled('mailalerts') &&
            (int) Configuration::get('MA_CUSTOMER_QTY') &&
            $result['quantity'] > 0 &&
            $id_product &&
            in_array(1009, $selected_notifications)
        ) {
            include_once _PS_MODULE_DIR_ . '/mailalerts/MailAlert.php';
            $customers = MailAlert::getCustomers($id_product, $id_product_attribute);

            if (count($customers)) {
                $product = new Product((int) $id_product, false, Context::getContext()->language->id);
            }

            foreach ($customers as $customer) {
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $customer['id_customer']);

                if ($id_subscribers) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1009, $id_subscribers, $product, $this)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1009] . ' (' . (int) $product->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }

                // app subscribers
                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $customer['id_customer'], true);

                if (Module::isEnabled('rg_psmobileapp') &&
                    Configuration::get('RGMOAPP_SET_APP_NAME') &&
                    $id_subscribers
                ) {
                    if ($id_onesignal = RgPuNoTools::sendHookNotification(1009, $id_subscribers, $product, $this, false, true)) {
                        foreach ($id_subscribers as $id_subscriber) {
                            $notification = new RgPuNoNotification();
                            $notification->id_subscriber = (int) $id_subscriber;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->title = $this->available_notifications[1009] . ' (' . (int) $product->id . ')';
                            $notification->notification_type = 'event';
                            $notification->date_start = date('Y-m-d H:i:s');
                            $notification->date_end = date('Y-m-d H:i:s', strtotime('+72 hours'));
                            $notification->add();
                        }
                    }
                }
            }
        }
    }

    public function hookActionObjectCustomerUpdateAfter($params)
    {
        if ($id_customer = $params['object']->id) {
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
                SET `id_guest` = (SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'guest` WHERE `id_customer` = ' . (int) $id_customer . ' LIMIT 1)
            ');
        }
    }

    public function hookActionObjectCustomerAddAfter($params)
    {
        $this->hookActionObjectCustomerUpdateAfter($params);
    }

    public function setNewSubcriber($id_player)
    {
        $id_subscriber = RgPuNoSubscriber::getIdSubscriberByPlayer($id_player);

        if ($id_customer = (int) Context::getContext()->customer->id) {
            $id_guest = Db::getInstance()->getValue(
                'SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'guest` WHERE `id_customer` = ' . (int) $id_customer
            );
        } elseif ($id_guest = (int) Context::getContext()->cookie->id_guest) {
            $id_customer = 0;
        }

        $subscriber = new RgPuNoSubscriber((int) $id_subscriber);

        if (!$id_subscriber ||
            $subscriber->id_customer != (int) $id_customer ||
            $subscriber->id_guest != (int) $id_guest
        ) {
            $subscriber->id_customer = (int) $id_customer;
            $subscriber->id_guest = (int) $id_guest;
            $subscriber->id_player = $id_player;
            $subscriber->save();
        }
    }

    public function ajaxProcessUploadIcon()
    {
        $icon = (isset($_FILES['notification_icon_input']) ? $_FILES['notification_icon_input'] : false);

        if ($icon && !empty($icon['tmp_name']) && $icon['tmp_name'] != 'none'
            && (!isset($icon['error']) || !$icon['error'])
            && preg_match('/\.(jpe?g|gif|png)$/', Tools::strtolower($icon['name']))
            && is_uploaded_file($icon['tmp_name'])
            && ImageManager::isRealImage($icon['tmp_name'], $icon['type'])
        ) {
            $file = $icon['tmp_name'];
            $icon_id = 100;
            $icon_files = glob($this->local_path . 'uploads/*.png');
            $icon_files = array_map('basename', $icon_files);
            $icon_files = array_map('intval', $icon_files);

            if ($icon_files = array_filter($icon_files)) {
                $icon_id = max($icon_files) + 1;
            }

            $icon_name = $icon_id . '.png';

            if (!ImageManager::resize($file, $this->local_path . 'uploads/' . $icon_name, 192, 192, 'png', true)) {
                return '<return result="error" message="' . $this->l('Impossible to resize the image') . ' ' . Tools::safeOutput(_PS_TMP_IMG_DIR_) . '" />';
            }

            @unlink($file);

            return '<return result="success" message="' . $this->l('Icon uploaded successfully') . '" />';
        }

        return '<return result="error" message="' . $this->l('Cannot upload file') . '" />';
    }
}
