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

class EtsRVStaff extends EtsRVModel
{
    public $id_employee;
    public $display_name;
    public $avatar;
    public $id_last_activity;
    public $sendmail;
    public $enabled;

    public static $definition = array(
        'table' => 'ets_rv_staff',
        'primary' => 'id_employee',
        'multilang' => false,
        'fields' => array(
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_last_activity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'display_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'avatar' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'sendmail' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($id)
            $this->id_employee = $id;
        if ($id <= 0 || $id == null)
            return $this;
        else {
            $employee = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'employee` WHERE `id_employee`=' . (int)$id);
            if ($employee) {
                foreach ($employee as $field => $value)
                    $this->$field = $value;
                $this->id = $this->id_employee;
            }
            return $this;
        }
    }

    public static function getAll($context, $sendmail = null)
    {
        if ($context == null)
            $context = Context::getContext();
        $query = '
            SELECT e.`id_employee` as `id`
                 , CONCAT(e.firstname, " ", e.lastname) as `name`
                 , e.`email`
                 , e.`id_lang` 
                 , 1 as `employee` 
            FROM `' . _DB_PREFIX_ . 'employee` e
            INNER JOIN `' . _DB_PREFIX_ . 'employee_shop` es ON (es.`id_employee`=e.`id_employee` AND es.`id_shop`=' . (int)$context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_staff` rs ON (rs.`id_employee`=e.`id_employee`)
            WHERE rs.`id_employee` > 0 AND rs.`enabled`=1' . ($sendmail !== null ? ' AND rs.sendmail=' . (int)$sendmail : '') . '
        ';
        $res = Db::getInstance()->executeS($query);
        $employees = [];
        if ($res) {
            foreach ($res as $re) {
                $employees[trim($re['email'])] = $re;
            }
        }

        if ($sendmail !== null && ($emails = Configuration::get('ETS_RV_EMAIL_NOTIFICATIONS')) !== '') {
            $emails = explode(',', $emails);
            foreach ($emails as $email) {
                if (isset($employees[trim($email)]))
                    continue;
                $employeeId = (int)Db::getInstance()->getValue('SELECT `id_employee` FROM `' . _DB_PREFIX_ . 'employee` WHERE `email` = \'' . pSQL(trim($email)) . '\' AND `active` = 1');
                $employee = new Employee($employeeId);
                if ($employee instanceof Employee) {
                    $employees[trim($email)] = [
                        'id' => $employee->id,
                        'name' => $employee->firstname . ' ' . $employee->lastname,
                        'email' => $email,
                        'id_lang' => $employee->id_lang,
                        'employee' => 1,
                    ];
                } else {
                    $employees[trim($email)] = [
                        'id' => 0,
                        'name' => Configuration::get('PS_SHOP_NAME'),
                        'email' => $email,
                        'id_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
                        'employee' => 1,
                    ];
                }
            }
        }

        return $employees;
    }

    public static function isGrand($id_employee)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        return self::isSupperAdmin($id_employee) || (int)Db::getInstance()->getValue('SELECT `id_employee` FROM `' . _DB_PREFIX_ . 'ets_rv_staff` WHERE `id_employee`=' . (int)$id_employee . ' AND enabled=1') > 0;
    }

    public static function isSupperAdmin($id_employee)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        $employee = new Employee($id_employee);
        return $employee->id > 0 && $employee->id_profile == _PS_ADMIN_PROFILE_;
    }

    public static function itemExist($id_employee)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        return Db::getInstance()->getValue('SELECT `id_employee` FROM `' . _DB_PREFIX_ . 'ets_rv_staff` WHERE `id_employee`=' . (int)$id_employee);
    }

    public static function lastViewer($id_employee, $id_last_activity)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee) || !self::isGrand($id_employee) || !$id_last_activity || !Validate::isUnsignedInt($id_last_activity))
            return false;

        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_staff` SET id_last_activity=' . (int)$id_last_activity . ' WHERE id_employee=' . (int)$id_employee);
    }

    public static function getLastActivityId($id_employee)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        return (int)Db::getInstance()->getValue('SELECT `id_last_activity` FROM `' . _DB_PREFIX_ . 'ets_rv_staff` WHERE `id_employee`=' . (int)$id_employee);
    }

    public static function initSupperAdmin($id_employee = 0)
    {
        $id_last_activity = (int)EtsRVActivity::getLastID();
        $query = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'ets_rv_staff`(`id_employee`, `id_last_activity`, `display_name`, `avatar`, `enabled`) VALUES ';
        if ($id_employee > 0) {
            return Db::getInstance()->execute($query . '(' . (int)$id_employee . ',' . (int)$id_last_activity . ', NULL, \'\', 1);');
        } else {
            $employees = Db::getInstance()->executeS('SELECT id_employee FROM `' . _DB_PREFIX_ . 'employee` WHERE id_profile = ' . (int)_PS_ADMIN_PROFILE_);
            if (count($employees) > 0) {
                $query2 = '';
                foreach ($employees as $emp) {
                    $query2 .= '(' . (int)$emp['id_employee'] . ',' . ((int)Configuration::get('ETS_RV_LAST_ID_EMPLOYEE_' . (int)$emp['id_employee']) ?: $id_last_activity) . ', NULL, \'\', 1),';
                }
                if ($query2 !== '')
                    Db::getInstance()->execute($query . rtrim($query2, ','));
            }
        }

        return true;
    }

    public static function getInfos($id_employee)
    {
        if (!$id_employee || !Validate::isUnsignedInt($id_employee))
            return false;

        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_staff` WHERE `id_employee`=' . (int)$id_employee);
    }
}
