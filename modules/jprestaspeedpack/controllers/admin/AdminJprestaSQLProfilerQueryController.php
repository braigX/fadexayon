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

use JPresta\Greenlion\PHPSQLParser\PHPSQLCreator;
use JPresta\SpeedPack\JprestaUtils;

class AdminJprestaSQLProfilerQueryController extends ModuleAdminController
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

    public function init()
    {
        if (!isset(Context::getContext()->link)) {
            /* Link should be initialized in the context but sometimes it is not */
            $https_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            Context::getContext()->link = new Link($https_link, $https_link);
        }
        // avoid useless treatments
    }

    public function initHeader()
    {
        // avoid useless treatments
    }

    public function setMedia($isNewTheme = false)
    {
        // avoid useless treatments
    }

    public function initContent()
    {
        /** @var JprestaSQLProfilerQuery $query */
        $query = JprestaSQLProfilerQuery::getById((int) Tools::getValue('id_query'));
        if (!$query) {
            Controller::getController('PageNotFoundController')->run();
            exit;
        }

        $infos = [];
        $infos['url_query_ctrl'] = $this->context->link->getAdminLink('AdminJprestaSQLProfilerQuery');
        $infos['id_query'] = $query->getIdQuery();
        $infos['query'] = $query;
        $tables = [];
        $infos['explain'] = null;
        $sql = null;
        try {
            //  Delete index if requested
            $deleteIndex = Tools::getValue('del_idx_name');
            $deleteIndexTable = Tools::getValue('del_idx_tbl');
            if ($deleteIndex && JprestaUtils::startsWith($deleteIndex, 'jpresta_')
                && $deleteIndexTable && JprestaUtils::startsWith($deleteIndexTable, _DB_PREFIX_)) {
                JprestaUtils::dbDeleteIndexByName($deleteIndexTable, $deleteIndex);
            }

            //  Create index if requested
            $addIndexColumns = Tools::getValue('add_idx_cols');
            $addIndexTable = Tools::getValue('add_idx_tbl');
            if ($addIndexColumns
                && $addIndexTable && JprestaUtils::startsWith($addIndexTable, _DB_PREFIX_)) {
                $cols = explode(',', $addIndexColumns);
                if (is_array($cols) && count($cols) > 0) {
                    $indexName = 'jpresta_' . time();
                    JprestaUtils::dbCreateIndexIfNotExists($addIndexTable, $cols, $indexName);
                }
            }

            $jprestaParser = new JprestaSQLProfilerParser($query->getExampleQuery());

            // Get tables used in the query
            foreach ($jprestaParser->getTableNames() as $tableName) {
                if (!JprestaUtils::startsWith($tableName, 'INFORMATION_SCHEMA')) {
                    $tables[$tableName] = JprestaUtils::dbGetValue('SELECT COUNT(*) FROM `' . $tableName . '`');
                }
            }

            // Format the SQL to be easily readable
            try {
                $creator = new PHPSQLCreator(JprestaSQLProfilerModule::removeComments($jprestaParser->parsed));
                $sql = SqlFormatter::format($creator->created);
            } catch (PHPSQLParser\exceptions\UnsupportedFeatureException $e) {
                // Ignore
            }

            // Get the EXPLAIN of the query
            $infos['explain'] = JprestaSQLProfilerModule::getExplain($query->getExampleQuery());
            $infos['suggestions'] = $this->sqlProfiler->getSuggestions($jprestaParser, $infos['explain'], $query, $tables);
            $infos['indexes'] = JprestaSQLProfilerModule::getJPrestaIndexes($jprestaParser);
            $infos['callstacks'] = JprestaSQLProfilerCallstack::getByIdQuery($infos['id_query']);

            if (Tools::getValue('run_it')) {
                $start = microtime(true);
                JprestaUtils::dbExecuteSQL($query->getExampleQuery());
                $infos['duration_ms'] = (int) round((microtime(true) - $start) * 1000);
                $infos['duration_diff'] = round($infos['duration_ms'] - $query->getDurationMaxMs());
                $infos['duration_percent'] = round(($query->getDurationMaxMs() - $infos['duration_ms']) * 100 / $query->getDurationMaxMs(), 1);
            }
        } catch (Exception $e) {
            JprestaUtils::addLog('SQLProfiler | Unexpected error while analysing the query #' . Tools::getValue('id_query') . ': ' . $query->getExampleQuery() . ' -- ' . $e->getMessage());
        }
        $infos['sql'] = $sql;
        $infos['tables'] = $tables;

        $callstacks = JprestaSQLProfilerCallstack::getByIdQuery($infos['id_query']);
        $infos['callstacks'] = $callstacks;
        $infos['module_name'] = $this->module->name;
        $this->context->smarty->assign($infos);
        exit($this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/sql-profiler-query.tpl'));
    }
}
