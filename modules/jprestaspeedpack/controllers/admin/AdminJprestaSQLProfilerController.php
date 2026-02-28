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

use JPresta\SpeedPack\JprestaUtils;

class AdminJprestaSQLProfilerController extends ModuleAdminController
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
        header('Access-Control-Allow-Origin: *');
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Cache-Control: max-age=300, private');
        }
        header('Content-type: application/json');

        if (Tools::getIsset('id_run')) {
            // Db table to use
            $table = _DB_PREFIX_ . JprestaSQLProfilerQuery::TABLE;

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = [
                ['db' => 'id_query', 'dt' => 0],
                ['db' => 'query_uniq', 'dt' => 1],
                ['db' => 'exec_count', 'dt' => 2],
                ['db' => 'd_max_ms', 'dt' => 3],
                ['db' => 'd_med_ms', 'dt' => 4],
                ['db' => 'd_avg_ms', 'dt' => 5],
                ['db' => 'd_tot_ms', 'dt' => 6],
                ['db' => 'd_tot_pe', 'dt' => 7],
                ['db' => 'suspect', 'dt' => 8],
            ];
        } else {
            // Db table to use
            $table = _DB_PREFIX_ . JprestaSQLProfilerRun::TABLE;

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = [
                ['db' => 'id_run', 'dt' => 0],
                ['db' => 'date_start', 'dt' => 1],
                ['db' => 'url', 'dt' => 2, 'formatter' => function ($s, $row) {
                    return $s;
                }],
                ['db' => 'exec_count', 'dt' => 3],
                ['db' => 'd_tot_ms', 'dt' => 4],
                ['db' => 'method', 'dt' => 5],
                ['db' => 'is_ajax', 'dt' => 6],
                ['db' => 'suspect_count', 'dt' => 7],
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Tools::getIsset('deleteAll')) {
            JprestaSQLProfilerRun::deleteAll(Tools::getValue('keepRelevant'));
            exit(json_encode([]));
        } else {
            $result = self::simple($_GET, $table, $columns);
            exit(json_encode($result));
        }
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

        if (Tools::getIsset('id_run')) {
            $columnSearch[] = 'id_run=' . (int) Tools::getValue('id_run');
        } else {
            $columnSearch[] = 'is_closed=1';
        }

        // Combine the filters into a single string
        if (count($columnSearch)) {
            return 'WHERE ' . implode(' AND ', $columnSearch);
        }

        return '';
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
