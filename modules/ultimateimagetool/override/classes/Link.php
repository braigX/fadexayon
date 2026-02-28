<?php
/***
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

class Link extends LinkCore
{

    protected $webpSupported = false;

    public function __construct($protocolLink = null, $protocolContent = null)
    {
        parent::__construct($protocolLink, $protocolContent);
        if( 
            Module::isEnabled('ultimateimagetool') &&  
            (int)Configuration::get('uit_use_webp') >= 1 &&  
            (int)Configuration::get('uit_use_picture_webp') == 0 && 
            (int)Configuration::get('uit_use_webp_termination') >= 1 
            && (isset($_SERVER['HTTP_ACCEPT']) === true) &&  (false !== strpos($_SERVER['HTTP_ACCEPT'], 'image/webp'))   )
        {
            $this->webpSupported = true;
        }
    }

   public function getImageLink($name, $ids, $type = NULL, string $extension = 'jpg')
    {
        $parent = parent::getImageLink($name, $ids, $type,  $extension);
    
        if ($this->webpSupported) 
        {
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $uri_path = _PS_ROOT_DIR_._THEME_PROD_DIR_.Image::getImgFolderStatic( $id_image ). $id_image .($type ? '-'.$type : '').'.webp';
             if(file_exists(  $uri_path ) && strpos($uri_path , '/p/default-') === false)
                return str_replace('.jpg', '.webp', $parent);
         
             return $parent;
        }
        
        return $parent;
    }

    public function getCatImageLink($name, $idCategory, $type = null, string $extension = 'jpg')
    {
        $parent = parent::getCatImageLink($name, $idCategory, $type, $extension);

        if ($this->webpSupported) 
        {
            $uri_path = _PS_ROOT_DIR_._THEME_CAT_DIR_.Image::getImgFolderStatic($idCategory).$idCategory.($type ? '-'.$type : '').'.webp';
       
            if(file_exists(  $uri_path )&& strpos($uri_path , '/c/default-') === false)
                 return str_replace('.jpg', '.webp', $parent);
            

            $allow = (int) Configuration::get('PS_REWRITING_SETTINGS');

            if ($allow == 1 && $type) {
                $uri_path = _PS_ROOT_DIR_ . '/img/c/' . $idCategory . '-' . $type  . '.webp';
            } else {
                $uri_path = _THEME_CAT_DIR_ . $idCategory . ($type ? '-' . $type : '') . '.webp';
            }
 
            if(file_exists(  $uri_path )&& strpos($uri_path , '/c/default-') === false)
                 return str_replace('.jpg', '.webp', $parent);

        }
        
        return $parent;
    }

    public function getSupplierImageLink($idSupplier, $type = null, string $extension = 'jpg')
    {
        $parent = parent::getSupplierImageLink($idSupplier, $type, $extension );
        if ($this->webpSupported) 
        {           
            $uri_path = _PS_ROOT_DIR_._PS_SUPP_IMG_DIR_.Image::getImgFolderStatic($idSupplier).$idSupplier.($type ? '-'.$type : '').'.webp';
            if(file_exists(  $uri_path )&& strpos($uri_path , '/s/default-') === false)
                return str_replace('.jpg', '.webp', $parent);
        }
        return $parent;
    }

    public function getStoreImageLink($name, $idStore, $type = null, string $extension = 'jpg')
    {
        $parent = parent::getStoreImageLink($name, $idStore, $type, $extension );
        if ($this->webpSupported) 
        {           
            $uri_path = _PS_ROOT_DIR_._PS_STORE_IMG_DIR_.Image::getImgFolderStatic($idStore).$idStore.($type ? '-'.$type : '').'.webp';
            if(file_exists(  $uri_path )&& strpos($uri_path , '/st/default-') === false)
                return str_replace('.jpg', '.webp', $parent);
        }
        return $parent;
    }

    public function getManufacturerImageLink($idManufacturer, $type = null, string $extension = 'jpg')
    {
        $parent = parent::getManufacturerImageLink($idManufacturer, $type, $extension );
        if ($this->webpSupported) 
        {           
            $uri_path = _PS_ROOT_DIR_._PS_MANU_IMG_DIR_.Image::getImgFolderStatic($idManufacturer).$idManufacturer.($type ? '-'.$type : '').'.webp';
            if(file_exists(  $uri_path )&& strpos($uri_path , '/m/default-') === false)
                return str_replace('.jpg', '.webp', $parent);
            
        }
        return $parent;
    }
}