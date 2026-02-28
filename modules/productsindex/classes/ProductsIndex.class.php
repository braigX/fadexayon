<?php
/**
 * 2007 - 2018 ZLabSolutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future. If you wish to customize module for your
 * needs please contact developer at http://zlabsolutions.com for more information.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLab Solutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions https://www.facebook.com/ZLabSolutions/
 */

require_once _PS_MODULE_DIR_ . '../config/config.inc.php';
require_once _PS_MODULE_DIR_ . '../init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_ . 'productsindex/productsindex.php';

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

class ProductsIndexClass
{
    public $config;

    /* front */
    /*
        public function getManufacturersPositions($products)
        {
            return true;
        }
    */
    /* apply */
    public function applyIndexAjax()
    {
        $new_index = Tools::getValue('new_index');
        $this->config = $config_products = Tools::getValue('products_config');
        $new_index = json_decode($new_index);
        $result = $this->applyIndex($config_products, $new_index);
        if ($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function applyIndex($config_products, $new_index = null)
    {
        $index_type = (int) $config_products['index_type'];
        if (($index_type == 1) && isset($config_products['id_category'])) {
            $result = $this->applyIndexCategory((int) $config_products['id_category'], $new_index);
        } elseif (($index_type == 2) && isset($config_products['id_manufacturer'])) {
            $result = $this->applyIndexManufacturer((int) $config_products['id_manufacturer'], $new_index);
        }

        return $result;
    }

    public function applyIndexManufacturer($id_manufacturer, $new_index = null)
    {
        $result = true;
        if (is_array($new_index) && count($new_index)) {
            if (self::truncateManufacturerPositions($id_manufacturer)) {
                foreach ($new_index as $position => $id_product) {
                    $result &= self::addManufacturerProductAssociation($id_product, $id_manufacturer, $position);
                }
            }
        } else {
            return false;
        }

        return $result;
    }

    public function applyIndexCategory($id_category, $new_index)
    {
        $result = true;
        if (is_array($new_index) && count($new_index)) {
            if (self::truncateCategoryPositions($id_category)) {
                foreach ($new_index as $position => $id_product) {
                    $result &= self::addCategoryProductAssociation($id_product, $id_category, $position);
                }
            }
        } else {
            return false;
        }

        return $result;
    }

    public static function truncateManufacturerPositions($id_manufacturer)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'zlcpi_manufacturer_product`
                WHERE id_manufacturer=' . (int) $id_manufacturer;
        Db::getInstance()->execute($sql);

        return true;
    }

    public static function addNewProductToCategoriesList($id_product)
    {
        $product = new Product($id_product);
        $id_manufacturer = $product->id_manufacturer;
        if ($id_manufacturer) {
            $sql = 'SELECT * 
                    FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                    WHERE id_manufacturer = ' . (int) $id_manufacturer . '
                     AND id_product = ' . (int) $id_product;
            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                return false;
            } else {
                $sql = 'SELECT * 
                        FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                        WHERE id_product = ' . (int) $id_product;
                $result2 = Db::getInstance()->executeS($sql);
                if ($result2) {
                    self::deleteProductFromManufacturerList($id_product);
                }

                return self::addManufacturerProductAssociation($id_product, $id_manufacturer, 0);
            }
        } else {
            return false;
        }

        return true;
    }

    public static function addNewProductToManufacturerList($id_product)
    {
        $product = new Product($id_product);

        if ($product->active == false) {
            return false;
        }
        $id_manufacturer = $product->id_manufacturer;
        if ($id_manufacturer) {
            $sql = 'SELECT * 
                    FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                    WHERE id_manufacturer = ' . (int) $id_manufacturer . '
                     AND id_product = ' . (int) $id_product;
            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                return false;
            } else {
                $sql = 'SELECT * 
                        FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                         WHERE id_product = ' . (int) $id_product;
                $result2 = Db::getInstance()->executeS($sql);
                if ($result2) {
                    self::deleteProductFromManufacturerList($id_product);
                }

                return self::addManufacturerProductAssociation($id_product, $id_manufacturer, 0);
            }
        } else {
            return false;
        }

        return true;
    }

    public static function deleteProductFromManufacturerList($id_product)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'zlcpi_manufacturer_product`
                WHERE id_product=' . (int) $id_product;

        return Db::getInstance()->execute($sql);
    }

    public static function addManufacturerProductAssociation($id_product, $id_manufacturer, $product_position)
    {
        if ($product_position == 0) {
            $max_position = 1;
        }

        $sql = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'zlcpi_manufacturer_product`
                SET id_manufacturer=' . (int) $id_manufacturer . ',
                    id_product=' . (int) $id_product . ',
                    position=' . (int) $product_position;

        return Db::getInstance()->execute($sql);
    }

    public static function truncateCategoryPositions($id_category)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE id_category=' . (int) $id_category;

        return Db::getInstance()->execute($sql);
    }

    public static function addCategoryProductAssociation($id_product, $id_category, $product_position)
    {
        $sql = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'category_product`
                SET id_category=' . (int) $id_category . ',
                    id_product=' . (int) $id_product . ',
                    position=' . (int) $product_position;

        return Db::getInstance()->execute($sql);
    }

    public static function moveCategoriesProductOOS()
    {
        $categories = self::getCategories();
        foreach ($categories as $category) {
            $id_category = (int) $category['id_category'];
            self::moveCategoryProductOOS($id_category);
        }

        return true;
    }

    public static function moveCategoryProductOOS($id_category)
    {
        // echo $id_category.'<br>';
        $id_shop = Context::getContext()->shop->id;
        $sql_oos = 'SELECT cp.* FROM `' . _DB_PREFIX_ . 'category_product` cp
                    INNER JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = cp.id_product
                        AND sa.id_product_attribute = 0 AND sa.id_shop = ' . (int) $id_shop . '
                    WHERE cp.id_category = ' . (int) $id_category . '
                        AND sa.quantity < 1';
        $list_oos = Db::getInstance()->executeS($sql_oos);

        if (count($list_oos) > 0) {
            $sql_noos = 'SELECT cp.id_category, cp.id_product FROM `' . _DB_PREFIX_ . 'category_product` cp
                    INNER JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = cp.id_product
                        AND sa.id_product_attribute = 0 AND sa.id_shop = ' . (int) $id_shop . '
                    WHERE cp.id_category = ' . (int) $id_category . '
                        AND sa.quantity > 0 
                    ORDER BY cp.position ASC';
            $list_noos = Db::getInstance()->executeS($sql_noos);
            self::truncateCategoryPositions($id_category);
            $i = 0;
            if ($list_noos) {
                foreach ($list_noos as $cat) {
                    self::addCategoryProductAssociation(
                        $cat['id_product'],
                        $cat['id_category'],
                        $i
                    );
                    ++$i;
                }
            }
            if ($list_oos) {
                foreach ($list_oos as $ncat) {
                    self::addCategoryProductAssociation(
                        $ncat['id_product'],
                        $ncat['id_category'],
                        $i
                    );
                    ++$i;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /* getters */
    public function getConfigProductsAjax()
    {
        $config_products = Tools::getValue('products_config');
        $index_type = $config_products['index_type'];
        if (isset($config_products['id_category'])) {
            $target_category_id = $config_products['id_category'];
        } else {
            $target_category_id = 0;
        }
        $target_brand_id = $config_products['id_manufacturer'];
        $sort_by = $config_products['sort_by'];
        $sort_way = $config_products['sort_way'];
        $limit = $config_products['limit'];
        $limit = 0;

        $products = $this->getConfigProducts(
            $index_type,
            $target_category_id,
            $target_brand_id,
            $sort_by,
            $sort_way,
            $limit
        );

        echo json_encode($products);
    }

    public static function getCategories()
    {
        $sql = 'SELECT DISTINCT id_category FROM `' . _DB_PREFIX_ . 'category_product`';
        $list = Db::getInstance()->executeS($sql);

        return $list;
    }

    public function getConfigProducts(
        $index_type,
        $target_category_id,
        $target_brand_id,
        $sort_by,
        $sort_way,
        $limit = 0
    ) {
        $products = [];
        $list = [];
        $link_obj = new Link();
        if ($index_type == 1) {
            $res = $this->getCategoryProducts($target_category_id, $sort_by, $sort_way, $limit);
            $link = $link_obj->getCategoryLink(new Category($target_category_id));
        } elseif ($index_type == 2) {
            $res = $this->getBrandProducts($target_brand_id, $sort_by, $sort_way, $limit);
            $link = $link_obj->getManufacturerLink($target_brand_id);
        }
        if ($res) {
            $image_type = Zlabcustomclasszl::getConfigValueByOption('IMAGE_TYPE');
            foreach ($res as $r) {
                $reference = $r['reference'];

                if (!in_array($r['id_product'], $list)) {
                    $list[] = $r['id_product'];
                    $products[] = [
                        'id_product' => $r['id_product'],
                        'position' => $r['position'],
                        'reference' => $reference,
                        'name' => $r['name'],
                        'category_name' => $r['category_name'],
                        'price' => $r['price'],
                        'image' => self::getImagePath($r['id_image'], $image_type),
                        'active' => $r['active'],
                        'quantity' => $r['quantity'],
                        'brand_name' => $r['brand_name'],
                        'supplier_name' => $r['supplier_name'],
                        'imported' => 1,
                    ];
                } else {
                    continue;
                }
            }
        }

        return ['products' => $products, 'link' => $link];
    }

    public static function getOrder($sort_by_value)
    {
        switch ($sort_by_value) {
            case '0':
                $order = 'cp.position';
                break;
            case '1':
                $order = 'sa.quantity';
                break;
            case '2':
                $order = 'pl.name';
                break;
            case '3':
                $order = 'cp.position';
                break;
            case '4':
                $order = 'ps.price';
                break;
            case '5':
                $order = 'ps.date_upd';
                break;
            case '6':
                $order = 'p.id_product';
                break;
            default:
                $order = 'cp.position';
        }

        return $order;
    }

    /*
    public static function sortManufacturerPositions2($id_manufacturer, $products, $page, $nbrPerPage)
    {
        $sorted = self::getManufacturerPositionsFront($id_manufacturer, $page, $nbrPerPage);
        if (!$sorted) {
            return $products;
        }
        //file_put_contents(_PS_MODULE_DIR_.'/productsindex/temp1.txt', print_r($sorted, true));
        $id_lang = Context::getContext()->language->id;
        //$manufacturer = new Manufacturer();
        $ids = array();
        foreach ($sorted as $s) {
            $ids[] = $s['id_product'];
        }
        $products = self::getManufacturerProductsByIds171($id_manufacturer, $id_lang, implode(',', $ids));

    }
    */
    public static function sortManufacturerPositions($id_manufacturer, $products, $page, $nbrPerPage)
    {
        $sorted = self::getManufacturerPositionsFront($id_manufacturer, $page, $nbrPerPage);
        if (!$sorted) {
            return $products;
        }
        // file_put_contents(_PS_MODULE_DIR_.'/productsindex/temp1.txt', print_r($sorted, true));
        $id_lang = Context::getContext()->language->id;
        // $manufacturer = new Manufacturer();
        $ids = [];
        foreach ($sorted as $s) {
            $ids[] = $s['id_product'];
        }
        if (_PS_VERSION_ < '1.7.0.0') {
            $products = self::getManufacturerProductsByIds($id_manufacturer, $id_lang, implode(',', $ids));
        } else {
            $products = self::getManufacturerProductsByIds171(implode(',', $ids));
        }
        $sorted_products = [];
        foreach ($sorted as $product) {
            $id_product = $product['id_product'];
            $product_info = self::getProductInfo($products, $id_product);
            if ($product_info) {
                $sorted_products[] = $product_info;
            } else {
                continue;
            }
        }
        /*
        //if enable show only one product in 1.7.7.0+
        $front = new FrontController();
        $front->addColorsToProductList($sorted_products);
        */
        // return $products;
        return $sorted_products;
    }

    public static function getManufacturerPositionsFront($id_manufacturer, $page, $nbrPerPage)
    {
        $page = $page - 1;

        if (($page == 0) && $nbrPerPage) {
            $limit = 'LIMIT 0, ' . (int) $nbrPerPage;
        } elseif ($page && $nbrPerPage) {
            $first = $page * $nbrPerPage;
            $limit = 'LIMIT ' . (int) $first . ', ' . (int) $nbrPerPage;
        } else {
            $limit = '';
        }
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT pi.*
                FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product pi
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product=pi.id_product
                    AND ps.active=1 AND ps.id_shop=' . (int) $id_shop . '
                WHERE pi.id_manufacturer = ' . (int) $id_manufacturer . '
                ORDER BY pi.position ASC '
                . $limit;

        $sql = 'SELECT ps.id_manufacturer, ps.id_product, IFNULL(pi.position, 10000) as position
                FROM ' . _DB_PREFIX_ . 'product ps
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop p ON ps.id_product=p.id_product AND p.active=1 AND p.id_shop=' . (int) $id_shop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product pi ON ps.id_product=pi.id_product
                    AND pi.id_manufacturer = ' . (int) $id_manufacturer . '
                WHERE ps.id_manufacturer = ' . (int) $id_manufacturer . '
                ORDER BY position ASC '
                . $limit;

        $result = Db::getInstance()->executeS($sql);

        if (Tools::getValue('debug') == 1) {
            foreach ($result as $prod) {
                echo $prod['id_product'] . ' - ' . $prod['position'] . " limit $limit|" . '<br>';
            }
            echo 'end sorted front-----------------------------<br>';
        }

        return $result;
    }

    public static function getManufacturerPositions($id_manufacturer)
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product pi
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product=pi.id_product
                    AND ps.active=1 AND ps.id_shop=' . (int) $id_shop . '
                WHERE pi.id_manufacturer = ' . (int) $id_manufacturer . '
                ORDER BY pi.position ASC';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public function updateBrandProductPosition($id_manufacturer, $id_product, $position)
    {
        $result = false;
        $is_exists = 'SELECT * 
                        FROM ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product pi 
                        WHERE id_manufacturer=' . (int) $id_manufacturer . ' AND  id_product=' . (int) $id_product;
        if (Db::getInstance()->executeS($is_exists)) {
            // update
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                    SET position = ' . (int) $position . '
                    WHERE id_manufacturer=' . (int) $id_manufacturer . ' AND  id_product=' . (int) $id_product;
            $result = Db::getInstance()->execute($sql);
        } else {
            // insert
            $sql = 'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product
                    SET position = ' . (int) $position . ',
                        id_manufacturer=' . (int) $id_manufacturer . ',
                        id_product=' . (int) $id_product;
            $result = Db::getInstance()->execute($sql);
        }

        return $result;
    }

    public function getBrandProducts($id_manufacturer, $sort_by, $sort_way, $limit)
    {
        $order = self::getOrder($sort_by);
        $order_way = 'ASC';
        if ($sort_way == '1') {
            $order_way = 'ASC';
        } elseif ($sort_way == '2') {
            $order_way = 'DESC';
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT DISTINCT p.id_product, ps.active, p.reference, ps.price, sa.quantity, pl.name,
                    cl.name as category_name, m.name as brand_name, s.name as supplier_name, ps.date_upd,
                    cp.position, i.id_image
                FROM ' . _DB_PREFIX_ . 'product p' .
                ' LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = p.id_product
                    AND sa.id_product_attribute = 0 AND sa.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON m.id_manufacturer = p.id_manufacturer
                  LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer_shop ms ON ms.id_manufacturer = p.id_manufacturer
                    AND ms.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier = p.id_supplier

                  LEFT JOIN ' . _DB_PREFIX_ . 'supplier_shop ss ON ss.id_supplier = p.id_supplier
                    AND ss.id_shop=' . (int) $id_shop . '
                  INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product
                    AND ps.visibility <> \'none\' AND ps.id_shop=' . (int) $id_shop . '

                  LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=ps.id_product AND i.cover = 1
                  LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product 
                    AND pl.id_lang=' . (int) $id_lang . ' 
                    AND pl.id_shop=' . (int) $id_shop .
                ' LEFT JOIN ' . _DB_PREFIX_ . 'zlcpi_manufacturer_product cp ON cp.id_product=p.id_product
                                                AND cp.id_manufacturer=' . (int) $id_manufacturer . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.id_category = p.id_category_default 
                    AND cl.id_lang=' . (int) $id_lang . ' 
                    AND cl.id_shop=' . (int) $id_shop .
                ' WHERE p.id_manufacturer = ' . (int) $id_manufacturer . ' 
                    AND ps.active=1
                  ORDER BY ' . $order . ' ' . $order_way .
                ($limit ? ' LIMIT 0, ' . (int) $limit : '');
        // echo $sql;
        $res = Db::getInstance()->executeS($sql);
        if ($sort_by == '3') {
            // $res = self::findManufacturerBestSales($res, $id_manufacturer);
            // $res = self::findCategoryBestSales($res, $id_category);
        }

        return $res;
    }

    public function getCategoryProducts($id_category, $sort_by, $sort_way, $limit)
    {
        $order = self::getOrder($sort_by);
        $order_way = 'ASC';
        if ($sort_way == '1') {
            $order_way = 'ASC';
        } elseif ($sort_way == '2') {
            $order_way = 'DESC';
        }
        // echo '123123123';
        $sort_active = (int) Zlabcustomclasszl::getConfigValueByOption('MOVE_DISABLED');
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT DISTINCT p.id_product, ps.active, p.reference, ps.price, sa.quantity,
                    pl.name, cl.name as category_name,
                    m.name as brand_name, s.name as supplier_name, ps.date_upd, cp.position, i.id_image
                FROM ' . _DB_PREFIX_ . 'product p' .
                ' LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = p.id_product
                    AND sa.id_product_attribute = 0 AND sa.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON m.id_manufacturer = p.id_manufacturer
                  LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer_shop ms ON ms.id_manufacturer = p.id_manufacturer
                    AND ms.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier = p.id_supplier
                  LEFT JOIN ' . _DB_PREFIX_ . 'supplier_shop ss ON ss.id_supplier = p.id_supplier
                    AND ss.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product
                    AND ps.visibility <> \'none\' AND ps.id_shop=' . (int) $id_shop . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=ps.id_product AND i.cover = 1
                  LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product 
                    AND pl.id_lang=' . (int) $id_lang . ' 
                    AND pl.id_shop=' . (int) $id_shop .
                ' INNER JOIN ' . _DB_PREFIX_ . 'category_product cp ON cp.id_product=p.id_product
                                                AND cp.id_category=' . (int) $id_category . '
                  LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.id_category = p.id_category_default 
                    AND cl.id_lang=' . (int) $id_lang . ' 
                    AND cl.id_shop=' . (int) $id_shop .
                ' ORDER BY ' . ($sort_active ? ' active DESC, ' : '') .
                            $order . ' ' . $order_way .
                ($limit ? ' LIMIT 0, ' . (int) $limit : '');

        $res = Db::getInstance()->executeS($sql);
        if ($sort_by == '3') {
            $res = self::findCategoryBestSales($res, $id_category);
        }

        return $res;
    }

    public static function findCategoryBestSales($products, $id_category)
    {
        $id_lang = Context::getContext()->language->id;
        $best_sales = self::getBestSalesLightCategory($id_lang, $id_category, 0, 10000);
        // print_r($best_sales);
        if (!$best_sales) {
            return $products;
        }
        $sorted_products = [];
        foreach ($best_sales as $product) {
            $id_product = $product['id_product'];
            $sorted_products[] = self::getProductInfo($products, $id_product);
        }

        return $sorted_products;
    }

    public static function getProductInfo($products, $id_product)
    {
        $product = false;
        // $id_lang = Context::getContext()->language->id;
        foreach ($products as $row) {
            if ($row['id_product'] == $id_product) {
                if (_PS_VERSION_ > '1.7.0.0') {
                    // $row['images'] = Image::getImages($id_lang, $id_product);
                }
                $product = $row;
                break;
            }
        }

        return $product;
    }

    public static function getProductInfoPlus($products, $id_product)
    {
        $product = false;
        $id_lang = Context::getContext()->language->id;
        foreach ($products as $row) {
            if ($row['id_product'] == $id_product) {
                if (_PS_VERSION_ > '1.7.0.0') {
                    $row['images'] = Image::getImages($id_lang, $id_product);
                    $row['cover'] = $row['images'][0];
                }
                $product = $row;
                break;
            }
        }

        return $product;
    }

    /**
     * Get required informations on best sales products
     * From ProductSale
     *
     * @param int $idLang Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     *
     * @return array keys : id_product, link_rewrite, name, id_image, legend, sales, ean13, upc, link
     */
    public static function getBestSalesLightCategory(
        $idLang,
        $id_category,
        $pageNumber = 0,
        $nbProducts = 1000
    ) {
        if (!$id_category) {
            return false;
        }
        $context = Context::getContext();
        if ($pageNumber < 0) {
            $pageNumber = 0;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }

        // no group by needed : there's only one attribute with default_on=1 for a given id_product + shop
        // same for image with cover=1
        $sql = '
        SELECT
            p.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, pl.`link_rewrite`,
            pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
            image_shop.`id_image` id_image, il.`legend`,
            ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category,
            p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
            IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
            product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' .
                (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int) Configuration::get(
                    'PS_NB_DAYS_NEW_PRODUCT'
                ) : 20) . ' DAY')) . '" as new,
            product_shop.`on_sale`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity
        FROM `' . _DB_PREFIX_ . 'product_sale` ps
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
        ' . Shop::addSqlAssociation('product', 'p') . '
        INNER JOIN ' . _DB_PREFIX_ . 'category_product cpp ON cpp.id_product = ps.id_product
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` 
                AND product_attribute_shop.`default_on` = 1 
                AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa 
            ON (product_attribute_shop.id_product_attribute=pa.id_product_attribute)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
            ON p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 
                AND image_shop.id_shop=' . (int) $context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` 
            AND il.`id_lang` = ' . (int) $idLang . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
            ON cl.`id_category` = product_shop.`id_category_default`
            AND cl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . Product::sqlStock('p', 0);

        $sql .= '
        WHERE product_shop.`active` = 1
        AND p.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category
                    AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1') . ')
                WHERE cp.`id_product` = p.`id_product`)';
        }

        $sql .= '
        ORDER BY ps.quantity DESC
        LIMIT ' . (int) ($pageNumber * $nbProducts) . ', ' . (int) $nbProducts;

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public static function getImagePath($id_image, $image_type)
    {
        $base_uri = __PS_BASE_URI__ == '/' ? '' : Tools::substr(__PS_BASE_URI__, 0, Tools::strlen(__PS_BASE_URI__) - 1);
        $image = str_split((string) $id_image);
        if (_PS_VERSION_ >= '1.7.0.0') {
            $path = $base_uri . '/img/p/' . implode('/', $image) . '/' . $id_image . '-' . $image_type . '.jpg';
        } else {
            $path = $base_uri . '/img/p/' . implode('/', $image) . '/' . $id_image . '-' . $image_type . '.jpg';
        }

        return $path;
    }

    public static function getManufacturerProductsByIds(
        $id_manufacturer,
        $id_lang,
        $ids,
        $order_by = null,
        $order_way = null,
        $get_total = false,
        $active = true,
        $active_category = true
    ) {
        $context = Context::getContext();

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'name';
        }

        if (empty($order_way)) {
            $order_way = 'ASC';
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            exit(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sql_groups = count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1';

        /* Return only the number of products */
        if ($get_total) {
            $sql = '
                SELECT p.`id_product`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE p.id_manufacturer = ' . (int) $id_manufacturer
                . ($active ? ' AND product_shop.`active` = 1' : '') . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                AND EXISTS (
                    SELECT 1
                    FROM `' . _DB_PREFIX_ . 'category_group` cg
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)' .
                    ($active_category ?
                        ' INNER JOIN `' . _DB_PREFIX_ . 'category` ca 
                            ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '') . '
                    WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sql_groups . '
                )';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            return (int) count($result);
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif ($order_by == 'name') {
            $alias = 'pl.';
        } elseif ($order_by == 'manufacturer_name') {
            $order_by = 'name';
            $alias = 'm.';
        } elseif ($order_by == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS
                    product_attribute_minimal_quantity,
                    IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '') . '
            , pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image,
            il.`legend`, m.`name` AS manufacturer_name,
                DATEDIFF(
                    product_shop.`date_add`,
                    DATE_SUB(
                        "' . date('Y-m-d') . ' 00:00:00",
                        INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                    )
                ) > 0 AS new'
            . ' FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') .
            (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` 
                product_attribute_shop
                        ON (p.`id_product` = product_attribute_shop.`id_product`
                            AND product_attribute_shop.`default_on` = 1
                            AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1
                        AND image_shop.id_shop=' . (int) $context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                ON (m.`id_manufacturer` = p.`id_manufacturer`)
            ' . Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $active_category) {
            $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.`id_category` = cg.`id_category`
                    AND cg.`id_group` ' . $sql_groups . ')';
            }
            if ($active_category) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
                WHERE p.`id_manufacturer` = ' . (int) $id_manufacturer . '
                ' . ($active ? ' AND product_shop.`active` = 1' : '') . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    AND product_shop.id_product IN(' . pSQL($ids) . ')
                GROUP BY p.id_product
                ORDER BY ' . $alias . '`' . bqSQL($order_by) . '` ' . pSQL($order_way);

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $full_products = Product::getProductsProperties($id_lang, $result);

        return $full_products;
    }

    /*
    private function prepareProductForTemplate(array $rawProduct) {
        $product = (new ProductAssembler($this->context))->assembleProduct($rawProduct);

        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();
        $productOut = $presenter->present($settings, $product, $this->context->language);
        // Force delete this value from the ProductListingLazyArray > ProductLazyArray. Cya!
        $productOut->offsetUnset('main_variants', true);
        // Regenerate (performance hit).
        $mainVs = $productOut->getMainVariants();
        // This time we can play around with the 'main_variants' var however we like
        foreach ($mainVs as &$varient) {
            $imgs = Image::getImages(
                $this->context->language->id,
                $varient['id_product'],
                $varient['id_product_attribute']
            );
            $varient['image_url'] = '';
            if (count($imgs) > 0) {
                $varient['image_url'] = $this->context->link->getImageLink(
                    $productOut['link_rewrite'],
                    $product['id_product'] .'-' . $imgs[0]['id_image'],
                    'home_default'
                );
            }
        }
        // Put it back (using their method, because why not).
        $productOut->__set('main_variants', $mainVs);
        return $productOut;
    }
    */
    public static function getManufacturerProductsByIds171($ids)
    {
        $context = Context::getContext();
        /*
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }
        */
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'product` WHERE id_product IN(' . $ids . ')';
        $products = Db::getInstance()->executeS($sql);
        $assembler = new ProductAssembler($context);
        $presenterFactory = new ProductPresenterFactory($context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $context->link
            ),
            $context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $context->getTranslator()
        );
        $products_for_template = [];
        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $context->language
            );
        }
        // print_r($products_for_template);
        return $products_for_template;
    }

    public static function getManufacturerProductsByIds17(
        $idManufacturer,
        $idLang,
        $ids,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $activeCategory = true
    ) {
        $context = Context::getContext();
        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if (empty($orderBy) || $orderBy == 'position') {
            $orderBy = 'name';
        }

        if (empty($orderWay)) {
            $orderWay = 'ASC';
        }

        if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay)) {
            exit(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sqlGroups = count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1';

        /* Return only the number of products */
        if ($getTotal) {
            $sql = '
                SELECT p.`id_product`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE p.id_manufacturer = ' . (int) $idManufacturer
                . ($active ? ' AND product_shop.`active` = 1' : '') . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                AND EXISTS (
                    SELECT 1
                    FROM `' . _DB_PREFIX_ . 'category_group` cg
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)' .
                    ($activeCategory ? ' INNER JOIN `' . _DB_PREFIX_ . 'category` ca
                        ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '') . '
                    WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sqlGroups . '
                )';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            return (int) count($result);
        }
        if (strpos($orderBy, '.') > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]) . '.`' . pSQL($orderBy[1]) . '`';
        }

        if ($orderBy == 'price') {
            $alias = 'product_shop.';
        } elseif ($orderBy == 'name') {
            $alias = 'pl.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        } elseif ($orderBy == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            . (
                Combination::isFeatureActive() ?
                ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, 
                IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' :
                ''
            ) . '
            , pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image,
            il.`legend`, m.`name` AS manufacturer_name,
                DATEDIFF(
                    product_shop.`date_add`,
                    DATE_SUB(
                        "' . date('Y-m-d') . ' 00:00:00",
                        INTERVAL ' . (
                Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') :
                20
            ) . ' DAY
                    )
                ) > 0 AS new'
            . ' FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') .
            (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                        ON (p.`id_product` = product_attribute_shop.`id_product`
                            AND product_attribute_shop.`default_on` = 1
                            AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` 
                        AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                ON (m.`id_manufacturer` = p.`id_manufacturer`)
            ' . Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $activeCategory) {
            $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_group` cg 
                    ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` ' . $sqlGroups . ')';
            }
            if ($activeCategory) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
                WHERE p.`id_manufacturer` = ' . (int) $idManufacturer . '
                ' . ($active ? ' AND product_shop.`active` = 1' : '') . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    AND product_shop.id_product IN(' . pSQL($ids) . ')
                GROUP BY p.id_product
                ORDER BY ' . $alias . '`' . bqSQL($orderBy) . '` ' . pSQL($orderWay);
        /* end17 */
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($orderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
        }
        $products = Product::getProductsProperties($idLang, $result);
        $out_products = [];
        // print_r($products);
        $image_types = ImageType::getImagesTypes();
        foreach ($products as $product) {
            $id_product_image = explode('-', $product['id_image']);
            $id_image = $id_product_image[1];
            $protocol = strpos('ttps://', $product['link']) ? 'https://' : 'http://';
            $product['cover'] = self::getCoverInfo(
                $id_image,
                $product['legend'],
                $product['link_rewrite'],
                $image_types,
                $protocol
            );
            $product['url'] = Context::getContext()->link->getProductLink($product['id_product']);
            // echo $product['url'];
            $out_products[] = $product;
        }

        return $out_products;
    }

    public static function getCoverInfo($id_image, $legend, $link_rewrite, $image_types, $protocol)
    {
        $image = [
            'bySize' => [],
            'legend' => $legend,
            'cover' => 1,
            'id_image' => $id_image,
            'position' => 1,
        ];
        $type = $image_types[0]['name'];
        $link = new Link();
        $url = $link->getImageLink($link_rewrite, $id_image, $type);
        foreach ($image_types as $image_type) {
            $new_url = str_replace($type, $image_type['name'], $url);
            $image['bySize'][$image_type['name']] = [
                'url' => $protocol . $new_url,
                'width' => $image_type['width'],
                'height' => $image_type['height'],
            ];
            $image[$image_type['name']] = [];
        }

        return $image;
    }
}
