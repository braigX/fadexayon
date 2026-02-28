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
if (!defined('_PS_VERSION_')){
	exit;
}
class leveragebrowsercachecmsModuleFrontController extends ModuleFrontController
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

		if($token != Tools::getValue('token'))
			die('Invalid Token');

		$id_shop = (int)Tools::getValue('rc_id_shop');


		$offset = (int)Configuration::get('rc_cms_offset_'.$id_shop);

		$limit = (int)Configuration::get('rc_cms');

		if($limit < 1)
			$limit = 1;




	    $sql = 'SELECT  id_cms FROM `'._DB_PREFIX_.'cms_shop` '; 

	    if(!empty(Tools::getValue('rc_id_shop')))
	    	$sql .= ' WHERE id_shop ='.(int)$id_shop;

	    $sql .=' LIMIT '.(int)$limit .' OFFSET '.(int)$offset;	


		$results = Db::getInstance()->executeS($sql);
		$url = '';

		if(!$results)
		{
			Configuration::updateValue('rc_cms_offset_'.$id_shop, 0);	
			die(json_encode(array('new_offset' => 0)));
		}

		$link = new Link();
		$langs = Language::getLanguages(true); 

		if($results)
		{
			foreach($results as $res)
			{

				foreach($langs as $lang)
				{

					if($id_shop <= 0)
						$url = $link->getCMSLink((int)$res['id_cms'], NULL, NULL, (int)$lang['id_lang']);
					else
						$url = $link->getCMSLink((int)$res['id_cms'], NULL, NULL, (int)$lang['id_lang'], $id_shop);
					
					echo 'Cached -> '.$url.'<br/>';
					$content = leveragebrowsercache::get_url_content_static($url);
				}		
			}

			Configuration::updateValue('rc_cms_offset_'.$id_shop, $offset + count($results)  );	
			die(json_encode(array('new_offset' => $offset + count($results) )));

		}
	}
}