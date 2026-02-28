<?php

@ini_set('display_errors', 'off');
error_reporting(0);

if(!class_exists('PHPExcel')) {
	include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel.php');
	include_once(_PS_MODULE_DIR_ . 'quantityupdate/libraries/PHPExcel_1.7.9/Classes/PHPExcel/IOFactory.php');
}

require_once(dirname(__FILE__) . '/../../classes/updateLive.php');
require_once(dirname(__FILE__) . '/../../classes/export.php');
require_once(dirname(__FILE__) . '/../../classes/update.php');
include_once(_PS_MODULE_DIR_.'quantityupdate/classes/quantityUpdateTools.php');

class AdminQuantityUpdateController extends ModuleAdminController
{

    public function __construct()
    {
        ini_set("max_execution_time","0");
        ini_set('memory_limit', '-1');
        @ini_set('display_errors', 'off');

        $write_fd = fopen('error.log', 'w');
        fwrite($write_fd, " ");
        fclose($write_fd);
        ini_set("log_errors", 1);
        ini_set("error_log", "error.log");

        parent::__construct();

    }

  public function ajaxProcessFieldsMapping()
  {
    try{
      $json = array();
      if( Tools::getValue('feed_source') == 'file_url' ){
        $headers = $this->_copyFromUrl();
      }
      else{
        $headers = $this->_checkFtpConnect();
      }
      $json['form'] = $this->_getForm($headers);
      die(json_encode($json));
    }
    catch(Exception $e){
      $json['error'] = $e->getMessage();
      die(json_encode($json));
    }
  }

  private function _checkFtpConnect()
  {
    if( !Tools::getValue('ftp_server') || !Validate::isUrl( Tools::getValue('ftp_server') ) ){
      throw new Exception($this->l('Please enter valid FTP Server!'));
    }

    if( !Tools::getValue('ftp_user') ){
      throw new Exception($this->l('Please enter valid FTP User Name!'));
    }

    if( !Tools::getValue('ftp_password') ){
      throw new Exception($this->l('Please enter valid FTP Password!'));
    }

    if( !Tools::getValue('ftp_file_path') ){
      throw new Exception($this->l('Please enter valid FTP File Path!'));
    }

    $conn_id = @ftp_connect(Tools::getValue('ftp_server'));
    if( !$conn_id ){
      throw new Exception($this->l('Can not connect to your FTP Server!'));
    }

    $login_result = @ftp_login($conn_id, Tools::getValue('ftp_user'), Tools::getValue('ftp_password'));

    if( !$login_result ){
      throw new Exception($this->l('Can not Login to your FTP Server, please check access!'));
    }

    $dest = _PS_MODULE_DIR_ . 'quantityupdate/files/tmp_import.'.Tools::getValue('format');

    if (!@ftp_get($conn_id, $dest, Tools::getValue('ftp_file_path'), FTP_BINARY)) {
      throw new Exception($this->l('Can not download file from FTP, please check file path!'));
    }

    $mime = mime_content_type($dest);

    if( strpos($mime, 'octet-stream') === false && strpos($mime, 'text') === false && strpos($mime, 'csv') === false && strpos($mime, 'officedocument') === false && strpos($mime, 'vnd.openxmlformats') === false ){
      unlink($dest);
      throw new Exception($this->l('File for import is not valid!'));
    }

    return $this->_getHeaders( $dest );
  }

  public function ajaxProcessRemoveSettings()
  {
    try{
      $json = array();
      $settings = $this->decodeSettingsNameFromUrl(Tools::getValue('settings_name'));
      Configuration::deleteByName('GOMAKOIL_QUANTITYUPDATE_' . $settings);
      die(json_encode($json));
    }
    catch(Exception $e){
      $json['error'] = $e->getMessage();
      die(json_encode($json));
    }
  }

  public function ajaxProcessLoadSettings()
  {
    try{
      $json = array();
      $settings = $this->decodeSettingsNameFromUrl(Tools::getValue('settings_name'));
      $data = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . $settings);
      $json['data'] = quantityUpdateTools::jsonDecode($data);
      die(json_encode($json));
    }
    catch(Exception $e){
      $json['error'] = $e->getMessage();
      die(json_encode($json));
    }
  }

  public function ajaxProcessSaveSettings()
  {
    try{
      $json = array();
      if( Tools::getValue('file_product_identifier') == 'no' ){
        throw new Exception($this->l('Please select Product Identification field!'));
      }

      $quantity_for_update_is_set = ((Tools::getValue('file_product_price') != 'no' || Tools::getValue('file_quantity') != 'no') || (
        !empty(Tools::getValue('price_update_conditions')) && (Tools::getValue('quantity_source') == 'shop' || Tools::getValue('price_source') == 'shop')
        ));

      if (!$quantity_for_update_is_set) {
        throw new Exception($this->l('Please select at least one field for update (Quantity or Price)!'));
      }

      if( !Tools::getValue('settings') ){
        throw new Exception($this->l('Please enter settings name!'));
      }

      $this->_saveSettings();
      $description = '<p>You can place the following URL in your crontab file, or you can click it yourself regularly</p>';
      $description .= '<p><strong><a href="'.Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/quantityupdate/automatic_update.php?settings='.Tools::str2url(Tools::getValue('settings')).'&id_shop='.Tools::getValue('id_shop').'&secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'" onclick="return !window.open($(this).attr(\'href\'));">'.Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/quantityupdate/automatic_update.php?settings='.Tools::str2url(Tools::getValue('settings')).'&id_shop='.Tools::getValue('id_shop').'&secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'</a></strong></p>';

      $json['description'] = $description;

      $json['success'] = $this->l('Data successfully saved!');

      die(json_encode($json));
    }
    catch(Exception $e){
      $json['error'] = $e->getMessage();
      die(json_encode($json));
    }
  }

  private function _saveSettings()
  {
    $data = array(
      'format'                     => Tools::getValue('format'),
      'delimiter'                  => Tools::getValue('delimiter'),
      'file_url'                   => Tools::getValue('file_url'),
      'feed_source'                => Tools::getValue('feed_source'),
      'ftp_server'                 => Tools::getValue('ftp_server'),
      'ftp_user'                   => Tools::getValue('ftp_user'),
      'ftp_password'               => Tools::getValue('ftp_password'),
      'ftp_file_path'              => Tools::getValue('ftp_file_path'),
      'product_identifier'         => Tools::getValue('product_identifier'),
      'file_product_identifier'    => Tools::getValue('file_product_identifier'),
      'product_price'              => Tools::getValue('product_price'),
      'file_product_price'         => Tools::getValue('file_product_price'),
      'file_quantity'              => Tools::getValue('file_quantity'),
      'quantity_update_method'     => Tools::getValue('quantity_update_method'),
      'quantity_source'            => Tools::getValue('quantity_source'),
      'price_source'               => Tools::getValue('price_source'),
      'emails'                     => Tools::getValue('emails'),
      'in_store_not_in_file'       => Tools::getValue('in_store_not_in_file'),
      'in_store_and_in_file'       => Tools::getValue('in_store_and_in_file'),
      'zero_quantity_disable'      => Tools::getValue('zero_quantity_disable'),
      'disable_hooks'               => Tools::getValue('disable_hooks'),
      'settings'                   => Tools::getValue('settings'),
      'quantity_update_conditions' => Tools::getValue('quantity_update_conditions'),
      'price_update_conditions'    => Tools::getValue('price_update_conditions'),
      'show_advanced_price_settings'    => Tools::getValue('show_advanced_price_settings'),
      'show_advanced_quantity_settings'    => Tools::getValue('show_advanced_quantity_settings'),
    );

    $settings = Tools::str2url(Tools::getValue('settings'));
    Configuration::updateValue('GOMAKOIL_QUANTITYUPDATE_' . $settings, quantityUpdateTools::jsonEncode($data));
  }

  private function _copyFromUrl()
  {
    $dest = _PS_MODULE_DIR_ . 'quantityupdate/files/tmp_import.'.Tools::getValue('format');

    $remoteHeaders = @get_headers(Tools::getValue('file_url'));
    $checkFormatFile = false;

    foreach ( $remoteHeaders as $header ){
      if( strpos($header, 'csv') !== false || strpos($header, 'officedocument') !== false || strpos($header, 'octet-stream') !== false || strpos($header, 'text') !== false || strpos($header, 'vnd.openxmlformats') !== false ){
        $checkFormatFile= true;
        break;
      }
    }

    if( !$checkFormatFile ){
      throw new Exception($this->l('File for import is not valid!'));
    }

    if( !@copy(Tools::getValue('file_url'), $dest) ){
      throw new Exception($this->l('Can not copy file for import, please check module folder file permissions or contact us.'));
    }

    return $this->_getHeaders( $dest );
  }

  private function _getHeaders( $dest )
  {
    $headers = array(array('name' => 'no'));

    if(Tools::getValue('format') == 'xlsx' ){
      $objPHPExcel = PHPExcel_IOFactory::load($dest);
    }
    elseif(Tools::getValue('format') == 'csv'){
      $reader = PHPExcel_IOFactory::createReader("CSV");
      $reader->setDelimiter(Tools::getValue('delimiter'));
      $objPHPExcel = $reader->load($dest);
    }

    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
      $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      for ($row = 1; $row <= 1; ++ $row) {
        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
          $cell = $worksheet->getCellByColumnAndRow($col, $row);
          $val = $cell->getValue();
          if($row == 1){
            if( !$val ){
              continue;
            }
            $headers[] = array('name' => $val);
          }
        }
      }
    }

    return $headers;
  }

  private function _getForm( $headers = array() )
  {
    $quantityUpdate = Module::getInstanceByName('quantityupdate');
    $helper = new HelperForm();
    $helper->module = $quantityUpdate;
    $helper->name_controller = $quantityUpdate->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$quantityUpdate->name;
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
    $helper->title = $quantityUpdate->displayName;
    $automaticDescription = "";
    if( Tools::getValue('settings') ){
      $automaticDescription = '<p>You can place the following URL in your crontab file, or you can click it yourself regularly</p>';
      $automaticDescription .= '<p><strong><a href="'.Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/quantityupdate/automatic_update.php?settings='.$this->decodeSettingsNameFromUrl(Tools::getValue('settings')).'&id_shop='.Tools::getValue('id_shop').'&secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'" onclick="return !window.open($(this).attr(\'href\'));">'.Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/quantityupdate/automatic_update.php?settings='.$this->decodeSettingsNameFromUrl(Tools::getValue('settings')).'&id_shop='.Tools::getValue('id_shop').'&secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'</a></strong></p>';

    }

    $quantity_update_methods = array(
      array(
        'id' => 'override',
        'name' => 'Override'
      ),
      array(
        'id' => 'add',
        'name' => 'Add to existing value'
      ),
      array(
        'id' => 'subtract',
        'name' => 'Remove from existing value'
      )
    );

    $this->fields_form[0]['form'] = array(
      'input' => array(
        array(
          'type' => 'select',
          'label' => $this->l('Key for product identification:'),
          'name' => 'product_identifier',
          'class' => 'product_identifier',
          'form_group_class' => 'product_identifier',
          'options' => array(
            'query' =>array(
              array(
                'id' => 'reference',
                'name' => $this->l('Reference code')
              ),
              array(
                'id' => 'ean13',
                'name' => $this->l('EAN-13 or JAN barcode')
              ),
              array(
                'id' => 'upc',
                'name' => $this->l('UPC barcode')
              ),
              array(
                'id' => 'id_product',
                'name' => $this->l('Product Id')
              ),
            ),
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'select',
          'name' => 'file_product_identifier',
          'class' => 'file_product_identifier',
          'form_group_class' => 'file_product_identifier',
          'options' => array(
            'query' => $headers,
            'id' => 'name',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'html',
          'html_content' => '<div></div>',
          'name' => '',
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Price:'),
          'name' => 'product_price',
          'class' => 'product_price',
          'form_group_class' => 'product_price',
          'options' => array(
            'query' =>array(
              array(
                'id' => 'pre_tax_price',
                'name' => $this->l('Pre-tax retail price')
              ),
              array(
                'id' => 'tax_price',
                'name' => $this->l('Retail price with tax')
              ),
            ),
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'select',
          'name' => 'file_product_price',
          'class' => 'file_product_price',
          'form_group_class' => 'file_product_price',
          'options' => array(
            'query' => $headers,
            'id' => 'name',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'html',
          'html_content' => '<div></div>',
          'name' => ''
        ),
        array(
          'type' => 'select',
          'name' => 'file_quantity',
          'class' => 'file_quantity',
          'label' => $this->l('Quantity:'),
          'form_group_class' => 'file_quantity',
          'options' => array(
            'query' => $headers,
            'id' => 'name',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'select',
          'name' => 'quantity_update_method',
          'class' => 'quantity_update_method',
          'label' => $this->l('Quantity Update Method:'),
          'form_group_class' => 'file_quantity',
          'options' => array(
            'query' => $quantity_update_methods,
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'textarea',
          'name' => 'emails',
          'class' => 'emails',
          'label' => $this->l('Emails For Products Update Report:'),
          'hint'  => $this->l('Each email in per line'),
          'form_group_class' => 'emails',
          'options' => array(
            'query' => $headers,
            'id'    => 'name',
            'name'  => 'name'
          )
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Products in store but not in file'),
          'name' => 'in_store_not_in_file',
          'class' => 'in_store_not_in_file',
          'form_group_class' => 'in_store_not_in_file',
          'tab' => 'update',
          'options' => array(
            'query' =>
              array(
                array(
                  'id' => 'ignore',
                  'name' => $this->l('Ignore')
                ),
                array(
                  'id' => 'enable',
                  'name' => $this->l('Enable')
                ),
                array(
                  'id' => 'disable',
                  'name' => $this->l('Disable')
                ),
                array(
                  'id' => 'zero_quantity',
                  'name' => $this->l('Quantity to Zero')
                ),
              ),
            'id' => 'id',
            'name' => 'name'
          ),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Products in store and in file'),
          'name' => 'in_store_and_in_file',
          'class' => 'in_store_and_in_file',
          'form_group_class' => 'in_store_and_in_file',
          'tab' => 'update',
          'options' => array(
            'query' =>
              array(
                array(
                  'id' => 'ignore',
                  'name' => $this->l('Ignore')
                ),
                array(
                  'id' => 'enable',
                  'name' => $this->l('Enable')
                ),
                array(
                  'id' => 'disable',
                  'name' => $this->l('Disable')
                ),
                array(
                  'id' => 'zero_quantity',
                  'name' => $this->l('Quantity to Zero')
                ),
              ),
            'id' => 'id',
            'name' => 'name'
          ),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Disable products with zero quantity'),
          'name' => 'zero_quantity_disable',
          'is_bool' => true,
          'tab' => 'update',
          'values' => array(
            array(
              'id' => 'zero_quantity_disable_on',
              'value' => 1,
              'label' => $this->l('Yes')),
            array(
              'id' => 'zero_quantity_disable_off',
              'value' => 0,
              'label' => $this->l('No')),
          ),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Disable modules hooks for import'),
          'name' => 'disable_hooks',
          'is_bool' => true,
          'tab' => 'update',
          'hint' => $this->l('This feature disable hooks in all modules during update that will increase products update speed'),
          'values' => array(
            array(
              'id' => 'disable_hooks_on',
              'value' => 1,
              'label' => $this->l('Yes')),
            array(
              'id' => 'disable_hooks_off',
              'value' => 0,
              'label' => $this->l('No')),
          ),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Show advanced price settings'),
          'name' => 'show_advanced_price_settings',
          'is_bool' => true,
          'tab' => 'update',
          'values' => array(
            array(
              'id' => 'show_advanced_price_settings_on',
              'value' => 1,
              'label' => $this->l('Yes')),
            array(
              'id' => 'show_advanced_price_settings_off',
              'value' => 0,
              'label' => $this->l('No')),
          ),
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'price-update-condition-rules',
          'name' => '',
          'label' => '',
          'html_content' => $this->getPriceConditionRules(),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Show advanced quantity settings'),
          'name' => 'show_advanced_quantity_settings',
          'is_bool' => true,
          'tab' => 'update',
          'values' => array(
            array(
              'id' => 'show_advanced_quantity_settings_on',
              'value' => 1,
              'label' => $this->l('Yes')),
            array(
              'id' => 'show_advanced_quantity_settings_off',
              'value' => 0,
              'label' => $this->l('No')),
          ),
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'quantity-update-condition-rules',
          'name' => '',
          'label' => '',
          'html_content' => $this->getQuantityConditionRules(),
        ),
        array(
          'type' => 'html',
          'name' => '',
          'form_group_class' => 'line',
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Settings name:'),
          'name' => 'settings',
          'class' => 'settings',
          'form_group_class' => 'settings',
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'save_settings',
          'name' => '',
          'html_content' => '<button type="button" class="btn btn-default save_settings">'.$this->l('Save').'</button>',
        ),
        array(
          'type' => 'html',
          'name' => '',
          'form_group_class' => 'line',
        ),
        array(
          'type' => 'html',
          'name' => '',
          'html_content' => $automaticDescription,
          'form_group_class' => 'automatic_block',
        ),
      )
    );

    $helper->fields_value['file_quantity'] = 'no';
    $helper->fields_value['quantity_update_method'] = 'override';
    $helper->fields_value['quantity_source'] = 'file';
    $helper->fields_value['price_source'] = 'file';
    $helper->fields_value['in_store_not_in_file'] = 'ignore';
    $helper->fields_value['in_store_and_in_file'] = 'ignore';
    $helper->fields_value['zero_quantity_disable'] = 0;
    $helper->fields_value['disable_hooks'] = 1;
    $helper->fields_value['file_product_identifier'] = 'no';
    $helper->fields_value['file_product_price'] = 'no';
    $helper->fields_value['product_price'] = 'pre_tax_price';
    $helper->fields_value['product_identifier'] = 'reference';
    $helper->fields_value['settings'] = '';
    $helper->fields_value['emails'] = '';
    $helper->fields_value['show_advanced_price_settings'] = 0;
    $helper->fields_value['show_advanced_quantity_settings'] = 0;

    if( Tools::getValue('settings') ){
      $settings = $this->decodeSettingsNameFromUrl(Tools::getValue('settings'));
      $data = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . $settings);
      $data = quantityUpdateTools::jsonDecode($data);
      $helper->fields_value['file_quantity'] = $data['file_quantity'];
      $helper->fields_value['quantity_update_method'] = $data['quantity_update_method'];
      $helper->fields_value['quantity_source'] = $data['quantity_source'];
      $helper->fields_value['price_source'] = $data['price_source'];
      $helper->fields_value['in_store_not_in_file'] = $data['in_store_not_in_file'];
      $helper->fields_value['in_store_and_in_file'] = $data['in_store_and_in_file'];
      $helper->fields_value['zero_quantity_disable'] = isset($data['zero_quantity_disable']) ? $data['zero_quantity_disable'] : 0;
      $helper->fields_value['disable_hooks'] = isset($data['disable_hooks']) ? $data['disable_hooks'] : 0;
      $helper->fields_value['file_product_identifier'] = $data['file_product_identifier'];
      $helper->fields_value['file_product_price'] = $data['file_product_price'];
      $helper->fields_value['product_price'] = $data['product_price'];
      $helper->fields_value['product_identifier'] = $data['product_identifier'];
      $helper->fields_value['settings'] = $data['settings'];
      $helper->fields_value['emails'] = $data['emails'];
      $helper->fields_value['show_advanced_price_settings'] = $data['show_advanced_price_settings'];
      $helper->fields_value['show_advanced_quantity_settings'] = $data['show_advanced_quantity_settings'];
    }

    return $helper->generateForm($this->fields_form);
  }

  private function getQuantityConditionRules()
  {
    $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'quantityupdate/views/templates/hook/quantity_condition_rules.tpl');

    $quantity_update_conditions = array();
    $quantity_source = 'file';

    if( Tools::getValue('settings') ){
      $settings = $this->decodeSettingsNameFromUrl(Tools::getValue('settings'));
      $save_data = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . $settings);
      $save_data = quantityUpdateTools::jsonDecode($save_data);

      $quantity_update_conditions = $save_data['quantity_update_conditions'];
      $quantity_source = $save_data['quantity_source'];
    }

    $tpl->assign(
      array(
        'quantity_update_conditions' => $quantity_update_conditions,
        'quantity_source' => $quantity_source
      )
    );

    return $tpl->fetch();
  }

  private function getPriceConditionRules()
  {
    $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'quantityupdate/views/templates/hook/price_condition_rules.tpl');

    $price_update_conditions = array();
    $price_source = 'file';

    if( Tools::getValue('settings') ){
      $settings = $this->decodeSettingsNameFromUrl(Tools::getValue('settings'));
      $save_data = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . $settings);
      $save_data = quantityUpdateTools::jsonDecode($save_data);

      $price_update_conditions = $save_data['price_update_conditions'];
      $price_source = $save_data['price_source'];
    }

    $tpl->assign(
      array(
        'price_update_conditions' => $price_update_conditions,
        'price_source' => $price_source
      )
    );

    return $tpl->fetch();
  }

  private function decodeSettingsNameFromUrl($settings_name)
  {
      $settings_name = urldecode($settings_name);
      return Tools::str2url($settings_name);
  }

    public function ajaxProcessliveUpdate()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_lang = Tools::getValue('id_lang');

            $updateLive = new updateLiveProductCatalog($id_shop , $id_lang);

            $data = array(
                'products'                => Tools::getValue('selected_products'),
                'price_increment'         => Tools::getValue('price_increment'),
                'type_price_increment'    => Tools::getValue('type_price_increment'),
                'operations'              => Tools::getValue('operations'),
                'options_live_update'     => Tools::getValue('options_live_update'),
                'quantity_increment'      => Tools::getValue('quantity_increment'),
                'quantity_operations'     => Tools::getValue('quantity_operations'),
                'type_quantity_increment' => Tools::getValue('type_quantity_increment'),
            );

            if($updateLive->update($data)){
                $search_data = array(
                    'p'                 => Tools::getValue('pagination_page'),
                    'n'                 => Tools::getValue('pagination_show'),
                    'categories'        => Tools::getValue('categoryBox'),
                    'id'                => Tools::getValue('quantityupdateFilter_id_product'),
                    'reference'         => Tools::getValue('quantityupdateFilter_reference'),
                    'name'              => Tools::getValue('quantityupdateFilter_name'),
                    'supplier'          => Tools::getValue('quantityupdateFilter_supplier'),
                    'manufacturer'      => Tools::getValue('quantityupdateFilter_manufacturer'),
                    'price_min'         => Tools::getValue('quantityupdateFilter_price_min'),
                    'price_max'         => Tools::getValue('quantityupdateFilter_price_max'),
                    'quantity_min'      => Tools::getValue('quantityupdateFilter_quantity_min'),
                    'quantity_max'      => Tools::getValue('quantityupdateFilter_quantity_max'),
                    'active'            => Tools::getValue('quantityupdateFilter_active'),
                    'products'          => Tools::getValue('selected_products'),
                );

                $tpl = Module::getInstanceByName('quantityupdate')->searchProductsList($id_lang, $id_shop, $search_data );
                $json['table'] = $tpl;
                $json['success'] = Module::getInstanceByName('quantityupdate')->l('Data successfully update!', __CLASS__);
            }
            else{
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Some error occurred please contact us!', __CLASS__));
            }

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessFilterQuantityUpdate()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_lang = Tools::getValue('id_lang');

            $search_data = array(
                'p'             => Tools::getValue('pagination_page'),
                'n'             => Tools::getValue('pagination_show'),
                'categories'    => Tools::getValue('categoryBox'),
                'id'            => Tools::getValue('quantityupdateFilter_id_product'),
                'reference'     => Tools::getValue('quantityupdateFilter_reference'),
                'name'          => Tools::getValue('quantityupdateFilter_name'),
                'supplier'      => Tools::getValue('quantityupdateFilter_supplier'),
                'manufacturer'  => Tools::getValue('quantityupdateFilter_manufacturer'),
                'price_min'     => Tools::getValue('quantityupdateFilter_price_min'),
                'price_max'     => Tools::getValue('quantityupdateFilter_price_max'),
                'quantity_min'  => Tools::getValue('quantityupdateFilter_quantity_min'),
                'quantity_max'  => Tools::getValue('quantityupdateFilter_quantity_max'),
                'active'        => Tools::getValue('quantityupdateFilter_active'),
                'products'      => array(),
            );

            if((int)Tools::getValue('reset')){

                $search_data = array(
                    'p'             => 1,
                    'n'             => 50,
                    'categories'    => Tools::getValue('categoryBox'),
                    'id'            => '',
                    'reference'     => '',
                    'name'          => '',
                    'supplier'      => '',
                    'manufacturer'  => '',
                    'price_min'     => '',
                    'price_max'     => '',
                    'quantity_min'  => '',
                    'quantity_max'  => '',
                    'active'        => '',
                    'products'      => array(),
                );
            }

            $tpl = Module::getInstanceByName('quantityupdate')->searchProductsList($id_lang, $id_shop, $search_data );
            $json['success'] = $tpl;

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessAddProductToExport()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_product = Tools::getValue('id_product');

            $name_config = 'GOMAKOIL_PRODUCTS_CHECKED';
            $config = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
            if( !$config ){
                $config = array();
            }
            if (!in_array($id_product, $config)){
                array_push($config, $id_product);
            }
            else{
                $key = array_search($id_product, $config);
                if ($key !== false)
                {
                    unset ($config[$key]);
                }
            }
            $products = quantityUpdateTools::jsonEncode($config);
            Configuration::updateValue($name_config, $products);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessAddManufacturerToExport()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_manufacturer = Tools::getValue('id_manufacturer');

            $name_config = 'GOMAKOIL_MANUFACTURERS_CHECKED';
            $config = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
            if( !$config ){
                $config = array();
            }
            if (!in_array($id_manufacturer, $config)){
                array_push($config, $id_manufacturer);
            }
            else{
                $key = array_search($id_manufacturer, $config);
                if ($key !== false)
                {
                    unset($config[$key]);
                }
            }
            $config = quantityUpdateTools::jsonEncode($config);
            Configuration::updateValue($name_config, $config);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessAddSupplierToExport()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_supplier = Tools::getValue('id_supplier');

            $name_config = 'GOMAKOIL_SUPPLIERS_CHECKED';
            $config = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
            if( !$config ){
                $config = array();
            }
            if (!in_array( $id_supplier, $config)){
                array_push($config, $id_supplier);
            }
            else{
                $key = array_search($id_supplier, $config);
                if ($key !== false)
                {
                    unset($config[$key]);
                }
            }
            $config = quantityUpdateTools::jsonEncode($config);
            Configuration::updateValue($name_config, $config);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }



    public function ajaxProcessSearchProduct()
    {
        try{
            $json = array();

            $id_shop = Tools::getValue('id_shop');
            $id_lang = Tools::getValue('id_lang');
            $search = Tools::getValue('search_product');
            $json['products'] = Module::getInstanceByName('quantityupdate')->searchProducts($search, $id_shop, $id_lang);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessSearchManufacturer()
    {
        try{
            $json = array();

            $search = Tools::getValue('search_manufacturer');
            $json['manufacturers'] = Module::getInstanceByName('quantityupdate')->searchManufacturers($search);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessSearchSupplier()
    {
        try{
            $json = array();

            $search = Tools::getValue('search_supplier');
            $json['suppliers'] = Module::getInstanceByName('quantityupdate')->searchSuppliers($search);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowCheckedProducts()
    {
        try{
            $json = array();

            $id_shop = Tools::getValue('id_shop');
            $id_lang = Tools::getValue('id_lang');
            $json['products'] = Module::getInstanceByName('quantityupdate')->showCheckedProducts($id_shop, $id_lang);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowCheckedManufacturers()
    {
        try{
            $json = array();
            $json['manufacturers'] = Module::getInstanceByName('quantityupdate')->showCheckedManufacturers();
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowCheckedSuppliers()
    {
        try{
            $json = array();
            $json['suppliers'] = Module::getInstanceByName('quantityupdate')->showCheckedSuppliers();
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowAllProducts()
    {
        try{
            $json = array();
            $id_shop = Tools::getValue('id_shop');
            $id_lang = Tools::getValue('id_lang');
            $json['products'] = Module::getInstanceByName('quantityupdate')->showAllProducts($id_shop, $id_lang);
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowAllManufacturers()
    {
        try{
            $json = array();
            $json['manufacturers'] = Module::getInstanceByName('quantityupdate')->showAllManufacturers();
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessShowAllSuppliers()
    {
        try{
            $json = array();
            $json['suppliers'] = Module::getInstanceByName('quantityupdate')->showAllSuppliers();
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessExportProducts()
    {
        try{
            $json = array();

            if( Tools::getValue('price_value') !== '' && !Tools::getValue('selection_type_price') ){
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Please select sign inequality', __CLASS__));
            }

            if( Tools::getValue('price_value') !== '' && !Validate::isFloat( Tools::getValue('price_value')) ){
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Please enter valid price value', __CLASS__));
            }

            if( Tools::getValue('quantity_value') !== '' && !Tools::getValue('selection_type_quantity') ){
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Please select sign inequality', __CLASS__));
            }

            if( Tools::getValue('quantity_value') !== '' && !Validate::isInt( Tools::getValue('quantity_value')) ){
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Please enter valid quantity value', __CLASS__));
            }

            if( Tools::getValue('name_export_file') && !Tools::getValue('name_file') ){
                throw new Exception( Module::getInstanceByName('quantityupdate')->l('Please set name export file', __CLASS__));
            }

            Configuration::updateValue('GOMAKOIL_SHOW_NAME_FIELD', Tools::getValue('name_export_file'));
            Configuration::updateValue('GOMAKOIL_NAME_FIELD', Tools::getValue('name_file'));
            Configuration::updateValue('GOMAKOIL_FORMAT_FILE', Tools::getValue('format_file'));
            Configuration::updateValue('GOMAKOIL_CATEGORIES_CHECKED', '');

            if( Tools::getValue('categories') ){
                Configuration::updateValue('GOMAKOIL_CATEGORIES_CHECKED', quantityUpdateTools::jsonEncode(Tools::getValue('categories')));
            }

            if( Tools::getValue('field') ){
                Configuration::updateValue('GOMAKOIL_FIELDS_CHECKED', quantityUpdateTools::jsonEncode(Tools::getValue('field')));
            }

            $more_settings = array(
                'active_products'   => Tools::getValue('active_products'),
                'inactive_products' => Tools::getValue('inactive_products'),
                'image_cover'       => Tools::getValue('image_cover'),
                'round_value'       => Tools::getValue('round_value'),
                'tax_price'         => Tools::getValue('tax_price'),
                'orderby'           => Tools::getValue('orderby'),
                'orderway'          => Tools::getValue('orderway'),
                'price_products'    => [
                    'price_value'          => Tools::getValue('price_value'),
                    'selection_type_price' => Tools::getValue('selection_type_price')
                ],
                'quantity_products' => [
                    'quantity_value'          => Tools::getValue('quantity_value'),
                    'selection_type_quantity' => Tools::getValue('selection_type_quantity')
                ],
            );
            $export = new quantityExportProducts( Tools::getValue('id_shop'), Tools::getValue('id_lang'), Tools::getValue('format_file'), $more_settings );
            $fileName = $export->export( Tools::getValue('page_limit') );

            if( is_int($fileName) ){
                $json['page_limit'] = $fileName;
            }
            else{
                $json['file'] = $fileName;
                $json['success'] = Module::getInstanceByName('quantityupdate')->l('Data successfully saved!', __CLASS__);
            }

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }


    public function ajaxProcessUpdateProducts()
    {
        try{
            $json = array();

            $obj = new updateProductCatalog(array(
                'id_shop' => Tools::getValue('id_shop'),
                'id_lang' => Tools::getValue('id_lang'),
                'format_file' => Tools::getValue('format_file'),
                'field_update' => Tools::getValue('field_update'),
                'field_for_update' => Tools::getValue('field_for_update'),
            ));

            $res = $obj->update( Tools::getValue('page_limit') );
            if( is_int($res) ){
                $json['page_limit'] = $res;
            }
            else{
                $json['success'] = $res;
            }

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }


    public function ajaxProcessReturnExportCount()
    {
        try{
            $json = array();
            $productsCount = Configuration::get('EXPORT_PRODUCTS_COUNT','',Context::getContext()->shop->id_shop_group, Tools::getValue('id_shop'));
            $currentExportedProducts = Configuration::get('EXPORT_PRODUCTS_CURRENT_COUNT','',Context::getContext()->shop->id_shop_group, Tools::getValue('id_shop'));
            $json['export_notification'] = Module::getInstanceByName('quantityupdate')->l('Successfully exported ' . $currentExportedProducts . ' from ' . $productsCount . ' products', __CLASS__);

            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

    public function ajaxProcessReturnUpdateCount()
    {
        try{
            $json = array();
            $currentUpdatedProducts = Configuration::get('UPDATED_PRODUCTS_CURRENT_COUNT','',Context::getContext()->shop->id_shop_group, Tools::getValue('id_shop'));
            $json['update_notification'] = Module::getInstanceByName('quantityupdate')->l('Successfully updated ' . $currentUpdatedProducts . ' items', __CLASS__);
            die(json_encode($json));
        }
        catch(Exception $e){
            $json['error'] = $e->getMessage();
            die(json_encode($json));
        }
    }

}