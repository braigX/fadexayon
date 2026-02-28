<?php
use CrazyElements\Modules\DynamicTags\Module as TagsModule;
use CrazyElements\PrestaHelper;
use CrazyElements\Controls_Manager;
use CrazyElements\Core\Schemes;

function public_header_control( $obj, $dflthdr ) {
	$obj->start_controls_section(
		'public_header_typography',
		array(
			'label' => PrestaHelper::__( 'Header', 'myshop-module' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		)
	);

	$obj->add_control(
		'public_header_tag',
		array(
			'label'   => PrestaHelper::__( 'Header Tag', 'myshop-module' ),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'p'    => 'p',
				'div'  => 'div',
				'span' => 'span',
			),
			'default' => $dflthdr,
		)
	);

	$obj->add_group_control(
		CrazyElements\Group_Control_Typography::get_type(),
		array(
			'name'     => 'public_header_typography',
			'label'    => PrestaHelper::__( 'Header', 'myshop-module' ),
			'selector' => '{{WRAPPER}} .typo-header-text',
		)
	);

	$obj->add_control(
		'public_header_color',
		array(
			'label'     => PrestaHelper::__( 'Header Color', 'myshop-module' ),
			'separator' => 'after',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .typo-header-text' => 'color: {{VALUE}} !important',
			),
		)
	);

	$obj->end_controls_section();
}


function public_title_control( $obj ) {
	$obj->start_controls_section(
		'public_title_typography',
		array(
			'label' => PrestaHelper::__( 'Title', 'myshop-module' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		)
	);

	$obj->add_group_control(
		CrazyElements\Group_Control_Typography::get_type(),
		array(
			'name'     => 'public_title_typography',
			'label'    => PrestaHelper::__( 'Title', 'myshop-module' ),
			'selector' => '{{WRAPPER}} .typo-title-text',
		)
	);

	$obj->add_control(
		'public_title_color',
		array(
			'label'     => PrestaHelper::__( 'Title Color', 'myshop-module' ),
			'separator' => 'after',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .typo-title-text' => 'color: {{VALUE}} !important',
			),
		)
	);

	$obj->end_controls_section();
}

function public_content_control( $obj ) {
	$obj->start_controls_section(
		'public_content_typography',
		array(
			'label' => PrestaHelper::__( 'Content', 'myshop-module' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		)
	);

	$obj->add_group_control(
		CrazyElements\Group_Control_Typography::get_type(),
		array(
			'name'     => 'public_content_typography',
			'label'    => PrestaHelper::__( 'Content', 'myshop-module' ),
			'selector' => '{{WRAPPER}} .typo-content-text',
		)
	);

	$obj->add_control(
		'public_content_color',
		array(
			'label'     => PrestaHelper::__( 'Content Color', 'myshop-module' ),
			'separator' => 'after',
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .typo-content-text' => 'color: {{VALUE}} !important',
			),
		)
	);

	$obj->end_controls_section();
}


function get_crazy_url( $obj ) {
	if ( $obj['url'] ) {
		if ( $obj['is_external'] ) {
			$target = 'target = "_blank"';
		} else {
			$target = null; }
		if ( $obj['nofollow'] ) {
			$nofollow = 'nofollow';
		} else {
			$nofollow = null; }
		$url = 'href="' . $obj['url'] . '" ' . $target . ' ' . $nofollow;
		return $url;
	} else {
		$url = null;
		return $url;
	}
}
