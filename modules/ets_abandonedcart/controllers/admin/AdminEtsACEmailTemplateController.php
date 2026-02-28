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


require_once(dirname(__FILE__) . '/AdminEtsACFormController.php');

class AdminEtsACEmailTemplateController extends AdminEtsACFormController
{
    public $keep_old_image = null;
    public $template_type = [];
    public $type_of_campaign = [];
    public $fields_settings = array();

    public function __construct()
    {
        header('Referrer-Policy: no-referrer');
        $this->table = 'ets_abancart_email_template';
        $this->className = 'EtsAbancartEmailTemplate';
        $this->list_id = $this->table;
        $this->lang = true;
        $this->show_form_cancel_button = false;
        $this->list_no_link = true;

        $this->addRowAction('edittmp');
        $this->addRowAction('view');
        $this->addRowAction('copytmp');
        $this->addRowAction('exporttmp');
        $this->addRowAction('deletetmp');

        parent::__construct();

        $this->tpl_folder = 'common/';

        $this->_select = 'IF(a.thumbnail is NOT NULL AND TRIM(a.thumbnail)!=\'\', a.thumbnail,\'thumbnail.jpg\') as `thumbnail`, b.name';
        $this->_where = 'AND a.id_shop = ' . (int)$this->context->shop->id;

        $this->template_type = [
            'email' => $this->l('Email reminder (shopping cart reminder)', 'AdminEtsACEmailTemplateController'),
            'customer' => $this->l('Custom emails and newsletter', 'AdminEtsACEmailTemplateController'),
            'both' => $this->l('Both Email reminder, custom emails and newsletter', 'AdminEtsACEmailTemplateController'),
        ];
        $this->type_of_campaign = [
            'with_discount' => $this->l('Campaign with discount', 'AdminEtsACEmailTemplateController'),
            'without_discount' => $this->l('Campaign without discount', 'AdminEtsACEmailTemplateController'),
            'both' => $this->l('Both', 'AdminEtsACEmailTemplateController')
        ];
        $this->fields_list = array(
            'id_ets_abancart_email_template' => array(
                'title' => $this->l('ID', 'AdminEtsACEmailTemplateController'),
                'type' => 'int',
                'filter_key' => 'a!id_ets_abancart_email_template',
                'class' => 'fixed-width-xs center',
            ),
            'thumbnail' => array(
                'title' => $this->l('Thumbnail', 'AdminEtsACEmailTemplateController'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'callback' => 'displayThumbnail',
                'align' => 'center',
                'class' => 'fixed-width-lg',
            ),
            'name' => array(
                'title' => $this->l('Name', 'AdminEtsACEmailTemplateController'),
                'type' => 'text',
                'filter_key' => 'b!name',
            ),
            'template_type' => array(
                'title' => $this->l('Used for reminder type?', 'AdminEtsACEmailTemplateController'),
                'type' => 'select',
                'list' => $this->template_type,
                'callback' => 'displayTemplateType',
                'filter_key' => 'a!template_type'
            ),
            'type_of_campaign' => array(
                'title' => $this->l('Which type of campaign is this template available for?', 'AdminEtsACEmailTemplateController'),
                'type' => 'select',
                'list' => $this->type_of_campaign,
                'callback' => 'displayTypeOfCampaign',
                'filter_key' => 'a!type_of_campaign'
            ),
        );
        $this->bulk_actions = array(
            'duplicateEmailTemplate' => array(
                'text' => $this->l('Duplicate selection', 'AdminEtsACEmailTemplateController'),
                'icon' => 'icon-copy',
                'confirm' => $this->l('Do you want to duplicate selected emails templates?', 'AdminEtsACEmailTemplateController')
            ),
        );
    }

    public function displayTemplateType($type)
    {
        return $type && isset($this->template_type[$type]) && $this->template_type[$type] ? $this->template_type[$type] : null;
    }

    public function displayTypeOfCampaign($type_of)
    {
        return $type_of && isset($this->type_of_campaign[$type_of]) && $this->type_of_campaign[$type_of] ? $this->type_of_campaign[$type_of] : null;
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->toolbar_btn['import'] = array(
            'href' => '#',
            'desc' => $this->l('Import new template', 'AdminEtsACEmailTemplateController'),
        );
    }

    public function initToolbarTitle()
    {
        if (!$this->display || $this->display == 'view') {
            $this->toolbar_title = array($this->l('Email templates', 'AdminEtsACEmailTemplateController', null, null, false));
            if (is_array($this->meta_title)) {
                $this->meta_title = array($this->l('Email templates', 'AdminEtsACEmailTemplateController', null, null, false));
            }
            if ($filter = $this->addFiltersToBreadcrumbs()) {
                $this->toolbar_title[] = $filter;
            }
        } else {
            parent::initToolbarTitle();
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['import_email_template'] = array(
                'href' => '#',
                'desc' => $this->l('Import new template', 'AdminEtsACEmailTemplateController', null, null, false),
                'icon' => 'process-icon-import',
            );
            $this->page_header_toolbar_btn['new_email_template'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new template', 'AdminEtsACEmailTemplateController', null, null, false),
                'icon' => 'process-icon-new',
            );
        } else {
            $this->page_header_toolbar_btn['back_to_list'] = array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->l('Back to list', 'AdminEtsACEmailTemplateController', null, null, false),
                'icon' => 'process-icon-back',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        if (Tools::isSubmit('duplicate' . $this->list_id)) {
            if ($this->access('edit')) {
                $this->action = 'duplicateObject';
            } else {
                $this->errors[] = $this->l('You do not have permission to add this.', 'AdminEtsACEmailTemplateController');
            }
        }
        parent::initProcess();
        if (Tools::isSubmit('submitAdd' . $this->table))
            $this->display = 'edit';
        if ($this->display == 'add' || $this->display == 'edit') {
            $template_type_array = [];
            if ($this->template_type) {
                foreach ($this->template_type as $id => $item) {
                    $template_type_array[$id] = [
                        'id' => $id,
                        'label' => $item,
                        'value' => $id,
                    ];
                }
            }
            $type_of_campaign_array = [];
            if ($this->type_of_campaign) {
                foreach ($this->type_of_campaign as $id => $item) {
                    $type_of_campaign_array[$id] = [
                        'id' => $id,
                        'label' => $item,
                        'value' => $id,
                    ];
                }
            }
            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Email template editor', 'AdminEtsACEmailTemplateController'),
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsACEmailTemplateController'),
                ),
                'input' => array(
                    'email_content' => array(
                        'name' => 'email_content',
                        'label' => $this->l('Email content', 'AdminEtsACEmailTemplateController'),
                        'type' => 'textarea',
                        'autoload_rte' => 'true',
                        'lang' => true,
                        'required' => true,
                        'form_group_class' => 'ets_ac_config_popup_content',
                        'desc_type' => 'customer'
                    ),
                    'name' => array(
                        'name' => 'name',
                        'label' => $this->l('Name', 'AdminEtsACEmailTemplateController'),
                        'type' => 'text',
                        'lang' => true,
                        'required' => true,
                        'form_group_class' => 'ets_ac_item_panel_2 ets_ac_config_popup_item'
                    ),
                    'template_type' => array(
                        'name' => 'template_type',
                        'label' => $this->l('Used for reminder type?', 'AdminEtsACEmailTemplateController'),
                        'type' => 'radio',
                        'values' => $template_type_array,
                        'default_value' => 'email',
                        'form_group_class' => 'ets_ac_item_panel_2 ets_ac_config_popup_item'
                    ),
                    'type_of_campaign' => array(
                        'name' => 'type_of_campaign',
                        'label' => $this->l('Which type of campaign is this template available for?', 'AdminEtsACEmailTemplateController'),
                        'type' => 'radio',
                        'values' => $type_of_campaign_array,
                        'default_value' => 'both',
                        'form_group_class' => 'ets_ac_item_panel_2 ets_ac_config_popup_item'
                    ),

                    'thumbnail' => array(
                        'name' => 'thumbnail',
                        'label' => $this->l('Email template thumbnail', 'AdminEtsACEmailTemplateController'),
                        'type' => 'file',
                        'display_image' => true,
                        'hint' => $this->l('Upload a thumbnail for email template from your computer.', 'AdminEtsACEmailTemplateController'),
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb', 'AdminEtsACEmailTemplateController'), (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                        'form_group_class' => 'form-group-file ets_ac_item_panel_2 ets_ac_config_popup_item'
                    ),
                ),
            );

            $this->fields_settings = array(
                'legend' => array(
                    'title' => $this->l('Email template editor', 'AdminEtsACEmailTemplateController'),
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'AdminEtsACEmailTemplateController'),
                ),
                'input' => array(
                    'email_content' => array(
                        'name' => 'email_content',
                        'label' => $this->l('Email content', 'AdminEtsACEmailTemplateController'),
                        'type' => 'textarea',
                        'autoload_rte' => 'true',
                        'lang' => true,
                        'required' => true,
                    ),
                    'name' => array(
                        'name' => 'name',
                        'label' => $this->l('Name', 'AdminEtsACEmailTemplateController'),
                        'type' => 'text',
                        'lang' => true,
                        'required' => true,
                    ),
                    'template_type' => array(
                        'name' => 'template_type',
                        'label' => $this->l('Used for reminder type?', 'AdminEtsACEmailTemplateController'),
                        'type' => 'radio',
                        'values' => $template_type_array,
                        'default_value' => 'email'
                    ),
                    'type_of_campaign' => array(
                        'name' => 'type_of_campaign',
                        'label' => $this->l('Which type of campaign is this template available for?', 'AdminEtsACEmailTemplateController'),
                        'type' => 'radio',
                        'values' => $type_of_campaign_array,
                        'default_value' => 'both'
                    ),

                    'thumbnail' => array(
                        'name' => 'thumbnail',
                        'label' => $this->l('Email template thumbnail', 'AdminEtsACEmailTemplateController'),
                        'type' => 'file',
                        'display_image' => true,
                        'hint' => $this->l('Upload a thumbnail for email template from your computer.', 'AdminEtsACEmailTemplateController'),
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb', 'AdminEtsACEmailTemplateController'), (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                        'form_group_class' => 'form-group-file'
                    ),
                ),
            );
        }
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        if (isset($this->context->shop->id) && $this->context->shop->id) {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'label' => $this->l('Shop ID', 'AdminEtsACEmailTemplateController'),
                'name' => 'id_shop',
                'default_value' => $this->context->shop->id,
            );
        }
        $this->fields_form['buttons'] = array(
            'back' => array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'title' => $this->l('Back to list', 'AdminEtsACEmailTemplateController'),
                'icon' => 'process-icon-back',
                'class' => 'btn-back-to-list'
            ),
        );

        if ($this->object->id && $this->object->thumbnail != '' && @file_exists(($image = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($this->object->folder_name ?: $this->object->id) . '/' . $this->object->thumbnail))) {
            $imageType = Tools::strtolower(Tools::substr(strrchr($this->object->thumbnail, '.'), 1));
            $image_url = ImageManager::thumbnail(
                $image,
                $this->object->thumbnail,
                220,
                $imageType,
                true,
                true
            );
            $image_size = file_exists($image) ? filesize($image) / 1000 : false;
            $this->fields_form['input']['thumbnail']['image'] = $image_url;
            $this->fields_form['input']['thumbnail']['size'] = $image_size;
            $this->fields_form['input']['thumbnail']['delete_url'] = self::$currentIndex . '&field=thumbnail' . ($this->object->id ? '&' . $this->identifier . '=' . $this->object->id : '') . '&token=' . $this->token;
        }

        $this->tpl_form_vars['short_codes'] = EtsAbancartDefines::getInstance()->getFields('short_codes');
        $this->tpl_form_vars['short_code_urls'] = EtsAbancartDefines::getInstance()->getFields('short_code_urls');
        if (!(int)Tools::getValue($this->identifier)) {
            $this->tpl_form_vars['warning_add_new'] = self::$currentIndex . '&token=' . $this->token;
        }

        $this->tpl_form_vars['lead_forms'] = EtsAbancartForm::getAllForms(false, true);
        $this->tpl_form_vars['field_types'] = EtsAbancartField::getInstance()->getFieldType();
        $this->tpl_form_vars['is17Ac'] = $this->module->is17;
        $this->tpl_form_vars['maxSizeUpload'] = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');

        self::$currentIndex .= (Tools::isSubmit('add' . $this->list_id) ? '&add' . $this->list_id : '') . (Tools::isSubmit('update' . $this->list_id) ? '&update' . $this->list_id : '');

        return parent::renderForm();
    }


    public function getFieldsValue($obj)
    {
        parent::getFieldsValue($obj);
        if ($this->fields_value && $obj instanceof EtsAbancartEmailTemplate) {
            foreach (Language::getLanguages(false) as $lang) {
                $contentLang = '';
                if (Tools::isSubmit('submitAdd' . $this->table)) {
                    $contentLang = Tools::getValue('email_content_' . $lang['id_lang']);
                    if (!Validate::isCleanHtml($contentLang)) {
                        $contentLang = '';
                    }
                } elseif (is_array($obj->temp_path) && isset($obj->temp_path[$lang['id_lang']]) && $obj->temp_path[$lang['id_lang']]) {
                    $contentLang = EtsAbancartEmailTemplate::getBodyMailTemplate(_ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $obj->folder_name . '/' . $obj->temp_path[$lang['id_lang']], $obj);
                }
                $this->fields_value['email_content'][$lang['id_lang']] = $contentLang;
            }

        }

        return $this->fields_value;
    }

    public function renderView()
    {
        if (!($email_template = $this->loadObject(true))) {
            return;
        }

        $this->context->smarty->assign(array(
            'content' => $email_template instanceof EtsAbancartEmailTemplate ? $email_template->getEmailContent() : '',
            'templateType' => $email_template->template_type,
            'processBackToList' => self::$currentIndex . '&token=' . $this->token,
            'duplicateLink' => self::$currentIndex . '&token=' . $this->token . '&id_ets_abancart_email_template=' . (int)Tools::getValue('id_ets_abancart_email_template') . '&duplicateets_abancart_email_template&inViewTemplate=1',
        ));

        return $this->createTemplate($this->override_folder . 'view.tpl')->fetch();
    }

    protected function copyFromPost(&$object, $table)
    {
        if ($object->id && $object->thumbnail)
            $this->keep_old_image = $object->thumbnail;

        parent::copyFromPost($object, $table);

        if (!trim($object->thumbnail) && trim($this->keep_old_image) != '')
            $object->thumbnail = $this->keep_old_image;

        if ($object instanceof EtsAbancartEmailTemplate) {
            $email_content = [];
            $languages = Language::getLanguages(false);
            if ($languages) {
                foreach ($languages as $l) {
                    $email_content[(int)$l['id_lang']] = Tools::getValue('email_content_' . $l['id_lang']);
                }
            }
            $object->updateContentToSave($email_content);
            if (isset($_FILES['thumbnail']) && isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name']) {
                $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $allows = array('jpg', 'png', 'gif', 'jpeg');
                $maxSize = EtsAbancartTools::getPostMaxSizeBytes();
                if (!in_array($ext, $allows)) {
                    $this->errors[] = $this->l('File uploaded is not image', 'AdminEtsACEmailTemplateController');
                } elseif ($maxSize < $_FILES['thumbnail']['size']) {
                    $this->errors[] = $this->l('Image upload is too large', 'AdminEtsACEmailTemplateController');
                } elseif (!Validate::isFileName(str_replace(' ', '_', $_FILES['thumbnail']['name']))) {
                    $this->errors[] = $this->l('Filename is invalid. Filename must only contain letters, numbers, underscores, periods, and hyphens.', 'AdminEtsACEmailTemplateController');
                }
                if (!$this->errors) {
                    $mailDir = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($object->folder_name ?: $object->id);
                    $thumbnailName = 'thumbnail_email.' . $ext;
                    if (is_dir($mailDir)) {
                        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $mailDir . '/' . $thumbnailName);
                        if ($object->thumbnail) {
                            EtsAbancartHelper::unlink($mailDir . '/' . $object->thumbnail);
                        }
                        $object->thumbnail = $thumbnailName;
                    }
                }
            }
        }
    }

    public function afterUpdate($object)
    {
        if ($object->id && @file_exists(_PS_IMG_DIR_ . Ets_abandonedcart::ETS_ABC_UPLOAD_IMG_DIR . $this->keep_old_image) && trim($this->keep_old_image) != trim($object->thumbnail)) {
            @unlink(_PS_IMG_DIR_ . Ets_abandonedcart::ETS_ABC_UPLOAD_IMG_DIR . $this->keep_old_image);
            $this->keep_old_image = null;
        }

        return true;
    }

    public function processDuplicateObject()
    {
        $object = new EtsAbancartEmailTemplate((int)Tools::getValue('id_ets_abancart_email_template'));
        if (!$object->id) {
            $this->errors[] = $this->l('Object not found!', 'AdminEtsACEmailTemplateController');
        }
        if (!count($this->errors)) {
            $object->is_init = 0;
            $languages = Language::getLanguages(false);

            $folderName = 'template_' . uniqid();
            EtsAbancartTools::createMailUploadFolder();
            mkdir(_ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName, 0755);
            @copy(dirname(__FILE__) . '/index.php', _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName . '/index.php');
            EtsAbancartTools::copyFolder(_ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($object->folder_name ?: $object->id), _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName);

            foreach ($languages as $l) {
                $object->name[$l['id_lang']] .= ' [' . $this->l('duplicate', 'AdminEtsACEmailTemplateController') . ']';
            }
            $object->folder_name = $folderName;
            $copy_object_id = $object->id;
            $object->id = null;
            if (!$object->add()) {
                $this->errors[] = sprintf($this->l('Duplicate object(#%s) error!', 'AdminEtsACEmailTemplateController'), $copy_object_id);
            } else
                $this->redirect_after = self::$currentIndex . '&conf=19&token=' . $this->token;
        }
        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            $this->display = 'list';
            return false;
        }

        return $object;
    }

    public function processDuplicate($temp_id = null)
    {
        if ($temp_id) {
            $object = new EtsAbancartEmailTemplate($temp_id);
        } else {
            $object = $this->loadObject();
        }
        if (!$object || !$object->id)
            return;

        $object->is_init = 0;
        $languages = Language::getLanguages(false);
        $folderName = 'template_' . uniqid();
        EtsAbancartTools::createMailUploadFolder();
        mkdir(_ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName, 0755);
        @copy(dirname(__FILE__) . '/index.php', _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName . '/index.php');
        EtsAbancartTools::copyFolder(_ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($object->folder_name ?: $object->id), _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . $folderName);

        foreach ($languages as $l) {
            $object->name[$l['id_lang']] .= ' [' . $this->l('duplicate', 'AdminEtsACEmailTemplateController') . ']';
        }
        $object->folder_name = $folderName;

        unset($object->id);

        return $object->add(true, true);
    }

    public function exportTemplate()
    {
        $idTemp = (int)Tools::getValue('id_ets_abancart_email_template');
        if ($idTemp && ($emailTemp = new EtsAbancartEmailTemplate($idTemp)) && $emailTemp->id) {
            $mailDir = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($emailTemp->folder_name ?: $emailTemp->id);
            if (is_dir($mailDir)) {
                $cacheDir = _PS_CACHE_DIR_ . '/ets_abandonedcart';
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755);
                }
                $cacheDir = $cacheDir . '/';
                if (is_dir($mailDir)) {
                    $fileName = 'email_template_' . microtime(true) . '.zip';
                    $zip = new ZipArchive();
                    if ($zip->open($cacheDir . $fileName, ZipArchive::OVERWRITE | ZipArchive::CREATE) === true) {

                        $files = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($mailDir),
                            RecursiveIteratorIterator::LEAVES_ONLY
                        );

                        foreach ($files as $file) {
                            if (!$file->isDir()) {
                                $filePath = $file->getRealPath();
                                $relativePath = Tools::substr($filePath, Tools::strlen($mailDir) + 1);

                                if ($file->getFilename() !== 'index.php') {
                                    $zip->addFile($filePath, $relativePath);
                                }
                            }
                        }

                        $zip->close();
                        if (!is_file($cacheDir . $fileName)) {
                            $this->errors[] = $this->module->l(sprintf($this->l('Could not create %s', 'AdminEtsACEmailTemplateController'), $cacheDir . $fileName));
                        }
                        if (!$this->errors) {
                            if (ob_get_length() > 0) {
                                ob_end_clean();
                            }
                            $zipName = $fileName;
                            if (isset($emailTemp->name[$this->context->language->id]) && $emailTemp->name[$this->context->language->id]) {
                                $zipName = $emailTemp->name[$this->context->language->id] . '.zip';
                            } elseif ($emailTemp->folder_name) {
                                $zipName = $emailTemp->folder_name . '.zip';
                            }
                            if(!Validate::isFileName($zipName)) {
                                $zipName = EtsAbancartHelper::sanitizeFileName($zipName);
                            }
                            ob_start();
                            header('Pragma: public');
                            header('Expires: 0');
                            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                            header('Cache-Control: public');
                            header('Content-Description: File Transfer');
                            header('Content-type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="' . $zipName . '"');
                            header('Content-Transfer-Encoding: binary');
                            ob_end_flush();
                            EtsAbancartHelper::readfile($cacheDir . $fileName);
                            EtsAbancartHelper::unlink($cacheDir . $fileName);
                            exit;
                        }
                    }
                }
            }
        }
        $this->errors[] = $this->l('Email template does not exist', 'AdminEtsACEmailTemplateController');
    }

    public function ajaxProcessUploadedImage()
    {
        if ($this->access('edit')) {

            $this->loadObject(true);
            $key = trim(Tools::getValue('key'));
            if (!$key ||
                !Validate::isCleanHtml($key) ||
                !property_exists($this->object, $key)
            ) {
                return false;
            }
            $file_dest = $this->module->getLocalPath() . 'views/img/upload/';
            $json = array();

            if (isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                $salt = Tools::substr(sha1(microtime()), 0, 10);
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                $image = @file_exists($file_dest . Tools::strtolower($_FILES[$key]['name'])) || Tools::strtolower($_FILES[$key]['name']) == $this->object->$key ? $salt . '-' . Tools::strtolower($_FILES[$key]['name']) : Tools::strtolower($_FILES[$key]['name']);
                $file_name = $file_dest . $image;
                if (@file_exists($file_name)) {
                    $this->errors[] = $this->l('File name already exists. Try to rename the file and upload again', 'AdminEtsACEmailTemplateController');
                } else {
                    $image_size = @getimagesize($_FILES[$key]['tmp_name']);
                    if (!$this->errors && isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name']) && !empty($image_size) && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                        if ($errors = ImageManager::validateUpload($_FILES[$key]))
                            $this->errors[] = $errors;
                        elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                            $this->errors[] = $this->l('Cannot upload file', 'AdminEtsACEmailTemplateController');
                        elseif (!ImageManager::resize($temp_name, $file_name, null, null, $type))
                            $this->errors[] = $this->l('An error occurred during the image upload process.', 'AdminEtsACEmailTemplateController');
                        EtsAbancartHelper::unlink($temp_name);
                    } else
                        $this->errors[] = $this->l('The uploaded image format is not valid. Please try again', 'AdminEtsACEmailTemplateController');
                }
                if (!count($this->errors)) {
                    $json['image'] = $image;
                    $this->context->smarty->assign(array(
                        'upf_link' => $this->mPath . 'views/img/upload/' . $image,
                        'upf_name' => $key,
                    ));
                    $json['html'] = $this->createTemplate('upload-img.tpl')->fetch();
                }
            } else {
                $this->errors[] = $this->l('File does not exist', 'AdminEtsACEmailTemplateController');
            }
            $json['errors'] = count($this->errors) > 0 ? $this->module->displayError($this->errors) : '';
            if (!$json['errors'] && $this->object->id && $this->object->$key != '') {
                $old_image = $this->object->$key;
                $this->object->$key = $image;
                if ($this->object->update() && file_exists($file_dest . $old_image))
                    unlink($file_dest . $old_image);
            }
            $this->toJson($json);
        }
    }

    public function ajaxProcessDeleteImage()
    {
        if ($this->access('edit')) {
            $this->loadObject(true);
            $field = trim(Tools::getValue('field'));
            if (!Validate::isCleanHtml($field) ||
                !property_exists($this->object, $field)
            ) {
                $this->errors[] = sprintf($this->l('Field "%s" does not exist.', 'AdminEtsACEmailTemplateController'), $field);
            }
            if (!$this->errors) {
                $thumbnailDir = _ETS_AC_MAIL_UPLOAD_DIR_ . '/' . ($this->object->folder_name ?: $this->object->id) . '/' . $this->object->{$field};
                if (@file_exists($thumbnailDir)) {
                    EtsAbancartHelper::unlink($thumbnailDir);
                    $this->object->{$field} = '';
                    if (!$this->object->update(true))
                        $this->errors[] = $this->l('Cannot delete image.', 'AdminEtsACEmailTemplateController');
                } else
                    $this->errors[] = $this->l('Image does not exist.', 'AdminEtsACEmailTemplateController');
            }
            $hasError = count($this->errors) > 0 ? true : false;
            $this->toJson(array(
                'errors' => $hasError,
                'msg' => !$hasError ? $this->l('Delete image successfully', 'AdminEtsACEmailTemplateController') : $this->l('Delete image failed.', 'AdminEtsACEmailTemplateController'),
            ));
        }
    }

    public function displayThumbnail($thumb, $tpl_vars)
    {
        $this->context->smarty->assign(array(
            'thumb_link' => $thumb != 'thumbnail.jpg' ? _ETS_AC_MAIL_UPLOAD_ . '/' . ($tpl_vars['folder_name'] ?: $tpl_vars['id_ets_abancart_email_template']) . '/' . $tpl_vars['thumbnail'] : false,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'thumb_title' => !empty($tpl_vars['name']) ? $tpl_vars['name'] : $this->l('Thumbnail', 'AdminEtsACEmailTemplateController')
        ));

        return $this->createTemplate('thumb.tpl')->fetch();
    }

    public function displayEdittmpLink($token, $id)
    {
        if (($object = new $this->className($id)) && !$object->is_init) {
            if (!isset(self::$cache_lang['edittmp'])) {
                self::$cache_lang['edittmp'] = $this->l('Edit', 'AdminEtsACEmailTemplateController');
            }
            $this->context->smarty->assign(array(
                'href' => self::$currentIndex .
                    '&' . $this->identifier . '=' . $id .
                    '&update' . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => self::$cache_lang['edittmp'],
            ));

            return $this->context->smarty->fetch('helpers/list/list_action_edit.tpl');
        }
    }

    public function displayCopytmpLink($token, $id)
    {
        if (!isset(self::$cache_lang['copytmp'])) {
            self::$cache_lang['copytmp'] = $this->l('Duplicate', 'AdminEtsACEmailTemplateController');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex .
                '&' . $this->identifier . '=' . $id .
                '&duplicate' . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['copytmp'],
        ));

        return $this->createTemplate('helpers/list/list_action_copy.tpl')->fetch();
    }

    public function displayExporttmpLink($token, $id)
    {
        if (!isset(self::$cache_lang['exporttmp'])) {
            self::$cache_lang['exporttmp'] = $this->l('Export', 'AdminEtsACEmailTemplateController');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex .
                '&' . $this->identifier . '=' . $id .
                '&exporttmp' . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['exporttmp'],
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/ets_abandonedcart/views/templates/hook/list_action_export_temp.tpl');
    }

    public function displayDeletetmpLink($token, $id)
    {
        if (($object = new $this->className($id)) && !$object->is_init) {
            if (!isset(self::$cache_lang['deletetmp'])) {
                self::$cache_lang['deletetmp'] = $this->l('Delete', 'AdminEtsACEmailTemplateController');
            }
            $this->context->smarty->assign(array(
                'href' => self::$currentIndex .
                    '&' . $this->identifier . '=' . $id .
                    '&delete' . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => self::$cache_lang['deletetmp'],
                'confirm' => $this->l('Delete selected items?', 'AdminEtsACEmailTemplateController'),
            ));

            return $this->context->smarty->fetch('helpers/list/list_action_delete.tpl');
        }

    }

    public function renderList()
    {
        $PS_ATTACHMENT_MAXIMUM_SIZE = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
        if (!$this->isCached(($template = 'views/templates/hook/modal_import_email_temp.tpl'), EtsAbancartCache::getCachedId('modal_import_email', null, $PS_ATTACHMENT_MAXIMUM_SIZE))) {
            try {
                $linkConfig = $this->context->link->getAdminLink('AdminAdminPreferences', true, array('route' => 'admin_administration'));
            } catch (Exception $ex) {
                $linkConfig = $this->context->link->getAdminLink('AdminAdminPreferences');
            }
            $this->context->smarty->assign(array(
                'linkImportEmailTemplate' => $this->context->link->getAdminLink('AdminEtsACEmailTemplate'),
                'maxSizeUpload' => $PS_ATTACHMENT_MAXIMUM_SIZE,
                'linkConfig' => $linkConfig
            ));
        }

        return parent::renderList() . $this->module->display($this->module->getLocalPath(), $template, EtsAbancartCache::getCachedId('modal_import_email', null, $PS_ATTACHMENT_MAXIMUM_SIZE));
    }

    public function postProcess()
    {
        parent::postProcess();
        if (isset($this->context->cookie->msg_bulk_email_temp_success) && $this->context->cookie->msg_bulk_email_temp_success) {
            $this->confirmations[] = $this->context->cookie->msg_bulk_email_temp_success;
            $this->context->cookie->msg_bulk_email_temp_success = null;
        } elseif (isset($this->context->cookie->msg_bulk_email_temp_error) && $this->context->cookie->msg_bulk_email_temp_error) {
            $this->errors[] = $this->context->cookie->msg_bulk_email_temp_error;
            $this->context->cookie->msg_bulk_email_temp_error = null;
        }
        if (Tools::isSubmit('etsAcDuplicateEmailTemp')) {
            if ($this->processDuplicate()) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Duplicate email template successfully', 'AdminEtsACEmailTemplateController')
                )));
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Duplicate email template failed', 'AdminEtsACEmailTemplateController')
            )));
        }
        if (Tools::isSubmit('exporttmpets_abancart_email_template')) {
            $this->exportTemplate();
        }

        if (Tools::getIsset('submitBulkduplicateEmailTemplateets_abancart_email_template')) {
            $temps = Tools::getValue('ets_abancart_email_templateBox');
            if ($temps && is_array($temps)) {
                $temps = array_map('intval', $temps);
                if ($temps) {
                    $success = false;
                    $count = 0;
                    foreach ($temps as $temp_id) {
                        if ($this->processDuplicate($temp_id)) {
                            $success = true;
                            $count++;
                        }
                    }
                    if ($success) {
                        $this->context->cookie->msg_bulk_email_temp_success = sprintf($this->l('Duplicated %s email(s) template successfully', 'AdminEtsACEmailTemplateController'), $count);
                    }
                }

            }
            if (!$temps) {
                $this->context->cookie->msg_bulk_email_temp_error = $this->l('No email template selected to duplicate', 'AdminEtsACEmailTemplateController');
            } else
                $this->context->cookie->msg_bulk_email_temp_error = $this->l('Duplicate email template failed', 'AdminEtsACEmailTemplateController');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsACEmailTemplate'));
        }
        if (Tools::isSubmit('etsAcImportEmailTemplate')) {
            $this->uploadEmailTemplate();
        }

    }

    public function uploadEmailTemplate()
    {
        $templateFile = isset($_FILES['email_template']) ? $_FILES['email_template'] : null;
        $fileName = $templateFile ? $templateFile['name'] : '';
        $ext = $fileName ? pathinfo($fileName, PATHINFO_EXTENSION) : '';
        $fileSize = $templateFile ? $templateFile['size'] : 0;
        $maxFileSize = (float)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024;
        if (!$templateFile) {
            $this->errors[] = $this->l('Template file is required', 'AdminEtsACEmailTemplateController');
        } elseif ($ext !== 'zip') {
            $this->errors[] = $this->l('Template file must be a zip file', 'AdminEtsACEmailTemplateController');
        } elseif (!Validate::isFileName($fileName)) {
            $this->errors[] = $this->l('Template file name is invalid. Filename must only contain letters, numbers, underscores, periods, and hyphens.', 'AdminEtsACEmailTemplateController');
        } elseif ($fileSize && (float)$fileSize > $maxFileSize) {
            $this->errors[] = $this->l('Template file size is too large', 'AdminEtsACEmailTemplateController');
        }

        if (!$this->errors) {
            $cacheDir = _PS_CACHE_DIR_ . 'ets_abandonedcart';
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0777);
            }

            $zipName = 'template_' . microtime(true) . '.zip';
            $zipPath = $cacheDir . '/' . $zipName;
            $uploader = new Uploader('email_template');
            $uploader->setMaxSize(1048576000);
            $uploader->setAcceptTypes(array('zip'));
            $uploader->setSavePath($cacheDir);
            $fileUpload = $uploader->process($zipName);

            if ($fileUpload[0]['error'] === 0) {
                if (!Tools::ZipTest($cacheDir . '/' . $zipName))
                    $this->errors[] = $this->l('Zip file seems to be broken', 'AdminEtsACEmailTemplateController');
            } else {
                $this->errors[] = $fileUpload[0]['error'];
            }

            if (!@file_exists($zipPath))
                $this->errors[] = $this->l('Zip file doesn\'t exist', 'AdminEtsACEmailTemplateController');
            if (!$this->errors) {
                $dataDir = $cacheDir . '/temp_data_' . uniqid();
                if (!Tools::ZipExtract($zipPath, $dataDir)) {
                    $this->errors[] = $this->l('Cannot extract zip data', 'AdminEtsACEmailTemplateController');
                } else {
                    $scanDirs = scandir($dataDir);
                    $listDirName = array();
                    $listFileName = array();
                    if ($scanDirs) {
                        foreach ($scanDirs as $item) {
                            if ($item == '.' || $item == '..') {
                                continue;
                            }
                            if (is_dir($dataDir . '/' . $item)) {
                                $listDirName[] = $item;
                            }
                            if (is_file($dataDir . '/' . $item)) {
                                $listFileName[] = $item;
                            }
                        }
                    }
                    if ($listDirName && count($listDirName) == 1 && !$listFileName) {
                        $templateName = Tools::strtolower(str_replace(' ', '_', $listDirName[0]));
                        if (EtsAbancartEmailTemplate::isTemplateNameExists($templateName)) {
                            $templateName = $templateName . ((int)EtsAbancartEmailTemplate::getMaxId() + 1);
                        }
                        if (!preg_match('/^[a-zA-Z0-9_\-]$/')) {
                            $this->errors[] = $this->l('Sub folder name in zip file is invalid. Please rename and try again', 'AdminEtsACEmailTemplateController');
                        }
                        if (EtsAbancartEmailTemplate::isTemplateNameExists($templateName)) {
                            $this->errors[] = $this->l('Sub folder name in zip file is invalid. Please rename and try again', 'AdminEtsACEmailTemplateController');
                        }
                    } else {
                        $templateName = '' . (int)EtsAbancartEmailTemplate::getMaxId() + 1;
                    }

                    if (!$this->errors && ($files = EtsAbancartEmailTemplate::getTemplateFile($dataDir))) {
                        $defaultLang = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
                        @rename($files[0], dirname($files[0]) . '/index_' . $defaultLang['iso_code'] . '.html');
                        EtsAbancartEmailTemplate::removeLinkInContentMail(dirname($files[0]) . '/index_' . $defaultLang['iso_code'] . '.html');
                        $languages = Language::getLanguages(false);
                        foreach ($languages as $lang) {
                            if (!file_exists(dirname($files[0]) . '/index_' . $lang['iso_code'] . '.html'))
                                @copy(dirname($files[0]) . '/index_' . $defaultLang['iso_code'] . '.html', dirname($files[0]) . '/index_' . $lang['iso_code'] . '.html');
                        }
                        $thumbnail = null;
                        $thumbnails = glob($dataDir . '/*[.png|.jpg|.gif]');
                        if ($thumbnails) {
                            $thumbnail = str_replace($dataDir . '/', '', $thumbnails[0]);
                        }
                        if ($mailDir = EtsAbancartTools::createMailUploadFolder()) {
                            $mailDir = $mailDir . '/' . $templateName;
                            $tempPath = array();
                            foreach ($languages as $lang) {
                                $tempPath[$lang['id_lang']] = str_replace($dataDir . '/', '', dirname($files[0]) . '/index_' . $lang['iso_code'] . '.html');
                            }
                            EtsAbancartTools::copyFolder($dataDir, $mailDir);

                            EtsAbancartTools::deleteAllDataInFolder($cacheDir);

                            $emailTemp = new EtsAbancartEmailTemplate();
                            $emailTemp->id_shop = $this->context->shop->id;
                            $emailTemp->thumbnail = $thumbnail;
                            $emailTemp->template_type = 'both';
                            $emailTemp->folder_name = $templateName;
                            $emailTemp->type_of_campaign = 'both';
                            $emailTemp->is_init = 0;
                            $emailTemp->name = array();
                            $emailTemp->temp_path = array();
                            foreach (Language::getLanguages(false) as $lang) {
                                $emailTemp->name[$lang['id_lang']] = $templateName;
                                $emailTemp->temp_path[$lang['id_lang']] = $tempPath[$lang['id_lang']];
                            }
                            if ($emailTemp->add()) {
                                die(json_encode(array(
                                    'success' => true,
                                    'message' => $this->l('Email template imported successfully', 'AdminEtsACEmailTemplateController')
                                )));
                            } else {
                                $this->errors[] = $this->l('Cannot import email template', 'AdminEtsACEmailTemplateController');
                            }
                        } else {
                            $this->errors[] = $this->l('Cannot create mail directory', 'AdminEtsACEmailTemplateController');
                        }
                    } else {
                        if (!$this->errors)
                            $this->errors[] = $this->l('No template file exists', 'AdminEtsACEmailTemplateController');
                    }
                }
            }
        }

        if ($this->errors) {
            die(json_encode(array(
                'success' => false,
                'message' => $this->errors
            )));
        }

        die(json_encode(array(
            'success' => false,
            'message' => $this->l('Something went wrong', 'AdminEtsACEmailTemplateController')
        )));

    }
}