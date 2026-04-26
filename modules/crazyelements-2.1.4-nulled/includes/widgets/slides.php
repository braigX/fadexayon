<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}
class Widget_Slides extends Widget_Base
{

    public function get_name()
    {
        return 'slides';
    }

    public function get_title()
    {
        return PrestaHelper::__('Slides', 'elementor');
    }

    public function get_icon()
    {
        return 'ceicon-slides';
    }

    public function get_keywords()
    {
        return ['slides', 'carousel', 'image', 'title', 'slider'];
    }

    public function get_script_depends()
    {
        return ['imagesloaded'];
    }

    public static function get_button_sizes()
    {
        return [
            'xs' => PrestaHelper::__('Extra Small', 'elementor'),
            'sm' => PrestaHelper::__('Small', 'elementor'),
            'md' => PrestaHelper::__('Medium', 'elementor'),
            'lg' => PrestaHelper::__('Large', 'elementor'),
            'xl' => PrestaHelper::__('Extra Large', 'elementor'),
        ];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_slides',
            [
                'label' => PrestaHelper::__('Slides', 'elementor'),
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs('slides_repeater');

        $repeater->start_controls_tab('background', ['label' => PrestaHelper::__('Background', 'elementor')]);

        $repeater->add_control(
            'background_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#bbbbbb',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'background_image',
            [
                'label' => PrestaHelper::_x('Image', 'Background Control', 'elementor'),
                'type' => Controls_Manager::MEDIA,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-image: url({{URL}})',
                ],
            ]
        );

        $repeater->add_control(
            'background_size',
            [
                'label' => PrestaHelper::_x('Size', 'Background Control', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => PrestaHelper::_x('Cover', 'Background Control', 'elementor'),
                    'contain' => PrestaHelper::_x('Contain', 'Background Control', 'elementor'),
                    'auto' => PrestaHelper::_x('Auto', 'Background Control', 'elementor'),
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-size: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_ken_burns',
            [
                'label' => PrestaHelper::__('Ken Burns Effect', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'zoom_direction',
            [
                'label' => PrestaHelper::__('Zoom Direction', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'in',
                'options' => [
                    'in' => PrestaHelper::__('In', 'elementor'),
                    'out' => PrestaHelper::__('Out', 'elementor'),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_ken_burns',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay',
            [
                'label' => PrestaHelper::__('Background Overlay', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.5)',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_overlay',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-background-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay_blend_mode',
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
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_overlay',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-background-overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('content', ['label' => PrestaHelper::__('Content', 'elementor')]);

        $repeater->add_control(
            'heading',
            [
                'label' => PrestaHelper::__('Title & Description', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('Slide Heading', 'elementor'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => PrestaHelper::__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor'),
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => PrestaHelper::__('Button Text', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => PrestaHelper::__('Click Here', 'elementor'),
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => PrestaHelper::__('Link', 'elementor'),
                'type' => Controls_Manager::URL,
                'placeholder' => PrestaHelper::__('https://your-link.com', 'elementor'),
            ]
        );

        $repeater->add_control(
            'link_click',
            [
                'label' => PrestaHelper::__('Apply Link On', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'slide' => PrestaHelper::__('Whole Slide', 'elementor'),
                    'button' => PrestaHelper::__('Button Only', 'elementor'),
                ],
                'default' => 'slide',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'link[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('style', ['label' => PrestaHelper::__('Style', 'elementor')]);

        $repeater->add_control(
            'custom_style',
            [
                'label' => PrestaHelper::__('Custom', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'description' => PrestaHelper::__('Set custom style that will only affect this specific slide.', 'elementor'),
            ]
        );

        $repeater->add_control(
            'horizontal_position',
            [
                'label' => PrestaHelper::__('Horizontal Position', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => PrestaHelper::__('Left', 'elementor'),
                        'icon' => 'ceicon-h-align-left',
                    ],
                    'center' => [
                        'title' => PrestaHelper::__('Center', 'elementor'),
                        'icon' => 'ceicon-h-align-center',
                    ],
                    'right' => [
                        'title' => PrestaHelper::__('Right', 'elementor'),
                        'icon' => 'ceicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-contents' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'vertical_position',
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
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner' => 'align-items: {{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'text_align',
            [
                'label' => PrestaHelper::__('Text Align', 'elementor'),
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
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner' => 'text-align: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'content_color',
            [
                'label' => PrestaHelper::__('Content Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-heading' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-description' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'repeater_text_shadow',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-contents',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'slides',
            [
                'label' => PrestaHelper::__('Slides', 'elementor'),
                'type' => Controls_Manager::REPEATER,
                'show_label' => true,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => PrestaHelper::__('Slide 1 Heading', 'elementor'),
                        'description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'button_text' => PrestaHelper::__('Click Here', 'elementor'),
                        'background_color' => '#833ca3',
                    ],
                    [
                        'heading' => PrestaHelper::__('Slide 2 Heading', 'elementor'),
                        'description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'button_text' => PrestaHelper::__('Click Here', 'elementor'),
                        'background_color' => '#4054b2',
                    ],
                    [
                        'heading' => PrestaHelper::__('Slide 3 Heading', 'elementor'),
                        'description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'button_text' => PrestaHelper::__('Click Here', 'elementor'),
                        'background_color' => '#1abc9c',
                    ],
                ],
                'title_field' => '{{{ heading }}}',
            ]
        );

        $this->add_responsive_control(
            'slides_height',
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
                'default' => [
                    'size' => 400,
                ],
                'size_units' => ['px', 'vh', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider_options',
            [
                'label' => PrestaHelper::__('Slider Options', 'elementor'),
                'type' => Controls_Manager::SECTION,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label' => PrestaHelper::__('Navigation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => PrestaHelper::__('Arrows and Dots', 'elementor'),
                    'arrows' => PrestaHelper::__('Arrows', 'elementor'),
                    'dots' => PrestaHelper::__('Dots', 'elementor'),
                    'none' => PrestaHelper::__('None', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => PrestaHelper::__('Autoplay', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => PrestaHelper::__('Pause on Hover', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'render_type' => 'none',
                'frontend_available' => true,
                'condition' => [
                    'autoplay!' => '',
                ],
            ]
        );

        $this->add_control(
            'pause_on_interaction',
            [
                'label' => PrestaHelper::__('Pause on Interaction', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'render_type' => 'none',
                'frontend_available' => true,
                'condition' => [
                    'autoplay!' => '',
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => PrestaHelper::__('Autoplay Speed', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide' => 'transition-duration: calc({{VALUE}}ms*1.2)',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => PrestaHelper::__('Infinite Loop', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'transition',
            [
                'label' => PrestaHelper::__('Transition', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => PrestaHelper::__('Slide', 'elementor'),
                    'fade' => PrestaHelper::__('Fade', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => PrestaHelper::__('Transition Speed', 'elementor') . ' (ms)',
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'content_animation',
            [
                'label' => PrestaHelper::__('Content Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeInUp',
                'options' => [
                    '' => PrestaHelper::__('None', 'elementor'),
                    'fadeInDown' => PrestaHelper::__('Down', 'elementor'),
                    'fadeInUp' => PrestaHelper::__('Up', 'elementor'),
                    'fadeInRight' => PrestaHelper::__('Right', 'elementor'),
                    'fadeInLeft' => PrestaHelper::__('Left', 'elementor'),
                    'zoomIn' => PrestaHelper::__('Zoom', 'elementor'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_slides',
            [
                'label' => PrestaHelper::__('Slides', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_max_width',
            [
                'label' => PrestaHelper::__('Content Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['%', 'px'],
                'default' => [
                    'size' => '66',
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide-contents' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slides_padding',
            [
                'label' => PrestaHelper::__('Padding', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'slides_horizontal_position',
            [
                'label' => PrestaHelper::__('Horizontal Position', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => PrestaHelper::__('Left', 'elementor'),
                        'icon' => 'ceicon-h-align-left',
                    ],
                    'center' => [
                        'title' => PrestaHelper::__('Center', 'elementor'),
                        'icon' => 'ceicon-h-align-center',
                    ],
                    'right' => [
                        'title' => PrestaHelper::__('Right', 'elementor'),
                        'icon' => 'ceicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'elementor--h-position-',
            ]
        );

        $this->add_control(
            'slides_vertical_position',
            [
                'label' => PrestaHelper::__('Vertical Position', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'middle',
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
                'prefix_class' => 'elementor--v-position-',
            ]
        );

        $this->add_control(
            'slides_text_align',
            [
                'label' => PrestaHelper::__('Text Align', 'elementor'),
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
                    '{{WRAPPER}} .swiper-slide-inner' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .swiper-slide-contents',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => PrestaHelper::__('Title', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_spacing',
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
                    '{{WRAPPER}} .swiper-slide-inner .elementor-slide-heading:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-slide-heading' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => PrestaHelper::__('Title Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-slide-heading',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_spacing',
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
                    '{{WRAPPER}} .swiper-slide-inner .elementor-slide-description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-slide-description' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => PrestaHelper::__('Description Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-slide-description',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => PrestaHelper::__('Button', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => PrestaHelper::__('Size', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'sm',
                'options' => self::get_button_sizes(),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => PrestaHelper::__('Button Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-slide-button',
            ]
        );

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
                    '{{WRAPPER}} .elementor-slide-button' => 'border-width: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .elementor-slide-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab('normal', ['label' => PrestaHelper::__('Normal', 'elementor')]);

        $this->add_control(
            'button_text_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-slide-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementor-slide-button',
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
                    '{{WRAPPER}} .elementor-slide-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('hover', ['label' => PrestaHelper::__('Hover', 'elementor')]);

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-slide-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementor-slide-button:hover',
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
                    '{{WRAPPER}} .elementor-slide-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => PrestaHelper::__('Navigation', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'navigation' => ['arrows', 'dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'heading_style_arrows',
            [
                'label' => PrestaHelper::__('Arrows', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_position',
            [
                'label' => PrestaHelper::__('Arrows Position', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'inside',
                'options' => [
                    'inside' => PrestaHelper::__('Inside', 'elementor'),
                    'outside' => PrestaHelper::__('Outside', 'elementor'),
                ],
                'prefix_class' => 'elementor-arrows-position-',
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label' => PrestaHelper::__('Arrows Size', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => PrestaHelper::__('Arrows Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-swiper-button svg' => 'fill: {{VALUE}}',
                ],
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'heading_style_dots',
            [
                'label' => PrestaHelper::__('Dots', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label' => PrestaHelper::__('Dots Position', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'inside',
                'options' => [
                    'outside' => PrestaHelper::__('Outside', 'elementor'),
                    'inside' => PrestaHelper::__('Inside', 'elementor'),
                ],
                'prefix_class' => 'elementor-pagination-position-',
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_size',
            [
                'label' => PrestaHelper::__('Dots Size', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 15,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => PrestaHelper::__('Dots Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();
        

        if (empty($settings['slides'])) {
            return;
        }
        $this->add_render_attribute('button', 'class', ['elementor-button', 'elementor-slide-button']);

        if (!empty($settings['button_size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['button_size']);
        }

        $slides = [];
        $slide_count = 0;

        foreach ($settings['slides'] as $slide) {
            $slide_html = '';
            $btn_attributes = '';
            $slide_attributes = '';
            $slide_element = 'div';
            $btn_element = 'div';

            if (!empty($slide['link']['url'])) {
                $this->add_link_attributes('slide_link' . $slide_count, $slide['link']);

                if ('button' === $slide['link_click']) {
                    $btn_element = 'a';
                    $btn_attributes = $this->get_render_attribute_string('slide_link' . $slide_count);
                } else {
                    $slide_element = 'a';
                    $slide_attributes = $this->get_render_attribute_string('slide_link' . $slide_count);
                }
            }

            $slide_html .= '<' . $slide_element . ' class="swiper-slide-inner" ' . $slide_attributes . '>';

            $slide_html .= '<div class="swiper-slide-contents">';

            if ($slide['heading']) {
                $slide_html .= '<div class="elementor-slide-heading">' . $slide['heading'] . '</div>';
            }

            if ($slide['description']) {
                $slide_html .= '<div class="elementor-slide-description">' . $slide['description'] . '</div>';
            }

            if ($slide['button_text']) {
                $slide_html .= '<' . $btn_element . ' ' . $btn_attributes . ' ' . $this->get_render_attribute_string('button') . '>' . $slide['button_text'] . '</' . $btn_element . '>';
            }
          
            if ('yes' === $slide['background_overlay']) {
                $slide_html = '<div class="elementor-background-overlay"></div>' . $slide_html;
             
            }

            $ken_class = '';

            if ($slide['background_ken_burns']) {
                $ken_class = ' elementor-ken-burns elementor-ken-burns--' . $slide['zoom_direction'];
            }

            $slide_html = '<div class="swiper-slide-bg' . $ken_class . '"></div>' . $slide_html;

            $slides[] = '<div class="elementor-repeater-item-' . $slide['_id'] . ' swiper-slide">' . $slide_html . '</div>';
            $slide_count++;
        }

        $direction = PrestaHelper::is_rtl() ? 'rtl' : 'ltr';

        $show_dots = (in_array($settings['navigation'], ['dots', 'both']));
        $show_arrows = (in_array($settings['navigation'], ['arrows', 'both']));

        $slides_count = count($settings['slides']);
?>
        <div class="elementor-swiper">
            <div class="elementor-slides-wrapper elementor-main-swiper swiper-container" dir="<?php Utils::print_unescaped_internal_string($direction); ?>" data-animation="<?php $this->print_render_attribute_string($settings['content_animation']); ?>">
                <div class="swiper-wrapper elementor-slides">
                    <?php // PHPCS - Slides for each is safe. 
                    ?>
                    <?php echo implode('', $slides); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                    ?>
                </div>
                <?php if (1 < $slides_count) : ?>
                    <?php if ($show_dots) : ?>
                        <div class="swiper-pagination"></div>
                    <?php endif; ?>
                    <?php if ($show_arrows) : ?>
                        <div class="elementor-swiper-button elementor-swiper-button-prev">
                            <?php $this->render_swiper_button('previous'); ?>
                            <span class="elementor-screen-only"><?php echo PrestaHelper::__('Previous', 'elementor'); ?></span>
                        </div>
                        <div class="elementor-swiper-button elementor-swiper-button-next">
                            <?php $this->render_swiper_button('next'); ?>
                            <span class="elementor-screen-only"><?php echo PrestaHelper::__('Next', 'elementor'); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    /**
     * Render Slides widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     * @access protected
     */
    protected function content_template()
    {
    ?>
        <# var direction=elementorFrontend.config.is_rtl ? 'rtl' : 'ltr' , next=elementorFrontend.config.is_rtl ? 'left' : 'right' , prev=elementorFrontend.config.is_rtl ? 'right' : 'left' , navi=settings.navigation, showDots=( 'dots'===navi || 'both'===navi ), showArrows=( 'arrows'===navi || 'both'===navi ), buttonSize=settings.button_size; #>
            <div class="elementor-swiper">
                <div class="elementor-slides-wrapper elementor-main-swiper swiper-container" dir="{{ direction }}" data-animation="{{ settings.content_animation }}">
                    <div class="swiper-wrapper elementor-slides">
                        <# jQuery.each( settings.slides, function( index, slide ) { #>
                            <div class="elementor-repeater-item-{{ slide._id }} swiper-slide">
                                <# var kenClass='' ; if ( '' !=slide.background_ken_burns ) { kenClass=' elementor-ken-burns elementor-ken-burns--' + slide.zoom_direction; } #>
                                    <div class="swiper-slide-bg{{ kenClass }}"></div>
                                    <# if ( 'yes'===slide.background_overlay ) { #>
                                        <div class="elementor-background-overlay"></div>
                                        <# } #>
                                            <div class="swiper-slide-inner">
                                                <div class="swiper-slide-contents">
                                                    <# if ( slide.heading ) { #>
                                                        <div class="elementor-slide-heading">{{{ slide.heading }}}</div>
                                                        <# } if ( slide.description ) { #>
                                                            <div class="elementor-slide-description">{{{ slide.description }}}</div>
                                                            <# } if ( slide.button_text ) { #>
                                                                <div class="elementor-button elementor-slide-button elementor-size-{{ buttonSize }}">{{{ slide.button_text }}}</div>
                                                                <# } #>
                                                </div>
                                            </div>
                            </div>
                            <# } ); #>
                    </div>
                    <# if ( 1 < settings.slides.length ) { #>
                        <# if ( showDots ) { #>
                            <div class="swiper-pagination"></div>
                            <# } #>
                                <# if ( showArrows ) { #>
                                    <div class="elementor-swiper-button elementor-swiper-button-prev">
                                        <i class="eicon-chevron-{{ prev }}" aria-hidden="true"></i>
                                        <span class="elementor-screen-only"><?php echo PrestaHelper::__('Previous', 'elementor'); ?></span>
                                    </div>
                                    <div class="elementor-swiper-button elementor-swiper-button-next">
                                        <i class="eicon-chevron-{{ next }}" aria-hidden="true"></i>
                                        <span class="elementor-screen-only"><?php echo PrestaHelper::__('Next', 'elementor'); ?></span>
                                    </div>
                                    <# } #>
                                        <# } #>
                </div>
            </div>
    <?php
    }

    private function render_swiper_button($type)
    {
        $direction = 'next' === $type ? 'right' : 'left';
        if (PrestaHelper::is_rtl()) {
            $direction = 'right' === $direction ? 'left' : 'right';
        }

        $icon_value = 'ceicon-chevron-' . $direction;

        Icons_Manager::render_icon([
            'library' => 'ceicons',
            'value' => $icon_value,
        ], ['aria-hidden' => 'true']);
    }
}
