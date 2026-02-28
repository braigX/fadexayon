<?php
/***
*    2007-2025 PrestaShop
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
require_once(dirname(__FILE__).'/../ultimateimagetool.php');
$token = Configuration::get('uit_token');
if($token != Tools::getValue('token'))
	die('Invalid Token');
Configuration::updateValue('uit_cron_last_execution', date('Y-m-d H:i:s')  );
$token = Configuration::get('uit_token');
$limit = (int)Configuration::get('uit_products'); 
$uit_compress_os = (int)Configuration::get('uit_compress_os'); 

$quality = (int)Configuration::get('uit_cron_quality'); 

if($limit <= 0)
	$limit = 1;

if($quality <= 0)
	$quality = 80;

	$sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush`  WHERE processed = 0 ORDER BY id DESC LIMIT '.(int)$limit;

$to_process = Db::getInstance()->executes($sql);


if($to_process)
{
foreach($to_process as $tp)
{
	
	$image_type =  pSQL($tp['image_size']);

	$url = '';
	$langs = Language::getLanguages(true); 
	$object_id = $tp['object_id'];
	$link = new Link();

	$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = 0, `new_size` = 0, `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
	$row = Db::getInstance()->execute($sql);

	$valid = true;

	if(Tools::strlen(	$image_type ) <= 1 && $uit_compress_os  == 0)
		$valid = false;

	if($valid){

	if($tp['object_type'] == 'product' || empty($tp['object_type'] ))
	{
	
		foreach($langs as $l)
		{
		
			$product = new Product($tp['object_id'], false, $l['id_lang']);
			$images = $product->getImages($l['id_lang']);
		
			foreach($images as $image)
			{
			
                $image_old = new Image($image['id_image']);
                    
                if(Tools::strlen(  $image_type ) > 0)
                {
                    $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'.jpg';
                    $image_path2x = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'2x.jpg';
                }
                else
                {
                    $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'.jpg';
                    $image_path2x = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'2x.jpg';
                }


				$optimized_image = ultimateimagetool::get_optimized_image_api($image_path, $quality );
		
				if (!isset($optimized_image['error'])) 
				{	
					if(isset($optimized_image['percent']))
					{
						if((int)$optimized_image['percent'] > 0)
						{
							$image_old = new Image($image['id_image']);




							if (file_exists($image_path)  ) 
							{
								if(isset($optimized_image['dest']) && is_writable($image_path))
								{
									$new_image =  ultimateimagetool::get_url_content2($optimized_image['dest']);
									if($new_image && is_writable($image_path)   )
									{

										$true = file_put_contents($image_path, $new_image );
									 	if($true)
									 	{
											$saved_before = (int)Configuration::get('uit_saved_'.$image_type );
											$saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
											Configuration::updateValue('uit_saved_'.$image_type, $saved_after  );
											$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
											$row = Db::getInstance()->execute($sql);
									 	}
								 	}
								}
							}
						}
						else
						{
							$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
							$row = Db::getInstance()->execute($sql);
						}
					}
					else
					{
						$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
						$row = Db::getInstance()->execute($sql);
					}	
				}
			}
		}	
	}
	elseif($tp['object_type'] == 'category' )
	{
		foreach($langs as $l)
			{	
				$category = new Category($tp['object_id'], $l['id_lang']);
				$id_category = $tp['object_id'];

				if(!empty($category->id_image))
				{
					$imageLink = ultimateimagetool::get_domain_prefix().$link->getCatImageLink($category->link_rewrite, $category->id_category, $image_type);
					$image_ext = explode('.', $imageLink);
					$image_ext = end($image_ext);
					$imageLink = str_replace('.webp', '.jpg', $imageLink);





                    if(Tools::strlen(  $image_type ) > 0){
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'.'.$image_ext;
                        $image_path2x = str_replace('.'.$image_ext, '2x.'.$image_ext, $image_path);
                                            
                    }
                    else{
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'.'.$image_ext;
                        $image_path2x = str_replace('.'.$image_ext, '2x.'.$image_ext, $image_path);
                    }


					$optimized_image = ultimateimagetool::get_optimized_image_api($image_path, $quality);

					if (!isset($optimized_image['error'])) 
					{
						if(isset($optimized_image['percent']))
						{
							if((int)$optimized_image['percent'] > 0)
							{
								 if(Tools::strlen(	$image_type ) > 0)
								 	$image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'.'.$image_ext;
								 else
								 	$image_path = _PS_CAT_IMG_DIR_.$category->id_category.'.'.$image_ext;


 								if (file_exists($image_path) ) 
								{
								 	if(isset($optimized_image['dest']) && is_writable($image_path))
								 	{
									 	$new_image = ultimateimagetool::get_url_content2($optimized_image['dest']);
									 	if($new_image  && is_writable($image_path))
									 	{
										 	$true = file_put_contents($image_path, $new_image );
								 			if($true)
								 			{
												$saved_before = (int)Configuration::get('uit_saved_cat_'.$image_type );
												$saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
												Configuration::updateValue('uit_saved_cat_'.$image_type, $saved_after  );
												$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1  WHERE  `id` ='.(int)$tp['id'];
												$row = Db::getInstance()->execute($sql);
								 			}	
									 	}
								 	}
								}
							}
							else
							{
								$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
								$row = Db::getInstance()->execute($sql);
							}	
						}
						else
						{
							$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
							$row = Db::getInstance()->execute($sql);
						}
					}	
				}
			}
	}
	elseif($tp['object_type'] == 'supplier' )
	{
					$id_supplier = $tp['object_id'];
					if(Tools::strlen(	$image_type ) > 0)
					{
						$image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'.jpg';  
						$imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/su/' . (int) $id_supplier .'-'.$image_type.'.jpg';  
					}
				    else
				    {
						$image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
						$imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/su/' . (int) $id_supplier . '.jpg';
				    }
					$image_ext = explode('.', $imageLink);
					$image_ext = end($image_ext);
					$imageLink = str_replace('.webp', '.jpg', $imageLink);
					$optimized_image = ultimateimagetool::get_optimized_image_api($image_path, $quality);




		
					$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = 0, `new_size` = 0, `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
					$row = Db::getInstance()->execute($sql);
				

 					if (file_exists($image_path)  ) 
					{
						if (!isset($optimized_image['error'])) 
						{
							if(isset($optimized_image['percent']))
							{
								if((int)$optimized_image['percent'] > 0)
								{
									if(isset($optimized_image['dest']) && is_writable($image_path))
									{
										 	$new_image = ultimateimagetool::get_url_content2($optimized_image['dest']);
										 	if($new_image  && is_writable($image_path))
										 	{
											 	$true = file_put_contents($image_path, $new_image );
									 			if($true)
									 			{
													$saved_before = (int)Configuration::get('uit_saved_sup_'.$image_type );
													$saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
													Configuration::updateValue('uit_saved_sup_'.$image_type, $saved_after  );
													$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1  WHERE `id` ='.(int)$tp['id'];
														$row = Db::getInstance()->execute($sql);
									 			}
										 	}
									 }
								}
							}
							else
							{
								$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
								$row = Db::getInstance()->execute($sql);
							}	
						}
						else
						{
							$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
							$row = Db::getInstance()->execute($sql);
						}
					}	
	}
	elseif($tp['object_type'] == 'manufacturer' )
	{
					$id_manufacturer = $tp['object_id'];
					if(Tools::strlen(	$image_type ) > 0)
					{
						$image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'.jpg';  
						$imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/m/' . (int) $id_manufacturer .'-'.$image_type.'.jpg';  
					}
				    else
				    {
						$image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg';
						$imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/m/' . (int) $id_manufacturer . '.jpg';
				    }
					$image_ext = explode('.', $imageLink);
					$image_ext = end($image_ext);
					$imageLink = str_replace('.webp', '.jpg', $imageLink);
					$optimized_image = ultimateimagetool::get_optimized_image_api($image_path, $quality);


 					if (file_exists($image_path) ) 
					{
						if (!isset($optimized_image['error'])) 
						{
							if(isset($optimized_image['percent']))
							{
								if((int)$optimized_image['percent'] > 0)
								{
									 	if(isset($optimized_image['dest']) && is_writable($image_path))
									 	{
										 	$new_image = ultimateimagetool::get_url_content2($optimized_image['dest']);
										 	if($new_image  && is_writable($image_path))
										 	{
											 	$true = file_put_contents($image_path, $new_image );
									 			if($true)
									 			{
													$saved_before = (int)Configuration::get('uit_saved_sup_'.$image_type );
													$saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
													Configuration::updateValue('uit_saved_sup_'.$image_type, $saved_after  );
													$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1  WHERE id ='.(int)$tp['id'];
														$row = Db::getInstance()->execute($sql);
									 			}	
										 	}
									 	}
									 }
								}
								else
								{
									$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
									$row = Db::getInstance()->execute($sql);
								}
							}
							else
							{
								$sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(float)($optimized_image['src_size'] / 1024).', `new_size` = '.(float)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", `processed` = 1  WHERE `id` ='.(int)$tp['id'];
								$row = Db::getInstance()->execute($sql);
							}
						}	
	}}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
DONE
</body>
</html>