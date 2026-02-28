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

function upgrade_module_3_0_6($object)
{
    // Remplacement des valeurs serializées par du json_encode
    $sql = 'SELECT id_configuration, `value` 
            FROM `' . _DB_PREFIX_ . 'configuration`
            WHERE (
                name = "DMUEBP_COMPTE_PAYMENT"
                OR name = "DMUEBP_COMPTE_CUSTOMERS"
                OR name = "DMUEBP_CATEGORY_TTC"
                OR name = "DMUEBP_CATEGORY_HT"
            )
            AND value IS NOT NULL';
    $configs = Db::getInstance()->ExecuteS($sql);
    if (!empty($configs)) {
        foreach ($configs as $config) {
            if (!empty($config['value'])) {
                $new_value = json_encode(unserialize($config['value']));
                $sql_new = 'UPDATE `' . _DB_PREFIX_ . 'configuration` 
                SET value = "' . pSQL($new_value) . '" 
                WHERE id_configuration = ' . (int) $config['id_configuration'];
                Db::getInstance()->Execute($sql_new);
            }
        }
    }
    return true;
}
