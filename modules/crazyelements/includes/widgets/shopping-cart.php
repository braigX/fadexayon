<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Widget_Shopping_Cart extends Widget_Base {


	public function get_name() {
		return 'shopping_cart';
	}

	public function get_title() {
		return PrestaHelper::__( 'Shopping Cart', 'elementor-pro' );
	}

	public function get_icon() {
		return 'ceicon-cart';
	}

	public function get_categories() {
		return [ 'theme-elements' ];
	}


	protected function _register_controls() {

		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => PrestaHelper::__( 'Style', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'              => PrestaHelper::__( 'Default', 'elementor' ),
					'custom'     => PrestaHelper::__( 'Custom', 'elementor' )
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'icon_cart',
			array(
				'label'            => PrestaHelper::__( 'Icon', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'cart_text',
			array(
				'label'   => PrestaHelper::__( 'Text', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'count_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Count', 'elementor'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_count',
			array(
				'label'   => PrestaHelper::__( 'Show Count', 'elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'dynamic' => array(
					'active' => true,
				),
				'default' => 'yes',
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'count_style',
			array(
				'label'   => PrestaHelper::__( 'Count Style', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'text'              => PrestaHelper::__( 'Text', 'elementor' ),
					'ball_top'     => PrestaHelper::__( 'Flag Style', 'elementor' )
				),
				'default' => 'text',
				'condition'        => array(
					'show_count' => 'yes',
				),
			)
		);

		$this->add_control(
			'count_pref',
			array(
				'label'   => PrestaHelper::__( 'Count Prefix', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( '(', 'elementor' ),
				'condition'        => array(
					'count_style' => 'text',
				),
			)
		);

		$this->add_control(
			'count_suff',
			array(
				'label'   => PrestaHelper::__( 'Count Suffix', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( ')', 'elementor' ),
				'condition'        => array(
					'count_style' => 'text',
				),
			)
		);

		$this->add_control(
			'subtotal_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Subtotal', 'elementor'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_subtotal',
			array(
				'label'   => PrestaHelper::__( 'Show Subtotal', 'elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'dynamic' => array(
					'active' => true,
				),
				'default' => 'yes',
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_pos',
			array(
				'label'   => PrestaHelper::__( 'Icon Position', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'before'              => PrestaHelper::__( 'Before', 'elementor' ),
					'after'     => PrestaHelper::__( 'After', 'elementor' )
				),
				'default' => 'before',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => PrestaHelper::__( 'General Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart' => 'background-color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cart_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-shopping-cart',
			)
		);
		$this->add_responsive_control(
			'cart_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-shopping-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sec_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-shopping-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cart_align',
			[
				'label' => PrestaHelper::__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => PrestaHelper::__( 'Left', 'elementor' ),
						'icon' => 'ceicon-text-align-left',
					],
					'center' => [
						'title' => PrestaHelper::__( 'Center', 'elementor' ),
						'icon' => 'ceicon-text-align-center',
					],
					'right' => [
						'title' => PrestaHelper::__( 'Right', 'elementor' ),
						'icon' => 'ceicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-shopping-cart' => 'text-align: {{VALUE}}'
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_cart_style',
			array(
				'label' => PrestaHelper::__( 'Icon Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .crazy-shopping-cart svg' => 'fill: {{VALUE}};',
				),
			)
		);


		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart i:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .crazy-shopping-cart svg:hover' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .crazy-shopping-cart a:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .crazy-shopping-cart a:hover svg' => 'fill: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'cart_icon_icon',
			array(
				'label'     => PrestaHelper::__( 'Icon Size', 'elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart svg' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .crazy-shopping-cart i' => 'font-size: {{SIZE}}{{UNIT}};',
				),

			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'count_flag_style',
			array(
				'label' => PrestaHelper::__( 'Count Flag Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'        => array(
					'count_style' => 'ball_top',
				),

			)
		);

		$this->add_control(
			'count_background_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart .sb-cart-with-count .sb-cart-count' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'count_flag_text_color',
			array(
				'label'     => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart .sb-cart-with-count .sb-cart-count' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_responsive_control(
			'count_falg_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-shopping-cart  .sb-cart-with-count .sb-cart-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'count_flag_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-shopping-cart  .sb-cart-with-count .sb-cart-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'text_cart_style',
			array(
				'label' => PrestaHelper::__( 'Text Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart .cart-text-wrapper' => 'color: {{VALUE}};',
				),
			)
		);


		$this->add_control(
			'text_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-shopping-cart .cart-text-wrapper:hover' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_text_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-shopping-cart .cart-text-wrapper',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( PrestaHelper::is_admin() ) {

			return;
		}

		$settings       = $this->get_settings_for_display();
		$source       = $settings['source'];
		$context         = \Context::getContext();
		
		if($source == 'default'){
			if ( \Module::isInstalled( 'ps_shoppingcart' ) && \Module::isEnabled( 'ps_shoppingcart' ) ) {
				$mod_ins = \Module::getInstanceByName( 'ps_shoppingcart' );
	
				if ( ! is_object( $mod_ins ) ) {
					return $results;
				}
				$results         = '';
				
				if ( \Validate::isLoadedObject( $mod_ins ) && method_exists( $mod_ins, 'renderWidget' ) ) {
					$results = $mod_ins->renderWidget( PrestaHelper::$hook_current, array() );
				}
				echo $results;
			}
		}else{
			
			$out_put = '';
			array_unshift($context->smarty->registered_resources['module']->paths, _PS_MODULE_DIR_ . 'crazyelements/views/templates/front/modules/');
			$icon_cart       = $settings['icon_cart'];
			$cart_text       = $settings['cart_text'];
			$show_count       = $settings['show_count'];
			$show_subtotal       = $settings['show_subtotal'];
			$icon_pos       = $settings['icon_pos'];
			$active_text = 'inactive';
			$icon_html = Icons_Manager::render_icon( $icon_cart, [ 'aria-hidden' => 'true' ], 'i', 0 );
			$shop_enable = (int) \Configuration::get('PS_SHOP_ENABLE');
			if($shop_enable){
				$cart = (new CartPresenter())->present($context->cart);
				$prd_count = $cart['products_count'];
				if($cart['products_count'] > 0){ 
					$active_text = 'active';
				}
				$formatted_subtotal = '';
				if(isset($cart['subtotals'])){
					$subtotal = 0;
					$price_formatter = new PriceFormatter();
					foreach($cart['subtotals'] as $sub_tem){
						$subtotal += $sub_tem['amount'];
					}
					$formatted_subtotal = $price_formatter->format($subtotal);
				}
			}else{
				$prd_count = 0;
				$formatted_subtotal = '$0';
			}
			
			$cart_page_url = $context->link->getPageLink(
				'cart',
				null,
				$context->language->id,
				[
					'action' => 'show',
				],
				false,
				null,
				true
			);
			$count_style       = $settings['count_style'];

			$ajax_arr = array(
				'active_text'      => $active_text,
				'icon_pos'      => $icon_pos,
				'count_style'       => $count_style,
				'show_count'       => $show_count,
				'show_subtotal'       => $show_subtotal,
				'cart_text'       => $cart_text,
				'icon_cart'       => $icon_cart,
				'crazy_cart_init'       => 1,
				'elementprefix'    => 'crazy-shoping-cart',
			);
			$count_text = '';
			$ajax_arr['count_pref'] = '';
			$ajax_arr['count_suff'] = '';
			if($count_style == 'text'){
								
				$count_pref       = $settings['count_pref'];
				$count_suff       = $settings['count_suff'];
				$ajax_arr['count_pref'] = $count_pref;
				$ajax_arr['count_suff'] = $count_suff;
				$count_text = $count_pref . $cart['products_count'] . $count_suff;
			}	

			$refres_url =  $context->link->getModuleLink('crazyelements', 'ajax', $ajax_arr, null, null, null, true);
			$ajax_arr['refres_url'] = $refres_url;
			$ajax_arr['prd_count'] = $prd_count;
			$ajax_arr['cart_page_url'] = $cart_page_url;
			$ajax_arr['formatted_subtotal'] = $formatted_subtotal;
			$ajax_arr['icon_html'] = $icon_html;
			$context->smarty->assign(
                $ajax_arr
            );
			$out_put .= $context->smarty->fetch('module:ps_shoppingcart/ps_shoppingcart.tpl');
            echo $out_put;
		}
	}

	/**
	 * Render accordion widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function _content_template() {
	}

}
