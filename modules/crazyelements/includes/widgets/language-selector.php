<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Widget_Language_Selector extends Widget_Base {


	public function get_name() {
		return 'language_selector';
	}

	public function get_title() {
		return PrestaHelper::__( 'Language Selector', 'elementor-pro' );
	}

	public function get_icon() {
		return 'ceicon-font-awesome';
	}

	public function get_categories() {
		return [ 'theme-elements' ];
	}


	protected function _register_controls() {

		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => PrestaHelper::__( 'Style', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'              => PrestaHelper::__( 'Default', 'elementor' ),
					'custom'     => PrestaHelper::__( 'Custom', 'elementor' )
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'   => PrestaHelper::__( 'Title', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Language : ',
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_bt',
			array(
				'label'            => PrestaHelper::__( 'Button Icon', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-language',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'bt_align',
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
					'{{WRAPPER}}  .smart-dropdown-area' => 'justify-content: {{VALUE}}'
				],
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => PrestaHelper::__( 'Button Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'button_background_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background_color_hover',
			array(
				'label'     => PrestaHelper::__( 'Background Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn:hover' => 'background-color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);


		$this->add_control(
			'text_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn' => 'color: {{VALUE}};',
				),
			)
		);


		$this->add_control(
			'bt_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn:hover' => 'color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bt_text_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bt_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn',
			)
		);

		$this->add_responsive_control(
			'bt_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bt_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  .smart-dropdown-area #smart_dropdown.crazy-customer-lang-selector  .smart-dropbtn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_pos',
			array(
				'label'   => PrestaHelper::__( 'Icon Position', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'before'              => PrestaHelper::__( 'Before', 'elementor' ),
					'after'     => PrestaHelper::__( 'After', 'elementor' )
				),
				'default' => 'before',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lang_icon_size',
			array(
				'label'     => PrestaHelper::__( 'Icon Size', 'elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .crazy-customer-lang-selector .smart-dropbtn svg' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .crazy-customer-lang-selector .smart-dropbtn i' => 'font-size: {{SIZE}}{{UNIT}};',
				),

			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'dd_style',
			array(
				'label' => PrestaHelper::__( 'Dropdown Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'dd_background_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dd_background_color_hover',
			array(
				'label'     => PrestaHelper::__( 'Background Hover/Selected Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content .smart-log-current' => 'background-color: {{VALUE}} !important;',
				),
				'separator' => 'after',
			)
		);


		$this->add_control(
			'dd_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a' => 'color: {{VALUE}};',
				),
			)
		);


		$this->add_control(
			'dd_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover/Selected Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content .smart-log-current' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dd_text_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'dd_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content > li',
			)
		);

		$this->add_responsive_control(
			'dd_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector .smart-dropdown-content > li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dd_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  #smart_dropdown.crazy-customer-lang-selector  .smart-dropdown-content a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( PrestaHelper::is_admin() ) {
			return;
		}

		$settings       = $this->get_settings_for_display();
		$source       = $settings['source'];
		$context         = \Context::getContext();
		if($source == 'default'){
			if ( \Module::isInstalled( 'ps_languageselector' ) && \Module::isEnabled( 'ps_languageselector' ) ) {
				$mod_ins = \Module::getInstanceByName( 'ps_languageselector' );
				if ( ! PrestaHelper::is_admin() ) {
					\Hook::exec( 'displayHeader' );
				}

				if ( ! is_object( $mod_ins ) ) {
					return $results;
				}
				$results         = '';
				$context         = \Context::getContext();
				if ( \Validate::isLoadedObject( $mod_ins ) && method_exists( $mod_ins, 'renderWidget' ) ) {
					$results = $mod_ins->renderWidget( PrestaHelper::$hook_current, array() );
				}
				echo $results;
			}
		}else{

			$icon_bt       = $settings['icon_bt'];
			$heading_text       = $settings['heading_text'];
			$icon_pos       = $settings['icon_pos'];

			$languages = \Language::getLanguages(true, $context->shop->id);

			foreach ($languages as &$lang) {
				$lang['name_simple'] = $this->getNameSimple($lang['name']);
			}
			$current_language = array(
                'id_lang' => $context->language->id,
                'name' => $context->language->name,
                'name_simple' => $this->getNameSimple($context->language->name),
                'iso_code' => $context->language->iso_code
            );
			$link = $context->link;
			?>
			<div class="smart-dropdown-area">
				<div id="smart_dropdown" class="crazy-customer-lang-selector smart-dropdown">
					<button class="smart-dropbtn">
						<?php 
						if($icon_pos == 'before'){
							Icons_Manager::render_icon( $icon_bt, [ 'aria-hidden' => 'true' ] ); 
						}
						?>
						<span class="title"><?php echo $heading_text; ?></span>
						<span><?php echo $current_language['name_simple']; ?></span>
						<?php 
						if($icon_pos == 'after'){
							Icons_Manager::render_icon( $icon_bt, [ 'aria-hidden' => 'true' ] ); 
						}
						?>
					</button>
					<ul class="smart-dropdown-content">
						<?php 
						foreach($languages as $language){
							?>
							<li>
								<a <?php if($language['id_lang'] == $current_language['id_lang']){ echo 'class="smart-log-current"'; } ?> href="<?php echo $link->getLanguageLink($language['id_lang']); ?>"><?php echo $language['name_simple']; ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php 
		}
	}

	private function getNameSimple($name)
    {
        return preg_replace('/\s\(.*\)$/', '', $name);
    }

	/**
	 * Render accordion widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function _content_template() {
	}

}
