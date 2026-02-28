<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2019 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_9($module)
{
    try {
        $module->registerHook('actionAdminControllerSetMedia');
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
