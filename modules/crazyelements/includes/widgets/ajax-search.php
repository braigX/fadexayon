<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_AjaxSearch extends Widget_Base {

	public function get_name() {
		return 'ajax_search';
	}

	public function get_title() {
		return PrestaHelper::__( 'Ajax Search', 'elementor' );
	}

	public function get_icon() {
		$hook = \Tools::getValue('hook');
		if($hook == 'hbuilder' || $hook == 'fbuilder' || $hook == 'fzfbuilder'){
			return 'ceicon-search';
		}else{
			return 'ceicon-ajax-search-widget';
		}
	}

	public function get_categories() {
		return array( 'products', 'theme-elements' );
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => PrestaHelper::__( 'General', 'elementor' ),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => PrestaHelper::__( 'Style', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'              => PrestaHelper::__( 'Default', 'elementor' ),
					'search-container'     => PrestaHelper::__( 'Search Container Form', 'elementor' ),
					'meterial_search_form' => PrestaHelper::__( 'Material Form', 'elementor' ),
					'toggle_search_form' => PrestaHelper::__( 'Toogle Form Layout', 'elementor' ),
				),
				'default' => 'default',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => PrestaHelper::__( 'Title', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => PrestaHelper::__( 'Type your title here', 'elementor' ),
			)
		);
		$this->add_control(
			'placeholder',
			array(
				'label'       => PrestaHelper::__( 'Placeholder', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => PrestaHelper::__( 'Search', 'elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);
		$this->add_control(
			'selected_icon',
			array(
				'label'            => PrestaHelper::__( 'Icon', 'elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-search',
					'library' => 'fa-solid',
				),
			)
		);
		$this->add_control(
			'button_title',
			array(
				'label'       => PrestaHelper::__( 'Button Text', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => PrestaHelper::__( 'Type your title here', 'elementor' ),
				'default' => 'Search',
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'layout',
							'operator' => '==',
							'value'    => 'search-container',
						),
					),
				),
			)
		);
		$this->add_control(
			'type',
			array(
				'label'   => PrestaHelper::__( 'Search Type', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'products'      => PrestaHelper::__( 'Products', 'elementor' ),
					'category'      => PrestaHelper::__( 'Categories', 'elementor' ),
					'suppliers'     => PrestaHelper::__( 'Suppliers', 'elementor' ),
					'manufacturers' => PrestaHelper::__( 'Manufacturers', 'elementor' ),
				),
				'default' => 'products',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_of_current_cat',
			array(
				'label'     => PrestaHelper::__( 'Show Product of Current Category', 'elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => 'yes',
				'condition' => array(
					'type' => array( 'products' ),
				),
			)
		);

		$this->add_control(
			'number_of_prds',
			[
				'label' => PrestaHelper::__('Number of results', 'elementor') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 4
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'title_style',
			array(
				'label' => PrestaHelper::__( 'Title & Form', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => PrestaHelper::__( 'Title Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-search label' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_typography',
				'label'    => PrestaHelper::__( 'Typography Title', 'elementor' ),
				'selector' => '{{WRAPPER}} .form-search label',
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'search_typography_placeholder',
				'label'    => PrestaHelper::__( 'Typography Form', 'elementor' ),
				'selector' => '{{WRAPPER}} .form-search, {{WRAPPER}} .smart_search_top #search_autocomplete ul li a',
			)
		);
		$this->add_responsive_control(
			'form_width',
			array(
				'label'      => PrestaHelper::__( 'Form Width', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'separator' => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .smart_search_top' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
            'alignment_form',
            [
                'label' => PrestaHelper::__('Form Alignment', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => PrestaHelper::__('Left', 'elementor'),
                        'icon' => 'ceicon-text-align-left',
                    ],
                    'center' => [
                        'title' => PrestaHelper::__('Center', 'elementor'),
                        'icon' => 'ceicon-text-align-center',
                    ],
                    'right' => [
                        'title' => PrestaHelper::__('Right', 'elementor'),
                        'icon' => 'ceicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .crazy-ajax-search-wrapper' => 'justify-content: {{VALUE}}',
                ],
                'separator' => 'after'
            ]
        );

		$this->add_control(
			'form_border_area',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Form Border', 'elementor'),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border_form',
				'label'    => 'Form Border',
				'selector' => '{{WRAPPER}} .smart_search_top .form-search #search_query_top',
			)
		);

		$this->add_responsive_control(
			'form_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .smart_search_top .form-search #search_query_top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'label'    => PrestaHelper::__('Box Shadow', 'customaddons'),
				'selector' => '{{WRAPPER}} .smart_search_top .form-search #search_query_top',
			)
		);

		$this->add_control(
			'form_border_focus_area',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Form Border Focus', 'elementor'),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border_form_hover',
				'label'    => 'Form Border Focus',
				'selector' => '{{WRAPPER}} .smart_search_top .form-search #search_query_top:focus',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'button_style',
			array(
				'label' => PrestaHelper::__( 'Button & Icon', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'alignment',
			array(
				'label'        => PrestaHelper::__( 'Alignment', 'elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'    => array(
						'title' => PrestaHelper::__( 'Left', 'elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'right'   => array(
						'title' => PrestaHelper::__( 'Right', 'elementor' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => PrestaHelper::__( 'Justify', 'elementor' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'prefix_class' => 'alignment%s',
			)
		);
		$this->add_responsive_control(
			'width',
			array(
				'label'      => PrestaHelper::__( 'Width', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .search-container button' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .search-container button ,{{WRAPPER}} .meterial_search_form',
			)
		);
		$this->add_control(
			'button_color',
			array(
				'label'     => PrestaHelper::__( 'Button Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .search-container button ,{{WRAPPER}} .meterial_search_form button' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'button_icon_color',
			array(
				'label'     => PrestaHelper::__( 'Icon Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart_search_top .form-search button[type=submit] i' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'button_icon_hover_color',
			array(
				'label'     => PrestaHelper::__( 'Icon Hover Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .smart_search_top .form-search button[type=submit] i:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => PrestaHelper::__( 'Normal', 'elementor' ),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'label'    => PrestaHelper::__( 'Background', 'elementor' ),
				'types'    => array( 'classic', 'gradient', 'video' ),
				'selector' => '{{WRAPPER}} .search-container button , {{WRAPPER}} .meterial_search_form button',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => PrestaHelper::__( 'Hover', 'elementor' ),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'background',
				'label'    => PrestaHelper::__( 'Hover Effect', 'elementor' ),
				'types'    => array( 'classic', 'gradient', 'video' ),
				'selector' => '{{WRAPPER}} .search-container button:hover , {{WRAPPER}} .meterial_search_form button:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_section();
		$this->start_controls_section(
			'advanced',
			array(
				'label' => PrestaHelper::__( 'Advanced', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => PrestaHelper::__( 'Gap', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .search-container , {{WRAPPER}} .meterial_search_form' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'layout',
							'operator' => '==',
							'value'    => 'search-container',
						),
					),
				),
			)
		);
		$this->add_responsive_control(
			'height',
			array(
				'label'      => PrestaHelper::__( 'Height', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .search-container, {{WRAPPER}} .meterial_search_form, {{WRAPPER}} .meterial_search_form input[type=text]' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'padding',
			array(
				'label'      => PrestaHelper::__( 'Padding', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'margin',
			array(
				'label'      => PrestaHelper::__( 'Margin', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-search' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'result_style',
			array(
				'label' => PrestaHelper::__( 'Results', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'result_area_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Result Area', 'elementor'),
			]
		);

		$this->add_control(
			'result_wrapper_back_color',
			array(
				'label'     => PrestaHelper::__( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul' => 'background: {{VALUE}} !important',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'area_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul',
			)
		);
		$this->add_responsive_control(
			'area_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'result_area_padding',
			array(
				'label'      => PrestaHelper::__( 'Area Padding', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'result_item_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => PrestaHelper::__('Result Items', 'elementor'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'result_typography',
				'label'    => PrestaHelper::__( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li a',
			)
		);
		$this->add_control(
			'result_color',
			array(
				'label'     => PrestaHelper::__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li a' => 'color: {{VALUE}}',
				),
			)
		);

		

		$this->add_control(
			'result_back_color',
			array(
				'label'     => PrestaHelper::__( 'Item Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'img_width',
			[
				'label' => PrestaHelper::__( 'Image Width', 'elementor' ),
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
				'selectors' => [
					'{{WRAPPER}} #search_autocomplete ul li a img' => 'width: {{SIZE}}{{UNIT}}',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'label'    => PrestaHelper::__( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li',
			)
		);
		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => PrestaHelper::__( 'Border Radius', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'result_padding',
			array(
				'label'      => PrestaHelper::__( 'Item Padding', 'elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'devices'    => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'  => array(
					'{{WRAPPER}} .crazy-ajax-search-wrapper #search_autocomplete ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$layout        = $settings['layout'];
		$type          = $settings['type'];
		$placeholder   = $settings['placeholder'];
		$selected_icon = $settings['selected_icon'];
		$title         = $settings['title'];
		$number_of_res         = $settings['number_of_prds'];
		$button_title  = $settings['button_title'];
		$alignment     = $settings['alignment'];
		$link          = new \Link();

		$data = array(
			'number_of_results' => $number_of_res
		);
		?>
		<script>
			var frontajaxurl = '<?php echo PrestaHelper::getAjaxUrl(); ?>';
			<?php 
			if(isset($layout) && $layout == 'toggle_search_form'){
			?>
			$(".toggle_search_form").hide();
			function search_toggle_on(){
				$(".toggle_search_form").fadeIn('slow');
				$(".search-toggle-off").show();
				$(".search-toggle-on").hide();
			}
			function search_toggle_off(){
				$(".toggle_search_form").fadeOut('slow');
				$(".search-toggle-on").show();
				$(".search-toggle-off").hide();
			}
			<?php 
			}
			?>
		</script>
		<div class="crazy-ajax-search-wrapper search-wrapper" >
			<div class="smart_search_top">
				<form id="searchbox" action="<?php echo $link->getPageLink( 'search' ); ?>" method="get">
				<div class="form-search">
					<?php if ( $title ) { ?>
						<label for="search_query_top"><?php echo $title; ?></label>
					<?php } ?>
					<?php
					if(isset($layout) && $layout == 'toggle_search_form'){
						echo '<div class="search-toggle-container">';
					}
					?>
					<div class="<?php echo $layout; ?> float-<?php echo $alignment; ?>">
						<input type="hidden" class="search_query_type" value="<?php echo $type; ?>"/>
							<?php
							if ( $type == 'products' ) {
								$show_of_current_cat = $settings['show_of_current_cat'];
								if ( ! isset( $show_of_current_cat ) || $show_of_current_cat == '' ) {
									$show_of_current_cat = 'no';
								} else {
									$show_of_current_cat = \Tools::getValue( 'id_category' );
								}
								$data['is_current'] = $show_of_current_cat;
							}
							$data = json_encode($data);
							?>
						<input type="hidden" class="is_current_catg" value="<?php echo htmlspecialchars($data); ?>"/>
						<input class="search_query search_query_top input-text" type="text" id="search_query_top" name="search_query" placeholder="<?php echo $placeholder; ?>"/>
						<?php 
							if(isset($layout) && $layout != 'toggle_search_form'){
							?>
						<button type="submit" class="<?php echo $layout; ?>">
							<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php echo $button_title; ?>
						</button>
						<?php 
						}
						?>
					</div>
					<?php
						if(isset($layout) && $layout == 'toggle_search_form'){
							echo '<div class="search-toggle">
							<i onclick="search_toggle_on()" class="search-toggle-on material-icons search" style="display: inline;"></i>
							<i onclick="search_toggle_off()" class="search-toggle-off material-icons" style="display: none;">close</i>
						</div></div>';
						}
					?>
				</div>     
				</form> 
				<div id="search_autocomplete" class="crazy-ajx-result search-autocomplete" style="display:none;">
					<ul id="autocomplete_appender" class="autocomplete_appender">        
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	protected function _content_template() {
	}
}