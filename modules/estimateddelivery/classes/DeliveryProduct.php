<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015-2018
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

class DeliveryProduct
{
    const DEFAULT_AVAILABLE_DATE = '0000-00-00';
    private static $dp_cache = [];

    private static $EMPTY_COMBINATION_DATA = [
        'delay' => '0',
        'picking_days' => '0',
        'customization_days' => '0',
        'disabled' => '0',
        'release_date' => '',
        'available_date' => '',
    ];
    /* Product Name */
    public $name;
    /* Product Identifier */
    public $id_product;
    /* Combination Identifier */
    public $id_product_attribute;
    /* Default Attribute */
    public $id_default_attribute;
    /* Combination Identifier */
    public $id_category_default;
    /* Combination Identifier */
    public $id_manufacturer;
    /* Combination Identifier */
    public $id_supplier;
    /* Shop Identifier */
    public $id_shop;
    /** @var string Width in default width unit */
    public $width = 0;
    /** @var string Height in default height unit */
    public $height = 0;
    /** @var string Depth in default depth unit */
    public $depth = 0;
    /** @var string Weight in default weight unit */
    public $weight = 0;
    /* Days to add for OOS products */
    public $isOOS;
    /* Days to add for OOS products */
    public $canOOS;
    /* If the product uses the basic or the advanced stock management */
    public $depends_on_stock;
    /* Bool is product customizable */
    public $customizable = false;
    /* Days to add for OOS products */
    /* BOOL add customization days */
    public $is_custom = false;
    /* Days to add in the customization */
    public $add_custom_days;
    public $oos_add_days;
    /* Days to add in the picking */
    public $add_picking_days;
    /* BOOL Is a product Pack */
    public $is_pack = false;
    /* BOOL Is release date */
    public $is_release = false;
    /* Future release date, if set */
    public $release_date;
    /* BOOL Is Virtual Product */
    public $is_virtual = false;
    /* BOOL Is Undefined Delivery */
    public $is_undefined_delivery = false;
    /* BOOL Don't have stock and has a future Available date */
    public $is_available = false;
    /* Restock date for the product, if no OOS sale enabled */
    public $available_date = '';
    /* Compute the Additional Out Of stock Days for available dates? */
    public $compute_available_oos;
    /* Available quantity */
    public $quantity;
    protected $original_qty = false;
    /* Quantity wanted (for cart involved process) */
    public $quantity_wanted;
    /* Special missage to display only for Virtual Products or Available / Release dates */
    public $msg;
    private $release_data;
    private $available_data;
    public $formatted_date;
    private static $adv_stock;
    private $wh_quantity;
    // The ID for the warehouse with enough stock
    public $id_warehouse = 0;
    public $warehouse_add_days = 0;
    private static $module;

    /**
     * @var false|int
     */
    public function __construct($product, $id_product, $id_product_attribute, $id_category_default, $id_shop, $quantity_wanted, $quantity, $is_order)
    {
        $context = Context::getContext();
        self::$module = Module::getInstanceByName('estimateddelivery');
        if (!isset(self::$adv_stock)) {
            self::$adv_stock = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
        }
        $cache_key = 'DeliveryProduct_' . (int) $id_product . '_' . (int) $id_product_attribute . '_' . (int) $id_shop . '_' . $context->language->id;
        if (Cache::isStored($cache_key)) {
            // Load the cached product data
            $cachedProduct = Cache::retrieve($cache_key);
            foreach ($cachedProduct as $key => $value) {
                $this->{$key} = $value;
            }
        } else {
            // Static Product Data (independent of the quantities and availability)
            $this->populateStaticProductData($product, $id_product, $id_product_attribute, $id_shop, $id_category_default, $context);

            // Load combination-specific data if necessary
            if ($this->id_product_attribute > 0) {
                $this->loadCombinationData($product, $context);
            }

            // Check product release and customization
            $this->checkProductRelease();

            if ($this->customizable && Configuration::get('ed_enable_custom_days')) {
                $this->checkProductCustomization($context);
                if ($this->is_custom) {
                    $this->checkCategoryCustomization();
                }
            }

            // Set virtual product flag
            $this->is_virtual = $product->is_virtual;
            // Cache the product data
            Cache::store($cache_key, $this);
        }

        // Get The product Additional Days, will use the cache if data is available
        $cache_key = 'DeliveryProductAdditionalData' . (int) $id_product . '_' . (int) $id_product_attribute . '_' . (int) $id_shop;
        $this->getProductAdditionalDays($cache_key);

        // Start Quantity data handling
        $this->quantity_wanted = (int) $quantity_wanted;

        if ($quantity !== false) {
            $this->setQuantity($quantity);
        }

        // Process quantity related data
        if ($this->id_product_attribute > 0) {
            // Separate the quantity-related logic
            $this->processCombinationQuantity();

            // Handle available date
            $this->processCombinationAvailableDate();
        } else {
            if (!isset($this->quantity)) {
                $this->setQuantity($product->getQuantity($id_product));
            }
        }
        $this->setCanOOS();

        if ($this->isOOS) {
            $this->setAvailableDate($product, $id_product_attribute);
            $this->is_undefined_delivery = $this->setHasUndefinedDelivery();
        }

        $this->getSpecialMessage();
    }

    private function populateStaticProductData($product, $id_product, $id_product_attribute, $id_shop, $id_category_default, $context)
    {
        $this->name = $product->name[$context->language->id];
        $this->id_product = (int) $id_product;
        $this->id_shop = (int) $id_shop;
        $this->id_product_attribute = (int) $id_product_attribute;
        $this->id_category_default = (int) $id_category_default;
        $this->id_manufacturer = $product->id_manufacturer;
        $this->id_supplier = $product->id_supplier;
        $this->depends_on_stock = $product->depends_on_stock ?? StockAvailable::dependsOnStock($this->id_product);
        $this->customizable = Customization::isFeatureActive() && $product->customizable;

        // Set product measures (height, width, depth, weight)
        $this->setProductMeasures($product);

        // Load additional product data (custom logic based on your requirements)
        $this->loadProductData();
    }

    private function setProductMeasures($p)
    {
        $dimensions = ['width', 'depth', 'weight', 'height'];
        foreach ($dimensions as $k) {
            if ($p->{$k} > 0) {
                $this->{$k} = $p->{$k};
            }
        }
    }

    /**
     * Get the product Data only if the element doesn't exist
     * This will be later used by other processess of the module like checkProductCustomization, checkProductRelease...
     */
    private function loadProductData()
    {
        if (!isset(self::$dp_cache[$this->id_product][0])) {
            $sql = 'SELECT delay AS oos_add_days, picking_days AS add_picking_days, customization_days AS add_custom_days, release_date, disabled  FROM ' . _DB_PREFIX_ . 'ed_prod WHERE id_product = ' . (int) $this->id_product . ' ' . Shop::addSqlRestriction();
            $results = Db::getInstance()->getRow($sql);
            //            Tools::dieObject($results);
            if ($results !== false) {
                self::$dp_cache[$this->id_product][0] = $results;
            }
        }
    }

    /**
     * Sets up the delivery date as undefined and displays the special message for the undefined delivery dates
     * Can work with suppliers or manufacturers, depending on the shop settings
     *
     * @return bool
     */
    private function setHasUndefinedDelivery()
    {
        switch (Configuration::get('ED_UNDEFINED_DAYS_MODE')) {
            case 1:
                $mode = 'supplier';
                break;
            case 2:
                $mode = 'manufacturer';
                break;
            default:
                return false;
        }
        if ($this->{'id_' . $mode} > 0 && !$this->is_available) {
            $sql = 'SELECT undefined_delivery FROM ' . _DB_PREFIX_ . 'ed_' . $mode . ' WHERE id_' . $mode . ' = ' . (int) $this->{'id_' . $mode} . ' ' . Shop::addSqlRestriction();

            return (bool) Db::getInstance()->getValue(pSQL($sql));
        }

        return false;
    }

    /*
    ** Fills in the combination data for 1.7 Products
    */
    private function loadCombinationData($product, $context)
    {
        $combination = $product->getAttributeCombinationsById($this->id_product_attribute, $context->language->id);
        $combination = reset($combination);

        if (empty($combination)) {
            return false;
        }

        $excludedKeys = ['id_product', 'id_product_attribute']; // List of keys that should not be overwritten
        foreach ($combination as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $excludedKeys)) {
                if ($key === 'weight') {
                    // Sum the original product weight with the combination weight
                    $this->setCombinationWeight($value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }

        $this->getEDCombinationData();
        $this->setDefaultAttribute();

        return true;
    }

    private function processCombinationQuantity()
    {
        // Handle advanced mode and quantity from database
        if (Configuration::get('ed_adv_mode') && Configuration::get('ED_GET_QUANTITY_FROM_DATABASE')) {
            $this->original_qty = $this->quantity;
            $this->quantity = Db::getInstance()->getValue('SELECT SUM(quantity) FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product = ' . (int) $this->id_product . ' AND id_product_attribute = ' . (int) $this->id_product_attribute);
        }

        // Set the combination quantity if not already set
        if (!isset($this->quantity)) {
            $this->setCombinationQuantity($this->quantity);
        }
        if (isset($this->quantity)) {
            $this->isOOS = $this->isOOS();
        }
    }

    private function processCombinationAvailableDate()
    {
        if ($this->quantity > 0) {
            return;
        }

        // Check if the available date is set
        if ($this->available_date != self::DEFAULT_AVAILABLE_DATE && DeliveryHelper::isFutureDate($this->available_date)) {
            $this->is_available = true;
        } else {
            $this->getAvailableDate();
        }
    }

    private function handleAdvancedStock()
    {
        $this->addWarehousesQuantities();
        $this->setActiveWarehouse();
    }

    private function getEDCombinationData()
    {
        // Check if data is already in cache
        if ($this->isCombinationDataCached()) {
            return self::$dp_cache[$this->id_product][$this->id_product_attribute];
        }

        // Fetch from database
        $data = $this->fetchCombinationDataFromDatabase();

        // Set default value for 'available_date'
        if (!empty($data)) {
            $data['available_date'] = isset($data['restock_date']) ? $data['restock_date'] : '';
        }

        // Cache and return the result
        self::$dp_cache[$this->id_product][$this->id_product_attribute] = empty($data) ? self::$EMPTY_COMBINATION_DATA : $data;

        return self::$dp_cache[$this->id_product][$this->id_product_attribute];
    }

    private function isCombinationDataCached()
    {
        return isset(self::$dp_cache[$this->id_product][$this->id_product_attribute]);
    }

    private function fetchCombinationDataFromDatabase()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ed_prod_combi WHERE id_product = ' . (int) $this->id_product . ' AND id_product_attribute = ' . (int) $this->id_product_attribute . ' ' . Shop::addSqlRestriction();

        return Db::getInstance()->getRow($sql) ?: [];
    }

    /*private function isCombinationDisabled()
    {
        return self::$dp_cache[$this->id_product][$this->id_product_attribute]['disabled'];
    }*/
    /**
     * Checks if the product combination or the product has a future release date
     */
    public function checkProductRelease()
    {
        $attributes = [0];
        if ($this->id_product_attribute > 0) {
            $attributes[] = $this->id_product_attribute;
        }
        while (!empty($attributes)) {
            $att = array_pop($attributes);
            if (isset(self::$dp_cache[$this->id_product][$att]['release_date'])) {
                $date = self::$dp_cache[$this->id_product][$att]['release_date'];
                if (DeliveryHelper::isFutureDate($date)) {
                    $this->is_release = true;
                    $this->release_date = $date;
                    break;
                }
            }
        }
    }

    public function checkProductCustomization($context)
    {
        // Stop if the product is not customizable
        if (!$this->customizable || !Configuration::get('ed_enable_custom_days')) {
            return;
        }

        // Set the is_custom variable based on logic
        $this->setIsCustom($context);
    }

    /**
     * Determines if the customization days from the module should be used
     * Only if the product is customizable and complies with the custom days settings in the module ed_custom_days_mode:
     * 2 = Always
     * 0 = Only if the product is customized
     * 1 = If the product has all fields mandatory or has been customized
     */
    private function setIsCustom($context)
    {
        $custom_days_mode = (int) Configuration::get('ed_custom_days_mode');
        $this->is_custom = false; // Default state

        // Mode 2: Always custom
        if ($custom_days_mode === 2) {
            $this->is_custom = true;

            return;
        }

        // Determine the controller-specific behavior
        $controller_name = EDTools::getControllerName();

        if (!in_array($controller_name, ['product', 'order', 'order-opc'])) {
            return; // Exit early for unexpected controllers
        }

        $not_in_cart = ($controller_name === 'product') ? false : true;
        $customizations = $context->cart->getProductCustomization($this->id_product, null, $not_in_cart);

        // Mode 0: Only if customized
        if ($custom_days_mode === 0) {
            $this->is_custom = !empty($customizations);

            return;
        }

        // Mode 1: Customized or all fields mandatory
        if ($custom_days_mode === 1) {
            if (!empty($customizations)) {
                $this->is_custom = true;
            } else {
                $product = new Product($this->id_product);
                $this->is_custom = $product->hasAllRequiredCustomizableFields();
            }
        }
    }

    public function checkCategoryCustomization()
    {
        $rd = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT customization_days FROM ' . _DB_PREFIX_ . 'ed_cat WHERE id_category = ' . (int) $this->id_category_default . ' ' . Shop::addSqlRestriction());
        if ($rd != false && (int) $rd > 0) {
            $this->is_custom = true;
        }
    }

    public function getAvailableDate()
    {
        if (isset(self::$dp_cache[$this->id_product][$this->id_product_attribute])) {
            $this->available_date = self::$dp_cache[$this->id_product][$this->id_product_attribute]['available_date'];
            if ($this->available_date != '' && DeliveryHelper::isFutureDate($this->available_date)) {
                $this->is_available = true;
            }
        }
    }

    public function isVirtual()
    {
        return $this->is_virtual;
    }

    public function isRelease()
    {
        return $this->is_release;
    }

    public function isAvailableDate()
    {
        // Review the available date
        if ($this->available_date !== false && !$this->isOOS) {
            $this->is_available = false;
        }

        return $this->is_available;
    }

    public function setAvailableDate($product, $id_product_attribute)
    {
        if ($this->available_date == '') {
            if ($id_product_attribute > 0) {
                if (!isset($product->available_date) || $id_product_attribute > 0) {
                    $product->available_date = Product::getAvailableDate($product->id, $id_product_attribute);
                }
                if (DeliveryHelper::isFutureDate($product->available_date)) {
                    $this->available_date = $product->available_date;
                    $this->is_available = true;
                // $this->isOOS = 0;
                } else {
                    $this->getAvailableDate();
                }
            } else {
                if (isset($product->available_date) && DeliveryHelper::isFutureDate($product->available_date)) {
                    $this->available_date = $product->available_date;
                    $this->is_available = true;
                    // $this->isOOS = 0;
                }
            }
        }
    }

    public function getSpecialMessage()
    {
        if ($this->is_undefined_delivery) {
            $this->msg = self::$module->getUndefinedMessage();
        } elseif ($this->is_release) {
            $this->msg = self::$module->getReleaseMessage();
        } elseif ($this->is_available) {
            $this->msg = self::$module->getAvailableMessage();
        } elseif ($this->is_custom) {
            $this->msg = self::$module->getCustomizationMessage();
        }
    }

    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;
        $this->isOOS = $this->isOOS();
        if (self::$adv_stock) {
            $this->addWarehousesQuantities();
            $this->setActiveWarehouse();
        }
    }

    private function addWarehousesQuantities()
    {
        $sm = new StockManager();
        $whs = Warehouse::getWarehouses();

        if ($this->depends_on_stock) {
            if (method_exists('StockManager', 'getPhysicalProductQuantities')) {
                foreach ($whs as $wh) {
                    $this->wh_quantity[$wh['id_warehouse']] = $sm->getPhysicalProductQuantities(
                        [
                            'product_id' => $this->id_product,
                            'product_attribute_id' => $this->id_product_attribute,
                            'warehouse_id' => $wh['id_warehouse'],
                        ]
                    );
                }
            } else {
                foreach ($whs as $wh) {
                    $this->wh_quantity[$wh['id_warehouse']] = $sm->getProductPhysicalQuantities($this->id_product, $this->id_product_attribute, $wh['id_warehouse']);
                }
            }
        } else {
            // The stock is controlled on the product edit page
            foreach ($whs as $wh) {
                $this->wh_quantity[$wh['id_warehouse']] = $this->quantity;
            }
        }
    }

    private function setActiveWarehouse()
    {
        $list = Warehouse::getProductWarehouseList($this->id_product, $this->id_product_attribute, Context::getContext()->shop->id);
        $prod_wh = [];
        if (!empty($list)) {
            foreach ($list as $wh) {
                $prod_wh[] = $wh['id_warehouse'];
            }
        }
        unset($list);
        foreach ($this->wh_quantity as $id_warehouse => $qty) {
            if (isset($prod_wh) && in_array($id_warehouse, $prod_wh) && $this->quantity_wanted <= $qty) {
                $this->id_warehouse = (int) $id_warehouse;
                $this->warehouse_add_days = $this->getProductWarehouseAdditionalDays();

                return;
            }
        }
    }

    private function getProductWarehouseAdditionalDays()
    {
        $context = Context::getContext();
        $column = [1 => 'supplier', 2 => 'manufacturer'];
        $column = $column[Configuration::get('ED_WAREHOUSES_MODE', null, null, null, 2)];

        return Db::getInstance()->getValue('SELECT picking_days FROM ' . _DB_PREFIX_ . 'ed_warehouse WHERE id_warehouse = ' . (int) $this->id_warehouse . ' AND id_' . bqSQL($column) . ' = ' . pSQL($this->{'id_' . $column}) . ' ' . Shop::addSqlRestriction());
    }

    public function setDefaultAttribute()
    {
        $this->id_default_attribute = Product::getDefaultAttribute($this->id_product);
    }

    public function setCombinationQuantity($force_quantity = false)
    {
        if ($force_quantity !== false) {
            $this->quantity = (int) $force_quantity;
        } else {
            $cache_key = 'ProductAttributeQuantity_' . $this->id_product . '_' . $this->id_product_attribute . '_' . Context::getContext()->shop->id;
            if (!Cache::isStored($cache_key)) {
                if (self::$adv_stock) {
                    $this->addWarehousesQuantities();
                    $this->setActiveWarehouse();
                }
                $result = StockAvailable::getQuantityAvailableByProduct($this->id_product, $this->id_product_attribute);
                Cache::store(
                    $cache_key,
                    $result
                );
                $this->quantity = $result;
            } else {
                $this->quantity = Cache::retrieve($cache_key);
            }
        }
        $this->isOOS = $this->isOOS();
    }

    public function setCombinationWeight($weight = 0)
    {
        if ($weight == 0) {
            $sql = 'SELECT weight FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . (int) $this->id_product_attribute;
            $weight = Db::getInstance()->getValue(pSQL($sql));
        }
        if (is_numeric($weight)) {
            $this->weight += $weight;
        }
    }

    /**
     * Check if the product can be sold if it's OOS
     * If the
     */
    private function setCanOOS()
    {
        $sa = StockAvailable::outOfStock($this->id_product);
        $this->canOOS = !Configuration::get('ED_DISABLE_OOS')
            && (
                $sa == 1
                || (
                    $sa == 2
                    && (int) Configuration::get('PS_ORDER_OUT_OF_STOCK')
                )
                || (
                    $this->original_qty !== false
                    && Configuration::get('ED_SET_CAN_OOS_IF_ORIGINAL_IS_POSITIVE')
                    && $this->original_qty > $this->quantity_wanted
                )
            );
    }

    public function isOOS()
    {
        $neg_qty = ($this->quantity - $this->quantity_wanted) < 0;
        if (!Configuration::get('PS_STOCK_MANAGEMENT')
           || (!$neg_qty)
           || ($neg_qty && DeliveryHelper::isFutureDate($this->available_date))) {
            return 0;
        }

        return 1;
    }

    private function getProductAdditionalDays($cache_key)
    {
        // Check if the additional days are already cached
        if (Cache::isStored($cache_key)) {
            // Retrieve cached days and apply them directly
            $days = Cache::retrieve($cache_key);

            // Apply additional days to the current product/combination
            if (is_array($days)) {
                $tmp_days = $this->id_product_attribute > 0 ? $days[$this->id_product_attribute] : $days;
                $this->oos_add_days = $tmp_days['oos_add_days'];
                $this->add_picking_days = $tmp_days['add_picking_days'];
                $this->add_custom_days = $tmp_days['customization_days'] ?? 0;
            }

            if (self::$module::$debug_mode) {
                self::$module->debugVar('', 'ED: Product additional Days Loaded from Cache');
            }

            return $this;
        }

        $days = false;

        // Function List with priority based on configurations
        $flist = ['getGlobalOOS', 'getGlobalCustom'];

        // Add specific priority based on configuration
        switch ((int) Configuration::get('ED_ADD_OOS_DAYS_MODE')) {
            case 0:
                $flist[] = 'getCatOOS';
                break;
            case 1:
                $flist[] = 'getSupplierOOS';
                break;
            case 2:
                $flist[] = 'getManufacturerOOS';
                break;
        }

        switch ((int) Configuration::get('ED_ADD_PICKING_MODE')) {
            case 0:
                $flist[] = 'getCatPicking';
                break;
            case 1:
                $flist[] = 'getManufacturerAdditional';
                break;
            case 2:
                $flist[] = 'getSupplierAdditional';
                break;
        }

        switch ((int) Configuration::get('ED_ADD_CUSTOM_DAYS_MODE')) {
            case 0:
                $flist[] = 'getCatCustom';
                break;
            case 1:
                $flist[] = 'getSupplierCustom';
                break;
            case 2:
                $flist[] = 'getManufacturerCustom';
                break;
        }

        $flist[] = 'getProductAdditional';

        // Handle combinations if present
        if ($this->id_product_attribute > 0) {
            $flist[] = 'getCombiAdditional';
        }

        // Process each function in reverse order using array_pop
        while (!empty($flist)) {
            $func = array_pop($flist);
            $tmp = ($func === 'getGlobalOOS' || $func === 'getGlobalCustom')
                ? self::{$func}()
                : self::{$func}($this);

            if ($tmp !== false) {
                $days = $days === false ? $tmp : self::processAdditionalDays($days, $tmp);
            }
        }

        // Apply additional days to the current product/combination
        if (is_array($days) && !empty($days)) {
            $tmp_days = $this->id_product_attribute > 0 ? $days[$this->id_product_attribute] : $days;
            $this->oos_add_days = $tmp_days['oos_add_days'];
            $this->add_picking_days = $tmp_days['add_picking_days'];
            $this->add_custom_days = $tmp_days['add_custom_days'];
        }

        // Cache the calculated days
        Cache::store($cache_key, $days);

        if (self::$module::$debug_mode) {
            self::$module->debugVar('', 'ED: Product additional Days Added to Cache');
        }

        return $this;
    }

    protected static function arrayKeyFirst($arr)
    {
        if (!function_exists('array_key_first')) {
            foreach ($arr as $key => $unused) {
                return $key;
            }

            return null;
        }

        return array_key_first($arr);
    }

    private static function processAdditionalDays($days, $tmp)
    {
        foreach ($tmp as $k => $v) {
            if ($v > 0) {
                foreach ($days as $key => $day) {
                    if (is_array($day)) {
                        // Product with combinations
                        foreach ($day as $ck => $cv) {
                            if (($days[$key][$ck] == 0) && ($k == $ck)) {
                                $days[$key][$ck] = (int) $v;
                            }
                        }
                    } else {
                        // Product without combinations
                        if ($days[$key] == 0 && $key == $k) {
                            $days[$key] = (int) $v;
                        }
                    }
                }
            }
        }

        return $days;
    }

    private static function getcombiAdditional($dp)
    {
        if (isset(self::$dp_cache[$dp->id_product][$dp->id_product_attribute])) {
            return [$dp->id_product_attribute => [
                'oos_add_days' => self::$dp_cache[$dp->id_product][$dp->id_product_attribute]['delay'],
                'add_picking_days' => self::$dp_cache[$dp->id_product][$dp->id_product_attribute]['picking_days'],
                'add_custom_days' => self::$dp_cache[$dp->id_product][$dp->id_product_attribute]['customization_days'],
                ],
            ];
        }
    }

    public static function getProductAdditional($dp)
    {
        if (isset(self::$dp_cache[$dp->id_product][0])) {
            return self::$dp_cache[$dp->id_product][0];
        } else {
            return false;
        }
    }

    public static function getCatAdditional($dp, $type = '')
    {
        if ($type == '') {
            $type = 'delay AS oos_add_days, picking_days AS add_picking_days, customization_days AS add_custom_days';
        }
        $sql = 'SELECT ' . bqSQL($type) . ' FROM ' . _DB_PREFIX_ . 'ed_cat WHERE id_category = ' . (int) $dp->id_category_default . ' ' . Shop::addSqlRestriction();
        // $sql = 'SELECT '.pSQL($data_type).' FROM '._DB_PREFIX_.'ed_cat_oos WHERE  '.pSQL($data_type).' > 0 AND id_category = '.(int)$dp->id_product.' AND id_shop = '.(int)$dp->id_shop;
        $results = DB::getInstance()->getRow(pSQL($sql));
        if ($results != false && count($results) > 0) {
            return
                [
                    'oos_add_days' => isset($results['oos_add_days']) ? $results['oos_add_days'] : 0,
                    'add_picking_days' => isset($results['add_picking_days']) ? $results['add_picking_days'] : 0,
                    'add_custom_days' => isset($results['add_custom_days']) ? $results['add_custom_days'] : 0,
                ];
        }

        return false;
    }

    public static function getManufacturerAdditional($dp)
    {
        return self::getAdditionalPicking($dp, 'manufacturer');
    }

    public static function getSupplierAdditional($dp)
    {
        return self::getAdditionalPicking($dp, 'supplier');
    }

    public static function getAdditionalPicking($dp, $name = '')
    {
        if ($name != 'manufacturer' && $name != 'supplier') {
            exit;
        }
        if ((int) $dp->{'id_' . $name} > 0) {
            $sql = 'SELECT picking_days FROM ' . _DB_PREFIX_ . 'ed_' . bqSQL($name) . ' WHERE id_' . bqSQL($name) . ' = ' . (int) $dp->{'id_' . $name} . ' AND id_shop = ' . (int) $dp->id_shop;
            $result = DB::getInstance()->getValue(pSQL($sql));
            if ($result != false) {
                return
                    [
                        'oos_add_days' => 0,
                        'add_picking_days' => (int) $result,
                        'add_custom_days' => 0,
                    ];
            }
        }

        return [
            'oos_add_days' => 0,
            'add_picking_days' => 0,
            'add_custom_days' => 0,
        ];
    }

    public static function getManufacturerCustom($dp)
    {
        return self::getAdditionalCustom($dp, 'manufacturer');
    }

    public static function getSupplierCustom($dp)
    {
        return self::getAdditionalCustom($dp, 'supplier');
    }

    public static function getAdditionalCustom($dp, $name = '')
    {
        if ($name != 'manufacturer' && $name != 'supplier') {
            exit;
        }
        $result = 0;
        if ((int) $dp->{'id_' . $name} > 0) {
            $sql = 'SELECT customization_days FROM ' . _DB_PREFIX_ . 'ed_' . bqSQL($name) . ' WHERE id_' . bqSQL($name) . ' = ' . (int) $dp->{'id_' . $name} . ' AND id_shop = ' . (int) $dp->id_shop;
            $result = DB::getInstance()->getValue(pSQL($sql));
        }

        return [
            'oos_add_days' => 0,
            'add_picking_days' => 0,
            'add_custom_days' => (int) $result,
        ];
    }

    public static function getCatOOS($dp)
    {
        return self::getCatAdditional($dp, 'delay AS oos_add_days');
    }

    public static function getCatPicking($dp)
    {
        return self::getCatAdditional($dp, 'picking_days AS add_picking_days');
    }

    public static function getCatCustom($dp)
    {
        return self::getCatAdditional($dp, 'customization_days AS add_custom_days');
    }

    public static function getSupplierOOS($dp)
    {
        return self::getAdditionalOOS($dp, 'supplier');
    }

    public static function getManufacturerOOS($dp)
    {
        return self::getAdditionalOOS($dp, 'manufacturer');
    }

    public static function getAdditionalOOS($dp, $name = '')
    {
        if ($name != 'manufacturer' && $name != 'supplier') {
            exit;
        }
        $sql = 'SELECT delay AS oos_add_days FROM ' . _DB_PREFIX_ . 'ed_' . $name . ' WHERE id_' . $name . ' = ' . (int) $dp->{'id_' . $name} . ' AND id_shop = ' . (int) $dp->id_shop;
        $result = DB::getInstance()->getValue(pSQL($sql));
        if ($result != false) {
            return
                [
                    'oos_add_days' => $result,
                    'add_picking_days' => 0,
                    'add_custom_days' => 0,
                ];
        }

        return false;
    }

    public static function getGlobalOOS()
    {
        return ['oos_add_days' => Configuration::get('ed_oos'), 'add_picking_days' => 0, 'add_custom_days' => 0];
    }

    public static function getGlobalCustom()
    {
        return ['add_custom_days' => Configuration::get('ed_custom_days'), 'oos_add_days' => 0, 'add_picking_days' => 0];
    }

    private function setProductAttribute($id_product_attribute)
    {
        $this->id_product_attribute = (int) $id_product_attribute;
    }

    private function setProductQuantity($quantity)
    {
        $this->quantity = (int) $quantity;
    }

    public function ignorePickingDays()
    {
        $this->add_picking_days = 0;
    }
}
