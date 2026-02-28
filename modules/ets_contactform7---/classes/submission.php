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
 
class WPCF7_Submission {
	private static $instance;
	/** @var WPCF7_ContactForm */
	private $contact_form;
	private $status = 'init';
	private $posted_data = array();
	private $uploaded_files = array();
	private $skip_mail = false;
	private $response = '';
	private $invalid_fields = array();
	private $meta = array();
	private $consent = array();
    private $attachments = array();
	private function __construct() {}
	public static function get_instance( WPCF7_ContactForm $contact_form = null, $args = array() ) {
		$args = array_merge( $args, array(
			'skip_mail' => false,
		));
		if ( empty( self::$instance ) ) {
			if ( null == $contact_form ) {
				return null;
			}
			self::$instance = new self;
			self::$instance->contact_form = $contact_form;
			self::$instance->skip_mail = (bool) $args['skip_mail'];
			self::$instance->setup_posted_data();
			self::$instance->submit($contact_form->id,$contact_form->unit_tag);
		} elseif ( null != $contact_form ) {
			return null;
		}
		return self::$instance;
	}
	public static function is_restful() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}
	public function get_status() {
		return $this->status;
	}
	public function set_status( $status ) {
		if ( preg_match( '/^[a-z][0-9a-z_]+$/', $status ) ) {
			$this->status = $status;
			return true;
		}
		return false;
	}
	public function is( $status ) {
		return $this->status == $status;
	}
	public function get_response() {
		return $this->response;
	}
	public function set_response( $response ) {
		$this->response = $response;
		return true;
	}
	public function get_contact_form() {
		return $this->contact_form;
	}
	public function get_invalid_field( $name ) {
		if ( isset( $this->invalid_fields[$name] ) ) {
			return $this->invalid_fields[$name];
		} else {
			return false;
		}
	}
	public function get_invalid_fields() {
		return $this->invalid_fields;
	}
	public function get_posted_data( $name = '' ) {
		if ( ! empty( $name ) ) {
			if ( isset( $this->posted_data[$name] ) ) {
				return $this->posted_data[$name];
			} else {
				return null;
			}
		}
		return $this->posted_data;
	}
	private function setup_posted_data() {
		$posted_data = (array) $_POST;
		$posted_data = array_diff_key( $posted_data, array( '_wpnonce' => '' ) );
        
		$posted_data = $this->sanitize_posted_data( $posted_data );
        
		$tags = $this->contact_form->scan_form_tags();
		foreach ( (array) $tags as $tag ) {
			if ( empty( $tag->name ) ) {
				continue;
			}
			$name = $tag->name;
			$pipes = $tag->pipes;
			$value_orig = $value = '';
            
			if ( isset( $posted_data[$name] ) ) {
				$value_orig = $value = $posted_data[$name];
			}
			if ($pipes instanceof WPCF7_Pipes
			&& ! $pipes->zero() ) {
				if ( is_array( $value_orig ) ) {
					$value = array();
					foreach ( $value_orig as $v ) {
						$value[] = $pipes->do_pipe( ets_unslash( $v ) );
					}
				} else {
					$value = $pipes->do_pipe( ets_unslash( $value_orig ) );
				}
			}
			$posted_data[$name] = $value;
		}
		$this->posted_data = $posted_data;;
		return $this->posted_data;
	}
	private function sanitize_posted_data( $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( array( $this, 'sanitize_posted_data' ), $value );
		} elseif ( is_string( $value ) ) {
			$value = ets_check_invalid_utf8( $value );
			$value = ets_kses_no_null( $value );
		}
		return $value;
	}

    public function ipBlackList($ip_blacklist)
    {
        if (!$ip_blacklist)
            return false;
        $remote_addr = Tools::getRemoteAddr();
        $ips = explode("\n", $ip_blacklist);
        if ($ips) {
            foreach ($ips as $ip) {
                if (preg_match('/^' . $this->formatPattern($ip) . '$/', $remote_addr)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function formatPattern($pattern)
    {
        return str_replace('*', '(.*)', trim($pattern));
    }
	private function submit($id_contact,$unit_tag) {
		if ( ! $this->is( 'init' ) ) {
			return $this->status;
		}

		$this->meta = array(
			'remote_ip' => $this->get_remote_ip_addr(),
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] )
				? Tools::substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '',
			'url' => $this->get_request_url(),
			'timestamp' => ets_current_time( 'timestamp' ),
			'unit_tag' => $unit_tag && Validate::isCleanHtml($unit_tag) ? $unit_tag : '',
			'container_post_id' => $id_contact
				? (int) $id_contact : 0,
			'current_user_id' => (int)Context::getContext()->customer->id,
		);
		$contact_form = $this->contact_form;

        if($this->ipBlackList(Configuration::get('ETS_CTF7_IP_BLACK_LIST')))
        {
            $this->set_status( 'validation_failed' );
			$this->set_response($contact_form->message('ip_black_list'));
        }
		elseif ( ! $this->validate() ) { // Validation error occured
			$this->set_status( 'validation_failed' );
			$this->set_response( $contact_form->message( 'validation_error' ) );
		} elseif ( ! $this->accepted() ) { // Not accepted terms
			$this->set_status( 'acceptance_missing' );
			$this->set_response( $contact_form->message( 'accept_terms' ) );
		} elseif ( $this->spam() ) { // Spam!
			$this->set_status( 'spam' );
			$this->set_response( $contact_form->message( 'spam' ) );
		} elseif ( ($send_mail=$this->mail())===true ) {
            if ($contact_form ->thank_you_active){
                if ( trim($contact_form->thank_you_page) =='thank_page_url'){
                    $this->set_status('mail_redirect');
                    $this->set_response($contact_form->message('thank_you_url'));
                }else{
                    $this->set_status('load_thank_page');
                    $base_url = Ets_contactform7::getLinkContactForm($contact_form->id,(int)Context::getContext()->language->id,'thank');
                    $base_url .='thank/'.$contact_form->thank_you_alias;
                    $this->set_response($base_url);
                }
            }else{
                $this->set_status( 'mail_sent' );
			    $this->set_response( $contact_form->message( 'mail_sent_ok' ) );
            }
		} else {
			$this->set_status( 'mail_failed' );
            if($send_mail===false)
			     $this->set_response( $contact_form->message( 'mail_sent_ng' ) );
            elseif($send_mail==-1)
                $this->set_response('Invalid mail to');
            elseif($send_mail==-2)
                $this->set_response( 'Invalid e-mail subject' );   
		}
        if(!$contact_form->save_message)
		      $this->remove_uploaded_files();
		return $this->status;
	}
	private function get_remote_ip_addr() {
		$ip_addr = '';

		if ( isset( $_SERVER['REMOTE_ADDR'] )
		&& $_SERVER['REMOTE_ADDR']) {
			$ip_addr = $_SERVER['REMOTE_ADDR'];
		}
		return $ip_addr;
	}
	private function get_request_url() {
		$home_url = ets_untrailingslashit( Context::getContext()->link->getPageLink('index'));
		if ( self::is_restful() ) {
			$referer = isset( $_SERVER['HTTP_REFERER'] )
				? trim( $_SERVER['HTTP_REFERER'] ) : '';
			if ( $referer && 0 === strpos( $referer, $home_url ) ) {
				return ets_esc_url_raw( $referer );
			}
		}
		$url = preg_replace( '%(?<!:|/)/.*$%', '', $home_url )
			. etscf7_get_request_uri();
		return $url;
	}
    private function validate() {
		if ( $this->invalid_fields ) {
			return false;
		}
		require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/validation.php');
		$result = new WPCF7_Validation();
		$tags = $this->contact_form->scan_form_tags();
		foreach ( $tags as $tag ) {
			$type = str_replace('*','',$tag->type);
            if($type=='radio')
                $type='checkbox';
            if($type=='range')
                $type='number';
            $func= $type.'_validation_filter';
			if(method_exists('Ets_contactform7',$func))
                $result = Ets_contactform7::{$func}($result,$tag);
            else
                $result =  Ets_contactform7::text_validation_filter($result,$tag);
		}
		$this->invalid_fields = $result->get_invalid_fields();
		return $result->is_valid();
	}
	private function accepted() {
		return true;
	}
	public function add_consent( $name, $conditions ) {
		$this->consent[$name] = $conditions;
		return true;
	}
	public function collect_consent() {
		return (array) $this->consent;
	}
	private function spam() {
		return etscf7_recaptcha_check_with_google(false);
	}
	private function is_blacklisted() {
		$target = etscf7_array_flatten( $this->posted_data );
		$target[] = $this->get_meta( 'remote_ip' );
		$target[] = $this->get_meta( 'user_agent' );
		$target = implode( "\n", $target );
		return (bool) etscf7_blacklist_check( $target );
	}
	private function mail() {
		$contact_form = $this->contact_form;
		$result = WPCF7_Mail::send( $contact_form->prop( 'mail' ), 'mail',true );
		if ( $result===true ) {
			$additional_mail = array();
			if (( $mail_2 = $contact_form->prop( 'mail_2' ) ) && $mail_2['active'] ) {
				$additional_mail['mail_2'] = $mail_2;
			}
			foreach ( $additional_mail as $name => $template ) {
				WPCF7_Mail::send($template, $name,false);
			}
            WPCF7_Mail::deleteFileNotUse( $contact_form->prop( 'mail' ), 'mail');
			return true;
		}
        WPCF7_Mail::deleteFileNotUse( $contact_form->prop( 'mail' ), 'mail');
		return $result;
	}
	public function uploaded_files() {
		return $this->uploaded_files;
	}
    public function attachments()
    {
        return $this->attachments;
    }
	public function add_uploaded_file( $name, $file_path,$attachment ) {
		$this->uploaded_files[$name] = $file_path;
        $this->attachments[$name]=$attachment;
		if ( empty( $this->posted_data[$name] ) ) {
			$this->posted_data[$name] = basename( $file_path );
		}
	}
	public function remove_uploaded_files() {
		foreach ( (array) $this->uploaded_files as $path ) {
			etscf7_rmdir_p( $path );
			if ( ( $dir = dirname( $path ) )
			&& false !== ( $files = scandir( $dir ) )
			&& ! array_diff( $files, array( '.', '..' ) ) ) {
				// remove parent dir if it's empty.
				rmdir( $dir );
			}
		}
	}
	public function get_meta( $name ) {
		if ( isset( $this->meta[$name] ) ) {
			return $this->meta[$name];
		}
	}
}