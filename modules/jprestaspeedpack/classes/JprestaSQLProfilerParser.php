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

include_once dirname(__FILE__) . '/../autoload-deps.php';

use JPresta\Greenlion\PHPSQLParser\PHPSQLParser;
use JPresta\SpeedPack\JprestaUtils;

class JprestaSQLProfilerParser extends PHPSQLParser
{
    /**
     * @var array
     */
    private $tables;

    private $whereColumns;

    private $groupByColumns;

    private $orderByColumns;

    public function __construct($sql = false, $calcPositions = false, array $options = [])
    {
        parent::__construct($sql, $calcPositions, $options);
        $this->tables = self::findTables($this->parsed);
        $this->whereColumns = $this->findColumnsInClause('WHERE', $this->parsed);
        $this->groupByColumns = $this->findColumnsInClause('GROUP', $this->parsed);
        $this->orderByColumns = $this->findColumnsInClause('ORDER', $this->parsed);
    }

    protected static function removeBackQuotes($str)
    {
        return str_replace('`', '', $str);
    }

    protected static function findTables($parsed)
    {
        $tables = [];
        if (is_array($parsed)) {
            if (isset($parsed['table'])) {
                $tables[self::removeBackQuotes($parsed['table'])] = self::removeBackQuotes($parsed['table']);
                if (isset($parsed['alias'])
                    && is_array($parsed['alias'])) {
                    $tables[self::removeBackQuotes($parsed['alias']['name'])] = self::removeBackQuotes($parsed['table']);
                }
            }
            foreach ($parsed as $value) {
                $tables = array_merge($tables, self::findTables($value));
            }
        }

        return $tables;
    }

    protected function findColumnsInClause($clause, $parsed, $fromTables = null, $nextToken = null)
    {
        $cols = [];
        if (is_array($parsed)) {
            if (isset($parsed[$clause])) {
                // We found a new WHERE/ORDER clause here
                $cols = array_merge($cols,
                    $this->findColumnsInClause(
                        $clause,
                        $parsed[$clause],
                        isset($parsed['FROM']) ?
                            array_values(
                                array_unique(
                                    self::findTables($parsed['FROM'])
                                )
                            ) : []
                    )
                );
            } elseif ($fromTables !== null) {
                // We are in a WHERE/ORDER clause
                if (isset($parsed['expr_type']) && $parsed['expr_type'] === 'colref') {
                    $tableName = null;
                    if ($parsed['no_quotes']['delim']) {
                        $tableName = $this->getTableName($parsed['no_quotes']['parts'][0]);
                        $columnName = self::removeBackQuotes($parsed['no_quotes']['parts'][1]);
                    } else {
                        $columnName = self::removeBackQuotes($parsed['no_quotes']['parts'][0]);
                        if (count($fromTables) === 1) {
                            $tableName = $fromTables[0];
                        } elseif (count($fromTables) > 0) {
                            $tableName = JprestaUtils::dbGetTableOfColumn($columnName, $fromTables);
                        }
                    }
                    $nextOperator = null;
                    if ($nextToken !== null) {
                        if (isset($nextToken['expr_type']) && $nextToken['expr_type'] === 'operator') {
                            $op = strtoupper($nextToken['base_expr']);
                            if (in_array($op, ['=', 'LIKE', 'IN', '>', '<', '>=', '<='])) {
                                $nextOperator = $op;
                            }
                        }
                    }
                    if (isset($cols[$tableName . '.' . $columnName])) {
                        ++$cols[$tableName . '.' . $columnName]['count'];
                        $cols[$tableName . '.' . $columnName]['operators'][] = $nextOperator;
                    } else {
                        $cols[$tableName . '.' . $columnName] = [
                            'table' => $tableName,
                            'column' => $columnName,
                            'count' => 1,
                            'operators' => [$nextOperator],
                        ];
                    }
                }
                foreach ($parsed as $key => $value) {
                    $nextToken = null;
                    if (is_int($key) && isset($parsed[$key + 1])) {
                        $nextToken = $parsed[$key + 1];
                    }
                    $cols = array_merge($cols, self::findColumnsInClause($clause, $value, $fromTables, $nextToken));
                }
            }
        }

        return $cols;
    }

    public function getTableNames()
    {
        return array_values(array_unique($this->tables));
    }

    public function getTableName($aliasOrName)
    {
        if (isset($this->tables[self::removeBackQuotes($aliasOrName)])) {
            return $this->tables[self::removeBackQuotes($aliasOrName)];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getWhereColumns()
    {
        return $this->whereColumns;
    }

    /**
     * @return array
     */
    public function getOrderByColumns()
    {
        return $this->orderByColumns;
    }

    /**
     * @return array
     */
    public function getGroupByColumns()
    {
        return $this->groupByColumns;
    }
}
