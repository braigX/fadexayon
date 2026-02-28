<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class FrontController extends FrontControllerCore
{

  
    public function init()
    {
        if (Module::isInstalled('ec_seo') && Module::isEnabled('ec_seo')) {
            require_once _PS_ROOT_DIR_.'/modules/ec_seo/ec_seo.php';
            $ec_seo = new Ec_seo();
            $ec_seo->checkRedirection();
        }
        parent::init();
    }
}
