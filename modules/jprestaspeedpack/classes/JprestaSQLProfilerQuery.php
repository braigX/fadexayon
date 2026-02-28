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

class JprestaSQLProfilerQuery
{
    const TABLE = 'jpresta_profiler_query';
    const RECOMMENDED_MAX_ROW = 200;
    const RECOMMENDED_INDEX_FROM_ROW = 500;
    const RECOMMENDED_IDENTICAL_QUERY = 3;

    public static $queries_to_ignore = [
        'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS X,c.`value`,cl.`value`) AS value, c.id_shop_group, c.id_shop FROM `' . _DB_PREFIX_ . 'configuration` c LEFT JOIN `' . _DB_PREFIX_ . 'configuration_lang` cl ON (c.`id_configuration` = cl.`id_configuration`)',
    ];

    /**
     * @var int
     */
    private $id_query;

    /**
     * @var int
     */
    private $id_run;

    /**
     * @var string
     */
    private $uniqQuery;

    /**
     * @var string
     */
    private $exampleQuery;

    /**
     * @var int
     */
    private $executedCount;

    /**
     * @var float[]
     */
    private $durationMs;

    /**
     * @var float
     */
    private $durationTotalMs;

    /**
     * @var float
     */
    private $durationTotalPercent;

    /**
     * @var float
     */
    private $durationAvgMs;

    /**
     * @var float
     */
    private $durationMedianMs;

    /**
     * @var float
     */
    private $durationMaxMs;

    /**
     * @var int
     */
    private $rowCountMax;

    /**
     * true if this query should be analyzed for optimization
     *
     * @var bool
     */
    private $suspect;

    /**
     * @var int[]
     */
    private $identicalsCount;

    /**
     * @var int
     */
    private $identicalCountMax;

    /**
     * @var JprestaSQLProfilerCallstack[]
     */
    private $callstacks;

    public static function createTable()
    {
        JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE . '`(
            `id_query` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_run` int(11) UNSIGNED NOT NULL,
            `query_uniq` TEXT NOT NULL,
            `query_example` TEXT NOT NULL,
            `exec_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `row_count_max` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
            `same_count_max` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `d_max_ms` FLOAT(8, 2) DEFAULT NULL,
            `d_med_ms` FLOAT(8, 2) DEFAULT NULL,
            `d_avg_ms` FLOAT(8, 2) DEFAULT NULL,
            `d_tot_ms` FLOAT(8, 2) DEFAULT NULL,
            `d_tot_pe` FLOAT(8, 2) DEFAULT NULL,
            `suspect` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_query`),
            KEY `d_tot_ms` (`d_tot_ms`),
            KEY `d_avg_ms` (`d_avg_ms`),
            KEY `d_med_ms` (`d_med_ms`),
            KEY `d_max_ms` (`d_max_ms`),
            KEY `exec_count` (`exec_count`)
            ) ENGINE=InnoDb DEFAULT CHARSET=utf8', true, true);
    }

    public static function dropTable()
    {
        JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE . '`;');
    }

    /**
     * @param string $uniqQuery
     */
    public function __construct($id_run, $uniqQuery, $exempleQuery)
    {
        $this->id_run = (int) $id_run;
        if (!$this->id_run) {
            throw new PrestaShopException("Invalid id_run '$id_run'");
        }
        $this->uniqQuery = $uniqQuery;
        if (empty($uniqQuery)) {
            throw new PrestaShopException('Invalid uniqQuery (empty)');
        }
        $this->exampleQuery = $exempleQuery;
        $this->executedCount = 0;
        $this->rowCountMax = 0;
        $this->durationAvgMs = 0;
        $this->durationMedianMs = 0;
        $this->durationMaxMs = 0;
        $this->durationTotalMs = -1;
        $this->durationTotalPercent = -1;
        $this->identicalCountMax = -1;
        $this->durationMs = [];
        $this->identicalsCount = [];
        $this->callstacks = [];
    }

    /**
     * @param int $id_query
     *
     * @return JprestaSQLProfilerQuery|null
     */
    public static function getById($id_query)
    {
        $query = null;
        $row = JprestaUtils::dbGetRow('SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_query=' . (int) $id_query);
        if ($row) {
            $query = new JprestaSQLProfilerQuery((int) $row['id_run'], $row['query_uniq'], $row['query_example']);
            $query->id_query = (int) $row['id_query'];
            $query->executedCount = (int) $row['exec_count'];
            $query->rowCountMax = (int) $row['row_count_max'];
            $query->durationAvgMs = (float) $row['d_max_ms'];
            $query->durationMedianMs = (float) $row['d_med_ms'];
            $query->durationMaxMs = (float) $row['d_avg_ms'];
            $query->durationTotalMs = (float) $row['d_tot_ms'];
            $query->durationTotalPercent = (float) $row['d_tot_pe'];
            $query->identicalCountMax = (int) $row['same_count_max'];
            $query->suspect = (bool) $row['suspect'];
        }

        return $query;
    }

    public function save()
    {
        $db = Db::getInstance();
        $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE . '` (`id_run`, `query_uniq`, `query_example`, `exec_count`)
                VALUES (
                ' . JprestaUtils::dbToInt($this->id_run) . ',
                ' . JprestaUtils::dbToString($db, $this->uniqQuery) . ',
                ' . JprestaUtils::dbToString($db, $this->exampleQuery) . ',
                ' . (int) $this->executedCount . '
                )';
        JprestaUtils::dbExecuteSQL($query, true, true);
        $this->id_query = (int) Db::getInstance()->Insert_ID();
    }

    public function update()
    {
        $db = Db::getInstance();
        $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET query_example=' . JprestaUtils::dbToString($db, $this->exampleQuery)
            . ', exec_count=' . (int) $this->executedCount
            . ', row_count_max=' . (int) $this->rowCountMax
            . ', d_max_ms=' . (float) $this->durationMaxMs
            . ', d_med_ms=' . (float) $this->durationMedianMs
            . ', d_avg_ms=' . (float) $this->durationAvgMs
            . ', d_tot_ms=' . (float) $this->durationTotalMs
            . ', d_tot_pe=' . (float) $this->durationTotalPercent
            . ', same_count_max=' . (int) $this->identicalCountMax
            . ', suspect=' . ($this->suspect ? 1 : 0)
            . ' WHERE id_query=' . (int) $this->id_query
        ;
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    public static function deleteAll()
    {
        JprestaSQLProfilerCallstack::deleteAll();
        $query = 'TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE . '`';
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    public static function deleteByIdRun($id_run)
    {
        JprestaSQLProfilerCallstack::deleteByIdRun($id_run);
        $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_run=' . (int) $id_run;
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    /**
     * @return int
     */
    public function getIdQuery()
    {
        return $this->id_query;
    }

    /**
     * @return int
     */
    public function getIdRun()
    {
        return $this->id_run;
    }

    /**
     * @return string
     */
    public function getUniqQuery()
    {
        return $this->uniqQuery;
    }

    /**
     * @return string
     */
    public function getExampleQuery()
    {
        return $this->exampleQuery;
    }

    /**
     * @return int
     */
    public function getExecutedCount()
    {
        return $this->executedCount;
    }

    /**
     * @return int
     */
    public function getRowCountMax()
    {
        return $this->rowCountMax;
    }

    /**
     * @return float[]
     */
    public function getDurationMs()
    {
        return $this->durationMs;
    }

    /**
     * @return float
     */
    public function getDurationAvgMs()
    {
        return $this->durationAvgMs;
    }

    /**
     * @return float
     */
    public function getDurationMedianMs()
    {
        return $this->durationMedianMs;
    }

    /**
     * @return float
     */
    public function getDurationMaxMs()
    {
        return $this->durationMaxMs;
    }

    /**
     * @return int
     */
    public function getIdenticalCountMax()
    {
        return $this->identicalCountMax;
    }

    /**
     * @return bool
     */
    public function isSuspect()
    {
        return $this->suspect;
    }

    /**
     * @return float
     */
    public function getDurationTotalMs()
    {
        if ($this->durationTotalMs < 0) {
            // So it can be computed in close()
            return array_sum($this->durationMs);
        } else {
            return $this->durationTotalMs;
        }
    }

    /**
     * @param string $sql
     * @param float $durationMs
     * @param array $callstack
     * @param int $rowCount
     *
     * @return void
     */
    public function addExecution($sql, $durationMs, $callstack, $rowCount)
    {
        if ($this->durationMaxMs < $durationMs) {
            $this->exampleQuery = $sql;
            $this->durationMaxMs = $durationMs;
        }
        if ($this->rowCountMax < $rowCount) {
            $this->rowCountMax = $rowCount;
        }
        // Store identical SQL call count (keep only the biggest one) to know if a small cache would help
        $sqlChecksum = md5($sql);
        if (!isset($this->identicalsCount[$sqlChecksum])) {
            $this->identicalsCount[$sqlChecksum] = 1;
        } else {
            ++$this->identicalsCount[$sqlChecksum];
        }
        $this->durationMs[] = $durationMs;
        ++$this->executedCount;
        $stackChecksum = self::computeChecksum($callstack);
        if (!isset($this->callstacks[$stackChecksum])) {
            $this->callstacks[$stackChecksum] = JprestaSQLProfilerCallstack::create($this->id_query, $callstack);
            $this->callstacks[$stackChecksum]->save();
            // Avoid overflow
            $this->callstacks[$stackChecksum]->freeCallstack();
        } else {
            $this->callstacks[$stackChecksum]->incrementCount();
        }
    }

    private static function computeChecksum($callstack)
    {
        $str = '';
        foreach ($callstack as $call) {
            $str .= isset($call['file']) ? $call['file'] . '|' : '';
            $str .= isset($call['line']) ? $call['line'] . '|' : '';
            $str .= isset($call['function']) ? $call['function'] . '|' : '';
        }

        return md5($str);
    }

    /**
     * @param int $totalDurationMs
     *
     * @return void
     */
    public function close($totalDurationMs)
    {
        $valCount = count($this->durationMs);
        if ($valCount === 0) {
            return;
        }

        // Compute the median duration
        sort($this->durationMs);
        $valCount = count($this->durationMs);
        $medianIndex = floor(($valCount - 1) / 2);
        if ($valCount % 2 == 0) {
            $this->durationMedianMs = ($this->durationMs[(int) $medianIndex] + $this->durationMs[(int) ($medianIndex + 1)]) / 2;
        } else {
            $this->durationMedianMs = $this->durationMs[(int) $medianIndex];
        }

        // Compute how many time the exact same query is executed, keep the highest count
        $this->identicalCountMax = max($this->identicalsCount);

        // Compute the total duration
        $this->durationTotalMs = array_sum($this->durationMs);

        // Compute the average duration
        $this->durationAvgMs = $this->durationTotalMs / $valCount;

        // Compute the percentage of the total
        if ($totalDurationMs > 0) {
            $this->durationTotalPercent = $this->durationTotalMs * 100 / $totalDurationMs;
        } else {
            $this->durationTotalPercent = -1;
        }

        // Check if this query should be optimized
        $this->suspect = !in_array($this->getUniqQuery(), JprestaSQLProfilerQuery::$queries_to_ignore)
            && (
                // Big percentage
                $this->durationMaxMs > 100 && $this->durationTotalPercent > 30.0
                // Many rows
                || $this->rowCountMax > self::RECOMMENDED_MAX_ROW && $this->durationMaxMs > 10
                // Repeated query
                || $this->durationAvgMs > 10 && $this->identicalCountMax > self::RECOMMENDED_IDENTICAL_QUERY
                || $this->identicalCountMax > self::RECOMMENDED_IDENTICAL_QUERY * 10
            );

        $this->update();
    }
}
