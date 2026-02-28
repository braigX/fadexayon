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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceOrder.php';

class AdminGeodisOrdersGridController extends GeodisControllerAdminAbstractMenu
{
    protected $statusList = array();
    protected $carrierList = array();
    protected $orderShipments = array();
    protected $defaultOrderWay = 'DESC';

    public function __construct()
    {
        // Utilisation de GeodisServiceTranslation dans les templates
        GeodisServiceTranslation::registerSmarty();
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->toolbar_btn['print'] = array(
           'href' => $this->context->link->getAdminLink(
               GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint'
           ),
           'desc' =>  GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.link.print'),
           'class' => 'icon-print',
        );
        $this->toolbar_btn['transmit'] = array(
            'href' => $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'ShipmentsGridTransmit'
            ),
           'desc' =>  GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.link.transmit'),
           'class' => 'icon-send',
        );

        $this->bootstrap = true;
        $this->table = 'orders';
        $this->list_no_link = true;
        $this->identifier = 'id_order';
        $this->allow_export = false;
        $this->_orderBy = 'id_order';
        $this->addRowAction('view');
        $this->_select = '
            concat(upper(c.`lastname`),
            " ",
            c.`firstname`) as customer,
            c.`email` as email,
            d.`name` as status,
            e.`name` as carrier_name,
            e.`id_carrier` as idc,
            a.`date_add` as date_add,
            os.color,
            a.`id_order` as `action`';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'order_state_lang` d
                ON (a.`current_state` = d.`id_order_state` and d.`id_lang` = '.(int) $this->context->language->id.')
            LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
            LEFT JOIN `'._DB_PREFIX_.'carrier` e ON (a.`id_carrier` = e.`id_carrier`)
        ';

        $this->_where = 'and a.current_state in ('.$this->getAvailableOrderStatesAsString().')';

        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        $availableOrderStates = GeodisServiceConfiguration::getInstance()->get('available_order_states');
        foreach ($statuses as $status) {
            if (empty($availableOrderStates) || in_array($status['id_order_state'], $availableOrderStates)) {
                $this->statusList[$status['id_order_state']] = $status['name'];
            }
        }

        $carriers = Carrier::getCarriers((int)$this->context->language->id, true);
        foreach ($carriers as $carrier) {
            $this->carrierList[$carrier['id_carrier']] = $carrier['name'];
        }

        $this->fields_list = array(
            'reference' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.reference'),
                'class' => 'fixed-width-xs',
            ),
            'customer' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.customerName'),
                'havingFilter' => true,
            ),
            'email' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.customerEmail'),
                'havingFilter' => true,
            ),
            'shipment_list' => array(
                'float' => true,
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.shipmentList'),
                'havingFilter' => false,
                'filter' => false,
                'search' => false,
            ),
            'status' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.orderStatus'),
                'type' => 'select',
                'color' => 'color',
                'filter_key' => 'current_state',
                'list' => $this->statusList,
                'filter_type' => 'int',
                'order_key' => 'status',
            ),
            'carrier_name' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.carrier'),
                'havingFilter' => true,
                'type' => 'select',
                'filter_key' => 'idc',
                'list' => $this->carrierList,
                'filter_type' => 'int',
                'order_key' => 'carrier_name',
            ),
            'date_add' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.orderDate'),
                'havingFilter' => true,
                'type' => 'datetime',
            ),
            'total_paid' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.orderTotal'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
            ),
            'action' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.actions'),
                'search' => false,
                'orderby' => false,
                'align' => 'text-right',
                'callback' => 'actionButton',
            ),
        );
        $this->base_tpl_view = 'main.tpl';
        // Declaration of bulk actions
        $this->bulk_actions = array(
            'printOrdersLabels' => array('text' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.printLabels'), 'icon' => 'icon-print'),
            'sendOrdersShipments' => array('text' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.sendShipments'), 'icon' => 'icon-send'),
        );
        if (Tools::isSubmit('submitBulkprintOrdersLabels' . $this->table)) {
            $this->processPrintOrdersLabels();
        }
        if (Tools::isSubmit('submitBulksendOrdersShipments' . $this->table)) {
            $this->processSendOrdersShipments();
        }
        if (Tools::getIsset('download_labels_file') && Tools::getIsset('file_name') && Tools::getIsset('file_content')) {
            $this->sendFile(Tools::getValue('file_name'), Tools::getValue('file_content'));
        }
    }

    public static function setOrderCurrency($echo, $tr)
    {
        $order = new Order($tr['id_order']);
        return Tools::displayPrice($echo, (int)$order->id_currency);
    }

    public function access($action = null, $disable = false)
    {
        return true;
    }

    protected function getOrderShipments($idOrder)
    {
        if (!isset($this->orderShipments[$idOrder])) {
            $this->orderShipments[$idOrder] = GeodisServiceOrder::getInstance()->getOrderShipments($idOrder);
        }

        return $this->orderShipments[$idOrder];
    }

    public function actionButton($id, $order)
    {
        $buttons = array();
        if (!GeodisServiceOrder::getInstance()->isOrderShipped($order['id_order'])) {
            $buttons[] = array(
                'label' => 'Admin.OrdersGrid.index.action.createShipment',
                'href' => $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'Shipment',
                    true,
                    array(),
                    array('id_order' => $order['id_order'])
                )
            );
        }

        if (count($this->getOrderShipments($order['id_order'])) == 1) {
            $buttons[] = array(
                'label' => 'Admin.OrdersGrid.index.action.editShipment',
                'href' => $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'Shipment',
                    true,
                    array(),
                    array('id' => $this->getOrderShipments($order['id_order'])->getFirst()->id)
                )
            );
        }

        $multiple = count($buttons);
        $html = '';
        foreach ($buttons as $button) {
            $this->context->smarty->assign(
                array(
                    'module_dir' => __PS_BASE_URI__.'modules/'.GEODIS_MODULE_NAME.'/',
                    'multiple' => $multiple,
                )
            );
            $this->context->smarty->assign($button);

            $html .= $this->context->smarty->fetch(
                _PS_MODULE_DIR_.'geodisofficiel//views/templates/admin/_partial/button_action.tpl'
            );
        }


        return $html;
    }

    public function initToolbar()
    {
        if ($this->display == 'view') {
            $idOrder = Tools::getValue('id_order');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders').'&id_order='.$idOrder.'&vieworder');
        }

        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    protected function getAvailableOrderStatesAsString()
    {
        $values = GeodisServiceConfiguration::getInstance()->get('available_order_states');

        if (empty($values)) {
            $states = OrderState::getOrderStates((int)$this->context->language->id);
            foreach ($states as $state) {
                $values[] = $state['id_order_state'];
            }
        }

        $values = array_map('intval', $values);

        return implode(',', $values);
    }

    /**
     * AdminController::getList() override.
     *
     * @see AdminController::getList()
     */
    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        foreach ($this->_list as &$item) {
            $item['shipment_list'] = $this->getShipmentList($item['action']);
        }
    }

    public function getShipmentList($idOrder)
    {
        $shipments = $this->getOrderShipments($idOrder);

        if (!count($shipments)) {
            return;
        }

        $arrayShipments = array();
        foreach ($shipments as $shipment) {
            $arrayShipments[] = array(
                'id' => $shipment->id,
                'reference' => $shipment->reference,
                'is_complete' => $shipment->is_complete,
                'status_label' => $shipment->status_label,
                'color' => $shipment->incident ? '#8f0621' : '#108510',
            );
        }

        $fields_list = array(
            'reference' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.shipments.reference'),
                'type' => 'text',
            ),
            'is_complete' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.shipments.transmitted'),
                'type' => 'bool',
                'active' => 'status',
            ),
            'status_label' => array(
                'title' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.shipments.status'),
                'type' => 'text',
                'color' => 'color',
                'class' => 'shipment_status',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
//        $helper->table = 'shipment';
        $helper->table = 'shipment_'.$idOrder;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'delete');
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->title = GeodisServiceTranslation::get('Admin.OrdersGrid.index.shipments.title');
        $helper->token = Tools::getAdminTokenLite(GEODIS_ADMIN_PREFIX.'Shipment');
        $helper->currentIndex = $this->removeToken(
            $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'Shipment'
            )
        );

        return $helper->generateList($arrayShipments, $fields_list);
    }

    protected function removeToken($url)
    {
        return preg_replace('/&token=.*$/', '', $url);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel//views/js/admin/orderGrid.js');
        Media::addJsDef(
            array(
                'geodisOrderGridHide' => (string) GeodisServiceTranslation::get(
                    'Admin.OrdersGrid.index.shipments.hide'
                ),
                'geodisOrderGridShow' => (string) GeodisServiceTranslation::get(
                    'Admin.OrdersGrid.index.shipments.show'
                ),
            )
        );
    }
    /**
     * Process print multi orders's labels
     */
    private function processPrintOrdersLabels()
    {
        // Load orders from selected checkbox
        $orders = $this->loadOrdersSelected();
        if (!$orders) {
            return false;
        }

        // Load shipments from orders and extract Recept numbers
        $unacceptedRefOrders = [];
        $acceptedShipments = [];
        $acceptedreceptNumbers = [];
        foreach ($orders as $order) {
            $shipments = GeodisServiceOrder::getInstance()->getOrderShipmentsByCarrierPrestashop($order->id, $order->id_carrier)->getResults();

            if (empty($shipments)) {
                $unacceptedRefOrders[] = $order->reference;
            } else {
                foreach ($shipments as $shipment) {
                    if (!empty($shipment->recept_number)) {
                        $acceptedShipments[] = $shipment;
                        $acceptedreceptNumbers[] = $shipment->recept_number;
                    } else {
                        $unacceptedRefOrders[] = $order->reference;
                    }
                }
            }
        }

        // Generate error
        if (!empty($unacceptedRefOrders)) {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.printLabels.failed') . implode(', ', $unacceptedRefOrders);
            return false;
        }

        // Send labels to print
        $response = [];
        try {
            $response = GeodisServiceWebservice::getInstance()->getPackageLabel($acceptedreceptNumbers);
        } catch (Exception $e) {
            GeodisServiceLog::getInstance()->error($e->getMessage());
        }

        if (!empty($response)) {
            // Update Shipments
            $acceptedShipmentsIds = [];
            foreach ($acceptedShipments as $shipment) {
                $acceptedShipmentsIds[] = $shipment->id;
                $shipment->is_label_printed = true;
                $shipment->save();
            }

            // Form to download labels's file in the success message
            $fileName = 'labels-' . implode('_', $acceptedShipmentsIds) . '.pdf';
            $formDl = '<form action="'.$this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'OrdersGrid').'&download_labels_file'.'" method="POST" id="formDownloadLabelsFile">
                <input type="hidden" name="file_name" id="file_name" value="'.$fileName.'">
                <input type="hidden" name="file_content" id="file_content" value="'.urlencode($response).'">
                '.GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.printLabels.form.label').'
                <input class="btn btn-default" type="submit" value="'.GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.printLabels.form.download.btn.label').'">
            </form>';

            $this->confirmations[] = $formDl;
        } else {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.error.no.print.ws.answer');
            return false;
        }

        return true;
    }

    private function sendFile($fileName, $fileContent)
    {
        if (!empty($fileName) && !empty($fileContent)) {
            $pdf = urldecode($fileContent);
            $pdf = Tools::substr($pdf, 0, strpos($pdf, '%%EOF') + 5);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . Tools::strlen($pdf, "ASCII"));
            die($pdf);
        } else {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.downloadFile.error.data.empty');
        }
    }

    private function processSendOrdersShipments()
    {
        // Load orders from selected checkbox
        $orders = $this->loadOrdersSelected();
        if (!$orders) {
            return false;
        }

        // Load shipments from orders
        $unacceptedRefOrders = [];
        $acceptedShipments = [];
        $acceptedreceptNumbers = [];
        foreach ($orders as $order) {
            $shipments = GeodisServiceOrder::getInstance()->getOrderShipmentsByCarrierPrestashop($order->id, $order->id_carrier)->getResults();

            if (empty($shipments)) {
                $unacceptedRefOrders[] = $order->reference;
            } else {
                // Shipments must be printed and not sent
                foreach ($shipments as $shipment) {
                    // If not printed or even sent refused Shipment (Add once in refused orders)
                    if (($shipment->is_label_printed <= 0) || ($shipment->is_complete >= 1)) {
                        if (!in_array($order->reference, $unacceptedRefOrders)) {
                            $unacceptedRefOrders[] = $order->reference;
                        }
                    } else {
                        $acceptedShipments[] = $shipment;
                        $acceptedreceptNumbers[] = $shipment->recept_number;
                    }
                }
            }
        }

        // Generate error
        if (!empty($unacceptedRefOrders)) {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.sendShipments.failed') . implode(', ', $unacceptedRefOrders);
            return false;
        }

        // Send shipments
        $response = [];
        try {
            $response = GeodisServiceWebservice::getInstance()->sendShipment($acceptedreceptNumbers);
        } catch (Exception $e) {
            GeodisServiceLog::getInstance()->error($e->getMessage());
        }

        if (!empty($response)) {
            // If answer in error
            if ($response['ok'] != true) {
                $message = $response['codeErreur'] . ' : ' . $response['texteErreur'];
                GeodisServiceLog::getInstance()->error($message);
                $this->errors[] = $message;
                return false;
            }

            // Check shimpents managed
            $managedReceptNum = array();
            foreach ($response['contenu'] as $record) {
                // If recepisse has error log it in warning
                if ($record['erreur'] == true) {
                    $message = $record['noRecepisse'] . ' : ' . $record['messageErreur'];
                    GeodisServiceLog::getInstance()->log($message);
                    $this->warnings[] = $message;
                }

                // Recepisse is complete even if has error
                $managedReceptNum[] = $record['noRecepisse'];
            }

            // Update managed shipments
            foreach ($acceptedShipments as $shipment) {
                if (in_array($shipment->recept_number, $managedReceptNum)) {
                    $shipment->is_complete = true;
                    $shipment->save();
                    GeodisServiceOrder::getInstance()->updateOrderState($shipment->id_order);
                }
            }

            // If errors in shipments return errors messages else confirm
            if (empty($this->errors)) {
                $this->confirmations[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.success.shipments') . implode(', ', $managedReceptNum);
            } else {
                return false;
            }
        } else {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.error.no.send.shipments.ws.answer');
            return false;
        }

        return true;
    }

    /**
     * Load orders from selected checkbox
     */
    private function loadOrdersSelected()
    {
        // At least one order must be checked
        if (!$this->hasOrdersChecked()) {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.error.no.order.checked');
            return false;
        }

        // Get orders from ids
        $idOrderList = Tools::getValue('ordersBox');
        $orders = GeodisServiceOrder::getInstance()->getOrdersByIds($idOrderList)->getResults();

        // Must have the same number of order as ids
        if (count($orders) != count($idOrderList)) {
            $this->errors[] = GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.error.failed.to.find.all.orders');
            return false;
        }

        return $orders;
    }

    /**
     * Check if at least one order checkbox is checked
     */
    private function hasOrdersChecked()
    {
        if (Tools::getIsset('ordersBox') && !empty(Tools::getValue('ordersBox'))) {
            return true;
        }

        return false;
    }
}
