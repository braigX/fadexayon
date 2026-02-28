<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class TestHookError extends Module
{
    public function __construct()
    {
        $this->name = 'testhookerror';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Debug';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Test Hook Error';
        $this->description = 'Module pour simuler Undefined offset: 1 sur Admin Products';
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayAdminProductsCombination'); // hook déclinaisons
    }

    // Hook qui provoque l'erreur
    public function hookDisplayAdminProductsCombination($params)
    {
        // On renvoie un tableau mal formaté pour simuler l'erreur
        return [
            'foo',  // seul index 0
            // index 1 manquant → Undefined offset: 1
        ];
    }
}
