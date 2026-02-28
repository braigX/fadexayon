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
 * Class AdminEtsSeoCronjobSettingController.
 *
 * @property \Ets_Seo $module
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoCronjobSettingController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->fields_options = [
            'cronjob_setting' => [
                'title' => $this->module->l('Cronjob settings','AdminEtsSeoCronjobSettingController'),
                'fields' => [],
                'icon' => '',
                'submit' => [
                ],
                'buttons' => [
                ],
            ],
        ];
    }

    /**
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function _renderCronjobBlock()
    {
        if (!($cronjobToken = Configuration::getGlobalValue('ETS_SEO_CRONJOB_TOKEN'))) {
            Configuration::updateGlobalValue('ETS_SEO_CRONJOB_TOKEN', $cronjobToken = Tools::passwdGen(12));
        }
        $tpl = 'cronjob.tpl';
        $cacheId = $this->module->_getCacheId(['_renderCronjobBlock']);
        if (!$this->module->isCached($tpl, $cacheId)) {
            $this->context->smarty->assign([
                'phpBin' => (defined('PHP_BINDIR') ? PHP_BINDIR . '/' : '') . 'php ',
                'linkCronjob' => $this->context->link->getModuleLink('ets_seo', 'cron'),
                'cronjobFile' => _PS_MODULE_DIR_ . 'ets_seo/cronjob.php',
                'cronjobToken' => $cronjobToken,
            ]);
        }

        return $this->module->display($this->module->getLocalPath(), $tpl, $cacheId) . $this->renderLastRun();
    }

    /**
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function renderLastRun()
    {
        $lastRun = '';
        $needRunNow = true;
        if ($cronjob_time = Configuration::getGlobalValue('ETS_SEO_TIME_LOG_CRONJOB')) {
            $last_time = strtotime($cronjob_time);
            $time = strtotime(date('Y-m-d H:i:s')) - $last_time;
            // 7 days
            if ($time >= (7 * 24 * 60 * 60)) {
                $needRunNow = true;
            } else {
                $needRunNow = false;
            }
            if ($time > 86400) {
                $lastRun = Tools::displayDate($cronjob_time, true);
            } elseif ($time) {
                if ($hours = floor($time / 3600)) {
                    $lastRun .= $hours . ' ' . $this->module->l('hours', 'AdminEtsSeoCronjobSettingController') . ' ';
                    $time %= 3600;
                }
                if ($minutes = floor($time / 60)) {
                    $lastRun .= $minutes . ' ' . $this->module->l('minutes', 'AdminEtsSeoCronjobSettingController') . ' ';
                    $time %= 60;
                }
                if ($time) {
                    $lastRun .= $time . ' ' . $this->module->l('seconds', 'AdminEtsSeoCronjobSettingController') . ' ';
                }
                $lastRun .= $this->module->l('ago', 'AdminEtsSeoCronjobSettingController');
            }
        }
        $this->context->smarty->assign([
            'needRunNow' => $needRunNow,
            'lastRun' => $lastRun,
        ]);

        return $this->module->display($this->module->getLocalPath(), 'last-run-cronjob.tpl');
    }

    /**
     * @return void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitUpdateToken')) {
            $cronjobToken = Tools::passwdGen(12);
            Configuration::updateGlobalValue('ETS_SEO_CRONJOB_TOKEN', $cronjobToken);
            $tpl = 'cronjob.tpl';
            $cacheId = $this->module->_getCacheId(['_renderCronjobBlock'], true);
            $this->module->_clearCache($tpl, $cacheId);
            header('Content-Type: application/json');
            exit(json_encode(['value' => $cronjobToken, 'success' => true]));
        }
    }

    /**
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function renderOptions()
    {
        return $this->_renderCronjobBlock();
    }
}
