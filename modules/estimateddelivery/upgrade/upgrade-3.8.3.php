<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_8_3($module)
{
    /* Update config names. All configurations should start with ed_ or ED_ */
    $old_values = [
        'dd_order_state',
        'dd_days_limit',
        'dd_admin_hours',
        'dd_customer_hours',
        'dd_test_mode',
        'dd_admin_email',
        'dd_test_orders',
        'cron_secret_key',
        'dd_test_orders_mode',
        'dd_test_orders_email',
        'edoos',
        'picking_adv',
        'SET_CAN_OOS_IF_ORIGINAL_IS_POSITIVE',
        'enable_delayed_delivery',
        'enable_cc_email',
        'enable_custom_days',
        'custom_module_for_custom_days',
        'edclass',
        'edcustombg',
        'edcustomborder',
    ];
    // Perform a rename on the fields, since it may have a multishop configuration we will work directly on the configuration database
    foreach ($old_values as $old) {
        $new = (strtolower($old) === $old ? 'ed_' : 'ED_') . preg_replace('/(ed)?([a-zA-Z0-9-_]*)/', '$2', $old);
        Db::getInstance()->update('configuration', ['name' => $new], 'name = "' . $old . '"');
    }

    // Update the ed_holidays constraint to include the holiday_name
    $rename_constraints = [];
    $rename_constraints[] = [
        'table' => 'ed_holidays',
        'constraint_name' => 'ed_holiday',
        'new_values' => 'UNIQUE (holiday_start, holiday_end, `repeat`, `holiday_name`)',
    ];
    foreach ($rename_constraints as $rename_constraint) {
        $tmp_sql = 'SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = \'' . _DB_NAME_ . '\' AND CONSTRAINT_NAME = \'' . _DB_PREFIX_ . bqSQL($rename_constraint['table']) . '\'';

        $res = DB::getInstance()->executeS($tmp_sql);
        if (!empty($res) || count($res) > 0) {
            $sql = [];
            $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($rename_constraint['table']) . '` DROP CONSTRAINT `' . $rename_constraint['constraint_name'];
            $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($rename_constraint['table']) . '` ADD CONSTRAINT ' . $rename_constraint['constraint_name'] . ' ' . $rename_constraint['new_values'];

            foreach ($sql as $query) {
                if (DB::getInstance()->execute($sql) === false) {
                    return false;
                }
            }
        }
    }

    // Update Language-related configs
    $lang_fields = [
        'ed_virtual_msg',
        'ed_preorder_msg',
        'ED_ORDER_LONG_MSG',
        'ed_available_date_msg',
        'ed_custom_date_msg',
        'ed_undefined_delivery_msg',
    ];
    foreach (Language::getLanguages(false) as $lang) {
        $values = [];
        foreach ($lang_fields as $field) {
            $field_with_lang = $field . '_' . $lang['id_lang'];
            if (Configuration::hasKey($field_with_lang)) {
                $values[$lang['id_lang']] = Configuration::get($field_with_lang);
                Configuration::deleteByName($field_with_lang);
            }
        }
        if (!empty($values)) {
            Configuration::updateValue($field, $values);
        }
    }

    return true;
}
