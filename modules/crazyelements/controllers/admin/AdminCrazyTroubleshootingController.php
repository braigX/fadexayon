<?php
defined( '_PS_VERSION_' ) or exit;
require_once _PS_MODULE_DIR_ . 'crazyelements/PrestaHelper.php';
use CrazyElements\PrestaHelper;
use Doctrine\DBAL\Connection;

class AdminCrazyTroubleshootingController extends AdminController {

	public function __construct() {
		$this->context   = Context::getContext();
		$this->bootstrap = true;
		$this->table     = 'configuration';
		parent::__construct();
	}

	public function initContent() {
		PrestaHelper::get_lience_expired_date();
		$this->context->controller->addCSS( CRAZY_ASSETS_URL . 'css/select2.min.css' );
		$this->context->controller->addJS( CRAZY_ASSETS_URL . 'js/select2.min.js' );
		$this->context->controller->addJS( CRAZY_ASSETS_URL . 'js/crazy_admin.js' );
		if ( Tools::isSubmit( 'tabs_and_hooks_submit' ) ) {
			
			$register_hooks_ce = Tools::getValue( 'register_hooks_ce' );
			if ( $register_hooks_ce == '1' ) {
				$mod_ins = Module::getInstanceByName( 'crazyelements' );
				$mod_ins->registerHook('header');
				$mod_ins->registerHook('displayDashboardTop');
				$mod_ins->registerHook('backOfficeHeader');
				$mod_ins->registerHook('displayBackOfficeHeader');
				$mod_ins->registerHook('backOfficeFooter');
				$mod_ins->registerHook('actionCrazyBeforeInit');
				$mod_ins->registerHook('actionCrazyAddCategory');
				$mod_ins->registerHook('actionObjectAddAfter');
				$mod_ins->registerHook('actionObjectUpdateAfter');
				$mod_ins->registerHook('overrideLayoutTemplate');
				$mod_ins->registerHook('actionCmsPageFormBuilderModifier');
				$mod_ins->registerHook('actionObjectCmsUpdateAfter');
				$mod_ins->registerHook( 'DisplayOverrideTemplate' );
			}
			$langs    = Language::getLanguages();
			$refreshtabs_ce = Tools::getValue( 'refreshtabs_ce' );
			if ( $refreshtabs_ce == '1' ) {
				$tabs = $this->get_tabs();
				foreach($tabs as $key => $tab){
					$idtab = Tab::getIdFromClassName($key);
					if(!$idtab){
						$newtab             = new Tab();
						$newtab->class_name = $tab['class_name'];
						$newtab->module     = $tab['module'];
						$newtab->id_parent  = $tab['id_parent'];
						foreach ($langs as $l) {
							$newtab->name[$l['id_lang']] = $tab['name'];
						}
						$newtab->add(true, false);
					}
				}
			}
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazyTroubleshooting' ) );
		}
		
		if ( Tools::isSubmit( 'assets_submit' ) ) {
			
			$assets_disable = Tools::getValue( 'assets_disable' );
			PrestaHelper::update_option( 'crazy_assets_disabled', $assets_disable );		
			Tools::redirectAdmin( $this->context->link->getAdminLink( 'AdminCrazyTroubleshooting' ) );
		}
		if ( Tools::isSubmit( 'extras_submit' ) ) {
			
			$crazy_hlpr_tpl_disabled = Tools::getValue( 'crazy_hlpr_tpl_disabled' );
			PrestaHelper::update_option( 'crazy_hlpr_tpl_disabled', $crazy_hlpr_tpl_disabled );		
		}
		parent::initContent();
	}

	public function renderList(){


		$dbissues = $this->check_dbissues();

		$tabs = $this->get_tabs();

		$assets_disabled = PrestaHelper::get_option( 'crazy_assets_disabled' );
		$assets_disabled = json_decode( $assets_disabled, true );
		$assets_checked = array();

		$hlpr_tpl_disabled = PrestaHelper::get_option( 'crazy_hlpr_tpl_disabled' );
		$hlpr_tpl_disabled_yes = "";
		$hlpr_tpl_disabled_no = 'checked = "checked"';

		if(isset($hlpr_tpl_disabled) && $hlpr_tpl_disabled){
			$hlpr_tpl_disabled_yes = 'checked = "checked"';
			$hlpr_tpl_disabled_no = '';
		}

		if(isset($assets_disabled) && !empty($assets_disabled)){
			foreach($assets_disabled as $key => $asset){
				if($asset){
					$assets_checked[$key]['checked_yes'] = 'checked = "checked"';
					$assets_checked[$key]['checked_no'] = '';
				}else{
					$assets_checked[$key]['checked_yes'] = '';
					$assets_checked[$key]['checked_no'] = 'checked = "checked"';
				}
			}
		}else{
			$assets_checked['waypoint_js']['checked_yes'] = '';
			$assets_checked['waypoint_js']['checked_no'] = 'checked = "checked"';
			$assets_checked['slick_js']['checked_yes'] = '';
			$assets_checked['slick_js']['checked_no'] = 'checked = "checked"';
			$assets_checked['fontawesome_css']['checked_yes'] = '';
			$assets_checked['fontawesome_css']['checked_no'] = 'checked = "checked"';
		}

		$tabopts = '';

		foreach($tabs as $key => $tab){

			$idtab = Tab::getIdFromClassName($key);
			if(!$idtab){
				$tabopts .= "<br>". $tab['name'];
			}
		}
		if($tabopts != ''){
			$tabopts = "<div class='error-block'>The tabs below are unavailable.<div class='table-names'>" . $tabopts . '</div></div>';
			$tabopts = '<div class="form-group"> 
			<div class="col-lg-8 col-lg-offset-4">
				'.$tabopts.'
			</div>
		</div>
		<div class="form-group"> 
			<label class="control-label col-lg-4">Generate Tabs Above</label> 
			<div class="col-lg-8"> <span class="switch prestashop-switch fixed-width-lg"> 
			<input type="radio" name="refreshtabs_ce" id="refreshtabs_ce_on" value="1">
			<label for="refreshtabs_ce_on" class="radioCheck">Yes</label>
			<input type="radio" name="refreshtabs_ce" id="refreshtabs_ce_off" value="0" checked = "checked">
			<label for="refreshtabs_ce_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
			</div>
		</div>';
		}
		$assetsues = '<div class="form-group"> 
						<label class="control-label col-lg-6"> Disable Waypoint Js</label> 
						<div class="col-lg-6"> <span class="switch prestashop-switch fixed-width-lg"> 
						<input type="radio" name="assets_disable[waypoint_js]" id="waypoint_js_disable_on" value="1" '.$assets_checked['waypoint_js']['checked_yes'].'>
						<label for="waypoint_js_disable_on" class="radioCheck">Yes</label>
						<input type="radio" name="assets_disable[waypoint_js]" id="waypoint_js_disable_off" value="0" '.$assets_checked['waypoint_js']['checked_no'].'>
						<label for="waypoint_js_disable_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
						</div>
					</div>
					<div class="form-group"> 
						<label class="control-label col-lg-6"> Disable Slick Js</label> 
						<div class="col-lg-6"> <span class="switch prestashop-switch fixed-width-lg"> 
						<input type="radio" name="assets_disable[slick_js]" id="slick_js_disable_on" value="1" '.$assets_checked['slick_js']['checked_yes'].'>
						<label for="slick_js_disable_on" class="radioCheck">Yes</label>
						<input type="radio" name="assets_disable[slick_js]" id="slick_js_disable_off" value="0" '.$assets_checked['slick_js']['checked_no'].'>
						<label for="slick_js_disable_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
						</div>
					</div>
					<div class="form-group"> 
						<label class="control-label col-lg-6"> Disable Fontawesome</label> 
						<div class="col-lg-6"> <span class="switch prestashop-switch fixed-width-lg"> 
						<input type="radio" name="assets_disable[fontawesome_css]" id="slick_css_disable_on" value="1" '.$assets_checked['fontawesome_css']['checked_yes'].'>
						<label for="slick_css_disable_on" class="radioCheck">Yes</label>
						<input type="radio" name="assets_disable[fontawesome_css]" id="slick_css_disable_off" value="0" '.$assets_checked['fontawesome_css']['checked_no'].'>
						<label for="slick_css_disable_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
						</div>
					</div>';

		$html = '<form action="" id="configuration_form_2" method="post" enctype="multipart/form-data" class="form-horizontal"> 
					<div class="panel trouble-db" id="configuration_fieldset_trouble"> 
						<div class="panel-heading"> 
							<i class="icon-cogs"></i>
							Database Issues
						</div>
						<div class="panel-content row">
							<div class="issue-block col-lg-6">
								'.$dbissues['issues'].'
							</div>
						</div>
					</div>
        		</form>
					<form action="" id="configuration_form_1" method="post" enctype="multipart/form-data" class="form-horizontal"> 
						<div class="panel " id="configuration_fieldset_trouble"> <div class="panel-heading"> <i class="icon-cogs"></i>Tabs and Hooks</div>
							<div class="form-group"> 
								<label class="control-label col-lg-4">Register All Hooks Needed for Crazyelements</label> 
								<div class="col-lg-8"> <span class="switch prestashop-switch fixed-width-lg"> 
								<input type="radio" name="register_hooks_ce" id="register_hooks_ce_on" value="1">
								<label for="register_hooks_ce_on" class="radioCheck">Yes</label>
								<input type="radio" name="register_hooks_ce" id="register_hooks_ce_off" value="0" checked = "checked">
								<label for="register_hooks_ce_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
								</div>
							</div>
							'.$tabopts.'
							<div class="panel-footer"> <button type="submit" class="btn btn-default pull-right" name="tabs_and_hooks_submit"><i class="process-icon-save"></i> Save</button> </div>
						</div>
					</form>
					<form action="" id="configuration_form_5" method="post" enctype="multipart/form-data" class="form-horizontal"> 
						<div class="panel " id="configuration_fieldset_trouble"> <div class="panel-heading"> <i class="icon-cogs"></i>Asset Loading</div>
						'.$assetsues.'
						<div class="panel-footer"> <button type="submit" class="btn btn-default pull-right" name="assets_submit"><i class="process-icon-save"></i> Save</button> </div>
						</div>
					</form>
				<form action="" id="configuration_form_6" method="post" enctype="multipart/form-data" class="form-horizontal"> 
					<div class="panel " id="configuration_fieldset_trouble"> <div class="panel-heading"> <i class="icon-cogs"></i>Extras</div>
						<div class="form-group"> 
							<label class="control-label col-lg-6">Disable Helper Loading for Logo</label> 
							<div class="col-lg-6"> <span class="switch prestashop-switch fixed-width-lg"> 
							<input type="radio" name="crazy_hlpr_tpl_disabled" id="crazy_hlpr_tpl_disabled_on" value="1" '.$hlpr_tpl_disabled_yes.'>
							<label for="crazy_hlpr_tpl_disabled_on" class="radioCheck">Yes</label>
							<input type="radio" name="crazy_hlpr_tpl_disabled" id="crazy_hlpr_tpl_disabled_off" value="0" '.$hlpr_tpl_disabled_no.'>
							<label for="crazy_hlpr_tpl_disabled_off" class="radioCheck">No</label> <a class="slide-button btn"></a> </span> 
							</div>
						</div>
					<div class="panel-footer"> <button type="submit" class="btn btn-default pull-right" name="extras_submit"><i class="process-icon-save"></i> Save</button> </div>
					</div>
				</form>';

        return parent::renderList() . $html;
    }

	private function check_dbissues(){
		$return = array();
		$defaults = array(
			_DB_PREFIX_ . "crazy_autocomplete_products",
			_DB_PREFIX_ . "crazy_content",
			_DB_PREFIX_ . "crazy_content_lang",
			_DB_PREFIX_ . "crazy_content_shop",
			_DB_PREFIX_ . "crazy_extended_modules",
			_DB_PREFIX_ . "crazy_fonts",
			_DB_PREFIX_ . "crazy_options",
			_DB_PREFIX_ . "crazy_library",
			_DB_PREFIX_ . "crazyprdlayouts",
			_DB_PREFIX_ . "crazyprdlayouts_lang",
			_DB_PREFIX_ . "crazyprdlayouts_shop",
			_DB_PREFIX_ . "crazy_layout_type",
			_DB_PREFIX_ . "crazy_setting",
			_DB_PREFIX_ . "crazy_revision"
		);

		$sql = 'SHOW TABLES LIKE "%_crazy_%"';
		$result = Db::getInstance()->query( $sql );
		$existing = $result->fetchAll(PDO::FETCH_COLUMN);
		$not_available = array_diff($defaults, $existing);
		$returnhtml = "";
		$solutionhtml = "";
		if(isset($not_available) && !empty($not_available)){
			$returnhtml .= "<div class='error-block'>Please create the unavailable tables below.<div class='table-names'>" . implode("<br>",$not_available) . '</div></div>';
			include_once CRAZY_PATH . '/sql/install_tables_trouble.php';
			$diff_keys = array_diff_key($sql, $not_available);
			foreach($not_available as $table){
				foreach ( $sql[$table] as $query ) {
					Db::getInstance()->execute( $query );
				}
			}
			$returnhtml .= "<div class='success-block'>The Unavailable Tables Have Been Created. Please Reload!!!</div>";
		}else{
			$returnhtml .= "<div class='success-block'>Everything is okay with the data tables releated to Crazyelements.</div>";
		}
		$return['issues'] = $returnhtml;
		$return['solutions'] = $solutionhtml;
		return $return;
	}

	private function get_tabs(){
		$id_parent_main = Tab::getIdFromClassName('AdminCrazyMain');
		$id_parent = Tab::getIdFromClassName('AdminCrazyEditor');
		$tabvalue  = array(
			'AdminCrazyEditor' => array(
				'class_name' => 'AdminCrazyEditor',
				'id_parent' => $id_parent_main,
				'module' => 'crazyelements',
				'name' => 'Crazy Editors',
				'icon' => 'brush',
			),
			'AdminCrazyFonts' => array(
				'class_name' => 'AdminCrazyFonts',
				'id_parent' => $id_parent_main,
				'module' => 'crazyelements',
				'name' => 'Font Manager',
				'active' => 1,
			),
			'AdminCrazyPseIcon' => array(
				'class_name' => 'AdminCrazyPseIcon',
				'id_parent' => $id_parent_main,
				'module' => 'crazyelements',
				'name' => 'Icon Manager',
				'active' => 1,
			),
			'AdminCrazySetting' => array(
				'class_name' => 'AdminCrazySetting',
				'id_parent' => $id_parent_main,
				'module' => 'crazyelements',
				'name' => 'Settings',
				'active' => 1,
				'icon' => 'settings',
			),
			'AdminCrazyExtendedmodules' => array(
				'class_name' => 'AdminCrazyExtendedmodules',
				'id_parent' => $id_parent_main,
				'module' => 'crazyelements',
				'name' => 'Extend Third Party Modules',
				'active' => 1,
			),
			'AdminCrazyContent' => array(
				'class_name' => 'AdminCrazyContent',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Content Any Where',
				'active'     => 1,
			),
			'AdminCrazyPages' => array(
				'class_name' => 'AdminCrazyPages',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Pages (cms)',
				'active'     => 1,
			),
			'AdminCrazyProducts' => array(
				'class_name' => 'AdminCrazyProducts',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Products Description',
				'active'     => 1,
			),
			'AdminCrazyCategories' => array(
				'class_name' => 'AdminCrazyCategories',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Categories Page',
				'active'     => 1,
			),
			'AdminCrazySuppliers' => array(
				'class_name' => 'AdminCrazySuppliers',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Suppliers Page',
				'active'     => 1,
			),
			'AdminCrazyBrands' => array(
				'class_name' => 'AdminCrazyBrands',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Brands Page',
				'active'     => 1,
			),
			'AdminCrazyPrdlayouts' => array(
				'class_name' => 'AdminCrazyPrdlayouts',
				'id_parent'  => $id_parent,
				'module'     => 'crazyelements',
				'name'       => 'Product Layout Builder',
				'active'     => 1,
			)
		);
		return $tabvalue;
	}
}