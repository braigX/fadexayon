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

use JPresta\SpeedPack\JprestaUtils;

class JprestaSQLProfilerRun
{
    const METHOD_GET = 1;
    const METHOD_POST = 2;
    const METHOD_PUT = 3;
    const METHOD_DELETE = 4;

    const TABLE = 'jpresta_profiler_run';

    /**
     * @var int
     */
    private $id_run;

    /**
     * @var string
     */
    private $url;

    /**
     * @var DateTime
     */
    private $date_start;

    /**
     * @var int
     */
    private $method;

    /**
     * @var bool
     */
    private $ajax;

    /**
     * @var bool
     */
    private $closed;

    /**
     * @var int
     */
    private $executedCount;

    /**
     * @var float
     */
    private $durationTotalMs;

    /**
     * @var int
     */
    private $suspectCount;

    public static function createTable()
    {
        JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE . '`(
            `id_run` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `url` TEXT NOT NULL,
            `date_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `method` TINYINT(1) NOT NULL,
            `is_ajax` TINYINT(1) NOT NULL,
            `is_closed` TINYINT(1) DEFAULT 0,
            `exec_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `d_tot_ms` FLOAT(8, 2) DEFAULT NULL,
            `suspect_count` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_run`),
            KEY `date_start` (`date_start`),
            KEY `exec_count` (`exec_count`),
            KEY `d_tot_ms` (`d_tot_ms`)
            ) ENGINE=InnoDb DEFAULT CHARSET=utf8', true, true);
    }

    public static function dropTable()
    {
        JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE . '`;');
    }

    /**
     * @param string $url
     * @param DateTime $date_start
     * @param int $method
     * @param bool $is_ajax
     */
    public function __construct($url, $date_start, $method_name, $is_ajax)
    {
        $this->url = $url;
        $this->date_start = $date_start;
        switch ($method_name) {
            case 'POST':
                $this->method = self::METHOD_POST;
                break;
            case 'PUT':
                $this->method = self::METHOD_PUT;
                break;
            case 'DELETE':
                $this->method = self::METHOD_DELETE;
                break;
            default:
                $this->method = self::METHOD_GET;
                break;
        }
        $this->ajax = $is_ajax;
        $this->closed = false;
    }

    public function save()
    {
        $db = Db::getInstance();
        $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE . '` (`url`, `date_start`, `method`, `is_ajax`)
                VALUES (
                ' . JprestaUtils::dbToString($db, $this->url) . ',
                ' . JprestaUtils::dbToString($db, $this->date_start->format('Y-m-d H:i:s')) . ',
                ' . JprestaUtils::dbToInt($this->method) . ',
                ' . ($this->ajax ? 1 : 0) . '
                )';
        JprestaUtils::dbExecuteSQL($query, true, true);
        $this->id_run = (int) Db::getInstance()->Insert_ID();
    }

    public function update()
    {
        $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET is_closed=' . ($this->closed ? 1 : 0)
            . ', exec_count=' . (int) $this->executedCount
            . ', d_tot_ms=' . (float) $this->durationTotalMs
            . ', suspect_count=' . (int) $this->suspectCount
            . ' WHERE id_run=' . (int) $this->id_run
        ;
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    public static function deleteAll($keepRelevant = true)
    {
        if (!$keepRelevant) {
            JprestaSQLProfilerQuery::deleteAll();
            $query = 'TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE . '`';
            JprestaUtils::dbExecuteSQL($query, true, true);
        } else {
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE suspect_count=0';
            $rows = JprestaUtils::dbSelectRows($query);
            foreach ($rows as $row) {
                JprestaSQLProfilerQuery::deleteByIdRun($row['id_run']);
                $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_run=' . (int) $row['id_run'];
                JprestaUtils::dbExecuteSQL($query, true, true);
            }
        }
    }

    public function delete()
    {
        JprestaSQLProfilerQuery::deleteByIdRun($this->id_run);
        $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_run=' . (int) $this->id_run;
        JprestaUtils::dbExecuteSQL($query, true, true);
    }

    public static function purge()
    {
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE is_closed=0 AND date_start < NOW() - INTERVAL 2 MINUTE';
        $rows = JprestaUtils::dbSelectRows($query);
        foreach ($rows as $row) {
            JprestaSQLProfilerQuery::deleteByIdRun($row['id_run']);
            $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_run=' . (int) $row['id_run'];
            JprestaUtils::dbExecuteSQL($query, true, true);
        }
    }

    /**
     * @return int
     */
    public function getIdRun()
    {
        return $this->id_run;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param bool $is_closed
     */
    public function setClosed($is_closed)
    {
        $this->closed = $is_closed;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return DateTime
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * @return int
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }

    /**
     * @return int
     */
    public function getExecutedCount()
    {
        return $this->executedCount;
    }

    /**
     * @param int $executedCount
     */
    public function setExecutedCount($executedCount)
    {
        $this->executedCount = $executedCount;
    }

    /**
     * @return float
     */
    public function getDurationTotalMs()
    {
        return $this->durationTotalMs;
    }

    /**
     * @param float $durationTotalMs
     */
    public function setDurationTotalMs($durationTotalMs)
    {
        $this->durationTotalMs = $durationTotalMs;
    }

    /**
     * @return int
     */
    public function getSuspectCount()
    {
        return $this->suspectCount;
    }

    /**
     * @param int $suspectCount
     */
    public function setSuspectCount($suspectCount)
    {
        $this->suspectCount = $suspectCount;
    }
}
