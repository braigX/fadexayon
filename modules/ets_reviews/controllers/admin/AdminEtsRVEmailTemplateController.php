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

if (!defined('_PS_VERSION_')) { exit; }

require_once(dirname(__FILE__) . '/AdminEtsRVBaseController.php');

class AdminEtsRVEmailTemplateController extends AdminEtsRVBaseController
{
    /**
     * @var EtsRVEmailTemplate
     */
    protected $object;

    public function __construct()
    {
        $this->table = 'ets_rv_email_template';
        $this->list_id = $this->table;
        $this->className = 'EtsRvEmailTemplate';
        $this->list_simple_header = false;
        $this->show_toolbar = false;
        $this->list_no_link = true;
        $this->lang = false;
        $this->bulk_actions = array();

        parent::__construct();

        $this->addRowAction('edit');

        $this->_default_pagination = 20;
        $this->_select = 'b.*, \'\' `content`';
        $this->_join = '
            INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` b ON a.id_ets_rv_email_template = b.id_ets_rv_email_template' . Shop::addSqlRestrictionOnLang('b') . ' AND b.id_lang = ' . (int)$this->context->language->id . ' 
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` ets ON (a.`id_ets_rv_email_template` = ets.`id_ets_rv_email_template` AND ets.id_shop=' . (int)$this->context->shop->id . ')
        ';

        Shop::addTableAssociation($this->table, ['type' => 'shop']);

        $this->fields_list = [
            'id_ets_rv_email_template' => [
                'title' => $this->l('ID', 'AdminEtsRVEmailTemplateController'),
                'align' => 'ets-rv-id_ets_rv_email_template center',
                'class' => 'ets-rv-id_ets_rv_email_template fixed-width-xs',
                'filter_key' => 'a!id_ets_rv_email_template',
            ],
            'template' => [
                'title' => $this->l('Template', 'AdminEtsRVEmailTemplateController'),
                'align' => 'ets-rv-template left',
                'class' => 'ets-rv-template fixed-width-lg',
                'filter_key' => 'a!template'
            ],
            'subject' => [
                'title' => $this->l('Subject', 'AdminEtsRVEmailTemplateController'),
                'align' => 'ets-rv-template left',
                'class' => 'ets-rv-template',
                'filter_key' => 'b!subject'
            ],
            'content' => [
                'title' => $this->l('Content', 'AdminEtsRVEmailTemplateController'),
                'align' => 'ets-rv-content left',
                'class' => 'ets-rv-content',
                'filter_key' => 'content',
                'havingFilter' => true,
                'callback' => 'displayContent'
            ],
            'active' => [
                'title' => $this->l('Is enabled', 'AdminEtsRVEmailTemplateController'),
                'align' => 'ets-rv-active center',
                'class' => 'ets-rv-active',
                'filter_key' => 'a!active',
                'type' => 'bool',
                'active' => 'status',
            ]
        ];

        $this->fields_options = [
            'general' => [
                'title' => $this->l('Email configs', 'AdminEtsRVEmailTemplateController'),
                'fields' => [
                    'ETS_RV_EMAIL_NOTIFICATIONS' => [
                        'title' => $this->l('Email address(es) to receive notifications', 'AdminEtsRVEmailTemplateController'),
                        'type' => 'text',
                        'default' => Configuration::get('PS_SHOP_MAIL'),
                        'desc' => $this->l('Emails that you want to receive notifications, separated by a comma (,)', 'AdminEtsRVEmailTemplateController'),
                        'validate' => 'isEmailListSeparatedByComma',
                    ],
                ],
                'submit' => ['title' => $this->l('Save',  'AdminEtsRVEmailTemplateController')],
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef([
            'PS_SHOP_LOGO' => EtsRVMail::getShopLogoMail($this->context->shop->id),
            'PS_SHOP_URL' => $this->context->shop->getBaseURL(),
            'ETS_RV_UNSUBSCRIBE_LABEL' => Configuration::get('ETS_RV_MAIL_UNSUBSCRIBE_TEXT', $this->context->language->id) ?: $this->l('Unsubscribe', 'AdminEtsRVEmailTemplateController'),
        ]);
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['new']['href']);
    }

    public function displayContent($content, $tr)
    {
        if (!isset($tr['template']) || trim($tr['template']) == '')
            return $content;

        $theme = ($this->module->is17 ? $this->context->shop->theme->getName() : $this->context->shop->getTheme());
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/' . $this->module->name . '/mails/',
            $this->module->getLocalPath() . 'mails/',
        );
        foreach ($basePathList as $path) {
            $iso_path = $path . $this->context->language->iso_code . '/' . $tr['template'];
            if (@file_exists($iso_path . '.html')) {
                $template = Tools::file_get_contents($iso_path . '.html');
                preg_match('/<'.'body(?:[^>]*?)>(.*?)<\/body>/s', $template, $matches);
                $content = strip_tags(isset($matches[0]) ? $matches[0] : $template);
                break;
            }
        }

        return $content !== '' ? str_replace('{tracking}', '', $content) : $content;
    }

    public function getTemplateFormVars()
    {
        $this->loadObject();
        $this->tpl_form_vars['variables'] = EtsRVTools::getVariables($this->object->template);
        $this->tpl_form_vars['short_codes'] = EtsRVTools::getShortCodesInSubject($this->object->template);
        return parent::getTemplateFormVars();
    }

    public function renderForm()
    {
        if (!$this->object = $this->loadObject())
            return false;
        $subject = EtsRVTools::getInstance()->getSubjects($this->object->template);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Email templates') . ($this->object->id > 0 ? ' #' . $this->object->id : ''),
                'icon' => 'icon-envelop'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Template'),
                    'name' => 'template',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ],
                    'default_value' => 1,
                    'desc' => isset($subject['desc']) ? $subject['desc'] : $this->l('Enable or disable this email template'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Subject'),
                    'name' => 'subject',
                    'lang' => true,
                    'required' => true,
                    'maxlength' => 500,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content_html',
                    'lang' => true,
                    'required' => true,
                    'autoload_rte' => true,
                    'form_group_class' => 'template_type html',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content_txt',
                    'lang' => true,
                    'required' => true,
                    'form_group_class' => 'template_type txt',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content full'),
                    'name' => 'content_html_full',
                    'lang' => true,
                    'form_group_class' => 'content_html_full',
                )
            )
        );

        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $this->fields_form['buttons'] = [
            'save-and-stay' => [
                'title' => $this->l('Save and stay', 'Admin.Catalog.Feature'),
                'name' => 'submitAdd' . $this->table . 'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save',
            ],
        ];

        return parent::renderForm();
    }

    public function getFieldsValue($obj)
    {
        /** @var EtsRVEmailTemplate $obj */
        parent::getFieldsValue($obj);

        $languages = Language::getLanguages(false);
        if ($languages) {
            $theme = ($this->module->is17 ? $this->context->shop->theme->getName() : $this->context->shop->getTheme());
            $basePathList = array(
                _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/' . $this->module->name . '/mails/',
                $this->module->getLocalPath() . 'mails/',
            );
            foreach ($languages as $l) {
                $id_lang = (int)$l['id_lang'];
                foreach ($basePathList as $path) {
                    $flag = false;
                    $iso_path = $path . $l['iso_code'] . '/' . $obj->template;
                    if (@file_exists($iso_path . '.html')) {
                        $template = Tools::file_get_contents($iso_path . '.html');
                        preg_match('#<'.'body(?:[^>]*?)>(.*?)<\/body>#s', $template, $matches);
                        $this->fields_value['content_html'][$id_lang] = !empty($matches[0]) ? $matches[0] : '';
                        $this->fields_value['content_html_full'][$id_lang] = preg_replace('#(<'.'body([^>]*?)>)(.*?)(<\/body>)#s', '$1@content@$4', $template);
                        $flag = true;
                    }
                    if (@file_exists($iso_path . '.txt')) {
                        $this->fields_value['content_txt'][$id_lang] = Tools::file_get_contents($iso_path . '.txt');
                    }
                    if ($flag)
                        break;
                }
            }
        }

        return $this->fields_value;
    }

    public function afterUpdate($object)
    {
        /** @var EtsRVEmailTemplate $object */
        $res = parent::afterUpdate($object);

        $languages = Language::getLanguages(false);
        if ($languages) {
            $base_dir = _PS_ROOT_DIR_ . '/themes/' . ($this->module->is17 ? $this->context->shop->theme->getName() : $this->context->shop->getTheme()) . '/modules/' . $this->module->name . '/mails/';
            if (!is_dir($base_dir))
                mkdir($base_dir, 0755, true);

            foreach ($languages as $l) {
                $id_lang = (int)$l['id_lang'];
                $object->content_html[$id_lang] = Tools::getValue('content_html_' . $id_lang);
                $object->content_txt[$id_lang] = Tools::getValue('content_txt_' . $id_lang);
                $object->content_html_full[$id_lang] = Tools::getValue('content_html_full_' . $id_lang);

                $iso_path = $base_dir . $l['iso_code'] . '/';
                if (!is_dir($iso_path))
                    mkdir($iso_path, 0755, true);

                if (isset($object->content_html[$id_lang]))
                    @file_put_contents($iso_path . $object->template . '.html', preg_replace('/@content@/', $object->content_html[$id_lang], $object->content_html_full[$id_lang]));

                if (isset($object->content_txt[$id_lang]))
                    @file_put_contents($iso_path . $object->template . '.txt', $object->content_txt[$id_lang]);
            }
        }

        return $res;
    }

    public function updateAssoShop($id_object)
    {
        if (Shop::isFeatureActive())
            return $id_object;
        return parent::updateAssoShop($id_object);
    }

    public function beforeUpdateOptions()
    {
        parent::beforeUpdateOptions();

        if ($this->fields_options) {
            $fields = $this->fields_options['general']['fields'];
            foreach ($fields as $key => $field) {
                if (isset($field['required']) && $field['required'] && !trim(Tools::getValue($key)))
                    $this->errors[] = $field['title'] . ' ' . $this->l('is required', 'AdminEtsRVEmailTemplateController');
                elseif (isset($field['validate']) && $field['validate'] && ($field_value = trim(Tools::getValue($key))) !== '') {
                    $validate = trim($field['validate']);
                    if (method_exists('Validate', $validate) && !Validate::$validate($field_value))
                        $this->errors[] = $field['title'] . ' ' . $this->l('is invalid', 'AdminEtsRVEmailTemplateController');
                    elseif (method_exists('EtsRVTools', $validate) && !EtsRVTools::$validate($field_value)) {
                        $this->errors[] = $field['title'] . ' ' . $this->l('is invalid', 'AdminEtsRVEmailTemplateController');
                    }
                }
            }
        }
    }
}