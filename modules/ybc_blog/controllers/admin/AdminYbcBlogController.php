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
 * Class AdminYbcBlogController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogController extends ModuleAdminController
{
    public $baseLink;
    public function init()
    {
        parent::init();
        if(($controller = Tools::getValue('controller')) && $controller=='AdminYbcBlog')
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminYbcBlogPost'));
    }
    public function _saveConfiguration($control)
    {
        if($control == 'seo')
            $configs = Ybc_blog_defines::getInstance()->getConfigsSeo();
        elseif($control=='sitemap')
            $configs = Ybc_blog_defines::getInstance()->getConfigSiteMap();
        elseif($control=='rss')
            $configs = Ybc_blog_defines::getInstance()->getConfigsRss();
        elseif($control=='author')
            $configs = Ybc_blog_defines::getInstance()->getCustomerSettings();
        elseif($control=='socials')
            $configs = Ybc_blog_defines::getInstance()->getConfigsSocials();
        elseif($control=='email')
            $configs = Ybc_blog_defines::getInstance()->getConfigsEmail();
        elseif($control=='image')
            $configs = Ybc_blog_defines::getInstance()->getConfigsImage();
        elseif($control=='sidebar')
            $configs = Ybc_blog_defines::getInstance()->getConfigsSidebar();
        elseif($control=='homepage')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsHome();
            $configs['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
                'label' => $this->l('Select blog categories to display'),
                'type' => 'blog_categories',
                'name' => 'categories',
                'default' =>'',
            );
        }
        elseif($control=='postlistpage')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsPostListPage();
        }
        elseif($control=='postpage')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsPostPage();
        }
        elseif($control=='categorypage')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsCategoryPage();
        }
        elseif($control=='productpage')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsProductPage();
        }
        elseif($control=='config')
        {
            $configs = Ybc_blog_defines::getInstance()->getConfigsGlobal();
        }
        if(isset($configs) && $configs)
        {
            $dirImg = _PS_YBC_BLOG_IMG_DIR_.'avata/';
            $width_image =Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300);
            $height_image = Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300);
            $errors = array();
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $key_values = array();
            $aliasArg = array('YBC_BLOG_ALIAS','YBC_BLOG_ALIAS_POST','YBC_BLOG_ALIAS_CATEGORY','YBC_BLOG_ALIAS_GALLERY','YBC_BLOG_ALIAS_LATEST','YBC_BLOG_ALIAS_POPULAR','YBC_BLOG_ALIAS_FEATURED','YBC_BLOG_ALIAS_SEARCH','YBC_BLOG_ALIAS_AUTHOR','YBC_BLOG_ALIAS_AUTHOR2','YBC_BLOG_ALIAS_TAG');
            Hook::exec('actionUpdateBlog', array());
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    $label = $config['label'];
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $key_lang_default = trim(Tools::getValue($key.'_'.$id_lang_default));
                        if(isset($config['required']) && $config['required'] && $config['type']!='switch' && $key_lang_default=='')
                        {
                            $errors[] = sprintf($this->l('%s is required'),$config['label']);
                        }
                        if($key_lang_default && in_array($key,$aliasArg) && !Validate::isLinkRewrite($key_lang_default))
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                        elseif($key_lang_default && !Validate::isCleanHtml($key_lang_default))
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                        $key_values[$key][$id_lang_default] = $key_lang_default;
                        foreach($languages as $language)
                        {
                            $id_lang = (int)$language['id_lang'];
                            if($id_lang!=$id_lang_default)
                            {
                                $key_lang = trim(Tools::getValue($key.'_'.$id_lang));
                                if($key_lang && in_array($key,$aliasArg) && !Validate::isLinkRewrite($key_lang))
                                    $errors[] = sprintf($this->l('%s is not valid in %s'),$config['label'],$language['iso_code']);
                                elseif($key_lang && !Validate::isCleanHtml($key_lang))
                                    $errors[] = sprintf($this->l('%s is not valid in %s'),$config['label'],$language['iso_code']);
                                $key_values[$key][$id_lang] = $key_lang;
                            }
                        }
                    }
                    elseif($config['type']=='image')
                    {
                        $key_width = Tools::getValue($key.'_WIDTH');
                        if(!$key_width)
                            $errors[] = sprintf($this->l('%s width is required'),$label);
                        elseif(!Validate::isFloat($key_width))
                            $errors[] = sprintf($this->l('%s width is not valid'),$label);
                        elseif($key_width && ($key_width <50 ||$key_width >3000))
                            $errors[] = sprintf($this->l('%s width needs to be from 50 to 3000'),$label);
                        $key_height = Tools::getValue($key.'_HEIGHT');
                        if(!$key_height)
                            $errors[] = sprintf($this->l('%s height is required'),$label);
                        elseif(!Validate::isFloat($key_height))
                            $errors[] = sprintf($this->l('%s height is not valid'),$label);
                        elseif($key_height && ($key_height<50 || $key_height>3000) )
                            $errors[] = sprintf($this->l('%s height needs to be from 50 to 3000'),$label);
                        $key_values[$key.'_WIDTH'] = $key_width;
                        $key_values[$key.'_HEIGHT'] = $key_height;
                    }
                    else
                    {
                        $key_value = Tools::getValue($key);
                        if(isset($config['required']) && $config['required'] && $config['type']!='switch' && ((!is_array($key_value) && trim($key_value)=='') || (is_array($key_value) && !$key_value)) )
                        {
                            $errors[] = sprintf($this->l('%s is required'),$config['label']);
                        }
                        if(!is_array($key_value) && trim($key_value) && isset($config['validate']) && method_exists('Validate',$config['validate']))
                        {
                            $validate = $config['validate'];
                            if(!Validate::$validate(trim($key_value)))
                                $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                            unset($validate);
                        }
                        elseif($key_value && !is_array($key_value) && !Validate::isCleanHtml(trim($key_value)))
                        {
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                        }
                        elseif($key_value && is_array($key_value) && !Ybc_blog::validateArray($key_value))
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                        $key_values[$key] = $key_value;
                    }
                }
            }
            $YBC_BLOG_CAPTCHA_TYPE = Tools::getValue('YBC_BLOG_CAPTCHA_TYPE');
            if($YBC_BLOG_CAPTCHA_TYPE=='google' && !$key_values['YBC_BLOG_CAPTCHA_SITE_KEY'])
            {
                $errors[] = $this->l('Site key is required');
            }
            if($YBC_BLOG_CAPTCHA_TYPE=='google3' && !$key_values['YBC_BLOG_CAPTCHA_SITE_KEY3'])
            {
                $errors[] = $this->l('Site key is required');
            }
            if($YBC_BLOG_CAPTCHA_TYPE=='google' && !$key_values['YBC_BLOG_CAPTCHA_SECRET_KEY'])
            {
                $errors[] = $this->l('Secret key is required');
            }
            if($YBC_BLOG_CAPTCHA_TYPE=='google3' && !$key_values['YBC_BLOG_CAPTCHA_SECRET_KEY3'])
            {
                $errors[] = $this->l('Secret key is required');
            }
            //Custom validation
            if($control=='seo')
            {
                if(!$errors)
                {
                    $aliasArg = array('YBC_BLOG_ALIAS','YBC_BLOG_ALIAS_POST','YBC_BLOG_ALIAS_CATEGORY','YBC_BLOG_ALIAS_GALLERY','YBC_BLOG_ALIAS_LATEST','YBC_BLOG_ALIAS_POPULAR','YBC_BLOG_ALIAS_FEATURED','YBC_BLOG_ALIAS_SEARCH','YBC_BLOG_ALIAS_AUTHOR','YBC_BLOG_ALIAS_AUTHOR2','YBC_BLOG_ALIAS_TAG');
                    $alias = array();
                    foreach($languages as $lang)
                    {
                        $alias[$lang['id_lang']]=array();
                        foreach($aliasArg as $aliaKey)
                        {
                            $postedAlias = trim(Tools::getValue($aliaKey.'_'.$lang['id_lang']));
                            if($postedAlias && in_array($postedAlias,$alias[$lang['id_lang']]))
                            {
                                $errors[] = sprintf($this->l('Alias needs to be unique in %s'),$lang['iso_code']);
                                break;
                            }
                            elseif($postedAlias){
                                $alias[$lang['id_lang']][] = $postedAlias;
                            }
                        }
                    }

                }
            }
            if(Tools::isSubmit('YBC_BLOG_SHOW_AUTHOR_BLOCK') && isset($key_values['YBC_BLOG_AUTHOR_NUMBER']) &&  (int)$key_values['YBC_BLOG_AUTHOR_NUMBER'] <= 0)
                $errors[] = $this->l('Maximum number of positive authors needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_COMMENT_BLOCK') && isset($key_values['YBC_BLOG_COMMENT_LENGTH']) && (int)$key_values['YBC_BLOG_COMMENT_LENGTH'] <= 0)
                $errors[] = $this->l('Maximum comment length of latest comments displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_COMMENT_BLOCK') && isset($key_values['YBC_BLOG_COMMENT_NUMBER']) &&  (int)$key_values['YBC_BLOG_COMMENT_NUMBER'] <= 0)
                $errors[] = $this->l('Maximum number of latest comments displayed in sidebar needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED') && $key_values['YBC_BLOG_GALLERY_POST_NUMBER'] && (int)$key_values['YBC_BLOG_GALLERY_POST_NUMBER'] <= 0)
                $errors[] = $this->l('Maximum number of featured gallery images displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK') && isset($key_values['YBC_BLOG_LATES_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_LATES_POST_NUMBER'] <= 0)
                $errors[] = $this->l('Number of latest posts displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && isset($key_values['YBC_BLOG_PUPULAR_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_PUPULAR_POST_NUMBER'] <= 0)
                $errors[] = $this->l('Number of popular posts displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_FEATURED_BLOCK') && isset($key_values['YBC_BLOG_FEATURED_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_FEATURED_POST_NUMBER'] <= 0)
                $errors[] = $this->l('Maximum number of featured posts displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_LATES_POST_NUMBER') && isset($key_values['YBC_BLOG_MAX_COMMENT']) &&  (int)$key_values['YBC_BLOG_MAX_COMMENT'] < 0)
                $errors[] = $this->l('Maximum number of latest comments displayed needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_DEFAULT_RATING') && ((int)$key_values['YBC_BLOG_DEFAULT_RATING'] < 1 || (int)$key_values['YBC_BLOG_DEFAULT_RATING'] >5))
                $errors[] = $this->l('Default rating must be between 1 - 5');
            if(Tools::isSubmit('YBC_BLOG_ITEMS_PER_PAGE') && $key_values['YBC_BLOG_ITEMS_PER_PAGE']!='' && Validate::isInt($key_values['YBC_BLOG_ITEMS_PER_PAGE']) && (int)$key_values['YBC_BLOG_ITEMS_PER_PAGE'] <= 0)
                $errors[] = $this->l('Number of posts per page on main page needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_SHOW_TAGS_BLOCK') && isset($key_values['YBC_BLOG_TAGS_NUMBER']) &&  (int)$key_values['YBC_BLOG_TAGS_NUMBER'] <= 0)
                $errors[] = $this->l('Maximum number of tags displayed on Tags block needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_GALLERY_PER_PAGE') && (int)$key_values['YBC_BLOG_GALLERY_PER_PAGE'] <= 0)
                $errors[] = $this->l('Number of image per page needs to be greater than 0');
            if(Tools::isSubmit('YBC_BLOG_COMMENT_PER_PAGE') && (int)$key_values['YBC_BLOG_COMMENT_PER_PAGE'] <= 0)
                $errors[] = $this->l('Number of comment per page needs to be greater than 0');
            if($control=='homepage')
            {
                if(isset($key_values['YBC_BLOG_SHOW_LATEST_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_LATEST_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']))
                {
                    if($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']=='')
                        $errors[] = $this->l('Maximum number of latest posts displayed is required');
                    elseif($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']<=0)
                        $errors[] = $this->l('Maximum number of latest posts displayed needs to be greater than 0');
                }
                if(isset($key_values['YBC_BLOG_SHOW_POPULAR_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']))
                {
                    if($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']=='')
                        $errors[] = $errors[] = $this->l('Maximum number of popular posts displayed is required');
                    elseif($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']<=0)
                        $errors[] = $this->l('Maximum number of popular posts displayed needs to be greater than 0');
                }
                if(isset($key_values['YBC_BLOG_SHOW_FEATURED_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_FEATURED_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME']))
                {
                    if($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME']=='')
                        $errors[] = $this->l('Maximum number of featured posts displayed is required');
                    elseif($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME'] <=0)
                        $errors[] = $this->l('Maximum number of featured posts displayed needs to be greater than 0');
                }
                if(isset($key_values['YBC_BLOG_SHOW_GALLERY_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']))
                {
                    if($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']=='')
                        $errors[] = $this->l('Maximum number of featured gallery images displayed is required');
                    elseif($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']<=0)
                        $errors[] = $this->l('Maximum number of featured gallery images displayed needs to be greater than 0');
                }
                if(isset($key_values['YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']))
                {
                    if($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']=='')
                        $errors[] = $this->l('Maximum number of post categories displayed is required');
                    elseif($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']<=0)
                        $errors[] = $this->l('Maximum number of post categories displayed needs to be greater than 0');
                }
            }
            if($emailsStr = Tools::getValue('YBC_BLOG_ALERT_EMAILS'))
            {
                $emails = explode(',',$emailsStr);
                if($emails)
                {
                    foreach($emails as $email)
                    {
                        if(!Validate::isEmail(trim($email)))
                        {
                            $errors[] = $this->l('One of the submitted emails is not valid');
                            break;
                        }
                    }
                }
            }
            if(!$errors)
            {
                if($configs)
                {
                    foreach($configs as $key => $config)
                    {
                        if(isset($config['lang']) && $config['lang'])
                        {
                            $valules = array();
                            foreach($languages as $lang)
                            {
                                if($config['type']=='switch')
                                    $valules[$lang['id_lang']] = (int)$key_values[$key][$lang['id_lang']] ? 1 : 0;
                                else
                                    $valules[$lang['id_lang']] = $key_values[$key][$lang['id_lang']] ? : $key_values[$key][$id_lang_default];
                            }
                            Configuration::updateValue($key,$valules,true);
                        }
                        else
                        {
                            if($config['type']=='switch')
                            {
                                Configuration::updateValue($key,(int)$key_values[$key] ? 1 : 0);
                            }
                            elseif($config['type']=='checkbox')
                                Configuration::updateValue($key,isset($key_values[$key]) && $key_values[$key] ? implode(',',$key_values[$key]):'');
                            elseif($config['type']=='image')
                            {
                                Configuration::updateValue($key.'_WIDTH',$key_values[$key.'_WIDTH']);
                                Configuration::updateValue($key.'_HEIGHT',$key_values[$key.'_HEIGHT']);
                            }
                            elseif($config['type']=='blog_categories' && ($blog_categories  = Tools::getValue('blog_categories')) && is_array($blog_categories) && Ybc_blog::validateArray($blog_categories))
                            {
                                Configuration::updateValue($key,implode(',',$blog_categories));
                            }
                            elseif($config['type']=='file')
                            {
                                if(isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name'])
                                {
                                    $_FILES[$key]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$key]['name']);
                                    if(!Validate::isFileName($_FILES[$key]['name']))
                                    {
                                        $errors[] = $this->l('Image is not valid');
                                    }
                                    else
                                    {
                                        if(file_exists($dirImg.$_FILES[$key]['name']))
                                        {
                                            $_FILES[$key]['name'] = $this->module->createNewFileName($dirImg,$_FILES[$key]['name']);
                                        }
                                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                        $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                        if (isset($_FILES[$key]) &&
                                            !empty($_FILES[$key]['tmp_name']) &&
                                            !empty($imagesize) &&
                                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                                        )
                                        {
                                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                                            if($_FILES[$key]['size'] > $max_file_size*1024*1024)
                                                $errors[] = sprintf($this->l('Image file is too large. Limit: %sMb'),$max_file_size);
                                            elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                                $errors[] = $this->l('Cannot upload the file');
                                            elseif(!ImageManager::resize($temp_name, $dirImg.$_FILES[$key]['name'], $width_image, $height_image, $type))
                                                $errors[] = $this->l('An error occurred during the image upload process.');
                                            if (isset($temp_name) && file_exists($temp_name))
                                                @unlink($temp_name);
                                            if(($img = Configuration::get($key)))
                                            {
                                                if(file_exists($dirImg.$img))
                                                    @unlink($dirImg.$img);
                                            }
                                            Configuration::updateValue($key,$_FILES[$key]['name']);
                                        }
                                    }


                                }
                            }
                            else
                            {
                                if(is_array($key_values[$key]))
                                    Configuration::updateValue($key,$key_values[$key] ? implode(',',$key_values[$key]):'');
                                else
                                    Configuration::updateValue($key,trim($key_values[$key]));
                            }
                        }
                    }
                }
                $this->module->refreshCssCustom();
            }
            if (count($errors))
            {
                $this->module->errorMessage = $this->module->displayError($errors);
            }
            if($control=='sidebar')
            {
                $config_values=array(
                    'YBC_BLOG_SHOW_CATEGORIES_BLOCK' => Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK'),
                    'YBC_BLOG_SHOW_POPULAR_POST_BLOCK' => Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK'),
                    'YBC_BLOG_SHOW_LATEST_NEWS_BLOCK' => Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'),
                    'YBC_BLOG_SHOW_GALLERY_BLOCK' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK'),
                    'YBC_BLOG_SHOW_ARCHIVES_BLOCK' => Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK'),
                    'YBC_BLOG_SHOW_SEARCH_BLOCK' => Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK'),
                    'YBC_BLOG_SHOW_TAGS_BLOCK' => Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK'),
                    'YBC_BLOG_SHOW_COMMENT_BLOCK' => Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK'),
                    'YBC_BLOG_SHOW_AUTHOR_BLOCK' => Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK'),
                    'YBC_BLOG_SHOW_HTML_BOX' => Configuration::get('YBC_BLOG_SHOW_HTML_BOX'),
                    'YBC_BLOG_SHOW_FEATURED_BLOCK' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK'),
                );
            }
            if($control=='homepage')
            {
                $config_values=array(
                    'YBC_BLOG_SHOW_LATEST_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME'),
                    'YBC_BLOG_SHOW_POPULAR_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'),
                    'YBC_BLOG_SHOW_FEATURED_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME'),
                    'YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'),
                    'YBC_BLOG_SHOW_GALLERY_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'),
                );
            }
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => $errors ? 'error' : 'success',
                        'message' => $errors ? $this->module->errorMessage : $this->module->displayConfirmation($this->l('Configuration saved')),
                        'ybc_link_desc'=>$this->module->getLink(),
                        'config_values' => isset($config_values) ? $config_values:'',
                    )
                ));
            }
            if(!count($errors))
                Tools::redirectAdmin($this->baseLink.'&conf=4&control='.$control);
        }
    }

}
