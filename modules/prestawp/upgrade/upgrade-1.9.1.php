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

function upgrade_module_1_9_1($module)
{
    try {
        // Set default values for new settings:
        $values = [];
        foreach (Language::getLanguages(false) as $language) {
            $values[$language['id_lang']] = 'category';
        }
        Configuration::updateValue($module->settings_prefix . 'CATEGORY_URL', $values);
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
