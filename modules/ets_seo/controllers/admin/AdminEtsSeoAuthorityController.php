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
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoAuthorityController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoAuthorityController extends ModuleAdminController
{
    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'general' => [
                'title' => $this->module->l('Authority', 'AdminEtsSeoAuthorityController'),
                'fields' => $seoDef->fields_config()['search_general'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoAuthorityController'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoAuthorityController');
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $ETS_SEO_SITE_OF_PERSON_OR_COMP = Tools::getValue('ETS_SEO_SITE_OF_PERSON_OR_COMP');
            $orgName = Tools::getValue('ETS_SEO_SITE_ORIG_NAME');
            $personName = Tools::getValue('ETS_SEO_SITE_PERSON_NAME');
            if ('COMPANY' == $ETS_SEO_SITE_OF_PERSON_OR_COMP && (!$orgName || !Validate::isCleanHtml($orgName))) {
                $this->errors[] = $this->module->l('Organization name is required.', 'AdminEtsSeoAuthorityController');
            } elseif ('PERSON' == $ETS_SEO_SITE_OF_PERSON_OR_COMP && (!$personName || !Validate::isCleanHtml($personName))) {
                $this->errors[] = $this->module->l('Name is required.', 'AdminEtsSeoAuthorityController');
            } else {
                $this->setDefaultValue('ETS_SEO_SITE_ORIG_LOGO');
                $this->setDefaultValue('ETS_SEO_SITE_PERSON_AVATAR');
                if (isset($_FILES['ETS_SEO_SITE_ORIG_LOGO'])) {
                    $this->checkImageUploaded('ETS_SEO_SITE_ORIG_LOGO');
                }
                if (isset($_FILES['ETS_SEO_SITE_PERSON_AVATAR'])) {
                    $this->checkImageUploaded('ETS_SEO_SITE_PERSON_AVATAR');
                }
            }
        }

        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }

    public function renderOptions()
    {
        $this->context->smarty->assign([
            'ETS_SEO_SITE_OF_PERSON_OR_COMP' => Configuration::get('ETS_SEO_SITE_OF_PERSON_OR_COMP'),
        ]);

        return parent::renderOptions();
    }

    protected function checkImageUploaded($file)
    {
        $image = $_FILES[$file];
        if (!$image['name'] || $image['error'] > 0) {
            return false;
        }

        $allowExtentions = ['png', 'jpg', 'jpeg', 'gif'];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
        $max_kb = $max_file_size * 1024 * 1024;
        if (!Validate::isFileName(str_replace([' ', '(', ')', '!', '@', '#', '+'], '_', $image['name']))) {
            $this->errors[] = sprintf($this->module->l('The file name "%s" is invalid', 'AdminEtsSeoAuthorityController'), $image['name']);
        } elseif (!in_array($ext, $allowExtentions)) {
            $this->errors[] = 'ETS_SEO_SITE_PERSON_AVATAR' == $file ? $this->module->l('The avatar is not in the correct format, accepted formats: png, jgg, jpeg, gif', 'AdminEtsSeoAuthorityController') : $this->module->l('The organization logo is not in the correct format, accepted formats: png, jgg, jpeg, gif', 'AdminEtsSeoAuthorityController');
        } elseif ($max_file_size &&  ImageManager::validateUpload($image, $max_kb)) {
            $this->errors[] = 'ETS_SEO_SITE_PERSON_AVATAR' == $file ? sprintf($this->module->l('The avatar logo is too large. Maximum size: %dMb', 'AdminEtsSeoAuthorityController'), $max_file_size) : sprintf($this->module->l('The organization logo is too large. Maximum size: %dMb', 'AdminEtsSeoAuthorityController'), $max_file_size);
        } else {
            if (($file_name = Configuration::get($file)) && file_exists(_PS_ROOT_DIR_ . '/img/social/' . $file_name)) {
                unlink(_PS_ROOT_DIR_ . '/img/social/' . $file_name);
            }
            $this->uploadLogoImage($image, $file);
        }
    }

    protected function uploadLogoImage($image, $name)
    {
        if (!$image['name'] || $image['error'] > 0) {
            return false;
        }
        $image_name = time() . rand(11111, 99999) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);

        if (move_uploaded_file($image['tmp_name'], _PS_ROOT_DIR_ . '/img/social/' . $image_name)) {
            Configuration::updateValue($name, $image_name);
            $_POST[$name] = $image_name;

            return true;
        }

        return false;
    }

    protected function setDefaultValue($key)
    {
        $requestKey = ($requestKey = Tools::getValue($key)) && Validate::isCleanHtml($requestKey) ? $requestKey : '';
        if (Configuration::get($key) && !$requestKey) {
            $_POST[$key] = Configuration::get($key);
        }
    }
}
