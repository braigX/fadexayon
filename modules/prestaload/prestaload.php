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
require_once __DIR__ . '/classes/PrestaLoadCacheStore.php';
require_once __DIR__ . '/classes/PrestaLoadPageCache.php';

class PrestaLoad extends Module
{
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

        if (Tools::isSubmit('submitPrestaLoadSettings')) {
            $this->settings->updateFromRequest();
            $output .= $this->displayConfirmation($this->trans('Settings updated.', [], 'Admin.Notifications.Success'));
        }

        if (Tools::isSubmit('submitPrestaLoadClearCache')) {
            $this->pageCache->clear();
            $output .= $this->displayConfirmation($this->trans('Full-page cache cleared.', [], 'Admin.Notifications.Success'));
        }

        $this->context->smarty->assign([
            'prestaload_stats' => $this->pageCache->getStats(),
            'prestaload_settings_form' => $this->renderSettingsForm(),
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
        $store = new PrestaLoadCacheStore($this->settings->getCacheDirectory());

        return new PrestaLoadPageCache($this->context, $this->settings, $eligibility, $keyBuilder, $store);
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

    private function renderSettingsForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->submit_action = 'submitPrestaLoadSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = 0;
        $helper->tpl_vars = [
            'fields_value' => $this->settings->getFormValues(),
        ];

        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
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
                        'class' => 'fixed-width-sm',
                        'desc' => 'How long one cached page stays valid before it expires.',
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
                    'name' => 'submitPrestaLoadSettings',
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
        ];

        return $helper->generateForm([$fieldsForm]);
    }
}
