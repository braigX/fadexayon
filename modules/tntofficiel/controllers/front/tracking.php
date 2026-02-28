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

class TNTOfficielTrackingModuleFrontController extends ModuleFrontController
{
    /**
     * TNTOfficielTrackingModuleFrontController constructor.
     * Controller always used for AJAX response.
     */
    public function __construct()
    {
        TNTOfficiel_Logstack::log();

        parent::__construct();

        // FO Auth is required and guest allowed.
        $this->auth = true;
        $this->guestAllowed = true;

        // SSL
        $this->ssl = Tools::usingSecureMode();
        // No header/footer.
        $this->ajax = true;
    }

    /**
     * Display the tracking popup.
     *
     * @return bool
     */
    public function displayAjaxTracking()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('orderId');

        $objCustomer = $this->context->customer;

        if ($objCustomer->isLogged($this->guestAllowed)) {
            // Load TNT order.
            $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
            // If TNTOrder object available.
            if ($objTNTOrderModel !== null) {
                $objPSOrder = $objTNTOrderModel->getPSOrder();
                // If Order object available.
                if ($objPSOrder !== null) {
                    // If order belong to customer.
                    if ((int)$objPSOrder->id_customer === (int)$objCustomer->id) {
                        // Update tracking state.
                        $objTNTOrderModel->updateParcelsTrackingState();
                        // Get parcels.
                        $arrObjTNTParcelModelList = $objTNTOrderModel->getTNTParcelModelList();
                        if ((count($arrObjTNTParcelModelList) > 0)) {
                            $this->context->smarty->assign(
                                array(
                                    'arrObjTNTParcelModelList' => $arrObjTNTParcelModelList,
                                )
                            );
                            echo $this->context->smarty->fetch(
                                TNTOfficiel::getDirModule('template') . 'front/displayAjaxTracking.tpl'
                            );

                            return true;
                        }
                    }
                }
            }
        }

        // 404 fallback.
        Controller::getController('PageNotFoundController')->run();

        return false;
    }
}
