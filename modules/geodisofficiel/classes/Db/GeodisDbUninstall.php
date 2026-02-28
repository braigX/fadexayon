<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbSchema.php';

class GeodisDbUninstall
{
    public function run()
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'configuration WHERE NAME LIKE \''.GEODIS_NAME.'_%\'';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql = 'DELETE FROM '._DB_PREFIX_.'carrier where external_module_name = \''.GEODIS_MODULE_NAME.'\'';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $schema = new GeodisDbSchema();

        return $schema->dropTableWSCapacity() &&
        $schema->dropTableCartCarrier() &&
        $schema->dropTableLog() &&
        $schema->dropTablePackageLabel() &&
        $schema->dropTableRemoval() &&
        $schema->dropTableAccountPrestation() &&
        $schema->dropTableCarrierOption() &&
        $schema->dropTablePrestationOption() &&
        $schema->dropTableCarrier() &&
        $schema->dropTableGroupCarrier() &&
        $schema->dropTableAccount() &&
        $schema->dropTablePrestation() &&
        $schema->dropTableOption() &&
        $schema->dropTableSite() &&
        $schema->dropTableProductWineLiquor() &&
        $schema->dropTableFiscalCode() &&
        $schema->dropTablePackageOrderDetail() &&
        $schema->dropTablePackage() &&
        $schema->dropTableShipmentHistory() &&
        $schema->dropTableDeliveryLabel() &&
        $schema->dropTableShipment() &&
        $schema->dropTableTranslation() &&
        $schema->dropTableSiteLang();
    }
}
