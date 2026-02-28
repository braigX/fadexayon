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

function upgrade_module_3_7_5($module)
{
    Configuration::updateValue('ED_ALLOW_MULTIPLE_INSTANCES', 0);

    // Update List Date Format to keep the previous setting
    $dldf = Configuration::get('ED_LIST_DATE_FORMAT');
    if ($dldf > 2) {
        Configuration::updateValue('ED_LIST_DATE_FORMAT', $dldf + 1);
    }

    // Update the Others date formats Basic, Email and Special)
    $dateFormats = ['ED_DATE_TYPE', 'ED_SPECIAL_DATE_FORMAT', 'ED_EMAIL_DATE_FORMAT'];
    foreach ($dateFormats as $dateFormat) {
        $df = Configuration::get($dateFormat);
        if ($df > 1 && $df < 7) {
            Configuration::updateValue($dateFormat, $df + 1);
        } elseif ($df > 6 && $df < 15) {
            Configuration::updateValue($dateFormat, $df + 3);
        } else {
            Configuration::updateValue($dateFormat, 1);
        }
    }

    // Cleanup. Remove old classes files and the city_equivalences file
    $files = ['/classes/deliveryProduct', '/classes/delivery', '/classes/deliveryHelper', '/classes/deliveryCarrier', '/city_equivalences'];
    $base = _PS_MODULE_DIR_ . $module->name;
    foreach ($files as $file) {
        if (file_exists($base . $file . '.php')) {
            unlink($base . $file . '.php');
        }
    }

    return true;
}
