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
require_once __DIR__ . '/classes/PrestaLoadFontOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadCssOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadImgProxyUrlBuilder.php';
require_once __DIR__ . '/classes/PrestaLoadImageOptimizer.php';
require_once __DIR__ . '/classes/PrestaLoadHtmlOptimizer.php';

class PrestaLoad extends Module
{
    private const TAB_GENERAL = 'general';
    private const TAB_FONTS = 'fonts';
    private const TAB_CSS = 'css';
    private const TAB_IMAGES = 'images';

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
        $this->pageCache = $this->buildPageCache();
    }

    /**
     * Install default settings and register cache hooks.
     */
    public function install()
    {
        return parent::install()
            && $this->settings->installDefaults()
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
        $output = '';
        $activeTab = $this->getActiveTab();

        if (Tools::isSubmit('submitPrestaLoadGeneralSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_ENABLED,
                PrestaLoadCacheSettings::CONFIG_TTL,
                PrestaLoadCacheSettings::CONFIG_ALLOWED_CONTROLLERS,
            ]);
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('General settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadFontSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_FONT_OPTIMIZATION_ENABLED,
            ]);
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Font settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadCssSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_CSS_OPTIMIZATION_ENABLED,
            ]);
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('CSS settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadImageSettings')) {
            $this->settings->updateSubsetFromRequest([
                PrestaLoadCacheSettings::CONFIG_IMAGE_OPTIMIZATION_ENABLED,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_BASE_URL,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_QUALITY,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_KEY,
                PrestaLoadCacheSettings::CONFIG_IMGPROXY_SALT,
            ]);
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Image settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadClearCache')) {
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Full-page cache cleared.', [], 'Admin.Notifications.Success'));
        }

        $this->context->smarty->assign([
            'prestaload_active_tab' => $activeTab,
            'prestaload_tabs' => $this->getAdminTabs(),
            'prestaload_stats' => $this->pageCache->getStats(),
            'prestaload_settings_form' => $this->renderSettingsForm($activeTab),
        ]);

        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    /**
     * Cache hit path. Runs before full controller execution.
     */
    public function hookActionDispatcher($params)
    {
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
        $keyBuilder = new PrestaLoadCacheKeyBuilder($this->context);
        $logger = new PrestaLoadCacheLogger($this->settings->getLogFile());
        $store = new PrestaLoadCacheStore($this->settings->getCacheDirectory());
        $fontOptimizer = new PrestaLoadFontOptimizer($this->settings);
        $cssOptimizer = new PrestaLoadCssOptimizer($this->settings);
        $imgProxyUrlBuilder = new PrestaLoadImgProxyUrlBuilder($this->settings);
        $imageOptimizer = new PrestaLoadImageOptimizer($this->settings, $imgProxyUrlBuilder);
        $htmlOptimizer = new PrestaLoadHtmlOptimizer($fontOptimizer, $cssOptimizer, $imageOptimizer);

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

        $forms = [
            self::TAB_GENERAL => [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('General', [], 'Admin.Global'),
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
                            'desc' => 'Example: http://127.0.0.1:8094',
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
                'label' => 'General',
                'link' => $this->getAdminConfigurationLink(self::TAB_GENERAL),
            ],
            self::TAB_FONTS => [
                'label' => 'Fonts',
                'link' => $this->getAdminConfigurationLink(self::TAB_FONTS),
            ],
            self::TAB_CSS => [
                'label' => 'CSS',
                'link' => $this->getAdminConfigurationLink(self::TAB_CSS),
            ],
            self::TAB_IMAGES => [
                'label' => 'Images',
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
}
