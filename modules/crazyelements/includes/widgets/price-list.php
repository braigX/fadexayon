<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}
class Widget_PriceList extends Widget_Base
{

    public function get_name()
    {
        return 'price-list';
    }

    public function get_title()
    {
        return PrestaHelper::__('Price List', 'elementor');
    }

    public function get_icon()
    {
        return 'ceicon-price-list';
    }

    public function get_keywords()
    {
        return ['pricing', 'list', 'product', 'image', 'menu'];
    }

    public function get_categories() {
		return array( 'crazy_addons' );
	}

    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_list',
            [
                'label' => PrestaHelper::__('List', 'elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'price',
            [
                'label' => PrestaHelper::__('Price', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => PrestaHelper::__('Title', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => 'true',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'item_description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => PrestaHelper::__('Image', 'elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => PrestaHelper::__('Link', 'elementor'),
                'type' => Controls_Manager::URL,
                'default' => ['url' => '#'],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'price_list',
            [
                'label' => PrestaHelper::__('List Items', 'elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => PrestaHelper::__('First item on the list', 'elementor'),
                        'item_description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'price' => '$20',
                        'link' => ['url' => '#'],
                    ],
                    [
                        'title' => PrestaHelper::__('Second item on the list', 'elementor'),
                        'item_description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'price' => '$9',
                        'link' => ['url' => '#'],
                    ],
                    [
                        'title' => PrestaHelper::__('Third item on the list', 'elementor'),
                        'item_description' => PrestaHelper::__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'elementor'),
                        'price' => '$32',
                        'link' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_list_style',
            [
                'label' => PrestaHelper::__('List', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading__title',
            [
                'label' => PrestaHelper::__('Title', 'elementor'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-header' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
                'selector' => '{{WRAPPER}} .elementor-price-list-header',
            ]
        );

        $this->add_control(
            'price_title',
            [
                'label' => PrestaHelper::__('Price', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
                'selector' => '{{WRAPPER}} .elementor-price-list-price',
            ]
        );

        $this->add_control(
            'heading_item_description',
            [
                'label' => PrestaHelper::__('Description', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
                'selector' => '{{WRAPPER}} .elementor-price-list-description',
            ]
        );

        $this->add_control(
            'heading_separator',
            [
                'label' => PrestaHelper::__('Separator', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'separator_style',
            [
                'label' => PrestaHelper::__('Style', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'solid' => PrestaHelper::__('Solid', 'elementor'),
                    'dotted' => PrestaHelper::__('Dotted', 'elementor'),
                    'dashed' => PrestaHelper::__('Dashed', 'elementor'),
                    'double' => PrestaHelper::__('Double', 'elementor'),
                    'none' => PrestaHelper::__('None', 'elementor'),
                ],
                'default' => 'dotted',
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-style: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'separator_weight',
            [
                'label' => PrestaHelper::__('Weight', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 10,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'default' => [
                    'size' => 2,
                ],
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label' => PrestaHelper::__('Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-color: {{VALUE}};',
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'separator_spacing',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 40,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_image_style',
            [
                'label' => PrestaHelper::__('Image', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => PrestaHelper::__('Border Radius', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_spacing',
            [
                'label' => PrestaHelper::__('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body.rtl {{WRAPPER}} .elementor-price-list-image' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
                    'body.rtl {{WRAPPER}} .elementor-price-list-image + .elementor-price-list-text' => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
                    'body:not(.rtl) {{WRAPPER}} .elementor-price-list-image' => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
                    'body:not(.rtl) {{WRAPPER}} .elementor-price-list-image + .elementor-price-list-text' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
                ],
                'default' => [
                    'size' => 20,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_item_style',
            [
                'label' => PrestaHelper::__('Item', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'row_gap',
            [
                'label' => PrestaHelper::__('Rows Gap', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'size' => 20,
                ],
            ]
        );

        $this->add_control(
            'vertical_align',
            [
                'label' => PrestaHelper::__('Vertical Align', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => PrestaHelper::__('Top', 'elementor'),
                        'icon' => 'ceicon-v-align-top',
                    ],
                    'center' => [
                        'title' => PrestaHelper::__('Center', 'elementor'),
                        'icon' => 'ceicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => PrestaHelper::__('Bottom', 'elementor'),
                        'icon' => 'ceicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-list-item' => 'align-items: {{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'bottom' => 'flex-end',
                ],
                'default' => 'top',
            ]
        );

        $this->end_controls_section();
    }

    private function render_image($item, $instance)
    {
        $image_src = $item['image']['url'];
        return sprintf('<img src="%s" alt="%s" />', $image_src, $item['title']);
    }

    private function render_item_header($item)
    {
        $url = $item['link']['url'];

        $item_id = $item['_id'];

        if ($url) {
            $unique_link_id = 'item-link-' . $item_id;

            $this->add_render_attribute($unique_link_id, 'class', 'elementor-price-list-item');

            $this->add_link_attributes($unique_link_id, $item['link']);

            return '<li><a ' . $this->get_render_attribute_string($unique_link_id) . '>';
        } else {
            return '<li class="elementor-price-list-item">';
        }
    }

    private function render_item_footer($item)
    {
        if ($item['link']['url']) {
            return '</a></li>';
        } else {
            return '</li>';
        }
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $price_list = $settings['price_list'];
       
?>
        <ul class="elementor-price-list">
            <?php
            if (isset($price_list)) {
                foreach ($settings['price_list'] as $index => $item) :
                    $title            = $item['title'];
                    $price            = $item['price'];
                    $item_description = $item['item_description'];        
            ?>
                    <?php if (!empty($item['title']) || !empty($item['price']) || !empty($item['item_description'])) :
                   
                        $title_repeater_setting_key = $this->get_repeater_setting_key('title', 'price_list', $index);
                        $description_repeater_setting_key = $this->get_repeater_setting_key('item_description', 'price_list', $index);
                        $this->add_inline_editing_attributes($title_repeater_setting_key);
                        $this->add_inline_editing_attributes($description_repeater_setting_key);
                        $this->add_render_attribute($title_repeater_setting_key, 'class', 'elementor-price-list-title');
                        $this->add_render_attribute($description_repeater_setting_key, 'class', 'elementor-price-list-description');
                    ?>
                        <?php
                        // PHPCS - the method render_item_header is safe.
                        echo $this->render_item_header($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                        <?php if (!empty($item['image']['url'])) : ?>
                            <div class="elementor-price-list-image">
                                <?php
                                // PHPCS - the method render_image is safe.
                                echo $this->render_image($item, $settings); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="elementor-price-list-text">
                            <?php if (!empty($item['title']) || !empty($item['price'])) : ?>
                                <div class="elementor-price-list-header">
                                    <?php if (!empty($item['title'])) : ?>
                                        <span <?php $this->print_render_attribute_string($title_repeater_setting_key); ?>>

                                            <?php echo $title; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ('none' != $settings['separator_style']) : ?>
                                        <span class="elementor-price-list-separator"></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['price'])) : ?>
                                        <span class="elementor-price-list-price"><?php echo $price;?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['item_description'])) : ?>
                                <p <?php $this->print_render_attribute_string($description_repeater_setting_key); ?>>
                                    <?php echo $item_description;?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php
                        // PHPCS - the method render_item_footer is safe.
                        echo $this->render_item_footer($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    <?php endif; ?>
            <?php endforeach;
            }
            ?>

        </ul>

    <?php
    }

    /**
     * Render Price List widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     * @access protected
     */
    protected function content_template()
    {
    ?>
        <ul class="elementor-price-list">
            <# for ( var i in settings.price_list ) { var item=settings.price_list[i], item_open_wrap='<li class="elementor-price-list-item">' , item_close_wrap='</li>' ; if ( item.link.url ) { item_open_wrap='<li><a href="' + item.link.url + '" class="elementor-price-list-item">' ; item_close_wrap='</a></li>' ; } if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) || ! _.isEmpty( item.description ) || ! _.isEmpty( item.image ) ) { #>

                {{{ item_open_wrap }}}
                <# if ( item.image && item.image.id ) { var image={ id: item.image.id, url: item.image.url, model: view.getEditModel() }; var image_url=elementor.imagesManager.getImageUrl( image ); if ( image_url ) { #>
                    <div class="elementor-price-list-image"><img src="{{ image_url }}" alt="{{ item.title }}"></div>
                    <# } #>

                        <# } #>


                            <# if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) || ! _.isEmpty( item.item_description ) ) { #>
                                <div class="elementor-price-list-text">

                                    <# if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) ) { #>
                                        <div class="elementor-price-list-header">

                                            <# if ( ! _.isEmpty( item.title ) ) { #>
                                                <span class="elementor-price-list-title">{{{ item.title }}}</span>
                                                <# } #>

                                                    <# if ( 'none' !=settings.separator_style ) { #>
                                                        <span class="elementor-price-list-separator"></span>
                                                        <# } #>

                                                            <# if ( ! _.isEmpty( item.price ) ) { #>
                                                                <span class="elementor-price-list-price">{{{ item.price }}}</span>
                                                                <# } #>

                                        </div>
                                        <# } #>

                                            <# if ( ! _.isEmpty( item.item_description ) ) { #>
                                                <p class="elementor-price-list-description">{{{ item.item_description }}}</p>
                                                <# } #>

                                </div>
                                <# } #>

                                    {{{ item_close_wrap }}}

                                    <# } #>
                                        <# } #>
        </ul>
<?php
    }

    public function get_group_name()
    {
        return 'pricing';
    }
}
