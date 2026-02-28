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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbSchema.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrierOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';

class GeodisCarrier extends ObjectModel
{
    public $id_group_carrier;
    public $id_prestation;
    public $id_account;
    public $active;
    public $name;
    public $description;
    public $price;
    public $free_shipping_from;
    public $additional_shipping_cost;
    public $deleted = false;
    public $date_add;
    public $date_upd;
    public $enable_price_fixed;
    public $enable_price_according;
    public $enable_free_shipping;
    protected $groupCarrier;
    protected $prestation;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_carrier',
        'primary' => 'id_carrier',
        'fields' => array(
            'id_group_carrier' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_prestation' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_account' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'name' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'description' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'free_shipping_from' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => false),
            'additional_shipping_cost' =>  array('type' => self::TYPE_BOOL, 'required' => false),
            'deleted' =>  array('type' => self::TYPE_BOOL, 'required' => false),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'enable_price_fixed' => array('type' => self::TYPE_BOOL, 'enable_price_fixed' => 'isBool'),
            'enable_price_according' => array('type' => self::TYPE_BOOL, 'enable_price_according' => 'isBool'),
            'enable_free_shipping' => array('type' => self::TYPE_BOOL, 'enable_free_shipping' => 'isBool'),
        ),
    );

    public static function getCollection()
    {
        $collection = new PrestaShopCollection(self::class);
        $collection->where('deleted', '=', 0);

        return $collection;
    }

    public function getGroupCarrier()
    {
        if (!$this->id_group_carrier) {
            return false;
        }

        if (!$this->groupCarrier) {
            $this->groupCarrier = new GeodisGroupCarrier($this->id_group_carrier);
        }

        return $this->groupCarrier;
    }

    public function getPrestation()
    {
        if (!$this->prestation) {
            $this->prestation = new GeodisPrestation($this->id_prestation);
        }

        return $this->prestation;
    }

    public function getOptionCollection($filterActive = false)
    {
        $collection = GeodisOption::getCollection();

        $subQuery = new DbQuery();
        $subQuery->select('id_option');
        $subQuery->from(GEODIS_NAME_SQL.'_carrier_option');
        $subQuery->where('id_carrier = '.(int)$this->id);

        if ($filterActive) {
            $subQuery->where('active = 1');
        }

        $collection->sqlWhere('id_option IN ('.$subQuery.')');
        $collection->orderBy('position', 'ASC');

        return $collection;
    }

    public function getCarrierOptionCollection($filterActive = false)
    {
        $collection = array();

        foreach ($this->getOptionCollection($filterActive) as $option) {
            $tempCollection = GeodisCarrierOption::getCollection();
            $tempCollection->where('id_carrier', '=', (int)$this->id);
            $tempCollection->where('id_option', '=', (int)$option->id);

            $collection[] = $tempCollection->getFirst();
        }

        return $collection;
    }

    /**
     * Retrieve carrier option from option id
     */
    public function getCarrierOption($idOption)
    {
        $collection = GeodisCarrierOption::getCollection();
        $collection->where('id_carrier', '=', (int)$this->id);
        $collection->where('id_option', '=', (int)$idOption);

        return $collection->getFirst();
    }

    public static function getCollectionFilterByPrestation($prestation)
    {
        $collection = self::getCollection();
        $collection->where('id_prestation', '=', $prestation->id);

        return $collection;
    }

    public static function getFirstFromPrestationId($idPrestation)
    {
        $collection = self::getCollection();
        $collection->where('id_prestation', '=', $idPrestation);

        return $collection->getFirst();
    }

    public function getInitialPriceFromCart($cart, $idPCarrier)
    {
        $products = $cart->getProducts(false, false, null, false);

        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ));

        // Get shipping price
        $price = $cart->getPackageShippingCost($idPCarrier);

        $taxesRate = 0.0;
        if (!empty($cart->id_address_delivery)) {
            $pCarrier = new Carrier($idPCarrier);
            $addressDelivery = new Address($cart->id_address_delivery);
            $taxesRate = $pCarrier->getTaxesRate($addressDelivery);
        }

        // Remove selected carrier addiotionnal options from cart if not free shipping
        if (!(($this->enable_free_shipping == 1) && ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $this->free_shipping_from))) {
            $cartCarrier = GeodisCartCarrier::loadFromIdCart($cart->id);
            if ($cartCarrier != false) {
                foreach ($cartCarrier->getCarrierOptionCollection() as $carrierOption) {
                    $optionPriceImpact = $carrierOption->price_impact;
                    if (($optionPriceImpact > 0) && ($taxesRate > 0)) {
                        $optionPriceImpact = $optionPriceImpact + ($optionPriceImpact * ($taxesRate / 100));
                    }
                    if ($price > 0) {
                        $price -= $optionPriceImpact;
                    }
                }
            }
        }

        // Adding handling charges
        if (isset($configuration['PS_SHIPPING_HANDLING']) &&
            $this->getGroupCarrier()->getCarrier()->shipping_handling) {
            $price += (float)$configuration['PS_SHIPPING_HANDLING'];
        }

        $shippingCost = 0;
        // Additional Shipping Cost per product
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shippingCost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }

        if ($this->free_shipping_from > 0) {
            if ($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) >= $this->free_shipping_from) {
                if (!$this->additional_shipping_cost) {
                    $shippingCost = 0;
                }
            }
        }

        $price += $shippingCost;

        $price = Tools::convertPrice($price, Currency::getCurrencyInstance((int)$cart->id_currency));

        return $price;
    }

    public function getCarriersByGroupCarrier($idGroupCarrier = null)
    {
        $collection = GeodisCarrier::getCollection();
        $collection->where('id_group_carrier', '=', (int)$idGroupCarrier);

        return $collection;
    }
}
