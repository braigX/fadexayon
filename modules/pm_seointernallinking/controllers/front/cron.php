<?php
/**
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
class pm_seointernallinkingcronModuleFrontController extends ModuleFrontController
{
    public $ajax = true;
    public $display_header = false;
    public $display_footer = false;
    public $display_column_left = false;
    public $display_column_right = false;
    public function init()
    {
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('X-Robots-Tag: noindex, nofollow', true);
        header('Content-type: application/json');
        $secureKey = Configuration::getGlobalValue('PM_SIL_CRON_SECURE_KEY');
        if (empty($secureKey) || $secureKey !== Tools::getValue('secure_key')) {
            Tools::redirect('404');
            die;
        }
        $type = trim(Tools::strtolower(Tools::getValue('type')));
        die(json_encode(array(
            'result' => $this->module->runCrontab($type),
        )));
    }
}
