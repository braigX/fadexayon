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
 * Class AdminYbcBlogCommentController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogCommentController extends ModuleAdminController
{
    public $baseLink;
    public $_html='';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogComment');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog comments'))
        {
            $this->checked = true;
            if(Tools::isSubmit('comment_reply'))
            {
                $this->_posstReply();
            }
            else
                $this->_postComment();
        }

    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('comment'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getContent()
    {
        if(Tools::isSubmit('editybc_comment') && ($id_comment = (int)Tools::getValue('id_comment')) && Validate::isLoadedObject( new Ybc_blog_comment_class($id_comment)))
            return $this->renderCommentForm($id_comment);
        elseif(Tools::isSubmit('comment_reply'))
        {
            $id_comment = (int)Tools::getValue('id_comment');
            if(!Validate::isLoadedObject(new Ybc_blog_comment_class($id_comment)))
                Tools::redirectAdmin($this->baseLink);
            return $this->displayReplyComment($id_comment);
        }
        else
            return $this->renderListComments();
    }
    private function renderListComments()
    {
        $fields_list = array(
            'id_comment' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'subject' => array(
                'title' => $this->l('Subject'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'rating' => array(
                'title' => $this->l('Rating'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'rating_field' => true,
                'filter_list' => array(
                    'id_option' => 'rating',
                    'value' => 'stars',
                    'list' => array(
                        0 => array(
                            'rating' => 0,
                            'stars' => $this->l('No reviews')
                        ),
                        1 => array(
                            'rating' => 1,
                            'stars' => '1 '.$this->l('star')
                        ),
                        2 => array(
                            'rating' => 2,
                            'stars' => '2 '.$this->l('stars')
                        ),
                        3 => array(
                            'rating' => 3,
                            'stars' => '3 '.$this->l('stars')
                        ),
                        4 => array(
                            'rating' => 4,
                            'stars' => '4 '.$this->l('stars')
                        ),
                        5 => array(
                            'rating' => 5,
                            'stars' => '5 '.$this->l('stars')
                        ),
                    )
                )
            ),
            'name' => array(
                'title' => $this->l('Customer'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'title'=>array(
                'title'=>$this->l('Blog post'),
                'type' => 'text',
                'filter' => true,
                'strip_tag'=>false,
            ),
            'count_reply'=>array(
                'title'=>$this->l('Replies'),
                'type' => 'text',
            ),
            'approved' => array(
                'title' => $this->l('Status'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->l('Approved')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->l('Pending')
                        )
                    )
                )
            ),
            'reported' => array(
                'title' => $this->l('Not reported as abused'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'form_group_class' => 'text-center',
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            )
        );
        //Filter
        $filter = "";
        $show_reset = false;
        if(($id = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id))
        {
            $filter .= " AND bc.id_comment = ".(int)$id;
            $show_reset = true;
        }
        if(($com = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($com))
        {
            $filter .= " AND bc.comment like '%".pSQL($com)."%'";
            $show_reset = true;
        }
        if(($subject = trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
        {
            $filter .= " AND (bc.subject LIKE '%".pSQL($subject)."%' OR bc.comment LIKE '%".pSQL($subject)."%')";
            $show_reset = true;
        }
        if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
        {
            $filter .= " AND bc.rating = ".(int)$rating;
            $show_reset = true;
        }
        if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
        {
            $filter .= " AND bc.name like '%".pSQL($name)."%'";
            $show_reset = true;
        }
        if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
        {
            $filter .= " AND bc.approved = ".(int)$approved;
            $show_reset = true;
        }
        if(($reported = trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
        {
            $filter .= " AND bc.reported = ".(int)$reported;
            $show_reset = true;
        }
        if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
        {
            $filter .= " AND pl.title like '%".pSQL($title)."%'";
            $show_reset = true;
        }
        //Sort
        $sort_post = Tools::strtolower(Tools::getValue('sort'));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type ='desc';
        if($sort_post && isset($fields_list[$sort_post]))
        {
            $sort = $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = 'bc.id_comment desc,';
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page <1)
            $page=1;
        $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter,false);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_comment_select_limit',20);
        $paggination->name ='ybc_comment';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $comments = Ybc_blog_comment_class::getCommentsWithFilter($filter, $sort, $start, $paggination->limit,false);
        if($comments)
        {
            foreach($comments as &$comment)
            {
                $comment['view_url'] = $this->module->getLink('blog', array('id_post' => $comment['id_post'])).'#blog_comment_line_'.$comment['id_comment'];
                $comment['view_text'] = $this->l('View in post');
                $comment['child_view_url'] = $this->baseLink.'&comment_reply=1&id_comment='.(int)$comment['id_comment'];
                $replies = Ybc_blog_reply_class::getTotalRepliesByIDComment($comment['id_comment']);
                $replies_no_approved = Ybc_blog_reply_class::getTotalRepliesByIDComment($comment['id_comment'],0);
                if($replies)
                    $comment['count_reply'] = $replies. ($replies_no_approved ? ' ('.$replies_no_approved.' '.$this->l('pending').')':'');
                else
                    $comment['count_reply']=0;
                $comment['title'] = $this->module->displayText($comment['title'],'a',null,null,$this->module->getLink('blog',array('id_post'=>$comment['id_post'])),true);
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_comment',
            'actions' => array('edit','approve' ,'delete'),
            'currentIndex' => $this->baseLink.($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
            'identifier' => 'id_comment',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Comments'),
            'fields_list' => $fields_list,
            'field_values' => $comments,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'show_add_new' => false,
            'sort'=> $sort_post ?: 'id_comment',
            'sort_type'=> $sort_type,
        );
        return $this->module->renderList($listData);
    }
    public function renderCommentForm($id_comment)
    {
        //Form
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manage Comments'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Subject'),
                        'name' => 'subject',
                        'required' => true,
                        'desc' => $id_comment && ($comment = Ybc_blog_comment_class::getCommentById($id_comment)) ? $this->module->displayCommentInfo($comment,(int)$comment['id_user'],$this->module->getLink('blog',array('id_post' => (int)$comment['id_post']))) : '',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Rating'),
                        'name' => 'rating',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => '0',
                                    'name' => $this->l('No ratings')
                                ),
                                array(
                                    'id_option' => '1',
                                    'name' => '1 '. $this->l('rating')
                                ),
                                array(
                                    'id_option' => '2',
                                    'name' => '2 '. $this->l('ratings')
                                ),
                                array(
                                    'id_option' => '3',
                                    'name' => '3 '. $this->l('ratings')
                                ),
                                array(
                                    'id_option' => '4',
                                    'name' => '4 '. $this->l('ratings')
                                ),
                                array(
                                    'id_option' => '5',
                                    'name' => '5 '. $this->l('ratings')
                                )
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Comment'),
                        'name' => 'comment',
                        'required' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Approved'),
                        'name' => 'approved',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Not reported as abused'),
                        'name' => 'reported',
                        'is_bool' => true,
                        'form_group_class' => 'text-center',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier ='id_comment';
        $helper->submit_action = 'saveComment';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogComment', false);
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogComment');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getCommentField(),'id_comment','Ybc_blog_comment_class','saveComment'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'cancel_url' => $this->baseLink,
        );
        if(Tools::isSubmit('id_comment') && $id_comment)
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_comment');
        }
        return $helper->generateForm(array($fields_form));
    }
    private function submitBulkActionMessage()
    {
        if(($comments =  Tools::getValue('message_readed')) && Ybc_blog::validateArray($comments) && $action = Tools::getValue('bulk_action_message'))
        {
            if($action=='delete_selected' && $comments)
            {
                foreach($comments as $id_comment => $value)
                {
                    if($value)
                    {
                        Hook::exec('actionUpdateBlog', array(
                            'id_comment' => (int)$id_comment,
                        ));
                        if(($comment = new Ybc_blog_comment_class($id_comment)) &&  Validate::isLoadedObject($comment))
                            $comment->delete();
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->baseLink.'&conf=2',
                    )
                ));
            }
            else
            {
                if($action=='mark_as_approved')
                {
                    $value_field=1;
                    $field='approved';
                }
                elseif($action=='mark_as_unapproved')
                {
                    $value_field=0;
                    $field='approved';
                }
                elseif($action=='mark_as_read')
                {
                    $value_field=1;
                    $field='viewed';
                }
                else
                {
                    $value_field=0;
                    $field='viewed';
                }
                foreach($comments as $id_comment => $value)
                {
                    if($value)
                    {
                        $commentObj = new Ybc_blog_comment_class(($id_comment));
                        if(Validate::isLoadedObject($commentObj))
                        {
                            Hook::exec('actionUpdateBlog', array(
                                'id_comment' => (int)$id_comment,
                            ));

                            $commentObj->{$field} = $value_field;
                            $commentObj->update();
                        }
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->baseLink.'&conf=4',
                    )
                ));
            }
        }

    }
    private function actionChangeStatus()
    {
        $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
        $field = Tools::getValue('field');
        $id_comment = (int)Tools::getValue('id_comment');
        $comment = new Ybc_blog_comment_class($id_comment);
        $post= new Ybc_blog_post_class($comment->id_post);
        Hook::exec('actionUpdateBlog', array(
            'id_post' => (int)$comment->id_post,
        ));
        if($field == 'approved' || $field == 'reported' && $id_comment)
        {
            Ybc_blog_defines::changeStatus('comment',$field,$id_comment,$status);
            if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email,true)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
                $idLang = $customer->id_lang;
            else
                $idLang = $this->context->language->id;
            if($field=='approved' && $status==1 && ($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_comment',$idLang)))
            {
                Mail::Send(
                    $idLang,
                    'approved_comment',
                    $subject,
                    array('{customer_name}' => $comment->name, '{email}' => $comment->email,'{rating}' => ' '.($comment->rating != 1 ? $this->l('stars','blog') : $this->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post->title[$this->context->language->id],'{post_link}' => $this->module->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                    $comment->email, null, null, null, null, null,
                    _PS_MODULE_DIR_.'ybc_blog'.'/mails/',
                    false, $this->context->shop->id
                );
            }
            if($field=='approved')
            {
                if($status==1)
                    $title = $this->l('Click to mark as unapproved');
                else
                    $title = $this->l('Click to mark as approved');
            }
            else
            {
                if($status==1)
                    $title = $this->l('Click to mark as unreported');
                else
                    $title = $this->l('Click to mark as reported');
            }
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(array(
                    'listId' => $id_comment,
                    'enabled' => $status,
                    'field' => $field,
                    'message' => $field == 'approved' ? $this->module->displaySuccessMessage($this->l('The status has been successfully updated')):$this->module->displaySuccessMessage($this->l('The status has been successfully updated')),
                    'messageType'=>'success',
                    'title'=>$title,
                    'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_comment='.$id_comment,
                )));
            }
            Tools::redirectAdmin($this->baseLink.'&conf=4');
        }
    }
    private function _postComment()
    {
        $errors = array();
        $id_comment = (int)Tools::getValue('id_comment');
        if(Tools::isSubmit('editybc_comment') && ($id_comment && !Validate::isLoadedObject(new Ybc_blog_comment_class($id_comment)) || !$id_comment))
            Tools::redirectAdmin($this->baseLink);
        if(Tools::isSubmit('submitBulkActionMessage'))
        {
            $this->submitBulkActionMessage();
        }
        if(Tools::isSubmit('change_enabled'))
        {
            $this->actionChangeStatus();
        }
        if(Tools::isSubmit('del'))
        {
            $id_comment = (int)Tools::getValue('id_comment');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if(!(($comment = new Ybc_blog_comment_class($id_comment)) &&  Validate::isLoadedObject($comment)))
                $errors[] = $this->l('Comment does not exist');
            elseif($comment->delete())
            {
                Tools::redirectAdmin($this->baseLink.'&conf=2');
            }
            else
                $errors[] = $this->l('Could not delete the comment. Please try again');
        }
        if(Tools::isSubmit('approve'))
        {
            $id_comment = (int)Tools::getValue('id_comment');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if(!(($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment)))
                $errors[] = $this->l('Comment does not exist');
            else
            {
                $comment->approved =1;
                if($comment->update())
                    Tools::redirectAdmin($this->baseLink.'&conf=4');
                else
                    $errors[] = $this->l('Could not approve the comment. Please try again');
            }
        }
        /**
         * Save comment
         */
        if(Tools::isSubmit('saveComment'))
        {
            if($id_comment && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
            {
                $post= new Ybc_blog_post_class($comment->id_post);
                Hook::exec('actionUpdateBlog', array(
                    'id_post' => (int)$comment->id_post,
                ));
                $rating = (int)Tools::getValue('rating');
                $approved = $comment->approved;
                $comment->subject = trim(Tools::getValue('subject',''));
                $comment->comment = trim(Tools::getValue('comment',''));
                $comment->reply = trim(Tools::getValue('reply',''));
                $comment->rating = $rating >=0 && $rating <=5 ? $rating : 0;
                $comment->approved = (int)trim(Tools::getValue('approved',1)) ? 1 : 0;
                $comment->reported = (int)trim(Tools::getValue('reported',0)) ? 1 : 0;
                $comment->replied_by = (int)$this->context->employee->id;
                if(Tools::strlen($comment->subject) < 10)
                    $errors[] = $this->l('Subject needs to be at least 10 characters');
                if(Tools::strlen($comment->subject) >300)
                    $errors[] = $this->l('Subject cannot be longer than 300 characters');
                if(!Validate::isCleanHtml($comment->subject,false))
                    $errors[] = $this->l('Subject needs to be clean HTML');
                if(Tools::strlen($comment->comment) < 20)
                    $errors[] = $this->l('Comment needs to be at least 20 characters');
                if(!Validate::isCleanHtml($comment->comment,false))
                    $errors[] = $this->l('Comment needs to be clean HTML');
                if(Tools::strlen($comment->comment) >2000)
                    $errors[] = $this->l('Comment cannot be longer than 2000 characters');

                if(!Validate::isCleanHtml($comment->reply,false))
                    $errors[] = $this->l('Reply needs to be clean HTML');
                if(Tools::strlen($comment->reply) >2000)
                    $errors[] = $this->l('Reply cannot be longer than 2000 characters');
                if(!$errors)
                {
                    if(!$comment->update())
                    {
                        $errors[] = $this->l('The comment could not be updated.');
                    }
                    else
                    {
                        if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
                            $id_lang = $customer->id_lang;
                        else
                            $id_lang = $this->context->language->id;
                        if($approved!=$comment->$approved && $comment->approved==1 && ($subject = Ybc_blog_email_template_class::getInstance()->getSubjects('approved_comment',$id_lang)))
                        {
                            Mail::Send(
                                $id_lang,
                                'approved_comment',
                                $subject,
                                array('{customer_name}' => $comment->name, '{email}' => $comment->email,'{rating}' => ' '.($comment->rating != 1 ? $this->l('stars','blog') : $this->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post->title[$this->context->language->id],'{post_link}' => $this->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                $comment->email, null, null, null, null, null,
                                _PS_MODULE_DIR_.'ybc_blog'.'/mails/',
                                false, $this->context->shop->id
                            );
                        }
                    }
                }
            }
            else
            {
                $errors[] = $this->l('Comment does not exist');
            }
        }
        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->module->displayError($errors) : $this->module->displayConfirmation($this->l('Comment saved')),
                )
            ));
        }
        if (count($errors))
        {
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        elseif (Tools::isSubmit('saveComment') && $id_comment)
            Tools::redirectAdmin($this->baseLink.'&conf=4');
        elseif (Tools::isSubmit('saveComment'))
        {
            Tools::redirectAdmin($this->baseLink);
        }
    }
    public function displayReplyComment($id_comment)
    {
        if($id_comment)
        {
            $comment= new Ybc_blog_comment_class($id_comment);
            if(!Validate::isLoadedObject($comment))
            {
                return $this->displayWarning($this->l('Comment not exists'));
            }
            else
            {
                $comment->viewed=1;
                $comment->update();
                $comment->comment = str_replace("\n",'<'.'b'.'r/'.'>',$comment->comment);
                $replies= Ybc_blog_comment_class::getRepliesByIdComment($id_comment);
                if($replies)
                {
                    foreach($replies as &$reply)
                    {
                        $reply['reply'] = str_replace("\n",'<'.'b'.'r/'.'>',$reply['reply']);
                        if($reply['id_employee'])
                        {
                            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_employee'],false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name  )
                                $reply['name']= $employeePost->name;
                            elseif(($employee = new Employee($reply['id_employee'])) && Validate::isLoadedObject($employee))
                                $reply['name']= $employee->firstname.' '.$employee->lastname;
                        }
                        if($reply['id_user'])
                        {
                            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById((int)$reply['id_user'])) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name)
                                $reply['name']= $employeePost->name;
                            elseif(($customer = new Customer($reply['id_user'])) && Validate::isLoadedObject($customer)  && Validate::isLoadedObject($customer))
                                $reply['name']= $customer->firstname.' '.$customer->lastname;
                        }
                    }
                }
                $this->context->smarty->assign(
                    array(
                        'comment'=>$comment,
                        'replies'=>$replies,
                        'post_class' => new Ybc_blog_post_class($comment->id_post,$this->context->language->id),
                        'curenturl' => $this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment,
                        'link_back'=> $this->baseLink,
                        'post_link' => $this->module->getLink('blog',array('id_post'=>$comment->id_post)),
                        'link_delete' => $this->baseLink.'&id_comment='.(int)$id_comment.'&del=1',
                    )
                );
            }

        }
        return $this->module->display($this->module->getLocalPath(),'reply_comment.tpl');
    }
    public function _posstReply()
    {
        $id_comment = (int)Tools::getValue('id_comment');
        $errors=array();
        if(Tools::isSubmit('submitBulkActionReply') && ($reply_readed = Tools::getValue('reply_readed')) && Ybc_blog::validateArray($reply_readed) && ($bulk_action_reply =Tools::getValue('bulk_action_reply')) )
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if($bulk_action_reply=='delete_selected')
            {
                foreach($reply_readed as $id_reply => $value)
                {
                    if($value)
                    {
                        Ybc_blog_comment_class::deleteReply($id_reply);
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment.'&conf=2',
                    )
                ));
            }
            else
            {
                if($bulk_action_reply=='mark_as_approved')
                {
                    $approved=1;
                }
                else
                {
                    $approved=0;
                }
                foreach($reply_readed as $id_reply => $value)
                {
                    if($value)
                    {
                        Ybc_blog_comment_class::updateApprovedReply($id_reply,$approved);
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment.'&conf=4',
                    )
                ));
            }
        }
        if(Tools::isSubmit('change_approved') && ($id_reply=(int)Tools::getValue('id_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj))
        {
            $change_approved = (int)Tools::getValue('change_approved');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            $approved = $replyObj->approved;
            $replyObj->approved = (int)$change_approved;
            if($replyObj->update())
            {
                if($change_approved)
                    $title = $this->l('Click to mark as unapproved');
                else
                    $title = $this->l('Click to mark as approved');
                if($approved!=$change_approved && $change_approved==1)
                {
                    $this->module->sendMailRepyCustomer($replyObj->id_comment,$replyObj->name,$replyObj->reply);
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_reply,
                        'enabled' => $change_approved,
                        'field' => 'approved',
                        'message' => $this->module->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment.'&change_approved='.($change_approved ? '0' : '1').'&id_reply='.(int)$id_reply,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
        if(Tools::isSubmit('change_comment_approved') && $id_comment && ($comment = new Ybc_blog_comment_class(($id_comment))) && Validate::isLoadedObject($comment) )
        {
            $change_comment_approved = (int)Tools::getValue('change_comment_approved');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            $comment->approved = (int)$change_comment_approved;
            if($comment->update())
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_reply,
                        'enabled' => $change_comment_approved,
                        'field' => 'approved',
                        'href' => $this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment.'&change_comment_approved='.($change_comment_approved ? '0' : '1'),
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=5&comment_reply=1&id_comment='.(int)$id_comment);
            }
        }
        if(Tools::isSubmit('delreply') && ($id_reply=(int)Tools::getValue('id_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) && $replyObj->delete())
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            Tools::redirectAdmin($this->baseLink.'&comment_reply=1&id_comment='.(int)$id_comment.'&conf=2');
        }
        if(Tools::isSubmit('addReplyComment') && $id_comment && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => $id_comment,
            ));
            $reply_comment_text = Tools::getValue('reply_comment_text');
            if(Tools::strlen($reply_comment_text) < 20)
                $errors[] = $this->l('Reply needs to be at least 20 characters');
            if(!Validate::isCleanHtml($reply_comment_text,false))
                $errors[] = $this->l('Reply needs to be clean HTML');
            if(Tools::strlen($reply_comment_text) >2000)
                $errors[] = $this->l('Reply cannot be longer than 2000 characters');
            if(!$errors)
            {
                $replyObj = new Ybc_blog_reply_class();
                $replyObj->id_comment = $comment->id;
                $replyObj->id_user =0;
                $replyObj->name = $this->context->employee->firstname.' '.$this->context->employee->lastname;
                $replyObj->email = $this->context->employee->email;
                $replyObj->id_employee = $this->context->employee->id;
                $replyObj->approved =1;
                $replyObj->reply = $reply_comment_text;
                $replyObj->datetime_added = date('Y-m-d H:i:s');
                $replyObj->datetime_updated = date('Y-m-d H:i:s');
                if($replyObj->add())
                {
                    $this->module->sendMailRepyCustomer($id_comment,$this->context->employee->firstname.' '.$this->context->employee->lastname);
                    $this->module->sendMailReplyAdmin($id_comment,$this->context->employee->firstname.' '.$this->context->employee->lastname,1,$reply_comment_text);
                    $this->_html .= $this->module->displaySuccessMessage($this->l('Reply has been submitted'));
                }
                else
                    $errors[] = $this->l('Add reply failed');
            }
            if($errors)
            {
                $this->context->smarty->assign(
                    array(
                        'replyCommentsave' => $id_comment,
                        'reply_comment_text' => $reply_comment_text,
                    )
                );
                $this->_html .= $this->module->displayError($errors);
            }
        }
    }
}
