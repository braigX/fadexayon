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

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
require_once(dirname(__FILE__).'/../regeneratecache.php');

$token = Configuration::get('rc_token');
$header_cron = Configuration::get('rc_header_cron');

if($token != Tools::getValue('token'))
	die('Invalid Token');

$offset = (int)Configuration::get('rc_supplier_offset');
$limit = (int)Configuration::get('rc_suppliers');

if($limit < 1)
	$limit = 1;
  $sql = 'SELECT  id_supplier FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1 LIMIT '.(int)$limit.' OFFSET '.(int)$offset;	
  $results = Db::getInstance()->executeS($sql);
  $url = '';

if(!$results)
{
	Configuration::updateValue('rc_supplier_offset', 0);	
	die(json_encode(array('new_offset' => 0)));
}


$langs = Language::getLanguages(true); 

if($results)
{
	foreach($results as $res)
	{

		foreach($langs as $lang)
		{
			$link = new Link();
			$url = $link->getSupplierLink((int)$res['id_supplier'], NULL, (int)$lang['id_lang']);
          if ($header_cron == 0) { $content = RegenerateCache::get_url_content_static_mobile($url); }
          if ($header_cron == 1) { $content = RegenerateCache::get_url_content_static($url); }
          if ($header_cron == 2) {
					$content = RegenerateCache::get_url_content_static($url);
          $content = RegenerateCache::get_url_content_static_mobile($url); }  
		}		
	}

	Configuration::updateValue('rc_supplier_offset', $offset + count($results)  );	
	die(json_encode(array('new_offset' => $offset + count($results) )));

}