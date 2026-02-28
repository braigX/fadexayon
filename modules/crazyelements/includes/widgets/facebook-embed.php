<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
require_once CRAZY_PATH . 'includes/classes/facebook-sdk-manager.php';

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_Facebook_Embed extends Widget_Base {

	public function get_name() {
		return 'facebook-embed';
	}

	public function get_title() {
		return PrestaHelper::__( 'Facebook Embed', 'elementor' );
	}

	public function get_icon() {
		return 'ceicon-fb-embed';
	}

	public function get_categories() {
		return [ 'pro-elements' ];
	}

	public function get_keywords() {
		return [ 'facebook', 'social', 'embed', 'video', 'post', 'comment' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => PrestaHelper::__( 'Embed', 'elementor' ),
			]
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'type',
			[
				'label' => PrestaHelper::__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post' => PrestaHelper::__( 'Post', 'elementor' ),
					'video' => PrestaHelper::__( 'Video', 'elementor' ),
					'comment' => PrestaHelper::__( 'Comment', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'post_url',
			[
				'label' => PrestaHelper::__( 'URL', 'elementor' ),
				'default' => 'https://www.facebook.com/elemntor/posts/2624214124556197',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'post',
				],
				'description' => PrestaHelper::__( 'Hover over the date next to the post, and copy its link address.', 'elementor' ),
			]
		);

		$this->add_control(
			'video_url',
			[
				'label' => PrestaHelper::__( 'URL', 'elementor' ),
				'default' => 'https://www.facebook.com/elemntor/videos/1683988961912056/',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'video',
				],
				'description' => PrestaHelper::__( 'Hover over the date next to the video, and copy its link address.', 'elementor' ),
			]
		);

		$this->add_control(
			'comment_url',
			[
				'label' => PrestaHelper::__( 'URL', 'elementor' ),
				'default' => 'https://www.facebook.com/elemntor/videos/1811703749140576/?comment_id=1812873919023559',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'comment',
				],
				'description' => PrestaHelper::__( 'Hover over the date next to the comment, and copy its link address.', 'elementor' ),
			]
		);

		$this->add_control(
			'include_parent',
			[
				'label' => PrestaHelper::__( 'Parent Comment', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => PrestaHelper::__( 'Set to include parent comment (if URL is a reply).', 'elementor' ),
				'condition' => [
					'type' => 'comment',
				],
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => PrestaHelper::__( 'Full Post', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => PrestaHelper::__( 'Show the full text of the post', 'elementor' ),
				'condition' => [
					'type' => [ 'post', 'video' ],
				],
			]
		);

		$this->add_control(
			'video_allowfullscreen',
			[
				'label' => PrestaHelper::__( 'Allow Full Screen', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type' => 'video',
				],
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label' => PrestaHelper::__( 'Autoplay', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type' => 'video',
				],
			]
		);

		$this->add_control(
			'video_show_captions',
			[
				'label' => PrestaHelper::__( 'Captions', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => PrestaHelper::__( 'Show captions if available (only on desktop).', 'elementor' ),
				'condition' => [
					'type' => 'video',
				],
			]
		);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['type'] ) ) {
			PrestaHelper::esc_html_e( 'Please set the embed type', 'elementor' );

			return;
		}

		if ( 'comment' === $settings['type'] && empty( $settings['comment_url'] ) || 'post' === $settings['type'] && empty( $settings['post_url'] ) || 'video' === $settings['type'] && empty( $settings['video_url'] ) ) {
			PrestaHelper::esc_html_e( 'Please enter a valid URL', 'elementor' );

			return;
		}

		$attributes = [
			// The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
			'style' => 'min-height: 10px',
		];

		switch ( $settings['type'] ) {
			case 'comment':
				$attributes['class'] = 'elementor-facebook-widget fb-comment-embed';
				$attributes['data-href'] = PrestaHelper::esc_url( $settings['comment_url'] );
				$attributes['data-include-parent'] = 'yes' === $settings['include_parent'] ? 'true' : 'false';
				break;
			case 'post':
				$attributes['class'] = 'elementor-facebook-widget fb-post';
				$attributes['data-href'] = PrestaHelper::esc_url( $settings['post_url'] );
				$attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
				break;
			case 'video':
				$attributes['class'] = 'elementor-facebook-widget fb-video';
				$attributes['data-href'] = PrestaHelper::esc_url( $settings['video_url'] );
				$attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
				$attributes['data-allowfullscreen'] = 'yes' === $settings['video_allowfullscreen'] ? 'true' : 'false';
				$attributes['data-autoplay'] = 'yes' === $settings['video_autoplay'] ? 'true' : 'false';
				$attributes['data-show-captions'] = 'yes' === $settings['video_show_captions'] ? 'true' : 'false';
				break;
		}

		$this->add_render_attribute( 'embed_div', $attributes );

		echo '<div ' . $this->get_render_attribute_string( 'embed_div' ) . '></div>'; // XSS ok.
	}

	public function render_plain_content() {}

	public function get_group_name() {
		return 'social';
	}
}
