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
 * Class Ybc_blogManagementcommentsModuleFrontController
 * @property Ybc_blog $module
 */
class Ybc_blogManagementcommentsModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $_errros= array();
    public $sussecfull;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
		$this->context = Context::getContext();
	}
	public function init()
	{
		parent::init();
        if (!$this->context->customer->isLogged())
        {
            Tools::redirect('index.php?controller=authentication');
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
	public function initContent()
	{
	    parent::initContent();
        $this->module->setMetas();
        if (!$this->context->customer->isLogged())
		{
                
            Tools::redirect('index.php?controller=authentication');
        }
        $tabmanagament = Tools::getValue('tabmanagament');
        if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
            $tabmanagament ='comment';
        if(Tools::isSubmit('submitComment') || Tools::isSubmit('submitCommentStay'))
            $this->_saveComment();
        if(Tools::isSubmit('commentapproved') && ($id_comment=(int)Tools::getValue('id_comment')))
        {
               if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
               {
                    $commentapproved = (int)Tools::getValue('commentapproved');
                    $comment->approved = (int)$commentapproved;
                    if($comment->update())
                    {
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));
                    }
               }
               else
               {
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'error' => $this->module->l('Sorry, you do not have permission','managementcomments'),
                                )
                            )
                        );
                    }
                    else
                        $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementcomments');
               }
                    
        }
        if(Tools::isSubmit('deletecomment') && $id_comment=(int)Tools::getValue('id_comment'))
        {
            if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$id_comment) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
            {
                if($comment->delete())
                {
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('You have just deleted the comment successfully','managementcomments'),
                                )
                            )
                        );
                    }
                    else
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'deletedcomment'=>1)));
                }

            }
            else
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'error' => $this->module->l('Sorry, you do not have permission','managementcomments'),
                            )
                        )
                    );
                }
                else
                    $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementcomments');
            }
               
        }   
        if(Tools::isSubmit('deletedcomment'))
            $this->sussecfull = $this->module->l('You have just deleted the comment successfully','managementcomments');
        if(Tools::isSubmit('updateComment'))
            $this->sussecfull = $this->module->l('Comment updated','managementcomments');
        $this->context->smarty->assign(
            array(
                'errors_html'=>$this->_errros ? $this->module->displayError($this->_errros) : false,
                'form_html_post'=> $this->displayRightFormComments(),
                'breadcrumb' => $this->module->is17 ? $this->getBreadCrumb() : false, 
                'path' => $this->getBreadCrumb(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/management_comments.tpl');      
        else         
            $this->setTemplate('management_comments16.tpl');  
    }
    private function displayRightFormComments()
    {
        $this->context->smarty->assign(
            array(
                'sucsecfull_html' => $this->sussecfull ? $this->module->displaySuccessMessage($this->sussecfull):'',
                'content_html_right'=>$this->renderCommentOtherListByCustomer(),
            )
        );
        return $this->module->display($this->module->getLocalPath(),'blog_management_right.tpl');
    }
    private function renderCommentOtherListByCustomer()
    {
        $fields_list = array(
            'id_comment' => array(
                'title' => $this->module->l('Id','managementcomments'),
                'width' => 40,
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'id_comment','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'id_comment','sort_type'=>'desc')),
                'filter' => true,
            ),
            'subject' => array(
                'title' => $this->module->l('Subject','managementcomments'),
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'subject','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'subject','sort_type'=>'desc')),
                'filter' => true,
            ),
            'rating' => array(
                'title' => $this->module->l('Rating','managementcomments'),
                'type' => 'select',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'rating','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'rating','sort_type'=>'desc')),
                'filter' => true,
                'rating_field' => true,
                'filter_list' => array(
                    'id_option' => 'rating',
                    'value' => 'stars',
                    'list' => array(
                        0 => array(
                            'rating' => 0,
                            'stars' => $this->module->l('No reviews','managementcomments')
                        ),
                        1 => array(
                            'rating' => 1,
                            'stars' => '1 '.$this->module->l('star','managementcomments')
                        ),
                        2 => array(
                            'rating' => 2,
                            'stars' => '2 '.$this->module->l('stars','managementcomments')
                        ),
                        3 => array(
                            'rating' => 3,
                            'stars' => '3 '.$this->module->l('stars','managementcomments')
                        ),
                        4 => array(
                            'rating' => 4,
                            'stars' => '4 '.$this->module->l('stars','managementcomments')
                        ),
                        5 => array(
                            'rating' => 5,
                            'stars' => '5 '.$this->module->l('stars','managementcomments')
                        ),
                    )
                )
            ),
            'title' => array(
                'title' => $this->module->l('Blog post','managementcomments'),
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'title','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'title','sort_type'=>'desc')),
                'filter' => true,
                'strip_tag' => false,
            ),
            'approved' => array(
                'title' => $this->module->l('Approved','managementcomments'),
                'type' => 'active',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'approved','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'approved','sort_type'=>'desc')),
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->module->l('Yes','managementcomments')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->module->l('No','managementcomments')
                        )
                    )
                )
            )
        );
        //Filter comment
        $filter = " AND bc.id_user ='".(int)$this->context->customer->id."'";
        $tabmanagament = Tools::getValue('tabmanagament');
        if(Tools::isSubmit('ybc_submit_ybc_comment') && $tabmanagament=='comment_other')
        {
            if(($id = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id))
                $filter .= " AND bc.id_comment = ".(int)$id;
            if(($comment_post = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($comment_post))
                $filter .= " AND bc.comment like '%".pSQL($comment_post)."%'";
            if(($subject = trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
                $filter .= " AND (bc.subject like '%".pSQL($subject)."%' OR bc.comment like '%".pSQL($subject)."%' )";
            if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
                $filter .= " AND bc.rating = ".(int)$rating;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND bc.name like '%".pSQL($name)."%'";
            if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
                $filter .= " AND bc.approved = ".(int)$approved;
            if(($reported = trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
                $filter .= " AND bc.reported = ".(int)$reported;
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
        }
        //Sort
        $sort = "";
        $sort_post = trim(Tools::getValue('sort'));
        $sort_type = Tools::strtolower(trim(Tools::getValue('sort_type','desc')));
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type='desc';
        if($sort_post && isset($fields_list[$sort_post]))
        {
            $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = 'bc.id_comment desc,';

        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<0)
            $page=1;
        $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementcomments',array('tabmanagament'=>'comment_other','page'=>'_page_',)).$this->module->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_comment');
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_comment_select_limit',20);
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $comments = Ybc_blog_comment_class::getCommentsWithFilter($filter, $sort, $start, $paggination->limit);
        if($comments)
        {
            foreach($comments as &$comment)
            {
                $comment['view_url'] = $this->module->getLink('blog', array('id_post' => $comment['id_post'])).'#blog_comment_line_'.$comment['id_comment'];
                $comment['view_text'] = $this->module->l('View in post','managementcomments');
                $comment['title'] = $this->module->displayText($comment['title'],'a',null,null,$comment['view_url'],true);
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment['id_comment']))
                    $comment['edit_url'] = $this->module->getLink('blog',array('id_post'=>$comment['id_post'],'edit_comment'=>$comment['id_comment']));
                if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment['id_comment']))
                    $comment['delete_url'] = $this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>'comment_other','id_comment'=>$comment['id_comment'],'deletecomment'=>1));

            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','managementcomments');
        $paggination->style_links = 'links';
        $paggination->style_results = 'results';
        $listData = array(
            'name' => 'ybc_comment',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementcomments',array('tabmanagament'=>'comment_other')).($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
            'identifier' => 'id_comment',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('My comments','managementcomments'),
            'fields_list' => $fields_list,
            'field_values' => $comments,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_comment'),
            'show_reset' => Tools::isSubmit('ybc_submit_ybc_comment') && isset($comment_post) && ($id_comment!='' || $comment_post!='' || $rating!='' || $subject!='' || $approved!='' || $reported!='' || $title!='') ? true : false,
            'totalRecords' => $totalRecords,
            'show_add_new' => false,
            'sort'=>$sort_post,
            'sort_type'=>$sort_type,
        );
        return $this->module->renderListByCustomer($listData);
    }
    private function _saveComment()
    {
        $id_comment = (int)Tools::getValue('id_comment');
        $tabmanagament = Tools::getValue('tabmanagament');
        if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
            $tabmanagament ='comment';
        if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament))
        {
            $ybc_comment= new Ybc_blog_comment_class($id_comment);
            if(!($subject = Tools::getValue('subject')))
                $this->_errros[]= $this->module->l('Subject is required','managementcomments');
            elseif(!Validate::isCleanHtml($subject))
                $this->_errros[]= $this->module->l('Subject is not valid','managementcomments');
            else
                $ybc_comment->subject = $subject;
            if(!($comment = Tools::getValue('comment')))
                $this->_errros[] = $this->module->l('Comment is requied','managementcomments');
            elseif(Tools::strlen($comment)<20)
                $this->_errros[]=$this->module->l('Comment needs to be at least 20 characters','managementcomments');
            elseif(!Validate::isCleanHtml($comment,true))
                $this->_errros[] = $this->module->l('Comment is not valid','managementcomments');
            else
                $ybc_comment->comment = Tools::getValue('comment');
            if(Tools::isSubmit('reply'))
            {
                $reply = Tools::getValue('reply');
                if($reply && !Validate::isCleanHtml($reply,true))
                    $this->_errros[] = $this->module->l('Reply is not valid','managementcomments');
                else
                {
                    $ybc_comment->reply = $reply;
                    if($reply)
                    {
                        $ybc_comment->replied_by = $this->context->customer->id;
                        $ybc_comment->customer_reply=1;
                    }
                    else
                        $ybc_comment->customer_reply=0;
                }
                
            }
            if(Tools::isSubmit('approved'))
            {
                $approved = (int)Tools::getValue('approved');
                $ybc_comment->approved = $approved;
            }
            $tabmanagament = Tools::getValue('tabmanagament');
            if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
                $tabmanagament ='post';
            if(!$this->_errros)
            {
                $ybc_comment->update();
                if(Tools::isSubmit('submitComment'))
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));
                else
                    $this->sussecfull = $this->module->l('Comment updated','managementcomments');
            }
                
        }
    }
    public function getBreadCrumb()
    {
        $nodes=array();
        $nodes[] = array(
            'title' => $this->module->l('Home','managementcomments'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $this->module->l('Your account','managementcomments'),
            'url' => $this->context->link->getPageLink('my-account'),
        );
        $nodes[] = array(
            'title' => $this->module->l('My blog comments','managementcomments'),
            'url' => $this->context->link->getModuleLink('ybc_blog','managementcomments'),
        );
        if($this->module->is17)
                return array('links' => $nodes,'count' => count($nodes));
        return $this->module->displayBreadcrumb($nodes);
    }
}