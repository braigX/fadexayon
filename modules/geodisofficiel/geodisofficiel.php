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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/globalConf.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbInstall.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbUninstall.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTabs.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceSynchronize.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceCron.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';

class GeodisOfficiel extends CarrierModule
{
    protected $config_form = false;
    public $id_carrier;
    protected $availablePrestations;

    public function __construct()
    {
        $this->name = 'geodisofficiel';

        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';


        $this->author = 'NOVATIS Agency';

        $this->module_key = '07d78fcb0b8111b8cf1ad11101e2f20f';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->defineTabs();

        $this->displayName = 'GEODIS V8';

        $this->description = $this->l("Offer your customers GEODIS delivery services to France or Europe, in standard or express delivery. With this module, you also benefit from the management of your orders from your backoffice.");

        if ($this->id) {
            /* // Comment this line to update the translation
            GeodisDbTranslation::getInstance()->init();
            //*/

            /* // Comment this line to force upgrade database
            (new GeodisDbInstall())->run();
            //*/

             // Comment this line to update menu tab
            GeodisTabs::getInstance($this)->update();
            //*/
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
{
    if (!extension_loaded('curl')) {
        $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
        return false;
    }

    // Call parent install function
    if (!parent::install()) {
        return false;
    }

    // Register hooks
    $hooks = [
        'actionAdminControllerSetMedia',
        'displayBeforeCarrier',
        'displayCarrierExtraContent',
        'displayHeader',
        'moduleRoutes',
    ];

    foreach ($hooks as $hook) {
        if (!$this->registerHook($hook)) {
            $this->_errors[] = $this->l("Failed to register hook: $hook");
            return false;
        }
    }

    // Perform module-specific installation logic
    $install = new GeodisDbInstall();
    if (!$install->initConfiguration()) {
        $this->_errors[] = $this->l("Failed to initialize module configuration.");
        return false;
    }

    // Return true if installation succeeds
    return true;
}



    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $adminLink = $this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX . 'Index');

        // Display the button in the configuration page
        return $this->displayButton($adminLink);
    }

    private function displayButton($adminLink)
    {
        return '<div class="panel">
                    <h3>' . $this->displayName . '</h3>
                    <p>' . $this->l('Click the button below to go to the module admin page:') . '</p>
                    <a href="' . htmlspecialchars($adminLink) . '" class="btn btn-primary">
                        ' . $this->l('Go to Module Admin Page') . '
                    </a>
                </div>';
    }
//    public function getContent()
//    {
//        return Tools::redirectAdmin($this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'Index'));
//    }

    public function uninstall()
    {
        $uninstall = new GeodisDbUninstall();

        if (!$uninstall->run()) {
            return false;
        }

        try {
            return parent::uninstall();
        } catch (Exception $e) {
            // Bug on PrestaShop Tab removing
            return true;
        }
    }

    public function getOrderShippingCost($cart, $shippingCost)
    {
        $lowerPrice = -1;
        $lowerCarrier = null;
        if (!GeodisServiceConfiguration::getInstance()->get('active')) {
            return false;
        }

        try {
            $this->getAvailablePrestations();

            $checkAvailablePrestation = true;
        } catch (Exception $e) {
            // If webservice is down, disable carrier
            //$checkAvailablePrestation = false;
            return false;
        }

        //$idAddressDelivery = Context::getContext()->cart->id_address_delivery;
        // $address = new Address($idAddressDelivery);

        // Get carrier group
        $groupCarrier = GeodisGroupCarrier::loadFromIdCarrier($this->id_carrier);

        if (!$groupCarrier || !$groupCarrier->active) {
            return false;
        }

        if ($checkAvailablePrestation) {
            $hasPrestationsAvailable = false;
            foreach ($groupCarrier->getCarrierCollection() as $carrier) {
                if (!$this->prestationIsAvailable($carrier->getPrestation(), $groupCarrier->reference)) {
                    continue;
                }

                if ($carrier->free_shipping_from > 0) {
                    $cart = Context::getContext()->cart;
                    if ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $carrier->free_shipping_from) {
                        if (!$carrier->additional_shipping_cost) {
                            $shippingCost = 0;
                        }
                        $lowerPrice = 0 + $shippingCost;
                        $lowerCarrier = $carrier;
                    } elseif ($carrier->price < $lowerPrice || $lowerPrice == -1) {
                        $lowerPrice = $carrier->price;
                        $lowerCarrier = $carrier;
                    }
                } elseif ($carrier->price < $lowerPrice || $lowerPrice == -1) {
                    $lowerPrice = $carrier->price;
                    $lowerCarrier = $carrier;
                }

                $hasPrestationsAvailable = true;
            }

            if (!$hasPrestationsAvailable) {
                return false;
            }
        }

        // Get cart carrier
        $cartCarrier = GeodisCartCarrier::loadFromIdCart($cart->id);

        // Get carrier (geodis)
        if ($cartCarrier) {
            $carrier = $cartCarrier->getCarrier();
        }

        if (!$cartCarrier || $carrier->id_group_carrier != $groupCarrier->id) {
            $carrier = $lowerCarrier;
            $price = $lowerPrice;
        } else {
            $price = $carrier->price;

            foreach ($cartCarrier->getCarrierOptionCollection() as $carrierOption) {
                $price += $carrierOption->price_impact;
            }
        }

        if ($carrier->free_shipping_from > 0) {
            $cart = Context::getContext()->cart;
            if ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $carrier->free_shipping_from) {
                $price = 0;

                if (!$carrier->additional_shipping_cost) {
                    $shippingCost = 0;
                }
            }
        }

        return $price + $shippingCost;
    }

    public function getOrderShippingCostExternal($cart)
    {
        return $this->getOrderShippingCost($cart, 0);
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/adminMenu.css', 'all');
    }

    public function hookDisplayBeforeCarrier($params)
    {
        Context::getContext()->smarty->assign(
            'geodisLoadGoogleMapJs',
            GeodisServiceConfiguration::getInstance()->get('map_enabled')
            && GeodisServiceConfiguration::getInstance()->get('load_google_map_js')
        );
        Context::getContext()->smarty->assign(
            'geodisGoogleMapApiKey',
            GeodisServiceConfiguration::getInstance()->get('google_map_api_key')
        );
        Context::getContext()->smarty->assign(
            'geodisGoogleMapClient',
            GeodisServiceConfiguration::getInstance()->get('google_map_client')
        );

        return $this->display(
            _PS_MODULE_DIR_.'geodisofficiel//'.GEODIS_MODULE_NAME.'.php',
            'views/templates/front/hook/beforeCarrier.tpl'
        );
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        // Get carrier group
        $groupCarrier = GeodisGroupCarrier::loadFromIdCarrier($params['carrier']['id']);

        $cartCarrier = GeodisCartCarrier::loadFromIdCart(Context::getContext()->cart->id);
        $defaultCarrier = 0;
        if ($cartCarrier) {
            $defaultCarrier = $cartCarrier->id_carrier;
        }

        if (!$groupCarrier) {
            return false;
        }

        Context::getContext()->smarty->assign(
            'geodisIdReferenceCarrier',
            (int)$params['carrier']['id_reference']
        );
        Context::getContext()->smarty->assign(
            'geodisCarrierCollection',
            $groupCarrier->getCarrierCollection()
        );
        Context::getContext()->smarty->assign(
            'geodisDefaultCarrier',
            $defaultCarrier
        );

        Context::getContext()->smarty->assign(
            'groupCarrierId',
            $params['carrier']['id']
        );
        Context::getContext()->smarty->assign(
            'jsonConfig',
            $this->getJsonConfig($groupCarrier, $params['carrier']['id'], $params['cart']->id_address_delivery)
        );
        Context::getContext()->smarty->assign(
            'jsonValues',
            $this->getJsonValues()
        );
        Context::getContext()->smarty->assign(
            'intlUtilsUrl',
            $this->context->link->getBaseLink(null, true)
            .'/modules/'.GEODIS_MODULE_NAME.'/views/js/lib/intlTel/utils.js'
        );

        $url = $this->context->link->getModuleLink(
            $this->name,
            'getJsTemplate',
            array(),
            true
        );

        Context::getContext()->smarty->assign(
            'getTemplateUrl',
            $this->context->link->getModuleLink(
                $this->name,
                'getJsTemplate',
                array(),
                true
            )
        );
        Context::getContext()->smarty->assign(
            'submitUrl',
            $this->context->link->getModuleLink(
                $this->name,
                'setCarrier',
                array(),
                true
            )
        );
        Context::getContext()->smarty->assign(
            'pointListUrl',
            $this->context->link->getModuleLink(
                $this->name,
                'getPickupPointList',
                array(),
                true
            )
        );
        Context::getContext()->smarty->assign(
            'formatPriceUrl',
            $this->context->link->getModuleLink(
                $this->name,
                'formatPrice',
                array(),
                true
            )
        );
        Context::getContext()->smarty->assign(
            'markerShopUrl',
            $this->context->link->getBaseLink(null, true)
            .'modules/'.GEODIS_MODULE_NAME.'/views/img/css/popin/flag-shop-'
            .(GEODIS_MODULE_NAME == 'geodisofficiel' ? 'geodis' : 'fe').'.png'
        );
        Context::getContext()->smarty->assign(
            'markerHomeUrl',
            $this->context->link->getBaseLink(null, true)
            .'modules/'.GEODIS_MODULE_NAME.'/views/img/css/popin/flag-home.png'
        );
        Context::getContext()->smarty->assign(
            'markerSelectedShopUrl',
            $this->context->link->getBaseLink(null, true)
            .'modules/'.GEODIS_MODULE_NAME.'/views/img/css/popin/flag-shop-selected.png'
        );
        Context::getContext()->smarty->assign(
            'sentenceDistanceMeters',
            (string) GeodisServiceTranslation::get('front.popin.point.distanceMeters.@')
        );
        Context::getContext()->smarty->assign(
            'sentenceDistanceKilometers',
            (string) GeodisServiceTranslation::get('front.popin.point.distanceKilometers.@')
        );
        Context::getContext()->smarty->assign(
            'geodisModuleName',
            GEODIS_MODULE_NAME
        );
        Context::getContext()->smarty->assign(
            'geodisMapEnabled',
            GeodisServiceConfiguration::getInstance()->get('map_enabled')
        );
        Context::getContext()->smarty->assign(
            'apiConfig',
            $this->getApiConfig()
        );

        $html = $this->display(
            _PS_MODULE_DIR_.'geodisofficiel//'.GEODIS_MODULE_NAME.'.php',
            'views/templates/front/hook/carrierExtra.tpl'
        );
        return $html;
    }

    protected function getAvailablePrestations()
    {
        try {
            if (!$this->availablePrestations) {
                $webService = GeodisServiceWebservice::getInstance();

                // Get default site
                $site = GeodisSite::getDefault();

                // Get country from site or default country if site is not defined
                if ($site) {
                    $senderCountry = $site->getCountry();
                } else {
                    $senderCountry = Context::getContext()->country;
                }


                $cart = Context::getContext()->cart;
                $idAddressDelivery = $cart->id_address_delivery;
                $recipientAddress = new Address($idAddressDelivery);

                // Get country of recipient
                $recipientCountry = new Country($recipientAddress->id_country);


                $result = $webService->getAccountPrestation(
                    $recipientCountry->iso_code,
                    $senderCountry->iso_code,
                    1,
                    0,
                    (float) $cart->getTotalWeight(),
                    empty($recipientAddress->company) ? 'PAR' : 'PRO'
                );

                if (!isset($result['contenu'])) {
                    throw new \Exception("Error Processing Request", 1);
                }

                $this->availablePrestations = array();
                foreach ($result['contenu'] as $row) {
                    $prestation = $row['prestationCommerciale'];
                    $prestation['depotage'] = false;
                    $prestation['livEtage'] = false;
                    $prestation['miseLieuUtil'] = false;
                    $prestation['rdv'] = false;
                    $prestation['rdw'] = false;

                    foreach ($row['listOptions'] as $option) {
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

                    $this->availablePrestations[] = array(
                        'codeGroupeProduits' => $prestation['codeGroupeProduits'],
                        'codeProduit' => $prestation['codeProduit'],
                        'codeOption' => $prestation['codeOption'],
                        'depotage' => $prestation['depotage'],
                        'livEtage' => $prestation['livEtage'],
                        'miseLieuUtil' => $prestation['miseLieuUtil'],
                        'rdv' => $prestation['rdv'],
                        'rdw' => $prestation['rdw'],
                    );
                }
            }

            return $this->availablePrestations;
        } catch (Exception $e1) {
            throw new \Exception("Error getAvailablePrestation", 1);
        }
    }

    protected function prestationIsAvailable($prestation, $groupCarrierReference = 'classic')
    {
        foreach ($this->getAvailablePrestations() as $availablePrestation) {
            if ($prestation->code_groupe_produits == $availablePrestation['codeGroupeProduits']
                && $prestation->code_produit == $availablePrestation['codeProduit']
                && $prestation->code_option == $availablePrestation['codeOption']
            ) {
                switch ($groupCarrierReference) {
                    case 'rdv':
                        if (!$availablePrestation['rdv'] && $prestation->tel_appointment) {
                            return false;
                        }
                        break;
                }
                return true;
            }
        }

        return false;
    }

    protected function optionIsAvailable($option, $prestation)
    {
        foreach ($this->getAvailablePrestations() as $availablePrestation) {
            if ($prestation->code_groupe_produits == $availablePrestation['codeGroupeProduits']
                && $prestation->code_produit == $availablePrestation['codeProduit']
                && $prestation->code_option == $availablePrestation['codeOption']
            ) {
                return $availablePrestation[$option->attribute];
            }
        }

        return false;
    }

    protected function getJsonConfig($groupCarrier, $idPCarrier, $idAddressDelivery)
    {
        $idCarrier = 0;
        $whiteLabel = GeodisServiceConfiguration::getInstance()->get('use_white_label');
        if ($whiteLabel) {
            $carrier = Carrier::getCarrierByReference($groupCarrier->id_reference_carrier);

            if ($carrier) {
                $idCarrier = $carrier->id;
            }
        }

        $config = array(
            'carrierList' => array(),
            'popinTitle' => $groupCarrier->getCarrier()->name,
            'whiteLabel' => $whiteLabel,
            'whiteLabelImageUrl' =>  __PS_BASE_URI__.'img/s/'.$idCarrier.'.jpg',
            'popinSubtitle' => $groupCarrier->getCarrier()->delay[Context::getContext()->language->id],
            'description' => (string) GeodisServiceTranslation::get(
                'front.popin.description.'.$groupCarrier->reference
            ),
        );

        try {
            $this->getAvailablePrestations();
            $checkAvailablePrestation = true;
        } catch (Exception $e) {
            // If webservice is down, allow all configuration
            $checkAvailablePrestation = false;
        }

        // Get carrier prestashop taxesRate
        $taxesRate = 0.0;
        if (!empty($idPCarrier) && !empty($idAddressDelivery)) {
            $pCarrier = new Carrier($idPCarrier);
            $addressDelivery = new Address($idAddressDelivery);
            $taxesRate = $pCarrier->getTaxesRate($addressDelivery);
        }

        foreach ($groupCarrier->getCarrierFilteredCollection() as $carrier) {
            $optionCollection = $carrier->getCarrierOptionCollection(true);
            $optionList = array();

            if ($checkAvailablePrestation) {
                if (!$this->prestationIsAvailable($carrier->getPrestation(), $groupCarrier->reference)) {
                    continue;
                }
            }

            $optionApplyPrice = true;
            if ($carrier->free_shipping_from > 0) {
                $cart = Context::getContext()->cart;
                if ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $carrier->free_shipping_from) {
                    $optionApplyPrice = false;
                }
            }

            foreach ($optionCollection as $option) {
                if ($checkAvailablePrestation) {
                    if (!$this->optionIsAvailable($option->getOption(), $carrier->getPrestation())) {
                        continue;
                    }
                }

                $optionPriceImpact = $option->price_impact;
                if (($optionPriceImpact > 0) && ($taxesRate > 0)) {
                    $optionPriceImpact = $optionPriceImpact + ($optionPriceImpact * ($taxesRate / 100));
                }

                $optionList[] = array(
                    'id_option' => $option->getOption()->id,
                    'name' => (string) GeodisServiceTranslation::get(
                        'front.popin.option.name.'.$option->getOption()->code
                    ),
                    'desc' => (string) GeodisServiceTranslation::get(
                        'front.popin.option.description.'.$option->getOption()->code
                    ),
                    'price_impact' => $optionApplyPrice ? (float) $optionPriceImpact : 0,
                    'code' => $option->getOption()->code,
                );
            }

            $prestation = $carrier->getPrestation();
            $descriptionType = '.web';
            $suffix = $groupCarrier->reference
                        .'.'
                        .Tools::strtolower($prestation->zone);

            if ($groupCarrier->reference == 'rdv') {
                if ($prestation->tel_appointment) {
                    $suffix .= '.tel';
                    $descriptionType = '.tel';
                } elseif ($prestation->web_appointment) {
                    $suffix .= '.web';
                    $descriptionType = '.web';
                }
            }

            if ($prestation->type == 'EXP' || $prestation->type == 'EPI') {
                $suffix .= '.exp';
            }

            $config['carrierList'][] = array(
                'name' => (string) GeodisServiceTranslation::get(
                    'front.popin.prestation.name.'.$suffix
                ),
                'desc' => (string) GeodisServiceTranslation::get(
                    'front.popin.prestation.description.'.$suffix
                ),
                'longdesc' => (string) GeodisServiceTranslation::get(
                    'front.popin.prestation.longDescription.'.$suffix
                ),
                'id_carrier' => $carrier->id,
                'price' => (float) $carrier->getInitialPriceFromCart(Context::getContext()->cart, $idPCarrier),
                'optionList' => $optionList,
                'appointmentType' => (string) GeodisServiceTranslation::get(
                    'front.popin.contactDescription'.$descriptionType
                ),
            );
        }

        $config['carrierType'] = ($groupCarrier->reference === 'relay') ? 'relay' : 'classic';
        $config['carrierType2'] = $groupCarrier->reference;

        return json_encode($config);
    }

    public function getApiConfig()
    {
        $data = array();
        $data['geodisGoogleMapApiKey'] = GeodisServiceConfiguration::getInstance()->get('google_map_api_key');
        $data['geodisGoogleMapClient'] = GeodisServiceConfiguration::getInstance()->get('google_map_client');

        return json_encode($data);
    }

    public function getJsonValues()
    {
        $cartCarrier = GeodisCartCarrier::loadFromIdCart(Context::getContext()->cart->id);

        if ($cartCarrier) {
            $data = $cartCarrier->getDataForJson();
        } else {
            $data = array(
                'idCarrier' => null,
                'idOptionList' => array(),
                'codeWithdrawalPoint' => null,
                'codeWithdrawalAgency' => null,
                'info' => array(),
            );
        }

        $data['email'] = Context::getContext()->customer->email;
        $data['countryCode'] = Context::getContext()->country->iso_code;

        $idAddressDelivery = Context::getContext()->cart->id_address_delivery;
        $address = new Address($idAddressDelivery);

        $data['telephone1'] = $address->phone;
        $data['telephone2'] = $address->phone_mobile;

        if ($address) {
            $country = new Country($address->id_country);
            $data['address'] = array(
                'address1' => $address->address1,
                'address2' => $address->address2,
                'city' => $address->city,
                'zipCode' => $address->postcode,
                'countryCode' => $country->iso_code,
            );
        }

        $data['token'] = $this->context->cookie->geodisToken;

        return json_encode($data);
    }

    public function hookDisplayHeader($params)
    {
        if (empty($this->context->cookie->geodisToken)) {
            $this->context->cookie->geodisToken = Tools::encrypt(uniqid());
        }

        Media::addJsDef(
            array(
                'geodisSetCarrierUrl' => $this->context->link->getModuleLink(
                    //$this->name,
                    'geodis',
                    'setCarrier',
                    array(),
                    Tools::usingSecureMode()
                ),
                'geodisToken' => $this->context->cookie->geodisToken,
            )
        );
        $this->context->controller->addJS(($this->_path).'views/js/GeodisCarrierSelectorBootstrap.js');
        $this->context->controller->addJS(($this->_path).'views/js/GeodisTemplate.js');
        $this->context->controller->addJS(($this->_path).'views/js/GeodisCarrierSelector.js');
        $this->context->controller->addJS(($this->_path).'views/js/GeodisMap.js');
        $this->context->controller->addJS(($this->_path).'views/js/lib/intlTel/intlTelInput.min.js');
        $this->context->controller->addCss(($this->_path).'views/css/front.css');
        $this->context->controller->addCss(($this->_path).'views/css/intlTelInput.min.css');
        $this->context->controller->addjQueryPlugin(array('fancybox'));
    }

    public function hookModuleRoutes()
    {
        return array(
            'module-geodis-shipmentStatus' => array(
                'controller' => 'shipmentStatus',
                'rule' => 'shipmentStatus/{tracking_number}',
                'keywords' => array(
                    'tracking_number' => array('regexp' => '[\w]+', 'param' => 'tracking_number'),
                    'module'  => array('regexp' => '[\w]+', 'param' => 'module'),
                    'controller' => array('regexp' => '[\w]+',  'param' => 'controller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'geodis',
                    'controller' => 'shipmentStatus',
                )
            ),
        );
    }

    protected function defineTabs()
    {
        $this->tabs = array(
            array(
                'name' => $this->l('My informations'),
                'class_name' => GEODIS_ADMIN_PREFIX.'Information',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('Module'),
                'class_name' => GEODIS_ADMIN_PREFIX.'Index',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => (GEODIS_NAME == 'GEODIS')?
                    $this->l('GEODIS Carrier') :
                    $this->l('France Express Carrier'),
                'class_name' => GEODIS_ADMIN_PREFIX.'ConfigurationBack',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => true,
            ),
            array(
                'name' => $this->l('My configuration front'),
                'class_name' => GEODIS_ADMIN_PREFIX.'ConfigurationFront',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('My addresses'),
                'class_name' => GEODIS_ADMIN_PREFIX.'Address',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => (GEODIS_NAME == 'GEODIS')?
                    $this->l('GEODIS Orders') :
                    $this->l('France Express Orders'),
                'class_name' => GEODIS_ADMIN_PREFIX.'OrdersGrid',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => true,
            ),
            array(
                'name' => $this->l('My shipments'),
                'class_name' => GEODIS_ADMIN_PREFIX.'Shipment',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => (GEODIS_NAME == 'GEODIS')?
                    $this->l('My GEODIS Removals') :
                    $this->l('My France Express removals'),
                'class_name' => GEODIS_ADMIN_PREFIX.'Removal',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => true,
            ),
            array(
                'name' => $this->l('My log'),
                'class_name' => GEODIS_ADMIN_PREFIX.'LogsGrid',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('Print'),
                'class_name' => GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('My shipments'),
                'class_name' => GEODIS_ADMIN_PREFIX.'ShipmentsGridTransmit',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('Introduction'),
                'class_name' => GEODIS_ADMIN_PREFIX.'AddressConfiguration',
                'ParentClassName' => 'AdminParentShipping',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('CronJob'),
                'class_name' => GEODIS_ADMIN_PREFIX.'CronGrid',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
            array(
                'name' => $this->l('Cron'),
                'class_name' => GEODIS_ADMIN_PREFIX.'CronJob',
                'ParentClassName' => 'AdminParentOrders',
                'icon' => 'geodis',
                'visible' => false,
            ),
        );
    }
}