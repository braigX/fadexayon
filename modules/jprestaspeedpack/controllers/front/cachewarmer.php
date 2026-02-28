<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/../../jprestaspeedpack.php';

class jprestaspeedpackCacheWarmerModuleFrontController extends ModuleFrontController
{
    const SEPARATOR = "\t";

    const CONTROLLER_INDEX = 1;
    const CONTROLLER_PRODUCT = 2;
    const CONTROLLER_CATEGORY = 3;
    const CONTROLLER_CMS = 4;
    const CONTROLLER_CMS_CATEGORY = 5;
    const CONTROLLER_SUPPLIER = 6;
    const CONTROLLER_MANUFACTURER = 7;
    const CONTROLLER_CONTACT = 8;
    const CONTROLLER_SITEMAP = 9;
    const CONTROLLER_NEW_PRODUCTS = 10;
    const CONTROLLER_PRICE_DROPS = 11;
    const CONTROLLER_BEST_SALES = 12;

    private $start_time;

    private $url_count = 0;

    public function __construct()
    {
        parent::__construct();
        $this->start_time = microtime(true);
    }

    public function init()
    {
        try {
            // Disable redirect from autolanguagecurrency module
            $this->context->cookie->autolocation = 1;
            parent::init();
        } catch (Throwable $e) {
            header('HTTP/1.0 500 ' . $e->getMessage());
            JprestaUtils::addLog('PageCache | An error occured when the cache-warmer tried to get the list of URLs (init): ' . $e->getMessage() . '. ' . JprestaUtils::jTraceEx($e), 2);
            exit("\n** error **\n" . JprestaUtils::jTraceEx($e));
        }
    }

    public function initContent()
    {
        try {
            parent::initContent();

            if (JprestaUtils::isModuleEnabled('jprestaspeedpack')) {
                $token = Tools::getValue('token', '');
                $goodToken = JprestaUtils::getSecurityToken();
                if (!$goodToken || strcmp($goodToken, $token) === 0) {
                    if (!Configuration::get('pagecache_debug')) {
                        // Make sure that the column used_by_cw has been created, the upgrade script was not executed
                        // on multiple customers but I don't know why
                        if (!JprestaUtils::dbColumnExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, 'used_by_cw')) {
                            JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '` ADD `used_by_cw` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER v_js');
                        }

                        self::checkSecurityParameters();

                        $action = Tools::getValue('action');
                        if ($action === 'GetShopInfos') {
                            self::processGetShopInfos(Tools::getValue('shopId'));
                        } elseif ($action === 'GetReportStats') {
                            $start = new DateTime('@' . (float) Tools::getValue('start'));
                            $end = new DateTime('@' . (float) Tools::getValue('end'));
                            $this->processGetReportStats(Tools::getValue('shopId'), $start, $end);
                        }
                    } else {
                        header('HTTP/1.0 503 Module is in test mode');
                        exit('Module is in test mode');
                    }
                } else {
                    header('HTTP/1.0 403 Bad token');
                    exit('Bad token ' . $token);
                }
            } else {
                // Cannot be called when module is disabled but...
                header('HTTP/1.0 503 Module not enabled');
                exit('Module not enabled');
            }
        } catch (Throwable $e) {
            header('HTTP/1.0 500 ' . $e->getMessage());
            JprestaUtils::addLog('PageCache | An error occured when the cache-warmer tried to get the list of URLs (initContent): ' . $e->getMessage() . '. ' . JprestaUtils::jTraceEx($e), 2);
            exit("\n** error **\n" . JprestaUtils::jTraceEx($e));
        }
        header('HTTP/1.0 404 Not found');
        exit('Not found');
    }

    private function processGetReportStats($shopId, $start, $end)
    {
        $shopArray = Shop::getShop((int) $shopId);

        if (!$shopArray) {
            header('HTTP/1.0 404 Shop not found');
            exit('Shop not found #' . $shopId);
        }

        try {
            // Set the context shop for external modules to generate links for this shop
            Shop::setContext(/* Shop::CONTEXT_SHOP */ 1, $shopId);

            ob_end_clean();
            header('Content-Type: application/json');

            $infos = [
                'hit_missed' => PageCacheDAO::getPerformances($shopId),
                'ttfb_all' => $this->getTTFBDatas($shopId, $start, $end),
                'ttfb_home' => $this->getTTFBDatas($shopId, $start, $end, 'index'),
                'ttfb_products' => $this->getTTFBDatas($shopId, $start, $end, 'product'),
                'ttfb_categories' => $this->getTTFBDatas($shopId, $start, $end, 'category'),
            ];
            exit(json_encode($infos));
        } catch (Throwable $e) {
            header('HTTP/1.0 500 ' . $e->getMessage());
            JprestaUtils::addLog('PageCache | An error occured when the cache-warmer tried to get statistics: ' . $e->getMessage() . '. ' . JprestaUtils::jTraceEx($e), 2);
            exit("\n** error **\n" . JprestaUtils::jTraceEx($e));
        }
    }

    private function getTTFBDatas($shopId, $start, $end, $controller = null)
    {
        $id_controller = Jprestaspeedpack::getManagedControllerId($controller);
        $whereClause = 'WHERE id_shop=' . (int) $shopId;
        if ($id_controller) {
            $whereClause .= ' AND id_controller=' . (int) $id_controller;
        }
        $whereClause .= ' AND date_add >= FROM_UNIXTIME(' . $start->format('U') . ') AND date_add <= FROM_UNIXTIME(' . $end->format('U') . ')';
        $query = 'SELECT UNIX_TIMESTAMP(day_add) AS day,
            ROUND(AVG(ttfb_ms_hit_server)) AS ttfb_ms_hit_server,
            ROUND(AVG(ttfb_ms_hit_static)) AS ttfb_ms_hit_static,
            ROUND(AVG(ttfb_ms_hit_browser)) AS ttfb_ms_hit_browser,
            ROUND(AVG(ttfb_ms_hit_bfcache)) AS ttfb_ms_hit_bfcache,
            ROUND(AVG(ttfb_ms_missed)) AS ttfb_ms_missed
            FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ' . $whereClause . ' GROUP BY day_add ORDER BY day_add ASC;';
        $rows = JprestaUtils::dbSelectRows($query);
        $missed = $server = $static = $browser = $bf = [];
        foreach ($rows as $row) {
            $missed[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_missed']];
            $server[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_server']];
            $static[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_static']];
            $browser[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_browser']];
            $bf[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_bfcache']];
        }
        $datas = [
            [
                'values' => $missed,
                'key' => 'none',
            ],
            [
                'values' => $server,
                'key' => 'server',
            ],
            [
                'values' => $static,
                'key' => 'static',
            ],
            [
                'values' => $browser,
                'key' => 'browser',
            ],
            [
                'values' => $bf,
                'key' => 'bf',
            ],
        ];

        $query = 'SELECT date_format(MIN(day_add), \'%Y-%m-%d\') AS start_date,
            SUM(1) AS total_count
            FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ' . $whereClause;
        $rows = JprestaUtils::dbSelectRows($query);
        $start_date = 0;
        $total_count = 0;
        if (count($rows) > 0) {
            $start_date = $rows[0]['start_date'];
            $total_count = (int) $rows[0]['total_count'];
        }

        return ['datas' => $datas, 'start_date' => $start_date, 'total_count' => $total_count];
    }

    private function processGetShopInfos($shopId)
    {
        $shopArray = Shop::getShop((int) $shopId);

        if (!$shopArray) {
            header('HTTP/1.0 404 Shop not found');
            exit('Shop not found #' . $shopId);
        }

        try {
            // Set the context shop for external modules to generate links for this shop
            Shop::setContext(/* Shop::CONTEXT_SHOP */ 1, $shopId);

            $shop = new Shop($shopId);
            $settings = JprestaCacheWarmerSettings::get($shopId);

            ob_end_clean();
            header('Content-Type: text/plain');

            echo $this->module->version . self::SEPARATOR;
            echo _PS_VERSION_ . self::SEPARATOR;
            echo $shop->getBaseURL(true) . self::SEPARATOR;
            echo $settings->getPagesCount() . self::SEPARATOR;
            echo $settings->getContextCount();
            echo "\n";

            if (!$this->getShopUrls($settings)) {
                echo "...\n";
            } else {
                // This will inform the cache-warmer that there is no more data to wait.
                echo ".\n";
            }
        } catch (Throwable $e) {
            header('HTTP/1.0 500 ' . $e->getMessage());
            JprestaUtils::addLog('PageCache | An error occured when the cache-warmer tried to get the list of URLs: ' . $e->getMessage() . '. ' . JprestaUtils::jTraceEx($e), 2);
            exit("\n** error **\n" . JprestaUtils::jTraceEx($e));
        }

        exit;
    }

    /**
     * @return bool true if max execution time has been reached
     */
    private function isMaxExecutionTime()
    {
        static $max_in_seconds = null;
        if ($max_in_seconds === null) {
            $userDefinedMax = (int) Configuration::get('pagecache_max_exec_time');
            $serverDefinedMax = (int) Tools::getValue('timeout_s', 300);
            $max_in_seconds = 0.8 * min(8 * 60, max(1, min($userDefinedMax, $serverDefinedMax)));
        }
        $spent = microtime(true) - $this->start_time;

        return $spent >= $max_in_seconds;
    }

    private function isMaxUrlCount()
    {
        static $max_url_count = null;
        if ($max_url_count === null) {
            $max_url_count = (int) Tools::getValue('max', 100000);
        }

        return $this->url_count >= $max_url_count;
    }

    private static function displaySuppliers()
    {
        return Configuration::get('PS_DISPLAY_SUPPLIERS');
    }

    private static function displayManufacturers()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return Configuration::get('PS_DISPLAY_MANUFACTURERS');
        }

        return true;
    }

    private static function displayBestSales()
    {
        return Configuration::get('PS_DISPLAY_BEST_SELLERS');
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     *
     * @return bool true if all URLs have been returned, false if the script was too long and all URLs have not been returned
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getShopUrls($settings)
    {
        $link = new Link();
        $shop = new Shop($settings->id_shop);

        foreach ($settings->getContextsToWarmup() as $context) {
            if ($context['group']) {
                $anonymousCustomer = new JprestaCustomer();
                $anonymousCustomer = $anonymousCustomer->getByEmail($context['group']);
                if ($anonymousCustomer) {
                    if (JprestaCustomer::isVisitor($anonymousCustomer->id)) {
                        // The Visitor group must not be specified
                        $context['group'] = null;
                    } else {
                        // Simulate the connection of the user so restrictions/acesses are correctly applied
                        $this->context->customer = $anonymousCustomer;
                        $this->context->cart = new Cart();
                        $this->context->cookie->id_customer = $this->context->customer->id;
                        // Used by PGA module to apply restrictions on products and categories
                        $this->context->cart->id_customer = $this->context->customer->id;
                    }
                } else {
                    // Cannot find the anonymous group (should not happen), ignore it
                    continue;
                }
            }
            if (!$context['group']) {
                // Restore the anonymous context
                $this->context->customer = new Customer();
                $this->context->cart = new Cart();
                $this->context->cookie->id_customer = null;
            }

            //
            // GENERIC PAGES
            //
            if (Configuration::get('pagecache_index') && array_key_exists('index', $settings->controllers) && $settings->controllers['index']['checked']) {
                $this->addPage($settings, $link, 'index', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            if (Configuration::get('pagecache_newproducts') && array_key_exists('newproducts', $settings->controllers) && $settings->controllers['newproducts']['checked']) {
                $this->addPage($settings, $link, 'new-products', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            if (Configuration::get('pagecache_pricesdrop') && array_key_exists('pricesdrop', $settings->controllers) && $settings->controllers['pricesdrop']['checked']) {
                $this->addPage($settings, $link, 'prices-drop', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            if (Configuration::get('pagecache_contact') && array_key_exists('contact', $settings->controllers) && $settings->controllers['contact']['checked']) {
                $this->addPage($settings, $link, 'contact', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            if (Configuration::get('pagecache_sitemap') && array_key_exists('sitemap', $settings->controllers) && $settings->controllers['sitemap']['checked']) {
                $this->addPage($settings, $link, 'sitemap', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            if (self::displayBestSales() && (int) Configuration::get('pagecache_bestsales') && array_key_exists('bestsales', $settings->controllers) && $settings->controllers['bestsales']['checked']) {
                $this->addPage($settings, $link, 'best-sales', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
            }
            //
            // MANUFACTURERS
            //
            if (self::displayManufacturers() && Configuration::get('pagecache_manufacturer') && array_key_exists('manufacturer', $settings->controllers) && $settings->controllers['manufacturer']['checked']) {
                // List of manufacturers
                $this->addPage($settings, $link, 'manufacturer', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
                // Each manufacturers
                $sql = 'SELECT c.id_manufacturer
                    FROM `' . _DB_PREFIX_ . 'manufacturer` c' . $shop->addSqlAssociation('manufacturer', 'c') . '
                    WHERE c.`active` = 1';
                $id_manufacturer_rows = Db::getInstance()->executeS($sql);
                foreach ($id_manufacturer_rows as $id_manufacturer_row) {
                    $this->addManufacturer($settings, $link, $id_manufacturer_row['id_manufacturer'], $context);
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }
            }
            //
            // SUPPLIERS
            //
            if (self::displaySuppliers() && Configuration::get('pagecache_supplier') && array_key_exists('supplier', $settings->controllers) && $settings->controllers['supplier']['checked']) {
                // List of suppliers
                $this->addPage($settings, $link, 'supplier', $context);
                if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                    return false;
                }
                // Each suppliers
                $sql = 'SELECT c.id_supplier
                    FROM `' . _DB_PREFIX_ . 'supplier` c' . $shop->addSqlAssociation('supplier', 'c') . '
                    WHERE c.`active` = 1';
                $id_supplier_rows = Db::getInstance()->executeS($sql);
                foreach ($id_supplier_rows as $id_supplier_row) {
                    $this->addSupplier($settings, $link, $id_supplier_row['id_supplier'], $context);
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }
            }
            //
            // PRODUCTS
            //
            if (Configuration::get('pagecache_product') && array_key_exists('product', $settings->controllers) && $settings->controllers['product']['checked']) {
                $sql = 'SELECT p.id_product FROM `' . _DB_PREFIX_ . 'product` p' . $shop->addSqlAssociation('product', 'p');
                $whereClauses = [];
                $whereClauses[] = 'product_shop.`active` = 1';
                if (JprestaUtils::isModuleEnabled('ndk_advanced_custom_fields')) {
                    $whereClauses[] = 'p.reference NOT LIKE \'custom-%\'';
                    $whereClauses[] = 'p.supplier_reference NOT LIKE \'custom-%\'';
                }
                $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
                $id_product_rows = Db::getInstance()->executeS($sql);
                foreach ($id_product_rows as $id_product_row) {
                    if (!Configuration::get('pagecache_cache_customizable')) {
                        // Check that product is not customizable
                        $customizationFieldCount = (int) JprestaUtils::dbGetValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'customization_field` WHERE `id_product` = ' . (int) $id_product_row['id_product']);
                        if ($customizationFieldCount) {
                            // Skip this product
                            continue;
                        }
                    }

                    if (!$this->addProduct($settings, $link, $shop, $id_product_row['id_product'], $context)) {
                        return false;
                    }
                }
            }
            //
            // CATEGORIES
            //
            if (Configuration::get('pagecache_category') && array_key_exists('category', $settings->controllers) && $settings->controllers['category']['checked']) {
                $sql = 'SELECT c.id_category
                    FROM `' . _DB_PREFIX_ . 'category` c' . $shop->addSqlAssociation('category', 'c') . '
                    WHERE c.`active` = 1 AND c.is_root_category = 0 AND c.id_parent > 0';
                $id_category_rows = Db::getInstance()->executeS($sql);
                foreach ($id_category_rows as $id_category_row) {
                    $this->addCategory($settings, $link, $id_category_row['id_category'], $context);
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }
            }
            //
            // CMS
            //
            if (Configuration::get('pagecache_cms') && array_key_exists('cms', $settings->controllers) && $settings->controllers['cms']['checked']) {
                $sql = 'SELECT c.id_cms
                    FROM `' . _DB_PREFIX_ . 'cms` c' . $shop->addSqlAssociation('cms', 'c') . '
                    WHERE c.`active` = 1';
                $id_cms_rows = Db::getInstance()->executeS($sql);
                foreach ($id_cms_rows as $id_cms_row) {
                    $this->addCMS($settings, $link, $id_cms_row['id_cms'], $context);
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }

                //
                // CMS CATEGORIES
                //
                $sql = 'SELECT c.id_cms_category
                    FROM `' . _DB_PREFIX_ . 'cms_category` c' . $shop->addSqlAssociation('cms_category', 'c') . '
                    WHERE c.`active` = 1';
                $id_cms_category_rows = Db::getInstance()->executeS($sql);
                foreach ($id_cms_category_rows as $id_cms_category_row) {
                    $this->addCMSCategory($settings, $link, $id_cms_category_row['id_cms_category'], $context);
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }
            }
            //
            // Pages generated by other modules
            //
            $id_lang = (int) Language::getIdByIso($context['language']);
            foreach ($settings->controllers as $controllerName => $controllerSettings) {
                if ($controllerSettings['checked']
                    && JprestaUtilsModule::isModuleController($controllerName)
                    && JprestaUtilsModule::canBeWarmed($controllerName)
                ) {
                    $timeout_minutes = (int) Configuration::get('pagecache_' . str_replace('-', '', $controllerName) . '_timeout');

                    $urls = JprestaUtilsModule::getAllURLs($controllerName, $id_lang);
                    foreach ($urls as $url) {
                        $this->addURL($settings, $url, 0, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
                        if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $controller string
     * @param $context
     */
    private function addPage($settings, $link, $controller, $context)
    {
        $timeout_minutes = (int) Configuration::get('pagecache_' . str_replace('-', '', $controller) . '_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);
        $url = $link->getPageLink($controller, null, $id_lang, null, false, $settings->id_shop);
        switch ($controller) {
            case 'index':
                $id_controller = self::CONTROLLER_INDEX;
                break;
            case 'new-products':
                $id_controller = self::CONTROLLER_NEW_PRODUCTS;
                break;
            case 'prices-drop':
                $id_controller = self::CONTROLLER_PRICE_DROPS;
                break;
            case 'contact':
                $id_controller = self::CONTROLLER_CONTACT;
                break;
            case 'sitemap':
                $id_controller = self::CONTROLLER_SITEMAP;
                break;
            case 'best-sales':
                $id_controller = self::CONTROLLER_BEST_SALES;
                break;
            default:
                $id_controller = 0;
        }
        $this->addURL($settings, $url, $id_controller, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $id
     */
    private function addManufacturer($settings, $link, $id, $context)
    {
        $timeout_minutes = (int) Configuration::get('pagecache_manufacturer_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);
        $url = $link->getManufacturerLink((int) $id, null, $id_lang, $settings->id_shop);
        $this->addURL($settings, $url, self::CONTROLLER_MANUFACTURER, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $id
     */
    private function addSupplier($settings, $link, $id, $context)
    {
        $timeout_minutes = (int) Configuration::get('pagecache_supplier_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);
        $url = $link->getSupplierLink((int) $id, null, $id_lang, $settings->id_shop);
        $this->addURL($settings, $url, self::CONTROLLER_SUPPLIER, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $id
     */
    private function addCMS($settings, $link, $id, $context)
    {
        $timeout_minutes = (int) Configuration::get('pagecache_cms_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);
        $url = $link->getCMSLink((int) $id, null, null, $id_lang, $settings->id_shop);
        $this->addURL($settings, $url, self::CONTROLLER_CMS, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $id
     */
    private function addCMSCategory($settings, $link, $id, $context)
    {
        $timeout_minutes = (int) Configuration::get('pagecache_cms_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);
        $url = $link->getCMSCategoryLink((int) $id, null, $id_lang, $settings->id_shop);
        $this->addURL($settings, $url, self::CONTROLLER_CMS_CATEGORY, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $id
     */
    private function addCategory($settings, $link, $id, $context)
    {
        $cat = new Category($id);
        if ($cat->checkAccess(isset($context->customer) ? $context->customer->id : 0)) {
            $timeout_minutes = (int) Configuration::get('pagecache_category_timeout');
            $id_lang = (int) Language::getIdByIso($context['language']);
            $url = $link->getCategoryLink((int) $id, null, $id_lang, null, $settings->id_shop);
            $this->addURL($settings, $url, self::CONTROLLER_CATEGORY, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
        }
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param $link LinkCore
     * @param $shop ShopCore
     * @param $id_product integer
     *
     * @throws PrestaShopException
     */
    private function addProduct($settings, $link, $shop, $id_product, $context)
    {
        if ($settings->filter_products_cats_ids) {
            $sqlFilterCats = 'SELECT id_category
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`
                WHERE p.id_product=' . (int) $id_product . '
                    AND cp.`id_category` IN  (' . $settings->filter_products_cats_ids . ')
                    AND product_shop.`visibility` IN ("both", "catalog")
                    AND product_shop.`active` = 1
                LIMIT 1;';
            if (!JprestaUtils::dbGetValue($sqlFilterCats)) {
                return true;
            }
        }

        // Sometimes product combinaisons have the same URL so we need to check it to avoid warming the same URL multiple times
        // I don't do it at the global level to avoid consumming to much memory
        $urls = [];

        $timeout_minutes = (int) Configuration::get('pagecache_product_timeout');
        $id_lang = (int) Language::getIdByIso($context['language']);

        // Gettting the product object here will reduce SQL query count
        $product = new Product((int) $id_product, false, $id_lang, $settings->id_shop);

        // Avoid 403
        if (!$product->checkAccess(isset($context->customer) ? $context->customer->id : 0)) {
            return true;
        }

        $needSimpleUrl = true;
        $dispatcher = Dispatcher::getInstance();
        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'id_product_attribute')) {
            // Check if it is a product with combinations
            $sql = 'SELECT pa.id_product_attribute
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa' . $shop->addSqlAssociation('product_attribute', 'pa') . '
                WHERE pa.id_product = ' . (int) $id_product;
            $ipa_rows = Db::getInstance()->executeS($sql);
            if ($ipa_rows && count($ipa_rows) > 0) {
                // Product with combinations
                foreach ($ipa_rows as $ipa_row) {
                    $needSimpleUrl = false;
                    // Add URL for all combinations
                    $url = $link->getProductLink($product, null, null, null, $id_lang, $settings->id_shop, $ipa_row['id_product_attribute']);
                    $url_no_anchor = strtok($url, '#');
                    if (!array_key_exists($url_no_anchor, $urls)) {
                        $urls[$url_no_anchor] = true;
                        $this->addURL($settings, $url_no_anchor, self::CONTROLLER_PRODUCT, $timeout_minutes, $id_lang,
                            $context['currency'], $context['device'], $context['country'], $context['group'],
                            isset($context['specifics']) ? $context['specifics'] : null);
                    }
                    if ($this->isMaxExecutionTime() || $this->isMaxUrlCount()) {
                        return false;
                    }
                }
            }
        }

        if ($needSimpleUrl) {
            // Simple product (even products with combinations have a simple URL)
            $url = $link->getProductLink($product, null, null, null, $id_lang, $settings->id_shop);
            $urls[$url] = true;
            $this->addURL($settings, $url, self::CONTROLLER_PRODUCT, $timeout_minutes, $id_lang, $context['currency'], $context['device'], $context['country'], $context['group'], isset($context['specifics']) ? $context['specifics'] : null);
        }

        return true;
    }

    /**
     * @param $settings JprestaCacheWarmerSettings
     * @param string $url string
     * @param int $id_controller One of self::CONTROLLER_*
     * @param int $timeout_minutes Configured timeout
     * @param string $iso_currency A valid currency ISO value or null
     * @param string $device 'desktop' or 'mobile'
     * @param string $iso_country A valid country ISO value or null
     * @param string $group Email of the group or null
     * @param int $id_specifics
     */
    private function addURL($settings, $url, $id_controller, $timeout_minutes, $id_lang, $iso_currency, $device, $iso_country, $group, $id_specifics)
    {
        static $baseUrl = null;
        static $baseUrlFromRoot = null;
        if ($baseUrl === null) {
            $shop = new Shop($settings->id_shop);
            $baseUrl = $shop->getBaseURL(true);
            $baseUrlFromRoot = $shop->getBaseURL(true, false);
        }

        if (Jprestaspeedpack::isExcludedByRegex($url)) {
            // This URL will not be cached
            return;
        }

        static $lgseoredirect = null;
        if ($lgseoredirect === null) {
            $lgseoredirect = JprestaUtils::isModuleEnabled('lgseoredirect') && JprestaUtils::dbTableExists(_DB_PREFIX_ . 'lgseoredirect');
            if ($lgseoredirect && !JprestaUtils::dbIndexExists(_DB_PREFIX_ . 'lgseoredirect', ['url_old', 'id_shop'])) {
                JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . 'lgseoredirect` ADD INDEX `url_old_id_shop` (`url_old`(250), `id_shop`);');
            }
        }
        if ($lgseoredirect) {
            if (self::isRedirectedByLGSeoRedirect('/' . self::reduceUrl($baseUrl, $url), $settings->id_shop)) {
                return;
            }
        }

        static $arseopro = null;
        if ($arseopro === null) {
            $arseopro = JprestaUtils::isModuleEnabled('arseopro') && JprestaUtils::dbTableExists(_DB_PREFIX_ . 'arseopro_redirect');
        }
        if ($arseopro) {
            if (self::isRedirectedByArSeoPro(self::reduceUrl($baseUrlFromRoot, $url), $settings->id_shop)) {
                return;
            }
        }

        static $ecseo = null;
        if ($ecseo === null) {
            $ecseo = JprestaUtils::isModuleEnabled('ec_seo') && JprestaUtils::dbTableExists(_DB_PREFIX_ . 'ec_seo_redirect');
        }
        if ($ecseo) {
            if (self::isRedirectedByEcSeo($url, $settings->id_shop)) {
                return;
            }
        }

        $cacheKey = self::getCacheKeyFromParams($settings, $id_lang, $iso_currency, $device, $iso_country, $group, $id_specifics);
        $cacheKey->add('url', $url);
        $stats = PageCacheDAO::getStatsByCacheKey($cacheKey);
        if (!$stats) {
            $ttl = 0;
            $priority = 1000;
        } else {
            $timeout_minutes_to_use = $timeout_minutes;
            if ($timeout_minutes < 0) {
                // Timeout is defined to infinity
                $timeout_minutes_to_use = PHP_INT_MAX;
            }
            $ttl = $stats['deleted'] ? 0 : max(0, $timeout_minutes_to_use - $stats['max_age_minutes']);
            $priority = $stats['sum_hit'] + $stats['sum_missed'];
        }
        if ($ttl < (24 * 60)) {
            echo self::reduceUrl($baseUrl, $this->specificTreatmentOnUrl($url)) . self::SEPARATOR;
            echo $priority . self::SEPARATOR;
            echo $device . self::SEPARATOR;
            echo $iso_currency . self::SEPARATOR;
            echo ($iso_country ? $iso_country : '') . self::SEPARATOR;
            echo ($group ? $group : '') . self::SEPARATOR;
            echo self::SEPARATOR; // tax manager
            echo ($id_specifics ? $id_specifics : '') . self::SEPARATOR;
            echo $id_controller;
            echo "\n";
            ++$this->url_count;
        }
    }

    /**
     * @param $settings
     * @param $id_lang int
     * @param $iso_currency string|null
     * @param $device string|null
     * @param $iso_country string|null
     * @param $group int|null
     * @param $id_specifics int|null
     *
     * @return JprestaCacheKey
     */
    private static function getCacheKeyFromParams($settings, $id_lang, $iso_currency, $device, $iso_country, $group, $id_specifics)
    {
        static $ids_currency = [];
        static $ids_country = [];
        static $ids_fake_customer = [];
        static $localCache = [];

        // Generate a local cache key to reduce CPU, RAM and SQL queries
        $cacheKey = md5(json_encode([(int) $id_lang, $iso_currency, $device, $iso_country, $group, (int) $id_specifics]));
        if (isset($localCache[$cacheKey])) {
            return $localCache[$cacheKey];
        }

        if ($iso_currency && !array_key_exists($iso_currency, $ids_currency)) {
            $ids_currency[$iso_currency] = (int) Currency::getIdByIsoCode($iso_currency);
        }
        $id_currency = $iso_currency ? $ids_currency[$iso_currency] : null;

        $id_device = Jprestaspeedpack::DEVICE_COMPUTER;
        if ($device === 'mobile') {
            $id_device = Jprestaspeedpack::DEVICE_MOBILE;
        }

        if ($iso_country && !array_key_exists($iso_country, $ids_country)) {
            if ($settings->isCountryOthers($iso_country)) {
                $ids_country[$iso_country] = null;
            } else {
                $ids_country[$iso_country] = (int) Country::getByIso($iso_country);
            }
        }
        $id_country = $iso_country ? $ids_country[$iso_country] : null;

        if ($group && !array_key_exists($group, $ids_fake_customer)) {
            $customerArray = Customer::getCustomersByEmail($group);
            if ($customerArray && count($customerArray) === 1) {
                $ids_fake_customer[$group] = $customerArray[0]['id_customer'];
            } else {
                $ids_fake_customer[$group] = null;
            }
        }
        $id_fake_customer = $group ? $ids_fake_customer[$group] : null;

        $id_context = PageCacheDAO::getContextIdByInfos((int) $settings->id_shop, $id_lang, $id_currency, $id_device, $id_country, $id_fake_customer, null, $id_specifics);
        $context = PageCacheDAO::getContextById($id_context);
        $cacheKeyResult = self::getCacheKeyFromContext((int) $settings->id_shop, $id_lang, $id_currency, $id_device, $id_country, $id_fake_customer, $context['id_tax_csz'], $id_specifics);

        $localCache[$cacheKey] = $cacheKeyResult;

        return $cacheKeyResult;
    }

    /**
     * Generate a cache key based on provided parameters
     *
     * @param $id_shop
     * @param $id_lang
     * @param $id_currency
     * @param $id_device
     * @param $id_country
     * @param $id_fake_customer
     * @param $id_tax_csz
     * @param $id_specifics
     *
     * @return JprestaCacheKey
     */
    private static function getCacheKeyFromContext($id_shop, $id_lang, $id_currency, $id_device, $id_country, $id_fake_customer, $id_tax_csz, $id_specifics)
    {
        $cacheKey = new JprestaCacheKey();
        $cacheKey->add('id_shop', (int) $id_shop);
        $cacheKey->add('id_currency', (int) $id_currency);
        $cacheKey->add('id_lang', (int) $id_lang);
        $cacheKey->add('id_fake_customer', (int) $id_fake_customer ? (int) $id_fake_customer : null);
        $cacheKey->add('id_device', (int) $id_device);
        $cacheKey->add('id_country', (int) $id_country ? (int) $id_country : null);
        if ($id_tax_csz) {
            $cacheKey->add('id_tax_manager', (int) $id_tax_csz);
        }
        $cacheKey->add('id_specifics', (int) $id_specifics ? (int) $id_specifics : null);
        if (Configuration::get('pagecache_depend_on_css_js')) {
            $cacheKey->add('css_version', Configuration::get('PS_CCCCSS_VERSION'));
            $cacheKey->add('js_version', Configuration::get('PS_CCCJS_VERSION'));
        }

        return $cacheKey;
    }

    private static function isRedirectedByLGSeoRedirect($url, $id_shop)
    {
        $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'lgseoredirect ' .
            'WHERE (url_old="' . pSQL($url) . '" OR url_old LIKE "' . pSQL($url) . '#%") ' .
            'AND id_shop = "' . (int) $id_shop . '"';

        return JprestaUtils::dbGetValue($sql, false, false);
    }

    private static function isRedirectedByArSeoPro($url, $id_shop)
    {
        $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'arseopro_redirect ' .
            'WHERE `from` = "' . pSQL($url) . '" ' .
            'AND id_shop IN(0, ' . (int) $id_shop . ') ' .
            'AND status = 1';

        return JprestaUtils::dbGetValue($sql, false, false);
    }

    private static function isRedirectedByEcSeo($url, $id_shop)
    {
        $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'ec_seo_redirect ' .
            'WHERE `old_link` = "' . pSQL($url) . '" ' .
            'AND id_shop IN(0, ' . (int) $id_shop . ') ' .
            'AND onlineS = 1';

        return JprestaUtils::dbGetValue($sql, false, false);
    }

    private static function checkSecurityParameters()
    {
        if (JprestaUtils::isModuleEnabled('securitypro')) {
            $whiteList = Configuration::get('PRO_FIREWALL_WHITELIST');
            if (!$whiteList) {
                Configuration::updateValue('PRO_FIREWALL_WHITELIST', '18.119.72.109,18.189.172.189');
            } elseif (strpos($whiteList, '18.119.72.109,') === false) {
                Configuration::updateValue('PRO_FIREWALL_WHITELIST', $whiteList . ',18.119.72.109,18.189.172.189');
            }
        }
    }

    private static function reduceUrl($baseUrl, $url)
    {
        // Yes, some shops have tab in URLs... Of course it does not work but it exists
        return str_replace([$baseUrl, '\t'], ['', '%09'], $url);
    }

    /**
     * This can be overriden to treat specific redirections for exemple
     * To override this function, create a file in override/modules/jprestaspeedpack/controllers/front/cachewarmer.php
     * with this contents:
     * <pre>
     * <?php
     * if (!defined('_PS_VERSION_')) {
     *     exit;
     * }
     *
     * class jprestaspeedpackCacheWarmerModuleFrontControllerOverride extends jprestaspeedpackCacheWarmerModuleFrontController
     * {
     *     public function specificTreatmentOnUrl($url)
     *     {
     *         return $url.'?exampleOfModifiedURL';
     *     }
     * }
     * </pre>
     *
     * @param string $url URL that will be generated
     *
     * @return string Modified URL
     */
    public function specificTreatmentOnUrl($url)
    {
        return $url;
    }
}
