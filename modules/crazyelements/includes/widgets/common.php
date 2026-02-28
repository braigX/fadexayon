<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper; if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_Common extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve common widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'common';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the common widget in the panel or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether to show the widget in the panel.
	 */
	public function show_in_panel() {
		return false;
	}

	/**
	 * Register common widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'_section_style',
			[
				'label' => PrestaHelper::__( 'Advanced', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'_title',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'render_type' => 'none',
			]
		);

		$this->add_responsive_control(
			'_margin',
			[
				'label' => PrestaHelper::__( 'Margin', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_z_index',
			[
				'label' => PrestaHelper::__( 'Z-Index', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => [
					'{{WRAPPER}}' => 'z-index: {{VALUE}};',
				],
				'label_block' => false,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'_element_id',
			[
				'label' => PrestaHelper::__( 'CSS ID', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => PrestaHelper::__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor' ),
				'label_block' => false,
				'style_transfer' => false,
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->add_control(
			'_css_classes',
			[
				'label' => PrestaHelper::__( 'CSS Classes', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'prefix_class' => '',
				'title' => PrestaHelper::__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_effects',
			[
				'label' => PrestaHelper::__( 'Motion Effects', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$fields = [
			'motion_fx_scrolling' => [
				'label' => PrestaHelper::__( 'Scrolling Effects', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => PrestaHelper::__( 'Off', 'elementor-pro' ),
				'label_on' => PrestaHelper::__( 'On', 'elementor-pro' ),
				'render_type' => 'ui',
				'frontend_available' => true,
			],
		];

		$this->prepare_effects( 'scrolling', $fields );

		$transform_origin_conditions = [
			'terms' => [
				[
					'name' => 'motion_fx_scrolling',
					'value' => 'yes',
				],
				[
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'rotateZ_effect',
							'value' => 'yes',
						],
						[
							'name' => 'scale_effect',
							'value' => 'yes',
						],
					],
				],
			],
		];
		$fields['transform_origin_x'] = [
			'label' => PrestaHelper::__( 'X Anchor Point', 'elementor-pro' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => 'center',
			'options' => [
				'left' => [
					'title' => PrestaHelper::__( 'Left', 'elementor-pro' ),
					'icon' => 'ceicon-h-align-left',
				],
				'center' => [
					'title' => PrestaHelper::__( 'Center', 'elementor-pro' ),
					'icon' => 'ceicon-h-align-center',
				],
				'right' => [
					'title' => PrestaHelper::__( 'Right', 'elementor-pro' ),
					'icon' => 'ceicon-h-align-right',
				],
			],
			'conditions' => $transform_origin_conditions,
			'toggle' => false,
			'render_type' => 'ui',
			'selectors' => [
				'{{SELECTOR}}' => '--e-transform-origin-x: {{VALUE}}',
			],
		];

		$fields['transform_origin_y'] = [
			'label' => PrestaHelper::__( 'Y Anchor Point', 'elementor-pro' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => 'center',
			'options' => [
				'top' => [
					'title' => PrestaHelper::__( 'Top', 'elementor-pro' ),
					'icon' => 'ceicon-v-align-top',
				],
				'center' => [
					'title' => PrestaHelper::__( 'Center', 'elementor-pro' ),
					'icon' => 'ceicon-v-align-middle',
				],
				'bottom' => [
					'title' => PrestaHelper::__( 'Bottom', 'elementor-pro' ),
					'icon' => 'ceicon-v-align-bottom',
				],
			],
			'conditions' => $transform_origin_conditions,
			'selectors' => [
				'{{SELECTOR}}' => '--e-transform-origin-y: {{VALUE}}',
			],
			'toggle' => false,
		];

		
		$this->add_responsive_control(
			'_animation',
			[
				'label' => PrestaHelper::__( 'Entrance Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label' => PrestaHelper::__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'slow' => PrestaHelper::__( 'Slow', 'elementor' ),
					'' => PrestaHelper::__( 'Normal', 'elementor' ),
					'fast' => PrestaHelper::__( 'Fast', 'elementor' ),
				],
				'prefix_class' => 'animated-',
				'condition' => [
					'_animation!' => '',
				],
			]
		);

		$this->add_control(
			'_animation_delay',
			[
				'label' => PrestaHelper::__( 'Animation Delay', 'elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'condition' => [
					'_animation!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_transform',
			[
				'label' => PrestaHelper::__( 'Transform', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_positioning' );

		$transform_prefix_class = 'e-';
		$transform_return_value = 'transform';

		foreach ( [ '', '_hover' ] as $tab ) {
			$state = '_hover' === $tab ? ':hover' : '';

			$this->start_controls_tab(
				"_tab_positioning{$tab}",
				[
					'label' => '' === $tab ? PrestaHelper::__( 'Normal', 'elementor' ) : PrestaHelper::__( 'Hover', 'elementor' ),
				]
			);

			$this->add_control(
				"_transform_rotate_popover{$tab}",
				[
					'label' => PrestaHelper::__( 'Rotate', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_rotateZ_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Rotate', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-rotateZ: {{SIZE}}deg',
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				"_transform_rotate_3d{$tab}",
				[
					'label' => PrestaHelper::__( '3D Rotate', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => PrestaHelper::__( 'On', 'elementor' ),
					'label_off' => PrestaHelper::__( 'Off', 'elementor' ),
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-rotateX: 1deg;  --e-transform-perspective: 20px;',
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
					],
				]
			);

			$this->add_responsive_control(
				"_transform_rotateX_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Rotate X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_rotate_3d{$tab}!" => '',
						"_transform_rotate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-rotateX: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_rotateY_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Rotate Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_rotate_3d{$tab}!" => '',
						"_transform_rotate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-rotateY: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_perspective_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Perspective', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
						"_transform_rotate_3d{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-perspective: {{SIZE}}px',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_translate_popover{$tab}",
				[
					'label' => PrestaHelper::__( 'Offset', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_translateX_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Offset X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range' => [
						'%' => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_translate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-translateX: {{SIZE}}{{UNIT}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_translateY_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Offset Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range' => [
						'%' => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_translate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-translateY: {{SIZE}}{{UNIT}};',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_scale_popover{$tab}",
				[
					'label' => PrestaHelper::__( 'Scale', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_control(
				"_transform_keep_proportions{$tab}",
				[
					'label' => PrestaHelper::__( 'Keep Proportions', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => PrestaHelper::__( 'On', 'elementor' ),
					'label_off' => PrestaHelper::__( 'Off', 'elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_responsive_control(
				"_transform_scale_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Scale', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-scale: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_scaleX_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Scale X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-scaleX: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_scaleY_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Scale Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-scaleY: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_skew_popover{$tab}",
				[
					'label' => PrestaHelper::__( 'Skew', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_skewX_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Skew X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_skew_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-skewX: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_skewY_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Skew Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_skew_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-skewY: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_flipX_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Flip Horizontal', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'transform' => [
							'title' => PrestaHelper::__( 'Flip Horizontal', 'elementor' ),
							'icon' => 'ceicon-flip ceicon-tilted',
						],
					],
					'prefix_class' => $transform_prefix_class,
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-flipX: -1',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				"_transform_flipY_effect{$tab}",
				[
					'label' => PrestaHelper::__( 'Flip Vertical', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'transform' => [
							'title' => PrestaHelper::__( 'Flip Vertical', 'elementor' ),
							'icon' => 'ceicon-flip',
						],
					],
					'prefix_class' => $transform_prefix_class,
					'selectors' => [
						"{{WRAPPER}} > .elementor-widget-container{$state}" => '--e-transform-flipY: -1',
					],
					'frontend_available' => true,
				]
			);

			if ( '_hover' === $tab ) {
				$this->add_control(
					'_transform_transition_hover',
					[
						'label' => PrestaHelper::__( 'Transition Duration (ms)', 'elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min' => 100,
								'max' => 10000,
							],
						],
						'selectors' => [
							'{{WRAPPER}} > .elementor-widget-container' => '--e-transform-transition-duration: {{SIZE}}ms',
						],
					]
				);
			}

			${"transform_origin_conditions{$tab}"} = [
				[
					'name' => "_transform_scale_popover{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_rotate_popover{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_flipX_effect{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_flipY_effect{$tab}",
					'operator' => '!=',
					'value' => '',
				],
			];

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$transform_origin_conditions = [
			'relation' => 'or',
			'terms' => array_merge( $transform_origin_conditions, $transform_origin_conditions_hover ),
		];

		// Will override motion effect transform-origin
		$this->add_responsive_control(
			'motion_fx_transform_x_anchor_point',
			[
				'label' => PrestaHelper::__( 'X Anchor Point', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => PrestaHelper::__( 'Left', 'elementor' ),
						'icon' => 'ceicon-h-align-left',
					],
					'center' => [
						'title' => PrestaHelper::__( 'Center', 'elementor' ),
						'icon' => 'ceicon-h-align-center',
					],
					'right' => [
						'title' => PrestaHelper::__( 'Right', 'elementor' ),
						'icon' => 'ceicon-h-align-right',
					],
				],
				'conditions' => $transform_origin_conditions,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--e-transform-origin-x: {{VALUE}}',
				],
			]
		);

		// Will override motion effect transform-origin
		$this->add_responsive_control(
			'motion_fx_transform_y_anchor_point',
			[
				'label' => PrestaHelper::__( 'Y Anchor Point', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => PrestaHelper::__( 'Top', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'center' => [
						'title' => PrestaHelper::__( 'Center', 'elementor' ),
						'icon' => 'ceicon-v-align-middle',
					],
					'bottom' => [
						'title' => PrestaHelper::__( 'Bottom', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'conditions' => $transform_origin_conditions,
				'selectors' => [
					'{{WRAPPER}}' => '--e-transform-origin-y: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_background',
			[
				'label' => PrestaHelper::__( 'Background', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_background' );

		$this->start_controls_tab(
			'_tab_background_normal',
			[
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => '_background',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_background_hover',
			[
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => '_background_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_control(
			'_background_hover_transition',
			[
				'label' => PrestaHelper::__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'render_type' => 'ui',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_border',
			[
				'label' => PrestaHelper::__( 'Border', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_border' );

		$this->start_controls_tab(
			'_tab_border_normal',
			[
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_border',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->add_responsive_control(
			'_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_box_shadow',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_border_hover',
			[
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_border_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_responsive_control(
			'_border_radius_hover',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}}:hover > .elementor-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_box_shadow_hover',
				'selector' => '{{WRAPPER}}:hover .elementor-widget-container',
			]
		);

		$this->add_control(
			'_border_hover_transition',
			[
				'label' => PrestaHelper::__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'transition: background {{_background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_position',
			[
				'label' => PrestaHelper::__( 'Custom Positioning', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_responsive_control(
			'_element_width',
			[
				'label' => PrestaHelper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => PrestaHelper::__( 'Default', 'elementor' ),
					'inherit' => PrestaHelper::__( 'Full Width', 'elementor' ) . ' (100%)',
					'auto' => PrestaHelper::__( 'Inline', 'elementor' ) . ' (auto)',
					'initial' => PrestaHelper::__( 'Custom', 'elementor' ),
				],
				'selectors_dictionary' => [
					'inherit' => '100%',
				],
				'prefix_class' => 'elementor-widget%s__width-',
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{VALUE}}; max-width: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'_element_custom_width',
			[
				'label' => PrestaHelper::__( 'Custom Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'_element_width' => 'initial',
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'condition' => [
							'_element_width_tablet' => [ 'initial' ],
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'condition' => [
							'_element_width_mobile' => [ 'initial' ],
						],
					],
				],
				'size_units' => [ 'px', '%', 'vw' ],
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'_element_vertical_align',
			[
				'label' => PrestaHelper::__( 'Vertical Align', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => PrestaHelper::__( 'Start', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'center' => [
						'title' => PrestaHelper::__( 'Center', 'elementor' ),
						'icon' => 'ceicon-v-align-middle',
					],
					'flex-end' => [
						'title' => PrestaHelper::__( 'End', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'condition' => [
					'_element_width!' => '',
					'_position' => '',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'align-self: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'_position_description',
			[
				'raw' => '<strong>' . PrestaHelper::__( 'Please note!', 'elementor' ) . '</strong> ' . PrestaHelper::__( 'Custom positioning is not considered best practice for responsive web design and should not be used too frequently.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_control(
			'_position',
			[
				'label' => PrestaHelper::__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => PrestaHelper::__( 'Default', 'elementor' ),
					'absolute' => PrestaHelper::__( 'Absolute', 'elementor' ),
					'fixed' => PrestaHelper::__( 'Fixed', 'elementor' ),
				],
				'prefix_class' => 'elementor-',
				'frontend_available' => true,
			]
		);

		$start = PrestaHelper::is_rtl() ? PrestaHelper::__( 'Right', 'elementor' ) : PrestaHelper::__( 'Left', 'elementor' );
		$end = ! PrestaHelper::is_rtl() ? PrestaHelper::__( 'Right', 'elementor' ) : PrestaHelper::__( 'Left', 'elementor' );

		$this->add_control(
			'_offset_orientation_h',
			[
				'label' => PrestaHelper::__( 'Horizontal Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => $start,
						'icon' => 'ceicon-h-align-left',
					],
					'end' => [
						'title' => $end,
						'icon' => 'ceicon-h-align-right',
					],
				],
				'classes' => 'elementor-control-start-end',
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x',
			[
				'label' => PrestaHelper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h!' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x_end',
			[
				'label' => PrestaHelper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 0.1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_control(
			'_offset_orientation_v',
			[
				'label' => PrestaHelper::__( 'Vertical Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => PrestaHelper::__( 'Top', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'end' => [
						'title' => PrestaHelper::__( 'Bottom', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y',
			[
				'label' => PrestaHelper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v!' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y_end',
			[
				'label' => PrestaHelper::__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v' => 'end',
					'_position!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_responsive',
			[
				'label' => PrestaHelper::__( 'Responsive', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'responsive_description',
			[
				'raw' => PrestaHelper::__( 'Responsive visibility will take effect only on preview or live page, and not while editing in Elementor.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'hide_desktop',
			[
				'label' => PrestaHelper::__( 'Hide On Desktop', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-desktop',
			]
		);

		$this->add_control(
			'hide_tablet',
			[
				'label' => PrestaHelper::__( 'Hide On Tablet', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-tablet',
			]
		);

		$this->add_control(
			'hide_mobile',
			[
				'label' => PrestaHelper::__( 'Hide On Mobile', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'elementor-',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'hidden-phone',
			]
		);

		$this->end_controls_section();

		Plugin::$instance->controls_manager->add_custom_css_controls( $this );
	}
	
	private function prepare_effects( $effects_group, array &$fields ) {
		$method_name = "get_{$effects_group}_effects";

		$effects = $this->$method_name();

		foreach ( $effects as $effect_name => $effect_args ) {
			$args = [
				'label' => $effect_args['label'],
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'motion_fx_' . $effects_group => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			];

			if ( ! empty( $effect_args['separator'] ) ) {
				$args['separator'] = $effect_args['separator'];
			}

			$fields[ $effect_name . '_effect' ] = $args;

			$effect_fields = $effect_args['fields'];

			$first_field = & $effect_fields[ key( $effect_fields ) ];

			$first_field['popover']['start'] = true;

			end( $effect_fields );

			$last_field = & $effect_fields[ key( $effect_fields ) ];

			$last_field['popover']['end'] = true;

			reset( $effect_fields );

			foreach ( $effect_fields as $field_name => $field ) {
				$field = array_merge( $field, [
					'condition' => [
						'motion_fx_' . $effects_group => 'yes',
						$effect_name . '_effect' => 'yes',
					],
					'render_type' => 'none',
					'frontend_available' => true,
				] );

				$fields[ $effect_name . '_' . $field_name ] = $field;
			}
		}
	}

	private function get_scrolling_effects() {
		return [
			'translateY' => [
				'label' => PrestaHelper::__( 'Vertical Scroll', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => PrestaHelper::__( 'Up', 'elementor-pro' ),
							'negative' => PrestaHelper::__( 'Down', 'elementor-pro' ),
						],
					],
					'speed' => [
						'label' => PrestaHelper::__( 'Speed', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
						],
						'range' => [
							'px' => [
								'max' => 10,
								'step' => 0.1,
							],
						],
					],
					'affectedRange' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 0,
								'end' => 100,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
			'translateX' => [
				'label' => PrestaHelper::__( 'Horizontal Scroll', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => PrestaHelper::__( 'To Left', 'elementor-pro' ),
							'negative' => PrestaHelper::__( 'To Right', 'elementor-pro' ),
						],
					],
					'speed' => [
						'label' => PrestaHelper::__( 'Speed', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
						],
						'range' => [
							'px' => [
								'max' => 10,
								'step' => 0.1,
							],
						],
					],
					'affectedRange' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 0,
								'end' => 100,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
			'opacity' => [
				'label' => PrestaHelper::__( 'Transparency', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'out-in',
						'options' => [
							'out-in' => 'Fade In',
							'in-out' => 'Fade Out',
							'in-out-in' => 'Fade Out In',
							'out-in-out' => 'Fade In Out',
						],
					],
					'level' => [
						'label' => PrestaHelper::__( 'Level', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 10,
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 10,
								'step' => 0.1,
							],
						],
					],
					'range' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 20,
								'end' => 80,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
			'blur' => [
				'label' => PrestaHelper::__( 'Blur', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'out-in',
						'options' => [
							'out-in' => 'Fade In',
							'in-out' => 'Fade Out',
							'in-out-in' => 'Fade Out In',
							'out-in-out' => 'Fade In Out',
						],
					],
					'level' => [
						'label' => PrestaHelper::__( 'Level', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 7,
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 15,
							],
						],
					],
					'range' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 20,
								'end' => 80,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
			'rotateZ' => [
				'label' => PrestaHelper::__( 'Rotate', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => PrestaHelper::__( 'To Left', 'elementor-pro' ),
							'negative' => PrestaHelper::__( 'To Right', 'elementor-pro' ),
						],
					],
					'speed' => [
						'label' => PrestaHelper::__( 'Speed', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
						],
						'range' => [
							'px' => [
								'max' => 10,
								'step' => 0.1,
							],
						],
					],
					'affectedRange' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 0,
								'end' => 100,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
			'scale' => [
				'label' => PrestaHelper::__( 'Scale', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'out-in',
						'options' => [
							'out-in' => 'Scale Up',
							'in-out' => 'Scale Down',
							'in-out-in' => 'Scale Down Up',
							'out-in-out' => 'Scale Up Down',
						],
					],
					'speed' => [
						'label' => PrestaHelper::__( 'Speed', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
						],
						'range' => [
							'px' => [
								'min' => -10,
								'max' => 10,
							],
						],
					],
					'range' => [
						'label' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'sizes' => [
								'start' => 20,
								'end' => 80,
							],
							'unit' => '%',
						],
						'labels' => [
							PrestaHelper::__( 'Bottom', 'elementor-pro' ),
							PrestaHelper::__( 'Top', 'elementor-pro' ),
						],
						'scales' => 1,
						'handles' => 'range',
					],
				],
			],
		];
	}
}