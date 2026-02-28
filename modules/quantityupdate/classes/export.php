<?php

class quantityExportProducts
{
  private $_context;
  private $_idShop;
  private $_idLang;
  private $_format;
  private $_model;
  private $_PHPExcel;
  private $_alphabet;
  private $_head;
  private $_more_settings;
  private $_productsCount;
  private $_imageType;
  private $_limit;
  private $_limitN = 1000;

  public function __construct( $idShop, $idLang, $format, $more_settings ){
    include_once(dirname(__FILE__).'/../../../config/config.inc.php');
    include_once(dirname(__FILE__).'/../../../init.php');

    if (!class_exists('PHPExcel')) {
      include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel.php');
      include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel/IOFactory.php');
    }

    include_once(_PS_MODULE_DIR_ . 'quantityupdate/classes/datamodel.php');

    $this->_context = Context::getContext();
    $this->_idShop = $idShop;
    $this->_idLang = $idLang;
    $this->_format = $format;
    $this->_more_settings = $more_settings;
    if( $format == 'csv' ){
      $this->_more_settings['image_cover'] = false;
    }
    $this->_shopGroupId = ContextCore::getContext()->shop->id_shop_group;
    $this->_model = new quantityUpdateModel();
    $this->_PHPExcel = new PHPExcel();
    $this->_alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
      'BA','BB','BC','BD','BE','BF','BG','BH', 'BI','BJ','BK','BL','BM','BN','BO','BP','BQ', 'BR','BS','BT','BU','BV','BW','BX','BY','BZ',
      'CA','CB','CC','CD','CE','CF','CG','CH', 'CI','CJ','CK','CL','CM','CN','CO','CP','CQ', 'CR','CS','CT','CU','CV','CW','CX','CY','CZ',
      'DA','DB','DC','DD','DE','DF','DG','DH', 'DI','DJ','DK','DL','DM','DN','DO','DP','DQ', 'DR','DS','DT','DU','DV','DW','DX','DY','DZ',
      'EA','ED','EC'
    );

    if( $this->_more_settings['image_cover'] ){
      $imageTypes = ImageType::getImagesTypes('products');
      foreach ( $imageTypes  as $type ){
        if( $type['height'] > 150 ){
          $this->_imageType = $type['name'];
          break;
        }
      }
    }
  }

  public function export( $limit = 0 )
  {
    $this->_limit = $limit;

    if( !$limit ){
      Configuration::updateValue('EXPORT_PRODUCTS_TIME', Date('Y.m.d_G-i-s'), false, $this->_shopGroupId, $this->_idShop);
      $this->_productsCount = $this->_model->getExportIds( $this->_idShop, $this->_idLang, $this->_more_settings, $limit, $this->_limitN, true );
      Configuration::updateValue('EXPORT_PRODUCTS_COUNT', $this->_productsCount, false, $this->_shopGroupId, $this->_idShop);
      Configuration::updateValue('EXPORT_PRODUCTS_CURRENT_COUNT', 0, false, $this->_shopGroupId, $this->_idShop);
      if( !$this->_productsCount ){
        throw new Exception(Module::getInstanceByName('quantityupdate')->l('No of matching products','export'));
      }
    }
    else{
      $this->_productsCount = Configuration::get('EXPORT_PRODUCTS_COUNT', '' ,$this->_shopGroupId, $this->_idShop);

      if( $this->_format == 'xlsx' || $this->_format == 'xls' ){
        $this->_PHPExcel = PHPExcel_IOFactory::load(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '' ,$this->_shopGroupId, $this->_idShop) . ( (int)$limit - 1 ) . '.' . $this->_format);
      }

      if( $this->_format == 'csv' ){
        $reader = PHPExcel_IOFactory::createReader("CSV");
        $this->_PHPExcel = $reader->load(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '' ,$this->_shopGroupId, $this->_idShop) . ( (int)$limit - 1 ) . '.' . $this->_format);
      }
    }

    $productIds = $this->_model->getExportIds( $this->_idShop, $this->_idLang, $this->_more_settings, $limit, $this->_limitN );

    return $this->_getProductsData($productIds);
  }

  private function _checkQuantityCondition( $productQuantity )
  {
    $rightQuantityCondition = false;
    $quantityCondition = $this->_more_settings['quantity_products'];

    if( $quantityCondition['quantity_value'] !== '' && $quantityCondition['selection_type_quantity'] ){
      if($quantityCondition['selection_type_quantity'] == 1){
        if( (int)$productQuantity < (int)$quantityCondition['quantity_value'] ){
          $rightQuantityCondition = true;
        }
      }
      if($quantityCondition['selection_type_quantity'] == 2){
        if( (int)$productQuantity > (int)$quantityCondition['quantity_value'] ){
          $rightQuantityCondition = true;
        }
      }
      if($quantityCondition['selection_type_quantity'] == 3){
        if( (int)$productQuantity == (int)$quantityCondition['quantity_value'] ){
          $rightQuantityCondition = true;
        }
      }
    }

    if( $quantityCondition['quantity_value'] == '' ){
      $rightQuantityCondition = true;
    }

    return $rightQuantityCondition;
  }

  private function _checkPriceCondition( $productPrice )
  {
    $rightPriceCondition = false;

    $priceCondition = $this->_more_settings['price_products'];
    if( $priceCondition['price_value'] !== '' && $priceCondition['selection_type_price'] ){
      if($priceCondition['selection_type_price'] == 1){
        if( (float)$productPrice < (float)$priceCondition['price_value'] ){
          $rightPriceCondition = true;
        }
      }
      if($priceCondition['selection_type_price'] == 2){
        if( (float)$productPrice > (float)$priceCondition['price_value'] ){
          $rightPriceCondition = true;
        }
      }
      if($priceCondition['selection_type_price'] == 3){
        if( (float)$productPrice == (float)$priceCondition['price_value'] ){
          $rightPriceCondition = true;
        }
      }
    }

    if( $priceCondition['price_value'] == '' ){
      $rightPriceCondition = true;
    }
    
    return $rightPriceCondition;
  }

  private function _getProductsData( $productIds )
  {
    $line = 2;
    if( $this->_limit ){

      foreach ( $this->_PHPExcel->getWorksheetIterator() as $worksheet ){
        $highestRow         = $worksheet->getHighestRow();
        break;
      }
      $line = $highestRow+1;
    }
    $this->_createHead();

    foreach( $productIds as $productId ){
      $productId = $productId['id_product'];
      $product = new ProductCore($productId, false, $this->_idLang, $this->_idShop);
      $combinations = array();
      foreach( $product->getWsCombinations() as $attribute ){
        $combination = new Combination($attribute['id'], null, $this->_idShop );
        $combinations[$attribute['id']] = $combination;
      }

      $productPrice = $product->price;

      if( $this->_more_settings['tax_price'] ){
        $address = null;
        if (is_object(Context::getContext()->cart) && Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
          $address = Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }
        $taxRate = (float)$product->getTaxesRate(new Address($address));
        $productPrice = (float)$productPrice;
        $productPrice = $productPrice + ($productPrice * ($taxRate / 100));
      }

      $productPrice = Tools::ps_round($productPrice, $this->_more_settings['round_value']);
      $productPrice = number_format($productPrice, $this->_more_settings['round_value'], '.','');
      $productQuantity = StockAvailable::getQuantityAvailableByProduct($productId, 0);
      
      if( $this->_checkPriceCondition($productPrice) && $this->_checkQuantityCondition($productQuantity) ){
        $data = array(
          'id_product'           => $productId,
          'id_product_attribute' => 0,
          'reference'            => $product->reference,
          'product_name'         => $product->getProductName($productId),
          'quantity'             => $productQuantity,
          'price'                => $productPrice,
        );

        if( $this->_more_settings['image_cover'] ){
          $cover = $product->getCover($product->id);
          if( !$cover ){
            $data['product_cover_image'] = false;
          }
          else{
            $url_cover = _PS_ROOT_DIR_.'/img/p/'.Image::getImgFolderStatic($cover['id_image']).$cover['id_image'].'-'.$this->_imageType.'.jpg';
            $data['product_cover_image'] = $url_cover;
          }
        }

        $this->_setProductInFile($data, $line);
        $line++;
      }

      $currentExported = Configuration::get('EXPORT_PRODUCTS_CURRENT_COUNT','',$this->_shopGroupId, Context::getContext()->shop->id);
      Configuration::updateValue('EXPORT_PRODUCTS_CURRENT_COUNT', ((int)$currentExported+1), false, $this->_shopGroupId, Context::getContext()->shop->id);

      if( $combinations ){
        foreach( $combinations as $idCombination => $combination ){

          $combinationPrice = $combination->price;
          if( $this->_more_settings['tax_price'] ){
            $address = null;
            if (is_object(Context::getContext()->cart) && Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
              $address = Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }
            $taxRate = (float)$product->getTaxesRate(new Address($address));
            $combinationPrice = (float)$combinationPrice;
            $combinationPrice = $combinationPrice + ($combinationPrice * ($taxRate / 100));
          }
          $combinationPrice = Tools::ps_round($combinationPrice, $this->_more_settings['round_value']);
          $combinationPrice = number_format($combinationPrice, $this->_more_settings['round_value'], '.','');

          $combinationQuantity = $product->getQuantity($productId, $idCombination);

          if( !$this->_checkQuantityCondition($combinationQuantity) || !$this->_checkPriceCondition($combinationPrice) ){
            continue;
          }
          
          $data = array(
            'id_product'           => $productId,
            'id_product_attribute' => $idCombination,
            'reference'            => $combination->reference,
            'product_name'         => $product->getProductName($productId, $idCombination),
            'quantity'             => $combinationQuantity,
            'price'                => $combinationPrice,
          );

          if( $this->_more_settings['image_cover'] ){
            $cover = $product->getCover($product->id);
            $images = $this->getCombinationImageById($idCombination, $this->_idLang);

            if( !$cover && !$images ){
              $data['product_cover_image'] = false;
            }
            else{
              if($images['id_image']){
                  $url_cover = _PS_ROOT_DIR_.'/img/p/'.Image::getImgFolderStatic($images['id_image']).$images['id_image'].'-'.$this->_imageType.'.jpg';
              }
              else{
                $url_cover = _PS_ROOT_DIR_.'/img/p/'.Image::getImgFolderStatic($cover['id_image']).$cover['id_image'].'-'.$this->_imageType.'.jpg';
              }
              $data['product_cover_image'] = $url_cover;
            }
          }
          $this->_setProductInFile($data, $line);
          $line++;
        }
      }
    }

    if( (int)$this->_productsCount <= ((int)$this->_limit*(int)$this->_limitN)+(int)$this->_limitN ){
      $this->_setStyle($line);
    }
    $fileName = $this->_saveFile();

    return $fileName;
  }
  
  private function _setStyle( $line )
  {
    $i = $line;
    $j = count($this->_head);

    $style_wrap = array(
      'borders'=>array(
        'outline' => array(
          'style'=>PHPExcel_Style_Border::BORDER_THICK
        ),
        'allborders'=>array(
          'style'=>PHPExcel_Style_Border::BORDER_THIN,
          'color' => array(
            'rgb'=>'696969'
          )
        )
      )
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A1:'.$this->_alphabet[$j-1].($i-1))->applyFromArray($style_wrap);

    $style_hprice = array(
      //выравнивание
      'alignment' => array(
        'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
      ),
      //заполнение цветом
      'fill' => array(
        'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
        'color'=>array(
          'rgb' => 'CFCFCF'
        )
      ),
      //Шрифт
      'font'=>array(
        'bold' => true,
        'italic' => true,
        'name' => 'Times New Roman',
        'size' => 13
      ),
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A1:'.$this->_alphabet[$j-1].'1')->applyFromArray($style_hprice);

    $style_price = array(
      'alignment' => array(
        'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
      )
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A2:'.$this->_alphabet[$j-1].($i-1))->applyFromArray($style_price);

    $style_background1 = array(
      //заполнение цветом
      'fill' => array(
        'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
        'color'=>array(
          'rgb' => 'F2F2F5'
        )
      ),
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A2:'.$this->_alphabet[$j-1].($i-1))->applyFromArray($style_background1);

    $style_background2 = array(
      //заполнение цветом
      'fill' => array(
        'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
        'color'=>array(
          'rgb' => 'BDD3C7'
        )
      ),
    );

    $this->_PHPExcel->getActiveSheet()->getStyle('F2:F'.($i-1))->applyFromArray($style_background2);
    if( $this->_more_settings['image_cover'] ){
      $this->_PHPExcel->getActiveSheet()->getStyle('G2:G'.($i-1))->applyFromArray($style_background2);
    }
    else{
      $this->_PHPExcel->getActiveSheet()->getStyle('E2:E'.($i-1))->applyFromArray($style_background2);
    }
  }
  
  private function _getImageObject($mime, $image)
  {
    switch (Tools::strtolower($mime['mime'])) {
      case 'image/png':
        $img_r = imagecreatefrompng($image);
        break;
      case 'image/jpeg':
        $img_r = imagecreatefromjpeg($image);
        break;
      case 'image/gif':
        $img_r = imagecreatefromgif($image);
        break;
      default:
        $img_r = imagecreatefrompng($image);;
    }

    return $img_r;
  }

  private function _setProductInFile( $product, $line )
  {
    $i = 0;

    foreach( array_keys($this->_head) as $field ){
      if( $field == 'product_cover_image' ){
          if(isset($product[$field]) && $product[$field] && $mime = @getimagesize($product[$field])){
              $gdImage = $this->_getImageObject($mime, $product[$field]);
              $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
              $objDrawing->setImageResource($gdImage);
              $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
              $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
              $objDrawing->setHeight(150);
              $objDrawing->setOffsetX(6);
              $objDrawing->setOffsetY(6);
              $objDrawing->setCoordinates($this->_alphabet[$i].$line);
              $objDrawing->setWorksheet( $this->_PHPExcel->getActiveSheet() );
              $this->_PHPExcel->getActiveSheet()->getRowDimension($line)->setRowHeight(121);
              $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(23);
          }
      }
      else{
        $this->_PHPExcel->setActiveSheetIndex(0)
          ->setCellValueExplicit($this->_alphabet[$i].$line, isset($product[$field]) ? $product[$field] : '',PHPExcel_Cell_DataType::TYPE_STRING);
      }
      $i++;
    }
  }

  private function _saveFile()
  {
    $show =  Configuration::get('GOMAKOIL_SHOW_NAME_FIELD');
    $name =  Configuration::get('GOMAKOIL_NAME_FIELD');
    $date = Configuration::get('EXPORT_PRODUCTS_TIME', '' ,$this->_shopGroupId, $this->_idShop);
    $name_file = 'export_products_' . $date;

    if($show &&  $name){
      $name_file = $name;
    }

    $name_file = $name_file . '.' . $this->_format;


    if ($this->_format == 'xlsx'){
      $objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'Excel2007');
      if( (int)$this->_productsCount <= ((int)$this->_limit*(int)$this->_limitN)+(int)$this->_limitN ){
        $objWriter->save(_PS_MODULE_DIR_ . 'quantityupdate/files/'.$name_file);
        for( $l = 0;$l<(int)$this->_limit;$l++ ){
          if( file_exists(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . ((int)$l) . '.' . $this->_format) ){
            unlink(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . ((int)$l) . '.' . $this->_format);
          }
        }
      }
      else{
        $objWriter->save(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . $this->_limit . '.' . $this->_format);
      }
    }
    elseif ($this->_format == 'csv'){
      $objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'CSV');


      if( (int)$this->_productsCount <= ((int)$this->_limit*(int)$this->_limitN)+(int)$this->_limitN ){
        $objWriter->save(_PS_MODULE_DIR_ . 'quantityupdate/files/'.$name_file);
        for( $l = 0;$l<(int)$this->_limit;$l++ ){
          if( file_exists(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . ((int)$l) . '.' . $this->_format) ){
            unlink(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . ((int)$l) . '.' . $this->_format);
          }
        }
      }
      else{
        $objWriter->save(_PS_MODULE_DIR_ . 'quantityupdate/files/export_products_' . $date . $this->_limit . '.' . $this->_format);
      }

    }

    if( (int)$this->_productsCount > ((int)$this->_limit*(int)$this->_limitN)+(int)$this->_limitN ){
      return (int)$this->_limit+1;
    }

    return _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/quantityupdate/files/' . $name_file;
  }

  private function _createHead()
  {
    $this->_head = $this->_getHeadFields();
    $this->_PHPExcel->getProperties()->setCreator("PHP")
      ->setLastModifiedBy("Admin")
      ->setTitle("Office 2007 XLSX")
      ->setSubject("Office 2007 XLSX")
      ->setDescription(" Office 2007 XLSX, PHPExcel.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("File");
    $this->_PHPExcel->getActiveSheet()->setTitle('Export');

    $i = 0;
    foreach($this->_head as $field => $name) {
      $this->_PHPExcel->setActiveSheetIndex(0)
        ->setCellValue($this->_alphabet[$i].'1', $name);
      if( $field == "product_name" ){
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      }
      elseif($field == "product_cover_image" || $field == "id_product_attribute" ){
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(25);
      }
      else{
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(20);
      }
      $i++;
    }

    $this->_PHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
  }

  private function _getHeadFields()
  {
    $selected_fields = array(
      'id_product'            => 'id_product',
      'id_product_attribute'  => 'id_product_attribute',
      'reference'             => 'reference',
      'product_name'          => 'product_name',
      'quantity'              => 'quantity',
      'price'                 => 'price',
    );

    if( $this->_more_settings['image_cover'] ){
      $selected_fields = array(
        'id_product'           => 'id_product',
        'id_product_attribute' => 'id_product_attribute',
        'product_cover_image'  => 'product_cover_image',
        'reference'            => 'reference',
        'product_name'         => 'product_name',
        'quantity'             => 'quantity',
        'price'                => 'price',
      );
    }

    return $selected_fields;
  }


  public function getCombinationImageById($id_product_attribute, $id_lang)
  {
    if (!Combination::isFeatureActive() || !$id_product_attribute) {
      return false;
    }

    $result = Db::getInstance()->executeS('
			SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
			FROM `'._DB_PREFIX_.'product_attribute_image` pai
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.`id_image` = pai.`id_image`)
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
			WHERE pai.`id_product_attribute` = '.(int)$id_product_attribute.' AND il.`id_lang` = '.(int)$id_lang.' ORDER by i.`position` LIMIT 1'
    );

    if (!$result) {
      return false;
    }

    return $result[0];
  }


}
