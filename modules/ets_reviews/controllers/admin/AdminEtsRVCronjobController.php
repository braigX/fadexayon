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

require_once(dirname(__FILE__) . '/AdminEtsRVOptionController.php');

class AdminEtsRVCronjobController extends AdminEtsRVOptionController
{
    static $send_to = [];

    public function __construct()
    {
        parent::__construct();

        $this->config = 'getAutoConfigs';
        $this->submit = 'submitAutoConfig';
    }

    public function getConfigs()
    {
        return EtsRVDefines::getInstance()->getAutoConfigs();
    }

    public function getTemplateVars($helper)
    {
        $tpl_vars = parent::getTemplateVars($helper);
        $tpl_vars['cronjobLog'] = @file_exists(($file = _PS_ROOT_DIR_ . '/var/logs/' . $this->module->name . '.cronjob.log')) ? Tools::file_get_contents($file) : '';
        $tpl_vars['url'] = $this->context->link->getAdminLink('AdminEtsRVCronjob') . '&secure=' . Configuration::getGlobalValue('ETS_RV_SECURE_TOKEN');
        $tpl_vars['path'] = '* * * * * '.(defined('PHP_BINDIR') && PHP_BINDIR && is_string(PHP_BINDIR) ? PHP_BINDIR.'/' : '').'php ' . _PS_MODULE_DIR_ . $this->module->name . '/cronjob.php secure=' . Configuration::getGlobalValue('ETS_RV_SECURE_TOKEN');

        return $tpl_vars;
    }


    protected function ajaxProcessClearLog()
    {
        if (@file_exists(($file = _PS_ROOT_DIR_ . '/var/logs/' . $this->module->name . '.cronjob.log'))) {
            if (!@unlink($file)) {
                $this->errors[] = $this->l('Cannot clear cronjob log. Please check the permission file.', 'AdminEtsRVAutomationController');
            }
        } else
            $this->errors[] = $this->l('Cronjob log is cleaned', 'AdminEtsRVAutomationController');
        $hasError = count($this->errors) > 0;

        $this->jsonRender(array(
            'errors' => $hasError,
            'msg' => $hasError ? $this->errors : $this->l('Clear cronjob log successfully', 'AdminEtsRVAutomationController'),
        ));
    }

    protected function ajaxProcessCronjobExecute()
    {
        EtsRVTools::getInstance()->runCronjob(trim(Tools::getValue('secure')));
    }
}