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

function upgrade_module_2_7_0()
{
    $sql = 'SELECT id_reference, shippingdays, min, max, picking_days, picking_limit, ed_active, ed_alias FROM ' . _DB_PREFIX_ . 'carrier GROUP BY id_reference ORDER BY id_reference DESC, id_carrier DESC';
    $shops = Shop::getShops(true, null, true);
    $results = Db::getInstance()->executeS($sql);
    $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ed_shop`; CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_shop` (
    `id_reference` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `shippingdays` TEXT NOT NULL,
    `min` int(11) NOT NULL DEFAULT 0,
    `max` int(11) NOT NULL DEFAULT 0,
    `picking_days` TEXT NOT NULL,
    `picking_limit` TEXT NOT NULL,
    `ed_active` TEXT NOT NULL,
    `ed_alias` TEXT NOT NULL,
    CONSTRAINT ed_ref_shop UNIQUE (id_reference, id_shop)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    if (Db::getInstance()->execute($sql) !== false) {
        if (count($results) > 0 && $results !== false) {
            $values = '';
            // Get the column names
            $columns = array_keys($results[0]);
            $columns[] = 'id_shop';
            foreach ($shops as $shop) {
                foreach ($results as $result) {
                    $result['id_shop'] = (int) $shop;
                    $values .= '("' . implode('", "', $result) . '"),';
                }
            }
            $values = rtrim($values, ',');

            $dup_up = '';
            foreach ($columns as $column) {
                if ($column != 'id_shop' && $column != 'id_reference') {
                    $dup_up .= $column . ' = VALUES(' . $column . '), ';
                }
            }
            $dup_up = rtrim($dup_up, ', ');
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ed_shop (' . implode(',', $columns) . ') VALUES ' . $values . ' ON DUPLICATE KEY UPDATE ' . $dup_up;
            if (Db::getInstance()->execute($sql) !== false) {
                $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ed_shop`';
                if (count($results) * count($shops) == Db::getInstance()->getValue($sql)) {
                    // All settings have been created for each shop, can proceed with the deletion of the old data
                    $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'carrier 
                        DROP `shippingdays`,
                        DROP `min`,
                        DROP `max`,
                        DROP `picking_days`,
                        DROP `picking_limit`,
                        DROP `ed_active`,
                        DROP `ed_alias`';
                    Db::getInstance()->execute($sql);
                }
            }
        }
    }

    return true;
}
