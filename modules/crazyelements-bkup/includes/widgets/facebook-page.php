<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

require_once CRAZY_PATH . 'includes/classes/facebook-sdk-manager.php';

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}

class Widget_Facebook_Page extends Widget_Base
{

	public function get_name()
	{
		return 'facebook-page';
	}

	public function get_title()
	{
		return PrestaHelper::__('Facebook Page', 'elementor');
	}

	public function get_icon()
	{
		return 'ceicon-fb-feed';
	}

	public function get_keywords()
	{
		return ['facebook', 'social', 'embed', 'page'];
	}

	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => PrestaHelper::__('Page', 'elementor'),
			]
		);

		$this->add_control(
			'url',
			[
				'label' => PrestaHelper::__('Link', 'elementor'),
				'placeholder' => 'https://www.facebook.com/your-page/',
				'default' => 'https://www.facebook.com/elemntor/',
				'label_block' => true,
				'description' => PrestaHelper::__('Paste the URL of the Facebook page.', 'elementor'),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => PrestaHelper::__('Layout', 'elementor'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'default' => [
					'timeline',
				],
				'options' => [
					'timeline' => PrestaHelper::__('Timeline', 'elementor'),
					'events' => PrestaHelper::__('Events', 'elementor'),
					'messages' => PrestaHelper::__('Messages', 'elementor'),
				],
			]
		);

		$this->add_control(
			'small_header',
			[
				'label' => PrestaHelper::__('Small Header', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'show_cover',
			[
				'label' => PrestaHelper::__('Cover Photo', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_facepile',
			[
				'label' => PrestaHelper::__('Profile Photos', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_cta',
			[
				'label' => PrestaHelper::__('Custom CTA Button', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'height',
			[
				'label' => PrestaHelper::__('Height', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min' => 70,
						'max' => 1000,
					],
				],
				'size_units' => ['px'],
			]
		);

		$this->end_controls_section();
	}

	public function render()
	{
		$settings = $this->get_settings();

		if (empty($settings['url'])) {
			echo $this->get_title() . ': ' . PrestaHelper::__('Please enter a valid URL', 'elementor'); // XSS ok.

			return;
		}

		$height = $settings['height']['size'] . $settings['height']['unit'];

		$attributes = [
			'class' => 'elementor-facebook-widget fb-page',
			'data-href' => $settings['url'],
			'data-tabs' => implode(',', $settings['tabs']),
			'data-height' => $height,
			'data-width' => '500px', // Try the max possible width
			'data-small-header' => $settings['small_header'] ? 'true' : 'false',
			'data-hide-cover' => $settings['show_cover'] ? 'false' : 'true', // if `show` - don't hide.
			'data-show-facepile' => $settings['show_facepile'] ? 'true' : 'false',
			'data-hide-cta' => $settings['show_cta'] ? 'false' : 'true', // if `show` - don't hide.
			// The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget.
			'style' => 'min-height: 10px;height:' . $height,
		];
		$this->add_render_attribute('embed_div', $attributes);

		echo '<div ' . $this->get_render_attribute_string('embed_div') . '></div>'; // XSS ok.
	}

	public function render_plain_content()
	{
	}

	public function get_group_name()
	{
		return 'social';
	}
}
