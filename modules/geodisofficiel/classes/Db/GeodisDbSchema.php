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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceSynchronize.php';

class GeodisDbSchema
{
    public static function getInstance()
    {
        return new GeodisDbSchema();
    }

    public function getTableName($tableName)
    {
        return _DB_PREFIX_.GEODIS_NAME_SQL.'_'.$tableName;
    }

    public function createTableTranslation()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('translation')).'` (
            `id_translation` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `key` varchar(100) not null,
            `id_lang` int(11) unsigned NOT NULL,
            `value` varchar(2048) not null,
            `date_add` DATETIME not null,
            `date_upd` DATETIME not null,
            PRIMARY KEY (`id_translation`),
            KEY `key` (`key`),
            KEY `id_lang` (`id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        try {
            $details = $this->getColumnDetails('translation', 'value');
            if (Tools::strtolower($details['Type']) != 'varchar(2048)') {
                $sql = 'ALTER TABLE `'.bqSql($this->getTableName('translation')).
                    '` MODIFY COLUMN `value` varchar(2048) not null';
                if (Db::getInstance()->execute($sql) == false) {
                    return false;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        unset($details, $sql);
        return true;
    }

    public function createTableShipment()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('shipment')).'` (
            `id_shipment` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `reference` VARCHAR(30) NOT NULL,
            `id_order` INT(11) UNSIGNED NOT NULL,
            `id_reference_carrier` INT(11) UNSIGNED NOT NULL,
            `id_group_carrier` INT(11) UNSIGNED,
            `id_carrier` INT(11) UNSIGNED,
            `tracking_number` VARCHAR(30),
            `tracking_url` VARCHAR(255),
            `is_complete` TINYINT(1) UNSIGNED DEFAULT 0,
            `is_label_printed` TINYINT(1) UNSIGNED DEFAULT 0,
            `weight` DECIMAL(8,2) NOT NULL,
            `status_code` VARCHAR(30),
            `status_label` VARCHAR(30),
            `incident` TINYINT(1) UNSIGNED DEFAULT 0,
            `recept_number` VARCHAR(30),
            `type_position` VARCHAR(30),
            `departure_date` DATE,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            `is_endlife` TINYINT(1) UNSIGNED DEFAULT 0,
            PRIMARY KEY (`id_shipment`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if ($this->columnExists('shipment', 'id_prestation')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('shipment')).'` DROP COLUMN `id_prestation`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if ($this->columnExists('shipment', 'id_account')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('shipment')).'` DROP COLUMN `id_account`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('shipment', 'id_group_carrier')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('shipment')).'`
                ADD COLUMN `id_group_carrier` INT(11) UNSIGNED AFTER `id_reference_carrier`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('shipment', 'id_carrier')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('shipment')).'`
                ADD COLUMN `id_carrier` INT(11) UNSIGNED AFTER `id_group_carrier`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('shipment', 'is_endlife')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('shipment')).'`
                ADD COLUMN `is_endlife` TINYINT(1) UNSIGNED DEFAULT 0 AFTER `date_upd`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        unset($sql);
        return true;
    }

    public function createTableShipmentHistory()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('shipment_history')).'` (
            `id_shipment_history` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shipment` INT(11) UNSIGNED NOT NULL,
            `status_code` VARCHAR(255) NOT NULL,
            `status_label` VARCHAR(255) NOT NULL,
            `event_date` VARCHAR(255),
            `event_place` VARCHAR(255),
            `event_trace` VARCHAR(255),
            `event_infos` VARCHAR(255),
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_shipment_history`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_id_shipment` FOREIGN KEY (`id_shipment`)
                REFERENCES `'.bqSql($this->getTableName('shipment')).'`(`id_shipment`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function createTablePackage()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('package')).'` (
            `id_package` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `reference` VARCHAR(30) NOT NULL,
            `id_shipment` INT(11) UNSIGNED NOT NULL,
            `width` DECIMAL(8,2) NOT NULL,
            `height` DECIMAL(8,2) NOT NULL,
            `depth` DECIMAL(8,2) NOT NULL,
            `weight` DECIMAL(8,2) NOT NULL,
            `volume` DECIMAL(8,2) NOT NULL,
            `package_type` ENUM(\'pallet\', \'box\'),
            `wine_liquor` VARCHAR(30),
            `status_code` VARCHAR(30),
            `status_label` VARCHAR(255),
            `incident` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_package`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function createTableProductWineLiquor()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('product_wine_liquor')).'` (
            `id_product_wine_liquor` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT(11) UNSIGNED NOT NULL,
            `is_wine_liquor` TINYINT(1) UNSIGNED DEFAULT 0,
            `id_fiscal_code` INT(11) UNSIGNED DEFAULT NULL,
            `detail` VARCHAR(255) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_product_wine_liquor`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_id_product` FOREIGN KEY (`id_product`)
                REFERENCES '._DB_PREFIX_.'product(`id_product`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableWSCapacity()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('ws_capacity')).'` (
            `id_ws_capacity` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `key` VARCHAR(10) DEFAULT NULL,
            `label` VARCHAR(10) DEFAULT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_ws_capacity`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        return true;
    }

    public function createTableFiscalCode()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('fiscal_code')).'` (
            `id_fiscal_code` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `label` VARCHAR(30) DEFAULT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_fiscal_code`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function populateTranslationBack()
    {
        return GeodisDbTranslation::getInstance()->initBack();
    }

    public function populateTranslationFront()
    {
        return GeodisDbTranslation::getInstance()->initFront();
    }

    public function populateTableFiscalCode()
    {
        return GeodisFiscalCode::init();
    }

    public function populateTableOption()
    {
        return GeodisOption::init();
    }

    public function populateTableGroupCarrier()
    {
        return GeodisGroupCarrier::init();
    }

    public function createTablePackageOrderDetail()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('package_order_detail')).'` (
            `id_package_order_detail` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_package` INT(11) UNSIGNED NOT NULL,
            `id_order_detail` INT(11) UNSIGNED NOT NULL,
            `quantity` TINYINT(3) NOT NULL,
            `is_wine_and_liquor` TINYINT(1) UNSIGNED DEFAULT 0,
            `id_fiscal_code` INT(11) UNSIGNED DEFAULT NULL,
            `nb_col` INT(11) UNSIGNED DEFAULT NULL,
            `volume_cl` DECIMAL(8,2) NOT NULL,
            `volume_l` DECIMAL(8,2) NOT NULL,
            `n_mvt` VARCHAR(20) DEFAULT NULL,
            `shipping_duration` INT(11) UNSIGNED DEFAULT 0,
            `fiscal_code_ref` VARCHAR(20) DEFAULT NULL,
            `n_ea` VARCHAR(20) DEFAULT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_package_order_detail`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_id_package` FOREIGN KEY (id_package)
                REFERENCES `'.bqSql($this->getTableName('package')).'`(`id_package`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_id_order_detail` FOREIGN KEY (`id_order_detail`)
                REFERENCES '._DB_PREFIX_.'order_detail(`id_order_detail`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableSite()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('site')).'` (
            `id_site` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(10) NOT NULL,
            `code` VARCHAR(15) NOT NULL,
            `name` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `telephone` VARCHAR(20) NOT NULL,
            `address1` VARCHAR(255) NOT NULL,
            `address2` VARCHAR(255) NOT NULL,
            `zip_code` VARCHAR(10) NOT NULL,
            `city` VARCHAR(50) NOT NULL,
            `id_country` INT(11) UNSIGNED NOT NULL,
            `default` TINYINT(1) NOT NULL,
            `removal` TINYINT(1) NOT NULL DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_site`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_site_id_country` FOREIGN KEY (id_country)
                REFERENCES `'.bqSql(_DB_PREFIX_.'country').'`(`id_country`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if (!$this->columnExists('site', 'removal')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('site')).'`
                ADD COLUMN `removal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `default`';

            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }
        unset($sql);
        return true;
    }

    public function createTableOption()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('option')).'` (
            `id_option` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `code` VARCHAR(15) NOT NULL,
            `attribute` VARCHAR(15) NOT NULL,
            `position` INT(11) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_option`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTablePrestation()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('prestation')).'` (
            `id_prestation` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `code_sa` VARCHAR(50) NOT NULL,
            `code_client` VARCHAR(50) NOT NULL,
            `code_groupe_produits` VARCHAR(15) NOT NULL,
            `code_produit` VARCHAR(15) NOT NULL,
            `code_option` VARCHAR(15) NOT NULL,
            `type` VARCHAR(10) NOT NULL,
            `type_service` VARCHAR(15) NOT NULL,
            `zone` VARCHAR(30) DEFAULT "France",
            `withdrawal_point` TINYINT(1) UNSIGNED DEFAULT 0,
            `withdrawal_agency` TINYINT(1) UNSIGNED DEFAULT 0,
            `web_appointment` TINYINT(1) UNSIGNED DEFAULT 0,
            `tel_appointment` TINYINT(1) UNSIGNED DEFAULT 0,
            `libelle` VARCHAR(50) NOT NULL,
            `deleted` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_prestation`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if (!$this->columnExists('prestation', 'zone')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('prestation')).'`
                ADD COLUMN `zone` VARCHAR(30) DEFAULT "France" AFTER `type_service`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('prestation', 'tel_appointment')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('prestation')).'`
                ADD COLUMN `tel_appointment` TINYINT(1) UNSIGNED DEFAULT 0 AFTER `web_appointment`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if ($this->columnExists('prestation', 'manage_wine_and_liquor')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('prestation')).'`
                DROP COLUMN `manage_wine_and_liquor`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        unset($sql);
        return true;
    }

    public function createTableAccount()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('account')).'` (
            `id_account` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_site` INT(11) UNSIGNED,
            `id_customer_account` INT(11) UNSIGNED,
            `id_agency` INT(11) UNSIGNED NOT NULL,
            `code_sa` VARCHAR(50) NOT NULL,
            `code_client` VARCHAR(50) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_account`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_account_id_site` FOREIGN KEY (id_site)
                REFERENCES `'.bqSql($this->getTableName('site')).'`(`id_site`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_account_id_customer_account` FOREIGN KEY (id_customer_account)
                REFERENCES `'.bqSql($this->getTableName('site')).'`(`id_site`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_account_id_agency` FOREIGN KEY (id_agency)
                REFERENCES `'.bqSql($this->getTableName('site')).'`(`id_site`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableGroupCarrier()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('group_carrier')).'` (
            `id_group_carrier` INT(11) UNSIGNED AUTO_INCREMENT,
            `id_reference_carrier` INT(11) UNSIGNED DEFAULT NULL,
            `reference` VARCHAR(20) NOT NULL,
            `preparation_delay` INT(11) UNSIGNED DEFAULT NULL,
            `active` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_group_carrier`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableCartCarrier()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('cart_carrier')).'` (
            `id_cart_carrier` INT(11) UNSIGNED AUTO_INCREMENT,
            `id_cart` INT(11) UNSIGNED DEFAULT NULL,
            `id_carrier` INT(11) UNSIGNED DEFAULT NULL,
            `id_option_list` VARCHAR(50) DEFAULT NULL,
            `code_withdrawal_point` VARCHAR(10) DEFAULT NULL,
            `code_withdrawal_agency` VARCHAR(10) DEFAULT NULL,
            `info` VARCHAR(2000) DEFAULT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_cart_carrier`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_cart_carrier_id_carrier` FOREIGN KEY (id_carrier)
                REFERENCES `'.bqSql($this->getTableName('carrier')).'`(`id_carrier`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_cart_carrier_id_cart` FOREIGN KEY (id_cart)
                REFERENCES `'._DB_PREFIX_.'cart`(`id_cart`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableCarrier()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('carrier')).'` (
            `id_carrier` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_group_carrier` INT(11) UNSIGNED DEFAULT NULL,
            `id_prestation` INT(11) UNSIGNED NOT NULL,
            `id_account` INT(11) UNSIGNED NOT NULL,
            `active` TINYINT(1) UNSIGNED DEFAULT 0,
            `name` VARCHAR(50) NOT NULL,
            `description` VARCHAR(255) NOT NULL,
            `price` decimal(20,2) NOT NULL,
            `free_shipping_from` decimal(20,2) NOT NULL,
            `additional_shipping_cost` TINYINT(1) UNSIGNED DEFAULT 1,
            `enable_price_fixed` TINYINT(1) UNSIGNED DEFAULT 1,
            `enable_price_according` TINYINT(1) UNSIGNED DEFAULT 1,
            `enable_free_shipping` TINYINT(1) UNSIGNED DEFAULT 1,
            `deleted` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_carrier`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_carrier_id_prestation` FOREIGN KEY (id_prestation)
                REFERENCES `'.bqSql($this->getTableName('prestation')).'`(`id_prestation`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_carrier_id_account` FOREIGN KEY (id_account)
                REFERENCES `'.bqSql($this->getTableName('account')).'`(`id_account`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_carrier_id_group_carrier` FOREIGN KEY (id_group_carrier)
                REFERENCES `'.bqSql($this->getTableName('group_carrier')).'`(`id_group_carrier`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if (!$this->columnExists('carrier', 'free_shipping_from')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                ADD COLUMN `free_shipping_from` decimal(20,2) NOT NULL AFTER `price`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if ($this->columnExists('carrier', 'offer_additional_shipping_cost')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                CHANGE COLUMN `offer_additional_shipping_cost` `additional_shipping_cost`
                TINYINT(1) UNSIGNED DEFAULT 1';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('carrier', 'additional_shipping_cost')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                ADD COLUMN `additional_shipping_cost` TINYINT(1) UNSIGNED DEFAULT 1 AFTER `free_shipping_from`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('carrier', 'enable_price_fixed')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                ADD COLUMN `enable_price_fixed` TINYINT(1) UNSIGNED DEFAULT 1 AFTER `additional_shipping_cost`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('carrier', 'enable_price_according')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                ADD COLUMN `enable_price_according` TINYINT(1) UNSIGNED DEFAULT 1 AFTER `enable_price_fixed`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        if (!$this->columnExists('carrier', 'enable_free_shipping')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('carrier')).'`
                ADD COLUMN `enable_free_shipping` TINYINT(1) UNSIGNED DEFAULT 1 AFTER `enable_price_according`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        unset($sql);
        return true;
    }

    public function createTablePrestationOption()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('prestation_option')).'` (
            `id_prestation_option` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_prestation` INT(11) UNSIGNED NOT NULL,
            `id_option` INT(11) UNSIGNED NOT NULL,
            `active` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_prestation_option`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_prestation_option_id_prestation` FOREIGN KEY (id_prestation)
                REFERENCES `'.bqSql($this->getTableName('prestation')).'`(`id_prestation`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_prestation_option_id_option` FOREIGN KEY (id_option)
                REFERENCES `'.bqSql($this->getTableName('option')).'`(`id_option`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableCarrierOption()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('carrier_option')).'` (
            `id_carrier_option` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_carrier` INT(11) UNSIGNED NOT NULL,
            `id_option` INT(11) UNSIGNED NOT NULL,
            `price_impact` decimal(20,2) NOT NULL,
            `active` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_carrier_option`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_carrier_option_id_carrier` FOREIGN KEY (id_carrier)
                REFERENCES `'.bqSql($this->getTableName('carrier')).'`(`id_carrier`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_carrier_option_id_option` FOREIGN KEY (id_option)
                REFERENCES `'.bqSql($this->getTableName('option')).'`(`id_option`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableAccountPrestation()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('account_prestation')).'` (
            `id_account_prestation` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_account` INT(11) UNSIGNED NOT NULL,
            `id_prestation` INT(11) UNSIGNED NOT NULL,
            `manage_wine_and_liquor` TINYINT(1) UNSIGNED DEFAULT 0,
            `delay` INT(11) UNSIGNED NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_account_prestation`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_account_prestation_id_prestation` FOREIGN KEY (id_prestation)
                REFERENCES `'.bqSql($this->getTableName('prestation')).'`(`id_prestation`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_account_prestation_id_account` FOREIGN KEY (id_account)
                REFERENCES `'.bqSql($this->getTableName('account')).'`(`id_account`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if (!$this->columnExists('account_prestation', 'manage_wine_and_liquor')) {
            $sql = 'ALTER TABLE `'.bqSql($this->getTableName('account_prestation')).'`
                ADD COLUMN `manage_wine_and_liquor` TINYINT(1) UNSIGNED DEFAULT 0 AFTER `delay`';
            if (Db::getInstance()->execute($sql) == false) {
                return false;
            }
        }

        unset($sql);
        return true;
    }

    public function createTableRemoval()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('removal')).'` (
            `id_removal` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `reference` VARCHAR(20) NOT NULL,
            `recept_number` VARCHAR(30),
            `id_site` INT(11) UNSIGNED NOT NULL,
            `id_account` INT(11) UNSIGNED NOT NULL,
            `id_prestation` INT(11) UNSIGNED NOT NULL,
            `number_of_box` INT(11) UNSIGNED NOT NULL,
            `number_of_pallet` INT(11) UNSIGNED NOT NULL,
            `weight` DECIMAL (8,2) NOT NULL,
            `volume` DECIMAL (8,2) NOT NULL,
            `is_hazardous` TINYINT(1) UNSIGNED DEFAULT 0,
            `fiscal_code` INT(11) UNSIGNED DEFAULT 0,
            `legal_volume` DECIMAL (8,4) DEFAULT NULL,
            `total_volume` DECIMAL (8,2) DEFAULT NULL,
            `sent` TINYINT(1) UNSIGNED DEFAULT 0,
            `observations` VARCHAR(255) DEFAULT NULL,
            `removal_date` DATETIME NOT NULL,
            `time_slot` ENUM(\'daytime\', \'morning\', \'afternoon\'),
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_removal`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_removal_id_site` FOREIGN KEY (id_site)
                REFERENCES `'.bqSql($this->getTableName('site')).'`(`id_site`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_removal_id_account` FOREIGN KEY (id_account)
                REFERENCES `'.bqSql($this->getTableName('account')).'`(`id_account`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_removal_id_prestation` FOREIGN KEY (id_prestation)
                REFERENCES `'.bqSql($this->getTableName('prestation')).'`(`id_prestation`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableDeliveryLabel()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('delivery_label')).'` (
            `id_delivery_label` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shipment` INT(11) UNSIGNED NOT NULL,
            `content` MEDIUMBLOB NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_delivery_label`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_delivery_label_id_shipment` FOREIGN KEY (id_shipment)
                REFERENCES `'.bqSql($this->getTableName('shipment')).'`(`id_shipment`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableLog()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('log')).'` (
            `id_geodis_log` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `message` TEXT NOT NULL,
            `is_error` TINYINT(1) UNSIGNED DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_geodis_log`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTablePackageLabel()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('package_label')).'` (
            `id_package_label` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shipment` INT(11) UNSIGNED NOT NULL,
            `format` ENUM(\'pdf\', \'small_pdf\', \'file1\', \'file2\'),
            `content` MEDIUMBLOB NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME,
            PRIMARY KEY (`id_package_label`),
            CONSTRAINT `fk_'.GEODIS_MODULE_NAME.'_package_label_id_shipment` FOREIGN KEY (`id_shipment`)
                REFERENCES `'.bqSql($this->getTableName('shipment')).'`(`id_shipment`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function createTableSiteLang()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.bqSql($this->getTableName('site_lang')).'` (
            `id_site` INT(11) UNSIGNED NOT NULL,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `default` TINYINT(1) UNSIGNED DEFAULT 0,
            PRIMARY KEY (`id_site`, `id_shop`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        unset($sql);
        return true;
    }

    public function dropTableWSCapacity()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('ws_capacity')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        return true;
    }

    public function dropTableFiscalCode()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('fiscal_code')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableCartCarrier()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('cart_carrier')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        return true;
    }

    public function dropTableLog()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('log')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableDeliveryLabel()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('delivery_label')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTablePackageLabel()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('package_label')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableRemoval()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('removal')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableAccountPrestation()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('account_prestation')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableCarrierOption()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('carrier_option')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTablePrestationOption()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('prestation_option')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableGroupCarrier()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('group_carrier')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableCarrier()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('carrier')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableAccount()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('account')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTablePrestation()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('prestation')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        return true;
    }

    public function dropTableOption()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('option')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableSite()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('site')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableTranslation()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('translation')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableShipment()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('shipment')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableShipmentHistory()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('shipment_history')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTablePackage()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('package')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTablePackageOrderDetail()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('package_order_detail')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableProductWineLiquor()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('product_wine_liquor')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function dropTableSiteLang()
    {
        $sql = 'DROP TABLE IF EXISTS `'.bqSql($this->getTableName('site_lang')).'`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        unset($sql);
        return true;
    }

    public function columnExists($tableName, $columnName)
    {
        try {
            $this->getColumnDetails($tableName, $columnName);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function getColumnDetails($tableName, $columnName)
    {
        if (!$this->tableExists($tableName)) {
            throw new Exception("No table '$tableName'");
        }

        $sql = 'SHOW COLUMNS FROM `'.bqSql($this->getTableName($tableName)).'`
            LIKE \''.pSql($columnName).'\'';

        $result = Db::getInstance()->executeS($sql, true, false);
        if (!$result) {
            throw new Exception("No column '$columnName' on table '$tableName'");
        }

        unset($sql);
        return $result[0];
    }

    public function tableExists($tableName)
    {
        $sql = 'SHOW TABLES LIKE \''.pSql($this->getTableName($tableName)).'\'';

        $result = Db::getInstance()->executeS($sql, true, false);
        if (!$result) {
            return false;
        }

        unset($sql);
        return true;
    }
}
