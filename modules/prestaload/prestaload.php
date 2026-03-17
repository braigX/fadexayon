<?php
/**
 * PrestaLoad
 *
 * Anonymous full-page HTML cache for selected front-office controllers.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/PrestaLoadCacheSettings.php';
require_once __DIR__ . '/classes/PrestaLoadCacheEligibility.php';
require_once __DIR__ . '/classes/PrestaLoadCacheKeyBuilder.php';
require_once __DIR__ . '/classes/PrestaLoadCacheLogger.php';
require_once __DIR__ . '/classes/PrestaLoadCacheStore.php';
require_once __DIR__ . '/classes/PrestaLoadPageCache.php';
require_once __DIR__ . '/classes/PrestaLoadBrowserCacheManager.php';
require_once __DIR__ . '/classes/PrestaLoadEarlyCacheKeyBuilder.php';
require_once __DIR__ . '/classes/PrestaLoadRuntimeConfig.php';
require_once __DIR__ . '/classes/PrestaLoadAssetPageRegistry.php';
require_once __DIR__ . '/classes/PrestaLoadAssetScannerClient.php';
require_once __DIR__ . '/classes/PrestaLoadAssetScanStore.php';
require_once __DIR__ . '/classes/PrestaLoadAssetRuleStore.php';
require_once __DIR__ . '/classes/PrestaLoadAssetMinifier.php';
require_once __DIR__ . '/classes/PrestaLoadAssetRuleApplier.php';
require_once __DIR__ . '/classes/PrestaLoadCriticalCssPageRegistry.php';
require_once __DIR__ . '/classes/PrestaLoadCriticalCssScannerClient.php';
require_once __DIR__ . '/classes/PrestaLoadCriticalCssStore.php';
require_once __DIR__ . '/classes/PrestaLoadCriticalCssInjector.php';
require_once __DIR__ . '/classes/PrestaLoadFontOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadCssOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadImgProxyUrlBuilder.php';
require_once __DIR__ . '/classes/PrestaLoadImageDimensionOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadImageLoadingOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadImageOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadHtmlCompressor.php';
require_once __DIR__ . '/classes/PrestaLoadHtmlOptimizer.php';

class PrestaLoad extends Module
{
    private const TAB_GENERAL = 'general';
    private const TAB_ASSETS = 'assets';
    private const TAB_CACHE_LIFETIMES = 'cache_lifetimes';
    private const TAB_FONTS = 'fonts';
    private const TAB_CSS = 'css';
    private const TAB_IMAGES = 'images';
    private const TAB_CRITICAL_CSS = 'critical_css';

    /**
     * Hooks that should invalidate the full-page cache because content changed.
     */
    private const INVALIDATION_HOOKS = [
        'actionClearCache',
        'actionClearCompileCache',
        'actionCategoryAdd',
        'actionCategoryUpdate',
        'actionCategoryDelete',
        'actionProductAdd',
        'actionProductUpdate',
        'actionProductDelete',
        'actionProductSave',
        'actionObjectProductAddAfter',
        'actionObjectProductUpdateAfter',
        'actionObjectProductDeleteAfter',
        'actionObjectCategoryAddAfter',
        'actionObjectCategoryUpdateAfter',
        'actionObjectCategoryDeleteAfter',
        'actionObjectCmsAddAfter',
        'actionObjectCmsUpdateAfter',
        'actionObjectCmsDeleteAfter',
    ];

    private $settings;
    private $pageCache;
    private $browserCacheManager;
    private $runtimeConfig;
    private $assetPageRegistry;
    private $assetScannerClient;
    private $assetScanStore;
    private $assetRuleStore;
    private $assetMinifier;
    private $criticalCssPageRegistry;
    private $criticalCssScannerClient;
    private $criticalCssStore;

    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = 'PrestaLoad';
        $this->description = 'Anonymous full-page cache for selected Prestashop pages.';

        $this->settings = new PrestaLoadCacheSettings($this->name, __DIR__);
        $this->browserCacheManager = new PrestaLoadBrowserCacheManager($this->settings);
        $this->runtimeConfig = new PrestaLoadRuntimeConfig($this->settings, __DIR__);
        $this->assetPageRegistry = new PrestaLoadAssetPageRegistry($this->context, $this->settings);
        $this->assetScannerClient = new PrestaLoadAssetScannerClient($this->settings);
        $this->assetScanStore = new PrestaLoadAssetScanStore(__DIR__);
        $this->assetRuleStore = new PrestaLoadAssetRuleStore(__DIR__);
        $this->assetMinifier = new PrestaLoadAssetMinifier($this->context, __DIR__);
        $this->criticalCssPageRegistry = new PrestaLoadCriticalCssPageRegistry($this->context, $this->settings);
        $this->criticalCssScannerClient = new PrestaLoadCriticalCssScannerClient($this->settings);
        $this->criticalCssStore = new PrestaLoadCriticalCssStore(__DIR__);
        $this->pageCache = $this->buildPageCache();
    }

    /**
     * Install default settings and register cache hooks.
     */
    public function install()
    {
        return parent::install()
            && $this->settings->installDefaults()
            && $this->runtimeConfig->write()
            && $this->registerHook('actionDispatcher')
            && $this->registerHook('actionOutputHTMLBefore')
            && $this->registerHooks(self::INVALIDATION_HOOKS);
    }

    /**
     * Remove settings and clear cached files.
     */
    public function uninstall()
    {
        $this->pageCache->clear();
        $this->runtimeConfig->delete();

        return $this->unregisterHook('actionDispatcher')
            && $this->unregisterHook('actionOutputHTMLBefore')
            && $this->unregisterHooks(self::INVALIDATION_HOOKS)
            && $this->settings->uninstallDefaults()
            && parent::uninstall();
    }

    /**
     * Configuration page:
     * - enable or disable cache
     * - set TTL
     * - define allowed controllers
     * - clear cached pages
     */
    public function getContent()
    {
        $this->handleAjaxRequest();

        $output = '';
        $activeTab = $this->getActiveTab();

        if (Tools::isSubmit('submitPrestaLoadGeneralSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_ENABLED,
                PrestaLoadCacheSettings::CONFIG_HTML_COMPRESSION_ENABLED,
                PrestaLoadCacheSettings::CONFIG_TTL,
                PrestaLoadCacheSettings::CONFIG_ALLOWED_CONTROLLERS,
            ]);
            $this->runtimeConfig->write();
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Full page caching settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadCriticalCssSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_CRITICAL_CSS_ENABLED,
            ]);
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Critical CSS settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadBrowserCacheSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_ENABLED,
                PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_ASSET_TTL,
                PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_MEDIA_TTL,
            ]);
            $this->runtimeConfig->write();

            $browserCacheSync = $this->browserCacheManager->sync();

            if (!empty($browserCacheSync['success'])) {
                $output .= $this->displayConfirmation($this->trans('Browser cache settings updated.', [], 'Admin.Notifications.Success'));
            } else {
                $output .= $this->displayWarning($this->trans('Browser cache settings were saved, but the .htaccess file needs a manual update.', [], 'Admin.Notifications.Warning'));
            }

            if (!empty($browserCacheSync['message'])) {
                $output .= $this->displayInformation($browserCacheSync['message']);
            }
        }

        if (Tools::isSubmit('submitPrestaLoadFontSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_FONT_OPTIMIZATION_ENABLED,
            ]);
            $this->runtimeConfig->write();
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Font settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadCssSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_CSS_OPTIMIZATION_ENABLED,
            ]);
            $this->runtimeConfig->write();
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('CSS settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadImageSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_IMAGE_LOADING_OPTIMIZATION_ENABLED,
                PrestaLoadCacheSettings::CONFIG_BACKGROUND_IMAGE_LAZY_LOADING_ENABLED,
                PrestaLoadCacheSettings::CONFIG_IMAGE_DIMENSIONS_OPTIMIZATION_ENABLED,
                PrestaLoadCacheSettings::CONFIG_IMAGE_OPTIMIZATION_ENABLED,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_BASE_URL,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_QUALITY,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_KEY,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_SALT,
            ]);
            $this->runtimeConfig->write();
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Image settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadAssetSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_ASSET_SCANNER_BASE_URL,
                PrestaLoadCacheSettings::CONFIG_ASSET_SCAN_TARGET_BASE_URL,
            ]);
            $output .= $this->displayConfirmation($this->trans('Asset scanner settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadClearCache')) {
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Full-page cache cleared.', [], 'Admin.Notifications.Success'));
        }

        $assetPages = $this->assetPageRegistry->getPages();
        $selectedAssetPage = $this->getSelectedAssetPage();
        $selectedAssetScan = !empty($selectedAssetPage) ? $this->assetScanStore->getLatestAssetSummary($selectedAssetPage['key']) : null;
        $selectedAssetRules = !empty($selectedAssetPage) ? $this->assetRuleStore->getRulesForPage($selectedAssetPage['key']) : [];
        $detectedShopBaseUrl = $this->getDetectedShopBaseUrl();
        $effectiveScanBaseUrl = $this->settings->getAssetScanTargetBaseUrl() !== ''
            ? $this->settings->getAssetScanTargetBaseUrl()
            : $detectedShopBaseUrl;

        $this->context->smarty->assign([
            'prestaload_active_tab' => $activeTab,
            'prestaload_tabs' => $this->getAdminTabs(),
            'prestaload_stats' => $this->pageCache->getStats(),
            'prestaload_settings_form' => $this->renderSettingsForm($activeTab),
            'prestaload_browser_cache_status' => $this->browserCacheManager->getStatus(),
            'prestaload_asset_pages' => $assetPages,
            'prestaload_selected_asset_page' => $selectedAssetPage,
            'prestaload_selected_asset_scan' => $this->decorateAssetScan($selectedAssetScan),
            'prestaload_selected_asset_rules' => $this->indexRulesByUrl($selectedAssetRules),
            'prestaload_asset_scan_ajax_url' => $this->getAjaxConfigurationLink('runAssetScan'),
            'prestaload_asset_toggle_flag_ajax_url' => $this->getAjaxConfigurationLink('toggleAssetFlag'),
            'prestaload_asset_bulk_rule_ajax_url' => $this->getAjaxConfigurationLink('saveBulkAssetRules'),
            'prestaload_asset_minify_ajax_url' => $this->getAjaxConfigurationLink('minifyAsset'),
            'prestaload_asset_bulk_minify_ajax_url' => $this->getAjaxConfigurationLink('bulkMinifyAssets'),
            'prestaload_asset_bulk_clear_minified_ajax_url' => $this->getAjaxConfigurationLink('bulkClearMinifiedAssets'),
            'prestaload_critical_css_pages' => $this->decorateCriticalCssPages($this->criticalCssPageRegistry->getPages()),
            'prestaload_critical_css_generate_ajax_url' => $this->getAjaxConfigurationLink('generateCriticalCss'),
            'prestaload_detected_shop_base_url' => $detectedShopBaseUrl,
            'prestaload_effective_asset_scan_base_url' => $effectiveScanBaseUrl,
        ]);

        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    /**
     * Cache hit path. Runs before full controller execution.
     */
    public function hookActionDispatcher($params)
    {
        if (!headers_sent()) {
            header('X-PrestaLoad-Boot: prestashop');
        }

        $this->pageCache->maybeServe(is_array($params) ? $params : []);
    }

    /**
     * Cache storage path. Runs with the final HTML output.
     */
    public function hookActionOutputHTMLBefore($params)
    {
        $html = isset($params['html']) ? $params['html'] : '';
        $html = $this->pageCache->optimizeHtml($html);

        if (is_array($params) && array_key_exists('html', $params)) {
            $params['html'] = $html;
        }

        $this->pageCache->maybeStore($html);
    }

    /**
     * All mutation hooks use the same invalidation behavior in V1.
     */
    public function __call($name, $arguments)
    {
        if (strpos(Tools::strtolower((string) $name), 'hookaction') === 0) {
            $this->pageCache->clear();
        }
    }

    private function buildPageCache()
    {
        $eligibility = new PrestaLoadCacheEligibility($this->context, $this->settings);
        $keyBuilder = new PrestaLoadCacheKeyBuilder($this->context, $this->local_path);
        $logger = new PrestaLoadCacheLogger($this->settings->getLogFile());
        $store = new PrestaLoadCacheStore($this->settings->getCacheDirectory());
        $fontOptimizer = new PrestaLoadFontOptimizer($this->settings);
        $cssOptimizer = new PrestaLoadCssOptimizer($this->settings);
        $imgProxyUrlBuilder = new PrestaLoadImgProxyUrlBuilder($this->settings);
        $imageDimensionOptimizer = new PrestaLoadImageDimensionOptimizer($this->context, $this->settings);
        $imageLoadingOptimizer = new PrestaLoadImageLoadingOptimizer($this->settings);
        $imageOptimizer = new PrestaLoadImageOptimizer($this->context, $this->settings, $imgProxyUrlBuilder, $imageDimensionOptimizer, $imageLoadingOptimizer);
        $assetRuleApplier = new PrestaLoadAssetRuleApplier($this->context, $this->assetRuleStore, $cssOptimizer, $this->assetMinifier);
        $criticalCssInjector = new PrestaLoadCriticalCssInjector($this->context, $this->criticalCssStore, $this->settings, __DIR__);
        $htmlCompressor = new PrestaLoadHtmlCompressor($this->settings);
        $htmlOptimizer = new PrestaLoadHtmlOptimizer($criticalCssInjector, $fontOptimizer, $cssOptimizer, $imageOptimizer, $assetRuleApplier, $htmlCompressor);

        return new PrestaLoadPageCache($this->context, $this->settings, $eligibility, $keyBuilder, $store, $logger, $htmlOptimizer);
    }

    private function registerHooks(array $hooks)
    {
        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function unregisterHooks(array $hooks)
    {
        foreach ($hooks as $hook) {
            $this->unregisterHook($hook);
        }

        return true;
    }

    private function renderSettingsForm($activeTab)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->getAdminConfigurationLink($activeTab);
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = 0;
        $helper->tpl_vars = [
            'fields_value' => $this->settings->getFormValues(),
        ];

        if ($activeTab === self::TAB_ASSETS) {
            $fieldsValue = $helper->tpl_vars['fields_value'];
            if (empty($fieldsValue[PrestaLoadCacheSettings::CONFIG_ASSET_SCAN_TARGET_BASE_URL])) {
                $fieldsValue[PrestaLoadCacheSettings::CONFIG_ASSET_SCAN_TARGET_BASE_URL] = $this->getDetectedShopBaseUrl();
            }
            $helper->tpl_vars['fields_value'] = $fieldsValue;
        }

        $forms = [
            self::TAB_GENERAL => [
                'form' => [
                    'legend' => [
                        'title' => 'Full page caching',
                        'icon' => 'icon-cogs',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Enable full-page cache',
                            'name' => PrestaLoadCacheSettings::CONFIG_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_enabled_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_enabled_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Only anonymous GET requests can be cached.',
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Compress final HTML',
                            'name' => PrestaLoadCacheSettings::CONFIG_HTML_COMPRESSION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_html_compression_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_html_compression_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Removes safe whitespace and HTML comments from the final cached markup.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'TTL seconds',
                            'name' => PrestaLoadCacheSettings::CONFIG_TTL,
                            'class' => 'fixed-width-xl',
                            'desc' => 'How long one cached page stays valid before it expires. Default: 15 days.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Allowed controllers',
                            'name' => PrestaLoadCacheSettings::CONFIG_ALLOWED_CONTROLLERS,
                            'desc' => 'Comma-separated list. Example: index,category,product,cms',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadGeneralSettings',
                    ],
                    'buttons' => [
                        [
                            'title' => 'Clear Cache',
                            'name' => 'submitPrestaLoadClearCache',
                            'type' => 'submit',
                            'class' => 'btn btn-default pull-left',
                            'icon' => 'process-icon-delete',
                        ],
                    ],
                ],
            ],
            self::TAB_FONTS => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Fonts', [], 'Admin.Global'),
                        'icon' => 'icon-font',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Optimize fonts',
                            'name' => PrestaLoadCacheSettings::CONFIG_FONT_OPTIMIZATION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_fonts_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_fonts_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Adds safe font-loading optimizations to cached public HTML.',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadFontSettings',
                    ],
                ],
            ],
            self::TAB_CACHE_LIFETIMES => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Cache Lifetimes', [], 'Admin.Global'),
                        'icon' => 'icon-time',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Enable browser cache lifetime rules',
                            'name' => PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_browser_cache_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_browser_cache_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Adds or removes a managed .htaccess block for long-lived browser caching of static files.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Static asset TTL seconds',
                            'name' => PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_ASSET_TTL,
                            'class' => 'fixed-width-xl',
                            'desc' => 'Applies to CSS, JavaScript, fonts, and images. Default: 31536000 seconds.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Media TTL seconds',
                            'name' => PrestaLoadCacheSettings::CONFIG_BROWSER_CACHE_MEDIA_TTL,
                            'class' => 'fixed-width-xl',
                            'desc' => 'Applies to media files such as MP4, WebM, and MP3. Default: 2592000 seconds.',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadBrowserCacheSettings',
                    ],
                ],
            ],
            self::TAB_ASSETS => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Assets', [], 'Admin.Global'),
                        'icon' => 'icon-sitemap',
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'label' => 'Scanner base URL',
                            'name' => PrestaLoadCacheSettings::CONFIG_ASSET_SCANNER_BASE_URL,
                            'class' => 'fixed-width-xxl',
                            'desc' => 'Remote scanner used for page-level CSS and JS analysis.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'Public scan base URL',
                            'name' => PrestaLoadCacheSettings::CONFIG_ASSET_SCAN_TARGET_BASE_URL,
                            'class' => 'fixed-width-xxl',
                            'desc' => 'Optional. Use this when the back office runs on a local domain but scans must target a public shop URL, for example https://plexi-cindar.novprojet.com',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadAssetSettings',
                    ],
                ],
            ],
            self::TAB_CRITICAL_CSS => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Critical CSS', [], 'Admin.Global'),
                        'icon' => 'icon-flask',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Enable beta critical CSS injection',
                            'name' => PrestaLoadCacheSettings::CONFIG_CRITICAL_CSS_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_critical_css_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_critical_css_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'When enabled, stored critical CSS is injected locally by page type and device. Beta feature.',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadCriticalCssSettings',
                    ],
                ],
            ],
            self::TAB_CSS => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('CSS', [], 'Admin.Global'),
                        'icon' => 'icon-code',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Experimental CSS deferral',
                            'name' => PrestaLoadCacheSettings::CONFIG_CSS_OPTIMIZATION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_css_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_css_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Disabled by default. Can improve render-blocking in some shops but may increase layout shifts when modules inject visible CSS late.',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadCssSettings',
                    ],
                ],
            ],
            self::TAB_IMAGES => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Images', [], 'Admin.Global'),
                        'icon' => 'icon-picture',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => 'Optimize image loading',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMAGE_LOADING_OPTIMIZATION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_image_loading_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_image_loading_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Keeps likely above-the-fold images eager and lazy-loads the remaining images.',
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Lazy load background images',
                            'name' => PrestaLoadCacheSettings::CONFIG_BACKGROUND_IMAGE_LAZY_LOADING_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_background_images_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_background_images_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Conservatively delays inline HTML background images outside obvious hero or slider sections.',
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Add missing width and height',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMAGE_DIMENSIONS_OPTIMIZATION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_image_dimensions_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_image_dimensions_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Adds width and height attributes to local image tags when the file dimensions can be resolved safely.',
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Optimize images with ImgProxy',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMAGE_OPTIMIZATION_ENABLED,
                            'is_bool' => true,
                            'values' => [
                                ['id' => 'prestaload_images_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                                ['id' => 'prestaload_images_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                            ],
                            'desc' => 'Disabled by default. Rewrites raster image URLs to the configured ImgProxy service.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'ImgProxy base URL',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMGPROXY_BASE_URL,
                            'class' => 'fixed-width-xxl',
                            'desc' => 'Example: https://imgcdn.prestaload.com/',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'ImgProxy quality',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMGPROXY_QUALITY,
                            'class' => 'fixed-width-sm',
                            'desc' => 'WebP quality used in generated ImgProxy URLs.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'ImgProxy key',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMGPROXY_KEY,
                            'class' => 'fixed-width-xxl',
                            'desc' => 'Hex-encoded imgproxy signing key. Leave empty only if your ImgProxy server allows unsafe URLs.',
                        ],
                        [
                            'type' => 'text',
                            'label' => 'ImgProxy salt',
                            'name' => PrestaLoadCacheSettings::CONFIG_IMGPROXY_SALT,
                            'class' => 'fixed-width-xxl',
                            'desc' => 'Hex-encoded imgproxy signing salt used together with the key.',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save', [], 'Admin.Actions'),
                        'name' => 'submitPrestaLoadImageSettings',
                    ],
                ],
            ],
        ];

        return $helper->generateForm([$forms[$activeTab]]);
    }

    private function getActiveTab()
    {
        $tab = Tools::getValue('prestaload_tab', self::TAB_GENERAL);
        $allowedTabs = array_keys($this->getAdminTabs());

        return in_array($tab, $allowedTabs, true) ? $tab : self::TAB_GENERAL;
    }

    private function getAdminTabs()
    {
        return [
            self::TAB_GENERAL => [
                'label' => 'Full page caching',
                'icon' => 'icon-dashboard',
                'link' => $this->getAdminConfigurationLink(self::TAB_GENERAL),
            ],
            self::TAB_FONTS => [
                'label' => 'Fonts',
                'icon' => 'icon-font',
                'link' => $this->getAdminConfigurationLink(self::TAB_FONTS),
            ],
            self::TAB_ASSETS => [
                'label' => 'Assets',
                'icon' => 'icon-sitemap',
                'link' => $this->getAdminConfigurationLink(self::TAB_ASSETS),
            ],
            self::TAB_CRITICAL_CSS => [
                'label' => 'Critical CSS',
                'icon' => 'icon-flask',
                'link' => $this->getAdminConfigurationLink(self::TAB_CRITICAL_CSS),
            ],
            self::TAB_CACHE_LIFETIMES => [
                'label' => 'Cache Lifetimes',
                'icon' => 'icon-time',
                'link' => $this->getAdminConfigurationLink(self::TAB_CACHE_LIFETIMES),
            ],
            self::TAB_CSS => [
                'label' => 'CSS',
                'icon' => 'icon-code',
                'link' => $this->getAdminConfigurationLink(self::TAB_CSS),
            ],
            self::TAB_IMAGES => [
                'label' => 'Images',
                'icon' => 'icon-picture',
                'link' => $this->getAdminConfigurationLink(self::TAB_IMAGES),
            ],
        ];
    }

    private function getAdminConfigurationLink($tab)
    {
        return $this->context->link->getAdminLink('AdminModules')
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name
            . '&prestaload_tab=' . urlencode((string) $tab);
    }

    private function getSelectedAssetPage()
    {
        $selectedKey = (string) Tools::getValue('prestaload_asset_page', 'home');

        foreach ($this->assetPageRegistry->getPages() as $page) {
            if ($page['key'] === $selectedKey) {
                return $page;
            }
        }

        return [];
    }

    /**
     * The back-office context URL is a good default for scans until the admin
     * overrides it with a public hostname.
     */
    private function getDetectedShopBaseUrl()
    {
        $ssl = $this->context->shop && method_exists($this->context->shop, 'getBaseURL')
            ? $this->context->shop->getBaseURL(true)
            : $this->context->link->getPageLink('index', true);

        $parts = parse_url((string) $ssl);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return rtrim((string) $ssl, '/');
        }

        $baseUrl = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $baseUrl .= ':' . (int) $parts['port'];
        }

        return $baseUrl;
    }

    /**
     * Adds UI metadata so the template can render score colors and grouped
     * asset tabs without duplicating Lighthouse threshold logic in Smarty.
     */
    private function decorateAssetScan($scan)
    {
        if (!is_array($scan)) {
            return $scan;
        }

        $metrics = isset($scan['metrics']) && is_array($scan['metrics']) ? $scan['metrics'] : [];
        foreach ($metrics as $metricKey => $metric) {
            $metrics[$metricKey]['label'] = $this->getMetricLabel($metricKey);
            $metrics[$metricKey]['status'] = $this->getMetricStatus($metricKey, isset($metric['numeric_value']) ? $metric['numeric_value'] : null);
        }

        $scan['metrics'] = $metrics;
        if (isset($scan['assets']) && is_array($scan['assets'])) {
            foreach ($scan['assets'] as &$asset) {
                $asset['normalized_url'] = $this->normalizeAssetUrlForUi(isset($asset['url']) ? $asset['url'] : '');
            }
            unset($asset);
        }
        $scan['score_cards'] = $this->buildScoreCards($scan);
        $scan['asset_groups'] = $this->groupAssetsForDisplay(isset($scan['assets']) && is_array($scan['assets']) ? $scan['assets'] : []);

        return $scan;
    }

    private function buildScoreCards(array $scan)
    {
        $cards = [];
        $mobileScore = isset($scan['mobile_score']) ? $scan['mobile_score'] : null;
        $cards[] = [
            'key' => 'mobile_score',
            'label' => 'Performance score',
            'display_value' => $mobileScore === null ? '-' : (string) round(((float) $mobileScore) * 100),
            'status' => $this->getScoreStatus($mobileScore),
        ];

        foreach (isset($scan['metrics']) && is_array($scan['metrics']) ? $scan['metrics'] : [] as $metricKey => $metric) {
            $cards[] = [
                'key' => $metricKey,
                'label' => isset($metric['label']) ? $metric['label'] : $metricKey,
                'display_value' => isset($metric['display_value']) && $metric['display_value'] !== null ? $metric['display_value'] : '-',
                'status' => isset($metric['status']) ? $metric['status'] : 'neutral',
            ];
        }

        return $cards;
    }

    private function getScoreStatus($score)
    {
        if ($score === null || $score === '') {
            return 'neutral';
        }

        $normalizedScore = (float) $score;
        if ($normalizedScore >= 0.9) {
            return 'good';
        }
        if ($normalizedScore >= 0.5) {
            return 'warning';
        }

        return 'bad';
    }

    private function getMetricLabel($metricKey)
    {
        $labels = [
            'first-contentful-paint' => 'First Contentful Paint',
            'largest-contentful-paint' => 'Largest Contentful Paint',
            'speed-index' => 'Speed Index',
            'interactive' => 'Time to Interactive',
            'total-blocking-time' => 'Total Blocking Time',
            'cumulative-layout-shift' => 'Cumulative Layout Shift',
        ];

        return isset($labels[$metricKey]) ? $labels[$metricKey] : $metricKey;
    }

    private function getMetricStatus($metricKey, $value)
    {
        if ($value === null || $value === '') {
            return 'neutral';
        }

        $numericValue = (float) $value;
        $thresholds = [
            'first-contentful-paint' => [1800, 3000],
            'largest-contentful-paint' => [2500, 4000],
            'speed-index' => [3400, 5800],
            'interactive' => [3800, 7300],
            'total-blocking-time' => [200, 600],
            'cumulative-layout-shift' => [0.1, 0.25],
        ];

        if (!isset($thresholds[$metricKey])) {
            return 'neutral';
        }

        if ($numericValue <= $thresholds[$metricKey][0]) {
            return 'good';
        }
        if ($numericValue <= $thresholds[$metricKey][1]) {
            return 'warning';
        }

        return 'bad';
    }

    private function groupAssetsForDisplay(array $assets)
    {
        $groups = [
            'css' => ['key' => 'css', 'label' => 'CSS', 'assets' => []],
            'js' => ['key' => 'js', 'label' => 'JavaScript', 'assets' => []],
            'media' => ['key' => 'media', 'label' => 'Media', 'assets' => []],
            'other' => ['key' => 'other', 'label' => 'Other', 'assets' => []],
        ];

        foreach ($assets as $asset) {
            $type = isset($asset['type']) ? (string) $asset['type'] : 'other';
            if (!isset($groups[$type])) {
                $type = 'other';
            }
            $groups[$type]['assets'][] = $asset;
        }

        return array_values(array_filter($groups, function ($group) {
            return !empty($group['assets']);
        }));
    }

    private function indexRulesByUrl(array $rules)
    {
        $indexedRules = [];

        foreach ($rules as $rule) {
            if (!isset($rule['asset_url'])) {
                continue;
            }

            $flags = $this->extractRuleFlags($rule);
            $rule['disable'] = $flags['disable'];
            $rule['defer'] = $flags['defer'];
            $rule['minify'] = $flags['minify'];
            $rule['load_after_window_load'] = $flags['load_after_window_load'];
            $normalizedUrl = $this->normalizeAssetUrlForUi($rule['asset_url']);
            $indexedRules[$normalizedUrl] = $rule;
        }

        return $indexedRules;
    }

    private function normalizeAssetUrlForUi($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $baseUrl = rtrim($this->getDetectedShopBaseUrl(), '/');
        if (strpos($url, '/') === 0) {
            return $baseUrl . $url;
        }

        return $baseUrl . '/' . ltrim($url, '/');
    }

    /**
     * Handles lightweight AJAX actions for the admin UI.
     */
    private function handleAjaxRequest()
    {
        if (!Tools::getValue('ajax')) {
            return;
        }

        try {
            $action = (string) Tools::getValue('action');
            if ($action === 'runAssetScan') {
                $page = $this->getSelectedAssetPage();
                if (empty($page)) {
                    throw new Exception('Selected page was not found.');
                }

                $paths = $this->assetScanStore->saveScan($page, $this->assetScannerClient->scanPage($page['url']));

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Asset scan completed.',
                    'reload_url' => $this->getAdminConfigurationLink(self::TAB_ASSETS) . '&prestaload_asset_page=' . urlencode((string) $page['key']),
                    'paths' => $paths,
                ]);
            }

            if ($action === 'generateCriticalCss') {
                $result = $this->generateCriticalCssFromRequest();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Critical CSS generated successfully.',
                    'entry' => $result,
                ]);
            }

            if ($action === 'saveAssetRule') {
                $savedRule = $this->saveAssetRuleFromRequest();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Asset rule updated.',
                    'rule' => $savedRule,
                ]);
            }

            if ($action === 'toggleAssetFlag') {
                $updatedRule = $this->toggleAssetFlagFromRequest();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Asset rule updated.',
                    'rule' => $updatedRule,
                ]);
            }

            if ($action === 'saveBulkAssetRules') {
                $savedCount = $this->saveBulkAssetRulesFromRequest();

                $this->jsonResponse([
                    'success' => true,
                    'message' => sprintf('Updated %d asset rules.', $savedCount),
                    'saved_count' => $savedCount,
                ]);
            }

            if ($action === 'minifyAsset') {
                $result = $this->minifyAssetFromRequest();

                $this->jsonResponse(array_merge([
                    'success' => true,
                ], $result));
            }

            if ($action === 'bulkMinifyAssets') {
                $result = $this->bulkMinifyAssetsFromRequest();

                $this->jsonResponse(array_merge([
                    'success' => true,
                ], $result));
            }

            if ($action === 'bulkClearMinifiedAssets') {
                $result = $this->bulkClearMinifiedAssetsFromRequest();

                $this->jsonResponse(array_merge([
                    'success' => true,
                ], $result));
            }

            $this->jsonResponse([
                'success' => false,
                'message' => 'Unknown AJAX action.',
            ]);
        } catch (Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function saveAssetRuleFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $action = trim((string) Tools::getValue('prestaload_asset_action', 'keep'));
        $assetUrl = trim((string) Tools::getValue('prestaload_asset_url', ''));
        $assetType = trim((string) Tools::getValue('prestaload_asset_type', 'other'));

        if ($assetUrl === '') {
            throw new Exception('Asset URL is required.');
        }

        if (!in_array($action, ['keep', 'defer', 'disable', 'minify', 'load_after_window_load'], true)) {
            throw new Exception('Invalid asset action.');
        }

        $rule = [
            'page_key' => $page['key'],
            'page_url' => $page['url'],
            'asset_url' => $assetUrl,
            'asset_type' => $assetType,
            'action' => $action,
        ];

        if (!$this->assetRuleStore->saveRule($rule)) {
            throw new Exception('Could not save the asset rule.');
        }
        $this->pageCache->clear();

        return $rule;
    }

    private function generateCriticalCssFromRequest()
    {
        $pageKey = trim((string) Tools::getValue('prestaload_critical_css_page', 'home'));
        $page = $this->criticalCssPageRegistry->getPageByKey($pageKey);
        if (empty($page)) {
            throw new Exception('Selected page type was not found.');
        }

        $result = $this->criticalCssScannerClient->generate($page['key'], $page['url']);
        $entry = $this->criticalCssStore->saveVariants($page, $result['variants']);
        $this->pageCache->clear();

        return $entry;
    }

    private function toggleAssetFlagFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $assetUrl = trim((string) Tools::getValue('prestaload_asset_url', ''));
        $assetType = trim((string) Tools::getValue('prestaload_asset_type', 'other'));
        $flag = trim((string) Tools::getValue('prestaload_asset_flag', ''));
        $enabled = (bool) (int) Tools::getValue('prestaload_asset_enabled', 0);

        if ($assetUrl === '') {
            throw new Exception('Asset URL is required.');
        }

        if (!in_array($flag, ['disable', 'defer', 'minify', 'load_after_window_load'], true)) {
            throw new Exception('Invalid asset flag.');
        }

        if ($flag === 'load_after_window_load' && $assetType !== 'js') {
            throw new Exception('This flag is only available for JavaScript assets.');
        }

        $existingRule = $this->assetRuleStore->getRule($page['key'], $assetUrl);
        $flags = $this->extractRuleFlags($existingRule);
        $flags[$flag] = $enabled;

        if ($flag === 'disable' && $enabled) {
            $flags['defer'] = false;
            $flags['minify'] = false;
            $flags['load_after_window_load'] = false;
        }

        if ($flag === 'defer' && $enabled) {
            $flags['disable'] = false;
            $flags['load_after_window_load'] = false;
        }

        if ($flag === 'load_after_window_load' && $enabled) {
            $flags['disable'] = false;
            $flags['defer'] = false;
        }

        if ($flag === 'minify' && $enabled) {
            $flags['disable'] = false;
        }

        if ($flag === 'minify') {
            if ($enabled) {
                $minifiedUrl = $this->assetMinifier->getMinifiedAssetUrl($assetUrl, $assetType);
                if ($minifiedUrl === '') {
                    throw new Exception('Could not build the minified asset.');
                }
            } else {
                $this->assetMinifier->clearMinifiedAsset($assetUrl, $assetType);
            }
        }

        $rule = [
            'page_key' => $page['key'],
            'page_url' => $page['url'],
            'asset_url' => $assetUrl,
            'asset_type' => $assetType,
            'disable' => (int) $flags['disable'],
            'defer' => (int) $flags['defer'],
            'minify' => (int) $flags['minify'],
            'load_after_window_load' => (int) $flags['load_after_window_load'],
            'action' => $this->deriveRuleAction($flags),
        ];

        if (!$this->assetRuleStore->saveRule($rule)) {
            throw new Exception('Could not save the asset rule.');
        }
        $this->pageCache->clear();

        return $rule;
    }

    private function saveBulkAssetRulesFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $action = trim((string) Tools::getValue('prestaload_asset_action', 'defer'));
        if (!in_array($action, ['keep', 'defer', 'disable', 'minify', 'load_after_window_load'], true)) {
            throw new Exception('Invalid asset action.');
        }

        $assetUrls = Tools::getValue('prestaload_asset_urls', []);
        $assetTypes = Tools::getValue('prestaload_asset_types', []);

        if (!is_array($assetUrls) || empty($assetUrls)) {
            throw new Exception('Select at least one asset.');
        }

        $savedCount = 0;
        foreach ($assetUrls as $index => $assetUrl) {
            $assetUrl = trim((string) $assetUrl);
            if ($assetUrl === '') {
                continue;
            }

            $assetType = isset($assetTypes[$index]) ? trim((string) $assetTypes[$index]) : 'other';
            $existingRule = $this->assetRuleStore->getRule($page['key'], $assetUrl);
            $flags = $this->extractRuleFlags($existingRule);

            if ($action === 'keep') {
                $flags = [
                    'disable' => false,
                    'defer' => false,
                    'minify' => false,
                    'load_after_window_load' => false,
                ];
            } elseif ($action === 'disable') {
                $flags = [
                    'disable' => true,
                    'defer' => false,
                    'minify' => false,
                    'load_after_window_load' => false,
                ];
            } elseif ($action === 'defer') {
                $flags['disable'] = false;
                $flags['defer'] = true;
                $flags['load_after_window_load'] = false;
            } elseif ($action === 'load_after_window_load' && $assetType === 'js') {
                $flags['disable'] = false;
                $flags['defer'] = false;
                $flags['load_after_window_load'] = true;
            } elseif ($action === 'minify') {
                $flags['disable'] = false;
                $flags['minify'] = true;
            }

            if (!$this->assetRuleStore->saveRule([
                'page_key' => $page['key'],
                'page_url' => $page['url'],
                'asset_url' => $assetUrl,
                'asset_type' => $assetType,
                'disable' => (int) $flags['disable'],
                'defer' => (int) $flags['defer'],
                'minify' => (int) $flags['minify'],
                'load_after_window_load' => (int) $flags['load_after_window_load'],
                'action' => $this->deriveRuleAction($flags),
            ])) {
                throw new Exception('Could not save one of the selected asset rules.');
            }
            ++$savedCount;
        }

        if ($savedCount === 0) {
            throw new Exception('Select at least one asset.');
        }

        $this->pageCache->clear();

        return $savedCount;
    }

    private function minifyAssetFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $assetUrl = trim((string) Tools::getValue('prestaload_asset_url', ''));
        $assetType = trim((string) Tools::getValue('prestaload_asset_type', ''));
        if ($assetUrl === '') {
            throw new Exception('Asset URL is required.');
        }

        if (!in_array($assetType, ['css', 'js'], true)) {
            throw new Exception('Only CSS and JavaScript assets can be minified.');
        }

        $minifiedUrl = $this->assetMinifier->getMinifiedAssetUrl($assetUrl, $assetType);
        if ($minifiedUrl === '') {
            throw new Exception('Could not build the minified asset.');
        }

        $rule = [
            'page_key' => $page['key'],
            'page_url' => $page['url'],
            'asset_url' => $assetUrl,
            'asset_type' => $assetType,
            'action' => 'minify',
        ];

        if (!$this->assetRuleStore->saveRule($rule)) {
            throw new Exception('Could not save the minify rule.');
        }
        $this->pageCache->clear();

        return [
            'message' => 'Asset minified successfully.',
            'minified_url' => $minifiedUrl,
            'rule' => $rule,
        ];
    }

    private function bulkMinifyAssetsFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $assetUrls = Tools::getValue('prestaload_asset_urls', []);
        $assetTypes = Tools::getValue('prestaload_asset_types', []);
        if (!is_array($assetUrls) || empty($assetUrls)) {
            throw new Exception('Select at least one asset.');
        }

        $processed = 0;
        foreach ($assetUrls as $index => $assetUrl) {
            $assetUrl = trim((string) $assetUrl);
            $assetType = isset($assetTypes[$index]) ? trim((string) $assetTypes[$index]) : '';

            if ($assetUrl === '' || !in_array($assetType, ['css', 'js'], true)) {
                continue;
            }

            $minifiedUrl = $this->assetMinifier->getMinifiedAssetUrl($assetUrl, $assetType);
            if ($minifiedUrl === '') {
                continue;
            }

            if (!$this->assetRuleStore->saveRule([
                'page_key' => $page['key'],
                'page_url' => $page['url'],
                'asset_url' => $assetUrl,
                'asset_type' => $assetType,
                'disable' => 0,
                'defer' => 0,
                'minify' => 1,
                'load_after_window_load' => 0,
                'action' => 'minify',
            ])) {
                throw new Exception('Could not save one of the selected minify rules.');
            }
            ++$processed;
        }

        if ($processed === 0) {
            throw new Exception('Could not minify the selected assets.');
        }

        $this->pageCache->clear();

        return [
            'message' => sprintf('Minified %d assets.', $processed),
            'processed_count' => $processed,
        ];
    }

    private function bulkClearMinifiedAssetsFromRequest()
    {
        $page = $this->getSelectedAssetPage();
        if (empty($page)) {
            throw new Exception('Selected page was not found.');
        }

        $assetUrls = Tools::getValue('prestaload_asset_urls', []);
        $assetTypes = Tools::getValue('prestaload_asset_types', []);
        if (!is_array($assetUrls) || empty($assetUrls)) {
            throw new Exception('Select at least one asset.');
        }

        $processed = 0;
        foreach ($assetUrls as $index => $assetUrl) {
            $assetUrl = trim((string) $assetUrl);
            $assetType = isset($assetTypes[$index]) ? trim((string) $assetTypes[$index]) : '';

            if ($assetUrl === '' || !in_array($assetType, ['css', 'js'], true)) {
                continue;
            }

            $this->assetMinifier->clearMinifiedAsset($assetUrl, $assetType);
            $existingRule = $this->assetRuleStore->getRule($page['key'], $assetUrl);
            $flags = $this->extractRuleFlags($existingRule);
            $flags['minify'] = false;
            if (!$this->assetRuleStore->saveRule([
                'page_key' => $page['key'],
                'page_url' => $page['url'],
                'asset_url' => $assetUrl,
                'asset_type' => $assetType,
                'disable' => (int) $flags['disable'],
                'defer' => (int) $flags['defer'],
                'minify' => 0,
                'load_after_window_load' => (int) $flags['load_after_window_load'],
                'action' => $this->deriveRuleAction($flags),
            ])) {
                throw new Exception('Could not clear minified state for one of the selected assets.');
            }
            ++$processed;
        }

        if ($processed === 0) {
            throw new Exception('Could not clear the selected minified assets.');
        }

        $this->pageCache->clear();

        return [
            'message' => sprintf('Cleared minified state for %d assets.', $processed),
            'processed_count' => $processed,
        ];
    }

    private function getAjaxConfigurationLink($action)
    {
        return $this->getAdminConfigurationLink($this->getActiveTab())
            . '&ajax=1&action=' . urlencode((string) $action);
    }

    private function jsonResponse(array $payload)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        $encoded = json_encode($payload);
        if ($encoded === false) {
            $encoded = json_encode([
                'success' => false,
                'message' => 'JSON encoding failed.',
            ]);
        }

        exit($encoded);
    }

    private function extractRuleFlags(array $rule)
    {
        return [
            'disable' => !empty($rule['disable']) || (isset($rule['action']) && $rule['action'] === 'disable'),
            'defer' => !empty($rule['defer']) || (isset($rule['action']) && $rule['action'] === 'defer'),
            'minify' => !empty($rule['minify']) || (isset($rule['action']) && $rule['action'] === 'minify'),
            'load_after_window_load' => !empty($rule['load_after_window_load']) || (isset($rule['action']) && $rule['action'] === 'load_after_window_load'),
        ];
    }

    private function decorateCriticalCssPages(array $pages)
    {
        $entries = $this->criticalCssStore->getEntries();

        foreach ($pages as &$page) {
            $entry = isset($entries[$page['key']]) ? $entries[$page['key']] : [];
            $page['critical_css'] = [
                'generated' => !empty($entry['devices']),
                'mobile' => [
                    'generated' => !empty($entry['devices']['mobile']),
                    'size_bytes' => isset($entry['devices']['mobile']['size_bytes']) ? (int) $entry['devices']['mobile']['size_bytes'] : 0,
                    'generated_at' => isset($entry['devices']['mobile']['generated_at']) ? (string) $entry['devices']['mobile']['generated_at'] : '',
                ],
                'desktop' => [
                    'generated' => !empty($entry['devices']['desktop']),
                    'size_bytes' => isset($entry['devices']['desktop']['size_bytes']) ? (int) $entry['devices']['desktop']['size_bytes'] : 0,
                    'generated_at' => isset($entry['devices']['desktop']['generated_at']) ? (string) $entry['devices']['desktop']['generated_at'] : '',
                ],
            ];
        }
        unset($page);

        return $pages;
    }

    private function deriveRuleAction(array $flags)
    {
        $enabledFlags = array_keys(array_filter($flags));
        if (count($enabledFlags) > 1) {
            return 'composed';
        }

        if (empty($enabledFlags)) {
            return 'keep';
        }

        return $enabledFlags[0];
    }
}
