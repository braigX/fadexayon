<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_ProductBrand extends Widget_Base {

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
		return 'product_brand';
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
		return PrestaHelper::__( 'Product Brand', 'elementor' );
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
		return 'ceicon-product-manufacturing-widget';
	}

	public function get_categories() {
		return array( 'products_layout' );
	}

	private function getPsImgSizesOption() {
		$db        = \Db::getInstance();
		$tablename = _DB_PREFIX_ . 'image_type';
		$sizes     = $db->executeS( "SELECT name FROM {$tablename} ORDER BY name ASC" );
		$options   = array( 'Default' => '' );
		if ( ! empty( $sizes ) ) {
			foreach ( $sizes as $size ) {
				$options[ $size['name'] ] = $size['name'];
			}
		}
		return $options;
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
			'show_name',
			array(
				'label'        => PrestaHelper::__( 'Show Name', 'elecounter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => PrestaHelper::__( 'Show', 'elecounter' ),
				'label_off'    => PrestaHelper::__( 'Hide', 'elecounter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'img_size',
			array(
				'label'   => PrestaHelper::__( 'Image Size', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->getPsImgSizesOption(),
			)
		);

		$this->add_responsive_control(
			'brand_image_align',
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
					'{{WRAPPER}} .crazy-product-brand' => 'text-align: {{VALUE}}',
				],
				'frontend_available' => true,
                'separator' => 'before'
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'product_brand_image',
			array(
				'label'      => PrestaHelper::__('Style', 'elementor'),
				'tab'        => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typo',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-brand .crazy-product-brand-name',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_shadow',
				'label'    => PrestaHelper::__( 'Shadow', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-product-brand .crazy-product-brand-name',
			]
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => PrestaHelper::__( 'Name Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-brand .crazy-product-brand-name' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);


		$this->add_control(
			'brand_image_bg',
			array(
				'label'     => PrestaHelper::__( 'Background', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-product-brand .manufacturer-logo' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'brand_image_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-product-brand .manufacturer-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'brand_image_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-product-brand .manufacturer-logo',
			)
		);

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
		$man_img_size  = $settings['img_size'];
		$show_name  = $settings['show_name'];

		$controller_name = \Tools::getValue('controller');

        $out_put = '';
        $context = \Context::getContext();
        $id_lang = $context->language->id;
		
		if($controller_name == "product"){
			$id_product = (int)\Tools::getValue('id_product');
			$product = $context->controller->getProduct();
		} else {
			if (isset($_GET['prdid'])) {
				$id_product = (int)$_GET['prdid'];
			}
			$product = new \Product( $id_product, true, $id_lang );	
		}	

		$manu = new \Manufacturer($product->id_manufacturer, $id_lang);

		if(isset($manu->id)){
			$image_link = $context->link->getManufacturerImageLink($manu->id, $man_img_size);
			$link = $context->link->getManufacturerLink($manu->id, null, $id_lang);

			?>
				<div class="product-manufacturer crazy-product-brand">
					<a class="crazy-product-brand-link" href="<?php echo $link; ?>">
						<img src="<?php echo $image_link; ?>" class="img img-thumbnail manufacturer-logo" alt="<?php echo $manu->name; ?>">
					</a>
					<?php 
					if($show_name == 'yes'){
						?>
						<p class="h5 crazy-product-brand-name"><?php echo $manu->name; ?></p>
						<?php 
					}
					?>
				</div>
			<?php
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