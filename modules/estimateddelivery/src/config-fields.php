<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015-2018
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$ints = [
    ['name' => 'ED_LOCATION', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_LIST_LOCATION', 'def' => ($this->is_17 ? '1' : '0'), 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_STYLE', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DEFAULT_CARRIER_FIRST', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_PRIORITY', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_PRIORITY_2', 'def' => 2, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_DOUBLE_REPEAT', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_POPUP_CARRIERS', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_POPUP_CARRIERS_IMG', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_POPUP_CARRIERS_DESC', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_POPUP_CARRIERS_PRICE', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DISPLAY_POPUP_BACKGROUND', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_COUNTDOWN_LIMIT', 'def' => 12, 'type' => 'int', 's' => 'design'],
    ['name' => 'ed_longer_picking', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_DATE_TYPE', 'def' => 2, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_SHIPPING_TYPE', 'def' => 1, 'type' => 'int', 's' => 'design'],
    ['name' => 'ed_cart_modal', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ed_tooltip', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ed_disp_price', 'def' => 0, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_CALCULATION_METHOD', 'def' => 0, 'type' => 'int', 's' => 'design'],

    ['name' => 'ED_SPECIAL_DATE_FORMAT', 'def' => 8, 'type' => 'int', 's' => 'messages'],
    ['name' => 'ED_APPLY_OOS_TO_AVAIL', 'def' => 0, 'type' => 'int', 's' => 'messages'],

    ['name' => 'ed_oos', 'def' => 1, 'type' => 'int', 's' => 'oos'],

//    array('name' => 'enable_custom_days', 'def' => 0, 'type' => 'int', 's' => 'custom_days'),
    ['name' => 'ed_custom_days', 'def' => 0, 'type' => 'int', 's' => 'custom_days'],
    ['name' => 'ed_custom_days_days', 'def' => 0, 'type' => 'int', 's' => 'custom_days'],
    ['name' => 'ed_custom_module_for_custom_days', 'def' => 0, 'type' => 'int', 's' => 'custom_days'],
    ['name' => 'ED_ADD_CUSTOM_DAYS_MODE', 'def' => 0, 'type' => 'int', 's' => 'custom_days'],

    ['name' => 'ED_ADD_OOS_DAYS_MODE', 'def' => 0, 'type' => 'int', 's' => 'oos'],

    ['name' => 'ED_UNDEFINED_DAYS_MODE', 'def' => 0, 'type' => 'int', 's' => 'undefined_days'],
    ['name' => 'ed_undefined_validate_min', 'def' => 0, 'type' => 'int', 's' => 'undefined_days'],
    ['name' => 'ed_undefined_validate_max', 'def' => 0, 'type' => 'int', 's' => 'undefined_days'],
    ['name' => 'ed_undefined_notify', 'def' => 0, 'type' => 'int', 's' => 'undefined_days'],

    ['name' => 'ED_ADD_PICKING_MODE', 'def' => 0, 'type' => 'int', 's' => 'picking'],
    ['name' => 'ed_picking_adv', 'def' => 0, 'type' => 'int', 's' => 'picking'],

    ['name' => 'ED_LIST', 'def' => 0, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_PROD', 'def' => 0, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_INDEX', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_SEARCH', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_CATEGORY', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_MANUFACTURER', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_BEST-SALES', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_PRICES-DROP', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_NEW-PRODUCTS', 'def' => 1, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_FORMAT', 'def' => 0, 'type' => 'int', 's' => 'list'],
    ['name' => 'ED_LIST_DATE_FORMAT', 'def' => 0, 'type' => 'int', 's' => 'list'],
    ['name' => 'ed_list_max_display', 'def' => 0, 'type' => 'int', 's' => 'list'],

    ['name' => 'ed_carrier_adv', 'def' => 0, 'type' => 'int', 's' => 'carriers'],

    ['name' => 'ed_adv_mode', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_advanced_options', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_debug_var', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_debug_time', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_debug_force_print', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_force_locale', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DISABLE_GEOLOCATION', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_GET_QUANTITY_FROM_DATABASE', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_SET_CAN_OOS_IF_ORIGINAL_IS_POSITIVE', 'def' => 1, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_refresh_delay', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_ajax_delay', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ed_disable_font_awesome', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_FORCE_COUNTRY', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DISABLE_OOS', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_PACK_AS_PRODUCT', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DISABLE_PRODUCT_CARRIERS', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_ALLOW_MULTIPLE_INSTANCES', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DIS_REST', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DIS_COMMON_CARRIER_INTERSECTION', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_ALLOW_EMPTY_CARRIER_GROUPS', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],

    ['name' => 'ED_ORDER', 'def' => 1, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_BO_COLUMNS', 'def' => 1, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_SUMMARY', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_SUMMARY_PRODUCT', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_FORCE', 'def' => 1, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_TYPE', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_LONG', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_LONG_NO_OOS', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_ORDER_HIDE_DELAY', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_SHOW_INVOICE', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_DATES_BY_PRODUCT', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_DATES_BY_PRODUCT_FORCE', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_EMAIL_ICON', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ed_display_checkmark', 'def' => 1, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_FORCE_ALL_COMBI', 'def' => 0, 'type' => 'int', 's' => 'order'],
    ['name' => 'ED_EMAIL_DATE_FORMAT', 'def' => 3, 'type' => 'int', 's' => 'order'],

    ['name' => 'ED_TEST_MODE', 'def' => 0, 'type' => 'int', 's' => 'test_mode'],
    ['name' => 'ED_USE_TOT', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_DISABLE_AFTER_SHIPPING', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_FORCE_DATE', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_AMP', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_HOLIDAY_REPEATABLE', 'def' => 0, 'type' => 'int', 's' => 'holidays', 'ignore_save' => true],
    ['name' => 'ED_ADV_ORDER_FORCE_STOCK', 'def' => 0, 'type' => 'int', 's' => 'advanced_options'],
    ['name' => 'ED_LOCATION_INS', 'def' => 2, 'type' => 'int', 's' => 'design'],
    ['name' => 'ED_EXPORT_HEAD', 'def' => 0, 'type' => 'int', 's' => 'csv-export'],
    ['name' => 'ED_EXPORT_DELETE', 'def' => 0, 'type' => 'int', 's' => 'csv-export'],
    ['name' => 'ed_enable_delayed_delivery', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_enable_cc_email', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_dd_order_state', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_dd_days_limit', 'def' => 30, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_dd_admin_hours', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_dd_customer_hours', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_dd_test_mode', 'def' => 0, 'type' => 'int', 's' => 'delayed-delivery'],
    ['name' => 'ed_enable_custom_days', 'def' => 1, 'type' => 'int', 's' => 'custom_days'],
    ['name' => 'ed_carrier_zone_adv', 'def' => 0, 'type' => 'int', 's' => 'zone'],
    ['name' => 'ED_WAREHOUSES_MODE', 'def' => 2, 'type' => 'int', 's' => 'warehouses'],
    // TODO Review HR
    // Calendar days
    ['name' => 'ED_CALENDAR_DATE', 'def' => 0, 'type' => 'int', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DATE_FORCE', 'def' => 0, 'type' => 'int', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DATE_DAYS', 'def' => 15, 'type' => 'int', 's' => 'calendar_delivery'],
];

$texts = [
    ['name' => 'ed_class', 'def' => '', 'type' => 'text', 's' => 'design'],
    ['name' => 'ed_custombg', 'def' => '#FFFFFF', 'type' => 'text', 's' => 'design'],
    ['name' => 'ed_customborder', 'def' => '#CCCCCC', 'type' => 'text', 's' => 'design'],
    ['name' => 'ed_force_ip', 'def' => '', 'type' => 'text', 's' => 'advanced_options'],
    ['name' => 'ed_debug_var_ip', 'def' => '', 'type' => 'text', 's' => 'advanced_options'],
    ['name' => 'ed_price_prefix', 'def' => '', 'type' => 'text', 's' => 'design'],
    ['name' => 'ed_price_suffix', 'def' => '', 'type' => 'text', 's' => 'design'],
    ['name' => 'ED_LOCATION_SEL', 'def' => '', 'type' => 'text', 's' => 'design'],
    ['name' => 'ED_TEST_MODE_IPS', 'def' => '', 'type' => 'text', 's' => 'test_mode'],
    ['name' => 'ED_LIST_EXTRA_CONTROLLERS', 'def' => '', 'type' => 'text', 's' => 'list'],
    ['name' => 'ED_SPECIAL_ENCODING', 'def' => '', 'type' => 'text', 's' => 'advanced_options'],
    ['name' => 'ED_DEFAULT_TIMEZONE', 'def' => '', 'type' => 'text', 's' => 'advanced_options'],
    ['name' => 'ED_FORCED_DATE', 'def' => '', 'type' => 'text', 's' => 'advanced_options'],
    ['name' => 'ed_undefined_notify_email', 'def' => '', 'type' => 'text', 's' => 'undefined_days'],
    ['name' => 'ED_CUST_CHECKOUT', 'def' => '', 'type' => 'text', 's' => 'order'],
    ['name' => 'ED_DATE_CUSTOM', 'def' => '', 'type' => 'text', 's' => 'date'],
    ['name' => 'ED_DATE_CUSTOM_REGULAR', 'def' => '', 'type' => 'text', 's' => 'date'],
    ['name' => 'ED_EXPORT_SEP', 'def' => ';', 'type' => 'text', 's' => 'export'],
    ['name' => 'ED_EXPORT_MULTI_SEP', 'def' => ',', 'type' => 'text', 's' => 'export'],
    ['name' => 'ed_dd_admin_email', 'def' => '', 'type' => 'text', 's' => 'delayed_delivery'],
    ['name' => 'ed_dd_test_orders', 'def' => '', 'type' => 'text', 's' => 'delayed_delivery'],
    ['name' => 'ed_cron_secret_key', 'def' => '', 'type' => 'text', 's' => 'delayed_delivery'],
    ['name' => 'ed_dd_test_orders_mode', 'def' => 'file', 'type' => 'text', 's' => 'delayed_delivery'],
    ['name' => 'ed_dd_test_orders_email', 'def' => '', 'type' => 'text', 's' => 'delayed_delivery'],
    ['name' => 'ED_CALENDAR_DISPLAY', 'def' => 'carriers', 'type' => 'text', 's' => 'calendar_delivery'], // last changes
    ['name' => 'ED_CALENDAR_DISPLAY_CART', 'def' => '', 'type' => 'text', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DISPLAY_CARTFOOTER', 'def' => '', 'type' => 'text', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DISPLAY_CARRIERS', 'def' => '', 'type' => 'text', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DISPLAY_PAYMENT', 'def' => '', 'type' => 'text', 's' => 'calendar_delivery'],
    ['name' => 'ED_CALENDAR_DISPLAY_HOOK', 'def' => '', 'type' => 'text', 's' => 'calendar_delivery'],
    ['name' => 'ED_DISPLAY_POPUP_CARRIERS_NAME', 'def' => 'name', 'type' => 'text', 's' => 'design'],
];

$weekdays = [
    ['name' => 'ed_picking_days', 'def' => '1111100', 'type' => 'weekdays', 's' => 'picking'],
];
$json = [
    ['name' => 'ed_picking_limit', 'def' => json_encode(array_fill(0, 7, '23:59')), 'type' => 'weekdays_json', 's' => 'picking'],
];

// Create the combinations of arrays and modes to be able to use
// the SmartModulesMultiConfiguration module to save and restore settings
$arrays = [];
$array_types = ['Manufacturer', 'Supplier'];
$array_modes = ['picking', 'oos', 'custom'];
foreach ($array_modes as $mode) {
    foreach ($array_types as $type) {
        $arrays[] = ['name' => $mode . $type, 'def' => [], 'type' => 'array', 's' => 'date_modifiers', 'ignore_save' => true];
    }
}
$carriers = Carrier::getCarriers($this->context->language->id);
// $zones = Zone::getZones(true);
foreach ($carriers as $carrier) {
    $texts[] = ['name' => 'ED_ORDER_FORCE_CARRIER_' . $carrier['id_reference'], 'def' => 0, 'type' => 'text', 's' => 'order', 'group' => 'carrier|id_carrier'];
}

/* IGNORE SAVE FIELDS */
// Carrier related settings
$weekdays[] = ['name' => 'picking_days', 'def' => '1111100', 'type' => 'weekdays', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$weekdays[] = ['name' => 'shippingdays', 'def' => '1111100', 'type' => 'weekdays', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$json[] = ['name' => 'picking_limit', 'def' => json_encode(array_fill(0, 7, '23:59')), 'type' => 'weekdays_json', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$ints[] = ['name' => 'ed_active', 'def' => '1', 'type' => 'int', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$texts[] = ['name' => 'ed_alias', 'def' => '', 'type' => 'text', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$ints[] = ['name' => 'ed_ignore', 'def' => '0', 'type' => 'int', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$ints[] = ['name' => 'carrier_min', 'def' => '0', 'type' => 'int', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];
$ints[] = ['name' => 'carrier_max', 'def' => '0', 'type' => 'int', 's' => 'carriers', 'ignore_save' => true, 'group' => 'carrier|id_carrier'];

$arrays[] = ['name' => 'carrier_zone', 'def' => [], 'type' => 'array', 's' => 'zone', 'ignore_save' => true];
$ints[] = ['name' => 'ed_cat_picking_days', 'def' => '', 'type' => 'int', 's' => 'picking', 'ignore_save' => true];
$ints[] = ['name' => 'ed_cat_oos_days', 'def' => '', 'type' => 'int', 's' => 'oos', 'ignore_save' => true];
$ints[] = ['name' => 'ed_prod_dis', 'def' => '', 'type' => 'text', 's' => 'disable', 'ignore_save' => true];
$ints[] = ['name' => 'ed_prod_dis', 'def' => '', 'type' => 'text', 's' => 'disable', 'ignore_save' => true];

// Generate Messages with langs
$msgs = [
    ['name' => 'ed_virtual_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
    ['name' => 'ed_preorder_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
    ['name' => 'ed_order_long_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
    ['name' => 'ed_available_date_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
    ['name' => 'ed_custom_date_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
    ['name' => 'ed_undefined_delivery_msg', 'def' => '', 'type' => 'text_lang', 's' => 'messages'],
];
// foreach ($lang_fields as $f) {
//        $msgs[] = array('name' => $f, 'def' => '', 'type' => 'text_lang', 's' => 'messages');
//    }
// }
$langs = [
    ['name' => 'ed_locale', 'def' => '', 'type' => 'lang', 's' => 'lang', 'ignore_save' => true],
];
// foreach (Language::getLanguages() as $lang) {
//    $langs[] = array('name' => 'ed_locale', 'def' => '', 'type' => 'lang', 's' => 'lang', 'ignore_save' => true);
// }

/* HTML Fields */
$html = [
    ['name' => 'ED_ORDER_SUMMARY_LINE', 'def' => '', 'type' => 'html', 's' => 'order'],
];

$hooks_config = [
    'name' => 'module_hooks',
    'def' => [],
    'type' => 'array',
    's' => 'hooks',
    'ignore_save' => true,
];
