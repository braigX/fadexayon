<?php
/**
 * 2010-2023 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2023 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Bl_Google_Indexing extends Module
{
    const CSS_VERSION = 'v22';
    const DB_VERSION = 2;

    protected $langId = 0;
    protected $moduleImgPath = 0;

    /**
     * @var NotificationIndexing
     */
    protected $notification;

    public function __construct()
    {
        $this->tab = 'export';
        $this->name = 'bl_google_indexing';
        $this->version = '2.0.12';
        $this->module_key = 'c7492556c2cce459cb16d07ca8d2d606';
        $this->author = 'Bl Modules';
        $this->moduleImgPath = '../modules/' . $this->name . '/views/img/';

        parent::__construct();

        $this->displayName = $this->l('Google Indexing API');
        $this->description = $this->l('Indexing API allows directly notify Google when pages are updated or added');
        $this->confirmUninstall = $this->l('Are you sure you want to delete the module?');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('actionProductUpdate') ||
            !$this->registerHook('displayFooter')) {
            return false;
        }

        Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_indexing_api_log (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `url` VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL,
            `action` VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL,
            `response_phrase` VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL,
            `response_message` TEXT CHARACTER SET utf8mb4 NOT NULL,
            `created_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4');

        $settings = [
            'product_indexing' => 1,
            'requests_per_day' => 200,
            'indexing_all_products' => 0,
        ];

        Configuration::updateValue('BLMOD_INDEXING_SETTINGS', htmlspecialchars(json_encode($settings), ENT_QUOTES));

        $this->installDatabaseVersion1();
        $this->installDatabaseVersion2();

        return true;
    }

    public function installDatabaseVersion1()
    {
        $dateNew = date('Y-m-d H:i:s');

        $this->registerHook('displayFooter');

        try {
            Configuration::updateValue('BLMOD_INDEXING_CRON_UPDATE_DATE', $dateNew);
            Configuration::updateValue('BLMOD_INDEXING_DB_VERSION', self::DB_VERSION);
        } catch (Exception $e) {
        }
    }

    public function installDatabaseVersion2()
    {
        $this->registerHook('displayFooter');

        try {
            Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'blmod_indexing_api_product (
                `id_product` INT(11) NOT NULL AUTO_INCREMENT,
                `id_shop` INT(11) NOT NULL DEFAULT 1,
                `updated_at` DATETIME DEFAULT NULL,
                INDEX (id_product)
            ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4');
        } catch (Exception $e) {
        }
    }

    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_indexing_api_log');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blmod_indexing_api_product');
        Db::getInstance()->Execute('ALTER TABLE '._DB_PREFIX_.'product_shop DROP COLUMN blmod_indexed');

        Configuration::deleteByName('BLMOD_INDEXING_SETTINGS');
        Configuration::deleteByName('BLMOD_INDEXING_CRON_UPDATE_DATE');
        Configuration::deleteByName('BLMOD_INDEXING_DB_VERSION');

        return parent::uninstall();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if (Tools::getValue('controller') == 'AdminProducts') {
            $this->sendToGoogleLastUpdated();
        }

        if (Tools::getValue('configure') != 'bl_google_indexing' && Tools::getValue('controller') != 'AdminProducts') {
            return false;
        }

        $this->context->controller->addJS($this->_path.'views/js/admin_'.self::CSS_VERSION.'.js', 'all');
        $this->context->controller->addCSS($this->_path.'views/css/style_admin_'.self::CSS_VERSION.'.css', 'all');
        $this->context->controller->addCSS($this->_path.'views/css/bl_google_indexing_'.self::CSS_VERSION.'.css', 'all');

        if (Tools::getValue('configure') != 'bl_google_indexing') {
            return false;
        }

        if (_PS_VERSION_ < 1.7) {
            $this->context->controller->addCSS($this->_path.'views/css/ps16.css', 'all');
        }

        if (_PS_VERSION_ < 1.6) {
            $this->context->controller->addCSS($this->_path.'views/css/style_admin_ps_old.css', 'all');
            $this->context->controller->addCSS($this->_path.'views/css/admin-theme.css', 'all');
        }

        return true;
    }

    protected function sendToGoogleLastUpdated()
    {
        $settings = $this->getSettings();

        if (empty($settings['json_api_key'])) {
            return false;
        }

        $where = '';

        if (!empty($settings['indexing_only_active'])) {
            $where = ' AND p.active = 1';
        }

        $delay = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-1minutes'));
        $lastUpdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-120minutes'));

        $products = Db::getInstance()->executeS('SELECT p.id_product, p.date_upd
            FROM '._DB_PREFIX_.'product_shop p
            LEFT JOIN '._DB_PREFIX_.'blmod_indexing_api_product b ON
            b.id_product = p.id_product
            WHERE (p.date_upd > b.updated_at OR b.updated_at IS NULL) AND p.date_upd < "'.pSQL($delay).'" 
            AND p.date_upd > "'.pSQL($lastUpdate).'"'.pSQL($where).'
            LIMIT 1');

        if (empty($products)) {
            return false;
        }

        $this->loadClass();

        $indexingApi = new IndexingApi();

        foreach ($products as $p) {
            if (empty($settings['product_lang_id']) || empty($settings['product_indexing'])) {
                return false;
            }

            foreach ($settings['product_lang_id'] as $langId) {
                $indexingApi->sendAfterProductUpdate($p['id_product'], $langId, $settings);
            }
        }

        return true;
    }

    public function getContent()
    {
        $this->isValidDatabase();

        include_once(dirname(__FILE__).'/NotificationIndexing.php');
        $this->notification = new NotificationIndexing();

        $this->loadClass();
        $this->catchSave();

        $this->smarty->assign([
            '_PS_VERSION_' => _PS_VERSION_,
            'version' => $this->version,
            'moduleImgPath' => $this->moduleImgPath,
            'notifications' => $this->notification->getMessages(),
            'displayName' => $this->displayName,
            'contentHtml' => $this->getSettingsPage(),
        ]);

        return $this->displaySmarty('views/templates/admin/body.tpl');
    }

    public function catchSave()
    {
        $updateSettingsAction = Tools::getValue('update_settings');

        if (empty($updateSettingsAction)) {
            return false;
        }

        $settings = [];
        $settings['product_indexing'] = (int)Tools::getValue('product_indexing');
        $settings['combination_indexing'] = (int)Tools::getValue('combination_indexing');
        $settings['indexing_only_active'] = (int)Tools::getValue('indexing_only_active');
        $settings['product_lang_id'] = Tools::getValue('product_lang_id');
        $settings['indexing_all_products'] = Tools::getValue('indexing_all_products');
        $settings['json_api_key'] = !empty($_POST['json_api_key']) ? trim($_POST['json_api_key']) : ''; //Sorry, we cant use here Tools::getValue
        $settings['requests_per_day'] = Tools::getValue('requests_per_day');

        Configuration::updateValue('BLMOD_INDEXING_SETTINGS', htmlspecialchars(json_encode($settings), ENT_QUOTES));

        $this->notification->addConf($this->l('Settings successfully updated'));

        return true;
    }

    public function getSettingsPage()
    {
        $link = new Link();
        $indexingApiLog = new IndexingApiLog();
        $logPageUrl = htmlspecialchars(Tools::getValue('log_page_url'), ENT_QUOTES);

        $languages = Db::getInstance()->ExecuteS('SELECT l.id_lang, l.name 
			FROM '._DB_PREFIX_.'lang l');

        $totalProducts = $this->getTotalActiveProducts();
        $totalIndexed = $this->getTotalIndexed();
        $settings = $this->getSettings();
        $errorMessages = [];

        if (empty($settings['json_api_key'])) {
            $errorMessages[] = $this->l('JSON API Key must be filled');
        }

        if (empty($settings['product_lang_id'])) {
            $errorMessages[] = $this->l('At least one product language must be selected');
        }

        if (empty($settings['requests_per_day'])) {
            $errorMessages[] = $this->l('The daily quota must be entered (integer)');
        }

        if (empty($settings['product_indexing']) && empty($settings['combination_indexing'])) {
            $errorMessages[] = $this->l('Automatic indexing is inactive, need to be enabled type of automatic indexing');
        }

        $this->smarty->assign([
            'errorMessages' => $errorMessages,
            'requestUri' => $_SERVER['REQUEST_URI'],
            'languages' => $languages,
            'settings' => $settings,
            'APIURL' => $link->getModuleLink($this->name, 'api'),
            'logs' => $indexingApiLog->getLogs($logPageUrl),
            'logsRowsLimit' => ($indexingApiLog->countLogsTotal() > IndexingApiLog::LOG_ROWS_LIMIT) ? IndexingApiLog::LOG_ROWS_LIMIT : 0,
            'logPageUrl' => $logPageUrl,
            'totalProducts' => $totalProducts,
            'totalIndexed' => ($totalIndexed > $totalProducts) ? $totalProducts : $totalIndexed,
            'cronUrl' => $this->getCronUrl(),
            'manualPdfUrl' => $this->getShopProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/readme_en.pdf'
        ]);

        return $this->displaySmarty('views/templates/admin/page/settings.tpl');
    }

    public function getSettings()
    {
        $settings = json_decode(htmlspecialchars_decode(Configuration::get('BLMOD_INDEXING_SETTINGS')), true);

        $settings['product_lang_id'] = !empty($settings['product_lang_id']) ? $settings['product_lang_id'] : [];
        $settings['product_indexing'] = !empty($settings['product_indexing']) ? $settings['product_indexing'] : 0;
        $settings['combination_indexing'] = !empty($settings['combination_indexing']) ? $settings['combination_indexing'] : 0;
        $settings['indexing_all_products'] = !empty($settings['indexing_all_products']) ? $settings['indexing_all_products'] : 0;
        $settings['json_api_key'] = !empty($settings['json_api_key']) ? $settings['json_api_key'] : '';
        $settings['requests_per_day'] = !empty($settings['requests_per_day']) ? $settings['requests_per_day'] : '';

        return $settings;
    }

    public function getCronUrl()
    {
        if (_PS_VERSION_ < '1.5') {
            return '';
        }

        $link = new Link();

        return $link->getModuleLink('bl_google_indexing', 'all');
    }

    /**
     * After update product
     *
     * @param array $params
     */
    public function hookActionProductUpdate($params)
    {
        if (empty($params['id_product'])) {
            return;
        }

        $this->loadClass();
        $indexingApi = new IndexingApi();

        $settings = $this->getSettings();

        if (empty($settings['product_lang_id']) || empty($settings['product_indexing'])) {
            return;
        }

        foreach ($settings['product_lang_id'] as $langId) {
            $indexingApi->sendAfterProductUpdate($params['id_product'], $langId, $settings);
        }
    }

    public function hookDisplayFooter()
    {
        $this->sendToGoogleLastUpdated();
    }

    public function displaySmarty($path)
    {
        $this->smarty->assign('tpl_dir', _PS_MODULE_DIR_.$this->name.'/');

        return $this->display(__FILE__, $path);
    }

    public function getShopProtocol()
    {
        if (method_exists('Tools', 'getShopProtocol')) {
            return Tools::getShopProtocol();
        }

        return (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS'])
                && Tools::strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }

    protected function loadClass()
    {
        $this->langId = (int)Configuration::get('PS_LANG_DEFAULT');

        include_once(dirname(__FILE__).'/IndexingApi.php');
        include_once(dirname(__FILE__).'/IndexingApiLog.php');
    }

    protected function getTotalActiveProducts()
    {
        return Db::getInstance()->getValue('SELECT COUNT(p.id_product) 
            FROM '._DB_PREFIX_.'product_shop p
            WHERE p.id_shop = 1 AND p.active = 1');
    }

    protected function getTotalIndexed()
    {
        return Db::getInstance()->getValue('SELECT COUNT(p.id_product) 
            FROM '._DB_PREFIX_.'product_shop p
            LEFT JOIN '._DB_PREFIX_.'blmod_indexing_api_product b ON
            b.id_product = p.id_product
            WHERE p.id_shop = 1 AND p.active = 1 AND b.updated_at IS NOT NULL');
    }

    protected function isValidDatabase()
    {
        $versionFromDb = Configuration::get('BLMOD_INDEXING_DB_VERSION');

        if ((int)$versionFromDb != self::DB_VERSION) {
            $this->installDatabaseVersion1();
            $this->installDatabaseVersion2();
        }
    }
}
