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
 
function ets_kses_no_null( $string, $options = null ) {
    if ( ! isset( $options['slash_zero'] ) ) {
        $options = array( 'slash_zero' => 'remove' );
    }
 
    $string = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string );
    if ( 'remove' == $options['slash_zero'] ) {
        $string = preg_replace( '/\\\\+0+/', '', $string );
    }
    return $string;
}
function ets_absint( $maybeint ) {
    return abs( (int)$maybeint );
}

function etscf7_is_blacklist_email($email)
{
    $email_blacklist = Configuration::get('ETS_CTF7_EMAIL_BLACK_LIST');
    if (!$email_blacklist || !($email))
        return false;
    $emails = explode("\n", $email_blacklist);
    if ($emails) {
        foreach ($emails as $pattern) {
            if (preg_match('/^' . str_replace('*', '(.*)', trim($pattern)) . '$/', $email)) {
                return true;
            }
        }
    }
    return false;
}

function ets_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
    $string = (string) $string;
 
    if ( 0 === Tools::strlen( $string ) )
        return '';
 
    // Don't bother if there are no specialchars - saves some processing
    if ( ! preg_match( '/[&<>"\']/', $string ) )
        return $string;
 
    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if ( empty( $quote_style ) )
        $quote_style = ENT_NOQUOTES;
    elseif ( ! in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) )
        $quote_style = ENT_QUOTES;
 
    // Store the site charset as a static to avoid multiple calls to ets_load_alloptions()

 
    if ( in_array( $charset, array( 'utf8', 'utf-8', 'UTF8' ) ) )
        $charset = 'UTF-8';
 
    $_quote_style = $quote_style;
 
    if ( $quote_style === 'double' ) {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ( $quote_style === 'single' ) {
        $quote_style = ENT_NOQUOTES;
    }
 
    if ( ! $double_encode ) {
        // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
        // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
        $string = ets_kses_normalize_entities( $string );
    }
 
    $string = @htmlspecialchars( $string, $quote_style, $charset, $double_encode );
 
    // Back-compat.
    if ( 'single' === $_quote_style )
        $string = str_replace( "'", '&#039;', $string );
 
    return $string;
}
function ets_check_invalid_utf8( $string, $strip = false ) {
    $string = (string) $string;
 
    if ( 0 === Tools::strlen( $string ) ) {
        return '';
    }
 
    // Store the site charset as a static to avoid multiple calls to get_option()
    static $is_utf8 = null;
    if ( ! isset( $is_utf8 ) ) {
        $is_utf8 = true;
    }
    if ( ! $is_utf8 ) {
        return $string;
    }
 
    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre = null;
    if ( ! isset( $utf8_pcre ) ) {
        $utf8_pcre = @preg_match( '/^./u', 'a' );
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if ( !$utf8_pcre ) {
        return $string;
    }
 
    // preg_match fails when it encounters invalid UTF8 in $string
    if ( 1 === @preg_match( '/^./us', $string ) ) {
        return $string;
    }
 
    // Attempt to strip the bad chars if requested (not recommended)
    if ( $strip && function_exists( 'iconv' ) ) {
        return iconv( 'utf-8', 'utf-8', $string );
    }
 
    return '';
}
function ets_esc_html( $text ) {
    $safe_text = ets_check_invalid_utf8( $text );
    $safe_text = ets_specialchars( $safe_text, ENT_QUOTES );
    return $safe_text;
}
function etscf7_autop_preserve_newline_callback( $matches ) {
	return str_replace( "\n", '<WPPreserveNewline />', $matches[0] );
}

function etscf7_sanitize_query_var( $text ) {
	$text = ets_unslash( $text );
	$text = ets_check_invalid_utf8( $text );
	if ( false !== strpos( $text, '<' ) ) {
		$text = ets_pre_kses_less_than( $text );
		$text = ets_strip_all_tags( $text );
	}
    
	$text = preg_replace( '/%[a-f0-9]{2}/i', '', $text );
	$text = preg_replace( '/ +/', ' ', $text );
	$text = trim( $text, ' ' );

	return $text;
}
function ets_pre_kses_less_than( $text ) {
    return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'ets_pre_kses_less_than_callback', $text);
}
function ets_pre_kses_less_than_callback( $matches ) {
    if ( false === strpos($matches[0], '>') )
        return ets_esc_html($matches[0]);
    return $matches[0];
}
function etscf7_strip_quote( $text ) {
	$text = trim( $text );

	if ( preg_match( '/^"(.*)"$/s', $text, $matches ) ) {
		$text = $matches[1];
	} elseif ( preg_match( "/^'(.*)'$/s", $text, $matches ) ) {
		$text = $matches[1];
	}
	return $text;
}

function etscf7_strip_quote_deep( $arr ) {
	if ( is_string( $arr ) ) {
		return etscf7_strip_quote( $arr );
	}

	if ( is_array( $arr ) ) {
		$result = array();

		foreach ( $arr as $key => $text ) {
			$result[$key] = etscf7_strip_quote_deep( $text );
		}

		return $result;
	}
}

function etscf7_normalize_newline( $text, $to = "\n" ) {
	if ( ! is_string( $text ) ) {
		return $text;
	}

	$nls = array( "\r\n", "\r", "\n" );

	if ( ! in_array( $to, $nls ) ) {
		return $text;
	}

	return str_replace( $nls, $to, $text );
}

function etscf7_normalize_newline_deep( $arr, $to = "\n" ) {
	if ( is_array( $arr ) ) {
		$result = array();

		foreach ( $arr as $key => $text ) {
			$result[$key] = etscf7_normalize_newline_deep( $text, $to );
		}

		return $result;
	}

	return etscf7_normalize_newline( $arr, $to );
}

function etscf7_strip_newline( $str ) {
	$str = (string) $str;
	$str = str_replace( array( "\r", "\n" ), '', $str );
	return trim( $str );
}

function etscf7_canonicalize( $text, $strto = 'lower' ) {
    $text = mb_convert_kana( $text, 'asKV', 'UTF-8' );
	if ( 'lower' == $strto ) {
		$text = Tools::strtolower( $text );
	} elseif ( 'upper' == $strto ) {
		$text = Tools::strtoupper( $text );
	}

	$text = trim( $text );
	return $text;
}

/**
 * Check whether a string is a valid NAME token.
 *
 * ID and NAME tokens must begin with a letter ([A-Za-z])
 * and may be followed by any number of letters, digits ([0-9]),
 * hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
 *
 * @see http://www.w3.org/TR/html401/types.html#h-6.2
 *
 * @return bool True if it is a valid name, false if not.
 */
function etscf7_is_name( $string ) {
	return preg_match( '/^[A-Za-z][-A-Za-z0-9_:.]*$/', $string );
}

function etscf7_sanitize_unit_tag( $tag ) {
	$tag = preg_replace( '/[^A-Za-z0-9_-]/', '', $tag );
	return $tag;
}

function etscf7_is_email( $email ) {
	return Validate::isEmail($email);
}

function etscf7_is_url( $url ) {
	$result = ( false !== filter_var( $url, FILTER_VALIDATE_URL ) );
	return $result;
}

function etscf7_is_tel( $tel ) {
	$result = preg_match( '%^[+]?[0-9()/ -]*$%', $tel );
	return $result;
}

function etscf7_is_number( $number ) {
	return is_numeric( $number );
}
function etscf7_is_date( $date ) {
	$result = preg_match( '/^([0-9]{4,})-([0-9]{2})-([0-9]{2})( [0-9]{2}:[0-9]{2})$/', $date, $matches );

	if ( $result ) {
		$result = checkdate( $matches[2], $matches[3], $matches[1] );
	}
    if($result)
	   return $result;
    else
    {
        $result = preg_match( '/^([0-9]{4,})-([0-9]{2})-([0-9]{2})$/', $date, $matches );
    	if ( $result ) {
    		$result = checkdate( $matches[2], $matches[3], $matches[1] );
    	}
        return $result;
    }
}

function etscf7_is_mailbox_list( $mailbox_list ) {
	if ( ! is_array( $mailbox_list ) ) {
		$mailbox_text = (string) $mailbox_list;
		$mailbox_text = ets_unslash( $mailbox_text );

		$mailbox_text = preg_replace( '/\\\\(?:\"|\')/', 'esc-quote',
			$mailbox_text );

		$mailbox_text = preg_replace( '/(?:\".*?\"|\'.*?\')/', 'quoted-string',
			$mailbox_text );

		$mailbox_list = explode( ',', $mailbox_text );
	}

	$addresses = array();

	foreach ( $mailbox_list as $mailbox ) {
		if ( ! is_string( $mailbox ) ) {
			return false;
		}

		$mailbox = trim( $mailbox );

		if ( preg_match( '/<(.+)>$/', $mailbox, $matches ) ) {
			$addr_spec = $matches[1];
		} else {
			$addr_spec = $mailbox;
		}

		if ( ! etscf7_is_email( $addr_spec ) ) {
			return false;
		}

		$addresses[] = $addr_spec;
	}

	return $addresses;
}
function etscf7_antiscript_file_name( $filename ) {
	$filename = basename( $filename );
	$parts = explode( '.', $filename );

	if ( count( $parts ) < 2 ) {
		return $filename;
	}

	$script_pattern = '/^(php|phtml|pl|py|rb|cgi|asp|aspx)\d?$/i';

	$filename = array_shift( $parts );
	$extension = array_pop( $parts );

	foreach ( (array) $parts as $part ) {
		if ( preg_match( $script_pattern, $part ) ) {
			$filename .= '.' . $part . '_';
		} else {
			$filename .= '.' . $part;
		}
	}

	if ( preg_match( $script_pattern, $extension ) ) {
		$filename .= '.' . $extension . '_.txt';
	} else {
		$filename .= '.' . $extension;
	}

	return $filename;
}

function etscf7_mask_password( $text, $length_unmasked = 0 ) {
	$length = Tools::strlen( $text );
	$length_unmasked = ets_absint( $length_unmasked );

	if ( 0 == $length_unmasked ) {
		if ( 9 < $length ) {
			$length_unmasked = 4;
		} elseif ( 3 < $length ) {
			$length_unmasked = 2;
		} else {
			$length_unmasked = $length;
		}
	}

	$text = Tools::substr( $text, 0 - $length_unmasked );
	$text = str_pad( $text, $length, '*', STR_PAD_LEFT );
	return $text;
}
function ets_sanitize_html_class( $class, $fallback = '' ) {
    //Strip out any % encoded octets
    $sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $class );
 
    //Limit to A-Z,a-z,0-9,_,-
    $sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized );
 
    if ( '' == $sanitized && $fallback ) {
        return ets_sanitize_html_class( $fallback );
    }
    return $sanitized;
}
function esc_textarea( $text ) {
    $safe_text = htmlspecialchars( $text, ENT_QUOTES);
    return $safe_text;
}
function etscf7_blacklist_check( $target ) {
	$mod_keys = trim( Configuration::get('ets_ctf7_blacklist_keys')); // get_option( 'blacklist_keys' )

	if ( empty( $mod_keys ) ) {
		return false;
	}

	$words = explode( "\n", $mod_keys );

	foreach ( (array) $words as $word ) {
		$word = trim( $word );

		if ( empty( $word ) || 256 < Tools::strlen( $word ) ) {
			continue;
		}

		$pattern = sprintf( '#%s#i', preg_quote( $word, '#' ) );

		if ( preg_match( $pattern, $target ) ) {
			return true;
		}
	}

	return false;
}

function etscf7_array_flatten( $input ) {
	if ( ! is_array( $input ) ) {
		return array( $input );
	}

	$output = array();

	foreach ( $input as $value ) {
		$output = array_merge( $output, etscf7_array_flatten( $value ) );
	}

	return $output;
}

function etscf7_flat_join( $input ) {
	$input = etscf7_array_flatten( $input );
	$output = array();

	foreach ( (array) $input as $value ) {
		$output[] = trim( (string) $value );
	}

	return implode( ', ', $output );
}

function etscf7_support_html5() {
	return true;
}

function etscf7_support_html5_fallback() {
	return true;
}

function etscf7_use_really_simple_captcha() {
	return true;
}

function etscf7_validate_configuration() {
	return true;
}
function etscf7_load_js() {
	return true;
}

function etscf7_load_css() {
	return true;
}

function etscf7_format_atts( $atts ) {
	$html = '';
	$prioritized_atts = array( 'type', 'name', 'value' );
	foreach ( $prioritized_atts as $att ) {
		if ( isset( $atts[$att] ) ) {
			$value = trim( $atts[$att] );
			$html .= sprintf( ' %s="%s"', $att,  $value );
			unset( $atts[$att] );
		}
	}
	foreach ( $atts as $key => $value ) {
		$key = Tools::strtolower( trim( $key ) );
		if ( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $key ) ) {
			continue;
		}
		$value = trim( $value );
		if ( '' !== $value ) {
			$html .= sprintf( ' %s="%s"', $key, $value );
		}
	}
	$html = trim( $html );
	return $html;
}
function etscf7_link( $url, $anchor_text, $args = '' ) {
	$defaults = array(
		'id' => '',
		'class' => '',
	);

	$args = ets_parse_args( $args, $defaults );
	$args = array_intersect_key( $args, $defaults );
	$atts = etscf7_format_atts( $args );

	$link = sprintf( '<a href="%1$s"%3$s>%2$s</a>',ets_esc_url( $url ),ets_esc_html( $anchor_text ),$atts ? ( ' ' . $atts ) : '' );

	return $link;
}

function etscf7_get_request_uri() {
	static $request_uri = '';

	if ( empty( $request_uri ) ) {
		$request_uri = ets_add_query_arg( array() );
	}

	return ets_esc_url_raw( $request_uri );
}

function etscf7_version( $args = '' ) {
	$defaults = array(
		'limit' => -1,
		'only_major' => false,
	);

	$args = ets_parse_args( $args, $defaults );

	if ( $args['only_major'] ) {
		$args['limit'] = 2;
	}

	$args['limit'] = (int) $args['limit'];

	$ver = WPCF7_VERSION;
	$ver = strtr( $ver, '_-+', '...' );
	$ver = preg_replace( '/[^0-9.]+/', ".$0.", $ver );
	$ver = preg_replace( '/[.]+/', ".", $ver );
	$ver = trim( $ver, '.' );
	$ver = explode( '.', $ver );

	if ( -1 < $args['limit'] ) {
		$ver = array_slice( $ver, 0, $args['limit'] );
	}

	$ver = implode( '.', $ver );

	return $ver;
}

function etscf7_version_grep( $version, array $input ) {
	$pattern = '/^' . preg_quote( (string) $version, '/' ) . '(?:\.|$)/';

	return preg_grep( $pattern, $input );
}

function etscf7_enctype_value( $enctype ) {
	$enctype = trim( $enctype );

	if ( empty( $enctype ) ) {
		return '';
	}

	$valid_enctypes = array(
		'application/x-www-form-urlencoded',
		'multipart/form-data',
		'text/plain',
	);

	if ( in_array( $enctype, $valid_enctypes ) ) {
		return $enctype;
	}

	$pattern = '%^enctype="(' . implode( '|', $valid_enctypes ) . ')"$%';

	if ( preg_match( $pattern, $enctype, $matches ) ) {
		return $matches[1]; // for back-compat
	}

	return '';
}

function etscf7_rmdir_p( $dir ) {
	if ( is_file( $dir ) && file_exists($dir) ) {
		if ( ! $result = @unlink( $dir ) ) {
			$stat = stat( $dir );
			$perms = $stat['mode'];
			chmod( $dir, $perms | 0200 ); // add write for owner

			if ( ! $result = @unlink( $dir ) ) {
				chmod( $dir, $perms );
			}
		}

		return $result;
	}
	if ( ! is_dir( $dir ) ) {
		return false;
	}

	if ( $handle = opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( $file == "." || $file == ".." ) {
				continue;
			}

			etscf7_rmdir_p( ets_path_join( $dir, $file ) );
		}

		closedir( $handle );
	}

	if ( false !== ( $files = scandir( $dir ) )
	&& ! array_diff( $files, array( '.', '..' ) ) ) {
		return rmdir( $dir );
	}

	return false;
}

/* From ets_http_build_query in wp-includes/functions.php */
function etscf7_build_query( $args, $key = '' ) {
	$sep = '&';
	$ret = array();

	foreach ( (array) $args as $k => $v ) {
		$k = urlencode( $k );

		if ( ! empty( $key ) ) {
			$k = $key . '%5B' . $k . '%5D';
		}

		if ( null === $v ) {
			continue;
		} elseif ( false === $v ) {
			$v = '0';
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			array_push( $ret, etscf7_build_query( $v, $k ) );
		} else {
			array_push( $ret, $k . '=' . urlencode( $v ) );
		}
	}

	return implode( $sep, $ret );
}

/**
 * Returns the number of code units in a string.
 *
 * @see http://www.w3.org/TR/html5/infrastructure.html#code-unit-length
 *
 * @return int|bool The number of code units, or false if mb_convert_encoding is not available.
 */
function etscf7_count_code_units( $string ) {
	static $use_mb = null;

	if ( is_null( $use_mb ) ) {
		$use_mb = function_exists( 'mb_convert_encoding' );
	}

	if ( ! $use_mb ) {
		return false;
	}

	$string = (string) $string;
	$string = str_replace( "\r\n", "\n", $string );

	$encoding = mb_detect_encoding( $string, mb_detect_order(), true );

	if ( $encoding ) {
		$string = mb_convert_encoding( $string, 'UTF-16', $encoding );
	} else {
		$string = mb_convert_encoding( $string, 'UTF-16', 'UTF-8' );
	}

	$byte_count = mb_strlen( $string, '8bit' );

	return floor( $byte_count / 2 );
}

function etscf7_is_localhost() {
	$server_name = Tools::strtolower( $_SERVER['SERVER_NAME'] );
	return in_array( $server_name, array( 'localhost', '127.0.0.1' ) ); 
}
function ets_sanitize_key( $key ) {
    $key = Tools::strtolower( $key );
    $key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
}
function etscf7_contact_form( $id ) {
	return WPCF7_ContactForm::get_instance( $id );
}
function etscf7_get_current_contact_form() {
	if ( $current = WPCF7_ContactForm::get_current() ) {
		return $current;
	}
}

function etscf7_is_posted() {
	if ( ! $contact_form = etscf7_get_current_contact_form() ) {
		return false;
	}

	return $contact_form->is_posted();
}
function etscf7_get_message( $status ) {
	if ( ! $contact_form = etscf7_get_current_contact_form() ) {
		return '';
	}

	return $contact_form->message( $status );
}

function etscf7_form_controls_class( $type, $default = '' ) {
	$type = trim( $type );
	$default = array_filter( explode( ' ', $default ) );

	$classes = array_merge( array( 'wpcf7-form-control' ), $default );

	$typebase = rtrim( $type, '*' );
	$required = ( '*' == Tools::substr( $type, -1 ) );

	$classes[] = 'wpcf7-' . $typebase;

	if ( $required ) {
		$classes[] = 'wpcf7-validates-as-required';
	}

	$classes = array_unique( $classes );

	return implode( ' ', $classes );
}
function etscf7_sanitize_form( $input, $default = '' ) {
	if ( null === $input ) {
		return $default;
	}

	$output = trim( $input );
	return $output;
}

function etscf7_sanitize_mail( $input, $defaults = array() ) {
	$defaults = ets_parse_args( $defaults, array(
		'active' => false,
		'subject' => '',
		'sender' => '',
		'recipient' => '',
		'body' => '',
		'additional_headers' => '',
		'attachments' => '',
		'use_html' => false,
		'exclude_blank' => false,
	) );

	$input = ets_parse_args( $input, $defaults );

	$output = array();
	$output['active'] = (bool) $input['active'];
	$output['subject'] = trim( $input['subject'] );
	$output['sender'] = trim( $input['sender'] );
	$output['recipient'] = trim( $input['recipient'] );
	$output['body'] = trim( $input['body'] );
	$output['additional_headers'] = '';

	$headers = str_replace( "\r\n", "\n", $input['additional_headers'] );
	$headers = explode( "\n", $headers );

	foreach ( $headers as $header ) {
		$header = trim( $header );

		if ( '' !== $header ) {
			$output['additional_headers'] .= $header . "\n";
		}
	}

	$output['additional_headers'] = trim( $output['additional_headers'] );
	$output['attachments'] = trim( $input['attachments'] );
	$output['use_html'] = (bool) $input['use_html'];
	$output['exclude_blank'] = (bool) $input['exclude_blank'];

	return $output;
}
function etscf7_sanitize_additional_settings( $input, $default = '' ) {
	if ( null === $input ) {
		return $default;
	}

	$output = trim( $input );
	return $output;
}
function ets_parse_str( $string, &$array ) {
    parse_str( $string, $array );
    $array = ets_stripslashes_deep( $array );
}
function etscf7_text_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_text_form_tag_handler($tag);
}
function etscf7_textarea_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_textarea_form_tag_handler($tag);
}
function etscf7_select_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_select_form_tag_handler($tag);
}
function etscf7_submit_form_tag_handler( $tag ) {
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_submit_form_tag_handler($tag);
}
function etscf7_response_form_tag_handler( $tag ) {
    unset($tag);
	if ( $contact_form = etscf7_get_current_contact_form() ) {
		return $contact_form->form_response_output();
	}
}
function etscf7_recaptcha_form_tag_handler( $tag ) {
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_recaptcha_form_tag_handler($tag);
}
function etscf7_captcha_form_tag_handler( $tag )
{
    if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_captcha_form_tag_handler($tag);
}
function etscf7_quiz_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_quiz_form_tag_handler($tag);
}
function etscf7_number_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_number_form_tag_handler($tag);
}
function etscf7_hidden_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_hidden_form_tag_handler($tag);
}
function etscf7_file_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_file_form_tag_handler($tag);
}
function etscf7_date_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_date_form_tag_handler($tag);
}
function etscf7_count_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_count_form_tag_handler($tag);
}
function etscf7_checkbox_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_checkbox_form_tag_handler($tag);
}
function ets_parse_args( $args, $defaults = '' ) {
    if ( is_object( $args ) )
        $r = get_object_vars( $args );
    elseif ( is_array( $args ) )
        $r =& $args;
    else
        ets_parse_str( $args, $r );
 
    if ( is_array( $defaults ) )
        return array_merge( $defaults, $r );
}
function etscf7_acceptance_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
    $ets_contactform7 = Module::getInstanceByName('ets_contactform7');
    return $ets_contactform7->etscf7_acceptance_form_tag_handler($tag);
}
function etscf7_autop_or_not() {
	return true;
}
function etscf7_autop( $pee, $br = 1 ) {
	if ( trim( $pee ) === '' ) {
		return '';
	}

	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	$pee = preg_replace( '|<br />\s*<br />|', "\n\n", $pee );
	// Space things out a little
	/* wpcf7: remove select and input */
	$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
	$pee = preg_replace( '!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee );
	$pee = preg_replace( '!(</' . $allblocks . '>)!', "$1\n\n", $pee );

	/* wpcf7: take care of [response], [recaptcha], and [hidden] tags */
	$form_tags_manager = WPCF7_FormTagsManager::get_instance();
	$block_hidden_form_tags = $form_tags_manager->collect_tag_types(
		array( 'display-block', 'display-hidden' ) );
	$block_hidden_form_tags = sprintf( '(?:%s)',
		implode( '|', $block_hidden_form_tags ) );

	$pee = preg_replace( '!(\[' . $block_hidden_form_tags . '[^]]*\])!',
		"\n$1\n\n", $pee );

	$pee = str_replace( array( "\r\n", "\r" ), "\n", $pee ); // cross-platform newlines

	if ( strpos( $pee, '<object' ) !== false ) {
		$pee = preg_replace( '|\s*<param([^>]*)>\s*|', "<param$1>", $pee ); // no pee inside object/embed
		$pee = preg_replace( '|\s*</embed>\s*|', '</embed>', $pee );
	}

	$pee = preg_replace( "/\n\n+/", "\n\n", $pee ); // take care of duplicates
	// make paragraphs, including one at the end
	$pees = preg_split( '/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY );
	$pee = '';

	foreach ( $pees as $tinkle ) {
		$pee .=  trim( $tinkle, "\n" ) . "\n";
	}
	$pee = preg_replace( '|<p>\s*</p>|', '', $pee ); // under certain strange conditions it could create a P of entirely whitespace
	$pee = preg_replace( '!<p>([^<]+)</(div|address|form|fieldset)>!', "<p>$1</p></$2>", $pee );
	$pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee ); // don't pee all over a tag
	$pee = preg_replace( "|<p>(<li.+?)</p>|", "$1", $pee ); // problem with nested lists
	$pee = preg_replace( '|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee );
	$pee = str_replace( '</blockquote></p>', '</p></blockquote>', $pee );
	$pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee );
	$pee = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee );

	/* wpcf7: take care of [response], [recaptcha], and [hidden] tag */
	$pee = preg_replace( '!<p>\s*(\[' . $block_hidden_form_tags . '[^]]*\])!',
		"$1", $pee );
	$pee = preg_replace( '!(\[' . $block_hidden_form_tags . '[^]]*\])\s*</p>!',
		"$1", $pee );

	if ( $br ) {
		/* wpcf7: add textarea */
		$pee = preg_replace_callback(
			'/<(script|style|textarea).*?<\/\\1>/s',
			'etscf7_autop_preserve_newline_callback', $pee );
		$pee = preg_replace( '|(?<!<br />)\s*\n|', "<br />\n", $pee ); // optionally make line breaks
		$pee = str_replace( '<WPPreserveNewline />', "\n", $pee );

		/* wpcf7: remove extra <br /> just added before [response], [recaptcha], and [hidden] tags */
		$pee = preg_replace( '!<br />\n(\[' . $block_hidden_form_tags . '[^]]*\])!',
			"\n$1", $pee );
	}

	$pee = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee );
	$pee = preg_replace( '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee );

	if ( strpos( $pee, '<pre' ) !== false ) {
		$pee = preg_replace_callback( '!(<pre[^>]*>)(.*?)</pre>!is',
			'clean_pre', $pee );
	}

	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

	return $pee;
}
function etscf7_add_form_tag( $tag, $func, $features = '' ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->add( $tag, $func, $features );
}

function etscf7_remove_form_tag( $tag ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->remove( $tag );
}

function etscf7_replace_all_form_tags( $content ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->replace_all( $content );
}

function etscf7_scan_form_tags( $cond = null ) {
	$contact_form = WPCF7_ContactForm::get_current();

	if ( $contact_form ) {
		return $contact_form->scan_form_tags( $cond );
	}

	return array();
}

function etscf7_form_tag_supports( $tag, $feature ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->tag_type_supports( $tag, $feature );
}
function etscf7_file_form_enctype_filter( $enctype ) {
	$multipart = (bool) etscf7_scan_form_tags(
		array( 'type' => array( 'file', 'file*' ) ) );

	if ( $multipart ) {
		$enctype = 'multipart/form-data';
	}

	return $enctype;
}
function etscf7_generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = Tools::strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/* File uploading functions */

function etscf7_acceptable_filetypes( $types = 'default', $format = 'regex' ) {
	if ( 'default' === $types || empty( $types ) ) {
		$types = array(
			'jpg',
			'jpeg',
			'png',
			'gif',
			'pdf',
			'doc',
			'docx',
			'ppt',
			'pptx',
			'odt',
			'avi',
			'ogg',
			'm4a',
			'mov',
			'mp3',
			'mp4',
			'mpg',
			'wav',
			'wmv',
		);
	} else {
		$types_tmp = (array) $types;
		$types = array();

		foreach ( $types_tmp as $val ) {
			if ( is_string( $val ) ) {
				$val = preg_split( '/[\s|,]+/', $val );
			}

			$types = array_merge( $types, (array) $val );
		}
	}

	$types = array_unique( array_filter( $types ) );

	$output = '';

	foreach ( $types as $type ) {
		$type = trim( $type, ' ,.|' );
		$type = str_replace(
			array( '.', '+', '*', '?' ),
			array( '\.', '\+', '\*', '\?' ),
			$type );

		if ( '' === $type ) {
			continue;
		}

		if ( 'attr' === $format || 'attribute' === $format ) {
			$output .= sprintf( '.%s', $type );
			$output .= ',';
		} else {
			$output .= $type;
			$output .= '|';
		}
	}

	return trim( $output, ' ,|' );
}

function etscf7_init_uploads() {
	$dir = etscf7_upload_tmp_dir();
	ets_mkdir_p( $dir );
    return true;
}
function etscf7_upload_tmp_dir() {
	if(!is_dir(_PS_ETS_CTF7_UPLOAD_DIR_))
	    mkdir(_PS_ETS_CTF7_UPLOAD_DIR_);
    return _PS_ETS_CTF7_UPLOAD_DIR_;
}
function ets_esc_url( $url, $protocols = null, $_context = 'display' ) {
 
    if ( '' == $url )
        return $url;
 
    $url = str_replace( ' ', '%20', $url );
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
 
    if ( '' === $url ) {
        return $url;
    }
 
    if ( 0 !== stripos( $url, 'mailto:' ) ) {
        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = _deep_replace($strip, $url);
    }
 
    $url = str_replace(';//', '://', $url);
    /* If the URL doesn't appear to contain a scheme, we
     * presume it needs http:// prepended (unless a relative
     * link starting with /, # or ? or a php file).
     */
    if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
        ! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
        $url = 'http://' . $url;
 
    // Replace ampersands and single quotes only when displaying.
    if ( 'display' == $_context ) {
        $url = ets_kses_normalize_entities( $url );
        $url = str_replace( '&amp;', '&#038;', $url );
        $url = str_replace( "'", '&#039;', $url );
    }
    if ( '/' === $url[0] ) {
        $good_protocol_url = $url;
    } else {
        if ( ! is_array( $protocols ) )
            $protocols = ets_allowed_protocols();
        $good_protocol_url = ets_kses_bad_protocol( $url, $protocols );
        if ( Tools::strtolower( $good_protocol_url ) != Tools::strtolower( $url ) )
            return '';
    }
    return $good_protocol_url;
}
function ets_allowed_protocols() {
    static $protocols = array();
 
    if ( empty( $protocols ) ) {
        $protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn' );
    }
    return $protocols;
}
function ets_kses_normalize_entities($string) {
    // Disarm all entities by converting & to &amp;
    $string = str_replace('&', '&amp;', $string);
    // Change back the allowed entities in our entity whitelist
    $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'ets_kses_normalize_entities2', $string);
    return $string;
}
function ets_kses_normalize_entities2($matches) {
    if ( empty($matches[1]) )
        return '';
 
    $i = $matches[1];
    $i = "&amp;#$i;";
    return $i;
}
function ets_kses_bad_protocol($string, $allowed_protocols) {
    $string = ets_kses_no_null($string);
    $iterations = 0;
 
    do {
        $original_string = $string;
        $string = ets_kses_bad_protocol_once($string, $allowed_protocols);
    } while ( $original_string != $string && ++$iterations < 6 );
 
    if ( $original_string != $string )
        return '';
 
    return $string;
}
function ets_kses_bad_protocol_once($string, $allowed_protocols, $count = 1 ) {
    $string2 = preg_split( '/:|&#0*58;|&#x0*3a;/i', $string, 2 );
    if ( isset($string2[1]) && ! preg_match('%/\?%', $string2[0]) ) {
        $string = trim( $string2[1] );
        $protocol = ets_kses_bad_protocol_once2( $string2[0], $allowed_protocols );
        if ( 'feed:' == $protocol ) {
            if ( $count > 2 )
                return '';
            $string = ets_kses_bad_protocol_once( $string, $allowed_protocols, ++$count );
            if ( empty( $string ) )
                return $string;
        }
        $string = $protocol . $string;
    }
 
    return $string;
}
function ets_kses_bad_protocol_once2( $string, $allowed_protocols ) {
    $string2 = ets_kses_decode_entities($string);
    $string2 = preg_replace('/\s/', '', $string2);
    $string2 = ets_kses_no_null($string2);
    $string2 = Tools::strtolower($string2);
    $allowed = false;
    foreach ( (array) $allowed_protocols as $one_protocol )
        if ( Tools::strtolower($one_protocol) == $string2 ) {
            $allowed = true;
            break;
        }
    if ($allowed)
        return "$string2:";
    else
        return '';
}
function ets_kses_decode_entities($string) {
    $string = preg_replace_callback('/&#([0-9]+);/', '_ets_kses_decode_entities_chr', $string);
    $string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', '_ets_kses_decode_entities_chr_hexdec', $string);
    return $string;
}
function _ets_kses_decode_entities_chr_hexdec( $match ) {
    return chr( hexdec( $match[1] ) );
}
function _ets_kses_decode_entities_chr( $match ) {
    return chr( $match[1] );
}
function ets_untrailingslashit( $string ) {
    return rtrim( $string, '/\\' );
}
function ets_esc_url_raw( $url, $protocols = null ) {
    return ets_esc_url( $url, $protocols, 'db' );
}
function ets_add_query_arg() {
    $args = func_get_args();
    if ( is_array( $args[0] ) ) {
        if ( count( $args ) < 2 || false === $args[1] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[1];
    } else {
        if ( count( $args ) < 3 || false === $args[2] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[2];
    }
 
    if ( $frag = strstr( $uri, '#' ) )
        $uri = Tools::substr( $uri, 0, -Tools::strlen( $frag ) );
    else
        $frag = '';
 
    if ( 0 === stripos( $uri, 'http://' ) ) {
        $protocol = 'http://';
        $uri = Tools::substr( $uri, 7 );
    } elseif ( 0 === stripos( $uri, 'https://' ) ) {
        $protocol = 'https://';
        $uri = Tools::substr( $uri, 8 );
    } else {
        $protocol = '';
    }
 
    if ( strpos( $uri, '?' ) !== false ) {
        list( $base, $query ) = explode( '?', $uri, 2 );
        $base .= '?';
    } elseif ( $protocol || strpos( $uri, '=' ) === false ) {
        $base = $uri . '?';
        $query = '';
    } else {
        $base = '';
        $query = $uri;
    }
    $qs=array();
    ets_parse_str( $query, $qs );
    $qs = ets_urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
    if ( is_array( $args[0] ) ) {
        foreach ( $args[0] as $k => $v ) {
            $qs[ $k ] = $v;
        }
    } else {
        $qs[ $args[0] ] = $args[1];
    }
 
    foreach ( $qs as $k => $v ) {
        if ( $v === false )
            unset( $qs[$k] );
    }
 
    $ret = ets_build_query( $qs );
    $ret = trim( $ret, '?' );
    $ret = preg_replace( '#=(&|$)#', '$1', $ret );
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim( $ret, '?' );
    return $ret;
}
function ets_urlencode_deep( $value ) {
    return ets_map_deep( $value, 'urlencode' );
}
function ets_map_deep( $value, $callback ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $index => $item ) {
            $value[ $index ] = ets_map_deep( $item, $callback );
        }
    } elseif ( is_object( $value ) ) {
        $object_vars = get_object_vars( $value );
        foreach ( $object_vars as $property_name => $property_value ) {
            $value->$property_name = ets_map_deep( $property_value, $callback );
        }
    } else {
        $value = call_user_func( $callback, $value );
    }
 
    return $value;
}
function ets_build_query( $data ) {
    return ets_http_build_query( $data, null, '&', '', false );
}
function ets_http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
    $ret = array();
 
    foreach ( (array) $data as $k => $v ) {
        if ( $urlencode)
            $k = urlencode($k);
        if ( is_int($k) && $prefix != null )
            $k = $prefix.$k;
        if ( !empty($key) )
            $k = $key . '%5B' . $k . '%5D';
        if ( $v === null )
            continue;
        elseif ( $v === false )
            $v = '0';
 
        if ( is_array($v) || is_object($v) )
            array_push($ret,ets_http_build_query($v, '', $sep, $k, $urlencode));
        elseif ( $urlencode )
            array_push($ret, $k.'='.urlencode($v));
        else
            array_push($ret, $k.'='.$v);
    }
 
    if ( null === $sep )
        $sep = ini_get('arg_separator.output');
 
    return implode($sep, $ret);
}
function _deep_replace( $search, $subject ) {
    $subject = (string) $subject;
 
    $count = 1;
    while ( $count ) {
        $subject = str_replace( $search, '', $subject, $count );
    }
 
    return $subject;
}
function ets_current_time( $type, $gmt = 0 ) {
    switch ( $type ) {
        case 'mysql':
            return ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s',  time());
        case 'timestamp':
            return ( $gmt ) ? time() : time();
        default:
            return ( $gmt ) ? date( $type ) : date( $type, time());
    }
}
function ets_strip_all_tags($string, $remove_breaks = false) {
    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
    $string = strip_tags($string);
 
    if ( $remove_breaks )
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
 
    return trim( $string );
}
function etscf7_mail_replace_tags( $content, $args = '',$body=false ) {
    if($args)
    {
        $args=array(
			'html' => false,
			'exclude_blank' => false,
		);
    }
    
	if ( is_array( $content ) ) {
		foreach ( $content as $key => $value ) {
			$content[$key] = etscf7_mail_replace_tags( $value, $args );
		}

		return $content;
	}
	$content = explode( "\n", $content );
	foreach ( $content as $num => $line ) {
		$line = new WPCF7_MailTaggedText( $line, $args );
        $replaced = $line->replace_tags();
		if ( $args['exclude_blank'] ) {
			$replaced_tags = $line->get_replaced_tags();

			if ( empty( $replaced_tags ) || array_filter( $replaced_tags ) ) {
				$content[$num] = $replaced;
			} else {
				unset( $content[$num] ); // Remove a line.
			}
		} else {
			$content[$num] = $replaced;
		}
	}
    
	$content = implode( "\n", $content );
    unset($body);
	return $content;
}
function ets_unslash( $value ) {
    return ets_stripslashes_deep( $value );
}
function ets_stripslashes_deep( $value ) {
	return ets_map_deep( $value, 'ets_stripslashes_from_strings_only' );
}
function ets_stripslashes_from_strings_only( $value ) {
    return is_string( $value ) ? Tools::stripslashes( $value ) : $value;
}
function etscf7_is_valid_locale( $locale ) {
	$pattern = '/^[a-z]{2,3}(?:_[a-zA-Z_]{2,})?$/';
	return (bool) preg_match( $pattern, $locale );
}
function ets_path_join( $base, $path ) {
    if ( ets_path_is_absolute($path) )
        return $path;
 
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
function ets_mkdir_p( $target ) {
    $wrapper = null;
 
    // Strip the protocol.
    if ( ets_is_stream( $target ) ) {
        list( $wrapper, $target ) = explode( '://', $target, 2 );
    }
 
    // From php.net/mkdir user contributed notes.
    $target = str_replace( '//', '/', $target );
 
    // Put the wrapper back on the target.
    if ( $wrapper !== null ) {
        $target = $wrapper . '://' . $target;
    }
 
    /*
     * Safe mode fails with a trailing slash under certain PHP versions.
     * Use rtrim() instead of ets_untrailingslashit to avoid formatting.php dependency.
     */
    $target = rtrim($target, '/');
    if ( empty($target) )
        $target = '/';
 
    if ( file_exists( $target ) )
        return @is_dir( $target );
 
    // We need to find the permissions of the parent folder that exists and inherit that.
    $target_parent = dirname( $target );
    while ( '.' != $target_parent && ! is_dir( $target_parent ) ) {
        $target_parent = dirname( $target_parent );
    }
 
    // Get the permission bits.
    if ( $stat = @stat( $target_parent ) ) {
        $dir_perms = $stat['mode'] & 0007777;
    } else {
        $dir_perms = 0777;
    }
 
    if ( @mkdir( $target, $dir_perms, true ) ) {
 
        /*
         * If a umask is set that modifies $dir_perms, we'll have to re-set
         * the $dir_perms correctly with chmod()
         */
        if ( $dir_perms != ( $dir_perms & ~umask() ) ) {
            $folder_parts = explode( '/', Tools::substr( $target, Tools::strlen( $target_parent ) + 1 ) );
            for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ ) {
                @chmod( $target_parent . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
            }
        }
 
        return true;
    }
 
    return false;
}
function ets_is_stream( $path ) {
    $wrappers = stream_get_wrappers();
    $wrappers_re = '(' . join('|', $wrappers) . ')';
 
    return preg_match( "!^$wrappers_re://!", $path ) === 1;
}
function ets_path_is_absolute( $path ) {
    /*
     * This is definitive if true but fails if $path does not exist or contains
     * a symbolic link.
     */
    if ( realpath($path) == $path )
        return true;
 
    if ( Tools::strlen($path) == 0 || $path[0] == '.' )
        return false;
 
    // Windows allows absolute paths like this.
    if ( preg_match('#^[a-zA-Z]:\\\\#', $path) )
        return true;
 
    // A path starting with / or \ is absolute; anything else is relative.
    return ( $path[0] == '/' || $path[0] == '\\' );
}
function zeroise( $number, $threshold ) {
    return sprintf( '%0' . $threshold . 's', $number );
}
function ets_unique_filename( $dir, $filename, $unique_filename_callback = null ) {
    // Separate the filename into a name and extension.
    $ext = pathinfo( $filename, PATHINFO_EXTENSION );
    $name = pathinfo( $filename, PATHINFO_BASENAME );
    if ( $ext ) {
        $ext = '.' . $ext;
    }
 
    // Edge case: if file is named '.ext', treat as an empty name.
    if ( $name === $ext ) {
        $name = '';
    }
 
    /*
     * Increment the file number until we have a unique file to save in $dir.
     * Use callback if supplied.
     */
    if ( $unique_filename_callback && is_callable( $unique_filename_callback ) ) {
        $filename = call_user_func( $unique_filename_callback, $dir, $name, $ext );
    } else {
        $number = '';
 
        // Change '.ext' to lower case.
        if ( $ext && Tools::strtolower($ext) != $ext ) {
            $ext2 = Tools::strtolower($ext);
            $filename2 = preg_replace( '|' . preg_quote($ext) . '$|', $ext2, $filename );
 
            // Check for both lower and upper case extension or image sub-sizes may be overwritten.
            while ( file_exists($dir . "/$filename") || file_exists($dir . "/$filename2") ) {
                $new_number = (int) $number + 1;
                $filename = str_replace( array( "-$number$ext", "$number$ext" ), "-$new_number$ext", $filename );
                $filename2 = str_replace( array( "-$number$ext2", "$number$ext2" ), "-$new_number$ext2", $filename2 );
                $number = $new_number;
            }
            return $filename2;
        }
 
        while ( file_exists( $dir . "/$filename" ) ) {
            $new_number = (int) $number + 1;
            if ( '' == "$number$ext" ) {
                $filename = "$filename-" . $new_number;
            } else {
                $filename = str_replace( array( "-$number$ext", "$number$ext" ), "-" . $new_number . $ext, $filename );
            }
            $number = $new_number;
        }
    }
 
    /** This filter is documented in wp-includes/functions.php */
    return $filename;
}
function etscf7_recaptcha_noscript( $args = '' ) {
	$args = ets_parse_args( $args, array(
		'sitekey' => '',
	) );

	if ( empty( $args['sitekey'] ) ) {
		return;
	}

}
function etscf7_recaptcha_check_with_google( $spam ) {

	$contact_form = etscf7_get_current_contact_form();

	if ( ! $contact_form ) {
		return $spam;
	}

	$tags = $contact_form->scan_form_tags( array( 'type' => 'recaptcha' ) );

	if ( empty( $tags ) ) {
		return $spam;
	}

	$recaptcha = WPCF7_RECAPTCHA::get_instance();

	if ( ! $recaptcha->is_active() || !$recaptcha->isEnableRecaptcha() ) {
		return $spam;
	}

	$response_token = Ets_contactform7::recaptcha_response();
	$spam = ! $recaptcha->verify( $response_token );

	return $spam;
}
function ets_wpautop( $pee, $br = true ) {
    $pre_tags = array();
 
    if ( trim($pee) === '' )
        return '';
 
    // Just to make things a little easier, pad the end.
    $pee = $pee . "\n";
 
    /*
     * Pre tags shouldn't be touched by autop.
     * Replace pre tags with placeholders and bring them back after autop.
     */
    if ( strpos($pee, '<pre') !== false ) {
        $pee_parts = explode( '</pre>', $pee );
        $last_pee = array_pop($pee_parts);
        $pee = '';
        $i = 0;
 
        foreach ( $pee_parts as $pee_part ) {
            $start = strpos($pee_part, '<pre');
 
            // Malformed html?
            if ( $start === false ) {
                $pee .= $pee_part;
                continue;
            }
 
            $name = "<pre wp-pre-tag-$i></pre>";
            $pre_tags[$name] = Tools::substr( $pee_part, $start ) . '</pre>';
 
            $pee .= Tools::substr( $pee_part, 0, $start ) . $name;
            $i++;
        }
 
        $pee .= $last_pee;
    }
    // Change multiple <br>s into two line breaks, which will turn into paragraphs.
    $pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);
 
    $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
 
    // Add a double line break above block-level opening tags.
    $pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee);
 
    // Add a double line break below block-level closing tags.
    $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
 
    // Standardize newline characters to "\n".
    $pee = str_replace(array("\r\n", "\r"), "\n", $pee);
 
    // Find newlines in all elements and add placeholders.
    $pee = ets_replace_in_html_tags( $pee, array( "\n" => " <!-- wpnl --> " ) );
 
    // Collapse line breaks before and after <option> elements so they don't get autop'd.
    if ( strpos( $pee, '<option' ) !== false ) {
        $pee = preg_replace( '|\s*<option|', '<option', $pee );
        $pee = preg_replace( '|</option>\s*|', '</option>', $pee );
    }
 
    /*
     * Collapse line breaks inside <object> elements, before <param> and <embed> elements
     * so they don't get autop'd.
     */
    if ( strpos( $pee, '</object>' ) !== false ) {
        $pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
        $pee = preg_replace( '|\s*</object>|', '</object>', $pee );
        $pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
    }
 
    /*
     * Collapse line breaks inside <audio> and <video> elements,
     * before and after <source> and <track> elements.
     */
    if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
        $pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
        $pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
        $pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
    }
 
    // Collapse line breaks before and after <figcaption> elements.
    if ( strpos( $pee, '<figcaption' ) !== false ) {
        $pee = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $pee );
        $pee = preg_replace( '|</figcaption>\s*|', '</figcaption>', $pee );
    }
 
    // Remove more than two contiguous line breaks.
    $pee = preg_replace("/\n\n+/", "\n\n", $pee);
 
    // Split up the contents into an array of strings, separated by double line breaks.
    $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
 
    // Reset $pee prior to rebuilding.
    $pee = '';
 
    // Rebuild the content as a string, wrapping every bit with a <p>.
    foreach ( $pees as $tinkle ) {
        $pee .= Module::getInstanceByName('ets_contactform7')->displayText(trim($tinkle, "\n"),'p','');
    }
 
    // Under certain strange conditions it could create a P of entirely whitespace.
    $pee = preg_replace('|<p>\s*</p>|', '', $pee);
 
    // Add a closing <p> inside <div>, <address>, or <form> tag if missing.
    $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
 
    // If an opening or closing block element tag is wrapped in a <p>, unwrap it.
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
 
    // In some cases <li> may get wrapped in <p>, fix them.
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);
 
    // If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
 
    // If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
 
    // If an opening or closing block element tag is followed by a closing <p> tag, remove it.
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
 
    // Optionally insert line breaks.
    if ( $br ) {
        // Replace newlines that shouldn't be touched with a placeholder.
        $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);
 
        // Normalize <br>
        $pee = str_replace( array( Module::getInstanceByName('ets_contactform7')->displayText('','br',''), Module::getInstanceByName('ets_contactform7')->displayText('','br','') ), Module::getInstanceByName('ets_contactform7')->displayText('','br',''), $pee );
 
        // Replace any new line characters that aren't preceded by a <br /> with a <br />.
        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
 
        // Replace newline placeholders with newlines.
        $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
    }
 
    // If a <br /> tag is after an opening or closing block tag, remove it.
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
 
    // If a <br /> tag is before a subset of opening or closing block tags, remove it.
    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
    $pee = preg_replace( "|\n</p>$|", '</p>', $pee );
 
    // Replace placeholder <pre> tags with their original content.
    if ( !empty($pre_tags) )
        $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);
 
    // Restore newlines in all elements.
    if ( false !== strpos( $pee, '<!-- wpnl -->' ) ) {
        $pee = str_replace( array( ' <!-- wpnl --> ', '<!-- wpnl -->' ), "\n", $pee );
    }
 
    return $pee;
}
function ets_replace_in_html_tags( $haystack, $replace_pairs ) {
    // Find all elements.
    $textarr = ets_html_split( $haystack );
    $changed = false;
 
    // Optimize when searching for one item.
    if ( 1 === count( $replace_pairs ) ) {
        // Extract $needle and $replace.
        foreach ( $replace_pairs as $needle => $replace );
 
        // Loop through delimiters (elements) only.
        for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
            if ( false !== strpos( $textarr[$i], $needle ) ) {
                $textarr[$i] = str_replace( $needle, $replace, $textarr[$i] );
                $changed = true;
            }
        }
    } else {
        // Extract all $needles.
        $needles = array_keys( $replace_pairs );
 
        // Loop through delimiters (elements) only.
        for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
            foreach ( $needles as $needle ) {
                if ( false !== strpos( $textarr[$i], $needle ) ) {
                    $textarr[$i] = strtr( $textarr[$i], $replace_pairs );
                    $changed = true;
                    // After one strtr() break out of the foreach loop and look at next element.
                    break;
                }
            }
        }
    }
 
    if ( $changed ) {
        $haystack = implode( $textarr );
    }
 
    return $haystack;
}
function ets_html_split( $input ) {
    return preg_split( ets_get_html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
}
function ets_get_html_split_regex() {
    static $regex;
 
    if ( ! isset( $regex ) ) {
        $comments =
              '!'           // Start of comment, after the <.
            . '(?:'         // Unroll the loop: Consume everything until --> is found.
            .     '-(?!->)' // Dash not followed by end of comment.
            .     '[^\-]*+' // Consume non-dashes.
            . ')*+'         // Loop possessively.
            . '(?:-->)?';   // End of comment. If not found, match all input.
 
        $cdata =
              '!\[CDATA\['  // Start of comment, after the <.
            . '[^\]]*+'     // Consume non-].
            . '(?:'         // Unroll the loop: Consume everything until ]]> is found.
            .     '](?!]>)' // One ] not followed by end of comment.
            .     '[^\]]*+' // Consume non-].
            . ')*+'         // Loop possessively.
            . '(?:]]>)?';   // End of comment. If not found, match all input.
 
        $escaped =
              '(?='           // Is the element escaped?
            .    '!--'
            . '|'
            .    '!\[CDATA\['
            . ')'
            . '(?(?=!-)'      // If yes, which type?
            .     $comments
            . '|'
            .     $cdata
            . ')';
 
        $regex =
              '/('              // Capture the entire match.
            .     '<'           // Find start of element.
            .     '(?'          // Conditional expression follows.
            .         $escaped  // Find end of escaped element.
            .     '|'           // ... else ...
            .         '[^>]*>?' // Find end of normal element.
            .     ')'
            . ')/';
    }
    return $regex;
}
function _autop_newline_preservation_helper( $matches ) {
    return str_replace( "\n", "<WPPreserveNewline />", $matches[0] );
}
function ets_hash($data, $scheme = 'auth') {
    $salt = _COOKIE_KEY_;
    unset($scheme);
    return $data? md5($salt.$data):'';
}
function ets_mysql2date( $format, $date, $translate = true ) {
    if ( empty( $date ) )
        return false;
    unset($translate);
    if ( 'G' == $format )
        return strtotime( $date . ' +0000' );
 
    $i = strtotime( $date );
 
    if ( 'U' == $format )
        return $i;
    return date( $format, $i );
}
function etscf7_acceptance_mail_tag( $replaced, $submitted, $html, $mail_tag ) {
	$form_tag = $mail_tag->corresponding_form_tag();

	if ( ! $form_tag ) {
		return $replaced;
	}

	if ( ! empty( $submitted ) ) {
		$replaced = 'Consented';
	} else {
		$replaced = 'Not consented';
	}

	$content = empty( $form_tag->content )
		? (string) reset( $form_tag->values )
		: $form_tag->content;

	if ( ! $html ) {
		$content = ets_strip_all_tags( $content );
	}

	$content = trim( $content );

	if ( $content ) {
		/* translators: 1: 'Consented' or 'Not consented', 2: conditions */
		$replaced = sprintf('%1$s: %2$s mail output for acceptance checkboxes',
			$replaced,
			$content );
	}

	return $replaced;
}