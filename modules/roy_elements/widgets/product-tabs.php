<?php

use CrazyElements\Modules\DynamicTags\Module as TagsModule;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Controls_Manager;
use CrazyElements\Core\Schemes;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Roy_Product_Tabs extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve accordion widget name.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'roy_product_tabs';
    }

    /**
     * Get widget title.
     *
     * Retrieve accordion widget title.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return PrestaHelper::__('Roy Product Tabs', 'elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve accordion widget icon.
     *
     * @since  1.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'ceicon-gallery-grid';
    }

    public function get_categories()
    {
        return array('modez');
    }

    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_title',
            array(
                'label' => PrestaHelper::__('General', 'elementor'),
            )
        );

        $repeater = new CrazyElements\Repeater();
        $repeater->add_control(
            'tab_title',
            array(
                'label'   => PrestaHelper::__('Tab Title', 'modez'),
                'type'    => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('Tab Title', 'modez'),
            )
        );
        $repeater->add_control(
            'tab_type',
            array(
                'label'     => PrestaHelper::__('Select product Type', 'elementor'),
                'type'      => Controls_Manager::SELECT,
                'options' => array(
                    'featured'           => PrestaHelper::__('Featured products', 'elementor'),
                    'new'           => PrestaHelper::__('New products', 'elementor'),
                    'best'           => PrestaHelper::__('Best sellers', 'elementor'),
                    'sale'           => PrestaHelper::__('Sale products', 'elementor'),
                    'category'           => PrestaHelper::__('Custom Category', 'elementor'),
                    'custom'           => PrestaHelper::__('Custom Products', 'elementor'),
                ),
            )
        );
        $repeater->add_control(
            'category_id',
            array(
                'label'   => PrestaHelper::__('Category id', 'elementor'),
                'type'    => Controls_Manager::NUMBER,
                //'default' => 6,
                'condition'    => [
                    'tab_type' => 'category',
                ],
            )
        );
        $repeater->add_control(
            'ids',
            array(
                'label'     => PrestaHelper::__('Select products', 'elementor'),
                'type'      => Controls_Manager::AUTOCOMPLETE,
                'item_type' => 'product',
                'multiple'  => true,
                'condition'    => [
                    'tab_type' => 'custom',
                ],
            )
        );

        $this->add_control(
            'items',
            array(
                'label'   => PrestaHelper::__('Item List', 'modez'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => array(
                    array(
                        'title' => PrestaHelper::__('Tab 1', 'modez'),
                    ),
                ),
            )
        );


        $this->add_control(
            'layout',
            [
                'label' => PrestaHelper::__('Layout', 'modez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slider',
                'options' => [
                    'grid' => PrestaHelper::__('Grid', 'modez'),
                    'slider' => PrestaHelper::__('Slider', 'modez'),
                ],
            ]
        );

        $this->add_control(
            'is_autoplay',
            [
                'label'        => PrestaHelper::__('Autoplay', 'plugin-domain'),
                'type'         => Controls_Manager::SWITCHER,
                'true'          => PrestaHelper::__('Yes', 'your-plugin'),
                'false'           => PrestaHelper::__('No', 'your-plugin'),
                'default'      => 'false',
                'condition'    => [
                    'layout' => 'slider',
                ],
            ]
        );

        $this->add_control(
            'title_align',
            array(
                'label'   => PrestaHelper::__('Titles Alignment', 'elementor'),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'left' => PrestaHelper::__('Left', 'elementor'),
                    'center'  => PrestaHelper::__('Center', 'elementor'),
                ),
                'default' => 'left',
            )
        );

        $this->add_control(
            'per_row',
            array(
                'label'   => PrestaHelper::__('Product Per Row', 'modez'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 3,
            )
        );


        $this->add_control(
            'products_show',
            array(
                'label'   => PrestaHelper::__('Products to show', 'elementor'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            )
        );


        $this->add_control(
            'orderby',
            array(
                'label'   => PrestaHelper::__('Order by', 'elementor'),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'id_product'   => PrestaHelper::__('Product Id', 'elementor'),
                    'price'        => PrestaHelper::__('Price', 'elementor'),
                    'date_add'     => PrestaHelper::__('Published Date', 'elementor'),
                    'name'         => PrestaHelper::__('Product Name', 'elementor'),
                    'position'     => PrestaHelper::__('Position', 'elementor'),
                ),
                'default' => 'id_product',
            )
        );

        $this->add_control(
            'order',
            array(
                'label'   => PrestaHelper::__('Order', 'elementor'),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'DESC' => PrestaHelper::__('Descending', 'elementor'),
                    'ASC'  => PrestaHelper::__('Ascending', 'elementor'),
                ),
                'default' => 'ASC',
            )
        );


        $this->add_control(
            'show_all',
            [
                'label' => PrestaHelper::__('Add Show All Button', 'modez'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'btn_text',
            array(
                'label'   => PrestaHelper::__('Button Text', 'modez'),
                'type'    => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('Shop All', 'modez'),
                'condition' => [
                    'show_all' => 'yes',
                ],
            )
        );
        $this->add_control(
            'btn_link',
            array(
                'label' => PrestaHelper::__('Button Link', 'modez'),
                'type'  => Controls_Manager::URL,
                'show_external' => false,
                'condition' => [
                    'show_all' => 'yes',
                ],
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0
     * @access protected
     */
    protected function render()
    {

        if (PrestaHelper::is_admin()) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $orderby  = $settings['orderby'];
        $show_all  = $settings['show_all'];
        $order    = $settings['order'];
        $layout    = $settings['layout'];
        $is_autoplay    = $settings['is_autoplay'];
        $title_align      = $settings['title_align'];
        $per_row    = $settings['per_row'];
        $products_show = $settings['products_show'];
        if ( 'yes' === $settings['show_all'] ) {
            $btn_text = $settings['btn_text'];        
            $btn_link = $settings['btn_link']['url'];
        }		 

        // Generate Random id for tab section
        if (!empty($settings['items'])) {
            $unique_menu_id = [];
            foreach ($settings['items'] as $tab) {
                $tab_title = $tab['tab_title'];
                $random_id = rand(111, 999);
                array_push($unique_menu_id, $random_id);
            }
        }

?>

        <ul class='nav nav-tabs nav-tabs--carusel title-align-<?php echo $title_align; ?> <?php if ($layout == 'slider') : ?>slider-on<?php endif; ?>' data-auto='<?php echo $is_autoplay; ?>' data-max-slides='<?php echo $per_row; ?>' role='tablist'>
            <?php
            if (!empty($settings['items'])) {
                $i = 0;
                foreach ($settings['items'] as $tab) {
                    $tab_title = $tab['tab_title'];

                    if ($i == 0) {
                        $active = 'active';
                    } else {
                        $active = '';
                    }
            ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active; ?>" href='#product_tab_<?php echo $unique_menu_id[$i]; ?>' role="tab" data-toggle="tab"><?php echo $tab_title; ?></a>
                    </li>
            <?php
                    $i++;
                }
            }
            ?>
        </ul>
        <div class="tab-content columns-desktop-<?php echo $per_row; ?>" id="tab-content">
            <?php
            if (!empty($settings['items'])) {
                $i = 0;
                foreach ($settings['items'] as $tab) {

                    if ($i == 0) {
                        $active = 'in active';
                    } else {
                        $active = '';
                    }
            ?>
                    <div class="tab-pane fade <?php echo $active; ?>" id="product_tab_<?php echo $unique_menu_id[$i]; ?>" role="tabpanel" aria-expanded="false">
                        <?php
                        $context = \Context::getContext();
                        $out_put = '';
                        $id_lang = $context->language->id;
                        $front   = true;
                        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
                            $front = false;
                        }

                        if ($products_show < 0) {
                            $products_show = 12;
                        }
                        if ($tab['tab_type'] == 'custom') {
                            $ids = $tab['ids'];
                            $str             = implode(',', $ids);
                            $order_by_prefix = '';
                            if ($orderby == 'id_product' || $orderby == 'price' || $orderby == 'date_add' || $orderby == 'date_upd') {
                                $order_by_prefix = 'p';
                            } elseif ($orderby == 'name') {
                                $order_by_prefix = 'pl';
                            }

                            $sql = 'SELECT p.*, product_shop.*, pl.*, image_shop.`id_image`, il.`legend`, m.`name` AS manufacturer_name, s.`name` AS supplier_name
                                FROM `' . _DB_PREFIX_ . 'product` p
                                ' . \Shop::addSqlAssociation('product', 'p') . '
                                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . \Shop::addSqlRestrictionOnLang('pl') . ')
                                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                                                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
                                \Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) \Context::getContext()->language->id . ')
                                WHERE pl.`id_lang` = ' . (int) $id_lang .
                                ' AND p.`id_product` IN( ' . $str . ')' .
                                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                                ' AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))' .
                                ' AND product_shop.`active` = 1';

                            if (!empty($orderby) && isset($order_by_prefix)) {
                                $sql .= " ORDER BY {$order_by_prefix}.{$orderby} {$order}";
                            }

                            $rq = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                            $product = \Product::getProductsProperties($id_lang, $rq);

                            if (!$product) {
                                return false;
                            }
                        } else if ($tab['tab_type'] == 'best') {
                            $product = ProductSale::getBestSales($context->language->id, 0, $products_show, $orderby, $order);
                        } else {
                            if ($tab['tab_type'] == 'featured') {
                                $category = new Category((int) Configuration::get('HOME_FEATURED_CAT'));

                                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider(
                                    $context->getTranslator(),
                                    $category
                                );

                                $context1 = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($context);

                                $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
                            } else if ($tab['tab_type'] == 'category') {
                                $category = new Category((int) $tab['category_id']);
                                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider(
                                    $context->getTranslator(),
                                    $category
                                );

                                $context1 = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($context);

                                $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
                            } else if ($tab['tab_type'] == 'new') {
                                $searchProvider = new \PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider(
                                    $context->getTranslator()
                                );

                                $context1 = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($context);

                                $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
                                $query->setQueryType('new-products');
                            } else if ($tab['tab_type'] == 'best') {
                                $searchProvider = new \PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider(
                                    $context->getTranslator()
                                );

                                $context1 = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($context);

                                $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
                                $query->setQueryType('best-sales');
                            } else {
                                $searchProvider = new \PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider(
                                    $context->getTranslator()
                                );

                                $context1 = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($context);

                                $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
                                $query->setQueryType('prices-drop');
                            }
                            $query
                                ->setResultsPerPage($products_show)
                                ->setPage(1);
                            $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $orderby, $order));
                            $result = $searchProvider->runQuery(
                                $context1,
                                $query
                            );
                            $product = $result->getProducts();
                        }
                        if ($product) {
                            $assembler = new \ProductAssembler($context);

                            $presenterFactory     = new \ProductPresenterFactory($context);
                            $presentationSettings = $presenterFactory->getPresentationSettings();
                            $presenter            = new \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
                                new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                                    $context->link
                                ),
                                $context->link,
                                new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                                new \PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                                $context->getTranslator()
                            );

                            $products_for_template = array();

                            foreach ($product as $rawProduct) {

                                // echo $r;

                                $products_for_template[] = $presenter->present(
                                    $presentationSettings,
                                    $assembler->assembleProduct($rawProduct),
                                    $context->language
                                );
                                // print_r( $product_arr );

                            }

                            $context->smarty->assign(
                                array(
                                    'vc_products'         => $products_for_template,
                                    'column_val'          => 'col-lg-3',
                                    'elementprefix'       => 'single-product',
                                    'theme_template_path' => _PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl',

                                )
                            );

                            $template_file_name = ROYELEMENTS_PATH . '/views/templates/front/blocktabproducts.tpl';
                            $out_put           .= $context->smarty->fetch($template_file_name);

                            echo $out_put;
                        } else {
                            echo 'No products Selected';
                        }
                        ?>
                        <?php if ($show_all == 'yes') : ?>
                            <div class="show-all text-center offset-10">
                                <a href="<?php echo $btn_link; ?>" class="btn"><?php echo $btn_text; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
            <?php
                    $i++;
                }
            }
            ?>
        </div>

<?php
    }

    /**
     * Render accordion widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0
     * @access protected
     */
    protected function _content_template()
    {
    }
}

CrazyElements\Plugin::instance()->widgets_manager->register_widget_type(new \Roy_Product_Tabs());
