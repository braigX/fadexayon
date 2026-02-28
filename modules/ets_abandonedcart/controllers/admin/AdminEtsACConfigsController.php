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


require_once(dirname(__FILE__) . '/AdminEtsACOptionsController.php');

class AdminEtsACConfigsController extends AdminEtsACOptionsController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->fields_options = array(
            'title' => $this->l('Automation', 'AdminEtsACConfigsController'),
            'fields' => $this->def->getFields('configs'),
            'icon' => '',
            'submit' => array(
                'title' => $this->l('Save', 'AdminEtsACConfigsController'),
            ),
            'name' => 'leave_website'
        );
    }

    public function renderOptions()
    {
        $this->context->smarty->assign(array(
            'menuTab' => array(
                'config' => array(
                    'name' => $this->l('Configuration', 'AdminEtsACConfigsController'),
                    'icon' => '',
                ),
                'log' => array(
                    'name' => $this->l('Cronjob log', 'AdminEtsACConfigsController'),
                    'icon' => '',
                ),
            ),
            'cronjobLog' => EtsAbancartHelper::file_get_contents(_PS_ROOT_DIR_.'/var/logs/' . $this->module->name . '.cronjob.log') ?: '',
        ));

        return parent::renderOptions();
    }

    protected function ajaxProcessClearLog()
    {
        if (@file_exists(($file = _PS_ROOT_DIR_.'/var/logs/' . $this->module->name . '.cronjob.log'))) {
            if (!EtsAbancartHelper::unlink($file)) {
                $this->errors[] = $this->l('Cannot clear cronjob log. Check permission file.', 'AdminEtsACConfigsController');
            }
        } else
            $this->errors[] = $this->l('Cronjob log is cleaned', 'AdminEtsACConfigsController');
        $hasError = count($this->errors) > 0 ? true : false;

        $this->toJson(array(
            'errors' => $hasError,
            'msg' => $hasError ? implode(PHP_EOL, $this->errors) : $this->l('Clear cronjob log successfully', 'AdminEtsACConfigsController'),
        ));
    }

    protected function ajaxProcessCronjobExecute()
    {
        EtsAbancartTools::getInstance()->runCronjob(Tools::getValue('secure'), $this->context->shop->id, true);
    }

}