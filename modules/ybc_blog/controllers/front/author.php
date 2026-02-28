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
 * Class Ybc_blogAuthorModuleFrontController
 * @property Ybc_blog $module
 */
class Ybc_blogAuthorModuleFrontController extends ModuleFrontController
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
            Tools::redirect($this->module->getLink('author'));
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
        $page = (int)Tools::getValue('page');
        if($page<1)
            $page =1;
        $totalRecords = (int)Ybc_blog_post_employee_class::getCountAuthor();
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->module->getLink('author', array('page'=>"_page_"));
        $paggination->limit =  8;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $authors = Ybc_blog_post_employee_class::getListAuthorPost($start,$paggination->limit);
        if($authors)
        {
            foreach($authors as &$author)
            {
                if($author['is_customer'])
                {
                    $information = Ybc_blog_post_employee_class::getInformationByID($author['added_by']);
                    if(!$information['name'])
                        $information['name']= $information['firstname'].' '.$information['lastname'];
                    $author['information']= $information;
                    $author['link']=$this->module->getLink('blog',array('id_author'=>$author['added_by'],'is_customer'=>1,'alias'=> Tools::link_rewrite($information['name'])));
                    if($information['avata'])
                        $author['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$information['avata']);
                    else
                        $author['avata']= $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                else
                {
                    $information = Ybc_blog_post_employee_class::getInformationByID($author['added_by'],false);;
                    if(!$information['name'])
                        $information['name']=$information['firstname'].' '.$information['lastname'];
                    $author['information']=$information;
                    $author['link']=$this->module->getLink('blog',array('id_author'=>$author['added_by'],'is_customer'=>0,'alias'=> Tools::link_rewrite($information['name'])));
                    if($information['avata'])
                        $author['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$information['avata']);
                    else
                        $author['avata']= $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png')) ;
                }
                $author['posts'] = Ybc_blog_post_employee_class::getPosts($author['added_by'],$author['is_customer']);
                if($author['posts'])
                {
                    foreach($author['posts'] as &$post)
                    {
                        $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                    }
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'is_main_page' =>false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                'authors' => $authors,
                'blog_paggination' => $paggination->render(),
                'is17' => $this->module->is17,
            )
        );
        if(Tools::isSubmit('loadajax'))
        {
            die(
                json_encode(
                    array(
                        'list_blog'=> $this->module->display($this->module->getLocalPath(),'more_authors_list.tpl'),
                        'blog_paggination'=>$paggination->render(),
                    )
                )
            );
        }
    }
    public function initContent()
	{
        parent::initContent();
        if(Tools::isSubmit('loadajax'))
        {
            $this->_initContent();
        }
        else{
            $page = (int)Tools::getValue('page');
            if(!$this->module->isCached('authors_list.tpl',$this->module->_getCacheId($page)))
            {
                $this->_initContent();
            }
            $this->context->smarty->assign(
                array(
                    'authors_list_content' => $this->module->display($this->module->getLocalPath(),'authors_list.tpl',$this->module->_getCacheId($page)),
                    'path' => $this->module->getBreadCrumb(),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                )
            );
        }
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/author.tpl');
        else
            $this->setTemplate('author_16.tpl');
       
    }
}
