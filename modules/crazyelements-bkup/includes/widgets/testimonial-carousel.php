<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base; 
if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_Testimonial_Carousel extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve testimonial widget name.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'testimonial-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve testimonial widget title.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return PrestaHelper::__( 'Testimonial Carousel', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve testimonial widget icon.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'ceicon-testimonial-carousel';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'testimonial', 'carousel', 'image' ];
	}

	/**
	 * Register testimonial widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function _register_controls() {
		parent::_register_controls();

		// $this->start_injection( [
		// 	'of' => 'slides',
		// ] );
		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__('General', 'elementor'),
			)
		);

		$this->add_control(
			'skin',
			[
				'label' => PrestaHelper::__( 'Skin', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => PrestaHelper::__( 'Default', 'elementor' ),
					'bubble' => PrestaHelper::__( 'Bubble', 'elementor' ),
				],
				'prefix_class' => 'elementor-testimonial--skin-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => PrestaHelper::__( 'Layout', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image_inline',
				'options' => [
					'image_inline' => PrestaHelper::__( 'Image Inline', 'elementor' ),
					'image_stacked' => PrestaHelper::__( 'Image Stacked', 'elementor' ),
					'image_above' => PrestaHelper::__( 'Image Above', 'elementor' ),
					'image_left' => PrestaHelper::__( 'Image Left', 'elementor' ),
					'image_right' => PrestaHelper::__( 'Image Right', 'elementor' ),
				],
				'prefix_class' => 'elementor-testimonial--layout-',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => PrestaHelper::__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
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
				'prefix_class' => 'elementor-testimonial--align-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_skin_style',
			[
				'label' => PrestaHelper::__( 'Bubble', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'bubble',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'alpha' => false,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content, {{WRAPPER}} .elementor-testimonial__content:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_left .elementor-testimonial__footer,
					{{WRAPPER}}.elementor-testimonial--layout-image_right .elementor-testimonial__footer' => 'padding-top: {{TOP}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_above .elementor-testimonial__footer,
					{{WRAPPER}}.elementor-testimonial--layout-image_inline .elementor-testimonial__footer,
					{{WRAPPER}}.elementor-testimonial--layout-image_stacked .elementor-testimonial__footer' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border',
			[
				'label' => PrestaHelper::__( 'Border', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content, {{WRAPPER}} .elementor-testimonial__content:after' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => PrestaHelper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-testimonial__content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => PrestaHelper::__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content, {{WRAPPER}} .elementor-testimonial__content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_stacked .elementor-testimonial__content:after,
					{{WRAPPER}}.elementor-testimonial--layout-image_inline .elementor-testimonial__content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_above .elementor-testimonial__content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_navigation',
		] );

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => PrestaHelper::__( 'Content', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label' => PrestaHelper::__( 'Gap', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-testimonial--layout-image_inline .elementor-testimonial__footer,
					{{WRAPPER}}.elementor-testimonial--layout-image_stacked .elementor-testimonial__footer' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_above .elementor-testimonial__footer' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_left .elementor-testimonial__footer' => 'padding-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_right .elementor-testimonial__footer' => 'padding-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__text' => 'color: {{VALUE}}',
				],
                'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '',
			]
		);

		//ISSUE_IN_ELEMENTOR_PRO
		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	array(
		// 		'name'     => 'content_typography',
		// 		'selector' => '{{WRAPPER}} .elementor-testimonial__text',
		// 		'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
		// 	)
		// );

		$this->add_control(
			'name_title_style',
			[
				'label' => PrestaHelper::__( 'Name', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__name' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '',
			]
		);

		//ISSUE_IN_ELEMENTOR_PRO
		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name' => 'name_typography',
		// 		'selector' => '{{WRAPPER}} .elementor-testimonial__name',
		// 		'global' => [
		// 			'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
		// 		],
		// 	]
		// );

		$this->add_control(
			'heading_title_style',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__title' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '',
			]
		);
		//ISSUE_IN_ELEMENTOR_PRO
		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name' => 'title_typography',
		// 		'selector' => '{{WRAPPER}} .elementor-testimonial__title',
		// 		'global' => [
		// 			'default' => Global_Typography::TYPOGRAPHY_1,
		// 		],
        //      'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		// 	]
		// );

        
		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => PrestaHelper::__( 'Image', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => PrestaHelper::__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-testimonial--layout-image_left .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_right .elementor-testimonial__content:after' => 'top: calc( {{text_padding.TOP}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px );',

					'body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_stacked:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-right) .elementor-testimonial__content:after,
					 body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_inline:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-right) .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_stacked.elementor-testimonial--align-left .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_inline.elementor-testimonial--align-left .elementor-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_stacked:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-left) .elementor-testimonial__content:after,
					 body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_inline:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-left) .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_stacked.elementor-testimonial--align-right .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_inline.elementor-testimonial--align-right .elementor-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',

					'body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_above:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-right) .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_above.elementor-testimonial--align-left .elementor-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_above:not(.elementor-testimonial--align-center):not(.elementor-testimonial--align-left) .elementor-testimonial__content:after,
					 {{WRAPPER}}.elementor-testimonial--layout-image_above.elementor-testimonial--align-right .elementor-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_gap',
			[
				'label' => PrestaHelper::__( 'Gap', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_inline.elementor-testimonial--align-left .elementor-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_above.elementor-testimonial--align-left .elementor-testimonial__image + cite,
					 body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_inline .elementor-testimonial__image + cite,
					 body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_above .elementor-testimonial__image + cite' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',

					'body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_inline.elementor-testimonial--align-right .elementor-testimonial__image + cite,
					 body:not(.rtl) {{WRAPPER}}.elementor-testimonial--layout-image_above.elementor-testimonial--align-right .elementor-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_inline .elementor-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.elementor-testimonial--layout-image_above .elementor-testimonial__image + cite' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left:0;',

					'{{WRAPPER}}.elementor-testimonial--layout-image_stacked .elementor-testimonial__image + cite,
					 {{WRAPPER}}.elementor-testimonial--layout-image_left .elementor-testimonial__image + cite,
					 {{WRAPPER}}.elementor-testimonial--layout-image_right .elementor-testimonial__image + cite' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_border',
			[
				'label' => PrestaHelper::__( 'Border', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => PrestaHelper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => PrestaHelper::__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		// $this->update_responsive_control(
		// 	'width',
		// 	[
		// 		'selectors' => [
		// 			'{{WRAPPER}}.elementor-arrows-yes .elementor-main-swiper' => 'width: calc( {{SIZE}}{{UNIT}} - 40px )',
		// 			'{{WRAPPER}} .elementor-main-swiper' => 'width: {{SIZE}}{{UNIT}}',
		// 		],
		// 	]
		// );

		// $this->update_responsive_control(
		// 	'slides_per_view',
		// 	[
		// 		'condition' => null,
		// 	]
		// );

		// $this->update_responsive_control(
		// 	'slides_to_scroll',
		// 	[
		// 		'condition' => null,
		// 	]
		// );

		// $this->remove_control( 'effect' );
		// $this->remove_responsive_control( 'height' );
		// $this->remove_control( 'pagination_position' );
	}

    protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'content',
			[
				'label' => PrestaHelper::__( 'Content', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => PrestaHelper::__( 'Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => PrestaHelper::__( 'Name', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'John Doe', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'CEO', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
	}

    protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return [
			[
				'content' => PrestaHelper::__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
				'name' => PrestaHelper::__( 'John Doe', 'elementor' ),
				'title' => PrestaHelper::__( 'CEO', 'elementor' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => PrestaHelper::__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
				'name' => PrestaHelper::__( 'John Doe', 'elementor' ),
				'title' => PrestaHelper::__( 'CEO', 'elementor' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => PrestaHelper::__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
				'name' => PrestaHelper::__( 'John Doe', 'elementor' ),
				'title' => PrestaHelper::__( 'CEO', 'elementor' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
		];
	}

    private function print_cite( $slide, $location ) {
		if ( empty( $slide['name'] ) && empty( $slide['title'] ) ) {
			return '';
		}

		$skin = $this->get_settings( 'skin' );
		$layout = 'bubble' === $skin ? 'image_inline' : $this->get_settings( 'layout' );
		$locations_outside = [ 'image_above', 'image_right', 'image_left' ];
		$locations_inside = [ 'image_inline', 'image_stacked' ];

		$print_outside = ( 'outside' === $location && in_array( $layout, $locations_outside ) );
		$print_inside = ( 'inside' === $location && in_array( $layout, $locations_inside ) );

		$html = '';
		if ( $print_outside || $print_inside ) {
			$html = '<cite class="elementor-testimonial__cite">';
			if ( ! empty( $slide['name'] ) ) {
				$html .= '<span class="elementor-testimonial__name">' . $slide['name'] . '</span>';
			}
			if ( ! empty( $slide['title'] ) ) {
				$html .= '<span class="elementor-testimonial__title">' . $slide['title'] . '</span>';
			}
			$html .= '</cite>';
		}

		// PHPCS - the main text of a widget should not be escaped.
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

    protected function print_slide( array $slide, array $settings, $element_key ) {
		$this->add_render_attribute( $element_key . '-testimonial', [
			'class' => 'elementor-testimonial',
		] );

		if ( ! empty( $slide['image']['url'] ) ) {
			$this->add_render_attribute( $element_key . '-image', [
				'src' => $this->get_slide_image_url( $slide, $settings ),
				'alt' => ! empty( $slide['name'] ) ? $slide['name'] : '',
			] );
		}

		?>
		<div <?php $this->print_render_attribute_string( $element_key . '-testimonial' ); ?>>
			<?php if ( $slide['content'] ) : ?>
				<div class="elementor-testimonial__content">
					<div class="elementor-testimonial__text">
						<?php // PHPCS - the main text of a widget should not be escaped.
						echo $slide['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php $this->print_cite( $slide, 'outside' ); ?>
				</div>
			<?php endif; ?>
			<div class="elementor-testimonial__footer">
				<?php if ( $slide['image']['url'] ) : ?>
					<div class="elementor-testimonial__image">
						<img <?php $this->print_render_attribute_string( $element_key . '-image' ); ?>>
					</div>
				<?php endif; ?>
				<?php $this->print_cite( $slide, 'inside' ); ?>
			</div>
		</div>
		<?php
	}
	/**
	 * Render testimonial widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$default_settings = [
			'container_class' => 'elementor-main-swiper',
			'video_play_icon' => true,
		];

		$settings = array_merge( $default_settings, $settings );

		$slides_count = count( $settings['slides'] );
		?>
		<div class="elementor-swiper">
			<div class="<?php echo esc_attr( $settings['container_class'] ); ?> swiper-container">
				<div class="swiper-wrapper">
					<?php
					foreach ( $settings['slides'] as $index => $slide ) :
						$this->slide_prints_count++;
						?>
						<div class="swiper-slide">
							<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( 1 < $slides_count ) : ?>
					<?php if ( $settings['pagination'] ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( $settings['show_arrows'] ) : ?>
						<div class="elementor-swiper-button elementor-swiper-button-prev">
							<?php $this->render_swiper_button( 'previous' ); ?>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Previous', 'elementor' ); ?></span>
						</div>
						<div class="elementor-swiper-button elementor-swiper-button-next">
							<?php $this->render_swiper_button( 'next' ); ?>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Next', 'elementor' ); ?></span>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function get_group_name() {
		return 'carousel';
	}
}