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
class PrestaBtwoBRegistrationHelper
{
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const DATE = 'date';
    const YESNO = 'yes/no';
    const MULTISELECT = 'multiSelect';
    const DROPDOWN = 'dropdown';
    const CHECKBOX = 'checkbox';
    const RADIO = 'radio';
    const FILE = 'file';
    const MESSAGE = 'message';

    const TEXT_DISPLAY = 'Text';
    const TEXTAREA_DISPLAY = 'Textarea';
    const DATE_DISPLAY = 'Date';
    const YESNO_DISPLAY = 'Yes/No';
    const MULTISELECT_DISPLAY = 'Multi Select';
    const DROPDOWN_DISPLAY = 'Dropdown';
    const CHECKBOX_DISPLAY = 'Checkbox';
    const RADIO_DISPLAY = 'Radio';
    const FILE_DISPLAY = 'File';
    const MESSAGE_DISPLAY = 'Message';

    const TYPELIST = array(
        self::TEXT => self::TEXT_DISPLAY,
        self::TEXTAREA => self::TEXTAREA_DISPLAY,
        self::DATE => self::DATE_DISPLAY,
        self::YESNO => self::YESNO_DISPLAY,
        self::MULTISELECT => self::MULTISELECT_DISPLAY,
        self::DROPDOWN => self::DROPDOWN_DISPLAY,
        self::CHECKBOX => self::CHECKBOX_DISPLAY,
        self::RADIO => self::RADIO_DISPLAY,
        self::FILE => self::FILE_DISPLAY,
        self::MESSAGE => self::MESSAGE_DISPLAY
    );

    const MULTITYPELIST = array(
        self::MULTISELECT => self::MULTISELECT_DISPLAY,
        self::DROPDOWN => self::DROPDOWN_DISPLAY,
        self::CHECKBOX => self::CHECKBOX_DISPLAY,
        self::RADIO => self::RADIO_DISPLAY,
    );

    const NONE = 'none';
    const DECIMALNUMBER = 'decimalnumber';
    const INTEGERNUMBER = 'integernumber';
    const EMAILADDRESS = 'emailaddress';
    const WEBSITEURLADDRESS = 'websiteurladdress';
    const ALPHABETONLY = 'alphabetonly';
    const ALPHABETNUMERICONLY = 'alphabetnumericonly';
    const DATEONLY = 'dateonly';

    const NONE_DISPLAY = 'None';
    const DECIMALNUMBER_DISPLAY = 'Decimal Number';
    const INTEGERNUMBER_DISPLAY = 'Integer Number';
    const EMAILADDRESS_DISPLAY = 'Email Address';
    const WEBSITEURLADDRESS_DISPLAY = 'Website Url Address';
    const ALPHABETONLY_DISPLAY = 'Alphabet Only';
    const ALPHABETNUMERICONLY_DISPLAY = 'Alphabet Numeric Only';
    const DATEONLY_DISPLAY = 'Date';

    const VALIDATIONFIELD = array(
        self::NONE => self::NONE_DISPLAY,
        self::DECIMALNUMBER => self::DECIMALNUMBER_DISPLAY,
        self::INTEGERNUMBER => self::INTEGERNUMBER_DISPLAY,
        self::EMAILADDRESS => self::EMAILADDRESS_DISPLAY,
        self::WEBSITEURLADDRESS => self::WEBSITEURLADDRESS_DISPLAY,
        self::ALPHABETONLY => self::ALPHABETONLY_DISPLAY,
        self::ALPHABETNUMERICONLY => self::ALPHABETNUMERICONLY_DISPLAY,
        self::DATEONLY => self::DATEONLY_DISPLAY,
    );

    public function createTable()
    {
        $tables = array();
        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_registration_configuration` (
            `id_presta_btwob_registration_configuration` INT(11) unsigned NOT NULL AUTO_INCREMENT,
            `enable_b2b` tinyint(2) unsigned DEFAULT 0,
            `b2b_customer_auto_approval` tinyint(2) unsigned DEFAULT 0,
            `enable_custom_fields` tinyint(2) unsigned DEFAULT 0,
            `enable_group_selection` tinyint(2) unsigned DEFAULT 0,
            `enable_group_selection_one` tinyint(2) unsigned DEFAULT 0,
            `selected_groups` text,
            `assign_groups` TEXT NULL,
            `date_of_birth`tinyint(2) unsigned DEFAULT 0,
            `identification_siret_number` tinyint(2) unsigned DEFAULT 0,
            `address` tinyint(2) unsigned DEFAULT 0,
            `vat_number` tinyint(2) unsigned DEFAULT 0,
            `required_vat_number` tinyint(2) unsigned DEFAULT 0,
            `vat_validation` tinyint(2) unsigned DEFAULT 0,
            `address_complement` tinyint(2) unsigned DEFAULT 0,
            `phone` tinyint(2) unsigned DEFAULT 0,
            `disable_normal_registration` tinyint(2) unsigned DEFAULT 0,
            `customer_edit` tinyint(2) unsigned DEFAULT 0,
            `send_email_notification_admin` tinyint(2) unsigned DEFAULT 0,
            `admin_email_id` varchar(255) NOT NULL,
            `send_email_notification_to_customer` tinyint(2) unsigned DEFAULT 0,
            `enable_google_recaptcha` tinyint(2) unsigned DEFAULT 0,
            `recaptcha_type` INT(2) NULL,
            `site_key` TEXT NULL,
            `secret_key` TEXT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_presta_btwob_registration_configuration`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_registration_configuration_lang`(
            `id_presta_btwob_registration_configuration_lang` INT(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_presta_btwob_registration_configuration` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `top_link_text` text,
            `personal_data_heading` text,
            `address_data_heading` text,
            `custom_field_heading` text,
            `pending_account_message_text` text,
            PRIMARY KEY(`id_presta_btwob_registration_configuration_lang`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_manage_btwob_customer`(
            `id_presta_manage_btwob_customer` int(10) unsigned NOT NULL auto_increment,
            `id_customer` int(10) unsigned NOT NULL,
            `is_validated` tinyint(1) unsigned NOT NULL DEFAULT 0,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_presta_manage_btwob_customer`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`(
            `id_presta_btwob_custom_fields`  int(10) unsigned NOT NULL auto_increment,
            `field_type` varchar(255) NOT NULL,
            `field_validation` varchar(255) NOT NULL,
            `maximum_size` int(10) unsigned NOT NULL,
            `file_types` varchar(255) NOT NULL,
            `notice_types` int(10) unsigned NOT NULL,
            `active` tinyint(1) unsigned NOT NULL DEFAULT 0,
            `is_mandatory` tinyint(1) unsigned NOT NULL DEFAULT 0,
            `position` int(1) unsigned DEFAULT 0,
            `is_dependant` tinyint(1) unsigned NOT NULL DEFAULT 0,
            `id_dependant_field` int(10) unsigned NOT NULL,
            `id_dependant_value` varchar(255) NOT NULL,
            `is_deleted` int(10) unsigned NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_presta_btwob_custom_fields`)
        ) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_lang`(
            `id_presta_btwob_custom_fields_lang` int(10) unsigned NOT NULL auto_increment,
            `id_presta_btwob_custom_fields` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `field_title` text,
            `default_value` text,
            `notice_message` text,
            PRIMARY KEY (`id_presta_btwob_custom_fields_lang`)
        ) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] ='CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value`(
            `id_multi_value` int(10) unsigned NOT NULL auto_increment,
            `id_presta_btwob_custom_fields` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_multi_value`)
        ) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value_lang`(
            `id_presta_btwob_custom_fields_value_lang` int(10) unsigned NOT NULL auto_increment,
            `id_multi_value` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `value` varchar(255) NOT NULL,
            PRIMARY KEY (`id_presta_btwob_custom_fields_value_lang`)
        ) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8';

        $tables[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'presta_btwob_registration_value`(
            `id_presta_btwob_registration_value` int(10) unsigned NOT NULL auto_increment,
            `id_shop_group` int(10) unsigned NOT NULL,
            `field_id` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned NOT NULL,
            `field_value` text,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_presta_btwob_registration_value`)
        ) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8';

        foreach ($tables as $sql) {
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return true;
    }

    public function addCustomerIntoDesireGroup($idCustomer, $idCustomerGroup)
    {
        if (!$this->checkCustomerGroup($idCustomer, $idCustomerGroup)) {
            Db::getInstance()->insert(
                'customer_group',
                array(
                    'id_group' => (int) $idCustomerGroup,
                    'id_customer' => (int) $idCustomer
                )
            );
        }
        $obj_cust = new Customer($idCustomer);
        $obj_cust->active = 1;
        $obj_cust->id_default_group = (int) $idCustomerGroup;
        $obj_cust->update();
    }

    public function checkCustomerGroup($idCustomer, $idCustomerGroup)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_group` FROM ' . _DB_PREFIX_ . 'customer_group
                WHERE
                    id_group = ' . (int) $idCustomerGroup . ' AND
                    id_customer = ' . (int) $idCustomer
        );
    }
}
