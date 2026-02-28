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

class TNTOfficielCarrierModuleFrontController extends ModuleFrontController
{
    /**
     * TNTOfficielCarrierModuleFrontController constructor.
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
     * Get the delivery points popup via Ajax.
     * DROPOFFPOINT (Commerçants Partenaires) : XETT
     * DEPOT (Agences TNT) : PEX
     *
     * @return bool
     */
    public function displayAjaxBoxDeliveryPoints()
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;
        $objPSCart = $objContext->cart;

        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID((int)$objPSCart->id_carrier, false);
        if ($objTNTCarrierModel === null) {
            return false;
        }

        $strArgZipCode = trim(pSQL(Tools::getValue('tnt_postcode')));
        $strArgCity = trim(pSQL(Tools::getValue('tnt_city')));

        $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($objPSCart->id_address_delivery);
        // If delivery Address object available.
        if ($objPSAddressDelivery !== null && !$strArgZipCode && !$strArgCity) {
            // Default from delivery address.
            $strArgZipCode = trim($objPSAddressDelivery->postcode);
            $strArgCity = trim($objPSAddressDelivery->city);
        }

        $arrResultDeliveryPoints = $objTNTCarrierModel->getDeliveryPoints($strArgZipCode, $strArgCity);
        if ($arrResultDeliveryPoints === null) {
            return false;
        }

        // Get the relay points
        $this->context->smarty->assign(
            array(
                'carrier_type' => $objTNTCarrierModel->carrier_type,
                'current_postcode' => $arrResultDeliveryPoints['strZipCode'],
                'current_city' => $arrResultDeliveryPoints['strCity'],
                'arrRespositoryList' => $arrResultDeliveryPoints['arrPointsList'],
                'cities' => $arrResultDeliveryPoints['arrCitiesNameList'],
            )
        );

        echo $this->context->smarty->fetch(
            TNTOfficiel::getTemplateResource('front/displayAjaxBoxDeliveryPoints.tpl')
        );

        return true;
    }

    /**
     * Save delivery point XETT or PEX info.
     *
     * @return bool
     */
    public function displayAjaxSaveProductInfo()
    {
        TNTOfficiel_Logstack::log();

        $strDeliveryPoint = (string)Tools::getValue('product');
        $strDeliveryPointJSON = TNTOfficiel_Tools::inflate($strDeliveryPoint);
        $arrDeliveryPoint = TNTOfficiel_Tools::decJSON($strDeliveryPointJSON);

        $objContext = $this->context;
        $objCart = $objContext->cart;
        $intCartID = (int)$objCart->id;

        // Load TNT cart info or create a new one for its ID.
        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        // If fail.
        if ($objTNTCartModel === null) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => TNTOfficiel::getCodeTranslate('errorTechnical'),
                )
            );

            return false;
        }

        if ($objTNTCartModel->setDeliveryPoint($arrDeliveryPoint) === false) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => TNTOfficiel::getCodeTranslate('errorUnableSetDeliveryPointStr'),
                )
            );

            return false;
        }

        $this->context->smarty->assign(
            array(
                'item' => $arrDeliveryPoint,
                'carrier_type' => isset($arrDeliveryPoint['xett']) ? 'DROPOFFPOINT' : 'DEPOT',
            )
        );

        $arrResult = array();

        $arrResult['template'] = $this->context->smarty->fetch(
            TNTOfficiel::getTemplateResource('front/displayAjaxSaveProductInfo.tpl')
        );

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Check TNT data before payment process.
     *
     * @return bool
     */
    public function displayAjaxCheckPaymentReady()
    {
        TNTOfficiel_Logstack::log();

        $objContext = $this->context;

        $objCart = $objContext->cart;

        $intCartID = (int)$objCart->id;

        $arrResult = array(
            'error' => TNTOfficiel::getCodeTranslate('errorTechnical'),
            'carrier' => null,
        );

        // Load TNT cart info or create a new one for its ID.
        $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, true);
        if ($objTNTCartModel !== null) {
            $arrResult = $objTNTCartModel->isPaymentReady();
        }

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }
}
