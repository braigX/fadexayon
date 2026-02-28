<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormCron extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'cron';
        $this->tpl = 'configure-cron.tpl';
    }

    public function renderForm()
    {
        $url = Tools::getShopDomainSsl(true) . $this->module->getPathUri() . 'crons/';
        $params = '?token=' . $this->module->secure_key . '&id_shop=' . Context::getContext()->shop->id;

        $this->module->boSmartyAssign([
            'crons' => [
                [
                    'title' => $this->l('Abandoned Cart Reminder'),
                    'desc' => $this->l('Cron job required for abandoned carts.'),
                    'command' => $url . 'cart_reminder.php' . $params,
                    'periodicity' => $this->l('every 30 minutes'),
                    'comments' => false,
                ],
                [
                    'title' => $this->l('Maintenance'),
                    'desc' => $this->l('Cron job required for maintenance.'),
                    'command' => $url . 'maintenance.php' . $params,
                    'periodicity' => $this->l('once per day'),
                    'comments' => false,
                ],
                [
                    'title' => $this->l('OneSignal Statistics Data Refresh'),
                    'desc' => $this->l('Cron job required to update the statistics.'),
                    'command' => $url . 'refresh_os_data.php' . $params,
                    'periodicity' => $this->l('once per hour'),
                    'comments' => false,
                ],
            ],
        ]);

        return parent::renderForm();
    }
}
