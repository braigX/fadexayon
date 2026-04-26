<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Includes\Widgets\Traits\Product_Trait;


if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_ProductSinglePrices extends Widget_Base
{
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
    public function get_name()
    {
        return 'product_single_prices';
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
    public function get_title()
    {
        return PrestaHelper::__('Product Prices', 'elementor');
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
    public function get_icon()
    {
        return 'ceicon-product-prices-widget';
    }

    public function get_categories()
    {
        return array('products_layout');
    }

    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
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
            'selected_ids',
            array(
                'label'     => PrestaHelper::__('Select Product', 'elementor'),
                'type'      => Controls_Manager::AUTOCOMPLETE,
                'item_type' => 'product',
                'multiple'  => false,
            )
        );

        $this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
            'style',
            array(
                'label' => PrestaHelper::__('Content', 'elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
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
				'default' => 'stacked'
			]
		);

        $this->register_price_style('crazy_single_product_prices_typography', 'crazy_single_product_prices_color');

        $this->add_control(
			'discount_badge_style',
			[
				'label' => PrestaHelper::__( 'Discount Badge Style', 'elementor' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $this->add_control(
			'discount_badge_text_align',
			[
				'label' => PrestaHelper::__('Text Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => PrestaHelper::__('Left', 'elementor'),
						'icon' => 'ceicon-text-align-left',
					],
					'center' => [
						'title' => PrestaHelper::__('Center', 'elementor'),
						'icon' => 'ceicon-text-align-center',
					],
					'right' => [
						'title' => PrestaHelper::__('Right', 'elementor'),
						'icon' => 'ceicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'text-align: {{VALUE}}'
				],
				'separator' => 'after'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'discount_badge_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

		$this->add_control(
			'discount_badge_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'discount_badge_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'discount_badge_shadow',
				'selector' => '{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount'
			)
		);

        $this->add_responsive_control(
			'discount_badge_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ),
                'separator' => 'before'
			)
		);
        
        $this->add_responsive_control(
			'discount_badge_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->add_responsive_control(
			'discount_badge_width',
			[
				'label' => PrestaHelper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
					'vh' => [
						'min' => 0,
						'max' => 200,
					],
					'vw' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'selectors' => [
					'{{WRAPPER}} .crazy-single-product-price .product-price .current-price .discount' => 'width: {{SIZE}}{{UNIT}}',
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
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings     = $this->get_settings_for_display();
        $orientation        = $settings['orientation'];

        $controller_name = \Tools::getValue('controller');
        if ($controller_name == "product") {
            $out_put = '';
            $id_product = (int)\Tools::getValue('id_product');

            \Context::getcontext()->smarty->assign(
                array(
                    'from_editor'      => 'no',
                    'orientation'       => $orientation,
                    'elementprefix'    => 'single-product-price',
                )
            );
            $template_file_name = _PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_prices.tpl';
			$out_put           .= \Context::getcontext()->smarty->fetch($template_file_name);
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
            $product_arr = array();
            foreach($products_for_template as $product){

                $product_arr['id'] = $product['id'];
                $product_arr['has_discount'] = $product['has_discount'];
                $product_arr['seo_availability'] = $product['seo_availability'];

                $product_arr['rounded_display_price'] = $product['price'];
                                                        
                $displaYUnitPrice = (!empty($product['unity']) && $product['unit_price_ratio'] > 0.000000) ? true : false;
                $product_arr['price'] = $product['price'];
                $product_arr['regular_price'] = $product['regular_price'];
                $product_arr['discount_type'] = $product['discount_type'];
                $product_arr['discount_percentage_absolute'] = $product['discount_percentage_absolute'];
                $product_arr['price_tax_exc'] = $product['price_tax_exc'];
                $product_arr['additional_delivery_times'] = $product['additional_delivery_times'];
                $product_arr['delivery_information'] = $product['delivery_information'];
                $product_arr['quantity'] = $product['quantity'];
                $product_arr['labels']['tax_long'] = $product['labels']['tax_long'];
            }
            $configs = $this->getConfigurationVals();

            $priceDisplay = \Product::getTaxCalculationMethod((int) $this->current_context->cookie->id_customer);
            $currency['iso_code'] = $this->current_context->currency->iso_code;
            $out_put = '';
            $this->current_context->smarty->assign(
				array(
					'product'          => $product_arr,
					'currency'         => $currency,
                    'displayUnitPrice' => $displaYUnitPrice,
                    'priceDisplay'     => $priceDisplay,
					'elementprefix'    => 'single-product-price',
                    'configuration'      => $configs,
                    'from_editor'      => 'yes',
                    'orientation'       => $orientation,
				)
			);
			$template_file_name = _PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_prices.tpl';
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
    protected function _content_template()
    {
    }
}
