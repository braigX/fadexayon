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
 * Class TNTOfficiel.
 */
class TNTOfficiel extends CarrierModule
{
    // Name identifier.
    const MODULE_NAME = 'tntofficiel';
    // Version.
    const MODULE_VERSION = '1.0.16';
    // Release stamp : (((+new Date('YYYY-MM-DD HH:MM'))/1000)|0).toString(36)
    const MODULE_RELEASE = 's6jzw0';
    // Carrier Title.
    const MODULE_TITLE = 'TNT';

    // Google Map API Version (google.maps.version).
    const GMAP_API_VER = '3.exp';

    /**
     * Request timeout.
     */

    // Timeout for connection to the server.
    const REQUEST_CONNECTTIMEOUT = 8;
    // Timeout global (expiration).
    const REQUEST_TIMEOUT = 32;

    /**
     * Reserved by Cart Model.
     *
     * @var int|null Carrier ID set when retrieving shipping cost from module.
     * see getOrderShippingCost()
     */
    public $id_carrier = null;

    /** @var array[int] order ID list where shipment label was requested */
    public $arrRequestedSaveShipment = array();

    /** @var array Collection of module instances. */
    public static $arrObjTNTOfficiel = array();

    /** @var array Overridable Options. */
    public static $arrOptions = array(
        // Allow duplicate carrier.
        'CARRIER_CREATE_DUPLICATE' => false,
    );

    /**
     * TNTOfficiel constructor.
     */
    public function __construct()
    {
        // Immediate store instance.
        TNTOfficiel::$arrObjTNTOfficiel[] = $this;

        TNTOfficiel_Logstack::log();

        // Module is compliant with bootstrap. PS1.6+
        $this->bootstrap = true;

        // Version.
        $this->version = TNTOfficiel::MODULE_VERSION;

        // Prestashop supported version.
        $this->ps_versions_compliancy = array(
            'min' => TNTOfficiel::getVersionMin(),
            'max' => TNTOfficiel::getVersionMax(),
        );

        // Prestashop modules dependencies.
        $this->dependencies = array();

        // Name.
        $this->name = TNTOfficiel::MODULE_NAME;

        // Displayed Name. TNTOfficiel::MODULE_TITLE.
        $this->displayName = $this->l('TNT');
        // Description.
        $this->description = $this->l('Offer your customers, different delivery methods with TNT');

        // Type.
        $this->tab = 'shipping_logistics';

        // Confirmation message before uninstall.
        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');

        // Author.
        $this->author = 'Inetum';

        // Module key provided by addons.prestashop.com.
        $this->module_key = '1cf0bbdc13a4d4f319266cfe0bfac777';

        // Is this instance required on module when it is displayed in the module list.
        // This can be useful if the module has to perform checks on the PrestaShop configuration.
        $this->need_instance = 0;

        // Module Constructor.
        parent::__construct();

        /*
         * Display critical message.
         */

        // Check min version.
        if (version_compare(_PS_VERSION_, TNTOfficiel::getVersionMin(), '<')) {
            $this->displayAdminError(
                sprintf($this->l('Prestashop %s or higher is required.'), TNTOfficiel::getVersionMin()),
                null,
                array('admintntofficielaccountcontroller', 'admincarrierscontroller')
            );

            // Do nothing.
            return;
        }
        // Check max version.
        if (version_compare(_PS_VERSION_, TNTOfficiel::getVersionMax(), '>')) {
            $this->displayAdminError(
                sprintf($this->l('Prestashop %s or lower is required.'), TNTOfficiel::getVersionMax()),
                null,
                array('admintntofficielaccountcontroller', 'admincarrierscontroller')
            );

            // Do nothing.
            return;
        }

        /*
         * Display error or warning message.
         */

        // Check tntofficiel release version.
        if (TNTOfficiel::isDownGraded()) {
            $this->displayAdminError(
                $this->l('Update Required : Previously installed version is greater than the current one.'),
                null,
                array('admintntofficielaccountcontroller')
            );
        }

        if (!extension_loaded('curl')) {
            $this->displayAdminError(
                sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'cURL'),
                null,
                array('admintntofficielaccountcontroller')
            );
        }
        if (!extension_loaded('soap')) {
            $this->displayAdminError(
                sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'SOAP'),
                null,
                array('admintntofficielaccountcontroller')
            );
        }
        if (!extension_loaded('zip')) {
            $this->displayAdminWarning(
                sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'Zip'),
                null,
                array('admintntofficielaccountcontroller')
            );
        }

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // Do nothing.
            return;
        }

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            // Do nothing.
            return;
        }

        // If credential validated.
        if ($objTNTContextAccountModel->getAuthValidatedDateTime() !== null) {
            // Check each days state for auto invalidation (e.g: password changed).
            // If invalidated, module is disabled and carrier are not displayed on front-office.
            $objTNTContextAccountModel->updateAuthValidation(60 * 60 * 24);
        }

        // Apply default carriers values if required.
        TNTOfficielCarrier::forceAllCarrierDefaultValues();
    }

    /**
     * Get module instance.
     *
     * @return TNTOfficiel
     */
    public static function load()
    {
        // Retrieve existing instance.
        foreach (TNTOfficiel::$arrObjTNTOfficiel as $objTNTOfficiel) {
            if (is_object($objTNTOfficiel)
                && get_class($objTNTOfficiel) === 'TNTOfficiel'
            ) {
                return $objTNTOfficiel;
            }
        }

        // New one.
        // return new TNTOfficiel();
        return Module::getInstanceByName(TNTOfficiel::MODULE_NAME);
    }

    /**
     * @return string
     */
    public static function getVersionMin()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            return '8.0.0';
        }

        return '1.7.0.5';
    }

    /**
     * @return string
     */
    public static function getVersionMax()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            return '8.99.99';
        }

        return '1.7.99.99';
    }

    /**
     * @param $strArgName
     *
     * @return mixed|null
     */
    public static function getOption($strArgName)
    {
        if (array_key_exists($strArgName, TNTOfficiel::$arrOptions)) {
            return TNTOfficiel::$arrOptions[$strArgName];
        }

        return null;
    }

    /**
     * Get all translations for Admin or Front.
     *
     * @param bool $boolArgAdmin
     *
     * @return array
     */
    public function getTranslation($boolArgAdmin = false)
    {
        if ($boolArgAdmin) {
            $arrAdmin = array(
                'addStr' => $this->l('Add'),
                'updateStr' => $this->l('Update'),
                'deleteStr' => $this->l('Delete'),
                'successAddSuccessful' => $this->l('Add successful.'),
                'successUpdateSuccessful' => $this->l('Update successful.'),
                'successDeleteSuccessful' => $this->l('Delete successful.'),
                'errorUpdateFailRetry' => $this->l('Update not completed, please try again.'),
                'errorInvalidWeight' => $this->l('The weight is invalid.'),
                'errorInvalidWeightMax' => $this->l('The weight of a parcel cannot exceed %s Kg.'),
                'errorInvalidAmount' => $this->l('The amount is not valid.'),
                'errorInvalidAmountMin' => $this->l('The minimum amount of insurance for a parcel is %s €.'),
                'errorInvalidAmountMax' => $this->l('The maximum amount of insurance for a parcel is %s €.'),
                'errorInvalidAmountTotalMax' => $this->l('The total amount of parcel insurance cannot exceed %s €.'),
                'errorAtLeastParcel' => $this->l('An order requires at least one parcel.'),
                'errorAtMostParcels' => $this->l('A maximum of 30 parcels are allowed for one order.'),
                'errorAlreadyShipped' => $this->l('The order is already shipped.'),
                // TNTOfficiel_SoapClient :: expeditionCreation
                'errorWSAccountNotRegistered' => $this->l('Access to TNT web services not recognized.') . "\n"
                    . $this->l('The account number used is not authorized to access TNT\'s web services.') . "\n"
                    . $this->l('Please refer to the "prerequisites" in the TNT module installation manual') . ' '
                    . $this->l('to request by e-mail an authorization to connect your TNT account to the web services.'),
                'errorWSParcelWeightNotValid' => $this->l('The parcel weight is not valid.'),
                // TNTOfficielOrder :: updatePickupDate
                'errorDateInvalid' => $this->l('The date %s is invalid.'),
                'errorDateInvalidPassed' => $this->l('The date %s has already passed.'),
                'errorDateInvalidWE' => $this->l('The date is invalid on weekends.'),
                'errorDatePickupMissing' => $this->l('Pickup date is missing.'),
                // TNTOfficielOrder :: saveShipment
                'errorExpeditionAlreadyCreated' => $this->l('Expedition is already created for Order #%s.'),
                'errorDatePleaseChange' => $this->l('Please enter another date.'),
                'errorUnrecognizedAddress' => $this->l('Postal code or city unrecognized in the delivery address #%s.'),
                'warnPickupNoNewRequest' => $this->l('No new requests for occasional pickups will be sent to TNT.'),
                'warnPickupGiveToDriver' => $this->l('You will have to give the parcel to the driver scheduled for that day.'),
                'errorPickupDeadline' => $this->l('A request for an occasional pickup for %s, in the municipality %s, must be made before the %s deadline.'),
                'errorPickupValidFrom' => $this->l('Occasional pickup for the carrier is valid from %s.'),
                'errorPickupDriverAlreadyPassed' => $this->l('The driver has already passed by before %s.'),
                'errorDatePickupNotFeasible' => $this->l('The entered pickup date is not feasible.'),
                'errorPickupNotFeasible' => $this->l('The pickup is not feasible.'),
                // AdminTNTOfficielAccount.js
                'confirmApplyContext' =>
                    $this->l('The changes made will be applied to all selected stores.') . "\n\n"
                    . $this->l('Do you want to apply these changes and overwrite previously saved data?'),
                // hard2ReachArea.tpl
                'errorDownloadingHRA' =>
                    $this->l('Error while downloading the HRA list. Please contact the support.'),
                // AdminTNTOfficielAccountController
                'titleHeaderConfigureStr' => $this->l('Configure %s'),
                'warnWeightUnitStr' => $this->l('The supported weight unit is %s, but is currently %s.'),
                'buttonExportLogStr' => $this->l('Export logs'),
                'buttonInstallationManualStr' => $this->l('Installation manual'),
                'errorFieldMandatoryPleaseCheckStr' => $this->l('The field "%s" is mandatory, please check the information entered.'),
                'errorFieldsMandatoryPleaseCheckStr' => $this->l('The fields "%s" are mandatory, please check the information entered.'),
                'successSettingsUpdatedStr' => $this->l('Settings updated.'),
                'infoSelectContextStr' => $this->l('Before entering the configuration data for your module, be sure to have selected the context and the stores on which you want to apply this configuration.'),
                'warnFileZoneNotFoundStr' => $this->l('The file defining the Hard Access Zones can not be found, please click on the import button to download them.'),
                'titleFormMerchantStr' => $this->l('MERCHANT ACCOUNT SETTING'),
                'buttonSaveStr' => $this->l('Save'),
                'errorUnrecognizedAccountStr' => $this->l('The "Login myTNT", "MyTNT password" and "TNT account number" identifiers are not recognized by TNT, please check the information entered.'),
                'successAccountUpdatedStr' => $this->l('Account Settings updated.'),
                'alertSettingModifiedOnStr' => $this->l('Settings being modified on %s'),
                'labelAccountNumStr' => $this->l('TNT account number'),
                'labelLoginStr' => $this->l('MyTNT Login'),
                'labelPasswordStr' => $this->l('MyTNT password'),
                'errorInvalidPostalCodeStr' => $this->l('The postal code indicated is not valid, please check the information entered.'),
                'errorInvalidCityStr' => $this->l('The city shown is not valid, please check the information entered.'),
                'errorInvalidEmailStr' => $this->l('The format of the email is invalid, please check the information entered.'),
                'errorInvalidPhoneStr' => $this->l('The phone format is not valid, please check the information entered.'),
                'successSenderUpdatedStr' => $this->l('Sender Settings updated.'),
                'labelCompanyStr' => $this->l('Company'),
                'labelAddressStr' => $this->l('Address'),
                'labelAddressSupplementStr' => $this->l('Address supplement'),
                'labelZipCodeStr' => $this->l('Zip Code'),
                'labelCityStr' => $this->l('City'),
                'labelFirstNameStr' => $this->l('First name'),
                'labelLastNameStr' => $this->l('Last name'),
                'labelEmailStr' => $this->l('Email'),
                'labelPhoneStr' => $this->l('Phone'),
                'errorPreparationTimeStr' => $this->l('The "Preparation time" must be a positive integer less than or equal to 30 days, please check the information entered.'),
                'infoAPIKeyGMapServiceStr' => $this->l('This is the API key to use the mapping service of Google Maps.'),
                'infoAPIKeyGMapAccountStr' => $this->l('From your own Google account, you need to generate a key containing the "Maps JavaScript API and Geocoding API" APIs.'),
                'infoAPIKeyGMapMoreInfoStr' => $this->l('To generate or obtain more information about this key, %sclick here%s.'),
                'labelPrintFormatStr' => $this->l('Print format of labels'),
                'optionPrintA4Str' => $this->l('Standard A4 printer'),
                'optionPrintThermalStr' => $this->l('Thermal printer on your labels 4"x6"'),
                'optionPrintThermalLogoStr' => $this->l('Thermal printer on labels with TNT logo'),
                'labelPreparationTimeStr' => $this->l('Order preparation time (in days)'),
                'labelDeliveryNotificationStr' => $this->l('Send delivery notifications to your recipients (mails and sms)'),
                'optionEnabledStr' => $this->l('Enabled'),
                'optionDisabledStr' => $this->l('Disabled'),
                'labelViewEDDStr' => $this->l('View the estimated delivery date to customers'),
                'labelShowInsuranceStr' => $this->l('Display the Insurance option in TNT order management'),
                'descShowInsuranceStr' => $this->l('Activate this option if you want to be able to insure your shipment before issuing the transport vouchers.'),
                'labelPickupTypeStr' => $this->l('Pickup type'),
                'optionRegularStr' => $this->l('Regular'),
                'optionOccasionalStr' => $this->l('Occasional'),
                'labelDriverTimeStr' => $this->l('Driving time of the driver'),
                'labelClosingTimeStr' => $this->l('Closure hour'),
                'labelShowPickupNumStr' => $this->l('Display the pickup number in the list of shipping labels'),
                'labelAPIKeyGMapStr' => $this->l('Google Maps API Key'),
                'errorRegionalZoneMutExStr' => $this->l('All departments in the fields "Regional fees zone 1" and "Regional fees zone 2" must be separate, please check the information entered.'),
                'descRegionalZoneStr' => $this->l('Enter the department numbers for which you want to apply a specific pricing (ex: 01,18,75,95). You can set the rates in the "Setting TNT Services" tab.'),
                'labelRegionalZoneStr' => $this->l('Regional fees zone %s'),
                'errorOSShipmentAfterStr' => $this->l('The status to be applied automatically after the creation of the transport voucher must be different from the status that triggers the creation of the transport voucher.'),
                'successOrderStateUpdatedStr' => $this->l('OrderState Settings updated.'),
                'optionOSNoneStr' => $this->l('None (disabled)'),
                'labelOSShipmentSaveStr' => $this->l('Order status triggering creation of the shipping label'),
                'labelOSShipmentAfterStr' => $this->l('Order status to apply automatically after creation of the shipping label'),
                'descOSShipmentAfterStr' => $this->l('Select an order status only if you wish to apply a specific one as soon as the shipping label is created.'),
                'labelOSParcelTakeStr' => $this->l('Order status to apply when a parcel is taken in charge by carrier'),
                'descOSParcelTakeStr' => $this->l('Select an order status only if you wish to apply a specific one as soon as a parcel is taken in charge by the carrier.'),
                'labelOSParcelCheckStr' => $this->l('Enable automatic update of parcels delivery status'),
                'labelOSParcelCheckRateStr' => $this->l('Time interval beetween automatic parcels delivery status updates'),
                'optionOSParcelCheckRateStr' => $this->l('%sh'),
                'labelOSParcelAllDeliveredStr' => $this->l('Order status to apply when all parcels are delivered to final receiver'),
                'labelOSParcelAllDeliveredPointStr' => $this->l('Order status to apply when all parcels are delivered to partner merchant or TNT agency'),
                // AdminTNTOfficielCarrierController
                'titleHeaderSetupDeliveryStr' => $this->l('Set up %s delivery services'),
                'fieldIDStr' => $this->l('ID'),
                'fieldCarrierLabelStr' => $this->l('%s delivery service'),
                'fieldLogoLabelStr' => $this->l('Logo'),
                'fieldCarrierNameLabelStr' => $this->l('Carrier name'),
                'fieldParcelLabelStr' => $this->l('Parcel'),
                'fieldFreeLabelStr' => $this->l('Free'),
                'fieldShopStr' => $this->l('Shop'),
                'bulkDeleteStr' => $this->l('Delete selected'),
                'bulkConfirmDeleteStr' => $this->l('Delete selected items?'),
                'titleSelectCarrierStr' => $this->l('Select the carrier for which you want to update the pricing'),
                'warnAuthRequiredStr' => $this->l('To create carriers, the authentication must be validated on the account setting page.'),
                'titleFormDeliveryServicesStr' => $this->l('SETTING TNT DELIVERY SERVICES'),
                'buttonCreateCarrierStr' => $this->l('Create the carriers'),
                'warnDisabledCarrierStr' => $this->l('Disabled carrier(s) : %s.'),
                'linkCarrierOthersModificiationStr' => $this->l('Other carrier modifications (naming, taxes, zones, groups, ...) can be found on the Transporters page'),
                'errorCreateAlreadyAssociatedStr' => $this->l('At least one of the selected stores is already associated with this TNT service. To modify the associated pricing, please use the list of TNT carriers in the box at the bottom of the page. Otherwise please modify the selection of shops and try again.'),
                'infoCreateOneCarrierPerShopStr' => $this->l('A carrier will be created in Prestashop for each of the selected shops. They will then be editable independently.'),
                'errorNoCarrierCreatedStr' => $this->l('No TNT service was created.'),
                'alertCreateShopListStr' => $this->l('Create a new %s delivery service for %s.'),
                'labelSelectCarrierStr' => $this->l('Select the associated TNT service :'),
                'successCreateCarrierStr' => $this->l('The carrier (s) are well established, thank you for entering the pricing for this / these new carriers'),
                'tabRateZoneDefault' => $this->l('Default Rate Zone'),
                'tabRateZone' => $this->l('Rate Zone %s'),
                'errorAtLeastOneRangeForDefaultZoneStr' => $this->l('At least one rate range must be set for the default zone, please check the entered information.'),
                'errorExtraKgPriceFormatStr' => $this->l('The "extra kilogram price" must be a number with up to 6 decimals, and using the point as a separator, please check the information entered.'),
                'errorLimitFormatStr' => $this->l('The "limit" must be a number with 1 decimal place, and using the point as a separator, please check the information entered.'),
                'errorHRAFormatStr' => $this->l('The field "Hard to reach areas" must be a number with 6 decimal places maximum, and using the point as a separator, please check the information entered.'),
                'errorMarginFormatStr' => $this->l('The field "additional margin" must be a positive number to two decimal places between 0 and 100, using the point as a decimal point, please check the information entered.'),
                'errorWeightUpperBoundStr' => $this->l('The "upper bound" must be a number with 1 decimal place, and using the point as separator, please check the entered information.'),
                'errorPriceUpperBoundStr' => $this->l('The "upper bound" must be a number with up to 6 decimal places, and using the point as a separator, please check the entered information.'),
                'errorRangeIncompleteStr' => $this->l('At least one rate range is not completely entered, please check the information entered.'),
                'errorPriceFormatStr' => $this->l('The "price" must be a number with up to 6 decimals, and using the point as a separator, please check the information entered.'),
                'errorRangeMustAscOrderedStr' => $this->l('Shipping cost ranges are not entered in ascending order, please check the information entered.'),
                'successSaveStr' => $this->l('The data is correctly saved.'),
                'infoItemWeightStr' => $this->l('If any of the items ordered online weighs more than % kg, this TNT delivery services will not be offered.'),
                'infoNoTaxStr' => $this->l('No Tax.'),
                'infoTaxStr' => $this->l('Taxes %s : The VAT applied in %s is %s (%s).'),
                'infoOnStr' => $this->l('%s on %s :'),
                'alertModifiedCarrierListStr' => $this->l('TNT service being modified : %s'),
                'labelTNTRateStr' => $this->l('Use the specific TNT grid for setting the tarification rate?'),
                'optionYesStr' => $this->l('Yes'),
                'optionNoStr' => $this->l('No'),
                'labelTNTRateCloneStr' => $this->l('Apply the pricing of this service to other shops and/or other TNT services?'),
                'titleFormRateSettingStr' => $this->l('Pricing Setting for Delivery Services'),
                'buttonBackToListStr' => $this->l('Back to list'),
                // AdminTNTOfficielOrdersController
                'fieldReferenceStr' => $this->l('Reference'),
                'fieldCustomerStr' => $this->l('Customer'),
                'fieldCompanyStr' => $this->l('Company'),
                'fieldCarrierStr' => $this->l('Carrier'),
                'fieldTotalStr' => $this->l('Total'),
                'fieldPaymentStr' => $this->l('Payment'),
                'fieldStatusStr' => $this->l('Status'),
                'fieldDateStr' => $this->l('Date'),
                'fieldPDFStr' => $this->l('PDF'),
                'fieldTNTStr' => $this->l('TNT'),
                'fieldPickupNumStr' => $this->l('Pickup Number'),
                'bulkApplyStatusStr' => $this->l('Apply "%s" status'),
                'bulkShippingLabelStr' => $this->l('TNT shipping label'),
                'bulkManifestStr' => $this->l('TNT manifest'),
                'bulkRefreshDeliveryStatusStr' => $this->l('Refresh TNT delivery status'),
                'errorUnableLoadIDStr' => $this->l('Unable to load %s #%s'),
                'errorUnableSaveIDStr' => $this->l('Unable to save %s #%s'),
                'errorUnableApplyOSForOrderIDStr' => $this->l('Unable to apply "%s" OrderState for TNTOfficielOrder #%s'),
                'errorUnableCreateStr' => $this->l('Unable to create %s'),
                'errorUnableLoadForIDStr' => $this->l('Unable to load %s for %s #%s'),
            );

            foreach ($arrAdmin as $strTextID => $strTextTranslated) {
                $arrAdmin[$strTextID] = htmlentities($strTextTranslated);
            }

            return $arrAdmin;
        }

        $arrFront = array(
            'errorUnableLoadNotFoundStr' => $this->l('Unable to load %s, not found for %s #%s'),
            // TNTOfficielAccount
            'errorInvalidCredentialForAccountID' => $this->l('Invalid credentials for TNTOfficielAccount #%s'),
            'errorInvalidPaybackAmountStr' => $this->l('Cash on delivery by check is up to 10,000 Euros.'),
            // TNTOfficielCache
            'errorInvalidTTLNoStore' => $this->l('Unable to store cache. TTL is not a positive integer.'),
            'errorUnableLoadCreateCacheEntryKey' => $this->l('Unable to load or create cache entry for key %s.'),
            'errorUnableStoreValueType' => $this->l('Unable to store cache. Type is %s.'),
            'errorUnableStoreSize' => $this->l('Unable to store cache. Size exceed 65535 bytes.'),
            'errorUnableStoreSave' => $this->l('Unable to store cache. Error while saving.'),
            // TNTOfficielCarrier
            'errorDeleteIDCauseNotFoundStr' => $this->l('Deleting %s #%s because %s not found.'),
            'errorDeleteIDCauseDeleteStr' => $this->l('Deleting %s #%s because %s deleted.'),
            // TNTOfficielCart :: isPaymentReady
            'errorNoDeliveryCarrierSelected' => $this->l('No delivery carrier selected.'),
            'errorNoDeliveryOptionSelected' => $this->l('No delivery options selected.'),
            'errorWrongDeliveryCarrierSelected' => $this->l('Wrong delivery carrier selected'),
            'errorNoDeliveryAddressSelected' => $this->l('No delivery address selected.'),
            'errorDeliveryAddressDeleted' => $this->l('Delivery address deleted.'),
            'errorDeliveryOptionInconsistency' => $this->l('Delivery option inconsistency.'),
            'validateAdditionalCarrierInfo' =>
                $this->l('Please confirm the form with additional information for the carrier.'),
            'errorNoDeliveryPointSelected' => $this->l('No delivery point selected.'),
            // TNTOfficielReceiver :: validateReceiverInfo
            'errorFieldMandatory' => $this->l('The field "%s" is mandatory.'),
            'errorFieldInvalid' => $this->l('The field "%s" is invalid.'),
            'fieldEmail' => $this->l('E-mail'),
            'fieldMobile' => $this->l('Mobile phone'),
            'errorFieldInvalidPhone' =>
                $this->l('The phone number must be 10 digits long.'),
            'errorFieldMaxChar' => $this->l('The field must be %s characters maximum.'),
            // address.js
            'titleValidateDeliveryAddress' => $this->l('Validate your delivery address'),
            'errorUnknownPostalCode' => $this->l('Unknown postal code.'),
            'validatePostalCodeDeliveryAddress' =>
                $this->l('Please edit and validate the postal code of your delivery address.'),
            'errorUnrecognizedCity' => $this->l('Unrecognized city.'),
            'selectCityDeliveryAddress' =>
                $this->l('Please select the city from your delivery address.'),
            'labelPostalCode' => $this->l('Postal code'),
            'labelCity' => $this->l('City'),
            'buttonValidate' => $this->l('Validate'),
            // carrier.php
            'errorUnableSetDeliveryPointStr' => $this->l('Unable to set delivery point'),
            // AJAX
            'errorUnknow' => $this->l('An error has occurred.'),
            'errorTechnical' => $this->l('A technical error occurred.'),
            'errorConnection' => $this->l('A connection error occurred.'),
            'errorNetwork' => $this->l('A network error occurred.'),
            // JS
            'errorFancybox' => $this->l('Fancybox not available.'),
        );

        foreach ($arrFront as $strTextID => $strTextTranslated) {
            $arrFront[$strTextID] = htmlentities($strTextTranslated);
        }

        return $arrFront;
    }

    /**
     * Get translation from an error code.
     *
     * @param $strArgErrorCode
     *
     * @return string
     */
    public static function getCodeTranslate($strArgErrorCode)
    {
        $objTNTOfficiel = TNTOfficiel::load();

        $arrTranslate = $objTNTOfficiel->getTranslation();
        // If error code found.
        if (array_key_exists($strArgErrorCode, $arrTranslate)) {
            return html_entity_decode($arrTranslate[$strArgErrorCode]);
        }

        $arrTranslate = $objTNTOfficiel->getTranslation(true);
        // If error code found.
        if (array_key_exists($strArgErrorCode, $arrTranslate)) {
            return html_entity_decode($arrTranslate[$strArgErrorCode]);
        }

        return $strArgErrorCode;
    }

    /**
     * Get Template resource handle from file.
     *
     * @param $templatePath
     *
     * @return string the resource handle of the template file.
     */
    public static function getTemplateResource($templatePath)
    {
        // Template resource handle.
        return sprintf(
            'module:%s/%s%s',
            TNTOfficiel::MODULE_NAME,
            TNTOfficiel::getPathTemplate(),
            $templatePath
        );
    }

    /**
     * Fetch template file.
     *
     * @param $templatePath
     *
     * @return string rendered template output.
     */
    public function fetchTemplate($templatePath)
    {
        // Display template.
        return $this->fetch(
            TNTOfficiel::getTemplateResource($templatePath)
        );
    }

    /**
     * Display template file.
     *
     * @param $templatePath
     *
     * @return string rendered template output.
     */
    public function displayTemplate($templatePath)
    {
        return $this->display(
            __FILE__,
            TNTOfficiel::getPathTemplate() . $templatePath
        );
    }

    /**
     * Get HTML text with optional link.
     *
     * @param string $strArgMessage
     * @param array  $arrArgAttr
     * @param string $strArgName
     *
     * @return mixed
     */
    public function getTextLink($strArgMessage, $arrArgAttr = array(), $strArgName = null)
    {
        $this->smarty->assign(
            array(
                'strName' => $strArgName,
                'strMessage' => htmlspecialchars_decode($strArgMessage, ENT_QUOTES),
                'arrAttr' => $arrArgAttr,
            )
        );

        return $this->displayTemplate('front/textLink.tpl');
    }

    /**
     * Get a message for admin controller.
     *
     * @param string $strArgType
     * @param string $strArgMessage
     * @param string $strArgURL
     * @param array  $arrArgControllers
     *
     * TODO : Add shop/group context filter
     *
     * @return bool
     */
    public function getAdminMessage($strArgType, $strArgMessage, $strArgURL = null, $arrArgControllers = array())
    {
        // Filter type.
        $arrArgType = array('errors', 'warnings', 'informations', 'confirmations');
        if (!in_array($strArgType, $arrArgType, true)) {
            return false;
        }

        /** @var Context $objContext */
        $objContext = $this->context;

        if (!property_exists($objContext, 'controller')) {
            return false;
        }
        /** @var AdminController $objAdminController */
        $objAdminController = $objContext->controller;

        // If not an AdminController or is an AJAX request.
        if (!TNTOfficiel_Tools::isAdminController($objAdminController) || $objAdminController->ajax) {
            return false;
        }

        // Get Controller Name.
        $strCurrentControllerName = TNTOfficiel_Tools::getControllerName($objAdminController);

        // If controller filter list exist but not in list.
        if (!is_array($arrArgControllers)
            || (count($arrArgControllers) > 0 && !in_array($strCurrentControllerName, $arrArgControllers))
        ) {
            return false;
        }

        if (!is_string($strArgMessage) || Tools::strlen($strArgMessage) === 0) {
            return false;
        }

        if (!is_string($strArgURL)) {
            $strArgURL = null;
        }

        // TODO: quote escape check : $strArgMessage = str_replace('\'', '’', $strArgMessage);

        $strMessage = $this->getTextLink($strArgMessage, array('href' => $strArgURL), TNTOfficiel::MODULE_TITLE);

        if (is_string($strMessage)) {
            $objAdminController->{$strArgType}[] = $strMessage;
        }

        return true;
    }

    /**
     * Display a warning for admin controller.
     *
     * @param string $strArgMessage
     * @param string $strArgURL
     * @param array  $arrArgControllers
     *
     * @return bool
     */
    public function displayAdminWarning($strArgMessage, $strArgURL = null, $arrArgControllers = array())
    {
        return $this->getAdminMessage('warnings', $strArgMessage, $strArgURL, $arrArgControllers);
    }

    /**
     * Display an error for admin controller.
     *
     * @param string $strArgMessage
     * @param string $strArgURL
     * @param array  $arrArgControllers
     *
     * @return bool
     */
    public function displayAdminError($strArgMessage, $strArgURL = null, $arrArgControllers = array())
    {
        return $this->getAdminMessage('errors', $strArgMessage, $strArgURL, $arrArgControllers);
    }

    /**
     * Module install.
     *
     * @return bool
     */
    public function install()
    {
        TNTOfficiel_Logstack::log();

        // If MultiShop and more than 1 Shop.
        if (Shop::isFeatureActive()) {
            // Define Shop context to all Shops.
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        TNTOfficiel_Logger::logInstall(
            sprintf(
                $this->l('__ %s [%s] v%s : Install initiated __'),
                TNTOfficiel::MODULE_TITLE,
                TNTOfficiel::MODULE_NAME,
                TNTOfficiel::MODULE_VERSION
            )
        );

        // Check min version.
        if (version_compare(_PS_VERSION_, TNTOfficiel::getVersionMin(), '<')) {
            $strMessage = sprintf(
                $this->l('Prestashop %s or higher is required.'),
                TNTOfficiel::getVersionMin()
            );
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            return false;
        }
        // Check max version.
        if (version_compare(_PS_VERSION_, TNTOfficiel::getVersionMax(), '>')) {
            $strMessage = sprintf(
                $this->l('Prestashop %s or lower is required.'),
                TNTOfficiel::getVersionMax()
            );
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            return false;
        }

        // Check tntofficiel release version.
        if (TNTOfficiel::isDownGraded()) {
            $strMessage =
                $this->l('Downgrade not allowed : Previously installed version is greater than the current one.');
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            // Do not install.
            return false;
        }

        if (!extension_loaded('curl')) {
            $strMessage = sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'cURL');
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            return false;
        }
        if (!extension_loaded('soap')) {
            $strMessage = sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'SOAP');
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            return false;
        }
        if (!extension_loaded('zip')) {
            $strMessage = sprintf($this->l('You have to enable the PHP %s extension on your server.'), 'Zip');
            TNTOfficiel_Logger::logInstall($strMessage, false);
            $this->_errors[] = $strMessage;

            return false;
        }

        // Store release.
        Configuration::updateGlobalValue('TNTOFFICIEL_RELEASE', TNTOfficiel::MODULE_RELEASE);

        // Remove deprecated files.
        TNTOfficiel_Install::uninstallDeprecatedFiles();

        // Prestashop install.
        if (!parent::install()) {
            TNTOfficiel_Logger::logInstall('Module::install', false);
            $this->_errors[] = $this->l('Unable to install Module::install().');

            return false;
        }
        TNTOfficiel_Logger::logInstall('Module::install');

        // Update settings.
        if (!TNTOfficiel_Install::updateSettings()) {
            $this->_errors[] = $this->l('Unable to define configuration.');

            return false;
        }

        // Register hooks.
        foreach (TNTOfficiel_Install::$arrHookList as $strHookName) {
            if (!$this->registerHook($strHookName)) {
                TNTOfficiel_Logger::logInstall('Module::registerHook : ' . $strHookName, false);
                $this->_errors[] = sprintf($this->l('Unable to register hook "%s".'), $strHookName);

                return false;
            } else {
                TNTOfficiel_Logger::logInstall('Module::registerHook : ' . $strHookName);
            }
        }

        // Create the TNT OrderStates.
        if (!TNTOfficiel_Install::createOrderStates()) {
            $this->_errors[] = $this->l('Unable to add order states.');

            return false;
        }

        // Create the TNT tab.
        if (!TNTOfficiel_Install::createTab()) {
            $this->_errors[] = $this->l('Unable to add menu tab.');

            return false;
        }

        // Create the tables.
        if (!TNTOfficiel_Install::createTables()) {
            $this->_errors[] = $this->l('Unable to create tables in database.');

            return false;
        }

        // Clear cache.
        TNTOfficiel_Install::clearCache();

        TNTOfficiel_Logger::logInstall(
            sprintf(
                $this->l('__ %s [%s] v%s : Install complete __'),
                TNTOfficiel::MODULE_TITLE,
                TNTOfficiel::MODULE_NAME,
                TNTOfficiel::MODULE_VERSION
            )
        );

        return true;
    }

    /**
     * Module uninstall.
     *
     * @return bool
     */
    public function uninstall()
    {
        TNTOfficiel_Logstack::log();

        TNTOfficiel_Logger::logUninstall(
            sprintf(
                $this->l('__ %s [%s] v%s : Uninstall initiated __'),
                TNTOfficiel::MODULE_TITLE,
                TNTOfficiel::MODULE_NAME,
                TNTOfficiel::MODULE_VERSION
            )
        );

        // Delete Tab.
        if (!TNTOfficiel_Install::deleteTab()) {
            $this->_errors[] = $this->l('Unable to delete menu tab.');

            return false;
        }

        // Delete Settings.
        if (!TNTOfficiel_Install::deleteSettings()) {
            $this->_errors[] = $this->l('Unable to delete configuration.');

            return false;
        }

        // Prestashop Uninstall : Uninstall class or controllers override, Unregister Hooks, etc.
        if (!parent::uninstall()) {
            TNTOfficiel_Logger::logUninstall('Module::uninstall', false);
            $this->_errors[] = $this->l('Unable to uninstall Parent::uninstall().');

            return false;
        }
        TNTOfficiel_Logger::logUninstall('Module::uninstall');

        TNTOfficiel_Logger::logUninstall(
            sprintf(
                $this->l('__ %s [%s] v%s : Uninstall complete __'),
                TNTOfficiel::MODULE_TITLE,
                TNTOfficiel::MODULE_NAME,
                TNTOfficiel::MODULE_VERSION
            )
        );

        // TODO: check default carrier is not TNT.
        // Configuration::get('PS_CARRIER_DEFAULT')

        return true;
    }

    /**
     * Module configuration page content.
     * Large form is displayed in a custom admin controller.
     *
     * @return string HTML content.
     */
    public function getContent()
    {
        TNTOfficiel_Logstack::log();

        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminTNTOfficielAccount')
        );

        return '';
    }

    /**
     * Is current release older than the previously installed.
     */
    public static function isDownGraded()
    {
        // Check tntofficiel release version.
        $strRLPrevious = (string)Configuration::get('TNTOFFICIEL_RELEASE');
        $intTSPrevious = (int)base_convert($strRLPrevious, 36, 10);
        $intTSCurrent = base_convert(TNTOfficiel::MODULE_RELEASE, 36, 10);

        return ($intTSCurrent < $intTSPrevious);
    }

    /**
     * Is module ready for current context.
     */
    public static function isContextReady()
    {
        TNTOfficiel_Logstack::log();

        // Is module not correctly installed ?
        if (!TNTOfficiel_Install::isReady()) {
            return false;
        }

        // If module not activated (ps_module:active) $this->active ps_module_shop
        if (!Module::isEnabled(TNTOfficiel::MODULE_NAME)) {
            return false;
        }

        return true;
    }

    /**
     * Get Prestashop absolute directory.
     *
     * @return string
     */
    public static function getDirPS()
    {
        return _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR;
    }

    /**
     * Get module absolute directory.
     * getLocalPath() like but static.
     *
     * @return string
     */
    public static function getDirModule($strArgFolderName = null)
    {
        $strDirModule = _PS_MODULE_DIR_ . TNTOfficiel::MODULE_NAME . DIRECTORY_SEPARATOR;

        if ($strArgFolderName == 'js') {
            $strDirModule .= TNTOfficiel::getPathJS();
        } elseif ($strArgFolderName == 'css') {
            $strDirModule .= TNTOfficiel::getPathCSS();
        } elseif ($strArgFolderName == 'template') {
            $strDirModule .= TNTOfficiel::getPathTemplate();
        } elseif ($strArgFolderName == 'image') {
            $strDirModule .= TNTOfficiel::getPathImage();
        }

        return $strDirModule;
    }

    /**
     * Get Base dir from Prestashop constant.
     *
     * @param string $strArgConstBaseURI
     *
     * @return string
     */
    public static function getFolderBase($strArgConstBaseURI = '__PS_BASE_URI__')
    {
        // Allowed Const based on __PS_BASE_URI__
        $arrAllowedURI = array(
            '_MODULE_DIR_',
            '_THEMES_DIR_',
            '_THEME_DIR_',
            '_PS_JS_DIR_',
            '_PS_CSS_DIR_',
        );

        $strConstBaseURI = __PS_BASE_URI__;
        if (in_array($strArgConstBaseURI, $arrAllowedURI)) {
            $strConstBaseURI = constant($strArgConstBaseURI);
        }

        $strURIPath = Tools::substr($strConstBaseURI, Tools::strlen(__PS_BASE_URI__));

        return $strURIPath;
    }

    /**
     * Get module folder for assets.
     * getPathUri() like but without base URI.
     *
     * @param bool $boolArgBaseURI
     *
     * @return string
     */
    public static function getFolderModule($strArgFolderName = null)
    {
        $strModulesDir = TNTOfficiel::getFolderBase('_MODULE_DIR_');
        // Trim left slash.
        $strModulesDir = ltrim($strModulesDir, '/');

        $strModulePath = $strModulesDir . TNTOfficiel::MODULE_NAME . '/';

        if ($strArgFolderName == 'js') {
            $strModulePath .= TNTOfficiel::getPathJS();
        } elseif ($strArgFolderName == 'css') {
            $strModulePath .= TNTOfficiel::getPathCSS();
        } elseif ($strArgFolderName == 'template') {
            $strModulePath .= TNTOfficiel::getPathTemplate();
        } elseif ($strArgFolderName == 'image') {
            $strModulePath .= TNTOfficiel::getPathImage();
        }

        return $strModulePath;
    }

    /**
     * Get release folder for JS assets.
     *
     * @return string
     */
    public static function getPathJS()
    {
        return implode('/', array('views', 'js', TNTOfficiel::MODULE_RELEASE)) . '/';
    }

    /**
     * Get release folder for CSS assets.
     *
     * @return string
     */
    public static function getPathCSS()
    {
        return implode('/', array('views', 'css', TNTOfficiel::MODULE_RELEASE)) . '/';
    }

    /**
     * Get folder for images assets.
     *
     * @return string
     */
    public static function getPathImage()
    {
        return implode('/', array('views', 'img')) . '/';
    }

    /**
     * Get folder for templates assets.
     *
     * @return string
     */
    public static function getPathTemplate()
    {
        return implode('/', array('views', 'templates')) . '/';
    }

    /**
     * Get base URI path from Prestashop constant.
     *
     * @param string $strArgConstBaseURI
     * @param bool   $boolArgContext true to use Base URI from current shop context.
     *
     * @return string
     */
    public static function getURIBase(
        $strArgConstBaseURI = '__PS_BASE_URI__',
        $boolArgContext = false
    ) {
        $strURIPath = TNTOfficiel::getFolderBase($strArgConstBaseURI);

        if ($boolArgContext) {
            // Context Shop.
            $objContext = Context::getContext();
            $objPSShop = $objContext->shop;
            $strBaseURI = $objPSShop->physical_uri . $objPSShop->virtual_uri;
        } else {
            $strBaseURI = __PS_BASE_URI__;
        }

        $strURIPath = $strBaseURI . $strURIPath;

        return $strURIPath;
    }

    /**
     * Get Domain URL for current Shop Context.
     *
     * @return string
     */
    public static function getURLDomain($objPSArgShop = null)
    {
        $objContext = Context::getContext();
        $objPSShop = $objContext->shop;

        if (Validate::isLoadedObject($objPSArgShop)
            && (int)$objPSArgShop->id > 0
        ) {
            $objPSShop = $objPSArgShop;
        }

        $strDomainURL = 'http://' . $objPSShop->domain;
        if (Configuration::get('PS_SSL_ENABLED')) {
            $strDomainURL = 'https://' . $objPSShop->domain_ssl;
        }

        return $strDomainURL;
    }

    /**
     * Get Base URL for current Shop Context.
     *
     * @return string
     */
    public static function getURLFrontBase()
    {
        return TNTOfficiel::getURLDomain() . TNTOfficiel::getURIBase('__PS_BASE_URI__', true);
    }

    /**
     * Get module absolute path URI.
     * getPathUri() like but static.
     *
     * @return string
     */
    public static function getURLModulePath($strArgFolderName = null)
    {
        $strModulesDir = TNTOfficiel::getURIBase('_MODULE_DIR_', true);
        // Trim left slash.
        $strModulesDir = ltrim($strModulesDir, '/');

        $strModulePath = '/' . $strModulesDir . TNTOfficiel::MODULE_NAME . '/';

        if ($strArgFolderName == 'js') {
            $strModulePath .= TNTOfficiel::getPathJS();
        } elseif ($strArgFolderName == 'css') {
            $strModulePath .= TNTOfficiel::getPathCSS();
        } elseif ($strArgFolderName == 'template') {
            $strModulePath .= TNTOfficiel::getPathTemplate();
        } elseif ($strArgFolderName == 'image') {
            $strModulePath .= TNTOfficiel::getPathImage();
        }

        return $strModulePath;
    }

    /**
     * Add JS.
     *
     * @param string $strArgFile
     *
     * @return string
     */
    public function addJS($strArgFile)
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;

        // Controller.
        $objController = $objContext->controller;

        $strAssetJSDir = TNTOfficiel::getDirModule('js');
        $strAssetJSPath = TNTOfficiel::getFolderModule('js');

        $strFile = $strArgFile;
        $strServer = 'local';

        // If file exist in current module.
        if (file_exists($strAssetJSDir . $strArgFile)) {
            // Add current module path URI.
            $strFile = $strAssetJSPath . $strArgFile;
        }

        if (is_string(parse_url($strFile, PHP_URL_SCHEME))) {
            $strServer = 'remote';
        }

        // If an AdminController.
        if (TNTOfficiel_Tools::isAdminController($objController)) {
            if ($strServer === 'local') {
                // Prepend Admin Physical/Virtual Base URI.
                $strFile = __PS_BASE_URI__ . $strFile;
            }
            // Add for Admin.
            $objController->addJS($strFile);
        } else {
            // Add for Front.
            $objController->registerJavascript(
                sha1($strFile),
                $strFile,
                array('position' => 'bottom', 'priority' => 80, 'server' => $strServer)
            );
        }

        return $strFile;
    }

    /**
     * Add CSS.
     *
     * @param string $strArgFile
     * @param string $strArgCSSMediaType
     *
     * @return string
     */
    public function addCSS($strArgFile, $strArgCSSMediaType = 'all')
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;

        // Controller.
        $objController = $objContext->controller;

        $strAssetCSSDir = TNTOfficiel::getDirModule('css');
        $strAssetCSSPath = TNTOfficiel::getFolderModule('css');

        $strFile = $strArgFile;
        $strServer = 'local';

        // If file exist in current module.
        if (file_exists($strAssetCSSDir . $strArgFile)) {
            // Add current module path URI.
            $strFile = $strAssetCSSPath . $strArgFile;
        }

        if (is_string(parse_url($strFile, PHP_URL_SCHEME))) {
            $strServer = 'remote';
        }

        // If an AdminController.
        if (TNTOfficiel_Tools::isAdminController($objController)) {
            if ($strServer === 'local') {
                // Prepend Admin Physical/Virtual Base URI.
                $strFile = __PS_BASE_URI__ . $strFile;
            }
            // Add for Admin.
            $objController->addCSS($strFile/*, $strArgCSSMediaType*/);
        } else {
            // Add for Front.
            $objController->registerStylesheet(
                sha1($strFile),
                $strFile,
                array('media' => $strArgCSSMediaType, 'priority' => 80, 'server' => $strServer)
            );
        }

        return $strFile;
    }

    /**
     * @return array
     */
    public function getCommonVariable()
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;
        $objLink = $objContext->link;

        // Controller.
        $objController = $objContext->controller;
        // Get Controller Name.
        $strCurrentControllerName = TNTOfficiel_Tools::getControllerName($objController);
        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        $boolContextAuth = false;
        $strAPIGoogleMapKey = '';
        // If an account is available for current shop context.
        if ($objTNTContextAccountModel !== null) {
            $boolContextAuth = $objTNTContextAccountModel->getAuthValidatedDateTime() !== null;
            $strAPIGoogleMapKey = $objTNTContextAccountModel->api_google_map_key;
        }

        $arrCarrierList = array();
        // Include deleted carrier in BO.
        $arrObjTNTCarrierModelList = TNTOfficielCarrier::getContextCarrierModelList(defined('_PS_ADMIN_DIR_'));
        foreach ($arrObjTNTCarrierModelList as $intTNTCarrierID => $objTNTCarrierModel) {
            $arrCarrierList[$intTNTCarrierID] = array(
                'account_type' => $objTNTCarrierModel->account_type,
                'carrier_type' => $objTNTCarrierModel->carrier_type,
            );
        }

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $arrCountryList = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $arrCountryList = Country::getCountries($this->context->language->id, true);
        }

        foreach ($arrCountryList as $intCountryID => $arrCountryItem) {
            $arrCountryList[$intCountryID] = $arrCountryList[$intCountryID]['iso_code'];
        }

        // Javascript config.
        $arrTNTOfficiel = array(
            'timestamp' => microtime(true) * 1000,
            'module' => array(
                'name' => TNTOfficiel::MODULE_NAME,
                'version' => TNTOfficiel::MODULE_VERSION,
                'title' => TNTOfficiel::MODULE_TITLE,
                'context' => $boolContextAuth,
                'ready' => TNTOfficiel::isContextReady(),
            ),
            'config' => array(
                'google' => array(
                    'map' => array(
                        'url' => 'https://maps.googleapis.com/maps/api/js',
                        'data' => array(
                            'v' => TNTOfficiel::GMAP_API_VER,
                            'key' => $strAPIGoogleMapKey,
                        ),
                        'default' => array(
                            "lat" => 46.827742,
                            "lng" => 2.835644,
                            "zoom" => 6,
                        ),
                    ),
                ),
            ),
            'translate' => $this->getTranslation(),
            'link' => array(
                'controller' => $strCurrentControllerName,
                'front' => array(
                    // Get Front Shop full base URL.
                    'shop' => TNTOfficiel::getURLFrontBase(),
                    'module' => array(
                        'boxDeliveryPoints' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'carrier',
                            array('action' => 'boxDeliveryPoints'),
                            true
                        ),
                        'saveProductInfo' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'carrier',
                            array('action' => 'saveProductInfo'),
                            true
                        ),
                        'checkPaymentReady' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'carrier',
                            array('action' => 'checkPaymentReady'),
                            true
                        ),
                        'storeReceiverInfo' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'address',
                            array('action' => 'storeReceiverInfo'),
                            true
                        ),
                        'getAddressCities' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'address',
                            array('action' => 'getCities'),
                            true
                        ),
                        'updateAddressDelivery' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'address',
                            array('action' => 'updateDeliveryAddress'),
                            true
                        ),
                        'checkAddressPostcodeCity' => $objLink->getModuleLink(
                            TNTOfficiel::MODULE_NAME,
                            'address',
                            array('action' => 'checkPostcodeCity'),
                            true
                        ),
                    ),
                    'page' => array(
                        'order' => $objLink->getPageLink('order', true),
                    ),
                ),
                'back' => null,
                'image' => TNTOfficiel::getURLModulePath('image'),
            ),
            'country' => $arrCountryList,
            'carrier' => array(
                'list' => $arrCarrierList,
            ),
            'order' => array(
                'isTNT' => false,
            ),
        );

        return $arrTNTOfficiel;
    }

    /**
     * HOOK (AKA backOfficeHeader) called inside the head tag.
     * Ideal location for adding JavaScript and CSS files.
     * Hook called even if module is disabled !
     *
     * @param array $arrArgHookParams
     *
     * @return string HTML content in head tag.
     */
    public function hookDisplayBackOfficeHeader($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;
        // Controller.
        $objAdminController = $objContext->controller;
        // Get Controller Name.
        $strCurrentControllerName = TNTOfficiel_Tools::getControllerName($objAdminController);

        // Update All Parcels and OrderState accordingly.
        TNTOfficielOrder::updateAllOrderStateDeliveredParcels();

        // Global Admin CSS.
        $this->addCSS('Admin.css', 'all');

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'ajax' => $objAdminController->ajax,
                'controller_type' => $objAdminController->controller_type,
                'controllername' => $strCurrentControllerName,
                'controllerfilename' => Dispatcher::getInstance()->getController(),
            )
        );

        // Display nothing.
        return '';
    }

    /**
     * HOOK called to include CSS or JS files in the Back-Office header.
     *
     * @param array $arrArgHookParams
     */
    public function hookActionAdminControllerSetMedia($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        $objContext = $this->context;
        // Controller.
        $objAdminController = $objContext->controller;
        // Get Controller Name.
        $strCurrentControllerName = TNTOfficiel_Tools::getControllerName($objAdminController);

        $strAssetJSPath = TNTOfficiel::getFolderModule('js');

        $this->addCSS('global.css', 'all');
        $this->addJS('global.js');

        // Global Admin CSS.
        $this->addCSS('Admin.css', 'all');

        switch ($strCurrentControllerName) {
            case 'adminorderscontroller':
                if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
                    // Form.css required for address-city-check, ExtraData
                    $this->addCSS('form.css', 'all');
                    //
                    $this->addCSS('carrier.css', 'all');

                    // DatePicker.
                    $objAdminController->addJqueryUI('ui.datepicker');

                    // FancyBox required to display form (cp/ville check).
                    $objAdminController->addJqueryPlugin('fancybox');
                    $this->addJS('address.js');

                    // TNTOfficiel_inflate(), TNTOfficiel_deflate(), required by carrierDeliveryPoint.js
                    $this->addJS('lib/string.js');
                    // jQuery.fn.nanoScroller, required by carrierDeliveryPoint.js
                    $this->addJS('lib/nanoscroller/jquery.nanoscroller.min.js');
                    $this->addCSS($strAssetJSPath . 'lib/nanoscroller/nanoscroller.css', 'all');

                    $this->addJS('carrierDeliveryPoint.js');
                    $this->addJS('carrierAdditionalInfo.js');
                    $this->addJS('AdminOrder.js');
                }
                break;
            // Back-Office Carrier Wizard.
            case 'admincarrierwizardcontroller':
                $this->addJS('AdminCarrierWizard.js');
                break;
            case 'adminaddressescontroller':
                // Form.css required for address-city-check, ExtraData
                $this->addCSS('form.css', 'all');

                // FancyBox required to display form (cp/ville check).
                $objAdminController->addJqueryPlugin('fancybox');
                $this->addJS('address.js');
                break;
            default:
                // Update All Parcels and OrderState accordingly.
                TNTOfficielOrder::updateAllOrderStateDeliveredParcels();
                break;
        }

        $arrJSONTNTOfficiel = $this->getCommonVariable();
        $arrJSONTNTOfficiel['link']['back'] = array(
            'module' => array(
                /* Account settings. */
                'selectPostcodeCities' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'selectPostcodeCities',
                        'ajax' => 'true',
                    )
                ),
                'updateHRA' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielAccount',
                    true,
                    array(),
                    array(
                        'action' => 'updateHRA',
                        'ajax' => 'true',
                    )
                ),
                /* Order detail. */
                'checkShippingDateValidUrl' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'checkShippingDateValid',
                        'ajax' => 'true',
                    )
                ),
                'updateOrderParcels' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'updateOrderParcels',
                        'ajax' => 'true',
                    )
                ),
                // common displayAjaxStoreReceiverInfo
                'storeReceiverInfo' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'storeReceiverInfo',
                        'ajax' => 'true',
                    )
                ),
                // common displayAjaxBoxDeliveryPoints
                'boxDeliveryPoints' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'boxDeliveryPoints',
                        'ajax' => 'true',
                    )
                ),
                // common displayAjaxSaveProductInfo
                'saveProductInfo' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'saveProductInfo',
                        'ajax' => 'true',
                    )
                ),
                // common displayAjaxGetCities
                'getAddressCities' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'getCities',
                        'ajax' => 'true',
                    )
                ),
                // common displayAjaxUpdateDeliveryAddress
                'updateAddressDelivery' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'updateDeliveryAddress',
                        'ajax' => 'true',
                    )
                ),
                /* Customer Address detail. */
                // common displayAjaxCheckPostcodeCity
                'checkAddressPostcodeCity' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'checkPostcodeCity',
                        'ajax' => 'true',
                    )
                ),
            ),
        );

        $arrJSONTNTOfficiel['translate']['back'] = $this->getTranslation(true);

        // Init once.
        if (!array_key_exists('alert', $arrJSONTNTOfficiel)
            || !is_array($arrJSONTNTOfficiel['alert'])
        ) {
            $arrJSONTNTOfficiel['alert'] = array(
                'error' => array(),
                'warning' => array(),
                'info' => array(),
                'success' => array(),
            );
        }

        // Cookie TNTOfficielError is used to display error message once after redirect.
        if (!empty($objHookCookie->TNTOfficielError)) {
            // Add error message to the admin page if exists.
            $arrJSONTNTOfficiel['alert']['error'][] = $objHookCookie->TNTOfficielError;
            // Delete cookie.
            $objHookCookie->TNTOfficielError = null;
        }
        if (!empty($objHookCookie->TNTOfficielWarning)) {
            // Add error message to the admin page if exists.
            $arrJSONTNTOfficiel['alert']['warning'][] = $objHookCookie->TNTOfficielWarning;
            // Delete cookie.
            $objHookCookie->TNTOfficielWarning = null;
        }
        if (!empty($objHookCookie->TNTOfficielSuccess)) {
            // Add error message to the admin page if exists.
            $arrJSONTNTOfficiel['alert']['success'][] = $objHookCookie->TNTOfficielSuccess;
            // Delete cookie.
            $objHookCookie->TNTOfficielSuccess = null;
        }

        // Add TNTOfficiel global variable with others in main inline script.
        Media::addJsDef(array('TNTOfficiel' => $arrJSONTNTOfficiel));

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'ajax' => $objAdminController->ajax,
                'controller_type' => $objAdminController->controller_type,
                'controllername' => $strCurrentControllerName,
                'controllerfilename' => Dispatcher::getInstance()->getController(),
            )
        );
    }

    /**
     * HOOK (AKA Header) displayed in head tag on Front-Office.
     *
     * @param array $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayHeader($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;
        // Controller.
        $objFrontController = $objContext->controller;
        // Get Controller Name.
        $strCurrentControllerName = TNTOfficiel_Tools::getControllerName($objFrontController);

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // Display nothing.
            return '';
        }

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        // If no account available for current shop context, or is not authenticated.
        if ($objTNTContextAccountModel === null
            || $objTNTContextAccountModel->getAuthValidatedDateTime() === null
        ) {
            // Display nothing.
            return '';
        }

        $arrJSONTNTOfficiel = $this->getCommonVariable();
        // Add TNTOfficiel global variable with others in main inline script.
        Media::addJsDef(array('TNTOfficiel' => $arrJSONTNTOfficiel));

        // Google Font: Open Sans.
        $this->addCSS('https://fonts.googleapis.com/css?family=Open+Sans:400,700', 'all');

        $strAssetJSPath = TNTOfficiel::getFolderModule('js');

        $this->addCSS('global.css', 'all');
        $this->addJS('global.js');

        // Switch Controller Name.
        switch ($strCurrentControllerName) {
            // Front-Office Order History +guest.
            case 'orderdetailcontroller':
            case 'guesttrackingcontroller':
                // Form.css required for displayOrderDetail.tpl
                $this->addCSS('form.css', 'all');
                break;
            // Front-Office Address.
            case 'addresscontroller':
                // Front-Office Guest Checkout Address.
            case 'authcontroller':
                // Form.css required for address-city-check, ExtraData
                $this->addCSS('form.css', 'all');

                // FancyBox required to display form (cp/ville check).
                $objFrontController->addJqueryPlugin('fancybox');
                $this->addJS('address.js');
                break;

            // Front-Office Cart Process.
            case 'ordercontroller':
                // form.css required for address-city-check.
                $this->addCSS('form.css', 'all');
                // receiver.css for extradata.
                $this->addCSS('receiver.css', 'all');
                //
                $this->addCSS('carrier.css', 'all');

                // Prestashop Validation system.
                $this->addJS(_PS_JS_DIR_ . 'validate.js');

                // FancyBox required to display form (cp/ville check).
                $objFrontController->addJqueryPlugin('fancybox');
                $this->addJS('address.js');

                // TNTOfficiel_inflate(), TNTOfficiel_deflate(), required by carrierDeliveryPoint.js
                $this->addJS('lib/string.js');
                // jQuery.fn.nanoScroller, required by carrierDeliveryPoint.js
                $this->addJS('lib/nanoscroller/jquery.nanoscroller.min.js');
                $this->addCSS($strAssetJSPath . 'lib/nanoscroller/nanoscroller.css', 'all');

                $this->addJS('carrierDeliveryPoint.js');
                $this->addJS('carrierAdditionalInfo.js');
                // TNTOfficiel_deliveryPointsBox, used in displayAjaxBoxDeliveryPoints.tpl
                $this->addJS('carrier.js');
                break;

            default:
                break;
        }

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'ajax' => $objFrontController->ajax,
                'controller_type' => $objFrontController->controller_type,
                'controllername' => $strCurrentControllerName,
                'controllerfilename' => Dispatcher::getInstance()->getController(),
                'js' => $arrJSONTNTOfficiel,
            )
        );

        // Display nothing.
        return '';
    }

    /**
     * HOOK action<ClassName><Action>Before in AdminController->postProcess()
     * <ClassName> : 'AdminCartsController'
     * <Action> : ''
     *
     * BO Fix for DeliveryOption before ajaxProcess.
     *
     * Hook::exec('action' . get_class($this) . ucfirst($this->action) . 'Before', array('controller' => $this));
     *
     * @param $arrArgHookParams
     *
     * @return void
     */
    public function hookActionAdminCartsControllerBefore($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \AdminController $objAdminController */
        $objAdminController = $arrArgHookParams['controller'];
        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        /** @var \Cookie $objHookCookie */
        //$objHookCookie = $arrArgHookParams['cookie'];

        //
        if (!$objAdminController->ajax) {
            return;
        }

        $strAction = Tools::getValue('action');
        if (empty($strAction)) {
            return;
        }

        $strAction = Tools::strtolower($strAction);
        if (!method_exists($objAdminController, 'ajaxProcess' . $strAction)) {
            return;
        }

        if ($strAction === 'updatedeliveryoption') {
            $objHookCart->setDeliveryOption($objHookCart->getDeliveryOption(null, false, false));
            $objHookCart->save();
        }
    }

    /**
     * HOOK (AKA beforeCarrier) displayed before the carrier list on Front-Office.
     *
     * @param array $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayBeforeCarrier($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        $intCartID = (int)$objHookCart->id;

        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        $objContext = $this->context;

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // Display nothing.
            return '';
        }

        // Force $objHookCart->id_carrier Update using autoselect if not set (without using cache).
        // Known issue: $objHookCart->id_carrier maybe incorrectly set
        // when autoselection determine current selected carrier.
        // e.g: only one core carrier available, input radio is always already preselected,
        // but not $objHookCart->id_carrier since setDeliveryOption() was not used (and no change is possible).
        $objHookCart->setDeliveryOption($objHookCart->getDeliveryOption(null, false, false));
        $objHookCart->save();

        // Get delivery options.
        $arrDeliveryOption = $objHookCart->getDeliveryOption();
        // Exclude unset Delivery Address in DeliveryOption.
        unset($arrDeliveryOption[0]);

        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);

        // If at least one option, but selected address not found in delivery option !
        // Known issue: delivery option with a deleted address ID, but with id_address_delivery using a valid ID.
        // In this case, no carrier are available for cart, products must be re-added to unfreeze.
        if (count($arrDeliveryOption) > 0
            // Selected Delivery Address is set
            && $objHookCart->id_address_delivery > 0
            // Selected Delivery Address does not exist in DeliveryOption
            && !array_key_exists($objHookCart->id_address_delivery, $arrDeliveryOption)
        ) {
            $objException = new Exception(
                sprintf(
                    'Delivery address ID %s was not found in delivery option from cart ID %s.'
                    . ' Recreating cart products association',
                    $objHookCart->id_address_delivery,
                    $objHookCart->id
                )
            );
            TNTOfficiel_Logger::logException($objException);

            // Re-add cart products in ps_cart_product to fix id_address_delivery association.
            foreach ($objHookCart->getProducts() as $arrProduct) {
                $objHookCart->updateQty(
                    0,
                    $arrProduct['id_product'],
                    $arrProduct['id_product_attribute']
                );
                $objHookCart->updateQty(
                    (int)$arrProduct['cart_quantity'],
                    $arrProduct['id_product'],
                    $arrProduct['id_product_attribute']
                );
            }

            // Flush checkout state.
            $objTNTCartModel->flushCheckoutState();
            // Redirect to step 1.
            Tools::redirect($objContext->link->getPageLink('order', true, null, array('step' => '1')));

            // Display nothing.
            return '';
        }

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        // If no account available for current shop context, or is not authenticated.
        if ($objTNTContextAccountModel === null
            || $objTNTContextAccountModel->getAuthValidatedDateTime() === null
        ) {
            // Display nothing.
            return '';
        }

        $boolCityPostCodeIsValid = true;

        $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($objHookCart->id_address_delivery);
        // If delivery Address object is available.
        if ($objPSAddressDelivery !== null) {
            $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddressDelivery->id_country);
            $strReceiverZipCode = trim($objPSAddressDelivery->postcode);
            $strReceiverCity = trim($objPSAddressDelivery->city);

            // Check the city/postcode.
            $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide(
                $strReceiverCountryISO,
                $strReceiverZipCode,
                $strReceiverCity
            );
            // Unsupported country or communication error is considered true to prevent
            // always invalid address form and show error "unknow postcode" on Front-Office checkout.
            $boolCityPostCodeIsValid = (!$arrResultCitiesGuide['boolIsCountrySupported']
                || $arrResultCitiesGuide['boolIsRequestComError']
                || $arrResultCitiesGuide['boolIsCityNameValid']
            );
        }

        $strTNTPaymentReadyError = null;
        if (!empty($objHookCookie->TNTPaymentReadyError)) {
            $strTNTPaymentReadyError = TNTOfficiel::getCodeTranslate($objHookCookie->TNTPaymentReadyError);
        }
        $objHookCookie->TNTPaymentReadyError = null;

        $this->smarty->assign(
            array(
                'boolCityPostCodeIsValid' => $boolCityPostCodeIsValid,
                'linkAddress' => $objContext->link->getPageLink('address', true),
                'id_address_delivery' => (int)$objHookCart->id_address_delivery,
                'strTNTPaymentReadyError' => $strTNTPaymentReadyError,
            )
        );

        // Display template.
        return $this->fetchTemplate('hook/displayBeforeCarrier.tpl');
    }

    /**
     * HOOK called after the list of available carriers, during the order process.
     * Ideal location to add a carrier, as added by a module.
     * Display TNT products during the order process.
     * (displayCarrierList AKA extraCarrier is deprecated).
     *
     * @param array $arrArgHookParams array
     *
     * @return string
     */
    public function hookDisplayAfterCarrier($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        $intCartID = (int)$objHookCart->id;

        $intCarrierID = (int)$objHookCart->id_carrier;
        //$intAddressIDDelivery = (int)$objHookCart->id_address_delivery;
        $intCustomerID = (int)$objHookCart->id_customer;

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // Display nothing.
            return '';
        }

        // Prevent AJAX bug with carrier ID inconsistency.
        $objHookCart->save();

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        // If no account available for current shop context, or is not authenticated.
        if ($objTNTContextAccountModel === null
            || $objTNTContextAccountModel->getAuthValidatedDateTime() === null
        ) {
            // Display nothing.
            return '';
        }

        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        // If fail.
        if ($objTNTCartModel === null) {
            // Display nothing.
            return '';
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);

        // Default fields without receiver infos (used by template).
        $arrFormReceiverInfoValidate = TNTOfficielReceiver::validateReceiverInfo();
        $arrFormReceiverInfoValidate['errors'] = array();
        $arrFormReceiverInfoValidate['length'] = 0;

        $strExtraAddressDataValid = 'false';
        // A delivery address is optional.
        $objPSAddressDelivery = $objTNTCartModel->getPSAddressDelivery();
        $objPseudoAddressDelivery = null;
        // If delivery Address object is available.
        if ($objPSAddressDelivery !== null) {
            $objPseudoAddressDelivery = (object)array(
                'company' => $objPSAddressDelivery->company,
                'id_country' => $objPSAddressDelivery->id_country,
                'postcode' => trim($objPSAddressDelivery->postcode),
                'city' => trim($objPSAddressDelivery->city),
            );
            // Get postcode from delivery point.
            if ($objTNTCartModel->hasDeliveryPoint($intCarrierID)) {
                $arrDeliveryPoint = $objTNTCartModel->getDeliveryPoint($intCarrierID);
                if (array_key_exists('postcode', $arrDeliveryPoint)) {
                    $objPseudoAddressDelivery->postcode = trim($arrDeliveryPoint['postcode']);
                }
                if (array_key_exists('city', $arrDeliveryPoint)) {
                    $objPseudoAddressDelivery->city = trim($arrDeliveryPoint['city']);
                }
            }

            $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddressDelivery->id_country);
            // Only FR country is supported.
            if ($strReceiverCountryISO === 'FR') {
                // Load TNT receiver info or create a new one for its ID.
                $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($objPSAddressDelivery->id);
                // If success.
                if ($objTNTReceiverModel !== null) {
                    $objPSCustomer = TNTOfficielReceiver::getPSCustomerByID($intCustomerID);
                    $strAddressPhone = TNTOfficielReceiver::searchPhone($objPSAddressDelivery);

                    $strCustomerEMail = null;
                    // If Customer object available.
                    if ($objPSCustomer !== null) {
                        $strCustomerEMail = $objPSCustomer->email;
                    }

                    // Validate and store receiver info, using the customer email
                    // and address mobile phone as default values.
                    $arrFormReceiverInfoValidate = $objTNTReceiverModel->storeReceiverInfo(
                        $objTNTReceiverModel->receiver_email ? $objTNTReceiverModel->receiver_email : $strCustomerEMail,
                        $objTNTReceiverModel->receiver_mobile ? $objTNTReceiverModel->receiver_mobile : $strAddressPhone,
                        $objTNTReceiverModel->receiver_building,
                        $objTNTReceiverModel->receiver_accesscode,
                        $objTNTReceiverModel->receiver_floor,
                        $objTNTReceiverModel->receiver_instructions
                    );

                    $strExtraAddressDataValid = $arrFormReceiverInfoValidate['stored'] ? 'true' : 'false';
                }
            }
        }

        // Get the carriers model list using the heaviest product weight from cart.
        $arrObjTNTCarrierModelList = TNTOfficielCarrier::getLiveFeasibilityContextCarrierModelList(
            $objTNTCartModel->getCartHeaviestProduct(),
            $objPseudoAddressDelivery
        );

        $arrDump = array();
        /*
        if (TNTOfficiel_Logstack::isReady() === true) {
            $arrDump = array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'intCarrierID' => $intCarrierID,
                'objTNTCartModel' => array(
                    'objTNTCartModel' => $objTNTCartModel,
                    'getDeliveryOption' => $objTNTCartModel->getDeliveryOption(),
                    'isMultiShippingSupport' => $objTNTCartModel->isMultiShippingSupport(),
                    'CartTotalWeight' => $objTNTCartModel->getCartTotalWeight(), // Kg
                    'CartHeaviestProduct' => $objTNTCartModel->getCartHeaviestProduct(), // Kg
                    'CartTotalPrice' => $objTNTCartModel->getCartTotalPrice(), // € TTC
                    'isPaymentReady' => $objTNTCartModel->isPaymentReady(),
                ),
                'objHookCart' => array(
                    'objHookCart' => $objHookCart,
                    'getDeliveryOption' => $objHookCart->getDeliveryOption(),
                    'getDeliveryOptionList' => TNTOfficiel_Tools::dumpSafe(
                        $objHookCart->getDeliveryOptionList(null, true),
                        1048576 * 10,
                        8
                    ),
                    'getPackageList' => TNTOfficiel_Tools::dumpSafe(
                        $objHookCart->getPackageList(true),
                        1048576,
                        6
                    ),
                ),
            );
            //TNTOfficiel_Logstack::dump($arrDump);
        }*/

        $this->smarty->assign(
            array(
                'arrObjTNTCarrierModelList' => $arrObjTNTCarrierModelList,
                'arrDeliveryOption' => $objTNTCartModel->getDeliveryOption(),
                'strCarrierTypeSelected' =>
                    $objTNTCarrierModel === null ? null : ($objTNTCarrierModel->carrier_type),
                'arrFormReceiverInfoValidate' => $arrFormReceiverInfoValidate,
                'strExtraAddressDataValid' => $strExtraAddressDataValid,
                'arrDump' => $arrDump,
            )
        );

        // Display template.
        return $this->fetchTemplate('hook/displayAfterCarrier.tpl');
    }

    /**
     * HOOK called to display extra content of an available carriers when selected.
     *
     * @param array $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayCarrierExtraContent($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        $intCartID = (int)$objHookCart->id;

        $arrHookCarrier = $arrArgHookParams['carrier'];
        $intCarrierID = (int)$arrHookCarrier['id'];

        // If is not a TNT carrier.
        if (!TNTOfficielCarrier::isTNTOfficielCarrierID($intCarrierID)) {
            // Display nothing.
            return '';
        }

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // Display nothing.
            return '';
        }

        // Load TNT cart info or create a new one for its ID.
        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        // If fail.
        if ($objTNTCartModel === null) {
            // Display nothing.
            return '';
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        // If fail.
        if ($objTNTCarrierModel === null) {
            // Display nothing.
            return '';
        }

        $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();

        // If no account available for this carrier, or is not authenticated.
        if ($objTNTCarrierAccountModel === null
            || $objTNTCarrierAccountModel->getAuthValidatedDateTime() === null
        ) {
            // Display nothing.
            return '';
        }

        /*
         * Estimated delivery date.
         */

        $arrLiveFeasibility = null;
        $strDueDate = null;

        // A delivery address is optional.
        $objPSAddressDelivery = $objTNTCartModel->getPSAddressDelivery();
        $objAddressDelivery = null;
        $strReceiverPostCode = null;
        $strReceiverCity = null;
        // If delivery Address object is available.
        if ($objPSAddressDelivery !== null) {
            $objAddressDelivery = (object)array(
                'company' => $objPSAddressDelivery->company,
                'id_country' => $objPSAddressDelivery->id_country,
                'postcode' => trim($objPSAddressDelivery->postcode),
                'city' => trim($objPSAddressDelivery->city),
            );
            // Get postcode from delivery point.
            if ($objTNTCartModel->hasDeliveryPoint($intCarrierID)) {
                $arrDeliveryPoint = $objTNTCartModel->getDeliveryPoint($intCarrierID);
                if (array_key_exists('postcode', $arrDeliveryPoint)) {
                    $objAddressDelivery->postcode = trim($arrDeliveryPoint['postcode']);
                }
                if (array_key_exists('city', $arrDeliveryPoint)) {
                    $objAddressDelivery->city = trim($arrDeliveryPoint['city']);
                }
            }
            $strReceiverPostCode = $objAddressDelivery->postcode;
            $strReceiverCity = $objAddressDelivery->city;
        }

        // If delivery Address object is available.
        if ($objPSAddressDelivery !== null) {
            $arrLiveFeasibility = $objTNTCarrierModel->liveFeasibility(
                $strReceiverPostCode,
                $strReceiverCity,
                $objTNTCarrierModel->getReceiverType($objPSAddressDelivery)
            );
            if (is_array($arrLiveFeasibility)) {
                $strDueDate = $arrLiveFeasibility['dueDate'];
            }
        }

        $arrDump = array();
        /*
        if (TNTOfficiel_Logstack::isReady() === true) {
            $arrDump = array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'intCarrierID' => $intCarrierID,
                'Account' => $objTNTCarrierAccountModel,
                'getMaxPackageWeight' => $objTNTCarrierModel->getMaxPackageWeight(), // Kg
                'LiveFeasibility' => $arrLiveFeasibility,
                'ZonesConf' => $objTNTCarrierModel->getZonesConf(),
                'CartShippingFree' => $objTNTCartModel->isCartShippingFree($intCarrierID),
                'ExtraShippingCost' => $objTNTCartModel->getCartExtraShippingCost($intCarrierID), // € HT
                // TNT.
                'getPrice' => $objTNTCarrierModel->getPrice(
                    $objTNTCartModel->getCartTotalWeight(),
                    $objTNTCartModel->getCartTotalPrice(),
                    $strReceiverPostCode
                ), // € HT
                // TNT Custom.
                'getCustomCartCarrierPrice' => method_exists($this, 'getCustomCartCarrierPrice') ?
                    $this->getCustomCartCarrierPrice($objTNTCartModel, $objTNTCarrierModel) : null,
                // PS Final.
                'carrier_tax' => $objTNTCarrierModel->getPSCarrier()->getTaxesRate($objTNTCartModel->getPSAddressDelivery()),
                'total_shipping_tax_excl' => (float)$objHookCart->getPackageShippingCost($intCarrierID, false, null, null),
                'total_shipping_tax_incl' => (float)$objHookCart->getPackageShippingCost($intCarrierID, true, null, null),
            );
            //TNTOfficiel_Logstack::dump($arrDump);
        }*/

        $this->smarty->assign(
            array(
                'objTNTCarrierModel' => $objTNTCarrierModel,
                'strDueDate' => $objTNTCarrierAccountModel->delivery_display_edd ? $strDueDate : null,
                'deliveryPoint' => $objTNTCartModel->getDeliveryPoint($intCarrierID),
                'arrDump' => $arrDump,
            )
        );

        // Display template.
        return $this->fetchTemplate('hook/displayCarrierExtraContent.tpl');
    }

    /**
     * HOOK 1.7.1+ called when button continue is submitted (confirmDeliveryOption) on delivery step.
     * Check if state for a selected carrier of this module is completed.
     * https://github.com/PrestaShop/PrestaShop/commit/895255fd61b9cdf77e4e6096ef076b5149d884a4
     *
     * @param array $arrArgHookParams
     *
     * @return bool
     */
    public function hookActionValidateStepComplete($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        $intCartID = (int)$objHookCart->id;
        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        // Load TNT cart info or create a new one for its ID.
        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        if ($objTNTCartModel !== null) {
            $arrResult = $objTNTCartModel->isPaymentReady();
            // Set to true if completed.
            $arrArgHookParams['completed'] = !array_key_exists('error', $arrResult) || !is_string($arrResult['error']);
            // Store error message to display later after redirect in BeforeCarrier Hook.
            if (array_key_exists('error', $arrResult) && is_string($arrResult['error'])) {
                $objHookCookie->TNTPaymentReadyError = $arrResult['error'];
            }
        }

        return true;
    }

    /**
     * HOOK (AKA newOrder) called during the new order creation process, right after it has been created.
     * Called from /classes/PaymentModule.php
     *
     * Create XETT/PEX address if required and create parcels.
     *
     * @param $arrArgHookParams array
     *
     * @return bool
     */
    public function hookActionValidateOrder($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cart $objHookCart */
        $objHookCart = $arrArgHookParams['cart'];
        //$intCartID = (int)$objHookCart->id;

        /** @var \Order $objHookOrder */
        $objHookOrder = $arrArgHookParams['order'];
        $intHookOrderID = (int)$objHookOrder->id;

        //$objHookCustomer = $arrArgHookParams['customer'];
        //$objHookCurrency = $arrArgHookParams['currency'];
        //$objHookOrderStatus = $arrArgHookParams['orderStatus'];

        // Log TNT carrier ID change during order creation.
        if ((TNTOfficielCarrier::isTNTOfficielCarrierID($objHookCart->id_carrier)
                || TNTOfficielCarrier::isTNTOfficielCarrierID($objHookOrder->id_carrier))
            && ((int)$objHookCart->id_carrier !== (int)$objHookOrder->id_carrier)
        ) {
            $objException = new Exception(
                sprintf(
                    'Carrier ID change during order creation from %s in cart to %s in order',
                    $objHookCart->id_carrier,
                    $objHookOrder->id_carrier
                )
            );
            TNTOfficiel_Logger::logException($objException);
        }

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            // Do not have to save this cart.
            return false;
        }

        // Load TNT order info or create a new one for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, true);
        // If fail.
        if ($objTNTOrderModel === null) {
            return false;
        }

        // Get the selected TNT Carrier object from Order.
        $objTNTCarrierModel = $objTNTOrderModel->getTNTCarrierModel();
        // If fail.
        if ($objTNTCarrierModel === null) {
            return false;
        }

        $intCarrierID = (int)$objTNTCarrierModel->id_carrier;

        // Load TNT cart info or create a new one for its ID.
        $objTNTCartModel = $objTNTOrderModel->getTNTCartModel();
        // If fail.
        if ($objTNTCartModel === null) {
            return false;
        }

        // Creates parcels for order.
        $objTNTOrderModel->createParcels();

        // If delivery point selected for carrier.
        if ($objTNTCartModel->hasDeliveryPoint($intCarrierID)) {
            $arrDeliveryPoint = $objTNTCartModel->getDeliveryPoint($intCarrierID);
            // Copy Delivery Point from cart to order and create a new address.
            $mxdNewIDAddressDelivery = $objTNTOrderModel->setDeliveryPoint($arrDeliveryPoint);
            if (is_int($mxdNewIDAddressDelivery) && $mxdNewIDAddressDelivery > 0) {
                // Bind again for Hook.
                $objHookOrder->id_address_delivery = $mxdNewIDAddressDelivery;
            } else {
                $objException = new Exception(
                    sprintf(
                        'Error while binding new Address #%s from %s delivery point for Order #%s',
                        $mxdNewIDAddressDelivery,
                        $objTNTCarrierModel->carrier_type,
                        $intHookOrderID
                    )
                );
                TNTOfficiel_Logger::logException($objException);
            }

            // Save TNT order.
            $objTNTOrderModel->save();
        }

        // Update shipping date if available.
        $objTNTOrderModel->updatePickupDate();

        return true;
    }

    /**
     * HOOK (AKA adminOrder) called when the order's details are displayed, below the Client Information block.
     * Parcel management for orders with a TNT carrier.
     *
     * @param array $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayAdminOrder($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        $intHookOrderID = (int)$arrArgHookParams['id_order'];

        $objContext = $this->context;
        // Controller.
        $objAdminController = $objContext->controller;

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            // Display nothing.
            return '';
        }

        // Load TNT order info or create a new one for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, true);
        // If TNTOrder object not available or created.
        if ($objTNTOrderModel === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('Unable to load or create TNT Order for Order #%s'),
                    $intHookOrderID
                )
            );

            // Display nothing.
            return '';
        }

        $objPSOrder = $objTNTOrderModel->getPSOrder();
        // If Order object not available.
        if ($objPSOrder === null) {
            // Display nothing.
            return '';
        }

        // Prevent Prestahop bugs without override.
        // forge.prestashop.com/browse/BOOM-4050
        // forge.prestashop.com/browse/BOOM-5821
        if (version_compare(_PS_VERSION_, '1.7.7', '<')
            && Shop::getContext() !== Shop::CONTEXT_SHOP
        ) {
            // Change context to order shop.
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'AdminOrders',
                    true,
                    array(),
                    array(
                        'id_order' => $objPSOrder->id,
                        'vieworder' => 1,
                        'setShopContext' => 's-' . $objPSOrder->id_shop,
                    )
                )
            );
        }

        // Get the selected TNT Carrier object from Order.
        $objTNTCarrierModel = $objTNTOrderModel->getTNTCarrierModel();
        // If fail.
        if ($objTNTCarrierModel === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('Unable to load TNT Carrier #%s'),
                    $objPSOrder->id_carrier
                )
            );

            // Display nothing.
            return '';
        }

        $objTNTCarrierAccountModel = $objTNTOrderModel->getTNTAccountModel();
        // If no account available for this order's carrier.
        if ($objTNTCarrierAccountModel === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('Unable to load TNT Account for Carrier #%s'),
                    $objPSOrder->id_carrier
                )
            );

            // Display nothing.
            return '';
        }

        $objPSAddressDelivery = $objTNTOrderModel->getPSAddressDelivery();
        // If no address available for this order.
        if ($objPSAddressDelivery === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('Unable to load Address #%s'),
                    $objPSOrder->id_address_delivery
                )
            );

            // Display nothing.
            return '';
        }

        // If account is not authenticated.
        if ($objTNTCarrierAccountModel->getAuthValidatedDateTime() === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('TNT Account is not authenticated for Account #%s'),
                    $objTNTCarrierAccountModel->id
                )
            );

            // Display nothing.
            return '';
        }

        $boolDirectAddressCheck = false;

        $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddressDelivery->id_country);
        $strReceiverZipCode = trim($objPSAddressDelivery->postcode);
        $strReceiverCity = trim($objPSAddressDelivery->city);
        $boolIsReceiverB2B = !!trim($objPSAddressDelivery->company);

        // If expedition not already created.
        if (!$objTNTOrderModel->isExpeditionCreated()) {
            // Is carrier already available for account ?
            $arrFeasibilityAllCarrierType = $objTNTCarrierAccountModel->availabilities();

            $strCarrierID = implode(
                ':',
                array(
                    $objTNTCarrierModel->account_type,
                    $objTNTCarrierModel->carrier_type,
                    $objTNTCarrierModel->carrier_code1,
                    $objTNTCarrierModel->carrier_code2,
                )
            );

            // If no communication error and carrier service is not available.
            if (!$arrFeasibilityAllCarrierType['boolIsRequestComError']
                && !array_key_exists($strCarrierID, $arrFeasibilityAllCarrierType['arrTNTServiceList'])
            ) {
                $this->displayAdminError(
                    sprintf(
                        $this->l('Current TNT Carrier is no more available on TNT Account %s.'),
                        $objTNTCarrierAccountModel->account_number
                    ) . ' ' . $this->l('Please replace it to allow expedition creation.') . ' '
                    . $this->l('In the ORDER section, DELIVERY tab, MODIFY the Carrier.')
                );

                // Display nothing.
                return '';
            }

            // Is carrier available for address ?
            $boolIsAvailable = $objTNTCarrierModel->isAvailableForReceiverType($boolIsReceiverB2B);
            if (!$boolIsAvailable) {
                $this->displayAdminError(
                    sprintf(
                        $this->l('Delivery address is %s, but not the carrier.'),
                        $boolIsReceiverB2B ? $this->l('B2B') : $this->l('B2C')
                    ) . ' ' . $this->l('Please verify "Company" field in delivery address.') . ' '
                    . $this->l('Otherwise, you can also replace current carrier.') . ' '
                    . $this->l('In the ORDER section, DELIVERY tab, MODIFY the Carrier.')
                );
            }

            // Is address zipcode or city valid ?
            $arrResultCitiesGuideReceiver = $objTNTCarrierAccountModel->citiesGuide(
                $strReceiverCountryISO,
                $strReceiverZipCode,
                $strReceiverCity
            );
            // If the country is not supported
            // or the city does not match the postcode for the delivery address (without communication error).
            if (!$arrResultCitiesGuideReceiver['boolIsCountrySupported']
                || (!$arrResultCitiesGuideReceiver['boolIsRequestComError']
                    && !$arrResultCitiesGuideReceiver['boolIsCityNameValid']
                )
            ) {
                $boolDirectAddressCheck = true;
                $this->displayAdminError(
                    sprintf(
                        $this->l('Unrecognized zipcode or city in delivery Address #%s'),
                        $objPSOrder->id_address_delivery
                    )
                );
            }
        }

        // Load TNT Receiver info or create a new one for its ID.
        $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($objPSOrder->id_address_delivery);
        // If fail.
        if ($objTNTReceiverModel === null) {
            $this->displayAdminError(
                sprintf(
                    $this->l('Unable to load or create TNT Receiver for Address #%s'),
                    $objPSOrder->id_address_delivery
                )
            );

            // Display nothing.
            return '';
        }

        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            $strAssetJSPath = TNTOfficiel::getFolderModule('js');

            // Form.css required for address-city-check, ExtraData
            $this->addCSS('form.css', 'all');
            //
            $this->addCSS('carrier.css', 'all');

            // FancyBox required to display form (cp/ville check).
            $objAdminController->addJqueryPlugin('fancybox');
            $this->addJS('address.js');

            // TNTOfficiel_inflate(), TNTOfficiel_deflate(), required by carrierDeliveryPoint.js
            $this->addJS('lib/string.js');
            // jQuery.fn.nanoScroller, required by carrierDeliveryPoint.js
            $this->addJS('lib/nanoscroller/jquery.nanoscroller.min.js');
            $this->addCSS($strAssetJSPath . 'lib/nanoscroller/nanoscroller.css', 'all');

            $this->addJS('carrierDeliveryPoint.js');
            $this->addJS('carrierAdditionalInfo.js');
            $this->addJS('AdminOrder.js');

            // Remove script load of API Google Map to prevent conflicts.
            // Removed in this hook triggered after the setMedia to catch parent class script addition.
            foreach ($objAdminController->js_files as $key => $jsFile) {
                if (preg_match('/^((https?:)?\/\/)?maps\.google(apis)?\.com\/maps\/api\/js/ui', $jsFile)) {
                    unset($objAdminController->js_files[$key]);
                }
            }
            // Load once using TNTOfficel module API key.
            $this->addJS(
                'https://maps.googleapis.com/maps/api/js?v=' . TNTOfficiel::GMAP_API_VER . '&key='
                . $objTNTCarrierAccountModel->api_google_map_key
            );
        }

        $strPickUpNumber = $objTNTCarrierAccountModel->pickup_display_number ? $objTNTOrderModel->pickup_number : null;

        // Creates parcels for order if not already done.
        $objTNTOrderModel->createParcels();

        // If all parcels delivered and order state delivered is applied.
        if ($objTNTOrderModel->updateOrderStateDeliveredParcels() === true) {
            // Redirect to show new order state.
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'AdminOrders',
                    true,
                    array(),
                    array(
                        'id_order' => $objPSOrder->id,
                        'vieworder' => 1,
                        //'setShopContext' => 's-'.$objPSOrder->id_shop,
                    )
                )
            );
        }

        // Get the parcels.
        $arrObjTNTParcelModelList = $objTNTOrderModel->getTNTParcelModelList();

        // Check and display error about shipping date.
        if (!Tools::getIsset('submitState')) {
            // Check or update the shipping date.
            $arrResultPickupDate = $objTNTOrderModel->updatePickupDate();
            // If true error.
            if (is_string($arrResultPickupDate['strResponseMsgError'])) {
                $this->displayAdminError($arrResultPickupDate['strResponseMsgError']);
            }
            /*
            // If normal error.
            if (is_string($arrResultPickupDate['strResponseMsgWarning'])) {
                $this->displayAdminWarning($arrResultPickupDate['strResponseMsgWarning'])
            }
            */
        }

        $objDateTimeToday = new DateTime('midnight');
        $intDatePickupStart = (int)$objDateTimeToday->format('U');

        $intDatePickupShipping = TNTOfficiel_Tools::getDateTimeFormat($objTNTOrderModel->shipping_date);

        $strDueDateFormatted = '';
        $objDateTimeDue = TNTOfficiel_Tools::getDateTime($objTNTOrderModel->due_date);
        if ($objDateTimeDue !== null) {
            $strDueDateFormatted = TNTOfficiel_Tools::getDateTimeFormat($objTNTOrderModel->due_date, 'l jS F Y');
        }

        $arrDeliveryPoint = $objTNTOrderModel->getDeliveryPoint();
        $strDeliveryPointType = $objTNTOrderModel->getDeliveryPointType();
        $strDeliveryPointCode = $objTNTOrderModel->getDeliveryPointCode();

        $objPSCustomer = TNTOfficielReceiver::getPSCustomerByID((int)$objPSOrder->id_customer);
        $strAddressPhone = TNTOfficielReceiver::searchPhone($objPSAddressDelivery);
        // If mobile not found, search for fixed.
        if ($strAddressPhone === '') {
            $strAddressPhone = TNTOfficielReceiver::searchPhone($objPSAddressDelivery, true);
        }

        $strCustomerEMail = null;
        // If Customer object available.
        if ($objPSCustomer !== null) {
            $strCustomerEMail = $objPSCustomer->email;
        }

        // Validate and store receiver info, using the customer email and address mobile phone as default values.
        $arrFormReceiverInfoValidate = $objTNTReceiverModel->storeReceiverInfo(
            $objTNTReceiverModel->receiver_email ? $objTNTReceiverModel->receiver_email : $strCustomerEMail,
            $objTNTReceiverModel->receiver_mobile ? $objTNTReceiverModel->receiver_mobile : $strAddressPhone,
            $objTNTReceiverModel->receiver_building,
            $objTNTReceiverModel->receiver_accesscode,
            $objTNTReceiverModel->receiver_floor,
            $objTNTReceiverModel->receiver_instructions
        );

        $strBTLabelName = '';
        if ($objTNTOrderModel->isExpeditionCreated()) {
            // Load an existing TNT label info.
            $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intHookOrderID, false);
            // If success.
            if ($objTNTLabelModel !== null) {
                $strBTLabelName = $objTNTLabelModel->getLabelName();
            }
        }

        $strPickupDateRegistered = null;
        // If expedition already created.
        if ($objTNTOrderModel->isExpeditionCreated()) {
            $boolIsPickupDateRegistered = TNTOfficielPickup::isDateRegistered(
                $objTNTCarrierModel->id_shop,
                $objTNTCarrierAccountModel->account_number,
                TNTOfficiel_Tools::getDateTimeFormat($objTNTOrderModel->shipping_date, 'Y-m-d')
            );
            if ($boolIsPickupDateRegistered) {
                $strPickupDateRegistered = TNTOfficiel_Tools::getDateTimeFormat(
                    $objTNTOrderModel->shipping_date,
                    'l jS F Y'
                );
            }
        }

        if (!$objTNTOrderModel->isExpeditionCreated()) {
            if (is_string($strDeliveryPointType) && $strDeliveryPointCode === null) {
                $this->displayAdminError(
                    $this->l('This order must be finalized for expedition creation.') . ' '
                    . $this->l('In the CLIENT section, DELIVERY ADDRESS tab, SELECT a delivery point.')
                );
            }
            if ($arrFormReceiverInfoValidate['length'] !== 0) {
                $this->displayAdminError(
                    $this->l('This order must be finalized for expedition creation.') . ' '
                    . $this->l('In the CUSTOMER section, DELIVERY ADDRESS tab, CONFIRM the ADDITIONAL INFORMATION form.')
                );
            }
        }

        $arrAdminMessageList = array(
            'error' => $this->context->controller->errors,
            'warning' => $this->context->controller->warnings,
            'info' => $this->context->controller->informations,
            'success' => $this->context->controller->confirmations,
        );

        foreach ($arrAdminMessageList as $ktype => $varr) {
            foreach ($varr as $kn => $strMessage) {
                $arrAdminMessageList[$ktype][$kn] = htmlspecialchars_decode($strMessage, ENT_QUOTES);
            }
        }

        // Flush (use arrAdminMessageList).
        $this->context->controller->errors =
        $this->context->controller->warnings =
        $this->context->controller->informations =
        $this->context->controller->confirmations = array();

        $this->smarty->assign(
            array(
                'objPSOrder' => $objPSOrder,
                'objTNTOrderModel' => $objTNTOrderModel,
                'objPSAddressDelivery' => $objPSAddressDelivery,
                'strPickUpNumber' => $strPickUpNumber,
                'arrObjTNTParcelModelList' => $arrObjTNTParcelModelList,
                'intDatePickupStart' => $intDatePickupStart,
                'intDatePickupShipping' => $intDatePickupShipping,
                'strDueDateFormatted' => $strDueDateFormatted,
                'strPickupDateRegistered' => $strPickupDateRegistered,
                'boolDirectAddressCheck' => $boolDirectAddressCheck,
                'isExpeditionCreated' => (bool)$objTNTOrderModel->isExpeditionCreated(),
                'isUpdateParcelsStateAllowed' => (bool)$objTNTOrderModel->isUpdateParcelsStateAllowed(),
                'isAccountInsuranceEnabled' => (bool)$objTNTOrderModel->isAccountInsuranceEnabled(),
                'strBTLabelName' => $strBTLabelName,
                'strDeliveryPointType' => $strDeliveryPointType,
                'strDeliveryPointCode' => $strDeliveryPointCode,
                'arrFormReceiverInfoValidate' => $arrFormReceiverInfoValidate,
                'arrDeliveryPoint' => $arrDeliveryPoint,
                'boolDisplayNew' => (bool)version_compare(_PS_VERSION_, '1.7.7', '>='),
                'hrefDownloadBT' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'downloadBT',
                        'id_order' => $intHookOrderID,
                    )
                ),
                'hrefGetManifest' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'getManifest',
                        'id_order' => $intHookOrderID,
                    )
                ),
                'hrefTracking' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'tracking',
                        'ajax' => 'true',
                        'orderId' => $intHookOrderID,
                    )
                ),
                'arrAdminMessageList' => $arrAdminMessageList,
            )
        );

        // Display template.
        return $this->displayTemplate('hook/displayAdminOrder.tpl');
        //return $this->fetchTemplate('hook/displayAdminOrder.tpl');
    }

    /**
     * HOOK 1.7.7+ called when the order's details are displayed, below customer address.
     *
     * @param $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayAdminOrderSide($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        $intHookOrderID = (int)$arrArgHookParams['id_order'];

        $intHookOrderID === $intHookOrderID;

        // Display template.
        return $this->displayTemplate('hook/displayAdminOrderSide.tpl');
    }

    /**
     * HOOK 1.7.7+ called when the order's details are displayed, next to order status.
     *
     * @param array $arrArgHookParams
     *
     * @return bool
     */
    public function hookActionGetAdminOrderButtons($arrArgHookParams)
    {
        // Controller.
        //$objController = $arrArgHookParams['controller'];

        $intHookOrderID = (int)$arrArgHookParams['id_order'];
        /** @var \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButtonsCollection $backOfficeOrderButtons */
        $backOfficeOrderButtons = $arrArgHookParams['actions_bar_buttons_collection'];

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            return false;
        }

        // Load TNT order info or create a new one for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, true);
        // If fail.
        if ($objTNTOrderModel === null) {
            return false;
        }

        if ($objTNTOrderModel->isExpeditionCreated()) {
            $viewOrderUrlDownloadBT = $this->context->link->getAdminLink(
                'AdminTNTOfficielOrders',
                true,
                array(),
                array(
                    'action' => 'downloadBT',
                    'id_order' => $intHookOrderID,
                )
            );

            $strBTLabelName = '';
            // Load an existing TNT label info.
            $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intHookOrderID, false);
            // If success.
            if ($objTNTLabelModel !== null) {
                $strBTLabelName = $objTNTLabelModel->getLabelName();
            }

            $backOfficeOrderButtons->add(
                new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                    'btn-secondary' . ($objTNTOrderModel->isExpeditionCreated() ? '' : ' disabled'),
                    array('href' => $viewOrderUrlDownloadBT, 'title' => $strBTLabelName, 'target' => '_blank'),
                    $this->l('TNT Transport Ticket')
                )
            );
        } else {
            $backOfficeOrderButtons->add(
                new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                    'btn-secondary disabled',
                    array('href' => 'javascript:void(0);'),
                    $this->l('TNT Transport Ticket')
                )
            );
        }

        $viewOrderUrlGetManifest = $this->context->link->getAdminLink(
            'AdminTNTOfficielOrders',
            true,
            array(),
            array(
                'action' => 'getManifest',
                'id_order' => $intHookOrderID,
            )
        );

        $backOfficeOrderButtons->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-link',
                array('href' => $viewOrderUrlGetManifest, 'title' => $this->l('Manifest')),
                $this->l('TNT Manifest')
            )
        );

        if ($objTNTOrderModel->isExpeditionCreated()) {
            $viewOrderUrlTracking = $this->context->link->getAdminLink(
                'AdminTNTOfficielOrders',
                true,
                array(),
                array(
                    'action' => 'tracking',
                    'ajax' => 'true',
                    'orderId' => $intHookOrderID,
                )
            );

            $backOfficeOrderButtons->add(
                new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                    'btn-link',
                    array(
                        'href' => 'javascript:void(0);',
                        'onclick' => 'window.open('
                            . '\'' . $viewOrderUrlTracking . '\', '
                            . '\'Tracking\', '
                            . '\'menubar=no, scrollbars=yes, top=100, left=100, width=900, height=600\');',
                    ),
                    $this->l('TNT Tracking')
                )
            );
        } else {
            $backOfficeOrderButtons->add(
                new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                    'btn-link disabled',
                    array('href' => 'javascript:void(0);'),
                    $this->l('TNT Tracking')
                )
            );
        }

        return true;
    }

    /**
     * HOOK (AKA updateCarrier) called when a carrier is updated.
     * Updating a Carrier means preserve its previous state and adding a new one which include change using a new ID.
     *
     * @param array $arrArgHookParams
     *
     * @return bool
     */
    public function hookActionCarrierUpdate($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        $intHookCarrierIDModified = $arrArgHookParams['id_carrier'];
        /** @var \Carrier $objHookCarrierNew */
        $objHookCarrierNew = $arrArgHookParams['carrier'];

        // Update it.
        return TNTOfficielCarrier::updateCarrierID(
            $intHookCarrierIDModified,
            $objHookCarrierNew->id
        );
    }

    /**
     * Carrier module : Method triggered form Cart Model if $carrier->need_range == false.
     * Get the cart shipping price without using the ranges.
     * (best price).
     *
     * @param Cart $objArgCart
     *
     * @return float|false
     */
    public function getOrderShippingCostExternal($objArgCart)
    {
        TNTOfficiel_Logstack::log();

        $fltPrice = $this->getOrderShippingCost($objArgCart, 0.0);

        return $fltPrice;
    }

    /**
     * Carrier module : Method triggered form Cart Model if $carrier->need_range == true.
     * Get the shipping price depending on the ranges that were set in the back office.
     * Get the shipping cost for a cart (best price), if carrier need range (default).
     *
     * @param Cart  $objArgCart
     * @param float $fltArgShippingCost
     *
     * @return float|false false if no shipping cost (not available).
     */
    public function getOrderShippingCost($objArgCart, $fltArgShippingCost)
    {
        TNTOfficiel_Logstack::log();

        $intCartID = (int)$objArgCart->id;
        // See comment about current class $id_carrier property.
        $intCarrierID = (int)$this->id_carrier;

        // If is not a TNT carrier.
        if (!TNTOfficielCarrier::isTNTOfficielCarrierID($intCarrierID)) {
            // No shipping cost, not available.
            return false;
        }

        // If module not ready.
        if (!TNTOfficiel::isContextReady()) {
            // No shipping cost, not available.
            return false;
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        // If fail.
        if ($objTNTCarrierModel === null) {
            // No shipping cost, not available.
            return false;
        }

        $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();

        // If no account available for this carrier, or is not authenticated.
        if ($objTNTCarrierAccountModel === null
            || $objTNTCarrierAccountModel->getAuthValidatedDateTime() === null
        ) {
            // No shipping cost, not available.
            return false;
        }

        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        // If fail.
        if ($objTNTCartModel === null) {
            // No shipping cost, not available.
            return false;
        }

        // Multi-Shipping with multiple address or different carrier not supported.
        $boolMultiShippingSupport = $objTNTCartModel->isMultiShippingSupport();
        if (!$boolMultiShippingSupport) {
            return false;
        }

        // A delivery address is optional.
        $objPSAddressDelivery = $objTNTCartModel->getPSAddressDelivery();
        $objAddressDelivery = null;
        $strReceiverPostCode = null;
        // If delivery Address object is available.
        if ($objPSAddressDelivery !== null) {
            $objAddressDelivery = (object)array(
                'company' => $objPSAddressDelivery->company,
                'id_country' => $objPSAddressDelivery->id_country,
                'postcode' => trim($objPSAddressDelivery->postcode),
                'city' => trim($objPSAddressDelivery->city),
            );
            // Get postcode from delivery point.
            if ($objTNTCartModel->hasDeliveryPoint($intCarrierID)) {
                $arrDeliveryPoint = $objTNTCartModel->getDeliveryPoint($intCarrierID);
                if (array_key_exists('postcode', $arrDeliveryPoint)) {
                    $objAddressDelivery->postcode = trim($arrDeliveryPoint['postcode']);
                }
                if (array_key_exists('city', $arrDeliveryPoint)) {
                    $objAddressDelivery->city = trim($arrDeliveryPoint['city']);
                }
            }
            $strReceiverPostCode = $objAddressDelivery->postcode;
        }

        // Get the carriers model list using the heaviest product weight from cart.
        $arrObjTNTCarrierModelList = TNTOfficielCarrier::getLiveFeasibilityContextCarrierModelList(
            $objTNTCartModel->getCartHeaviestProduct(),
            $objAddressDelivery
        );

        // If carrier is feasible.
        if (array_key_exists($intCarrierID, $arrObjTNTCarrierModelList)) {
            //$objTNTCarrierModel = $arrObjTNTCarrierModelList[$intCarrierID];

            $fltPrice = $objTNTCarrierModel->getPrice(
                $objTNTCartModel->getCartTotalWeight(),
                $objTNTCartModel->getCartTotalPrice(),
                $strReceiverPostCode
            );

            // TNTOfficielOverride
            if (method_exists($this, 'getCustomCartCarrierPrice')) {
                $fltCustomCarrierPrice = $this->getCustomCartCarrierPrice($objTNTCartModel, $objTNTCarrierModel);
                // null : No custom price.
                if ($fltCustomCarrierPrice !== null) {
                    if ((is_int($fltCustomCarrierPrice) || is_float($fltCustomCarrierPrice))
                        && $fltCustomCarrierPrice >= 0
                    ) {
                        // Replace price with custom.
                        $fltPrice = (float)$fltCustomCarrierPrice;
                    } else {
                        // false or unknown value : No shipping cost, not available.
                        return false;
                    }
                }
            }

            // Use native Prestashop price.
            if ($fltPrice === null) {
                return $fltArgShippingCost;
            }
            // Carrier is disabled.
            if ($fltPrice === false) {
                return false;
            }
            // Shipping is free.
            if ($objTNTCartModel->isCartShippingFree($intCarrierID)) {
                return 0.0;
            }

            // Get additional shipping cost for cart.
            $fltCartExtraShippingCost = $objTNTCartModel->getCartExtraShippingCost($intCarrierID);

            return $fltPrice + $fltCartExtraShippingCost;
        }

        // No shipping cost, not available.
        return false;
    }

    /**
     * HOOK (AKA updateOrderStatus) called when an order's status is changed, right before it is actually changed.
     * Creates an expedition if status match the one set in account config.
     *
     * @param array $arrArgHookParams
     */
    public function hookActionOrderStatusUpdate($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        $objHookOrderStateNew = $arrArgHookParams['newOrderStatus'];
        $intHookOrderID = (int)$arrArgHookParams['id_order'];

        $intOrderStateIDNewID = (int)$objHookOrderStateNew->id;

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            // Do nothing.
            return;
        }

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            $objHookCookie->TNTOfficielError = sprintf(
                $this->l('Unable to load Order #%s'),
                $intHookOrderID
            );

            // Do nothing.
            return;
        }

        $objPSOrder = $objTNTOrderModel->getPSOrder();
        // If Order object not available.
        if ($objPSOrder === null) {
            // Do nothing.
            return;
        }

        $objOrderStateShipmentSave = $objTNTOrderModel->getOSShipmentSave();
        // If no orderstate available for this order carrier account.
        if ($objOrderStateShipmentSave === null) {
            // Do nothing.
            return;
        }
        $intOrderStateShipmentSaveID = (int)$objOrderStateShipmentSave->id;

        // If new order status must trigger expedition creation.
        if ($intOrderStateIDNewID === $intOrderStateShipmentSaveID) {
            // Check or update the shipping date.
            $arrResultPickupDate = $objTNTOrderModel->updatePickupDate();

            // If true error.
            if (is_string($arrResultPickupDate['strResponseMsgError'])) {
                $objHookCookie->TNTOfficielError = $arrResultPickupDate['strResponseMsgError'];
            } elseif (!$objTNTOrderModel->isExpeditionCreated()) {
                // Flag shipment label was requested for this order.
                // Prevent to chain to an After OrderState if was not a shipment creation request.
                $this->arrRequestedSaveShipment += array($intHookOrderID => true);
                // Send a shipment request.
                $arrResponse = $objTNTOrderModel->saveShipment();
                // If the response is a string, there is an error.
                if (is_string($arrResponse['strResponseMsgError'])) {
                    $objHookCookie->TNTOfficielError = $arrResponse['strResponseMsgError'];
                }
            }

            // If normal error.
            if (is_string($arrResultPickupDate['strResponseMsgWarning'])) {
                $objHookCookie->TNTOfficielWarning = $arrResultPickupDate['strResponseMsgWarning'];
            }

            // If order has no shipment created.
            if (!$objTNTOrderModel->isExpeditionCreated()) {
                // Default error message.
                if (!$objHookCookie->TNTOfficielError) {
                    $objHookCookie->TNTOfficielError = sprintf(
                        $this->l('Error while create shipping for Order #%s'),
                        $intHookOrderID
                    );
                }
                // Log.
                TNTOfficiel_Logger::logException(new Exception($objHookCookie->TNTOfficielError));
                // Redirect to prevent new order state (cleaner than reverting).
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink(
                        'AdminOrders',
                        true,
                        array(),
                        array(
                            'id_order' => $objPSOrder->id,
                            'vieworder' => 1,
                            //'setShopContext' => 's-'.$objPSOrder->id_shop,
                        )
                    )
                );
            }
        }
    }

    /**
     * HOOK (AKA postUpdateOrderStatus) called when an order's status is changed, right after it is actually changed.
     * Alert if the shipment was not saved (for an unknown reason).
     *
     * @param array $arrArgHookParams
     */
    public function hookActionOrderStatusPostUpdate($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        $objHookOrderStateNew = $arrArgHookParams['newOrderStatus'];
        $intHookOrderID = (int)$arrArgHookParams['id_order'];

        $intOrderStateIDNewID = (int)$objHookOrderStateNew->id;

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            // Do nothing.
            return;
        }

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            // Do nothing.
            return;
        }

        $objOrderStateShipmentSave = $objTNTOrderModel->getOSShipmentSave();
        // If no orderstate available for this order carrier account.
        if ($objOrderStateShipmentSave === null) {
            // Do nothing.
            return;
        }
        $intOrderStateShipmentSaveID = (int)$objOrderStateShipmentSave->id;

        // Check if the new order status is the one that must trigger shipment creation.
        if ($intOrderStateIDNewID === $intOrderStateShipmentSaveID) {
            // If order has no shipment created.
            if (!$objTNTOrderModel->isExpeditionCreated()) {
                $strMsgError = sprintf(
                    $this->l('Error while create shipping for Order #%s'),
                    $intHookOrderID
                );
                TNTOfficiel_Logger::logException(new Exception($strMsgError));
                if (!$objHookCookie->TNTOfficielError) {
                    $objHookCookie->TNTOfficielError = $strMsgError;
                }
            }
        }
    }

    /**
     * HOOK called after an order's status is changed.
     * Used to chain with another status.
     *
     * @param $arrArgHookParams
     */
    public function hookActionOrderHistoryAddAfter($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Cookie $objHookCookie */
        $objHookCookie = $arrArgHookParams['cookie'];

        $objHookOrderHistory = $arrArgHookParams['order_history'];
        $intOrderID = (int)$objHookOrderHistory->id_order;
        $intOrderStateIDNewID = (int)$objHookOrderHistory->id_order_state;

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intOrderID)) {
            // Do nothing.
            return;
        }

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            // Do nothing.
            return;
        }

        $objOrderStateShipmentSave = $objTNTOrderModel->getOSShipmentSave();
        // If no orderstate available for this order carrier account.
        if ($objOrderStateShipmentSave === null) {
            // Do nothing.
            return;
        }
        $intOrderStateShipmentSaveID = (int)$objOrderStateShipmentSave->id;

        // Check if the new order status is the one that must trigger shipment creation.
        // And if shipment is created
        // And if shipment was requested for this order.
        if ($intOrderStateIDNewID === $intOrderStateShipmentSaveID
            && $objTNTOrderModel->isExpeditionCreated()
            && array_key_exists($intOrderID, $this->arrRequestedSaveShipment)
        ) {
            $objOrderStateShipmentAfter = $objTNTOrderModel->getOSShipmentAfter();
            // If no orderstate available for this order carrier account.
            if ($objOrderStateShipmentAfter === null) {
                // Do nothing.
                return;
            }
            $intOrderStateShipmentAfterID = (int)$objOrderStateShipmentAfter->id;

            // Fix : Clearing cache is required for updated \OrderInvoiceCore::getTotalPaid() to apply,
            // otherwise a double invoice is created, as if the first one does not count.
            Cache::clear();

            // Apply next OrderState.
            $mxdUpdatedOS = $objTNTOrderModel->addOrderStateHistory($intOrderStateShipmentAfterID);
            if ($mxdUpdatedOS === false) {
                $strMsgError = sprintf(
                    $this->l('Error while adding status "%s" to Order #%s'),
                    $objOrderStateShipmentAfter->name,
                    $intOrderID
                );
                TNTOfficiel_Logger::logException(new Exception($strMsgError));
                if (!$objHookCookie->TNTOfficielError) {
                    $objHookCookie->TNTOfficielError = $strMsgError;
                }
            }
        }
    }

    /**
     * HOOK (AKA orderDetailDisplayed) displayed on order detail on Front-Office.
     * Insert parcel tracking block on order detail.
     *
     * @param array $arrArgHookParams
     *
     * @return string
     */
    public function hookDisplayOrderDetail($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        /** @var \Order $objHookOrder */
        $objHookOrder = $arrArgHookParams['order'];
        $intHookOrderID = (int)$objHookOrder->id;

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intHookOrderID)) {
            // Display nothing.
            return '';
        }

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intHookOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            // Display nothing.
            return '';
        }

        // If order has no shipment created.
        if (!$objTNTOrderModel->isExpeditionCreated()) {
            // Display nothing.
            return '';
        }

        $this->smarty->assign(
            array(
                'trackingUrl' => $this->context->link->getModuleLink(
                    TNTOfficiel::MODULE_NAME,
                    'tracking',
                    array('action' => 'tracking', 'orderId' => $intHookOrderID),
                    true
                ),
            )
        );

        // Display template.
        return $this->fetchTemplate('hook/displayOrderDetail.tpl');
    }

    /**
     * Add mail template variable.
     *
     * @param $arrArgHookParams
     *
     * @return bool
     */
    public function hookActionGetExtraMailTemplateVars($arrArgHookParams)
    {
        TNTOfficiel_Logstack::log();

        if (!array_key_exists('extra_template_vars', $arrArgHookParams)) {
            return false;
        }

        // Variables default is immediately available (empty).
        $arrArgHookParams['extra_template_vars']['{tntofficiel_tracking_url_text}'] = '';
        $arrArgHookParams['extra_template_vars']['{tntofficiel_tracking_url_html}'] = '';

        $intLangID = (int)$arrArgHookParams['id_lang'];
        $strLangISO = Language::getIsoById($intLangID);

        // If id_order not provided.
        if (!array_key_exists('{id_order}', $arrArgHookParams['template_vars'])) {
            return false;
        }

        $intOrderID = (int)$arrArgHookParams['template_vars']['{id_order}'];

        // If not an order associated with a TNT Carrier.
        if (!TNTOfficielOrder::isTNTOfficielOrderID($intOrderID)) {
            return false;
        }

        // Load TNT order.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            return false;
        }

        // Translation.
        $strLinkTrack = 'Track my TNT parcels';
        if ($strLangISO === 'fr') {
            $strLinkTrack = 'Suivre mes colis TNT';
        }

        // mails/fr/shipped.txt; mails/fr/shipped.html
        // if ($arrArgHookParams['template'] === 'shipped') {}

        // Get tracking URL if available.
        $strTrackingURL = $objTNTOrderModel->getTrackingURL();
        if (!is_string($strTrackingURL)) {
            return false;
        }

        $arrArgHookParams['extra_template_vars']['{tntofficiel_tracking_url_text}'] =
            $strLinkTrack . ' : [' . $strTrackingURL . ']';
        $arrArgHookParams['extra_template_vars']['{tntofficiel_tracking_url_html}'] =
            $this->getTextLink($strLinkTrack, array('href' => $strTrackingURL, 'style' => 'color:#337FF1'));

        return true;
    }
}
