<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
 
class WPCF7_Pipe {
	public $before = '';
	public $after = '';
	public function __construct( $text ) {
		$text = (string) $text;
		$pipe_pos = strpos( $text, '|' );
		if ( false === $pipe_pos ) {
			$this->before = $this->after = trim( $text );
		} else {
			$this->before = trim( Tools::substr( $text, 0, $pipe_pos ) );
			$this->after = trim( Tools::substr( $text, $pipe_pos + 1 ) );
		}
	}
}
class WPCF7_Pipes {
	private $pipes = array();
	public function __construct( array $texts ) {
		foreach ( $texts as $text ) {
			$this->add_pipe( $text );
		}
	}
	private function add_pipe( $text ) {
		$pipe = new WPCF7_Pipe( $text );
		$this->pipes[] = $pipe;
	}
	public function do_pipe( $before ) {
		foreach ( $this->pipes as $pipe ) {
			if ( $pipe->before == $before ) {
				return $pipe->after;
			}
		}
		return $before;
	}
	public function collect_befores() {
		$befores = array();

		foreach ( $this->pipes as $pipe ) {
			$befores[] = $pipe->before;
		}
		return $befores;
	}
	public function collect_afters() {
		$afters = array();
		foreach ( $this->pipes as $pipe ) {
			$afters[] = $pipe->after;
		}
		return $afters;
	}
	public function zero() {
		return empty( $this->pipes );
	}
	public function random_pipe() {
		if ( $this->zero() ) {
			return null;
		}
		return $this->pipes[array_rand( $this->pipes )];
	}
}