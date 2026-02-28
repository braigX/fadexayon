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
 * Class AdminYbcBlogPollsController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogPollsController extends ModuleAdminController
{
    public $baseLink;
    public $_html='';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogPolls');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog comments'))
        {
            $this->checked = true;
            $this->_postPolls();
        }

    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('polls'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getcontent()
    {
        $fields_list = array(
            'id_polls' => array(
                'title' => $this->l('Vote ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'email' => array(
                'title' => $this->l('Email'),
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
            'feedback'=>array(
                'title'=>$this->l('Feedback'),
                'type' => 'text',
                'filter' => true,
            ),
            'polls' => array(
                'title' => $this->l('Helpful'),
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
        if(($id_polls = trim(Tools::getValue('id_polls')))!='' && Validate::isCleanHtml($id_polls))
        {
            $filter .= " AND po.id_polls = ".(int)$id_polls;
            $show_reset = true;
        }
        if(($feedback = trim(Tools::getValue('feedback')))!='' && Validate::isCleanHtml($feedback))
        {
            $filter .= " AND po.feedback like '%".pSQL($feedback)."%'";
            $show_reset = true;
        }
        if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
        {
            $filter .= " AND po.name like '%".pSQL($name)."%'";
            $show_reset = true;
        }
        if(($polls = trim(Tools::getValue('polls')))!='' && Validate::isCleanHtml($polls))
        {
            $filter .= " AND po.polls = ".(int)$polls;
            $show_reset = true;
        }
        if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
        {
            $filter .= " AND pl.title like '%".pSQL($title)."%'";
            $show_reset = true;
        }
        if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
        {
            $show_reset = true;
            $filter .= " AND po.email like '%".pSQL($email)."%'";
        }
        //Sort
        $sort_post = Tools::strtolower(Tools::getValue('sort'));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
        if($sort_post && isset($fields_list[$sort_post]))
        {
            $sort = $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = 'po.id_polls DESC,';
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page <1)
            $page =1;
        $totalRecords = (int)Ybc_blog_polls_class::countPollsWithFilter($filter,false);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_polls_select_limit',20);
        $paggination->name ='ybc_polls';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $polls = Ybc_blog_polls_class::getPollsWithFilter($filter, $sort, $start, $paggination->limit,false);
        if($polls)
        {
            foreach($polls as &$poll)
            {
                $poll['title'] = $this->module->displayText($poll['title'],'a',null,null,$this->module->getLink('blog',array('id_post'=>$poll['id_post'])),true);
                if($poll['id_user'])
                {
                    if(version_compare(_PS_VERSION_, '1.7.6', '>='))
                    {
                        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
                        if (null !== $sfContainer) {
                            $sfRouter = $sfContainer->get('router');
                            $link_customer= $sfRouter->generate(
                                'admin_customers_view',
                                array('customerId' => $poll['id_user'])
                            );
                        }
                        else
                            $link_customer = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$poll['id_user'].'&viewcustomer';
                    }
                    else
                        $link_customer = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$poll['id_user'].'&viewcustomer';
                    $poll['link_customer'] = $link_customer;
                }
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_polls',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink.($paggination->limit!=20 ? '&paginator_ybc_polls_select_limit='.$paggination->limit:''),
            'identifier' => 'id_polls',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Polls'),
            'fields_list' => $fields_list,
            'field_values' => $polls,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'show_add_new' => false,
            'sort'=> $sort_post ? :'id_polls',
            'sort_type'=> $sort_type,
        );
        return $this->module->renderList($listData);
    }
    private function _postPolls()
    {
        /**
         * Change status
         */
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_polls = (int)Tools::getValue('id_polls');
            $polls_class = new Ybc_blog_polls_class($id_polls);
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$polls_class->id_post,
            ));
            if($id_polls && property_exists('Ybc_blog_polls_class', $field))
            {
                Ybc_blog_defines::changeStatus('polls',$field,$id_polls,$status);
                if($status==1)
                    $title = $this->l('Click to mark this as unhelpful');
                else
                    $title = $this->l('Click to mark this as helpful');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_polls,
                        'enabled' => $status,
                        'field' => $field,
                        'message' =>  $this->module->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_polls='.$id_polls,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
        /**
         * Delete comment
         */
        if(Tools::isSubmit('del'))
        {
            $id_polls = (int)Tools::getValue('id_polls');
            if(($polls_class = new Ybc_blog_polls_class($id_polls)) && Validate::isLoadedObject($polls_class))
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$polls_class->id_post,
                ));
                if($polls_class->delete())
                    Tools::redirectAdmin($this->baseLink.'&conf=2');
            }
        }
        /**
         * form send mail
         */
        if(Tools::isSubmit('sendmailform') && ($id_polls= (int)Tools::getValue('id_polls')))
        {
            $polls_class = new Ybc_blog_polls_class($id_polls);
            $this->context->smarty->assign(
                array(
                    'polls_class' => $polls_class,

                )
            );
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'html_form' => $this->module->display($this->module->getLocalPath(),'form_send_mail_polls.tpl'),
                        )
                    )
                );
            }
            return $this->module->display($this->module->getLocalPath(),'form_send_mail_polls.tpl');
        }
        if(Tools::isSubmit('send_mail_polls') && ($id_polls=(int)Tools::getValue('id_polls')))
        {
            $errors=array();
            if(($message_email = trim(Tools::getValue('message_email')))=='')
            {
                $errors[] = $this->l('Message is required');
            }
            elseif($message_email && !Validate::isCleanHtml($message_email))
                $errors[] = $this->l('Message is not valid');
            if(($subject = trim(Tools::getValue('subject_email')))=='')
                $errors[]=$this->l('Subject is required');
            elseif($subject && !Validate::isCleanHtml($subject))
                $errors[]=$this->l('Subject is not valid');
            if(!$errors)
            {
                $polls_class = new Ybc_blog_polls_class($id_polls);
                $template_customer_vars=array(
                    '{message_email}'  => $message_email,
                    '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                    '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                );
                Mail::Send(
                    Context::getContext()->language->id,
                    'reply_polls_customer',
                    $subject,
                    $template_customer_vars,
                    $polls_class->email,
                    $polls_class->name,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'ybc_blog'.'/mails/'
                );
                die(json_encode(
                    array(
                        'message' =>$this->l('Email was sent successfully'),
                        'messageType'=>'success'
                    )
                ));
            }
            else
            {
                die(json_encode(
                    array(
                        'message' =>$this->module->displayError($errors),
                        'messageType'=>'error'
                    )
                ));
            }

        }
    }
}
