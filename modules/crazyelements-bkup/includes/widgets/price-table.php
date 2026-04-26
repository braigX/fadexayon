<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_PriceTable extends Widget_Base {

	public function get_name() {
		return 'price-table';
	}

	public function get_title() {
		return PrestaHelper::__( 'Price Table', 'elementor' );
	}

	public function get_icon() {
		return 'ceicon-price-table';
	}

	public function get_keywords() {
		return [ 'pricing', 'table', 'product', 'image', 'plan', 'button' ];
	}

	public function get_categories() {
		return array( 'crazy_addons' );
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_header',
			[
				'label' => PrestaHelper::__( 'Header', 'elementor' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'Enter your title', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'sub_heading',
			[
				'label' => PrestaHelper::__( 'Description', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'Enter your description', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label' => PrestaHelper::__( 'Title HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pricing',
			[
				'label' => PrestaHelper::__( 'Pricing', 'elementor' ),
			]
		);

		$this->add_control(
			'currency_symbol',
			[
				'label' => PrestaHelper::__( 'Currency Symbol', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => PrestaHelper::__( 'None', 'elementor' ),
					'dollar' => '&#36; ' . PrestaHelper::_x( 'Dollar', 'Currency', 'elementor' ),
					'euro' => '&#128; ' . PrestaHelper::_x( 'Euro', 'Currency', 'elementor' ),
					'baht' => '&#3647; ' . PrestaHelper::_x( 'Baht', 'Currency', 'elementor' ),
					'franc' => '&#8355; ' . PrestaHelper::_x( 'Franc', 'Currency', 'elementor' ),
					'guilder' => '&fnof; ' . PrestaHelper::_x( 'Guilder', 'Currency', 'elementor' ),
					'krona' => 'kr ' . PrestaHelper::_x( 'Krona', 'Currency', 'elementor' ),
					'lira' => '&#8356; ' . PrestaHelper::_x( 'Lira', 'Currency', 'elementor' ),
					'peseta' => '&#8359 ' . PrestaHelper::_x( 'Peseta', 'Currency', 'elementor' ),
					'peso' => '&#8369; ' . PrestaHelper::_x( 'Peso', 'Currency', 'elementor' ),
					'pound' => '&#163; ' . PrestaHelper::_x( 'Pound Sterling', 'Currency', 'elementor' ),
					'real' => 'R$ ' . PrestaHelper::_x( 'Real', 'Currency', 'elementor' ),
					'ruble' => '&#8381; ' . PrestaHelper::_x( 'Ruble', 'Currency', 'elementor' ),
					'rupee' => '&#8360; ' . PrestaHelper::_x( 'Rupee', 'Currency', 'elementor' ),
					'indian_rupee' => '&#8377; ' . PrestaHelper::_x( 'Rupee (Indian)', 'Currency', 'elementor' ),
					'shekel' => '&#8362; ' . PrestaHelper::_x( 'Shekel', 'Currency', 'elementor' ),
					'yen' => '&#165; ' . PrestaHelper::_x( 'Yen/Yuan', 'Currency', 'elementor' ),
					'won' => '&#8361; ' . PrestaHelper::_x( 'Won', 'Currency', 'elementor' ),
					'custom' => PrestaHelper::__( 'Custom', 'elementor' ),
				],
				'default' => 'dollar',
			]
		);

		$this->add_control(
			'currency_symbol_custom',
			[
				'label' => PrestaHelper::__( 'Custom Symbol', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'currency_symbol' => 'custom',
				],
			]
		);

		$this->add_control(
			'price',
			[
				'label' => PrestaHelper::__( 'Price', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '39.99',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'currency_format',
			[
				'label' => PrestaHelper::__( 'Currency Format', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => '1,234.56 (Default)',
					',' => '1.234,56',
				],
			]
		);

		$this->add_control(
			'sale',
			[
				'label' => PrestaHelper::__( 'Sale', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => PrestaHelper::__( 'On', 'elementor' ),
				'label_off' => PrestaHelper::__( 'Off', 'elementor' ),
				'default' => '',
			]
		);

		$this->add_control(
			'original_price',
			[
				'label' => PrestaHelper::__( 'Original Price', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '59',
				'condition' => [
					'sale' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'period',
			[
				'label' => PrestaHelper::__( 'Period', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'Monthly', 'elementor' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features',
			[
				'label' => PrestaHelper::__( 'Features', 'elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_text',
			[
				'label' => PrestaHelper::__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'List Item', 'elementor' ),
			]
		);

		$default_icon = [
			'value' => 'far fa-check-circle',
			'library' => 'fa-regular',
		];

		$repeater->add_control(
			'selected_item_icon',
			[
				'label' => PrestaHelper::__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'item_icon',
				'default' => $default_icon,
			]
		);

		$repeater->add_control(
			'item_icon_color',
			[
				'label' => PrestaHelper::__( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'features_list',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'item_text' => PrestaHelper::__( 'List Item #1', 'elementor' ),
						'selected_item_icon' => $default_icon,
					],
					[
						'item_text' => PrestaHelper::__( 'List Item #2', 'elementor' ),
						'selected_item_icon' => $default_icon,
					],
					[
						'item_text' => PrestaHelper::__( 'List Item #3', 'elementor' ),
						'selected_item_icon' => $default_icon,
					],
				],
				'title_field' => '{{{ item_text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_footer',
			[
				'label' => PrestaHelper::__( 'Footer', 'elementor' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => PrestaHelper::__( 'Button Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'Click Here', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => PrestaHelper::__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => PrestaHelper::__( 'https://your-link.com', 'elementor' ),
				'default' => [
					'url' => '#',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'footer_additional_info',
			[
				'label' => PrestaHelper::__( 'Additional Info', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => PrestaHelper::__( 'This is text element', 'elementor' ),
				'rows' => 3,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ribbon',
			[
				'label' => PrestaHelper::__( 'Ribbon', 'elementor' ),
			]
		);

		$this->add_control(
			'show_ribbon',
			[
				'label' => PrestaHelper::__( 'Show', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ribbon_title',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => PrestaHelper::__( 'Popular', 'elementor' ),
				'condition' => [
					'show_ribbon' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ribbon_horizontal_position',
			[
				'label' => PrestaHelper::__( 'Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => PrestaHelper::__( 'Left', 'elementor' ),
						'icon' => 'ceicon-h-align-left',
					],
					'right' => [
						'title' => PrestaHelper::__( 'Right', 'elementor' ),
						'icon' => 'ceicon-h-align-right',
					],
				],
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_header_style',
			[
				'label' => PrestaHelper::__( 'Header', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--e-price-table-header-background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_heading_style',
			[
				'label' => PrestaHelper::__( 'Title', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__heading',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				// ],
			]
		);

		$this->add_control(
			'heading_sub_heading_style',
			[
				'label' => PrestaHelper::__( 'Sub Title', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_heading_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__subheading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_heading_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__subheading',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pricing_element_style',
			[
				'label' => PrestaHelper::__( 'Pricing', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'pricing_element_bg_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__price' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'pricing_element_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__currency, {{WRAPPER}} .elementor-price-table__integer-part, {{WRAPPER}} .elementor-price-table__fractional-part' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				// Targeting also the .elementor-price-table class in order to get a higher specificity from the inline CSS.
				'selector' => '{{WRAPPER}} .elementor-price-table .elementor-price-table__price',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				// ],
			]
		);

		$this->add_control(
			'heading_currency_style',
			[
				'label' => PrestaHelper::__( 'Currency Symbol', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'currency_size',
			[
				'label' => PrestaHelper::__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__price > .elementor-price-table__currency' => 'font-size: calc({{SIZE}}em/100)',
				],
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'currency_position',
			[
				'label' => PrestaHelper::__( 'Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'before',
				'options' => [
					'before' => [
						'title' => PrestaHelper::__( 'Before', 'elementor' ),
						'icon' => 'ceicon-h-align-left',
					],
					'after' => [
						'title' => PrestaHelper::__( 'After', 'elementor' ),
						'icon' => 'ceicon-h-align-right',
					],
				],
			]
		);

		$this->add_control(
			'currency_vertical_position',
			[
				'label' => PrestaHelper::__( 'Vertical Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => PrestaHelper::__( 'Top', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'middle' => [
						'title' => PrestaHelper::__( 'Middle', 'elementor' ),
						'icon' => 'ceicon-v-align-middle',
					],
					'bottom' => [
						'title' => PrestaHelper::__( 'Bottom', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__currency' => 'align-self: {{VALUE}}',
				],
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'fractional_part_style',
			[
				'label' => PrestaHelper::__( 'Fractional Part', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'fractional-part_size',
			[
				'label' => PrestaHelper::__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__fractional-part' => 'font-size: calc({{SIZE}}em/100)',
				],
			]
		);

		$this->add_control(
			'fractional_part_vertical_position',
			[
				'label' => PrestaHelper::__( 'Vertical Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => PrestaHelper::__( 'Top', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'middle' => [
						'title' => PrestaHelper::__( 'Middle', 'elementor' ),
						'icon' => 'ceicon-v-align-middle',
					],
					'bottom' => [
						'title' => PrestaHelper::__( 'Bottom', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__after-price' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_original_price_style',
			[
				'label' => PrestaHelper::__( 'Original Price', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'sale' => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'original_price_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__original-price' => 'color: {{VALUE}}',
				],
				'condition' => [
					'sale' => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'original_price_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__original-price',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				// ],
				'condition' => [
					'sale' => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'original_price_vertical_position',
			[
				'label' => PrestaHelper::__( 'Vertical Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => PrestaHelper::__( 'Top', 'elementor' ),
						'icon' => 'ceicon-v-align-top',
					],
					'middle' => [
						'title' => PrestaHelper::__( 'Middle', 'elementor' ),
						'icon' => 'ceicon-v-align-middle',
					],
					'bottom' => [
						'title' => PrestaHelper::__( 'Bottom', 'elementor' ),
						'icon' => 'ceicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'default' => 'bottom',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__original-price' => 'align-self: {{VALUE}}',
				],
				'condition' => [
					'sale' => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'heading_period_style',
			[
				'label' => PrestaHelper::__( 'Period', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->add_control(
			'period_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__period' => 'color: {{VALUE}}',
				],
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'period_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__period',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->add_control(
			'period_position',
			[
				'label' => PrestaHelper::__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'below' => PrestaHelper::__( 'Below', 'elementor' ),
					'beside' => PrestaHelper::__( 'Beside', 'elementor' ),
				],
				'default' => 'below',
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_list_style',
			[
				'label' => PrestaHelper::__( 'Features', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'features_list_bg_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'features_list_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'features_list_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list' => '--e-price-table-features-list-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'features_list_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__features-list li',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_TEXT,
				// ],
			]
		);

		$this->add_control(
			'features_list_alignment',
			[
				'label' => PrestaHelper::__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .elementor-price-table__features-list' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'item_width',
			[
				'label' => PrestaHelper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 25,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__feature-inner' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_control(
			'list_divider',
			[
				'label' => PrestaHelper::__( 'Divider', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => PrestaHelper::__( 'Style', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => PrestaHelper::__( 'Solid', 'elementor' ),
					'double' => PrestaHelper::__( 'Double', 'elementor' ),
					'dotted' => PrestaHelper::__( 'Dotted', 'elementor' ),
					'dashed' => PrestaHelper::__( 'Dashed', 'elementor' ),
				],
				'default' => 'solid',
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list li:before' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list li:before' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => PrestaHelper::__( 'Weight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list li:before' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label' => PrestaHelper::__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list li:before' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_control(
			'divider_gap',
			[
				'label' => PrestaHelper::__( 'Gap', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__features-list li:before' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_footer_style',
			[
				'label' => PrestaHelper::__( 'Footer', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'footer_bg_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__footer' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'footer_padding',
			[
				'label' => PrestaHelper::__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_footer_button',
			[
				'label' => PrestaHelper::__( 'Button', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => PrestaHelper::__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'xs' => PrestaHelper::__( 'Extra Small', 'elementor' ),
					'sm' => PrestaHelper::__( 'Small', 'elementor' ),
					'md' => PrestaHelper::__( 'Medium', 'elementor' ),
					'lg' => PrestaHelper::__( 'Large', 'elementor' ),
					'xl' => PrestaHelper::__( 'Extra Large', 'elementor' ),
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				// ],
				'selector' => '{{WRAPPER}} .elementor-price-table__button',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .elementor-price-table__button',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_3,
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .elementor-price-table__button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[
				'label' => PrestaHelper::__( 'Text Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .elementor-price-table__button:hover',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => PrestaHelper::__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => PrestaHelper::__( 'Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'heading_additional_info',
			[
				'label' => PrestaHelper::__( 'Additional Info', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'footer_additional_info!' => '',
				],
			]
		);

		$this->add_control(
			'additional_info_color',
			[
				'label' => PrestaHelper::__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__additional_info' => 'color: {{VALUE}}',
				],
				'condition' => [
					'footer_additional_info!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'additional_info_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__additional_info',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_TEXT,
				// ],
				'condition' => [
					'footer_additional_info!' => '',
				],
			]
		);

		$this->add_control(
			'additional_info_margin',
			[
				'label' => PrestaHelper::__( 'Margin', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 15,
					'right' => 30,
					'bottom' => 0,
					'left' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__additional_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'footer_additional_info!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ribbon_style',
			[
				'label' => PrestaHelper::__( 'Ribbon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ribbon_bg_color',
			[
				'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__ribbon-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$ribbon_distance_transform = PrestaHelper::is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)';

		$this->add_responsive_control(
			'ribbon_distance',
			[
				'label' => PrestaHelper::__( 'Distance', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					 '{{WRAPPER}} .elementor-price-table__ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . $ribbon_distance_transform,
				],
			]
		);

		$this->add_control(
			'ribbon_text_color',
			[
				'label' => PrestaHelper::__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-table__ribbon-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .elementor-price-table__ribbon-inner',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .elementor-price-table__ribbon-inner',
			]
		);

		$this->end_controls_section();
	}

	private function render_currency_symbol( $symbol, $location ) {
		$currency_position = $this->get_settings( 'currency_position' );
		$location_setting = ! empty( $currency_position ) ? $currency_position : 'before';
		if ( ! empty( $symbol ) && $location === $location_setting ) {
			echo '<span class="elementor-price-table__currency">' . PrestaHelper::esc_html( $symbol ) . '</span>';
		}
	}

	private function get_currency_symbol( $symbol_name ) {
		$symbols = [
			'dollar' => '&#36;',
			'euro' => '&#128;',
			'franc' => '&#8355;',
			'pound' => '&#163;',
			'ruble' => '&#8381;',
			'shekel' => '&#8362;',
			'baht' => '&#3647;',
			'yen' => '&#165;',
			'won' => '&#8361;',
			'guilder' => '&fnof;',
			'peso' => '&#8369;',
			'peseta' => '&#8359',
			'lira' => '&#8356;',
			'rupee' => '&#8360;',
			'indian_rupee' => '&#8377;',
			'real' => 'R$',
			'krona' => 'kr',
		];

		return isset( $symbols[ $symbol_name ] ) ? $symbols[ $symbol_name ] : '';
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$heading        = $settings['heading'];
		$sub_heading    = $settings['sub_heading'];
		$heading_tag    = $settings['heading_tag'];
		$original_price = $settings['original_price'];

		$symbol = '';

		if ( ! empty( $settings['currency_symbol'] ) ) {
			if ( 'custom' !== $settings['currency_symbol'] ) {
				$symbol = $this->get_currency_symbol( $settings['currency_symbol'] );
			} else {
				$symbol = $settings['currency_symbol_custom'];
			}
		}
		$currency_format = empty( $settings['currency_format'] ) ? '.' : $settings['currency_format'];
		$price = explode( $currency_format, $settings['price'] );
		$intpart = $price[0];
		$fraction = '';
		if ( 2 === count( $price ) ) {
			$fraction = $price[1];
		}

		$this->add_render_attribute( 'button_text', 'class', [
			'elementor-price-table__button',
			'elementor-button',
			'elementor-size-' . $settings['button_size'],
		] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'button_text', $settings['link'] );
		}

		if ( ! empty( $settings['button_hover_animation'] ) ) {
			$this->add_render_attribute( 'button_text', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
		}

		$this->add_render_attribute( 'heading', 'class', 'elementor-price-table__heading' );
		$this->add_render_attribute( 'sub_heading', 'class', 'elementor-price-table__subheading' );
		$this->add_render_attribute( 'period', 'class', [ 'elementor-price-table__period', 'elementor-typo-excluded' ] );
		$this->add_render_attribute( 'footer_additional_info', 'class', 'elementor-price-table__additional_info' );
		$this->add_render_attribute( 'ribbon_title', 'class', 'elementor-price-table__ribbon-inner' );

		$this->add_inline_editing_attributes( 'heading', 'none' );
		$this->add_inline_editing_attributes( 'sub_heading', 'none' );
		$this->add_inline_editing_attributes( 'period', 'none' );
		$this->add_inline_editing_attributes( 'footer_additional_info' );
		$this->add_inline_editing_attributes( 'button_text' );
		$this->add_inline_editing_attributes( 'ribbon_title' );

		$period_position = $settings['period_position'];
		$period_element = '<span ' . $this->get_render_attribute_string( 'period' ) . '>' . $settings['period'] . '</span>';

		$migration_allowed = Icons_Manager::is_migration_allowed();
		?>

		<div class="elementor-price-table">
			<?php  if ( $settings['heading'] || $settings['sub_heading'] ) : ?>
				<div class="elementor-price-table__header">
					<?php if ( ! empty( $settings['heading'] ) ) : ?>
						<<?php echo $heading_tag;?> <?php $this->print_render_attribute_string( 'heading' ); ?>>
						<?php echo $heading; ?>
						</<?php echo $heading_tag; ?>>
					<?php endif; ?>

					<?php if ( ! empty( $settings['sub_heading'] ) ) : ?>
						<span <?php $this->print_render_attribute_string( 'sub_heading' ); ?>>
							<?php  echo $sub_heading; ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="elementor-price-table__price">
				<?php  if ( 'yes' === $settings['sale'] && ! empty( $settings['original_price'] ) ) : ?>
					<div class="elementor-price-table__original-price elementor-typo-excluded">
						<?php
						$this->render_currency_symbol( $symbol, 'before' );
                        echo $original_price;
						$this->render_currency_symbol( $symbol, 'after' );
						?>
					</div>
				<?php  endif; ?>
				<?php $this->render_currency_symbol( $symbol, 'before' ); ?>
				<?php if ( ! empty( $intpart ) || 0 <= $intpart ) : ?>
					<span class="elementor-price-table__integer-part">
						<?php
							// PHPCS - the main text of a widget should not be escaped.
							echo $intpart; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</span>
				<?php  endif; ?>

				<?php  if ( '' !== $fraction || ( ! empty( $settings['period'] ) && 'beside' === $period_position ) ) : ?>
					<div class="elementor-price-table__after-price">
						<span class="elementor-price-table__fractional-part">
							<?php
								// PHPCS - the main text of a widget should not be escaped.
								echo $fraction; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</span>

						<?php  if ( ! empty( $settings['period'] ) && 'beside' === $period_position ) : ?>
							<?php
								// PHPCS - already escaped before
								echo $period_element; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						<?php  endif; ?>
					</div>
				<?php  endif; ?>

				<?php $this->render_currency_symbol( $symbol, 'after' ); ?>

				<?php if ( ! empty( $settings['period'] ) && 'below' === $period_position ) : ?>
					<?php
						// PHPCS - already escaped before
						echo $period_element; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['features_list'] ) ) : ?>
				<ul class="elementor-price-table__features-list">
					<?php
					foreach ( $settings['features_list'] as $index => $item ) :
						$repeater_setting_key = $this->get_repeater_setting_key( 'item_text', 'features_list', $index );
						$this->add_inline_editing_attributes( $repeater_setting_key );

						$migrated = isset( $item['__fa4_migrated']['selected_item_icon'] );
						// add old default
						if ( ! isset( $item['item_icon'] ) && ! $migration_allowed ) {
							$item['item_icon'] = 'fa fa-check-circle';
						}
						$is_new = ! isset( $item['item_icon'] ) && $migration_allowed;
						?>
						<li class="elementor-repeater-item-<?php echo $item['_id']; ?>">
							<div class="elementor-price-table__feature-inner">
								<?php if ( ! empty( $item['item_icon'] ) || ! empty( $item['selected_item_icon'] ) ) :
									if ( $is_new || $migrated ) :
										Icons_Manager::render_icon( $item['selected_item_icon'], [ 'aria-hidden' => 'true' ] );
									else : ?>
										<i class="<?php echo $item['item_icon']; ?>" aria-hidden="true"></i>
										<?php
									endif;
								endif; ?>
								<?php if ( ! empty( $item['item_text'] ) ) : ?>
									<span <?php $this->print_render_attribute_string( $repeater_setting_key ); ?>>
										<?php $this->print_unescaped_setting( 'item_text', 'features_list', $index ); ?>
									</span>
									<?php
								else :
									echo '&nbsp;';
								endif;
								?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $settings['button_text'] ) || ! empty( $settings['footer_additional_info'] ) ) : ?>
				<div class="elementor-price-table__footer">
					<?php if ( ! empty( $settings['button_text'] ) ) : ?>
						<a <?php $this->print_render_attribute_string( 'button_text' ); ?>>
							<?php $this->print_unescaped_setting( 'button_text' ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $settings['footer_additional_info'] ) ) : ?>
						<div <?php $this->print_render_attribute_string( 'footer_additional_info' ); ?>>
							<?php $this->print_unescaped_setting( 'footer_additional_info' ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		if ( 'yes' === $settings['show_ribbon'] && ! empty( $settings['ribbon_title'] ) ) :
			$this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-price-table__ribbon' );

			if ( ! empty( $settings['ribbon_horizontal_position'] ) ) :
				$this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_horizontal_position'] );
			endif;

			?>
			<div <?php $this->print_render_attribute_string( 'ribbon-wrapper' ); ?>>
				<div <?php $this->print_render_attribute_string( 'ribbon_title' ); ?>>
					<?php $this->print_unescaped_setting( 'ribbon_title' ); ?>
				</div>
			</div>
			<?php
		 endif;
	}

	/**
	 * Render Price Table widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var symbols = {
				dollar: '&#36;',
				euro: '&#128;',
				franc: '&#8355;',
				pound: '&#163;',
				ruble: '&#8381;',
				shekel: '&#8362;',
				baht: '&#3647;',
				yen: '&#165;',
				won: '&#8361;',
				guilder: '&fnof;',
				peso: '&#8369;',
				peseta: '&#8359;',
				lira: '&#8356;',
				rupee: '&#8360;',
				indian_rupee: '&#8377;',
				real: 'R$',
				krona: 'kr'
			};

			var symbol = '',
				iconsHTML = {};

			if ( settings.currency_symbol ) {
				if ( 'custom' !== settings.currency_symbol ) {
					symbol = symbols[ settings.currency_symbol ] || '';
				} else {
					symbol = settings.currency_symbol_custom;
				}
			}

			var buttonClasses = 'elementor-price-table__button elementor-button elementor-size-' + settings.button_size;

			if ( settings.button_hover_animation ) {
				buttonClasses += ' elementor-animation-' + settings.button_hover_animation;
			}

		view.addRenderAttribute( 'heading', 'class', 'elementor-price-table__heading' );
		view.addRenderAttribute( 'sub_heading', 'class', 'elementor-price-table__subheading' );
		view.addRenderAttribute( 'period', 'class', ['elementor-price-table__period', 'elementor-typo-excluded'] );
		view.addRenderAttribute( 'footer_additional_info', 'class', 'elementor-price-table__additional_info'  );
		view.addRenderAttribute( 'button_text', 'class', buttonClasses  );
		view.addRenderAttribute( 'ribbon_title', 'class', 'elementor-price-table__ribbon-inner'  );

		view.addInlineEditingAttributes( 'heading', 'none' );
		view.addInlineEditingAttributes( 'sub_heading', 'none' );
		view.addInlineEditingAttributes( 'period', 'none' );
		view.addInlineEditingAttributes( 'footer_additional_info' );
		view.addInlineEditingAttributes( 'button_text' );
		view.addInlineEditingAttributes( 'ribbon_title' );

		var currencyFormat = settings.currency_format || '.',
			price = settings.price.split( currencyFormat ),
			intpart = price[0],
			fraction = price[1],

			periodElement = '<span ' + view.getRenderAttributeString( "period" ) + '>' + settings.period + '</span>';

		#>
		<div class="elementor-price-table">
			<# if ( settings.heading || settings.sub_heading ) { #>
				<div class="elementor-price-table__header">
					<# if ( settings.heading ) { #>
						<# var headingTag = elementorPro.validateHTMLTag( settings.heading_tag ) #>
						<{{ headingTag }} {{{ view.getRenderAttributeString( 'heading' ) }}}>{{{ settings.heading }}}</{{ headingTag }}>
					<# } #>
					<# if ( settings.sub_heading ) { #>
						<span {{{ view.getRenderAttributeString( 'sub_heading' ) }}}>{{{ settings.sub_heading }}}</span>
					<# } #>
				</div>
			<# } #>

			<div class="elementor-price-table__price">
				<# if ( settings.sale && settings.original_price ) { #>
					<div class="elementor-price-table__original-price elementor-typo-excluded">
						<# if ( ! _.isEmpty( symbol ) && ( 'before' == settings.currency_position || _.isEmpty( settings.currency_position ) ) ) { #>
							<span class="elementor-price-table__currency">{{{ symbol }}}</span>{{{ settings.original_price }}}
						<# } #>
						<#
						/* The duplicate usage of the original price setting in the "if blocks" is to avoid whitespace between the number and the symbol. */
						if ( _.isEmpty( symbol ) ) {
						#>
							{{{ settings.original_price }}}
						<# } #>
						<# if ( ! _.isEmpty( symbol ) && 'after' == settings.currency_position ) { #>
						{{{ settings.original_price }}}<span class="elementor-price-table__currency">{{{ symbol }}}</span>
						<# } #>
					</div>
				<# } #>

				<# if ( ! _.isEmpty( symbol ) && ( 'before' == settings.currency_position || _.isEmpty( settings.currency_position ) ) ) { #>
					<span class="elementor-price-table__currency elementor-currency--before">{{{ symbol }}}</span>
				<# } #>
				<# if ( intpart ) { #>
					<span class="elementor-price-table__integer-part">{{{ intpart }}}</span>
				<# } #>
				<div class="elementor-price-table__after-price">
					<# if ( fraction ) { #>
						<span class="elementor-price-table__fractional-part">{{{ fraction }}}</span>
					<# } #>
					<# if ( settings.period && 'beside' === settings.period_position ) { #>
						{{{ periodElement }}}
					<# } #>
				</div>

				<# if ( ! _.isEmpty( symbol ) && 'after' == settings.currency_position ) { #>
				<span class="elementor-price-table__currency elementor-currency--after">{{{ symbol }}}</span>
				<# } #>

				<# if ( settings.period && 'below' === settings.period_position ) { #>
					{{{ periodElement }}}
				<# } #>
			</div>

			<# if ( settings.features_list ) { #>
				<ul class="elementor-price-table__features-list">
				<# _.each( settings.features_list, function( item, index ) {

					var featureKey = view.getRepeaterSettingKey( 'item_text', 'features_list', index ),
						migrated = elementor.helpers.isIconMigrated( item, 'selected_item_icon' );

					view.addInlineEditingAttributes( featureKey ); #>

						<li class="elementor-repeater-item-{{ item._id }}">
							<div class="elementor-price-table__feature-inner">
								<# if ( item.item_icon  || item.selected_item_icon ) {
									iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.selected_item_icon, { 'aria-hidden': 'true' }, 'i', 'object' );
									if ( ( ! item.item_icon || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
										{{{ iconsHTML[ index ].value }}}
									<# } else { #>
										<i class="{{ item.item_icon }}" aria-hidden="true"></i>
									<# }
								} #>
								<# if ( ! _.isEmpty( item.item_text.trim() ) ) { #>
									<span {{{ view.getRenderAttributeString( featureKey ) }}}>{{{ item.item_text }}}</span>
								<# } else { #>
									&nbsp;
								<# } #>
							</div>
						</li>
					<# } ); #>
				</ul>
			<# } #>

			<# if ( settings.button_text || settings.footer_additional_info ) { #>
				<div class="elementor-price-table__footer">
					<# if ( settings.button_text ) { #>
						<a href="#" {{{ view.getRenderAttributeString( 'button_text' ) }}}>{{{ settings.button_text }}}</a>
					<# } #>
					<# if ( settings.footer_additional_info ) { #>
						<p {{{ view.getRenderAttributeString( 'footer_additional_info' ) }}}>{{{ settings.footer_additional_info }}}</p>
					<# } #>
				</div>
			<# } #>
		</div>

		<# if ( 'yes' === settings.show_ribbon && settings.ribbon_title ) {
			var ribbonClasses = 'elementor-price-table__ribbon';
			if ( settings.ribbon_horizontal_position ) {
				ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
			} #>
			<div class="{{ ribbonClasses }}">
				<div {{{ view.getRenderAttributeString( 'ribbon_title' ) }}}>{{{ settings.ribbon_title }}}</div>
			</div>
		<# } #>
		<?php
	}

	public function get_group_name() {
		return 'pricing';
	}
}
