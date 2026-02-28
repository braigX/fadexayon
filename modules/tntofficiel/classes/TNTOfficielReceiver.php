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
 * Class TNTOfficielReceiver
 */
class TNTOfficielReceiver extends ObjectModel
{
    // id_tntofficiel_receiver
    public $id;

    public $id_address;
    public $receiver_email;
    public $receiver_mobile;
    public $receiver_building;
    public $receiver_accesscode;
    public $receiver_floor;
    public $receiver_instructions;

    public static $definition = array(
        'table' => 'tntofficiel_receiver',
        'primary' => 'id_tntofficiel_receiver',
        'fields' => array(
            'id_address' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'receiver_email' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 128,
            ),
            'receiver_mobile' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isPhoneNumber',
                'size' => 32,
            ),
            'receiver_building' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 16,
            ),
            'receiver_accesscode' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 16,
            ),
            'receiver_floor' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 16,
            ),
            'receiver_instructions' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 64,
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

        $strTableName = $strTablePrefix . TNTOfficielReceiver::$definition['table'];

        // If table exist.
        if (TNTOfficiel_Tools::isTableExist($strTableName) === true) {
            // Update table.
            TNTOfficielReceiver::upgradeTables();
        } else {
            // Create table.
            $strSQLCreateReceiver = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_receiver`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_address`                    INT(10) UNSIGNED NOT NULL,
    `receiver_email`                VARCHAR(128) NOT NULL DEFAULT '',
    `receiver_mobile`               VARCHAR(32) NOT NULL DEFAULT '',
    `receiver_building`             VARCHAR(16) NOT NULL DEFAULT '',
    `receiver_accesscode`           VARCHAR(16) NOT NULL DEFAULT '',
    `receiver_floor`                VARCHAR(16) NOT NULL DEFAULT '',
    `receiver_instructions`         VARCHAR(64) NOT NULL DEFAULT '',
-- Key.
    PRIMARY KEY (`id_tntofficiel_receiver`),
    UNIQUE INDEX `id_address` (`id_address`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

            $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreateReceiver);
            if (is_string($boolDBResult)) {
                TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

                return false;
            }

            TNTOfficiel_Logger::logInstall($strLogMessage);
        }

        return TNTOfficielReceiver::checkTables();
    }

    /**
     * Upgrade table.
     *
     * @return bool
     */
    public static function upgradeTables()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = __CLASS__ . '::' . __FUNCTION__;

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielReceiver::$definition['table'];

        // Upgrade table.
        $strSQLTableReceiverAddColumns = <<<SQL
ALTER TABLE `${strTableName}`
    ADD COLUMN `receiver_instructions`  VARCHAR(64) NOT NULL DEFAULT '' AFTER `receiver_floor`;
SQL;

        $arrRequireColumnsList = array('receiver_floor');
        $arrAddColumnsList = array('receiver_instructions');

        // If table exist, but not some columns.
        if (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrRequireColumnsList) === true
            && TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrAddColumnsList) === false
        ) {
            // Update table if it exists.
            $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLTableReceiverAddColumns);
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
        $strTableName = $strTablePrefix . TNTOfficielReceiver::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielReceiver::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID);
    }

    /**
     * Load existing object model or optionally create a new one for its ID.
     *
     * @param int  $intArgAddressID
     * @param bool $boolArgCreate
     *
     * @return TNTOfficielReceiver|null
     */
    public static function loadAddressID($intArgAddressID, $boolArgCreate = true)
    {
        TNTOfficiel_Logstack::log();

        $intAddressID = (int)$intArgAddressID;

        // No new address ID.
        if (!($intAddressID > 0)) {
            return null;
        }

        $strEntityID = '_' . $intAddressID . '-' . (int)null . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielReceiver::$arrLoadedEntities)) {
            $objTNTReceiverModel = TNTOfficielReceiver::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTReceiverModel)
                && (int)$objTNTReceiverModel->id_address === $intAddressID
            ) {
                return $objTNTReceiverModel;
            }
        }

        // Search row for address ID.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielReceiver::$definition['table']);
        $objDbQuery->where('id_address = ' . $intAddressID);

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found and match address ID.
        if (is_array($arrDBResult) && count($arrDBResult) === 1
            && $intAddressID === (int)$arrDBResult[0]['id_address']
        ) {
            // Load existing TNT address entry.
            $objTNTReceiverModel = new TNTOfficielReceiver((int)$arrDBResult[0]['id_tntofficiel_receiver']);
        } elseif ($boolArgCreate === true) {
            // Create a new TNT address entry.
            $objTNTReceiverModelCreate = new TNTOfficielReceiver(null);
            $objTNTReceiverModelCreate->id_address = $intAddressID;
            $objTNTReceiverModelCreate->save();
            // Reload to get default DB values after creation.
            $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($intAddressID, false);
        } else {
            // Log.
            $objException = new Exception(sprintf(
                TNTOfficiel::getCodeTranslate('errorUnableLoadNotFoundStr'),
                'TNTOfficielReceiver',
                'Address',
                $intAddressID
            ));
            TNTOfficiel_Logger::logException($objException);

            return null;
        }

        // Check.
        if (!Validate::isLoadedObject($objTNTReceiverModel)
            || (int)$objTNTReceiverModel->id_address !== $intAddressID
        ) {
            return null;
        }

        $objTNTReceiverModel->id = (int)$objTNTReceiverModel->id;
        $objTNTReceiverModel->id_address = (int)$objTNTReceiverModel->id_address;

        TNTOfficielReceiver::$arrLoadedEntities[$strEntityID] = $objTNTReceiverModel;

        return $objTNTReceiverModel;
    }

    /**
     * Search for a list of existing receiver object model, via a customer ID.
     *
     * @param int    $intArgCustomerID
     * @param string $strArgCountryISO
     *
     * @return array list of TNTOfficielReceiver model found.
     */
    public static function searchCustomerID($intArgCustomerID, $strArgCountryISO)
    {
        TNTOfficiel_Logstack::log();

        $arrObjTNTReceiverModelList = array();

        $intCustomerID = (int)$intArgCustomerID;

        // If no customer ID.
        if (!($intCustomerID > 0)) {
            return $arrObjTNTReceiverModelList;
        }

        // Get enabled ID list of Address from a Customer ID and Country.
        $arrIntAddressIDList = TNTOfficielReceiver::getPSAddressIDList($intCustomerID, $strArgCountryISO);
        // If no address ID, no DB Query.
        if (!(count($arrIntAddressIDList) > 0)) {
            return $arrObjTNTReceiverModelList;
        }

        // Search row for customer address ID list.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielReceiver::$definition['table']);
        $objDbQuery->where('id_address IN (' . implode(',', $arrIntAddressIDList) . ')');

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found.
        if (is_array($arrDBResult) && count($arrDBResult) > 0) {
            foreach ($arrDBResult as $arrValue) {
                // Load existing TNT receiver info (do not create).
                $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID((int)$arrValue['id_address'], false);
                // If success.
                if ($objTNTReceiverModel !== null) {
                    $arrObjTNTReceiverModelList[] = $objTNTReceiverModel;
                }
            }
        }

        return $arrObjTNTReceiverModelList;
    }

    /**
     * Load an existing Prestashop Address object from ID.
     *
     * @param int $intArgAddressID
     *
     * @return Address|null
     */
    public static function getPSAddressByID($intArgAddressID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSAddress = array();

        // Carrier ID must be an integer greater than 0.
        if (empty($intArgAddressID) || $intArgAddressID != (int)$intArgAddressID || !((int)$intArgAddressID > 0)) {
            return null;
        }

        $intAddressID = (int)$intArgAddressID;

        // If already loaded.
        if (array_key_exists($intAddressID, $arrStaticPSAddress)) {
            $objPSAddressMem = $arrStaticPSAddress[$intAddressID];
            // Check.
            if (Validate::isLoadedObject($objPSAddressMem)
                && (int)$objPSAddressMem->id === $intAddressID
            ) {
                return $objPSAddressMem;
            }
        }

        // Load Address.
        $objPSAddress = new Address($intAddressID);

        // If Address object not available.
        if (!Validate::isLoadedObject($objPSAddress)
            || (int)$objPSAddress->id !== $intAddressID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSAddress[$intAddressID] = $objPSAddress;

        return $objPSAddress;
    }

    /**
     * Load an existing Prestashop Customer object from ID.
     *
     * @param int $intArgCustomerID
     *
     * @return Customer|null
     */
    public static function getPSCustomerByID($intArgCustomerID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSCustomer = array();

        // Carrier ID must be an integer greater than 0.
        if (empty($intArgCustomerID) || $intArgCustomerID != (int)$intArgCustomerID || !((int)$intArgCustomerID > 0)) {
            return null;
        }

        $intCustomerID = (int)$intArgCustomerID;

        // If already loaded.
        if (array_key_exists($intCustomerID, $arrStaticPSCustomer)) {
            $objPSCustomerMem = $arrStaticPSCustomer[$intCustomerID];
            // Check.
            if (Validate::isLoadedObject($objPSCustomerMem)
                && (int)$objPSCustomerMem->id === $intCustomerID
            ) {
                return $objPSCustomerMem;
            }
        }

        // Load Customer.
        $objPSCustomer = new Customer($intCustomerID);

        // If Customer object not available.
        if (!Validate::isLoadedObject($objPSCustomer)
            || (int)$objPSCustomer->id !== $intCustomerID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSCustomer[$intCustomerID] = $objPSCustomer;

        return $objPSCustomer;
    }

    /**
     * Get enabled ID list of Address from a Customer ID, matching a country.
     *
     * @param        $intArgCustomerID
     * @param string $strArgCountryISO
     *
     * @return array
     */
    public static function getPSAddressIDList($intArgCustomerID, $strArgCountryISO)
    {
        TNTOfficiel_Logstack::log();

        $arrIntAddressIDList = array();

        $strCountryISO = Tools::strtoupper(trim($strArgCountryISO));

        $objPSCustomer = TNTOfficielReceiver::getPSCustomerByID($intArgCustomerID);
        // If Customer object available.
        if ($objPSCustomer !== null) {
            $arrAddressList = $objPSCustomer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
            foreach ($arrAddressList as $arrAddress) {
                $strReceiverCountryISOCheck = TNTOfficielReceiver::getCountryISOCode($arrAddress['id_country']);
                if ($strReceiverCountryISOCheck === $strCountryISO) {
                    $arrIntAddressIDList[] = (int)$arrAddress['id_address'];
                }
            }
        }

        return $arrIntAddressIDList;
    }

    /**
     * Validate Receiver Info.
     *
     * @param string $strArgReceiverEmail
     * @param string $strArgReceiverPhone
     * @param string $strArgReceiverBuilding
     * @param string $strArgReceiverAccessCode
     * @param string $strArgReceiverFloor
     *
     * @return array
     */
    public static function validateReceiverInfo(
        $strArgReceiverCountryISO = 'FR',
        $strArgReceiverEmail = '',
        $strArgReceiverPhone = '',
        $strArgReceiverBuilding = '',
        $strArgReceiverAccessCode = '',
        $strArgReceiverFloor = '',
        $strArgReceiverInstructions = ''
    ) {
        TNTOfficiel_Logstack::log();

        $boolReceiverAllowFixed = true;

        $strReceiverCountryISO = Tools::strtoupper(trim((string)$strArgReceiverCountryISO));

        $arrFormInput = array(
            'receiver_email' => trim((string)$strArgReceiverEmail),
            'receiver_phone' => trim((string)$strArgReceiverPhone),
            'receiver_building' => trim((string)$strArgReceiverBuilding),
            'receiver_accesscode' => trim((string)$strArgReceiverAccessCode),
            'receiver_floor' => trim((string)$strArgReceiverFloor),
            'receiver_instructions' => trim((string)$strArgReceiverInstructions),
        );

        $arrFormError = array();

        // Check if email is set and not empty.
        if (!isset($arrFormInput['receiver_email']) || $arrFormInput['receiver_email'] === '') {
            $arrFormError['receiver_email'] = sprintf(
                TNTOfficiel::getCodeTranslate('errorFieldMandatory'),
                TNTOfficiel::getCodeTranslate('fieldEmail')
            );
        }

        // Check if the email is valid.
        if (!filter_var($arrFormInput['receiver_email'], FILTER_VALIDATE_EMAIL)) {
            $arrFormError['receiver_email'] = sprintf(
                TNTOfficiel::getCodeTranslate('errorFieldInvalid'),
                TNTOfficiel::getCodeTranslate('fieldEmail')
            );
        }

        // Check if mobile phone is set and not empty.
        if (!isset($arrFormInput['receiver_phone']) || $arrFormInput['receiver_phone'] === '') {
            $arrFormError['receiver_phone'] = sprintf(
                TNTOfficiel::getCodeTranslate('errorFieldMandatory'),
                TNTOfficiel::getCodeTranslate('fieldMobile')
            );
        } else {
            $arrFormInput['receiver_phone'] = preg_replace('/[\s.-]+/ui', '', $arrFormInput['receiver_phone']);
        }

        // Check if mobile phone is valid.
        $strReceiverMobilePhone = TNTOfficiel_Tools::validateMobilePhone(
            $strReceiverCountryISO,
            $arrFormInput['receiver_phone']
        );

        $strReceiverFixedPhone = false;
        if ($boolReceiverAllowFixed) {
            // Check if fixed phone is valid.
            $strReceiverFixedPhone = TNTOfficiel_Tools::validateFixedPhone(
                $strReceiverCountryISO,
                $arrFormInput['receiver_phone']
            );
        }

        // Cleaned Phone.
        $strReceiverPhone = ($strReceiverMobilePhone !== false ? $strReceiverMobilePhone : $strReceiverFixedPhone);

        if ($strReceiverPhone === false) {
            $arrFormError['receiver_phone'] = TNTOfficiel::getCodeTranslate(
                $boolReceiverAllowFixed ? 'errorFieldInvalidPhone' : 'errorFieldInvalidMobile'
            );
        } else {
            $arrFormInput['receiver_phone'] = $strReceiverPhone;
        }

        // If building is set and not empty.
        if (isset($arrFormInput['receiver_building']) && $arrFormInput['receiver_building'] !== '') {
            $mxdBuildingValidated = TNTOfficiel_Tools::translitASCII($arrFormInput['receiver_building']);
            $arrFormInput['receiver_building'] = $mxdBuildingValidated;
        }
        // If accesscode is set and not empty.
        if (isset($arrFormInput['receiver_accesscode']) && $arrFormInput['receiver_accesscode'] !== '') {
            $mxdAccessCodeValidated = TNTOfficiel_Tools::translitASCII($arrFormInput['receiver_accesscode']);
            $arrFormInput['receiver_accesscode'] = $mxdAccessCodeValidated;
        }
        // If floor is set and not empty.
        if (isset($arrFormInput['receiver_floor']) && $arrFormInput['receiver_floor'] !== '') {
            $mxdFloorValidated = TNTOfficiel_Tools::translitASCII($arrFormInput['receiver_floor']);
            $arrFormInput['receiver_floor'] = $mxdFloorValidated;
        }

        // If instructions is set and not empty.
        if (isset($arrFormInput['receiver_instructions']) && $arrFormInput['receiver_instructions'] !== '') {
            $mxdInstructionsValidated = TNTOfficiel_Tools::translitASCII($arrFormInput['receiver_instructions']);
            $arrFormInput['receiver_instructions'] = $mxdInstructionsValidated;
        }

        $arrFieldMaxLength = array(
            'receiver_email' => array(
                'maxlength' => 80,
            ),
            'receiver_phone' => array(
                'maxlength' => 15,
            ),
            'receiver_building' => array(
                'maxlength' => 3,
            ),
            'receiver_accesscode' => array(
                'maxlength' => 7,
            ),
            'receiver_floor' => array(
                'maxlength' => 2,
            ),
            'receiver_instructions' => array(
                'maxlength' => 30,
            ),
        );

        foreach ($arrFieldMaxLength as $strFieldName => $arrField) {
            if ($arrFormInput[$strFieldName]) {
                if (Tools::strlen($arrFormInput[$strFieldName]) > $arrField['maxlength']) {
                    $arrFormError[$strFieldName] = sprintf(
                        TNTOfficiel::getCodeTranslate('errorFieldMaxChar'),
                        $arrField['maxlength']
                    );
                }
            }
        }

        return array(
            'fields' => $arrFormInput,
            'errors' => $arrFormError,
            'length' => count($arrFormError),
        );
    }

    /**
     * Get country ISO code from a Prestashop Country ID.
     *
     * @return null|string
     */
    public static function getCountryISOCode($intArgCountryID)
    {
        $strCountryISO = Country::getIsoById((int)$intArgCountryID);
        $strCountryISO = is_string($strCountryISO) ? Tools::strtoupper(trim($strCountryISO)) : null;

        return $strCountryISO;
    }

    /**
     * Get associated delivery address country ISO code.
     *
     * @return null|string
     */
    public function getAddressCountryISOCode()
    {
        // Get delivery address of receiver.
        $objPSAddress = TNTOfficielReceiver::getPSAddressByID($this->id_address);

        // Get country ISO code.
        $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddress->id_country);

        return $strReceiverCountryISO;
    }

    /**
     * Store Receiver Info for an Address ID.
     *
     * @param string $strArgCustomerEmail
     * @param string $strArgCustomerPhone
     * @param string $strArgAddressBuilding
     * @param string $strArgReceiverAccessCode
     * @param string $strArgAddressFloor
     *
     * @return array
     */
    public function storeReceiverInfo(
        $strArgCustomerEmail,
        $strArgCustomerPhone,
        $strArgAddressBuilding,
        $strArgReceiverAccessCode,
        $strArgAddressFloor,
        $strArgReceiverInstructions
    ) {
        TNTOfficiel_Logstack::log();

        // Validate receiver info.
        $arrFormReceiverInfoValidate = TNTOfficielReceiver::validateReceiverInfo(
            $this->getAddressCountryISOCode(),
            $strArgCustomerEmail,
            $strArgCustomerPhone,
            $strArgAddressBuilding,
            $strArgReceiverAccessCode,
            $strArgAddressFloor,
            $strArgReceiverInstructions
        );

        $boolStored = false;
        // If no errors.
        if ($arrFormReceiverInfoValidate['length'] === 0) {
            // Map correct field.
            $arrFormReceiverInfoValidate['fields']['receiver_mobile'] = $arrFormReceiverInfoValidate['fields']['receiver_phone'];
            // Model hydrate using validated fields data.
            // ObjectModel self escape data with formatValue(). Do not double escape using pSQL.
            $this->hydrate($arrFormReceiverInfoValidate['fields']);
            $boolStored = $this->save();
            // Remove.
            unset($arrFormReceiverInfoValidate['fields']['receiver_mobile']);
            /*
            // Get delivery address of receiver.
            $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($this->id_address);
            // If delivery address object available.
            if ($objPSAddressDelivery !== null) {
                // If receiver mobile is valid and non empty.
                if (!array_key_exists('receiver_mobile', $arrFormReceiverInfoValidate['errors'])
                    && $this->receiver_mobile
                ) {
                    // If phone field is empty and receiver mobile different from phone_mobile field.
                    if (!$objPSAddressDelivery->phone
                        && $this->receiver_mobile != $objPSAddressDelivery->phone_mobile
                    ) {
                        // Save receiver mobile for next time.
                        $objPSAddressDelivery->phone = $this->receiver_mobile;
                        $objPSAddressDelivery->save();
                    } elseif (!$objPSAddressDelivery->phone_mobile
                        && $this->receiver_mobile != $objPSAddressDelivery->phone
                    ) {
                        // Save receiver mobile for next time.
                        $objPSAddressDelivery->phone_mobile = $this->receiver_mobile;
                        $objPSAddressDelivery->save();
                    }
                }
            }
            */
        }

        // Validated and stored in DB.
        $arrFormReceiverInfoValidate['stored'] = $boolStored;

        return $arrFormReceiverInfoValidate;
    }

    /**
     * Search for the mobile or fixed phone from a delivery address or others customer address in the same country.
     *
     * @param Address $objArgPSAddressDelivery
     * @param         $boolArgFixed true to search for fixed phone, else search for mobile phone.
     *
     * @return string '' empty string if not found.
     */
    public static function searchPhone(Address $objArgPSAddressDelivery, $boolArgFixed = false)
    {
        TNTOfficiel_Logstack::log();

        $fnValidatePhone = array('TNTOfficiel_Tools', 'validateMobilePhone');
        if ($boolArgFixed) {
            $fnValidatePhone = array('TNTOfficiel_Tools', 'validateFixedPhone');
        }

        $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objArgPSAddressDelivery->id_country);

        // Mobile phone may be in phone field.
        $strAddressPhone = call_user_func(
            $fnValidatePhone,
            $strReceiverCountryISO,
            $objArgPSAddressDelivery->phone
        );

        if (!is_string($strAddressPhone)) {
            $strAddressPhone = call_user_func(
                $fnValidatePhone,
                $strReceiverCountryISO,
                $objArgPSAddressDelivery->phone_mobile
            );
        }

        // Search in Customer TNTOfficielReceiver info.
        if (!is_string($strAddressPhone)) {
            $strAddressPhone = false;

            // Get object list of TNTOfficielReceiver from a Customer ID and Country.
            $arrObjTNTReceiverList = TNTOfficielReceiver::searchCustomerID(
                $objArgPSAddressDelivery->id_customer,
                $strReceiverCountryISO
            );

            foreach ($arrObjTNTReceiverList as $objTNTReceiver) {
                if ($objTNTReceiver->receiver_mobile) {
                    $strAddressPhone = $objTNTReceiver->receiver_mobile;
                    break;
                }
            }
        }

        // Search in others Customer Addresses.
        if (!is_string($strAddressPhone)) {
            $strAddressPhone = false;

            // Get enabled ID list of Address from a Customer ID and Country.
            $arrIntAddressIDList = TNTOfficielReceiver::getPSAddressIDList(
                $objArgPSAddressDelivery->id_customer,
                $strReceiverCountryISO
            );

            foreach ($arrIntAddressIDList as $intAddressID) {
                $objPSAddress = TNTOfficielReceiver::getPSAddressByID($intAddressID);
                // If Address object not available, skip.
                if ($objPSAddress === null) {
                    continue;
                }

                $strReceiverCountryISOCheck = TNTOfficielReceiver::getCountryISOCode($objPSAddress->id_country);

                $strAddressPhoneCheck = call_user_func(
                    $fnValidatePhone,
                    $strReceiverCountryISOCheck,
                    $objPSAddress->phone
                );

                if (is_string($strAddressPhoneCheck)) {
                    $strAddressPhone = $strAddressPhoneCheck;
                    break;
                }

                $strAddressPhoneCheck = call_user_func(
                    $fnValidatePhone,
                    $strReceiverCountryISOCheck,
                    $objPSAddress->phone_mobile
                );
                if (is_string($strAddressPhoneCheck)) {
                    $strAddressPhone = $strAddressPhoneCheck;
                    break;
                }
            }
        }

        if (!is_string($strAddressPhone)) {
            $strAddressPhone = '';
        }

        return $strAddressPhone;
    }
}
