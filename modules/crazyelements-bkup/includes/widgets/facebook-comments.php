<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
require_once CRAZY_PATH . 'includes/classes/facebook-sdk-manager.php';
if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}
class Widget_Facebook_Comments extends Widget_Base {

	public function get_name() {
		return 'facebook-comments';
	}

	public function get_title() {
		return PrestaHelper::__( 'Facebook Comments', 'elementor' );
	}

	public function get_icon() {
		return 'ceicon-facebook-comments';
	}

	public function get_keywords() {
		return [ 'facebook', 'comments', 'embed' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => PrestaHelper::__( 'Comments Box', 'elementor' ),
			]
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'comments_number',
			[
				'label' => PrestaHelper::__( 'Comment Count', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'default' => '10',
				'description' => PrestaHelper::__( 'Minimum number of comments: 5', 'elementor' ),
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => PrestaHelper::__( 'Order By', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'social',
				'options' => [
					'social' => PrestaHelper::__( 'Social', 'elementor' ),
					'reverse_time' => PrestaHelper::__( 'Reverse Time', 'elementor' ),
					'time' => PrestaHelper::__( 'Time', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'url_type',
			[
				'label' => PrestaHelper::__( 'Target URL', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'current_page' => PrestaHelper::__( 'Current Page', 'elementor' ),
					'custom' => PrestaHelper::__( 'Custom', 'elementor' ),
				],
				'default' => 'current_page',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'url_format',
			[
				'label' => PrestaHelper::__( 'URL Format', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'plain' => PrestaHelper::__( 'Plain Permalink', 'elementor' ),
					'pretty' => PrestaHelper::__( 'Pretty Permalink', 'elementor' ),
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
				'label' => PrestaHelper::__( 'Link', 'elementor' ),
				'placeholder' => PrestaHelper::__( 'https://your-link.com', 'elementor' ),
				'label_block' => true,
				'condition' => [
					 'url_type' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings();

		if ( 'current_page' === $settings['url_type'] ) {
		$permalink = Facebook_SDK_Manager::get_permalink( $settings );
		} else {
			if ( ! filter_var( $settings['url'], FILTER_VALIDATE_URL ) ) {
				echo $this->get_title() . ': ' . PrestaHelper::__( 'Please enter a valid URL', 'elementor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				return;
			}

			$permalink = PrestaHelper::esc_url( $settings['url'] );
		}

		$attributes = [
			'class' => 'elementor-facebook-widget fb-comments',
			'data-href' => $permalink,
			'data-width' => '100%',
			'data-numposts' => $settings['comments_number'],
			'data-order-by' => $settings['order_by'],
			// The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
			'style' => 'min-height: 1px',
		];

		$this->add_render_attribute( 'embed_div', $attributes );
		?>
		<div <?php $this->print_render_attribute_string( 'embed_div' ); ?>></div>
		<?php
	}

	public function render_plain_content() {}

	public function get_group_name() {
		return 'social';
	}
}
