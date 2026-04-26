<?php

namespace CrazyElements;
// require_once CRAZY_PATH . 'includes/classes/facebook-sdk-manager.php';

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_Facebook_Button extends Widget_Base
{

    public function get_name()
    {
        return 'facebook-button';
    }

    public function get_title()
    {
        return PrestaHelper::__('Facebook Button', 'elementor');
    }

    public function get_icon()
    {
        return 'ceicon-facebook-like-box';
    }

    public function get_keywords()
    {
        return ['facebook', 'social', 'embed', 'button', 'like', 'share', 'recommend', 'follow'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => PrestaHelper::__('Button', 'elementor'),
            ]
        );

        Facebook_SDK_Manager::add_app_id_control($this);

        $this->add_control(
            'type',
            [
                'label' => PrestaHelper::__('Type', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'like',
                'options' => [
                    'like' => PrestaHelper::__('Like', 'elementor'),
                    'recommend' => PrestaHelper::__('Recommend', 'elementor'),
                ],
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => PrestaHelper::__('Layout', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'standard',
                'options' => [
                    'standard' => PrestaHelper::__('Standard', 'elementor'),
                    'button' => PrestaHelper::__('Button', 'elementor'),
                    'button_count' => PrestaHelper::__('Button Count', 'elementor'),
                    'box_count' => PrestaHelper::__('Box Count', 'elementor'),
                ],
            ]
        );

        $this->add_control(
            'size',
            [
                'label' => PrestaHelper::__('Size', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'small',
                'options' => [
                    'small' => PrestaHelper::__('Small', 'elementor'),
                    'large' => PrestaHelper::__('Large', 'elementor'),
                ],
            ]
        );

        $this->add_control(
            'color_scheme',
            [
                'label' => PrestaHelper::__('Color Scheme', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'light',
                'options' => [
                    'light' => PrestaHelper::__('Light', 'elementor'),
                    'dark' => PrestaHelper::__('Dark', 'elementor'),
                ],
            ]
        );

        $this->add_control(
            'show_share',
            [
                'label' => PrestaHelper::__('Share Button', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'type!' => 'follow',
                ],
            ]
        );

        $this->add_control(
            'show_faces',
            [
                'label' => PrestaHelper::__('Faces', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'url_type',
            [
                'label' => PrestaHelper::__('Target URL', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                   'current_page' => PrestaHelper::__('Current Page', 'elementor'),
                   'custom' => PrestaHelper::__('Custom', 'elementor'),
                ],
                'default' => 'current_page',
                'separator' => 'before',
                'condition' => [
                    'type' => ['like', 'recommend'],
                ],
            ]
        );

        $this->add_control(
            'url_format',
            [
                'label' => PrestaHelper::__('URL Format', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'plain' => PrestaHelper::__('Plain Permalink', 'elementor'),
                    'pretty' => PrestaHelper::__('Pretty Permalink', 'elementor'),
                ],
                'default' => 'plain',
                'condition' => [
                     'url_type' => 'current_page',
                ],
            ]
        );

        $this->add_control(
            'url',
            [
                'label' => PrestaHelper::__('Link', 'elementor'),
                'placeholder' => PrestaHelper::__('https://your-link.com', 'elementor'),
                'label_block' => true,
                'condition' => [
                    'type' => ['like', 'recommend'],
                    'url_type' => 'custom',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render()
    {
        $settings = $this->get_settings();

        // Validate URL
        switch ($settings['type']) {
            case 'like':
            case 'recommend':
                if ('custom' === $settings['url_type'] && !filter_var($settings['url'], FILTER_VALIDATE_URL)) {
                    echo $this->get_title() . ': ' . PrestaHelper::__('Please enter a valid URL', 'elementor');
                    return;
                }
                break;
        }

        $attributes = [
            'data-layout' => $settings['layout'],
            'data-colorscheme' => $settings['color_scheme'],
            'data-size' => $settings['size'],
            'data-show-faces' => $settings['show_faces'] ? 'true' : 'false',
        ];

        switch ($settings['type']) {
            case 'like':
            case 'recommend':
                if ('current_page' === $settings['url_type']) {
                    $permalink = Facebook_SDK_Manager::get_permalink($settings);
                } else {
                    $permalink = PrestaHelper::esc_url($settings['url']);
                }

                $attributes['class'] = 'elementor-facebook-widget fb-like';
                $attributes['data-href'] = $permalink;
                $attributes['data-share'] = $settings['show_share'] ? 'true' : 'false';
                $attributes['data-action'] = $settings['type'];
                break;
        }

        $this->add_render_attribute('embed_div', $attributes);
?>
        <div <?php $this->print_render_attribute_string('embed_div'); ?>></div>
<?php
    }

    public function render_plain_content()
    {
    }

    public function get_group_name()
    {
        return 'social';
    }
}
