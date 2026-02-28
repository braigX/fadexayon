<?php

/**
 * 2017 Keshva Thakur
 * @author Keshva Thakur
 * @copyright Keshva Thakur
 * @license   https://www.prestashop.com/en/osl-license
 * @version   2.1.8
 */

if (!defined('_PS_VERSION_')){
	exit;
}


class leveragebrowsercache extends Module
{	
	public $languages;
	public $html;
	public $_html="";
	public $module_p;
	public $confirmation = false;

	public function __construct()

	{
		$this->name = 'leveragebrowsercache';
		$this->tab = 'administration';
		$this->version = '4.1.9';		
		$this->need_instance = 1;
		$this->bootstrap  = true;
		$this->module_key = '505aa4b40399225ffb509696cd65b03c';		
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->_html="";


		parent::__construct();

		$this->displayName = $this->l('Cache warmer Regenerate & Leverage Cache - - Speed up website');
		$this->description = $this->l('Increase your page loading speed be building the cache before your customers access it .');

		$this->_buildData();

	}

	public function installDb()
	{

			

		$token = Configuration::get('rc_token');

		if(!empty($token) || !($token))
			Configuration::updateValue('rc_token', md5(Configuration::get('PS_SHOP_EMAIL').rand(1,1000)) );	


	    return true;

  }

	public function install()
	{
		if (
			!parent::install() OR 
			!$this->registerHook('actionProductUpdate') OR
			!$this->registerHook('displayBackOfficeHeader') OR
			!$this->installDb()
		 )
			return false;


		Configuration::updateValue('rc_products',  5 );
		Configuration::updateValue('rc_categories',  5 );
		Configuration::updateValue('rc_manufacturers',  5 );
		Configuration::updateValue('rc_suppliers',  5 );
		Configuration::updateValue('rc_cms', 5);	
		Configuration::updateValue('rc_products_preview', 0);
		Configuration::updateValue('rc_products_preview_p', 0);
		Configuration::updateValue('leveragebrowsercache', 0);


		return true;
	}

     public function uninstall() {
      
        if (!parent::uninstall() || !$this->removeHTACCESSCode()) {        
             return true;
             Configuration::deleteByName('leveragebrowsercache');
        }
        return true;
    }
    
   
   public function removeHTACCESSCode() {


        $presta_headers = '# ~~start~~ Do not remove this comment';
        $presta_closing = '# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again';

        // now add to the htaccess

       
        $htweaker_bottom_starts = '# ~leveragebrowsercache_bottom~';       
        $bottom_content = "#leverage";      
        $htweaker_bottom_ends = '# ~leveragebrowsercache_bottom_end~';

        $htaccess = _PS_ROOT_DIR_ . '/.htaccess';
        if (!file_exists($htaccess)) { 
             return false;
        }else{
            $htaccess = file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        }
        
        $content_to_add_bottom = $htweaker_bottom_starts . "\n" . $bottom_content . "\n" . $htweaker_bottom_ends;

        if (preg_match('/\# ~leveragebrowsercache_bottom~(.*?)\# ~leveragebrowsercache_bottom_end~/s', $htaccess, $m)) {

            $content_to_remove = $m[0];

            $htaccess = str_replace($content_to_remove, $content_to_add_bottom, $htaccess);
        } else // nothing found at the top, add it
            $htaccess = str_replace($presta_closing, $presta_closing . "\n\n" . $content_to_add_bottom, $htaccess);

        file_put_contents(_PS_ROOT_DIR_ . '/.htaccess', $htaccess);
            
    }

    public function addAndUpdateHTACCESS() {


        $presta_headers = '# ~~start~~ Do not remove this comment';
        $presta_closing = '# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again';

        // now add to the htaccess

      
        $htweaker_bottom_starts = '# ~leveragebrowsercache_bottom~';       
        $bottom_content = $this->htCode();
        $htweaker_bottom_ends = '# ~leveragebrowsercache_bottom_end~';


        $htaccess = _PS_ROOT_DIR_ . '/.htaccess';
        if (!file_exists($htaccess)) {              
            Tools::generateHtaccess();
            $htaccess = file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        }else{
            $htaccess = file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');   
        }       

        $content_to_add_bottom = $htweaker_bottom_starts . "\n" . $bottom_content . "\n" . $htweaker_bottom_ends;

        if (preg_match('/\# ~leveragebrowsercache_bottom~(.*?)\# ~leveragebrowsercache_bottom_end~/s', $htaccess, $m)) {

            $content_to_remove = $m[0];

            $htaccess = str_replace($content_to_remove, $content_to_add_bottom, $htaccess);
        } else // nothing found at the top, add it
            $htaccess = str_replace($presta_closing, $presta_closing . "\n\n" . $content_to_add_bottom, $htaccess);

        file_put_contents(_PS_ROOT_DIR_ . '/.htaccess', $htaccess);
    }
        

    public function htCode() {
        $this->htaccess_cntn = "\n";
        $this->htaccess_cntn .= '# LBROWSERCSTART Browser Caching' . "\n";
        $this->htaccess_cntn .= '<IfModule mod_expires.c>' . "\n";
        $this->htaccess_cntn .= 'ExpiresActive On' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType image/gif "access 1 year"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType image/jpg "access 1 year"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType image/jpeg "access 1 year"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType image/png "access 1 year"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType image/x-icon "access 1 year"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType text/css "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType text/javascript "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType text/html "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType application/javascript "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType application/x-javascript "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType application/xhtml-xml "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType application/pdf "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresByType application/x-shockwave-flash "access 1 month"' . "\n";
        $this->htaccess_cntn .= 'ExpiresDefault "access 1 month"' . "\n";
        $this->htaccess_cntn .= '</IfModule>' . "\n";
        $this->htaccess_cntn .= '# END Caching LBROWSERCEND' . "\n";

        return $this->htaccess_cntn;
    }

	public function hookdisplayBackOfficeHeader($params)
	{

		if(Tools::getIsset('controller'))
		{
			if( Tools::getValue('controller') == 'AdminPerformance')
			{
	
				$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
				$tokenModule = Tools::getAdminToken('AdminModules'.((int)(Tab::getIdFromClassName('AdminModules'))).((int)($cookie->id_employee)));
				$this->smarty->assign(
						array(
								'tokenModule' => $tokenModule
							)
					);
				return $this->display(__FILE__, 'views/templates/admin/backoffice_header.tpl');
			}
			elseif(Tools::getValue('controller') == 'AdminModules')
			{	

					if(Tools::getIsset('module_name'))
					{
						if( Tools::getValue('module_name') == 'pagecache')
						{

							$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
							$tokenModule = Tools::getAdminToken('AdminModules'.((int)(Tab::getIdFromClassName('AdminModules'))).((int)($cookie->id_employee)));
							$this->smarty->assign(
									array(
											'tokenModule' => $tokenModule
										)
								);
							return $this->display(__FILE__, 'views/templates/admin/backoffice_header.tpl');
						}
					}



			}
		}

	}

	public function hookactionProductUpdate($params)
	{

		

		return true;
	}



	public function _buildData()
	{
		$this->languages = Language::getLanguages(true); 
	}

	public function process_post()
	{
		Configuration::updateValue('rc_products',  Tools::getValue('product_per_excution') );
		Configuration::updateValue('rc_categories',  Tools::getValue('categories_per_excution') );
		Configuration::updateValue('rc_manufacturers',  Tools::getValue('manufacturers_per_excution') );
		Configuration::updateValue('rc_suppliers',  Tools::getValue('suppliers_per_excution') );
		Configuration::updateValue('rc_cms',  Tools::getValue('cms_per_excution') );	
		Configuration::updateValue('rc_products_preview',  Tools::getValue('products_preview') );	
		Configuration::updateValue('rc_products_preview_p',  Tools::getValue('products_preview_p') );	
		$this->confirmation = $this->l('Settings have been updated');
	}


	public function getContent()
	{

	   	 $this->processSubmit();   


		if(Tools::getIsset('product_per_excution'))
			$this->process_post();

		$this->_html = $this->displayName;
		$this->_buildData();
		$this->_buildHtml();
		
		return $this->_html;
	}
	
	public function displayFormLeverage() {
        $fields_form = array();
        $fields_form[]['form'] = array(
            'input' => array(
                array(
                    'name' => 'topform',
                    'type' => 'topform',
                    'leveragebrowsercache' => Configuration::get('leveragebrowsercache'),
                ),
            ),
        );


        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $this->_html . $helper->generateForm($fields_form);
    }
	

    public function processSubmit() {

        if (Tools::isSubmit('submit' . $this->name)) {
            $on_off_option = Tools::getValue('leveragebrowsercache');
            if ($on_off_option == '1') {
                $this->addAndUpdateHTACCESS();
                Configuration::updateValue('leveragebrowsercache', $on_off_option);
                $this->_html .= $this->displayConfirmation("Leverage browser cache enable sucessfully");
            }
            if ($on_off_option == '0') {
                $this->removeHTACCESSCode();
                Configuration::updateValue('leveragebrowsercache', $on_off_option);
                $this->_html .= $this->displayConfirmation("Leverage browser cache disable sucessfully");
            }
        }
    }

	private function get_url_content($url)
	{

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);


		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);



		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");

		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_ENCODING, "");


		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 8);



		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$contents = curl_exec($curl);
		curl_close($curl);


		return $contents;

	}


	public static function get_url_content_static($url)
	{

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);


		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);



		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");

		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_ENCODING, "");


		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 8);



		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$contents = curl_exec($curl);
		curl_close($curl);


		return $contents;

	}



	public function _buildHtml()
	{
	
		$ps_version = Tools::substr(_PS_VERSION_,0,3);
		$arr = array();



		$arr['rc_module_path'] = '//'.Tools::getShopDomainSsl(false).'/modules/'.$this->name;	
		$arr['rc_site'] = '//'.Tools::getShopDomainSsl(false);	
		$arr['rc_token'] = Configuration::get('rc_token');

		$arr['rc_products'] = Configuration::get('rc_products');
		$arr['rc_categories'] = Configuration::get('rc_categories');
		$arr['rc_manufacturers'] = Configuration::get('rc_manufacturers');
		$arr['rc_suppliers'] = Configuration::get('rc_suppliers');
		$arr['rc_cms'] = Configuration::get('rc_cms');
		$arr['rc_products_preview'] = Configuration::get('rc_products_preview');
		$arr['rc_products_preview_p'] = Configuration::get('rc_products_preview_p');
		$arr['rc_confirmation'] = $this->confirmation;

		if ($ps_version == '1.5')
			$arr['css_file'] = 'global_15.css';
		else
			$arr['css_file'] = 'global_16.css';


		$id_shop = Context::getContext()->shop->id;
		$sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product_shop`  WHERE active = 1 ' ;
		
		if($id_shop)
			$sql .= ' and id_shop ='.(int)$id_shop;	
		
		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['products_count'] = $results['count(id_product)'];
	    else
	   		$arr['products_count'] = 0;


		$sql = 'SELECT  count(id_category) FROM `'._DB_PREFIX_.'category_shop`  ';	
		if($id_shop)
			$sql .= ' WHERE  id_shop ='.(int)$id_shop;	

		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['categories_count'] = $results['count(id_category)'];
	    else
	   		$arr['categories_count'] = 0;


		$sql = 'SELECT  count(id_manufacturer) FROM `'._DB_PREFIX_.'manufacturer_shop`    ';	

		if($id_shop)
			$sql .= ' WHERE  id_shop ='.(int)$id_shop;	

		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['manufacturers_count'] = $results['count(id_manufacturer)'];
	    else
	   		$arr['manufacturers_count'] = 0;



		$sql = 'SELECT  count(id_cms) FROM `'._DB_PREFIX_.'cms_shop`   ';	
		
		if($id_shop)
			$sql .= ' WHERE id_shop ='.(int)$id_shop;	

		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['cms_count'] = $results['count(id_cms)'];
	    else
	   		$arr['cms_count'] = 0;


		$sql = 'SELECT  count(id_supplier) FROM `'._DB_PREFIX_.'supplier_shop`  ';	
		
		if($id_shop)
			$sql .= ' WHERE id_shop ='.(int)$id_shop;	

		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['suppliers_count'] = $results['count(id_supplier)'];
	    else
	   		$arr['suppliers_count'] = 0;

		$arr['curl_error'] = 0;
		$arr['rc_id_shop'] = $id_shop;

	   	//if(!$this->get_url_content(_PS_BASE_URL_))
	   	//	$arr['curl_error'] = 1;

		$this->smarty->assign($arr);
		
		$this->_html .=$this->displayFormLeverage();
		
		$this->_html .=  $this->display(__FILE__, 'views/templates/admin/content.tpl');


	}

	


}