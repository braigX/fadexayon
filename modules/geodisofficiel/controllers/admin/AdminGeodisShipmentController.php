<?php
/**
 * 2024 Braigue Aziz (BRAIGUE.COM).
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to aziz@braigue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    BRAIGUE <aziz@braigue.com>
 *  @copyright 2024 BRAIGUE
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackage.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackageOrderDetail.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipmentHistory.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccountPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisDeliveryLabel.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackageLabel.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceOrder.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisWSCapacity.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataShipment.php';

class AdminGeodisShipmentController extends GeodisControllerAdminAbstractMenu
{
    public $idOrder;
    public $order;
    public $nombreJour = 2;
    public $productErrors = array();
    public $packageErrors = array();
    public $shipmentSuccess = array();
    public $shipmentErrors = array();
    public $formData = array();
    protected $shipment;
    protected $items = array();
    protected $fatalError = array();

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        if (Tools::isSubmit('removalDate')) {
            echo $this->getDaysOff((int)Tools::getValue('idGeodisCarrier'));
            die();
        }

        if (Tools::getIsset('call') && Tools::getValue('call') == 'getPrestationAvailable') {
            $this->getPrestationAvailable();
        }

        $this->shipment = new GeodisShipment(Tools::getValue('id'));

        if (Tools::getValue('call') == 'getLabels') {
            $this->getLabels();
        }

        if ($this->shipment->id) {
            $this->idOrder = $this->shipment->id_order;
        } else {
            $this->idOrder = Tools::getValue('id_order');
            $this->shipment->reference = Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
        }
        $this->order = new Order($this->idOrder);

        $this->items = GeodisServiceOrder::getInstance()->getOrderItems($this->idOrder);
        $this->checkGlobalErrors();

        GeodisServiceTranslation::registerSmarty();

        parent::__construct();
        $this->bootstrap = true;

        $this->context->smarty->assign(
            'orderGridLink',
            $this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'OrdersGrid')
        );

        $this->base_tpl_view = 'main.tpl';

        if (Tools::isSubmit('submit') || Tools::isSubmit('submitandnew')) {
            try {
                if ($this->processShipment()) {
                    $this->submitShipment();
                }
            } catch (Exception $e) {
                $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                    $e->getMessage()
                );
            }
        } elseif (Tools::isSubmit('send')) {
            $this->processShipment();
            $this->sendShipment();
        } elseif (Tools::isSubmit('deleteshipment_'.$this->idOrder)) {
            $this->processDelete();
        }

        if (Tools::isSubmit('submit') && $this->shipment->id && !$this->fatalError) {
            return Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'Shipment',
                    true,
                    array(),
                    array(
                        'id' => $this->shipment->id,
                        'errors' => $this->shipmentErrors,
                        'success' => $this->shipmentSuccess,
                        'packageErrors' => $this->packageErrors
                    )
                )
            );
        }

        if (Tools::isSubmit('submitandnew') && empty($this->shipmentErrors) && !$this->fatalError) {
            return Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'Shipment',
                    true,
                    array(),
                    array('id_order' => $this->shipment->id_order)
                )
            );
        }

        if (empty($this->packageErrors)) {
            $errors = array();
            foreach (Tools::getValue('packageErrors', array()) as $idPackage => $packageErrors) {
                $errors[$idPackage] = array();
                foreach ($packageErrors as $error) {
                    $errors[$idPackage][] = $error;
                }
            }
            $this->packageErrors = $errors;
        }

        if (empty($this->shipmentErrors)) {
            $this->shipmentErrors = Tools::getValue('errors', array());
        }

        if (empty($this->shipmentSuccess)) {
            $this->shipmentSuccess = Tools::getValue('success', array());
        }

        $this->items = GeodisServiceOrder::getInstance()->getOrderItems($this->idOrder);
    }

    protected function getVolumes()
    {
        return GeodisWSCapacity::getCollection();
    }

    protected function checkGlobalErrors()
    {
        // Shipment is valid?
        if (!$this->shipment->id && Tools::getValue('id')) {
            $this->errors[] = GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidShipment'
            );
            return;
        }

        // Order is valid?
        if (!$this->order->id) {
            $this->errors[] = GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidOrder'
            );
            return;
        }


        // Is there product available?

        if ($this->shipment->id) {
            // We are not in creating mode
            return;
        }

        foreach ($this->items as $item) {
            if ($item->getQuantityAvailable()) {
                // One product at least is available
                return;
            }
        }

        $this->errors[] = GeodisServiceTranslation::get(
            'Admin.ShipmentController.index.error.noProductsAvailable'
        );
    }

    protected function getDaysOff($idGeodisCarrier)
    {
        $geodisCarrier = new GeodisCarrier($idGeodisCarrier);
        $prestation = new GeodisPrestation($geodisCarrier->id_prestation);
        $account = new GeodisAccount($geodisCarrier->id_account);

        $geodisSiteCollection = GeodisSite::getCollection();
        $iso = null;
        foreach ($geodisSiteCollection as $site) {
            if ($site->default[1] == 1) {
                $iso = Country::getIsoById($site->id_country);
            }
        }

        try {
            $response = GeodisServiceWebservice::getInstance()->getDepartureCalendar(
                $prestation->code_groupe_produits,
                $prestation->code_option,
                $iso,
                $prestation->code_produit,
                $account->code_sa
            );
            $data = array(
                'daysOff' =>  $response['contenu']['listExceptions'],
                'firstDay' => $response['contenu']['jourDebut'],
            );
        } catch (Exception $e) {
            $data = array(
                'daysOff' => array(),
                'firstDay' => (new DateTime)->format('Y-m-d')
            );
        }

        return json_encode($data);
    }

    protected function getRemovalDate()
    {
        if ($this->shipment->id) {
            return $this->shipment->departure_date;
        }

        return null;
    }

    protected function getGroupCarrierCollection()
    {
        $collection = GeodisGroupCarrier::getCollection();
        $collection->where('active', '=', 1);

        return $collection;
    }

    public function getCarrierFromGroupCarrier()
    {
        $groupCarrierAndCarrierArray = array();
        $geodisGroupCarrierCollection = $this->getGroupCarrierCollection();
        foreach ($geodisGroupCarrierCollection as $geodisGroupCarrier) {
            $geodisCarrierCollection = GeodisCarrier::getCollection();
            $geodisCarrierCollection->where('deleted', '=', 0);
            $geodisCarrierCollection->where('id_group_carrier', '=', $geodisGroupCarrier->id);
            foreach ($geodisCarrierCollection as $geodisCarrier) {
                $geodisAccountPrestation =
                GeodisAccountPrestation::get($geodisCarrier->id_account, $geodisCarrier->id_prestation);
                $groupCarrierAndCarrierArray[$geodisGroupCarrier->id][] = array(
                    'name' => $geodisCarrier->name,
                    'id_account_prestation' => $geodisAccountPrestation->id,
                    'id' => $geodisCarrier->id,
                    'idPrestation' => $geodisCarrier->id_prestation,
                );
            }
        }

        return $groupCarrierAndCarrierArray;
    }

    public function getPrestationFromCarrier()
    {
        $prestationFromCarrier = array();
        $geodisCarrierCollection = GeodisCarrier::getCollection();

        foreach ($geodisCarrierCollection as $geodisCarrier) {
            $prestation = new GeodisPrestation($geodisCarrier->id_prestation);
            $prestationFromCarrier[$geodisCarrier->id] = array(
                'label' => $prestation->libelle,
                'idPrestation' => $prestation->id,
            );
        }

        return $prestationFromCarrier;
    }

    public function getAccountFromCarrier()
    {
        $accountFromCarrier = array();
        $geodisCarrierCollection = GeodisCarrier::getCollection();

        foreach ($geodisCarrierCollection as $geodisCarrier) {
            $account = new GeodisAccount($geodisCarrier->id_account);
            $accountFromCarrier[$geodisCarrier->id] = $account->code_sa.'-'.$account->code_client;
        }

        return $accountFromCarrier;
    }

    public function renderList()
    {
        $tooltipFormUpdated= (string) GeodisServiceTranslation::get(
            'Admin.Shipment.index.send.infobulle.submit'
        );
        $tooltip = (string) GeodisServiceTranslation::get(
            'Admin.Shipment.index.send.infobulle.submit'
        );
        if ($this->shipment->recept_number != null && $this->shipment->recept_number != '') {
            $tooltip = (string) GeodisServiceTranslation::get(
                'Admin.Shipment.index.send.infobulle.print'
            );
        }

        if (Tools::getIsset('id_order')) {
            $formUrl = $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'Shipment',
                true,
                null,
                array('id_order' => Tools::getValue('id_order'))
            );
        } else {
            $formUrl = $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'Shipment',
                true,
                null,
                array('id' => $this->shipment->id)
            );
        }

        $this->tpl_view_vars = array(
            'order' => $this->order,
            'fiscalCodeCollection' => $this->getFiscalCodeCollection(),
            'formData' => $this->formData,
            'productErrors' => $this->productErrors,
            'packageErrors' => $this->packageErrors,
            'shipmentErrors' => $this->shipmentErrors,
            'shipmentSuccess' => $this->shipmentSuccess,
            'tooltip' => $tooltip,
            'tooltipFormUpdated' => $tooltipFormUpdated,
            'removalDate' => $this->getRemovalDate(),
            'items' => $this->items,
            'idLang' => Context::getContext()->language->id,
            'packages' => $this->getPackages(),
            'shipment' => $this->shipment,
            'hasError' => count($this->errors),
            'fiscalCodeRules' => GeodisPackageOrderDetail::getWsRules(),
            'volumes' => $this->getVolumes(),
            'printlabelControllerUrl' => $this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'PackageLabel')
            .'&idShipment='.$this->shipment->id,
            'printDeliveryControllerUrl' => $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'DeliveryLabel'
            ).'&idShipment='.$this->shipment->id,
            'groupCarrierCollection' => $this->getGroupCarrierCollection(),
            'menu' => $this->tpl_view_vars['menu'],
            'formUrl' => $formUrl,
        );
        return parent::renderView();
    }

    protected function getFiscalCodeCollection()
    {
        return GeodisFiscalCode::getCollection();
    }

    public function generateJSON()
    {
        /*Edit with tem wassim novatis*/
        $data = array();
        foreach ($this->items as $item) {
            $weight = (float) $item->getProduct()->weight;
            $volume = (float) $item->getProduct()->volume;
            if ($item->getCombination()) {
                $weight += (float) $item->getCombination()->weight;
            }
            $weight = ceil($weight * 100) / 100;

            $itemData = array(
                'item_id' => $item->getProduct()->id,
                'item_name' => $item->getProduct()->name,
                'item_reference' => $item->getProduct()->reference,
                'quantity_available' => $item->getQuantityAvailable($this->shipment),
                'item_weight' => $weight,
                'item_volume' => $volume,
                'wl_is_wine_liquor' => $item->getWineLiquor()->is_wine_liquor,
                'wl_id_fiscal_code' => $item->getWineLiquor()->id_fiscal_code,
                'wl_detail' => $item->getWineLiquor()->detail,
            );
            $data['items'][] = $itemData;
            /*End */
        }

        $data['packages'] = array();
        $packages = $this->getPackages();
        foreach ($packages as $package) {
            $packageData = array(
                'package_type' => $package->package_type,
                'package_reference' => $package->reference,
                'package_width' => $package->width,
                'package_height' => $package->height,
                'package_depth' => $package->depth,
                'package_weight' => $package->weight,
                'package_volume' => $package->volume,
                'package_type' => $package->package_type,
                'package_id' => $package->id,
                'items' => array(),
            );

            foreach ($this->items as $item) {
                $packageData['items'][] = array(
                    'id_item' => $item->getProduct()->id,
                    'quantity' => $item->getPackageOrderDetail($package)->quantity,
                    'is_wine_and_liquor' => $item->getPackageOrderDetail($package)->is_wine_and_liquor,
                    'id_fiscal_code' => $item->getPackageOrderDetail($package)->id_fiscal_code,
                    'nb_col' => $item->getPackageOrderDetail($package)->nb_col,
                    'volume_cl' => (float) $item->getPackageOrderDetail($package)->volume_cl,
                    'volume_l' => $item->getPackageOrderDetail($package)->volume_l,
                    'n_mvt' => $item->getPackageOrderDetail($package)->n_mvt,
                    'shipping_duration' => $item->getPackageOrderDetail($package)->shipping_duration,
                    'fiscal_code_ref' => $item->getPackageOrderDetail($package)->fiscal_code_ref,
                    'n_ea' => $item->getPackageOrderDetail($package)->n_ea,
                );
            }
            $data['packages'][] = $packageData;
        }

        $data['wsRules'] = GeodisPackageOrderDetail::getWSRules();
        $data['errors'] = array(
            'invalidIntWLField' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidIntWLField'
            ),
            'invalidNumberOfDays' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidNumberOfDays'
            ),
            'missingWLField' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.missingWLField'
            ),
            'incompatibleFicalCode' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.incompatibleFicalCode'
            ),
            'invalidVolume' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidVolume'
            ),
        );
        $data['warnings'] = array(
            'daaUniq' => (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.warning.daaUniq'
            ),
        );
        $data['removalDate'] = $this->getRemovalDate();
        $data['defaultFiscalCode'] = GeodisServiceConfiguration::getInstance()->get('default_fiscal_code');

        if (!$this->shipment->id) {
            $cartCarrier = GeodisCartCarrier::loadFromIdCart($this->order->id_cart);
            if ($cartCarrier) {
                $data['groupCarrier'] = $cartCarrier->getCarrier()->id_group_carrier;
                $data['carrier'] = $cartCarrier->getCarrier()->id;
            } else {
                $data['groupCarrier'] = null;
                $data['carrier'] = null;
            }
        } else {
            $data['groupCarrier'] = $this->shipment->id_group_carrier;
            $data['carrier'] = $this->shipment->id_carrier;
        }

        $data['braig_weight'] = $this->shipment->weight;
        $data['braig_volume'] = $this->shipment->weight;

        $logFile = _PS_MODULE_DIR_ . 'geodisofficiel/log.txt';
        $shipmentData = print_r($this->shipment, true);
        file_put_contents($logFile, $shipmentData, FILE_APPEND);

        
        
        return json_encode($data);
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_.'geodisofficiel/views/css/admin/shippingCreation.css', 'all', 1);
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel/views/js/admin/GeodisShipment.js');
        $this->addjQueryPlugin(array('chosen', 'fancybox'));

        $post = null;
        if (Tools::getIsset('submit')) {
            $post = Tools::getAllValues();
        }
        $json = $this->generateJSON();
        $idGroupCarrier = 0;
        $idCarrier = 0;

        if ($this->shipment->id) {
            $idGroupCarrier = $this->shipment->id_group_carrier;
            $idCarrier = $this->shipment->id_carrier;
        } else {
            $cartCarrier = GeodisCartCarrier::loadFromIdCart($this->order->id_cart);

            if ($cartCarrier) {
                $idCarrier = $cartCarrier->id_carrier;
                $carrier = new GeodisCarrier($idCarrier);
                $idGroupCarrier = $carrier->getGroupCarrier()->id;
            }
        }

        Media::addJsDef(
            array(
                'geodis' => array(
                    'json' => $json,
                    'post' => $post,
                    'wl' => $this->getWsAccountPrestationInfo(),
                    'packages' => $this->getPackages(),
                    'carrierCollection' => $this->getCarrierFromGroupCarrier(),
                    'prestationCollection' => $this->getPrestationFromCarrier(),
                    'accountCollection' => $this->getAccountFromCarrier(),
                    'idGroupCarrier' => $idGroupCarrier,
                    'idCarrier' => $idCarrier,
                    'token' => Tools::getAdminTokenLite(GEODIS_ADMIN_PREFIX.'Shipment'),
                    'printingPort' => GeodisServiceConfiguration::getInstance()->get('thermal_printing_port'),
                    'thermalPrintingStatus' => GeodisServiceConfiguration::getInstance()
                        ->get('thermal_printing_activated'),
                    'idShipment' => $this->shipment->id,
                    'admin' => GEODIS_ADMIN_PREFIX,
                    'idOrder' => $this->idOrder,
                    'isComplete' => $this->shipment->is_complete,
                    'noLabelAvailable' => Tools::getIsset('errors'),
                ),
            )
        );
    }

    public function getPackages()
    {
        if ($this->shipment->id) {
            return $this->shipment->getPackages();
        }

        $package = new GeodisPackage();
        $package->reference = Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));

        $totalWeight = 0;

        foreach ($this->items as $item) {
            if ($item->getCombination()) {
                // For combination, it's combination weight + product weight
                $totalWeight += (float) $item->getCombination()->weight;
            }

            $totalWeight += (float) $item->getProduct()->weight;
        }

        $package->weight = ceil($totalWeight * 100) / 100;

        return array(
            $package
        );
    }

    public function processDelete()
    {
        $this->base_tpl_view = 'empty.tpl';
        try {
            if ($this->shipment->is_complete == 1) {
                throw new Exception(
                    GeodisServiceTranslation::get(
                        'Admin.ShipmentController.delete.error.removeShipmentForbidden'
                    )
                );
            }

            $packages = $this->getPackages();
            foreach ($packages as $package) {
                $packageOrderDetailCollection = $package->getPackageOrderDetailCollection();
                foreach ($packageOrderDetailCollection as $packageOrderDetail) {
                    $packageOrderDetail->delete();
                }
                $package->delete();
            }

            $this->shipment->delete();
            $this->confirmations[] = GeodisServiceTranslation::get(
                'Admin.ShipmentController.delete.success'
            );
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    protected function processShipment()
    {
        $idGroupCarrier = (int) Tools::getValue('groupCarrier');
        $geodisCarrier = new GeodisCarrier((int) Tools::getValue('carrier'));
        $prestation = new GeodisPrestation($geodisCarrier->id_prestation);

        try {
            $removalDateString = Tools::getValue('removal_date');
            if (empty($removalDateString)) {
                $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.missingDate'
                );
            } else {
                $firstDayAvailable = new DateTime(Tools::getValue('firstDayAvailable'));
                $removalDate = new DateTime($removalDateString);
                if ($firstDayAvailable > $removalDate) {
                    $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                        'Admin.ShipmentController.index.error.pastDate'
                    );
                }
            }
            $this->formData['removal_date'] = $removalDateString;
        } catch (Exception $e) {
            $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.invalidDate'
            );
        }

        $nbColis = sizeof(Tools::getValue('package_id'));
        for ($i = 0; $i < $nbColis; $i++) {
            // Weight must be set and a valid float
            if (empty(Tools::getValue('package_weight')[$i]) || (!empty(Tools::getValue('package_weight')[$i])
                && (string) (float) Tools::getValue('package_weight')[$i] != Tools::getValue('package_weight')[$i])
            ) {
                if (!isset($this->packageErrors[$i])) {
                    $this->packageErrors[$i] = array();
                }

                $this->packageErrors[$i][] = GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.invalidWeight'
                );
            }

            // Height must be empty or a valid float
            if (!empty(Tools::getValue('package_height')[$i])
                && (string) (float) Tools::getValue('package_height')[$i] != Tools::getValue('package_height')[$i]
            ) {
                if (!isset($this->packageErrors[$i])) {
                    $this->packageErrors[$i] = array();
                }

                $this->packageErrors[$i][] = GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.invalidHeight'
                );
            }

            // Depth must be empty or a valid float
            if (!empty(Tools::getValue('package_depth')[$i])
                && (string) (float) Tools::getValue('package_depth')[$i] != Tools::getValue('package_depth')[$i]
            ) {
                if (!isset($this->packageErrors[$i])) {
                    $this->packageErrors[$i] = array();
                }

                $this->packageErrors[$i][] = GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.invalidDepth'
                );
            }

            // Width must be empty or a valid float
            if (!empty(Tools::getValue('package_width')[$i])
                && (string) (float) Tools::getValue('package_width')[$i] != Tools::getValue('package_width')[$i]
            ) {
                if (!isset($this->packageErrors[$i])) {
                    $this->packageErrors[$i] = array();
                }

                $this->packageErrors[$i][] = GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.invalidWidth'
                );
            }

            // Volume must me set if prestation if out of France and in all case, it must be a valid float
            if (0 == ((float) Tools::getValue('package_volume')[$i])
                && $prestation->zone != 'France' || (!empty(Tools::getValue('package_volume')[$i])
                && (string) (float) Tools::getValue('package_volume')[$i] != Tools::getValue('package_volume')[$i])
            ) {
                if (!isset($this->packageErrors[$i])) {
                    $this->packageErrors[$i] = array();
                }

                $this->packageErrors[$i][] = (string)GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.invalidVolume'
                );
            }
        }

        $this->formData['package_width'] = Tools::getValue('package_width');
        $this->formData['package_height'] = Tools::getValue('package_height');
        $this->formData['package_depth'] = Tools::getValue('package_depth');
        $this->formData['package_volume'] = Tools::getValue('package_volume');
        $this->formData['package_weight'] = ceil(((float) Tools::getValue('package_weight')) * 100) / 100;
        $this->formData['package_type'] = Tools::getValue('package_type');
        $this->formData['package_wl_fiscal_code'] = Tools::getValue('package_wl_fiscal_code');
        $this->formData['product_wl_active'] = Tools::getValue('product_wl_active');
        $this->formData['product_wl_fiscal_code'] = Tools::getValue('product_wl_fiscal_code');
        $this->formData['product_quantity'] = Tools::getValue('product_quantity');
        $this->formData['package_type'] = Tools::getValue('package_type');

        if (empty($this->productErrors) &&
            empty($this->packageErrors) &&
            empty($this->shipmentErrors)
        ) {
            $totalWeight = 0;
            foreach (Tools::getValue('package_weight') as $weight) {
                $totalWeight += (float) $weight;
            }
            $totalWeight = ceil($totalWeight * 100) / 100;

            $creation = !$this->shipment->id;

            if ($creation) {
                $this->shipment->reference = Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
                $this->shipment->status_code = "default";
                $this->shipment->status_label = (string) GeodisServiceTranslation::get(
                    'Admin.ServiceSynchronize.shipment.statusLabel.waitingTransmission'
                );
                $this->shipment->is_complete = 0;
            }

            $this->shipment->id_order = $this->idOrder;
            $order = new Order($this->idOrder);
            $this->shipment->id_reference_carrier = $order->id_carrier;
            $this->shipment->tracking_number = Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
            $this->shipment->weight = $totalWeight;
            $this->shipment->departure_date = Tools::getValue('removal_date');
            $this->shipment->incident = 0;
            $this->shipment->id_group_carrier = $idGroupCarrier;
            $this->shipment->id_carrier = $geodisCarrier->id;

            // Save order traking
            if (!$this->shipment->id) {
                $orderCarrier = $this->getOrderCarrier($this->order);
                $orderCarrier->tracking_number = $this->shipment->tracking_number;
                $orderCarrier->id_carrier = $geodisCarrier->getGroupCarrier()->getCarrier()->id;
                $orderCarrier->save();
            }

            try {
                $this->shipment->save();
            } catch (Exception $e) {
                $this->shipmentErrors[] = $e->getMessage();
                return;
            }

            if (!$this->shipment->id) {
                $history = new GeodisShipmentHistory();
                $history->id_shipment = $this->shipment->id;
                $history->status_code = $this->shipment->status_code;
                $history->status_label = $this->shipment->status_label;
                $history->save();
            }

            $nbColis = sizeof(Tools::getValue('package_id'));
            for ($packageIndex = 0; $packageIndex < $nbColis; $packageIndex++) {
                $package = new GeodisPackage(Tools::getValue('package_id')[$packageIndex]);

                // Suppression package
                if (Tools::getValue('remove_package')[$packageIndex]) {
                    if ($package->id) {
                        foreach ($package->getPackageOrderDetailCollection() as $packageOrderDetail) {
                            $packageOrderDetail->delete();
                        }
                        $package->delete();
                    }
                    continue;
                }

                $package->reference = Tools::getValue('package_reference')[$packageIndex];
                $package->id_shipment = $this->shipment->id;
                $package->width = Tools::getValue('package_width')[$packageIndex];
                $package->height = Tools::getValue('package_height')[$packageIndex];
                $package->depth = Tools::getValue('package_depth')[$packageIndex];
                $package->weight = ceil(((float) Tools::getValue('package_weight')[$packageIndex]) * 100) / 100;
                $package->volume = Tools::getValue('package_volume')[$packageIndex];
                $package->package_type = Tools::getValue('package_type')[$packageIndex];

                if (!$package->id) {
                    $package->status_code = "default";
                    $package->status_label = "default";
                }

                $package->incident = 0;
                $package->save();

                // pour chaque colis, recupérer chaque produit et la quantité associée
                foreach ($this->items as $itemIndex => $item) {
                    $geodisOrderDetail = $item->getPackageOrderDetail($package);
                    $geodisOrderDetail->id_package = $package->id;
                    $geodisOrderDetail->id_order_detail = $item->getOrderDetail()->id;
                    $geodisOrderDetail->quantity = Tools::getValue('product_quantity')[$packageIndex][$itemIndex];

                    if (!empty(Tools::getValue('product_id_fiscal_code')[$packageIndex][$itemIndex])) {
                        $geodisOrderDetail->is_wine_and_liquor = true;
                        $geodisOrderDetail->id_fiscal_code =
                        (int) Tools::getValue('product_id_fiscal_code')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->nb_col = (int) Tools::getValue('product_nb_col')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->volume_cl =
                        (float) Tools::getValue('product_volume_cl')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->volume_l = Tools::getValue('product_volume_l')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->n_mvt = Tools::getValue('product_n_mvt')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->shipping_duration =
                        Tools::getValue('product_shipping_duration')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->fiscal_code_ref =
                        Tools::getValue('product_fiscal_code_ref')[$packageIndex][$itemIndex];
                        $geodisOrderDetail->n_ea = Tools::getValue('product_n_ea')[$packageIndex][$itemIndex];
                    } else {
                        $geodisOrderDetail->is_wine_and_liquor = false;
                        $geodisOrderDetail->id_fiscal_code = 0;
                        $geodisOrderDetail->nb_col = null;
                        $geodisOrderDetail->volume_cl = null;
                        $geodisOrderDetail->volume_l = null;
                        $geodisOrderDetail->n_mvt = null;
                        $geodisOrderDetail->shipping_duration = null;
                        $geodisOrderDetail->fiscal_code_ref = null;
                        $geodisOrderDetail->n_ea = null;
                    }
                    $geodisOrderDetail->save();

                    $wineLiquor = $item->getWineLiquor();

                    if (!empty(Tools::getValue('product_id_fiscal_code')[$packageIndex][$itemIndex])) {
                        $wineLiquor->is_wine_liquor = true;
                        $wineLiquor->id_fiscal_code = (int) Tools::getValue(
                            'product_id_fiscal_code'
                        )[$packageIndex][$itemIndex];
                    } else {
                        $wineLiquor->is_wine_liquor = true;
                        $wineLiquor->id_fiscal_code = 0;
                    }

                    $wineLiquor->save();
                }
            }
            return true;
        } else {
            $this->fatalError = true;
            return false;
        }
    }

    protected function getOrderCarrier($order)
    {
        $idOrderCarrier = Db::getInstance()->getValue('
            SELECT `id_order_carrier`
            FROM `'._DB_PREFIX_.'order_carrier`
            WHERE `id_order` = '.(int)$order->id);
        if ($idOrderCarrier) {
            $orderCarrier = new OrderCarrier($idOrderCarrier);
        } else {
            $orderCarrier = new OrderCarrier();
            $orderCarrier->id_order = $order->id;
        }

        return $orderCarrier;
    }

    public function getWsAccountPrestationInfo()
    {
        $collection = GeodisAccountPrestation::getCollection();
        $data = array();
        foreach ($collection as $accountPrestation) {
            $data[$accountPrestation->id] = $accountPrestation->manage_wine_and_liquor;
        }
        return $data;
    }

    public function sendShipment()
    {
        $this->shipment->is_complete = 1;
        $this->shipment->save();
        GeodisServiceOrder::getInstance()->updateOrderState($this->idOrder);
        $response = GeodisServiceWebservice::getInstance()->sendShipment(array($this->shipment->recept_number));

        if (!$response) {
            $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.unvailableWSSendShipment'
            );
            return;
        }

        if ($response['ok'] != true) {
            $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                'Admin.ShipmentController.index.error.cannotValidate'
            );
            return;
        }

        $this->shipmentSuccess[] = (string) GeodisServiceTranslation::get(
            'Admin.ShipmentController.index.success.shipmentSend'
        );
    }

    public function submitShipment()
    {
        if (!empty($this->productErrors) ||
            !empty($this->packageErrors) ||
            !empty($this->shipmentErrors)
        ) {
            return;
        }

        $jsonObject = GeodisDataShipment::getInstance()->hydrate($this->shipment);
        $response = GeodisServiceWebservice::getInstance()->sendShipmentRecord($jsonObject);
        if ($response['ok'] == true) {
            $this->shipment->recept_number = $response['contenu']['noRecepisse'];
            $this->shipment->type_position = $response['contenu']['typePosition'];
            $this->shipment->save();

            if ($response['contenu']['localiteExpediteurCorrigee'] != null) {
                $this->shipmentSuccess[] = ((string) GeodisServiceTranslation::get('Admin.info.locality.exped.change'))
                    .$response['contenu']['localiteExpediteurCorrigee']['codePostal']." - "
                    .$response['contenu']['localiteExpediteurCorrigee']['libelleLocalite']
                    ." (".$response['contenu']['localiteExpediteurCorrigee']['codePays'].") ";
            } elseif ($response['contenu']['localiteDestinataireCorrigee'] != null) {
                $this->shipmentSuccess[] = ((string) GeodisServiceTranslation::get('Admin.info.locality.desti.change'))
                    .$response['contenu']['localiteDestinataireCorrigee']['codePostal']." - "
                    .$response['contenu']['localiteDestinataireCorrigee']['libelleLocalite']
                    ." (".$response['contenu']['localiteDestinataireCorrigee']['codePays'].") ";
            }
        } else {
            if (isset($response['texteErreur'])) {
                $message = $response['texteErreur'];
                $position = strpos($message, 'com.geodis');
                $position2 = strpos($message, ': [err.');

                if ($position2 !== false) {
                    if ($position !== false && $position2 < $position) {
                        $position = $position2;
                    }
                }

                if ($position !== false) {
                    if ($position2 !== false) {
                        if ($position !== false && $position2 < $position) {
                            $position = $position2;
                        }
                    }
                    $message = Tools::substr($message, 0, $position-1);
                } elseif ($position2 !== false) {
                    $message = Tools::substr($message, 0, $position2-1);
                }

                $this->shipmentErrors[] = $message;
            } else {
                $this->shipmentErrors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ShipmentController.index.error.cannotValidate'
                );
            }
        }
    }

    public function getLabels()
    {
        $receptNumber = $this->shipment->recept_number;
        echo GeodisServiceWebservice::getInstance()->getPackageLabel(array($receptNumber));
        die();
    }

    public function getPrestationAvailable()
    {
        $logFilePath = _PS_MODULE_DIR_ . 'geodisofficiel/debug_log.txt';

        if (Tools::getValue('weight') != null) {
            $weight = ceil(((float) Tools::getValue('weight')) * 100) / 100;
        } else {
            $weight = 0;
        }
        $nbPackages = (int) Tools::getValue('nbPackages');
        $nbPallets = (int) Tools::getValue('nbPallets');
        $idOrder = (int) Tools::getValue('idOrder');
        $groupCarrierReference = Tools::getValue('groupCarrierReference');

        $order = new Order($idOrder);

        $defaultSite = null;
        $idLang = Context::getContext()->language->id;
        $geodisSiteCollection = GeodisSite::getcollection();
        foreach ($geodisSiteCollection as $geodisSite) {
            if ($geodisSite->default[$idLang] == 1) {
                $defaultSite = $geodisSite;
            }
        }

        $isoCoutrySender = Country::getIsoById($defaultSite->id_country);

        $address = new Address($order->id_address_delivery);
        if ($address->company != null && $address->company != '') {
            $recipientType = "PRO";
        } else {
            $recipientType = "PAR";
        }

        $isoCountryRecipient = Country::getIsoById($address->id_country);

        try {

            $response = GeodisServiceWebservice::getInstance()->getAccountPrestation(
                $isoCountryRecipient,
                $isoCoutrySender,
                $nbPackages,
                $nbPallets,
                $weight,
                $recipientType
            );
            $idPrestationAvailableList = array();

            foreach ($response['contenu'] as $accountPrestation) {
                $prestation = $accountPrestation['prestationCommerciale'];
                $compte = $accountPrestation['compte'];
                $geodisPrestation = GeodisPrestation::getFromExternal(
                    $prestation['codeGroupeProduits'],
                    $prestation['codeProduit'],
                    $prestation['codeOption'],
                    $compte['codeSa'],
                    $compte['codeClient'],
                    'PREPA.EXPE',
                    true
                );

                $prestation['rdv'] = false;
                $prestation['rdw'] = false;

                foreach ($accountPrestation['listOptions'] as $option) {
                    switch ($option['code']) {
                        case 'ET2':
                            $prestation['livEtage'] = true;
                            break;
                        case 'ETC':
                            $prestation['miseLieuUtil'] = true;
                            break;
                        case 'DPO':
                            $prestation['depotage'] = true;
                            break;
                        case 'RDV':
                            $prestation['rdv'] = true;
                            break;
                        case 'RDW':
                            $prestation['rdw'] = true;
                            break;
                    }
                }

                switch ($groupCarrierReference) {
                    case 'rdv':
                        if (!$prestation['rdv'] && $geodisPrestation->tel_appointment) {
                            break;
                        }
                        // no break
                    default:
                        $idPrestationAvailableList[] = (string) $geodisPrestation->id;
                }
            }

            // remove multiple prestation ids (because web service returns prestation-account association)
            $idPrestationAvailableList = array_values(array_unique($idPrestationAvailableList));

            echo json_encode($idPrestationAvailableList);
            die();
        } catch (Exception $e) {
            echo '[]';
            die();
        }
    }
}
