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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackageOrderDetail.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOrderDetail.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';

class GeodisServiceOrder
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new GeodisServiceOrder();
        }

        return self::$instance;
    }

    /**
     * @param int $idOrder
     *
     * @return GeodisOrderDetail[]
     *
     * ]
     */
    public function getOrderItems($idOrder)
    {
        $result = array();
        $orderDetailCollection = new PrestaShopCollection('OrderDetail');
        $orderDetailCollection->where('id_order', '=', $idOrder);

        foreach ($orderDetailCollection as $orderDetail) {
            $item = new GeodisOrderDetail();
            $item->setOrderDetail($orderDetail);

            $quantityOrdered = (float) $orderDetail->product_quantity;
            $item->setQuantityOrdered($quantityOrdered);

            $geodisOrderDetailCollection = new PrestaShopCollection('GeodisPackageOrderDetail');
            $geodisOrderDetailCollection->where('id_order_detail', '=', $orderDetail->id);

            $item->setPackageOrderDetailCollection($geodisOrderDetailCollection);
            $quantityReserved = 0;
            $quantityShipped = 0;
            foreach ($geodisOrderDetailCollection as $geodisOrderDetail) {
                $quantityReserved += (float) $geodisOrderDetail->quantity;
                $quantityShipped += (float) $geodisOrderDetail->quantity;
            }
            $item->setQuantityReserved($quantityReserved);
            $item->setQuantityShipped($quantityShipped);


            $result[] = $item;
        }

        return $result;
    }

    /**
     * Check if some or all products are sent
     * and change the order states if needed
     */
    public function updateOrderState($idOrder)
    {
        $itemsShipped = false; // becomes true if an item is shipped
        $itemsNotShipped = false; // becomes true if an item is not shipped
        $order = new Order($idOrder);

        // Check order is not in an ignored state
        if (in_array($order->current_state, GeodisServiceConfiguration::getInstance()->get('ignore_order_states'))) {
            return;
        }

        // Loop all order items and check if some or all items are shipped
        foreach ($this->getOrderItems($idOrder) as $orderItem) {
            if ($orderItem->getQuantityShipped() > 0) {
                $itemsShipped = true;
            }
            if ($orderItem->getQuantityShipped() < $orderItem->getQuantityOrdered()) {
                $itemsNotShipped = true;
            }
        }

        // No items shipped
        if (!$itemsShipped) {
            return $this;
        }

        $idEmployee = 0;

        if (Context::getContext()->employee) {
            $idEmployee = Context::getContext()->employee->id;
        }

        // Some items are shipped
        if ($itemsNotShipped && GeodisServiceConfiguration::getInstance()->get('partial_shipping_state')) {
            $order->setCurrentState(
                GeodisServiceConfiguration::getInstance()->get('partial_shipping_state'),
                $idEmployee
            );
            return $this;
        }

        // All items are shipped
        if (GeodisServiceConfiguration::getInstance()->get('complete_shipping_state')) {
            $order->setCurrentState(
                GeodisServiceConfiguration::getInstance()->get('complete_shipping_state'),
                $idEmployee
            );
            return $this;
        }

        return $this;
    }

    /**
     * @return true if all items of the order are shipped
     */
    public function isOrderShipped($idOrder)
    {
        // Loop all order items and check if some or all items are shipped
        foreach ($this->getOrderItems($idOrder) as $orderItem) {
            if ($orderItem->getQuantityShipped() < $orderItem->getQuantityOrdered()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return GeodisShipment[]
     */
    public function getOrderShipments($idOrder)
    {
        $collection = new PrestaShopCollection('GeodisShipment');
        $collection->where('id_order', '=', $idOrder);

        return $collection;
    }

    /**
     * @return GeodisShipment[]
     */
    public function getOrderShipmentsByCarrierPrestashop($idOrder, $idCarrierPrestashop)
    {
        $collection = new PrestaShopCollection('GeodisShipment');
        $collection->where('id_order', '=', $idOrder);
        $collection->where('id_reference_carrier', '=', $idCarrierPrestashop);

        return $collection;
    }

    /**
     * Extract orders by list ids
     *
     * @return Order[]
     */
    public function getOrdersByIds($idOrderList)
    {
        $collection = new PrestaShopCollection('Order');
        $collection->where('id_order', 'in', $idOrderList);
        return $collection;
    }
}
