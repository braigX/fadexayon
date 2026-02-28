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

class JprestaSQLProfilerCallstack
{
    const TABLE = 'jpresta_profiler_stack';

    /**
     * var int
     */
    private $id_stack;

    /**
     * var int
     */
    private $id_query;

    /**
     * @var array
     */
    private $callstack;

    /**
     * @var int
     */
    private $executedCount;

    /**
     * @param int|null $id_stack
     * @param int $id_query
     * @param string $callstack
     * @param int $executedCount
     */
    private function __construct($id_stack, $id_query, $callstack, $executedCount)
    {
        $this->id_stack = $id_stack;
        $this->id_query = $id_query;
        $this->executedCount = $executedCount;
        $this->callstack = $callstack;
    }

    /**
     * @param int $id_query
     * @param array $callstack
     *
     * @return JprestaSQLProfilerCallstack
     */
    public static function create($id_query, $callstack)
    {
        // We cannot store all datas or it will consumes too many datas so we remove useless ones
        if (is_array($callstack)) {
            foreach ($callstack as &$call) {
                if (isset($call['args'])) {
                    unset($call['args']);
                }
            }
        }

        return new JprestaSQLProfilerCallstack(null, $id_query, json_encode($callstack, defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : null), 1);
    }

    public static function createTable()
    {
        JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE . '`(
            `id_stack` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_query` int(11) UNSIGNED NOT NULL,
            `callstack` TEXT NOT NULL,
            `exec_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_stack`)
            ) ENGINE=InnoDb DEFAULT CHARSET=utf8', true, true);
    }

    public static function dropTable()
    {
        JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE . '`;');
    }

    public static function getByIdQuery($id_query)
    {
        $cs = [];
        $rows = JprestaUtils::dbSelectRows('SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_query=' . (int) $id_query . ' ORDER BY exec_count DESC');
        foreach ($rows as $row) {
            $cs[(int) $row['id_stack']] = new JprestaSQLProfilerCallstack(
                (int) $row['id_stack'],
                (int) $row['id_query'],
                json_decode($row['callstack'], true),
                (int) $row['exec_count']);
        }

        return $cs;
    }

    public function save()
    {
        if ($this->callstack === null) {
            throw new PrestaShopException('callstack has been freed, cannot save anymore');
        }
        $db = Db::getInstance();
        $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE . '` (`id_query`, `callstack`, `exec_count`)
                VALUES (
                ' . JprestaUtils::dbToInt($this->id_query) . ',
                ' . JprestaUtils::dbToString($db, $this->callstack) . ',
                ' . (int) $this->executedCount . '
                )';
        JprestaUtils::dbExecuteSQL($query, true, true);
        $this->id_stack = (int) Db::getInstance()->Insert_ID();
    }

    public static function deleteAll()
    {
        $query = 'TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE . '`';
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    public static function deleteByIdRun($id_run)
    {
        $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_query IN (SELECT id_query FROM `' . _DB_PREFIX_ . JprestaSQLProfilerQuery::TABLE . '` WHERE id_run=' . (int) $id_run . ')';
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    /**
     * @return mixed
     */
    public function getIdStack()
    {
        return $this->id_stack;
    }

    /**
     * @return mixed
     */
    public function getIdQuery()
    {
        return $this->id_query;
    }

    /**
     * @return array
     */
    public function getCallstack()
    {
        return $this->callstack;
    }

    /**
     * @return int
     */
    public function getExecutedCount()
    {
        return $this->executedCount;
    }

    public function getCaller()
    {
        $caller = '';
        if (is_array($this->callstack)) {
            $previousFileLine = null;
            foreach ($this->callstack as $callstack) {
                if (isset($callstack['class']) && $previousFileLine && $callstack['class'] !== 'Db' && $callstack['class'] !== 'DbCore') {
                    $caller = $previousFileLine['file'] . ':' . $previousFileLine['line'] . ' - ' . $callstack['class'] . '::' . $callstack['function'];
                    break;
                }
                if (isset($callstack['file'])) {
                    $previousFileLine = $callstack;
                }
            }
        }

        return $caller;
    }

    public function incrementCount()
    {
        ++$this->executedCount;
        $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET exec_count=exec_count+1 WHERE id_stack=' . JprestaUtils::dbToInt($this->id_stack);
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    /**
     * Free memory
     *
     * @return void
     */
    public function freeCallstack()
    {
        $this->callstack = null;
    }
}
