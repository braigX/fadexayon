<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class EtsSeoGptMessage
 *
 * @since 2.6.2
 *
 * @mixin \ObjectModelCore
 */
class EtsSeoGptMessage extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_seo_gpt_message;
    /**
     * @var int
     */
    public $is_chatgpt = 0;
    /**
     * @var string
     */
    public $message;
    /**
     * @var int|null
     */
    public $id_parent;
    /**
     * @var string
     */
    public $date_add;
    public static $definition = [
        'table' => 'ets_seo_gpt_message',
        'primary' => 'id_ets_seo_gpt_message',
        'multilang_shop' => false,
        'multilang' => false,
        'fields' => [
            'is_chatgpt' => ['type' => self::TYPE_INT],
            'message' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'id_parent' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];
    public static function getMessages($lastId = false, $limit = 10, $offset = 0)
    {
        $query = (new \DbQuery())->select(sprintf('%s as id', bqSQL(self::$definition['primary'])))->from(bqSQL(self::$definition['table']));
        if ($lastId && $lastId > 0) {
            $query->where(sprintf('%s < %d', bqSQL(self::$definition['primary']), (int) $lastId));
        }
        $query->orderBy('id DESC');
        if ($limit && $limit > 0) {
            $start = ($offset && $offset > 0) ? (int) $offset : 0;
            $query->limit((int) $limit, $start);
        }
        $rs = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $results = [];
        foreach ($rs as $id) {
            $results[] = new self($id['id']);
        }

        return $results;
    }

    /**
     * @return int
     */
    public static function countAllMessages()
    {
        $query = (new \DbQuery())->select(sprintf('COUNT(%s) as total', bqSQL(self::$definition['primary'])))->from(bqSQL(self::$definition['table']));

        return (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @return bool
     */
    public static function deleteAllMessages()
    {
        $query = sprintf('DELETE FROM `%s%s` WHERE 1', _DB_PREFIX_, bqSQL(self::$definition['table']));

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
    }
}
