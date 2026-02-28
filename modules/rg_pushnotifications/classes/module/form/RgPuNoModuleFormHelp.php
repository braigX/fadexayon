<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormHelp extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'help';
        $this->tpl = 'configure-help.tpl';
    }

    public function renderForm()
    {
        $this->module->boSmartyAssign([
            'documentation' => [
                [
                    'lang' => $this->l('english'),
                    'link' => RgPuNoTools::getLink('docs', $this->module, 'en'),
                ],
                [
                    'lang' => $this->l('spanish'),
                    'link' => RgPuNoTools::getLink('docs', $this->module, 'es'),
                ],
            ],
            'support_link' => RgPuNoTools::getLink('support', $this->module),
            'rate_link' => RgPuNoTools::getLink('rate', $this->module),
        ]);

        return parent::renderForm();
    }
}
