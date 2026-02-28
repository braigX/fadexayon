<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @version   1.1.1
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/HiPrestaModule.php';
include_once dirname(__FILE__) . '/classes/adminForms.php';
include_once dirname(__FILE__) . '/classes/user.php';

class HiGoogleConnect extends Module
{
    public $secure_key;
    public $hiPrestaClass;
    public $adminForms;

    public $psv;
    public $errors = [];
    public $success = [];

    // General Settings
    public $enableGoogleConnect;
    public $googleClientId;
    public $cleanDb;

    public function __construct()
    {
        $this->name = 'higoogleconnect';
        $this->tab = 'front_office_features';
        $this->version = '1.1.1';
        $this->author = 'hipresta';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = 'bc684704036c9a54c970be76fe7b319c';
        $this->author_address = '0xf5655d2008293E524dF46426b60893806f12c8B0';
        parent::__construct();
        $this->globalVars();
        $this->displayName = $this->l('Sign In With Google + One Tap prompt');
        $this->description = $this->l('Allow your customers to Log in or Register with one tap by using their Google Account');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->hiPrestaClass = new HiPrestaGoogleConnect($this);
        $this->adminForms = new HiGoogleConnectAdminForms($this);

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayNav1')
            || !$this->registerHook('displayNav2')
            || !$this->registerHook('displayTop')
            || !$this->registerHook('displayCustomerLoginFormAfter')
            || !$this->registerHook('displayCustomerAccountFormTop')
            || !$this->registerHook('displayHiGoogleConnect')
            || !$this->registerHook('displayHiGoogleConnectCustom')
            || !$this->registerHook('actionDeleteGDPRCustomer')
            || !$this->registerHook('actionExportGDPRData')
            || !$this->hiPrestaClass->createTabs('AdminHiGoogleConnect', 'AdminHiGoogleConnect', 'HI_GC_CONTROLLER_TAB', 0)
        ) {
            return false;
        }
        $this->proceedDb();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        if (Configuration::get('HI_GC_CLEAN_DB')) {
            $this->proceedDb(true);
        }
        $this->hiPrestaClass->deleteTabs('HI_GC_CONTROLLER_TAB');

        return true;
    }

    private function proceedDb($drop = false)
    {
        if (!$drop) {
            Configuration::updateValue('HI_GC_CLEAN_DB', false);
            Configuration::updateValue('HI_GC_ENABLE', false);
            Configuration::updateValue('HI_GC_CLIENT_ID', '');

            $positions = $this->getPositionsList();
            foreach ($positions as $id_position => $position) {
                Configuration::updateValue('HI_GC_BUTTON_ACTIVE_' . $id_position, 0);
                Configuration::updateValue('HI_GC_BUTTON_TYPE_' . $id_position, 'standart');
                Configuration::updateValue('HI_GC_BUTTON_THEME_' . $id_position, 'outline');
                Configuration::updateValue('HI_GC_BUTTON_SHAPE_' . $id_position, 'rectangular');
                Configuration::updateValue('HI_GC_BUTTON_TEXT_' . $id_position, 'signin_with');
                Configuration::updateValue('HI_GC_BUTTON_SIZE_' . $id_position, 'large');
                Configuration::updateValue('HI_GC_ONE_TAP_PROMPT_' . $id_position, true);
            }

            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higoogleuser` (
                    `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `id_shop` int(10) NOT NULL,
                    `id_google_account` VARCHAR (250) NOT NULL,
                    `first_name` varchar (100) NOT NULL,
                    `last_name` varchar (100) NOT NULL,
                    `email` varchar (100) NOT NULL,
                    `date_add` datetime NOT NULL,
                    `date_upd` datetime NOT NULL,
                    PRIMARY KEY (`id_user`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');
        } else {
            Configuration::deleteByName('HI_GC_CLEAN_DB');
            Configuration::deleteByName('HI_GC_ENABLE');
            Configuration::deleteByName('HI_GC_CLIENT_ID');

            $positions = $this->getPositionsList();
            foreach ($positions as $id_position => $position) {
                Configuration::deleteByName('HI_GC_BUTTON_ACTIVE_' . $id_position);
                Configuration::deleteByName('HI_GC_BUTTON_TYPE_' . $id_position);
                Configuration::deleteByName('HI_GC_BUTTON_THEME_' . $id_position);
                Configuration::deleteByName('HI_GC_BUTTON_SHAPE_' . $id_position);
                Configuration::deleteByName('HI_GC_BUTTON_TEXT_' . $id_position);
                Configuration::deleteByName('HI_GC_BUTTON_SIZE_' . $id_position);
                Configuration::deleteByName('HI_GC_ONE_TAP_PROMPT_' . $id_position);
            }

            DB::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'higoogleuser');
        }
    }

    private function globalVars()
    {
        $this->psv = (float) Tools::substr(_PS_VERSION_, 0, 3);
        $this->cleanDb = (bool) Configuration::get('HI_GC_CLEAN_DB');
        $this->enableGoogleConnect = (bool) Configuration::get('HI_GC_ENABLE');
        $this->googleClientId = Configuration::get('HI_GC_CLIENT_ID');
    }

    public function renderMenuTabs()
    {
        $tabs = [
            'generelSettings' => [
                'title' => $this->l('General Settings'),
                'icon' => 'icon-cog',
            ],
            'positions' => [
                'title' => $this->l('Positions'),
                'icon' => 'icon-puzzle-piece',
            ],
            'users' => [
                'title' => $this->l('Users'),
                'icon' => 'icon-user',
            ],
            'stats' => [
                'title' => $this->l('Stats'),
                'icon' => 'icon-pie-chart',
            ],
            'rateMe' => [
                'title' => $this->l('Leave a review'),
                'icon' => 'icon-star',
                'url' => $this->getRateUrl(),
            ],
            'contactUs' => [
                'title' => $this->l('Contact Us'),
                'icon' => 'icon-support',
                'url' => $this->getContactUrl(),
            ],
            'version' => [
                'title' => $this->l('Version'),
                'icon' => 'icon-info',
            ],
        ];

        $recommendations = $this->getModuleRecommendations();
        if ($recommendations) {
            $tabs['more_modules'] = [
                'title' => $this->l('More Modules'),
                'icon' => 'icon-puzzle-piece',
            ];
        }

        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'tabs' => $tabs,
                'module_version' => $this->version,
                'module_url' => $this->hiPrestaClass->getModuleUrl(),
                'module_tab_key' => $this->name,
                'active_tab' => Tools::getValue($this->name),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/menu_tabs.tpl');
    }

    public function getRateUrl()
    {
        $langIsoCode = $this->context->language->iso_code;
        $psLanguages = ['en', 'fr', 'es', 'de', 'it', 'nl', 'pl', 'pt', 'ru'];

        if (in_array($langIsoCode, $psLanguages)) {
            return 'https://addons.prestashop.com/' . $this->context->language->iso_code . '/ratings.php';
        }

        return 'https://addons.prestashop.com/en/ratings.php';
    }

    public function getModuleRecommendations()
    {
        $recommendations = '';
        if (file_exists(__DIR__ . '/libs/hi-modules/modules.json')) {
            $recommendations = Tools::file_get_contents(__DIR__ . '/libs/hi-modules/modules.json');
            if ($recommendations) {
                $recommendations = json_decode($recommendations, true);
            }
        }

        return $recommendations ? $recommendations : [];
    }

    public function getContactUrl()
    {
        $langIsoCode = $this->context->language->iso_code;
        $psLanguages = ['en', 'fr', 'es', 'de', 'it', 'nl', 'pl', 'pt', 'ru'];

        if (in_array($langIsoCode, $psLanguages)) {
            return 'https://addons.prestashop.com/' . $this->context->language->iso_code . '/contact-us?id_product=88500';
        }

        return 'https://addons.prestashop.com/en/contact-us?id_product=88500';
    }

    public function renderVersionForm()
    {
        $changelog = '';
        if (file_exists(dirname(__FILE__) . '/changelog.txt')) {
            $changelog = Tools::file_get_contents(dirname(__FILE__) . '/changelog.txt');
        }
        $this->context->smarty->assign('changelog', $changelog);

        return $this->display(__FILE__, 'views/templates/admin/version.tpl');
    }

    public function renderShopGroupError()
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/shop_group_error.tpl');
    }

    public function renderModuleAdminVariables()
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'id_lang' => $this->context->language->id,
                'hiGoogleConnectSecureKey' => $this->secure_key,
                'hiGoogleConnectAdminController' => $this->context->link->getAdminLink('AdminHiGoogleConnect'),
                // this is used for backoffice preview
                'googleClientId' => $this->googleClientId ? $this->googleClientId : '123456789',
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/variables.tpl');
    }

    public function renderDisplayForm($content)
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'errors' => $this->errors,
                'success' => $this->success,
                'content' => $content,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/display_form.tpl');
    }

    public function renderDocumentation()
    {
        $this->context->smarty->assign([
            'moduleAssetsDir' => _MODULE_DIR_ . $this->name . '/libs/hi-modules-doc/img/',
            'contactLink' => $this->getContactUrl(),
        ]);

        return $this->display(__FILE__, 'libs/hi-modules-doc/doc.tpl');
    }

    public function postProcess()
    {
        $languages = Language::getLanguages(false);
        if (Tools::isSubmit('submitSettingsForm')) {
            Configuration::updateValue('HI_GC_CLEAN_DB', (bool) Tools::getValue('cleanDb'));
            Configuration::updateValue('HI_GC_ENABLE', (bool) Tools::getValue('enableGoogleConnect'));
            Configuration::updateValue('HI_GC_CLIENT_ID', Tools::getValue('googleClientId'));

            $this->success[] = $this->l('Successfully saved');
        }
    }

    public function renderModuleAdvertisingForm()
    {
        $recommendations = $this->getModuleRecommendations();
        $this->context->smarty->assign('modules', $recommendations);

        return $this->display(__FILE__, 'views/templates/admin/moduleadvertising.tpl');
    }

    public function renderChartStats()
    {
        if (isset($this->context->cookie->hiGoogleConnectChartType) && $this->context->cookie->hiGoogleConnectChartType) {
            $type = $this->context->cookie->hiGoogleConnectChartType;
        } else {
            $type = 'year';
        }

        $now = date('Y-m-d');
        if (isset($this->context->cookie->hiGoogleConnectChartCustomFrom) && $this->context->cookie->hiGoogleConnectChartCustomFrom) {
            $from = $this->context->cookie->hiGoogleConnectChartCustomFrom;
        } else {
            $from = date('Y-m-d', strtotime('-1 month', strtotime($now)));
        }

        if (isset($this->context->cookie->hiGoogleConnectChartCustomTo) && $this->context->cookie->hiGoogleConnectChartCustomTo) {
            $to = $this->context->cookie->hiGoogleConnectChartCustomTo;
        } else {
            $to = $now;
        }

        $this->context->smarty->assign([
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'registrationsData' => $this->getRegistrationsByDate($type, $from, $to),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/chart-stats.tpl');
    }

    public function displayForm()
    {
        $html = $this->renderModuleAdminVariables();
        $content = '';
        if (!$this->hiPrestaClass->isSelectedShopGroup()) {
            $html .= $this->renderMenuTabs();
            switch (Tools::getValue($this->name)) {
                case 'generelSettings':
                    $content .= $this->adminForms->renderSettingsForm();
                    break;
                case 'positions':
                    $content .= $this->renderModal();
                    $content .= $this->adminForms->renderPositionsList();
                    break;
                case 'users':
                    $content .= $this->adminForms->renderUsersList();
                    break;
                case 'stats':
                    $content .= $this->renderChartStats();
                    break;
                case 'version':
                    $content .= $this->renderVersionForm();
                    break;
                case 'more_modules':
                    $content .= $this->renderModuleAdvertisingForm();
                    break;
                case 'free_module':
                    $content .= $this->renderFreeModuleAdvertisingForm();
                    break;
                default:
                    $content .= $this->adminForms->renderSettingsForm();
                    break;
            }

            $content .= $this->renderDocumentation();

            $html .= $this->renderDisplayForm($content);
        } else {
            $html .= $this->renderShopGroupError();
        }

        if (Tools::getValue('higoogleconnect') == 'stats') {
            $this->context->controller->addJS($this->_path . 'libs/chart-js/chart.js');
        }

        $this->context->controller->addCSS($this->_path . 'libs/hi-modules-table/table.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/hi-modules-table/table.js');

        $this->context->controller->addCSS($this->_path . 'libs/magnific-popup/magnific-popup.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/magnific-popup/jquery.magnific-popup.min.js');

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        $this->context->controller->addCSS($this->_path . 'libs/hi-modules-doc/doc.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/hi-modules-doc/doc.js');

        return $html;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitSettingsForm')) {
            $this->postProcess();
        }
        $this->globalVars();

        return $this->displayForm();
    }

    public function getPositionsList()
    {
        return [
            1 => [
                'hook' => 'displayNav1',
                'id_position' => 1,
                'settings' => $this->getPositionSettings(1),
            ],
            2 => [
                'hook' => 'displayNav2',
                'id_position' => 2,
                'settings' => $this->getPositionSettings(2),
            ],
            3 => [
                'hook' => 'displayTop',
                'id_position' => 3,
                'settings' => $this->getPositionSettings(3),
            ],
            4 => [
                'hook' => 'displayCustomerLoginFormAfter',
                'id_position' => 4,
                'settings' => $this->getPositionSettings(4),
            ],
            5 => [
                'hook' => 'displayCustomerAccountFormTop',
                'id_position' => 5,
                'settings' => $this->getPositionSettings(5),
            ],
            6 => [
                'hook' => $this->l('Custom') . ' {hook h=\'displayHiGoogleConnect\'}',
                'id_position' => 6,
                'settings' => $this->getPositionSettings(6),
            ],
        ];
    }

    public function getPositionSettings($id_position)
    {
        return [
            'id_position' => $id_position,
            'active' => Configuration::get('HI_GC_BUTTON_ACTIVE_' . $id_position),
            'buttonType' => Configuration::get('HI_GC_BUTTON_TYPE_' . $id_position),
            'buttonTheme' => Configuration::get('HI_GC_BUTTON_THEME_' . $id_position),
            'buttonShape' => Configuration::get('HI_GC_BUTTON_SHAPE_' . $id_position),
            'buttonText' => Configuration::get('HI_GC_BUTTON_TEXT_' . $id_position),
            'buttonSize' => Configuration::get('HI_GC_BUTTON_SIZE_' . $id_position),
            'enableOneTapPrompt' => Configuration::get('HI_GC_ONE_TAP_PROMPT_' . $id_position),
        ];
    }

    public function savePositionSettings($id_position)
    {
        Configuration::updateValue('HI_GC_BUTTON_ACTIVE_' . $id_position, (bool) Tools::getValue('active'));
        Configuration::updateValue('HI_GC_BUTTON_TYPE_' . $id_position, Tools::getValue('buttonType'));
        Configuration::updateValue('HI_GC_BUTTON_THEME_' . $id_position, Tools::getValue('buttonTheme'));
        Configuration::updateValue('HI_GC_BUTTON_SHAPE_' . $id_position, Tools::getValue('buttonShape'));
        Configuration::updateValue('HI_GC_BUTTON_TEXT_' . $id_position, Tools::getValue('buttonText'));
        Configuration::updateValue('HI_GC_BUTTON_SIZE_' . $id_position, Tools::getValue('buttonSize'));
        Configuration::updateValue('HI_GC_ONE_TAP_PROMPT_' . $id_position, (bool) Tools::getValue('enableOneTapPrompt'));
    }

    public function renderModal($class = null)
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'modal_class' => $class,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/modal.tpl');
    }

    public function displayAjaxError($message)
    {
        exit(Tools::jsonEncode([
            'error' => $message,
        ]));
    }

    public function isContentSizeValid($content, $size)
    {
        if (iconv_strlen($content) > $size) {
            return false;
        }

        return true;
    }

    public function hookDisplayHeader()
    {
        if (!$this->enableGoogleConnect || !$this->googleClientId || $this->context->customer->isLogged()) {
            return false;
        }

        $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');

        Media::addJsDef([
            'hiGoogleConnect' => [
                'frontUrl' => $this->context->link->getModuleLink($this->name, 'googleConnect'),
                'secure_key' => $this->secure_key,
            ],
        ]);

        return $this->display(__FILE__, 'header.tpl');
    }

    public function renderHookContent($id_position, $hooName)
    {
        if ($this->context->customer->isLogged()) {
            return false;
        }

        if (!$this->enableGoogleConnect || !$this->googleClientId || !Configuration::get('HI_GC_BUTTON_ACTIVE_' . $id_position)) {
            return false;
        }

        $this->context->smarty->assign([
            'googleClientId' => $this->googleClientId,
            'hiGoogleButtonSettings' => $this->getPositionSettings($id_position),
            'hook' => $hooName,
            'langIsoCode' => $this->context->language->iso_code,
        ]);

        return $this->display(__FILE__, 'google-connect.tpl');
    }

    // position 1
    public function hookDisplayNav1($params)
    {
        return $this->renderHookContent(1, 'displayNav1');
    }

    // position 2
    public function hookDisplayNav2($params)
    {
        return $this->renderHookContent(2, 'displayNav2');
    }

    // position 3
    public function hookDisplayTop($params)
    {
        return $this->renderHookContent(3, 'displayTop');
    }

    // position 4
    public function hookDisplayCustomerLoginFormAfter($params)
    {
        return $this->renderHookContent(4, 'displayCustomerLoginFormAfter');
    }

    // position 5
    public function hookDisplayCustomerAccountFormTop($params)
    {
        return $this->renderHookContent(5, 'displayCustomerAccountFormTop');
    }

    // position 6
    public function hookDisplayHiGoogleConnect($params)
    {
        return $this->renderHookContent(6, 'displayHiGoogleConnect');
    }

    // custom hook for more use
    public function hookDisplayHiGoogleConnectCustom($params)
    {
        if (!$this->enableGoogleConnect || !$this->googleClientId || $this->context->customer->isLogged()) {
            return false;
        }

        $this->context->smarty->assign([
            'googleClientId' => $this->googleClientId,
            'hiGoogleButtonSettings' => [
                'id_position' => 9999,
                'active' => true,
                'buttonType' => (isset($params['buttonType']) ? $params['buttonType'] : 'standart'),
                'buttonTheme' => (isset($params['buttonTheme']) ? $params['buttonTheme'] : 'outline'),
                'buttonShape' => (isset($params['buttonShape']) ? $params['buttonShape'] : 'rectangular'),
                'buttonText' => (isset($params['buttonText']) ? $params['buttonText'] : 'signin_with'),
                'buttonSize' => (isset($params['buttonSize']) ? $params['buttonSize'] : 'large'),
                'enableOneTapPrompt' => (isset($params['oneTap']) ? $params['oneTap'] : 0),
            ],
            'hook' => 'displayHiGoogleConnectCustom',
            'langIsoCode' => $this->context->language->iso_code,
        ]);

        return $this->display(__FILE__, 'google-connect.tpl');
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . "higoogleuser WHERE email = '" . pSQL($customer['email']) . "'";
            if (Db::getInstance()->execute($sql)) {
                return json_encode(true);
            }

            return json_encode($this->l('Unable to delete customer using email'));
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $res2 = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . "higoogleuser WHERE email = '" . pSQL($customer['email']) . "'");
            $res = array_merge($res1, $res2);

            if ($res) {
                return json_encode($res);
            }

            return json_encode($this->l('Unable to export customer using email'));
        }
    }

    public function getRegistrationsByDate($type, $from = null, $to = null)
    {
        if ($type == 'custom') {
            if (!$from || !$to) {
                return [
                    'totalCustomers' => 0,
                    'totalOtherRegistrations' => 0,
                    'totalGoogleUsers' => 0,
                ];
            }
            $customers = Db::getInstance()->getValue('SELECT count(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` WHERE date_add BETWEEN \'' . pSQL($from) . '\' AND \'' . pSQL($to) . '\'');
            $googleUsers = Db::getInstance()->getValue('SELECT count(`id_user`) FROM `' . _DB_PREFIX_ . 'higoogleuser` WHERE date_add BETWEEN \'' . pSQL($from) . '\' AND \'' . pSQL($to) . '\'');
        } elseif ($type == 'all') {
            $customers = Db::getInstance()->getValue('SELECT count(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer`');
            $googleUsers = Db::getInstance()->getValue('SELECT count(`id_user`) FROM `' . _DB_PREFIX_ . 'higoogleuser`');
        } else {
            $customers = Db::getInstance()->getValue('SELECT count(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` WHERE date_add > now() - interval 1 ' . pSQL($type));
            $googleUsers = Db::getInstance()->getValue('SELECT count(`id_user`) FROM `' . _DB_PREFIX_ . 'higoogleuser` WHERE date_add > now() - interval 1 ' . pSQL($type));
        }

        return [
            'totalCustomers' => (int) $customers,
            'totalOtherRegistrations' => (int) ($customers - $googleUsers),
            'totalGoogleUsers' => (int) $googleUsers,
        ];
    }
}
