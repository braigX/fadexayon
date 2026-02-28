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

include_once dirname(__FILE__) . '/../../jprestaspeedpack.php';

class AdminPageCacheMemcachedTestController extends ModuleAdminController
{
    public $php_self;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        header('Access-Control-Allow-Origin: *');

        parent::initContent();

        $host = Tools::getValue('host', '');
        $port = (int) Tools::getValue('port', '');
        $memcached = new PageCacheCacheMemcached($host, $port);
        $isConnected = $memcached->isConnected($host, $port);
        $result = [
            'host' => $host,
            'port' => $port,
            'status' => $isConnected ? 1 : 0,
            'comments' => $isConnected ? 'Server version ' . $memcached->getVersion() : $memcached->getResultMessage(),
        ];
        exit(json_encode($result));
    }
}
