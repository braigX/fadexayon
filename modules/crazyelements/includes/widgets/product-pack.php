<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_ProductPack extends Widget_Base
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
        return 'product_pack';
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
        return PrestaHelper::__('Product Pack', 'elementor');
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
        return 'ceicon-product-pack-widget';
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
        $this->start_controls_section(
			'general',
			array(
				'label' => PrestaHelper::__( 'General', 'elecounter' ),
			)
		);

        $this->add_control(
			'heading',
			array(
				'label'       => PrestaHelper::__( 'Heading', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				)
			)
		);

        $this->add_control(
			'q_sign',
			array(
				'label'       => PrestaHelper::__( 'Quantity Sign', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
                ),
                'default' => 'x '
			)
		);
        
        $this->end_controls_section();

        $this->start_controls_section(
			'section_style',
			array(
				'label'      => PrestaHelper::__('General Style', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typo',
				'label'    => PrestaHelper::__( 'Heading Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack .h4',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'heading_shadow',
				'label'    => PrestaHelper::__( 'Heading Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack .h4',
			]
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => PrestaHelper::__( 'Heading Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack .h4' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'item_style',
			array(
				'label'      => PrestaHelper::__('Item Style', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

        $this->add_control(
			'item_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack article .card' => 'background: {{VALUE}};',
				),
			)
		);

        

        $this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card',
			)
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_bordershadow',
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card',
			]
		);

        $this->start_controls_tabs( 'product_pack_item_style' );

		$this->start_controls_tab(
			'pack_name_style',
			array(
				'label' => PrestaHelper::__( 'Name', 'elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card .pack-product-name',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack article .card .pack-product-name a' => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'name_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack article .card .pack-product-name a:hover' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
			'pack_price_style',
			array(
				'label' => PrestaHelper::__( 'Price', 'elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pack_price_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card .pack-product-price',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_control(
			'pack_price_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack article .card .pack-product-price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
			'c_style',
			array(
				'label' => PrestaHelper::__( 'Quantity', 'elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pack_quantity_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card .pack-product-quantity',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_control(
			'pack_quantity_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-pack article .card .pack-product-quantity' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pack_quantity_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-pack article .card .pack-product-quantity',
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
    protected function render()
    {

        $settings       = $this->get_settings_for_display();
		$heading     = $settings['heading'];
		$q_sign     = $settings['q_sign'];

        $context = \Context::getContext();
        $id_lang = $context->language->id;

        $controller_name = \Tools::getValue('controller');
        if ($controller_name == "product") {
            $out_put = '';
            $context->smarty->assign(
                array(
                    'heading' => $heading,
                    'q_sign' => $q_sign,
                )
            );
            $out_put .= $context->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_pack.tpl');
            echo $out_put;
        } else {

            if (isset($_GET['prdid'])) {
                $id_product = (int)$_GET['prdid'];
            }
            
            $product = new \Product( $id_product, true, $id_lang );

            $pack_items = \Pack::isPack($id_product) ? \Pack::getItemTable($id_product, $context->language->id, true) : [];

			if(isset($pack_items) && !empty($pack_items)){
				$assembler = new \ProductAssembler( \Context::getcontext() );

				$presenterFactory     = new \ProductPresenterFactory( \Context::getcontext() );
				$presentationSettings = $presenterFactory->getPresentationSettings();
				$presenter            = new \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
					new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
						$context->link
					),
					$context->link,
					new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
					new \PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
					$context->getTranslator()
				);
	
				foreach ( $pack_items as $item ) {
					$presentedPackItems[] = $presenter->present(
						$presentationSettings,
						$assembler->assembleProduct( $item ),
						$context->language
					);
				}
	
				$product_arr['show_price'] = $product->show_price;
	
				$context->smarty->assign(
					array(
						'packItems' => $presentedPackItems,
						'product' => $product_arr,
						'heading' => $heading,
						'q_sign' => $q_sign,
					)
				);
				$out_put = '';
				$out_put .= $context->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_pack.tpl');
				echo $out_put;
			}else{
				echo 'No Pack Items Available for This Product';	
			}            
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