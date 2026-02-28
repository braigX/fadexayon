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
class Ybc_blogRssModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
		$this->context = Context::getContext();
        $this->module= new Ybc_blog();
        
	}
    public function init()
	{
		parent::init();
        if($this->module->friendly && Tools::strpos($_SERVER['REQUEST_URI'],'/module/ybc_blog') !==false)
        {
            $this->module->redirect($this->module->getLink('rss'));
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
           if(Configuration::get('YBC_BLOG_ENABLE_RSS'))
           {
                if($id_category =(int)Tools::getValue('id_category') )
                {
                    $ybc_category= new Ybc_blog_category_class($id_category,$this->context->language->id);
                    $posts = Ybc_blog_post_class::getPostsByIdCategory($id_category);
                    if($posts)
                    {
                        foreach($posts as &$post)
                        {
                            if($post['thumb'])
                                $post['thumb'] =$this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                            if($post['image'])
                                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                        }
                    }
                    $xml ='<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .='<rss xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">'."\n";
                    $xml .='<channel>'."\n";
                        $xml .='<title>'.$this->cleanUTF8($ybc_category->title).'</title>'."\n";
                        $xml .='<description>'.($ybc_category->description ? strip_tags($this->cleanUTF8($ybc_category->description)):'').'</description>'."\n";
                        if($ybc_category->image)
                        {
                            $xml .='<image>'."\n";
                            $xml .='<url>'.$this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/'.$ybc_category->image).'</url>'."\n";
                            $xml .='<title>'.$this->cleanUTF8($ybc_category->title).'</title>'."\n";
                            $xml .='<link>'.$this->module->getLink().'</link>'."\n";
                            $xml .='</image>'."\n";
                        }  
                        $xml .='<pubDate>'.date('r',strtotime($ybc_category->datetime_added)).'</pubDate>'."\n";
                        $xml .='<generator>'.$this->context->shop->domain.'</generator>'."\n";
                        $xml .='<link>'.$this->module->getLink('blog',array('id_category'=>$ybc_category->id)).'</link>'."\n";
                        if($posts)
                        {
                            $xml .= $this->getXml($posts);
                        }
                    $xml .= '</channel>';
                    $xml .='</rss>';
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }
                    header("Content-Type: application/xml; charset=UTF-8");
                    mb_internal_encoding('UTF-8');
                    die($xml);
               }
               if(Tools::isSubmit('latest'))
               {
                    $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.id_post DESC, ');
                    if($posts)
                    {
                        foreach($posts as &$post)
                        {
                            if($post['thumb'])
                                $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                            if($post['image'])
                                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                        }
                    }
                    $xml ='<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .='<rss xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">'."\n";
                    $xml .='<channel>'."\n";
                        $xml .='<title>'.$this->module->l('Latest posts','rss').'</title>'."\n";
                        $xml .='<description></description>'."\n";
                        $xml .='<generator>'.$this->context->shop->domain.'</generator>'."\n";
                        $xml .='<link>'.$this->module->getLink('blog',array('latest'=>1)).'</link>'."\n";
                        if($posts)
                        {
                            $xml .= $this->getXml($posts);
                        }
                    $xml .= '</channel>';
                    $xml .='</rss>';
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }
                    header("Content-Type: application/xml; charset=UTF-8");
                    mb_internal_encoding('UTF-8');
                    die($xml);
                    
               }     
               if(Tools::isSubmit('popular'))
               {
                    $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.click_number desc,');
                    if($posts)
                    {
                        foreach($posts as &$post)
                        {
                            if($post['thumb'])
                                $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                            if($post['image'])
                                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                        }
                    }
                    $xml ='<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .='<rss xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">'."\n";
                    $xml .='<channel>'."\n";
                    $xml .='<title>'.$this->module->l('Featured posts','rss').'</title>'."\n";
                    $xml .='<description></description>'."\n";
                    $xml .='<generator>'.$this->context->shop->domain.'</generator>'."\n";
                    $xml .='<link>'.$this->module->getBaseLink().'</link>'."\n";
                    if($posts)
                    {
                        $xml .= $this->getXml($posts);
                    }
                    $xml .= '</channel>';
                    $xml .='</rss>';
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }
                    header("Content-Type: application/xml; charset=UTF-8");
                    mb_internal_encoding('UTF-8');
                    die($xml); 
               }
               if(Tools::isSubmit('featured'))
               {
                    $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 AND p.is_featured=1',$this->module->sort);
                    if($posts)
                    {
                        foreach($posts as &$post)
                        {
                            if($post['thumb'])
                                $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                            if($post['image'])
                                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                        }
                    }
                    $xml ='<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .='<rss xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">'."\n";
                    $xml .='<channel>'."\n";
                    $xml .='<title>'.$this->module->l('Featured posts','rss').'</title>'."\n";
                    $xml .='<description></description>'."\n";
                    $xml .='<generator>'.$this->context->shop->domain.'</generator>'."\n";
                    $xml .='<link>'.$this->module->getBaseLink().'</link>'."\n";
                    if($posts)
                    {
                        $xml .= $this->getXml($posts);
                    }
                    $xml .= '</channel>';
                    $xml .='</rss>';
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }
                    header("Content-Type: application/xml; charset=UTF-8");
                    mb_internal_encoding('UTF-8');
                    die($xml);   
                }
                if($id_author= (int)Tools::getValue('id_author'))
                {
                    $is_customer = (int)Tools::getValue('is_customer') ? 1 :0;
                    $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.added_by="'.(int)$id_author.'" AND p.is_customer="'.(int)$is_customer.'"',$this->module->sort);
                    if($posts)
                    {
                        foreach($posts as &$post)
                        {
                            if($post['thumb'])
                                $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                            if($post['image'])
                                $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                            $post['link'] = $this->module->getLink('blog',array('id_post'=>$post['id_post']));
                        }
                    }
                    $xml ='<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .='<rss xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">'."\n";
                    $xml .='<channel>'."\n";
                    $xml .='<title>'.$this->module->l('Featured posts','rss').'</title>'."\n";
                    $xml .='<description></description>'."\n";
                    $xml .='<generator>'.$this->context->shop->domain.'</generator>'."\n";
                    $xml .='<link>'.$this->module->getBaseLink().'</link>'."\n";
                    if($posts)
                    {
                        $xml .= $this->getXml($posts);
                    }
                    $xml .= '</channel>';
                    $xml .='</rss>';
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }
                    header("Content-Type: application/xml; charset=UTF-8");
                    mb_internal_encoding('UTF-8');
                    die($xml);   
                }
                $rss_type = Configuration::get('YBC_BLOC_RSS_TYPE')? explode(',',Configuration::get('YBC_BLOC_RSS_TYPE')):array();
                $this->context->smarty->assign(
                    array(
                        'YBC_BLOC_RSS_TYPE' => $rss_type,
                        'link_latest_posts' => $this->module->getLink('rss',array('latest_posts'=>1)),
                        'link_popular_posts' => $this->module->getLink('rss',array('popular_posts'=>1)),
                        'link_featured_posts' => $this->module->getLink('rss',array('featured_posts'=>1)),
                        'blogRssCategory' => in_array('category',$rss_type) ? $this->displayBlogRssCategory():'',
                        'blogRssAuthor' => in_array('authors',$rss_type) ? $this->displayBlogRssAuthor():'',
                    )
                );
               if($this->module->is17)
                   $this->setTemplate('module:ybc_blog/views/templates/front/rss.tpl');
               else
                   $this->setTemplate('rss_16.tpl');
           }
           else
               Tools::redirect($this->context->link->getPageLink('index'));

    }
    private function displayBlogRssCategory()
    {
        if(!$this->module->isCached('rss_categories_block.tpl',$this->module->_getCacheId()))
        {
            $blockCategTree = Ybc_blog_category_class::getBlogCategoriesTree(0);
            $this->context->smarty->assign(array(
                'blockCategTree'=> $blockCategTree,
                'branche_tpl_path' => _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/rss-category-tree-branch.tpl'
            ));
        }
        return $this->module->display($this->module->getLocalPath(), 'rss_categories_block.tpl',$this->module->_getCacheId());
    }
    private function displayBlogRssAuthor()
    {
        if(!$this->module->isCached('rss_author_block.tpl',$this->module->_getCacheId()))
        {
            $this->context->smarty->assign(
                Ybc_blog_post_class::getBlogRssAuthor()
            );
        }
        return $this->module->display($this->module->getLocalPath(),'rss_author_block.tpl',$this->module->_getCacheId());

    }
    public function cleanUTF8($some_string)
    {
        $some_string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.'|[\x00-\x7F][\x80-\xBF]+'.'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S','?', $some_string );
        $some_string = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.'|\xED[\xA0-\xBF][\x80-\xBF]/S','?', $some_string );
        return $some_string;
    }
    public function getXML($posts)
    {
        $xml ='';
        foreach($posts as $post)
        {
            $xml .='<item>'."\n";
                $xml .='<title>'.$post['title'].'</title>'."\n";
                $xml .='<description><![CDATA[';
                $xml.='<a href="'.($post['thumb'] ? $post['thumb'] : ($post['image']?$post['image']:'')).'"><img width=130 height=100 src="'.($post['thumb'] ? $post['thumb'] : ($post['image']?$post['image']:'')).'" ></a>';
                $xml .='</br>'.strip_tags($this->cleanUTF8($post['short_description'])); 
                $xml.=']]></description>'."\n";
                $xml .='<pubDate>'.date('r',strtotime($post['datetime_added'])).'</pubDate>'."\n";
                $xml .='<link>'.$post['link'].'</link>'."\n";;
                $xml .='<guid>'.$post['link'].'</guid>'."\n";;
                $xml .='<slash:comments>0</slash:comments>'."\n";;
            $xml .='</item>'."\n";;
        }
        return $xml;
    }
}