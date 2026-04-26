<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}
class Widget_ShareButtons extends Widget_Base
{

	private static $networks_class_dictionary = [
		'pocket' => [
			'value' => 'fa fa-get-pocket',
		],
		'email' => [
			'value' => 'fa fa-envelope',
		],
	];

	private static $networks_icon_mapping = [
		'pocket' => [
			'value' => 'fab fa-get-pocket',
			'library' => 'fa-brands',
		],
		'email' => [
			'value' => 'fas fa-envelope',
			'library' => 'fa-solid',
		],
		'print' => [
			'value' => 'fas fa-print',
			'library' => 'fa-solid',
		],
	];

	public function get_style_depends()
	{
		if (Icons_Manager::is_migration_allowed()) {
			return [
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
			];
		}
		return [];
	}

	private static function get_network_icon_data($network_name)
	{
		$prefix = 'fa ';
		$library = '';

		if (Icons_Manager::is_migration_allowed()) {
			if (isset(self::$networks_icon_mapping[$network_name])) {
				return self::$networks_icon_mapping[$network_name];
			}
			$prefix = 'fab ';
			$library = 'fa-brands';
		}
		if (isset(self::$networks_class_dictionary[$network_name])) {
			return self::$networks_class_dictionary[$network_name];
		}

		return [
			'value' => $prefix . 'fa-' . $network_name,
			'library' => $library,
		];
	}

	public function get_name()
	{
		return 'share-buttons';
	}

	public function get_title()
	{
		return PrestaHelper::__('Share Buttons', 'elementor');
	}

	public function get_icon()
	{
		$hook = \Tools::getValue('hook');
		if($hook == 'prdlayouts'){
			return 'ceicon-product-share';
		}else{
			return 'ceicon-share';
		}	
	}

	public function get_categories() {

		$hook = \Tools::getValue('hook');
		if($hook == 'prdlayouts'){
			return array( 'products_layout' );
		}else{
			return array( 'crazy_addons' );
		}
	}

	public function get_keywords()
	{
		return ['sharing', 'social', 'icon', 'button', 'like'];
	}

	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_buttons_content',
			[
				'label' => PrestaHelper::__('Share Buttons', 'elementor'),
			]
		);

		$repeater = new Repeater();

		$networks = Widget_ShareButtons::get_networks();

		$networks_names = array_keys($networks);

		$repeater->add_control(
			'button',
			[
				'label' => PrestaHelper::__('Network', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => array_reduce($networks_names, function ($options, $network_name) use ($networks) {
					$options[$network_name] = $networks[$network_name]['title'];

					return $options;
				}, []),
				'default' => 'facebook',
			]
		);

		$repeater->add_control(
			'text',
			[
				'label' => PrestaHelper::__('Custom Label', 'elementor'),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'share_buttons',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'button' => 'facebook',
					],
					[
						'button' => 'twitter',
					],
					[
						'button' => 'linkedin',
					],
				]
			]
		);

		$this->add_control(
			'view',
			[
				'label' => PrestaHelper::__('View', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon-text' => 'Icon & Text',
					'icon' => 'Icon',
					'text' => 'Text',
				],
				'default' => 'icon-text',
				'separator' => 'before',
				'prefix_class' => 'elementor-share-buttons--view-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'show_label',
			[
				'label' => PrestaHelper::__('Label', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => PrestaHelper::__('Show', 'elementor'),
				'label_off' => PrestaHelper::__('Hide', 'elementor'),
				'default' => 'yes',
				'condition' => [
					'view' => 'icon-text',
				],
			]
		);

		$this->add_control(
			'skin',
			[
				'label' => PrestaHelper::__('Skin', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'gradient' => PrestaHelper::__('Gradient', 'elementor'),
					'minimal' => PrestaHelper::__('Minimal', 'elementor'),
					'framed' => PrestaHelper::__('Framed', 'elementor'),
					'boxed' => PrestaHelper::__('Boxed Icon', 'elementor'),
					'flat' => PrestaHelper::__('Flat', 'elementor'),
				],
				'default' => 'gradient',
				'prefix_class' => 'elementor-share-buttons--skin-',
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => PrestaHelper::__('Shape', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'square' => PrestaHelper::__('Square', 'elementor'),
					'rounded' => PrestaHelper::__('Rounded', 'elementor'),
					'circle' => PrestaHelper::__('Circle', 'elementor'),
				],
				'default' => 'square',
				'prefix_class' => 'elementor-share-buttons--shape-',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => PrestaHelper::__('Columns', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => 'Auto',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'elementor-grid%s-',
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
					'justify' => [
						'title' => PrestaHelper::__('Justify', 'elementor'),
						'icon' => 'ceicon-text-align-justify',
					],
				],
				/*---------------------------------------------------*/
				'condition' => [
					'columns' => '0',
				],
				/* `selectors` was added on v3.1.0 as a superior alternative to the previous `prefix_class` solution */
				'selectors' => [
					'{{WRAPPER}} .elementor-grid.elementor-share-buttons' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'share_url_type',
			[
				'label' => PrestaHelper::__('Target URL', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'current_page' => PrestaHelper::__('Current Page', 'elementor'),
					'custom' => PrestaHelper::__('Custom', 'elementor'),
				],
				'default' => 'current_page',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'share_url',
			[
				'label' => PrestaHelper::__('Link', 'elementor'),
				'type' => Controls_Manager::URL,
				'options' => false,
				'placeholder' => PrestaHelper::__('https://your-link.com', 'elementor'),
				'condition' => [
					'share_url_type' => 'custom',
				],
				'show_label' => false,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => PrestaHelper::__('Share Buttons', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => PrestaHelper::__('Columns Gap', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-grid.elementor-share-buttons' => 'gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}} .elementor-grid.elementor-share-buttons' => 'gap: {{SIZE}}{{UNIT}}',
					'(mobile) {{WRAPPER}} .elementor-grid.elementor-share-buttons' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => PrestaHelper::__('Rows Gap', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-bottom-margin: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-bottom-margin: {{SIZE}}{{UNIT}}',
					'(mobile) {{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-bottom-margin: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'button_size',
			[
				'label' => PrestaHelper::__('Button Size', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.5,
						'max' => 2,
						'step' => 0.05,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => PrestaHelper::__('Icon Size', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0.5,
						'max' => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => ['em', 'px'],
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn__icon' => '--e-share-buttons-icon-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label' => PrestaHelper::__('Button Height', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 1,
						'max' => 7,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => ['em', 'px'],
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_size',
			[
				'label' => PrestaHelper::__('Border Size', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin' => ['framed', 'boxed'],
				],
			]
		);

		$this->add_control(
			'color_source',
			[
				'label' => PrestaHelper::__('Color', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'official' => PrestaHelper::__('Official', 'elementor'),
					'custom' => PrestaHelper::__('Custom', 'elementor'),
				],
				'default' => 'official',
				'prefix_class' => 'elementor-share-buttons--color-',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs(
			'tabs_button_style',
			[
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => PrestaHelper::__('Normal', 'elementor'),
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => PrestaHelper::__('Primary Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--e-share-buttons-primary-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label' => PrestaHelper::__('Secondary Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--e-share-buttons-secondary-color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'skin!' => 'framed',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => PrestaHelper::__('Hover', 'elementor'),
			]
		);

		$this->add_control(
			'primary_color_hover',
			[
				'label' => PrestaHelper::__('Primary Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn:hover' => '--e-share-buttons-primary-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'secondary_color_hover',
			[
				'label' => PrestaHelper::__('Secondary Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-share-btn:hover' => '--e-share-buttons-secondary-color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .elementor-share-btn__title',
				'exclude' => ['line_height'],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label' => PrestaHelper::__('Text Padding', 'elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'view' => 'text',
				],
			]
		);

		$this->end_controls_section();
	}

	private static function render_share_icon($network_name)
	{
		$network_icon_data = self::get_network_icon_data($network_name);
		$icon = sprintf('<i class="%s" aria-hidden="true"></i>', $network_icon_data['value']);
		Utils::print_unescaped_internal_string( $icon );
	}

	public static function get_networks($network_name = null)
	{
		if ($network_name) {
			return isset(self::$networks[$network_name]) ? self::$networks[$network_name] : null;
		}

		return self::$networks;
	}
	public function add_localize_data($settings)
	{
		$settings['shareButtonsNetworks'] = self::$networks;

		return $settings;
	}

	private static $networks = [
		'facebook' => [
			'title' => 'Facebook',
			'has_counter' => true,
		],
		'twitter' => [
			'title' => 'Twitter',
		],
		'linkedin' => [
			'title' => 'LinkedIn',
			'has_counter' => true,
		],
		'pinterest' => [
			'title' => 'Pinterest',
			'has_counter' => true,
		],
		'reddit' => [
			'title' => 'Reddit',
			'has_counter' => true,
		],
		'vk' => [
			'title' => 'VK',
			'has_counter' => true,
		],
		'odnoklassniki' => [
			'title' => 'OK',
			'has_counter' => true,
		],
		'tumblr' => [
			'title' => 'Tumblr',
		],
		'digg' => [
			'title' => 'Digg',
		],
		'skype' => [
			'title' => 'Skype',
		],
		'stumbleupon' => [
			'title' => 'StumbleUpon',
			'has_counter' => true,
		],
		'mix' => [
			'title' => 'Mix',
		],
		'telegram' => [
			'title' => 'Telegram',
		],
		'pocket' => [
			'title' => 'Pocket',
			'has_counter' => true,
		],
		'xing' => [
			'title' => 'XING',
			'has_counter' => true,
		],
		'whatsapp' => [
			'title' => 'WhatsApp',
		],
		'email' => [
			'title' => 'Email',
		],
		'print' => [
			'title' => 'Print',
		],
	];

	protected function render()
	{
		$settings = $this->get_active_settings();
		if (empty($settings['share_buttons'])) {
			return;
		}

		$button_classes = 'elementor-share-btn';

		$show_text = 'text' === $settings['view'] || 'yes' === $settings['show_label'];
?>
		<div class="elementor-grid elementor-share-buttons">
			<?php
			$networks_data = Widget_ShareButtons::get_networks();

			foreach ($settings['share_buttons'] as $button) {
				$network_name = $button['button'];

				// A deprecated network.
				if (!isset($networks_data[$network_name])) {
					continue;
				}

				$social_network_class = ' elementor-share-btn_' . $network_name;
			?>
				<div class="elementor-grid-item">
					<div class="<?php echo PrestaHelper::esc_attr($button_classes . $social_network_class); ?>">
						<?php if ('icon' === $settings['view'] || 'icon-text' === $settings['view']) : ?>
							<span class="elementor-share-btn__icon">
								<?php self::render_share_icon($network_name); ?>
								<span class="elementor-screen-only"><?php echo sprintf(PrestaHelper::__('Share on %s', 'elementor'), PrestaHelper::esc_html($network_name)); ?></span>
							</span>
						<?php endif; ?>
						<?php if ($show_text) : 
							?>
							<div class="elementor-share-btn__text">
								<?php if ('yes' === $settings['show_label'] || 'text' === $settings['view']) : ?>
									<span class="elementor-share-btn__title">
										<?php
										// PHPCS - the main text of a widget should not be escaped.
										echo $button['text'] ? $button['text'] : $networks_data[$network_name]['title'];
										?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	<?php
	}

	/**
	 * Render Share Buttons widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template()
	{
	?>
		<# var shareButtonsEditorModule=elementorPro.modules.shareButtons, buttonClass='elementor-share-btn' ; var showText='icon-text'===settings.view ? 'yes'===settings.show_label : 'text'===settings.view; #>
			<div class="elementor-grid">
				<# _.each( settings.share_buttons, function( button ) { // A deprecated network. if ( ! shareButtonsEditorModule.getNetworkData( button ) ) { return; } var networkName=button.button, socialNetworkClass='elementor-share-btn_' + networkName; #>
					<div class="elementor-grid-item">
						<div class="{{ buttonClass }} {{ socialNetworkClass }}">
							<# if ( 'icon'===settings.view || 'icon-text'===settings.view ) { #>
								<span class="elementor-share-btn__icon">
									<i class="{{ shareButtonsEditorModule.getNetworkClass( networkName ) }}" aria-hidden="true"></i>
									<span class="elementor-screen-only">Share on {{{ networkName }}}</span>
								</span>
								<# } #>
									<# if ( showText ) { #>
										<div class="elementor-share-btn__text">
											<# if ( 'yes'===settings.show_label || 'text'===settings.view ) { #>
												<span class="elementor-share-btn__title">{{{ shareButtonsEditorModule.getNetworkTitle( button ) }}}</span>
												<# } #>
										</div>
										<# } #>
						</div>
					</div>
					<# } ); #>
			</div>
	<?php
	}
}
