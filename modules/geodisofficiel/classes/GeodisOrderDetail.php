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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisProductWineLiquor.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbSchema.php';

class GeodisOrderDetail
{
    protected $order;
    protected $orderDetail;
    protected $product;
    protected $combination;
    protected $packageOrderDetailCollection;
    protected $quantityOrdered;
    protected $quantityReserved;
    protected $quantityShipped;
    protected $wineLiquor;

    /**
     * Set OrderDetail ID
     *
     * @param  OrderDetail $orderDetail
     * @return GeodisOrderDetail
     */
    public function setOrderDetail($orderDetail)
    {
        $this->orderDetail = $orderDetail;

        return $this;
    }

    /**
     * Get OrderDetail
     *
     * @return OrderDetail
     */
    public function getOrderDetail()
    {
        if (!$this->orderDetail) {
            $this->orderDetail = new OrderDetail($this->idOrderDetail);
        }
        return $this->orderDetail;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = new Product($this->orderDetail->product_id);
        }
        return $this->product;
    }

    /**
     * Get combination
     *
     * @return Combination
     */
    public function getCombination()
    {
        if (is_null($this->combination)) {
            if (!$this->orderDetail->product_attribute_id) {
                $this->combination = false;
            } else {
                $this->combination = new Combination($this->orderDetail->product_attribute_id);
            }
        }
        return $this->combination;
    }

    /**
     * Set PackageOrderDetail Collection
     *
     * @param  $packageOrderDetailCollection
     * @return GeodisOrderDetail
     */
    public function setPackageOrderDetailCollection($packageOrderDetailCollection)
    {
        $this->packageOrderDetailCollection = $packageOrderDetailCollection;

        return $this;
    }

    /**
     * Get packageOrderDetailCollection
     *
     * @return Collection of GeodisPackageOrderDetail
     */
    public function getPackageOrderDetailCollection()
    {
        return $this->packageOrderDetailCollection;
    }

    /**
     * Set quantity ordered
     *
     * @param  $quantityOrdered
     * @return GeodisOrderDetail
     */
    public function setQuantityOrdered($quantityOrdered)
    {
        $this->quantityOrdered = $quantityOrdered;

        return $this;
    }

    /**
     * Get quantityOrdered
     *
     * @return float
     */
    public function getQuantityOrdered()
    {
        return $this->quantityOrdered;
    }

    /**
     * Set quantityReserved
     *
     * @param  $quantityReserved
     * @return GeodisOrderDetail
     */
    public function setQuantityReserved($quantityReserved)
    {
        $this->quantityReserved = $quantityReserved;

        return $this;
    }

    /**
     * Get quantityReserved
     *
     * @return float
     */
    public function getQuantityReserved()
    {
        return $this->quantityReserved;
    }

    /**
     * Set quantityShipped
     *
     * @param  $quantityShipped
     * @return GeodisOrderDetail
     */
    public function setQuantityShipped($quantityShipped)
    {
        $this->quantityShipped = $quantityShipped;

        return $this;
    }

    /**
     * Get quantityShipped
     *
     * @return float
     */
    public function getQuantityShipped()
    {
        return $this->quantityShipped;
    }

    /**
     * Get quantityAvailable
     *
     * @return float
     */
    public function getQuantityAvailable($shipment = null)
    {
        if (is_null($shipment) || !$shipment->id) {
            return $this->quantityOrdered - $this->quantityReserved;
        }

        $quantityAvailable = $this->quantityOrdered - $this->quantityReserved;
        foreach ($this->packageOrderDetailCollection as $packageOrderDetail) {
            $package = $packageOrderDetail->getPackage();
            if ($shipment->id == $package->id_shipment) {
                $quantityAvailable += $packageOrderDetail->quantity;
            }
        }

        return $quantityAvailable;
    }

    public function debug()
    {
        return array(
            'id_order_detail' => $this->getOrderDetail()->id,
            'id_combination' => $this->getCombination()->id,
            'id_product' => $this->getProduct()->id,
            'quantityOrdered' => $this->quantityOrdered,
            'quantityReserved' => $this->quantityReserved,
            'quantityShipped' => $this->quantityShipped,
            'quantityAvailable' => $this->getQuantityAvailable(),
        );
    }

    public function getOrder()
    {
        if (!$this->order) {
            $this->order = new Order($this->getOrderDetail()->id_order);
        }

        return $this->order;
    }

    public function getPackageQuantity($package)
    {
        if (!$package->id) {
            return $this->getQuantityAvailable();
        }

        foreach ($this->packageOrderDetailCollection as $package) {
            return (float) $this->getPackageOrderDetail($package)->quantity;
        }

        return 0;
    }

    public function getPackageOrderDetail($package)
    {
        foreach ($this->packageOrderDetailCollection as $packageOrderDetail) {
            if ($packageOrderDetail->id_package == $package->id) {
                return $packageOrderDetail;
            }
        }

        return new GeodisPackageOrderDetail();
    }

    public function getWineLiquor()
    {
        if (!$this->wineLiquor) {
            $this->wineLiquor = GeodisProductWineLiquor::getFromProduct($this->getProduct());
        }

        return $this->wineLiquor;
    }
}
