<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/../../jprestaspeedpack.php';

class AdminPageCacheDatasController extends ModuleAdminController
{
    public $php_self;

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
        if (Tools::getIsset('url') && Tools::getIsset('id_context')) {
            exit(
                Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_cache.tpl')
                . $this->module->getCache(Tools::getValue('id_shop'))->get(Tools::getValue('url'), PageCacheDAO::getContextKeyById(Tools::getValue('id_context')))
            );
        }

        header('Access-Control-Allow-Origin: *');
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Cache-Control: max-age=300, private');
        }
        header('Content-type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && Tools::getValue('type') === 'contexts') {
            self::displayContextsDatas();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && Tools::getValue('type') === 'ttfb') {
            $this->displayTTFBDatas();
        }

        // Db table to use
        $table = _DB_PREFIX_ . PageCacheDAO::TABLE;

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = [
            ['db' => 'url', 'dt' => 0, 'formatter' => function ($s, $row) {
                return self::formatURL($s, $row);
            }],
            ['db' => 'cache_key', 'dt' => -1],
            ['db' => 'id_shop', 'dt' => -1],
            ['db' => 'id_context', 'dt' => 1, 'formatter' => function ($id_context) {
                return self::formatContext(PageCacheDAO::getContextById((int) $id_context));
            }],
            ['db' => 'id_controller', 'dt' => 2, 'formatter' => function ($id_controller) {
                return self::formatController($id_controller);
            }],
            ['db' => 'id_object', 'dt' => 3],
            ['db' => 'last_gen', 'dt' => 4, 'formatter' => function ($d, $row) {
                return self::formatLastGenerated($d, $row);
            }],
            ['db' => 'deleted', 'dt' => 5, 'formatter' => function ($deleted) {
                return self::formatDeleted($deleted);
            }],
            ['db' => 'count_hit', 'dt' => 6, 'formatter' => function ($s, $row) {
                return self::formatHit($s, $row);
            }],
            ['db' => 'count_missed', 'dt' => -1],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Tools::getIsset('clear')) {
            $searchUrl = trim($_POST['columns'][0]['search']['value']);
            $searchContext = trim($_POST['columns'][1]['search']['value']);
            $searchCtrl = (int) $_POST['columns'][2]['search']['value'];
            $searchId = (int) $_POST['columns'][3]['search']['value'];
            if ($searchCtrl || $searchId || $searchUrl || $searchContext) {
                // Clear cache of filtered rows
                self::simpleClear($_POST, $table, $columns);
            } else {
                // Clear all
                $this->module->clearCache('manually called from stats');
            }
            exit(json_encode([]));
        } else {
            $result = self::simple($_GET, $table, $columns);
            exit(json_encode($result));
        }
    }

    private static function displayContextsDatas()
    {
        $data = [];
        $pageSize = Tools::getValue('length', 10);
        $start = Tools::getValue('start', 0);
        $searchQuery = Tools::getValue('search', '');
        $searchQuery = $searchQuery['value'];
        if ($searchQuery && strpos($searchQuery, '=') !== false) {
            $searchVals = explode(',', $searchQuery);
            $whereClauses = [];
            foreach ($searchVals as $searchVal) {
                list($key, $val) = explode('=', $searchVal);
                if ((int) $val > 0 && $key === 'id_lang') {
                    $whereClauses[] = 'id_lang=' . (int) $val;
                } elseif ((int) $val > 0 && $key === 'id_cur') {
                    $whereClauses[] = 'id_currency=' . (int) $val;
                } elseif ((int) $val > 0 && $key === 'id_country') {
                    $whereClauses[] = 'id_country=' . (int) $val;
                } elseif ($key === 'id_specs') {
                    if ((int) $val > 0) {
                        $whereClauses[] = 'id_specifics=' . (int) $val;
                    } elseif ($val === 'null') {
                        $whereClauses[] = 'id_specifics IS NULL';
                    }
                } elseif ((int) $val > 0 && $key === 'id_device') {
                    $whereClauses[] = 'id_device=' . (int) $val;
                } elseif ($key === 'id_group') {
                    if ((int) $val > 0) {
                        $whereClauses[] = 'id_fake_customer=' . (int) $val;
                    } elseif ($val === '0') {
                        $whereClauses[] = 'id_fake_customer IS NULL';
                    }
                }
            }
        }

        $select = 'SELECT *, count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache as count_hit,
            count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache + count_missed as count_visit,
            (count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache) * 100 / (count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache + count_missed) as hit_rate';
        $selectTotal = 'SELECT count(*) as total_ctx, sum(count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache + count_missed) as total_visit_count, sum(count_bot) as total_bot_count';
        $from = ' FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`';
        $whereClauses[] = 'id_shop=' . (int) Shop::getContextShopID();
        $where = ' WHERE ' . implode(' AND ', $whereClauses);
        $limit = " LIMIT $pageSize OFFSET $start";

        $orders = Tools::getValue('order', []);
        if (count($orders) === 1) {
            $order = $orders[0];
            $orderDir = $order['dir'];
            switch ($order['column']) {
                case 3:
                    $orderCol = 'hit_rate';
                    break;
                case 4:
                    $orderCol = 'count_bot';
                    break;
                case 2:
                default:
                    $orderCol = 'count_visit';
                    break;
            }
            $orderBy = ' ORDER BY ' . $orderCol . ' ' . $orderDir;
        } else {
            $orderBy = ' ORDER BY count_visit desc';
        }
        $total_ctx = 0;
        $total_visit_count = 0;
        $total_bot_count = 0;
        $rows = JprestaUtils::dbSelectRows($select . $from . $where . $orderBy . $limit);
        $rowsTotal = JprestaUtils::dbSelectRows($selectTotal . $from . $where);
        if (count($rowsTotal) > 0) {
            $total_ctx = (int) $rowsTotal[0]['total_ctx'];
            $total_visit_count = (int) $rowsTotal[0]['total_visit_count'];
            $total_bot_count = (int) $rowsTotal[0]['total_bot_count'];
        }
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $statsContext = PageCacheDAO::getStatsByContext($row['id']);
                $data[] = (object) [
                    0 => $row['context_key'],
                    1 => self::formatContext($row),
                    2 => !$total_visit_count ? 0 : $row['count_visit'] . ' (' . round($row['count_visit'] * 100 / $total_visit_count, 2) . '%)',
                    3 => self::formatHit((int) $row['count_hit'], $row),
                    4 => !$total_bot_count ? 0 : $row['count_bot'] . ' (' . round($row['count_bot'] * 100 / $total_bot_count, 2) . '%)',
                    5 => !$statsContext['count'] ? '0/0 (0%)' : $statsContext['count_deleted'] . '/' . $statsContext['count'] . ' (' . round($statsContext['count_deleted'] * 100 / $statsContext['count'], 2) . '%)',
                ];
            }
        }
        $recordsTotal = $recordsFiltered = $total_ctx;

        $datas = [
            'data' => $data,
            'recordsFiltered' => $recordsFiltered,
            'recordsTotal' => $recordsTotal,
        ];
        exit(json_encode($datas));
    }

    private function displayTTFBDatas()
    {
        $controller = Tools::getValue('controller_name');
        $id_controller = Jprestaspeedpack::getManagedControllerId($controller);
        $whereClause = 'WHERE id_shop=' . (int) Shop::getContextShopID();
        if ($id_controller) {
            $whereClause .= ' AND id_controller=' . (int) $id_controller;
        }
        $whereClause .= ' AND date_add >= CURRENT_DATE() - interval 13 DAY';
        $query = 'SELECT UNIX_TIMESTAMP(day_add) AS day,
            ROUND(AVG(ttfb_ms_hit_server)) AS ttfb_ms_hit_server,
            ROUND(AVG(ttfb_ms_hit_static)) AS ttfb_ms_hit_static,
            ROUND(AVG(ttfb_ms_hit_browser)) AS ttfb_ms_hit_browser,
            ROUND(AVG(ttfb_ms_hit_bfcache)) AS ttfb_ms_hit_bfcache,
            ROUND(AVG(ttfb_ms_missed)) AS ttfb_ms_missed
            FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ' . $whereClause . ' GROUP BY day_add ORDER BY day_add ASC;';
        $rows = JprestaUtils::dbSelectRows($query);
        $missed = $server = $static = $browser = $bf = [];
        foreach ($rows as $row) {
            $missed[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_missed']];
            $server[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_server']];
            $static[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_static']];
            $browser[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_browser']];
            $bf[] = ['x' => (int) $row['day'], 'y' => $row['ttfb_ms_hit_bfcache']];
        }
        $datas = [
            [
                'values' => $missed,
                'key' => $this->module->l('Cache not available', 'AdminPageCacheDatasController'),
                'color' => '#ce720b',
            ],
            [
                'values' => $server,
                'key' => $this->module->l('Server cache', 'AdminPageCacheDatasController'),
                'color' => '#007e00',
            ],
            [
                'values' => $static,
                'key' => $this->module->l('Static cache', 'AdminPageCacheDatasController'),
                'color' => '#00bd00',
            ],
            [
                'values' => $browser,
                'key' => $this->module->l('Browser cache', 'AdminPageCacheDatasController'),
                'color' => '#00da00',
            ],
            [
                'values' => $bf,
                'key' => $this->module->l('Back/forward cache', 'AdminPageCacheDatasController'),
                'color' => '#00ff00',
            ],
        ];

        $query = 'SELECT date_format(MIN(day_add), \'%Y-%m-%d\') AS start_date,
            SUM(1) AS total_count
            FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PERFS . '` ' . $whereClause;
        $rows = JprestaUtils::dbSelectRows($query);
        $start_date = 0;
        $total_count = 0;
        if (count($rows) > 0) {
            $start_date = $rows[0]['start_date'];
            $total_count = (int) $rows[0]['total_count'];
        }

        exit(json_encode(['datas' => $datas, 'start_date' => $start_date, 'total_count' => $total_count]));
    }

    private static function formatController($id_controller)
    {
        return Jprestaspeedpack::getManagedControllerNameById($id_controller);
    }

    private static function formatHit($count_hit, $row)
    {
        $smarty = Context::getContext()->smarty;
        $smarty->assign('count_hit', (int) $count_hit);
        $smarty->assign('count_missed', (int) $row['count_missed']);
        $smarty->assign('count_percent', round(((int) $count_hit * 100) / max(1, (int) $count_hit + (int) $row['count_missed']), 1));

        return $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_hit.tpl');
    }

    private static function formatDeleted($deleted)
    {
        $smarty = Context::getContext()->smarty;
        $smarty->assign('deleted', $deleted);
        $smarty->assign('isPs17', JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>'));

        return $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_deleted.tpl');
    }

    private static function formatLastGenerated($d, $row)
    {
        $lastGen = strtotime($d);
        $cache_ttl = 60 * ((int) Configuration::get('pagecache_' . Jprestaspeedpack::getManagedControllerNameById($row['id_controller']) . '_timeout'));
        $age = time() - $lastGen;
        $ttl = $cache_ttl - $age;
        if ($cache_ttl <= 0) {
            $percent = 0;
        } elseif ($cache_ttl <= $age) {
            $percent = 100;
        } else {
            $percent = $age * 100 / $cache_ttl;
        }
        $color = '#ccc';
        if ($percent >= 100) {
            $color = 'red';
        } elseif ($percent >= 95) {
            $color = 'orange';
        }

        $smarty = Context::getContext()->smarty;
        $smarty->assign('lastGen', strtotime($d));
        if ($cache_ttl == -60) {
            $ttl_msg = 'forever';
        } elseif ($ttl <= 0) {
            $ttl_msg = 'dead';
        } else {
            $ttl_msg = self::getNiceDuration($ttl);
        }
        $smarty->assign('age', self::getNiceDuration($age));
        $smarty->assign('last_gen', date('Y-m-d H:i:s', strtotime($d)));
        $smarty->assign('ttl_msg', $ttl_msg);
        $smarty->assign('color', $color);
        $smarty->assign('percent', $percent);

        return $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_lastgen.tpl');
    }

    private static function formatURL($url, $row)
    {
        $smarty = Context::getContext()->smarty;
        $smarty->clearAssign('flag_lang');
        $smarty->clearAssign('flag_currency');
        $smarty->clearAssign('flag_country');
        $smarty->clearAssign('flag_device');
        $smarty->clearAssign('flag_group');
        $smarty->clearAssign('flag_tax_manager');
        $smarty->clearAssign('flag_specifics');
        $smarty->clearAssign('flag_specifics_more');
        $smarty->clearAssign('flag_v_css');
        $smarty->clearAssign('flag_v_js');
        $smarty->clearAssign('url_cached');

        if (!empty($row['id_lang'])) {
            $smarty->assign('flag_lang', $row['id_lang']);
        }
        if (!empty($row['id_currency'])) {
            $currency = new Currency($row['id_currency']);
            $smarty->assign('flag_currency', $currency->sign);
        }
        if (!empty($row['id_country'])) {
            $country = new Country($row['id_country']);
            $smarty->assign('flag_country', $country->iso_code);
        }
        if (!empty($row['id_device'])) {
            if ($row['id_device'] == Jprestaspeedpack::DEVICE_COMPUTER) {
                $smarty->assign('flag_device', 'desktop');
            } elseif ($row['id_device'] == Jprestaspeedpack::DEVICE_TABLET) {
                $smarty->assign('flag_device', 'tablet');
            } elseif ($row['id_device'] == Jprestaspeedpack::DEVICE_MOBILE) {
                $smarty->assign('flag_device', 'mobile');
            }
        }
        if (!empty($row['id_fake_customer'])) {
            $jCustomer = new JprestaCustomer((int) $row['id_fake_customer']);
            $smarty->assign('flag_group', $jCustomer->getLabel() . ' (#' . $row['id_fake_customer'] . ')');
        }
        if (!empty($row['id_tax_csz'])) {
            $tax_manager_json = PageCacheDAO::getDetailsById($row['id_tax_csz']);
            $smarty->assign('flag_tax_manager', $row['id_tax_csz']);
            $smarty->assign('flag_tax_manager_more', JprestaUtilsTaxManager::toPrettyString($tax_manager_json));
        }
        if (!empty($row['id_specifics'])) {
            $specifics = PageCacheDAO::getDetailsById($row['id_specifics']);
            $jscks = new JprestaCacheKeySpecifics($specifics);
            $smarty->assign('flag_specifics', $row['id_specifics']);
            $smarty->assign('flag_specifics_more', $jscks->toPrettyString());
        }
        if (!empty($row['v_css'])) {
            $smarty->assign('flag_v_css', $row['v_css']);
        }
        if (!empty($row['v_js'])) {
            $smarty->assign('flag_v_js', $row['v_js']);
        }

        $smarty->assign('url', $url);

        if (array_key_exists('deleted', $row) && !$row['deleted'] && $row['cache_key']) {
            $cacheLink =
                Context::getContext()->link->getAdminLink(
                    'AdminPageCacheDatas',
                    true,
                    [],
                    ['url' => $url, 'id_context' => $row['id_context']]);
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
                $cacheLink .= '&url=' . urlencode($url) . '&id_context=' . (int) $row['id_context'];
            }
            $smarty->assign('url_cached', $cacheLink);
        }

        $smarty->assign('isPs17', JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>'));

        return $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_url.tpl');
    }

    public static function formatContext($contextRow)
    {
        $smarty = Context::getContext()->smarty;
        $smarty->clearAssign('flag_lang');
        $smarty->clearAssign('flag_currency');
        $smarty->clearAssign('flag_country');
        $smarty->clearAssign('flag_device');
        $smarty->clearAssign('flag_group');
        $smarty->clearAssign('flag_tax_manager');
        $smarty->clearAssign('flag_specifics');
        $smarty->clearAssign('flag_specifics_more');
        $smarty->clearAssign('flag_v_css');
        $smarty->clearAssign('flag_v_js');

        $smarty->assign('id_context', $contextRow['id']);
        if (!empty($contextRow['id_lang'])) {
            $smarty->assign('flag_lang', $contextRow['id_lang']);
        }
        if (!empty($contextRow['id_currency'])) {
            $currency = new Currency($contextRow['id_currency']);
            $smarty->assign('flag_currency', $currency->sign);
        }
        if (!empty($contextRow['id_country'])) {
            $country = new Country($contextRow['id_country']);
            $smarty->assign('flag_country', $country->iso_code);
        }
        if (!empty($contextRow['id_device'])) {
            if ($contextRow['id_device'] == Jprestaspeedpack::DEVICE_COMPUTER) {
                $smarty->assign('flag_device', 'desktop');
            } elseif ($contextRow['id_device'] == Jprestaspeedpack::DEVICE_TABLET) {
                $smarty->assign('flag_device', 'tablet');
            } elseif ($contextRow['id_device'] == Jprestaspeedpack::DEVICE_MOBILE) {
                $smarty->assign('flag_device', 'mobile');
            }
        }
        if (!empty($contextRow['id_fake_customer'])) {
            $jCustomer = new JprestaCustomer((int) $contextRow['id_fake_customer']);
            $smarty->assign('flag_group', $jCustomer->getLabel() . ' (#' . $contextRow['id_fake_customer'] . ')');
        }
        if (!empty($contextRow['id_tax_csz'])) {
            $tax_manager_json = PageCacheDAO::getDetailsById($contextRow['id_tax_csz']);
            $smarty->assign('flag_tax_manager', $contextRow['id_tax_csz']);
            $smarty->assign('flag_tax_manager_more', JprestaUtilsTaxManager::toPrettyString($tax_manager_json));
        }
        if (!empty($contextRow['id_specifics'])) {
            $specifics = PageCacheDAO::getDetailsById($contextRow['id_specifics']);
            $jscks = new JprestaCacheKeySpecifics($specifics);
            $smarty->assign('flag_specifics', $contextRow['id_specifics']);
            $smarty->assign('flag_specifics_more', $jscks->toPrettyString());
        }
        if (!empty($contextRow['v_css'])) {
            $smarty->assign('flag_v_css', $contextRow['v_css']);
        }
        if (!empty($contextRow['v_js'])) {
            $smarty->assign('flag_v_js', $contextRow['v_js']);
        }

        $smarty->assign('isPs17', JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>'));

        return $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/get-content-tab-datas_context.tpl');
    }

    private static function getNiceDuration($durationInSeconds)
    {
        $duration = '';
        if ($durationInSeconds < 0) {
            $duration = '-';
        } else {
            $days = floor($durationInSeconds / 86400);
            $durationInSeconds -= $days * 86400;
            $hours = floor($durationInSeconds / 3600);
            $durationInSeconds -= $hours * 3600;
            $minutes = floor($durationInSeconds / 60);
            $seconds = $durationInSeconds - $minutes * 60;

            if ($days > 0) {
                $duration .= $days . ' days';
            }
            if ($hours > 0) {
                $duration .= ' ' . $hours . ' hours';
            }
            if ($minutes > 0) {
                $duration .= ' ' . $minutes . ' minutes';
            }
            if ($seconds > 0) {
                $duration .= ' ' . $seconds . ' seconds';
            }
        }

        return $duration;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data Data from the SQL get
     *
     * @return array Formatted data in a row based format
     */
    private static function data_output($columns, $data)
    {
        $out = [];
        for ($i = 0, $ien = count($data); $i < $ien; ++$i) {
            $row = [];
            for ($j = 0, $jen = count($columns); $j < $jen; ++$j) {
                $column = $columns[$j];
                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }
            $out[] = $row;
        }

        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param array $request Data sent to server by DataTables
     *
     * @return string SQL limit clause
     */
    private static function limit($request)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = 'LIMIT ' . ((int) $request['start']) . ', ' . ((int) $request['length']);
        }

        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param array $request Data sent to server by DataTables
     * @param array $columns Column information array
     *
     * @return string SQL order by clause
     */
    private static function order($request, $columns)
    {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = [];
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; ++$i) {
                // Convert the column index into the column data property
                $columnIdx = (int) $request['order'][$i]['column'];
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    if ($column['db'] == 'count_hit') {
                        // Special case
                        $orderBy[] = 'count_hit+count_missed ' . $dir;
                    } else {
                        $orderBy[] = '`' . $column['db'] . '` ' . $dir;
                    }
                }
            }
            if (count($orderBy)) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }

        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param array $request Data sent to server by DataTables
     * @param array $columns Column information array
     *
     * @return string SQL where clause
     */
    private static function filter($request, $columns)
    {
        $columnSearch = [];
        $dtColumns = self::pluck($columns, 'dt');

        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; ++$i) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                $str = $requestColumn['search']['value'];

                if ($requestColumn['searchable'] == 'true' && $str != '') {
                    if (!empty($column['db'])) {
                        if ($column['db'] === 'url') {
                            $columnSearch[] = '`' . $column['db'] . "` LIKE '%" . $str . "%'";
                        } else {
                            $columnSearch[] = '`' . $column['db'] . "` = '" . $str . "'";
                        }
                    }
                }
            }
        }

        // Combine the filters into a single string
        $where = 'id_shop IN (' . implode(',', Shop::getContextListShopID()) . ')';

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param array $request Data sent to server by DataTables
     * @param string $table SQL table to query
     * @param array $columns Column information array
     *
     * @return array Server-side processing response array
     */
    private static function simple($request, $table, $columns)
    {
        // Build the SQL query string from the request
        $limit = self::limit($request);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns);

        // Main query to actually get the data
        try {
            $data = JprestaUtils::dbSelectRows('SELECT `' . implode('`, `', self::pluck($columns, 'db')) . "`
			 FROM `$table`
			 $where
			 $order
			 $limit"
            );
            // Data set length after filtering
            // Total data set length
            $recordsFiltered = $recordsTotal = JprestaUtils::dbGetValue("SELECT COUNT(*) FROM `$table` $where");
        } catch (Exception $e) {
            exit($e->getMessage());
        }

        /*
         * Output
         */
        return [
            'draw' => isset($request['draw']) ? (int) $request['draw'] : 0,
            'recordsTotal' => (int) $recordsTotal,
            'recordsFiltered' => (int) $recordsFiltered,
            'data' => self::data_output($columns, $data),
        ];
    }

    private static function simpleClear($request, $table, $columns)
    {
        // Build the SQL query string from the request
        $where = self::filter($request, $columns);

        // Main query to actually get the data
        try {
            $rows = JprestaUtils::dbSelectRows("SELECT * FROM `$table` as c $where AND deleted=0 LIMIT 1000");
            PageCacheDAO::deleteCachedPages($rows, false);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param array $a Array to get data from
     * @param string $prop Property to read
     *
     * @return array Array of property values
     */
    private static function pluck($a, $prop)
    {
        $out = [];
        for ($i = 0, $len = count($a); $i < $len; ++$i) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }
}
