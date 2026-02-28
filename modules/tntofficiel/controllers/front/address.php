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

class TNTOfficielAddressModuleFrontController extends ModuleFrontController
{
    /**
     * TNTOfficielAddressModuleFrontController constructor.
     * Controller always used for AJAX response.
     */
    public function __construct()
    {
        TNTOfficiel_Logstack::log();

        parent::__construct();

        // SSL
        $this->ssl = Tools::usingSecureMode();
        // No header/footer.
        $this->ajax = true;
    }

    /**
     * Store Extra Information of Receiver Delivery Address (FO).
     *
     * @return bool
     */
    public function displayAjaxStoreReceiverInfo()
    {
        TNTOfficiel_Logstack::log();

        // Default is technical Error.
        $arrFormReceiverInfoValidate = array(
            'fields' => array(),
            'errors' => array(),
            'length' => 0,
            'stored' => false,
        );

        $objContext = $this->context;
        $objCart = $objContext->cart;

        // Load TNT receiver info or create a new one for its ID.
        $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($objCart->id_address_delivery);
        // If fail.
        if ($objTNTReceiverModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrFormReceiverInfoValidate);

            return false;
        }

        // Validate and store receiver info, using form values.
        $arrFormReceiverInfoValidate = $objTNTReceiverModel->storeReceiverInfo(
            (string)Tools::getValue('receiver_email'),
            (string)Tools::getValue('receiver_phone'),
            (string)Tools::getValue('receiver_building'),
            (string)Tools::getValue('receiver_accesscode'),
            (string)Tools::getValue('receiver_floor'),
            (string)Tools::getValue('receiver_instructions')
        );

        echo TNTOfficiel_Tools::encJSON($arrFormReceiverInfoValidate);

        return true;
    }

    /**
     * Check if the city match the postcode.
     *
     * @return bool
     */
    public function displayAjaxCheckPostcodeCity()
    {
        TNTOfficiel_Logstack::log();

        $arrResult = array(
            'required' => false,
            'postcode' => false,
            'cities' => false,
        );

        // Check the country
        $intCountryID = (int)Tools::getValue('countryId');
        $strCountryISO = TNTOfficielReceiver::getCountryISOCode($intCountryID);
        $strZipCode = pSQL(Tools::getValue('postcode'));
        $strCity = pSQL(Tools::getValue('city'));

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return false;
        }

        if ($strCountryISO === 'FR') {
            // Check is required for France.
            $arrResult['required'] = true;
            // Check the city/postcode.
            $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide($strCountryISO, $strZipCode, $strCity);
            // PostCode is well formated NNNNN
            if ($arrResultCitiesGuide['strZipCode'] !== null) {
                // If city/postcode correct.
                // If communication error, TNT carrier are not available,
                // but postcode/city is considered wrong and then show error "unknow postcode" on Front-Office checkout.
                // Also, return true to prevent always invalid address form.
                if ($arrResultCitiesGuide['boolIsRequestComError'] || $arrResultCitiesGuide['boolIsCityNameValid']) {
                    $arrResult['postcode'] = true;
                    $arrResult['cities'] = true;
                } else {
                    // Get cities from the webservice from the given postal code.
                    if (count($arrResultCitiesGuide['arrCitiesNameList']) > 0) {
                        $arrResult['postcode'] = true;
                    }

                    $arrResult['cities'] = $arrResultCitiesGuide['arrCitiesNameList'];
                }
            }
        }

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Get cities for a postcode.
     *
     * @return bool false on failure. true on success.
     */
    public function displayAjaxGetCities()
    {
        TNTOfficiel_Logstack::log();

        // Default address ID.
        $objCart = $this->context->cart;
        $intAddressID = (int)$objCart->id_address_delivery;

        if (Tools::getIsset('id_address')) {
            $intAddressID = (int)Tools::getValue('id_address');
        }

        $arrResult = array(
            'valid' => true,
            'cities' => array(),
            'postcode' => false,
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($intAddressID);
        // If delivery address object is not available.
        if ($objPSAddressDelivery === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        // If delivery Address do not belong to customer.
        if ((int)$this->context->customer->id !== (int)$objPSAddressDelivery->id_customer) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddressDelivery->id_country);
        $strReceiverZipCode = trim($objPSAddressDelivery->postcode);
        $strReceiverCity = trim($objPSAddressDelivery->city);

        // Check the city/postcode.
        $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide(
            $strReceiverCountryISO,
            $strReceiverZipCode,
            $strReceiverCity
        );

        $arrResult = array(
            // Is current ZipCode/CityName Valid for FR (else valid) ?
            // Unsupported country or communication error is considered true to prevent always
            // invalid address form and show error "unknow postcode" on Front-Office checkout.
            'valid' => (!$arrResultCitiesGuide['boolIsCountrySupported']
                || $arrResultCitiesGuide['boolIsRequestComError']
                || $arrResultCitiesGuide['boolIsCityNameValid']
            ),
            // Cities name list available for current ZipCode.
            'cities' => $arrResultCitiesGuide['arrCitiesNameList'],
            // Current ZipCode.
            'postcode' => $arrResultCitiesGuide['strZipCode'],
        );

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Update the city for the current delivery address.
     *
     * @return bool false on failure. true on success.
     */
    public function displayAjaxUpdateDeliveryAddress()
    {
        TNTOfficiel_Logstack::log();

        // Default address ID.
        $objPSCart = $this->context->cart;
        $intAddressID = (int)$objPSCart->id_address_delivery;

        if (Tools::getIsset('id_address')) {
            $intAddressID = (int)Tools::getValue('id_address');
        }

        $strCity = trim(pSQL(Tools::getValue('city')));

        $arrResult = array(
            'result' => false,
        );

        $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($intAddressID);

        // If delivery address object is not available.
        if ($objPSAddressDelivery === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        // If delivery Address do not belong to customer.
        if ((int)$this->context->customer->id !== (int)$objPSAddressDelivery->id_customer) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        // If not a string or zero length.
        if (!is_string($strCity) || !(Tools::strlen($strCity) > 0)) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $objPSAddressDelivery->city = $strCity;
        $arrResult['result'] = $objPSAddressDelivery->save();

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }
}
