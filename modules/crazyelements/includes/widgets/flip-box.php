<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_FlipBox extends Widget_Base
{

    public function get_name()
    {
        return 'flip-box';
    }

    public function get_title()
    {
        return PrestaHelper::__('Flip Box', 'elementor');
    }

    public function get_icon()
    {
        return 'ceicon-flip-box';
    }

    public function get_categories() {
		return array( 'crazy_addons' );
	}

    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_side_a_content',
            [
                'label' => PrestaHelper::__('Front', 'elementor'),
            ]
        );

        $this->start_controls_tabs('side_a_content_tabs');

        $this->start_controls_tab('side_a_content_tab', ['label' => PrestaHelper::__('Content', 'elementor')]);

        $this->add_control(
            'graphic_element',
            [
                'label' => PrestaHelper::__('Graphic Element', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => PrestaHelper::__('None', 'elementor'),
                        'icon' => 'ceicon-ban',
                    ],
                    'image' => [
                        'title' => PrestaHelper::__('Image', 'elementor'),
                        'icon' => 'fa fa-picture-o',
                    ],
                    'icon' => [
                        'title' => PrestaHelper::__('Icon', 'elementor'),
                        'icon' => 'ceicon-star',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => PrestaHelper::__('Choose Image', 'elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Actually its `image_size`
                'default' => 'thumbnail',
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => PrestaHelper::__('Icon', 'elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_view',
            [
                'label' => PrestaHelper::__('View', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => PrestaHelper::__('Default', 'elementor'),
                    'stacked' => PrestaHelper::__('Stacked', 'elementor'),
                    'framed' => PrestaHelper::__('Framed', 'elementor'),
                ],
                'default' => 'default',
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_shape',
            [
                'label' => PrestaHelper::__('Shape', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'circle' => PrestaHelper::__('Circle', 'elementor'),
                    'square' => PrestaHelper::__('Square', 'elementor'),
                ],
                'default' => 'circle',
                'condition' => [
                    'icon_view!' => 'default',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'title_text_a',
            [
                'label' => PrestaHelper::__('Title & Description', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('This is the heading', 'elementor'),
                'placeholder' => PrestaHelper::__('Enter your title', 'elementor'),
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_text_a',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                'placeholder' => PrestaHelper::__('Enter your description', 'elementor'),
                'separator' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('side_a_background_tab', ['label' => PrestaHelper::__('Background', 'elementor')]);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_a',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .elementor-flip-box__front',
            ]
        );

        $this->add_control(
            'background_overlay_a',
            [
                'label' => PrestaHelper::__('Background Overlay', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'background_a_image[id]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'background_overlay_a_filters',
                'selector' => '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay',
                'condition' => [
                    'background_overlay_a!' => '',
                ],
            ]
        );

        $this->add_control(
            'background_overlay_a_blend_mode',
            [
                'label' => PrestaHelper::__('Blend Mode', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => PrestaHelper::__('Normal', 'elementor'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'color-burn' => 'Color Burn',
                    'hue' => 'Hue',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'exclusion' => 'Exclusion',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
                'condition' => [
                    'background_overlay_a!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_side_b_content',
            [
                'label' => PrestaHelper::__('Back', 'elementor'),
            ]
        );

        $this->start_controls_tabs('side_b_content_tabs');

        $this->start_controls_tab('side_b_content_tab', ['label' => PrestaHelper::__('Content', 'elementor')]);

        $this->add_control(
            'title_text_b',
            [
                'label' => PrestaHelper::__('Title & Description', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('This is the heading', 'elementor'),
                'placeholder' => PrestaHelper::__('Enter your title', 'elementor'),
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'description_text_b',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                'placeholder' => PrestaHelper::__('Enter your description', 'elementor'),
                'separator' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => PrestaHelper::__('Button Text', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('Click Here', 'elementor'),
                'dynamic' => [
                    'active' => true,
                ],
                'separator' => 'before',
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
                'placeholder' => PrestaHelper::__('https://your-link.com', 'elementor'),
            ]
        );

        $this->add_control(
            'link_click',
            [
                'label' => PrestaHelper::__('Apply Link On', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'box' => PrestaHelper::__('Whole Box', 'elementor'),
                    'button' => PrestaHelper::__('Button Only', 'elementor'),
                ],
                'default' => 'button',
                'condition' => [
                    'link[url]!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('side_b_background_tab', ['label' => PrestaHelper::__('Background', 'elementor')]);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_b',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .elementor-flip-box__back',
            ]
        );

        $this->add_control(
            'background_overlay_b',
            [
                'label' => PrestaHelper::__('Background Overlay', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'background_b_image[id]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'background_overlay_b_filters',
                'selector' => '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay',
                'condition' => [
                    'background_overlay_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'background_overlay_b_blend_mode',
            [
                'label' => PrestaHelper::__('Blend Mode', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => PrestaHelper::__('Normal', 'elementor'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'color-burn' => 'Color Burn',
                    'hue' => 'Hue',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'exclusion' => 'Exclusion',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
                'condition' => [
                    'background_overlay_b!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_settings',
            [
                'label' => PrestaHelper::__('Settings', 'elementor'),
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => PrestaHelper::__('Height', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__layer, {{WRAPPER}} .elementor-flip-box__layer__overlay' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'flip_effect',
            [
                'label' => PrestaHelper::__('Flip Effect', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'flip',
                'options' => [
                    'flip' => 'Flip',
                    'slide' => 'Slide',
                    'push' => 'Push',
                    'zoom-in' => 'Zoom In',
                    'zoom-out' => 'Zoom Out',
                    'fade' => 'Fade',
                ],
                'prefix_class' => 'elementor-flip-box--effect-',
            ]
        );

        $this->add_control(
            'flip_direction',
            [
                'label' => PrestaHelper::__('Flip Direction', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'up',
                'options' => [
                    'left' => PrestaHelper::__('Left', 'elementor'),
                    'right' => PrestaHelper::__('Right', 'elementor'),
                    'up' => PrestaHelper::__('Up', 'elementor'),
                    'down' => PrestaHelper::__('Down', 'elementor'),
                ],
                'condition' => [
                    'flip_effect!' => [
                        'fade',
                        'zoom-in',
                        'zoom-out',
                    ],
                ],
                'prefix_class' => 'elementor-flip-box--direction-',
            ]
        );

        $this->add_control(
            'flip_3d',
            [
                'label' => PrestaHelper::__('3D Depth', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => PrestaHelper::__('On', 'elementor'),
                'label_off' => PrestaHelper::__('Off', 'elementor'),
                'return_value' => 'elementor-flip-box--3d',
                'default' => '',
                'prefix_class' => '',
                'condition' => [
                    'flip_effect' => 'flip',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_a',
            [
                'label' => PrestaHelper::__('Front', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'padding_a',
            [
                'label' => PrestaHelper::__('Padding', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'alignment_a',
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
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'vertical_position_a',
            [
                'label' => PrestaHelper::__('Vertical Position', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => PrestaHelper::__('Top', 'elementor'),
                        'icon' => 'ceicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => PrestaHelper::__('Middle', 'elementor'),
                        'icon' => 'ceicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => PrestaHelper::__('Bottom', 'elementor'),
                        'icon' => 'ceicon-v-align-bottom',
                    ],
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__overlay' => 'justify-content: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_a',
                'selector' => '{{WRAPPER}} .elementor-flip-box__front',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_image_style',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Image', 'elementor'),
                'condition' => [
                    'graphic_element' => 'image',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_spacing',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_width',
            [
                'label' => PrestaHelper::__('Size', 'elementor') . ' (%)',
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__image img' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => PrestaHelper::__('Opacity', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__image' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .elementor-flip-box__image img',
                'condition' => [
                    'graphic_element' => 'image',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_control(
            'heading_icon_style',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Icon', 'elementor'),
                'condition' => [
                    'graphic_element' => 'icon',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_spacing',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_primary_color',
            [
                'label' => PrestaHelper::__('Primary Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-view-stacked .elementor-icon svg' => 'stroke: {{VALUE}}',
                    '{{WRAPPER}} .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-view-framed .elementor-icon svg, {{WRAPPER}} .elementor-view-default .elementor-icon svg' => 'fill: {{VALUE}}; border-color: {{VALUE}}',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_secondary_color',
            [
                'label' => PrestaHelper::__('Secondary Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'graphic_element' => 'icon',
                    'icon_view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-view-framed .elementor-icon svg' => 'stroke: {{VALUE}};',
                    '{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-view-stacked .elementor-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => PrestaHelper::__('Icon Size', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-icon svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label' => PrestaHelper::__('Icon Padding', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                    'icon_view!' => 'default',
                ],
            ]
        );

        $this->add_control(
            'icon_rotate',
            [
                'label' => PrestaHelper::__('Icon Rotate', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_border_width',
            [
                'label' => PrestaHelper::__('Border Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                    'icon_view' => 'framed',
                ],
            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                    'icon_view!' => 'default',
                ],
            ]
        );

        $this->add_control(
            'heading_title_style_a',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Title', 'elementor'),
                'separator' => 'before',
                'condition' => [
                    'title_text_a!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_spacing_a',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'description_text_a!' => '',
                    'title_text_a!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_color_a',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__title' => 'color: {{VALUE}}',

                ],
                'condition' => [
                    'title_text_a!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography_a',
                'label' => PrestaHelper::__('Title Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__title',
                'condition' => [
                    'title_text_a!' => '',
                ],
            ]
        );

        $this->add_control(
            'heading_description_style_a',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Description', 'elementor'),
                'separator' => 'before',
                'condition' => [
                    'description_text_a!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_color_a',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__description' => 'color: {{VALUE}}',

                ],
                'condition' => [
                    'description_text_a!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography_a',
                'label' => PrestaHelper::__('Description Typography ', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-flip-box__front .elementor-flip-box__layer__description',
                'condition' => [
                    'description_text_a!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_b',
            [
                'label' => PrestaHelper::__('Back', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'padding_b',
            [
                'label' => PrestaHelper::__('Padding', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'alignment_b',
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
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay' => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .elementor-flip-box__button' => 'margin-{{VALUE}}: 0',
                ],
            ]
        );

        $this->add_control(
            'vertical_position_b',
            [
                'label' => PrestaHelper::__('Vertical Position', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => PrestaHelper::__('Top', 'elementor'),
                        'icon' => 'ceicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => PrestaHelper::__('Middle', 'elementor'),
                        'icon' => 'ceicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => PrestaHelper::__('Bottom', 'elementor'),
                        'icon' => 'ceicon-v-align-bottom',
                    ],
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__overlay' => 'justify-content: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_b',
                'selector' => '{{WRAPPER}} .elementor-flip-box__back',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_title_style_b',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Title', 'elementor'),
                'separator' => 'before',
                'condition' => [
                    'title_text_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_spacing_b',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_text_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_color_b',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__title' => 'color: {{VALUE}}',

                ],
                'condition' => [
                    'title_text_b!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography_b',
                'label' => PrestaHelper::__('Title Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__title',
                'condition' => [
                    'title_text_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'heading_description_style_b',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Description', 'elementor'),
                'separator' => 'before',
                'condition' => [
                    'description_text_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_spacing_b',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'description_text_b!' => '',
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_color_b',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__description' => 'color: {{VALUE}}',

                ],
                'condition' => [
                    'description_text_b!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography_b',
                'label' => PrestaHelper::__('Description Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-flip-box__back .elementor-flip-box__layer__description',
                'condition' => [
                    'description_text_b!' => '',
                ],
            ]
        );

        $this->add_control(
            'heading_button',
            [
                'type' => Controls_Manager::HEADING,
                'label' => PrestaHelper::__('Button', 'elementor'),
                'separator' => 'before',
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => PrestaHelper::__('Size', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'sm',
                'options' => [
                    'xs' => PrestaHelper::__('Extra Small', 'elementor'),
                    'sm' => PrestaHelper::__('Small', 'elementor'),
                    'md' => PrestaHelper::__('Medium', 'elementor'),
                    'lg' => PrestaHelper::__('Large', 'elementor'),
                    'xl' => PrestaHelper::__('Extra Large', 'elementor'),
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .elementor-flip-box__button',
                'label' => PrestaHelper::__('Button Typography', 'elementor'),
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab(
            'normal',
            [
                'label' => PrestaHelper::__('Normal', 'elementor'),
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementor-flip-box__button',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label' => PrestaHelper::__('Border Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'hover',
            [
                'label' => PrestaHelper::__('Hover', 'elementor'),
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementor-flip-box__button:hover',
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
                'label' => PrestaHelper::__('Border Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'button_border_width',
            [
                'label' => PrestaHelper::__('Border Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-flip-box__button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'after',
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
       // $image_src = $settings['image']['url'];
        $wrapper_tag = 'div';
        $button_tag = 'a';
        $migration_allowed = Icons_Manager::is_migration_allowed();
        $this->add_render_attribute('button', 'class', [
            'elementor-flip-box__button',
            'elementor-button',
            'elementor-size-' . $settings['button_size'],
        ]);

        $this->add_render_attribute('wrapper', 'class', 'elementor-flip-box__layer elementor-flip-box__back');

        if (!empty($settings['link']['url'])) {
            $link_element = 'button';

            if ('box' === $settings['link_click']) {
                $wrapper_tag = 'a';
                $button_tag = 'span';
                $link_element = 'wrapper';
            }

            $this->add_link_attributes($link_element, $settings['link']);
        }

        if ('icon' === $settings['graphic_element']) {
            $this->add_render_attribute('icon-wrapper', 'class', 'elementor-icon-wrapper');
            $this->add_render_attribute('icon-wrapper', 'class', 'elementor-view-' . $settings['icon_view']);
            if ('default' != $settings['icon_view']) {
                $this->add_render_attribute('icon-wrapper', 'class', 'elementor-shape-' . $settings['icon_shape']);
            }

            if (!isset($settings['icon']) && !$migration_allowed) {
                // add old default
                $settings['icon'] = 'fa fa-star';
            }

            if (!empty($settings['icon'])) {
                $this->add_render_attribute('icon', 'class', $settings['icon']);
            }
        }

        $has_icon = !empty($settings['icon']) || !empty($settings['selected_icon']);
        $migrated = isset($settings['__fa4_migrated']['selected_icon']);
        $is_new = empty($settings['icon']) && $migration_allowed;

?>
        <div class="elementor-flip-box">
            <div class="elementor-flip-box__layer elementor-flip-box__front">
                <div class="elementor-flip-box__layer__overlay">
                    <div class="elementor-flip-box__layer__inner">
                        <?php if ('image' === $settings['graphic_element'] && !empty($settings['image']['url'])) : ?>
                            <div class="elementor-flip-box__image">                 
                            <img src="<?php echo str_replace('http://', 'https://', $settings['image']['url']); ?>" alt="" title="">
                            </div>
                        <?php elseif ('icon' === $settings['graphic_element'] && $has_icon) : ?>
                            <div <?php $this->print_render_attribute_string('icon-wrapper'); ?>>
                                <div class="elementor-icon">
                                    <?php if ($is_new || $migrated) :
                                        Icons_Manager::render_icon($settings['selected_icon']);
                                    else : ?>
                                        <i <?php $this->print_render_attribute_string('icon'); ?>></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($settings['title_text_a'])) : ?>
                            <h3 class="elementor-flip-box__layer__title">
                                <?php $this->print_unescaped_setting('title_text_a'); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if (!empty($settings['description_text_a'])) : ?>
                            <div class="elementor-flip-box__layer__description">
                                <?php $this->print_unescaped_setting('description_text_a'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <<?php echo $wrapper_tag; ?> <?php $this->print_render_attribute_string('wrapper'); ?>>
                <div class="elementor-flip-box__layer__overlay">
                    <div class="elementor-flip-box__layer__inner">
                        <?php if (!empty($settings['title_text_b'])) : ?>
                            <h3 class="elementor-flip-box__layer__title">
                                <?php $this->print_unescaped_setting('title_text_b'); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if (!empty($settings['description_text_b'])) : ?>
                            <div class="elementor-flip-box__layer__description">
                                <?php $this->print_unescaped_setting('description_text_b'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($settings['button_text'])) : ?>
                            <<?php echo $button_tag; ?> <?php $this->print_render_attribute_string('button'); ?>>
                                <?php $this->print_unescaped_setting('button_text'); ?>
                            </<?php echo $button_tag; ?>>
                        <?php endif; ?>
                    </div>
                </div>
            </<?php echo $wrapper_tag; ?>>
        </div>
    <?php
    }

    /**
     * Render Flip Box widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     * @access protected
     */
    protected function content_template()
    {
    ?>
        <# var btnClasses='elementor-flip-box__button elementor-button elementor-size-' + settings.button_size; if ( 'image'===settings.graphic_element && '' !==settings.image.url ) { var image={ id: settings.image.id, url: settings.image.url, size: settings.image_size, dimension: settings.image_custom_dimension, model: view.getEditModel() }; var imageUrl=elementor.imagesManager.getImageUrl( image ); } var wrapperTag='div' , buttonTag='a' ; if ( 'box'===settings.link_click ) { wrapperTag='a' ; buttonTag='span' ; } if ( 'icon'===settings.graphic_element ) { var iconWrapperClasses='elementor-icon-wrapper' ; iconWrapperClasses +=' elementor-view-' + settings.icon_view; if ( 'default' !==settings.icon_view ) { iconWrapperClasses +=' elementor-shape-' + settings.icon_shape; } } var hasIcon=settings.icon || settings.selected_icon, iconHTML=elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden' : true }, 'i' , 'object' ), migrated=elementor.helpers.isIconMigrated( settings, 'selected_icon' ); #>

            <div class="elementor-flip-box">
                <div class="elementor-flip-box__layer elementor-flip-box__front">
                    <div class="elementor-flip-box__layer__overlay">
                        <div class="elementor-flip-box__layer__inner">
                            <# if ( 'image'===settings.graphic_element && '' !==settings.image.url ) { #>
                                <div class="elementor-flip-box__image">
                                    <img src="{{ imageUrl }}">
                                </div>
                                <# } else if ( 'icon'===settings.graphic_element && hasIcon ) { #>
                                    <div class="{{ iconWrapperClasses }}">
                                        <div class="elementor-icon">
                                            <# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
                                                {{{ iconHTML.value }}}
                                                <# } else { #>
                                                    <i class="{{ settings.icon }}"></i>
                                                    <# } #>
                                        </div>
                                    </div>
                                    <# } #>

                                        <# if ( settings.title_text_a ) { #>
                                            <h3 class="elementor-flip-box__layer__title">{{{ settings.title_text_a }}}</h3>
                                            <# } #>

                                                <# if ( settings.description_text_a ) { #>
                                                    <div class="elementor-flip-box__layer__description">{{{ settings.description_text_a }}}</div>
                                                    <# } #>
                        </div>
                    </div>
                </div>
                <{{ wrapperTag }} class="elementor-flip-box__layer elementor-flip-box__back">
                    <div class="elementor-flip-box__layer__overlay">
                        <div class="elementor-flip-box__layer__inner">
                            <# if ( settings.title_text_b ) { #>
                                <h3 class="elementor-flip-box__layer__title">{{{ settings.title_text_b }}}</h3>
                                <# } #>

                                    <# if ( settings.description_text_b ) { #>
                                        <div class="elementor-flip-box__layer__description">{{{ settings.description_text_b }}}</div>
                                        <# } #>

                                            <# if ( settings.button_text ) { #>
                                                <{{ buttonTag }} href="#" class="{{ btnClasses }}">{{{ settings.button_text }}}</{{ buttonTag }}>
                                                <# } #>
                        </div>
                    </div>
                </{{ wrapperTag }}>
            </div>
    <?php
    }
}
