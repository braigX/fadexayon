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
 * Class Ybc_blogBlogModuleFrontController
 * @property Ybc_blog $module;
 */
class Ybc_blogBlogModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    private $useCache = true;
    public function __construct()
	{
		parent::__construct();
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='right')
            $this->display_column_right = true;
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='left')
            $this->display_column_left =true;
		$this->context = Context::getContext();
	}
	private function actionSubmitPoll($id_post)
    {
        $errors=array();
        $post_class = new Ybc_blog_post_class($id_post,$this->context->language->id);
        if(!$ybc_blog_polls = $this->getPollsCurrent($id_post))
            $ybc_blog_polls = new Ybc_blog_polls_class();
        if($this->context->customer->logged)
        {
            $ybc_blog_polls->id_user=$this->context->customer->id;
            $ybc_blog_polls->name =$this->context->customer->firstname. ' '.$this->context->customer->lastname;
            $ybc_blog_polls->email = $this->context->customer->email;
        }
        else
        {
            $ybc_blog_polls->id_user=0;
            if(($polls_name = trim(Tools::getValue('polls_name')))=='')
                $errors[] = $this->module->l('Name is required','blog');
            elseif(!Validate::isName($polls_name))
                $errors[] = $this->module->l('Name is not valid','blog');
            else
                $ybc_blog_polls->name = $polls_name;
            if(($polls_email = trim(Tools::getValue('polls_email')))=='')
                $errors[]=$this->module->l('Email is required','blog');
            elseif(!Validate::isEmail($polls_email))
                $errors[]=$this->module->l('Email is invalid','blog');
            else
                $ybc_blog_polls->email= $polls_email;
        }
        $polls_feedback = Tools::getValue('polls_feedback');
        $YBC_BLOG_POLLS_TEXT_MAXIMUM = (int)Configuration::get('YBC_BLOG_POLLS_TEXT_MAXIMUM') ? : 500;
        if(Configuration::get('YBC_BLOG_POLLS_FEEDBACK_NEED') && trim($polls_feedback)=='')
            $errors[] = $this->module->l('Feedback is required','blog');
        elseif(trim($polls_feedback) && Tools::strlen(trim($polls_feedback))<20)
            $errors[] = $this->module->l('Feedback needs to be at least 20 characters','blog');
        elseif(trim($polls_feedback) && Tools::strlen(trim($polls_feedback)) > $YBC_BLOG_POLLS_TEXT_MAXIMUM)
            $errors[] = sprintf($this->module->l('Feedback cannot be longer than %s characters','blog'),$YBC_BLOG_POLLS_TEXT_MAXIMUM);
        if(!Validate::isCleanHtml($polls_feedback,false))
            $errors[] = $this->module->l('Feedback needs to be clean HTML','blog');
        else
            $ybc_blog_polls->feedback = $polls_feedback;
        if(Configuration::get('YBC_BLOG_ENABLE_POLLS_CAPCHA'))
        {
            if(Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' || Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google3')
            {
                $g_recaptcha = Tools::getValue('g-recaptcha-response');
                if(!$g_recaptcha)
                {
                    $errors[] = $this->module->l('reCAPTCHA is invalid','blog');
                }
                else
                {
                    $recaptcha = $g_recaptcha ?  : false;
                    if ($recaptcha) {
                        $response = json_decode(Tools::file_get_contents($this->module->link_capcha), true);
                        if ($response['success'] == false) {
                            $errors[] = $this->module->l('reCAPTCHA is invalid');
                        }
                    }
                }
            }
            else
            {
                $security_polls_capcha_code = call_user_func('ets_getCookie','security_polls_capcha_code');
                $polls_capcha_code = Tools::getValue('polls_capcha_code');
                if(trim($polls_capcha_code)=='')
                    $errors[] = $this->module->l('Captcha is required','blog');
                elseif(trim($polls_capcha_code)!=$security_polls_capcha_code)
                    $errors[]=$this->module->l('Captcha is invalid','blog');
            }

        }
        if(!$errors)
        {
            $ybc_blog_polls->id_post = (int)$id_post;
            $ybc_blog_polls->polls =(int)Tools::getValue('polls_post');
            $ybc_blog_polls->dateadd = date('Y-m-d H:i:s');
            if($ybc_blog_polls->save())
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$id_post,
                ));
                if(!$this->context->customer->logged)
                {
                    if($this->context->cookie->id_post_polls)
                    {
                        $id_post_polls= json_decode($this->context->cookie->id_post_polls,true);
                    }
                    else
                        $id_post_polls=array();
                    if(!isset($id_post_polls[$id_post]))
                    {
                        $id_post_polls[$id_post]=$ybc_blog_polls->id;
                        $this->context->cookie->id_post_polls = json_encode($id_post_polls);
                        $this->context->cookie->write();
                    }

                }
                $this->sendMailAdminVoteNew($ybc_blog_polls,$post_class);
                die(
                json_encode(
                    array(
                        'sussec' => $this->module->displaySuccessMessage($this->module->l('You have submitted your feedback successfully. Thank you!','blog')),
                        'polls_post_helpful_no' => Ybc_blog_polls_class::countPollsWithFilter(' AND po.polls=0 AND p.id_post='.(int)$id_post),
                        'polls_post_helpful_yes' => Ybc_blog_polls_class::countPollsWithFilter(' AND po.polls=1 AND p.id_post='.(int)$id_post),
                    )
                )
                );

            }
            else
            {
                die(
                json_encode(
                    array(
                        'error' => $this->module->displayError($this->module->l('Feedback submitting failed','blog')),
                    )
                )
                );
            }
        }
        else
        {
            die(
                json_encode(
                    array(
                        'error' => $this->module->displayError($errors),
                    )
                )
            );
        }
    }
    private function getIdPost()
    {
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$this->context->language->id);
            if($id_post && $this->module->friendly && !Configuration::get('YBC_BLOG_URL_NO_ID'))
            {
                $this->module->redirect($this->module->getLink('blog',array('id_post'=>$id_post)));
            }
        }
        elseif($id_post && Configuration::get('YBC_BLOG_URL_NO_ID') && !Tools::isSubmit('edit_comment') && !Tools::isSubmit('all_comment') && $this->module->friendly)
        {
            $this->module->redirect($this->module->getLink('blog',array('id_post'=>$id_post)));
        }
        if($id_post && $this->module->friendly && (Tools::strpos($_SERVER['REQUEST_URI'],'post_url_alias') !==false || Tools::strpos($_SERVER['REQUEST_URI'],'url_alias')!==false))
        {
            $this->module->redirect($this->module->getLink('blog',array('id_post'=>$id_post)));
        }
        return $id_post;
    }
    private function getIdCategory()
    {
        $id_category = (int)trim(Tools::getValue('id_category'));
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias);
            if(!Configuration::get('YBC_BLOG_URL_NO_ID') && $id_category)
                $this->module->redirect($this->module->getLink('blog',array('id_category'=>$id_category)));

        }
        elseif($id_category && Configuration::get('YBC_BLOG_URL_NO_ID') && $this->module->friendly)
            $this->module->redirect($this->module->getLink('blog',array('id_category'=>$id_category)));
        if($id_category && $this->module->friendly && (Tools::strpos($_SERVER['REQUEST_URI'],'category_url_alias') !==false || Tools::strpos($_SERVER['REQUEST_URI'],'url_alias')!==false))
        {
            $this->module->redirect($this->module->getLink('blog',array('id_category'=>$id_category)));
        }
        if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) && $category->enabled && $category->id_shop == $this->context->shop->id)
            return $id_category;
        return 0;
    }
	public function init()
	{
		parent::init();
        if(($id_post = (int)Tools::getValue('id_post')) && !(Validate::isLoadedObject(new Ybc_blog_post_class($id_post))))
            Tools::redirect($this->module->getLink('blog'));
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
    public function displayDetailPost($id_post)
    {
        if($id_post)
        {
            $browser= $this->module->getDevice();
            if(Tools::strpos($browser,'unknown')!==false)
                $browser = $this->module->l('Unknown','blog');
            Ybc_blog_post_class::logViewCustomer($id_post,$browser);
            if(Tools::isSubmit('polls_submit') && $id_post)
            {
                $this->actionSubmitPoll($id_post);
            }
            if(Tools::isSubmit('bcsubmit') && (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT'))
            {
                $this->useCache = false;
            }
            if(Tools::isSubmit('replyCommentsave') || $this->context->cookie->success_reply || $this->context->cookie->success || Tools::isSubmit('edit_comment'))
            {
                $this->useCache = false;
            }
        }
        if(!$this->useCache || !$this->module->isCached('single_post.tpl',$this->module->_getCacheId(array('single_post',$id_post))))
        {
            if($id_post && ($blogObj = new Ybc_blog_post_class($id_post)) &&  Validate::isLoadedObject($blogObj) && $blogObj->id_shop == $this->context->shop->id)
            {
                if($id_post && Validate::isLoadedObject(new Ybc_blog_post_class($id_post)))
                {
                    $browser= $this->module->getDevice();
                    if(Tools::strpos($browser,'unknown')!==false)
                        $browser = $this->module->l('Unknown','blog');
                    Ybc_blog_post_class::logViewCustomer($id_post,$browser);
                    $post = $this->getPost((int)$id_post);
                    $errors = array();
                    $success = false;
                    $success_reply = false;
                    if(Tools::isSubmit('bcsubmit') && (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT'))
                    {
                        $justAdded=false;
                        if(($id_comment = (int)Tools::getValue('id_comment')) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment) )
                        {
                            if(!Ybc_blog_post_employee_class::checkPermisionComment('edit',$id_comment,'post'))
                                $errors[] = $this->module->l('Sorry, you do not have permission');
                        }
                        else
                        {
                            $comment = new Ybc_blog_comment_class();
                            if($post['is_customer'] && $post['added_by']== $this->context->customer->id)
                                $comment->approved=1;
                            else
                                $comment->approved = (int)Configuration::get('YBC_BLOG_COMMENT_AUTO_APPROVED') ? 1 : 0;
                        }
                        $comment->subject = trim(Tools::getValue('subject'));
                        $comment_old = $comment->comment;
                        $comment->comment = trim(Tools::getValue('comment'));
                        $comment->id_post = (int)$id_post;
                        $comment->datetime_added = date('Y-m-d H:i:s');
                        $comment->viewed=0;
                        $name_customer = Tools::getValue('name_customer');
                        $email_customer = Tools::getValue('email_customer');
                        if((int)$this->context->cookie->id_customer)
                        {
                            $comment->id_user = (int)$this->context->cookie->id_customer;
                            $comment->name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                            $comment->email = $this->context->customer->email;
                        }
                        else
                        {
                            $comment->name = $name_customer;
                            $comment->email = $email_customer;
                        }
                        $comment->rating = (int)Tools::getValue('rating');
                        $comment->reported = 1;
                        if(!$this->context->cookie->id_customer)
                        {
                            if(!$name_customer)
                                $errors[] = $this->module->l('Name is required','blog');
                            elseif($name_customer && !Validate::isCleanHtml($name_customer))
                            {
                                $errors[] = $this->module->l('Name is required','blog');
                            }
                            if($email_customer && !Validate::isEmail($email_customer))
                            {
                                $errors[] = $this->module->l('Invalid email address','blog');
                            }
                        }
                        if(Tools::strlen($comment->subject) < 10)
                            $errors[] = $this->module->l('Subject needs to be at least 10 characters','blog');
                        if(Tools::strlen($comment->subject) >300)
                            $errors[] = $this->module->l('Subject cannot be longer than 300 characters','blog');
                        if(!Validate::isCleanHtml($comment->subject,false))
                            $errors[] = $this->module->l('Subject needs to be clean HTML','blog');
                        if(Tools::strlen($comment->comment) < 20)
                            $errors[] = $this->module->l('Comment needs to be at least 20 characters','blog');
                        if(!Validate::isCleanHtml($comment->comment,false))
                            $errors[] = $this->module->l('Comment needs to be clean HTML','blog');
                        if(Tools::strlen($comment->comment) >2000)
                            $errors[] = $this->module->l('Subject cannot be longer than 2000 characters','blog');
                        if(!$comment->id_user && !(int)Configuration::get('YBC_BLOG_ALLOW_GUEST_COMMENT'))
                            $errors[] = $this->module->l('You need to log in before posting a comment','blog');
                        if((int)Configuration::get('YBC_BLOG_ALLOW_RATING'))
                        {
                            if($comment->rating > 5 || $comment->rating < 1)
                                $errors[] = $this->module->l('Rating needs to be from 1 to 5','blog');
                        }
                        else
                            $comment->rating = 0;
                        if((int)Configuration::get('YBC_BLOG_USE_CAPCHA'))
                        {
                            if(Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' || Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google3')
                            {
                                $g_recaptcha = Tools::getValue('g-recaptcha-response');
                                if(!$g_recaptcha)
                                {
                                    $errors[] = $this->module->l('reCAPTCHA is invalid','blog');
                                }
                                else
                                {
                                    $recaptcha = $g_recaptcha ? $g_recaptcha : false;
                                    if ($recaptcha) {
                                        $response = json_decode(Tools::file_get_contents($this->module->link_capcha), true);
                                        if ($response['success'] == false) {
                                            $errors[] = $this->module->l('reCAPTCHA is invalid');
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $savedCode = call_user_func('ets_getCookie','ybc_security_capcha_code');
                                $capcha_code = trim(Tools::getValue('capcha_code'));
                                if(!$capcha_code)
                                    $errors[] = $this->module->l('Security code is required','blog');
                                elseif($savedCode && Tools::strtolower($capcha_code)!=Tools::strtolower($savedCode))
                                {
                                    $errors[] = $this->module->l('Security code is invalid','blog');
                                }
                            }
                        }
                        if(!count($errors))
                        {
                            if($comment->id)
                            {
                                $comment->update();
                                if((int)$this->context->cookie->id_customer)
                                {
                                    $customer = new Customer((int)$this->context->cookie->id_customer);
                                    if(Ybc_blog_email_template_class::getSubjectByTemplate('edit_comment'))
                                        $this->sendCommentNotificationEmail(
                                            trim($customer->firstname.' '.$customer->lastname),
                                            $customer->email,
                                            $comment->subject,
                                            $comment->comment,
                                            $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')),
                                            $this->module->getLink('blog', array('id_post' => $comment->id_post)),
                                            'edit_comment',
                                            $comment_old
                                        );
                                    if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('edit_comment_customer')))
                                    {
                                        Mail::Send(
                                            $this->context->language->id,
                                            'edit_comment_customer',
                                            $subject,
                                            array('{customer}' => $customer->firstname.' '.$customer->lastname, '{email}' => $customer->email,'{rating}' => $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post['title'],'{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                            $customer->email, null, null, null, null, null,
                                            dirname(__FILE__).'/../../mails/',
                                            false, $this->context->shop->id
                                        );
                                    }
                                }
                                else
                                {
                                    if(Ybc_blog_email_template_class::getSubjectByTemplate('edit_comment'))
                                        $this->sendCommentNotificationEmail(
                                            trim($name_customer),
                                            $email_customer,
                                            $comment->subject,
                                            $comment->comment,
                                            $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')),
                                            $this->module->getLink('blog', array('id_post' => $comment->id_post)),
                                            'edit_comment',
                                            $comment_old
                                        );
                                    if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('edit_comment_customer')))
                                    {
                                        Mail::Send(
                                            $this->context->language->id,
                                            'edit_comment_customer',
                                            $subject,
                                            array('{customer}' => trim($name_customer), '{email}' => $email_customer,'{rating}' => $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post['title'], '{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                            $email_customer, null, null, null, null, null,
                                            dirname(__FILE__).'/../../mails/',
                                            false, $this->context->shop->id
                                        );
                                    }

                                }
                                $justAdded = true;
                                $success = $this->module->l('Comment has been updated ','blog');
                            }
                            else
                            {
                                $comment->add();
                                if((int)$this->context->cookie->id_customer)
                                {
                                    $customer = new Customer((int)$this->context->cookie->id_customer);
                                    $this->sendCommentNotificationEmail(
                                        trim($customer->firstname.' '.$customer->lastname),
                                        $customer->email,
                                        $comment->subject,
                                        $comment->comment,
                                        $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')),
                                        $this->module->getLink('blog', array('id_post' => $comment->id_post)),
                                        'new_comment'.($comment->approved==1?'_1':'_0')
                                    );
                                    if(($subjectMail = Ybc_blog_email_template_class::getSubjectByTemplate('new_comment_customer'.($comment->approved==1?'_1':'_0'))))
                                        Mail::Send(
                                            $this->context->language->id,
                                            'new_comment_customer'.($comment->approved==1?'_1':'_0'),
                                            $subjectMail,
                                            array('{customer_name}' => $customer->firstname.' '.$customer->lastname, '{email}' => $customer->email,'{rating}' => ' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment, '{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{post_title}'=>$post['title'],'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                            $customer->email, null, null, null, null, null,
                                            dirname(__FILE__).'/../../mails/',
                                            false, $this->context->shop->id
                                        );
                                }
                                else
                                {
                                    $this->sendCommentNotificationEmail(
                                        trim($name_customer),
                                        $email_customer,
                                        $comment->subject,
                                        $comment->comment,
                                        $comment->rating.' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')),
                                        $this->module->getLink('blog', array('id_post' => $comment->id_post)),
                                        'new_comment'.($comment->approved==1?'_1':'_0')
                                    );
                                    if(($subjectMail = Ybc_blog_email_template_class::getSubjectByTemplate('new_comment_customer'.($comment->approved==1?'_1':'_0'))))
                                        Mail::Send(
                                            $this->context->language->id,
                                            'new_comment_customer'.($comment->approved==1?'_1':'_0'),
                                            $subjectMail,
                                            array('{customer_name}' => $name_customer, '{email}' => $email_customer,'{rating}' => ' '.($comment->rating != 1 ? $this->module->l('stars','blog') : $this->module->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment, '{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{post_title}'=>$post['title'],'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                            $email_customer, null, null, null, null, null,
                                            dirname(__FILE__).'/../../mails/',
                                            false, $this->context->shop->id
                                        );
                                }

                                $justAdded = true;
                                $success = $this->module->l('Comment has been submitted ','blog');
                                if($comment->approved)
                                    $success .= $this->module->l('and approved','blog');
                                else
                                    $success .= $this->module->l('and is waiting for approval','blog');
                            }
                            Hook::exec('actionUpdateBlog', array(
                                'id_post' =>(int)$id_post,
                            ));

                        }
                    }
                    if(($id_comment=(int)Tools::getValue('replyCommentsave')))
                    {
                        if(Ybc_blog_post_employee_class::checkPermisionComment('reply',$id_comment,'post'))
                        {
                            $reply_comment_text = Tools::getValue('reply_comment_text');
                            if(Tools::strlen($reply_comment_text) < 20)
                                $errors[] = $this->module->l('Reply needs to be at least 20 characters','blog');
                            if(!Validate::isCleanHtml($reply_comment_text,false))
                                $errors[] = $this->module->l('Reply needs to be clean HTML','blog');
                            if(Tools::strlen($reply_comment_text) >2000)
                                $errors[] = $this->module->l('Reply cannot be longer than 2000 characters','blog');
                        }
                        else
                            $errors[]= $this->module->l('Sorry, you do not have permission','blog');
                        if(!$errors)
                        {
                            $comment = new Ybc_blog_comment_class($id_comment);
                            $post_class= new Ybc_blog_post_class($comment->id_post,$this->context->language->id);
                            if($post_class->is_customer && $post_class->added_by ==$this->context->customer->id)
                            {
                                $approved=1;
                            }
                            else
                                $approved=0;
                            $replyObj = new Ybc_blog_reply_class();
                            $replyObj->id_comment = $id_comment;
                            $replyObj->id_user = $this->context->customer->id;
                            $replyObj->name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                            $replyObj->email = $this->context->customer->email;
                            $replyObj->reply = $reply_comment_text;
                            $replyObj->id_employee = 0;
                            $replyObj->approved = $approved;
                            $replyObj->datetime_added = date('Y-m-d H:i:s');
                            $replyObj->datetime_updated = date('Y-m-d H:i:s');
                            if($replyObj->add())
                            {
                                if($approved)
                                    $success_reply = $this->module->l('Reply has been submitted','blog');
                                else
                                    $success_reply = $this->module->l('Reply has been submitted and is waiting for approval','blog');
                                $comment->viewed=0;
                                $comment->update();
                                if($approved)
                                {
                                    if($this->context->customer->email!=$comment->email)
                                    {
                                        $this->module->sendMailRepyCustomer($id_comment,$this->context->customer->firstname.' '.$this->context->customer->lastname);
                                    }
                                }
                                $this->module->sendMailReplyAdmin($id_comment,$this->context->customer->firstname.' '.$this->context->customer->lastname,$approved);
                                Hook::exec('actionUpdateBlog', array(
                                    'id_post' =>(int)$id_post,
                                ));
                                $this->context->smarty->assign(
                                    array(
                                        'replyCommentsave' => $id_comment,
                                        'reply_comment_text' => $reply_comment_text,
                                        'replyCommentsaveok' => true,
                                    )
                                );
                            }

                        }
                        if($errors)
                        {
                            $this->context->smarty->assign(
                                array(
                                    'replyCommentsave' => $id_comment,
                                    'reply_comment_text' => $reply_comment_text
                                )
                            );
                        }
                    }
                    $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
                    $id_group = null;
                    if ($id_customer) {
                        $id_group = Customer::getDefaultGroupId((int)$id_customer);
                    }
                    if (!$id_group) {
                        $id_group = (int)Group::getCurrent()->id;
                    }
                    $group= new Group($id_group);
                    if($post)
                    {
                        $urlAlias = Tools::strtolower(trim(Tools::getValue('url_alias')));
                        $edit_comment = (int)Tools::getValue('edit_comment');
                        $idComment = (int)Tools::getValue('id_comment');
                        if($urlAlias && !$edit_comment &&  $urlAlias != Tools::strtolower(trim($post['url_alias'])))
                            $this->module->redirect($this->module->getLink('blog',array('id_post' => $post['id_post'])));
                        //check if liked post
                        $likedPost = $this->module->isLikedPost($post['id_post']);
                        if((int)Tools::getValue('all_comment'))
                            $climit=false;
                        else
                            $climit = (int)Configuration::get('YBC_BLOG_MAX_COMMENT') ? (int)Configuration::get('YBC_BLOG_MAX_COMMENT') : false;
                        $cstart = $climit ? 0 : false;
                        $countComment= Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.approved = 1 AND bc.id_post='.(int)$id_post);
                        if($climit && $countComment > (int)$climit )
                            $this->context->smarty->assign('link_view_all_comment', $this->module->getLink('blog',array('id_post' => $post['id_post'],'all_comment'=>1)).'#blog-comments-list');
                        $prettySkin = Configuration::get('YBC_BLOG_GALLERY_SKIN');
                        $randomcode = time();
                        $pollrandomcode= $randomcode;
                        if($edit_comment && ($comment_edit = new Ybc_blog_comment_class($edit_comment)) && Validate::isLoadedObject($comment_edit)  && (!Tools::isSubmit('bcsubmit') || (Tools::isSubmit('bcsubmit') && $idComment == $edit_comment) ) && Ybc_blog_post_employee_class::checkPermisionComment('edit',$edit_comment,'post'))
                        {
                            $this->context->smarty->assign(
                                array(
                                    'comment_edit' => $comment_edit,
                                )
                            );
                        }

                        if(isset($justAdded) && !$justAdded)
                        {
                            $email_customer = Tools::getValue('email_customer');
                            $name_customer = Tools::getValue('name_customer');
                            $subject = Tools::getValue('subject');
                            $comment = Tools::getValue('comment');
                            $this->context->smarty->assign(
                                array(
                                    'comment' => !$justAdded && Validate::isCleanHtml($comment) ? $comment : '',
                                    'subject' => !$justAdded && Validate::isCleanHtml($subject) ? $subject : '',
                                    'name_customer' => !$justAdded && Validate::isCleanHtml($name_customer) ? $name_customer : '',
                                    'email_customer' => !$justAdded && Validate::isCleanHtml($email_customer) ? $email_customer : '',
                                    ''
                                )
                            );
                        }
                        if($success)
                        {
                            $this->context->cookie->success=$success;
                            $this->context->cookie->write();
                            $this->module->redirect($this->module->getLink('blog',array('id_post'=>$id_post)));
                        }
                        if($this->context->cookie->success)
                        {

                            $success = $this->context->cookie->success;
                            $this->context->cookie->success='';
                            $this->context->cookie->write();
                        }
                        if($success_reply)
                        {
                            $this->context->cookie->success_reply=$success_reply;
                            $this->context->cookie->replyCommentsave = (int)$id_comment;
                            $this->context->cookie->write();
                            $this->module->redirect($this->module->getLink('blog',array('id_post'=>$id_post)));
                        }
                        if($this->context->cookie->success_reply)
                        {
                            $success_reply = $this->context->cookie->success_reply;
                            $this->context->cookie->success_reply='';
                            $this->context->smarty->assign(
                                array(
                                    'replyCommentsave' => $this->context->cookie->replyCommentsave,
                                )
                            );
                            $this->context->cookie->replyCommentsave =0;
                            $this->context->cookie->write();
                        }
                        $comments= Ybc_blog_comment_class::getCommentsWithFilter(' AND bc.approved = 1 AND bc.id_post='.(int)$id_post,' bc.id_comment desc, ',$cstart,$climit);
                        if($comments)
                            foreach($comments as &$comment)
                                $comment['reply'] = Ybc_blog_post_employee_class::checkPermisionComment('reply',$comment['id_comment'],'post');
                        if($this->context->customer->logged)
                        {
                            $allow_report_comment = (int)Configuration::get('YBC_BLOG_ALLOW_REPORT') ? true : false;
                        }
                        elseif((int)Configuration::get('YBC_BLOG_ALLOW_REPORT') && (int)Configuration::get('YBC_BLOG_ALLOW_GUEST_REPORT'))
                            $allow_report_comment=true;
                        else
                            $allow_report_comment=false;
                        $polls_name = Tools::getValue('polls_name');
                        $polls_email = Tools::getValue('polls_email');
                        $polls_feedback = Tools::getValue('polls_feedback');
                        $rating = (int)Tools::getValue('rating');
                        $this->context->smarty->assign(
                            array(
                                'blog_post' => $post,
                                'display_desc'=>Configuration::get('YBC_BLOG_POST_PAGE_DISPLAY_DESC'),
                                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT'),
                                'langLocale' => $this->module->is17 ? $this->context->language->locale:$this->context->language->language_code,
                                'allowGuestsComments' => (int)Configuration::get('YBC_BLOG_ALLOW_GUEST_COMMENT') ? true : false,
                                'blogCommentAction' => $this->module->getLink('blog',array('id_post'=>(int)$id_post)),
                                'hasLoggedIn' => $this->context->customer->isLogged(true),
                                'blog_errors' => $errors,
                                'replyCommentsaveok' => $success_reply ? true :false,
                                'comments' => $comments,
                                'reportedComments' => $this->context->cookie->reported_comments ? @unserialize($this->context->cookie->reported_comments) : false,
                                'blog_success' => $success ? $success :$success_reply,
                                'allow_report_comment' =>$allow_report_comment,
                                'allow_reply_comment' => Configuration::get('YBC_BLOG_ALLOW_REPLY_COMMENT') ? $this->context->customer->logged :false,
                                'display_related_products' =>(int)Configuration::get('YBC_BLOG_SHOW_RELATED_PRODUCTS') ? true : false,
                                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                                'allow_comment' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true:false,
                                'default_rating' => (int)$rating > 0 && (int)$rating <=5 ? (int)$rating  :(int)Configuration::get('YBC_BLOG_DEFAULT_RATING'),
                                'everage_rating' => $this->module->getEverageReviews($post['id_post']),
                                'total_review' =>(int)Ybc_blog_post_class::countTotalReviewsWithRating($post['id_post']),
                                'use_capcha' => (int)Configuration::get('YBC_BLOG_USE_CAPCHA') ? true : false,
                                'polls_post_helpful_no' => Ybc_blog_polls_class::countPollsWithFilter(' AND po.polls=0 AND p.id_post='.(int)$id_post),
                                'polls_post_helpful_yes' => Ybc_blog_polls_class::countPollsWithFilter(' AND po.polls=1 AND p.id_post='.(int)$id_post),
                                'use_facebook_share' => (int)Configuration::get('YBC_BLOG_ENABLE_FACEBOOK_SHARE') ? true : false,
                                'use_google_share' => (int)Configuration::get('YBC_BLOG_ENABLE_GOOGLE_SHARE') ? true : false,
                                'use_twitter_share' => (int)Configuration::get('YBC_BLOG_ENABLE_TWITTER_SHARE') ? true : false,
                                'post_url' => $this->module->getLink('blog',array('id_post'=>(int)$id_post)),
                                'report_url' => $this->module->getLink('report'),
                                'likedPost' => $likedPost,
                                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                                'show_tags' => (int)Configuration::get('YBC_BLOG_SHOW_POST_TAGS') ? true : false,
                                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                                'enable_slideshow' => (int)Configuration::get('YBC_BLOG_ENABLE_POST_SLIDESHOW') ? true : false,
                                'prettySkin' => in_array($prettySkin, array('dark_square','dark_rounded','default','facebook','light_rounded','light_square')) ? $prettySkin : 'dark_square',
                                'prettyAutoPlay' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                                'show_author' => (int)Configuration::get('YBC_BLOG_SHOW_POST_AUTHOR') ? 1 : 0,
                                'blog_random_code' => $randomcode,
                                'capcha_image' => $this->module->getLink('capcha',array('randcode'=>$randomcode)),
                                'blog_poll_random_code'=>$pollrandomcode,
                                'allowPolls' => $this->context->customer->logged || Configuration::get('YBC_BLOG_ENABLE_POLLS_GUESTS'),
                                'polls_customer' => $this->context->customer->logged ? $this->context->customer :false,
                                'polls_feedback' => Validate::isCleanHtml($polls_feedback) ? $polls_feedback :'',
                                'polls_email' => Validate::isCleanHtml($polls_email) ? $polls_email :'',
                                'polls_name' => Validate::isCleanHtml($polls_name) ? $polls_name :'',
                                'polls_class' => $this->getPollsCurrent($id_post),
                                'polls_capcha_image' => $this->context->link->getModuleLink('ybc_blog','capcha',array('randcode'=>$pollrandomcode,'type'=>'polls')),
                                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                                'blog_related_product_type' => Tools::strtolower(Configuration::get('YBC_RELATED_PRODUCTS_TYPE')),
                                'blog_template_dir' => dirname(__FILE__).'/../../views/templates/front',
                                'show_price'=>$group->show_prices,
                                'blog_dir' => $this->module->blogDir,
                                'justAdded' => isset($justAdded) ? $justAdded :false,
                                'image_folder' => _PS_YBC_BLOG_IMG_,
                                'id_lang'=> $this->context->language->id,
                                'text_gdpr' => Configuration::get('YBC_BLOG_TEXT_GDPR_NOTIFICATION',$this->context->language->id,null,null,$this->module->l('I agree with the use of cookie and personal data according to EU GDPR','blog')),
                            )
                        );
                    }
                    else
                    {
                        header("HTTP/1.0 404 Not Found");
                        $this->context->smarty->assign(
                            array(
                                'blog_post' => false
                            ));
                    }
                }
                else
                {
                    $this->context->smarty->assign(
                        array(
                            'blog_post' => false
                        ));
                }
            }
            else
            {
                $this->context->smarty->assign(
                    array(
                        'blog_post' => false
                    )
                );
            }
        }
        if($this->useCache)
            return $this->module->display($this->module->getLocalPath(),'single_post.tpl',$this->module->_getCacheId(array('single_post',$id_post)));
        else
            return $this->module->display($this->module->getLocalPath(),'single_post.tpl');
    }
    public function getParams()
    {
        $params = array('post_list'=>'post_list','page'=>(int)Tools::getValue('page',1));
        if($id_category = $this->getIdCategory())
            $params['id_category'] = $id_category;
        elseif(($latest = trim(Tools::getValue('latest'))) && Validate::isCleanHtml($latest))
            $params['latest'] = true;
        elseif(($featured = trim(Tools::getValue('featured'))) && Validate::isCleanHtml($featured))
            $params['featured'] = true;
        elseif(($popular = trim(Tools::getValue('popular'))) && Validate::isCleanHtml($popular)) {
            $params['popular'] = true;
        }
        elseif(($tag = trim(Tools::getValue('tag')))!=''&& ($tag= urldecode(trim($tag))) && Validate::isCleanHtml($tag))
        {
            $params['tag'] = $tag;
        }
        elseif(($search = trim(Tools::getValue('search')))!='' && Validate::isCleanHtml($search))
        {
            $params['search'] = $search;
        }
        elseif($id_author = (int)Tools::getValue('id_author'))
        {
            $is_customer = (int)Tools::getValue('is_customer');
            $params['id_author'] = $id_author;
            $params['is_customer'] = $is_customer;
        }
        elseif(($year = (int)Tools::getValue('year')) && ($month = (int)Tools::getValue('month')))
        {
            $params['year']= $year;
            $params['month']= $month;
        }
        elseif($year)
        {
            $params['year'] = $year;
        }
        return $params;
    }
    public function displayListPost()
    {
        $params = $this->getParams();
        if(Tools::isSubmit('loadajax'))
        {
            $params['loadajax'] = true;
            $this->loadMoreBlog($params);
        }
        if(!$this->module->isCached('list_post.tpl',$this->module->_getCacheId($params)))
        {
            $postData = $this->getPosts($params);
            $is_main_page = !$postData['category'] && !$postData['tag'] && !$postData['search'] && !Tools::isSubmit('latest') && !Tools::isSubmit('id_author') && !Tools::isSubmit('popular') && !Tools::isSubmit('featured') && !$postData['month'] && !$postData['year'] ? true : false;
            $this->context->smarty->assign(
                array(
                    'blog_posts' => $postData['posts'],
                    'blog_paggination' => $postData['paggination'],
                    'blog_category' => $postData['category'],
                    'blog_latest' => $postData['latest'],
                    'blog_featured' => $postData['featured'],
                    'blog_popular' => $postData['popular'],
                    'blog_dir' => $postData['blogDir'],
                    'blog_tag' => $postData['tag'],
                    'month' => $postData['month'],
                    'year' => $postData['year'],
                    'blog_search' => $postData['search'],
                    'is_main_page' => $is_main_page,
                    'html_slide_block' => $is_main_page ? $this->displayBlogSlidersBlock():'',
                    'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                    'allow_comment' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true:false,
                    'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                    'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'author' => $postData['author'],
                    'image_folder' => _PS_YBC_BLOG_IMG_,
                    'is17' => $this->module->is17,
                )
            );
        }
        return $this->module->display($this->module->getLocalPath(),'list_post.tpl',$this->module->_getCacheId($params));
    }
	public function initContent()
	{
		parent::initContent();
        $this->module->setMetas();
        if(($id_post = $this->getIdPost()) || Tools::isSubmit('post_url_alias') )
        {
            $this->context->smarty->assign(
                array(
                    'post_detail_content' => $this->displayDetailPost($id_post),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                    'path' => $this->module->getBreadCrumb(),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ybc_blog/views/templates/front/detail_post.tpl');
            else         
                $this->setTemplate('detail_post_16.tpl');
        }
        else
        {
            $this->context->smarty->assign(
                array(
                    'list_post_content' => $this->displayListPost(),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                    'path' => $this->module->getBreadCrumb(),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ybc_blog/views/templates/front/post_list.tpl');
            else        
                $this->setTemplate('post_list_16.tpl');
        }               
	}
    public function displayBlogSlidersBlock()
    {
        if(!$this->module->isCached('slider_block.tpl',$this->module->_getCacheId()))
        {
            if(!Configuration::get('YBC_BLOG_SHOW_SLIDER'))
                return '';
            $slides = Ybc_blog_slide_class::getSlidesWithFilter(' AND s.enabled=1','s.sort_order asc, s.id_slide asc,');
            if($slides)
                foreach($slides as &$slide)
                {
                    if($slide['image'])
                        $slide['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'slide/'.$slide['image']);
                }
            $this->context->smarty->assign(
                array(
                    'loading_img' => __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'.'views/img/img/loading.gif',
                    'slides' => $slides,
                    'nivoTheme' => 'default',
                    'nivoAutoPlay' => (int)Configuration::get('YBC_BLOG_SLIDER_AUTO_PLAY') ? true : false,
                )
            );
        }
        return $this->module->display($this->module->getLocalPath(), 'slider_block.tpl',$this->module->_getCacheId());
    }
    private function loadMoreBlog($params)
    {
        $postData = $this->getPosts($params);
        $this->context->smarty->assign(
            array(
                'blog_posts' => $postData['posts'],
                'blog_paggination' => $postData['paggination'],
                'blog_category' => $postData['category'],
                'blog_latest' => $postData['latest'],
                'blog_dir' => $postData['blogDir'],
                'blog_tag' => $postData['tag'],
                'blog_search' => $postData['search'],
                'is_main_page' => !$postData['category'] && !$postData['tag'] && !$postData['search'] && !Tools::isSubmit('latest') && !Tools::isSubmit('id_author') ? true : false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'allow_comment' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true:false,
                'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                'author' => $postData['author'],
                'loadajax'=>1,
            )
        );
        $list_blog = $this->module->display($this->module->getLocalPath(),'blog_list.tpl');
        die(
            json_encode(
                array(
                    'list_blog'=> $list_blog,
                    'blog_paggination'=>$postData['paggination'],
                )
            )
        );
    }
    public function getPost($id_post)
    {
        $post = $this->module->getPostById($id_post);
        if($post)
        {
            $post['id_category'] = $this->module->getCategoriesStrByIdPost($post['id_post']);
            $post['tags'] = Ybc_blog_post_class::getTagsByIdPost($post['id_post']);
            $post['related_posts'] = (int)Configuration::get('YBC_BLOG_DISPLAY_RELATED_POSTS') ? Ybc_blog_post_class::getRelatedPosts($id_post, $post['tags'], $this->context->language->id) : false;
            if($post['related_posts'])
            {
                foreach($post['related_posts'] as &$rpost)
                    if($rpost['image'])
                    {
                        $rpost['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);
                        if($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                        else
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);            
                        $rpost['link'] = $this->module->getLink('blog',array('id_post'=>$rpost['id_post']));
                        $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                        $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                        $rpost['liked'] = $this->module->isLikedPost($rpost['id_post']);                        
                    }
                    else
                    {
                        $rpost['image'] = '';
                        if($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                        else
                            $rpost['thumb'] = '';
                        $rpost['link'] =   $this->module->getLink('blog',array('id_post'=>$rpost['id_post']));
                        $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                        $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                        $rpost['liked'] = $this->module->isLikedPost($rpost['id_post']);  
                    }                        
            }               
            $post['img_name'] = isset($post['image']) ? $post['image'] : '';
            if($post['image'])
                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);                            
            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
            $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            $post['products'] = Ybc_blog_post_class::getRelatedProductByProductsStr($post['id_post'],$post['products'],$post['exclude_products']);
            $params = array(); 
            $params['id_author'] = (int)$post['added_by'];
            $params['is_customer'] = (int)$post['is_customer'];
            $employee = Ybc_blog_post_employee_class::getAuthorById($params['id_author'],$params['is_customer']);
            if($employee)
            {
                if(!isset($employee['name'])|| !$employee['name'])
                    $employee['name']=$employee['firstname'].' '.$employee['lastname'];
            }
            $params['alias'] = str_replace(' ','-',trim($employee['name'])); 
            $post['author_link'] = $this->module->getLink('blog', $params);
            $post['employee'] =$employee;
            if($post['is_customer'] && Ybc_blog_post_employee_class::checkPermistionPost($post['id_post'],'edit_blog') )
            {
                $post['link_edit'] = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'id_post'=>$post['id_post']));
            }
            return $post;
        }
        return false;
    }
    public function getPosts($params)
    {
        $context = Context::getContext();
        $module = new Ybc_blog();
        $filter = ' AND p.enabled =1';
        $featurePage = false;
        $id_category = (int)trim(Tools::getValue('id_category'));
        $params['page']= '_page_';
        if(isset($params['id_category']) && ($id_category = (int)$params['id_category']))
        {
            if(($category = new Ybc_blog_category_class($id_category,$this->context->language->id)) && Validate::isLoadedObject($category) )
            {
                $urlAlias = Tools::strtolower(trim(Tools::getValue('url_alias')));
                if($urlAlias && $urlAlias != Tools::strtolower(trim($category->url_alias)))
                    $this->module->redirect($module->getLink('blog',array('id_category' => $id_category)));
            }
            $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_category = ".(int)$id_category.") ";
        }
        elseif(isset($params['latest']) && $params['latest'])
        {
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'latest') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($module->getLink('blog',array('latest'=>true)));
        } 
        elseif(isset($params['featured']) && $params['featured'])
        {
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'featured') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($module->getLink('blog',array('featured'=>true)));
            $filter .=' AND p.is_featured=1';
        }
        elseif(isset($params['popular']) && $params['popular'])
        {
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'popular') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($module->getLink('blog',array('popular'=>true)));
        }                
        elseif(isset($params['tag']) && ($tag = $params['tag']))
        {    
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'tag') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                 $this->module->redirect($this->module->getLink('blog',array('tag'=>$tag)));
            $md5tag = md5(urldecode(trim(Tools::strtolower($tag))));            
            $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_tag` WHERE tag = '".pSQL($tag)."' AND id_lang = ".(int)$this->context->language->id.")";
            if(!$context->cookie->tags_viewed)
               $tagsViewed = array();
            else
               $tagsViewed = @unserialize($context->cookie->tags_viewed);
                     
            if(is_array($tagsViewed) && !in_array($md5tag, $tagsViewed))
            {   
                if(Ybc_blog_post_class::increasTagViews($tag))
                {
                    $tagsViewed[] = $md5tag;
                    $context->cookie->tags_viewed = @serialize($tagsViewed);
                    $context->cookie->write();    
                }                              
            }
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'tag') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($module->getLink('blog',array('tag'=>$tag)));
        }  
        elseif(isset($params['search']) && ($search =$params['search']))
        {
            $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_post_lang` WHERE (title like '%".pSQL(str_replace('+',' ',$search))."%' OR description like '%".pSQL(str_replace('+',' ',$search))."%') AND id_lang = ".(int)$this->context->language->id.")";
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'search') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($module->getLink('blog',array('search'=>$search)));
        }
        elseif(isset($params['id_author']) && ($id_author = (int)$params['id_author']))
        {
            $is_customer = isset($params['is_customer']) ? (int)$params['is_customer']:0;
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'author_name') !==false)
            {
                $this->module->redirect($this->module->getLink('blog',array('id_author'=>$id_author,'is_customer'=> $is_customer)));
            }
            $filter .= " AND p.added_by = ".(int)$id_author.' AND p.is_customer="'.(int)$is_customer.'"';
            $params['id_author'] = $id_author;
            $params['is_customer'] = $is_customer;
           
            $employee = Ybc_blog_post_employee_class::getAuthorById($id_author,$is_customer);
            if($employee)
                $params['alias'] = $employee['alias'];
            else
            {
                header("HTTP/1.0 404 Not Found");
                $employee['disabled']=true;
            }
        }  
        elseif((isset($params['year']) && ($year = $params['year']))  && (isset($params['month']) && ($month = $params['month'])))
        {
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'month') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                $this->module->redirect($this->module->getLink('blog',array('year'=>$year,'month'=>$month)));
            $filter .=' AND MONTH(p.datetime_added) ="'.pSQL($month).'" AND YEAR(p.datetime_added)="'.pSQL($year).'"';
        }
        elseif(isset($year) && $year)
        {
            if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'year') !==false && Tools::strpos($_SERVER['REQUEST_URI'],'ybc_blog')!==false)
                 $this->module->redirect($this->module->getLink('blog',array('year'=>$year)));
            $filter .=' AND YEAR(p.datetime_added)="'.pSQL($year).'"';
        }              
        else
        {
            if(Configuration::get('YBC_BLOG_MAIN_PAGE_POST_TYPE') == 'featured')
            {
                $filter .= ' AND p.is_featured = 1';
                $featurePage = true;    
            }                        
        }
        if(isset($id_category) && $id_category)            
        {
            if(!Configuration::get('YBC_BLOG_POST_SORT_BY'))
                $sort = 'p.datetime_added DESC, ';
            else
            {
                if(Configuration::get('YBC_BLOG_POST_SORT_BY')=='sort_order')
                    $sort = 'pc.position ASC, ';
                elseif(Configuration::get('YBC_BLOG_POST_SORT_BY')=='id_post')
                    $sort = 'p.datetime_added DESC, ';
                else
                    $sort = 'p.'.Configuration::get('YBC_BLOG_POST_SORT_BY').' DESC, ';
            }
        }
        elseif(isset($params['popular']) && $params['popular'])
            $sort = 'p.click_number desc,';
        elseif(isset($params['latest']) && $params['latest'])
            $sort = 'p.id_post DESC, ';
        else
        {
            $sort = $this->module->sort;
        }
        if(($ybc_sort_by_posts =Tools::getValue('ybc_sort_by_posts')) && in_array($ybc_sort_by_posts,array('id_post','sort_order','click_number')))
        {
            if($ybc_sort_by_posts=='sort_order')
            {
                if(isset($id_category) && $id_category)
                    $sort = 'pc.position ASC, ';
                else
                    $sort = 'p.sort_order ASC, ';
            }
            elseif($ybc_sort_by_posts=='id_post')
                $sort = 'p.datetime_added DESC, ';
            else
                $sort = 'p.'.$ybc_sort_by_posts.' DESC, ';
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page <1)
            $page=1;
        $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $module->getLink('blog', $params);
        if(!Tools::isSubmit('id_category') && !Tools::isSubmit('search') && !Tools::isSubmit('tag') && !Tools::isSubmit('latest') && !Tools::isSubmit('id_author'))
            $paggination->limit =  (int)Configuration::get('YBC_BLOG_ITEMS_PER_PAGE') > 0 ? (int)Configuration::get('YBC_BLOG_ITEMS_PER_PAGE') : 20;
        else
            $paggination->limit =  (int)Configuration::get('YBC_BLOG_ITEMS_PER_PAGE_INNER') > 0 ? (int)Configuration::get('YBC_BLOG_ITEMS_PER_PAGE_INNER') : 20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        if(!$featurePage)
            $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, $start, $paggination->limit);
        else
            $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, 0, false);
           
        if($posts)
        {
            foreach($posts as &$post)
            {
                $post['id_category'] = $module->getCategoriesStrByIdPost($post['id_post']);
                $post['tags'] = Ybc_blog_post_class::getTagsByIdPost($post['id_post']);
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                if($post['image'])
                    $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                $post['link'] = $module->getLink('blog',array('id_post'=>$post['id_post']));
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                $post['everage_rating'] = $module->getEverageReviews($post['id_post']);
                $post['total_review'] = Ybc_blog_post_class::countTotalReviewsWithRating($post['id_post']);
                $post['liked'] = $this->module->isLikedPost($post['id_post']);
            }                
        }
        elseif(isset($params['id_author']) && $params['id_author'] && !(isset($employee) && $employee))
        {
            Tools::redirect($this->module->getLink('author'));
        }
        $category = (int)$id_category ? (($cat = Ybc_blog_category_class::getCategoryById((int)$id_category)) ? $cat : array('enabled' => false)) : false;
        if($category && !$category['enabled'])
            header("HTTP/1.0 404 Not Found");
        return array(
            'posts' => $posts, 
            'paggination' => $featurePage ? '' : $paggination->render(), 
            'category' => $category,
            'blogDir' => $module->blogDir,
            'tag' => isset($params['tag']) && $params['tag'] !='' ? $params['tag'] : false,
            'search' => isset($params['search']) && $params['search'] ? urldecode($params['search']) : false,
            'latest' => isset($params['latest']) && $params['latest'] ? true  : false,
            'popular' => isset($params['popular']) && $params['popular'] ? true : false,
            'featured' => isset($params['featured']) && $params['featured'] ? true : false,
            'author' => isset($employee) && $employee ? $employee:false,
            'month' => isset($month) && $month && isset($year) && $year ? $year.' - '.$this->module->getMonthName($month) :false,
            'year' => isset($year) && $year ? $year :false,
        );
    }
    public function sendCommentNotificationEmail($customer, $bemail, $subject, $comment, $rating, $postLink,$team_mail='new_comment',$comment_old='')
    {
        $id_post= (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$this->context->language->id);
        }
        $mailDir = dirname(__FILE__).'/../../mails/';
        $post = new Ybc_blog_post_class($id_post);
        if($post->is_customer)
        {
            $author= new Customer($post->added_by);
            $emails=array($author->email);
            $link_view_comment= $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','list'=>1));
        }
        else
        {
            $author= new Employee($post->added_by);
            $emails=array($author->email);
            $link_view_comment= $this->module->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
        }
        $lang = new Language((int)$author->id_lang);
        $mail_lang_id = (int)$lang->id;
        if(!is_dir($mailDir.$lang->iso_code))
           $mail_lang_id = (int)Configuration::get('PS_LANG_DEFAULT'); 
        $post = new Ybc_blog_post_class($id_post,$mail_lang_id);
        if($emails && ($subjectMail= Ybc_blog_email_template_class::getSubjectByTemplate($team_mail,$mail_lang_id)))
        {
            foreach($emails as $email)
            {    
                if(Validate::isEmail(trim($email)))
                {
                    if($team_mail=='edit_comment')
                    {
                        $mail_val =array(
                            '{customer_name}' => $customer,
                            '{email}' => $bemail,
                            '{rating}' => $rating,
                            '{subject}' => $subject,
                            '{comment}'=>$comment,
                            '{comment_link}' => '#',
                            '{post_title}'=>$post->title,
                            '{author_name}' => $author->firstname.' '.$author->lastname,
                            '{link_view_comment}'=>$link_view_comment,
                            '{post_link}'=>$postLink,
                            '{comment_old}'=>$comment_old,
                            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                        );
                        Mail::Send(
                            $mail_lang_id, 
                            $team_mail,
                            $subjectMail, 
                            $mail_val,  
                            trim($email), null, null, null, null, null, 
                            $mailDir, 
                            false, $this->context->shop->id
                        );  
                    }
                    else
                    {
                        $mail_val=array(
                            '{customer_name}' => $customer,
                            '{email}' => $bemail,
                            '{rating}' => $rating, 
                            '{subject}' => $subject, 
                            '{comment}'=>$comment, 
                            '{comment_link}' => '#',
                            '{author_name}' => $author->firstname.' '.$author->lastname,
                            '{link_view_comment}'=>$link_view_comment,
                            '{post_link}'=>$postLink,
                            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER'),
                            '{post_title}'=>$post->title
                        );
                        Mail::Send(
                            $mail_lang_id, 
                            $team_mail, 
                            $subjectMail, 
                            $mail_val,  
                            trim($email), null, null, null, null, null, 
                            $mailDir, 
                            false, $this->context->shop->id
                        );
                    }   
                }                
            }
        }
        if(Configuration::get('YBC_BLOG_ALERT_EMAILS'))
        {
            $emails = explode(',',Configuration::get('YBC_BLOG_ALERT_EMAILS'));
            $link_view_comment= $this->module->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
            if($emails)
            {
                foreach($emails as $email)
                {    
                    if(Validate::isEmail(trim($email)))
                    {
                        $employeeObj = new Employee();
                        if(($employee = $employeeObj->getByEmail($email)) && ($lang = new Language($employee->id_lang)) && Validate::isLoadedObject($lang))
                            $mail_lang_id = $lang->id;
                        else
                            $mail_lang_id = $this->context->language->id;
                        if(($subjectMail = Ybc_blog_email_template_class::getSubjectByTemplate($team_mail,$mail_lang_id)))
                        {
                            $mail_val=array(
                                '{customer_name}' => $customer,
                                '{email}' => $bemail,
                                '{rating}' => $rating, 
                                '{subject}' => $subject, 
                                '{comment}'=>$comment, 
                                '{comment_link}' => '#',
                                '{author_name}' => Configuration::get('PS_SHOP_NAME'),
                                '{link_view_comment}'=>$link_view_comment,
                                '{post_link}'=>$postLink,
                                '{post_title}'=>$post->title,
                                '{comment_old}'=>$comment_old,
                                '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                                '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                            );
                            if($team_mail=='edit_comment')
                            {
                                Mail::Send(
                                    $mail_lang_id, 
                                    $team_mail, 
                                    $subjectMail, 
                                    $mail_val,  
                                    trim($email), null, null, null, null, null, 
                                    $mailDir, 
                                    false, $this->context->shop->id
                                );
                            }
                            else
                            {
                                Mail::Send(
                                    $mail_lang_id, 
                                    $team_mail,
                                    $subjectMail, 
                                    $mail_val,  
                                    trim($email), null, null, null, null, null, 
                                    $mailDir, 
                                    false, $this->context->shop->id
                                );
                            }
                        }
                    }
                }
            }
        }  
    }
    public function getPollsCurrent($id_post)
    {
        if($this->context->customer->logged)
        {
            $id_polls = Ybc_blog_polls_class::getIDPolls($id_post,$this->context->customer->id);
        }
        else
        {
            if($this->context->cookie->id_post_polls)
            {
                $id_post_polls = json_decode($this->context->cookie->id_post_polls,true);
                $id_polls = isset($id_post_polls[$id_post]) ? $id_post_polls[$id_post] :0;
            }
            else
                $id_polls=0;
        }
        if($id_polls)
        {
            return new Ybc_blog_polls_class($id_polls);
        }
        else
            return false;
    }
    public function sendMailAdminVoteNew($ybc_blog_polls,$post_class)
    {
        if($post_class->is_customer)
        {
            $author = new Customer($post_class->added_by);
        }
        else
        {
            $author = new Employee($post_class->added_by);
        }
        
        $mail_template= array(
        '{feedback}'=>$ybc_blog_polls->feedback, 
        '{post_link}' => $this->module->getLink('blog', array('id_post' => $post_class->id)),
        '{polls_helpful}' => $ybc_blog_polls->polls? $this->module->l('Yes','blog'): $this->module->l('No','blog'),
        '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
        '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER'),
        '{post_title}'=>$post_class->title);
        if($email = $author->email)
        {
            if(($lang  = new Language($author->id_lang)) && $lang->active )
                $idLang = $lang->id;
            else
                $idLang = $this->context->language->id;
            if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('new_vote_admin',$idLang)))
            {
                Mail::Send(
                    $this->context->language->id, 
                    'new_vote_admin',
                    $subject,
                    $mail_template,  
                    $email, null, null, null, null, null, 
                    dirname(__FILE__).'/../../mails/', 
                    false, $this->context->shop->id
                );
            }
        }
        if(($emails = Configuration::get('YBC_BLOG_ALERT_EMAILS')) &&  $emails = explode(',',$emails))
        {
            foreach($emails as $email)
            {
                $employeeobj  = new Employee();
                if(Validate::isEmail($email))
                {
                    if(($employee = $employeeobj->getByEmail($email)) && ($lang = new Language($employee->id)) && $lang->active)
                    {
                        $idLang = $lang->id;
                    }
                    else
                        $idLang = $this->context->language->id;
                    if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('new_vote_admin',$idLang)))
                    {
                        Mail::Send(
                            $idLang, 
                            'new_vote_admin',
                            $subject,
                            $mail_template,  
                            $email, null, null, null, null, null, 
                            dirname(__FILE__).'/../../mails/', 
                            false, $this->context->shop->id
                        );
                    }
                    
                }
            }
        }
        if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('new_vote_customer')))
        {
            $mail_template= array(
            '{feedback}'=>$ybc_blog_polls->feedback, 
            '{customer_name}' => $ybc_blog_polls->name,
            '{post_link}' => $this->module->getLink('blog', array('id_post' => $post_class->id)),
            '{polls_helpful}' => $ybc_blog_polls->polls? $this->module->l('Yes','blog'): $this->module->l('No','blog'),
            '{post_title}'=>$post_class->title,
            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
            );
            Mail::Send(
                $this->context->language->id, 
                'new_vote_customer',
                $subject,
                $mail_template,  
                $ybc_blog_polls->email, null, null, null, null, null, 
                dirname(__FILE__).'/../../mails/', 
                false, $this->context->shop->id
            );
        }
        
    }
}