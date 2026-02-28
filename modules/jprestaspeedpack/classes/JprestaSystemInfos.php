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

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaSystemInfos')) {
    class JprestaSystemInfos
    {
        private $infos = [];

        public function __construct()
        {
            if (class_exists('\PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation')) {
                $hostingInformation = new PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation();
                $this->infos['server'] = ['label' => 'Operating system', 'value' => $hostingInformation->getUname()];
                $this->infos['server_php'] = ['label' => 'Server PHP', 'value' => $hostingInformation->getServerInformation()['version']];
                $this->infos['php_version'] = ['label' => 'PHP version', 'value' => $hostingInformation->getServerInformation()['php']['version']];
                $this->infos['php_memory_limit'] = ['label' => 'PHP memory limit', 'value' => $hostingInformation->getServerInformation()['php']['memoryLimit']];
                $this->infos['php_max_exec_time'] = ['label' => 'PHP max execution time (s)', 'value' => $hostingInformation->getServerInformation()['php']['maxExecutionTime']];
                $this->infos['db_version'] = ['label' => 'Database version', 'value' => $hostingInformation->getDatabaseInformation()['version']];
                $this->infos['db_engine'] = ['label' => 'Database engine', 'value' => $hostingInformation->getDatabaseInformation()['engine']];
                $this->infos['db_driver'] = ['label' => 'Database driver', 'value' => $hostingInformation->getDatabaseInformation()['driver']];
            } else {
                $this->infos['server'] = ['label' => 'Operating system', 'value' => function_exists('php_uname') ? php_uname('s') . ' ' . php_uname('v') . ' ' . php_uname('m') : ''];
                $this->infos['server_php'] = ['label' => 'Server PHP', 'value' => $_SERVER['SERVER_SOFTWARE']];
                $this->infos['php_version'] = ['label' => 'PHP version', 'value' => phpversion()];
                $this->infos['php_memory_limit'] = ['label' => 'PHP memory limit', 'value' => ini_get('memory_limit')];
                $this->infos['php_max_exec_time'] = ['label' => 'PHP max execution time (s)', 'value' => ini_get('max_execution_time')];
                $this->infos['db_version'] = ['label' => 'Database version', 'value' => Db::getInstance()->getVersion()];
                $this->infos['db_engine'] = ['label' => 'Database engine', 'value' => _MYSQL_ENGINE_];
                $this->infos['db_driver'] = ['label' => 'Database driver', 'value' => Db::getClass()];
            }
            $op_cache_infos = false;
            if (function_exists('opcache_get_status')) {
                $op_cache_infos = @opcache_get_status(false);
            }
            if ($op_cache_infos) {
                $this->infos['op_cache_enabled'] = ['label' => 'OP Cache', 'value' => $op_cache_infos['opcache_enabled'] ? 'On' : 'Off'];
            } else {
                $this->infos['op_cache_enabled'] = ['label' => 'OP Cache', 'value' => 'Off (or not available)'];
            }
        }

        public function getAll()
        {
            return $this->infos;
        }

        public function get($key)
        {
            if (isset($this->infos[$key])) {
                return $this->infos[$key];
            }

            return null;
        }
    }
}
