<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class Ybc_blogGalleryModuleFrontController
 * @property Ybc_blog $module;
 */
class Ybc_blogGalleryModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public function __construct()
	{
		parent::__construct();
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='right')
            $this->display_column_right=true;
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='left')
            $this->display_column_left =true;
		$this->context = Context::getContext();
        
	}
	public function init()
	{
		parent::init();
        if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'module/ybc_blog/') !==false)
        {
            Tools::redirect($this->module->getLink('gallery'));
        }
	}
    public function getAlternativeLangsUrl()
    {
        $alternativeLangs = array();
        $languages = Language::getLanguages(true, $this->context->shop->id);

        if ($languages < 2) {
            // No need to display alternative lang if there is only one enabled
            return $alternativeLangs;
        }

        foreach ($languages as $lang) {
            $alternativeLangs[$lang['language_code']] = $this->module->getLanguageLink($lang['id_lang']);
        }
        return $alternativeLangs;
    }
    public function _initContent()
    {
        $galleryData = $this->getGalleries();
        $prettySkin = Configuration::get('YBC_BLOG_GALLERY_SKIN');
        $this->context->smarty->assign(
            array(
                'blog_galleries' => $galleryData['galleries'],
                'blog_paggination' => $galleryData['paggination'],
                'prettySkin' => in_array($prettySkin, array('dark_square','dark_rounded','default','facebook','light_rounded','light_square')) ? $prettySkin : 'dark_square',
                'prettyAutoPlay' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                'per_row'=> Configuration::get('YBC_BLOG_GALLERY_PER_ROW') ? Configuration::get('YBC_BLOG_GALLERY_PER_ROW'):12,
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                'image_folder' => _PS_YBC_BLOG_IMG_,
                'is17' => $this->module->is17,
            )
        );
        if(Tools::isSubmit('loadajax'))
        {
            $list_galleries = $this->module->display($this->module->getLocalPath(),'more_gallery_list.tpl');
            die(
                json_encode(
                    array(
                        'list_galleries'=> $list_galleries,
                        'blog_paggination'=>$galleryData['paggination'],
                    )
                )
            );
        }

    }
	public function initContent()
	{
        parent::initContent();
        $this->module->setMetas();
        if(Tools::isSubmit('loadajax'))
        {
            $this->_initContent();
        }
        else
        {
            $page = (int)Tools::getValue('page');
            if(!$this->module->isCached('gallery_list.tpl',$this->module->_getCacheId($page)))
            {
                $this->_initContent();
            }
            $this->context->smarty->assign(
                array(
                    'gallery_list_content' => $this->module->display($this->module->getLocalPath(),'gallery_list.tpl',$this->module->_getCacheId($page)),
                    'path' => $this->module->getBreadCrumb(),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,

                )
            );
        }
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/gallery_list.tpl');
        else  
            $this->setTemplate('gallery_list_16.tpl');
	}    
    public function getGalleries()
    {
        $filter = ' AND g.enabled = 1';            
        $sort = ' g.sort_order asc, g.id_gallery asc, ';
        $module = new Ybc_blog();
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<1)
            $page =1;
        $totalRecords = (int)Ybc_blog_gallery_class::countGalleriesWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $module->getLink('gallery', array('page'=>"_page_"));
        $paggination->limit =  (int)Configuration::get('YBC_BLOG_GALLERY_PER_PAGE') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_PER_PAGE') : 24;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $galleries = Ybc_blog_gallery_class::getGalleriesWithFilter($filter, $sort, $start, $paggination->limit);
        if($galleries)
        {
            foreach($galleries as &$gallery)
            {
                if($gallery['thumb'])
                    $gallery['thumb'] =  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/thumb/'.$gallery['thumb']);   
                else
                     $gallery['thumb']= $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']); 
                if($gallery['image'])
                {                       
                    $gallery['image'] =  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']);    
                }                     
            }                
        }        
        return array(
            'galleries' => $galleries , 
            'paggination' => $paggination->render()
        );
    }
}