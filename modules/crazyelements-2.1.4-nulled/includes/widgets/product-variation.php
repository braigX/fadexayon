<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_ProductVariation extends Widget_Base
{


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
        return 'product_variation';
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
        return PrestaHelper::__('Product Variation', 'elementor');
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
        return 'ceicon-product-variation-widget';
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
			'general',
			array(
				'label' => PrestaHelper::__( 'General', 'elecounter' ),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typo',
				'label'    => PrestaHelper::__( 'Label Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants .control-label',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'label_shadow',
				'label'    => PrestaHelper::__( 'Label Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants .control-label',
			]
		);

		$this->add_control(
			'label__color',
			array(
				'label'     => PrestaHelper::__( 'Label Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-variant .product-variants .control-label' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
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

        $this->add_responsive_control(
			'gap_between',
			[
				'label' => PrestaHelper::__( 'Gap Between', 'elementor' ),
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
				'default' => [
					'size' => '10',
				],
				'selectors' => [
					'.crazy-product-variant .product-variants' => 'gap: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after'
			]
		);


        $this->add_responsive_control(
			'crazy_single_product_variant_align',
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
					'{{WRAPPER}} .crazy-product-variant' => 'justify-content: {{VALUE}}',
				],
				'frontend_available' => true,
			]
		);
       
        
        $this->end_controls_section();

        $this->start_controls_section(
			'select_list_style_section',
			array(
				'label'      => PrestaHelper::__('Select List', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'select_list_typo',
                'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item select',
                'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
            )
        );

        $this->start_controls_tabs( 'select_list_style' );

		$this->start_controls_tab(
			'select_list_style_normal',
			array(
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			)
		);

        $this->add_control(
            'select_list_color',
            array(
                'label'     => PrestaHelper::__( 'Color', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'select_list_bg',
            array(
                'label'     => PrestaHelper::__( 'Background', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select' => 'background: {{VALUE}};',
                ),
                'separator' => 'after',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'select_list_border',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item select',
            )
        );

        $this->add_responsive_control(
            'select_list_radius',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'select_list_shadow',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item select',
            ]
        );

		$this->end_controls_tab();

       $this->start_controls_tab(
			'select_list_style_hover',
			array(
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			)
		);
        $this->add_control(
            'select_list_color_hover',
            array(
                'label'     => PrestaHelper::__( 'Color', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'select_list_bg_hover',
            array(
                'label'     => PrestaHelper::__( 'Background', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select:hover' => 'background: {{VALUE}};',
                ),
                'separator' => 'after',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'select_list_border_hover',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item select:hover',
            )
        );

        $this->add_responsive_control(
            'select_list_radius_hover',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item select:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'select_list_shadow_hover',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item select:hover',
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->end_controls_section();

        $this->start_controls_section(
			'color_btn_style_section',
			array(
				'label'      => PrestaHelper::__('Color Buttons', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_responsive_control(
			'color_btn_height',
			[
				'label' => PrestaHelper::__( 'Height', 'elementor' ),
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
				'default' => [
					'size' => '34',
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-product-variant .product-variants-item .color' => 'height: {{SIZE}}{{UNIT}}'
				],
			]
		);

        $this->add_responsive_control(
			'color_btn_width',
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
				'default' => [
					'size' => '34',
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-product-variant .product-variants-item .color' => 'width: {{SIZE}}{{UNIT}}'
				],
				'separator' => 'after'
			]
		);

        $this->start_controls_tabs( 'color_btn_style' );

		$this->start_controls_tab(
			'color_btn_style_normal',
			array(
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			)
		);


        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'color_btn_border',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .color',
            )
        );

        $this->add_responsive_control(
            'color_btn_radius',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'color_btn_shadow',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .color',
            ]
        );

		$this->end_controls_tab();

       $this->start_controls_tab(
			'color_btn_style_hover',
			array(
				'label' => PrestaHelper::__( 'Hover \ Active', 'elementor' ),
			)
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'color_btn_border_hover',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .color:hover, {{WRAPPER}} .crazy-product-variant .product-variants-item .input-color:checked + .color',
            )
        );

        $this->add_responsive_control(
            'color_btn_radius_hover',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .color:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-color:checked + .color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'color_btn_shadow_hover',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .color:hover',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
		$this->end_controls_section();

        $this->start_controls_section(
			'radio_btns_style_section',
			array(
				'label'      => PrestaHelper::__('Radio Buttons', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_responsive_control(
			'radio_btn_height',
			[
				'label' => PrestaHelper::__( 'Height', 'elementor' ),
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
				'default' => [
					'size' => '34',
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label' => 'height: {{SIZE}}{{UNIT}}'
				],
			]
		);

        $this->add_responsive_control(
			'radio_btn_width',
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
				'default' => [
					'size' => '34',
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label' => 'width: {{SIZE}}{{UNIT}}'
				],
				'separator' => 'after'
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'radio_btn_typo',
                'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label',
                'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
            )
        );

        $this->start_controls_tabs( 'radio_btns_style' );

		$this->start_controls_tab(
			'radio_btns_style_normal',
			array(
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			)
		);

        $this->add_control(
            'radio_btns_color',
            array(
                'label'     => PrestaHelper::__( 'Color', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'radio_btns_bg',
            array(
                'label'     => PrestaHelper::__( 'Background', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label' => 'background: {{VALUE}};',
                ),
                'separator' => 'after',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'radio_btns_border',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label',
            )
        );

        $this->add_responsive_control(
            'radio_btns_radius',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'radio_btns_shadow',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .radio-label',
            ]
        );

		$this->end_controls_tab();

       $this->start_controls_tab(
			'radio_btns_style_hover',
			array(
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			)
		);
        $this->add_control(
            'radio_btns_color_hover',
            array(
                'label'     => PrestaHelper::__( 'Color', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:hover + span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:checked + .radio-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'radio_btns_bg_hover',
            array(
                'label'     => PrestaHelper::__( 'Background', 'elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:hover + span' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:checked + .radio-label' => 'background: {{VALUE}};',
                ),
                'separator' => 'after',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'radio_btns_border_hover',
                'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:hover + span, {{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:checked + .radio-label',
            )
        );

        $this->add_responsive_control(
            'radio_btns_radius_hover',
            [
                'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:hover + span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:checked + .radio-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'radio_btns_shadow_hover',
                'selector' => '{{WRAPPER}} .crazy-product-variant .product-variants-item .input-radio:hover + span',
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
    protected function render()
    {

        if ( PrestaHelper::is_admin() ) {
			return;
		}
        $settings       = $this->get_settings_for_display();
        $orientation        = $settings['orientation'];

        $context = \Context::getContext();
        $id_lang = $context->language->id;

        $controller_name = \Tools::getValue('controller');
        if ($controller_name == "product") {
            $context->smarty->assign(
                array(
                    'orientation' => $orientation,
                )
            );
            $out_put = '';
            $out_put .= $context->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_variants.tpl');
            echo $out_put;
        } else {
            if (isset($_GET['prdid'])) {
                $id_product = (int)$_GET['prdid'];
            }
            $product = new \Product( $id_product, true, $id_lang );
            $combinations_groups = $product->getAttributesGroups($id_lang);
            
            $groups = array();
            $unique = array();
            foreach($combinations_groups as $group){
                $groups[$group['id_attribute_group']]['group_name'] = $group['group_name'];
                $groups[$group['id_attribute_group']]['name'] = $group['public_group_name'];
                $groups[$group['id_attribute_group']]['group_type'] = $group['group_type'];
                if( $group['default_on']){
                    $selected = 1;
                    $groups[$group['id_attribute_group']]['default'] = $group['id_attribute'];
                }else{
                    $groups[$group['id_attribute_group']]['default'] = '';
                    $selected = 0;
                }
                if(!in_array($group['attribute_name'], $unique)){
                    $groups[$group['id_attribute_group']]['attributes'][] = array(
                        'name' => $group['attribute_name'],
                        'html_color_code' => $group['attribute_color'], 
                        'texture' => '',
                        'selected' => $selected,
                    );
                    $unique[] = $group['attribute_name'];
                }
            }
            $context->smarty->assign(
                array(
                    'groups' => $groups,
                    'orientation' => $orientation,
                )
            );
			$out_put = '';
            $out_put .= $context->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_variants.tpl');
            echo $out_put;
        }
    }
}