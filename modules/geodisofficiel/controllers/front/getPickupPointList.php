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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';

class GeodisOfficielGetPickupPointListModuleFrontController extends ModuleFrontController
{
    protected $price;
    protected $result;
    protected $pickupPointList = array();
    public $ssl = true;

    public function __construct()
    {
        parent::__construct();
        $this->page_name = 'ajax';

        $this->result = array();
    }

    public function initContent()
    {
        if (!Tools::getIsset('idCarrier')) {
            $this->sendError('Missing parameter idCarrier.');
        }

        if (!Tools::getIsset('token')) {
            $this->sendError('Missing parameter token.');
        }

        if (Tools::getIsset('token') != Context::getContext()->cookie->geodisToken) {
            $this->sendError('Invalid token.');
        }

        $idCarrier = Tools::getValue('idCarrier');

        if (is_array($idCarrier)) {
            $this->getPickupPointListFromPrestations();
        } else {
            return $this->getPickupPointListFromCarrier();
        }
    }

    protected function getPickupPointListFromPrestations()
    {
        $idCarrierList = Tools::getValue('idCarrier');
        $idCarrierList = array_map('intval', $idCarrierList);

        $carrierCollection = GeodisCarrier::getCollection();
        $carrierCollection->where('id_carrier', 'in', $idCarrierList);

        try {
            $webService = GeodisServiceWebservice::getInstance();
            $this->pickupPointList = $webService->getPickupPointFromCarriers(
                $carrierCollection,
                Tools::getValue('latitude', 0),
                Tools::getValue('longitude', 0),
                Tools::getValue('defaultLatitude', 0),
                Tools::getValue('defaultLongitude', 0),
                null,
                Tools::getValue('zipCode'),
                Tools::getValue('countryCode')
            );
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }

        $this->sendSuccess();
    }

    protected function getPickupPointListFromCarrier()
    {
        // Need to get all carrier linked to the group of carrier
        $idCarrier = (int) Tools::getValue('idCarrier');

        if (!$idCarrier) {
            return $this->getAllPickupPointList();
        }
        $carrier = new GeodisCarrier($idCarrier);

        if (!$carrier) {
            $this->sendError('Carrier do not exists.');
        }

        $withdrawalType = null;

        if ($carrier->getPrestation()->withdrawal_agency) {
            $withdrawalType = 'point';
        } else {
            $withdrawalType = 'agency';
        }

        try {
            $webService = GeodisServiceWebservice::getInstance();
            $this->pickupPointList = $webService->getPickupPoint(
                $withdrawalType,
                Tools::getValue('latitude'),
                Tools::getValue('longitude'),
                Tools::getValue('defaultLatitude'),
                Tools::getValue('defaultLongitude'),
                null,
                Tools::getValue('zipCode'),
                Tools::getValue('countryCode')
            );
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }

        $this->sendSuccess();
    }

    protected function sendSuccess()
    {
        $this->result['status'] = 'success';
        $this->result['pickupPointList'] = $this->pickupPointList;

        $this->send();
    }

    protected function sendError($message)
    {
        $this->result['status'] = 'error';
        $this->result['message'] = $message;

        $this->send();
    }

    protected function send()
    {
        echo json_encode($this->result);
        exit;
    }
}
