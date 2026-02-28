<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_ProductInformation extends Widget_Base {


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
		return 'product_info';
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
		return PrestaHelper::__( 'Product Info', 'elementor' );
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
		return 'ceicon-product-information-widget';
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
			'connector',
			array(
				'label'       => PrestaHelper::__( 'Connector', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
                'default' => ': ',
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


        $repeater = new Repeater();

        $repeater->add_control(
			'label',
			array(
				'label'       => PrestaHelper::__( 'Label', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

		$repeater->add_control(
            'info_type',
            array(
                'label'   => PrestaHelper::__('Type', 'elementor'),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'category'   => PrestaHelper::__('Category', 'elementor'),
                    'brand'   => PrestaHelper::__('Brand / Manufacturer', 'elementor'),
                    'supplier'   => PrestaHelper::__('Main Supplier', 'elementor'),
                    'quantity'   => PrestaHelper::__('Quantity in Stock', 'elementor'),
                    'availability_date'   => PrestaHelper::__('Availability Date', 'elementor'),
                    'reference'   => PrestaHelper::__('Reference', 'elementor')
                )
            )
        );

        $repeater->add_control(
            'show_link',
            array(
                'label'   => PrestaHelper::__( 'Show Link?', 'elementor' ),
                'type'    => Controls_Manager::SWITCHER,
                'dynamic' => array(
                    'active' => true,
                ),
                'default' => false,
                'conditions' => array(
                    'relation' => 'or',
                    'terms'    => array(
                        array(
                            'name'     => 'info_type',
                            'operator' => '==',
                            'value'    => 'category',
                        ),
                        array(
                            'name'     => 'info_type',
                            'operator' => '==',
                            'value'    => 'brand',
                        ),
                        array(
                            'name'     => 'info_type',
                            'operator' => '==',
                            'value'    => 'supplier',
                        ),
                    ),
                )
            )
        );

		$repeater->add_control(
			'suffix',
			array(
				'label'       => PrestaHelper::__( 'Suffix', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'conditions' => array(
                    'relation' => 'and',
                    'terms'    => array(
                        array(
                            'name'     => 'info_type',
                            'operator' => '==',
                            'value'    => 'quantity',
                        )
                    ),
                )
			)
		);

        $this->add_control(
            'info_items',
            [
                'label' => PrestaHelper::__('List Items', 'elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'info_type' => 'category',
                    ]
                ]
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
			'style_section',
			array(
				'label'      => PrestaHelper::__( 'General Style', 'elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE
			)
		);

        $this->add_responsive_control(
			'section_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->add_control(
            'info_alignment',
            [
                'label' => PrestaHelper::__('Alignment', 'elementor'),
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
                    '{{WRAPPER}} .crazy-product-info' => 'justify-content: {{VALUE}}',
                ],
                'separator' => 'before'
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
					'{{WRAPPER}} .crazy-product-info' => 'gap: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after'
			]
		);

        $this->add_control(
			'section_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-info' => 'background: {{VALUE}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-info',
			)
		);

        $this->add_responsive_control(
			'section_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->end_controls_section();


        $this->item_styles('category', 'Category Style');
        $this->item_styles('brand', 'Brand Style');
        $this->item_styles('supplier', 'Supplier Style');
        $this->item_styles('quantity', 'Quantity Style');
        $this->item_styles('availability_date', 'Availability Style');
        $this->item_styles('reference', 'Reference Style');
        
	}

    private function item_styles($pref, $label){
        $this->start_controls_section(
			$pref. '_style',
			array(
				'label'      => $label,
				'tab'        => Controls_Manager::TAB_STYLE
			)
		);

        $this->add_responsive_control(
			$pref . '_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->add_control(
            $pref . '_alignment',
            [
                'label' => PrestaHelper::__('Alignment', 'elementor'),
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
                    '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref => 'text-align: {{VALUE}}',
                ],
                'separator' => 'after'
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $pref . '_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref,
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

        $this->add_control(
			$pref . '_color',
			array(
				'label'     => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref => 'color: {{VALUE}};',
				),
			)
		);

        if($pref == 'category' || $pref == 'brand' || $pref == 'supplier'){
            $this->add_control(
                $pref . 'link_color',
                array(
                    'label'     => PrestaHelper::__( 'Link Color', 'elementor' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref. ' .crazy-prinfo-link-'.$pref => 'color: {{VALUE}};',
                    ),
                )
            );
            $this->add_control(
                $pref . 'link_hover_color',
                array(
                    'label'     => PrestaHelper::__( 'Link Hover Color', 'elementor' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref. ' .crazy-prinfo-link-'.$pref.':hover' => 'color: {{VALUE}};',
                    ),
                )
            );
        }

        $this->add_control(
			$pref . '_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref => 'background: {{VALUE}};',
				),
                'separator' => 'after'
			)
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $pref . '_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref,
			)
		);

        $this->add_responsive_control(
			$pref . '_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $pref . '_shadow',
				'selector' => '{{WRAPPER}} .crazy-product-info .crazy-product-'.$pref,
			)
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
	protected function render() {

		$settings     = $this->get_settings_for_display();
		$info_items        = $settings['info_items'];
		$connector        = $settings['connector'];
		$orientation        = $settings['orientation'];

		$controller_name = \Tools::getValue('controller');

        $out_put = '';
        $context = \Context::getContext();
        $id_lang = $context->language->id;

		if($controller_name == "product"){
			$id_product = (int)\Tools::getValue('id_product');
            $product = $context->controller->getProduct();
		} else {
            if (isset($_GET['prdid'])) {
                $id_product = (int)$_GET['prdid'];
            }
            $product = new \Product( $id_product, true, $id_lang );
		}
		if(isset($product->id)){
			echo '<div class="crazy-product-info crazy-product-info--'.$orientation.'">';
			foreach($info_items as $item){
				$label = $item['label'];
				$type = $item['info_type'];
				$value = '';
				if($label == ''){
					$label = ucwords(str_replace('_',' ',$item['info_type']));
				}
				if($type == 'category'){
					$catg_id = $product->id_category_default;
					$category = new \Category($catg_id, $id_lang);
					$value = $category->name;
					$show_link = $item['show_link'];
					if($show_link == 'yes'){
						$link = $context->link->getCategoryLink($catg_id, $category->link_rewrite);
						$value = '<a class="crazy-prinfo-link-'.$type.'" href="'.$link.'" target="_blank">'.$value.'</a>';
					}
				}elseif($type=='brand'){
					$value = $product->manufacturer_name;
					
					$show_link = $item['show_link'];
					if($show_link == 'yes'){
						$manu = new \Manufacturer($product->id_manufacturer, $id_lang);
						$link = $context->link->getManufacturerLink($manu->id, null, $id_lang);
						$value = '<a class="crazy-prinfo-link-'.$type.'" href="'.$link.'" target="_blank">'.$value.'</a>';
					}
				}elseif($type=='supplier'){
					$value = $product->supplier_name;
					$show_link = $item['show_link'];
					if($show_link == 'yes'){
						$supplier = new \Supplier($product->id_supplier, $id_lang);
						$link = $context->link->getSupplierLink( $supplier->id, null, $id_lang );
						$value = '<a class="crazy-prinfo-link-'.$type.'" href="'.$link.'" target="_blank">'.$value.'</a>';
					}
				}elseif($type=='quantity'){
					$suffix = $item['suffix'];
					$value = $product->quantity.$suffix;
				}elseif($type=='availability_date'){
					$value = $product->available_date;
				}elseif($type=='reference'){
					$value = $product->reference;
				}

				echo '<div class="crazy-product-info-item crazy-product-'.$type.'">';
				echo $label . $connector . $value;
				echo '</div>';
			}
			?>
			</div>
			<style>
				.crazy-product-info{
					display: flex;
				}
				.crazy-product-info--stacked{
					flex-direction: column;
				}
			</style>
			<?php 
		}else{
			echo 'No Product Found';
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