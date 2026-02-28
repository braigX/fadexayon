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
    exit;
}
class PrestaBtwoBRegistrationValue extends ObjectModel
{
    public $id_shop_group;
    public $field_id;
    public $id_customer;
    public $field_value;
    public $date_add;
    public $date_upd;

    const TABLENAME = 'presta_btwob_registration_value';

    public static $definition = array(
        'table' => 'presta_btwob_registration_value',
        'primary' => 'id_presta_btwob_registration_value',
        'fields' => array(
            'id_shop_group' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt',
                'required' => 'true',
            ),
            'field_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_customer' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt',
                'required' => 'true',
            ),
            'field_value' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false
            ),
        )
    );

    public function deleteCustomValues($idCustomer)
    {
        return Db::getInstance()->delete(
            'presta_btwob_registration_value',
            'id_customer = ' . (int) $idCustomer
        );
    }

    public function getAllCustomerData()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::TABLENAME;

        return Db::getInstance()->executeS($sql);
    }

    public function getFieldValueByIdField($idField, $idCustomer)
    {
        $sql = 'SELECT `field_value`, `id_presta_btwob_registration_value` FROM ' . _DB_PREFIX_ . self::TABLENAME . '
            WHERE
                `field_id` = ' . (int) $idField . ' AND
                `id_customer` = ' . (int) $idCustomer;

        return Db::getInstance()->getRow($sql);
    }

    public function getCustomerCustomFieldValues($idCustomer)
    {
        return Db::getInstance()->executes(
            'SELECT * FROM `' . _DB_PREFIX_ . 'presta_btwob_custom_fields` cf
                LEFT JOIN `' . _DB_PREFIX_ . 'presta_btwob_registration_value` cfv
                ON (cf.`id_presta_btwob_custom_fields` = cfv.`field_id` AND cfv.id_customer = ' . (int) $idCustomer . ')
                WHERE cf.active = 1'
        );
    }

    public function getCustomFieldValues($idCustomer, $idLang)
    {
        $sql = 'SELECT
            rfl.`field_title`,
            rfl.`notice_message`,
            rf.*,
            crv.* FROM ' . _DB_PREFIX_ . 'presta_btwob_registration_value crv
            INNER JOIN ' . _DB_PREFIX_ . 'presta_btwob_custom_fields rf
            ON (crv.`field_id` = rf.`id_presta_btwob_custom_fields`)
            LEFT JOIN ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_lang rfl
            ON (rf.`id_presta_btwob_custom_fields` = rfl.`id_presta_btwob_custom_fields` AND
            rfl.`id_lang` = ' . (int) $idLang . ')';
            $sql .= ' WHERE
                crv.`id_customer` = ' . (int) $idCustomer;

        return Db::getInstance()->executes($sql);
    }

    public function getCustomerRegistrationDataValue($idCustomRegistrationDataValue)
    {
        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value_lang
            WHERE `id_multi_value` = ' . (int) $idCustomRegistrationDataValue;

        return Db::getInstance()->getValue($sql);
    }
}
