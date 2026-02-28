<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WkVoiceSystem extends Module
{
    public $secure_key;

    public function __construct()
    {
        $this->name = 'wkvoicesystem';
        $this->tab = 'front_office_features';
        $this->version = '4.0.3';
        $this->module_key = 'd6b35d33ba3437efb4b121bb43ef9b5c';
        $this->author = 'Webkul';
        $this->secure_key = Tools::hash($this->name);
        $this->bootstrap = 'true';
        parent::__construct();

        $this->displayName = $this->l('Prestashop Voice Search');
        $this->description = $this->l('This module provides a voice recognition system on the search bar and registration form fields');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->confirmUninstall = $this->l('Are you sure to uninstall?');
    }

    /**
     * Register the module hook for this module.
     *
     * @return bool
     */
    public function registerModuleHook()
    {
        return $this->registerHook(
            [
                'actionFrontControllerSetMedia',
            ]
        );
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'wk-voice-css',
            'modules/' . $this->name . '/views/css/wkvoice.css'
        );
        $this->context->controller->registerJavascript(
            'wk-voice-js',
            'modules/' . $this->name . '/views/js/wkvoice.js'
        );
        $this->context->controller->addjQueryPlugin('growl', null, true);
        Media::addJsDef(
            [
                'locale' => $this->context->language->locale,
            ]
        );
        Media::addJsDef([
            'unsupported_browser' => $this->l('Voice input isn\'t supported on this browser'),
        ]);
    }

    /**
     * register hook used in this module
     *
     * @return bool if install properly return true else false
     */
    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHook()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Overriding Module::uninstall()
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }
}
