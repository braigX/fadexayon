<?php
defined( '_PS_VERSION_' ) or exit;
require_once _PS_MODULE_DIR_ . 'crazyelements/PrestaHelper.php';
require_once _PS_MODULE_DIR_ . 'crazyelements/includes/plugin.php';
use CrazyElements\PrestaHelper;
class AdminCrazySettingController extends AdminController {


	public $dirpaths                     = array();
	public $json_file_name               = '';
	public $svg_file_name                = '';
	public $new_json                     = array();
	public $custom_icon_upload_font_name = '';
	public $text_file_name               = 'fontarray.txt';
	public $new_json_file_name           = 'fontarray.json';
	public $folder_name                  = '';
	public $first_icon_name              = '';
	public function __construct() {
		$this->context   = Context::getContext();
		$this->bootstrap = true;
		$this->table     = 'configuration';
		parent::__construct();
	}

	public function initContent() {
		PrestaHelper::get_lience_expired_date();
		$license_status = PrestaHelper::get_option( 'ce_licence_status', 'invalid' );

		$this->crzy_update_siteb_fileds();

		if ( Tools::isSubmit( 'crazy_home_settings' ) ) {

			$shop_id = $this->context->shop->id;
			$lang_id = $this->context->language->id;
			$isMultiShopActive = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
			$crazy_home_layout = '';
			if($isMultiShopActive){
				$crazy_home_layout = Tools::getValue( 'crazy_home_layout_'.$shop_id.'_'.$lang_id );
				PrestaHelper::update_option( 'crazy_home_layout_'.$shop_id.'_'.$lang_id, $crazy_home_layout );
			}else{
				$crazy_home_layout = Tools::getValue( 'crazy_home_layout' );
				PrestaHelper::update_option( 'crazy_home_layout', $crazy_home_layout );
			}
			
			$remove_display_home_hook = Tools::getValue( 'remove_display_home_hook' );
			if ( $remove_display_home_hook == '1' ) {
				$hookid = Hook::getIdByName('displayHome');
				$moduleslist = Hook::getModulesFromHook($hookid);
				
				foreach($moduleslist as $module){
					$mod_ins = Module::getInstanceByName( trim($module['name']) );
					$mod_ins->unregisterHook('displayHome');
				}
			}
			if($license_status == "valid"){
				$crazy_header_layout = Tools::getValue( 'crazy_header_layout_'.$shop_id );
				PrestaHelper::update_option( 'crazy_header_layout_'.$shop_id, $crazy_header_layout );

				$crazy_footer_layout = Tools::getValue( 'crazy_footer_layout_'.$shop_id );
				PrestaHelper::update_option( 'crazy_footer_layout_'.$shop_id, $crazy_footer_layout );

				$crazy_footer_layout = Tools::getValue( 'crazy_404_layout_'.$shop_id );
				PrestaHelper::update_option( 'crazy_404_layout_'.$shop_id, $crazy_footer_layout );
			}

			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		if($license_status == "valid"){
			
			if ( Tools::isSubmit( 'crazy_cust_hook' ) ) {
				$cust_hook_name = Tools::getValue( 'cust_hook_name' );
				$cust_modules_rewrite = Tools::getValue( 'cust_modules_rewrite' );
				$remove_hook = Tools::getValue( 'remove_hook' );
				$custom_hooks = PrestaHelper::get_option( 'crazy_custom_hooks' );
				$custom_hooks = json_decode( $custom_hooks, true );
				if(isset($remove_hook)){
					foreach($remove_hook as $hook){
						unset($custom_hooks[$hook]);
					}
				}
				if($cust_hook_name != ''){				
					$custom_hooks[$cust_hook_name] = $cust_modules_rewrite;
					$custom_hooks = json_encode( $custom_hooks );
				}
				PrestaHelper::update_option( 'crazy_custom_hooks',  $custom_hooks);
				Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
			}
		}
		
		if ( Tools::isSubmit( 'page_title' ) ) {
			$page_title = Tools::getValue( 'page_title' );
			PrestaHelper::update_option( 'page_title', $page_title );
			$presta_editor_enable = Tools::getValue( 'presta_editor_enable' );
			if ( $presta_editor_enable == '1' ) {
				$presta_editor_enable = 'yes';
			} else {
				$presta_editor_enable = 'no';
			}
			PrestaHelper::update_option( 'presta_editor_enable', $presta_editor_enable );
			if($license_status == "valid"){
				$crazy_content_disable = Tools::getValue( 'crazy_content_disable' );
				if ( $crazy_content_disable == '1' ) {
					$crazy_content_disable = 'yes';
				} else {
					$crazy_content_disable = 'no';
					PrestaHelper::update_option( 'specific_page_disable', '' );
				}
				$specific_page = Tools::getValue( 'specific_page' );
				$specific_page = json_encode( $specific_page );
				PrestaHelper::update_option( 'specific_page_disable', $specific_page );
				PrestaHelper::update_option( 'crazy_content_disable', $crazy_content_disable );
			}
		}
		if ( Tools::isSubmit( 'license_data' ) ) {
			$license_data = Tools::getValue( 'license_data' );

			if ( $license_data == '' ) {
				PrestaHelper::update_option( 'ce_licence_status', 'false' );
			}
			PrestaHelper::update_option( 'ce_licence_date', '' );
			PrestaHelper::update_option( 'ce_licence', $license_data );
			PrestaHelper::get_lience( $license_data );
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		if ( Tools::isSubmit( 'check_update' ) ) {
			$cookie = new Cookie( 'check_update' );
			if ( isset( $cookie->check_update ) ) {
				unset($cookie->check_update);
			}
			
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		if ( Tools::isSubmit( 'license_refresh' ) ) {
			$license_data = Tools::getValue( 'license_data_deactivate' );
			PrestaHelper::refresh_licence( $license_data );
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		if ( Tools::isSubmit( 'license_data_deactivate' ) ) {
			$license_data = Tools::getValue( 'license_data_deactivate' );
			PrestaHelper::deactivated_licence( $license_data );
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		
		if ( Tools::isSubmit( 'mailchimp_data' ) ) {
			$mailchimp_data = Tools::getValue( 'mailchimp_data' );
			PrestaHelper::update_option( 'mailchimp_data', $mailchimp_data );
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
		}
		if ( Tools::isSubmit( 'crazy_clear_cache' ) ) {
			$crazy_clear_cache = Tools::getValue( 'crazy_clear_cache' );
			if ( $crazy_clear_cache ) {
				$this->clear_cache();
				Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazySetting' ) );
			}
		}
		
		if ( Tools::isSubmit( 'crazy_old_url' ) && Tools::isSubmit( 'crazy_new_url' ) ) {
			$from = ! empty( Tools::getValue( 'crazy_old_url' ) ) ? Tools::getValue( 'crazy_old_url' ) : '';
			$to   = ! empty( Tools::getValue( 'crazy_new_url' ) ) ? Tools::getValue( 'crazy_new_url' ) : '';
			$this->replace_urls( $from, $to );
			$this->clear_cache();
		}
		parent::initContent();
	}

	public function initHeader() {
		parent::initHeader();
	}

	public function clear_cache() {
		$files = glob( _PS_MODULE_DIR_ . 'crazyelements/assets/css/frontend/css/*' ); // get all file names
		foreach ( $files as $file ) { // iterate files
			if ( is_file( $file ) ) {
				unlink( $file ); // delete file
			}
		}
		Db::getInstance()->delete( 'crazy_options', "option_name like '_elementor_css%'  OR option_name ='_elementor_global_css'" );
		Db::getInstance()->delete( 'crazy_options', "option_name ='elementor_remote_info_library'" );
		$admincontroller = Tools::getValue( 'controller' );
		$token           = Tools::getValue( 'token' );
		Configuration::updateValue( 'crazy_clear_cache', 0 );
	}

	public function replace_urls( $from, $to ) {
		if ( $from === $to ) {
			throw new \Exception( PrestaHelper::__( 'The `from` and `to` URL\'s must be different', 'elementor' ) );
		}
		$is_valid_urls = ( filter_var( $from, FILTER_VALIDATE_URL ) && filter_var( $to, FILTER_VALIDATE_URL ) );
		if ( ! $is_valid_urls ) {
			throw new \Exception( PrestaHelper::__( 'The `from` and `to` URL\'s must be valid URL\'s', 'elementor' ) );
		}
		Db::getInstance()->update(
			'crazy_content_lang',
			array(
				'resource' => array(
					'type'  => 'sql',
					'value' => "REPLACE(`resource`, '" . str_replace(
						'/',
						'\\\/',
						$from
					) . "','" . str_replace(
						'/',
						'\\\/',
						$to
					) . "')",
				),
			),
			"`resource` LIKE '[%' "
		);
	}
	private function crzy_update_siteb_fileds(){	
		$shop_id = $this->context->shop->id;
		$lang_id = $this->context->language->id;

		$sitb_fixed = PrestaHelper::get_option( "crazy_sitebfields_fix_".$shop_id, 0 );
		if(!$sitb_fixed){
			$hnamefield = "crazy_header_layout_" .$shop_id;
			$fnamefield = "crazy_footer_layout_" .$shop_id;
			$fzfnamefield = "crazy_404_layout_" .$shop_id;
	
			$hnameval = PrestaHelper::get_option( "crazy_header_layout_" .$shop_id . '_' . $lang_id, 0 );
			$fnameval = PrestaHelper::get_option( "crazy_footer_layout_" .$shop_id . '_' . $lang_id, 0 );
			$fzfnameval = PrestaHelper::get_option( "crazy_404_layout_" .$shop_id . '_' . $lang_id, 0 );
	
			if($hnameval!=0){
				PrestaHelper::update_option( 'crazy_header_layout_'.$shop_id, $hnameval );
			}
	
			if($fnameval!=0){
				PrestaHelper::update_option( 'crazy_footer_layout_'.$shop_id, $fnameval );
			}
	
			if($fzfnameval!=0){
				PrestaHelper::update_option( 'crazy_404_layout_'.$shop_id, $fzfnameval );
			}
	
			PrestaHelper::delete_option_without_id("crazy_header_layout_" .$shop_id . '_' . $lang_id);
			PrestaHelper::delete_option_without_id("crazy_footer_layout_" .$shop_id . '_' . $lang_id);
			PrestaHelper::delete_option_without_id("crazy_404_layout_" .$shop_id . '_' . $lang_id);

			PrestaHelper::update_option( 'crazy_sitebfields_fix_'.$shop_id, 1 );
		}
	}
	public function renderList() {

		$crazy_license = PrestaHelper::get_option( 'ce_licence', '' );
		$license_status = PrestaHelper::get_option( 'ce_licence_status', 'invalid' );
		

		$license_form_html = $this->crazy_licence_form($crazy_license, $license_status);
		$general_settings_html = $this->crazy_general_settings_form($crazy_license, $license_status);
		$layout_settings_html = $this->crazy_layout_settings_form();
		$cust_hook_settings_html = $this->crazy_cust_hook_settings_form($crazy_license, $license_status);
		$replace_url_settings_html = $this->crazy_replace_url_settings_form();
		$clear_cache_settings_html = $this->crazy_clear_cache_settings_form();
		$mailchimp_settings_html = $this->crazy_mailchimp_settings_form();


		$fromhtml = '
		<div class="double_section">
			'.$license_form_html
			.$general_settings_html.'
		</div>
		'
		.$layout_settings_html
		.$cust_hook_settings_html
		.$replace_url_settings_html
		.'
		<div class="double_section">
        	'.$clear_cache_settings_html
			.$mailchimp_settings_html.'
        </div>';
		$html     = parent::renderList() . $fromhtml;
		return $html;
	}

	public function crazy_licence_form($license, $license_status){
		$validity_msg    = '';
		$info_msg = '';
		$license_bt      = 'Activate License';
		$ce_license_name = 'license_data';
		$tag_text        = 'Deactivated';
		$tag_class       = 'crazy-licence-deactive';
		$activated_msg = '';
		if($license == ''){
			$validity_msg = '
			<div class="help-block"> Enter Your License.
				<a class="get-prod-bt" href="https://classydevs.com/elementor-prestashop-page-builder/pricing/?utm_source=backofc_licnse&utm_medium=backofc_licnse&utm_campaign=backofc_licnse&utm_id=backofc_licnse&utm_term=backofc_licnse&utm_content=backofc_licnse" target="_blank"> Click Here </a> To Get A Valid License Key
			</div>';
		}else{
			if ( $license_status != 'valid' ) {
				if($license_status == 'expired'){
					$tag_text        = 'Expired';
					$validity_msg = '
					<div class="error-block"> Your License Key is Expired. Click Here. 
						<a href="https://classydevs.com/elementor-prestashop-page-builder/pricing/?utm_source=backofc_licnse&utm_medium=backofc_licnse&utm_campaign=backofc_licnse&utm_id=backofc_licnse&utm_term=backofc_licnse&utm_content=backofc_licnse" target="_blank" class="get-prod-bt"> to Get A New License. </a>
					</div>';
					
				}else{
					$validity_msg = '
					<div class="error-block"> Your License Key is Invalid. Please Enter Valid License Key 
						<a href="https://classydevs.com/elementor-prestashop-page-builder/pricing/?utm_source=backofc_licnse&utm_medium=backofc_licnse&utm_campaign=backofc_licnse&utm_id=backofc_licnse&utm_term=backofc_licnse&utm_content=backofc_licnse" target="_blank" class="get-prod-bt"> Click Here </a> To Get A Valid License Key
					</div>';
				}
			}else{
				$expirydate = PrestaHelper::get_option( 'ce_licence_expires');
				$today = date("Y-m-d H:i:s"); 
				$cookie = new Cookie( 'check_update' );
				$cookie_version = $cookie->check_update;
				if(!isset($cookie_version) || $cookie_version == false){
					$cookie_version = CRAZY_VERSION;
				}
				$d_link = PrestaHelper::get_option( 'ce_new_v' );
				if($expirydate == 'lifetime'){
					$expiration_msg = "<span id='has_time'>You Have Lifetime License</span>";
				}else{
					$expirydate = date_create($expirydate);
					$today = date_create($today);
					$diff=date_diff($expirydate,$today);
					$expiration_msg = '';
					if($diff->days > 30){
						$expiration_msg = "<span id='has_time'>You need to renew your license in " . $diff->m . ' months and ' . $diff->d . ($diff->d>1 ? ' days' : " day"). '</span> <a class="how-renew" href="https://classydevs.com/docs/general-knowledgebase/licensing/how-to-renew-license/?utm_source=crazypro_bckoffice&utm_medium=crazypro_bckoffice&utm_campaign=crazypro_bckoffice&utm_id=crazypro_bckoffice&utm_term=crazypro_bckoffice&utm_content=crazypro_bckoffice" target="_blank"> How to renew?</a>';
					}elseif($diff->days < 30 && $diff->days > 0){
						$expiration_msg = "<span id='less_one_m'>You have only " . $diff->d . ($diff->d>1 ? ' days' : " day") . ' to renew your license.</span> <a class="how-renew" href="https://classydevs.com/docs/general-knowledgebase/licensing/how-to-renew-license/?utm_source=crazypro_bckoffice&utm_medium=crazypro_bckoffice&utm_campaign=crazypro_bckoffice&utm_id=crazypro_bckoffice&utm_term=crazypro_bckoffice&utm_content=crazypro_bckoffice" target="_blank"> How to renew?</a>';
					}else{
						if($diff->h > 0){
							$expiration_msg = '<span id="less_one_m">Your License Will Expire Tomorrow.</span><a class="how-renew" href="https://classydevs.com/docs/general-knowledgebase/licensing/how-to-renew-license/?utm_source=crazypro_bckoffice&utm_medium=crazypro_bckoffice&utm_campaign=crazypro_bckoffice&utm_id=crazypro_bckoffice&utm_term=crazypro_bckoffice&utm_content=crazypro_bckoffice" target="_blank"> How to renew?</a>';
						}else{
							$expiration_msg = '<span id="less_one_m">Your License Has Expired. Please Renew Your License</span><a class="how-renew" href="https://classydevs.com/docs/general-knowledgebase/licensing/how-to-renew-license/?utm_source=crazypro_bckoffice&utm_medium=crazypro_bckoffice&utm_campaign=crazypro_bckoffice&utm_id=crazypro_bckoffice&utm_term=crazypro_bckoffice&utm_content=crazypro_bckoffice" target="_blank"> How to renew?</a>';
						}
						
					}
				}
				$validity_msg    = '<div class="success-block"> Your License is Activated </div>';
				$validity_msg    .= '<div class="success-block">'.$expiration_msg.'</div>';
				$info_msg = '<div class="col-lg-9 col-lg-offset-3 module-info"> Installed Version : ' . CRAZY_VERSION . '</div>
				<div class="col-lg-9 col-lg-offset-3 module-info"> Available Version : ' . $cookie_version . '<button type="submit" class="btn btn-default check-update-bt" name="check_update"><i class="process-icon-refresh icon-check-update"></i>Check Update
				</button></div>
				<div class="col-lg-9 col-lg-offset-3 module-info"> <a href="https://classydevs.com/docs/crazy-elements/?utm_source=crazylicsec&utm_medium=crazylicsec&utm_campaign=crazylicsec&utm_id=crazylicsec&utm_term=crazylicsec&utm_content=crazylicsec" target="_blank">Check Documentation</a></div>
				<div class="col-lg-9 col-lg-offset-3 module-info"> <a href="https://support.classydevs.com/" target="_blank">Get Support</a></div>';
				
				$license_bt      = 'Deactivate License';
				$ce_license_name = 'license_data_deactivate';
				$tag_text        = 'Activated';
				$tag_class       = 'crazy-licence-active';
			}
		}
		return '
		<form action="" id="configuration_form_3" method="post" enctype="multipart/form-data" class="form-horizontal"> 
        	<div class="panel ce_licence_panel" id="configuration_fieldset_license"> 
				<div class="panel-heading"> 
					<i class="icon-cogs"></i> 
					Enter License
					<div class="crazy-licence-status-area">
						<span class="crazy-licence-status ' . $tag_class . '">' . $tag_text . '</span>
					</div>
        		</div>
				<div class="form-wrapper"> 
					<div class="form-group"> 
						<div id="conf_id_license_data"> 
							<label class="control-label col-lg-3"> License </label> 
							<div class="col-lg-9">
								<input class="form-control " type="text" size="5" name="' . $ce_license_name . '" value="' . PrestaHelper::get_option( 'ce_licence' ) . '"> 
							</div>
							<div class="col-lg-9 col-lg-offset-3">' . $validity_msg . '</div>
							'.$info_msg.'
						</div>
					</div>
				</div>
        		<div class="panel-footer"> 
        			<button type="submit" class="btn btn-default pull-right" name="license_data_submit"><i class="process-icon-save"></i>' . $license_bt . '</button> 
					<button type="submit" class="btn btn-default pull-left" name="license_refresh"><i class="process-icon-refresh"></i>Refresh Activation</button>
        		</div>
			</div>
        </form>';
	}

	public function crazy_general_settings_form($license, $license_status){

		$check_yes           = '';
		$check_no            = '';
		$page_title_selector = PrestaHelper::get_option( 'page_title' );
		if ( PrestaHelper::get_option( 'presta_editor_enable' ) == 'yes' ) {
			$check_yes = 'checked = checked';
			$check_no  = '';
		}
		if ( PrestaHelper::get_option( 'presta_editor_enable', 'no' ) == 'no' ) {
			$check_no  = 'checked = checked';
			$check_yes = '';
		}

		$content_check_yes = '';
		$content_check_no  = '';
		$disable_ce = '';
		if ( PrestaHelper::get_option( 'crazy_content_disable' ) == 'yes' ) {
			$content_check_yes = 'checked = checked';
			$content_check_no  = '';
			$checked_pages = PrestaHelper::get_option( 'specific_page_disable' );
			$checked_pages = json_decode( $checked_pages, true );
			$page_types = array(
				'index' => 'Homepage',
				'cms' => 'Cms',
				'product' => 'Product',
				'category' => 'Product Category',
				'supplier' => 'Supplier',
				'manufacturer' => 'Manufacturer'
			);
			$page_options = '';
			foreach($page_types as $key => $p_type){
				$selected = '';
				
				if(isset($checked_pages[$key])){
					$selected = ' checked="checked" ';
				}
				$page_options .= '<div class="specific-page"><input type="checkbox" id="'.$key.'" name="specific_page['.$key.']" '.$selected.'> <span>' . $p_type. '</span> </div>';
			}
			$disable_ce = '<div class="form-group">
			<label class="control-label col-lg-3">Enable Crazyelements Content On</label>
			<div class="col-lg-9 specific-page-wrapper">
			'.$page_options.'
			</div>
		</div>';
		}
		if ( PrestaHelper::get_option( 'crazy_content_disable', 'no' ) == 'no' ) {
			$content_check_no  = 'checked = checked';
			$content_check_yes = '';
		}
		$disabled_crazy_html = "";
		if($license == '' || $license_status != 'valid'){
			$disabled_crazy_html = '
			<div class="form-group"> 
				<div id="conf_crazy_content_disable"> 
					'.$this->crazy_settings_desc_block('error-block', 'Activate your license to use features like "Disable Crazyelements loading in specific pages".', 'https://classydevs.com/elementor-prestashop-page-builder/pricing/?utm_source=backofc_licnse&utm_medium=backofc_licnse&utm_campaign=backofc_licnse&utm_id=backofc_licnse&utm_term=backofc_licnse&utm_content=backofc_licnse','Click Here to Get License Key.').'>
				</div>
			</div>';
		}else{
			$disabled_crazy_html = '
			<div class="form-group"> 
				<div id="conf_crazy_content_disable"> 
					<label class="control-label col-lg-3"> Disable Crazyelements Content </label> 
					<div class="col-lg-9"> <span class="switch prestashop-switch fixed-width-lg"> 
					<input type="radio" name="crazy_content_disable" id="crazy_content_disable_on" value="1" ' . $content_check_yes . '>
					<label for="crazy_content_disable_on" class="radioCheck">Yes</label>
					<input type="radio" name="crazy_content_disable" id="crazy_content_disable_off" value="0"  ' . $content_check_no . '>
					<label for="crazy_content_disable_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
					</div>
					'.$this->crazy_settings_desc_block('help-block', 'Enable or disable Crazyelements content in front').'
				</div>
			</div>
			'.$disable_ce;
		}
		return '
		<form action="" id="configuration_form_4" method="post" enctype="multipart/form-data" class="form-horizontal"> 
        	<div class="panel " id="configuration_fieldset_page_title_selector">
        		 <div class="panel-heading"> <i class="icon-cogs"></i> General Settings</div>
				<div class="form-wrapper"> 
					<div class="form-group">
						<div id="conf_id_page_title"> 
							<label class="control-label col-lg-3"> Page Title Selector </label> 
							<div class="col-lg-9">
								<input class="form-control " type="text" size="5" name="page_title" value="' . $page_title_selector . '"> 
							</div>
							<div class="col-lg-9 col-lg-offset-3"> </div>
						</div>
					</div>
					<div class="form-group"> 
						<div id="conf_presta_editor_enable"> 
							<label class="control-label col-lg-3"> Enable Presta Editor </label> 
							<div class="col-lg-9"> <span class="switch prestashop-switch fixed-width-lg"> 
							<input type="radio" name="presta_editor_enable" id="presta_editor_enable_on" value="1" ' . $check_yes . '>
							<label for="presta_editor_enable_on" class="radioCheck">Yes</label>
							<input type="radio" name="presta_editor_enable" id="presta_editor_enable_off" value="0"  ' . $check_no . '>
							<label for="presta_editor_enable_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
							</div>
							'.$this->crazy_settings_desc_block('help-block', ' Enable or disable Prestashop default editor').'
						</div>
					</div>
					'.$disabled_crazy_html.'
					<div class="form-group"> 
						<div id="conf_presta_editor_enable"> 
							<label class="control-label col-lg-3 trouble-label"> Facing Issues? </label> 
							<div class="col-lg-9"> 
								<a href="'.$this->context->link->getAdminLink( 'AdminCrazyTroubleshooting' ).'" class="btn btn btn-danger ">Go to Troubleshooting!!!</a>
							</div>
						</div>
					</div>
					'.$this->crazy_settings_panel_footer('page_title_submit', 'Save').'
				</div> 
			</div> 
        </form>';
	}

	public function crazy_layout_settings_form(){
		
		$shop_id = $this->context->shop->id;
		$lang_id = $this->context->language->id;

		// Home Layout
		$home_setting_html = "";
		$selected_home_layout = "";
		$layout_options = "";
		$namefield = 'crazy_home_layout';

		$layout_types = array(
			'default' => 'Default',
			'crazy_canvas' => 'Crazy Canvas Layout',
			'crazy_fullwidth' => 'Crazy Fullwidth Layout'
		);
		
		$isMultiShopActive = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');

		if($isMultiShopActive){
			$namefield = $namefield . '_' .$shop_id . '_' . $lang_id;
			$selected_home_layout = PrestaHelper::get_option( $namefield, '' );	
			if($selected_home_layout == ''){
			    $selected_home_layout = PrestaHelper::get_option( $namefield, 'default' );
			    PrestaHelper::update_option( $namefield, $selected_home_layout );
			}
		}else{
			$selected_home_layout = PrestaHelper::get_option( $namefield, 'default' );
		}

		foreach($layout_types as $key => $l_type){
			$selected = '';
			if($key == $selected_home_layout){
				$selected = ' selected="selected" ';
			}
			$layout_options .= '<option '.$selected.' value="'.$key.'">'.$l_type.'</option>';
		}
		
		$layout_html = '<select name="'.$namefield.'">'.$layout_options.'</select>';

		// Remove Hook Html
		$remove_hook_html = '<span class="switch prestashop-switch fixed-width-lg"> 
		<input type="radio" name="remove_display_home_hook" id="remove_display_home_hook" value="1">
		<label for="remove_display_home_hook" class="radioCheck">Yes</label>
		<input type="radio" name="remove_display_home_hook" id="remove_display_home_hook_off" value="0" checked="checked">
		<label for="remove_display_home_hook_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span>';

		// Header Selector

		$header_options = "";
		$footer_options = "";
		$fzf_options = "";
		$hnamefield = "crazy_header_layout_" .$shop_id;
		$fnamefield = "crazy_footer_layout_" .$shop_id;
		$fzfnamefield = "crazy_404_layout_" .$shop_id;

		$results         = array();
		$table_name      = _DB_PREFIX_ . 'crazy_content';
		$table_name_lang = _DB_PREFIX_ . 'crazy_content_lang';
		$table_name_shop = _DB_PREFIX_ . 'crazy_content_shop';
		$results         = Db::getInstance()->executeS("SELECT v.id_crazy_content,vl.title, v.hook FROM $table_name v 
		INNER JOIN $table_name_lang vl ON (v.`id_crazy_content` = vl.`id_crazy_content` AND vl.`id_lang` = $lang_id)
		INNER JOIN $table_name_shop vs ON (v.`id_crazy_content` = vs.`id_crazy_content` AND vs.`id_shop` = $shop_id AND v.`active` = 1)
		WHERE v.hook='hbuilder' OR v.hook='fbuilder' OR v.hook='fzfbuilder'");

		array_unshift($results, array('id_crazy_content'=>'0','title'=>'Default','hook'=>'hbuilder'));
		array_unshift($results, array('id_crazy_content'=>'0','title'=>'Default','hook'=>'fbuilder'));
		array_unshift($results, array('id_crazy_content'=>'0','title'=>'Default','hook'=>'fzfbuilder'));

		foreach($results as $result){
			$selected = '';
			if($result['hook'] == 'hbuilder'){
				$selected_header_layout = PrestaHelper::get_option( $hnamefield, '0' );	
				if($result['id_crazy_content'] == $selected_header_layout){
					$selected = ' selected="selected" ';
				}
				$header_options .= '<option '.$selected.' value="'.$result['id_crazy_content'].'">'.$result['title'].'</option>';
			}elseif($result['hook'] == 'fbuilder'){
				$selected_footer_layout = PrestaHelper::get_option( $fnamefield, '0' );	
				if($result['id_crazy_content'] == $selected_footer_layout){
					$selected = ' selected="selected" ';
				}
				$footer_options .= '<option '.$selected.' value="'.$result['id_crazy_content'].'">'.$result['title'].'</option>';
			}elseif($result['hook'] == 'fzfbuilder'){
				$selected_404_layout = PrestaHelper::get_option( $fzfnamefield, '0' );	
				if($result['id_crazy_content'] == $selected_404_layout){
					$selected = ' selected="selected" ';
				}
				$fzf_options .= '<option '.$selected.' value="'.$result['id_crazy_content'].'">'.$result['title'].'</option>';
			}

		}

		$header_html = '<select name="'.$hnamefield.'">'.$header_options.'</select>';
		$footer_html = '<select name="'.$fnamefield.'">'.$footer_options.'</select>';
		$fzf_html = '<select name="'.$fzfnamefield.'">'.$fzf_options.'</select>';

		$link = new Link();
		$hbuilder_link = $link->getAdminLink( 'AdminCrazyhbuilder' );
		$fbuilder_link = $link->getAdminLink( 'AdminCrazyfbuilder' );
		$fzfbuilder_link = $link->getAdminLink( 'AdminCrazyfzfbuilder' );


		$home_setting_html = '
		<div class="form-wrapper"> 
			<div class="form-group">
				'.$this->crazy_settings_form_body('Select Home Layout',$layout_html)
				.$this->crazy_settings_desc_block('help-block', ' Select Layout for your Homepage').'
			</div>
			<div class="form-group"> 
				'.$this->crazy_settings_form_body('Clear displayHome Hook',$remove_hook_html)
				.$this->crazy_settings_desc_block('help-block', 'Remove all modules from displayHome hook').'
			</div>
			<div class="form-group"> 
				'.$this->crazy_settings_form_body('Select Header Template',$header_html)
				.$this->crazy_settings_desc_block('help-block', 'Select Your Created Header Template with Header Builder', $hbuilder_link, 'Create Header Template').'
			</div>
			<div class="form-group"> 
				'.$this->crazy_settings_form_body('Select Footer Template',$footer_html)
				.$this->crazy_settings_desc_block('help-block', 'Select Your Created Footer Template with Footer Builder', $fbuilder_link, 'Create Footer Template').'
			</div>
			<div class="form-group"> 
				'.$this->crazy_settings_form_body('Select 404 Template',$fzf_html)
				.$this->crazy_settings_desc_block('help-block', 'Select Your Created 404 Page Template with 404 Builder', $fzfbuilder_link, 'Create 404 Template').'
			</div>
		</div>
		'.$this->crazy_settings_panel_footer('crazy_home_settings', 'Save');
		return '<form action="" id="configuration_form_6" method="post" enctype="multipart/form-data" class="form-horizontal"> 
        <div class="panel " id="configuration_fieldset_replace"> <div class="panel-heading"> <i class="icon-cogs"></i> Layout Settings</div>
		'.$home_setting_html.'
		</div>
        </form>';
	}

	public function crazy_cust_hook_settings_form($license, $license_status){
		$cust_hook_html = "";

		if($license == '' || $license_status != 'valid'){
			$cust_hook_html = '
			<div class="form-wrapper"> 
				<div class="form-group"> 
					<div id="conf_crazy_content_disable"> 
					'.$this->crazy_settings_desc_block('error-block', ' Activate your license to Add Custom Hooks to Content Anywhere', 'https://classydevs.com/elementor-prestashop-page-builder/pricing/?utm_source=backofc_licnse&utm_medium=backofc_licnse&utm_campaign=backofc_licnse&utm_id=backofc_licnse&utm_term=backofc_licnse&utm_content=backofc_licnse', 'Click Here to Get License Key').'
					</div>
				</div>
			</div>';
		}else{
			$custom_hooks = PrestaHelper::get_option( 'crazy_custom_hooks' );
			$custom_hooks = json_decode( $custom_hooks, true );
			$custom_hooks_html = '';
			$co = 1;
			if(isset($custom_hooks)){
				foreach($custom_hooks as $custom_hook => $mod_route){
					$custom_hooks_html .= '
					<div class="form-group"> 
						<div id="conf_id_hook_data">
							<label class="control-label col-lg-2">('.$co.') Hook Name </label> 
							<div class="col-lg-2">
								<input class="form-control " disabled="disabled" type="text" size="5" value="'.$custom_hook.'">
							</div>
							<label class="control-label col-lg-2"> Page Rewrite </label> 
							<div class="col-lg-2">
								<input class="form-control " disabled="disabled" type="text" size="5" value="'.$mod_route.'"> 
							</div>
							<div class="col-lg-2">
								<input type="checkbox" id="vehicle1" name="remove_hook[]" value="'.$custom_hook.'"> Remove
							</div>
						</div>
					</div>';
					$co++;
				}
			}			
			$cust_hook_html = '
			<div class="form-group"> 
				<div id="conf_id_hook_data"> 
					<label class="control-label col-lg-2"> Add Hook Name </label> 
					<div class="col-lg-3">
						<input class="form-control " type="text" size="5" name="cust_hook_name"> 
					</div>
					<label class="control-label col-lg-2"> Add Page Rewrite </label> 
					<div class="col-lg-3">
						<input class="form-control " type="text" size="5" name="cust_modules_rewrite"> 
					</div>
				</div>
			</div>'.$custom_hooks_html. 
			$this->crazy_settings_panel_footer('crazy_cust_hook', 'Save');
		}
		return '
		<form action="" id="configuration_form_7" method="post" enctype="multipart/form-data" class="form-horizontal"> 
			<div class="panel " id="configuration_fieldset_replace"> 
				<div class="panel-heading"> 
					<i class="icon-cogs"></i> Add Custom Hooks</div><div class="form-wrapper"> 
				</div>
				<div class="form-wrapper"> 
					'.$cust_hook_html.'
				</div>
			</div>
        </form>';
	}

	public function crazy_replace_url_settings_form(){
		return '<form action="" id="configuration_form_2" method="post" enctype="multipart/form-data" class="form-horizontal"> 
        <div class="panel " id="configuration_fieldset_replace"> <div class="panel-heading"> <i class="icon-cogs"></i> Update Site Address</div><div class="form-wrapper"> <div class="form-group"> <div id="conf_id_crazy_old_url"> <label class="control-label col-lg-3"> Old Url </label> <div class="col-lg-9"><input class="form-control " type="text" size="5" name="crazy_old_url" value=""> </div>'.$this->crazy_settings_desc_block('help-block', 'Enter Your Old URL').'</div></div><div class="form-group"> <div id="conf_id_crazy_new_url"> <label class="control-label col-lg-3"> New Url </label> <div class="col-lg-9"><input class="form-control " type="text" size="5" name="crazy_new_url" value=""> </div>'.$this->crazy_settings_desc_block('help-block', 'Enter Your New URL').'</div></div></div>'.$this->crazy_settings_panel_footer('crazy_url_submit', 'Replace URL').'</div></form>';
	}

	public function crazy_clear_cache_settings_form(){
		return 
		'<form action="" id="configuration_form_1" method="post" enctype="multipart/form-data" class="form-horizontal"> 
			<div class="panel " id="configuration_fieldset_cache"> 
				<div class="panel-heading"> <i class="icon-cogs"></i> Clear Cache for Crazy</div>
				<div class="form-wrapper"> 
					<div class="form-group"> 
						<div id="conf_id_crazy_clear_cache"> 
							<label class="control-label col-lg-3"> Clear Cache </label> 
							<div class="col-lg-9"> <span class="switch prestashop-switch fixed-width-lg"> <input type="radio" name="crazy_clear_cache" id="crazy_clear_cache_on" value="1"><label for="crazy_clear_cache_on" class="radioCheck">Yes</label><input type="radio" name="crazy_clear_cache" id="crazy_clear_cache_off" value="0" checked="checked"><label for="crazy_clear_cache_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> </div>
							'.$this->crazy_settings_desc_block('help-block', 'If your css is not working clearing cache might help.').'
						</div>
					</div>
				</div>
				'.$this->crazy_settings_panel_footer('crazy_clear_cache_submit', 'Clear Cache').'
			</div>
        </form>';
	}

	public function crazy_mailchimp_settings_form(){
		return '<form action="" id="configuration_form_5" method="post" enctype="multipart/form-data" class="form-horizontal"> 
        <div class="panel " id="configuration_fieldset_mailchimp"> <div class="panel-heading"> <i class="icon-cogs"></i> Enter Mailchimp API Key</div><div class="form-wrapper"> <div class="form-group"> <div id="conf_id_license_data"> <label class="control-label col-lg-3"> Api Key </label> <div class="col-lg-9"><input class="form-control " type="text" size="5" name="mailchimp_data" value="' . PrestaHelper::get_option( 'mailchimp_data' ) . '"> </div>'.$this->crazy_settings_desc_block('help-block', 'Enter Your Mailchimp API Key.').'</div></div></div>
		'.$this->crazy_settings_panel_footer('mailchimp_data_submit', 'Save').'</div></form>';
	}

	public function crazy_settings_form_body($label, $body){
		return '<label class="control-label col-lg-3">'.$label.'</label><div class="col-lg-5">'.$body.'</div>';
	}

	public function crazy_settings_desc_block($class, $text, $url = '', $url_text = ''){
		if($url != ''){
			$text .= '<a href="'.$url.'" target="_blank" class="get-prod-bt"> '.$url_text.' </a>';
		}
		return '<div class="col-lg-9 col-lg-offset-3"> <div class="'.$class.'">'.$text.'</div></div>';
	}

	public function crazy_settings_panel_footer($bt_name, $text){
		return '<div class="panel-footer"><button type="submit" class="btn btn-default pull-right" name="'.$bt_name.'"><i class="process-icon-save"></i> '.$text.'</button></div>';
	}
}