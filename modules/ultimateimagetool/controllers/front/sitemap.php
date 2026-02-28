<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/***
 * @since 1.5.0
 */


if (!defined('_PS_VERSION_')) { exit; }


class ultimateimagetoolsitemapModuleFrontController extends ModuleFrontController
{
	public $ssl = true;




	public function clean($string) 
	{
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}


	/***
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		 parent::initContent();



		$token = Configuration::get('uit_token');

		if($token != Tools::getValue('token'))
			die('Invalid Token');


		$sql = 'SELECT *
						FROM `'._DB_PREFIX_.'product` p
						WHERE active = 1
						ORDER BY `id_product` ASC ';

		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		$link = new Link();
		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');


		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'; 

		$is_ssl = (int)Configuration::get('PS_SSL_ENABLED');
		$image_size = '';

		if($is_ssl === 1)
			$http = 'https';
		else
			$http = 'http';

		if(Configuration::get('sitemap_image_size'))
			$image_size = Configuration::get('sitemap_image_size');

		if(!empty($products)):
		    $link = new Link();
		    foreach($products as $prd): 
				
				$product = new Product($prd['id_product'], false, $id_lang);

				$cover = $product->getCover($product->id);
				$url = $link->getProductLink($product, null, null, null, $id_lang);

				$images = $product->getImages($id_lang);


			    
			     if($images):
			     	if(Tools::strlen($url) > 10)
			     	{
				     	$xml .= '<url><loc>'.$url.'</loc>';

				    	foreach($images as $img):
				    		$image_url = $link->getImageLink($product->link_rewrite, $img['id_image'], $image_size); 

				    		if(Tools::strlen($image_url) > 6)
				    		{
				 
							    $xml .= '<image:image><image:loc><![CDATA['.$http .'://'.$image_url.']]></image:loc>';
							    if(!empty($img['legend']))
							    	$xml .='<image:caption><![CDATA['.Tools::substr($img['legend'], 0,9000).']]></image:caption>'; 
							    else
							    	$xml .='<image:caption><![CDATA['.Tools::substr($product->name, 0,9000).']]></image:caption>'; 

							    $xml .= '</image:image>'; 	    			
				    		}

				    	endforeach; 
				    	$xml .= '</url>';	     		
			     	}

			    endif;
				
			endforeach; 
		endif;
		$xml .= '</urlset>';
		echo $xml;

	    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
			$this->setTemplate('module:ultimateimagetool/views/templates/front/sitemap.tpl');

		die();
		
	}

	
}
