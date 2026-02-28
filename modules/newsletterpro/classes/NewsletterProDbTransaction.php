<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProDbTransaction
{
    const DB_TYPE_MYSQL = 'MySQL';
    const DB_TYPE_PDO = 'DbPDO';
    const DB_TYPE_MYSQLI = 'DbMySQLi';

    const FETCH_ASSOC = 1;

    private $type;

    public $db;

    public $conn;

    /**
     * The transaction will fetch only one record.
     */
    public function __construct()
    {
        $this->type = Db::getClass();
        $this->db = Db::getInstance();
        $this->conn = $this->db->connect();
    }

    public static function newInstance()
    {
        return new self();
    }

    public function begin()
    {
        if (self::DB_TYPE_PDO == $this->type) {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->beginTransaction();
        } elseif (self::DB_TYPE_MYSQLI == $this->type) {
            $this->conn->begin_transaction();
        } elseif (self::DB_TYPE_MYSQL == $this->type) {
            mysql_query('START TRANSACTION', $this->conn);
            mysql_query('BEGIN', $this->conn);
        } else {
            $this->throwDatabaseTypeError();
        }
    }

    public function exec($query)
    {
        if (self::DB_TYPE_PDO == $this->type) {
            $this->conn->exec($query);
        } elseif (self::DB_TYPE_MYSQLI == $this->type) {
            $this->conn->query($query);
        } elseif (self::DB_TYPE_MYSQL == $this->type) {
            mysql_query($query, $this->conn);
        } else {
            $this->throwDatabaseTypeError();
        }
    }

    public function query($query, $fetch = self::FETCH_ASSOC)
    {
        if (self::DB_TYPE_PDO == $this->type) {
            $q = $this->conn->query($query);

            $f = PDO::FETCH_ASSOC;
            switch ($fetch) {
                case self::FETCH_ASSOC:
                default:
                    $f = PDO::FETCH_ASSOC;
                    break;
            }

            return $q->fetch($f);
        } elseif (self::DB_TYPE_MYSQLI == $this->type) {
            $q = $this->conn->query($query);

            $f = 'fetch_assoc';

            switch ($fetch) {
                case self::FETCH_ASSOC:
                default:
                    $f = 'fetch_assoc';
                    break;
            }

            return call_user_func([$q, $f]);
        } elseif (self::DB_TYPE_MYSQL == $this->type) {
            $q = mysql_query($query);

            $f = 'mysql_fetch_assoc';

            switch ($fetch) {
                case self::FETCH_ASSOC:
                default:
                    $f = 'mysql_fetch_assoc';
                    break;
            }

            return call_user_func($f, $q);
        } else {
            $this->throwDatabaseTypeError();
        }
    }

    public function commit()
    {
        if (self::DB_TYPE_PDO == $this->type || self::DB_TYPE_MYSQLI == $this->type) {
            $this->conn->commit();
        } elseif (self::DB_TYPE_MYSQL == $this->type) {
            mysql_query('COMMIT', $this->conn);
        } else {
            $this->throwDatabaseTypeError();
        }
    }

    public function rollback()
    {
        if (self::DB_TYPE_PDO == $this->type || self::DB_TYPE_MYSQLI == $this->type) {
            $this->conn->rollback();
        } elseif (self::DB_TYPE_MYSQL == $this->type) {
            mysql_query('ROLLBACK', $this->conn);
        } else {
            $this->throwDatabaseTypeError();
        }
    }

    private function throwDatabaseTypeError()
    {
        throw new Exception(sprintf('Invalid database type "%s".'), $this->type);
    }
}
