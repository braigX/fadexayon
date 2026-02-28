<?php
/**
 * Advanced plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please office@advancedplugins.com
 *
 * @author    Advanced Plugins <office@advancedplugins.com>
 * @copyright Advanced Plugins
 * @license   commercial
 */




if (!defined('_PS_VERSION_')) { exit; }
header("Access-Control-Allow-Origin: *");

class ultimateimagetoolajaxswapModuleFrontController extends ModuleFrontController

{

	public $ssl = true;

	private $function_pre = "ajax_";

	private $prefix = 'http://' ;



	/***

	 * @see FrontController::initContent()

	 */





	public function initContent()

	{

		 parent::initContent();



		// die('xxxx');



		if (Tools::getIsset('action'))

		{



			$function_name = $this->function_pre.Tools::getValue('action');

			

			$this->set_prefix();



			if (method_exists('ultimateimagetoolajaxswapModuleFrontController', $function_name))

				$this->$function_name();

		}



	

	    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 

			$this->setTemplate('module:ultimateimagetool/views/templates/front/sitemap.tpl');



		die();

		

	}







	private function set_prefix()

	{

		$PS_SSL_ENABLED = (int)Configuration::get('PS_SSL_ENABLED');

		

		if(	$PS_SSL_ENABLED === 1)

			$this->prefix = 'https://';



	}



	private function ajax_get_all_swap_images()

	{

		$position = Configuration::get('uit_mouse_hover_position');

		$products = json_decode(Tools::getValue('products'), true);



		$uit_mouse_hover_thumb = Configuration::get('uit_mouse_hover_thumb');



		$answer = array();

		$link = new Link();

		$context = Context::getContext();

		$id_lang = $context->cookie->id_lang;



		$image_type = 'home_'.'default';

		$image_type_ts =  'cart_'.'default';

		$image_type_ps =  'home_'.'default';

		$set_type = false;

		$set_type_ps = false;

		

		if($uit_mouse_hover_thumb == 'enabled')

		{

			$set_type = Configuration::get('uit_hover_image_type');

			$set_type_ts = Configuration::get('uit_hover_image_ts');

			$set_type_ps = Configuration::get('uit_hover_image_ps');			

		}





		if($set_type)

			$image_type = $set_type;



		if($set_type_ps)

			$image_type_ps = $set_type_ps;



		if($set_type_ps)

			$image_type_ts = $set_type_ps;





		foreach($products as $prd)

		{

			$image_id = NULL;





		     $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NOT NULL ORDER BY position DESC ' ) ;

		     $answer[$prd]['all'] = array();

	 

		    if($i)

		    {	

		    	$product = new Product($prd, false, $id_lang );

		    

		    	foreach($i as $ii)

		    	{

			    	$image_id = $ii['id_image'];

					if($uit_mouse_hover_thumb == 'enabled')

					{

						$arr = array();

						$arr['small'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ts);

						$arr['big'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ps);

						$answer[$prd]['all'][] = $arr;			

					}	    		

		    	}



		    }






		  //  var_dump(Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NULL ORDER BY position ASC LIMIT 11' ) );
			if($position == 'second_image')
		        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NULL ORDER BY position ASC LIMIT 1' ) ;
			else
		        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NULL ORDER BY position DESC LIMIT 1' ) ;

	 

		    if($i)

		    {	

		    	$product = new Product($prd, false, $id_lang );

		 

		    	foreach($i as $ii)

		    	{

			    	$image_id = $ii['id_image'];

			    	$imageLink = $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type);

			    	$answer[$prd]['second'] = $imageLink;



					if($uit_mouse_hover_thumb == 'enabled')

					{

						$arr = array();

						$arr['small'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ts);

						$arr['big'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ps);

						$answer[$prd]['all'][] = $arr;			

					}	    		

		    	}



		    }













		}



	    die(

	    			json_encode(

	    					array(

	    							'error' => 0,

	    							'images' => $answer

	    						)

	    				)

	    		);





	}



	private function ajax_get_swap_image()

	{



		$position = Configuration::get('uit_mouse_hover_position');

		$id_product = Tools::getValue('id_product');



		$image_id = NULL;



		if($position == 'second_image')

	        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$id_product.' and cover is NULL ORDER BY position ASC ' ) ;

		else

	        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$id_product.' and cover is NULL ORDER BY position DESC ' ) ;

	 



	    if(!$i)

	    	die(

	    			json_encode(

	    					array(

	    							'error' => 1

	    						)

	    				)

	    		);



	   	$image_id = $i['id_image'];



		$link = new Link();

		$context = Context::getContext();

		$id_lang = $context->cookie->id_lang;

		$product = new Product($id_product, false, $id_lang );

		$image_type = 'home_'.'default';

		$set_type = Configuration::get('uit_hover_image_type');



		if($set_type == $image_type)

			$image_type = $set_type;



		$imageLink = $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type);



	    die(

	    			json_encode(

	    					array(

	    							'error' => 0,

	    							'img_src' => $imageLink

	    						)

	    				)

	    		);

	    





	}





}