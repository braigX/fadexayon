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
class Ybc_blogCommentModuleFrontController extends ModuleFrontController
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
        $this->module= new Ybc_blog();
	}
	public function init()
	{
		parent::init();
        if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'/module/ybc_blog') !==false)
        {
            $this->module->redirect($this->module->getLink('comment'));
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
        $totalRecords= Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.approved=1');
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->module->getLink('comment', array('page'=>"_page_"));
        $paggination->limit = (int)Configuration::get('YBC_BLOG_COMMENT_PER_PAGE') ? : 8;
        $totalPages = ceil($totalRecords / $paggination->limit);
        $page = (int)Tools::getValue('page',1);
        if($page<1)
            $page = 1;
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $posts = Ybc_blog_comment_class::getCommentsWithFilter(' AND bc.approved=1','bc.datetime_added DESC,',$start,$paggination->limit);;
        if($posts)
        {
            foreach($posts as &$post)
            {
                $post['link'] = $this->module->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$post['id_post'].' AND approved=1');
                $post['liked'] = $this->module->isLikedPost($post['id_post']);
                if($post['id_user'] && !$post['name'])
                {
                    $customer = new Customer($post['id_user']);
                    $post['name'] =  $customer->firstname.' '.$customer->lastname;
                }
                if($post['id_user'])
                {
                    $customerinfo = Ybc_blog_post_employee_class::getInformationByID($post['id_user']);
                    if($customerinfo && $customerinfo['avata'])
                    {
                        $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$customerinfo['avata']);
                    }
                    else
                        $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                else
                {
                    $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            }
        }
        $this->context->smarty->assign(
            array(
                'posts' => $posts,
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'comment_length' => (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH') ? (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH'):120,
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                'comment_paggination' => $paggination->render(),
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'image_folder' => _PS_YBC_BLOG_IMG_.'avata/',
                'is17' => $this->module->is17,
            )
        );
        if(Tools::isSubmit('loadajax'))
        {
            die(
                json_encode(
                    array(
                        'list_blog'=> $this->module->display($this->module->getLocalPath(),'more_comment_list.tpl'),
                        'blog_paggination'=> $paggination->render(),
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
            if(!$this->module->isCached('comment_list.tpl',$this->module->_getCacheId($page)))
            {
                $this->_initContent();
            }
            $this->context->smarty->assign(
                array(
                    'commnet_list_content' => $this->module->display($this->module->getLocalPath(),'comment_list.tpl',$this->module->_getCacheId($page)),
                    'path' => $this->module->getBreadCrumb(),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                )
            );
        }
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/comment.tpl');
        else  
            $this->setTemplate('comment-16.tpl');
	}
}