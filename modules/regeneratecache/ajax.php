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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');


Class Ajax_controller
{
	private $function_pre = "ajax_";

	public function __construct()
	{

		if (Tools::getIsset('action'))
		{

			$token = Configuration::get('rc_token');

			if($token != Tools::getValue('token'))
				die('Invalid Token');

			$function_name = $this->function_pre.Tools::getValue('action');

			if (method_exists('ajax_controller', $function_name))
				$this->$function_name();
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
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.114 Mobile Safari/537.36');      
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
  	curl_setopt($curl, CURLOPT_MAXREDIRS, 8);
 	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
  	$contents = curl_exec($curl);
		curl_close($curl);
 		return $contents;

	}

	private function ajax_regenerate_products()
	{
		$offset = (int)Tools::getValue('offset');
		$preview = (int)Tools::getValue('preview');
    $sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product`  WHERE active = 1 ORDER by id_product DESC  LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$url = '';
		$url_arr = array();

		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 
		

			foreach($langs as $lang)
			{
				$link = new Link();
				$url =  $link->getProductLink((int)$results['id_product'], null, null, null, (int)$lang['id_lang']);
				//$url = _PS_BASE_URL_.'/index.php?id_product='.$results['id_product'].'&controller=product&id_lang='.$lang['id_lang'];
				$url_arr[] = $url;

				if($preview == 1)
					$this->get_url_content($url.'?content_only=1');

				
				$content = $this->get_url_content($url);
				
			}
		
				if($content)
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0,
											'url' => $url_arr
										)
								)	
						);
				}
				else
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0,
											'url' => $url_arr
										)
								)	
						);
				}

		}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1,
											'url' => $url
										)
								)	
						);

	}

	private function ajax_regenerate_categories()
	{

		$offset = (int)Tools::getValue('offset');

		$url = '';
    $sql = 'SELECT  id_category FROM `'._DB_PREFIX_.'category`  WHERE active = 1 LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$link = new Link();


		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 

			foreach($langs as $lang)
			{
	
				$url =  $link->getCategoryLink((int)$results['id_category'], null, (int)$lang['id_lang']);
				//$url = _PS_BASE_URL_.'/index.php?id_category='.$results['id_category'].'&controller=category&id_lang='.$lang['id_lang'];
				$content = $this->get_url_content($url);
				
			}

			if($content)
							{
								die(
										json_encode(
												array(
														'new_offset' => $offset + 1,
														'is_end' => 0,
														'url' => $url
													)
											)	
									);
							}
							else
							{
								die(
										json_encode(
												array(
														'new_offset' => $offset + 1,
														'is_end' => 0,
														'url' => $url
													)
											)	
									);
							}

					}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1
										)
								)	
						);




	}



private function ajax_regenerate_categories_pages()
{	

		$offset = (int)Tools::getValue('offset');
		$offset_pagination = (int)Tools::getValue('current_category_page');

	    $sql = 'SELECT  id_category FROM `'._DB_PREFIX_.'category`  WHERE active = 1 LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$link = new Link();
		$url = '';
		$pages_nb = '';
		$nbProducts = 1;


		if($offset_pagination < 1)
			$offset_pagination = 1;


		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 

			foreach($langs as $lang)
			{

				$cat  = new Category($results['id_category']);
				$nbProducts = $cat->getProducts(null, null, null, NULL, NULL, true);

				if($nbProducts == 0)
					$nbProducts = 1;

    			$default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
     		    $pages_nb = ceil( $nbProducts  / (int)$default_products_per_page);    		    


				
				$url =  $link->getCategoryLink((int)$results['id_category'], null, (int)$lang['id_lang']);

	      		if($offset_pagination > 1)
				    $url .= '?p='.$offset_pagination;
				
				//$url = _PS_BASE_URL_.'/index.php?id_category='.$results['id_category'].'&controller=category&id_lang='.$lang['id_lang'].'&p='.$offset_pagination;
				$content = $this->get_url_content( $url );

			}
				
				if($content)
				{

					if( $offset_pagination <=   $pages_nb  )
						die(
								json_encode(
										array(
												'new_offset' => $offset + 1,
												'is_end' => 0,
												'last_page' => 0,
												'url' =>  $url,
												'nb_product' => $nbProducts,
												'pages' => $pages_nb,
												'l' => 1 
											)
									)	
							);


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0,
											'last_page' => 1,
											'url' =>  $url,
											'nb_product' => $nbProducts,
											'pages' => $pages_nb ,
											'l' => 2
										)
								)	
						);

				}
				else
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0,
											'url' =>  $url,
											'last_page' => 1,
											'nb_product' => $nbProducts,
											'pages' => $pages_nb,
											'l' => 3
										)
								)	
						);
				}
		}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1,
											'nb_product' => $nbProducts,
											'pages' => $pages_nb,
											'l' => 4
										)
								)	
						);




	}



private function ajax_regenerate_manufacturers()
{
		
		$offset = (int)Tools::getValue('offset');


	    $sql = 'SELECT  id_manufacturer FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1 LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$link = new Link();


		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 

			foreach($langs as $lang)
			{
				//$url = _PS_BASE_URL_.'/index.php?id_manufacturer='.$results['id_manufacturer'].'&controller=manufacturer&id_lang='.$lang['id_lang'];
				$url = $link->getManufacturerLink((int)$results['id_manufacturer'], NULL, (int)$lang['id_lang']);
				$content = $this->get_url_content($url);
				
			}
			
				if($content)
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}
				else
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}

		}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1
										)
								)	
						);



	}


private function ajax_regenerate_suppliers()
{
		
		$offset = (int)Tools::getValue('offset');


	    $sql = 'SELECT  id_supplier FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1 LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$link = new Link();


		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 

			foreach($langs as $lang)
			{
				//$url = _PS_BASE_URL_.'/index.php?id_supplier='.$results['id_supplier'].'&controller=supplier&id_lang='.$lang['id_lang'];
				$url = $link->getSupplierLink((int)$results['id_supplier'], NULL, (int)$lang['id_lang']);
				$content = $this->get_url_content($url);
				
			}
			
				if($content)
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}
				else
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}
				
		}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1
										)
								)	
						);



	}



private function ajax_regenerate_cms()
	{

		$offset = (int)Tools::getValue('offset');
    $sql = 'SELECT  id_cms FROM `'._DB_PREFIX_.'cms`  WHERE active = 1 LIMIT 1 OFFSET '.(int)$offset;	
		$results = Db::getInstance()->executeS($sql);
		$link = new Link();

		if($results)
		{
			$results = $results[0];
			$langs = Language::getLanguages(true); 

			foreach($langs as $lang)
			{
				//$url = _PS_BASE_URL_.'/index.php?id_cms='.$results['id_cms'].'&controller=cms&id_lang='.$lang['id_lang'];
				$url = $link->getCMSLink((int)$results['id_cms'], NULL, NULL, (int)$lang['id_lang']);
				$content = $this->get_url_content($url);
				
				if($content)
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}
				else
				{
					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 0
										)
								)	
						);
				}
			}
			
		}


					die(
							json_encode(
									array(
											'new_offset' => $offset + 1,
											'is_end' => 1
										)
								)	
						);

	}




	private function check_employee()
	{
		$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
		$employee = new Employee((int)$cookie->id_employee);

		if (!Validate::isLoadedObject($employee) || !$employee->checkPassword((int)$cookie->id_employee, $cookie->passwd))
			die('you must login as employee');
		
	}


}

	$ajax = new Ajax_controller();