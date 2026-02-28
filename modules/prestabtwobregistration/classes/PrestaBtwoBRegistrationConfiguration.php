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
class PrestaBtwoBRegistrationConfiguration extends ObjectModel
{
    public $enable_b2b;
    public $b2b_customer_auto_approval;
    public $enable_custom_fields;
    public $enable_group_selection_one;
    public $enable_group_selection;
    public $selected_groups;
    public $assign_groups;
    public $date_of_birth;
    public $identification_siret_number;
    public $address;
    public $vat_number;
    public $required_vat_number;
    public $address_complement;
    public $phone;
    public $disable_normal_registration;
    public $customer_edit;
    public $vat_validation;
    public $send_email_notification_admin;
    public $admin_email_id;
    public $send_email_notification_to_customer;
    public $enable_google_recaptcha;
    public $recaptcha_type;
    public $site_key;
    public $secret_key;
    public $date_add;
    public $date_upd;

    // lang fiels
    public $top_link_text;
    public $personal_data_heading;
    public $address_data_heading;
    public $custom_field_heading;
    public $pending_account_message_text;

    const TABLE_NAME = 'presta_btwob_registration_configuration';
    const TABLE_NAME2 = 'presta_btwob_registration_configuration_lang';

    public static $definition = array(
        'table' => 'presta_btwob_registration_configuration',
        'primary' =>'id_presta_btwob_registration_configuration',
        'multilang' => true,
        'fields'=> array(
            'enable_b2b' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'b2b_customer_auto_approval' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'enable_custom_fields' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'enable_group_selection_one' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'enable_group_selection' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'selected_groups'=>array(
                'type'=>self::TYPE_STRING,
            ),
            'assign_groups' => array(
                'type' => self::TYPE_STRING,
            ),
            'date_of_birth' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'identification_siret_number' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'address' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'vat_number' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'required_vat_number' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'vat_validation' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'address_complement' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'phone' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'disable_normal_registration' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'customer_edit' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'send_email_notification_admin' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'admin_email_id' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 255,
            ),
            'send_email_notification_to_customer' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'enable_google_recaptcha' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'recaptcha_type' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'site_key' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ),
            'secret_key' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
             // LANG FIELDS
             'top_link_text'=>array(
                'lang' => true,
                'type' =>self::TYPE_HTML
            ),
            'personal_data_heading'=>array(
                'lang'=>true,
                'type'=>self::TYPE_HTML
            ),
            'address_data_heading'=>array(
                'lang' => true,
                'type' =>self::TYPE_HTML
            ),
            'custom_field_heading'=>array(
                'lang'=>true,
                'type'=>self::TYPE_HTML
            ),
            'pending_account_message_text'=>array(
                'lang' => true,
                'type' =>self::TYPE_HTML
            ),
        )
    );

    public static function getConfigId()
    {
        $sql = 'SELECT `id_presta_btwob_registration_configuration` FROM `' . _DB_PREFIX_ . self::TABLE_NAME . '`';
        if ($result = Db::getInstance()->getValue($sql)) {
            return (int) $result;
        }
        return false;
    }
}
