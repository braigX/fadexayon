<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_ClassLoader.php';

/**
 * Class TNTOfficielAccount
 */
class TNTOfficielAccount extends ObjectModel
{
    const PASSWORD_REPLACE = '%p#c`Q9,6GSP?U4]e]Zst';

    // id_tntofficiel_account
    public $id;

    /** @var int Shop Group ID association. */
    public $id_shop_group;
    /** @var int Shop ID association. */
    public $id_shop;

    /*
     * Account.
     */

    /** @var string Account number (8 digits). */
    public $account_number;
    /** @var string MyTNT user email (ID). */
    public $account_login;
    /** @var string SHA1 Hex of MyTNT password. */
    public $account_password;
    /** @var int UNIX timestamp of the last validation (e.g :: 1523019418). */
    public $account_validated;

    /*
     * Sender.
     */

    /** @var string Sender Company. */
    public $sender_company;
    /** @var string Sender address line 1. */
    public $sender_address1;
    /** @var string Sender address line 2. */
    public $sender_address2;
    /** @var string Sender zip code. */
    public $sender_zipcode;
    /** @var string Sender city. */
    public $sender_city;
    /** @var string  first name. */
    public $sender_firstname;
    /** @var string Sender last name. */
    public $sender_lastname;
    /** @var string Sender email. */
    public $sender_email;
    /** @var string Sender phone (without separators). */
    public $sender_phone;

    /*
     * Pickup.
     */

    /** @var string Pickup type ['REGULAR', 'OCCASIONAL']. */
    public $pickup_type;
    /** @var string Pickup delivery time for type REGULAR. */
    public $pickup_driver_time;
    /** @var string Pickup closing time for type OCCASIONAL. */
    public $pickup_closing_time;
    /** @var int Pickup days of preparation. */
    public $pickup_preparation_days;
    /** @var string Pickup label type ['STDA4', 'THERMAL', 'THERMAL,NO_LOGO']. */
    public $pickup_label_type;
    /** @var bool Pickup display number (BO). */
    public $pickup_display_number;

    /*
     * Delivery.
     */

    /** @var bool Display EDD (Estimated Delivery Date) on FO. */
    public $delivery_display_edd;
    /** @var bool Delivery customer notification. */
    public $delivery_notification;
    /** @var bool Delivery Insurance. */
    public $delivery_insurance;

    /*
     * Zone.
     */

    /** @var string Departments list in zone 1 (PHP serialized). */
    public $zone1_departments;
    /** @var string Departments list in zone 2 (PHP serialized). */
    public $zone2_departments;

    /*
     * API.
     */

    /** @var string */
    public $api_google_map_key;

    /*
     * Status
     */

    /** @var int OrderStatus that trigger shipment creation. Default 4:PS_OS_SHIPPING. 0 to disable. */
    public $os_shipment_save_id;
    /** @var int OrderStatus. 0 to disabled. */
    public $os_shipment_after_id;
    /** @var int OrderStatus. 0 to disabled. */
    public $os_parcel_takenincharge_id;
    /** @var int OrderStatus to apply when all parcels are delivered. Default 5:PS_OS_DELIVERED. 0 to disable. */
    public $os_parcel_alldelivered_id;
    /** @var int OrderStatus. 0 to disabled. */
    public $os_parcel_alldeliveredtopoint_id;
    /** @var bool parcel check enable. */
    public $os_parcel_check_enable;
    /** @var int Time interval in second. */
    public $os_parcel_check_rate;

    /*
     * State.
     */

    /** @var bool status */
    public $active = true;
    /** @var bool True if has been deleted (staying in database as deleted) */
    public $deleted = 0;
    /** @var string creation date */
    public $date_add;
    /** @var string last modification date */
    public $date_upd;

    /** @var TNTOfficiel_SoapClient */
    protected $objWebServiceTNT = null;

    public static $definition = array(
        'table' => 'tntofficiel_account',
        'primary' => 'id_tntofficiel_account',
        'fields' => array(
            'id_shop_group' => array(
                'type' => ObjectModel::TYPE_NOTHING,
                'validate' => 'isUnsignedId',
            ),
            'id_shop' => array(
                'type' => ObjectModel::TYPE_NOTHING,
                'validate' => 'isUnsignedId',
            ),
            'account_number' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 8,
            ),
            'account_login' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 128,
            ),
            'account_password' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 40,
            ),
            'account_validated' => array(
                'type' => ObjectModel::TYPE_INT,
            ),

            'sender_company' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_address1' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_address2' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_zipcode' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 10,
            ),
            'sender_city' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_firstname' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_lastname' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'sender_email' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 80, // 128 ?
            ),
            'sender_phone' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 15,
            ),

            'pickup_type' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 16,
            ),
            'pickup_driver_time' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
            'pickup_closing_time' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
            'pickup_preparation_days' => array(
                'type' => ObjectModel::TYPE_INT,
            ),
            'pickup_label_type' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'pickup_display_number' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),

            'delivery_display_edd' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'delivery_notification' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'delivery_insurance' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),

            'zone1_departments' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
            'zone2_departments' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),

            'api_google_map_key' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 64,
            ),

            'os_shipment_save_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'os_shipment_after_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'os_parcel_takenincharge_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'os_parcel_alldelivered_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'os_parcel_alldeliveredtopoint_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'os_parcel_check_enable' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'os_parcel_check_rate' => array(
                'type' => ObjectModel::TYPE_INT,
            ),
            'active' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'deleted' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'date_add' => array(
                'type' => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'date_upd' => array(
                'type' => ObjectModel::TYPE_DATE,
                'validate' => 'isDate',
            ),
        ),
    );

    private static $arrDepartments = array(
        'Ain' => '01',
        'Aisne' => '02',
        'Allier' => '03',
        'Alpes-de-Haute-Provence' => '04',
        'Hautes-Alpes' => '05',
        'Alpes-Maritimes' => '06',
        'Ardèche' => '07',
        'Ardennes' => '08',
        'Ariège' => '09',
        'Aube' => '10',
        'Aude' => '11',
        'Aveyron' => '12',
        'Bouches-du-Rhône' => '13',
        'Calvados' => '14',
        'Cantal' => '15',
        'Charente' => '16',
        'Charente-Maritime' => '17',
        'Cher' => '18',
        'Corrèze' => '19',
        'Corse' => '20',
        'Côte-d\'Or' => '21',
        'Côtes-d\'Armor' => '22',
        'Creuse' => '23',
        'Dordogne' => '24',
        'Doubs' => '25',
        'Drôme' => '26',
        'Eure' => '27',
        'Eure-et-Loir' => '28',
        'Finistère' => '29',
        'Gard' => '30',
        'Haute-Garonne' => '31',
        'Gers' => '32',
        'Gironde' => '33',
        'Hérault' => '34',
        'Ille-et-Vilaine' => '35',
        'Indre' => '36',
        'Indre-et-Loire' => '37',
        'Isère' => '38',
        'Jura' => '39',
        'Landes' => '40',
        'Loir-et-Cher' => '41',
        'Loire' => '42',
        'Haute-Loire' => '43',
        'Loire-Atlantique' => '44',
        'Loiret' => '45',
        'Lot' => '46',
        'Lot-et-Garonne' => '47',
        'Lozère' => '48',
        'Maine-et-Loire' => '49',
        'Manche' => '50',
        'Marne' => '51',
        'Haute-Marne' => '52',
        'Mayenne' => '53',
        'Meurthe-et-Moselle' => '54',
        'Meuse' => '55',
        'Morbihan' => '56',
        'Moselle' => '57',
        'Nièvre' => '58',
        'Nord' => '59',
        'Oise' => '60',
        'Orne' => '61',
        'Pas-de-Calais' => '62',
        'Puy-de-Dôme' => '63',
        'Pyrénées-Atlantiques' => '64',
        'Hautes-Pyrénées' => '65',
        'Pyrénées-Orientales' => '66',
        'Bas-Rhin' => '67',
        'Haut-Rhin' => '68',
        'Rhône' => '69',
        'Haute-Saône' => '70',
        'Saône-et-Loire' => '71',
        'Sarthe' => '72',
        'Savoie' => '73',
        'Haute-Savoie' => '74',
        'Paris' => '75',
        'Seine-Maritime' => '76',
        'Seine-et-Marne' => '77',
        'Yvelines' => '78',
        'Deux-Sèvres' => '79',
        'Somme' => '80',
        'Tarn' => '81',
        'Tarn-et-Garonne' => '82',
        'Var' => '83',
        'Vaucluse' => '84',
        'Vendée' => '85',
        'Vienne' => '86',
        'Haute-Vienne' => '87',
        'Vosges' => '88',
        'Yonne' => '89',
        'Territoire-de-Belfort' => '90',
        'Essonne' => '91',
        'Hauts-de-Seine' => '92',
        'Seine-Saint-Denis' => '93',
        'Val-de-Marne' => '94',
        'Val-d\'Oise' => '95',
        'Monaco' => '98',
    );

    /** @var array Available pickup label Type. */
    private static $arrPickupLabelTypes = array(
        'STDA4',
        'THERMAL',
        'THERMAL,NO_LOGO',
    );

    // cache and prevent race condition.
    private static $arrLoadedEntities = array();

    /**
     * Creates the tables needed by the model.
     *
     * @return bool
     */
    public static function createTables()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $strTablePrefix = _DB_PREFIX_;
        $strTableEngine = _MYSQL_ENGINE_;

        $strTableName = $strTablePrefix . TNTOfficielAccount::$definition['table'];

        // If table exist.
        if (TNTOfficiel_Tools::isTableExist($strTableName) === true) {
            // Update table.
            TNTOfficielAccount::upgradeTables010009();
            TNTOfficielAccount::upgradeTables010010();
        } else {
            $intOrderStateShipmentSaveID = (int)Configuration::get('PS_OS_SHIPPING');
            $intOrderStateAllDeliveredID = (int)Configuration::get('PS_OS_DELIVERED');

            // Create table.
            $strSQLCreateAccount = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_account`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop_group`                 INT(10) UNSIGNED DEFAULT NULL,
    `id_shop`                       INT(10) UNSIGNED DEFAULT NULL,
-- Account.
    `account_number`                VARCHAR(10) NOT NULL DEFAULT '',
    `account_login`                 VARCHAR(128) NOT NULL DEFAULT '',
    `account_password`              VARCHAR(40) NOT NULL DEFAULT '',
    `account_validated`             INT(10) UNSIGNED NULL DEFAULT NULL,
-- Sender.
    `sender_company`                VARCHAR(32) NOT NULL DEFAULT '',
    `sender_address1`               VARCHAR(32) NOT NULL DEFAULT '',
    `sender_address2`               VARCHAR(32) NOT NULL DEFAULT '',
    `sender_zipcode`                VARCHAR(10) NOT NULL DEFAULT '',
    `sender_city`                   VARCHAR(32) NOT NULL DEFAULT '',
    `sender_firstname`              VARCHAR(32) NOT NULL DEFAULT '',
    `sender_lastname`               VARCHAR(32) NOT NULL DEFAULT '',
    `sender_email`                  VARCHAR(80) NOT NULL DEFAULT '',
    `sender_phone`                  VARCHAR(15) NOT NULL DEFAULT '',
-- Pickup.
    `pickup_type`                   VARCHAR(25) NOT NULL DEFAULT 'REGULAR',
    `pickup_driver_time`            TIME NOT NULL DEFAULT '17:00:00',
    `pickup_closing_time`           TIME NOT NULL DEFAULT '17:00:00',
    `pickup_preparation_days`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `pickup_label_type`             VARCHAR(32) NOT NULL DEFAULT 'STDA4',
    `pickup_display_number`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `delivery_display_edd`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `delivery_notification`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    `delivery_insurance`            TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
-- Zone.
    `zone1_departments`             TEXT NULL,
    `zone2_departments`             TEXT NULL,
-- API.
    `api_google_map_key`            VARCHAR(64) NOT NULL DEFAULT '',
-- OrderState.
    `os_shipment_save_id`           INT(10) UNSIGNED NOT NULL DEFAULT '${intOrderStateShipmentSaveID}',
    `os_shipment_after_id`          INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `os_parcel_takenincharge_id`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `os_parcel_alldelivered_id`     INT(10) UNSIGNED NOT NULL DEFAULT '${intOrderStateAllDeliveredID}',
    `os_parcel_alldeliveredtopoint_id`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `os_parcel_check_enable`        TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `os_parcel_check_rate`          INT(10) UNSIGNED NOT NULL DEFAULT '21600',
-- State.
    `active`                        TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `deleted`                       TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    -- Dates d'ajout et de modification.
    `date_add`                      DATETIME NOT NULL DEFAULT '0000-00-00',
    `date_upd`                      DATETIME NOT NULL DEFAULT '0000-00-00',
-- Key.
    PRIMARY KEY (`id_tntofficiel_account`),
    INDEX `id_shop` (`id_shop`),
    INDEX `id_shop_group` (`id_shop_group`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

            $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreateAccount);
            if (is_string($boolDBResult)) {
                TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

                return false;
            }

            TNTOfficiel_Logger::logInstall($strLogMessage);
        }

        return TNTOfficielAccount::checkTables();
    }

    /**
     * Upgrade table.
     *
     * @return bool
     */
    public static function upgradeTables010009()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = __CLASS__ . '::' . __FUNCTION__;

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielAccount::$definition['table'];

        $intOrderStateShipmentSaveID = (int)Configuration::get('PS_OS_SHIPPING');
        $intOrderStateAllDeliveredID = (int)Configuration::get('PS_OS_DELIVERED');

        // Upgrade table.
        $strSQLTableAccountAddColumns = <<<SQL
ALTER TABLE `${strTableName}`
    ADD COLUMN `os_shipment_save_id`        INT(10) UNSIGNED NOT NULL DEFAULT '${intOrderStateShipmentSaveID}' AFTER `api_google_map_key`,
    ADD COLUMN `os_shipment_after_id`       INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `os_shipment_save_id`,
    ADD COLUMN `os_parcel_takenincharge_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `os_shipment_after_id`,
    ADD COLUMN `os_parcel_alldelivered_id`  INT(10) UNSIGNED NOT NULL DEFAULT '${intOrderStateAllDeliveredID}' AFTER `os_parcel_takenincharge_id`,
    ADD COLUMN `os_parcel_alldeliveredtopoint_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `os_parcel_alldelivered_id`,
    ADD COLUMN `os_parcel_check_enable`     TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `os_parcel_alldeliveredtopoint_id`,
    ADD COLUMN `os_parcel_check_rate`       INT(10) UNSIGNED NOT NULL DEFAULT '21600' AFTER `os_parcel_check_enable`;
SQL;

        $arrRequireColumnsList = array('api_google_map_key');
        $arrMissingColumnsList = array(
            'os_shipment_save_id',
            'os_shipment_after_id',
            'os_parcel_takenincharge_id',
            'os_parcel_alldelivered_id',
            'os_parcel_alldeliveredtopoint_id',
            'os_parcel_check_enable',
            'os_parcel_check_rate',
        );

        // If table exist, but not some columns.
        if (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrRequireColumnsList) === true
            && TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrMissingColumnsList) === false
        ) {
            // Update table.
            $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLTableAccountAddColumns);
            if (is_string($boolDBResult)) {
                TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

                return false;
            }
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return true;
    }

    /**
     * Upgrade table.
     *
     * @return bool
     */
    public static function upgradeTables010010()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = __CLASS__ . '::' . __FUNCTION__;

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielAccount::$definition['table'];

        // Upgrade table.
        $strSQLTableAccountAddColumns = <<<SQL
ALTER TABLE `${strTableName}`
    ADD COLUMN `delivery_insurance`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `delivery_notification`;
SQL;

        $arrRequireColumnsList = array('delivery_notification');
        $arrMissingColumnsList = array('delivery_insurance');

        // If table exist, but not some columns.
        if (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrRequireColumnsList) === true
            && TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrMissingColumnsList) === false
        ) {
            // Update table.
            $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLTableAccountAddColumns);
            if (is_string($boolDBResult)) {
                TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

                return false;
            }
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return true;
    }

    /**
     * Check if table and columns exist.
     *
     * @return bool
     */
    public static function checkTables()
    {
        TNTOfficiel_Logstack::log();

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielAccount::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielAccount::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID);

        $this->initWebService();
    }

    /**
     * Do shop ID and shop group ID consistency.
     *
     * @param int $intArgShopID
     * @param int $intArgShopGroupID
     *
     * @return bool
     */
    private static function correctShopAndGroupID(&$intArgShopID = null, &$intArgShopGroupID = null)
    {
        TNTOfficiel_Logstack::log();

        $intShopID = null;
        $objPSShop = null;

        if (Shop::isFeatureActive() && $intArgShopID > 0) {
            $intShopID = (int)$intArgShopID;
            $objPSShop = TNTOfficielAccount::getPSShopByID($intShopID);
            // If Shop object available.
            if ($objPSShop !== null) {
                $intArgShopGroupID = (int)$objPSShop->id_shop_group;
            } else {
                $intShopID = null;
                $intArgShopGroupID = null;
            }
        }

        $intShopGroupID = null;
        $objPSShopGroup = null;

        if (Shop::isFeatureActive() && $intArgShopGroupID > 0) {
            $intShopGroupID = (int)$intArgShopGroupID;
            $objPSShopGroup = TNTOfficielAccount::getPSShopGroupByID($intShopGroupID);
            // If ShopGroup object not available.
            if ($objPSShopGroup === null) {
                $intShopGroupID = null;
            }
        }

        // Correct.
        $intArgShopID = (int)$intShopID;
        $intArgShopGroupID = (int)$intShopGroupID;

        return true;
    }

    /**
     * Load an existing object model or create a new one with a new ID.
     *
     * @param int $intArgAccountID (optional)
     *
     * @return TNTOfficielAccount|null
     */
    public static function loadAccountID($intArgAccountID = null, $boolArgCreate = false)
    {
        TNTOfficiel_Logstack::log();

        $intAccountID = (int)$intArgAccountID;
        // Create.
        if ($intAccountID === 0 && $boolArgCreate === true) {
            // Create a new TNT account entry.
            $objTNTAccountModelCreate = new TNTOfficielAccount(null);
            // Apply default.
            $objTNTAccountModelCreate->setPickupType('REGULAR');
            $objTNTAccountModelCreate->pickup_driver_time = '17:00:00';
            $objTNTAccountModelCreate->pickup_closing_time = '17:00:00';
            $objTNTAccountModelCreate->pickup_label_type = 'STDA4';
            $objTNTAccountModelCreate->delivery_notification = '1';
            $objTNTAccountModelCreate->delivery_insurance = '0';
            $objTNTAccountModelCreate->save();
            $intAccountID = (int)$objTNTAccountModelCreate->id;
            unset($objTNTAccountModelCreate);
        }

        // No new account ID.
        if (!($intAccountID > 0)) {
            return null;
        }

        $strEntityID = $intAccountID . '-' . (int)null . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielAccount::$arrLoadedEntities)) {
            $objTNTAccountModel = TNTOfficielAccount::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTAccountModel)
                && (int)$objTNTAccountModel->id === $intAccountID
            ) {
                return $objTNTAccountModel;
            }
        }

        // Load existing TNT account entry.
        // or reload after create, to get default DB values after creation.
        $objTNTAccountModel = new TNTOfficielAccount($intAccountID);
        // Check.
        if (!Validate::isLoadedObject($objTNTAccountModel)
            || (int)$objTNTAccountModel->id !== $intAccountID
        ) {
            return null;
        }

        $objTNTAccountModel->id = (int)$objTNTAccountModel->id;
        $objTNTAccountModel->id_shop_group = (int)$objTNTAccountModel->id_shop_group;
        $objTNTAccountModel->id_shop = (int)$objTNTAccountModel->id_shop;
        $objTNTAccountModel->account_validated = (int)$objTNTAccountModel->account_validated;
        $objTNTAccountModel->os_shipment_save_id = (int)$objTNTAccountModel->os_shipment_save_id;
        $objTNTAccountModel->os_shipment_after_id = (int)$objTNTAccountModel->os_shipment_after_id;
        $objTNTAccountModel->os_parcel_takenincharge_id = (int)$objTNTAccountModel->os_parcel_takenincharge_id;
        $objTNTAccountModel->os_parcel_alldelivered_id = (int)$objTNTAccountModel->os_parcel_alldelivered_id;
        $objTNTAccountModel->os_parcel_alldeliveredtopoint_id = (int)$objTNTAccountModel->os_parcel_alldeliveredtopoint_id;
        $objTNTAccountModel->os_parcel_check_enable = (bool)$objTNTAccountModel->os_parcel_check_enable;
        $objTNTAccountModel->os_parcel_check_rate = (int)$objTNTAccountModel->os_parcel_check_rate;

        TNTOfficielAccount::$arrLoadedEntities[$strEntityID] = $objTNTAccountModel;

        return $objTNTAccountModel;
    }

    /**
     * @param TNTOfficielAccount $objArgTNTAccountModelSource
     *
     * @return $this
     */
    private function inherit(TNTOfficielAccount $objArgTNTAccountModelSource = null)
    {
        TNTOfficiel_Logstack::log();

        $arrExcludingProperties = array(
            'id_shop_group',
            'id_shop',
            //'account_number',
            'date_add',
            'date_upd',
        );

        // If a source model exist.
        if ($objArgTNTAccountModelSource !== null) {
            // Copy.
            foreach (TNTOfficielAccount::$definition['fields'] as $strPropName => $arrPropDefinition) {
                if (in_array($strPropName, $arrExcludingProperties, true)) {
                    continue;
                }
                if (property_exists($objArgTNTAccountModelSource, $strPropName)) {
                    $this->{$strPropName} = $objArgTNTAccountModelSource->{$strPropName};
                }
            }
        }

        // Init webservice and auth.
        $this->initWebService();

        return $this;
    }

    /**
     * Create an account using shop or shop group ID, optionally cloned from any other account object model.
     *
     * @param int                $intArgShopID
     * @param int                $intArgShopGroupID
     * @param TNTOfficielAccount $objTNTAccountModelSource
     *
     * @return TNTOfficielAccount
     */
    private static function createContextShopID(
        $intArgShopID = null,
        $intArgShopGroupID = null,
        TNTOfficielAccount $objTNTAccountModelSource = null
    ) {
        TNTOfficiel_Logstack::log();

        $intShopID = (int)$intArgShopID;
        $intShopGroupID = (int)$intArgShopGroupID;

        TNTOfficielAccount::correctShopAndGroupID($intShopID, $intShopGroupID);

        // Create a new Account model.
        $objTNTAccountModelCreate = TNTOfficielAccount::loadAccountID(null, true);
        // Inherit.
        $objTNTAccountModelCreate->inherit($objTNTAccountModelSource);

        // Apply shop and group ID.
        $objTNTAccountModelCreate->id_shop_group = $intShopGroupID;
        $objTNTAccountModelCreate->id_shop = $intShopID;
        $objTNTAccountModelCreate->save();

        return $objTNTAccountModelCreate;
    }

    /**
     * Load the account depending of the current shop context. If unexist, create an account from the nearest parent.
     *
     * @param bool $boolArgAllowCreate Create
     *
     * @return TNTOfficielAccount
     */
    public static function loadContextShop($intArgShopID = null, $intArgShopGroupID = null, $boolArgAllowCreate = true)
    {
        TNTOfficiel_Logstack::log();

        if ($intArgShopID === null) {
            $intShopID = (int)Shop::getContextShopID();
        } else {
            $intShopID = (int)$intArgShopID;
        }

        if ($intArgShopID === null) {
            $intShopGroupID = (int)Shop::getContextShopGroupID(true);
        } else {
            $intShopGroupID = (int)$intArgShopGroupID;
        }

        TNTOfficielAccount::correctShopAndGroupID($intShopID, $intShopGroupID);

        try {
            // Search exact account for shop or group.
            $objTNTAccountModelStrict = TNTOfficielAccount::searchContextShopID($intShopID, $intShopGroupID);

            // Create an account if unexist, or shop ID is different from source,
            // or shop group ID is different from source.
            if ($boolArgAllowCreate
                && ($objTNTAccountModelStrict === null
                    || ($intShopID !== $objTNTAccountModelStrict->id_shop
                        || $intShopGroupID !== $objTNTAccountModelStrict->id_shop_group
                    )
                )
            ) {
                // Search account in parent context.
                $objTNTAccountModelInherit = TNTOfficielAccount::searchContextShopID(
                    $intShopID,
                    $intShopGroupID,
                    false
                );

                return TNTOfficielAccount::createContextShopID($intShopID, $intShopGroupID, $objTNTAccountModelInherit);
            }
        } catch (Exception $objException) {
            TNTOfficiel_Logger::logException($objException);
            $objTNTAccountModelStrict = null;
        }

        return $objTNTAccountModelStrict;
    }

    /**
     * Search for a list of non deleted account object model, via shop or shop group ID.
     *
     * @param int  $intArgShopID
     * @param int  $intArgShopGroupID
     * @param bool $boolArgStrictMatch true to match exact context.
     * @param bool $boolArgActive      true to search only active account.
     *
     * @return TNTOfficielAccount|null.
     */
    public static function searchContextShopID(
        $intArgShopID = null,
        $intArgShopGroupID = null,
        $boolArgStrictMatch = true,
        $boolArgActive = true
    ) {
        TNTOfficiel_Logstack::log();

        $intShopID = (int)$intArgShopID;
        $intShopGroupID = (int)$intArgShopGroupID;

        TNTOfficielAccount::correctShopAndGroupID($intShopID, $intShopGroupID);

        $arrObjTNTAccountModelFound = array();

        // If shop ID defined.
        if ($intShopID > 0) {
            // Search row for shop ID.
            $objDbQuery = new DbQuery();
            $objDbQuery->select('*');
            $objDbQuery->from(TNTOfficielAccount::$definition['table']);
            $objDbQuery->where('id_shop = ' . $intShopID);
            $objDbQuery->where('deleted = 0');
            if ($boolArgActive === true) {
                $objDbQuery->where('active = 1');
            }
            $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
            // If row found.
            if (is_array($arrDBResult) && count($arrDBResult) > 0) {
                foreach ($arrDBResult as $arrValue) {
                    if ($intShopID === (int)$arrValue['id_shop']) {
                        $intTNTAccountID = (int)$arrValue['id_tntofficiel_account'];
                        $objTNTAccountModel = TNTOfficielAccount::loadAccountID($intTNTAccountID);
                        // If TNT carrier object not available.
                        if (Validate::isLoadedObject($objTNTAccountModel)
                            && (int)$objTNTAccountModel->id === $intTNTAccountID
                        ) {
                            $arrObjTNTAccountModelFound[] = $objTNTAccountModel;
                        }
                    }
                }
            }
        }

        // If shop group ID defined, and shop ID undefined (or non-strict match).
        if ($intShopGroupID > 0 && (!$boolArgStrictMatch || $intShopID === 0)) {
            // Search row for shop group ID.
            $objDbQuery = new DbQuery();
            $objDbQuery->select('*');
            $objDbQuery->from(TNTOfficielAccount::$definition['table']);
            $objDbQuery->where('id_shop_group = ' . $intShopGroupID);
            $objDbQuery->where('id_shop = 0');
            $objDbQuery->where('deleted = 0');
            if ($boolArgActive === true) {
                $objDbQuery->where('active = 1');
            }
            $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
            // If row found and match accound ID.
            if (is_array($arrDBResult) && count($arrDBResult) > 0) {
                foreach ($arrDBResult as $arrValue) {
                    if ($intShopGroupID === (int)$arrValue['id_shop_group']) {
                        $intTNTAccountID = (int)$arrValue['id_tntofficiel_account'];
                        $objTNTAccountModel = TNTOfficielAccount::loadAccountID($intTNTAccountID);
                        // If TNT carrier object not available.
                        if (Validate::isLoadedObject($objTNTAccountModel)
                            && (int)$objTNTAccountModel->id === $intTNTAccountID
                        ) {
                            $arrObjTNTAccountModelFound[] = $objTNTAccountModel;
                        }
                    }
                }
            }
        }

        // If shop ID and shop group ID undefined (or non-strict match).
        if (($intShopID === 0 && $intShopGroupID === 0) || !$boolArgStrictMatch) {
            // Search row for global.
            $objDbQuery = new DbQuery();
            $objDbQuery->select('*');
            $objDbQuery->from(TNTOfficielAccount::$definition['table']);
            $objDbQuery->where('id_shop IS NULL OR id_shop = 0');
            $objDbQuery->where('id_shop_group IS NULL OR id_shop_group = 0');
            $objDbQuery->where('deleted = 0');
            if ($boolArgActive === true) {
                $objDbQuery->where('active = 1');
            }
            $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
            // If row found and match account ID.
            if (is_array($arrDBResult) && count($arrDBResult) > 0) {
                foreach ($arrDBResult as $arrValue) {
                    $intTNTAccountID = (int)$arrValue['id_tntofficiel_account'];
                    $objTNTAccountModel = TNTOfficielAccount::loadAccountID($intTNTAccountID);
                    // If TNT carrier object not available.
                    if (Validate::isLoadedObject($objTNTAccountModel)
                        && (int)$objTNTAccountModel->id === $intTNTAccountID
                    ) {
                        $arrObjTNTAccountModelFound[] = $objTNTAccountModel;
                    }
                }
            }
        }

        // From most to less qualified.
        foreach ($arrObjTNTAccountModelFound as $objTNTAccountModel) {
            // return AccountModel object or null;
            return $objTNTAccountModel;
        }

        return null;
    }

    /**
     * Save for context (account list).
     */
    public function saveContextShop()
    {
        TNTOfficiel_Logstack::log();

        // get this account context shop list.
        $arrObjPSShopList = $this->getPSShopList();

        // If account context is a shop (for multistore).
        if ($this->id_shop > 0) {
            // remove from the shop list.
            unset($arrObjPSShopList[$this->id_shop]);
        }

        foreach ($arrObjPSShopList as $intShopID => $objPSShop) {
            // Get account for a shop ID (or create it from inherit).
            $objTNTShopAccountModel = TNTOfficielAccount::loadContextShop($intShopID);
            // If no account available for this shop ID.
            if ($objTNTShopAccountModel === null) {
                continue;
            }

            $objTNTShopAccountModel->inherit($this);
            $objTNTShopAccountModel->save();
        }

        $this->save();
    }

    /**
     * Load an existing Prestashop Shop object from ID.
     *
     * @param int $intArgShopID
     *
     * @return Shop|null
     */
    public static function getPSShopByID($intArgShopID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSShop = array();

        // Carrier ID must be an integer greater than 0.
        if (empty($intArgShopID) || $intArgShopID != (int)$intArgShopID || !((int)$intArgShopID > 0)) {
            return null;
        }

        $intShopID = (int)$intArgShopID;

        // If already loaded.
        if (array_key_exists($intShopID, $arrStaticPSShop)) {
            $objPSShopMem = $arrStaticPSShop[$intShopID];
            // Check.
            if (Validate::isLoadedObject($objPSShopMem)
                && (int)$objPSShopMem->id === $intShopID
            ) {
                return $objPSShopMem;
            }
        }

        // Load Shop.
        $objPSShop = new Shop($intShopID);

        // If carrier object not available.
        if (!Validate::isLoadedObject($objPSShop)
            || (int)$objPSShop->id !== $intShopID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSShop[$intShopID] = $objPSShop;

        return $objPSShop;
    }

    /**
     * Load an existing Prestashop ShopGroup object from ID.
     *
     * @param int $intArgShopGroupID
     *
     * @return ShopGroup|null
     */
    public static function getPSShopGroupByID($intArgShopGroupID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSShopGroup = array();

        // Carrier ID must be an integer greater than 0.
        if (empty($intArgShopGroupID) || $intArgShopGroupID != (int)$intArgShopGroupID || !((int)$intArgShopGroupID > 0)) {
            return null;
        }

        $intShopGroupID = (int)$intArgShopGroupID;

        // If already loaded.
        if (array_key_exists($intShopGroupID, $arrStaticPSShopGroup)) {
            $objPSShopGroupMem = $arrStaticPSShopGroup[$intShopGroupID];
            // Check.
            if (Validate::isLoadedObject($objPSShopGroupMem)
                && (int)$objPSShopGroupMem->id === $intShopGroupID
            ) {
                return $objPSShopGroupMem;
            }
        }

        // Load ShopGroup.
        $objPSShopGroup = new ShopGroup($intShopGroupID);

        // If carrier object not available.
        if (!Validate::isLoadedObject($objPSShopGroup)
            || (int)$objPSShopGroup->id !== $intShopGroupID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSShopGroup[$intShopGroupID] = $objPSShopGroup;

        return $objPSShopGroup;
    }

    /**
     * Get account context object shop list.
     *
     * @return array
     */
    public function getPSShopList()
    {
        TNTOfficiel_Logstack::log();

        $intShopID = (int)$this->id_shop;
        $intShopGroupID = (int)$this->id_shop_group;

        $arrIDList = array();

        if ($intShopID > 0) {
            // Get shop ID.
            $arrIDList = array($intShopID);
        } elseif ($intShopGroupID > 0) {
            // Get shop ID list from a group ID.
            $arrIDList = Shop::getShops(true, $intShopGroupID, true);
        } else {
            // Get all shop ID List.
            $arrIDList = Shop::getShops(true, null, true);
        }

        $arrObjPSShopList = array();
        foreach ($arrIDList as $intShopIDCurrent) {
            $intShopIDCurrent = (int)$intShopIDCurrent;
            $objPSShopCurrent = TNTOfficielAccount::getPSShopByID($intShopIDCurrent);
            // If Shop object not available, skip.
            if ($objPSShopCurrent === null) {
                continue;
            }
            $arrObjPSShopList[$intShopIDCurrent] = $objPSShopCurrent;
        }

        return $arrObjPSShopList;
    }

    /**
     * Restoring account shop context.
     */
    private function restorePSShopContext()
    {
        TNTOfficiel_Logstack::log();

        if ($this->id_shop > 0) {
            Shop::setContext(Shop::CONTEXT_SHOP, $this->id_shop);
        } elseif ($this->id_shop_group > 0) {
            Shop::setContext(Shop::CONTEXT_GROUP, $this->id_shop_group);
        } else {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
    }

    /**
     * Store account number.
     *
     * @param string $strArgAccountNumber
     *
     * @return bool
     */
    public function setAccountNumber($strArgAccountNumber)
    {
        TNTOfficiel_Logstack::log();

        $this->account_number = Tools::substr($strArgAccountNumber, 0, 8);

        $this->initWebService();

        return true;
    }

    /**
     * Store account login
     *
     * @param string $strArgAccountLogin
     *
     * @return bool
     */
    public function setAccountLogin($strArgAccountLogin)
    {
        TNTOfficiel_Logstack::log();

        $this->account_login = Tools::substr($strArgAccountLogin, 0, 128);

        $this->initWebService();

        return true;
    }

    /**
     * Store clear account password to SHA1.
     *
     * @param string $strArgAccountPassword
     *
     * @return bool
     */
    public function setAccountPassword($strArgAccountPassword)
    {
        TNTOfficiel_Logstack::log();

        $this->account_password = sha1($strArgAccountPassword);

        $this->initWebService();

        return true;
    }

    /**
     * Get account password.
     *
     * @return string
     */
    public function getAccountPassword()
    {
        TNTOfficiel_Logstack::log();

        return $this->account_password;
    }

    /**
     * Get DateTime of the last credentials validation.
     *
     * @return DateTime|null null if invalid credentials.
     */
    public function getAuthValidatedDateTime()
    {
        TNTOfficiel_Logstack::log();

        $intCredentialCurrentState = $this->account_validated;

        if (!($intCredentialCurrentState > 0)) {
            return null;
        }

        $objValidatedDateTime = TNTOfficiel_Tools::getDateTime($intCredentialCurrentState);

        return $objValidatedDateTime;
    }

    /**
     * Call the WS to check the credentials.
     * Save invalidation or validation date.
     *
     * @param int $intArgRefreshDelay
     *
     * @return bool true if valid or always valid, false if invalid, null if error.
     */
    public function updateAuthValidation($intArgRefreshDelay = 0)
    {
        TNTOfficiel_Logstack::log();

        $intRefreshDelay = (int)$intArgRefreshDelay;
        if (!($intRefreshDelay >= 0)) {
            $intRefreshDelay = 0;
        }

        $objValidatedDateTime = $this->getAuthValidatedDateTime();

        // If validated timestamp exist and not expired.
        if (TNTOfficiel_Tools::isExpired($objValidatedDateTime, $intRefreshDelay) === false) {
            return true;
        }

        // Get WS Response.
        $boolIsAuth = $this->isCorrectAuthentication();

        // Communication Error.
        if ($boolIsAuth === null) {
            return null;
        } elseif ($boolIsAuth !== true) {
            // Disable the module if authentication fail.
            $this->account_validated = 0;
            $this->save();

            return false;
        }

        $objDateTimeNow = new DateTime('now');
        $intTSNow = (int)$objDateTimeNow->format('U');

        $this->account_validated = $intTSNow;
        $this->save();

        return true;
    }

    /**
     * Set sender company name.
     *
     * @param string $strArgSenderCompany
     *
     * @return bool
     */
    public function setSenderCompany($strArgSenderCompany)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_company = Tools::substr($strArgSenderCompany, 0, 32);

        return true;
    }

    /**
     * Store sender address 1.
     *
     * @param string $strArgSenderAddress1
     *
     * @return bool
     */
    public function setSenderAddress1($strArgSenderAddress1)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_address1 = Tools::substr($strArgSenderAddress1, 0, 32);

        return true;
    }

    /**
     * Set sender address 2.
     *
     * @param string $strArgSenderAddress2
     *
     * @return bool
     */
    public function setSenderAddress2($strArgSenderAddress2)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_address2 = Tools::substr($strArgSenderAddress2, 0, 32);

        return true;
    }

    /**
     * Set sender zip code.
     *
     * @param string $strArgSenderZipCode
     *
     * @return bool
     */
    public function setSenderZipCode($strArgSenderZipCode)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_zipcode = Tools::substr($strArgSenderZipCode, 0, 10);

        return true;
    }

    /**
     * Set sender city.
     *
     * @param string $strArgSenderCity
     *
     * @return bool
     */
    public function setSenderCity($strArgSenderCity)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_city = Tools::substr($strArgSenderCity, 0, 32);

        return true;
    }

    /**
     * Set sender first name.
     *
     * @param string $strArgSenderFirstName
     *
     * @return bool
     */
    public function setSenderFirstName($strArgSenderFirstName)
    {
        TNTOfficiel_Logstack::log();

        // TODO : return TNTOfficiel_Tools::setField($this, 'sender_firstname', $strArgSenderFirstName);

        $this->sender_firstname = Tools::substr($strArgSenderFirstName, 0, 32);

        return true;
    }

    /**
     * Set sender last name.
     *
     * @param string $strArgSenderLastName
     *
     * @return bool
     */
    public function setSenderLastName($strArgSenderLastName)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_lastname = Tools::substr($strArgSenderLastName, 0, 32);

        return true;
    }

    /**
     * Store sender email.
     *
     * @param string $strArgSenderEMail
     *
     * @return bool
     */
    public function setSenderEMail($strArgSenderEMail)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_email = Tools::substr($strArgSenderEMail, 0, 80);

        return true;
    }

    /**
     * Set sender phone.
     *
     * @param string $strArgSenderPhone
     *
     * @return bool
     */
    public function setSenderPhone($strArgSenderPhone)
    {
        TNTOfficiel_Logstack::log();

        $this->sender_phone = Tools::substr($strArgSenderPhone, 0, 15);

        return true;
    }

    /**
     * Set sender phone.
     *
     * @param string $strArgPickupLabelType
     *
     * @return bool
     */
    public function setPickupLabelType($strArgPickupLabelType)
    {
        TNTOfficiel_Logstack::log();

        if (in_array($strArgPickupLabelType, TNTOfficielAccount::$arrPickupLabelTypes, true)) {
            $this->pickup_label_type = Tools::substr($strArgPickupLabelType, 0, 32);
        }

        return true;
    }

    /**
     * Set pickup type.
     *
     * @param string $strArgSenderPphone
     *
     * @return bool
     */
    public function setPickupType($strArgPickupType)
    {
        TNTOfficiel_Logstack::log();

        $strPickupType = Tools::strtoupper($strArgPickupType);

        if ($strPickupType !== 'OCCASIONAL') {
            $strPickupType = 'REGULAR';
        }

        $this->pickup_type = $strPickupType;

        return true;
    }

    /**
     * Is pickup type occasional.
     *
     * @return bool
     */
    public function isPickupTypeOccasional()
    {
        TNTOfficiel_Logstack::log();

        if ($this->pickup_type !== 'OCCASIONAL') {
            return false;
        }

        return true;
    }

    /**
     * Get pickup driver time (REGULAR).
     *
     * @return DateTime
     */
    public function getPickupDriverTime()
    {
        TNTOfficiel_Logstack::log();

        $strArgPDT = $this->pickup_driver_time;
        $objDateTimePDTCheck = TNTOfficiel_Tools::getDateTime($strArgPDT);
        $objDateTimePDTDefault = TNTOfficiel_Tools::getDateTime('17:00:00');

        if ($objDateTimePDTCheck === null) {
            return $objDateTimePDTDefault;
        }

        return $objDateTimePDTCheck;
    }

    /**
     * Get pickup closing time (OCCASIONAL).
     *
     * @return DateTime
     */
    public function getPickupClosingTime()
    {
        TNTOfficiel_Logstack::log();

        $strArgPCT = $this->pickup_closing_time;
        $objDateTimePCTCheck = TNTOfficiel_Tools::getDateTime($strArgPCT);
        $objDateTimePCTMin = TNTOfficiel_Tools::getDateTime('15:00:00');
        $objDateTimePCTDefault = TNTOfficiel_Tools::getDateTime('17:00:00');

        if ($objDateTimePCTCheck === null) {
            return $objDateTimePCTDefault;
        }

        if ($objDateTimePCTCheck < $objDateTimePCTMin) {
            return $objDateTimePCTMin;
        }

        return $objDateTimePCTCheck;
    }

    /**
     * Set orderstate shipment save ID.
     *
     * @param int $intArgOSShipmentSaveID OrderState ID.
     *
     * @return bool
     */
    public function setOSShipmentSaveID($intArgOSShipmentSaveID)
    {
        TNTOfficiel_Logstack::log();

        $intOrderStateShipmentAfterID = (int)$this->os_shipment_after_id;
        $intOrderStateShipmentSaveID = (int)$intArgOSShipmentSaveID;

        // OrderState ShipmentSave must be different from OrderState ShipmentAfter.
        if ($intOrderStateShipmentSaveID === $intOrderStateShipmentAfterID) {
            return false;
        }

        $this->os_shipment_save_id = $intOrderStateShipmentSaveID;

        return true;
    }

    /**
     * Set orderstate shipment after ID.
     *
     * @param int $intArgOSShipmentAfterID OrderState ID.
     *
     * @return bool
     */
    public function setOSShipmentAfterID($intArgOSShipmentAfterID)
    {
        TNTOfficiel_Logstack::log();

        $intOrderStateShipmentSaveID = (int)$this->os_shipment_save_id;
        $intOrderStateShipmentAfterID = (int)$intArgOSShipmentAfterID;

        // OrderState ShipmentAfter must be different from OrderState ShipmentSave.
        if ($intOrderStateShipmentAfterID === $intOrderStateShipmentSaveID) {
            return false;
        }

        $this->os_shipment_after_id = $intOrderStateShipmentAfterID;

        return true;
    }

    /**
     * Set orderstate parcel take in charge ID.
     *
     * @param int $intArgOSParcelTakenInChargeID OrderState ID.
     *
     * @return bool
     */
    public function setOSParcelTakenInChargeID($intArgOSParcelTakenInChargeID)
    {
        TNTOfficiel_Logstack::log();

        $this->os_parcel_takenincharge_id = (int)$intArgOSParcelTakenInChargeID;

        return true;
    }

    /**
     * Set orderstate parcel all delivered ID.
     *
     * @param int $intArgOSParcelAllDeliveredID OrderState ID.
     *
     * @return bool
     */
    public function setOSParcelAllDeliveredID($intArgOSParcelAllDeliveredID)
    {
        TNTOfficiel_Logstack::log();

        $this->os_parcel_alldelivered_id = (int)$intArgOSParcelAllDeliveredID;

        return true;
    }

    /**
     * Set orderstate parcel all delivered to point ID.
     *
     * @param int $intArgOSParcelAllDeliveredToPointID OrderState ID.
     *
     * @return bool
     */
    public function setOSParcelAllDeliveredToPointID($intArgOSParcelAllDeliveredToPointID)
    {
        TNTOfficiel_Logstack::log();

        $this->os_parcel_alldeliveredtopoint_id = (int)$intArgOSParcelAllDeliveredToPointID;

        return true;
    }

    /**
     * Set parcel check enable.
     *
     * @param int $boolArgOSParcelCheckEnable in seconds.
     *
     * @return bool
     */
    public function setOSParcelCheckEnable($boolArgOSParcelCheckEnable)
    {
        TNTOfficiel_Logstack::log();

        $this->os_parcel_check_enable = (bool)$boolArgOSParcelCheckEnable;

        return true;
    }

    /**
     * Set parcel check rate.
     *
     * @param int $intArgOSParcelCheckRate in seconds.
     *
     * @return bool
     */
    public function setOSParcelCheckRate($intArgOSParcelCheckRate = 0)
    {
        TNTOfficiel_Logstack::log();

        // Default is 6 hours.
        if (!($intArgOSParcelCheckRate > 0)) {
            $intArgOSParcelCheckRate = 6 * 60 * 60;
        }

        // Minimum is 3 hours.
        $this->os_parcel_check_rate = max((int)$intArgOSParcelCheckRate, 3 * 60 * 60);

        return true;
    }

    /**
     * Get OrderState who save shipment.
     *
     * @param null $intArgLangID
     *
     * @return null|OrderState
     */
    public function getOSShipmentSave($intArgLangID = null)
    {
        $intLangID = (int)$intArgLangID;
        if (!($intArgLangID > 0)) {
            $intLangID = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        // Check ID.
        $intOrderStateShipmentSaveID = (int)$this->os_shipment_save_id;
        if (!($intOrderStateShipmentSaveID > 0)) {
            return null;
        }

        // Check Object.
        $objOrderStateShipmentSave = new OrderState($intOrderStateShipmentSaveID, $intLangID);
        if (!Validate::isLoadedObject($objOrderStateShipmentSave)
            || (int)$objOrderStateShipmentSave->id !== $intOrderStateShipmentSaveID
        ) {
            return null;
        }

        return $objOrderStateShipmentSave;
    }

    /**
     * Get OrderState after shipment.
     *
     * @param null $intArgLangID
     *
     * @return null|OrderState
     */
    public function getOSShipmentAfter($intArgLangID = null)
    {
        $intLangID = (int)$intArgLangID;
        if (!($intArgLangID > 0)) {
            $intLangID = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $intOrderStateShipmentSaveID = (int)$this->os_shipment_save_id;
        $intOrderStateShipmentAfterID = (int)$this->os_shipment_after_id;

        // OrderState ShipmentAfter must be different from OrderState ShipmentSave.
        if ($intOrderStateShipmentAfterID === $intOrderStateShipmentSaveID) {
            return null;
        }

        // Check ID.
        if (!($intOrderStateShipmentAfterID > 0)) {
            return null;
        }

        // Check Object.
        $objOrderStateShipmentAfter = new OrderState($intOrderStateShipmentAfterID, $intLangID);
        if (!Validate::isLoadedObject($objOrderStateShipmentAfter)
            || (int)$objOrderStateShipmentAfter->id !== $intOrderStateShipmentAfterID
        ) {
            return null;
        }

        return $objOrderStateShipmentAfter;
    }

    /**
     * Get OrderState for parcel taken in charge.
     *
     * @param null $intArgLangID
     *
     * @return null|OrderState
     */
    public function getOSParcelTakenInCharge($intArgLangID = null)
    {
        $intLangID = (int)$intArgLangID;
        if (!($intArgLangID > 0)) {
            $intLangID = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        // Check ID.
        $intOrderStateParcelTakenInChargeID = (int)$this->os_parcel_takenincharge_id;
        if (!($intOrderStateParcelTakenInChargeID > 0)) {
            return null;
        }

        // Check Object.
        $objOrderStateParcelTakenInCharge = new OrderState($intOrderStateParcelTakenInChargeID, $intLangID);
        if (!Validate::isLoadedObject($objOrderStateParcelTakenInCharge)
            || (int)$objOrderStateParcelTakenInCharge->id !== $intOrderStateParcelTakenInChargeID
        ) {
            return null;
        }

        return $objOrderStateParcelTakenInCharge;
    }

    /**
     * Get OrderState for all parcels delivered.
     *
     * @param null $intArgLangID
     *
     * @return null|OrderState
     */
    public function getOSParcelAllDelivered($intArgLangID = null)
    {
        $intLangID = (int)$intArgLangID;
        if (!($intArgLangID > 0)) {
            $intLangID = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        // Check ID.
        $intOrderStateParcelAllDeliveredID = (int)$this->os_parcel_alldelivered_id;
        if (!($intOrderStateParcelAllDeliveredID > 0)) {
            return null;
        }

        // Check Object.
        $objOrderStateParcelAllDelivered = new OrderState($intOrderStateParcelAllDeliveredID, $intLangID);
        if (!Validate::isLoadedObject($objOrderStateParcelAllDelivered)
            || (int)$objOrderStateParcelAllDelivered->id !== $intOrderStateParcelAllDeliveredID
        ) {
            return null;
        }

        return $objOrderStateParcelAllDelivered;
    }

    /**
     * Get OrderState for all parcels delivered at point.
     *
     * @param null $intArgLangID
     *
     * @return null|OrderState
     */
    public function getOSParcelAllDeliveredAtPoint($intArgLangID = null)
    {
        $intLangID = (int)$intArgLangID;
        if (!($intArgLangID > 0)) {
            $intLangID = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        // Check ID.
        $intOrderStateParcelAllDeliveredAtPointID = (int)$this->os_parcel_alldeliveredtopoint_id;
        if (!($intOrderStateParcelAllDeliveredAtPointID > 0)) {
            return null;
        }

        // Check Object.
        $objOrderStateParcelAllDeliveredAtPoint = new OrderState($intOrderStateParcelAllDeliveredAtPointID, $intLangID);
        if (!Validate::isLoadedObject($objOrderStateParcelAllDeliveredAtPoint)
            || (int)$objOrderStateParcelAllDeliveredAtPoint->id !== $intOrderStateParcelAllDeliveredAtPointID
        ) {
            return null;
        }

        return $objOrderStateParcelAllDeliveredAtPoint;
    }

    /**
     * Create a new Prestashop carrier, flagged with module name.
     * Save TNT associated account, type and code to model.
     *
     * @param string $strArgAccountType
     * @param string $strArgCarrierType
     * @param string $strArgCarrierCode1
     * @param string $strArgCarrierCode2
     *
     * @return array|bool Array of created carrier ID. false on error.
     */
    public function createCarrier($strArgAccountType, $strArgCarrierType, $strArgCarrierCode1, $strArgCarrierCode2)
    {
        TNTOfficiel_Logstack::log();

        if (!TNTOfficielCarrier::isWhiteListed(
            $strArgAccountType,
            $strArgCarrierType,
            $strArgCarrierCode1,
            $strArgCarrierCode2
        )) {
            return false;
        }

        $arrObjPSShopList = $this->getPSShopList();

        $arrLangList = Language::getLanguages(true);

        $arrCarrierCreated = array();

        foreach ($arrObjPSShopList as $intShopID => $objShop) {
            // Is carrier already created for this shop ?
            $boolExist = TNTOfficiel::getOption('CARRIER_CREATE_DUPLICATE') !== true
                && TNTOfficielCarrier::isExist(
                $intShopID,
                $strArgAccountType,
                $strArgCarrierType,
                $strArgCarrierCode1,
                $strArgCarrierCode2
            );

            if ($boolExist) {
                // Skip already created carrier.
                continue;
            }

            $boolResult = true;

            // Creating a new Prestashop Carrier.
            $objPSCarrierNew = new Carrier();
            $objPSCarrierNew->active = true;
            $objPSCarrierNew->deleted = false;
            // Carrier used for module.
            $objPSCarrierNew->is_module = true;
            $objPSCarrierNew->external_module_name = TNTOfficiel::MODULE_NAME;
            // Carrier name.
            $objPSCarrierNew->name = TNTOfficiel::MODULE_TITLE;
            // Carrier delay description per language ISO code [1-128] characters.
            $arrDelayLang = array();
            if (is_array($arrLangList)) {
                foreach ($arrLangList as $arrLang) {
                    $arrDelayLang[(int)$arrLang['id_lang']] = '-';
                }
            }
            $objPSCarrierNew->delay = $arrDelayLang;
            // Applying tax rules group (0: Disable).
            $objPSCarrierNew->id_tax_rules_group = 0;
            // Use default shipping method (weight or price).
            $objPSCarrierNew->shipping_method = Carrier::SHIPPING_METHOD_DEFAULT;

            // Disable adding handling charges from config PS_SHIPPING_HANDLING.
            $objPSCarrierNew->shipping_handling = false;
            // Enable use of Cart getPackageShippingCost, getOrderShippingCost or getOrderShippingCostExternal
            $objPSCarrierNew->shipping_external = true;
            // Enable calculations for the ranges.
            $objPSCarrierNew->need_range = true;
            $objPSCarrierNew->range_behavior = 0;

            // If unable to create new Prestashop Carrier.
            // NOTE : Add() set the carrier id_reference. Do not use save().
            if (!$objPSCarrierNew->add()) {
                return false;
            }

            // Get new ID.
            $intCarrierIDNew = (int)$objPSCarrierNew->id;

            // Reload
            $objPSCarrierCreated = TNTOfficielCarrier::getPSCarrierByID($intCarrierIDNew);
            // If Carrier object not available.
            if ($objPSCarrierCreated === null) {
                return false;
            }

            // Create a new TNT carrier model using the created Prestashop carrier ID.
            $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierIDNew, true);
            // If fail.
            if ($objTNTCarrierModel === null) {
                return false;
            }

            // Get account for a shop ID (or create it from inherit).
            $objTNTShopAccountModel = TNTOfficielAccount::loadContextShop($intShopID);
            // If no account available for this shop ID.
            if ($objTNTShopAccountModel === null) {
                return false;
            }

            $objTNTCarrierModel->id_account = $objTNTShopAccountModel->id;
            $objTNTCarrierModel->id_shop = $intShopID;
            $objTNTCarrierModel->account_type = $strArgAccountType;
            $objTNTCarrierModel->carrier_type = $strArgCarrierType;
            $objTNTCarrierModel->carrier_code1 = $strArgCarrierCode1;
            $objTNTCarrierModel->carrier_code2 = $strArgCarrierCode2;

            $boolResult = $objTNTCarrierModel->save() && $boolResult;

            $objCarrierInfos = $objTNTCarrierModel->getCarrierInfos();
            if ($objCarrierInfos === null) {
                return false;
            }

            // Carrier name.
            $objPSCarrierCreated->name = $objCarrierInfos->label;
            $objPSCarrierCreated->name = Tools::substr($objPSCarrierCreated->name, 0, 64);

            // Carrier delay description per language ISO code [1-128] characters.
            $arrDelayLang = array();
            if (is_array($arrLangList)) {
                foreach ($arrLangList as $arrLang) {
                    $arrDelayLang[(int)$arrLang['id_lang']] = Tools::substr($objCarrierInfos->delay, 0, 128);
                }
            }
            $objPSCarrierCreated->delay = $arrDelayLang;
            // Shortest shipping delay.
            $objPSCarrierCreated->grade = 9;

            // All shop context required for lang.
            Shop::setContext(Shop::CONTEXT_ALL);
            $boolResult = $objPSCarrierCreated->save() && $boolResult;
            // Restore shop context according to account.
            $this->restorePSShopContext();

            $intCarrierTaxRulesGroupID = 0;
            // Find Taxe Rule Group : FR 20%, Enabled, Non-Deleted, named like 'FR\ Taux\ standard'.
            $intCountryFRID = (int)Country::getByIso('FR');
            // If country FR exist.
            if ($intCountryFRID > 0) {
                $arrTaxRulesGroup = TaxRulesGroup::getAssociatedTaxRatesByIdCountry($intCountryFRID);
                foreach ($arrTaxRulesGroup as $intTaxRulesGroupID => $strTaxAmount) {
                    if ((int)$strTaxAmount === 20) {
                        $objTaxRulesGroup = new TaxRulesGroup((int)$intTaxRulesGroupID);
                        if ($objTaxRulesGroup->active
                            && (!property_exists($objTaxRulesGroup, 'deleted') || !$objTaxRulesGroup->deleted)
                            && preg_match('/^FR\ Taux\ standard/ui', $objTaxRulesGroup->name) === 1
                        ) {
                            $intCarrierTaxRulesGroupID = (int)$intTaxRulesGroupID;
                            break;
                        }
                    }
                }
            }
            // Applying tax rules group (0: Disable).
            $objPSCarrierCreated->setTaxRulesGroup($intCarrierTaxRulesGroupID);

            // Add groups.
            $arrGroupID = array();
            // Use current language for name translation.
            $intLangID = (int)Context::getContext()->language->id;
            $arrGroupList = Group::getGroups($intLangID, $intShopID);
            foreach ($arrGroupList as $arrGroup) {
                $arrGroupID[] = (int)$arrGroup['id_group'];
                $objPSCarrierCreated->setGroups($arrGroupID);
            }

            // Add Price Range.
            $objRangePrice = new RangePrice();
            $objRangePrice->id_carrier = $intCarrierIDNew;
            $objRangePrice->delimiter1 = '0';
            $objRangePrice->delimiter2 = '1000000';
            $objRangePrice->add();
            // Add Weight Range.
            $objRangeWeight = new RangeWeight();
            $objRangeWeight->id_carrier = $intCarrierIDNew;
            $objRangeWeight->delimiter1 = '0';
            $objRangeWeight->delimiter2 = '1000000';
            $objRangeWeight->add();

            // If country FR exist.
            if ($intCountryFRID > 0) {
                // Use current language for name translation.
                $intLangID = (int)Context::getContext()->language->id;
                $objCountryFR = new Country($intCountryFRID, $intLangID);
                $intCountryFRZoneID = (int)$objCountryFR->id_zone;
                if ($intCountryFRZoneID > 0) {
                    $objPSCarrierCreated->addZone($intCountryFRZoneID);
                }
            }

            // Add carrier logo.
            $boolResult = copy(
                    TNTOfficiel::getDirModule('image') . 'logo/96x60.png',
                    _PS_SHIP_IMG_DIR_ . $objTNTCarrierModel->id_carrier . '.jpg'
                ) && $boolResult;

            $arrCarrierCreated[] = $intCarrierIDNew;
        }

        return $arrCarrierCreated;
    }

    /**
     * Get default zone department list.
     *
     * @return array
     */
    public function getZoneDefaultDepartments()
    {
        TNTOfficiel_Logstack::log();

        $arrZoneDefaultDepartments = array_diff(
            TNTOfficielAccount::$arrDepartments,
            array_merge(
                $this->getZone1Departments(),
                $this->getZone2Departments()
            )
        );

        return $arrZoneDefaultDepartments;
    }

    /**
     * Get all department list.
     *
     * @return array
     */
    public function getZoneAllDepartments()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficielAccount::$arrDepartments;
    }

    /**
     * set zone 1 & 2 department list.
     *
     * @param array $arrZone1Departments
     * @param array $arrZone2Departments
     *
     * @return bool true on success, false if Zone 1 and 2 have common departments.
     */
    public function setZoneDepartments($arrZone1Departments, $arrZone2Departments)
    {
        TNTOfficiel_Logstack::log();

        if (!is_array($arrZone1Departments)) {
            $arrZone1Departments = array();
        }
        if (!is_array($arrZone2Departments)) {
            $arrZone2Departments = array();
        }

        $arrZone1Departments = array_intersect(TNTOfficielAccount::$arrDepartments, $arrZone1Departments);
        $arrZone2Departments = array_intersect(TNTOfficielAccount::$arrDepartments, $arrZone2Departments);

        if (count(array_intersect($arrZone1Departments, $arrZone2Departments)) > 0) {
            return false;
        }

        $this->zone1_departments = TNTOfficiel_Tools::serialize($arrZone1Departments);
        $this->zone2_departments = TNTOfficiel_Tools::serialize($arrZone2Departments);

        return true;
    }

    /**
     * @return array
     */
    public function getZone1Departments()
    {
        TNTOfficiel_Logstack::log();

        $arrZone1Departments = TNTOfficiel_Tools::unserialize($this->zone1_departments);
        if (!is_array($arrZone1Departments)) {
            $arrZone1Departments = array();
        }

        return $arrZone1Departments;
    }

    /**
     * @return array
     */
    public function getZone2Departments()
    {
        TNTOfficiel_Logstack::log();

        $arrZone2Departments = TNTOfficiel_Tools::unserialize($this->zone2_departments);
        if (!is_array($arrZone2Departments)) {
            $arrZone2Departments = array();
        }

        return $arrZone2Departments;
    }

    /**
     * Get Zone ID for ZipCode.
     *
     * @param string|null $strArgZipCode
     *
     * @return int
     */
    public function getZipCodeZone($strArgZipCode = null)
    {
        TNTOfficiel_Logstack::log();

        if ($strArgZipCode === null) {
            return 0;
        }

        $strDepartment = Tools::substr($strArgZipCode, 0, 2);

        $arrZone1Departments = $this->getZone1Departments();
        $arrZone2Departments = $this->getZone2Departments();

        if (in_array($strDepartment, $arrZone2Departments)) {
            return 2;
        }
        if (in_array($strDepartment, $arrZone1Departments)) {
            return 1;
        }

        return 0;
    }

    /**
     * Init SoapClient for account.
     *
     * @return null
     */
    private function initWebService()
    {
        TNTOfficiel_Logstack::log();

        $this->objWebServiceTNT = new TNTOfficiel_SoapClient(
            $this->account_number,
            $this->account_login,
            $this->getAccountPassword()
        );

        return null;
    }

    /**
     * Check authentication.
     *
     * @return bool|null
     */
    public function isCorrectAuthentication()
    {
        TNTOfficiel_Logstack::log();

        return $this->objWebServiceTNT->isCorrectAuthentication();
    }

    /**
     * @param      $strArgCountryISO
     * @param      $strArgZipCode
     * @param null $strArgCity
     *
     * @return array
     */
    public function citiesGuide($strArgCountryISO, $strArgZipCode, $strArgCity = null)
    {
        TNTOfficiel_Logstack::log();

        return $this->objWebServiceTNT->citiesGuide($strArgCountryISO, $strArgZipCode, $strArgCity);
    }

    /**
     * @param      $strArgZipCode
     * @param      $strArgCity
     * @param null $strArgEDD
     *
     * @return array|null
     */
    public function dropOffPoints($strArgZipCode, $strArgCity, $strArgEDD = null)
    {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        return $this->objWebServiceTNT->dropOffPoints($strArgZipCode, $strArgCity, $strArgEDD);
    }

    /**
     * @param      $strArgZipCode
     * @param      $strArgCity
     * @param null $strArgEDD
     *
     * @return array|null
     */
    public function tntDepots($strArgZipCode, $strArgCity, $strArgEDD = null)
    {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        return $this->objWebServiceTNT->tntDepots($strArgZipCode, $strArgCity, $strArgEDD);
    }

    /**
     * Get list of all available services.
     *
     * @return array
     */
    public function availabilities()
    {
        TNTOfficiel_Logstack::log();

        $arrFeasibilityAllCarrierType = array(
            'boolIsRequestComError' => false,
            'arrTNTServiceList' => array(),
        );

        if ($this->getAuthValidatedDateTime() === null) {
            return $arrFeasibilityAllCarrierType;
        }

        // For each carrier type.
        foreach (TNTOfficielCarrier::$arrCarrierTypeList as $strCarrierType) {
            $arrResultFeasibility = $this->objWebServiceTNT->feasibility($strCarrierType);
            // Adding state to list.
            $arrFeasibilityAllCarrierType['boolIsRequestComError']
                = $arrFeasibilityAllCarrierType['boolIsRequestComError']
                || $arrResultFeasibility['boolIsRequestComError'];
            // Adding service to list.
            $arrFeasibilityAllCarrierType['arrTNTServiceList'] = array_merge(
                $arrFeasibilityAllCarrierType['arrTNTServiceList'],
                $arrResultFeasibility['arrTNTServiceList']
            );
        }

        $arrTNTServiceListFiltered = array();

        // Filtering ...
        foreach ($arrFeasibilityAllCarrierType['arrTNTServiceList'] as $arrFeasibilityServiceItem) {
            if (TNTOfficielCarrier::isWhiteListed(
                $arrFeasibilityServiceItem['accountType'],
                $arrFeasibilityServiceItem['carrierType'],
                $arrFeasibilityServiceItem['carrierCode1'],
                $arrFeasibilityServiceItem['carrierCode2']
            )) {
                $strID = implode(
                    ':',
                    array(
                        $arrFeasibilityServiceItem['accountType'],
                        $arrFeasibilityServiceItem['carrierType'],
                        $arrFeasibilityServiceItem['carrierCode1'],
                        $arrFeasibilityServiceItem['carrierCode2'],
                    )
                );
                $arrTNTServiceListFiltered[$strID] = array_intersect_key(
                    $arrFeasibilityServiceItem,
                    array(
                        'accountType' => null,
                        'carrierType' => null,
                        'carrierCode1' => null,
                        'carrierCode2' => null,
                        'carrierLabel' => null,
                    )
                );
            }
        }

        $arrFeasibilityAllCarrierType['arrTNTServiceList'] = $arrTNTServiceListFiltered;

        return $arrFeasibilityAllCarrierType;
    }

    /**
     * @param string                   $strArgReceiverZipCode
     * @param string                   $strArgReceiverCity
     * @param string|int|DateTime|null $mxdArgShippingDate    (optional)
     * @param array                    $arrArgCarrierTypeList (optional)
     *
     * @return array
     */
    public function feasibility(
        $strArgReceiverZipCode,
        $strArgReceiverCity,
        $mxdArgShippingDate = null,
        array $arrArgCarrierTypeList = array()
    ) {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        $arrCarrierTypeList = TNTOfficielCarrier::$arrCarrierTypeList;
        if (count($arrArgCarrierTypeList) > 0) {
            $arrCarrierTypeList = array_intersect(TNTOfficielCarrier::$arrCarrierTypeList, $arrArgCarrierTypeList);
        }

        $arrResult = array(
            'arrTNTServiceList' => array(),
        );

        // For each carrier type.
        foreach ($arrCarrierTypeList as $strCarrierType) {
            $arrResultFeasibility = $this->objWebServiceTNT->feasibility(
                $strCarrierType,
                $this->sender_zipcode,
                $this->sender_city,
                $strArgReceiverZipCode,
                $strArgReceiverCity,
                null,
                $mxdArgShippingDate
            );

            // If communication error.
            if ($arrResultFeasibility['boolIsRequestComError']) {
                // Stop search here.
                break;
            }

            $arrResult['arrTNTServiceList'] = array_merge(
                $arrResult['arrTNTServiceList'],
                $arrResultFeasibility['arrTNTServiceList']
            );
        }

        // Filtering : Saturday delivery is excluded for 'ENTERPRISE' or 'DEPOT' (non-free option).
        $arrTNTServiceList = array();
        foreach ($arrResult['arrTNTServiceList'] as $arrTNTService) {
            if (($arrTNTService['saturdayDelivery'] == 0
                    && in_array($arrTNTService['carrierType'], array('ENTERPRISE', 'DEPOT')))
                || in_array($arrTNTService['carrierType'], array('INDIVIDUAL', 'DROPOFFPOINT'))
            ) {
                $arrTNTServiceList[] = $arrTNTService;
            }
        }

        return $arrTNTServiceList;
    }

    /**
     * feasibility like, but takes current times into account :
     * - the order's preparation delay before shipment.
     * - the first pickup date available, and cutoff time.
     * - 2nd try using the next weekday.
     *
     * @param string $strArgReceiverZipCode
     * @param string $strArgReceiverCity
     * @param array  $arrArgCarrierTypeList (optional)
     *
     * @return array
     */
    public function liveFeasibility(
        $strArgReceiverZipCode,
        $strArgReceiverCity,
        array $arrArgCarrierTypeList = array()
    ) {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        $arrResult = array(
            'arrTNTServiceList' => array(),
        );

        // Date Today.
        $objDateTimeToday = new DateTime('midnight');
        $strDateToday = $objDateTimeToday->format('Y-m-d');

        $arrResultPickup = $this->getPickupDate($strDateToday);
        // Pickup cut-off time ('H:i').
        $strPickupCutOffTime = $arrResultPickup['cutOffTime'];
        // Pickup first available date ('Y-m-d').
        $strPickupDateOrigin = $arrResultPickup['pickupDate'];

        $intPickupDateOrigin = strtotime($strPickupDateOrigin);
        $intShippingDelay = (int)$this->pickup_preparation_days;

        // If cut-off time has passed, subtract a day to the shipping delay.
        if (date('Hi') >= str_replace(':', '', $strPickupCutOffTime)) {
            $intShippingDelay--;
            if ($intShippingDelay < 0) {
                $intShippingDelay = 0;
            }
        }

        $strShippingDateDelayed = date(
            'Y-m-d',
            strtotime(
                sprintf('+%d weekdays', $intShippingDelay),
                $intPickupDateOrigin
            )
        );

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'strDateToday' => $strDateToday,
                'strPickupDateOrigin' => $strPickupDateOrigin,
                'strPickupCutOffTime' => $strPickupCutOffTime,
                'intShippingDelay' => $intShippingDelay,
                'strShippingDateDelayed' => $strShippingDateDelayed,
            )
        );

        // Try next open day (weekdays).
        $strShippingDateDelayedNextWeekDays = date(
            'Y-m-d',
            strtotime(
                sprintf('+%d weekdays', 1),
                strtotime($strShippingDateDelayed)
            )
        );

        $arrShippingDateList = array(
            $strShippingDateDelayed,
            $strShippingDateDelayedNextWeekDays,
        );

        // For each date.
        foreach ($arrShippingDateList as $strShippingDate) {
            $arrResult['arrTNTServiceList'] = $this->feasibility(
                $strArgReceiverZipCode,
                $strArgReceiverCity,
                $strShippingDate,
                $arrArgCarrierTypeList
            );

            // If any result.
            if (count($arrResult['arrTNTServiceList']) > 0) {
                // Stop search here.
                break;
            }
            // else its may be holidays...loop using next date.
        }

        return $arrResult['arrTNTServiceList'];
    }

    /**
     * Get the pickup date and the cut-off time depending on the pickup type.
     *
     * @param string|int|DateTime|null $mxdArgPickupDate (optional)
     *
     * @return array
     */
    public function getPickupDate($mxdArgPickupDate = null)
    {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        // Date Now.
        $objDateTimeNow = new DateTime('now');
        // Date Today.
        $objDateTimeToday = new DateTime('midnight');
        $strDateToday = $objDateTimeToday->format('Y-m-d');

        // Get first weekday from today.
        $strDateFirstWeekDay = TNTOfficiel_Tools::getFirstWeekDay($objDateTimeToday, 'Y-m-d');
        // Get next weekday from today.
        $strDateNextWeekDay = TNTOfficiel_Tools::getNextWeekDay($objDateTimeToday, 'Y-m-d');

        // Check Pickup date requested for apply. Default is today.
        $strPickupDate = TNTOfficiel_Tools::getDateTimeFormat($mxdArgPickupDate, 'Y-m-d', $strDateFirstWeekDay);

        // If pickup type is occasional, else is regular.
        if ($this->isPickupTypeOccasional()) {
            // Get today pickup availability for sender location.
            $arrResultPickup = $this->objWebServiceTNT->getPickupContext(
                $this->sender_zipcode,
                $this->sender_city,
                $strPickupDate
            );
        } else {
            $arrResultPickup = array(
                'boolIsRequestComError' => false,
                'strResponseMsgError' => null,
                // Pickup first available date is the requested date ('Y-m-d').
                'pickupDate' => $strPickupDate,
                // Pickup cut-off time is the driver time from account ('H:i').
                'cutOffTime' => $this->getPickupDriverTime()->format('H:i'),
                'pickupOnMorning' => null,
            );
            // If the requested date is today and the current time is greater than the driver time.
            if ($strPickupDate === $strDateToday
                && $objDateTimeNow->format('Hi') > $this->getPickupDriverTime()->format('Hi')
            ) {
                // Available date is the next weekday.
                $arrResultPickup['pickupDate'] = $strDateNextWeekDay;
            }
        }

        return $arrResultPickup;
    }

    /**
     * @param string     $strArgReceiverType
     * @param string     $strDeliveryPointCode
     * @param string     $strArgReceiverCompany
     * @param string     $strArgReceiverAddress1
     * @param string     $strArgReceiverAddress2
     * @param string     $strArgReceiverZipCode
     * @param string     $strArgReceiverCity
     * @param string     $strArgReceiverLastName
     * @param string     $strArgReceiverFirstName
     * @param string     $strArgReceiverEMail
     * @param string     $strArgReceiverPhone
     * @param string     $strArgReceiverBuilding
     * @param string     $strArgReceiverAccessCode
     * @param string     $strArgReceiverFloor
     * @param string     $strArgCarrierCode
     * @param string     $strArgPickupDate
     * @param array      $arrArgParcelRequest
     * @param bool       $boolArgSendPickupRequest
     * @param float|null $fltArgPaybackAmount (optional)
     *
     * @return array
     */
    public function expeditionCreation(
        $strArgReceiverType,
        $strDeliveryPointCode,
        $strArgReceiverCompany,
        $strArgReceiverAddress1,
        $strArgReceiverAddress2,
        $strArgReceiverZipCode,
        $strArgReceiverCity,
        $strArgReceiverLastName,
        $strArgReceiverFirstName,
        $strArgReceiverEMail,
        $strArgReceiverPhone,
        $strArgReceiverBuilding,
        $strArgReceiverAccessCode,
        $strArgReceiverFloor,
        $strArgReceiverInstructions,
        $strArgCarrierCode,
        $strArgPickupDate,
        $arrArgParcelRequest,
        $boolArgSendPickupRequest,
        $fltArgPaybackAmount = null
    ) {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return array(
                'boolIsRequestComError' => false,
                'strResponseMsgError' => sprintf(
                    TNTOfficiel::getCodeTranslate('errorInvalidCredentialForAccountID'),
                    $this->id
                ),
            );
        }

        if ($fltArgPaybackAmount !== null
            && $fltArgPaybackAmount > 10000.0
        ) {
            return array(
                'boolIsRequestComError' => false,
                'strResponseMsgError' => TNTOfficiel::getCodeTranslate('errorInvalidPaybackAmountStr'),
            );
        }

        $arrResult = $this->objWebServiceTNT->expeditionCreation(
            $this->sender_company,
            $this->sender_address1,
            $this->sender_address2,
            $this->sender_zipcode,
            $this->sender_city,
            $this->sender_lastname,
            $this->sender_firstname,
            $this->sender_email,
            $this->sender_phone,
            $strArgReceiverType,
            $strDeliveryPointCode,
            $strArgReceiverCompany,
            $strArgReceiverAddress1,
            $strArgReceiverAddress2,
            $strArgReceiverZipCode,
            $strArgReceiverCity,
            $strArgReceiverLastName,
            $strArgReceiverFirstName,
            $strArgReceiverEMail,
            $strArgReceiverPhone,
            $strArgReceiverBuilding,
            $strArgReceiverAccessCode,
            $strArgReceiverFloor,
            $strArgReceiverInstructions,
            $this->delivery_notification,
            $strArgCarrierCode,
            $strArgPickupDate,
            $arrArgParcelRequest,
            $this->isPickupTypeOccasional(),
            $this->pickup_label_type,
            $this->getPickupClosingTime()->format('H:i'),
            $boolArgSendPickupRequest,
            $fltArgPaybackAmount
        );

        return $arrResult;
    }

    /**
     * Get a parcel tracking data.
     *
     * @param string $strArgParcelNumber
     *
     * @return array
     */
    public function trackingByConsignment($strArgParcelNumber)
    {
        TNTOfficiel_Logstack::log();

        if ($this->getAuthValidatedDateTime() === null) {
            return null;
        }

        $arrResult = $this->objWebServiceTNT->trackingByConsignment($strArgParcelNumber);

        return $arrResult;
    }
}
