<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
    require_once _PS_MODULE_DIR_ . 'prestawp/classes/PSWPModule16.php';
} else {
    require_once _PS_MODULE_DIR_ . 'prestawp/classes/PSWPModule17.php';
}

require_once _PS_MODULE_DIR_ . 'prestawp/classes/PSWPBlock.php';
require_once _PS_MODULE_DIR_ . 'prestawp/classes/PSWPCache.php';

class PrestaWP extends PSWPModule
{
    private $html;
    protected $test_result;
    protected $shop_name_cache = [];
    protected $errors = [];
    public $settings_prefix = 'PSWP_';

    public $wppath;
    public $securekey;
    public $open_blank;
    public $cache_lifetime;
    public $custom_css;
    public $connection_method;
    public $view_in_ps;
    public $disable_indexation;
    public $ps_show_comments;
    public $ps_allow_commenting;
    public $skip_ssl_check;
    public $img_size;
    public $cache_images;
    public $product_list_wrp;
    public $use_raw_content;
    public $theme;
    public $user_htpasswd;
    public $pass_htpasswd;
    public $alt_the_content;
    public $remove_dup_title;

    public $show_posts;
    public $show_featured_image;
    public $carousel;
    public $carousel_autoplay;
    public $carousel_dots;
    public $carousel_arrows;
    public $show_article_footer;
    public $show_full_posts;
    public $grid_columns;
    public $number_of_posts;
    public $posts_strip_tags;
    public $masonry;
    public $title_color;
    public $title_bg_color;
    public $show_preview;
    public $show_preview_no_img;
    public $posts_wp_categories;
    public $posts_wp_posts;
    public $posts_hide_title;
    public $ajax;
    public $classes;

    public $show_posts_product;
    public $hook_product;
    public $grid_columns_product;
    public $max_posts_product;
    public $show_featured_image_product;
    public $carousel_product;
    public $carousel_autoplay_product;
    public $carousel_dots_product;
    public $carousel_arrows_product;
    public $show_preview_product;
    public $show_preview_no_img_product;
    public $masonry_product;
    public $title_color_product;
    public $title_bg_color_product;
    public $show_article_footer_product;
    public $show_full_posts_product;
    public $posts_strip_tags_product;
    public $ajax_product;

    public $show_shortposts;
    public $show_shortposts_date;
    public $number_of_shortposts;

    public $enable_posts_page;
    public $url_rewrite_page;
    public $grid_columns_page;
    public $show_featured_image_page;
    public $show_preview_page;
    public $show_preview_no_img_page;
    public $masonry_page;
    public $title_color_page;
    public $title_bg_color_page;
    public $show_article_footer_page;
    public $show_full_posts_page;
    public $posts_strip_tags_page;
    public $posts_per_page;
    public $meta_title_page;
    public $meta_desc_page;
    public $meta_keywords_page;
    public $title_page;
    public $desc_page;
    public $rss_limit;
    public $show_alt_langs_url = false;
    public $category_url;
    public $show_search_page;

    public $show_comments;
    public $show_comments_date;
    public $show_comments_content;
    public $number_of_comments;

    public $show_categories;
    public $number_of_categories;
    public $wp_categories;

    public $wp_category_options_cache = [];

    public function __construct()
    {
        $this->name = 'prestawp';
        $this->tab = 'front_office_features';
        $this->version = '1.9.1';
        $this->ps_versions_compliancy = ['min' => '1.5.4.0', 'max' => _PS_VERSION_];
        $this->author = 'PrestaSite';
        $this->bootstrap = true;
        $this->module_key = '333ac9bc03c6dae50cb6ab697111aa28';
        $this->controllers = ['products', 'ajax', 'list', 'post', 'wpcategory'];

        parent::__construct();
        $this->loadSettings();

        $this->displayName = $this->l('PrestaShop-WordPress two-way integration');
        $this->description = $this->l('A module for integration with WordPress: posts, categories, comments, shopping cart etc.');
        $this->confirmUninstall = 'Are you sure? All module data will be PERMANENTLY DELETED.';
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Register hooks
        $this->installHooks();

        // Create tables
        $this->installDB();

        // default values:
        $this->installDefaultSettings();

        // set friendly URL for the controller
        try {
            $pages = [
                'pswp-products' => 'module-' . $this->name . '-products',
                'pswp-main' => 'module-' . $this->name . '-list',
                'pswp-main-item' => 'module-' . $this->name . '-post',
                'pswp-category' => 'module-' . $this->name . '-category',
            ];
            foreach ($pages as $key => $page) {
                $id_meta = Db::getInstance()->getValue(
                    'SELECT `id_meta` FROM ' . _DB_PREFIX_ . 'meta WHERE `page` = "' . pSQL($page) . '"'
                );
                if ($id_meta) {
                    $meta = new Meta($id_meta);
                    $save = false;
                    foreach ($meta->url_rewrite as &$meta_url) {
                        if (!$meta_url) {
                            $meta_url = $key;
                            $save = true;
                        }
                    }
                    if ($save) {
                        $meta->save();
                    }
                }
            }
        } catch (Exception $e) {
            // do nothing
        }

        return true;
    }

    public function installHooks()
    {
        $hooks = [
            'displayHeader',
            'displayBackOfficeHeader',
            'actionAdminControllerSetMedia',
            'displayHome',
            'leftColumn',
            'PSWPposts',
            'PSWPshortposts',
            'PSWPcategories',
            'PSWPcomments',
            'PSWPproduct',
            'moduleRoutes',
            'displayAdminProductsExtra',
            'actionProductSave',
            'displayFooterProduct',
            'actionClearCache',
            'actionClearCompileCache',
            'gSitemapAppendUrls',
        ];

        foreach ($hooks as $hook) {
            $this->registerHook($hook);
        }

        return true;
    }

    protected function installDB()
    {
        $install_queries = $this->getDbTables();
        foreach ($install_queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    protected function getDbTables()
    {
        return [
            'prestawp_block' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block` (
                `id_prestawp_block` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `hook` VARCHAR(255),
                `active` TINYINT(1) DEFAULT 1,
                `number_of_posts` INT(10) UNSIGNED,
                `grid_columns` INT(4) UNSIGNED,
                `show_featured_image` TINYINT(1) DEFAULT 1,
                `carousel` TINYINT(1) DEFAULT 0,
                `carousel_autoplay` TINYINT(1) DEFAULT 0,
                `carousel_dots` TINYINT(1) DEFAULT 0,
                `carousel_arrows` TINYINT(1) DEFAULT 1,
                `show_preview_no_img` TINYINT(1) DEFAULT 1,
                `show_preview` TINYINT(1) DEFAULT 1,
                `masonry` TINYINT(1) DEFAULT 1,
                `title_color` VARCHAR(255),
                `title_bg_color` VARCHAR(255),
                `show_article_footer` TINYINT(1) DEFAULT 0,
                `show_full_posts` TINYINT(1) DEFAULT 0,
                `strip_tags` TINYINT(1) DEFAULT 1,
                `wp_categories` TEXT,
                `wp_posts` TEXT,
                `truncate` INT(6) UNSIGNED,
                `ajax_load` TINYINT(1) DEFAULT 0,
                PRIMARY KEY (`id_prestawp_block`),
                INDEX (`hook`, `active`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'prestawp_block_lang' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_lang` (
                `id_prestawp_block` INT(11) UNSIGNED NOT NULL,
                `id_lang` INT(10) UNSIGNED NOT NULL,
                `title` VARCHAR(255),
                UNIQUE (`id_prestawp_block`, `id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'prestawp_block_shop' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_shop` (
                `id_prestawp_block` INT(11) UNSIGNED NOT NULL,
                `id_shop` INT(10) UNSIGNED NOT NULL,
                UNIQUE (`id_prestawp_block`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'prestawp_block_relation' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_block_relation` (
                `id_prestawp_block` INT(11) UNSIGNED NOT NULL,
                `id_object` INT(11) UNSIGNED NOT NULL,
                `type` VARCHAR(65),
                UNIQUE (`id_prestawp_block`, `id_object`, `type`),
                INDEX (`id_prestawp_block`, `type`),
                INDEX (`id_object`, `type`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'prestawp_product' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_product` (
                `id_product` INT(11) UNSIGNED NOT NULL,
                `id_shop` INT(11) UNSIGNED NOT NULL,
                `wp_categories` TEXT,
                `wp_posts` TEXT,
                UNIQUE (`id_product`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'prestawp_cache' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestawp_cache` (
                `id_prestawp_cache` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `cache_id` VARCHAR(255),
                `filename` VARCHAR(255),
                `datetime` DATETIME,
                PRIMARY KEY (`id_prestawp_cache`),
                UNIQUE (`cache_id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        ];
    }

    protected function installDefaultSettings()
    {
        foreach ($this->getSettings() as $item) {
            if ($item['type'] == 'html') {
                continue;
            }
            $item_name = Tools::strtoupper($item['name']);
            if (isset($item['default']) && (Configuration::get($this->settings_prefix . $item_name) === false)) {
                if (isset($item['lang']) && $item['lang']) {
                    $lang_value = [];
                    $set = false;
                    foreach (Language::getLanguages() as $lang) {
                        $lang_value[$lang['id_lang']] = $item['default'];
                        if (Configuration::get($this->settings_prefix . $item_name, $lang['id_lang']) !== false) {
                            $set = true;
                        }
                    }
                    if (!$set && sizeof($lang_value)) {
                        Configuration::updateValue($this->settings_prefix . $item_name, $lang_value, true);
                    }
                } else {
                    Configuration::updateValue($this->settings_prefix . $item_name, $item['default']);
                }
            }
        }
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        // drop tables
        foreach ($this->getDbTables() as $table_name => $query) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . pSQL($table_name) . '`;');
        }

        // Delete all the module settings, both from configuration and configuration_lang
        $ids_conf = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'configuration`  WHERE `name` LIKE "' . pSQL($this->settings_prefix) . '%"'
        );
        foreach ($ids_conf as $id_conf) {
            $id_conf = $id_conf['id_configuration'];
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'configuration_lang` WHERE `id_configuration` = ' . (int) $id_conf
            );
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `id_configuration` = ' . (int) $id_conf
            );
        }

        return true;
    }

    public function getContent()
    {
        // Register the custom modifier with Smarty, PS8.1
        if (version_compare(_PS_VERSION_, '8.1', '>=')) {
            $smarty = $this->context->smarty;
            if (empty($smarty->registered_plugins['modifier']['implode'])) {
                $smarty->registerPlugin('modifier', 'implode', [$this, 'smartyModifierImplode']);
            }
        }

        $this->html = '';

        if (!$this->active) {
            $this->html .= $this->displayWarning(
                $this->l('The module is deactivated. Please activate it for proper working.')
            );
        }

        if (!$this->checkWpPluginVersion()) {
            $this->html .= $this->renderWpPluginUpdateInfo();
        }

        $this->html .= $this->postProcess();

        if (!Configuration::get($this->settings_prefix . 'HIDE_QUICK_GUIDE')) {
            $this->html .= $this->renderQuickGuide();
        }

        if (!is_writable(_PS_MODULE_DIR_ . $this->name . '/cache/')) {
            $this->html .= $this->displayError(
                $this->l('Cache directory is not writable. Please make it writeable: ')
                . _PS_MODULE_DIR_ . $this->name . '/cache/'
            );
        }

        $tabs = [
            [
                'name' => $this->l('Main settings'),
                'content' => $this->renderSettingsForm(),
            ],
            [
                'name' => $this->l('Posts at the homepage'),
                'content' => $this->renderPostsForm(),
            ],
            [
                'name' => $this->l('Product posts'),
                'content' => $this->renderProductPostsForm(),
            ],
            [
                'name' => $this->l('Posts and the post list'),
                'content' => $this->renderPostsPageForm(),
            ],
            [
                'name' => $this->l('Comments'),
                'content' => $this->renderCommentsForm(),
            ],
            [
                'name' => $this->l('Categories'),
                'content' => $this->renderCategoriesForm(),
            ],
            [
                'name' => $this->l('Custom blocks'),
                'content' => $this->renderCustomBlocksForm(),
            ],
            [
                'name' => $this->l('Additional instructions'),
                'content' => $this->renderInstallationInfo() . $this->renderFeedbackBlock(),
            ],
        ];

        $this->context->smarty->assign([
            'tabs' => $tabs,
            'pswp_tab' => Tools::getValue('pswp_tab'),
        ]);
        $this->html .= $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/tabs.tpl'
        );

        return $this->html;
    }

    protected function postProcess()
    {
        $html = '';
        $errors = [];
        $settings_updated = false;
        $languages = Language::getLanguages(false);

        // Check if this is an ajax call / PS1.5
        if ($this->getPSVersion() < 1.6
            && Tools::getIsset('ajax') && Tools::getValue('ajax') && Tools::getValue('action')) {
            if (is_callable([$this, 'ajaxProcess' . Tools::getValue('action')])) {
                call_user_func([$this, 'ajaxProcess' . Tools::getValue('action')]);
            }
            exit;
        }

        if (Tools::isSubmit('submitModule')) {
            $this->clearWPCache();
            // saving settings:
            $settings = $this->getSettings();
            foreach ($settings as $item) {
                if (empty($item['type']) || $item['type'] == 'html' || $item['type'] == 'wp_content'
                    || (isset($item['lang']) && $item['lang'] == true)) {
                    continue;
                }
                $item['name'] = str_replace('[]', '', $item['name']);
                if (Tools::isSubmit($item['name'])) {
                    $validated = true;
                    $val_method = (isset($item['validate']) ? $item['validate'] : '');
                    if (Tools::strlen(Tools::getValue($item['name']))) {
                        // Validation:
                        if (Tools::strlen($val_method) && is_callable(['Validate', $val_method])) {
                            $validated = call_user_func(['Validate', $val_method], Tools::getValue($item['name']));
                        }
                    } elseif (isset($item['required']) && $item['required']) {
                        $label = trim($item['label'], ':');
                        $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                    }
                    if ($validated) {
                        $value = Tools::getValue($item['name']);
                        if ($val_method == 'isArrayWithIds') {
                            $value = implode(',', Tools::getValue($item['name']));
                        }
                        $allow_html = (isset($item['allow_html']) && $item['allow_html'] == false ? false : true);
                        $this->setOption($item['name'], $value, $allow_html);
                        $settings_updated = true;
                    } else {
                        $label = trim($item['label'], ':');
                        $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                    }
                }
            }

            // update lang fields:
            $languages = Language::getLanguages();
            foreach ($settings as $item) {
                if (!(isset($item['lang']) && $item['lang'])) {
                    continue;
                }
                $is_submit = false;
                $val_method = (isset($item['validate']) ? $item['validate'] : '');
                $lang_value = [];
                foreach ($languages as $lang) {
                    if (Tools::isSubmit($item['name'] . '_' . $lang['id_lang'])) {
                        $is_submit = true;
                        $validated = true;
                        if (Tools::strlen(Tools::getValue($item['name'] . '_' . $lang['id_lang']))) {
                            // Validation:
                            if (Tools::strlen($val_method) && is_callable(['Validate', $val_method])) {
                                $validated =
                                    call_user_func(
                                        ['Validate', $val_method],
                                        Tools::getValue($item['name'] . '_' . $lang['id_lang'])
                                    );
                            }
                        }
                        if ($validated) {
                            $lang_value[$lang['id_lang']] = Tools::getValue($item['name'] . '_' . $lang['id_lang']);
                            $settings_updated = true;
                        } else {
                            $label = trim($item['label'], ':');
                            $this->errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                        }
                    }
                }
                if (sizeof($lang_value)) {
                    $this->setOption($item['name'], $lang_value, true);
                }

                $filtered_lang_value = array_filter($lang_value);
                if ($is_submit && !$filtered_lang_value && isset($item['required']) && $item['required']) {
                    $label = trim($item['label'], ':');
                    $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                }
            }

            // update wp content field:
            foreach ($settings as $item) {
                if ($item['type'] != 'wp_content') {
                    continue;
                }

                $input_names = [$item['wp_posts_name'], $item['wp_categories_name']];
                foreach ($input_names as $input_name) {
                    if (Tools::isSubmit($input_name)) {
                        $validated = true;
                        $val_method = $item['validate'];
                        $value = implode(',', Tools::getValue($input_name));
                        if (Tools::strlen($value)) {
                            // Validation:
                            if (Tools::strlen($val_method) && is_callable(['Validate', $val_method])) {
                                $validated = call_user_func(['Validate', $val_method], $value);
                            }
                        }
                        if ($validated) {
                            $this->setOption($input_name, $value, true);
                            $settings_updated = true;
                        } else {
                            $label = trim($item['label'], ':');
                            $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                        }
                    }
                }
            }
        }

        if (Tools::isSubmit('deleteprestawp_block')) {
            $id_block = Tools::getValue('id_prestawp_block');
            $block = new PSWPBlock($id_block);
            $block->delete();
            $settings_updated = true;
        }

        if (Tools::isSubmit('fix_url')) {
            $fixed = false;
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $url = $this->getWPPath($id_lang_default);

            // http/https
            if (Tools::strpos($url, 'http://') !== false) {
                $new_url = str_replace('http://', 'https://', $url);
                if ($this->tryAnotherWPUrl($new_url, $url)) {
                    $fixed = true;
                }
            } elseif (Tools::strpos($url, 'https://') !== false) {
                $new_url = str_replace('https://', 'http://', $url);
                if ($this->tryAnotherWPUrl($new_url, $url)) {
                    $fixed = true;
                }
            }

            // toggle curl/default
            if (!$fixed) {
                if ($this->connection_method == 'file_get_contents') {
                    $this->setOption('CONNECTION_METHOD', 'curl');
                    if ($this->tryAnotherWPUrl($url, $url)) {
                        $fixed = true;
                    } elseif (!$this->skip_ssl_check) {
                        // try disabling ssl check
                        $this->setOption('SKIP_SSL_CHECK', 1);
                        if ($this->tryAnotherWPUrl($url, $url)) {
                            $fixed = true;
                        } else {
                            $this->setOption('SKIP_SSL_CHECK', 0);
                        }
                    }
                    if (!$fixed) {
                        $this->setOption('CONNECTION_METHOD', 'file_get_contents');
                    }
                } else {
                    if (!$this->skip_ssl_check) {
                        // try disabling ssl check
                        $this->setOption('SKIP_SSL_CHECK', 1);
                        if ($this->tryAnotherWPUrl($url, $url)) {
                            $fixed = true;
                        } else {
                            $this->setOption('SKIP_SSL_CHECK', 0);
                        }
                    }
                    if (!$fixed) {
                        $this->setOption('CONNECTION_METHOD', 'file_get_contents');
                        if ($this->tryAnotherWPUrl($url, $url)) {
                            $fixed = true;
                        }
                    }

                    if (!$fixed) {
                        $this->setOption('CONNECTION_METHOD', 'curl');
                    }
                }
            }

            // www
            if (!$fixed) {
                if (Tools::strpos($url, '://www.') !== false) {
                    $new_url = str_replace('://www.', '://', $url);
                    if ($this->tryAnotherWPUrl($new_url, $url)) {
                        $fixed = true;
                    }
                } elseif (Tools::strpos($url, '://www.') === false) {
                    $new_url = str_replace('://', '://www.', $url);
                    if ($this->tryAnotherWPUrl($new_url, $url)) {
                        $fixed = true;
                    }
                }
            }

            // add blog/wordpress
            if (!$fixed) {
                if (Tools::strpos($url, '/blog') === false) {
                    $new_url = rtrim($url, '/') . '/blog';
                    if ($this->tryAnotherWPUrl($new_url, $url)) {
                        $fixed = true;
                    }
                }
            }

            if ($fixed) {
                $token = Tools::getAdminTokenLite('AdminModules');
                $redirect_url =
                    'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . $token . '&conf=6';

                Tools::redirectAdmin($redirect_url);
            }
        }

        $this->loadSettings();

        // Fix URL
        $lang_value = [];
        foreach ($languages as $lang) {
            $wppath = trim($this->getWPPath($lang['id_lang']));
            if ($wppath && Tools::strpos($wppath, 'http') === false) {
                $fixed_wppath = 'https://' . $wppath;
                if (!$this->checkWPURL($fixed_wppath)) {
                    $fixed_wppath = 'http://' . $wppath;
                }
                $lang_value[$lang['id_lang']] = $fixed_wppath;
                $this->setOption('WPPATH', $lang_value);
                $this->loadSettings();
            }
        }

        $wp_url_ok = false;
        if ($this->getWPPath(Configuration::get('PS_LANG_DEFAULT'))) {
            $wp_url_ok = $this->checkWPURL();
            if (!$wp_url_ok) {
                $this->test_result = false;
                $errors[] = $this->l('Unable to connect to WordPress. Please check the URL and make sure WordPress plugin is installed.')
                    . (Tools::isSubmit('fix_url') ? '' : $this->renderFixURLLink());
            }
        }

        // check for correct WordPress secure key
        if ($wp_url_ok) {
            if ($this->checkSecureKey()) {
                $securekey_ok = true;
            } else {
                $securekey_ok = false;
                $this->clearWPCache();
            }
            $this->test_result = $securekey_ok;
            if (!$securekey_ok) {
                $errors[] = $this->l('Unable to connect to WordPress. Please check the secure key.')
                    . (Tools::isSubmit('fix_url') ? '' : $this->renderFixURLLink());
            }
        }

        if ($this->errors) {
            $errors = array_merge($errors, $this->errors);
        }

        if ($settings_updated && !sizeof($errors)) {
            if (Tools::getValue('submitposts_product_settings')) {
                $this->registerHook($this->hook_product);
            }
            // Clear smarty cache
            $this->clearSmartyCache();

            // tabs
            $active_tab = null;
            $tabs = [
                'submitmain_settings' => 0,
                'submitposts_settings' => 1,
                'submitposts_product_settings' => 2,
                'submitpage_settings' => 3,
                'submitcomments_settings' => 4,
                'submitcategories_settings' => 5,
            ];
            foreach ($tabs as $tab => $key) {
                if (Tools::isSubmit($tab)) {
                    $active_tab = $key;
                    break;
                }
            }

            // redirect
            $token = Tools::getAdminTokenLite('AdminModules');
            $redirect_url = 'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . $token . '&conf=6'
                . ($active_tab !== null ? '&pswp_tab=' . $active_tab : '');

            if (Tools::strlen(Tools::getValue('submitModule')) > 1) {
                $hash = '#' . Tools::getValue('submitModule');
                $redirect_url .= $hash;
            }

            Tools::redirectAdmin($redirect_url);
        } elseif (sizeof($errors)) {
            foreach ($errors as $err) {
                $html .= $this->displayError($err);
            }
        }

        return $html;
    }

    protected function renderSettingsForm()
    {
        // check for correct WordPress url
        $url_desc = '';

        $url_class = '';
        $wp_url_ok = false;
        if ($this->getWPPath(Configuration::get('PS_LANG_DEFAULT'))) {
            $wp_url_ok = $this->checkWPURL();
            $url_desc = $wp_url_ok
                ? ''
                : $this->l('Unable to connect to WordPress. Please check the URL and make sure WordPress plugin is installed.');
            $url_class = $wp_url_ok ? 'input-ok' : 'input-err';
        }

        // check for correct WordPress secure key
        $securekey_desc = '';
        $securekey_class = '';
        if ($wp_url_ok) {
            $securekey_ok = $this->checkSecureKey();
            if (!$securekey_ok) {
                $this->clearWPCache();
            }
            $securekey_desc = $securekey_ok ?
                '' : $this->l('Unable to connect to WordPress. Please check the secure key.');
            $securekey_class = $securekey_ok ? 'input-ok' : 'input-err';
        }

        $settings = $this->getMainSettings($url_desc, $url_class, $securekey_desc, $securekey_class, true);

        // load image sizes
        $sizes = [
            [
                'id_option' => '',
                'name' => $this->l('Full'),
            ],
        ];
        $wp_sizes = $this->getWPData('image_sizes');
        if (is_array($wp_sizes) && $wp_sizes) {
            foreach ($wp_sizes as $key => $wp_size) {
                $sizes[] = [
                    'id_option' => $key,
                    'name' => $key . ' (' . $wp_size['width'] . 'x' . $wp_size['height'] . ')',
                ];
            }
        }
        $settings['img_size']['options']['query'] = $sizes;

        // Main settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Main settings'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-main-settings',
                    ],
                    'input' => $settings,
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'main_settings');
        $this->loadSettingsValues($this->getMainSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderPostsForm()
    {
        // Posts settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Posts'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-posts-settings',
                    ],
                    'input' => $this->getPostsSettings(true),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'posts_settings');
        $this->loadSettingsValues($this->getPostsSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderPostsPageForm()
    {
        // Posts settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Posts and the post list'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-page-settings',
                    ],
                    'input' => $this->getPageSettings(true),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'page_settings');
        $this->loadSettingsValues($this->getPageSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderProductPostsForm()
    {
        // Posts settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Product posts'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-pposts-settings',
                    ],
                    'input' => $this->getProductPostsSettings(true),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'posts_product_settings');
        $this->loadSettingsValues($this->getProductPostsSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderCommentsForm()
    {
        // Comments settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Comments'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-comments-settings',
                    ],
                    'input' => $this->getCommentsSettings(),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'comments_settings');
        $this->loadSettingsValues($this->getCommentsSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderCategoriesForm()
    {
        // Categories settings
        $form_settings = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Categories'),
                        'icon' => 'icon-cogs',
                        'href' => 'pswp-categories-settings',
                    ],
                    'input' => $this->getCategoriesSettings(true),
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'submitModule',
                    ],
                ],
            ],
        ];

        $helper = $this->createFormHelper($form_settings, 'categories_settings');
        $this->loadSettingsValues($this->getCategoriesSettings(), $helper);

        return $helper->generateForm($form_settings);
    }

    public function renderCustomBlocksForm()
    {
        $title = $this->l('New block');

        $this->context->smarty->assign([
            'preTable' => $this->renderNewBlockButton() .
                $this->renderBlockForm($title, $this->getBlockFormFields(), 'prestawp_block', null),
            'psv' => $this->getPSVersion(),
            'form_id' => 'prestawp_block',
        ]);

        return $this->renderBlocksList();
    }

    protected function renderBlockForm($title, $settings, $form_name, $block_obj)
    {
        $field_forms = [
            [
                'form' => [
                    'legend' => [
                        'title' => $title,
                        'icon' => 'icon-cogs',
                    ],
                    'input' => $settings,
                    'submit' => [
                        'title' => $this->l('Save'),
                        'name' => 'pswp_block_submit',
                    ],
                ],
            ],
        ];

        if (Validate::isLoadedObject($block_obj)) {
            $form_name = $form_name . '-' . $block_obj->id;
        }
        $helper = $this->createFormHelper($field_forms, $form_name, $block_obj);

        foreach ($settings as $option) {
            if (isset($option['default']) && $helper->tpl_vars['fields_value'][$option['name']] === false) {
                $helper->tpl_vars['fields_value'][$option['name']] = $option['default'];
            }
        }

        return $helper->generateForm($field_forms);
    }

    public function hookPSWPposts($params)
    {
        if (!$this->getWPPath()) {
            return '';
        }

        // get params
        $custom_params = [];
        $custom_params['post_ids'] = (isset($params['ids']) ? $params['ids'] : '');
        $custom_params['category_ids'] = (isset($params['category_ids']) ? $params['category_ids'] : '');
        $custom_params['limit'] = (isset($params['limit']) ? $params['limit'] : '');
        $custom_params['columns'] = (isset($params['columns']) ? $params['columns'] : '');
        $custom_params_filtered = array_filter($custom_params);

        // check if it's a custom hook with params
        $is_custom_hook = false;
        $custom_key = $this->name . '|pswp_posts';
        if ($custom_params_filtered) {
            $custom_key = md5(implode('-', $custom_params_filtered));
            $is_custom_hook = true;
        }

        $tpl_file = ($this->getPSVersion() >= 1.7
            ? 'module:' . $this->name . '/views/templates/hook/posts.tpl'
            : 'posts.tpl');
        if (!empty($params['posts_only'])) {
            $tpl_file = ($this->getPSVersion() >= 1.7
                ? 'module:' . $this->name . '/views/templates/hook/_posts_list.tpl'
                : '_posts_list.tpl');
        }

        $cache_id = ($this->cache_lifetime > 0 ? $this->getCacheId($custom_key) : null);
        if (!$this->isCached($tpl_file, $cache_id)
            || (!$is_custom_hook && $this->checkCacheExpired('POSTS'))
        ) {
            $wp_params = [];
            // Check if there are any selected WP categories or posts
            if (!$is_custom_hook) {
                $number_of_posts = $this->number_of_posts;
                $wp_params = [];
                foreach (['posts' => 'WP_POSTS_POSTS', 'categories' => 'WP_POSTS_CATEGORIES'] as $type => $option) {
                    $option_value = Configuration::get($this->settings_prefix . $option);
                    if ($option_value) {
                        $option_value = explode(',', $option_value);
                        $wp_params[$type] = $option_value;
                    }
                }
            } else {
                $number_of_posts = ($custom_params['limit'] !== '' ? $custom_params['limit'] : $this->number_of_posts);
                if ($custom_params['post_ids']) {
                    $post_ids = explode(',', $custom_params['post_ids']);
                    $wp_params['posts'] = array_map('intval', $post_ids);
                }
                if ($custom_params['category_ids']) {
                    $category_ids = explode(',', $custom_params['category_ids']);
                    $wp_params['categories'] = array_map('intval', $category_ids);
                }
            }

            // Get posts from WP
            if (!$this->ajax) {
                $posts = $this->getWPData('posts', $number_of_posts, $wp_params);
                if (!$posts) {
                    return '';
                }
            } else {
                $posts = [];
            }

            $list_tpl_file = $this->name . '/views/templates/hook/_posts_list.tpl';

            // Assign smarty data
            $this->context->smarty->assign([
                'posts' => $posts,
                'wp_path' => $this->getWPPath(),
                'show_footer' => $this->show_article_footer,
                'show_full' => $this->show_full_posts,
                'show_featured_images' => $this->show_featured_image,
                'pswp_masonry' => $this->masonry,
                'pswp_title_color' => $this->title_color,
                'pswp_title_bg_color' => $this->title_bg_color,
                'pswp_show_preview_no_img' => $this->show_preview_no_img,
                'pswp_blank' => $this->open_blank,
                'grid_columns' => ($custom_params['columns'] ? $custom_params['columns'] : $this->grid_columns),
                'strip_tags' => $this->posts_strip_tags,
                'psv' => $this->getPSVersion(),
                'psvwd' => $this->getPSVersion(true),
                'pswp_hide_readall' => $is_custom_hook,
                'pswp_show_preview' => $this->show_preview,
                'pswp_enable_posts_page' => $this->enable_posts_page,
                'pswp_posts_page_url' => $this->getModuleLink($this->name, 'list'),
                'pswp_carousel' => $this->carousel,
                'pswp_carousel_autoplay' => $this->carousel_autoplay,
                'pswp_carousel_dots' => $this->carousel_dots,
                'pswp_carousel_arrows' => $this->carousel_arrows,
                'pswp_theme' => $this->getCurrentThemeName(),
                'pswp_hide_title' => $this->posts_hide_title,
                'pswp_ajax_load' => $this->ajax,
                'pswp_list_tpl_file' => (file_exists(_PS_THEME_DIR_ . 'modules/' . $list_tpl_file)
                    ? _PS_THEME_DIR_ . 'modules/' . $list_tpl_file : _PS_MODULE_DIR_ . $list_tpl_file),
                'pswp_block_type' => 'main',
                'pswp_wrp_class' => $this->classes,
            ]);

            Configuration::updateGlobalValue($this->settings_prefix . 'CACHE_UPDATED_POSTS', time());
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function hookPSWPproduct($params)
    {
        $hook = (isset($params['hook']) ? $params['hook'] : '');
        if (!$this->show_posts_product || $hook != $this->hook_product || !$this->getWPPath()) {
            return '';
        }

        $id_product = $this->getParamsIdProduct($params);
        if (!$id_product) {
            return '';
        }

        // get params
        $custom_params = [];
        $custom_params['limit'] = (isset($params['limit']) ? $params['limit'] : $this->max_posts_product);
        $custom_params['columns'] = (isset($params['columns']) ? $params['columns'] : '');
        $custom_params_filtered = array_filter($custom_params);

        // check if it's a custom hook with params
        $custom_key = 'pswp_product';
        if ($custom_params_filtered) {
            $custom_key = md5(implode('-', $custom_params_filtered));
        }

        $custom_key .= '-' . $id_product;

        $tpl_file = ($this->getPSVersion() >= 1.7
            ? 'module:' . $this->name . '/views/templates/hook/posts.tpl'
            : 'posts.tpl');
        if (!empty($params['posts_only'])) {
            $tpl_file = ($this->getPSVersion() >= 1.7
                ? 'module:' . $this->name . '/views/templates/hook/_posts_list.tpl'
                : '_posts_list.tpl');
        }

        $cache_id = ($this->cache_lifetime > 0 ? $this->getCacheId($custom_key) : null);
        if (!$this->isCached($tpl_file, $cache_id)) {
            // Check if there are any selected WP categories or posts
            $number_of_posts = ($this->max_posts_product ? $this->max_posts_product : 1000);
            $wp_params = [];
            $wp_params['posts'] = $this->getProductWPData($id_product, 'wp_posts');
            $wp_params['categories'] = $this->getProductWPData($id_product, 'wp_categories');

            // check if any posts associated with this product
            if (!$wp_params['posts'] && !$wp_params['categories']) {
                return '';
            }

            // Get posts from WP
            if (!$this->ajax_product) {
                $posts = $this->getWPData('posts', $number_of_posts, $wp_params);
                if (!$posts) {
                    return '';
                }
            } else {
                $posts = [];
            }

            $list_tpl_file = $this->name . '/views/templates/hook/_posts_list.tpl';

            // Assign smarty data
            $this->context->smarty->assign([
                'pswp_title' => $this->l('More about this product:'),
                'posts' => $posts,
                'wp_path' => $this->getWPPath(),
                'show_footer' => $this->show_article_footer_product,
                'show_full' => $this->show_full_posts_product,
                'show_featured_images' => $this->show_featured_image_product,
                'pswp_masonry' => $this->masonry_product,
                'pswp_title_color' => $this->title_color_product,
                'pswp_title_bg_color' => $this->title_bg_color_product,
                'pswp_show_preview_no_img' => $this->show_preview_no_img_product,
                'pswp_blank' => $this->open_blank,
                'grid_columns' => ($custom_params['columns'] ? $custom_params['columns'] : $this->grid_columns_product),
                'strip_tags' => $this->posts_strip_tags_product,
                'psv' => $this->getPSVersion(),
                'psvwd' => $this->getPSVersion(true),
                'pswp_hide_readall' => true,
                'pswp_show_preview' => $this->show_preview_product,
                'pswp_carousel' => $this->carousel_product,
                'pswp_carousel_autoplay' => $this->carousel_autoplay_product,
                'pswp_carousel_dots' => $this->carousel_dots_product,
                'pswp_carousel_arrows' => $this->carousel_arrows_product,
                'pswp_ajax_load' => $this->ajax_product,
                'pswp_list_tpl_file' => (file_exists(_PS_THEME_DIR_ . 'modules/' . $list_tpl_file)
                    ? _PS_THEME_DIR_ . 'modules/' . $list_tpl_file : _PS_MODULE_DIR_ . $list_tpl_file),
                'pswp_block_type' => 'product',
                'pswp_theme' => $this->getCurrentThemeName(),
            ]);

            Configuration::updateGlobalValue($this->settings_prefix . 'CACHE_UPDATED_POSTS_PRODUCT', time());
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function hookPSWPshortposts($params)
    {
        if (!$this->getWPPath()) {
            return '';
        }

        if ($this->getPSVersion() >= 1.7) {
            $tpl_file = 'module:' . $this->name . '/views/templates/hook/shortposts.tpl';
        } else {
            $tpl_file = 'shortposts.tpl';
        }

        $cache_id = ($this->cache_lifetime > 0 ? $this->getCacheId() : null);
        if (!$this->isCached($tpl_file, $cache_id) || $this->checkCacheExpired('POSTS')) {
            $posts = $this->getWPData('posts', $this->number_of_shortposts);
            if (!$posts) {
                return '';
            }

            $this->context->smarty->assign([
                'posts' => $posts,
                'wp_path' => $this->getWPPath(),
                'show_date' => $this->show_shortposts_date,
                'blank' => $this->open_blank,
                'psv' => $this->getPSVersion(),
                'psvwd' => $this->getPSVersion(true),
            ]);

            Configuration::updateGlobalValue($this->settings_prefix . 'CACHE_UPDATED_POSTS', time());
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function hookPSWPcategories($params)
    {
        if (!$this->show_categories || !$this->getWPPath()) {
            return '';
        }

        if ($this->getPSVersion() >= 1.7) {
            $tpl_file = 'module:' . $this->name . '/views/templates/hook/categories.tpl';
        } else {
            $tpl_file = 'categories.tpl';
        }

        $cache_id = ($this->cache_lifetime > 0 ? $this->getCacheId() : null);
        if (!$this->isCached($tpl_file, $cache_id) || $this->checkCacheExpired('CATEGORIES')) {
            $categories = $this->getWPData('categories', $this->number_of_categories);
            if (!$categories) {
                return '';
            }

            $selected_categories = array_filter(explode(',', $this->wp_categories));
            if ($selected_categories) {
                foreach ($categories as $key => &$category) {
                    if (isset($category['cat_ID']) & !in_array($category['cat_ID'], $selected_categories)) {
                        unset($categories[$key]);
                    }
                }
            }

            if ($this->enable_posts_page) {
                foreach ($categories as &$category) {
                    $category['url'] = $this->getModuleLink(
                        $this->name,
                        'wpcategory',
                        ['category_name' => $category['slug']],
                        null,
                        $this->context->language->id
                    );
                }
            }

            $this->context->smarty->assign([
                'pswp_categories' => $categories,
                'pswp_blank' => $this->open_blank,
                'pswp_psv' => $this->getPSVersion(),
                'pswp_psvwd' => $this->getPSVersion(true),
            ]);

            Configuration::updateGlobalValue($this->settings_prefix . 'CACHE_UPDATED_CATEGORIES', time());
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function hookPSWPcomments($params)
    {
        if (!$this->show_comments || !$this->getWPPath()) {
            return '';
        }

        if ($this->getPSVersion() >= 1.7) {
            $tpl_file = 'module:' . $this->name . '/views/templates/hook/comments.tpl';
        } else {
            $tpl_file = 'comments.tpl';
        }

        $cache_id = ($this->cache_lifetime > 0 ? $this->getCacheId() : null);
        if (!$this->isCached($tpl_file, $cache_id) || $this->checkCacheExpired('COMMENTS')) {
            $comments = $this->getWPData('comments', $this->number_of_comments);

            if (is_array($comments)) {
                foreach ($comments as $key => &$comment) {
                    if (!(isset($comment['comment_approved']) && $comment['comment_approved'])) {
                        unset($comments[$key]);
                    } else {
                        if ($this->view_in_ps && $this->show_comments) {
                            if (!empty($comment['post_slug'])) {
                                $comment['url'] =
                                    $this->getModuleLink($this->name, 'post', ['post_name' => $comment['post_slug']]) .
                                    '#comment-' . $comment['comment_ID'];
                            }
                        }
                    }
                }
            }

            if (!$comments) {
                return '';
            }

            $this->context->smarty->assign([
                'pswp_comments' => $comments,
                'pswp_show_date' => $this->show_comments_date,
                'pswp_show_content' => $this->show_comments_content,
                'pswp_blank' => $this->open_blank,
                'pswp_psv' => $this->getPSVersion(),
                'pswp_psvwd' => $this->getPSVersion(true),
            ]);

            Configuration::updateGlobalValue($this->settings_prefix . 'CACHE_UPDATED_COMMENTS', time());
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function hookDisplayHome($params)
    {
        $html = '';

        if ($this->show_posts) {
            $html .= $this->hookPSWPposts($params);
        }
        $html .= $this->renderWidget('displayHome', $params);

        return $html;
    }

    public function hookLeftColumn($params)
    {
        return $this->renderWidget('displayLeftColumn', $params)
            . $this->hookPSWPcategories($params)
            . $this->hookPSWPcomments($params);
    }

    public function hookRightColumn($params)
    {
        return $this->renderWidget('displayRightColumn', $params);
    }

    public function hookDisplayFooterProduct($params)
    {
        $params['hook'] = 'displayFooterProduct';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayFooterProduct', $params);
    }

    public function hookDisplayProductDeliveryTime($params)
    {
        $params['hook'] = 'displayProductDeliveryTime';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayProductDeliveryTime', $params);
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        $params['hook'] = 'displayProductAdditionalInfo';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayProductAdditionalInfo', $params);
    }

    public function hookDisplayProductButtons($params)
    {
        $params['hook'] = 'displayProductButtons';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayProductButtons', $params);
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        $params['hook'] = 'displayLeftColumnProduct';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayLeftColumnProduct', $params);
    }

    public function hookDisplayRightColumnProduct($params)
    {
        $params['hook'] = 'displayRightColumnProduct';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayRightColumnProduct', $params);
    }

    public function hookDisplayReassurance($params)
    {
        $params['hook'] = 'displayReassurance';

        return $this->hookPSWPproduct($params) . $this->renderWidget('displayReassurance', $params);
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/prestawp.css', 'all');
        if ($this->isAnyMasonry()) {
            $this->context->controller->addJS($this->_path . 'views/js/masonry.pkgd.min.js');
        }
        $this->context->controller->addJS($this->_path . 'views/js/front.js');

        if ($this->isAnySliderActive()) {
            $this->context->controller->addCSS($this->_path . 'views/css/slick.css', 'all');
            $this->context->controller->addCSS($this->_path . 'views/css/slick-theme.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/slick.min.js');
        }

        // Register theme CSS
        if ($this->theme) {
            $this->context->controller->addCSS(
                $this->_path . 'views/css/themes/' . $this->theme
            );
        }

        $rss_link = '';
        if (Tools::strpos(get_class($this->context->controller), 'PrestaWP') !== false) {
            $rss_link = $this->context->link->getModuleLink($this->name, 'rss');
        }
        $rss_page_title = $this->getLangOptionValue('title_page');
        $rss_page_title = ($rss_page_title ? $rss_page_title : $this->l('Posts'));

        $this->context->smarty->assign([
            'custom_css' => html_entity_decode($this->custom_css),
            'grid_columns' => $this->grid_columns,
            'psv' => $this->getPSVersion(),
            'pswp_psv' => $this->getPSVersion(),
            'pswp_theme' => $this->getCurrentThemeName(),
            'pswp_token' => Tools::getToken(false),
            'pswp_rss_link' => $rss_link,
            'pswp_rss_title' => $rss_page_title,
            'pswp_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajaxps'),
        ]);

        return $this->display(__FILE__, 'front_header.tpl');
    }

    public function getWPData($type, $count = 10, $params = [], $use_cache = true, $no_content = false)
    {
        if (!$this->securekey) {
            return false;
        }

        if ($this->test_result === false) {
            return false;
        }

        // prevent recursive connection
        if (Tools::isSubmit('pswp_trigger')) {
            return false;
        }

        if ($type == 'posts') {
            $params['image_size'] = Configuration::get($this->settings_prefix . 'IMG_SIZE');
            $params['alt_the_content'] = Configuration::get($this->settings_prefix . 'ALT_THE_CONTENT');
        }

        // detect module conf page
        if ($no_content
            || (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == 'prestawp')
        ) {
            $params['no_content'] = 1;
        }

        $result = null;
        $id_lang = (!empty($params['id_lang']) ? $params['id_lang'] : $this->context->language->id);
        $cache_id = $type . $count . json_encode($params) . $this->context->shop->id . '-' . $id_lang;
        $cache_id = hash('sha256', $cache_id);
        $cache_lifetime = Configuration::get($this->settings_prefix . 'CACHE_LIFETIME');

        if ($use_cache && PSWPCache::isStored($cache_id, $cache_lifetime)) {
            $result = PSWPCache::get($cache_id, $cache_lifetime);
        } else {
            if ($this->connection_method == 'curl') {
                $result = $this->curlGetData($type, $count, $params);
            } else {
                $result = $this->fileGetContentsGetData($type, $count, $params);
            }

            PSWPCache::set($cache_id, $result);
        }

        if ($result) {
            $result = $this->removeBom($result);
            // skip errors etc
            if ($result[0] != '{' && $result[0] != '[') {
                $pos_cbraces = Tools::strpos($result, '{');
                $pos_sbraces = Tools::strpos($result, '[');
                $pos = 0;
                // find the first bracket and delete text before it (in case of third party errors etc)
                if ($pos_cbraces !== false && $pos_sbraces === false) {
                    $pos = $pos_sbraces;
                } elseif ($pos_cbraces === false && $pos_sbraces !== false) {
                    $pos = $pos_sbraces;
                } elseif ($pos_cbraces !== false && $pos_sbraces !== false) {
                    $pos = ($pos_cbraces < $pos_sbraces ? $pos_cbraces : $pos_sbraces);
                }
                $result = Tools::substr($result, $pos);
            }
            $result = json_decode($result, true);

            if (is_array($result)) {
                if ($type == 'posts') {
                    $cache_dir = _PS_MODULE_DIR_ . $this->name . '/cache/';

                    foreach ($result as $key => &$post) {
                        // skip invalid post types
                        if (Tools::strpos($post['post_type'], 'elementor') !== false
                            || Tools::strpos($post['post_type'], 'elementskit') !== false
                        ) {
                            unset($result[$key]);
                            continue;
                        }

                        // cache images
                        if ($this->cache_images && Tools::strpos($post['image'], 'http') !== false) {
                            $filename = $post['ID'] . '-' . pathinfo($post['image'], PATHINFO_BASENAME);

                            if ($filename) {
                                if (!file_exists($cache_dir . $filename)) {
                                    Tools::copy($post['image'], $cache_dir . $filename);
                                }
                                $post['image'] = _MODULE_DIR_ . $this->name . '/cache/' . $filename;
                            }
                        }

                        // remove duplicate title in content
                        if ($this->remove_dup_title) {
                            $title_plain = strip_tags($post['post_title']);
                            $content_plain = strip_tags($post['post_content']);
                            $main_content_plain = strip_tags($post['main_content']);

                            // Check if content starts with title
                            if (strpos($content_plain, $title_plain) === 0) {
                                // Remove the title from the content (only first occurrence)
                                $post['post_content'] = preg_replace('/' . preg_quote($title_plain, '/') . '/', '', $post['post_content'], 1);
                            }
                            if (strpos($main_content_plain, $title_plain) === 0) {
                                // Remove the title from the content (only first occurrence)
                                $post['main_content'] = preg_replace('/' . preg_quote($title_plain, '/') . '/', '', $post['main_content'], 1);
                            }
                        }

                        // prepare data if WP content should be viewed in PS
                        if ($this->view_in_ps) {
                            $post['wp_url'] = $post['url'];
                            $post['url'] = $this->getModuleLink($this->name, 'post', ['post_name' => $post['post_name']]);
                            $post['id'] = $post['ID'];
                            $post['title'] = $post['post_title'];
                        }

                        if ($this->use_raw_content) {
                            $post['main_content'] =
                                (!empty($post['post_excerpt']) ? $post['post_excerpt'] : $post['post_content']);
                        } else {
                            $post['main_content'] =
                                (!empty($post['post_excerpt']) ? $post['post_excerpt'] : $post['main_content']);
                        }

                        // Remove unnecessary tags and their content when only text needed.
                        // We're using strip_tags to get text content, so these tags can break the output.
                        // If "post_name" is specified, it's the post page, and we need all HTML content.
                        if (empty($params['post_name'])) {
                            $post['main_content'] = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $post['main_content']);
                            $post['main_content'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $post['main_content']);
                            $post['main_content'] = preg_replace('/<noscript\b[^>]*>(.*?)<\/noscript>/is', '', $post['main_content']);
                            $post['main_content'] = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $post['main_content']);
                        }
                    }
                }
            }

            return $result;
        }

        return null;
    }

    public function postDataToWP($data)
    {
        if (!$this->securekey) {
            return false;
        }

        if ($this->test_result === false) {
            return false;
        }

        // prevent recursive connection
        if (Tools::isSubmit('pswp_trigger')) {
            return false;
        }

        if ($this->connection_method == 'curl') {
            $result = $this->curlGetData('send_data', null, $data);
        } else {
            $result = $this->fileGetContentsGetData('send_data', null, $data);
        }

        if ($result) {
            $result = $this->removeBom($result);
            // skip errors etc
            if ($result[0] != '{' && $result[0] != '[') {
                $pos_cbraces = Tools::strpos($result, '{');
                $pos_sbraces = Tools::strpos($result, '[');
                $pos = 0;
                // find the first bracket and delete text before it (in case of third party errors etc)
                if ($pos_cbraces !== false && $pos_sbraces === false) {
                    $pos = $pos_sbraces;
                } elseif ($pos_cbraces === false && $pos_sbraces !== false) {
                    $pos = $pos_sbraces;
                } elseif ($pos_cbraces !== false && $pos_sbraces !== false) {
                    $pos = ($pos_cbraces < $pos_sbraces ? $pos_cbraces : $pos_sbraces);
                }
                $result = Tools::substr($result, $pos);
            }

            return json_decode($result, true);
        }

        return null;
    }

    // check for correct WordPress url:
    protected function checkWPURL($wppath = null)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');

        $wppath = ($wppath ? $wppath : $this->getWPPath($id_lang_default));
        if (!$wppath) {
            return false;
        }

        $wppath = rtrim($wppath, '/') . '/';
        $url = $wppath . '?pswp_trigger=1';
        $result = $this->checkURL404($url);

        return $result;
    }

    protected function checkURL404($url)
    {
        // prevent recursive connection
        if (Tools::isSubmit('pswp_trigger')) {
            return false;
        }

        if ($this->connection_method == 'curl' && $this->checkCurlAvail()) {
            $ch = curl_init();
            $url = str_replace('&amp;', '&', $url);

            if ($this->skip_ssl_check) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'spider');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 0);

            // HTTP basic auth:
            if ($this->user_htpasswd) {
                curl_setopt($ch, CURLOPT_USERPWD, $this->user_htpasswd . ':' . $this->pass_htpasswd);
            }

            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // error
            if (in_array($http_code, [0, '401', '404', '500', '301', '302'])) {
                return false;
            } else {
                return true;
            }
        } else {
            $OriginalUserAgent = ini_get('user_agent');
            ini_set('user_agent', 'Mozilla/5.0');
            $file_headers = @get_headers($url);
            ini_set('user_agent', $OriginalUserAgent);

            if (!$file_headers || !isset($file_headers[0]) || Tools::strpos($file_headers[0], '404') !== false) {
                $result = false;
            } else {
                $result = true;
            }
        }

        return $result;
    }

    protected function checkCacheExpired($type)
    {
        $cache_updated = Configuration::getGlobalValue($this->settings_prefix . 'CACHE_UPDATED_' . $type);
        if (!$cache_updated || !Configuration::get($this->settings_prefix . 'CACHE_LIFETIME')) {
            return true;
        }

        $cache_life = (time() - $cache_updated) / 60;

        if ($cache_life > Configuration::get($this->settings_prefix . 'CACHE_LIFETIME')) {
            return true;
        } else {
            return false;
        }
    }

    protected function renderInstallationInfo()
    {
        $wp_input = [
            'wp_category_options' => $this->getWPCategoriesOptionList(),
            'wp_post_options' => $this->getWPPostsOptionList(),
            'id_item' => '-instructions',
            'wp_posts_name' => 'WP_POSTS_POSTS',
            'wp_categories_name' => 'WP_POSTS_CATEGORIES',
        ];

        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'path' => $this->_path,
            'wp_input' => $wp_input,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/instructions.tpl');
    }

    protected function renderQuickGuide()
    {
        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'path' => $this->_path,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/guide.tpl');
    }

    public function getPSVersion($without_dots = false)
    {
        $ps_version = _PS_VERSION_;
        $ps_version = Tools::substr($ps_version, 0, 3);

        if ($without_dots) {
            $ps_version = str_replace('.', '', $ps_version);
        }

        return (float) $ps_version;
    }

    public function getSettings($url_desc = '', $url_class = '', $securekey_desc = '', $securekey_class = '')
    {
        $settings = array_merge(
            $this->getMainSettings($url_desc, $url_class, $securekey_desc, $securekey_class),
            $this->getPostsSettings(),
            $this->getProductPostsSettings(),
            $this->getPageSettings(),
            $this->getCommentsSettings(),
            $this->getCategoriesSettings()
        );

        return $settings;
    }

    public function getMainSettings($url_desc = '', $url_class = '', $securekey_desc = '', $securekey_class = '', $render_html = false)
    {
        $settings = [
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $this->renderSettingsSeparator($this->l('Connection'), $render_html),
                'form_group_class' => 'fg-separator',
            ],
            [
                'type' => 'text',
                'label' => $this->l('WordPress URL:'),
                'name' => 'WPPATH',
                'hint' => $this->l('E.g. http://yoursite.com/blog. You can set different URLs for each language. URL for default language is required, others are optional.'),
                'required' => true,
                'desc' => $url_desc,
                'class' => $url_class,
                'default' => '',
                'validate' => 'isUrl',
                'lang' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Secure key:'),
                'name' => 'SECUREKEY',
                'desc' => $securekey_desc,
                'hint' => $this->l('It should match the secure key in the WordPress plugin settings.'),
                'required' => true,
                'class' => $securekey_class,
                'default' => md5(time()),
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Cache lifetime:'),
                'name' => 'CACHE_LIFETIME',
                'hint' => $this->l('Choose how often the data will be updated. 0 for disable cache. 60 by default.'),
                'required' => false,
                'suffix' => $this->l('minutes'),
                'default' => 60,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'select',
                'name' => 'CONNECTION_METHOD',
                'class' => 't',
                'label' => $this->l('Connection method:'),
                'hint' => $this->l('Try to toggle this option if you have troubles connecting to WordPress.'),
                'validate' => 'isCleanHtml',
                'options' => [
                    'query' => [
                        [
                            'id_option' => 'file_get_contents',
                            'name' => $this->l('Default'),
                        ],
                        [
                            'id_option' => 'curl',
                            'name' => 'curl',
                        ],
                    ],
                    'id' => 'id_option',
                    'name' => 'name',
                ],
                'required' => false,
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Skip SSL verification:'),
                'hint' => $this->l('You can disable SSL check if you have some problems with your SSL certificate'),
                'name' => 'SKIP_SSL_CHECK',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'skip_ssl_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'skip_ssl_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $this->renderSettingsSeparator($this->l('Displaying'), $render_html),
                'form_group_class' => 'fg-separator',
            ],
            [
                'type' => 'select',
                'name' => 'THEME',
                'label' => $this->l('Theme for the post list:'),
                'class' => 't',
                'options' => ($render_html ? $this->getThemeOptions() : []),
                'default' => '',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Open links in new window:'),
                'name' => 'OPEN_BLANK',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'blank_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'blank_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
            ],
            'img_size' => [
                'type' => 'select',
                'name' => 'IMG_SIZE',
                'class' => 't',
                'label' => $this->l('Post preview image size:'),
                'hint' => $this->l('Here you can select the most suitable image size, so post images would not be too heavy while having good quality. This list contains image sizes available in WordPress.'),
                'validate' => 'isCleanHtml',
                'options' => [
                    'query' => [],
                    'id' => 'id_option',
                    'name' => 'name',
                ],
                'required' => false,
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => ($render_html
                    ? $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_more_options_btn.tpl')
                    : ''
                ),
            ],
            [
                'type' => 'textarea',
                'name' => 'CUSTOM_CSS',
                'label' => $this->l('Custom CSS:'),
                'hint' => $this->l('Add your styles directly in this field without editing files. Mainly for developers.'),
                'validate' => 'isCleanHtml',
                'resize' => true,
                'cols' => '',
                'rows' => '',
                'form_group_class' => 'pswp_more_options_row first-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'name' => 'PRODUCT_LIST_WRP',
                'label' => $this->l('Product list wrapper:'),
                'hint' => $this->l('Using this option you can change HTML structure of the product list wrapper when displaying products in WordPress via iframe. It is useful when your product list layout is very different from the default theme structure. Mainly for developers.'),
                'desc' => $this->l('Example: #js-product-list > .products.row.products-grid'),
                'validate' => 'isString',
                'allow_html' => false,
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Pre-download featured images:'),
                'hint' => $this->l('Pre-download featured images to avoid hotlinking protection (if any)'),
                'name' => 'CACHE_IMAGES',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'cache_images_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'cache_images_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Use raw WP content:'),
                'hint' => $this->l('Do not apply content filters to WordPress posts'),
                'name' => 'USE_RAW_CONTENT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'use_raw_content_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'use_raw_content_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Use alternative content processing:'),
                'hint' => $this->l('Enable this option if content is duplicated in different posts.'),
                'name' => 'ALT_THE_CONTENT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'alt_the_content_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'alt_the_content_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Remove duplicate title in content:'),
                'hint' => $this->l('Enable this option if your post content usually starts from the same text as the title.'),
                'name' => 'REMOVE_DUP_TITLE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'remove_dup_title_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'remove_dup_title_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Username for HTTP Basic Authentication:'),
                'hint' => $this->l('If your site is password protected, you can use this option to allow the module to connect'),
                'name' => 'USER_HTPASSWD',
                'validate' => 'isCleanHtml',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Password for HTTP Basic Authentication:'),
                'hint' => $this->l('If your site is password protected, you can use this option to allow the module to connect'),
                'name' => 'PASS_HTPASSWD',
                'validate' => 'isCleanHtml',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function getPostsSettings($load_wp_data = false)
    {
        $settings = [
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display WordPress posts at the home page:'),
                'name' => 'SHOW_POSTS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'posts_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'posts_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of posts to display:'),
                'name' => 'NUMBER_OF_POSTS',
                'hint' => $this->l('How many posts will be displayed in your shop'),
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of columns:'),
                'hint' => $this->l('Only in desktop view. On mobile there is always 1 column'),
                'name' => 'GRID_COLUMNS',
                'required' => false,
                'default' => 2,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'name' => '',
                'type' => 'wp_content',
                'label' => $this->l('Select posts to display:'),
                'hint' => $this->l('Select necessary categories to display posts from them. Select specific posts if necessary.'),
                'required' => false,
                'validate' => 'isString',
                'wp_category_options' => ($load_wp_data ? $this->getWPCategoriesOptionList() : []),
                'wp_post_options' => ($load_wp_data ? $this->getWPPostsOptionList(0, 'WP_POSTS_POSTS') : []),
                'id_item' => null,
                'wp_posts_name' => 'WP_POSTS_POSTS',
                'wp_categories_name' => 'WP_POSTS_CATEGORIES',
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => ($load_wp_data
                    ? $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_more_options_btn.tpl')
                    : ''
                ),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show featured images:'),
                'hint' => $this->l('Display featured image instead of post content.'),
                'name' => 'SHOW_FEATURED_IMAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_featured_image_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_featured_image_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row first-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text:'),
                'hint' => $this->l('Show the short post preview text in addition to the post title'),
                'name' => 'SHOW_PREVIEW',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_preview_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_preview_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text if no image:'),
                'hint' => $this->l('Show the preview text instead of featured image if there is no image'),
                'name' => 'SHOW_PREVIEW_NO_IMG',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_preview_no_img_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_preview_no_img_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Masonry view:'),
                'hint' => $this->l('Display posts in masonry view, fill all available space'),
                'name' => 'MASONRY',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'masonry_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'masonry_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title color:'),
                'name' => 'TITLE_COLOR',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title background color:'),
                'name' => 'TITLE_BG_COLOR',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show posts extra data (date and comments count):'),
                'name' => 'SHOW_ARTICLE_FOOTER',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'footer_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'footer_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display full posts:'),
                'hint' => $this->l('Show content after the "more"'),
                'name' => 'SHOW_FULL_POSTS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'full_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'full_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Remove HTML tags:'),
                'hint' => $this->l('Remove all HTML tags from post content'),
                'name' => 'POSTS_STRIP_TAGS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'strip_tags_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'strip_tags_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel / slider:'),
                'hint' => $this->l('Display posts as a horizontal carousel/slider.'),
                'name' => 'CAROUSEL',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel autoplay:'),
                'name' => 'CAROUSEL_AUTOPLAY',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_autoplay_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_autoplay_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel arrows:'),
                'hint' => $this->l('Show arrows for switching slides'),
                'name' => 'CAROUSEL_ARROWS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_arrows_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_arrows_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel dots:'),
                'hint' => $this->l('Show dot indicators below the carousel'),
                'name' => 'CAROUSEL_DOTS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_dots_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_dots_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Hide the block title:'),
                'name' => 'POSTS_HIDE_TITLE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'posts_hide_title_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'posts_hide_title_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row last-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Dynamically load the block:'),
                'hint' => $this->l('Load the block by Ajax after opening the page. It may increase the page loading speed.'),
                'name' => 'AJAX',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ajax_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ajax_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('HTML classes:'),
                'name' => 'CLASSES',
                'hint' => $this->l('Here you can add some custom classes for the block, it can help to style it similarly to other blocks in your theme.'),
                'desc' => $this->l('Example: container'),
                'required' => false,
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function getPageSettings($load_wp_data = false)
    {
        $pre_settings = [];
        if ($load_wp_data && $this->enable_posts_page && $this->getLangOptionValue('url_rewrite_page', null, true)) {
            $rss_urls = [];
            foreach (Language::getLanguages() as $lang) {
                $rss_urls[Tools::strtoupper($lang['iso_code'])] =
                    $this->context->link->getModuleLink($this->name, 'rss', [], null, $lang['id_lang']);
            }

            $this->context->smarty->assign([
                'pswp_page_link' => $this->getModuleLink($this->name, 'list'),
                'pswp_psv' => $this->getPSVersion(),
                'pswp_rss_urls' => $rss_urls,
                'pswp_sitemap_url' => $this->context->link->getModuleLink($this->name, 'sitemap'),
            ]);
            $html = $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_the_page_info.tpl'
            );
            $pre_settings = [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $html,
            ];
        }

        $settings = [
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $this->renderSettingsSeparator($this->l('Viewing posts in the store'), $load_wp_data),
                'form_group_class' => 'fg-separator',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('View posts in PrestaShop:'),
                'hint' => $this->l('Open WordPress posts directly in PrestaShop without redirecting to the blog.'),
                'name' => 'VIEW_IN_PS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ps_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ps_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Disable search engine indexation of posts in PrestaShop:'),
                'hint' => [
                    $this->l('In order to prevent content duplication and problems with SEO, it is necessary to disable indexation of posts either in PrestaShop or in WordPress.'),
                    $this->l('If you deactivate this option, it is recommended to close your WP blog from being indexed by search engines. For example in WordPress settings - "Settings > Reading > Search engine visibility".'),
                ],
                'name' => 'DISABLE_INDEXATION',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'disable_indexation_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'disable_indexation_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => '1',
                'validate' => 'isInt',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display comments:'),
                'hint' => $this->l('Enable this option to display comments on the post page'),
                'name' => 'PS_SHOW_COMMENTS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ps_show_comments_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ps_show_comments_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Allow to write new comments:'),
                'hint' => $this->l('Enable this option to visitors to add comments to posts'),
                'name' => 'PS_ALLOW_COMMENTING',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ps_allow_commenting_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ps_allow_commenting_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $this->renderSettingsSeparator($this->l('Post list'), $load_wp_data),
                'form_group_class' => 'fg-separator',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Create a separate page with list of all posts:'),
                'name' => 'ENABLE_POSTS_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'page_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'page_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => 'text',
                'name' => 'URL_REWRITE_PAGE',
                'label' => $this->l('Page URL:'),
                'lang' => true,
                'hint' => $this->l('A friendly URL for the posts page'),
                'desc' => $this->l('Example: "posts" (without quotes).'),
                'prefix' => ($load_wp_data ? $this->getBaseUrl() : ''),
                'form_group_class' => 'pswp-url-fg',
                'required' => true,
                'default' => 'posts',
                'validate' => 'isCleanHtml',
            ],
            'pre_settings' => $pre_settings,
            [
                'type' => 'text',
                'name' => 'META_TITLE_PAGE',
                'label' => $this->l('Meta title:'),
                'hint' => $this->l('Meta title of the page. Necessary for SEO'),
                'lang' => true,
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => 'text',
                'name' => 'META_DESC_PAGE',
                'label' => $this->l('Meta description:'),
                'hint' => $this->l('Meta description of the page. Necessary for SEO'),
                'lang' => true,
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => 'text',
                'name' => 'TITLE_PAGE',
                'label' => $this->l('Page title:'),
                'hint' => $this->l('Page heading displayed at the top of the page'),
                'lang' => true,
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => 'textarea',
                'lang' => true,
                'autoload_rte' => true,
                'name' => 'DESC_PAGE',
                'label' => $this->l('Page description text:'),
                'hint' => $this->l('Top text displayed above posts'),
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Posts per page:'),
                'hint' => $this->l('Number of posts displayed per page. Default is 10'),
                'name' => 'POSTS_PER_PAGE',
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of columns:'),
                'hint' => $this->l('Only in desktop view. On mobile there is always 1 column'),
                'name' => 'GRID_COLUMNS_PAGE',
                'required' => false,
                'default' => 2,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'name' => '',
                'type' => 'wp_content',
                'label' => $this->l('Select posts to display:'),
                'hint' => $this->l('Select necessary categories to display posts from them. Select specific posts if necessary.'),
                'required' => false,
                'validate' => 'isString',
                'wp_category_options' => ($load_wp_data ? $this->getWPCategoriesOptionList() : []),
                'wp_post_options' => ($load_wp_data ? $this->getWPPostsOptionList(0, 'WP_POSTS_POSTS_PAGE') : []),
                'id_item' => null,
                'wp_posts_name' => 'WP_POSTS_POSTS_PAGE',
                'wp_categories_name' => 'WP_POSTS_CATEGORIES_PAGE',
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => ($load_wp_data
                    ? $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_more_options_btn.tpl')
                    : ''
                ),
            ],
            [
                'type' => 'text',
                'name' => 'META_KEYWORDS_PAGE',
                'label' => $this->l('Meta keywords:'),
                'hint' => $this->l('Meta keywords of the page. Necessary for SEO'),
                'lang' => true,
                'validate' => 'isCleanHtml',
                'form_group_class' => 'pswp_more_options_row first-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show featured images:'),
                'hint' => $this->l('Display featured image instead of post content.'),
                'name' => 'SHOW_FEATURED_IMAGE_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_featured_image_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_featured_image_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text:'),
                'hint' => $this->l('Show the short post preview text in addition to the post title'),
                'name' => 'SHOW_PREVIEW_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_preview_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_preview_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text if no image:'),
                'hint' => $this->l('Show the preview text instead of featured image if there is no image'),
                'name' => 'SHOW_PREVIEW_NO_IMG_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_preview_no_img_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_preview_no_img_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Masonry view:'),
                'hint' => $this->l('Display posts in masonry view, fill all available space'),
                'name' => 'MASONRY_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'masonry_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'masonry_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title color:'),
                'name' => 'TITLE_COLOR_PAGE',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title background color:'),
                'name' => 'TITLE_BG_COLOR_PAGE',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post extra data (date etc):'),
                'name' => 'SHOW_ARTICLE_FOOTER_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'footer_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'footer_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display full posts:'),
                'hint' => $this->l('Show content after the "more"'),
                'name' => 'SHOW_FULL_POSTS_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'full_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'full_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Remove HTML tags:'),
                'hint' => $this->l('Remove all HTML tags from post content'),
                'name' => 'POSTS_STRIP_TAGS_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'strip_tags_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'strip_tags_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of posts in the RSS feed:'),
                'hint' => $this->l('Enter the number of recent articles that will be shown in the RSS feed. The default is 10.'),
                'name' => 'RSS_LIMIT',
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
                'form_group_class' => 'pswp_more_options_row last-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'name' => 'CATEGORY_URL',
                'label' => $this->l('The category page URL:'),
                'lang' => true,
                'hint' => $this->l('A friendly URL for the category pages.'),
                'desc' => $this->l('Default: "category"'),
                'prefix' => ($load_wp_data ? $this->getBaseUrl() . '/' : ''),
                'form_group_class' => 'pswp_more_options_row pswp-url-fg last-row ps' . $this->getPSVersion(true),
                'required' => false,
                'default' => 'category',
                'validate' => 'isCleanHtml',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show the search form:'),
                'hint' => $this->l('Show the search field above the post list'),
                'name' => 'SHOW_SEARCH_PAGE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_search_page_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_search_page_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
        ];

        // remove the empty element if necessary
        if (empty($settings['pre_settings'])) {
            unset($settings['pre_settings']);
        }

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function getProductPostsSettings($render_info = false)
    {
        $html = '';
        if ($render_info) {
            $this->context->smarty->assign([
                'pswp_link' => $this->context->link,
                'pswp_random_product_id' => $this->getRandomProductID(),
                'pswp_psv' => $this->getPSVersion(),
            ]);
            $html = $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_product_post_info.tpl'
            );
        }

        $settings = [
            [
                'type' => 'full_width_html',
                'name' => '',
                'label' => '',
                'html_content' => $html,
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display WordPress posts at the product page:'),
                'name' => 'SHOW_POSTS_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pposts_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pposts_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
            ],
            [
                'type' => 'select',
                'name' => 'HOOK_PRODUCT',
                'class' => 't',
                'label' => $this->l('Position:'),
                'hint' => $this->l('Choose a hook for displaying posts at the product page.'),
                'desc' => $this->l('Please note that your theme may not support some hooks.'),
                'validate' => 'isCleanHtml',
                'options' => [
                    'query' => $this->getProductHookList(),
                    'id' => 'id_option',
                    'name' => 'name',
                ],
                'default' => 'displayFooterProduct',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of columns:'),
                'hint' => $this->l('Only in desktop view. On mobile there is always 1 column'),
                'name' => 'GRID_COLUMNS_PRODUCT',
                'required' => false,
                'default' => 2,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Maximum number of posts:'),
                'hint' => $this->l('You can limit the maximum number of posts to display. This is useful, for example, if you want to show all the posts from a category.'),
                'name' => 'MAX_POSTS_PRODUCT',
                'required' => false,
                'default' => 8,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => ($render_info
                    ? $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_more_options_btn.tpl')
                    : ''
                ),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show featured images:'),
                'hint' => $this->l('Display featured image instead of post content.'),
                'name' => 'SHOW_FEATURED_IMAGE_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pshow_featured_image_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pshow_featured_image_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row first-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text:'),
                'hint' => $this->l('Show the short post preview text in addition to the post title'),
                'name' => 'SHOW_PREVIEW_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'show_ppreview_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'show_ppreview_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text if no image:'),
                'hint' => $this->l('Show the preview text instead of featured image if there is no image'),
                'name' => 'SHOW_PREVIEW_NO_IMG_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pshow_preview_no_img_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pshow_preview_no_img_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Masonry view:'),
                'hint' => $this->l('Display posts in masonry view, fill all available space'),
                'name' => 'MASONRY_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pmasonry_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pmasonry_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title color:'),
                'name' => 'TITLE_COLOR_PRODUCT',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title background color:'),
                'name' => 'TITLE_BG_COLOR_PRODUCT',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show posts extra data (date and comments count):'),
                'name' => 'SHOW_ARTICLE_FOOTER_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'footer_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'footer_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display full posts:'),
                'hint' => $this->l('Show content after the "more"'),
                'name' => 'SHOW_FULL_POSTS_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pfull_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pfull_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Remove HTML tags:'),
                'hint' => $this->l('Remove all HTML tags from post content'),
                'name' => 'POSTS_STRIP_TAGS_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'pstrip_tags_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'pstrip_tags_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel / slider:'),
                'hint' => $this->l('Display posts as a horizontal carousel/slider.'),
                'name' => 'CAROUSEL_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_product_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_product_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel autoplay:'),
                'name' => 'CAROUSEL_AUTOPLAY_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_autoplay_product_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_autoplay_product_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel arrows:'),
                'hint' => $this->l('Show arrows for switching slides'),
                'name' => 'CAROUSEL_ARROWS_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_arrows_product_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_arrows_product_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel dots:'),
                'hint' => $this->l('Show dot indicators below the carousel'),
                'name' => 'CAROUSEL_DOTS_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'carousel_dots_product_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'carousel_dots_product_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row last-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Dynamically load the block:'),
                'hint' => $this->l('Load the block by Ajax after opening the page. It may increase the page loading speed.'),
                'name' => 'AJAX_PRODUCT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ajax_product_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ajax_product_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function getCommentsSettings()
    {
        $settings = [
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display latest comments in the left column:'),
                'name' => 'SHOW_COMMENTS',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'comments_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'comments_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display comment date:'),
                'name' => 'SHOW_COMMENTS_DATE',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'commentsdate_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'commentsdate_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display comment content (first 200 symbols):'),
                'name' => 'SHOW_COMMENTS_CONTENT',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'commentscontent_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'commentscontent_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of comments to display:'),
                'name' => 'NUMBER_OF_COMMENTS',
                'hint' => $this->l('How many comments will be displayed in the comments block'),
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function getCategoriesSettings($load_wp_data = false)
    {
        $settings = [
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display WordPress categories in the left column:'),
                'name' => 'SHOW_CATEGORIES',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'categories_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'categories_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Max number of categories to display:'),
                'name' => 'NUMBER_OF_CATEGORIES',
                'hint' => $this->l('How many categories will be displayed in the categories block'),
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'select',
                'multiple' => true,
                'size' => 10,
                'label' => $this->l('Select categories to display:'),
                'name' => 'WP_CATEGORIES[]',
                'class' => 'wp_categories_input',
                'hint' => $this->l('Select some categories if you don\'t need all of them.'),
                'required' => false,
                'validate' => 'isArrayWithIds',
                'options' => [
                    'query' => ($load_wp_data ? $this->getWPCategoriesOptionList() : []),
                    'id' => 'id_option',
                    'name' => 'name',
                ],
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        // check whether it's the module's page
        if (Tools::getValue('configure') == $this->name
            || $this->context->controller->controller_name == 'AdminProducts'
        ) {
            $token = Tools::getAdminTokenLite('AdminModules');
            $ajax_url = 'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . $token;

            $this->context->smarty->assign([
                'psv' => $this->getPSVersion(),
                'ajax_url' => $ajax_url,
            ]);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/admin_header.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $css_version = (version_compare(_PS_VERSION_, '8.0.0', '>=') ? '?' . $this->version : '');
        $js_version = (version_compare(_PS_VERSION_, '1.6.1', '>=') ? '?' . $this->version : '');

        if (Tools::getValue('configure') == $this->name
            || $this->context->controller->controller_name == 'AdminProducts'
        ) {
            $this->context->controller->addCSS($this->_path . 'views/css/spectrum.css' . $css_version, 'all');
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css' . $css_version, 'all');

            if ($this->context->controller->controller_name == 'AdminModules') {
                $this->context->controller->addJquery();
            }
            $this->context->controller->addJS($this->_path . 'views/js/jquery.spectrum.min.js' . $js_version);
            $this->context->controller->addJS($this->_path . 'views/js/admin.js' . $js_version);
        }
    }

    public function loadSettings()
    {
        $languages = Language::getLanguages(false);
        foreach ($this->getSettings() as $item) {
            if (empty($item['type']) || $item['type'] == 'html' || empty($item['name'])) {
                continue;
            }
            $name = Tools::strtolower($item['name']);
            if (Tools::substr($name, -2) == '[]') {
                $name = Tools::substr($name, 0, -2);
            }

            if (isset($item['lang']) && $item['lang']) {
                foreach ($languages as $lang) {
                    $this->{$name}[$lang['id_lang']] =
                        Configuration::get($this->settings_prefix . Tools::strtoupper($name), $lang['id_lang']);
                }
            } else {
                $this->$name = Configuration::get($this->settings_prefix . Tools::strtoupper($name));
            }
        }
    }

    protected function checkCurlAvail()
    {
        return function_exists('curl_version');
    }

    protected function getAdvancedModeURL($id_lang = null)
    {
        $wp_path = rtrim($this->getWPPath($id_lang), '/');
        $url = $wp_path . '/?pswp_trigger=1';
        $url = str_replace('&amp;', '&', $url);

        return $url;
    }

    protected function curlGetData($type, $count, $params = [])
    {
        $id_lang = null;
        $iso_lang = $this->context->language->iso_code;
        if (!empty($params['id_lang'])) {
            $iso_lang = Language::getIsoById($params['id_lang']);
            $id_lang = $params['id_lang'];
        }
        $url = $this->getAdvancedModeURL($id_lang);
        $ch = curl_init();
        $url = str_replace('&amp;', '&', $url);

        if ($this->skip_ssl_check) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'spider');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        // HTTP basic auth:
        if ($this->user_htpasswd) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->user_htpasswd . ':' . $this->pass_htpasswd);
        }

        $post_data = [
            'securekey' => $this->securekey,
            'type' => $type,
            'count' => $count,
            'lang' => $iso_lang,
            'params' => $params,
        ];
        if (isset($params['start']) && $params['start']) {
            $post_data['start'] = $params['start'];
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($data === false && Tools::getValue('configure') == $this->name && curl_error($ch)) {
            $this->errors[] = curl_error($ch);
        }
        curl_close($ch);

        // error
        if (in_array($http_code, ['401', '404', '500'])) {
            return [];
        } else {
            return $data;
        }
    }

    protected function fileGetContentsGetData($type, $count, $params = [])
    {
        $OriginalUserAgent = ini_get('user_agent');
        ini_set('user_agent', 'Mozilla/5.0');

        $id_lang = null;
        $iso_lang = $this->context->language->iso_code;
        if (!empty($params['id_lang'])) {
            $iso_lang = Language::getIsoById($params['id_lang']);
            $id_lang = $params['id_lang'];
        }
        $url = $this->getAdvancedModeURL($id_lang);

        $post_data = [
            'securekey' => $this->securekey,
            'type' => $type,
            'count' => $count,
            'lang' => $iso_lang,
            'params' => $params,
        ];
        if (isset($params['start']) && $params['start']) {
            $post_data['start'] = $params['start'];
        }

        $auth = '';
        // if using HTTP Basic Authentication (htpasswd):
        if ($this->user_htpasswd) {
            $auth = "\r\n" . 'Authorization: Basic ' . base64_encode($this->user_htpasswd . ':' . $this->pass_htpasswd);
        }

        $query = http_build_query($post_data);
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded' . $auth,
                'content' => $query,
                'timeout' => 15,
                'follow_location' => 1,
            ],
        ];

        $context = stream_context_create($opts);
        $result = Tools::file_get_contents($url, false, $context);

        ini_set('user_agent', $OriginalUserAgent);

        return $result;
    }

    public function getWPPath($id_lang = null)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $id_lang = $id_lang ? $id_lang : $this->context->language->id;

        $wppath = '';
        if (isset($this->wppath[$id_lang])) {
            $wppath = $this->wppath[$id_lang];
        }

        if (!$wppath && $id_lang_default != $id_lang) {
            $wppath = $this->getWPPath($id_lang_default);
        }

        return $wppath;
    }

    public function clearSmartyCache($template_file = null)
    {
        $directory = _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/';
        $templates = array_diff(scandir($directory), ['..', '.']);
        foreach ($templates as $key => &$template) {
            if (Tools::strpos($template, '.tpl') === false) {
                continue;
            }

            if ($template_file !== null) {
                if ($template != $template_file) {
                    continue;
                }
            }

            $template = basename($template, '.tpl');

            if (method_exists($this, '_clearCache')) {
                $this->_clearCache($template);
            }

            if ($this->getPSVersion() >= 1.7 && method_exists($this, '_deferedClearCache')) {
                $this->_deferedClearCache($this->getTemplatePath($template), null, null);
            }
        }
    }

    public function renderWidget($hookName, array $params)
    {
        if (!$hookName) {
            $hookName = (isset($params['hook']) ? $params['hook'] : '');
            // if no hook specified, show the default post list
            if (!$hookName) {
                return $this->hookPSWPposts($params);
            }
        } elseif ($hookName == 'displayViaLeoelements') {
            // if the widget was added by Leo Elements, show the default post list
            return $this->hookPSWPposts($params);
        }

        if ($this->getPSVersion() >= 1.7) {
            $tpl_file = 'module:' . $this->name . '/views/templates/hook/blocks.tpl';
        } else {
            $tpl_file = 'blocks.tpl';
        }

        $cache_id = $this->getBlockCacheId($hookName);
        if (!$this->isCached($tpl_file, $cache_id)) {
            $this->context->smarty->assign($this->getWidgetVariables($hookName, $params));
            // clear old cache
            $this->clearSmartyCache('blocks.tpl');
        }

        if ($this->getPSVersion() >= 1.7) {
            return $this->fetch($tpl_file, $cache_id);
        } else {
            return $this->display(__FILE__, $tpl_file, $cache_id);
        }
    }

    public function getWidgetVariables($hookName, array $params)
    {
        $tpl_file = $this->name . '/views/templates/hook/_block.tpl';
        $tpl_file_posts = $this->name . '/views/templates/hook/_block_posts.tpl';

        return [
            'psv' => $this->getPSVersion(),
            'psvd' => $this->getPSVersion(true),
            'psvwd' => $this->getPSVersion(true),
            'pswp_blocks' => PSWPBlock::getBlocksFront($hookName),
            'pswp_block_hook' => $hookName,
            'pswp_block_tpl_file' => (file_exists(_PS_THEME_DIR_ . 'modules/' . $tpl_file)
                ? _PS_THEME_DIR_ . 'modules/' . $tpl_file : _PS_MODULE_DIR_ . $tpl_file),
            'pswp_block_posts_tpl_file' => (file_exists(_PS_THEME_DIR_ . 'modules/' . $tpl_file_posts)
                ? _PS_THEME_DIR_ . 'modules/' . $tpl_file_posts : _PS_MODULE_DIR_ . $tpl_file_posts),
            'pswp_blank' => $this->open_blank,
            'pswp_wp_path' => $this->getWPPath(),
            'pswp_enable_posts_page' => $this->enable_posts_page,
            'pswp_posts_page_url' => $this->getModuleLink($this->name, 'list'),
            'pswp_theme' => $this->getCurrentThemeName(),
        ];
    }

    public function slashRedirect()
    {
        // remove the ending slash symbol
        $request_uri = $_SERVER['REQUEST_URI'];
        if (rtrim($request_uri, '/') != $request_uri) {
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));

            $url = 'http' . ($force_ssl ? 's' : '') . "://$_SERVER[HTTP_HOST]" . rtrim($request_uri, '/');
            Tools::redirect($url);
        }
    }

    public function hookModuleRoutes($params)
    {
        $routes = [
            'module-prestawp-products' => [
                'controller' => 'products',
                'rule' => 'pswp-products',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestawp',
                ],
            ],
        ];

        $iso = null;
        if (Tools::getValue('isolang')) {
            $iso = Tools::getValue('isolang');
            if (!Language::getIdByIso($iso)) {
                return $routes;
            }
        }

        $default_url = $this->getLangOptionValue('url_rewrite_page', null, true);
        $default_url = trim($default_url);
        $default_url = trim($default_url, '/');

        $urls = $this->url_rewrite_page;
        if (is_array($urls) && array_filter($urls)) {
            $regexp = '[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS\-]+';
            foreach ($urls as $id_lang => $url) {
                $url = trim($url);
                $url = trim($url, '/');
                $url = ($url ?: $default_url);
                if (!$url) {
                    continue;
                }

                $category_url = Configuration::get($this->settings_prefix . 'CATEGORY_URL', $id_lang);
                $category_url = ($category_url ? $category_url : 'category');
                $routes_plus = [
                    'module-prestawp-list-' . (int) $id_lang => [
                        'controller' => 'list',
                        'rule' => $url,
                        'keywords' => [],
                        'params' => [
                            'fc' => 'module',
                            'module' => 'prestawp',
                            'url_rewrite' => $url,
                        ],
                    ],
                    'module-prestawp-wpcategory-' . (int) $id_lang => [
                        'controller' => 'wpcategory',
                        'rule' => $url . '/' . $category_url . '/{category_name}',
                        'keywords' => [
                            'category_name' => ['regexp' => $regexp, 'param' => 'category_name'],
                        ],
                        'params' => [
                            'fc' => 'module',
                            'module' => 'prestawp',
                            'url_rewrite' => $url,
                        ],
                    ],
                    'module-prestawp-post-' . (int) $id_lang => [
                        'controller' => 'post',
                        'rule' => $url . '/{post_name}',
                        'keywords' => [
                            'post_name' => ['regexp' => $regexp, 'param' => 'post_name'],
                        ],
                        'params' => [
                            'fc' => 'module',
                            'module' => 'prestawp',
                            'url_rewrite' => $url,
                        ],
                    ],
                ];

                $routes = array_merge($routes, $routes_plus);
            }
        }

        // Add URLs with trailing slash for Front Office
        if (!defined('_PS_ADMIN_DIR_')) {
            foreach ($routes as $key => $route) {
                $route['rule'] .= '/';
                $routes[$key . '/'] = $route;
            }
        }

        return $routes;
    }

    public function getModulePath()
    {
        return $this->_path;
    }

    protected function createListHelper($table, $identifier = null)
    {
        if ($identifier === null) {
            $identifier = 'id_' . $table;
        }

        $this->context->cookie->{$table . '_pagination'} =
            Tools::getValue($table . '_pagination', $this->context->cookie->{$table . '_pagination'});
        if (!$this->context->cookie->{$table . '_pagination'}) {
            $this->context->cookie->{$table . '_pagination'} = 20;
        }

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = $identifier;
        $helper->actions = [];
        $helper->show_toolbar = false;
        $helper->_defaultOrderBy = 'id_prestawp_block';
        $helper->list_id = $table;
        $helper->table_id = $table;
        $helper->actions = ['edit', 'delete'];
        $helper->table = $table;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->currentIndex = str_replace('adminmodules', 'AdminModules', $helper->currentIndex);
        $helper->no_link = false;
        $helper->tpl_vars = [
            'pbc_psv' => $this->getPSVersion(),
        ];

        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            if (isset($this->context->cookie->{$helper->table . '_pagination'})
                && $this->context->cookie->{$helper->table . '_pagination'}) {
                $helper->_default_pagination = $this->context->cookie->{$helper->table . '_pagination'};
            } elseif ($this->getPSVersion() > 1.5) {
                $helper->_default_pagination = $helper->_pagination[0];
            } else {
                $helper->_default_pagination = 20;
            }
        }
        $helper->module = $this;

        $order_way = Tools::strtolower(Tools::getValue($table . 'Orderway'));
        $order_way = ($order_way == 'desc' ? 'desc' : 'asc');
        $order_by = Tools::getValue($table . 'Orderby', 'id_prestawp_block');
        $helper->orderBy = $order_by;
        $helper->orderWay = $order_way;
        $p = (int) Tools::getValue('submitFilter' . $table, Tools::getValue('page', 1));
        if ($p < 1) {
            $p = 1;
        }
        $helper->page = $p;

        $helper->n = Tools::getValue(
            $table . '_pagination',
            isset($this->context->cookie->{$table . '_pagination'}) ?
                $this->context->cookie->{$table . '_pagination'} :
                $helper->_default_pagination
        );

        return $helper;
    }

    protected function createFormHelper(&$form_settings, $table, $item = null)
    {
        if ($this->getPSVersion() == 1.5) {
            foreach ($form_settings as &$form) {
                $form['form']['submit']['class'] = 'button ps15-submit-button';
            }
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
                Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') :
                0;
        $this->fields_form = [];

        $helper->identifier = 'id_' . $table;
        $helper->submit_action = 'submit' . $table;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [],
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
            'psvd' => $this->getPSVersion(true),
            'psvwd' => $this->getPSVersion(true),
            'psv' => $this->getPSVersion(),
            'pswp_ps_version' => _PS_VERSION_,
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'post_select_form_tpl' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/_post_select_form.tpl',
        ];
        $helper->module = $this;
        if (Validate::isLoadedObject($item) && $item->id) {
            $helper->id = $item->id;
        }

        $languages = Language::getLanguages(false);
        foreach ($form_settings as $form) {
            foreach ($form['form']['input'] as $row) {
                if (!empty($row['type']) && $row['type'] != 'html' && $row['name']) {
                    if (Validate::isLoadedObject($item) && $item->id) {
                        $item_name = str_replace('[]', '', $row['name']);
                        $helper->tpl_vars['fields_value'][$row['name']] = $item->{$item_name};
                        if (Tools::isSubmit($row['name'])) {
                            $helper->tpl_vars['fields_value'][$row['name']] = Tools::getValue($row['name']);
                        }
                    } else {
                        if (isset($row['lang']) && $row['lang']) {
                            foreach ($languages as $language) {
                                $helper->tpl_vars['fields_value'][$row['name']][$language['id_lang']] =
                                    Tools::getValue($row['name'] . '_' . $language['id_lang']);
                            }
                        } else {
                            $helper->tpl_vars['fields_value'][$row['name']] = Tools::getValue($row['name']);
                        }
                    }
                }
            }
        }

        $iso = $this->context->language->iso_code;
        $helper->tpl_vars['iso'] = file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
        $helper->tpl_vars['path_css'] = _THEME_CSS_DIR_;
        $helper->tpl_vars['ad'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
        $helper->tpl_vars['tinymce'] = true;

        return $helper;
    }

    protected function loadSettingsValues($settings, &$helper)
    {
        foreach ($settings as $item) {
            if (empty($item['type']) || $item['type'] == 'html') {
                continue;
            }
            if (isset($item['lang']) && $item['lang']) {
                foreach (Language::getLanguages(false) as $language) {
                    $helper->tpl_vars['fields_value'][$item['name']][$language['id_lang']] = Configuration::get(
                        $this->settings_prefix . $item['name'],
                        $language['id_lang']
                    );
                }
            } elseif ($item['type'] == 'colors') {
                foreach ($this->getColorsData() as $i => $color) {
                    $helper->tpl_vars['fields_value'][$item['name']][$i] = Configuration::get(
                        $this->settings_prefix . $item['name'] . '_' . $i
                    );
                }
            } elseif (isset($item['validate']) && $item['validate'] == 'isArrayWithIds') {
                $item_name = str_replace('[]', '', $item['name']);
                $value = Configuration::get($this->settings_prefix . $item_name);
                $value = explode(',', $value);
                $helper->tpl_vars['fields_value'][$item['name']] = $value;
            } elseif ($item['type'] == 'wp_content') {
                $input_names = [$item['wp_posts_name'], $item['wp_categories_name']];
                foreach ($input_names as $input_name) {
                    $value = Configuration::get($this->settings_prefix . $input_name);
                    $value = explode(',', $value);
                    $helper->tpl_vars['fields_value'][$input_name] = $value;
                }
            } else {
                $helper->tpl_vars['fields_value'][$item['name']] = Configuration::get(
                    $this->settings_prefix .
                    $item['name']
                );
            }
            if ($item['name'] == 'CUSTOM_CSS') {
                $helper->tpl_vars['fields_value'][$item['name']] = html_entity_decode(
                    Configuration::get($this->settings_prefix . $item['name'])
                );
            }
        }
    }

    public function ajaxProcessHideGuide()
    {
        $this->setOption('HIDE_QUICK_GUIDE', 1);
    }

    protected function renderFeedbackBlock()
    {
        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'module_path' => $this->_path,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/feedback.tpl');
    }

    protected function renderNewBlockButton()
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $form_url = 'index.php?controller=AdminModules&configure=' . $this->name . '&createNewBlock=1&token=' . $token;

        $this->context->smarty->assign([
            'form_url' => $form_url,
            'psv' => $this->getPSVersion(),
            'psvd' => $this->getPSVersion(true),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/new_block.tpl');
    }

    protected function getBlockFormFields($id_item = null)
    {
        $selected_ps_categories = [];
        $block = null;
        if ($id_item) {
            $block = new PSWPBlock($id_item);
            $selected_ps_categories = $block->ps_categories;
        }
        $fields = [
            'hooks' => [
                'type' => 'select',
                'name' => 'hook',
                'class' => 't',
                'id' => 'hook' . ($id_item ? $id_item : ''),
                'label' => $this->l('Position:'),
                'hint' => $this->l('Choose a hook for displaying this block.'),
                'desc' => $this->l('Please note that your theme may not support some hooks.'),
                'validate' => 'isCleanHtml',
                'options' => [
                    'query' => $this->getBlockHookList(),
                    'id' => 'id_option',
                    'name' => 'name',
                ],
                'required' => true,
            ],
            [
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Block title:'),
                'validate' => 'isCleanHtml',
                'required' => false,
                'lang' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of posts to display:'),
                'name' => 'number_of_posts',
                'hint' => $this->l('How many posts will be displayed at the home page.'),
                'required' => false,
                'default' => 10,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Number of columns:'),
                'hint' => $this->l('Only in desktop view. On mobile there is always 1 column'),
                'name' => 'grid_columns',
                'required' => false,
                'default' => 2,
                'validate' => 'isInt',
                'class' => 'fixed-width-sm',
            ],
            [
                'name' => '',
                'type' => 'wp_content',
                'label' => $this->l('Select posts to display:'),
                'hint' => $this->l('Select necessary categories to display posts from them. Select specific posts if necessary.'),
                'required' => false,
                'validate' => 'isString',
                'wp_category_options' => $this->getWPCategoriesOptionList($id_item),
                'wp_post_options' => $this->getWPPostsOptionList($id_item),
                'id_item' => $id_item,
            ],
            'ps_categories' => [
                'type' => 'categories',
                'label' => $this->l('Show only in these categories:'),
                'hint' => $this->l('This block will be shown only at pages of chosen categories or their products. It depends on selected position.'),
                'name' => 'ps_categories',
                'required' => false,
                'validate' => 'isString',
                'tree' => [
                    'root_category' => $this->context->shop->id_category,
                    'id' => 'id_category' . ($id_item ? $id_item : ''),
                    'name' => 'block_categoryBox',
                    'selected_categories' => $selected_ps_categories,
                    'use_checkbox' => true,
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $fields[] = [
                'type' => 'shops',
                'name' => 'shops',
                'label' => $this->l('Shops:'),
                'hint' => $this->l('Enable this block for selected shops'),
            ];
        }

        $fields = array_merge($fields, [
            [
                'type' => 'html',
                'name' => '',
                'label' => '',
                'html_content' => $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_more_options_btn.tpl'
                ),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show featured images:'),
                'hint' => $this->l('Display featured image instead of post content.'),
                'name' => 'show_featured_image',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_show_featured_image_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_show_featured_image_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row first-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text:'),
                'hint' => $this->l('Show the short post preview text in addition to the post title'),
                'name' => 'show_preview',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_show_preview_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_show_preview_on',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show post preview text if no image:'),
                'hint' => $this->l('Show the preview text instead of featured image if there is no image'),
                'name' => 'show_preview_no_img',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_show_preview_no_img_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_show_preview_no_img_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Masonry view:'),
                'hint' => $this->l('Display posts in masonry view, fill all available space'),
                'name' => 'masonry',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_masonry_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_masonry_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title color:'),
                'name' => 'title_color',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post title background color:'),
                'name' => 'title_bg_color',
                'required' => false,
                'class' => 'pswpColorPickerInput',
                'default' => '',
                'validate' => 'isString',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Show posts extra data (date and comments count):'),
                'name' => 'show_article_footer',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_footer_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_footer_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Display full posts:'),
                'hint' => $this->l('Show content after the "more"'),
                'name' => 'show_full_posts',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_full_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_full_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Remove HTML tags:'),
                'hint' => $this->l('Remove all HTML tags from post content'),
                'name' => 'strip_tags',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_strip_tags_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_strip_tags_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'text',
                'label' => $this->l('Maximum length of displayed content, characters:'),
                'hint' => $this->l('Text exceeding this limit will be truncated'),
                'name' => 'truncate',
                'required' => false,
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
                'class' => 'fixed-width-sm',
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel / slider:'),
                'hint' => $this->l('Display posts as a horizontal carousel/slider.'),
                'name' => 'carousel',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_carousel_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_carousel_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel autoplay:'),
                'name' => 'carousel_autoplay',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_carousel_autoplay_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_carousel_autoplay_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel arrows:'),
                'hint' => $this->l('Show arrows for switching slides'),
                'name' => 'carousel_arrows',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_carousel_arrows_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_carousel_arrows_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 1,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Carousel dots:'),
                'hint' => $this->l('Show dot indicators below the carousel'),
                'name' => 'carousel_dots',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'b_carousel_dots_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'b_carousel_dots_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row pswp_more_option_carousel_row last-row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'label' => $this->l('Dynamically load the block:'),
                'hint' => $this->l('Load the block by Ajax after opening the page. It may increase the page loading speed.'),
                'name' => 'ajax_load',
                'class' => 't',
                'values' => [
                    [
                        'id' => 'ajax_load_on' . ($id_item ? $id_item : ''),
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'ajax_load_off' . ($id_item ? $id_item : ''),
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
                'form_group_class' => 'pswp_more_options_row ps' . $this->getPSVersion(true),
            ],
            [
                'type' => 'html',
                'name' => '',
                'id' => 'error_container',
                'html_content' => $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_errors_wrp.tpl'
                ),
            ],
        ]);

        if ($this->getPSVersion() >= 1.7) {
            $fields['hooks'] = [
                'type' => 'select',
                'name' => 'hook',
                'class' => 't',
                'label' => $this->l('Position:'),
                'hint' => $this->l('Choose a hook for displaying this block.'),
                'desc' => $this->l('Please note that your theme may not support some hooks.'),
                'validate' => 'isCleanHtml',
                'options' => [
                    'optiongroup' => [
                        'label' => 'name',
                        'query' => $this->getBlockHookList(),
                    ],
                    'options' => [
                        'id' => 'id_option',
                        'name' => 'name',
                        'query' => 'query',
                    ],
                ],
                'required' => true,
            ];
        }

        foreach ($fields as &$field) {
            if (!(isset($field['id']) && $field['id'])) {
                $fname = $field['name'];
                $fname = str_replace('[]', '', $fname);
                $field['id'] = $fname . ($id_item ? $id_item : '');
            }
        }

        if ($this->getPSVersion() < 1.6) {
            foreach ($fields as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }

            // PS1.5 Category input
            $root_category = Category::getRootCategory();
            if (!$root_category->id) {
                $root_category->id = 0;
                $root_category->name = $this->l('Root');
            }
            $root_category = ['id_category' => (int) $root_category->id, 'name' => $root_category->name];
            $trads = [
                'Root' => $root_category,
                'selected' => $this->l('Selected'),
                'Check all' => $this->l('Check all'),
                'Check All' => $this->l('Check All'),
                'Uncheck All' => $this->l('Uncheck All'),
                'Collapse All' => $this->l('Collapse All'),
                'Expand All' => $this->l('Expand All'),
                'search' => $this->l('Search a category'),
            ];
            $fields['ps_categories']['values'] = [
                'trads' => $trads,
                'selected_cat' => $selected_ps_categories,
                'input_name' => 'CATEGORIES[]',
                'use_radio' => false,
                'use_search' => true,
                'disabled_categories' => [],
                'top_category' => Category::getTopCategory(),
                'use_context' => true,
            ];
        }

        $this->context->smarty->assign([
            'pswp_block' => $block,
        ]);

        return $fields;
    }

    protected function getBlockHookList($list = false)
    {
        $hooks = [
            [
                'id_option' => 'displayHome',
                'name' => $this->l('Home page'),
            ],
            [
                'id_option' => 'displayLeftColumn',
                'name' => $this->l('Left column'),
            ],
            [
                'id_option' => 'displayRightColumn',
                'name' => $this->l('Right column'),
            ],
            [
                'id_option' => 'displayFooterProduct',
                'name' => $this->l('Product footer'),
            ],
            [
                'id_option' => 'displayProductAdditionalInfo',
                'name' => $this->l('Product additional info'),
            ],
            [
                'id_option' => 'displayRightColumnProduct',
                'name' => $this->l('Product right column'),
            ],
            [
                'id_option' => 'custom',
                'name' => $this->l('Custom'),
            ],
        ];

        if ($this->getPSVersion() >= 1.7) {
            $all_hooks = [];
            foreach (Hook::getHooks(false, true) as $hook) {
                if (Tools::strpos($hook['name'], 'displayAdmin') === false
                    && Tools::strpos($hook['name'], 'displayBackOffice') === false
                ) {
                    $all_hooks[] = [
                        'id_option' => $hook['name'],
                        'name' => $hook['name'],
                    ];
                }
            }

            if (!$list) {
                $result = [
                    'main' => [
                        'name' => $this->l('Main'),
                        'query' => $hooks,
                    ],
                    'all' => [
                        'name' => $this->l('All'),
                        'query' => $all_hooks,
                    ],
                ];
            } else {
                $result = [];
                foreach ($hooks as $hook) {
                    $result[$hook['id_option']] = $hook['name'];
                }
            }

            $hooks = $result;
        } elseif ($list) {
            $tmp = [];
            foreach ($hooks as $hook) {
                $tmp[$hook['id_option']] = $hook['name'];
            }
            $hooks = $tmp;
        }

        return $hooks;
    }

    protected function getProductHookList()
    {
        return [
            [
                'id_option' => 'displayFooterProduct',
                'name' => $this->l('Product footer'),
            ],
            [
                'id_option' => 'displayProductAdditionalInfo',
                'name' => $this->l('Additional info'),
            ],
            [
                'id_option' => 'displayReassurance',
                'name' => $this->l('Reassurance info'),
            ],
            [
                'id_option' => 'displayProductActions',
                'name' => $this->l('Product actions'),
            ],
            [
                'id_option' => 'displayProductDeliveryTime',
                'name' => $this->l('Delivery info'),
            ],
            [
                'id_option' => 'displayProductButtons',
                'name' => $this->l('Product buttons'),
            ],
            [
                'id_option' => 'displayLeftColumnProduct',
                'name' => $this->l('Extra left'),
            ],
            [
                'id_option' => 'displayRightColumnProduct',
                'name' => $this->l('Extra right'),
            ],
        ];
    }

    protected function renderBlocksList()
    {
        $table = 'prestawp_block';
        $helper = $this->createListHelper($table);
        $helper->actions = ['edit', 'delete'];
        $helper->title = $this->l('Blocks');

        $fields_list = [
            'id_prestawp_block' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'class' => 'fixed-width-xs',
                'search' => false,
                'orderby' => false,
            ],
            'categories' => [
                'title' => $this->l('Categories'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'hook' => [
                'title' => $this->l('Position'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'title' => [
                'title' => $this->l('Title'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'type' => 'status',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
        ];

        // if multishop
        if (Shop::isFeatureActive()) {
            $fields_list['shops'] = [
                'title' => $this->l('Shops'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ];
        }

        $hooks = $this->getBlockHookList(true);
        $content = $this->getBlocksBO($helper->page, $helper->n);
        foreach ($content as &$block) {
            $block_obj = new PSWPBlock($block['id_prestawp_block']);
            $block['categories'] = $block_obj->getPSCategoryNames();
            if (isset($hooks[$block['hook']])) {
                $block['hook'] = $hooks[$block['hook']];
            }

            if (Shop::isFeatureActive()) {
                $shops = PSWPBlock::getShopsStatic($block['id_prestawp_block']);
                $shop_names = [];
                foreach ($shops as $id_shop) {
                    $shop_names[] = $this->getShopName($id_shop);
                }
                $block['shops'] = implode(', ', $shop_names);
            }
        }
        $helper->listTotal = $this->getBlocksListTotal();

        return $helper->generateList($content, $fields_list);
    }

    public function getBlocksBO($page = 1, $n = 20)
    {
        $query =
            'SELECT *
			 FROM `' . _DB_PREFIX_ . 'prestawp_block` b
			 LEFT JOIN `' . _DB_PREFIX_ . 'prestawp_block_lang` bl
			  ON b.`id_prestawp_block` = bl.`id_prestawp_block` AND bl.`id_lang` = ' . (int) $this->context->language->id . '
			 ORDER BY b.`id_prestawp_block`
			 LIMIT ' . (((int) $page - 1) * (int) $n) . ', ' . (int) $n;
        $blocks = Db::getInstance()->executeS($query);

        $hooks = $this->getBlockHookList(true);
        foreach ($blocks as &$block) {
            if (isset($hooks[$block['hook']])) {
                $block['hook'] = $hooks[$block['hook']];
            }
        }

        return $blocks;
    }

    public function getBlocksListTotal()
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(`id_prestawp_block`)
             FROM `' . _DB_PREFIX_ . 'prestawp_block`'
        );
    }

    public function ajaxProcessSaveBlock()
    {
        $id_block = Tools::getValue('id_prestawp_block');
        $block = new PSWPBlock($id_block);
        if ($id_block && !Validate::isLoadedObject($block)) {
            exit('0');
        }

        foreach (PSWPBlock::$definition['fields'] as $field_name => $field_data) {
            if (isset($field_data['lang']) && $field_data['lang']) {
                $block->{$field_name} = [];
                foreach (Language::getLanguages(false) as $lang) {
                    $block->{$field_name}[$lang['id_lang']] = trim(Tools::getValue($field_name . '_' . $lang['id_lang']));
                }
            } elseif (is_array(Tools::getValue($field_name))) {
                $block->$field_name = implode(',', Tools::getValue($field_name));
            } else {
                if ($id_block) {
                    if (Tools::isSubmit($field_name)) {
                        $block->$field_name = trim(Tools::getValue($field_name));
                    }
                } else {
                    $block->$field_name = trim(Tools::getValue($field_name));
                }
            }
        }

        $block->ps_categories = Tools::getValue('ps_categories');

        // Default values
        if (!$id_block) {
            $block->active = 1;
        }

        if (Shop::isFeatureActive()) {
            $block->shops = Tools::getValue('shops');
        }

        // Check for errors
        $field_errors = $block->validateAllFields();
        // Save if no errors
        if (!(is_array($field_errors) && count($field_errors))) {
            if ($block->save()) {
                exit('1');
            }
        }

        // Display errors if any
        if (is_array($field_errors) && count($field_errors)) {
            $html = '';
            foreach ($field_errors as $field_error) {
                $html .= $this->displayError($field_error);
            }
            if (!$id_block && Validate::isLoadedObject($block)) {
                $block->delete(); // delete newly created block if there were any errors
            }
            exit($html);
        }

        exit('0');
    }

    public function ajaxProcessGetBlockList()
    {
        exit($this->renderCustomBlocksForm());
    }

    public function ajaxProcessChangeBlockStatus()
    {
        $id_block = Tools::getValue('id_block');
        $block = new PSWPBlock($id_block);

        $block->active = ($block->active ? 0 : 1);
        $block->save();

        exit('1');
    }

    public function ajaxProcessGetWpPostsOptions()
    {
        $wp_cats = Tools::getValue('cats');

        $params = [
            'categories' => $wp_cats,
        ];

        $result = [];
        $result[] = [
            'id_option' => '',
            'name' => $this->l('All'),
        ];
        $posts = $this->getWPData('posts', 999999999, $params);

        if (is_array($posts) && count($posts)) {
            foreach ($posts as $post) {
                $title = $post['post_title'];
                if (!$title) {
                    $title = Tools::substr($post['post_content'], 0, 50);
                }
                $result[] = [
                    'id_option' => $post['ID'],
                    'name' => $title,
                ];
            }
        }

        exit(json_encode($result));
    }

    protected function getBlockCacheId($name = null)
    {
        if (!$this->cache_lifetime) {
            return md5(mt_rand() . time());
        }

        $cache_array = [];
        $cache_array[] = $this->name;
        if ($name !== null) {
            $cache_array[] = $name;
        }
        $cache_array[] = date('Y-m-d H'); // 1hour cache
        $cache_array[] = Tools::getValue('id_category') . '-' . Tools::getValue('id_product');
        if (Configuration::get('PS_SSL_ENABLED')) {
            $cache_array[] = (int) Tools::usingSecureMode();
        }
        if (Shop::isFeatureActive()) {
            $cache_array[] = (int) $this->context->shop->id;
        }
        if (Group::isFeatureActive() && isset($this->context->customer)) {
            $cache_array[] = (int) Group::getCurrent()->id;
            $cache_array[] = implode('_', Customer::getGroupsStatic($this->context->customer->id));
        }
        if (Language::isMultiLanguageActivated()) {
            $cache_array[] = (int) $this->context->language->id;
        }
        if (method_exists('Currency', 'isMultiCurrencyActivated')) {
            if (Currency::isMultiCurrencyActivated()) {
                $cache_array[] = (int) $this->context->currency->id;
            }
        }
        $cache_array[] = (int) $this->context->country->id;

        return implode('|', $cache_array);
    }

    protected function getWPCategoriesOptionList($id_block = 0, $selected_cats = [])
    {
        $id_block = (int) $id_block;

        $result = [];
        $result[] = [
            'id_option' => '',
            'name' => $this->l('All'),
            'selected' => false,
        ];
        $categories = $this->getWPData('categories', 999999999);

        if (is_array($categories) && count($categories)) {
            if ($id_block) {
                $block = new PSWPBlock($id_block);
                $selected_cats = $block->wp_categories;
            }

            foreach ($categories as $category) {
                $result[] = [
                    'id_option' => $category['term_id'],
                    'name' => $category['name'],
                    'selected' => in_array($category['term_id'], $selected_cats),
                ];
            }

            return $result;
        }

        return $result;
    }

    protected function getWPPostsOptionList($id_block = 0, $type = '', $selected_categories = [], $selected_posts = [])
    {
        $id_block = (int) $id_block;

        $params = [];
        if ($type == 'WP_POSTS_POSTS') {
            $wp_categories = Configuration::get($this->settings_prefix . 'WP_POSTS_CATEGORIES');
            $wp_categories = explode(',', $wp_categories);
            $params['categories'] = $wp_categories;
        } elseif ($id_block) {
            $block = new PSWPBlock($id_block);
            $selected_posts = $block->wp_posts;
            if (count($block->wp_categories)) {
                $params['categories'] = $block->wp_categories;
            }
        } elseif ($selected_categories) {
            $params['categories'] = implode(',', $selected_categories);
        }

        if (!empty($params['categories']) && is_array($params['categories'])) {
            $params['categories'] = array_filter($params['categories']);
        }

        $result = [];
        $result[] = [
            'id_option' => '',
            'name' => $this->l('All'),
            'selected' => false,
        ];
        $posts = $this->getWPData('posts', 999999999, $params);

        if (is_array($posts) && count($posts)) {
            foreach ($posts as $post) {
                $title = $post['post_title'];
                if (!$title) {
                    $title = Tools::substr($post['post_content'], 0, 50);
                }
                $result[] = [
                    'id_option' => $post['ID'],
                    'name' => $title,
                    'selected' => in_array($post['ID'], $selected_posts),
                ];
            }

            return $result;
        }
    }

    public function clearWPCache()
    {
        Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'prestawp_cache`');

        $files = glob(_PS_MODULE_DIR_ . $this->name . '/cache/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file) && Tools::strpos($file, 'index.php') === false) {
                unlink($file); // delete file
            }
        }

        // Sitemap cache:
        $files = glob(_PS_MODULE_DIR_ . $this->name . '/sitemap-*.xml'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                @unlink($file); // delete file
            }
        }
        // RSS cache:
        $files = glob(_PS_MODULE_DIR_ . $this->name . '/rss-*.xml'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                @unlink($file); // delete file
            }
        }
    }

    /**
     * Ajax get products for autocomplete
     */
    public function ajaxProcessGetProducts()
    {
        $query = Tools::getValue('query', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            exit;
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = Tools::strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql =
            'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, p.`cache_default_attribute`
		     FROM `' . _DB_PREFIX_ . 'product` p
             ' . Shop::addSqlAssociation('product', 'p') . '
		     LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON
		      (pl.id_product = p.id_product
              AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		     WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\'
		      OR p.`id_product` = ' . (int) $query . ')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS
              (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        $result = [];
        $result['suggestions'] = [];
        if ($items) {
            foreach ($items as $item) {
                $result['suggestions'][] = [
                    'value' => trim($item['name']) . (!empty($item['reference']) ? ' (' . $this->l('ref:') . ' '
                            . $item['reference'] . ')' : '') . ' | ID ' . (int) $item['id_product'],
                    'data' => $item['id_product'],
                ];

                // add combinations
                if (Tools::getValue('with_combinations')) {
                    $obj = new Product($item['id_product']);
                    $attributes = $obj->getAttributesGroups($this->context->language->id);
                    if (count($attributes)) {
                        $combinations = [];
                        foreach ($attributes as $attribute) {
                            $ipa = $attribute['id_product_attribute'];
                            $combinations[$ipa]['id_product_attribute'] = $ipa;
                            $combinations[$ipa]['reference'] = $attribute['reference'];
                            if (!isset($combinations[$ipa]['attributes'])) {
                                $combinations[$ipa]['attributes'] = '';
                            }
                            $combinations[$ipa]['attributes'] .= $attribute['attribute_name'] . ' - ';
                        }
                        foreach ($combinations as &$combination) {
                            $combination['attributes'] = rtrim($combination['attributes'], ' - ');

                            $result['suggestions'][] = [
                                'value' => '  --- ' .
                                    trim($item['name']) .
                                    ' (' . $combination['attributes'] . ')' .
                                    (!empty($combination['reference']) ? ' (ref: ' . $combination['reference'] . ')'
                                        : (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '')),
                                'data' => $item['id_product'] . '-' . $combination['id_product_attribute'],
                            ];
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * Ajax get categories for autocomplete
     */
    public function ajaxProcessGetCategories()
    {
        $query = Tools::getValue('query', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            exit;
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = Tools::strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        $context = Context::getContext();

        $sql =
            'SELECT `id_category`, `name`
		     FROM `' . _DB_PREFIX_ . 'category_lang` 
		     WHERE
		      `name` LIKE "%' . pSQL($query) . '%" AND `id_lang` = ' . (int) $context->language->id .
             (!empty($excludeIds) ? ' AND `id_category` NOT IN (' . $excludeIds . ') ' : ' ') .
             ' GROUP BY `id_category`';

        $items = Db::getInstance()->executeS($sql);

        $result = [];
        $result['suggestions'] = [];
        if ($items) {
            foreach ($items as $item) {
                $category = new Category($item['id_category'], $context->language->id);
                $parents = $category->getAllParents();
                $full_name_parts = [];
                foreach ($parents as $parent) {
                    if ($parent->id != 1) {
                        $full_name_parts[] = $parent->name;
                    }
                }
                $full_name_parts[] = $category->name;
                $result['suggestions'][] = [
                    'value' => implode(' >> ', $full_name_parts),
                    'data' => $item['id_category'],
                ];
            }
        }

        echo json_encode($result);
    }

    /**
     * Ajax get manufacturers for autocomplete
     */
    public function ajaxProcessGetManufacturers()
    {
        $query = Tools::getValue('query', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            exit;
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = Tools::strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        $sql =
            'SELECT `id_manufacturer`, `name`
		     FROM `' . _DB_PREFIX_ . 'manufacturer` 
		     WHERE
		      `name` LIKE "%' . pSQL($query) . '%" AND `active` = 1' .
            (!empty($excludeIds) ? ' AND `id_manufacturer` NOT IN (' . $excludeIds . ') ' : ' ') .
            ' GROUP BY `id_manufacturer`' .
            ' ORDER BY `name` ASC';

        $items = Db::getInstance()->executeS($sql);

        $result = [];
        $result['suggestions'] = [];
        if ($items) {
            foreach ($items as $item) {
                $result['suggestions'][] = [
                    'value' => $item['name'],
                    'data' => $item['id_manufacturer'],
                ];
            }
        }

        echo json_encode($result);
    }

    public function ajaxProcessGetBlockForm()
    {
        $id_block = Tools::getValue('id_prestawp_block');
        $block_obj = new PSWPBlock($id_block);

        if (Validate::isLoadedObject($block_obj)) {
            exit($this->renderBlockForm(
                $this->l('Edit block'),
                $this->getBlockFormFields($block_obj->id),
                'prestawp_block',
                $block_obj
            ));
        }

        exit('0');
    }

    protected function getRandomProductID()
    {
        $row = Db::getInstance()->getValue('SELECT FLOOR(COUNT(*) * RAND()) FROM `' . _DB_PREFIX_ . 'product`');
        $id = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` LIMIT ' . (int) $row . ', 1');
        if (count($id) && isset($id[0]['id_product'])) {
            return $id[0]['id_product'];
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if ($this->show_posts_product) {
            $id_product = $this->getParamsIdProduct($params);

            $wp_categories = $this->getProductWPData($id_product, 'wp_categories');
            $wp_posts = $this->getProductWPData($id_product, 'wp_posts');

            $wp_input = [
                'wp_category_options' => $this->getWPCategoriesOptionList(0, $wp_categories),
                'wp_post_options' => $this->getWPPostsOptionList(0, '', $wp_categories, $wp_posts),
                'id_item' => '-extra',
                'wp_posts_name' => 'wp_posts',
                'wp_categories_name' => 'wp_categories',
            ];

            if (Validate::isLoadedObject($product = new Product($id_product))) {
                $this->context->smarty->assign([
                    'psv' => $this->getPSVersion(),
                    'module_name' => $this->name,
                    'pswp_id_product' => $id_product,
                    'wp_input' => $wp_input,
                    'post_select_form_tpl' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/_post_select_form.tpl',
                    'pswp_is_product_page' => true,
                ]);

                return $this->display(__FILE__, 'admin_products_extra.tpl');
            }
        }
    }

    public function hookActionProductSave($params)
    {
        if (Tools::isSubmit('prestawp-submit')) {
            $id_product = $this->getParamsIdProduct($params);
            $wp_categories = '';
            if (is_array(Tools::getValue('wp_categories'))) {
                $wp_categories = implode(',', Tools::getValue('wp_categories'));
            }
            $wp_posts = '';
            if (is_array(Tools::getValue('wp_posts'))) {
                $wp_posts = implode(',', Tools::getValue('wp_posts'));
            }

            foreach (Shop::getContextListShopID() as $id_shop) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'prestawp_product`
                     WHERE `id_product` = ' . (int) $id_product . '
                      AND `id_shop` = ' . (int) $id_shop
                );

                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_product`
                     (`id_product`, `id_shop`, `wp_categories`, `wp_posts`)
                     VALUES
                     (' . (int) $id_product . ', ' . (int) $id_shop . ', "' . pSQL($wp_categories) . '", "' . pSQL($wp_posts) . '")'
                );
            }

            $this->clearSmartyCache();
            $this->clearWPCache();
        }
    }

    public function getParamsIdProduct($params)
    {
        // Get id_product
        if (isset($params['id_product']) && $params['id_product'] > 0) {
            $id_product = $params['id_product'];
        } elseif (isset($params['product']) && $params['product']) {
            $product = $params['product'];
            if (is_array($product) && isset($product['id_product'])) {
                $id_product = $product['id_product'];
            } elseif (is_object($product)) {
                $id_product = $product->id;
            } else {
                return false;
            }
        } else {
            $id_product = Tools::getValue('id_product');
        }

        if (!$id_product) {
            return null;
        }

        return $id_product;
    }

    public function getProductWPData($id_product, $type)
    {
        $data = Db::getInstance()->getValue(
            'SELECT `' . pSQL($type) . '` FROM `' . _DB_PREFIX_ . 'prestawp_product`
             WHERE `id_product` = ' . (int) $id_product . '
              AND `id_shop` = ' . (int) $this->context->shop->id
        );

        if ($data) {
            return explode(',', $data);
        }

        return [];
    }

    public function getShopName($id_shop)
    {
        if ($id_shop) {
            if (!isset($this->shop_name_cache[$id_shop])) {
                $shop = new Shop($id_shop, $this->context->language->id);
                $this->shop_name_cache[$id_shop] = $shop->name;

                return $shop->name;
            } else {
                return $this->shop_name_cache[$id_shop];
            }
        }

        return '';
    }

    protected function setOption($name, $value, $html = false)
    {
        if (Shop::isFeatureActive()) {
            // save global values if necessary
            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                Configuration::updateGlobalValue($this->settings_prefix . $name, $value, $html);
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                Configuration::updateValue(
                    $this->settings_prefix . $name,
                    $value,
                    $html,
                    $this->context->shop->id_shop_group
                );
            }

            // save per-shop values
            foreach (Shop::getContextListShopID() as $id_shop) {
                $shop = new Shop($id_shop);
                Configuration::updateValue(
                    $this->settings_prefix . $name,
                    $value,
                    $html,
                    $shop->id_shop_group,
                    $id_shop
                );
            }
        } else {
            Configuration::updateValue($this->settings_prefix . $name, $value, $html);
        }
    }

    public function getBaseUrl($id_shop = null)
    {
        $id_shop = ($id_shop ? $id_shop : $this->context->shop->id);
        $url = $this->getShopDomain(true, false, $id_shop);
        $url_ssl = $this->getShopDomainSsl(true, false, $id_shop);

        if (Configuration::get('PS_SSL_ENABLED', null, null, $id_shop) && $url_ssl) {
            $url = $url_ssl;
        }

        $url = rtrim($url, '/') . '/';

        $main_shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        if (Validate::isLoadedObject($main_shop)) {
            $url .= trim($main_shop->physical_uri, '/') . '/';
        }

        $url = rtrim($url, '/');

        return $url;
    }

    public function getShopDomain($http = false, $entities = false, $id_shop = null)
    {
        $id_shop = ($id_shop ? $id_shop : $this->context->shop->id);

        if (!$domain = ShopUrl::getMainShopDomain($id_shop)) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = 'http://' . $domain;
        }

        return $domain;
    }

    public function getShopDomainSsl($http = false, $entities = false, $id_shop = null)
    {
        $id_shop = ($id_shop ? $id_shop : $this->context->shop->id);

        if (!$domain = ShopUrl::getMainShopDomainSSL($id_shop)) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = (Configuration::get('PS_SSL_ENABLED', null, null, $id_shop) ? 'https://' : 'http://') . $domain;
        }

        return $domain;
    }

    public function stripTags($html)
    {
        $html = str_replace('<p>', '', $html);
        $html = str_replace('</p>', '<br>', $html);
        $html = strip_tags($html, '<br>');
        $html = $this->br2nl($html);
        $html = preg_replace("/[\r\n]+/", "\n", $html);
        $html = rtrim($html);

        return $html;
    }

    protected function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }

    public function getPostsFront($p, $n, $id_lang = null, $no_content = false, $categories = [], $q = '')
    {
        if ($p < 1) {
            $p = 1;
        }
        $urls = $this->url_rewrite_page;
        if (!($this->enable_posts_page && is_array($urls) && array_filter($urls))) {
            return [];
        }

        // Check if there are any selected WP categories or posts
        $wp_params = [];
        foreach (['posts' => 'WP_POSTS_POSTS_PAGE', 'categories' => 'WP_POSTS_CATEGORIES_PAGE'] as $type => $option) {
            $option_value = Configuration::get($this->settings_prefix . $option);
            if ($option_value) {
                $option_value = explode(',', $option_value);
                $wp_params[$type] = $option_value;
            }
        }
        if ($categories) {
            $wp_params['categories'] = $categories;
        }
        if ($q) {
            $wp_params['search'] = $q;
        }
        $wp_params['start'] = ((int) $p - 1) * (int) $n;

        if ($id_lang) {
            $wp_params['id_lang'] = $id_lang;
        }

        // Get posts from WP
        $posts = $this->getWPData('posts', $n, $wp_params, true, $no_content);

        if (!$posts) {
            return [];
        }

        // remove html tags etc
        if ($this->show_featured_image_page || $this->posts_strip_tags_page) {
            foreach ($posts as &$post) {
                if (isset($post['main_content'])) {
                    $post['main_content'] = $this->stripTags($post['main_content']);
                }
            }
        }

        return $posts;
    }

    public function getTotalNumberOfPosts($categories = [], $q = '')
    {
        $wp_params = [];
        foreach (['posts' => 'WP_POSTS_POSTS_PAGE', 'categories' => 'WP_POSTS_CATEGORIES_PAGE'] as $type => $option) {
            $option_value = Configuration::get($this->settings_prefix . $option);
            if ($option_value) {
                $option_value = explode(',', $option_value);
                $wp_params[$type] = $option_value;
            }
        }
        if ($categories) {
            $wp_params['categories'] = $categories;
        }
        if ($q) {
            $wp_params['search'] = $q;
        }

        return $this->getWPData('post_count', 1, $wp_params);
    }

    public function getModuleLink($module, $controller = 'default', array $params = [], $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            if (Tools::getValue('isolang')) {
                $iso = Tools::getValue('isolang');
                $id_lang = Language::getIdByIso($iso);
            }
            // if still no id lang
            $id_lang = ($id_lang ?: Tools::getValue('id_lang'));
            $id_lang = ($id_lang ?: Context::getContext()->language->id);
        }

        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $controller .= '-' . (int) $id_lang;
        }

        return $this->context->link->getModuleLink(
            $module,
            $controller,
            $params,
            $ssl,
            $id_lang,
            $id_shop,
            $relative_protocol
        );
    }

    public function getLangOptionValue($option_name, $id_lang = null, $get_default_if_nothing = false)
    {
        if ($id_lang === null) {
            $id_lang = $this->context->language->id;
        }

        if (!property_exists($this, $option_name)) {
            return '';
        }

        if (!is_array($this->{$option_name})) {
            return $this->{$option_name};
        }

        if (!is_array($this->{$option_name})) {
            $this->loadSettings();
        }

        if (is_array($this->{$option_name})
            && isset($this->{$option_name}[$id_lang])
            && $this->{$option_name}[$id_lang]
        ) {
            return $this->{$option_name}[$id_lang];
        }

        // if no value for selected language, get value for default language or any value
        if ($get_default_if_nothing) {
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            if (isset($this->{$option_name}[$id_lang_default])) {
                return $this->{$option_name}[$id_lang_default];
            } else {
                foreach ($this->{$option_name} as $lang_value) {
                    if ($lang_value) {
                        return $lang_value;
                    }
                }
            }
        }

        return '';
    }

    public function getDefaultProductImageType()
    {
        $image_type = null;
        if (method_exists('ImageType', 'getFormattedName')) {
            $image_type = ImageType::getFormattedName('home');
        } elseif (method_exists('ImageType', 'getFormatedName')) {
            $image_type = ImageType::getFormatedName('home');
        }
        if (!$image_type || !ImageType::typeAlreadyExists($image_type)) {
            $image_type = Db::getInstance()->getValue(
                'SELECT `name`
                 FROM `' . _DB_PREFIX_ . 'image_type`
                 WHERE `products` = 1
                  AND `width` >= 250
                  AND `height` >= `width`
                 ORDER BY ABS(`width` - 250) ASC'
            );
        }

        return $image_type;
    }

    public function hookActionClearCache($params)
    {
        $this->clearWPCache();
    }

    public function hookActionClearCompileCache($params)
    {
        $this->clearWPCache();
    }

    public function removeBom($str = '')
    {
        if (substr($str, 0, 3) == pack('CCC', 0xEF, 0xBB, 0xBF)) {
            $str = substr($str, 3);
        }

        return $str;
    }

    protected function renderFixURLLink()
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $link = 'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . $token . '&fix_url=1';

        $this->context->smarty->assign([
            'pswp_link' => $link,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/fix_url_link.tpl');
    }

    protected function checkSecureKey()
    {
        $securekey_ok = $this->getWPData('test', 10, [], false);

        if (isset($securekey_ok['ok']) && $securekey_ok['ok'] == 1) {
            return true;
        }

        return false;
    }

    protected function tryAnotherWPUrl($new_url, $old_url)
    {
        $this->changeMainWPUrl($new_url);

        if ($this->checkSecureKey()) {
            return true;
        } else {
            $this->changeMainWPUrl($old_url);

            return false;
        }
    }

    protected function changeMainWPUrl($new_url)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');

        $this->wppath[$id_lang_default] = $new_url;
        $this->setOption('WPPATH', $this->wppath);
        $this->loadSettings();
    }

    protected function isAnySliderActive()
    {
        if ($this->carousel || $this->carousel_product) {
            return true;
        }

        $check_blocks = Db::getInstance()->getValue(
            'SELECT `carousel` FROM `' . _DB_PREFIX_ . 'prestawp_block`
             WHERE `active` = 1 AND `carousel` = 1'
        );

        if ($check_blocks) {
            return true;
        }

        return false;
    }

    protected function isAnyMasonry()
    {
        if ($this->masonry || $this->masonry_page || $this->masonry_product) {
            return true;
        }

        $check_blocks = Db::getInstance()->getValue(
            'SELECT `masonry` FROM `' . _DB_PREFIX_ . 'prestawp_block`
             WHERE `active` = 1'
        );

        if ($check_blocks) {
            return true;
        }

        return false;
    }

    protected function renderSettingsSeparator($text, $render)
    {
        if (!$render) {
            return '';
        }

        $this->context->smarty->assign([
            'pswp_sep_name' => $text,
        ]);

        return $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/separator.tpl'
        );
    }

    protected function getThemeOptions()
    {
        $options = [
            'query' => [
                [
                    'id_option' => '',
                    'name' => $this->l('Default'),
                ],
            ],
            'id' => 'id_option',
            'name' => 'name',
        ];

        foreach ($this->getThemes() as $theme) {
            $options['query'][] = [
                'id_option' => $theme['file'],
                'name' => $theme['name'],
            ];
        }

        return $options;
    }

    protected function getThemes()
    {
        $themes = [];

        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/css/themes/')) {
            $themes_files = scandir(_PS_MODULE_DIR_ . $this->name . '/views/css/themes/');
            natsort($themes_files);
            foreach ($themes_files as $file) {
                if (Tools::strpos($file, '.css') !== false) {
                    $pos = Tools::strpos($file, '.css');
                    $themes[] = ['file' => $file, 'name' => Tools::substr($file, 0, $pos)];
                }
            }
        }

        return $themes;
    }

    public function getCurrentThemeName()
    {
        return str_replace('.css', '', $this->theme);
    }

    public function hookGSitemapAppendUrls($params)
    {
        if (!$this->enable_posts_page) {
            return [];
        }

        $links = [];
        $lang = (isset($params['lang']) ? $params['lang'] : null);

        $links[] = [
            'link' => $this->getModuleLink($this->name, 'list', [], null, $lang['id_lang']),
            'page' => 'cms',
        ];

        if (!$this->view_in_ps) {
            return $links;
        }

        $posts = $this->getPostsFront(1, 99999999, $lang['id_lang']);
        foreach ($posts as $item) {
            $links[] = [
                'link' => $this->getModuleLink(
                    $this->name,
                    'post',
                    ['post_name' => $item['post_name']],
                    null,
                    $lang['id_lang']
                ),
                'page' => 'cms',
            ];
        }

        return $links;
    }

    public function smartyModifierImplode($separator, $array)
    {
        return implode($separator, $array);
    }

    protected function checkWpPluginVersion()
    {
        if (!$this->getWPPath()) {
            return true;
        }

        $plugin_version = $this->getWPData('plugin_version');

        if (!$plugin_version || ($plugin_version && version_compare($this->version, $plugin_version, '<='))) {
            return true;
        }

        // check for exceptions
        $exceptions = ['1.9.1' => '1.9.0'];
        if ($plugin_version && isset($exceptions[$this->version]) && $exceptions[$this->version] == $plugin_version) {
            return true;
        }

        return false;
    }

    protected function renderWpPluginUpdateInfo()
    {
        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'pswp_path' => $this->_path,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/wp_update.tpl');
    }

    public function updateControllersMetas()
    {
        return $this->installControllers();
    }
}
