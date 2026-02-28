<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormDashboard extends RgPuNoModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'dashboard';
        $this->tpl = 'configure-dashboard.tpl';
    }

    public function renderForm()
    {
        $source = isset($this->module->module_key) && $this->module->module_key ? 'addons' : 'rolige';

        $this->module->boSmartyAssign([
            'displayName' => $this->module->displayName,
            'description' => $this->module->description,
            'author' => $this->module->author,
            'author_link' => RgPuNoTools::getLink('author', $this->module),
            'module_link' => RgPuNoTools::getLink('module', $this->module),
            'partner_link' => RgPuNoTools::getLink('partner'),
            'source' => $source,
            'products_marketing' => RgPuNoTools::getProductsMarketing($this->module->name, $source),
        ]);

        return parent::renderForm();
    }
}
