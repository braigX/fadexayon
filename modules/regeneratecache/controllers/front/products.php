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

class regeneratecacheproductsModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	private $function_pre = "ajax_";
	private $prefix = 'http://' ;

	/**
	 * @see FrontController::initContent()
	 */

	public function initContent()
	{
		parent::initContent();



		$token = Configuration::get('rc_token');
    $header_cron = Configuration::get('rc_header_cron');

		if($token != Tools::getValue('token'))
			die('Invalid Token');

		$id_shop = (int)Tools::getValue('rc_id_shop');

		$offset = (int)Configuration::get('rc_product_offset_'.$id_shop);
		$limit = (int)Configuration::get('rc_products');

		if($limit < 1)
			$limit = 1;


		$sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product_shop`  WHERE active = 1 ';
			  
		if($id_shop > 0)
			$sql .= ' AND id_shop ='.(int)$id_shop;
			   
		$sql .= ' ORDER by id_product DESC  LIMIT '.(int)$limit.' OFFSET '.(int)$offset;	




		$results = Db::getInstance()->executeS($sql);
		$url = '';

		if(!$results)
		{
			Configuration::updateValue('rc_product_offset_'.$id_shop, 0);	
			die(json_encode(array('new_offset_'.$id_shop => 0)));
		}


		$langs = Language::getLanguages(true); 
		$link = new Link();


		if($results)
		{
			foreach($results as $res)
			{
			
				foreach($langs as $lang)
				{


					if($id_shop > 0)
						$url =  $link->getProductLink((int)$res['id_product'], null, null, null, (int)$lang['id_lang'], $id_shop);
					else
						$url =  $link->getProductLink((int)$res['id_product'], null, null, null, (int)$lang['id_lang']);

					echo 'Cached -> '.$url.'<br/>';
          if ($header_cron == 0) { $content = RegenerateCache::get_url_content_static_mobile($url); }
          if ($header_cron == 1) { $content = RegenerateCache::get_url_content_static($url); }
          if ($header_cron == 2) {
					$content = RegenerateCache::get_url_content_static($url);
          $content = RegenerateCache::get_url_content_static_mobile($url); }          
			
					if((int)Configuration::get('rc_products_preview') == 1) {
          if ($header_cron == 0) { $content = RegenerateCache::get_url_content_static_mobile($url.'?content_only=1'); }
          if ($header_cron == 1) { $content = RegenerateCache::get_url_content_static($url.'?content_only=1'); }
          if ($header_cron == 2) {
					$content = RegenerateCache::get_url_content_static_mobile($url.'?content_only=1');
          $content = RegenerateCache::get_url_content_static($url.'?content_only=1'); }
          }
				}		
			}

			Configuration::updateValue('rc_product_offset_'.$id_shop, $offset + count($results)  );	
			die(json_encode(array('new_offset_'.$id_shop => $offset + count($results) )));

		}

	}
}
