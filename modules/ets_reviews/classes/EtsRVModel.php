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

class EtsRVModel extends ObjectModel
{
    const NAME_MAX_LENGTH = 65535;
    const NAME_MIN_LENGTH = 3;

    public static function deleteCascade($id, $table, $parent = '', $id_group = '')
    {
        if (!$id ||
            !is_array($id) && !Validate::isUnsignedInt($id) ||
            !$table ||
            !$parent && !$id_group
        ) {
            return false;
        }
        $qr = '
            DELETE 
                `' . _DB_PREFIX_ . 'ets_rv_@TABLE@`,
                `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_lang`,
                `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_origin_lang`,
                `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_usefulness`,
                `' . _DB_PREFIX_ . 'ets_rv_activity`
            FROM `' . _DB_PREFIX_ . 'ets_rv_@TABLE@`
                ' . ($parent ? 'LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_@PARENT_TABLE@` ON (`' . _DB_PREFIX_ . 'ets_rv_@TABLE@`.id_ets_rv_@PARENT_TABLE@ = `' . _DB_PREFIX_ . 'ets_rv_@PARENT_TABLE@`.id_ets_rv_@PARENT_TABLE@)' : '') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_lang` ON (`' . _DB_PREFIX_ . 'ets_rv_@TABLE@`.id_ets_rv_@TABLE@ = `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_lang`.id_ets_rv_@TABLE@)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_origin_lang` ON (`' . _DB_PREFIX_ . 'ets_rv_@TABLE@`.id_ets_rv_@TABLE@ = `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_origin_lang`.id_ets_rv_@TABLE@)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_usefulness` ON (`' . _DB_PREFIX_ . 'ets_rv_@TABLE@`.id_ets_rv_@TABLE@ = `' . _DB_PREFIX_ . 'ets_rv_@TABLE@_usefulness`.id_ets_rv_@TABLE@)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_activity` ON (`' . _DB_PREFIX_ . 'ets_rv_@TABLE@`.id_ets_rv_@TABLE@ = `' . _DB_PREFIX_ . 'ets_rv_activity`.id_ets_rv_@TABLE@)
            WHERE `' . _DB_PREFIX_ . 'ets_rv_' . (trim($parent) !== '' ? bqSQL($parent) : '@TABLE@') . '`.id_ets_rv_' . (trim($id_group) !== '' ? pSQL($id_group) : '@PARENT_TABLE@') . (is_array($id) ? ' IN (' . implode(',', array_map('intval', $id)) . ')' : ' = ' . (int)$id) . '
        ';

        $qr = @preg_replace('/@TABLE@/i', bqSQL($table), $qr);
        $qr = @preg_replace('/@PARENT_TABLE@/i', bqSQL($parent), $qr);

        return Db::getInstance()->execute($qr) && (trim($id_group) == '' || self::deleteActivity($id, $id_group));
    }

    public static function deleteActivity($id, $table)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_activity` WHERE `id_ets_rv_' . bqSQL($table) . '`=' . (int)$id);
    }

    public static function getParent($id, $table, $parent)
    {
        if (!$id ||
            !Validate::isUnsignedInt($id) ||
            !$table ||
            !$parent
        ) {
            return false;
        }
        $parent_id = 'id_' . trim($parent);
        $dq = new DbQuery();
        $dq
            ->select('a.validate, a.' . $parent_id)
            ->from($parent, 'a')
            ->leftJoin(pSQL($table), 'b', 'a.' . $parent_id . '=b.' . $parent_id)
            ->where('id_' . pSQL($table) . '=' . $id);

        return Db::getInstance()->getRow($dq);
    }

    public static function getIds($table, $id_customer, $qa = 0, $answer = 0)
    {
        if (!$id_customer ||
            !Validate::isUnsignedInt($id_customer) ||
            !$table
        ) {
            return false;
        }
        $qd = new DbQuery();
        $qd
            ->select('GROUP_CONCAT(id_' . pSQL($table) . ' SEPARATOR ",")')
            ->from($table)
            ->where('id_customer=' . (int)$id_customer)
            ->where('question=' . (int)$qa);
        if (trim($table) == 'ets_rv_comment' && $answer != -1) {
            $qd
                ->where('answer=' . (int)$answer);
        }

        return Db::getInstance()->getValue($qd);
    }

    public static function deleteByIds($table, $ids)
    {
        $qd = new DbQuery();
        $qd
            ->type('DELETE')
            ->from($table)
            ->where('id_' . $table . ' IN (' . $ids . ')');

        return Db::getInstance()->execute($qd);
    }

    public static function deleteGroupTables($tables, $parent_table, $where = null)
    {
        if (!$parent_table ||
            !$tables ||
            !is_array($tables)
        ) {
            return false;
        }
        $res = true;
        foreach ($tables as $table) {
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . bqSQL($parent_table) . '_' . bqSQL($table) . '`' . ($where != null ? ' WHERE ' . $where : '') . ';');
        }
        return $res;
    }

    public static function getEmployeeByEmail($email, $activeOnly = true)
    {
        if (!Validate::isEmail($email)) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('e.*');
        $sql->from('employee', 'e');
        $sql->where('e.`email` = \'' . pSQL($email) . '\'');
        if ($activeOnly) {
            $sql->where('e.`active` = 1');
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if ($result) {
            return new Employee((int)$result['id_employee']);
        }
        return false;
    }
}