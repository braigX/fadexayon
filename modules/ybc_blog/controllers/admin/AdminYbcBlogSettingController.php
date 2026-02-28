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
if(!class_exists('AdminYbcBlogController'))
    require_once dirname(__FILE__) . '/AdminYbcBlogController.php';
/**
 * Class AdminYbcBlogSettingController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogSettingController extends AdminYbcBlogController
{
    public $baseLink;
    public $_html='';
    public $controls = array('config','seo','sitemap','rss','socials','email','image','sidebar','homepage','postlistpage','postpage','categorypage','productpage');
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogSetting');
        $this->bootstrap = true;
        if($this->checkProfileEmployee())
        {
            $this->checked = true;
            if(Tools::isSubmit('saveConfig'))
            {
                $this->_postConfig();
            }
            elseif(Tools::isSubmit('saveEmailTemplate') || Tools::isSubmit('change_enabled'))
            {
                $this->submitSaveEamilTemplate();
            }
            elseif(Tools::isSubmit('submitBulkEnabled') && ($id_email_template = Tools::getValue('bulk_ybc_email')) )
            {
                Ybc_blog_email_template_class::submitBulkEnabled($id_email_template);
                Tools::redirectAdmin($this->baseLink.'&control=email&conf=4');
            }
            elseif(Tools::isSubmit('submitBulkDiasabled') && ($id_email_template = Tools::getValue('bulk_ybc_email')) )
            {
                Ybc_blog_email_template_class::submitBulkDiasabled($id_email_template);
                Tools::redirectAdmin($this->baseLink.'&control=email&conf=4');
            }
            elseif(Tools::isSubmit('deldefaultavataimage'))
            {
                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT'));
                Configuration::updateValue('YBC_BLOG_IMAGE_AVATA_DEFAULT','');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->module->displayConfirmation($this->l('Image deleted')),
                            'image_default' => $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/default_customer.png'),
                        )
                    ));
                }
            }
            elseif(($action = Tools::getValue('action')) && $action=='updateSidebarOrdering')
            {
                $this->updateSidebarOrdering();
            }
            elseif($action=='updateBlock')
            {
                $this->updateBlock();
            }
            elseif(Tools::isSubmit('saveTemplateGPT'))
                $this->saveTemplateGPT();
            elseif(Tools::isSubmit('delGPT') && ($id_ybc_chatgpt_template = (int)Tools::getValue('id_ybc_chatgpt_template')))
            {
                $this->deleteTemplateGPT($id_ybc_chatgpt_template);
            }
            elseif(Tools::isSubmit('editybc_chatgpt') && Tools::isSubmit('id_ybc_chatgpt_template'))
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'form' =>$this->renderFormTemplateChatGPT()
                            )
                        )
                    );
                }
                else
                    return $this->renderFormTemplateChatGPT();
            }
        }
    }
    private function deleteTemplateGPT($id_ybc_chatgpt_template)
    {
        $templateGpt = new Ybc_chatgpt($id_ybc_chatgpt_template);
        $templateGpt->delete();
        if(Tools::isSubmit('ajax'))
        {
            die(
                json_encode(
                    array(
                        'success' => $this->l('Delete successfully'),
                    )
                )
            );
        }
    }
    private function saveTemplateGPT(){
        $errors = array();
        if(($id_ybc_chatgpt_template = (int)Tools::getValue('id_ybc_chatgpt_template')))
        {
            $template = new Ybc_chatgpt($id_ybc_chatgpt_template);
        }
        else
        {
            $template = new Ybc_chatgpt();
        }
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

        $label = Tools::getValue('label_'.$id_lang_default);
        $content = Tools::getValue('content_'.$id_lang_default);
        if(!$label)
            $errors[] = $this->l('Label is required');
        elseif($label && !Validate::isCleanHtml($label))
            $errors[] = $this->l('Label is required');
        if(!$content)
            $errors[] = $this->l('Content is required');
        elseif($content && !Validate::isCleanHtml($content))
            $errors[] = $this->l('Content is required');
        if(!$errors)
        {
            $languages = Language::getLanguages(false);
            foreach($languages as $language)
            {
                $label_lang = Tools::getValue('label_'.$language['id_lang']);
                $content_lang = Tools::getValue('content_'.$language['id_lang']);
                if($label_lang && !Validate::isCleanHtml($label_lang))
                    $errors[] = sprintf($this->l('Label in %s is not valid'), $language['iso_code']);
                else
                    $template->label[$language['id_lang']] = $label_lang ? : $label;
                if($content_lang && !Validate::isCleanHtml($content_lang))
                    $errors[] = sprintf($this->l('Content in %s is not valid'), $language['iso_code']);
                else
                    $template->content[$language['id_lang']] = $content_lang ? : $content;
            }
        }
        if($errors)
        {
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($errors)
                        )
                    )
                );
            }
            else
                $this->_html += $this->module->displayError($errors);

        }
        else{
            $success = '';
            if($template->id)
            {
                if($template->update())
                    $success = $this->l('Updated successfully');
            }
            elseif($template->add())
                $success = $this->l('Added successfully');
            if($success)
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $success,
                                'list' => $this->module->displayListTemplateChatGPT(),
                            )
                        )
                    );
                }
                else
                    $this->_html = $this->module->displayConfirmation($success);
            }
            else
            {
                $errors[] = $this->l('An error occurred while saving the template');
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($errors)
                            )
                        )
                    );
                }
                else
                    $this->_html += $this->module->displayError($errors);
            }
        }
    }
    private function updateBlock()
    {
        $field = Tools::getValue('field');
        $value_filed = Tools::getValue('value_filed');
        if(Validate::isConfigName($field) && Validate::isCleanHtml($value_filed,true))
        {
            Configuration::updateValue(Tools::getValue('field'),Tools::getValue('value_filed'));
            $this->module->_clearCache('*');
            die(
                json_encode(
                    array(
                        'messageType' => 'success',
                        'message'=> $this->module->displaySuccessMessage($this->l('Updated successfully')),
                    )
                )
            );
        }
        else
        {
            die(json_encode(
                array(
                    'messageType' => 'error',
                    'message'=> $this->module->displayError($this->l('Update failed')),
                )
            ));
        }
    }
    private function updateSidebarOrdering()
    {
        $control = Tools::getValue('control');
        $ok = false;
        if($control=='sidebar')
        {
            $positions= Tools::getValue('sidebar-position-sidebar');
            if($positions && is_array($positions) && Ybc_blog::validateArray($positions))
            {
                foreach($positions as $key=> $position)
                    $positions[$key] ='sidebar_'.$position;
                Configuration::updateValue('YBC_BLOG_POSITION_SIDEBAR',implode(',',$positions));
                $ok = true;
            }
        }
        elseif($control=='homepage')
        {
            $positions= Tools::getValue('sidebar-position-homepage');
            if($positions && is_array($positions) && Ybc_blog::validateArray($positions))
            {
                foreach($positions as $key=> $position)
                    $positions[$key] ='homepage_'.$position;
                Configuration::updateValue('YBC_BLOG_POSITION_HOMEPAGE',implode(',',$positions));
                $ok= true;
            }
        }
        if($ok)
        {
            die(
                json_encode(
                    array(
                        'messageType' => 'success',
                        'message'=> $this->module->displaySuccessMessage($this->l('Position updated')),
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'messageType'=>'error',
                        'message'=> $this->module->displayError($this->l('Update failed')),
                    )
                )
            );
        }
    }
    public function renderList()
    {
        $control = Tools::getValue('control','config');
        if(!in_array($control,$this->controls))
            Tools::redirectAdmin($this->baseLink);
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign($control), array('ybc_blog_body_html' => $this->_getContent($control))));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getContent($control)
    {
        if($control=='seo')
        {
           return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsSeo(), $this->l('SEO'),'icon-seo');
        }
        elseif($control=='sitemap')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigSiteMap(), $this->l('Google sitemap'),'icon-sitemap');
        }
        elseif($control=='rss')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsRss(),$this->l('RSS feed'),'icon-rss');
        }
        elseif($control=='socials')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsSocials(), $this->l('Socials'),'icon-socials');
        }
        elseif($control=='email')
        {
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                return $email_template->renderForm().$email_template->previewTemplate();
            }
            else
            {
                $params = array(
                    'id_ybc_blog_email_template' => (int)Tools::getValue('id_ybc_blog_email_template'),
                    'template' => Tools::getValue('template'),
                    'active' => Tools::getValue('active'),
                    'subject' => Tools::getValue('subject'),
                    'send_to' => Tools::getValue('send_to'),
                    'sort_type' => Tools::getValue('sort_type'),
                    'sort' => Tools::getValue('sort'),
                    'page' => (int)Tools::getValue('page'),
                    'paginator_ybc_email_select_limit' => (int)Tools::getValue('paginator_ybc_email_select_limit'),
                );
                return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsEmail(), $this->l('Email configuration'),'icon-email').Ybc_blog_email_template_class::getInstance()->renderList($params);
            }
        }
        elseif($control=='image')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsImage(), $this->l('Image'),'icon-cogs');
        }
        elseif($control=='sidebar')
        {
            return $this->renderConfig($control, Ybc_blog_defines::getInstance()->getConfigsSidebar(), $this->l('Sidebar'),'icon-sidebar');
        }
        elseif($control=='homepage')
        {
            $configs_home = Ybc_blog_defines::getInstance()->getConfigsHome();
            $configs_home['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME'] = array(
                'label' => $this->l('Select blog categories to display'),
                'type' => 'blog_categories',
                'html_content' =>$this->module->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),$this->module->getSelectedCategories()),
                'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),
                'name' => 'categories',
                'selected_categories' => $this->module->getSelectedCategories(),
                'default' =>'',
            );
            return $this->renderConfig($control,$configs_home, $this->l('Home page'),'icon-homepage');
        }
        elseif($control=='postlistpage')
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsPostListPage(), $this->l('Post listing pages'),'icon-postlistpage');
        elseif($control=='postpage')
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsPostPage(), $this->l('Post details page'),'icon-postpage');
        elseif($control=='categorypage')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsCategoryPage(), $this->l('Product categories page'),'icon-categorypage');
        }
        elseif($control=='productpage')
        {
            return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsProductPage(), $this->l('Product details page'),'icon-productpage');
        }
        elseif($control=='config')
        {
            if(Tools::isSubmit('addNew') || Tools::isSubmit('id_ybc_chatgpt_template'))
            {
                return $this->renderFormTemplateChatGPT();
            }
            else
            {
                return $this->renderConfig($control,Ybc_blog_defines::getInstance()->getConfigsGlobal(), $this->l('Global settings'),'icon-AdminAdmin').$this->module->displayText($this->renderFormTemplateChatGPT(),'div','box-form-chatgpt');
            }
        }
        return $control;
    }
    private function checkProfileEmployee()
    {
        $control = Tools::getValue('control','config');
        if($control=='seo')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Seo');
        elseif($control=='sitemap')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Sitemap');
        elseif($control=='rss')
            return $this->module->checkProfileEmployee($this->context->employee->id,'RSS feed');
        elseif($control=='socials')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Socials');
        elseif($control=='email')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Email');
        elseif($control=='image')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Image');
        elseif($control=='sidebar')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Sidebar');
        elseif($control=='homepage')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Home page');
        elseif($control=='postlistpage')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Post listing pages');
        elseif($control=='postpage')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Post detail page');
        elseif($control=='categorypage')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Category page');
        elseif($control=='productpage')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Product detail page');
        elseif($control=='config')
            return $this->module->checkProfileEmployee($this->context->employee->id,'Global settings');
        return true;
    }
    private function _postConfig()
    {
        $control = Tools::getValue('control','config');
        if(!in_array($control,$this->controls))
            Tools::redirectAdmin($this->baseLink);
        $this->_saveConfiguration($control);
    }
    private function submitSaveEamilTemplate()
    {
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                $email_template->active = $status;
                if($email_template->update())
                {
                    if($status==1)
                        $title= $this->l('Click to disabled');
                    else
                        $title=$this->l('Click to enabled');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(array(
                            'listId' => $id_ybc_blog_email_template,
                            'enabled' => $status,
                            'field' => $field,
                            'message' => $this->module->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                            'messageType'=>'success',
                            'title'=>$title,
                            'href' => $this->baseLink.'&control=email&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_ybc_blog_email_template='.$id_ybc_blog_email_template,
                        )));
                    }
                }
                else
                {
                    die(json_encode(array(
                        'message' => $this->module->displaySuccessMessage($this->l('Update status failed')) ,
                        'messageType'=>'error',
                    )));
                }
            }
            else
            {
                die(json_encode(array(
                    'message' => $this->module->displaySuccessMessage($this->l('Email template is not valid')) ,
                    'messageType'=>'error',
                )));
            }
        }
        else
        {
            $errors = array();
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                $this->submitSaveMailTemplate($email_template,$errors);
            }
            else
                $errors[] = $this->l("Email template is not valid");
            if($errors)
                $this->module->errorMessage = $this->module->displayError($errors);
            else
                Tools::redirectAdmin($this->baseLink.'&control=email&conf=4');
        }
    }
    private function submitSaveMailTemplate($emailTemplate,&$errors)
    {
        /** @var Ybc_blog_email_template_class  $emailTemplate */
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();
        $subject = trim(Tools::getValue('subject_'.$id_lang_default));
        $content_html = trim(Tools::getValue('content_html_'.$id_lang_default));
        $content_txt = trim(Tools::getValue('content_txt_'.$id_lang_default));
        $active = (int)Tools::getValue('active');
        if(!$subject)
            $errors[] = $this->l('Subject is required');
        elseif($subject && !Validate::isMailSubject($subject))
            $errors[] = $this->l('Subject is not valid');
        if(!$content_html)
            $errors[] = $this->l('Content in HTML form is required');
        elseif($content_html && !Validate::isCleanHtml($content_html,true))
            $errors[] = $this->l('Content in HTML form is not valid');
        if(!$content_txt)
            $errors[] = $this->l('Content in TXT form is required');
        elseif($content_txt && !Validate::isCleanHtml($content_txt))
            $errors[] = $this->l('Content in TXT form is not valid');
        $content_htmls = array();
        $content_txts = array();
        if(!$errors)
        {
            $emailTemplate->active = $active;
            if($languages)
            {
                foreach($languages as $language)
                {
                    $id_lang = (int)$language['id_lang'];
                    if($id_lang !=$id_lang_default)
                    {
                        $subject_lang = trim(Tools::getValue('subject_'.$id_lang));
                        if($subject_lang && !Validate::isMailSubject($subject_lang))
                            $errors[] = sprintf($this->l('Subject is not valid in %s'),$language['iso_code']);
                        else
                            $emailTemplate->subject[$id_lang] = $subject_lang ? : $subject;
                        $content_html_lang = trim(Tools::getValue('content_html_'.$id_lang));
                        if($content_html_lang && !Validate::isCleanHtml($content_html_lang,true))
                            $errors[] = sprintf($this->l('Content in HTML form is not valid in %s'),$language['iso_code']);
                        else
                            $content_htmls[$id_lang] = $content_html_lang ? : $content_html;
                        $content_txt_lang = trim(Tools::getValue('content_txt_'.$id_lang));
                        if($content_txt_lang && !Validate::isCleanHtml($content_txt_lang))
                            $errors[] = sprintf($this->l('Content in HTML form is not valid in %s'),$language['iso_code']);
                        else
                            $content_txts[$id_lang] = $content_txt_lang ? : $content_txt;
                    }
                    else
                    {
                        $emailTemplate->subject[$id_lang] = $subject;
                        $content_htmls[$id_lang] = $content_html;
                        $content_txts[$id_lang] = $content_txt;
                    }
                }
            }
        }
        if(!$errors)
        {
            if($emailTemplate->update())
            {
                if ($languages) {
                    $base_dir = _PS_ROOT_DIR_ . '/themes/' . ($this->module->is17 ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme()) . '/modules/' . $this->module->name . '/mails/';
                    if (!is_dir($base_dir))
                        mkdir($base_dir, 0755, true);

                    foreach ($languages as $l) {
                        $id_lang = (int)$l['id_lang'];

                        $iso_path = $base_dir . $l['iso_code'] . '/';
                        if (!is_dir($iso_path))
                            mkdir($iso_path, 0755, true);
                        @file_put_contents($iso_path . $emailTemplate->template . '.html', $content_htmls[$id_lang]);
                        @file_put_contents($iso_path . $emailTemplate->template . '.txt', $content_txts[$id_lang]);
                    }
                }
            }
            else
                $errors[] = $this->l("Update failed");
        }
    }
    private function renderConfig($control,$configs,$title,$icon)
    {
        $this->context->smarty->assign(
            array(
                'title' => $title,
            )
        );
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon!='icon-email' ? $icon:'icon-AdminAdmin',
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                $arg = array(
                    'name' => $key,
                    'type' => $config['type'],
                    'label' => $config['label'],
                    'autoload_rte' => isset($config['autoload_rte'])? $config['autoload_rte'] :false,
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] ? true : false,
                    'required2' => isset($config['required2']) && $config['required2'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'values' => isset($config['values']) ? $config['values'] : array(),
                    'lang' => isset($config['lang']) ? $config['lang'] : false,
                    'class' => isset($config['class']) ? $config['class'] : '',
                    'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
                    'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                    'html_content' => isset($config['html_content']) ? $config['html_content']:false,
                    'categories' => isset($config['categories']) ? $config['categories']:false,
                    'col' => isset($config['col']) ? $config['col']:9,
                    'selected_categories' => isset($config['selected_categories']) ? $config['selected_categories']:false,
                );
                if(isset($arg['suffix']) && !$arg['suffix'])
                    unset($arg['suffix']);
                $fields_form['form']['input'][] = $arg;
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = 'id_module';
        $helper->submit_action = 'saveConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogSetting', false).'&control='.$control;
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogSetting');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();
        $languages = Language::getLanguages(false);
        if(Tools::isSubmit('saveConfig'))
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                        }
                    }
                    else
                        $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');
                }
            }
        }
        else
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                        }
                    }
                    elseif($config['type']=='checkbox')
                        $fields[$key] = explode(',',Configuration::get($key));
                    elseif($config['type']=='image')
                    {
                        $fields[$key]['width'] = Configuration::get($key.'_WIDTH');
                        $fields[$key]['height'] = Configuration::get($key.'_HEIGHT');
                    }
                    elseif($config['type']=='file')
                    {
                        if(Configuration::get($key))
                        {
                            $display_img = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.Configuration::get($key));
                            $img_del_link = $this->baseLink.'&deldefaultavataimage=true&control=image';
                        }
                        else
                        {
                            $display_img = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/default_customer.png');
                        }
                    }
                    else
                        $fields[$key] = Configuration::get($key);
                }
            }
        }
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'cancel_url' => $icon!='icon-email' ? $this->context->link->getAdminLink('AdminYbcBlogPost'):false,
            'isConfigForm' => true,
            'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'display_img' => isset($display_img)? $display_img : '',
            'img_del_link' => isset($img_del_link) ? $img_del_link :'',
            'link_module_blog' => $this->module->getLocalPath(),
        );
        if($control=='homepage')
        {
            $homepages=array(
                'homepage_new'=>array(
                    'title'=>$this->l('Latest posts'),
                    'name'=>'YBC_BLOG_SHOW_LATEST_BLOCK_HOME',
                ),
                'homepage_popular' => array(
                    'title'=>$this->l('Popular posts'),
                    'name'=>'YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'
                ),
                'homepage_featured' => array(
                    'title'=>$this->l('Featured posts'),
                    'name'=> 'YBC_BLOG_SHOW_FEATURED_BLOCK_HOME',
                ),
                'homepage_categories' => array(
                    'title'=>$this->l('Featured categories'),
                    'name'=> 'YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME',
                ),
                'homepage_gallery' => array(
                    'title'=>$this->l('Photo gallery'),
                    'name'=>'YBC_BLOG_SHOW_GALLERY_BLOCK_HOME',
                ),
            );
            $position_homepages= explode(',',Configuration::get('YBC_BLOG_POSITION_HOMEPAGE')? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE'):'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
            $helper->tpl_vars['homepages'] = $homepages;
            $helper->tpl_vars['position_homepages'] = $position_homepages;
        }
        elseif($control=='sidebar')
        {
            $sidebars=array(
                'sidebar_new' => array(
                    'title'=>$this->l('Latest posts'),
                    'name'=> 'YBC_BLOG_SHOW_LATEST_NEWS_BLOCK',
                ),
                'sidebar_popular' =>array(
                    'name'=>'YBC_BLOG_SHOW_POPULAR_POST_BLOCK',
                    'title'=>  $this->l('Popular posts'),
                ),
                'sidebar_featured' => array(
                    'title'=>$this->l('Featured posts'),
                    'name'=>'YBC_BLOG_SHOW_FEATURED_BLOCK',
                ),
                'sidebar_gallery' => array(
                    'title'=>$this->l('Photo gallery'),
                    'name'=>'YBC_BLOG_SHOW_GALLERY_BLOCK',
                ),
                'sidebar_archived' => array(
                    'title'=>$this->l('Archived posts'),
                    'name'=>'YBC_BLOG_SHOW_ARCHIVES_BLOCK',
                ),
                'sidebar_categories' => array(
                    'title'=>$this->l('Blog categories'),
                    'name'=>'YBC_BLOG_SHOW_CATEGORIES_BLOCK',
                ),
                'sidebar_search' => array(
                    'title'=>$this->l('Search in blog'),
                    'name'=>'YBC_BLOG_SHOW_SEARCH_BLOCK',
                ),
                'sidebar_tags' => array(
                    'title'=>$this->l('Blog tags'),
                    'name'=>'YBC_BLOG_SHOW_TAGS_BLOCK'
                ),
                'sidebar_comments' => array(
                    'title'=>$this->l('Latest comments'),
                    'name'=>'YBC_BLOG_SHOW_COMMENT_BLOCK',
                ),
                'sidebar_authors' => array(
                    'title'=>$this->l('Top authors'),
                    'name'=>'YBC_BLOG_SHOW_AUTHOR_BLOCK',
                ),
                'sidebar_htmlbox' => array(
                    'title'=>$this->l('HTML box'),
                    'name'=>'YBC_BLOG_SHOW_HTML_BOX',
                ),
                'sidebar_rss' => array(
                    'title'=>$this->l('Blog RSS'),
                    'name'=>'YBC_BLOG_ENABLE_RSS_SIDEBAR',
                ),
            );
            $helper->tpl_vars['sidebars'] = $sidebars;
            $position_sidebar= explode(',',Configuration::get('YBC_BLOG_POSITION_SIDEBAR') ? Configuration::get('YBC_BLOG_POSITION_SIDEBAR'):'sidebar_categories,sidebar_search,sidebar_new,sidebar_popular,sidebar_featured,sidebar_tags,sidebar_gallery,sidebar_archived,sidebar_comments,sidebar_authors,sidebar_htmlbox,sidebar_rss');
            if(!in_array('sidebar_htmlbox',$position_sidebar))
                $position_sidebar[]='sidebar_htmlbox';
            $helper->tpl_vars['position_sidebar'] = $position_sidebar;
        }
        elseif($control=='config')
        {
            $helper->tpl_vars['configTabs'] = array(
                'general' => $this->l('General'),
                'gallery' => $this->l('Gallery'),
                'slider' => $this->l('Slider'),
                'comment' => $this->l('Likes and Comments'),
                'polls' => $this->l('Polls'),
                'design' => $this->l('Design'),
                'chatgpt' => $this->l('ChatGPT'),
            );
        }
        elseif($control=='sitemap')
        {
            $urls_sitemap=array();
            $languages = Language::getLanguages(true);
            foreach($languages as $lang)
            {
                $urls_sitemap[]= array(
                    'link'=>trim($this->module->getBaseLink(),'/').'/'.$lang['iso_code'].'/blog_sitemap.xml',
                    'img'=> $this->module->getBaseLink().'img/l/'.$lang['id_lang'].'.jpg'
                );
            }
            $helper->tpl_vars['url_sitemap'] = trim($this->module->getBaseLink(),'/').'/blog_sitemap.xml';
            $helper->tpl_vars['urls_sitemap'] = isset($urls_sitemap) && count($urls_sitemap) > 1 ? $urls_sitemap : false;
        }
        elseif($control=='rss')
        {
            $urls_rss=array();
            $languages = Language::getLanguages(true);
            foreach($languages as $lang)
            {
                $urls_rss[]= array(
                    'link'=>$this->module->getLink('rss',array(),$lang['id_lang']),
                    'img'=> $this->module->getBaseLink().'img/l/'.$lang['id_lang'].'.jpg'
                );
            }
            $helper->tpl_vars['urls_rss'] = $urls_rss;
        }
        return $helper->generateForm(array($fields_form));
    }
    private function renderFormTemplateChatGPT()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => Tools::isSubmit('id_ybc_chatgpt_template') ? $this->l('Edit prompt template') : $this->l('Add prompt template'),
                    'icon' => 'icon-AdminCatalog',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Label'),
                        'name' => 'label',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'content',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_ybc_chatgpt_template',
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'ybc_chatgpt_template';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this->module;
        $helper->identifier = 'id_ybc_chatgpt_template';
        $helper->submit_action = 'saveTemplateGPT';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminYbcBlogSetting', false).'&control=config&current_tab=chatgpt';
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogSetting');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array(
            array(
                'name' => 'id_ybc_chatgpt_template',
                'primary_key' => true
            ),
            array(
                'name' => 'label',
                'multi_lang' => true
            ),
            array(
                'name' => 'content',
                'multi_lang' => true
            ),
        );
        $helper->tpl_vars = array(
            'base_url' => Context::getContext()->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues($fields,'id_ybc_chatgpt_template','Ybc_chatgpt','saveTemplateGPT'),
            'cancel_popup' => '#',
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'post_key' => 'id_ybc_chatgpt_template',
        );
        return $helper->generateForm(array($fields_form));
    }
}
