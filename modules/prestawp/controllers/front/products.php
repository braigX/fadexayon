<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2024 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPProductsModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $php_self = '';

    protected $ids;
    protected $category_ids;
    protected $manufacturer_ids;
    protected $order_by;
    protected $order_way;
    protected $limit;
    public $display_type;

    public function __construct()
    {
        parent::__construct();

        if ($this->module->getPSVersion() >= 1.7 && class_exists('ProductListingFrontControllerCore')) {
            require_once _PS_MODULE_DIR_ . 'prestawp/classes/PswpProductSearchProvider.php';
            require_once _PS_MODULE_DIR_ . 'prestawp/classes/PswpProductListingFrontController.php';
        }

        if (!headers_sent()) {
            header('X-Robots-Tag: noindex');
        }
    }

    public function init()
    {
        // init the query vars
        $this->ids = Tools::getValue('ids');
        $this->category_ids = Tools::getValue('category_ids');
        $this->manufacturer_ids = Tools::getValue('brand_ids');
        $this->order_by = Tools::getValue('order_by', 'position');
        $this->order_way = Tools::getValue('order_way', 'ASC');
        $this->limit = Tools::getValue('limit');
        $this->display_type = Tools::getValue('type');

        // load the result from cache if available
        $cache_lifetime = $this->module->cache_lifetime;
        $cache_id = $this->getCacheId();
        if ($cache_lifetime && PSWPCache::isStored($cache_id, $cache_lifetime)) {
            $result = PSWPCache::get($cache_id, $cache_lifetime);

            exit($result);
        }

        // if display type is API, set the necessary cookie values
        if (Tools::getValue('type') == 'api') {
            $id_currency = $this->getIdCurrency();
            $cookie = Context::getContext()->cookie;
            $currency = Currency::getCurrencyInstance((int) $id_currency);
            if (is_object($currency) && $currency->id && !$currency->deleted && $currency->isAssociatedToShop()) {
                $cookie->id_currency = (int) $currency->id;
            }
        }

        parent::init();
    }

    public function initContent()
    {
        $this->module->slashRedirect();
        parent::initContent();

        if ($this->ids) {
            $ids = implode(',', array_map('intval', explode(',', $this->ids))); // just in case
            $products = $this->getProductsFront($ids);
        } elseif ($this->category_ids) {
            $category_ids = implode(',', array_map('intval', explode(',', $this->category_ids))); // just in case
            $products = $this->getProductsFront('', $category_ids, $this->order_by, $this->order_way);
        } elseif ($this->manufacturer_ids) {
            $manufacturer_ids = implode(',', array_map('intval', explode(',', $this->manufacturer_ids))); // just in case
            $products = $this->getProductsFront('', $this->category_ids, $this->order_by, $this->order_way, $this->limit, $manufacturer_ids);
        } else {
            exit;
        }

        if ($this->display_type == 'api') {
            $html = '';

            if (Tools::getValue('securekey') == $this->module->securekey) {
                $image_type = $this->module->getDefaultProductImageType();

                foreach ($products as &$product) {
                    $product['img_url'] =
                        $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], $image_type);
                }

                $this->context->smarty->assign([
                    'pswp_products' => $products,
                    'pswp_cart_link' => $this->context->link->getPageLink('cart'),
                    'pswp_limit_mobile' => Tools::getValue('limit_mobile'),
                ]);

                $tpl_file = $this->module->name . '/views/templates/front/api_products.tpl';
                $tpl_file = (file_exists(_PS_THEME_DIR_ . 'modules/' . $tpl_file)
                    ? _PS_THEME_DIR_ . 'modules/' . $tpl_file : _PS_MODULE_DIR_ . $tpl_file);
                $html = $this->context->smarty->fetch($tpl_file);
            }

            // will be cached on the WP side
            exit($html);
        } else {
            $wrp = Configuration::get($this->module->settings_prefix . 'PRODUCT_LIST_WRP');
            $wrps = [];
            if ($wrp) {
                $parts = explode('>', trim($wrp));
                foreach ($parts as $part) {
                    $selectors = preg_split('/([.#]+[^.#]+)/', trim($part), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    if (is_array($selectors) && $selectors) {
                        $lvl = [];
                        $lvl['id'] = '';
                        $lvl['classes'] = '';
                        foreach ($selectors as $selector) {
                            if ($selector[0] == '#') {
                                $lvl['id'] = trim($selector, '#');
                            } elseif ($selector[0] == '.') {
                                $lvl['classes'] .= trim($selector, '.') . ' ';
                            }
                        }
                        $wrps[] = $lvl;
                    }
                }
            }

            $this->context->smarty->assign([
                'pswp_products' => $products,
                'tpl_dir' => _PS_THEME_DIR_,
                'pswp_wrps' => $wrps,
                'pswp_limit_mobile' => Tools::getValue('limit_mobile'),
            ]);

            if ($this->module->getPSVersion() >= 1.7) {
                $listing_controller = new PswpProductListingFrontController();
                $listing_controller->custom_total = count($products);
                $listing_controller->custom_products = $products;
                $listing = $listing_controller->getProductListing('');
                $this->context->smarty->assign([
                    'listing' => $listing,
                ]);

                $this->setTemplate('module:prestawp/views/templates/front/products17.tpl');
            } else {
                $this->setTemplate('products.tpl');
            }
        }
    }

    public function smartyOutputContent($content)
    {
        // get the page render result
        ob_start();
        parent::smartyOutputContent($content);
        $html = ob_get_clean();
        $html = trim($html);

        // cache the result
        $cache_id = $this->getCacheId();
        $cache_lifetime = $this->module->cache_lifetime;
        if ($cache_lifetime) {
            PSWPCache::set($cache_id, $html);
        }

        echo $html;
    }

    protected function getCacheId()
    {
        $data = [
            $this->ids,
            $this->category_ids,
            $this->manufacturer_ids,
            $this->order_by,
            $this->order_way,
            $this->limit,
            $this->display_type,
            $this->context->shop->id,
            $this->context->language->id,
        ];

        $key = implode('|', $data);

        return hash('sha256', $key);
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
        } else {
            $this->context->controller->addCSS([
                _THEME_CSS_DIR_ . 'category.css' => 'all',
                _THEME_CSS_DIR_ . 'product_list.css' => 'all',
                $this->module->getModulePath() . 'views/css/products.css' => 'all',
            ]);
        }

        $this->addJquery();
        $this->addJS($this->module->getModulePath() . 'views/js/iframeResizer.contentWindow.min.js');
        $this->addJS($this->module->getModulePath() . 'views/js/front_products.js');
    }

    public function getBreadcrumbLinks()
    {
        $letter = Tools::getValue('letter');
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->module->l('Products'),
            'url' => $this->context->link->getModuleLink('prestawp', 'products'),
        ];
        if ($letter) {
            $breadcrumb['links'][] = [
                'title' => Tools::strtoupper($letter),
                'url' => '',
            ];
        }

        return $breadcrumb;
    }

    public function getProductsFront($ids, $category_ids = null, $order_by = 'position', $order_way = 'ASC', $limit = null, $manufacturer_ids = null)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $interval =
            (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);

        if ($category_ids) {
            $category_ids = explode(',', $category_ids);
            if (is_array($category_ids)) {
                // include products from subcategories if Faceted Search module configured that way
                if (Module::isEnabled('ps_facetedsearch') && Configuration::get('PS_LAYERED_FULL_TREE')) {
                    $category_ids_tmp = [];
                    foreach ($category_ids as $id_category) {
                        $category_tmp = new Category($id_category, $id_lang);
                        $children = $category_tmp->getAllChildren($id_lang);
                        foreach ($children as $child) {
                            $category_ids_tmp[] = $child->id;
                        }
                    }
                    $category_ids = array_merge($category_ids, $category_ids_tmp);
                    $category_ids = array_unique($category_ids);
                }
            }
        }
        if ($manufacturer_ids) {
            $manufacturer_ids = explode(',', $manufacturer_ids);
        }

        if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $sql = '
            SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
                pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
                pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
                m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
                MAX(image_shop.`id_image`) id_image, il.`legend`,
                t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
                DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
                INTERVAL ' . $interval . ' DAY)) > 0 AS new
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p', false) . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`
             ' . (is_array($category_ids) && count($category_ids) == 1
                    ? ' AND cp.`id_category` = ' . (int) $category_ids[0]
                    : '') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
             ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
                AND tr.`id_country` = ' . (int) Context::getContext()->country->id . '
                AND tr.`id_state` = 0
            LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
            ' . Product::sqlStock('p');
        } elseif ($this->getPSVersion() == 1.6) {
            $sql = '
            SELECT
                p.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute,
                pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
                image_shop.`id_image` id_image, il.`legend`,
                p.`ean13`, p.`upc`, p.`reference`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order,
                IFNULL(stock.quantity, 0) as quantity, p.customizable,
                IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
                product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $interval . ' DAY')) . '" as new,
                product_shop.`on_sale`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1
                 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
             ON (product_attribute_shop.id_product_attribute=pa.id_product_attribute)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`
             ' . (is_array($category_ids) && count($category_ids) == 1
                    ? ' AND cp.`id_category` = ' . (int) $category_ids[0]
                    : '') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product`
             AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
             AND il.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON cl.`id_category` = product_shop.`id_category_default`
                AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . Product::sqlStock('p', 0);
        } else { // 1.7
            $sql = '
            SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
                ' . (Combination::isFeatureActive() ? 'product_attribute_shop.minimal_quantity
                 AS product_attribute_minimal_quantity,
                IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute,' : '') . '
                pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
                pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
                image_shop.`id_image` id_image, il.`legend`,
                t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
                DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                INTERVAL ' . (int) $interval . ' DAY)) > 0 AS new 
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p', false);

            if (Combination::isFeatureActive()) {
                $sql .= '
                 LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				    ON (p.`id_product` = product_attribute_shop.`id_product`
				     AND product_attribute_shop.`default_on` = 1 
				     AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')';
            }

            $sql .= '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`
                 ' . (is_array($category_ids) && count($category_ids) == 1
                    ? ' AND cp.`id_category` = ' . (int) $category_ids[0]
                    : '') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1
					 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
				 AND il.`id_lang` = ' . (int) $context->language->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = ' . (int) $context->country->id . '
					AND tr.`id_state` = 0
				LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
				' . Product::sqlStock('p', 0);
        }

        if ($category_ids) {
            $sql .= ' WHERE p.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_category` IN (' . implode(',', array_map('intval', $category_ids)) . ')
            )';
        } elseif ($manufacturer_ids) {
            $sql .= ' WHERE p.`id_manufacturer` IN (' . implode(',', array_map('intval', $manufacturer_ids)) . ')';
        } else {
            $sql .= ' WHERE p.`id_product` IN (' . pSQL($ids) . ')';
        }

        $sql .= '
            AND product_shop.`active` = 1
            AND p.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
				JOIN `' . _DB_PREFIX_ . 'category_group` cg
				 ON (cp.id_category = cg.id_category AND cg.`id_group`
				  ' . (count($groups) ? 'IN (' . implode(',', array_map('intval', $groups)) . ')' : '= 1') . ')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        $sql .= ' GROUP BY p.`id_product` ';
        if ($ids) {
            $sql .= ' ORDER BY FIELD(p.`id_product`, ' . pSQL($ids) . ') ASC';
        } else {
            // Sorting
            if ($order_by && $order_by != 'rand') {
                $order_by = Validate::isOrderBy($order_by) ? Tools::strtolower($order_by) : 'position';
                $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

                $order_by_prefix = false;
                if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
                    $order_by_prefix = 'p';
                } elseif ($order_by == 'name') {
                    $order_by_prefix = 'pl';
                } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
                    $order_by_prefix = 'm';
                    $order_by = 'name';
                } elseif ($order_by == 'position') {
                    $order_by_prefix = 'cp';
                } elseif ($order_by == 'price') {
                    $order_by_prefix = 'p';
                }

                $sql .= ' ORDER BY ' . (!empty($order_by_prefix) ? $order_by_prefix . '.' : '') . '`' . bqSQL($order_by)
                    . '` ' . pSQL($order_way);
            } elseif ($order_by == 'rand') {
                $sql .= ' ORDER BY RAND()';
            } else {
                $sql .= ' ORDER BY cp.`position` ASC';
            }
        }

        $limit = ($limit ? $limit : Tools::getValue('limit'));
        if ($limit && is_numeric($limit)) {
            $sql .= ' LIMIT 0, ' . (int) $limit;
        }

        $products = Db::getInstance()->executeS($sql);
        $products = Product::getProductsProperties($context->language->id, $products);

        if ($this->getPSVersion() >= 1.7 && $this->display_type == 'api') {
            $assembler = new ProductAssembler($context);
            $presenterFactory = new ProductPresenterFactory($context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
                new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                    $context->link
                ),
                $context->link,
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                $context->getTranslator()
            );

            $products_for_template = [];

            foreach ($products as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $context->language
                );
            }

            $products = $products_for_template;
        }

        return $products;
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

    protected function getIdCurrency()
    {
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');

        $iso = Tools::getValue('isolang');
        $cur_by_iso = [
            'fr' => 'EUR',
            'de' => 'EUR',
            'es' => 'EUR',
            'it' => 'EUR',
            'pl' => 'PLN',
            'ru' => 'RUB',
            'en' => 'GBP',
            'no' => 'NOK',
        ];
        if (isset($cur_by_iso[$iso])) {
            $cur_iso = $cur_by_iso[$iso];
            $id = Currency::getIdByIsoCode($cur_iso);
            if ($id) {
                $id_currency = $id;
            }
        }

        return (int) $id_currency;
    }

    public function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->buildContainer();
        }

        return $this->container;
    }
}
