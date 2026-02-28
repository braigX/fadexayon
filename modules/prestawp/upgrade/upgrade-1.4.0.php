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

function upgrade_module_1_4_0($module)
{
    try {
        // new hooks
        $module->installHooks();
        $module->installDefaultSettings();

        // add fields
        Db::getInstance()->execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'prestawp_block`
             ADD `truncate` INT(6) DEFAULT 0;'
        );
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
