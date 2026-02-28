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

// Remove the old ed-order-states.tpl as it has been relocated
function upgrade_module_3_9_8($module)
{
    // Remove the Hook call
    $module->unregisterHook('actionProductSave');

    // File Cleanup. Remove old TPL files
    $templates = ['/views/templates/admin/estimateddelivery', '/views/templates/admin/estimateddelivery-1.7'];
    $base = _PS_MODULE_DIR_ . $module->name;
    foreach ($templates as $template) {
        if (file_exists($base . $template . '.tpl')) {
            unlink($base . $template . '.tpl');
        }
    }

    $db = Db::getInstance();
    $sql = [];

    // Step 1: Create the new `ed_holidays_shop` table with composite primary key
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ed_holidays_shop` (
        `id_holidays` int(11) NOT NULL,
        `id_shop` int(11) NOT NULL,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id_holidays`, `id_shop`),
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    // Execute table creation query
    foreach ($sql as $query) {
        if (!$db->execute($query)) {
            return false;
        }
    }

    // Step 2: Migrate data from `ed_holidays` to `ed_holidays_shop`
    $existing_holidays = $db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ed_holidays`');
    $shops = Shop::getShops();

    foreach ($existing_holidays as $holiday) {
        foreach ($shops as $shop) {
            $insert_query = 'INSERT INTO `' . _DB_PREFIX_ . 'ed_holidays_shop` (id_holidays, id_shop, active)
                             VALUES (' . (int) $holiday['id_holidays'] . ', ' . (int) $shop['id_shop'] . ', ' . (int) $holiday['active'] . ')
                            ON DUPLICATE KEY UPDATE `active` = VALUES(`active`)';
            if (!$db->execute($insert_query)) {
                return false;
            }
        }
    }

    // Step 3: Remove `id_shop` and `active` columns from `ed_holidays`, and update unique constraint
    $sql = [];
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ed_holidays`
          DROP COLUMN `active`';

    // Execute column drop and unique constraint modification
    foreach ($sql as $query) {
        if (!$db->execute($query)) {
            return false;
        }
    }

    return true;
}
