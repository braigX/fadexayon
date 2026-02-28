<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminHiGoogleConnectController extends ModuleAdminController
{
    private $secure_key;
    private $adminForms;

    public function __construct()
    {
        $this->secure_key = Tools::getValue('secure_key');
        parent::__construct();

        $this->adminForms = $this->module->adminForms;
    }

    public function init()
    {
        parent::init();

        if (!$this->ajax) {
            Tools::redirectAdmin($this->module->hiPrestaClass->getModuleUrl());
        }

        if ($this->secure_key != $this->module->secure_key) {
            $this->ajaxDie(json_encode([
                'error' => $this->l('Bad Secure Key'),
            ]));
        }
    }

    protected function ajaxRender($value = null, $controller = null, $method = null)
    {
        if (method_exists(get_parent_class($this), 'ajaxRender')) {
            return parent::ajaxRender($value, $controller, $method);
        }

        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $method = $bt[1]['function'];
        }

        /* @deprecated deprecated since 1.6.1.1 */
        Hook::exec('actionAjaxDieBefore', ['controller' => $controller, 'method' => $method, 'value' => $value]);

        /*
         * @deprecated deprecated since 1.6.1.1
         * use 'actionAjaxDie'.$controller.$method.'Before' instead
         */
        Hook::exec('actionBeforeAjaxDie' . $controller . $method, ['value' => $value]);
        Hook::exec('actionAjaxDie' . $controller . $method . 'Before', ['value' => $value]);
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        echo $value;
    }

    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (ob_get_contents()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $this->ajaxRender($value, $controller, $method);
        exit;
    }

    public function displayAjaxDisplayPositionForm()
    {
        $this->ajaxDie(json_encode([
            'content' => $this->adminForms->reanderPositionForm((int) Tools::getValue('id_position')),
        ]));
    }

    public function displayAjaxSavePositionSettings()
    {
        $this->module->savePositionSettings((int) Tools::getValue('id_position'));

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->module->l('Successfully saved'),
            'content' => $this->adminForms->renderPositionsList(),
        ]));
    }

    public function displayAjaxUpdatePositionStatus()
    {
        $id_position = (int) Tools::getValue('id_position');
        $status = ((int) Tools::getValue('status') ? 0 : 1);
        Configuration::updateValue('HI_GC_BUTTON_ACTIVE_' . $id_position, $status);

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->module->l('Status successfully changed'),
            'content' => $this->adminForms->renderPositionsList(),
        ]));
    }

    public function displayAjaxDeleteGoogleUser()
    {
        $idUser = (int) Tools::getValue('idElement');
        $user = new HiGoogleConnectUser($idUser);

        if (!$user->delete()) {
            $this->ajaxDie(json_decode([
                'error' => $this->l('We were not able to delete the user, please try again.'),
            ]));
        }

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->module->l('User Account successfully unlinked from Google'),
            'content' => $this->adminForms->renderUsersList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }

    public function displayAjaxRenderGoogleUserList()
    {
        $this->ajaxDie(json_encode([
            'error' => false,
            'content' => $this->adminForms->renderUsersList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }

    public function displayAjaxUpdateRegistrationsChart()
    {
        $dateType = Tools::getValue('dateType');
        $dateFrom = Tools::getValue('dateFrom');
        $dateTo = Tools::getValue('dateTo');

        $this->context->cookie->hiGoogleConnectChartType = $dateType;
        if ($dateFrom && $dateTo && $dateFrom <= $dateTo) {
            $this->context->cookie->hiGoogleConnectChartCustomFrom = $dateFrom;
            $this->context->cookie->hiGoogleConnectChartCustomTo = $dateTo;
        }

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->l('Chart successfully updated for selected period'),
            'registrationData' => $this->module->getRegistrationsByDate($dateType, $dateFrom, $dateTo),
        ]));
    }
}
