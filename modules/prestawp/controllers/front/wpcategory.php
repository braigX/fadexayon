<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2023 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPWPCategoryModuleFrontController extends ModuleFrontController
{
    /**
     * @var PrestaWP
     */
    public $module;
    public $display_column_left = false;
    public $display_column_right = false;
    public $php_self = '';
    public $n = 10;
    public $category;

    public function init()
    {
        $this->module->slashRedirect();

        $category = $this->loadCategory();
        // 404 redirect
        if (!is_array($category)) {
            $controller = Controller::getController('PageNotFoundController');
            $controller->php_self = 'post';
            $controller->run();
            exit;
        }

        $this->langRedirect();
        $this->prettyUrlRedirect();

        if (!$this->module->enable_posts_page) {
            $controller = Controller::getController('PageNotFoundController');
            $controller->php_self = 'post';
            $controller->run();
            exit;
        }

        // redirect to the correct url from default controller url
        if (Tools::strpos($_SERVER['REQUEST_URI'], 'pswp-category') !== false) {
            $url = $this->module->getModuleLink($this->module->name, 'list');
            Tools::redirect($url);
        }

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $this->module->loadSettings();

        $q = Tools::getValue('q', '');
        $p = (int) Tools::getValue('p', 1);
        $n = (int) ($this->module->posts_per_page ? $this->module->posts_per_page : $this->n);
        if (!$n) {
            $n = 10;
        }

        $meta_title = $this->category['name'];
        $blog_title = $this->module->getLangOptionValue('meta_title_page');
        if ($blog_title) {
            $meta_title .= ' - ' . $blog_title;
        }

        $page_title = $this->module->l('Category:', 'category') . ' ' . $this->category['name'];

        $desc = $this->category['description'];

        $this->context->smarty->assign([
            'pswp_top_text' => $desc,
            'psv' => $this->module->getPSVersion(),
            'psvd' => $this->module->getPSVersion(true),
            'pswp_page' => $p,
            'pswp_page_title' => $page_title,
            'meta_title' => $meta_title,
            'meta_description' => '',
            'meta_keywords' => '',
            'pswp' => $this->module,
        ]);

        $total_pages = 1;
        $total_posts = $this->module->getTotalNumberOfPosts($this->category['cat_ID']);
        if (is_numeric($total_posts)) {
            $total_pages = ceil($total_posts / $n);
        }

        $posts = $this->module->getPostsFront($p, $n, null, false, [$this->category['cat_ID']]);
        $this->context->smarty->assign([
            'pswp_total_pages' => $total_pages,
            'pswp_posts' => $posts,
            'q' => $q,
        ]);

        // Assign smarty data
        $this->context->smarty->assign([
            'wp_path' => $this->module->getWPPath(),
            'show_footer' => $this->module->show_article_footer_page,
            'show_full' => $this->module->show_full_posts_page,
            'show_featured_images' => $this->module->show_featured_image_page,
            'pswp_masonry' => $this->module->masonry_page,
            'pswp_title_color' => $this->module->title_color_page,
            'pswp_title_bg_color' => $this->module->title_bg_color_page,
            'pswp_show_preview_no_img' => $this->module->show_preview_no_img_page,
            'pswp_blank' => $this->module->open_blank ? ' target="_blank" ' : '',
            'grid_columns' => $this->module->grid_columns_page,
            'strip_tags' => $this->module->posts_strip_tags_page,
            'psv' => $this->module->getPSVersion(),
            'psvwd' => $this->module->getPSVersion(true),
            'pswp_show_preview' => $this->module->show_preview_page,
            'pswp_theme' => $this->module->getCurrentThemeName(),
            'pswp_module' => $this->module,
        ]);

        if ($this->module->getPSVersion() <= 1.6) {
            $this->setTemplate('category.tpl');
        } else {
            $this->setTemplate('module:prestawp/views/templates/front/category17.tpl');
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->module->l('Posts', 'category'),
            'url' => $this->module->getModuleLink($this->module->name, 'list'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->category['name'],
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function langRedirect()
    {
        if (Language::isMultiLanguageActivated()) {
            if (!Tools::getValue('id_lang') && $this->context->language->id) {
                // Prevent duplicate URLs by redirecting to link with lang code
                $request_uri = $_SERVER['REQUEST_URI'];
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
                $iso = Language::getIsoById($this->context->language->id);

                $url = 'http' . ($force_ssl ? 's' : '') . "://$_SERVER[HTTP_HOST]/" . $iso . $request_uri;
                Tools::redirect($url);
            }
        }
    }

    public function prettyUrlRedirect()
    {
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $ruri = $_SERVER['REQUEST_URI'];
            $url_rewrite = $this->module->getLangOptionValue('url_rewrite_page', null, true);
            $url = $this->module->getModuleLink($this->module->name, 'list'); // todo

            // if it's incorrect default url:
            if (Tools::strpos($ruri, 'module/prestawp/') !== false) {
                if (Tools::strpos($url, 'module/prestawp/') === false) {
                    Tools::redirect($url);
                }
            }

            // if current url is not for current language and resulting url is correct:
            if (Tools::strpos($ruri, $url_rewrite) === false && Tools::strpos($url, $url_rewrite) !== false) {
                Tools::redirect($url);
            }
            // if current url is not for current language and resulting url is correct because of wrong URL keyword:
            $category_url =
                Configuration::get($this->module->settings_prefix . 'CATEGORY_URL', $this->context->language->id);
            if ($category_url
                && Tools::strpos($ruri, '/' . $category_url . '/') === false) {
                $canonical_url = $this->module->getModuleLink($this->module->name, 'wpcategory', ['category_name' => Tools::getValue('category_name')]);
                Tools::redirect($canonical_url);
            }
        }
    }

    protected function loadCategory()
    {
        $category_name = Tools::getValue('category_name');
        if (!$category_name) {
            return null;
        }

        // It's not optimal, but it's more reliable and won't require updating the WP plugin:
        $categories = $this->module->getWPData('categories', 999);

        if ($categories) {
            foreach ($categories as $category) {
                if (Tools::strtolower($category['slug']) == Tools::strtolower(urlencode($category_name))) {
                    $this->category = $category;

                    return $category;
                }
            }
        }

        return null;
    }
}
