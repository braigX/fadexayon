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

require_once(_PS_MODULE_DIR_ .'/ultimateimagetool/vendor/autoload.php');
use WebPConvert\WebPConvert;
header("Access-Control-Allow-Origin: *");
class ultimateimagetoolajaxController extends ModuleAdminControllerCore
{
    public $ssl = true;
    private $function_pre = "ajax_";
    private $prefix = 'http://' ;
    /***
     * @see FrontController::initContent()
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $cookie = new Cookie('psAdmin');
        $id_employee = (int)$cookie->id_employee;
        if($id_employee == 0)
            die();
        parent::__construct();
        $this->meta_title = $this->l('Ajax Handler');
        if (Tools::getIsset('action'))
        {
            $function_name = $this->function_pre.Tools::getValue('action');
            if (method_exists('ultimateimagetoolajaxController', $function_name))
                $this->$function_name();
        }
         if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
            $this->setTemplate('ajax.tpl');
        die();
    }
    private function set_prefix()
    {
        $PS_SSL_ENABLED = (int)Configuration::get('PS_SSL_ENABLED');
        //$PS_SSL_ENABLED_EVERYWHERE =  (int)Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        if( $PS_SSL_ENABLED === 1)
            $this->prefix = 'https://';
    }
    private function get_url_content($url)
    {
        $content =  Tools::file_get_contents($url, false, null, 25);
        if(empty($content))
            return false;
        return $content;
    }
    private function ajax_update_configuration()
    {
        Configuration::updateValue(Tools::getValue('variable'), Tools::getValue('value'));
        if(
            Tools::getValue('variable') == 'uit_enable_gzip'  || 
            Tools::getValue('variable') == 'uit_use_webp' || 
            Tools::getValue('variable') == 'uit_zoom' || 
            Tools::getValue('variable') == 'uit_use_webp_termination'
        )
        {
            if(method_exists('Tools','generateHtaccess'))
                Tools::generateHtaccess();
            if(method_exists('Tools','clearAllCache'))
                Tools::clearAllCache();
        }
        die(json_encode(array('is_end' => 1,'error' => 0)));
    }
    private function ajax_update_wepb_status()
    {
        Configuration::updateValue('uit_use_webp', (int)Tools::getValue('val'));
        die(json_encode(array('is_end' => 1,'error' => 0)));
    }
    private function ajax_update_uit_auto_webp()
    {
        Configuration::updateValue('uit_auto_webp', (int)Tools::getValue('val'));
        die(json_encode(array('is_end' => 1,'error' => 0)));
    }
    private function ajax_get_subfolders_and_images()
    {
        $subdirectories =  ultimateimagetool::getDirContents(Tools::getValue('folder'));
        if(!empty(Tools::getValue('folder')))
            $subdirectories[] = Tools::getValue('folder');
        $allimages = array();
        if(!empty($subdirectories))
        {
            $subdirectories = array_unique($subdirectories);
            $allimages = ultimateimagetool::getAllImgs($subdirectories);
        }
        //if(sizeof($allimages) > 7000) 
        //  die(json_encode(array('error' => 1, 'error_msg' => 'There are too many images, try getting subfolders one by one')));
        die(json_encode(array('error' => 0, 'images' => $allimages)));
    }
    private function ajax_get_subfolders()
    {
        $folder = Tools::getValue('folder');
        if(empty(Tools::getValue('folder')))
            $folder = _PS_ROOT_DIR_;
        $subdirectories =  ultimateimagetool::get_subdirectories($folder);
        if(!empty($subdirectories))
        {   echo '<ul>';
            foreach($subdirectories as $subdir)
            {
                $expl = explode('/', $subdir);
                echo ' <li  class="jstree-closed" rel="'.$subdir.'">'.end($expl).'</li>';
            }
            echo '</ul>';
        }
    }
    private function ajax_reset_image_size()
    {
        $image_type = Tools::getValue('image_type');
        $type = Tools::getValue('type');
        if($image_type  == 'original')
            $image_type = '';
        if($type == 'product')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_'.pSQL($image_type), 0);
        }
        elseif($type == 'category')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_cat_'.pSQL($image_type), 0);
        }
        elseif($type == 'manufacturer')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_manuf_'.pSQL($image_type), 0);
        }
        elseif($type == 'supplier')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_sup_'.pSQL($image_type), 0);
        }
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_reset_image_size_convert()
    {
        $image_type = Tools::getValue('image_type');
        $type = Tools::getValue('type');
        if($image_type  == 'original')
            $image_type = '';
        if($type == 'product')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_c_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_c_'.pSQL($image_type), 0);
        }
        elseif($type == 'category')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_c_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_cat_c_'.pSQL($image_type), 0);
        }
        elseif($type == 'manufacturer')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_c_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_manuf_c_'.pSQL($image_type), 0);
        }
        elseif($type == 'supplier')
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_c_'.pSQL($image_type) . '" ');
            Configuration::updateValue('uit_i_o_sup_c_'.pSQL($image_type), 0);
        }
        die(json_encode(array('is_end' => 1)));
    }
 
    private function ajax_image_quality()
    {
        $val = Tools::getValue('val');
        $type = Tools::getValue('type');
        if($type == 'product')
            Configuration::updateValue('uit_quality', pSQL($val));
        elseif($type == 'category')
            Configuration::updateValue('uit_quality_cat', pSQL($val));
        elseif($type == 'manufacturer')
            Configuration::updateValue('uit_quality_manuf', pSQL($val));
        elseif($type == 'supplier')
            Configuration::updateValue('uit_quality_sup', pSQL($val));
        elseif($type == 'module')
            Configuration::updateValue('uit_quality_module', pSQL($val));
        elseif($type == 'theme')
            Configuration::updateValue('uit_quality_theme', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_lazy_load_status()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_lazy_load', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_sitemap_image_type()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('sitemap_image_size', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_alt_tags_auto()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('alt_tags_auto', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_update_alt_syntax()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_alt_format', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_mouse_hover_status()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_mouse_hover', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_uit_mouse_hover_thumb()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_mouse_hover_thumb', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_uit_mouse_hover_ts()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_mouse_hover_ts', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_uit_mouse_hover_ps()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_mouse_hover_ps', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_mouse_hover_position()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_mouse_hover_position', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_mouse_hover_image_type()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_hover_image_type', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_cron_image_quality()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_cron_quality', pSQL($val));
        Configuration::updateValue('uit_quality', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function ajax_product_per_excution()
    {
        $val = Tools::getValue('val');
        Configuration::updateValue('uit_products', pSQL($val));
        die(json_encode(array('is_end' => 1)));
    }
    private function get_optimized_image($img_src, $quality = 70)
    {
        $service = 'http://api.resmush.it/ws.php?qlty='.$quality.'&img=';
        $path = $service . urlencode($img_src);
        $content = $this->get_url_content($path);
        $o = json_decode( $content, true);
        if(isset($o->error) || $o == NULL)
        {
            return array('is_end' => 1);
        }
        else
            return $o;
    }

    private function get_optimized_image_api($file, $quality = 70)
    {
        if(!file_exists($file))
             return array('is_end' => 1);

        if($quality < 60)
            $quality = 60;

        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];

    



        $output = new CURLFile($file, $mime, $name);
   
        $data = array(
            "files" => $output,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty='.(int)$quality);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyCustomUserAgent/1.0');
        curl_setopt($ch, CURLOPT_REFERER, 'https://example.com');
        $result = curl_exec($ch);

 
        if (curl_errno($ch)) {
            return array('is_end' => 1);
        }
        curl_close ($ch);
        $o = json_decode( $result, true);
        if(isset($o->error) || $o == NULL)
        {
            return array('is_end' => 1);
        }
        else
            return $o;
    }
    public function regenerateProducts($image, $image_type = '',$id_product = 0, $image_data = [])
    {
        $image_old = new Image($image['id_image']);
        if(Tools::strlen($image_type ) == 0 || empty($image_type))
            return false;
        $original_image = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'.jpg';
        $image_obj = new Image((int)$image['id_image']);
        $image_obj->id_product = (int)$id_product;
        $generated = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'.jpg';
        $generated2x = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'2x.jpg';
        if (file_exists($generated)) 
        {
            unlink($generated);
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) 
        {
            ImageManager::resize($original_image, $generated, $image_data['width'], $image_data['height']);
        } 
        else 
        {
           ImageResize($original_image, $generated, $image_data['width'], $image_data['height']);
        }
        if(Configuration::get('PS_HIGHT_DPI'))
        {
            if (file_exists($generated2x)) 
            {
                unlink($generated2x);
            }
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) 
                {
                    ImageManager::resize($original_image, $generated2x, $image_data['width']*2, $image_data['height']*2);
                } 
                else 
                {
                   ImageResize($original_image, $generated2x, $image_data['width']*2, $image_data['height']*2);
                }
        }        
        return true;
    }
    private function ajax_regenerate_image_sizes()
    {
        $regenerate_type = Tools::getValue('regenerate_type');
        $regenerate_size =  Tools::getValue('regenerate_size');
        $offset = Tools::getValue('offset');
        $context = Context::getContext();
        $id_lang = $context->cookie->id_lang;
        try
        {
            if($regenerate_type == 'products')
            {
                $sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product`  WHERE active = 1'; 
                $results = Db::getInstance()->getRow($sql);
                if($results)
                    $count = $results['count(id_product)'];
                else
                    $count = 0;
                $sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product`  WHERE active = 1  LIMIT 1 OFFSET '.(int)$offset;  
                $results = Db::getInstance()->executeS($sql);
                $image_types = ImageType::getImagesTypes('products');
                if($regenerate_size == 'all')
                {
                    if($results)
                    {
                        foreach($results as $res)
                        {
                            foreach($image_types as $it)
                            {
                                $product = new Product($res['id_product'], false, $id_lang);
                                $images = $product->getImages($id_lang);
                                foreach($images as $i)
                                {
                                    $this->regenerateProducts($i, $it['name'], $res['id_product'], $it);
                                }
                            }
                        }
                        die(
                                json_encode(array('error' => 0, 'total_products' => $count))
                            );
                    }
                    else
                    {
                        die(
                                json_encode(array('error' => 1))
                            );
                    }
                }
                else
                {
                    if($results)
                    {
                        foreach($results as $res)
                        {
                            foreach($image_types as $it)
                            {
                                if($it['name'] == $regenerate_size)
                                {
                                    $product = new Product($res['id_product'], false, $id_lang);
                                    $images = $product->getImages($id_lang);
                                    foreach($images as $i)
                                    {
                                        $this->regenerateProducts($i, $it['name'], $res['id_product'], $it);
                                    }                               
                                }
                            }
                        }
                    die(
                                json_encode(array('error' => 0, 'total_products' => $count))
                        );
                    }
                    else
                    {
                        die(
                                json_encode(array('error' => 1))
                            );
                    }
                }
            }
        } 
        //CATCH the exception if something goes wrong.
        catch (Exception $ex) {
            //Print out the exception message.
            echo $ex->getMessage();
        }
    }
    private function ajax_generate_tags()
    {
        $alt_syntax =  pSQL(Tools::getValue('alt_syntax'));
        $alt_apply =  (int)Tools::getValue('alt_apply');
        if($alt_apply == 2)
        {
            $sql = 'SELECT  * FROM `'._DB_PREFIX_.'image` i INNER JOIN `'._DB_PREFIX_.'image_lang` il ON i.id_image = il.id_image'; 
            $q = 'UPDATE `'._DB_PREFIX_.'image_lang` SET legend = "" ';
            Db::getInstance()->execute($q);
        }
        else
        {
            $sql = 'SELECT  * FROM `'._DB_PREFIX_.'image` i INNER JOIN `'._DB_PREFIX_.'image_lang` il ON i.id_image = il.id_image AND ( il.legend = ""  OR il.legend is NULL  )';   
        }
        $results = Db::getInstance()->executeS($sql);
        if(!empty($results))
        {
            foreach($results as $res)
            {
                $alt_tag = $alt_syntax;
                $product = new Product($res['id_product'], true, $res['id_lang']);
                $category = new Category($product->id_category_default, $res['id_lang']);
                $alt_tag  = str_replace(array('{PARENT_CATEGORY_NAME}', '{SUPPLIER_NAME}', '{MANUFACTURER_NAME}', '{PRODUCT_NAME}', '{PRODUCT_PRICE}', '{PRODUCT_SHORT_DESCRIPTION}', '{IMAGE_POSITION}', '{PRODUCT_REFERENCE}'), array($category->name, $product->supplier_name, $product->manufacturer_name, $product->name, $product->price, $product->description_short, $res['position'], $product->reference), $alt_tag);
                $q = 'UPDATE `'._DB_PREFIX_.'image_lang` SET legend = "'.pSQL($alt_tag).'" WHERE id_image ='.(int)$res['id_image'] .' AND id_lang ='.(int)$res['id_lang'];
                Db::getInstance()->execute($q);
            }
            $sql = 'SELECT  count(id_image) FROM `'._DB_PREFIX_.'image_lang`';  
            $results = Db::getInstance()->getRow($sql);
            $uit_total_images = (int)$results['count(id_image)'];
            $sql = 'SELECT  count(id_image) FROM `'._DB_PREFIX_.'image_lang` WHERE ( legend = "" OR legend is NULL )';  
            $results = Db::getInstance()->getRow($sql); 
            $uit_empty_images = (int)$results['count(id_image)'];
            die(
                    json_encode(array('error' => 0, 'error_msg' => $uit_empty_images.'/'.$uit_total_images ))
                );
        }
            die(
                    json_encode(array('error' => 1, 'error_msg' => 'There are no images without alt tags' ))
                );
    }
    private function ajax_regenerate_product_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $image_path = NULL;
        $optimized_image = array();
        $offset = (int)Configuration::get('uit_i_o_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_'.pSQL($image_type) . '" ';  
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_'.pSQL($image_type) . '" ');
        }   
        Configuration::updateValue('uit_i_o_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product`  WHERE active = 1 ORDER by id_product DESC  LIMIT 1 OFFSET '.(int)$offset; 
        $results = Db::getInstance()->executeS($sql);
        $id_product = 0;
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_product = $results['id_product'];
            $link = new Link();
            foreach($langs as $l)
            {   
                $product = new Product($results['id_product'], false, $l['id_lang']);
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
                    $optimized_image = $this->get_optimized_image_api( $image_path , $image_quality);
                    if (!isset($optimized_image['error'])) 
                    {
                        if(isset($optimized_image['percent']))
                        {
                            if((int)$optimized_image['percent'] > 0)
                            {
                                if (file_exists($image_path)) 
                                {
                                    if(isset($optimized_image['dest']) && is_writable($image_path))
                                    {
                                        $new_image = $this->get_url_content($optimized_image['dest']);
                                        if($new_image)
                                        {
                                            $true = file_put_contents($image_path, $new_image );
                                            if($true)
                                            {
                                                $saved_before = (int)Configuration::get('uit_saved_'.$image_type );
                                                $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                Configuration::updateValue('uit_saved_'.$image_type, $saved_after  );
                                                $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_product.' AND image_size = "'.pSQL($image_type).'" and object_type = "product"';
                                                $row = Db::getInstance()->getRow($sql);
                                                if(!$row)
                                                {
                                                    $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_product.', "product",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                                else
                                                {
                                                    $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_product.' AND image_size = "'.pSQL($image_type).'" AND object_type = "product"';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                            }   
                                        }
                                    }
                                }
                            }
                        }   
                    }
                    // COMPRESS 2x IMAGES
                    if(Configuration::get('PS_HIGHT_DPI'))
                    {
                        $optimized_image = $this->get_optimized_image_api( $image_path2x , $image_quality);
                        if (!isset($optimized_image['error'])) 
                        {
                            if(isset($optimized_image['percent']))
                            {
                                if((int)$optimized_image['percent'] > 0)
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
                                    if (file_exists($image_path)) 
                                    {
                                        if(isset($optimized_image['dest'])  && is_writable($image_path))
                                        {
                                            $new_image = $this->get_url_content($optimized_image['dest']);
                                            if($new_image)
                                            {
                                                $true = file_put_contents($image_path, $new_image );
                                                if($true)
                                                {
                                                    $saved_before = (int)Configuration::get('uit_saved_'.$image_type );
                                                    $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                    Configuration::updateValue('uit_saved_'.$image_type, $saved_after  );
                                                    $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_product.' AND image_size = "'.pSQL($image_type).'" and object_type = "product"';
                                                    $row = Db::getInstance()->getRow($sql);
                                                    if(!$row)
                                                    {
                                                        $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_product.', "product",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                        $row = Db::getInstance()->execute($sql);
                                                    }
                                                    else
                                                    {
                                                        $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_product.' AND image_size = "'.pSQL($image_type).'" AND object_type = "product"';
                                                        $row = Db::getInstance()->execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }   
                        }
                    }
                }
            }
        $sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product`  WHERE active = 1'; 
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_product)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'saved_after' =>    (int)Configuration::get('uit_saved_'.$image_type ),
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'json' => json_encode($optimized_image),
                                            'product_id' => $id_product
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_convert_manufacturer_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_manuf_c_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_c_'.pSQL($image_type) . '" ';  
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_c_'.pSQL($image_type) . '" ');
        }
        Configuration::updateValue('uit_i_o_manuf_c_'.$image_type, ($offset + 1));
        //$sql = 'DELETE  FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "'.('uit_i_o_manuf_'.$image_type).'"'; 
        //$results = Db::getInstance()->execute($sql);
        $sql = 'SELECT  id_manufacturer FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1 ORDER by id_manufacturer DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_manufacturer = 0;
        $optimized_image = array();
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if($imagewebp)
            $lossless = false;
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_manufacturer = $results['id_manufacturer'];
            $link = new Link();
            if(Tools::strlen(   $image_type ) > 0)
                $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'.jpg';
            else
                $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg';
            if(file_exists($image_path))
            {
                $image_destination = '';
                if(Tools::strlen(   $image_type ) > 0)
                {
                    $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'.jpg';
                    $image_destination =  _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'.webp';
                }
                else
                {
                    $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg';
                    $image_destination = _PS_MANU_IMG_DIR_.$id_manufacturer.'.webp';
                }
                if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                        unlink($image_destination);
                if((int)Configuration::get('uit_use_external_api') != 1)
                {
                    $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' =>$image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                }
                if(!file_exists($image_destination))
                {
                    $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                    if($image)
                        file_put_contents($image_destination, $image);
                }   
            }
            if(Configuration::get('PS_HIGHT_DPI'))
            {
                if(Tools::strlen($image_type) >0)
                    $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'2x.jpg';
                else
                    $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'2x.jpg';
                if(file_exists($image_path))
                {
                    $image_destination = '';
                    if(Tools::strlen(   $image_type ) > 0)
                    {
                        $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'2x.jpg';
                        $image_destination =  _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.$image_type.'2x.webp';
                    }
                    else
                    {
                        $image_path = _PS_MANU_IMG_DIR_.$id_manufacturer.'2x.jpg';
                        $image_destination = _PS_MANU_IMG_DIR_.$id_manufacturer.'2x.webp';
                    }
                    if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                        unlink($image_destination);
                    if((int)Configuration::get('uit_use_external_api') != 1)
                        $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' =>$image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                    if(!file_exists($image_destination))
                    {
                        $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                        if($image)
                            file_put_contents($image_destination, $image);
                    }
                }
            }
        $sql = 'SELECT  count(id_manufacturer) FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1';   
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_manufacturer)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'id_manufacturer' => $id_manufacturer
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_get_image()
    {
        $src = urldecode(Tools::getValue('image'));
        $str = Tools::file_get_contents($src);
        header('Content-type: image/png');
        echo $str;
    }
    private function ajax_convert_supplier_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if($imagewebp)
            $lossless = false;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_sup_c_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_c_'.pSQL($image_type) . '" ';    
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_c_'.pSQL($image_type) . '" ');
        }
        Configuration::updateValue('uit_i_o_sup_c_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_supplier FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1 ORDER by id_supplier DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_supplier = 0;
        $optimized_image = array();
        if($results)
        {
            $id_supplier = $results[0]['id_supplier'];
            if($image_type > 0)
                $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'.jpg';
            else
                $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
            if(file_exists($image_path))
            {
                $image_destination = '';
                if(Tools::strlen(   $image_type ) > 0)
                {
                    $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'.jpg';
                    $image_destination =  _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'.webp';
                }
                else
                {
                    $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
                    $image_destination = _PS_SUPP_IMG_DIR_.$id_supplier.'.webp';
                }
                if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                    unlink($image_destination);
                if((int)Configuration::get('uit_use_external_api') != 1)
                    $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' =>$image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                if(!file_exists($image_destination))
                {
                    $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                    if($image)
                        file_put_contents($image_destination, $image);
                }
                if(Configuration::get('PS_HIGHT_DPI'))
                {
                    if(Tools::strlen(   $image_type ) > 0)
                    {
                        $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'2x.jpg';
                        $image_destination =  _PS_SUPP_IMG_DIR_.$id_supplier.'-'.$image_type.'2x.webp';
                    }
                    else
                    {
                        $image_path = _PS_SUPP_IMG_DIR_.$id_supplier.'2x.jpg';
                        $image_destination = _PS_SUPP_IMG_DIR_.$id_supplier.'2x.webp';
                    }
                    if(file_exists($image_path))
                    {
                        if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                            unlink($image_destination);
                        if((int)Configuration::get('uit_use_external_api') != 1)
                            $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' =>$image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                        if(!file_exists($image_destination))
                        {
                            $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                            if($image)
                                file_put_contents($image_destination, $image);
                        }
                    }
                }
            }
        $sql = 'SELECT  count(id_supplier) FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1';   
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_supplier)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'id_supplier' => $id_supplier
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_convert_category_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 70;
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if($imagewebp)
            $lossless = false;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_cat_c_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_c_'.pSQL($image_type) . '" ';    
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_c_'.pSQL($image_type) . '" ');
        }
        Configuration::updateValue('uit_i_o_cat_c_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_category FROM `'._DB_PREFIX_.'category`  WHERE active = 1 ORDER by id_category DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_category = 0;
        $optimized_image = array();
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_category = $results['id_category'];
            $link = new Link();
            foreach($langs as $l)
            {   
                $category = new Category($results['id_category'], $l['id_lang']);
                if(!empty($category->id_image))
                {
                    $imageLink = $this->prefix .$link->getCatImageLink($category->link_rewrite, $category->id_category, $image_type);
                    $image_ext = explode('.', $imageLink);
                    $image_ext = str_replace('webp', 'jpg', end($image_ext));
                    $image_destination = '';
                    if(Tools::strlen(   $image_type ) > 0)
                    {
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'.'.$image_ext;
                        $image_destination =  _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'.webp';
                    }
                    else
                    {
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'.'.$image_ext;
                        $image_destination = _PS_CAT_IMG_DIR_.$category->id_category.'.webp';
                    }
                    if (file_exists($image_path)) 
                    {
                        if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                            unlink($image_destination);
                        if((int)Configuration::get('uit_use_external_api') != 1)
                            $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                        if(!file_exists($image_destination))
                        {
                            $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                            if($image)
                                file_put_contents($image_destination, $image);
                        }
                    }
                    if(Configuration::get('PS_HIGHT_DPI'))
                    {
                        if(Tools::strlen(   $image_type ) > 0)
                        {
                            $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'2x.'.$image_ext;
                            $image_destination =  _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'2x.webp';
                        }
                        else
                        {
                            $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'2x.'.$image_ext;
                            $image_destination = _PS_CAT_IMG_DIR_.$category->id_category.'2x.webp';
                        }
                        if (file_exists($image_path)) 
                        {
                            if(file_exists($image_destination) && substr_count($image_destination, '.webp') > 0 )
                                unlink($image_destination);
                            if((int)Configuration::get('uit_use_external_api') != 1)
                                $true = WebPConvert::convert($image_path, $image_destination, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                            if(!file_exists($image_destination))
                            {
                                $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                                if($image)
                                    file_put_contents($image_destination, $image);
                            }
                        }
                    }
                }
            }
        $sql = 'SELECT  count(id_category) FROM `'._DB_PREFIX_.'category`  WHERE active = 1';   
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_category)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'id_category' => $id_category
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_convert_product_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_path = NULL;
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 60)
            $image_quality = 60;
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if($imagewebp)
            $lossless = false;
        $offset = (int)Configuration::get('uit_i_o_c_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_c_'.pSQL($image_type) . '" ';    
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_c_'.pSQL($image_type) . '" ');
        }
        Configuration::updateValue('uit_i_o_c_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product`  WHERE active = 1 ORDER by id_product DESC  LIMIT 1 OFFSET '.(int)$offset; 
        $results = Db::getInstance()->executeS($sql);
        $id_product = 0;
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_product = $results['id_product'];
            $link = new Link();
            foreach($langs as $l)
            {   
                $product = new Product($results['id_product'], false, $l['id_lang']);
                $images = $product->getImages($l['id_lang']);
                foreach($images as $image)
                {
                    $image_old = new Image($image['id_image']);
                    if(Tools::strlen(   $image_type ) > 0)
                    {
                        $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'.jpg';
                    }
                    else
                    {
                        $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'.jpg';
                    }
                    $webp_image_path = str_replace('.jpg', '.webp', $image_path);
                    if (file_exists($image_path)) 
                    {
                        if(file_exists($webp_image_path) && substr_count($webp_image_path, '.webp') > 0 )
                            unlink($webp_image_path);
                        if((int)Configuration::get('uit_use_external_api') != 1)
                            $true = WebPConvert::convert($image_path, str_replace('.jpg', '.webp', $image_path), array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                        if(!file_exists(str_replace('.jpg', '.webp', $image_path)))
                        {
                            $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64', $image_quality);
                            if($image)
                                file_put_contents($webp_image_path, $image);
                        }
                    }   
                    if(Configuration::get('PS_HIGHT_DPI'))
                    {
                        if(Tools::strlen(   $image_type ) > 0)
                        {
                            $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'2x.jpg';
                        }
                        else
                        {
                            $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'2x.jpg';
                        }
                        $webp_image_path = str_replace('.jpg', '.webp', $image_path);
                        if (file_exists($image_path)) 
                        {
                            if(file_exists($webp_image_path) && substr_count($webp_image_path, '.webp') > 0 )
                                unlink($webp_image_path);
                            if((int)Configuration::get('uit_use_external_api') != 1)
                                $true = WebPConvert::convert($image_path, $webp_image_path, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));
                            if(!file_exists($webp_image_path))
                            {
                                $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64', $image_quality);
                                if($image)
                                    file_put_contents($webp_image_path, $image);
                            }
                        }
                    }
                }
            }
        $sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product`  WHERE active = 1'; 
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_product)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'product_id' => $id_product
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_regenerate_category_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_cat_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_'.pSQL($image_type) . '" ';  
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_cat_'.pSQL($image_type) . '" ');
        }   
        Configuration::updateValue('uit_i_o_cat_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_category FROM `'._DB_PREFIX_.'category`  WHERE active = 1 ORDER by id_category DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_category = 0;
        $optimized_image = array();
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_category = $results['id_category'];
            $link = new Link();
            foreach($langs as $l)
            {   
                $category = new Category($results['id_category'], $l['id_lang']);
                if(!empty($category->id_image))
                {
                    // Compress original images
                    $imageLink = $this->prefix .$link->getCatImageLink($category->link_rewrite, $category->id_category, $image_type);
                    $image_ext = explode('.', $imageLink);
                    $image_ext = str_replace('webp', 'jpg', end($image_ext));
                    if(Tools::strlen(  $image_type ) > 0){
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'.'.$image_ext;
                        $image_path2x = str_replace('.'.$image_ext, '2x.'.$image_ext, $image_path);
                    }
                    else{
                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'.'.$image_ext;
                        $image_path2x = str_replace('.'.$image_ext, '2x.'.$image_ext, $image_path);
                    }
                    $optimized_image = $this->get_optimized_image_api($image_path, $image_quality);
                    if (!isset($optimized_image['error'])) 
                    {
                        if(isset($optimized_image['percent']))
                        {
                            if((int)$optimized_image['percent'] > 0)
                            {
                                 if (file_exists($image_path)) 
                                 {
                                    if(isset($optimized_image['dest'])  && is_writable($image_path))
                                    {
                                        $new_image = $this->get_url_content($optimized_image['dest']);
                                        if($new_image)
                                        {
                                            $true = file_put_contents($image_path, $new_image );
                                            if($true)
                                            {
                                                $saved_before = (int)Configuration::get('uit_saved_cat_'.$image_type );
                                                $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                Configuration::updateValue('uit_saved_cat_'.$image_type, $saved_after  );
                                                $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_category.' AND image_size = "'.pSQL($image_type).'" and object_type = "category"';
                                                $row = Db::getInstance()->getRow($sql);
                                                if(!$row)
                                                {
                                                    $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_category.', "category",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                                else
                                                {
                                                    $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_category.' AND image_size = "'.pSQL($image_type).'" AND object_type = "category"';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                            }
                                        }
                                    }
                                 }
                            }
                        }
                    }   
                    // Compress 2x images
                    if(Configuration::get('PS_HIGHT_DPI'))
                    {
                        $imageLink = $this->prefix .$link->getCatImageLink($category->link_rewrite, $category->id_category, $image_type);
                        $image_ext = explode('.', $imageLink);
                        $image_ext = str_replace('webp', 'jpg', end($image_ext));
                        $imageLink = str_replace('.jpg', '2x.jpg', $imageLink);
                        $optimized_image = $this->get_optimized_image_api($image_path2x, $image_quality);
                        if (!isset($optimized_image['error'])) 
                        {
                            if(isset($optimized_image['percent']))
                            {
                                if((int)$optimized_image['percent'] > 0)
                                {
                                     if(Tools::strlen(  $image_type ) > 0)
                                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'-'.$image_type.'2x.'.$image_ext;
                                     else
                                        $image_path = _PS_CAT_IMG_DIR_.$category->id_category.'2x.'.$image_ext;
                                     if (file_exists($image_path)) 
                                     {
                                        if(isset($optimized_image['dest'])  && is_writable($image_path))
                                        {
                                            $new_image = $this->get_url_content($optimized_image['dest']);
                                            if($new_image)
                                            {
                                                $true = file_put_contents($image_path, $new_image );
                                                if($true)
                                                {
                                                    $saved_before = (int)Configuration::get('uit_saved_cat_'.$image_type );
                                                    $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                    Configuration::updateValue('uit_saved_cat_'.$image_type, $saved_after  );
                                                    $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_category.' AND image_size = "'.pSQL($image_type).'" and object_type = "category"';
                                                    $row = Db::getInstance()->getRow($sql);
                                                    if(!$row)
                                                    {
                                                        $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_category.', "category",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                        $row = Db::getInstance()->execute($sql);
                                                    }
                                                    else
                                                    {
                                                        $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_category.' AND image_size = "'.pSQL($image_type).'" AND object_type = "category"';
                                                        $row = Db::getInstance()->execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                     }
                                }
                            }
                        }   
                    }
                }
            }
        $sql = 'SELECT  count(id_category) FROM `'._DB_PREFIX_.'category`  WHERE active = 1';   
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_category)'];
        else
            $products_count = 0;
        $offset = $offset + 1;
        if($offset >= $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'saved_after' =>    (int)Configuration::get('uit_saved_cat_'.$image_type ),
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'json' => json_encode($optimized_image),
                                            'id_category' => $id_category
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_regenerate_manufacturer_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_manuf_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_'.pSQL($image_type) . '" ';    
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_manuf_'.pSQL($image_type) . '" ');
        }   
        Configuration::updateValue('uit_i_o_manuf_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_manufacturer FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1 ORDER by id_manufacturer DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_manufacturer = 0;
        $optimized_image = array();
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_manufacturer = $results['id_manufacturer'];
            foreach($langs as $l)
            {   
                $manufacturer = new Manufacturer($results['id_manufacturer'], $l['id_lang']);
                if(Tools::strlen(   $image_type ) > 0)
                {
                    $image_path = _PS_MANU_IMG_DIR_.$manufacturer->id.'-'.$image_type.'.jpg';  
                  //  $imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/m/' . (int) $manufacturer->id .'-'.$image_type.'.jpg';  
                }
                else
                {
                    $image_path = _PS_MANU_IMG_DIR_.$manufacturer->id.'.jpg';
                   // $imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/m/' . (int) $manufacturer->id . '.jpg';
                }
                if(file_exists($image_path))
                {
                    $optimized_image = $this->get_optimized_image_api($image_path, $image_quality);
                    if (!isset($optimized_image['error'])) 
                    {
                        if(isset($optimized_image['percent']))
                        {
                            if((int)$optimized_image['percent'] > 0)
                            {
                                if (file_exists($image_path)) 
                                 {
                                    if(isset($optimized_image['dest'])  && is_writable($image_path))
                                    {
                                        $new_image = $this->get_url_content($optimized_image['dest']);
                                        if($new_image)
                                        {
                                            $true = file_put_contents($image_path, $new_image );
                                            if($true)
                                            {
                                                $saved_before = (int)Configuration::get('uit_saved_manuf_'.$image_type );
                                                $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                Configuration::updateValue('uit_saved_manuf_'.$image_type, $saved_after  );
                                                $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_manufacturer.' AND image_size = "'.pSQL($image_type).'" and object_type = "manufacturer"';
                                                $row = Db::getInstance()->getRow($sql);
                                                if(!$row)
                                                {
                                                    $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_manufacturer.', "manufacturer",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                                else
                                                {
                                                    $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_manufacturer.' AND image_size = "'.pSQL($image_type).'" AND object_type = "manufacturer"';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                            }
                                        }
                                    }
                                 }
                            }
                        }
                    }   
                }
            }
        $sql = 'SELECT  count(id_manufacturer) FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1';       
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_manufacturer)'];
        else
            $products_count = 0;
        $offset++;
        if($offset > $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'saved_after' =>    (int)Configuration::get('uit_saved_ manuf_'.$image_type ),
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'json' => json_encode($optimized_image),
                                            'id_manufacturer' => $id_manufacturer
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function ajax_regenerate_supplier_images()
    {
        $image_type =  pSQL(Tools::getValue('image_type'));
        if($image_type  == 'original')
            $image_type = '';
        $image_quality = (int)Tools::getValue('image_quality');
        if($image_quality <= 0)
            $image_quality = 10;
        $image_path = NULL;
        $offset = (int)Configuration::get('uit_i_o_sup_'.$image_type );
        $sql = 'SELECT  count(*) as counti FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_'.pSQL($image_type) . '" ';  
        $results = Db::getInstance()->getRow($sql);
        if($results['counti'] > 1)
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration`    WHERE `name` = "' . 'uit_i_o_sup_'.pSQL($image_type) . '" ');
        }   
        Configuration::updateValue('uit_i_o_sup_'.$image_type, ($offset + 1));
        $sql = 'SELECT  id_supplier FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1 ORDER by id_supplier DESC  LIMIT 1 OFFSET '.(int)$offset;  
        $results = Db::getInstance()->executeS($sql);
        $id_supplier = 0;
        $optimized_image = array();
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_supplier = $results['id_supplier'];
            foreach($langs as $l)
            {   
                $supplier = new Supplier($results['id_supplier'], $l['id_lang']);
                if(Tools::strlen(   $image_type ) > 0)
                {
                    $image_path = _PS_SUPP_IMG_DIR_.$supplier->id.'-'.$image_type.'.jpg';  
                    $imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/su/' . (int) $supplier->id .'-'.$image_type.'.jpg';  
                }
                else
                {
                    $image_path = _PS_SUPP_IMG_DIR_.$supplier->id.'.jpg';
                    $imageLink =  Tools::getHttpHost(true).__PS_BASE_URI__ . 'img/su/' . (int) $supplier->id . '.jpg';
                }
                if(file_exists($image_path))
                {
                    $optimized_image = $this->get_optimized_image_api($image_path, $image_quality);
                    if (!isset($optimized_image['error'])) 
                    {
                        if(isset($optimized_image['percent']))
                        {
                            if((int)$optimized_image['percent'] > 0)
                            {
                                if (file_exists($image_path)) 
                                 {
                                    if(isset($optimized_image['dest']) && is_writable($image_path))
                                    {
                                        $new_image = $this->get_url_content($optimized_image['dest']);
                                        if( $new_image )
                                        {
                                            $true = file_put_contents($image_path, $new_image );
                                            if($true)
                                            {
                                                $saved_before = (int)Configuration::get('uit_saved_sup_'.$image_type );
                                                $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                                Configuration::updateValue('uit_saved_sup_'.$image_type, $saved_after  );
                                                $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_supplier.' AND image_size = "'.pSQL($image_type).'" and object_type = "supplier"';
                                                $row = Db::getInstance()->getRow($sql);
                                                if(!$row)
                                                {
                                                    $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,'.(int)$id_supplier.', "supplier",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($image_type).'" ,1 )';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                                else
                                                {
                                                    $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($image_type).'"  WHERE object_id ='.(int)$id_supplier.' AND image_size = "'.pSQL($image_type).'" AND object_type = "supplier"';
                                                    $row = Db::getInstance()->execute($sql);
                                                }
                                            }
                                        }
                                    }
                                 }
                            }
                        }
                    }   
                }
            }
        $sql = 'SELECT  count(id_supplier) FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1';       
        $results = Db::getInstance()->getRow($sql);
        if($results)
            $products_count= $results['count(id_supplier)'];
        else
            $products_count = 0;
        $offset++;
        if($offset > $products_count )
            $offset = $products_count ;
            die(
                    json_encode(
                                    array(
                                            'is_end' => 0,
                                            'saved_after' =>    (int)Configuration::get('uit_saved_sup_'.$image_type ),
                                            'image_type' => $image_type,
                                            'offset' => $offset,
                                            'image_path' =>  $image_path,
                                            'json' => json_encode($optimized_image),
                                            'id_supplier' => $id_supplier
                                        )
                        )   
                );
        }
        else
        {
            die(
                    json_encode(
                                    array(
                                            'is_end' => 1,
                                            'offset' => $offset,
                                            'sql' => $sql
                                        )
                        )   
                );
        }
    }
    private function check_employee()
    {
        $cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
        $employee = new Employee((int)$cookie->id_employee);
        if (!Validate::isLoadedObject($employee) || !$employee->checkPassword((int)$cookie->id_employee, $cookie->passwd))
            die('you must login as employee');
    }
    private function ajax_convert_in_folder()
    {
        $this->check_employee();
        $image_path =  Tools::getValue('path');
        $imageLink =  Tools::getValue('url');
        $image_quality =  (int)Tools::getValue('quality');
        $type =  Tools::getValue('type');
        $context = Context::getContext();
        $image_path  = str_replace('//', '/', $image_path );
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if($imagewebp)
            $lossless = false;
        if($image_quality < 60)
            $image_quality = 60;
        if(file_exists($image_path))
        {
            $imagewebp = function_exists('imagewebp');
            $imagick = extension_loaded('imagick');
            $is_imagick = false;
            if($imagick)
            {
                if(class_exists('Imagick'))
                {
                    if(method_exists('Imagick', 'queryFormats'))
                    {
                        $formats = Imagick::queryFormats();
                        foreach($formats as $format)
                        {
                            if(Tools::strtoupper($format) == 'WEBP')
                                $is_imagick = true;
                        }   
                    }
                }
            }
            $webp_image_path = str_replace(array('.jpg','.png','.JPG','.PNG'), '.webp', $image_path);
            if($webp_image_path == $image_path)
                $webp_image_path = $image_path.'.webp';
            if((int)Configuration::get('uit_use_external_api') == 1 || (substr_count(Tools::strtolower($image_path), '.png') > 0 && $is_imagick  == false) ){
                $imagewebp = false;
                $imagick = false;
                $is_imagick = false;
            }
                    try {
                        if(file_exists($webp_image_path) && substr_count($webp_image_path, '.webp') > 0 )
                            unlink($webp_image_path);
                        if($is_imagick || $imagewebp )
                        {
                            $true = \WebPConvert\WebPConvert::convert($image_path, $webp_image_path, array( 'converters' => [ 'imagick', 'imagemagick', 'gd'], 'lossless' => $lossless,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));    
                        }
                        else
                        {
                            if(!file_exists($webp_image_path))
                            {
                                $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)), 'image_file_b64');
                                if($image)
                                    file_put_contents($webp_image_path, $image);
                            }
                        }
                    } catch (Exception $exception) {
                        PrestaShopLogger::addLog(
                            empty($exception->getMessage()) ? 'Unknown error' : $exception->getMessage(),
                            1,
                            $exception->getCode(),
                            'ultimateimagetool',
                            null,
                            true
                        );
                    }
                    if(file_exists($webp_image_path))
                    {
                        $arr = array();
                        $arr['uit_files'] = array();
                        $arr['uit_files']['url'] = $imageLink;
                        $arr['uit_files']['path'] = $webp_image_path;
                        $arr['uit_files']['before'] = (int)(filesize($image_path)/ 1024 );
                        $arr['uit_files']['after'] = (int)(filesize($webp_image_path)/ 1024 );
                        $arr['webp'] = 1;
                        $context->smarty->assign($arr);
                        $rec = new ultimateimagetool();
                        
                        if (version_compare(_PS_VERSION_, '1.7.5.0', '>=') === true) 
                        {
                            $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool', 'views/templates/front/table-folder-images-single.tpl');
                        }
                        elseif (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true && version_compare(_PS_VERSION_, '1.7.5.0', '<') === true) 
                        {
                            $tpl = $rec->fetch('module:ultimateimagetool/views/templates/front/table-folder-images-single.tpl');
                        }

                        elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true && version_compare(_PS_VERSION_, '1.6.0.0', '>=') === true ) 
                        {
                  
                            $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool','table-folder-images-single.tpl');
                        }
                        else
                        {   
                            $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool', 'views/templates/front/table-folder-images-single.tpl');
                        }

                        die(json_encode(array(
                                                'error' => 0,
                                                'result' => $tpl
                        )));        
                    }
                die(json_encode(array(
                                      'error' => 1,
                                      'error_msg' => 'There was a problem with the conversion'
                                 )));
        }   
        else
        {
            die(json_encode(array(
                                      'error' => 1,
                                      'error_msg' => 'Image path not found.'
                                 )));
        }   
    }
    private function ajax_optimize_in_folder()
    {
        $this->check_employee();
        $image_path =  Tools::getValue('path');
        $imageLink =  Tools::getValue('url');
        $image_quality =  Tools::getValue('quality');
        $type =  Tools::getValue('type');
        $context = Context::getContext();
        $true = false;

        if(file_exists($image_path))
        {
            $optimized_image = $this->get_optimized_image_api($image_path, $image_quality);
            if (!isset($optimized_image['error'])) 
            {
                if(isset($optimized_image['percent']))
                {
                    if((int)$optimized_image['percent'] >= 0)
                    {
                        if(isset($optimized_image['dest']) && is_writable($image_path))
                        {
                            $new_image = $this->get_url_content($optimized_image['dest']);

                            if($new_image && (int)$optimized_image['percent'] >= 0)
                                $true = file_put_contents($image_path, $new_image );



                            if($true)
                            {
                                $saved_before = (int)Configuration::get('uit_saved_theme' );
                                $saved_after = $saved_before  + (int)(($optimized_image['src_size'] - $optimized_image['dest_size']) / 1024);
                                Configuration::updateValue('uit_saved_'.$type, $saved_after  );
                                $sql = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = 0 AND image_size = "'.pSQL($imageLink).'" and object_type = "'.pSQL($type).'"';
                                            $row = Db::getInstance()->getRow($sql);
                                if(!$row)
                                {
                                    $sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, image_size, processed  )  VALUES (NULL,0,  "'.pSQL($type).'",  '.(int)($optimized_image['src_size'] / 1024).', '.(int)($optimized_image['dest_size'] / 1024).', "'.date('Y-m-d H:i:s').'", "'.pSQL($imageLink).'" ,1 )';
                                    $row = Db::getInstance()->execute($sql);
                                }
                                else
                                {
                                    $sql = 'UPDATE `'._DB_PREFIX_.'uit_smush`  SET  `original_size` = '.(int)($optimized_image['src_size'] / 1024).', `new_size` = '.(int)($optimized_image['dest_size'] / 1024).', `date_add` = "'.date('Y-m-d H:i:s').'", processed = 1, image_size = "'.pSQL($imageLink).'"  WHERE object_id = 0 AND image_size = "'.pSQL($imageLink).'" AND object_type =  "'.pSQL($type).'"';
                                    $row = Db::getInstance()->execute($sql);
                                }
                                $arr = array('uit_files' => array());
                                $arr['uit_files']['url'] = $imageLink ;
                                $arr['uit_files']['path'] = $image_path ;
                                $arr['uit_files']['before'] = (int)($optimized_image['src_size'] / 1024 );
                                $arr['uit_files']['after'] = (int)($optimized_image['dest_size'] / 1024 );
                                $context->smarty->assign($arr);
                                $rec = New ultimateimagetool();
                               if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
                               {
                                    $tpl = $rec->fetch('module:ultimateimagetool/views/templates/front/table-folder-images-single.tpl');
                               }
                                elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true && version_compare(_PS_VERSION_, '1.6.0.0', '>=') === true ) 
                                {
                                    $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool','table-folder-images-single.tpl');
                                }
                                else
                                {
                                    $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool', 'views/templates/front/table-folder-images-single.tpl');
                                }
                                die(json_encode(array(
                                    'error' => 0,
                                    'result' => $tpl,
                                    'json' => json_encode($optimized_image)
                                )));    
                            }
                            else
                            {
                                die(json_encode(array(
                                      'error' => 1,
                                      'error_msg' => 'File Permissions do not allow compression.'
                                 )));
                            }
                        }
                    }
                    else
                    {
                        die(json_encode(array(
                                              'error' => 1,
                                              'error_msg' => 'Percent under 0.',
                                              'json' => json_encode($optimized_image)
                                         )));
                    }
                }
                else
                {
                    die(json_encode(array(
                                          'error' => 1,
                                          'error_msg' => 'Image could not be optimized',
                                          'json' => json_encode($optimized_image)
                                     )));
                }
            }
            else
            {
                die(json_encode(array(
                                      'error' => 1,
                                      'error_msg' => 'There is an error.',
                                      'json' => json_encode($optimized_image)
                                 )));
            }
        }   
        else
        {
            die(json_encode(array(
                                      'error' => 1,
                                      'error_msg' => 'Image path not found.'
                                 )));
        }   
    }
    private function ajax_search_and_optimize_in_folder()
    {
        $this->check_employee();
        $path =  Tools::getValue('path');
        $webp = 0;
        if(Tools::getValue('webp') == 1)
            $webp = 1;
        $images = ultimateimagetool::search_for_images_in_folder($path);
        $context = Context::getContext();
        if(sizeof($images )  == 0)
        {
            die(json_encode(array(
                        'error' => 1,
                        'error_msg' => 'no images found'
                    )));
        }
        $arr = array('uit_files' => array());
        $images = array_unique($images);
        foreach($images  as $img)
        {
            $img = str_replace('//', '/', $img);
            $a = array();
            $a['path'] = $img;
            $e = explode('/', $img);
            $image_path = array();
            $is_found = 0;
            $search_path = 'themes';
            if(substr_count(    $path , '/modules') >= 1)
                $search_path = 'modules';
            if(substr_count(    $img , '/img/co') >= 1)
                $search_path = 'co';
            if(substr_count(    $img , '/img/st') >= 1)
                $search_path = 'st';
            if(substr_count(    $img , '/img/scenes') >= 1)
                $search_path = 'scenes';
            if(substr_count(    $path , '/cms') >= 1)
                $search_path = 'cms';
            foreach($e as $el)
            {
                if($el == $search_path )
                {
                    $is_found = 1;
                    $image_path[] = $el;
                }
                else
                {
                    if($is_found == 1)
                        $image_path[] = $el;
                }
            }
            $image_path = implode('/', $image_path);
            if($search_path == 'cms' || $search_path == 'co' || $search_path == 'st' || $search_path == 'scenes')
            {
                $a['url'] =  Tools::getHttpHost().__PS_BASE_URI__.'img/'.$image_path;
            }
            else
            {
                $a['url'] =  Tools::getHttpHost().__PS_BASE_URI__.$image_path;
            }
            $a['url'] = $this->prefix.str_replace('//', '/', $a['url']);
            $arr['uit_files'][] = $a;
            $arr['webp'] = $webp;
        }
        $context->smarty->assign($arr);
        $rec = New ultimateimagetool();
       if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
       {
            $tpl = $rec->fetch('module:ultimateimagetool/views/templates/front/table-folder-images.tpl');
       }
        elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true && version_compare(_PS_VERSION_, '1.6.0.0', '>=') === true ) 
        {
            $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool','table-folder-images.tpl');
        }
        else
        {
            $tpl = $rec->display(_PS_MODULE_DIR_.'ultimateimagetool', 'views/templates/front/table-folder-images.tpl');
        }
        die(json_encode(array(
                        'error' => 0,
                        'result' => $tpl
                    )));    
    }
    
    private function ajax_generate_test()
    {
        $image_quality = Tools::getValue('test_quality');
        $image_path = NULL;
        $image_type = '';
        $sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product`  WHERE active = 1'; 
        $results = Db::getInstance()->getRow($sql);
        $count = $results['count(id_product)'];
        $offset = rand(0,   ($count - 1));
        $sql = 'SELECT  id_product FROM `'._DB_PREFIX_.'product`  WHERE active = 1 ORDER by id_product DESC  LIMIT 1 OFFSET '.(int)$offset; 
        $results = Db::getInstance()->executeS($sql);
        $id_product = 0;
        if($results)
        {
            $langs = Language::getLanguages(true); 
            $results = $results[0];
            $id_product = $results['id_product'];
            $link = new Link();
            foreach($langs as $l)
            {   
                $product = new Product($results['id_product'], false, $l['id_lang']);
                $images = $product->getImages($l['id_lang']);
                foreach($images as $image)
                {
                     $image_old = new Image($image['id_image']);
                    if(Tools::strlen(  $image_type ) > 0)
                    {
                        $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'.jpg';
                    }
                    else
                    {
                        $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'.jpg';
                    }
                $img = $product->getCover($product->id);
                $img_url = '//'.$link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$image['id_image'], $image_type);
               // var_dump( $img_url );
                    $optimized_image = $this->get_optimized_image_api( $image_path, $image_quality);
                    
                    if (isset($optimized_image['error'])) {
                        die(json_encode(array(
                                        'error' => 1,
                                        'error_msg' => $optimized_image['message']
                                    )));    
                    }

                   //   echo "<pre>";print_r($optimized_image);echo "</pre>";
                    
                    if (!isset($optimized_image['error'])) 
                    {
                        if(isset($optimized_image['percent']))
                        {
                            if((int)$optimized_image['percent'] >= 0)
                            {
                                 $image_old = new Image($image['id_image']);
                                 $optimized_image['src'] =   $img_url;
                                 if(Tools::strlen(  $image_type ) > 0)
                                    $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'-'.$image_type.'.jpg';
                                 else
                                    $image_path = _PS_PROD_IMG_DIR_.$image_old->getExistingImgPath().'.jpg';
                                 if (file_exists($image_path)) 
                                 {
                                    if(isset($optimized_image['dest']))
                                    {
                                        $optimized_image['dest'] = Context::getContext()->link->getAdminLink('ultimateimagetoolajax').'&&image='.urlencode($optimized_image['dest']).'&action=get_image';
                                        die(json_encode($optimized_image));
                                    }
                                 }
                            }
                        }   
                    }
                }
            }
        }
    }
    private function ajax_delete_images()
    {
        $this->check_employee();
        $size =  Tools::getValue('size');
        $image_type =  Tools::getValue('type');
        $folder = 'xxq1234xx123/';
        if($image_type == 'products')
        {
            $folder = _PS_PROD_IMG_DIR_;
        }
        elseif($image_type == 'categories')
        {
            $folder = _PS_CAT_IMG_DIR_;
        }
        elseif($image_type == 'manufacturers')
        {
            $folder = _PS_MANU_IMG_DIR_;
        }
        elseif($image_type == 'suppliers')
        {
            $folder = _PS_SUPP_IMG_DIR_;
        }
        $images = ultimateimagetool::search_for_images_in_folder($folder, $size, true, true);
        if(sizeof($images) > 0)
        {
            foreach($images as $img)
            {
                if(substr_count($img, '-'.$size))
                {
                    if(file_exists($img))
                        unlink($img);                       
                }
            }
        }
        die(json_encode(array(
                        'error' => 1
                    )));    
    }
    private function ajax_get_image_sizes()
    {
        $this->check_employee();
        $val =  Tools::getValue('val');
        $image_types = ImageType::getImagesTypes($val);
        if($image_types)
            die(json_encode(array(
                        'error' => 0,
                        'result' => $image_types
                    )));    
            die(json_encode(array(
                        'error' => 1,
                        'result' => $image_types
                    )));    
    }
}