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

class TNTOfficiel_PDFCreator
{
    /**
     * Create manifest from an order ID list.
     *
     * @param array $arrArgOrderIDList
     */
    public static function createManifest(array $arrArgOrderIDList)
    {
        TNTOfficiel_Logstack::log();

        // Class loaded here to prevent any conflict with global tcpdf K_TCPDF_CALLS_IN_HTML constant and/or others modules.
        // Required by PDF constructor which later load class HTMLTemplate<NAME> (using name 'TNTOfficielManifest').
        require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/pdf/HTMLTemplateTNTOfficielManifest.php';

        $arrManifesDataList = array();

        foreach ($arrArgOrderIDList as $intOrderID) {
            $intOrderID = (int)$intOrderID;

            // Load TNT Order.
            $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
            if ($objTNTOrderModel === null) {
                continue;
            }
            // Get the selected TNT Carrier object from Order.
            $objTNTCarrierModel = $objTNTOrderModel->getTNTCarrierModel();
            // If fail or carrier is not from TNT module.
            if ($objTNTCarrierModel === null) {
                continue;
            }
            $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();
            // If no account available for this carrier.
            if ($objTNTCarrierAccountModel === null) {
                continue;
            }

            $objPSShop = $objTNTCarrierModel->getPSShop();
            // If Shop object not available, skip.
            if ($objPSShop === null) {
                continue;
            }

            // Doc per Account ID (shop)
            $strPageKey = '_' . $objTNTCarrierModel->id_shop . '_' . $objTNTCarrierAccountModel->id;
            // Doc per Order
            //$strPageKey = '_'.$intOrderID;

            // Set shop context to get the right configuration.
            Shop::setContext(Shop::CONTEXT_SHOP, $objTNTCarrierModel->id_shop);
            /*
            $strShopCompanyName = $objPSShop->name;
            if ($strShopCompanyName && $strShopCompanyName !== Configuration::get('PS_SHOP_NAME')) {
                $strShopCompanyName .= (', ' . Configuration::get('PS_SHOP_NAME'));
            }
            */
            if (!array_key_exists($strPageKey, $arrManifesDataList)) {
                $arrManifesDataList[$strPageKey] = array();
            }
            if (!array_key_exists('carrierAccount', $arrManifesDataList[$strPageKey])) {
                $arrManifesDataList[$strPageKey]['carrierAccount'] = $objTNTCarrierAccountModel->account_number;
            }
            if (!array_key_exists('address', $arrManifesDataList[$strPageKey])) {
                $intShopCountryID = (int)(Configuration::get('PS_SHOP_COUNTRY_ID')
                    ? Configuration::get('PS_SHOP_COUNTRY_ID')
                    : Configuration::get('PS_COUNTRY_DEFAULT'));

                $strShopCountryISO = TNTOfficielReceiver::getCountryISOCode($intShopCountryID);

                $arrManifesDataList[$strPageKey]['address'] = array(
                    //$strShopCompanyName,
                    'name' => $objTNTCarrierAccountModel->sender_company,
                    //Configuration::get('PS_SHOP_ADDR1'),
                    'address1' => $objTNTCarrierAccountModel->sender_address1,
                    //Configuration::get('PS_SHOP_ADDR2'),
                    'address2' => $objTNTCarrierAccountModel->sender_address2,
                    //Configuration::get('PS_SHOP_CODE'),
                    'postcode' => trim($objTNTCarrierAccountModel->sender_zipcode),
                    //Configuration::get('PS_SHOP_CITY'),
                    'city' => trim($objTNTCarrierAccountModel->sender_city),
                    //PS_SHOP_COUNTRY
                    'country' => $strShopCountryISO,
                );
            }
            if (!array_key_exists('arrParcelInfoList', $arrManifesDataList[$strPageKey])) {
                $arrManifesDataList[$strPageKey]['arrParcelInfoList'] = array();
            }
            if (!array_key_exists('totalWeight', $arrManifesDataList[$strPageKey])) {
                $arrManifesDataList[$strPageKey]['totalWeight'] = 0.0;
            }
            if (!array_key_exists('parcelsNumber', $arrManifesDataList[$strPageKey])) {
                $arrManifesDataList[$strPageKey]['parcelsNumber'] = 0;
            }

            $objPSAddressDelivery = $objTNTOrderModel->getPSAddressDelivery();

            // Get the parcels.
            $arrObjTNTParcelModelList = $objTNTOrderModel->getTNTParcelModelList();
            /** @var TNTOfficielParcel $objTNTParcelModel */
            foreach ($arrObjTNTParcelModelList as $objTNTParcelModel) {
                // Add weight for the parcels.
                $arrManifesDataList[$strPageKey]['totalWeight'] += $objTNTParcelModel->weight;
                $arrManifesDataList[$strPageKey]['parcelsNumber']++;
                $arrManifesDataList[$strPageKey]['arrParcelInfoList'][] = array(
                    'objTNTParcelModel' => $objTNTParcelModel,
                    'objPSAddressDelivery' => $objPSAddressDelivery,
                    'strCarrierLabel' => $objTNTCarrierModel->getCarrierInfos()->label,
                );
            }
        }

        $objPDFMerger = new TNTOfficiel_PDFMerger();
        $intManifestCounter = 0;
        $strOutputFileName = 'manifest_list.pdf';

        foreach ($arrManifesDataList as $arrManifesData) {
            $objManifestPDF = new PDF(
                array('manifestData' => $arrManifesData),
                'TNTOfficielManifest',
                Context::getContext()->smarty
            );

            ++$intManifestCounter;
            $objPDFMerger->addPDF('manifest_' . $intManifestCounter . '.pdf', 'all', $objManifestPDF->render(false));
        }

        // Concat.
        if ($intManifestCounter > 0) {
            // Download and exit.
            TNTOfficiel_Tools::download(
                $strOutputFileName,
                $objPDFMerger->merge('string', $strOutputFileName),
                'application/pdf'
            );
        }
    }
}
