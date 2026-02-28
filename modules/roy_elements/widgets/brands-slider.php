<?php

use CrazyElements\Modules\DynamicTags\Module as TagsModule;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;
use CrazyElements\Controls_Manager;
use CrazyElements\Core\Schemes;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}

class Roy_Brands_Slider extends Widget_Base 
{

	/**
	 * Get widget name.
	 *
	 * Retrieve accordion widget name.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'roy_brands_slider';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve accordion widget title.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return PrestaHelper::__('Roy Brands', 'elementor');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve accordion widget icon.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'ceicon-slider-push';
	}

	public function get_categories()
	{
		return array('modez');
	}

	/**
	 * Register accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function _register_controls() 
    {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => PrestaHelper::__( 'Title', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_align',
			array(
				'label'   => PrestaHelper::__('Title Alignment', 'elementor'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'left' => PrestaHelper::__('Left', 'elementor'),
					'center'  => PrestaHelper::__('Center', 'elementor'),
				),
				'default' => 'left',
			)
		);

        $this->add_control(
            'per_row',
            array(
                'label'   => PrestaHelper::__('Brands Per Row', 'modez'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            )
        );

        $this->add_control(
			'layout',
			[
				'label' => PrestaHelper::__( 'Layout', 'modez' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slider',
				'options' => [
					'grid' => PrestaHelper::__( 'Grid', 'modez' ),
					'slider' => PrestaHelper::__( 'Slider', 'modez' ),
				],
			]
		);
        
		$this->add_control(
			'is_autoplay',
			[
				'label'        => PrestaHelper::__( 'Autoplay', 'plugin-domain' ),
				'type'         => Controls_Manager::SWITCHER,
				'true'          => PrestaHelper::__( 'Yes', 'your-plugin' ),
				'false'           => PrestaHelper::__( 'No', 'your-plugin' ),
				'default'      => 'false',
				'condition'    => [
					'layout' => 'slider',
				],
			]
		);

        
		$this->end_controls_section();
	}
	/**
	 * Render accordion widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function render() {

		if (PrestaHelper::is_admin()) {
			return;
		}

		$settings      = $this->get_settings_for_display();
		$title         = $settings['title'];
		$title_align      = $settings['title_align'];
		$per_row    = $settings['per_row'];
        $layout     = $settings['layout'];
        $is_autoplay = $settings['is_autoplay'];
        
		$context       = \Context::getContext();
		$manufacturers = \Manufacturer::getManufacturers( false, $context->language->id, true );

		$context->smarty->assign(
			array(
				'title'             => $title,
				'title_align'         => $title_align,
				'manufacturers'     => $manufacturers,
				'per_row'           => $per_row,
                'layout'            => $layout,
				'is_autoplay'       => $is_autoplay,
				'img_manu_dir'      => _PS_IMG_ . 'm/',
				'type'              => 'suppliers',
			)
		);

		$output = $context->smarty->fetch(
			ROYELEMENTS_PATH . '/views/templates/front/blockroybrands.tpl'
		);

		echo $output;
	}

	/**
	 * Render accordion widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function _content_template()
	{
	}
}

CrazyElements\Plugin::instance()->widgets_manager->register_widget_type(new \Roy_Brands_Slider());
