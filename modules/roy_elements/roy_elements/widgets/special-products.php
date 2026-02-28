<?php

use CrazyElements\Modules\DynamicTags\Module as TagsModule;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Controls_Manager;
use CrazyElements\Core\Schemes;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}

class Roy_Special_Products extends Widget_Base
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
		return 'roy_special_products';
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
		return PrestaHelper::__('Roy Special Products', 'elementor');
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

		$this->add_control(
			'title',
			array(
				'label'       => PrestaHelper::__('Title', 'elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_align',
			array(
				'label'   => PrestaHelper::__('Title Alignment', 'elementor'),
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
			'per_page',
			array(
				'label'   => PrestaHelper::__('Products to Show', 'elementor'),
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
					'id_product'   => PrestaHelper::__( 'Product Id', 'elementor' ),
					'price'        => PrestaHelper::__( 'Price', 'elementor' ),
					'date_add'     => PrestaHelper::__( 'Published Date', 'elementor' ),
					'name'         => PrestaHelper::__( 'Product Name', 'elementor' ),
					'position'     => PrestaHelper::__( 'Position', 'elementor' ),
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
			'layout',
			[
				'label' => PrestaHelper::__( 'Layout', 'modez' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slider',
				'options' => [
					'grid' => PrestaHelper::__( 'Grid', 'modez' ),
					'slider' => PrestaHelper::__( 'Slider', 'modez' ),
				],
			]
		);
        
		$this->add_control(
			'is_autoplay',
			[
				'label'        => PrestaHelper::__( 'Autoplay', 'plugin-domain' ),
				'type'         => Controls_Manager::SWITCHER,
				'true'          => PrestaHelper::__( 'Yes', 'your-plugin' ),
				'false'           => PrestaHelper::__( 'No', 'your-plugin' ),
				'default'      => 'false',
				'condition'    => [
					'layout' => 'slider',
				],
			]
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

		$settings   = $this->get_settings_for_display();
		$title      = $settings['title'];
		$title_align      = $settings['title_align'];
		$per_page   = $settings['per_page'];
		$orderby    = $settings['orderby'];
		$order      = $settings['order'];
		$per_row    = $settings['per_row'];
        $layout     = $settings['layout'];
        $is_autoplay = $settings['is_autoplay'];
		$page       = 1;
		$context    = \Context::getContext();
		$output     = '';
        
		$cache_products = \Product::getPricesDrop( (int) \Context::getContext()->language->id, $page, $per_page, false, $orderby, $order );
		if ( ! $cache_products ) {
			echo 'No New Products Found';
			return false;
		}

		$context->controller->addCSS(_THEME_CSS_DIR_ . 'product.css');
		$context->controller->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
		$context->controller->addCSS(_THEME_CSS_DIR_ . 'print.css', 'print');
		$context->controller->addJqueryPlugin(array('fancybox', 'idTabs', 'scrollTo', 'serialScroll'));

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

		if (isset($cache_products) && !empty($cache_products)) {
			foreach ($cache_products as $rawProduct) {
				$products_for_template[] = $presenter->present(
					$presentationSettings,
					$assembler->assembleProduct($rawProduct),
					$context->language
				);
			}
		}

		$context->smarty->assign(
			array(
				'vc_products'         => $products_for_template,
				'vc_title'            => $title,
				'title_align'         => $title_align,
				'per_row'             => $per_row,
                'layout'              => $layout,
				'is_autoplay'         => $is_autoplay,
				'elementprefix'       => 'specialproducts',
				'theme_template_path' => _PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl',
                'allProductsLink' => Context::getContext()->link->getPageLink('prices-drop'),
			)
		);

		$output = $context->smarty->fetch(ROYELEMENTS_PATH . '/views/templates/front/blockroyproducts.tpl');

		echo $output;
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

CrazyElements\Plugin::instance()->widgets_manager->register_widget_type(new \Roy_Special_Products());
