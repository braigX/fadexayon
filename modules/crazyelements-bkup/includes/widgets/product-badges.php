<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Includes\Widgets\Traits\Product_Trait;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_ProductBadges extends Widget_Base {

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
		return 'product_badges';
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
		return PrestaHelper::__( 'Product Badges', 'elementor' );
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
		return 'ceicon-products-badge-widget';
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
			'badge_type',
			[
				'label' => PrestaHelper::__( 'Badge Type', 'elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'discount' => 'Sale',
					'new' => 'New',
					'pack' => 'Pack',
					'out' => 'Out of Stock',
					'online-only' => 'Online Only'
				)
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'product_flag',
			array(
				'label'      => PrestaHelper::__('Flag', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'orientation',
			[
				'label' => PrestaHelper::__( 'Orientation', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'inline' => 'Inline',
					'stacked' => 'Stacked',
				),
				'default' => 'stacked',
			]
		);

		$this->register_flag_style('relative');

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

		$controller_name = \Tools::getValue('controller');
		$settings     = $this->get_settings_for_display();
		$badge_type        = $settings['badge_type'];
		$orientation        = $settings['orientation'];
		
		if($controller_name == "product"){

			$out_put = '';
			$id_product = (int)\Tools::getValue('id_product');

			\Context::getcontext()->smarty->assign(
				array(
					'elementprefix'       => 'single-product-badge',
					'orientation'       => $orientation,
				)
			);

			$out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_flags.tpl');
			echo $out_put;
		} else {
			
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
			
			$flags_arr = array();
            foreach($products_for_template as $product){
				$flags_arr = $product['flags'];
            }
			
			$product_arr = array();
			foreach($badge_type as $badge){
				if(isset($flags_arr[$badge])){
					$product_arr['flags'][$badge] = $flags_arr[$badge];
				}
			}
			
			
			$this->current_context->smarty->assign(
				array(
					'product'         => $product_arr,
					'elementprefix'       => 'single-product-badge',
					'orientation'       => $orientation,
				)
			);

			$template_file_name = CRAZY_PATH . 'views/templates/front/single-product/crazy_single_product_flags.tpl';
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