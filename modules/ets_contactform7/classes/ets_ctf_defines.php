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
class Ets_ctf_defines
{ 
    protected static $instance;
    /** @var Ets_contactform7 */
    private $module;
    public	function __construct()
	{
        $this->context= Context::getContext();
        $this->module = new Ets_contactform7();
	}
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_contactform7', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_ctf_defines();
        }
        return self::$instance;
    }
    public function getFieldConfig($type,$config=true,$id_contact = 0)
    {
        if($type=='config_fields')
        {
            return array(
    			'form' => array(
    				'legend' => array(
    					'title' => $this->l('Integration'),
    					'icon' => 'icon-cogs'
    				),
                    'id_form'=>'module_form_integration',
    				'input' => array(
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_ENABLE_RECAPTCHA',
                            'label'=> $this->l('Enable reCAPTCHA'),
                            'values' => array(
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_RECAPTCHA_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_RECAPTCHA_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>0,
                            'form_group_class'=>'form_group_contact google',
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('ReCaptcha type'),
                            'name' => 'ETS_CTF7_RECAPTCHA_TYPE',
                            'required' => true,
                            'form_group_class' => 'form_group_contact google google2',
                            'values' => array(
                                array(
                                    'id' => 'id_recaptcha_v2',
                                    'value' => 'v2',
                                    'label' => $this->l('reCaptcha v2'),
                                ),
                                array(
                                    'id' => 'id_recaptcha_v3',
                                    'value' => 'v3',
                                    'label' => $this->l('reCaptcha v3'),
                                ),
                            ),
                            'default' => 'v2'
                        ),
    					array(
    						'type' => 'text',
    						'label' => $this->l('Site Key'),
    						'name' => 'ETS_CFT7_SITE_KEY',
                            'required' => true,
                            'form_group_class'=>'form_group_contact google google2 capv2',
    					),
    					array(
    						'type' => 'text',
    						'label' => $this->l('Secret Key'),
    						'name' => 'ETS_CFT7_SECRET_KEY',
                            'required' => true,
                            'form_group_class'=>'form_group_contact google google2 capv2',
    					),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Site Key v3'),
                            'name' => 'ETS_CTF7_SITE_KEY_V3',
                            'required' => true,
                            'form_group_class' => 'form_group_contact google google3 capv3',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Secret Key v3'),
                            'name' => 'ETS_CTF7_SECRET_KEY_V3',
                            'required' => true,
                            'form_group_class' => 'form_group_contact google google3 capv3',
                        ),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Contact alias'),
    						'name' => 'ETS_CFT7_CONTACT_ALIAS',
    						'required' => true,
                            'lang'=>true,
                            'validate'=> 'isLinkRewrite',
                            'default'=>'contact-form',
                            'form_group_class'=>'form_group_contact other_setting',
    					),
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_URL_SUBFIX',
                            'label'=> $this->l('Use URL suffix'),
                            'values' => array(
                    			array(
                    				'id' => 'ETS_CTF_URL_SUBFIX_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'ETS_CTF_URL_SUBFIX_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>0,
                            'form_group_class'=>'form_group_contact other_setting',
                            'desc' => $this->l('Add ".html" to the end of form page URL. Set this to "Yes" if your product pages are ended with ".html". Set this to "No", if product pages are NOT ended with ".html"'),
                        ), 
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_ENABLE_TMCE',
                            'label'=> $this->l('Enable TinyMCE editor'),
                            'values' => array(
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_TMCE_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_TMCE_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>0,
                            'form_group_class'=>'form_group_contact other_setting',
                            'desc' => $this->l('Set this to "Yes" will allow you to enable rich text editor for textarea fields when compiling contact forms'),
                        ),
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_ENABLE_HOOK_SHORTCODE',
                            'label'=> $this->l('Enable Shortcode & Contact form in Prestashop hook'),
                            'values' => array(
                                array(
                                    'id' => 'ETS_CTF7_ENABLE_HOOK_SHORTCODE_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'ETS_CTF7_ENABLE_HOOK_SHORTCODE_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'default'=>0,
                            'form_group_class'=>'form_group_contact other_setting',
                        ),
                        array(
                            'type' => 'textarea',
                            'name'=>'ETS_CTF7_IP_BLACK_LIST',
                            'label'=> $this->l('IP blacklist (IPs to block)'),
                            'desc' => $this->l('Enter exact IP or IP pattern using "*", each IP/IP pattern on a line. For example: 69.89.31.226, 69.89.31.*, *.226, etc.'),
                            'form_group_class'=>'form_group_contact black_list',
                        ),
                        array(
                            'type' => 'textarea',
                            'name'=>'ETS_CTF7_EMAIL_BLACK_LIST',
                            'label'=> $this->l('Email blacklist (emails to block)'),
                            'desc' => $this->l('Enter exact email address or email pattern using "*", each email/email pattern on a line. For example: example@mail.ru,*@mail.ru, *@qq.com, etc.'),
                            'form_group_class'=>'form_group_contact black_list',
                        ),
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_URL_NO_ID',
                            'label'=> $this->l('Remove form ID on URL'),
                            'values' => array(
                                array(
                                    'id' => 'ETS_CTF7_URL_NO_ID_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'ETS_CTF7_URL_NO_ID_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'default'=>1,
                            'form_group_class'=>'form_group_contact other_setting',
                            'desc' => $this->l('Make URLs more friendly '),
                        ),
                        array(
                            'type' => 'hidden',
                            'name'=>'ETS_CTF7_NUMBER_MESSAGE',
                            'label'=> $this->l('Number of messages displayed per message page in back office'),
                            'default'=>20,
                            'validate'=>'isUnsignedId',
                            'form_group_class'=>'form_group_contact other_setting',
                        ),
    
                    ),
    				'submit' => array(
    					'title' => $this->l('Save'),
    				)
    			),
    		);
        }
        if($type=='email_fields')
        {
            return array(
                'form' => array(
    				'legend' => array(
    					'title' => $this->l('Email template'),
    					'icon' => 'icon-file-text-o'
    				),
                    'id_form'=>'module_form_email_template',
    				'input' => array(
                        array(
                            'type' => 'switch',
                            'name'=>'ETS_CTF7_ENABLE_TEAMPLATE',
                            'label'=> $this->l('Enable email template'),
                            'values' => array(
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_RECAPTCHA_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'ETS_CTF7_ENABLE_RECAPTCHA_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>1,
                            'form_group_class'=>'template',
                            'desc' => $this->l('Disable this option if you would like to send simple email without HTML/CSS styles'),
                        ), 
                        array(
                            'type'=>'textarea',
                            'label'=> $this->l('Main email template'),
                            'name'=>'ETS_CTF_TEMPLATE_1',
                            'lang'=>true,
                            'required'=>true,
                            'autoload_rte'=>true,
                            'default'=> !$config ? '':Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_contactform7/views/templates/hook/mail_template.tpl'),
                            'form_group_class'=>'template template2',
                            'desc'=> !$config ? '': $this->l('Available shortcodes:').$this->module->displayText('{shop_name}','span','').','.
                                $this->module->displayText('{shop_logo}','span','').','.
                                $this->module->displayText('{message_content}','span','').','.
                                $this->module->displayText('{shop_url}','span',''),
                        ),
                        array(
                            'type'=>'textarea',
                            'label'=> $this->l('Mail 2 template'),
                            'name'=>'ETS_CTF_TEMPLATE_2',
                            'lang'=>true,
                            'required'=>true,
                            'autoload_rte'=>true,
                            'form_group_class'=>'template template2',
                            'default'=> !$config ? '': Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_contactform7/views/templates/hook/mail_template2.tpl'),
                            'desc'=> !$config ? '': $this->l('Available shortcodes:'). $this->module->displayText('{shop_name}','span','').','.
                                $this->module->displayText('{shop_logo}','span','').','.
                                $this->module->displayText('{message_content}','span','').','.
                                $this->module->displayText('{shop_url}','span','')
                        ),
                        array(
                            'type'=>'textarea',
                            'label'=> $this->l('Reply email template'),
                            'name'=>'ETS_CTF_TEMPLATE_3',
                            'lang'=>true,
                            'required'=>true,
                            'autoload_rte'=>true,
                            'form_group_class'=>'template template2',
                            'default'=> !$config ? '' : Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_contactform7/views/templates/hook/mail_template_reply.tpl'),
                            'desc'=> !$config ? '' : $this->l('Available shortcodes:').
                                $this->module->displayText('{shop_name}','span','').','.
                                $this->module->displayText('{shop_logo}','span','').','.
                                $this->module->displayText('{message_content}','span','').','.
                                $this->module->displayText('{shop_url}','span','')
                        ),
    				),
    				'submit' => array(
    					'title' => $this->l('Save'),
    				)
    			),
            );
        }
        if($type=='contact_fields')
        {
            return array(
    			'form' => array(
    				'legend' => array(
    					'title' => $id_contact ? $this->l('Edit contact form') : $this->l('Add contact form'),
    					'icon' => $id_contact ? 'icon-pencil-square-o' : 'icon-pencil-square-o'
    				),
    				'input' => array(
    					array(
    						'type' => 'text',
    						'label' => $this->l('Title'),
    						'name' => 'title',
    						'required' => true,
    						'lang' => true,
                            'form_group_class'=>'form_group_contact form',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'textarea',
    						'label' => $this->l('Form editor'),
    						'name' => 'short_code',
    						'required' => true,
    						'lang' => true,
                            'id'=>'wpcf7-form',
                            'class'=>'wpcf7-form',
                            'default'=>(
                                $this->module->displayText('Your Name (required) [text* your-name]','label','')."\n".
                                $this->module->displayText('Your Email (required) [email* your-email]','label','')."\n".
                                $this->module->displayText('Subject (required) [text* your-subject]','label','')."\n".
                                $this->module->displayText('Your Message (required) [textarea* your-message]','label','')."\n".
                            '[submit "Send"]'),
                            'form_group_class'=>'form_group_contact form short_code',
                            'cols'=>'200',
                            'rows' =>'10',  
                            'validate' => 'isCleanHtml'                      
    					),
                        array(
                            'type' => 'switch',
                            'name'=>'save_message',
                            'label'=> $this->l('Save messages'),
                            'values' => array(
                    			array(
                    				'id' => 'save_message_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'save_message_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>1,
                            'form_group_class'=>'form_group_contact general_settings',
                            'desc' => $this->l('Save customer messages to "Messages" tab.'),
                        ),
                        array(
                            'type' => 'switch',
                            'name'=>'save_attachments',
                            'label'=> $this->l('Save attachments'),
                            'values' => array(
                    			array(
                    				'id' => 'save_attachments_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'save_attachments_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'desc' => $this->l('Save attached files on your server, you can download the files in "Messages" tab. Enable this option is useful but it will take some of your hosting disk space to store the files. You can set this to "No" if it is not necessary for saving files on server because the files will be also sent to your email inbox'),
                            'default'=>1,
                            'form_group_class'=>'form_group_contact general_settings general_settings4',
                        ),
                        array(
                            'type' => 'switch',
                            'name'=>'star_message',
                            'label'=> $this->l('Star messages from this contact form'),
                            'values' => array(
                    			array(
                    				'id' => 'star_message_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'star_message_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>0,
                            'form_group_class'=>'form_group_contact general_settings general_settings4',
                            'desc' => $this->l('Highlight messages sent from this contact form in the "Messages" tab by a yellow star'),
                        ),
                        array(
                            'type' => 'switch',
                            'name'=>'open_form_by_button',
                            'label'=> $this->l('Open form by button'),
                            'values' => array(
                    			array(
                    				'id' => 'open_form_by_button_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'open_form_by_button_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'form_group_class'=>'form_group_contact general_settings',
                            'desc' => $this->l('Display a button (hide the form initially), when customer click on the button, it will open the form via a popup'),
                        ),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Button label'),
    						'name' => 'button_label',
                            'lang'=>true,
                            'default' => $this->l('Open contact form'),
                            'form_group_class'=>'form_group_contact general_settings general_settings2',
                            'validate' => 'isCleanHtml'
    					),
                        array(
                            'type' => 'switch',
                            'name'=>'enable_form_page',
                            'label'=> $this->l('Enable separate form page'),
                            'values' => array(
                    			array(
                    				'id' => 'enable_form_page_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'enable_form_page_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'default'=>1,
                            'form_group_class'=>'form_group_contact seo',
                            'desc' => $this->l('Besides displaying the form using shortcode, custom hook and default Prestashop hooks, you can also create a specific web page to display the form'),
                        ),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Contact alias'),
    						'name' => 'title_alias',
                            'lang'=>true,
                            'form_group_class'=>'form_group_contact seo seo3',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Meta title'),
    						'name' => 'meta_title',
                            'lang'=>true,
                            'form_group_class'=>'form_group_contact seo seo3',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'tags',
    						'label' => $this->l('Meta key words'),
    						'name' => 'meta_keyword',
                            'lang'=>true,
                            'form_group_class'=>'form_group_contact seo seo3',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'textarea',
    						'label' => $this->l('Meta description'),
    						'name' => 'meta_description',
                            'lang'=>true,
                            'form_group_class'=>'form_group_contact seo seo3',
                            'validate' => 'isCleanHtml'
    					),
                        array(
                            'type'=>'checkbox',
                            'name'=>'hook',
                            'label'=> $this->l('Preserved display position (default Prestashop hooks)'),
                            'values' => array(
                                'query'=>array(
                                    array(
                                        'name'=>$this->l('Header - top navigation'),
                                        'val'=>'nav_top',
                                        'id'=>'nav_top',
                                    ),
                                    array(
                                        'name'=>$this->l('Header - main header'),
                                        'val'=>'header',
                                        'id'=>'header',
                                    ),
                                    array(
                                        'name'=>$this->l('Top'),
                                        'val'=>'displayTop',
                                        'id' =>'displayTop',
                                    ),
                                    array(
                                        'name'=>$this->l('Home'),
                                        'val'=>'home',
                                        'id' =>'home',
                                    ),
                                    array(
                                        'name'=>$this->l('Left column'),
                                        'val'=>'left_column',
                                        'id' =>'left_column',
                                    ),
                                    array(
                                        'name'=>$this->l('Right column'),
                                        'val'=>'right_column',
                                        'id' =>'right_column',
                                    ),
                                     array(
                                        'name'=>$this->l('Footer page'),
                                        'val'=>'footer_page',
                                        'id' =>'footer_page',
                                    ),
                                    array(
                                        'name'=>$this->l('Product page - below product images'),
                                        'val'=>'product_thumbs',
                                        'id'=>'product_thumbs',
                                    ),
                                    array(
                                        'name'=>$this->l('Product page - Footer'),
                                        'val'=>'product_footer',
                                        'id'=>'product_footer',
                                    ),
                                    array(
                                        'name' => $this->l('Checkout page'),
                                        'val'=>'checkout_page',
                                        'id'=>'checkout_page',
                                    ),
                                    array(
                                        'name' => $this->l('Login page'),
                                        'val'=>'login_page',
                                        'id'=>'login_page',
                                    ),
                                ),
                                'id' => 'id',
                    			'name' => 'name'
                            ),
                            'desc' => $this->l('Besides using shortcode, custom hook and a separated page to display the contact form, you can also display contact form on default Prestashop pre-defined hooks'),
                            'form_group_class'=>'form_group_contact general_settings form_hook '.((int)Configuration::get('ETS_CTF7_ENABLE_HOOK_SHORTCODE') ? '' :'hide').' '
                        ),
                        array(
                    		'type' => 'switch',
                    		'label' => $this->l('Activate contact form'),
                    		'name' => 'active',
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
                    		),                        
                            'default'=>1,
                            'form_group_class'=>'form_group_contact general_settings',
                    	),
                        array(
                            'type' => 'field_form',
                            'label' => $this->l('Form field'),
                            'name' => 'field_form',
                            'lang'=>true,
                            'form_group_class'=>'form_group_contact mail hidden',
                            'default' => '[your-name][your-email][your-subject][your-message]',
                            'validate' => 'isCleanHtml'
                        ),
                        array(
    						'type' => 'text',
    						'label' => $this->l('To'),
    						'name' => 'email_to',
                            'required' => true,
                            'form_group_class'=>'form_group_contact mail',
                            'default' => Configuration::get('PS_SHOP_NAME').' <'.Configuration::get('PS_SHOP_EMAIL').'>',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Bcc'),
    						'name' => 'bcc',
                            'form_group_class'=>'form_group_contact mail',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('From'),
    						'name' => 'email_from',
                            'form_group_class'=>'form_group_contact mail',
                            'default'=> '[your-name] <'.(Configuration::get('PS_MAIL_METHOD')==2? Configuration::get('PS_MAIL_USER'): Configuration::get('PS_SHOP_EMAIL')).'>',
                            'desc' => $this->l('This should be an authorized email address. Normally it is your shop SMTP email (if your website is enabled with SMTP) or an email associated with your website domain name (if your website uses default Mail() function to send emails)'),
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Subject'),
    						'name' => 'subject',
                            'lang'=>true,
                            'required' => true,
                            'form_group_class'=>'form_group_contact mail',
                            'default' => '[your-subject]',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Reply to'),
    						'name' => 'additional_headers',
                            'form_group_class'=>'form_group_contact mail',
                            'default' => '[your-name] <[your-email]>',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'textarea',
    						'label' => $this->l('Message body'),
    						'name' => 'message_body',
                            'lang'=> true,
                            'autoload_rte'=>true,
                            'form_group_class'=>'form_group_contact mail',
                            'validate' => 'isCleanHtml',
                            'default' => $this->module->displayText('From: [your-name] ([your-email])','p','').
                                        $this->module->displayText('Subject: [your-subject]','p','').
                                        $this->module->displayText('Message Body: [your-message]','p','').
                                        $this->module->displayText('-- This e-mail was sent from a contact form on'.Configuration::get('PS_SHOP_NAME'),'p',''),
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('File attachments'),
    						'name' => 'file_attachments',
                            'form_group_class'=>'form_group_contact mail',
                            'desc' => $this->l('*Note: You need to enter respective mail-tags for the file form-tags used in the "Form editor" into this field in order to receive the files via email as well as "Messages" tab.'),
                            'validate' => 'isCleanHtml'
    					),
                        array(
                    		'type' => 'switch',
                    		'label' => $this->l('Use mail 2'),
                    		'name' => 'use_email2',
                    		'values' => array(
                    			array(
                    				'id' => 'use_email2_on',
                    				'value' => 1,
                    				'label' => $this->l('Yes')
                    			),
                    			array(
                    				'id' => 'use_email2_off',
                    				'value' => 0,
                    				'label' => $this->l('No')
                    			)
                    		),
                            'desc'=> $this->l('Mail (2) is an additional mail template often used as an autoresponder.'),
                            'form_group_class'=>'form_group_contact mail',
                    	),
                        array(
    						'type' => 'text',
    						'label' => $this->l('To'),
    						'name' => 'email_to2',
                            'form_group_class'=>'form_group_contact mail mail2',
                            'default' =>  '[your-name] <[your-email]>',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Bcc'),
    						'name' => 'bcc2',
                            'form_group_class'=>'form_group_contact mail mail2',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('From'),
    						'name' => 'email_from2',
                            'form_group_class'=>'form_group_contact mail mail2',
                            'default'=> '[your-name] <'.(Configuration::get('PS_MAIL_METHOD')==2? Configuration::get('PS_MAIL_USER'): Configuration::get('PS_SHOP_EMAIL')).'>',
                            'desc' => $this->l('This should be an authorized email address. Normally it is your shop SMTP email (if your website is enabled with SMTP) or an email associated with your website domain name (if your website uses default Mail() function to send emails)'),
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Subject'),
    						'name' => 'subject2',
                            'lang'=>true,
                            'required' => true,
                            'form_group_class'=>'form_group_contact mail mail2',
                            'default' => 'Your email has been sent',
                            'validate' => 'isCleanHtml'
                            
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Reply to'),
    						'name' => 'additional_headers2',
                            'form_group_class'=>'form_group_contact mail mail2',
                            'default' => Configuration::get('PS_SHOP_NAME').' <'.Configuration::get('PS_SHOP_EMAIL').'>',
                            'validate' => 'isCleanHtml'
    					),
                        array(
    						'type' => 'textarea',
    						'label' => $this->l('Message body'),
    						'name' => 'message_body2',
                            'lang'=> true,
                            'autoload_rte'=>true,
                            'form_group_class'=>'form_group_contact mail mail2',
                            'validate' => 'isCleanHtml',
                            'default' => $this->module->displayText('From: [your-name] ([your-email])','p','').
	                            $this->module->displayText('Subject: [your-subject]','p','').
	                            $this->module->displayText('Message Body: [your-message]','p','').
	                            $this->module->displayText('-- This e-mail was sent from a contact form on'.Configuration::get('PS_SHOP_NAME'),'p','')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('File attachments'),
    						'name' => 'file_attachments2',
                            'form_group_class'=>'form_group_contact mail mail2',
                            'validate' => 'isCleanHtml',
                            'desc' => $this->l('*Note: You need to enter respective mail-tags for the file form-tags used in the "Form editor" into this field in order to receive the files via email.'),
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Notification when message was sent successfully'),
    						'name' => 'message_mail_sent_ok',
                            'lang'=> true,
                            'default'=> $this->l('Thank you for your message. It has been sent.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Notification when message failed to send'),
    						'name' => 'message_mail_sent_ng',
                            'lang'=> true,
                            'default' =>$this->l('There was an error trying to send your message. Please try again later.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Validation errors occurred'),
    						'name' => 'message_validation_error',
                            'lang'=> true,
                            'default' =>$this->l('One or more fields have an error. Please check and try again.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Submission was referred to as spam'),
    						'name' => 'message_spam',
                            'lang'=> true,
                            'default' =>$this->l('There was an error trying to send your message. Please try again later.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('There are terms that the sender must accept'),
    						'name' => 'message_accept_terms',
                            'lang'=> true,
                            'default' =>$this->l('You must accept the terms and conditions before sending your message.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('There is a field that the sender must fill in'),
    						'name' => 'message_invalid_required',
                            'lang'=> true,
                            'default' =>$this->l('The field is required.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('There is a field with input that is longer than the maximum allowed length'),
    						'name' => 'message_invalid_too_long',
                            'lang'=> true,
                            'default' =>$this->l('The field is too long.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('There is a field with input that is shorter than the minimum allowed length'),
    						'name' => 'message_invalid_too_short',
                            'lang'=> true,
                            'default' =>$this->l('The field is too short.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Date format that the sender entered is invalid'),
    						'name' => 'message_invalid_date',
                            'lang'=> true,
                            'default' =>$this->l('The date format is incorrect.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Date is earlier than minimum limit'),
    						'name' => 'message_date_too_early',
                            'lang'=> true,
                            'default' =>$this->l('The date is before the earliest one allowed.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Date is later than maximum limit'),
    						'name' => 'message_date_too_late',
                            'lang'=> true,
                            'default' =>$this->l('The date is after the latest one allowed.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Uploading a file fails for any reason'),
    						'name' => 'message_upload_failed',
                            'lang'=> true,
                            'default' =>$this->l('There was an unknown error uploading the file.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Uploaded file is not allowed for file type'),
    						'name' => 'message_upload_file_type_invalid',
                            'lang'=> true,
                            'default' =>$this->l('You are not allowed to upload files of this type.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Sender does not enter the correct answer to the quiz'),
    						'name' => 'message_quiz_answer_not_correct',
                            'lang'=> true,
                            'default' =>$this->l('The answer to the quiz is incorrect.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Uploaded file is too large'),
    						'name' => 'message_upload_file_too_large',
                            'lang'=> true,
                            'default' =>$this->l('The file is too big.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Uploading a file fails for PHP error'),
    						'name' => 'message_upload_failed_php_error',
                            'lang'=> true,
                            'default' =>$this->l('There was an error uploading the file.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Number format that the sender entered is invalid'),
    						'name' => 'message_invalid_number',
                            'lang'=> true,
                            'default' =>$this->l('The number format is invalid.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Number is smaller than minimum limit'),
    						'name' => 'message_number_too_small',
                            'lang'=> true,
                            'default' =>$this->l('The number is smaller than the minimum allowed.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Number is larger than maximum limit'),
    						'name' => 'message_number_too_large',
                            'lang'=> true,
                            'default' =>$this->l('The number is larger than the maximum allowed'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Email address that the sender entered is invalid'),
    						'name' => 'message_invalid_email',
                            'lang'=> true,
                            'default' =>$this->l('The e-mail address entered is invalid.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('URL that the sender entered is invalid'),
    						'name' => 'message_invalid_url',
                            'lang'=> true,
                            'default' =>$this->l('The URL is invalid.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Telephone number that the sender entered is invalid'),
    						'name' => 'message_invalid_tel',
                            'lang'=> true,
                            'default' =>$this->l('The telephone number is invalid.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Message IP is in blacklist'),
    						'name' => 'message_ip_black_list',
                            'lang'=> true,
                            'default' =>$this->l('You are not allowed to submit this form. Please contact webmaster for more information.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Message Email is in blacklist'),
                            'name' => 'message_email_black_list',
                            'lang'=> true,
                            'default' =>$this->l('You are not allowed to submit this form. Please contact webmaster for more information.'),
                            'validate' => 'isCleanHtml',
                            'form_group_class'=>'form_group_contact message',
	                        'placeholder' => $this->l('Leave blank to show the default value')
                        ),
                        array(
    						'type' => 'text',
    						'label' => $this->l('Captcha entered is invalid'),
    						'name' => 'message_captcha_not_match',
                            'lang'=> true,
                            'default' =>$this->l('Your entered code is incorrect.'),
                            'form_group_class'=>'form_group_contact message',
                            'validate' => 'isCleanHtml',
	                        'placeholder' => $this->l('Leave blank to show the default value')
    					),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Display "Thank you" page after form submission'),
                            'name' => 'thank_you_active',
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
                            ),
                            'default' => 0,
                            'form_group_class' => 'form_group_contact thank_you thank_you_active',
                        ),
                        array(
                            'label' => $this->l('"Thank you" page'),
                            'type' => 'select',
                            'name' => 'thank_you_page',
                            'options' => array(
                                'query'=>array(
                                    array(
                                        'name' => $this->l('Default page'),
                                        'thank_page' => 'thank_page_default',
                                    ),
                                    array(
                                        'name' => $this->l('Custom URL'),
                                        'thank_page' => 'thank_page_url',
                                    ),
                                ),
                                'id' => 'thank_page',
                                'name' => 'name',
                            ),
                            'default' => 'thank_page_url',
                            'form_group_class' => 'form_group_contact thank_you thank_you_page',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Title'),
                            'name' => 'thank_you_page_title',
                            'lang' => true,
                            'required' => $this->module->getConfigContactFormByKey('thank_you_page', 'thank_page_url') == 'thank_page_default',
                            'class' => 'title_tk_page',
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                            'default' => $this->l('Thanks for submitting the form'),
                            'validate' => 'isCleanHtml',
                        ),
                        array(
                            'type' => 'text',
                            'label' => 'Page alias',
                            'lang' => true,
                            'name' => 'thank_you_alias',
                            'class' => 'alias_tk_page',
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                            'default' => $this->l('thanks-for-submitting-the-form'),
                            'validate' => 'isCleanHtml',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Content'),
                            'name' => 'thank_you_message',
                            'lang' => true,
                            'required' => $this->module->getConfigContactFormByKey('thank_you_page', 'thank_page_url') == 'thank_page_default',
                            'default' => $this->l('Thank you for contacting us. This message is to confirm that you have successfully submitted the contact form. We\'ll get back to you shortly.'),
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                            'class' => 'rte',
                            'autoload_rte' => true,
                            'validate' => 'isCleanHtml',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Custom URL'),
                            'name' => 'thank_you_url',
                            'lang' => true,
                            'required' => true,
                            'placeholder'=>$this->l('https://example.com/thank-you.html'),
                            'default' => $this->module->getConfigContactFormByKey('thank_you_page', 'thank_page_url') == 'thank_page_url' ? '#' : '',
                            'desc' => $this->l('Customer will be redirected to this URL after submitting the form successfully'),
                            'form_group_class' => 'form_group_contact thank_you thank_you_url',
                            'validate' => 'isCleanHtml',
                        ),
    				),
    				'submit' => array(
    					'title' => $this->l('Save'),
    				),
                    'buttons' => array(
                        array(
                            'type'=>'submit',
                            'id' =>'submitSaveAndStayContact',
                            'name'=>'submitSaveAndStayContact',
                            'icon' =>'process-icon-save',
                            'class'=>'pull-right',
                            'title'=> $this->l('Save and stay'),
                        ),
                        array(
                            'id' =>'backListContact',
                            'href'=> defined('_PS_ADMIN_DIR_')? 'index.php?controller=AdminContactFormContactForm&token='.Tools::getAdminTokenLite('AdminContactFormContactForm'):'#',
                            'icon' =>'process-icon-cancel',
                            'class'=>'pull-left',
                            'title'=> $this->l('Cancel'),
                        )
                    )
    			),
    		);
        }
    }
    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
    }
    public static function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$alt = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null) {
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
		    if($alt)
			    $text .=' alt="'.$alt.'"';
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
	    }
	    return $text;
    }
    public static function getListHooks() {
    	return array(
		    'actionOutputHTMLBefore',
		    'contactForm7LeftBlok',
		    'displayBackOfficeHeader',
		    'displayContactForm7',
		    'displayHeader',
		    'displayHome',
		    'moduleRoutes',
		    'displayNav2',
		    'displayNav',
		    'displayTop',
		    'displayLeftColumn',
		    'displayFooter',
		    'displayRightColumn',
		    'displayProductAdditionalInfo',
		    'displayFooterProduct',
		    'displayAfterProductThumbs',
		    'displayRightColumnProduct',
		    'displayLeftColumnProduct',
		    'displayShoppingCartFooter',
		    'displayCustomerAccountForm',
		    'displayCustomerLoginFormAfter',
		    'displayBackOfficeFooter'
	    );
    }
    public static function getSubTabs() {
    	$module = new Ets_contactform7();
    	return array(
		    array(
			    'class_name' => 'AdminContactFormContactForm',
			    'tab_name' => $module->l('Contact forms'),
			    'icon'=>'icon icon-envelope-o'
		    ),
		    array(
			    'class_name' => 'AdminContactFormMessage',
			    'tab_name' => $module->l('Messages'),
			    'icon'=>'icon icon-comments',
		    ),
		    array(
			    'class_name' => 'AdminContactFormEmail',
			    'tab_name' => $module->l('Email templates'),
			    'icon'=>'icon icon-file-text-o',
		    ),
		    array(
			    'class_name' => 'AdminContactFormImportExport',
			    'tab_name' => $module->l('Import/Export'),
			    'icon'=>'icon icon-exchange',
		    ),
		    array(
			    'class_name' => 'AdminContactFormIntegration',
			    'tab_name' => $module->l('Integration'),
			    'icon'=>'icon icon-cogs',
		    ),
		    array(
			    'class_name' => 'AdminContactFormStatistics',
			    'tab_name' => $module->l('Statistics'),
			    'icon'=>'icon icon-line-chart',
		    ),
		    array(
			    'class_name' => 'AdminContactFormHelp',
			    'tab_name' => $module->l('Help'),
			    'icon'=>'icon icon-question-circle',
		    ),
	    );
    }
    public static function installDb()
    {
        $res = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_contact` (
          `id_contact` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `email_to` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `bcc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `email_from` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `exclude_lines` int(11) NOT NULL,
          `use_html_content` int(11) NOT NULL,
          `use_email2` int(11) NOT NULL,
          `email_to2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `bcc2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `email_from2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `additional_headers` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `additional_headers2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `exclude_lines2` int(11) NOT NULL,
          `use_html_content2` int(11) NOT NULL,
          `id_employee` int(1) NOT NULL,
          `save_message` int(1) NOT NULL,
          `save_attachments` INT(1) NOT NULL,
          `star_message` INT(1) NOT NULL,
          `open_form_by_button` INT (1),
          `file_attachments2` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `file_attachments` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `hook` VARCHAR(222),
          `thank_you_active` INT(1) NOT NULL,
          `thank_you_page` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
          `active` INT(1),
          `enable_form_page` INT(1),
          `position` INT(11),
          `date_add` date NOT NULL,
          `date_upd` date NOT NULL,
          PRIMARY KEY (`id_contact`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_contact_lang` (
          `id_contact` int(11) NOT NULL,
          `id_lang` int(11) NOT NULL,
          `title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `title_alias` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `meta_title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `meta_keyword` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `meta_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `button_label` VARCHAR(222),
          `short_code` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `field_form` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `template_mail` text NOT NULL,
          `subject` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_body` text CHARACTER SET utf8 COLLATE utf8_general_ci	,
          `subject2` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_body2` text CHARACTER SET utf8 COLLATE utf8_general_ci	,
          `message_mail_sent_ok` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_mail_sent_ng` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_validation_error` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_spam` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_accept_terms` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_required` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_too_long` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_too_short` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_date_too_early` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL, 
          `message_invalid_date` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_date_too_late` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_upload_failed` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_upload_file_type_invalid` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_upload_file_too_large` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_quiz_answer_not_correct` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_email` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_url` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_tel` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `additional_settings` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_upload_failed_php_error` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_invalid_number` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_number_too_small` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_number_too_large` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_captcha_not_match` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_ip_black_list` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `message_email_black_list` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `thank_you_page_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `thank_you_message` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `thank_you_alias` VARCHAR(100) CHARACTER SET utf8 NOT NULL,
          `thank_you_url` text CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL
        )ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_contact_shop` (
          `id_contact` int(11) NOT NULL,
          `id_shop` int(11) NOT NULL
        )ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &=Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_contact_message`(
          `id_contact_message` int(11) unsigned NOT NULL AUTO_INCREMENT ,
          `id_contact` int(11) NOT NULL,
          `id_customer` INT (11) NOT NULL,
          `replied` INT(1) NOT NULL,
          `readed` INT(1) NOT NULL,
          `special` INT(1) NOT NULL,
          `subject` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `sender` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `body` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `recipient` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `attachments` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `reply_to` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
          `date_add` DATETIME NOT NULL,
          `date_upd` DATETIME NOT NULL,
          PRIMARY KEY (`id_contact_message`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_contact_message_shop` (
          `id_contact_message` int(11) NOT NULL,
          `id_shop` int(11) NOT NULL 
        )ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_message_reply`(
            `id_ets_ctf_message_reply` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
            `id_contact_message` INT(11) NOT NULL,
            `id_employee` INT(11) NOT NULL,
            `content` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
            `reply_to` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
            `subject` text CHARACTER SET utf8 COLLATE utf8_general_ci	 NOT NULL,
            `attachment` VARCHAR(50), 
            `attachment_name` VARCHAR(255),
            `date_add` date NOT NULL,
            `date_upd` date NOT NULL,
            PRIMARY KEY (`id_ets_ctf_message_reply`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_ctf_log`(
            `ip` varchar(50) DEFAULT NULL,
            `id_contact` INT(11) NOT NULL,
            `browser` varchar(70) DEFAULT NULL,
            `id_customer` INT (11) DEFAULT NULL,
            `datetime_added` datetime NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        return $res;
    }
    public static function uninstallDb()
    {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_contact');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_contact_lang');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_contact_shop');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_contact_message');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_contact_message_shop');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_message_reply');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_ctf_log');
        return $res;
    }
    public static function createIndex()
    {
        $sqls= array();
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_lang` ADD PRIMARY KEY (`id_contact`, `id_lang`)';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_message` ADD INDEX (`id_contact`), ADD index(`id_customer`),ADD index(`replied`),ADD index(`readed`),ADD index(`special`)';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_message_shop` ADD PRIMARY KEY (`id_contact_message`, `id_shop`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_ctf_contact_shop` ADD PRIMARY KEY (`id_contact`, `id_shop`)';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_log` ADD INDEX (`id_contact`),ADD INDEX (`id_customer`)';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_ctf_message_reply` ADD INDEX (`id_contact_message`),ADD INDEX (`id_employee`)';
        foreach($sqls as $sql)
        {
            Db::getInstance()->execute($sql);
        }
        return true;
    }
 }