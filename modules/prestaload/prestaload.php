<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaLoad extends Module
{
    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('PrestaLoad', [], 'Modules.Prestaload.Admin');
        $this->description = $this->trans('PrestaLoad SaaS connector module.', [], 'Modules.Prestaload.Admin');
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->context->smarty->assign([
            'prestaload_module_version' => $this->version,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
