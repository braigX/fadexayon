<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}
class Widget_ProductQuantity extends Widget_Base {


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
		return 'product_quantity';
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
		return PrestaHelper::__( 'Product Quantity', 'elementor' );
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
		return 'ceicon-product-quantity-widget';
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
		$this->add_responsive_control(
			'alignment',
			array(
				'label'        => PrestaHelper::__( 'Alignment', 'elecounter' ),
				'type'         => Controls_Manager::CHOOSE,
				'devices'      => array( 'desktop', 'tablet', 'mobile' ),
				'options'      => array(
					'left'    => array(
						'title' => PrestaHelper::__( 'Left', 'elecounter' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => PrestaHelper::__( 'Center', 'elecounter' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => PrestaHelper::__( 'Right', 'elecounter' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => PrestaHelper::__( 'Justify', 'elecounter' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'prefix_class' => 'alignment%s',
				'default'      => 'center',
				'separator' => 'before'
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => PrestaHelper::__( 'Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->start_controls_tabs( 'product_quantity_style' );

		$this->start_controls_tab(
			'spinner_tab',
			array(
				'label' => PrestaHelper::__( 'Spinner', 'elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'spinner_typography',
				'label'    => PrestaHelper::__( 'Heading Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-single-product-quantity #quantity_wanted',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

		$this->add_control(
			'spinner_color',
			array(
				'label'     => PrestaHelper::__( 'Spinner Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity #quantity_wanted' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'spinner_bg_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity #quantity_wanted' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'spinner_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-single-product-quantity #quantity_wanted',
			)
		);

		$this->add_responsive_control(
			'spinner_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .crazy-single-product-quantity #quantity_wanted' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spinner_up_tab',
			array(
				'label' => PrestaHelper::__( 'Up', 'elementor' ),
			)
		);

		$this->add_control(
			'spinner_up_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-up' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'spinner_up_bg_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-up' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'spinner_up_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-up',
			)
		);

		$this->add_responsive_control(
			'spinner_up_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-up' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spinner_down_tab',
			array(
				'label' => PrestaHelper::__( 'Down', 'elementor' ),
			)
		);

		$this->add_control(
			'spinner_down_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-down' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'spinner_down_bg_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-down' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'spinner_down_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-down',
			)
		);

		$this->add_responsive_control(
			'spinner_down_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .crazy-single-product-quantity .bootstrap-touchspin-down' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
		$controller_name = \Tools::getValue('controller');
			
		if ($controller_name == "product") {
            $out_put = '';
            $id_product = (int)\Tools::getValue('id_product');

            \Context::getcontext()->smarty->assign(
                array(
                    'from_editor'      => 'no',
                    'elementprefix'    => 'single-product-quantity',
                )
            );
            $out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_quantity.tpl');
            echo $out_put;
        } else {
            // controls
			if (isset($_GET['prdid'])) {
                $id_product = (int)$_GET['prdid'];
            }else{
				$id_product = null;
			}
			
			// common vars
			$out_put = '';
			$context = \Context::getContext();
			$id_lang = $context->language->id;

			$product = new \Product( $id_product, true, $id_lang );
			
			$product_arr['quantity_wanted'] = (int) \Tools::getValue('quantity_wanted', 1);
			$product_arr['minimal_quantity'] = $product->minimal_quantity;
        
            $out_put = '';
            $context->smarty->assign(
				array(
					'product'          => $product_arr,
                    'from_editor'      => 'yes',
					'elementprefix'    => 'single-product-quantity',
				)
			);
			$template_file_name = _PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_quantity.tpl';
			$out_put           .= $context->smarty->fetch($template_file_name);
			echo $out_put;
        }
	}
}