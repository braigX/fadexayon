<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminJprestaDbOptimizerConfigurationController extends ModuleAdminController
{
    private $jpresta_submodule;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->jpresta_submodule = $this->module->jpresta_submodules['JprestaDbOptimizer'];
    }

    /**
     * Check if the module needs to be upgraded (scripts)
     */
    public function init()
    {
        if ($this->module->needsUpgrade()) {
            $this->module->upgradeIfNeeded();
            $this->confirmations = array_merge($this->confirmations, $this->module->getConfirmations());
            $this->errors = array_merge($this->errors, $this->module->getErrors());
        }
        parent::init();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/back.css');
    }

    public function renderList()
    {
        return $this->jpresta_submodule->saveConfiguration() . $this->jpresta_submodule->displayForm();
    }
}
