<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Widget_Site_Title extends Widget_Heading {


	public function get_name() {
		// `theme` prefix is to avoid conflicts with a dynamic-tag with same name.
		return 'theme-site-title';
	}

	public function get_title() {
		return PrestaHelper::__( 'Site Title', 'elementor-pro' );
	}

	public function get_icon() {
		return 'ceicon-site-title';
	}

	public function get_categories() {
		return [ 'theme-elements' ];
	}

	public function get_keywords() {
		return [ 'site', 'title', 'name' ];
	}

	protected function _register_controls() {
		parent::_register_controls();

        $this->update_control(
			'title',
			[
				'default' => \Configuration::get('PS_SHOP_NAME'),
			]
		);

		$this->update_control(
			'link',
			[
				'default' => [
                    'url' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                ],
			]
		);
	}

	protected function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' elementor-widget-' . parent::get_name();
	}

	private function get_caption_source_options() {
		$caption_source_options = $this->get_controls( 'caption_source' )['options'];

		unset( $caption_source_options['custom'] );

		return $caption_source_options;
	}
}
