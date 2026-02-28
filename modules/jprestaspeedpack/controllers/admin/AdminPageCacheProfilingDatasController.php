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

class AdminPageCacheProfilingDatasController extends ModuleAdminController
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

        // Db table to use
        $table = _DB_PREFIX_ . PageCacheDAO::TABLE_PROFILING;

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = [
            [
                'db' => 'date_exec',
                'dt' => 2,
                'formatter' => function ($d) {
                    return date('Y-m-d H:i:s', strtotime($d));
                },
            ],
            [
                'db' => 'id_module',
                'dt' => 0,
                'formatter' => function ($d) {
                    $moduleInstance = JprestaUtils::getModuleInstanceById($d);
                    if ($moduleInstance) {
                        return '<img src="../modules/' . $moduleInstance->name . '/logo.png" width="32" height="32" style="margin: 3px"/>' . $moduleInstance->displayName . ' (' . $moduleInstance->name . ')';
                    }

                    return "Module #$d";
                },
            ],
            ['db' => 'description', 'dt' => 1],
            [
                'db' => 'duration_ms',
                'dt' => 3,
                'formatter' => function ($d) {
                    return number_format($d) . ' ms';
                },
            ],
        ];
        $result = self::simple($_GET, $table, $columns);
        exit(json_encode($result));
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
     * @param array $columns Column information array
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
                    $orderBy[] = '`' . $column['db'] . '` ' . $dir;
                }
            }
            if (count($orderBy)) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }

        return $order;
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
        // Main query to actually get the data
        try {
            $data = JprestaUtils::dbSelectRows('SELECT `' . implode('`, `', self::pluck($columns, 'db')) . "`
			 FROM `$table`
			 $order
			 $limit"
            );
            // Data set length after filtering
            // Total data set length
            $recordsFiltered = $recordsTotal = JprestaUtils::dbGetValue("SELECT COUNT(*) FROM `$table`");
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
