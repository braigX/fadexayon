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
 * Class Ets_SeoCronFrontModuleController.
 *
 * @property \Ets_Seo $module
 * @property \Context|\ContextCore $context
 */
class Ets_SeoCronModuleFrontController extends ModuleFrontController
{
    /** @var bool If set to true, will redirected user to login page during init function. */
    public $auth = false;

    public function init()
    {
        parent::init();
        $_getAjaxResponse = static function ($ok, $msg) {
            return json_encode(['success' => $ok, 'message' => $msg]);
        };
        $isAjax = Tools::getValue('ajax');
        $token = Tools::getValue('secure');
        $cronjobToken = Configuration::getGlobalValue('ETS_SEO_CRONJOB_TOKEN');
        $msg = '';
        if ($isAjax) {
            header('Content-Type: application/json');
        }
        if ($token !== $cronjobToken) {
            $msg = 'Token mismatch. Aborted.';
            if ($isAjax) {
                exit($_getAjaxResponse(false, $msg));
            }
            exit($msg);
        }
        if (!Module::isEnabled('ets_seo')) {
            $msg = 'Module Ets Seo not enabled. Aborted';
            if ($isAjax) {
                exit($_getAjaxResponse(false, $msg));
            }
            exit($msg);
        }
        Configuration::updateGlobalValue('ETS_SEO_TIME_LOG_CRONJOB', $timeRun = date('Y-m-d H:i:s'));
        $this->module->_clearCache('cronjob.tpl');
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $url = (new Shop($shop['id_shop']))->getBaseURL(true, true);
            $url = rtrim($url, '/').'/';
            $url .= basename($this->context->link->getModuleLink('ets_seo', 'sitemap', [], Tools::usingSecureMode(), (int)Configuration::get('PS_LANG_DEFAULT')));
            \Ets_Seo::file_get_contents('https://www.google.com/ping?sitemap=' . urlencode($url));
            $msg .= sprintf('Ping sitemap : %s . %s', $url, PHP_EOL);
        }
        $msg .= 'Saving last ran cronjob at: ' . $timeRun;
        if ($isAjax) {
            exit($_getAjaxResponse(true, nl2br($msg)));
        }
        exit($msg);
    }
}
