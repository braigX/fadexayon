<?php

require_once _PS_MODULE_DIR_ . 'quantityupdate/quantityupdate.php';
require_once _PS_MODULE_DIR_ . 'quantityupdate/classes/QuantityUpdateConditionRule.php';

class updateProductCatalog
{
  private $_context;
  private $_idShop;
  private $_idLang;
  private $_format;
  private $_model;
  private $_parser;
  private $_PHPExcelFactory;
  private $_alphabet;
  private $_automaticConfig;
  private $_head;
  private $_productsForUpdate = 0;
  private $_updatedProducts = 0;
  private $_inStoreButNotInFile;
  private $_inStoreAndInFile;
  private $_isFeature = false;
  private $_limit;
  private $_limitN = 500;
  private $_disableProductsWithZeroQty;
  private $_quantityUpdateMethod;
  private $_quantitySource;
  private $_priceSource;
  private $_quantityUpdateConditions;
  private $_priceUpdateConditions;

  public function __construct($config)
  {
    include_once(dirname(__FILE__) . '/../../../config/config.inc.php');
    include_once(dirname(__FILE__) . '/../../../init.php');

    if (!class_exists('PHPExcel')) {
      include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel.php');
      include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel/IOFactory.php');
    }

    include_once(_PS_MODULE_DIR_ . 'quantityupdate/classes/datamodel.php');
    $this->_context = Context::getContext();
    $this->_idShop = $config['id_shop'];
    if (isset(Context::getContext()->shop->id_shop_group)) {
      $this->_shopGroupId = Context::getContext()->shop->id_shop_group;
    } elseif (isset(Context::getContext()->shop->id_group_shop)) {
      $this->_shopGroupId = Context::getContext()->shop->id_group_shop;
    }

    $this->_idLang = $config['id_lang'];
    $this->_format = $config['format_file'];
    $this->_parser = $config['field_for_update'];
    $this->_disableProductsWithZeroQty = !empty($config['zero_quantity_disable']) ? $config['zero_quantity_disable'] : false;
    $this->_quantityUpdateMethod = $config['field_update']['quantity_update_method'];
    $this->_quantitySource = $config['field_update']['quantity_source'];
    $this->_priceSource = $config['field_update']['price_source'];
    $this->_quantityUpdateConditions = $config['field_update']['quantity_update_conditions'];
    $this->_priceUpdateConditions = $config['field_update']['price_update_conditions'];
    $this->_model = new quantityUpdateModel();
    $this->_automaticConfig = $config['field_update'];
    $this->_inStoreButNotInFile = !empty($config['in_store_not_in_file']) ? $config['in_store_not_in_file'] : 'ignore';
    $this->_inStoreAndInFile = !empty($config['in_store_and_in_file']) ? $config['in_store_and_in_file'] : 'ignore';
    $this->_alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
      'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
      'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
      'CA', 'CC', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
      'DA', 'DD', 'DD', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
    );
  }

  public function update($limit = false)
  {

    $this->_limit = $limit;

    if (!$this->_limit) {
      $this->_clearErrorFile();
      Configuration::updateValue('UPDATED_PRODUCTS_CURRENT_COUNT', 0, false, $this->_shopGroupId, $this->_idShop);
      $this->_updatedProducts = 0;
    } else {
      $this->_updatedProducts = (int)Configuration::get('UPDATED_PRODUCTS_CURRENT_COUNT', '', $this->_shopGroupId, $this->_idShop);
    }

    if ($this->_automaticConfig) {
      if ($this->_format == 'csv') {
        $reader = PHPExcel_IOFactory::createReader("CSV");
        $reader->setDelimiter($this->_automaticConfig['delimiter']);
        $this->_PHPExcelFactory = $reader->load(_PS_MODULE_DIR_ . "quantityupdate/files/" . Tools::getValue('settings') . '_import.' . $this->_format);
      } else {
        $this->_PHPExcelFactory = PHPExcel_IOFactory::load(_PS_MODULE_DIR_ . "quantityupdate/files/" . Tools::getValue('settings') . '_import.' . $this->_format);
      }
    } else {
      $this->_copyFile();
    }
    $this->_updateData();

    Configuration::updateValue('UPDATED_PRODUCTS_CURRENT_COUNT', $this->_updatedProducts, false, $this->_shopGroupId, $this->_idShop);

    if ((int)$this->_productsForUpdate > ((int)$this->_limit * (int)$this->_limitN) + (int)$this->_limitN) {
      return (int)$this->_limit + 1;
    }

    if ($this->_updatedProducts != $this->_productsForUpdate) {
      $res = array(
        'message'    => sprintf(Module::getInstanceByName('quantityupdate')->l('Successfully updated %1s items from: %2s', __CLASS__), $this->_updatedProducts, $this->_productsForUpdate),
        'error_logs' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/quantityupdate/error/error_logs.csv',
      );
    } else {
      $res = array(
        'message'    => sprintf(Module::getInstanceByName('quantityupdate')->l('Successfully updated %s items!', __CLASS__), $this->_updatedProducts),
        'error_logs' => false
      );
    }

    if ($this->_disableProductsWithZeroQty) {
      $this->disableProductsWithZeroQuantity();
    }

    return $res;
  }

  private function _checkAutoHead($fileFields)
  {
    if (!in_array($this->_automaticConfig['file_product_identifier'], $fileFields)) {
      throw new Exception(sprintf(Module::getInstanceByName('quantityupdate')->l('You selected field <strong>%s</strong> like Product Identifier, but it is missing now, please check file!', __CLASS__), $this->_automaticConfig['file_product_identifier']));
    }
    if ($this->_automaticConfig['file_product_price'] != 'no' && !in_array($this->_automaticConfig['file_product_price'], $fileFields)) {
      throw new Exception(sprintf(Module::getInstanceByName('quantityupdate')->l('You selected field <strong>%s</strong> like Product Price, but it is missing now, please check file!', __CLASS__), $this->_automaticConfig['file_product_price']));
    }
    if ($this->_automaticConfig['file_quantity'] != 'no' && !in_array($this->_automaticConfig['file_quantity'], $fileFields)) {
      throw new Exception(sprintf(Module::getInstanceByName('quantityupdate')->l('You selected field <strong>%s</strong> like Product Quantity, but it is missing now, please check file!', __CLASS__), $this->_automaticConfig['file_quantity']));
    }
  }

  private function _checkHead($fileFields)
  {
    if ($this->_parser == 'reference') {
      if (!in_array('reference', $fileFields)) {
        throw new Exception(Module::getInstanceByName('quantityupdate')->l('Must be field reference in file for update!', __CLASS__));
      }
    } else {
      if (!in_array('id_product', $fileFields)) {
        throw new Exception(Module::getInstanceByName('quantityupdate')->l('Must be field id_product in file for update!', __CLASS__));
      }
    }

    if (!in_array('price', $fileFields) && !in_array('quantity', $fileFields)) {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Must be field quantity or price in file for update!', __CLASS__));
    }
  }

  private function _getAutomaticProductData($product)
  {
    $data = array();

    if ($this->_automaticConfig['file_quantity'] != 'no') {
      $data['quantity'] = $product[$this->_automaticConfig['file_quantity']];
    }

    if ($this->_automaticConfig['file_product_price'] != 'no') {
      $data['price'] = $product[$this->_automaticConfig['file_product_price']];
      if ($this->_automaticConfig['product_price'] == 'tax_price') {
        $data['tax_price'] = true;
      } else {
        $data['tax_price'] = false;
      }
    }

    if ($this->_automaticConfig['product_identifier'] == 'id_product') {
      $data['id_product'] = $product[$this->_automaticConfig['file_product_identifier']];
    } else {
      $ids = $this->_getIdProductByType($this->_automaticConfig['product_identifier'], $product[$this->_automaticConfig['file_product_identifier']]);

      if (!$ids) {
        $this->_createErrorsFile('Product does not exists with ' . $this->_automaticConfig['product_identifier'] . ' - ' . $product[$this->_automaticConfig['file_product_identifier']], $product[$this->_automaticConfig['file_product_identifier']]);
      }

      foreach ($ids as $productIdentifier) {
        $data['id_product'] = $productIdentifier['id_product'];
        $data['id_product_attribute'] = $productIdentifier['id_product_attribute'];

        $this->_updateProduct($data);
      }

      return true;
    }
    $data['id_product_attribute'] = 0;
    $this->_updateProduct($data);
  }

  private function _updateData()
  {
    foreach ($this->_PHPExcelFactory->getWorksheetIterator() as $worksheet) {
      $highestRow = $worksheet->getHighestRow(); // e.g. 10
      $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $fileFields = array();
      $this->_productsForUpdate = ($highestRow - 1);

      $checkHead = false;

      if ($this->_productsForUpdate <= ($this->_updatedProducts)) {
        return false;
      }

      $rowLimit = ((($this->_limit + 1) * $this->_limitN));

      if ($rowLimit > $this->_productsForUpdate + 1) {
        $rowLimit = $this->_productsForUpdate + 1;
      }
      if ($rowLimit == $this->_productsForUpdate) {
        $rowLimit = $this->_productsForUpdate + 1;
      }

      for ($row = 1; $row <= $rowLimit; ++$row) {
        if ($row != 1 && ($this->_limit * $this->_limitN) >= $row) {
          continue;
        }

        $product = array();
        for ($col = 0; $col < $highestColumnIndex; ++$col) {
          $cell = $worksheet->getCellByColumnAndRow($col, $row);
          $val = $cell->getValue();
          if ($row == 1) {
            if (!$val) {
              continue;
            }
            $fileFields[$col] = $val;
          } else {
            if (!isset($fileFields[$col])) {
              continue;
            }
            if (!$checkHead && $col == 0) {
              if ($this->_automaticConfig) {
                $this->_checkAutoHead($fileFields);
              } else {
                $this->_checkHead($fileFields);
              }
            }

            $product[$fileFields[$col]] = $val;
          }
        }

        if ($this->_automaticConfig && $product) {
          $this->_getAutomaticProductData($product);
        }

        if ($product && !$this->_automaticConfig && ($product['id_product'] || $product['reference'])) {
          if ($this->_parser == 'reference') {

            if (!$product['reference']) {
              continue;
            }

            $res = $this->_getIdProductByType('reference', $product['reference']);

            if ($res) {
              foreach ($res as $value) {
                $product['id_product'] = $value['id_product'];

                if (isset($value['id_product_attribute']) && $value['id_product_attribute']) {
                  $product['id_product_attribute'] = $value['id_product_attribute'];
                } else {
                  $product['id_product_attribute'] = 0;
                }

                $this->_updateProduct($product);
              }
            } else {
              $this->_createErrorsFile('Product does not exists with reference - ' . $product['reference'], '-');
            }

          } else {
            if (!$product['id_product']) {
              continue;
            } else {
              $this->_updateProduct($product);
            }
          }
        }
      }

      if ($this->_automaticConfig && $this->_inStoreButNotInFile !== 'ignore') {
        $this->processProductsThatAreInStoreButNotInFile();
      }

      if ($this->_automaticConfig && $this->_inStoreAndInFile !== 'ignore') {
        $this->processProductsThatAreInStoreAndInFile();
      }

      return true;
    }
  }

  private function processProductsThatAreInStoreButNotInFile()
  {
    $products_for_update = $this->getProductsIdsForUpdate(true);

    foreach ($products_for_update as $product_id_container) {
      if ($product_id_container['id_product'] == 0) {
        continue;
      }

      switch ($this->_inStoreButNotInFile) {
        case 'enable':
          $this->enableProduct((int)$product_id_container['id_product']);
          break;
        case 'disable':
          $this->disableProduct((int)$product_id_container['id_product']);
          break;
        case 'zero_quantity':
          $this->setQuantityForProductAndAllCombinations((int)$product_id_container['id_product'], 0);
          break;
      }
    }

    return true;
  }

  private function processProductsThatAreInStoreAndInFile()
  {
    $products_for_update = $this->getProductsIdsForUpdate();

    foreach ($products_for_update as $product_id_container) {
      if ($product_id_container['id_product'] == 0) {
        continue;
      }

      switch ($this->_inStoreAndInFile) {
        case 'enable':
          $this->enableProduct((int)$product_id_container['id_product']);
          break;
        case 'disable':
          $this->disableProduct((int)$product_id_container['id_product']);
          break;
        case 'zero_quantity':
          $this->setQuantityForProductAndAllCombinations((int)$product_id_container['id_product'], 0);
          break;
      }
    }

    return true;
  }

  private function enableProduct($id_product)
  {
    $product_obj = new Product($id_product, false, null, $this->_idShop);
    $product_obj->active = true;
    return $product_obj->update();
  }

  private function disableProduct($id_product)
  {
    $product_obj = new Product($id_product, false, null, $this->_idShop);
    $product_obj->active = false;
    return $product_obj->update();
  }

  private function setQuantityForProductAndAllCombinations($id_product, $quantity)
  {
    $all_product_attributes_ids = Product::getProductAttributesIds($id_product, true);

    foreach ($all_product_attributes_ids as $attribute_container) {
      StockAvailable::setQuantity($id_product, $attribute_container['id_product_attribute'], (int)$quantity);
    }

    StockAvailable::setQuantity($id_product, null, (int)$quantity);
  }

  private function _getIdProductByType($type, $value)
  {
    if (empty($value)) {
      return array();
    }

    $where = "";
    $whereAttr = "";

    if ($type == 'reference') {
      $where = ' WHERE p.reference = "' . pSQL($value) . '" ';
      $whereAttr = ' WHERE pa.reference = "' . pSQL($value) . '" ';
    }

    if ($type == 'ean13') {
      $where = ' WHERE p.ean13 = "' . pSQL($value) . '" ';
      $whereAttr = ' WHERE pa.ean13 = "' . pSQL($value) . '" ';
    }

    if ($type == 'upc') {
      $where = ' WHERE p.upc = "' . pSQL($value) . '" ';
      $whereAttr = ' WHERE pa.upc = "' . pSQL($value) . '" ';
    }

    $sql = '
			SELECT pa.id_product, pa.id_product_attribute
      FROM ' . _DB_PREFIX_ . 'product_attribute AS pa
      ' . $whereAttr . '
			';
    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    $sql = '
			SELECT p.id_product, 0 AS id_product_attribute
      FROM ' . _DB_PREFIX_ . 'product AS p
      ' . $where . '
			';
    $res_p = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


    return array_merge($res_p, $res);
  }

  private function _updateProduct($product)
  {
    Configuration::updateValue('UPDATED_PRODUCTS_CURRENT_COUNT', $this->_updatedProducts, false, $this->_shopGroupId, $this->_idShop);

    if (!ObjectModel::existsInDatabase($product['id_product'], 'product')) {
      $this->_createErrorsFile('Product does not exists with Product ID - ' . $product['id_product'], $product['id_product']);
      return false;
    }

    self::addIdToListOfProductsForUpdate($product['id_product']);

    $quantity_is_updated = $this->updateQuantity($product);
    $price_is_updated = $this->updatePrice($product);

    if ($quantity_is_updated && $price_is_updated) {
      $this->_updatedProducts++;
    }

    return true;
  }

  private function updateQuantity($product)
  {
    if (isset($product['quantity']) && is_numeric($product['quantity'])) {
      $file_quantity = $product['quantity'];

      if ($this->_quantitySource == 'file') {
        $current_qty = Product::getQuantity($product['id_product'], (int)$product['id_product_attribute']);

        switch ($this->_quantityUpdateMethod) {
          case 'add':
            $file_quantity = $current_qty + $file_quantity;
            break;
          case 'subtract':
            $file_quantity = $current_qty - $file_quantity;
            break;
        }
      }

      StockAvailable::setQuantity($product['id_product'], (int)$product['id_product_attribute'], $file_quantity, $this->_idShop);
    }

    if (!empty($this->_quantityUpdateConditions)) {
      if (($this->_quantitySource == 'file' && isset($product['quantity'])) || $this->_quantitySource == 'shop') {
        $this->applyQuantityConditionRules($product);
      }
    }

    return true;
  }

  private function updatePrice($product)
  {
    if (isset($product['price']) && $product['price']) {
      $productObject = new Product($product['id_product'], false, null, $this->_idShop);
      $product['price'] = $this->formatPrice($product['price']);

      if (Tools::getValue('tax_price_update') || (!empty($product['tax_price']) && $this->_priceSource === 'file')) {
        $address = null;
        if (is_object(Context::getContext()->cart) && Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
          $address = Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        $taxRate = (float)$productObject->getTaxesRate(new Address($address));
        $product['price'] = $this->formatPrice($product['price'] / (($taxRate / 100) + 1));
      }

      if (!$this->_priceSource || $this->_priceSource === 'file') {
        if (!$product['id_product_attribute']) {
          $productObject->price = $product['price'];

          if (($error = $productObject->validateFields(false, true)) !== true) {
            $this->_createErrorsFile($error, $product['id_product']);
            return false;
          }

          if (($error = $productObject->validateFieldsLang(false, true)) !== true) {
            $this->_createErrorsFile($error, $product['id_product']);
            return false;
          }

          $productObject->update();
        } else {
          if (!ObjectModel::existsInDatabase($product['id_product_attribute'], 'product_attribute')) {
            $this->_createErrorsFile('Product Combination does not exists with id_product_attribute - ' . $product['id_product_attribute'], '-');
            return false;
          }

          $combination = new Combination($product['id_product_attribute'], null, $this->_idShop);
          $combination->price = $product['price'];
          if (($error = $combination->validateFields(false, true)) !== true) {
            $this->_createErrorsFile($error, $product['id_product']);
            return false;
          }

          $combination->update();
        }
      }
    }

    if (!empty($this->_priceUpdateConditions)) {
      if (($this->_priceSource == 'file' && isset($product['price'])) || $this->_priceSource == 'shop') {
        if (!$product['id_product_attribute']) {
          $this->applyPriceConditionRules($product);
        } else {
          $this->applyCombinationPriceConditionRules($product);
        }
      }
    }

    return true;
  }


  private function applyQuantityConditionRules($product)
  {
    $current_qty = Product::getQuantity($product['id_product'], (int)$product['id_product_attribute']);

    if ($this->_quantitySource == 'file' && isset($product['quantity'])) {
      if (is_numeric($product['quantity'])) {
        $quantity = $current_qty;
      } else {
        $quantity = $product['quantity'];
      }
    } else if ($this->_quantitySource == 'shop') {
      $quantity = $current_qty;
    } else {
      return false;
    }

    $condition_rule_handler = new QuantityUpdateConditionRule($quantity, $this->_quantityUpdateConditions, QuantityUpdateConditionRule::TYPE_QTY);
    $applied_conditions_result = $condition_rule_handler->applyRules();
    $new_qty = is_numeric($applied_conditions_result) ? $applied_conditions_result : Product::getQuantity($product['id_product'], (int)$product['id_product_attribute']);
    StockAvailable::setQuantity($product['id_product'], (int)$product['id_product_attribute'], $new_qty, $this->_idShop);

    return true;
  }

  private function applyPriceConditionRules($product)
  {
    $productObject = new Product($product['id_product'], false, null, $this->_idShop);

    if ($this->_priceSource == 'file' && isset($product['price'])) {
      if (is_numeric($product['price'])) {
        $price = $productObject->price;
      } else {
        $price = $product['price'];
      }
    } else if ($this->_priceSource == 'shop') {
      $price = $productObject->price;
    } else {
      return false;
    }

    $condition_rule_handler = new QuantityUpdateConditionRule($price, $this->_priceUpdateConditions, QuantityUpdateConditionRule::TYPE_PRICE);

    $applied_conditions_result = $condition_rule_handler->applyRules();
    $productObject->price = is_numeric($applied_conditions_result) ? $applied_conditions_result : $productObject->price;
    $productObject->update();

    return true;
  }

  private function applyCombinationPriceConditionRules($product)
  {
    $combination = new Combination($product['id_product_attribute'], null, $this->_idShop);

    if ($this->_priceSource == 'file' && isset($product['price'])) {
      if (is_numeric($product['price'])) {
        $price = $combination->price;
      } else {
        $price = $product['price'];
      }
    } else if ($this->_priceSource == 'shop') {
      $price = $combination->price;
    } else {
      return false;
    }

    $condition_rule_handler = new QuantityUpdateConditionRule($price, $this->_priceUpdateConditions, QuantityUpdateConditionRule::TYPE_PRICE);

    $applied_conditions_result = $condition_rule_handler->applyRules();
    $combination->price = is_numeric($applied_conditions_result) ? $applied_conditions_result : $combination->price;

    $combination->update();

    return true;
  }

  private function getProductsIdsForUpdate($in_store_not_in_file = false)
  {
    $where = ' AND pfu.id_product IS NOT NULL';

    if ($in_store_not_in_file === true) {
      $where = ' AND pfu.id_product IS NULL';
    }

    $sql = Db::getInstance()->executeS("SELECT DISTINCT(ps.id_product) 
            FROM " . _DB_PREFIX_ . "product_shop as ps
            LEFT JOIN " . quantityUpdate::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME . " pfu
            ON pfu.id_product = ps.id_product
            WHERE ps.id_shop = " . (int)$this->_idShop . $where);

    return $sql;
  }

    public static function addIdToListOfProductsForUpdate($id_product)
    {
      return Db::getInstance()->execute("INSERT INTO `" . quantityUpdate::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME . "` (`id_product`) VALUES(".(int)$id_product.")");
    }

    public static function clearIdsOfProductsForUpdate()
    {
      return Db::getInstance()->execute("TRUNCATE TABLE " . quantityUpdate::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME);
    }

    private function disableProductsWithZeroQuantity()
    {
      $products_from_file_ids = $this->getProductsIdsForUpdate();

      foreach ($products_from_file_ids as $id_product_container) {
        $id_product = $id_product_container['id_product'];
        $product_attributes_ids = Product::getProductAttributesIds($id_product);
        $product_quantity_without_combination = Product::getQuantity($id_product);

        if (empty($product_attributes_ids)) {
          if ($product_quantity_without_combination == 0) {
            $this->disableProduct($id_product);
          }

          continue;
        } else {
          foreach ($product_attributes_ids as $product_attribute_id_container) {
            $product_attribute_id = $product_attribute_id_container['id_product_attribute'];
            if (Product::getQuantity($id_product, $product_attribute_id) > 0) {
              continue 2;
            }
          }

          if ($product_quantity_without_combination > 0) {
            continue;
          }
        }

        $this->disableProduct($id_product);
      }

      return true;
    }

    private function formatPrice($price)
    {
        $price = str_replace(',', '.', $price);
        return number_format($price, 4, '.', '');
    }

  private function _createErrorsFile($error, $nameProduct)
  {
    $write_fd = fopen(_PS_MODULE_DIR_ . 'quantityupdate/error/error_logs.csv', 'a+');
    if (@$write_fd !== false) {
      fwrite($write_fd, $nameProduct . ',' . $error . "\r\n");
    }
    fclose($write_fd);
  }

  private function _clearErrorFile()
  {
    $write_fd = fopen(_PS_MODULE_DIR_ . 'quantityupdate/error/error_logs.csv', 'w');
    fwrite($write_fd, $this->_parser.',error' . "\r\n");
    fclose($write_fd);
  }

  private function _copyFile()
  {
    if (!isset($_FILES['file'])) {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please select file for update!', __CLASS__));
    }

    $file_type = Tools::substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.') + 1);

    if ($file_type != $this->_format && $this->_format == 'xlsx') {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('File must have XLSX extension!', __CLASS__));
    }

    if ($file_type != $this->_format && $this->_format == 'csv') {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('File must have CSV extension!', __CLASS__));
    }

    if (!Tools::copy($_FILES['file']['tmp_name'], _PS_MODULE_DIR_ . 'quantityupdate/files/import_products.' . $this->_format)) {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('An error occurred while uploading: ', __CLASS__) . $_FILES['file']['tmp_name']);
    }

    $this->_PHPExcelFactory = PHPExcel_IOFactory::load(_PS_MODULE_DIR_ . "quantityupdate/files/import_products." . $this->_format);
  }
}