<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_ProductAttachments extends Widget_Base {


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
	public function get_name() {
		return 'product_attachments';
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
	public function get_title() {
		return PrestaHelper::__( 'Product Attachments', 'elementor' );
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
	public function get_icon() {
		return 'ceicon-product-attachment-widget';
	}

	public function get_categories() {
		return array( 'products_layout' );
	}

	/**
	 * Register accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);

        $this->add_control(
			'orientation',
			[
				'label' => PrestaHelper::__( 'Orientation', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'inline' => 'Inline',
					'stacked' => 'Stacked',
				),
				'default' => 'stacked'
			]
		);

        $this->add_control(
			'heading',
			array(
				'label'       => PrestaHelper::__( 'Heading', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);
        
        $this->add_control(
			'button_text',
			array(
				'label'       => PrestaHelper::__( 'Download Button Text', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
                'default' => 'Download'
			)
		);

        $this->add_control(
			'icon',
			array(
				'label'       => PrestaHelper::__( 'Icon', 'elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
                'separator' => "after"
			)
		);

        $this->add_control(
            'show_file_size',
            array(
                'label'   => PrestaHelper::__( 'Show File Size', 'elementor' ),
                'type'    => Controls_Manager::SWITCHER,
                'dynamic' => array(
                    'active' => true,
                ),
                'default' => 'yes',
            )
        );	

        $this->add_control(
            'show_description',
            array(
                'label'   => PrestaHelper::__( 'Show Description', 'elementor' ),
                'type'    => Controls_Manager::SWITCHER,
                'dynamic' => array(
                    'active' => true,
                ),
                'default' => 'yes',
            )
        );	


        $this->end_controls_section();

        $this->start_controls_section(
			'style_section',
			array(
				'label'      => PrestaHelper::__( 'General Style', 'elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading',
				'label'    => PrestaHelper::__( 'Heading Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-heading',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'heading_shadow',
				'label'    => PrestaHelper::__( 'Heading Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-heading',
			]
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => PrestaHelper::__( 'Heading Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-heading' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

        $this->add_control(
			'section_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments' => 'background: {{VALUE}};',
				),
			)
		);

        $this->add_responsive_control(
			'section_padding',
			array(
				'label'      => PrestaHelper::__( 'Section Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments',
			)
		);

		$this->add_responsive_control(
			'section_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_shadow',
				'selector' => '{{WRAPPER}} .crazy-product-attachments',
			)
		);

        $this->add_responsive_control(
			'section_align',
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
					'{{WRAPPER}} .crazy-product-attachments' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items' => 'justify-content: {{VALUE}}',
				],
				'frontend_available' => true,
                'separator' => 'before'
			]
		);

        $this->add_responsive_control(
			'gap_between',
			[
				'label' => PrestaHelper::__( 'Gap Between', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
					'vh' => [
						'min' => 0,
						'max' => 200,
					],
					'vw' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '10',
				],
				'selectors' => [
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items' => 'gap: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after'
			]
		);

        $this->end_controls_section();

        
        $this->start_controls_section(
			'item_style',
			array(
				'label'      => PrestaHelper::__( 'Item Style', 'elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE
			)
		);


        $this->add_responsive_control(
			'item_padding',
			array(
				'label'      => PrestaHelper::__( 'Section Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

        $this->start_controls_tabs( 'product_attachment_style' );

		$this->start_controls_tab(
			'name_style_tab',
			array(
				'label' => PrestaHelper::__( 'Name', 'elementor' ),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-name',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_shadow',
				'label'    => PrestaHelper::__( 'Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-name',
			]
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-name a.attachment-link' => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'name_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Name Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-name a.attachment-link:hover' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
			'desc_style_tab',
			array(
				'label' => PrestaHelper::__( 'Description', 'elementor' ),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item p',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'desc_shadow',
				'label'    => PrestaHelper::__( 'Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item p',
			]
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
			'link_style_tab',
			array(
				'label' => PrestaHelper::__( 'Button', 'elementor' ),
			)
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button' => 'color: {{VALUE}};',
				),
                'separator' => 'before'
			)
		);

        $this->add_control(
			'link_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'link_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button' => 'background: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'link_bg_hover',
			array(
				'label'     => PrestaHelper::__( 'Background Hover', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button:hover' => 'background: {{VALUE}};',
				),
                'separator' => 'after'
			)
		);

        $this->add_responsive_control(
			'link_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'link_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button',
			)
		);

		$this->add_responsive_control(
			'link_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-attachments .crazy-product-attachment-items .crazy-product-attachment-item .attachment-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
	}

	/**
	 * Render accordion widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

        $settings     = $this->get_settings_for_display();
		$heading   = $settings['heading'];
		$orientation   = $settings['orientation'];
		$button_text   = $settings['button_text'];
		$show_description   = $settings['show_description'];
		$show_file_size   = $settings['show_file_size'];
		$icon   = $settings['icon'];

        if(isset($icon['value'])){
            $icon = $icon['value'];
        }else{
            $icon = '';
        }

        $out_put = '';
        $context = \Context::getContext();
        $id_lang = $context->language->id;

        $controller_name = \Tools::getValue('controller');
        if($controller_name == "product"){
            \Context::getcontext()->smarty->assign(
                array(
                    'from_editor'      => 'no',
                    'heading'      => $heading,
                    'button_text'      => $button_text,
                    'show_file_size'      => $show_file_size,
                    'show_description'      => $show_description,
                    'orientation'       => $orientation,
                    'icon'       => $icon,
                    'elementprefix'    => 'product-attachment',
                )
            );
            $out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy-single-product-attachments.tpl');
            echo $out_put;
		} else {
            if (isset($_GET['prdid'])) {
                $id_product = (int)$_GET['prdid'];
            }
            $attachments = \Attachment::getAttachments($id_lang, $id_product);
            foreach($attachments as $key => $attachment){
                $attachments[$key]['file_size_formatted'] = \Tools::formatBytes($attachment['file_size'], 2);
            }
            $product['attachments'] = $attachments;
            \Context::getcontext()->smarty->assign(
                array(
                    'from_editor'      => 'no',
                    'heading'      => $heading,
                    'button_text'      => $button_text,
                    'show_file_size'      => $show_file_size,
                    'show_description'      => $show_description,
                    'orientation'       => $orientation,
                    'elementprefix'    => 'product-attachment',
                    'icon'       => $icon,
                    'product'    => $product
                )
            );
            $out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy-single-product-attachments.tpl');
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
	protected function _content_template() {
	}
}