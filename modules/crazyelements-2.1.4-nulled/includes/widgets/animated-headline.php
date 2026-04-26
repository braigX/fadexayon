<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}

class Widget_AnimatedHeadline extends Widget_Base
{

	public function get_name()
	{
		return 'animated_headline';
	}

	public function get_title()
	{
		return PrestaHelper::__('Animated Headline', 'elementor');
	}

	public function get_icon()
	{
		return 'ceicon-animated-headline';
	}

	public function get_keywords()
	{
		return ['headline', 'heading', 'animation', 'title', 'text'];
	}

	public function get_categories() {
		return array( 'crazy_addons' );
	}

	protected function _register_controls()
	{
		$this->start_controls_section(
			'text_elements',
			[
				'label' => PrestaHelper::__('Headline', 'elementor'),
			]
		);

		$this->add_control(
			'headline_style',
			[
				'label' => PrestaHelper::__('Style', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'highlight',
				'options' => [
					'highlight' => PrestaHelper::__('Highlighted', 'elementor'),
					'rotate' => PrestaHelper::__('Rotating', 'elementor'),
				],
				'prefix_class' => 'elementor-headline--style-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_type',
			[
				'label' => PrestaHelper::__('Animation', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'typing' => 'Typing',
					'clip' => 'Clip',
					'flip' => 'Flip',
					'swirl' => 'Swirl',
					'blinds' => 'Blinds',
					'drop-in' => 'Drop-in',
					'wave' => 'Wave',
					'slide' => 'Slide',
					'slide-down' => 'Slide Down',
				],
				'default' => 'typing',
				'condition' => [
					'headline_style' => 'rotate',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'marker',
			[
				'label' => PrestaHelper::__('Shape', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => PrestaHelper::_x('Circle', 'Shapes', 'elementor'),
					'curly' => PrestaHelper::_x('Curly', 'Shapes', 'elementor'),
					'underline' => PrestaHelper::_x('Underline', 'Shapes', 'elementor'),
					'double' => PrestaHelper::_x('Double', 'Shapes', 'elementor'),
					'double_underline' => PrestaHelper::_x('Double Underline', 'Shapes', 'elementor'),
					'underline_zigzag' => PrestaHelper::_x('Underline Zigzag', 'Shapes', 'elementor'),
					'diagonal' => PrestaHelper::_x('Diagonal', 'Shapes', 'elementor'),
					'strikethrough' => PrestaHelper::_x('Strikethrough', 'Shapes', 'elementor'),
					'x' => 'X',
				],
				'render_type' => 'template',
				'condition' => [
					'headline_style' => 'highlight',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => PrestaHelper::__('Before Text', 'elementor'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						//TagsModule::TEXT_CATEGORY,
					],
				],
				'default' => PrestaHelper::__('This page is', 'elementor'),
				'placeholder' => PrestaHelper::__('Enter your headline', 'elementor'),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'highlighted_text',
			[
				'label' => PrestaHelper::__('Highlighted Text', 'elementor'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						//TagsModule::TEXT_CATEGORY,
					],
				],
				'default' => PrestaHelper::__('Amazing', 'elementor'),
				'label_block' => true,
				'condition' => [
					'headline_style' => 'highlight',
				],
				'separator' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rotating_text',
			[
				'label' => PrestaHelper::__('Rotating Text', 'elementor'),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => PrestaHelper::__('Enter each word in a separate line', 'elementor'),
				'separator' => 'none',
				'default' => "Better\nBigger\nFaster",
				'dynamic' => [
					'active' => true,
					'categories' => [
						// TagsModule::TEXT_CATEGORY,
					],
				],
				'condition' => [
					'headline_style' => 'rotate',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => PrestaHelper::__('After Text', 'elementor'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						//TagsModule::TEXT_CATEGORY,
					],
				],
				'placeholder' => PrestaHelper::__('Enter your headline', 'elementor'),
				'label_block' => true,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => PrestaHelper::__('Infinite Loop', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--iteration-count: infinite',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'highlight_animation_duration',
			[
				'label' => PrestaHelper::__('Duration', 'elementor') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 1200,
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--animation-duration: {{VALUE}}ms',
				],
				'condition' => [
					'headline_style' => 'highlight',
				],
			]
		);

		$this->add_control(
			'highlight_iteration_delay',
			[
				'label' => PrestaHelper::__('Delay', 'elementor') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 8000,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => [
					'headline_style' => 'highlight',
					'loop' => 'yes',
				],
			]
		);

		$this->add_control(
			'rotate_iteration_delay',
			[
				'label' => PrestaHelper::__('Duration', 'elementor') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 2500,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => [
					'headline_style' => 'rotate',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => PrestaHelper::__('Link', 'elementor'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .elementor-headline' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => PrestaHelper::__('HTML Tag', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_marker',
			[
				'label' => PrestaHelper::__('Shape', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'headline_style' => 'highlight',
				],
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label' => PrestaHelper::__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'stroke_width',
			[
				'label' => PrestaHelper::__('Width', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'above_content',
			[
				'label' => PrestaHelper::__('Bring to Front', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper svg' => 'z-index: 2',
					'{{WRAPPER}} .elementor-headline-dynamic-text' => 'z-index: auto',
				],
			]
		);

		$this->add_control(
			'rounded_edges',
			[
				'label' => PrestaHelper::__('Rounded Edges', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke-linecap: round; stroke-linejoin: round',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => PrestaHelper::__('Headline', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => PrestaHelper::__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-plain-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => PrestaHelper::__('Typography', 'elementor'),
				'selector' => '{{WRAPPER}} .elementor-headline',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_stroke',
				'selector' => '{{WRAPPER}} .elementor-headline',
			]
		);

		$this->add_control(
			'heading_words_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Animated Text', 'elementor'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'words_color',
			[
				'label' => PrestaHelper::__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--dynamic-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'words_typography',
				'label' => PrestaHelper::__('Animated Words', 'elementor'),
				'selector' => '{{WRAPPER}} .elementor-headline-dynamic-text',
				'exclude' => ['font_size'],
			]
		);

		$this->add_control(
			'typing_animation_highlight_colors',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Selected Text', 'elementor'),
				'separator' => 'before',
				'condition' => [
					'headline_style' => 'rotate',
					'animation_type' => 'typing',
				],
			]
		);

		$this->add_control(
			'highlighted_text_background_color',
			[
				'label' => PrestaHelper::__('Selection Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--typing-selected-bg-color: {{VALUE}}',
				],
				'condition' => [
					'headline_style' => 'rotate',
					'animation_type' => 'typing',
				],
			]
		);

		$this->add_control(
			'highlighted_text_color',
			[
				'label' => PrestaHelper::__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--typing-selected-color: {{VALUE}}',
				],
				'condition' => [
					'headline_style' => 'rotate',
					'animation_type' => 'typing',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$tag      = $settings['tag'];

		$this->add_render_attribute('headline', 'class', 'elementor-headline');

		if ('rotate' === $settings['headline_style']) {
			$this->add_render_attribute('headline', 'class', 'elementor-headline-animation-type-' . $settings['animation_type']);

			$is_letter_animation = in_array($settings['animation_type'], ['typing', 'swirl', 'blinds', 'wave']);

			if ($is_letter_animation) {
				$this->add_render_attribute('headline', 'class', 'elementor-headline-letters');
			}
		}

		if (!empty($settings['link']['url'])) {
			$this->add_link_attributes('url', $settings['link']);
?>
			<a <?php $this->print_render_attribute_string('url'); ?>>

			<?php
		}

			?>
			<<?php echo $tag;?> <?php $this->print_render_attribute_string('headline'); ?>>
				<?php if (!empty($settings['before_text'])) : ?>
					<span class="elementor-headline-plain-text elementor-headline-text-wrapper"><?php $this->print_unescaped_setting('before_text'); ?></span>
				<?php endif; ?>
				<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
					<?php if ('rotate' === $settings['headline_style'] && $settings['rotating_text']) :
						$rotating_text = explode("\n", $settings['rotating_text']);
						foreach ($rotating_text as $key => $text) : ?>
							<span class="elementor-headline-dynamic-text<?php echo 1 > $key ? ' elementor-headline-text-active' : ''; ?>">
								<?php Utils::print_unescaped_internal_string(str_replace(' ', '&nbsp;', $text));
								?>
							</span>
						<?php endforeach; ?>
					<?php elseif ('highlight' === $settings['headline_style'] && !empty($settings['highlighted_text'])) : ?>
						<span class="elementor-headline-dynamic-text elementor-headline-text-active"><?php $this->print_unescaped_setting('highlighted_text'); ?></span>
					<?php endif ?>
				</span>
				<?php if (!empty($settings['after_text'])) : ?>
					<span class="elementor-headline-plain-text elementor-headline-text-wrapper"><?php $this->print_unescaped_setting('after_text'); ?></span>
				<?php endif; ?>
			</<?php echo $tag;?>>
			<?php

			if (!empty($settings['link']['url'])) {
				echo '</a>';
			}
		}

		/**
		 * Render Animated Headline widget output in the editor.
		 *
		 * Written as a Backbone JavaScript template and used to generate the live preview.
		 *
		 * @since 2.9.0
		 * @access protected
		 */
		protected function content_template()
		{
			?>
			<# var headlineClasses='elementor-headline' , tag=elementorPro.validateHTMLTag( settings.tag ); if ( 'rotate'===settings.headline_style ) { headlineClasses +=' elementor-headline-animation-type-' + settings.animation_type; var isLetterAnimation=-1 !==[ 'typing' , 'swirl' , 'blinds' , 'wave' ].indexOf( settings.animation_type ); if ( isLetterAnimation ) { headlineClasses +=' elementor-headline-letters' ; } } if ( settings.link.url ) { #>
				<a href="#">
					<# } #>
						<{{{ tag }}} class="{{{ headlineClasses }}}">
							<# if ( settings.before_text ) { #>
								<span class="elementor-headline-plain-text elementor-headline-text-wrapper">{{{ settings.before_text }}}</span>
								<# } #>

									<# if ( settings.rotating_text ) { #>
										<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
											<# if ( 'rotate'===settings.headline_style && settings.rotating_text ) { var rotatingText=( settings.rotating_text || '' ).split( '\n' ); for ( var i=0; i < rotatingText.length; i++ ) { var statusClass=0===i ? 'elementor-headline-text-active' : '' ; #>
												<span class="elementor-headline-dynamic-text {{ statusClass }}">
													{{{ rotatingText[ i ].replace( ' ', '&nbsp;' ) }}}
												</span>
												<# } } else if ( 'highlight'===settings.headline_style && settings.highlighted_text ) { #>
													<span class="elementor-headline-dynamic-text elementor-headline-text-active">{{{ settings.highlighted_text }}}</span>
													<# } #>
										</span>
										<# } #>

											<# if ( settings.after_text ) { #>
												<span class="elementor-headline-plain-text elementor-headline-text-wrapper">{{{ settings.after_text }}}</span>
												<# } #>
						</{{{ tag }}}>
						<# if ( settings.link.url ) { #>
				</a>
				<# } #>
			<?php
		}
	}
