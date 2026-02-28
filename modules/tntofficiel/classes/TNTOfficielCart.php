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

/**
 * Class TNTOfficielCart
 */
class TNTOfficielCart extends ObjectModel
{
    // id_tntofficiel_cart
    public $id;

    public $id_cart;
    public $id_address;
    public $id_carrier;
    public $delivery_point;

    public static $definition = array(
        'table' => 'tntofficiel_cart',
        'primary' => 'id_tntofficiel_cart',
        'fields' => array(
            'id_cart' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_address' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
            ),
            'id_carrier' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
            ),
            'delivery_point' => array(
                'type' => ObjectModel::TYPE_STRING,
                /*'validate' => 'isSerializedArray', 'size' => 65000,*/
            ),
        ),
    );

    // cache and prevent race condition.
    private static $arrLoadedEntities = array();

    /**
     * Creates the tables needed by the model.
     *
     * @return bool
     */
    public static function createTables()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $strTablePrefix = _DB_PREFIX_;
        $strTableEngine = _MYSQL_ENGINE_;

        $strTableName = $strTablePrefix . TNTOfficielCart::$definition['table'];

        // Create table.
        $strSQLCreateCart = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_cart`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart`                       INT(10) UNSIGNED NOT NULL,
    `id_address`                    INT(10) UNSIGNED NOT NULL,
    `id_carrier`                    INT(10) UNSIGNED NOT NULL,
    `delivery_point`                TEXT NULL,
-- Key.
    PRIMARY KEY (`id_tntofficiel_cart`),
    UNIQUE INDEX `id_cart` (`id_cart`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

        $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreateCart);
        if (is_string($boolDBResult)) {
            TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

            return false;
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return TNTOfficielCart::checkTables();
    }

    /**
     * Check if table and columns exist.
     *
     * @return bool
     */
    public static function checkTables()
    {
        TNTOfficiel_Logstack::log();

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielCart::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielCart::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID);
    }

    /**
     * Load existing object model or optionally create a new one for its ID.
     *
     * @param      $intArgCartID
     * @param bool $boolArgCreate
     *
     * @return TNTOfficielCart|null
     */
    public static function loadCartID($intArgCartID, $boolArgCreate = true)
    {
        TNTOfficiel_Logstack::log();

        $intCartID = (int)$intArgCartID;

        // No new cart ID.
        if (!($intCartID > 0)) {
            return null;
        }

        $strEntityID = '_' . $intCartID . '-' . (int)null . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielCart::$arrLoadedEntities)) {
            $objTNTCartModel = TNTOfficielCart::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTCartModel)
                && (int)$objTNTCartModel->id_cart === $intCartID
            ) {
                return $objTNTCartModel;
            }
        }

        // Search row for cart ID.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielCart::$definition['table']);
        $objDbQuery->where('id_cart = ' . $intCartID);

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found and match cart ID.
        if (is_array($arrDBResult) && count($arrDBResult) === 1 && $intCartID === (int)$arrDBResult[0]['id_cart']) {
            // Load existing TNT cart entry.
            $objTNTCartModel = new TNTOfficielCart((int)$arrDBResult[0]['id_tntofficiel_cart']);
        } elseif ($boolArgCreate === true) {
            // Create a new TNT cart entry.
            $objTNTCartModelCreate = new TNTOfficielCart(null);
            $objTNTCartModelCreate->id_cart = $intCartID;
            $objTNTCartModelCreate->save();
            // Reload to get default DB values after creation.
            $objTNTCartModel = TNTOfficielCart::loadCartID($intCartID, false);
        } else {
            // Log.
            $objException = new Exception(sprintf(
                TNTOfficiel::getCodeTranslate('errorUnableLoadNotFoundStr'),
                'TNTOfficielCart',
                'Cart',
                $intCartID
            ));
            TNTOfficiel_Logger::logException($objException);

            return null;
        }

        // Check.
        if (!Validate::isLoadedObject($objTNTCartModel)
            || (int)$objTNTCartModel->id_cart !== $intCartID
        ) {
            return null;
        }

        $objTNTCartModel->id = (int)$objTNTCartModel->id;
        $objTNTCartModel->id_cart = (int)$objTNTCartModel->id_cart;
        $objTNTCartModel->id_address = (int)$objTNTCartModel->id_address;
        $objTNTCartModel->id_carrier = (int)$objTNTCartModel->id_carrier;

        TNTOfficielCart::$arrLoadedEntities[$strEntityID] = $objTNTCartModel;

        return $objTNTCartModel;
    }

    /**
     * Load an existing Prestashop Cart object from ID.
     *
     * @return Cart|null
     */
    public static function getPSCartByID($intArgCartID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSCart = array();

        // Cart ID must be an integer greater than 0.
        if (empty($intArgCartID) || $intArgCartID != (int)$intArgCartID || !((int)$intArgCartID > 0)) {
            return null;
        }

        $intCartID = (int)$intArgCartID;

        // If already loaded.
        if (array_key_exists($intCartID, $arrStaticPSCart)) {
            $objPSCartMem = $arrStaticPSCart[$intCartID];
            // Check.
            if (Validate::isLoadedObject($objPSCartMem)
                && (int)$objPSCartMem->id === $intCartID
            ) {
                return $objPSCartMem;
            }
        }

        // Load Cart.
        $objPSCart = new Cart($intCartID);

        // If Cart object not available.
        if (!Validate::isLoadedObject($objPSCart)
            || (int)$objPSCart->id !== $intCartID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSCart[$intCartID] = $objPSCart;

        return $objPSCart;
    }

    /**
     * Load the Prestashop Cart object associated with this order.
     *
     * @return Cart|null
     */
    public function getPSCart()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficielCart::getPSCartByID($this->id_cart);
    }

    /**
     * Load the Prestashop Address object used for delivery from Cart.
     *
     * @return Address|null
     */
    public function getPSAddressDelivery()
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return null;
        }

        $intAddressID = (int)$objPSCart->id_address_delivery;

        return TNTOfficielReceiver::getPSAddressByID($intAddressID);
    }

    /**
     * Get the selected TNT Carrier object from Cart.
     *
     * @return TNTOfficielCarrier|null
     */
    public function getTNTCarrierModel()
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return null;
        }

        $intCarrierID = (int)$objPSCart->id_carrier;

        // Load an existing TNT carrier.
        return TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
    }

    /**
     * Get the carrier Account model from cart.
     *
     * @return TNTOfficielAccount|null
     */
    public function getTNTAccountModel()
    {
        TNTOfficiel_Logstack::log();

        // Get the selected TNT Carrier object from Cart.
        $objTNTCarrierModel = $this->getTNTCarrierModel();
        // If not TNT or load fail.
        if ($objTNTCarrierModel === null) {
            return null;
        }

        return $objTNTCarrierModel->getTNTAccountModel();
    }

    /**
     * Get carrier selected delivery point.
     *
     * @param int|null $intArgCarrierID
     *
     * @return array
     */
    public function getDeliveryPoint($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return array();
        }

        $intCarrierID = (int)$intArgCarrierID;
        if ($intArgCarrierID === null) {
            $intCarrierID = (int)$objPSCart->id_carrier;
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        // If not TNT or load fail.
        if ($objTNTCarrierModel === null) {
            return array();
        }

        $arrDeliveryPoint = TNTOfficiel_Tools::unserialize($this->delivery_point);
        if (!is_array($arrDeliveryPoint)) {
            $arrDeliveryPoint = array();
        }

        // DEPOT have an PEX code.
        // DROPOFFPOINT have an XETT code.
        if ($objTNTCarrierModel->carrier_type === 'DEPOT'
            && isset($arrDeliveryPoint['pex'])
        ) {
            unset($arrDeliveryPoint['xett']);
        } elseif ($objTNTCarrierModel->carrier_type === 'DROPOFFPOINT'
            && isset($arrDeliveryPoint['xett'])
        ) {
            unset($arrDeliveryPoint['pex']);
        } else {
            $arrDeliveryPoint = array();
        }

        return $arrDeliveryPoint;
    }

    /**
     * Carrier has a corresponding selected delivery point ?
     *
     * @param int|null $intArgCarrierID
     *
     * @return bool
     */
    public function hasDeliveryPoint($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $arrDeliveryPoint = $this->getDeliveryPoint($intArgCarrierID);
        $strDeliveryPointType = $this->getDeliveryPointType($intArgCarrierID);
        if (is_string($strDeliveryPointType)) {
            return isset($arrDeliveryPoint[$strDeliveryPointType]);
        }

        return false;
    }

    /**
     * Is delivery point required for a selected carrier in cart.
     *
     * @param int|null $intArgCarrierID
     *
     * @return null|bool
     */
    public function isDeliveryPoint($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return null;
        }

        $intCarrierID = (int)$intArgCarrierID;
        if ($intArgCarrierID === null) {
            $intCarrierID = (int)$objPSCart->id_carrier;
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        // If not TNT or load fail.
        if ($objTNTCarrierModel === null) {
            return null;
        }

        return $objTNTCarrierModel->isDeliveryPoint();
    }

    /**
     * Get the delivery point type (xett or pex) for a selected carrier in cart.
     *
     * @return null|string
     */
    public function getDeliveryPointType($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return null;
        }

        $intCarrierID = (int)$intArgCarrierID;
        if ($intArgCarrierID === null) {
            $intCarrierID = (int)$objPSCart->id_carrier;
        }

        // Load an existing TNT carrier.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        // If not TNT or load fail.
        if ($objTNTCarrierModel === null) {
            return null;
        }

        return $objTNTCarrierModel->getDeliveryPointType();
    }

    /**
     * Save a delivery point based on the current carrier type.
     *
     * @param array $arrArgDeliveryPoint
     *
     * @return bool false on failure. true on success.
     */
    public function setDeliveryPoint($arrArgDeliveryPoint)
    {
        TNTOfficiel_Logstack::log();

        if (!is_array($arrArgDeliveryPoint)) {
            return false;
        }

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return false;
        }

        $objTNTCarrierModel = $this->getTNTCarrierModel();
        if ($objTNTCarrierModel === null) {
            return false;
        }

        // TODO : id_address + id_carrier for multiple package.
        //$arrDeliveryOption = $this->getDeliveryOption();
        $this->id_address = (int)$objPSCart->id_address_delivery;
        $this->id_carrier = (int)$objTNTCarrierModel->id_carrier;

        // DEPOT have an PEX code.
        // DROPOFFPOINT have an XETT code.
        if ($objTNTCarrierModel->carrier_type === 'DEPOT'
            && isset($arrArgDeliveryPoint['pex'])
        ) {
            unset($arrArgDeliveryPoint['xett']);
        } elseif ($objTNTCarrierModel->carrier_type === 'DROPOFFPOINT'
            && isset($arrArgDeliveryPoint['xett'])
        ) {
            unset($arrArgDeliveryPoint['pex']);
        } else {
            $arrArgDeliveryPoint = array();
        }

        $this->delivery_point = TNTOfficiel_Tools::serialize($arrArgDeliveryPoint);

        return $this->save();
    }

    /**
     * List of product in cart, unit by unit, sorted by weight (Kg), from the heaviest to the lightest.
     *
     * @return array
     */
    public function getCartProductUnitList()
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return array();
        }

        $arrProductList = $objPSCart->getProducts();

        // Sorting the list of products by weight, from the heaviest to the lightest.
        usort($arrProductList, array(__CLASS__, 'compareProductByWeight'));

        // Set all product in an array of products
        $arrProductUnitList = array();
        foreach ($arrProductList as $arrProduct) {
            // Convert the product weight in Kg.
            $arrProduct['weight'] = TNTOfficielCarrier::getShopWeightKg((float)$arrProduct['weight']);
            // Bool.
            $arrProduct['is_virtual'] = !!$arrProduct['is_virtual'];
            // Float.
            $arrProduct['additional_shipping_cost'] = (float)$arrProduct['additional_shipping_cost'];

            $intProductQuantity = (int)$arrProduct['cart_quantity']; // $arrProduct['quantity'];
            for ($intProductCount = 0; $intProductCount < $intProductQuantity; ++$intProductCount) {
                $arrProductUnitList[] = $arrProduct;
            }
        }

        return $arrProductUnitList;
    }

    /**
     * Compare two products by their weight for sorting from the heaviest to the lightest.
     *
     * @param $productA
     * @param $productB
     *
     * @return int
     */
    public static function compareProductByWeight($productA, $productB)
    {
        TNTOfficiel_Logstack::log();

        if ((float)$productA['weight'] === (float)$productB['weight']) {
            return 0;
        }

        return ((float)$productA['weight'] > (float)$productB['weight']) ? -1 : 1;
    }

    /**
     * Get the heaviest product weight from cart.
     *
     * @return float
     */
    public function getCartHeaviestProduct()
    {
        TNTOfficiel_Logstack::log();

        $fltHeaviestProduct = 0.0;

        $arrProductUnitList = $this->getCartProductUnitList();

        foreach ($arrProductUnitList as $arrProductUnit) {
            // If a product weight is greater than the current max.
            if ($arrProductUnit['weight'] > $fltHeaviestProduct) {
                // Set the new max.
                $fltHeaviestProduct = $arrProductUnit['weight'];
            }
        }

        return $fltHeaviestProduct;
    }

    /**
     * Get the total weight of products from cart (Kg).
     *
     * @return float
     */
    public function getCartTotalWeight()
    {
        TNTOfficiel_Logstack::log();

        $fltCartWeight = 0.0;

        $arrProductUnitList = $this->getCartProductUnitList();

        foreach ($arrProductUnitList as $arrProductUnit) {
            // Adding weight.
            $fltCartWeight += $arrProductUnit['weight'];
        }

        return $fltCartWeight;
    }

    /**
     * Get the total price of products from cart.
     *
     * @return float
     */
    public function getCartTotalPrice()
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return 0.0;
        }

        // SubTotal without tax (Cart Items Price)
        $fltSubtotalNoTax = $objPSCart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        // Discount without tax.
        $fltDiscountNoTax = -($objPSCart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING)
            - $objPSCart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
        // Tax part for SubTotal and Discount.
        $fltTaxTotal = $objPSCart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING)
            - $objPSCart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING);

        return $fltSubtotalNoTax - $fltDiscountNoTax + $fltTaxTotal;
    }

    /**
     * Is shipping free for cart, through configuration.
     *
     * @param Cart     $objArgCart
     * @param int|null $intArgCarrierID the current carrier for which price is to determine.
     *
     * @return bool
     */
    public function isCartShippingFree($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return false;
        }

        $intCarrierID = (int)$intArgCarrierID;
        if ($intArgCarrierID === null) {
            $intCarrierID = (int)$objPSCart->id_carrier;
        }

        $arrConfigShipping = Configuration::getMultiple(
            array(
                'PS_SHIPPING_FREE_PRICE',
                'PS_SHIPPING_FREE_WEIGHT',
            )
        );

        // Load carrier object.
        $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($intCarrierID);
        // If carrier object not available.
        if ($objPSCarrier === null) {
            return true;
        }

        // If carrier is inactive or free.
        if (!$objPSCarrier->active || $objPSCarrier->getShippingMethod() == Carrier::SHIPPING_METHOD_FREE) {
            return true;
        }

        // Get cart amount to reach for free shipping.
        $fltFreeFeesPrice = 0;
        if (isset($arrConfigShipping['PS_SHIPPING_FREE_PRICE'])) {
            // Not deprecated since 1.7.4. Comment error corrected in PS 2cfd75cbd5f6 2021-10-01.
            $fltFreeFeesPrice = (float)Tools::convertPrice(
                (float)$arrConfigShipping['PS_SHIPPING_FREE_PRICE'],
                Currency::getCurrencyInstance((int)$objPSCart->id_currency)
            );
        }
        // Free shipping if cart amount, inc. taxes, inc. product & discount, exc. shipping > PS_SHIPPING_FREE_PRICE
        if ($fltFreeFeesPrice > 0
            && $objPSCart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false) >= $fltFreeFeesPrice
        ) {
            return true;
        }

        // Free shipping if cart weight > PS_SHIPPING_FREE_WEIGHT
        if (isset($arrConfigShipping['PS_SHIPPING_FREE_WEIGHT'])
            && $objPSCart->getTotalWeight() >= (float)$arrConfigShipping['PS_SHIPPING_FREE_WEIGHT']
            && (float)$arrConfigShipping['PS_SHIPPING_FREE_WEIGHT'] > 0.0
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get additional shipping cost for cart (exc. taxes).
     *
     * @param int|null $intArgCarrierID the current carrier for which price is to determine.
     *
     * @return float
     */
    public function getCartExtraShippingCost($intArgCarrierID = null)
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return 0;
        }

        $intCarrierID = (int)$intArgCarrierID;
        if ($intArgCarrierID === null) {
            $intCarrierID = (int)$objPSCart->id_carrier;
        }

        $fltShippingCost = 0;

        $arrProductUnitList = $this->getCartProductUnitList();

        // If no product, no shipping extra cost.
        if (!count($arrProductUnitList)) {
            return 0;
        }

        // If only virtual products in cart, no extra shipping cost.
        if ($objPSCart->isVirtualCart()) {
            return 0;
        }

        // If TNT carrier is free.
        $boolIsCartShippingFree = $this->isCartShippingFree($intCarrierID);
        if ($boolIsCartShippingFree) {
            return 0;
        }

        // Load carrier object.
        $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($intCarrierID);
        // If carrier object not available, no extra shipping cost.
        if ($objPSCarrier === null) {
            return 0;
        }

        // Adding handling charges.
        $shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
        if (isset($shipping_handling) && $objPSCarrier->shipping_handling) {
            $fltShippingCost += (float)$shipping_handling;
        }

        // Adding additional shipping cost per product.
        foreach ($arrProductUnitList as $arrProductUnit) {
            if (!$arrProductUnit['is_virtual']) {
                $fltShippingCost += $arrProductUnit['additional_shipping_cost'];
            }
        }

        // Not deprecated since 1.7.4. Comment error corrected in PS 2cfd75cbd5f6 2021-10-01.
        return (float)Tools::convertPrice(
            $fltShippingCost,
            Currency::getCurrencyInstance((int)$objPSCart->id_currency)
        );
    }

    /**
     * Get delivery option preventing recursions.
     *
     * @return array
     */
    public function getDeliveryOption()
    {
        TNTOfficiel_Logstack::log();

        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return array();
        }

        static $arrStaticDeliveryOption = array();

        $strMemKey = $objPSCart->delivery_option;
        if (isset($arrStaticDeliveryOption[$strMemKey])) {
            return $arrStaticDeliveryOption[$strMemKey];
        }

        // PS 1.7.3 use JSON.
        $arrDeliveryOption = TNTOfficiel_Tools::decJSON($objPSCart->delivery_option);
        if (!is_array($arrDeliveryOption)) {
            $arrDeliveryOption = TNTOfficiel_Tools::unserialize($objPSCart->delivery_option);
        }
        if (!is_array($arrDeliveryOption)) {
            $arrDeliveryOption = array();
        }

        // Mem.
        $arrStaticDeliveryOption[$strMemKey] = $arrDeliveryOption;

        return $arrDeliveryOption;
    }

    /**
     * Determine if cart multi-shipping state (delivery option) is supported.
     *
     * @param Cart $objArgCart
     *
     * @return bool
     */
    public function isMultiShippingSupport()
    {
        TNTOfficiel_Logstack::log();

        $boolMultiShippingSupport = true;

        $arrDeliveryOption = $this->getDeliveryOption();
        // If multiple address for cart.
        if (count($arrDeliveryOption) > 1) {
            // Not supported.
            $boolMultiShippingSupport = false;
        } else {
            // If an address have an option with different carrier.
            foreach ($arrDeliveryOption as /*$id_address_delivery =>*/ $strCarrierIDList) {
                if (preg_match('/^(?:([0-9]++),?(?:\1,?)*)$/ui', $strCarrierIDList) !== 1) {
                    // Not supported.
                    $boolMultiShippingSupport = false;
                    break;
                }
            }
        }

        return $boolMultiShippingSupport;
    }

    /**
     * is cart consistent for payment, then order creation ?
     *
     * @return array
     */
    public function isPaymentReady($boolIsAddressOptional = false)
    {
        TNTOfficiel_Logstack::log();

        $arrResult = array(
            'error' => null,
            'carrier' => null,
        );

        $objPSCart = $this->getPSCart();

        // If only virtual products in cart, no check.
        if ($objPSCart->isVirtualCart()) {

            return $arrResult;
        }

        if ($objPSCart === null) {
            // Cart object not available.
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorTechnical');

            return $arrResult;
        } elseif ($objPSCart->id_carrier == 0) {
            // No carrier selected.
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorNoDeliveryCarrierSelected');

            return $arrResult;
        }

        $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($objPSCart->id_carrier);
        // If Carrier object not available.
        if ($objPSCarrier === null) {
            // Carrier object not available.
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorTechnical');

            return $arrResult;
        } else {
            // Flag selected (core) carrier as TNT,  handle it.
            $arrResult['carrier'] = $objPSCarrier->external_module_name;
        }

        // If is not a TNT carrier.
        if (!TNTOfficielCarrier::isTNTOfficielCarrierID($objPSCart->id_carrier)) {
            // Must control only TNT carriers.
            return $arrResult;
        }

        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($objPSCart->id_carrier, false);
        // If fail.
        if ($objTNTCarrierModel === null) {
            // TNT Carrier object not available.
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorTechnical');

            return $arrResult;
        }

        $arrDeliveryOption = $this->getDeliveryOption();
        // If no option.
        if (count($arrDeliveryOption) === 0) {
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorNoDeliveryOptionSelected');

            return $arrResult;
        }

        // Multi-Shipping with multiple address or different carrier not supported.
        $boolMultiShippingSupport = $this->isMultiShippingSupport();
        if (!$boolMultiShippingSupport) {
            // TNT shouldn't be selected (no price available).
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorWrongDeliveryCarrierSelected');

            return $arrResult;
        }

        if ($objPSCart->id_address_delivery == 0) {
            if ($boolIsAddressOptional !== true) {
                // No Delivery Address selected.
                $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorNoDeliveryAddressSelected');
            }

            return $arrResult;
        }

        // Get Prestashop delivery address of cart.
        $objPSAddressDelivery = TNTOfficielReceiver::getPSAddressByID($objPSCart->id_address_delivery);
        if ($objPSAddressDelivery === null) {
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorTechnical');

            return $arrResult;
        } elseif ($objPSAddressDelivery->deleted) {
            // Address deleted !
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorDeliveryAddressDeleted');

            return $arrResult;
        }

        if (!array_key_exists($objPSCart->id_address_delivery, $arrDeliveryOption)) {
            // Inconsistency (selected address not found in option).
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorDeliveryOptionInconsistency');

            return $arrResult;
        } else {
            $strCarrierID = preg_replace('/,$/ui', '', $arrDeliveryOption[$objPSCart->id_address_delivery]);
            $arrCarrierID = array_flip(explode(',', $strCarrierID));
            if (!array_key_exists($objPSCart->id_carrier, $arrCarrierID)) {
                // Inconsistency (selected carrier not found in option).
                $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorDeliveryOptionInconsistency');

                return $arrResult;
            }
        }

        // Load TNT receiver info or create a new one for its ID.
        $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($objPSCart->id_address_delivery);
        if ($objTNTReceiverModel === null) {
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorTechnical');

            return $arrResult;
        } else {
            // Validate current receiver info.
            $arrFormReceiverInfoValidate = TNTOfficielReceiver::validateReceiverInfo(
                $objTNTReceiverModel->getAddressCountryISOCode(),
                $objTNTReceiverModel->receiver_email,
                $objTNTReceiverModel->receiver_mobile,
                $objTNTReceiverModel->receiver_building,
                $objTNTReceiverModel->receiver_accesscode,
                $objTNTReceiverModel->receiver_floor,
                $objTNTReceiverModel->receiver_instructions
            );
            if ($arrFormReceiverInfoValidate['length'] !== 0) {
                $arrResult['error'] = TNTOfficiel::getCodeTranslate('validateAdditionalCarrierInfo');

                return $arrResult;
            }
        }

        // Get selected TNT Carrier delivery point.
        if ($this->isDeliveryPoint()
            && !$this->hasDeliveryPoint()
        ) {
            $arrResult['error'] = TNTOfficiel::getCodeTranslate('errorNoDeliveryPointSelected');

            return $arrResult;
        }

        return $arrResult;
    }

    /**
     * Clear checkout step.
     *
     * @return string|true string on error.
     */
    public function flushCheckoutState()
    {
        $objPSCart = $this->getPSCart();
        // If Cart object not available.
        if ($objPSCart === null) {
            return TNTOfficiel::getCodeTranslate('errorTechnical');
        }

        $strTablePrefix = _DB_PREFIX_;

        $arrCartDefinition = ObjectModel::getDefinition($objPSCart);

        $strTableName = $strTablePrefix . $arrCartDefinition['table'];

        $intCartID = $this->id_cart;
        //$intCartID = $objPSCart->id;

        // Update table.
        $strSQLCartClearStep = <<<SQL
UPDATE `${strTableName}`
    SET checkout_session_data = NULL WHERE id_cart = ${intCartID};
SQL;

        $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCartClearStep);

        return $boolDBResult;
    }
}
