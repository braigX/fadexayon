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
 * *                                                 *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminEstimatedDeliveryController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function ajaxProcessEDUpdate()
    {
        if ($this->ajax == true) {
            if (Tools::getValue('ed_token') == Tools::getAdminToken('AdminOrders')) {
                if (!isset($this->module)) {
                    $this->module = new EstimatedDelivery();
                }
                $id_order = (int) Tools::getValue('id_order');
                if (Tools::getIsset('ed_update_type')) {
                    $type = Tools::getValue('ed_update_type');
                    $newDelivery = [];
                    if (Tools::getIsset('force_date') == false) {
                        $force_date = date('Y-m-d H:i', strtotime(Tools::getValue('ed_update_date')));
                    } else {
                        $force_date = date('Y-m-d H:i', strtotime(Tools::getValue('force_date')));
                    }

                    $this->module->setBetterLocales();
                    $order = new Order($id_order);
                    if ($type == 'calendar') {
                        $this->module->saveCalendarDates($force_date, $order);
                        $newDelivery = [
                            'success' => 1,
                            'delivery_min' => $force_date,
                            'delivery_max' => $force_date,
                        ];
                    } elseif ($type != 'shipping') {
                        $newDelivery = (array) $this->updateDelivery($order, $force_date, $type == 'picking');
                        $newDelivery['success'] = (bool) $newDelivery;
                    } else {
                        // Proceed From Shipping, calculate the shipping days and update the delivery date
                        // It's a shipping update
                        if (Tools::getIsset('id_carrier') && (int) Tools::getValue('id_carrier') > 0) {
                            $id_carrier = (int) Tools::getValue('id_carrier');
                        } else {
                            $id_carrier = (int) $order->id_carrier;
                        }
                        $id_carrier = new Carrier($id_carrier);
                        // Get the id and the reference just in case the carrier has changed.
                        $carrier = [$id_carrier->id, $id_carrier->id_reference];
                        $carrier = $this->module->getCarriersFromIds($carrier);

                        // Get the first result
                        $carrier = $carrier[0];
                        $dh = new DeliveryHelper();
                        $force_date = date('Y-m-d', strtotime($force_date));
                        $newDelivery['delivery_max'] = $newDelivery['delivery_cmp_max'] = $dh->addDaysIteration($force_date, $carrier['picking_days'], $carrier['max'] - $carrier['min']);
                        $newDelivery['delivery_max'] = EDTools::setDateFormatForED($newDelivery['delivery_max'], EDTools::getDateFormat('base_df'));
                        $newDelivery['delivery_min'] = $newDelivery['delivery_cmp_min'] = $force_date;
                        $newDelivery['delivery_min'] = EDTools::setDateFormatForED($newDelivery['delivery_min'], EDTools::getDateFormat('base_df'));

                        $picking = $this->module->isAdvPicking() ? $carrier['picking_days'] : Configuration::get('ed_picking_days');

                        // Get picking date for order new code, get picking date when customer selct date from calendar only
                        $shipping_date = $dh->addDaysIteration($force_date, $picking, $carrier['min'], true);
                        $newDelivery['picking_day'] = $shipping_date = $dh->checkNext('shipping', $shipping_date, $picking, '', $return = 'date', false, true);
                        // Can't be an undefined delivery
                        $newDelivery['undefined_delivery'] = 0;
                        $success = 0;
                        if (Validate::isDate($newDelivery['delivery_cmp_min']) && Validate::isDate($newDelivery['delivery_cmp_max'])) {
                            // Normalize the dates
                            $newDelivery['delivery_min'] = date('Y-m-d', strtotime($newDelivery['delivery_cmp_min']));
                            $newDelivery['delivery_max'] = date('Y-m-d', strtotime($newDelivery['delivery_cmp_max']));
                            unset($newDelivery['delivery_cmp_max'], $newDelivery['delivery_cmp_min']);
                            if (Db::getInstance()->update('ed_orders', $newDelivery, 'id_order = ' . $id_order) === false) {
                                Tools::dieObject(Db::getInstance()->getMsgError(), false);
                            } else {
                                $success = 1;
                            }
                        } else {
                            $success = 0;
                        }
                        $newDelivery = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'ed_orders WHERE id_order = ' . $id_order) + ['success' => $success];
                    }
                    // Print the saved value in the database to have all the data available
                    echo json_encode($newDelivery);
                    exit;
                }
            }
        }
    }

    public function ajaxProcessSendEmail()
    {
        $id_order = (int) Tools::getValue('id_order');
        if (Tools::getIsset('ed_update_mail') && $id_order > 0) {
            $context = Context::getContext();
            $order = new Order((int) $id_order);
            $id_lang = (int) $order->id_lang;
            $context->language = new Language($id_lang);
            $this->module->setBetterLocales($id_lang);
            if (method_exists('Context', 'setInstanceForTesting')) {
                Context::setInstanceForTesting($context);
            }
            if ($this->module->notifyEDUpdate($order)) {
                echo json_encode(['success' => 1]);
            } else {
                echo json_encode(['error' => 1]);
            }
            exit;
        }
    }

    public function ajaxProcessContentReplace()
    {
        $ret = [
            'success' => false,
            'data' => '',
        ];
        if (Tools::getValue('ed_token') == Tools::getAdminToken('AdminEstimatedDelivery')) {
            $this->module->setBoVars();
            $this->module->buildAdditionalDays();
            $ret = [
                'success' => true,
                'return' => $this->module->buildCategoryTree(false, Tools::getValue('id'), Tools::getValue('input_name'), Tools::getValue('selected_cat'), true),
            ];
        }
        echo json_encode($ret);
        exit;
    }

    public function ajaxProcessDismissLocaleCheck()
    {
        echo json_encode(Configuration::updateGlobalValue('ed_dismiss_locale_check', 1));
    }

    /**
     * Process the Estimated Delievey and generate a new date accorging to the date selected in the calendar
     *
     * @param $order
     * @param $force_date
     * @param $from_picking
     *
     * @return mixed
     */
    private function updateDelivery($order, $force_date, $from_picking)
    {
        $params = [
            'cart' => [
                'id_address_delivery' => $order->id_address_delivery,
            ],
            'order' => $order,
        ];

        return $this->module->updateEstimatedDeliveryForOrder($order, $params, false, $force_date, $from_picking, true);
    }

    /** Ajax Process Methods  */
    public function ajaxProcessToggleHook()
    {
        $hookName = Tools::getValue('hookName');
        $isEnabled = Tools::getValue('isEnabled');

        try {
            if ($isEnabled) {
                if ($this->module->registerHook($hookName)) {
                    exit(json_encode(['success' => true, 'message' => $this->module->l('Hook enabled successfully.')]));
                } else {
                    exit(json_encode(['false' => true, 'message' => $this->module->l('Couldn\'t enable hook.')]));
                }
            } else {
                if (Hook::unregisterHook($this->module, $hookName)) {
                    exit(json_encode(['success' => true, 'message' => $this->module->l('Hook disabled successfully.')]));
                } else {
                    exit(json_encode(['false' => true, 'message' => $this->module->l('Couldn\'t disable hook.')]));
                }
            }
        } catch (Exception $e) {
            exit(json_encode(['success' => false, 'message' => $this->module->l('Failed to update hook.')]));
        }
        exit;
    }
}
