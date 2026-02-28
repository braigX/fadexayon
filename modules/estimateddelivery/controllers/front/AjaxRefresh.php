<?php
/**
 ** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class EstimatedDeliveryAjaxRefreshModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjaxCart()
    {
        if (!$this->module->is_active) {
            return '';
        }
        if (Tools::isSubmit('ajax')
            && Tools::getValue('token') == Configuration::get('ED_AJAX_TOKEN')
            && (Tools::getIsset('ajaxRefresh') || Tools::getIsset('modalAction') || Tools::getIsset('action_on_cart'))) {
            $id_product = Tools::getValue('id_product');
            if (Tools::getIsset('id_product_attribute')) {
                $id_product_attribute = Tools::getValue('id_product_attribute');
            } else {
                if (Tools::getIsset('ipa')) {
                    $id_product_attribute = Tools::getValue('ipa');
                } else {
                    $id_product_attribute = 0;
                }
            }
            $quantity_wanted = -1;
            if (Tools::getIsset('quantity_wanted')) {
                $quantity_wanted = Tools::getValue('quantity_wanted');
            } elseif (Tools::getIsset('qty')) {
                $quantity_wanted = Tools::getValue('qty');
            }

            $context = Context::getContext();
            if (isset($context->cart)) {
                $cart = $context->cart;
                // get the correct quantity_wanted from the product in cart
                $products = $context->cart->getProducts();
                foreach ($products as $product) {
                    if ($product['id_product'] == $id_product && $product['id_product_attribute'] == $id_product_attribute) {
                        $quantity_wanted = (int) $product['cart_quantity'];
                    }
                }
            } else {
                $cart = new Cart();
            }

            $product = new Product((int) $id_product);
            $arrProduct = (array) $product;
            $arrProduct['id'] = $id_product;
            $arrProduct['id_product'] = $id_product;
            $arrProduct['id_product_attribute'] = $id_product_attribute;
            $arrProduct['quantity'] = $quantity_wanted;
            $arrProduct['quantity_available'] = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);

            $params = [
                'product' => $arrProduct,
            ];

            $cart->id_lang = (int) $context->cookie->id_lang;
            $cart->id_currency = (int) $context->cookie->id_currency;
            $cart->id_guest = (int) $context->cookie->id_guest;
            $cart->id_shop_group = (int) $context->shop->id_shop_group;
            $cart->id_shop = $context->shop->id;

            if ($context->customer->isLogged()) {
                $cart->id_customer = (int) $context->customer->id;
                $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                $cart->id_address_invoice = (int) $cart->id_address_delivery;
            } else {
                $cart->id_address_delivery = 0;
                $cart->id_address_invoice = 0;
            }
            $params = array_merge(
                $params,
                [
                    'cart' => $cart,
                ]
            );
            if (in_array(Configuration::get('ED_STYLE'), [2, 3])) {
                $cd_limit = Configuration::get('ED_COUNTDOWN_LIMIT');
                if ($cd_limit != '' && (int) $cd_limit > 0) {
                    $this->context->smarty->assign(['ed_countdown_limit' => Configuration::get('ED_COUNTDOWN_LIMIT')]);
                }
            }
            if (Tools::getIsset('action_on_cart')) {
                $summary = ((int) Tools::getValue('ed_display_option') == 1) ? true : false;
                if ((int) Tools::getValue('ed_display_option') == 1) {
                    $return = $this->module->displayCarriersOnCart($params, $summary);
                } else {
                    $return = $this->module->hookDisplayCartSummaryProductDelivery($params, true);
                }
            } else {
                if (Tools::getIsset('ajaxRefresh')) {
                    $return = $this->module->generateEstimatedDelivery($params, $id_product, $id_product_attribute, $quantity_wanted);
                } elseif (Tools::getIsset('modalAction')) {
                    // Modal action
                    $return = $this->module->displayEDsOnAjaxCartModal($params);
                }
            }
            echo json_encode($return);
            exit;
        }
    }

    public function displayAjaxCalendarRefresh()
    {
        $this->prepareOrderData();
        $params = ['cart' => $this->context->cart];
        $id_zone = $this->module->getIdZone();
        if ($id_zone) {
            $id_customer = $this->context->customer->id ?? $this->context->cart->id_customer ?? 0;
            $carriers = $this->module->getCarriersForOrder($id_customer, $params);
        } else {
            echo json_encode(['return' => 'error', 'message' => 'Id Zone couldn\'t be located']);
            exit;
        }

        $products = $this->getOrderProducts();

        $both_deliveries = [false, false];
        $relandavail = [];

        $deliveries = $this->module->getDeliveriesFromProductList($params, $products, $both_deliveries, $relandavail);
        if ($deliveries !== false && count($deliveries) > 0) {
            // Add calendar days
            $deliveries = $this->module->addCalendarDays($deliveries);

            // Load dateformat and locales for the datepicker
            $this->module->loadCalendarParams();

            // Assign the data
            // Try to assign the current selected carrier or the first one if is not available or selected in the order process
            $this->context->smarty->assign(
                [
                    'id_carrier' => $this->context->cart->id_carrier ?? $carriers[0]['id_carrier'],
                    'ed_cart' => $deliveries,
                ]
            );

            // If the date was previously selected and the current options doesn't contain the same date remove the preselected date
            $selectedDaterevoked = false;
            if ($this->context->cookie->__isset('ed_calendar_date')) {
                $selectedDate = $this->context->cookie->__get('ed_calendar_date');
                foreach ($deliveries as $delivery) {
                    $id_carrier = (int) $this->context->cart->id_carrier;
                    if ($delivery->dc->id_carrier == $id_carrier && !in_array($selectedDate, $delivery->calendar_dates)) {
                        $this->context->cookie->__unset('ed_calendar_date');
                        break;
                    }
                }
            }
            echo json_encode(
                [
                    'return' => 'ok',
                    'message' => $this->module->display(_PS_MODULE_DIR_ . $this->module->name . '/', 'views/templates/hook/ed-calendar-delivery-display.tpl'),
                    'selectedDateRevoked' => $selectedDaterevoked,
                ]
            );
        } else {
            echo json_encode(['return' => 'error', 'message' => 'Deliveries could not be generated']);
        }
        exit;
    }

    public function displayAjaxCartUpdate()
    {
        $deliveries = $this->module->displayCarriersOnCart([], true, false);
        if ($deliveries !== false) {
            echo json_encode(['success' => true, 'deliveries' => $deliveries]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function displayAjaxProductSummaryUpdate()
    {
        $products = $this->getOrderProducts();
        $ret = [];
        foreach ($products as $product) {
            $ret[] = [
                'id_product' => $product['id_product'],
                'id_product_attribute' => $product['id_product_attribute'],
                'html' => $this->module->hookDisplayCartSummaryProductDelivery(['product' => $product], true),
            ];
        }
        echo json_encode($ret);
        exit;
    }

    private function prepareOrderData()
    {
        /* Get the Carriers */
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }

        if (isset($this->context->cart->id_customer) && (!isset($this->module->id_zone) || $this->module->id_zone == 0)) {
            $this->module->getAddrCarriers($this->context->cart, true);
        } else { // in case of the guest or visitor
            $this->module->getIpCarriers();
        }
    }

    private function getOrderProducts()
    {
        /* Get the Products */
        $products = $this->context->cart->getProducts();

        // Make sure gifts doesn't interfere with ED
        return $this->module->removeGifts($products);
    }
}
