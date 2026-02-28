<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormLog extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'log';
        $this->tpl = 'configure-log.tpl';
    }

    public function renderForm()
    {
        $url = Tools::getShopDomainSsl(true) . $this->module->getPathUri() . 'logs/';
        $params = '?token=' . $this->module->secure_key . '&id_shop=' . Context::getContext()->shop->id;

        $this->module->boSmartyAssign([
            'logs' => [
                [
                    'title' => $this->l('General Log'),
                    'desc' => false,
                    'url' => $url . 'logs.php' . $params,
                    'comments' => $this->l('Some logs are informative.'),
                ],
            ],
        ]);

        return parent::renderForm();
    }
}
