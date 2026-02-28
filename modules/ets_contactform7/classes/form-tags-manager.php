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
 
class WPCF7_FormTagsManager {
	private static $instance;
	private $tag_types = array();
	private $scanned_tags = null; // Tags scanned at the last time of scan()
	private function __construct() {}
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	public function get_scanned_tags() {
		return $this->scanned_tags;
	}
	public function add( $tag, $func, $features = '' ) {
		if ( ! is_callable( $func ) ) {
			return;
		}
		if ( true === $features ) { // for back-compat
			$features = array( 'name-attr' => true );
		}
		$features = ets_parse_args( $features, array() );
		$tags = array_filter( array_unique( (array) $tag ) );
		foreach ( $tags as $tag ) {
			$tag = $this->sanitize_tag_type( $tag );
			if ( ! $this->tag_type_exists( $tag ) ) {
				$this->tag_types[$tag] = array(
					'function' => $func,
					'features' => $features,
				);
			}
		}
	}
	public function tag_type_exists( $tag ) {
		return isset( $this->tag_types[$tag] );
	}
	public function tag_type_supports( $tag, $feature ) {
		$feature = array_filter( (array) $feature );
		if ( isset( $this->tag_types[$tag]['features'] ) ) {
			return (bool) array_intersect(
				array_keys( array_filter( $this->tag_types[$tag]['features'] ) ),
				$feature );
		}
		return false;
	}
	public function collect_tag_types( $feature = null, $invert = false ) {
		$tag_types = array_keys( $this->tag_types );
		if ( empty( $feature ) ) {
			return $tag_types;
		}
		$output = array();
		foreach ( $tag_types as $tag ) {
			if ( ! $invert && $this->tag_type_supports( $tag, $feature )
			|| $invert && ! $this->tag_type_supports( $tag, $feature ) ) {
				$output[] = $tag;
			}
		}
		return $output;
	}
	private function sanitize_tag_type( $tag ) {
		$tag = preg_replace( '/[^a-zA-Z0-9_*]+/', '_', $tag );
		$tag = rtrim( $tag, '_' );
		$tag = Tools::strtolower( $tag );
		return $tag;
	}
	public function remove( $tag ) {
		unset( $this->tag_types[$tag] );
	}
	public function normalize( $content ) {
		if ( empty( $this->tag_types ) ) {
			return $content;
		}
		$content = preg_replace_callback(
			'/' . $this->tag_regex() . '/s',
			array( $this, 'normalize_callback' ),
			$content );

		return $content;
	}
	private function normalize_callback( $m ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return $m[0];
		}
		$tag = $m[2];
		$attr = trim( preg_replace( '/[\r\n\t ]+/', ' ', $m[3] ) );
		$attr = strtr( $attr, array( '<' => '&lt;', '>' => '&gt;' ) );

		$content = trim( $m[5] );
		$content = str_replace( "\n", '<WPPreserveNewline />', $content );

		$result = $m[1] . '[' . $tag
			. ( $attr ? ' ' . $attr : '' )
			. ( $m[4] ? ' ' . $m[4] : '' )
			. ']'
			. ( $content ? $content . '[/' . $tag . ']' : '' )
			. $m[6];

		return $result;
	}
	public function replace_all( $content ) {
		return $this->scan( $content, true );
	}
	public function scan( $content, $replace = false ) {
		$this->scanned_tags = array();
		if ( empty( $this->tag_types ) ) {
			if ( $replace ) {
				return $content;
			} else {
				return $this->scanned_tags;
			}
		}
		if ( $replace ) {
			$content = preg_replace_callback(
				'/' . $this->tag_regex() . '/s',
				array( $this, 'replace_callback' ),
				$content );
			return $content;
		} else {
			preg_replace_callback(
				'/' . $this->tag_regex() . '/s',
				array( $this, 'scan_callback' ),
				$content );

			return $this->scanned_tags;
		}
	}
	public function filter( $input, $cond ) {
		if ( is_array( $input ) ) {
			$tags = $input;
		} elseif ( is_string( $input ) ) {
			$tags = $this->scan( $input );
		} else {
			$tags = $this->scanned_tags;
		}
		if ( empty( $tags ) ) {
			return array();
		}
		$cond = ets_parse_args( $cond, array(
			'type' => array(),
			'name' => array(),
			'feature' => '',
		) );
		$type = array_filter( (array) $cond['type'] );
		$name = array_filter( (array) $cond['name'] );
		$feature = is_string( $cond['feature'] ) ? trim( $cond['feature'] ) : '';
		if ( '!' == Tools::substr( $feature, 0, 1 ) ) {
			$feature_negative = true;
			$feature = trim(Tools::substr( $feature, 1 ) );
		} else {
			$feature_negative = false;
		}
		$output = array();
		foreach ( $tags as $tag ) {
			$tag = new WPCF7_FormTag( $tag );

			if ( $type && ! in_array( $tag->type, $type, true ) ) {
				continue;
			}
			if ( $name && ! in_array( $tag->name, $name, true ) ) {
				continue;
			}
			if ( $feature ) {
				if ( ! $this->tag_type_supports( $tag->type, $feature )
				&& ! $feature_negative ) {
					continue;
				} elseif ( $this->tag_type_supports( $tag->type, $feature )
				&& $feature_negative ) {
					continue;
				}
			}
			$output[] = $tag;
		}
		return $output;
	}
	private function tag_regex() {
		$tagnames = array_keys( $this->tag_types );
		$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );

		return '(\[?)'
			. '\[(' . $tagregexp . ')(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\]'
			. '(?:([^[]*?)\[\/\2\])?'
			. '(\]?)';
	}
	private function replace_callback( $m ) {
	   
		return $this->scan_callback( $m, true );
	}
	private function scan_callback( $m, $replace = false ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return Tools::substr( $m[0], 1, -1 );
		}
		$tag = $m[2];
		$attr = $this->parse_atts( $m[3] );
		$scanned_tag = array(
			'type' => $tag,
			'basetype' => trim( $tag, '*' ),
			'name' => '',
			'options' => array(),
			'raw_values' => array(),
			'values' => array(),
			'pipes' => null,
			'labels' => array(),
			'attr' => '',
			'content' => '',
		);
		if ( is_array( $attr ) ) {
			if ( is_array( $attr['options'] ) ) {
				if ( $this->tag_type_supports( $tag, 'name-attr' )
				&& ! empty( $attr['options'] ) ) {
					$scanned_tag['name'] = array_shift( $attr['options'] );

					if ( ! etscf7_is_name( $scanned_tag['name'] ) ) {
						return $m[0]; // Invalid name is used. Ignore this tag.
					}
				}
				$scanned_tag['options'] = (array) $attr['options'];
			}
			$scanned_tag['raw_values'] = (array) $attr['values'];

			$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
			$scanned_tag['values'] = $pipes->collect_afters() ?: $pipes->collect_befores();
			$scanned_tag['pipes'] = $pipes;
//			$scanned_tag['labels'] = $scanned_tag['values'];
			$scanned_tag['labels'] = $pipes->collect_befores();
		} else {
			$scanned_tag['attr'] = $attr;
		}
		$scanned_tag['values'] = array_map( 'trim', $scanned_tag['values'] );
		$scanned_tag['labels'] = array_map( 'trim', $scanned_tag['labels'] );
		$content = trim( $m[5] );
		$content = preg_replace( "/<br[\r\n\t ]*\/?>$/m", '', $content );
		$scanned_tag['content'] = $content;
		$scanned_tag = new WPCF7_FormTag( $scanned_tag );
		$this->scanned_tags[] = $scanned_tag;

		if ( $replace ) {
			$func = $this->tag_types[$tag]['function'];
			return $m[1] . call_user_func( $func, $scanned_tag ) . $m[6];
		} else {
			return $m[0];
		}
	}

	private function parse_atts( $text ) {
		$atts = array( 'options' => array(), 'values' => array() );
		$text = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", $text );
		$text = stripcslashes( trim( $text ) );

		$pattern = '%^([-+*=0-9a-zA-Z:.!?#$&@_/|\%\r\n\t ]*?)((?:[\r\n\t ]*"[^"]*"|[\r\n\t ]*\'[^\']*\')*)$%';

		if ( preg_match( $pattern, $text, $match ) ) {
			if ( ! empty( $match[1] ) ) {
				$atts['options'] = preg_split( '/[\r\n\t ]+/', trim( $match[1] ) );
			}

			if ( ! empty( $match[2] ) ) {
				preg_match_all( '/"[^"]*"|\'[^\']*\'/', $match[2], $matched_values );
				$atts['values'] = etscf7_strip_quote_deep( $matched_values[0] );
			}
		} else {
			$atts = $text;
		}

		return $atts;
	}
    public function set_instance()
    {
        etscf7_add_form_tag( 'acceptance',
    		'etscf7_acceptance_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'do-not-store' => true,
    		)
    	);
        etscf7_add_form_tag( array( 'checkbox', 'checkbox*', 'radio' ),
    		'etscf7_checkbox_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'selectable-values' => true,
    			'multiple-controls-container' => true,
    		)
    	);
        etscf7_add_form_tag( 'count',
    		'etscf7_count_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'zero-controls-container' => true,
    			'not-for-mail' => true,
    		)
    	);
        etscf7_add_form_tag( array( 'date', 'date*' ),
		'etscf7_date_form_tag_handler', array( 'name-attr' => true ) );
        etscf7_add_form_tag( array( 'file', 'file*' ),
		'etscf7_file_form_tag_handler', array( 'name-attr' => true ) );
        etscf7_add_form_tag( 'hidden',
    		'etscf7_hidden_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'display-hidden' => true,
    		)
    	);
        etscf7_add_form_tag( array( 'number', 'number*', 'range', 'range*' ),
		'etscf7_number_form_tag_handler', array( 'name-attr' => true ) );
        etscf7_add_form_tag( 'quiz',
    		'etscf7_quiz_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'do-not-store' => true,
    			'not-for-mail' => true,
    		)
    	);
        etscf7_add_form_tag( 'recaptcha', 'etscf7_recaptcha_form_tag_handler', array( 'display-block' => true ) );
        etscf7_add_form_tag( 'captcha', 'etscf7_captcha_form_tag_handler', array('name-attr' => true, 'display-block' => true ) );
        etscf7_add_form_tag( 'response', 'etscf7_response_form_tag_handler', array( 'display-block' => true ) );
        etscf7_add_form_tag( array( 'select', 'select*' ),
    		'etscf7_select_form_tag_handler',
    		array(
    			'name-attr' => true,
    			'selectable-values' => true,
    		)
    	);
        etscf7_add_form_tag( 'submit', 'etscf7_submit_form_tag_handler' );
        etscf7_add_form_tag(array( 'text', 'text*', 'email', 'email*', 'url', 'url*', 'tel', 'tel*', 'password', 'password*' ), 'etscf7_text_form_tag_handler', array( 'name-attr' => true ) );
        etscf7_add_form_tag( array( 'textarea', 'textarea*' ), 'etscf7_textarea_form_tag_handler', array( 'name-attr' => true ) );
    }
}
