<?php
/**
 * Minimal PrestaLoad module.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaLoad extends Module
{
    public function __construct()
    {
        $this->name = 'prestaload';
        $this->tab = 'administration';
        $this->version = '0.4.0';
        $this->author = 'Acrosoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = 'PrestaLoad';
        $this->description = 'Minimal placeholder module.';
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
        return '<div class="panel"><h3>Hello world</h3></div>';
    }
}
