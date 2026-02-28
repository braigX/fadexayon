<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class Link extends LinkCore
{
 
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
    protected $webpSupported = false;
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
   /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
    /*
    * module: ultimateimagetool
    * date: 2025-10-08 08:38:04
    * version: 2.3.2
    */
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
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    protected function getLangLink($idLang = null, Context $context = null, $idShop = null)
    {
        $langLink = parent::getLangLink($idLang, $context, $idShop);
        if (!Module::getInstanceByName('ets_seo')) {
            return $langLink;
        }
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        if (!$idLang) {
            $idLang = $context->language->id;
        }
        if (Language::isMultiLanguageActivated($idShop) && (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL') && $idLang == (int) Configuration::get('PS_LANG_DEFAULT')) {
            return '';
        }
        return $langLink;
    }
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    public function getCategoryLink(
        $category,
        $alias = null,
        $idLang = null,
        $selectedFilters = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!Module::isEnabled('ets_seo')) {
            return parent::getCategoryLink($category, $alias, $idLang, $selectedFilters, $idShop, $relativeProtocol);
        }
        
        $dispatcher = Dispatcher::getInstance();
        if (!$idLang) {
            $idLang = Ets_Seo::getContextStatic()->language->id;
        }
        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);
        $params = [];
        if (Validate::isLoadedObject($category)) {
            $params['id'] = $category->id;
        } elseif (isset($category['id_category'])) {
            $params['id'] = $category['id_category'];
        } elseif (is_int($category) or ctype_digit($category)) {
            $params['id'] = (int) $category;
        } else {
            throw new \InvalidArgumentException('Invalid category parameter');
        }
        $selectedFilters = null === $selectedFilters ? '' : $selectedFilters;
        if (empty($selectedFilters)) {
            $rule = 'category_rule';
        } else {
            $rule = 'layered_rule';
            $params['selected_filters'] = $selectedFilters;
        }
        if (!$alias) {
            $category = $this->getCategoryObject($category, $idLang);
        }
        $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
        if ($dispatcher->hasKeyword($rule, $idLang, 'parent_rewrite', $idShop)) {
            $params['parent_rewrite'] = '';
            try {
                $cats = [];
                
                $currentCategory = $this->getCategoryObject($category, $idLang);
                foreach ($currentCategory->getParentsCategories($idLang) as $cat) {
                    if (!in_array($cat['id_category'], [1, 2, $currentCategory->id])) {
                        $cats[] = $cat['link_rewrite'];
                    }
                }
                if (count($cats)) {
                    $params['parent_rewrite'] = implode('/', array_reverse($cats));
                }
            } catch (PrestaShopException $e) {
            }
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_keywords', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_title', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
        }
        return $url . $dispatcher->createUrl($rule, $idLang, $params, $this->allow, '', $idShop);
    }
}
