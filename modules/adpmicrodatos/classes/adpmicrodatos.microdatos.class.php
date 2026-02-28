<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdpmicrodatosMicrodatos
{
    private static $TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS = 'Organization/LocalBusiness';
    private static $TYPE_MICRODATA_WEBPAGE = 'WebPage';
    private static $TYPE_MICRODATA_WEBSITE = 'WebSite';
    private static $TYPE_MICRODATA_BREADCRUMBLIST = 'BreadcrumbList';
    private static $TYPE_MICRODATA_STORE = 'Store';
    private static $TYPE_MICRODATA_PRODUCT = 'Product';
    private static $TYPE_MICRODATA_ITEMLIST = 'ItemList';

    private static $PAGE_PRICESDROP = 'pricesdrop';
    private static $PAGE_BESTSALES = 'bestsales';
    private static $PAGE_NEWPRODUCTS = 'newproducts';

    private $name = 'AdpmicrodatosMicrodatos';
    private $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function getMicrodataList()
    {
        $microdata_list = [
            [
                'id' => self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS,
                'name' => 'Organization/LocalBusiness',
            ],
            [
                'id' => self::$TYPE_MICRODATA_WEBPAGE,
                'name' => 'WebPage',
            ],
            [
                'id' => self::$TYPE_MICRODATA_WEBSITE,
                'name' => 'WebSite',
            ],
            [
                'id' => self::$TYPE_MICRODATA_BREADCRUMBLIST,
                'name' => 'BreadcrumbList',
            ],
            [
                'id' => self::$TYPE_MICRODATA_STORE,
                'name' => 'Store',
            ],
            [
                'id' => self::$TYPE_MICRODATA_PRODUCT,
                'name' => 'Product',
            ],
            [
                'id' => self::$TYPE_MICRODATA_ITEMLIST,
                'name' => 'ItemList',
            ],
        ];

        return $microdata_list;
    }

    public function getMicrodataHomeList()
    {
        $microdata_list = $this->getMicrodataList();
        unset($microdata_list[5]);
        unset($microdata_list[6]);
        unset($microdata_list[3]);

        return $microdata_list;
    }

    public function getDefaultOptionMicrodataHomeList()
    {
        return [self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS,
            self::$TYPE_MICRODATA_WEBPAGE,
            self::$TYPE_MICRODATA_WEBSITE,
            self::$TYPE_MICRODATA_STORE, ];
    }

    public function getMicrodataListProduct()
    {
        $microdata_list = $this->getMicrodataList();
        unset($microdata_list[5]);
        unset($microdata_list[2]);

        return $microdata_list;
    }

    public function getDefaultOptionMicrodataListProduct()
    {
        return [self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS,
            self::$TYPE_MICRODATA_WEBPAGE,
            self::$TYPE_MICRODATA_BREADCRUMBLIST,
            self::$TYPE_MICRODATA_STORE,
            self::$TYPE_MICRODATA_ITEMLIST, ];
    }

    public function getMicrodataProduct()
    {
        $microdata_list = $this->getMicrodataList();
        unset($microdata_list[6]);
        unset($microdata_list[2]);

        return $microdata_list;
    }

    public function getDefaultOptionMicrodataProduct()
    {
        return [self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS,
            self::$TYPE_MICRODATA_WEBPAGE,
            self::$TYPE_MICRODATA_BREADCRUMBLIST,
            self::$TYPE_MICRODATA_STORE,
            self::$TYPE_MICRODATA_PRODUCT, ];
    }

    public function getMicrodataOtherPages()
    {
        $microdata_list = $this->getMicrodataList();
        unset($microdata_list[5]);
        unset($microdata_list[6]);
        unset($microdata_list[2]);
        unset($microdata_list[3]);

        return $microdata_list;
    }

    public function getDefaultOptionMicrodataOtherPages()
    {
        return [self::$TYPE_MICRODATA_ORGANIZATION_LOCALBUSINESS,
            self::$TYPE_MICRODATA_WEBPAGE,
            self::$TYPE_MICRODATA_BREADCRUMBLIST,
            self::$TYPE_MICRODATA_STORE, ];
    }

    public function getStores($character_separation_hours = '')
    {
        $stores = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_GET_STORES);

        $range_price = Db::getInstance()->executeS('
            SELECT MIN(`price`) as min, MAX(`price`) as max FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`) 
            WHERE p.`active` = 1 AND p.`id_shop_default` = ' . (int) $this->context->shop->id . ' AND 
            pl.`id_lang` = ' . (int) $this->context->language->id);

        foreach ($stores as &$store) {
            $store['imagen'] = '';
            if (file_exists(_PS_STORE_IMG_DIR_ . (int) $store['id_store'] . '.jpg')) {
                $store['imagen'] = AdpmicrodatosTools::getLinkRoot() . 'img/st/' . (int) $store['id_store'] . '.jpg';
            }

            $store['min_price'] = (!empty($range_price)) ? Tools::convertPrice(round($range_price[0]['min'], 2)) : '';
            $store['max_price'] = (!empty($range_price)) ? Tools::convertPrice(round($range_price[0]['max'], 2)) : '';

            if (!empty($character_separation_hours) && !empty($store['hours'])) {
                $aux_hours = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_STORE_HOURS, $store['hours']);
                $store['hours'] = [];
                if (!empty($aux_hours)) {
                    foreach ($aux_hours as $index => $row_hour) {
                        $row_hour = is_array($row_hour) ? $row_hour[0] : $row_hour;

                        $multitimetable = explode(',', $row_hour);

                        foreach ($multitimetable as $timetableitem) {
                            $timetable = explode($character_separation_hours, $timetableitem);

                            $store['hours'][] = [
                                'day' => self::GetDatyName($index),
                                'opens' => (!empty($timetable[0])) ? trim($timetable[0]) : '',
                                'closes' => (!empty($timetable[1])) ? trim($timetable[1]) : '',
                            ];
                        }
                    }
                }
            }

            if (!empty($store['id_country'])) {
                $country = new Country($store['id_country'], $this->context->language->id);
                if ($country->id) {
                    $store['country'] = pSQL($country->name);
                }
            }

            if (!empty($store['id_state'])) {
                $state = new State($store['id_state']);
                if ($state->id) {
                    $store['region'] = pSQL($state->name);
                }
            }

            $store['streetAddress'] = implode(', ', array_filter([$store['address1'], $store['address2']]));
        }

        return $stores;
    }

    private static function GetDatyName($dayIndex)
    {
        switch ($dayIndex) {
            case 0: return 'Monday';
            case 1: return 'Tuesday';
            case 2: return 'Wednesday';
            case 3: return 'Thursday';
            case 4: return 'Friday';
            case 5: return 'Saturday';
            default: return 'Sunday';
        }
    }

    public function getDatosShop($id_shop)
    {
        $datos_shop = [];
        $datos_shop['email_comercio'] = trim(Configuration::get('PS_SHOP_EMAIL'));
        $datos_shop['addressLocality'] = trim(Configuration::get('PS_SHOP_CITY'));
        $datos_shop['addressRegion'] = trim(Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_COUNTRY_ID')));
        $datos_shop['postalCode'] = trim(Configuration::get('PS_SHOP_CODE'));
        $datos_shop['streetAddress'] = trim(Configuration::get('PS_SHOP_ADDR1') . ' ' . Configuration::get('PS_SHOP_ADDR2'));
        $datos_shop['telefono_comercio'] = trim(Configuration::get('PS_SHOP_PHONE'));
        $datos_shop['country'] = trim(Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')));
        $datos_shop['latitude'] = (float) Configuration::get('PS_STORES_CENTER_LAT');
        $datos_shop['longitude'] = (float) Configuration::get('PS_STORES_CENTER_LONG');
        $datos_shop['region'] = '';

        $psShopStateId = Configuration::get('PS_SHOP_STATE_ID');
        if (!empty($psShopStateId)) {
            $state = new State($psShopStateId);
            if ($state->id) {
                $datos_shop['region'] = pSQL($state->name);
            }
        }

        $range_price = Db::getInstance()->executeS('
            SELECT MIN(`price`) as min, MAX(`price`) as max FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`) 
            WHERE p.`active` = 1 AND p.`id_shop_default` = ' . (int) $this->context->shop->id . ' AND 
            pl.`id_lang` = ' . (int) $this->context->language->id);

        $datos_shop['min_price'] = (!empty($range_price)) ? Tools::convertPrice(round($range_price[0]['min'], 2)) : '';
        $datos_shop['max_price'] = (!empty($range_price)) ? Tools::convertPrice(round($range_price[0]['max'], 2)) : '';

        return $datos_shop;
    }

    public function getCategorias($id_category, $tipo_imagen, $includeRootCategory, $includeHomeCategory)
    {
        $ignoredCategoryIds = [];
        if (Shop::isFeatureActive() && !$includeRootCategory) {
            $ignoredCategoryIds[] = Configuration::get('PS_ROOT_CATEGORY');
        }

        if (!$includeHomeCategory) {
            // Id de la categoría principal de la tienda
            $ignoredCategoryIds[] = Category::getRootCategory($this->context->language->id, $this->context->shop)->id;
        }

        $categoria_actual = new Category($id_category, $this->context->language->id, $this->context->shop->id);
        $todas_categorias = array_reverse($categoria_actual->getParentsCategories());
        $idiomas_activados = Language::getLanguages(true, Context::getContext()->shop->id);

        $link = new Link(AdpmicrodatosTools::getHttp());

        $info_imagen = [];
        $aux_imagen = $link->getCatImageLink($categoria_actual->link_rewrite, $categoria_actual->id_image, $tipo_imagen);
        if (false === strpos($aux_imagen, AdpmicrodatosTools::getHttp())) {
            $aux_imagen = AdpmicrodatosTools::getHttp() . $aux_imagen;
        }
        $info_imagen['img_categoria_actual'] = $aux_imagen;

        $result = [];
        foreach ($todas_categorias as $categoria) {
            if (in_array($categoria['id_category'], $ignoredCategoryIds)) {
                continue;
            }

            $datos_categoria = [];
            if ($categoria['id_category'] == Context::getContext()->shop->getCategory()) {
                $datos_categoria['url'] = AdpmicrodatosTools::getLinkRoot(count($idiomas_activados));
            } else {
                $datos_categoria['url'] = $link->getCategoryLink((int) $categoria['id_category'], $categoria['link_rewrite'], $this->context->language->id, null, $this->context->shop->id);
            }

            $datos_categoria['name'] = $categoria['name'];
            $result[] = $datos_categoria;
        }

        return [$result, $info_imagen];
    }

    public function getCategoriasFabricante($id_manufacturer, $includeRootCategory, $includeHomeCategory)
    {
        $ignoredCategoryIds = [];
        if (Shop::isFeatureActive() && !$includeRootCategory) {
            $ignoredCategoryIds[] = Configuration::get('PS_ROOT_CATEGORY');
        }

        if (!$includeHomeCategory) {
            // Id de la categoría principal de la tienda
            $ignoredCategoryIds[] = Category::getRootCategory($this->context->language->id, $this->context->shop)->id;
        }
        $result = [];
        $idiomas_activados = Language::getLanguages(true, Context::getContext()->shop->id);
        $id_categoria = Context::getContext()->shop->getCategory();

        if (!in_array($id_categoria, $ignoredCategoryIds)) {
            $aux_categoria = new Category($id_categoria, $this->context->language->id, $this->context->shop->id);

            $datos_categoria = [];
            $datos_categoria['url'] = AdpmicrodatosTools::getLinkRoot(count($idiomas_activados));
            $datos_categoria['name'] = $aux_categoria->name;
            $result[] = $datos_categoria;
        }

        $link = new Link(AdpmicrodatosTools::getHttp());
        $fabricante = new Manufacturer($id_manufacturer, $this->context->language->id);
        $datos_categoria = [];
        $datos_categoria['url'] = $link->getManufacturerLink($fabricante, null, $this->context->language->id, $this->context->shop->id);
        $datos_categoria['name'] = $fabricante->name;

        $result[] = $datos_categoria;

        return $result;
    }

    private function getIdProduct($producto)
    {
        $result = Configuration::get('ADP_SET_MICRODATA_ID_PRODUCT');

        $aux_id_product = is_object($producto) ? $producto->id : $producto['id_product'];
        $aux_reference_product = is_object($producto) ? $producto->reference : $producto['reference'];
        $aux_ean13_product = is_object($producto) ? $producto->ean13 : $producto['ean13'];

        $result = str_replace('{id_product}', $aux_id_product, $result);
        $result = str_replace('{reference_product}', $aux_reference_product, $result);
        $result = str_replace('{ean13_product}', $aux_ean13_product, $result);

        // Sino hay valor ponemos por defecto el id de producto
        if (empty($result)) {
            $result = $aux_id_product;
        }

        return $result;
    }

    private function getIdProductCombination($producto, $producto_combination)
    {
        $result = Configuration::get('ADP_SET_MICRODATA_ID_PRODUCT_COMBINATION');

        $aux_id_product = is_object($producto) ? $producto->id : $producto['id_product'];
        $aux_id_product_combination = is_object($producto_combination) ? $producto_combination->id : $producto_combination['id_product_attribute'];
        $aux_reference_combination = is_object($producto_combination) ? $producto_combination->reference : $producto_combination['reference'];
        $aux_ean13_combination = is_object($producto_combination) ? $producto_combination->ean13 : $producto_combination['ean13'];

        $result = str_replace('{id_product}', $aux_id_product, $result);
        $result = str_replace('{id_product_combination}', $aux_id_product_combination, $result);
        $result = str_replace('{reference_product_combination}', $aux_reference_combination, $result);
        $result = str_replace('{ean13_product_combination}', $aux_ean13_combination, $result);

        // Sino hay valor ponemos por defecto el id de combinación del producto
        if (empty($result)) {
            $result = $producto_combination->id;
        }

        return $result;
    }

    public function getDatosProducto($producto, $tipo_imagen)
    {
        $moneda = Context::getContext()->currency;
        $link = new Link(AdpmicrodatosTools::getHttp());
        $datos_producto = [];
        // BUG en ciertas versiones de prestashop 1.6, donde a veces no trae el texto sino un array
        if (is_array($producto->name)) {
            $datos_producto['nombre'] = $producto->name[$this->context->language->id];
        } else {
            $datos_producto['nombre'] = $producto->name;
        }

        $datos_producto['imagenes'] = $this->getImagesProduct($producto, $tipo_imagen);

        $fabricante = new Manufacturer((int) $producto->id_manufacturer, $this->context->language->id);
        $datos_producto['url'] = $link->getProductLink($producto, null, null, null, $this->context->language->id, $this->context->shop->id);
        $datos_producto['id_product'] = $producto->id;
        $datos_producto['sku'] = $producto->reference;
        $datos_producto['ean13'] = $producto->ean13;
        $datos_producto['upc'] = '0' . $producto->upc;
        $datos_producto['isbn'] = $producto->isbn;
        $datos_producto['mpn'] = $producto->mpn;
        $datos_producto['category'] = $producto->category;
        $datos_producto['condition'] = $producto->condition;
        // BUG en ciertas versiones de prestashop 1.6, donde a veces no trae el texto sino un array
        if (is_array($producto->description)) {
            if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                $datos_producto['description'] = $producto->description_short[$this->context->language->id];
            } else {
                $datos_producto['description'] = $producto->description[$this->context->language->id];
            }
        } else {
            if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                $datos_producto['description'] = $producto->description_short;
            } else {
                $datos_producto['description'] = $producto->description;
            }
        }
        // En caso de tener stock ilimitado, ponemos el minimo necesario
        $datos_producto['quantity'] = !Configuration::get('PS_ORDER_OUT_OF_STOCK') ? StockAvailable::getQuantityAvailableByProduct($producto->id, null, $this->context->shop->id) : '1';
        $datos_producto['productPrice'] = AdpmicrodatosTools::getProductPrice($producto->id, null);
        $datos_producto['moneda'] = $moneda->iso_code;
        $datos_producto['fabricante'] = $fabricante->name;
        $datos_producto['weight'] = $producto->weight;
        $datos_producto['inProductGroupWithID'] = 0;

        $fecha = date('Y-m-d');
        $nuevafecha = strtotime('+' . Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT') . ' days', strtotime($fecha));
        if (Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT')) {
            $nuevafecha = date('Y-m-d', $nuevafecha);
        } else {
            $nuevafecha = null;
        }

        $datos_producto['fechaValidaHasta'] = $nuevafecha;
        $datos_producto['permitir_pedido_fuera_stock'] = ($producto->out_of_stock == 1 || Configuration::get('PS_ORDER_OUT_OF_STOCK')) ? '1' : '0';

        return $datos_producto;
    }

    public function getDatosCombinacionProducto($producto, $id_product_combination, $tipo_imagen)
    {
        $moneda = Context::getContext()->currency;
        $link = new Link(AdpmicrodatosTools::getHttp());
        $combinacion_producto = new Combination($id_product_combination, $this->context->language->id, $this->context->shop->id);
        $datos_combinacion_producto = [];
        // BUG en ciertas versiones de prestashop 1.6, donde a veces no trae el texto sino un array
        if (is_array($producto->name)) {
            $datos_combinacion_producto['nombre'] = $producto->name[$this->context->language->id];
        } else {
            $datos_combinacion_producto['nombre'] = $producto->name;
        }

        $datos_combinacion_producto['imagenes'] = $this->getImagesCombinationProduct($producto, $id_product_combination, $tipo_imagen);

        $fabricante = new Manufacturer((int) $producto->id_manufacturer, $this->context->language->id);
        $datos_combinacion_producto['url'] = $link->getProductLink($producto, null, null, null, $this->context->language->id, $this->context->shop->id, $id_product_combination, false, false, true);
        $datos_combinacion_producto['id_product'] = $this->getIdProductCombination($producto, $combinacion_producto);
        $datos_combinacion_producto['ean13'] = !empty($combinacion_producto->ean13) ? $combinacion_producto->ean13 : $producto->ean13;
        $datos_combinacion_producto['upc'] = !empty($combinacion_producto->upc) ? '0' . $combinacion_producto->upc : '0' . $producto->upc;
        $datos_combinacion_producto['isbn'] = !empty($combinacion_producto->isbn) ? $combinacion_producto->isbn : $producto->isbn;
        $datos_combinacion_producto['category'] = $producto->category;
        $datos_combinacion_producto['sku'] = !empty($combinacion_producto->reference) ? $combinacion_producto->reference : $producto->reference;
        $datos_combinacion_producto['mpn'] = !empty($combinacion_producto->mpn) ? $combinacion_producto->mpn : $producto->mpn;
        $datos_combinacion_producto['condition'] = $producto->condition;

        // BUG en ciertas versiones de prestashop 1.6, donde a veces no trae el texto sino un array
        if (is_array($producto->description)) {
            if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                $datos_combinacion_producto['description'] = $producto->description_short[$this->context->language->id];
            } else {
                $datos_combinacion_producto['description'] = $producto->description[$this->context->language->id];
            }
        } else {
            if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                $datos_combinacion_producto['description'] = $producto->description_short;
            } else {
                $datos_combinacion_producto['description'] = $producto->description;
            }
        }
        // En caso de tener stock ilimitado, ponemos el minimo necesario
        $datos_combinacion_producto['quantity'] = !Configuration::get('PS_ORDER_OUT_OF_STOCK') ? StockAvailable::getQuantityAvailableByProduct($producto->id, $id_product_combination, $this->context->shop->id) : '1';
        $datos_combinacion_producto['productPrice'] = AdpmicrodatosTools::getProductPrice($producto->id, $id_product_combination);
        $datos_combinacion_producto['moneda'] = $moneda->iso_code;
        $datos_combinacion_producto['fabricante'] = $fabricante->name;
        $datos_combinacion_producto['weight'] = $producto->weight;
        $datos_combinacion_producto['inProductGroupWithID'] = $producto->id;

        $fecha = date('Y-m-d');
        $nuevafecha = strtotime('+' . Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT') . ' days', strtotime($fecha));
        if (Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT')) {
            $nuevafecha = date('Y-m-d', $nuevafecha);
        } else {
            $nuevafecha = null;
        }

        $datos_combinacion_producto['fechaValidaHasta'] = $nuevafecha;
        $datos_combinacion_producto['permitir_pedido_fuera_stock'] = ($producto->out_of_stock == 1 || Configuration::get('PS_ORDER_OUT_OF_STOCK')) ? '1' : '0';

        return $datos_combinacion_producto;
    }

    public function getDatosCombinaciones($producto, $tipo_imagen)
    {
        $sql = 'SELECT pa.`id_product`,pl.`name` as nombre,pa.`id_product_attribute`, pa.`price`, pa.`reference`,
                    sta.`quantity`,pa.`ean13`,pa.`upc`,pa.`isbn`,pa.`mpn`,p.`price` as precioProducto,ag.`id_attribute_group`,
                    agl.`name` as group_name, al.`name` as attribute_name, a.`id_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = pa.`id_product`
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' . (int) $this->context->language->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sta ON (sta.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $this->context->language->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $this->context->language->id . ')
                WHERE p.`id_product` = ' . (int) $producto->id . ' 
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute`';

        $moneda = Context::getContext()->currency;
        $combinaciones_productos = [];
        $link = new Link(AdpmicrodatosTools::getHttp());
        $resultados = Db::getInstance()->executeS($sql);

        if (!empty($resultados)) {
            $datos_producto = [];

            foreach ($resultados as $combination) {
                $datos_producto['imagenes'] = $this->getImagesCombinationProduct($producto, $combination['id_product_attribute'], $tipo_imagen);

                if ('' != $combination['nombre']) {
                    $datos_producto['nombre'] = $combination['nombre'];
                } else {
                    $datos_producto['nombre'] = $producto->name;
                }

                $datos_producto['url'] = $link->getProductLink($producto, null, null, null, $this->context->language->id, $this->context->shop->id, $combination['id_product_attribute'], false, false, true);
                $datos_producto['id_product'] = $this->getIdProductCombination($producto, $combination);
                $datos_producto['ean13'] = !empty($combination['ean13']) ? $combination['ean13'] : $producto->ean13;
                $datos_producto['upc'] = !empty($combination['upc']) ? '0' . $combination['upc'] : '0' . $producto->upc;
                $datos_producto['isbn'] = !empty($combination['isbn']) ? $combination['isbn'] : $producto->isbn;
                $datos_producto['condition'] = $producto->condition;

                if (is_array($producto->description)) {
                    if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                        $datos_producto['description'] = $producto->description_short[$this->context->language->id];
                    } else {
                        $datos_producto['description'] = $producto->description[$this->context->language->id];
                    }
                } else {
                    if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                        $datos_producto['description'] = $producto->description_short;
                    } else {
                        $datos_producto['description'] = $producto->description;
                    }
                }
                // En caso de tener stock ilimitado, ponemos el minimo necesario
                $datos_producto['quantity'] = !Configuration::get('PS_ORDER_OUT_OF_STOCK') ? StockAvailable::getQuantityAvailableByProduct($combination['id_product'], $combination['id_product_attribute'], $this->context->shop->id) : '1';

                $datos_producto['productPrice'] = AdpmicrodatosTools::getProductPrice($combination['id_product'], $combination['id_product_attribute']);
                $datos_producto['moneda'] = $moneda->iso_code;
                $datos_producto[$combination['id_product_attribute']][$combination['id_attribute_group']] = ['group_name' => $combination['group_name'], 'attribute_name' => $combination['attribute_name']];

                $fecha = date('Y-m-d');
                $nuevafecha = strtotime('+' . Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT') . ' days', strtotime($fecha));
                if (Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT')) {
                    $nuevafecha = date('Y-m-d', $nuevafecha);
                } else {
                    $nuevafecha = null;
                }
                $datos_producto['fechaValidaHasta'] = $nuevafecha;

                $datos_producto['sku'] = !empty($combination['reference']) ? $combination['reference'] : $producto->reference;
                $datos_producto['mpn'] = !empty($combination['mpn']) ? $combination['mpn'] : $producto->mpn;
                $datos_producto['weight'] = $producto->weight;

                $combinaciones_productos[$combination['id_product_attribute']] = $datos_producto;
            }
        }

        return $combinaciones_productos;
    }

    public function getCaracteristicas($id_product, $adp_ids_disable_microdata_features_product)
    {
        if (empty($adp_ids_disable_microdata_features_product)) {
            $adp_ids_disable_microdata_features_product = 0;
        }

        $features = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT fl.`name` as nombre,fvl.`value` as valor
                        FROM `' . _DB_PREFIX_ . 'feature_product` fp
                        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (fp.`id_product` = p.`id_product`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (fp.`id_feature` = fl.`id_feature`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fp.`id_feature_value` = fv.`id_feature_value`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fp.`id_feature_value` = fvl.`id_feature_value`)
                        WHERE p.`id_product` = ' . (int) $id_product . ' AND p.`id_shop_default`=' . Context::getContext()->shop->id . ' AND
                        fvl.`id_lang`=' . $this->context->language->id . ' AND fl.`id_lang`=' . $this->context->language->id . ' AND 
                        fp.`id_feature_value` not in (' . $adp_ids_disable_microdata_features_product . ')');

        $result = [];
        if (!empty($features)) {
            foreach ($features as $feature) {
                if (!empty($feature['valor'])) {
                    $result[$feature['valor']] = $feature['nombre'];
                }
            }
        }

        return $result;
    }

    public function getCaracteristicaById($id_product, $id_feature)
    {
        $result = [];

        if (empty($id_feature)) {
            return $result;
        }

        $features = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT fl.`name` as nombre,fvl.`value` as valor
                        FROM `' . _DB_PREFIX_ . 'feature_product` fp
                        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (fp.`id_product` = p.`id_product`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (fp.`id_feature` = fl.`id_feature`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fp.`id_feature_value` = fv.`id_feature_value`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fp.`id_feature_value` = fvl.`id_feature_value`)
                        WHERE p.`id_product` = ' . (int) $id_product . ' AND p.`id_shop_default`=' . Context::getContext()->shop->id . ' AND
                        fvl.`id_lang`=' . $this->context->language->id . ' AND fl.`id_lang`=' . $this->context->language->id . ' AND 
                        fp.`id_feature` = ' . $id_feature);

        if (!empty($features)) {
            foreach ($features as $feature) {
                if (!empty($feature['valor'])) {
                    $result[] = $feature['valor'];
                }
            }
        }

        return $result;
    }

    public function getListadosProductosFromCategory($id_category, $tipo_busqueda, $tipo_imagen)
    {
        $categoryProducts = [];

        $orderby = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_ORDEN_PAGINA);
        $tipo_orden = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_TIPO_ORDEN_PAGINA);

        switch ($tipo_busqueda) {
            case self::$PAGE_PRICESDROP:
                $pagina = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_NUMERO_PAGINA);
                $categoryProducts = Product::getPricesDrop($this->context->language->id, $pagina, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'), false, $orderby, $tipo_orden, false, false, $this->context);
                break;
            case self::$PAGE_BESTSALES:
                $pagina = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_NUMERO_PAGINA);
                $categoryProducts = ProductSale::getBestSalesLight($this->context->language->id, $pagina, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'), $this->context);
                break;
            case self::$PAGE_NEWPRODUCTS:
                $pagina = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_NUMERO_PAGINA);
                $categoryProducts = Product::getNewProducts($this->context->language->id, $pagina, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'), false, $orderby, $tipo_orden, $this->context);
                break;
            default:
                $pagina = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_NUMERO_PAGINA, '', 1);
                $category = new Category((int) $id_category, (int) $this->context->language->id, (int) $this->context->shop->id);
                if (Validate::isLoadedObject($category)) {
                    $categoryProducts = $category->getProducts($this->context->language->id, $pagina, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'), $orderby, $tipo_orden, false, true, false, 1, true, $this->context);
                }
                break;
        }

        $listados_productos = [];
        if (!empty($categoryProducts)) {
            $link = new Link(AdpmicrodatosTools::getHttp());
            foreach ($categoryProducts as $product) {
                $datos_producto = [];

                $auxProduct = new Product($product['id_product'], $this->context->language->id);

                $datos_producto['nombre'] = $auxProduct->name[$this->context->language->id];
                $datos_producto['imagenes'] = $this->getImagesProduct($auxProduct, $tipo_imagen);
                $datos_producto['url'] = $link->getProductLink($auxProduct, null, null, null, $this->context->language->id, $this->context->shop->id);

                $listados_productos[] = $datos_producto;
            }
        }

        return $listados_productos;
    }

    public function getListadosProductosFromManufacturer($id_manufacturer, $tipo_imagen)
    {
        $pagina = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_NUMERO_PAGINA, '', 1);
        $orderby = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_ORDEN_PAGINA);
        $tipo_orden = AdpmicrodatosTools::getDataByVersionPrestashop(AdpmicrodatosTools::ADPMICRODATOS_TIPO_ORDEN_PAGINA);

        $manufacturerProducts = Manufacturer::getProducts($id_manufacturer, $this->context->language->id, $pagina, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'), $orderby, $tipo_orden, false, true, true, $this->context);

        $listados_productos = [];
        if (!empty($manufacturerProducts)) {
            $link = new Link(AdpmicrodatosTools::getHttp());
            foreach ($manufacturerProducts as $product) {
                $datos_producto = [];

                $auxProduct = new Product($product['id_product'], $this->context->language->id);

                $datos_producto['nombre'] = $auxProduct->name[$this->context->language->id];
                $datos_producto['imagenes'] = $this->getImagesProduct($auxProduct, $tipo_imagen);
                $datos_producto['url'] = $link->getProductLink($auxProduct, null, null, null, $this->context->language->id, $this->context->shop->id);

                $listados_productos[] = $datos_producto;
            }
        }

        return $listados_productos;
    }

    public function getBreadCrumbsFromPage($page_name)
    {
        $result = [];
        $datos_categoria = [];
        $idiomas_activados = Language::getLanguages(true, Context::getContext()->shop->id);

        $link = new Link(AdpmicrodatosTools::getHttp());

        $categoria = new Category((int) Context::getContext()->shop->getCategory(), $this->context->language->id, $this->context->shop->id);
        $datos_categoria['url'] = AdpmicrodatosTools::getLinkRoot(count($idiomas_activados));
        $datos_categoria['name'] = $categoria->name;
        $result[] = $datos_categoria;

        $meta = Meta::getMetaByPage($page_name, $this->context->language->id);
        $datos_categoria['url'] = $link->getPageLink($page_name, null, $this->context->language->id);
        $datos_categoria['name'] = $meta['title'];
        $result[] = $datos_categoria;

        return $result;
    }

    public function getProductosRelacionados($producto, $num_products_related, $tipo_imagen, $mpn_reference_same_value)
    {
        $moneda = Context::getContext()->currency;
        $category = new Category((int) $producto->id_category_default, $this->context->language->id, $this->context->shop->id);
        $categoryProducts = $category->getProducts($this->context->language->id, 1, $num_products_related, null, null, false, true, false, 1, true, $this->context);
        $productos_relacionados = [];
        if (!empty($categoryProducts)) {
            $link = new Link(AdpmicrodatosTools::getHttp());
            foreach ($categoryProducts as $product) {
                if ($product['id_product'] == $producto->id) {
                    continue;
                }

                $auxProduct = new Product($product['id_product'], $this->context->language->id);

                $datos_producto = [];
                $datos_producto['nombre'] = $auxProduct->name[$this->context->language->id];

                $fabricante = new Manufacturer((int) $auxProduct->id_manufacturer, $this->context->language->id);

                $datos_producto['imagenes'] = $this->getImagesProduct($auxProduct, $tipo_imagen);
                $datos_producto['url'] = $link->getProductLink($auxProduct, null, null, null, $this->context->language->id, $this->context->shop->id);
                $datos_producto['id_product'] = $this->getIdProduct($auxProduct);
                $datos_producto['ean13'] = $auxProduct->ean13;
                $datos_producto['upc'] = '0' . $auxProduct->upc;
                $datos_producto['isbn'] = $auxProduct->isbn;
                $datos_producto['category'] = $auxProduct->category;
                $datos_producto['sku'] = $auxProduct->reference;
                $datos_producto['mpn'] = $mpn_reference_same_value ? $auxProduct->reference : $auxProduct->mpn;
                $datos_producto['fabricante'] = $fabricante->name;
                $datos_producto['condition'] = $auxProduct->condition;

                // BUG en ciertas versiones de prestashop 1.6, donde a veces no trae el texto sino un array
                if (is_array($auxProduct->description)) {
                    if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                        $datos_producto['description'] = $auxProduct->description_short[$this->context->language->id];
                    } else {
                        $datos_producto['description'] = $auxProduct->description[$this->context->language->id];
                    }
                } else {
                    if (Configuration::get('ADP_SET_MICRODATA_DESCRIPTION_PRODUCT_PAGE')) {
                        $datos_producto['description'] = $auxProduct->description_short;
                    } else {
                        $datos_producto['description'] = $auxProduct->description;
                    }
                }

                // En caso de tener stock ilimitado, ponemos el minimo necesario
                $datos_producto['quantity'] = !Configuration::get('PS_ORDER_OUT_OF_STOCK') ? $auxProduct->quantity : '1';
                $datos_producto['productPrice'] = AdpmicrodatosTools::getProductPrice((int) $auxProduct->id);

                $datos_producto['moneda'] = $moneda->iso_code;

                $fecha = date('Y-m-d');
                $nuevafecha = strtotime('+' . Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT') . ' days', strtotime($fecha));
                if (Configuration::get('ADP_NUM_DAYS_DATE_VALID_UNTIL_PRODUCT')) {
                    $nuevafecha = date('Y-m-d', $nuevafecha);
                } else {
                    $nuevafecha = null;
                }
                $datos_producto['fechaValidaHasta'] = $nuevafecha;

                $productos_relacionados[] = $datos_producto;
            }
        }

        return $productos_relacionados;
    }

    public function renderName($name)
    {
        return Tools::strtoupper('pts_relate_pro_' . $name);
    }

    public function getConfigValue($key, $value = null)
    {
        return Configuration::hasKey($this->renderName($key)) ? Configuration::get($this->renderName($key)) : $value;
    }

    public function getImagesCombinationProduct($product, $id_product_combination, $tipo_imagen)
    {
        $imagenes_combinacion_producto = [];
        $imagenes = AdpmicrodatosTools::getImagesCombination($id_product_combination);

        if (!$imagenes) {
            return $this->getImagesProduct($product, $tipo_imagen);
        }

        $link = new Link(AdpmicrodatosTools::getHttp());

        foreach ($imagenes as $imagen) {
            $imageId = $product->id . '-' . $imagen['id_image'];
            if (is_array($product->link_rewrite)) {
                $aux_imagen = $link->getImageLink($product->link_rewrite[$this->context->language->id], $imageId, $tipo_imagen);
            } else {
                $aux_imagen = $link->getImageLink($product->link_rewrite, $imageId, $tipo_imagen);
            }

            if (false === strpos($aux_imagen, AdpmicrodatosTools::getHttp())) {
                $aux_imagen = AdpmicrodatosTools::getHttp() . $aux_imagen;
            }
            $imagenes_combinacion_producto[] = $aux_imagen;
        }

        return $imagenes_combinacion_producto;
    }

    public function getImagesProduct($product, $tipo_imagen)
    {
        $imagen_portada = $this->getImageCoverProduct($product, $tipo_imagen);

        $imagenes_producto = [];

        if (Configuration::get('ADP_SET_IMAGE_PRODUCT') && !empty($imagen_portada)) {
            $imagenes_producto[] = $imagen_portada;

            return $imagenes_producto;
        } else {
            $link = new Link(AdpmicrodatosTools::getHttp());

            $imagenes = $product->getImages($this->context->language->id);

            if (!empty($imagenes)) {
                foreach ($imagenes as $imagen) {
                    $imageId = $product->id . '-' . $imagen['id_image'];
                    if (is_array($product->link_rewrite)) {
                        $aux_imagen = $link->getImageLink($product->link_rewrite[$this->context->language->id], $imageId, $tipo_imagen);
                    } else {
                        $aux_imagen = $link->getImageLink($product->link_rewrite, $imageId, $tipo_imagen);
                    }

                    if (false === strpos($aux_imagen, AdpmicrodatosTools::getHttp())) {
                        $aux_imagen = AdpmicrodatosTools::getHttp() . $aux_imagen;
                    }
                    $imagenes_producto[] = $aux_imagen;
                }
            }
        }

        if (Configuration::get('ADP_SET_IMAGE_PRODUCT') && !empty($imagenes_producto)) {
            return $imagenes_producto[0];
        }

        return $imagenes_producto;
    }

    public function getImageCoverProduct($product, $tipo_imagen)
    {
        $images = Product::getCover($product->id);
        $aux_imagen = '';
        $link = new Link(AdpmicrodatosTools::getHttp());
        if (!empty($images)) {
            if (is_array($product->link_rewrite)) {
                $aux_imagen = $link->getImageLink($product->link_rewrite[$this->context->language->id], $images['id_image'], $tipo_imagen);
            } else {
                $aux_imagen = $link->getImageLink($product->link_rewrite, $images['id_image'], $tipo_imagen);
            }

            if (false === strpos($aux_imagen, AdpmicrodatosTools::getHttp())) {
                $aux_imagen = AdpmicrodatosTools::getHttp() . $aux_imagen;
            }
        }

        return $aux_imagen;
    }
}
