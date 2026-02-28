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

if (!defined('_PS_VERSION_')) { exit; }

class EtsRVActivity extends ObjectModel
{
    const ETS_RV_TYPE_REVIEW = 1;//rev
    const ETS_RV_TYPE_COMMENT = 2;//com
    const ETS_RV_TYPE_QUESTION = 3;//que
    const ETS_RV_TYPE_COMMENT_QUESTION = 4;//cmq
    const ETS_RV_TYPE_ANSWER_QUESTION = 5;//ans
    const ETS_RV_TYPE_COMMENT_ANSWER = 6;//cma
    const ETS_RV_TYPE_REPLY_COMMENT = 7;//rep

    const ETS_RV_ACTION_REVIEW = 1;//rev
    const ETS_RV_ACTION_COMMENT = 2;//com
    const ETS_RV_ACTION_REPLY = 3;//rep
    const ETS_RV_ACTION_QUESTION = 4;//que
    const ETS_RV_ACTION_ANSWER = 5;//ans
    const ETS_RV_ACTION_LIKE = 7;//lie
    const ETS_RV_ACTION_DISLIKE = 8;//die


    const ETS_RV_RECORDED_REVIEWS = 1;
    const ETS_RV_RECORDED_QUESTIONS = 2;
    const ETS_RV_RECORDED_USEFULNESS = 3;

    public $id_customer;
    public $id_guest;
    public $employee;
    public $id_product;
    public $type;
    public $action;
    public $id_ets_rv_product_comment;
    public $id_ets_rv_comment;
    public $id_ets_rv_reply_comment;
    public $read;
    public $content;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_activity',
        'primary' => 'id_ets_rv_activity',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 54),
            'action' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 54),
            'id_ets_rv_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_ets_rv_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_ets_rv_reply_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'read' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'content' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => '65535'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getActivitiesFromID($from_id = 0, $exclude_id_employee = 0)
    {
        if ($from_id == '' || !Validate::isUnsignedInt($from_id)) {
            $from_id = 0;
        }
        $dq = new DbQuery();
        $dq
            ->select('COUNT(id_ets_rv_activity)')
            ->from('ets_rv_activity')
            ->where('id_ets_rv_activity > ' . (int)$from_id);

        if ($exclude_id_employee > 0) {
            $dq
                ->where('employee!=' . (int)$exclude_id_employee);
        }

        return Db::getInstance()->getValue($dq);
    }

    public static function getLastID()
    {
        $dq = new DbQuery();
        $dq->select('id_ets_rv_activity')
            ->from('ets_rv_activity')
            ->orderBy('id_ets_rv_activity DESC');
        return Db::getInstance()->getValue($dq);
    }

    public static function makeRead($id_employee, $id)
    {
        return Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_staff_activity` VALUES(' . (int)$id_employee . ', ' . (int)$id . ', 1);');
    }

    public static function makeReadAll($id_employee, $ids)
    {
        $queries = [];
        if ($ids) {
            foreach ($ids as $id)
                $queries[] = '(' . (int)$id_employee . ', ' . (int)$id . ', 1)';
        }
        return count($queries) > 0 && Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_staff_activity` VALUES' . implode(',', $queries) . ';');
    }

    public static function makeReadLastViewer($id_employee, $id_ets_rv_activity)
    {
        if (!$id_ets_rv_activity || !Validate::isUnsignedInt($id_ets_rv_activity) || !$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        return Db::getInstance()->execute('
            INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_staff_activity` (`id_employee`, `id_ets_rv_activity`, `read`)
            SELECT \'' . (int)$id_employee . '\' as `id_employee`, a.`id_ets_rv_activity`, \'1\' as `read`
            FROM `' . _DB_PREFIX_ . 'ets_rv_activity` a
            LEFT JOIN  `' . _DB_PREFIX_ . 'ets_rv_staff_activity` sa ON (sa.`id_ets_rv_activity` = a.`id_ets_rv_activity` AND sa.`id_employee` = ' . (int)$id_employee . ')
            WHERE a.`id_ets_rv_activity` <= ' . (int)$id_ets_rv_activity . ' AND sa.`id_ets_rv_activity` is NULL
        ');
    }

    public static function deleteAll($question = 0)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_activity` WHERE `id_ets_rv_product_comment` IN (SELECT `id_ets_rv_product_comment` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE `question`=' . (int)$question . ')');
    }
}