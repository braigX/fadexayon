<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}
class Widget_Gallery extends Widget_Base
{

    /**
     * Get element name.
     *
     * Retrieve the element name.
     *
     * @return string The name.
     * @since 2.7.0
     * @access public
     *
     */
    public function get_name()
    {
        return 'gallery';
    }

    public function get_title()
    {
        return PrestaHelper::__('Gallery', 'elementor');
    }

    public function get_script_depends()
    {
        return ['elementor-gallery'];
    }

    public function get_style_depends()
    {
        return ['elementor-gallery'];
    }

    public function get_icon()
    {
        return 'ceicon-gallery-justified';
    }

    public function get_inline_css_depends()
    {
        if ('multiple' === $this->get_settings_for_display('gallery_type')) {
            return ['nav-menu'];
        }

        return [];
    }

    protected function _register_controls()
    {
        $this->start_controls_section('settings', ['label' => PrestaHelper::__('Settings', 'elementor')]);

        $this->add_control(
            'gallery_type',
            [
                'type' => Controls_Manager::SELECT,
                'label' => PrestaHelper::__('Type', 'elementor'),
                'default' => 'single',
                'options' => [
                    'single' => PrestaHelper::__('Single', 'elementor'),
                    'multiple' => PrestaHelper::__('Multiple', 'elementor'),
                ],
            ]
        );

        $this->add_control(
            'gallery',
            [
                'type' => Controls_Manager::GALLERY,
                'condition' => [
                    'gallery_type' => 'single',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'gallery_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => PrestaHelper::__('Title', 'elementor'),
                'default' => PrestaHelper::__('New Gallery', 'elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'multiple_gallery',
            [
                'type' => Controls_Manager::GALLERY,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'galleries',
            [
                'type' => Controls_Manager::REPEATER,
                'label' => PrestaHelper::__('Galleries', 'elementor'),
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ gallery_title }}}',
                'default' => [
                    [
                        'gallery_title' => PrestaHelper::__('New Gallery', 'elementor'),
                    ],
                ],
                'condition' => [
                    'gallery_type' => 'multiple',
                ],
            ]
        );

        $this->add_control(
            'order_by',
            [
                'type' => Controls_Manager::SELECT,
                'label' => PrestaHelper::__('Order By', 'elementor'),
                'options' => [
                    '' => PrestaHelper::__('Default', 'elementor'),
                    'random' => PrestaHelper::__('Random', 'elementor'),
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'lazyload',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => PrestaHelper::__('Lazy Load', 'elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'gallery_layout',
            [
                'type' => Controls_Manager::SELECT,
                'label' => PrestaHelper::__('Layout', 'elementor'),
                'default' => 'grid',
                'options' => [
                    'grid' => PrestaHelper::__('Grid', 'elementor'),
                    'justified' => PrestaHelper::__('Justified', 'elementor'),
                    'masonry' => PrestaHelper::__('Masonry', 'elementor'),
                ],
                'separator' => 'before',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => PrestaHelper::__('Columns', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'min' => 1,
                'max' => 24,
                'condition' => [
                    'gallery_layout!' => 'justified',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        // $active_breakpoints = Plugin::crazyelements()->breakpoints->get_active_breakpoints();
        // echo $active_breakpoints;
        $ideal_row_height_device_args = [];
        $gap_device_args = [];

        // Add default values for all active breakpoints.
        // foreach ($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
        //     if ('widescreen' !== $breakpoint_name) {
        //         $ideal_row_height_device_args[$breakpoint_name] = [
        //             'default' => [
        //                 'size' => 150,
        //             ],
        //         ];

        //         $gap_device_args[$breakpoint_name] = [
        //             'default' => [
        //                 'size' => 10,
        //             ],
        //         ];
        //     }
        // }

        $this->add_responsive_control(
            'ideal_row_height',
            [
                'label' => PrestaHelper::__('Row Height', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 200,
                ],
                'device_args' => $ideal_row_height_device_args,
                'condition' => [
                    'gallery_layout' => 'justified',
                ],
                'required' => true,
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'device_args' => $gap_device_args,
                'required' => true,
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => PrestaHelper::__('Link', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'file',
                'options' => [
                    '' => PrestaHelper::__('None', 'elementor'),
                    'file' => PrestaHelper::__('Media File', 'elementor'),
                    'custom' => PrestaHelper::__('Custom URL', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'url',
            [
                'label' => PrestaHelper::__('URL', 'elementor'),
                'type' => Controls_Manager::URL,
                'condition' => [
                    'link_to' => 'custom',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'aspect_ratio',
            [
                'type' => Controls_Manager::SELECT,
                'label' => PrestaHelper::__('Aspect Ratio', 'elementor'),
                'default' => '3:2',
                'options' => [
                    '1:1' => '1:1',
                    '3:2' => '3:2',
                    '4:3' => '4:3',
                    '9:16' => '9:16',
                    '16:9' => '16:9',
                    '21:9' => '21:9',
                ],
                'condition' => [
                    'gallery_layout' => 'grid',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_image',
                'default' => 'medium',
            ]
        );

        $this->end_controls_section(); // settings

        $this->start_controls_section(
            'section_filter_bar_content',
            [
                'label' => PrestaHelper::__('Filter Bar', 'elementor'),
                'condition' => [
                    'gallery_type' => 'multiple',
                ],
            ]
        );

        $this->add_control(
            'show_all_galleries',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => PrestaHelper::__('"All" Filter', 'elementor'),
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'show_all_galleries_label',
            [
                'type' => Controls_Manager::TEXT,
                'label' => PrestaHelper::__('"All" Filter Label', 'elementor'),
                'default' => PrestaHelper::__('All', 'elementor'),
                'condition' => [
                    'show_all_galleries' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pointer',
            [
                'label' => PrestaHelper::__('Pointer', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'underline',
                'options' => [
                    'none' => PrestaHelper::__('None', 'elementor'),
                    'underline' => PrestaHelper::__('Underline', 'elementor'),
                    'overline' => PrestaHelper::__('Overline', 'elementor'),
                    'double-line' => PrestaHelper::__('Double Line', 'elementor'),
                    'framed' => PrestaHelper::__('Framed', 'elementor'),
                    'background' => PrestaHelper::__('Background', 'elementor'),
                    'text' => PrestaHelper::__('Text', 'elementor'),
                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'animation_line',
            [
                'label' => PrestaHelper::__('Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => 'Fade',
                    'slide' => 'Slide',
                    'grow' => 'Grow',
                    'drop-in' => 'Drop In',
                    'drop-out' => 'Drop Out',
                    'none' => 'None',
                ],
                'condition' => [
                    'pointer' => ['underline', 'overline', 'double-line'],
                ],
            ]
        );

        $this->add_control(
            'animation_framed',
            [
                'label' => PrestaHelper::__('Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => 'Fade',
                    'grow' => 'Grow',
                    'shrink' => 'Shrink',
                    'draw' => 'Draw',
                    'corners' => 'Corners',
                    'none' => 'None',
                ],
                'condition' => [
                    'pointer' => 'framed',
                ],
            ]
        );

        $this->add_control(
            'animation_background',
            [
                'label' => PrestaHelper::__('Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => 'Fade',
                    'grow' => 'Grow',
                    'shrink' => 'Shrink',
                    'sweep-left' => 'Sweep Left',
                    'sweep-right' => 'Sweep Right',
                    'sweep-up' => 'Sweep Up',
                    'sweep-down' => 'Sweep Down',
                    'shutter-in-vertical' => 'Shutter In Vertical',
                    'shutter-out-vertical' => 'Shutter Out Vertical',
                    'shutter-in-horizontal' => 'Shutter In Horizontal',
                    'shutter-out-horizontal' => 'Shutter Out Horizontal',
                    'none' => 'None',
                ],
                'condition' => [
                    'pointer' => 'background',
                ],
            ]
        );

        $this->add_control(
            'animation_text',
            [
                'label' => PrestaHelper::__('Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grow',
                'options' => [
                    'grow' => 'Grow',
                    'shrink' => 'Shrink',
                    'sink' => 'Sink',
                    'float' => 'Float',
                    'skew' => 'Skew',
                    'rotate' => 'Rotate',
                    'none' => 'None',
                ],
                'condition' => [
                    'pointer' => 'text',
                ],
            ]
        );

        $this->end_controls_section(); // settings

        $this->start_controls_section('overlay', ['label' => PrestaHelper::__('Overlay', 'elementor')]);

        $this->add_control(
            'overlay_background',
            [
                'label' => PrestaHelper::__('Background', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'overlay_title',
            [
                'label' => PrestaHelper::__('Title', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => PrestaHelper::__('None', 'elementor'),
                    'title' => PrestaHelper::__('Title', 'elementor'),
                    'caption' => PrestaHelper::__('Caption', 'elementor'),
                    'alt' => PrestaHelper::__('Alt', 'elementor'),
                    'description' => PrestaHelper::__('Description', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'overlay_description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => PrestaHelper::__('None', 'elementor'),
                    'title' => PrestaHelper::__('Title', 'elementor'),
                    'caption' => PrestaHelper::__('Caption', 'elementor'),
                    'alt' => PrestaHelper::__('Alt', 'elementor'),
                    'description' => PrestaHelper::__('Description', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section(); // overlay

        $this->start_controls_section(
            'image_style',
            [
                'label' => PrestaHelper::__('Image', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('image_tabs');

        $this->start_controls_tab(
            'image_normal',
            [
                'label' => PrestaHelper::__('Normal', 'elementor'),
            ]
        );

        $this->add_control(
            'image_border_color',
            [
                'label' => PrestaHelper::__('Border Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--image-border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'image_border_width',
            [
                'label' => PrestaHelper::__('Border Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}}' => '--image-border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}}' => '--image-border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters',
                'selector' => '{{WRAPPER}} .e-gallery-image',
            ]
        );

        $this->end_controls_tab(); // overlay_background normal

        $this->start_controls_tab(
            'image_hover',
            [
                'label' => PrestaHelper::__('Hover', 'elementor'),
            ]
        );

        $this->add_control(
            'image_border_color_hover',
            [
                'label' => PrestaHelper::__('Border Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-gallery-item:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius_hover',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-gallery-item:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .e-gallery-item:hover .e-gallery-image',
            ]
        );

        $this->end_controls_tab(); // overlay_background normal

        $this->end_controls_tabs(); // overlay_background tabs

        $this->add_control(
            'image_hover_animation',
            [
                'label' => PrestaHelper::__('Hover Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => 'None',
                    'grow' => 'Zoom In',
                    'shrink-contained' => 'Zoom Out',
                    'move-contained-left' => 'Move Left',
                    'move-contained-right' => 'Move Right',
                    'move-contained-top' => 'Move Up',
                    'move-contained-bottom' => 'Move Down',
                ],
                'separator' => 'before',
                'default' => '',
                'frontend_available' => true,
                'render_type' => 'ui',
            ]
        );

        $this->add_control(
            'image_animation_duration',
            [
                'label' => PrestaHelper::__('Animation Duration', 'elementor') . ' (ms)',
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 800,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--image-transition-duration: {{SIZE}}ms',
                ],
            ]
        );

        $this->end_controls_section(); // overlay_background

        $this->start_controls_section(
            'overlay_style',
            [
                'label' => PrestaHelper::__('Overlay', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'overlay_background' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('overlay_background_tabs');

        $this->start_controls_tab(
            'overlay_normal',
            [
                'label' => PrestaHelper::__('Normal', 'elementor'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'overlay_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .elementor-gallery-item__overlay',
                'fields_options' => [
                    'background' => [
                        'label' => PrestaHelper::__('Overlay', 'elementor'),
                    ],
                ],
            ]
        );

        $this->end_controls_tab(); // overlay_background normal

        $this->start_controls_tab(
            'overlay_hover',
            [
                'label' => PrestaHelper::__('Hover', 'elementor'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'overlay_background_hover',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .e-gallery-item:hover .elementor-gallery-item__overlay',
                'exclude' => ['image'],
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => 'rgba(0,0,0,0.5)',
                    ],
                ],
            ]
        );

        $this->end_controls_tab(); // overlay_background normal

        $this->end_controls_tabs(); // overlay_background tabs

        $this->add_control(
            'image_blend_mode',
            [
                'label' => PrestaHelper::__('Blend Mode', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
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
                    '{{WRAPPER}}' => '--overlay-mix-blend-mode: {{VALUE}}',
                ],
                'separator' => 'before',
                'render_type' => 'ui',
            ]
        );

        $this->add_control(
            'background_overlay_hover_animation',
            [
                'label' => PrestaHelper::__('Hover Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'groups' => [
                    [
                        'label' => PrestaHelper::__('None', 'elementor'),
                        'options' => [
                            '' => PrestaHelper::__('None', 'elementor'),
                        ],
                    ],
                    [
                        'label' => PrestaHelper::__('Entrance', 'elementor'),
                        'options' => [
                            'enter-from-right' => 'Slide In Right',
                            'enter-from-left' => 'Slide In Left',
                            'enter-from-top' => 'Slide In Up',
                            'enter-from-bottom' => 'Slide In Down',
                            'enter-zoom-in' => 'Zoom In',
                            'enter-zoom-out' => 'Zoom Out',
                            'fade-in' => 'Fade In',
                        ],
                    ],
                    [
                        'label' => PrestaHelper::__('Exit', 'elementor'),
                        'options' => [
                            'exit-to-right' => 'Slide Out Right',
                            'exit-to-left' => 'Slide Out Left',
                            'exit-to-top' => 'Slide Out Up',
                            'exit-to-bottom' => 'Slide Out Down',
                            'exit-zoom-in' => 'Zoom In',
                            'exit-zoom-out' => 'Zoom Out',
                            'fade-out' => 'Fade Out',
                        ],
                    ],
                ],
                'separator' => 'before',
                'default' => '',
                'frontend_available' => true,
                'render_type' => 'ui',
            ]
        );

        $this->add_control(
            'background_overlay_animation_duration',
            [
                'label' => PrestaHelper::__('Animation Duration', 'elementor') . ' (ms)',
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 800,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--overlay-transition-duration: {{SIZE}}ms',
                ],
            ]
        );

        $this->end_controls_section(); // overlay_background

        $this->start_controls_section(
            'overlay_content_style',
            [
                'label' => PrestaHelper::__('Content', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                //TODO: add conditions for this section
            ]
        );

        $this->add_control(
            'content_alignment',
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
                    '{{WRAPPER}}' => '--content-text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'content_vertical_position',
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
                    '{{WRAPPER}}' => '--content-justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => PrestaHelper::__('Padding', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--content-padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => PrestaHelper::__('Title', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'overlay_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--title-text-color: {{VALUE}}',
                ],
                'condition' => [
                    'overlay_title!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => PrestaHelper::__('Title Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-gallery-item__title',
                'condition' => [
                    'overlay_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_spacing',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}}' => '--description-margin-top: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'overlay_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'heading_description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'overlay_description!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--description-text-color: {{VALUE}}',
                ],
                'condition' => [
                    'overlay_description!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => PrestaHelper::__('Description Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-gallery-item__description',
                'condition' => [
                    'overlay_description!' => '',
                ],
            ]
        );

        $this->add_control(
            'content_hover_animation',
            [
                'label' => PrestaHelper::__('Hover Animation', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'groups' => [
                    [
                        'label' => PrestaHelper::__('None', 'elementor'),
                        'options' => [
                            '' => PrestaHelper::__('None', 'elementor'),
                        ],
                    ],
                    [
                        'label' => PrestaHelper::__('Entrance', 'elementor'),
                        'options' => [
                            'enter-from-right' => 'Slide In Right',
                            'enter-from-left' => 'Slide In Left',
                            'enter-from-top' => 'Slide In Up',
                            'enter-from-bottom' => 'Slide In Down',
                            'enter-zoom-in' => 'Zoom In',
                            'enter-zoom-out' => 'Zoom Out',
                            'fade-in' => 'Fade In',
                        ],
                    ],
                    [
                        'label' => PrestaHelper::__('Reaction', 'elementor'),
                        'options' => [
                            'grow' => 'Grow',
                            'shrink' => 'Shrink',
                            'move-right' => 'Move Right',
                            'move-left' => 'Move Left',
                            'move-up' => 'Move Up',
                            'move-down' => 'Move Down',
                        ],
                    ],
                    [
                        'label' => PrestaHelper::__('Exit', 'elementor'),
                        'options' => [
                            'exit-to-right' => 'Slide Out Right',
                            'exit-to-left' => 'Slide Out Left',
                            'exit-to-top' => 'Slide Out Up',
                            'exit-to-bottom' => 'Slide Out Down',
                            'exit-zoom-in' => 'Zoom In',
                            'exit-zoom-out' => 'Zoom Out',
                            'fade-out' => 'Fade Out',
                        ],
                    ],
                ],
                'default' => 'fade-in',
                'separator' => 'before',
                'render_type' => 'ui',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'content_animation_duration',
            [
                'label' => PrestaHelper::__('Animation Duration', 'elementor') . ' (ms)',
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 800,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--content-transition-duration: {{SIZE}}ms; --content-transition-delay: {{SIZE}}ms;',
                ],
                'condition' => [
                    'content_hover_animation!' => '',
                ],
            ]
        );

        $this->add_control(
            'content_sequenced_animation',
            [
                'label' => PrestaHelper::__('Sequenced Animation', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'content_hover_animation!' => '',
                ],
                'frontend_available' => true,
                'render_type' => 'ui',
            ]
        );

        $this->end_controls_section(); // overlay_content

        $this->start_controls_section(
            'filter_bar_style',
            [
                'label' => PrestaHelper::__('Filter Bar', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'gallery_type' => 'multiple',
                ],
            ]
        );

        $this->add_control(
            'align_filter_bar_items',
            [
                'label' => PrestaHelper::__('Align', 'elementor'),
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
                'prefix_class' => 'elementor-gallery--filter-align-',
                'selectors_dictionary' => [
                    'left' => 'flex-start',
                    'right' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--titles-container-justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->start_controls_tabs('filter_bar_colors');

        $this->start_controls_tab(
            'filter_bar_colors_normal',
            [
                'label' => PrestaHelper::__('Normal', 'elementor'),
            ]
        );

        $this->add_control(
            'galleries_title_color_normal',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-title-color-normal: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'galleries_titles_typography',
                'label' => PrestaHelper::__('Gallery Title Typography', 'elementor'),
                'selector' => '{{WRAPPER}} .elementor-gallery-title',
                
            ]
        );

        $this->end_controls_tab(); // filter_bar_colors_normal

        $this->start_controls_tab(
            'filter_bar_colors_hover',
            [
                'label' => PrestaHelper::__('Hover', 'elementor'),
            ]
        );

        $this->add_control(
            'galleries_title_color_hover',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-title-color-hover: {{VALUE}}',
                ],
                'condition' => [
                    'pointer!' => 'background',
                ],
            ]
        );

        /*
		When the pointer style = background, users could need a different text color.
		The control handles the title color in hover state, only when the pointer style is background.
		*/
        $this->add_control(
            'galleries_title_color_hover_pointer_bg',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-title-color-hover: {{VALUE}}',
                ],
                'condition' => [
                    'pointer' => 'background',
                ],
            ]
        );

        $this->add_control(
            'galleries_pointer_color_hover',
            [
                'label' => PrestaHelper::__('Pointer Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-pointer-bg-color-hover: {{VALUE}}',
                ],
                'condition' => [
                    'pointer!' => ['none', 'text'],
                ],
            ]
        );

        $this->end_controls_tab(); // filter_bar_colors_hover

        $this->start_controls_tab(
            'filter_bar_colors_active',
            [
                'label' => PrestaHelper::__('Active', 'elementor'),
            ]
        );

        $this->add_control(
            'galleries_title_color_active',
            [
                'label' => PrestaHelper::__('Text Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--gallery-title-color-active: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'galleries_pointer_color_active',
            [
                'label' => PrestaHelper::__('Pointer Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-pointer-bg-color-active: {{VALUE}}',
                ],
                'condition' => [
                    'pointer!' => ['none', 'text'],
                ],

            ]
        );

        $this->end_controls_tab(); // filter_bar_colors_active

        $this->end_controls_tabs(); // filter_bar_colors

        $this->add_control(
            'pointer_width',
            [
                'label' => PrestaHelper::__('Pointer Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                //'devices' => [Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP, Breakpoints_Manager::BREAKPOINT_KEY_TABLET],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--galleries-pointer-border-width: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
                'condition' => [
                    'pointer' => ['underline', 'overline', 'double-line', 'framed'],
                ],
            ]
        );

        $this->add_control(
            'galleries_titles_space_between',
            [
                'label' => PrestaHelper::__('Space Between', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-gallery-title' => '--space-between: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'galleries_titles_gap',
            [
                'label' => PrestaHelper::__('Gap', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-gallery__titles-container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section(); // filter_bar_style
    }

    protected function render_static()
    {
        $settings = $this->get_settings_for_display();

        $is_multiple = 'multiple' === $settings['gallery_type'] && !empty($settings['galleries']);

        $is_single = 'single' === $settings['gallery_type'] && !empty($settings['gallery']);

        $gap = $settings['gap']['size'] . $settings['gap']['unit'];
        $ratio_percentage = '75';
        $columns = 4;

        if ($settings['columns']) {
            $columns = $settings['columns'];
        }

        if ($settings['aspect_ratio']) {
            $ratio_array = explode(':', $settings['aspect_ratio']);

            $ratio_percentage = ($ratio_array[1] / $ratio_array[0]) * 100;
        }

        $this->add_render_attribute(
            'gallery_container',
            [
                'style' => "--columns: {$columns}; --aspect-ratio: {$ratio_percentage}%; --hgap: {$gap}; --vgap: {$gap};",
                'class' => 'e-gallery-grid',
            ]
        );

        $galleries = [];

        if ($is_multiple) {
            foreach (array_values($settings['galleries']) as $multi_gallery) {
                if (!$multi_gallery['multiple_gallery']) {
                    continue;
                }

                $galleries[] = $multi_gallery['multiple_gallery'];
            }
        } elseif ($is_single) {
            $galleries[0] = $settings['gallery'];
        }

        foreach ($galleries as $gallery) {
            foreach ($gallery as $item) {
               // $image_src = wp_get_attachment_image_src($item['id']);
                $image_src = $item['url'];

                $this->add_render_attribute('gallery_item_image_' . $item['url'], [
                    'style' => "background-image: url('{$image_src[0]}');",
                ]);
            }
        }

        $this->render();
    }

    /**
     *
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $is_multiple = 'multiple' === $settings['gallery_type'] && !empty($settings['galleries']);

        $is_single = 'single' === $settings['gallery_type'] && !empty($settings['gallery']);

        $has_description = !empty($settings['overlay_description']);

        $has_title = !empty($settings['overlay_title']);

        $has_animation = !empty($settings['image_hover_animation']) || !empty($settings['content_hover_animation']) || !empty($settings['background_overlay_hover_animation']);

        $gallery_item_tag = !empty($settings['link_to']) ? 'a' : 'div';

        $galleries = [];

        if ($is_multiple) {
            $this->add_render_attribute('titles-container', 'class', 'elementor-gallery__titles-container');

            if ($settings['pointer']) {
                $this->add_render_attribute('titles-container', 'class', 'e--pointer-' . $settings['pointer']);

                foreach ($settings as $key => $value) {
                    if (0 === strpos($key, 'animation') && $value) {
                        $this->add_render_attribute('titles-container', 'class', 'e--animation-' . $value);
                        break;
                    }
                }
            } ?>
            <div <?php $this->print_render_attribute_string('titles-container'); ?>>
                <?php if ($settings['show_all_galleries']) { ?>
                    <a data-gallery-index="all" class="elementor-item elementor-gallery-title">
                        <?php $this->print_unescaped_setting('show_all_galleries_label'); ?>
                    </a>
                <?php } ?>

                <?php foreach ($settings['galleries'] as $index => $gallery) :
                    if (!$gallery['multiple_gallery']) {
                        continue;
                    }

                    $galleries[$index] = $gallery['multiple_gallery'];
                ?>
                    <a data-gallery-index="<?php echo PrestaHelper::esc_attr($index); ?>" class="elementor-item elementor-gallery-title">
                        <?php $this->print_unescaped_setting('gallery_title', 'galleries', $index); ?>
                    </a>
                <?php
                endforeach; ?>
            </div>
        <?php
        } elseif ($is_single) {
            $galleries[0] = $settings['gallery'];
        } elseif (Plugin::$instance->editor->is_edit_mode()) { 
        ?>
       <i class="elementor-widget-empty-icon eicon-gallery-justified"></i>
        <?php  }

        $this->add_render_attribute('gallery_container', 'class', 'elementor-gallery__container');

        if ($has_title || $has_description) {
            $this->add_render_attribute('gallery_item_content', 'class', 'elementor-gallery-item__content');

            if ($has_title) {
                $this->add_render_attribute('gallery_item_title', 'class', 'elementor-gallery-item__title');
            }

            if ($has_description) {
                $this->add_render_attribute('gallery_item_description', 'class', 'elementor-gallery-item__description');
            }
        }

        $this->add_render_attribute('gallery_item_background_overlay', ['class' => 'elementor-gallery-item__overlay']);

        $gallery_items = [];
        $thumbnail_size = $settings['thumbnail_image_size'];
        foreach ($galleries as $gallery_index => $gallery) {
            foreach ($gallery as $index => $item) {
               
                if (in_array($item['url'], array_keys($gallery_items), true)) {
                    $gallery_items[$item['url']][] = $gallery_index;
                } else {
                    $gallery_items[$item['url']] = [$gallery_index];
                }
            }
        }

        if ('random' === $settings['order_by']) {
            $shuffled_items = [];
            $keys = array_keys($gallery_items);
            shuffle($keys);
            foreach ($keys as $key) {
                $shuffled_items[$key] = $gallery_items[$key];
            }
            $gallery_items = $shuffled_items;
        }

        if (!empty($galleries)) { ?>
            <div <?php $this->print_render_attribute_string('gallery_container'); ?>>
                <?php
                foreach ($gallery_items as $id => $tags) :
                   $unique_index = $id; //$gallery_index . '_' . $index;
                    // $image_src = wp_get_attachment_image_src($id, $thumbnail_size);
                    $image_src = $item['url'];
                    if (!$image_src) {
                        continue;
                    }
                   // $attachment = get_post($id);
                   
                    $image_data = [
                    //    'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
                    //    'media' => wp_get_attachment_image_src($id, 'full')['0'],
                        'src' => $image_src['0'],
                        'width' => $image_src['1'],
                        'height' => $image_src['2'],
                        // 'caption' => $attachment->post_excerpt,
                        // 'description' => $attachment->post_content,
                        // 'title' => $attachment->post_title,
                    ];
                    
                    $this->add_render_attribute('gallery_item_' . $unique_index, [
                        'class' => [
                            'e-gallery-item',
                            'elementor-gallery-item',
                        ],
                    ]);

                    if ($has_animation) {
                        $this->add_render_attribute('gallery_item_' . $unique_index, ['class' => 'elementor-animated-content']);
                    }

                    if ($is_multiple) {
                        $this->add_render_attribute('gallery_item_' . $unique_index, ['data-e-gallery-tags' => implode(',', $tags)]);
                    }

                    if ('a' === $gallery_item_tag) {
                        // if ('file' === $settings['link_to']) {
                        //     $href = $image_data['media'];
            
                        //     $this->add_render_attribute('gallery_item_' . $unique_index, [
                        //        'href' => $href,
                        //     ]);

                        //    $this->add_lightbox_data_attributes('gallery_item_' . $unique_index, $id, 'yes', 'all-' . $this->get_id());
                        // } elseif ('custom' === $settings['link_to']) {
                        //     $this->add_link_attributes('gallery_item_' . $unique_index, $settings['url']);
                        // }
                    }

                    $this->add_render_attribute(
                        'gallery_item_image_' . $unique_index,
                        [
                            'class' => [
                                'e-gallery-image',
                                'elementor-gallery-item__image',
                            ],
                            'data-thumbnail' => $image_data['src'],
                            'data-width' => $image_data['width'],
                            'data-height' => $image_data['height'],
                            //'alt' => $image_data['alt'],
                        ]
                    ); ?>
                    <<?php echo $gallery_item_tag;?> <?php $this->print_render_attribute_string('gallery_item_' . $unique_index); ?> >
                        <div <?php $this->print_render_attribute_string('gallery_item_image_' . $unique_index); ?>></div>
                        <?php if (!empty($settings['overlay_background'])) : ?>
                            <div <?php $this->print_render_attribute_string('gallery_item_background_overlay'); ?>></div>
                        <?php endif; ?>
                        <?php if ($has_title || $has_description) : ?>
                            <div <?php $this->print_render_attribute_string('gallery_item_content'); ?>>
                                <?php if ($has_title) :
                                    $title = $image_data[$settings['overlay_title']];
                                    if (!empty($title)) : ?>
                                        <div <?php $this->print_render_attribute_string('gallery_item_title'); ?>>
                                            <?php // PHPCS - the main text of a widget should not be escaped. 
                                            ?>
                                            <?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                            ?>
                                        </div>
                                    <?php endif;
                                endif;
                                if ($has_description) :
                                    $description = $image_data[$settings['overlay_description']];
                                    if (!empty($description)) : ?>
                                        <div <?php $this->print_render_attribute_string('gallery_item_description'); ?>>
                                            <?php // PHPCS - the main text of a widget should not be escaped. 
                                            ?>
                                            <?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                            ?>
                                        </div>
                                <?php endif;
                                endif; ?>
                            </div>
                        <?php endif; ?>
                    </<?php echo $gallery_item_tag;?>>
                <?php endforeach;
                //endforeach; 
                ?>
            </div>
<?php }
    }
}
