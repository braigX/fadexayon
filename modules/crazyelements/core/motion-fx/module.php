<?php
namespace CrazyElements\Core\MotionFx;

use CrazyElements\Plugin;
use CrazyElements\PrestaHelper; 
use CrazyElements\Controls_Manager;
use CrazyElements\Element_Base;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}
use CrazyElements\Core\Base\App as BaseApp;

class Module extends BaseApp{

	public function __construct()
    {
        $this->add_actions();
    }

	public function get_name() {
		return 'motion-fx';
	}

	private function is_instance_of( $element, array $types ) {
		foreach ( $types as $type ) {
			if ( $element instanceof $type ) {
				return true;
			}
		}

		return false;
	}

	public function add_motion_effects(){
		Plugin::crazyelements()->controls_manager->add_group_control( Controls_Group::get_type(), new Controls_Group() );
	}

	public function scrolling_effects( Element_Base $element ) {
		$exclude = [];

		$selector = '{{WRAPPER}}';

		$class_sec = "\\CrazyElements\\Element_Section";
		$class_col = "\\CrazyElements\\Element_Column";
		
		if ( $element instanceof $class_sec ) {
			$exclude[] = 'motion_fx_mouse';
		} elseif ( $element instanceof $class_col ) {
			$selector .= ' > .elementor-widget-wrap';
		} else {
			$selector .= ' > .elementor-widget-container';
		}

		$element->add_group_control(
			Controls_Group::get_type(),
			[
				'name' => 'motion_fx',
				'selector' => $selector,
				'exclude' => $exclude
			]
		);
	}

	public function sticky_controls($element){
		$element->add_control(
			'sticky',
			[
				'label' => PrestaHelper::__( 'Sticky', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => PrestaHelper::__( 'None', 'elementor-pro' ),
					'top' => PrestaHelper::__( 'Top', 'elementor-pro' ),
					'bottom' => PrestaHelper::__( 'Bottom', 'elementor-pro' ),
				],
				'separator' => 'before',
				'render_type' => 'none',
				'frontend_available' => true,
				'assets' => $this->get_asset_conditions_data(),
			]
		);


		$element->add_control(
			'sticky_on',
			[
				'label' => PrestaHelper::__( 'Sticky On', 'elementor-pro' ),
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
					'sticky!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_responsive_control(
			'sticky_offset',
			[
				'label' => PrestaHelper::__( 'Offset', 'elementor-pro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'max' => 500,
				'required' => true,
				'condition' => [
					'sticky!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_responsive_control(
			'sticky_effects_offset',
			[
				'label' => PrestaHelper::__( 'Effects Offset', 'elementor-pro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'max' => 1000,
				'required' => true,
				'condition' => [
					'sticky!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		// Add `Stay In Column` only to the following types:
		$types = [
			Element_Section::class,
			Widget_Base::class,
		];

		$class_sec = "\\CrazyElements\\Element_Section";
		$class_wid = "\\CrazyElements\\Widget_Base";

		$is_sec = $element instanceof $class_sec;
		$is_wid = $element instanceof $class_wid;

		if ( $is_sec ||  $is_wid ) {
			$conditions = [
				'sticky!' => '',
			];

			// Target only inner sections.
			// Checking for `$element->get_data( 'isInner' )` in both editor & frontend causes it to work properly on the frontend but
			// break on the editor, because the inner section is created in JS and not rendered in PHP.
			// So this is a hack to force the editor to show the `sticky_parent` control, and still make it work properly on the frontend.
			if ( $element instanceof Element_Section && Plugin::elementor()->editor->is_edit_mode() ) {
				$conditions['isInner'] = true;
			}

			$element->add_control(
				'sticky_parent',
				[
					'label' => PrestaHelper::__( 'Stay In Column', 'elementor-pro' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => $conditions,
					'render_type' => 'none',
					'frontend_available' => true,
				]
			);
		}

		$element->add_control(
			'sticky_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

	}
	private function get_asset_conditions_data() {
		return [
			'scripts' => [
				[
					'name' => 'e-sticky',
					'conditions' => [
						'terms' => [
							[
								'name' => 'sticky',
								'operator' => '!==',
								'value' => '',
							],
						],
					],
				],
			],
		];
	}

	/**
	 * @since 1.0.0
	 * @access private
	 */
	private function add_actions() {

		PrestaHelper::add_action( 'elementor/controls/controls_registered', [ $this, 'add_motion_effects' ] );

		PrestaHelper::add_action( 'elementor/element/section/section_effects/after_section_start', [ $this, 'scrolling_effects' ] );
		PrestaHelper::add_action( 'elementor/element/column/section_effects/after_section_start', [ $this, 'scrolling_effects' ] );
		PrestaHelper::add_action( 'elementor/element/common/section_effects/after_section_start', [ $this, 'scrolling_effects' ] );

		PrestaHelper::add_action('elementor/element/section/section_effects/after_section_start', [$this, 'sticky_controls']);
        PrestaHelper::add_action('elementor/element/common/section_effects/after_section_start', [$this, 'sticky_controls']);
	}
}
