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
 * Class AdminYbcBlogBackupController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogBackUpController extends ModuleAdminController
{
    public $baseLink;
    public $_html = '';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogBackUp');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id, 'Import/Export'))
        {
            $this->checked = true;
            $this->_postExport();
        }

    }

    public function renderList()
    {
        if (!$this->checked)
            return $this->module->display($this->module->getLocalPath(), 'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('export'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html . $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getcontent()
    {
        $this->context->smarty->assign(array(
            'errors'=>$this->module->errors,
            'import_ok'=>$this->module->import_ok,
        ));
        return $this->module->display($this->module->getLocalPath(),'export.tpl');
    }
    private function _postExport()
    {
        if(Tools::isSubmit('submitExportBlog'))
        {
            $import= new Ybc_Blog_ImportExport();
            $import->generateArchive();
        }
        if(Tools::isSubmit('submitImportBlog'))
        {
            if(!is_dir(_YBC_BLOG_CACHE_DIR_))
                mkdir(_YBC_BLOG_CACHE_DIR_,'0755');
            $import= new Ybc_Blog_ImportExport();
            $data_import = Tools::getValue('data_import');
            $params = array(
                'data_import'=> $data_import && is_array($data_import) && Ybc_blog::validateArray($data_import) ? $data_import : array(),
                'importoverride' => (int)Tools::getValue('importoverride'),
                'keepauthorid' => (int)Tools::getValue('keepauthorid'),
                'keepcommenter' => (int)Tools::getValue('keepcommenter'),
            );
            $this->context->smarty->assign($params);
            $errors =$import->processImport(false,$params);
            if($errors)
                $this->module->errors=$errors;
            else
            {
                $this->module->import_ok=true;
                $this->module->refreshCssCustom();
                $this->module->_clearCache('*');
            }
        }
        if(Tools::isSubmit('submitImportBlogWP'))
        {
            if(!is_dir(_YBC_BLOG_CACHE_DIR_))
                mkdir(_YBC_BLOG_CACHE_DIR_,'0755');
            $import= new Ybc_Blog_ImportExport();
            $errors =$import->processImportWordPress((int)Tools::getValue('importoverridewp'));
            if($errors)
                $this->module->errors=$errors;
            else
            {
                $this->module->import_ok=true;
                $this->module->refreshCssCustom();
                $this->module->_clearCache('*');
            }
        }
    }
}