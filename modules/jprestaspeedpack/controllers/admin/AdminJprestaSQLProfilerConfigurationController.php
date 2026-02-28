<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use JPresta\SpeedPack\JprestaUtils;

class AdminJprestaSQLProfilerConfigurationController extends ModuleAdminController
{
    /**
     * @var JprestaSQLProfilerModule
     */
    private $sqlProfiler;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->sqlProfiler = $this->module->jpresta_submodules['JprestaSQLProfiler'];
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
        $this->sqlProfiler->purge();
        if (JprestaUtils::isOverridenBy('db/Db', 'query', $this->module->name)
            && !(int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE')) {
            // The override is installed so we synchronize the state
            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE', 1);
        }
        if (!(int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE')) {
            // Make sure the cookie is removed when the profiler is disabled
            setcookie('jpresta_profiler_run', '', time() - 3600, '/');
        }
        parent::init();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        if (method_exists($this, 'addJQuery')) {
            $this->addJquery();
        }
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/jquery.dataTables.min.js');
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/dataTables.buttons.min.js');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/jquery.dataTables.min.css');
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/buttons.dataTables.min.css');
    }

    public function renderList()
    {
        $output = $this->sqlProfiler->saveConfiguration();

        $infos = [];
        $infos['datas_run_url'] = $this->context->link->getAdminLink('AdminJprestaSQLProfiler');
        $infos['url_query_ctrl'] = $this->context->link->getAdminLink('AdminJprestaSQLProfilerQuery');
        $infos['module_name'] = $this->module->name;
        $infos['sql_profiler_enabled'] = (int) JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_SQL_PROFILER_ENABLE') && !_PS_DEBUG_PROFILING_;
        $infos['faq_url'] = 'https://jpresta.com/' . Context::getContext()->language->iso_code . '/faq/sql-profiler';

        $this->context->smarty->assign($infos);

        return $output . $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/sql-profiler.tpl')
            . $this->sqlProfiler->displayForm()
        ;
    }
}
