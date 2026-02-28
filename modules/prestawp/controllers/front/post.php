<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2018 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPPostModuleFrontController extends ModuleFrontController
{
    /**
     * @var PrestaWP
     */
    public $module;
    public $display_column_left = false;
    public $display_column_right = false;
    public $php_self = '';
    public $post;

    public function __construct()
    {
        parent::__construct();

        if (!headers_sent() && Configuration::get('PSWP_DISABLE_INDEXATION')) {
            header('X-Robots-Tag: noindex');
        }
    }

    public function init()
    {
        $this->module->slashRedirect();
        $post = $this->loadPost();

        // redirect to default glossary from default controller url
        if (!is_array($post)) {
            $controller = Controller::getController('PageNotFoundController');
            $controller->php_self = 'post';
            $controller->run();
            exit;
        }
        $this->langRedirect();
        $this->prettyUrlRedirect();

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $post = $this->loadPost();
        if (!$post) {
            return false;
        }

        $meta_title = strip_tags($post['title']);

        $this->parseShortcodes($post['post_content']);

        if ($post['post_password']) {
            if ($post['post_password'] == Tools::getValue('password')) {
                $post['post_password'] = '';
            }
        }

        $this->context->smarty->assign([
            'pswp_post' => $post,
            'psv' => $this->module->getPSVersion(),
            'psvd' => $this->module->getPSVersion(true),
            'meta_title' => $meta_title,
            'meta_description' => Tools::substr($post['main_content'], 0, 160),
            'meta_keywords' => '',
            'navigationPipe' => Configuration::get('PS_NAVIGATION_PIPE'),
            'pswp' => $this->module,
            'pswp_prev_next_posts' => $this->getNextPrevPosts(),
            'pswp_psv' => $this->module->getPSVersion(),
        ]);

        if ($this->module->getPSVersion() <= 1.6) {
            $this->setTemplate('post.tpl');
        } else {
            $this->setTemplate('module:prestawp/views/templates/front/post17.tpl');
        }
    }

    protected function loadPost()
    {
        $post_name = Tools::getValue('post_name');
        if (!$post_name) {
            return null;
        }

        $post = null;
        $posts = $this->module->getWPData('posts', 1, ['post_name' => $post_name]);
        if ($posts) {
            $post_tmp = array_shift($posts);

            if (is_array($post_tmp) && isset($post_tmp['id'])) {
                $post = $post_tmp;
            }
        }

        if (!$post) {
            return null;
        }

        $this->post = $post;

        return $post;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if ($this->module->enable_posts_page) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Posts', 'Posts'),
                'url' => $this->module->getModuleLink($this->module->name, 'list'),
            ];
        }

        $breadcrumb['links'][] = [
            'title' => $this->post['title'],
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function langRedirect()
    {
        if (Language::isMultiLanguageActivated()) {
            // Prevent duplicate URLs by redirecting to link with lang code
            if (!Tools::getValue('id_lang') && $this->context->language->id) {
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
            $url =
                $this->module->getModuleLink($this->module->name, 'post', ['post_name' => $this->post['post_name']]);

            // if it's incorrect default url:
            if (Tools::strpos($ruri, 'module/prestawp/') !== false) {
                if (Tools::strpos($url, 'module/prestwp/') === false) {
                    Tools::redirect($url);
                }
            }

            // if current url is not for current language and resulting url is correct:
            if (Tools::strpos($ruri, $url_rewrite) === false && Tools::strpos($url, $url_rewrite) !== false) {
                Tools::redirect($url);
            }
        }
    }

    protected function getPSVersion()
    {
        return $this->module->getPSVersion();
    }

    public function getCanonicalURL()
    {
        $post = $this->loadPost();
        if ($post && isset($post['wp_url'])) {
            if ($this->module->disable_indexation && !empty($post['wp_url'])) {
                return $post['wp_url'];
            } elseif (!$this->module->disable_indexation && !empty($post['url'])) {
                return $post['url'];
            }
        }

        return false;
    }

    protected function getAlternativeLangsUrl()
    {
        if (!$this->module->show_alt_langs_url) {
            return [];
        }

        $alternativeLangs = [];

        $post = $this->loadPost();
        if ($post && is_array($post) && isset($post['post_name'])) {
            $languages = Language::getLanguages(true, $this->context->shop->id);

            if (count($languages) < 2) {
                // No need to display alternative lang if there is only one enabled
                return $alternativeLangs;
            }

            foreach ($languages as $lang) {
                $alternativeLangs[$lang['language_code']] = $this->module->getModuleLink(
                    $this->module->name,
                    'post',
                    ['post_name' => $post['post_name']],
                    null,
                    $lang['id_lang']
                );
            }

            $alternativeLangs['x-default'] = $this->module->getModuleLink(
                $this->module->name,
                'post',
                ['post_name' => $post['post_name']],
                null,
                Configuration::get('PS_LANG_DEFAULT')
            );
        }

        return $alternativeLangs;
    }

    protected function parseShortcodes(&$text)
    {
        if (Tools::strpos($text, '[pswp_') === false) {
            return true;
        }

        $shortcodes = ['products', 'addtocart'];

        foreach ($shortcodes as $shortcode) {
            $text = preg_replace_callback('/\[pswp_' . $shortcode . '( .+)+\]/mi', function ($matches) use ($shortcode) {
                $shortcode_atts = [];
                if (isset($matches[1]) && $matches[1]) {
                    $tmp_elem = new SimpleXMLElement("<element $matches[1] />");
                    foreach ($tmp_elem->attributes() as $name => $attr) {
                        $shortcode_atts[(string) $name] = (string) $attr;
                    }
                }
                if ($shortcode_atts) {
                    return $this->processShortcode($shortcode, $shortcode_atts);
                }

                return $matches[0];
            }, $text);
        }
    }

    protected function processShortcode($name, $atts)
    {
        if (is_callable([$this, 'shortcode' . $name])) {
            return call_user_func([$this, 'shortcode' . $name], $atts);
        }
    }

    protected function shortcodeProducts($atts)
    {
        require_once _PS_MODULE_DIR_ . 'prestawp/controllers/front/products.php';
        $prod_controller = new PrestaWpProductsModuleFrontController();
        $prod_controller->display_type = 'api';

        $product_ids = (isset($atts['ids']) && $atts['ids'] ? $atts['ids'] : '');
        $category_ids = (isset($atts['category_ids']) && $atts['category_ids'] ? $atts['category_ids'] : '');
        $brand_ids = (isset($atts['brand_ids']) && $atts['brand_ids'] ? $atts['brand_ids'] : '');
        if ($product_ids) {
            $product_ids = implode(',', array_map('intval', explode(',', $product_ids))); // just in case
            $products = $prod_controller->getProductsFront($product_ids);
        } elseif ($category_ids) {
            $category_ids = implode(',', array_map('intval', explode(',', $category_ids))); // just in case
            $limit = (isset($atts['limit']) && $atts['limit'] ? $atts['limit'] : '');
            $order_by = (isset($atts['order_by']) && $atts['order_by'] ? $atts['order_by'] : '');
            $order_way = (isset($atts['order_way']) && $atts['order_way'] ? $atts['order_way'] : '');
            $products = $prod_controller->getProductsFront('', $category_ids, $order_by, $order_way, $limit);
        } elseif ($brand_ids) {
            $brand_ids = implode(',', array_map('intval', explode(',', $brand_ids))); // just in case
            $limit = (isset($atts['limit']) && $atts['limit'] ? $atts['limit'] : '');
            $order_by = (isset($atts['order_by']) && $atts['order_by'] ? $atts['order_by'] : '');
            $order_way = (isset($atts['order_way']) && $atts['order_way'] ? $atts['order_way'] : '');
            $products = $prod_controller->getProductsFront('', $category_ids, $order_by, $order_way, $limit, $brand_ids);
        } else {
            return false;
        }

        $image_type = $this->module->getDefaultProductImageType();

        foreach ($products as &$product) {
            $product['img_url'] =
                $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], $image_type);
        }

        $this->context->smarty->assign([
            'pswp_products' => $products,
            'pswp_cart_link' => $this->context->link->getPageLink('cart'),
            'pswp_psv' => $this->module->getPSVersion(),
            'pswp_tpl_dir' => _PS_THEME_DIR_,
        ]);
        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/shortcode_products.tpl'
        );

        return $html;
    }

    protected function shortcodeAddToCart($atts)
    {
        if (!isset($atts['id_product']) || !$atts['id_product']) {
            return false;
        }

        $id_product = $atts['id_product'];
        $id_attribute = isset($atts['id_attribute']) ? $atts['id_attribute'] : 0;
        $id_attribute = isset($atts['id_product_attribute']) ? $atts['id_product_attribute'] : $id_attribute;
        $qty = isset($atts['quantity']) ? $atts['quantity'] : 1;
        $add_to_cart_text = isset($atts['text']) ? $atts['text'] : $this->module->l('Add to cart', 'post');
        $align = (isset($atts['align']) ? $atts['align'] : false);

        $this->context->smarty->assign([
            'pswp_id_product' => $id_product,
            'pswp_id_attribute' => $id_attribute,
            'pswp_qty' => $qty,
            'pswp_add_to_cart_text' => $add_to_cart_text,
            'pswp_align' => $align,
            'pswp_cart_link' => $this->context->link->getPageLink('cart'),
            'pswp_token' => Tools::getToken(false),
        ]);
        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/shortcode_add_to_cart.tpl'
        );

        return $html;
    }

    public function setMedia()
    {
        parent::setMedia();

        if ($this->getPSVersion() >= 1.7) {
            $this->registerStylesheet(
                'pswp-products',
                $this->module->getModulePath() . 'views/css/products.css',
                ['media' => 'all', 'priority' => 100]
            );
            $this->registerStylesheet(
                'pswp-block-library',
                $this->module->getModulePath() . 'views/css/block-library.css',
                ['media' => 'all', 'priority' => 100]
            );
        } else {
            $this->context->controller->addCSS([
                _THEME_CSS_DIR_ . 'category.css' => 'all',
                _THEME_CSS_DIR_ . 'product_list.css' => 'all',
                $this->module->getModulePath() . 'views/css/products.css' => 'all',
                $this->module->getModulePath() . 'views/css/block-library.css' => 'all',
            ]);
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('ajax')) {
            if (Tools::getValue('submitComment')) {
                if (Tools::getValue('token') != Tools::getToken(false)) {
                    exit($this->module->l('Invalid token', 'post'));
                }

                $reload_comments = 0;

                if (!Tools::getValue('name')) {
                    $this->errors[] = $this->module->l('Field Name is required.', 'post');
                }
                if (!Tools::getValue('comment')) {
                    $this->errors[] = $this->module->l('Field Comment is required.', 'post');
                }
                if (!Tools::getValue('email')) {
                    $this->errors[] = $this->module->l('Field E-mail is required.', 'post');
                }

                if (!$this->errors) {
                    $comment_data = [
                        'request_type' => 'add_comment',
                        'comment_post_ID' => $this->post['ID'],
                        'comment_author' => Tools::getValue('name'),
                        'comment_author_email' => Tools::getValue('email'),
                        'comment_content' => Tools::getValue('comment'),
                        'comment_parent' => Tools::getValue('id_parent'),
                    ];

                    $result = $this->module->postDataToWP($comment_data);

                    if (is_array($result) && !empty($result['result']['errors'])) {
                        foreach ($result['result']['errors'] as $error) {
                            $this->errors[] = $error[0];
                        }
                    }

                    if (!$this->errors) {
                        $this->context->smarty->assign([
                            'comment_sent' => true,
                        ]);

                        $reload_comments = 1;
                    }
                }

                $this->context->smarty->assign([
                    'pswp_errors' => $this->errors,
                ]);
                $html = $this->renderCommentForm();
                $response = ['reload' => $reload_comments, 'html' => $html];
                exit(json_encode($response));
            }
        }
    }

    public function displayAjax()
    {
        if (Tools::isSubmit('ajax')) {
            if (Tools::getValue('token') != Tools::getToken(false)) {
                exit($this->module->l('Invalid token', 'post'));
            }

            if (Tools::getValue('action') == 'getCommentForm') {
                $html = $this->renderCommentForm();
                exit($html);
            }

            if (Tools::getValue('action') == 'getCommentList') {
                $html = $this->renderCommentList();
                exit($html);
            }

            exit('0');
        }
    }

    protected function renderCommentForm()
    {
        $heading = Tools::getValue('reply_to');
        $heading = ($heading ? $heading : $this->module->l('Leave a comment', 'post'));

        $this->context->smarty->assign([
            'pswp' => $this->module,
            'pswp_token' => Tools::getToken(false),
            'pswp_heading' => $heading,
            'pswp_id_parent' => Tools::getValue('id_parent'),
            'pswp_reply_to' => Tools::getValue('reply_to'),
            'pswp_id_module' => $this->module->id,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/comment_form.tpl'
        );
    }

    protected function renderCommentList()
    {
        $this->context->smarty->assign([
            'pswp' => $this->module,
            'post' => $this->post,
            'pswp_comments' => $this->post['comments'],
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/_comment_list.tpl'
        );
    }

    protected function getNextPrevPosts()
    {
        if (!$this->post) {
            return [];
        }

        // get all posts
        $posts = $this->module->getPostsFront(1, 99999999, null, true);

        $prev = null;
        $next = null;
        foreach ($posts as $key => $post) {
            // find the current post in the post list
            if ($post['ID'] == $this->post['ID']) {
                // try to get the next post
                if (!empty($posts[$key + 1])) {
                    $next = $posts[$key + 1];
                }
                break;
            } else {
                // store the prev post
                $prev = $post;
            }
        }

        return ['prev' => $prev, 'next' => $next];
    }
}
