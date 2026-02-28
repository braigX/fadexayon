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

function upgrade_module_3_1_7()
{
    $files = ['virtual_delivery', 'preorder'];
    foreach ($files as $file) {
        $filename = dirname(__FILE__) . '../views/templates/hook/' . $file;
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    return true;
}
