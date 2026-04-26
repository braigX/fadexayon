<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_ProductSingleTitle extends Widget_Base
{


    /**
     * Get widget name.
     *
     * Retrieve accordion widget name.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'product_single_title';
    }

    /**
     * Get widget title.
     *
     * Retrieve accordion widget title.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return PrestaHelper::__('Product Title', 'elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve accordion widget icon.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'ceicon-product-title-widget';
    }

    public function get_categories()
    {
        return array('products_layout');
    }

    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        // Style Tab

        $this->start_controls_section(
            'general',
            array(
                'label' => PrestaHelper::__('Content', 'elecounter'),
            )
        );

        $this->add_control(
            'selected_ids',
            array(
                'label'     => PrestaHelper::__('Select Product', 'elementor'),
                'type'      => Controls_Manager::AUTOCOMPLETE,
                'item_type' => 'product',
                'multiple'  => false,
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style',
            array(
                'label' => PrestaHelper::__('Style', 'elecounter'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'crazy_single_product_heading_typography',
                'label'    => PrestaHelper::__('Title Typography', 'elecounter'),
                'selector' => '{{WRAPPER}} .crazy_single_product_heading',
            )
        );

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'crazy_single_product_heading_shadow',
				'label'    => PrestaHelper::__( 'Title Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy_single_product_heading',
			]
		);

		$this->add_control(
            'crazy_single_product_heading_color',
            array(
                'label'     => PrestaHelper::__('Title Color', 'elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .crazy_single_product_heading' => 'color: {{VALUE}}',
                ),
            )
        );

		$this->add_responsive_control(
			'crazy_single_product_heading_align',
			[
				'label' => PrestaHelper::__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
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
					'{{WRAPPER}} .crazy_single_product_heading' => 'text-align: {{VALUE}}',
				],
				'frontend_available' => true,
			]
		);
        $this->end_controls_section();
        // Style Tab
    }

    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings     = $this->get_settings_for_display();
        $controller_name = \Tools::getValue('controller');
        if ($controller_name == "product") {
            $out_put = '';
            $id_product = (int)\Tools::getValue('id_product');
            \Context::getcontext()->smarty->assign(
                array(
                    'show_flag' => "yes",
                    'jqZoomEnabled' => \Configuration::get('PS_DISPLAY_JQZOOM')
                )
            );
            $out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_title.tpl');
            echo $out_put;
        } else {
            $ids          = $settings['selected_ids'];
            $str             = $this->render_autocomplete_result($ids);
            if ($str == '' && isset($_GET['prdid'])) {
				$str = (int)$_GET['prdid'];
			}
            $title  = \Product::getProductName($str);
            $out_put = "<h1 class=\"crazy_single_product_heading h1\" itemprop=\"name\">" . $title . "</h1>";
            echo $out_put;
        }
    }

    /**
     * Render accordion widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _content_template()
    {
    }
}
