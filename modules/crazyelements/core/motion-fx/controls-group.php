<?php

namespace CrazyElements\Core\MotionFX;

use CrazyElements\PrestaHelper; 

use CrazyElements\Controls_Manager;
use CrazyElements\Group_Control_Base;
use CrazyElements\Plugin;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Controls_Group extends Group_Control_Base {

	protected static $fields;

	/**
	 * Get group control type.
	 *
	 * Retrieve the group control type.
	 *
	 * @since  2.5.0
	 * @access public
	 * @static
	 */
	public static function get_type() {
		return 'motion_fx';
	}

	/**
	 * Init fields.
	 *
	 * Initialize group control fields.
	 *
	 * @since  2.5.0
	 * @access protected
	 */
	protected function init_fields() {
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


        $css_transform_controls = [
            [
                'name' => 'transform_x_anchor_point',
                'value' => '',
            ],
            [
                'name' => 'transform_y_anchor_point',
                'value' => '',
            ],
        ];

        $transform_origin_conditions['terms'] = array_merge( $transform_origin_conditions['terms'], $css_transform_controls );

		$fields['transform_origin_x'] = [
			'label' => PrestaHelper::__( 'X Anchor Point', 'elementor-pro' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => 'center',
			'options' => [
				'left' => [
					'title' => PrestaHelper::__( 'Left', 'elementor-pro' ),
					'icon' => 'eicon-h-align-left',
				],
				'center' => [
					'title' => PrestaHelper::__( 'Center', 'elementor-pro' ),
					'icon' => 'eicon-h-align-center',
				],
				'right' => [
					'title' => PrestaHelper::__( 'Right', 'elementor-pro' ),
					'icon' => 'eicon-h-align-right',
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
					'icon' => 'eicon-v-align-top',
				],
				'center' => [
					'title' => PrestaHelper::__( 'Center', 'elementor-pro' ),
					'icon' => 'eicon-v-align-middle',
				],
				'bottom' => [
					'title' => PrestaHelper::__( 'Bottom', 'elementor-pro' ),
					'icon' => 'eicon-v-align-bottom',
				],
			],
			'conditions' => $transform_origin_conditions,
			'selectors' => [
				'{{SELECTOR}}' => '--e-transform-origin-y: {{VALUE}}',
			],
			'toggle' => false,
		];

		$fields['devices'] = [
			'label' => PrestaHelper::__( 'Apply Effects On', 'elementor-pro' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'label_block' => true,
			'default' => ['desktop', 'tablet', 'mobile'],
				'options' => array(
					'desktop' => 'Desktop',
					'tablet' => 'Tablet',
					'mobile' => 'Mobile'
				),
			'condition' => [
				'motion_fx_scrolling' => 'yes',
			],
			'render_type' => 'none',
			'frontend_available' => true,
		];

		$fields['range'] = [
			'label' => PrestaHelper::__( 'Effects Relative To', 'elementor-pro' ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'' => PrestaHelper::__( 'Default', 'elementor-pro' ),
				'viewport' => PrestaHelper::__( 'Viewport', 'elementor-pro' ),
				'page' => PrestaHelper::__( 'Entire Page', 'elementor-pro' ),
			],
			'condition' => [
				'motion_fx_scrolling' => 'yes',
			],
			'render_type' => 'none',
			'frontend_available' => true,
		];

		$fields['motion_fx_mouse'] = [
			'label' => PrestaHelper::__( 'Mouse Effects', 'elementor-pro' ),
			'type' => Controls_Manager::SWITCHER,
			'label_off' => PrestaHelper::__( 'Off', 'elementor-pro' ),
			'label_on' => PrestaHelper::__( 'On', 'elementor-pro' ),
			'separator' => 'before',
			'render_type' => 'none',
			'frontend_available' => true,
		];

		$this->prepare_effects( 'mouse', $fields );

		return $fields;
	}

	protected function get_default_options() {
		return [
			'popover' => false,
		];
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

	private function get_mouse_effects() {
		return [
			'mouseTrack' => [
				'label' => PrestaHelper::__( 'Mouse Track', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => [
							'' => PrestaHelper::__( 'Opposite', 'elementor-pro' ),
							'negative' => PrestaHelper::__( 'Direct', 'elementor-pro' ),
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
				],
			],
			'tilt' => [
				'label' => PrestaHelper::__( '3D Tilt', 'elementor-pro' ),
				'fields' => [
					'direction' => [
						'label' => PrestaHelper::__( 'Direction', 'elementor-pro' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => [
							'' => PrestaHelper::__( 'Direct', 'elementor-pro' ),
							'negative' => PrestaHelper::__( 'Opposite', 'elementor-pro' ),
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
				],
			],
		];
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
}
