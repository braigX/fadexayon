<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Includes\Widgets\Traits\Product_Trait;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_ProductImage extends Widget_Base {

	use Product_Trait;

	/**
	 * Get widget name.
	 *
	 * Retrieve accordion widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'product_thumbnail';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve accordion widget title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return PrestaHelper::__( 'Product Thumbnail', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve accordion widget icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'ceicon-product-thumb-widget';
	}

	public function get_categories() {
		return array( 'products_layout' );
	}

	/**
	 * Register accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);

		$this->add_control(
			'selected_ids',
			array(
				'label'     => PrestaHelper::__('Select Products', 'elementor'),
				'type'      => Controls_Manager::AUTOCOMPLETE,
				'item_type' => 'product',
				'multiple'  => false,
			)
		);

		$this->add_control(
			'show_flags',
			array(
				'label'   => PrestaHelper::__( 'Show Flags?', 'elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'dynamic' => array(
					'active' => true,
				),
				'default' => 'yes',
				'return_value' => 'yes',
			)
		);	

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_product_image',
			array(
				'label' => PrestaHelper::__( 'Product Image', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->register_image_styles();

		$this->add_responsive_control(
			'product_single_image_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-single-product-image .products .product-cover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .crazy-single-product-image .product-images .thumb-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'product_flag',
			array(
				'label'      => PrestaHelper::__('Flag', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

		$this->register_flag_style();

		$this->end_controls_section();
	}

	/**
	 * Render accordion widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings     = $this->get_settings_for_display();
		$show_flags        = $settings['show_flags'];

		$controller_name = \Tools::getValue('controller');

		if($controller_name == "product"){

			$out_put = '';
			$id_product = (int)\Tools::getValue('id_product');

			\Context::getcontext()->smarty->assign(array(
				'show_flag' => $show_flags,  
				'jqZoomEnabled' => \Configuration::get('PS_DISPLAY_JQZOOM'),
				'elementprefix'       => 'single-product-image',
				)
			);

			$out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_image.tpl');
			echo $out_put;
		} else {

			if (PrestaHelper::is_admin()) {
				return;
			}	

			// controls
			$ids        = $settings['selected_ids'];
			
			// common vars
			$out_put = '';
			$this->current_context = \Context::getContext();
			$id_lang = $this->current_context->language->id;

			// load assets
			$this->load_assets();

			// visivility check
			$front   = true;
			if (!in_array($this->current_context->controller->controller_type, array('front', 'modulefront'))) {
				$front = false;
			}

			$ids    = $settings['selected_ids'];
			$str    = $this->render_autocomplete_result($ids);
			if ($str == '' && isset($_GET['prdid'])) {
				$str = (int)$_GET['prdid'];
			}

			$query_id = '';
			if ($str != '') {
				$query_id = ' AND p.`id_product` IN( ' . $str . ')';
			}

			$query = $this->build_query($query_id, $id_lang, $front);
			$results = \Db::getInstance( _PS_USE_SQL_SLAVE_ )->executeS( $query );
			$products_for_template = $this->get_products_for_template($id_lang, $results);

			$this->current_context->smarty->assign(
				array(
					'show_flag' => $show_flags,  
					'crazy_products'         => $products_for_template,
					'elementprefix'       => 'single-product-image',
					'theme_template_path' => _PS_THEME_DIR_ . 'templates/catalog/_partials/product-cover-thumbnails.tpl',
				)
			);

			$template_file_name = 'module:crazyelements/views/templates/front/single-product/crazy_single_product_image.tpl';
			$out_put           .= $this->current_context->smarty->fetch($template_file_name);
			echo $out_put;
		}		
	}

	/**
	 * Render accordion widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _content_template() {
	}
}