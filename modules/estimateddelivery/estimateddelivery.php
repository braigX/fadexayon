<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 *
 * @version 3.9.8
 * ***************************************************
 * *              Estimated Delivery                 *
 * *          http://www.smart-modules.com           *
 * *                    V 3.9.8                      *
 *
 * Versions: To see the Changelog check versions.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/autoload.php';

class EstimatedDelivery extends Module
{
    const PREFIX = 'ED_';
    const MAX_CAT_DISPLAY = 150; // Default value 150
    const CART_SUMMARY = 1;
    const CART_SUMMARY_FOOTER = 2;
    const CART_SUMMARY_IND = 5;
    const CART_SUMMARY_ALL = 10;

    /* Media set check */
    private $front_media_set = false;
    private $shops = [];
    private static $today;
    private static $tomorrow;
    private static $microtime;
    private static $microtime_init;
    public static $debug_time;
    private static $debug_times = [];
    private static $ed_orders = [];
    protected $config_form = false;
    public static $debug_mode;
    /* Use Today or Tomorrow for close dates */
    public static $tot;
    public $print_debug = false;
    protected $force_ip = '';
    protected $form_fields = '';
    protected $user_group = '';
    protected $ip_info = '';
    protected $user_data = '';
    protected $ip_carriers = false;
    protected $addr_carriers = false;
    protected $id_carriers = false;
    protected $id_zone = 0;
    protected $holidayDates = [];
    protected $adv_carr = false;
    protected $adv_mode = false;
    protected $adv_picking = false;
    protected $fo_media_set = false;
    protected $mobile_device = false;
    protected $cat_count = 0;
    protected $cache_modules = ['pagecache', 'ets_superspeed'];

    /* Flags to prevent display in more than one hook */
    private $is_displayed_calendar = false;
    private $is_displayed_summary = false;
    private $modal_displayed = false;

    public $extra_mail_vars = [
        '{estimateddelivery}' => '',
        '{ed_parcel_delivery}' => '',
    ];
    // TODO move this inside the form generation and handling
    // All date formats
    protected $dateFormat = []; // TODO update to protected  // TODO 2 remove it?
    // List-related  date formats
    protected $listDateFormat = [];

    protected $locale = false;
    public $hook_name = '';

    // Special Messages
    private $undefined_msg;
    private $available_msg;
    private $release_msg;
    private $virtual_msg;
    private $customization_msg;

    public function __construct()
    {
        // Old Translation System
        $this->old_ts = version_compare(_PS_VERSION_, '1.7.3', '<') ? true : false;
        if ($this->old_ts && (Tools::getValue('controller') == 'AdminOrders') && (Tools::getIsset('submitState') || Tools::getIsset('submitShippingNumber') || Tools::getIsset('ed_token'))) {
            // It's a state update and PrestaShop version is below 1.7.3 this means custom translations can't be fetched for the email messages
            // Will update the context object by setting the language to the one used in the order.
            $order = new Order((int) Tools::getValue('id_order'));
            $context = Context::getContext();
            $context->language = new Language((int) $order->id_lang);
            if (method_exists('Context', 'setInstanceForTesting')) {
                Context::setInstanceForTesting($context);
            }
        }
        $this->name = 'estimateddelivery';
        $this->tab = 'shipping_logistics';
        $this->version = '3.9.8';
        $this->author = 'Smart Modules';
        $this->need_instance = 0;
        $this->module_key = '93e53030e149cdfc2816fd51f150597b';
        $this->bootstrap = true;
        $this->displayName = $this->l('Estimated Delivery');
        $this->description = $this->l('Let your customers know when they will receive their order');
        $this->confirmUninstall = $this->l('Are you sure to uninstall the Estimated Delivery, all data will be lost?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        self::$microtime = microtime(true);
        // $this->lastMicrotime = microtime(true);

        parent::__construct();

        //        SmartForm::init($this);
        // If Advanced Carriers are enabled
        $this->prefix = 'ED_';
        $this->adv_carr = (bool) Configuration::get('ed_carrier_adv');
        $this->adv_picking = (bool) Configuration::get('ed_picking_adv');
        $this->adv_mode = (bool) Configuration::get('ed_adv_mode');

        $this->loadDateFormats();

        self::$debug_mode = $this->getDebugMode();
        $this->print_debug = Configuration::get('ed_debug_force_print');
        self::$debug_time = $this->getDebugTime();
        if (self::$debug_time) {
            $this->saveDebugTime('Module Constructor');
        }
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->is_17 = false;
        } else {
            $this->is_17 = true;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->smarty->assign(['old_ps' => 1]);
        } else {
            $this->context->smarty->assign(['old_ps' => 0]);
        }
        $this->controllerName = EDTools::getControllerName();
        $this->is_active = $this->isTestModeActive();
        $this->isDisplayed = false;
        self::$today = $this->l('today');
        self::$tomorrow = $this->l('tomorrow');
    }

    public function getAdvMode()
    {
        return $this->adv_mode;
    }

    private function getConfigurationFields()
    {
        // Load the variables for the config form
        include 'src/config-fields.php';

        return [
            'arrays' => $arrays,
            'ints' => $ints,
            'texts' => $texts,
            'msgs' => $msgs,
            'html' => $html,
            'langs' => $langs,
            'weekdays' => $weekdays,
            'json' => $json,
        ];
    }

    /**
     * Return the configuration fields grouped by section "s"
     * Used by the configurator restore module
     */
    public function getConfigurationFieldsByGroups()
    {
        if (Module::isEnabled('smartdemosmulticonfiguration')) {
            return SmartDemosMultiConfiguration::getConfigurationFieldsByGroups($this->form_fields);
        }

        return false;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install($reset = false)
    {
        foreach ($this->getConfigurationFields() as $type_group) {
            foreach ($type_group as $field) {
                Configuration::updateValue($field['name'], $field['def']);
            }
        }

        if (!$reset) {
            include dirname(__FILE__) . '/sql/install.php';
        }
        $this->generateAjaxToken();
        $this->manageTabs();

        if (parent::install()) {
            // Create ED Carriers
            $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
            if (!empty($carriers)) {
                foreach ($carriers as $carrier) {
                    if (!$this->edCarrierExists($carrier['id_reference'])) {
                        $this->createEDCarrier($carrier['id_reference']);
                    }
                }
            }

            return $this->installHooks();
        }

        return false;
    }

    public function uninstall($reset = false)
    {
        foreach ($this->getConfigurationFields() as $type_group) {
            foreach ($type_group as $field) {
                Configuration::deleteByName($field['name']);
            }
        }
        Configuration::deleteByName('ed_dismiss_locale_check');
        if (!$reset) {
            include dirname(__FILE__) . '/sql/uninstall.php';
        }

        return parent::uninstall()
            && $this->manageTabs(true);
    }

    /**
     * Retrieves a list of hooks used by the module with translatable descriptions and context.
     *
     * @param bool $only_keys Return only the hook names if true
     *
     * @return array List of hooks with details or only names if $only_keys is true
     */
    /**
     * Retrieves a list of hooks used by the module with translatable descriptions and context.
     *
     * @param bool $only_keys Return only the hook names if true
     *
     * @return array List of hooks with details or only names if $only_keys is true
     */
    private function getHooks($only_keys = true)
    {
        $hooks = [
            // Front office hooks
            'displayHeader' => [
                'description' => $this->l('Adds JavaScript and CSS files for module functionality. Does nothing if media is already set.'),
                'context' => 'front',
            ],
            'actionFrontControllerSetMedia' => [
                'description' => $this->l('Adds JavaScript and CSS files for module functionality. Does nothing if media is already set.'),
                'context' => 'front',
            ],
            'displayProductFooter' => [
                'description' => $this->l('Displays estimated delivery information in the product footer.'),
                'context' => 'front',
            ],
            'displayProductDeliveryTime' => [
                'description' => $this->l('Shows delivery time estimates on the product page (PrestaShop 1.6 or older).'),
                'context' => 'front',
            ],
            'displayProductButtons' => [
                'description' => sprintf($this->l('Displays estimated delivery near action buttons on product pages. Alias of %s.'), 'displayProductAdditionalInfo'),
                'context' => 'front',
            ],
            'displayProductAdditionalInfo' => [
                'description' => $this->l('Displays estimated delivery in the additional info area on product pages (PrestaShop 1.7+).'),
                'context' => 'front',
            ],
            'displayShoppingCart' => [
                'description' => $this->l('Displays estimated delivery information in the shopping cart.'),
                'context' => 'front',
            ],
            'displayShoppingCartFooter' => [
                'description' => $this->l('Adds estimated delivery information to the cart footer.'),
                'context' => 'front',
            ],
            'displayBeforeCarrier' => [
                'description' => $this->l('Displays estimated delivery before carrier selection in the checkout process.'),
                'context' => 'front',
            ],
            'displayAfterCarrier' => [
                'description' => $this->l('Displays estimated delivery after carrier selection in the checkout process.'),
                'context' => 'front',
            ],
            'displayProductAMPDeliveryDate' => [
                'description' => $this->l('Shows delivery estimates on AMP (Accelerated Mobile Pages) product pages.'),
                'context' => 'front',
            ],
            'displayProductTab' => [
                'description' => $this->l('Adds an Estimated Delivery tab on the product page.'),
                'context' => 'front',
            ],
            'displayProductTabContent' => [
                'description' => $this->l('Displays content in the Estimated Delivery tab on the product page.'),
                'context' => 'front',
            ],
            'displayProductExtraContent' => [
                'description' => $this->l('Displays estimated delivery in the product tabs area (PrestaShop 1.7+).'),
                'context' => 'front',
            ],
            'displayProductListFunctionalButtons' => [
                'description' => $this->l('Allows estimated delivery to display on product listing pages.'),
                'context' => 'front',
            ],
            'displayEDInProductList' => [
                'description' => $this->l('Allows estimated delivery to display on product listing pages.'),
                'context' => 'front',
            ],
            'displayCartSummaryProductDelivery' => [
                'description' => $this->l('Displays estimated delivery within the cart summary.'),
                'context' => 'front',
            ],
            'displayCartModalContent' => [
                'description' => $this->l('Displays estimated delivery within the cart modal.'),
                'context' => 'front',
            ],
            'displayOrderConfirmation' => [
                'description' => $this->l('Displays estimated delivery on the order confirmation page.'),
                'context' => 'front',
            ],
            'displayOrderDetail' => [
                'description' => $this->l('Displays estimated delivery on the order detail page.'),
                'context' => 'front',
            ],
            'displayPaymentTop' => [
                'description' => $this->l('Shows estimated delivery information at the top of the payment page.'),
                'context' => 'front',
            ],
            'displayLeftColumnProduct' => [
                'description' => $this->l('Displays estimated delivery information in the left column on product pages (older PrestaShop versions).'),
                'context' => 'front',
            ],
            'displayRightColumnProduct' => [
                'description' => $this->l('Shows estimated delivery in the right column on product pages (older PrestaShop versions).'),
                'context' => 'front',
            ],

            // Back office hooks
            'actionAdminControllerSetMedia' => [
                'description' => $this->l('Adds assets to back-office pages for module functionalities.'),
                'context' => 'back',
            ],
            'displayAdminProductsExtra' => [
                'description' => $this->l('Adds estimated delivery configuration to the admin product edit page.'),
                'context' => 'back',
            ],
            'actionProductUpdate' => [
                'description' => $this->l('Updates estimated delivery information when a product is modified.'),
                'context' => 'back',
            ],
            'actionCarrierUpdate' => [
                'description' => $this->l('Updates estimated delivery data when carriers are modified.'),
                'context' => 'back',
            ],
            'actionValidateOrder' => [
                'description' => $this->l('Registers estimated delivery information when an order is created.'),
                'context' => 'back',
            ],
            'actionOrderStatusPostUpdate' => [
                'description' => $this->l('Updates estimated delivery data when an order status is updated.'),
                'context' => 'back',
            ],
            'sendMailAlterTemplateVars' => [
                'description' => $this->l('Allows the module to add estimated delivery data to email template variables.'),
                'context' => 'back',
            ],
            'actionGetExtraMailTemplateVars' => [
                'description' => $this->l('Allows the module to add estimated delivery data to email template variables.'),
                'context' => 'back',
            ],
            'displayAdminOrder' => [
                'description' => $this->l('Displays estimated delivery data in the admin order view.'),
                'context' => 'back',
            ],
            'displayPDFInvoice' => [
                'description' => $this->l('Adds estimated delivery details to PDF invoices.'),
                'context' => 'back',
            ],
            'actionOrderGridDefinitionModifier' => [
                'description' => $this->l('Allows adding estimated delivery columns to the order list page.'),
                'context' => 'back',
            ],
            'actionOrderGridQueryBuilderModifier' => [
                'description' => $this->l('Allows adding estimated delivery columns to the order list page.'),
                'context' => 'back',
            ],
            'actionOrderGridDataModifier' => [
                'description' => $this->l('Allows adding estimated delivery columns to the order list page.'),
                'context' => 'back',
            ],
            'actionAdminOrdersListingResultsModifier' => [
                'description' => $this->l('Allows adding estimated delivery columns to the order list page.'),
                'context' => 'back',
            ],
            'actionAdminOrdersListingFieldsModifier' => [
                'description' => $this->l('Allows adding estimated delivery columns to the order list page.'),
                'context' => 'back',
            ],
        ];

        // Add conditional hooks for PrestaShop 1.7+
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (defined('ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK')) {
                $hooks[ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK] = [
                    'description' => $this->l('Allows customization of mail themes for Estimated Delivery.'),
                    'context' => 'back',
                ];
            }
            if (defined('LayoutVariablesBuilderInterface::BUILD_MAIL_LAYOUT_VARIABLES_HOOK')) {
                $hooks[LayoutVariablesBuilderInterface::BUILD_MAIL_LAYOUT_VARIABLES_HOOK] = [
                    'description' => $this->l('Allows customization of mail layout variables.'),
                    'context' => 'back',
                ];
            }
        }

        // Return only keys if $only_keys is true
        if ($only_keys) {
            return array_keys($hooks);
        }

        // Sort hooks by context: front first, then back
        uasort($hooks, function ($a, $b) {
            return strcmp($b['context'], $a['context']);
        });

        return $hooks;
    }

    private function installHooks()
    {
        foreach ($this->getHooks() as $hook) {
            if (!$this->isRegisteredInHook($hook) && !$this->registerHook($hook)) {
                $this->context->controller->errors[] = 'Hook ' . $hook . ' Could not be installed';
            }
        }

        return true;
    }

    public function reset()
    {
        if ($this->uninstall(true) === false) {
            return false;
        }
        if ($this->install(true) === false) {
            return false;
        }

        return true;
    }

    /**
     * Generate a unique key for each shop.
     * Can be only generated once
     */
    public function generateAjaxToken()
    {
        if (!Configuration::hasKey('ED_AJAX_TOKEN')) {
            Configuration::updateValue('ED_AJAX_TOKEN', md5(_COOKIE_KEY_ . $this->name));
        }
    }

    public function manageTabs($delete = false)
    {
        $langs = Language::getLanguages();
        $trans = [
            'en' => 'Order Picking',
            'es' => 'Preparación de Pedidos',
            'ca' => 'Comandes per preparar',
            'fr' => 'Commande prise',
            'pl' => 'Kompletacja Zamówień',
            'pt' => 'Preparação de pedido',
            'ru' => 'Подготовка заказа',
            'no' => 'Ordreplukking',
            'it' => 'Preparazione dell\'ordine',
            'de' => 'Auftrag zusammenstellen',
            'cl' => 'Preparación de Pedidos',
            'mx' => 'Preparación de Pedidos',
            'co' => 'Preparación de Pedidos',
            'nl' => 'Orderpicken',
            'gr' => 'Προετοιμασία της παραγγελίας',
        ];
        $tabNames = [];
        foreach ($langs as $lang) {
            if (isset($trans[$lang['iso_code']])) {
                $tabNames[$lang['id_lang']] = $trans[$lang['iso_code']];
            }
        }

        $tabs = [
            [
                'parent' => 'SELL',
                'class' => 'AdminPickingList',
                'visible' => 1,
                'icon' => 'date_range',
                'name' => $trans,
            ],
            [
                'parent' => '0',
                'class' => 'AdminEstimatedDelivery',
                'visible' => 0,
                'name' => 'ED Ajax',
            ],
        ];
        if (!$delete) {
            foreach ($tabs as $tab) {
                if ($this->installTab($tab) === false) {
                    return false;
                }
            }
        }
        if ($delete && $this->uninstallTab() === false) {
            return false;
        }

        return true;
    }

    private function installTab($data)
    {
        $tabId = (int) Tab::getIdFromClassName($data['class']);
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->visible = $data['visible'];
        $tab->class_name = $data['class'];
        $tab->name = $this->getIdFromIso($data['name']);
        $tab->id_parent = Tab::getIdFromClassName($data['parent']);
        if (isset($data['icon'])) {
            $tab->icon = $data['icon'];
        }

        return $tab->save();
    }

    private function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    private function getIdFromIso($names)
    {
        $langs = Language::getLanguages();
        $ret = [];
        foreach ($langs as $l) {
            if (!is_array($names)) {
                $name = $names;
            } elseif (array_key_exists($l['iso_code'], $names)) {
                $name = $names[$l['iso_code']];
            } else {
                $name = $names['en'];
            }
            $ret[$l['id_lang']] = $name;
        }

        return $ret;
    }

    public function renderHooksManagementSection()
    {
        // Gather hooks data
        $hooks = [];
        $hook_context = '';
        $context_title = [
            'front' => $this->l('Front Office Hooks (Customer Area)'),
            'back' => $this->l('Back Office Hooks (Admin Area)'),
        ];

        foreach ($this->getHooks(false) as $hook => $data) {
            if ($data['context'] != $hook_context) {
                $hooks[] = [
                    'context' => $context_title[$data['context']],
                    'is_section' => true,
                ];
                $hook_context = $data['context'];
            }

            $hooks[] = [
                'hook' => $hook,
                'is_section' => false,
                'is_enabled' => $this->isRegisteredInHook($hook),
                'description' => $data['description'],
            ];
        }

        // Assign data to Smarty
        $this->context->smarty->assign([
            'hooks' => $hooks,
        ]);

        // Render the template
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/hooks_management.tpl');
    }

    /**
     * Hooks allows to modify Order grid definition.
     * This hook is a right place to add/remove columns or actions (bulk, grid).
     *
     * @param array $params
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        if (Configuration::get('ED_ORDER') && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            /** @var GridDefinitionInterface $definition */
            $definition = $params['definition'];
            $definition
                ->getColumns()
                ->addBefore(
                    'date_add',
                    (new PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn('ed_date'))
                        // (new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn('ed_date'))
                        ->setName($this->l('Delivery Date'))
                        ->setOptions([
                            'field' => 'ed_date',
                            // 'format' => $this->context->language->date_format_lite,
                            'clickable' => false,
                        ])
                )
                ->addBefore(
                    'ed_date',
                    (new PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn('picking_date'))
                        // (new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn('picking_date'))
                        // (new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn('picking_date'))
                        ->setName($this->l('Picking Limit'))
                        ->setOptions([
                            'field' => 'picking_date',
                            // 'format' => $this->context->language->date_format_lite,
                            'clickable' => false,
                        ])
                );
        }
    }

    /**
     * Hook allows to modify Orders query builder and add custom sql statements.
     * Modify the query to get the parameters
     *
     * @param array $params
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        if (Configuration::get('ED_ORDER') && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            /** @var QueryBuilder $searchQueryBuilder */
            $searchQueryBuilder = $params['search_query_builder'];
            $df = $this->getMySQLDateFormat();
            $this->setBetterLocales();
            $searchQueryBuilder->addSelect(
                'CONCAT_WS(" - ", DATE_FORMAT(edo.`delivery_min`, "' . $df . '"), DATE_FORMAT(edo.`delivery_max`, "' . $df . '")) AS `ed_date`'
            );
            $searchQueryBuilder->addSelect(
                'DATE_FORMAT(edo.`picking_day`, "' . $df . '") AS `picking_date`'
            );
            $searchQueryBuilder->addSelect(
                'edo.`undefined_delivery`'
            );
            $searchQueryBuilder->leftJoin(
                'o',
                '`' . _DB_PREFIX_ . 'ed_orders`',
                'edo',
                'edo.`id_order` = o.`id_order`'
            );
        }
    }

    /**
     * Grid modifier after 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionOrderGridDataModifier(array $params)
    {
        if (Configuration::get('ED_ORDER') && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            if (empty($params['data'])) {
                return;
            }
            SmartForm::init($this);
            /** @var PrestaShop\PrestaShop\Core\Grid\Data\GridData $gridData */
            // Get the Data
            $gridData = $params['data'];
            $table_data = $gridData->getRecords()->all();
            // Modify it
            foreach ($table_data as $key => $data) {
                $invalid_dates = ['1970-01-01', Tools::formatDateStr('1970-01-01')];
                if ($data['undefined_delivery']) {
                    $undefined_date = EDTools::formatDateForOrderList($this->l('Undefined'), $this, 'picking', $table_data[$key]['id_order']);
                    $table_data[$key]['picking_date'] = $undefined_date;
                    $table_data[$key]['ed_date'] = $undefined_date;
                } else {
                    if (!empty($data['picking_date']) && !in_array($data['picking_date'], $invalid_dates)) {
                        $table_data[$key]['picking_date'] = EDTools::formatDateForOrderList($data['picking_date'], $this, 'picking', $table_data[$key]['id_order']);
                    }
                }
            }
            // Save the new data
            $params['data'] = new PrestaShop\PrestaShop\Core\Grid\Data\GridData(
                new PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection($table_data),
                $gridData->getRecordsTotal(),
                $gridData->getQuery()
            );
        }
    }

    /**
     * Hook allows to modify Order grid data before 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionAdminOrdersListingFieldsModifier(array $params)
    {
        if (Configuration::get('ED_ORDER') && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            // Initialize the SmartForm class
            SmartForm::init($this);
            // If hook is called in AdminController::processFilter() we have to check existence
            if (isset($params['select'])) {
                $df = $this->getMySQLDateFormat();
                $this->setBetterLocales();
                $params['select'] .= ', CONCAT_WS(" - ", DATE_FORMAT(edo.`delivery_min`, "' . $df . '"), DATE_FORMAT(edo.`delivery_max`, "' . $df . '")) AS `ed_date`, IF(ISNULL(edo.picking_day)=1,NULL, DATE_FORMAT(edo.`picking_day`, "' . $df . '")) AS `picking_date`';
                $params['select'] = str_replace(',,', ',', $params['select']);
            }

            // If hook is called in AdminController::processFilter() we have to check existence
            if (isset($params['join'])) {
                // $params['join'] .= 'LEFT JOIN ' . _DB_PREFIX_ . 'ed_orders AS edo ON (edo.`id_order` = a.`id_order`)';
                // Alternative method >>
                $params['join'] .= "\n\t\t" . 'LEFT JOIN (SELECT id_order, delivery_min, delivery_max, picking_day FROM ' . _DB_PREFIX_ . 'ed_orders) AS edo ON (edo.`id_order` = a.`id_order`)';
            }
            $list = [];
            $picking_times = DB::getInstance()->executeS('SELECT id_order, picking_day FROM ' . _DB_PREFIX_ . 'ed_orders'); // WAS .' WHERE 1 '.Shop::addSqlRestriction());

            if (false === empty($picking_times)) {
                foreach ($picking_times as $order) {
                    $list[(int) $order['id_order']] = $order['picking_day'];
                }
            }

            $params['fields']['picking_date'] = [
                'title' => $this->l('Picking Day'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => false,
                'filter' => 'edo!picking_day',
                'filter_key' => 'edo!picking_day',
                'order_key' => 'edo!picking_day',
                'type' => 'datetime',
                'format' => $this->context->language->date_format_lite,
                'list' => $list,
            ];

            $params['fields']['ed_date'] = [
                'title' => $this->l('Delivery Date'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'edo!delivery_min',
                'order_key' => 'edo!delivery_min',
                'type' => 'datetime',
                'format' => $this->context->language->date_format_lite,
                'list' => $list,
            ];

            return $params;
        }
    }

    /**
     * Hook allows to modify Order grid data before 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionAdminOrdersListingResultsModifier(array $params)
    {
        if (Configuration::get('ED_ORDER') && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            foreach ($params['list'] as $key => $fields) {
                if (empty($fields['picking_date']) || $fields['picking_date'] == '1970-01-01') {
                    $params['list'][$key]['picking_date'] = '---';
                } else {
                    $params['list'][$key]['picking_date'] = EDTools::formatDateForOrderList($params['list'][$key]['picking_date'], $this, 'picking');
                }
            }
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        //        echo $_SERVER['REMOTE_ADDR'];
        //        if (in_array($_SERVER['REMOTE_ADDR'], ['2a0c:5a87:5801:8100:c5da:683e:cb1c:77b8', '79.117.190.252'])) {
        //            Tools::dieObject(Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'ed_cat WHERE excluded = 1'), false);
        //        }
        // Init the Smart Form class
        SmartForm::init($this);

        $this->form_fields = $this->getConfigurationFields();
        $this->setBoVars();
        $output = '';
        // Check emails templates just in case there is a new Language
        $template = 'ed_update'; // OLD WAS .(version_compare(_PS_VERSION_, '1.7', '<=') ? '-16' : '');
        if (version_compare(_PS_VERSION_, '1.6.0.9', '<=')) {
            $output .= $this->display(__FILE__, 'views/templates/admin/bo-js-vars-old.tpl');
        }
        $output .= $this->checkEmailTemplates($template);
        $this->checkMissingEmailTranslations($template);
        // If advanced mode is enabled don't check the locales
        if ($this->adv_mode == false) {
            $output .= $this->checkLanguageLocales();
        }
        if (Tools::getIsset('reinstallhooks')) {
            $this->installHooks();
        }
        // If values have been submitted in the form, process.
        if (Tools::getIsset('successUpdate') && Tools::getValue('successUpdate') == 1) {
            $output .= $this->displayConfirmation($this->l('Settings Updated'));
        }
        if (Tools::isSubmit('review_ed_db')) {
            $output .= $this->reviewUpdates();
            $output .= $this->_postProcess();
        }

        if (Tools::isSubmit('submit' . $this->name . 'Module') && Tools::getIsset('ED_LOCATION')) {
            $output .= $this->_postProcess();
        }

        if (Tools::isSubmit('delete' . $this->name)) {
            $id_holidays = (int) Tools::getValue('id_holidays');
            if ($id_holidays > 0) {
                Db::getInstance()->delete('ed_holidays', 'id_holidays = ' . (int) $id_holidays);
            }
        }
        if (Tools::isSubmit('status' . $this->name)) {
            // Status is saved on the active column
            $output .= $this->updateHolidayState('active');
        }
        if (Tools::isSubmit('repeat' . $this->name)) {
            $output .= $this->updateHolidayState('repeat');
        }
        foreach ($this->shops as $id_shop) {
            if (Tools::isSubmit('toggle_shop_' . (int) $id_shop . '_status' . $this->name)) {
                $output .= $this->updateHolidayShopState($id_shop);
            }
        }

        $this->displayUsefullInformations();
        $this->context->smarty->assign(
            [
                'module_dir' => $this->_path,
                'input_limit' => ini_get('max_input_vars'),
                'selected_menu' => Tools::getValue('selected_menu'),
            ]
        );
        // Review if test mode is correclty configured
        if (Configuration::get('ed_dd_test_mode')) {
            if (Configuration::get('ed_dd_test_orders_mode') == 'file') {
                $err = false;
                $path = __DIR__ . '/logs';
                if (!file_exists($path)) {
                    if (!mkdir($path) && !is_dir($path)) {
                        $err = true;
                    }
                }
                if (!$err) {
                    if (!is_writable($path)) {
                        $err = true;
                    }
                }
                if ($err) {
                    $this->context->controller->errors[] = sprintf($this->l('%s folder is not writeable. Delayed Delivery test results won\'t be saved. Please review the %s folder exist and it\'s writeable. The folder should be located at %s '), '"logs"', '"logs"', $path);
                }
            } else {
                if (!Validate::isEmail(Configuration::get('ed_dd_test_orders_email'))) {
                    $this->context->controller->errors[] = sprintf($this->l('The email configured for Delayed Delivery Test %s is not correct, please review on section 5.1 and fix it before running the test'), '"' . Configuration::get('ed_dd_test_orders_email') . '"');
                }
            }
        }
        $output .= $this->display(__FILE__, 'views/templates/admin/bo-js-vars.tpl');
        $output .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');

        // Set the locales to display the Dates in the BO language
        $this->setBetterLocales();
        // Add the forms and insert the category tree on the right place
        $output .= $this->renderForm();
        // Add JS for behaviour emulation
        $output .= $this->previewJS();
        // Update the cron_secret_key forcelly
        if (Configuration::hasKey('ed_cron_secret_key')) {
            if (Configuration::get('ed_cron_secret_key') != Tools::getAdminTokenLite('AdminModules')) {
                Configuration::updateValue('ed_cron_secret_key', Tools::getAdminTokenLite('AdminModules'));
            }
        } else {
            Configuration::set('ed_cron_secret_key', Tools::getAdminTokenLite('AdminModules'));
        }
        $this->context->smarty->assign(
            [
                'DD_CRON_URL' => $this->context->link->getModuleLink('estimateddelivery', 'DelayedDeliveryWarning') . '?cron_secret_key=' . Tools::getAdminTokenLite('AdminModules'),
                'DD_CRON_PATH' => _PS_ROOT_DIR_ . '/modules/' . $this->name . '/DelayedDeliveryWarning.php?cron_secret_key=' . Tools::getAdminTokenLite('AdminModules'),
                'PHP_CLI_PATH' => PHP_BINARY,
                'isPS17' => $this->is_17 ? true : false,
            ]
        );
        $this->context->smarty->assign(
            [
                'csv_sep' => Configuration::get('ED_EXPORT_SEP'),
                'csv_msep' => Configuration::get('ED_EXPORT_MULTI_SEP'),
                'enable_csv_head' => (int) Configuration::get('ED_EXPORT_HEAD'),
                'enable_ED_DELETE' => (int) Configuration::get('ED_EXPORT_DELETE'),
                'tomorrow_date' => date('Y-m-d H:i:s', strtotime('tomorrow')),
            ]
        );
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            // Additional days for Picking and OOS
            // ED category exclusion
            if (version_compare(_PS_VERSION_, '1.6.0.9', '<=')) {
                $empty = false;
            } else {
                $empty = $this->cat_count > EstimatedDelivery::MAX_CAT_DISPLAY;
            }

            $this->buildAdditionalDays();
            $output .= $this->buildCategoryTree($empty, 'ed_cat_picking', 'categoryPickingDays');
            $output .= $this->buildCategoryTree($empty);
            $output .= $this->buildCategoryTree($empty, 'ed_cat_exclude', 'categoryExcluded', true);
            $output .= $this->buildCategoryTree($empty, 'ed_custom_days', 'CustomDays');
            // Undefined Delivery dates
            $output .= $this->buildUndefinedDeliveriesOptions();
            if (!$this->is_17 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $output .= $this->buildWarehousesAdditionalDays();
            }
        }
        $output .= $this->getEdDeliveryByZoneOptions();
        // TODO Review CSV Import Feature
        $output .= $this->display(__FILE__, 'views/templates/admin/csv-import.tpl');
        $output .= $this->display(__FILE__, 'views/templates/admin/cron-jobs.tpl');

        return SmartForm::openTag('div', 'id="module-body" class="clearfix"') .
            $output .
            SmartForm::closeTag('div');
    }

    private function checkPageBuilders()
    {
        $modules = ['creativeelements', 'appagebuilder'];
        foreach ($modules as $module) {
            if (Module::isEnabled($module)) {
                return true;
            }
        }

        return false;
    }

    private function buildUndefinedDeliveriesOptions()
    {
        $this->context->smarty->assign(
            [
                'ed_undefined_validate_min' => Configuration::get('ed_undefined_validate_min'),
                'ed_undefined_validate_max' => Configuration::get('ed_undefined_validate_max'),
                'ed_undefined_notify' => Configuration::get('ed_undefined_notify'),
                'ed_undefined_notify_email' => Configuration::get('ed_undefined_notify_email'),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/undefined-delivery-dates.tpl');
    }

    private function getEdDeliveryByZoneOptions()
    {
        $carriers = $this->getCarriersList();
        if (empty($carriers)) {
            return '';
        }
        $zonesByCarrier = $this->getZonesByCarrier($carriers);
        $carrier_zones = [];
        $global_carrier = [];

        $delivery_zones = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ed_delivery_zones`');
        $delivery_ranges_by_zone = [];
        if (!empty($delivery_zones)) {
            foreach ($delivery_zones as $delivery_zone) {
                $delivery_ranges_by_zone[$delivery_zone['id_reference']][$delivery_zone['id_zone']] = [$delivery_zone['delivery_min'], $delivery_zone['delivery_max']];
            }
        } else {
            $empty_zones = true;
        }
        foreach ($carriers as $carrier) {
            $id_ref = $carrier['id_reference'];
            $global_carrier[$id_ref] = $carrier;
            $arrZone = [
                [
                    'min' => isset($carrier['min']) ? trim($carrier['min']) : 0,
                    'max' => isset($carrier['max']) ? trim($carrier['max']) : 0,
                ],
            ];
            if (isset($zonesByCarrier[$id_ref])) {
                foreach ($zonesByCarrier[$id_ref] as $zone) {
                    if (!isset($empty_zones) && isset($delivery_ranges_by_zone[$id_ref][$zone])) {
                        $arrZone[$zone]['min'] = $delivery_ranges_by_zone[$id_ref][$zone][0];
                        $arrZone[$zone]['max'] = $delivery_ranges_by_zone[$id_ref][$zone][1];
                    } else {
                        $arrZone[$zone]['min'] = '';
                        $arrZone[$zone]['max'] = '';
                    }
                }
                $carrier_zones[$id_ref] = $arrZone;
            }
        }
        $this->context->smarty->assign(
            [
                'base_dir' => __PS_BASE_URI__,
                'ed_carrier_zone_adv' => Configuration::get('ed_carrier_zone_adv'),
                'zones' => Zone::getZones(true),
                'carriers' => $carriers,
                'global_carrier' => $global_carrier,
                'carrier_zones' => $carrier_zones,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/ed-delivery-zone.tpl');
    }

    /**
     * Returns a list of the zones available for each carrier
     *
     * @param array $carriers A list of carriers
     *
     * @return array $zones A list of each carrier reference and the active zones
     */
    private function getZonesByCarrier($carriers)
    {
        $zones = [];
        $carrier_ref_to_id = [];
        $zonesByCarrier = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'carrier_zone` LEFT JOIN `' . _DB_PREFIX_ . 'carrier` USING (id_carrier) WHERE id_carrier IN (' . implode(',', $this->getIdCarriersFromList($carriers)) . ')');
        foreach ($zonesByCarrier as $data) {
            if (!isset($zones[$data['id_reference']])) {
                $zones[$data['id_reference']] = [];
            }
            $zones[$data['id_reference']][] = $data['id_zone'];
            $carrier_ref_to_id[$data['id_carrier']] = $data['id_reference'];
        }
        $this->context->smarty->assign(['carrier_ref_to_id' => json_encode($carrier_ref_to_id)]);

        return $zones;
    }

    private function getIdCarriersFromList($carriers)
    {
        $ret = [];
        foreach ($carriers as $carrier) {
            $ret[] = (int) trim($carrier['id_carrier']);
        }

        return $ret;
    }

    public function setBoVars()
    {
        $this->context_all = false;
        $this->shops = [];
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            $this->shops = Shop::getShops(true, (int) $this->context->shop->getContextShopGroupID(), true);
            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                $this->context_all = true;
            }
        } else {
            $this->shops[0] = (int) $this->context->shop->id;
        }
        $this->cat_count = (int) Db::getInstance()->getValue('SELECT count(*) FROM ' . _DB_PREFIX_ . 'category');
        $this->context->smarty->assign(
            [
                'cat_count' => $this->cat_count,
                'ed_token' => Tools::getAdminToken('AdminEstimatedDelivery'),
            ]
        );

        // Customization Options
        $module_for_custom_days[] = [
            'id' => 0,
            'name' => 'prestashop',
        ];
        $module_list = Module::getModulesInstalled();
        foreach ($module_list as $module) {
            if ((int) $module['active'] == 1 && $module['name'] == 'customizecart') {
                $module_for_custom_days[] = [
                    'id' => 1,
                    'name' => 'customizecart',
                ];
                break;
            }
        }
        $this->context->smarty->assign(
            [
                'enable_custom_days' => Configuration::get('ed_enable_custom_days'),
                'module_for_custom_days' => $module_for_custom_days,
                'selectedCustomModule' => Configuration::get('ed_custom_module_for_custom_days'),
            ]
        );
        $js_var = [
            'cat_count' => $this->cat_count,
            'ed_ajax_url' => $this->context->link->getAdminLink('AdminEstimatedDelivery', true),
            'selected_menu' => Tools::getValue('selected_menu'),
            'input_limit' => ini_get('max_input_vars'),
            'remoteAddr' => Tools::getRemoteAddr(),
            'remember_to_save' => addslashes($this->l('Setting updated. Remember to save the options to apply the changes')),
        ];
        if (version_compare(_PS_VERSION_, '1.6.0.9', '>')) {
            Media::addJsDef($js_var);
        } else {
            $this->context->smarty->assign(['js_vars' => $js_var]);
        }
    }

    public function checkEmailTemplates($templates)
    {
        if (!is_array($templates)) {
            $templates = [$templates];
        }
        $languages = $this->context->controller->getLanguages(false);
        $source = dirname(__FILE__) . '/mails/en/';
        $ext = ['txt', 'html'];
        $c = 0;
        foreach ($languages as $lang) {
            $folder = dirname(__FILE__) . '/mails/' . $lang['iso_code'] . '/';
            if (file_exists($folder) === false) {
                mkdir($folder, 0755, true);
            }
            foreach ($templates as $template) {
                foreach ($ext as $e) {
                    $file = $folder . $template . '.' . $e;
                    if (file_exists($file) === false) {
                        copy($source . $template . '.' . $e, $file);
                        ++$c;
                    }
                }
            }
        }
        if ($c > 0) {
            $this->context->controller->confirmations[] = sprintf($this->l('There were %d missing Email Templates and have been automatically created'), $c);
        }
    }

    public function checkMissingEmailTranslations($templates)
    {
        if (!is_array($templates)) {
            $templates = [$templates];
        }
        $languages = $this->context->controller->getLanguages(false);
        $source = dirname(__FILE__) . '/mails/en/';
        $ext = ['txt', 'html'];
        $translation_needed = [];
        foreach ($languages as $lang) {
            foreach ($templates as $template) {
                $dest = dirname(__FILE__) . '/mails/' . $lang['iso_code'] . '/';
                foreach ($ext as $e) {
                    $filename = $template . '.' . $e;
                    if (!file_exists($dest . $filename)) {
                        $translation_needed[] = $lang['name'];
                        break;
                    }
                }
            }
        }
        if (count($translation_needed) > 0) {
            $msg = $this->l('Please update the email template translations for the following Languages:');
            $list = [];
            foreach ($translation_needed as $l) {
                $list[] = $l;
            }
            $msg .= SmartForm::genList($list, 'ul');
            $msg .= sprintf($this->l('To update them, go to %s and select Email Templates > Your Theme Name > And the language you want to update'), version_compare(_PS_VERSION_, '1.7', '<') ? $this->l('Localization > Translations') : $this->l('International > Translations'));
            $this->context->controller->warnings[] = $msg;
        }
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $langs = Language::getLanguages(false);
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name . 'Module';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            // 'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        // Free inputs initialization
        $helper->fields_value['free'] = '';
        $helper->fields_value['name'] = '';
        $helper->fields_value['holiday_id_shop'] = '';
        $helper->fields_value['holiday_name'] = '';
        $helper->fields_value['holiday_start'] = '';
        $helper->fields_value['holiday_end'] = '';
        foreach ($this->shops as $id_shop) {
            $helper->fields_value['holiday_id_shop_' . $id_shop] = '';
        }
        // Set Picking Days
        $helper->fields_value['ed_picking_days'] = str_split(Configuration::get('ed_picking_days'));
        // Set Picking Limit
        if (strlen(Configuration::get('ed_picking_limit')) <= 5) {
            $helper->fields_value['ed_picking_limit'] = ['23:59', '23:59', '23:59', '23:59', '23:59', '23:59', '23:59'];
        } else {
            $helper->fields_value['ed_picking_limit'] = json_decode(Configuration::get('ed_picking_limit'), true);
        }
        // Set Carriers
        foreach ($this->getCarriersList(true) as $carrier) {
            if (empty($carrier['shippingdays']) || $carrier['shippingdays'] == '0000000') {
                $carrier['shippingdays'] = '1111100';
            }
            if (empty($carrier['picking_days']) || $carrier['picking_days'] == '0000000') {
                $carrier['picking_days'] = '1111100';
            }
            for ($i = 0; $i < 7; ++$i) {
                $helper->fields_value['shippingdays_' . $carrier['id_carrier'] . '_' . $i] = $carrier['shippingdays'][$i];
            }
            $helper->fields_value['carrier_min_' . $carrier['id_carrier']] = $carrier['min'];
            $helper->fields_value['carrier_max_' . $carrier['id_carrier']] = max($carrier['max'], $carrier['min']);
            $helper->fields_value['ed_active_' . $carrier['id_carrier']] = $carrier['ed_active'];
            $helper->fields_value['ed_ignore_' . $carrier['id_carrier']] = $carrier['ignore_picking'];
            $helper->fields_value['ed_alias_' . $carrier['id_carrier']] = $carrier['ed_alias'];
            $helper->fields_value['picking_days_' . $carrier['id_carrier']] = str_split($carrier['picking_days']);
            $helper->fields_value['picking_limit_' . $carrier['id_carrier']] = !empty($carrier['picking_limit']) ? json_decode($carrier['picking_limit'], true) : array_fill(0, 7, '23:59');
        }
        // Get form values
        foreach ($this->form_fields as $key_group => $fields) {
            foreach ($fields as $field) {
                if (strpos($field['name'], 'holiday') !== false) {
                    continue;
                }
                // Current Config
                $config = Configuration::get($field['name'], null, null, null, $field['def']);
                if ($key_group == 'ints') {
                    $helper->fields_value[$field['name']] = (int) $config;
                } elseif ($key_group == 'texts') {
                    $helper->fields_value[$field['name']] = $config;
                } elseif ($key_group == 'html') {
                    $helper->fields_value[$field['name']] = html_entity_decode($config);
                } elseif ($key_group == 'msgs') {
                    $helper->fields_value[$field['name']] = $this->getConfigInMultipleLangs($field['name']);
                } elseif ($key_group == 'langs') {
                    foreach ($langs as $lang) {
                        $helper->fields_value[$field['name'] . '_' . $lang['id_lang']] = Configuration::get($field['name'] . '_' . $lang['id_lang']);
                    }
                }
            }
        }
        $this->context->smarty->assign(
            [
                'weekdays' => [
                    $this->l('Monday'),
                    $this->l('Tuesday'),
                    $this->l('Wednesday'),
                    $this->l('Thursday'),
                    $this->l('Friday'),
                    $this->l('Saturday'),
                    $this->l('Sunday'),
                ],
            ]
        );

        // Return the Form
        $output = SmartForm::openTag('div', 'id="estimateddelivery_form"') . $helper->generateForm($this->getConfigForm()) . SmartForm::closeTag('div');
        $output .= SmartForm::openTag('div', 'id="estimateddelivery_holidays"') . $this->generateList() . SmartForm::closeTag('div');

        return $output;
    }

    private function getConfigInMultipleLangs($field)
    {
        if (method_exists('Configuration', 'getConfigInMultipleLangs')) {
            return Configuration::getConfigInMultipleLangs($field);
        } else {
            // It's a fallback method call, can't remove it
            return Configuration::getInt($field);
        }
    }

    public function buildCategoryTree($empty = false, $id = 'ed_cat_oos', $inputName = 'categoryBox', $setSectedCat = false, $ajax = false)
    {
        if (!$empty) {
            /* Build Categories Tree */
            if (Shop::isFeatureActive() && count(Shop::getShops(true, null, true)) > 1) {
                $helper = new HelperForm();
                $helper->id = Tools::getValue($id, null);
                $helper->table = 'ed_cat_oos';
                $helper->identifier = $id;
                $this->context->smarty->assign('asso_shops', $helper->renderAssoShop());
                $helper->title = $this->l('Out of stock increase days by Category');
            }

            if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                $main_id = 'categories-treeview' . $id;
                $tree_categories_helper = new HelperTreeCategories($main_id);
                $root_category = Shop::getContext() == Shop::CONTEXT_SHOP ? Category::getRootCategory()->id : 0;
                $tree_categories_helper->setRootCategory($root_category)->setUseCheckBox(true);
            } else {
                if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $root_category = Category::getRootCategory();
                    $root_category = ['id_category' => $root_category->id_category, 'name' => $root_category->name];
                } else {
                    $root_category = ['id_category' => '0', 'name' => $this->l('Root')];
                }
                $tree_categories_helper = new Helper();
            }
            if ($setSectedCat) {
                $selectedCat = $this->getExcludedCat('excluded = 1');
                if ($selectedCat !== false && is_array($selectedCat) && count($selectedCat) > 0) {
                    $tree_categories_helper->setSelectedCategories($selectedCat);
                    $this->context->smarty->assign(['excludedCategories' => implode(',', $selectedCat)]);
                }
            }
            // TODO review the generation method to not include the associated shops
            if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                $tree_categories_helper->setInputName($inputName);
                $this->context->smarty->assign('categories_tree', $tree_categories_helper->render());
            } else {
                $this->context->smarty->assign('categories_tree', $tree_categories_helper->renderCategoryTree($root_category, [], $inputName));
            }
        }
        $this->context->smarty->assign(
            [
                'ed_main_id' => $id,
                'ed_input_name' => $inputName,
                'ed_selected_cat' => $setSectedCat,
                'ajax_replace' => (bool) $empty,
                'ajax_load' => $ajax,
            ]
        );
        switch ($id) {
            case 'ed_cat_oos':
                $this->context->smarty->assign(
                    [
                        'ed_oos' => Tools::getValue('ed_oos', Configuration::get('ed_oos')),
                    ]
                );

                return $this->display(__FILE__, 'views/templates/admin/category-tree-oos.tpl');
            case 'ed_cat_picking':
                return $this->display(__FILE__, 'views/templates/admin/category-tree-picking.tpl');
            case 'ed_cat_exclude':
                $this->context->smarty->assign(['ed_prod_dis' => $this->getDisabledProductList()]);

                return $this->display(__FILE__, 'views/templates/admin/category-tree-exclude.tpl');
            case 'ed_custom_days':
                $this->context->smarty->assign(['ed_custom_days' => Tools::getValue('ed_custom_days', Configuration::get('ed_custom_days'))]);

                return $this->display(__FILE__, 'views/templates/admin/category-tree-custom.tpl');
            default:
                break;
        }
    }

    private function getDisabledProductList()
    {
        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'ed_prod WHERE disabled = 1 ' . Shop::addSqlRestriction();
        $results = Db::getInstance()->executeS($sql);
        if ($results !== false && count($results) > 0) {
            return implode(',', array_column($results, 'id_product'));
        }

        return '';
    }

    private function getExcludedCat($where = '', $id_category = 0)
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->from('ed_cat');
        if ($where != '') {
            $sql->where($where);
        }
        if ($id_category != 0) {
            $sql->where('id_category = ' . (int) $id_category);
        }
        $sql->where('id_shop IN (' . (!empty($this->shops) ? implode($this->shops) : $this->context->shop->id) . ')');

        if ($id_category != 0) {
            return Db::getInstance()->getValue($sql->build());
        } else {
            $results = Db::getInstance()->executeS($sql->build());
        }
        if (count($results) > 0) {
            $ret = [];
            if ($where != '') {
                foreach ($results as $result) {
                    $ret[] = $result['id_category'];
                }
                if (count($ret) > 0) {
                    return $ret;
                }
            } else {
                return $results;
            }
        }
    }

    private function buildWarehousesAdditionalDays()
    {
        $warehouses = Warehouse::getWarehouses();
        $datas = ['supplier', 'manufacturer'];
        $types = [];
        foreach ($warehouses as &$warehouse) {
            foreach ($datas as $data) {
                $sql = 'SELECT ms.id_' . $data . ', sup.name, edw.picking_days FROM ' . _DB_PREFIX_ . bqSQL($data) . ' AS sup ' .
                    'LEFT JOIN ' . _DB_PREFIX_ . bqSQL($data) . '_shop AS ms ON (sup.id_' . $data . ' = ms.id_' . $data . ') ' .
                    'LEFT JOIN ' . _DB_PREFIX_ . 'ed_warehouse edw ON (ms.id_' . $data . ' = edw.id_' . $data . ' AND edw.id_warehouse = ' . $warehouse['id_warehouse'] . ') ' .
                    'WHERE ms.id_shop IN (' . implode(',', $this->shops) . ')' .
                    ' GROUP BY ms.id_' . $data;
                $results = Db::getInstance()->executeS(pSQL($sql));
                if ($results === false) {
                    $this->context->controller->errors[] = $this->l('Error:') . ' ' . Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                } else {
                    if (is_array($results) && count($results) > 0) {
                        foreach ($results as $r) {
                            $warehouse[$data][$r['id_' . $data]] = $r;
                        }
                    }
                }
                if (!empty($results)) {
                    $types[$data] = $data;
                }
            }
        }
        $this->context->smarty->assign(
            [
                'ed_warehouses_mode' => Configuration::get('ED_WAREHOUSES_MODE'),
                'ed_warehouses' => $warehouses,
                'ed_wh_types' => $types,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/ed-warehouses.tpl');
    }

    /**
     * @return void
     */
    private function displayUsefullInformations()
    {
        if (Configuration::get('ED_TEST_MODE') == 1) {
            $this->context->controller->informations[] = $this->l('You have enabled the SandBox mode. This will restrict the module only to the allowed IPs') .
                SmartForm::genDesc('', '', 'br') .
                $this->l('Remember to deactivate it once you finish configuring and testing the module') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::openTag('strong') .
                $this->l('To disable the Test Mode go to:') .
                SmartForm::closeTag('strong') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::openTag('u') .
                sprintf($this->l('%s > Sandbox Mode > Off'), $this->l('Estimated Delivery Basic Settings')) .
                SmartForm::closeTag('u') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc('', '', 'br') .
                sprintf($this->l('Your IP (%s) is currently %s'), $this->getCurrentUserIp()[0], SmartForm::genDesc($this->isTestModeActive() ? $this->l('Allowed') : $this->l('Not Allowed'), 'strong'));
        }
        if ($this->checkPageBuilders() && (!Configuration::get('ED_ALLOW_MULTIPLE_INSTANCES') || Configuration::get('ED_LOCATION') > 0)) {
            if (!empty($this->context->controller->informations)) {
                $this->context->controller->informations[] = SmartForm::openTag('hr');
            }
            $this->context->controller->informations[] = SmartForm::genDesc($this->l('Page Builder Related Issues'), 'strong');
            if (!Configuration::get('ED_ALLOW_MULTIPLE_INSTANCES')) {
                $this->context->controller->informations[] = SmartForm::genDesc($this->l('Module Display possible conflict:'), 'u') . ' ' . sprintf($this->l('You seem to be using a page builder module. If you can\'t see the %s message on your product page you may need to enable an advanced option called "%s".'), $this->name, $this->l('Enable multiple instances on Product Page')) . ' ' . SmartForm::openTag('a', 'href="" class="activate_setting" data-target="#ED_ALLOW_MULTIPLE_INSTANCES"') . $this->l('click here to activate it') . SmartForm::closeTag('a');
                // $('#ED_LOCATION').prop('selectedIndex', 0);
            }
            if (Configuration::get('ED_LOCATION') > 0) {
                $this->context->controller->informations[] = SmartForm::genDesc($this->l('Module Positioning possible conflict:'), 'u') . ' ' . $this->l('You have selected a custom placement for the Estimated Delivery but you\'re using a Page Builder module to generate the content. It\'s highly recommended that you select the default position in the placement options of the section 1 to prevent issues.') . ' ' . SmartForm::openTag('a', 'href="" class="force_select_value" data-target="#ED_LOCATION" data-value="0"') . $this->l('click here to update this option') . SmartForm::closeTag('a');
                // $('#ED_LOCATION').prop('selectedIndex', 0);
            }
        }
        if (Configuration::get('ED_CALENDAR_DATE') && Configuration::get('ED_DATES_BY_PRODUCT')) {
            $this->context->controller->warnings[] = $this->l('The calendar date is not compatible yet with the individual product dates generation. The calendar generation will be ignored');
        }
    }

    /**
     * @return void
     */
    public function processAdditionalDaysUpdates(): void
    {
        $updated_days = [];

        $category_delay_days = $this->saveTreeCategory('oos', $this->shops);
        $category_picking_days = $this->saveTreeCategory('picking', $this->shops);
        $category_custom_days = $this->saveTreeCategory('custom', $this->shops);
        $updated_days['category'] = array_merge($category_delay_days, $category_picking_days, $category_custom_days);

        $this->saveTreeExclude($this->shops);
        $updated_days += $this->saveAdditionalDays();

        //        if (!empty($updated_days)) {
        //            $this->clearCacheFromUpdatedDays($updated_days);
        //        }
    }

    private function clearCacheFromUpdatedDays($updated_days)
    {
        $products = $this->getProductsByAssociations($updated_days);

        $this->clearCache('DeliveryProductAdditionalData', $products);
    }

    public function getProductsByAssociations($additional_days_updated)
    {
        $categoryIds = [];
        $manufacturerIds = [];
        $supplierIds = [];

        // Collect category IDs
        if (isset($additional_days_updated['category'])) {
            foreach ($additional_days_updated['category'] as $categoryType => $categoryUpdates) {
                foreach ($categoryUpdates as $category) {
                    $categoryIds[] = $category;  // Collect all category IDs
                }
            }
        }

        // Collect manufacturer IDs
        if (isset($additional_days_updated['manufacturer'])) {
            foreach ($additional_days_updated['manufacturer'] as $manufacturerUpdate) {
                $manufacturerIds[] = $manufacturerUpdate['id'];  // Collect all manufacturer IDs
            }
        }

        // Collect supplier IDs
        if (isset($additional_days_updated['supplier'])) {
            foreach ($additional_days_updated['supplier'] as $supplierUpdate) {
                $supplierIds[] = $supplierUpdate['id'];  // Collect all supplier IDs
            }
        }

        // Initialize productIds array
        $productIds = [];

        // Fetch products by category in a single query
        if (!empty($categoryIds)) {
            $productIds[] = $this->getProductIdsByEntity('category', $categoryIds);
        }

        // Fetch products by manufacturer in a single query
        if (!empty($manufacturerIds)) {
            $productIds[] = $this->getProductIdsByEntity('manufacturer', $manufacturerIds);
        }

        // Fetch products by supplier in a single query
        if (!empty($supplierIds)) {
            $productIds[] = $this->getProductIdsByEntity('supplier', $supplierIds);
        }

        // Flatten and remove duplicates
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $productIds = array_merge(...$productIds);  // Spread operator for PHP 7.4+
        } else {
            $productIds = call_user_func_array('array_merge', $productIds);  // Fallback for PHP < 7.4
        }

        $productIds = array_unique($productIds);

        return $productIds;
    }

    /**
     * Retrieves product IDs associated with a specific entity.
     *
     * @param string $entity the type of entity (category, manufacturer, or supplier)
     * @param array $entityIds array of entity IDs
     *
     * @return array the product IDs associated with the provided entity IDs
     */
    public function getProductIdsByEntity($entity, $entityIds)
    {
        $productIds = [];

        switch ($entity) {
            case 'category':
                // Get products by category association
                $sql = 'SELECT DISTINCT p.id_product
                    FROM ' . _DB_PREFIX_ . 'category_product cp
                    INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = cp.id_product
                    WHERE cp.id_category IN (' . implode(',', array_map('intval', $entityIds)) . ')';
                break;

            case 'manufacturer':
                // Get products by manufacturer association
                $sql = 'SELECT DISTINCT p.id_product
                    FROM ' . _DB_PREFIX_ . 'product p
                    WHERE p.id_manufacturer IN (' . implode(',', array_map('intval', $entityIds)) . ')';
                break;

            case 'supplier':
                // Get products by supplier association
                $sql = 'SELECT DISTINCT ps.id_product
                    FROM ' . _DB_PREFIX_ . 'product_supplier ps
                    WHERE ps.id_supplier IN (' . implode(',', array_map('intval', $entityIds)) . ')';
                break;

            default:
                return $productIds; // Return empty if entity is unknown
        }

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            foreach ($result as $row) {
                $productIds[] = (int) $row['id_product'];
            }
        }

        return $productIds;
    }

    private function clearCache($cache_type, $productData, $id_langs = [])
    {
        if (empty($productData) || empty($cache_type)) {
            return;
        }

        // Determine shop context
        if (isset($this->shops) && !empty($this->shops)) {
            $shopIds = $this->shops; // Back-office: use available shop IDs
        } else {
            if (method_exists($this, 'setBoVars')) {
                $this->setBoVars(); // Set the shops if in back-office
                $shopIds = $this->shops;
            } else {
                // Front-office: fallback to the current shop context
                $shopIds = [$this->context->shop->id];
            }
        }

        // Loop through product data (['id_product', 'id_product_attribute'])
        foreach ($productData as $data) {
            $id_product = (int) $data['id_product'];
            $id_product_attribute = (int) $data['id_product_attribute'];

            foreach ($shopIds as $id_shop) {
                // If id_langs is provided, loop through each language and clear cache for all languages
                $cache_key_base = $cache_type . '_' . $id_product . '_' . $id_product_attribute . '_' . (int) $id_shop;
                if (!empty($id_langs)) {
                    foreach ($id_langs as $id_lang) {
                        $cache_key = $cache_key_base . '_' . (int) $id_lang;
                        Cache::clean($cache_key);
                    }
                } else {
                    // Clear cache without id_lang
                    Cache::clean($cache_key_base);
                }
            }
        }
    }

    private function getWarehouseAdditionalDays()
    {
        $return = [];
        $sql = 'SELECT edwar.*, sup.name, war.name FROM ' . _DB_PREFIX_ . 'ed_warehouse AS edwar ' .
            'LEFT JOIN ' . _DB_PREFIX_ . 'supplier AS sup USING(id_supplier) ' .
            'LEFT JOIN ' . _DB_PREFIX_ . 'warehouse AS war USING(id_warehouse) ' .
            'WHERE 1 ' . Shop::addSqlRestriction() . ' ' .
            'ORDER BY id_warehouse, id_supplier';
        $results = Db::getInstance()->executeS(pSQL($sql));
        if ($results === false) {
            $this->context->errors[] = $this->l('Error retrieving the Warehouses: ') . Db::getInstance()->getMsgError() .
                SmartForm::genDesc('', '', 'br') . $sql;
        } else {
            if (count($results) > 0) {
                // Order the data in arrays grouped by warehouse id
                foreach ($results as $result) {
                    $return[$result['id_warehouse']][] = $result;
                }
            }
        }

        return $return;
    }

    private function saveWarehousesAdditionalDays()
    {
        if (Tools::getIsset('ed_warehouse')) {
            $warehouses = Warehouse::getWarehouses();
            $types = ['supplier', 'manufacturer'];
            $values = [];
            $data = Tools::getValue('ed_warehouse');
            $shops = Shop::getShops();
            $d = [
                'id_warehouse' => 0,
                'id_supplier' => 0,
                'id_manufacturer' => 0,
                'id_shop' => 0,
                'picking_days' => 0,
            ];
            $insert_data = [];
            foreach ($shops as $shop) {
                $d['id_shop'] = $shop['id_shop'];
                foreach ($warehouses as $warehouse) {
                    $d['id_warehouse'] = $warehouse['id_warehouse'];
                    $d['id_supplier'] = 0;
                    $d['id_manufacturer'] = 0;
                    foreach ($types as $type) {
                        if (isset($data[$warehouse['id_warehouse']][$type])) {
                            $tmp_data = $data[$warehouse['id_warehouse']][$type];
                            if (!empty($tmp_data)) {
                                foreach ($tmp_data as $id => $val) {
                                    $d['id_' . $type] = $id;
                                    $d['picking_days'] = (int) $val;
                                    $insert_data[] = '(' . implode(',', $d) . ')';
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($insert_data)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_warehouse (' . implode(',', array_keys($d)) . ') VALUES ' . implode(',', $insert_data) . ' ON DUPLICATE KEY UPDATE picking_days = VALUES(picking_days)';
                if (Db::getInstance()->execute(pSQL($sql)) === false) {
                    $this->context->controller->errors[] = $this->l('Error while saving the warehouse fields, please try again. If the issue persists contact us to get support') . SmartForm::genDesc('', '', 'br') .
                        $this->l('Error details:') . ' ' . Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') .
                        $this->l('SQL Query:') . ' ' . $sql;
                }
            }
        }
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        // Generate the config form
        require_once dirname(__FILE__) . '/src/config-form.php';

        return $fields_form;
    }

    private function printCarriers($carriers, $mode, $data)
    {
        $switch_options = [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled'),
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled'),
            ],
        ];
        $ret = [];
        /*
         * Get The Carriers List
         *
         */
        if ($mode == 'picking') {
            $ret[] = [
                'type' => 'switch',
                'label' => $this->l('Picking Time advanced mode'),
                'desc' => $this->l('Set custom picking time for each carrier'),
                'hint' => $this->l('Use this if you have multiple picking times.') .
                    SmartForm::genDesc('', '', 'br') .
                    $this->l('Format: 00:00'),
                'name' => 'ed_picking_adv',
                'bool' => true,
                'values' => $switch_options,
            ];
        } elseif ($mode == 'advanced') {
            $ret[] = [
                'type' => 'switch',
                'label' => $this->l('Carriers advanced mode'),
                'desc' => $this->l('Set custom picking time for each carrier'),
                'hint' => $this->l('Use this if you have multiple picking times.') .
                    SmartForm::genDesc('', '', 'br') .
                    $this->l('Format: 00:00'),
                'name' => 'ed_carrier_adv',
                'bool' => true,
                'values' => $switch_options];
        }
        foreach ($carriers as $carrier) {
            if ($mode == 'weekdays') {
                $ret[] = [
                    'type' => 'free',
                    'label' => SmartForm::openTag('img', 'src="' . __PS_BASE_URI__ . 'img/s/' . $carrier['id_carrier'] . '.jpg" class="carrier_img"', true),
                    'desc' => SmartForm::genDesc($carrier['name'], ['h3', 'class="modal-title text-info"']) .
                        $carrier['delay'],
                    'name' => 'name',
                ];
                $ret[] = [
                    'form_group_class' => 'ed_carrier_' . $mode,
                    'type' => 'checkbox',
                    'label' => $carrier['name'] . ' ' . $this->l('Shipping days'),
                    'name' => 'shippingdays_' . $carrier['id_carrier'] . '',
                    'hint' => $this->l('Choose which days the carrier do the shipping'),
                    'values' => [
                        'query' => $data,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ];
                $ret[] = [
                    'type' => 'free',
                    'label' => '',
                    'desc' => SmartForm::genDesc('', '', 'hr') .
                        SmartForm::genDesc('', '', 'br'),
                    'name' => 'name',
                ];
            }
            if ($mode == 'picking') {
                $ret[] = [
                    'type' => 'free',
                    'label' => SmartForm::openTag('img', 'src="' . __PS_BASE_URI__ . 'img/s/' . $carrier['id_carrier'] . '.jpg" class="carrier_img"', true),
                    'desc' => SmartForm::genDesc($carrier['name'], ['h3', 'class="modal-title text-info"']) .
                        $carrier['delay'],
                    'name' => 'name',
                ];
                $ret[] = [
                    'form_group_class' => 'ed_carrier_' . $mode,
                    'type' => 'checklimit',
                    'label' => $carrier['name'] . ' ' . $this->l('Picking days'),
                    'desc' => $this->l('Click on the day to enable the picking for that day') .
                        SmartForm::genDesc('', '', 'br') .
                        $this->l('Set up a picking limit for the enabled days') . '. ' .
                        $this->l('Format 00:00'),
                    'hint' => $this->l('Click on the days you prepare orders and configure the order preparation\'s time limit for that day'),
                    'name' => 'picking_days_' . $carrier['id_carrier'],
                    'name2' => 'picking_limit_' . $carrier['id_carrier'],
                ];
                $ret[] = [
                    'type' => 'free',
                    'label' => '',
                    'desc' => SmartForm::closeTag('p') .
                        SmartForm::openTag('hr', true) .
                        SmartForm::openTag('br', true),
                    'name' => 'name',
                ];
            }
            if ($mode == 'advanced') {
                $ret[] = [
                    'type' => 'free',
                    'label' => SmartForm::openTag('img', 'src="' . __PS_BASE_URI__ . 'img/s/' . $carrier['id_carrier'] . '.jpg" class="carrier_img"', true),
                    'desc' => SmartForm::genDesc($carrier['name'], ['h3', 'class="modal-title text-info"']) .
                        $carrier['delay'] .
                        SmartForm::genDesc('', '', 'hr'),
                    'name' => 'name',
                ];
                $ret[] =
                    [
                        'form_group_class' => 'ed_carrier_' . $mode,
                        'type' => 'switch',
                        'label' => $this->l('Enable') . ' ' . $carrier['name'] . '?',
                        'hint' => $this->l('Enable or disable this carrier for the estimated delivery module'),
                        'name' => 'ed_active_' . $carrier['id_carrier'],
                        'bool' => true,
                        'values' => $data,
                    ];
                $ret[] =
                    [
                        'form_group_class' => 'ed_carrier_' . $mode,
                        'type' => 'text',
                        'label' => $this->l('Carrier alias'),
                        'desc' => $this->l('Set an alias for this carrier (optional), leave blank ig you don\'t want to use this feature'),
                        'hint' => $this->l('Set an alias for this carrier') .
                            SmartForm::genDesc('', '', 'br') .
                            $this->l('Format: 00:00'),
                        'name' => 'ed_alias_' . $carrier['id_carrier'],
                        'class' => 'input fixed-width-lg',
                    ];
                $ret[] =
                    [
                        'form_group_class' => 'ed_carrier_' . $mode,
                        'type' => 'switch',
                        'label' => $this->l('Ignore Picking Days') . '?',
                        'hint' => $this->l('Activate this feature to make the carrier ignore the Picking Days for a product'),
                        'desc' => $this->l('Activate this feature to make the carrier ignore the Picking Days for a product') .
                            SmartForm::genDesc('', '', 'br') .
                            SmartForm::genDesc('', '', 'hr'),
                        'name' => 'ed_ignore_' . $carrier['id_carrier'],
                        'bool' => true,
                        'values' => $data,
                    ];
            }
        }

        return $ret;
    }

    /**
     * Get The Carriers List
     */
    private function printCarriersDelivery($carriers)
    {
        $days = [];
        for ($i = -1; $i < 101; ++$i) {
            if ($i == -1) {
                $days[$i] = [
                    'id' => -1,
                    'name' => '-- ' . $this->l('Select Days') . ' --',
                ];
            } else {
                $days[$i] = [
                    'id' => $i,
                    'name' => $i,
                ];
            }
        }
        $ret = [];
        foreach ($carriers as $carrier) {
            $ret[] = [
                'type' => 'free',
                'label' => SmartForm::openTag('img', 'src="' . __PS_BASE_URI__ . 'img/s/' . $carrier['id_carrier'] . '.jpg" class="carrier_img"', true),
                'desc' => SmartForm::genDesc($carrier['name'], ['h3', 'class="modal-title text-info"']) .
                    $carrier['delay'],
                'name' => 'name',
            ];
            $ret[] = [
                'type' => 'select',
                'label' => $this->l('Minium'),
                'name' => 'carrier_min_' . $carrier['id_carrier'],
                'options' => [
                    'query' => $days,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
            $ret[] = [
                'type' => 'select',
                'label' => $this->l('Maxium'),
                'name' => 'carrier_max_' . $carrier['id_carrier'],
                'desc' => $this->l('Choose the number of days'),
                'options' => [
                    'query' => $days,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
            $ret[] = [
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'br') .
                    SmartForm::genDesc('', '', 'hr'),
                'name' => 'name',
            ];
        }

        return $ret;
    }

    /**
     * Get The Carriers List
     */
    private function getCarriersList($fresh = false)
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $cache_key = 'get_carriers_list_' . $id_lang . '_' . $id_shop;
        if ($fresh) {
            Cache::clean($cache_key);
        }
        if (!Cache::isStored($cache_key)) {
            // TODO check it
            $sql = 'SELECT * FROM (SELECT c.id_carrier, c.id_reference, c.name, cl.delay, c.position, c.is_free, c.range_behavior, c.shipping_method, edc.picking_limit, edc.picking_days, edc.shippingdays, edc.min, edc.max, edc.ignore_picking, edc.ed_active, edc.ed_alias, cl.delay AS carrier_description
                FROM `' . _DB_PREFIX_ . 'carrier` c
                LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'ed_carriers` AS edc ON (c.id_reference = edc.id_reference AND edc.id_shop = ' . $this->context->shop->id . ')
                WHERE cl.id_lang = ' . (int) $id_lang . ' AND active = 1 AND deleted = 0 ' .
                        Shop::addSqlRestriction(false, 'cl') . '
                ORDER BY c.id_reference DESC, c.id_carrier DESC) AS tmp
                GROUP BY id_reference';
            if (($results = Db::getInstance()->executeS($sql)) === false) {
                $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
            } elseif (count($results) == 0) {
                $current_url = $_SERVER['REQUEST_URI'];
                $current_url = strtok($current_url, '?');
                $this->context->controller->errors[] = $this->l('There Are no Carriers Set, please go to') . ' ' . SmartForm::genDesc($this->l('Carriers page'), ['a', 'href="' . $current_url . '?token=' . Tools::getAdminTokenLite('AdminModules') . '&controller=AdminCarriers"']);
            } else {
                Cache::store($cache_key, $results);
            }
        }

        return Cache::retrieve($cache_key);
    }

    /**
     * Set values for the inputs.
     */
    protected function getCarrierFields()
    {
        /* Store the values */
        $a = [];
        $carriers = $this->getCarriersList(true);
        foreach ($carriers as $carrier) {
            $b = [];
            $b['shippingdays'] = '';
            $b['picking_limit'] = [];
            $b['picking_days'] = '';
            for ($i = 0; $i < 7; ++$i) {
                $b['shippingdays'] .= Tools::getValue('shippingdays_' . $carrier['id_carrier'] . '_' . $i) == 'on' ? 1 : 0;
                $b['picking_days'] .= Tools::getValue('picking_days_' . $carrier['id_carrier'] . '_' . $i) == 'on' ? 1 : 0;
                $b['picking_limit'][] = Tools::getValue('picking_limit_' . $carrier['id_carrier'] . '_' . $i, '23:59');
            }
            $b['min'] = Tools::getValue('carrier_min_' . $carrier['id_carrier'], 0);
            $b['max'] = Tools::getValue('carrier_max_' . $carrier['id_carrier'], 0);
            $b['ed_active'] = Tools::getValue('ed_active_' . $carrier['id_carrier'], 1);
            $b['ignore_picking'] = Tools::getValue('ed_ignore_' . $carrier['id_carrier'], 0);
            $b['ed_alias'] = Tools::getValue('ed_alias_' . $carrier['id_carrier'], '');
            $b['id_carrier'] = $carrier['id_carrier'];
            $b['id_reference'] = $carrier['id_reference'];
            $b['name'] = $carrier['name'];

            // Custom shipping days HR
            // TODO Review HR
            // $b['number_of_days'] = Tools::getValue('custom_shipping_days_'.$carrier['id_carrier']);

            // Save in the array
            $a['carriers'][] = $b;
        }

        return $a;
    }

    /**
     * Save form data.
     */
    protected function _postProcess()
    {
        $this->activeRequiredHooksOnSave();
        // Process the data
        $messages = '';
        // New vacation days
        $messages .= $this->processHolidays();
        // Update Carriers picking and Shippings
        $messages .= $this->processCarriers();

        $this->processAdditionalDaysUpdates();

        $this->saveUndefinedDeliveries();
        $this->saveWarehousesAdditionalDays();
        $picking_days = $picking_limit = [];
        for ($i = 0; $i < 7; ++$i) {
            $picking_days[] = Tools::getValue('ed_picking_days_' . $i) == 'on' ? 1 : 0;
            $picking_limit[] = Tools::getValue('ed_picking_limit_' . $i);
        }
        if (!$this->validatePickingLimit($picking_limit)) {
            $this->context->controller->errors[] = $this->l('Time malformatted, some of the picking limits have a wrong format. Remember to use a 24h format without AM or PM (00:00 to 23:59)');
        }
        // Update the form values
        $langs = Language::getLanguages(false);
        foreach ($this->form_fields as $key_group => $values) {
            foreach ($values as $field) {
                if (isset($field['ignore_save'])) {
                    continue;
                }
                if (Tools::getIsset($field['name'])
                    || $this->getstrpos($field['name'], 'picking') !== false
                    || $this->getstrpos($field['name'], 'ED_CALENDAR_DISPLAY') !== false
                    || $this->getstrpos($field['name'], '_msg') !== false) { // Review if more exceptions are needed
                    if ($key_group == 'ints') {
                        Configuration::updateValue($field['name'], (int) Tools::getValue($field['name']));
                    } elseif ($key_group == 'msgs') {
                        $msgs = [];
                        foreach ($langs as $lang) {
                            $msgs[$lang['id_lang']] = Tools::getValue($field['name'] . '_' . $lang['id_lang']);
                        }
                        Configuration::updateValue($field['name'], $msgs);
                    } elseif ($key_group == 'texts') {
                        if ($this->getstrpos($field['name'], 'ED_SPECIAL_ENCODING') !== false) {
                            if (Tools::getValue($field['name']) == '' || EDTools::validEncoding(Tools::getValue($field['name']))) {
                                Configuration::updateValue($field['name'], Tools::getValue($field['name']));
                            } else {
                                $this->context->controller->errors[] = $this->l('Could not save the advanced encoding.') .
                                    sprintf($this->l('"%s" is not a valid Encoding.'), SmartForm::genDesc(Tools::getValue($field['name']), 'strong')) .
                                    SmartForm::openTag('br') .
                                    sprintf($this->l('Advanced encodings are only required in case UTF-8 can\'t display the dates correctly, to check the list of valid encodings %s'), SmartForm::genDesc($this->l('here'), ['a', 'href="https://www.php.net/manual/en/mbstring.supported-encodings.php" target="_blank"']));
                            }
                        } elseif ($this->getstrpos($field['name'], 'picking') === false) {
                            Configuration::updateValue($field['name'], Tools::getValue($field['name']));
                        } else {
                            // echo 'Inside the Else!'.'<br>';
                            // echo 'Group? '.$key_group.'<br>';
                            // echo $field['name'].' >> '.Tools::getValue($field['name']).'<br>';
                            // Configuration::updateValue($field['name'], json_encode($picking_limit));
                        }
                    } elseif ($key_group == 'weekdays') {
                        if ($this->getstrpos($field['name'], 'days') !== false) {
                            Configuration::updateValue($field['name'], implode('', $picking_days));
                        }
                    } elseif ($key_group == 'json') {
                        Configuration::updateValue($field['name'], json_encode($picking_limit));
                    } elseif ($key_group == 'html') {
                        Configuration::updateValue($field['name'], Tools::getValue($field['name']), true);
                    }
                }
            }
        }
        // Do it if csv import form is submitted
        if (Tools::isSubmit('EDSubmitImport')) {
            // require_once $this->local_path.'classes/CSVImporter.php';
            $vi = new CSVImporter($this);
            if ($vi !== false) {
                $vi->importEDs();
            }
        }
        // Do update the carrier delivery zone
        if (Tools::isSubmit('submitCarrierDeliveryZone')) {
            $carrier_zone = Tools::getValue('carrier_zone');
            if ($carrier_zone) {
                foreach ($carrier_zone as $id_ref => $item) {
                    foreach ($item as $id_zone => $ed) {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_delivery_zones (id_reference, id_zone, delivery_min, delivery_max) 
                        VALUES(' . (int) $id_ref . ', ' . (int) $id_zone . ', "' . $ed['min'] . '", "' . max($ed['max'], $ed['min']) . '")
                        ON DUPLICATE KEY UPDATE delivery_min = VALUES(delivery_min), delivery_max = VALUES(delivery_max)';
                        if (Db::getInstance()->execute($sql) === false) {
                            $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                        }
                    }
                }
            }
        }
        if ($messages == '') {
            // No errors, redirect to module configuration page to avoid mistakes
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $redUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . Configuration::get('PS_SHOP_DOMAIN') . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/index.php?controller=AdminModules&configure=' . $this->name . '&successUpdate=1&token=' . Tools::getAdminTokenLite('AdminModules');
                Tools::redirect($redUrl);
            }
        } else {
            return $messages;
        }
        /*
        if (empty($this->context->controller->errors)) {
            $this->context->controller->success[] = $this->l('Settings Updated without errors');
        } else {
            $this->context->controller->success[] = $this->l('Settings Updated but there are some errors that should be reviewed');
        }
        */
        $this->context->controller->confirmations[] = $this->l('Settings successfully updated');
    }

    private function activeRequiredHooksOnSave()
    {
        $hooks = [];
        $position = Configuration::get('ED_LOCATION');
        if ($this->is_17) {
            if ($position > 0) {
                $hooks[] = 'displayProductAdditionalInfo';
            } elseif ($position == -1) {
                $hooks[] = 'displayProductTab';
                $hooks[] = 'displayProductTabContent';
            }
        } else {
            switch ($position) {
                case 1:
                    $hooks[] = 'displayRightColumnProduct';
                    break;
                case 2:
                    $hooks[] = 'displayLeftColumnProduct';
                    break;
                case 3:
                    $hooks[] = 'displayProductFooter';
                    break;
                case 4:
                    $hooks[] = 'displayProductTab';
                    $hooks[] = 'displayProductTabContent';
                    break;
            }
            if ($position == -5) {
                $hooks[] = 'displayProductDeliveryTime';
            }
        }
        foreach ($hooks as $hook) {
            if (!$this->isRegisteredInHook($hook)) {
                $this->registerHook($hook);
            }
        }
    }

    private function processHolidays()
    {
        $msg = '';
        $holiday_start = Tools::getValue('holiday_start');
        if ($holiday_start != '') {
            $holiday_name = Tools::getValue('holiday_name');
            if ($holiday_name != '') {
                $holiday_end = Tools::getValue('holiday_end');
                $holiday_repeatable = (int) Tools::getValue('ED_HOLIDAY_REPEATABLE');
                if ($holiday_end == '') {
                    $holiday_end = $holiday_start;
                }
                foreach ($this->shops as $id_shop) {
                    $this->holidayUpdate($holiday_name, $holiday_start, $holiday_end, $holiday_repeatable);
                }
            } else {
                $msg .= $this->displayError($this->l('You must enter a name to save a holiday range') . ' ' . SmartForm::genDesc($this->l('Jump to') . ' ' . $this->l('Holidays') . ' ' . $this->l('Section') . ' &raquo;', ['a', 'class="target-menu" href="#no_delivery_days"']));
            }
        }

        return $msg;
    }

    private function processCarriers()
    {
        $msg = '';
        $a = $this->getCarrierFields();
        // Create the ON DUPLICATE UPDATE String
        $dup_up = '';
        $columns = array_keys($a['carriers'][0]);
        foreach ($columns as $column) {
            if ($column != 'id_carrier' && $column != 'name') {
                $dup_up .= $column . ' = VALUES(' . $column . '), ';
            }
        }
        $dup_up = rtrim($dup_up, ', ');

        // Check the carriers parameters, if there is any error print it.
        foreach ($a['carriers'] as &$carrier) {
            if ($carrier['shippingdays'] == '0000000') {
                $msg .= $this->displayError($this->l('Please configure Shipping days for carrier') . ' ' . $carrier['name'] . ' ' .
                    SmartForm::genDesc($this->l('Jump to') . ' ' . $this->l('Shipping Days') . ' ' . $this->l('Section') . ' &raquo;', ['a', 'href="#shipping" class="target-menu"']));
            }
            if ($carrier['min'] == -1 || $carrier['max'] == -1) {
                if ($carrier['min'] == -1 && $carrier['max'] == -1) {
                    $msg .= $this->displayError($this->l('Please Set the Minium and Maxium delivery time for carrier') . ' ' . $carrier['name'] . ' ' .
                        SmartForm::genDesc($this->l('Jump to') . ' ' . $this->l('Delivery Interval') . ' ' . $this->l('Section') . ' &raquo;', ['a', 'class="target-menu" href="#fieldset_3"']));
                } else {
                    $msg .= $this->displayError($this->l('In order to work, please Set the ') . ($carrier['min'] == -1 ? $this->l('Minium') : $this->l('Maxium')) . ' ' . $this->l('delivery time for carrier') . ' ' . $carrier['name'] . ' ' .
                        SmartForm::genDesc($this->l('Jump to') . ' ' . $this->l('Delivery Interval') . ' ' . $this->l('Section') . ' &raquo;', ['a', 'href="#carrier_delivery" class="target-menu"']));
                }
            }

            if ($carrier['max'] < $carrier['min']) {
                $carrier['max'] = $carrier['min'];
                $this->context->controller->warnings[] = sprintf($this->l('Carrier %s maximum delivery has to be at east equal to the minimum delivery. Value has been updated automatically.'), $carrier['name']);
            }
            // Validate the picking limit
            if (isset($carrier['picking_limit'])) {
                if (!$this->validatePickingLimit($carrier['picking_limit'])) {
                    $this->context->controller->errors[] = $this->l('Time malformatted, some of the picking limits have a wrong format. Remember to use a 24h format without AM or PM (00:00 to 23:59)') . ' ' . $carrier['name'] . ' ' .
                        SmartForm::genDesc($this->l('Jump to') . ' ' . $this->l('Advanced') . ' ' . $this->l('Picking Days') . ' ' . $this->l('Section') . ' &raquo;', ['a', 'class="target-menu" href="#picking_advanced"']);
                }
                $carrier['picking_limit'] = pSQL(json_encode($carrier['picking_limit']));
            }
            unset($carrier['name'], $carrier['id_carrier']);
            foreach ($this->shops as $id_shop) {
                // Save the carriers to all Shops in context
                $carrier['id_shop'] = $id_shop;
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_carriers (' . implode(',', array_keys($carrier)) . ') VALUES ("' . implode('", "', array_values($carrier)) . '") ON DUPLICATE KEY UPDATE ' . $dup_up;
                if (Db::getInstance()->execute($sql) === false) {
                    $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                }
            }
        }

        return $msg;
    }

    private function validatePickingLimit(&$limits)
    {
        $error = false;
        foreach ($limits as &$limit) {
            // Handles this time formats 23:50, 23.59, 2359, 23 and converts them
            if (Tools::strpos($limit, ':') !== false) {
                $limit = explode(':', $limit);
            } elseif (Tools::strpos($limit, '.') !== false) {
                $limit = explode('.', $limit);
            } elseif (Tools::strlen($limit) == 4) {
                $limit = str_split($limit, 2);
            } elseif (Tools::strlen($limit) == 1 || Tools::strlen($limit) == 2 && $limit >= 0 && $limit < 24) {
                $limit = [$limit, '00'];
            } else {
                $error = true;
                $limit = ['23', '59'];
            }
            $limit = [(int) $limit[0], (int) $limit[1]];
            if (!isset($limit[1]) || $limit[0] < 0 || $limit[0] > 23 || $limit[1] < 0 || $limit[1] > 59) {
                $error = true;
                $limit = '23:59';
            } else {
                $limit = $limit[0] . ':' . ($limit[1] < 10 ? '0' . $limit[1] : $limit[1]);
            }
        }

        return !$error;
    }

    public function saveTreeCategory($type, $id_shop)
    {
        $a = [
            'oos' => [
                'name' => 'categoryBox',
                'column' => 'delay',
                'var' => 'ed_cat_oos_days',
            ],
            'picking' => [
                'name' => 'categoryPickingDays',
                'column' => 'picking_days',
                'var' => 'ed_cat_picking_days',
            ],
            'custom' => [
                'name' => 'CustomDays',
                'column' => 'customization_days',
                'var' => 'ed_custom_days_days',
            ],
        ];

        $updatedCategories = [];

        if (Tools::getIsset($a[$type]['name'])) {
            $categories = Tools::getValue($a[$type]['name']);
            $var = (int) Tools::getValue($a[$type]['var']);

            if (!empty($categories)) {
                // Fetch all existing values for the categories in one query
                $category_ids = implode(',', array_map('intval', $categories));
                $sql = 'SELECT id_category, id_shop, ' . $a[$type]['column'] . ' FROM ' . _DB_PREFIX_ . 'ed_cat WHERE id_category IN (' . $category_ids . ') AND id_shop = ' . (int) $id_shop;
                $existingValues = Db::getInstance()->executeS($sql);

                // Index the existing values by category ID for easy comparison
                $existingValuesMap = [];
                foreach ($existingValues as $row) {
                    $existingValuesMap[$row['id_category']] = (int) $row[$a[$type]['column']];
                }

                $values = [];
                foreach ($categories as $category) {
                    $currentValue = isset($existingValuesMap[$category]) ? $existingValuesMap[$category] : null;

                    // Only process if the value has changed or it's a new category
                    if ($currentValue === null || $currentValue !== $var) {
                        $updatedCategories['category'][] = $category['id_category'] ?? $category;

                        if (is_array($id_shop) && !empty($id_shop)) {
                            foreach ($id_shop as $shop) {
                                $values[] = '(' . (int) $category . ',' . (int) $shop . ',' . (int) $var . ')';
                            }
                        } else {
                            $values[] = '(' . (int) $category . ',' . (int) $id_shop . ',' . (int) $var . ')';
                        }
                    }
                }

                // Perform the insert/update query only if there are changes
                if (!empty($values)) {
                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_cat (id_category,id_shop,' . $a[$type]['column'] . ') VALUES ' . implode(',', $values) . ' ON DUPLICATE KEY UPDATE ' . $a[$type]['column'] . ' = VALUES(' . $a[$type]['column'] . ')';
                    if (!DB::getInstance()->execute(pSQL($sql))) {
                        $this->context->controller->errors[] = Tools::displayError('Error: ') . Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                    }
                }
            }
        }

        return array_unique($updatedCategories); // Return the list of updated categories
    }

    /**
     * Save the Additional Days days by manufacturer or supplier
     * Returns a list of IDs of the updated elements
     */
    private function saveAdditionalDays()
    {
        $types = ['manufacturer', 'supplier'];
        $methods = ['picking' => 'picking_days', 'oos' => 'delay', 'custom' => 'customization_days'];
        $updatedElements = [];

        foreach ($types as $type) {
            foreach ($methods as $method => $column) {
                // Fetch all existing values for this type and method in one query
                $existingValues = Db::getInstance()->executeS(
                    'SELECT id_' . $type . ', id_shop, ' . $column . ' FROM ' . _DB_PREFIX_ . 'ed_' . $type
                );

                // Create a map of existing values by (id_type, id_shop)
                $existingValuesMap = [];
                foreach ($existingValues as $row) {
                    $existingValuesMap[$row['id_' . $type] . '_' . $row['id_shop']] = (int) $row[$column];
                }

                $sqlValues = [];
                $fields = Tools::getValue($method . Tools::ucfirst($type));

                if ($fields !== false) {
                    foreach ($this->shops as $shop) {
                        foreach ($fields as $id => $value) {
                            $key = $id . '_' . $shop;
                            $currentValue = isset($existingValuesMap[$key]) ? $existingValuesMap[$key] : null;

                            // Only update if the value has changed or is new
                            if ($currentValue === null || $currentValue !== (int) $value) {
                                $updatedElements[$type][] = $id;

                                $sqlValues[] = '(' . (int) $id . ', ' . (int) $shop . ', ' . (int) $value . ')';
                            }
                        }
                    }

                    // Only execute the query if there are values to update
                    if (!empty($sqlValues)) {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_' . $type . ' (id_' . $type . ', id_shop, ' . $column . ') VALUES '
                            . implode(',', $sqlValues)
                            . ' ON DUPLICATE KEY UPDATE ' . $column . ' = VALUES(' . $column . ')';

                        if (DB::getInstance()->execute($sql) === false) {
                            $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                        }
                    }
                }
            }
            if (!empty($updatedElements[$type])) {
                $updatedElements[$type] = array_unique($updatedElements[$type]);
            }
        }

        return $updatedElements; // Return the list of updated elements
    }

    public function saveTreeExclude($id_shop, $name = 'categoryExcluded')
    {
        if (Tools::getIsset('ed_prod_dis')) {
            $this->saveDisabledProducts();
        }
        if (Tools::getIsset($name) === false) {
            Db::getInstance()->update('ed_cat', ['excluded' => 0]);
        } else {
            $excluded = array_flip(Tools::getValue($name));
            if (is_array($excluded) && count($excluded) > 0) {
                $tmp = $this->getExcludedCat();
                // Prepare the Variables
                $cat = [];
                // Set the current fields
                foreach ($excluded as $key => $item) {
                    $cat[$key] = 1;
                }
                // Update the categories to update the unexcluded
                if (isset($tmp) && count($tmp) > 0) {
                    foreach ($tmp as $c) {
                        $cat[$c['id_category']] = isset($excluded[$c['id_category']]) ? 1 : 0;
                    }
                }
                $values = [];
                // Create the SQL query to update the categories
                foreach ($cat as $key => $value) {
                    foreach ($id_shop as $shop) {
                        $values[] = '(' . (int) $key . ',' . (int) $shop . ',' . (int) $value . ')';
                    }
                }
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_cat (id_category,id_shop,excluded) VALUES ' . implode(',', $values) . ' ON DUPLICATE KEY UPDATE excluded = VALUES(excluded)';
                if (DB::getInstance()->execute(pSQL($sql)) === false) {
                    $this->context->controller->errors[] = 'Error: ' . DB::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                }
            }
        }
    }

    private function saveDisabledProducts()
    {
        $old_ids = $this->getDisabledProductList();
        $ids = trim(str_replace(', ', ',', Tools::getValue('ed_prod_dis')));
        if ($old_ids === $ids) {
            // The list has the same values, don't do anything
            return;
        } elseif (empty(trim($ids))) {
            // List is empty
            // Enable all products
            Db::getInstance()->update('ed_prod', ['disabled' => 0]);
        } else {
            // Something has changed
            $wrong_ids = [];
            $ids = explode(',', $ids);
            $c = count($ids);
            for ($i = 0; $i < $c; ++$i) {
                $ids[$i] = trim($ids[$i]);
                if (is_numeric($ids[$i])) {
                    $product = new Product((int) $ids[$i], false, $this->context->language->id);
                }
                if (!is_numeric($ids[$i]) || !$product->id) {
                    $wrong_ids[] = $ids[$i];
                    unset($ids[$i]);
                }
            }
            if (!empty($wrong_ids)) {
                $this->context->controller->warnings[] = sprintf($this->l('Invalid Product Ids detected in the exclude products by ID list: "%s"'), implode('", "', $wrong_ids));
            }
            if (!empty(array_filter($ids))) {
                // Check if the product IDS are already registered in the products DB from the ED
                $existing_ids = array_column(Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'ed_prod WHERE id_product IN (' . implode(',', $ids) . ')'), 'id_product');
                $new_ids = array_diff($ids, $existing_ids);
                if (!empty($new_ids)) {
                    // Some products are missing in the ed_prod database
                    $items = [];
                    foreach ($new_ids as $id) {
                        foreach ($this->shops as $shop) {
                            $items[] = '(' . (int) $id . ',' . (int) $shop . ')';
                        }
                    }
                    Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'ed_prod (id_product, id_shop) VALUES ' . implode(',', $items));
                }
                // Update the products
                Db::getInstance()->update('ed_prod', ['disabled' => 1], 'id_product IN (' . implode(',', $ids) . ')');
            }
        }
    }

    /**** START ADDITIONAL DAYS FORM STRUCTURE ****/
    /**
     * Build additional days create the form structures to add the additional days by category, manufacturer or supplier
     */
    public function buildAdditionalDays()
    {
        // TODO review with new table names
        $types = ['manufacturer', 'supplier'];
        $datas = [
            [
                'method' => 'picking',
                'column' => 'picking_days',
                'input_type' => 'text',
            ],
            [
                'method' => 'oos',
                'column' => 'delay',
                'input_type' => 'text',
            ],
            [
                'method' => 'custom',
                'column' => 'customization_days',
                'input_type' => 'text',
            ],
            [
                'method' => 'undefined_delivery',
                'column' => 'undefined_delivery',
                'input_type' => 'checkbox',
            ],
        ];

        foreach ($types as $type) {
            $id = $type;
            $sql = 'SELECT id_' . $id . ', sup.name, picking_days, delay, customization_days, undefined_delivery FROM ' . _DB_PREFIX_ . bqSQL($id) . '_shop AS ms LEFT JOIN ' . _DB_PREFIX_ . bqSQL($id) . ' AS sup USING (id_' . $id . ') LEFT JOIN ' . _DB_PREFIX_ . 'ed_' . bqSQL($type) . ' USING (id_' . $id . ', id_shop) WHERE ms.id_shop IN (' . implode(',', $this->shops) . ') GROUP BY id_' . $id;
            $results = Db::getInstance()->executeS(pSQL($sql));
            if ($results === false) {
                $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
            } else {
                foreach ($datas as $data) {
                    $this->assignAdditionalDaysPanel($results, $type, $data['method'], $data['column'], $data['input_type']);
                }
            }
        }

        $categories = DB::getInstance()->executeS(pSQL('SELECT id_category, delay, picking_days, customization_days FROM ' . _DB_PREFIX_ . 'ed_cat WHERE 1 ' . Shop::addSqlRestriction())); // TODO make settings multishop '.Shop::addSqlRestriction()

        Media::addJsDef(['cat_delay' => $categories]);

        $this->context->smarty->assign(
            [
                'ed_add_picking_mode' => Configuration::get('ED_ADD_PICKING_MODE'),
                'ed_add_oos_days_mode' => Configuration::get('ED_ADD_OOS_DAYS_MODE'),
                'ed_add_custom_days_mode' => Configuration::get('ED_ADD_CUSTOM_DAYS_MODE'),
                'ed_undefined_days_mode' => Configuration::get('ED_UNDEFINED_DAYS_MODE'),
            ]
        );
    }

    /**
     * Generate the HTML for form to save the additional days by category, manufacturer or supplier
     */
    private function assignAdditionalDaysPanel($results, $type, $method, $column, $input_type = 'text')
    {
        $this->context->smarty->assign([
            'ed_results' => $results,
            'ed_type' => $type,
            'ed_method' => $method,
            'ed_column' => $column,
            'ed_input_type' => $input_type,
        ]);

        $this->context->smarty->assign(
            [
                $type . '_' . $method => $this->display(__FILE__, 'views/templates/admin/additional-days-panel.tpl'),
            ]
        );
    }

    private function saveUndefinedDeliveries()
    {
        if (!Tools::getIsset('undefined_deliveries_submit')) {
            return;
        }
        $types = ['supplier', 'manufacturer'];
        foreach ($types as $type) {
            $values = Tools::getValue('undefined_delivery_' . $type);
            $insert = [];
            $shops = Shop::getShops(true);
            foreach ($shops as $shop) {
                // Empty previous data
                Db::getInstance()->update('ed_' . $type, ['undefined_delivery' => 0], 'id_shop = ' . (int) $shop['id_shop']);
                if (!empty($values)) {
                    foreach ($values as $id_supplier => $value) {
                        $insert[] = '(' . (int) $id_supplier . ',' . 1 . ',' . $shop['id_shop'] . ')';
                    }
                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_' . $type . ' (id_' . $type . ', undefined_delivery, id_shop) VALUES ' . implode(',', $insert) . ' ON DUPLICATE KEY UPDATE undefined_delivery = VALUES(undefined_delivery)';
                    if (Db::getInstance()->getInstance()->execute(pSQL($sql)) === false) {
                        $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::openTag('br') . $sql;
                    }
                }
            }
        }
    }

    public function saveOOSSupplier()
    {
        if (Tools::getIsset('oosSupplier')) {
            $suppliers = Tools::getValue('oosSupplier');
            if (is_array($suppliers)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_supplier (id_supplier, delay, id_shop) VALUES ';
                foreach ($suppliers as $id => $value) {
                    foreach ($this->shops as $shop) {
                        $sql .= '(' . (int) $id . ', ' . (int) $value . ', ' . (int) $shop . '),';
                    }
                }
                $sql = rtrim($sql, ',') . ' ON DUPLICATE KEY UPDATE delay = VALUES(delay)';
                if (DB::getInstance()->execute($sql) === false) {
                    $this->context->controller->errors[] = Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;
                }
            }
        }
    }

    public function updateHolidayState($field)
    {
        $allowed_fields = ['repeat' => 'repeat'];
        if (in_array($field, $allowed_fields)) {
            $id = (int) Tools::getValue('id_holidays');
            $sql = 'SELECT `' . bqSQL($field) . '` FROM ' . _DB_PREFIX_ . 'ed_holidays WHERE id_holidays = ' . $id;
            if (($results = Db::getInstance()->getValue(pSQL($sql))) !== false) {
                $results = Db::getInstance()->update(
                    'ed_holidays',
                    [$field => !$results],
                    'id_holidays = ' . (int) $id
                );
                if ($results !== false) {
                    return $this->displayConfirmation($this->l('Holidays Repeatable Status Updated'));
                } else {
                    $this->context->controller->errors[] = $this->l('Couldn\'t update the Holiday Status, please try again');
                }
            } else {
                $this->context->controller->errors[] =
                    $this->l('SQL Error') . ': ' .
                    DB::getInstance()->getMsgError() .
                    SmartForm::openTag('br') .
                    $sql;
            }
        }
    }

    private function updateHolidayShopState($id_shop)
    {
        // Ensure `id_holidays` parameter is provided
        $id_holidays = (int) Tools::getValue('id_holidays');
        if (!$id_holidays) {
            $this->context->controller->errors[] = $this->l('Holiday ID is missing.');
        }

        // Fetch the current status
        $current_status = Db::getInstance()->getValue('
        SELECT `active` FROM `' . _DB_PREFIX_ . 'ed_holidays_shop`
        WHERE `id_holidays` = ' . $id_holidays . ' AND `id_shop` = ' . (int) $id_shop
        );

        if ($current_status === false) {
            $this->context->controller->errors[] = $this->l('Failed to retrieve the current status.');
        }

        // Toggle the status
        $new_status = (int) !$current_status;
        $update_success = Db::getInstance()->update(
            'ed_holidays_shop',
            ['active' => $new_status],
            '`id_holidays` = ' . (int) $id_holidays . ' AND `id_shop` = ' . (int) $id_shop
        );

        if (!$update_success) {
            $this->context->controller->errors[] = $this->l('Failed to update the holiday status for the shop.');
        }

        // Add a confirmation message with the new status
        $status_text = $new_status ? $this->l('activated') : $this->l('deactivated');
        $shop = new Shop((int) $id_shop, $this->context->language->id);
        $this->context->controller->confirmations[] =
            sprintf($this->l('The holiday has been %s for shop: %s (%s)'), $status_text, $shop->name, $id_holidays);
    }

    /**
     * Generates the Holiday Lists
     *
     * @return the Helper List
     **/
    private function generateList()
    {
        $this->fields_list = [];
        $this->fields_list['id_holidays'] = [
            'title' => $this->l('ID'),
            'hint' => $this->l('Unique identifier of the holiday'),
            'type' => 'int',
            'search' => false,
            'orderby' => true,
        ];
        $this->fields_list['holiday_name'] = [
            'title' => $this->l('Name'),
            'hint' => $this->l('Name reference for the holiday'),
            'type' => 'text',
            'search' => false,
            'orderby' => true,
        ];
        //        $this->fields_list['active'] = [
        //            'title' => $this->l('Active'),
        //            'type' => 'bool',
        //            'search' => false,
        //            'orderby' => false,
        //            'align' => 'text-center',
        //            'active' => 'status',
        //        ];
        $this->fields_list['holiday_start'] = [
            'title' => $this->l('Holiday Start'),
            'hint' => $this->l('When the holiday starts, date included'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        ];
        $this->fields_list['holiday_end'] = [
            'title' => $this->l('Holiday End'),
            'hint' => $this->l('When the holiday ends, date included'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        ];
        $this->fields_list['repeat'] = [
            'title' => $this->l('Repeat'),
            'hint' => $this->l('Will this holiday repeat on the same date every year?'),
            'type' => 'bool',
            'search' => false,
            'align' => 'text-center',
            'orderby' => false,
            'active' => 'repeat',
        ];

        // Add a status column for each shop
        $shops = Shop::getShops();
        $shops_count = count($shops);
        foreach ($shops as $shop) {
            $this->fields_list['status_' . $shop['id_shop']] = [
                'title' => $shops_count > 1 ? $shop['name'] : $this->l('Status'),
                'hint' => $shops_count > 1 ? sprintf($this->l('Set if this holiday will be active for the shop "%s"'), $shop['name']) : $this->l('Set if this holiday will be active'),
                'type' => 'bool',
                'search' => false,
                'align' => 'text-center',
                'active' => 'toggle_shop_' . $shop['id_shop'] . '_status',
                'ajax_toggle' => true,
            ];
        }

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_holidays';
        $helper->actions = ['delete'];
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->title = $this->l('Holidays List');
        $helper->table = $this->name;
        $helper->list_id = 'holidays_list';
        $helper->class = 'class_test';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $content = $this->getListContent($this->context->language->id);

        return $helper->generateList($content, $this->fields_list);
    }

    /**
     * Holiday List Content
     *
     * @param $id
     *
     * @return the content for the holiday list
     */
    private function getListContent($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT h.*, hs.id_shop, hs.active as status
            FROM `' . _DB_PREFIX_ . 'ed_holidays` h
            LEFT JOIN `' . _DB_PREFIX_ . 'ed_holidays_shop` hs ON h.id_holidays = hs.id_holidays
            WHERE hs.id_shop IN (' . implode(',', array_column(Shop::getShops(), 'id_shop')) . ')';

        $content = Db::getInstance()->executeS($sql);

        // Restructure content to include a status field for each shop
        $holidayList = [];
        foreach ($content as $holiday) {
            $id_holidays = $holiday['id_holidays'];

            if (!isset($holidayList[$id_holidays])) {
                $holidayList[$id_holidays] = [
                    'id_holidays' => $holiday['id_holidays'],
                    'holiday_name' => $holiday['holiday_name'],
                    'holiday_start' => $holiday['holiday_start'],
                    'holiday_end' => $holiday['holiday_end'],
                    'repeat' => $holiday['repeat'],
                ];
            }

            // Assign the shop-specific status
            $holidayList[$id_holidays]['status_' . $holiday['id_shop']] = (bool) $holiday['status'];
        }

        return array_values($holidayList);  // Return structured list for HelperList display
    }

    /**
     * holiday Update Function, create or delete holiday range vacations
     */
    private function holidayUpdate($name, $start, $end, $is_repeatable, $id = 0)
    {
        $data = [
            'holiday_name' => pSQL($name),
            'holiday_start' => $start,
            'holiday_end' => $end,
            'repeat' => (int) $is_repeatable,
        ];
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_holidays (`holiday_name`, `holiday_start`, `holiday_end`, `repeat`) VALUES ("' . implode('","', $data) . '") ON DUPLICATE KEY UPDATE `repeat` = VALUES(`repeat`)';
        if (Db::getInstance()->execute($sql) !== false) {
            $id_holidays = Db::getInstance()->Insert_ID();
            if (empty($id_holidays)) {
                $id_holidays = Db::getInstance()->getValue('SELECT MAX(id_holidays) FROM ' . _DB_PREFIX_ . 'ed_holidays');
            }
            foreach ($this->shops as $id_shop) {
                if (Tools::getIsset('holiday_id_shop_' . $id_shop)) {
                    Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'ed_holidays_shop (`id_holidays`, `id_shop`, `active`) VALUES (' . $id_holidays . ', ' . $id_shop . ', 1) ON DUPLICATE KEY UPDATE `active` = VALUES(`active`);');
                }
            }
        } else {
            $this->context->controller->errors[] =
                Db::getInstance()->getMsgError() .
                SmartForm::openTag('br') .
                $sql;
        }
    }

    private function ipInfo($ip = null, $purpose = 'location', $deep_detect = true, $iptype = 0, $option = 0)
    {
        $stream_context = @stream_context_create(
            [
                'http' => ['timeout' => 0.08],
            ]
        );
        // Use 3 IP detection systems and choose the best
        $output = null;
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $this->getCurrentUserIP()[0];
        }
        $purpose = str_replace(['name', '\n', '\t', ' ', '-', '_'], '', Tools::strtolower(trim($purpose)));
        if ($option == 0) {
            $ipdat = @json_decode(Tools::file_get_contents('http://ip-api.com/json/' . $ip, false, $stream_context));
            if (is_object($ipdat) && (isset($ipdat->country) || isset($ipdat->countryCode))) {
                $output = [
                    'city' => @$ipdat->city,
                    'country' => @$ipdat->country,
                    'country_code' => @$ipdat->countryCode,
                ];
                if (isset($ipdat->regionName)) {
                    $output['state'] = $ipdat->regionName;
                }
                if ($iptype == 1) {
                    if (isset($output['country_code']) && $output['country_code'] != '') {
                        return $output;
                    }
                } elseif ($iptype == 2) {
                    if (isset($output['state']) && $output['state'] != '') {
                        return $output;
                    }
                }
            }
        }
        if ($option == 1) {
            // 3rd option
            $ipdat = @json_decode(Tools::file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip, false, $stream_context), true);
            if ($ipdat != null && isset($ipdat->geoplugin_countryCode)) {
                $output = [
                    'city' => @$ipdat->geoplugin_city,
                    'state' => @$ipdat->geoplugin_regionName,
                    'country' => @$ipdat->geoplugin_countryName,
                    'country_code' => @$ipdat->geoplugin_countryCode,
                ];
            }
            if ($iptype == 1) {
                if (isset($output['country_code']) && $output['country_code'] != '') {
                    return $output;
                }
            } elseif ($iptype == 2) {
                if ((isset($output['city']) && $output['city'] != '') || (isset($output['state']) && $output['state'] != '')) {
                    return $output;
                }
            }
        }
        // If nothing has been found check the other IP systems. After 3 tries return false
        if ($option <= 1) {
            return $this->ipInfo($ip, $purpose, $deep_detect, $iptype, $option + 1);
        } else {
            return ['country_code' => Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))];
            // return array('country_code' => 'ES');
        }
    }

    private function getCarrierIds($params)
    {
        if ($this->id_carriers === false && $this->addr_carriers === false && $this->ip_carriers === false) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->hookDisplayHeader($params);
            } else {
                $this->getEDCarriers($params['cart']);
            }
        }
        if ((empty($this->id_carriers) && empty($this->addr_carriers) && empty($this->ip_carriers)) && isset($params['carriers'])) {
            // Special case for the One Page Checkout module (onepagecheckout, #opc_checkout)
            $tmp_carriers = [];
            foreach ($params['carriers'] as $carrier) {
                $tmp_carriers[]['id_carrier'] = Cart::desintifier($carrier['id_carrier'], '');
            }

            //            if (!empty($tmp_carriers)) {
            //                $this->id_carriers = $this->getCarriersFromIds($tmp_carriers);
            //            }
            return $tmp_carriers;
        }
        if ($this->id_carriers === false && $this->addr_carriers === false && $this->ip_carriers === false) {
            // Couldn't find any carriers
            if (self::$debug_mode) {
                $this->debugVar('', 'Carriers couldn\'t be found. Aborting Estimated Delivery');
            }

            return false;
        }
        $carr_type = ['ID' => 'id_carriers', 'Address' => 'addr_carriers', 'IP' => 'ip_carriers'];
        foreach ($carr_type as $k => $v) {
            if (isset($this->$v) && $this->$v !== false) { // && count($this->$v) > 0) {
                // Try to get the carriers from the Cart or from the cookie if it's a registered customer
                if (self::$debug_mode) {
                    $this->debugVar('', 'Getting carriers from ' . $k);
                }

                return $this->$v;
            }
        }

        return false;
    }

    public function getIpCarriers()
    {
        $data = [];
        $ip = $this->getCurrentUserIP()[0];
        if (isset($this->context->cookie->ip_loc) && !empty($this->context->cookie->ip_loc)) {
            $data = @json_decode($this->context->cookie->ip_loc, true);
            if (!isset($data['ip']) || !in_array($data['ip'], $this->getCurrentUserIP())) {
                // IP have changed
                unset($this->context->cookie->ip_loc);
                $data = [];
            }
        }
        // If advanced mode is enabled and Force Ip is activated set it to override settings
        if ($this->adv_mode && Configuration::get('ed_force_ip') != '') {
            $this->force_ip = Configuration::get('ed_force_ip');
        }

        $iptype = $this->adv_mode && Configuration::get('ED_FORCE_COUNTRY') == 1 ? 1 : Configuration::get('ED_SHIPPING_TYPE');

        if ($this->adv_mode && Configuration::get('ED_DISABLE_GEOLOCATION')) {
            $country_iso = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
            if (self::$debug_mode) {
                $this->debugVar('', 'GEOLOCATION DISABLED: ' . SmartForm::openTag('br') . 'Getting the carriers associated to the default country: ' . $country_iso);
            }
            $data = ['country_code' => $country_iso];
            // Force country mode if the Geolocation is disabled
            $iptype = 1;
        } elseif (isset($this->force_ip) && $this->force_ip != '') {
            if (self::$debug_mode) {
                $this->debugVar($this->force_ip, 'IP Forced');
            }
            $data = $this->ipInfo($this->force_ip, 'location', true, $iptype);
        } else {
            if (!isset($data) || count($data) == 0) {
                $data = $this->ipInfo('Visitor', 'location', true, $iptype);
            }
        }

        if (!empty($data)) {
            $query = $subquery = [];
            $query['select'] = 'SELECT * FROM ' . _DB_PREFIX_ . 'carrier_shop AS cs 
            LEFT JOIN ' . _DB_PREFIX_ . 'carrier USING (id_carrier) 
            LEFT JOIN ' . _DB_PREFIX_ . 'ed_carriers edc USING (id_reference, id_shop)  
            LEFT JOIN ' . _DB_PREFIX_ . 'carrier_lang AS cl USING (id_carrier, id_shop) 
            LEFT JOIN ' . _DB_PREFIX_ . 'carrier_zone AS cz USING (id_carrier)';

            if (Configuration::get('ED_FORCE_COUNTRY') == 1) {
                $subquery[0] = 'SELECT id_zone FROM `' . _DB_PREFIX_ . 'country` AS tmpz ';
            } else {
                $subquery[0] = 'SELECT tmpz.`id_zone` FROM `' . _DB_PREFIX_ . 'state` AS tmpz LEFT JOIN `' . _DB_PREFIX_ . 'country` USING (id_country) ';
            }
            $query['select'] .= 'WHERE `id_zone` IN (' . $subquery[0];

            $sql = '';
            // If we have found data for the IP carry on with the Calculations
            switch ($iptype) {
                case 1: // Country
                    if (isset($data['country_code']) && $data['country_code'] != '') {
                        $subquery[1] = 'WHERE tmpz.`iso_code` LIKE \'' . pSQL($data['country_code']) . '%\' AND tmpz.`active` = 1 AND tmpz.id_zone > 0 GROUP BY tmpz.id_zone';
                        $sql = $query['select'] . $subquery[1] . ') AND `active` = 1 AND deleted = 0 AND cl.`id_lang` = ' . (int) $this->context->language->id . ' AND cs.id_shop = ' . (int) $this->context->shop->id; // .' AND active = 1 AND deleted=0'; // AND id_shop = '.$shopid;
                    }
                    break;
                case 2: // State
                    if (isset($data['state']) && $data['state'] != '') {
                        $subquery[1] = 'WHERE tmpz.name LIKE \'%' . pSQL($data['city']) . '%\' AND tmpz.`active` = 1 AND tmpz.id_zone > 0 GROUP BY tmpz.id_zone';
                        $sql = $query['select'] . $subquery[1] . ') AND `active` = 1 AND `deleted` = 0 AND cl.`id_lang` = ' . (int) $this->context->language->id . ' AND cs.id_shop = ' . (int) $this->context->shop->id;    // AND id_shop = '.$shopid;
                    }
                    break;
            }
            // Add the order by to the query this way we only get the latest carrier update
            $query['group_by'] = ' GROUP BY id_reference';
            $query['order_by'] = ' ORDER BY position ASC, id_carrier DESC';

            if ($sql != '') {
                // $sql = 'SELECT * FROM ('.$sql.$query['order_by'].') AS tmp GROUP BY id_reference';
                $sql = $sql . $query['group_by'] . $query['order_by'];
                // Set the zone id
                if ($this->id_zone == 0) {
                    $zone_query = $subquery[0] . $subquery[1];
                    $this->id_zone = DB::getInstance()->executeS($zone_query);
                }
                if (self::$debug_mode) {
                    $this->debugVar($sql, 'Carriers SQL');
                }
            }
            if (!isset($this->id_country) || $this->id_country == '') {
                $this->id_country = $this->getIDCountryFromISO($data['country_code']);
            }
            $carrier_ids = '';
            if ($sql != '') {
                $carrier_ids = Db::getInstance()->executeS($sql);
            }
            // TODO review last resort generation
            if ((empty($carrier_ids) || $carrier_ids == '') && $iptype == 2) {
                $data = $this->ipInfo('Visitor', 'location', true, $iptype);
                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'carrier_shop AS cs NATURAL JOIN ' . _DB_PREFIX_ . 'carrier LEFT JOIN ' . _DB_PREFIX_ . 'ed_carriers USING (id_reference, id_shop) LEFT JOIN ' . _DB_PREFIX_ . 'carrier_lang AS cl USING (id_carrier, id_shop) NATURAL JOIN ' . _DB_PREFIX_ . 'carrier_zone WHERE `id_zone` IN (SELECT id_zone FROM `' . _DB_PREFIX_ . 'country` LEFT JOIN `' . _DB_PREFIX_ . 'zone` USING (id_zone) WHERE `iso_code` = \'' . pSQL($data['country_code']) . '\') AND `active` = 1 AND `deleted` = 0 AND cl.`id_lang` = ' . (int) $this->context->language->id . $query['group_by'] . $query['order_by'];
                $carrier_ids = Db::getInstance()->executeS($sql);
            }
        }
        if (self::$debug_mode) {
            $this->debugVar($data, 'Data found through IP');
            if (isset($zone_query)) {
                $this->debugVar($zone_query, 'Zone query');
            }
            $this->debugVar($this->id_zone, 'Zone Results');
            if ($carrier_ids === false) {
                $this->debugVar(Db::getInstance()->getMsgError(), 'Query error');
            } else {
                $this->debugVar(count($carrier_ids), 'Carriers found on Database');
                $this->debugVar($carrier_ids, 'Carriers data');
            }
            $this->debugVar($sql, 'Carriers SQL');
        }
        if (empty($data)) {
            return false;
        }
        if (isset($carrier_ids) && is_array($carrier_ids) && count($carrier_ids) > 0) {
            if (!isset($this->context->cookie->ip_loc) || $this->context->cookie->ip_loc == '') {
                $data['ip'] = $ip;
                $this->context->cookie->ip_loc = @json_encode($data);
            }

            return $carrier_ids;
        }

        return false;
    }

    /**
     * Try to get the address from the cart, if is not available get it from the Cookie
     *
     * @params $params from the header
     *
     * @retun a list of carrier ids or FALSE
     */
    public function getAddrCarriers($cart, $only_zone = false)
    {
        $carriers = [];
        $addr = $this->getIdAddressFromCustomer($cart);
        if ($addr === false) {
            return;
        }
        if (self::$debug_mode) {
            $this->debugVar('Yes', 'Address Found for this customer');
        }
        if (!isset($addr['id_zone'])) {
            // Busquem la zona
            // Mirar si hi han estats
            $sql = 'SELECT id_zone FROM ' . _DB_PREFIX_ . 'state WHERE id_state = ' . (int) $addr['id_state'];
            $results = DB::getInstance()->executeS(pSQL($sql));
            if ($results === false || count($results) == 0) {
                // Sino buscar el country
                $sql = 'SELECT id_zone FROM ' . _DB_PREFIX_ . 'country WHERE id_country = ' . (int) $addr['id_country'];
                $results = DB::getInstance()->executeS(pSQL($sql));
            }
        } else {
            $results = [['id_zone' => $addr['id_zone']]];
        }

        if ($results === false) {
            if (self::$debug_mode) {
                $this->debugVar(DB::getInstance()->getMsgError(), 'SQL Error to detect the zone');
            }

            return;
        }

        if (count($results) > 0 && !$this->id_zone) {
            $this->id_zone = $results;
            if ($only_zone == true) {
                return true;
            }
        }
        // Set up the country ID if is not set for the carrier prices
        if (!isset($this->id_country) || $this->id_country == '') {
            $this->id_country = (int) $addr['id_country'];
        }
        if (isset($this->id_zone)) {
            if (isset($this->context->cookie->id_customer)) {
                $id_customer = $this->context->cookie->id_customer;
            } elseif (isset($cart) && isset($cart->id_customer)) {
                $id_customer = $cart->id_customer;
            }
            if (isset($id_customer)) {
                $carriers = $this->getCarriersForOrder($id_customer, $cart);
            }
            if (self::$debug_mode) {
                if (empty($carriers)) {
                    $this->debugVar($carriers, 'Couldn\'t locate the customer\'s carriers');
                } else {
                    $this->debugVar(count($carriers), 'Carriers found by Address');
                    $this->debugVar(array_column($carriers, 'name'), 'Carriers List');
                }
            }

            return $carriers;
        }
    }

    private function addDeliveryDataToCarriers($carriers)
    {
        $ref_list = array_column($carriers, 'id_reference');
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ed_carriers WHERE id_reference IN (' . implode(',', $ref_list) . ') ' . Shop::addSqlRestriction();
        $results = Db::getInstance()->executeS($sql);
        $carr = [];
        if ($results !== false) {
            foreach ($results as $result) {
                $carr[$result['id_reference']] = $result;
            }
        }
        if (empty($carr)) {
            if (self::$debug_mode) {
                $this->debugVar('', 'Can\'t add Delivery Data to Carriers');
            }

            return false;
        }

        foreach ($carriers as &$carrier) {
            // ToReview not sure if the condition is necessary, it has never been until thread #1048468
            if (isset($carr[$carrier['id_reference']]) && is_array($carr[$carrier['id_reference']])) {
                $carrier += $carr[$carrier['id_reference']];
            }
        }

        return $carriers;
    }

    public function getIdZone()
    {
        if (isset($this->id_zone) && !empty($this->id_zone)) {
            if (is_array($this->id_zone)) {
                $id_zone = $this->id_zone[0];
            } else {
                $id_zone = $this->id_zone;
            }

            return $id_zone['id_zone'] ?? $id_zone;
        }

        // For OPC modules, try to get the id_zone from the cart if it's available
        if (isset($this->context->cookie->id_cart)) {
            // Try to get the id_zone from the cart object if no other methods are available
            $cart = new Cart((int) $this->context->cookie->id_cart);
            $id_zone = Address::getZoneById($cart->id_address_delivery);
            if ($id_zone > 0) {
                $this->id_zone = $id_zone;

                return $this->id_zone;
            }
        }

        return false;
    }

    public function getCarriersFromIds($ids_list, $get_references = false)
    {
        if (empty($ids_list) && (int) Tools::getValue('id_carrier') > 0) {
            $ids_list = [(int) Tools::getValue('id_carrier')];
        }
        if ($get_references) {
            $ids_list = $this->addReferencesToCarrierIDs($ids_list);
        }
        if (is_array($ids_list) && count($ids_list) > 0) {
            // Address found, try to get the carriers available for that address
            $sql = 'SELECT c.*, cl.delay, ed.*' .
                ' FROM `' . _DB_PREFIX_ . 'carrier` c' .
                ' LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = ' . (int) $this->context->language->id . Shop::addSqlRestrictionOnLang('cl') . ')' .
                ' LEFT JOIN ' . _DB_PREFIX_ . 'ed_carriers AS ed USING (id_reference)' .
                ' WHERE c.`deleted` = 0 AND c.`active` = 1' .
                ' AND ed.id_shop = ' . (int) $this->context->shop->id .
                ' AND (c.id_carrier IN (' . implode(',', $ids_list) . ')' .
                ' OR c.id_reference IN (' . implode(',', $ids_list) . '))' .
                ' ORDER BY position ASC, id_carrier DESC';

            /* Old SQL
             $sql = 'SELECT * FROM '._DB_PREFIX_.'carrier_shop AS cs NATURAL JOIN '._DB_PREFIX_.'carrier LEFT JOIN '._DB_PREFIX_.'ed_carriers USING (id_reference,id_shop) LEFT JOIN '._DB_PREFIX_.'carrier_lang AS cl USING (id_carrier, id_shop) WHERE cl.id_lang='.$this->context->language->id.' AND id_carrier IN ('.implode(',', $ids_list).') AND active = 1 AND deleted=0 AND cs.id_shop = '.(int)$this->context->shop->id.' ORDER BY position ASC, id_carrier DESC';*/
            $results = DB::getInstance()->executeS(pSQL($sql));
            if (count($results) > 0 && $results !== false) {
                if (self::$debug_mode) {
                    $this->debugVar($results, 'Carriers Found by ID');
                }

                return $results;
            }
        }

        return false;
    }

    private function addReferencesToCarrierIDs($ids_list)
    {
        $sql = 'SELECT id_reference FROM ' . _DB_PREFIX_ . 'carrier WHERE id_carrier IN (' . implode(',', array_map('intval', $ids_list)) . ')';
        $results = Db::getInstance()->executeS(pSQL($sql));
        if (!empty($results)) {
            foreach ($results as $result) {
                $ids_list[] = (int) $result['id_reference'];
            }
        }

        return $ids_list;
    }

    public function getIdAddressFromCustomer($cart)
    {
        if (!empty($cart)) {
            if (is_array($cart)) {
                $id_address = $cart['id_address_delivery'];
            } else {
                $id_address = $cart->id_address_delivery;
            }
        }
        $id_zone = Address::getZoneById($id_address);
        if (!isset($id_address) || $id_address == 0) {
            // print_r($this->context->cookie);
            if (isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer > 0) {
                $customer = new Customer($this->context->cookie->id_customer);
                $addr = $customer->getAddresses($this->context->language->id);
            }
        } else {
            // Address found
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'address WHERE id_address = ' . (int) $id_address;
            $addr = DB::getInstance()->executeS(pSQL($sql));
            if ($addr === false || count($addr) == 0) {
                return false;
            }
            if ($id_zone !== false) {
                // If the ID zone was Found add it to the address array
                $addr[0]['id_zone'] = $id_zone;
            }
        }
        if (isset($addr) && count($addr) > 0) {
            return $addr[0];
        } else {
            // If we get here there is no information about the customer so we can't seach for the address
            return false;
        }
    }

    /** Executed Before showEstimatedDelivery
     ** Checks if product is a pack or just a regular product
     */
    public function generateEstimatedDelivery($params, $id_product = 0, $id_product_attribute = 0, $quantity_wanted = -1, $return = 'tpl', $disable_quantity_check = false, $quantity = false, $force_date = '', $from_picking = false, $is_order = false, $processing_pack = false)
    {
        if (self::$debug_time) {
            if (self::$debug_time) {
                $this->saveDebugTime('Init Generate Estimated', true);
            }
        }
        $ed = false;
        // If id_product no is set get it from the params
        if ($id_product == 0) {
            $this->getIdProdAndAttribute($params, $id_product, $id_product_attribute);
        }

        // Force date, just for test purposes, will only override the settings if the force_date variable has the default value
        if ($force_date == '' && $this->adv_mode && Configuration::get('ED_FORCE_DATE')) {
            $date = Configuration::get('ED_FORCED_DATE');
            if (Validate::isDate($date)) {
                $force_date = $date;
                DeliveryHelper::setForceDate($force_date);
            }
        }
        if ($quantity_wanted == -1) {
            $quantity_wanted = $this->getQuantityWantedFromParams($params);
        }
        if (!$processing_pack && $this->isPack($id_product) && ($this->adv_mode && !Configuration::get('ED_PACK_AS_PRODUCT'))) {
            if (self::$debug_mode) {
                $this->debugVar('', 'Is a Product Pack');
            }
            $both_deliveries = [false, false];
            $relandavail = [];
            // Get Advanced Pack Products
            $products = $this->getPackProducts($id_product, $id_product_attribute);
            $deliveries = $this->getDeliveriesFromProductList($params, $products, $both_deliveries, $relandavail, $force_date, $from_picking, $is_order, true);
            // Prices for products packs
            $pack_totals = $this->getPackTotals($products);

            $deliveries = $this->addPricesToCarriers($deliveries, $pack_totals);
            if ($deliveries === false) {
                return false;
            }
            if (!is_array($deliveries)) {
                $deliveries = [$deliveries];
            } else {
                $deliveries = array_values($deliveries);
            }
            $deliveries = $this->sortDeliveries($deliveries, Configuration::get('ED_DISPLAY_PRIORITY'));
            $ed = $this->renderDeliveries($deliveries, $return);
        } else {
            if (self::$debug_mode) {
                $this->debugVar('', 'Regular Product');
            }

            $ed = $this->showEstimatedDelivery($params, $id_product, $id_product_attribute, $quantity_wanted, $return, $disable_quantity_check, $quantity, $force_date, $from_picking, $is_order, $processing_pack);
        }
        if ($ed === false && !$this->is_17) {
            // Needed on 1.6 to prevent the load of all the combinations in one go
            return $this->display(__FILE__, 'views/templates/front/empty-delivery.tpl');
        } else {
            return $ed;
        }
    }

    /**
     * Returns the Estimated delivery given some parameters
     *
     * @param $params array hook parameters
     * @param $idprod int Product ID
     * @param $id_product_attribute int limit it just to a product attribute
     * @param $quantity_wanted int the number of items wanted useful for cart calculations
     * @param $return string The type of variable to return
     * @param $disable_quantity_check bool >> Only for ED regeneration when ED is not definitive
     * @param $quantity int Force the product stock for calculations in order process and validation
     *
     * @return return string|array Depending on the return type the return can be tpl, tpl-list or array
     **/
    public function showEstimatedDelivery($params, $id_product, $id_product_attribute, $quantity_wanted, $return, $disable_quantity_check, $quantity, $force_date, $from_picking, $is_order, $is_pack)
    {
        if (self::$debug_mode) {
            $this->debugVar('', 'Start EstimatedDelivery hook');
        }
        if ($id_product > 0) {
            // Get basic params to work
            $prod = new Product($id_product);
            if (!Validate::isLoadedObject($prod)) {
                // If a non valid product has been passed, return false
                if (self::$debug_mode) {
                    $this->debugVar('', 'Could not identify the product, aborting ED');
                }

                return false;
            }
            $idcat = $prod->getDefaultCategory();
            if ($is_order && (int) Tools::getValue('id_order') > 0) {
                $order = new Order((int) Tools::getValue('id_order'));
                $group = new Group((int) Customer::getDefaultGroupId($order->id_customer));
            } else {
                $group = Group::getCurrent();
            }
            // Check if this product is excluded from the ED

            if ((bool) $this->getExcludedCat('excluded = 1', $idcat) != false || ($group->show_prices == 0 && !$is_order)) {
                // Product Excluded, exit
                if (self::$debug_mode) {
                    if ($group->show_prices == 0) {
                        $this->debugVar('', 'Prices Disabled, aborting ED');
                    } else {
                        $this->debugVar('', 'Product Excluded by Category, aborting ED');
                    }
                }

                return false;
            }
            $dh = new DeliveryHelper();
            /*if ($this->is_17 && $params['id_product_attribute'] != 0) {

        }*/
            if (self::$debug_mode) {
                $this->debugVar('', 'Before Pre Process');
            }
            if (self::$debug_time) {
                $this->saveDebugTime('Before Pre-Process');
            }
            $dps = $this->preProcessProducts($prod, $id_product, $id_product_attribute, $idcat, $this->context->shop->id, $quantity_wanted, $disable_quantity_check, $quantity, $is_order);
            if ($dps !== false) {
                if (self::$debug_time) {
                    $this->saveDebugTime('After Pre-Process');
                    $this->debugVar($dps, 'DP (after pre process)');
                }

                if ($dps[0]->isVirtual()) {
                    $items = count($dps);
                    for ($i = 0; $i < $items; ++$i) {
                        // Check if it's a virtual product.
                        if ($this->virtual_msg == '') {
                            if (self::$debug_mode) {
                                $this->debugVar('', 'Virtual Product Without message, ED will not be generated');
                            }

                            return false;
                        }
                        $dps[$i]->msg = $this->virtual_msg;
                    }
                }
                $curr_order = $this->getCurrentOrderPriceAndWeight($prod, $id_product_attribute, $quantity_wanted);
                $carriers = $this->getCarrierIds($params);

                if ($carriers === false) {
                    return false;
                }
                $order = false;
                if ($is_order && (isset($params['order']) || isset($params['id_order']))) {
                    $id_order = 0;
                    if (isset($params['id_order'])) {
                        $id_order = $params['id_order'];
                    } elseif (isset($params['order']->id)) {
                        $id_order = $params['order']->id;
                    }
                    if ($id_order) {
                        $order = new Order($id_order);
                    }
                }

                $carriers = $this->getAvailableCarriersForED($carriers, $dps[0], $curr_order, $order);
                if (self::$debug_mode) {
                    $this->debugVar($carriers == false ? '0' : count($carriers), 'Carriers available before process:');
                }
                if ($carriers != false && count($carriers) > 0) {
                    if (self::$debug_time) {
                        $this->saveDebugTime('Before Process');
                    }
                    $deliveries = $this->processDeliveries($dps, $carriers, $dh, $force_date, $from_picking);
                    if (self::$debug_time) {
                        $this->saveDebugTime('After Process');
                    }
                    // Added force date to condition to prevent the Delivery Update Feature from trying to get the price
                    if ($deliveries !== false && !$is_pack && $force_date == '' && Configuration::get('ed_disp_price') && isset($this->context->controller) && $this->context->controller->php_self == 'product') {
                        $deliveries = $this->addPricesToCarriers($deliveries, $curr_order);
                    }

                    if ($deliveries !== false && count($deliveries) > 0) {
                        $deliveries = $this->sortDeliveries($deliveries, (int) Configuration::get('ED_DISPLAY_PRIORITY'));
                        if (self::$debug_time) {
                            $this->saveDebugTime('Before Render');
                        }

                        return $this->renderDeliveries($deliveries, $return);
                    }
                }
            } else {
                if (self::$debug_mode) {
                    $this->debugVar('', 'No Delivery Product Returned after pre process');
                }
            }
        } else {
            if (self::$debug_mode) {
                $this->debugVar('', 'Could not found the Product ID');
            }
        }

        return false;
    }

    private function sortDeliveries($deliveries, $mode)
    {
        if ($deliveries === false) {
            return $deliveries;
        }
        $old_del = $deliveries;
        if (!(reset($deliveries) instanceof EDelivery)) {
            // Not a list of deliveries, return
            return $deliveries;
        }
        switch ($mode) {
            case 1:
                usort($deliveries, 'EstimatedDelivery::cmp');
                break;
            case 2:
                usort($deliveries, 'EstimatedDelivery::cmpPrice');
                break;
            case 3:
                usort($deliveries, 'EstimatedDelivery::cmpCaPosition');
                break;
        }
        if (Configuration::get('ED_DEFAULT_CARRIER_FIRST')) {
            usort($deliveries, 'EstimatedDelivery::cmpDefault');
        }
        usort($deliveries, 'EstimatedDelivery::cmpUndefinedFirst');

        return $deliveries;
    }

    private function cmpUndefinedFirst($a, $b)
    {
        if ($a->dp->is_undefined_delivery) {
            return -1;
        } elseif ($b->dp->is_undefined_delivery) {
            return 1;
        } else {
            return 0;
        }
    }

    private function cmpCaPosition($a, $b)
    {
        if ($a->position == $b->position) {
            return 0;
        }

        return ($a->position > $b->position) ? 1 : -1;
    }

    private function cmpDefault($a, $b)
    {
        if ($a->dc->is_default) {
            return -1;
        } elseif ($b->dc->is_default) {
            return 1;
        } else {
            return 0;
        }
    }

    private function cmpPrice($a, $b)
    {
        if ($a->price_cmp == $b->price_cmp) {
            return $this->cmp($a, $b);
        }

        return ($a->price_cmp > $b->price_cmp) ? 1 : -1;
    }

    private function cmp($a, $b)
    {
        // Populate pla and plb variables
        $pla = $plb = ['23', '59'];
        foreach (['a', 'b'] as $item) {
            if (isset(${$item}->picking_limit)) {
                ${'pl' . $item} = explode(':', ${$item}->picking_limit);
                if (count(${'pl' . $item}) != 2) {
                    ${'pl' . $item} = ['23', '59'];
                }
            }
        }
        // Init compare
        if ($a->delivery_cmp_min == $b->delivery_cmp_min) {
            if ($a->delivery_cmp_max == $b->delivery_cmp_max) {
                if ($pla[0] == $plb[0]) {
                    if ($pla[1] == $plb[1]) {
                        return ($a->position < $b->position) ? -1 : 1;
                    }
                    $ret = ($pla[1] < $plb[1]) ? -1 : 1;
                } else {
                    $ret = ($pla[0] < $plb[0]) ? -1 : 1;
                }

                // If the setting is active, fetches the carrier with the longest delivery by inverting the results
                return Configuration::get('ed_longer_picking') ? (-1 * $ret) : $ret;
            }

            return ($a->delivery_cmp_max < $b->delivery_cmp_max) ? -1 : 1;
        }

        return ($a->delivery_cmp_min < $b->delivery_cmp_min) ? -1 : 1;
    }

    /**
     * @params $prod The product object
     * @params $id_product the ID of the product
     * @params $id_product_attribute If it's a single combination instead of thw whole product
     * @params $idcat Id of the default category
     * @params $id_shop Id of the current shop
     * @params $quantity_wanted Quantity wanted for the estimated Delviery
     * @params $disable_quantity_check Use for recalculation
     * @params $quantity Force a stock quantity, used only in the ED for order calculations
     **/
    private function preProcessProducts($prod, $id_product, &$id_product_attribute, $idcat, $id_shop, $quantity_wanted, $disable_quantity_check, $quantity, $is_order)
    {
        // Create the DeliveryProduct objects
        if (DeliveryHelper::isDisabledProduct($id_product)) {
            if (self::$debug_mode) {
                $this->debugVar('', 'Product "' . $prod->name[$this->context->language->id] . '" (ID: ' . $id_product . ') it\'s excluded by the product exclusion feature');
            }

            return false;
        }
        if ($id_product_attribute > 0 && DeliveryHelper::isDisabledCombination($id_product, $id_product_attribute)) {
            if (self::$debug_mode) {
                $this->debugVar('', 'Combination is disabled, skip generation');
            }

            return false;
        }
        if ($id_product_attribute == 0 && !$this->is_17) {
            /* Force the generation only of the current combination even on 1.6 */
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }
        $dp = new DeliveryProduct($prod, $id_product, $id_product_attribute, $idcat, $id_shop, $quantity_wanted, $quantity, $is_order);
        if (self::$debug_mode) {
            $this->debugVar('', 'New Delivery Product');
        }

        /* For 1.7 Products */
        if (!is_array($dp)) {
            $dp = [$dp];
        }

        if (self::$debug_mode) {
            $this->debugVar(count($dp), 'Before SetOrClean');
        }
        if ($disable_quantity_check == false) {
            // Clean OOS Products from the ED if can't sell
            $dp = DeliveryHelper::SetOrCleanOOS($dp);
        }
        if (self::$debug_mode) {
            if ($dp === false || empty($dp)) {
                $this->debugVar(['quantity' => $quantity, 'quantity_wanted' => $quantity_wanted], 'Product Quantities');
            } else {
                $this->debugVar($dp, 'DP after Clean');
                $this->debugVar(count($dp), 'DP Count');
            }
        }

        if (empty($dp) || count($dp) == 0) {
            if (self::$debug_mode) {
                $this->debugVar('', 'ED: Product not allowed. Skipping product');
            }

            return false;
        }

        return $dp;
    }

    private function checkCombiOOS($combi, $ret_mode = 'check')
    {
        $return = false;
        $all = 0;
        foreach ($combi as $c) {
            if ($c['quantity'] <= 0) {
                $return = true;
                ++$all;
                if ($ret_mode == 'check') {
                    break;
                }
            }
        }
        if ($ret_mode == 'check') {
            return $return;
        } else {
            return $all == count($combi) ? true : false;
        }

        return $dp;
    }

    //    private function printProductSpecialDate($dp)
    //    {
    //        $dp->msg = Configuration::get('ed_virtual_msg_'.$this->context->language->id);
    //        if ($dp->msg != '') {
    //            return $dp;
    //        }
    //        return false;
    //    }

    private function getIdProdAndAttribute($params, &$id_product, &$id_product_attribute)
    {
        if (isset($params['product']) && $this->is_17) {
            // 1.7 Versions
            if (is_object($params['product'])) {
                if (isset($params['product']->id_product_attribute) && $params['product']->id_product_attribute > 0) {
                    $id_product_attribute = $params['product']->id_product_attribute;
                }
            } else {
                if (isset($params['product']['id_product_attribute']) && $params['product']['id_product_attribute'] > 0) {
                    $id_product_attribute = $params['product']['id_product_attribute'];
                }
            }
            if ($id_product_attribute == 0 && Tools::getIsset('id_product_attribute')) {
                $id_product_attribute = (int) Tools::getValue('id_product_attribute');
            }
            if ($id_product_attribute > 0) {
                $this->context->smarty->assign(['id_product_attribute' => (int) $id_product_attribute]);
            }
        }
        // Prior to 1.7 versions
        if (isset($params['id_product']) && $params['id_product'] > 0) {
            $id_product = $params['id_product'];
        } elseif (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } elseif (is_object($params['product'])) {
                $id_product = $params['product']->id;
            } else {
                return false;
            }
        } else {
            $id_product = (int) Tools::getValue('id_product');
        }
    }

    private function processDeliveries($dps, $allcarriers, $dh, $force_date, $from_picking)
    {
        if (self::$debug_mode) {
            $this->debugVar('', 'Start Processing the Deliveries');
        }
        $deliveries = [];
        $forced_date = false;
        if ($force_date != '' && strtotime($force_date) && $force_date != '0000-00-00 00:00:00') {
            $forced_date = true;
            $pre_initial_date = date('Y-m-d H:i:s', strtotime($force_date));
        } else {
            $pre_initial_date = date('Y-m-d H:i:s');
        }
        if (self::$debug_mode) {
            $this->debugVar($pre_initial_date, 'Initial date before holidays');
        }
        $pre_initial_date = $dh->checkHolidays($pre_initial_date);
        if (self::$debug_mode) {
            $this->debugVar($pre_initial_date, 'Initial date after holidays');
        }
        $special_df = EDTools::getDateFormat('special_df');
        // Start building the Deliveries
        // TODO Review Available Release dates creation
        foreach ($dps as $dp) {
            $delivery = new EDelivery($dp);
            if ($dp->is_virtual) {
                $deliveries[] = clone $delivery;
            } else {
                // Apply the restrictions for each combination, just in case the new weight or price exceed the carrier restrictions
                if ($this->adv_mode && Configuration::get('ED_DIS_REST') == 0) {
                    $carriers = $this->applyProductRestrictionToCarriers($dp, $allcarriers);
                } else {
                    $carriers = $allcarriers;
                }
                foreach ($carriers as $dc) {
                    $initial_date = $pre_initial_date;
                    $tmp_delivery = clone $delivery;
                    if (self::$debug_mode) {
                        $this->debugVar($dc->name, 'Processing Carrier');
                    }
                    // Set the carrier for the delivery
                    $tmp_delivery->setDeliveryCarrier($dc);
                    if (($dp->is_available && $this->available_msg == '') || $dp->is_release) {
                        $initial_date = date(
                            'Y-m-d H:i:s',
                            strtotime(($dp->is_available ? $dp->available_date : $dp->release_date) . ' 00:00:00')
                        );
                        $tmp_delivery->dp->formatted_date = $special_df->format($initial_date);
                    }
                    // Undefined delivery date procedure
                    if ($dp->isOOS && $dp->is_undefined_delivery) {
                        // Can't set any dates
                        $tmp_delivery->dp->msg = $this->getUndefinedMessage(EDTools::getControllerName() == 'order' ? 'cart' : 'product');
                        $tmp_delivery->dc->delivery_cmp_min = $tmp_delivery->dc->delivery_cmp_max = '1000-01-01';
                        $deliveries[] = $tmp_delivery;
                        continue;
                    }
                    if (!$from_picking) {
                        $tmp_delivery->setInitialDate($tmp_delivery->calculatePicking($dh, $initial_date, $forced_date));
                        if (self::$debug_mode) {
                            $this->debugVar($tmp_delivery->initial_date, 'Delivery Initial Date');
                        }
                        // Calculate the picking date
                        $tmp_delivery->shipping_day = $tmp_delivery->picking_date = $tmp_delivery->initial_date;
                        // $tmp_delivery->picking_date = $tmp_delivery->calculatePicking($dh, $tmp_delivery->picking_date);
                        if (self::$debug_mode) {
                            $this->debugVar($tmp_delivery->picking_date, 'Picking day');
                        }
                        /* If it's a release or an available date update the minimum to match the new date */
                        /* Keep the picking date to not display a way too long hours before the next picking for the product
                    But update the shipping_day to match the minimum day for the delivery */

                        if (($dp->is_available && $this->available_msg != '') || $dp->is_release) {
                            $tmp_delivery->shipping_day = $tmp_delivery->calculatePicking($dh, date('Y-m-d', strtotime(($dp->is_available ? $dp->available_date : $dp->release_date) . ' 00:00:00')), $forced_date);
                            $tmp_delivery->dp->formatted_date = $special_df->format($tmp_delivery->shipping_day);
                        }
                        // Shipping day is the supposed day the order should be shipped to be deliveried in the ED range
                        if (!$tmp_delivery->dp->is_release && !($dp->is_available && $this->available_msg != '')) {
                            $tmp_delivery->shipping_day = $dh->addDaysIteration($tmp_delivery->shipping_day, $tmp_delivery->dc->picking_days, $tmp_delivery->dp->add_picking_days);
                        }
                        /* Add warehouse days depending on the stock */
                        if ($tmp_delivery->dp->id_warehouse > 0 && $tmp_delivery->dp->warehouse_add_days > 0) {
                            $tmp_delivery->shipping_day = $dh->addDaysIteration($tmp_delivery->shipping_day, $tmp_delivery->dc->picking_days, $tmp_delivery->dp->warehouse_add_days);
                        }
                        // Make sure the shipping day is a picking day too
                        $tmp_delivery->shipping_day = $tmp_delivery->calculatePicking($dh, $tmp_delivery->shipping_day, $forced_date);
                        if (self::$debug_mode) {
                            $this->debugVar($tmp_delivery->shipping_day, 'Shipping day');
                        }
                    } else {
                        $tmp_delivery->setInitialDate($initial_date);
                        // was picking_day but is no longer correct as shipping day take it's value from now on
                        $tmp_delivery->shipping_day = $initial_date;
                    }
                    // Set Days to add for each combination
                    $tmp_delivery->days_to_add = DeliveryHelper::getDateDiff(date('Y-m-d H:i:s'), $tmp_delivery->initial_date, '%a');
                    // Calculate the shipping
                    $tmp_delivery->calculateShipping($dh, $dc, $dp);
                    $tmp_delivery->setDateFormatForED('base_df', self::getTot());
                    if (self::$debug_mode) {
                        $this->debugVar($tmp_delivery, 'Delivery after shipping calculation');
                    }
                    $deliveries[] = $tmp_delivery;
                }
            }
        }
        $this->context->smarty->assign(
            [
                'preorder_msg' => $this->release_msg,
                'available_msg' => $this->available_msg,
                'undefined_msg' => $this->undefined_msg,
            ]
        );
        if (count($deliveries) > 0) {
            return $deliveries;
        }

        return false;
    }

    private function getSpecialMessages()
    {
        $msgs = ['undefined', 'available', 'release', 'virtual', 'customization'];
        foreach ($msgs as $msg) {
            $this->{$msg . '_msg'} = $this->{'get' . ucfirst($msg) . 'Message'}();
        }
    }

    public function getUndefinedMessage($type = 'product')
    {
        $msg = '';
        switch ($type) {
            case 'product':
                $msg = Configuration::get('ed_undefined_delivery_msg', $this->context->language->id);
                if (empty($msg)) {
                    // $msg = $this->l('Delivery date for this product is not defined. We will contact you after you complete the order');
                    $msg = $this->l('Inquire about delivery time');
                }
                break;
            case 'cart':
                $msg = $this->l('Delivery date to be established. You will be contacted as soon as we have a estimated delivery');
                break;
            case 'order':
                $msg = $this->l('Some products in your order are pending a delivery date confirmation. We\'ll be sure to notify you promptly by phone or email when we have a definite date');
                break;
        }

        if (empty($msg)) {
            return '';
        }

        $min = (int) Configuration::get('ed_undefined_validate_min');
        if ($min > 0) {
            $max = (int) Configuration::get('ed_undefined_validate_max');
            if ($min == $max) {
                if ($min == 1) {
                    $tmp_msg = $this->l('This usually takes 1 day');
                } else {
                    $tmp_msg = $this->l('This usually takes %d days');
                }
            } else {
                $tmp_msg = $this->l('This usually takes between %d and %d days');
            }
            if ($type == 'order') {
                $msg .= '. ' . SmartForm::genDesc(sprintf($tmp_msg, $min, $max), 'u');
            }
        }

        return $msg;
    }

    public function getAvailableMessage()
    {
        return Configuration::get('ed_available_date_msg', $this->context->language->id);
    }

    public function getReleaseMessage()
    {
        return Configuration::get('ed_preorder_msg', $this->context->language->id);
    }

    public function getVirtualMessage()
    {
        return Configuration::get('ed_virtual_msg', $this->context->language->id);
    }

    public function getCustomizationMessage()
    {
        return Configuration::get('ed_custom_date_msg', $this->context->language->id);
    }

    private function applyProductRestrictionToCarriers($dp, $dcs)
    {
        // If product have a packaging size configured, check if the carriers are allowed to delivery it
        $csize = count($dcs);
        for ($i = 0; $i < $csize; ++$i) {
            if ($dp->width > 0 || $dp->height > 0 || $dp->depth > 0) {
                $carrier_sizes = [(int) $dcs[$i]->max_width, (int) $dcs[$i]->max_height, (int) $dcs[$i]->max_depth];
                $product_sizes = [(int) $dp->width, (int) $dp->height, (int) $dp->depth];
                rsort($carrier_sizes, SORT_NUMERIC);
                rsort($product_sizes, SORT_NUMERIC);

                if (($carrier_sizes[0] > 0 && $carrier_sizes[0] < $product_sizes[0])
                    || ($carrier_sizes[1] > 0 && $carrier_sizes[1] < $product_sizes[1])
                    || ($carrier_sizes[2] > 0 && $carrier_sizes[2] < $product_sizes[2])) {
                    unset($dcs[$i]);
                }
            }
        }

        return $dcs;
    }

    /**
     * Display delivery style
     *
     * @param $deliveries list of the available delivery times, sorted
     * @param $retun format to return the data tpl | array
     *
     * @return The generated HTML to display the Estimated Delivery
     */
    private function renderDeliveries($deliveries, $return = 'tpl')
    {
        $output = '';
        // TODO check si hi ha available date. Si es que si imprimir el missatge de available date.
        if (self::$debug_mode) {
            $this->debugVar('', 'Render the Deliveries');
        }
        if ($return == 'tpl-list' && Configuration::get('ed_list_max_display')) {
            $deliveries = $this->filterExceededDeliveries($deliveries);
        }
        if (empty($deliveries)) {
            return '';
        }
        if ($return == 'array') {
            if (self::$debug_mode) {
                $this->debugVar('', 'Returning the delivery Array');
            }

            return $deliveries;
        }
        if (is_array($deliveries[0])) {
            // It's a pack and Individual dates are allowed
            $deliveries = DeliveryHelper::groupIndividualDeliveriesForPacks($deliveries);
        }
        $display_mode = Configuration::get('ED_STYLE');
        $this->context->smarty->assign([
            'edclass' => Configuration::get('ed_class'),
            'edbackground' => Configuration::get('ed_custombg'),
            'edborder' => Configuration::get('ed_customborder'),
            'edstyle' => $display_mode,
            'edidprod' => reset($deliveries)->id_product,
            'ed_popup' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS'),
            'ed_display_checkmark' => Configuration::get('ed_display_checkmark'),
            'enable_custom_days' => Configuration::get('ed_enable_custom_days'),
        ]);
        if (Configuration::get('ed_disp_price')) {
            $this->context->smarty->assign([
                'ed_display_price' => Configuration::get('ed_disp_price'),
                'ed_price_prefix' => Configuration::get('ed_price_prefix'),
                'ed_price_suffix' => Configuration::get('ed_price_suffix'),
            ]);
        }
        if ($return == 'tpl-list') {
            $delivery = $this->prepareDeliveryForListing($deliveries[0]);
            $this->context->smarty->assign(['delivery' => $delivery]);

            return $this->display(__FILE__, 'views/templates/hook/estimateddelivery-list.tpl');
        }

        if ($display_mode < 2) {
            $deliveries = DeliveryHelper::groupDeliveriesByCombi($deliveries);
            $this->context->smarty->assign(['deliveries' => $deliveries]);
        } else {
            $this->context->smarty->assign(['delivery' => $deliveries[0]]);
        }
        $days_to_add = 0;

        // Add the rest parameters to all deliveries
        if (!empty($deliveries)) {
            array_walk_recursive($deliveries, function ($deli) {
                if ($deli instanceof EDelivery) {
                    $deli->setDeliveryRestParameters();
                }
            });
        }

        /* Popup Carreirs */
        $all_deliveries = $deliveries;
        /* Display all carriers popup */
        $first_delivery = reset($all_deliveries);
        if (is_array($first_delivery) && !Configuration::get('ED_DATES_BY_PRODUCT')) {
            $first_delivery = reset($first_delivery);
        }

        if (Configuration::get('ED_DISPLAY_POPUP_CARRIERS') && !$first_delivery->dp->is_virtual && !$first_delivery->dp->is_undefined_delivery) {
            // Order the deliveries by the standard order (set in carriers menu)
            if ($this->is_17) {
                if (is_array(reset($all_deliveries))) {
                    $all_deliveries = reset($all_deliveries);
                }
                $all_deliveries = $this->sortDeliveries($all_deliveries, 3);
                $all_deliveries = $this->addImagesToDeliveries($all_deliveries);
                $this->context->smarty->assign(
                    [
                        'ed_popup_options' => [
                            'name' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_NAME'),
                            'desc' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_DESC'),
                            'img' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_IMG'),
                            'price' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_PRICE'),
                        ],
                        'popup_deliveries' => $all_deliveries, // was $deliveries
                        'ed_popup_background' => Configuration::get('ED_DISPLAY_POPUP_BACKGROUND'),
                    ]
                );
                if (self::$debug_time) {
                    $this->debugVar(self::$debug_times, 'Module Generation Times');
                }
                $output .= $this->display(__FILE__, 'views/templates/hook/estimateddelivery-popup.tpl');
            }
        }

        // Picking date format
        if ($display_mode == 4) {
            foreach ($deliveries as &$delivery) {
                $delivery->formatted_date = EDTools::setDateFormatForED($delivery->shipping_day, 'base_df');
            }
        }

        // Get the time before the fastest carrier picking day
        if ($display_mode >= 2 && $display_mode <= 4) {
            $deliveries = $this->cleanDuplicateDeliveries($deliveries);
            // Display Estimated Delviery Order BeforeStyle
            if (self::$debug_mode) {
                $this->debugVar('', 'Printing EstimatedDelivery Order Before');
            }
            if (self::$debug_time) {
                $this->debugVar(self::$debug_times, 'Module Generation Times');
            }
            $this->context->smarty->assign(
                [
                    'deliveries' => $deliveries,
                ]
            );
            if ($display_mode == 4) {
                $output = $this->display(__FILE__, 'views/templates/hook/estimateddelivery-ob-picking-day.tpl') . $output;
            } else {
                $output = $this->display(__FILE__, 'views/templates/hook/estimateddelivery-ob.tpl') . $output;
            }
        } elseif ($display_mode == 5) {
            // It's double display mode
            $sorting = [
                1 => $this->l('Fastest Carrier'),
                2 => $this->l('Cheapest carrier'),
                3 => $this->l('Recommended Carrier'),
            ];

            $results = $sorting_title = [];
            $results[0] = $deliveries[0];
            $sorting_title[$deliveries[0]->dc->id_carrier] = $sorting[Configuration::get('ED_DISPLAY_PRIORITY')];
            if (count($deliveries) > 1) {
                $deliveries = $this->sortDeliveries($deliveries, Configuration::get('ED_DISPLAY_PRIORITY_2'));
                if (Configuration::get('ED_DISPLAY_DOUBLE_REPEAT')) {
                    $results[1] = $deliveries[0];
                } else {
                    $results[1] = $deliveries[0]->dc->id_carrier == $results[0]->dc->id_carrier ? $deliveries[1] : $deliveries[0];
                }
                $results[1]->setDeliveryRestParameters();
                $sorting_title[$results[1]->dc->id_carrier] = $sorting[Configuration::get('ED_DISPLAY_PRIORITY_2')];
            }
            $this->context->smarty->assign(
                [
                    'deliveries' => $results,
                    'sorting_title' => $sorting_title,
                ]
            );
            if (self::$debug_time) {
                $this->debugVar(self::$debug_times, 'Module Generation Times');
            }
            $output = $this->display(__FILE__, 'views/templates/hook/estimateddelivery-double-display.tpl') . $output;
        } else {
            if (self::$debug_time) {
                $this->debugVar(self::$debug_times, 'Module Generation Times');
            }
            $output = $this->display(__FILE__, 'views/templates/hook/estimateddelivery.tpl') . $output;
        }
        if (self::$debug_mode) {
            $this->debugVar('', 'Printing EstimatedDelivery Carrier Style');
        }

        return $output;
    }

    /**
     * If the maximum delivery is greater than the limit, remove the deliery from the list
     *
     * @param $deliveries
     *
     * @return mixed
     */
    private function filterExceededDeliveries($deliveries)
    {
        $max_days = (int) Configuration::get('ed_list_max_display');
        if ($max_days == 0) {
            return $deliveries;
        }
        $date_limit = date('Y-m-d H:i:s', strtotime('+' . $max_days . ' days'));

        foreach ($deliveries as $key => $delivery) {
            if ($delivery->delivery_cmp_max > $date_limit) {
                unset($deliveries[$key]);
            }
        }

        return $deliveries;
    }

    private function addImagesToDeliveries($deliveries)
    {
        foreach ($deliveries as &$delivery) {
            if (isset($delivery->dc) && isset($delivery->dc->id_carrier)) {
                $fn = _PS_IMG_DIR_ . 's/' . $delivery->dc->id_carrier . '.jpg';
                if (file_exists($fn)) { // TODO HERE revisar isset del tpl i aqui
                    $delivery->dc->img = __PS_BASE_URI__ . 'img/s/' . $delivery->dc->id_carrier . '.jpg';
                }
            }
        }

        return $deliveries;
    }

    private function cleanDuplicateDeliveries($deliveries)
    {
        $tmp = [];
        $return = [];
        $c = count($deliveries);
        for ($i = 0; $i < $c; ++$i) {
            $key = $deliveries[$i]->dp->id_product . ',' . $deliveries[$i]->dp->id_product_attribute;
            if (empty($tmp) || !in_array($key, $tmp)) {
                $tmp[] = $key;
                $return[] = $deliveries[$i];
            }
        }

        return $return;
    }

    private function prepareDeliveryForListing($delivery)
    {
        $mode = (int) Configuration::get('ED_LIST_DATE_FORMAT');
        $types = ['min', 'max'];

        // Last two date formats are the hours and days date format
        $total_df = count($this->listDateFormat);
        foreach ($types as $type) {
            if ($mode < ($total_df - 2)) {
                $delivery->{'delivery_' . $type} = EDTools::setDateFormatForED($delivery->{'delivery_cmp_' . $type}, 'product_listing_df');
            } else {
                $delivery->{'delivery_' . $type} = DeliveryHelper::getDateDiff(date('Y-m-d'), $delivery->{'delivery_cmp_' . $type}, '%a');
                if ($mode == ($total_df - 2)) {
                    $delivery->{'delivery_' . $type} *= 24;
                }
            }
        }

        return $delivery;
    }

    public function setDateFormatForCarrier($carrier, $df, $tot = false)
    {
        $types = ['min', 'max'];
        foreach ($types as $type) {
            if (!isset($carrier['delivery_cmp_' . $type]) && isset($carrier['delivery_' . $type])) {
                $carrier['delivery_cmp_' . $type] = $carrier['delivery_' . $type];
            }
            $days_diff = EDTools::getDaysDiff($carrier['delivery_' . $type]);
            if ($tot && $days_diff < 2) {
                $carrier['tot'] = true;
            }
            $carrier['delivery_' . $type] = EDTools::setDateFormatForED($carrier['delivery_' . $type], $df); // WAS , $days_diff, $tot);
        }

        return $carrier;
    }

    private function getAvailableCarriersForED($carrier_ids, $delivery_product, $curr_order, $order)
    {
        $id_default_carrier = Configuration::get('PS_CARRIER_DEFAULT') > 0 ? Configuration::get('PS_CARRIER_DEFAULT') : 0;
        // Get all product available carriers (id and ref)
        $productc = $this->getProductCarriers($delivery_product->id_product);
        if (self::$debug_mode) {
            $this->debugVar(count($carrier_ids), 'Initial Carrier IDs');
            $this->debugVar($productc, 'Product Carriers');
        }
        // If Advanced stock enabled intersect Product Carriers with Warehouses
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $productc = $this->checkWarehouses($delivery_product->id_product, $productc);
            if (self::$debug_mode) {
                $this->debugVar($productc, 'Product Carriers after Wharehouse Check');
            }
        }

        if (!($this->adv_mode && Configuration::get('ED_DIS_REST')) && $order == false) { // WAS !empty($curr_order)) { for order updates. TODO review
            // $curr_order will only be null when we are updating an order
            // Filter Carriers and exclude them if the price or weight isn't in it's range.

            $carrier_ids = $this->applyRestrictionsToCarriers($carrier_ids, $curr_order);

            if (self::$debug_mode) {
                $this->debugVar(count($carrier_ids), 'Carriers after restrictions');
                $this->debugVar($carrier_ids, 'Carriers list after restrictions');
            }
        }
        // Clean the carriers list if product has custom carriers activated also intersect with IP avaliable carriers
        if (($productc === false || count($productc) > 0)
            && is_array($carrier_ids)
            && count($carrier_ids) > 0) {
            $edcarriers = [];
            foreach ($carrier_ids as &$carrier) {
                // Check if it's in the Product Carriers
                if ($productc === false
                    || in_array($carrier['id_reference'], $productc)
                    || in_array($carrier['id_carrier'], $productc)) {
                    if ($order && isset($order->id_address_delivery)) {
                        $carrier['id_zone'] = Address::getZoneById($order->id_address_delivery);
                    } else {
                        $id_zone = $this->getIdZone();
                        if (!$id_zone) {
                            return false;
                        }
                        $carrier['id_zone'] = $id_zone;
                    }
                    $dc = new DeliveryCarrier($carrier, (int) $carrier['id_carrier'] == (int) $id_default_carrier);
                    if ((int) $dc->id_carrier > 0) {
                        $edcarriers[] = $dc;
                    }
                }
            }
            if (self::$debug_mode) {
                $this->debugVar(count($edcarriers), 'After Product Carriers');
            }
            if (count($edcarriers) > 0 && $this->adv_carr) {
                $edcarriers = DeliveryCarrier::cleanDisabledCarriers($edcarriers, $this->controllerName, $order);
                if (self::$debug_mode) {
                    $this->debugVar(count($edcarriers), 'After Clean Disabled Carriers');
                }
            }
            if (count($edcarriers) == 0) {
                if (self::$debug_mode) {
                    $this->debugVar('All carriers are disabled');
                }

                return false;
            } else {
                return array_values($edcarriers);
            }
        } else {
            if (self::$debug_mode) {
                $this->debugVar('No cariers to process');

                return false;
            }
        }
    }

    private function applyRestrictionsToCarriers($carriers, $curr_order)
    {
        $mode = ['price', 'weight', 'price'];
        if (is_array($carriers) && $carriers != '') {
            $len = count($carriers);
            for ($i = 0; $i < $len; ++$i) {
                $type = $carriers[$i]['shipping_method'];
                $behaviour = $carriers[$i]['range_behavior'];
                // In some fresh installs shipping method of the default carriers is 0
                if ($type > 0) {
                    // Create the vars if they don't exist
                    if (!isset($this->{$mode[$type] . 'Ranges'})) {
                        $this->{$mode[$type] . 'Ranges'} = $this->getCarriersRange($mode[$type]);
                        // print_r($this->{$mode[$type].'Ranges'});
                    }
                    if (isset($this->{$mode[$type] . 'Ranges'}[$carriers[$i]['id_carrier']])) {
                        if ($this->getIdRange($mode[$type], $curr_order[$mode[$type]], $this->{$mode[$type] . 'Ranges'}[$carriers[$i]['id_carrier']], $behaviour) === false) {
                            // Not allowed for this range, remove the carrier
                            unset($carriers[$i]);
                        }
                    }
                }
            }

            return array_values($carriers);
        }

        return array_values([]);
    }

    private function getProductCarriers($idprod)
    {
        if ($this->adv_mode && Configuration::get('ED_DISABLE_PRODUCT_CARRIERS')) {
            return false;
        }
        $p = new Product((int) $idprod);
        $pcarriers = $p->getCarriers();
        $return = [];
        if (count($pcarriers) <= 0) {
            $pcarriers = $this->getCarriersList();
        }
        foreach ($pcarriers as $c) {
            // WAS $return[] = $result['id_carrier'];
            $return[] = $c['id_carrier'];
            if (!in_array($c['id_reference'], $return)) {
                $return[] = $c['id_reference'];
            }
        }

        return $return;
    }

    private function checkWarehouses($idprod, $productc)
    {
        /* TODO - finish new WH system check */
        $carriers = $newcarriers = [];
        $wids = Warehouse::getWarehousesByProductId($idprod);
        foreach ($wids as $wid) {
            $ws = new Warehouse($wid);
            $carriers = array_merge($carriers, $ws->getWsCarriers());
        }
        /* END TODO */
        if (StockAvailable::dependsOnStock($idprod)) {
            // It has the advanced Whatehouses configuration active
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'stock AS stock LEFT JOIN ' . _DB_PREFIX_ . 'warehouse_product_location  USING (id_warehouse) LEFT JOIN ' . _DB_PREFIX_ . 'warehouse_carrier USING (id_warehouse) WHERE stock.id_product = ' . (int) $idprod . ' AND usable_quantity > 0 GROUP BY id_carrier';
        } else {
            // Get the Warehouses for this product
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'warehouse_product_location LEFT JOIN ' . _DB_PREFIX_ . 'warehouse_carrier USING (id_warehouse) WHERE id_product = ' . (int) $idprod . ' GROUP BY id_carrier';
        }
        $results = Db::getInstance()->executeS($sql);
        if (empty($results)) {
            // No warehouse assigned
            return $productc;
        } else {
            // Intersect carriers and wharehouses
            $c = count($results);
            for ($i = 0; $i < $c; ++$i) {
                if (in_array($results[$i]['id_carrier'], $productc)) {
                    $newcarriers[] = $results[$i]['id_carrier'];
                }
            }
            if (count($newcarriers) > 0) {
                return $newcarriers;
            } else {
                return $productc;
            }
        }
    }

    /**
     * Retrieve the current order data to add it to the Estimated Delivery calculation
     * If the Calculation Method is set to Only the current product this will return false,
     * otherwise it will return an array with the current order data
     *
     * @param $prod String Product Object
     * @param $id_product_attribute int If current product should be added pass the id_product_attribute to handle coombinations too
     * @param $quantity_wanted int The quantity wanted of the current product
     *
     * @return array|false
     */
    public function getCurrentOrderPriceAndWeight($prod, $id_product_attribute, $quantity_wanted)
    {
        $total_p = $total_w = 0;

        // Only add the current product if we're not in an order
        if (EDTools::getControllerName() != 'order') {
            $total_p += Product::getPriceStatic($prod->id, true, $id_product_attribute > 0 ? $id_product_attribute : null) * $quantity_wanted;
            // Check if the 2nd parameter (tax) should be updated depending on the settings and which ones
            $total_w += $prod->weight * $quantity_wanted;
        }
        if ((int) $id_product_attribute > 0) {
            $combi_data = $prod->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
            $combi_data = $combi_data[0];
            $total_w += $combi_data['weight'];
        }
        if (EDTools::getControllerName() == 'order'
            || (Configuration::get('ED_CALCULATION_METHOD') == 1
                && isset($this->context->cart)
                && !empty($this->context->cart->getProducts()))) {
            foreach ($this->context->cart->getProducts() as $item) {
                $total_p += $item['price_wt'] * $item['cart_quantity'];
                $total_w += $item['weight'] * $item['cart_quantity'];
            }
        }
        $current_order = ['price' => $total_p, 'weight' => $total_w];
        if (self::$debug_mode) {
            $this->debugVar($current_order, 'Current order');
        }

        return $current_order;
    }

    /**
     * Adds The carrier estimated prices to the deliveries
     *
     * @delieries The array with the available deliveries
     *
     * @return the carriers with the delivery price.
     *             TODO Move to DelvieryHelper
     */
    public function addPricesToCarriers($deliveries, $curr_order)
    {
        $free_p = Tools::convertPrice(Configuration::get('PS_SHIPPING_FREE_PRICE'));
        $free_w = Configuration::get('PS_SHIPPING_FREE_WEIGHT');
        $free_w_max = Configuration::get('PS_SHIPPING_FREE_WEIGHT_MAX', null, null, null, 0);
        // Check order free delivery:
        $is_free = false;
        $handling = (float) Configuration::get('PS_SHIPPING_HANDLING');
        if ($handling == 0) {
            $is_free = $this->isFreeShipping($curr_order, $free_p, $free_w, $free_w_max);
        }
        $mode = [2 => 'price', 1 => 'weight', 0 => 'price'];
        $range_prices = $this->getCarriersRange('deliveries');
        $carr_ids = [];
        if (is_array($deliveries) && !empty($deliveries)) {
            foreach ($deliveries as $delivery) {
                if (isset($delivery->dc)) {
                    array_push($carr_ids, $delivery->dc->id_carrier);
                } else {
                    // Is a product without a delivery, do not try to add a delivery price
                    return $deliveries;
                }
            }
            $add_tax = !Group::getCurrent()->price_display_method;
            $taxes = $this->getCarriersTaxes($carr_ids);
            // Get only the tables needed.
            foreach ($deliveries as &$delivery) {
                if ($delivery->dc->is_free == 1 || $is_free) {
                    $delivery->price = 0;
                    if ($handling != 0 && $delivery->dc->is_free != 1 && $delivery->dc->shipping_handling != 0) {
                        $delivery->price = $handling;
                    }
                } else {
                    $type = $delivery->dc->shipping_method;
                    if (!isset($this->{$mode[$type] . 'Ranges'})) {
                        $this->{$mode[$type] . 'Ranges'} = $this->getCarriersRange($mode[$type]);
                    }
                    if (isset($this->{$mode[$type] . 'Ranges'}[$delivery->dc->id_carrier])) {
                        $delivery->id_range = $this->getIdRange($mode[$type], $curr_order[$mode[$type]], $this->{$mode[$type] . 'Ranges'}[$delivery->dc->id_carrier], $delivery->dc->range_behavior);
                        if ($delivery->id_range === false) {
                            unset($delivery);
                            continue;
                        }
                        if (isset($range_prices[$delivery->dc->id_carrier])) {
                            $delivery->price = $this->getCarrierFinalPrice($range_prices[$delivery->dc->id_carrier], $delivery->id_range, $mode[$type], $delivery->dc->shipping_handling ? $handling : 0);
                            if ($add_tax && (float) $delivery->price > 0 && isset($taxes[$delivery->dc->id_carrier]) && (int) $taxes[$delivery->dc->id_carrier] > 0) {
                                $delivery->price *= (1 + $taxes[$delivery->dc->id_carrier] / 100);
                            }
                        }
                    }
                }
                $delivery->price_cmp = $delivery->price;
                if ($delivery->is_free == 1 || $delivery->price == 0) {
                    $delivery->price = $this->l('Free!');
                } else {
                    $delivery->price = Tools::displayPrice(Tools::convertPrice($delivery->price), $this->context->currency, false, $this->context);
                }
            }
        }

        return $deliveries;
    }

    public function isFreeShipping($curr_order, $free_p, $free_w, $free_w_max)
    {
        if ($free_p > 0 || $free_w > 0) {
            if ($free_p > 0 && $curr_order['price'] < $free_p) {
                return false;
            }
            if ($free_w > 0) {
                if ($free_w_max > 0 && $curr_order['weight'] > $free_w_max) {
                    return false;
                }
                if ($curr_order['weight'] < $free_w) {
                    return false;
                }
            }

            return true;
        }
    }

    private function getCarriersRange($type)
    {
        if ($type == 'deliveries') {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'delivery` WHERE (id_carrier, id_zone) IN (SELECT id_carrier, id_zone FROM `' . _DB_PREFIX_ . 'carrier_zone`)';
        } else {
            $sql = 'SELECT id_carrier, id_range_' . $type . ', delimiter1, delimiter2 FROM ' . _DB_PREFIX_ . 'range_' . $type . ' AS tmp LEFT JOIN ' . _DB_PREFIX_ . 'carrier USING (id_carrier) WHERE active = 1';
            $sql .= ' ORDER BY delimiter1 ASC';
        }
        $results = DB::getInstance()->executeS(pSQL($sql));
        if (!empty($results)) {
            $return = [];
            foreach ($results as $result) {
                $return[$result['id_carrier']][] = $result;
            }

            return $return;
        } else {
            return false;
        }
    }

    private function getIdRange($type, $total, $ranges, $behavior)
    {
        $max_deli = $ranges[0];
        if ((float) $total < (float) $ranges[0]['delimiter1']) {
            if (self::$debug_mode) {
                $this->debugVar('Carrier below minimum range');
            }

            return false;
        }
        foreach ($ranges as $range) {
            if (!isset($max_deli['delimiter2']) || ($max_deli['delimiter2'] >= $range['delimiter2'])) {
                $max_deli = $range;
            }
            if ($total >= $range['delimiter1'] && $total <= $range['delimiter2']) {
                return $range['id_range_' . $type];
            }
        }
        if ($behavior == 0) {
            return $max_deli['id_range_' . $type];
        } else {
            return false;
        }
    }

    private function getCarrierFinalPrice($ranges, $id_range, $type, $handling)
    {
        if (!is_array($ranges) || empty($this->id_zone) || empty(array_filter($this->id_zone))) {
            return;
        }

        foreach ($ranges as $range) {
            if (!is_array($this->id_zone) && (int) $this->id_zone > 0) {
                $this->id_zone = [$this->id_zone];
            }
            foreach ($this->id_zone as $zone) {
                if (($range['id_range_' . $type] == $id_range) && ($range['id_zone'] == $zone['id_zone'])) {
                    // Check if there are multiple values
                    $price = $range['price'] + $handling;
                    if ((float) $price == 0.0) {
                        return $this->l('Free!');
                    } else {
                        return $price;
                    }
                }
            }
        }

        return false;
    }

    private function getCarriersTaxes($carr_ids)
    {
        if (!isset($this->id_country)) {
            $this->id_country = $this->context->country->id ?? Configuration::get('PS_COUNTRY_DEFAULT');
        }
        // ID state is set to 0 to get the default value for the country, countries with multiple taxes depending on the state should review this setting
        $sql = 'SELECT id_carrier, rate FROM  `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop` AS carr LEFT JOIN  `' . _DB_PREFIX_ . 'tax_rule` USING (id_tax_rules_group) LEFT JOIN `' . _DB_PREFIX_ . 'tax` USING (id_tax) WHERE id_country = ' . (int) $this->id_country . ' AND id_state = 0 AND id_shop = ' . $this->context->shop->id . ' AND carr.id_carrier IN (' . implode(',', $carr_ids) . ')';
        $results = DB::getInstance()->executeS(pSQL($sql));
        $ret = [];
        foreach ($results as $result) {
            $ret[$result['id_carrier']] = $result['rate'];
        }

        return $ret;
    }

    private function getIDCountryFromISO($country_iso)
    {
        return DB::getInstance()->getValue('SELECT id_country FROM ' . _DB_PREFIX_ . 'country WHERE iso_code LIKE "' . pSQL($country_iso) . '"');
    }

    /**
     * Join Arrays from DB
     */
    private function joinMultiArray($results, $keyvalue)
    {
        $cid = [];
        foreach ($results as $result) {
            if (is_array($keyvalue)) {
                foreach ($keyvalue as $k) {
                    $cid[] = $result[$k];
                }
            } else {
                $cid[] = $result[$keyvalue];
            }
        }

        return array_unique($cid);
    }

    private function buildDateFormatOptions($type)
    {
        // Visit https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
        // To see all available format dates

        if ($type == 'weekday') {
            return [
                'pattern' => 'EEEE',
                'intl' => true,
                'desc' => $this->l('Day of the week'),
            ];
        }
        $c = 0;
        $dateFormats = [
            ++$c => [
                'index' => $c,
                'intl' => true,
                'date_type' => 'LONG',
                'desc' => $this->l('Default long date format by locale'),
            ],
            ++$c => [
                'index' => $c,
                'intl' => true,
                'date_type' => 'SHORT',
                'desc' => $this->l('Default short date format by locale'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'd-m-Y',
                'desc' => $this->l('Day-Month-Year'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'd/m/Y',
                'desc' => $this->l('Day/Month/Year'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'd.m.Y',
                'desc' => $this->l('Day.Month.Year'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'E d',
                'intl' => true,
                'list' => true,
                'desc' => $this->l('Short weekday + day number'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'EEEE d',
                'intl' => true,
                'list' => true,
                'desc' => $this->l('Full weekday + day number'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'd MMM',
                'intl' => true,
                'list' => true,
                'desc' => $this->l('Day + Short Month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'dd/MM',
                'intl' => true,
                'list' => true,
                'desc' => $this->l('Day + Month number (with leading 0s)'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'E d MMMM',
                'intl' => true,
                'desc' => $this->l('Short weekday + day + month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'E, d MMMM',
                'intl' => true,
                'desc' => $this->l('Short weekday, + day + month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'E d. MMMM',
                'intl' => true,
                'desc' => $this->l('Short weekday + day. + month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'EEEE d MMMM',
                'intl' => true,
                'desc' => $this->l('Full Week day + Day + Month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'EEEE, d MMMM',
                'intl' => true,
                'desc' => $this->l('Full Week, day + Day + Month'),
            ],
            ++$c => [
                'index' => $c,
                'pattern' => 'EEEE d. MMMM',
                'intl' => true,
                'desc' => $this->l('Full Week day + Day. + Month'),
            ],
        ];
        if ($type == 'full') {
            $dateFormats[-1] = [
                'index' => -1,
                'intl' => true,
                'pattern' => Configuration::get('ED_DATE_CUSTOM'),
                'desc' => $this->l('Custom Date Format'),
            ];
            $dateFormats[-2] = [
                'index' => -2,
                'pattern' => Configuration::get('ED_DATE_CUSTOM_REGULAR'),
                'desc' => $this->l('Regular Date, Custom Format') . ' ' . $this->l('Use only this format for numeric dates'),
            ];
        }
        foreach ($dateFormats as $key => $df) {
            if ($type == 'list' && !isset($df['list'])) {
                unset($dateFormats[$key]);
            }
        }
        if ($type == 'list') {
            $dateFormats = array_values($dateFormats);
            $dateFormats[] = [
                'pattern' => 'EEEE',
                'list' => true,
                'intl' => true,
                'desc' => $this->l('Day of the week'),
            ];
            $dateFormats[] = ['desc' => $this->l('Hours format')];
            $dateFormats[] = ['desc' => $this->l('Days format')];
        }

        return $dateFormats;
    }

    private function loadDateFormats()
    {
        if (Configuration::hasKey('ED_LOCATION') !== false) {
            // Set Locale for outputting the date in current language
            $this->dateFormat = $this->buildDateFormatOptions('full');

            // To display the alerts in the product listings
            $this->listDateFormat = $this->buildDateFormatOptions('list');
            $special_df =
            $dateFormats = [
                'base_df' => $this->dateFormat[Configuration::get('ED_DATE_TYPE', null, null, null, 2)],
                'special_df' => $this->dateFormat[Configuration::get('ED_SPECIAL_DATE_FORMAT', null, null, null, 8)],
                'weekday_df' => $this->buildDateFormatOptions('weekday'),
                'email_df' => $this->dateFormat[Configuration::get('ED_EMAIL_DATE_FORMAT', null, null, null, 3)],
                'product_listing_df' => $this->listDateFormat[Configuration::get('ED_LIST_DATE_FORMAT', null, null, null, 0)],
            ];
            foreach ($dateFormats as $df => $format) {
                EDTools::setDateFormat($df, $format);
            }
        }
    }

    public function getDateFormat($type = 'base')
    {
        if ($type == 'list') {
            return $this->listDateFormat;
        }

        return $this->dateFormat;
    }

    /**
     * Get the TOT configuration
     * If the controller_type is not set, then it can be a webservice
     *
     * @return bool is TOT allowed?
     */
    public static function getTot()
    {
        if (isset(self::$tot)) {
            return self::$tot;
        }

        $context = Context::getContext();
        $controllerType = $context->controller->controller_type ?? null;

        if (!$controllerType || !in_array($controllerType, ['front', 'modulefront'])) {
            self::$tot = false;
        } else {
            $advancedModeEnabled = Configuration::get('ed_adv_mode');
            $useTotDisabled = Configuration::get('ED_USE_TOT');

            self::$tot = !($advancedModeEnabled && $useTotDisabled);
        }

        return self::$tot;
    }

    public static function getToday()
    {
        return self::$today;
    }

    public static function getTomorrow()
    {
        return self::$tomorrow;
    }

    /**
     * Adds the code for previewing the display
     */
    private function previewJS()
    {
        // Detect if locale is in UTF-8 format
        if ($this->getstrpos($this->locale, 'UTF-8') === false) {
            // Not UTF-8 Encoded
            $needEncode = true;
        } else {
            $needEncode = false;
        }
        $datemin_default = '';

        $date_list = [
            date('Y-m-d'),
            date('Y-m-d', strtotime('+1 day')),
            date('Y-m-d', strtotime('+2 day')),
            date('Y-m-d', strtotime('+3 day')),
            date('Y-m-d', strtotime('+4 day')),
        ];
        $default_index = (int) Configuration::get('ED_DATE_TYPE');
        $dates = [];
        foreach ($this->dateFormat as $df) {
            $dateFormat = EDTools::createDateFormat($df);
            foreach ($date_list as $date) {
                $dates[$df['index']][] = $dateFormat->format($date);
            }

            if ($df['index'] == $default_index) {
                $datemin_default = $dates[$df['index']][0];
                $datemax_default = $dates[$df['index']][2];
            }
        }
        $ed_class = 'estimateddeliverypreview ' . (Configuration::get('ed_class') != '' && Configuration::get('ed_class') != 'custom' ? Configuration::get('ed_class') : '');
        $currency = $this->context->currency;
        $sorting = [
            1 => $this->l('Fastest Carrier'),
            2 => $this->l('Cheapest carrier'),
            3 => $this->l('Recommended Carrier'),
        ];
        $this->context->smarty->assign(
            [
                'ed_class' => $ed_class,
                'ed_style' => Configuration::get('ED_STYLE'),
                'dates' => json_encode($dates),
                // 'datemin' => json_encode($datemin),
                // 'datemax' => json_encode($datemax),
                'datemin_default' => $datemin_default, // Dynamically defined
                'datemax_default' => $datemax_default, // Dynamically defined
                'price1' => Tools::displayPrice(Tools::convertPrice(5, $currency), $currency),
                'price2' => Tools::displayPrice(Tools::convertPrice(8, $currency), $currency),
                'sorting_title' => $sorting,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/ed-preview.tpl');
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookActionAdminControllerSetMedia()
    {
        SmartForm::init($this);
        // Get existing categories with delay and save them to use in tpl
        Media::addJsDef(
            [
                'days_text' => $this->l('days'),
                'methods' => ['picking' => 'picking_days', 'oos' => 'delay', 'custom' => 'customization_days'],
                'prefix' => 'ed_',
                'max_cat_allowed' => self::MAX_CAT_DISPLAY,
            ]
        );
        if (Tools::getValue('configure') === 'estimateddelivery') {
            $this->context->controller->addJS(basename(_PS_ADMIN_DIR_) . '/themes/default/js/tree.js');
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path . 'views/css/estimateddelivery-back-1.5.css');
            }
            $this->context->controller->addCSS($this->_path . 'views/css/estimateddelivery-back.css');
            $this->context->controller->addCSS($this->_path . 'views/css/font-awesome.css');
            $this->context->controller->addJS($this->_path . 'views/js/estimateddelivery-back.js');
            $this->context->controller->addJS($this->_path . 'views/js/tab-magic-menus.js');
            if (!$this->is_17) {
                $this->context->controller->addJqueryUI('ui.datepicker');
            }
        }
        if (Tools::getValue('controller') === 'AdminOrders' || Tools::getValue('controller') === 'AdminPickingList') {
            $this->context->controller->addCSS($this->_path . 'views/css/ed-orders-back.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin/ed-orders-back.js');
        }
        if (Tools::getValue('controller') === 'AdminOrders' && Configuration::get('ED_ORDER_BO_COLUMNS')) {
            $this->context->controller->addJS($this->_path . 'views/js/ed-order-unescape-columns.js');
        }
        if ($this->is_17 && strpos($_SERVER['PHP_SELF'], '/product') !== false) {
            $this->context->controller->addCSS($this->_path . 'views/css/ed-product-edit-17.css');
            $this->context->controller->addJS($this->_path . 'views/js/ed-product-edit-17.js');
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/ed-product-edit.css');
        }
    }

    /** Add a custom tab on product to add extra days to oos products (used for dropshipping like sites) */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = Tools::getValue('id_product');
        if ($id_product == '') {
            $id_product = $params['id_product'];
        }
        $p = new Product($id_product);
        if (Validate::isLoadedObject($p)) {
            $this->getProductOOS($id_product);
            $this->context->smarty->assign(['productid' => $id_product]);
        }
        $this->context->smarty->assign(
            [
                'old_ps' => version_compare(_PS_VERSION_, '1.6', '<'),
            ]
        );
        $this->addProductCombinationsForRestockDate($p);
        if (!$this->is_17) {
            return $this->display(__FILE__, 'views/templates/admin/estimateddelivery.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/estimateddelivery-1.7.tpl');
        }
    }

    private function addProductCombinationsForRestockDate($p)
    {
        $combi = $p->getAttributeCombinations($this->context->language->id);
        if (count($combi) > 0) {
            $list = [];
            foreach ($combi as $c) {
                if (!isset($list[$c['id_product_attribute']])) {
                    $list[$c['id_product_attribute']] = ['id' => $c['id_product_attribute'], 'picking_days' => '', 'delay' => '', 'release_date' => '', 'restock_date' => '', 'combiName' => ''];
                } else {
                    $list[$c['id_product_attribute']]['combiName'] .= ', ';
                }
                $list[$c['id_product_attribute']]['combiName'] .= $c['group_name'] . ': ' . $c['attribute_name'];
            }
            $list = $this->getProductCombinationsForRestockDate($list);
            $this->context->smarty->assign(['combiList' => $list]);
        }
    }

    private function getProductCombinationsForRestockDate($list)
    {
        if (!isset($this->shops)) {
            $this->setBoVars();
        }
        $ids = array_keys($list);
        $sql = 'SELECT id_product_attribute, delay, picking_days, release_date, restock_date, customization_days, disabled FROM ' . _DB_PREFIX_ . 'ed_prod_combi WHERE id_shop IN (' . implode(',', $this->shops) . ') AND id_product_attribute IN (' . implode(',', $ids) . ')';
        $results = Db::getInstance()->executeS(pSQL($sql));
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $id = $result['id_product_attribute'];
                unset($result['id_product_attribute']);
                $list[$id] = array_merge($list[$id], $result);
            }
        }

        return $list;
    }

    private function getProductOOS($id_product = null, $return = false)
    {
        if ($id_product == null) {
            $id_product = (int) Tools::getValue('id_product');
        }
        $sql = 'SELECT delay,release_date, picking_days, customization_days, disabled FROM ' . _DB_PREFIX_ . 'ed_prod WHERE id_product = ' . (int) $id_product . ' AND id_shop = ' . (int) $this->context->shop->id;

        $results = DB::getInstance()->executeS(pSQL($sql));
        if ($results === false) {
            $this->context->controller->errors[] = $this->l('There was an error while loading the data');
        }
        if (self::$debug_mode == true) {
            $this->debugVar($results, 'Product OOS results');
        }

        if (count($results) > 0) {
            if ($return === false) {
                $this->context->smarty->assign(
                    [
                        'ed_prod_oos' => $results[0]['delay'],
                        'ed_prod_release' => $results[0]['release_date'],
                        'ed_prod_picking' => $results[0]['picking_days'],
                        'ed_prod_custom_days' => $results[0]['customization_days'],
                        'ed_prod_dis' => (int) $results[0]['disabled'],
                    ]
                );
            } else {
                if (isset($results[0])) {
                    if (isset($results[0]['release_date']) && $results[0]['release_date'] != '') {
                        return $results[0];
                    } else {
                        return $results[0]['delay'];
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /*
    private function getCategoryOOS($id_category, $return = 'delay')
    {
        $sql = 'SELECT '.$return.' FROM '._DB_PREFIX_.'ed_cat WHERE id_category = '.(int)$id_category.' AND id_shop = '.(int)$this->context->shop->id;
        $results = DB::getInstance()->executeS(pSQL($sql));
        if (count($results) > 0) {
            return ($results[0][$return] != '' ? $results[0][$return] : false);
        }
    }*/

    /** Process the product saved **/
    public function hookActionProductUpdate($params)
    {
        if (!isset($this->shops)) {
            $this->setBoVars();
        }
        $id_product = (int) Tools::getValue('id_product');
        if (Tools::getIsset('estimateddelivery')) {
            $ed_data = Tools::getValue('estimateddelivery');
        }
        $combi_data = [];
        if ($this->is_17) {
            if (isset($ed_data)) {
                if ($ed_data === false) { // WAS } || empty(array_filter($ed_data))) {
                    $data = [
                        'disabled' => 0,
                    ];
                } else {
                    $data = [
                        'delay' => ($ed_data['ed_prod_oos'] != '' ? (int) $ed_data['ed_prod_oos'] : 0),
                        'id_shop' => 0,
                        'release_date' => $ed_data['ed_prod_release'],
                        'picking_days' => (int) $ed_data['ed_prod_picking'],
                        'customization_days' => (int) $ed_data['ed_prod_custom_days'],
                        'disabled' => isset($ed_data['ed_prod_dis']) && (int) $ed_data['ed_prod_dis'] > 0 ? (int) $ed_data['ed_prod_dis'] : 0,
                    ];
                }
                $data['id_product'] = $id_product;
            } else {
                return;
            }
        } else {
            if (Tools::getValue('fc') == 'module' && Tools::getValue('module') == 'productcomposer') {
                return;
            }
            $data = [
                'delay' => (Tools::getValue('ed_prod_oos') != '' ? (int) Tools::getValue('ed_prod_oos') : 0),
                'id_product' => $id_product,
                'id_shop' => $this->shops[0],
                'release_date' => Tools::getValue('ed_prod_release'),
                'picking_days' => (int) Tools::getValue('ed_prod_picking'),
                'customization_days' => (int) Tools::getValue('ed_prod_custom_days'),
                'disabled' => (int) Tools::getValue('ed_prod_dis'),
            ];
        }
        if (isset($ed_data) && is_array($ed_data) && isset($ed_data['combi'])) {
            // Fix missing disabled status for combination when not disabled
            $ed_data['combi'] = $this->normalizeDisabledCombis($ed_data['combi']);
            $combi_data = $this->getCombiData($id_product, $ed_data['combi']);
        }
        if (isset($data)) {
            foreach ($this->shops as $id_shop) {
                $exists = $this->checkProdctOOS($id_product, $id_shop);
                $data['id_shop'] = $id_shop;
                if (empty($exists) || $exists === false) {
                    if (!Db::getInstance()->insert('ed_prod', $data)) {
                        $this->context->controller->errors[] = Tools::displayError('Error: ') . Db::getInstance()->getNumberError() . Db::getInstance()->getMsgError();
                    }
                } else {
                    $where = ' id_product = ' . $id_product;
                    if ($id_shop != '' && !empty($id_shop) && _PS_VERSION_ >= '1.5') {
                        $where .= ' AND id_shop = ' . (int) $id_shop;
                    }
                    if (!Db::getInstance()->update('ed_prod', $data, $where)) {
                        $this->context->controller->errors[] = Tools::displayError('Error: ') . Db::getInstance()->getNumberError() . Db::getInstance()->getMsgError();
                    }
                }
                if (count($combi_data) > 0) {
                    $combi = $this->addShopToCombi($combi_data, $id_shop);
                    $columns = ['id_product', 'id_product_attribute', 'picking_days', 'delay', 'customization_days', 'disabled', 'restock_date', 'release_date', 'id_shop'];
                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_prod_combi (' . implode(',', $columns) . ') VALUES (' . implode('),(', $combi) . ') ON DUPLICATE KEY UPDATE picking_days = VALUES(picking_days), delay = VALUES(delay), restock_date = VALUES(restock_date), release_date = VALUES(release_date), customization_days = VALUES(customization_days), disabled = VALUES(disabled)';
                    Db::getInstance()->execute($sql);
                }
            }
        }
    }

    private function handleProductCacheClear($id_product)
    {
        // Get product combinations
        $productCombinations = $this->getProductIDCombinationsPairs($id_product);

        // Add the base product itself to the list
        $productData = array_merge(
            [['id_product' => $id_product, 'id_product_attribute' => 0]],  // Product without combinations
            $productCombinations                                           // Product with combinations
        );

        // Get all languages
        $id_langs = Language::getLanguages(true, false, true);

        // Clear cache for all product combinations and languages
        // $this->clearCache('DeliveryProduct', $productData, $id_langs);
    }

    /**
     * Get the combinaion pairs (id_product, id_product_attribute
     *
     * @param $id_product
     *
     * @return array|bool|mysqli_result|PDOStatement|resource|null
     */
    private function getProductIDCombinationsPairs($id_product)
    {
        $sql = 'SELECT id_product, id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . (int) $id_product;

        return Db::getInstance()->executeS($sql);
    }

    public function hookActionProductSave($params)
    {
        $id_product = (int) $params['id_product'];
        // $this->handleProductCacheClear($id_product);
    }

    public function hookActionProductDelete($params)
    {
        $id_product = (int) $params['id_product'];
        // $this->handleProductCacheClear($id_product);
    }

    public function hookActionProductAttributeUpdate($params)
    {
        $id_product = (int) $params['id_product'];
        $id_product_attribute = (int) $params['id_product_attribute'];

        // Only the specific combination (product attribute) is affected, so we pass it directly
        $productData = [['id_product' => $id_product, 'id_product_attribute' => $id_product_attribute]];

        // Get all languages
        // $id_langs = Language::getLanguages(true, false, true);

        // Clear cache for this product attribute and all languages
        // $this->clearCache('DeliveryProduct', $productData, $id_langs);
    }

    private function normalizeDisabledCombis($combis)
    {
        foreach ($combis as &$combi) {
            $combi_disabled = (int) isset($combi['disabled']);
            unset($combi['disabled']);
            $combi = array_slice($combi, 0, 3, true) + ['disabled' => $combi_disabled] + array_slice($combi, 3, count($combi) - 1, true);
        }

        return $combis;
    }

    private function addShopToCombi($combi, $id_shop)
    {
        $c = count($combi);
        for ($i = 0; $i < $c; ++$i) {
            $combi[$i] .= ',' . (int) $id_shop;
        }

        return $combi;
    }

    private function getCombiData($id_product, $combi)
    {
        $return = [];
        if (!empty($combi)) {
            foreach ($combi as $id => $params) {
                $tmp = (int) $id_product . ',' . (int) $id;
                $c = 0;
                foreach ($params as $field) {
                    if ($c < 4) {
                        $tmp .= ',' . (int) $field;
                    } else {
                        $tmp .= ',' . (DeliveryHelper::validateDate($field) ? '"' . pSQL($field) . '"' : 'NULL');
                    }
                    ++$c;
                }
                $return[] = $tmp;
            }
        }

        return $return;
    }

    private function checkProdctOOS($id_prdoduct, $id_shop)
    {
        return DB::getInstance()->executeS(pSQL('SELECT * FROM ' . _DB_PREFIX_ . 'ed_prod WHERE id_product = ' . (int) $id_prdoduct . ' AND id_shop = ' . (int) $id_shop));
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader($params)
    {
        if (!$this->is_active) {
            return false;
        }
        if (method_exists('SmartForm', 'hasInit') && !SmartForm::hasInit()) {
            SmartForm::init($this);
        }
        if (isset($this->context->controller) && !$this->front_media_set) {
            // Should only trigger in earlier 1.6 versions
            $this->hookActionFrontControllerSetMedia();
        }
        if ($this->adv_mode) {
            // Check the TimeZone and update if necessary
            $new_timezone = Configuration::get('ED_DEFAULT_TIMEZONE');
            if ($new_timezone != '' && (date_default_timezone_get() !== $new_timezone)) {
                date_default_timezone_set($new_timezone);
            }
            // Check for the use of Today and Tomorrow words
            // $this->tot = !Configuration::get('ED_USE_TOT');
        }
        if (self::$debug_mode == true) {
            $this->debugVar('', 'START ESTIMATED DELIVERY');
        }
        $output = '';
        // Add the Scripts and CSS files
        if ($this->fo_media_set == false) {
            $this->hookActionFrontControllerSetMedia();
        }

        // Check PS Version to avoid conflict with AddJsDef function
        $this->context->smarty->assign(
            [
                'is_17' => (int) $this->is_17,
                // 'ed_has_combi' => $p->hasAttributes(),
                'ed_refresh_delay' => Configuration::get('ed_refresh_delay'),
                'ed_placement' => Configuration::get('ED_LOCATION'),
                'ed_custom_selector' => Configuration::get('ED_LOCATION_SEL'),
                'ed_custom_ins' => Configuration::get('ED_LOCATION_INS'),
                'ed_sm' => Configuration::get('PS_STOCK_MANAGEMENT'),
                'edclass' => Configuration::get('ed_class'),
                'edbackground' => Configuration::get('ed_custombg'),
                'edborder' => Configuration::get('ed_customborder'),
                'edstyle' => Configuration::get('ED_STYLE'),
                'ed_cart_modal' => (int) Configuration::get('ed_cart_modal'),
                'front_ajax_url' => $this->context->link->getModuleLink('estimateddelivery', 'AjaxRefresh', ['token' => Configuration::get('ED_AJAX_TOKEN'), 'ajax' => true]),
                'front_ajax_cart_url' => $this->context->link->getModuleLink('estimateddelivery', 'AjaxCart', ['token' => Configuration::get('ED_AJAX_TOKEN'), 'ajax' => true]),
                'ps_version' => ($this->is_17) ? 17 : 16,
                'ed_display_option' => Configuration::get('ED_ORDER_SUMMARY'),
                'ed_amp' => $this->adv_mode && Configuration::get('ED_AMP'),
            ]
        );
        if ($this->adv_mode && Configuration::get('ED_AMP')) {
            if (method_exists('Context', 'isMobile')) {
                $this->mobile_device = $this->context->isMobile() || $this->context->isTablet();
            } else {
                $this->mobile_device = $this->isMobileOrTablet();
            }
            if ($this->mobile_device) {
                $output .= $this->display(__FILE__, 'views/templates/front/amp-js.tpl');
            }
        }
        $output .= $this->display(__FILE__, 'views/templates/front/js-vars.tpl');

        // Get current user group
        $this->user_group = Group::getCurrent();
        $this->getEDCarriers($params['cart']);

        return $output;
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->is_active && isset($this->context->controller) && !$this->front_media_set) {
            //            echo '<!-- ***** '.$this->controllerName.' vs '.$this->context->controller->php_self.' -->';
            if (!$this->isAjax()) {
                $this->front_media_set = true;
            }
            if (file_exists(dirname(__FILE__) . 'views/css/')) {
                $path = $this->_path;
            } else {
                $path = dirname(__FILE__) . '/';
            }
            $controller = $this->controllerName;
            $orders = ['orderopc', 'order-opc', 'order', 'order-step', 'supercheckout', 'cart', 'checkout', 'default'];

            // Added searchresults to add compatibility with Advanced Search 4 module
            $product_lists = ['product', 'index', 'search', 'searchresults', 'category', 'manufacturer', 'price-drop', 'prices-drop', 'pricesdrop', 'new-products', 'newproducts', 'best-sales', 'bestsales', 'jolisearch'];

            $all_available = array_merge($product_lists, $orders, ['order-confirmation', 'orderconfirmation', 'orderdetail', 'order-history', 'history']);

            if (Configuration::get('ed_cart_modal')) {
                $this->context->controller->addJS($this->_path . 'views/js/ed-cart-modal.js');
            }

            if (in_array($controller, $all_available) || Configuration::get('ED_LOCATION') == 50) {
                $this->context->controller->addCSS($path . 'views/css/estimateddelivery.css');
                if (!(Configuration::get('ed_adv_mode') && Configuration::get('ed_disable_font_awesome'))) {
                    $this->context->controller->addCSS($path . 'views/css/font-awesome.css');
                }
                if ($this->is_17) {
                    $this->context->controller->addJS($this->_path . 'views/js/ed-product-placement.js');
                }
                $controller_group = '';
                if (in_array($controller, $orders)) {
                    $controller_group = 'order';
                } elseif (in_array($controller, $product_lists)) {
                    $controller_group = 'products';
                } elseif (in_array($controller, $all_available)) {
                    $controller_group = 'order-history';
                }
                $this->context->smarty->assign(
                    [
                        'ed_tooltip' => Configuration::get('ed_tooltip'),
                        'ed_path' => $this->_path,
                        'ed_controller' => $controller_group,
                    ]
                );
                if (in_array(Configuration::get('ED_STYLE'), [2, 3])) {
                    $this->context->controller->addJS($path . 'views/js/ed_countdown.js');
                    $cd_limit = Configuration::get('ED_COUNTDOWN_LIMIT');
                    if ($cd_limit != '' && (int) $cd_limit > 0) {
                        $this->context->smarty->assign(['ed_countdown_limit' => Configuration::get('ED_COUNTDOWN_LIMIT')]);
                    }
                }
                if ($controller == 'product') {
                    if (!$this->is_17) {
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery.js');
                    }
                    if (Configuration::get('ED_DISPLAY_POPUP_CARRIERS')) {
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery-popup.js');
                    }
                    if (Module::isEnabled('pm_advancedpack')) {
                        $this->context->controller->addJS($path . 'views/js/modules/pm_advancedpack.js');
                    }
                } elseif ($controller_group == 'order' && Configuration::get('ED_ORDER')) {
                    if (Configuration::get('ED_ORDER_SUMMARY')) {
                        $this->context->controller->addJS($path . 'views/js/cart/ed-cart-update-checker.js');
                    }
                    if (Tools::getIsset('step') && (int) Tools::getValue('step') == 0 && in_array(Configuration::Get('ED_ORDER_SUMMARY'), [1, 2])) {
                        // It's the cart resume
                        $this->context->controller->addJS($path . 'views/js/ed_shopping_footer.js');
                    } elseif (Configuration::get('ED_ORDER_TYPE') == 0) {
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery-cart-line.js');
                    } else {
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery-cart.js');
                    }
                    if ($this->willDisplayCalendar()) {
                        $this->context->smarty->assign([
                            'willDisplayCalendar' => true,
                        ]);
                        $this->context->controller->addCSS($path . 'views/css/ed-cart.css');
                        $lang = $this->context->language->language_code;
                        // If it's available add the datepicker plugin
                        if (method_exists('FrontController', 'addJqueryUI')) {
                            $this->context->controller->addJqueryUI('ui.datepicker');
                        }

                        if ($this->is_17) {
                            $this->context->controller->registerJavascript(
                                $this->name . 'datepicker-lang',
                                'js/jquery/ui/i18n/jquery.ui.datepicker-' . $lang . '.js',
                                ['position' => 'bottom', 'priority' => 1100]
                            );
                            $this->context->controller->registerJavascript(
                                $this->name . 'ed-cart',
                                'modules/' . $this->name . '/views/js/ed-cart.js',
                                ['position' => 'bottom', 'priority' => 1150]
                            );
                        } else {
                            $this->context->controller->addJS(_PS_CORE_DIR_ . '/js/jquery/ui/i18n/jquery.ui.datepicker-' . $lang . '.js');
                            $this->context->controller->addJS($path . 'views/js/ed-cart.js');
                        }
                    }
                    if (Configuration::get('ED_CALENDAR_DISPLAY_CARTFOOTER') == 'on') {
                        // Add Calendar Refresh JS into the pages if the calendar is enabled
                        $this->context->controller->addJS($this->_path . 'views/js/ed-calendar-refresh.js');
                    }
                    // Update the ED if the individual product delivery hook has been set and it's a compatible OPC module
                    if ($controller == 'order' && Configuration::get('ED_ORDER_SUMMARY_PRODUCT') && Module::isEnabled('onepagecheckoutps')) {
                        $this->context->controller->addJS($path . 'views/js/order/ed-opc-checkout.js');
                    }
                } elseif (in_array($controller, $product_lists) && $controller != 'product') {
                    if ($this->is_17) {
                        $this->context->controller->addCSS($path . 'views/css/estimateddelivery-pl-17.css');
                    }
                    $this->context->controller->addJS($path . 'views/js/estimateddelivery-pl.js');
                    if (Configuration::get('ED_DISPLAY_POPUP_CARRIERS')) {
                        $this->context->smarty->assign(
                            [
                                'ed_popup_options' => [
                                    'name' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_NAME'),
                                    'desc' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_DESC'),
                                    'img' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_IMG'),
                                    'price' => Configuration::get('ED_DISPLAY_POPUP_CARRIERS_PRICE'),
                                ],
                                'ed_popup_background' => Configuration::get('ED_DISPLAY_POPUP_BACKGROUND'),
                            ]
                        );
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery-popup.js');
                    }
                } elseif ($controller == 'order-confirmation') {
                    $this->context->controller->addJS($path . 'views/js/ed-order-confirmation.js');
                }
                if ($controller_group == 'products' || $controller_group == 'order') {
                    Media::addJsDef(['ed_ajax_delay' => Configuration::get('ed_ajax_delay')]);
                    if ($this->is_17) { // && $controller == 'product') {
                        $this->context->controller->addJS($path . 'views/js/estimateddelivery-1.7.js');
                    }
                }
                // Add the modal JS to all files, just in case any product has a list of products and we need to interact with the
                $this->context->controller->addJS($path . 'views/js/ed-product-placement-modal.js');
            }

            $this->getSpecialMessages();
            $this->fo_media_set = true;
        }
    }

    public function hookCustomEdJsVars()
    {
        $output = '';
        $orders = ['orderopc', 'order-opc', 'order', 'order-step', 'supercheckout', 'cart', 'checkout', 'default'];
        $product_lists = ['product', 'index', 'search', 'category', 'manufacturer', 'price-drop', 'new-products', 'prices-drop', 'best-sales'];
        $all_available = array_merge($product_lists, $orders, ['order-confirmation', 'orderdetail', 'order-history', 'history']);
        $controller = $this->controllerName;
        if (in_array($controller, $orders)) {
            $controller_group = 'order';
        } elseif (in_array($controller, $product_lists)) {
            $controller_group = 'products';
        } elseif (in_array($controller, $all_available)) {
            $controller_group = 'order-history';
        }
        if (isset($controller_group)) {
            $this->context->smarty->assign(
                [
                    'ed_tooltip' => Configuration::get('ed_tooltip'),
                    'ed_path' => $this->_path,
                    'ed_controller' => $controller_group,
                    'isAmp' => Configuration::get($this->prefix . 'AMP'),
                ]
            );
        }

        if ($this->adv_mode && Configuration::get('ED_AMP')) {
            if (method_exists('Context', 'isMobile')) {
                $this->mobile_device = $this->context->isMobile() || $this->context->isTablet();
            } else {
                $this->mobile_device = $this->isMobileOrTablet();
            }
            if ($this->mobile_device) {
                $output .= $this->display(__FILE__, 'views/templates/front/ed-amp-css.tpl');
            }

            if (in_array(Configuration::get('ED_STYLE'), [2, 3])) {
                $cd_limit = Configuration::get('ED_COUNTDOWN_LIMIT');
                if ($cd_limit != '' && (int) $cd_limit > 0) {
                    $this->context->smarty->assign(['ed_countdown_limit' => Configuration::get('ED_COUNTDOWN_LIMIT')]);
                    $output .= $this->display(__FILE__, 'views/templates/front/ed-amp-js.tpl');
                }
            }
        }

        return $output;
    }

    /**
     * Public function to set up the carriers by type
     */
    public function getEDAvailableCarriers($cart)
    {
        return $this->getEDCarriers($cart);
    }

    /**
     * Set the variables with the available carriers
     */
    private function getEDCarriers($cart)
    {
        $this->setBetterLocales();
        if ($this->id_carriers === false && Tools::getIsset('id_carrier') && (int) Tools::getValue('id_carrier') > 0) {
            $this->id_carriers = $this->getCarriersFromIds([(int) Tools::getValue('id_carrier')], true);

            return;
        }
        $this->addr_carriers = $this->getAddrCarriers($cart);
        if (!empty($this->addr_carriers)) {
            return;
        }
        $this->ip_carriers = $this->getIpCarriers();
    }

    private function checkLanguageLocales()
    {
        $output = '';
        $langs = Language::getLanguages(true);
        $missing_loc = [];
        foreach ($langs as $lang) {
            if ($this->setBetterLocales($lang['id_lang']) === false) {
                if (isset($lang['locale'])) {
                    $locale = $lang['locale'];
                } elseif (isset($lang['language_code'])) {
                    $locale = $lang['language_code'];
                } else {
                    $locale = $lang['iso_code'];
                }
                $missing_loc[] = [$lang['name'], $locale];
            }
        }
        if (count($missing_loc) > 0 && !Configuration::get('ed_dismiss_locale_check')) {
            $output = SmartForm::genDesc($this->l('Some %s are missing'), 'h4', null, ['Locales']) .
                SmartForm::genDesc($this->l('The %s are a special translation file hosted in the servers that have the purpose of generating the dates.'), 'strong', null, ['Locales']) .
                SmartForm::genDesc('', '', 'br') .
                sprintf($this->l('The module uses the %s to translate the weekdays and months.'), 'Locales') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc('', '', 'br') .
                $this->l('They can also allow local translations for languages with multiple regions, so the output is always the best for a specific language.') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc('', '', 'br') .
                sprintf($this->l('The Estimated Delivery module always performs a check to see if any %s is missing. If it\'s the case, the server will try to generate the dates with the default language (usually English) and this can lead to some unwanted results on the Front Office.'), 'Locales') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('After checking it, the module has found those %s missing in your server:'), 'u', null, ['Locales']) .
                SmartForm::genDesc('', '', 'br');
            $list = [];
            foreach ($missing_loc as $l) {
                $list[] = $l[0] . ' >> (' . $l[1] . ')';
            }
            $output .= SmartForm::genList($list, 'ul');
            $output .= SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('The detection system is accurate but it may generate false positives'), 'strong', 'br') .
                $this->l('With that in mind, we recommend you to check the generated dates for each language detected.') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('If they are fine you can click on the bottom at the end to permanently dismiss this message'), 'em', 'br') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('If the month or weekday aren\'t translated, then you will need to add the missing %s'), 'strong', 'br', ['Locale/s']) .
                SmartForm::genDesc($this->l('There are two ways to fix this:'), 'u', 'br') .
                SmartForm::genDesc($this->l('The easiest one is to ask your hosting to install the missing %s, it just takes a few minutes and they usually do it fast.'), '', 'br', ['Locales']) .
                SmartForm::genDesc($this->l('The other one, if you have a SSH access is to install them by yourself by using the following commands on the console:'), '', 'br', ['Locales']);
            $list = [];
            foreach ($missing_loc as $l) {
                $list[] = 'sudo locale-gen ' . $l[1];
                $list[] = 'sudo locale-gen ' . $l[1] . '.UTF-8';
            }
            $output .= SmartForm::genList($list, 'ul') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('NOTE: In most of the cases, the apache has to be restarted after adding it to be able to apply the changes'), 'em', 'br');
            $output .= SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('Permanently dismiss this message'), 'strong') .
                SmartForm::genDesc('', '', 'br') .
                SmartForm::genDesc($this->l('The locales are ok, dismiss the message'), ['button', 'class="btn btn-primary dismiss-locale-check"']); // Add data-dismiss="alert" to automatically close the message when clicking
        }
        // Set the default language locales
        $this->setBetterLocales($this->context->language->id);

        return !empty($output) ? $this->displayWarning($output) : '';
    }

    public function setBetterLocales($id_lang = 0)
    {
        return true;
        $locales = [];
        if ((int) $id_lang > 0) {
            $locale = new Language((int) $id_lang);
            if (isset($locale->locale)) {
                $locale = $locale->locale;
            } elseif (isset($locale->language_code)) {
                $locale = $locale->language_code;
            } else {
                $locale = $locale->iso_code;
            }
        } else {
            // Set the locales once, if they need to be re-set
            if (Configuration::get('ed_adv_mode') && Configuration::get('ed_force_locale') && Configuration::get('ed_locale_' . $this->context->language->id) != '0') {
                $locale = Configuration::get('ed_locale_' . $this->context->language->id);
            } else {
                $locale = $this->context->language->language_code;
            }
        }
        // Get locale from context and make sure it's in correct format
        $locale = str_replace('-', '_', $locale);
        $locale = explode('_', $locale);
        if (!isset($locale[1])) {
            if ($locale[0] == 'en') {
                $locale[1] = 'us';
            } else {
                $locale[1] = $locale[0];
            }
        }
        $locale[1] = Tools::strtoupper($locale[1]);
        $locales = [
            $locale[0] . '_' . $locale[1] . '.UTF-8',
            $locale[0] . '-' . $locale[1] . '.UTF-8',
            $locale[0] . '_' . $locale[1],
            $locale[0] . '-' . $locale[1],
            $locale[0],
        ];
        // Set locale to display dates in local language
        setlocale(LC_TIME, '');
        $this->locale = setlocale(LC_TIME, $locales);

        return (bool) $this->locale;
    }

    private function isPack($id_product)
    {
        if (Pack::isPack((int) $id_product) || (class_exists('AdvancedPack') && AdvancedPack::isValidPack((int) $id_product))) {
            return true;
        }

        return false;
    }

    private function getPackProducts($id_product, $id_product_attribute)
    {
        if (Pack::isPack($id_product)) {
            // TODO Rewiew if pack works well
            $pack = Pack::getItems($id_product, $this->context->language->id);
            foreach ($pack as &$product) {
                $product = (array) $product;
                $product['id_product'] = $product['id'];
                $product['id_product_attribute'] = $product['id_pack_product_attribute'];
                $product['product_stock'] = $product['quantity'] = $product['pack_quantity'];
            }
        } elseif (class_exists('AdvancedPack') && AdvancedPack::isValidPack($id_product)) {
            if ($id_product_attribute > 0) {
                $pack_data = json_decode($id_product_attribute, true);
                if (is_array($pack_data)) {
                    $pack_data = ['id_pack' => key($pack_data), 'id_product_attribute' => array_pop($pack_data)];
                } elseif (Tools::getIsset('productPackChoice')) {
                    foreach (Tools::getValue('productPackChoice') as $data) {
                        $tmp_attribute = AdvancedPack::combinationExists($data['idProductPack'], $data['attributesList']);
                        if ($tmp_attribute !== false) {
                            // The first item contains the id_product_attribute
                            $pack_data = ['id_pack' => $data['idProductPack'], 'id_product_attribute' => $tmp_attribute[0]];
                            break;
                        }
                    }
                } else {
                    $pack_data = false;
                }
            }
            // ID product to search the product
            // Use $pack_data to set the id_product_attribute for the current product
            $pack = AdvancedPack::getPackContent($id_product);
            foreach ($pack as &$product) {
                $product['id_product_attribute'] = 0;
                if ($pack_data && isset($pack_data['id_pack']) && $product['id_product_pack'] == $pack_data['id_pack']) {
                    $product['id_product_attribute'] = $pack_data['id_product_attribute'];
                }
                $qw = (int) Tools::getValue('quantity_wanted') == 0 ? 1 : (int) Tools::getValue('quantity_wanted');
                // $p = new Product($product['id_product']);
                $product['product_stock'] = $product['quantity'] * $qw;
                $product['quantity'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);
                // WAS StockAvailable::getQuantityAvailableByProduct($id_product, $product['default_id_product_attribute']);
                // AFTER WAS $product['quantity'] = $p->quantity;
            }
        }

        return $pack;
    }

    private function getPackTotals($products)
    {
        $total_p = $total_w = 0;
        foreach ($products as $product) {
            if (isset($product['price'])) {
                $total_p += $product['price'] * $product['quantity'];
            }
            if (isset($product['weight'])) {
                $total_w += $product['weight'] * $product['quantity'];
            }
        }

        return ['price' => $total_p, 'weight' => $total_w];
    }

    public function hookDisplayRightColumnProduct($params)
    {
        if ($this->is_active && !$this->is_17 && Configuration::get('ED_LOCATION') == 1) {
            return $this->generateEstimatedDelivery($params);
        }
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        if ($this->is_active && !$this->is_17 && Configuration::get('ED_LOCATION') == 2) {
            return $this->generateEstimatedDelivery($params);
        }
    }

    public function hookDisplayProductFooter($params)
    {
        if ($this->is_active && Configuration::get('ED_LOCATION') == 3) {
            return $this->generateEstimatedDelivery($params);
        }
    }

    public function hookDisplayProductTab()
    {
        if ($this->is_active && Configuration::get('ED_LOCATION') == 4) {
            return SmartForm::openTag('li') .
                SmartForm::genDesc($this->l('Estimated Delivery Time'), ['a', 'href="#idTab321" class="idTabHrefShort" data-toggle="tab"']) .
                SmartForm::closeTag('li');
        }
    }

    public function hookDisplayProductTabContent($params)
    {
        if ($this->is_active && Configuration::get('ED_LOCATION') == 4) {
            return SmartForm::openTag('div', 'id="idTab321"') . $this->generateEstimatedDelivery($params) . SmartForm::closeTag('div');
        }
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        if ($this->is_active && $this->is_17 && ($this->isDisplayed == false || $this->isAjax()) && Configuration::get('ED_LOCATION') >= 0) {
            // New 1.7 versions have updated the name for the  display product buttons hook
            if (!$this->isAjax() && !Configuration::get('ED_ALLOW_MULTIPLE_INSTANCES')) {
                $this->isDisplayed = true;
            }

            return $this->generateEstimatedDelivery($params);
        }
    }

    public function hookDisplayShoppingCart($params)
    {
        if ($this->is_active && Configuration::get('ED_ORDER')) {
            $displaySummary = Configuration::get('ED_ORDER_SUMMARY') == 1;
            $displayCalendar = $this->willDisplayCalendar('ED_CALENDAR_DISPLAY_CART');
            if ($displaySummary || $displayCalendar) {
                return $this->displayEdOnCartSummary($params, $displaySummary, $displayCalendar);
            }
        }
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        if ($this->is_active) {
            $displaySummary = Configuration::get('ED_ORDER_SUMMARY') == 2;
            $displayCalendar = $this->willDisplayCalendar('ED_CALENDAR_DISPLAY_CARTFOOTER');
            if ($displaySummary || $displayCalendar) {
                return $this->displayEdOnCartSummary($params, $displaySummary, $displayCalendar);
            }
        }
    }

    private function displayEdOnCartSummary($params, $displaySummary, $displayCalendar)
    {
        /* Exit if it's already displayed */
        if ($this->is_displayed_summary && $this->is_displayed_calendar) {
            return false;
        }
        if ($displaySummary) {
            $this->is_displayed_summary = true;
        }
        if ($displayCalendar) {
            $this->is_displayed_calendar = true;
        }

        return $this->displayCarriersOnCart($params, $displaySummary, $displayCalendar);
    }

    // TODO Review HR
    public function hookDisplayPaymentTop($params)
    {
        if ($this->is_active && Configuration::get('ED_ORDER') && $this->is_17 && Configuration::get('ED_CALENDAR_DISPLAY_PAYMENT') == 'on') {
            return $this->displayCarriersOnCart($params, false, true);
        }
    }

    /**
     * Displays the calendar to choose the delivery date, only if the feature is enabled
     *
     * @param object
     *
     * @display The calendar to pick a delivery day
     */
    public function hookDisplayEstimatedDeliveryCalendar($params)
    {
        if ($this->is_active && Configuration::get('ED_ORDER') && Configuration::get('ED_CALENDAR_DISPLAY_HOOK') == 'on') {
            return $this->displayCarriersOnCart($params);
        }
    }

    public function hookDisplayBeforeCarrier($params)
    {
        if ($this->is_active && !$this->is_17 && Configuration::get('ED_ORDER')) {
            return $this->displayCarriersOnCart($params);
        }
    }

    public function hookDisplayAfterCarrier($params)
    {
        if ($this->is_active && $this->is_17 && Configuration::get('ED_ORDER')) {
            $displayCalendar = $this->willDisplayCalendar('ED_CALENDAR_DISPLAY_CARRIERS');

            return $this->displayCarriersOnCart($params, false, $displayCalendar);
        }
    }

    public function displayCarriersOnCart($params = [], $summary = false, $displayCalendar = false)
    {
        if (!$summary && !$displayCalendar && Configuration::get('ED_ORDER_TYPE') == -1) {
            return;
        }
        $controller = $this->context->controller->php_self;
        if ($controller == '') {
            $controller_vars = get_object_vars($this->context->controller);
            if (isset($controller_vars->module->name)) {
                $controller = $controller_vars->module->name;
            } elseif (isset($controller_vars->name)) {
                $controller = $controller_vars->name;
            }
        }
        if ($this->isAjax()) {
            $this->setBetterLocales();
        }
        if (empty($params)) {
            $params['cart'] = $this->context->cart;
        }
        // Get the carrier ids and set them to the id_carriers variable
        $this->getCarriersFromCart($params);
        if (empty($this->id_carriers)) {
            return false;
        }

        // Check if there is a carrier selected, if there is move it to first position
        $sel_carrier = 0;
        if (isset($params['cart'])) {
            if (is_object($params['cart'])) {
                $sel_carrier = $params['cart']->id_carrier;
            } else {
                $sel_carrier = $params['cart']['id_carrier'];
            }
        }

        if ($sel_carrier == 0 && Tools::getIsset('id_carrier')) {
            $sel_carrier = (int) Tools::getValue('id_carrier');
            $sel_carrier = Cart::desintifier($sel_carrier, '');
        }

        $products = $this->context->cart->getProducts();

        // Make sure gifts doesn't interfere with ED
        if (empty($products)) {
            return false;
        }

        if (!Configuration::get('ED_PACK_AS_PRODUCT')) {
            foreach ($products as $key => $product) {
                if ($this->isPack($product['id_product'])) {
                    $packProducts = $this->getPackProducts($product['id_product'], $product['id_product_attribute']);

                    foreach ($packProducts as $packProduct) {
                        $index = $this->findProductIndex($products, $packProduct['id_product'], $packProduct['id_product_attribute']);

                        if ($index !== null) {
                            // Increase the quantity if the product already exists
                            $products[$index]['quantity'] += $packProduct['quantity'];
                        } else {
                            // Add the whole product if it does not exist
                            $products[] = $packProduct;
                        }
                    }

                    unset($products[$key]);
                }
            }
        }

        // Reindex the products array to ensure no gaps after unsetting elements
        $products = array_values($products);
        $products = $this->removeGifts($products);
        $both_deliveries = [false, false];
        $relandavail = [];
        $deliveries = $this->getDeliveriesFromProductList($params, $products, $both_deliveries, $relandavail, null, null, false); // is_order was true
        // for the customization days

        //        if (isset($deliveries[0]) && is_array($deliveries[0]) && reset($deliveries[0]) instanceof EDelivery) {
        //            $deliveries = $deliveries[0];
        //        }
        $this->checkConditionForCustomization($params);

        if ($deliveries && !empty(array_filter($deliveries)) && (Configuration::get('ED_DISPLAY_PRIORITY') == 2)) {
            $current_cart = $this->context->cart;
            $curr_order = ['price' => $current_cart->getOrderTotal(), 'weight' => $this->context->cart->getTotalWeight()];
            $deliveries = $this->addPricesToCarriers($deliveries, $curr_order);
        }

        $deliveries = $this->sortDeliveries($deliveries, Configuration::get('ED_DISPLAY_PRIORITY'));

        if (($deliveries !== false && !empty($deliveries)) || count($relandavail) > 0) {
            if ($this->willDisplayCalendar()) {
                $deliveries = $this->addCalendarDays($deliveries); // HR
            }
            $this->context->smarty->assign(
                [
                    'hasDeliveries' => count($deliveries) > 0 ? true : false,
                    'id_carrier' => $this->id_carriers[0]['id_carrier'],
                    'ed_cart' => $deliveries,
                    'ed_selected' => $sel_carrier,
                    'edclass' => Configuration::get('ed_class'),
                    'edbackground' => Configuration::get('ed_custombg'),
                    'edborder' => Configuration::get('ed_customborder'),
                    'ed_logged' => $this->context->cookie->id_customer > 0,
                    'ed_controller' => $controller,
                    'ed_notify_oos' => ($both_deliveries[0] && $both_deliveries[1]),
                    'ed_notify_oos_msg' => Configuration::get('ed_order_long_msg', $this->context->language->id),
                    'ed_hide_delay' => Configuration::get('ED_ORDER_HIDE_DELAY'),
                    'dates_by_product' => Configuration::get('ED_DATES_BY_PRODUCT'),
                    'display_cart_line' => Configuration::get('ED_ORDER_TYPE') == 0,
                    'ed_relandavail' => $relandavail, // To be tested
                ]
            );
            $output = '';
            if ($summary !== false) {
                $output .= $this->display(__FILE__, 'views/templates/hook/estimateddelivery-summary.tpl');
            } else {
                $output .= $this->display(__FILE__, 'views/templates/hook/estimateddelivery-cart.tpl');
            }
            if ($displayCalendar) {
                $this->loadCalendarParams();

                $output .= $this->display(__FILE__, 'views/templates/hook/ed-calendar-delivery-display.tpl'); // ED_CALENDAR_DISPLAY
            }

            return $output;
        }
    }

    private function findProductIndex($products, $id_product, $id_product_attribute)
    {
        foreach ($products as $index => $product) {
            if ($product['id_product'] == $id_product && $product['id_product_attribute'] == $id_product_attribute) {
                return $index;
            }
        }

        return null;
    }

    public function loadCalendarParams()
    {
        // if ($this->context->cookie->__isset('ed_calendar_date')) {
        $date_format = str_replace('/', '-', $this->context->language->date_format_lite);
        $this->context->smarty->assign(
            [
                'ed_date_format_calendar' => $date_format,
                'ed_locale' => $this->context->language->locale,
                'ed_datepicker_format' => $this->datePickerFormatGenerator($date_format),
                'ed_calendar_date' => date($date_format, strtotime($this->context->cookie->__get('ed_calendar_date'))),
                'ed_calendar_date_formatted' => EDTools::setDateFormatForED($this->context->cookie->__get('ed_calendar_date'), 'base_df'), // WAS , 0),
            ]
        );
        // }
    }

    /**
     * Function to transform a date to a datepicker format, full year is "yy"
     *
     * @param $inputFormat
     *
     * @return string
     */
    private function datePickerFormatGenerator($inputFormat)
    {
        $formatMapping = [
            'Y' => 'yy',
            'm' => 'mm',
            'd' => 'dd',
            'y' => 'yy',  // Will force the 2 digits display
            'n' => 'mm',  // Will force the 2 digits display
            'j' => 'dd',  // Will force the 2 digits display
        ];

        return strtr($inputFormat, $formatMapping);
    }

    private function getCarriersFromCart($params)
    {
        $carrier_ids = [];
        $carriers = $this->getCarrierIds($params);

        if (empty($carriers)) {
            return false;
        }
        $carriers_count = count($carriers);
        for ($i = 0; $i < $carriers_count; ++$i) {
            if (strpos($carriers[$i]['id_carrier'], '0') !== false && Tools::strlen($carriers[$i]['id_carrier']) > 5) {
                $tmp = rtrim(Cart::desintifier($carriers[$i]['id_carrier']), ',');
                if ($tmp == 0) {
                    $tmp = $carriers[$i]['id_carrier'];
                }
            } else {
                $tmp = $carriers[$i]['id_carrier'];
            }
            if ($this->getstrpos($tmp, ',') !== false) {
                $tmp = explode(',', $tmp);
                foreach ($tmp as $item) {
                    if (!empty($item) && $item != '') {
                        $carrier_ids[] = $item;
                    }
                }
            } else {
                $carrier_ids[] = $tmp;
            }
        }

        $carrier_ids = array_unique($carrier_ids);
        if ($this->id_carriers === false && count($carrier_ids) > 0) {
            $this->id_carriers = $this->getCarriersFromIds($carrier_ids);
        }
    }

    private function willDisplayCalendar($location = '')
    {
        if (isset($this->willDisplayCalendar)) {
            if (!$this->willDisplayCalendar) {
                return false;
            }

            return $location ? $this->willDisplayCalendar[$location] == 'on' : !empty($this->willDisplayCalendar);
        }
        if (!Configuration::get('ED_CALENDAR_DATE') || (Configuration::get('ED_CALENDAR_DATE') && Configuration::get('ED_DATES_BY_PRODUCT'))) {
            $this->willDisplayCalendar = false;
        }
        if (!isset($this->willDisplayCalendar)) {
            $calendar_locations = Configuration::getMultiple(
                [
                    'ED_CALENDAR_DISPLAY_CART',
                    'ED_CALENDAR_DISPLAY_CARTFOOTER',
                    'ED_CALENDAR_DISPLAY_CARRIERS',
                    'ED_CALENDAR_DISPLAY_PAYMENT',
                    'ED_CALENDAR_DISPLAY_HOOK',
                ]
            );
            $this->willDisplayCalendar = $calendar_locations;
        }

        return $this->willDisplayCalendar;
    }

    /**
     * Method to generate the available calendar days
     * It generates as many selectable days as in the configuration options
     */
    public function addCalendarDays($deliveries)
    {
        $selected_carrier = (int) ($this->context->cart->id_carrier ?? Configuration::get('PS_CARRIER_DEFAULT'));
        $days = (int) Configuration::get('ED_CALENDAR_DATE_DAYS');
        $dh = new DeliveryHelper();
        $date_format = str_replace('/', '-', $this->context->language->date_format_lite);
        foreach ($deliveries as $key => $delivery) {
            if (is_array($delivery)) {
                foreach ($delivery as $subKey => $d) {
                    $deliveries[$key][$subKey] = $this->addCalendarDaysToDelivery($d, $selected_carrier, $days, $dh, $date_format);
                }
            } else {
                $deliveries[$key] = $this->addCalendarDaysToDelivery($delivery, $selected_carrier, $days, $dh, $date_format);
            }
        }

        return $deliveries;
    }

    private function addCalendarDaysToDelivery($delivery, $selected_carrier, $days, $dh, $date_format)
    {
        if ($delivery->dc->id_carrier == $selected_carrier) {
            // Set the calendar initial date
            $date = date('Y-m-d', strtotime($delivery->delivery_cmp_min));
            if (!$this->context->cookie->__isset('ed_calendar_date')) {
                $this->context->cookie->__set('ed_calendar_date', $date);
                $this->context->cookie->write();
                EDTools::addPendingCalendarDate($this->context->cart->id, $date);
            }
        }
        $pattern = $delivery->dc->shippingdays ?? '1111100';
        $date = $delivery->delivery_cmp_min;
        $dates = [];
        $c = 0;
        for ($i = 0; $c < $days; ++$i) {
            $weekday = date('N', strtotime($date));
            if ($pattern[$weekday - 1] == 1) {
                $dates[] = date($date_format, strtotime($date));
                ++$c;
            }
            $date = date('Y-m-d', strtotime($date . ' +1 days')) . ' 00:00:00';
            $date = $dh->checkHolidays($date);
        }
        $delivery->calendar_dates = json_encode($dates);

        return $delivery;
    }

    /**
     * @param array holiday list
     * HR
     */
    // TODO Review HR
    private function holidayList()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ed_holidays` WHERE CURDATE() between holiday_start and holiday_end';
        $holidays = Db::getInstance()->executeS(pSQL($sql));

        return $holidays;
    }

    /**
     * @param current date
     * HR
     */
    // TODO Review HR
    private function validateHoliday()
    {
        $holidays = $this->holidayList();

        $holidayEnd = array_column($holidays, 'holiday_end');

        if ($holidayEnd[0] > $holidayEnd[1]) {
            $holidayEndate = $holidayEnd[0];
        } else {
            $holidayEndate = $holidayEnd[1];
        }

        $stop_date = new DateTime($holidayEndate);
        $modifiedDate = $stop_date->modify('+1 day');
        $newCurrDate['date'] = $modifiedDate->format('Y-m-d');
        $newCurrDate['day'] = $modifiedDate->format('l');

        return $newCurrDate;
    }

    /**
     * @param current date
     * HR
     */
    // TODO Review HR
    private function validatePickingShipping()
    {
    }

    public function checkConditionForCustomization($params)
    {
        $enable_custom_days = Configuration::get('ed_enable_custom_days');
        $which_module = Configuration::get('ed_custom_module_for_custom_days');
        if ($which_module > 0) {
            $customizecart = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'customizecart WHERE id_cart = ' . (int) $params['cart']->id);
        } else {
            $customizecart = [];
        }
        $this->context->smarty->assign(
            [
                'enable_custom_days' => (int) $enable_custom_days,
                'which_module' => ((int) $which_module > 0) ? 1 : 0,
                'is_customize_cart' => (count($customizecart) > 0) ? 1 : 0,
            ]
        );
    }

    public function hookDisplayCartModalContent($params)
    {
        return $this->displayEDsOnAjaxCartModal($params);
    }

    public function displayEDsOnAjaxCartModal($params)
    {
        if (!Configuration::get('ed_cart_modal')) {
            return false;
        }
        // Need to initializate the SmartForm
        SmartForm::init($this);
        /*if (!$this->modal_displayed == true) {
            return;
        }*/
        // TODO review if id_product, id_product_attribute are really needed
        // TODO review this feature
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->setBetterLocales();
        }
        $this->getCarriersFromCart($params);

        if (empty($this->id_carriers)) {
            return false;
        }

        // Check if there is a carrier selected, if there is move it to first position
        $sel_carrier = 0;
        if (isset($params['cart'])) {
            if (is_object($params['cart'])) {
                $sel_carrier = $params['cart']->id_carrier;
            } else {
                $sel_carrier = $params['cart']['id_carrier'];
            }
        }

        $products = $this->context->cart->getProducts();
        // Make sure gifts doesn't interfere with ED
        $products = $this->removeGifts($products);
        $both_deliveries = [false, false];
        $relandavail = [];
        $deliveries = $this->getDeliveriesFromProductList($params, $products, $both_deliveries, $relandavail);
        if ($deliveries === false || empty(array_filter($deliveries))) {
            return false; // Didn't find any deliveries
        }
        if (!is_array($deliveries)) {
            $deliveries = [$deliveries];
        } else {
            $deliveries = array_values($deliveries);
        }

        // Simulate default placement to avoid the hide-default class
        $this->context->smarty->assign(['ed_placement' => 0]);

        // $this->modal_displayed = true;
        return SmartForm::openTag('div', 'id="ed_modal"') . $this->renderDeliveries($deliveries, null, true) . SmartForm::closeTag('div');
        // return $this->renderDeliveries($deliveries);
    }

    public function getCarriersForOrder($id_customer, $cart)
    {
        $id_zone = $this->getIdZone();
        if (!$id_zone) {
            return false;
        }
        //        if ($cart->id == 0) {
        // //            if (!Cart::getNbProducts($cart->id)) {
        // //                return $this->getCarriersByZone($id_zone);
        // //            }
        //        } else {
        //            $cart = null;
        //        }
        $carriers = Carrier::getCarriersForOrder(
            $id_zone,
            Customer::getGroupsStatic($id_customer),
            $cart
        );

        if (!empty($carriers)) {
            return $this->addDeliveryDataToCarriers($carriers);
        }

        // No carriers have been found
        return false;
    }

    //    private function getCarriersByZone($id_zone)
    //    {
    //        $sql = '
    //        SELECT * FROM
    //             (SELECT * FROM ' . _DB_PREFIX_ . 'carrier_shop AS cs
    //            NATURAL JOIN ' . _DB_PREFIX_ . 'carrier
    //            LEFT JOIN ' . _DB_PREFIX_ . 'ed_carriers USING (id_reference, id_shop)
    //            LEFT JOIN ' . _DB_PREFIX_ . 'carrier_lang AS cl USING (id_carrier, id_shop)
    //            NATURAL JOIN ' . _DB_PREFIX_ . 'carrier_zone
    //            WHERE id_zone = ' . (int) $id_zone . '
    //            AND id_lang=' . (int) $this->context->language->id . '
    //            AND active = 1 AND deleted=0
    //            AND cs.id_shop = ' . (int) $this->context->shop->id . '
    //            ORDER BY position ASC, id_carrier DESC) AS tmp
    //        GROUP BY id_reference';
    //
    //        $results = DB::getInstance()->executeS($sql);
    //        if (!empty($results) && $results !== false) {
    //            if (self::$debug_mode) {
    //                $this->debugVar($results, 'Carriers Found by address');
    //            }
    //
    //            return $results;
    //        }
    //    }

    public function removeGifts($products, $id_cart = 0)
    {
        if ((int) $id_cart > 0) {
            $cart = new Cart((int) $id_cart);
        } elseif (isset($this->context->cart)) {
            $cart = $this->context->cart;
        }
        if (isset($cart) && method_exists($cart, 'getSummaryDetails')) {
            $gifts = $cart->getSummaryDetails(null, true);
            $gifts = $gifts['gift_products'];
            $pcount = count($products);
            if (count($gifts) > 0) {
                foreach ($gifts as $gift) {
                    for ($i = 0; $i < $pcount; ++$i) {
                        if ($gift['id_product'] == $products[$i]['id_product']) {
                            unset($products[$i]);
                            break;
                        }
                    }
                }
            }
        }

        return array_values($products);
    }

    /* Special hook to display the Estimated Delivery in the order summary, on each product */
    public function hookDisplayCartSummaryProductDelivery($params, $ajax = false)
    {
        if ($this->is_active
            && Configuration::Get('ED_ORDER_SUMMARY_PRODUCT')
            && Configuration::get('ED_ORDER')
            && (($this->context->controller->php_self == 'order' && (int) Tools::getValue('step') == 0)
                || ($this->context->controller->php_self == 'cart') || $ajax == true)) {
            if (isset($this->context->cart->id_carrier)) {
                // Load Carriers and ID Zone
                $this->getEDCarriers($this->context->cart);
                // Force the selected Carrier
                $this->id_carriers = $this->getCarriersFromIds([$this->context->cart->id_carrier]);
            }
            if (!isset($params['carrier'])) {
                $params['carrier'] = new Carrier($this->context->cart->id_carrier);
            }
            if ($params['product'] instanceof ProductListingLazyArray) {
                $product = (object) [
                    'id' => $params['product']['id_product'],
                    'id_product_attribute' => $params['product']['id_product_attribute'],
                    'quantity' => $params['product']['quantity'],
                    'quantity_available' => $params['product']['quantity_available'],
                ];
            } else {
                if (is_array($params['product'])) { // for v1.6
                    if (!isset($params['product']['id'])) {
                        $params['product']['id'] = $params['product']['id_product'];
                    }
                    $params['product'] = (object) $params['product'];
                }
                $product = $params['product'];
            }
            $deliveries = $this->generateEstimatedDelivery($params, $product->id, $product->id_product_attribute, $product->quantity, 'array', false, $product->quantity_available, false, false, true);
            if ($deliveries === false) {
                return;
            }

            if (!empty(array_filter($deliveries)) && (Configuration::get('ED_DISPLAY_PRIORITY') == 2)) {
                $current_cart = $this->context->cart;
                $curr_order = ['price' => $current_cart->getOrderTotal(), 'weight' => $this->context->cart->getTotalWeight()];
                $deliveries = $this->addPricesToCarriers($deliveries, $curr_order);
            }
            $deliveries = $this->sortDeliveries($deliveries, Configuration::get('ED_DISPLAY_PRIORITY'));

            $delivery = $deliveries[0];
            $more_options = false;
            if (is_object($deliveries) || is_array($deliveries)) {
                if (count($deliveries) > 1) {
                    // If more than one result, try to find the default carrier
                    foreach ($deliveries as $key => $d) {
                        if ($deliveries[$key]->dc->is_default) {
                            $d = $deliveries[$key];
                            break;
                        }
                    }
                    $more_options = true;
                }
            }
            $this->checkConditionForCustomization($params);
            $this->context->smarty->assign(
                [
                    'delivery' => $delivery,
                    'more_options' => $more_options,
                    'ed_product_summary' => html_entity_decode(Configuration::get('ED_ORDER_SUMMARY_LINE')),
                    'ed_id_product' => $params['product']->id,
                    'ed_id_product_attribute' => (isset($params['product']->id_product_attribute)) ? $params['product']->id_product_attribute : 0,
                ]
            );

            return $this->display(__FILE__, 'views/templates/hook/ed-cart-product-summary.tpl');
        }
    }

    public function hookDisplayNtCartSummaryProductDelivery($params)
    {
        if ($this->is_active
            && (($this->context->controller->php_self == 'order'
                    && (int) Tools::getValue('step') == 0) || ($this->context->controller->php_self == 'cart'))
            && Configuration::Get('ED_ORDER_SUMMARY_PRODUCT')
            && Configuration::get('ED_ORDER')
        ) {
            $deliveries = $this->generateEstimatedDelivery($params, $params['product']->id, $params['product']->id_product_attribute, $params['product']->quantity, 'array', false, $params['product']->quantity_available);
            $delivery = $deliveries[0];
            $more_options = false;
            if (count((array) $deliveries) > 1) {
                // If more than one result, try to find the default carrier
                foreach ($deliveries as $key => $d) {
                    if ($deliveries[$key]->dc->is_default) {
                        $d = $deliveries[$key];
                        break;
                    }
                }
                $more_options = true;
            }
            $this->context->smarty->assign(
                [
                    'delivery' => $delivery,
                    'more_options' => $more_options,
                    'ed_product_summary' => html_entity_decode(Configuration::get('ED_ORDER_SUMMARY_LINE')),
                ]
            );

            return $this->display(__FILE__, 'views/templates/hook/nt-ed-cart-product-summary.tpl');
        }
    }

    /**
     * If a new carrier is created, set up the default parameters for the Estimated Delivery Module
     *
     * @param $params
     *
     * @return void
     */
    public function hookActionCarrierUpdate($params)
    {
        $ref = $params['carrier']->id_reference;
        if (!$this->edCarrierExists($ref)) {
            $this->createEDCarrier($ref);
        }
    }

    private function edCarrierExists($id_reference)
    {
        $sql = 'SELECT id_reference FROM ' . _DB_PREFIX_ . 'ed_carriers WHERE id_reference = ' . (int) $id_reference;

        return (bool) Db::getInstance()->getValue($sql);
    }

    private function createEDCarrier($id_reference)
    {
        $data = [
            'id_reference' => (int) $id_reference,
            'shippingdays' => '1111100',
            'min' => 1,
            'max' => 1,
            'picking_days' => '1111100',
            'picking_limit' => json_encode(array_fill(0, 7, '23:59')),
            'ed_active' => 1,
            'ed_alias' => '',
        ];
        foreach (Shop::getShops() as $shop) {
            $data['id_shop'] = $shop['id_shop'];
            Db::getInstance()->insert('ed_carriers', $data);
        }
    }

    public function hookActionValidateOrder($params)
    {
        $force_date = false;

        if (!($this->is_active && Configuration::get('ED_ORDER'))) {
            return;
        }

        if (isset($params['order'])) {
            $order = $params['order'];
        } elseif (isset($params['objOrder'])) {
            $order = $params['objOrder'];
        } else {
            // No order detected
            return false;
        }

        $id_lang = null;
        if (isset($params['order']->id_lang)) {
            $id_lang = $params['order']->id_lang;
        } elseif (isset($params['customer']->id_lang)) {
            $id_lang = $params['customer']->id_lang;
        }

        $this->setBetterLocales($id_lang);

        $calendar_date = false;
        if (Configuration::get('ED_CALENDAR_DATE')) {
            if ($this->context->cookie->__isset('ed_calendar_date')) {
                $calendar_date = $this->context->cookie->__get('ed_calendar_date');
            }
            if (!$calendar_date) {
                $calendar_date = EDTools::getPendingCalendarDates($order->id_cart);
                if (is_array($calendar_date)) {
                    $calendar_date = $calendar_date[0];
                }
            }
        }
        $this->updateEstimatedDeliveryForOrder($order, $params, null, '', false, false, $calendar_date);

        $this->extra_mail_vars['{estimateddelivery}'] = $this->getExtraVarsFromOrderId($order->id, $params['order']->id_lang);

        if (Configuration::get('ed_undefined_notify')) {
            $this->sendUndefinedDeliveryAdminEmail($order);
        }
    }

    private function sendUndefinedDeliveryAdminEmail($order)
    {
        $context = Context::getContext();
        $ed = $this->getEdOrder($order->id);
        if (!$ed['undefined_delivery']) {
            return;
        }
        $data = [];
        $data['{id_order}'] = (int) $order->id;
        $data['{order_id}'] = (int) $order->id;
        $data['{order_name}'] = $order->getUniqReference();
        $data['{days_limit}'] = Configuration::get('ed_undefined_validate_max');

        foreach ($ed as $var => $value) {
            $data['{' . $var . '}'] = $value;
        }

        $to = explode(',', str_replace(' ', '', Configuration::get('ed_undefined_notify_email')));
        foreach ($to as $key => $email) {
            if (!Validate::isEmail($email)) {
                unset($to[$key]);
            }
        }
        if (empty($to)) {
            PrestaShopLogger::addLog('The email to the admin to notify an undefined delivery couldn\'t be sent. The email addresses are invalid or empty', 2);

            return;
        }

        $sent = Mail::Send(
            (int) $order->id_lang,
            'undefined_delivery_admin_email',
            sprintf(Mail::l('New order with undefined delivery #%s', $order->id_lang), $order->reference),
            $data,
            $to,
            null,
            Configuration::get('PS_SHOP_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            _PS_ROOT_DIR_ . '/modules/' . $this->name . '/mails/',
            false,
            (int) $order->id_shop
        );
        if ($sent === false) {
            echo 'Email not sent';
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (Configuration::get('ED_ORDER') && isset($params['id_order'])) {
            $id_order = (int) $params['id_order'];
            if (Configuration::get('ed_enable_delayed_delivery')) {
                // Set shipped State
                $dd_state = (null !== Configuration::get('ed_dd_order_state')) ? Configuration::get('ed_dd_order_state') : 0;
                if ($dd_state == $params['newOrderStatus']->id) {
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ed_orders` SET shipped = 1 WHERE id_order = ' . (int) $id_order);
                }
            }

            $c = (int) DeliveryHelper::countOrderStates($id_order);
            $order = new Order((int) $id_order);
            // Add the id_address_delivery to be able to detect the right carrier
            if (!isset($params['cart'])) {
                $params['cart']['id_address_delivery'] = $order->id_address_delivery;
            }

            $ed_order = $this->getEdOrder($id_order);
            if (($c == 0 && empty($ed_order)) || ($c > 0 && !DeliveryHelper::edIsDefinitive($id_order) && $params['newOrderStatus']->logable)) {
                $this->updateEstimatedDeliveryForOrder($order, $params, $params['newOrderStatus']->logable);
            }
            if (Configuration::get('ED_CALENDAR_DATE') && ($params['newOrderStatus']->logable || in_array($params['newOrderStatus']->id, [(int) Configuration::get('PS_OS_ERROR'), (int) Configuration::get('PS_OS_CANCELED')]))) {
                // If the order is valid, remove the pending calendar dates
                $this->context->cookie->__unset('ed_calendar_date');
                $this->removePendingCalendarDate($id_order);
            }
        }
    }

    /**
     * If the order state is valid payment, remove the pending calendar date from the Configurations
     * Old saved dates are kept at maximum for 7 days
     *
     * @param $id_order
     *
     * @return void
     */
    private function removePendingCalendarDate($id_order)
    {
        $update = false;
        if ((int) $id_order == 0) {
            return;
        }
        $order = new Order((int) $id_order);
        $dates = EDTools::getPendingCalendarDates();
        if (isset($dates[$order->id_cart])) {
            unset($dates[$order->id_cart]);
            $update = true;
        }
        // If there are still dates on the array, check if they are some older than 7 days
        if (!empty($dates)) {
            // Get the current date and time as a Unix timestamp
            $dateLimit = time() - (7 * 24 * 3600);
            foreach ($dates as $key => $date) {
                // Convert the date string to a Unix timestamp
                $creationDate = strtotime($date[1]);

                // Check if the timestamp of the date is less than the timestamp of 7 days ago
                if ($creationDate < $dateLimit) {
                    unset($dates[$key]);
                    $update = true;
                }
            }
        }
        if ($update) {
            Configuration::updateValue('ED_PENDING_CALENDAR_DATE', json_encode($dates));
        }
    }

    public function hookDisplayPDFInvoice($params)
    {
        if (Configuration::get('ED_SHOW_INVOICE')) {
            $objOrder = new Order((int) $params['object']->id_order);
            $params['order'] = $objOrder;

            if ($this->prepareEDForOrderDetails($params)) {
                return $this->display(__FILE__, 'views/templates/hook/ed-invoice-template.tpl');
            }
        }

        return null;
    }

    /* TODO */
    protected function getProductListFromOrder($order)
    {
        // var_dump($order->id_carrier);
        $this->id_carriers = $this->getCarriersFromIds([$order->id_carrier]);
        if (isset($order->product_list) && is_array($order->product_list)) {
            $products = $order->product_list;
        } else {
            $products = $order->getProductsDetail();
            $products = $this->removeGifts($products, $order->id_cart);
            if (self::$debug_mode) {
                $this->debugVar($products, 'Order retrieved Products before processing');
            }
            foreach ($products as &$p) {
                if (!isset($p['id_product']) && isset($p['product_id'])) {
                    $p['id_product'] = (int) $p['product_id'];
                }
                if (!isset($p['id_product_attribute']) && isset($p['product_attribute_id'])) {
                    $p['id_product_attribute'] = (int) $p['product_attribute_id'];
                }
                if ($p['product_quantity_in_stock'] <= 0) {
                    $p['quantity_available'] = $p['product_quantity_in_stock'];
                } else {
                    $p['quantity_available'] = $p['product_quantity'];
                }
                if ((version_compare(_PS_VERSION_, '1.7.0', '>')
                    && version_compare(_PS_VERSION_, '1.7.6.8', '<='))
                    || Configuration::get('ED_ADV_ORDER_FORCE_STOCK')) {  // WAS 1.7.5.2 but the same issue has been detected on  1.7.6.8
                    if ($p['product_quantity_in_stock'] == 0) {
                        $p['product_quantity_in_stock'] = $p['product_quantity'];
                    }
                }
            }
            if (self::$debug_mode) {
                $this->debugVar($products, 'Order retrieved Products After processing');
            }
        }

        return $products;
    }

    /**
     * Get the Estimated Delivery Data from an order and save it, if it's called again it will take the data from the cache
     *
     * @param $id_order
     *
     * @return array The Estimated Delivery data
     */
    private function getEdOrder($id_order)
    {
        if (!isset(self::$ed_orders[$id_order])) {
            $data = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'ed_orders WHERE id_order = ' . (int) $id_order);
            if (empty($data)) {
                return false;
            }
            self::$ed_orders[$id_order] = $data;
        }

        return self::$ed_orders[$id_order];
    }

    public function updateEstimatedDeliveryForOrder($order, $params, $definitive = false, $force_date = '', $from_picking = false, $ajax_update = false, $calendar_date = false) // $disable_quantity_check need review
    {
        // Make sure the Smart Form has been initialized
        if (method_exists('SmartForm', 'hasInit') && !SmartForm::hasInit()) {
            SmartForm::init($this);
        }

        if ($this->id_carriers === false) {
            $this->id_carriers = $this->getCarriersFromIds([$order->id_carrier], true);
        }
        $is_order = true;
        $is_undefined = false;
        $deliveries = $individual_deliveries = [];
        $both_deliveries = [false, false];
        $is_available = $is_release = false;
        $relandavail = [];
        $context = Context::getContext();
        $context->language = new Language($order->id_lang);
        if (!isset($context->customer)) {
            $context->customer = new Customer((int) $order->id_customer);
        }
        if (method_exists('Context', 'setInstanceForTesting')) {
            Context::setInstanceForTesting($context);
        }
        // TODO Implement release and available
        $all_virtual = $is_available = $is_release = 0;

        /* Start Calendar Dates */
        if (($calendar_date && Validate::isDate($calendar_date)) || $this->isCalendarDateOrder($order->id)) {
            // Maybe The future date check can bring issues, review it on later updates
            if (DeliveryHelper::isFutureDate($calendar_date)) {
                return $this->saveCalendarDates($calendar_date, $order, $definitive);
            } else {
                $msg = $this->l('Estimated Delivery: Error when saving the date by using the calendar feature.') . '. ' . $this->l('Reason: Calendar date sent with past date') . '. ' . sprintf($this->l('Selected Date %s'), $calendar_date);
                if ($ajax_update) {
                    $ret = [
                        'success' => false,
                        'data' => $msg,
                    ];
                    echo json_encode($ret);
                } else {
                    // Trying to update to a past date, abort date saving and generate a register in the log
                    PrestaShopLogger::addLog($msg, 3, 10, 'order', $order->id);
                }

                return;
            }
        }
        /* End Calendar Dates */
        $products = $this->getProductListFromOrder($order);
        if (EDelivery::allVirtual($products)) {
            $all_virtual = 1;
        } else {
            $deliveries = $this->getDeliveriesFromProductList($params, $products, $both_deliveries, $relandavail, $force_date, $from_picking, $is_order);
        }

        if (is_array($deliveries)) {
            $deliveries = reset($deliveries);
        }

        if (($deliveries !== false && !empty($deliveries)) || count($relandavail) > 0 || $all_virtual == 1) {
            $ed_order = $this->getEdOrder($order->id);
            if (Configuration::get('ED_DATES_BY_PRODUCT_FORCE')) {
                $individual_dates = Configuration::get('ED_DATES_BY_PRODUCT');
            } else {
                $individual_dates = $ed_order !== false ? $ed_order['individual_dates'] : Configuration::get('ED_DATES_BY_PRODUCT');
            }
            $individual_data = [];
            $is_definitive = $definitive || (isset($params['orderStatus']) && $params['orderStatus']->logable == 1) || (isset($params['newOrderStatus']) && $params['newOrderStatus']->logable == 1);
            // Fill in the fields
            if ($all_virtual) {
                $date_min = $date_max = $picking_day = date('Y-m-d');
            } elseif ($individual_dates) {
                $date_min = $date_max = '1970-01-01';
                $order_details = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'order_detail` WHERE id_order =' . (int) $order->id);
                foreach ($deliveries as $delivery) {
                    foreach ($order_details as $detail) {
                        if (!isset($delivery->dp)) {
                            continue;
                        }
                        if ($delivery->dp->id_product == $detail['product_id'] && $delivery->dp->id_product_attribute == $detail['product_attribute_id']) {
                            $individual_data[] = [
                                'id_order' => $order->id,
                                'id_order_detail' => $detail['id_order_detail'],
                                'delivery_min' => pSQL(date('Y-m-d', strtotime($delivery->delivery_cmp_min))),
                                'delivery_max' => pSQL(date('Y-m-d', strtotime($delivery->delivery_cmp_max))),
                                'picking_day' => pSQL(date('Y-m-d', strtotime($delivery->shipping_day))),
                                'undefined_delivery' => (int) $delivery->dp->is_undefined_delivery,
                            ];

                            if ($delivery->dp->is_undefined_delivery) {
                                $is_undefined = true;
                            }

                            if ($delivery->delivery_cmp_min > $date_min) {
                                $date_min = $delivery->delivery_cmp_min;
                                $date_max = $delivery->delivery_cmp_max;
                                $picking_day = $delivery->shipping_day;
                            }
                        }
                    }
                }
                $deliveries = ['individual_data' => $deliveries];
            } else {
                // Regular delivery
                if (is_array($deliveries)) {
                    $deliveries = reset($deliveries);
                }
                $date_min = $deliveries->delivery_cmp_min;
                $date_max = $deliveries->delivery_cmp_max;
                $picking_day = $deliveries->shipping_day;
                $is_release = $deliveries->dp->is_available;
                $is_available = $deliveries->dp->is_virtual;
                $is_undefined = $deliveries->dp->is_undefined_delivery;
            }
            $data = [
                'id_order' => (int) $order->id,
                'id_carrier' => (int) $order->id_carrier,
                'delivery_min' => pSQL(date('Y-m-d', strtotime($date_min))),
                'delivery_max' => pSQL(date('Y-m-d', strtotime($date_max))),
                'picking_day' => pSQL(date('Y-m-d', strtotime($picking_day))),
                'is_virtual' => (int) $all_virtual,
                'is_available' => (int) $is_available,
                'is_release' => (int) $is_release,
                'is_definitive' => (int) $is_definitive,
                'undefined_delivery' => (int) $is_undefined,
                'individual_dates' => (int) $individual_dates,
            ];
            // if we have reached this point and Calendar Date Feature is active the minimum date needs to be saved
            if (Configuration::get('ED_CALENDAR_DATE')) {
                $data['calendar_date'] = pSQL(date('Y-m-d', strtotime($date_min)));
            }
            // Update the main date
            if ($ajax_update || (!$ajax_update && !$ed_order) || $is_definitive) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_orders (' . implode(',', array_keys($data)) . ') VALUES ("' . implode('","', $data) . '") ON DUPLICATE KEY UPDATE delivery_min = VALUES(delivery_min), delivery_max = VALUES(delivery_max), is_definitive = VALUES(is_definitive), picking_day = VALUES(picking_day), undefined_delivery = VALUES(undefined_delivery)';
                if (DB::getInstance()->execute($sql) === false) {
                    echo 'Error: ' . Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;

                    return false;
                }
            }
            // Update the individual dates after updating the order
            if (count($individual_data) > 0) {
                // Preventivelly delete the old dates when individual dates are generated.
                Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ed_order_individual WHERE id_order = ' . (int) $order->id);
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_order_individual (' . implode(',', array_keys($individual_data[0])) . ') VALUES';
                foreach ($individual_data as $data) {
                    $sql .= ' ("' . $data['id_order'] . '", "' . $data['id_order_detail'] . '", "' . $data['delivery_min'] . '", "' . $data['delivery_max'] . '", "' . $data['picking_day'] . '", "' . (int) $data['undefined_delivery'] . '"),';
                }
                $sql = trim($sql, ',') . ' ON DUPlICATE KEY UPDATE delivery_min = VALUES(delivery_min), delivery_max = VALUES(delivery_max), picking_day = VALUES(picking_day)';
                if (DB::getInstance()->execute($sql) === false) {
                    echo 'Error while updating the order\'s invididual dates: ' . Db::getInstance()->getMsgError() . SmartForm::genDesc('', '', 'br') . $sql;

                    return false;
                }
                $data['individual_data'] = $individual_data;
            }

            return $deliveries;
        }

        return false;
    }

    /**
     * Check if an order has a fixed calendar date
     *
     * @param $id_order the order's ID to check
     *
     * @return bool True if the order is definitive or false if the order does not exist yet or it has a falsy value
     */
    public function isCalendarDateOrder($id_order)
    {
        $ret = Db::getInstance()->getValue('SELECT calendar_date FROM ' . _DB_PREFIX_ . 'ed_orders WHERE id_order = ' . (int) $id_order);

        return $ret != false && $ret != '1000-01-01';
    }

    public function saveCalendarDates($calendar_date, $order, $is_definitive = null)
    {
        $dh = new DeliveryHelper();
        $order = new Order($order->id);
        $carrier = $this->getCarriersFromIds([(int) $order->id_carrier]);
        $carrier = $carrier[0];

        $picking = $this->adv_picking ? $carrier['picking_days'] : Configuration::get('ed_picking_days');
        // Get picking date for order new code, get picking date when customer selct date from calendar only
        $shipping_date = $dh->addDaysIteration($calendar_date, $picking, $carrier['min'], true);
        $shipping_date = $dh->checkNext('shipping', $shipping_date, $picking, '', $return = 'date', false, true);

        $newDelivery = [
            'id_order' => $order->id,
            'id_carrier' => $order->id_carrier,
            'is_definitive' => (int) ($order->valid || $is_definitive),
            'calendar_date' => $calendar_date,
            'picking_day' => date('Y-m-d', strtotime($shipping_date)),
            'delivery_min' => $calendar_date,
            'delivery_max' => $calendar_date,
        ];
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_orders
            (' . implode(',', array_keys($newDelivery)) . ')
            VALUES ("' . implode('","', $newDelivery) . '")
            ON DUPLICATE KEY UPDATE 
                is_definitive = VALUES(is_definitive),
                delivery_min = VALUES(delivery_min),
                delivery_max = VALUES(delivery_max),
                calendar_date = VALUES(calendar_date),
                picking_day = VALUES(picking_day)';
        Db::getInstance()->execute($sql);
    }

    /** notifyEDUpdate
     * Function to send an email with the Estiamted Delviery Date when order changes from no valid to valid or when the date is changed and notify customer has been activated
     **/
    public function notifyEDUpdate($order)
    {
        $delivery = Db::getInstance()->getRow('SELECT delivery_min, delivery_max FROM ' . _DB_PREFIX_ . 'ed_orders WHERE id_order = ' . (int) $order->id);
        $customer = new Customer((int) $order->id_customer);
        $delivery['delivery_cmp_min'] = $delivery['delivery_min'];
        $delivery['delivery_cmp_max'] = $delivery['delivery_max'];
        $ed = $this->setDateFormatForCarrier($delivery, 'email_df');
        $params = [
            'cart' => [
                'id_address_delivery' => $order->id_address_delivery,
            ],
        ];
        $deliveries = $this->displayOrderIndividualProductDelivery($order, $params);
        // $deliveries = reset($deliveries); //No longer needed
        $this->context->smarty->assign(
            [
                'deliveries' => $deliveries,
            ]
        );
        $individual_vars = $this->display(__FILE__, 'views/templates/hook/ed-update-individual.tpl');
        $email_vars = [
            '{order_name}' => $order->reference,
            '{order_reference}' => $order->reference,
            '{delivery_min}' => $ed['delivery_min'],
            '{delivery_max}' => $ed['delivery_max'],
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{date_by_product}' => (int) Configuration::get('ED_DATES_BY_PRODUCT'),
            '{deliveries}' => $individual_vars,
        ];
        // TODO Cange Icon selector for Unicode and Emojis
        // ? ? ? ? ?
        $checkMarkIcon = Configuration::get('ED_EMAIL_ICON') ? '✓' : '';
        $subject = $checkMarkIcon . ' ' . sprintf($this->l('Estimated Delivery date updated (%s)'), $order->reference);
        // $subject = $checkMarkIcon . ' ' . sprintf($this->emailSubjectTranslation('Estimated Delivery date updated (%s)'), $order->reference);
        $templatePath = $this->local_path . 'mails/';
        if ((int) Configuration::get('ED_DATES_BY_PRODUCT') == 0) {
            $template = 'ed_update' . ($this->is_17 ? '' : '-16');
        } else {
            $template = 'ed_update_individual';
        }

        return Mail::send(
            $order->id_lang,
            $template,
            $subject,
            $email_vars,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            $templatePath
        );
    }

    //    private function emailSubjectTranslation($msg, $id_lang)
    //    {
    //        if (method_exists('this', 'trans')) {
    //            return $this->trans($msg, [], 'Emails.Subject', $id_lang);
    //        } else {
    //            return Mail::l($msg, $id_lang);
    //        }
    //    }
    public function hookSendMailAlterTemplateVars($params)
    {
        $var = 'template_vars';
        $extra_vars = $this->addVariablesToEmails($params, $var);
        if (is_array($extra_vars)) {
            $params[$var] += $extra_vars;
        }
    }

    public function hookActionGetExtraMailTemplateVars($params)
    {
        $var = 'extra_template_vars';
        $extra_vars = $this->addVariablesToEmails($params, $var);
        if (is_array($extra_vars)) {
            $params[$var] += $extra_vars;
        }
    }

    private function addVariablesToEmails($params, $var)
    {
        $empty_vars = ['{estimateddelivery}' => '', '{delivery_min}' => '', '{delivery_max}' => '', '{ed_parcel_delivery}' => ''];
        if (isset($params[$var]['{estimateddelivery}']) && $params[$var]['{estimateddelivery}'] != '') {
            return;
        }
        $id_order = $this->getOrderIdFromTemplateVars($params['template_vars']);

        // Could not locate the order, therefore the Estimated Delivery message can't be recovered
        if (!$id_order) {
            return $empty_vars;
        }

        $order = new Order((int) $id_order);
        $id_lang = $order->id_lang;

        $this->setBetterLocales($id_lang);

        $extra_vars = $this->getExtraVarsFromOrderId($id_order, $id_lang);

        return $extra_vars;
    }

    private function getOrderIdFromTemplateVars($vars)
    {
        $id_order = $this->searchInVars($vars, 'id_order');
        if ($id_order === false) {
            $id_order = $this->searchOrderNameInTemplateVars($vars);
        }

        return $id_order;
    }

    private function searchOrderNameInTemplateVars($vars)
    {
        $order_ref = $this->searchInVars($vars, 'order_name');
        if ($order_ref !== false) {
            $id_order = $this->getOrderIdByReference($order_ref);
            if ($id_order !== false && $id_order > 0) {
                return $id_order;
            }
        }

        return false;
    }

    public function hookActionListMailThemes(array $params)
    {
        /*
        if (!isset($params['mailThemes'])) {
            return;
        }
        // @var ThemeCollectionInterface $themes
        $themes = $params['mailThemes'];
        // @var ThemeInterface $theme
        foreach ($themes as $theme) {
            $theme->getLayouts()->add(new Layout(
                'undefined_delivery_admin_email',
                __DIR__ . '/mails/layouts/undefined_delivery_admin_email.html.twig',
                '',
                $this->name
            ));
        }
        */
    }

    /**
     * @param array $params
     */
    public function hookActionBuildMailLayoutVariables(array $params)
    {
        /*
        if (!isset($params['mailLayout'])) {
            return;
        }
        // @var LayoutInterface $mailLayout
        $mailLayout = $params['mailLayout'];
        if ($mailLayout->getModuleName() != $this->name || $mailLayout->getName() != 'undefined_delivery_admin_email') {
            return;
        }
        $mail_vars = array();
        $id_cart = false;
        if (!isset($params['mailLayoutVariables']['id_order'])) {
            if (isset($this->context->cart->id)) {
                $id_cart = $this->context->cart->id;
            } elseif (isset($this->context->cookie->id_cart)) {
                $id_cart = $this->context->cookie->id_cart;
            }
            if (!$id_cart) {
                return; // Can't locate the cart files...
            }
            $order = $this->getOrderFromCartID($this->context->cart->id);
            if (!$order) {
                return; // Couldn't locate the order from this cart
            }
            $order = new Order((int)$order);
            $mail_vars = array(
                'id_order' => $order->id,
                'order_name' => $order->reference,
            );
        }
        $mail_vars['ed_undefined_validate_max'] = Configuration::get('ed_undefined_validate_max');
        $params['mailLayoutVariables'] += $mail_vars;
        */
    }

    private function getOrderIdByReference($order_ref)
    {
        $sql = 'SELECT id_order FROM ' . _DB_PREFIX_ . 'orders WHERE reference = "' . pSQL($order_ref) . '" ORDER BY id_order DESC';

        return Db::getInstance()->getValue($sql);
    }

    private function getOrderFromCartID($id_cart)
    {
        $sql = 'SELECT id_order FROM ' . _DB_PREFIX_ . 'orders WHERE id_cart = ' . (int) $id_cart;

        return Db::getInstance()->getValue(pSQL($sql));
    }

    private function searchInVars($vars, $search)
    {
        if (isset($vars['{' . $search . '}'])) {
            return $vars['{' . $search . '}'];
        } else {
            $id_order = $this->arraySearchPartial($vars, $search);
            if ($id_order === false) {
                return false;
            } else {
                return $id_order;
            }
        }
    }

    private function getExtraVarsFromOrderId($id_order, $id_lang)
    {
        $return = [
            '{estimateddelivery}' => '',
            '{ed_parcel_delivery}' => '',
        ];
        // Prevent issues with third party modules when the cart or the employees aren't configured
        if (!isset($this->context->cart->id) && !isset($this->context->employee)) {
            return $return;
        }
        $return = [];
        $context = Context::getContext();
        $this->context->language = new Language($id_lang);
        if (method_exists('Context', 'setInstanceForTesting')) {
            Context::setInstanceForTesting($this->context);
        }
        $extra_vars = '';
        $relandavail = [];

        // Review Virtual Products or Prerelase dates
        $ed = $this->getEdOrder($id_order);

        if (!is_array($ed) || empty($ed)) {
            return $return;
        }
        $expectedArrival = [];
        $ed['delivery_cmp_min'] = $expectedArrival['from'] = $ed['delivery_min'];
        $ed['delivery_cmp_max'] = $expectedArrival['until'] = $ed['delivery_max'];
        if ($ed['undefined_delivery']) {
            $ed['msg'] = $this->getUndefinedMessage('order');
        }
        $expectedArrival['from'] = $expectedArrival['from'] . ' 00:00:00';
        $expectedArrival['until'] = $expectedArrival['until'] . ' 00:00:00';
        $ed = $this->setDateFormatForCarrier($ed, 'email_df');
        $color = [
            'ed_lightblue' => 'background: #FCFEFF; border-color: #ACD8E4 !important;',
            'ed_softred' => 'background: #FFF5F5; border-color: #E4ACAC !important;',
            'ed_lightgreen' => 'background: #F5FFF5; border-color: #ADE4AC !important;',
            'ed_lightpurple' => 'background: #FAF5FF; border-color: #CDACE4 !important;',
            'ed_lightbrown' => 'background: #FFFDF5; border-color: #E4D6AC !important;',
            'ed_lightyellow' => 'background: #FFFFF5; border-color: #E4E1AC !important;',
            'ed_orange' => 'background: #FFF5E7; border-color: #E6853E !important;',
            'custom' => 'background: ' . Configuration::get('ed_custombg') . '; border-color: ' . Configuration::get('ed_customborder') . ' !important;',
        ];
        $order = new Order($id_order);
        // get the infos for the parcel delivery
        $delivery_address = new Address($order->id_address_delivery);
        $carrier = new Carrier($order->id_carrier);
        $tracking_number = DB::getInstance()->getRow(pSQL('SELECT * FROM ' . _DB_PREFIX_ . 'order_carrier WHERE id_order = ' . (int) $id_order));
        $merchant = Configuration::get('PS_SHOP_NAME');
        $orderNumber = $id_order;
        $store_url = Context::getContext()->shop->domain;
        $cart = new Cart($order->id_cart);
        $info_products = $cart->getProducts();
        foreach ($info_products as &$product) {
            $product['url'] = Context::getContext()->link->getProductLink($product['id_product']);
            $product['description_short'] = strip_tags($product['description_short']);
            $brand = new Manufacturer($product['id_manufacturer'], Context::getContext()->language->id);
            $product['brand'] = $brand->name;
            $images = Product::getCover($product['id_product']);
            if (empty($images)) {
                $p = new Product($product['id_product']);
                $images = $p->getImages(Context::getContext()->language->id);
                if (!empty($images)) {
                    $images = $images[0];
                }
            }
            if (!empty($images)) {
                $product['image'] = Context::getContext()->link->getImageLink(
                    $product['link_rewrite'],
                    $images['id_image'],
                    EDTools::getImageFormattedName('home')
                );
            }
            $product['color'] = '';
            $objProduct = new Product($product['id_product']);
            $combinations = $objProduct->getAttributeCombinationsById($product['id_product_attribute'], Context::getContext()->language->id);
            foreach ($combinations as $combination) {
                if ($combination['group_name'] == 'Color' || $combination['group_name'] == 'color') {
                    $product['color'] = $combination['attribute_name'];
                }
            }
        }
        $link = new Link();
        $trackingUrl = $link->getPageLink('history');
        $this->context->smarty->assign(
            [
                'delivery_address' => (array) $delivery_address,
                'expectedArrival' => $expectedArrival,
                'edcarrier' => (array) $carrier,
                'tracking_number' => $tracking_number['tracking_number'],
                'trackingUrl' => $trackingUrl,
                'merchant' => $merchant,
                'orderNumber' => $orderNumber,
                'store_url' => $store_url,
                'products' => $info_products,
            ]
        );
        if ($ed['individual_dates'] == 1) { // in case of the individual dates
            $both_deliveries = [false, false];
            $relandavail = [];
            $deliveries = Db::getInstance()->executeS('SELECT product_id AS id_product, product_attribute_id AS id_product_attribute, product_name AS name, delivery_min, delivery_max, picking_day FROM ' . _DB_PREFIX_ . 'order_detail NATURAL JOIN ' . _DB_PREFIX_ . 'ed_order_individual WHERE id_order = ' . (int) $order->id);
            $individual_dates = [];
            foreach ($deliveries as $delivery) {
                foreach ($info_products as $info) {
                    if ($delivery['id_product'] == $info['id_product']
                        && $delivery['id_product_attribute'] == $info['id_product_attribute']) {
                        // Product matched, add the necessary information
                        if ($delivery['id_product_attribute'] > 0) {
                            // Capture the combinations name
                            preg_match('/( - [\s\S]*:)/', $delivery['name'], $pos, PREG_OFFSET_CAPTURE);
                            $combi_name = '';
                            if (isset($pos[0])) {
                                $combi_name = Tools::substr($delivery['name'], $pos[0][1]);
                            }
                            $delivery['combi'] = $combi_name;
                        }
                        $delivery['delivery_cmp_min'] = $delivery['delivery_min'];
                        $delivery['delivery_cmp_max'] = $delivery['delivery_max'];
                        $delivery = $this->setDateFormatForCarrier($delivery, 'email_df');
                        $individual_dates[] = $info + $delivery;
                        break;
                    }
                }
            }
            $individual_dates = DeliveryHelper::sortDeliveriesByDateArray($individual_dates);
            $this->context->smarty->assign(
                [
                    'deliveries' => $individual_dates,
                ]
            );
        } else {
            // Not individual dates
            if ($ed !== false && count($ed) > 0) {
                $dt = ''; // Delivery Text
                if ($ed['delivery_min'] == $ed['delivery_max']) {
                    $dt = sprintf($this->l('On %s'), $ed['delivery_min']);
                    $dt = EDTools::fixDate($dt, $ed['delivery_min']);
                } else {
                    $dt = sprintf($this->l('Between %s and %s'), $ed['delivery_min'], $ed['delivery_max']);
                }
                $this->context->smarty->assign(
                    [
                        'delivery' => $dt,
                        'ed_relandavail' => $relandavail, // Todo
                        'require_validation' => $this->l('If your payment method requires a manual confirmation the estimated delivery of your order may change'),
                    ]
                );
            }
        }
        $this->context->smarty->assign(
            [
                'old_ts' => $this->old_ts,
                'edcolor' => isset($color[Configuration::get('ed_class')]) ? $color[Configuration::get('ed_class')] : '',
                'edbasestyles' => 'border: 1px solid #ccc; padding: 5px 10px; margin-bottom: 10px; clear:both;',
                'edmayvary' => !$ed['is_definitive'],
                'ed' => $ed,
                'ed_header' => $this->l('Estimated Delivery'),
                'require_validation' => $this->l('If your payment method requires a manual confirmation the estimated delivery of your order may change'),
                'individual_dates' => $ed['individual_dates'],
                'days_limit' => Configuration::get('ed_undefined_validate_max'),
                'undefined_validate_range' => ['min' => Configuration::get('ed_undefined_validate_min'), 'max' => Configuration::get('ed_undefined_validate_max')],
            ]
        );

        $return['{estimateddelivery}'] = $this->display(__FILE__, 'views/templates/hook/ed-email-template.tpl');
        $return['{ed_parcel_delivery}'] = $this->display(__FILE__, 'views/templates/hook/parcel-microdata.tpl');

        // Revert to the old context instance
        if (method_exists('Context', 'setInstanceForTesting')) {
            Context::setInstanceForTesting($context);
        }

        return $return;
    }

    private function arraySearchPartial($arr, $keyword)
    {
        foreach ($arr as $index => $string) {
            if (strpos($index, $keyword) !== false) {
                return $index;
            }
        }

        return false;
    }

    public function getDeliveriesFromProductList($params, $products, &$both_deliveries, &$relandavail, $force_date = '', $from_picking = false, $is_order = false, $is_pack = false)
    {
        $tmp_deliveries = [];
        $deliveries = [];
        if ($force_date == '0000-00-00 00:00:00') {
            $force_date = '';
        }

        $totalp = count($products);
        for ($i = 0; $i < $totalp; ++$i) {
            if (isset($products[$i]['product_attribute_id']) && $products[$i]['product_attribute_id'] > 0) {
                $products[$i]['id_product_attribute'] = $products[$i]['product_attribute_id'];
            }
            if (isset($products[$i]['cart_quantity']) || isset($products[$i]['product_quantity'])) {
                // cart_quantity is set if it's a new order!
                // product_quantity is set if it's an order status change
                $quantity_wanted = $products[$i]['cart_quantity'] ?? $products[$i]['product_quantity'];
                $quantity = $products[$i]['product_quantity_in_stock'] ?? $products[$i]['quantity_available'];
            // Item has stock
            // May this be needed in some versions?
            //                if (($quantity >= 0) && $quantity < $quantity_wanted) {
            //                    $quantity = $quantity_wanted;
            //                }
            } else {
                // Is a product pack
                $quantity = $products[$i]['quantity'];
                $quantity_wanted = $products[$i]['product_stock'];
            }
            $tmp_deliveries = $this->generateEstimatedDelivery($params, $products[$i]['id_product'], $products[$i]['id_product_attribute'], $quantity_wanted, 'array', false, $quantity, $force_date, $from_picking, $is_order, $is_pack);

            if ($tmp_deliveries === false) {
                continue;
            }
            if (is_object($tmp_deliveries[0])) {
                if (isset($tmp_deliveries['is_release_date']) || (isset($tmp_deliveries['is_available_date']) && $this->available_msg != '')) {
                    $relandavail[] = $tmp_deliveries;
                } elseif (Configuration::get('ED_ORDER_LONG')) {
                    $c = count($tmp_deliveries);
                    for ($j = 0; $j < $c; ++$j) {
                        if (!$tmp_deliveries[$j]->dp->isOOS && $both_deliveries[0] == false) {
                            $both_deliveries[0] = true;
                        } elseif ($tmp_deliveries[$j]->dp->isOOS && $both_deliveries[1] == false) {
                            $both_deliveries[1] = true;
                        }
                    }
                    $deliveries[] = $tmp_deliveries;
                } else {
                    $deliveries[] = $tmp_deliveries;
                }
            } elseif (is_array($tmp_deliveries)) {
                // It's a product pack generated with individual dates
                $deliveries = array_merge($deliveries, $tmp_deliveries);
            }
        }
        $deliveries = array_values($deliveries);

        if (empty($deliveries)) {
            return false;
        }
        // insert the attibute info into dp
        if (Configuration::get('ED_DATES_BY_PRODUCT')) {
            foreach ($deliveries as &$delivery) {
                $c = count($delivery);
                for ($i = 0; $i < $c; ++$i) {
                    foreach ($products as $product) {
                        if (!($this->isPack($product['id_product']) && $this->adv_mode && !Configuration::get('ED_PACK_AS_PRODUCT'))
                            && isset($product['id_product']) && $product['id_product'] != '') {
                            // Added is_object to prevent errors from appearing
                            if (is_object($delivery[$i]) && $delivery[$i]->dp->id_product == $product['id_product'] && $delivery[$i]->dp->id_product_attribute == $product['id_product_attribute']) {
                                if (isset($product['attributes']) && !empty($product['attributes'])) {
                                    $delivery[$i]->dp->attributes = $this->getAttrFromIpa($product['id_product_attribute']);
                                } elseif ((isset($params['order']) && is_object($params['order'])) || (isset($params['id_order']) && $params['id_order'] > 0) || (isset($params['cart']) && isset($params['cart']->id_address_delivery))) { // in case of an order confirmation or history
                                    if ((int) $product['id_product_attribute'] > 0) {
                                        $delivery[$i]->dp->attributes = $this->getAttrFromIpa($product['id_product_attribute']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Filter Deliveries in common
        // For PS 1.5 & 1.6 and when in the order process (carrier + order) don't perform the intersect operation to allow carriers combinations

        // Forcing it to false on the clear parameter to avoid always returning empty common carriers
        $commonCarrierIds = array_unique(DeliveryHelper::getCommonCarriersFromDeliveries($deliveries, false)); // !($is_order && !$this->is_17)
        // Exclude order process from 1.6 and 1.5 versions since orders can generate carriers combinations (Carrier 1 + Carrier 2)
        if (is_array($deliveries) && count($deliveries) > 0) { // WAS When working for 1.6 multi carrier && !($is_order && !$this->is_17)) {
            // Results found
            if (Configuration::get('ED_ORDER_LONG_NO_OOS') && ($both_deliveries[1] == true && $both_deliveries[0] == true)) {
                // Needs a Long Delivery Advice
                $deliveries = DeliveryHelper::filterDeliveries($deliveries, true, $commonCarrierIds);
            } else {
                $deliveries = DeliveryHelper::filterDeliveries($deliveries, false, $commonCarrierIds);
            }
        }

        return $deliveries;
    }

    private function getQuantityWantedFromParams($params)
    {
        if (isset($params['product'])) {
            if (is_object($params['product'])) {
                if (isset($params['product']->quantity_wanted)) {
                    return $params['product']->quantity_wanted > 0 ? $params['product']->quantity_wanted : 1;
                }
            } elseif (isset($params['product']['quantity_wanted'])) {
                return $params['product']['quantity_wanted'] > 0 ? $params['product']['quantity_wanted'] : 1;
            }
        }

        return 1;
    }

    public function getAttrFromIpa($id_product_attribute)
    {
        $id_lang = $this->context->language->id;
        $sql = 'SELECT pac.`id_product_attribute`, agl.`public_name` AS attr_group_name, al.`name` AS attr_name
            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = ' . (int) $id_lang . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = ' . (int) $id_lang . '
            )
            WHERE pac.`id_product_attribute` = ' . $id_product_attribute . '
            ORDER BY ag.`position` ASC, a.`position` ASC';
        $result = Db::getInstance()->executeS($sql);
        $attributes = [];
        foreach ($result as $res) {
            $attributes[] = [
                'attr_group_name' => $res['attr_group_name'],
                'attr_name' => $res['attr_name'],
            ];
        }

        return $attributes;
    }

    /* TODO check if it's necessary
 * public function getAttrFromOrderProducts($str)
{
    $attributes =[];
    if (strpos($str, '- ') !== false) {
        $temp = explode('- ', $str);
        for ($num=0; $num<count($temp); $num++) {
            $attribute = explode(':', $temp[$num]);
            $attributes[] = array(
                'attr_group_name' => $attribute[0],
                'attr_name'       => $attribute[1]
            );
        }
        return $attributes;
    } else {
        $attribute = explode(':', $str);
        $attributes[] = array(
            'attr_group_name' => $attribute[0],
            'attr_name'       => $attribute[1]
        );
        return $attributes;
    }
}*/
    public function getstrpos($haystack, $needle)
    {
        /* To make it compatible with PS 1.5 versions */
        if (function_exists('Tools::strpos')) {
            return Tools::strpos($haystack, $needle);
        } else {
            return strpos($haystack, $needle);
        }
    }

    /**
     * Newly added hook
     * Used for AMP
     * 24022022 HR
     */
    public function hookDisplayProductAMPDeliveryDate($params)
    {
        if ($this->is_active && $this->is_17 && $this->isDisplayed == false && Configuration::get('ED_LOCATION') >= 0) {
            // New 1.7 versions have updated the name for the  display product buttons hook
            if (!$this->isAjax() && !Configuration::get('ED_ALLOW_MULTIPLE_INSTANCES')) {
                $this->isDisplayed = true;
            }

            return $this->generateEstimatedDelivery($params);
        }
    }

    // TODO Review HR
    public function hookDisplayNtDeliveryDesiredDate($params)// client's method
    {
        if ($this->is_active && $this->context->controller instanceof NtImportProductRequestModuleFrontController) {
            $deliveries = $this->generateEstimatedDelivery($params, 0, 0, 1, 'array');
            if ($deliveries) {
                $delivery = $deliveries[0];
                $more_options = false;
                if (count($deliveries) > 1) {
                    // If more than one result, try to find the default carrier
                    foreach ($deliveries as $key => $d) {
                        if ($deliveries[$key]->dc->is_default) {
                            $d = $deliveries[$key];
                            break;
                        }
                    }
                    $more_options = true;
                }
                $this->context->smarty->assign(
                    [
                        'delivery' => $delivery,
                        'more_options' => $more_options,
                        'ed_product_summary' => html_entity_decode(Configuration::get('ED_ORDER_SUMMARY_LINE')),
                    ]
                );

                return $this->display(__FILE__, 'views/templates/hook/nt-desired-delivery.tpl');
            }
        }
    }

    // END TODO Review HR
    public function hookDisplayNtDeliveryTime($params)// client's method
    {
        if ($this->is_active && $this->context->controller instanceof NtImportProductRequestModuleFrontController) {
            $deliveries = $this->generateEstimatedDelivery($params, 0, 0, 1, 'array');
            if ($deliveries) {
                $delivery = $deliveries[0];
                $more_options = false;
                if (count($deliveries) > 1) {
                    // If more than one result, try to find the default carrier
                    foreach ($deliveries as $key => $d) {
                        if ($deliveries[$key]->dc->is_default) {
                            $d = $deliveries[$key];
                            break;
                        }
                    }
                    $more_options = true;
                }
                $this->context->smarty->assign(
                    [
                        'delivery' => $delivery,
                        'more_options' => $more_options,
                        'ed_product_summary' => html_entity_decode(Configuration::get('ED_ORDER_SUMMARY_LINE')),
                    ]
                );

                return $this->display(__FILE__, 'views/templates/hook/nt-estimate-delivery.tpl');
            }
            // print_r($delivery);
            // return $this->displayCarriersOnCart($params);
        }
    }

    public function hookDisplayProductDeliveryTime($params)
    {
        if ($this->is_active && !$this->is_17 && Configuration::get('ED_LOCATION') == -5 && $this->context->controller->php_self == 'product') {
            // It's a product
            $id_product = 0;
            $id_product_attribute = 0;
            $this->getIdProdAndAttribute($params, $id_product, $id_product_attribute);
            if ($id_product == Tools::getValue('id_product') || (Configuration::get('ED_LIST') && Configuration::get('ED_LIST_PROD'))) {
                return $this->generateEstimatedDelivery($params);
            }
        } elseif (Configuration::get('ED_LIST') && Configuration::get('ED_LIST_LOCATION') == 0 && !is_object($params['product'])) {
            return $this->showEstimatedDeliveryOnLists($params);
        }
    }

    public function hookDisplayProductListFunctionalButtons($params)
    {
        if ($this->is_active && Configuration::get('ED_LIST') && Configuration::get('ED_LIST_LOCATION') == 1) {
            return $this->showEstimatedDeliveryOnLists($params);
        }
    }

    public function hookDisplayEDInProductList($params)
    {
        if ($this->is_active && Configuration::get('ED_LIST') && Configuration::get('ED_LIST_LOCATION') == 2) {
            return $this->showEstimatedDeliveryOnLists($params);
        }
    }

    public function showEstimatedDeliveryOnLists($params)
    {
        if ($this->is_active && Configuration::get('ED_LIST')) {
            $entitys = EDTools::getControllerName();
            $add_controllers = explode(',', str_replace(' ', '', Configuration::get('ED_LIST_EXTRA_CONTROLLERS')));
            // Advanced search 4 module compatibility for the search results
            if ($entitys == 'module-pm_advancedsearch4-searchresults') {
                $entitys = 'search';
            } elseif ($entitys = 'advancedsearch4') {
                $entitys = 'search';
            }
            foreach ($this->getConfigurationFields()['ints'] as $field) {
                if ($this->getstrpos($field['name'], 'ED_LIST_') !== false
                || in_array($entitys, $add_controllers)) { // Tools::strpos does not exist on PS 1.5 Versions
                    if (Configuration::get($field['name'])) {
                        $entity = Tools::substr($field['name'], 8);
                        if ($entity == Tools::strtoupper($entitys)) {
                            if (isset($params['product'])) {
                                if (is_array($params['product'])) {
                                    // 1.5 - 1.6
                                    $id_product = $params['product']['id_product'];
                                    $id_product_attribute = $params['product']['id_product_attribute'];
                                    $quantity_wanted = (isset($params['product']['minimal_quantity']) && ($params['product']['minimal_quantity'] > 0)) ? $params['product']['minimal_quantity'] : 1;
                                } elseif (isset($params['product']->id)) {
                                    // It's 1.7
                                    $id_product = $params['product']->id;
                                    $id_product_attribute = isset($params['product']->id_product_attribute) ? $params['product']->id_product_attribute : 0;
                                    $quantity_wanted = (isset($params['product']->minimal_quantity) && ($params['product']->minimal_quantity > 0)) ? $params['product']->minimal_quantity : 1;
                                } else {
                                    return false;
                                }
                                $this->context->smarty->assign(
                                    [
                                        'dlf' => Configuration::get('ED_LIST_FORMAT'),
                                        'dldf' => Configuration::get('ED_LIST_DATE_FORMAT'),
                                        'dldf_total' => count($this->listDateFormat) - 1, // -1 to match the dldf index
                                    ]
                                );

                                return $this->generateEstimatedDelivery($params, $id_product, $id_product_attribute, $quantity_wanted, 'tpl-list');
                            }
                        }
                    }
                } else {
                    if (self::$debug_mode) {
                        $this->debugVar($entitys, 'Controller not accepted for the products list');
                    }
                }
            }
        }
    }

    /* TODO REVIEW
    * Revisar si el pedido no es vÃ¡lid que passa amb la data, s'actualitza al moment o desprÃ©s de validar el pago
    */
    public function prepareEDForOrderDetails($params)
    {
        $order = $this->getOrderFromParams($params);
        if ($order !== false) {
            $deliveries_by_product = [];
            if ($this->adv_mode
                && Configuration::get('ED_DISABLE_AFTER_SHIPPING')
                && $this->orderHasShipped($order, Configuration::get('PS_OS_SHIPPING'))) {
                return false;
            }
            $picking = '';
            if ($order->valid == 0) {
                $dh = new DeliveryHelper();
                $picking = $dh->calculatePickingFromOrder($order, 'base_df');
            }
            $this->context->smarty->assign([
                'id_order' => $order->id,
                'picking' => $picking,
                'valid_date' => $this->getOrderValidationDate($order->id),
                'date_add' => $order->date_add,
                'undefined_validate_range' => ['min' => Configuration::get('ed_undefined_validate_min'), 'max' => Configuration::get('ed_undefined_validate_max')],
            ]);
            $this->setBetterLocales();
            $dates = $this->getEdOrder($order->id);
            $this->context->smarty->assign([
                'edclass' => Configuration::get('ed_class'),
                'edbackground' => Configuration::get('ed_custombg'),
                'edborder' => Configuration::get('ed_customborder'),
                'edstyle' => Configuration::get('ED_STYLE'),
                'force_date' => $order->date_add,
            ]);
            if ($dates && count($dates) > 0) {
                // Get the individual delivery dates if the order is originally in that mode
                if ($dates['individual_dates']) {
                    $deliveries_by_product = $this->displayOrderIndividualProductDelivery($order, $params);
                }

                // If it's a special message, assign it to the msg var
                if ($dates['undefined_delivery']) {
                    $dates['msg'] = $this->getUndefinedMessage('order');
                } elseif ($dates['is_virtual']) {
                    $dates['msg'] = $this->getVirtualMessage('order');
                }
                // Force date display in Back Office orders
                $dates = $this->setDateFormatForCarrier($dates, 'email_df', self::getTot());
                $this->context->smarty->assign([
                    'edcarrier' => $dates,
                    'dates_by_product' => (int) $dates['individual_dates'],
                    'deliveries' => $deliveries_by_product,
                ]);
            }

            return true;
        }

        return false;
    }

    private function getOrderValidationDate($id_order)
    {
        return Db::getInstance()->getValue('SELECT oh.date_add FROM ' . _DB_PREFIX_ . 'order_history oh LEFT JOIN ' . _DB_PREFIX_ . 'order_state USING(id_order_state) WHERE id_order = ' . (int) $id_order . ' AND logable = 1 ORDER BY oh.date_add ASC');
    }

    public function displayOrderIndividualProductDelivery($order, $params)
    {
        $deliveries = [];
        $both_deliveries = [false, false];
        $relandavail = [];

        // TODO Implement release and available
        $all_virtual = $is_available = $is_release = 0;
        $products = $this->getProductListFromOrder($order);
        if (EDelivery::allVirtual($products)) {
            $all_virtual = 1;
        }

        // TODO Get each Product Attributes
        $sql = 'SELECT eoi.*, od.product_id AS id_product, od.product_attribute_id AS id_product_attribute 
            FROM `' . _DB_PREFIX_ . 'ed_order_individual` eoi 
            LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (eoi.id_order_detail = od.id_order_detail)
            WHERE eoi.id_order = ' . (int) $order->id;
        $ed_individual_products = DB::getInstance()->executeS($sql);
        if (count($ed_individual_products) > 0) {
            $deliveries = [];
            // $df = $this->dateFormat[Configuration::get('ED_DATE_TYPE')];
            foreach ($ed_individual_products as &$delivery) {
                $product = new Product((int) $delivery['id_product']);
                $delivery['name'] = $product->name[$this->context->language->id];
                $delivery['attributes'] = $this->getAttrFromIpa($delivery['id_product_attribute']);
                // $product->getAttributeCombinationsById($delivery['id_product_attribute'], $this->context->language->id);

                $delivery['delivery_cmp_min'] = $delivery['delivery_min'];
                $delivery['delivery_cmp_max'] = $delivery['delivery_max'];

                $delivery = $this->setDateFormatForCarrier($delivery, 'base_df', self::getTot());

                $deliveries[] = $delivery;
            }
        }

        return $deliveries;
    }

    private function orderHasShipped($order, $id_order_state)
    {
        return Db::getInstance()->getValue('SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order_state = ' . (int) $id_order_state . ' AND id_order = ' . (int) $order->id) == $id_order_state;
    }

    private function getOrderFromParams($params)
    {
        if (isset($params['id_order'])) {
            return new Order((int) $params['id_order']);
        } elseif (isset($params['order']->id)) {
            return $params['order'];
        } elseif (isset($params['objOrder']->id)) {
            return $params['objOrder'];
        }

        return false;
    }

    public function hookDisplayAdminOrder($params)
    {
        if (!Configuration::get('ED_ORDER')) {
            return;
        }
        if ($this->prepareEDForOrderDetails($params) === false) {
            return;
        }
        $order = $this->getOrderFromParams($params);
        $this->context->smarty->assign(
            [
                'new_template' => version_compare(_PS_VERSION_, '1.7.7', '>='),
                'ed_token' => Tools::getAdminToken('AdminOrders'),
                'ed_ajax' => $this->_path,
                'admin_url' => $this->context->link->getAdminLink('AdminEstimatedDelivery', true),
                'calendar_order' => $this->isCalendarDateOrder($order->id),
                'edorder' => $order,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/ed-admin-orders.tpl');
    }

    public function hookDisplayOrderDetail($params)
    {
        if ($this->is_active && Configuration::get('ED_ORDER')) {
            SmartForm::init($this);
            if ($this->prepareEDForOrderDetails($params)) {
                // $this->context->smarty->assign(array('ed_virtual_msg' => Configuration::get('ed_virtual_msg_'.$this->context->language->id)));
                return $this->display(__FILE__, 'views/templates/front/ed-order-details.tpl');
            }
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if ($this->is_active && Configuration::get('ED_ORDER')) {
            if ($this->prepareEDForOrderDetails($params)) {
                return $this->display(__FILE__, 'views/templates/front/ed-order-details.tpl');
            }
        }
    }

    /* Product Tabs for PS 1.7, if theme has the option */
    public function hookDisplayProductExtraContent($params)
    {
        if ($this->is_active && Configuration::get('ED_LOCATION') == -1) {
            $content = $this->generateEstimatedDelivery($params);
            if ($content != '' && $content != false) {
                $productExtraContent = new PrestaShop\PrestaShop\Core\Product\ProductExtraContent();
                $productExtraContent->setTitle($this->l('Estimated Delivery'));
                $productExtraContent->setContent($content);

                return [$productExtraContent];
            }
        }
    }

    public function hookDisplayProductDeliveryDate($params)
    {
        if (!isset($params['id_product']) || (int) $params['id_product'] == 0) {
            return;
        }
        $ipa = (isset($params['id_product_attribute']) && (int) $params['id_product_attribute'] > 0) ? $params['id_product_attribute'] : 0;
        $p = new Product((int) $params['id_product']);
        $this->showEstimatedDelivery($id_product, $ipa);
    }

    private function reviewUpdates()
    {
        $output = '';
        require_once dirname(__FILE__) . '/src/review-install.php';

        return $output;
    }

    private function reviewInstall($case, $columnNames = '')
    {
        require_once dirname(__FILE__) . '/sql/review-install.php';
    }

    /* Check if the module is in Sandbox Mode to restrict the access to the allowed IPs */
    private function isTestModeActive()
    {
        if (Configuration::get('ED_TEST_MODE')) {
            $ips = explode(',', Configuration::get('ED_TEST_MODE_IPS'));
            $current_ips = $this->getCurrentUserIp();
            $c = count($ips);
            for ($i = 0; $i < $c; ++$i) {
                if (in_array(trim($ips[$i]), $current_ips)) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    /* Get the current user IP */
    private function getCurrentUserIP()
    {
        $keys_to_check = [
            'HTTP_X_SUCURI_CLIENTIP',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];
        $ret = [];
        foreach ($keys_to_check as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                if (Tools::strpos($_SERVER[$key], ',') !== false) {
                    $ret[] = explode(',', $_SERVER[$key])[0];
                } else {
                    $ret[] = $_SERVER[$key];
                }
            }
        }

        return $ret;
    }

    private function getMySQLDateFormat()
    {
        $separators = ['/', '-', '|.'];
        foreach ($separators as $sep) {
            if (Tools::strpos($this->context->language->date_format_lite, $sep) !== false) {
                $df = explode($sep, $this->context->language->date_format_lite);
                foreach ($df as &$f) {
                    $f = '%' . $f;
                }

                return implode($sep, $df);
            }
        }
    }

    private function getDebugMode()
    {
        if (!defined('_PS_ADMIN_DIR_') && $this->isAjax()) {
            return 0;
        }
        if (Configuration::get('ed_debug_var')) {
            $debug_ips = explode(',', Configuration::get('ed_debug_var_ip'));
            if (@in_array($_SERVER['REMOTE_ADDR'], $debug_ips)
                || @in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $debug_ips)
                || @in_array($_SERVER['HTTP_X_REAL_IP'], $debug_ips)
                || empty($debug_ips)) {
                return true;
            }
        }

        return false;
    }

    private function getDebugTime()
    {
        if (Configuration::get('ed_debug_time')) {
            $debug_ips = explode(',', Configuration::get('ed_debug_var_ip'));
            if (@in_array($_SERVER['REMOTE_ADDR'], $debug_ips)
                || @in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $debug_ips)
                || @in_array($_SERVER['HTTP_X_REAL_IP'], $debug_ips)
                || empty($debug_ips)) {
                return true;
            }
        }

        return false;
    }

    private function saveDebugTime($section_name, $setInitialTime = false)
    {
        $dec = 4;
        $now = microtime(true);
        if ($setInitialTime) {
            self::$microtime_init = $now;
        }
        $microtime = isset(self::$microtime_init) ? self::$microtime_init : self::$microtime;
        if (!empty(self::$debug_times)) {
            $c = count(self::$debug_times) - 1;
            $section_time = self::$debug_times[$c]['current_micro'];
        } else {
            $section_time = $now - $microtime;
        }
        self::$debug_times[] = [
            'section_name' => $section_name,
            'current_micro' => $now,
            'computed_time' => number_format($now - $microtime, $dec),
            'section_time' => number_format($now - $section_time, $dec),
            'total_time' => number_format($now - self::$microtime, $dec),
        ];
    }

    private function encrypt($text)
    {
        if (method_exists('Tools', 'encrypt')) {
            return Tools::encrypt($text);
        }

        return Tools::hash($text);
    }

    private function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function isAdvPicking()
    {
        return $this->adv_picking;
    }

    /**
     * Debug Variables
     *
     * @param var - variable to print
     * @param title - Title to show before printing the variable
     */
    public function debugVar($var, $title = '')
    {
        $nl = $this->print_debug ? SmartForm::openTag('br') . "\n" : "\n";
        $output = '';
        if (is_array($var) || is_object($var)) {
            // It's an object or array, pretty print it
            $output .= ($title != '' ? $title . ': ' : '') . $nl;
            $output .= ($this->print_debug ? $this->prettyPrint_r($var) : print_r($var, true)) . $nl;
        } else {
            // It's a regular variable, print the title and the value
            $nl2 = Tools::strlen($var) > 50 ? $nl : '';
            $output .= str_replace(':', '', $title) . ($var !== '' ? ': ' . $nl2 . $var : '') . $nl;
            if ($this->print_debug) {
                $output .= '______________________' . $nl;
            }
        }
        echo $this->print_debug ? str_replace('\n', SmartForm::openTag('br'), $output) : '<!-- ' . $output . ' -->';
    }

    private function prettyPrint_r($var)
    {
        return trim(SmartForm::openTag('pre') . print_r($var, true) . SmartForm::closeTag('pre'));
    }
}
