<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 * @author    Dream me up <prestashop@dream-me-up.fr>
 * @copyright 2007 - 2024 Dream me up
 * @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_4($object)
{
    $sql = [];
    $sql[] = 'CREATE TABLE `' . _DB_PREFIX_ . 'dmuebp_taxrule` (
        `new_tax_rules_group` int(8) NOT NULL,
        `old_tax_rules_group` int(8) NOT NULL
      ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dmuebp_taxrule`
        ADD PRIMARY KEY (`new_tax_rules_group`,`old_tax_rules_group`)';

    foreach ($sql as $query) {
        if (!Db::getInstance()->execute($query)) {
            return false;
        }
    }

    $object->uninstallOverrides();
    $object->installOverrides();

    return true;
}
