<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminRoyCustomizerController extends ModuleAdminController
{
    public function initContent()
    {
        $link = new Link();
        Tools::redirectAdmin($link->getAdminLink('AdminModules') . '&configure=roy_customizer');
    }
}
