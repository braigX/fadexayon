<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}
/**
 * Integration with Facebook SDK
 */
class Facebook_SDK_Manager
{

	const OPTION_NAME_APP_ID = 'elementor_pro_facebook_app_id';

	public static function get_app_id()
	{
		return PrestaHelper::get_option(self::OPTION_NAME_APP_ID, '');
	}

	public static function get_lang()
	{
		return get_locale();
	}

	public static function enqueue_meta_app_id()
	{
		$app_id = self::get_app_id();
		if ($app_id) {
			printf('<meta property="fb:app_id" content="%s" />', PrestaHelper::esc_attr($app_id));
		}
	}

	/**
	 * @param Widget_Base $widget
	 */
	public static function add_app_id_control($widget)
	{
		if (!self::get_app_id()) {
			/* translators: 1: Setting Page link open tag, 2: Link closing tag. */
			$html = sprintf(
				PrestaHelper::__('Set your Facebook App ID in the %1$sIntegrations Settings%2$s', 'elementor'),
				sprintf('<a href="%s" target="_blank">', Settings::get_url() . '#tab-integrations'),
				'</a>'
			);
			$content_classes = 'elementor-panel-alert elementor-panel-alert-warning';
		} else {
			/* translators: 1: App ID, 2: Setting Page link open tag, 3: Link closing tag. */
			$html = sprintf(
				PrestaHelper::__('You are connected to Facebook App %1$s, %2$sChange App%3$s', 'elementor'),
				self::get_app_id(),
				sprintf('<a href="%s" target="_blank">', Settings::get_url() . '#tab-integrations'),
				'</a>'
			);
			$content_classes = 'elementor-panel-alert elementor-panel-alert-info';
		}

		$widget->add_control(
			'app_id',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => $html,
				'content_classes' => $content_classes,
			]
		);
	}

	public function localize_settings($settings)
	{
		$settings['facebook_sdk'] = [
			'lang' => self::get_lang(),
			'app_id' => self::get_app_id(),
		];

		return $settings;
	}

	public function __construct()
	{
		PrestaHelper::add_action('wp_head', [__CLASS__, 'enqueue_meta_app_id']);
		PrestaHelper::add_filter('elementor_pro/frontend/localize_settings', [$this, 'localize_settings']);

		// The nonce already validated on the options page,
		if (!empty($_POST['option_page']) && 'elementor' === $_POST['option_page']) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->validate_sdk();
		}

		if (is_admin()) {
			PrestaHelper::add_action('elementor/admin/after_create_settings/' . Settings::PAGE_ID, [$this, 'register_admin_fields']);
		}
	}

	public static function get_permalink($settings = [])
	{
		$post_id = PrestaHelper::get_post_id();

		if (isset($settings['url_format']) && 'pretty' === $settings['url_format']) {
			return get_permalink($post_id);
		}

		// Use plain url to avoid losing comments after change the permalink.
		return PrestaHelper::add_query_arg('p', $post_id, PrestaHelper::home_url());
	}

	public function register_admin_fields(Settings $settings)
	{
		$settings->add_section(Settings::TAB_INTEGRATIONS, 'facebook_sdk', [
			'callback' => function () {
				echo '<hr><h2>' . PrestaHelper::__('Facebook SDK', 'elementor') . '</h2>';

				echo sprintf(
					/* translators: 1: Link open tag, 2: Link closing tag. */
					PrestaHelper::__('Facebook SDK lets you connect to your %1$sdedicated application%2$s so you can track the Facebook Widgets analytics on your site.', 'elementor'),
					'<a href="https://developers.facebook.com/docs/apps/register/" target="_blank">',
					'</a>'
				);
				echo '<br><br>';

				echo PrestaHelper::__('If you are using the Facebook Comments Widget, you can add moderating options through your application. Note that this option will not work on local sites and on domains that don\'t have public access.', 'elementor');
			},
			'fields' => [
				'pro_facebook_app_id' => [
					'label' => PrestaHelper::__('App ID', 'elementor'),
					'field_args' => [
						'type' => 'text',
						/* translators: 1: Link open tag, 2: Link closing tag. */
						'desc' => sprintf(
							PrestaHelper::__('Remember to add the domain to your %1$sApp Domains%2$s', 'elementor'),
							sprintf('<a href="%s" target="_blank">', $this->get_app_settings_url()),
							'</a>'
						),
					],
				],
			],
		]);
	}

	private function get_app_settings_url()
	{
		$app_id = self::get_app_id();
		print_r($app_id);
		if ($app_id) {
			return sprintf('https://developers.facebook.com/apps/%d/settings/', $app_id);
		} else {
			return 'https://developers.facebook.com/apps/';
		}
	}

	private function validate_sdk()
	{
		$errors = [];

		if (!empty($_POST['elementor_pro_facebook_app_id'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$response = PrestaHelper::wp_remote_get('https://graph.facebook.com/' . $_POST['elementor_pro_facebook_app_id']); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if (PrestaHelper::is_wp_error($response) || 200 !== (int) PrestaHelper::wp_remote_retrieve_response_code($response)) {
				$errors[] = PrestaHelper::__('Facebook App ID is not valid', 'elementor');
			}
		}

		$message = implode('<br>', $errors);

		if (!empty($errors)) {
			wp_die($message, PrestaHelper::__('Facebook SDK', 'elementor'), ['back_link' => true]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
