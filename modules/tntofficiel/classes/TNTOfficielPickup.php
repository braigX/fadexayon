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
 * Class TNTOfficielPickup
 */
class TNTOfficielPickup extends ObjectModel
{
    // id_tntofficiel_pickup
    public $id;

    public $id_shop;
    public $account_number;
    public $pickup_date;

    public static $definition = array(
        'table' => 'tntofficiel_pickup',
        'primary' => 'id_tntofficiel_pickup',
        'fields' => array(
            'id_shop' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'account_number' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 8,
            ),
            'pickup_date' => array(
                'type' => ObjectModel::TYPE_DATE,
                'validate' => 'isDateFormat',
            ),
        ),
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

        $strTableName = $strTablePrefix . TNTOfficielPickup::$definition['table'];

        // Create table.
        $strSQLCreatePickup = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_pickup`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop`                       INT(10) UNSIGNED NOT NULL,
    `account_number`                VARCHAR(10) NOT NULL DEFAULT '',
    `pickup_date`                   DATE NOT NULL DEFAULT '0000-00-00',
-- Key.
    PRIMARY KEY (`id_tntofficiel_pickup`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

        $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreatePickup);
        if (is_string($boolDBResult)) {
            TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

            return false;
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return TNTOfficielPickup::checkTables();
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
        $strTableName = $strTablePrefix . TNTOfficielPickup::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielPickup::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null, $intArgLangID = null, $intArgShopID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID, $intArgLangID, $intArgShopID);
    }

    /**
     * Load existing object model or optionally create a new one for its ID.
     *
     * @param int  $intArgPickupID
     * @param bool $boolArgCreate
     * @param int  $intArgLangID
     * @param int  $intArgShopID
     *
     * @return TNTOfficielPickup|null
     */
    public static function loadPickupID($intArgPickupID = null)
    {
        TNTOfficiel_Logstack::log();

        $intPickupID = (int)$intArgPickupID;
        // Create.
        if ($intPickupID === 0) {
            // Create a new TNT pickup entry.
            $objTNTPickupModelCreate = new TNTOfficielPickup(null);
            // Apply default.

            $objTNTPickupModelCreate->save();
            $intPickupID = (int)$objTNTPickupModelCreate->id;
            unset($objTNTPickupModelCreate);
        }

        // No new pickup ID.
        if (!($intPickupID > 0)) {
            return null;
        }

        $strEntityID = $intPickupID . '-' . (int)null . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielPickup::$arrLoadedEntities)) {
            $objTNTPickupModel = TNTOfficielPickup::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTPickupModel)
                && (int)$objTNTPickupModel->id === $intPickupID
            ) {
                return $objTNTPickupModel;
            }
        }

        // Load existing TNT pickup entry.
        // or reload after create, to get default DB values after creation.
        $objTNTPickupModel = new TNTOfficielPickup($intPickupID);
        // Check.
        if (!Validate::isLoadedObject($objTNTPickupModel)
            || (int)$objTNTPickupModel->id !== $intPickupID
        ) {
            return null;
        }

        $objTNTPickupModel->id = (int)$objTNTPickupModel->id;
        $objTNTPickupModel->id_shop = (int)$objTNTPickupModel->id_shop;

        TNTOfficielPickup::$arrLoadedEntities[$strEntityID] = $objTNTPickupModel;

        return $objTNTPickupModel;
    }

    /**
     * Search for a list of existing pickup object model, via a shop ID and account number.
     *
     * @param int    $intArgShopID
     * @param string $strArgAccountNumber
     *
     * @return array list of TNTOfficielPickup model found.
     */
    public static function searchShopIDAccountNumber($intArgShopID, $strArgAccountNumber)
    {
        TNTOfficiel_Logstack::log();

        $arrObjTNTPickupModelList = array();

        $intShopID = (int)$intArgShopID;
        $strAccountNumber = (string)$strArgAccountNumber;

        // If no shop ID.
        if (!($intShopID > 0)) {
            return $arrObjTNTPickupModelList;
        }

        // Search row for shop ID and account number.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielPickup::$definition['table']);
        $objDbQuery->where('id_shop = ' . $intShopID);
        $objDbQuery->where('account_number = \'' . pSQL($strAccountNumber) . '\'');

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found.
        if (is_array($arrDBResult) && count($arrDBResult) > 0) {
            foreach ($arrDBResult as $arrValue) {
                if ($intShopID === (int)$arrValue['id_shop']) {
                    $objTNTPickupModel = TNTOfficielPickup::loadPickupID((int)$arrValue['id_tntofficiel_pickup']);
                    if ($objTNTPickupModel !== null) {
                        $arrObjTNTPickupModelList[] = $objTNTPickupModel;
                    }
                }
            }
        }

        return $arrObjTNTPickupModelList;
    }

    /**
     * Check if a pickup date is already registered.
     *
     * @param int    $intArgShopID
     * @param string $strArgAccountNumber
     * @param string $strArgPickupDate 'Y-m-d'
     *
     * @return bool true if requested date is a registered pickup date.
     */
    public static function isDateRegistered($intArgShopID, $strArgAccountNumber, $strArgPickupDate)
    {
        TNTOfficiel_Logstack::log();

        $arrObjTNTPickupModelList = TNTOfficielPickup::searchShopIDAccountNumber(
            $intArgShopID,
            $strArgAccountNumber
        );

        $arrPickupDateList = array();
        foreach ($arrObjTNTPickupModelList as $objTNTPickupModel) {
            $arrPickupDateList[] = $objTNTPickupModel->pickup_date;
        }

        return in_array($strArgPickupDate, $arrPickupDateList);
    }
}
