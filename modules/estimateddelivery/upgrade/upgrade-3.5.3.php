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

function upgrade_module_3_5_3($module)
{
    $old_js_vars = _PS_MODULE_DIR_ . $module->name . '/views/templates/front/js-';
    $versions = ['1.5', '1.6'];
    foreach ($versions as $version) {
        $file = $old_js_vars . $version . '.tpl';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    return true;
}
