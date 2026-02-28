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
use JPresta\Greenlion\PHPSQLParser\PHPSQLParser;
use JPresta\SpeedPack\JprestaSubModule;
use JPresta\SpeedPack\JprestaUtils;

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaSQLProfilerModule')) {
    include_once dirname(__FILE__) . '/../autoload-deps.php';

    require_once 'JprestaSubModule.php';
    require_once 'JprestaSQLProfilerParser.php';
    require_once 'JprestaSQLProfilerRun.php';
    require_once 'JprestaSQLProfilerQuery.php';
    require_once 'JprestaSQLProfilerCallstack.php';
    require_once 'JprestaUtils.php';

    class JprestaSQLProfilerModule extends JprestaSubModule
    {
        public static $queries_to_ignore = [
            'BEGIN', 'COMMIT',
        ];

        /**
         * @var JprestaSQLProfilerQuery[]
         */
        private static $profilerQueries = [];

        private static $durationTotalMs = 0.0;

        private static $executeTotalCount = 0;

        /**
         * @var JprestaSQLProfilerRun
         */
        public static $currentRun;

        public function install()
        {
            $this->module->installTab('AdminJprestaSQLProfiler');
            $this->module->installTab('AdminJprestaSQLProfilerQuery');

            // Tables will not be created if they already exist
            JprestaSQLProfilerRun::createTable();
            JprestaSQLProfilerQuery::createTable();
            JprestaSQLProfilerCallstack::createTable();

            if (!defined('JprestaMigPCU2SP')) {
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE', 0);
            } else {
                if ((int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE')) {
                    $this->enableOverride();
                }
            }

            return true;
        }

        public function uninstall()
        {
            if (!defined('JprestaMigPCU2SP')) {
                JprestaSQLProfilerRun::dropTable();
                JprestaSQLProfilerQuery::dropTable();
                JprestaSQLProfilerCallstack::dropTable();
                Configuration::deleteByName('SPEED_PACK_SQL_PROFILER_ENABLE');
            }

            return true;
        }

        public function enable()
        {
            return true;
        }

        public function disable()
        {
            return true;
        }

        public function saveConfiguration()
        {
            $output = '';
            try {
                if (Tools::isSubmit('submitSQLProfiler')) {
                    if (_PS_MODE_DEMO_ && !Context::getContext()->employee->isSuperAdmin()) {
                        $output .= $this->module->displayError($this->module->l('In DEMO mode you cannot modify the configuration.', 'jprestasqlprofilermodule'));
                    } else {
                        $oldEnable = (int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE');
                        $newEnable = (int) Tools::getValue('SPEED_PACK_SQL_PROFILER_ENABLE');
                        if ($newEnable && !$oldEnable) {
                            $this->enableOverride();
                            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE', 1);
                        } elseif (!$newEnable && $oldEnable) {
                            $this->disableOverride();
                            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE', 0);
                            // Make sure the cookie is removed when the profiler is disabled
                            setcookie('jpresta_profiler_run', '', time() - 3600, '/');
                        }

                        $output .= $this->module->displayConfirmation($this->module->l('Settings updated', 'jprestasqlprofilermodule'));
                    }
                }
            } catch (Exception $e) {
                $output .= $this->module->displayError($e->getMessage());
            }

            return $output;
        }

        /**
         * @param $relPath
         *
         * @throws Exception
         */
        public function enableOverride()
        {
            $overrideFullPath = _PS_MODULE_DIR_ . $this->module->name . '/override/classes/db/Db.php';
            $overrideName = basename($overrideFullPath, '.php');
            if (!file_exists($overrideFullPath)) {
                if (!rename($overrideFullPath . '.off', $overrideFullPath)) {
                    throw new PrestaShopException($this->module->l('Link override not found in ' . $overrideFullPath . '.off'));
                }
            }
            try {
                if (!$this->module->addOverride($overrideName)) {
                    if (file_exists($overrideFullPath)) {
                        rename($overrideFullPath, $overrideFullPath . '.off');
                    }
                    throw new PrestaShopException($this->module->l('Cannot install ' . $overrideName . ' override'));
                }
            } catch (Exception $e) {
                if (file_exists($overrideFullPath)) {
                    rename($overrideFullPath, $overrideFullPath . '.off');
                }
                throw $e;
            }
        }

        /**
         * @param $relPath
         *
         * @throws PrestaShopException
         */
        public function disableOverride()
        {
            $overrideFullPath = _PS_MODULE_DIR_ . $this->module->name . '/override/classes/db/Db.php';
            $overrideName = basename($overrideFullPath, '.php');
            if (JprestaUtils::isOverridenBy('db/Db', 'query', $this->module->name)) {
                try {
                    if (!$this->module->removeOverride($overrideName)) {
                        JprestaUtils::addLog($this->module->l('Unable to remove ' . $overrideName . ' override'), 2);
                    }
                } catch (Exception $e) {
                    JprestaUtils::addLog($this->module->l('Unable to remove ' . $overrideName . ' override') . ': ' . $e->getMessage(), 2);
                }
            }
            if (file_exists($overrideFullPath)) {
                rename($overrideFullPath, $overrideFullPath . '.off');
            }
        }

        public function displayForm()
        {
            // Init Fields form array
            $fieldsForm = [];

            // Init Fields form array
            $fieldsForm[0]['form'] = [
                'legend' => [
                    'title' => $this->module->l('Settings', 'jprestasqlprofilermodule'),
                ],
            ];
            $fieldsForm[0]['form']['input'] = [];
            if (_PS_DEBUG_PROFILING_) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_error',
                    'name' => 'SPEED_PACK_WEBP_INFO',
                    'text' => $this->l('Profiling of Prestashop must be disabled, please set _PS_DEBUG_PROFILING_ to false in file /config/defines.inc.php',
                        'jprestasqlprofilermodule'),
                ];
            } else {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_info',
                    'name' => 'SPEED_PACK_WEBP_INFO',
                    'text' => $this->l('Be aware that the profiling of Prestashop must remain disabled or it will disable this SQL profiler',
                        'jprestasqlprofilermodule'),
                ];
            }
            $fieldsForm[0]['form']['input'][] = [
                'type' => 'switch',
                'label' => $this->module->l('Enable SQL profiler feature', 'jprestasqlprofilermodule'),
                'name' => 'SPEED_PACK_SQL_PROFILER_ENABLE',
                'is_bool' => true,
                'disabled' => false,
                'desc' => $this->module->l('It has a very low impact on real visitors but keep it disabled if you don\'t use it', 'jprestasqlprofilermodule'),
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => true,
                        'label' => $this->module->l('Enabled', 'Admin.Global'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => false,
                        'label' => $this->module->l('Disabled', 'Admin.Global'),
                    ],
                ],
            ];

            $fieldsForm[0]['form']['submit'] = [
                'title' => $this->module->l('Save', 'Admin.Actions'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitSQLProfiler',
            ];

            $helper = new HelperForm();
            $helper->module = $this->module;

            // Load current value
            $helper->fields_value['SPEED_PACK_SQL_PROFILER_ENABLE'] = (int) JprestaUtils::getConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE');

            return $helper->generateForm($fieldsForm);
        }

        public static function addQuery($sql, $durationMs, $callstack, $rowCount)
        {
            static $recursive = false;
            if ($recursive || strpos($sql, 'jpresta_profiler_') !== false) {
                // Do not profile queries executed by the profiler itself or it will never end
                return;
            }
            try {
                $recursive = true;

                if (in_array($sql, self::$queries_to_ignore)) {
                    return;
                }

                if (self::$currentRun === null) {
                    self::$currentRun = new JprestaSQLProfilerRun(urldecode($_SERVER['REQUEST_URI']), new DateTime(), $_SERVER['REQUEST_METHOD'], JprestaUtils::isAjax());
                    self::$currentRun->save();
                    register_shutdown_function('JprestaSQLProfilerModule::close');
                }

                try {
                    $uniqQuery = $sql;
                    $parser = new PHPSQLParser($sql);
                    $parsed = $parser->parsed;
                    if (!is_array($parsed)) {
                        throw new PrestaShopException('Cannot parse query');
                    }
                    if (!JprestaUtils::startsWith($uniqQuery, 'OPTIMIZE TABLE', false)
                        && !JprestaUtils::startsWith($uniqQuery, 'ANALYZE TABLE', false)
                        && !JprestaUtils::startsWith($uniqQuery, 'EXPLAIN', false)
                    ) {
                        $creator = new PHPSQLCreator(self::maskConstantsAndRemoveComments($parsed));
                        $uniqQuery = $creator->created;
                        if (empty($uniqQuery)) {
                            throw new PrestaShopException('Cannot format query');
                        }
                    }
                } catch (Exception $e) {
                    JprestaUtils::addLog("SQLProfiler | was not able to parse/format this query: $sql -- " . $e->getMessage());
                }
                $checksum = md5($uniqQuery);
                if (!isset(self::$profilerQueries[$checksum])) {
                    self::$profilerQueries[$checksum] = new JprestaSQLProfilerQuery(self::$currentRun->getIdRun(), $uniqQuery, $sql);
                    self::$profilerQueries[$checksum]->save();
                }
                self::$profilerQueries[$checksum]->addExecution($sql, $durationMs * 1000.0, $callstack, $rowCount);
            } finally {
                $recursive = false;
            }
        }

        /**
         * Process and save all datas in database
         */
        public static function close()
        {
            if (self::$currentRun) {
                self::$durationTotalMs = 0.0;
                self::$executeTotalCount = 0;
                foreach (self::$profilerQueries as $profilerQuery) {
                    self::$durationTotalMs += $profilerQuery->getDurationTotalMs();
                    self::$executeTotalCount += $profilerQuery->getExecutedCount();
                }
                $suspectCount = 0;
                foreach (self::$profilerQueries as $profilerQuery) {
                    $profilerQuery->close(self::$durationTotalMs);
                    if ($profilerQuery->isSuspect()) {
                        ++$suspectCount;
                    }
                }
                if (self::$executeTotalCount > 0) {
                    self::$currentRun->setExecutedCount(self::$executeTotalCount);
                    self::$currentRun->setDurationTotalMs(self::$durationTotalMs);
                    self::$currentRun->setSuspectCount($suspectCount);
                    self::$currentRun->setClosed(true);
                    self::$currentRun->update();
                } else {
                    self::$currentRun->delete();
                }
            }
        }

        public function purge()
        {
            JprestaSQLProfilerRun::purge();
        }

        /**
         * @param array $parsed
         *
         * @return array
         */
        public static function maskConstantsAndRemoveComments($parsed)
        {
            return self::maskConstants(self::removeComments($parsed));
        }

        /**
         * @param array $parsed
         *
         * @return array
         */
        public static function maskConstants($parsed)
        {
            if (isset($parsed['expr_type'])
                && $parsed['expr_type'] === 'const'
                && isset($parsed['base_expr'])
            ) {
                $parsed['base_expr'] = 'X';
            }
            if (isset($parsed['rowcount'])) {
                $parsed['rowcount'] = 'X';
            }
            if (isset($parsed['offset'])) {
                $parsed['offset'] = 'X';
            }
            if (is_array($parsed)) {
                foreach ($parsed as $key => $value) {
                    if (is_array($value)) {
                        $parsed[$key] = self::maskConstants($value);
                    }
                }
            }

            return $parsed;
        }

        /**
         * @param array $parsed
         *
         * @return array
         */
        public static function removeComments($parsed)
        {
            if (is_array($parsed)) {
                foreach ($parsed as $key => $value) {
                    if (is_array($value)) {
                        if (isset($value['expr_type'])
                            && $value['expr_type'] === 'comment'
                        ) {
                            unset($parsed[$key]);
                        } else {
                            $parsed[$key] = self::removeComments($value);
                        }
                    }
                }
            }

            return $parsed;
        }

        public static function getExplain($sql)
        {
            if (JprestaUtils::startsWith($sql, 'EXPLAIN')
                || JprestaUtils::startsWith($sql, 'SHOW')) {
                // Cannot explain an EXPLAIN or SHOW...
                return [];
            }
            $explains = JprestaUtils::dbSelectRows('EXPLAIN ' . $sql);
            foreach ($explains as &$explain) {
                if (isset($explain['possible_keys']) && is_array($explain['possible_keys'])) {
                    $explain['possible_keys'] = str_replace(',', ', ', $explain['possible_keys']);
                } else {
                    $explain['possible_keys'] = '';
                }
                if (isset($explain['ref']) && is_array($explain['ref'])) {
                    $explain['ref'] = str_replace(',', ', ', $explain['ref']);
                } else {
                    $explain['ref'] = '';
                }
            }

            return $explains;
        }

        /**
         * @param JprestaSQLProfilerParser $jprestaParser
         * @param array $explains
         * @param JprestaSQLProfilerQuery $query
         * @param array $tables key=table name, value=row count
         *
         * @return array
         */
        public function getSuggestions($jprestaParser, $explains, $query, $tables)
        {
            $suggestions = [];

            if (in_array($query->getUniqQuery(), JprestaSQLProfilerQuery::$queries_to_ignore)) {
                $suggestions[] = [
                    'type' => 'tip',
                    'msg' => $this->l('This query is known and cannot be optimized (normally)', 'jprestasqlprofilermodule'),
                ];

                // Returns right away because we ignore it
                return $suggestions;
            }
            if (JprestaUtils::startsWith($query->getUniqQuery(), 'OPTIMIZE TABLE')
                || JprestaUtils::startsWith($query->getUniqQuery(), 'ANALYZE TABLE')
                || JprestaUtils::startsWith($query->getUniqQuery(), 'EXPLAIN')) {
                $suggestions[] = [
                    'type' => 'tip',
                    'msg' => $this->l('This query is known and cannot be optimized (normally)', 'jprestasqlprofilermodule'),
                ];

                // Returns right away because we ignore it
                return $suggestions;
            }

            if (!$explains && $query->getDurationMaxMs() < 200 && !JprestaUtils::dbExecuteSQL($query->getExampleQuery(), true, false)) {
                $suggestions[] = [
                    'type' => 'error',
                    'msg' => $this->l('It looks like this query contains an error, check the logs of Prestashop to know more about it.', 'jprestasqlprofilermodule'),
                ];

                // Returns right away because parsing will be messed up
                return $suggestions;
            }

            if ($query->getRowCountMax() > JprestaSQLProfilerQuery::RECOMMENDED_MAX_ROW) {
                $suggestions[] = [
                    'type' => 'error',
                    'msg' => $this->l('This query returned %d results, you must reduce this number by adding filters or pagination', 'jprestasqlprofilermodule', [$query->getRowCountMax()]),
                ];
            }

            if ($query->getIdenticalCountMax() > JprestaSQLProfilerQuery::RECOMMENDED_IDENTICAL_QUERY) {
                $suggestions[] = [
                    'type' => ($query->getIdenticalCountMax() > JprestaSQLProfilerQuery::RECOMMENDED_IDENTICAL_QUERY * 20 ? 'error' : ($query->getIdenticalCountMax() > JprestaSQLProfilerQuery::RECOMMENDED_IDENTICAL_QUERY * 10 ? 'suggest' : 'tip')),
                    'msg' => $this->l('This query has been executed %d times with the exact same parameters, maybe a cache could be used here', 'jprestasqlprofilermodule', [$query->getIdenticalCountMax()]),
                ];
            }

            $colsThatNeedIndex = array_merge($jprestaParser->getWhereColumns(), $jprestaParser->getOrderByColumns());
            $groupByTable = [];
            foreach ($colsThatNeedIndex as $colThatNeedIndex) {
                if (!isset($groupByTable)) {
                    $groupByTable[$colThatNeedIndex['table']] = [];
                }
                $operators = $colThatNeedIndex['operators'];
                if (!in_array('LIKE', $operators) || !empty(array_diff($operators, ['LIKE']))) {
                    $groupByTable[$colThatNeedIndex['table']][] = $colThatNeedIndex['column'];
                }
                if (in_array('LIKE', $operators)) {
                    $suggestions[] = [
                        'type' => 'tip',
                        'msg' => $this->l('The function LIKE is used on column %s of table %s, make sure the pattern is optimized and does not contain a %% or _ sign in the middle or on the left side', 'jprestasqlprofilermodule', [$colThatNeedIndex['column'], $colThatNeedIndex['table']]),
                    ];
                }
            }
            $db = Db::getInstance();
            foreach ($groupByTable as $tableName => $columns) {
                $rows = JprestaUtils::dbSelectRows('SELECT INDEX_NAME, GROUP_CONCAT(COLUMN_NAME SEPARATOR \',\') as COLUMN_NAMES FROM INFORMATION_SCHEMA.STATISTICS WHERE '
                    . 'TABLE_SCHEMA=' . JprestaUtils::dbToString($db, JprestaUtils::getDatabaseName())
                    . ' AND TABLE_NAME=' . JprestaUtils::dbToString($db, $tableName)
                    . ' GROUP BY INDEX_NAME');
                $indexes = [];
                foreach ($rows as $row) {
                    $indexes[$row['INDEX_NAME']] = explode(',', $row['COLUMN_NAMES']);
                }
                $potentialIndexes = self::findPotentialIndexes($indexes, $columns);
                $coveredColumns = [];
                foreach ($potentialIndexes as $potentailIndex) {
                    $coveredColumns = array_merge($coveredColumns, $indexes[$potentailIndex]);
                }
                $colsThatNeedIndex = array_diff($columns, $coveredColumns);
                if (count($colsThatNeedIndex) > 0) {
                    if (isset($tables[$tableName]) && $tables[$tableName] > JprestaSQLProfilerQuery::RECOMMENDED_INDEX_FROM_ROW) {
                        $listCols = implode(', ', $colsThatNeedIndex);
                        $suggestions[] = [
                            'type' => 'suggest',
                            'msg' => $this->l('You should probably create an index in table %s (%d rows) for the following columns: %s', 'jprestasqlprofilermodule', [$tableName, $tables[$tableName], $listCols]),
                            'action' => 'createIndex(' . $query->getIdQuery() . ", '$tableName', '$listCols')",
                            'action_label' => $this->l('Create the suggested index', 'jprestasqlprofilermodule'),
                        ];
                    }
                }
            }

            return $suggestions;
        }

        private static function findPotentialIndexes($indexes, $requiredColumns)
        {
            $potentialIndexes = [];
            foreach ($indexes as $indexName => $columnsOfIndex) {
                // All column of the index must be included in required columns
                $diff = array_diff($columnsOfIndex, $requiredColumns);
                if ($diff == []) {
                    $potentialIndexes[] = $indexName;
                }
            }

            return $potentialIndexes;
        }

        /**
         * @param JprestaSQLProfilerParser $jprestaParser
         *
         * @return array
         */
        public static function getJPrestaIndexes($jprestaParser)
        {
            $indexes = [];
            $db = Db::getInstance();
            $tableNames = $jprestaParser->getTableNames();
            if (count($tableNames) > 0) {
                $rows = JprestaUtils::dbSelectRows('SELECT DISTINCT TABLE_NAME, INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE '
                    . 'table_schema=' . JprestaUtils::dbToString($db, JprestaUtils::getDatabaseName())
                    . ' AND table_name IN (' . implode(', ', array_map(function ($str) {
                        return '\'' . $str . '\'';
                    }, $tableNames))
                    . ') AND index_name like \'jpresta_%\'');
                foreach ($rows as $row) {
                    $indexes[] = [
                        'table' => $row['TABLE_NAME'],
                        'index' => $row['INDEX_NAME'],
                        'columns' => implode(', ', JprestaUtils::dbGetIndexColumns($row['TABLE_NAME'], $row['INDEX_NAME'])),
                    ];
                }
            }

            return $indexes;
        }
    }
}
