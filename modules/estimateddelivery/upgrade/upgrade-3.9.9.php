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
function upgrade_module_3_9_9($module)
{
    // Define the table and column details
    $tableName = _DB_PREFIX_ . 'ed_holidays_shop'; // Replace with your table name
    $referencedColumn = 'id_holidays'; // Column referenced in the foreign key

    try {
        // Fetch all foreign keys referencing the column
        $query = 'SELECT CONSTRAINT_NAME 
                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                  WHERE TABLE_NAME = "' . bqSQL($tableName) . '" 
                  AND TABLE_SCHEMA = "' . _DB_NAME_ . '" 
                  AND REFERENCED_COLUMN_NAME = "' . bqSQL($referencedColumn) . '"';

        $result = Db::getInstance()->executeS($query);

        if (!empty($result)) {
            foreach ($result as $foreignKey) {
                $foreignKeyName = $foreignKey['CONSTRAINT_NAME'];

                // Drop the foreign key
                $dropQuery = 'ALTER TABLE `' . bqSQL($tableName) . '` 
                              DROP FOREIGN KEY `' . bqSQL($foreignKeyName) . '`';

                if (!Db::getInstance()->execute($dropQuery)) {
                    throw new PrestaShopException('Failed to remove foreign key `' . $foreignKeyName . '` from table `' . $tableName . '`.');
                }
            }
        }

        // If successful, return true
        return true;
    } catch (Exception $e) {
        // Display the error message to the user
        $module->_errors[] = $e->getMessage();

        return false;
    }
}
