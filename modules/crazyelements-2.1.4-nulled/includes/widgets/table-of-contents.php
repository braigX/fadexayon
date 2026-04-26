<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_Table_Of_Contents extends Widget_Base {

	public function get_name() {
		return 'table-of-contents';
	}

	public function get_title() {
		return PrestaHelper::__( 'Table of Contents', 'elementor' );
	}

	public function get_icon() {
		return 'ceicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'pro-elements', 'theme-elements-single' ];
	}

	public function get_keywords() {
		return [ 'toc' ];
	}

	/**
	 * Get Frontend Settings
	 *
	 * In the TOC widget, this implementation is used to pass a pre-rendered version of the icon to the front end,
	 * which is required in case the FontAwesome SVG experiment is active.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	public function get_frontend_settings() {
		$frontend_settings = parent::get_frontend_settings();

		// if ( Plugin::elementor()->experiments->is_feature_active( 'e_font_icon_svg' ) && ! empty( $frontend_settings['icon']['value'] ) ) {
		// 	$frontend_settings['icon']['rendered_tag'] = Icons_Manager::render_font_icon( $frontend_settings['icon'] );
		// }

		return $frontend_settings;
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'table_of_contents',
			[
				'label' => PrestaHelper::__( 'Table of Contents', 'elementor' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default' => PrestaHelper::__( 'Table of Contents', 'elementor' ),
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => PrestaHelper::__( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->start_controls_tabs( 'include_exclude_tags', [ 'separator' => 'before' ] );

		$this->start_controls_tab( 'include',
			[
				'label' => PrestaHelper::__( 'Include', 'elementor' ),
			]
		);

		$this->add_control(
			'headings_by_tags',
			[
				'label' => PrestaHelper::__( 'Anchors By Tags', 'elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => [ 'h2', 'h3', 'h4', 'h5', 'h6' ],
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'label_block' => true,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'container',
			[
				'label' => PrestaHelper::__( 'Container', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => PrestaHelper::__( 'This control confines the Table of Contents to heading elements under a specific container', 'elementor' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab(); // include

		$this->start_controls_tab( 'exclude',
			[
				'label' => PrestaHelper::__( 'Exclude', 'elementor' ),
			]
		);

		$this->add_control(
			'exclude_headings_by_selector',
			[
				'label' => PrestaHelper::__( 'Anchors By Selector', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => PrestaHelper::__( 'CSS selectors, in a comma-separated list', 'elementor' ),
				'default' => [],
				'label_block' => true,
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab(); // exclude

		$this->end_controls_tabs(); // include_exclude_tags

		$this->add_control(
			'marker_view',
			[
				'label' => PrestaHelper::__( 'Marker View', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'numbers',
				'options' => [
					'numbers' => PrestaHelper::__( 'Numbers', 'elementor' ),
					'bullets' => PrestaHelper::__( 'Bullets', 'elementor' ),
				],
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => PrestaHelper::__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition' => [
					'marker_view' => 'bullets',
				],
				'skin' => 'inline',
				'label_block' => false,
				'exclude_inline_options' => [ 'svg' ],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section(); // table_of_contents

		$this->start_controls_section(
			'additional_options',
			[
				'label' => PrestaHelper::__( 'Additional Options', 'elementor' ),
			]
		);

		$this->add_control(
			'word_wrap',
			[
				'label' => PrestaHelper::__( 'Word Wrap', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'ellipsis',
				'prefix_class' => 'elementor-toc--content-',
			]
		);

		$this->add_control(
			'minimize_box',
			[
				'label' => PrestaHelper::__( 'Minimize Box', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'expand_icon',
			[
				'label' => PrestaHelper::__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'minimize_box' => 'yes',
				],
			]
		);

		$this->add_control(
			'collapse_icon',
			[
				'label' => PrestaHelper::__( 'Minimize Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'minimize_box' => 'yes',
				],
			]
		);

		// TODO: For Pro 3.6.0, convert this to the breakpoints utility method introduced in core 3.5.0.
		// $breakpoints = Plugin::crazyelements()->breakpoints->get_active_breakpoints();

		$minimized_on_options = [];

		// foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
		// 	// This feature is meant for mobile screens.
		// 	if ( 'widescreen' === $breakpoint_key || 'laptop' === $breakpoint_key ) {
		// 		continue;
		// 	}

		// 	$minimized_on_options[ $breakpoint_key ] = sprintf(
		// 		/* translators: 1: `<` character, 2: Breakpoint value. */
		// 		PrestaHelper::__( '%1$s (%2$s %3$dpx)', 'elementor' ),
		// 		$breakpoint->get_label(),
		// 		'<',
		// 		$breakpoint->get_value()
		// 	);
		// }

		$this->add_control(
			'minimized_on',
			[
				'label' => PrestaHelper::__( 'Minimized On', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tablet',
				'options' => $minimized_on_options,
				'prefix_class' => 'elementor-toc--minimized-on-',
				'condition' => [
					'minimize_box!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'hierarchical_view',
			[
				'label' => PrestaHelper::__( 'Hierarchical View', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'collapse_subitems',
			[
				'label' => PrestaHelper::__( 'Collapse Subitems', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => PrestaHelper::__( 'The "Collapse" option should only be used if the Table of Contents is made sticky', 'elementor' ),
				'condition' => [
					'hierarchical_view' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section(); // settings

		$this->start_controls_section(
			'box_style',
			[
				'label' => PrestaHelper::__( 'Box', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--box-background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => PrestaHelper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--box-border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'loader_color',
			[
				'label' => PrestaHelper::__( 'Loader Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Not using CSS var for BC, when not configured: the loader should get the color from the body tag.
					'{{WRAPPER}} .elementor-toc__spinner' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => PrestaHelper::__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--box-border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--box-border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--box-padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label' => PrestaHelper::__( 'Min Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--box-min-height: {{SIZE}}{{UNIT}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}}',
			]
		);

		$this->end_controls_section(); // box_style

		$this->start_controls_section(
			'header_style',
			[
				'label' => PrestaHelper::__( 'Header', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_background_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--header-background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'header_text_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--header-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
                'label' => PrestaHelper::__( 'Header Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .elementor-toc__header, {{WRAPPER}} .elementor-toc__header-title',
			]
		);

		$this->add_control(
			'toggle_button_color',
			[
				'label' => PrestaHelper::__( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'minimize_box' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--toggle-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'header_separator_width',
			[
				'label' => PrestaHelper::__( 'Separator Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--separator-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section(); // header_style

		$this->start_controls_section(
			'list_style',
			[
				'label' => PrestaHelper::__( 'List', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'max_height',
			[
				'label' => PrestaHelper::__( 'Max Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--toc-body-max-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'list_typography',
                'label' => PrestaHelper::__( 'List Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .elementor-toc__list-item',
				
			]
		);

		$this->add_control(
			'list_indent',
			[
				'label' => PrestaHelper::__( 'Indent', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--nested-list-indent: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'item_text_style' );

		$this->start_controls_tab( 'normal',
			[
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'item_text_color_normal',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_text_underline_normal',
			[
				'label' => PrestaHelper::__( 'Underline', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-decoration: underline',
				],
			]
		);

		$this->end_controls_tab(); // normal

		$this->start_controls_tab( 'hover',
			[
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'item_text_color_hover',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-hover-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_text_underline_hover',
			[
				'label' => PrestaHelper::__( 'Underline', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-hover-decoration: underline',
				],
			]
		);

		$this->end_controls_tab(); // hover

		$this->start_controls_tab( 'active',
			[
				'label' => PrestaHelper::__( 'Active', 'elementor' ),
			]
		);

		$this->add_control(
			'item_text_color_active',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-active-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_text_underline_active',
			[
				'label' => PrestaHelper::__( 'Underline', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-active-decoration: underline',
				],
			]
		);

		$this->end_controls_tab(); // active

		$this->end_controls_tabs(); // item_text_style

		$this->add_control(
			'heading_marker',
			[
				'label' => PrestaHelper::__( 'Marker', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--marker-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'marker_size',
			[
				'label' => PrestaHelper::__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}}' => '--marker-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section(); // list_style
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'body', 'class', 'elementor-toc__body' );

		if ( $settings['collapse_subitems'] ) {
			$this->add_render_attribute( 'body', 'class', 'elementor-toc__list-items--collapsible' );
		}

		$html_tag =  $settings['html_tag'];
		?>
		<div class="elementor-toc__header">
			<<?php echo $html_tag; ?> class="elementor-toc__header-title">
				<?php $this->print_unescaped_setting( 'title' ); ?>
			</<?php echo $html_tag; ; ?>>
			<?php if ( 'yes' === $settings['minimize_box'] ) : ?>
				<div class="elementor-toc__toggle-button elementor-toc__toggle-button--expand"><?php Icons_Manager::render_icon( $settings['expand_icon'] ); ?></div>
				<div class="elementor-toc__toggle-button elementor-toc__toggle-button--collapse"><?php Icons_Manager::render_icon( $settings['collapse_icon'] ); ?></div>
			<?php endif; ?>
		</div>
		<div <?php $this->print_render_attribute_string( 'body' ); ?>>
			<div class="elementor-toc__spinner-container">
				<?php
					Icons_Manager::render_icon(
						[
							'library' => 'ceicons',
							'value' => 'ceicon-loading',
						],
						[
							'class' => [
								'elementor-toc__spinner',
								'ceicon-animation-spin',
							],
							'aria-hidden' => 'true',
						]
					); ?>
			</div>
		</div>
		<?php
	}
}
