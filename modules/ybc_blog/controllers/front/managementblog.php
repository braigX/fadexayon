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
 * Class Ybc_blogManagementblogModuleFrontController
 * @property Ybc_blog $module;
 */
class Ybc_blogManagementblogModuleFrontController extends ModuleFrontController
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
    public function init()
    {
        parent::init();
        if (!$this->context->customer->isLogged())
        {
            Tools::redirect('index.php?controller=authentication');
        }
    }
    public function initContent()
	{
	    parent::initContent();
        $this->module->setMetas();
        if ($this->context->customer->isLogged())
		{
            if(Ybc_blog_post_employee_class::checkGroupAuthor())
            {
                $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($this->context->customer->id);
                if($id_employee_post && ($employePost = new Ybc_blog_post_employee_class($id_employee_post)) && $employePost->status <=0)
                {
                    $this->context->smarty->assign(
                        array(
                            'ok_author' =>false,
                        )
                    );
                    $this->_errros[]= $this->module->l('Your account has been suspended. Please contact webmaster for more information','managementblog');
                }
                else
                {
                    $this->context->smarty->assign(
                        array(
                            'ok_author' =>true,
                        )
                    );
                }
            }
            else
            {
                $this->context->smarty->assign(
                    array(
                        'ok_author'=>false,
                    )
                );
                $this->_errros[] = $this->module->l('Sorry, you do not have permission','managementblog');
            }
        }
        $tabmanagament = Tools::getValue('tabmanagament');
        if(!Validate::isCleanHtml($tabmanagament))
            $tabmanagament = 'post';
        if(Tools::isSubmit('submitComment') || Tools::isSubmit('submitCommentStay'))
            $this->_saveComment($tabmanagament);
        if(Tools::isSubmit('submitAuthorManagement'))
        {
            if(!Ybc_blog_post_employee_class::checkPermistionPost(0,'upload_avatar_information'))
                $this->_errros[] = $this->module->l('Sorry, you do not have permission','managementblog');
            else
                $this->_postAuthor();
        }
        if(Tools::isSubmit('submitPost') || Tools::isSubmit('submitPostStay'))
        {
            $this->_savePost();
        }
        if(Tools::isSubmit('commentapproved') && ($id_comment=(int)Tools::getValue('id_comment')))
        {
               if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
               {
                    $commentapproved = (int)Tools::getValue('commentapproved');
                    $comment->approved = (int)$commentapproved;
                    if($comment->update())
                    {
                        if(($idCustomer = Customer::customerExists($comment->email)) && ($customer = new Customer($idCustomer)) && Validate::isLoadedObject($customer))
                            $idLang = $customer->id_lang;
                        else
                            $idLang = $this->context->language->id;
                        if($commentapproved && ($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_comment',$idLang)))
                        {
                            $post = new Ybc_blog_post_class($comment->id_post,$idLang);
                            Mail::Send(
                                $idLang,
                                'approved_comment',
                                $subject,
                                array('{customer_name}' => $comment->name, '{email}' => $comment->email,'{rating}' => ' '.($comment->rating != 1 ? $this->module->l('stars','managementblog') : $this->module->l('star','managementblog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post->title,'{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                $comment->email, null, null, null, null, null,
                                dirname(__FILE__).'/../../mails/',
                                false);
                        }
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));

                    }
                    else
                        $this->_errros[]=$this->module->l('An error occurred while saving the comment','managementblog');

                    
               }
               else
                    $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
        }
        if(Tools::isSubmit('deletethumb') && ($id_post=(int)Tools::getValue('id_post')))
        {
            if(Ybc_blog_post_employee_class::checkPermistionPost($id_post,'edit_blog') && ($postObj = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($postObj))
            {
                $thumb = isset($postObj->thumb[$this->context->language->id]) ? $postObj->thumb[$this->context->language->id] :'';
                $postObj->thumb[$this->context->language->id] = '';
                if($postObj->update())
                {
                    if($thumb && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$thumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$thumb);
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'post','deletedthumb'=>1,'editpost'=>1,'id_post'=>$id_post)));
                }

            }
            else
               $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
        }
        if(Tools::isSubmit('deleteimage') && ($id_post=(int)Tools::getValue('id_post')))
        {
            if(Ybc_blog_post_employee_class::checkPermistionPost($id_post,'edit_blog') && ($postObj = new Ybc_blog_post_class($id_post,$this->context->language->id)) && Validate::isLoadedObject($postObj))
            {
                $image = isset($postObj->image[$this->context->language->id]) ? $postObj->image[$this->context->language->id] :'';
                $postObj->image[$this->context->language->id] = '';
                if($postObj->update())
                {
                    if($image && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$image))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$image);
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'post','deletedimage'=>1,'editpost'=>1,'id_post'=>$id_post)));
                }

            }
            else
               $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
        }
        if(Tools::isSubmit('deletepost') && ($id_post = (int)Tools::getValue('id_post')))
        {
            if(Ybc_blog_post_employee_class::checkPermistionPost($id_post,'delete_blog') && ($postObj = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($postObj) )
            {
                if(Ybc_blog_post_class::_deletePost($id_post))
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'post','deletedpost'=>1)));
                else
                    $this->_errros[] = $this->module->l('Delete failed','managementblog');
            }
            else
               $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
        }
        if(Tools::isSubmit('deletecomment') && ($id_comment=(int)Tools::getValue('id_comment')))
        {
            if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$id_comment,$tabmanagament) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
            {
                if($comment->delete())
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>$tabmanagament,'deletedcomment'=>1)));
            }
            else
               $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
        }
        if(Tools::isSubmit('deletedpost'))
            $this->sussecfull = $this->module->l('You have just deleted the blog post successfully','managementblog');    
        if(Tools::isSubmit('deletedcomment'))
            $this->sussecfull = $this->module->l('You have just deleted the comment successfully','managementblog');
        if(Tools::isSubmit('added'))
        {
            if(Configuration::get('YBC_BLOG_STATUS_POST')=='waiting_approval')
                $this->sussecfull = $this->module->l('Your new blog post has just been added successfully. It is waiting to be approved by Administrator','managementblog');
            else
                $this->sussecfull = $this->module->l('Your new blog post has just been added successfully','managementblog');
        }
        if(Tools::isSubmit('updated'))
        {
            $this->sussecfull = $this->module->l('Updated successfully','managementblog');
        }
        if(Tools::isSubmit('addedReply'))
            $this->sussecfull = $this->module->l('Reply has been submitted','managementblog');
        if(Tools::isSubmit('updateComment'))
            $this->sussecfull = $this->module->l('Comment updated','managementblog');
        if(Tools::isSubmit('updatedReply'))
            $this->sussecfull = $this->module->l('Reply updated','managementblog');
        if(Tools::isSubmit('updatedComment'))
            $this->sussecfull = $this->module->l('Comment updated','managementblog');
        if(Tools::isSubmit('deleteddReply'))
            $this->sussecfull = $this->module->l('Delete reply successfully','managementblog');
        if(Tools::isSubmit('deletedthumb'))
            $this->sussecfull = $this->module->l('Delete thumbnail image successfully','managementblog');
        if(Tools::isSubmit('deletedimage'))
            $this->sussecfull = $this->module->l('Delete image successfully','managementblog');
        $this->context->smarty->assign(
            array(
                'left_content_html' => $this->displayLeftFormManagament(),
                'form_html_post'=> $this->displayRightFormManagament(),
                'breadcrumb' => $this->module->is17 ? $this->getBreadCrumb() : false, 
                'path' => $this->getBreadCrumb(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/management_blog.tpl');      
        else         
            $this->setTemplate('management_blog16.tpl');  
    }
    public function displayLeftFormManagament()
    {
        $tabmanagament = ($tabmanagament = Tools::getValue('tabmanagament','post')) && Validate::isCleanHtml($tabmanagament) ? $tabmanagament :'post';
        if(!$this->module->isCached('blog_management_left.tpl',$this->module->_getCacheId($tabmanagament)))
        {
            $left_tabs= array(
                array(
                    'title'=> $this->l('My posts'),
                    'link'=> $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','list'=>true)),
                    'name'=>'post',
                ),
                array(
                    'title'=> $this->l('Comments'),
                    'link'=> $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','list'=>true)),
                    'name'=>'comment',
                ),
            );
            $this->context->smarty->assign(
                array(
                    'left_tabs'=>$left_tabs,
                    'tabmanagament' => $tabmanagament,
                )
            );
        }
        return $this->module->display($this->module->getLocalPath(),'blog_management_left.tpl',$this->module->_getCacheId($tabmanagament));
    }
    private function displayRightFormManagament()
    {
        $tabmanagament=Tools::getValue('tabmanagament');
        switch ($tabmanagament) {
            case 'post':
                $content_html_right = $this->renderPostListByCustomer();
                break;
            case 'comment':
                $content_html_right = $this->renderCommentListByCustomer();
                break;
            default:
                $content_html_right= $this->renderPostListByCustomer();
        }
        $this->context->smarty->assign(
            array(
                'errors_html'=>$this->_errros ? $this->module->displayError($this->_errros) : false,
                'sucsecfull_html' => $this->sussecfull ? $this->module->displaySuccessMessage($this->sussecfull):'',
                'content_html_right'=>$content_html_right,
            )
        );
        return $this->module->display($this->module->getLocalPath(),'blog_management_right.tpl');
    }
    private function renderCommentListByCustomer()
    {
        if(Tools::isSubmit('viewcomment') && ($id_comment=(int)Tools::getValue('id_comment')) && ($comment= new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment) )
        {
            $errors =array();
            if(Tools::isSubmit('change_approved_comment'))
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id))
                {
                    $approved = (int)Tools::getValue('approved');
                    $comment->approved = $approved;
                    if($comment->update())
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'updatedComment'=> 1)));
                    else
                        $errors[] = $this->module->l('Update failed','managementblog');
                }
                else
                    $errors[]=  $this->module->l('Sorry, you do not have permission','managementblog');
            }
            if(Tools::isSubmit('addReplyComment'))
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('reply',$id_comment))
                {
                    $reply_comment_text = Tools::getValue('reply_comment_text');
                    if(Tools::strlen($reply_comment_text) < 20)
                        $errors[] = $this->module->l('Reply needs to be at least 20 characters','managementblog');
                    if(!Validate::isCleanHtml($reply_comment_text,false))
                        $errors[] = $this->module->l('Reply needs to be clean HTML','managementblog');
                    if(Tools::strlen($reply_comment_text) >2000)
                        $errors[] = $this->module->l('Reply cannot be longer than 2000 characters','managementblog');
                    if(!$errors)
                    {
                        $replyObj = new Ybc_blog_reply_class();
                        $replyObj->id_comment = (int)$comment->id;
                        $replyObj->id_user = (int)$this->context->customer->id;
                        $replyObj->name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                        $replyObj->email = $this->context->customer->email;
                        $replyObj->reply = $reply_comment_text;
                        $replyObj->datetime_added = date('Y-m-d H:i:s');
                        $replyObj->datetime_updated = date('Y-m-d H:i:s');
                        if($replyObj->add())
                        {
                            $this->module->sendMailRepyCustomer($id_comment,$replyObj->name);
                            Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'addedReply'=> 1)));
                        }
                        else
                            $errors[] = $this->module->l('Add reply failed','managementblog');
                    }
                    if($errors)
                    {
                        $this->context->smarty->assign(
                            array(
                                'replyCommentsave' => $id_comment,
                                'reply_comment_text' => $reply_comment_text,
                            )
                        );
                    }
                }
                else
                    $errors[]=$this->module->l('Sorry, you do not have permission','managementblog');

            }
            if(Tools::isSubmit('delete_reply') && ($id_reply = (int)Tools::getValue('delete_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) )
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$id_comment))
                {
                    if($replyObj->delete())
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'deleteddReply'=> 1)));
                    else
                        $errors[]=$this->module->l('Delete reply failed','managementblog');
                }
                else
                    $errors[]=$this->module->l('Sorry, you do not have permission','managementblog');
            }
            if(Tools::isSubmit('change_approved_reply') && ($id_reply= (int)Tools::getValue('change_approved_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) )
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$id_comment))
                {
                    $approved = (int)Tools::getValue('approved');
                    $status_old = $replyObj->approved;
                    $replyObj->approved = $approved;
                    if($replyObj->update())
                    {
                        if($status_old!=$approved && $approved==1)
                        {
                            $this->module->sendMailRepyCustomer($id_comment,$replyObj->name,$replyObj->reply);
                        }
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'updatedReply'=> 1)));
                    }
                    else
                        $errors[]=$this->module->l('Update reply failed','managementblog');
                }
                else
                    $errors[]=$this->module->l('Sorry, you do not have permission','managementblog');
            }
            $replies= Ybc_blog_comment_class::getRepliesByIdComment($comment->id);
            if($replies)
            {
                foreach($replies as &$reply)
                {
                    if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id))
                        $reply['link_approved'] = $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'change_approved_reply'=> $reply['id_reply'],'approved' => $reply['approved'] ? 0 :1));
                    if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment->id))
                        $reply['link_delete'] = $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'delete_reply'=> $reply['id_reply']));
                    $reply['reply'] = str_replace("\n",'<'.'b'.'r/'.'>',$reply['reply']);
                    if($reply['id_employee'])
                    {
                        if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_employee'],false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name )
                            $reply['name']= $employeePost->name;
                        elseif(($employee = new Employee($reply['id_employee'])) && Validate::isLoadedObject($employee))
                            $reply['name']= $employee->firstname.' '.$employee->lastname;
                    }
                    if($reply['id_user'])
                    {
                        if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_user'],true)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name )
                            $reply['name']= $employeePost->name;
                        elseif(($customer = new Customer($reply['id_user'])) && Validate::isLoadedObject($customer))
                            $reply['name']= $customer->firstname.' '.$customer->lastname;
                    }
                }
            }
            $comment->comment = str_replace("\n",'<'.'b'.'r/'.'>',$comment->comment);
            $this->context->smarty->assign(
                array(
                    'comment'=>$comment,
                    'replies'=>$replies,
                    'post_link' => $this->module->getLink('blog',array('id_post'=>$comment->id_post)),
                    'link_delete' => Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment->id) ? $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','deletecomment'=>1,'id_comment'=>$comment->id)):'',
                    'link_approved' => Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id) ? $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'change_approved_comment'=>1,'approved' => $comment->approved ? 0 :1)):'',
                    'post_class' => new Ybc_blog_post_class($comment->id_post,$this->context->language->id),
                    'link_back'=> $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment')),
                )
            );
            return ($errors ? $this->module->displayError($errors): '' ).$this->module->display($this->module->getLocalPath(),'author_reply_comment.tpl');
        }
        $fields_list = array(
            'id_comment' => array(
                'title' => $this->module->l('Id','managementblog'),
                'width' => 40,
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'id_comment','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'id_comment','sort_type'=>'desc')),
                'filter' => true,
            ),
            'subject' => array(
                'title' => $this->module->l('Subject','managementblog'),
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'subject','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'subject','sort_type'=>'desc')),
                'filter' => true,
            ),
            'title' => array(
                'title' => $this->module->l('Blog post','managementblog'),
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'title','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'title','sort_type'=>'desc')),
                'filter' => true,
                'strip_tag'=>false,
            ),
            'rating' => array(
                'title' => $this->module->l('Rating','managementblog'),
                'type' => 'select',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'rating','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'rating','sort_type'=>'desc')),
                'filter' => true,
                'rating_field' => true,
                'filter_list' => array(
                    'id_option' => 'rating',
                    'value' => 'stars',
                    'list' => array(
                        0 => array(
                            'rating' => 0,
                            'stars' => $this->module->l('No reviews','managementblog')
                        ),
                        1 => array(
                            'rating' => 1,
                            'stars' => '1 '.$this->module->l('star','managementblog')
                        ),
                        2 => array(
                            'rating' => 2,
                            'stars' => '2 '.$this->module->l('stars','managementblog')
                        ),
                        3 => array(
                            'rating' => 3,
                            'stars' => '3 '.$this->module->l('stars','managementblog')
                        ),
                        4 => array(
                            'rating' => 4,
                            'stars' => '4 '.$this->module->l('stars','managementblog')
                        ),
                        5 => array(
                            'rating' => 5,
                            'stars' => '5 '.$this->module->l('stars','managementblog')
                        ),
                    )
                )
            ),
            'name' => array(
                'title' => $this->module->l('Customer','managementblog'),
                'type' => 'text',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'name','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'name','sort_type'=>'desc')),
                'filter' => true
            ),
            'approved' => array(
                'title' => $this->module->l('Approved','managementblog'),
                'type' => 'active',
                'sort' => $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'approved','sort_type'=>'asc')),
                'sort_desc'=>$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'approved','sort_type'=>'desc')),
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->module->l('Yes','managementblog')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->module->l('No','managementblog')
                        )
                    )
                )
            )

        );
        //Filter
        $filter = " AND p.added_by ='".(int)$this->context->customer->id."' AND p.is_customer=1";
        if(($id_comment = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id_comment))
            $filter .= " AND bc.id_comment = ".(int)$id_comment;
        if(($comment = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($comment) )
            $filter .= " AND bc.comment like '%".pSQL($comment)."%'";
        if(($subject =  trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
            $filter .= " AND (bc.subject like '%".pSQL($subject)."%' OR bc.comment like '%".pSQL($subject)."%' )";
        if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
            $filter .= " AND bc.rating = ".(int)$rating;
        if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
            $filter .= " AND bc.name like '%".pSQL($name)."%'";
        if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
            $filter .= " AND bc.approved = ".(int)$approved;
        if(($reported =  trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
            $filter .= " AND bc.reported = ".(int)$reported;
        if(($title =  trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            $filter .= " AND pl.title like '%".pSQL($title)."%'";
        //Sort
        $sort_post = Tools::strtolower(Tools::getValue('sort','id_comment'));
        if(!isset($fields_list[$sort_post]))
            $sort_post = 'id_comment';
        $sort_type = Tools::strtolower(trim(Tools::getValue('sort_type','desc')));
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type = 'desc';
        $sort = "";
        if(trim($sort_post) && isset($fields_list[$sort_post]))
        {
            $sort .= trim($sort_post)." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = 'bc.id_comment desc,';

        //Paggination
        $page = (int)Tools::getValue('page');
        if($page <0 )
            $page =1;
        $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','page'=>'_page_',)).$this->module->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_comment');
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
            foreach($comments as &$comment_val)
            {
                $comment_val['child_view_url']=$this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'viewcomment'=>1));
                $comment_val['view_url'] = $this->module->getLink('blog', array('id_post' => $comment_val['id_post'])).'#blog_comment_line_'.$comment_val['id_comment'];
                $comment_val['title'] = $this->module->displayText($comment_val['title'],'a',null,null,$comment_val['view_url']);
                $comment_val['view_text'] = $this->module->l('View in post','managementblog');
                if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('manage_comments',$privileges))
                {
                    $comment_val['edit_url'] = $this->module->getLink('blog',array('id_post'=>$comment_val['id_post'],'edit_comment'=>$comment_val['id_comment']));
                    $comment_val['delete_url'] = $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'deletecomment'=>1));
                    $comment_val['edit_approved'] = $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'commentapproved'=>!$comment_val['approved']));
                }
            }
            unset($comment_val);
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','managementblog');
        $paggination->style_links = 'links';
        $paggination->style_results = 'results';
        $listData = array(
            'name' => 'ybc_comment',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment')).($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
            'identifier' => 'id_comment',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('Customer comments','managementblog'),
            'fields_list' => $fields_list,
            'field_values' => $comments,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_comment'),
            'show_reset' => $id_comment!='' || $comment!='' || $rating !='' || $subject !=''  || $approved !='' || $reported !='' || $title!='' ? true : false,
            'totalRecords' => $totalRecords,
            'show_add_new' => false,
            'sort'=> $sort_post,
            'sort_type'=>$sort_type,
        );
        return $this->module->renderListByCustomer($listData);
    }
    /**
     * Post
     */
    private function renderPostListByCustomer()
    {
        if(!Tools::isSubmit('editpost') && !Tools::isSubmit('addpost'))
        {
            $fields_list = array(
                'id_post' => array(
                    'title' => $this->module->l('Id','managementblog'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'id_post','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'id_post','sort_type'=>'desc')),
                    'filter' => true,
                ),
                'thumb_link'=>array(
                    'title'=> $this->module->l('Image','managementblog'),
                    'type' => 'text',
                    'strip_tag'=>false,
                ),
                'title' => array(
                    'title' => $this->module->l('Title','managementblog'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'title','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'title','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag'=>false,
                ),
                'total_comment' => array(
                    'title' => $this->module->l('Comments','managementblog'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'total_comment','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'total_comment','sort_type'=>'desc')),
                ),
                'enabled' => array(
                    'title' => $this->module->l('Status','managementblog'),
                    'type' => 'active',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'enabled','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'enabled','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->module->l('Published','managementblog')
                            ),
                            1=>array(
                                'enabled'=>-1,
                                'title' =>$this->module->l('Pending','managementblog')
                            ),
                            2 => array(
                                'enabled' => 0,
                                'title' => $this->module->l('Unpublished','managementblog')
                            ),
                            3 => array(
                                'enabled' => 2,
                                'title' => $this->module->l('Schedule publish date','managementblog')
                            ),
                        )
                    )
                )
            );
            //Filter
            $filter=" AND p.added_by =".(int)$this->context->customer->id." AND p.is_customer=1";
            $show_reset = false;
            if(($idPost = trim(Tools::getValue('id_post')))!='' && Validate::isCleanHtml($idPost)){
                $filter .= " AND p.id_post = ".(int)$idPost;
                $show_reset = true;
            }
            if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
            {
                $filter .= " AND p.sort_order = ".(int)$sort_order;
                $show_reset = true;
            }
            if(($click_number = trim(Tools::getValue('click_number')))!='' && Validate::isCleanHtml($click_number))
            {
                $filter .= " AND p.click_number = ".(int)$click_number;
                $show_reset = true;
            }
            if(($likes = trim(Tools::getValue('likes')))!='' && Validate::isCleanHtml($likes))
            {
                $filter .= " AND p.likes = ".(int)$likes;
                $show_reset = true;
            }
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            {
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
                $show_reset = true;
            }
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
            {
                $filter .= " AND pl.description like '%".pSQL($description)."%'";
                $show_reset = true;
            }
            if(($idCategory = trim(Tools::getValue('id_category')))!='' && Validate::isCleanHtml($idCategory))
            {
                $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_category = ".(int)$idCategory.") ";
                $show_reset = true;
            }
            if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
            {
                $filter .= " AND p.enabled = ".(int)$enabled;
                $show_reset = true;
            }
            if(($is_featured = trim(Tools::getValue('is_featured')))!='' && Validate::isCleanHtml($is_featured))
            {
                $filter .= " AND p.is_featured = ".(int)$is_featured;
            }
            //Sort
            $sort = "";
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type = 'desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = false;
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<1)
                $page=1;
            $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','page'=>'_page_',)).$this->module->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_post');
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_post_select_limit',20);
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, $start, $paggination->limit);
            if($posts)
            {
                foreach($posts as &$post)
                {
                    $post['id_category'] = $this->module->getCategoriesStrByIdPost($post['id_post']);
                    $post['view_url'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                    $post['title'] = $this->module->displayText($post['title'],'a',null,null,$post['view_url']);
                    if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('edit_blog',$privileges))
                    {
                        $post['edit_url'] = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'id_post'=>$post['id_post']));
                    }
                    if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('delete_blog',$privileges))
                    {
                        $post['delete_url'] = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','deletepost'=>1,'id_post'=>$post['id_post']));
                    }
                }
            }
            $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','managementblog');
            $paggination->style_links ='links';
            $paggination->style_results = 'results';
            $listData = array(
                'name' => 'ybc_post',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post')).($paggination->limit!=20 ? '&paginator_ybc_post_select_limit='.$paggination->limit:''),
                'identifier' => 'id_post',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->module->l('Blog posts','managementblog'),
                'fields_list' => $fields_list,
                'field_values' => $posts,
                'paggination' => $paggination->render(),
                'filter_params' => $this->module->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_post'),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'totalPost' => (int)Ybc_blog_post_class::countPostsWithFilter(" AND p.added_by =".(int)$this->context->customer->id." AND p.is_customer=1"),
                'preview_link' => $this->module->getLink('blog'),
                'show_add_new' => true,
                'link_addnew'=> $this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>'post','addpost'=>1)),
                'sort'=> $sort_post,
                'sort_type'=> $sort_type,

            );
            return $this->renderListPostByCustomer($listData);
        }
        else
            return $this->displayFormBlog();
    }
    private function renderListPostByCustomer($listData)
    {
        if(isset($listData['fields_list']) && $listData['fields_list'])
        {
            foreach($listData['fields_list'] as $key => &$val)
            {
                $val['active'] = trim(Tools::getValue($key));
            }
        }
        $this->context->smarty->assign($listData);
        return $this->module->display($this->module->getLocalPath(), 'list_post_by_customer.tpl');
    }
    private function displayFormBlog()
    {
        if(($id_post=(int)Tools::getValue('id_post')))
        {
            if(!Ybc_blog_post_employee_class::checkPermistionPost($id_post,'edit_blog'))
            {
                return $this->module->displayError($this->module->l('Sorry, you do not have permission','managementblog'));
            }
            $ybc_post= new Ybc_blog_post_class($id_post,$this->context->language->id);
            $this->context->smarty->assign(
                array(
                    'link_delete_thumb' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'deletethumb'=>1,'id_post'=>$id_post)),
                    'link_delete_image' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'deleteimage'=>1,'id_post'=>$id_post)),
                    'link_post' => $this->module->getLink('blog',array('id_post'=>$id_post)),
                )
            );
        }
        else
        {
            if(!Ybc_blog_post_employee_class::checkPermistionPost(0,'add_new'))
                return $this->module->displayError($this->module->l('Sorry, you do not have permission','managementblog'));
            $ybc_post = new Ybc_blog_post_class();
        }
        $params = array(
            'tabmanagament' => 'post',
        );
        if(Tools::isSubmit('editpost'))
        {
            $params['editpost']=1;
        }
        else
            $params['addpost']=1;
        $this->context->smarty->assign(
            array(
                'ybc_post'=>$ybc_post,
                'link'=> $this->context->link,
                'link_back_list' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post')),
                'dir_img' => _PS_YBC_BLOG_IMG_,
                'action' => $this->context->link->getModuleLink('ybc_blog','managementblog',$params),
                'html_content_category_block' =>$this->module->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTreeFontEnd(0),$this->module->getSelectedCategories((int)$id_post),'',Ybc_blog_category_class::getCategoriesDisabled()),
            )
        );
        return $this->module->display($this->module->getLocalPath(),'form_blog.tpl');
    }
    public function uploadFile($file)
    {
        $width_image='';
        $height_image='';
        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
        if($file=='thumb')
        {
            $dir_img = _PS_YBC_BLOG_IMG_DIR_.'post/thumb/';
            $width_image =Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260);
            $height_image =Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180);
            $_FILES[$file]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$file]['name']);
            if(!Validate::isFileName($_FILES[$file]['name']))
                $this->_errros[] = $this->module->l('Thumbnail image name is not valid','managementblog');
            elseif($_FILES[$file]['size'] > $max_file_size)
                $this->_errros[] = sprintf($this->module->l('Thumbnail image file is too large. Limit: %s','managementblog'),Tools::ps_round($max_file_size/1048576,2).'Mb');
        }
        elseif($file=='image')
        {
            $width_image =Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920);
            $height_image =Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750);
            $dir_img = _PS_YBC_BLOG_IMG_DIR_.'post/';
            $_FILES[$file]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$file]['name']);
            if(!Validate::isFileName($_FILES[$file]['name']))
                $this->_errros[] = $this->module->l('Image name is not valid','managementblog');
            elseif($_FILES[$file]['size'] > $max_file_size)
                $this->_errros[] = sprintf($this->module->l('Image file is too large. Limit: %s','managementblog'),Tools::ps_round($max_file_size/1048576,2).'Mb');
        }
        elseif($file=='avata')
        {
            $dir_img = _PS_YBC_BLOG_IMG_DIR_.'avata/';
            $width_image =Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',null,null,null,300);
            $height_image =Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',null,null,null,300);
            $_FILES[$file]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$file]['name']);
            if(!Validate::isFileName($_FILES[$file]['name']))
                $this->_errros[] = $this->module->l('Avatar name is not valid','managementblog');
            elseif($_FILES[$file]['size'] > $max_file_size)
                $this->_errros[] = sprintf($this->module->l('Avatar file is too large. Limit: %s','managementblog'),Tools::ps_round($max_file_size/1048576,2).'Mb');
        }
        $_FILES[$file]['name'] = str_replace(' ','-',$_FILES[$file]['name']);    
        if(file_exists($dir_img.$_FILES[$file]['name']))
        {
            $_FILES[$file]['name'] = $this->module->createNewFileName($dir_img,$_FILES[$file]['name']);
        }                
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$file]['name'], '.'), 1));
		$thumbsize = @getimagesize($_FILES[$file]['tmp_name']);
		if(!$this->_errros)
        {
            if (isset($_FILES[$file]) &&				
    			!empty($_FILES[$file]['tmp_name']) &&
    			!empty($thumbsize) &&
    			in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
    		)
    		{
    			$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
    			if ($error = ImageManager::validateUpload($_FILES[$file]))
    				$this->_errros[] = $error;
    			elseif (!$temp_name || !move_uploaded_file($_FILES[$file]['tmp_name'], $temp_name))
    				$this->_errros[] = $this->module->l('Cannot upload the file','managementblog');
    			elseif (!ImageManager::resize($temp_name, $dir_img.$_FILES[$file]['name'], $width_image, $height_image, $type))
    				$this->_errros[] = $this->module->displayError($this->module->l('An error occurred during the thumbnail upload process.','managementblog'));
    			if (isset($temp_name) && file_exists($temp_name))
    				@unlink($temp_name);
                 ;
                return $_FILES[$file]['name'];			
    		}
            else
                $this->_errros[] = $this->module->l('Image file is invalid','managementblog');
        }
        return false;
    }
    public function _saveComment($tabmanagament)
    {
        $id_comment = (int)Tools::getValue('id_comment');
        if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament))
        {
            $ybc_comment= new Ybc_blog_comment_class($id_comment);
            if(!($subject = Tools::getValue('subject')))
                $this->_errros[]= $this->module->l('Subject is required','managementblog');
            elseif(!Validate::isCleanHtml($subject))
                $this->_errros[]= $this->module->l('Subject is not valid','managementblog');
            else
                $ybc_comment->subject = $subject;
            if(!($comment = Tools::getValue('comment')))
                $this->_errros[] = $this->module->l('Comment is required','managementblog');
            elseif(Tools::strlen($comment)<20)
                $this->_errros[] = $this->module->l('Comment needs to be at least 20 characters','managementblog');
            elseif(!Validate::isCleanHtml($comment))
                $this->_errros[] = $this->module->l('Comment is not valid','managementblog');
            else
                $ybc_comment->comment = $comment;
            if(Tools::isSubmit('reply'))
            {
                $reply = Tools::getValue('reply');
                if($reply && !Validate::isCleanHtml($reply))
                    $this->_errros[] = $this->module->l('Reply is not valid','managementblog');
                else
                    $ybc_comment->reply = $reply;
                if($reply)
                {
                    $ybc_comment->replied_by= $this->context->customer->id;
                    $ybc_comment->customer_reply=1;
                }
                else
                    $ybc_comment->customer_reply=0;
            }
            $apdate_approved=false;
            if(Tools::isSubmit('approved'))
            {
                $approved = (int)Tools::getValue('approved');
                if($ybc_comment->approved!= $approved && $approved==1)
                    $apdate_approved = true;
                $ybc_comment->approved = $approved;
            }
            if(!$this->_errros)
            {
                $ybc_comment->update();
                if(($idCustomer = Customer::customerExists($ybc_comment->email)) && ($customer = new Customer($idCustomer)) && Validate::isLoadedObject($customer))
                    $idLang = $customer->id_lang;
                else
                    $idLang = $this->context->language->id;
                $post = new Ybc_blog_post_class($ybc_comment->id_post,$idLang);
                if($apdate_approved && ($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_comment',$idLang)))
                {
                       Mail::Send(
                            $idLang, 
                            'approved_comment',
                            $subject,
                            array('{customer_name}' => $ybc_comment->name, '{email}' => $ybc_comment->email,'{rating}' => ' '.($ybc_comment->rating != 1 ? $this->module->l('stars','managementblog') : $this->module->l('star','managementblog')), '{subject}' => $ybc_comment->subject, '{comment}'=>$ybc_comment->comment,'{post_title}'=>$post->title,'{post_link}' => $this->module->getLink('blog', array('id_post' => $ybc_comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                            $ybc_comment->email, null, null, null, null, null, 
                            dirname(__FILE__).'/../../mails/', 
                            false
                            );
                }
                if(Tools::isSubmit('submitComment'))
                {
                    $tabmanagament = Tools::getValue('tabmanagament');
                    if(!Validate::isCleanHtml($tabmanagament))
                        $tabmanagament = 'post';
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementblog',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));
                }    
                else
                    $this->sussecfull = $this->module->l('Comment updated','managementblog');
            }   
        }
        else
            $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementblog');
    }
    public function _savePost()
    {
        $categories = Tools::getValue('blog_categories');
        $disabled_categories =  Ybc_blog_category_class::getCategoriesDisabled();
        $category_vaid=true;
        if($categories && $disabled_categories)
        {
            foreach($categories as $category)
                if(in_array($category,$disabled_categories))
                    $category_vaid=false;
        }
        $id_lang= $this->context->language->id;
        $languages = Language::getLanguages(false);
        if(($id_post=(int)Tools::getValue('id_post')))
        {
            if(Ybc_blog_post_employee_class::checkPermistionPost($id_post,'edit_blog'))
            {
                $ybc_post= new Ybc_blog_post_class($id_post);
                if(($title = trim(Tools::getValue('title'))))
                {
                    if(!Validate::isCatalogName($title))
                    {
                        $this->_errros[] = $this->module->l('Title is not valid','managementblog');
                    }
                    else
                    {
                        $ybc_post->title[$id_lang] = $title;
                        $ybc_post->url_alias[$id_lang] = Tools::link_rewrite($title);
                        if(str_replace(array('0','1','2','3','4','5','6','7','8','9'),'',Tools::substr($ybc_post->title[$id_lang],0,1))=='')
                            $this->_errros[] = $this->module->l('Post title cannot have number on the start position because it will cause error when you enable "Remove post ID" option','managementblog');
                        if($ybc_post->url_alias[$id_lang] && Ybc_blog_post_class::checkUrlAliasExists($ybc_post->url_alias[$id_lang],$ybc_post->id))
                        {
                            $ybc_post->url_alias[$id_lang] = $ybc_post->url_alias[$id_lang].'-'.$ybc_post->id;
                        }
                    }
                    
                }
                else
                    $this->_errros[] = $this->module->l('Title is required','managementblog');
                if(!$categories || !is_array($categories))
                    $this->_errros [] = $this->module->l('You need to choose at least 1 category','managementblog');
                elseif(!$category_vaid || !Ybc_blog::validateArray($categories))
                     $this->_errros [] = $this->module->l('Categories are not valid','managementblog');
                $main_category = (int)Tools::getValue('main_category');
                if(!$main_category)
                    $this->_errros[] = $this->module->l('Main category is required','managementblog');
                elseif($categories && !in_array($main_category,$categories))
                    $this->_errros[] =$this->module->l('Main category is not valid','managementblog');
                else
                    $ybc_post->id_category_default = (int)$main_category;
                if(($short_desc  = Tools::getValue('short_description')))
                {
                    if(Validate::isCleanHtml($short_desc,true))
                        $ybc_post->short_description[$id_lang] = $short_desc;
                    else
                        $this->_errros[] = $this->module->l('Short description is not valid','managementblog');
                }
                else
                    $this->_errros[] = $this->module->l('Short description is required','managementblog'); 
                if(($desc = Tools::getValue('description')))
                    if(!Validate::isCleanHtml($desc,true))
                          $this->_errros[] = $this->module->l('Post content is not valid','managementblog');
                    else
                        $ybc_post->description[$id_lang] = $desc;
                else
                    $this->_errros[] = $this->module->l('Post content is required','managementblog'); 
                $ybc_post->datetime_modified=date('Y-m-d H:i:s');
                if($_FILES['thumb']['name'])
                {
                    $oldthumb= $ybc_post->thumb[$id_lang]; 
                    $ybc_post->thumb[$id_lang]= $this->uploadFile('thumb');
                    $newThumb  = $ybc_post->thumb[$id_lang];
                }
                elseif(!$ybc_post->id)
                    $this->_errros[]= $this->module->l('Post thumbnail is required','managementblog');    
                if($_FILES['image']['name'])
                {
                    $oldimage= $ybc_post->image[$id_lang];
                    $ybc_post->image[$id_lang] = $this->uploadFile('image');
                    $newImage = $ybc_post->image[$id_lang];
                }
            }
            else
                $this->_errros[] = $this->module->l('Sorry, you do not have permission','managementblog');
        }
        else
        {
            if(Ybc_blog_post_employee_class::checkPermistionPost(0,'add_new'))
            {
                $ybc_post= new Ybc_blog_post_class();
                if(($title = trim(Tools::getValue('title'))))
                {
                    if(!Validate::isCatalogName($title))
                    {
                        $this->_errros[] = $this->module->l('Title is not valid','managementblog');
                    }
                    else
                    {
                        if(str_replace(array('0','1','2','3','4','5','6','7','8','9'),'',Tools::substr($title,0,1))=='')
                            $this->_errros[] = $this->module->l('Post title cannot have number on the start position because it will cause error when you enable "Remove post ID" option','managementblog');
                        else
                        {
                            foreach($languages as $language)
                            {
                                $ybc_post->title[$language['id_lang']]= trim($title);
                                $ybc_post->url_alias[$language['id_lang']] = Tools::link_rewrite($title);
                                if($ybc_post->url_alias[$language['id_lang']] && Ybc_blog_post_class::checkUrlAliasExists($ybc_post->url_alias[$language['id_lang']],$ybc_post->id))
                                {
                                    $maxId = 1 + (int)Ybc_blog_post_class::getMaxID();
                                    $ybc_post->url_alias[$language['id_lang']] = $ybc_post->url_alias[$language['id_lang']].'-'.$maxId;
                                }
                            }
                        }
                    }
                }
                else
                    $this->_errros[] = $this->module->l('Title is required','managementblog');
                if(!$categories || !is_array($categories))
                    $this->_errros [] = $this->module->l('You need to choose at least 1 category','managementblog');
                elseif(!$category_vaid || !Ybc_blog::validateArray($categories))
                     $this->_errros [] = $this->module->l('Categories are not valid','managementblog');
                if(!($main_category = (int)Tools::getValue('main_category')))
                    $this->_errros[] = $this->module->l('Main category is required','managementblog');
                elseif(!in_array($main_category,$categories))
                    $this->_errros[] =$this->module->l('Main category is not valid','managementblog');
                else
                    $ybc_post->id_category_default = (int)$main_category;
                if(($short_description = Tools::getValue('short_description')))
                {
                    if(Validate::isCleanHtml($short_description))
                    {
                        foreach($languages as $language)
                            $ybc_post->short_description[$language['id_lang']] = $short_description;
                    }
                    else
                        $this->_errros[] = $this->module->l('Short description is not valid','managementblog');
                }
                else
                    $this->_errros[] = $this->module->l('Short description is required','managementblog'); 
               if(($description = Tools::getValue('description')))
               {
                    if( !Validate::isCleanHtml($description))
                          $this->_errros[] = $this->module->l('Post content is not valid','managementblog');
                    else
                    {
                        foreach($languages as $language)
                            $ybc_post->description[$language['id_lang']] = $description;
                    }
               }
               else
                    $this->_errros[] =$this->module->l('Post content is required','managementblog'); 
               $ybc_post->datetime_modified=date('Y-m-d H:i:s');
               $ybc_post->datetime_added=date('Y-m-d H:i:s');
               $ybc_post->sort_order =1+ (int)Ybc_blog_post_class::getMaxOrder();
                if($_FILES['thumb']['name'])
                {
                    $newThumb= $this->uploadFile('thumb');
                    foreach($languages as $language)
                        $ybc_post->thumb[$language['id_lang']] = $newThumb;
                }
                elseif(!$ybc_post->id)
                    $this->_errros[]= $this->module->l('Post thumbnail is required','managementblog');      
                if($_FILES['image']['name'])
                {
                    $newImage= $this->uploadFile('image');
                    foreach($languages as $language)
                        $ybc_post->image[$language['id_lang']] = $newImage;
                }
                $ybc_post->added_by = $this->context->customer->id;
                $ybc_post->is_customer = 1;
                if(Configuration::get('YBC_BLOG_STATUS_POST')=='active')
                    $ybc_post ->enabled=1;
                else
                    $ybc_post ->enabled=-1;
            }
            else
                $this->_errros[] = $this->module->l('Sorry, you do not have permission','managementblog');
            
        }
        if(!$this->_errros)
        {
            if($ybc_post->id)
            {
                if($ybc_post->update())
                {
                    if(isset($oldimage) && $oldimage && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldimage) && !in_array($oldimage,$ybc_post->image))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldimage);
                    if(isset($oldthumb) && $oldthumb  && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldthumb) && !in_array($oldthumb,$ybc_post->thumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldthumb);
                    Ybc_blog_post_class::updateCategories($categories, $ybc_post->id);
                    if(Tools::isSubmit('submitPostStay'))
                        Tools::redirect($this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','updated'=>1,'editpost'=>1,'id_post'=>$ybc_post->id)));
                    else
                        Tools::redirect($this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','updated'=>1)));
                }
                else
                { 
                    if(isset($newImage) && $newImage && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                    if(isset($newThumb) && $newThumb  && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb);
                    $this->_errros[]= $this->module->l('Update failed','managementblog');
                }
            }
            else
            {
                if($ybc_post->add())
                {
                    Ybc_blog_post_class::updateCategories($categories, $ybc_post->id);
                    if(($emails = Configuration::get('YBC_BLOG_ALERT_EMAILS')) && ($emails= explode(',',$emails)))
                    {
                        $template_admin_vars=array(
                            '{customer_name}' => $this->context->customer->firstname .' '.$this->context->customer->lastname,
                            '{post_title}' => $ybc_post->title[$this->context->language->id],
                            '{post_link}'=> $this->module->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER'),
                            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                        );
                        foreach($emails as $email)
                        {
                            if(Validate::isEmail($email))
                            {
                                $employeeObj = new Employee();
                                if(($employee = $employeeObj->getByEmail($email)) && ($lang = new Language($employee->id_lang)) && Validate::isLoadedObject($lang) && $lang->active )
                                    $idLang = $lang->id;
                                else
                                    $idLang = Context::getContext()->language->id;
                                if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('new_blog_admin'.($ybc_post ->enabled==1 ? '_1':'_0'),$idLang)))
                                {
                                    Mail::Send(
                            			$idLang,
                            			'new_blog_admin'.($ybc_post ->enabled==1 ? '_1':'_0'),
                            			$subject,
                            			$template_admin_vars,
                    			        $email,
                            			Configuration::get('PS_SHOP_NAME'),
                            			null,
                            			null,
                            			null,
                            			null,
                            			dirname(__FILE__).'/../../mails/'
                                    );
                                }
                                
                            }
                        }
                    }
                    if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('new_blog_customer'.($ybc_post->enabled==1 ? '_1':'_0'))))
                    {
                        $template_customer_vars=array(
                            '{customer_name}' => $this->context->customer->firstname .' '.$this->context->customer->lastname,
                            '{post_title}' => $ybc_post->title[$this->context->language->id],
                            '{post_link}'=> $this->module->getLink('blog',array('id_post'=>$ybc_post->id)),
                            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                        );
                        Mail::Send(
                			Context::getContext()->language->id,
                			'new_blog_customer'.($ybc_post->enabled==1 ? '_1':'_0'),
                			$subject,
                			$template_customer_vars,
        			        $this->context->customer->email,
                			$this->context->customer->firstname .' '.$this->context->customer->lastname,
                			null,
                			null,
                			null,
                			null,
                			dirname(__FILE__).'/../../mails/'
                        );
                    }
                    if(Tools::isSubmit('submitPostStay'))
                        Tools::redirect($this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','added'=>1,'editpost'=>1,'id_post'=>$ybc_post->id)));
                    else
                        Tools::redirect($this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','added'=>1)));
                }
                else
                {
                    $this->_errros[]= $this->module->l('Adding blog post failed','managementblog');
                    if(isset($newImage) && $newImage && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                    if(isset($newThumb) && $newThumb  && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb);
                }
            }
        }
    }
    public function _postAuthor()
    {
        if(Tools::isSubmit('delemployeeimage'))
        {
            if(($id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($this->context->customer->id,true)))
            {
                $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
                $avatar = $employeePost->avata;
                $employeePost->avata='';
                if($employeePost->update())
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$avatar))
                    {
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$avatar);
                        $this->sussecfull = $this->module->l('Delete image successfully','managementblog');
                    }
                }
            }
        }
        if(Tools::isSubmit('submitAuthorManagement'))
        {
            if(($id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($this->context->customer->id)))
            {
                $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
            }
            else
            {
                $employeePost = new Ybc_blog_post_employee_class();
                $employeePost->id_employee=$this->context->customer->id;
                $employeePost->is_customer=1;
            }

            if(!($author_name = Tools::getValue('author_name')))
            {
                $this->_errros[] = $this->module->l('Name is required','managementblog');
            }
            elseif(!Validate::isCleanHtml($author_name))
                $this->_errros[] = $this->module->l('Name is not valid','managementblog');
            else
                $employeePost->name = $author_name;
            $author_description = Tools::getValue('author_description');
            if($author_description && !Validate::isCleanHtml($author_description,true))
                $this->_errros[] = $this->module->l('Description is not valid','managementblog');
            if($id_employee_post)
            {
                $employeePost->description[$this->context->language->id] = $author_description;
            }
            else
            {
               $languages= Language::getLanguages(false);
                foreach($languages as $language)
                {
                    $employeePost->description[$language['id_lang']] = $author_description;
                } 
            }
            $oldImage = false;
            if(isset($_FILES['author_avata']['tmp_name']) && isset($_FILES['author_avata']['name']) && $_FILES['author_avata']['name'])
            {
                $_FILES['author_avata']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['author_avata']['name']);
                if(!Validate::isFileName($_FILES['author_avata']['name']))
                    $this->_errros[] = $this->module->l('Avatar is invalid','managementblog');
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$_FILES['author_avata']['name']))
                    {
                        $file_name = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'avata/',$_FILES['author_avata']['name']);
                    }
                    else
                        $file_name = $_FILES['author_avata']['name'];
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['author_avata']['name'], '.'), 1));
                    $imagesize = @getimagesize($_FILES['author_avata']['tmp_name']);
                    if (isset($_FILES['author_avata']) &&
                        in_array($type, array('jpg', 'gif', 'jpeg', 'png')) &&
                        !empty($_FILES['author_avata']['tmp_name']) &&
                        !empty($imagesize)
                    )
                    {
                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                        if($_FILES['author_avata']['size'] > $max_file_size*1024*1024)
                            $this->_errros[] = sprintf($this->module->l('Avatar image file is too large. Limit: %sMb','managementblog'),$max_file_size);
                        elseif (!$temp_name || !move_uploaded_file($_FILES['author_avata']['tmp_name'], $temp_name))
                            $this->_errros[] = $this->module->l('Cannot upload the file','managementblog');
                        elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'avata/'.$file_name, null, null, $type))
                            $this->_errros[] = $this->module->displayError($this->module->l('An error occurred during the image upload process.','managementblog'));
                        if (isset($temp_name) && file_exists($temp_name))
                            @unlink($temp_name);
                        if($employeePost->avata)
                            $oldImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                        $employeePost->avata = $file_name;
                    }
                    else
                        $this->_errros[] = $this->module->l('Avatar is invalid','managementblog');
                }
            }
            if(!$this->_errros)
            {
                if($id_employee_post)
                {
                    if(!$employeePost->update())
                        $this->_errros[] = $this->module->displayError($this->module->l('The author could not be updated.','managementblog'));
                    else
                        $this->sussecfull = $this->module->l('Information updated','managementblog');
                }
                else
                    if(!$employeePost->add())
                        $this->_errros[] = $this->module->displayError($this->module->l('The author could not be updated.','managementblog'));
                    else
                        $this->sussecfull = $this->module->l('Information updated','managementblog');
                        
            }
            if (!count($this->_errros) && $oldImage && file_exists($oldImage))
                @unlink($oldImage);
        }
    }
    public function getBreadCrumb()
    {
        $nodes=array();
        $nodes[] = array(
            'title' => $this->module->l('Home','managementblog'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $this->module->l('Your account','managementblog'),
            'url' => $this->context->link->getPageLink('my-account'),
        );
        $nodes[] = array(
            'title' => $this->module->l('My blog posts','managementblog'),
            'url' => $this->context->link->getModuleLink('ybc_blog','managementblog'),
        );
        if($this->module->is17)
                return array('links' => $nodes,'count' => count($nodes));
        return $this->module->displayBreadcrumb($nodes);
    }
}