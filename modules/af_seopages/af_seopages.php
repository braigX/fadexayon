<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Af_SeoPages extends Module
{
    public function __construct()
    {
        if (!defined('_PS_VERSION_')) {
            exit;
        }
        $this->name = 'af_seopages';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->ps_versions_compliancy = ['min' => '1.6.0.4', 'max' => _PS_VERSION_];
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->module_key = 'f4b759edacab7f521bbf0a82e30d495a';
        $this->bootstrap = true;
        $this->dependencies = ['amazzingfilter'];
        parent::__construct();
        $this->displayName = $this->l('SEO Pages for Amazzing Filter');
        $this->description = $this->l('Configurable SEO pages for Amazzing Filter');
        $this->definePublicVariables();
        // Configuration::deleteByName('PS_ROUTE_' . $this->fc_identifier);
    }

    public function definePublicVariables()
    {
        $this->db = Db::getInstance();
        $this->t_name = 'af_seopage';
        $this->default_id = 1;
        $this->fc_identifier = 'module-af_seopages-seopage';
        $this->id_shop = $this->context->shop->id;
        $this->id_lang = $this->getIDLang();
        $this->is_modern = Tools::substr(_PS_VERSION_, 0, 3) != '1.6';
        $this->x = [];
        $this->af_min_v = '3.3.0';
    }

    public function getIDLang()
    {
        return !defined('_PS_ADMIN_DIR_') ? $this->context->language->id : Configuration::get('PS_LANG_DEFAULT');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook('moduleRoutes')
            && $this->updatePosition(Hook::getIdByName('moduleRoutes'), 0, 1)
            && $this->registerHook('gSitemapAppendUrls')
            && $this->dataBase('install')
            && $this->prepareSettingsAndData();
    }

    public function prepareSettingsAndData()
    {
        $this->pageData('addDefault');
        $this->fillMissingSettings();

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->dataBase('uninstall') && $this->sitemap()->clear();
    }

    public function hookModuleRoutes()
    {
        $rule = $regexp = '';
        if (!defined('_PS_ADMIN_DIR_')) {
            $this->x['route'] = $this->getRouteSettings($this->id_shop);
            if ($this->x['route']['base']) {
                $rule = $this->url('getRoute') . '{rewrite}';
                $regexp = '[\/\+_a-zA-Z0-9-\pL]*';
            } else {
                $possible_link_rewrite = $this->url('getPossibleLinkRerwite'); // possible slashes are trimmed
                $params = ['multilang' => 1, 'f' => ['active' => 1, 'link_rewrite' => $possible_link_rewrite]];
                if ($this->x['matched_seopage'] = $this->pageData('get', $params)) {
                    $rule = $this->x['matched_seopage']['link_rewrite'] . '{rewrite}';
                    $regexp = '[\/]*'; // optional slash
                }
            }
        }

        return $rule ? [
            $this->fc_identifier => [
                'controller' => 'seopage',
                'rule' => $rule,
                'keywords' => ['rewrite' => ['regexp' => $regexp]],
                'params' => ['fc' => 'module', 'module' => $this->name],
            ],
        ] : [];
    }

    public function addJS($file_name, $custom_path = '')
    {
        $path = ($custom_path ? $custom_path : 'modules/' . $this->name . '/views/js/') . $file_name;
        if ($this->is_modern) {
            // priority should be more than 90 in order to be loaded after jqueryUI
            $params = ['server' => $custom_path ? 'remote' : 'local', 'priority' => 100];
            $this->context->controller->registerJavascript(sha1($path), $path, $params);
        } else {
            $path = $custom_path ? $path : __PS_BASE_URI__ . $path;
            $this->context->controller->addJS($path);
        }
    }

    public function hookGSitemapAppendUrls($params)
    {
        return $this->sitemap()->getLinksForGSitemap($params);
    }

    public function defineSettings()
    {
        if (!isset($this->settings)) {
            $this->af()->defineSettings();
            $this->settings = $this->af()->settings['seopage'];
            if (!isset($this->x['route'])) {
                $this->x['route'] = $this->getRouteSettings($this->id_shop);
            }
        }
    }

    public function extraSettingsActions($saved_settings, $shop_ids)
    {
        if (isset($saved_settings['route_base'])) {
            // save route settings in global config for fast access in hookModuleRoutes
            foreach ($shop_ids as $id_shop) {
                $new_route_settings = [
                    'base' => $saved_settings['route_base'],
                    'ext' => $saved_settings['route_ext'],
                ];
                if ($new_route_settings != $this->getRouteSettings($id_shop)) {
                    $this->saveRouteSettings($new_route_settings, $id_shop);
                    $this->x['route'] = $new_route_settings;
                    $this->sitemap()->updateAll([$id_shop]);
                }
            }
        }
    }

    public function getRouteSettings($id_shop)
    {
        return json_decode(Configuration::get('AFSP_ROUTE', null, null, $id_shop), true);
    }

    public function saveRouteSettings($settings, $id_shop)
    {
        $settings += ['base' => 0, 'ext' => 0];

        return Configuration::updateValue('AFSP_ROUTE', json_encode($settings), false, null, $id_shop);
    }

    public function fillMissingSettings($upd_keys = [])
    {
        if (empty($this->af()->sp)) {
            $this->af()->sp = $this; // required in af->saveSettings()
        }
        foreach ($this->shopIDs('all') as $id_shop) {
            $already_saved = $this->af()->getSavedSettings($id_shop, 'seopage');
            foreach ($upd_keys as $old_key => $new_key) {
                if (isset($already_saved[$old_key])) {
                    $already_saved[$new_key] = $already_saved[$old_key];
                    unset($already_saved[$old_key]);
                }
            }
            $this->af()->saveSettings('seopage', $already_saved, [$id_shop]);
        }
    }

    public function isDefault($id_seopage)
    {
        return $id_seopage == $this->default_id;
    }

    public function ajaxAction($action)
    {
        $this->defineSettings();
        $ret = [];
        switch ($action) {
            case 'quickSearch':
                $this->context->smarty->assign([
                    'results' => $this->criteria('find', ['q' => Tools::getValue('q'), 'lang_ext' => 1]),
                ]);
                $ret['html'] = $this->display(__FILE__, 'views/templates/admin/qs-results.tpl');
                break;
            case 'getCriteriaDataForDisplay':
                $ret['data'] = $this->criteria('prepareForDisplay', Tools::getValue('identifiers'));
                break;
            case 'renderSeoPageForm':
                $id = Tools::getValue('id_seopage');
                $is_default = $this->isDefault($id);
                $ret = [
                    'title' => !$is_default ? $this->l('SEO Page Data') : $this->l('Main SEO page'),
                    'content' => $this->renderForm($id, $is_default, Tools::getValue('is_duplicate')),
                ];
                break;
            case 'setStatus':
                $params = ['id_seopage' => Tools::getValue('id_seopage'), 'active' => Tools::getValue('active')];
                $ret['saved'] = $params['id_seopage'] && $this->pageData('setStatus', $params);
                break;
            case 'renderPossibleDuplicates':
                $params = [
                    'criteria' => Tools::getValue('criteria'),
                    'id_seopage' => Tools::getValue('id_seopage'),
                ];
                $duplicates = [
                    'sp' => [
                        'label' => $this->l('The following SEO page(s) use same criteria'),
                        'items' => [],
                    ],
                    'standard' => [
                        'label' => $this->l('The following standard page(s) include same products'),
                        'items' => [],
                    ],
                ];
                if ($params['criteria'] || $params['id_seopage'] == $this->default_id) {
                    $duplicates['sp']['items'] = $this->pageData('getDuplicatesByCriteria', $params);
                    $duplicates['standard']['items'] = $this->getStandardPageDuplicates($params['criteria']);
                    $canonical_action = '';
                    if ($this->settings['canonical'] == 1) {
                        $canonical_action = $this->l('They will be redirected to %s');
                    } elseif ($this->settings['canonical'] == 2) {
                        $canonical_action = $this->l('They will have rel=canonical of %s');
                    }
                    if ($canonical_action) {
                        $possible_primary_id_seopage = (int) current(array_keys($duplicates['sp']['items']));
                        if (!$possible_primary_id_seopage
                            || ($params['id_seopage'] && $params['id_seopage'] < $possible_primary_id_seopage)) {
                            $sprintf = $this->l('current SEO Page');
                        } else {
                            $sprintf = $duplicates['sp']['items'][$possible_primary_id_seopage]['link'];
                        }
                        $duplicates['standard']['canonical_action'] = sprintf($canonical_action, $sprintf);
                    }
                }
                $this->context->smarty->assign(['duplicates' => $duplicates]);
                $ret['html'] = $this->display(__FILE__, 'views/templates/admin/sp-item-duplicates.tpl');
                break;
            case 'loadSeoPages':
                $data = $this->pageData('getListing', $this->af()->parseStr(Tools::getValue('list_params')));
                $this->context->smarty->assign($data);
                $ret['html'] = [
                    'items' => $this->display(__FILE__, 'views/templates/admin/sp-items.tpl'),
                    'pagination' => $this->af()->display(
                        $this->af()->local_path,
                        'views/templates/admin/pagination.tpl'
                    ),
                ];
                break;
            case 'saveSeoPage':
                if ($data = $this->af()->parseStr(Tools::getValue('seopage_data'))) {
                    $ret['saved'] = $id_seopage = $this->pageData('save', $data);
                    $filters = ['sp.id_seopage' => $id_seopage];
                    $this->context->smarty->assign($this->pageData('getListing', ['f' => $filters]));
                    $ret['item_html'] = $this->display(__FILE__, 'views/templates/admin/sp-items.tpl');
                }
                break;
            case 'deleteSeoPage':
                $ret['deleted'] = $this->pageData('delete', ['id_seopage' => Tools::getValue('id_seopage')]);
                break;
            case 'viewLog':
                $ret['title'] = $this->l('Log for current month');
                $ret['content'] = '<pre>' . $this->log('get') . '</pre>';
                break;
            case 'updSitemap':
                $identifier = Tools::getValue('identifier');
                $this->sitemap()->updateData($identifier);
                $ret = $this->sitemap()->getData($identifier);
                break;
            case 'bulkGenerateAction':
                $ret = $this->bulkGenerator()->ajaxAction();
                break;
        }
        exit(json_encode($ret));
    }

    public function addConfigData()
    {
        if (!$this->is_modern) {
            $this->retro('assignMCEJSVars');
        }
        $ext = '?v=' . $this->version;
        $this->context->controller->css_files[$this->_path . 'views/css/back.css' . $ext] = 'all';
        $this->context->controller->css_files[$this->_path . 'views/css/bulk-generate.css' . $ext] = 'all';
        $this->context->controller->js_files[] = $this->_path . 'views/js/back.js' . $ext;
        $this->context->controller->js_files[] = $this->_path . 'views/js/bulk-generate.js' . $ext;
        $default_sorting = ['by' => 'sp.id_seopage', 'way' => 'DESC'];
        $this->context->smarty->assign(['sp' => [
            'tpl' => [
                'side_panel' => $this->local_path . 'views/templates/admin/side-panel.tpl',
                'center_panel' => $this->local_path . 'views/templates/admin/center-panel.tpl',
            ],
            'sitemaps' => $this->sitemap()->getAlldata(),
            'g' => $this->bulkGenerator()->getSmartyVariables(),
            'default_sorting' => $default_sorting,
            'info' => [
                'version' => $this->version,
                'changelog' => $this->_path . 'Readme.md?v=' . $this->version,
                'documentation' => $this->_path . 'readme_en.pdf?v=' . $this->version,
            ],
        ] + $this->pageData('getListing', ['order' => $default_sorting])]);
        if (version_compare($this->af()->version, $this->af_min_v, '<')) {
            $this->context->controller->warnings[] = 'Please upgrade Amazzing Filter to at least v'
                . $this->af_min_v . ' for full compatibility with SEO Pages';
        }
    }

    public function extendInitialParams(&$filters, &$params, $current_controller)
    {
        if (!empty($this->context->controller->seopage_data)) {
            $hidden_criteria = $hidden_values = $custom_base = [];
            foreach ($this->context->controller->seopage_data['required_filters'] as $key => $forced_values) {
                foreach ($forced_values as $id) {
                    if ($key == 'c' && !isset($filters[$key]['values'][$id])) {
                        $key = $this->getAlternativeCategoryKey($filters, $id) ?: $key;
                    }
                    if (!isset($filters[$key]['values'][$id])) {
                        $hidden_criteria[] = $key . ':' . $id;
                        $hidden_values[$key][$id] = $id;
                    } elseif (empty($filters[$key]['values'][$id]['selected'])) {
                        // only if selection was not applied in prepareFiltersData(), or cf()->extendInitialParams()
                        $filters[$key]['has_selection'] = 1;
                        $filters[$key]['values'][$id]['selected'] = 1;
                        $params['filters'][$filters[$key]['first_char']][$filters[$key]['id_group']][$id] = $id;
                    }
                }
            }
            if ($hidden_criteria = $this->criteria('sort', $hidden_criteria)) {
                $custom_base = $hidden_criteria == $this->context->controller->seopage_data['criteria']
                    ? $this->context->controller->seopage_data
                    : $this->pageData('get', ['f' => ['criteria' => $hidden_criteria, 'active' => 1]]);
                $this->processHiddenValues($params, $hidden_values);
            }
            // it was initiated in af()->hookHeader, so we can define header vars here
            if ($custom_base) {
                $js_def = [
                    'af_sp_base_id' => $custom_base['id_seopage'],
                    'af_sp_base_url' => $custom_base['canonical'],
                    'af_sp_custom_base' => 1,
                ];
            } else {
                $js_def = [
                    'af_sp_base_id' => $this->default_id,
                    'af_sp_base_url' => $this->url('build', ['is_default' => 1]),
                    'af_sp_custom_base' => 0,
                ];
            }
            Media::addJsDef($js_def);
            $this->addJS('front.js');
            if (!$this->is_modern) {
                $this->context->controller->addCSS(_THEME_DIR_ . 'css/product_list.css', 'all');
            }
        } elseif ($current_controller != 'search') {
            $this->defineSettings();
            if ($this->settings['canonical'] == 1) {
                $this->addJS('front.js');
                $this->addJS('front-extra.js');
                if (!$this->is_modern) {
                    $this->addJS('front-extra-16.js');
                }
            }
        }
        $this->processCanonical($params, $current_controller);
    }

    public function getAlternativeCategoryKey($filters, $id)
    {
        foreach ($filters as $key => $f) {
            if ($key[0] == 'c' && isset($f['values'][$id])) {
                return $key;
            }
        }
    }

    public function processHiddenValues(&$params, $hidden_values)
    {
        $hidden_filters = $this->getSmartyValue('hidden_filters', []); // may be defined in CustomerFilters
        foreach ($hidden_values as $key => $ids) {
            if (isset($hidden_filters[$key])) {
                $f = $hidden_filters[$key];
            } else {
                $f = $hidden_filters[$key] = $this->prepareHiddenGroup($key);
            }
            foreach (array_intersect_key($f['values'], $ids) as $id => $value) {
                if (isset($hidden_filters[$key]['forced_values'][$id])) {
                    $hidden_filters[$key]['forced_values'][$id]['class'] .= ' sp-hidden-filter';
                } else {
                    $hidden_filters[$key]['forced_values'][$id] = $value + ['class' => 'sp-hidden-filter'];
                    // available_options may be used in indexationData('prepareQuery')
                    $params['available_options'][$f['first_char']][$f['id_group']][$id] = $id;
                    if ($f['first_char'] == 'a') {
                        // available options for 'a' may be used in prepareCountData, and then extra_count_a
                        $available_options = array_column($f['values'], 'id', 'id');
                        $params['available_options'][$f['first_char']][$f['id_group']] += $available_options;
                    }
                    $params['filters'][$f['first_char']][$f['id_group']][$id] = $id;
                }
            }
        }
        $this->context->smarty->assign(['hidden_filters' => $hidden_filters]);
    }

    public function prepareHiddenGroup($key, $f = [])
    {
        if (!isset($this->f_names)) {
            $this->f_names = [
                'special' => $this->af()->getSpecialFilters(),
                'standard' => $this->af()->getStandardFilters(),
            ];
        }
        $f += ['name' => '', 'type' => 0, 'is_slider' => 0, 'forced_values' => []];
        if ($f['special'] = isset($this->f_names['special'][$key])) {
            $f['first_char'] = $key;
            $f['id_group'] = 0;
            $f['name'] = $f['name'] ?: $this->f_names['special'][$key];
            $f['values'] = [1 => ['name' => $f['name'], 'id' => 1, 'link' => 1, 'identifier' => $key]];
        } else {
            $f['first_char'] = Tools::substr($key, 0, 1);
            $f['id_group'] = (int) Tools::substr($key, 1);
            if ($f['first_char'] == 'c') {
                $f['id_parent'] = $this->context->shop->getCategory();
                $f['nesting_lvl'] = $f['id_group'] = 0; // id_group=0 to use 'and' with other possible 'c' filters
                $f['name'] = $f['name'] ?: $this->l('Categories');
            }
            $f['values'] = $this->af()->getFilterValues($f, $key);
            if (!$f['name']) {
                $first_value = current($f['values']) ?: [];
                if (isset($this->f_names['standard'][$key])) {
                    $f['name'] = $this->f_names['standard'][$key];
                } elseif (isset($first_value['group_name'])) {
                    $f['name'] = $first_value['group_name'];
                } else {
                    $f['name'] = $this->af()->getGroupName($f);
                }
            }
        }
        $f['link'] = !empty($f['link']) ? $f['link'] : $this->af()->generateLink($f['name'], $key);
        $f['submit_name'] = 'filters[' . $f['first_char'] . '][' . $f['id_group'] . '][]';

        return $f;
    }

    public function extendAjaxResponse(&$response, $params)
    {
        $this->defineSettings();
        $base_id = isset($params['sp_base_id']) ? $params['sp_base_id'] : false;
        if ((!$base_id && $this->settings['canonical'] != 1) || $params['current_controller'] == 'search') {
            return;
        }
        $criteria = $this->criteria('getByParams', $params);
        $matched_seopage = $this->pageData('get', ['f' => ['criteria' => $criteria, 'active' => 1]]);
        if (!$matched_seopage && $base_id) {
            $matched_seopage = $this->pageData('get', ['f' => ['sp.id_seopage' => $base_id]]);
        }
        if ($response['seopage'] = $matched_seopage) {
            if ($matched_seopage['id_seopage'] != $base_id) {
                $response['seopage']['upd_url'] = $matched_seopage['canonical'];
            }
            if ($base_id) { // temporary: dynamically update breadcrumbs only on SEO pages
                $this->specificThemeAjaxActions($params);
                $response['seopage']['breadcrumbs'] = $this->displayBreadCrumbs($matched_seopage);
            }
        }
    }

    public function specificThemeAjaxActions(&$params)
    {
        switch ($this->af()->getSpecificThemeIdentifier()) {
            case 'warehouse-17':
                $iqit_options = json_decode(Configuration::get('iqitthemeed_options'), true);
                $bread_width = isset($iqit_options['bread_width']) ? $iqit_options['bread_width'] : 'inherit';
                // required for breadcrumb.tpl
                $this->context->smarty->assign(['iqitTheme' => ['bread_width' => $bread_width]]);
                break;
            case 'warehouse-16':
                $vars = ['breadcrumb_width' => Configuration::get('thmedit_breadcrumb_width')];
                $this->context->smarty->assign(['warehouse_vars' => $vars]);
                break;
        }
    }

    public function getSettingsFields()
    {
        $fields = [
            'lc' => [
                'display_name' => $this->l('Display left column'),
                'value' => 1,
                'type' => 'switcher',
            ],
            'rc' => [
                'display_name' => $this->l('Display right column'),
                'value' => 0,
                'type' => 'switcher',
            ],
            'route_base' => [
                'display_name' => sprintf($this->l('Include %s in URL'), '/catalog/'),
                'value' => 1,
                'type' => 'switcher',
                'warning' => $this->l('Experimental option. Please test it thoroughly'),
                'class' => 'warn-if-0',
                'subtitle' => $this->l('URL settings'),
            ],
            'route_ext' => [
                'display_name' => $this->l('URL extension'),
                'value' => 0,
                'type' => 'select',
                'options' => [
                    0 => $this->l('None'),
                    1 => '/',
                ],
            ],
            'canonical' => [
                'display_name' => $this->l('Native pages with duplicate content'),
                'tooltip' => $this->l('Categories, manufacturer pages, etc.'),
                'value' => 1,
                'type' => 'select',
                'options' => [
                    0 => $this->l('are ignored'),
                    1 => $this->l('are redirected to matching SEO page'),
                    2 => $this->l('have rel=canonical of matching SEO page'),
                ],
                'subtitle' => $this->l('Duplicate content management'),
            ],
            'log_canonical' => [
                'display_name' => $this->l('Log accessing pages with duplicate content'),
                'value' => 0,
                'type' => 'switcher',
                'class' => 'log-settings',
            ],
            'page_canonical' => [
                'display_name' => $this->l('Canonicals for pagination'),
                'value' => 1,
                'type' => 'select',
                'options' => [
                    0 => $this->l('Use 1st page canonical for all pages'),
                    1 => $this->l('Each page has own canonical +  rel=next/prev in header'),
                ],
                'subtitle' => $this->l('Canonicals for SEO Page variations'),
            ],
            'order_canonical' => [
                'display_name' => $this->l('Canonicals for different sorting results'),
                'value' => 0,
                'type' => 'select',
                'options' => [
                    0 => $this->l('Use default canonical for all sorting results'),
                    1 => $this->l('Each sorting result has own canonical'),
                ],
            ],
            'bc_parents' => [
                'display_name' => $this->l('Include parent items in breadcrumbs'),
                'tooltip' => $this->l('Selected categories/manufacturers that have an associated SEO page'),
                'value' => 1,
                'type' => 'switcher',
                'subtitle' => $this->l('Other settings'),
            ],
            'gsitemap_hook' => [
                'display_name' => $this->l('Integrate with Google sitemap module'),
                'value' => 1,
                'type' => 'switcher',
            ],
            'sitemap_type' => [
                'display_name' => 'Sitemap type',
                'value' => 'xml',
                'type' => 'select',
                'options' => ['txt' => 'txt', 'xml' => 'xml'],
                'class' => 'hidden',
            ],
        ];
        if (!Module::isInstalled('gsitemap')) {
            $fields['gsitemap_hook']['subtitle'] = '';
            $fields['gsitemap_hook']['class'] = 'hidden';
        }
        if (!$this->is_modern) {
            unset($fields['canonical']['options']['2']);
            unset($fields['page_canonical']['subtitle']);
            $fields['page_canonical']['class'] = 'hidden';
            $fields['order_canonical']['class'] = 'hidden';
        }

        return $fields;
    }

    public function getPageOptions()
    {
        $options = [];
        foreach ($this->db->executeS($this->dataBase('prepareQuery')) as $item) {
            $options[$item['id_seopage']] = $item['header'] ?: $item['meta_title'] ?: $item['link_rewrite'];
        }

        return $options;
    }

    public function pageData($action, $params = [])
    {
        $ret = [];
        switch ($action) {
            case 'get':
                if ($ret = $this->db->getRow($this->dataBase('prepareQuery', $params))) {
                    $ret['required_filters'] = [];
                    if ($ret['criteria']) {
                        foreach (explode('-', $ret['criteria']) as $cr) {
                            $cr = explode(':', $cr);
                            $ret['required_filters'][$cr[0]][$cr[1]] = $cr[1];
                        }
                    } else {
                        $ret['is_default'] = 1;
                    }
                    if (!empty($params['multilang'])) {
                        $ret['alternate'] = $this->pageData('getAlternateLinksData', $ret);
                        $ret['canonical'] = $ret['alternate'][$this->id_lang]['url'];
                    } else {
                        $ret['canonical'] = $this->url('build', $ret);
                    }
                }
                break;
            case 'getAlternateLinksData':
                $ret = $this->pageData('getMultilangLinks', ['id_seopage' => $params['id_seopage']]);
                foreach ($ret as $id_lang => $link_rewrite) {
                    $ret[$id_lang] = [
                        'url' => $this->url('build', [
                            'id_lang' => $id_lang,
                            'link_rewrite' => $link_rewrite,
                            'is_default' => !empty($params['is_default']),
                        ]),
                        'link_rewrite' => $link_rewrite,
                    ];
                }
                break;
            case 'getMultilangLinks':
                $lang_ids = !empty($params['lang_ids']) ? $params['lang_ids']
                    : $this->af()->getAvailableLanguages(true, !defined('_PS_ADMIN_DIR_'));
                $shop_ids = !empty($params['shop_ids']) ? $params['shop_ids'] : $this->shopIDs();
                $ret = array_column($this->db->executeS('
                    SELECT id_lang, link_rewrite FROM ' . $this->sqlTable('_lang') . '
                    WHERE id_seopage = ' . (int) $params['id_seopage'] . '
                    AND id_lang IN (' . $this->sqlIDs($lang_ids) . ')
                    AND id_shop IN (' . $this->sqlIDs($shop_ids) . ')
                '), 'link_rewrite', 'id_lang');
                break;
            case 'getListing':
                $query = $this->dataBase('prepareQuery', $params + ['shop_ids' => $this->shopIDs()]);
                $query->groupBy('sp.id_seopage');
                if (!empty($params['return_total'])) {
                    $ret = count($this->db->executeS($query));
                } else {
                    $ret['pagination'] = $this->getConfigPaginationVariables($params);
                    $offset = ($ret['pagination']['p'] - 1) * $ret['pagination']['npp'];
                    $query->limit($ret['pagination']['npp'], $offset);
                    $ret['items'] = $this->db->executeS($query);
                    foreach ($ret['items'] as $k => $row) {
                        $row['criteria'] = $this->criteria('prepareForDisplay', $row['criteria']);
                        $row['is_default'] = $this->isDefault($row['id_seopage']);
                        $row['link'] = $this->url('build', $row);
                        $row['link_label'] = '/' . $this->url('getRoute', $row);
                        $ret['items'][$k] = $row;
                    }
                }
                break;
            case 'getAvailableItems':
                foreach ($this->db->executeS($this->dataBase('prepareQuery', $params)) as $row) {
                    $row['is_default'] = $this->isDefault($row['id_seopage']);
                    $ret[$row['id_seopage']] = $row + ['link' => $this->url('build', $row)];
                }
                break;
            case 'getDuplicatesByCriteria':
                $data = $this->pageData('getListing', ['f' => ['criteria' => $params['criteria']]]);
                foreach ($data['items'] as $item) {
                    if ($item['id_seopage'] != $params['id_seopage']) {
                        $ret[$item['id_seopage']] = ['link' => $item['link']];
                    }
                }
                break;
            case 'prepareDataForSaving':
                $ret = [
                    'id' => $params['id_seopage'],
                    'is_default' => $this->isDefault($params['id_seopage']),
                    'values' => [],
                    'lang_base' => array_fill_keys($this->af()->getAvailableLanguages(true), ''),
                ];
                $required_fields = $this->fields('configurable', $params);
                if ($ret['id']) {
                    foreach ($required_fields as $group => $fields) {
                        $required_fields[$group] = array_intersect_key($fields, $params);
                    }
                    if ($lang_field = current($required_fields['lang'])) {
                        $ret['lang_base'] = array_intersect_key($ret['lang_base'], $lang_field['value']);
                    }
                }
                if (isset($required_fields['main']['criteria'])) {
                    if ($ret['is_default']) {
                        unset($required_fields['main']['criteria']['validate']);
                        $criteria = '';
                    } else {
                        $criteria = $this->criteria('sort', $required_fields['main']['criteria']['value']);
                    }
                    $required_fields['main']['criteria']['value'] = $criteria;
                }
                foreach ($required_fields['lang'] as $key => $field) {
                    $field['value'] = array_intersect_key($field['value'], $ret['lang_base']) + $ret['lang_base'];
                    if ($key == 'header') {
                        foreach ($field['value'] as $id_lang => $header) {
                            if (!$header && !empty($required_fields['main']['criteria']['value'])) {
                                $this->id_lang = $this->af()->id_lang = $id_lang;
                                $header = $this->criteria('getTxt', $required_fields['main']['criteria']['value']);
                                $required_fields['lang']['header']['value'][$id_lang] = $header;
                            }
                            if (!$required_fields['lang']['link_rewrite']['value'][$id_lang]) {
                                $required_fields['lang']['link_rewrite']['value'][$id_lang] = Tools::str2url($header);
                            }
                            if (!$ret['id'] && !$required_fields['lang']['meta_title']['value'][$id_lang]) {
                                $required_fields['lang']['meta_title']['value'][$id_lang] = $header;
                            }
                        }
                        $this->af()->id_lang = $this->context->language->id;
                        $this->id_lang = $this->getIDLang();
                    }
                }
                if (isset($required_fields['lang']['link_rewrite']['value'])) {
                    $link_rewrite_multilang = $required_fields['lang']['link_rewrite']['value'];
                    $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
                    foreach ($link_rewrite_multilang as $id_lang => $value) {
                        if (!$value) {
                            if (!empty($link_rewrite_multilang[$id_lang_default])) {
                                $value = $link_rewrite_multilang[$id_lang_default];
                            } else {
                                $value = uniqid(true);
                            }
                        }
                        $link_rewrite_multilang[$id_lang] = trim(Tools::strtolower($value), '/'); // no trailing slash
                    }
                    $link_rewrite_multilang = $this->uniqueLinks($link_rewrite_multilang, $ret['id']);
                    $required_fields['lang']['link_rewrite']['value'] = $link_rewrite_multilang;
                }
                foreach ($required_fields as $group => $fields) {
                    foreach ($fields as $key => $field) {
                        $value = $field['value']; // passed by reference to af()->validateField
                        if ($error = $this->af()->validateField($value, $field)) {
                            $this->af()->throwError($error);
                        }
                        $ret['values'][$group][$key] = $value;
                    }
                }
                break;
            case 'save':
                $data = $this->pagedata('prepareDataForSaving', $params);
                $ret['saved'] = (bool) array_filter($data['values']);
                if ($data['values']['main']) {
                    $row = ['id_seopage' => $data['id']] + $data['values']['main'];
                    $ret['saved'] &= $this->db->execute('
                        INSERT INTO ' . $this->sqlTable() . ' (' . $this->sqlColumns(array_keys($row)) . ')
                        VALUES (' . $this->sqlStrings($row) . ')
                        ON DUPLICATE KEY UPDATE ' . $this->sqlUpd(array_keys($data['values']['main'])) . '
                    ');
                    if (empty($data['id'])) {
                        $data['id'] = $this->db->Insert_ID();
                    }
                }
                if ($data['values']['lang']) {
                    $rows = [];
                    foreach ($this->shopIDs() as $id_shop) {
                        foreach (array_keys($data['lang_base']) as $id_lang) {
                            $row = [
                                'id_seopage' => (int) $data['id'],
                                'id_lang' => (int) $id_lang,
                                'id_shop' => (int) $id_shop,
                            ];
                            foreach ($data['values']['lang'] as $key => $multilang_value) {
                                $allow_html = in_array($key, ['description', 'description_lower']);
                                $row[$key] = '\'' . pSQL($multilang_value[$id_lang], $allow_html) . '\'';
                            }
                            $rows[] = '(' . implode(', ', $row) . ')';
                        }
                    }
                    $upd_columns = array_keys($data['values']['lang']);
                    $columns = array_merge(['id_seopage', 'id_lang', 'id_shop'], $upd_columns);
                    $ret['saved'] &= $this->db->execute('
                        INSERT INTO ' . $this->sqlTable('_lang') . '(' . $this->sqlColumns($columns) . ')
                        VALUES ' . implode(', ', $rows) . '
                        ON DUPLICATE KEY UPDATE ' . $this->sqlUpd($upd_columns) . '
                    ');
                }
                if ($data['is_default']) {
                    unset($this->routes); // reset
                }
                $ret = $ret['saved'] ? $data['id'] : 0;
                break;
            case 'setStatus':
                $ret = $this->db->execute('
                    UPDATE ' . $this->sqlTable() . '
                    SET active = ' . (int) $params['active'] . '
                    WHERE id_seopage = ' . (int) $params['id_seopage'] . '
                ');
                break;
            case 'delete':
                if ($ret = !$this->isDefault($params['id_seopage'])) {
                    // first delete page data for shops in current context
                    $ret = $this->db->execute('
                        DELETE FROM ' . $this->sqlTable('_lang') . '
                        WHERE id_seopage = ' . (int) $params['id_seopage'] . '
                        AND id_shop IN (' . $this->shopIDs('context', true) . ')
                    ');
                    if (!$this->db->getValue('
                            SELECT id_seopage FROM ' . $this->sqlTable('_lang') . '
                            WHERE id_seopage = ' . (int) $params['id_seopage'] . '
                        ')) {
                        // if page data is not available in any other shop, it can be completely deleted
                        $ret &= $this->db->execute('
                            DELETE FROM ' . $this->sqlTable() . '
                            WHERE id_seopage = ' . (int) $params['id_seopage'] . '
                        ');
                    }
                }
                break;
            case 'addDefault':
                $data = ['id_seopage' => $this->default_id, 'criteria' => [], 'active' => 1];
                foreach ($this->af()->getAvailableLanguages(true) as $id_lang) {
                    $data['meta_title'][$id_lang] = $data['header'][$id_lang] = $this->l('Catalog');
                    $data['link_rewrite'][$id_lang] = Tools::str2url($data['header'][$id_lang]);
                }
                $ret = $this->pageData('save', $data);
                break;
        }

        return $ret;
    }

    public function getStandardPageDuplicates($criteria)
    {
        $criteria_controllers = [
            'c' => 'getCategoryLink',
            'm' => 'getManufacturerLink',
            's' => 'getSupplierLink',
        ];
        foreach (array_keys($this->af()->getSpecialFilters()) as $special_key) {
            $criteria_controllers[$special_key] = 'getPageLink';
        }

        $criteria = explode('-', $criteria);
        $id_root_cat = $this->context->shop->getCategory();
        if ($this->is_modern) {
            $criteria = array_merge(['c:' . $id_root_cat], $criteria);
        }

        $duplicates = [];
        foreach ($criteria as $cr) {
            $cr_expl = explode(':', $cr);
            $group = $cr_expl[0];
            $id = $cr_expl[1];
            if (isset($criteria_controllers[$group])) {
                $method = $criteria_controllers[$group];
                $argument = $method == 'getPageLink' ? $this->af()->getPageName($group) : $id;
                $duplicates[$cr] = [
                    'link' => $this->context->link->$method($argument),
                    'filters' => $this->criteria('prepareForDisplay', array_diff($criteria, [$cr])),
                ];
                if ($group == 'c') {
                    foreach ($this->af()->getAllParents($id) as $id_parent) {
                        if (!$this->is_modern && $id_parent == $id_root_cat) {
                            continue;
                        }
                        if (!isset($duplicates['c:' . $id_parent])) {
                            $duplicates['c:' . $id_parent] = [
                                'link' => $this->context->link->$method($id_parent),
                                'filters' => $this->criteria('prepareForDisplay', $criteria),
                            ];
                        }
                    }
                }
            }
        }

        return $duplicates;
    }

    public function criteria($action, $params = [])
    {
        $ret = [];
        switch ($action) {
            case 'getTxt':
                foreach (explode('-', $this->criteria('sort', $params)) as $key) {
                    if ($found = $this->criteria('findByID', ['q' => $key])) {
                        $ret[$key] = $found;
                    }
                }
                $ret = implode(' ', array_column($ret, 'name'));
                break;
            case 'prepareForDisplay':
                foreach (explode('-', $this->criteria('sort', $params)) as $key) {
                    if ($found = $this->criteria('findByID', ['q' => $key, 'lang_ext' => 1])) {
                        $ret[$key] = $found;
                    }
                }
                break;
            case 'sort':
                $ret = !is_array($params) ? explode('-', $params) : $params;
                natsort($ret);
                $ret = implode('-', $ret);
                break;
            case 'getByParams':
                $applied_criteria = isset($params['filters']) ? $params['filters'] : [];
                if (!empty($params['in_stock'])) { // in_stock may not be included in $params['filters']
                    $applied_criteria['in_stock'][0] = [1 => 1];
                }
                if (!empty($params['current_controller'])) {
                    $controller_keys = ['category' => 'c', 'manufacturer' => 'm', 'supplier' => 's'];
                    if (isset($controller_keys[$params['current_controller']])) {
                        $key = $controller_keys[$params['current_controller']];
                        $identifier = 'id_' . $params['current_controller'];
                        if (!empty($params[$identifier])) {
                            $controller_id = $params[$identifier];
                        } else {
                            $controller_id = Tools::getValue($identifier);
                        }
                        if ($key == 'c') {
                            if ($controller_id == $this->context->shop->getCategory()) {
                                $this->is_home_category = true;
                                $key = false; // no need to include top level category in $applied_criteria
                            } elseif (isset($applied_criteria['c'])) {
                                foreach (array_keys($applied_criteria['c']) as $id_parent_cat) {
                                    if ($id_parent_cat == $controller_id
                                        || $this->isSubCategory($id_parent_cat, $controller_id)) {
                                        // no need to include current category in $applied_criteria
                                        // if one of its subcategories is already there
                                        $key = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($key && $controller_id) {
                            $applied_criteria[$key][0] = [$controller_id => $controller_id];
                        }
                    } else {
                        $special_filters = $this->af()->getSpecialFilters();
                        if (isset($special_filters[$params['current_controller']])) {
                            $applied_criteria[$params['current_controller']][0] = [1 => 1];
                        }
                    }
                }
                foreach ($applied_criteria as $key => $grouped_values) {
                    if (!isset($params['ranges'][$key])) { // p, w have different format + not supported as criteria
                        foreach ($grouped_values as $id_group => $values) {
                            $k = $key . ($id_group && $key != 'c' ? $id_group : '');
                            foreach ($values as $id) {
                                $ret[] = $k . ':' . $id;
                            }
                        }
                    }
                }
                $ret = $this->criteria('sort', $ret);
                break;
            case 'findByID':
                $cache_key = implode('_', $params) . '_' . $this->id_lang;
                if (!isset($this->x['cr_txt'][$cache_key])) {
                    $this->x['cr_txt'][$cache_key] = [];
                    $params['by_id'] = 1;
                    $found = current($this->criteria('find', $params));
                    if ($found && !empty($found['values'])) {
                        $this->x['cr_txt'][$cache_key] = current($found['values']) + ['info' => $found['group_info']];
                    }
                }
                $ret = $this->x['cr_txt'][$cache_key];
                break;
            case 'find':
                $query = explode(':', $params['q']);
                $by_id = !empty($params['by_id']);
                $searched_label = '';
                $searched_value = trim($query[0]);
                if (isset($query[1])) {
                    $searched_label = $searched_value;
                    $searched_value = trim($query[1]);
                }
                $special_filters = $this->af()->getSpecialFilters();
                foreach ($this->af()->getAvailableFilters(false) as $group_identifier => $group) {
                    if (!$searched_label || $group_identifier == $searched_label
                        || (!$by_id && $this->containsSubstring($group['name'], $searched_label))) {
                        $r = [
                            'key' => $group_identifier[0],
                            'group_name' => $group['name'],
                            'group_prefix' => $group['prefix'],
                            'group_info' => $group['name'],
                            'values' => [],
                            'before_cut' => 10,
                        ];
                        if ($r['special'] = isset($special_filters[$group_identifier])) {
                            $r['key'] = 'special';
                            $r['group_info'] = '';
                        } elseif (in_array($r['key'], ['a', 'f'])) {
                            $r['group_info'] .= ' (' . $group['prefix'] . ')';
                        }
                        $ret[$group_identifier] = $r;
                    }
                }
                foreach ($ret as $group_identifier => $r) {
                    $values = [];
                    switch ($r['key']) {
                        case 'c':
                            $values = $this->af()->getSubcategories($this->id_lang);
                            break;
                        case 'a':
                        case 'f':
                            $id_group = Tools::substr($group_identifier, 1);
                            $get_method = $r['key'] == 'a' ? 'getAttributes' : 'getFeatures';
                            $values = $this->af()->$get_method($this->id_lang, $id_group);
                            break;
                        case 'm':
                        case 's':
                        case 'q':
                            $values = $this->af()->getRawFilterValues(['first_char' => $r['key']]);
                            break;
                        case 't':
                            $values = $this->db->executeS('
                                SELECT t.id_tag AS id, t.name' .
                                (!empty($params['lang_ext']) ? ', l.iso_code AS lang_ext' : '') . '
                                FROM ' . _DB_PREFIX_ . 'tag t LEFT JOIN ' . _DB_PREFIX_ . 'lang l
                                ON l.id_lang = t.id_lang
                            ');
                            break;
                        case 'special':
                            $values[] = ['id' => 1, 'name' => $r['group_name']];
                            break;
                    }
                    foreach ($values as $v) {
                        if ($v['id'] == $searched_value || (!$by_id && (!$searched_value
                            || $this->containsSubstring($v['name'], $searched_value)))) {
                            if (isset($v['lang_ext'])) {
                                $v['name'] .= ' (' . $v['lang_ext'] . ')';
                            }
                            $ret[$group_identifier]['values'][$group_identifier . ':' . $v['id']] = [
                                'id' => $v['id'],
                                'name' => $v['name'],
                            ];
                            if ($by_id) {
                                break;
                            }
                        }
                    }
                    if ($searched_value && !$ret[$group_identifier]['values']) {
                        unset($ret[$group_identifier]);
                    }
                }
                break;
        }

        return $ret;
    }

    public function processCanonical($params, $controller)
    {
        $time = microtime(true);
        $criteria = $this->criteria('getByParams', $params + ['current_controller' => $controller]);
        if ($criteria || !empty($this->is_home_category) || isset($this->context->controller->seopage_data)) {
            if (isset($this->context->controller->seopage_data)
                && $this->context->controller->seopage_data['criteria'] == $criteria) {
                $seopage_data = $this->context->controller->seopage_data;
            } else {
                $filters = ['criteria' => $criteria, 'active' => 1];
                $seopage_data = $this->pageData('get', ['f' => $filters, 'multilang' => 1]);
            }
            if ($seopage_data) {
                $this->defineSettings();
                $url_details = $this->url('getDetails');
                $canonical = $seopage_data['canonical'];
                if ($url_details['canonical_params']) {
                    $canonical .= '?' . $url_details['canonical_params'];
                }
                if ($url_details['main'] != $seopage_data['canonical']) { // page and order are not compared
                    $actions = [0 => 'ignored', 1 => 'redirect', 2 => 'updated canonical'];
                    if (isset($actions[$this->settings['canonical']])) {
                        $time_to_detect = round(microtime(true) - $time, 5) . ' s';
                        if ($this->settings['log_canonical']) {
                            $data = $actions[$this->settings['canonical']];
                            $data .= ' | ' . $url_details['full'] . ' ---> ' . $canonical .
                                ' | Time to detect: ' . $time_to_detect;
                            $this->log('add', $data);
                        }
                        switch ($this->settings['canonical']) {
                            case 1:
                                $this->af()->redirect301($canonical);
                                break;
                            case 2:
                                if (!empty($this->context->controller->seopage_data)) {
                                    $this->context->controller->seopage_data = $seopage_data;
                                } else {
                                    $smarty_page_data = $this->getSmartyValue('page');
                                    $smarty_page_data['canonical'] = $canonical;
                                    $this->context->smarty->assign('page', $smarty_page_data);
                                }
                                break;
                        }
                    }
                } elseif (!empty($this->context->controller->seopage_data) && $url_details['canonical_params']) {
                    $this->context->controller->seopage_data['canonical'] = $canonical;
                }
                if (!empty($this->context->controller->seopage_data) && $seopage_data['alternate']) {
                    $this->defineAlternateURLs($seopage_data, $url_details); // used in <head>
                    $this->addRoutesForOtherLanguages($seopage_data); // used in language switcher
                }
            }
        }
    }

    public function displayBreadCrumbs($sp_data)
    {
        if ($this->is_modern) {
            $this->context->smarty->assign(['breadcrumb' => $this->getBreadCrumbVariables($sp_data)]);
            $html = $this->af()->fetchThemeTpl('templates/_partials/breadcrumb.tpl');
        } else {
            $html = $this->retro('displayBreadcrumbs', $sp_data);
        }

        return $html;
    }

    public function getBreadCrumbVariables($sp_data)
    {
        if ($this->is_modern) {
            $bc = $this->getSmartyValue('breadcrumb') ?: $this->context->controller->getBreadcrumb();
        } else {
            $bc = [];
        }
        if (!$this->isDefault($sp_data['id_seopage'])) {
            $filters = ['active' => 1, 'criteria' => ['']]; // empty set for main page
            if (!empty($this->settings['bc_parents'])) {
                $possible_breadcrumb_parents = ['c', 'm'];
                foreach ($possible_breadcrumb_parents as $k) {
                    if (!empty($sp_data['required_filters'][$k]) && count($sp_data['required_filters'][$k]) == 1) {
                        $id_parent = current($sp_data['required_filters'][$k]);
                        if ($k == 'c') {
                            foreach ($this->af()->getAllParents($id_parent) as $id_grand_parent) {
                                $filters['criteria'][] = $k . ':' . $id_grand_parent;
                            }
                        }
                        $filters['criteria'][] = $k . ':' . $id_parent;
                        break;
                    }
                }
            }
            foreach ($this->pageData('getAvailableItems', ['f' => $filters]) as $item) {
                if ($item['id_seopage'] != $sp_data['id_seopage']) {
                    $bc['links'][] = ['title' => $item['header'], 'url' => $item['link']];
                }
            }
        }
        $bc['links'][] = ['title' => $sp_data['header'], 'url' => $sp_data['canonical']];
        $bc['count'] = count($bc['links']);

        return $bc;
    }

    public function defineAlternateURLs($seopage_data, $url_details)
    {
        $alt_urls = $this->context->smarty->tpl_vars['urls']->value['alternative_langs'];
        foreach (array_keys($alt_urls) as $code) {
            $id_lang = Language::getIdByIso(current(explode('-', $code)));
            if (isset($seopage_data['alternate'][$id_lang])) {
                $alt_url = $seopage_data['alternate'][$id_lang]['url'];
                if ($url_details['canonical_params']) {
                    $alt_url .= '?' . $url_details['canonical_params'];
                }
                $this->context->controller->seopage_data['alternate_urls'][$code] = $alt_url;
            }
        }
    }

    public function addRoutesForOtherLanguages($seopage_data)
    {
        $dispatcher = Dispatcher::getInstance();
        foreach ($seopage_data['alternate'] as $id_lang => $alt) {
            $rule = $this->url('getRoute', [
                'id_lang' => $id_lang,
                'link_rewrite' => $alt['link_rewrite'],
                'is_default' => empty($seopage_data['criteria']),
            ]);
            $dispatcher->addRoute($this->fc_identifier, $rule, 'seopage', $id_lang);
        }
    }

    public function getShopName($id_shop)
    {
        return $this->db->getValue('
             SELECT name FROM ' . _DB_PREFIX_ . 'shop WHERE id_shop = ' . (int) $id_shop . '
        ');
    }

    public function log($action, $data = '')
    {
        $ret = '';
        $log_file_path = $this->local_path . 'logs/' . date('m-y');
        switch ($action) {
            case 'add':
                $data = str_replace($this->url('getBase', ['no_lang' => 1]), '/', $data);
                $data = date('m.d.y H:i:s') . ' | ' . $data . "\n";
                $ret = file_put_contents($log_file_path, $data, FILE_APPEND | LOCK_EX);
                break;
            case 'get':
                if (file_exists($log_file_path)) {
                    $ret = implode('', array_reverse(file($log_file_path)));
                }
                break;
        }

        return $ret;
    }

    public function getSmartyValue($name, $default = '')
    {
        $value = $default;
        if (isset($this->context->smarty->tpl_vars[$name])) {
            $value = $this->context->smarty->tpl_vars[$name]->value;
        }

        return $value;
    }

    public function af()
    {
        if (!isset($this->af_obj)) {
            $this->af_obj = Module::getInstanceByName('amazzingfilter');
        }

        return $this->af_obj;
    }

    public function getConfigPaginationVariables($params = [])
    {
        $npp_options = [10, 20, 50, 100];
        $total = $this->pageData('getListing', $params + ['return_total' => true]);
        if (isset($params['npp']) && $params['npp'] == 'all') {
            $params['npp'] = $total;
        }

        return [
            'p' => !empty($params['p']) ? $params['p'] : 1,
            'npp' => !empty($params['npp']) ? $params['npp'] : current($npp_options),
            'npp_options' => $npp_options,
            'total' => $total,
        ];
    }

    public function uniqueLinks($links, $id_seopage = 0)
    {
        $remaining_attempts = 10;
        do {
            if ($links_imploded = $this->sqlStrings(array_unique($links))) {
                $is_last_attempt = --$remaining_attempts < 1;
                $possible_duplicates = $this->db->executeS('
                    SELECT id_lang, link_rewrite FROM ' . $this->sqlTable('_lang') . '
                    WHERE link_rewrite IN (' . $links_imploded . ')' .
                    ($id_seopage ? ' AND id_seopage <> ' . (int) $id_seopage : '') . '
                ');
                foreach ($possible_duplicates as $k => $dup) {
                    if (!isset($links[$dup['id_lang']]) || $links[$dup['id_lang']] != $dup['link_rewrite']) {
                        unset($possible_duplicates[$k]);
                    } elseif (empty($this->bulk_process)) {
                        $this->af()->throwError(sprintf(
                            $this->l('Link %1$s is already used for another page: %2$s'),
                            '"' . $dup['link_rewrite'] . '" [' . Language::getIsoById($dup['id_lang']) . ']',
                            $this->url('build', $dup)
                        ));
                    } else {
                        $link_expl = explode('-', $links[$dup['id_lang']]);
                        $extension = count($link_expl) > 1 ? array_pop($link_expl) : 0;
                        if ($extension && $extension == (int) $extension && ++$extension > 2) {
                            if ($is_last_attempt) {
                                $extension = uniqid(true);
                            }
                            $links[$dup['id_lang']] = implode('-', $link_expl) . '-' . $extension;
                        } else {
                            $links[$dup['id_lang']] .= '-2';
                        }
                    }
                }
            }
        } while ($possible_duplicates && !$is_last_attempt);

        return $links;
    }

    public function renderForm($id_seopage, $is_default, $is_duplicate = false)
    {
        $params = [
            'f' => ['sp.id_seopage' => $id_seopage],
            'lang_ids' => $this->af()->getAvailableLanguages(true),
            'shop_ids' => $this->shopIDs(),
        ];
        $saved_data = $this->db->executeS($this->dataBase('prepareQuery', $params));
        $data = [];
        $lang_fields = $this->fields('lang');
        foreach ($saved_data as $row) {
            foreach ($row as $c_name => $value) {
                if (isset($lang_fields[$c_name])) {
                    if (!isset($data[$c_name][$row['id_lang']])) {
                        $data[$c_name][$row['id_lang']] = $value;
                    }
                } else {
                    $data[$c_name] = $value;
                }
            }
        }
        if ($is_duplicate) {
            $data['id_seopage'] = 0;
            $data['link_rewrite'] = $this->uniqueLinks($data['link_rewrite']);
        }
        $af_tpl_path = $this->af()->local_path . 'views/templates/admin/';
        $fields = $this->fields('all', $data);
        $this->context->smarty->assign([
            'fields' => $this->decorateFields($fields, $is_default),
            'is_default' => $is_default,
            'af_tpl' => [
                'form_group' => $af_tpl_path . 'form-group.tpl',
                'form_footer' => $af_tpl_path . 'footer-save-btn.tpl',
            ],
        ]);
        $this->af()->assignLanguageVariables();

        return $this->display(__FILE__, 'views/templates/admin/sp-item-form.tpl');
    }

    public function decorateFields($fields, $is_default = false)
    {
        if (isset($fields['criteria'])) {
            $fields['criteria']['qs_example'] = $this->getRandomQuckSearchToolTip();
            if ($is_default) {
                $fields['criteria']['class'] = 'hidden';
            }
        }
        if (isset($fields['link_rewrite'])) {
            if ($is_default) {
                $fields['link_rewrite']['display_name'] = $this->l('Main URL');
                $fields['link_rewrite']['class'] = 'default-link-rewrite';
                $url_action = 'getBase';
            } else {
                $url_action = 'build';
            }
            foreach ($this->af()->getAvailableLanguages(true, true) as $id_lang) {
                $link_rewrite_prefix = $this->url($url_action, ['id_lang' => $id_lang]);
                $fields['link_rewrite']['input_prefix'][$id_lang] = rtrim($link_rewrite_prefix, '/') . '/';
                if ($this->x['route']['ext']) {
                    $fields['link_rewrite']['input_suffix'][$id_lang] = '/';
                }
            }
        }

        return $fields;
    }

    public function dataBase($action, $params = [])
    {
        $ret = [];
        switch ($action) {
            case 'prepareQuery':
                $params += ['shop_ids' => [$this->id_shop], 'lang_ids' => [$this->id_lang]];
                $ret = new DbQuery();
                $ret->select('*')->from($this->t_name, 'sp')
                    ->innerJoin($this->t_name . '_lang', 'spl', 'spl.id_seopage = sp.id_seopage')
                    ->where('spl.id_lang IN(' . $this->sqlIDs($params['lang_ids']) . ')')
                    ->where('spl.id_shop IN(' . $this->sqlIDs($params['shop_ids']) . ')');
                if (isset($params['f'])) {
                    foreach ($params['f'] as $key => $value) {
                        $column_ = $this->sqlColumn($key);
                        if (!is_array($value)) {
                            $ret->where($column_ . ' = \'' . pSQL($value) . '\'');
                        } elseif ($imploded_values_ = $this->sqlStrings($value)) {
                            $ret->where($column_ . ' IN (' . $imploded_values_ . ')');
                            if (!isset($params['order'])) {
                                $ret->orderBy('FIELD(' . $column_ . ', ' . $imploded_values_ . ')');
                            }
                        }
                    }
                }
                if (isset($params['s'])) {
                    foreach ($params['s'] as $key => $value) {
                        $column_ = $this->sqlColumn($key);
                        if ($key == 'criteria') {
                            foreach (explode('-', $value) as $cr) {
                                $ret->where($column_ . ' LIKE \'%' . pSQL($cr) . '-%\' OR '
                                    . $column_ . ' LIKE \'%' . pSQL($cr) . '\'');
                            }
                        } else {
                            $ret->where($column_ . ' LIKE \'%' . pSQL($value) . '%\'');
                        }
                    }
                }
                foreach (['order', 'order_2'] as $key) {
                    if (!empty($params[$key]['by'])) {
                        $ret->orderBy($this->sqlOrder($params[$key]));
                    }
                }
                if (count($params['shop_ids']) > 1) {
                    $ret->orderBy('spl.id_shop = ' . (int) $this->id_shop . ' DESC');
                }
                break;
            case 'install':
                $ret[] = 'CREATE TABLE IF NOT EXISTS ' . $this->sqlTable() . ' (
                    `id_seopage` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `criteria` TEXT NOT NULL,
                    `active` TINYINT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id_seopage`),
                    KEY `active` (`active`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
                $ret[] = 'CREATE TABLE IF NOT EXISTS ' . $this->sqlTable('_lang') . ' (
                    `id_seopage` INT(10) UNSIGNED NOT NULL,
                    `id_lang` INT(10) NOT NULL,
                    `id_shop` INT(10) NOT NULL,
                    `' . implode('` TEXT NOT NULL,
                    `', array_map('bqSQL', $this->fields('lang'))) . '` TEXT NOT NULL,
                    PRIMARY KEY (`id_seopage`, `id_lang`, `id_shop`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
                $ret = $this->dataBase('runSql', $ret);
                break;
            case 'uninstall':
                $ret[] = 'DROP TABLE IF EXISTS ' . $this->sqlTable();
                $ret[] = 'DROP TABLE IF EXISTS ' . $this->sqlTable('_lang');
                $ret = $this->dataBase('runSql', $ret);
                break;
            case 'runSql':
                $ret = true;
                foreach ($params as $sql) {
                    if (!$ret &= $this->db->execute($sql)) {
                        break;
                    }
                }
                break;
        }

        return $ret;
    }

    public function fields($type, $saved_data = [])
    {
        $ret = [];
        switch ($type) {
            case 'all':
                $ret = [
                    'id_seopage' => ['type' => 'hidden', 'value' => 0, 'validate' => 'isInt'],
                    'criteria' => [
                        'display_name' => $this->l('Criteria'),
                        'type' => 'tagify',
                        'qs_placeholder' => $this->l('start typing...'),
                        'validate' => 'isRoutePattern',
                    ],
                    'active' => ['type' => 'hidden', 'value' => 1,  'validate' => 'isInt'],
                    'link_rewrite' => [
                        'display_name' => $this->l('Canonical URL'),
                        'validate' => 'isRoutePattern',
                    ],
                    'meta_title' => [
                        'display_name' => $this->l('Meta title'),
                        'validate' => 'isGenericName',
                    ],
                    'meta_description' => [
                        'display_name' => $this->l('Meta description'),
                        'validate' => 'isGenericName',
                    ],
                    'meta_keywords' => [
                        'display_name' => $this->l('Meta keywords'),
                        'validate' => 'isGenericName',
                    ],
                    'header' => [
                        'display_name' => $this->l('Page header H1'),
                        'validate' => 'isGenericName',
                    ],
                    'description' => [
                        'display_name' => $this->l('Description above product list'),
                        'type' => 'textarea',
                        'input_class' => 'mce',
                        'validate' => 'isCleanHtml',
                        'multilang' => 1,
                    ],
                    'description_lower' => [
                        'display_name' => $this->l('Description below product list'),
                        'type' => 'textarea',
                        'input_class' => 'mce',
                        'validate' => 'isCleanHtml',
                        'multilang' => 1,
                    ],
                ];
                foreach ($ret as $key => $r) {
                    if (!isset($r['type'])) {
                        $ret[$key]['type'] = 'text';
                        $ret[$key]['multilang'] = 1;
                    }
                    if (isset($saved_data[$key])) {
                        $ret[$key]['value'] = $saved_data[$key];
                    } elseif (!isset($r['value'])) {
                        $ret[$key]['value'] = !empty($ret[$key]['multilang']) ? [] : '';
                    }
                }
                break;
            case 'lang':
                foreach ($this->fields('all', $saved_data) as $c_name => $field) {
                    if (!empty($field['multilang'])) {
                        $ret[$c_name] = $c_name;
                    }
                }
                break;
            case 'configurable':
                $ret = ['main' => [], 'lang' => []];
                foreach ($this->fields('all', $saved_data) as $c_name => $field) {
                    if ($c_name != 'id_seopage') {
                        $key = empty($field['multilang']) ? 'main' : 'lang';
                        $ret[$key][$c_name] = $field;
                    }
                }
                break;
        }

        return $ret;
    }

    public function url($action, $params = [])
    {
        $ret = '';
        $id_lang = isset($params['id_lang']) ? $params['id_lang'] : $this->id_lang;
        switch ($action) {
            case 'build':
                if (empty($params['relative'])) {
                    $ret .= $this->url('getBase', $params);
                }
                $ret .= $this->url('getRoute', $params);
                if (!empty($params['params_string'])) {
                    $ret .= '?' . $params['params_string'];
                }
                break;
            case 'getRoute':
                if ($this->x['route']['base'] || !empty($params['is_default'])) {
                    if (!isset($this->routes)) {
                        $this->routes = $this->pageData('getMultilangLinks', ['id_seopage' => $this->default_id]);
                    }
                    $ret .= isset($this->routes[$id_lang]) ? $this->routes[$id_lang] : 'catalog';
                }
                if (!empty($params['link_rewrite']) && empty($params['is_default'])) {
                    $ret .= ($ret ? '/' : '') . $params['link_rewrite'];
                }
                if ($ret && $this->x['route']['ext']) {
                    $ret .= '/';
                }
                break;
            case 'getBase':
                if (!isset($this->base_url)) {
                    // $this->base_url = $this->context->shop->getBaseURL(true);
                    $this->base_url = $this->context->link->getBaseLink();
                }
                $ret = $this->base_url;
                if (empty($params['no_lang']) && Language::isMultiLanguageActivated($this->id_shop)) {
                    $ret .= Language::getIsoById($id_lang) . '/';
                }
                break;
            case 'getPossibleLinkRerwite':
                $ret = current(explode('?', $_SERVER['REQUEST_URI']));
                $skip = $this->context->shop->getBaseURI();
                if (empty($params['no_lang']) && Language::isMultiLanguageActivated($this->id_shop)) {
                    $skip .= $this->context->language->iso_code . '/';
                }
                if ($this->x['route']['base']) {
                    $skip .= $this->url('getRoute');
                }
                $ret = strpos($ret, $skip) === 0 ? trim(substr($ret, strlen($skip)), '/') : '';
                break;
            case 'getDetails':
                $url = $this->getSmartyValue('current_url')
                    ?: Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $url_expl = explode('?', $url);
                $ret = [
                    'full' => $url,
                    'main' => $url_expl[0],
                    'canonical_params' => [],
                ];
                if (isset($url_expl[1])) {
                    $params = $this->af()->parseStr($url_expl[1]);
                    foreach (['page', 'order'] as $key) {
                        if (!empty($this->settings[$key . '_canonical']) && isset($params[$key])) {
                            $ret['canonical_params'][$key] = $params[$key];
                        }
                    }
                    if ($ret['canonical_params']) {
                        $ret['canonical_params'] = http_build_query($ret['canonical_params']);
                    }
                }
                break;
        }

        return $ret;
    }

    public function getRandomQuckSearchToolTip()
    {
        $tips = ['color: red', 'size: L', 'category: Women'];
        $key = rand(0, count($tips) - 1);

        return $tips[$key];
    }

    public function isSubCategory($id_category, $id_parent)
    {
        if (!isset($this->x['subcat'][$id_parent])) {
            $cat_obj = new Category($id_parent);
            $this->x['subcat'][$id_parent] = array_column($this->db->executeS('
                SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE active = 1
                AND nright < ' . (int) $cat_obj->nright . ' AND nleft > ' . (int) $cat_obj->nleft . '
            '), 'id_category', 'id_category');
        }

        return isset($this->x['subcat'][$id_parent][$id_category]);
    }

    public function containsSubstring($haystack, $needle)
    {
        return Tools::strpos(Tools::strtolower($haystack), Tools::strtolower($needle)) !== false;
    }

    public function getText($key)
    {
        switch ($key) {
            case 'already_exist': $key = $this->l('%d pages were skipped because they already exist');
                break;
            case 'no_products': $key = $this->l('%d pages were skipped because they are empty');
                break;
            case 'wait': $key = $this->l('Please wait %d seconds');
                break;
            case 'generated': $key = $this->l('%d pages generated');
                break;
            case 'updated': $key = $this->l('%d pages updated');
                break;
            case 'deleted': $key = $this->l('%d pages deleted');
                break;
        }

        return $key;
    }

    public function sqlTable($suffix = '')
    {
        return '`' . _DB_PREFIX_ . bqSQL($this->t_name . $suffix) . '`';
    }

    public function sqlColumn($column_name)
    {
        return strpos($column_name, '.') === false ? '`' . bqSQL($column_name) . '`'
            : '`' . implode('`.`', array_map('bqSQL', explode('.', $column_name))) . '`'; // has alias
    }

    public function sqlColumns($column_names)
    {
        return implode(', ', array_map([$this, 'sqlColumn'], $column_names));
    }

    public function sqlIDs($ids)
    {
        return $this->formatIDs($ids, true);
    }

    public function sqlStrings($values)
    {
        return '\'' . implode('\', \'', array_map('pSQL', $values)) . '\'';
    }

    public function sqlUpd($upd_columns)
    {
        foreach ($upd_columns as $k => $c) {
            $upd_columns[$k] = '`' . bqSQL($c) . '` = VALUES(`' . bqSQL($c) . '`)';
        }

        return implode(', ', $upd_columns);
    }

    public function sqlOrder($order)
    {
        return $this->sqlColumn($order['by']) . ' '
            . (isset($order['way']) && strtoupper($order['way']) == 'DESC' ? 'DESC' : 'ASC');
    }

    public function shopIDs($type = 'context', $implode = false)
    {
        if (!isset($this->x['shop_ids'][$type])) {
            $this->x['shop_ids'][$type] = $type == 'all' ? Shop::getShops(false, null, true)
                : Shop::getContextListShopID();
        }

        return $this->formatIDs($this->x['shop_ids'][$type], $implode);
    }

    public function formatIDs($ids, $return_string = false)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        $ids = array_map('intval', $ids);
        $ids = array_combine($ids, $ids);
        unset($ids[0]);

        return $return_string ? implode(',', $ids) : $ids;
    }

    public function sitemap()
    {
        if (!isset($this->sitemap_obj)) {
            require_once $this->local_path . 'classes/SiteMap.php';
            $this->sitemap_obj = new SiteMap($this);
        }

        return $this->sitemap_obj;
    }

    public function bulkGenerator()
    {
        if (!isset($this->bulkgenerator_obj)) {
            require_once $this->local_path . 'classes/BulkGenerator.php';
            $this->bulkgenerator_obj = new BulkGenerator($this);
        }

        return $this->bulkgenerator_obj;
    }

    public function retro($action, $data = [])
    {
        $ret = '';
        switch ($action) {
            case 'displayBreadcrumbs':
                $this->retro('assignBreadcrumbTplVars', $data);
                $ret = $this->context->smarty->fetch(_PS_THEME_DIR_ . 'breadcrumb.tpl');
                break;
            case 'assignBreadcrumbTplVars':
                $bc = $this->getBreadCrumbVariables($data);
                $this->context->smarty->assign([
                    'af_sp_bc' => [
                        'current_item' => array_pop($bc['links']),
                        'items' => $bc['links'],
                    ],
                ]);
                $this->display(__FILE__, 'views/templates/front/breadcrumb-16.tpl'); // define $smarty.capture.path
                break;
            case 'assignMCEJSVars':
                $iso = $this->context->language->iso_code;
                Media::addJsDef([
                    'ad' => dirname($_SERVER['PHP_SELF']),
                    'iso' => file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en',
                ]);
                break;
        }

        return $ret;
    }
}
