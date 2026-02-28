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
 
require_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_paggination_class.php'); 
if (!defined('_PS_VERSION_'))
    	exit;

/**
 * Class AdminYbcBlogStatisticsController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogStatisticsController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->bootstrap = true;
    }
    public function init()
    {
        parent::init();
        if (Tools::isSubmit('ajaxpostsearch'))
        {
            return $this->ajaxpostsearch();
        }
    }
    public function initContent()
    {
        parent::initContent();
        if (Tools::isSubmit('clearviewLogSubmit')) {
            Ybc_blog_post_class::clearAllViewLog();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminYbcBlogStatistics') . '&tab_ets=view-log&conf=1');
        }
        if (Tools::isSubmit('clearlikeLogSubmit')) {
            Ybc_blog_post_class::clearAllLikeLog();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminYbcBlogStatistics') . '&tab_ets=like-log&conf=1');
        }

    }
    public function renderList()
    {
        if(!$this->module->checkProfileEmployee($this->context->employee->id,'Statistics'))
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/error_access.tpl');
        $id_post = (int)Tools::getValue('id_post');
        $months=Tools::dateMonths();
        $now_year = date('Y')+2;
        $start_year = Ybc_blog_post_class::getMinYearAddPost($id_post);
        $years = array();
        if($start_year)
        {
            for($i=$start_year-2;$i<=$now_year;$i++)
            {
                $years[]=$i;
            }
        }
        $views=array();
        $likes =array();
        $comments=array();
        $year = (int)Tools::getValue('years',date('Y'));
        $month = (int)Tools::getValue('months',date('m'));
        if(!$year)
        {
            if($years)
            {
                foreach($years as $year)
                {
                    $likes[] =array(
                        0 => $year,
                        1 => Ybc_blog_post_class::getCountLike($year,'','',$id_post),
                    );
                    $views[] =array(
                        0 => $year,
                        1 => Ybc_blog_post_class::getCountView($year,'','',$id_post),
                    );
                    $comments[] =array(
                        0 => $year,
                        1 => Ybc_blog_post_class::getCountComment($year,'','',$id_post),
                    );
                }
            }
        }
        elseif($year)
        {
            if(!$month){
                if($months)
                {
                    foreach($months as $key=> $month)
                    {
                        $likes[] =array(
                            0 => $key,
                            1 => Ybc_blog_post_class::getCountLike($year,$key,'',$id_post),
                        );
                        $views[] =array(
                            0 => $key,
                            1 => Ybc_blog_post_class::getCountView($year,$key,'',$id_post),
                        );
                        $comments[] =array(
                            0 => $key,
                            1 => Ybc_blog_post_class::getCountComment($year,$key,'',$id_post),
                        );
                    }
                }
            }
            elseif($month)
            {
                $days = function_exists('cal_days_in_month') ? cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year) : (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $likes[] =array(
                            0 => $day,
                            1 => Ybc_blog_post_class::getCountLike($year,$month,$day,$id_post),
                        );
                        $views[] =array(
                            0 => $day,
                            1 => Ybc_blog_post_class::getCountView($year,$month,$day,$id_post),
                        );
                        $comments[] =array(
                            0 => $day,
                            1 => Ybc_blog_post_class::getCountComment($year,$month,$day,$id_post),
                        );
                    }
                }
            }
        }
        $lineChart =array( 
            array(
                'key'=> $this->module->l('Views'),
                'values'=>$views,
                'disables'=>1,
            ) 
        );
        if(Configuration::get('YBC_BLOG_ALLOW_LIKE'))
        {
           $lineChart[]= array(
                'key'=> $this->module->l('Likes'),
                'values'=>$likes,
                'disables'=>1,
            );
        }
        if(Configuration::get('YBC_BLOG_ALLOW_COMMENT'))
        {
            $lineChart[]=array(
                'key'=> $this->module->l('Comments'),
                'values'=>$comments,
                'disables'=>1,
            );
        }
        $posts= Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1');
        $total= Ybc_blog_post_class::getCountLogViews();
        $limit=20;
        $page = (int)Tools::getValue('page',1);
        if($page<=0)
            $page=1;
        $start= ($page-1)*$limit;
        $pagination_view = new Ybc_blog_paggination_class();
        $pagination_view->url = $this->context->link->getAdminLink('AdminYbcBlogStatistics').'&tab_ets=view-log&page=_page_';
        $pagination_view->limit=$limit;
        $pagination_view->page= $page;
        $pagination_view->total=$total;
        $viewlogs = Ybc_blog_post_class::getLogViews($start,$limit);
        if($viewlogs)
        {
            foreach($viewlogs as &$log)
            {
                $browser = explode(' ',$log['browser']);
                if(isset($browser[0]))
                    $log['class'] = Tools::strtolower($browser[0]);
                else
                    $log['class']='default';
                $log['title'] = $this->module->displayText($log['title'],'a',null,null,$this->module->getLink('blog',array('id_post'=>$log['id_post'])),true);
            }   
        }
        $total = Ybc_blog_post_class::getCountLogLikes();
        $limit=20;
        $page = (int)Tools::getValue('page',1);
        if($page<=0)
            $page=1;
        $start= ($page-1)*$limit;
        $pagination_like = new Ybc_blog_paggination_class();
        $pagination_like->url = $this->context->link->getAdminLink('AdminYbcBlogStatistics').'&tab_ets=like-log&page=_page_';
        $pagination_like->limit=$limit;
        $pagination_like->page= $page;
        $pagination_like->total=$total;
        $likelogs = Ybc_blog_post_class::getLogLikes($start,$limit);
        if($likelogs)
        {
            foreach($likelogs as &$log)
            {
                $browser = explode(' ',$log['browser']);
                if(isset($browser[0]))
                    $log['class'] = Tools::strtolower($browser[0]);
                else
                    $log['class']='default';
                $log['title'] = $this->module->displayText($log['title'],'a',null,null,$this->module->getLink('blog',array('id_post'=>$log['id_post'])),true);;
            }   
        }
        if(($id_post = (int)Tools::getValue('id_post')))
        {
            $post= new Ybc_blog_post_class($id_post,$this->context->language->id);
        }
        $tab_ets = Tools::getValue('tab_ets','chart');
        if(!Validate::isCleanHtml($tab_ets))
            $tab_ets = 'chart';
        $this->context->smarty->assign(
            array(
                'months' => $months,
                'ctf_month' => $month ? : date('m'),
                'action'=> $this->context->link->getAdminLink('AdminYbcBlogStatistics'),
                'years'=>$years,
                'ctf_year' => $year ? : date('Y'),
                'lineChart' => $lineChart,
                'ctf_post' => (int)$id_post,
                'ctf_post_title' => (int)$id_post ? $post->title : '',
                'js_dir_path' => $this->module->blogDir.'views/js/',
                'likelogs'=>$likelogs,
                'viewlogs'=>$viewlogs,
                'posts' => $posts,
                'tab_ets' => $tab_ets,
                'control'=> 'statistics',
                'ybc_blog_ajax_post_url' => $this->context->link->getAdminLink('AdminYbcBlogStatistics', true).'&ajaxpostsearch=true',
                'YBC_BLOG_ALLOW_LIKE' => Configuration::get('YBC_BLOG_ALLOW_LIKE'),
                'pagination_text_view' => $pagination_view->render(),
                'pagination_text_like' => $pagination_like->render(),
                'ybc_blog_sidebar' => $this->module->renderSidebar(),
                'show_reset' => Tools::isSubmit('submitFilterChart'),
            )
        );
        return  $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'statistics.tpl');
    }
    public function ajaxPostSearch()
    {
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR (Tools::strlen($query) < 3 AND !Validate::isUnsignedId($query)) OR !Validate::isCleanHtml($query) )
            die();
        $posts= Ybc_blog_post_class::getPostsWithFilter(' AND ( p.id_post="'.(int)$query.'" OR pl.title like "%'.pSQL($query).'%")');
        if($posts)
        {
            foreach ($posts as $post)
            {
                echo $post['title'].'|'.$post['id_post'].'|'.($post['thumb'] ?  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']) :  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']) )."\n";
            }
        }
        die();
    }
}