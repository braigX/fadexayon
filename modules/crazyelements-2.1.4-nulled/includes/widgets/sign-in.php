<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}

class Widget_Sign_In extends Widget_Base {


	public function get_name() {
		return 'sign_in';
	}

	public function get_title() {
		return PrestaHelper::__( 'Sign In', 'elementor-pro' );
	}

	public function get_icon() {
		return 'ceicon-sign-in-widget';
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
			'icon_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Icons', 'elementor'),
				'separator' => 'before',
				'condition'        => array(
					'source' => 'custom',
				),
			]
		);

		$this->add_control(
			'icon_sign_in',
			array(
				'label'            => PrestaHelper::__( 'Sign In', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-sign-in-alt',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_sign_out',
			array(
				'label'            => PrestaHelper::__( 'Sign Out', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-sign-out-alt',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_customer',
			array(
				'label'            => PrestaHelper::__( 'Customer', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'_icon_size',
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
					'{{WRAPPER}} .crazy-user-info a svg' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .crazy-user-info a i' => 'font-size: {{SIZE}}{{UNIT}};',
				),

			)
		);

		$this->add_control(
			'texts_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Texts', 'elementor'),
				'separator' => 'before',
				'condition'        => array(
					'source' => 'custom',
				),
			]
		);

		$this->add_control(
			'sign_in_text',
			array(
				'label'   => PrestaHelper::__( 'Sign In', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'condition'        => array(
					'source' => 'custom',
				),
				'label_block' => true,
				'default' => 'Sign In',
			)
		);

		$this->add_control(
			'sign_out_text',
			array(
				'label'   => PrestaHelper::__( 'Sign Out', 'elementor' ),
				'type'    => Controls_Manager::TEXT,
				'condition'        => array(
					'source' => 'custom',
				),
				'label_block' => true,
				'default' => 'Sign Out',
			)
		);

		$this->add_control(
			'layout_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Layout', 'elementor'),
				'separator' => 'before',
				'condition'        => array(
					'source' => 'custom',
				),
			]
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => PrestaHelper::__( 'Orientation', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'inline'              => PrestaHelper::__( 'Inline', 'elementor' ),
					'stacked'     => PrestaHelper::__( 'Stacked', 'elementor' )
				),
				'default' => 'stacked',
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'gap_between',
			[
				'label' => PrestaHelper::__( 'Gap Between', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
					'vh' => [
						'min' => 0,
						'max' => 200,
					],
					'vw' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '10',
				],
				'selectors' => [
					'{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'cust_name_style',
			array(
				'label'   => PrestaHelper::__( 'Name Style', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'first_name'              => PrestaHelper::__( 'First Name', 'elementor' ),
					'last_name'              => PrestaHelper::__( 'Last Name', 'elementor' ),
					'f_l_name'              => PrestaHelper::__( 'First Name Last Name', 'elementor' ),
					'l_f_name'              => PrestaHelper::__( 'Last Name First Name', 'elementor' ),
				),
				'default' => 'f_l_name',
				'condition'        => array(
					'source' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => PrestaHelper::__( 'General Style', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'sign_background_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info' => 'background-color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sign_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info',
			)
		);
		$this->add_responsive_control(
			'sign_in_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sec_padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elecounter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'sign_in_align',
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
					'{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info' => 'text-align: {{VALUE}}'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #_desktop_user_info.crazy-customer-sign-in .crazy-user-info a' => 'color: {{VALUE}};',
				),
			)
		);


		$this->add_control(
			'text_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-customer-sign-in .crazy-user-info a:hover' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sign_in_text_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}}  .crazy-customer-sign-in .crazy-user-info',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
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
			if ( \Module::isInstalled( 'ps_customersignin' ) && \Module::isEnabled( 'ps_customersignin' ) ) {
				$mod_ins = \Module::getInstanceByName( 'ps_customersignin' );
				
				if ( ! is_object( $mod_ins ) ) {
					return $results;
				}
				$results         = '';
				$context         = \Context::getContext();
				if ( \Validate::isLoadedObject( $mod_ins ) && method_exists( $mod_ins, 'renderWidget' ) ) {
					$results = $mod_ins->renderWidget( PrestaHelper::$hook_current, array() );
				}
				echo $results;
			}else{
				echo "You need 'ps_customersignin' module installed to use this style.";
			}
		}else{

			$orientation       = $settings['orientation'];
			$icon_sign_in       = $settings['icon_sign_in'];
			$icon_sign_out       = $settings['icon_sign_out'];
			$icon_customer       = $settings['icon_customer'];
			$sign_in_text       = $settings['sign_in_text'];
			$sign_out_text       = $settings['sign_out_text'];
			$cust_name_style       = $settings['cust_name_style'];
		
			$logged = $context->customer->isLogged();
			?>
			<div id="_desktop_user_info" class="crazy-customer-sign-in">
				<div class="user-info crazy-user-info crazy-u-info-<?php echo $orientation; ?>">
					<?php 
					$link = $context->link;
					
            		$my_account_url = $link->getPageLink('my-account', true);
					if($logged){
						$logout_url = $link->getPageLink('index', true, null, 'mylogout');
						$name = $context->customer->firstname . ' ' . $context->customer->lastname;
						if($cust_name_style == 'first_name'){
							$name = $context->customer->firstname;
						}elseif($cust_name_style == 'last_name'){
							$name = $context->customer->firstname;
						}elseif($cust_name_style == 'l_f_name'){
							$name = $context->customer->lastname . ' ' . $context->customer->firstname;
						}
					?>
						<a
							class="logout"
							href="<?php echo $logout_url; ?>"
							rel="nofollow"
						>
						<?php 
						Icons_Manager::render_icon( $icon_sign_out, [ 'aria-hidden' => 'true' ] );
						?>
							<?php echo $sign_out_text; ?>
						</a>
						<a
							class="account"
							href="<?php echo $my_account_url; ?>"
							rel="nofollow"
						>
						<?php 
						Icons_Manager::render_icon( $icon_customer, [ 'aria-hidden' => 'true' ] );
						?>
							<span ><?php echo $name; ?></span>
						</a>
					<?php }else{ ?>
						<a
							href="<?php echo $my_account_url; ?>"
							rel="nofollow"
						>
						<?php 
						Icons_Manager::render_icon( $icon_sign_in, [ 'aria-hidden' => 'true' ] );
						?>
							<span ><?php echo $sign_in_text; ?></span>
						</a>
					<?php } ?>
				</div>
			</div>

			<?php 
		}
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
