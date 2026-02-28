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

if(!defined('_PS_VERSION_'))
	exit;
include_once(dirname(__FILE__).'/classes/ybc_blog_category_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_post_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_paggination_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_comment_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_reply_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_polls_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_slide_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_gallery_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_link_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_employee_class.php');
include_once(dirname(__FILE__).'/classes/ybc_blog_email_template_class.php');
include_once(dirname(__FILE__).'/classes/ImportExport.php');
include_once(dirname(__FILE__).'/classes/ybc_browser.php');
include_once(dirname(__FILE__).'/ybc_blog_defines.php');
include_once(dirname(__FILE__).'/classes/ybc_chatgpt.php');
include_once(dirname(__FILE__).'/classes/ybc_chatgpt_message.php');
if(!function_exists('ets_getCookie'))
    include_once(dirname(__FILE__).'/classes/cookie');
if (!defined('_PS_YBC_BLOG_IMG_DIR_')) {
    define('_PS_YBC_BLOG_IMG_DIR_', _PS_IMG_DIR_.'ybc_blog/');
}
if (!defined('_PS_YBC_BLOG_IMG_')) {
    define('_PS_YBC_BLOG_IMG_', _PS_IMG_.'ybc_blog/');
}
if (!defined('_YBC_BLOG_CACHE_DIR_'))
    define('_YBC_BLOG_CACHE_DIR_', _PS_CACHE_DIR_ . 'ybc_blog/');
class Ybc_blog extends Module
{
    public $baseAdminPath;
    public $errorMessage = false;
    private $_html = '';
    public $blogDir;
    public $alias;
    public $friendly;
    public $is17 = false;
    public $configTabs = array();
    public $import_ok=false;   
    public $errors = array();
    public $sort = false;
    public $controls;
    public $link_capcha;
    public function __construct()
	{
        $this->name = 'ybc_blog';
		$this->tab = 'front_office_features';
		$this->version = '4.5.1';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true; 
        $this->module_key = 'da314fdf1af6d043f9b2f15dce2bef1e';
        parent::__construct();
        if(!Configuration::get('YBC_BLOG_POST_SORT_BY'))
            $this->sort = 'p.datetime_added DESC, ';
        else
        {
            if(Configuration::get('YBC_BLOG_POST_SORT_BY')=='sort_order')
                $this->sort = 'p.sort_order ASC, ';
            elseif(Configuration::get('YBC_BLOG_POST_SORT_BY')=='id_post')
                $this->sort = 'p.datetime_added DESC, ';
            else
                $this->sort = 'p.'.Configuration::get('YBC_BLOG_POST_SORT_BY').' DESC, ';
        }
		$this->displayName = $this->l('BLOG');
        $this->description = $this->l('The most powerful, flexible and feature-rich blog module for Prestashop. BLOG provides everything you need to create a professional blog area for your website.');
		$this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->blogDir = $this->_path;  
        $this->alias = Configuration::get('YBC_BLOG_ALIAS',$this->context->language->id) ? : Configuration::get('YBC_BLOG_ALIAS',Configuration::get('PS_LANG_DEFAULT'));
        $this->friendly = (int)Configuration::get('YBC_BLOG_FRIENDLY_URL') && (int)Configuration::get('PS_REWRITING_SETTINGS') ? true : false;    
        $g_recaptcha = Tools::getValue('g-recaptcha-response');
        $recaptcha = $g_recaptcha && Validate::isCleanHtml($g_recaptcha) ? $g_recaptcha: '';
        $secret = Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' ? Configuration::get('YBC_BLOG_CAPTCHA_SECRET_KEY') : Configuration::get('YBC_BLOG_CAPTCHA_SECRET_KEY3');
        $this->link_capcha="https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptcha . "&remoteip=" . Tools::getRemoteAddr();
        $this->controls = array('category','post','comment','polls','slide','gallery','seo','sitemap','rss','socials','email','image','sidebar','homepage','postlistpage','postpage','categorypage','productpage','employees','customer','export','config','comment_reply','author');
    }
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	    return parent::install()&& $this->registerHook('displayLeftColumn')
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('displayHome') 
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayFooter')
        && $this->registerHook('blogSearchBlock')
        && $this->registerHook('blogTagsBlock')
        && $this->registerHook('blogNewsBlock')
        && $this->registerHook('blogCategoriesBlock')
        && $this->registerHook('blogGalleryBlock')
        && $this->registerHook('blogPopularPostsBlock')
        && $this->registerHook('moduleRoutes')
        && $this->registerHook('blogSidebar')
        && $this->registerHook('blogFeaturedPostsBlock')
        && $this->registerHook('displayRightColumn')
        && $this->registerHook('displayFooterProduct')
        && $this->registerHook('blogArchivesBlock')
        && $this->registerHook('blogComments')
        && $this->registerHook('blogPositiveAuthor')
        && $this->registerHook('customerAccount')
        && $this->registerHook('displayMyAccountBlock')
        && $this->registerHook('blogRssSideBar')
        && $this->registerHook('blogCategoryBlock')
        && $this->registerHook('displayBackOfficeFooter')
        && $this->registerHook('displayFooterYourAccount')
        && $this->registerHook('actionObjectLanguageAddAfter')
        && $this->registerHook('displayFooterCategory')
        && $this->registerHook('actionUpdateBlog')
        && $this->registerHook('actionUpdateBlogImage')
        && $this->registerHook('actionMetaPageSave')
        && $this->_installDb()
        && $this->_installTabs() && $this->_copyForderMail();
    }    
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        return parent::uninstall() &&  $this->_uninstallDb() && $this->_uninstallTabs();
    }
    private function _installDb()
    {
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_);
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'slide/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'slide/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'slide/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'post/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'/post');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'post/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'/post/thumb');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'post/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'gallery/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'gallery/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'gallery/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'category/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'category/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'category/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'category/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'avata/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'avata/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'avata/index.php');
        $languages = Language::getLanguages(false);
        //Install db structure
        Configuration::updateValue('PS_ALLOW_HTML_IFRAME',1);
        require_once(dirname(__FILE__).'/install/sql.php');
        require_once(dirname(__FILE__).'/install/data.php');
        if($configs = Ybc_blog_defines::getInstance()->getConfigsGlobal())
        {
            foreach($configs as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_seo = Ybc_blog_defines::getInstance()->getConfigsSeo())
        {
            foreach($configs_seo as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if(($configs_sitemap = Ybc_blog_defines::getInstance()->getConfigSiteMap()))
        {
            foreach($configs_sitemap as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_homepage = Ybc_blog_defines::getInstance()->getConfigsHome())
        {
            $configs_homepage['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
                'label' => $this->l('Select blog categories to display'),
                    'type' => 'blog_categories',
                    'default' =>'',
            );
            foreach($configs_homepage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_postpage = Ybc_blog_defines::getInstance()->getConfigsPostPage())
        {
            foreach($configs_postpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_postlistpage = Ybc_blog_defines::getInstance()->getConfigsPostListPage())
        {
            foreach($configs_postlistpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_categorypage = Ybc_blog_defines::getInstance()->getConfigsCategoryPage())
        {
            foreach($configs_categorypage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_productpage = Ybc_blog_defines::getInstance()->getConfigsProductPage())
        {
            foreach($configs_productpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_sidebar = Ybc_blog_defines::getInstance()->getConfigsSidebar())
        {
            foreach($configs_sidebar as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_email = Ybc_blog_defines::getInstance()->getConfigsEmail())
        {
            foreach($configs_email as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($socials = Ybc_blog_defines::getInstance()->getConfigsSocials())
        {
            foreach($socials as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_rss = Ybc_blog_defines::getInstance()->getConfigsRss())
        {
            foreach($configs_rss as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if(($customer_settings = Ybc_blog_defines::getInstance()->getCustomerSettings()))
        {
            foreach($customer_settings as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($configs_image=Ybc_blog_defines::getInstance()->getConfigsImage())
        {
            foreach($configs_image as $key=>$config)
            {
                if($config['type']=='image')
                {
                    Configuration::updateValue($key.'_WIDTH',$config['default'][0]);
                    Configuration::updateValue($key.'_HEIGHT',$config['default'][1]);
                }
                else
                {
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
                }
                
            }
        } 
        Configuration::updateValue('YBC_BLOG_ALERT_EMAILS',Configuration::get('PS_SHOP_EMAIL'));
        if (defined('_PS_ADMIN_DIR_'))
        {
            $adminforder= str_replace(_PS_ROOT_DIR_,'',_PS_ADMIN_DIR_);
            $adminforder= trim(trim($adminforder,'\\'),'/');
            Configuration::updateValue('YBC_BLOG_ADMIN_FORDER',$adminforder);
        }
        $this->refreshCssCustom();
        $this->initEmailTemplate();
        return true;
    }
    public function _copyForderMail()
    {
        $languages = Language::getLanguages(false);
        $temp_dir_ltr = dirname(__FILE__) . '/mails/en';
        if ($languages && is_array($languages))
        {
            if (!@file_exists($temp_dir_ltr))
                return true;
            foreach ($languages as $language)
            {
                if(isset($language['iso_code']) && $language['iso_code'] != 'en')
                {
                     if (($new_dir = dirname(__FILE__) . '/mails/'. $language['iso_code']))
                     {
                        
                        $this->recurseCopy($temp_dir_ltr, $new_dir);
                     }
                }
            }
        }
        return true;
    }
    public function deleteDir($dir)
    {
        $dir = rtrim($dir,'/');
        $files = glob($dir.'/*'); 
        foreach($files as $file){ 
            if(is_dir($file))
                $this->deleteDir($file);
            elseif(is_file($file) && file_exists($file))
                @unlink($file); 
        }
        @rmdir($dir);
        return true;
    }
    public function recurseCopy($src, $dst)
    {
        if(!@file_exists($src))
            return false;
        $dir = opendir($src);
        if (!@is_dir($dst))
            @mkdir($dst);
        while(false !== ($file = readdir($dir)))
        {
            if (( $file != '.' ) && ($file != '..' ))
            {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file,$dst . '/' . $file);
                }
                elseif (!@file_exists($dst . '/' . $file))
                {
                    @copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }
    private function _uninstallDb()
    {
        if($configs = Ybc_blog_defines::getInstance()->getConfigsGlobal())
        {
            foreach($configs as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_seo = Ybc_blog_defines::getInstance()->getConfigsSeo())
        {
            foreach($configs_seo as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if(($configs_sitemap =Ybc_blog_defines::getInstance()->getConfigSiteMap()))
        {
            foreach($configs_sitemap as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_homepage = Ybc_blog_defines::getInstance()->getConfigsHome())
        {
            foreach($configs_homepage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_postpage = Ybc_blog_defines::getInstance()->getConfigsPostPage())
        {
            foreach($configs_postpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_postlistpage = Ybc_blog_defines::getInstance()->getConfigsPostListPage())
        {
            foreach($configs_postlistpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_categorypage =Ybc_blog_defines::getInstance()->getConfigsCategoryPage())
        {
            foreach($configs_categorypage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_productpage = Ybc_blog_defines::getInstance()->getConfigsProductPage())
        {
            foreach($configs_productpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_sidebar = Ybc_blog_defines::getInstance()->getConfigsSidebar())
        {
            foreach($configs_sidebar as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_email = Ybc_blog_defines::getInstance()->getConfigsEmail())
        {
            foreach($configs_email as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($socials = Ybc_blog_defines::getInstance()->getConfigsSocials())
        {
            foreach($socials as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($configs_rss = Ybc_blog_defines::getInstance()->getConfigsRss())
        {
            foreach($configs_rss as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if(($customer_settings = Ybc_blog_defines::getInstance()->getCustomerSettings()))
        {
            foreach($customer_settings as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        Ybc_blog_defines::deleteTableDb();
        $this->deleteDir(_PS_YBC_BLOG_IMG_DIR_);
        if(file_exists(_YBC_BLOG_CACHE_DIR_.'ybc_blog.data.zip'))
            unlink(_YBC_BLOG_CACHE_DIR_.'ybc_blog.data.zip');
        return true;
    }
    public function getContent()
	{
        if(!$this->active)
            return $this->displayWarning($this->l('Module is disabled'));
        if(($action = Tools::getValue('action')) &&  $action=='getCountMessageYbcBlog')
        {
            die(
                json_encode(
                    array(
                        'count' => Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.viewed=0',false),
                    )
                )
            );
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminYbcBlogPost'));
    }
    public function getAssign($control)
    {
        $id_post = (int)Tools::getValue('id_post');
        $id_category = (int)Tools::getValue('id_category');
        return array(
            'ybc_blog_ajax_url' => $this->context->link->getAdminLink('AdminYbcBlogPost', true).'&ajaxproductsearch=true',
            'ybc_blog_author_ajax_url' => $this->context->link->getAdminLink('AdminYbcBlogPost', true).'&ajaxCustomersearch=true',
            'ybc_blog_default_lang' => Configuration::get('PS_LANG_DEFAULT'),
            'ybc_blog_is_updating' => (int)$id_post || (int)$id_category ? 1 :  0,
            'ybc_blog_is_config_page' => $control == 'config' ? 1 : 0,
            'ybc_blog_invalid_file' => $this->l('Invalid file'),
            'ybc_blog_module_dir' => $this->_path,
            'ybc_blog_sidebar' => $this->renderSidebar($control),
            'ybc_blog_error_message' => $this->errorMessage,
            'control' => $control,
        );
    }
    public function displaySuccessMessage($msg, $title = false, $link = false)
    {
         if($msg)
         {
             $this->context->smarty->assign(array(
                 'msg' => $msg,
                 'title' => $title,
                 'link' => $link
             ));
             return $this->displayConfirmation($this->display(__FILE__, 'success_message.tpl'));
         }
         return '';
    }
    public function getSelectedCategories($id_post=false)
    {
        if(Tools::isSubmit('submitPostStay'))
        {
            $categories = Tools::getValue('blog_categories');
            if(is_array($categories) && Ybc_blog::validateArray($categories))
                return $categories;
            else
                return array();
        }
        $categories = array();
        if($id_post)
        {
            $rows = Ybc_blog_post_class::getOnlyCategoryBlog($id_post);
            if($rows)
            {
                foreach($rows as $row)
                {
                    $categories[] = $row['id_category'];
                }
            }
        }
        elseif($id_post===false)
            $categories = Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME') ? explode(',',Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME')):array();
        return $categories;
    }
    /**
     * Sidebar
     */
    public function renderSidebar($control =false)
    {
        if(!$this->isCached('sidebar.tpl',$this->_getCacheId(array($control,$this->context->employee->id))))
        {
            $settingAdminPath = $this->context->link->getAdminLink('AdminYbcBlogSetting');
            $list = array(
                array(
                    'label' => $this->l('Posts'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogPost'),
                    'id' => 'ybc_tab_post',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'),
                    'controller'=>'AdminYbcBlogPost',
                    'icon' => 'icon-AdminPriceRule'
                ),
                array(
                    'label' => $this->l('Categories'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogCategory'),
                    'id' => 'ybc_tab_category',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'),
                    'controller'=>'AdminYbcBlogCategory',
                    'icon' => 'icon-AdminCatalog'
                ),
                array(
                    'label' => $this->l('Comments'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogComment'),
                    'id' => 'ybc_tab_comment',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog comments'),
                    'controller'=>'AdminYbcBlogComment',
                    'icon' => 'icon-comments',
                    'total_result' => Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.viewed=0',false),
                ),
                array(
                    'label' => $this->l('Polls'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogPolls'),
                    'id' => 'ybc_tab_polls',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog comments'),
                    'controller'=>'AdminYbcBlogPolls',
                    'icon' => 'icon-polls',
                ),
                array(
                    'label' => $this->l('Slider'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogSlider'),
                    'id' => 'ybc_tab_slide',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog slider'),
                    'icon' => 'icon-AdminParentModules',
                    'controller'=>'AdminYbcBlogSlider',
                ),
                array(
                    'label' => $this->l('Photo gallery'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogGallery'),
                    'id' => 'ybc_tab_gallery',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog gallery'),
                    'icon' => 'icon-AdminDashboard',
                    'controller'=>'AdminYbcBlogGallery',
                ),
                array(
                    'label' => $this->l('Seo'),
                    'url' => $settingAdminPath.'&control=seo',
                    'id' => 'ybc_tab_seo',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Seo'),
                    'icon' => 'icon-seo',
                    'controller'=>'AdminYbcBlogSeo',
                ),
                array(
                    'label' => $this->l('Google sitemap'),
                    'url' => $settingAdminPath.'&control=sitemap',
                    'id' => 'ybc_tab_sitemap',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Sitemap'),
                    'icon' => 'icon-sitemap',
                    'controller'=>'AdminYbcBlogSitemap',
                ),
                array(
                    'label' => $this->l('RSS feed'),
                    'url' => $settingAdminPath.'&control=rss',
                    'id' => 'ybc_tab_rss',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'RSS feed'),
                    'icon' => 'icon-rss',
                    'controller'=>'AdminYbcBlogRSS',
                ),
                array(
                    'label' => $this->l('Socials'),
                    'url' => $settingAdminPath.'&control=socials',
                    'id' => 'ybc_tab_socials',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Socials'),
                    'icon' => 'icon-socials',
                    'controller'=>'AdminYbcBlogSocials',
                ),
                array(
                    'label' => $this->l('Email'),
                    'url' => $settingAdminPath.'&control=email',
                    'id' => 'ybc_tab_email',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Email'),
                    'icon' => 'icon-email',
                    'controller'=>'AdminYbcBlogEmail',
                ),
                array(
                    'label'=> $this->l('Image'),
                    'id'=>'ybc_tab_image',
                    'icon'=>'icon-image',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Image'),
                    'url' => $settingAdminPath.'&control=image',
                    'controller'=>'AdminYbcBlogImage',
                ),
                array(
                    'label' => $this->l('Sidebar'),
                    'url' => $settingAdminPath.'&control=sidebar',
                    'id' => 'ybc_tab_sidebar',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Sidebar'),
                    'icon' => 'icon-sidebar',
                    'controller'=>'AdminYbcBlogSidebar',
                ),
                array(
                    'label' => $this->l('Home page'),
                    'url' => $settingAdminPath.'&control=homepage',
                    'id' => 'ybc_tab_homepage',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Home page'),
                    'icon' => 'icon-homepage',
                    'controller'=>'AdminYbcBlogHomepage',
                ),
                array(
                    'label' => $this->l('Post listing pages'),
                    'url' => $settingAdminPath.'&control=postlistpage',
                    'id' => 'ybc_tab_postlistpage',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Post listing pages'),
                    'icon' => 'icon-postlistpage',
                    'controller'=>'AdminYbcBlogPostListPage',
                ),
                array(
                    'label' => $this->l('Post details page'),
                    'url' => $settingAdminPath.'&control=postpage',
                    'id' => 'ybc_tab_postpage',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Post detail page'),
                    'icon' => 'icon-postpage',
                    'controller'=>'AdminYbcBlogPostpage',
                ),
                array(
                    'label' => $this->l('Product categories page'),
                    'url' => $settingAdminPath.'&control=categorypage',
                    'id' => 'ybc_tab_categorypage',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Category page'),
                    'icon' => 'icon-categorypage',
                    'controller'=>'AdminYbcBlogCategorypage',
                ),
                array(
                    'label' => $this->l('Product details page'),
                    'url' => $settingAdminPath.'&control=productpage',
                    'id' => 'ybc_tab_productpage',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Product detail page'),
                    'icon' => 'icon-productpage',
                    'controller'=>'AdminYbcBlogProductpage',
                ),
                array(
                    'label'=> $this->l('Authors'),
                    'id'=>'ybc_tab_employees',
                    'icon'=>'icon-user',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Authors'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogAuthor'),
                    'controller'=>'AdminYbcBlogAuthor',
                ),
                array(
                    'label' => $this->l('Statistics'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogStatistics'),
                    'id' => 'ybc_tab_statistics',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Statistics'),
                    'icon' => 'icon-chart',
                    'controller'=>'AdminYbcBlogStatistics',
                ),
                array(
                    'label' => $this->l('Import/Export'),
                    'url' => $this->context->link->getAdminLink('AdminYbcBlogBackUp'),
                    'id' => 'ybc_tab_export',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Import/Export'),
                    'icon' => 'icon-exchange',
                    'controller'=>'AdminYbcBlogBackUp',
                ),
                array(
                    'label' => $this->l('Global settings'),
                    'url' => $settingAdminPath.'&control=config',
                    'id' => 'ybc_tab_config',
                    'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Global settings'),
                    'icon' => 'icon-AdminAdmin',
                    'controller'=>'AdminYbcBlogSetting',
                ),
            );
            $control = Tools::getValue('control',$control);
            $controller = Tools::getValue('controller');
            $this->context->smarty->assign(
                array(
                    'link' => $this->context->link,
                    'list' => $list,
                    'admin_path' => $this->baseAdminPath,
                    'active' => 'ybc_tab_'.($control && in_array($control,$this->controls) ? $control : ($controller=='AdminYbcBlogStatistics' ? 'statistics'  :'post'))
                )
            );
        }
        return $this->display(__FILE__, 'sidebar.tpl',$this->_getCacheId(array($control,$this->context->employee->id)));
    }
/**
 * Functions 
 */


public function getFieldsValues($formFields, $primaryKey, $objClass, $saveBtnName)
{
	$fields = array();        
	if (Tools::isSubmit($primaryKey))
	{
		$obj = new $objClass((int)Tools::getValue($primaryKey));
		$fields[$primaryKey] = (int)Tools::getValue($primaryKey, $obj->id);
	}
	else
    {
        $obj = new $objClass();
        $fields[$primaryKey] = 0;
    }
    foreach($formFields as $field)
    {
        if(!isset($field['primary_key']) && !isset($field['multi_lang']) && !isset($field['connection']))
        {
            $fieldName = $field['name'];
            $fields[$field['name']] = Tools::getValue($field['name'], $obj->$fieldName);
        }
                 
    }   
    $languages = Language::getLanguages(false);
    
    /**
     *  Default
     */
    
    if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
    {
        foreach($formFields as $field)
        {
            if(isset($field['default']) && !isset($field['multi_lang']))
            {
                if(isset($field['default_submit']))
                    $fields[$field['name']] = Tools::getValue($field['name']) ? : $field['default'];
                else
                    $fields[$field['name']] = $field['default'];
            }
        }
    }
    
    /**
     * Multiple language 
     */
	foreach ($languages as $lang)
	{
	    foreach($formFields as $field)
        {
            if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
            {
                if(isset($field['multi_lang']))
                {
                    if(isset($field['default']))
                        $fields[$field['name']][$lang['id_lang']] = $field['default'];
                    else
                        $fields[$field['name']][$lang['id_lang']] = '';
                }
            }
            elseif(Tools::isSubmit($saveBtnName))
            {
                if(isset($field['multi_lang']))
                    $fields[$field['name']][$lang['id_lang']] = Tools::getValue($field['name'].'_'.(int)$lang['id_lang']);  
                
            }
            else{                    
                if(isset($field['multi_lang']))
                {
                    $fieldName = $field['name'];
                    $field_langs = $obj->$fieldName;                        
                    $fields[$field['name']][$lang['id_lang']] = isset($field_langs[$lang['id_lang']]) ? $field_langs[$lang['id_lang']]:'';
                }                        
            }                
        }
	}
    $fields['control'] = trim(Tools::getValue('control')) ? : '';
    
    /**
     * Tags 
     */
     if($primaryKey=='id_post')
     {
        $id_post = (int)Tools::getValue('id_post');
        foreach ($languages as $lang)
        {
            if(Tools::isSubmit('savePost'))
            {                    
                $fields['tags'][$lang['id_lang']] = trim(trim(Tools::getValue('tags_'.(int)$lang['id_lang'])),',') ? : '';
            }
            else
                $fields['tags'][$lang['id_lang']] = Ybc_blog_post_class::getTagStr((int)$id_post, (int)$lang['id_lang']);
            
        }            
     }
     return $fields;
}
public function renderList($listData)
{      
    if(isset($listData['fields_list']) && $listData['fields_list'])
    {
        foreach($listData['fields_list'] as $key => &$val)
        {
            $control = Tools::getValue('control');
            if(isset($val['filter']) && $val['filter'] && $val['type']=='int')
            {
                $val['active']['max'] =  trim(Tools::getValue($key.'_max'));   
                $val['active']['min'] =  trim(Tools::getValue($key.'_min'));   
            }  
            elseif($listData['name']=='ybc_blog_employee' && $control!='employees')
            {
                $val['active']='';
            }
            elseif($listData['name']=='ybc_blog_customer' && $control!='customer')
            {
                $val['active']='';
            }
            elseif($key=='has_post' && !Tools::isSubmit('has_post'))
                $val['active']=1;
            else               
                $val['active'] = trim(Tools::getValue($key));
        }
    }    
    $this->smarty->assign($listData);
    return $this->display(__FILE__, 'list_helper.tpl');
}
public function renderListByCustomer($listData)
{
    if(isset($listData['fields_list']) && $listData['fields_list'])
    {
        foreach($listData['fields_list'] as $key => &$val)
        {
            $val['active'] = trim(Tools::getValue($key));
        }
    }    
    $this->context->smarty->assign($listData);
    return $this->display(__FILE__, 'list_helper_customer.tpl');
}
public function getUrlExtra($field_list)
{
    $params = '';
    $sort = Tools::strtolower(Tools::getValue('sort'));
    $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
    if(!in_array($sort_type,array('desc','asc')))
        $sort_type = 'desc';
    if($sort && isset($field_list[trim($sort)]))
    {
        $params .= '&sort='.trim($sort).'&sort_type='.(trim($sort_type) =='asc' ? 'asc' : 'desc');
    }
    if($field_list)
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    return $params;
}
public function getUrlExtraFrontEnd($field_list,$submit)
{
    $params = '';
    $sort = Tools::strtolower(Tools::getValue('sort'));
    $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
    if(!in_array($sort_type,array('desc','asc')))
        $sort_type = 'desc';
    if($sort && isset($field_list[trim($sort)]))
    {
        $params .= '&sort='.trim($sort).'&sort_type='.(trim($sort_type) =='asc' ? 'asc' : 'desc');
    }
    if($field_list)
    {
        $ok=false;
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
                $ok=true;
            }
        }
        if($ok)
            $params .='&'.$submit.'=1';
        unset($val);
    }
    return $params;
}
public function getFilterParams($field_list)
{
    $params = '';
    if($field_list && Tools::isSubmit('post_filter'))
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    return $params;
}
public function getFilterParamsFontEnd($field_list,$submit)
{
    $params = '';        
    if($field_list)
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    if($params)
        $params .='&'.$submit.'=1';
    return $params;
}
public function getCategoriesStrByIdPost($id_post)
{
    $categories = Ybc_blog_post_class::getOnlyCategoryBlog($id_post);
    $this->smarty->assign(array('categories' => $categories));
    return $this->display(__FILE__,'categories_str.tpl');
}
public function getPostById($id_post)
{
    $filter = ' AND (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.id_post = '.(int)$id_post;
    $posts= Ybc_blog_post_class::getPostsWithFilter($filter,false,false,false,false);
    if($posts)
    {
        $posts[0]['pending'] = $posts[0]['added_by']==$this->context->customer->id && $posts[0]['is_customer'] && ($posts[0]['enabled']==1 || $posts[0]['enabled']==-1) ? 1 :0;
        return $posts[0];
    }
    return false;
}
 public function refreshCssCustom()
 {
    $color = Configuration::get('YBC_BLOG_CUSTOM_COLOR');
    if(!$color) 
        $color = '#FF4C65';
    $color_hover= Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER');
    if(!$color_hover)
        $color_hover='#FF4C65';
    $css = file_exists(dirname(__FILE__).'/views/css/dynamic_style.css') ? Tools::file_get_contents(dirname(__FILE__).'/views/css/dynamic_style.css') : ''; 
    if($css)
        $css = str_replace(array('[color]','[color_hover]'),array($color,$color_hover),$css);
    file_put_contents(dirname(__FILE__).'/views/css/custom.css',$css);
    $this->_clearCache('*');
 }
 public function getLink($controller = 'blog', $params = array(),$id_lang=0)
 {
    $context = Context::getContext();      
    $id_lang =  $id_lang ? $id_lang : $context->language->id;
    $alias = $this->alias;
    $friendly = $this->friendly;
    $blogLink = new Ybc_blog_link_class();
    $subfix = (int)Configuration::get('YBC_BLOG_URL_SUBFIX') ? '.html' : '';
    $page = isset($params['page']) && $params['page'] ? $params['page'] : '';
    if(trim($page)!='')
    {
        $page = $page.'/';
    }
    else
        $page='';
    if($friendly && $alias)
    {
        $removeID = Configuration::get('YBC_BLOG_URL_NO_ID');
        $url = $blogLink->getBaseLinkFriendly(null, null).$blogLink->getLangLinkFriendly($id_lang, null, null).$alias.'/';
        if($controller=='gallery')
        {                
           $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$id_lang)) ? $subAlias : 'gallery').($page ? '/'.rtrim($page,'/') : '');
           return $url;
        }
        elseif($controller=='category')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$id_lang)) ? $subAlias : 'categories').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }
        elseif($controller=='comment')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$id_lang)) ? $subAlias : 'comments').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }
        elseif($controller=='rss')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$id_lang)) ? $subAlias : 'rss');
            if(isset($params['id_category']) && $categoryAlias = Ybc_blog_category_class::getCategoryAlias((int)$params['id_category'],$id_lang))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').'/'.(int)$params['id_category'].'-'.$categoryAlias.$subfix;
            }
            elseif(isset($params['id_author']) && isset($params['is_customer']) && $params['is_customer'] &&  $authorAlias = (isset($params['alias']) ? $params['alias'] : 'community-author'))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$id_lang)) ? $subAlias : 'community-author').'/'.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['id_author']) && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'author'))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').'/'.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['latest_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_LATEST',$id_lang)) ? $subAlias : 'latest-posts');
            }
            elseif(isset($params['popular_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_POPULAR',$id_lang)) ? $subAlias : 'popular-posts');
            }
            elseif(isset($params['featured_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_FEATURED',$id_lang)) ? $subAlias : 'featured-posts');
            }
            return $url;
        }
        elseif($controller=='blog')
        {
            if(isset($params['edit_comment']) && (int)$params['edit_comment'] && isset($params['id_post']) && $params['id_post'] && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.(int)$params['id_post'].'-'.(int)$params['edit_comment'].'-'.$postAlias.$subfix;
            }
            elseif( isset($params['all_comment']) && $params['all_comment'] &&  isset($params['id_post']) && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/allcomments/'.(int)$params['id_post'].'-'.$postAlias.$subfix;
            }
            elseif(isset($params['id_post']) && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                if($removeID)
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.$postAlias.$subfix;
                else
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.$params['id_post'].'-'.$postAlias.$subfix;
            }
            elseif($removeID && isset($params['post_url_alias']) && ($post_url_alias = $params['post_url_alias']) && Validate::isLinkRewrite($post_url_alias) && ($id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$this->context->language->id)) && ($postAlias = Ybc_blog_post_class::getPostAlias($id_post,$id_lang)))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.$postAlias.$subfix;
            }
            elseif(isset($params['id_category']) && $categoryAlias = Ybc_blog_category_class::getCategoryAlias((int)$params['id_category'],$id_lang))
            {
                 if($removeID)
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').($page ? '/'.rtrim($page) : '/').$categoryAlias.$subfix;
                 else
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').($page ? '/'.rtrim($page) : '/').$params['id_category'].'-'.$categoryAlias.$subfix;
            }
            elseif($removeID && isset($params['category_url_alias']) && ($category_url_alias = $params['category_url_alias']) && Validate::isLinkRewrite($category_url_alias) && ($id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$this->context->language->id)) && ($categoryAlias = Ybc_blog_category_class::getCategoryAlias($id_category,$id_lang)))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').($page ? '/'.rtrim($page) : '/').$categoryAlias.$subfix;
            }
            elseif(isset($params['id_author']) && isset($params['is_customer']) && $params['is_customer'] && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'community-author'))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$id_lang)) ? $subAlias : 'community-author').($page ? '/'.rtrim($page) : '/').(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['id_author']) && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'author'))
            {

                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').'/'.$page.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['tag']))
            {
                $url .= $page.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$id_lang)) ? $subAlias : 'tag').'/'.(string)$params['tag'];
            }
            elseif(isset($params['search']))
            {
                $url .= $page.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$id_lang)) ? $subAlias : 'search').'/'.(string)$params['search'];
            }
            elseif(isset($params['latest']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$id_lang)) ? $subAlias : 'latest').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['popular']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$id_lang)) ? $subAlias : 'popular').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['featured']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$id_lang)) ? $subAlias : 'featured').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['month']) && isset($params['year']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$id_lang)) ? $subAlias : 'month').'/'.$params['month'].'/'.$params['year'].($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['year']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$id_lang)) ? $subAlias : 'year').'/'.$params['year'].($page ? '/'.rtrim($page,'/') : '');
            }
            else
            {
                if($page)
                    $url .= trim($page,'/');
                else
                    $url = rtrim($url,'/');
            }
            if(isset($params['edit_comment']) && (int)$params['edit_comment'] && isset($params['id_post']) && $params['id_post'])  
                $url .='#ybc-blog-form-comment';
            return $url;            
        }
        elseif($controller=='author')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }            
    }
    return $this->context->link->getModuleLink($this->name,$controller,$params,null,$id_lang);
 }
 public function getEverageReviews($id_post)
 {
    $totalRating = Ybc_blog_post_class::getTotalReviewsWithRating($id_post);
    $numRating = Ybc_blog_post_class::countTotalReviewsWithRating($id_post);
    if($numRating > 0)
    {
        $rat = Tools::ps_round($totalRating/$numRating,2);
        $rat_ceil = ceil($totalRating/$numRating);
        $rat_floor = floor($totalRating/$numRating);
        if($rat_ceil-$rat <=0.25)
            return $rat_ceil;
        if($rat-$rat_floor<=0.25)   
            return $rat_floor;
        return $rat_floor+0.5;        
    }
        
    return 0;        
 }
 
    /**
    * Hooks
    */
    public function hookDisplayLeftColumn()
    {
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        if(Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && ($fc!='module' || $module!=$this->name))
            return '';
        $params=array();
        $sidebars=array(
            'sidebar_new' => Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK') ? $this->hookBlogNewsBlock($params):'',
            'sidebar_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') ? $this->hookBlogPopularPostsBlock($params):'',
            'sidebar_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? $this->hookBlogFeaturedPostsBlock($params):'',
            'sidebar_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') ? $this->hookBlogGalleryBlock($params):'',
            'sidebar_archived' => Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') ? $this->hookBlogArchivesBlock():'',
            'sidebar_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') ? $this->hookBlogCategoriesBlock():'',
            'sidebar_search' => Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') ? $this->hookBlogSearchBlock():'',
            'sidebar_tags' => Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') ? $this->hookBlogTagsBlock():'',
            'sidebar_comments' => Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') ? $this->hookBlogComments():'',
            'sidebar_authors' => Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') ? $this->hookBlogPositiveAuthor():'',
            'sidebar_htmlbox' => Configuration::get('YBC_BLOG_SHOW_HTML_BOX') ? $this->displayHtmlContent():'',
            'sidebar_rss' => Configuration::get('YBC_BLOG_ENABLE_RSS_SIDEBAR') && Configuration::get('YBC_BLOG_ENABLE_RSS') && in_array('side_bar',explode(',',Configuration::get('YBC_BLOC_RSS_DISPLAY'))) ? $this->hookBlogRssSideBar():'',
        );
        $sidebars_postion= explode(',',Configuration::get('YBC_BLOG_POSITION_SIDEBAR') ? Configuration::get('YBC_BLOG_POSITION_SIDEBAR') :'sidebar_categories,sidebar_search,sidebar_new,sidebar_popular,sidebar_featured,sidebar_tags,sidebar_gallery,sidebar_archived,sidebar_comments,sidebar_authors,sidebar_htmlbox,sidebar_rss');
        if(!in_array('sidebar_htmlbox',$sidebars_postion))
            $sidebars_postion[] = 'sidebar_htmlbox';
        $display_slidebar = false;
        if($sidebars)
        {
            foreach($sidebars as $sidebar)
            {
                if($sidebar)
                {
                    $display_slidebar = true;
                    break;
                }
            }
        }
        if(!$display_slidebar)
            return '';
        $this->context->smarty->assign(
            array(
                'sidebars_postion' => $sidebars_postion,
                'sidebars'=>$sidebars,
                'display_slidebar' => $display_slidebar,
            )
        );
        return $this->display(__FILE__, 'blocks.tpl');
    }
    public function displayHtmlContent()
    {
        if(!$this->isCached('html_box.tpl',$this->_getCacheId()))
        {
            if($content = Configuration::get('YBC_BLOG_CONTENT_HTML_BOX',$this->context->language->id))
            {
                $this->context->smarty->assign(
                    array(
                        'html_content_box' => $content,
                        'blog_page' => 'html_box',
                        'html_title_box' => Configuration::get('YBC_BLOG_TITLE_HTML_BOX',$this->context->language->id) ? : $this->l('Html box'),
                    )
                );

            }
        }
        return $this->display(__FILE__,'html_box.tpl',$this->_getCacheId());
    }
  public function hookBlogSidebar()
  {
      return $this->hookDisplayLeftColumn();
  }
  public function hookRightColumn()
  {
      return $this->hookDisplayLeftColumn();
  }      
  public function hookDisplayBackOfficeHeader()
  {
        $this->context->controller->addJquery();
        $this->context->controller->addCSS($this->_path.'views/css/admin_all.css');
        $this->context->controller->addJS($this->_path.'views/js/admin_all.js'); 
        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        $controllers = array('AdminYbcBlogStatistics','AdminYbcBlogAuthor','AdminYbcBlogCategory','AdminYbcBlogComment','AdminYbcBlog','AdminYbcBlogGallery','AdminYbcBlogPolls','AdminYbcBlogPost','AdminYbcBlogSetting','AdminYbcBlogSlider','Statistics','AdminYbcBlogBackUp');
        if(($controller=='AdminModules' && $configure==$this->name) || in_array($controller,$controllers))
        {
            $this->context->controller->addJqueryPlugin('autocomplete');
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJqueryPlugin('tagify');
            if(Configuration::get('YBC_BLOG_ENABLED_GPT'))
            {
                $this->context->controller->addJqueryUI(array('ui.draggable'));
                $this->context->controller->addJqueryUI(array('ui.resizable'));
            }
            $this->context->controller->addJS($this->_path.'views/js/chatgpt.js');
            if(Tools::isSubmit('current_tab') && ($current_tab = Tools::getValue('current_tab')) && Validate::isCleanHtml($current_tab) )
            {
                Media::addJsDef(array(
                    'ybc_current_tab'=>$current_tab
                ));
            }
            else
                Media::addJsDef(array(
                    'ybc_current_tab'=>false
                ));
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
            if(!$this->is17)
            {
                $this->context->controller->addCSS($this->_path.'views/css/admin_fix16.css'); 
            }
        }
        if($controller=='AdminYbcBlogStatistics')
        {
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3_rtl.css','all');
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3.css','all');
        }
  }
  public function hookDisplayFooter()
  {
        $this->context->smarty->assign(array(
                'like_url' => $this->getLink('like'),
                'YBC_BLOG_SLIDER_SPEED' => (int)Configuration::get('YBC_BLOG_SLIDER_SPEED') > 0 ? (int)Configuration::get('YBC_BLOG_SLIDER_SPEED') : 5000,
                'YBC_BLOG_GALLERY_SPEED' => (int)Configuration::get('YBC_BLOG_GALLERY_SPEED') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_SPEED') : 5000,
                'YBC_BLOG_GALLERY_SKIN' => Configuration::get('YBC_BLOG_GALLERY_SKIN') ? Configuration::get('YBC_BLOG_GALLERY_SKIN') : 'default',
                'YBC_BLOG_GALLERY_AUTO_PLAY' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                'ybc_like_error' =>  addslashes($this->l('There was a problem while submitting your request. Try again later'))                                   
            )
        );
        return $this->display(__FILE__, 'footer.tpl');
  }
  public function hookDisplayHeader()
  {

        $this->refreshCache();

        $controller = Tools::getValue('controller'); 
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        $this->context->controller->addCSS($this->_path.'views/css/blog_all.css');
        if($controller=='myaccount'){
            $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
            $this->context->controller->addCSS($this->_path.'views/css/myaccount.css');
            return '';
        }
        if($controller=='index'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME'))
            return '';
        if($controller=='index'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') && !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        if( ($fc!='module' || $module!=$this->name) && $controller!='index' && $controller!='product' &&  $controller!='category'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'))
            return '';
        elseif(($fc!='module' || $module!=$this->name) && $controller!='index' && $controller!='product' && $controller!='category'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY'))
            return '';
        if($controller=='category' && $fc!='module'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE')) 
            return '';
        if($controller=='product'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE')) 
            return '';
        if($controller=='product'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE')&& !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        if($controller=='category' && $fc!='module'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE')&& !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        $this->assignConfig();
        if(Module::isInstalled('ybc_blog')&& Module::isEnabled('ybc_blog'))
        {
            if(Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','datetime_active'))
                Ybc_blog_post_class::autoActivePost();
        }
        $disable_slick = Configuration::get('YBC_BLOG_DISABLE_SLICK_LIBRARY');
        if($controller!='index'){
            if(!$disable_slick)
            {
                $slick = false;
                if(Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE')=='carousel')
                    $slick = true;
                elseif($fc =='module' && $module == $this->name && $controller=='blog' && (Configuration::get('YBC_BLOG_DISPLAY_TYPE')=='carousel' || (Configuration::get('YBC_BLOG_SHOW_RELATED_PRODUCTS') && Configuration::get('YBC_RELATED_PRODUCTS_TYPE')=='carousel') || (Configuration::get('YBC_BLOG_DISPLAY_RELATED_POSTS') && Configuration::get('YBC_RELATED_POSTS_TYPE')=='carousel') ) )
                    $slick = true;
                elseif(!$fc && $controller=='category' && Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE') && Configuration::get('YBC_BLOG_CATEGORY_POST_TYPE')=='carousel')
                    $slick = true;
                elseif(!$fc && $controller=='product' &&  Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE') && Configuration::get('YBC_BLOG_PRODUCT_POST_TYPE')=='carousel')
                    $slick = true;
                if($slick)
                {
                    $this->context->controller->addJS($this->_path.'views/js/slick.js');
                    $this->context->controller->addCSS($this->_path.'views/css/slick.css');
                }
            }
            if(($fc == 'module' && $module== $this->name && $controller=='blog' && (Configuration::get('YBC_BLOG_ENABLE_POST_SLIDESHOW') || Configuration::get('YBC_BLOG_CATEGORY_ENABLE_POST_SLIDESHOW')) ) || (Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && Configuration::get('YBC_BLOG_GALLERY_SLIDESHOW_ENABLED')) )
            {
                $this->context->controller->addJS($this->_path.'views/js/jquery.prettyPhoto.js');
                $this->context->controller->addJS($this->_path.'views/js/prettyPhoto.inc.js');
                $this->context->controller->addCSS($this->_path.'views/css/prettyPhoto.css');
            }
            $this->context->controller->addJS($this->_path.'views/js/jquery.lazyload.min.js');
            $this->context->controller->addJS($this->_path.'views/js/blog.js');
            if(!$this->is17)
                $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
        }

        if($controller=='managementcomments'){
          $this->context->controller->addCSS($this->_path.'views/css/managementcomments.css');
        }
        if($controller=='managementmyinfo'){
          $this->context->controller->addCSS($this->_path.'views/css/managementmyinfo.css');
        }
        if($controller=='rss'){
          $this->context->controller->addCSS($this->_path.'views/css/rss.css');
        }
        if($controller=='managementblog'){
          $this->context->controller->addCSS($this->_path.'views/css/my_blog_post.css');
            $this->context->controller->addJS($this->_path.'views/js/my_blog_post.js');
        }
        if($controller=='product'){
          $this->context->controller->addCSS($this->_path.'views/css/product.css');
        }
      if($fc=='module' && $module=='ybc_blog' && $controller=='blog' && Configuration::get('YBC_BLOG_DISPLAY_TYPE') == 'nivo')
      {
          $this->context->controller->addJS($this->_path.'views/js/jquery.nivo.slider.js');
          $this->context->controller->addCSS($this->_path.'views/css/nivo-slider.css');
          $this->context->controller->addCSS($this->_path.'views/css/themes/default/default.css');
      }
        if($controller=='blog'){
          $this->context->controller->addCSS($this->_path.'views/css/detail_post.css');
            $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
            $this->context->controller->addJS($this->_path.'views/js/detail_post.js');
        }
        if($controller=='author'){
            if ( Configuration::get('YBC_BLOG_SIDEBAR_POSITION') == 'left' || Configuration::get('YBC_BLOG_SIDEBAR_POSITION') == 'right' ){
                $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
            }
            $this->context->controller->addCSS($this->_path.'views/css/author.css');
        }
        if($controller=='gallery'){
          $this->context->controller->addCSS($this->_path.'views/css/gallery.css');
          $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
        }
        if($controller=='comment'){
          $this->context->controller->addCSS($this->_path.'views/css/comment.css');
          $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
        }
        if(!$module=='ybc_blog' && $controller=='category'){
          $this->context->controller->addCSS($this->_path.'views/css/category.css');
        }
        if($fc=='module' && $module=='ybc_blog' && $controller=='category'){
          $this->context->controller->addCSS($this->_path.'views/css/category.css');
          $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
        }
        if ( Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') != true ){
            $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
            $this->context->controller->addJS($this->_path.'views/js/sidebar.js');
        } else {
            if( $fc=='module' && $module=='ybc_blog' &&
                (Configuration::get('YBC_BLOG_SIDEBAR_POSITION') == 'left' || Configuration::get('YBC_BLOG_SIDEBAR_POSITION') == 'right') &&
                ($controller=='blog' || $controller=='author' || $controller=='gallery' || $controller=='comment' || $controller=='category')
            ){
                $this->context->controller->addCSS($this->_path.'views/css/sidebar.css');
                $this->context->controller->addJS($this->_path.'views/js/sidebar.js');
            }
        }

        if($controller=='index') {
            if(Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && Configuration::get('YBC_BLOG_GALLERY_SLIDESHOW_ENABLED'))
            {
                $this->context->controller->addJS($this->_path.'views/js/jquery.prettyPhoto.js');
                $this->context->controller->addJS($this->_path.'views/js/prettyPhoto.inc.js');
                $this->context->controller->addCSS($this->_path.'views/css/prettyPhoto.css');
            }
            if(!$disable_slick)
            {
                if(Configuration::get('YBC_BLOG_HOME_POST_TYPE')=='carousel' || (Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && Configuration::get('YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED')))
                {
                    $this->context->controller->addJS($this->_path.'views/js/slick.js');
                    $this->context->controller->addCSS($this->_path.'views/css/slick.css');
                }
            }
            $this->context->controller->addJS($this->_path.'views/js/home_blog.js');
            if(!$this->is17)           
                $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
            $this->context->controller->addCSS($this->_path.'views/css/blog_home.css');
        }
        if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
            $this->context->controller->addCSS($this->_path.'views/css/rtl.css');


        if($controller=='category' && Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE') && $id_category=(int)Tools::getValue('id_category'))
        {
            if(Tools::isSubmit('displayPostRelatedCategories'))
            {
                die(json_encode(
                    array(
                        'html_block' => $this->displayPostRelatedCategories($id_category),
                    )
                ));
            }
            $this->context->controller->addJS($this->_path.'views/js/related.js'); 
        }

        return $this->getInternalStyles();
  }
  public function assignConfig()
  {
      $assign = array();
      if($configs = Ybc_blog_defines::getInstance()->getConfigsGlobal())
      {
          foreach($configs as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_seo = Ybc_blog_defines::getInstance()->getConfigsSeo())
      {
          foreach($configs_seo as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_sitemap = Ybc_blog_defines::getInstance()->getConfigSiteMap())
      {
          foreach($configs_sitemap as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }

      if($configs_sidebar = Ybc_blog_defines::getInstance()->getConfigsSidebar())
      {
          foreach($configs_sidebar as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }

      if($configs_home = Ybc_blog_defines::getInstance()->getConfigsHome())
      {
          $configs_home['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
              'label' => $this->l('Select blog categories to display'),
              'type' => 'blog_categories',
              'default' =>'',
          );
          foreach($configs_home as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_postpage = Ybc_blog_defines::getInstance()->getConfigsPostPage())
      {
          foreach($configs_postpage as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }

      if($configs_postlistpage = Ybc_blog_defines::getInstance()->getConfigsPostListPage())
      {
          foreach($configs_postlistpage as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_productpage = Ybc_blog_defines::getInstance()->getConfigsProductPage())
      {
          foreach($configs_productpage as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_categorypage = Ybc_blog_defines::getInstance()->getConfigsCategoryPage())
      {
          foreach($configs_categorypage as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_email = Ybc_blog_defines::getInstance()->getConfigsEmail())
      {
          foreach($configs_email as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if(($socials = Ybc_blog_defines::getInstance()->getConfigsSocials()))
      {
          foreach($socials as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }

      if(($customer_settings = Ybc_blog_defines::getInstance()->getCustomerSettings()))
      {
          foreach($customer_settings as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }
      if($configs_rss = Ybc_blog_defines::getInstance()->getConfigsRss())
      {
          foreach($configs_rss as $key => $val)
          {
              $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
          }
      }

      if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
        $rtl = true;
     else
        $rtl = false;
      $assign['YBC_BLOG_RTL_CLASS'] = $rtl ? 'ybc_blog_rtl_mode' : 'ybc_blog_ltr_mode'; 
      $assign['YBC_BLOG_SHOP_URI'] = _PS_BASE_URL_.__PS_BASE_URI__;  
      $fc = Tools::getValue('fc');
      $module = Tools::getValue('module');
      $controller = Tools::getValue('controller');
      $tabmanagament = Tools::getValue('tabmanagament');
      if($fc=='module' && $module=='ybc_blog' && $controller=='managementblog')
      {
            $this->context->smarty->assign('add_tmce',true);
      }
      $rating = (int)Tools::getValue('rating');
      $id_post = (int)Tools::getValue('id_post');
      $this->context->smarty->assign(
            array(
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT'),
                'allowGuestsComments' => (int)Configuration::get('YBC_BLOG_ALLOW_GUEST_COMMENT') ? true : false,
                'blogCommentAction' => $this->getLink('blog',array('id_post'=>(int)$id_post)),
                'hasLoggedIn' => $this->context->customer->isLogged(true), 
                'allow_report_comment' =>(int)Configuration::get('YBC_BLOG_ALLOW_REPORT') ? true : false,
                'display_related_products' =>(int)Configuration::get('YBC_BLOG_SHOW_RELATED_PRODUCTS') ? true : false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'default_rating' => (int)$rating > 0 && (int)$rating <=5 ? (int)$rating  :(int)Configuration::get('YBC_BLOG_DEFAULT_RATING'),
                'use_capcha' => (int)Configuration::get('YBC_BLOG_USE_CAPCHA') ? true : false,
                'use_facebook_share' => (int)Configuration::get('YBC_BLOG_ENABLE_FACEBOOK_SHARE') ? true : false,
                'use_google_share' => (int)Configuration::get('YBC_BLOG_ENABLE_GOOGLE_SHARE') ? true : false,
                'use_twitter_share' => (int)Configuration::get('YBC_BLOG_ENABLE_TWITTER_SHARE') ? true : false,                    
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_tags' => (int)Configuration::get('YBC_BLOG_SHOW_POST_TAGS') ? true : false,
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'enable_slideshow' => (int)Configuration::get('YBC_BLOG_ENABLE_POST_SLIDESHOW') ? true : false,
                'prettyAutoPlay' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                'show_author' => (int)Configuration::get('YBC_BLOG_SHOW_POST_AUTHOR') ? 1 : 0,
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')), 
                'blog_related_product_type' => Tools::strtolower(Configuration::get('YBC_RELATED_PRODUCTS_TYPE')),
                'blog_related_posts_type' => Tools::strtolower(Configuration::get('YBC_RELATED_POSTS_TYPE')),
                'blog_dir' => $this->blogDir,
                'image_folder' => _PS_YBC_BLOG_IMG_,
            )
      );
      $this->context->smarty->assign(array('blog_config' => $assign));
    }
    public function refreshCache()
    {
        $cached = Configuration::get('YBC_BLOG_NEED_CLEAR_CACHE');
        if(($cached && $cached!=date('ymd')) || $cached===false)
        {
            $this->_clearCache('*');
            Configuration::updateValue('YBC_BLOG_NEED_CLEAR_CACHE',date('ymd'));
        }
    }
    public function hookBlogSearchBlock()
    {
        if(($blog_search = trim(Tools::getValue('blog_search')))!='' && Validate::isCleanHtml($blog_search))
        {
            Tools::redirect($this->getLink('blog',array('search'=> urlencode($blog_search))));
        }
        $search = trim(Tools::getValue('search'));
        if(!Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK'))
            return '';
        $this->context->smarty->assign(
            array(
                'action' => $this->getLink('blog'),
                'search' => Validate::isCleanHtml($search) ? urldecode($search):'',
                'id_lang' => $this->context->language->id
            )
        );
        return $this->display(__FILE__, 'search_block.tpl');
    }
    public function hookBlogRssSideBar()
    {
        if(!$this->isCached('rss_block.tpl',$this->_getCacheId()))
        {
            $this->context->smarty->assign(
                array(
                    'url_rss' => $this->getLink('rss'),
                    'link_latest_posts' => $this->getLink('rss',array('latest_posts'=>1)),
                    'link_popular_posts' => $this->getLink('rss',array('popular_posts'=>1)),
                    'link_featured_posts' => $this->getLink('rss',array('featured_posts'=>1)),
                )
            );
        }
        return $this->display(__FILE__,'rss_block.tpl',$this->_getCacheId());
    }
    public function hookBlogComments()
    {
        if(!$this->isCached('comment_block.tpl',$this->_getCacheId()))
        {
            if(!Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK'))
                return '';
            $limit = Configuration::get('YBC_BLOG_COMMENT_NUMBER') ? (int)Configuration::get('YBC_BLOG_COMMENT_NUMBER'):20;
            $posts = Ybc_blog_comment_class::getCommentsWithFilter(' AND bc.approved=1','bc.id_comment DESC,',0,$limit);
            if($posts)
            {
                foreach($posts as &$post)
                {
                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                    if($post['thumb'])
                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                    $post['liked'] = $this->isLikedPost($post['id_post']);
                    if(!$post['name'] && $post['id_user'] && ($customer = new Customer($post['id_user'])) && Validate::isLoadedObject($customer))
                        $post['name'] = $customer->firstname.' '.$customer->lastname;
                    if($post['id_user'])
                    {
                        if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($post['id_user'])) && ($postUer = new Ybc_blog_post_employee_class($id)) && Validate::isLoadedObject($postUer) && $postUer->avata)
                        {
                            $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$postUer->avata);
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
                    'all_comment_link' => $this->getLink('comment'),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                    'comment_length' => (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH') ? (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH'):120,
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => 'comment_block',
                )
            );
        }
        return $this->display(__FILE__,'comment_block.tpl',$this->_getCacheId());
    }
    public function hookBlogPositiveAuthor()
    {
        if(!$this->isCached('positive_author.tpl',$this->_getCacheId()))
        {
            if(!Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK'))
                return '';
            $authors = Ybc_blog_post_class::getBlogPositiveAuthor();
            $this->context->smarty->assign(
                array(
                    'authors'=>$authors,
                    'author_link' => $this->getLink('author'),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => 'positive_author',
                )
            );
        }
        return $this->display(__FILE__,'positive_author.tpl',$this->_getCacheId());
    }
    public function hookBlogCategoriesBlock()
    {
        $id = (int)Tools::getValue('id_category');
        $module = Tools::getValue('module');
        if($id && $module==$this->name)
            $id_category = (int)$id;
        elseif(($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias);
        }
        elseif($id_post = (int)Tools::getValue('id_post'))
        {
            $post = new Ybc_blog_post_class($id_post);
            $id_category = $post->id_category_default;
        }
        elseif(($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias);
            if($id_post)
            {
                $post = new Ybc_blog_post_class($id_post);
                $id_category = $post->id_category_default;
            }
            else
                $id_category=0;
        }
        else
            $id_category=0;
        if(!$this->isCached('categories_block.tpl',$this->_getCacheId($id_category)))
        {
          if(!Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK'))
              return '';
          $this->context->smarty->assign(
              array(
                  'active' => $id_category,
                  'link_view_all'=> $this->getLink('category'),
              )
          );
          $blockCategTree = Ybc_blog_category_class::getBlogCategoriesTree(0);
          $this->context->smarty->assign('blockCategTree', $blockCategTree);
          $this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/category-tree-branch.tpl');
        }
        return $this->display(__FILE__, 'categories_block.tpl',$this->_getCacheId($id_category));
    }
    public function hookBlogTagsBlock()
    {
        if(!$this->isCached('tags_block.tpl',$this->_getCacheId()))
        {
            if(!Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK'))
                return '';
            $tags = Ybc_blog_post_class::getTags((int)Configuration::get('YBC_BLOG_TAGS_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_TAGS_NUMBER') : 20);
            if(is_array($tags) && $tags)
                shuffle($tags);
            $this->context->smarty->assign(
                array(
                    'tags' => $tags
                )
            );
        }
        return $this->display(__FILE__, 'tags_block.tpl',$this->_getCacheId());
    }
    public function hookBlogNewsBlock($params)
    {
        $page = isset($params['page']) ? $params['page'] : 'left';
        if(!$this->isCached('latest_posts_block.tpl',$this->_getCacheId($page)))
        {
            if(isset($params['page']) && $params['page']=='home')
            {
                if(!Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME'))
                    return '';
                $postCount = (int)Configuration::get('YBC_BLOG_LATEST_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_LATEST_POST_NUMBER_HOME') : 5;
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                    )
                );
            }
            else
            {
                if(!Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'))
                    return '';
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                    )
                );
                $postCount = (int)Configuration::get('YBC_BLOG_LATES_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_LATES_POST_NUMBER') : 5;
            }
            $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.datetime_active DESC,',0,$postCount);
            if($posts)
            {
                foreach($posts as $key => &$post)
                {
                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                    if($post['thumb'])
                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                    $post['liked'] = $this->isLikedPost($post['id_post']);
                    $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);

                }
                unset($key);
            }
            $this->context->smarty->assign(
                array(
                    'posts' => $posts,
                    'latest_link' => $this->getLink('blog',array('latest' => 'true')),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                    'hook' => 'homeblog',
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
                )
            );
        }
        return $this->display(__FILE__, 'latest_posts_block.tpl',$this->_getCacheId($page));
    }
    public function hookDisplayHome()
    {
        if(!$this->isCached('home_blocks.tpl',$this->_getCacheId()))
        {
            $homepages=array(
                'homepage_new'=>Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') ? $this->hookBlogNewsBlock(array('page'=>'home')):'',
                'homepage_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') ? $this->hookBlogPopularPostsBlock(array('page'=>'home')):'',
                'homepage_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') ? $this->hookBlogFeaturedPostsBlock(array('page'=>'home')):'',
                'homepage_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') ? $this->hookBlogCategoryBlock(array('page'=>'home')):'',
                'homepage_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') ? $this->hookBlogGalleryBlock(array('page'=>'home')):'',
            );
            $position_homepages= explode(',',Configuration::get('YBC_BLOG_POSITION_HOMEPAGE') ? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE'):'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
            $this->context->smarty->assign(
                array(
                    'position_homepages' => $position_homepages,
                    'homepages'=>$homepages
                )
            );
        }
        return $this->display(__FILE__, 'home_blocks.tpl',$this->_getCacheId());
    }
    public function getWidgetVariables($hookName, array $configuration = [])
    {
        if(!$this->isCached('home_blocks.tpl',$this->_getCacheId())) {
            $homepages = array(
                'homepage_new' => Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') ? $this->hookBlogNewsBlock(array('page' => 'home')) : '',
                'homepage_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') ? $this->hookBlogPopularPostsBlock(array('page' => 'home')) : '',
                'homepage_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') ? $this->hookBlogFeaturedPostsBlock(array('page' => 'home')) : '',
                'homepage_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') ? $this->hookBlogCategoryBlock(array('page' => 'home')) : '',
                'homepage_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') ? $this->hookBlogGalleryBlock(array('page' => 'home')) : '',
            );
            $position_homepages = explode(',', Configuration::get('YBC_BLOG_POSITION_HOMEPAGE') ? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE') : 'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
            $this->context->smarty->assign(
                array(
                    'position_homepages' => $position_homepages,
                    'homepages' => $homepages
                )
            );
            unset($hookName);
            unset($configuration);
        }
        return $this->display(__FILE__, 'home_blocks.tpl',$this->_getCacheId());
    }
    public function renderWidget($hookName, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if (preg_match('/^displayNav\d*$/', $hookName)) {
            $template_file = $this->templates['light'];
        } elseif ($hookName == 'displayLeftColumn') {
            $template_file = $this->templates['rich'];
        } else {
            $template_file = $this->templates['default'];
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/'.$template_file);
    }
    public function hookBlogPopularPostsBlock($params)
    {
        $page = isset($params['page']) && $params['page'] ? $params['page']:'';
        if(!$this->isCached('popular_posts_block.tpl',$this->_getCacheId($page)))
        {
            if($page=='home')
            {
                $postCount = (int)Configuration::get('YBC_BLOG_POPULAR_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_POPULAR_POST_NUMBER_HOME') : 5;
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                    )
                );
            }
            else
            {
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                    )
                );
                $postCount = (int)Configuration::get('YBC_BLOG_PUPULAR_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_PUPULAR_POST_NUMBER') : 5;
            }
            $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.click_number desc,',0,$postCount);
            if($posts)
                foreach($posts as &$post)
                {
                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                    if($post['thumb'])
                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                    $post['liked'] = $this->isLikedPost($post['id_post']);
                    $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                }
            $this->context->smarty->assign(
                array(
                    'posts' => $posts,
                    'popular_link' => $this->getLink('blog',array('popular' => 'true')),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => $page,
                )
            );
        }
        return $this->display(__FILE__, 'popular_posts_block.tpl',$this->_getCacheId($page));
    }
    public function hookBlogFeaturedPostsBlock($params)
    {
        $page = isset($params['page']) && $params['page'] ? $params['page']:'';
        if(!$this->isCached('featured_posts_block.tpl',$this->_getCacheId($page)))
        {
            if($page=='home')
            {
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                    )
                );
                $postCount = (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER_HOME') : 5;
            }
            else
            {
                $this->context->smarty->assign(
                    array(
                        'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                    )
                );
                $postCount = (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER') : 5;
            }
            $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 && p.is_featured=1',$this->sort,0,$postCount);
            if($posts)
                foreach($posts as &$post)
                {
                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                    if($post['thumb'])
                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                    $post['liked'] = $this->isLikedPost($post['id_post']);
                    $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                }
            $this->context->smarty->assign(
                array(
                    'posts' => $posts,
                    'featured_link' => $this->getLink('blog',array('featured' => 'true')),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
                )
            );
        }
        return $this->display(__FILE__, 'featured_posts_block.tpl',$this->_getCacheId($page));
    }
    public function hookBlogGalleryBlock($params)
    {
        $page = isset($params['page']) && $params['page'] ? $params['page']:'';
        if(!$this->isCached('gallery_block.tpl',$this->_getCacheId($page)))
        {
            if($page=='home')
            {
                if(!Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'))
                    return '';
                $postCount = (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER_HOME') : 10;
            }
            else
            {
                if(!Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK'))
                    return '';
                $postCount = (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER') : 10;
            }
            $galleries = Ybc_blog_gallery_class::getGalleriesWithFilter(' AND g.enabled=1  AND g.is_featured=1','g.sort_order asc, g.id_gallery asc,',0,$postCount);
            if($galleries)
                foreach($galleries as &$gallery)
                {
                    if($gallery['thumb'])
                        $gallery['thumb'] =  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/thumb/'.$gallery['thumb']);
                    else
                        $gallery['thumb']= $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']);
                    if($gallery['image'])
                    {
                        $gallery['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']);
                    }

                }
            $this->context->smarty->assign(
                array(
                    'galleries' => $galleries,
                    'gallery_link' => $this->getLink('gallery',array()),
                    'blog_page' => $page,
                )
            );
        }
        return $this->display(__FILE__, 'gallery_block.tpl',$this->_getCacheId($page));
    }
    public function displayCommentInfo($comment, $id_customer, $postLink)
    {
        if($id_customer)
        {
            if(version_compare(_PS_VERSION_, '1.7.6', '>='))
                {
                    $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
                	if (null !== $sfContainer) {
                		$sfRouter = $sfContainer->get('router');
                		$customerLink= $sfRouter->generate(
                			'admin_customers_view',
                			array('customerId' => $id_customer)
                		);
                	}
                    else
                        $customerLink = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id_customer.'&viewcustomer';
                }
                else
                    $customerLink = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id_customer.'&viewcustomer';
        }
        else
            $customerLink='#';
        $this->context->smarty->assign(array(
            'comment' => $comment,
            'customerLink' => $customerLink,
            'postLink' => $postLink,
        ));
        return $this->display(__FILE__,'comment_info.tpl');
    }
    public function hookModuleRoutes() {
        $subfix = (int)Configuration::get('YBC_BLOG_URL_SUBFIX') ? '.html' : '';
        $blogAlias = Configuration::get('YBC_BLOG_ALIAS',$this->context->language->id) ? : Configuration::get('YBC_BLOG_ALIAS',Configuration::get('PS_LANG_DEFAULT'));
        if(!$blogAlias)
            return array(
                'ybcblogsitemap' => array(
                    'controller' => 'sitemap',
                    'rule' => 'blog_sitemap.xml',
                    'keywords' => array(),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'ybc_blog',
                    ),
                ),
            );
        $routes = array(
            'ybcblogsitemap' => array(
                'controller' => 'sitemap',
                'rule' => 'blog_sitemap.xml',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorall' => array(
                'controller' => 'author',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorallpage' => array(
                'controller' => 'author',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'ybcblogmainpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'ybcblogfeaturedpostspage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),  
            'ybcblogpostcomment' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{id_post}-{edit_comment}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                    'edit_comment' => array('regexp' => '[0-9]+', 'param' => 'edit_comment'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),  
            'ybcblogpostallcomments' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/allcomments/{id_post}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'all_comment'=>1,
                ),
            ),   
            'ybcblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{id_post}-{url_alias}'.$subfix,
                'keywords' => array(
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),         
            'ybcblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{post_url_alias}'.$subfix,
                'keywords' => array(
                    'post_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'post_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpostpage2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{page}/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpostpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{page}/{category_url_alias}'.$subfix,
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'category_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'category_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{category_url_alias}'.$subfix,
                'keywords' => array(
                    'category_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'category_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorblogpostpage2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{page}/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer'=>1,
                ),
            ),
            'authorblogpostpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{page}/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer'=>1,
                ),
            ),
            'authorblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogtagpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$this->context->language->id)) ? $subAlias : 'tag').'/{tag}',
                'keywords' => array(
                    'tag'       =>   array('regexp' => '.+','param' => 'tag'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogtag' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$this->context->language->id)) ? $subAlias : 'tag').'/{tag}',
                'keywords' => array(
                    'tag'       =>   array('regexp' => '.+','param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloglatestpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$this->context->language->id)) ? $subAlias : 'latest').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => 'true'
                ),
            ),
            'categorybloglatest' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$this->context->language->id)) ? $subAlias : 'latest'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => 'true'
                ),
            ),
            'categoryblogpopulartpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' => 'true'
                ),
            ),
            'categoryblogpopular' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' => 'true'
                ),
            ),
            'categoryblogfeaturedpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' => 'true'
                ),
            ),
            'categoryblogfeatured' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' => 'true'
                ),
            ),
            'categoryblogsearchpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$this->context->language->id)) ? $subAlias : 'search').'/{search}',
                'keywords' => array(
                    'search'       =>   array('regexp' => '.+','param' => 'search'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogsearch' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$this->context->language->id)) ? $subAlias : 'search').'/{search}',
                'keywords' => array(
                    'search'       =>   array('regexp' => '.+','param' => 'search'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogyearpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$this->context->language->id)) ? $subAlias : 'year').'/{year}/{page}',
                'keywords' => array(
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogyear' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$this->context->language->id)) ? $subAlias : 'year').'/{year}',
                'keywords' => array(
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogmonthpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$this->context->language->id)) ? $subAlias : 'month').'/{month}/{year}/{page}',
                'keywords' => array(
                    'month'       =>   array('regexp' => '[0-9]+','param' => 'month'),
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogmonth' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$this->context->language->id)) ? $subAlias : 'month').'/{month}/{year}',
                'keywords' => array(
                    'month'       =>   array('regexp' => '[0-9]+','param' => 'month'),
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloggallerypage' => array(
                'controller' => 'gallery',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$this->context->language->id)) ? $subAlias : 'gallery').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloggallery' => array(
                'controller' => 'gallery',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$this->context->language->id)) ? $subAlias : 'gallery'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcommentspage' => array(
                'controller' => 'comment',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$this->context->language->id)) ? $subAlias : 'comments').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcategoriespage' => array(
                'controller' => 'category',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$this->context->language->id)) ? $subAlias : 'categories').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcomments' => array(
                'controller' => 'comment',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$this->context->language->id)) ? $subAlias : 'comments'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcategories' => array(
                'controller' => 'category',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$this->context->language->id)) ? $subAlias : 'categories'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrss' => array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrsscategories'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrssauthors2'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer' => 1,
                ),
            ),
            'categoryblogrssauthors'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrssalatest'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_LATEST',$this->context->language->id)) ? $subAlias : 'latest-posts'),
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => true,
                ),
            ),
            'categoryblogrsspopular'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular-posts'),
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' =>    true,
                ),
            ),
            'categoryblogrssfeatured'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured-posts'),
                'keywords' => array(
                
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' =>    true,
                ),
            ),
        );
        if(Configuration::get('PS_ROUTE_ybcblogmainpage')) {
            foreach($routes as $key => $r) {
                Configuration::deleteByName('PS_ROUTE_'.$key);
                unset($r);
            }
            
        }

        return $routes;
    }
    public function setMetas()
    {
        $meta = array();
        $module = Tools::getValue('module');
        if($module!='ybc_blog')
            return;
        $id_lang = $this->context->language->id;
        $id_category = (int)Tools::getValue('id_category');
        $id_post = (int)Tools::getValue('id_post');
        $controller = Tools::getValue('controller');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$id_lang);
        }
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$id_lang);
        }
        if($id_category)
        {
            if(($category  = new Ybc_blog_category_class($id_category,$this->context->language->id)) && Validate::isLoadedObject($category) )
            {
                $meta['meta_title'] = trim($category->meta_title) ? $category->meta_title : $category->title;
                if(trim($category->meta_description))
                    $meta['meta_description'] = $category->meta_description;
                else
                    $meta['meta_description'] = trim($category->description) ? Tools::substr(strip_tags($category->description),0,300):'';
                $meta['meta_keywords'] = $category->meta_keywords;
            }
            else
                $meta['meta_title'] = $this->l('Page not found');
                     
        }
        elseif($id_post)
        {
            if(($post = new Ybc_blog_post_class($id_post,$this->context->language->id)) && Validate::isLoadedObject($post) )
            {
                $meta['meta_title'] = trim($post->meta_title) ? $post->meta_title : $post->title;

                if(trim($post->meta_description))
                    $meta['meta_description'] = $post->meta_description;
                else
                    $meta['meta_description'] = trim($post->short_description) ? Tools::substr(strip_tags($post->short_description),0,300):Tools::substr(strip_tags($post->description),0,300);
                $meta['meta_keywords'] = $post->meta_keywords;
            }
            else
                $meta['meta_title'] = $this->l('Page not found');  
        }
        elseif(($tag = Tools::getValue('tag')) && Validate::isCleanHtml($tag))
        {
            $meta['meta_title'] = $this->l('Tag: ').' "'.$tag.'"';
        }  
        elseif(($latest = Tools::getValue('latest')) && Validate::isCleanHtml($latest))
        {
            $meta['meta_title'] = $this->l('Latest posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_LATEST',$id_lang));            
        }
        elseif(($featured = Tools::getValue('featured')) && Validate::isCleanHtml($featured))
        {
            $meta['meta_title'] = $this->l('Featured posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_FEATURED',$id_lang));            
        }
        elseif(($popular = Tools::getValue('popular')) && Validate::isCleanHtml($popular))
        {
            $meta['meta_title'] = $this->l('Popular posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_POPULAR',$id_lang));            
        }
        elseif(($search = Tools::getValue('search')) && Validate::isCleanHtml($search))
        {
            $meta['meta_title'] = $this->l('Search:').' "'.str_replace('+',' ',$search).'"';
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_SEARCH',$id_lang));            
                        
        } 
        elseif(($year = (int)Tools::getValue('year')) && ($month = (int)Tools::getValue('month')))
          $meta['meta_title'] = $this->l('Posted in :').' "'.$year.' - '.$this->getMonthName($month).'"';  
        elseif($year)
          $meta['meta_title'] = $this->l('Posted in :').' "'.$year.'"';  
        elseif($controller=='gallery')
        {
            $meta['meta_title'] = $this->l('Gallery');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_GALLERY',$id_lang));           
        }
        elseif($controller=='comment')
        {
            $meta['meta_title'] = $this->l('All comments');
        } 
        elseif($id_author = (int)Tools::getValue('id_author'))
        {

            $is_customer = (int)Tools::getValue('is_customer');
            if($employee = Ybc_blog_post_employee_class::getAuthorById($id_author,$is_customer))
            {

                $meta['meta_title'] = $this->l('Author: ').$employee['name'];
                $meta['meta_description'] = strip_tags($employee['description']);
            }
            else
                $meta['meta_title'] = $this->l('Page not found');
                
        } 
        elseif($controller=='author')
        {
            $meta['meta_title'] = $this->l('Authors');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_AUTHOR',$id_lang));  
        }
        elseif($controller=='category')
        {
            $meta['meta_title'] = $this->l('All categories');
            $meta['meta_description'] = Configuration::get('YBC_BLOG_SEO_CATEGORIES',$id_lang) ? strip_tags(Configuration::get('YBC_BLOG_SEO_CATEGORIES',$id_lang)):'';                        
                        
        }
        elseif($controller=='rss')
        {
            $meta['meta_title']= $this->l('RSS');
        }
        elseif($controller=='managementblog')
        {
            $meta['meta_title'] = $this->l('My blog posts');
        }
        elseif($controller=='managementcomments')
        {
            $meta['meta_title'] = $this->l('My blog comments');
        }
        elseif($controller=='managementmyinfo')
        {
            $meta['meta_title']= $this->l('My blog info');
        }
        elseif($controller=='blog')
        {
            if($id_author = (int)Tools::getValue('id_author'))
            {
                $is_customer = (int)Tools::getValue('is_customer');
                if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_author,$is_customer)) && ($employeePost = new Ybc_blog_post_employee_class($id,$this->context->language->id)) && Validate::isLoadedObject($employeePost))
                {
                    $meta['meta_title'] = $this->l('Author').' '.$employeePost->name;
                    $meta['meta_description'] = $employeePost->description;
                                    
                }                
            }
            else
            {
                $meta['meta_title'] = Configuration::get('YBC_BLOG_META_TITLE',$id_lang);
                $meta['meta_description'] = Configuration::get('YBC_BLOG_META_DESCRIPTION',$id_lang);
                $meta['meta_keywords'] = Configuration::get('YBC_BLOG_META_KEYWORDS',$id_lang);
            }
            
        }
        if(!isset($meta['meta_title']))
            $meta['meta_title']='';
        if(!isset($meta['meta_description']))
            $meta['meta_description']='';
        if(!isset($meta['meta_keywords']))
            $meta['meta_keywords']='';
        if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
            $rtl = true;
        else
            $rtl = false;            
        if($this->is17)
        {
            $body_classes = array(
                'lang-'.$this->context->language->iso_code => true,
                'lang-rtl' => (bool) $this->context->language->is_rtl,
                'country-'.$this->context->country->iso_code => true,                                   
                'ybc_blog' => true,
                'ybc_blog_rtl' => $rtl,
            );
            $page = array(
                'title' => '',
                'canonical' => '',
                'meta' => array(
                    'title' => $meta['meta_title'],
                    'description' => $meta['meta_description'],
                    'keywords' => $meta['meta_keywords'],
                    'robots' => 'index',
                ),
                'page_name' => 'ybc_blog_page',
                'body_classes' => $body_classes,
                'admin_notifications' => array(),
            ); 
            $this->context->smarty->assign(array('page' => $page));
        }    
        else
        {
            $this->context->smarty->assign($meta);
            if($rtl) 
                $this->context->smarty->assign(array(
                    'body_classes' => array('ybc_blog_rtl'),
                ));
        }                
    }
    public function getBreadCrumb()
    {
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias);
        }
        $id_category = (int)Tools::getValue('id_category');
        if(!$id_category && ($category_url_alias= Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias);
        }
        $id_author = (int)Tools::getValue('id_author');
        $is_customer= (int)Tools::getValue('is_customer');
        $nodes = array();
        $controller = Tools::getValue('controller');
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $this->l('Blog'),
            'url' => $this->getLink('blog')
        );
        if($controller=='category')
        {
            $nodes[] = array(
                'title' => $this->l('All categories'),
                'url' => $this->getLink('category')
            );
        }
        if($controller=='comment')
        {
            $nodes[] = array(
                'title' => $this->l('All Comments'),
                'url' => $this->getLink('comment')
            );
        }
        if($id_category && $category = Ybc_blog_category_class::getCategoryById($id_category))
        {
            $nodes[] = array(
                'title' => $category['title'],
                'url' => $this->getLink('blog',array('id_category' => $id_category)),                   
            );
        }
        if($id_author && $author = Ybc_blog_post_employee_class::getAuthorById($id_author,$is_customer))
        {
            $nodes[] = array(
                'title' => $this->l('Authors'),
                'url' => $this->getLink('author'),               
            );
            $nodes[] = array(
                'title' => trim(Tools::ucfirst($author['name'])),
                'url' => $this->getLink('blog',array('id_author' => $id_author)),               
            );
        }
        elseif($controller=='author')
        {
             $nodes[] = array(
                    'title' => $this->l('Authors'),
                    'url' => $this->getLink('author'),               
             );
        }
        if($id_post && ($post = new Ybc_blog_post_class($id_post,$this->context->language->id)) && Validate::isLoadedObject($post))
        {
            if($post->id_category_default)
                $id_category_default= $post->id_category_default;
            else
            {
                $id_category_default = Ybc_blog_post_class::getFirstCategory($post->id);
            }
            if($id_category_default && ($category = new Ybc_blog_category_class($id_category_default,$this->context->language->id)) && Validate::isLoadedObject($category) )
            {
                $nodes[] = array(
                    'title' => $category->title,
                    'url' => $this->getLink('blog',array('id_category' => $category->id)),
                );
            }
            $nodes[] = array(
                'title' => $post->title,
                'url' => $this->getLink('blog',array('id_post' => $id_post)),                   
            );
        }
        if($controller=='rss')
        {
            $nodes[] = array(
                'title' => $this->l('Rss'),
                'url' => $this->getLink('rss'),                   
            );
        }
        if($controller == 'gallery')
        {
            $nodes[] = array(
                'title' => $this->l('Gallery'),
                'url' => $this->getLink('gallery'),                   
            );
        }
        if($controller == 'blog' && ($latest = Tools::getValue('latest')) && Validate::isCleanHtml($latest))
        {
            $nodes[] = array(
                'title' => $this->l('Latest posts'),
                'url' => $this->getLink('blog',array('latest' => true)),                   
            );
        }
        if($controller == 'blog' && ($popular = Tools::getValue('popular')) && Validate::isCleanHtml($popular))
        {
            $nodes[] = array(
                'title' => $this->l('Popular posts'),
                'url' => $this->getLink('blog',array('popular' => true)),                   
            );
        }
        if($controller == 'blog' && ($featured = Tools::getValue('featured')) && Validate::isCleanHtml($featured))
        {
            $nodes[] = array(
                'title' => $this->l('Featured posts'),
                'url' => $this->getLink('blog',array('featured' => true)),                   
            );
        }
        if($controller == 'blog' && ($tag = Tools::getValue('tag')) && Validate::isCleanHtml($tag))
        {
            $nodes[] = array(
                'title' => $this->l('Blog tag').': '.$tag,
                'url' => $this->getLink('blog',array('tag' => $tag)),                    
            );
        }
        if($controller == 'blog' && ($search = Tools::getValue('search')) && Validate::isCleanHtml($search))
        {
            $nodes[] = array(
                'title' => $this->l('Blog search').': '.str_replace('+',' ',$search),
                'url' => $this->getLink('blog',array('search' => $search)),                     
            );
        }
        $year = (int)Tools::getValue('year');
        $month = (int)Tools::getValue('month');
        if($controller == 'blog' && $month && $year)
        {
            $nodes[] = array(
                'title' => $month.'-'.$year,
                'url' => $this->getLink('blog',array('month' => $month,'year'=>$year)),                     
            );
        }
        elseif($controller == 'blog' && $year)
        {
            $nodes[] = array(
                'title' =>$year,
                'url' => $this->getLink('blog',array('year'=>$year)),                     
            );
        }
        if($this->is17)
            return array('links' => $nodes,'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }
    public function displayBreadcrumb($nodes)
    {
        $this->smarty->assign(array('nodes' => $nodes));
        return $this->display(__FILE__, 'nodes.tpl');
    }
    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        if(!($blogTabId = Tab::getIdFromClassName('AdminYbcBlog')))
        {
            $tab = new Tab();
            $tab->class_name = 'AdminYbcBlog';
            $tab->module = 'ybc_blog';
            $tab->id_parent = 0;
            foreach($languages as $lang){
                $tab->name[$lang['id_lang']] = ($text_lang = $this->getTextLang('Blog',$lang)) ? $text_lang : $this->l('Blog');
            }
            $tab->save();
            $blogTabId = $tab->id;
        }
        if($blogTabId)
        {
            $tabs = Ybc_blog_defines::getInstance()->getSubTabs();
            foreach($tabs as $tabArg)
            {
                if(!Tab::getIdFromClassName($tabArg['class_name']))
                {
                    $tab = new Tab();
                    $tab->class_name = $tabArg['class_name'];
                    $tab->module = 'ybc_blog';
                    $tab->id_parent = $blogTabId; 
                    $tab->icon=$tabArg['icon'];             
                    foreach($languages as $lang){
                            $tab->name[$lang['id_lang']] = ($text_lang = $this->getTextLang($tabArg['tabname'],$lang,'ybc_blog_defines')) ? $text_lang : $tabArg['tab_name'];
                    }
                    $tab->save();
                }
            }                
        }            
        return true;
    }
    private function _uninstallTabs()
    {
        $tabs = Ybc_blog_defines::getInstance()->getSubTabs();
        foreach($tabs as $tab)
        {
            if($tabId = Tab::getIdFromClassName($tab['class_name']))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }
        if($tabId = Tab::getIdFromClassName('AdminYbcBlog'))
        {
            $tab = new Tab($tabId);
            if($tab)
                $tab->delete();
        }
        return true;
    }
    public function getInternalStyles()
    {
        if(!file_exists(dirname(__FILE__).'/views/css/custom.css'))
        {
            $this->refreshCssCustom();
        }
        $this->context->controller->addCSS($this->_path.'views/css/custom.css');
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        $controller = Tools::getValue('controller');
        if($fc=='module' && $module=='ybc_blog')
        {
            $id_category = (int)Tools::getValue('id_category');
            $id_post = (int)Tools::getValue('id_post');
            if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
            {
                $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$this->context->language->id);
            }
            if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias) )
            {
                $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$this->context->language->id);
            }
            if($id_category)
                $current_link= $this->getLink($controller,array('id_category' => $id_category));
            elseif($id_post)
                $current_link= $this->getLink($controller,array('id_post' => $id_post));
            elseif($id_author=(int)Tools::getValue('id_author'))
                $current_link=$this->getLink($controller,array('id_author'=>$id_author));
            elseif(($tag=Tools::getValue('tag')) && Validate::isCleanHtml($tag))
                $current_link=$this->getLink($controller,array('tag'=>$tag));
            elseif(($search=Tools::getValue('search')) && Validate::isCleanHtml($search))
                $current_link=$this->getLink($controller,array('search'=>$search));
            elseif(($latest=Tools::getValue('latest')) && Validate::isCleanHtml($latest))
                $current_link=$this->getLink($controller,array('latest'=>$latest));
            else
                $current_link=$this->getLink($controller);
        }
        $this->smarty->assign(
            array(
                'link_current'=>isset($current_link) ? $current_link:false,
                'baseAdminDir' => __PS_BASE_URI__.'/',
                'url_path' => $this->_path,
                'ybc_blog_product_category' => isset($id_category) ? $id_category :0,
            )
        );
        if(isset($id_post) && $id_post && $module==$this->name && $controller=='blog')
        {
            $post = $this->getPostById($id_post);
            if($post)
            {
                $post['img_name'] = isset($post['image']) ? $post['image'] : '';
                if($post['image'])
                    $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['link'] = $this->getLink('blog',array('id_post'=>$post['id_post']));
                $this->context->smarty->assign(
                    array(
                        'blog_post_header'=>$post,
                    )
                );
            }
        }
        $this->context->smarty->assign(
            array(
                'YBC_BLOG_CAPTCHA_TYPE' => Configuration::get('YBC_BLOG_CAPTCHA_TYPE'),
                'YBC_BLOG_CAPTCHA_SITE_KEY' => Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' ? Configuration::get('YBC_BLOG_CAPTCHA_SITE_KEY') : Configuration::get('YBC_BLOG_CAPTCHA_SITE_KEY3'),
            )
        );
        return $this->display(__FILE__, 'head.tpl');
    }
    public function checkProfileEmployee($id_employee,$profile)
    {
        $employee = new Employee($id_employee);
        if($employee->id_profile==1)
            return true;
        $id_employee_post=  (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false,true);
        if($id_employee_post)
        {
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            if($employeePost->profile_employee)
            {
                $profiles = explode(',',$employeePost->profile_employee);
                if(in_array('All tabs',$profiles) || in_array($profile,$profiles))
                    return true;
                else
                    return false;
            }
        }
        return false;
    }
    public function hookDisplayFooterProduct()
    {
        $id_product = (int)Tools::getValue('id_product');
        if(!$this->isCached('product-post.tpl',$this->_getCacheId($id_product)))
        {
            if(!Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE'))
                return '';
            $limit = (int)Configuration::get('YBC_BLOG_NUMBER_POST_IN_PRODUCT') > 0 ? (int)Configuration::get('YBC_BLOG_NUMBER_POST_IN_PRODUCT') : 5;
            $posts= Ybc_blog_post_class::getPostsByIdProduct($id_product,$limit);
            if($posts)
            {
                foreach($posts as &$rpost)
                    if($rpost['image'])
                    {
                        $rpost['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);
                        if($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                        else
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);
                        $rpost['link'] =   $this->getLink('blog',array('id_post'=>$rpost['id_post']));
                        $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                        $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                        $rpost['liked'] = $this->isLikedPost($rpost['id_post']);
                    }
                    else
                    {
                        $rpost['image'] = '';
                        if($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                        else
                            $rpost['thumb'] = '';
                        $rpost['link'] =   $this->getLink('blog',array('id_post'=>$rpost['id_post']));
                        $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                        $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                        $rpost['liked'] = $this->isLikedPost($rpost['id_post']);
                    }
            }
            $this->context->smarty->assign(
                array(
                    'posts'=>$posts,
                    'image_folder' => _PS_YBC_BLOG_IMG_,
                    'display_desc' => Configuration::get('YBC_BLOG_PRODUCT_PAGE_DISPLAY_DESC'),
                    'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                    'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                    'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                )
            );
        }
        return $this->display(__FILE__,'product-post.tpl',$this->_getCacheId($id_product));
    }
    public function displayBlogCategoryTre($blockCategTree,$selected_categories,$name='',$disabled_categories=array())
    {
        if($id_post = (int)Tools::getValue('id_post'))
        {
            $post = new Ybc_blog_post_class($id_post);
            $id_category_default= $post->id_category_default;
        }
        else
            $id_category_default=0;
        $this->context->smarty->assign(
            array(
                'blockCategTree'=> $blockCategTree,
                'branche_tpl_path_input'=> _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/category-tree-blog.tpl',
                'selected_categories'=>$selected_categories,
                'disabled_categories' => $disabled_categories,
                'id_category_default' => (int)Tools::getValue('main_category',$id_category_default) ,
                'input_name'=>$name ? $name :'blog_categories',
            )
        );
        return $this->display(__FILE__, 'categories_blog.tpl');
    }
    public function hookBlogArchivesBlock()
    {
        if(!$this->isCached('block_archives.tpl',$this->_getCacheId()))
        {
            $this->context->smarty->assign(
                array(
                    'years'=>Ybc_blog_post_class::getBlogArchives(),
                )
            );
        }
        return $this->display(__FILE__,'block_archives.tpl',$this->_getCacheId());
    }
    public function getMonthName($month)
    {
        switch ($month) {
            case 1:
                return $this->l('January');
            case 2:
                return $this->l('February');
            case 3:
                return $this->l('March');
            case 4:
                return $this->l('April');
            case 5:
                return $this->l('May');
            case 6:
                return $this->l('June');
            case 7:
                return $this->l('July');
            case 8:
                return $this->l('August');
            case 9:
                return $this->l('September');
            case 10:
                return $this->l('October');
            case 11:
                return $this->l('November');
            case 12:
                return $this->l('December');
        }
        return '';
    }
    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
    }
    public function hookCustomerAccount()
    {
        $this->context->smarty->assign(
            array(
                'author'=> Ybc_blog_post_employee_class::checkGroupAuthor(),
                'path_module'=> $this->_path,
                'link' => $this->context->link,
                'suppened' =>(int)Ybc_blog_post_employee_class::getIdEmployeePostById((int)$this->context->customer->id,true,true) || !(int)Ybc_blog_post_employee_class::getIdEmployeePostById((int)$this->context->customer->id,true) ? false :true,
            )
        );
        if($this->is17)
    	   return $this->display(__FILE__, 'my-account.tpl');
        else
            return $this->display(__FILE__, 'my-account16.tpl');
    }
    public function hookDisplayMyAccountBlock()
    {
    	return $this->hookCustomerAccount();
    }
    public function sendMailRepyCustomer($id_comment,$replier,$comment_reply=''){
        $comment = new Ybc_blog_comment_class($id_comment);
        if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email,true)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
            $id_lang = $customer->id_lang;
        else
            $id_lang = $this->context->language->id;
        if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('admin_reply_comment_to_customer',$id_lang)))
        {
            $post = new Ybc_blog_post_class($comment->id_post,$id_lang);
            $reply_comment_text = Tools::getValue('reply_comment_text');
            $template_reply_comment=array(
                '{customer_name}' => $comment->name,
                '{customer_email}' => $comment->email,
                '{comment}' =>$comment->comment,
                '{comment_reply}' => $comment_reply ? $comment_reply : (Validate::isCleanHtml($reply_comment_text) ?  $reply_comment_text :''),
                '{post_link}' => $this->getLink('blog',array('id_post'=>$comment->id_post)),
                '{post_title}'=>$post->title,
                '{replier}' => $replier,
                '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
            );
            Mail::Send(
    			$id_lang,
    			'admin_reply_comment_to_customer',
    			$subject,
    			$template_reply_comment,
		        $comment->email,
    			$comment->name,
    			null,
    			null,
    			null,
    			null,
    			dirname(__FILE__).'/mails/'
            );
        }
    }
    public function sendMailReplyAdmin($id_comment,$replier,$approved=1,$comment_reply=''){
        $comment = new Ybc_blog_comment_class($id_comment);
        $post_class = new Ybc_blog_post_class($comment->id_post);
        if($post_class->is_customer && ($id_customer= $post_class->added_by))
        {
            $author= new Customer($id_customer);
            $link_view_comment= $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','list'=>1));
        }
        else
        {
            $author = new Employee($post_class->added_by);
            $link_view_comment= $this->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
        }
        $id_lang = $author->id_lang;
        if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('customer_reply_comment_to_admin_'.$approved,$id_lang)))
        {
            $post_class = new Ybc_blog_post_class($comment->id_post,$id_lang);
            $reply_comment_text = Tools::getValue('reply_comment_text');
            $template_reply_comment=array(
                '{customer_name}' => $comment->name, 
                '{customer_email}' => $comment->email,
                '{comment}' =>$comment->comment,
                '{comment_reply}' => $comment_reply ? $comment_reply : (Validate::isCleanHtml($reply_comment_text) ? $reply_comment_text :''),
                '{post_title}' => $post_class->title,
                '{replier}'=>$replier,
                '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER'),
                '{post_link}' => $this->getLink('blog',array('id_post'=>$post_class->id)),
            );
            
            if($author->id)
            {
                $template_reply_comment['{author_name}'] = $author->firstname.' '.$author->lastname;
                $template_reply_comment['{link_view_comment}'] = $link_view_comment;
                Mail::Send(
        			$id_lang,
        			'customer_reply_comment_to_admin_'.$approved,
        			str_replace('[post_title]',$post_class->title,$subject),
        			$template_reply_comment,
    		        $author->email,
        			$author->firstname.' '.$author->lastname,
        			null,
        			null,
        			null,
        			null,
        			dirname(__FILE__).'/mails/'
                );
            }
            if($emails= explode(',',Configuration::get('YBC_BLOG_ALERT_EMAILS')))
            {
                $link_view_comment= $this->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
                foreach($emails as $email)
                {
                    $template_reply_comment['{author_name}'] = Configuration::get('PS_SHOP_NAME');
                    $template_reply_comment['{link_view_comment}'] = $link_view_comment;
                    if(Validate::isEmail($email))
                        Mail::Send(
            			Context::getContext()->language->id,
            			'customer_reply_comment_to_admin_'.$approved,
            			$this->l('A customer has replied to a comment on ').$post_class->title,
            			$template_reply_comment,
        		        $email,
            			Configuration::get('PS_SHOP_NAME'),
            			null,
            			null,
            			null,
            			null,
            			dirname(__FILE__).'/mails/'
                    );
                }
            }
        }
    }
    public function getThumbCategory($id_category,&$thumb,&$lever)
    {
        $category = new Ybc_blog_category_class($id_category,$this->context->language->id);
        if($lever>=1)
            $thumb = ' > '.$this->displayText($category->title,'a',null,null,$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true&id_parent='.(int)$category->id).$thumb;
        else
            $thumb = ' > '.$category->title.$thumb;
        $lever++;
        if($category->id_parent)
            $this->getThumbCategory($category->id_parent,$thumb,$lever);
        return $thumb;
    }
    public function hookBlogCategoryBlock($params)
    {
        if(!$this->isCached('categories_home_block.tpl',$this->_getCacheId()))
        {
            if(Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'))
            {
                if(($ids = Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME')) && $limit=(int)Configuration::get('YBC_BLOG_CATEGORY_POST_NUMBER_HOME'))
                {
                    $categoires = Ybc_blog_category_class::getCategoriesWithFilter(' AND c.id_category IN ('.implode(',',array_map('intval',explode(',',$ids))).')',false,false,false,false);
                    if($categoires)
                    {
                        foreach($categoires as &$category)
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
                            $posts= Ybc_blog_post_class::getPostsWithFilter(" AND p.enabled=1 AND pc.id_category= '".(int)$category['id_category']."'",$sort,0,$limit);
                            if($posts)
                            {
                                foreach($posts as $key => &$post)
                                {
                                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                                    if($post['thumb'])
                                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                                    $post['liked'] = $this->isLikedPost($post['id_post']);
                                    $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);

                                }
                                unset($key);
                            }
                            $category['posts'] = $posts;
                            $category['link_all'] = $this->getLink('blog',array('id_category'=>$category['id_category']));
                        }
                    }
                    if($categoires)
                    {
                        $this->context->smarty->assign(
                            array(
                                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                                'hook' => 'homeblog',
                                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                                'blog_page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
                                'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                                'categoires' => $categoires,
                            )
                        );

                    }
                }
            }
        }
        return $this->display(__FILE__,'categories_home_block.tpl',$this->_getCacheId());
    }
    public function getDevice()
    {
      return ($userAgent = new Ybc_browser())? $userAgent->getBrowser().' '.$userAgent->getVersion().' '.$userAgent->getPlatform() : $this->l('Unknown');
    }
    public function isLikedPost($id_post)
    {
        if($this->context->customer->logged)
        {
            if(Ybc_blog_comment_class::checkCustomerIsLikePost($id_post))
            {
                return true;
            }
        }
        if(!$this->context->cookie->liked_posts)
            $likedPosts = array();
        else
            $likedPosts = @unserialize($this->context->cookie->liked_posts);
        
        if(is_array($likedPosts) && in_array($id_post, $likedPosts))
            $likedPost = true;
        else
            $likedPost = false;
        return $likedPost;
    }
    public function hookDisplayFooterCategory()
    {
        $id_category = (int)Tools::getValue('id_category');
        return $this->displayPostRelatedCategories($id_category);
    }
    public function displayPostRelatedCategories($id_category)
    {
        if(!$this->isCached('related_posts_category.tpl',$this->_getCacheId($id_category)))
        {
            if(!Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE') || !$id_category)
                return '';
            $limit = (int)Configuration::get('YBC_BLOG_NUMBER_POST_IN_CATEGORY');
            $posts= Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 AND rpc.id_category='.(int)$id_category,$this->sort,$limit ? 0 : false,$limit,false);
            if($posts)
                foreach($posts as &$post)
                {
                    $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                    if($post['thumb'])
                        $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                    $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                    $post['liked'] = $this->isLikedPost($post['id_post']);
                    $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                }
            $this->context->smarty->assign(
                array(
                    'posts' => $posts,
                    'display_desc' => Configuration::get('YBC_BLOG_CATEGORY_PAGE_DISPLAY_DESC'),
                    'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                    'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                    'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                    'sidebar_post_type' => Configuration::get('YBC_BLOG_CATEGORY_POST_TYPE'),
                    'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                    'blog_page' => 'home',
                )
            );
        }
        return $this->display(__FILE__, 'related_posts_category.tpl',$this->_getCacheId($id_category));
    }
    public function displayError($error)
    {
        if($error)
        {
            $this->context->smarty->assign(
                array(
                    'errors_blog'=>$error
                )
            );
            return $this->display(__FILE__,'errors.tpl');
        }
        return '';
    }
    public function hookDisplayBackOfficeFooter()
    {
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        $this->context->smarty->assign(
            array(
                'link_ajax' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
            )
        );
        return $this->display(__FILE__,'admin_footer.tpl');
    }
    
    public function hookDisplayFooterYourAccount(){
        $this->context->smarty->assign(
            array(
                'is_17' => $this->is17,
                'my_account_link' => $this->context->link->getPageLink('my-account',Configuration::get('PS_SSL_ENABLED'),$this->context->language->id),
                'home_link' => $this->context->link->getPageLink('index',Configuration::get('PS_SSL_ENABLED'),$this->context->language->id),
            )
        );
        return $this->display(__FILE__,'your_account_footer.tpl');
    }
    public function redirect($url)
    {
        Tools::redirect($url);
    }
    public static function checkIframeHTML($content)
    {
        if(!Configuration::get('PS_ALLOW_HTML_IFRAME') && (Tools::strpos($content,'<iframe')!==false || Tools::strpos($content,'<source')!==false) )
            return false;
        else
            return true;
    }
    public static function checkIsLinkRewrite($link)
    {
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
            return preg_match(Tools::cleanNonUnicodeSupport('/^[_a-zA-Z\x{0600}-\x{06FF}\pL\pS-]{1}[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]+$/u'), $link);
        }
        return preg_match('/^[_a-zA-Z\-]{1}[_a-zA-Z0-9\-]+$/', $link);
    }
    public function hookActionObjectLanguageAddAfter()
    {
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_category_lang',$this->context->shop->id,'id_category');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_employee_lang',$this->context->shop->id,'id_employee_post');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_gallery_lang',$this->context->shop->id,'id_gallery');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_post_lang',$this->context->shop->id,'id_post');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_slide_lang',$this->context->shop->id,'id_slide');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_email_template_lang',$this->context->shop->id,'id_ybc_blog_email_template');
       $this->_copyForderMail();
    }
    public function createNewFileName($dir,$name)
    {
        $i=1;
        $file_name = $name;
        while(file_exists($dir.$file_name))
        {
            $file_name =$i.'-'.$name;
            $i++;
        }
        return $file_name;
    }
    public function getTextLang($text, $lang, $file = '')
    {
        $modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->name;
        $fileTransDir = $modulePath . '/translations/' . $lang['iso_code'] . '.' . 'php';
        if (!@file_exists($fileTransDir)) {
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $strMd5 = md5($text);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file ?: $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if ($matches && isset($matches[2])) {
            return $matches[2];
        }
        return $text;
    }
    public function getLanguageLink($idLang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $controller = Dispatcher::getInstance()->getController();
        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }
        $params = Tools::getAllValues();
        if(isset($params['controller']))
            unset($params['controller']);
        if(isset($params['id_lang']))
            unset($params['id_lang']);
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$context->language->id);
        }
        if($id_post)
            $params['id_post'] = $id_post;
        $id_category = (int)trim(Tools::getValue('id_category'));
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$context->language->id);
        }
        if($id_category)
            $params['id_category'] = $id_category;
        return $this->getLink($controller,$params,$idLang);
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if($array)
        {
            if(!is_array($array))
            return false;
            if(method_exists('Validate',$validate))
            {
                if($array && is_array($array))
                {
                    $ok= true;
                    foreach($array as $val)
                    {
                        if(!is_array($val))
                        {
                            if($val && !Validate::$validate($val))
                            {
                                $ok= false;
                                break;
                            }
                        }
                        else
                            $ok = self::validateArray($val,$validate);
                    }
                    return $ok;
                }
            }
        }
        return true;
    }
    public function initEmailTemplate($default=true)
    {
         return Ybc_blog_email_template_class::getInstance()-> initEmailTemplate($default);
    }
    public function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $text ='';
        if($tag)
        {
            $text .= '<'.$tag.($class ? ' class="'.$class.'"':'').($id ? ' id="'.$id.'"':'');
            if($href)
                $text .=' href="'.$href.'"';
            if($blank && $tag ='a')
                $text .=' target="_blank"';
            if($src)
                $text .=' src ="'.$src.'"';
            if($name)
                $text .=' name="'.$name.'"';
            if($value)
                $text .=' value ="'.$value.'"';
            if($type)
                $text .= ' type="'.$type.'"';
            if($data_id_product)
                $text .=' data-id_product="'.(int)$data_id_product.'"';
            if($rel)
                $text .=' rel="'.$rel.'"';
            if($attr_datas)
            {
                foreach($attr_datas as $data)
                {
                    $text .=' '.$data['name'].'='.'"'.$data['value'].'"';
                }
            }
            if($tag=='img' || $tag=='br' || $tag=='input')
                $text .='/>';
            else
                $text .='>';
            if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
                $text .= $content;
            if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
                $text .= '<'.'/' . $tag . '>';
            return $text;
        }
    }
    public function displayPaggination($limit,$name)
    {
        if($name)
        {
            $this->context->smarty->assign(
                array(
                    'limit' => $limit,
                    'pageName' => $name,
                )
            );
            return $this->display(__FILE__,'limit.tpl');
        }
    }
    public function getFieldsMailTemplateValues($template)
    {
        $languages = Language::getLanguages();
        $subject = array();
        $content_html = array();
        $content_txt = array();
        $theme = (version_compare(_PS_VERSION_, '1.7', '>=') ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme());
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/ybc_blog/mails/',
            $this->getLocalPath() . 'mails/',
        );
        foreach($languages as $language)
        {
            $id_lang = (int)$language['id_lang'];
            $subject[$id_lang] =  isset($template->subject[$id_lang]) ? $template->subject[$id_lang]:'';
            foreach ($basePathList as $path) {
                $flag = false;
                $iso_path = $path . $language['iso_code'] . '/' . $template->template;
                if (@file_exists($iso_path . '.html')) {
                    $content_html[$id_lang] = Tools::getValue('content_html_'.$id_lang,Tools::file_get_contents($iso_path . '.html')) ;
                    $flag = true;
                }
                if (@file_exists($iso_path . '.txt')) {
                    $content_txt[$id_lang] = Tools::getValue('content_txt_'.$id_lang,Tools::file_get_contents($iso_path . '.txt'));
                }
                if ($flag)
                    break;
            }
            if(!isset($content_html[$id_lang]))
                $content_html[$id_lang]='';
            if(!isset($content_txt[$id_lang]))
                $content_txt[$id_lang]='';
        }
        $fields = array();
        $fields['subject'] = $subject;
        $fields['active'] = $template->active;
        $fields['id_ybc_blog_email_template'] = $template->id;
        $fields['content_html'] = $content_html;
        $fields['content_txt'] = $content_txt;
        return $fields;
    }
    public function displayListTemplateChatGPT()
    {
        $fields_list = array(
            'title' => array(
                'title' => $this->l('Label'),
                'type' => 'text',
                'sort' => false,
                'filter' => false,
            ),
            'content' => array(
                'title' => $this->l('Content'),
                'type' => 'text',
                'sort' => false,
                'filter' => false,
            ),
        );
        $totalRecords = (int)Ybc_chatgpt::countTemplatesWithFilter(false);
        $templates = Ybc_chatgpt::getTemplatesWithFilter(false, null, 0, false);
        $listData = array(
            'name' => 'ybc_chatgpt',
            'actions' => array('edit' ,'delete_gpt'),
            'currentIndex' => $this->context->link->getAdminLink('AdminYbcBlogSetting', true).'&control=config&current_tab=chatgpt',
            'identifier' => 'id_ybc_chatgpt_template',
            'show_toolbar' => false,
            'show_action' => true,
            'title' => $this->l('Prompt templates'),
            'fields_list' => $fields_list,
            'field_values' => $templates,
            'paggination' => '',
            'filter_params' => $this->getFilterParams($fields_list),
            'show_reset' => false,
            'totalRecords' => $totalRecords,
            'show_add_new' => true,
            'sort'=> '',
            'sort_type'=> '',
        );
        return $this->renderList($listData);
    }
    public function displayFormChatGPT()
    {
        $this->context->smarty->assign(
            array(
                'languages' => $this->context->controller->getLanguages(),
                'defaultFormLanguage' => Configuration::get('PS_LANG_DEFAULT'),
                'gpt_templates' =>  Ybc_chatgpt::getAllTemplate(),
                'chatgpt_messages' => ($messages = Ybc_chatgpt_message::getMessages()) ?  array_reverse($messages) : array(),
            )
        );
        return $this->display(__FILE__,'form_chatgpt.tpl');
    }
    public function _getCacheId($params = null)
    {
        $cacheId = $this->getCacheId($this->name);
        $cacheId = str_replace($this->name, '', $cacheId);
        $suffix ='';
        if($params)
        {
            if(is_array($params))
                $suffix .= '|'.implode('|',$params);
            else
                $suffix .= '|'.$params;
        }
        return $this->name . $suffix . $cacheId.'|'.date('ymd');
    }
    public function hookActionUpdateBlogImage()
    {
        $this->_clearCache('*');
    }
    public function hookActionUpdateBlog()
    {
        $this->_clearCache('*');
    }
    public function hookActionMetaPageSave()
    {
        $this->_clearCache('*');
    }
    public function _clearCache($template,$cache_id = null, $compile_id = null)
    {
        if($cache_id===null)
            $cache_id = $this->name;
        if($template=='*')
        {
            Tools::clearCache(Context::getContext()->smarty,null, $cache_id, $compile_id);
        }
        else
        {
            Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
        }
    }
}