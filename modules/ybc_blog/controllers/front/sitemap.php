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
class Ybc_blogSitemapModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public function __construct()
	{
	   parent::__construct();
	   $this->display_column_left=false;
       $this->display_column_right=false;
       $this->context = Context::getContext();
       $this->module= new Ybc_blog();
    }
    public function init()
	{
		parent::init();
		if(!Configuration::get('YBC_BLOG_ENABLE_SITEMAP'))
		    Tools::redirect('index.php');
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
       if($id_lang = $this->getLangFromUrl() || count(Language::getLanguages(true))==1)
       {
            $this->getSiteMapBlog($id_lang);
       }
       else
            $this->getSiteMapLanguages();
       
    }
    public function getSiteMapBlog($id_lang)
    {
        if(!$id_lang)
            $id_lang = $this->context->language->id;
        $pages_sitemap= explode(',',Configuration::get('YBC_BLOC_SITEMAP_PAGES'));
        $xml ='<?xml version="1.0" encoding="UTF-8"?>';
        $xml .='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
        if(in_array('main_blog',$pages_sitemap))
        {
            $xml .='<url>'."\n";
           $xml .='<loc><![CDATA['.$this->module->getLink('blog').']]></loc>'."\n";
           $xml .='<priority>1.0</priority>'."\n"; 
           $xml .='<changefreq>daily</changefreq>';
           $xml .='</url>'."\n";
        }
        if(in_array('single_post',$pages_sitemap))
        {
            $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1',$this->module->sort);
            if($posts)
            {
                foreach($posts as $post)
                {
                    $xml .='<url>'."\n";
                    $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('id_post'=>$post['id_post'])).']]></loc>'."\n";
                    $xml .='<priority>0.9</priority>'."\n"; 
                    $xml .='<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
                    $xml .='<changefreq>daily</changefreq>'."\n";
                    if($post['thumb'] || $post['image'])
                    {
                        $xml .='<image:image>'."\n";
                        $xml .='<image:loc><![CDATA['.($post['thumb'] ? $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']) : $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image'])).']]></image:loc>'."\n";
                        $xml .='<image:caption><![CDATA['.$post['title'].']]></image:caption>'."\n";
                        $xml .='<image:title><![CDATA['.$post['title'].']]></image:title>'."\n";
                        $xml .='</image:image>'."\n";   
                    }
                    $xml .='</url>'."\n";
                }
           }
        }
        if(in_array('category',$pages_sitemap))
        {
           $categories = Ybc_blog_category_class::getCategoriesWithFilter(' AND c.enabled=1');
           foreach($categories as $category)
           {
                $xml .='<url>'."\n";
                $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('id_category'=>$category['id_category'])).']]></loc>'."\n";
                $xml .='<priority>0.8</priority>'."\n"; 
                $xml .='<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
                $xml .='<changefreq>daily</changefreq>'."\n";
                if($category['image'] || $category['image'])
                {
                    $xml .='<image:image>'."\n";
                    $xml .='<image:loc><![CDATA['.$this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/'.$category['image']).']]></image:loc>'."\n";
                    $xml .='<image:caption><![CDATA['.$category['title'].']]></image:caption>'."\n";
                    $xml .='<image:title><![CDATA['.$category['title'].']]></image:title>'."\n";
                    $xml .='</image:image>'."\n";   
                }
                $xml .='</url>'."\n";
           }
        }
        if(in_array('authors',$pages_sitemap))
        {
           $employees = Ybc_blog_post_employee_class::getEmployeesFilter();
           foreach($employees as $employee)
           {
            
                $alias = $employee['name'] ? Tools::link_rewrite($employee['name']): Tools::link_rewrite($employee['firstname'].' '.$employee['lastname']);
                $xml .='<url>'."\n";
                $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('id_author'=>$employee['id_employee'],'alias'=>$alias,'is_customer'=>0)).']]></loc>'."\n";
                $xml .='<priority>0.7</priority>'."\n"; 
                $xml .='<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
                $xml .='<changefreq>daily</changefreq>'."\n";
                if($employee['avata'] || $employee['avata'])
                {
                    $xml .='<image:image>'."\n";
                    $xml .='<image:loc><![CDATA['.$this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$employee['avata']).']]></image:loc>'."\n";
                    $xml .='<image:caption><![CDATA['.($employee['name'] ? $employee['name']: $employee['firstname'].' '.$employee['lastname']).']]></image:caption>'."\n";
                    $xml .='<image:title><![CDATA['.($employee['name'] ? $employee['name']: $employee['firstname'].' '.$employee['lastname']).']]></image:title>'."\n";
                    $xml .='</image:image>'."\n";   
                }
                $xml .='</url>'."\n";
           }
           if(Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'))
           {
                $customers = Ybc_blog_post_employee_class::getCustomersFilter();
                foreach($customers as $customer)
                {
                    $alias = $customer['name'] ? Tools::link_rewrite($customer['name']): Tools::link_rewrite($customer['firstname'].' '.$customer['lastname']);
                    $xml .='<url>'."\n";
                    $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('id_author'=>$customer['id_customer'],'alias'=>$alias,'is_customer'=>1)).']]></loc>'."\n";
                    $xml .='<priority>0.6</priority>'."\n"; 
                    $xml .='<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
                    $xml .='<changefreq>daily</changefreq>'."\n";
                    if($customer['avata'] || $customer['avata'])
                    {
                        $xml .='<image:image>'."\n";
                        $xml .='<image:loc><![CDATA['.$this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$customer['avata']).']]></image:loc>'."\n";
                        $xml .='<image:caption><![CDATA['.($customer['name'] ? $customer['name']: $customer['firstname'].' '.$customer['lastname']).']]></image:caption>'."\n";
                        $xml .='<image:title><![CDATA['.($customer['name'] ? $customer['name']: $customer['firstname'].' '.$customer['lastname']).']]></image:title>'."\n";
                        $xml .='</image:image>'."\n";   
                        
                    }
                    $xml .='</url>'."\n";
               }
           }
           
        }
        if(in_array('latest_post',$pages_sitemap))
        {
            $xml .='<url>'."\n";
            $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('latest'=>true)).']]></loc>'."\n";
            $xml .='<priority>1.0</priority>'."\n"; 
            $xml .='<changefreq>daily</changefreq>';
            $xml .='</url>'."\n";
        }
        if(in_array('featured_post',$pages_sitemap))
        {
            $xml .='<url>'."\n";
            $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('featured'=>true)).']]></loc>'."\n";
            $xml .='<priority>1.0</priority>'."\n"; 
            $xml .='<changefreq>daily</changefreq>';
            $xml .='</url>'."\n";
        }
        if(in_array('popular_post',$pages_sitemap))
        {
            $xml .='<url>'."\n";
            $xml .='<loc><![CDATA['.$this->module->getLink('blog',array('popular'=>true)).']]></loc>'."\n";
            $xml .='<priority>1.0</priority>'."\n"; 
            $xml .='<changefreq>daily</changefreq>';
            $xml .='</url>'."\n";
        }
        $xml .='</urlset>';
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        header("Content-Type: application/xml; charset=UTF-8");
        mb_internal_encoding('UTF-8');
        die($xml);
    }
    public function getSiteMapLanguages()
    {
        if($languages = Language::getLanguages(true))
        {
            $xml ='<?xml version="1.0" encoding="UTF-8"?>'."\n";;
            $xml .='<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";;
                foreach($languages as $language)
                {
                    $xml .='<sitemap>'."\n";
                        $xml .='<loc>'.$this->module->getBaseLink().$language['iso_code'].'/blog_sitemap.xml</loc>'."\n";
                        $xml .'<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
                    $xml .='</sitemap>'."\n";
                }   
            $xml .='</sitemapindex>';
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
           header("Content-Type: application/xml; charset=ISO-8859-1");
           mb_internal_encoding('UTF-8');
           die(utf8_encode($xml));
        }
    }
    public function getLangFromUrl($getIsoCode = false)
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        $requestUri = rawurldecode($requestUri);

        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop)) {
            $requestUri = preg_replace(
                '#^'.preg_quote(Context::getContext()->shop->getBaseURI(), '#').'#i',
                '/',
                $requestUri
            );
        }

        // If there are several languages, get language from uri
        if (Language::isMultiLanguageActivated()) {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $requestUri, $m)) {
                $isoCode = $m[1];
                if($isoCode)
                {
                    $id_lang = Language::getIdByIso($isoCode);
                    if($id_lang)
                    {
                        if($getIsoCode)
                        {
                            return $isoCode;
                        }
                        return (int)$id_lang;
                    }
                    return false;
                }
            }
        }
        return 0;
    }
}
