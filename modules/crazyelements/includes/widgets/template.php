<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;

use CrazyElements\Core\Base\Document;
use CrazyElements\Plugin;

if (!defined('_PS_VERSION_')) {
	exit; // Exit if accessed directly.
}


class Widget_Template extends Widget_Base {

	public function get_name() {
		return 'template';
	}

	public function get_title() {
		return PrestaHelper::__( 'Template', 'elementor-pro' );
	}

	public function get_icon() {
		return 'ceicon-document-file';
	}

	public function get_keywords() {
		return [ 'elementor', 'template', 'library', 'block', 'page' ];
	}

	public function get_categories() {
		return array( 'crazy_addons' );
	}

	public function is_reload_preview_required() {
		return false;
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_template',
			[
				'label' => PrestaHelper::__( 'Template', 'elementor-pro' ),
			]
		);


        $this->add_control(
			'template_id',
			array(
				'label'     => PrestaHelper::__('Choose Template', 'elementor'),
				'type'      => Controls_Manager::AUTOCOMPLETE,
				'item_type' => 'saved_templates',
				'multiple'  => false,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
		// if ( 'publish' !== get_post_status( $template_id ) ) {
		// 	return;
		// }

        $template_ids          = $settings['template_id'];

		// Autocomplete result filterize 
		$template_id             = $this->render_autocomplete_result($template_ids);

		// function call to get the css for the template
		echo Plugin::crazyelements()->frontend->enqueue_template_css($template_id);

		?>
		<div class="elementor-template">
			<?php
				// PHPCS - should not be escaped.
				echo Plugin::crazyelements()->frontend->get_builder_content_for_display( $template_id );
			?>
		</div>
		<?php
	}

	public function render_plain_content() {}
}
