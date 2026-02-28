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
 
class WPCF7_Validation implements ArrayAccess {
	private $invalid_fields = array();
	private $container = array();
	public function __construct() {
		$this->container = array(
			'valid' => true,
			'reason' => array(),
			'idref' => array(),
		);
	}
	public function invalidate( $context, $message ) {
		if ( $context instanceof WPCF7_FormTag ) {
			$tag = $context;
		} elseif ( is_array( $context ) ) {
			$tag = new WPCF7_FormTag( $context );
		} elseif ( is_string( $context ) ) {
			$tags = etscf7_scan_form_tags( array( 'name' => trim( $context ) ) );
			$tag = $tags ? new WPCF7_FormTag( $tags[0] ) : null;
		}
		$name = ! empty( $tag ) ? $tag->name : null;
		if ( empty( $name ) || ! etscf7_is_name( $name ) ) {
			return;
		}
		if ( $this->is_valid( $name ) ) {
			$id = $tag->get_id_option();
			if ( empty( $id ) || ! etscf7_is_name( $id ) ) {
				$id = null;
			}
			$this->invalid_fields[$name] = array(
				'reason' => (string) $message,
				'idref' => $id,
			);
		}
	}
	public function is_valid( $name = null ) {
		if ( ! empty( $name ) ) {
			return ! isset( $this->invalid_fields[$name] );
		} else {
			return empty( $this->invalid_fields );
		}
	}
	public function get_invalid_fields() {
		return $this->invalid_fields;
	}
	public function offsetSet( $offset, $value ) {
		if ( isset( $this->container[$offset] ) ) {
			$this->container[$offset] = $value;
		}

		if ( 'reason' == $offset && is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$this->invalidate( $k, $v );
			}
		}
	}
	public function offsetGet( $offset ) {
		if ( isset( $this->container[$offset] ) ) {
			return $this->container[$offset];
		}
	}
	public function offsetExists( $offset ) {
		return isset( $this->container[$offset] );
	}
	public function offsetUnset( $offset ) {
	   unset($offset);
	}
}