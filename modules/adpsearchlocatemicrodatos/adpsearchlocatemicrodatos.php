<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2022 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

class Adpsearchlocatemicrodatos extends Module
{
    public $active_tab;
    public $active_modulo_unistall;
    public $active_modulo_disabled;
    public $show_clean_items;

    private $id_shop;
    private $controller_name;
    private $path;
    private $bck_folder;
    private $base_url;

    public function __construct()
    {
        $this->name = 'adpsearchlocatemicrodatos';
        $this->tab = 'administration';
        $this->version = '2.3.2';
        $this->author = 'Adalop';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->id_shop = Context::getContext()->shop->id;
        $this->module_key = '8d29ea5f0090f3e9b129f96b3834933e';
        $this->controller_name = 'AdminAdpSearchLocateMicrodatos';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_,
        ];

        $this->base_url = '';
        if (defined('_PS_BASE_URL_')) {
            $this->base_url = Tools::getShopDomain(true);
        }
        if (defined('_PS_BASE_URL_SSL_')) {
            $this->base_url = Tools::getShopDomainSsl(true);
        }

        require_once dirname(__FILE__) . '/classes/tools/TextToolsSearchLocate.php';
        require_once dirname(__FILE__) . '/classes/ThemeFilesSearchLocate.php';
        require_once dirname(__FILE__) . '/classes/AdpSearchLocateMicrodatosHelpers.php';
        require_once dirname(__FILE__) . '/classes/tools/Diff.php';

        $config = Configuration::getMultiple(['ADPSL_ACTIVE_MODULE_UNISTALL', 'ADPSL_ACTIVE_MODULE_DISABLED', 'ADPSL_SHOW_CLEAN_ITEMS']);

        $this->active_modulo_unistall = $config['ADPSL_ACTIVE_MODULE_UNISTALL'];
        $this->active_modulo_disabled = $config['ADPSL_ACTIVE_MODULE_DISABLED'];
        $this->show_clean_items = $config['ADPSL_SHOW_CLEAN_ITEMS'];

        parent::__construct();

        $this->path = _PS_MODULE_DIR_ . $this->name . '/';
        $this->bck_folder = $this->path . 'backups/';
        $this->displayName = $this->l('Search and Clean Incorrect Microdata - SEO');
        $this->description = $this->l('Find duplicate or incorrectly configured microdata in your e-commerce and clean it up automatically.');
        $this->confirmUninstall = $this->l('Are you sure about removing these options?');
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue('ADPSL_ACTIVE_MODULE_UNISTALL', '0')
            && Configuration::updateValue('ADPSL_ACTIVE_MODULE_DISABLED', '0')
            && Configuration::updateValue('ADPSL_SHOW_CLEAN_ITEMS', false);
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        Configuration::deleteByName('ADPSL_ACTIVE_MODULE_UNISTALL');
        Configuration::deleteByName('ADPSL_ACTIVE_MODULE_DISABLED');
        Configuration::deleteByName('ADPSL_SHOW_CLEAN_ITEMS');

        return true;
    }

    public function getContent()
    {
        // Procesamiento de solicitudes asíncronas
        if (isset($_REQUEST['asyncAction'])) {
            $action = $_REQUEST['asyncAction'];
            $this->$action();
            exit;
        }

        $this->active_tab = '#tab_search';
        $this->processConfiguration();

        $scanResult = $this->loadScanResults();

        $this->loadAssets();
        $doc_lang = $this->getLangForDoc();

        // Obtenemos un id de producto
        $product = Product::getProducts(Context::getContext()->language->id, 0, 1, 'id_product', 'desc', false, true, Context::getContext());
        // Obtenemos una categoria
        $categoria = Category::getTopCategory(Context::getContext()->language->id);

        $url_product_page = '';
        $url_list_product = '';
        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

        if (count($product) > 0) {
            $link = new Link($protocol_link);
            $url_product_page = $link->getProductLink((int) $product[0]['id_product'], null, null, null, (int) Context::getContext()->language->id);
            $url_list_product = $link->getCategoryLink((int) $product[0]['id_category_default'], null, (int) Context::getContext()->language->id);
        }

        // Passing variables to the back-office template
        $this->context->smarty->assign(
            [
                'module_version' => $this->version,
                'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'base_dir_ssl' => $protocol_link . Tools::getShopDomainSsl() . __PS_BASE_URI__,
                'id_lang' => Context::getContext()->language->id,
                'iso_code_language' => strtolower(Language::getIsoById(Context::getContext()->language->id)),
                'module_display' => $this->displayName,
                'guide_link' => 'docs/doc_' . $this->name . '_' . $doc_lang . '.pdf',
                'tracking_url' => "http://addons.prestashop.com/contact-form.php?utm_source=back-office&utm_medium=module&utm_campaign=back-office-ES&utm_content=$this->name",
                'msg_confirmation_delete' => $this->l('Are you sure you want to delete this item?'),
                'active_tab' => $this->active_tab,
                'active_modulo_disabled' => $this->active_modulo_disabled,
                'active_modulo_unistall' => $this->active_modulo_unistall,
                'show_clean_items' => $this->show_clean_items,
                'url_home_page' => $this->base_url . __PS_BASE_URI__,
                'url_product_page' => $url_product_page,
                'url_list_product' => $url_list_product,
                'url_new_list_product' => Context::getContext()->link->getPageLink('new-products', true, Context::getContext()->language->id),
                'url_best_sales_list_product' => Context::getContext()->link->getPageLink('best-sales', true, Context::getContext()->language->id),
                'url_discount_list_product' => Context::getContext()->link->getPageLink('prices-drop', true, Context::getContext()->language->id),
                'scanResult' => $scanResult,
                'modulos_dir' => _PS_MODULE_DIR_,
                'temp_folder_unwriteble' => !is_writable($this->bck_folder),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/index.tpl');
    }

    private static function getLogPath($type, $namne)
    {
        return dirname(__FILE__) . "/log/$type/$namne.txt";
    }

    public function processConfiguration()
    {
        $post_data = self::getAllValues();
        if (!empty($post_data['ClearCache'])) {
            $this->clearCache();
            $this->context->smarty->assign(['confirmation' => 'ok']);
        } elseif (Tools::isSubmit('option_form_submit_btn_adpsl')) {
            $this->active_modulo_disabled = $post_data['includeDisabled'];
            $this->active_modulo_unistall = $post_data['includeUninstalled'];
            $this->show_clean_items = json_decode($post_data['showCleanItems']);

            Configuration::updateValue('ADPSL_ACTIVE_MODULE_DISABLED', $this->active_modulo_disabled);
            Configuration::updateValue('ADPSL_ACTIVE_MODULE_UNISTALL', $this->active_modulo_unistall);
            Configuration::updateValue('ADPSL_SHOW_CLEAN_ITEMS', $this->show_clean_items);

            $this->context->smarty->assign(
                [
                    'confirmation' => 'ok',
                ]
            );

            $this->active_tab = '#tab_configuration';
        }
    }

    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    public function loadAssets()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/adpsearchlocatemicrodatos.css');
        $this->context->controller->addJS($this->_path . 'views/js/adpsearchlocatemicrodatos-asyncactions.js');
        $this->context->controller->addJS($this->_path . 'views/js/adpsearchlocatemicrodatos.js');
    }

    public function getLangForDoc()
    {
        $doc_langs_available = ['FR', 'ES', 'EN', 'DE', 'IT'];
        foreach ($doc_langs_available as $lang) {
            if (Tools::strtoupper($this->context->language->iso_code) == $lang) {
                return $lang;
            }
        }

        return 'EN';
    }

    public function clearCache()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            Tools::clearSmartyCache();
        } else {
            Tools::clearAllCache();
        }
    }

    // ----------------------
    // S_Region: Scan Methods
    // ----------------------

    private function loadScanResults()
    {
        return [
            'modules' => iterator_to_array($this->loadModulesScanResults()),
            'themes' => iterator_to_array($this->loadThemesScanResults()),
        ];
    }

    private function loadThemesScanResults()
    {
        foreach (AdpSearchLocateMicrodatosHelpers::listThemes($this->active_modulo_disabled == '1', $this->active_modulo_unistall == '1') as $themeName => $themeFolder) {
            $logPath = self::getLogPath('themes', $themeName);
            $analyzed = file_exists($logPath);
            $filesLog = $analyzed ? json_decode(Tools::file_get_contents($logPath), true) : [];

            $fixed = 0;
            $modified = 'false';
            $microdataCount = 0;
            foreach ($filesLog as $key => $value) {
                if ($value['fixed']) {
                    ++$fixed;
                    $filesLog[$key]['modified'] = 'false';
                    if (!file_exists($value['filePath'] . ThemeFilesSearchLocate::$COPY_FILE_EXTENSION) || md5_file($value['filePath']) != md5_file($value['filePath'] . ThemeFilesSearchLocate::$COPY_FILE_EXTENSION)) {
                        $filesLog[$key]['modified'] = 'true';
                        $modified = 'true';
                    }
                }
                $microdataCount += $value['microdataCount'];
            }
            if (!$analyzed || $this->show_clean_items || count($filesLog) > 0) {
                yield $themeName => [
                    'analyzed' => $analyzed,
                    'fixed' => $fixed,
                    'modified' => $modified,
                    'files' => $filesLog,
                    'microdataCount' => $microdataCount,
                ];
            }
        }
    }

    private function loadModulesScanResults()
    {
        foreach (AdpSearchLocateMicrodatosHelpers::listModules($this->active_modulo_disabled == '1', $this->active_modulo_unistall == '1') as $moduleName => $moduleFolder) {
            $logPath = self::getLogPath('modules', $moduleName);
            $analyzed = file_exists($logPath);
            $filesLog = $analyzed ? json_decode(Tools::file_get_contents($logPath), true) : [];

            $fixed = 0;
            $modified = 'false';
            $microdataCount = 0;
            foreach ($filesLog as $key => $value) {
                if ($value['fixed']) {
                    ++$fixed;
                    $filesLog[$key]['modified'] = 'false';
                    if (!file_exists($value['filePath'] . ThemeFilesSearchLocate::$COPY_FILE_EXTENSION) || md5_file($value['filePath']) != md5_file($value['filePath'] . ThemeFilesSearchLocate::$COPY_FILE_EXTENSION)) {
                        $filesLog[$key]['modified'] = 'true';
                        $modified = 'true';
                    }
                }
                $microdataCount += $value['microdataCount'];
            }
            if (!$analyzed || $this->show_clean_items || count($filesLog) > 0) {
                yield $moduleName => [
                    'displayName' => Module::getModuleName($moduleName),
                    'analyzed' => $analyzed,
                    'fixed' => $fixed,
                    'modified' => $modified,
                    'files' => $filesLog,
                    'microdataCount' => $microdataCount,
                ];
            }
        }
    }

    public function getScanResult()
    {
        $scanName = $_REQUEST['scanName'];
        $type = $_REQUEST['scanType'];
        $fileIndex = $_REQUEST['fileIndex'];

        $logFilePath = self::getLogPath($type, $scanName);

        header('Content-Type: application/json');
        echo json_encode(json_decode(Tools::file_get_contents($logFilePath), true)[$fileIndex]['details']);
    }

    public function scanModule()
    {
        $moduleName = $_REQUEST['moduleName'];
        $filesLog = iterator_to_array(ThemeFilesSearchLocate::searchLocate(_PS_MODULE_DIR_ . $moduleName));
        file_put_contents(self::getLogPath('modules', $moduleName), json_encode($filesLog));
    }

    public function scanTheme()
    {
        $themeName = $_REQUEST['themeName'];
        $filesLog = iterator_to_array(ThemeFilesSearchLocate::searchLocate(_PS_ALL_THEMES_DIR_ . $themeName));
        file_put_contents(self::getLogPath('themes', $themeName), json_encode($filesLog));
    }

    public function scanAll()
    {
        $searchContext = $_REQUEST['searchContext'];
        $forceRescan = true; // $_REQUEST["forceRescan"];

        // Themes scan
        if ($searchContext == '1' || $searchContext == '2') {
            foreach (AdpSearchLocateMicrodatosHelpers::listThemes($this->active_modulo_disabled == '1', $this->active_modulo_unistall == '1') as $themeName => $themeFolder) {
                $logPath = self::getLogPath('themes', $themeName);
                $analyzed = file_exists($logPath);
                if ($forceRescan || !$analyzed) {
                    $filesLog = iterator_to_array(ThemeFilesSearchLocate::searchLocate($themeFolder));
                    file_put_contents($logPath, json_encode($filesLog));
                }
            }
        }

        // Modules scan
        if ($searchContext == '1' || $searchContext == '3') {
            foreach (AdpSearchLocateMicrodatosHelpers::listModules($this->active_modulo_disabled == '1', $this->active_modulo_unistall == '1') as $moduleName => $moduleFolder) {
                $logPath = self::getLogPath('modules', $moduleName);
                $analyzed = file_exists($logPath);
                if ($forceRescan || !$analyzed) {
                    $filesLog = iterator_to_array(ThemeFilesSearchLocate::searchLocate($moduleFolder));
                    file_put_contents($logPath, json_encode($filesLog));
                }
            }
        }
    }

    public function downloadModuleScanResult()
    {
        $moduleName = $_REQUEST['moduleName'];
        $logPath = self::getLogPath('modules', $moduleName);
        $this->FileResult($logPath, 'text/plain', "$moduleName.txt");
    }

    public function downloadThemeScanResult()
    {
        $themeName = $_REQUEST['themeName'];
        $logPath = self::getLogPath('themes', $themeName);
        $this->FileResult($logPath, 'text/plain', "$themeName.txt");
    }

    public function fileResult($path, $mime, $filename)
    {
        header("Content-Type: $mime");
        header("Content-Disposition: attachment; filename=$filename");
        readfile($path);
    }

    // ----------------------
    // E_Region: Scan Methods
    // ----------------------

    // ---------------------
    // S_Region: Fix Methods
    // ---------------------
    public function fixModule()
    {
        $moduleName = $_REQUEST['moduleName'];
        ThemeFilesSearchLocate::processing([_PS_MODULE_DIR_ . $moduleName]);
        $this->scanModule();
    }

    public function recoveryModule()
    {
        $moduleName = $_REQUEST['moduleName'];
        ThemeFilesSearchLocate::recovery([_PS_MODULE_DIR_ . $moduleName]);
        $this->scanModule();
    }

    public function fixTheme()
    {
        $themeName = $_REQUEST['themeName'];
        ThemeFilesSearchLocate::processing([_PS_ALL_THEMES_DIR_ . $themeName]);
        $this->scanTheme();
    }

    public function recoveryTheme()
    {
        $themeName = $_REQUEST['themeName'];
        ThemeFilesSearchLocate::recovery([_PS_ALL_THEMES_DIR_ . $themeName]);
        $this->scanTheme();
    }

    public function fixFile()
    {
        $filePath = $_REQUEST['filePath'];
        ThemeFilesSearchLocate::processingFile($filePath);

        if (array_key_exists('moduleName', $_REQUEST)) {
            $this->scanModule();
        }
        if (array_key_exists('themeName', $_REQUEST)) {
            $this->scanTheme();
        }
    }

    public function recoveryFile()
    {
        $filePath = $_REQUEST['filePath'];
        ThemeFilesSearchLocate::recoveryFile($filePath);

        if (array_key_exists('moduleName', $_REQUEST)) {
            $this->scanModule();
        }
        if (array_key_exists('themeName', $_REQUEST)) {
            $this->scanTheme();
        }
    }

    // ---------------------
    // E_Region: Fix Methods
    // ---------------------

    public function getDiff()
    {
        header('Content-Type: application/json');

        $filePath = $_REQUEST['filePath'];
        $diff = Diff::compareFiles($filePath . ThemeFilesSearchLocate::$BACKUP_FILE_EXTENSION, $filePath);

        $line = 1;
        $result = [];
        $gen = false;
        for ($i = 0; $i < count($diff); ++$i) {
            $value = $diff[$i];
            $text = $value[0];
            $state = $value[1];
            switch ($state) {
                case Diff::UNMODIFIED:
                    if ($gen) {
                        $result[] = [
                            'line' => $line,
                            'text' => "<span class='diff-unmodified'>" . htmlentities($text) . '</span><br>',
                        ];
                        $gen = false;
                    }
                    ++$line;
                    break;
                case Diff::DELETED:
                    if (!$gen) {
                        $gen = true;
                        if ($i > 0 && $diff[$i - 1][1] == Diff::UNMODIFIED && end($result)['line'] != $line - 1) {
                            $prevText = $diff[$i - 1][0];
                            $result[] = [
                                'line' => $line - 1,
                                'text' => "<span class='diff-unmodified'>" . htmlentities($prevText) . '</span><br>',
                            ];
                        }
                    }
                    $result[] = [
                        'line' => '',
                        'text' => "<span class='diff-deleted'>" . htmlentities($text) . '</span><br>',
                    ];
                    break;
                case Diff::INSERTED:
                    if (!$gen) {
                        $gen = true;
                        if ($i > 0 && $diff[$i - 1][1] == Diff::UNMODIFIED && end($result)['line'] != $line - 1) {
                            $prevText = $diff[$i - 1][0];
                            $result[] = [
                                'line' => $line - 1,
                                'text' => "<span class='diff-unmodified'>" . htmlentities($prevText) . '</span><br>',
                            ];
                        }
                    }
                    $result[] = [
                        'line' => $line,
                        'text' => "<span class='diff-inserted'>" . htmlentities($text) . '</span><br>',
                    ];
                    ++$line;
                    break;
                default:
                    exit;
            }
        }

        echo json_encode($result);
    }
}
