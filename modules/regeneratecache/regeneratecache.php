<?php
/**
*    2007-2017 PrestaShop
*
*    NOTICE OF LICENSE
*
*    This source file is subject to the Academic Free License (AFL 3.0)
*    that is bundled with this package in the file LICENSE.txt.
*    It is also available through the world-wide-web at this URL:
*    http://opensource.org/licenses/afl-3.0.php
*    If you did not receive a copy of the license and are unable to
*    obtain it through the world-wide-web, please send an email
*    to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*    @author    PrestaShop SA <contact@prestashop.com>
*    @copyright 2007-2015 PrestaShop SA
*    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;


class RegenerateCache extends Module
{	
	public $languages;
	public $html;
	public $module_p;
	public $confirmation = false;
	public function __construct()
	{
		$this->name = 'regeneratecache';
		$this->tab = 'administration';
		$this->version = '1.6.0';
		$this->author = 'advancedplugins';
		$this->need_instance = 1;
		$this->bootstrap  = true;
    $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
		$this->module_key = '7a4a670ed5075ac3c39271f4549b63c0';
		$this->author_address = '0x6b0EA5e7A2019Ca82d288436edf45e1B9a3540b5';

		parent::__construct();

		$this->displayName = $this->l('Regenerate Cache');
		$this->description = $this->l('Increase your page loading speed be building the cache before your customers access it. Compatible Prestashop 8.x.x & Jpresta - Internal version not support');
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
			!$this->registerHook('updateproduct') OR
			!$this->registerHook('displayBackOfficeHeader') OR
			!$this->installDb()
		 )
			return false;

		Configuration::updateValue('rc_products',  5 );
		Configuration::updateValue('rc_categories',  5 );
		Configuration::updateValue('rc_manufacturers',  5 );
		Configuration::updateValue('rc_suppliers',  5 );
		Configuration::updateValue('rc_cms', 5);
    Configuration::updateValue('rc_cache_header', "Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13");	
		Configuration::updateValue('rc_header_cron', 0);
		Configuration::updateValue('rc_products_preview', 0);
		Configuration::updateValue('rc_products_preview_p', 0);
		return true;
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

	public function hookaupdateproduct($params)
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
		Configuration::updateValue('rc_cache_header',  Tools::getValue('products_header') );
    Configuration::updateValue('rc_header_cron',  Tools::getValue('header_cron') );
		Configuration::updateValue('rc_products_preview',  Tools::getValue('products_preview') );	
		Configuration::updateValue('rc_products_preview_p',  Tools::getValue('products_preview_p') );	
		$this->confirmation = $this->l('Settings have been updated');
	}

	public function getContent()
	{
		if(Tools::getIsset('product_per_excution'))
		$this->process_post();
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_buildData();
		$this->_buildHtml();
		return $this->_html;
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
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');    
		curl_setopt($curl, CURLOPT_ENCODING, "");
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 8);
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$contents = curl_exec($curl);
		curl_close($curl);
    return $contents;
    }

 	private function get_url_content_mobile($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.114 Mobile Safari/537.36');    
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
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13'); 
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 8);
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$contents = curl_exec($curl);
		curl_close($curl);
		return $contents;
	} 
  
	public static function get_url_content_static_mobile($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
		curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.114 Mobile Safari/537.36'); 
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
    $id_domain = Context::getContext()->shop->domain;
    $id_virtua = Context::getContext()->shop->virtual_uri;
    $id_url = ''.$id_domain.'/'.$id_virtua.'';
		$ps_version = Tools::substr(_PS_VERSION_,0,3);
		$arr = array();
		if ($_SERVER['HTTPS'] == 'on') { $arr['rc_module_path'] = 'https://'.$id_url.'/modules/'.$this->name; }	
    else { $arr['rc_module_path'] = 'http://'.$id_url.'/modules/'.$this->name; };
   	if ($_SERVER['HTTPS'] == 'on') { $arr['rc_site'] = 'https://'.$id_url.''; }	
    else { $arr['rc_site'] = 'http://'.$id_url.''; };
    if ($_SERVER['HTTPS'] == 'on') { $arr['rc_ajax'] = 'https://'.$id_url.'index.php?fc=module&module=regeneratecache&controller=ajax';	}
    else { $arr['rc_ajax'] = 'http://'.$id_url.'index.php?fc=module&module=regeneratecache&controller=ajax';	}
		$arr['rc_token'] = Configuration::get('rc_token');
		$arr['rc_products'] = Configuration::get('rc_products');
		$arr['rc_categories'] = Configuration::get('rc_categories');
		$arr['rc_manufacturers'] = Configuration::get('rc_manufacturers');
		$arr['rc_suppliers'] = Configuration::get('rc_suppliers');
		$arr['rc_cms'] = Configuration::get('rc_cms');
		$arr['rc_cache_header'] = Configuration::get('rc_cache_header');
		$arr['rc_header_cron'] = Configuration::get('rc_header_cron');     
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

		$this->_html .=  $this->display(__FILE__, 'views/templates/admin/content.tpl');





	}



	





}
