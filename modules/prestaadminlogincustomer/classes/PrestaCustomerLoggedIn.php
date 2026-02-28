<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    die('VERSION NOT EXIST');
}

class PrestaCustomerLoggedIn extends ObjectModel
{
    public $id_customer;
    public $id_employee;
    public $date_upd;

    public static $definition = array(
        'table' => 'presta_logged_in_details',
        'primary' => 'id_presta_logged_in_details',
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ),
            'id_employee' => array(
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ),
            'login_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false
            ),
        ),
    );

    public static function getLoggedInDetails($count = false)
    {
        $content = Db::getInstance()->executeS(
            'SELECT
                lg.*,
                c.`email` as cust_email,
                CONCAT(e.`firstname`, \' \', e.`lastname`) as emp_name,
                CONCAT(c.`firstname`, \' \', c.`lastname`) as cust_name
                FROM  `' . _DB_PREFIX_ . 'presta_logged_in_details` lg
                INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = lg.`id_customer`)
                INNER JOIN `' . _DB_PREFIX_ . 'employee` e ON (e.`id_employee` = lg.`id_employee`)
                Order By `id_presta_logged_in_details` DESC'
        );
        if ($count) {
            return count($content);
        }
        return $content;
    }

    public static function getRecords()
    {
        $content = Db::getInstance()->executeS(
            'SELECT
                DISTINCT lg.`id_customer`,
                c.`email` as cust_email,
                CONCAT(c.`firstname`, \' \', c.`lastname`) as cust_name
                FROM  `' . _DB_PREFIX_ . 'presta_logged_in_details` lg
                INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = lg.`id_customer`)
                Order By `id_presta_logged_in_details` DESC LIMIT 0, 5'
        );
        return $content;
    }

    public function installTable()
    {
        $tables = array(
            "sql1" => "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "presta_logged_in_details` (
                `id_presta_logged_in_details` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` int(11) unsigned NOT NULL,
                `id_employee` int(11) unsigned NOT NULL,
                `login_date` datetime NOT NULL,
                PRIMARY KEY (`id_presta_logged_in_details`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8"
        );

        foreach ($tables as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
}
