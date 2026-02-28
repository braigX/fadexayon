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

class regeneratecachecategoriespagesModuleFrontController extends ModuleFrontController
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
			$offset = (int)Configuration::get('rc_categories_p_offset_'.$id_shop);
			$offset_pagination = (int)Configuration::get('rc_categories_pages_offset');

			if($offset_pagination < 1)
				$offset_pagination = 1;
  			$limit = (int)Configuration::get('rc_categories');

			if($limit < 1)
				$limit = 1;
  	    $sql = 'SELECT  id_category FROM `'._DB_PREFIX_.'category_shop`  ';
		    
		    if(!empty(Tools::getValue('rc_id_shop')))
		    	$sql .= ' WHERE id_shop ='.(int)$id_shop;
  		    $sql .= ' LIMIT '.(int)$limit.' OFFSET '.(int)$offset;
    			$results = Db::getInstance()->executeS($sql);
		    	$link = new Link();
	     		$url = '';
	     		$pages_nb = '';
		    	$nbProducts = 1;
    			$langs = Language::getLanguages(true); 

			if($results)
			{
				foreach($results as $res)
				{
					foreach($langs as $lang)
					{
						$cat  = new Category($res['id_category']);
						$nbProducts = $cat->getProducts(null, null, null, NULL, NULL, true);
						
						if($nbProducts == 0)
							$nbProducts = 1;

						$default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
						$pages_nb = ceil( $nbProducts  / (int)$default_products_per_page);

						if($id_shop <= 0)
							$url =  $link->getCategoryLink((int)$res['id_category'], null, (int)$lang['id_lang']);
						else
							$url =  $link->getCategoryLink((int)$res['id_category'], null, (int)$lang['id_lang'], null, $id_shop);
				
						echo 'Cached -> '.$url.'<br/>';
			      		
			      		for($i = 1; $i<=3; $i++)
			      		{

				      		if($offset_pagination > 1)
							    $url .= '?p='.$offset_pagination;

          if ($header_cron == 0) { $content = RegenerateCache::get_url_content_static_mobile($url); }
          if ($header_cron == 1) { $content = RegenerateCache::get_url_content_static($url); }
          if ($header_cron == 2) {
					$content = RegenerateCache::get_url_content_static($url);
          $content = RegenerateCache::get_url_content_static_mobile($url); }

							if($content)
							{
								Configuration::updateValue('rc_categories_pages_offset_'.$id_shop, $offset_pagination + 1 );	
							}
							else
							{
								Configuration::updateValue('rc_categories_p_offset_'.$id_shop, $offset  + 1 );	
								Configuration::updateValue('rc_categories_pages_offset_'.$id_shop, 0 );	

								die(json_encode(array('new_offset' => $offset + count($results) )));
							}	

			      		}




					}

				}
			}


		die(json_encode(array('new_offset' => $offset )));


	}

}