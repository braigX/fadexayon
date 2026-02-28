<?php

class quantityUpdateModel
{
  private $_context;

  public function __construct(){
    include_once(dirname(__FILE__).'/../../../config/config.inc.php');
    include_once(dirname(__FILE__).'/../../../init.php');

      include_once(_PS_MODULE_DIR_.'quantityupdate/classes/quantityUpdateTools.php');
    $this->_context = Context::getContext();
  }

  public function searchProduct( $id_shop = false, $id_lang  = false, $search = false )
  {
    if($id_shop === false){
      $id_shop =  $this->_context->shop->id ;
    }
    if($id_lang === false){
      $id_lang =  $this->_context->language->id ;
    }
    $where = "";
    if( $search ){
      $where = " AND (pl.name LIKE '%".pSQL($search)."%' OR p.id_product LIKE '%".pSQL($search)."%' OR p.reference LIKE '%".pSQL($search)."%')";
    }
    $sql = '
			SELECT p.id_product, pl.name, p.reference
      FROM ' . _DB_PREFIX_ . 'product_lang as pl
      LEFT JOIN ' . _DB_PREFIX_ . 'product as p
      ON p.id_product = pl.id_product
      WHERE pl.id_lang = ' . (int)$id_lang . '
      AND pl.id_shop = ' . (int)$id_shop . '
      ' . $where . '
      ORDER BY pl.name
      LIMIT 0,50
			';
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function getSettings(){
    $settings = array();

    $sql = '
			SELECT c.value
      FROM ' . _DB_PREFIX_ . 'configuration as c
      WHERE c.name LIKE "GOMAKOIL_QUANTITYUPDATE_%"
			';

    $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    foreach( $row as $key => $setting ){
      $settings[$key]['id'] = $key+1;
      $tmpSettings = quantityUpdateTools::jsonDecode($setting['value']);
      $settings[$key]['settings_name'] = $tmpSettings['settings'];
    }

    return $settings;
  }

  public function searchManufacturer( $search = false )
  {
    $where = "";
    if( $search ){
      $where = " AND (m.name LIKE '%".pSQL($search)."%' OR m.id_manufacturer LIKE '%".pSQL($search)."%')";
    }
    $sql = '
			SELECT m.id_manufacturer, m.name
      FROM ' . _DB_PREFIX_ . 'manufacturer as m
      WHERE 1
      ' . $where . '
      ORDER BY m.name
      LIMIT 0,50
			';
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function searchSupplier( $search = false )
  {
    $where = "";
    if( $search ){
      $where = " AND (p.name LIKE '%".pSQL($search)."%' OR p.id_supplier LIKE '%".pSQL($search)."%')";
    }
    $sql = '
			SELECT p.id_supplier, p.name
      FROM ' . _DB_PREFIX_ . 'supplier as p
      WHERE 1
      ' . $where . '
      ORDER BY p.name
      LIMIT 0,50
			';
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function showCheckedProducts( $id_shop = false, $id_lang  = false, $products_check = false )
  {
    if($id_shop === false){
      $id_shop = $this->_context->shop->id ;
    }
    if($id_lang === false){
      $id_lang = $this->_context->language->id ;
    }
    $where = "";
    $limit = "  LIMIT 300 ";
    if( $products_check !== false ){
      if( !$products_check ){
        return array();
      }
      $products_check = implode(",", $products_check);
      $where = " AND p.id_product  IN (".implode(',', array_map('intval', explode(',', $products_check))).") ";
      $limit = "";
    }
    $sql = '
			SELECT p.id_product, pl.name
      FROM ' . _DB_PREFIX_ . 'product_lang as pl
      LEFT JOIN ' . _DB_PREFIX_ . 'product as p
      ON p.id_product = pl.id_product
      WHERE pl.id_lang = ' . (int)$id_lang . '
      AND pl.id_shop = ' . (int)$id_shop . '
      ' . $where . '
      ORDER BY pl.name
      ' . $limit . '
			';

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function showCheckedManufacturers( $items_check = false )
  {
    $where = "";
    $limit = "  LIMIT 300 ";
    if( $items_check !== false ){
      if( !$items_check ){
        return array();
      }
      $items_check = implode(",", $items_check);
      $where = " AND m.id_manufacturer  IN (".implode(',', array_map('intval', explode(',', $items_check))).") ";
      $limit = "";
    }
    $sql = '
			SELECT m.id_manufacturer, m.name
      FROM ' . _DB_PREFIX_ . 'manufacturer as m
      WHERE 1
      ' . $where . '
      ORDER BY m.name
      ' . $limit . '
			';

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function showCheckedSuppliers( $items_check = false )
  {
    $where = "";
    $limit = "  LIMIT 300 ";
    if( $items_check !== false ){
      if( !$items_check ){
        return array();
      }
      $items_check = implode(",", $items_check);
      $where = " AND s.id_supplier  IN (".implode(',', array_map('intval', explode(',', $items_check))).") ";
      $limit = "";
    }
    $sql = '
			SELECT s.id_supplier, s.name
      FROM ' . _DB_PREFIX_ . 'supplier as s
      WHERE 1
      ' . $where . '
      ORDER BY s.name
      ' . $limit . '
			';

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  public function getExportIds( $idShop  = false, $idLang = false, $more_settings = false, $limit = 0, $limitN = false, $count = false )
  {
    if( !$limit ){
      $limit = " LIMIT 0,".(int)$limitN." ";
    }
    else{
      $limit = " LIMIT ".( (int)$limit*(int)$limitN ).",".(int)$limitN." ";
    }
    
    $products_check = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_PRODUCTS_CHECKED'));
    $selected_manufacturers = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_MANUFACTURERS_CHECKED'));
    $selected_suppliers = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_SUPPLIERS_CHECKED'));
    $selected_categories = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_CATEGORIES_CHECKED'));
    $orderby = $more_settings['orderby'];
    $orderway = $more_settings['orderway'];

    $where = "";
    $justProducts = true;

    $price = $more_settings['price_products'];
    $quantity = $more_settings['quantity_products'];



      if( version_compare(_PS_VERSION_, '1.6.0.0') >= 0 && version_compare(_PS_VERSION_, '1.6.1.0') < 0) {

        if($price['price_value'] !== '' && $price['selection_type_price']){
          if($price['selection_type_price'] == 1){
            $where .= ' AND ( (ps.price) < '. (float)$price['price_value'] . ' OR pa.price < '. (float)$price['price_value'] . ' ) ';
          }
          if($price['selection_type_price'] == 2){
            $where .= ' AND ( (ps.price) > '. (float)$price['price_value'] . ' OR pa.price > '. (float)$price['price_value'] . ' ) ';;
          }
          if($price['selection_type_price'] == 3){
            $where .= ' AND ( (ps.price) = '. (float)$price['price_value'] . ' OR pa.price = '. (float)$price['price_value'] . ' ) ';;
          }
        }






      }
      else{
        if($price['price_value'] !== '' && $price['selection_type_price']){
          if($price['selection_type_price'] == 1){
            $where .= ' AND ( (ps.price) < '. (float)$price['price_value'] . ' OR pas.price < '. (float)$price['price_value'] . ' ) ';
          }
          if($price['selection_type_price'] == 2){
            $where .= ' AND ( (ps.price) > '. (float)$price['price_value'] . ' OR pas.price > '. (float)$price['price_value'] . ' ) ';;
          }
          if($price['selection_type_price'] == 3){
            $where .= ' AND ( (ps.price) = '. (float)$price['price_value'] . ' OR pas.price = '. (float)$price['price_value'] . ' ) ';;
          }
        }
      }




    if($quantity['quantity_value'] !== '' && $quantity['selection_type_quantity']){
      if($quantity['selection_type_quantity'] == 1){
        $where .= ' AND (sa.quantity) < '. (int)$quantity['quantity_value'];
      }
      if($quantity['selection_type_quantity'] == 2){
        $where .= ' AND (sa.quantity) > '. (int)$quantity['quantity_value'];
      }
      if($quantity['selection_type_quantity'] == 3){
        $where .= ' AND (sa.quantity) = '. (int)$quantity['quantity_value'];
      }
    }

    if($more_settings['active_products']){
      $where .= " AND ps.active = 1 ";
    }

    if($more_settings['inactive_products']){
      $where .= " AND ps.active = 0 ";
    }



    if( $selected_manufacturers ){
      $justProducts = false;
      $selected_manufacturers = implode(",", $selected_manufacturers);
      $where .= " AND p.id_manufacturer IN (".implode(',', array_map('intval', explode(',', $selected_manufacturers))).") ";
    }

    if( $selected_suppliers ){
      $justProducts = false;
      $selected_suppliers = implode(",", $selected_suppliers);
      $where .= " AND s.id_supplier IN (".implode(',', array_map('intval', explode(',', $selected_suppliers))).") ";
    }

    if( $selected_categories ){
      $justProducts = false;
      $selected_categories = implode(",", $selected_categories);
      $where .= " AND cp.id_category IN (".implode(',', array_map('intval', explode(',', $selected_categories))).") ";
    }


    if( $products_check ){
      $products_check = implode(",", $products_check);
      $justProducts = $justProducts ? 'AND' : 'OR';
      $where .= " $justProducts p.id_product IN (".implode(',', array_map('intval', explode(',', $products_check))).") ";
    }

    $order = ' ORDER BY p.id_product DESC, pa.id_product_attribute';
    if($orderway == 'asc'){
      $order_way = ' ASC';
    }
    else{
      $order_way = ' DESC';
    }


    $join = '';
    if( version_compare(_PS_VERSION_, '1.6.0.0') >= 0 && version_compare(_PS_VERSION_, '1.6.1.0') < 0) {

      if($orderby == 'id'){
        $order = ' ORDER BY p.id_product'.$order_way.', pa.id_product_attribute ASC';
      }
      if($orderby == 'name'){
        $order = ' ORDER BY pl.name '.$order_way.', pa.id_product_attribute ASC';
      }
      if($orderby == 'price'){
        $order = ' ORDER BY p.price '.$order_way.', pa.price '.$order_way.', pa.id_product_attribute ASC';
      }
      if($orderby == 'quantity'){
        $order = ' ORDER BY sa.quantity '.$order_way.', pa.id_product_attribute ASC';
      }
      if($orderby == 'date_add'){
        $order = ' ORDER BY p.date_add '.$order_way.', pa.id_product_attribute ASC';
      }
      if($orderby == 'date_update'){
        $order = ' ORDER BY p.date_upd '.$order_way.', pa.id_product_attribute ASC';
      }

      $join = 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute as pa  ON p.id_product = pa.id_product';
    }
    else{

      if($orderby == 'id'){
        $order = ' ORDER BY p.id_product'.$order_way.', pas.id_product_attribute ASC';
      }
      if($orderby == 'name'){
        $order = ' ORDER BY pl.name '.$order_way.', pas.id_product_attribute ASC';
      }
      if($orderby == 'price'){
        $order = ' ORDER BY p.price '.$order_way.', pas.price '.$order_way.', pas.id_product_attribute ASC';
      }
      if($orderby == 'quantity'){
        $order = ' ORDER BY sa.quantity '.$order_way.', pas.id_product_attribute ASC';
      }
      if($orderby == 'date_add'){
        $order = ' ORDER BY p.date_add '.$order_way.', pas.id_product_attribute ASC';
      }
      if($orderby == 'date_update'){
        $order = ' ORDER BY p.date_upd '.$order_way.', pas.id_product_attribute ASC';
      }

      $join = 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop as pas  ON p.id_product = pas.id_product AND pas.id_shop = ' . (int)$idShop ;
    }

    $sql = "
        SELECT DISTINCT p.id_product
         FROM " . _DB_PREFIX_ . "product as p
         INNER JOIN " . _DB_PREFIX_ . "product_shop as ps
         ON p.id_product = ps.id_product
         LEFT JOIN " . _DB_PREFIX_ . "category_product as cp
         ON p.id_product = cp.id_product
         LEFT JOIN " . _DB_PREFIX_ . "product_lang as pl
         ON p.id_product = pl.id_product
         LEFT JOIN " . _DB_PREFIX_ . "product_supplier as s
         ON p.id_product = s.id_product
         LEFT JOIN " . _DB_PREFIX_ . "stock_available as sa
         ON p.id_product = sa.id_product AND sa.id_shop = " . (int)$idShop . "
 
          ".$join."
 
         LEFT JOIN " . _DB_PREFIX_ . "specific_price as sp
         ON p.id_product = sp.id_product
         WHERE ps.id_shop = " . (int)$idShop . "
         ".$where."
         ".$order."
         " . $limit . "

      ";

    if( $count ){
      $sql = "
          SELECT count(DISTINCT p.id_product) as count
         FROM " . _DB_PREFIX_ . "product as p
         INNER JOIN " . _DB_PREFIX_ . "product_shop as ps
         ON p.id_product = ps.id_product
         LEFT JOIN " . _DB_PREFIX_ . "category_product as cp
         ON p.id_product = cp.id_product
         LEFT JOIN " . _DB_PREFIX_ . "product_supplier as s
         ON p.id_product = s.id_product
         LEFT JOIN " . _DB_PREFIX_ . "stock_available as sa
         ON p.id_product = sa.id_product AND sa.id_shop = " . (int)$idShop . "
          ".$join."
         LEFT JOIN " . _DB_PREFIX_ . "specific_price as sp
         ON p.id_product = sp.id_product
         WHERE ps.id_shop = " . (int)$idShop . "
         ".$where."
      ";

    }


    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


    if( $count ){
      return $res[0]['count'];
    }

    return $res;
  }
}