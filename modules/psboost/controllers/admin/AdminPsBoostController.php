<?php
/**
 * PSBoost Admin Controller
 * Registers the back-office tab that redirects to the module configuration page
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPsBoostController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function initContent()
    {
        // Redirect to module configuration page
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules') .
            '&configure=psboost&tab_module=administration&module_name=psboost'
        );
    }
}
